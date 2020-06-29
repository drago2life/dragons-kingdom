<?php // square.php :: Town square, contains many areas
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
    if ($do[0] == "main") { main($do[1]); }
    // Tavern and locals
    if ($do[0] == "tavern") { tavern($do[1]); }
    if ($do[0] == "ale") { ale($do[1]); }
    if ($do[0] == "whisk") { whisk($do[1]); }
    if ($do[0] == "dragon") { dragon($do[1]); }
    if ($do[0] == "locals") { locals($do[1]); }
    if ($do[0] == "locals1") { locals1($do[1]); }
    if ($do[0] == "locals2") { locals2($do[1]); }
    if ($do[0] == "locals3") { locals3($do[1]); }
    if ($do[0] == "locals4") { locals4($do[1]); } 
    elseif ($do[0] == "townmap") { townmap($do[1]); }  
    elseif ($do[0] == "build") { dobuild($do[1]); }
	elseif ($do[0] == "build2") { dobuild2(); }
    
}
function main() { // Town Square
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,ts_description FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);


$updatequery = doquery("UPDATE {{table}} SET location='Town Square' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        
        $title = "Town Square";
        $query = doquery("UPDATE {{table}} SET templist='0' LIMIT 1", "users");
        $page = "<table width='100%' border='1'><tr><td class='title'>Town Square</td></tr></table><p>";
        $page .= "".$townrow["ts_description"]."<br /><br />\n";
        $page .= "<ul><li /><a href=\"square.php?do=townmap\">".$townrow["name"]." Town Map</a><li /><a href=\"index.php?do=inn\">".$townrow["name"]." Inn</a><li /><a href=\"index.php?do=bank\">Town Bank</a><li /><a href=\"square.php?do=tavern\">Local Tavern</a><li /><a href=\"index.php?do=buy\">Town Blacksmith</a><li /><a href=\"index.php?do=maps\">Travel Store</a><li /><a href=\"market.php?do=main\">Market Place</a><li /><a href=\"square.php?do=build\">Construct a Home</a>\n";  

        if($userrow["home"] != "No") {
         $page .= "<li /><a href=\"index.php?do=homeportal\">Home Portal</a>\n"; 
        }       
        $page .= "<li /><a href=\"legends.php?do=main\">Hall of Legends</a></ul><p>You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br />\n";      
        {
        
    }
    
    display($page, $title);

}

function townmap() { // Town Map
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        if (isset($_POST["submit"])) {
        
    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
    
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {

$updatequery = doquery("UPDATE {{table}} SET location='Town Map' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        
        $title = "Town Map";
        $page = "<table width='100%' border='1'><tr><td class='title'>Town Map</td></tr></table><p>";
        $page .= "You view the town map, to see all the areas in this town:<br /><br /><center><img src=\"images/maps/townmap.jpg\" border=\"0\" alt=\"Town Map\" /></a></center><br /><br />\n";
        $page .= "<p>You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br />\n";      

        
    }
    
    display($page, $title);
    
}

function tavern() { // Local Tavern - Pub
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        if (isset($_POST["submit"])) {
        
    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
    
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {

$updatequery = doquery("UPDATE {{table}} SET location='Local Tavern' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        
        $title = "Local Tavern";
        $page = "<table width='100%' border='1'><tr><td class='title'>Local Tavern</td></tr></table><p>";
        $page .= "You walk into the Tavern, and look around.<p>As you walk through the Taverns louver doors, they close behind you and flap in and outwards.<p>Everyone looks at you, not knowing who you are..<p>The Barman looks at you and asks, What will it be?<p><i>You see all the available drinks lined up along the shelf of the bar, click a drink for more information</i>:<br />\n";
        $page .= "<br /><br /><ul><li /><a href=\"square.php?do=ale\">Mug of Ale</a><li /><a href=\"square.php?do=whisk\">Shot of Whiskey</a><li /><a href=\"square.php?do=dragon\">Dragons Special</a><br /><p><li /><a href=\"square.php?do=locals\">Or Speak to the Locals</a></ul><p><p>You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br />\n";      

        
    }
    
    display($page, $title);
    
}

function ale() { // Buying a Fine Ale gives more chance of item drops.
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,aleprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
    
    if ($userrow["gold"] < $townrow["aleprice"]) { display("You do not have enough gold to buy a fine Mug of Ale.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass to the right to start exploring.", "Inn"); die(); }
    
    if (isset($_POST["submit"])) {
        
        $newgold = $userrow["gold"] - $townrow["aleprice"];
        $query = doquery("UPDATE {{table}} SET gold='$newgold', drink='Mug of Ale' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Local Tavern - You drink your Mug of Ale";
        $page = "<table width='100%' border='1'><tr><td class='title'>Local Tavern - You drink your Mug of Ale</td></tr></table><p>";
        $page .= "You take a large sip of your fine <b>Mug of Ale</b> and it tastes very refreshing.<p>You feel a weird sensation inside of you, what could this mean?<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.";
        
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
        
        $title = "Local Tavern - Buy Mug of Ale";
        $page = "<table width='100%' border='1'><tr><td class='title'>Local Tavern - Buy Mug of Ale</td></tr></table><p>";
        $page .= "So you would like a <b>Mug of Ale</b> young adventurer?<br /><br />\n";
        $page .= "It only costs <b>" . $townrow["aleprice"] . " gold</b>. Rumour has it that it gives you great abilities such as having a better chance of finding monster drops more frequently.<p> But of course, you will be far luckier if you buy a fine Mug of Ale from one of our other more expensive Taverns in another town. <p>The only down side is that it appears to only last one nights heal, so if you get injured and need to heal, it will wear off and won't effect you anylonger. <p>What are you waiting for, I can't stand around here all day talking to you. <p>Would you like to buy one?<br /><br />\n";
        $page .= "<form action=\"square.php?do=ale\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes Please\" /> <input type=\"submit\" name=\"cancel\" value=\"No Thanks\" />\n";
        $page .= "</form>\n";
        $page .= "You may return to <a href=\"index.php\">town</a> or buy another drink at the <a href=\"square.php?do=tavern\">tavern</a>, or use the compass on the right to start exploring.<br /><br />\n";
       
        
    }
    
    display($page, $title);
    
}

function whisk() { // Buying a Shot of Whiskey gives more chance of winning at the Gambling Den.
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,whiskprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
    
    if ($userrow["gold"] < $townrow["whiskprice"]) { display("You do not have enough gold to buy a Shot of Whiskey.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass to the right to start exploring.", "Inn"); die(); }
    
    if (isset($_POST["submit"])) {
        
        $newgold = $userrow["gold"] - $townrow["whiskprice"];
        $query = doquery("UPDATE {{table}} SET gold='$newgold', drink='Shot of Whiskey' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Local Tavern - You drink your Shot of Whiskey";
        $page = "<table width='100%' border='1'><tr><td class='title'>Local Tavern - You drink your Shot of Whiskey</td></tr></table><p>";
        $page .= "You take a large gulp of your <b>Shot of Whiskey</b> and it tastes very refreshing.<p>You feel a weird sensation inside of you, what could this mean?<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.";
        
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
        
        $title = "Local Tavern - Buy a Shot of Whiskey";
        $page = "<table width='100%' border='1'><tr><td class='title'>Local Tavern - Buy a Shot of Whiskey</td></tr></table><p>";
        $page .= "So you want a <b>Shot of Whiskey</b>, young adventurer?<br /><br />\n";
        $page .= "It only costs <b>" . $townrow["whiskprice"] . " gold</b>. Rumour has it that it gives you great abilities such as having a better chance of winning at the casino more frequently.<p> But of course, you will be far luckier if you buy a Shot of Whiskey from one of our other more expensive Taverns in another town. <p>The only down side is that it appears to only last one nights heal, so if you get injured and need to heal, it will wear off and won't effect you anylonger. <p>What are you waiting for, I can't stand around here all day talking to you. <p>Would you like to buy one?<br /><br />\n";
        $page .= "<form action=\"square.php?do=whisk\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes Please\" /> <input type=\"submit\" name=\"cancel\" value=\"No Thanks\" />\n";
        $page .= "</form>\n";
        $page .= "You may return to <a href=\"index.php\">town</a> or buy another drink at the <a href=\"square.php?do=tavern\">tavern</a>, or use the compass on the right to start exploring.<br /><br />\n";
       
    }
    
    display($page, $title);
    
}

function dragon() { // Buying a Dragons Special gives more chance of an Excellent Hit.
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,dragprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
    
    if ($userrow["gold"] < $townrow["dragprice"]) { display("You do not have enough gold to buy a Dragons Special.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass to the right to start exploring.", "Not enough Gold"); die(); }
    
    if (isset($_POST["submit"])) {
        
        $newgold = $userrow["gold"] - $townrow["dragprice"];
        $query = doquery("UPDATE {{table}} SET gold='$newgold', drink='Dragons Special' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Local Tavern - You drink your Dragons Special";
        $page = "<table width='100%' border='1'><tr><td class='title'>Local Tavern - You drink your Dragons Special</td></tr></table><p>";
        $page .= "You take a large sip of your <b>Dragons Special</b> and it tastes very refreshing.<p>You feel a weird sensation inside of you, what could this mean?<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.";
        
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
        
        $title = "Local Tavern - Buy a Dragons Special";
        $page = "<table width='100%' border='1'><tr><td class='title'>Local Tavern - Buy a Dragons Special</td></tr></table><p>";
        $page = "So you would like a <b>Dragons Special</b>, young adventurer?<br /><br />\n";
        $page .= "It only costs <b>" . $townrow["dragprice"] . " gold</b>. Rumour has it that it gives you great abilities such as having a better chance of hitting an Excellent Hit more frequently.<p> But of course, you will be far luckier if you buy a Dragons Special from one of our other more expensive Taverns in another town. <p>The only down side is that it appears to only last one nights heal, so if you get injured and need to heal, it will wear off and won't effect you anylonger. <p>What are you waiting for, I can't stand around here all day talking to you. <p>Would you like to buy one?<br /><br />\n";
        $page .= "<form action=\"square.php?do=dragon\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes Please\" /> <input type=\"submit\" name=\"cancel\" value=\"No Thanks\" />\n";
        $page .= "</form>\n";
        $page .= "You may return to <a href=\"index.php\">town</a> or buy another drink at the <a href=\"square.php?do=tavern\">tavern</a>, or use the compass on the right to start exploring.<br /><br />\n";
       
    }
    
    display($page, $title);
    
}


function locals() { // Speak to the local drinkers for more information and tips
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        if (isset($_POST["submit"])) {
        
    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
    
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {

$updatequery = doquery("UPDATE {{table}} SET location='Speaking to Locals' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        
        $title = "Speak to the Locals";
        $page = "<table width='100%' border='1'><tr><td class='title'>Speak to the Locals</td></tr></table><p>";
        $page .= "You slowly and anxiously walk upto the corner of the tavern where you spot a group of locals exchanging laughs and drinks, sitting at a large round table:<br />\n";
        $page .= "<br />There is currently only three people who seem slightly interested in speaking to you.<p><ul><li /><a href=\"square.php?do=locals1\">Old Man</a><li /><a href=\"square.php?do=locals2\">Wealthy Gambler</a><li /><a href=\"square.php?do=locals3\">Wise Wizard</a><li /><a href=\"square.php?do=locals4\">Friend of Lucas</a></ul><p>There are several more people at the table, but they all completely ignore you...<p>You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br />\n";      

        
    }
    
    display($page, $title);
    
}


function locals1() { // Locals 1
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        if (isset($_POST["submit"])) {
        
    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
        
        $title = "Speak to the Locals - Old Man";
        $page = "<table width='100%' border='1'><tr><td class='title'>Speak to the Locals - Old Man</td></tr></table><p>";
        $page .= "<i>A mysterious Old Man turns round and begins speaking to you</i><p>Old Man: Hello there, how may I help you?<p>I am new here, and I am in need of an adventure, can you help me?<p>Old Man: Well young Adventurer, I'm unsure about that but I know what would get you a good reputation around here, and also put a smile back on the faces of the towns folk.<p>Whats that?<p>Old Man: Well, you have heard about the great King Black Dragon haven't you? A lot of people have tried to slay him but have failed, miserably. Are you up for the challenge?<p>I know very little about this Dragon, but I do know that I am not currently strong enough to take him on right now, maybe I can get a group of friends to team fight him with me...<p>Old Man: Whatever you decide to do, and if you do manage to slay him, return back here and I may have a reward for you.<p>Ok thanks, nice talking to you. I wonder how I am going to gather my fellow Adventurers to help me...<p><i>The Old Man turns back around in his seat and begins laughing again with his drunken friends.</i><br /><br />\n";
        $page .= "</ul><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br />\n";      

        
    }
    
    display($page, $title);
    
}

function locals2() { // Locals 2 - Gives gold
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        if (isset($_POST["submit"])) {
        
$resetquery = doquery("UPDATE {{table}} SET gold='5', bank='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $page = "<table width='100%' border='1'><tr><td class='title'>Speak to the Locals - Wealthy Gambler</td></tr></table><p>";
$page .= "The Wealthy Gambler emptys your inventory of gold, along with your banked gold and replaces it with <b>5 gold</b>.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.";
           $title = "Speak to the Locals - Wealthy Gambler";     
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
        
        $title = "Speak to the Locals - Wealthy Gambler";
        $page = "<table width='100%' border='1'><tr><td class='title'>Speak to the Locals - Wealthy Gambler</td></tr></table><p>";
        $page .= "<i>A Wealthy Gambler turns round and begins speaking to you</i><p>Wealthy Gambler: Hello there, how may I help you?<p>I heard a rumour that you give out gold to people who are in need of a nights sleep.<p>Wealthy Gambler: Well young Adventurer, you hear correctly but please don't go telling everybody about this otherwise I won't be so well off *Laughs*<p>If you want <b>5 gold</b> then I will be happy to give you that, so you can afford to heal, and anything else you may require.<p>In return, I want whatever gold you are currently holding. and all your gold from your bank, however small it may be. So if you are rich, I win, if you are poor, I lose.<p>What do you say?<br /><br />\n";      
        $page .= "<form action=\"square.php?do=locals2\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes please\" /> <input type=\"submit\" name=\"cancel\" value=\"Return to Town\" />\n";
        $page .= "</form>\n";
        
    }
    
    display($page, $title);

}

function locals3() { // Wise Wizard - Gives capture spell
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

 if (isset($_POST["submit"])) {
        
        $title = "Speak to the Locals - You receive the Lvl15 Capture Spell";

        $spellquery = doquery("SELECT id FROM {{table}}","spells");
        $userspells = explode(",",$userrow["spells"]);
        //add spell
        array_push($userspells, 65);
        $new_userspells = implode(",",$userspells);
        $userid = $userrow["id"];
        doquery("UPDATE {{table}} SET spells='$new_userspells' WHERE id='$userid' LIMIT 1", "users");
        $page = "<table width='100%' border='1'><tr><td class='title'>Speak to the Locals - You receive the Lvl15 Capture Spell</td></tr></table><p>";
        $page .= "You are handed a precious Stone, which makes you feel strange, but powerful.<p>Congratulations, you have learnt the Lvl15 Capture Spell!<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.";
        
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
        
        $title = "Speak to the Locals - Wise Wizard";
        $page = "<table width='100%' border='1'><tr><td class='title'>Speak to the Locals - Wise Wizard</td></tr></table><p>";
        $page .= "<i>A Wizard approaches you.</i><br /><br />\n";
        $page .= "Wise Wizard: Hello young one, would you like one of these 'rare' Capture Spells. These cannot be learnt, no, no, no. These are special.<p>What do they do?<p>Wise Wizard: Ahh, I thought you'd never ask. Come closer... A Capture spell Captures Pets for the Pet Arena which can be found within a Stronghold. Each spell is Unique. You can't learn them, you have to buy a powerful stone, from the Warlock.<p>Where is this Warlock you speak of?<p>Wise Wizard: I'm not sure, he comes and goes. Its hard to tell when and where he will be. But I may be able to help you, if you would like a Basic Capture Spell.<p>How much will it cost me? These sound interesting..<p>Wise Wizard: I don't need your Gold young one, I have all the Gold I could ever want. Here, take this precious Stone which will give you a Lvl15 Capture Spell. The level of the Spell means a lot. Whatever level it is, is the maximum monster level you can capture while using that specific spell.<p>Do you wish to take this gift?<br /><br />\n";
        $page .= "<form action=\"square.php?do=locals3\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes Please\" /> <input type=\"submit\" name=\"cancel\" value=\"No Thanks\" />\n";
        $page .= "</form>\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br /><br />\n";
       
    }
    
    display($page, $title);
    
}

function locals4() { // Friend of lucas - Gives ingredients for quest 2 AND part of Quest 4
    
    global $controlrow, $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
    
    if ($userrow["charclass"] == 1) { $userrow["charclass"] = $controlrow["class1name"]; }
    elseif ($userrow["charclass"] == 2) { $userrow["charclass"] = $controlrow["class2name"]; }
    elseif ($userrow["charclass"] == 3) { $userrow["charclass"] = $controlrow["class3name"]; }
    elseif ($userrow["charclass"] == 4) { $userrow["charclass"] = $controlrow["class4name"]; }
    elseif ($userrow["charclass"] == 5) { $userrow["charclass"] = $controlrow["class5name"]; }
    elseif ($userrow["charclass"] == 6) { $userrow["charclass"] = $controlrow["class6name"]; }
    elseif ($userrow["charclass"] == 7) { $userrow["charclass"] = $controlrow["class7name"]; }

 if($userrow["tempquest"] == "0") { //Quest 2 has been started and need to get ingredients for lucas       
        $title = "Speak to the Locals - Friend of Lucas";
        $page = "<table width='100%' border='1'><tr><td class='title'>Speak to the Locals - Friend of Lucas</td></tr></table><p>";
        $page .= "You look around the Tavern, trying to find Lucas' friend, and finally decide it must be the man drinking by himself in the corner. You approach him.<br /><br />\n";
        $page .= "\"Excuse me, but do you know Lucas?\" you ask when he looks up from his ale.<br /><br />\n";
        $page .= "He stares at you a long moment. \"Now suppose I did. What would that matter to one of you ".$userrow["charclass"]." types?\" he asks cautiously.<br /><br />\n";
        $page .= "He glares at you as you sit down opposite him, but you decide to ignore this.<br /><br />\n";
        $page .= "\"I'm ".$userrow["charname"].". Lucas sent me to retrieve some Ingredients he needs for his son. You have them?\"<br /><br />\n";
	$page .= "The man nods, and reaches into his pocket. He removes a small pouch.<br /><br />\n";
	$page .= "\"Glad to be rid of them, too,\" he says sourly. \"Stink like the Black Dragon, they do.\"<br /><br />\n";
	$page .= "\"Thank you. I must hurry away.\" You stand.<br /><br />\n";
	$page .= "Without replying, Lucas' friend returns to his mug.<br /><br />\n";
	$inventitemsquery = doquery("SELECT id FROM {{table}}","inventitems");
        $userinventitems = explode(",",$userrow["inventitems"]);
        //add item
        array_push($userinventitems, 89);
        $new_userinventitems = implode(",",$userinventitems);
        $userid = $userrow["id"];
        doquery("UPDATE {{table}} SET inventitems='$new_userinventitems',tempquest='ingredients' WHERE id='$userid' LIMIT 1", "users");
        
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br /><br />\n";
       
    }
    
           elseif($userrow["tempquest"] == "gotflower") { //Second Part of Quest 4     
        $title = "Speak to the Locals - Friend of Lucas";
        $page = "<table width='100%' border='1'><tr><td class='title'>Speak to the Locals - Friend of Lucas</td></tr></table><p>";
        $page .= "You stumble into the Tavern, knocking over a table by the door.<br /><br />\n";
        $page .= "\"Take a Dragon-bitten care where you're going!\" someone calls.<br /><br />\n";
        $page .= "Apologising profusely, you explain that it might be easier to do so if the room would just stop spinning. You clutch the King's Grace Flower before you like a talisman and present yourself to the blurry shadow that you hope is Lucas. You feel him take the flower from your hands.<br /><br />\n";
        $page .= "\"I'll just need to run home to mix…\" A pause. Lucas considers your state. \"On second thoughts, I'll make it here.\"<br /><br />\n";        
        $page .= "He calls for a mortar and pestle. You are able to concentrate vaguely on his form as he starts pulling a myriad of other ingredients from about his person. Just as you decide the sawdust covered floor <i>does</i> look comfortable enough to sleep on, he tilts your head back and forces a potion down you throat. Almost instantly you feel stronger.<br /><br />\n";
        $page .= "<font color=green>Your HP, MP and TP have been restored!</font><br /><br />\n";         
        $page .= "In fact, you feel fitter than you did before you started this quest.<br /><br />\n";
        $page .= "<font color=green>You have gained 18,000 Endurance experience points!</font><br /><br />\n";         
        $page .= "The shapes around you resolve into people. Sitting opposite you is a rather stricken looking Lucas, wringing his hands relentlessly. You recognise Lucas' friend to his right, concentrating hard on drinking his ale.<br /><br />\n";
        $page .= "\"I am terribly sorry,\" Lucas says. \"I had no idea that would happen. It's not much, but I would feel better if you took these. I mean, I have no real use for them. Please take them.\" He pushes a sack across the table towards you and stands up. \"You had better go and visit Magnus, too,\" he tells you. Before leaving the two men, you inspect your gift.<br /><br />\n";
        $page .= "<font color=green>You have gained 30 Dragon Scales.</font><br /><br />\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br /><br />\n";
$inventitems = "0,".$userrow["inventitems"].",0"; //Remove the flower
$newinventitems = str_replace(",98,", ",", $inventitems);
$updatequery = doquery("UPDATE {{table}} SET inventitems='$newinventitems', endurancexp=endurancexp+18000, dscales=dscales+30, currenthp='".$userrow["maxhp"]."',currentmp='".$userrow["maxmp"]."',currenttp='".$userrow["maxtp"]."', tempquest='magnus' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 

           }    
       elseif($userrow["tempquest"] == "slimedead") { //First Part of Quest 4     
        $title = "Speak to the Locals - Friend of Lucas";
        $page = "<table width='100%' border='1'><tr><td class='title'>Speak to the Locals - Friend of Lucas</td></tr></table><p>";
        $page .= "You run into the Tavern, hoping that Lucas' friend can tell you where to find your poisoner. Someone grabs your arm as you make your way between the tables. You try feebly to shake them off.<br /><br />\n";
        $page .= "\"".$userrow["charname"]."!\" they snap, before slapping you in the face.<br /><br />\n";
        $page .= "You turn to face a worried looking Lucas. He looks quite scared by the fact that he's just hit you. You raise you hands to show that you're not going to exact revenge. He calms slightly.<br /><br />\n";
        $page .= "\"I'm sorry about that, ".$userrow["charname"].", but I'm afraid you may not have much time. Magnus has explained to me what happened. I can heal you with the same potion that cured my son, but I have run out of King's Grace. It is a flower that grows just north of the Abandoned Ruins. You need to go and bring me some before it's too late. I would go myself, but I'm still a little worried about my son. I would not like to leave him for so long.\"<br /><br />\n";
        $page .= "\"But I don't know what a King's Grace looks like!\"<br /><br />\n";
        $page .= "\"No matter,\" Lucas says. \"It's the only thing that grows up there. Now go, please!\"<br /><br />\n";
	$page .= "As you turn and leave, you notice that your balance is slightly off. You had better hurry!<br /> <br />\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET tempquest='flower' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 

       }
       
              elseif($userrow["tempquest"] == "flower") { //First .5 Part of Quest 4     
        $title = "Speak to the Locals - Friend of Lucas";
        $page = "<table width='100%' border='1'><tr><td class='title'>Speak to the Locals - Friend of Lucas</td></tr></table><p>";
        $page .= "Lucas is still in the Tavern when you return.<br /><br />\n";
        $page .= "\"What are you doing back here?\" he asks. \"You should be finding a King's Grace Flower at the Abandoned Ruins!\"<br /><br />\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br /><br />\n";


       }
    
      elseif($userrow["tempquest"] == "ingredients") { //Quest 2 - already have ingredients, go take them to lucas  
        $title = "Speak to the Locals - Friend of Lucas";
        $page = "<table width='100%' border='1'><tr><td class='title'>Speak to the Locals - Friend of Lucas</td></tr></table><p>";
        $page .= "Lucas' friend looks at you questioningly as you re-enter the Tavern.<br /><br />\n";
        $page .= "\"L-U-C-A-S. Take them to Lucas.\" He says slowly. He waits until you have turned back to the door before drooping back over his ale mug.<br /><br />\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br /><br />\n";
   } 
    
   elseif($userrow["quest2"] == "Complete") { //Quest 2 has been started and need to get ingredients for lucas       
        $title = "Speak to the Locals - Friend of Lucas";
        $page = "<table width='100%' border='1'><tr><td class='title'>Speak to the Locals - Friend of Lucas</td></tr></table><p>";
        $page .= "Lucas' friend tips his head back and pours the rest of his ale down his throat. Wiping the froth from his mouth with the back of his sleeve, he looks at you.<br /><br />\n";
        $page .= "\"Listen, I just wanted to thank you for helping Lucas' son.\"<br /><br />\n";
        $page .= "Just as you begin to launch into a speech about the honour of helping others and the trials you selflessly endure, his head drops to the table. To the sounds of Lucas' friend snoring, you leave the tavern.<br /><br />\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br /><br />\n";
   }

else {

        $page .= "<table width='100%' border='1'><tr><td class='title'>Speak to the Locals - Friend of Lucas</td></tr></table><p>";
        $page .= "You approach a man who is drinking by himself in the corner. He tears his attention away from his tankard of ale at your approach.<br /><br />\n";
        $page .= "\"And who might you be, disturbing my meditation?\" he asks gruffly.<br /><br />\n";
        $page .= "\"I am ".$userrow["charname"]." the ".$userrow["charclass"]."!\" you say.<br /><br />\n";
	$page .= "\"Well, if you're buying my drinks, then you're a friend. Otherwise, get out of my meditation space. You're ruining my <i>ch'i</i>\" He returns to his ale.<br /><br />\n";
	$page .= "This drunkard is of no help to you, and you doubt he ever will be. Deciding that staying would just provoke him further, you leave.<br /><br />\n";
   }    

    display($page, $title);
    
}

function dobuild() {
	global $userrow;

	$castlequery = doquery("SELECT * FROM {{table}} WHERE charname='".$userrow["charname"]."' ", "homes");
	$castlerow = mysql_fetch_array($castlequery);

	if (mysql_num_rows($castlequery) >= 1) {
		$page = "<table width='100%'><tr><td class='title'>Build a Home</td></tr></table>";
		$page .= "<p>You already have a Home. You may only have one. You can move your current house by visiting it and selecting the appropriate link.<br>";
	    $page .= "<center><br><a href='index.php'>Back to the Town</a></center>";
		display($page, "Building a Home");
	}
	if ($userrow["dscales"] < 150) {
		$page = "<table width='100%'><tr><td class='title'>Build a new Home</td></tr></table>";
		$page .= "<p>You do not have enough Dragon Scales to build a Home.<br>";
		$page .= "You need to have at least 150 Dragon Scales to build a Home.<p>";
		$page .= "There are currently <b>".$userrow["dscales"]."</b> Dragon Scales available.<p>";
    		$page .= "<center><br><a href='index.php'>Return to Town</a></center>";
		display($page, "Building a Home");
	}
	if ($userrow["currentap"] <= 14) {
		$page = "<table width='100%'><tr><td class='title'>Not Enough Ability Points!</td></tr></table>";
		$page .= "<br><b>You do not have enough AP to perform this action.</b><br><br>";
		$page .= "You must have at least 15 AP in order to build a Home.";
		$page .= "Your AP is refilled for a price of 50 Dragon Scales.<br>";
		$page .= "<p>You may return to <a href='index.php'>Town</a>, ";
		$page .= "or leave and <a href='index.php'>continue exploring</a>.";
		display($page, "Not Enough AP");
	}

    if (isset($_POST["submit"])) {

$updatequery = doquery("UPDATE {{table}} SET location='Building a Home' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

		$page = "<table width='100%'><tr><td class='title'>Build a Home</td></tr></table>";
		$page .= "<p>You may build a Home anywhere on the map with the following restrictions:";
		$page .= "<ul><li>The Home can not be within 5 steps of any town.";
		$page .= "<li>The Home can not be within 25 steps of any other Stronghold.";
				$page .= "<li>The Home can not be within 5 steps of any other Home.";
		$page .= "<li>The Home must at least be 300 steps from the edge of the map.<br>";
		$page .= "<i>Maximum 300 latitude or longitude. Too dangerous to go above 300.</i></ul>";
		$page .= "<table width='100%' border='0'>";
		$page .= "<tr><td><form action='square.php?do=build2' method='POST'>";
		$page .= "<b>Enter coordinates for your Home:</b><hr></td></tr>";
		$page .= "<tr><td>Latitude: (25-300)<br>";
		$page .= "<input type='text' length='5' name='latitude'><br>";
		$page .= "<input type='radio' value='1' name='d_lat'>North";
		$page .= "<input type='radio' value='-1' name='d_lat'>South</td></tr>";
		$page .= "<tr><td><hr></td></tr>";
		$page .= "<tr><td>Longitude: (25-300)<br>";
		$page .= "<input type='text' length='5' name='longitude'><br>";
		$page .= "<input type='radio' value='1' name='d_lon'>East";
		$page .= "<input type='radio' value='-1' name='d_lon'>West</td></tr>";
		$page .= "<tr><td><hr></td></tr>";
		$page .= "<tr><td><input type='submit' name='submit' value='Build your Home'></form>";
		$page .= "</td><td><form action='index.php'>";
		$page .= "<input type='submit' name='cancel' value='Cancel'></form></td></tr></table>";

		$page .= "<center><br><a href='index.php'>Return to Town</a></center>";
    } elseif (isset($_POST["cancel"])) {
        header("Location: index.php"); die();

    } else {
	$page = "<table width='100%'><tr><td class='title'>Build a Home</td></tr></table>";
	$page .= "<p>It costs 150 Dragon Scales and 15 AP to construct a Home.<br>";
	$page .= "You currently have ".$userrow["dscales"]." Dragon Scales available.<br>";
	$page .= "Do you wish to construct a Home for yourself?<p>";
	$page .= "<form action='square.php?do=build' method='post'>\n";
	$page .= "<input type='submit' name='submit' value='Yes' />  \t";
	$page .= "<input type='submit' name='cancel' value='No' />\n";
	$page .= "</form>\n";
	$page .= "<center><br><a href='index.php'>Return to Town</a></center>";


    }
    display($page,"Build a Home");
}

function dobuild2() {
	global $userrow;

	if ($userrow["currentap"] <= 14) {
		$page = "<table width='100%'><tr><td class='title'>Not Enough Ability Points!</td></tr></table>";
		$page .= "<br><b>You do not have enough AP to perform this action.</b><br><br>";
		$page .= "You must have at least 15 AP in order to build a Home. ";
		$page .= "Your AP is refilled for a price of 50 Dragon Scales.<br>";
		$page .= "<p>You may return to <a href='index.php'>Town</a>, ";
		$page .= "or leave and <a href='index.php'>continue exploring</a>.";
		display($page, "Not Enough AP");
	}

	$page = "<table width='100%'><tr><td class='title'>Building a Home</td></tr></table>";
    if (!isset($_POST["submit"])) {
	$page .= "Invalid command!<p>";
	$page .= "<center><br><a href='index.php'>Return to Town</a></center>";
    display($page,"Building a Home");
    }
    $page = "<table width='100%'><tr><td class='title'>Building a Home</td></tr></table>";
    $lat = $_POST["latitude"];
    $lon = $_POST["longitude"];
	unset($errors);
	if (($lat > 300) || ($lat < 25)) {
		$errors .= "There are the following errors while constructing your Home, please go back and try again:<p><b>Invalid Latitude!</b><br>Latitude must be between 25 and 300<p>";
	}
	if (($lon > 300) || ($lon < 25)) {
		$errors .= "<b>Invalid Longitude!</b><br>Longitude must be between 25 and 300<p>";
	}
	$lat = ($_POST["latitude"] * $_POST["d_lat"]);
    $lon = ($_POST["longitude"] * $_POST["d_lon"]);
    
    $homesquery = doquery("SELECT * FROM {{table}} WHERE latitude<'".($lat+5)."' AND latitude>'".($lat-5)."' AND longitude<'".($lon+5)."' AND longitude>'".($lon-5)."' ", "homes");
	if (mysql_num_rows($homesquery) > 0) {
		$errors .= "<b>There are Homes nearby!</b><br>";
		while ($homesrow = mysql_fetch_array($homesquery)) {;
			$errors .= "A Home belonging to ".$homesrow["charname"]." is at ".$homesrow["latitude"];
			$direction = 'E';
			if ($homesrow["latitude"] < 0) {$direction='W';}
			$errors .= $direction .", ".$homesrow["longitude"];
			$direction = 'N';
			if ($homesrow["longitude"] < 0) {$direction='S';}
			$errors .= $direction."<br>";
		}
	}

	unset($homesrow);

	$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude<'".($lat+25)."' AND latitude>'".($lat-25)."' AND longitude<'".($lon+25)."' AND longitude>'".($lon-25)."' ", "strongholds");
	if (mysql_num_rows($castlequery) > 0) {
		$errors .= "<b>There are Strongholds nearby!</b><br>";
		while ($castlerow = mysql_fetch_array($castlequery)) {;
			$errors .= "A Stronghold belonging to ".$castlerow["guildname"]." is at ".$castlerow["latitude"];
			$direction = 'E';
			if ($castlerow["latitude"] < 0) {$direction='W';}
			$errors .= $direction .", ".$castlerow["longitude"];
			$direction = 'N';
			if ($castlerow["longitude"] < 0) {$direction='S';}
			$errors .= $direction."<br>";
		}
	}

	unset($castlerow);
	$townquery = doquery("SELECT * FROM {{table}} WHERE latitude<'".($lat+25)."' AND latitude>'".($lat-25)."' AND longitude<'".($lon+25)."' AND longitude>'".($lon-25)."' ", "towns");
	if (mysql_num_rows($townquery) > 0) {
		$errors .= "<b>There are Towns nearby!</b><br>";
		while ($townrow = mysql_fetch_array($townquery)) {;
			$errors .= $townrow["name"]." is at ".$townrow["latitude"];
			$direction = 'E';
			if ($townrow["latitude"] < 0) {$direction='W';}
			$errors .= $direction .", ".$townrow["longitude"];
			$direction = 'N';
			if ($townrow["longitude"] < 0) {$direction='S';}
			$errors .= $direction."<br>";
		}
	}

	if (isset($errors)) {
		$page .= $errors;
		$page .= "<center><br><a href='index.php'>Back to the Town</a></center>";
		display($page,"Building a Home");
		die();
	}
	$new = doquery("INSERT INTO {{table}} SET latitude='$lat',longitude='$lon',charname='".$userrow["charname"]."' ", "homes");
	$newdscales = $userrow["dscales"] - 150;
	$g = doquery("UPDATE {{table}} SET dscales='$newdscales', home='Yes' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	$page .= "You have successfully constructed your Home at ";

	if ($_POST['d_lon'] == '1')	{
		$lond = "E";
	}
	else {
		$lond = "W";
	}
	
	if ($_POST['d_lat'] == '1')	{
		$lati = "N";
	}
	else {
		$lati = "S";
	}
	
	$page .= abs($lat) . $lati . " " . abs($lon) . $lond . ".";
	$newap = $userrow["currentap"] - 15;
	$pquery = doquery("UPDATE {{table}} SET currentap='$newap' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	$page .= "<center><br><a href='index.php'>Back to the Town</a></center>";
	display($page,"Building a Home");

}
?>