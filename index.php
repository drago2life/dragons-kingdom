<?php // index.php
if (file_exists('install.php')) { die("Please delete <b>install.php</b> from your DK directory before continuing. Only do this once it is fully installed."); }

include('lib.php');
include('cookies.php');
$link = opendb();
$controlquery = doquery("SELECT * FROM {{table}} WHERE id='1' LIMIT 1", "control");
$controlrow = mysql_fetch_array($controlquery);

// Login (or verify) if not logged in.
$userrow = checkcookies();
if ($userrow == false) {
    if (isset($_GET["do"])) {
        if ($_GET["do"] == "verify") { header("Location: users.php?do=verify"); die(); }
    }
    header("Location: login.php?do=login"); die();
}

// Close game.
if ($controlrow["gameopen"] == 0) { display("<table width='100%' border='1'><tr><td class='title'>Dragon's Kingdom is Closed</td></tr></table><p><center><img src='images/main.gif' alt=\"Dragon's Kingdom RPG\"></center><p>Dragon's Kingdom is currently closed for maintanence and/or updates.<p><b>Estimated Time:</b> ".$controlrow["updatetime"]."<br><b>Information:</b> ".$controlrow["info"]."<p>Please check back later.","Game Closed For Updates"); die(); }
// Block user if he/she has been banned.
if ($userrow["authlevel"] == 2 || ($_COOKIE['dk_login'] == 1)) { setcookie("dk_login", "1", time()+999999999999999);
die("<b>You have been banned</b><p>Your accounts has been banned and you have been placed into the Town Jail. This may well be permanent, or just a 24 hour temporary warning ban. If you want to be unbanned, contact the game administrator by emailing admin@dk-rpg.com."); }
if ($userrow["tutorial"] == 0) { header("Location: tutorial.php"); die(); } //Not done the tutorial
//Must vote
if (($userrow["poll"] != "Voted") && ($userrow["level"] >= "3")) { header("Location: poll.php"); die(); }
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

//Set storage varibles
$backpackdropslots = 3;
$storagedropslots = 10;
$backpackitemslots = 3;
$storageitemslots = 15;
$backpackjewelleryslots = 3;
$storagejewelleryslots = 10;
##############

if (isset($_GET["do"])) {
    $do = explode(":",$_GET["do"]);

    // Town functions.
    if ($do[0] == "inn") { include('towns.php'); inn(); }
     elseif ($do[0] == "post_comment") { include('comments_mod.php'); addpost($do[1]); }
    elseif ($do[0] == "comments") { include('comments_mod.php'); read($do[1]); }
    elseif ($do[0] == "editcomment") { include('comments_mod.php'); editcomment($do[1]); }
     elseif ($do[0] == "delete") { include('comments_mod.php'); delete($do[1]); }
    elseif ($do[0] == "buy") { include('towns.php'); buy(); }
    elseif ($do[0] == "buy2") { include('towns.php'); buy2($do[1]); }
    elseif ($do[0] == "buy3") { include('towns.php'); buy3($do[1]); }
    elseif ($do[0] == "buy4") { include('towns.php'); buy4($do[1]); }
    elseif ($do[0] == "sell") { include('towns.php'); sell(); }
    elseif ($do[0] == "maps") { include('towns.php'); maps(); }
    elseif ($do[0] == "maps2") { include('towns.php'); maps2($do[1]); }
    elseif ($do[0] == "maps3") { include('towns.php'); maps3($do[1]); }
    elseif ($do[0] == "gotown") { include('towns.php'); travelto($do[1]); }
    elseif ($do[0] == "homeportal") { include('towns.php'); homeportal(); }
    elseif ($do[0] == "logbridge") { include('towns.php'); logbridge($do[1]); }
    elseif ($do[0] == "gamble") { include('towns.php'); gamble($do[1]); }
    if ($do[0] == "bank") { include('towns.php'); bank(); }
    // Cave actions
    if ($do[0] == "cave") { include('cave.php'); cave(); }
    if ($do[0] == "stash") { include('cave.php'); stash(); }
    if ($do[0] == "pool") { include('cave.php'); pool(); }
    if ($do[0] == "water") { include('cave.php'); water(); }
    if ($do[0] == "arena") { include('pvp.php'); arena(); }
    // Dueling
    if ($do[0] == "startduel") { include('pvp.php'); startduel(); }
    if ($do[0] == "acceptduel") { include('pvp.php'); acceptduel(); }
    if ($do[0] == "declineduel") { include('pvp.php'); declineduel(); }
    // Other.php random features such as options
    elseif ($do[0] == "options") { include('templates/other.php'); options($do[1]); }
    elseif ($do[0] == "forums") { include('templates/other.php'); forums($do[1]); }
    elseif ($do[0] == "mailadmin") { include('templates/other.php'); mailadmin($do[1]); }
    elseif ($do[0] == "notes") { include('templates/other.php'); notes($do[1]); }
    elseif ($do[0] == "profile") { include('templates/other.php'); profile($do[1]); }
    elseif ($do[0] == "archive") { include('templates/other.php'); doarchive($do[1]); }
    elseif ($do[0] == "users") { include('templates/other.php'); dolistmembers($do[1]); }
    elseif ($do[0] == "dueloption") { include('templates/other.php'); dueloption($do[1]); }
    elseif ($do[0] == "hideplayers") { include('templates/other.php'); hideplayers($do[1]); }
    elseif ($do[0] == "contact") { include('templates/other.php'); contact($do[1]); }
    elseif ($do[0] == "changeavatar") { include('templates/other.php'); changeavatar($do[1]); }
    elseif ($do[0] == "playerchat") { include('templates/other.php'); playerchat($do[1]); }
    elseif ($do[0] == "upgrade") { include('templates/other.php'); upgrade($do[1]); }
    elseif ($do[0] == "capture") { include('fight.php'); capture(); }
    elseif ($do[0] == "whosonline") { whosonline($do[1]); }
    elseif ($do[0] == "dotreasure") { dotreasure($do[1]); }
    elseif ($do[0] == "supportSite") {include('towns.php'); dobonus($do[1]); }
    elseif ($do[0] == "qitem") { include('qitem.php'); quickitems($do[1]); }
    elseif ($do[0] == "itemdel") { itemdel($do[1]); }
    elseif ($do[0] == "news") { news(); }
    // Exploring functions.
    elseif ($do[0] == "move") { include('explore.php'); move(); }
    elseif ($do[0] == "runon") { include('templates/other.php'); runon(); }
    elseif ($do[0] == "runoff") { include('templates/other.php'); runoff(); }
    elseif ($do[0] == "oasis") { include('explore.php'); oasis(); }
    // Fighting functions.
     elseif ($do[0] == "fight") { include('fight.php'); fight(); }
    elseif ($do[0] == "fatigue") { include('fight.php'); fatigue(); }
    elseif ($do[0] == "corpse") { include('fight.php'); corpse(); }
    elseif ($do[0] == "victory") { include('fight.php'); victory(); }
    elseif ($do[0] == "drop") { include('fight.php'); dropitem2(); }
    elseif ($do[0] == "dropitem") { include('fight.php'); dropitem(); }
    elseif ($do[0] == "take") { include('fight.php'); take(); }
    elseif ($do[0] == "dead") { include('fight.php'); dead(); }
    elseif ($do[0] == "attributes") { include('fight.php'); attributes(); }
    // Misc functions.
    elseif ($do[0] == "verify") { header("Location: users.php?do=verify"); die(); }
    elseif ($do[0] == "spell") { include('heal.php'); healspells($do[1]); }
    elseif ($do[0] == "onlinechar") { onlinechar($do[1]); }
    elseif ($do[0] == "showmap") { showmap(); }
    elseif ($do[0] == "viewitems") { viewitems(); }
    elseif ($do[0] == "daily") { daily(); }
        elseif ($do[0] == "collected") { collected(); }
    if ($do[0] == "viewpets") {doviewpets($do[1]); }
    elseif ($do[0] == "chat") { chat(); }
    //Item functions.
    elseif ($do[0] == "backpack" || $do[0] == "storage" || $do[0] == "backpackdropclean" || $do[0] == 'backpackitemclean') { require('storage.php'); itemstorage(); }
    elseif ($do[0] == "dropjunk") { require('storage.php'); dropjunk($do[1], $_GET['where']); }
    elseif ($do[0] == "selljunk") { require('storage.php'); dropjunk($do[1], $_GET['where'], 1); }
    elseif ($do[0] == "dropalljunk") { require('storage.php'); dropall($_GET[what], $_GET[where]); }
    elseif ($do[0] == "sellalljunk") { require('storage.php'); dropall($_GET[what], $_GET[where], 1); }
    elseif ($do[0] == "equipitem") { require('storage.php'); equipstoreditem($do[1], $_GET['where']); }
    elseif ($do[0] == "moveitem") { require('storage.php'); moveitem($do[1], $_GET['where']); }
    //Player Market
    elseif ($do[0] == "playermarket") { require('pmarket.php'); playermarket(); }
    elseif ($do[0] == "buyfrommarket") { require('pmarket.php'); buy_from_market($do[1]); }
    elseif ($do[0] == "cancellisting") { require('pmarket.php'); cancel_listing($do[1]); }
    elseif ($do[0] == 'viewlisting') { require('pmarket.php'); view_listing($do[1]); }


} else {  include('bonus.php');donothing(); }

function donothing() {

    global $userrow;
    $townquery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    $townrow = mysql_fetch_array($townquery);
    $towname = $townrow["name"]; //Display town name when in town.

    if ($userrow["currentaction"] == "In Town") {
        $page = dotown();
        $title = "$towname";
    } elseif ($userrow["currentaction"] == "Exploring") {
        $page = doexplore();
        $title = "Exploring";
    } elseif ($userrow["currentaction"] == "Oasis") {
        $page = dooasis();
        $title = "Oasis";
    } elseif ($userrow["currentaction"] == "Quicksand") {
        $page = doquicksand();
        $title = "Quicksand";
    } elseif ($userrow["currentaction"] == "Fighting")  {
        $page = dofight();
        $title = "Fighting";
    } elseif ($userrow["currentaction"] == "Treasure")  {
        $page = dotreasure();
        $title = "Treasure";
    } elseif ($userrow["currentaction"] == "Corpse")  {
        $page = docorpse();
        $title = "Found a Corpse";
        $updatequery = doquery("UPDATE {{table}} SET templist='corpse', location='Found a Corpse' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
    } elseif ($userrow["currentaction"] == "Outside Cave")  {
        $page = dooutsidecave();
        $title = "Outside a Cave";
        $updatequery = doquery("UPDATE {{table}} SET templist='cave', location='Outside a Cave' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
    } elseif ($userrow["currentaction"] == "Stronghold") {
	  header("Location: index.php?do=move:0");
	  die();
	  } elseif ($userrow["currentaction"] == "Home") {
	  header("Location: index.php?do=move:0");
	  die();
    } elseif ($userrow["currentaction"] == "Cave") {
    	$page = docave();
        $title = "Cave";
  $updatequery = doquery("UPDATE {{table}} SET templist='cave', location='Inside a Cave' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

       header("Location: index.php?do=cave");

	  die();
    } elseif ($userrow["currentaction"] == "Healing Pool") {
  $updatequery = doquery("UPDATE {{table}} SET templist='cave',currentaction='Healing Pool', location='Healing Pool' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	  header("Location: index.php?do=pool");
	  die();
    }

    display($page, $title);

}

function dotown() { // Spit out the main town page and capture IP

    global $userrow, $controlrow, $numqueries, $bonus;

    $townquery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) == 0) { display("There is an error with your user account, or with the town data. Please try again or contact the Administrator.","Error"); }
    $townrow = mysql_fetch_array($townquery);

    $ipquery = doquery("UPDATE {{table}} SET templist='0',location='{{currenttown2}}',ipaddress='".$_SERVER["REMOTE_ADDR"]."' WHERE username='".$userrow["username"]."' LIMIT 1","users");

    ///Bonus
    $time=time();
	$query = doquery("SELECT UNIX_TIMESTAMP(bonusTime) from {{table}} WHERE id='".$userrow["id"]."' LIMIT 1", "users");
    $data=mysql_fetch_array($query);
	$lastTime=$data[0];

    if(($time-$lastTime)>$bonus["time"]){
      $townrow["bonus"]='<a href="index.php?do=supportSite">Daily Bonus Arena</a>';
    }else{
      $townrow["bonus"]='<a href="index.php?do=supportSite" class="done">Daily Bonus Arena</a><br><br><font color=red>You have collected your Bonus already, please wait until your 24hours are up.</font>';
	}

    $page = gettemplate("towns");
    $page = parsetemplate($page, $townrow);

    return $page;

}

function news()

{
global $controlrow, $userrow;

$updatequery = doquery("UPDATE {{table}} SET location='Viewing News' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
if ($controlrow["shownews"] == 1) {
$page = "<table width='100%' border='1'><tr><td class=\"title\"><img src=\"images/titles/title_wb.gif\" alt=\"Welcome back!\" /></td></tr></table>";
       $newsquery = doquery("SELECT * FROM {{table}} ORDER BY id DESC LIMIT 8", "news");
         $page .= "<p><p><center><img src='images/main.gif' alt=\"Dragon's Kingdom RPG\"></center><p>You last logged in at <b>".$userrow["onlinetime"]."</b> with the IP Address of <b>".$userrow["ipaddress"]."</b>.<p><center><i>Please spare a moment to view the latest news and announcements below.</i></center>
         <p><center>You may [<a href=\"index.php\">Enter</a>] the Game.</center><p><table width='100%' border='1'><tr><td class=\"title\"><img src=\"images/titles/title_news.gif\" alt=\"Latest News and Announcements\" /></td></tr></table><table width=\"95%\">";
		while ($newsrow = mysql_fetch_assoc($newsquery)) {
			    $newsrow = str_replace(":)", "<img src='images/smilies/smile.gif'>", $newsrow); //16 Smilies
			    $newsrow = str_replace(":(", "<img src='images/smilies/sad.gif'>", $newsrow);
		        $newsrow = str_replace(":P", "<img src='images/smilies/tongue.gif'>", $newsrow);
			    $newsrow = str_replace(";)", "<img src='images/smilies/wink.gif'>", $newsrow);
			    $newsrow = str_replace("(ha)", "<img src='images/smilies/biggrin.gif'>", $newsrow);
			    $newsrow = str_replace("^^", "<img src='images/smilies/rolleyes.gif'>", $newsrow);
			    $newsrow = str_replace("o.O", "<img src='images/smilies/freak.gif'>", $newsrow);
			    $newsrow = str_replace(":$", "<img src='images/smilies/embaressed.gif'>", $newsrow);
			    $newsrow = str_replace("(c)", "<img src='images/smilies/cool.gif'>", $newsrow);
			    $newsrow = str_replace(":@", "<img src='images/smilies/mad.gif'>", $newsrow);
			    $newsrow = str_replace(":/", "<img src='images/smilies/umm.gif'>", $newsrow);
			    $newsrow = str_replace(":O", "<img src='images/smilies/shocked.gif'>", $newsrow);
			    $newsrow = str_replace(":?", "<img src='images/smilies/ques-tion.gif'>", $newsrow);
			    $newsrow = str_replace(":!", "<img src='images/smilies/exclamation.gif'>", $newsrow);
			    $newsrow = str_replace(":D", "<img src='images/smilies/lol.gif'>", $newsrow);
			    $newsrow = str_replace(":%", "<img src='images/smilies/drool.gif'>", $newsrow);
			$page .= "<tr><td>\n<span class=\"news\">[".prettydate($newsrow["postdate"])."] ".$newsrow["title"]." - By ".$newsrow["author"]."</span><br /><br />".nl2br($newsrow["content"]);
			$page .= "</td></tr>\n";
			$numquery = doquery("SELECT * FROM {{table}} WHERE topic=".$newsrow['id']."", "comments");
			$comments = mysql_num_rows($numquery);
			$page .= "<tr><td><a href=index.php?do=comments:".$newsrow['id'].">Post Comments</a> ($comments) <hr></td></tr>";
		}
        $page .= "</table>\n";

        } else { $townrow["news"] = "News is offline."; }

	display($page, "Welcome back!");
}


function doexplore() { // Main exploring page
global $userrow;

if ($userrow["nearbylist"] == 1) {

$nearbyquery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' AND charname!='".$userrow["charname"]."' AND authlevel!='1' ORDER BY LEVEL DESC", "users");

	$nearby = "<p><p><center><table width=\"85%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"5\" style=\"background-color:#dddddd;\"><center>Nearby Players - In order of Level</center></th></tr><tr><th width=\"1%\" style=\"background-color:#dddddd;\">Character Name</th><th width=\"5%\" style=\"background-color:#dddddd;\">Location</th><th width=\"2%\" style=\"background-color:#dddddd;\">Level</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Attack</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Mug</th></tr>";

        $count = 2;
        if (mysql_num_rows($nearbyquery) == 0) {
       $nearby .= "<td style='background-color:#ffffff;' colspan='5'><b>No players nearby.</b></tr></td></table></table>\n";
        } else {
	    while ($nearbyrow = mysql_fetch_array($nearbyquery)) {
	    $lastactive = strtotime($nearbyrow['onlinetime']);
	    $nowtime = time();
	    $timesincelast = $nowtime - $lastactive;

	    if (($userrow["latitude"] <= 15 && $userrow["latitude"] >= -15) && ($userrow["longitude"] <= 15 && $userrow["longitude"] >= -15) || $userrow["currentaction"] == "In Town") {
	    $namelink2 = "<font color=green>Safe Zone</font>";
	    }
	    elseif ($timesincelast <= 120) {
	  	$namelink2 = "<a href='index.php?do=startduel&id=".$nearbyrow["id"]."'>Request</a>";
	    }
	    elseif ($timesincelast <= 600) {
	  	$namelink2 = "<font color=blue>Idle</font>";
	  	} else {
	  		$namelink2 = "<font color=red>Not Online</font>";
	  	}

	    if (($userrow["latitude"] <= 15 && $userrow["latitude"] >= -15) && ($userrow["longitude"] <= 15 && $userrow["longitude"] >= -15) || $userrow["currentaction"] == "In Town") {
	  	$namelink1 = "<font color=green>Safe Zone</font>";
	  	} else {
	  		$namelink1 = "Soon";
	  	}

		if ($nearbyrow["latitude"] < 0) { $nearbyrow["latitude"] = $nearbyrow["latitude"] * -1 . "S"; } else { $nearbyrow["latitude"] .= "N"; }
        if ($nearbyrow["longitude"] < 0) { $nearbyrow["longitude"] = $nearbyrow["longitude"] * -1 . "W"; } else { $nearbyrow["longitude"] .= "E"; }

		if ($count == 1) { $color = "bgcolor='#ffffff'"; $count = 2; }
		else { $color = "bgcolor='#eeeeee'"; $count = 1;}
		$nearby .= "<tr><td ".$color." width='15%'>";
		$nearby .= "<a href=\"index.php?do=onlinechar:".$nearbyrow["id"]."\">".$nearbyrow["charname"]."</a></td>";
		$nearby .= "<td ".$color." width='5%'>".$nearbyrow["latitude"].", ".$nearbyrow["longitude"]."</td>";
		$nearby .= "<td ".$color." width='5%'>".$nearbyrow["level"]."</td>";

     	$nearby .= "<td ".$color." width='5%'>".$namelink2."</td>";
     	$nearby .= "<td ".$color." width='5%'>".$namelink1."</td>";
	  	$nearby .= "</tr>";
	}
	$nearby .= "</table></table></center>";

        }
	 } elseif ($userrow["nearbylist"] == 0) {

	 	$nearby .= "Nearby Players list hidden. You can Enable it again by visiting your <a href='index.php?do=hideplayers'>Player Options</a>.";


}

         $userrow["nearby"] = $nearby;
    // Exploring without a GET string is normally when they first log in, or when they've just finished fighting.

$page = <<<END
<table width="95%">
<tr><td class="title"><img src="images/title_exploring.gif" alt="Exploring" /></td></tr>
<tr><td>
You are exploring the mighty battle field, and you find nothing but waste land and desert. To continue exploring this deserted land, use the Compass or the Travel To menus. <p>Be aware of whats's approaching you next... As you struggle to see through the haze of dust, from the sand.<p><center>$nearby</center>
</td></tr>
</table>
END;

    return $page;

}

function doquicksand() { // Quicksand - 1 in every 100 to find scales AND gold. scales = between 1 to user level divide by 7. gold = between 1 to user level
    global $userrow, $controlrow, $numqueries;

    $damage = intval(rand(1,$userrow["level"]));
    $newhp = $userrow["currenthp"] - $damage;

$updatequery = doquery("UPDATE {{table}} SET currentaction='Exploring',location='Quicksand',currenthp='$newhp' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

{
}
$page = <<<END
<table width="450">
<tr><td class="title"><img src="images/title_quicksand.gif" alt="Quicksand" /></td></tr>
<tr><td>
You are exploring the mighty battle field, and you stumble across a refreshing Oasis! You take a closer look...<p><font color=red>You then rub your eyes to realise that it's just a mirage and you begin sinking into the Quicksand. You are hurt for $damage Damage!</font><p><center><img src="images/quicksand.jpg" alt="Quick Sand" border="0" /></center><p>To continue exploring this deserted land, use the Compass or the Travel To menus. Be aware of whats's approaching you next... As you struggle to see through the haze of dust, from the sand.
</td></tr>
</table>
END;
    return $page;
}

function dooutsidecave() { // Cave

$updatequery = doquery("UPDATE {{table}} SET location='Outside a Cave' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

$page = <<<END
<table width="450">
<tr><td class="title"><img src="images/title_cave.gif" alt="Cave" /></td></tr>
<tr><td>
You are exploring the mighty battle field, to discover an ancient Cave.<p>You may enter the dark <a href="index.php?do=cave">Cave</a>.<p><center><a href="index.php?do=cave"><img src="images/entercave.jpg" alt="Enter Cave" border="0" /></a></center><p>To continue exploring this deserted land, use the Compass or the Travel To menus. Be aware of whats's approaching you next... As you struggle to see through the haze of dust, from the sand.
</td></tr>
</table>
END;

    return $page;

}

function docorpse() { // Corpse

$updatequery = doquery("UPDATE {{table}} SET location='Found a Corpse' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

$page = <<<END
<table width="450">
<tr><td class="title"><img src="images/title_corpse.gif" alt="Found a Corpse" /></td></tr>
<tr><td>
You are exploring the mighty battle field, to stumble upon a monster's corpse.<p>You may <a href="index.php?do=corpse">search</a> the insect-ridden monster's corpse, to see what you can find.<p>To continue exploring this deserted land, use the Compass or the Travel To menus. Be aware of whats's approaching you next... As you struggle to see through the haze of dust, from the sand.
</td></tr>
</table>
END;

    return $page;

}

function docave() { // Cave

$updatequery = doquery("UPDATE {{table}} SET location='Outside a Cave' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

$page = <<<END
<table width="450">
<tr><td class="title"><img src="images/title_cave.gif" alt="Cave" /></td></tr>
<tr><td>
You are exploring the mighty battle field, to discover an ancient Cave.<p>You may enter the dark <a href="index.php?do=cave">Cave</a>.<p><center><a href="index.php?do=cave"><img src="images/entercave.jpg" alt="Enter Cave" border="0" /></a></center><p>To continue exploring this deserted land, use the Compass or the Travel To menus. Be aware of whats's approaching you next... As you struggle to see through the haze of dust, from the sand.
</td></tr>
</table>
END;

    return $page;

}

function dooasis() { // Oasis while exploring to restore health

$updatequery = doquery("UPDATE {{table}} SET location='Oasis' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

$page = <<<END
<table width="450">
<tr><td class="title"><img src="images/title_oasis.gif" alt="Oasis" /></td></tr>
<tr><td>
You are exploring the mighty battle field, and you stumble across a refreshing Oasis!<p>You may restore your Health and Travel Points by visiting the <a href="index.php?do=oasis">Oasis</a>.<p><center><a href="index.php?do=oasis"><img src="images/oasis.jpg" alt="Enter Oasis" border="0" /></a></center><p>To continue exploring this deserted land, use the Compass or the Travel To menus. Be aware of whats's approaching you next... As you struggle to see through the haze of dust, from the sand.
</td></tr>
</table>
END;

    return $page;

}

function dofight() { // Redirect to fighting.

    header("Location: index.php?do=fight");

}

function dotreasure() { // Treasure Page - 1 in every 100 to find scales AND gold. scales = between 1 to user level divide by 7. gold = between 1 to user level
    global $userrow, $controlrow, $numqueries;
if (rand(1,100) >= 3) {
    $treasure = intval(rand(1,$userrow["level"]*3) + ($userrow["magicfind"]*2));
    $newgold = $userrow["gold"] + $treasure;
    if ($newgold > 9999999) {$newgold = $newgold - 9999999;}
    $treasuretype = "Gold";
$updatequery = doquery("UPDATE {{table}} SET currentaction='Exploring',location='Treasure Chest',gold='$newgold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
}
else {
    $treasure = intval(rand(1,($userrow["level"]/5) + ($userrow["magicfind"]/4)));
    if ($treasure <= 1) {$treasure = 1;}
    $newscales = $userrow["dscales"] + $treasure;
    if ($newscales > 99999) {$newscales = $newscales - 99999;}
    $treasuretype = "Dragon Scale(s)";
$updatequery = doquery("UPDATE {{table}} SET currentaction='Exploring', dscales='$newscales' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
}
$page = <<<END
<table width="450">
<tr><td class="title"><img src="images/title_treasure.gif" alt="Treasure" /></td></tr>
<tr><td>
While exploring the battle field outside of town, you come across a deserted treasure chest.<p>It appears to have something inside, so you take a closer look at it finding it contains <font color=green><b>$treasure</b> <b>$treasuretype</b></font> inside of it.<p><center><img src="images/icon_chest.gif" alt="Treasure" /></center>
</td></tr>
</table>
END;
    return $page;
}

function viewitems() {

    global $userrow, $controlrow;

    $inventitemquery = doquery("SELECT id,name,description FROM {{table}}","inventitems");
    $userinventitems = explode(",",$userrow["inventitems"]);
    $userrow["inventitemslist"] = "";
    while ($inventitemrow = mysql_fetch_array($inventitemquery)) {
        $inventitem = false;
        foreach($userinventitems as $a => $b) {
            if ($b == $inventitemrow["id"]) {
		   $userrow["inventitemslist"] .= "[<a href='index.php?do=itemdel:".$inventitemrow["id"]."'>Drop</a>] <b>".$inventitemrow["name"]."</b> - <i>(".$inventitemrow["description"].")</i><br> ";
		}
        }
    }
    if ($userrow["inventitemslist"] == "") { $userrow["inventitemslist"] = "No Items Available"; }

    // Make page tags for XHTML validation.
    $xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n"
    . "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"DTD/xhtml1-transitional.dtd\">\n"
    . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n";



        $charsheet = gettemplate("items");
    $page = parsetemplate($charsheet, $userrow);
    display($page, "Items");

}

function doviewpets($id) {
	global $userrow;

	$updatequery = doquery("UPDATE {{table}} SET location='Viewing Pets' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	$page = "<table width='100%'><tr><td class='title'>Viewing Pets</td></tr></table>";
	$petquery = doquery("SELECT * FROM {{table}} WHERE trainer='".$userrow["charname"]."'", "arena");
	if (mysql_num_rows($petquery) <= 0) {
	$page .= "You do not have any Captured Pets.<p>";
	$page .= "In order to capture pets, you must use a capture spell or capture item ";
	$page .= "in battle against an enemy.  If the spell is strong enough to hold the creature, ";
	$page .= "and the monster has been weakened enough (low HP) you can capture it as a Pet. You can then visit a Pet Arena from within a Guild Stronghold to battle them.<p>";
	$page .= "You may <a href='index.php'>return</a> to what you were doing.";
	display($page, "Viewing Pets");
	}

	if (!isset($id)) {
	$page .= "<p>Here is a list of all of your Captured Pets. ";
	$page .= "To battle them against other Pets, you must visit the Pet Arena from within a Guild Stronghold.";
	$page .= "<p><center><table width='95%' style='border: solid 1px black' cellspacing='0' cellpadding='0'>";
	$page .= "<tr><td colspan='5' bgcolor='#fffff'><center><b>Your Captured Pets</b></center></td></tr>";
	$page .= "<tr><td><b>Name</b></td><td><b>Species</b></td><td><b>HP</b></td><td><b>Level</b></td><td><b>Win/Loss</b></td></tr>";
	$count = 2;
	while ($petrow = mysql_fetch_array($petquery)) {
		if ($count == 1) { $color = "bgcolor='#ffffff'"; $count = 2; }
		else { $color = "bgcolor='#eeeeee'"; $count = 1;}
		$page .= "<tr><td ".$color." width='25%'>";
		$page .= "".$petrow["name"]."</a></td>";
		$page .= "<td ".$color." width='25%'>".$petrow["type"]."</td>";
		$page .= "<td ".$color." width='20%'>".$petrow["currenthp"]."/".$petrow["maxhp"]."</td>";
		$page .= "<td ".$color." width='20%'>".$petrow["level"]."</td>";
		$page .= "<td ".$color." width='20%'>".$petrow["wins"]."/".$petrow["losses"]."</td>";
	  	$page .= "</tr>";
	}}
	$page .= "</table></center>";

    display($page,"Viewing Pets");
}

function onlinechar($id) {

    global $controlrow;
    $userquery = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "users");
    if (mysql_num_rows($userquery) == 1) { $userrow = mysql_fetch_array($userquery); } else { display("No such user.", "Error"); }
    if ($userrow["id"] == 1) { display("<table width='100%' border='1'><tr><td class='title'>Restricted</td></tr></table><p>You cannot view an Administrators Profile. <p>You may return to what you were <a href=\"index.php\">doing</a>, or continue exploring by using the compass images to the right.", "Restricted"); die(); }

    $townquery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    $townrow = mysql_fetch_array($townquery);
                    $userquery = doquery("SELECT * FROM {{table}} WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        unset($userrow);
        $userrow = mysql_fetch_array($userquery);

        // Current town name.
        if ($userrow["currentaction"] == "In Town") {
            $townquery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
            $townrow = mysql_fetch_array($townquery);
            $userrow["currenttown"] = "".$townrow["name"]."";
        } else {
            $userrow["currenttown"] = "Not in a Town";
        }
                    if ($userrow["currentaction"] == "In Town") {
            $town2query = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
            $town2row = mysql_fetch_array($town2query);
            $userrow["currenttown2"] = "".$townrow["name"]."";
            }

         $townslist = explode(",",$userrow["towns"]);
        $townquery2 = doquery("SELECT * FROM {{table}} ORDER BY id", "towns");
        $userrow["townslist"] = "";
        while ($townrow2 = mysql_fetch_array($townquery2)) {
            $town = false;
            foreach($townslist as $a => $b) {
                if ($b == $townrow2["id"]) { $town = true; }
            }
            if ($town == true) {
                $userrow["townslist"] .= "".$townrow2["name"]."<br />\n";
            }
        }

        // Format various userrow stuffs...
        if ($userrow["latitude"] < 0) { $userrow["latitude"] = $userrow["latitude"] * -1 . "S"; } else { $userrow["latitude"] .= "N"; }
        if ($userrow["longitude"] < 0) { $userrow["longitude"] = $userrow["longitude"] * -1 . "W"; } else { $userrow["longitude"] .= "E"; }
             if ($userrow["run"] == 1) { $userrow["run"] = "Walking"; }
        if ($userrow["run"] == 3) { $userrow["run"] = "Running"; }
        $userrow["totalexp"] = ($userrow["experience"] + $userrow["miningxp"] + $userrow["endurancexp"] + $userrow["smeltingxp"] + $userrow["muggingxp"] + $userrow["craftingxp"] + $userrow["forgingxp"]);
        $userrow["skilltotal"] = ($userrow["level"] + $userrow["mining"] + $userrow["endurance"] + $userrow["smelting"] + $userrow["mugging"] + $userrow["crafting"]  + $userrow["forging"] + $userrow["skill1level"] + $userrow["skill2level"] + $userrow["skill3level"] + $userrow["skill4level"]);
    // Format various userrow stuffs.
    $userrow["totalexp"] = number_format($userrow["totalexp"]);
    $userrow["experience"] = number_format($userrow["experience"]);
        $userrow["miningxp"] = number_format($userrow["miningxp"]);
        $userrow["smeltingxp"] = number_format($userrow["smeltingxp"]);
        $userrow["endurancexp"] = number_format($userrow["endurancexp"]);
         $userrow["craftingxp"] = number_format($userrow["craftingxp"]);
         $userrow["forgingxp"] = number_format($userrow["forgingxp"]);
         $userrow["currenthp"] = number_format($userrow["currenthp"]);
         $userrow["maxhp"] = number_format($userrow["maxhp"]);
         $userrow["currentmp"] = number_format($userrow["currentmp"]);
         $userrow["maxmp"] = number_format($userrow["maxmp"]);
         $userrow["currenttp"] = number_format($userrow["currenttp"]);
         $userrow["maxtp"] = number_format($userrow["maxtp"]);
$userrow["attributes"] = number_format($userrow["attributes"]);
$userrow["bones"] = number_format($userrow["bones"]);
$userrow["defensepower"] = number_format($userrow["defensepower"]);
$userrow["attackpower"] = number_format($userrow["attackpower"]);
$userrow["strength"] = number_format($userrow["strength"]);
$userrow["dexterity"] = number_format($userrow["dexterity"]);
    $userrow["gold"] = number_format($userrow["gold"]);
        $userrow["dscales"] = number_format($userrow["dscales"]);
    $userrow["bank"] = number_format($userrow["bank"]);
            if (($userrow["latitude"] <= 15 && $userrow["latitude"] >= -15) && ($userrow["longitude"] <= 15 && $userrow["longitude"] >= -15) || $userrow["currentaction"] == "In Town") { $userrow["zone"] = "<font color=green><b>Safe Zone</b></font>"; }
     else {
         $userrow["zone"] = "<font color=red><b>Danger Zone</b></font>";
    }

            if ($townrow["latitude"] >= 0) { $latitude = $townrow["latitude"] . "N,"; } else { $latitude = ($townrow["latitude"]*-1) . "S,"; }
        if ($townrow["longitude"] >= 0) { $longitude = $townrow["longitude"] . "E"; } else { $longitude = ($townrow["longitude"]*-1) . "W"; }
    if ($userrow["expbonus"] > 0) {
        $userrow["plusexp"] = "<span class=\"light\">(+".$userrow["expbonus"]."%)</span>";
    } elseif ($userrow["expbonus"] < 0) {
        $userrow["plusexp"] = "<span class=\"light\">(".$userrow["expbonus"]."%)</span>";
    } else { $userrow["plusexp"] = ""; }
    if ($userrow["goldbonus"] > 0) {
        $userrow["plusgold"] = "<span class=\"light\">(+".$userrow["goldbonus"]."%)</span>";
    } elseif ($userrow["goldbonus"] < 0) {
        $userrow["plusgold"] = "<span class=\"light\">(".$userrow["goldbonus"]."%)</span>";
    } else { $userrow["plusgold"] = ""; }

    $levelquery = doquery("SELECT ". $userrow["charclass"]."_exp FROM {{table}} WHERE id='".($userrow["level"]+1)."' LIMIT 1", "levels");
    $levelrow = mysql_fetch_array($levelquery);
    $userrow["nextlevel"] = number_format($levelrow[$userrow["charclass"]."_exp"]);
    $titi = $userrow["customtitle"];
$titi2 = $userrow["avatarlink"];
    if ($userrow["authlevel"] == 3) { $userrow["avatarlink"] = "<img src=\"$titi2\" alt=\"$titi\" width=\"80\" height=\"80\">"; }
 else { $userrow["avatarlink"] = "<img src=\"$titi2\" alt=\"$titi\" width=\"60\" height=\"60\">"; }
    if ($userrow["charclass"] == 1) { $userrow["charclass"] = $controlrow["class1name"]; }
    elseif ($userrow["charclass"] == 2) { $userrow["charclass"] = $controlrow["class2name"]; }
    elseif ($userrow["charclass"] == 3) { $userrow["charclass"] = $controlrow["class3name"]; }
    elseif ($userrow["charclass"] == 4) { $userrow["charclass"] = $controlrow["class4name"]; }
    elseif ($userrow["charclass"] == 5) { $userrow["charclass"] = $controlrow["class5name"]; }
    elseif ($userrow["charclass"] == 6) { $userrow["charclass"] = $controlrow["class6name"]; }
    elseif ($userrow["charclass"] == 7) { $userrow["charclass"] = $controlrow["class7name"]; }

    $spellquery = doquery("SELECT id,name FROM {{table}}","spells");
    $userspells = explode(",",$userrow["spells"]);
    $userrow["magiclist"] = "";
    while ($spellrow = mysql_fetch_array($spellquery)) {
        $spell = false;
        foreach($userspells as $a => $b) {
            if ($b == $spellrow["id"]) { $spell = true; }
        }
        if ($spell == true) {
            $userrow["magiclist"] .= $spellrow["name"]."<br />";
        }
    }

    if ($userrow["magiclist"] == "") { $userrow["magiclist"] = "None"; }

    $inventitemquery = doquery("SELECT id,name,description FROM {{table}}","inventitems");
    $userinventitems = explode(",",$userrow["inventitems"]);
    $userrow["inventitemslist"] = "";
    while ($inventitemrow = mysql_fetch_array($inventitemquery)) {
        $inventitem = false;
        foreach($userinventitems as $a => $b) {
            if ($b == $inventitemrow["id"]) {
		   $userrow["inventitemslist"] .= "".$inventitemrow["name"]."<br> ";
		}
        }
    }
    if ($userrow["inventitemslist"] == "") { $userrow["inventitemslist"] = "No Items Available"; }

    $charsheet = gettemplate("onlinechar");
    $page = parsetemplate($charsheet, $userrow);
    display($page, "Character Profile");

}

function showmap() {

    global $userrow;

    // Make page tags for XHTML validation.
    $xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n"
    . "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"DTD/xhtml1-transitional.dtd\">\n"
    . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n";


    $page = $xml . gettemplate("minimal");

    $array = array("content"=>"<center><object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0\" width=\"585\" height=\"605\">
  <param name=\"movie\" value=\"onlinemap.swf\">
  <param name=\"quality\" value=\"high\">
  <embed src=\"onlinemap.swf\" quality=\"high\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" type=\"application/x-shockwave-flash\" width=\"585\" height=\"605\"></embed>
</object></center>", "title"=>"Dragons Kingdom Map");
    echo parsetemplate($page, $array);

			$numquery = doquery("SELECT * FROM {{table}} ", "strongholds");
			$strongholds = mysql_num_rows($numquery);

			$numquery = doquery("SELECT * FROM {{table}} ", "homes");
			$homes = mysql_num_rows($numquery);

echo "There are a total of : <b>$strongholds Strongholds</b> between the areas of 25 and 500 in any direction. There is also <b>$homes</b> Homes located around the map.<br><i>If the map above fails to load, or you dont have flash enabled, click <a href=\"http://dk-rpg.com/templates/map.php\">here</a> for the normal map image.</i>" ;


    die();

}

function itemdel($id) { // Spit out the main town page.
	global $userrow;
$updatequery = doquery("UPDATE {{table}} SET location='Dropping an Item' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	if (isset($_POST["submit"])) {
	$page = "<table width='100%'><tr><td class='title'>Dropped an Item</td></tr></table>";
	$userinventitems = explode(",",$userrow["inventitems"]);
	if(in_array($id, $userinventitems)) {
	unset($userinventitems[array_search($id, $userinventitems)]);
	$page .= "You dropped item# ".$id." <p>";
	}
	rsort($userinventitems);
	$newinventitems = join(",",$userinventitems);
	$iquery = doquery("UPDATE {{table}} SET inventitems='$newinventitems' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	header("Location: index.php"); die();
	} elseif (isset($_POST["cancel"])) {
		header("Location: index.php"); die();
	}
	$iquery = doquery("SELECT name FROM {{table}} WHERE id='$id' LIMIT 1 ","inventitems");
	$irow = mysql_fetch_array($iquery);
	$page = "<table width='100%'><tr><td class='title'>Dropping an Item</td></tr></table>";
	$page .= "<p>Are you sure that you want to drop <b>".$irow["name"]."</b>?<p>";
	$page .= "<form action='index.php?do=itemdel:".$id."' method='POST'>";
	$page .= "<input type='submit' name='submit' value='Drop Item'> - ";
	$page .= "<input type='submit' name='cancel' value='Cancel'>";
	$page .= "</form>";
	display($page, "Dropping an Item");
}

function whosonline()

{
global $userrow;

            $townquery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
            $townrow = mysql_fetch_array($townquery);

$updatequery = doquery("UPDATE {{table}} SET location='Viewing Whos Online' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

        $page = "<table width='100%' border='1'><tr><td class='title'>Who is Online</td></tr></table><br>";

  $onlinequery = doquery("SELECT * FROM {{table}} WHERE UNIX_TIMESTAMP(onlinetime) >= '".(time()-600)."' ORDER BY experience DESC", "users");
         $page .= "<center><table width=\"95%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"4\" style=\"background-color:#dddddd;\"><center>There have been " . mysql_num_rows($onlinequery) . " user(s) Online within the last few minutes, in order<br>of experience: </center></th></tr><th width=\"30%\" style=\"background-color:#dddddd;\">Character Name</th><th width=\"30%\" style=\"background-color:#dddddd;\">Guild Name</th><th width=\"5%\" style=\"background-color:#dddddd;\">Level</th><th width=\"30%\" style=\"background-color:#dddddd;\">Currently</th></tr>\n";

    $count = 1;


     while ($onlinerow = mysql_fetch_array($onlinequery)) {
     	    if ($onlinerow["location"] == "{{currenttown2}}") {
            $town2query = doquery("SELECT name,longitude,latitude FROM {{table}} WHERE latitude='".$onlinerow["latitude"]."' AND longitude='".$onlinerow["longitude"]."' LIMIT 1", "towns");
            $town2row = mysql_fetch_array($town2query);
            $onlinerow["location"] = "".$town2row["name"]."";
            }
        if ($onlinerow["authlevel"] == 1){
	   $page .= "\n";

	}elseif($onlinerow["authlevel"] == 3) {
           $page .= "<tr><td style=\"background-color:green;\"><a href=\"index.php?do=onlinechar:".$onlinerow["id"]."\">".$onlinerow["charname"]."</a></td><td style=\"background-color:green;\">".$onlinerow["guildname"]."</td><td style=\"background-color:green;\"><center>".$onlinerow["level"]."</center></td><td style=\"background-color:green;\">".$onlinerow["location"]."</td></tr>\n";

		}elseif($onlinerow["authlevel"] == 4) {
           $page .= "<tr><td style=\"background-color:blue;\"><a href=\"index.php?do=onlinechar:".$onlinerow["id"]."\">".$onlinerow["charname"]."</a></td><td style=\"background-color:blue;\">".$onlinerow["guildname"]."</td><td style=\"background-color:blue;\"><center>".$onlinerow["level"]."</center></td><td style=\"background-color:blue;\">".$onlinerow["location"]."</td></tr>\n";

	}else{
	   if ($count == 1) {
             $page .= "<tr><td style=\"background-color:#ffffff;\"><a href=\"index.php?do=onlinechar:".$onlinerow["id"]."\">".$onlinerow["charname"]."</a></td><td style=\"background-color:#ffffff;\">".$onlinerow["guildname"]."</td><td style=\"background-color:#ffffff;\"><center>".$onlinerow["level"]."</center></td><td style=\"background-color:#ffffff;\">".$onlinerow["location"]."</td></tr>\n";
             $count = 2;
           } else {
             $page .= "<tr><td style=\"background-color:#eeeeee;\"><a href=\"index.php?do=onlinechar:".$onlinerow["id"]."\">".$onlinerow["charname"]."</a></td><td style=\"background-color:#eeeeee;\">".$onlinerow["guildname"]."</td><td style=\"background-color:#eeeeee;\"><center>".$onlinerow["level"]."</center></td><td style=\"background-color:#eeeeee;\">".$onlinerow["location"]."</td></tr>\n";
             $count = 1;
           }
        }
     }

    $page .= "</table></td></tr></table></center>";
    $page .= "<br>Click on a players Character Name to see their profile and other stats.<p><br><b><u>Colour Key</u></b>:<br><font color=red><b>Administrators (Usually Invisible)</b></font><br><font color=green><b>Moderators</b></font><br><font color=blue><b>Muted from Chat, Forum, News Comments and Game Mails</b></font><br><br>You may <a href=\"index.php\">return</a> to what you were doing, or use the compass on the right to start exploring.</center>";

    display($page, "Who is Online");
}

function daily() {
	global $userrow;
$updatequery = doquery("UPDATE {{table}} SET location='Daily Bonus Arena' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

  	$page = "<table width=\"100%\"><tr><td class=\"title\">Daily Bonus</td></tr></table>";



$page .= <<<END
<table>
<tr><td>

<body>
<a name="top"></a>
<h1>Daily Bonus</h1>
[ <a href='index.php'>Return to the game</a> ]

<br /><br /><hr />

<p><font color="green">You have received your Daily Bonus of 800 Gold and 12 Dragon Scales.</font>
<hr /><br />
<script type='text/javascript'>
<!--



//default banner house ad url
clicksor_default_url = '';

clicksor_layer_border_color = '#999999';
clicksor_layer_ad_bg = '#CCCCCC';
clicksor_layer_ad_link_color = '#000000';
clicksor_layer_ad_text_color = '#000000';
clicksor_text_link_bg = '';
clicksor_text_link_color = '#000FFF';

clicksor_enable_text_link = true;



clicksor_banner_border = '#999999';
clicksor_banner_ad_bg = '#CCCCCC';
clicksor_banner_link_color = '#000000';
clicksor_banner_text_color = '#000000';
//-->
</script>
<script type='text/javascript' src='http://ads.clicksor.com/showAd.php?pid=5918&sid=57935&adtype=2'></script>               


<noscript><a href='http://www.clicksor.com'>Contextual advertising</a></noscript></center>
<br /><br />
<p>
</body>

<p>
</td></tr>
</table>
END;

    display($page,"Daily Bonus");
}

function collected() {
	global $userrow;
$updatequery = doquery("UPDATE {{table}} SET location='Daily Bonus Arena' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

  	$page = "<table width=\"100%\"><tr><td class=\"title\">Daily Bonus</td></tr></table>";



$page .= <<<END
<table>
<tr><td>

<body>
<a name="top"></a>
<h1>Daily Bonus</h1>
[ <a href='index.php'>Return to the game</a> ]

<br /><br /><hr />

<p><font color="red">You have already received your Daily Bonus of 800 Gold and 12 Dragon Scales. Please try again later.</font>

<hr /><br /><center>
<script type='text/javascript'>
<!--



//default banner house ad url
clicksor_default_url = '';

clicksor_layer_border_color = '#999999';
clicksor_layer_ad_bg = '#CCCCCC';
clicksor_layer_ad_link_color = '#000000';
clicksor_layer_ad_text_color = '#000000';
clicksor_text_link_bg = '';
clicksor_text_link_color = '#000FFF';

clicksor_enable_text_link = false;



clicksor_banner_border = '#999999';
clicksor_banner_ad_bg = '#CCCCCC';
clicksor_banner_link_color = '#000000';
clicksor_banner_text_color = '#000000';
//-->
</script>
<script type='text/javascript' src='http://ads.clicksor.com/showAd.php?pid=5918&sid=57935&adtype=2'></script>               


<noscript><a href='http://www.clicksor.com'>Contextual advertising</a></noscript></center>
<br /><br />

</body>

<p>
</td></tr>
</table>
END;

    display($page,"Daily Bonus");
}

function chat() {
    
    global $userrow, $controlrow;

$query = doquery("UPDATE {{table}} SET chattime=NOW() WHERE id='".$userrow["id"]."' LIMIT 1", "users");

    if ($controlrow["displaychat"] == 0) {
die("Player Chat has been closed for all Players. It will be available again later. Sorry for any inconvenience that this may cause."); }

    elseif ($userrow["level"] < 5) {
die("You must be at least level 5 or above to be able to view the Player Chat."); }

    elseif ($userrow["authlevel"] == 4) {
die("You are currently muted from using game mails, the forum, posting news comments and the player chat. Please check back later or contact a moderator."); }

    if (isset($_POST["babble"])) {
        $safecontent = makesafe($_POST["babble"]);	
		

        if ($safecontent == "" || $safecontent == " ") { //blank post. do nothing.
        } else { 
            if (substr($safecontent,0,2) == "/m") {
                $msgarray = explode(" ",$safecontent);
                unset($msgarray[0]);
                $to = $msgarray[1];
                unset($msgarray[1]);
                $safecontent = implode(" ",$msgarray);
            } else { $to = ""; }
            $insert = doquery("INSERT INTO {{table}} SET id='',posttime=NOW(),author='".$userrow["charname"]."',babble='$safecontent',touser='$to'", "chat"); 
        }
        header("Location: index.php?do=chat");
        die();
    }
    
    $babblebox = array("content"=>"");
    $bg = 1;
    $babblequery = doquery("SELECT * FROM {{table}} WHERE touser='' OR touser='".$userrow["charname"]."' OR author='".$userrow["charname"]."' ORDER BY id DESC LIMIT 20", "chat");
    while ($babblerow = mysql_fetch_array($babblequery)) {
			    $babblerow = str_replace(":)", "<img src='images/smilies/smile.gif'>", $babblerow); //16 Smilies
			    $babblerow = str_replace(":(", "<img src='images/smilies/sad.gif'>", $babblerow);
		        $babblerow = str_replace(":P", "<img src='images/smilies/tongue.gif'>", $babblerow);
			    $babblerow = str_replace(";)", "<img src='images/smilies/wink.gif'>", $babblerow);
			    $babblerow = str_replace("(ha)", "<img src='images/smilies/biggrin.gif'>", $babblerow);
			    $babblerow = str_replace("^^", "<img src='images/smilies/rolleyes.gif'>", $babblerow);
			    $babblerow = str_replace("o.O", "<img src='images/smilies/freak.gif'>", $babblerow);
			    $babblerow = str_replace(":$", "<img src='images/smilies/embaressed.gif'>", $babblerow);
			    $babblerow = str_replace("(c)", "<img src='images/smilies/cool.gif'>", $babblerow);
			    $babblerow = str_replace(":@", "<img src='images/smilies/mad.gif'>", $babblerow);
			    $babblerow = str_replace(":/", "<img src='images/smilies/umm.gif'>", $babblerow);
			    $babblerow = str_replace(":O", "<img src='images/smilies/shocked.gif'>", $babblerow);
			    $babblerow = str_replace(":?", "<img src='images/smilies/ques-tion.gif'>", $babblerow);
			    $babblerow = str_replace(":!", "<img src='images/smilies/exclamation.gif'>", $babblerow);
			    $babblerow = str_replace(":D", "<img src='images/smilies/lol.gif'>", $babblerow);
			    $babblerow = str_replace(":%", "<img src='images/smilies/drool.gif'>", $babblerow);
			    $babblerow = str_replace("fuck", " <b><font color=red>[Word Censored]</font></b> ", $babblerow); 
			    $babblerow = str_replace("shit", " <b><font color=red>[Word Censored]</font></b> ", $babblerow);
			    $babblerow = str_replace("bastard", " <b><font color=red>[Word Censored]</font></b> ", $babblerow); 
			    $babblerow = str_replace("piss", " <b><font color=red>[Word Censored]</font></b> ", $babblerow); 
			    $babblerow = str_replace("cunt", " <b><font color=red>[Word Censored]</font></b> ", $babblerow); 		
			    $babblerow = str_replace("dick", " <b><font color=red>[Word Censored]</font></b> ", $babblerow); 			    	    			    			    			     			    
			    $babblerow = str_replace("bitch", " <b><font color=red>[Word Censored]</font></b> ", $babblerow); 	
			    $babblerow = str_replace("twat", " <b><font color=red>[Word Censored]</font></b> ", $babblerow); 
		 	
       $cu = doquery("SELECT authlevel FROM {{table}} WHERE charname='".$babblerow["author"]."' LIMIT 1", "users");
			$chatrow = mysql_fetch_array($cu);

            if ($chatrow["authlevel"] == 1) {
	    $name = "<font color=red>".$babblerow["author"].":</font>";
	    }
	    elseif ($chatrow["authlevel"] == 3) {
	  	$name = "<font color=green>".$babblerow["author"].":</font>";
	    }
	    elseif ($chatrow["authlevel"] == 4) {
	  	$name = "<font color=blue>".$babblerow["author"].":</font>";
	  	} else {
	  		$name = "".$babblerow["author"].":";
	  	}

        if ($babblerow["touser"] != "") { $spanbegin = "<span style=\"color: red;\">"; $spanend = "</span>"; } else { $spanbegin = ""; $spanend = ""; }
        if ($bg == 1) { $new = "<div style=\"width:98%; background-color:#eeeeee;\"><b>".$name."</b><br> $spanbegin".$babblerow["babble"]."$spanend</div>\n"; $bg = 2; }
        else { $new = "<div style=\"width:98%; background-color:#ffffff;\"><b>".$name."</b><br> ".stripslashes($babblerow["babble"])."</div>\n"; $bg = 1; } 
      


    $babblebox["content"] = $new . $babblebox["content"];
    }
    $babblebox["content"] .= "<center><form action=\"index.php?do=chat\" method=\"post\"><br><input type=\"text\" name=\"babble\" size=\"40\" maxlength=\"150\" /> <input type=\"submit\" name=\"submit\" value=\"Reply\" /> <input type=\"reset\" name=\"reset\" value=\"Clear\" /></form><br><b><font color=red>Please don't discuss Quests in chat. You <u>will</u> be muted.</font></b></center>";

        $onlinequery = doquery("SELECT * FROM {{table}} WHERE UNIX_TIMESTAMP(chattime) >= '".(time()-90)."' AND charname!='Admin' ORDER BY charname", "users");
        $babblebox["content"] .= "<table width=\"95%\"><tr><tr><td>\n";
        $babblebox["content"] .= "<font color=#336666><b>Players Chatting (" . mysql_num_rows($onlinequery) . "):</b></font> ";
        while ($onlinerow = mysql_fetch_array($onlinequery)) { 
   $babblebox["content"] .= "<b>".$onlinerow["charname"]."" . "</b>, "; }
        $babblebox["content"] .= rtrim($townrow["whosonline"], ", ");
        $babblebox["content"] .= "</td></tr></table>\n";
    // Make page tags for XHTML validation.
    $xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n"
    . "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"DTD/xhtml1-transitional.dtd\">\n"
    . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n";
    $page = $xml . gettemplate("chat");
    echo parsetemplate($page, $babblebox);
    die();

}


?>
