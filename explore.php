<?php // explore.php :: Handles all map exploring, chances to fight, etc.

function move_and_check($latitude, $longitude, $x_delta, $y_delta) {
    global $controlrow;
    $longitude+=$x_delta;
    $latitude+=$y_delta; 

    if ($latitude > $controlrow["gamesize"]) 
    { 
	$latitude = $controlrow["gamesize"]; 
    } 
    elseif ($latitude < ($controlrow["gamesize"]*-1))
    { 
	$latitude = ($controlrow["gamesize"]*-1);
    }
    if ($longitude > $controlrow["gamesize"])
    {
	$longitude = $controlrow["gamesize"]; 
    }
    elseif ($longitude < ($controlrow["gamesize"]*-1))
    {
	$longitude = ($controlrow["gamesize"]*-1);
    }

    $townquery = doquery("SELECT id FROM {{table}} WHERE latitude='$latitude' AND longitude='$longitude' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) > 0) {
        $townrow = mysql_fetch_array($townquery);
        include('towns.php');
        travelto($townrow["id"], false);
        die();
    }
    

     $castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='$latitude' AND longitude='$longitude' LIMIT 1", "strongholds");
   if (mysql_num_rows($castlequery) > 0) {
       $castlerow = mysql_fetch_array($castlequery);
       include('towns.php');
       castletravelto($castlerow["id"], false);
       die();
    }
   
    $homesquery = doquery("SELECT * FROM {{table}} WHERE latitude='$latitude' AND longitude='$longitude' LIMIT 1", "homes");
   if (mysql_num_rows($homesquery) > 0) {
       $homesrow = mysql_fetch_array($homesquery);
       include('towns.php');
       homestravelto($homesrow["id"], false);
       die();
       
   }

    return array($latitude, $longitude);
}

function move() {
    
    global $userrow, $controlrow;
        $fatlimit = $userrow["currentfat"];
        $fatlimit = $fatlimit + $userrow["run"];
        if($fatlimit > $userrow["maxfat"]){ header("Location: index.php?do=fatigue"); die(); }

    if ($userrow["currentaction"] == "Fighting") { header("Location: index.php?do=fight"); die(); }       

    $latitude = $userrow["latitude"];
    $longitude = $userrow["longitude"];
    $run = $userrow["run"];
    $x_delta=0;
    $y_delta=0;
    if (isset($_POST["direction"])) {
	switch ($_POST["direction"]) {
	    case "North":
		$x_delta=0;
		$y_delta=1;
		break;
	    case "South":
		$x_delta=0;
		$y_delta=-1;
		break;
	    case "East":
		$x_delta=1;
		$y_delta=0;
		break;
	    case "West":
		$x_delta=-1;
		$y_delta=0;
		break;
	    case "North East":
		$x_delta=1;
		$y_delta=1;
		break;
	    case "North West":
		$x_delta=-1;
		$y_delta=1;
		break;
	    case "South East":
		$x_delta=1;
		$y_delta=-1;
		break;
	    case "South West":
		$x_delta=-1;
		$y_delta=-1;
		break;
	}
    } // make first step and check
   
    list($latitude, $longitude) = move_and_check($latitude, $longitude, $x_delta, $y_delta);

   $mugchance = rand(1,215);
	if ($mugchance == 1) {
		$gold = intval(rand(1,$userrow["level"])*2+5);
		if ($gold > $userrow["gold"])
			$gold = $userrow["gold"];
		doquery("UPDATE {{table}} SET gold=gold-$gold WHERE id=".$userrow["id"], "users");
		doquery("UPDATE {{table}} SET $action latitude='$latitude', longitude='$longitude', location='Mugged', dropcode='0', dropcode2='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
		$page = "<table width='100%' border='1'><tr><td class=\"title\"><img src=\"images/title_exploring.gif\" alt=\"Exploring\" /></td></tr></table><p>You are exploring the mighty battle field, and something or someone jumped out and mugged you! <font color=red>You have lost <b>$gold</b> gold.</font><p>Be aware of whats's approaching you next... As you struggle to see through the haze of dust, from the sand.";
		display($page, "You were Mugged!");
		die();
	}
   
    if ($run == 3)
	list($latitude, $longitude) = move_and_check($latitude, $longitude, $x_delta, $y_delta);

   if ($run == 1) { //If run is off
   $chancetofight = rand(1,14); // 3 in 14 chance for monster spawn, 1 in 14 chance for treasure (take away the 13 from 14 = 1)
   if ($chancetofight <= 3) {
       $action = "currentaction='Fighting', currentfight='1',";
   } elseif ($chancetofight == 13) {
       $action = "currentaction='Treasure',";
   } elseif (rand(1,99) == 81) {
       $action = "currentaction='Oasis',";
   } elseif (rand(1,83) == 79) {
       $action = "currentaction='Quicksand',";
   } elseif (rand(1,26) == 23) {
       $action = "currentaction='Corpse',";
   } elseif (rand(1,131) == 117) {
       $action = "currentaction='Outside Cave',";
   } else {
       $action = "currentaction='Exploring',";
   }
   
   } elseif ($run == 3) { //If run is on
   $chancetofight = rand(1,14); // 3 in 14 chance for monster spawn, 1 in 14 chance for treasure (take away the 13 from 14 = 1)
   if ($chancetofight <= 4) {
       $action = "currentaction='Fighting', currentfight='1',";
   } elseif ($chancetofight == 11) {
       $action = "currentaction='Treasure',"; //No oasis while running
   } elseif (rand(1,63) == 67) {
       $action = "currentaction='Quicksand',";
   } elseif (rand(1,89) == 81) {
       $action = "currentaction='Outside Cave',";
   } elseif (rand(1,36) == 23) {
       $action = "currentaction='Corpse',";    
   } else {
       $action = "currentaction='Exploring',";
   }
   }
    
    $updatequery = doquery("UPDATE {{table}} SET $action latitude='$latitude', longitude='$longitude', currentfat=currentfat+$run,templist='0', location='Exploring', dropcode='0', dropcode2='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
    header("Location: index.php");
    

}



function oasis() { // Oasis
    
     global $userrow, $numqueries;

$userquery = doquery("SELECT * FROM {{table}} WHERE id='".$userrow["id"]."' LIMIT 1", "users");
if ($userrow["currentaction"] != "Oasis") {header("Location: index.php"); die(); }

    $userrow = mysql_fetch_array($userquery);

  $updatequery = doquery("UPDATE {{table}} SET currentaction='Oasis', location='Oasis' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
       
    if (isset($_POST["submit"])) {
 
        $query = doquery("UPDATE {{table}} SET currenthp='".$userrow["maxhp"]."',currenttp='".$userrow["maxtp"]."' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Oasis";
        $page = "<table width='100%' border='1'><tr><td class='title'>Oasis - Refreshed</td></tr></table><p>";
        $page .= "You feel refreshed and ready to fight those Monsters once again, with all current stats to maximum capacity.<br /><br />You may return to the <a href=\"index.php?do=oasis\">oasis</a>, or use the compass on the right to continue exploring.";
        
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
        
        $title = "Oasis";

        $page = "<table width='100%' border='1'><tr><td class='title'>Oasis</td></tr></table><p>";
        $page .= "Resting at this Oasis will refill your current HP and TP to their maximum levels and it will not restore MP. Not to mention, that it is totally free.\n";
        $page .= "<p>Is that ok?<br /><br />\n";
        $page .= "<form action=\"index.php?do=oasis\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Restore\" /><p>If you have changed your mind you may return to <a href=\"index.php?do=move:0\">exploring</a>.\n";
        $page .= "</form>\n";
        
    }
    
    display($page, $title);
}
    
?>