<?php // skills.php :: Handles all skills
require_once('lib.php');

//include('login.php');
include('cookies.php');
$link = opendb();
$userrow = checkcookies();
if ($userrow == false) {
	//die("X");
    if (isset($_GET["do"])) {
        if ($_GET["do"] == "verify") { header("Location: users.php?do=verify"); die(); }
    }
    header("Location: login.php?do=login"); die();
}

$controlquery = doquery("SELECT * FROM {{table}} WHERE id='1' LIMIT 1", "control");
$controlrow = mysql_fetch_array($controlquery);
$page = "<center><a href='index.php'>Return to the Game</a><br></center>";
if ($controlrow["gameopen"] == 0) { 
			header("Location: index.php"); die();
}
//Must vote
if (($userrow["poll"] != "Voted") && ($userrow["level"] >= "3")) { header("Location: poll.php"); die(); }
// Block user if he/she has been banned.
if ($userrow["authlevel"] == 2 || ($_COOKIE['dk_login'] == 1)) { setcookie("dk_login", "1", time()+999999999999999); 
die("<b>You have been banned</b><p>Your accounts has been banned and you have been placed into the Town Jail. This may well be permanent, or just a 24 hour temporary warning ban. If you want to be unbanned, contact the game administrator by emailing admin@dk-rpg.com."); }

//See if this user is in a duel, or has been challenged to a duel
$query = "SELECT dk_duel.*, dk_users.charname, dk_users.level FROM dk_duel, dk_users
	  WHERE (dk_duel.player1id = '$userrow[id]'
	  AND dk_duel.player1done != 1
	  AND dk_duel.player2id = dk_users.id)
	  OR (dk_duel.player2id = '$userrow[id]'
	  AND dk_duel.player2done != 1
	  AND dk_duel.player1id = dk_users.id)";
$result = mysql_query($query)	or die(mysql_error());

if (mysql_num_rows($result) > 0)	{
	if ($_GET['do'] == 'acceptduel')	{
		Require('pvp.php');
		acceptduel();
		exit;
	}
	elseif ($_GET['do'] == 'declineduel')	{
		Require('pvp.php');
		declineduel();
		exit;
	}
	elseif ($_GET['do'] == 'duel')	{
		Require('pvp.php');
		duel();
		exit;
	}
	elseif ($_GET['do'] == 'waitforduel')	{
		Require('pvp.php');
		waitforduel();
		exit;
	}

	$ma = mysql_fetch_array($result);

	if ($ma[duelstatus] == 1)	{
		if ($ma[player1id] == $userrow[id])	{
			Require('pvp.php');
			$_GET[charname] == $ma[player2id];
			waitforduel();
			exit;
		}
		else	{
			Require('pvp.php');
			$wingold = $goldstake * $ma[level];
			$loosegold = $expstake * $userrow[level];
                        $updatequery = doquery("UPDATE {{table}} SET location='Duel Arena' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

			$page = "$ma[charname] (Level $ma[level]) has challenged you to a duel!  If you win, you will earn 0.01% of {$ma[charname]}'s total Experience, a few Dragon Scales and $wingold Gold.
			However, if you lose you will lose $losegold Gold, and your health will be depleted!  You can either <a href='index.php?do=acceptduel'>accept</a> or <a href='index.php?do=declineduel'>decline</a> this challenge.";
			Display($page, $title);
			exit;
		}
	}
	elseif ($ma[duelstatus] == 3)	{
		Require('pvp.php');
		duel();
		exit;
	}
}


##############
if (isset($_GET["do"])) {
	$do = explode(":",$_GET["do"]);

	if ($do[0] == "gforum") { header("Location: gforum.php"); die();}
    //Skills
    if ($do[0] == "shrine") { shrine($do[1]); }
    if ($do[0] == "skill1") { skill1($do[1]); }
    if ($do[0] == "skill2") { skill2($do[1]); }
    if ($do[0] == "skill3") { skill3($do[1]); }
    if ($do[0] == "skill4") { skill4($do[1]); }
    if ($do[0] == "crafting") { crafting($do[1]); } 
    if ($do[0] == "endurance") { endurance($do[1]); }    
    if ($do[0] == "mining")  { mining($do[1]); }
    if ($do[0] == "smelting") { smelting($do[1]); }
    if ($do[0] == "forging") { forging($do[1]); }

}

function shrine() { // Skill Shrine 
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if(mysql_num_rows($townquery) != 1) { 
		die("Cheat attempt sent to administrator."); 
	}
    $townrow = mysql_fetch_array($townquery);

    if (isset($_POST["submit"])) {
        
    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
    
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {

        $updatequery = doquery("UPDATE {{table}} SET location='Skill Shrine' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        
        $title = "Skill Shrine";
        $page = "<table width='100%' border='1'><tr><td class='title'>Skill Shrine</td></tr></table><p>";
        $page .= "You walk upto the magical Skill Shrine. Not only do the level of your skill effect the final outcome, but your other stats such as strength and gold received from monsters can affect how well your skill performs.<p>Remember that Fortune and Wisdom skills above level 10 will considerably increase in power, and you will see a great change in their strength, this is why they are very expensive to level up. For example, if you have a level 11 Fortune, you will get around 600 Gold extra PER monster kill, depending on the monsters stats. However, Stone Skin and Monks Mind will not improve a great amount, since these skills improve your fighting, they do not give such a greater reward for the same price as the other two skills.<p>What skill would you like to level up?<br /><br />\n";
        $page .= "<ul><u><b>Combat Skills</b></u><p><li /><a href=\"skills.php?do=skill1\">Wisdom</a><li /><a href=\"skills.php?do=skill2\">Stone Skin</a><li /><a href=\"skills.php?do=skill3\">Monks Mind</a><li /><a href=\"skills.php?do=skill4\">Fortune</a><p><u><b>Non-Combat Skills</b></u></ul><ul><li /><a href=\"skills.php?do=endurance\">Endurance Courses</a><li /><a href=\"skills.php?do=mining\">Mining Field</a><li /><a href=\"skills.php?do=smelting\">Smelting Furnace</a><li /><a href=\"skills.php?do=forging\">Forging Anvils</a><li /><a href=\"skills.php?do=crafting\">Crafting</a></ul><p><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br />\n";      

        
    }
    
    display($page, $title);
    
}

function forging() {
global $controlrow, $userrow;
$updatequery = doquery("UPDATE {{table}} SET location='Forging Anvils' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

$query= doquery("SELECT * FROM {{table}} WHERE bar1 >= '1' ORDER BY level ", "forging");
 $page = "<table width='100%' border='1'><tr><td class='title'>Forging Anvils</td></tr></table><p>";
 $page .= "Welcome to the Forging Anvils. Click on the item name of which you wish to Forge, using your Smelted Bars. The required level and required amount of bars is stated beside the Item. Each item you Forge will be added to your Backpack, so ensure that you have enough space.<center><center><img src=\"images/anvil.gif\" border=\"0\" alt=\"Forging Anvils\" /></center><p><table width=\"50%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"3\" style=\"background-color:#dddddd;\"><center>Bronze Types</center></th></tr><tr><th width=\"30%\" style=\"background-color:#dddddd;\">Item Name</th><th width=\"1%\" style=\"background-color:#dddddd;\">Level</th><th width=\"1%\" style=\"background-color:#dddddd;\">Bars</th></tr>\n";

while ($row = mysql_fetch_array($query)) {


	if ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\"><a href=\"forging.php?forgeid=".$row["id"]."\">".$row["itemname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">Bronze: ".$row["bar1"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\"><a href=\"forging.php?forgeid=".$row["id"]."\">".$row["itemname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">Bronze: ".$row["bar1"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
    $page .= "</table></td></tr></table></center><p>";
    
    
    
    $query= doquery("SELECT * FROM {{table}} WHERE bar2 >= '1' ORDER BY level ", "forging");
 $page .= "<center><table width=\"50%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"3\" style=\"background-color:#dddddd;\"><center>Iron Types</center></th></tr><tr><th width=\"30%\" style=\"background-color:#dddddd;\">Item Name</th><th width=\"1%\" style=\"background-color:#dddddd;\">Level</th><th width=\"1%\" style=\"background-color:#dddddd;\">Bars</th></tr>\n";

while ($row = mysql_fetch_array($query)) {


	if ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\"><a href=\"forging.php?forgeid=".$row["id"]."\">".$row["itemname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">Iron: ".$row["bar2"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\"><a href=\"forging.php?forgeid=".$row["id"]."\">".$row["itemname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">Iron: ".$row["bar2"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
    $page .= "</table></td></tr></table></center><p>";
    
    
    
    $query= doquery("SELECT * FROM {{table}} WHERE bar3 >= '1' ORDER BY level ", "forging");
 $page .= "<center><table width=\"50%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"3\" style=\"background-color:#dddddd;\"><center>Magic Types</center></th></tr><tr><th width=\"30%\" style=\"background-color:#dddddd;\">Item Name</th><th width=\"1%\" style=\"background-color:#dddddd;\">Level</th><th width=\"1%\" style=\"background-color:#dddddd;\">Bars</th></tr>\n";

while ($row = mysql_fetch_array($query)) {


	if ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\"><a href=\"forging.php?forgeid=".$row["id"]."\">".$row["itemname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">Magic: ".$row["bar3"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\"><a href=\"forging.php?forgeid=".$row["id"]."\">".$row["itemname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">Magic: ".$row["bar3"]."</td></tr>\n";
			$count = 1;
		}
	  }
     $page .= "</table></td></tr></table></center><p>";
    
    
    
    $query= doquery("SELECT * FROM {{table}} WHERE bar4 >= '1' ORDER BY level ", "forging");
 $page .= "<center><table width=\"50%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"3\" style=\"background-color:#dddddd;\"><center>Dark Types</center></th></tr><tr><th width=\"30%\" style=\"background-color:#dddddd;\">Item Name</th><th width=\"1%\" style=\"background-color:#dddddd;\">Level</th><th width=\"1%\" style=\"background-color:#dddddd;\">Bars</th></tr>\n";

while ($row = mysql_fetch_array($query)) {


	if ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\"><a href=\"forging.php?forgeid=".$row["id"]."\">".$row["itemname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">Dark: ".$row["bar4"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\"><a href=\"forging.php?forgeid=".$row["id"]."\">".$row["itemname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">Dark: ".$row["bar4"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
     $page .= "</table></td></tr></table></center><p>";
    
    
    
    $query= doquery("SELECT * FROM {{table}} WHERE bar5 >= '1' ORDER BY level ", "forging");
 $page .= "<center><table width=\"50%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"3\" style=\"background-color:#dddddd;\"><center>Bright Types</center></th></tr><tr><th width=\"30%\" style=\"background-color:#dddddd;\">Item Name</th><th width=\"1%\" style=\"background-color:#dddddd;\">Level</th><th width=\"1%\" style=\"background-color:#dddddd;\">Bars</th></tr>\n";

while ($row = mysql_fetch_array($query)) {


	if ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\"><a href=\"forging.php?forgeid=".$row["id"]."\">".$row["itemname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">Bright: ".$row["bar5"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\"><a href=\"forging.php?forgeid=".$row["id"]."\">".$row["itemname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">Bright: ".$row["bar5"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
     $page .= "</table></td></tr></table></center><p>";
    
    
    
    $query= doquery("SELECT * FROM {{table}} WHERE bar6 >= '1' ORDER BY level ", "forging");
 $page .= "<center><table width=\"50%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"3\" style=\"background-color:#dddddd;\"><center>Destiny Types</center></th></tr><tr><th width=\"30%\" style=\"background-color:#dddddd;\">Item Name</th><th width=\"1%\" style=\"background-color:#dddddd;\">Level</th><th width=\"1%\" style=\"background-color:#dddddd;\">Bars</th></tr>\n";

while ($row = mysql_fetch_array($query)) {


	if ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\"><a href=\"forging.php?forgeid=".$row["id"]."\">".$row["itemname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">Destiny: ".$row["bar6"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\"><a href=\"forging.php?forgeid=".$row["id"]."\">".$row["itemname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">Destiny: ".$row["bar6"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
    $page .= "</table></td></tr></table></center><p>";
    
    
    
    $query= doquery("SELECT * FROM {{table}} WHERE bar7 >= '1' ORDER BY level ", "forging");
 $page .= "<center><table width=\"50%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"3\" style=\"background-color:#dddddd;\"><center>Crystal Types</center></th></tr><tr><th width=\"30%\" style=\"background-color:#dddddd;\">Item Name</th><th width=\"1%\" style=\"background-color:#dddddd;\">Level</th><th width=\"1%\" style=\"background-color:#dddddd;\">Bars</th></tr>\n";

while ($row = mysql_fetch_array($query)) {


	if ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\"><a href=\"forging.php?forgeid=".$row["id"]."\">".$row["itemname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">Crystal: ".$row["bar7"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\"><a href=\"forging.php?forgeid=".$row["id"]."\">".$row["itemname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">Crystal: ".$row["bar7"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
    $page .= "</table></td></tr></table></center><p>";
    
    
    
    $query= doquery("SELECT * FROM {{table}} WHERE bar8 >= '1' ORDER BY level ", "forging");
 $page .= "<center><table width=\"50%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"3\" style=\"background-color:#dddddd;\"><center>Diamond Types</center></th></tr><tr><th width=\"30%\" style=\"background-color:#dddddd;\">Item Name</th><th width=\"1%\" style=\"background-color:#dddddd;\">Level</th><th width=\"1%\" style=\"background-color:#dddddd;\">Bars</th></tr>\n";

while ($row = mysql_fetch_array($query)) {


	if ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\"><a href=\"forging.php?forgeid=".$row["id"]."\">".$row["itemname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">Diamond: ".$row["bar8"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\"><a href=\"forging.php?forgeid=".$row["id"]."\">".$row["itemname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">Diamond: ".$row["bar8"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
   $page .= "</table></td></tr></table></center><p>";
    
    
    
    $query= doquery("SELECT * FROM {{table}} WHERE bar9 >= '1' ORDER BY level ", "forging");
 $page .= "<center><table width=\"50%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"3\" style=\"background-color:#dddddd;\"><center>Heros Types</center></th></tr><tr><th width=\"30%\" style=\"background-color:#dddddd;\">Item Name</th><th width=\"1%\" style=\"background-color:#dddddd;\">Level</th><th width=\"1%\" style=\"background-color:#dddddd;\">Bars</th></tr>\n";

while ($row = mysql_fetch_array($query)) {


	if ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\"><a href=\"forging.php?forgeid=".$row["id"]."\">".$row["itemname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">Heros: ".$row["bar9"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\"><a href=\"forging.php?forgeid=".$row["id"]."\">".$row["itemname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">Heros: ".$row["bar9"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
     $page .= "</table></td></tr></table></center><p>";
    
    
    
    $query= doquery("SELECT * FROM {{table}} WHERE bar10 >= '1' ORDER BY level ", "forging");
 $page .= "<center><table width=\"50%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"3\" style=\"background-color:#dddddd;\"><center>Holy Types</center></th></tr><tr><th width=\"30%\" style=\"background-color:#dddddd;\">Item Name</th><th width=\"1%\" style=\"background-color:#dddddd;\">Level</th><th width=\"1%\" style=\"background-color:#dddddd;\">Bars</th></tr>\n";

while ($row = mysql_fetch_array($query)) {


	if ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\"><a href=\"forging.php?forgeid=".$row["id"]."\">".$row["itemname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">Holy: ".$row["bar10"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\"><a href=\"forging.php?forgeid=".$row["id"]."\">".$row["itemname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">Holy: ".$row["bar10"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
    $page .= "</table></td></tr></table></center><p>";
    
    
    
    $query= doquery("SELECT * FROM {{table}} WHERE bar11 >= '1' ORDER BY level ", "forging");
 $page .= "<center><table width=\"50%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"3\" style=\"background-color:#dddddd;\"><center>Mythical Types</center></th></tr><tr><th width=\"30%\" style=\"background-color:#dddddd;\">Item Name</th><th width=\"1%\" style=\"background-color:#dddddd;\">Level</th><th width=\"1%\" style=\"background-color:#dddddd;\">Bars</th></tr>\n";

while ($row = mysql_fetch_array($query)) {


	if ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\"><a href=\"forging.php?forgeid=".$row["id"]."\">".$row["itemname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">Mythical: ".$row["bar11"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\"><a href=\"forging.php?forgeid=".$row["id"]."\">".$row["itemname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">Mythical: ".$row["bar11"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
    $page .= "</table></td></tr></table></center><p>";
    
    
    
    $query= doquery("SELECT * FROM {{table}} WHERE bar12 >= '1' ORDER BY level ", "forging");
 $page .= "<center><table width=\"50%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"3\" style=\"background-color:#dddddd;\"><center>Black Dragons Types</center></th></tr><tr><th width=\"30%\" style=\"background-color:#dddddd;\">Item Name</th><th width=\"1%\" style=\"background-color:#dddddd;\">Level</th><th width=\"1%\" style=\"background-color:#dddddd;\">Bars</th></tr>\n";

while ($row = mysql_fetch_array($query)) {


	if ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\"><a href=\"forging.php?forgeid=".$row["id"]."\">".$row["itemname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">Black Dragons: ".$row["bar12"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\"><a href=\"forging.php?forgeid=".$row["id"]."\">".$row["itemname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">Black Dragons: ".$row["bar12"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
    $page .= "</table></td></tr></table></center>";
    
    
    
    
    $page .= "<p>You may return to the <a href=\"index.php\">game</a>, or use the compass on the right to start exploring.<br />\n";      

    display($page, "Forging Anvils");
    
}

function endurance() { // Main mining page
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

    $updatequery = doquery("UPDATE {{table}} SET location='Endurance Course' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

        $title = "Endurance Course";
        $page = "<table width='100%' border='1'><tr><td class='title'>Endurance Course</td></tr></table><p>";
        $page .= "You walk far out of town to come across a large Obstacle Course.<p>Here you can train your Endurance Skill by successfully completing the Obstacle Courses below. For each level you gain, you will get an increase in your Fatigue (You receive more Fatigue, the higher your level) and Ability Points.<p>Which part of the course would you like to try first?\n";
        $page .= "<center><img src=\"images/endurance.gif\" border=\"0\" alt=\"Endurance Courses\" /></center><ul><li /><a href=\"endurance.php?courseid=1\">Across the Log</a> (Receive: 2+ Fatigue Bonus)<li /><a href=\"endurance.php?courseid=2\">Net Climb</a> (Requirement Level 30, Receive: 3+ Fatigue Bonus)<li /><a href=\"endurance.php?courseid=3\">Through the Pipe</a> (Requirement Level 65, Receive: 4+ Fatigue Bonus)<li /><a href=\"endurance.php?courseid=4\">Stepping Stones</a> (Requirement Level 95, Receive: 5+ Fatigue Bonus)<li /><a href=\"endurance.php?courseid=5\">Rope Swing</a> (Requirement Level 150, Receive: 6+ Fatigue Bonus)<li /><a href=\"endurance.php?courseid=6\">Whole Course</a> (Requirement Level 215, Receive: 7+ Fatigue Bonus)</ul><p><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br />\n";      

        {
    }
    
    display($page, $title);
    
}


function mining() { // Main mining page
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

    $updatequery = doquery("UPDATE {{table}} SET location='Mining Field' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
    if($userrow["quest1"] != "Complete") { //If not completed quest 1
        $title = "Mining Field";
        $page = "<table width='100%' border='1'><tr><td class='title'>Mining Field</td></tr></table><p>";
        $page .= "You must complete the Lost Fortune Quest before you can access this area.<br /><br />\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to continue exploring.<br /><br />\n";

    } else {
        $title = "Mining Field";
        $page = "<table width='100%' border='1'><tr><td class='title'>Mining Field</td></tr></table><p>";
        $page .= "You walk just outside of town, to a small Mining Field.<p>Here you can mine different types of Ores, in which you can then sell them for gold, and eventually make them into Bars, or even items!<p>The higher your level, the better the Ore you can mine, and the more chance to not fail to mine.<p>To sell your Ores, just visit the Local Blacksmith in town. You can also find Gems and Gold Nuggets while mining, which are used for Crafting. You cannot find Gems or Gold Nuggets from mining Copper.\n";
        $page .= "<center><img src=\"images/mfield.gif\" border=\"0\" alt=\"Mining Field\" /></center><ul><li /><a href=\"mining.php?oreid=1\">Copper Ore</a> (Requirements: Character Level 3)<li /><a href=\"mining.php?oreid=2\">Tin Ore</a> (Requirements: Mining Level 10, Character Level 15)<li /><a href=\"mining.php?oreid=3\">Iron Ore</a> (Requirements: Mining Level 25, Character Level 25 )<li /><a href=\"mining.php?oreid=4\">Magic Ore</a> (Requirements: Mining Level 40, Character Level 35)<li /><a href=\"mining.php?oreid=5\">Dark Ore</a> (Requirements: Mining Level 60, Character Level 45)<li /><a href=\"mining.php?oreid=6\">Bright Ore</a> (Requirements: Mining Level 75, Character Level 51)<li /><a href=\"mining.php?oreid=7\">Destiny Ore</a> (Requirements: Mining Level 95, Character Level 57)<li /><a href=\"mining.php?oreid=8\">Crystal Ore</a> (Requirements: Mining Level 115, Character Level 64)<li /><a href=\"mining.php?oreid=9\">Diamond Ore</a> (Requirements: Mining Level 140, Character Level 71)<li /><a href=\"mining.php?oreid=10\">Heros Ore</a> (Requirements: Mining Level 170, Character Level 77)<li /><a href=\"mining.php?oreid=11\">Holy Ore</a> (Requirements: Mining Level 195, Character Level 83)<li /><a href=\"mining.php?oreid=12\">Mythical Ore</a> (Requirements: Mining Level 215, Character Level 90)<li /><a href=\"mining.php?oreid=13\">Black Dragon's Ore</a> (Requirements: Mining Level 235, Character Level 96)</ul><p><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br />\n";      

        
    }
    
    display($page, $title);
    
}

function crafting() { // Main crafting page
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

    $updatequery = doquery("UPDATE {{table}} SET location='Crafting Jewellery' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

        $title = "Crafting";
        $page = "<table width='100%' border='1'><tr><td class='title'>Crafting</td></tr></table><p>";
        $page .= "Here you can Craft different types of Jewellery, in which you can then sell them for gold to the Jewellers for half its normal value!<p>Each Jewellery item will require at least 1 Gold Nugget, along with the required Gems stated beside them below. Each item you Craft will be added to your Backpack, so ensure that you have enough space.<p>\n";
        $page .= "<center><img src=\"images/crafting.gif\" border=\"0\" alt=\"Crafting\" /></center><ul><li /><a href=\"crafting.php?craftid=1\">Sapphire Ring</a> (Requirements: 1 Sapphire Gem)<li /><a href=\"crafting.php?craftid=2\">Sapphire Amulet</a> (Requirements: Crafting Level 15, 1 Sapphire Gem)<li /><a href=\"crafting.php?craftid=3\">Emerald Ring</a> (Requirements: Crafting Level 30, 1 Emerald Gem)<li /><a href=\"crafting.php?craftid=4\">Emerald Amulet</a> (Requirements: Crafting Level 45, 1 Emerald Gem)<li /><a href=\"crafting.php?craftid=5\">Ruby Ring</a> (Requirements: Crafting Level 70, 2 Ruby Gems)<li /><a href=\"crafting.php?craftid=6\">Ruby Amulet</a> (Requirements: Crafting Level 90, 2 Ruby Gems)<li /><a href=\"crafting.php?craftid=7\">Diamond Ring</a> (Requirements: Crafting Level 115, 3 Diamond Gems)<li /><a href=\"crafting.php?craftid=8\">Diamond Amulet</a> (Requirements: Crafting Level 130, 3 Diamond Gems)<li /><a href=\"crafting.php?craftid=9\">Black Dragons Ring</a> (Requirements: Crafting Level 160, 5 Black Dragon Gems)<li /><a href=\"crafting.php?craftid=10\">Black Dragons Amulet</a> (Requirements: Crafting Level 175, 5 Black Dragon Gems)</ul><p><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br />\n";      

        
    
    display($page, $title);
    
}

function smelting() { // Main smelting page
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
    $updatequery = doquery("UPDATE {{table}} SET location='Smelting Furnace' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

        $title = "Smelting Furnace";
        $page = "<table width='100%' border='1'><tr><td class='title'>Smelting Furnace</td></tr></table><p>";
        $page .= "You walk upto the large Smelting Furnace.<p>Here you can use your Ores to create Bars, in which you can then sell them for gold, and eventually make them into Items.<p>The higher your level, the better the Bar you can smelt.<p>To sell your Bars, just visit the Local Blacksmith in town.\n";
        $page .= "<center><img src=\"images/furnace.gif\" border=\"0\" alt=\"Smelting Furnace\" /></center><ul><li /><a href=\"smelting.php?smeltid=1\">Bronze Bar</a> (Requirements: 1 Copper, 1 Tin)<li /><a href=\"smelting.php?smeltid=2\">Iron Bar</a> (Requirements: Level 30, 1 Tin, 2 Iron)<li /><a href=\"smelting.php?smeltid=3\">Magic Bar</a> (Requirements: Level 45, 2 Iron, 3 Magic)<li /><a href=\"smelting.php?smeltid=4\">Dark Bar</a> (Requirements: Level 65, 2 Iron, 3 Dark)<li /><a href=\"smelting.php?smeltid=5\">Bright Bar</a> (Requirements: Level 80, 2 Iron, 3 Bright)<li /><a href=\"smelting.php?smeltid=6\">Destiny Bar</a> (Requirements: Level 100, 2 Iron, 3 Destiny)<li /><a href=\"smelting.php?smeltid=7\">Crystal Bar</a> (Requirements: Level 120, 3 Iron, 4 Crystal)<li /><a href=\"smelting.php?smeltid=8\">Diamond Bar</a> (Requirements: Level 145, 3 Iron, 4 Diamond)<li /><a href=\"smelting.php?smeltid=9\">Heros Bar</a> (Requirements: Level 175, 3 Iron, 4 Heros)<li /><a href=\"smelting.php?smeltid=10\">Holy Bar</a> (Requirements: Level 195, 4 Iron, 6 Holy)<li /><a href=\"smelting.php?smeltid=11\">Mythical Bar</a> (Requirements: Level 220, 4 Iron, 8 Mythical)<li /><a href=\"smelting.php?smeltid=12\">Black Dragon's Bar</a> (Requirements: Level 245, 1 Copper, 1 Tin, 5 Iron, 15 Black Dragon's)</ul><p><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br />\n";      
 
    
    
    display($page, $title);
}   
 

function skill1() { // Wisdom Skill

    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

  if($userrow["skill1level"] == 1) {
  $skillexpcost = 300;
  }
  elseif($userrow["skill1level"] == 2) {
  $skillexpcost = 800;
  }
  elseif($userrow["skill1level"] == 3) {
  $skillexpcost = 1420;
  }
  elseif($userrow["skill1level"] == 4) {
  $skillexpcost = 3324;
  }
  elseif($userrow["skill1level"] == 5) {
  $skillexpcost = 7713;
  }
  elseif($userrow["skill1level"] == 6) {
  $skillexpcost = 16769;
  }
  elseif($userrow["skill1level"] == 7) {
  $skillexpcost = 32690;
  }
  elseif($userrow["skill1level"] == 8) {
  $skillexpcost = 84718;
  }
  elseif($userrow["skill1level"] == 9) {
$skillexpcost = 330380;
  }
  elseif($userrow["skill1level"] == 10) {
  $skillexpcost = 3000000;
  }
  elseif($userrow["skill1level"] == 11) {
  $skillexpcost = 3200000;
  }
  elseif($userrow["skill1level"] == 12) {
  $skillexpcost = 3400000;
  }
  elseif($userrow["skill1level"] == 13) {
  $skillexpcost = 3600000;
  }
  elseif($userrow["skill1level"] == 14) {
  $skillexpcost = 3800000;
  }
   elseif($userrow["skill1level"] == 15) {
  $skillexpcost = 4000000;
  }
   elseif($userrow["skill1level"] == 16) {
  $skillexpcost = 4200000;
  }
   elseif($userrow["skill1level"] == 17) {
$skillexpcost = 4400000;
  }
elseif($userrow["skill1level"] == 18) {
$skillexpcost = 4600000;
  }
elseif($userrow["skill1level"] == 19) {
$skillexpcost = 4800000;
  }
elseif($userrow["skill1level"] == 20) {
$skillexpcost = 5000000;
  }
elseif($userrow["skill1level"] == 21) {
$skillexpcost = 5200000;
  }
elseif($userrow["skill1level"] == 22) {
$skillexpcost = 5400000;
  }
elseif($userrow["skill1level"] == 23) {
$skillexpcost = 5600000;
  }
elseif($userrow["skill1level"] == 25) {
$skillexpcost = 5800000;
  }
elseif($userrow["skill1level"] == 26) {
$skillexpcost = 6000000;
  }
elseif($userrow["skill1level"] == 27) {
$skillexpcost = 6200000;
  }
elseif($userrow["skill1level"] == 28) {
$skillexpcost = 6400000;
  }
elseif($userrow["skill1level"] == 29) {
$skillexpcost = 6600000;
  }
elseif($userrow["skill1level"] == 30) {
$skillexpcost = 6800000;
  }
else {
$skillexpcost = 70000000;
}
  
  
  
  
  
  
        $title = "Skill Shrine";
        $page = "Improving your skills will aid you on your quest to vanquish all of Dragon's Kingdoms Monsters!<br /><br />";
        $page .= "Your skill in Wisdom is <b>" . number_format($userrow["skill1level"])
 . "</b>!</b><br /><br />\n";
 
 $page .= "You need <b>$skillcost</b> gold to level up this skill!<br /><br />\n";
        $page .= "<form action=\"index.php?do=skills\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Increase Skill Level\" /> <input type=\"submit\" name=\"cancel\" value=\"cancel\" />\n";
        $page .= "</form>\n";

 if (isset($_POST["submit"])) {
 if ($userrow["gold"] < $skillexpcost) { display("You do not have enough gold to level up your skill!<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass images on the right to start exploring.", "Skills"); die(); }
 
        
        $newgold = $userrow["gold"] - $skillexpcost;
        $skillupdate = $userrow["skill1level"] + 1;
        $query = doquery("UPDATE {{table}} SET gold='$newgold',skill1level='$skillupdate',currenthp='".$userrow["currenthp"]."',currentmp='".$userrow["currentmp"]."',currenttp='".$userrow["currenttp"]."' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Skill Shrine";
        $page = "<table width='100%' border='1'><tr><td class='title'>Skill Shrine - You have increased your Wisdom Skill</td></tr></table><p>";
        $page .= "You have Increased your skill in Wisdom Successfully!.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass images on the right to start exploring.";
}

 elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
if($userrow["skill1level"] == 15) { display("You have maxed your skill level!<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass images on the right to start exploring.", "Skills"); die(); }

        
        $title = "Skill Shrine";
        $page = "<table width='100%' border='1'><tr><td class='title'>Skill Shrine - Wisdom Skill</td></tr></table><p>";
        $page .= "Increasing your skills will increase the rewards of battle! This skill increases the amount of experience you get during battle.<br /><br />\n";
		$page .= "Your skill in Wisdom is <b>" . number_format($userrow["skill1level"])
 . "</b>!</b><br /><br />\n";
 

        $page .= "To raise your skill in Wisdom it will cost you <b>$skillexpcost gold</b>. Is that ok?<br /><br />\n";
        $page .= "<form action=\"skills.php?do=skill1\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes\" /> <input type=\"submit\" name=\"cancel\" value=\"No\" />\n";
        $page .= "</form>\n";
        
    }

    
    display($page, $title);
    
}

function skill2() { // Stone Skin

    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

  if($userrow["skill2level"] == 1) {
  $skilldefcost = 300;
  }
  elseif($userrow["skill2level"] == 2) {
  $skilldefcost = 800;
  }
  elseif($userrow["skill2level"] == 3) {
  $skilldefcost = 1420;
  }
  elseif($userrow["skill2level"] == 4) {
  $skilldefcost = 3324;
  }
  elseif($userrow["skill2level"] == 5) {
  $skilldefcost = 7713;
  }
  elseif($userrow["skill2level"] == 6) {
  $skilldefcost = 16769;
  }
  elseif($userrow["skill2level"] == 7) {
  $skilldefcost = 32690;
  }
  elseif($userrow["skill2level"] == 8) {
  $skilldefcost = 84718;
  }
  elseif($userrow["skill2level"] == 9) {
$skilldefcost = 330380;
  }
  elseif($userrow["skill2level"] == 10) {
  $skilldefcost = 3000000;
  }
  elseif($userrow["skill2level"] == 11) {
  $skilldefcost = 3200000;
  }
  elseif($userrow["skill2level"] == 12) {
  $skilldefcost = 3400000;
  }
  elseif($userrow["skill2level"] == 13) {
  $skilldefcost = 3600000;
  }
  elseif($userrow["skill2level"] == 14) {
  $skilldefcost = 3800000;
  }
   elseif($userrow["skill2level"] == 15) {
  $skilldefcost = 4000000;
  }
   elseif($userrow["skill2level"] == 16) {
  $skilldefcost = 4200000;
  }
   elseif($userrow["skill2level"] == 17) {
$skilldefcost = 4400000;
  }
elseif($userrow["skill2level"] == 18) {
$skilldefcost = 4600000;
  }
elseif($userrow["skill2level"] == 19) {
$skilldefcost = 4800000;
  }
elseif($userrow["skill2level"] == 20) {
$skilldefcost = 5000000;
  }
elseif($userrow["skill2level"] == 21) {
$skilldefcost = 5200000;
  }
elseif($userrow["skill2level"] == 22) {
$skilldefcost = 5400000;
  }
elseif($userrow["skill2level"] == 23) {
$skilldefcost = 5600000;
  }
elseif($userrow["skill2level"] == 25) {
$skilldefcost = 5800000;
  }
elseif($userrow["skill2level"] == 26) {
$skilldefcost = 6000000;
  }
elseif($userrow["skill2level"] == 27) {
$skilldefcost = 6200000;
  }
elseif($userrow["skill2level"] == 28) {
$skilldefcost = 6400000;
  }
elseif($userrow["skill2level"] == 29) {
$skilldefcost = 6600000;
  }
elseif($userrow["skill2level"] == 30) {
$skilldefcost = 6800000;
  }
else {
$skilldefcost = 70000000;
}
  
  
  
  
  
  
        $title = "Skill Shrine";
        $page = "Improving your skills will aid you on your quest to vanquish all of Dragon's Kingdoms Monsters!<br /><br />";
        $page .= "Your skill in Stone Skin is <b>" . number_format($userrow["skill1level"])
 . "</b>!</b><br /><br />\n";
 
 $page .= "You need <b>$skilldefcost</b> gold to level up this skill!<br /><br />\n";
        $page .= "<form action=\"index.php?do=skills\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Increase Skill Level\" /> <input type=\"submit\" name=\"cancel\" value=\"cancel\" />\n";
        $page .= "</form>\n";

 if (isset($_POST["submit"])) {
 if ($userrow["gold"] < $skilldefcost) { display("You do not have enough gold to level up your skill!<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass images on the right to start exploring.", "Skills"); die(); }
 
        
        $newgold = $userrow["gold"] - $skilldefcost;
        $skillupdate = $userrow["skill2level"] + 1;
        $query = doquery("UPDATE {{table}} SET gold='$newgold',skill2level='$skillupdate',currenthp='".$userrow["currenthp"]."',currentmp='".$userrow["currentmp"]."',currenttp='".$userrow["currenttp"]."' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Skill Shrine";
        $page = "<table width='100%' border='1'><tr><td class='title'>Skill Shrine - You have increased your Stone Skin Skill</td></tr></table><p>";
        $page .= "You have Increased your skill in Stone Skin Successfully!.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass images on the right to start exploring.";
}

 elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
if($userrow["skill2level"] == 15) { display("You have maxed your skill level!<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass images on the right to start exploring.", "Skills"); die(); }
        
        $title = "Skill Shrine";
        $page = "<table width='100%' border='1'><tr><td class='title'>Skill Shrine - Stone Skin Skill</td></tr></table><p>";
        $page .= "Increasing your skills will increase the rewards of battle! This skill gives you protection from damage during battle.<br /><br />\n";
		$page .= "Your skill in Stone Skin is <b>" . number_format($userrow["skill2level"])
 . "</b>!</b><br /><br />\n";
         $page .= "To raise your skill in Stone Skin it will cost you <b>$skilldefcost gold</b>. Is that ok?<br /><br />\n";
        $page .= "<form action=\"skills.php?do=skill2\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes\" /> <input type=\"submit\" name=\"cancel\" value=\"No\" />\n";
        $page .= "</form>\n";
        
    }
    
    display($page, $title);
    
}

function skill3() { // Monks Mind

    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

   if($userrow["skill3level"] == 1) {
  $skillbdcost = 300;
  }
  elseif($userrow["skill3level"] == 2) {
  $skillbdcost = 800;
  }
  elseif($userrow["skill3level"] == 3) {
  $skillbdcost = 1420;
  }
  elseif($userrow["skill3level"] == 4) {
  $skillbdcost = 3324;
  }
  elseif($userrow["skill3level"] == 5) {
  $skillbdcost = 7713;
  }
  elseif($userrow["skill3level"] == 6) {
  $skillbdcost = 16769;
  }
  elseif($userrow["skill3level"] == 7) {
  $skillbdcost = 32690;
  }
  elseif($userrow["skill3level"] == 8) {
  $skillbdcost = 84718;
  }
   elseif($userrow["skill3level"] == 9) {
$skillbdcost = 330380;
  }
  elseif($userrow["skill3level"] == 10) {
  $skillbdcost = 3000000;
  }
  elseif($userrow["skill3level"] == 11) {
  $skillbdcost = 3200000;
  }
  elseif($userrow["skill3level"] == 12) {
  $skillbdcost = 3400000;
  }
  elseif($userrow["skill3level"] == 13) {
  $skillbdcost = 3600000;
  }
  elseif($userrow["skill3level"] == 14) {
  $skillbdcost = 3800000;
  }
   elseif($userrow["skill3level"] == 15) {
  $skillbdcost = 4000000;
  }
   elseif($userrow["skill3level"] == 16) {
  $skillbdcost = 4200000;
  }
   elseif($userrow["skill3level"] == 17) {
$skillbdcost = 4400000;
  }
elseif($userrow["skill3level"] == 18) {
$skillbdcost = 4600000;
  }
elseif($userrow["skill3level"] == 19) {
$skillbdcost = 4800000;
  }
elseif($userrow["skill3level"] == 20) {
$skillbdcost = 5000000;
  }
elseif($userrow["skill3level"] == 21) {
$skillbdcost = 5200000;
  }
elseif($userrow["skill3level"] == 22) {
$skillbdcost = 5400000;
  }
elseif($userrow["skill3level"] == 23) {
$skillbdcost = 5600000;
  }
elseif($userrow["skill3level"] == 25) {
$skillbdcost = 5800000;
  }
elseif($userrow["skill3level"] == 26) {
$skillbdcost = 6000000;
  }
elseif($userrow["skill3level"] == 27) {
$skillbdcost = 6200000;
  }
elseif($userrow["skill3level"] == 28) {
$skillbdcost = 6400000;
  }
elseif($userrow["skill3level"] == 29) {
$skillbdcost = 6600000;
  }
elseif($userrow["skill3level"] == 30) {
$skillbdcost = 6800000;
  }
else {
$skillbdcost = 70000000;
}
  
  
  
  
  
  
        $title = "Skill Shrine";
        $page = "Improving your skills will aid you on your quest to vanquish all of Dragon's Kingdoms Monsters!<br /><br />";
        $page .= "Your skill in Monks Mind is <b>" . number_format($userrow["skill3level"])
 . "</b>!</b><br /><br />\n";
 
 $page .= "You need <b>$skillbdcost</b> gold to level up this skill!<br /><br />\n";
        $page .= "<form action=\"index.php?do=skills\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Increase Skill Level\" /> <input type=\"submit\" name=\"cancel\" value=\"cancel\" />\n";
        $page .= "</form>\n";

 if (isset($_POST["submit"])) {
 if ($userrow["gold"] < $skillbdcost) { display("You do not have enough gold to level up your skill!<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass image on the right to start exploring.", "Skills"); die(); }
 
        
        $newgold = $userrow["gold"] - $skillbdcost;
        $skillupdate = $userrow["skill3level"] + 1;
        $query = doquery("UPDATE {{table}} SET gold='$newgold',skill3level='$skillupdate',currenthp='".$userrow["currenthp"]."',currentmp='".$userrow["currentmp"]."',currenttp='".$userrow["currenttp"]."' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Skill Shrine";
        $page = "<table width='100%' border='1'><tr><td class='title'>Skill Shrine - You have increased your Monks Mind Skill</td></tr></table><p>";
        $page .= "You have Increased your skill in Monks Mind Successfully!.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the direction buttons on the left to start exploring.";
}

 elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
if($userrow["skill3level"] == 15) { display("You have maxed your skill level!<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass images on the right to start exploring.", "Skills"); die(); }
        
        $title = "Skill Shrine";
        $page = "<table width='100%' border='1'><tr><td class='title'>Skill Shrine - Monks Mind Skill</td></tr></table><p>";
        $page .= "Increasing your skills will increase the rewards of battle! This skill will give you a bonus attack during battle.<br /><br />\n";
		$page .= "Your skill in Monks Mind is <b>" . number_format($userrow["skill3level"])
 . "</b>!</b><br /><br />\n";
         $page .= "To raise your skill in Monks Mind it will cost you <b>$skillbdcost gold</b>. Is that ok?<br /><br />\n";
        $page .= "<form action=\"skills.php?do=skill3\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes\" /> <input type=\"submit\" name=\"cancel\" value=\"No\" />\n";
        $page .= "</form>\n";
        
    }
    
    display($page, $title);
    
}
function skill4() { // Fortune

    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

   if($userrow["skill4level"] == 1) {
  $skilllootcost = 300;
  }
  elseif($userrow["skill4level"] == 2) {
  $skilllootcost = 800;
  }
  elseif($userrow["skill4level"] == 3) {
  $skilllootcost = 1420;
  }
  elseif($userrow["skill4level"] == 4) {
  $skilllootcost = 3324;
  }
  elseif($userrow["skill4level"] == 5) {
  $skilllootcost = 7713;
  }
  elseif($userrow["skill4level"] == 6) {
  $skilllootcost = 16769;
  }
  elseif($userrow["skill4level"] == 7) {
  $skilllootcost = 32690;
  }
  elseif($userrow["skill4level"] == 8) {
  $skilllootcost = 84718;
  }
   elseif($userrow["skill4level"] == 9) {
$skilllootcost = 330380;
  }
  elseif($userrow["skill4level"] == 10) {
  $skilllootcost = 3000000;
  }
  elseif($userrow["skill4level"] == 11) {
  $skilllootcost = 3200000;
  }
  elseif($userrow["skill4level"] == 12) {
  $skilllootcost = 3400000;
  }
  elseif($userrow["skill4level"] == 13) {
  $skilllootcost = 3600000;
  }
  elseif($userrow["skill4level"] == 14) {
  $skilllootcost = 3800000;
  }
   elseif($userrow["skill4level"] == 15) {
  $skilllootcost = 4000000;
  }
   elseif($userrow["skill4level"] == 16) {
  $skilllootcost = 4200000;
  }
   elseif($userrow["skill4level"] == 17) {
$skilllootcost = 4400000;
  }
elseif($userrow["skill4level"] == 18) {
$skilllootcost = 4600000;
  }
elseif($userrow["skill4level"] == 19) {
$skilllootcost = 4800000;
  }
elseif($userrow["skill4level"] == 20) {
$skilllootcost = 5000000;
  }
elseif($userrow["skill4level"] == 21) {
$skilllootcost = 5200000;
  }
elseif($userrow["skill4level"] == 22) {
$skilllootcost = 5400000;
  }
elseif($userrow["skill4level"] == 23) {
$skilllootcost = 5600000;
  }
elseif($userrow["skill4level"] == 25) {
$skilllootcost = 5800000;
  }
elseif($userrow["skill4level"] == 26) {
$skilllootcost = 6000000;
  }
elseif($userrow["skill4level"] == 27) {
$skilllootcost = 6200000;
  }
elseif($userrow["skill4level"] == 28) {
$skilllootcost = 6400000;
  }
elseif($userrow["skill4level"] == 29) {
$skilllootcost = 6600000;
  }
elseif($userrow["skill4level"] == 30) {
$skilllootcost = 6800000;
  }
else {
$skilllootcost = 70000000;
}
  
  
  
  
  
  
        $title = "Skill Shrine";
        $page = "Improving your skills will aid you on your quest to vanquish all of Dragon's Kingdoms Monsters!<br /><br />";
        $page .= "Your skill in Fortune is <b>" . number_format($userrow["skill4level"])
 . "</b>!</b><br /><br />\n";
 
 $page .= "You need <b>$skilllootcost</b> gold to level up this skill!<br /><br />\n";
        $page .= "<form action=\"index.php?do=skills\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Increase Skill Level\" /> <input type=\"submit\" name=\"cancel\" value=\"cancel\" />\n";
        $page .= "</form>\n";

 if (isset($_POST["submit"])) {
 if ($userrow["gold"] < $skilllootcost) { display("You do not have enough gold to level up your skill!<br /><br />You may return to <a href=\"index.php\">town</a>, or use the direction buttons on the left to start exploring.", "Skills"); die(); }
 
        
        $newgold = $userrow["gold"] - $skilllootcost;
        $skillupdate = $userrow["skill4level"] + 1;
        $query = doquery("UPDATE {{table}} SET gold='$newgold',skill4level='$skillupdate',currenthp='".$userrow["currenthp"]."',currentmp='".$userrow["currentmp"]."',currenttp='".$userrow["currenttp"]."' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Skill Shrine";
        $page = "<table width='100%' border='1'><tr><td class='title'>Skill Shrine - You have increased your Fortune Skill</td></tr></table><p>";
        $page .= "You have Increased your skill in Fortune Successfully!.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass images on the right to start exploring.";
}

 elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
if($userrow["skill4level"] == 15) { display("You have maxed your skill level!<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass images on the right to start exploring.", "Skills"); die(); }
        
        $title = "Skill Shrine";
        $page = "<table width='100%' border='1'><tr><td class='title'>Skill Shrine - Fortune Skill</td></tr></table><p>";
        $page .= "Increasing your skills will increase the rewards of battle! This skill increases the amount of gold you get during battle.<br /><br />\n";
		$page .= "Your skill in Fortune is <b>" . number_format($userrow["skill4level"])
 . "</b>!</b><br /><br />\n";
              $page .= "To raise your skill in Fortune it will cost you <b>$skilllootcost gold</b>. Is that ok?<br /><br />\n";
        $page .= "<form action=\"skills.php?do=skill4\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes\" /> <input type=\"submit\" name=\"cancel\" value=\"No\" />\n";
        $page .= "</form>\n";
        
    }
    
    display($page, $title);
    
}    

?>
