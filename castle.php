<?php // castle.php :: Castle Areas

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
if ($controlrow["gameopen"] == 0) { 
			header("Location: index.php"); die();
}
//Must vote
if (($userrow["poll"] != "Voted") && ($userrow["level"] >= "3")) { header("Location: poll.php"); die(); }
// Force verify if the user isn't verified yet.
if ($controlrow["verifyemail"] == 1 && $userrow["verify"] != 1) { header("Location: users.php?do=verify"); die(); }
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

    // Castle Areas
    if ($do[0] == "lake") { lake(); }
    elseif ($do[0] == "gate") { gate(); }
    //Main floor and areas   
    elseif ($do[0] == "main") { main(); }
    elseif ($do[0] == "dungeon") { dungeon(); }
    elseif ($do[0] == "folk") { folk(); }
    //Second floor and areas
    elseif ($do[0] == "second") { second(); }
    //Folk
    elseif ($do[0] == "princess") { princess(); }
    elseif ($do[0] == "king") { king(); }
    elseif ($do[0] == "knight") { knight(); } 
    elseif ($do[0] == "attackknight") { attackknight(); }                    
                    
}
    
function lake() { // Castle Lake
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

$updatequery = doquery("UPDATE {{table}} SET location='Castle Lake' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        
        $title = "Castle Lake";
        $page = "<table width='100%' border='1'><tr><td class='title'>Castle Lake</td></tr></table><p>";
        $page .= "You walk upto the Castle Lake. It looks so peaceful...<br /><br />\n";
        $page .= "Castle Guard: Don't get any ideas about Fishing in that lake young one! Remove yourself this instance or else I will have to escort you away.<br /><br />\n";
        $page .= "<p>Return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br />\n";     

    display($page, $title);
   

}



function gate() { // Castle Gate
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

$updatequery = doquery("UPDATE {{table}} SET location='Castle Gate' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        
        $title = "Castle Gate";
        $page = "<table width='100%' border='1'><tr><td class='title'>Castle Gate</td></tr></table><p>";
        
        
        $page .= "You walk upto the large Castle Gate, which has a Guard outside.<br /><br />\n";
        $page .= "Castle Guard: You don't have permission to enter this Castle. Remove yourself this instance or else I will have to escort you away. You require a Castle Pass and a Key to enter this Castle. All guards have their own Pass, and their own unique Key.<br /><br />\n";
        $page .= "<p>You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br />\n";     
           
        
    display($page, $title);
        }



//Main floor
function main() { // Main castle floor
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

$updatequery = doquery("UPDATE {{table}} SET location='Inside the Castle' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
                if ($userrow["quest5"] != "Complete") { 
			header("Location: index.php"); die(); }// Check to see if quest is completed
			
        $title = "Inside the Castle";
        $page = "<table width='100%' border='1'><tr><td class='title'>Inside the Castle</td></tr></table><p>";
        $page .= "You show your Castle Pass to a Guard and enter the Castle, without any of the Guards being suspicious.<br /><br />\n";
        $page .= "You see the following areas to visit:<br />\n";
        $page .= "<ul><li /><a href=\"castle.php?do=second\">Go Upstairs</a><li /><a href=\"castle.php?do=folk\">Castle Folk</a><li /><a href=\"castle.php?do=dungeon\">Enter Dungeon</a></ul><br /><br />\n";        
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br />\n";      
    
    display($page, $title);
   

}

function dungeon() { // Main dungeon
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

$updatequery = doquery("UPDATE {{table}} SET location='Castle Dungeon' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
                if ($userrow["quest5"] != "Complete") { 
			header("Location: index.php"); die(); }// Check to see if quest is completed
        $title = "Castle Dungeon";
        $page = "<table width='100%' border='1'><tr><td class='title'>Castle Dungeon</td></tr></table><p>";
        $page .= "You slowly enter the dark Dungeon. It is too dangerous for you to continue exploring this unknown Dungeon. You will also require some light source.<p>\n";         
        $page .= "Dungeon Latitude: 0N<br>Dungeon Longitude: 0E<p><br /><br />\n"; 
        $page .= "You may return to the Castle main <a href=\"castle.php?do=main\">floor</a>, or use the compass on the right to start exploring.<br />\n";      
    
    display($page, $title);
   

}

function folk() { // Main floor speaking to people
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
    
$updatequery = doquery("UPDATE {{table}} SET location='Castle Folk' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
                if ($userrow["quest5"] != "Complete") { 
			header("Location: index.php"); die(); }// Check to see if quest is completed
        $title = "Castle Folk";
        $page = "<table width='100%' border='1'><tr><td class='title'>Castle Folk</td></tr></table><p>";
        $page .= "I don't think its a good idea for you to make yourself too noticed, they may get suspicious...<p><ul><li /><a href=\"castle.php?do=princess\">Princess Larissa</a><li /><a href=\"castle.php?do=king\">King Arthur</a><li /><a href=\"castle.php?do=knight\">A Knight</a></ul><p>\n";         
        $page .= "You may return to the Castle main <a href=\"castle.php?do=main\">area</a>, or use the compass on the right to start exploring.<br />\n";      
    
    display($page, $title);
   
}

function princess() { // Princess
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
                if ($userrow["quest5"] != "Complete") { 
			header("Location: index.php"); die(); }// Check to see if quest is completed
        $title = "Princess Larissa";
        $page = "<table width='100%' border='1'><tr><td class='title'>Princess Larissa</td></tr></table><p>";
        $page .= "".$userrow["charname"].": Hello again!<br /><br />\n";
        $page .= "Princess Larissa: Hello ".$userrow["charname"]."! What are you doing here? How on earth did you get past the Guards?<br /><br />\n";
        $page .= "".$userrow["charname"]." Well that was simple, I merely got a key and Guard pass, and the guards allowed me to enter. You really should get better security around here. But, please don't tell anyone that I am here under a false Guard pass.<br /><br />\n";
        $page .= "Princess Larissa: Wow.. that easy? I won't tell anyone, aslong as you don't get upto anything bad while wandering around the Castle. Its the least I can do, after you helping me with my Ring.<br /><br />\n";
        $page .= "".$userrow["charname"].": Thank you Princess Larissa!<br /><br />\n";
        $page .= "Princess Larissa: If you ever need to Heal, then feel free to come to me, and I will allow you to rest here for a while, without anyone knowing.<br /><br />\n";
        $page .= "".$userrow["charname"].": Thanks again, I will hold you to that. I was wondering whether you have a Lantern, or something which will aid me through the Dungeon?<br /><br />\n";
        $page .= "Princess Larissa: As a matter of fact, I do have a Lantern which you can use but I really don't recommend going down into that dark Dungeon though.<br /><br />\n";
        $page .= "<i>She hands you an old Lantern.</i><br /><br />\n";
        $page .= "".$userrow["charname"].": Thanks! I will return soon, whenever I need to Heal.<br /><br />\n"; 
        $page .= "Princess Larissa: Ok, I will see you soon.<br /><br />\n"; 
        $page .= "You may return to the Castle main <a href=\"castle.php?do=main\">area</a>, or return to the <a href=\"castle.php?do=folk\">castle folk</a> or use the compass on the right to start exploring.<br />\n";  
    display($page, $title);
   

}

function king() { // King Arthur
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
                if ($userrow["quest5"] != "Complete") { 
			header("Location: index.php"); die(); }// Check to see if quest is completed
        $title = "King Arthur";
        $page = "<table width='100%' border='1'><tr><td class='title'>King Arthur</td></tr></table><p>";
        $page .= "I don't think its wise for you to speak to the King, he may realise you are not supposed to be here! Best to try again later.<p>\n";         
        $page .= "You may return to the Castle main <a href=\"castle.php?do=main\">area</a>, or return to the <a href=\"castle.php?do=folk\">castle folk</a> or use the compass on the right to start exploring.<br />\n";  
    
    display($page, $title);
   
}

function knight() { // Fight the Knight
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
    
    $damage = $userrow["level"]+3;
    $updatequery = doquery("UPDATE {{table}} SET currenthp=currenthp-$damage, templist='knight' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
                if ($userrow["quest5"] != "Complete") { 
			header("Location: index.php"); die(); }// Check to see if quest is completed
        $title = "A Knight";
        $page = "<table width='100%' border='1'><tr><td class='title'>A Knight</td></tr></table><p>";
        $page .= "A Knight: Hang on... I don't remember seeing you around here before! Show me your Guard Pass please.<br /><br />\n"; 
        $page .= "".$userrow["charname"].": Err.. No.<br /><br />\n"; 
        $page .= "A Knight: You must be hiding something from me to not want to show me your Pass.<br /><br />\n"; 
        $page .= "<i>The Knight approaches you and pushs you up against a wall, and begins searching you... You are hurt for $damage Damage!</i><br /><br />\n";
        $page .= "A Knight: Hmmm... You are not the person on this Pass! Get out of here this instance, or feel the wrath of my Sword!<br /><br />\n"; 

       $page .= "You may <a href=\"castle.php?do=attackknight\">attack the Knight</a>, or return to the <a href=\"castle.php?do=folk\">castle folk</a> or use the compass on the right to start exploring.<br />\n";  
    
           if ($userrow["currenthp"] <= "0" ) {
             $newgold = ceil($userrow["gold"]/2);
			$newhp = ceil($userrow["maxhp"]/4);
			$newdscales = ceil($userrow["dscales"]/3);
			$updatequery = doquery("UPDATE {{table}} SET dscales='$newdscales',currenthp='$newhp',location='Dead',currentaction='In Town',currentmonster='0',currentmonsterhp='0',currentmonstersleep='0',currentmonsterimmune='0',currentfight='0',latitude='0',longitude='0',gold='$newgold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
			$page = "<table width='100%'><tr><td class='title'>Attacking the Knight</td></tr></table>";
			$page .= "<p><font color=red>You have died from attacking the Knight.</font></b><br /><br />As a consequence, you've lost half of your gold and <b>some dragon scales</b>. However, you have been given back a portion of your hit points to continue your journey.<br /><br /><center><img src=\"images/died.gif\" border=\"0\" alt=\"You have Died\" /></a></center><p>You may now continue back to <a href=\"index.php\">town</a>, and we hope you fair better next time.";
			$townquery = doquery("SELECT * FROM {{table}} WHERE name='".$userrow["lasttown"]."' LIMIT 1", "towns");
			$townrow = mysql_fetch_array($townquery);
			$latitude=$townrow["latitude"];
			$longitude=$townrow["longitude"];
			$updatequery = doquery("UPDATE {{table}} SET latitude='$latitude',longitude='$longitude' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

    }
       
    display($page, $title);
   
}
// Do stats and database for monster, add victory to fight.php and ensure its cheat proof etc.
function attackknight() { // Fight Knight

    global $userrow, $numqueries;
                if ($userrow["quest5"] != "Complete") { 
			header("Location: index.php"); die(); }// Check to see if quest is completed
      if ($userrow["templist"] != "knight") { header("Location: index.php"); die(); }

setcookie ("tempquest", $userrow["knight"]);
$updatequery = doquery("UPDATE {{table}} SET currentaction='Fighting',currentfight='2',currentmonster='249',currentmonsterhp='640',currentmonsterimmune='2',location='Castle Knight' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

header("Location: index.php?do=fight"); die();


}    

//Second floor
function second() { // Second floor
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

$updatequery = doquery("UPDATE {{table}} SET location='Castle Second Floor' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
                if ($userrow["quest5"] != "Complete") { 
			header("Location: index.php"); die(); }// Check to see if quest is completed
        $title = "Castle Second Floor";
        $page = "<table width='100%' border='1'><tr><td class='title'>Castle Second Floor</td></tr></table><p>";
        $page .= "You slowly walk up the long and swirling stairs, to arrive at the second floor of the castle.<br /><br />\n";
        $page .= "You see the following areas to visit:<br />\n";
        $page .= "<ul><li /><a href=\"castle.php?do=main\">Go Downstairs</a></ul><br /><br />\n";        
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br />\n";      
    
    display($page, $title);
   

}
?>