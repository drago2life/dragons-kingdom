<?PHP //storage.php :: Handles item stoarge, as well as equiping/unequping items.
$updatequery = doquery("UPDATE {{table}} SET location='Stored Items' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

//dk_itemstorage
/**
 * @return void
 * @desc Shows the user what they are currently carring or storing, and allows them to equip or drop the itesm they are carrying.
*/
function itemstorage()	{
	global $userrow, $backpackdropslots, $storagedropslots, $backpackitemslots, $storageitemslots, $backpackjewelleryslots, $storagejewelleryslots, $_GET;

	$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' AND charname='".$userrow["charname"]."' LIMIT 1", "homes");
	if (mysql_num_rows($castlequery) <= 0)	{
		$inhouse = false;
	}
	else {
		$inhouse = true;
	}

	$townquery = doquery("SELECT name FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
	if (mysql_num_rows($townquery) != 1)	{
		$intown = false;
	}
	else {
		$intown = true;
	}

	if ($_GET['do'] == 'backpack')	{
		$capdoing = "Backpack";
		$doing = "backpack";
		$sendto = "To Storage";
		$type = 1;
		$dbtype = 1;
	}
	elseif ($_GET['do'] == 'backpackdropclean')	{
		$capdoing = "Backpack";
		$doing = "backpack";
		$type = 3;
		$dbtype = 1;
		$action = "drop";
	}
	elseif ($_GET['do'] == 'backpackitemclean')	{
		$capdoing = "Backpack";
		$doing = "backpack";
		$type = 4;
		$dbtype = 1;
		$action = "dropitem";
	}
	elseif ($_GET['do'] == 'storage')	{
		if (!$inhouse)	{
			header("Location: index.php?do=move:0");
			exit;
		}

		$capdoing = "Storage Box";
		$doing = "storage box";
		$sendto = "To Backpack";
		$type = 2;
		$dbtype = 2;
	}

	$title = $capdoing;

	$page = "<table width='100%' border='1'><tr><td class='title'>$capdoing</td></tr></table><br />";
	if ($type == 1)	{

		$page .= "Here are all the items in your backpack.  If you own a house you can place these items in your home's storage box, but you will have to be inside your house to do this. You may also sell your items, but you must be in town to do this.<br /><br />";
	}
	else {

		$page .= "These are the items you have stored in you storage box. You may place them into your backpack if you wish to use them.<br /><br />";
	}

	//Show Items
	$page .= "<center><table width='95%'><tr><td style='padding:1px; background-color:black;'><table width='100%' style='margins:0px;' cellspacing='1' cellpadding='3'><tr><th colspan='4' style='background-color:#dddddd;'><center>Items in your $doing</center></th></tr>
	<tr><th width='50%' style='background-color:#dddddd;'>Item</th><th width='15%' style='background-color:#dddddd;'>Equip</th><th width='15%' style='background-color:#dddddd;'>Drop</th><th width='20%' style='background-color:#dddddd;'>Action</th></tr>";

	$query = "SELECT dk_itemstorage.isid, dk_items.* FROM dk_itemstorage, dk_items
		  WHERE dk_itemstorage.playerid = '$userrow[id]'
		  AND dk_itemstorage.itemtype = 1
		  AND dk_itemstorage.location = '$dbtype'
		  AND dk_itemstorage.itemid = dk_items.id
		  ORDER BY dk_items.name";
	$result = mysql_query($query)	or die(mysql_error());

	$bgcolor = "#ffffff";

	while($ma = mysql_fetch_array($result))	{
		$page .= "<tr><td style='background-color:$bgcolor'>$ma[name]</td><td style='background-color:$bgcolor'><a href='index.php?do=equipitem:" . $ma[isid] . "&amp;where=" . $type . "'>Equip</a></td><td style='background-color:$bgcolor'><a href='index.php?do=dropjunk:" . $ma[isid] . "&amp;where=". $type . "'>Drop</a><td style='background-color:$bgcolor'>";
		if ($inhouse)	{
			$page .= "<a href='index.php?do=moveitem:" . $ma[isid] . "&amp;where=" . $type . "'>$sendto</a>";
		}
		elseif ($intown)	{
			$page .= "<a href='index.php?do=selljunk:" . $ma[isid] . "&amp;where= " . $type . "'>Sell Item</a>";
		}
		else {
			$page .= "-";
		}
		$page .= "</td></tr>";

		if ($bgcolor == "#ffffff")	{
			$bgcolor = "#eeeeee";
		}
		else {
			$bgcolor = "#ffffff";
		}
	}

	if ($doing == 'storage box')	{
		$dothis = 'storage';
	}
	else {
		$dothis = $doing;
	}
	$is = "{$dothis}itemslots";

//	die("$is :: $dothis :: $$is");

	if (mysql_num_rows($result) < 1)	{
		$itemroom = $$is;
		$page .= "<tr><td style='background-color:$bgcolor' colspan='4'>You have no items in your $doing.  You can store up to <b>$itemroom</b> items.</td></tr>";
	}
	else {
		if ($intown)	{
			$sellall = "[ <a href='index.php?do=sellalljunk&amp;what=1&amp;where=$type'>Sell All Items</a> ]";
		}

		$itemroom = $$is - mysql_num_rows($result);
		$page .= "<tr><td style='background-color:$bgcolor' colspan='4'>You have room in your $doing for <b>$itemroom</b> item(s).  [ <a href='index.php?do=dropalljunk&amp;what=1&amp;where=$type'>Drop All Items</a> ] $sellall</td></tr>";
	}

	$page .= "</table></td></tr></table></center><br />";
	//End

	//Show Drops
	$page .= "<center><table width='95%'><tr><td style='padding:1px; background-color:black;'><table width='100%' style='margins:0px;' cellspacing='1' cellpadding='3'><tr><th colspan='4' style='background-color:#dddddd;'><center>Item drops in your $doing</center></th></tr>
	<tr><th width='50%' style='background-color:#dddddd;'>Item Drop</th><th width='15%' style='background-color:#dddddd;'>Equip</th><th width='15%' style='background-color:#dddddd;'>Drop</th><th width='20%' style='background-color:#dddddd;'>Action</th></tr>";

	$query = "SELECT dk_itemstorage.isid, dk_drops.* FROM dk_itemstorage, dk_drops
		  WHERE dk_itemstorage.playerid = '$userrow[id]'
		  AND dk_itemstorage.itemtype = 2
		  AND dk_itemstorage.location = '$dbtype'
		  AND dk_itemstorage.itemid = dk_drops.id
		  ORDER BY dk_drops.name";
	$result = mysql_query($query)	or die(mysql_error());

	$bgcolor = "#ffffff";

	while($ma = mysql_fetch_array($result))	{
		$page .= "<tr><td style='background-color:$bgcolor'>$ma[name]</td><td style='background-color:$bgcolor'><a href='index.php?do=equipitem:" . $ma[isid] . "&amp;where=" . $type . "'>Equip</a></td><td style='background-color:$bgcolor'><a href='index.php?do=dropjunk:" . $ma[isid] . "&amp;where=". $type . "'>Drop</a><td style='background-color:$bgcolor'>";
		if ($inhouse)	{
			$page .= "<a href='index.php?do=moveitem:" . $ma[isid] . "&amp;where=" . $type . "'>$sendto</a>";
		}
		elseif ($intown)	{
			$page .= "<i>Can't Sell</i>";
		}
		else {
			$page .= "-";
		}
		$page .= "</td></tr>";

		if ($bgcolor == "#ffffff")	{
			$bgcolor = "#eeeeee";
		}
		else {
			$bgcolor = "#ffffff";
		}
	}
	$ds = "{$dothis}dropslots";
	if (mysql_num_rows($result) < 1)	{
		$droproom = $$ds;
		$page .= "<tr><td style='background-color:$bgcolor' colspan='4'>You have no item drops in your $doing.  You can store up to <b>$droproom</b> drops.</td></tr>";
	}
	else {
		$droproom = $$ds - mysql_num_rows($result);

		$page .= "<tr><td style='background-color:$bgcolor' colspan='4'>You have room in your $doing for <b>$droproom</b> drops(s). [ <a href='index.php?do=dropalljunk&amp;what=2&amp;where=$type'>Drop All Drops</a> ]</td></tr>";
	}
	$page .= "</table></td></tr></table></center><br />";
	//End

	//Show Jewellery
	$page .= "<center><table width='95%'><tr><td style='padding:1px; background-color:black;'><table width='100%' style='margins:0px;' cellspacing='1' cellpadding='3'><tr><th colspan='4' style='background-color:#dddddd;'><center>Jewellery in your $doing</center></th></tr>
	<tr><th width='50%' style='background-color:#dddddd;'>Jewellery Piece</th><th width='15%' style='background-color:#dddddd;'>Equip</th><th width='15%' style='background-color:#dddddd;'>Drop</th><th width='20%' style='background-color:#dddddd;'>Action</th></tr>";

	$query = "SELECT dk_itemstorage.isid, dk_jewellery.* FROM dk_itemstorage, dk_jewellery
		  WHERE dk_itemstorage.playerid = '$userrow[id]'
		  AND dk_itemstorage.itemtype = 3
		  AND dk_itemstorage.location = '$dbtype'
		  AND dk_itemstorage.itemid = dk_jewellery.id
		  ORDER BY dk_jewellery.name";
	$result = mysql_query($query)	or die(mysql_error());

	$bgcolor = "#ffffff";

	while($ma = mysql_fetch_array($result))	{
		$page .= "<tr><td style='background-color:$bgcolor'>$ma[name]</td><td style='background-color:$bgcolor'><a href='index.php?do=equipitem:" . $ma[isid] . "&amp;where=" . $type . "'>Equip</a></td><td style='background-color:$bgcolor'><a href='index.php?do=dropjunk:" . $ma[isid] . "&amp;where=". $type . "'>Drop</a><td style='background-color:$bgcolor'>";
		if ($inhouse)	{
			$page .= "<a href='index.php?do=moveitem:" . $ma[isid] . "&amp;where=" . $type . "'>$sendto</a>";
		}
		elseif ($intown)	{
			$page .= "<a href='index.php?do=selljunk:" . $ma[isid] . "&amp;where= " . $type . "'>Sell Item</a>";
		}
		else {
			$page .= "-";
		}
		$page .= "</td></tr>";

		if ($bgcolor == "#ffffff")	{
			$bgcolor = "#eeeeee";
		}
		else {
			$bgcolor = "#ffffff";
		}
	}
	$js = "{$dothis}jewelleryslots";

	if (mysql_num_rows($result) < 1)	{
		$jewelleryroom = $$js;
		$page .= "<tr><td style='background-color:$bgcolor' colspan='4'>You have no jewellery pieces in your $doing.  You can store up to <b>$jewelleryroom</b> jewellery pieces. </td></tr>";
	}
	else {
		if ($intown)	{
			$sellall = "[ <a href='index.php?do=sellalljunk&amp;what=3&amp;where=$type'>Sell All Items</a> ]";
		}

		$jewelleryroom = $$js - mysql_num_rows($result);
		$page .= "<tr><td style='background-color:$bgcolor' colspan='4'>You have room in your $doing for <b>$jewelleryroom</b> jewellery pieces(s). [ <a href='index.php?do=dropalljunk&amp;what=3&amp;where=$type'>Drop All Jewellery</a> ] $sellall</td></tr>";
	}

	$page .= "</table></td></tr></table></center><br />";
	//End

	if ($type == 3 || $type == 4)	{
		$page .= "<font color='red'>After you are finished cleaning out your backpack, you may return to the <a href='index.php?do=$action'>Monster Drop</a>.</font>";
	}
	else {
		if ($type == 1)	{
			$page .= "After you are done with your backpack you may <a href='index.php'>return</a> to what you were doing, or use the compass to start exploring.";
		}
		else {
			$page .= "After you are done with your storage box you may <a href='home.php'>return</a> to your home, or use the compass to start exploring.";
		}
	}

	display($page, $title);

}

//Dropjunk
/**
 * @return void
 * @param int $dk_itemstorageid
 * @param int $where
 * @desc Drops (deletes) an item in your backpack or storage
*/
function dropjunk($itemstorageid, $where, $sell = '0')	{
	global $userrow;

	if ($sell == 1)	{
		$townquery = doquery("SELECT name FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
		if (mysql_num_rows($townquery) != 1)	{
			header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "" . "index.php");
			exit;
		}
	}

	if ($where == 1)	{
		$dodet = "backpack";
	}
	elseif ($where == 2)	{
		$dodet = "storage";
	}
	elseif ($where == 3)	{
		$dodet = "backpackdropclean";
	}
	elseif ($where == 4)	{
		$dodet = "backpackitemclean";
	}

	$typequery = doquery("SELECT * FROM {{table}} WHERE isid = '$itemstorageid'", "itemstorage");
	$typerow = mysql_fetch_array($typequery);

	if ($typerow[itemtype] == 1)	{
		$table = "items";
	}
	elseif ($typerow[itemtype] == 2)	{
		$table = "drops";
	}
	elseif ($typerow[itemtype] == 3)	{
		$table = "jewellery";
	}

	$itemsquery = doquery("SELECT * FROM {{table}} WHERE id = '$typerow[itemid]'", $table);
	$itemsrow = mysql_fetch_array($itemsquery);

	if ($sell != 1)	{
		$page = "<table width='100%' border='1'><tr><td class='title'>Drop Item</td></tr></table><br />";
	}
	else {
		$page = "<table width='100%' border='1'><tr><td class='title'>Sell Item</td></tr></table><br />";
	}

	if ($_GET[confirm] != 1)	{
		if ($sell != 1)	{
			$page .= "Are you sure you wish to drop your $itemsrow[name]?<br />
			<br />
			[ <a href='index.php?do=dropjunk:{$itemstorageid}&amp;where=$_GET[where]&amp;confirm=1'>Yes</a> ] [ <a href='index.php?do=$dodet'>No</a> ]";
			display($page, "Drop Item?");
			exit;
		}
		else {
			if ($typerow[itemtype] == 1)	{
				$sellamount = floor($itemsrow['buycost'] / 3);
			}
			elseif ($typerow[itemtype] == 3)	{
				$sellamount = floor($itemsrow['buycost'] / 2);
			}
			$page .= "Are you sure you wish to sell your $itemsrow[name] for <b>$sellamount</b> gold?<br />
			<br />
			[ <a href='index.php?do=selljunk:{$itemstorageid}&amp;where=$_GET[where]&amp;confirm=1'>Yes</a> ] [ <a href='index.php?do=$dodet'>No</a> ]";
			display($page, "Sell Item?");
			exit;
		}
	}

	if ($typerow[itemtype] == 1)	{
		$sellamount = floor($itemsrow['buycost'] / 3);
	}
	elseif ($typerow[itemtype] == 3)	{
		$sellamount = floor($itemsrow['buycost'] / 2);
	}

	$check = delitem($itemstorageid);

	if (!$check)	{
		die("Couldn't drop/sell item");
	}

	if ($sell == 1)	{
		$newgold = $userrow['gold'] + $sellamount;
		doquery("UPDATE {{table}} SET gold = '$newgold' WHERE id = '$userrow[id]'", "users");
		$page .= "You have sold your $itemsrow[name].  You may now return to your <a href='index.php?do=$dodet'>$dodet</a>.";
		display($page, "Item Sold");
	}
	else {
		$page .= "You have dropped your $itemsrow[name].  You may now return to your <a href='index.php?do=$dodet'>$dodet</a>.";
		display($page, "Item Dropped");
	}
//	header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "" . "index.php?do=$dodet");
}

//dropall
/**
 * @return void
 * @param int $what
 * @param int $where
 * @param int $sell
 * @desc Drops all of the items in the backpack/storage box.
*/
function dropall($what, $where, $sell = '0')	{
	global $userrow;

	if ($sell == 1)	{
		$townquery = doquery("SELECT name FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
		if (mysql_num_rows($townquery) != 1)	{
			header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "" . "index.php");
			exit;
		}
	}

	if ($where == 1)	{
		$dodet = "backpack";
	}
	elseif ($where == 2)	{
		$dodet = "storage";
	}
	elseif ($where == 3)	{
		$dodet = "backpackdropclean";
	}
	elseif ($where == 4)	{
		$dodet = "backpackitemclean";
	}
	else {
		header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "" . "index.php");
		exit;
	}

	if ($what == 1)	{
		$table = "items";
		$sellv = 3;
	}
	elseif ($what == 2)	{
		$table = "drops";
	}
	elseif ($what == 3)	{
		$table = "jewellery";
		$sellv = 2;
	}
	else {
		header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "" . "index.php");
		exit;
	}

	if ($sell == 1 && $what == 2)	{
		header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "" . "index.php");
		exit;
	}

	if ($sell != 1)	{
		$page = "<table width='100%' border='1'><tr><td class='title'>Drop All " . ucfirst($table) . "</td></tr></table><br />";
	}
	else {
		$page = "<table width='100%' border='1'><tr><td class='title'>Sell All " . ucfirst($table) . "</td></tr></table><br />";
	}

	$dropquery = "SELECT dk_{$table}.*, dk_itemstorage.isid FROM dk_{$table}, dk_itemstorage
		  WHERE dk_{$table}.id = dk_itemstorage.itemid
		  AND dk_itemstorage.playerid = '$userrow[id]'
		  AND dk_itemstorage.itemtype = '$what'
		  AND dk_itemstorage.location = '$where'
		  ORDER BY dk_{$table}.id";

	$dropresult = mysql_query($dropquery) or die(mysql_error());

	if (mysql_num_rows($dropresult) < 1)	{
		header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "" . "index.php");
		exit;
	}


	if ($sell != 1)	{
		$page .= "Are you sure you wish to drop the following items?<br />
		<ul>";
	}
	else {
		$page .= "Are you sure you wish to sell your the following items?<br />
		<ul>";
	}

	while ($droprow = mysql_fetch_array($dropresult))	{
		$page .= "<li>$droprow[name]";
		if ($sell == 1)	{
			$sellfor = floor($droprow[buycost] / $sellv);
			$page .= " for <b>$sellfor</b> gold";
			$selltotal += $sellfor;
		}
		$page .= "</li>";
	}

	if ($_GET[confirm] != 1)	{
		if ($sell != 1)	{
			$page .= "</ul>
			[ <a href='index.php?do=dropalljunk&amp;where=$where&amp;what=$what&amp;confirm=1'>Yes</a> ] [ <a href='index.php?do=$dodet'>No</a> ]";
			display($page, "Drop Items?");
			exit;
		}
		else {
			$page .= "</ul>Total: <b>$selltotal</b> gold. <br /><br />
			[ <a href='index.php?do=sellalljunk&amp;where=$where&amp;what=$what&amp;confirm=1'>Yes</a> ] [ <a href='index.php?do=$dodet'>No</a> ]";
			display($page, "Sell Items?");
			exit;
		}
	}
	else {
		doquery("DELETE FROM {{table}} WHERE playerid = '$userrow[id]' AND itemtype = '$what' AND location = '$where'", "itemstorage");

		if ($sell == 1)	{
			$newgold = $userrow[gold] + $selltotal;
			doquery("UPDATE {{table}} SET gold = '$newgold' WHERE id = '$userrow[id]'", "users");

		}
		header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "". "index.php?do=$dodet");
	}
}

//equipstoreditem
/**
 * @return void
 * @param int $isid
 * @param int $where
 * @desc Equips and item in your storage box or backpack.
*/
function equipstoreditem($isid, $where, $go=1)	{
	global $userrow;

	$isidquery = doquery("SELECT * FROM {{table}} WHERE isid='$isid'", "itemstorage");
	$isidrow = mysql_fetch_array($isidquery);

	if (mysql_num_rows($isidquery) != 1)	{
		header("Location: index.php?do=move:0");
		exit;
	}

	if ($where == 1)	{
		$dodet = "backpack";
	}
	elseif ($where == 2)	{
		$dodet = "storage";
	}
	elseif ($where == 3)	{
		$dodet = "backpackdropclean";
	}
	elseif ($where == 4)	{
		$dodet = "backpackitemclean";
	}

	if ($isidrow['itemtype'] == 1)	{
		$it = 1;
		$tbl = "items";
	}
	elseif ($isidrow['itemtype'] == 2)	{
		$it = 2;
		$tbl = "drops";
	}
	elseif ($isidrow['itemtype'] == 3)	{
		$it = 3;
		$tbl = "jewellery";
	}

	$page = "<table width='100%' border='1'><tr><td class='title'>Equip Item</td></tr></table><br />";

	$itemquery = doquery("SELECT * FROM {{table}} WHERE id = '$isidrow[itemid]'", "$tbl");
	$itemrow = mysql_fetch_array($itemquery);

	if ($userrow['level'] < $itemrow['requirement'])	{
		$page .= "You do not meet to requirements to equip the $itemrow[name].  You must be at least level <b>$itemrow[requirement]</b> to equip it.<br />
		<br />
		You may return to your <a href='index.php?do=$dodet'>$dodet</a>.";

		display($page, "Can't equip");
		exit;
	}

	if ($it == 1)	{
		equipitem($isidrow['itemid']);
	}
	elseif ($it == 2)	{
		equipdrop($isidrow['itemid'], $isid);
	}
	elseif ($it == 3)	{
		equipjewellery($isidrow['itemid']);
	}

	delitem($isid);

	if ($go == 1)	{
		$page .= "You have equiped the $itemrow[name].  You may now return to your <a href='index.php?do=$dodet'>$dodet</a>, or use the compass on your right to continue exploring.";
		display($page, "Item Equiped");
	}
	else {
		return;
	}
}

//Moveitem
/**
 * @desc Moves an item from the backpack to storage, and vise versa
 * @return void
 * @param int $isid
 * @param int $where
 */
function moveitem($isid, $where)	{
	global $userrow, $backpackdropslots, $storagedropslots, $backpackitemslots, $storageitemslots, $backpackjewelleryslots, $storagejewelleryslots;

	if ($where == 1)	{
		$dodet = "backpack";
		$move = 2;
		$moveto = "<a href='index.php?do=storage'>storage box</a>";
		$from = "<a href='index.php?do=backpack'>backpack</a>";
	}
	elseif ($where == 2)	{
		$dodet = "storage";
		$move = 1;
		$moveto = "<a href='index.php?do=backpack'>backpack</a>";
		$from = "<a href='index.php?do=storage'>storage box</a>";
	}

	$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' AND charname='".$userrow["charname"]."' LIMIT 1", "homes");
	if (mysql_num_rows($castlequery) <= 0)	{
		header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "" . "index.php?do=$dodet");
		exit;
	}

	$isidquery = doquery("SELECT location, itemtype FROM {{table}} WHERE isid = '$isid' AND playerid = '$userrow[id]'", "itemstorage");

	if (mysql_num_rows($isidquery) != 1)	{
		header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "" . "index.php?do=$dodet");
		exit;
	}
	else {
		$isidrow = mysql_fetch_array($isidquery);
	}

	if ($move == 1)	{
		if ($isidrow['itemtype'] == 1)	{
			$touse = "backpackitemslots";
		}
		elseif ($isidrow['itemtype'] == 2)	{
			$touse = "backpackdropslots";
		}
		elseif ($isidrow['itemtype'] == 3)	{
			$touse = "backpackjewelleryslots";
		}
	}
	else {
		if ($isidrow['itemtype'] == 1)	{
			$touse = "storageitemslots";
		}
		elseif ($isidrow['itemtype'] == 2)	{
			$touse = "storagedropslots";
		}
		elseif ($isidrow['itemtype'] == 3)	{
			$touse = "storagejewelleryslots";
		}
	}

	$page = "<table width='100%' border='1'><tr><td class='title'>Move Item</td></tr></table><br />";

	$bpquery = doquery("SELECT * FROM {{table}} WHERE playerid = '$userrow[id]' AND itemtype = '$isidrow[itemtype]' AND location = '$move'", "itemstorage");
	$slotsr = $$touse - mysql_num_rows($bpquery);

	if ($slotsr < 1)	{
		$page .= "You don't have room in your $moveto to move this item. Either make room in your $moveto to move this item, or return to your $from.";
		display($page, "Can't Move Item");
		exit;
	}

	$updatequery = doquery("UPDATE {{table}} SET location = '$move' WHERE isid = '$isid'", "itemstorage");

	$page .= "You have successfully moved the item to $moveto.  You may now return to your $from, or go to your $moveto.";
	display($page, "Item Moved");
}

//Additem
/**
 * @return true/false
 * @param int $itemid
 * @param int $itemtype
 * @param int $where
 * @param int $playerid
 * @desc Adds an item into storage
*/
function additem($itemid, $itemtype, $where, $playerid='')	{
	if (empty($playerid))	{
		global $userrow;
		$playerid = $userrow[id];
	}

	$query = "INSERT INTO dk_itemstorage VALUES
		  ('', '$playerid', '$itemtype', '$itemid', '$where')";
	$result = mysql_query($query);

	if ($result)	{
		return true;
	}
	else {
		return false;
	}
}

//Delitem
/**
 * @return true/false
 * @param int $itemid
 * @desc Deletes a stored item
*/
function delitem($itemstorageid)	{
	global $userrow;

	$query = "DELETE FROM dk_itemstorage
		  WHERE isid = '$itemstorageid'
		  AND playerid = '$userrow[id]' ";
	$result = mysql_query($query)	or die(mysql_error());

	if (mysql_affected_rows() != 0)	{
		return true;
	}
	else {
		return false;
	}
}

//Equipitem
/**
 * @return true/false
 * @param int $itemid
 * @desc Equips a regular item, and changes the user's stats based on the item
*/
function equipitem($itemid)	{
	global $userrow;

	//Do item query.
	$query = "SELECT * FROM dk_items WHERE id = '$itemid' LIMIT 1";
	$result = mysql_query($query)	or die("Something went terribly wrong");

	$itemsrow = mysql_fetch_array($result);

	//Set item type.
	if ($itemsrow[type] == 1)	{ //weapon
		$what = "attackpower";
		$whatid = "weaponid";
		$whatname = "weaponname";
	}
	elseif ($itemsrow[type] == 2)	{ //armor
		$what = "defensepower";
		$whatid = "armorid";
		$whatname = "armorname";
	}
	elseif ($itemsrow[type] == 3)	{ //shield
		$what = "defensepower";
		$whatid = "shieldid";
		$whatname = "shieldname";
	}
	elseif ($itemsrow[type] == 4)	{ //helm
		$what = "defensepower";
		$whatid = "helmid";
		$whatname = "helmname";
	}
	elseif ($itemsrow[type] == 5)	{ //legs
		$what = "defensepower";
		$whatid = "legsid";
		$whatname = "legsname";
	}
	elseif ($itemsrow[type] == 6)	{ //gauntlets
		$what = "attackpower";
		$whatid = "gauntletsid";
		$whatname = "gauntletsname";
	}
	else {
		return false;
	}

	//Unequip allready equpid item.
	if ($userrow[$whatid] != 0) {
		$ue = unequipitem($userrow[$whatid]);
		if (!$ue) { return false; }

		$query = "SELECT * FROM dk_users
			  WHERE id = '$userrow[id]' ";
		$result = mysql_query($query);
		$userrow = mysql_fetch_array($result);
	}

	// Special item fields.
	$specialchange1 = "";
        if ($itemsrow["special"] != "X") {
		$special = explode(",",$itemsrow["special"]);
		$tochange = $special[0];
		$userrow[$tochange] = $userrow[$tochange] + $special[1];
		$specialchange1 = "$tochange='".$userrow[$tochange]."',";
		if ($tochange == "strength") { $userrow["attackpower"] += $special[1]; }
		if ($tochange == "dexterity") { $userrow["defensepower"] += $special[1]; }
        }

        // New stats.
        $newstat = $userrow[$what] + $itemsrow["attribute"];
        $newid = $itemsrow["id"];
        $newname = $itemsrow["name"];
        $userid = $userrow["id"];
        if ($userrow["currenthp"] > $userrow["maxhp"]) { $newhp = $userrow["maxhp"]; } else { $newhp = $userrow["currenthp"]; }
        if ($userrow["currentmp"] > $userrow["maxmp"]) { $newmp = $userrow["maxmp"]; } else { $newmp = $userrow["currentmp"]; }
        if ($userrow["currenttp"] > $userrow["maxtp"]) { $newtp = $userrow["maxtp"]; } else { $newtp = $userrow["currenttp"]; }
        if ($userrow["currentap"] > $userrow["maxap"]) { $newap = $userrow["maxap"]; } else { $newap = $userrow["currentap"]; }

        // Final update.
        $updatequery = "UPDATE dk_users SET $specialchange1 $what='$newstat', $whatid='$newid', $whatname='$newname', currenthp='$newhp', currentmp='$newmp', currenttp='$newtp', currentap='$newap' WHERE id='$userid'";
        mysql_query($updatequery)	or die("DAMN THAT CHICKEN!!!!");

//        die(mysql_num_rows($result) . "items in db, user = $userrow[id], itemid = $itemid, item row = $itemsrow, updatequery = $updatequery");

        //Set return.
        if (!$updatequery)	{
        	return false;
        }
        else {
        	return true;
        }
}

//Unequipitem
/**
 * @return true/false
 * @param int $itemid
 * @desc Unequips a regular item, and changes the user's stats based on the item
*/
function unequipitem($itemid) {
	global $userrow;

	//Do item query.
	$query = "SELECT * FROM dk_items WHERE id = '$itemid' LIMIT 1";
	$result = mysql_query($query);

	$itemsrow2 = mysql_fetch_array($result);

	//Set item type.
	if ($itemsrow2[type] == 1)	{ //weapon
		$what = "attackpower";
		$whatid = "weaponid";
		$whatname = "weaponname";
	}
	elseif ($itemsrow2[type] == 2)	{ //armor
		$what = "defensepower";
		$whatid = "armorid";
		$whatname = "armorname";
	}
	elseif ($itemsrow2[type] == 3)	{ //shield
		$what = "defensepower";
		$whatid = "shieldid";
		$whatname = "shieldname";
	}
	elseif ($itemsrow2[type] == 4)	{ //helm
		$what = "defensepower";
		$whatid = "helmid";
		$whatname = "helmname";
	}
	elseif ($itemsrow2[type] == 5)	{ //legs
		$what = "defensepower";
		$whatid = "legsid";
		$whatname = "legsname";
	}
	elseif ($itemsrow2[type] == 6)	{ //gauntlets
		$what = "attackpower";
		$whatid = "gauntletsid";
		$whatname = "gauntletsname";
	}
	else {
		return false;
	}

	// Special item fields.
	$specialchange2 = "";
	if ($itemsrow2["special"] != "X") {
		$special2 = explode(",",$itemsrow2["special"]);
		$tochange2 = $special2[0];
		$userrow[$tochange2] = $userrow[$tochange2] - $special2[1];
		$specialchange2 = "$tochange2='".$userrow[$tochange2]."',";
		if ($tochange2 == "strength") { $userrow["attackpower"] -= $special2[1]; }
		if ($tochange2 == "dexterity") { $userrow["defensepower"] -= $special2[1]; }
        }

        // New stats.
        $newstat = $userrow[$what] - $itemsrow2["attribute"];
//        $newid = $itemsrow2["id"];
        $newname = $itemsrow2["name"];
        $userid = $userrow["id"];
        if ($userrow["currenthp"] > $userrow["maxhp"]) { $newhp = $userrow["maxhp"]; } else { $newhp = $userrow["currenthp"]; }
        if ($userrow["currentmp"] > $userrow["maxmp"]) { $newmp = $userrow["maxmp"]; } else { $newmp = $userrow["currentmp"]; }
        if ($userrow["currenttp"] > $userrow["maxtp"]) { $newtp = $userrow["maxtp"]; } else { $newtp = $userrow["currenttp"]; }
        if ($userrow["currentap"] > $userrow["maxap"]) { $newap = $userrow["maxap"]; } else { $newap = $userrow["currentap"]; }

        // Final update.
        $updatequery = "UPDATE dk_users SET $specialchange2 $what='$newstat', $whatid='0', $whatname='$newname', currenthp='$newhp', currentmp='$newmp', currenttp='$newtp', currentap='$newap' WHERE id='$userid'";
        $ur = mysql_query($updatequery);

//        die($updatequery);

        //Put unequiped item in storage
        if (empty($_GET['where']))	{
        	$_GET['where'] = 2;
        }
        additem($itemid, 1, $_GET['where']);

        //Set return.
        if (!$ur)	{
        	return false;
        }
        else {
        	return true;
        }
}

//Equipdrop
/**
 * @desc Equips an item that is a drop item (item type 2)
 * @return void
 * @param unknown_type $dropid
 */
function equipdrop($dropid, $isid)	{
	global $userrow;

	//Do item query.
	$query = doquery("SELECT * FROM {{table}} WHERE id = '$itemid' LIMIT 1", "drops");
	$itemsrow = mysql_fetch_array($query);

	if (empty($_POST['slot']))	{
		$page = "Equip this item to which slot?  (Notice: Equip this item to a non-empty slot will cause the previously equiped item to be unequiped.) <br />
		<br />
		<form action='index.php?do=equipitem:" . $isid . "&amp;where=" . $_GET['where'] . "' method='post'>
		<select name='slot'>
		 <option value=''>Choose a Slot</option>
		 <option value='1'>Slot 1: $userrow[slot1name]</option>
		 <option value='2'>Slot 2: $userrow[slot2name]</option>
		 <option value='3'>Slot 3: $userrow[slot3name]</option>
		 <option value='4'>Slot 4: $userrow[slot4name]</option>
		 <option value='5'>Slot 5: $userrow[slot5name]</option>
		 <option value='6'>Slot 6: $userrow[slot6name]</option>";

		if ($userrow['level'] >= 75)	{
			$page .= "<option value='7'>Slot 7: $userrow[slot7name]</option>";
		}
		if ($userrow['level'] >= 100)	{
			$page .= "<option value='8'>Slot 8: $userrow[slot8name]</option>";
		}

		$page .= "</select>
		<input type='submit' value='Equip' />
		</form>";

		display($page, "Select a Equip Slot");
		exit;
	}

	//USING: Old equip drop code
	$dropquery = doquery("SELECT * FROM {{table}} WHERE id='$dropid' LIMIT 1", "drops");
	$droprow = mysql_fetch_array($dropquery);

	$slot = $_POST["slot"];

	if ($userrow["slot".$slot."id"] != 0) {

         $slotquery = doquery("SELECT * FROM {{table}} WHERE id='".$userrow["slot".$slot."id"]."' LIMIT 1", "drops");
         $slotrow = mysql_fetch_array($slotquery);

         $old1 = explode(",",$slotrow["attribute1"]);
         if ($slotrow["attribute2"] != "X") { $old2 = explode(",",$slotrow["attribute2"]); } else { $old2 = array(0=>"maxhp",1=>0); }
         $new1 = explode(",",$droprow["attribute1"]);
         if ($droprow["attribute2"] != "X") { $new2 = explode(",",$droprow["attribute2"]); } else { $new2 = array(0=>"maxhp",1=>0); }

         $userrow[$old1[0]] -= $old1[1];
         $userrow[$old2[0]] -= $old2[1];
         if ($old1[0] == "strength") { $userrow["attackpower"] -= $old1[1]; }
         if ($old1[0] == "dexterity") { $userrow["defensepower"] -= $old1[1]; }
         if ($old2[0] == "strength") { $userrow["attackpower"] -= $old2[1]; }
         if ($old2[0] == "dexterity") { $userrow["defensepower"] -= $old2[1]; }

         $userrow[$new1[0]] += $new1[1];
         $userrow[$new2[0]] += $new2[1];
         if ($new1[0] == "strength") { $userrow["attackpower"] += $new1[1]; }
         if ($new1[0] == "dexterity") { $userrow["defensepower"] += $new1[1]; }
         if ($new2[0] == "strength") { $userrow["attackpower"] += $new2[1]; }
         if ($new2[0] == "dexterity") { $userrow["defensepower"] += $new2[1]; }

         if ($userrow["currenthp"] > $userrow["maxhp"]) { $userrow["currenthp"] = $userrow["maxhp"]; }
         if ($userrow["currentmp"] > $userrow["maxmp"]) { $userrow["currentmp"] = $userrow["maxmp"]; }
         if ($userrow["currenttp"] > $userrow["maxtp"]) { $userrow["currenttp"] = $userrow["maxtp"]; }
         if ($userrow["currentap"] > $userrow["maxap"]) { $userrow["currentap"] = $userrow["maxap"]; }

         $newname = addslashes($droprow["name"]);
         $query = doquery("UPDATE {{table}} SET slot".$_POST["slot"]."name='$newname',slot".$_POST["slot"]."id='".$droprow["id"]."',$old1[0]='".$userrow[$old1[0]]."',$old2[0]='".$userrow[$old2[0]]."',$new1[0]='".$userrow[$new1[0]]."',$new2[0]='".$userrow[$new2[0]]."',attackpower='".$userrow["attackpower"]."',defensepower='".$userrow["defensepower"]."',currenthp='".$userrow["currenthp"]."',currentmp='".$userrow["currentmp"]."',currenttp='".$userrow["currenttp"]."',currentap='".$userrow["currentap"]."',dropcode='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

         additem($slotrow[id], 2, $_GET['where']);

     } else {

         $new1 = explode(",",$droprow["attribute1"]);
         if ($droprow["attribute2"] != "X") { $new2 = explode(",",$droprow["attribute2"]); } else { $new2 = array(0=>"maxhp",1=>0); }

         $userrow[$new1[0]] += $new1[1];
         $userrow[$new2[0]] += $new2[1];
         if ($new1[0] == "strength") { $userrow["attackpower"] += $new1[1]; }
         if ($new1[0] == "dexterity") { $userrow["defensepower"] += $new1[1]; }
         if ($new2[0] == "strength") { $userrow["attackpower"] += $new2[1]; }
         if ($new2[0] == "dexterity") { $userrow["defensepower"] += $new2[1]; }

         $newname = addslashes($droprow["name"]);
         $query = doquery("UPDATE {{table}} SET slot".$_POST["slot"]."name='$newname',slot".$_POST["slot"]."id='".$droprow["id"]."',$new1[0]='".$userrow[$new1[0]]."',$new2[0]='".$userrow[$new2[0]]."',attackpower='".$userrow["attackpower"]."',defensepower='".$userrow["defensepower"]."',dropcode='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

     }
     //End Usage
}

//EquipJewellery
/**
 * @desc Equips a jewellery item (THIS FUNCTION USES MOST OF THE OLD JEWELLERY EQUIPING CODE)
 * @return void
 * @param int $itemid
 */
function equipjewellery($itemid)	{
	global $userrow;

	$userid = $userrow['id'];

	$itemsquery = doquery("SELECT * FROM {{table}} WHERE id='$itemid' LIMIT 1", "jewellery");
	$itemsrow = mysql_fetch_array($itemsquery);

	if ($itemsrow["type"] == 1) { // ring

    	// Check if they already have an item in the slot.
        if ($userrow["ringid"] != 0) {
            $itemsquery2 = doquery("SELECT * FROM {{table}} WHERE id='".$userrow["ringid"]."' LIMIT 1", "jewellery");
            $itemsrow2 = mysql_fetch_array($itemsquery2);
            additem($itemsrow2['id'], 3, $_GET['where'], $userrow['id']);
        } else {
            $itemsrow2 = array("attribute"=>0,"buycost"=>0,"special"=>"X");
        }

        // New stats.
        $newattack = $userrow["magicfind"] + $itemsrow["attribute"] - $itemsrow2["attribute"];
        $newid = $itemsrow["id"];
        $newname = $itemsrow["name"];
        $userid = $userrow["id"];
        // Final update.
        $updatequery = doquery("UPDATE {{table}} SET magicfind='$newattack', ringid='$newid', ringname='$newname' WHERE id='$userid' LIMIT 1", "users");

    } elseif ($itemsrow["type"] == 2) { //amulet

    	// Check if they already have an item in the slot.
        if ($userrow["amuletid"] != 0) {
            $itemsquery2 = doquery("SELECT * FROM {{table}} WHERE id='".$userrow["amuletid"]."' LIMIT 1", "jewellery");
            $itemsrow2 = mysql_fetch_array($itemsquery2);
            additem($itemsrow2['id'], 3, $_GET['where'], $userrow['id']);
        } else {
            $itemsrow2 = array("attribute"=>0,"buycost"=>0,"special"=>"X");
        }

        // New stats.
        $newdefense = $userrow["magicfind"] + $itemsrow["attribute"] - $itemsrow2["attribute"];
        $newid = $itemsrow["id"];
        $newname = $itemsrow["name"];
        $userid = $userrow["id"];
        // Final update.
        $updatequery = doquery("UPDATE {{table}} SET magicfind='$newdefense', amuletid='$newid', amuletname='$newname' WHERE id='$userid' LIMIT 1", "users");

    }
}

/*

CODED BY MARK SAUVERWALD FOR ADAM DEAR & DK-RPG.COM
E-mail: namadoor@gmail.com
Web Development by Auroria

*/

?>