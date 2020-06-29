<?php

//Player Market Vars
$listing_times = array(1, 3, 5, 7); //Set the time in days that players can list items for.
$listing_fees = array(30, 75, 150, 250); //The fees in gold for listing items.  The listing fees corospond to the respective listing time above.
$sell_fee = 5; //The percent of the sale price a user must pay if it sells.

$trends_history = 7; //The number of days of history of market trends to determine prices.
$commission = 10; //Precent of commission.  For example, if and item is at par and par value = 100 then a 10 commission would result in sell value being 90, and buy 110.

$town_link = "You may return to <a href='index.php'>town</a>, look in your <a href='index.php?do=backpack'>backpack</a> or use the compass to start exploring.";

//PLAYER MARKET -- View the items for sale in the market

		//Get Times
		 //Start Select
		  $time_select = "<select name='time'><option value='-1'></option>";
		 //Get times
		  for ($i = 0; !empty($listing_times[$i]); $i++) {
		  	$time_select .= "<option value='$i'>$listing_times[$i] days - $listing_fees[$i] gold</option>";
		  }
		 //End Select
		  $time_select .= "</select>";

/**
 * @desc Views the item listings
 * @return void
 */
function playermarket()
{
	//Get needed vars
	global $userrow, $listing_times, $listing_fees, $sell_fee, $time_select, $town_link;

        $updatequery = doquery("UPDATE {{table}} SET location='Player Market' WHERE id='".$userrow["id"]."' LIMIT 1", "users");


	//Make sure the user is in a town
	$townquery = doquery("SELECT name FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
	if (mysql_num_rows($townquery) != 1)	{
		header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "index.php");
		exit;
	}

	//Item Listing has expired
	$now = time();
	$expire_query = mysql_query("SELECT * FROM dk_playermarket WHERE playerid = '$userrow[id]' and endtime <= '$now' ") or die(mysql_error() . "|| <BR> ||" . $query);
	if (mysql_num_rows($expire_query) != 0 && ($_GET['show'] != 'add')) {
		$expire_row = mysql_fetch_array($expire_query);

		//Get the item's name
		if ($expire_row[itemtype] == 1)	{
			$table = "items";
		}
		elseif ($expire_row[itemtype] == 2)	{
			$table = "drops";
		}
		elseif ($expire_row[itemtype] == 3)	{
			$table = "jewellery";
		}

		$name_query = doquery("SELECT * FROM {{table}} WHERE id = '$expire_row[itemid]'", $table);
		$name_row = mysql_fetch_array($name_query);

		$page .= "<table width='100%' border='1'><tr><td class='title'>Listing Expired</td></tr></table><br />
		Your listing for <b>$name_row[name]</b> has expired.  Please either relist the item, or cancel the item's listing.<br />
		<br />
		<a href='index.php?do=cancellisting:$expire_row[pmid]&conf=2'>Cancel Listing</a><br />
		<br />
		<form action='index.php?do=playermarket&amp;show=add&amp;conf=2&amp;pmid=$expire_row[pmid]' method='post'>
		<center><table width='95%'><tr><td style='padding:1px; background-color:black;'><table width='100%' style='margins:0px;' cellspacing='1' cellpadding='3'>
		<tr><th style='background-color:#dddddd;'><center>Relist Item</center></th></tr>
		<tr><td style='background-color:#ffffff;'>Item: $name_row[name] <br />
		Price: <input type='text' name='price' size='8' maxlength='10' value='$expire_row[itemprice]'> gold <br />
		Listing Time: $time_select <br />
		<br />
		The item above will be put back onto the market.  You will have to pay the listing fee again.<br />
		<br />
		<input type='submit' value='Relist Item' />

		</td></tr>
		</table>
		</form>
		</td></tr></table></center>";

		display($page, "Item Listing Expired");
	}

	//Search window
	$search = "
	<form action='index.php?do=playermarket&amp;show=that' method='post'>
	<center><table width='95%'><tr><td style='padding:1px; background-color:black;'><table width='100%' style='margins:0px;' cellspacing='1' cellpadding='3'>
	<tr><th style='background-color:#dddddd;'><center>View Items for Sale</center></th></tr>
	<tr><td style='background-color:#ffffff'>View <select name='view'>
	<option value='1'>Items</option>
	<option value='2'>Drops</option>
	<option value='3'>Jewellery</option>
	</select> that are for sale.  List by
	<select name='list'>
	<option value='abc'>Name</option>
	<option value='price'>Price</option>
	<option value='date'>Date Listed</option>
	</select>
	<select name='way'>
	<option value=''>Ascending</option>
	<option value='DESC'>Descending</option>
	</select>
	<input type='submit' value='Go'><br />
	<br />
	<a href='mforum.php'>&gt; View Market Forum</a><br />
	</td></tr></table></td></tr></table>
	</form>
	<br />";

	//main Market screen
	if (empty($_GET['show'])) {
		//Get Items
		 //Start Select
		 $item_select = "<select name='item'><option value=''></option>";
		 //Normal Items
		 $query = "SELECT dk_itemstorage.isid, dk_items.* FROM dk_itemstorage, dk_items
		  WHERE dk_itemstorage.playerid = '$userrow[id]'
		  AND dk_itemstorage.itemtype = 1
		  AND dk_itemstorage.location = 1
		  AND dk_itemstorage.itemid = dk_items.id
		  ORDER BY dk_items.name";
		$result = mysql_query($query)	or die(mysql_error());

		if (mysql_num_rows($result) != 0) {
			$items_in_bp = true;
			$item_select .= "<optgroup label='Items'>";
			while ($ma = mysql_fetch_array($result)) {
				$item_select .= "<option value='$ma[isid]'>$ma[name]</option>";
			}
			$item_select .= "</optgroup>";
		}
		 //Drop Items
		 $query = "SELECT dk_itemstorage.isid, dk_drops.* FROM dk_itemstorage, dk_drops
		  WHERE dk_itemstorage.playerid = '$userrow[id]'
		  AND dk_itemstorage.itemtype = 2
		  AND dk_itemstorage.location = 1
		  AND dk_itemstorage.itemid = dk_drops.id
		  ORDER BY dk_drops.name";
		$result = mysql_query($query)	or die(mysql_error());

		if (mysql_num_rows($result) != 0) {
			$items_in_bp = true;
			$item_select .= "<optgroup label='Drops'>";
			while ($ma = mysql_fetch_array($result)) {
				$item_select .= "<option value='$ma[isid]'>$ma[name]</option>";
			}
			$item_select .= "</optgroup>";
		}
		 //Jewellery
		 $query = "SELECT dk_itemstorage.isid, dk_jewellery.* FROM dk_itemstorage, dk_jewellery
		  WHERE dk_itemstorage.playerid = '$userrow[id]'
		  AND dk_itemstorage.itemtype = 3
		  AND dk_itemstorage.location = 1
		  AND dk_itemstorage.itemid = dk_jewellery.id
		  ORDER BY dk_jewellery.name";
		$result = mysql_query($query)	or die(mysql_error());

		if (mysql_num_rows($result) != 0) {
			$items_in_bp = true;
			$item_select .= "<optgroup label='Jewellery'>";
			while ($ma = mysql_fetch_array($result)) {
				$item_select .= "<option value='$ma[isid]'>$ma[name]</option>";
			}
			$item_select .= "</optgroup>";
		}
		//End Select
		 $item_select .= "</select>";


			$numquery = doquery("SELECT * FROM {{table}} ", "playermarket");
			$marketitems = mysql_num_rows($numquery);

		 //Start the display
		 $page .= "<table width='100%' border='1'><tr><td class='title'>Player Market</td></tr></table><br />
		Welcome to the Player Market!  Here you can browse the items other players put up for sale, or put an item up for sale yourself.<p>Currently there are <b>$marketitems</b> items for sale.<br /><br />";

		//Display the Search Window


		$page .= $search;


		//Display the list window
		if ($items_in_bp == true) {
			$page .= "<form action='index.php?do=playermarket&amp;show=add' method='post'>
			<center><table width='95%'><tr><td style='padding:1px; background-color:black;'><table width='100%' style='margins:0px;' cellspacing='1' cellpadding='3'>
			<tr><th style='background-color:#dddddd;'><center>List an Item</center></th></tr>
			<tr><td style='background-color:#ffffff;'>
			<table width='95%'>
			<tr><td>Item:</td><td>$item_select</td></tr>
			<tr><td>Price:</td><td><input type='text' name='price' size='8' maxlength='10'> gold</td></tr>
			<tr><td>Listing Time:</td><td>$time_select</td></tr>
			<tr><td>Short Description/Comments<br />(max 255 characters):</td><td><textarea name='comments' rows='2' cols='30'></textarea></td></tr>
			</table>
			If you choose to list the above item it will be removed from your backpack and placed in the market for the listing time that you have selected.
			If it sells the gold will be deposited into your bank account, minus a $sell_fee% tax.  You will also have to pay a listing fee for the listing time you selected.<br />
			<br />
			<input type='submit' value='List Item' />

			</td></tr>
			</table>
			</td></tr></table></form></center>";
		}
		else {
			$page .= "<center><table width='95%'><tr><td style='padding:1px; background-color:black;'><table width='100%' style='margins:0px;' cellspacing='1' cellpadding='3'>
			<tr><th style='background-color:#dddddd;'><center>List an Item</center></th></tr>
			<tr><td style='background-color:#ffffff;'>You currently have no items in your backpack.  Any items that you wish to put up for sale must be in your backpack.
			</td></tr>
			</table>
			</form>
			</td></tr></table></center>";
		}

		$page .= "<br /><br />$town_link";

		display($page, "Player market");

	}

	//Put the needed vars into the address bar
	elseif ($_GET['show'] == 'that')	{
		header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "index.php?do=playermarket&show=list&type=$_POST[view]&list=$_POST[list]&dir=$_POST[way]");
		exit;
	}

	//Dipslay the items for sale
	elseif ($_GET['show'] == 'list')	{
		//Get the type of item
		if ($_GET[type] == '1')	{
			$table = 'items';
		}
		elseif ($_GET[type] == '2')	{
			$table = 'drops';
		}
		elseif ($_GET[type] == '3')	{
			$table = 'jewellery';
		}
		else {
			header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "index.php?do=playermarket");
			exit;
		}

		//Get what to sort by
		if ($_GET['list'] == 'abc')	{
			$orderby = "dk_{$table}.name";
		}
		elseif ($_GET['list'] == 'price') {
			$orderby = "dk_playermarket.itemprice";
		}
		elseif ($_GET['list'] == 'date') {
			$orderby = "dk_playermarket.datelisted";
		}
		else {
			header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "index.php?do=playermarket");
			exit;
		}

		//Start the page
		$page .= "<table width='100%' border='1'><tr><td class='title'>Player Market</td></tr></table><br />
		<center><table width='95%'><tr><td style='padding:1px; background-color:black;'><table width='100%' style='margins:0px;' cellspacing='1' cellpadding='3'><tr><th colspan='4' style='background-color:#dddddd;'><center>" . ucfirst($table) . " for Sale</center></th></tr>
		<tr><th width='40%' style='background-color:#dddddd;'>Item</th><th width='15%' style='background-color:#dddddd;'>Seller</th><th width='25%' style='background-color:#dddddd;'>Price</th><th width='20%' style='background-color:#dddddd;'>Option</th></tr>";

		//Do the query to select the items
		$query = "SELECT dk_playermarket.*, dk_{$table}.*, dk_users.charname, dk_users.id FROM dk_playermarket, dk_{$table}, dk_users
			  WHERE dk_playermarket.itemtype = '$_GET[type]'
			  AND dk_playermarket.itemid = dk_{$table}.id
			  AND dk_playermarket.playerid = dk_users.id
			  AND dk_playermarket.endtime >= " . time() . "
			  ORDER BY $orderby $_GET[dir]";
		$marketquery = mysql_query($query)	or die(mysql_error());

		$bgcolor = "#ffffff";

		//Display the items
		while ($marketrow = mysql_fetch_array($marketquery))	{
			if ($marketrow[itemprice] != '2147483647') {
				$pce = number_format($marketrow[itemprice]) . " gold";
			}
			else {
				$pce = "Your Soul";
			}

			$page .= "<tr><td style='background-color:$bgcolor;'><a href='index.php?do=viewlisting:$marketrow[pmid]'>$marketrow[name]</a></td>
			<td style='background-color:$bgcolor;'><a href='index.php?do=onlinechar:$marketrow[playerid]'>$marketrow[charname]</a></td>
			<td style='background-color:$bgcolor;'>$pce</td>";

			if ($marketrow['id'] != $userrow['id']) {
				if ($userrow['gold'] >= $marketrow['itemprice']) {
					$page .= "<td style='background-color:$bgcolor;'><a href='index.php?do=buyfrommarket:$marketrow[pmid]'>Buy</a></td></tr>";
				}
				elseif ($marketrow[itemprice] == '2147483647') {
					$page .= "<td style='background-color:$bgcolor;''><i>Don't do it!</i></td>";
				}
				else {
					$page .= "<td style='background-color:$bgcolor;''><i>Not enough gold</i></td>";
				}
			}
			else {
				$page .= "<td style='background-color:$bgcolor;''><a href='index.php?do=cancellisting:$marketrow[pmid]'>Cancel Listing</a></td>";
			}

			if ($bgcolor == "#ffffff")	{
				$bgcolor = "#eeeeee";
			}
			else {
				$bgcolor = "#ffffff";
			}
		}
		if (mysql_num_rows($marketquery) == 0)	{
			$page .= "<tr><td style='background-color:$bgcolor;' colspan='4'><i>There are no $table for sale.</i></td></tr>";
		}

		//Close the table
		$page .= "</table></td></tr></table><br /><br />";

		$page .= $search;

		$page .= "<br /><br />$town_link  You may also return to the <a href='index.php?do=playermarket'>market home</a>.";

		display($page, "Player Market - Items for Sale");
	}

	//List an item
	elseif ($_GET['show'] == 'add') {
		//Do confirm
		if ($_GET['conf'] != 1 && $_GET['conf'] != 2) {
			//Get the item
			$typequery = doquery("SELECT * FROM {{table}} WHERE isid = '$_POST[item]' AND location = 1", "itemstorage");
			if (mysql_num_rows($typequery) == 0)	{
				header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "index.php?do=playermarket");
				exit;
			}
			else {
				$typerow = mysql_fetch_array($typequery);
			}

			if ($typerow[itemtype] == 1)	{
				$table = "items";
			}
			elseif ($typerow[itemtype] == 2)	{
				$table = "drops";
			}
			elseif ($typerow[itemtype] == 3)	{
				$table = "jewellery";
			}

			//Get the item
			$itemsquery = doquery("SELECT * FROM {{table}} WHERE id = '$typerow[itemid]'", $table);
			$itemsrow = mysql_fetch_array($itemsquery);

			//Get the listing times
			$tm = $_POST[time];
			$time = $listing_times[$tm];
			$fee = $listing_fees[$tm];

			//Add 1 to the tme var so it has no chance of being empty
			$tme = $_POST['time'] + 1;

			//Display
			if (empty($_POST['item'])) {
				$page .= "<table width='100%' border='1'><tr><td class='title'>Player Market - List Item</td></tr></table><br />
				Please select the item that you wish to list. <br />
				<br />
				[ <a href='index.php?do=playermarket'>Back</a> ]";
			}
			elseif (empty($_POST['price']) || !is_numeric($_POST['price'])) {
				$page .= "<table width='100%' border='1'><tr><td class='title'>Player Market - List Item</td></tr></table><br />
				Please enter a valid price for your <b>$itemsrow[name]</b>.  Valid prices must be only whole numbers. <br />
				<br />
				[ <a href='index.php?do=playermarket'>Back</a> ]";
			}
			elseif ($_POST[time] == '-1') {
				$page .= "<table width='100%' border='1'><tr><td class='title'>Player Market - List Item</td></tr></table><br />
				Please select how long you would like to list your <b>$itemsrow[name]</b> for.<br />
				<br />
				[ <a href='index.php?do=playermarket'>Back</a> ]";
			}
			elseif ($userrow[gold] < $fee) {
				$page .= "<table width='100%' border='1'><tr><td class='title'>Player Market - List Item</td></tr></table><br />
				You do not have enough gold to list this item.  You need <b>$fee</b> gold on hand. <br />
				<br />
				[ <a href='index.php?do=playermarket'>Back</a> ]";
			}
			else {
				$page .= "<table width='100%' border='1'><tr><td class='title'>Player Market - List Item</td></tr></table><br />
				Are you sure you wish to put your <b>$itemsrow[name]</b> up for sale at the price of <b>$_POST[price]</b> gold? <br />
				<br />
				You have chosen to list the item for <b>$time</b> days, which will cost you <b>$fee</b> gold.<br />
				<br />
				<form name='c' action='index.php?do=playermarket&amp;show=add&amp;conf=1' method='post'>
				<input type='hidden' name='isid' value='$_POST[item]' />
				<input type='hidden' name='price' value='$_POST[price]' />
				<input type='hidden' name='time' value='$tme' />
				<input type='hidden' name='comments' value='$_POST[comments]' />
				[ <a href='#' onclick=\"document.forms.c.submit()\">Yes</a> ] [ <a href='index.php?do=playermarket'>No</a> ]";
			}
			 //Dipslay the page
			 display($page, "Player Market - List Item");
		}
		elseif ($_GET['conf'] == 1) {
			if (empty($_POST['isid']) || empty($_POST['price']) || empty($_POST['time']) || !is_numeric($_POST['price'])) {
				header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "index.php?do=playermarket");
				exit;
			}

			//Restore the correct time
			$_POST['time']--;

			//Get the listing times
			$tm = $_POST[time];
			$time = $listing_times[$tm];
			$fee = $listing_fees[$tm];

			//Cheat Guards
			$typequery = doquery("SELECT * FROM {{table}} WHERE isid = '$_POST[isid]' AND location = 1", "itemstorage");
			if (mysql_num_rows($typequery) == 0)	{
				header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "index.php?do=playermarket");
				exit;
			}
			else {
				$typerow = mysql_fetch_array($typequery);
				$item = $typerow[itemid];
				$type = $typerow[itemtype];
			}

			if (!empty($listing_times[$i])) {
				header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "index.php?do=playermarket");
				exit;
			}
			if ($userrow[gold] < $fee) {
				header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "index.php?do=playermarket");
				exit;
			}

			//Remove item from backpack
			$del = doquery("DELETE FROM {{table}} WHERE isid = '$_POST[isid]'", "itemstorage");

			//Charge the fee
			$fee = doquery("UPDATE {{table}} SET gold = gold - $fee WHERE id = $userrow[id]", "users");

			//Add the item to the market
			$end = time() + (86400 * $time);
			$add = doquery("INSERT INTO {{table}} VALUES ('', '$userrow[id]', '$type', '$item', '$_POST[price]', '". time() . "', '$end', '" . addslashes($_POST[comments]) . "')", "playermarket");

			//Done!
			$page .= "<table width='100%' border='1'><tr><td class='title'>Player Market - Item Listed</td></tr></table><br />
			You have successfully listed your item on the market!<br />
			<br />
			[ <a href='index.php?do=playermarket'>Back</a> ]";

			 //Dipslay the page
			 display($page, "Player Market - List Item");
		}


		//relist
		else {
			//Get the listing times
			$tm = $_POST[time];
			$time = $listing_times[$tm];
			$fee = $listing_fees[$tm];

			$typequery = doquery("SELECT * FROM {{table}} WHERE pmid = '$_GET[pmid]'", "playermarket");
			if (mysql_num_rows($typequery) == 0)	{
				die('1');
				header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "index.php?do=playermarket");
				exit;
			}
			else {
				$typerow = mysql_fetch_array($typequery);
				$item = $typerow[itemid];
				$type = $typerow[itemtype];
			}

			if ($typerow[itemtype] == 1)	{
				$table = "items";
			}
			elseif ($typerow[itemtype] == 2)	{
				$table = "drops";
			}
			elseif ($typerow[itemtype] == 3)	{
				$table = "jewellery";
			}

			//Get the item
			$itemsquery = doquery("SELECT * FROM {{table}} WHERE id = '$typerow[itemid]'", $table);
			$itemsrow = mysql_fetch_array($itemsquery);

			//Errors
			if (empty($_POST['price']) || !is_numeric($_POST['price'])) {
				$page .= "<table width='100%' border='1'><tr><td class='title'>Player Market - List Item</td></tr></table><br />
				Please enter a valid price for your item.  Valid prices must be only whole numbers. <br />
				<br />
				[ <a href='index.php?do=playermarket'>Back</a> ]";

				display($page, "Player Market - Relist Item");
				exit;
			}
			elseif ($_POST[time] == '-1') {
				$page .= "<table width='100%' border='1'><tr><td class='title'>Player Market - List Item</td></tr></table><br />
				 Please select how long you would like to list your item for.<br />
				<br />
				[ <a href='index.php?do=playermarket'>Back</a> ]";

				display($page, "Player Market - Relist Item");
				exit;
			}
			elseif ($userrow[gold] < $fee) {
				$page .= "<table width='100%' border='1'><tr><td class='title'>Player Market - List Item</td></tr></table><br />
				You do not have enough gold to list this item.  You need <b>$fee</b> gold on hand. <br />
				<br />
				[ <a href='index.php?do=playermarket'>Back</a> ]";

				display($page, "Player Market - Relist Item");
				exit;
			}

			//Cheat guards
			$is_own = doquery("SELECT pmid FROM {{table}} WHERE pmid = '$_GET[pmid]' AND playerid = '$userrow[id]'", "playermarket");

			if (mysql_num_rows($is_own) != 1) {
				header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "index.php?do=playermarket");
				exit;
			}

			//Charge the fee
			$fee = doquery("UPDATE {{table}} SET gold = gold - $fee WHERE id = $userrow[id]", "users");

			//Readd the item to the market
			$end = time() + (86400 * $time);
			$add = doquery("UPDATE {{table}} SET itemprice = '$_POST[price]', datelisted = '". time() . "', endtime = '$end' WHERE pmid = '$_GET[pmid]'", "playermarket");

			//Done!
			$page .= "<table width='100%' border='1'><tr><td class='title'>Player Market - Item Relisted</td></tr></table><br />
			You have successfully relisted your item on the market!<br />
			<br />
			[ <a href='index.php?do=playermarket'>Back</a> ]";

			display($page, "Player Market - Item Relisted");

		}
	}
}

function buy_from_market($pmid) {
	//Get needed vars
	global $userrow, $listing_times, $listing_fees, $sell_fee, $backpackitemslots, $backpackdropslots, $backpackjewelleryslots;

	//Make sure item is still around
	$query = "SELECT * FROM dk_playermarket
		  WHERE pmid = $pmid";
	$result = mysql_query($query)	or die(mysql_error());

	if (mysql_num_rows($result) != 1) {
		$page .= "<table width='100%' border='1'><tr><td class='title'>Player Market - Buy Error</td></tr></table><br />
		Sorry, but the item you attempted to purchase is no longer on the market.  Perhaps it has already been sold. <br />
		<br />
		You may return to the <a href='index.php?do=playermarket'>market</a>, <a href='index.php'>town</a> or use the compass to start exploring.";

		display($page, "Player Market - Buy Error");

		exit;
	}
	else {
		$ma = mysql_fetch_array($result);

		//Get the item's name
		if ($ma[itemtype] == 1)	{
			$table = "items";
		}
		elseif ($ma[itemtype] == 2)	{
			$table = "drops";
		}
		elseif ($ma[itemtype] == 3)	{
			$table = "jewellery";
		}
	}

	if ($userrow['gold'] < $ma['itemprice']) {
		$page .= "<table width='100%' border='1'><tr><td class='title'>Player Market - Not Enough Gold</td></tr></table><br />
		You do not have enough gold to buy that item.  Gold must be in hand to buy.<br />
		<br />
		You may return to the <a href='index.php?do=playermarket'>market</a>, <a href='index.php'>town</a> or use the compass to start exploring.";

		display($page, "Player Market - Buy Error");

		exit;
	}

	//Make sure backpack isn't full.
	$countquery = doquery("SELECT isid FROM {{table}} WHERE playerid = $userrow[id] AND itemtype = $ma[itemtype] AND location = 1", "itemstorage");
	$count = mysql_num_rows($countquery);


	if ($ma['itemtype'] == 1) {
		if ($count >= $backpackitemslots) {
			$full = true;
		}
	}
	elseif ($ma['itemtype'] == 2)	{
		if ($count >= $backpackdropslots) {
			$full = true;
		}
	}
	elseif ($ma['itemtype'] == 3)	{
		if ($count >= $backpackjewelleryslots) {
			$full = true;
		}
	}

	if ($full) {
		$page .= "<table width='100%' border='1'><tr><td class='title'>Player Market - Backpack Full</td></tr></table><br />
		You do not have the room in your backpack to store this item. Please go to your <a href='index.php?do=backpack'>backpack</a> to clear out some room and then return to market.";

		display($page, "Player Market - Backpack Full");

		exit;
	}

	//Get confermation
	$name_query = doquery("SELECT * FROM {{table}} WHERE id = $ma[itemid]", $table);
	$name_row = mysql_fetch_array($name_query);

	if ($_GET['conf'] != 1) {
		//Display the conf page
		$page .= "<table width='100%' border='1'><tr><td class='title'>Player Market - Buy Item</td></tr></table><br />
		Are you sure you want to buy <b>$name_row[name]</b> for <b>$ma[itemprice]</b>? <br />
		<br />
		[ <a href='index.php?do=buyfrommarket:$pmid&amp;conf=1'>Yes</a> ] [ <a href='javascript:history.go(-1)'>No</a> ]";

		display($page, "Player Market - Confirm Purchase");

		exit;
	}

	//Make the purchase

	//Pay for the item
	$pay = doquery("UPDATE {{table}} SET gold = gold - $ma[itemprice] WHERE id = '$userrow[id]'", "users");

	//Tansfer funds to seller's account
	$sale = $ma['itemprice'] - (($sell_fee / 100) * $ma['itemprice']);
	$sale = floor($sale);

	$sale_q = doquery("UPDATE {{table}} SET bank = bank + $sale WHERE id = '$ma[playerid]'", "users");

	//Delete the item from the market
	$delete = doquery("DELETE FROM {{table}} WHERE pmid = $pmid", "playermarket", "playermarket");

	//Add the item to the buyer's inventory
	$add = doquery("INSERT INTO {{table}} VALUES ('', '$userrow[id]', '$ma[itemtype]', '$ma[itemid]', '1')", "itemstorage");

	//Send a Game Mail to the Seller
	$username_query = doquery("SELECT charname FROM {{table}} WHERE id = '$ma[playerid]'", "users");
	$ua = mysql_fetch_array($username_query);

	$content = "Congratulations! <br />
	You have successfully sold your <b>$name_row[name]</b> on the player market for to <a href='index.php?do=onlinechar:$userrow[id]'>$userrow[charname]</a> for <b>$ma[itemprice]</b> gold.  After the $sell_fee% sell fee, you have <b>$sale</b> gold.  This gold has been deposited into your bank account. <br />
	<br />
	This game mail was automatically sent.  Please do not reply to it.";

	$content = addslashes($content);

	$gm = doquery("INSERT INTO {{table}} SET postdate=NOW(),author='Player Market',recipient='$ua[charname]',subject='Your item has been sold!',content='$content'", "gamemail");

	//Sale Compete!
	$page .= "<table width='100%' border='1'><tr><td class='title'>Player Market - Item Bought</td></tr></table><br />
	You have purchased a <b>$name_row[name]</b> for <b>$ma[itemprice]</b> gold.  It has been put in your <a href='index.php?do=backpack'>backpack</a>.<br />
	<br />
	You may return to the <a href='index.php?do=playermarket'>market</a>, <a href='index.php'>town</a>, view your <a href='index.php?do=backpack'>backpack</a> or use the compass to start exploring.";

	display($page, "Player Market - Item Bought");

	exit;
}

function cancel_listing($pmid) {
	//Get needed vars
	global $userrow, $listing_times, $listing_fees, $sell_fee, $backpackitemslots, $backpackdropslots, $backpackjewelleryslots;

	//Make sure the user owns the listing
	$check_query = doquery("SELECT * FROM {{table}} WHERE pmid = '$pmid' AND playerid = '$userrow[id]'", "playermarket");

	if (mysql_num_rows($check_query) != 1) {
		header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "index.php?do=playermarket");
		exit;
	}

	//Make sure item is still around
	$query = "SELECT * FROM dk_playermarket
		  WHERE pmid = $pmid";
	$result = mysql_query($query)	or die(mysql_error());

	if (mysql_num_rows($result) != 1) {
		$page .= "<table width='100%' border='1'><tr><td class='title'>Player Market - Buy Error</td></tr></table><br />
		Sorry, but the item you listing you are attempting to cancel is no longer on the market.  It may have sold, or you may have already cancelled it. <br />
		<br />
		You may return to the <a href='index.php?do=playermarket'>market</a>, <a href='index.php'>town</a> or use the compass to start exploring.";

		display($page, "Player Market - Cancel Error");

		exit;
	}
	else {
		$ma = mysql_fetch_array($result);

		//Get the item's name
		if ($ma[itemtype] == 1)	{
			$table = "items";
		}
		elseif ($ma[itemtype] == 2)	{
			$table = "drops";
		}
		elseif ($ma[itemtype] == 3)	{
			$table = "jewellery";
		}
	}

	//Make sure backpack isn't full.
	$countquery = doquery("SELECT isid FROM {{table}} WHERE playerid = $userrow[id] AND itemtype = $ma[itemtype] AND location = 1", "itemstorage");
	$count = mysql_num_rows($countquery);


	if ($ma['itemtype'] == 1) {
		if ($count >= $backpackitemslots) {
			$full = true;
		}
	}
	elseif ($ma['itemtype'] == 2)	{
		if ($count >= $backpackdropslots) {
			$full = true;
		}
	}
	elseif ($ma['itemtype'] == 3)	{
		if ($count >= $backpackjewelleryslots) {
			$full = true;
		}
	}

	if ($full) {
		$page .= "<table width='100%' border='1'><tr><td class='title'>Player Market - Backpack Full</td></tr></table><br />
		You do not have the room in your backpack to store this item. Please go to your <a href='index.php?do=backpack'>backpack</a> to clear out some room before attempting to cancel your listing.";

		display($page, "Player Market - Backpack Full");

		exit;
	}

	//Get confermation
	$name_query = doquery("SELECT * FROM {{table}} WHERE id = $ma[itemid]", $table);
	$name_row = mysql_fetch_array($name_query);

	if ($_GET['conf'] != 1) {
		//Display the conf page
		$page .= "<table width='100%' border='1'><tr><td class='title'>Player Market - Cancel Listing</td></tr></table><br />
		Are you sure you want to cancel the listing for your <b>$name_row[name]</b>? The listing fee charged to list this item will not be refunded.<br />
		<br />
		[ <a href='index.php?do=cancellisting:$pmid&amp;conf=1'>Yes</a> ] [ <a href='javascript:history.go(-1)'>No</a> ]";

		display($page, "Player Market - Cancel Listing");

		exit;
	}

	//Cancel the listing

	//Delete the item from the market
	$delete = doquery("DELETE FROM {{table}} WHERE pmid = $pmid", "playermarket", "playermarket");

	//Return the item to the seller's inventory
	$add = doquery("INSERT INTO {{table}} VALUES ('', '$userrow[id]', '$ma[itemtype]', '$ma[itemid]', '1')", "itemstorage");

	//Cancel Compete!
	$page .= "<table width='100%' border='1'><tr><td class='title'>Player Market - Listing Cancelled</td></tr></table><br />
	You have cancelled the listing for your <b>$name_row[name]</b>.  It has been put into your <a href='index.php?do=backpack'>backpack</a>.<br />
	<br />
	You may return to the <a href='index.php?do=playermarket'>market</a>, <a href='index.php'>town</a>, view your <a href='index.php?do=backpack'>backpack</a> or use the compass to start exploring.";

	display($page, "Player Market - Listing Cancelled");

	exit;
}

function view_listing($pmid) {
	global $userrow, $town_link;

	if (empty($pmid)) {
		header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "index.php?do=playermarket");
		exit;
	}

	//Make sure item is still around
	$query = "SELECT * FROM dk_playermarket
		  WHERE pmid = $pmid";
	$result = mysql_query($query)	or die(mysql_error());

	if (mysql_num_rows($result) != 1) {
		$page .= "Invalid player market id (pmid).";

		display($page, "Player Market");

		exit;
	}
	else {
		$ma = mysql_fetch_array($result);

		//Get the item's name
		if ($ma[itemtype] == 1)	{
			$table = "items";
		}
		elseif ($ma[itemtype] == 2)	{
			$table = "drops";
		}
		elseif ($ma[itemtype] == 3)	{
			$table = "jewellery";
		}

		$name_query = doquery("SELECT * FROM {{table}} WHERE id = $ma[itemid]", $table);
		$itemsrow = mysql_fetch_array($name_query);
	}

	$seller_query = doquery("SELECT * FROM {{table}} WHERE id = $ma[playerid]", "users");
	$seller_row = mysql_fetch_array($seller_query);

	if (empty($ma[comments])) {
 		$seller_notes = "<i>This seller has not added any comments.</i>";
	}
	else {
		$seller_notes = $ma[comments];
	}

	$page .= "<table width='100%' border='1'><tr><td class='title'>Player Market</td></tr></table><br />
	<center>
	<table width='95%'><tr><td style='padding:1px; background-color:black;'><table width='100%'><tr><th colspan='2' style='background-color:#dddddd;'>Listing Details</th></tr>
	<tr><td width style='background-color:#ffffff;'>Item Listed:</td><td width='60%' style='background-color:#eeeeee;'>$itemsrow[name]</td></tr>
	<tr><td width style='background-color:#ffffff;'>Time Listed:</td><td width='60%' style='background-color:#eeeeee;'>" . date("F jS, Y g:i A", $ma[datelisted]) . "</td></tr>
	<tr><td width style='background-color:#ffffff;'>End Time:</td><td width='60%' style='background-color:#eeeeee;'>" . date("F jS, Y g:i A", $ma[endtime]) . "</td></tr>
	<tr><td width style='background-color:#ffffff;'>Listing Price:</td><td width='60%' style='background-color:#eeeeee;'>" . number_format($ma[itemprice]) . " gold</td></tr>
	<tr><td width style='background-color:#ffffff;'>Seller:</td><td width='60%' style='background-color:#eeeeee;'><a href='index.php?do=onlinechar:$ma[playerid]'>$seller_row[charname]</a></td></tr>
	<tr><td width style='background-color:#ffffff;'>Seller's Comments:</td><td width='60%' style='background-color:#eeeeee;'>$seller_notes<br /><br /></td></tr>
	</table></td></tr></table><br />
	<br />
	<table width='95%'><tr><td style='padding:1px; background-color:black;'><table width='100%'><tr><th colspan='2' style='background-color:#dddddd;'>Item Details</th></tr>";

	if ($ma[itemtype] == 1) {
		if ($itemsrow["type"] == 1 || $itemsrow["type"] == 6) { $image = "weapon"; $power = "Attack"; } elseif ($itemsrow["type"] == 2) { $image = "armor"; $power = "Defense"; } else { $image = "shield"; $power = "Defense"; }
    		if ($itemsrow["special"] != "X") {
        	$special = explode(",",$itemsrow["special"]);
       		if ($special[0] == "maxhp") { $attr = "Max HP"; }
        	elseif ($special[0] == "maxmp") { $attr = "Max MP"; }
        	elseif ($special[0] == "maxtp") { $attr = "Max TP"; }
        	elseif ($special[0] == "goldbonus") { $attr = "Gold Bonus (%)"; }
        	elseif ($special[0] == "expbonus") { $attr = "Experience Bonus (%)"; }
        	elseif ($special[0] == "strength") { $attr = "Strength"; }
        	elseif ($special[0] == "dexterity") { $attr = "Dexterity"; }
        	elseif ($special[0] == "attackpower") { $attr = "Attack"; }
        	elseif ($special[0] == "defensepower") { $attr = "Defense"; }
        	else { $attr = $special[0]; }
        	if ($special[1] > 0) { $stat = "+" . $special[1]; } else { $stat = $special[1]; }
        	$bigspecial = "$attr $stat";
    		} else { $bigspecial = "None"; }

		$page .= "<tr><td width='40%' style='background-color:#eeeeee;'>Item Name:</td><td width='60%' style='background-color:#ffffff;'>$itemsrow[name]</td></tr>
		<tr><td width='40%' style='background-color:#eeeeee;'>Attribute:</td><td width='60%' style='background-color:#ffffff;'><img src=\"images/icon_$image.gif\" alt=\"$image\"> $itemsrow[attribute] $power</td></tr>
		<tr><td width='40%' style='background-color:#eeeeee;'>Special:</td><td width='60%' style='background-color:#ffffff;'>$bigspecial</td></tr>
		<tr><td width='40%' style='background-color:#eeeeee;'>Monster Level:</td><td width='60%' style='background-color:#ffffff;'>$itemsrow[mlevel]</td></tr>
		<tr><td width='40%' style='background-color:#eeeeee;'>Level Requirement:</td><td width='60%' style='background-color:#ffffff;'>$itemsrow[requirement]</td></tr>";
	}
	elseif ($ma[itemtype] == 2) {
		if ($itemsrow["attribute1"] != "X") {
	        $special1 = explode(",",$itemsrow["attribute1"]);
	        if ($special1[0] == "maxhp") { $attr1 = "Max HP"; }
	        elseif ($special1[0] == "maxmp") { $attr1 = "Max MP"; }
	        elseif ($special1[0] == "maxtp") { $attr1 = "Max TP"; }
			elseif ($special1[0] == "maxap") { $attr1 = "Max AP"; }
	        elseif ($special1[0] == "goldbonus") { $attr1 = "Gold Bonus (%)"; }
	        elseif ($special1[0] == "expbonus") { $attr1 = "Experience Bonus (%)"; }
	        elseif ($special1[0] == "strength") { $attr1 = "Strength"; }
	        elseif ($special1[0] == "dexterity") { $attr1 = "Dexterity"; }
	        elseif ($special1[0] == "attackpower") { $attr1 = "Attack"; }
	        elseif ($special1[0] == "defensepower") { $attr1 = "Defense"; }
	        else { $attr1 = $special1[0]; }
	        if ($special1[1] > 0) { $stat1 = "+" . $special1[1]; } else { $stat1 = $special1[1]; }
	        $bigspecial1 = "$attr1 $stat1";
	    } else { $bigspecial1 = "None"; }
	    if ($itemsrow["attribute2"] != "X") {
	        $special2 = explode(",",$itemsrow["attribute2"]);
	        if ($special2[0] == "maxhp") { $attr2 = "Max HP"; }
	        elseif ($special2[0] == "maxmp") { $attr2 = "Max MP"; }
	        elseif ($special2[0] == "maxtp") { $attr2 = "Max TP"; }
			elseif ($special2[0] == "maxap") { $attr2 = "Max AP"; }
	        elseif ($special2[0] == "goldbonus") { $attr2 = "Gold Bonus (%)"; }
	        elseif ($special2[0] == "expbonus") { $attr2 = "Experience Bonus (%)"; }
	        elseif ($special2[0] == "strength") { $attr2 = "Strength"; }
	        elseif ($special2[0] == "dexterity") { $attr2 = "Dexterity"; }
	        elseif ($special2[0] == "attackpower") { $attr2 = "Attack"; }
	        elseif ($special2[0] == "defensepower") { $attr2 = "Defense"; }
	        else { $attr2 = $special2[0]; }
	        if ($special2[1] > 0) { $stat2 = "+" . $special2[1]; } else { $stat2 = $special2[1]; }
	        $bigspecial2 = "$attr2 $stat2";
	    } else { $bigspecial2 = "None"; }

	    $page .= "<tr><td width='40%' style='background-color:#eeeeee;'>Item Name:</td><td width='60%' style='background-color:#ffffff;'>$itemsrow[name]</td></tr>
	    <tr><td width='40%' style='background-color:#eeeeee;'>Monster Level:</td><td width='60%' style='background-color:#ffffff;'>$itemsrow[mlevel]</td></tr>
	    <tr><td width='40%' style='background-color:#eeeeee;'>Attribute 1:</td><td width='60%' style='background-color:#ffffff;'>$bigspecial1</td></tr>
	    <tr><td width='40%' style='background-color:#eeeeee;'>Attribute 2:</td><td width='60%' style='background-color:#ffffff;'>$bigspecial2</td></tr>
	    <tr><td width='40%' style='background-color:#eeeeee;'>Level Requirement:</td><td width='60%' style='background-color:#ffffff;'>$itemsrow[requirement]</td></tr>";
	}
	elseif ($ma[itemtype] == 3) {
		if ($itemsrow["type"] == 1) { $image = "ring"; $power = "Magic Find"; } elseif ($itemsrow["type"] == 2) { $image = "amulet"; $power = "Magic Find"; }

		$page .= "<tr><td width='40%' style='background-color:#eeeeee;'>Item Name:</td><td width='60%' style='background-color:#ffffff;'>$itemsrow[name]</td></tr>
		<tr><td width='40%' style='background-color:#eeeeee;'>Magic Find:</td><td width='60%' style='background-color:#ffffff;'><img src=\"images/icon_$image.gif\" alt=\"$image\"> $itemsrow[attribute] $power</td></tr>
		<tr><td width='40%' style='background-color:#eeeeee;'>Level Requirement:</td><td width='60%' style='background-color:#ffffff;'>$itemsrow[requirement]</td></tr>";
	}

	$page .= "</table></td></tr></table><br /><br />";

	if ($userrow[id] != $ma[playerid]) {
		$page .= "[ <a href='index.php?do=buyfrommarket:$ma[pmid]'>Purchase Item</a> ]<br /><br />";
	}

	$page .= "$town_link  You may also return to the <a href='index.php?do=playermarket'>market home</a>.";

	display($page, "Player Market - Items Listing");
}

function find_inequality($market_row, $add_buy = 0, $add_sell = 0) {
	global $trends_history;

	//Find inequality...
	$his = explode(";", $market_row[history]);

	$buys = 0 + $add_buy;
	$sells = 0 + $add_sell;

	for ($i = 0; !empty($his[$i]); $i++) {
		$h = explode(":", $his[$i]);

		if ((strtotime($h[0]) + (86400 * $trends_history)) < strtotime(date("Y-m-d"))) {
			break;
		}

		$sells += $h[1];
		$buys += $h[2];
	}

	$total_buys = $buys + $market_row['var'];
	$total_sells = $sells + $market_row['var'];

	if ($total_buys >= $total_sells) {
		$inequality = ($total_buys / $total_sells) - 1;
		$sign = '+';
	}
	else {
		$inequality = ($total_sells / $total_buys) - 1;
		$sign = '-';
	}

	return "$sign:$inequality";
}

function find_buy($par, $sign, $inequality, $commission) {
	if ($sign == '-') {
		$buy_price = ($par / ($inequality + 1)) * (1 + ($commission / 100));
	}
	else {
		$buy_price = ($par * ($inequality + 1)) * (1 + ($commission / 100));
	}

	if ($buy_price == 0) {
		$buy_price = 1;
	}

	return $buy_price;
}

function find_sell($par, $sign, $inequality, $commission) {
	//Find sell/buy prices
	if ($sign == '-') {
		$sell_price = ($par / ($inequality + 1)) * (1 - ($commission / 100));
	}
	else {
		$sell_price = ($par * ($inequality + 1)) * (1 - ($commission / 100));
	}

	if ($sell_price == 0) {
		$sell_price = 1;
	}

	return $sell_price;
}

function craft_market() {
	//Get Globals
	global $userrow, $town_link, $commission;

	$page .= "<table width='100%' border='1'><tr><td class='title'>Player Market - Crafting Materials</td></tr></table><br />
	Welcome to the crafting materials section of the player market.  Here you can buy and sell bars, ores, and other crafting materials.
	The price to buy an item, and the amount you can sell them for vary by supply and demand.  See below for details.<br /><br />";

	//Get the market rows
	$market_query = doquery("SELECT * FROM {{table}} ORDER BY type, cid", "pmcraft");

	//Set the mem_cat
	$mem_cat = 0;

	//Set the BG color
	$bgcolor = "#ffffff";

	//Make the table
	while ($market_row = mysql_fetch_array($market_query)) {
		if ($market_row[type] != $mem_cat) {
			if ($market_row[type] == 1) {
				$page .= "<center><table width='95%'><tr><td style='padding:1px; background-color:black;'><table width='100%' style='margins:0px;' cellspacing='1' cellpadding='3'><tr><th colspan='6' style='background-color:#dddddd;'><center>Ores</center></th></tr>
				<tr><th width='20%' style='background-color:#dddddd;'>Item</th>
				<th width='20%' style='background-color:#dddddd;'>Par Value</th>
				<th width='20%' style='background-color:#dddddd;'>Inequality*</th>
				<th width='15%' style='background-color:#dddddd;'>Sell Price&dagger;</th>
				<th width='15%' style='background-color:#dddddd;'>Buy Price&dagger;</th>
				<th width='10%' style='background-color:#dddddd;'>Trade</th></tr>";
			}
			elseif ($market_row[type] == 2) {
				$page .= "</table></td></tr></table><br />
				<br />
				<table width='95%'><tr><td style='padding:1px; background-color:black;'><table width='100%' style='margins:0px;' cellspacing='1' cellpadding='3'><tr><th colspan='6' style='background-color:#dddddd;'><center>Bars</center></th></tr>
				<tr><th width='20%' style='background-color:#dddddd;'>Item</th>
				<th width='20%' style='background-color:#dddddd;'>Par Value</th>
				<th width='20%' style='background-color:#dddddd;'>Inequality*</th>
				<th width='15%' style='background-color:#dddddd;'>Sell Price&dagger;</th>
				<th width='15%' style='background-color:#dddddd;'>Buy Price&dagger;</th>
				<th width='10%' style='background-color:#dddddd;'>Trade</th></tr>";
			}
			elseif ($market_row[type] == 3) {
				$page .= "</table></td></tr></table><br />
				<br />
				<table width='95%'><tr><td style='padding:1px; background-color:black;'><table width='100%' style='margins:0px;' cellspacing='1' cellpadding='3'><tr><th colspan='6' style='background-color:#dddddd;'><center>Crafting Materials</center></th></tr>
				<tr><th width='20%' style='background-color:#dddddd;'>Item</th>
				<th width='20%' style='background-color:#dddddd;'>Par Value</th>
				<th width='20%' style='background-color:#dddddd;'>Inequality*</th>
				<th width='15%' style='background-color:#dddddd;'>Sell Price&dagger;</th>
				<th width='15%' style='background-color:#dddddd;'>Buy Price&dagger;</th>
				<th width='10%' style='background-color:#dddddd;'>Trade</th></tr>";
			}

			$mem_cat = $market_row[type];
		}

		//Find inequality...
		$inq = find_inequality($market_row);

		$iq = explode(":", $inq);
		$sign = $iq[0];
		$inequality = $iq[1];


		//Find sell/buy prices
		$buy_price = find_buy($market_row[par], $sign, $inequality, $commission);
		$sell_price = find_sell($market_row[par], $sign, $inequality, $commission);

		$page .= "<tr><td style='background-color:$bgcolor;'>$market_row[name]</td>
		<td style='background-color:$bgcolor;'>" . number_format($market_row[par]) . " gold</td>
		<td style='background-color:$bgcolor;'> $sign" . number_format($inequality, '5') . "</td>
		<td style='background-color:$bgcolor;'>" . number_format($sell_price) . " gold</td>
		<td style='background-color:$bgcolor;'>" . number_format($buy_price) . " gold</td>
		<td style='background-color:$bgcolor;'><a href='index.php?do=tradecraft:$market_row[cid]'>Trade</a></td></tr>";

		if ($bgcolor == "#ffffff")	{
			$bgcolor = "#eeeeee";
		}
		else {
			$bgcolor = "#ffffff";
		}
	}

	//Finish the page
	$page .= "</table></td></tr></table></center><br /><br />

	*A positive inequality indacates that more people have been buying that item than selling it; a negative inequality indicates that more are selling than buying.
	Therefore if the equality is positive the prices to buy and sell the item will be higher, while if it is a negative equality the price to buy and sell will be lower.
	It is best to buy when the equality is negative, and sell when the equality is positive.<br />
	<br />
	If an item has an equality of 0.00000 that means that an equal number of items have been bought and sold.  If this is the case the item can be purchased for its par value + $commission% commission, and sold for its par value - $commission% commission.<br />
	<br />
	&dagger;Buy and Sell values are only valid if you are buying or selling <b>one</b> of the specific item.  Since if you are buying more than one the inequality value you will change, you will have to get a quote for buying mulitples of more than one item.
	To get a quote click the trade button beside the item you wish to buy/sell, enter the quanity you wish to trade, and click the quote button.<br />
	<br />
	<u>How prices are calculated</u><br />
	If an inequality is positive then the price of the item to buy is (1 + the inequality) times the par value, plus $commission% commission, while to sell it is (1 + the inequality) times the par value, minus $commission% commission.
	If an inequality is positive then the price of the item to buy is the par value divided by (1 + the inequality), plus $commission% commission, while to sell it is the par value divided by (1 + the inequality), minus $commission% commission.
	The inequalites are based on the number of buys and sells in the past $trends_history days.<br />
	<br />";

	$page .= $town_link . " You may also return to the <a href='index.php?do=playermarket'>market home</a>.";

	//Display Page
	display($page, "Player Market - Crafting Items");
}

function trade_craft($cid) {
	//Globals
	global $userrow, $commission, $town_link;

	//Fatal Error
	if((!empty($_POST['quote']) && !empty($_GET['buy'])) || (!empty($_POST['quote']) && !empty($_GET['sell'])) || (!empty($_POST['sell']) && !empty($_GET['buy']))) {
		header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "index.php?do=playermarket");
		exit;
	}

	//Get the market row
	$market_query = doquery("SELECT * FROM {{table}} WHERE cid = $cid ORDER BY type, cid", "pmcraft");
	$market_row = mysql_fetch_array($market_query);

	if ($market_row[type] == 1) {
		$type = " Ore";
	}
	elseif ($market_row[type] == 2) {
		$type = " Bars";
	}
	elseif ($market_row[type] == 3) {
		$type = "";
	}

	//Find Base Buy/Sell prices
	//Find inequality...
	$inq = find_inequality($market_row);

	$iq = explode(":", $inq);
	$sign = $iq[0];
	$inequality = $iq[1];

	//Find sell/buy prices
	$buy_price = find_buy($market_row[par], $sign, $inequality, $commission);
	$sell_price = find_sell($market_row[par], $sign, $inequality, $commission);

	//Find 10 Buy/Sell prices
	//Find inequality...
	$inqb10 = find_inequality($market_row, 10, 0);
	$inqs10 = find_inequality($market_row, 0, 10);

	$iq = explode(":", $inqb10);
	$sign10b = $iq[0];
	$inequalityb10 = $iq[1];

	$iq = explode(":", $inqs10);
	$sign10s = $iq[0];
	$inequalitys10 = $iq[1];

	//Find sell/buy prices
	$buy_price10 = find_buy($market_row[par], $sign10b, $inequalityb10, $commission);
	$sell_price10 = find_sell($market_row[par], $sign10s, $inequalitys10, $commission);

	//Find 100 Buy/Sell prices
	//Find inequality...
	$inqb100 = find_inequality($market_row, 100, 0);
	$inqs100 = find_inequality($market_row, 0, 100);

	$iq = explode(":", $inqb100);
	$sign100b = $iq[0];
	$inequalityb100 = $iq[1];

	$iq = explode(":", $inqs100);
	$sign100s = $iq[0];
	$inequalitys100 = $iq[1];

	//Find sell/buy prices
	$buy_price100 = find_buy($market_row[par], $sign100b, $inequalityb100, $commission);
	$sell_price100 = find_sell($market_row[par], $sign100s, $inequalitys100, $commission);

	//Find x Buy/Sell prices
	if (!empty($_POST['quote']) || !empty($_GET['buy']) || !empty($_GET['sell'])) {
		if (!empty($_POST['quote'])) {
			$qte = $_POST['quote'];
		}
		elseif (!empty($_GET['buy'])) {
			$qte = $_GET['buy'];
		}
		elseif (!empty($_GET['sell'])) {
			$qte = $_GET['sell'];
		}

		//Find inequality...
		$inqbq = find_inequality($market_row, $qte, 0);
		$inqsq = find_inequality($market_row, 0, $qte);

		$iq = explode(":", $inqbq);
		$signqb = $iq[0];
		$inequalitybq = $iq[1];

		$iq = explode(":", $inqsq);
		$signqs = $iq[0];
		$inequalitysq = $iq[1];

		//Find sell/buy prices
		$buy_priceq = find_buy($market_row[par], $signqb, $inequalitybq, $commission);
		$sell_priceq = find_sell($market_row[par], $signqs, $inequalitysq, $commission);
	}

	$bb = $buy_price;
	$sb = $sell_price;

	$b10 = ($buy_price + $buy_price10) / 2;
	$s10 = ($sell_price + $sell_price10) / 2;
	$b10t = $b10 * 10;
	$s10t = $s10 * 10;

	$b100 = ($buy_price + $buy_price100) / 2;
	$s100 = ($sell_price + $sell_price100) / 2;
	$b100t = $b100 * 100;
	$s100t = $s100 * 100;

	if ($qte != 1) {
		$bq = ($buy_price + $buy_priceq) / 2;
	}
	else {
		$bq = $buy_price;
	}
	if ($qte!= 1) {
		$sq = ($sell_price + $sell_priceq) / 2;
	}
	else {
		$sq = $sell_price;
	}
	$bqt = $bq * $qte;
	$sqt = $sq * $qte;


	//Traded
	if (!empty($_GET[did]) && !empty($_GET[num])) {
		//Make Page
		$page .= "<table width='100%' border='1'><tr><td class='title'>Player Market - $market_row[name]$type Traded</td></tr></table><br />
		You have $_GET[did] $_GET[num] $market_row[name]$type. <br />
		<br />";

		$page .= $town_link . " You may also return to the <a href='index.php?do=playermarket'>market home</a>.";

		display($page, "Player Market - $market_row[name]$type Traded");
	}

	//Get quote
	if (empty($_GET['buy']) && empty($_GET['sell'])) {
		//Find out what the user can afford
		if ($userrow[gold] >= $bb) {
			$bbl = "[ <a href='index.php?do=tradecraft:$cid&amp;buy=1'>Buy</a> ]";
		}
		if ($userrow[gold] >= $b10t) {
			$b10l = "[ <a href='index.php?do=tradecraft:$cid&amp;buy=10'>Buy</a> ]";
		}
		if ($userrow[gold] >= $b100t) {
			$b100l = "[ <a href='index.php?do=tradecraft:$cid&amp;buy=100'>Buy</a> ]";
		}
		if($userrow[gold] >= $bqt) {
			$bql = "[ <a href='index.php?do=tradecraft:$cid&amp;buy=$_POST[quote]'>Buy</a> ]";
		}
		$craft = $market_row[userlink];
		if($userrow[$craft] >= 1) {
			$sbl = "[ <a href='index.php?do=tradecraft:$cid&amp;sell=1'>Sell</a> ]";
		}
		if($userrow[$craft] >= 10) {
			$s10l = "[ <a href='index.php?do=tradecraft:$cid&amp;sell=10'>Sell</a> ]";
		}
		if($userrow[$craft] >= 100) {
			$s100l = "[ <a href='index.php?do=tradecraft:$cid&amp;sell=100'>Sell</a> ]";
		}
		if($userrow[$craft] >= $_POST[quote]) {
			$sql = "[ <a href='index.php?do=tradecraft:$cid&amp;sell=$_POST[quote]'>Sell</a> ]";
		}


		//Start the page
		$page .= "<table width='100%' border='1'><tr><td class='title'>Player Market - Trade $market_row[name]$type</td></tr></table><br />
		If you would like to buy and/or sell $market_row[name]$type, you can do so below.  Please note that the prices you can by and sell this item for are subject to change, and change frequently!<br />
		<br />
		<form action='index.php?do=tradecraft:$cid' method='post'>
		<center>
		<table width='95%'><tr><td style='padding:1px; background-color:black;'><table width='100%' style='margins:0px;' cellspacing='1' cellpadding='3'>
		<tr><th width='50%' style='background-color:#dddddd;'>Buy</th><th width='50%' style='background-color:#dddddd;'>Sell</th></tr>

		<tr><td style='background-color:#ffffff;'>Buy <b>1</b> $market_row[name]$type for <b>" . number_format($bb) . "</b> gold. <br />
		$bbl</td>
		<td style='background-color:#ffffff;'>Sell <b>1</b> $market_row[name]$type for <b>" . number_format($sb) . "</b> gold. <br />
		$sbl</td></tr>

		<tr><td style='background-color:#eeeeee;'>Buy <b>10</b> $market_row[name]$type for <b>" . number_format($b10t) . "</b> gold. (<b>" . number_format($b10) . "</b> gold each)<br />
		$b10l</td>
		<td style='background-color:#eeeeee;'>Sell <b>10</b> $market_row[name]$type for <b>" . number_format($s10t) . "</b> gold. (<b>" . number_format($s10) . "</b> gold each) <br />
		$s10l</td></tr>

		<tr><td style='background-color:#ffffff;'>Buy <b>100</b> $market_row[name]$type for <b>" . number_format($b100t) . "</b> gold. (<b>" . number_format($b100) . "</b> gold each)<br />
		$b100l</td>
		<td style='background-color:#ffffff;'>Sell <b>100</b> $market_row[name]$type for <b>" . number_format($s100t) . "</b> gold. (<b>" . number_format($s100) . "</b> gold each)<br />
		$s100l</td></tr>";

		if (!empty($_POST[quote])) {
			$page .= "<tr><td style='background-color:#eeeeee;'>Buy <b>$_POST[quote]</b> $market_row[name]$type for <b>" . number_format($bqt) . "</b> gold. (<b>" . number_format($bq) . "</b> gold each) <br />
			$bql</td>
			<td style='background-color:#eeeeee;'>Sell <b>$_POST[quote]</b> $market_row[name]$type for <b>" . number_format($sqt) . "</b> gold. (<b>" . number_format($sq) . "</b> gold each) <br />
			$sql</td></tr>";

			$bg = "ffffff";
		}
		else {
			$bg = "eeeeee";
		}

		$page .= "<tr><td colspan='2' style='background-color:#$bg;'>Get a quote for buying/selling <input type='text' size='4' maxlength='4' name='quote' value='1' /> $market_row[name]$type.
		<input type='submit' value='Get Quote!' /></td></tr>
		</table></td></tr></table></center><br />
		<br />
		</form>";

		$page .= $town_link . " You may also return to the <a href='index.php?do=playermarket'>market home</a>.";

		display($page, "Player Market - Trade $market_row[name]$type");

	}

	//Buying
	elseif (!empty($_GET[buy]) || !empty($_GET[sell])) {
		//Get Craft
		$craft = $market_row[userlink];

		//Buy
		if (!empty($_GET['buy'])) {
			//Cheat Guard
			if ($userrow[gold] < $bqt) {
				header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "index.php?do=playermarket");
				exit;
			}

			$new_gold = $userrow[gold] - $bqt;
			$new_craft = $userrow[$craft] + $_GET[buy];
			$did = "bought";
			$num = $_GET[buy];
		}

		//Sell
		if (!empty($_GET['sell'])) {
			//Cheat Guard
			if ($userrow[$craft] < $_GET['sell']) {
				header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "index.php?do=playermarket");
				exit;
			}

			$new_gold = $userrow[gold] + $bqt;
			$new_craft = $userrow[$craft] - $_GET[sell];
			$did = "sold";
			$num = $_GET[sell];
		}

		//Transaction
		$trans = doquery("UPDATE {{table}} SET gold = '$new_gold', $craft = '$new_craft' WHERE id = '$userrow[id]'", "users");

		//History
		$his = explode(";", $market_row[history]);
		$h = explode(":", $his[0]);

		if ($h[0] == date("Y-m-d")) {
			//Same day
			if (!empty($_GET[sell])) {
				$h[1] = $h[1] + $_GET[sell];
			}
			else {
				$h[2] = $h[2] + $_GET[buy];
			}

			$his[0] = implode(":", $h);

			$new_history = implode(";", $his);
		}
		else {
			//New day
			$a = date("Y-m-d");

			if (!empty($_GET[sell])) {
				$b = $_GET[sell];
				$c = 0;
			}
			else {
				$c =  $_GET[buy];
				$b = 0;
			}

			$now = "$a:$b:$c";

			$new_history = $now . ';' . $market_row[history];
		}

		$history_update = doquery("UPDATE {{table}} SET history = '$new_history' WHERE cid = '$cid'", "pmcraft");

		header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "index.php?do=tradecraft:$cid&did=$did&num=$num");
		exit;
	}
}

/*

#######################################################
# CODED BY MARK SAUVERWALD FOR ADAM DEAR & DK-RPG.COM #
# E-mail: namadoor@gmail.com                          #
# Web Development by Auroria                          #
#######################################################

*/

?>