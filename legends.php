<?php // legends.php :: Hall of legends
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
        // Hall of legends and main townin town square     
    if ($do[0] == "main") { main($do[1]); }
    if ($do[0] == "titles1") { titles1($do[1]); }
    if ($do[0] == "titles2") { titles2($do[1]); }
    if ($do[0] == "titles3") { titles3($do[1]); }
    if ($do[0] == "titles4") { titles4($do[1]); }
    if ($do[0] == "titles5") { titles5($do[1]); }
    
}
function main() { // Hall of Legends - Titles
    
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

$updatequery = doquery("UPDATE {{table}} SET location='Hall of Legends' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        
        $title = "Hall of Legends";
        $page = "<table width='100%' border='1'><tr><td class='title'>Hall of Legends</td></tr></table><p>";
        $page .= "Welcome to the Hall of Legends.<p>Here is where you come to claim your official title, and your reward of a new and unique spell.<p>You simply need to have a specific level requirement to claim each title, once you claim your desired titles, you will be put down in history into the Hall of Legends book. You will finally receive a reward of a spell.<p>I admit, that the spells you get for the lower titles are not very good, but the last ones are very, very powerful and can only be achieved from a title.<p>You may claim more than one title at a time, but it is best to keep the maximum title you can gain in your character profile.<p>Please choose the room you wish to visit, to claim your desired title:<br />\n";
        $page .= "<br /><br /><ul><li /><a href=\"legends.php?do=titles1\">Peasants Hall</a><li /><a href=\"legends.php?do=titles2\">Slayers Hall</a><li /><a href=\"legends.php?do=titles3\">Champions Hall</a><li /><a href=\"legends.php?do=titles4\">Heros Hall</a><li /><a href=\"legends.php?do=titles5\">Main Legends Hall</a></ul><br /><p><p>You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br />\n";      

        
    }
    
    display($page, $title);
    
}

function titles1() { // Title - Peasant
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

 if (isset($_POST["submit"])) {
        
if ($userrow["level"] < 15) { display("Sorry, but you do not currently meet the requirement level for this Title.<p>Please try again later when you are of a greater level.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass to the right to start exploring.","You do not meet the level requirement for this Title."); die(); } 
        $title = "Hall of Legends - You receive the Peasants Title.";

        $spellquery = doquery("SELECT id FROM {{table}}","spells");
        $userspells = explode(",",$userrow["spells"]);
        //add spell
        array_push($userspells, 60);
        $new_userspells = implode(",",$userspells);
        $userid = $userrow["id"];
        doquery("UPDATE {{table}} SET spells='$new_userspells', title='Peasant' WHERE id='$userid' LIMIT 1", "users");
        $page = "<table width='100%' border='1'><tr><td class='title'>Hall of Legends - You receive the Peasants Title</td></tr></table><p>";
        $page .= "You sign a certificate which grants you a new title of the <b>Peasant</b> and the man notes you down in the Hall of Legends book.<p>You also gain a new spell named: <b>Peasants Spell</b>.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.";
        
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
        
        $title = "Hall of Legends - Peasants Title";
        $page = "<table width='100%' border='1'><tr><td class='title'>Hall of Legends - Peasants Title</td></tr></table><p>";
        $page .= "Welcome to the Hall of Legends.<br /><br />\n";
        $page .= "If you meet the current level requirement of <b>level 15</b>, you are granted a new title of the <b>Peasant</b>. Not only do you receive this title, you also learn a new and unique spell which will be added to your current list of spells.<p>Once you meet the next title level requirement, you can then gain yet another new spell.<p>Do you wish to be blessed with the title of the <b>Peasant</b>?<br /><br />\n";
        $page .= "<form action=\"legends.php?do=titles1\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes Please\" /> <input type=\"submit\" name=\"cancel\" value=\"No Thanks\" />\n";
        $page .= "</form>\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br /><br />\n";
       
    }
    
    display($page, $title);
    
}

function titles2() { // Title - Slayer
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

 if (isset($_POST["submit"])) {
        
if ($userrow["level"] < 30) { display("Sorry, but you do not currently meet the requirement level for this Title.<p>Please try again later when you are of a greater level.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass to the right to start exploring.","You do not meet the level requirement for this Title."); die(); } 
        $title = "Hall of Legends - You receive the Slayers Title.";

        $spellquery = doquery("SELECT id FROM {{table}}","spells");
        $userspells = explode(",",$userrow["spells"]);
        //add spell
        array_push($userspells, 61);
        $new_userspells = implode(",",$userspells);
        $userid = $userrow["id"];
        doquery("UPDATE {{table}} SET spells='$new_userspells', title='Slayer' WHERE id='$userid' LIMIT 1", "users");
        $page = "<table width='100%' border='1'><tr><td class='title'>Hall of Legends - You receive the Slayers Title</td></tr></table><p>";
        $page .= "You sign a certificate which grants you a new title of the <b>Slayer</b> and the man notes you down in the Hall of Legends book.<p>You also gain a new spell named: <b>Slayers Spell</b>.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.";
        
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
        
        $title = "Hall of Legends - Slayers Title";
        $page = "<table width='100%' border='1'><tr><td class='title'>Hall of Legends - Slayers Title</td></tr></table><p>";
        $page .= "Welcome to the Hall of Legends.<br /><br />\n";
        $page .= "If you meet the current level requirement of <b>level 30</b>, you are granted a new title of the <b>Slayer</b>. Not only do you receive this title, you also learn a new and unique spell which will be added to your current list of spells.<p>Once you meet the next title level requirement, you can then gain yet another new spell.<p>Do you wish to be blessed with the title of the <b>Slayer</b>?<br /><br />\n";
        $page .= "<form action=\"legends.php?do=titles2\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes Please\" /> <input type=\"submit\" name=\"cancel\" value=\"No Thanks\" />\n";
        $page .= "</form>\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br /><br />\n";
       
    }
    
    display($page, $title);
    
}

function titles3() { // Title - Champion
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

 if (isset($_POST["submit"])) {
        
if ($userrow["level"] < 45) { display("Sorry, but you do not currently meet the requirement level for this Title.<p>Please try again later when you are of a greater level.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass to the right to start exploring.","You do not meet the level requirement for this Title."); die(); } 
        $title = "Hall of Legends - You receive the Champions Title.";
      
        $spellquery = doquery("SELECT id FROM {{table}}","spells");
        $userspells = explode(",",$userrow["spells"]);
        //add spell
        array_push($userspells, 62);
        $new_userspells = implode(",",$userspells);
        $userid = $userrow["id"];
        doquery("UPDATE {{table}} SET spells='$new_userspells', title='Champion' WHERE id='$userid' LIMIT 1", "users");
        $page = "<table width='100%' border='1'><tr><td class='title'>Hall of Legends - You receive the Champions Title</td></tr></table><p>";
        $page .= "You sign a certificate which grants you a new title of the <b>Champion</b> and the man notes you down in the Hall of Legends book.<p>You also gain a new spell named: <b>Champions Spell</b>.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.";
        
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
        
        $title = "Hall of Legends - Champions Title";
        $page = "<table width='100%' border='1'><tr><td class='title'>Hall of Legends - Champions Title</td></tr></table><p>";
        $page .= "Welcome to the Hall of Legends.<br /><br />\n";
        $page .= "If you meet the current level requirement of <b>level 45</b>, you are granted a new title of the <b>Champion</b>. Not only do you receive this title, you also learn a new and unique spell which will be added to your current list of spells.<p>Once you meet the next title level requirement, you can then gain yet another new spell.<p>Do you wish to be blessed with the title of the <b>Champion</b>?<br /><br />\n";
        $page .= "<form action=\"legends.php?do=titles3\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes Please\" /> <input type=\"submit\" name=\"cancel\" value=\"No Thanks\" />\n";
        $page .= "</form>\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br /><br />\n";
       
    }
    
    display($page, $title);
    
}

function titles4() { // Title - Hero
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

 if (isset($_POST["submit"])) {
        
if ($userrow["level"] < 70) { display("Sorry, but you do not currently meet the requirement level for this Title.<p>Please try again later when you are of a greater level.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass to the right to start exploring.","You do not meet the level requirement for this Title."); die(); } 
        $title = "Hall of Legends - You receive the Heros Title.";

        $spellquery = doquery("SELECT id FROM {{table}}","spells");
        $userspells = explode(",",$userrow["spells"]);
        //add spell
        array_push($userspells, 63);
        $new_userspells = implode(",",$userspells);
        $userid = $userrow["id"];
        doquery("UPDATE {{table}} SET spells='$new_userspells', title='Hero' WHERE id='$userid' LIMIT 1", "users");
        $page = "<table width='100%' border='1'><tr><td class='title'>Hall of Legends - You receive the Heros Title</td></tr></table><p>";
        $page .= "You sign a certificate which grants you a new title of the <b>Heros</b> and the man notes you down in the Hall of Legends book.<p>You also gain a new spell named: <b>Heros Spell</b>.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.";
        
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
        
        $title = "Hall of Legends - Heros Title";
        $page = "<table width='100%' border='1'><tr><td class='title'>Hall of Legends - Heros Title</td></tr></table><p>";
        $page .= "Welcome to the Hall of Legends.<br /><br />\n";
        $page .= "If you meet the current level requirement of <b>level 70</b>, you are granted a new title of the <b>Hero</b>. Not only do you receive this title, you also learn a new and unique spell which will be added to your current list of spells.<p>Once you meet the next title level requirement, you can then gain yet another new spell.<p>Do you wish to be blessed with the title of the <b>Hero</b>?<br /><br />\n";
        $page .= "<form action=\"legends.php?do=titles4\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes Please\" /> <input type=\"submit\" name=\"cancel\" value=\"No Thanks\" />\n";
        $page .= "</form>\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br /><br />\n";
       
    }
    
    display($page, $title);
    
}

function titles5() { // Title - Legend
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

 if (isset($_POST["submit"])) {
        
if ($userrow["level"] < 95) { display("Sorry, but you do not currently meet the requirement level for this Title.<p>Please try again later when you are of a greater level.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass to the right to start exploring.","You do not meet the level requirement for this Title."); die(); } 
        $title = "Hall of Legends - You receive the Legends Title.";

        $spellquery = doquery("SELECT id FROM {{table}}","spells");
        $userspells = explode(",",$userrow["spells"]);
        //add spell
        array_push($userspells, 64);
        $new_userspells = implode(",",$userspells);
        $userid = $userrow["id"];
        doquery("UPDATE {{table}} SET spells='$new_userspells', title='Legend' WHERE id='$userid' LIMIT 1", "users");
        $page = "<table width='100%' border='1'><tr><td class='title'>Hall of Legends - You receive the Legends Title</td></tr></table><p>";
        $page .= "You sign a certificate which grants you a new title of the <b>Legend</b> and the man notes you down in the Hall of Legends book.<p>You also gain a new spell named: <b>Legends Spell</b>.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.";
        
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
        
        $title = "Hall of Legends - Legends Title";
        $page = "<table width='100%' border='1'><tr><td class='title'>Hall of Legends - Legends Title</td></tr></table><p>";
        $page .= "Welcome to the Hall of Legends.<br /><br />\n";
        $page .= "If you meet the current level requirement of <b>level 95</b>, you are granted a new title of the <b>Legend</b>. Not only do you receive this title, you also learn a new and unique spell which will be added to your current list of spells.<p>Do you wish to be blessed with the title of the <b>Legend</b>?<br /><br />\n";
        $page .= "<form action=\"legends.php?do=titles5\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes Please\" /> <input type=\"submit\" name=\"cancel\" value=\"No Thanks\" />\n";
        $page .= "</form>\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br /><br />\n";
       
    }
    
    display($page, $title);
    
}
?>
