<?php // towns.php :: Handles all actions you can do in town.
include('bonus.php');
function dobonus(){
		global $userrow, $bonus;

        $query = doquery("SELECT UNIX_TIMESTAMP(bonusTime),gold,dscales from {{table}} WHERE id='".$userrow["id"]."' LIMIT 1", "users");
		$data= mysql_fetch_array($query);
		$lastTime=$data[0];
		$gold=$data[1];
		$dscales=$data[2];
        $presentTime=time();


		if(($presentTime-$lastTime)>$bonus["time"]){

           $query = doquery("UPDATE {{table}} SET gold='".($gold+$bonus["gold"])."', dscales='".($dscales+$bonus["dscales"])."' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

		   $test = doquery("UPDATE {{table}} SET bonusTime=NOW() WHERE id='".$userrow["id"]."' LIMIT 1","users");

		   header("Location: index.php?do=daily");
		}else{
		   header("Location: index.php?do=collected");
		}



}

function inn() { // Staying at the inn resets all expendable stats to their max values.

    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

    if ($userrow["gold"] < $townrow["innprice"]) { display("You do not have enough gold to stay at this Inn tonight.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass to the right to start exploring.", "Inn"); die(); }

    if (isset($_POST["submit"])) {

        $newgold = $userrow["gold"] - $townrow["innprice"];
        $query = doquery("UPDATE {{table}} SET gold='$newgold', drink='Empty', potion='Empty', currenthp='".$userrow["maxhp"]."',currentmp='".$userrow["maxmp"]."',currenttp='".$userrow["maxtp"]."' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Inn - Refreshed";
        $page = "<table width='100%' border='1'><tr><td class='title'>Inn - Refreshed</td></tr></table><p>";
        $page .= "You feel refreshed and ready for another day, with all current stats to maximum capacity.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.";

    } elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

    } else {

$updatequery = doquery("UPDATE {{table}} SET location='Resting at the Inn' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
 

        $title = "Inn";
        $page = "<table width='100%' border='1'><tr><td class='title'>Inn</td></tr></table><p>";
        $page .= "Herak: Resting at the inn will refill your current HP, MP, and TP to their maximum levels. It will also remove your current Tavern Drink and Potion if you have recently purchased one.<br /><br />\n";
        $page .= "A night's sleep at this Inn will cost you <b>" . $townrow["innprice"] . " gold</b>. If you can't afford this amount then I heard a rumour about a Wealthy Gambler in the Local Tavern who is very generous.<p>Is that ok?<br /><br />\n";
        $page .= "<form action=\"index.php?do=inn\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes\" /> <input type=\"submit\" name=\"cancel\" value=\"No\" />\n";
        $page .= "</form>\n";

    }

    display($page, $title);

}
  
function buy() { // Displays a list of available items for purchase.

    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,itemslist FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

    $townquery = doquery("SELECT name,itemslist FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    $townrow = mysql_fetch_array($townquery);

    $itemslist = explode(",",$townrow["itemslist"]);
    $querystring = "";
    foreach($itemslist as $a=>$b) {
        $querystring .= "id='$b' OR ";
    }
    $querystring = rtrim($querystring, " OR ");

    $itemsquery = doquery("SELECT * FROM {{table}} WHERE $querystring ORDER BY id", "items");
        $page = "<table width='100%' border='1'><tr><td class='title'>Town Blacksmith</td></tr></table><p>";
    $page .= "Buying weapons and gauntlets will increase your Attack Power. Buying armor, leg armor, helms and shields will increase your Defense Power. Each item may require you to be of a specific level, to be able to equip them.<br /><br />Click an item name to purchase it.<br /><br />You may also sell me your <a href=\"index.php?do=backpack\">forged items</a>, <A href='sell.php?do=sellores'>Ores</a> or <A href='sell.php?do=sellbars'>Bars</a>, which I then use to create my Items.<p>\n";
    $page .= "<p>Your Equipped Items:          <table width='100%'>
                                  <tr>
              <td><img src='images/icon_helm.gif' alt='Helm' title='Helm' /></td>
              <td><b>Helm:</b> ".$userrow["helmname"]."</td>
            </tr>
            <tr>
              <td><img src='images/icon_weapon.gif' alt='Weapon' title='Weapon' /></td>
              <td width='100%'><b>Weapon:</b> ".$userrow["weaponname"]."</td>
            </tr>
            <tr>
              <td><img src='images/icon_armor.gif' alt='Armor' title='Armor' /></td>
              <td><b>Armor:</b> ".$userrow["armorname"]."</td>
            </tr>
                        <tr>
              <td><img src='images/icon_legarmor.gif' alt='Leg Armor' title='Leg Armor' /></td>
              <td><b>Leg Armor:</b> ".$userrow["legsname"]."</td>
            </tr>
            <tr>
              <td><img src='images/icon_shield.gif' alt='Shield' title='Shield' /></td>
              <td><b>Shield:</b> ".$userrow["shieldname"]."</td>
            </tr>
             <tr>
              <td><img src='images/icon_gauntlet.gif' alt='Gauntlets' title='Gauntlets' /></td>
              <td><b>Gauntlets:</b> ".$userrow["gauntletsname"]."</td>
            </tr>
    
    </table><p>The following items are available at this town:<p><table width=\"99%\">\n";
    while ($itemsrow = mysql_fetch_array($itemsquery)) {
        if ($itemsrow["type"] == 1 || $itemsrow["type"] == 6) { $attrib = "Attack Power:"; } else  { $attrib = "Defense Power:"; }
        $page .= "<tr><td width=\"4%\">";
        if ($itemsrow["type"] == 1) { $page .= "<img src=\"images/icon_weapon.gif\" alt=\"weapon\" /></td>"; }
        if ($itemsrow["type"] == 2) { $page .= "<img src=\"images/icon_armor.gif\" alt=\"armor\" /></td>"; }
        if ($itemsrow["type"] == 3) { $page .= "<img src=\"images/icon_shield.gif\" alt=\"shield\" /></td>"; }
        if ($itemsrow["type"] == 4) { $page .= "<img src=\"images/icon_helm.gif\" alt=\"helm\" /></td>"; }
        if ($itemsrow["type"] == 5) { $page .= "<img src=\"images/icon_legarmor.gif\" alt=\"legarmor\" /></td>"; }    
        if ($itemsrow["type"] == 6) { $page .= "<img src=\"images/icon_gauntlet.gif\" alt=\"gauntlet\" /></td>"; }             
        if ($itemsrow["requirement"] > $userrow["level"]) {
            $page .= "<td width=\"25%\"><span class=\"light\">".$itemsrow["name"]."</span></td><td width=\"25%\"><span class=\"light\">$attrib ".$itemsrow["attribute"]."</span></td><td width=\"25%\"><span class=\"light\">Requirement: Level ".$itemsrow["requirement"]."</span></td><td width=\"40%\"><span class=\"light\">Can't Buy</span></td></tr>\n";
        
        
        } else {
            if ($itemsrow["special"] != "X") { $specialdot = "<span class=\"highlight\">&#42;</span>"; } else { $specialdot = ""; }
            $page .= "<td width=\"25%\"><b><a href=\"index.php?do=buy2:".$itemsrow["id"]."\">".$itemsrow["name"]."</a>$specialdot</b></td><td width=\"25%\">$attrib <b>".$itemsrow["attribute"]."</b></td><td width=\"25%\">Requirement: <b>Level ".$itemsrow["requirement"]."</b></td><td width=\"40%\">Price: <b>".$itemsrow["buycost"]." gold</b></td></tr>\n";
        }
    }
    $page .= "</table><br />\n";
    $page .= "*These are items that come with special attributes that modify other parts of your character profile. More information about these are found in the help guide, under the section of items and item drops.\n";
    $page .= "<p>If you've changed your mind, you may also return back to <a href=\"index.php\">town</a>.\n";
    $title = "Town Blacksmith";

$updatequery = doquery("UPDATE {{table}} SET location='Town Blacksmith' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
 

    display($page, $title);

}

//buy2
/**
 * @desc Confirm user's intent to purchase item.
 * @return void
 * @param int $id
 */
function buy2($id) {
	global $userrow, $numqueries, $backpackitemslots;

	$townquery = doquery("SELECT name,itemslist FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
	if (mysql_num_rows($townquery) != 1)	{
		display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error");
	}
	
	$townrow = mysql_fetch_array($townquery);
	$townitems = explode(",",$townrow["itemslist"]);
  	if (! in_array($id, $townitems))	{
  		display("Cheat attempt sent to administrator.", "Error"); 
  	}

	$itemsquery = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "items");
	$itemsrow = mysql_fetch_array($itemsquery);

	if ($userrow["gold"] < $itemsrow["buycost"])	{
		display("You do not have enough gold to buy this item.<br /><br />You may return to <a href=\"index.php\">town</a>, <a href=\"index.php?do=buy\">store</a>, or use the direction compass on the right to start exploring.", "Town Blacksmith");
		exit;
	}
	
	if ($userrow["level"] < $itemsrow["requirement"])	{
		display("You do not meet the level requirement to buy this item.<br /><br />You may return to <a href=\"index.php\">town</a>, <a href=\"index.php?do=buy\">store</a>, or use the direction compass on the right to start exploring.", "Town Blacksmith");
		exit;
	}
	
	$bpquery = doquery("SELECT * FROM {{table}} WHERE playerid = '$userrow[id]' AND itemtype = '1' AND location = '1'", "itemstorage");
	$slotsr = $backpackitemslots - mysql_num_rows($bpquery);
	
	if ($slotsr < 1)	{
		display("You do not have the room in your backpack to store this item.  Please go to your <a href='index.php?do=backpack'>backpack</a> to clear out some room and then return to me.", "Town Blacksmith");
	}

	$page = "<table width='100%' border='1'><tr><td class='title'>Town Blacksmith - Buy Item</td></tr></table><p>";
	$page .= "You have chosen to buy the $itemsrow[name] for $itemsrow[buycost] gold. Are you sure you wish to buy this item?<br /><br /><form action=\"index.php?do=buy3:$id\" method=\"post\"><input type=\"submit\" name=\"submit\" value=\"Yes\" /> <input type=\"submit\" name=\"cancel\" value=\"No\" /></form>";

	$title = "Town Blacksmith";
	display($page, $title);
}

//buy3
/**
 * @desc Purcahses the item.
 * @return void
 * @param int $id
 */
function buy3($id) {
	global $userrow;

	if (isset($_POST["cancel"]))	{
		header("Location: index.php");
		exit;
	}
	
	require('storage.php');
	
	//Cheat catcher
	if ($userrow["gold"] < $itemsrow["buycost"])	{
		display("Cheat attempt sent to administrator.", "Error"); 
		exit;
	}
	
	
	//Pay for the item
	$result = doquery("SELECT buycost FROM {{table}} WHERE id = $id", "items");
	$ma = mysql_fetch_array($result);
	
	//Cheat catcher
	if ($userrow["gold"] < $ma["buycost"])	{
		display("Cheat attempt sent to administrator.", "Error"); 
		exit;
	}
	

	doquery("UPDATE {{table}} SET gold = gold - $ma[buycost] WHERE id = '$userrow[id]'", "users");

	//Add the item to storage
	additem($id, 1, 1);
	
	//Select the storageid.
	$query = "SELECT * FROM {{table}}
		  WHERE playerid = '$userrow[id]'
		  AND itemid = '$id' ";
	$result = doquery($query, "itemstorage");
	
	$ma = mysql_fetch_array($result);
	$isid = $ma['isid'];
	
//	die(":::$ma[isid]:::$isid");
	
	display("Thank you for purchasing this item.  You may now either <a href='index.php?do=buy4:$isid&amp;equip=1&amp;where=1'>equip</a> the item, or put it in your <a href='index.php?do=buy4:$isid'>backpack</a>.", "Town Blacksmith");
}

//buy4
/**
 * @desc Equip the item (if needed)
 * @return void
 * @param int $id
 */
function buy4($id) {
	if ($_GET[equip] == 1)	{
		require('storage.php');
		equipstoreditem($id, 1, 0);
		$page = "The item has been equiped.  If you already had an item equiped it has been placed in your <a href='index.php?do=backpack'>backpack</a>.";
	}
	else {
		$page = "You put the item in your <a href='index.php?do=backpack'>backpack</a>.";
	}
	
	display($page. "<br /><br />You may return to <a href=\"index.php\">town</a>, <a href=\"index.php?do=buy\">store</a>, or use the direction compass on the right to start exploring.", "Town Blacksmith");
}

function maps() { // List maps the user can buy.

    global $userrow, $numqueries;
    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

    $mappedtowns = explode(",",$userrow["towns"]);
    $page = "<table width='100%' border='1'><tr><td class='title'>Travel Store</td></tr></table><p>";
    $page .= "Buying maps will put the town in your Travel To box, and it won't cost you as many TP to get there. By clicking the map in the future, it will automatically travel you to your desired destination.<p>If you walk to your desired location, you automatically get the map for free.<br /><br />\n";
    $page .= "Click a town name to purchase its map.<br /><br />\n";
    $page .= "<table width=\"90%\">\n";

    $townquery = doquery("SELECT * FROM {{table}} ORDER BY id", "towns");
    while ($townrow = mysql_fetch_array($townquery)) {

        if ($townrow["latitude"] >= 0) { $latitude = $townrow["latitude"] . "N,"; } else { $latitude = ($townrow["latitude"]*-1) . "S,"; }
        if ($townrow["longitude"] >= 0) { $longitude = $townrow["longitude"] . "E"; } else { $longitude = ($townrow["longitude"]*-1) . "W"; }

        $mapped = false;
        foreach($mappedtowns as $a => $b) {
            if ($b == $townrow["id"]) { $mapped = true; }
        }
        if ($mapped == false) {
            $page .= "<tr><td width=\"25%\"><a href=\"index.php?do=maps2:".$townrow["id"]."\">".$townrow["name"]."</a></td><td width=\"25%\">Price: ".$townrow["mapprice"]." gold</td><td width=\"50%\" colspan=\"2\">Buy map to reveal details.</td></tr>\n";
        } else {
            $page .= "<tr><td width=\"25%\"><span class=\"light\">".$townrow["name"]."</span></td><td width=\"25%\"><span class=\"light\">Already mapped.</span></td><td width=\"35%\"><span class=\"light\">Location: $latitude $longitude</span></td><td width=\"15%\"><span class=\"light\">TP: ".$townrow["travelpoints"]."</span></td></tr>\n";
        }

    }

    $page .= "</table><br />\n";
    $page .= "If you've changed your mind, you may also return back to <a href=\"index.php\">town</a>.\n";
$updatequery = doquery("UPDATE {{table}} SET location='Travel Store' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
 

    display($page, "Travel Store");

}

function maps2($id) { // Confirm user's intent to purchase map.

    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

    $townquery = doquery("SELECT name,mapprice FROM {{table}} WHERE id='$id' LIMIT 1", "towns");
    $townrow = mysql_fetch_array($townquery);

    if ($userrow["gold"] < $townrow["mapprice"]) { display("You do not have enough gold to buy this map.<br /><br />You may return to <a href=\"index.php\">town</a>, <a href=\"index.php?do=maps\">store</a>, or use the direction compass on the right to start exploring.", "Travel Store"); die(); }
            $page = "<table width='100%' border='1'><tr><td class='title'>Travel Store - Buy Map</td></tr></table><p>";
    $page .= "You have chosen to buy the map for ".$townrow["name"]." . Are you sure you wish to purchase this map?<br /><br /><form action=\"index.php?do=maps3:$id\" method=\"post\"><input type=\"submit\" name=\"submit\" value=\"Yes\" /> <input type=\"submit\" name=\"cancel\" value=\"No\" /></form>";

    display($page, "Travel Store");

}

function maps3($id) { // Add new map to user's profile.

    if (isset($_POST["cancel"])) { header("Location: index.php"); die(); }

    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

    $townquery = doquery("SELECT name,mapprice FROM {{table}} WHERE id='$id' LIMIT 1", "towns");
    $townrow = mysql_fetch_array($townquery);

    if ($userrow["gold"] < $townrow["mapprice"]) { display("You do not have enough gold to buy this map.<br /><br />You may return to <a href=\"index.php\">town</a>, <a href=\"index.php?do=maps\">store</a>, or use the direction compass on the right to start exploring.", "Travel Store"); die(); }

    $mappedtowns = $userrow["towns"].",$id";
    $newgold = $userrow["gold"] - $townrow["mapprice"];

    $updatequery = doquery("UPDATE {{table}} SET towns='$mappedtowns',gold='$newgold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

    display("Thank you for purchasing this map.<br /><br />You may return to <a href=\"index.php\">town</a>, <a href=\"index.php?do=maps\">store</a>, or use the direction compass on the right to start exploring.", "Travel Store");

}

function homeportal() {
	global $userrow;
   if ($userrow["home"] == "No") {header("Location: index.php"); die(); }
	if (isset($_POST["totown"])) {	
		
    $townquery = doquery("SELECT * FROM {{table}} WHERE charname='".$userrow["charname"]."' LIMIT 1", "homes");
    $townrow = mysql_fetch_array($townquery);
    
    $newlat = $townrow["latitude"];
    $newlon = $townrow["longitude"];
    $newtown = $townrow["id"];
    $newid = $userrow["id"];
    $charname = $townrow["charname"];

	 $updatequery = doquery("UPDATE {{table}} SET currentaction='Home',location='At Home',latitude='$newlat',longitude='$newlon' WHERE id='$newid' LIMIT 1", "users");
     header("Location: home.php"); die();
	}
	
	$townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

	$page = "<table width='100%'><tr><td class='title'>Home Portal</td></tr></table>";
	$page .= "<p>You stand before a small portal which leads back to your Town Portal at your Home for free.<p> ";
	$page .= "<form action='index.php?do=homeportal' method='POST'>";
	$page .= "<input type='submit' name='totown' value='Enter Portal'>";
	$page .= "</form><p>";
	$page .= "You may return to <a href='index.php'>Town</a>, ";
	$page .= "or leave and <a href='index.php?do=move:0'>continue exploring</a>.";
	$pquery = doquery("UPDATE {{table}} SET location='Home Portal' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	display($page, "Home Portal");
}

function travelto($id, $usepoints=true) { // Send a user to a town from the Travel To menu.

    global $userrow, $numqueries;

    if ($userrow["currentaction"] == "Fighting") { header("Location: index.php?do=fight"); die(); }

    $townquery = doquery("SELECT name,travelpoints,latitude,longitude FROM {{table}} WHERE id='$id' LIMIT 1", "towns");
    $townrow = mysql_fetch_array($townquery);
    
    if ($usepoints==true) {
        if ($userrow["currenttp"] < $townrow["travelpoints"]) {
            display("You do not have enough TP to travel here. Please go <a href=\"index.php\">back</a> and try again when you get more TP.", "Travel To"); die();
        }
        $mapped = explode(",",$userrow["towns"]);
        if (!in_array($id, $mapped)) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    }

    if (($userrow["latitude"] == $townrow["latitude"]) && ($userrow["longitude"] == $townrow["longitude"])) { display("You are already in this town. <a href=\"index.php\">Click here</a> to return to the main town screen.", "Outside Town"); die(); }

    if ($usepoints == true) { $newtp = $userrow["currenttp"] - $townrow["travelpoints"]; } else { $newtp = $userrow["currenttp"]; }

    $newlat = $townrow["latitude"];
    $newlon = $townrow["longitude"];
    $newid = $userrow["id"];

    // If they got here by exploring, add this town to their map.
    $mapped = explode(",",$userrow["towns"]);
    $town = false;
    foreach($mapped as $a => $b) {
        if ($b == $id) { $town = true; }
    }
    $mapped = implode(",",$mapped);
    if ($town == false) {
        $mapped .= ",$id";
        $mapped = "towns='".$mapped."',";
    } else {
        $mapped = "towns='".$mapped."',";
    }

    $updatequery = doquery("UPDATE {{table}} SET location='Outside Town',currentaction='In Town',$mapped currenttp='$newtp',latitude='$newlat',longitude='$newlon' WHERE id='$newid' LIMIT 1", "users");
    $page .= "You have travelled to ".$townrow["name"].". If this is your first visit here without purchasing the town map, you receive the map for free.<p>You may now <a href=\"index.php\">enter this town</a>.<p><p><center><a href=\"index.php\"><img src=\"images/entertown.jpg\" border=\"0\" alt=\"You may enter Town\" /></a></center>";

if($userrow["tempquest"] == "2") { //Check to see if player needs to find rare herb for Lucas quest 2

        $page = "<table width='100%' border='1'><tr><td class='title'>Outside Town</td></tr></table><p>";
    $page .= "You see a small object lying in the bush beside town. You look closer and discover that its a Herb. This could be the Rare Potent Herb that Lucas needs.<p><font color=green>You found the Rare Potent Herb which Lucas needs for his Antidote Potion</font><P>You have travelled to ".$townrow["name"].". If this is your first visit here without purchasing the town map, you receive the map for free.<p>You may now <a href=\"index.php\">enter this town</a> or you may also visit the following Out of Town areas:<p><ul><li /><a href=\"castle.php?do=gate\">".$townrow["name"]." Castle</a><li /><a href=\"castle.php?do=lake\">Castle Lake</a><li /><a href=\"skills.php?do=mining\">Mining Field</a><li /><a href=\"skills.php?do=endurance\">Endurance Courses</a></ul><p><p><center><a href=\"index.php\"><img src=\"images/entertown.jpg\" border=\"0\" alt=\"You may enter Town\" /></a></center>";
 $inventitemsquery = doquery("SELECT id FROM {{table}}","inventitems");
        $userinventitems = explode(",",$userrow["inventitems"]);
        //add Quest 1 Item
        array_push($userinventitems, 91);
        $new_userinventitems = implode(",",$userinventitems);
        $userid = $userrow["id"];
        doquery("UPDATE {{table}} SET inventitems='$new_userinventitems',tempquest='herb',quest2='Half Complete' WHERE id='$userid' LIMIT 1", "users");

}

elseif($userrow["tempquest"] == "flower" && $townrow["name"] == "Abandoned Ruins") { //Check to see if player needs to find the flower for Quest 4

        $page = "<table width='100%' border='1'><tr><td class='title'>Outside Town</td></tr></table><p>";
    $page .= "You notice a small log in the distance, which takes you over a small bridge, to a deserted patch of land. You then see a few flowers in the distance, waving in the wind.<p>Do you wish to cross this <a href=\"index.php?do=logbridge\">log</a>? You will require level 15 in Endurance.<P>You have travelled to ".$townrow["name"].". If this is your first visit here without purchasing the town map, you receive the map for free.<p>You may now <a href=\"index.php\">enter this town</a> or you may also visit the following Out of Town areas:<p><ul><li /><a href=\"castle.php?do=gate\">".$townrow["name"]." Castle</a><li /><a href=\"castle.php?do=lake\">Castle Lake</a><li /><a href=\"skills.php?do=mining\">Mining Field</a><li /><a href=\"skills.php?do=endurance\">Endurance Courses</a></ul><p><p><center><a href=\"index.php\"><img src=\"images/entertown.jpg\" border=\"0\" alt=\"You may enter Town\" /></a></center>";

}
else {
        $page = "<table width='100%' border='1'><tr><td class='title'>Outside Town</td></tr></table><p>";
    $page .= "<p>You have travelled to ".$townrow["name"].". If this is your first visit here without purchasing the town map, you receive the map for free.<p>You may now <a href=\"index.php\">enter this town</a> or you may also visit the following Out of Town areas:<p><ul><li /><a href=\"castle.php?do=gate\">".$townrow["name"]." Castle</a><li /><a href=\"castle.php?do=lake\">Castle Lake</a><li /><a href=\"skills.php?do=mining\">Mining Field</a><li /><a href=\"skills.php?do=endurance\">Endurance Courses</a></ul><p><p><center><a href=\"index.php\"><img src=\"images/entertown.jpg\" border=\"0\" alt=\"You may enter Town\" /></a></center>";
 }
    display($page, "Outside Town");

}

function castletravelto($id, $usepoints=true) { // Send a user to a town from the Travel To menu.

    global $userrow, $numqueries;

    $townquery = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "strongholds");
    $townrow = mysql_fetch_array($townquery);

    $newlat = $townrow["latitude"];
    $newlon = $townrow["longitude"];
    $newtown = $townrow["id"];
    $newid = $userrow["id"];
    $guildname = $townrow["guildname"];
    if ($userrow["guildname"] == $townrow["guildname"]) {$castleid = $id;}
        if ($townrow["ruined"] != 0) {
	  		$status = "<p><center><img src=\"images/strongholddestroyed.jpg\" border=\"0\" alt=\"Outside a Destroyed Stronghold\" /></center><p>";
	  	}
	elseif ($userrow["guildname"] == $guildname) {
	  		$status = "<p><center><img src=\"images/stronghold.jpg\" border=\"0\" alt=\"Outside a Stronghold\" /></center><p>";
	  	} 

    $updatequery = doquery("UPDATE {{table}} SET location='Outside a Stronghold',currentaction='Stronghold',latitude='$newlat',longitude='$newlon' WHERE id='$newid' LIMIT 1", "users");

    $page = "<table width=\"100%\"><tr><td class=\"title\"><img src=\"images/title_stronghold.gif\" alt=\"Outside of a Guild Stronghold\" /></td></tr></table>";
	if ($townrow["ruined"] != 0) {$page .= "<p align='center'><b>The Stronghold lies in ruins!</b>";}
    $page .= "<p>You are standing outside of a Guild stronghold belonging to the <font color=red>'".$guildname."' Guild</font>.".$status."";
    $page .= "<p>You may ";
    if ($userrow["guildname"] == $guildname) {
	$page .= "<a href='strongholds.php'>enter this stronghold</a>";
    } else {
	$page .= "<a href='strongholds.php?do=siege'>try to break in</a> or you may continue exploring using the compass images to the right.";
    }


    display($page, "Guild Strongholds");

}

function homestravelto($id, $usepoints=true) { // Send a user to a town from the Travel To menu.

    global $userrow, $numqueries;

    $townquery = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "homes");
    $townrow = mysql_fetch_array($townquery);

    $newlat = $townrow["latitude"];
    $newlon = $townrow["longitude"];
    $newtown = $townrow["id"];
    $newid = $userrow["id"];
    $charname = $townrow["charname"];
    $guards = $townrow["guards"];
    if ($userrow["charname"] == $townrow["charname"]) {$castleid = $id;}
        if ($townrow["ruined"] != 0) {
	  		$status = "<p><center><img src=\"images/house.gif\" border=\"0\" alt=\"Outside a House\" /></center><p>";
	  	}
	elseif ($userrow["charname"] == $charname) {
	  		$status = "<p><center><img src=\"images/house.gif\" border=\"0\" alt=\"Outside a House\" /></center><p>";
	  	} 

    $updatequery = doquery("UPDATE {{table}} SET location='Outside a House',currentaction='Home',latitude='$newlat',longitude='$newlon' WHERE id='$newid' LIMIT 1", "users");

    $page = "<table width=\"100%\"><tr><td class=\"title\"><img src=\"images/title_home.gif\" alt=\"Outside of a House\" /></td></tr></table>";
	if ($townrow["ruined"] != 0) {$page .= "<p align='center'><b>The Home lies in ruins!</b>";}
    $page .= "<p>You are standing outside of a house, belonging to <font color=blue>".$charname."</font>, which appears to have around <b>".$guards."</b> Guards outside.".$status."";
    $page .= "<p>You may ";
    if ($userrow["charname"] == $charname) {
	$page .= "<a href='home.php'>enter your home</a>";
    } else {
	$page .= "<a href='index.php'>try to destroy the home</a>, or you may continue exploring using the compass images to the right. (you cannot destroy homes yet!)";
    }


    display($page, "Outside a Home");

}

function logbridge() { // Cross the log for the flower - quest 4
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

  if($userrow["tempquest"] == "flower" && $userrow["longitude"] == "364" && $userrow["latitude"] == "474") { //Quest 4 has been started and needs to get flower        
        $title = "Crossing a Log";
        $page = "<table width='100%' border='1'><tr><td class='title'>Crossing a Log</td></tr></table><p>";
        $page .= "<i>You carefully cross the narrow log, leading to a small patch of land, just outside town.<p>You have a small look around, to discover a few flowers. It must be the Kings Grace, since there is no other signs of life in this Town.</i><p><font color=blue>You have found the Kings Grace Flower!</font><p><i>You carefully pick one, and place it into your inventory.</i><br /><br />\n";
        $page .= "You may return to <a href=\"index.php\">town</a> through the backway, or use the compass on the right to start exploring.<br />\n";      
        $inventitemsquery = doquery("SELECT id FROM {{table}}","inventitems");
        $userinventitems = explode(",",$userrow["inventitems"]);
        //add item
        array_push($userinventitems, 98);
        $new_userinventitems = implode(",",$userinventitems);
        $userid = $userrow["id"];
        doquery("UPDATE {{table}} SET inventitems='$new_userinventitems',location='Crossing a Log',tempquest='gotflower' WHERE id='$userid' LIMIT 1", "users");
        
      }  
else {

        { header("Location: index.php"); die(); }
}   
    
    display($page, $title);
    
}

function gamble($game) {
 global $userrow;
 
    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

    if (isset($_POST["submit"])) {
 $game = $_POST["game"];
 $page = "<table width=\"100%\"><tr><td class=\"title\">Gambling Den</td></tr></table>";
 if ($game == "dice") {
  if ($userrow["gold"] < 150) {
  $page .= "You do not have enough gold to roll the Dice!<br>";
  $page .= "You may go back to <a href='index.php'>town</a>, if you have changed your mind. ";
  display($page, "Gambling Den");
  }
  $d1 = rand(1,6);
  $d2 = rand(1,6);
  $d3 = rand(1,6);
  $page .= "You Roll the Dice and received:<br><table><tr>";
  $page .= "<td align='center'><img
src='img/dice".$d1.".gif'><br><b>".$d1."</b></td>";
  $page .= "<td align='center'><img
src='img/dice".$d2.".gif'><br><b>".$d2."</b></td>";
  $page .= "<td align='center'><img
src='img/dice".$d3.".gif'><br><b>".$d3."</b></td>";
  $page .= "</tr></table>";
  $newgold = $userrow["gold"];

  $payout = -150;
  if (($d1 == $d2) && ($d1 == $d3)) {$payout = $d1*400; }
  if (($d2 == $d1+1) && ($d3 == $d2+1)) {$payout = $d1*60;}
  if (($d2 == $d1-1) && ($d3 == $d2-1)) {$payout = $d1*50;}
  $newgold += $payout;
  if ($payout >= 0) {
   $page .= "<h3 align='center'>You Won!</h3>";
   $page .= "You have won a total of <b>".$payout."</b> gold!<p>";
  } else {
   $page .= "You did not win anything...<p>";
  }
  $page .= "<form action='index.php?do=gamble' method='POST'>";
  $page .= "You may either <input type='Submit' name='submit' value='Play
Again (150g)' onclick='over18sonly();'> ";
  $page .= "<input type='hidden' name='game' value='dice'></form></div>";
  $page .= "or go back to <a href='index.php'>town.</a><p>";
  $query = doquery("UPDATE {{table}} SET
gold='$newgold' WHERE id='".$userrow["id"]."' LIMIT 1",
"users");
  display($page, "Gambling Den");

 } elseif ($game == "shells") {
  if ($userrow["gold"] < 60) {
  $page .= "You do not have enough gold to play Shells!<br>";
  $page .= "You may go back to <a href='index.php'>Town</a>,
";
  $page .= " or leave and ";
  $page .= "continue exploring using the compass</a>.<p>";
  display($page, "Gambling Den");
  }
  if (!isset($_POST["shellnumber"])) {
  $page .= "Under one of these shells is hidden a rare blue pearl.";
  $page .= "If you choose the correct shell, you win some gold, if not you
";
  $page .= "can always try playing again!<p>";
  $page .= "Please Choose a Shell:";
  $page .= "<table><tr>";
  $page .= "<td align='center'><FORM ACTION='index.php?do=gamble:shells'
method='post'>";
  $page .= "<img SRC='img/gambleshell.gif' ALT='Shell 1'><br>";
  $page .= "<input type='Submit' name='submit' value='Shell 1'>";
  $page .= "<input type='hidden' name='game' value='shells'>";
  $page .= "<input type='hidden' name='shellnumber' value='1'>";
  $page .= "</FORM></td>";
  $page .= "<td align='center'><FORM ACTION='index.php?do=gamble:shells'
method='post'>";
  $page .= "<img SRC='img/gambleshell.gif' ALT='Shell 2'><br>";
  $page .= "<input type='Submit' name='submit' value='Shell 2'>";
  $page .= "<input type='hidden' name='game' value='shells'>";
  $page .= "<input type='hidden' name='shellnumber' value='2'>";
  $page .= "</FORM></td>";
  $page .= "<td align='center'><FORM ACTION='index.php?do=gamble'
method='post'>";
  $page .= "<img SRC='img/gambleshell.gif' ALT='Shell 3'><br>";
  $page .= "<input type='Submit' name='submit' value='Shell 3'>";
  $page .= "<input type='hidden' name='game' value='shells'>";
  $page .= "<input type='hidden' name='shellnumber' value='3'>";
  $page .= "</FORM></td></tr></table>";
  $page .= "Simply Click on a shell to see if you win!<p>";
  $page .= "You may go back to <a href='index.php'>town</a>, if you have changed your mind. ";
  display($page,"Gambling Den");
  }
  $payout = -60;
  $newgold = $userrow["gold"];
  $correct = rand(1,3);
  $page .= "You chose shell number <b>".$_POST["shellnumber"]."</b>...<p>";
  if ($correct == $_POST["shellnumber"]) {
   $payout = $correct*50;
   $page .= "<h3><img src='img/gambleshellw.gif'><br>You Won!</h3>";
   $page .= "You have won a total of <b>".$payout."</b> gold!<p>";
  } else {
   $page .= "<img src='img/gambleshell.gif'><br>";
   $page .= "You did not win anything...<p>";
  }
  $newgold += $payout;
  $page .= "<form action='index.php?do=gamble' method='POST'>";
  $page .= "You may either <input type='Submit' name='submit' value='Play
Again (60g)' onclick='over18sonly();'> ";
  $page .= "<input type='hidden' name='game' value='shells'></form></div>";
  $page .= "or
return to ";
  $page .= "<a href='index.php'>town.</a><p>";
  $query = doquery("UPDATE {{table}} SET gold='$newgold' WHERE
id='".$userrow["id"]."' LIMIT 1", "users");
  display($page, "Gambling Den");

 } elseif ($game == "cards") {
  if ($userrow["gold"] < 35) {
  $page .= "You do not have enough gold to play Card Hi-Low!<br>";
  $page .= "You may go back to <a href='index.php'>town</a>,</a>
";
  $page .= "or leave and ";
  $page .= "continue exploring using the compass.</a><p>";
  display($page, "Gambling Den");
  }
  if (!isset($_POST["guess"])) {
  $page .= "This game is simple, A card is drawn, and you must choose
whether the next card ";
  $page .= "will be higher or lower in value.  Aces are HIGH, 2 is
LOW.<br>";
  $page .= "The suit of the card also matters!  The Joker is the highest
value of any card.<br>";
  $page .= "Spades are low, then Hearts, then Clubs, then Diamonds
High.<br>";
  $page .= "Choose wisely!<p>";
  $page .= "Your Card is:";
  $cardvalue = rand(2,14);
  $cardsuit = rand(1,4);
  $cardpicture = "img/card".$cardvalue.$cardsuit.".jpg";
  if (rand(1,54) >= 53) {$cardvalue = 15; $cardsuit='5';$cardpicture =
"img/card55.jpg";}

  $page .= "<table><tr>";
  $page .= "<td align='center' colspan='2'>First Card:<br><img
SRC='$cardpicture' ALT='Card'>";
  $page .= "<br>Card Value: <b>".$cardvalue."</b></td></tr>";
  $page .= "<tr><td align='center'><FORM ACTION='index.php?do=gamble:cards'
method='post'>";
  $page .= "<input type='Submit' name='submit' value='HIGHER'>";
  $page .= "<input type='hidden' name='game' value='cards'>";
  $page .= "<input type='hidden' name='guess' value='higher'>";
  $page .= "<input type='hidden' name='firstcard' value='".$cardvalue."'>";
  $page .= "<input type='hidden' name='firstsuit' value='".$cardsuit."'>";
  $page .= "</FORM></td>";
  $page .= "<td align='center'><FORM ACTION='index.php?do=gamble:cards'
method='post'>";
  $page .= "<input type='Submit' name='submit' value='LOWER'>";
  $page .= "<input type='hidden' name='game' value='cards'>";
  $page .= "<input type='hidden' name='guess' value='lower'>";
  $page .= "<input type='hidden' name='firstcard' value='".$cardvalue."'>";
  $page .= "<input type='hidden' name='firstsuit' value='".$cardsuit."'>";
  $page .= "</FORM></td></tr></table>";
  $page .= "Simply Click on a choice to see if you win!<p>";
  $a = doquery("UPDATE {{table}} SET templist='cards' WHERE id='".$userrow["id"]."' ", "users");
  $page .= "You may go back to <a href='index.php'>town</a>, if you have changed your mind. ";
  display($page,"Gambling Den");
  }
  if ($userrow["templist"] != "cards") {header("Location: index.php?do=gamble"); die(); }
  $payout = -35;
  $newgold = $userrow["gold"];
  $firstcard = $_POST["firstcard"];
  $firstsuit = $_POST["firstsuit"];
  $guess = $_POST["guess"];
  $nextcard = rand(2,14);
  $nextsuit = rand(1,4);
  while (($firstcard == $nextcard) && ($firstsuit == $nextsuit)) {$nextcard
= rand(1,14); $nextsuit=rand(1,4);}
  $page .= "You chose the next card was <b>".$_POST["guess"]."</b>...<p>";

  $cardpicture1 = "img/card".$firstcard.$firstsuit.".jpg";
  $cardpicture2 = "img/card".$nextcard.$nextsuit.".jpg";
  if ($firstcard >= 15) {$cardpicture1 = "img/card55.jpg"; $firstcard =
$nextcard;}
  $page .= "<table><tr>";
  $page .= "<td align='center'>First Card:<br><img SRC='$cardpicture1'
ALT='Card'><br>";
  $page .= "Value:".$firstcard."</td>";
  $page .= "<td align='center'>Next Card:<br><img SRC='$cardpicture2'
ALT='Card'><br>";
  $page .= "Value:".$nextcard."</td>";
  $page .= "</tr></table>";

  if (($guess == "higher") &&($firstcard < $nextcard)) {
   $payout = rand(1,$nextcard)*2 + 25;
   $page .= "<h3>You Won!</h3>";
   $page .= "You have won a total of <b>".$payout."</b> gold!<p>";
  } elseif (($guess == "lower") &&($firstcard > $nextcard)) {
   $payout = rand(1,$nextcard)*2 + 25;
   $page .= "<h3>You Won!</h3>";
   $page .= "You have won a total of <b>".$payout."</b> gold!<p>";
  } else {
   $page .= "You did not win anything...<br>";
   if ($cardpicture1 == "img/card55.jpg") {$payout = 25; $page .= "...but
the first card was a joker so you broke even.<br>";}
  }
  $newgold += $payout;
  $page .= "<form action='index.php?do=gamble' method='POST'>";
  $page .= "You may either <input type='Submit' name='submit' value='Play
Again (35g)' onclick='over18sonly();'> ";
  $page .= "<input type='hidden' name='game' value='cards'></form></div>";
  $page .= "or
return to ";
  $page .= "<a href='index.php'>town.</a><p>";
  $query = doquery("UPDATE {{table}} SET gold='$newgold', templist='0' WHERE
id='".$userrow["id"]."' LIMIT 1", "users");
  display($page, "Gambling Den");


 } elseif ($game == "wheel") {
  if ($userrow["gold"] < 150) {
  $page .= "You do not have enough gold to spin the Wheel!<br>";
  $page .= "You may go back to <a href='index.php'>Town</a>,
";
  $page .= "or head back to exploring by using the compass.</a>";
  display($page, "Gambling Den");
  }
  $payout = -150;
  $newgold = $userrow["gold"];
  $chance = rand(1,50);
  $page .= "You spin the Wheel of Fortune and received a
<b>".$chance."</b>...<br>";
  if ($chance <=6) {
   $payout = $chance*150;
   $page .= "<h3>You Won!</h3>";
   $page .= "You have won a total of <b>".$payout."</b> gold!<p>";
  } else {
   $page .= "<br>";
   $page .= "You did not win anything...Sorry.<p>";
  }
  $newgold += $payout;
  $page .= "<form action='index.php?do=gamble' method='POST'>";
  $page .= "You may either <input type='Submit' name='submit' value='Play
Again (150g)' onclick='over18sonly();'> ";
  $page .= "<input type='hidden' name='game' value='wheel'></form></div>";
  $page .= "or go back to <a href='index.php'>town.</a><p>";
  $query = doquery("UPDATE {{table}} SET gold='$newgold' WHERE
id='".$userrow["id"]."' LIMIT 1", "users");
display($page, "Gambling Den");
 } else {
  $page .= "<p> ERROR:  You must choose a game to play, or there was an
error processing ";
  $page .= "the request.  Please <a href='index.php?do=gamble'>Go Back</a>
and try again.<p>";
 }

    } else {

$updatequery = doquery("UPDATE {{table}} SET location='Gambling Den' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

 $page = "<table width='100%' border='1'><tr><td class='title'>Gambling Den</td></tr></table>";
 $page .= "<center><table><tr>";
 $page .= "<td align='center'><b><u>Card Hi-Low!</u></b><br> It only costs
35 gold!<br>";
 $page .= "A card is drawn, you say whether the next card will be higher or
lower. ";
 $page .= "If you win, you get more Gold, if you lose, just try again!";
 $page .= "<form action='index.php?do=gamble:cards' method='POST'>";
 $page .= "<input type='hidden' name='game' value='cards'>";
 $page .= "<input type='Submit' name='submit' value='Play (35g)'>";
 $page .= "</form></td>";

 $page .= "<td align='center'><b><u>Pick A Shell!</u></b><br> It only costs
60 gold!<br>";
 $page .= "A rare blue pearl has been hidden under one of three shells. ";
 $page .= " If you choose the shell with the pearl under it, you win!.<br>";
 $page .= "<form action='index.php?do=gamble:shells' method='POST'>";
 $page .= "<input type='hidden' name='game' value='shells'>";
 $page .= "<input type='Submit' name='submit' value='Play (60g)'>";
 $page .= "</form></td></tr><tr>";

 $page .= "<td align='center'><b><u>Wheel of Fortune!</u></b><br> It only
costs 150 gold!<br>";
 $page .= "Spin the wheel for fun and prizes!";
 $page .= "<form action='index.php?do=gamble:wheel' method='POST'>";
 $page .= "<input type='hidden' name='game' value='wheel'>";
 $page .= "<input type='Submit' name='submit' value='Play (150g)'>";
 $page .= "</form></td>";

 $page .= "<td align='center'><b><u>Roll the Dice!</u></b><br> It only costs
150 gold!<br> If you get 3-of-a-kind, you win big! ";
 $page .= "Other combinations also win smaller payouts.<br>";
 $page .= "<form action='index.php?do=gamble:dice' method='POST'>";
 $page .= "<input type='hidden' name='game' value='dice'>";
 $page .= "<input type='Submit' name='submit' value='Play (150g)'>";
 $page .= "</form></td></tr></table>";
 $page .= "Please note that whatever amount of gold you pay out, you get it back if you win.<p>You may go back to <a href='index.php'>town</a>, if you have changed your mind. ";
    }
 display($page, "Gambling Den");
}

function bank() { // Bank Function

    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

$updatequery = doquery("UPDATE {{table}} SET location='Town Bank' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
 

        $title = "Town Bank";
        $page = "<table width='100%' border='1'><tr><td class='title'>Town Bank</td></tr></table><p>";
        $page .= "<br><b><u>Deposit and Withdraw Gold</u>:</b><p>Depositing all your gold will ensure you don't lose it while exploring the battle field and from duels.<br /><br />\n";
        $page .= "You have <b>" . number_format($userrow["bank"])
 . "</b> gold in the bank.</b><br /><br />\n";
        $page .= "<form action=\"index.php?do=bank\" method=\"post\">\n";


        $page .= "<input type=\"submit\" name=\"submit\" value=\"Deposit\" /> <input type=\"submit\" name=\"cancel\" value=\"Withdraw\" /><br /><br /><b><u>Trade Gold to other Players</u>:</b><p>Enter the ID number of the player you want to transfer gold to. You will be charged a <b>15% tax</b> for trading gold to another player. Gold <b>must</b> be stored in your bank to be able to transfer it and it then appears in their bank account.<br /><br /><b>ID Number</b>:<br><input type=\"text\" name=\"id\" value=\"0\" /> E.g. 1001<br /><b>Enter the amount you wish to Transfer</b>:<br /><input type=\"text\" name=\"amount\" value=\"0\" /> E.g. 10000<br /><input type=\"submit\" name=\"submit2\" value=\"Transfer Gold\" /><p><p>If you've changed your mind, you may also return back to <a href=\"index.php\">town</a>.\n";
        $page .= "</form>\n";

 if (isset($_POST["submit"])) {


        $newgold = $userrow["gold"] - $userrow["gold"];
        $newbank = $userrow["bank"] + $userrow["gold"];
        $query = doquery("UPDATE {{table}} SET gold='$newgold',bank='$newbank',currenthp='".$userrow["currenthp"]."',currentmp='".$userrow["currentmp"]."',currenttp='".$userrow["currenttp"]."' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Town Bank - Deposited Gold";
        $page = "You have deposited your gold successfully!<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass image on the right to start exploring.";
}

elseif (isset($_POST["cancel"])) {



        $newgold = $userrow["gold"] + $userrow["bank"];
        $newbank = $userrow["bank"] - $userrow["bank"];
        $query = doquery("UPDATE {{table}} SET gold='$newgold',bank='$newbank',currenthp='".$userrow["currenthp"]."',currentmp='".$userrow["currentmp"]."',currenttp='".$userrow["currenttp"]."' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Town Bank - Withdrawn Gold";
        $page = "You have withdrawn your gold successfully!<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass image on the right to start exploring.";


    }

if(isset($_POST['submit2']))
  {

    $yourstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
    $yourstats2=mysql_query($yourstats) or die("Could not get your stats");
    $yourstats3=mysql_fetch_array($yourstats2);
    $id=$_POST['id'];
    $id=strip_tags($id);
    $oppstats="SELECT * from dk_users where id='$id'";
    $oppstats2=mysql_query($oppstats) or die("Could not get player's stats");
    $oppstats3=mysql_fetch_array($oppstats2);

    if($yourstats3["id"]==$oppstats3["id"])
    {
       $page = "Giving yourself gold doesn't exactly help yourself.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass images on the right to start exploring.";

    }
else {
$postamount = $_POST['amount'];
if($postamount > $yourstats3["bank"]) {
$page = "You don't have that much gold in your bank to trade.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass images on the right to start exploring.";
}
elseif($postamount <= 100) {
$page = "You can't send less than 100 gold.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass images on the right to start exploring.";
}
elseif($userrow["level"] < 15) {
$page = "You must be level 15 or above before you can trade gold.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass images on the right to start exploring.";
}
else {
$taxamount = number_format($postamount * .15);
$postamount2 = $postamount - $taxamount;


$newoppbank = $oppstats3["bank"] + $postamount2;

  $yournewbank = $yourstats3["bank"] - $postamount;
 $updateyourstats="update dk_users set bank='$yournewbank' where charname='".$userrow["charname"]."'";
        mysql_query($updateyourstats) or die("Could not update your stats");
        $updateopp="update dk_users set bank='$newoppbank' where id='$id'";
        mysql_query($updateopp) or die(mysql_error());
        $page = "You have transfered your gold successfully from your bank account to their bank account.<br />Although you incurred <b>$taxamount</b> gold as tax!<br /><p>You may return to <a href=\"index.php\">town</a>, or use the compass images on the right to start exploring.";


}

}}
    display($page, $title);

}
?>