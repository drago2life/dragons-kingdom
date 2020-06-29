<?php // fame.php hall of fame tables

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
//Must vote
if (($userrow["poll"] != "Voted") && ($userrow["level"] >= "3")) { header("Location: poll.php"); die(); }
if ($controlrow["gameopen"] == 0) { 
			header("Location: index.php"); die();
}
// Block user if he/she has been banned.
if ($userrow["authlevel"] == 2 || ($_COOKIE['dk_login'] == 1)) { setcookie("dk_login", "1", time()+999999999999999); 
die("<b>You have been banned</b><p>Your accounts has been banned and you have been placed into the Town Jail. This may well be permanent, or just a 24 hour temporary warning ban. If you want to be unbanned, contact the game administrator by emailing admin@dk-rpg.com."); }


##############


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

	if ($do[0] == "vault") { dovault(); }	

    elseif ($do[0] == "main") { main($do[1]); }
    elseif ($do[0] == "sorc") { sorc($do[1]); }
    elseif ($do[0] == "barb") { barb($do[1]); }
    elseif ($do[0] == "pala") { pala($do[1]); }
    elseif ($do[0] == "ranger") { ranger($do[1]); }
    elseif ($do[0] == "necro") { necro($do[1]); }
    elseif ($do[0] == "druid") { druid($do[1]); }
    elseif ($do[0] == "assn") { assn($do[1]); }
    elseif ($do[0] == "duel") { duel($do[1]); }
    elseif ($do[0] == "combat") { combat($do[1]); }
    elseif ($do[0] == "noncombat") { noncombat($do[1]); }
    elseif ($do[0] == "other") { other($do[1]); }
    elseif ($do[0] == "totals") { totals($do[1]); }


} 

function main() {
global $controlrow, $userrow;
$updatequery = doquery("UPDATE {{table}} SET location='Hall of Fame' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

$query= doquery("SELECT * FROM {{table}} ORDER BY experience DESC LIMIT 100", "users");
 $page = "<table width='100%' border='1'><tr><td class='title'>Hall of Fame</td></tr></table><p>";
  $page .= "<center>[<a href=\"fame.php?do=main\">Main Hall</a>] [<a href=\"fame.php?do=totals\">Totals</a>]  [<a href=\"fame.php?do=sorc\">Sorceress</a>] [<a href=\"fame.php?do=barb\">Barbarian</a>] [<a href=\"fame.php?do=pala\">Paladin</a>] [<a href=\"fame.php?do=ranger\">Ranger</a>] [<a href=\"fame.php?do=necro\">Necromancer</a>] [<a href=\"fame.php?do=druid\">Druid</a>]<br>[<a href=\"fame.php?do=assn\">Assassin</a>] [<a href=\"fame.php?do=duel\">Dueling</a>] [<a href=\"fame.php?do=combat\">Combat Skills</a>] [<a href=\"fame.php?do=noncombat\">Non-Combat Skills</a>] [<a href=\"fame.php?do=other\">Other</a>] or [<a href=\"index.php\">Return to Game</a>]</center><br>";
 $page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"7\" style=\"background-color:#dddddd;\"><center>Overall Top 100 Players<p>Click on a Character Name to view their Profiles</center></th></tr><tr><th width=\"1%\" style=\"background-color:#dddddd;\">Rank</th><th width=\"20%\" style=\"background-color:#dddddd;\">Character Name</th><th width=\"2%\" style=\"background-color:#dddddd;\">Class</th><th width=\"2%\" style=\"background-color:#dddddd;\">Level</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Experience</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Gold</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Banked Gold</th></tr>\n";

$count = 1;
$n=0;
while ($row = mysql_fetch_array($query)) {
$n += 1;

	    	$row["gold"] = number_format($row["gold"]);
	    	$row["bank"] = number_format($row["bank"]);
	    	$row["experience"] = number_format($row["experience"]);
     if ($row["charclass"] == 1) { $row["charclass"] = $controlrow["class1name"]; }
    elseif ($row["charclass"] == 2) { $row["charclass"] = $controlrow["class2name"]; }
    elseif ($row["charclass"] == 3) { $row["charclass"] = $controlrow["class3name"]; }
    elseif ($row["charclass"] == 4) { $row["charclass"] = $controlrow["class4name"]; }
    elseif ($row["charclass"] == 5) { $row["charclass"] = $controlrow["class5name"]; }
    elseif ($row["charclass"] == 6) { $row["charclass"] = $controlrow["class6name"]; }
    elseif ($row["charclass"] == 7) { $row["charclass"] = $controlrow["class7name"]; }
if($row["charname"] == $userrow["charname"]) {
           $page .= "<tr><td style=\"background-color:orange;\">$n</td><td style=\"background-color:orange;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:orange;\">".$row["charclass"]."</td><td style=\"background-color:orange;\">".$row["level"]."</td><td style=\"background-color:orange;\">".$row["experience"]."</td><td style=\"background-color:orange;\">".$row["gold"]."</td><td style=\"background-color:orange;\">".$row["bank"]."</td></tr>\n";
}
		elseif ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\">$n</td><td style=\"background-color:#ffffff;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["charclass"]."</td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">".$row["experience"]."</td><td style=\"background-color:#ffffff;\">".$row["gold"]."</td><td style=\"background-color:#ffffff;\">".$row["bank"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">$n</td><td style=\"background-color:#eeeeee;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["charclass"]."</td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">".$row["experience"]."</td><td style=\"background-color:#eeeeee;\">".$row["gold"]."</td><td style=\"background-color:#eeeeee;\">".$row["bank"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
    $page .= "</table></td></tr></table><hr />";
    $page .= "<p>You may return to the <a href=\"index.php\">game</a>, or use the compass on the right to start exploring.<br />\n";      
    
    display($page, "Hall of Fame");
    
}

function sorc() {
global $controlrow, $userrow;

$query= doquery("SELECT * FROM {{table}} WHERE charclass=1 ORDER BY experience DESC LIMIT 75", "users");
 $page = "<table width='100%' border='1'><tr><td class='title'>Hall of Fame</td></tr></table><p>";
  $page .= "<center>[<a href=\"fame.php?do=main\">Main Hall</a>] [<a href=\"fame.php?do=totals\">Totals</a>]  [<a href=\"fame.php?do=sorc\">Sorceress</a>] [<a href=\"fame.php?do=barb\">Barbarian</a>] [<a href=\"fame.php?do=pala\">Paladin</a>] [<a href=\"fame.php?do=ranger\">Ranger</a>] [<a href=\"fame.php?do=necro\">Necromancer</a>] [<a href=\"fame.php?do=druid\">Druid</a>]<br>[<a href=\"fame.php?do=assn\">Assassin</a>] [<a href=\"fame.php?do=duel\">Dueling</a>] [<a href=\"fame.php?do=combat\">Combat Skills</a>] [<a href=\"fame.php?do=noncombat\">Non-Combat Skills</a>] [<a href=\"fame.php?do=other\">Other</a>] or [<a href=\"index.php\">Return to Game</a>]</center><br>";
 $page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"7\" style=\"background-color:#dddddd;\"><center>Top 75 Sorceress<p>Click on a Character Name to view their Profiles</center></th></tr><tr><th width=\"1%\" style=\"background-color:#dddddd;\">Rank</th><th width=\"20%\" style=\"background-color:#dddddd;\">Character Name</th><th width=\"2%\" style=\"background-color:#dddddd;\">Class</th><th width=\"2%\" style=\"background-color:#dddddd;\">Level</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Experience</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Gold</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Banked Gold</th></tr>\n";

$count = 1;
$n=0;
while ($row = mysql_fetch_array($query)) {
$n += 1;

	    	$row["gold"] = number_format($row["gold"]);
	    	$row["bank"] = number_format($row["bank"]);
	    	$row["experience"] = number_format($row["experience"]);
     if ($row["charclass"] == 1) { $row["charclass"] = $controlrow["class1name"]; }

if($row["charname"] == $userrow["charname"]) {
           $page .= "<tr><td style=\"background-color:orange;\">$n</td><td style=\"background-color:orange;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:orange;\">".$row["charclass"]."</td><td style=\"background-color:orange;\">".$row["level"]."</td><td style=\"background-color:orange;\">".$row["experience"]."</td><td style=\"background-color:orange;\">".$row["gold"]."</td><td style=\"background-color:orange;\">".$row["bank"]."</td></tr>\n";
}
		elseif ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\">$n</td><td style=\"background-color:#ffffff;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["charclass"]."</td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">".$row["experience"]."</td><td style=\"background-color:#ffffff;\">".$row["gold"]."</td><td style=\"background-color:#ffffff;\">".$row["bank"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">$n</td><td style=\"background-color:#eeeeee;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["charclass"]."</td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">".$row["experience"]."</td><td style=\"background-color:#eeeeee;\">".$row["gold"]."</td><td style=\"background-color:#eeeeee;\">".$row["bank"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
    $page .= "</table></td></tr></table><hr />";
    $page .= "<p>You may return to the <a href=\"index.php\">game</a>, or use the compass on the right to start exploring.<br />\n";      
    
    display($page, "Hall of Fame");
    
}

function barb() {
global $controlrow, $userrow;

$query= doquery("SELECT * FROM {{table}} WHERE charclass=2 ORDER BY experience DESC LIMIT 75", "users");
 $page = "<table width='100%' border='1'><tr><td class='title'>Hall of Fame</td></tr></table><p>";
  $page .= "<center>[<a href=\"fame.php?do=main\">Main Hall</a>] [<a href=\"fame.php?do=totals\">Totals</a>]  [<a href=\"fame.php?do=sorc\">Sorceress</a>] [<a href=\"fame.php?do=barb\">Barbarian</a>] [<a href=\"fame.php?do=pala\">Paladin</a>] [<a href=\"fame.php?do=ranger\">Ranger</a>] [<a href=\"fame.php?do=necro\">Necromancer</a>] [<a href=\"fame.php?do=druid\">Druid</a>]<br>[<a href=\"fame.php?do=assn\">Assassin</a>] [<a href=\"fame.php?do=duel\">Dueling</a>] [<a href=\"fame.php?do=combat\">Combat Skills</a>] [<a href=\"fame.php?do=noncombat\">Non-Combat Skills</a>] [<a href=\"fame.php?do=other\">Other</a>] or [<a href=\"index.php\">Return to Game</a>]</center><br>";
 $page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"7\" style=\"background-color:#dddddd;\"><center>Top 75 Barbarian<p>Click on a Character Name to view their Profiles</center></th></tr><tr><th width=\"1%\" style=\"background-color:#dddddd;\">Rank</th><th width=\"20%\" style=\"background-color:#dddddd;\">Character Name</th><th width=\"2%\" style=\"background-color:#dddddd;\">Class</th><th width=\"2%\" style=\"background-color:#dddddd;\">Level</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Experience</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Gold</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Banked Gold</th></tr>\n";

$count = 1;
$n=0;
while ($row = mysql_fetch_array($query)) {
$n += 1;

	    	$row["gold"] = number_format($row["gold"]);
	    	$row["bank"] = number_format($row["bank"]);
	    	$row["experience"] = number_format($row["experience"]);
     if ($row["charclass"] == 2) { $row["charclass"] = $controlrow["class2name"]; }

if($row["charname"] == $userrow["charname"]) {
           $page .= "<tr><td style=\"background-color:orange;\">$n</td><td style=\"background-color:orange;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:orange;\">".$row["charclass"]."</td><td style=\"background-color:orange;\">".$row["level"]."</td><td style=\"background-color:orange;\">".$row["experience"]."</td><td style=\"background-color:orange;\">".$row["gold"]."</td><td style=\"background-color:orange;\">".$row["bank"]."</td></tr>\n";
}
		elseif ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\">$n</td><td style=\"background-color:#ffffff;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["charclass"]."</td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">".$row["experience"]."</td><td style=\"background-color:#ffffff;\">".$row["gold"]."</td><td style=\"background-color:#ffffff;\">".$row["bank"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">$n</td><td style=\"background-color:#eeeeee;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["charclass"]."</td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">".$row["experience"]."</td><td style=\"background-color:#eeeeee;\">".$row["gold"]."</td><td style=\"background-color:#eeeeee;\">".$row["bank"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
    $page .= "</table></td></tr></table><hr />";
    $page .= "<p>You may return to the <a href=\"index.php\">game</a>, or use the compass on the right to start exploring.<br />\n";      
    
    display($page, "Hall of Fame");
    
}

function pala() {
global $controlrow, $userrow;

$query= doquery("SELECT * FROM {{table}} WHERE charclass=3 ORDER BY experience DESC LIMIT 75", "users");
 $page = "<table width='100%' border='1'><tr><td class='title'>Hall of Fame</td></tr></table><p>";
  $page .= "<center>[<a href=\"fame.php?do=main\">Main Hall</a>] [<a href=\"fame.php?do=totals\">Totals</a>]  [<a href=\"fame.php?do=sorc\">Sorceress</a>] [<a href=\"fame.php?do=barb\">Barbarian</a>] [<a href=\"fame.php?do=pala\">Paladin</a>] [<a href=\"fame.php?do=ranger\">Ranger</a>] [<a href=\"fame.php?do=necro\">Necromancer</a>] [<a href=\"fame.php?do=druid\">Druid</a>]<br>[<a href=\"fame.php?do=assn\">Assassin</a>] [<a href=\"fame.php?do=duel\">Dueling</a>] [<a href=\"fame.php?do=combat\">Combat Skills</a>] [<a href=\"fame.php?do=noncombat\">Non-Combat Skills</a>] [<a href=\"fame.php?do=other\">Other</a>] or [<a href=\"index.php\">Return to Game</a>]</center><br>";
 $page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"7\" style=\"background-color:#dddddd;\"><center>Top 75 Paladin<p>Click on a Character Name to view their Profiles</center></th></tr><tr><th width=\"1%\" style=\"background-color:#dddddd;\">Rank</th><th width=\"20%\" style=\"background-color:#dddddd;\">Character Name</th><th width=\"2%\" style=\"background-color:#dddddd;\">Class</th><th width=\"2%\" style=\"background-color:#dddddd;\">Level</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Experience</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Gold</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Banked Gold</th></tr>\n";

$count = 1;
$n=0;
while ($row = mysql_fetch_array($query)) {
$n += 1;

	    	$row["gold"] = number_format($row["gold"]);
	    	$row["bank"] = number_format($row["bank"]);
	    	$row["experience"] = number_format($row["experience"]);
     if ($row["charclass"] == 3) { $row["charclass"] = $controlrow["class3name"]; }

if($row["charname"] == $userrow["charname"]) {
           $page .= "<tr><td style=\"background-color:orange;\">$n</td><td style=\"background-color:orange;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:orange;\">".$row["charclass"]."</td><td style=\"background-color:orange;\">".$row["level"]."</td><td style=\"background-color:orange;\">".$row["experience"]."</td><td style=\"background-color:orange;\">".$row["gold"]."</td><td style=\"background-color:orange;\">".$row["bank"]."</td></tr>\n";
}
		elseif ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\">$n</td><td style=\"background-color:#ffffff;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["charclass"]."</td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">".$row["experience"]."</td><td style=\"background-color:#ffffff;\">".$row["gold"]."</td><td style=\"background-color:#ffffff;\">".$row["bank"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">$n</td><td style=\"background-color:#eeeeee;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["charclass"]."</td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">".$row["experience"]."</td><td style=\"background-color:#eeeeee;\">".$row["gold"]."</td><td style=\"background-color:#eeeeee;\">".$row["bank"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
    $page .= "</table></td></tr></table><hr />";
    $page .= "<p>You may return to the <a href=\"index.php\">game</a>, or use the compass on the right to start exploring.<br />\n";      
    
    display($page, "Hall of Fame");
    
}

function ranger() {
global $controlrow, $userrow;

$query= doquery("SELECT * FROM {{table}} WHERE charclass=4 ORDER BY experience DESC LIMIT 75", "users");
 $page = "<table width='100%' border='1'><tr><td class='title'>Hall of Fame</td></tr></table><p>";
  $page .= "<center>[<a href=\"fame.php?do=main\">Main Hall</a>] [<a href=\"fame.php?do=totals\">Totals</a>]  [<a href=\"fame.php?do=sorc\">Sorceress</a>] [<a href=\"fame.php?do=barb\">Barbarian</a>] [<a href=\"fame.php?do=pala\">Paladin</a>] [<a href=\"fame.php?do=ranger\">Ranger</a>] [<a href=\"fame.php?do=necro\">Necromancer</a>] [<a href=\"fame.php?do=druid\">Druid</a>]<br>[<a href=\"fame.php?do=assn\">Assassin</a>] [<a href=\"fame.php?do=duel\">Dueling</a>] [<a href=\"fame.php?do=combat\">Combat Skills</a>] [<a href=\"fame.php?do=noncombat\">Non-Combat Skills</a>] [<a href=\"fame.php?do=other\">Other</a>] or [<a href=\"index.php\">Return to Game</a>]</center><br>";
 $page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"7\" style=\"background-color:#dddddd;\"><center>Top 75 Ranger<p>Click on a Character Name to view their Profiles</center></th></tr><tr><th width=\"1%\" style=\"background-color:#dddddd;\">Rank</th><th width=\"20%\" style=\"background-color:#dddddd;\">Character Name</th><th width=\"2%\" style=\"background-color:#dddddd;\">Class</th><th width=\"2%\" style=\"background-color:#dddddd;\">Level</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Experience</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Gold</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Banked Gold</th></tr>\n";

$count = 1;
$n=0;
while ($row = mysql_fetch_array($query)) {
$n += 1;

	    	$row["gold"] = number_format($row["gold"]);
	    	$row["bank"] = number_format($row["bank"]);
	    	$row["experience"] = number_format($row["experience"]);
     if ($row["charclass"] == 4) { $row["charclass"] = $controlrow["class4name"]; }

if($row["charname"] == $userrow["charname"]) {
           $page .= "<tr><td style=\"background-color:orange;\">$n</td><td style=\"background-color:orange;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:orange;\">".$row["charclass"]."</td><td style=\"background-color:orange;\">".$row["level"]."</td><td style=\"background-color:orange;\">".$row["experience"]."</td><td style=\"background-color:orange;\">".$row["gold"]."</td><td style=\"background-color:orange;\">".$row["bank"]."</td></tr>\n";
}
		elseif ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\">$n</td><td style=\"background-color:#ffffff;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["charclass"]."</td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">".$row["experience"]."</td><td style=\"background-color:#ffffff;\">".$row["gold"]."</td><td style=\"background-color:#ffffff;\">".$row["bank"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">$n</td><td style=\"background-color:#eeeeee;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["charclass"]."</td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">".$row["experience"]."</td><td style=\"background-color:#eeeeee;\">".$row["gold"]."</td><td style=\"background-color:#eeeeee;\">".$row["bank"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
    $page .= "</table></td></tr></table><hr />";
    $page .= "<p>You may return to the <a href=\"index.php\">game</a>, or use the compass on the right to start exploring.<br />\n";      
    
    display($page, "Hall of Fame");
    
}

function necro() {
global $controlrow, $userrow;

$query= doquery("SELECT * FROM {{table}} WHERE charclass=5 ORDER BY experience DESC LIMIT 75", "users");
 $page = "<table width='100%' border='1'><tr><td class='title'>Hall of Fame</td></tr></table><p>";
  $page .= "<center>[<a href=\"fame.php?do=main\">Main Hall</a>] [<a href=\"fame.php?do=totals\">Totals</a>]  [<a href=\"fame.php?do=sorc\">Sorceress</a>] [<a href=\"fame.php?do=barb\">Barbarian</a>] [<a href=\"fame.php?do=pala\">Paladin</a>] [<a href=\"fame.php?do=ranger\">Ranger</a>] [<a href=\"fame.php?do=necro\">Necromancer</a>] [<a href=\"fame.php?do=druid\">Druid</a>]<br>[<a href=\"fame.php?do=assn\">Assassin</a>] [<a href=\"fame.php?do=duel\">Dueling</a>] [<a href=\"fame.php?do=combat\">Combat Skills</a>] [<a href=\"fame.php?do=noncombat\">Non-Combat Skills</a>] [<a href=\"fame.php?do=other\">Other</a>] or [<a href=\"index.php\">Return to Game</a>]</center><br>";
 $page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"7\" style=\"background-color:#dddddd;\"><center>Top 75 Necromancer<p>Click on a Character Name to view their Profiles</center></th></tr><tr><th width=\"1%\" style=\"background-color:#dddddd;\">Rank</th><th width=\"20%\" style=\"background-color:#dddddd;\">Character Name</th><th width=\"2%\" style=\"background-color:#dddddd;\">Class</th><th width=\"2%\" style=\"background-color:#dddddd;\">Level</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Experience</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Gold</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Banked Gold</th></tr>\n";

$count = 1;
$n=0;
while ($row = mysql_fetch_array($query)) {
$n += 1;

	    	$row["gold"] = number_format($row["gold"]);
	    	$row["bank"] = number_format($row["bank"]);
	    	$row["experience"] = number_format($row["experience"]);
     if ($row["charclass"] == 5) { $row["charclass"] = $controlrow["class5name"]; }

if($row["charname"] == $userrow["charname"]) {
           $page .= "<tr><td style=\"background-color:orange;\">$n</td><td style=\"background-color:orange;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:orange;\">".$row["charclass"]."</td><td style=\"background-color:orange;\">".$row["level"]."</td><td style=\"background-color:orange;\">".$row["experience"]."</td><td style=\"background-color:orange;\">".$row["gold"]."</td><td style=\"background-color:orange;\">".$row["bank"]."</td></tr>\n";
}
		elseif ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\">$n</td><td style=\"background-color:#ffffff;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["charclass"]."</td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">".$row["experience"]."</td><td style=\"background-color:#ffffff;\">".$row["gold"]."</td><td style=\"background-color:#ffffff;\">".$row["bank"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">$n</td><td style=\"background-color:#eeeeee;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["charclass"]."</td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">".$row["experience"]."</td><td style=\"background-color:#eeeeee;\">".$row["gold"]."</td><td style=\"background-color:#eeeeee;\">".$row["bank"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
    $page .= "</table></td></tr></table><hr />";
    $page .= "<p>You may return to the <a href=\"index.php\">game</a>, or use the compass on the right to start exploring.<br />\n";      
    
    display($page, "Hall of Fame");
    
}

function druid() {
global $controlrow, $userrow;

$query= doquery("SELECT * FROM {{table}} WHERE charclass=6 ORDER BY experience DESC LIMIT 75", "users");
 $page = "<table width='100%' border='1'><tr><td class='title'>Hall of Fame</td></tr></table><p>";
  $page .= "<center>[<a href=\"fame.php?do=main\">Main Hall</a>] [<a href=\"fame.php?do=totals\">Totals</a>]  [<a href=\"fame.php?do=sorc\">Sorceress</a>] [<a href=\"fame.php?do=barb\">Barbarian</a>] [<a href=\"fame.php?do=pala\">Paladin</a>] [<a href=\"fame.php?do=ranger\">Ranger</a>] [<a href=\"fame.php?do=necro\">Necromancer</a>] [<a href=\"fame.php?do=druid\">Druid</a>]<br>[<a href=\"fame.php?do=assn\">Assassin</a>] [<a href=\"fame.php?do=duel\">Dueling</a>] [<a href=\"fame.php?do=combat\">Combat Skills</a>] [<a href=\"fame.php?do=noncombat\">Non-Combat Skills</a>] [<a href=\"fame.php?do=other\">Other</a>] or [<a href=\"index.php\">Return to Game</a>]</center><br>";
 $page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"7\" style=\"background-color:#dddddd;\"><center>Top 75 Druid<p>Click on a Character Name to view their Profiles</center></th></tr><tr><th width=\"1%\" style=\"background-color:#dddddd;\">Rank</th><th width=\"20%\" style=\"background-color:#dddddd;\">Character Name</th><th width=\"2%\" style=\"background-color:#dddddd;\">Class</th><th width=\"2%\" style=\"background-color:#dddddd;\">Level</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Experience</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Gold</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Banked Gold</th></tr>\n";

$count = 1;
$n=0;
while ($row = mysql_fetch_array($query)) {
$n += 1;

	    	$row["gold"] = number_format($row["gold"]);
	    	$row["bank"] = number_format($row["bank"]);
	    	$row["experience"] = number_format($row["experience"]);
     if ($row["charclass"] == 6) { $row["charclass"] = $controlrow["class6name"]; }

if($row["charname"] == $userrow["charname"]) {
           $page .= "<tr><td style=\"background-color:orange;\">$n</td><td style=\"background-color:orange;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:orange;\">".$row["charclass"]."</td><td style=\"background-color:orange;\">".$row["level"]."</td><td style=\"background-color:orange;\">".$row["experience"]."</td><td style=\"background-color:orange;\">".$row["gold"]."</td><td style=\"background-color:orange;\">".$row["bank"]."</td></tr>\n";
}
		elseif ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\">$n</td><td style=\"background-color:#ffffff;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["charclass"]."</td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">".$row["experience"]."</td><td style=\"background-color:#ffffff;\">".$row["gold"]."</td><td style=\"background-color:#ffffff;\">".$row["bank"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">$n</td><td style=\"background-color:#eeeeee;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["charclass"]."</td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">".$row["experience"]."</td><td style=\"background-color:#eeeeee;\">".$row["gold"]."</td><td style=\"background-color:#eeeeee;\">".$row["bank"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
    $page .= "</table></td></tr></table><hr />";
    $page .= "<p>You may return to the <a href=\"index.php\">game</a>, or use the compass on the right to start exploring.<br />\n";      
    
    display($page, "Hall of Fame");
    
}

function assn() {
global $controlrow, $userrow;

$query= doquery("SELECT * FROM {{table}} WHERE charclass=7 ORDER BY experience DESC LIMIT 75", "users");
 $page = "<table width='100%' border='1'><tr><td class='title'>Hall of Fame</td></tr></table><p>";
  $page .= "<center>[<a href=\"fame.php?do=main\">Main Hall</a>] [<a href=\"fame.php?do=totals\">Totals</a>]  [<a href=\"fame.php?do=sorc\">Sorceress</a>] [<a href=\"fame.php?do=barb\">Barbarian</a>] [<a href=\"fame.php?do=pala\">Paladin</a>] [<a href=\"fame.php?do=ranger\">Ranger</a>] [<a href=\"fame.php?do=necro\">Necromancer</a>] [<a href=\"fame.php?do=druid\">Druid</a>]<br>[<a href=\"fame.php?do=assn\">Assassin</a>] [<a href=\"fame.php?do=duel\">Dueling</a>] [<a href=\"fame.php?do=combat\">Combat Skills</a>] [<a href=\"fame.php?do=noncombat\">Non-Combat Skills</a>] [<a href=\"fame.php?do=other\">Other</a>] or [<a href=\"index.php\">Return to Game</a>]</center><br>";
 $page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"7\" style=\"background-color:#dddddd;\"><center>Top 75 Assassin<p>Click on a Character Name to view their Profiles</center></th></tr><tr><th width=\"1%\" style=\"background-color:#dddddd;\">Rank</th><th width=\"20%\" style=\"background-color:#dddddd;\">Character Name</th><th width=\"2%\" style=\"background-color:#dddddd;\">Class</th><th width=\"2%\" style=\"background-color:#dddddd;\">Level</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Experience</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Gold</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Banked Gold</th></tr>\n";

$count = 1;
$n=0;
while ($row = mysql_fetch_array($query)) {
$n += 1;

	    	$row["gold"] = number_format($row["gold"]);
	    	$row["bank"] = number_format($row["bank"]);
	    	$row["experience"] = number_format($row["experience"]);
     if ($row["charclass"] == 7) { $row["charclass"] = $controlrow["class7name"]; }

if($row["charname"] == $userrow["charname"]) {
           $page .= "<tr><td style=\"background-color:orange;\">$n</td><td style=\"background-color:orange;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:orange;\">".$row["charclass"]."</td><td style=\"background-color:orange;\">".$row["level"]."</td><td style=\"background-color:orange;\">".$row["experience"]."</td><td style=\"background-color:orange;\">".$row["gold"]."</td><td style=\"background-color:orange;\">".$row["bank"]."</td></tr>\n";
}
		elseif ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\">$n</td><td style=\"background-color:#ffffff;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["charclass"]."</td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">".$row["experience"]."</td><td style=\"background-color:#ffffff;\">".$row["gold"]."</td><td style=\"background-color:#ffffff;\">".$row["bank"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">$n</td><td style=\"background-color:#eeeeee;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["charclass"]."</td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">".$row["experience"]."</td><td style=\"background-color:#eeeeee;\">".$row["gold"]."</td><td style=\"background-color:#eeeeee;\">".$row["bank"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
    $page .= "</table></td></tr></table><hr />";
    $page .= "<p>You may return to the <a href=\"index.php\">game</a>, or use the compass on the right to start exploring.<br />\n";      
    
    display($page, "Hall of Fame");
    
}

function duel() {
global $controlrow, $userrow;

$query= doquery("SELECT * FROM {{table}} ORDER BY numbattlewon-numbattlelost DESC LIMIT 50", "users");
 $page = "<table width='100%' border='1'><tr><td class='title'>Hall of Fame</td></tr></table><p>";
  $page .= "<center>[<a href=\"fame.php?do=main\">Main Hall</a>] [<a href=\"fame.php?do=totals\">Totals</a>]  [<a href=\"fame.php?do=sorc\">Sorceress</a>] [<a href=\"fame.php?do=barb\">Barbarian</a>] [<a href=\"fame.php?do=pala\">Paladin</a>] [<a href=\"fame.php?do=ranger\">Ranger</a>] [<a href=\"fame.php?do=necro\">Necromancer</a>] [<a href=\"fame.php?do=druid\">Druid</a>]<br>[<a href=\"fame.php?do=assn\">Assassin</a>] [<a href=\"fame.php?do=duel\">Dueling</a>] [<a href=\"fame.php?do=combat\">Combat Skills</a>] [<a href=\"fame.php?do=noncombat\">Non-Combat Skills</a>] [<a href=\"fame.php?do=other\">Other</a>] or [<a href=\"index.php\">Return to Game</a>]</center><br>";
 $page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"7\" style=\"background-color:#dddddd;\"><center>Top 50 Duelers - Total Losses subtracted from Total Wins<p>Click on a Character Name to view their Profiles</center></th></tr><tr><th width=\"1%\" style=\"background-color:#dddddd;\">Rank</th><th width=\"20%\" style=\"background-color:#dddddd;\">Character Name</th><th width=\"2%\" style=\"background-color:#dddddd;\">Class</th><th width=\"2%\" style=\"background-color:#dddddd;\">Level</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Experience</th><th  width=\"1%\" style=\"background-color:#dddddd;\">Wins</th><th  width=\"1%\" style=\"background-color:#dddddd;\">Losses</th></tr>\n";

$count = 1;
$n=0;
while ($row = mysql_fetch_array($query)) {
$n += 1;

	    	$row["experience"] = number_format($row["experience"]);
         if ($row["charclass"] == 1) { $row["charclass"] = $controlrow["class1name"]; }
    elseif ($row["charclass"] == 2) { $row["charclass"] = $controlrow["class2name"]; }
    elseif ($row["charclass"] == 3) { $row["charclass"] = $controlrow["class3name"]; }
    elseif ($row["charclass"] == 4) { $row["charclass"] = $controlrow["class4name"]; }
    elseif ($row["charclass"] == 5) { $row["charclass"] = $controlrow["class5name"]; }
    elseif ($row["charclass"] == 6) { $row["charclass"] = $controlrow["class6name"]; }
    elseif ($row["charclass"] == 7) { $row["charclass"] = $controlrow["class7name"]; }

if($row["charname"] == $userrow["charname"]) {
           $page .= "<tr><td style=\"background-color:orange;\">$n</td><td style=\"background-color:orange;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:orange;\">".$row["charclass"]."</td><td style=\"background-color:orange;\">".$row["level"]."</td><td style=\"background-color:orange;\">".$row["experience"]."</td><td style=\"background-color:orange;\">".$row["numbattlewon"]."</td><td style=\"background-color:orange;\">".$row["numbattlelost"]."</td></tr>\n";
}
		elseif ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\">$n</td><td style=\"background-color:#ffffff;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["charclass"]."</td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">".$row["experience"]."</td><td style=\"background-color:#ffffff;\">".$row["numbattlewon"]."</td><td style=\"background-color:#ffffff;\">".$row["numbattlelost"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">$n</td><td style=\"background-color:#eeeeee;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["charclass"]."</td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">".$row["experience"]."</td><td style=\"background-color:#eeeeee;\">".$row["numbattlewon"]."</td><td style=\"background-color:#eeeeee;\">".$row["numbattlelost"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
    $page .= "</table></td></tr></table><hr /><p>";

    

$query= doquery("SELECT * FROM {{table}} ORDER BY numbattlelost DESC LIMIT 50", "users");
$page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"7\" style=\"background-color:#dddddd;\"><center>Top 50 Duel Losses<p>Click on a Character Name to view their Profiles</center></th></tr><tr><th width=\"1%\" style=\"background-color:#dddddd;\">Rank</th><th width=\"20%\" style=\"background-color:#dddddd;\">Character Name</th><th width=\"2%\" style=\"background-color:#dddddd;\">Class</th><th width=\"2%\" style=\"background-color:#dddddd;\">Level</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Experience</th><th  width=\"1%\" style=\"background-color:#dddddd;\">Wins</th><th  width=\"1%\" style=\"background-color:#dddddd;\">Losses</th></tr>\n";

$count = 1;
$n=0;
while ($row = mysql_fetch_array($query)) {
$n += 1;

	    	$row["experience"] = number_format($row["experience"]);
         if ($row["charclass"] == 1) { $row["charclass"] = $controlrow["class1name"]; }
    elseif ($row["charclass"] == 2) { $row["charclass"] = $controlrow["class2name"]; }
    elseif ($row["charclass"] == 3) { $row["charclass"] = $controlrow["class3name"]; }
    elseif ($row["charclass"] == 4) { $row["charclass"] = $controlrow["class4name"]; }
    elseif ($row["charclass"] == 5) { $row["charclass"] = $controlrow["class5name"]; }
    elseif ($row["charclass"] == 6) { $row["charclass"] = $controlrow["class6name"]; }
    elseif ($row["charclass"] == 7) { $row["charclass"] = $controlrow["class7name"]; }

if($row["charname"] == $userrow["charname"]) {
           $page .= "<tr><td style=\"background-color:orange;\">$n</td><td style=\"background-color:orange;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:orange;\">".$row["charclass"]."</td><td style=\"background-color:orange;\">".$row["level"]."</td><td style=\"background-color:orange;\">".$row["experience"]."</td><td style=\"background-color:orange;\">".$row["numbattlewon"]."</td><td style=\"background-color:orange;\">".$row["numbattlelost"]."</td></tr>\n";
}
		elseif ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\">$n</td><td style=\"background-color:#ffffff;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["charclass"]."</td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">".$row["experience"]."</td><td style=\"background-color:#ffffff;\">".$row["numbattlewon"]."</td><td style=\"background-color:#ffffff;\">".$row["numbattlelost"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">$n</td><td style=\"background-color:#eeeeee;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["charclass"]."</td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">".$row["experience"]."</td><td style=\"background-color:#eeeeee;\">".$row["numbattlewon"]."</td><td style=\"background-color:#eeeeee;\">".$row["numbattlelost"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
    $page .= "</table></td></tr></table><hr />";
    $page .= "<p>You may return to the <a href=\"index.php\">game</a>, or use the compass on the right to start exploring.<br />\n";      
    
    display($page, "Hall of Fame");
    
}

function combat() {
global $controlrow, $userrow;

$query= doquery("SELECT * FROM {{table}} WHERE skill1level ORDER BY skill1level DESC LIMIT 30", "users");
 $page = "<table width='100%' border='1'><tr><td class='title'>Hall of Fame</td></tr></table><p>";
  $page .= "<center>[<a href=\"fame.php?do=main\">Main Hall</a>] [<a href=\"fame.php?do=totals\">Totals</a>]  [<a href=\"fame.php?do=sorc\">Sorceress</a>] [<a href=\"fame.php?do=barb\">Barbarian</a>] [<a href=\"fame.php?do=pala\">Paladin</a>] [<a href=\"fame.php?do=ranger\">Ranger</a>] [<a href=\"fame.php?do=necro\">Necromancer</a>] [<a href=\"fame.php?do=druid\">Druid</a>]<br>[<a href=\"fame.php?do=assn\">Assassin</a>] [<a href=\"fame.php?do=duel\">Dueling</a>] [<a href=\"fame.php?do=combat\">Combat Skills</a>] [<a href=\"fame.php?do=noncombat\">Non-Combat Skills</a>] [<a href=\"fame.php?do=other\">Other</a>] or [<a href=\"index.php\">Return to Game</a>]</center><br>";
 $page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"6\" style=\"background-color:#dddddd;\"><center>Top 30 Wisdom Skill<p>Click on a Character Name to view their Profiles</center></th></tr><tr><th width=\"1%\" style=\"background-color:#dddddd;\">Rank</th><th width=\"20%\" style=\"background-color:#dddddd;\">Character Name</th><th width=\"2%\" style=\"background-color:#dddddd;\">Class</th><th width=\"2%\" style=\"background-color:#dddddd;\">Level</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Experience</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Wisdom Level</th></tr>\n";

$count = 1;
$n=0;
while ($row = mysql_fetch_array($query)) {
$n += 1;

	    	$row["gold"] = number_format($row["gold"]);
	    	$row["bank"] = number_format($row["bank"]);
	    	$row["experience"] = number_format($row["experience"]);
              if ($row["charclass"] == 1) { $row["charclass"] = $controlrow["class1name"]; }
    elseif ($row["charclass"] == 2) { $row["charclass"] = $controlrow["class2name"]; }
    elseif ($row["charclass"] == 3) { $row["charclass"] = $controlrow["class3name"]; }
    elseif ($row["charclass"] == 4) { $row["charclass"] = $controlrow["class4name"]; }
    elseif ($row["charclass"] == 5) { $row["charclass"] = $controlrow["class5name"]; }
    elseif ($row["charclass"] == 6) { $row["charclass"] = $controlrow["class6name"]; }
    elseif ($row["charclass"] == 7) { $row["charclass"] = $controlrow["class7name"]; }

if($row["charname"] == $userrow["charname"]) {
           $page .= "<tr><td style=\"background-color:orange;\">$n</td><td style=\"background-color:orange;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:orange;\">".$row["charclass"]."</td><td style=\"background-color:orange;\">".$row["level"]."</td><td style=\"background-color:orange;\">".$row["experience"]."</td><td style=\"background-color:orange;\">".$row["skill1level"]."</td></tr>\n";
}
		elseif ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\">$n</td><td style=\"background-color:#ffffff;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["charclass"]."</td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">".$row["experience"]."</td><td style=\"background-color:#ffffff;\">".$row["skill1level"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">$n</td><td style=\"background-color:#eeeeee;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["charclass"]."</td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">".$row["experience"]."</td><td style=\"background-color:#eeeeee;\">".$row["skill1level"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
    $page .= "</table></td></tr></table><hr /><p>";
 
$query= doquery("SELECT * FROM {{table}} WHERE skill2level ORDER BY skill2level DESC LIMIT 30", "users");
$page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"6\" style=\"background-color:#dddddd;\"><center>Top 30 Stone Skin Skill<p>Click on a Character Name to view their Profiles</center></th></tr><tr><th width=\"1%\" style=\"background-color:#dddddd;\">Rank</th><th width=\"20%\" style=\"background-color:#dddddd;\">Character Name</th><th width=\"2%\" style=\"background-color:#dddddd;\">Class</th><th width=\"2%\" style=\"background-color:#dddddd;\">Level</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Experience</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Stone Skin Level</th></tr>\n";

$count = 1;
$n=0;
while ($row = mysql_fetch_array($query)) {
$n += 1;

	    	$row["gold"] = number_format($row["gold"]);
	    	$row["bank"] = number_format($row["bank"]);
	    	$row["experience"] = number_format($row["experience"]);
              if ($row["charclass"] == 1) { $row["charclass"] = $controlrow["class1name"]; }
    elseif ($row["charclass"] == 2) { $row["charclass"] = $controlrow["class2name"]; }
    elseif ($row["charclass"] == 3) { $row["charclass"] = $controlrow["class3name"]; }
    elseif ($row["charclass"] == 4) { $row["charclass"] = $controlrow["class4name"]; }
    elseif ($row["charclass"] == 5) { $row["charclass"] = $controlrow["class5name"]; }
    elseif ($row["charclass"] == 6) { $row["charclass"] = $controlrow["class6name"]; }
    elseif ($row["charclass"] == 7) { $row["charclass"] = $controlrow["class7name"]; }

if($row["charname"] == $userrow["charname"]) {
           $page .= "<tr><td style=\"background-color:orange;\">$n</td><td style=\"background-color:orange;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:orange;\">".$row["charclass"]."</td><td style=\"background-color:orange;\">".$row["level"]."</td><td style=\"background-color:orange;\">".$row["experience"]."</td><td style=\"background-color:orange;\">".$row["skill2level"]."</td></tr>\n";
}
		elseif ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\">$n</td><td style=\"background-color:#ffffff;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["charclass"]."</td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">".$row["experience"]."</td><td style=\"background-color:#ffffff;\">".$row["skill2level"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">$n</td><td style=\"background-color:#eeeeee;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["charclass"]."</td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">".$row["experience"]."</td><td style=\"background-color:#eeeeee;\">".$row["skill2level"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
    $page .= "</table></td></tr></table><hr /><p>";
    
    
    $query= doquery("SELECT * FROM {{table}} WHERE skill3level ORDER BY skill3level DESC LIMIT 30", "users");
$page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"6\" style=\"background-color:#dddddd;\"><center>Top 30 Monks Mind Skill<p>Click on a Character Name to view their Profiles</center></th></tr><tr><th width=\"1%\" style=\"background-color:#dddddd;\">Rank</th><th width=\"20%\" style=\"background-color:#dddddd;\">Character Name</th><th width=\"2%\" style=\"background-color:#dddddd;\">Class</th><th width=\"2%\" style=\"background-color:#dddddd;\">Level</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Experience</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Monks Mind Level</th></tr>\n";

$count = 1;
$n=0;
while ($row = mysql_fetch_array($query)) {
$n += 1;

	    	$row["gold"] = number_format($row["gold"]);
	    	$row["bank"] = number_format($row["bank"]);
	    	$row["experience"] = number_format($row["experience"]);
              if ($row["charclass"] == 1) { $row["charclass"] = $controlrow["class1name"]; }
    elseif ($row["charclass"] == 2) { $row["charclass"] = $controlrow["class2name"]; }
    elseif ($row["charclass"] == 3) { $row["charclass"] = $controlrow["class3name"]; }
    elseif ($row["charclass"] == 4) { $row["charclass"] = $controlrow["class4name"]; }
    elseif ($row["charclass"] == 5) { $row["charclass"] = $controlrow["class5name"]; }
    elseif ($row["charclass"] == 6) { $row["charclass"] = $controlrow["class6name"]; }
    elseif ($row["charclass"] == 7) { $row["charclass"] = $controlrow["class7name"]; }

if($row["charname"] == $userrow["charname"]) {
           $page .= "<tr><td style=\"background-color:orange;\">$n</td><td style=\"background-color:orange;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:orange;\">".$row["charclass"]."</td><td style=\"background-color:orange;\">".$row["level"]."</td><td style=\"background-color:orange;\">".$row["experience"]."</td><td style=\"background-color:orange;\">".$row["skill3level"]."</td></tr>\n";
}
		elseif ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\">$n</td><td style=\"background-color:#ffffff;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["charclass"]."</td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">".$row["experience"]."</td><td style=\"background-color:#ffffff;\">".$row["skill3level"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">$n</td><td style=\"background-color:#eeeeee;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["charclass"]."</td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">".$row["experience"]."</td><td style=\"background-color:#eeeeee;\">".$row["skill3level"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
    $page .= "</table></td></tr></table><hr /><p>";
 
 $query= doquery("SELECT * FROM {{table}} WHERE skill4level ORDER BY skill4level DESC LIMIT 30", "users");
$page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"6\" style=\"background-color:#dddddd;\"><center>Top 30 Fortune Skill<p>Click on a Character Name to view their Profiles</center></th></tr><tr><th width=\"1%\" style=\"background-color:#dddddd;\">Rank</th><th width=\"20%\" style=\"background-color:#dddddd;\">Character Name</th><th width=\"2%\" style=\"background-color:#dddddd;\">Class</th><th width=\"2%\" style=\"background-color:#dddddd;\">Level</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Experience</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Fortune Level</th></tr>\n";

$count = 1;
$n=0;
while ($row = mysql_fetch_array($query)) {
$n += 1;

	    	$row["gold"] = number_format($row["gold"]);
	    	$row["bank"] = number_format($row["bank"]);
	    	$row["experience"] = number_format($row["experience"]);
              if ($row["charclass"] == 1) { $row["charclass"] = $controlrow["class1name"]; }
    elseif ($row["charclass"] == 2) { $row["charclass"] = $controlrow["class2name"]; }
    elseif ($row["charclass"] == 3) { $row["charclass"] = $controlrow["class3name"]; }
    elseif ($row["charclass"] == 4) { $row["charclass"] = $controlrow["class4name"]; }
    elseif ($row["charclass"] == 5) { $row["charclass"] = $controlrow["class5name"]; }
    elseif ($row["charclass"] == 6) { $row["charclass"] = $controlrow["class6name"]; }
    elseif ($row["charclass"] == 7) { $row["charclass"] = $controlrow["class7name"]; }

if($row["charname"] == $userrow["charname"]) {
           $page .= "<tr><td style=\"background-color:orange;\">$n</td><td style=\"background-color:orange;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:orange;\">".$row["charclass"]."</td><td style=\"background-color:orange;\">".$row["level"]."</td><td style=\"background-color:orange;\">".$row["experience"]."</td><td style=\"background-color:orange;\">".$row["skill4level"]."</td></tr>\n";
}
		elseif ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\">$n</td><td style=\"background-color:#ffffff;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["charclass"]."</td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">".$row["experience"]."</td><td style=\"background-color:#ffffff;\">".$row["skill4level"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">$n</td><td style=\"background-color:#eeeeee;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["charclass"]."</td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">".$row["experience"]."</td><td style=\"background-color:#eeeeee;\">".$row["skill4level"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
    $page .= "</table></td></tr></table><hr />";
    
    $page .= "<p>You may return to the <a href=\"index.php\">game</a>, or use the compass on the right to start exploring.<br />\n";      
    
    display($page, "Hall of Fame");
    
}

function noncombat() {
global $controlrow, $userrow;

$query= doquery("SELECT * FROM {{table}} ORDER BY endurancexp DESC LIMIT 50", "users");
 $page = "<table width='100%' border='1'><tr><td class='title'>Hall of Fame</td></tr></table><p>";
  $page .= "<center>[<a href=\"fame.php?do=main\">Main Hall</a>] [<a href=\"fame.php?do=totals\">Totals</a>]  [<a href=\"fame.php?do=sorc\">Sorceress</a>] [<a href=\"fame.php?do=barb\">Barbarian</a>] [<a href=\"fame.php?do=pala\">Paladin</a>] [<a href=\"fame.php?do=ranger\">Ranger</a>] [<a href=\"fame.php?do=necro\">Necromancer</a>] [<a href=\"fame.php?do=druid\">Druid</a>]<br>[<a href=\"fame.php?do=assn\">Assassin</a>] [<a href=\"fame.php?do=duel\">Dueling</a>] [<a href=\"fame.php?do=combat\">Combat Skills</a>] [<a href=\"fame.php?do=noncombat\">Non-Combat Skills</a>] [<a href=\"fame.php?do=other\">Other</a>] or [<a href=\"index.php\">Return to Game</a>]</center><br>";
 $page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"6\" style=\"background-color:#dddddd;\"><center>Top 50 Endurance Skill<p>Click on a Character Name to view their Profiles</center></th></tr><tr><th width=\"1%\" style=\"background-color:#dddddd;\">Rank</th><th width=\"20%\" style=\"background-color:#dddddd;\">Character Name</th><th width=\"2%\" style=\"background-color:#dddddd;\">Class</th><th width=\"2%\" style=\"background-color:#dddddd;\">Level</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Endurance Experience</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Endurance Level</th></tr>\n";

$count = 1;
$n=0;
while ($row = mysql_fetch_array($query)) {
$n += 1;
$row["endurancexp"] = number_format($row["endurancexp"]);
	    	$row["gold"] = number_format($row["gold"]);
	    	$row["bank"] = number_format($row["bank"]);
	    	$row["experience"] = number_format($row["experience"]);
              if ($row["charclass"] == 1) { $row["charclass"] = $controlrow["class1name"]; }
    elseif ($row["charclass"] == 2) { $row["charclass"] = $controlrow["class2name"]; }
    elseif ($row["charclass"] == 3) { $row["charclass"] = $controlrow["class3name"]; }
    elseif ($row["charclass"] == 4) { $row["charclass"] = $controlrow["class4name"]; }
    elseif ($row["charclass"] == 5) { $row["charclass"] = $controlrow["class5name"]; }
    elseif ($row["charclass"] == 6) { $row["charclass"] = $controlrow["class6name"]; }
    elseif ($row["charclass"] == 7) { $row["charclass"] = $controlrow["class7name"]; }

if($row["charname"] == $userrow["charname"]) {
           $page .= "<tr><td style=\"background-color:orange;\">$n</td><td style=\"background-color:orange;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:orange;\">".$row["charclass"]."</td><td style=\"background-color:orange;\">".$row["level"]."</td><td style=\"background-color:orange;\">".$row["endurancexp"]."</td><td style=\"background-color:orange;\">".$row["endurance"]."</td></tr>\n";
}
		elseif ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\">$n</td><td style=\"background-color:#ffffff;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["charclass"]."</td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">".$row["endurancexp"]."</td><td style=\"background-color:#ffffff;\">".$row["endurance"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">$n</td><td style=\"background-color:#eeeeee;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["charclass"]."</td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">".$row["endurancexp"]."</td><td style=\"background-color:#eeeeee;\">".$row["endurance"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
    $page .= "</table></td></tr></table><hr /><p>";
 
$query= doquery("SELECT * FROM {{table}} ORDER BY miningxp DESC LIMIT 50", "users");
 $page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"6\" style=\"background-color:#dddddd;\"><center>Top 50 Mining Skill<p>Click on a Character Name to view their Profiles</center></th></tr><tr><th width=\"1%\" style=\"background-color:#dddddd;\">Rank</th><th width=\"20%\" style=\"background-color:#dddddd;\">Character Name</th><th width=\"2%\" style=\"background-color:#dddddd;\">Class</th><th width=\"2%\" style=\"background-color:#dddddd;\">Level</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Mining Experience</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Mining Level</th></tr>\n";

$count = 1;
$n=0;
while ($row = mysql_fetch_array($query)) {
$n += 1;
$row["miningxp"] = number_format($row["miningxp"]);
	    	$row["gold"] = number_format($row["gold"]);
	    	$row["bank"] = number_format($row["bank"]);
	    	$row["experience"] = number_format($row["experience"]);
              if ($row["charclass"] == 1) { $row["charclass"] = $controlrow["class1name"]; }
    elseif ($row["charclass"] == 2) { $row["charclass"] = $controlrow["class2name"]; }
    elseif ($row["charclass"] == 3) { $row["charclass"] = $controlrow["class3name"]; }
    elseif ($row["charclass"] == 4) { $row["charclass"] = $controlrow["class4name"]; }
    elseif ($row["charclass"] == 5) { $row["charclass"] = $controlrow["class5name"]; }
    elseif ($row["charclass"] == 6) { $row["charclass"] = $controlrow["class6name"]; }
    elseif ($row["charclass"] == 7) { $row["charclass"] = $controlrow["class7name"]; }

if($row["charname"] == $userrow["charname"]) {
           $page .= "<tr><td style=\"background-color:orange;\">$n</td><td style=\"background-color:orange;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:orange;\">".$row["charclass"]."</td><td style=\"background-color:orange;\">".$row["level"]."</td><td style=\"background-color:orange;\">".$row["miningxp"]."</td><td style=\"background-color:orange;\">".$row["mining"]."</td></tr>\n";
}
		elseif ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\">$n</td><td style=\"background-color:#ffffff;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["charclass"]."</td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">".$row["miningxp"]."</td><td style=\"background-color:#ffffff;\">".$row["mining"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">$n</td><td style=\"background-color:#eeeeee;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["charclass"]."</td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">".$row["miningxp"]."</td><td style=\"background-color:#eeeeee;\">".$row["mining"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
    $page .= "</table></td></tr></table><hr /><p>";
    
    $query= doquery("SELECT * FROM {{table}} ORDER BY craftingxp DESC LIMIT 50", "users");
 $page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"6\" style=\"background-color:#dddddd;\"><center>Top 50 Crafting Skill<p>Click on a Character Name to view their Profiles</center></th></tr><tr><th width=\"1%\" style=\"background-color:#dddddd;\">Rank</th><th width=\"20%\" style=\"background-color:#dddddd;\">Character Name</th><th width=\"2%\" style=\"background-color:#dddddd;\">Class</th><th width=\"2%\" style=\"background-color:#dddddd;\">Level</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Crafting Experience</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Crafting Level</th></tr>\n";

$count = 1;
$n=0;
while ($row = mysql_fetch_array($query)) {
$n += 1;
$row["craftingxp"] = number_format($row["craftingxp"]);
	    	$row["gold"] = number_format($row["gold"]);
	    	$row["bank"] = number_format($row["bank"]);
	    	$row["experience"] = number_format($row["experience"]);
              if ($row["charclass"] == 1) { $row["charclass"] = $controlrow["class1name"]; }
    elseif ($row["charclass"] == 2) { $row["charclass"] = $controlrow["class2name"]; }
    elseif ($row["charclass"] == 3) { $row["charclass"] = $controlrow["class3name"]; }
    elseif ($row["charclass"] == 4) { $row["charclass"] = $controlrow["class4name"]; }
    elseif ($row["charclass"] == 5) { $row["charclass"] = $controlrow["class5name"]; }
    elseif ($row["charclass"] == 6) { $row["charclass"] = $controlrow["class6name"]; }
    elseif ($row["charclass"] == 7) { $row["charclass"] = $controlrow["class7name"]; }

if($row["charname"] == $userrow["charname"]) {
           $page .= "<tr><td style=\"background-color:orange;\">$n</td><td style=\"background-color:orange;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:orange;\">".$row["charclass"]."</td><td style=\"background-color:orange;\">".$row["level"]."</td><td style=\"background-color:orange;\">".$row["craftingxp"]."</td><td style=\"background-color:orange;\">".$row["crafting"]."</td></tr>\n";
}
		elseif ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\">$n</td><td style=\"background-color:#ffffff;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["charclass"]."</td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">".$row["craftingxp"]."</td><td style=\"background-color:#ffffff;\">".$row["crafting"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">$n</td><td style=\"background-color:#eeeeee;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["charclass"]."</td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">".$row["craftingxp"]."</td><td style=\"background-color:#eeeeee;\">".$row["crafting"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
    $page .= "</table></td></tr></table><hr /><p>";
    
    
  $query= doquery("SELECT * FROM {{table}} ORDER BY smeltingxp DESC LIMIT 50", "users");
 $page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"6\" style=\"background-color:#dddddd;\"><center>Top 50 Smelting Skill<p>Click on a Character Name to view their Profiles</center></th></tr><tr><th width=\"1%\" style=\"background-color:#dddddd;\">Rank</th><th width=\"20%\" style=\"background-color:#dddddd;\">Character Name</th><th width=\"2%\" style=\"background-color:#dddddd;\">Class</th><th width=\"2%\" style=\"background-color:#dddddd;\">Level</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Smelting Experience</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Smelting Level</th></tr>\n";

$count = 1;
$n=0;
while ($row = mysql_fetch_array($query)) {
$n += 1;
$row["smeltingxp"] = number_format($row["smeltingxp"]);
	    	$row["gold"] = number_format($row["gold"]);
	    	$row["bank"] = number_format($row["bank"]);
	    	$row["experience"] = number_format($row["experience"]);
              if ($row["charclass"] == 1) { $row["charclass"] = $controlrow["class1name"]; }
    elseif ($row["charclass"] == 2) { $row["charclass"] = $controlrow["class2name"]; }
    elseif ($row["charclass"] == 3) { $row["charclass"] = $controlrow["class3name"]; }
    elseif ($row["charclass"] == 4) { $row["charclass"] = $controlrow["class4name"]; }
    elseif ($row["charclass"] == 5) { $row["charclass"] = $controlrow["class5name"]; }
    elseif ($row["charclass"] == 6) { $row["charclass"] = $controlrow["class6name"]; }
    elseif ($row["charclass"] == 7) { $row["charclass"] = $controlrow["class7name"]; }

if($row["charname"] == $userrow["charname"]) {
           $page .= "<tr><td style=\"background-color:orange;\">$n</td><td style=\"background-color:orange;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:orange;\">".$row["charclass"]."</td><td style=\"background-color:orange;\">".$row["level"]."</td><td style=\"background-color:orange;\">".$row["smeltingxp"]."</td><td style=\"background-color:orange;\">".$row["smelting"]."</td></tr>\n";
}
		elseif ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\">$n</td><td style=\"background-color:#ffffff;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["charclass"]."</td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">".$row["smeltingxp"]."</td><td style=\"background-color:#ffffff;\">".$row["smelting"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">$n</td><td style=\"background-color:#eeeeee;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["charclass"]."</td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">".$row["smeltingxp"]."</td><td style=\"background-color:#eeeeee;\">".$row["smelting"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
    $page .= "</table></td></tr></table><hr /><p>";

  
  
    
    
     $query= doquery("SELECT * FROM {{table}} ORDER BY forgingxp DESC LIMIT 50", "users");
 $page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"6\" style=\"background-color:#dddddd;\"><center>Top 50 Forging Skill<p>Click on a Character Name to view their Profiles</center></th></tr><tr><th width=\"1%\" style=\"background-color:#dddddd;\">Rank</th><th width=\"20%\" style=\"background-color:#dddddd;\">Character Name</th><th width=\"2%\" style=\"background-color:#dddddd;\">Class</th><th width=\"2%\" style=\"background-color:#dddddd;\">Level</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Forging Experience</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Forging Level</th></tr>\n";

$count = 1;
$n=0;
while ($row = mysql_fetch_array($query)) {
$n += 1;
$row["forgingxp"] = number_format($row["forgingxp"]);
	    	$row["gold"] = number_format($row["gold"]);
	    	$row["bank"] = number_format($row["bank"]);
	    	$row["experience"] = number_format($row["experience"]);
              if ($row["charclass"] == 1) { $row["charclass"] = $controlrow["class1name"]; }
    elseif ($row["charclass"] == 2) { $row["charclass"] = $controlrow["class2name"]; }
    elseif ($row["charclass"] == 3) { $row["charclass"] = $controlrow["class3name"]; }
    elseif ($row["charclass"] == 4) { $row["charclass"] = $controlrow["class4name"]; }
    elseif ($row["charclass"] == 5) { $row["charclass"] = $controlrow["class5name"]; }
    elseif ($row["charclass"] == 6) { $row["charclass"] = $controlrow["class6name"]; }
    elseif ($row["charclass"] == 7) { $row["charclass"] = $controlrow["class7name"]; }

if($row["charname"] == $userrow["charname"]) {
           $page .= "<tr><td style=\"background-color:orange;\">$n</td><td style=\"background-color:orange;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:orange;\">".$row["charclass"]."</td><td style=\"background-color:orange;\">".$row["level"]."</td><td style=\"background-color:orange;\">".$row["forgingxp"]."</td><td style=\"background-color:orange;\">".$row["forging"]."</td></tr>\n";
}
		elseif ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\">$n</td><td style=\"background-color:#ffffff;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["charclass"]."</td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">".$row["forgingxp"]."</td><td style=\"background-color:#ffffff;\">".$row["forging"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">$n</td><td style=\"background-color:#eeeeee;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["charclass"]."</td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">".$row["forgingxp"]."</td><td style=\"background-color:#eeeeee;\">".$row["forging"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
    $page .= "</table></td></tr></table><hr />";
   
    $page .= "<p>You may return to the <a href=\"index.php\">game</a>, or use the compass on the right to start exploring.<br />\n";      
    
    display($page, "Hall of Fame");
    
}

function other() {
global $controlrow, $userrow;
	
$query= doquery("SELECT * FROM {{table}} ORDER BY level DESC LIMIT 50", "arena");
 $page = "<table width='100%' border='1'><tr><td class='title'>Hall of Fame</td></tr></table><p>";
  $page .= "<center>[<a href=\"fame.php?do=main\">Main Hall</a>] [<a href=\"fame.php?do=totals\">Totals</a>]  [<a href=\"fame.php?do=sorc\">Sorceress</a>] [<a href=\"fame.php?do=barb\">Barbarian</a>] [<a href=\"fame.php?do=pala\">Paladin</a>] [<a href=\"fame.php?do=ranger\">Ranger</a>] [<a href=\"fame.php?do=necro\">Necromancer</a>] [<a href=\"fame.php?do=druid\">Druid</a>]<br>[<a href=\"fame.php?do=assn\">Assassin</a>] [<a href=\"fame.php?do=duel\">Dueling</a>] [<a href=\"fame.php?do=combat\">Combat Skills</a>] [<a href=\"fame.php?do=noncombat\">Non-Combat Skills</a>] [<a href=\"fame.php?do=other\">Other</a>] or [<a href=\"index.php\">Return to Game</a>]</center><br>";
 $page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"8\" style=\"background-color:#dddddd;\"><center>Top 50 Pets - Ordered by Level<p>Click on a Trainers Name to view their Profiles</center></th></tr><tr><th width=\"1%\" style=\"background-color:#dddddd;\">Rank</th><th width=\"20%\" style=\"background-color:#dddddd;\">Pet Name</th><th width=\"2%\" style=\"background-color:#dddddd;\">Pet Type</th><th width=\"2%\" style=\"background-color:#dddddd;\">Pet Trainer</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Level</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Experience</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Wins</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Losses</th></tr>\n";

$count = 1;
$n=0;
while ($row = mysql_fetch_array($query)) {
	$query2 = doquery("SELECT id FROM {{table}} WHERE charname='".$row["trainer"]."' LIMIT 1", "users");
	  $row2 = mysql_fetch_array($query2);
$n += 1;

	    	$row["gold"] = number_format($row["gold"]);
	    	$row["bank"] = number_format($row["bank"]);
	    	$row["experience"] = number_format($row["experience"]);
   

if($row["trainer"] == $userrow["charname"]) {
           $page .= "<tr><td style=\"background-color:orange;\">$n</td><td style=\"background-color:orange;\">".$row["name"]."</td><td style=\"background-color:orange;\">".$row["type"]."</td><td style=\"background-color:orange;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["trainer"]."</a></td><td style=\"background-color:orange;\">".$row["level"]."</td><td style=\"background-color:orange;\">".$row["experience"]."</td><td style=\"background-color:orange;\">".$row["wins"]."</td><td style=\"background-color:orange;\">".$row["losses"]."</td></tr>\n";
}
		elseif ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\">$n</td><td style=\"background-color:#ffffff;\">".$row["name"]."</td><td style=\"background-color:#ffffff;\">".$row["type"]."</td><td style=\"background-color:#ffffff;\"><a href=\"index.php?do=onlinechar:".$row2["id"]."\">".$row["trainer"]."</a></td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">".$row["experience"]."</td><td style=\"background-color:#ffffff;\">".$row["wins"]."</td><td style=\"background-color:#ffffff;\">".$row["losses"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">$n</td><td style=\"background-color:#eeeeee;\">".$row["name"]."</td><td style=\"background-color:#eeeeee;\">".$row["type"]."</td><td style=\"background-color:#eeeeee;\"><a href=\"index.php?do=onlinechar:".$row2["id"]."\">".$row["trainer"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">".$row["experience"]."</td><td style=\"background-color:#eeeeee;\">".$row["wins"]."</td><td style=\"background-color:#eeeeee;\">".$row["losses"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
    $page .= "</table></td></tr></table><hr />";
    $page .= "<p>You may return to the <a href=\"index.php\">game</a>, or use the compass on the right to start exploring.<br />\n";      
    
    display($page, "Hall of Fame");

}

function totals() {
global $controlrow, $userrow;

$query= doquery("SELECT * FROM {{table}} ORDER BY experience+craftingxp+miningxp+smeltingxp+endurancexp+forgingxp DESC LIMIT 50", "users");
 $page = "<table width='100%' border='1'><tr><td class='title'>Hall of Fame</td></tr></table><p>";
  $page .= "<center>[<a href=\"fame.php?do=main\">Main Hall</a>] [<a href=\"fame.php?do=totals\">Totals</a>]  [<a href=\"fame.php?do=sorc\">Sorceress</a>] [<a href=\"fame.php?do=barb\">Barbarian</a>] [<a href=\"fame.php?do=pala\">Paladin</a>] [<a href=\"fame.php?do=ranger\">Ranger</a>] [<a href=\"fame.php?do=necro\">Necromancer</a>] [<a href=\"fame.php?do=druid\">Druid</a>]<br>[<a href=\"fame.php?do=assn\">Assassin</a>] [<a href=\"fame.php?do=duel\">Dueling</a>] [<a href=\"fame.php?do=combat\">Combat Skills</a>] [<a href=\"fame.php?do=noncombat\">Non-Combat Skills</a>] [<a href=\"fame.php?do=other\">Other</a>] or [<a href=\"index.php\">Return to Game</a>]</center><br>";
 $page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"7\" style=\"background-color:#dddddd;\"><center>Top 50 Experience Total Players<p>Click on a Character Name to view their Profiles</center></th></tr><tr><th width=\"1%\" style=\"background-color:#dddddd;\">Rank</th><th width=\"20%\" style=\"background-color:#dddddd;\">Character Name</th><th width=\"2%\" style=\"background-color:#dddddd;\">Class</th><th width=\"2%\" style=\"background-color:#dddddd;\">Level</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Total Experience</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Gold</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Banked Gold</th></tr>\n";
$count = 1;
$n=0;
while ($row = mysql_fetch_array($query)) {
$n += 1;
$row["totalexperience"] = ($row["experience"] + $row["miningxp"] + $row["endurancexp"] + $row["smeltingxp"] + $row["craftingxp"] + $row["forgingxp"]);
$row["totalexperience"] = number_format($row["totalexperience"]);
	    	$row["gold"] = number_format($row["gold"]);
	    	$row["bank"] = number_format($row["bank"]);
	    	$row["experience"] = number_format($row["experience"]);
     if ($row["charclass"] == 1) { $row["charclass"] = $controlrow["class1name"]; }
    elseif ($row["charclass"] == 2) { $row["charclass"] = $controlrow["class2name"]; }
    elseif ($row["charclass"] == 3) { $row["charclass"] = $controlrow["class3name"]; }
    elseif ($row["charclass"] == 4) { $row["charclass"] = $controlrow["class4name"]; }
    elseif ($row["charclass"] == 5) { $row["charclass"] = $controlrow["class5name"]; }
    elseif ($row["charclass"] == 6) { $row["charclass"] = $controlrow["class6name"]; }
    elseif ($row["charclass"] == 7) { $row["charclass"] = $controlrow["class7name"]; }
if($row["charname"] == $userrow["charname"]) {
           $page .= "<tr><td style=\"background-color:orange;\">$n</td><td style=\"background-color:orange;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:orange;\">".$row["charclass"]."</td><td style=\"background-color:orange;\">".$row["level"]."</td><td style=\"background-color:orange;\">".$row["totalexperience"]."</td><td style=\"background-color:orange;\">".$row["gold"]."</td><td style=\"background-color:orange;\">".$row["bank"]."</td></tr>\n";
}
		elseif ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\">$n</td><td style=\"background-color:#ffffff;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["charclass"]."</td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">".$row["totalexperience"]."</td><td style=\"background-color:#ffffff;\">".$row["gold"]."</td><td style=\"background-color:#ffffff;\">".$row["bank"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">$n</td><td style=\"background-color:#eeeeee;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["charclass"]."</td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">".$row["totalexperience"]."</td><td style=\"background-color:#eeeeee;\">".$row["gold"]."</td><td style=\"background-color:#eeeeee;\">".$row["bank"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
    $page .= "</table></td></tr></table><hr />";
    
    $query= doquery("SELECT * FROM {{table}} ORDER BY level+crafting+mining+smelting+endurance+forging+skill1level+skill2level+skill3level+skill4level DESC LIMIT 50", "users");
$page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"7\" style=\"background-color:#dddddd;\"><center>Top 50 Skill Total Players<p>Click on a Character Name to view their Profiles</center></th></tr><tr><th width=\"1%\" style=\"background-color:#dddddd;\">Rank</th><th width=\"20%\" style=\"background-color:#dddddd;\">Character Name</th><th width=\"2%\" style=\"background-color:#dddddd;\">Class</th><th width=\"2%\" style=\"background-color:#dddddd;\">Level</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Skill Total</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Gold</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Banked Gold</th></tr>\n";
$count = 1;
$n=0;
while ($row = mysql_fetch_array($query)) {
$n += 1;
$row["skilltotal"] = ($row["level"] + $row["mining"] + $row["endurance"] + $row["smelting"] + $row["crafting"] + $row["forging"] + $row["skill1level"] + $row["skill2level"] + $row["skill3level"] + $row["skill4level"]);
$row["skilltotal"] = number_format($row["skilltotal"]);
	    	$row["gold"] = number_format($row["gold"]);
	    	$row["bank"] = number_format($row["bank"]);
	    	$row["experience"] = number_format($row["experience"]);
     if ($row["charclass"] == 1) { $row["charclass"] = $controlrow["class1name"]; }
    elseif ($row["charclass"] == 2) { $row["charclass"] = $controlrow["class2name"]; }
    elseif ($row["charclass"] == 3) { $row["charclass"] = $controlrow["class3name"]; }
    elseif ($row["charclass"] == 4) { $row["charclass"] = $controlrow["class4name"]; }
    elseif ($row["charclass"] == 5) { $row["charclass"] = $controlrow["class5name"]; }
    elseif ($row["charclass"] == 6) { $row["charclass"] = $controlrow["class6name"]; }
    elseif ($row["charclass"] == 7) { $row["charclass"] = $controlrow["class7name"]; }
if($row["charname"] == $userrow["charname"]) {
           $page .= "<tr><td style=\"background-color:orange;\">$n</td><td style=\"background-color:orange;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:orange;\">".$row["charclass"]."</td><td style=\"background-color:orange;\">".$row["level"]."</td><td style=\"background-color:orange;\">".$row["skilltotal"]."</td><td style=\"background-color:orange;\">".$row["gold"]."</td><td style=\"background-color:orange;\">".$row["bank"]."</td></tr>\n";
}
		elseif ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\">$n</td><td style=\"background-color:#ffffff;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#ffffff;\">".$row["charclass"]."</td><td style=\"background-color:#ffffff;\">".$row["level"]."</td><td style=\"background-color:#ffffff;\">".$row["skilltotal"]."</td><td style=\"background-color:#ffffff;\">".$row["gold"]."</td><td style=\"background-color:#ffffff;\">".$row["bank"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">$n</td><td style=\"background-color:#eeeeee;\"><a href=\"index.php?do=onlinechar:".$row["id"]."\">".$row["charname"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["charclass"]."</td><td style=\"background-color:#eeeeee;\">".$row["level"]."</td><td style=\"background-color:#eeeeee;\">".$row["skilltotal"]."</td><td style=\"background-color:#eeeeee;\">".$row["gold"]."</td><td style=\"background-color:#eeeeee;\">".$row["bank"]."</td></tr>\n";
			$count = 1;
		}
	  }
    
    $page .= "</table></td></tr></table><hr />";
    
    
    $page .= "<p>You may return to the <a href=\"index.php\">game</a>, or use the compass on the right to start exploring.<br />\n";      
    
    display($page, "Hall of Fame");
    
}
?>