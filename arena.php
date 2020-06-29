<?php
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

$updatequery = doquery("UPDATE {{table}} SET location='Pet Arena' WHERE id='".$userrow["id"]."' LIMIT 1", "users");


$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "strongholds");
	if (mysql_num_rows($castlequery) == 0) {header("Location: index.php"); die();}

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

	if ($do[0] == "view") {doview($do[1]); }
	elseif ($do[0] == "spells") { spells($do[1]); }
	elseif ($do[0] == "spells1") { spells1($do[1]); }
	elseif ($do[0] == "spells2") { spells2($do[1]); }
	elseif ($do[0] == "spells3") { spells3($do[1]); }
	elseif ($do[0] == "spells4") { spells4($do[1]); }
	elseif ($do[0] == "spells5") { spells5($do[1]); }
	elseif ($do[0] == "spells6") { spells6($do[1]); }
	elseif ($do[0] == "train") { dotrain($do[1]); }
	elseif ($do[0] == "release") { dorelease($do[1]); }
	elseif ($do[0] == "practice") { dopractice($do[1]); }
	elseif ($do[0] == "train") { dotrain($do[1]); }
	elseif ($do[0] == "duel") { doduel($do[1]); }
	elseif ($do[0] == "duel2") { doduel2(); }
	elseif ($do[0] == "feed") { dofeed($do[1]); }
	elseif ($do[0] == "victory") { dovictory($do[1],$do[2],$do[3]); }
	elseif ($do[0] == "defeat") { dodefeat($do[1],$do[2],$do[3]); }


} else { donothing(); }


function donothing() {
	global $userrow;

$page = <<<END
<table width="100%" align='center'>
<tr><td class="title">Pet Arena</td></tr></table>
<p>You come here to feed and look after your captured Pets. You may also train them and battle other peoples Pets. Please note that Magic Res does not currently work so do not waste skill points on that.<p> For every duel you have, your guild automatically gets 1 Dragon Scale placed into its Stronghold Storage. However, you do not get this bonus Scale if you are only Practicing. All pets are fully restored to full health every 24 hours, for free.<p>
<table>
<tr><td>
<ul><b><u>General</u></b>
<li /><a href="arena.php?do=view">View Pets</a>
<li /><a href="arena.php?do=feed">Feed Pets</a>
<li /><a href="arena.php?do=spells">Buy Spells</a>
<li /><a href="arena.php?do=release">Release Pets</a><br>


</ul>
</td><td>
<ul><b><u>Train/Duel</u></b>
<li /><a href="arena.php?do=practice">Practice Duels</a>
<li /><a href="arena.php?do=duel">Duel Others</a>
<li /><a href="arena.php?do=train">Train Pets</a>
</ul>
</td></tr>
END;

$page .= "</table><center><p>You are limited to the amount of fights and wins you can have. If when you try to fight and you get sent back to this page, you must wait a while.<p><a href='strongholds.php'>Back to the Stronghold</a></center>";

    display($page,"Pet Arena");
}

function doview($id) {
	global $userrow;

	$updatequery = doquery("UPDATE {{table}} SET location='Viewing Pets' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	$page = "<table width='100%'><tr><td class='title'>Pet Arena - View your Pets</td></tr></table>";
	$petquery = doquery("SELECT * FROM {{table}} WHERE trainer='".$userrow["charname"]."'", "arena");
	if (mysql_num_rows($petquery) <= 0) {
	$page .= "You do not have any captured Pets to use in the Arena!<br>";
	$page .= "In order to capture pets, you must use a capture spell ";
	$page .= "in battle against an enemy.  If the spell is strong enough to hold the creature, ";
	$page .= "and the monster has been weakened enough (low HP) you can capture it as a Pet.<p>";
	$page .= "You may go back to <a href='strongholds.php'>the Stronghold</a>, or leave and ";
	$page .= "<a href='index.php?do=move:0'>continue exploring</a>.<p>";
	display($page, "Viewing Pets");
	}



	if (isset($_POST["submit"])) {
		$name = $_POST["rename"];
		$rename = doquery("UPDATE {{table}} SET name='$name' WHERE id='$id' LIMIT 1", "arena");
		$page .= "You have successfully renamed your pet!";
		$petquery = doquery("SELECT * FROM {{table}} WHERE id='$id' limit 1", "arena");
		$petrow = mysql_fetch_array($petquery);
		$page .= "<p>Here are all the details for your pet named<b>: ".$petrow["name"]."</b><p>";
		$page .= "<table border='0' width='99%'>";
		$page .= "<tr><td bgcolor='#eeeeee'><b>Name:</b></td><td bgcolor='#eeeeee'>";
		$page .= "<form action='arena.php?do=view:".$id."' method='POST'>";
		$page .= "<input type='text' name='rename' value='".$petrow["name"]."'>";
		$page .= "<input type='submit' name='submit' value='Rename'></form></td></tr>";
		$page .= "<tr><td colspan='2' bgcolor='#ffffff'>A level ".$petrow["level"]." ".$petrow["type"]."</td></tr>";
		$page .= "<tr><td bgcolor='#eeeeee'><b>Experience:</b></td><td bgcolor='#eeeeee'>".$petrow["experience"]." <i>(next:".($petrow["level"]*50)." )</i></td></tr>";
		$page .= "<tr><td bgcolor='#ffffff'><b>Health:</b></td><td bgcolor='#ffffff'>".$petrow["currenthp"]." / ".$petrow["maxhp"]."</td></tr>";
		$page .= "<tr><td bgcolor='#eeeeee'><b>Magic:</b></td><td bgcolor='#eeeeee'>".$petrow["currentmp"]." / ".$petrow["maxmp"]."</td></tr>";
		$page .= "<tr><td bgcolor='#ffffff'><b>Strength:</b></td><td bgcolor='#ffffff'>".$petrow["maxdam"]."</td></tr>";
		$page .= "<tr><td bgcolor='#eeeeee'><b>Dexterity:</b></td><td bgcolor='#eeeeee'>".$petrow["dexterity"]."</td></tr>";

		$page .= "<tr><td bgcolor='#ffffff'><b>Armor:</b></td><td bgcolor='#ffffff'>".$petrow["armor"]."</td></tr>";
		$page .= "<tr><td bgcolor='#eeeeee'><b>Magic Resistance:</b></td><td bgcolor='#eeeeee'>".$petrow["magicarmor"]."</td></tr>";

		$page .= "<tr><td bgcolor='#ffffff'><b>Wins:</b></td><td bgcolor='#ffffff'>".$petrow["wins"]."<br></td></tr>";
		$page .= "<tr><td bgcolor='#ffffff'><b>Last win:</b></td><td bgcolor='#ffffff'>".$petrow["lastwin"]."<br></td></tr>";
		$page .= "<tr><td bgcolor='#eeeeee'><b>Losses:</b></td><td bgcolor='#eeeeee'>".$petrow["losses"]."<br></td></tr>";
		$page .= "<tr><td bgcolor='#eeeeee'><b>Last Loss:</b></td><td bgcolor='#eeeeee'>".$petrow["lastloss"]."<br></td></tr>";
		$page .= "<tr><td bgcolor='#ffffff'><b>Skill Points:</b></td><td bgcolor='#ffffff'>".$petrow["skillpoints"]."<br></td></tr>";
		$page .= "</table>";
		$page .= "<center><br>You may<a href='arena.php'>Return to the Arena</a> if you have changed your mind.</center>";
    	display($page,"Pet Arena - View Pets");
	}
	if (!isset($id)) {
	$page .= "You can capture up to 5 different Pets to use within the Arena. ";
	$page .= "Some pets are better suited for different battles, depending on their ";
	$page .= "special abilities, immunities, and overall strengths and weaknesses.<p>";
	$page .= "<table width='95%' style='border: solid 1px black' cellspacing='0' cellpadding='0'>";
	$page .= "<tr><td colspan='5' bgcolor='#fffff'><center><b>Please Choose a Pet</b></center></td></tr>";
	$page .= "<tr><td><b>Name</b></td><td><b>Species</b></td><td><b>HP</b></td><td><b>Level</b></td><td><b>Win/Loss</b></td></tr>";
	$count = 2;
	while ($petrow = mysql_fetch_array($petquery)) {
		if ($count == 1) { $color = "bgcolor='#ffffff'"; $count = 2; }
		else { $color = "bgcolor='#eeeeee'"; $count = 1;}
		$page .= "<tr><td ".$color." width='25%'>";
		$page .= "<a href='arena.php?do=view:".$petrow["id"]."'>".$petrow["name"]."</a></td>";
		$page .= "<td ".$color." width='25%'>".$petrow["type"]."</td>";
		$page .= "<td ".$color." width='20%'>".$petrow["currenthp"]."/".$petrow["maxhp"]."</td>";
		$page .= "<td ".$color." width='20%'>".$petrow["level"]."</td>";
		$page .= "<td ".$color." width='20%'>".$petrow["wins"]."/".$petrow["losses"]."</td>";
	  	$page .= "</tr>";
	}
	$page .= "</table>";

	} else {   // if you chose to view a Pet...

	$petquery = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "arena");
	$petrow = mysql_fetch_array($petquery);
	if ($petrow["trainer"] != $userrow["charname"]) {
	$page .= "You do not own that pet!<br>";
	$page .= "You may only view, feed, and release pets that are under your control.<p>";
	$page .= "You may go back to <a href='strongholds.php'>the Stronghold</a>, or leave and ";
	$page .= "<a href='index.php?do=move:0'>continue exploring</a>.<p>";
	display($page, "Viewing Pets");
	}

	$petquery = doquery("SELECT * FROM {{table}} WHERE id='$id' limit 1", "arena");
	$petrow = mysql_fetch_array($petquery);
	$page .= "<p>Here are all the details for your pet named<b>: ".$petrow["name"]."</b><p>";
	$page .= "<table border='0' width='99%'>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Name:</b></td><td bgcolor='#eeeeee'>";
	$page .= "<form action='arena.php?do=view:".$id."' method='POST'>";
	$page .= "<input type='text' name='rename' value='".$petrow["name"]."'>";
	$page .= "<input type='submit' name='submit' value='Rename'></form></td></tr>";
	$page .= "<tr><td colspan='2' bgcolor='#ffffff'>A level ".$petrow["level"]." ".$petrow["type"]."</td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Experience:</b></td><td bgcolor='#eeeeee'>".$petrow["experience"]." <i>(next:".($petrow["level"]*50)." )</i></td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'><b>Health:</b></td><td bgcolor='#ffffff'>".$petrow["currenthp"]." / ".$petrow["maxhp"]."</td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Magic:</b></td><td bgcolor='#eeeeee'>".$petrow["currentmp"]." / ".$petrow["maxmp"]."</td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'><b>Strength:</b></td><td bgcolor='#ffffff'>".$petrow["maxdam"]."</td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Dexterity:</b></td><td bgcolor='#eeeeee'>".$petrow["dexterity"]."</td></tr>";

	$page .= "<tr><td bgcolor='#ffffff'><b>Armor:</b></td><td bgcolor='#ffffff'>".$petrow["armor"]."</td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Magic Resistance:</b></td><td bgcolor='#eeeeee'>".$petrow["magicarmor"]."</td></tr>";

	$page .= "<tr><td bgcolor='#ffffff'><b>Wins:</b></td><td bgcolor='#ffffff'>".$petrow["wins"]."<br></td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'><b>Last win:</b></td><td bgcolor='#ffffff'>".$petrow["lastwin"]."<br></td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Losses:</b></td><td bgcolor='#eeeeee'>".$petrow["losses"]."<br></td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Last Loss:</b></td><td bgcolor='#eeeeee'>".$petrow["lastloss"]."<br></td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'><b>Skill Points:</b></td><td bgcolor='#ffffff'>".$petrow["skillpoints"]."<br></td></tr>";

	$page .= "</table>";
	}
    $page .= "<center><br>You may <a href='arena.php'>Return to the Arena</a> if you have changed your mind.</center>";
    display($page,"Pet Arena - View Pets");
}

function spells() { // Purchase capture spells

    global $userrow, $numqueries;

        if (isset($_POST["submit"])) {

$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "strongholds");
	if (mysql_num_rows($castlequery) == 0) {header("Location: index.php"); die();}


    } elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

    } else {

        $title = "Buy Spells";

$updatequery = doquery("UPDATE {{table}} SET location='Buying Spells' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

        $page = "<table width='100%' border='1'><tr><td class='title'>Buy Spells</td></tr></table><p>";
        $page .= "Welcome to my Outpost.<p>I am the Legendry Warlock.<p>You can purchase precious Stones from me, which will give you the power of Capturing higher leveled Pets. The level which appears on the Spell, is the highest level monster that you can capture. The higher the monster you capture, the greater the chance its stats will be higher for use in the Pet Arena.<p>These Stones are not cheap, I require Dragon Scales from you, if you wish to purchase any of the following Spells:<br />\n";
        $page .= "<br /><br /><li /><a href=\"arena.php?do=spells1\">Lvl30 Capture</a><li /><a href=\"arena.php?do=spells2\">Lvl45 Capture</a><li /><a href=\"arena.php?do=spells3\">Lvl60 Capture</a><li /><a href=\"arena.php?do=spells4\">Lvl75 Capture</a><li /><a href=\"arena.php?do=spells5\">Lvl100 Capture</a><li /><a href=\"arena.php?do=spells6\">Lvl120 Capture</a><br /><p><p>You may return to the <a href=\"arena.php\">Arena</a>, or use the compass on the right to start exploring.<br />\n";


    }

    display($page, $title);

}


function spells1() { // Lvl30 capture

    global $userrow, $numqueries;

$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "strongholds");
	if (mysql_num_rows($castlequery) == 0) {header("Location: index.php"); die();}

 if (isset($_POST["submit"])) {

    if ($userrow["dscales"] < 100){ display("You do not have enough Dragon Scales to purchase this Capture Spell.<br /><br />You may return to your <a href=\"strongholds.php\">Stronghold</a>, or use the direction buttons on the right to start exploring.", "Not enough Dragon Scales"); die(); }

    $title = "Buy Spells - You have learnt a new Capture Spell";

        $spellquery = doquery("SELECT id FROM {{table}}","spells");
        $userspells = explode(",",$userrow["spells"]);
        //add spell
        array_push($userspells, 66);
        $new_userspells = implode(",",$userspells);
        $userid = $userrow["id"];
        doquery("UPDATE {{table}} SET spells='$new_userspells' WHERE id='$userid' LIMIT 1", "users");
        $newdscales = $userrow["dscales"] - 100;
        $query = doquery("UPDATE {{table}} SET dscales='$newdscales' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $page = "<table width='100%' border='1'><tr><td class='title'>Buy Spells - You have learnt a new Capture Spell</td></tr></table><p>";
        $page .= "You are handed a precious Stone, which makes you feel strange, but powerful.<p>Congratulations, you have learnt the <b>Lvl30 Capture Spell</b>!<br /><br />You may return to the <a href=\"arena.php\">Pet Arena</a>, or use the compass on the right to start exploring.";

    } elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

    } else {

        $title = "Buy Spells - Capture Spell";
        $page = "<table width='100%' border='1'><tr><td class='title'>Buy Spells - Capture Spell</td></tr></table><p>";
        $page .= "Are you sure you wish to buy this precious Stone to learn the <b>Lvl30 Capture Spell for a price of 100 Dragon Scales</b>?<br /><br />\n";
        $page .= "<form action=\"arena.php?do=spells1\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes Please\" /> <input type=\"submit\" name=\"cancel\" value=\"No Thanks\" />\n";
        $page .= "</form>\n";
        $page .= "You may return to the <a href=\"arena.php\">Pet Arena</a>, or use the compass on the right to start exploring.<br /><br />\n";

    }

    display($page, $title);

}


function spells2() { // Lvl45 capture

    global $userrow, $numqueries;

$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "strongholds");
	if (mysql_num_rows($castlequery) == 0) {header("Location: index.php"); die();}

 if (isset($_POST["submit"])) {

    if ($userrow["dscales"] < 300){ display("You do not have enough Dragon Scales to purchase this Capture Spell.<br /><br />You may return to your <a href=\"strongholds.php\">Stronghold</a>, or use the direction buttons on the right to start exploring.", "Not enough Dragon Scales"); die(); }

    $title = "Buy Spells - You have learnt a new Capture Spell";

        $spellquery = doquery("SELECT id FROM {{table}}","spells");
        $userspells = explode(",",$userrow["spells"]);
        //add spell
        array_push($userspells, 67);
        $new_userspells = implode(",",$userspells);
        $userid = $userrow["id"];
        doquery("UPDATE {{table}} SET spells='$new_userspells' WHERE id='$userid' LIMIT 1", "users");
        $newdscales = $userrow["dscales"] - 300;
        $query = doquery("UPDATE {{table}} SET dscales='$newdscales' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $page = "<table width='100%' border='1'><tr><td class='title'>Buy Spells - You have learnt a new Capture Spell</td></tr></table><p>";
        $page .= "You are handed a precious Stone, which makes you feel strange, but powerful.<p>Congratulations, you have learnt the <b>Lvl45 Capture Spell</b>!<br /><br />You may return to the <a href=\"arena.php\">Pet Arena</a>, or use the compass on the right to start exploring.";

    } elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

    } else {

        $title = "Buy Spells - Capture Spell";
        $page = "<table width='100%' border='1'><tr><td class='title'>Buy Spells - Capture Spell</td></tr></table><p>";
        $page .= "Are you sure you wish to buy this precious Stone to learn the <b>Lvl45 Capture Spell for a price of 300 Dragon Scales</b>?<br /><br />\n";
        $page .= "<form action=\"arena.php?do=spells2\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes Please\" /> <input type=\"submit\" name=\"cancel\" value=\"No Thanks\" />\n";
        $page .= "</form>\n";
        $page .= "You may return to the <a href=\"arena.php\">Pet Arena</a>, or use the compass on the right to start exploring.<br /><br />\n";

    }

    display($page, $title);

}


function spells3() { // Lvl60 capture

    global $userrow, $numqueries;

$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "strongholds");
	if (mysql_num_rows($castlequery) == 0) {header("Location: index.php"); die();}

 if (isset($_POST["submit"])) {

    if ($userrow["dscales"] < 500){ display("You do not have enough Dragon Scales to purchase this Capture Spell.<br /><br />You may return to your <a href=\"strongholds.php\">Stronghold</a>, or use the direction buttons on the right to start exploring.", "Not enough Dragon Scales"); die(); }

    $title = "Buy Spells - You have learnt a new Capture Spell";

        $spellquery = doquery("SELECT id FROM {{table}}","spells");
        $userspells = explode(",",$userrow["spells"]);
        //add spell
        array_push($userspells, 68);
        $new_userspells = implode(",",$userspells);
        $userid = $userrow["id"];
        doquery("UPDATE {{table}} SET spells='$new_userspells' WHERE id='$userid' LIMIT 1", "users");
        $newdscales = $userrow["dscales"] - 500;
        $query = doquery("UPDATE {{table}} SET dscales='$newdscales' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $page = "<table width='100%' border='1'><tr><td class='title'>Buy Spells - You have learnt a new Capture Spell</td></tr></table><p>";
        $page .= "You are handed a precious Stone, which makes you feel strange, but powerful.<p>Congratulations, you have learnt the <b>Lvl60 Capture Spell</b>!<br /><br />You may return to the <a href=\"arena.php\">Pet Arena</a>, or use the compass on the right to start exploring.";

    } elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

    } else {

        $title = "Buy Spells - Capture Spell";
        $page = "<table width='100%' border='1'><tr><td class='title'>Buy Spells - Capture Spell</td></tr></table><p>";
        $page .= "Are you sure you wish to buy this precious Stone to learn the <b>Lvl60 Capture Spell for a price of 500 Dragon Scales</b>?<br /><br />\n";
        $page .= "<form action=\"arena.php?do=spells3\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes Please\" /> <input type=\"submit\" name=\"cancel\" value=\"No Thanks\" />\n";
        $page .= "</form>\n";
        $page .= "You may return to the <a href=\"arena.php\">Pet Arena</a>, or use the compass on the right to start exploring.<br /><br />\n";

    }

    display($page, $title);

}


function spells4() { // Lvl75 capture

    global $userrow, $numqueries;

$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "strongholds");
	if (mysql_num_rows($castlequery) == 0) {header("Location: index.php"); die();}

 if (isset($_POST["submit"])) {

    if ($userrow["dscales"] < 850){ display("You do not have enough Dragon Scales to purchase this Capture Spell.<br /><br />You may return to your <a href=\"strongholds.php\">Stronghold</a>, or use the direction buttons on the right to start exploring.", "Not enough Dragon Scales"); die(); }

    $title = "Buy Spells - You have learnt a new Capture Spell";

        $spellquery = doquery("SELECT id FROM {{table}}","spells");
        $userspells = explode(",",$userrow["spells"]);
        //add spell
        array_push($userspells, 69);
        $new_userspells = implode(",",$userspells);
        $userid = $userrow["id"];
        doquery("UPDATE {{table}} SET spells='$new_userspells' WHERE id='$userid' LIMIT 1", "users");
        $newdscales = $userrow["dscales"] - 850;
        $query = doquery("UPDATE {{table}} SET dscales='$newdscales' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $page = "<table width='100%' border='1'><tr><td class='title'>Buy Spells - You have learnt a new Capture Spell</td></tr></table><p>";
        $page .= "You are handed a precious Stone, which makes you feel strange, but powerful.<p>Congratulations, you have learnt the <b>Lvl75 Capture Spell</b>!<br /><br />You may return to the <a href=\"arena.php\">Pet Arena</a>, or use the compass on the right to start exploring.";

    } elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

    } else {

        $title = "Buy Spells - Capture Spell";
        $page = "<table width='100%' border='1'><tr><td class='title'>Buy Spells - Capture Spell</td></tr></table><p>";
        $page .= "Are you sure you wish to buy this precious Stone to learn the <b>Lvl75 Capture Spell for a price of 850 Dragon Scales</b>?<br /><br />\n";
        $page .= "<form action=\"arena.php?do=spells4\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes Please\" /> <input type=\"submit\" name=\"cancel\" value=\"No Thanks\" />\n";
        $page .= "</form>\n";
        $page .= "You may return to the <a href=\"arena.php\">Pet Arena</a>, or use the compass on the right to start exploring.<br /><br />\n";

    }

    display($page, $title);

}

function spells5() { // Lvl100 capture

    global $userrow, $numqueries;

$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "strongholds");
	if (mysql_num_rows($castlequery) == 0) {header("Location: index.php"); die();}

 if (isset($_POST["submit"])) {

    if ($userrow["dscales"] < 1600){ display("You do not have enough Dragon Scales to purchase this Capture Spell.<br /><br />You may return to your <a href=\"strongholds.php\">Stronghold</a>, or use the direction buttons on the right to start exploring.", "Not enough Dragon Scales"); die(); }

    $title = "Buy Spells - You have learnt a new Capture Spell";

        $spellquery = doquery("SELECT id FROM {{table}}","spells");
        $userspells = explode(",",$userrow["spells"]);
        //add spell
        array_push($userspells, 70);
        $new_userspells = implode(",",$userspells);
        $userid = $userrow["id"];
        doquery("UPDATE {{table}} SET spells='$new_userspells' WHERE id='$userid' LIMIT 1", "users");
        $newdscales = $userrow["dscales"] - 1600;
        $query = doquery("UPDATE {{table}} SET dscales='$newdscales' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $page = "<table width='100%' border='1'><tr><td class='title'>Buy Spells - You have learnt a new Capture Spell</td></tr></table><p>";
        $page .= "You are handed a precious Stone, which makes you feel strange, but powerful.<p>Congratulations, you have learnt the <b>Lvl100 Capture Spell</b>!<br /><br />You may return to the <a href=\"arena.php\">Pet Arena</a>, or use the compass on the right to start exploring.";

    } elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

    } else {

        $title = "Buy Spells - Capture Spell";
        $page = "<table width='100%' border='1'><tr><td class='title'>Buy Spells - Capture Spell</td></tr></table><p>";
        $page .= "Are you sure you wish to buy this precious Stone to learn the <b>Lvl100 Capture Spell for a price of 1600 Dragon Scales</b>?<br /><br />\n";
        $page .= "<form action=\"arena.php?do=spells5\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes Please\" /> <input type=\"submit\" name=\"cancel\" value=\"No Thanks\" />\n";
        $page .= "</form>\n";
        $page .= "You may return to the <a href=\"arena.php\">Pet Arena</a>, or use the compass on the right to start exploring.<br /><br />\n";

    }

    display($page, $title);

}

function spells6() { // Lvl120 capture

    global $userrow, $numqueries;

$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "strongholds");
	if (mysql_num_rows($castlequery) == 0) {header("Location: index.php"); die();}

 if (isset($_POST["submit"])) {

    if ($userrow["dscales"] < 2100){ display("You do not have enough Dragon Scales to purchase this Capture Spell.<br /><br />You may return to your <a href=\"strongholds.php\">Stronghold</a>, or use the direction buttons on the right to start exploring.", "Not enough Dragon Scales"); die(); }

    $title = "Buy Spells - You have learnt a new Capture Spell";

        $spellquery = doquery("SELECT id FROM {{table}}","spells");
        $userspells = explode(",",$userrow["spells"]);
        //add spell
        array_push($userspells, 71);
        $new_userspells = implode(",",$userspells);
        $userid = $userrow["id"];
        doquery("UPDATE {{table}} SET spells='$new_userspells' WHERE id='$userid' LIMIT 1", "users");
        $newdscales = $userrow["dscales"] - 2100;
        $query = doquery("UPDATE {{table}} SET dscales='$newdscales' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $page = "<table width='100%' border='1'><tr><td class='title'>Buy Spells - You have learnt a new Capture Spell</td></tr></table><p>";
        $page .= "You are handed a precious Stone, which makes you feel strange, but powerful.<p>Congratulations, you have learnt the <b>Lvl120 Capture Spell</b>!<br /><br />You may return to the <a href=\"arena.php\">Pet Arena</a>, or use the compass on the right to start exploring.";

    } elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

    } else {

        $title = "Buy Spells - Capture Spell";
        $page = "<table width='100%' border='1'><tr><td class='title'>Buy Spells - Capture Spell</td></tr></table><p>";
        $page .= "Are you sure you wish to buy this precious Stone to learn the <b>Lvl120 Capture Spell for a price of 2100 Dragon Scales</b>?<br /><br />\n";
        $page .= "<form action=\"arena.php?do=spells6\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes Please\" /> <input type=\"submit\" name=\"cancel\" value=\"No Thanks\" />\n";
        $page .= "</form>\n";
        $page .= "You may return to the <a href=\"arena.php\">Pet Arena</a>, or use the compass on the right to start exploring.<br /><br />\n";

    }

    display($page, $title);

}

function dotrain($id) {
	global $userrow;
	global $userrow;

$updatequery = doquery("UPDATE {{table}} SET location='Training Pet' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	$page = "<table width='100%'><tr><td class='title'>Pet Arena</td></tr></table>";
	$petquery = doquery("SELECT * FROM {{table}} WHERE trainer='".$userrow["charname"]."' ", "arena");
	if (mysql_num_rows($petquery) <= 0) {
	$page .= "You do not have any captured Pets to use in the Arena!<br>";
	$page .= "In order to capture pets, you must use a capture spell ";
	$page .= "in battle against an enemy.  If the spell is strong enough to hold the creature, ";
	$page .= "and the monster has been weakened enough (low HP) you can capture it as a Pet.<p>";
	$page .= "You may go back to <a href='strongholds.php'>the Stronghold</a>, or leave and ";
	$page .= "<a href='index.php?do=move:0'>continue exploring</a>.<p>";
	display($page, "Viewing Pets");
	}


	if (!isset($id)) {


	$page .= "You can capture up to 5 different Pets to use within the Arena. ";
	$page .= "Some pets are better suited for different battles, depending on their ";
	$page .= "special abilities, immunities, and overall strengths and weaknesses. Click one of your Pets below to use its Skill Points to train it.<p>";
	$page .= "<table width='95%' style='border: solid 1px black' cellspacing='0' cellpadding='0'>";
	$page .= "<tr><td colspan='6' bgcolor='#fffff'><center><b>Please Choose a Pet</b></center></td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'><b>Name</b></td><td bgcolor='#ffffff'><b>Species</b></td>";
	$page .= "<td bgcolor='#ffffff'><b>HP</b></td><td bgcolor='#ffffff'><b>Level</b></td>";
	$page .= "<td bgcolor='#ffffff'><b>Win/Loss</b></td><td bgcolor='#ffffff'><b>SkillPoints</b></td></tr>";
	$count = 2;
	while ($petrow = mysql_fetch_array($petquery)) {
		if ($count == 1) { $color = "bgcolor='#ffffff'"; $count = 2; }
		else { $color = "bgcolor='#eeeeee'"; $count = 1;}
		$page .= "<tr><td ".$color." width='25%'>";
		if ($petrow["skillpoints"] >= 1) {
		$page .= "<a href='arena.php?do=train:".$petrow["id"]."'>".$petrow["name"]."</a></td>";
		} else {
		$page .= "<i>".$petrow["name"]."</i></td>";
		$petrow["skillpoints"] = "<b>*0*</b>";
		}
		$page .= "<td ".$color." width='25%'>".$petrow["type"]."</td>";
		$page .= "<td ".$color." width='20%'>".$petrow["currenthp"]."/".$petrow["maxhp"]."</td>";
		$page .= "<td ".$color." width='20%'>".$petrow["level"]."</td>";
		$page .= "<td ".$color." width='20%'>".$petrow["wins"]."/".$petrow["losses"]."</td>";
		$page .= "<td ".$color." width='20%'>".$petrow["skillpoints"]."</td>";
	  	$page .= "</tr>";
	}
	$page .= "</table>";

	} else {   // if you chose to view a Pet...

	$petquery = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "arena");
	$petrow = mysql_fetch_array($petquery);
	if ($petrow["trainer"] != $userrow["charname"]) {
	$page .= "You do not own that pet!<br>";
	$page .= "You may only view, feed, and release pets that are under your control.<p>";
	$page .= "You may go back to <a href='strongholds.php'>the Stronghold</a>, or leave and ";
	$page .= "<a href='index.php?do=move:0'>continue exploring</a>.<p>";
	display($page, "Training Pets");
	}

	$petquery = doquery("SELECT * FROM {{table}} WHERE id='$id' limit 1", "arena");
	$petrow = mysql_fetch_array($petquery);
	$page = "<h3>Details for ".$petrow["name"].":</h3><p>";
	if ($petrow["skillpoints"] <= 0) {
		$page .= "This Pet can not train any further right now.<br>";
		$page .= "Additional skill points are gained each time your pet levels up, ";
		$page .= "sometimes by using special items, or as a bonus from the Temples.<br>";
	    $page .= "<center><br><a href='arena.php'>Return to the Arena</a></center>";
    	display($page,"Pet Arena");
	}
	if (isset($_POST["hpup"])) {
		$hp = rand(1,$petrow["level"]);
		$newhp = $petrow["maxhp"] + $hp;
		$newskill = $petrow["skillpoints"] - 1;
		$upd = doquery("UPDATE {{table}} SET maxhp='$newhp',currenthp='$newhp',skillpoints='$newskill' WHERE id='$id' ", "arena");
		$page .= "<br><b>Your pet ".$petrow["name"]." has gained ".$hp." HP!</b>";
	}
	if (isset($_POST["mpup"])) {
		$mp = rand(1,$petrow["level"]);
		$newmp = $petrow["maxmp"] + $hp;
		$newskill = $petrow["skillpoints"] - 1;
		$upd = doquery("UPDATE {{table}} SET maxmp='$newmp',currentmp='$newmp',skillpoints='$newskill' WHERE id='$id' ", "arena");
		$page .= "<br><b>Your pet ".$petrow["name"]." has gained ".$hp." MP!</b>";
	}
	if (isset($_POST["strup"])) {
		$st = rand(1,$petrow["level"]);
		$newstr = $petrow["maxdam"] + $st;
		$newskill = $petrow["skillpoints"] - 1;
		$upd = doquery("UPDATE {{table}} SET maxdam='$newstr',skillpoints='$newskill' WHERE id='$id' ", "arena");
		$page .= "<br><b>Your pet ".$petrow["name"]." has gained ".$st." strength!</b>";
	}
	if (isset($_POST["dexup"])) {
		$dx = rand(1,$petrow["level"]);
		$newdex = $petrow["dexterity"] + $dx;
		$newskill = $petrow["skillpoints"] - 1;
		$upd = doquery("UPDATE {{table}} SET dexterity='$newdex',skillpoints='$newskill' WHERE id='$id' ", "arena");
		$page .= "<br><b>Your pet ".$petrow["name"]." has gained ".$dx." dexterity!</b>";
	}
	if (isset($_POST["armorup"])) {
		$arm = rand(1,$petrow["level"]);
		$newarm = $petrow["armor"] + $arm;
		$newskill = $petrow["skillpoints"] - 1;
		$upd = doquery("UPDATE {{table}} SET armor='$newarm',skillpoints='$newskill' WHERE id='$id' ", "arena");
		$page .= "<br><b>Your pet ".$petrow["name"]." has gained ".$arm." armor!</b>";
	}
	if (isset($_POST["magearmorup"])) {
		$marm = rand(1,$petrow["level"]);
		$newmarm = $petrow["magicarmor"] + $st;
		$newskill = $petrow["skillpoints"] - 1;
		$upd = doquery("UPDATE {{table}} SET magicarmor='$newmarm',skillpoints='$newskill' WHERE id='$id' ", "arena");
		$page .= "<br><b>Your pet ".$petrow["name"]." has gained ".$st." Magic Resistance!</b>";
	}
	$petquery = doquery("SELECT * FROM {{table}} WHERE id='$id' limit 1", "arena");
	$petrow = mysql_fetch_array($petquery);
		if ($petrow["skillpoints"] <= 0) {
			$page .= "<p>This Pet can not train any further right now.<br>";
			$page .= "Additional skill points are gained each time your pet levels up, ";
			$page .= "sometimes by using special items, or as a bonus from the Temples.<br>";
			$page .= "<center><br><a href='arena.php'>Return to the Arena</a></center>";
    		display($page,"Pet Arena");
	}
	$page .= "<form action='arena.php?do=train:".$id."' method='POST'>";
	$page .= "<table border='0' width='99%'>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Name:</b></td><td bgcolor='#eeeeee'>".$petrow["name"]."</td></tr>";
	$page .= "<tr><td colspan='2' bgcolor='#ffffff'>A level ".$petrow["level"]." ".$petrow["type"]."</td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Experience:</b></td><td bgcolor='#eeeeee'>".$petrow["experience"]." <i>(next:".($petrow["level"]*50)." )</i></td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'><input type='submit' name='hpup' value='+'> <b>Health:</b></td>";
	$page .= "<td bgcolor='#ffffff'>".$petrow["currenthp"]." / ".$petrow["maxhp"]."</td></tr>";
	$page .= "<tr bgcolor='#eeeeee'><td bgcolor='#eeeeee'><input type='submit' name='mpup' value='+'> <b>Magic:</b></td>";
	$page .= "<td bgcolor='#eeeeee'>".$petrow["currentmp"]." / ".$petrow["maxmp"]."</td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'><input type='submit' name='strup' value='+'> <b>Strength:</b></td>";
	$page .= "<td bgcolor='#ffffff'>".$petrow["maxdam"]."</td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><input type='submit' name='dexup' value='+'> <b>Dexterity:</b></td>";
	$page .= "<td bgcolor='#eeeeee'>".$petrow["dexterity"]."</td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'><input type='submit' name='armorup' value='+'> <b>Armor:</b></td>";
	$page .= "<td bgcolor='#ffffff'>".$petrow["armor"]."</td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><input type='submit' name='magearmorup' value='+'> <b>Magic Res:</b></td>";
	$page .= "<td bgcolor='#eeeeee'>".$petrow["magicarmor"]."</td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'><b>Wins:</b></td><td bgcolor='#ffffff'>".$petrow["wins"]."<br></td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'><b>Last win:</b></td><td bgcolor='#ffffff'>".$petrow["lastwin"]."<br></td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Losses:</b></td><td bgcolor='#eeeeee'>".$petrow["losses"]."<br></td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Last Loss:</b></td><td bgcolor='#eeeeee'>".$petrow["lastloss"]."<br></td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'><b>SkillPoints:</b></td><td bgcolor='#ffffff'>".$petrow["skillpoints"]."<br></td></tr>";
	$page .= "</table></form>";
	}
    $page .= "<center><br><a href='arena.php'>Return to the Arena</a></center>";
    display($page,"Pet Arena");
}


function dorelease($id) {
	global $userrow;

$updatequery = doquery("UPDATE {{table}} SET location='Releasing Pets' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	$page = "<table width='100%'><tr><td class='title'>Pet Arena - Release Pets</td></tr></table>";
	$petquery = doquery("SELECT * FROM {{table}} WHERE trainer='".$userrow["charname"]."'", "arena");
	if (mysql_num_rows($petquery) <= 0) {
	$page .= "You do not have any captured Pets to use in the Arena!<br>";
	$page .= "In order to capture pets, you must use a capture spell ";
	$page .= " in battle against an enemy.  If the spell is strong enuogh to hold the creature, ";
	$page .= "and the monster has been weakened enough (low HP) you can capture it as a Pet.<p>";
	$page .= "You may go back to <a href='strongholds.php'>the Stronghold</a>, or leave and ";
	$page .= "<a href='index.php?do=move:0'>continue exploring</a>.<p>";
	display($page, "Viewing Pets");
	}

	if (isset($_POST["submit"])) {
		$rename = doquery("DELETE FROM {{table}} WHERE id='$id' LIMIT 1", "arena");
		$page .= "<p>You have released your pet back into the wild! You won't ever be to re-capture your old Pet again, only similar ones.";
		$page .= "<center><br><a href='arena.php'>Return to the Arena</a></center>";
    	display($page,"Pet Arena");
	}
	if (isset($_POST["cancel"])) {
		header("Location: arena.php"); die();
	}
	if (!isset($id)) {
	$page .= "<p>You can capture up to 5 different Pets to use within the Arena. ";
	$page .= "When you have maxed out your amount of Pets, you can Release them back into the wild by coming here.<p>";
	$page .= "<table width='95%' style='border: solid 1px black' cellspacing='0' cellpadding='0'>";
	$page .= "<tr><td colspan='5' bgcolor='#fffff'><center><b>Please Choose a Pet</b></center></td></tr>";
	$page .= "<tr><td><b>Name</b></td><td><b>Species</b></td><td><b>HP</b></td><td><b>Level</b></td><td><b>Win/Loss</b></td></tr>";
	$count = 2;
	while ($petrow = mysql_fetch_array($petquery)) {
		if ($count == 1) { $color = "bgcolor='#ffffff'"; $count = 2; }
		else { $color = "bgcolor='#eeeeee'"; $count = 1;}
		$page .= "<tr><td ".$color." width='25%'>";
		$page .= "<a href='arena.php?do=release:".$petrow["id"]."'>".$petrow["name"]."</a></td>";
		$page .= "<td ".$color." width='25%'>".$petrow["type"]."</td>";
		$page .= "<td ".$color." width='20%'>".$petrow["currenthp"]."/".$petrow["maxhp"]."</td>";
		$page .= "<td ".$color." width='20%'>".$petrow["level"]."</td>";
		$page .= "<td ".$color." width='20%'>".$petrow["wins"]."/".$petrow["losses"]."</td>";
	  	$page .= "</tr>";
	}
	$page .= "</table>";

	} else {   // if you chose to view a Pet...

	$petquery = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "arena");
	$petrow = mysql_fetch_array($petquery);
	if ($petrow["trainer"] != $userrow["charname"]) {
	$page .= "You do not own that pet!<br>";
	$page .= "You may only view, feed, and release pets that are under your control.<p>";
	$page .= "You may go back to <a href='strongholds.php'>the Stronghold</a>, or leave and ";
	$page .= "<a href='index.php?do=move:0'>continue exploring</a>.<p>";
	display($page, "Releasing Pets");
	}

	$petquery = doquery("SELECT * FROM {{table}} WHERE id='$id' limit 1", "arena");
	$petrow = mysql_fetch_array($petquery);
	$page .= "<h3>Are you sure you want to release ".$petrow["name"]."?</h3>";
	$page .= "If you release a pet, it will be permanently removed from your list of Pets.<br>";
	$page .= "<form action='arena.php?do=release:".$id."' method='POST'>";
	$page .= "<input type='submit' name='submit' value='Release!'> - ";
	$page .= "<input type='submit' name='cancel' value='cancel'><p>";
	$page .= "<table border='0' width='99%'>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Name:</b></td>";
	$page .= "<td bgcolor='#eeeeee'>".$petrow["name"]."</td></tr>";
	$page .= "<tr><td colspan='2' bgcolor='#ffffff'>A level ".$petrow["level"]." ".$petrow["type"]."</td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Experience:</b></td><td bgcolor='#eeeeee'>".$petrow["experience"]." <i>(next:".($petrow["level"]*50)." )</i></td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'><b>Health:</b></td><td bgcolor='#ffffff'>".$petrow["currenthp"]." / ".$petrow["maxhp"]."</td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Magic:</b></td><td bgcolor='#eeeeee'>".$petrow["currentmp"]." / ".$petrow["maxmp"]."</td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'><b>Strength:</b></td><td bgcolor='#ffffff'>".$petrow["maxdam"]."</td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Dexterity:</b></td><td bgcolor='#eeeeee'>".$petrow["dexterity"]."</td></tr>";

	$page .= "<tr><td bgcolor='#ffffff'><b>Armor:</b></td><td bgcolor='#ffffff'>".$petrow["armor"]."</td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Magic Res:</b></td><td bgcolor='#eeeeee'>".$petrow["magicarmor"]."</td></tr>";

	$page .= "<tr><td bgcolor='#ffffff'><b>Wins:</b></td><td bgcolor='#ffffff'>".$petrow["wins"]."<br></td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'><b>Last win:</b></td><td bgcolor='#ffffff'>".$petrow["lastwin"]."<br></td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Losses:</b></td><td bgcolor='#eeeeee'>".$petrow["losses"]."<br></td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Last Loss:</b></td><td bgcolor='#eeeeee'>".$petrow["lastloss"]."<br></td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'><b>SkillPoints:</b></td><td bgcolor='#ffffff'>".$petrow["skillpoints"]."<br></td></tr>";

	$page .= "</table>";
	}
    $page .= "<center><br><a href='arena.php'>Return to the Arena</a></center>";
    display($page,"Pet Arena - Release Pets");
}

function dopractice($id) {
	global $userrow;


	$page = "<table width='100%'><tr><td class='title'>Pet Arena - Dueling</td></tr></table>";
	if ($userrow["dscales"] < 1) {
	$page .= "<p><b>You do not have enough Dragon Scales to Duel!</b><br>";
	$page .= "All Duels, whether Practice or against other Pets, cost 1 Dragon Scale.";
	$page .= "You can find Dragon Scales randomly by exploring, or steal them from other ";
	$page .= "guild Strongholds.  Also, the High Stake Gambling in the Strongholds will sometimes ";
	$page .= "payout a number of Dragon Scales.<p>";
	$page .= "<center><br><a href='arena.php'>Return to the Arena</a></center>";
	display($page,"Pet Arena");
	die();
	}

$updatequery = doquery("UPDATE {{table}} SET location='Practice Dueling' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	$page = "<table width='100%'><tr><td class='title'>Pet Arena - Practice Arena</td></tr></table>";
	if (!isset($id)) {
	$page .= "All Duels, cost 1 Dragon Scale. Once you begin a Duel, the fee will be immediately";
	$page .= "deducted from your total Dragon Scales.   If you cancel a duel before a winner is decided, ";
	$page .= "you will not receive the entry fee back.  It is already gone.";
	$page .= "<br>The Practice Arena will allow you to try out your Pet's abilities or simply ";
	$page .= "get used to the fighting arena.  Practice duels are exactly like dueling ";
	$page .= "other pets, but you do not earn experience points or suffer any lasting ";
	$page .= "damage.  All damage taken by your Pet in a practice duel is restored ";
	$page .= "at the duel.<p>";
	$page .= "The opponent in a practice duel is a generic 'Practice Pet' who will do ";
	$page .= "minimal damage and only has one special ability. The Practice Pet can not ";
	$page .= "be defeated in battle, since it recovers its full health each round.";
	$page .= "In order to stop a practice duel, simply click on the 'Leave Duel' option.<br>";
	$page .= "This allows you to get an idea of how your Pet will perform against others ";
	$page .= "and see what your Pet's special abilties do without risk of losing the battle.<hr>";

	$page .= "<table width='99%' ><tr><td><form action='arena.php?do=practice:1' method='POST'>";
	$page .= "To enter a practice battle, choose your Pet and an Arena below.</td></tr>";
	$petquery = doquery("SELECT * FROM {{table}} WHERE trainer='".$userrow["charname"]."' AND currenthp>'0'", "arena");
	if (mysql_num_rows($petquery) <= 0) {
		$page .= "<tr><td><b>You do not have any captured, healthy Pets to use in the Arena!</b><br>";
		$page .= "In order to capture pets, you must use a capture spell ";
		$page .= "in battle against an enemy.  If the spell is strong enuogh to hold the creature, ";
		$page .= "and the monster has been weakened enough (low HP) you can capture it as a Pet.<p>";
		$page .= "If you have a number of pets captured, they must be healthy enough to Duel! ";
		$page .= "Feed your pets in order to restore their health.<p>";
		$page .= "</form></td></tr></table>";
	} else {
		$page .= "<tr><td>Choose a Pet:  <select name='pet1'>";
		while ($petrow = mysql_fetch_array($petquery)) {
		$page .= "<option value='".$petrow["id"]."'>".$petrow["name"]." (".$petrow["currenthp"]."/".$petrow["maxhp"]."hp)</option>";
		}
	$page .= "</select></td></tr>";
	$page .= "<tr><td> </td></tr>";
	$page .= "<tr><td>Choose an Arena:  <select name='arena'>";
	$page .= "<option value='0'>Practice Arena</option>";
	$page .= "</select></td></tr>";
	$page .= "<tr><td> </td></tr>";
	$page .= "<tr><td><input type='submit' name='submit' value='Begin Practice'>";
	$page .= "</form></td></tr></table> <p> ";
	}
    $page .= "<center><br><a href='strongholds.php'>Return to the Stronghold</a></center>";
    display($page,"Pet Arena");
    } // If you pick an Arena

	$pet1 = $_POST["pet1"];
	$trainer = $userrow["charname"];
	$petquery = doquery("SELECT * FROM {{table}} WHERE id='".$pet1."'", "arena");
	$petrow = mysql_fetch_array($petquery);

	if ($petrow["trainer"] != $userrow["charname"]) {
		$page .= "ERROR! - The Pet you chose does not belong to you!<p>";
		$page .= "Please <a href='arena.php?do=practice'>go back</a> and choose one of your pets.<p>";
		display($page,"Practice Arena - Error");
		die();
	}

	$newdscales = $userrow["dscales"] - 1;
        $dscalesquery = doquery("UPDATE {{table}} SET templist='arena',dscales='$newdscales' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	$page .= "<p>You Have chosen to use your pet named ".$petrow["name"]." to enter the Practice Arena.<p>";
	$page .= "<form action='arena.php?do=duel2&pet=".$petrow["id"]."&arena=0&foe=1' method='POST'>";
	$page .= "<input type='submit' name='submit' value='Begin Duel'>";
	$page .= "</form> <p>";
    $page .= "<center><br><a href='arena.php'>Return to the arena</a></center>";
    display($page,"Pet Arena - Practice");
}


function doduel($id) {
	global $userrow;

$updatequery = doquery("UPDATE {{table}} SET location='Dueling Pets' WHERE id='".$userrow["id"]."' LIMIT 1", "users");


	$page = "<table width='100%'><tr><td class='title'>Pet Arena - Dueling</td></tr></table>";
	if ($userrow["dscales"] < 1) {
	$page .= "<p><b>You do not have enough Dragon Scales to Duel!</b><br>";
	$page .= "All Duels, whether Practice or against other Pets, cost 1 Dragon Scale.";
	$page .= "You can find Dragon Scales randomly by exploring, or steal them from other ";
	$page .= "guild Strongholds.  Also, the High Stake Gambling in the Strongholds will sometimes ";
	$page .= "payout a number of Dragon Scales.<p>";
	$page .= "<center><br><a href='arena.php'>Return to the Arena</a></center>";
	display($page,"Pet Arena");
	die();
	}
	if (!isset($_POST["submit"])) {
	$page .= "All Duels, whether Practice or against other Pets, cost 1 Dragon Scale.";
	$page .= "<br>Here you may choose one of your pets to Duel, and you will be matched with other ";
	$page .= "pets with similar levels  (this is to prevent people with high-level pets from picking ";
	$page .= "on lower level pets and winning too easily.)<hr>";
	$page .= "<table width='99%' ><tr><td><form action='arena.php?do=duel' method='POST'>";
	$page .= "To enter a battle, choose your Pet and an Arena below.<br>";
	$page .= "The next step will be to choose an opponent.</td></tr>";
	$petquery = doquery("SELECT * FROM {{table}} WHERE trainer='".$userrow["charname"]."' AND currenthp>'0'", "arena");
	if (mysql_num_rows($petquery) <= 0) {
		$page .= "<tr><td><b>You do not have any captured, healthy Pets to use in the Arena!</b><br>";
		$page .= "In order to capture pets, you must use a capture spell ";
		$page .= "in battle against an enemy.  If the spell is strong enough to hold the creature, ";
		$page .= "and the monster has been weakened enough (low HP) you can capture it as a Pet.<p>";
		$page .= "If you have a number of pets captured, they must be healthy enough to Duel! ";
		$page .= "Feed your pets in order to restore their health.<p>";
		$page .= "</form></td></tr></table>";
	} else {
		$page .= "<tr><td>Choose a Pet:  <select name='pet1'>";
		while ($petrow = mysql_fetch_array($petquery)) {
		$page .= "<option value='".$petrow["id"]."'>".$petrow["name"]." (".$petrow["currenthp"]."/".$petrow["maxhp"]."hp)</option>";
		}
	$page .= "</select></td></tr>";
	$page .= "<tr><td> </td></tr>";
	$page .= "<tr><td>Choose an Arena:  <select name='arena'>";
	$page .= "<option value='1'>Default Arena</option>";
	$page .= "<option value='2'>Toxic Arena</option>";
	$page .= "<option value='3'>Chaos Arena</option>";
	$page .= "</select></td></tr>";
	$page .= "<tr><td> </td></tr>";
	$page .= "<tr><td><input type='submit' name='submit' value='Continue'>";
	$page .= "</form></td></tr></table> <p> ";

	}
	$page .= "<center><br><a href='strongholds.php'>Return to the Stronghold</a></center>";
	display($page,"Pet Arena");

	} // If you picked an Arena and your Pet
	if (!isset($id)) {
	$pet1 = $_POST["pet1"];
	$arena = $_POST["arena"];
	$trainer = $userrow["charname"];
	$petquery = doquery("SELECT * FROM {{table}} WHERE id='".$pet1."'", "arena");
	$petrow = mysql_fetch_array($petquery);
	if ($petrow["trainer"] != $userrow["charname"]) {
		$page .= "ERROR! - The Pet you chose does not belong to you!<p>";
		$page .= "Please <a href='arena.php?do=practice'>go back</a> and choose one of your pets.<p>";
		display($page,"Practice Arena - Error");
		die();
	}

	$page .= "<p>You have chosen the following Pets in the Arena:<p>";
	$page .= "<b>Pet Name:</b> ".$petrow["name"]." <i>(a level ".$petrow["level"]." ".$petrow["type"].")</i><p>";
	$page .= "<b>Arena:</b> ";
	if ($arena == 1) {
		$page .= "Default Arena.<br><i>No special events, and Pets deal normal damage.<br>";
		$page .= "Winner gains normal Experience and the defeated Pet loses a little Experience.</i><p>";
	}elseif ($arena == 2) {
		$page .= "Toxic Arena.<br><i>All Pets take random damage each round, regardless of poison immunity.<br>";
		$page .= "The winner gains a bit of extra experience, but the defeated Pet does not lose any experience.<br>";
		$page .= "There is a slight chance of your pet suffering permanent damage from the toxic nature of this arena, ";
		$page .= "and its maximum HP or MP may be REDUCED slightly at the end of the Duel.</i><p>";
	}elseif ($arena == 3) {
		$page .= "Chaos Arena.<br><i>Each Round both sides are affected by random events.  ";
		$page .= "Some may heal, some may harm, and some may afflict status attacks.<br>";
		$page .= "Immunity is a factor, so poison-immune pets will not suffer poison damage.<br>";
		$page .= "Winner gains normal Experience and the defeated Pet loses a little Experience.</i><p>";
	}else {
		$page .= "Practice Arena.<br><i>No special events, and Pets deal normal damage.<br>";
		$page .= "Both sides are healed to full each round and any Death attacks deal zero damage.<br>";
		$page .= "No experience is awarded or lost on either side.</i><p>";
	}
	$pquery = doquery("SELECT * FROM {{table}} WHERE level='".$petrow["level"]."' AND currenthp>'5' AND trainer!='".$userrow["charname"]."' ORDER by RAND() LIMIT 25", "arena");
	if (mysql_num_rows($pquery) != 0) {
	$page .= "<form action='arena.php?do=duel:1' method='POST'>";
	$page .= "Please choose an opponent:<br>";
	$page .= "<select name='pet2'>";
	while ($prow = mysql_fetch_array($pquery)) {
	$page .= "<option value='".$prow["id"]."'>".$prow["name"]." (trainer=".$prow["trainer"].")</option>";
	}
	$page .= "</select><br>";
	$page .= "<input type='hidden' name='pet1' value='".$pet1."'>";
	$page .= "<input type='hidden' name='arena' value='".$arena."'>";
	$page .= "<input type='submit' name='submit' value='Continue!'>";
	$page .= "</form><p>";
	} else {
	$page .= "<p><b>There are no opponents eligible for a Duel at this time!</b><p>";
	}
	$page .= "<center><br><a href='arena.php'>Return to the Arena</a></center>";
    display($page,"Pet Arena");
	} //END of choose opponent;
	$pet1 = $_POST["pet1"];
	$pet2 = $_POST["pet2"];
	$arena = $_POST["arena"];
	$trainer = $userrow["charname"];
	$petquery = doquery("SELECT * FROM {{table}} WHERE id='$pet1' LIMIT 1", "arena");
	$petrow = mysql_fetch_array($petquery);
	if ($arena != 0) {
		$newdscales = $userrow["dscales"] - 1;
    	$dscalequery = doquery("UPDATE {{table}} SET dscales='$newdscales' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
		$gquery = doquery("SELECT * FROM {{table}} WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
		$grow = mysql_fetch_array($gquery);
		$dscales = $grow["dscales"] + 1;
		$dscalesquery = doquery("UPDATE {{table}} SET dscales='$dscales' WHERE id='".$grow["id"]."' LIMIT 1", "guilds");

	}
	$page .= "<p>You have chosen to use your pet named ".$petrow["name"]." to enter the Arena. Do you wish to continue to the Main Arena?<p>";
	$page .= "<form action='arena.php?do=duel2&pet=".$pet1."&arena=".$arena."&foe=".$pet2."' method='POST'>";
	$page .= "<input type='submit' name='submit' value='Begin Duel'>";
	$page .= "</form> <p>";
	$page .= "<center><br><a href='arena.php'>Return to the Arena</a></center>";
	$uquery = doquery("UPDATE {{table}} SET templist='arena' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
    display($page,"Pet Arena");
    }


function doduel2() {
	global $userrow;
	if ($userrow["templist"] != "arena") {header("Location: arena.php"); die();}
	$page = "<table width='100%'><tr><td class='title'>Pet Arena</td></tr></table> <p>";
	$pet1 = $_GET["pet"];
	$pet2 = $_GET["foe"];
	$arena = $_GET["arena"];

	$p1query = doquery("SELECT * FROM {{table}} WHERE id='".$pet1."' LIMIT 1", "arena");
	$p1row = mysql_fetch_array($p1query);
	$p2query = doquery("SELECT * FROM {{table}} WHERE id='".$pet2."' LIMIT 1", "arena");
	$p2row = mysql_fetch_array($p2query);
	if ($arena == 0) {$arenaname="Practice";}
	if ($arena == 1) {$arenaname="Default";}
	if ($arena == 2) {$arenaname="Toxic";}
	if ($arena == 3) {$arenaname="Chaos";}

	if (isset($_POST["fight"])) {
		$count = 0;
		while ($count < 5) {
		$count += 1;
		$tohit = ceil(rand($p1row["maxdam"]*.75,$p1rowrow["maxdam"])) + intval(sqrt($p1row["dexterity"]));
	    $toblock = ceil(rand($p2row["armor"]*.75,$p2row["armor"])/3) + intval(sqrt($p2row["dexterity"]));
	    $tododge = rand(1,200);
		if ($tododge <= sqrt($p2row["armor"])) {
			$tohit = 0; $page .= " <font color=red>The monster is dodging. No damage has been caused.</font><br />";
	   		$p2damage = 0;
	   	} else {
			$toexcellent = rand(1,150);
		if ($toexcellent <= sqrt($p2row["armor"])) { $tohit *= 2; $page .= "<b> <font color=blue>Excellent hit!</font></b><br />"; }
		$p2damage = $tohit - $toblock;
	    if ($p2damage < 1) { $p2damage = 1; }
	    $page .= " <font color=green>You attack your opponent for $p2damage damage.</font><br />";
	  	$p2row["currenthp"] -= $p2damage;
	    if ($arena == 0) {$p2row["currenthp"] += $p2damage;}
		}
		$p1query = doquery("UPDATE {{table}} SET currenthp='".$p2row["currenthp"]."',currentmp='".$p2row["currentmp"]."' WHERE id='".$p2row["id"]."' ", "arena");
		$tohit = ceil(rand($p2row["maxdam"]*.75,$p2rowrow["maxdam"])) + intval(sqrt($p2row["dexterity"]));
		$toblock = ceil(rand($p1row["armor"]*.75,$p1row["armor"])/3) + intval(sqrt($p1row["dexterity"]));
		$tododge = rand(1,200);
		if ($tododge <= sqrt($p1row["armor"])) {
			$tohit = 0; $page .= "<i><font color=red>-".$p1row["name"]." has dodged the attack. No damage has been caused.</font></i><br />";
	   		$p1damage = 0;
	   	} else {
		$toexcellent = rand(1,150);
		if ($toexcellent <= sqrt($p1row["armor"])) { $tohit *= 2; $page .= "<b><font color=blue>Excellent hit!</font></b><br>"; }
		$p1damage = $tohit - $toblock;
	    if ($p1damage < 1) { $p1damage = 1; }
	    $page .= "<i><font color=red>-Your opponent attacks for $p1damage damage.</font></i><br>";
	    $p1row["currenthp"] -= $p1damage;
	   	if ($arena == 0) {$p1row["currenthp"] += $p1damage;}
		$p2query = doquery("UPDATE {{table}} SET currenthp='".$p1row["currenthp"]."',currentmp='".$p1row["currentmp"]."' WHERE id='".$p1row["id"]."' ", "arena");
		if ($p2row["currenthp"] <= 0) {
			$page .= "<center><b>YOU WIN! Click below to continue.</b><br><br>";
			$page .= "<a href=\"arena.php?do=victory&pet=$pet1&arena=$arena&foe=$pet2\">";
			$page .= "Continue</a></center>";
    			$uquery = doquery("UPDATE {{table}} SET templist='arenawin' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
			display($page, "Pet Arena Dueling");}
		if ($p1row["currenthp"] <= 0) {
			$page .= "<center><b>You have Lost! Click below to continue.</b><br><br>";
			$page .= "<a href=\"arena.php?do=defeat&pet=$pet1&arena=$arena&foe=$pet2\">";
			$page .= "Continue</a></center>";
			display($page, "Pet Arena Dueling");}
		}
	}

	} elseif (isset($_POST["quit"])) {
		("Location: arena.php"); die();
	}
	if ($arena == 0) {
		$arenaname="Practice";
		$page .= "<b>All damage is healed in the Practice Arena.</b><p>";
	} elseif ($arena == 1) {
		if ($p2row["currenthp"] <= 0) {
			$page .= "<center><b>YOU WIN! Click below to continue.</b><br><br>";
			$page .= "<a href=\"arena.php?do=victory&pet=$pet1&arena=$arena&foe=$pet2\">";
			$page .= "Process Victory</a></center>";
    			$uquery = doquery("UPDATE {{table}} SET templist='arenawin' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
			display($page, "Pet Arena Dueling");}
		if ($p1row["currenthp"] <= 0) {
			$page .= "<center><b>You have Lost! Click below to continue.</b><br><br>";
			$page .= "<a href=\"arena.php?do=defeat&pet=$pet1&arena=$arena&foe=$pet2\">";
			$page .= "Process Victory</a></center>";
			display($page, "Pet Arena Dueling");}
	} elseif ($arena == 2) {
		$t2damage = rand(1,$p2row["level"]);
		$p2row["currenthp"] -= $t2damage;
		$page .= "<font color=blue>Your opponent takes ".$t2damage." damage from the Toxic Arena.</font><p>";
		$p2row["currenthp"] + $p2damage;
		if ($p2row["currenthp"] <= 0) {
			$page .= "<center><b>YOU WIN! Click below to continue.</b><br><br>";
			$page .= "<a href=\"arena.php?do=victory&pet=$pet1&arena=$arena&foe=$pet2\">";
			$page .= "Process Victory</a></center>";
    			$uquery = doquery("UPDATE {{table}} SET templist='arenawin' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
			display($page, "Pet Arena Dueling");}
		$t1damage = rand(1,$p1row["level"]);
		$p1row["currenthp"] -= $t1damage;
		$page .= "Your Pet takes ".$t1damage." damage from the Toxic Arena.<br>";
		if ($p1row["currenthp"] <= 0) {
			$page .= "<center><b>You have Lost! Click below to continue.</b><br><br>";
			$page .= "<a href=\"arena.php?do=defeat&pet=$pet1&arena=$arena&foe=$pet2\">";
			$page .= "Continue</a></center>";
			display($page, "Pet Arena Dueling");}
	} elseif ($arena == 3) {
		if ($p2row["currenthp"] <= 0) {
			$page .= "<center><b>YOU WIN! Click below to continue.</b><br><br>";
			$page .= "<a href=\"arena.php?do=victory&pet=$pet1&arena=$arena&foe=$pet2\">";
			$page .= "Continue</a></center>";
    			$uquery = doquery("UPDATE {{table}} SET templist='arenawin' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
			display($page, "Pet Arena Dueling");}
		if ($p1row["currenthp"] <= 0) {
			$page .= "<center><b>You have Lost! Click below to continue.</b><br><br>";
			$page .= "<a href=\"arena.php?do=defeat&pet=$pet1&arena=$arena&foe=$pet2\">";
			$page .= "Continue</a></center>";
			display($page, "Pet Arena Dueling");}
	} else {

	}

	$page .= "<table width='99%' style='border: solid 1px black'><tr><th align='center' colspan='2'>";
	$page .= "<b><u>Arena: ".$arenaname."</u></b><br>";
	$page .= "<b>".$p1row["name"]. "</b> - vs -  </b>".$p2row["name"]."</b></th></tr>";
	$page .= "<tr><td> </td><td> </td></tr>";
	$page .= "<tr><td width='50%' style='border: solid 1px black'>";
	$page .= "<p align='center'><b><u>".$p1row["name"]."</u></b><br>";
	$page .= "<i>(a ".$p1row["type"].")</i></p>";
	$page .= "<b>HP:</b> ".$p1row["currenthp"]."/".$p1row["maxhp"]."<br>";
	$page .= "<b>MP:</b> ".$p1row["currentmp"]."/".$p1row["maxmp"]."<br>";
	$page .= "<b>Strength:</b> ".$p1row["maxdam"]."<br>";
	$page .= "<b>Dexterity:</b> ".$p1row["dexterity"]."<br>";
	$page .= "<b>Armor:</b> ".$p1row["armor"]."<br>";
	$page .= "<form action='arena.php?do=duel2&pet=".$pet1."&arena=".$arena."&foe=".$pet2."' method='POST'>";
	$page .= "<input type='submit' name='fight' value='Fight!'><p>";
	$page .= "</form>";
	$page .= "<form action='arena.php' method='POST'>";
	$page .= "<input type='submit' name='quit' value='Cancel Duel'><p>";
	$page .= "</form>";
	$page .= "</td>";
	$page .= "<td width='50%' style='border: solid 1px black'>";
	$page .= "<p align='center'><b><u>".$p2row["name"]."</u></b><br>";
	$page .= "<i>(a ".$p2row["type"].")</i></p>";
	$page .= "<b>HP:</b> ".$p2row["currenthp"]."/".$p2row["maxhp"]."<br>";
	$page .= "<b>MP:</b> ".$p2row["currentmp"]."/".$p2row["maxmp"]."<br>";
	$page .= "<b>Strength:</b> ".$p2row["maxdam"]."<br>";
	$page .= "<b>Dexterity:</b> ".$p2row["dexterity"]."<br>";
	$page .= "<b>Armor:</b> ".$p2row["armor"]."<br>";
	$page .= "</td></tr>";
	$page .= "</table><p>";
	$p1id = $p1row["id"];
	$p1hp = $p1row["currenthp"];
	$p1mp = $p1row["currentmp"];
	$p1gold = $p1row["gold"];
	$p1foe = $p2row["id"];
	$p2id = $p2row["id"];
	$p2hp = $p2row["currenthp"];
	$p2mp = $p2row["currentmp"];
	$p2gold = $p2row["gold"];
	$p2foe = $p1row["id"];
	$p1query = doquery("UPDATE {{table}} SET currenthp='$p1hp',currentmp='$p1mp' WHERE id='$p1id'", "arena");
	$p2query = doquery("UPDATE {{table}} SET currenthp='$p2hp',currentmp='$p2mp' WHERE id='$p2id'", "arena");
	$page .= "<center><br>";
	$page .= "<form action='arena.php' method='POST'>";
	$page .= "<input type='submit' name='quit' value='Cancel Duel'><p>";
	$page .= "</form><p>A summary of your Pet and your opponents Pet Statistics and Duel is located above.</center>";
    display($page,"Pet Arena");
}


function dovictory($pet,$arena,$foe){
	global $userrow;
	if ($userrow["templist"] != "arenawin") {header("Location: arena.php"); die();}
	$page .= "<table width='100%'><tr><td class='title'>Pet Arena</td></tr></table> <p>";
	$pet1 = $_GET["pet"];
	$pet2 = $_GET["foe"];
	$arena = $_GET["arena"];
	$p1query = doquery("SELECT * FROM {{table}} WHERE id='$pet1' LIMIT 1", "arena");
	$p1row = mysql_fetch_array($p1query);
	$p2query = doquery("SELECT * FROM {{table}} WHERE id='$pet2' LIMIT 1", "arena");
	$p2row = mysql_fetch_array($p2query);
	$p1wins = $p1row["wins"] + 1;
	$p1lastwin = $p2row["name"]." (owner=".$p2row["trainer"].")";
	$p2lastloss = $p1row["name"]." (owner=".$p1row["trainer"].")";
	$p2loss = $p2row["losses"] + 1;
	$xpup = rand(1,(5*$p2row["level"]));
	$p1xp = $p1row["experience"] + $xpup;
	$p2xp = $p2row["experience"] - rand(1,$p1row["level"]);
	$level = $p1row["level"];
	$newskill = $p1row["skillpoints"];
	$dscales = rand($p2row["level"]*2,$p2row["level"]*3);
	$newdscales = $userrow["dscales"] + $dscales;
	$page .= "<b>You Win! Click below to continue.</b><p>";
	$page .= "Your Pet, ".$p1row["name"].", gained<b> $xpup </b>Experience points.<p>";
	$page .= "You earned $dscales Dragon Scales!<p>";
	if ($p1xp >= ($level * (50+$level))) {
		$level += 1;
		$newskill += 3;
		$p1xp = 0;
		$page .= "<br><b>Your pet gained a level!!</b><br>";
	}
	$page .= "<center><br><a href='arena.php'>Back to the Arena</a></center>";

	$p1query = doquery("UPDATE {{table}} SET experience='$p1xp',wins='$p1wins',lastwin='$p1lastwin',level='$level',skillpoints='$newskill' WHERE id='$pet1' ", "arena");
	$p2query = doquery("UPDATE {{table}} SET experience='$p2xp',losses='$p2losses',lastwin='$p2lastloss' WHERE id='$pet2'", "arena");
    	$uquery = doquery("UPDATE {{table}} SET dscales='$newdscales',templist='' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
    display($page,"Pet Arena");
}

function dodefeat($pet1,$arena,$pet2) {
	global $userrow;
	$pet1 = $_GET["pet"];
	$pet2 = $_GET["foe"];
	$arena = $_GET["arena"];
	$p1query = doquery("SELECT * FROM {{table}} WHERE id='$pet1' LIMIT 1", "arena");
	$p1row = mysql_fetch_array($p1query);
	$p2query = doquery("SELECT * FROM {{table}} WHERE id='$pet2' LIMIT 1", "arena");
	$p2row = mysql_fetch_array($p2query);
	$p2wins = $p2row["wins"] + 1;
	$p2lastwin = $p1row["name"]." (owner=".$p1row["trainer"].")";
	$p1lastloss = $p2row["name"]." (owner=".$p2row["trainer"].")";
	$p1loss = $p1row["losses"] + 1;
	$xpdown = rand(1,(3*$p2row["level"]));
	$p1xp = $p1row["experience"] - $xpdown;
	$p2xp = $p2row["experience"] + rand(1,(5*$p1row["level"]));
	$page .= "<table width='100%'><tr><td class='title'>Pet Arena</td></tr></table> <p>";
	$page .= "<b>You have lost! Click below to continue.</b><p>";
	$page .= "Your Pet, ".$p1row["name"].", lost<b> $xpdown </b>Experience points.<p>";
	$page .= "<center><br><a href='arena.php'>Back to the Arena</a></center>";

	$p1query = doquery("UPDATE {{table}} SET experience='$p1xp',losses='$p1loss',lastloss='$p1lastloss' WHERE id='$pet1'", "arena");
	$p2query = doquery("UPDATE {{table}} SET experience='$p2xp',wins='$p2wins',lastwin='$p2lastwin' WHERE id='$pet2'", "arena");

    display($page,"Pet Arena");
}


function dofeed($id) {
	global $userrow;

$updatequery = doquery("UPDATE {{table}} SET location='Feeding Pets' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	$page = "<table width='100%'><tr><td class='title'>Feeding Pets</td></tr></table>";
	$petquery = doquery("SELECT * FROM {{table}} WHERE trainer='".$userrow["charname"]."'", "arena");
	if (mysql_num_rows($petquery) <= 0) {
	$page .= "You do not have any captured Pets to use in the Arena!<br>";
	$page .= "In order to capture pets, you must use a capture spell ";
	$page .= "in battle against an enemy.  If the spell is strong enuogh to hold the creature, ";
	$page .= "and the monster has been weakened enough (low HP) you can capture it as a Pet.<p>";
	$page .= "You may go back to <a href='strongholds.php'>the Stronghold</a>, or leave and ";
	$page .= "<a href='index.php?do=move:0'>continue exploring</a>.<p>";
	display($page, "Feeding Pets");
	}

	if (!isset($id)) {
	$page .= "<p>Click on one of your Pets to heal its Health for a small price, by purchasing some food.<p> ";
	$page .= "<table width='95%' style='border: solid 1px black' cellspacing='0' cellpadding='0'>";
	$page .= "<tr><td colspan='5' bgcolor='#fffff'><center><b>Please Choose a Pet</b></center></td></tr>";
	$page .= "<tr><td><b>Name</b></td><td><b>Species</b></td><td><b>HP</b></td><td><b>Level</b></td><td><b>Win/Loss</b></td></tr>";
	$count = 2;
	while ($petrow = mysql_fetch_array($petquery)) {
		if ($count == 1) { $color = "bgcolor='#ffffff'"; $count = 2; }
		else { $color = "bgcolor='#eeeeee'"; $count = 1;}
		$page .= "<tr><td ".$color." width='25%'>";
		$page .= "<a href='arena.php?do=feed:".$petrow["id"]."'>".$petrow["name"]."</a></td>";
		$page .= "<td ".$color." width='25%'>".$petrow["type"]."</td>";
		$page .= "<td ".$color." width='20%'>".$petrow["currenthp"]."/".$petrow["maxhp"]."</td>";
		$page .= "<td ".$color." width='20%'>".$petrow["level"]."</td>";
		$page .= "<td ".$color." width='20%'>".$petrow["wins"]."/".$petrow["losses"]."</td>";
	  	$page .= "</tr>";
	}
	$page .= "</table>";

	} else {   // if you chose to view a Pet...
	$petquery = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "arena");
	$petrow = mysql_fetch_array($petquery);
	if (isset($_POST["treat"])) {
	  	if ($userrow["dscales"] < 1) {
	  	$page .= "<p><b>You do not have enough Dragon Scales!</b><br>";
	  	$page .= "A small treat costs 1 Dragon Scale, and you only have ".$userrow["dscales"].".<p>";
	  	$page .= "<center><br><a href='arena.php'>Return to the Arena</a></center>";
	  	display($page, "Viewing Pets");
	  	}
	  	$hp = intval($petrow["maxhp"] * 0.2);
	  	$newhp = $petrow["currenthp"] + $hp;
	  	if ($newhp > $petrow["maxhp"]) {$newhp = $petrow["maxhp"];}
	  	$q = doquery("UPDATE {{table}} SET currenthp='$newhp',currentmp='$newmp' WHERE id='$id'", "arena");
		$u = doquery("UPDATE {{table}} SET dscales='".($userrow["dscales"] - 1)."' WHERE id='".$userrow["id"]."'", "users");
	  	$petquery = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "arena");
		$petrow = mysql_fetch_array($petquery);
		$page .= "<p><b>The snack restored $hp health!</b><p>";
	$page .= "<table><form action='arena.php?do=feed:".$id."' method='POST'>";
	$page .= "<tr><td><input type='submit' name='treat' value='Treat'></td>";
	$page .= "<td>(1 Dragon Scale) A small treat that refills only a few health to your pet.</td></tr>";
	$page .= "<tr><td colspan='2'> </td></tr>";
	$page .= "<tr><td><input type='submit' name='snack' value='Snack'></td>";
	$page .= "<td>(2 Dragon Scales) A snack that restores a small amount of health.</td></tr>";
	$page .= "<tr><td colspan='2'> </td></tr>";
	$page .= "<tr><td><input type='submit' name='meal' value='Meal'></td>";
	$page .= "<td>(3 Dragon Scales) Restores up to 80% of your pet's health.</td></tr>";
	$page .= "<tr><td colspan='2'> </td></tr>";
	$page .= "<tr><td><input type='submit' name='megameal' value='MegaMeal'></td>";
	$page .= "<td>(5 Dragon Scales) Restores your pet's full health.</td></tr>";
	$page .= "<tr><td colspan='2'> </td></tr>";
	$page .= "</form></table>";
	  	$page .= "<table>";
	  	$page .= "<tr><td bgcolor='#eeeeee'><b>Name</b></td><td bgcolor='#eeeeee'><b>Species</b></td>";
	  	$page .= "<td bgcolor='#eeeeee'><b>HP</b></td><td bgcolor='#eeeeee'><b>Level</b></td>";
	  	$page .= "<td bgcolor='#eeeeee'><b>Win/Loss</b></td></tr>";
	  	$page .= "<tr><td bgcolor='#ffffff' width='25%'>".$petrow["name"]."</td>";
		$page .= "<td bgcolor='#ffffff' width='25%'>".$petrow["type"]."</td>";
		$page .= "<td bgcolor='#ffffff' width='20%'>".$petrow["currenthp"]."/".$petrow["maxhp"]."</td>";
		$page .= "<td bgcolor='#ffffff' width='20%'>".$petrow["level"]."</td>";
		$page .= "<td bgcolor='#ffffff' width='20%'>".$petrow["wins"]."/".$petrow["losses"]."</td></tr>";
		$page .= "</table>";
		$page .= "<center><br><a href='arena.php'>Return to the Arena</a></center>";
	  	display($page, "Viewing Pets");
  	}
  	if (isset($_POST["snack"])) {
  		if ($userrow["dscales"] < 2) {
  		$page .= "<p><b>You do not have enough Dragon Scales!</b><br>";
  		$page .= "A snack costs 2 Dragon Scales, and you only have ".$userrow["dscales"].".<p>";
  		$page .= "<center><br><a href='arena.php'>Return to the Arena</a></center>";
  		display($page, "Viewing Pets");
  	  	}
	  	$hp = intval($petrow["maxhp"] * 0.5);
	  	$newhp = $petrow["currenthp"] + $hp;
	  	if ($newhp > $petrow["maxhp"]) {$newhp = $petrow["maxhp"];}
	  	$q = doquery("UPDATE {{table}} SET currenthp='$newhp',currentmp='$newmp' WHERE id='$id'", "arena");
		$u = doquery("UPDATE {{table}} SET dscales='".($userrow["dscales"] - 2)."' WHERE id='".$userrow["id"]."'", "users");
	  	$petquery = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "arena");
		$petrow = mysql_fetch_array($petquery);
		$page .= "<p><b>The snack restored $hp health!</b><p>";
	$page .= "<table><form action='arena.php?do=feed:".$id."' method='POST'>";
	$page .= "<tr><td><input type='submit' name='treat' value='Treat'></td>";
	$page .= "<td>(1 Dragon Scale) A small treat that refills only a few health to your pet.</td></tr>";
	$page .= "<tr><td colspan='2'> </td></tr>";
	$page .= "<tr><td><input type='submit' name='snack' value='Snack'></td>";
	$page .= "<td>(2 Dragon Scales) A snack that restores a small amount of health.</td></tr>";
	$page .= "<tr><td colspan='2'> </td></tr>";
	$page .= "<tr><td><input type='submit' name='meal' value='Meal'></td>";
	$page .= "<td>(3 Dragon Scales) Restores up to 80% of your pet's health.</td></tr>";
	$page .= "<tr><td colspan='2'> </td></tr>";
	$page .= "<tr><td><input type='submit' name='megameal' value='MegaMeal'></td>";
	$page .= "<td>(5 Dragon Scales) Restores your pet's full health.</td></tr>";
	$page .= "<tr><td colspan='2'> </td></tr>";
	$page .= "</form></table>";
	  	$page .= "<table>";
	  	$page .= "<tr><td bgcolor='#eeeeee'><b>Name</b></td><td bgcolor='#eeeeee'><b>Species</b></td>";
	  	$page .= "<td bgcolor='#eeeeee'><b>HP</b></td><td bgcolor='#eeeeee'><b>Level</b></td>";
	  	$page .= "<td bgcolor='#eeeeee'><b>Win/Loss</b></td></tr>";
	  	$page .= "<tr><td bgcolor='#ffffff' width='25%'>".$petrow["name"]."</td>";
		$page .= "<td bgcolor='#ffffff' width='25%'>".$petrow["type"]."</td>";
		$page .= "<td bgcolor='#ffffff' width='20%'>".$petrow["currenthp"]."/".$petrow["maxhp"]."</td>";
		$page .= "<td bgcolor='#ffffff' width='20%'>".$petrow["level"]."</td>";
		$page .= "<td bgcolor='#ffffff' width='20%'>".$petrow["wins"]."/".$petrow["losses"]."</td></tr>";
		$page .= "</table>";
		$page .= "<center><br><a href='arena.php'>Return to the Arena</a></center>";
	  	display($page, "Viewing Pets");
  	}
  	if (isset($_POST["meal"])) {
  		if ($userrow["dscales"] < 3) {
  		$page .= "<p><b>You do not have enough Dragon Scales!</b><br>";
  		$page .= "A meal costs 5 Dragon Scales, and you only have ".$userrow["dscales"].".<p>";
  		$page .= "<center><br><a href='arena.php'>Return to the Arena</a></center>";
  		display($page, "Viewing Pets");
  	  	}
	  	$hp = intval($petrow["maxhp"] * 0.8);
	  	$newhp = $petrow["currenthp"] + $hp;
	  	if ($newhp > $petrow["maxhp"]) {$newhp = $petrow["maxhp"];}
	  	$q = doquery("UPDATE {{table}} SET currenthp='$newhp',currentmp='$newmp' WHERE id='$id'", "arena");
		$u = doquery("UPDATE {{table}} SET dscales='".($userrow["dscales"] - 3)."' WHERE id='".$userrow["id"]."'", "users");
	  	$petquery = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "arena");
		$petrow = mysql_fetch_array($petquery);
		$page .= "<p><b>The snack restored $hp health!</b><p>";
	$page .= "<table><form action='arena.php?do=feed:".$id."' method='POST'>";
	$page .= "<tr><td><input type='submit' name='treat' value='Treat'></td>";
	$page .= "<td>(1 Dragon Scale) A small treat that refills only a few health to your pet.</td></tr>";
	$page .= "<tr><td colspan='2'> </td></tr>";
	$page .= "<tr><td><input type='submit' name='snack' value='Snack'></td>";
	$page .= "<td>(2 Dragon Scales) A snack that restores a small amount of health.</td></tr>";
	$page .= "<tr><td colspan='2'> </td></tr>";
	$page .= "<tr><td><input type='submit' name='meal' value='Meal'></td>";
	$page .= "<td>(3 Dragon Scales) Restores up to 80% of your pet's health.</td></tr>";
	$page .= "<tr><td colspan='2'> </td></tr>";
	$page .= "<tr><td><input type='submit' name='megameal' value='MegaMeal'></td>";
	$page .= "<td>(5 Dragon Scales) Restores your pet's full health.</td></tr>";
	$page .= "<tr><td colspan='2'> </td></tr>";
	$page .= "</form></table>";
	  	$page .= "<table>";
	  	$page .= "<tr><td bgcolor='#eeeeee'><b>Name</b></td><td bgcolor='#eeeeee'><b>Species</b></td>";
	  	$page .= "<td bgcolor='#eeeeee'><b>HP</b></td><td bgcolor='#eeeeee'><b>Level</b></td>";
	  	$page .= "<td bgcolor='#eeeeee'><b>Win/Loss</b></td></tr>";
	  	$page .= "<tr><td bgcolor='#ffffff' width='25%'>".$petrow["name"]."</td>";
		$page .= "<td bgcolor='#ffffff' width='25%'>".$petrow["type"]."</td>";
		$page .= "<td bgcolor='#ffffff' width='20%'>".$petrow["currenthp"]."/".$petrow["maxhp"]."</td>";
		$page .= "<td bgcolor='#ffffff' width='20%'>".$petrow["level"]."</td>";
		$page .= "<td bgcolor='#ffffff' width='20%'>".$petrow["wins"]."/".$petrow["losses"]."</td></tr>";
		$page .= "</table>";
		$page .= "<center><br><a href='arena.php'>Return to the Arena</a></center>";
	  	display($page, "Viewing Pets");
  	}
  	if (isset($_POST["megameal"])) {
  		if ($userrow["dscales"] < 5) {
  		$page .= "<p><b>You do not have enough Dragon Scales!</b><br>";
  		$page .= "A mega-meal costs 5 Dragon Scales, and you only have ".$userrow["dscales"].".<p>";
  		$page .= "<center><br><a href='arena.php'>Return to the Arena</a></center>";
  		display($page, "Viewing Pets");
  		}
	  	$hp = intval($petrow["maxhp"]);
	  	$newhp = $petrow["currenthp"] + $hp;
	  	if ($newhp > $petrow["maxhp"]) {$newhp = $petrow["maxhp"];}
	  	$q = doquery("UPDATE {{table}} SET currenthp='$newhp',currentmp='$newmp' WHERE id='$id'", "arena");
		$u = doquery("UPDATE {{table}} SET dscales='".($userrow["dscales"] - 5)."' WHERE id='".$userrow["id"]."'", "users");
	  	$petquery = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "arena");
		$petrow = mysql_fetch_array($petquery);
		$page .= "<p><b>The snack restored $hp health!</b><p>";
	$page .= "<table><form action='arena.php?do=feed:".$id."' method='POST'>";
	$page .= "<tr><td><input type='submit' name='treat' value='Treat'></td>";
	$page .= "<td>(1 Dragon Scale) A small treat that refills only a few health to your pet.</td></tr>";
	$page .= "<tr><td colspan='2'> </td></tr>";
	$page .= "<tr><td><input type='submit' name='snack' value='Snack'></td>";
	$page .= "<td>(2 Dragon Scales) A snack that restores a small amount of health.</td></tr>";
	$page .= "<tr><td colspan='2'> </td></tr>";
	$page .= "<tr><td><input type='submit' name='meal' value='Meal'></td>";
	$page .= "<td>(3 Dragon Scales) Restores up to 80% of your pet's health.</td></tr>";
	$page .= "<tr><td colspan='2'> </td></tr>";
	$page .= "<tr><td><input type='submit' name='megameal' value='MegaMeal'></td>";
	$page .= "<td>(5 Dragon Scales) Restores your pet's full health.</td></tr>";
	$page .= "<tr><td colspan='2'> </td></tr>";
	$page .= "</form></table>";
	  	$page .= "<table>";
	  	$page .= "<tr><td bgcolor='#eeeeee'><b>Name</b></td><td bgcolor='#eeeeee'><b>Species</b></td>";
	  	$page .= "<td bgcolor='#eeeeee'><b>HP</b></td><td bgcolor='#eeeeee'><b>Level</b></td>";
	  	$page .= "<td bgcolor='#eeeeee'><b>Win/Loss</b></td></tr>";
	  	$page .= "<tr><td bgcolor='#ffffff' width='25%'>".$petrow["name"]."</td>";
		$page .= "<td bgcolor='#ffffff' width='25%'>".$petrow["type"]."</td>";
		$page .= "<td bgcolor='#ffffff' width='20%'>".$petrow["currenthp"]."/".$petrow["maxhp"]."</td>";
		$page .= "<td bgcolor='#ffffff' width='20%'>".$petrow["level"]."</td>";
		$page .= "<td bgcolor='#ffffff' width='20%'>".$petrow["wins"]."/".$petrow["losses"]."</td></tr>";
		$page .= "</table>";
		$page .= "<center><br><a href='arena.php'>Return to the Arena</a></center>";
	  	display($page, "Viewing Pets");
  	}
  	if (isset($_POST["special"])) {
  		if ($userrow["dscales"] < 1000) {
  		$page .= "<p><b>You do not have enough Dragon Scales!</b><br>";
  		$page .= "A Special Treat costs 1000 Dragon Scales, and you only have ".$userrow["dscales"].".<p>";
  		$page .= "<center><br><a href='arena.php'>Return to the Arena</a></center>";
  		display($page, "Viewing Pets");
  		}
  		$count = 0; $rcount = rand(3,5);
  		$newhp = $petrow["maxhp"];
  		$newmp = $petrow["maxmp"];
  		$newstr = $petrow["maxdam"];
  		$newdex = $petrow["dexterity"];
  		$newarm = $petrow["armor"];
  		$newmagearm = $petrow["magicarmor"];
  		while ($count < $rcount) {
  		$count += 1;
  		$stat = rand(1,7);
  		if ($stat == 1) {
	  	$hp = rand(0,3);
	  	$newhp = $petrow["maxhp"] + $hp;
	  	if ($hp >= 0){$gtxt = "gained";} else {$gtxt = "lost";}
		$page .= "<b>Your pet $gtxt ".abs($hp)." Health!</b><br>";
		}
		if ($stat == 2) {
		$mp = rand(0,3);
		$newmp = $petrow["maxmp"] + $mp;
		if ($mp >= 0){$gtxt = "gained";} else {$gtxt = "lost";}
		$page .= "<b>Your pet $gtxt ".abs($mp)." Magic Points!</b><br>";
		}
		if ($stat == 3) {
		$str = rand(0,3);
		$newstr = $petrow["maxdam"] + $str;
		if ($str >= 0){$gtxt = "gained";} else {$gtxt = "lost";}
		$page .= "<b>Your pet $gtxt ".abs($str)." Strength!</b><br>";
		}
  		if ($stat == 4) {
	  	$dex = rand(0,3);
	  	$newdex = $petrow["dexterity"] + $dex;
	  	if ($dex >= 0){$gtxt = "gained";} else {$gtxt = "lost";}
		$page .= "<b>Your pet $gtxt ".abs($dex)." Dexterity!</b><br>";
		}
  		if ($stat == 5) {
	  	$arm = rand(0,3);
	  	$newarm = $petrow["armor"] + $arm;
	  	if ($arm >= 0){$gtxt = "gained";} else {$gtxt = "lost";}
		$page .= "<b>Your pet $gtxt ".abs($arm)." Armor!</b><br>";
		}
  		if ($stat == 6) {
	  	$magearm = rand(0,3);
	  	$newmagearm = $petrow["magicarmor"] + $magearm;
	  	if ($magearm >= 0){$gtxt = "gained";} else {$gtxt = "lost";}
		$page .= "<b>Your pet $gtxt ".abs($magearm)." Magic Resistance!</b><br>";
		}
		}
		$q = doquery("UPDATE {{table}} SET maxhp='$newhp',maxmp='$newmp',maxdam='$newstr',dexterity='$newdex',armor='$newarm',magicarmor='$newmagearm',skillpoints='$newskill' WHERE id='$id'", "arena");
		$u = doquery("UPDATE {{table}} SET dscales='".($userrow["dscales"] - 100)."' WHERE id='".$userrow["id"]."'", "users");
	  	$petquery = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "arena");
		$petrow = mysql_fetch_array($petquery);
	$page .= "<table><form action='arena.php?do=feed:".$id."' method='POST'>";
	$page .= "<tr><td><input type='submit' name='treat' value='Treat'></td>";
	$page .= "<td>(1 Dragon Scale) A small treat that refills only a few health to your pet.</td></tr>";
	$page .= "<tr><td colspan='2'> </td></tr>";
	$page .= "<tr><td><input type='submit' name='snack' value='Snack'></td>";
	$page .= "<td>(2 Dragon Scales) A snack that restores a small amount of health.</td></tr>";
	$page .= "<tr><td colspan='2'> </td></tr>";
	$page .= "<tr><td><input type='submit' name='meal' value='Meal'></td>";
	$page .= "<td>(3 Dragon Scales) Restores up to 80% of your pet's health.</td></tr>";
	$page .= "<tr><td colspan='2'> </td></tr>";
	$page .= "<tr><td><input type='submit' name='megameal' value='MegaMeal'></td>";
	$page .= "<td>(5 Dragon Scales) Restores your pet's full health.</td></tr>";
	$page .= "<tr><td colspan='2'> </td></tr>";
	$page .= "</form></table>";
	  	$page .= "<table>";
	  	$page .= "<tr><td bgcolor='#eeeeee'><b>Name</b></td><td bgcolor='#eeeeee'><b>Species</b></td>";
	  	$page .= "<td bgcolor='#eeeeee'><b>HP</b></td><td bgcolor='#eeeeee'><b>Level</b></td>";
	  	$page .= "<td bgcolor='#eeeeee'><b>Win/Loss</b></td></tr>";
	  	$page .= "<tr><td bgcolor='#ffffff' width='25%'>".$petrow["name"]."</td>";
		$page .= "<td bgcolor='#ffffff' width='25%'>".$petrow["type"]."</td>";
		$page .= "<td bgcolor='#ffffff' width='20%'>".$petrow["currenthp"]."/".$petrow["maxhp"]."</td>";
		$page .= "<td bgcolor='#ffffff' width='20%'>".$petrow["level"]."</td>";
		$page .= "<td bgcolor='#ffffff' width='20%'>".$petrow["wins"]."/".$petrow["losses"]."</td></tr>";
		$page .= "</table>";
		$page .= "<center><br><a href='arena.php'>Return to the Arena</a></center>";
	  	display($page, "Viewing Pets");
	}

	$petquery = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "arena");
	$petrow = mysql_fetch_array($petquery);
	if ($petrow["trainer"] != $userrow["charname"]) {
	$page .= "You do not own that pet!<br>";
	$page .= "You may only view, feed, and release pets that are under your control.<p>";
	$page .= "You may go back to <a href='strongholds.php'>the Stronghold</a>, or leave and ";
	$page .= "<a href='index.php?do=move:0'>continue exploring</a>.<p>";
	display($page, "Feeding Pets");
	}

	$page .= "<h3>What would you like to feed ".$petrow["name"]."?</h3><p>";
	$page .= "<table><form action='arena.php?do=feed:".$id."' method='POST'>";
	$page .= "<tr><td><input type='submit' name='treat' value='Treat'></td>";
	$page .= "<td>(1 Dragon Scale) A small treat that refills only a few health to your pet.</td></tr>";
	$page .= "<tr><td colspan='2'> </td></tr>";
	$page .= "<tr><td><input type='submit' name='snack' value='Snack'></td>";
	$page .= "<td>(2 Dragon Scales) A snack that restores a small amount of health.</td></tr>";
	$page .= "<tr><td colspan='2'> </td></tr>";
	$page .= "<tr><td><input type='submit' name='meal' value='Meal'></td>";
	$page .= "<td>(3 Dragon Scales) Restores up to 80% of your pet's health.</td></tr>";
	$page .= "<tr><td colspan='2'> </td></tr>";
	$page .= "<tr><td><input type='submit' name='megameal' value='MegaMeal'></td>";
	$page .= "<td>(5 Dragon Scales) Restores your pet's full health.</td></tr>";
	$page .= "<tr><td colspan='2'> </td></tr>";
	$page .= "</form></table>";

	}
    $page .= "<center><br><a href='arena.php'>Return to the Arena</a></center>";
    display($page,"Pet Arena");
}





?>