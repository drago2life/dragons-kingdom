<?php // heal.php :: Handles stuff from the Quick items menu.

function quickitems($id) {

    global $userrow;

    $userinventitems = explode(",",$userrow["inventitems"]);
    $inventitemsquery = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "inventitems");
    $inventitemsrow = mysql_fetch_array($inventitemsquery);

    // All the various ways to error out.
    if ($userrow["currentaction"] == "Fighting") { display("You cannot use the Quick Items list during a fight. Please select an inventitems from the drop-down list within the main combat screen.<br> <a href='index.php'>Click here to continue.</a>", "Error"); die(); }
	$inventitems = false;
	$count = "0";
    foreach ($userinventitems as $a => $b) {
        if ($b == $id) { $inventitems = true; $inventitemsslot = $count;}
	  $count += 1;
    }

    if ($inventitems != true) { display("You do not have this Item in your inventory. Please go back and try again.<br>You can now continue <a href='index.php'>exploring</a>.", "Error"); die(); }
    if ($userrow["currentaction"] == "Cave" && $inventitemsrow["id"] != "79") { display("You cannot use the Quick Items list in a Cave or Healing Pool. You may only use your Desert Tent only.  Please go <a href='index.php'>back</a>  and continue exploring.", "Error"); die(); }
    if ($userrow["currentaction"] == "Healing Pool" && $inventitemsrow["id"] != "79") { display("You cannot use the Quick Items list in a Cave or Healing Pool. You may only use your Desert Tent only. Please go <a href='index.php'>back</a>  and continue exploring.", "Error"); die(); }
    
	if ($inventitemsrow["charges"] <= rand(1,100)) { $userinventitems[$inventitemsslot] = "0";}

	$newinventitems = rsort($userinventitems);
	$inventitems = join(",",$userinventitems);
	$updatequery = doquery("UPDATE {{table}} SET inventitems='$inventitems' WHERE id='".$userrow["id"]."' LIMIT 1", "users");


    if ($inventitemsrow["type"] == '1') {    //heal inventitems

    $newhp = $userrow["currenthp"] + $inventitemsrow["strength"];
    if ($userrow["maxhp"] < $newhp) { $inventitemsrow["strength"] = $userrow["maxhp"] - $userrow["currenthp"]; $newhp = $userrow["currenthp"] + $inventitemsrow["strength"]; }

    $updatequery = doquery("UPDATE {{table}} SET currenthp='$newhp' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

    display("<table width='100%'><tr><td class='title'>Healing Item</td></tr></table><p>You have used the ".$inventitemsrow["name"]." Item, and gained ".$inventitemsrow["strength"]." Hit Points. You can now continue <a href='index.php'>exploring</a>.", "Healing Item");

    } elseif ($inventitemsrow["type"] == '13') {   //Restore Magic inventitems
    $newmp = $userrow["currentmp"] + $inventitemsrow["strength"];
    if ($userrow["maxmp"] < $newmp) {
	$inventitemsrow["strength"] = $userrow["maxmp"] - $userrow["currentmp"];
	$newmp = $userrow["currentmp"] + $inventitemsrow["strength"];
	}
    $uq = doquery("UPDATE {{table}} SET currentmp='$newmp' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
    display("<table width='100%'><tr><td class='title'>Restore Magic Item</td></tr></table><p>You have used the ".$inventitemsrow["name"]." Item, and gained ".$inventitemsrow["strength"]." Magic Points. You can now continue <a href='index.php'>exploring</a>.", "Restore Magic Item");


    } elseif ($inventitemsrow["type"] == '14') {   //Restore TP items
    $newtp = $userrow["currenttp"] + $inventitemsrow["strength"];
    if ($userrow["maxtp"] < $newtp) {
	$inventitemsrow["strength"] = $userrow["maxtp"] - $userrow["currenttp"];
	$newtp = $userrow["currenttp"] + $inventitemsrow["strength"];
	}
    $uq = doquery("UPDATE {{table}} SET currenttp='$newtp' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
    display("<table width='100%'><tr><td class='title'>Restore TP Item</td></tr></table><p>You have used the ".$inventitemsrow["name"]." Item, and gained ".$inventitemsrow["strength"]." Travel Points. You can now continue <a href='index.php'>exploring</a>.", "Restore TP Item");


     } elseif ($inventitemsrow["type"] == '18') {   //Health Restore Drink Items
    $newhp = $userrow["currenthp"] + $inventitemsrow["strength"];
    if ($userrow["maxhp"] < $newhp) { $inventitemsrow["strength"] = $userrow["maxhp"] - $userrow["currenthp"]; $newhp = $userrow["currenthp"] + $inventitemsrow["strength"]; }
    $updatequery = doquery("UPDATE {{table}} SET currenthp='$newhp' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	$page = "<table width='100%'><tr><td class='title'>Healing Item</td></tr></table><p>";
	$page .= "You have drank the ".$inventitemsrow["name"].", and gained ".$inventitemsrow["strength"]." Hit Points.";
	$page .= "You can now continue <a href='index.php'>exploring</a>.";
    display($page,"Healing Item");
    die();

     } elseif ($inventitemsrow["type"] == '15') {   //AP restore Drink Items
    $newap = $userrow["currentap"] + $inventitemsrow["strength"];
    if ($userrow["maxap"] < $newap) { $inventitemsrow["strength"] = $userrow["maxap"] - $userrow["currentap"]; $newap = $userrow["currentap"] + $inventitemsrow["strength"]; }
    $updatequery = doquery("UPDATE {{table}} SET currentap='$newap' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	$page = "<table width='100%'><tr><td class='title'>AP Restore Item</td></tr></table><p>";
	$page .= "You consume your ".$inventitemsrow["name"].", item and gained ".$inventitemsrow["strength"]." Ability Points.";
	$page .= "You can now continue <a href='index.php'>exploring</a>.";
    display($page,"AP Restore Item");
    die();

     } elseif ($inventitemsrow["type"] == '21') {   //Cemetery Memorial Item
	$page = "<table width='100%'><tr><td class='title'>Cemetery Memorial</td></tr></table>";
	$page .= "<p>You carefully place the ".$inventitemsrow["name"].", on the ground in memory of a fallen Friend.<br>";
	if (rand(1,$inventitemsrow["strength"]) <= 1) {
		$newhp = $userrow["currenthp"] + rand(1,($inventitemsrow["strength"]));
		if ($userrow["maxhp"] < $newhp) { $inventitemsrow["strength"] = $userrow["maxhp"] - $userrow["currenthp"]; $newhp = $userrow["currenthp"] + $inventitemsrow["strength"]; }
		$updatequery = doquery("UPDATE {{table}} SET currenthp='$newhp' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
		$page .= "The Souls of Dragons Kingdom are pleased with your actions and refill a few of your HP!<br>";
	}
	$page .= "<br>You can now continue <a href='index.php'>exploring</a>.";
    display($page,"Memorial Items");
    die();

    } elseif ($inventitemsrow["type"] == '11') {   //Safeportal item

		$townquery = doquery("SELECT * FROM {{table}} WHERE name='".$userrow["lasttown"]."' LIMIT 1", "towns");
  		$townrow = mysql_fetch_array($townquery);
		$townid = $townrow["id"];
	    	$page = "<table width=\"100%\"><tr><td class=\"title\">Safe Portal Items</td></tr></table>";
  		$page .= "<b>You use the Safe Portal Item.</b><br>";
		$page .= "A shimmering doorway appears before you, leading to ".$townrow["name"].".<br>";
   		$page .= "You may either <a href=\"index.php?do=gotown:".$townid."\">enter the doorway</a>";
		$page .= ", or continue exploring using the image to the right.<p>";

		display($page, "Travel To");

		} elseif ($inventitemsrow["type"] == '13') {   //Restore Magic item
		$newhp = $userrow["currenthp"] + $inventitemsrow["strength"];
		if ($userrow["maxhp"] < $newhp) { $inventitemsrow["strength"] = $userrow["maxhp"] - $userrow["currenthp"]; $newhp = $userrow["currenthp"] + $inventitemsrow["strength"]; }
		$updatequery = doquery("UPDATE {{table}} SET currenthp='$newhp' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
		display("<table width=\"100%\"><tr><td class=\"title\">Healing Items</td></tr></table>You have used the ".$inventitemsrow["name"]." Item, and gained ".$inventitemsrow["strength"]." Hit Points. You can now continue <a href=\"index.php\">exploring</a>.", "Healing Items");
		die();


    } elseif ($inventitemsrow["type"] == "16") { // teleport item
	$title = "Teleport Spell";
	    	$page = "<table width=\"100%\"><tr><td class=\"title\">Teleport spell</td></tr></table>";
       	$page .= "You may teleport to any other town you have visited before.";
       	$page .= "<br><b>Please choose a town to travel to.</b><p>";
       	$page .= "<b> Towns in $mapname </b><br><table><tr>";
		$knowntowns = $userrow["knowntowns"];
	   	$townquery = doquery("SELECT * FROM {{table}} WHERE id IN ($knowntowns) AND realm<='".($userrow["realm"]+10)."' AND realm>='".($userrow["realm"]-10)."' ORDER BY realm asc,name asc", "towns");
       	if (mysql_num_rows($townquery) == 0) { display("There is an error with your user account, or with the town data. Please try again.","Error"); }
		$count=0; $townlist = "0";
       	while ($townrow = mysql_fetch_array($townquery)) {
		$count += 1;
		if ($townrow["latitude"] < 0) { $townrow["latitude"] = $townrow["latitude"] * -1 . "S"; } else { $townrow["latitude"] .= "N"; }
		if ($townrow["longitude"] < 0) { $townrow["longitude"] = $townrow["longitude"] * -1 . "W"; } else { $townrow["longitude"] .= "E"; }
    	   	$page .= "<td><b>".$count."</b>)</td><td> <a href=\"index.php?do=gotown:".$townrow["id"]."\">".$townrow["name"]."</a></td><td align=\"center\">(";
    	   	$page .= $townrow["latitude"].",".$townrow["longitude"].")</td><td>".$townrow["realmname"]."</td></tr>";
		$townlist .= ",".$townrow["id"];
		}
	   	$page .= "</table>";
    		$newmp = $userrow["currentmp"] - $spellrow["mp"];
    		$updatequery = doquery("UPDATE {{table}} SET templist='$townlist',currentmp='$newmp' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
    		display($page, $title);

     } elseif ($inventitemsrow["type"] == '17') {   //Antidote item
	$page = "<table width='100%'><tr><td class='title'>Antidote Item</td></tr></table>";
	$page .= "<p>You use the ".$inventitemsrow["name"].", and recover from any status effects.<p>";
	$updatequery = doquery("UPDATE {{table}} SET status='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	$page .= "<br>You can now continue <a href='index.php'>exploring</a>.";
	display($page,"Antidote Item");

     } elseif ($inventitemsrow["type"] == '25') {   //XP Gem
	$earnedXP = 500;
	$newXP = $userrow["experience"] + $earnedXP;
	if ($newXP >= 999999) {$newXP = 999999;}
	$page = "<table width='100%'><tr><td class='title'>Experience Gem</td></tr></table>";
	$page .= "<p>You use the ".$inventitemsrow["name"].", and earned ".$earnedXP." Experience Points!<p>";
	$updatequery = doquery("UPDATE {{table}} SET experience='$newXP' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	$page .= "<br>You can now continue <a href='index.php'>exploring</a>.";
	display($page,"XP Gem");

     } elseif ($inventitemsrow["type"] == '26') {   //XP Jewel
	$earnedXP = intval($userrow["experience"]*.02);
	$newXP = $userrow["experience"] + $earnedXP;
	if ($newXP >= 999999) {$newXP = 999999;}
	$page = "<table width='100%'><tr><td class='title'>Experience Gem</td></tr></table>";
	$page .= "<p>You use the ".$inventitemsrow["name"].", and earned ".$earnedXP." Experience Points!<p>";
	$updatequery = doquery("UPDATE {{table}} SET experience='$newXP' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	$page .= "<br>You can now continue <a href='index.php'>exploring</a>.";
	display($page,"XP Gem");

     } elseif ($inventitemsrow["type"] == '27') {   //XP Jewel2
	$earnedXP = intval($userrow["experience"]*.05);
	$newXP = $userrow["experience"] + $earnedXP;
	if ($newXP >= 999999) {$newXP = 999999;}
	$page = "<table width='100%'><tr><td class='title'>Experience Gem</td></tr></table>";
	$page .= "<p>You use the ".$inventitemsrow["name"].", and earned ".$earnedXP." Experience Points!<p>";
	$updatequery = doquery("UPDATE {{table}} SET experience='$newXP' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	$page .= "<br>You can now continue <a href='index.php'>exploring</a>.";
	display($page,"XP Gem");

 } elseif ($inventitemsrow["type"] == '28') {   //Restore Fatigue

$uq = doquery("UPDATE {{table}} SET location='Restore Fatigue' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
display("<table width='100%'><tr><td class='title'>Restore Fatigue</td></tr></table><p><center>Please type the image Code below, to restore your Fatigue.</center><p><center><img src=\"FormProtect.php?gen\"></center><p><form action=\"fatigue.php\" method=\"POST\" target=\"_self\"><p align=\"center\"><font size=\"1\" face=\"Verdana\">Enter Code: </font><input name=\"code\" type=\"text\" id=\"code\" size=\"10\"> <input type=\"submit\" name=\"Submit\" value=\"Restore\"></p></form><p><center>If you can't see the code correctly, <a href=\"javascript:location.reload()\" target=\"_self\">refresh</a> the page. Letters are case sensative.</center><p>If you have changed your mind then you can continue <a href='index.php'>exploring</a> using the compass images to the right.", "Restore Fatigue");
              
              
 } elseif ($inventitemsrow["type"] == '29') {   //Quest1 Item - Lost Fortune Quest - Ring

    display("<table width='100%'><tr><td class='title'>Precious Ring</td></tr></table><p>The Ring is covered in Blood. You give it a quick clean and see it sparkle in the light. It looks precious and expensive.. I wonder who this belongs to.<p>You can now continue <a href='index.php'>exploring</a>.", "Precious Ring");

 } elseif ($inventitemsrow["type"] == '30') {   //Quest2 Item - Potion Assistance - Bag of ingredients

    display("<table width='100%'><tr><td class='title'>Bag of Ingredients</td></tr></table><p>A Bag of Ingredients which the Son of Lucas needs. You should deliver these as soon as possible.<p>You can now continue <a href='index.php'>exploring</a>.", "Bag of Ingredients");

 } elseif ($inventitemsrow["type"] == '31') {   //Quest2 Item - Potion Assistance - Empty vial

    display("<table width='100%'><tr><td class='title'>Empty Vial</td></tr></table><p>This empty vial doesn't look like its of any use being empty. Maybe you should use this for creating Potions.<p>You can now continue <a href='index.php'>exploring</a>.", "Empty Vial");

 } elseif ($inventitemsrow["type"] == '32') {   //Quest2 Item - Potion Assistance - a rare herb

    display("<table width='100%'><tr><td class='title'>Rare Potent Herb</td></tr></table><p>A Rare Potent Herb which you found from Exploring outside of Town. I wonder what this Herb does...<p>You can now continue <a href='index.php'>exploring</a>.", "A Rare Herb");

 } elseif ($inventitemsrow["type"] == '33') {   //Quest2 Item - Potion Assistance - empty bucket

    display("<table width='100%'><tr><td class='title'>Empty Bucket</td></tr></table><p>A Bucket containing nothing. Looks completely useless unless you are going to make any use of it.<p>You can now continue <a href='index.php'>exploring</a>.", "Empty Bucket");

 } elseif ($inventitemsrow["type"] == '34') {   //Quest2 Item - Potion Assistance - bucket of healing water

    display("<table width='100%'><tr><td class='title'>Bucket of Healing Water</td></tr></table><p>A Bucket filled of Healing Water which you got from the Healing Pool. You should take this immediately to Lucas.<p>You can now continue <a href='index.php'>exploring</a>.", "Bucket of Healing Water");

      } elseif ($inventitemsrow["type"] == '35') {   //Restore Fatigue Item, without a code for blind player named: stirlock

    $uq = doquery("UPDATE {{table}} SET currentfat='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
    display("<table width='100%'><tr><td class='title'>Restore Fatigue Item</td></tr></table><p>You have used your Special Desert Tent to Restore your Fatigue back to 0%.<p>You can now continue <a href='index.php'>exploring</a>.", "Restore Fatigue Item");

     } elseif ($inventitemsrow["type"] == '36') {   //Quest4 Item - The Parasite - Empty Crystal Jar

    display("<table width='100%'><tr><td class='title'>Empty Crystal Jar</td></tr></table><p>An Empty Crystal Jar which you bought from the Jewellers at Narcillas Port.<p>You can now continue <a href='index.php'>exploring</a>.", "Empty Crystal Jar");

         } elseif ($inventitemsrow["type"] == '37') {   //Quest4 Item - The Parasite - Crystal Jar with Ingredients

    display("<table width='100%'><tr><td class='title'>Crystal Jar with Ingredients</td></tr></table><p>A Crystal Jar with Ingredients, which Magnus put in there. It contains a Dragons Special and spices.<p>You can now continue <a href='index.php'>exploring</a>.", "Crystal Jar with Ingredients");

             } elseif ($inventitemsrow["type"] == '38') {   //Quest4 Item - The Parasite - Crystal Jar with a Parasite

        display("<table width='100%'><tr><td class='title'>Crystal Jar with a Parasite</td></tr></table><p>A Crystal Jar with a Parasite in it, which you captured from a Monsters Corpse.<p>You can now continue <a href='index.php'>exploring</a>.", "Crystal Jar with a Parasite");

        
                     } elseif ($inventitemsrow["type"] == '39') {   //Quest4 Item - The Parasite - King's Grace Flower

        display("<table width='100%'><tr><td class='title'>King's Grace Flower</td></tr></table><p>A Kings Grace Flower which you hand picked from outside the Abandoned Ruins.<p>You can now continue <a href='index.php'>exploring</a>.", "King's Grace Flower");


                             } elseif ($inventitemsrow["type"] == '40') {   //Ring Mould

        display("<table width='100%'><tr><td class='title'>Ring Mould</td></tr></table><p>Without this you wouldn't be able to craft fine Rings.<p>You can now continue <a href='index.php'>exploring</a>.", "Ring Mould");

                                    } elseif ($inventitemsrow["type"] == '41') {   //Amulet Mould

        display("<table width='100%'><tr><td class='title'>Amulet Mould</td></tr></table><p>Without this you wouldn't be able to craft fine Amulets.<p>You can now continue <a href='index.php'>exploring</a>.", "Amulet Mould");

        
                                       } elseif ($inventitemsrow["type"] == '50') {   //Bday Cake
        $p = doquery("UPDATE {{table}} SET attributes=attributes+30 WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        display("<table width='100%'><tr><td class='title'>Birthday Cake</td></tr></table><p>You slowly eat Adam's slice of Cake. MmmMmm... It tastes good. You gain <b>30</b> Attribute points.<p>You can now continue <a href='index.php'>exploring</a>.", "Birthday Cake");

        
        
        
     } elseif ($inventitemsrow["type"] == '20') {   //Guild Charter
     	$ownskey = strpos($userrow["keyinventitems"], 55);
     	if ($ownskey) {
		$page = "<table width='100%'><tr><td class='title'>Guild Charter</td></tr></table>";
		$page .= "<p>You read the  ".$inventitemsrow["name"].", and begin filling in the official forms...<p>";
		$page .= "<form action='index.php?do=guildcharter:".$id."' method='POST'>";
		$page .= "<table><tr><td colspan='2' align='center'>";
		$page .= "<b>Ye Olde Guild Charter</b><br></td></tr>";
		$page .= "<tr><td colspan='2'>Whereupon the recipient of this form shall, with all due merit and reward, have a Guild ";
		$page .= "forged and recorded in the Royal scrolls of Everthorn, and assuming all rights and priviledges ";
		$page .= "therein.  Said Guild shall be named and duly officiated by aforementioned recipient, and charged ";
		$page .= "with tasks of membership, rank, and Strongholds. </td></tr>";
		$page .= "<tr><td colspan='2' align='center'>";
		$page .= "Aut vincere aut mori...Auxilio ab alto.<br>";
		$page .= "<i>(Either conquor or die...with help from on high)</i></td></tr>";
		$page .= "<tr><td>Guild name:</td><td><input type='text' name='guildname' size='25' maxlength='25'></td></tr>";
		$page .= "<tr><td colspan='2' align='center'><input type='submit' name='submit' value='Form Guild'></td></tr>";
		$page .= "</table></form><p>";
		$p = doquery("UPDATE {{table}} SET templist='guildcharter' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
		display($page,"Guild Charter");
		} else {
		$page = "<table width='100%'><tr><td class='title'>Guild Registry</td></tr></table>";
		$page .= "<p>You have already founded one Guild, you may not register another Guild.<br>";
		$page .= "<br>You can now continue <a href='index.php'>exploring</a>.";
		display($page,"Guild Charter");
		}
	} else {
	$page = "<table width='100%'><tr><td class='title'>Unknown Item</td></tr></table>";
	$page .= "<p>There was a error with your request, or the Item you tried to use in an unknown type.<br>";
	$page .= "<br>You can now continue <a href='index.php'>exploring</a>.";
	display($page,"Unknown Item");
	die();
	}

}

?>