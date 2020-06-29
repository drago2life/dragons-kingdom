<?php // market.php :: Market Page with all stalls etc.
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
if ($userrow["poll"] != "Voted") { header("Location: poll.php"); die(); }
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

##############JEWELLEY LIMITS (Make sure the same as on index.php)
$backpackjewelleryslots = 3;
$storagejewelleryslots = 10;

##############
if (isset($_GET["do"])) {
	$do = explode(":",$_GET["do"]);

	if ($do[0] == "gforum") { header("Location: gforum.php"); die();}
// Market areas.
    if ($do[0] == "main") { main($do[1]); }
    if ($do[0] == "pickaxe") { pickaxe($do[1]); }
    if ($do[0] == "pick1") { pick1($do[1]); }
    if ($do[0] == "pick2") { pick2($do[1]); }
    if ($do[0] == "pick3") { pick3($do[1]); }            
    if ($do[0] == "potions") { potions($do[1]); }
    if ($do[0] == "ogrespot") { ogrespot($do[1]); }
    if ($do[0] == "gobpot") { gobpot($do[1]); }
    if ($do[0] == "dragspot") { dragspot($do[1]); }
    elseif ($do[0] == "buyjewel") { buyjewel($do[1]); }
    elseif ($do[0] == "buyjewel2") { buyjewel2($do[1]); }
    elseif ($do[0] == "buyjewel3") { buyjewel3($do[1]); }
    elseif ($do[0] == "buyjewel4") { buyjewel4($do[1]); }
    if ($do[0] == "vial") { vial($do[1]); }
    if ($do[0] == "jar") { jar($do[1]); }
    if ($do[0] == "buystring") { buystring($do[1]); }
elseif ($do[0] == "items") { items($do[1]); }

} 

function main() { // Market Place
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        if (isset($_POST["submit"])) {
        
    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if(mysql_num_rows($townquery) != 1) { die("Cheat attempt sent to administrator."); }
    $townrow = mysql_fetch_array($townquery);
    
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {

$updatequery = doquery("UPDATE {{table}} SET location='Market Place' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        
        $title = "Market Place";
        $page = "<table width='100%' border='1'><tr><td class='title'>Market Place</td></tr></table><p>";
        $page .= "You walk upto a quiet Market, and look around. There appears to be only four Market Stalls at present, with very little items for sale. It looks a little deserted and you hope that it will become popular once again.<p>You see the following stalls you can visit:<br /><br />\n";
        $page .= "<ul><li /><a href=\"market.php?do=pickaxe\">Pickaxe Merchant</a><li /><a href=\"market.php?do=items\">Items Stall</a><li /><a href=\"market.php?do=potions\">Potions Stall</a><li /><a href=\"market.php?do=buyjewel\">Jewellers Stall</a></ul><p>You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br />\n";      

        
    }
    
    display($page, $title);
    
}

function buystring() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
      
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $string=$_POST['string'];          
        $string=strip_tags($string);
        $sellcost1=($string*50);

        if($string<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Buy String</td></tr></table><p>";
           $page .="You may not buy negative string. Stop trying to cheat!<p>";
           $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
       elseif($sellcost1 > $userstats3[gold])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Gold Nuggets</td></tr></table><p>";
          $page .="You don't have enough gold to buy that much String. You may return to <A href='index.php'>town</a> or continue exploring.";
       
              }
           else
        {
           $getarmy="update dk_users set gold=gold-'$sellcost1', string=string+'$string' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get string");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Buy String</td></tr></table><p>";
           $page .="You bought some string successfully. You may now go back to <A href='index.php'>town</a> or buy some <A href='market.php?do=buystring'>more</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   	
	   	$updatequery = doquery("UPDATE {{table}} SET location='Buying String' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
  
	   $title = "Buy String";
        $page = "<table width='100%' border='1'><tr><td class='title'>Buy String</td></tr></table><p>";
        $page .= "How many pieces of String do you wish to buy? They cost 50 gold each.\n";
        $page .= "<form action=\"market.php?do=buystring\" method=\"post\">\n";           
        $page .= "<br /><select name='string'><option value='1'>1 Piece (50 Gold)</option><option value='5'>5 Pieces (250 Gold)</option><option value='15'>15 Pieces (750 Gold)</option><option value='50'>50 Pieces (2500 Gold)</option></select> <input type=\"submit\" name=\"submit\" value=\"Buy String\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }
  

function pickaxe() { // Pickaxe Merchant
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);



$updatequery = doquery("UPDATE {{table}} SET location='Pickaxe Merchant' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        

        $title = "Pickaxe Merchant";
        $page = "<table width='100%' border='1'><tr><td class='title'>Pickaxe Merchant</td></tr></table><p>";
        $page .= "Hello and welcome to my collection of Pickaxes, I am the local Pickaxe Merchant.<p> Each pickaxe allows you to mine more Ore per swing of your Pickaxe. However you will need a high enough Mining Level to use these special Pickaxes.<p>What pickaxe are you interested in purchasing?<br />\n";
        $page .= "<br /><br /><ul><li /><a href=\"market.php?do=pick1\">Bronze Pickaxe</a> (Mine 2 Ores, 70,000 Gold, Mining Level: 15)<li /><a href=\"market.php?do=pick2\">Iron Pickaxe</a> (Mine 3 Ores, 365,000 Gold, Mining Level: 70)<li /><a href=\"market.php?do=pick3\">Steel Pickaxe</a> (Mine 4 Ores, 935,000 Gold, Mining Level: 130)</ul><p><p>You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br />\n";      

        {
    }
    
    display($page, $title);
    
}

function pick1() { // Bronze Pickaxe
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
    
    if ($userrow["gold"] < 70000) { display("You do not have enough gold to buy this Pickaxe.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass to the right to start exploring.", "Not enough Gold"); die(); }
    if ($userrow["mining"] < 15) { display("You do not have a high enough Mining level for this Pickaxe.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass to the right to start exploring.", "Not High Enough"); die(); }
    
    if (isset($_POST["submit"])) {
        
        $newgold = $userrow["gold"] - 70000;
        $query = doquery("UPDATE {{table}} SET gold='$newgold', pickaxe='Bronze Pickaxe', pickaxeid='2' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Pickaxe Merchant";
        $page = "<table width='100%' border='1'><tr><td class='title'>Pickaxe Merchant</td></tr></table><p>";
        $page .= "You pay for your Bronze Pickaxe and hand over your old Pickaxe to the Merchant. You can now mine 2 Ores at a time.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.";
        
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
        
        $title = "Pickaxe Merchant";
        $page = "<table width='100%' border='1'><tr><td class='title'>Pickaxe Merchant</td></tr></table><p>";
        $page .= "Are you sure you wish to buy the Bronze Pickaxe for 70,000 gold, which allows you to mine 2 Ores per swing?<br /><br />\n";
        $page .= "<form action=\"market.php?do=pick1\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Buy It\" />\n";
        $page .= "</form>\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br /><br />\n";
       
    }
    
    display($page, $title);
    
}

function pick2() { // Iron Pickaxe
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
    
    if ($userrow["gold"] < 365000) { display("You do not have enough gold to buy this Pickaxe.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass to the right to start exploring.", "Not enough Gold"); die(); }
     if ($userrow["mining"] < 70) { display("You do not have a high enough Mining level for this Pickaxe.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass to the right to start exploring.", "Not High Enough"); die(); }
     
    if (isset($_POST["submit"])) {
        
        $newgold = $userrow["gold"] - 365000;
        $query = doquery("UPDATE {{table}} SET gold='$newgold', pickaxe='Iron Pickaxe', pickaxeid='3' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Pickaxe Merchant";
        $page = "<table width='100%' border='1'><tr><td class='title'>Pickaxe Merchant</td></tr></table><p>";
        $page .= "You pay for your Iron Pickaxe and hand over your old Pickaxe to the Merchant. You can now mine 3 Ores at a time.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.";
        
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
        
        $title = "Pickaxe Merchant";
        $page = "<table width='100%' border='1'><tr><td class='title'>Pickaxe Merchant</td></tr></table><p>";
        $page .= "Are you sure you wish to buy the Iron Pickaxe for 365,000 gold, which allows you to mine 3 Ores per swing?<br /><br />\n";
        $page .= "<form action=\"market.php?do=pick2\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Buy It\" />\n";
        $page .= "</form>\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br /><br />\n";
       
    }
    
    display($page, $title);
    
}

function pick3() { // Steel Pickaxe
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
    
    if ($userrow["gold"] < 935000) { display("You do not have enough gold to buy this Pickaxe.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass to the right to start exploring.", "Not enough Gold"); die(); }
    if ($userrow["mining"] < 130) { display("You do not have a high enough Mining level for this Pickaxe.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass to the right to start exploring.", "Not High Enough"); die(); }
      
    if (isset($_POST["submit"])) {
        
        $newgold = $userrow["gold"] - 935000;
        $query = doquery("UPDATE {{table}} SET gold='$newgold', pickaxe='Steel Pickaxe', pickaxeid='4' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Pickaxe Merchant";
        $page = "<table width='100%' border='1'><tr><td class='title'>Pickaxe Merchant</td></tr></table><p>";
        $page .= "You pay for your Steel Pickaxe and hand over your old Pickaxe to the Merchant. You can now mine 4 Ores at a time.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.";
        
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
        
        $title = "Pickaxe Merchant";
        $page = "<table width='100%' border='1'><tr><td class='title'>Pickaxe Merchant</td></tr></table><p>";
        $page .= "Are you sure you wish to buy the Steel Pickaxe for 935,000 gold, which allows you to mine 4 Ores per swing?<br /><br />\n";
        $page .= "<form action=\"market.php?do=pick3\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Buy It\" />\n";
        $page .= "</form>\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br /><br />\n";
       
    }
    
    display($page, $title);
    
}

function potions() { // Potions Stall
    
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

$updatequery = doquery("UPDATE {{table}} SET location='Potions Stall' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        
        $title = "Potions Stall";
        $page = "<table width='100%' border='1'><tr><td class='title'>Potions Stall</td></tr></table><p>";
        $page .= "Hello and welcome to the Potions Stall.<p> I'm currently very low on stock, and don't have many Potions to choose from, but I hope to produce more soon.<br />\n";
        $page .= "<br /><br /><ul><li /><a href=\"market.php?do=ogrespot\">Ogres Potion</a><li /><a href=\"market.php?do=gobpot\">Goblins Potion</a><li /><a href=\"market.php?do=dragspot\">Dragons Potion</a></ul><p><p>You may also purchase some <a href=\"market.php?do=vial\">Potion Vials</a>, they will cost you 50 Gold each.<p>You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br />\n";      

        
    }
    
    display($page, $title);
    
}

function ogrespot() { // Ogres potion, raises str temp
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,ogreprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
    
    if ($userrow["gold"] < $townrow["ogreprice"]) { display("You do not have enough gold to buy an Ogres Potion.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass to the right to start exploring.", "Not enough Gold"); die(); }
    
    if (isset($_POST["submit"])) {
        
        $newgold = $userrow["gold"] - $townrow["ogreprice"];
        $query = doquery("UPDATE {{table}} SET gold='$newgold', potion='Ogres Potion' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Potions Stall - You drink your Ogres Potion";
        $page = "<table width='100%' border='1'><tr><td class='title'>Potions Stall - You drink your Ogres Potion</td></tr></table><p>";
        $page .= "You take a sip of your <b>Ogres Potion</b> and it tastes strange, it makes you go a little light headed and everything appears to be spinning.<p>After a minute or so, you feel a little better and are able to continue your journey.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.";
        
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
        
        $title = "Potions Stall - Buy an Ogres Potion";
        $page = "<table width='100%' border='1'><tr><td class='title'>Potions Stall - Buy an Ogres Potion</td></tr></table><p>";
        $page .= "So you want an <b>Ogres Potion</b>, young adventurer?<br /><br />\n";
        $page .= "It only costs <b>" . $townrow["ogreprice"] . " gold</b>. This unique Potion adds a 0-7% bonus to your current <b>Strength</b>.<p>I must warn you that the Potion you buy may be mixed wrongly, and you may end up with a weak potion, or a strong potion. This is due to my bad eye sight, and I sometimes tend to mix my potions inaccurately lately. Some of my relatives in other towns, may have more luck with accuracy for a stronger dosage, but they will charge you more gold.<p>Would you still like to buy one, knowing that the Potions dosage can vary?<br /><br />\n";
        $page .= "<form action=\"market.php?do=ogrespot\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes Please\" />\n";
        $page .= "</form>\n";
        $page .= "You may return to <a href=\"index.php\">town</a> or buy another Potion at the <a href=\"market.php?do=potions\">Potions Stall</a>, or use the compass on the right to start exploring.<br /><br />\n";
       
    }
    
    display($page, $title);
    
}

function gobpot() { // Goblins Potion, raises dex
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,gobprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
    
    if ($userrow["gold"] < $townrow["gobprice"]) { display("You do not have enough gold to buy a Goblins Potion.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass to the right to start exploring.", "Not enough Gold"); die(); }
    
    if (isset($_POST["submit"])) {
        
        $newgold = $userrow["gold"] - $townrow["gobprice"];
        $query = doquery("UPDATE {{table}} SET gold='$newgold', potion='Goblins Potion' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Potions Stall - You drink your Goblins Potion";
        $page = "<table width='100%' border='1'><tr><td class='title'>Potions Stall - You drink your Goblins Potion</td></tr></table><p>";
        $page .= "You take a sip of your <b>Goblins Potion</b> and it tastes strange, it makes you go a little light headed and everything appears to be spinning.<p>After a minute or so, you feel a little better and are able to continue your journey.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.";
        
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
        
        $title = "Potions Stall - Buy a Goblins Potion";
        $page = "<table width='100%' border='1'><tr><td class='title'>Potions Stall - Buy a Goblins Potion</td></tr></table><p>";
        $page .= "So you want a <b>Goblins Potion</b>, young adventurer?<br /><br />\n";
        $page .= "It only costs <b>" . $townrow["gobprice"] . " gold</b>. This unique Potion adds a 0-7% bonus to your current <b>Dexterity</b>.<p>I must warn you that the Potion you buy may be mixed wrongly, and you may end up with a weak potion, or a strong potion. This is due to my bad eye sight, and I sometimes tend to mix my potions inaccurately lately. Some of my relatives in other towns, may have more luck with accuracy for a stronger dosage, but they will charge you more gold.<p>Would you still like to buy one, knowing that the Potions dosage can vary?<br /><br />\n";
        $page .= "<form action=\"market.php?do=gobpot\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes Please\" />\n";
        $page .= "</form>\n";
        $page .= "You may return to <a href=\"index.php\">town</a> or buy another Potion at the <a href=\"market.php?do=potions\">Potions Stall</a>, or use the compass on the right to start exploring.<br /><br />\n";
       
    }
    
    display($page, $title);
    
}

function dragspot() { // Dragons Potion, reduces chance to of pre-emp hits
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,dragpotprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
    
    if ($userrow["gold"] < $townrow["dragpotprice"]) { display("You do not have enough gold to buy a Dragons Potion.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass to the right to start exploring.", "Not enough Gold"); die(); }
    
    if (isset($_POST["submit"])) {
        
        $newgold = $userrow["gold"] - $townrow["dragpotprice"];
        $query = doquery("UPDATE {{table}} SET gold='$newgold', potion='Dragons Potion' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Potions Stall - You drink your Dragons Potion";
        $page = "<table width='100%' border='1'><tr><td class='title'>Potions Stall - You drink your Dragons Potion</td></tr></table><p>";
        $page .= "You take a sip of your <b>Dragons Potion</b> and it tastes strange, it makes you go a little light headed and everything appears to be spinning.<p>After a minute or so, you feel a little better and are able to continue your journey.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.";
        
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
        
        $title = "Potions Stall - Buy a Dragons Potion";
        $page = "<table width='100%' border='1'><tr><td class='title'>Potions Stall - Buy a Dragons Potion</td></tr></table><p>";
        $page .= "So you want a <b>Dragons Potion</b>, young adventurer?<br /><br />\n";
        $page .= "It only costs <b>" . $townrow["dragpotprice"] . " gold</b>. This unique Potion adds a bonus of anything from 0-5% more <b>chance to avoid Pre-emp hits</b>.<p>I must warn you that the Potion you buy may be mixed wrongly, and you may end up with a weak potion, or a strong potion. This is due to my bad eye sight, and I sometimes tend to mix my potions inaccurately lately. Some of my relatives in other towns, may have more luck with accuracy for a stronger dosage, but they will charge you more gold.<p>Would you still like to buy one, knowing that the Potions dosage can vary?<br /><br />\n";
        $page .= "<form action=\"market.php?do=dragspot\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes Please\" />\n";
        $page .= "</form>\n";
        $page .= "You may return to <a href=\"index.php\">town</a> or buy another Potion at the <a href=\"market.php?do=potions\">Potions Stall</a>, or use the compass on the right to start exploring.<br /><br />\n";
       
    }
    
    display($page, $title);
    
}

function buyjewel() { // Displays a list of available items for purchase.

    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,itemslist FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
    
    $townquery = doquery("SELECT name,jewellerylist FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    $townrow = mysql_fetch_array($townquery);

    $itemslist = explode(",",$townrow["jewellerylist"]);
    $querystring = "";
    foreach($itemslist as $a=>$b) {
        $querystring .= "id='$b' OR ";
    }
    $querystring = rtrim($querystring, " OR ");

    $itemsquery = doquery("SELECT * FROM {{table}} WHERE $querystring ORDER BY id", "jewellery");
        $page = "<table width='100%' border='1'><tr><td class='title'>Jewellery Stall</td></tr></table><p>";
    $page .= "Buying Rings and Amulets add points to your Magic Find which greatly increases your chance of finding monster drops. It will also increase the amount of gold, dragon scales and luck in certain areas while exploring, such as treasure chests, souls, monster drops and other such areas. Each item may require you to be of a specific level, to be able to equip them.<br /><br />What would you like to buy? You may sell me your <a href=\"index.php?do=backpack\">crafted jewellery</a>, <a href=\"sell.php?do=sellnuggets\">any gold nuggets</a> or <a href=\"sell.php?do=sellgems\">gems</a> which you have. You can also buy <a href=\"market.php?do=buystring\">some string</a>. I will use these to craft my own Jewellery to sell.<p>\n";
    
        if($userrow["tempquest"] == "nojar" && $userrow["longitude"] == "-296" && $userrow["latitude"] == "-367") { //Quest 4 has been started and needs to get Jar         
        $page .= "You may also purchase some <a href=\"market.php?do=jar\">Crystal Jars</a>, they will cost you 2150 Gold each.\n";
        }
    
    $page .= "<p>The following items are available at this town:<p><table width=\"99%\">\n";
    while ($itemsrow = mysql_fetch_array($itemsquery)) {
        if ($itemsrow["type"] == 1 || $itemsrow["type"] == 2) { $attrib = "Magic Find:"; } else  { $attrib = "Magic Find:"; }
        $page .= "<tr><td width=\"4%\">";
        if ($itemsrow["type"] == 1) { $page .= "<img src=\"images/icon_ring.gif\" alt=\"ring\" /></td>"; }
        if ($itemsrow["type"] == 2) { $page .= "<img src=\"images/icon_amulet.gif\" alt=\"amulet\" /></td>"; }
          

	if ($itemsrow["requirement"] > $userrow["level"]) {
		$page .= "<td width=\"25%\"><span class=\"light\">".$itemsrow["name"]."</span></td><td width=\"25%\"><span class=\"light\">$attrib ".$itemsrow["attribute"]."</span></td><td width=\"25%\"><span class=\"light\">Requirement: Level ".$itemsrow["requirement"]."</span></td><td width=\"40%\"><span class=\"light\">Can't Equip</span></td></tr>\n";
	}
	else {
            if ($itemsrow["special"] != "X") { $specialdot = "<span class=\"highlight\">&#42;</span>"; } else { $specialdot = ""; }
            $page .= "<td width=\"25%\"><b><a href=\"market.php?do=buyjewel2:".$itemsrow["id"]."\">".$itemsrow["name"]."</a>$specialdot</b></td><td width=\"25%\">$attrib <b>".$itemsrow["attribute"]."</b></td><td width=\"25%\">Requirement: <b>Level ".$itemsrow["requirement"]."</b></td><td width=\"40%\">Price: <b>".$itemsrow["buycost"]." gold</b></td></tr>\n";
	}
    }
    	
    
    $page .= "</table><br />\n";
    $page .= "<p>If you've changed your mind, you may also return back to <a href=\"index.php\">town</a>.\n";
    $title = "Jewellery Stall";

$updatequery = doquery("UPDATE {{table}} SET location='Jewellery Stall' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
 

    display($page, $title);

}

function buyjewel2($id) { // Confirm user's intent to purchase item.
	global $userrow, $numqueries, $backpackjewelleryslots;

	$townquery = doquery("SELECT name,jewellerylist FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
	if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
	$townrow = mysql_fetch_array($townquery);
	$townitems = explode(",",$townrow["jewellerylist"]);
	if (! in_array($id, $townitems)) { display("Cheat attempt sent to administrator.", "Error"); }

	$itemsquery = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "jewellery");
	$itemsrow = mysql_fetch_array($itemsquery);

	if ($userrow["gold"] < $itemsrow["buycost"]) { display("You do not have enough gold to buy this item.<br /><br />You may return to <a href=\"index.php\">town</a>, <a href=\"market.php?do=buyjewel\">store</a>, or use the direction compass on the right to start exploring.", "Jewellery Stall"); die(); }
	if ($userrow["level"] < $itemsrow["requirement"]) { display("You do not meet the level requirement to buy this item.<br /><br />You may return to <a href=\"index.php\">town</a>, <a href=\"market.php?do=buyjewel\">store</a>, or use the direction compass on the right to start exploring.", "Jewellery Stall"); die(); }

	$bpquery = doquery("SELECT * FROM {{table}} WHERE playerid = '$userrow[id]' AND itemtype = '3' AND location = '1'", "itemstorage");
	$slotsr = $backpackjewelleryslots - mysql_num_rows($bpquery);
	
	if ($slotsr < 1)	{
		display("You do not have the room in your backpack to store this item.  Please go to your <a href='index.php?do=backpack'>backpack</a> to clear out some room and then return to me.", "Jewellery Stall");
	}
	
	if ($itemsrow['type'] == 1)	{
		$what = "Ring";
	}
	else {
		$what = "Amulet";
	}
	
	$page = "<table width='100%' border='1'><tr><td class='title'>Jewellery Stall - Buy $what</td></tr></table><p>";
	$page .= "You have chosen to buy a $itemsrow[name] for <b>$itemsrow[buycost]</b> gold. Are you sure you wish to buy this item?<br /><br /><form action=\"market.php?do=buyjewel3:$id\" method=\"post\"><input type=\"submit\" name=\"submit\" value=\"Yes\" /> <input type=\"submit\" name=\"cancel\" value=\"No\" /></form>";

	$title = "Jewellery Stall";
	display($page, $title);
}

function buyjewel3($id) { // Update user profile with new item & stats.
	global $userrow;

	if (isset($_POST["cancel"]))	{
		header("Location: index.php");
		exit;
	}
	
	//Check Cheats
	$townquery = doquery("SELECT name,jewellerylist FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
	if (mysql_num_rows($townquery) != 1)	{
		display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); 
		exit;
	}
	$townrow = mysql_fetch_array($townquery);
	$townitems = explode(",",$townrow["jewellerylist"]);
	if (! in_array($id, $townitems))	{
		display("Cheat attempt sent to administrator.", "Error"); 
		exit;
	}
	
	require('storage.php');
	
	//Pay for the item
	$result = doquery("SELECT buycost FROM {{table}} WHERE id = $id", "jewellery");
	$ma = mysql_fetch_array($result);
	
	//Cheat catcher
	if ($userrow["gold"] < $ma["buycost"])	{
		display("Cheat attempt sent to administrator.", "Error"); 
		exit;
	}

	doquery("UPDATE {{table}} SET gold = gold - $ma[buycost] WHERE id = '$userrow[id]'", "users");
  
	//Add the item to storage
	additem($id, 3, 1);
	
	//Select the storageid.
	$query = "SELECT * FROM {{table}}
		  WHERE playerid = '$userrow[id]'
		  AND itemid = '$id' ";
	$result = doquery($query, "itemstorage");
	
	$ma = mysql_fetch_array($result);
	$isid = $ma['isid'];
    
    
	display("Thank you for purchasing this item.  You may now either <a href='market.php?do=buyjewel4:$isid&amp;equip=1&amp;where=1'>equip</a> the item, or put it in your <a href='market.php?do=buyjewel4:$isid'>backpack</a>.", "Jewellery Stall");

}
function buyjewel4($id) {
	if ($_GET[equip] == 1)	{
		require('storage.php');
		equipstoreditem($id, 3, 0);
		$page = "The item has been equiped.  If you already had an item equiped it has been placed in your <a href='index.php?do=backpack'>backpack</a>.";
	}
	else {
		$page = "You put the item in your <a href='index.php?do=backpack'>backpack</a>.";
	}
	
	display($page. "<br /><br />You may return to <a href=\"index.php\">town</a>, <a href=\"market.php?do=buyjewel\">store</a>, or use the direction compass on the right to start exploring.", "Jewellery Stall");
}

function items ($id) {
    global $userrow, $numqueries;
$updatequery = doquery("UPDATE {{table}} SET location='Items Stall' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

$userrow["location"] = $townrow["name"];

if (isset($_POST["submit"])) {
	 
	
	 if ($userrow["gold"] < $_POST["cost"]) {
              $page = "<table width=\"100%\"><tr><td class=\"title\">Items Stall</td></tr></table>"; 
        $page .= "You do not have enough gold to purchase a ".$_POST["name"].". It requires ".$_POST["cost"]." gold.<br /><br />You may return to <a href=\"index.php\">town</a>, <a href=\"market.php?do=items\">continue buying </a>,or continue exploring.";
        display($page, "Items Stall");
}
        $newgold = $userrow["gold"] - $_POST["cost"];
        $newinventitems = $userrow["inventitems"].",".$_POST["inventitemsid"];
        $newinventitems = str_replace(",0,", ",", $newinventitems);
        $tempinventitems = explode(",",$newinventitems);
        rsort($tempinventitems);
	  $tempinventitems = array_slice($tempinventitems, 0, 50);
        $newinventitems = "0,".join(",",$tempinventitems);
        $title = "Items Stall";
    		$page = "<table width=\"100%\"><tr><td class=\"title\">Items Stall</td></tr></table>";
		$page .= "You have purchased a ".$_POST["name"].".<br /><br />You may return to <a href=\"index.php\">town</a>, <a href=\"market.php?do=items\">continue buying </a>,or continue exploring.";
        $query = doquery("UPDATE {{table}} SET gold='$newgold',inventitems='$newinventitems' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

    } else {
	$userinventitems = explode(",",$userrow["inventitems"]);
    	$page = "<table width=\"100%\"><tr><td class=\"title\">Items Stall</td></tr></table>";
	$page .= "<p>Welcome to the Items Stall!  Here you can buy items, which can do various different things to help you in your quest to Dragons Kingdom.<p>Some items will only be able to be used once, and some will be permanent items. However, there are a few which are able to be used several times. But sometimes they are not, it is all based on a percentage, sometimes you are lucky, sometimes you are not.<p>The usage percentage beside each Item is the percentage chance of it being able to used again. For example, if it says 50, it means it has a 50% chance to be used again, and this will continue until the item is completely used up.<p>As for the strength beside items, this is the actual strength of the item. For example, if it says 10 on a healing item, it will heal you 10 HP.<p>You may only hold 50 items, if you purchase more, it will replace one of your current items and take some gold off you.<p>Here are the following items which are available to purchase, you may find different items at different towns:<p>";
	if ($userinventitems[50] == 0){
	$page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"10\" style=\"background-color:#dddddd;\">";
	$page .= "<center><b>Items Stall</b></center></td></tr>";
	$page .= "<tr><center><th width=\"25%\" style=\"background-color:#dddddd;\">Item Name</th><th width=\"5%\" style=\"background-color:#dddddd;\">Strength</th><th width=\"5%\" style=\"background-color:#dddddd;\">Percentage Usage (%)</th><th width=\"10%\" style=\"background-color:#dddddd;\">Price</th><th width=\"25%\" style=\"background-color:#dddddd;\">Description</th><th width=\"5%\" style=\"background-color:#dddddd;\">Buy</th></center></tr>";
	$count = 1;
    	$townquery = doquery("SELECT * FROM {{table}} WHERE name='".$userrow["location"]."' LIMIT 1", "towns");
    	$townrow = mysql_fetch_array($townquery);
    	$querystring = $townrow["inventitemslist"];
      $inventitemsquery = doquery("SELECT * FROM {{table}} WHERE id IN ($querystring) ORDER BY type", "inventitems");
	while ($inventitemsrow = mysql_fetch_array($inventitemsquery)) {
       if ($count == 1) { $color = "bgcolor=\"#ffffff\""; $count = 2; } else { $color = "bgcolor=\"#eeeeee\""; $count = 1; }
	   $page .= "<tr><td ".$color." width=\"25%\">".$inventitemsrow["name"]."</td>";
	   $page .= "<td ".$color." width=\"5%\"><center>".$inventitemsrow["strength"]."</center></td>";
	   $page .= "<td ".$color." width=\"5%\"><center>".$inventitemsrow["charges"]."</center></td>";
	   $page .= "<td ".$color." width=\"10%\"><center>".$inventitemsrow["buycost"]."</center></td>";
	   $page .= "<td ".$color." width=\"25%\">".$inventitemsrow["description"]."</td>";
	   $page .= "<td ".$color." width=\"5%\"><center></center>";
	   $itemid = $inventitemsrow["id"];

	    if ($userrow["gold"] >= $inventitemsrow["buycost"]) {
	     	$page .= "<form action=\"market.php?do=items\" method=\"post\">\n";
		$page .= "<input type=\"hidden\" name=\"inventitemsid\" value='".$inventitemsrow["id"]."' />";
		$page .= "<input type=\"hidden\" name=\"cost\" value='".$inventitemsrow["buycost"]."' />";
	     	$page .= "<input type=\"hidden\" name=\"name\" value='".$inventitemsrow["name"]."' />";
	     	$page .= "<input type=\"submit\" name=\"submit\" value=\"Buy It\" />";
	   }
	  $page .= "</td></form></tr>\n";
	}
	$page .= "</table></table>";

	} else {     //inventory full
		$page .= "<p> <b>You have no more available slots in your Items Inventory. You may only hold 50 Items at a time.</b><p>";
	}
	$page .= "<br>Return to <a href=\"index.php\">town</a>, or leave and continue exploring</a>.";
	}
	   	display($page, "Items Stall");

}

function vial() { // Buy vials. Mainly for quest 2 atm
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
    if ($userrow["gold"] < 50){ display("You do not have enough gold to buy any Vials.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the direction buttons on the right to start exploring.", "Not enough Gold"); die(); }

$updatequery = doquery("UPDATE {{table}} SET location='Buy Vials' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
  if($userrow["tempquest"] == "1") { //Quest 2 has been started and need to get vial for lucas          
        $title = "Buy Vials";
        $page = "<table width='100%' border='1'><tr><td class='title'>Buy Vials</td></tr></table><p>";
        $page .= "You purchase one Potion Vial for 50 Gold. It looks like you bought my last Vial. Oh well, I will have to make some more.<br /><br />\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br />\n";      
        $inventitemsquery = doquery("SELECT id FROM {{table}}","inventitems");
        $userinventitems = explode(",",$userrow["inventitems"]);
        //add item
        array_push($userinventitems, 90);
        $new_userinventitems = implode(",",$userinventitems);
        $userid = $userrow["id"];
        doquery("UPDATE {{table}} SET gold=gold-50,inventitems='$new_userinventitems',tempquest='vial' WHERE id='$userid' LIMIT 1", "users");
        
      }  
else {

        $page .= "<table width='100%' border='1'><tr><td class='title'>Buy Vials</td></tr></table><p>";
        $page .= "Im sorry but I don't have anymore Vials for sale. Please check back later when I may have some more.<br /><br />\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br /><br />\n";
   }   
    
    display($page, $title);
    
}

function jar() { // Buy Crystal Jar. Mainly for quest 4 atm
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
    if ($userrow["gold"] < 2150){ display("You do not have enough gold to Buy a Crystal Jar. They cost 2150 Gold each.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the direction buttons on the right to start exploring.", "Not enough Gold"); die(); }

$updatequery = doquery("UPDATE {{table}} SET location='Buy a Crystal Jar' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
  if($userrow["tempquest"] == "nojar" && $userrow["longitude"] == "-296" && $userrow["latitude"] == "-367") { //Quest 4 has been started and needs to get Jar         
        $title = "Buy a Crystal Jar";
        $page = "<table width='100%' border='1'><tr><td class='title'>Buy a Crystal Jar</td></tr></table><p>";
        $page .= "You want a Crystal Jar aye? Wait here while I go see if I have any left.<p><i>The jeweller walks off, and takes a while before he returns with something in his hand. You purchase one Crystal Jar for 2150 Gold.</i> <p>It looks like you bought my last Jar. Not many people need to buy these anylonger, so you are lucky that I had one lying around.<br /><br />\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br />\n";      
        $inventitemsquery = doquery("SELECT id FROM {{table}}","inventitems");
        $userinventitems = explode(",",$userrow["inventitems"]);
        //add item
        array_push($userinventitems, 95);
        $new_userinventitems = implode(",",$userinventitems);
        $userid = $userrow["id"];
        doquery("UPDATE {{table}} SET gold=gold-2150,inventitems='$new_userinventitems',tempquest='gotjar' WHERE id='$userid' LIMIT 1", "users");
        
      }  
else {

          { header("Location: index.php"); die(); }
}   
    
    display($page, $title);
    
}

?>