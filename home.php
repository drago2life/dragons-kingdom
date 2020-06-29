<?php // home.php :: Guild Courtyard

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

	$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "homes");
	if (mysql_num_rows($castlequery) <= 0) { header("Location: index.php?do=move:0"); die(); }

$updatequery = doquery("UPDATE {{table}} SET location='At Home' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
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

	if ($do[0] == "gforum") { header("Location: gforum.php"); die();}
	elseif ($do[0] == "bed") { bed(); }
	elseif ($do[0] == "bank") { bank(); }
    elseif ($do[0] == "townportal") { dotownback(); }
	elseif ($do[0] == "hire") { dohire(); }   
    elseif ($do[0] == "homestatus") { dosstatus(); } 
	elseif ($do[0] == "defense") { dodefense(); }    
	elseif ($do[0] == "move") { move(); }  
		elseif ($do[0] == "move2") { move2(); }  
		
} else { donothing(); }


function donothing() {

	global $userrow;
	$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' AND charname='".$userrow["charname"]."' LIMIT 1", "homes");
	if (mysql_num_rows($castlequery) <= 0) { header("Location: index.php?do=move:0"); die(); }
  		$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "homes");
	$castlerow = mysql_fetch_array($castlequery);
	
	$guards = $castlerow["guards"];
	
	$page = "<table width=\"100%\"><tr><td class=\"title\">Home</td></tr></table>";

$page .= <<<END
<p>You enter into your home, going past all <b>$guards Guards</b> that you have guarding your house.<p>
<p>Note: This feature is under construction and will contain many other areas soon.<p>
<table>
<tr><td width="173">
<ul><b><u>House Menu</u></b>
<li /><a href="home.php?do=bed">Rest in your Bed</a>
<li /><a href="index.php?do=storage">Storage Box</a>
<li /><a href="home.php?do=bank">Personal Bank</a>
<li /><a href="home.php?do=homestatus">Repair Home</a><br>
<li /><a href="home.php?do=defense">Upgrade Defenses</a><br>
<li /><a href="home.php?do=hire">Hire Guards</a>
<li /><a href="home.php?do=move">Move House</a>
<li /><a href="home.php?do=townportal">Town Portal</a>


</ul></td></tr></table>
END;
    display($page,"Home");

}

function bed() { // Staying at the inn resets all expendable stats to their max values.

    global $userrow, $numqueries;

    if (isset($_POST["submit"])) {

        $query = doquery("UPDATE {{table}} SET drink='Empty', potion='Empty', currenthp='".$userrow["maxhp"]."',currentmp='".$userrow["maxmp"]."',currenttp='".$userrow["maxtp"]."' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Bed - Refreshed";
        $page = "<table width='100%' border='1'><tr><td class='title'>Bed - Refreshed</td></tr></table><p>";
        $page .= "You feel refreshed and ready for another day after a quick sleep, with all current stats to maximum capacity.<br /><br />You may return to your <a href=\"home.php\">Home</a>, or use the compass on the right to start exploring.";

    } elseif (isset($_POST["cancel"])) {

        header("Location: home.php"); die();

    } else {

$updatequery = doquery("UPDATE {{table}} SET location='Resting in Bed' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
 

        $title = "Bed";
        $page = "<table width='100%' border='1'><tr><td class='title'>Bed</td></tr></table><p>";
        $page .= "Resting in your bed will refill your current HP, MP, and TP to their maximum levels. It will also remove your current Tavern Drink and Potion if you have recently purchased one. It will not cost you anything.<br /><br />\n";
        $page .= "<form action=\"home.php?do=bed\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Lay-down\" /> <input type=\"submit\" name=\"cancel\" value=\"Leave\" />\n";
        $page .= "</form>\n";

    }

    display($page, $title);

}

function bank() { // Bank Function

    global $userrow, $numqueries;

$updatequery = doquery("UPDATE {{table}} SET location='Personal Bank' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
 

        $title = "Personal Bank";
        $page = "<table width='100%' border='1'><tr><td class='title'>Personal Bank</td></tr></table><p>";
        $page .= "<br><b><u>Deposit and Withdraw Gold</u>:</b><p>Depositing all your gold will ensure you don't lose it while exploring the battle field and from duels.<br /><br />\n";
        $page .= "You have <b>" . number_format($userrow["bank"])
 . "</b> gold in the bank.</b><br /><br />\n";
        $page .= "<form action=\"home.php?do=bank\" method=\"post\">\n";


        $page .= "<input type=\"submit\" name=\"submit\" value=\"Deposit\" /> <input type=\"submit\" name=\"cancel\" value=\"Withdraw\" /><br /><br /><b><u>Trade Gold to other Players</u>:</b><p>Enter the ID number of the player you want to transfer gold to. You will be charged a <b>15% tax</b> for trading gold to another player. Gold <b>must</b> be stored in your bank to be able to transfer it and it then appears in their bank account.<br /><br /><b>ID Number</b>:<br><input type=\"text\" name=\"id\" value=\"0\" /> E.g. 1001<br /><b>Enter the amount you wish to Transfer</b>:<br /><input type=\"text\" name=\"amount\" value=\"0\" /> E.g. 10000<br /><input type=\"submit\" name=\"submit2\" value=\"Transfer Gold\" /><p><p>If you've changed your mind, you may also return back to your <a href=\"home.php\">Home</a>.\n";
        $page .= "</form>\n";

 if (isset($_POST["submit"])) {


        $newgold = $userrow["gold"] - $userrow["gold"];
        $newbank = $userrow["bank"] + $userrow["gold"];
        $query = doquery("UPDATE {{table}} SET gold='$newgold',bank='$newbank',currenthp='".$userrow["currenthp"]."',currentmp='".$userrow["currentmp"]."',currenttp='".$userrow["currenttp"]."' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Personal Bank - Deposited Gold";
        $page = "You have deposited your gold successfully!<br /><br />You may return to your <a href=\"home.php\">Home</a>, or use the compass image on the right to start exploring.";
}

elseif (isset($_POST["cancel"])) {



        $newgold = $userrow["gold"] + $userrow["bank"];
        $newbank = $userrow["bank"] - $userrow["bank"];
        $query = doquery("UPDATE {{table}} SET gold='$newgold',bank='$newbank',currenthp='".$userrow["currenthp"]."',currentmp='".$userrow["currentmp"]."',currenttp='".$userrow["currenttp"]."' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Personal Bank - Withdrawn Gold";
        $page = "You have withdrawn your gold successfully!<br /><br />You may return to your <a href=\"home.php\">Home</a>, or use the compass image on the right to start exploring.";


    }

if(isset($_POST['submit2']))
  {

    $yourstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
    $yourstats2=mysql_query($yourstats) or die("Could not get your stats");
    $yourstats3=mysql_fetch_array($yourstats2);
    $id=$_POST['id'];
    $id=strip_tags($id);
    $oppstats="SELECT * from dk_users where id='$id'";
    $oppstats2=mysql_query($oppstats) or die("Could not get player's stats");
    $oppstats3=mysql_fetch_array($oppstats2);

    if($yourstats3["id"]==$oppstats3["id"])
    {
       $page = "Giving yourself gold doesn't exactly help yourself.<br /><br />You may return to your <a href=\"home.php\">Home</a>, or use the compass images on the right to start exploring.";

    }
else {
$postamount = $_POST['amount'];
if($postamount > $yourstats3["bank"]) {
$page = "You don't have that much gold in your bank to trade.<br /><br />You may return to your <a href=\"home.php\">Home</a>, or use the compass images on the right to start exploring.";
}
elseif($postamount < 1) {
$page = "You can't send less than 1 gold.<br /><br />You may return to your <a href=\"home.php\">Home</a>, or use the compass images on the right to start exploring.";
}
elseif($userrow["level"] < 15) {
$page = "You must be level 15 or above before you can trade gold.<br /><br />You may return to your <a href=\"home.php\">Home</a>, or use the compass images on the right to start exploring.";
}
else {
$taxamount = number_format($postamount * .15);
$postamount2 = $postamount - $taxamount;


$newoppbank = $oppstats3["bank"] + $postamount2;

  $yournewbank = $yourstats3["bank"] - $postamount;
 $updateyourstats="update dk_users set bank='$yournewbank' where charname='".$userrow["charname"]."'";
        mysql_query($updateyourstats) or die("Could not update your stats");
        $updateopp="update dk_users set bank='$newoppbank' where id='$id'";
        mysql_query($updateopp) or die(mysql_error());
        $page = "You have transfered your gold successfully from your bank account to their bank account.<br />Although you incurred <b>$taxamount</b> gold as tax!<br /><p>You may return to your <a href=\"home.php\">Home</a>, or use the compass images on the right to start exploring.";


}

}}
    display($page, $title);

}

function dotownback() {
	global $userrow;

	if (isset($_POST["totown"])) {

	$pu = doquery("UPDATE {{table}} SET currentaction='In Town', latitude='0', longitude='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	header("Location: home.php"); die();
	}

	$page = "<table width='100%'><tr><td class='title'>Town Portal</td></tr></table>";
	$page .= "<p>You stand before a small portal which leads back to the Kingdom of Valour for free.<p> ";
	$page .= "<form action='home.php?do=townportal' method='POST'>";
	$page .= "<input type='submit' name='totown' value='Enter Portal'>";
	$page .= "</form><p>";
	$page .= "You may return to your <a href='home.php'>Home</a>, ";
	$page .= "or leave and <a href='index.php?do=move:0'>continue exploring</a>.";
	$pquery = doquery("UPDATE {{table}} SET location='Town Portal' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	display($page, "Town Portal");
}

function dohire() {
	global $userrow;

$updatequery = doquery("UPDATE {{table}} SET location='Hiring Guards' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "homes");
	$castlerow = mysql_fetch_array($castlequery);
	if (mysql_num_rows($castlequery) <= 0) {header("Location: index.php?do=move:0"); die();}
	$page = "<table width='100%'><tr><td class='title'>Hiring Guards</td></tr></table><p>";
	if (isset($_POST["submit"])) {
		$numtroops = abs(intval($_POST["troops"]));
		$trooptype = $_POST["trooptype"];
		if ($userrow["dscales"] < $numtroops*5) {
	   $page .= "<p>You don't have enough Dragon Scales to purchase anymore Guards.<br>";
		$page .= "<p>Your stronghold currently has the following units currently available:<br>";
	    $page .= "<b>".$castlerow["guards"]. "</b> Guards.<br>";
	$page .= "Guards cost 5 Dragon Scales each. How many would you like to hire?<br>";
	$page .= "<form action='home.php?do=hire' method='POST'>";
	$page .= "Number of Guards: ";
		$page .= "<input type='text' name='troops' value='0' size='5'> ";
		$page .= "<select name='trooptype'>";
	    $page .= "<option value='guards'>Guards</option></select><br>";
		$page .= "<input type='submit' name='submit' value='Hire Guards'>";
		$page .= "<center><br><a href='home.php'>Return to Home</a></center>";
 		display($page,"Hiring Guards");
 		}
		$castlerow["$trooptype"] = $castlerow["$trooptype"] + $numtroops;
		$newguards = $castlerow["guards"];
		$newdscales = $userrow["dscales"] - $numtroops *5;
		$page .= "You hired $numtroops Guards.<br>";
                $updatequery = doquery("UPDATE {{table}} SET dscales='$newdscales' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
		$t = doquery("UPDATE {{table}} SET guards='$newguards' WHERE charname='".$castlerow["charname"]."' ", "homes");
		}
	$page .= "<p>Your house currently has the following amount of guards available:<br>";
	$page .= "<b>".$castlerow["guards"]. "</b> Guards.<br>";
	$page .= "Guards cost 5 Dragon Scales each. How many would you like to hire?<br>";
	$page .= "<form action='home.php?do=hire' method='POST'>";
	$page .= "Number of Guards: ";
	$page .= "<input type='text' name='troops' value='0' size='5'> ";
	$page .= "<select name='trooptype'>";
	$page .= "<option value='guards'>Guards</option></select><br>";
	$page .= "<input type='submit' name='submit' value='Hire Guards'>";
	$page .= "<center><br><a href='home.php'>Return to Home</a></center>";
	display($page,"Hiring Guards");
}

function dosstatus() {
	global $userrow;

$updatequery = doquery("UPDATE {{table}} SET location='Repairing Home' WHERE id='".$userrow["id"]."' LIMIT 1", "users");


	$tquery = doquery("SELECT id FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "homes");
	$trow = mysql_fetch_array($tquery);
	$currentcastle = $trow["id"];
	$castlequery = doquery("SELECT * FROM {{table}} WHERE id='$currentcastle' LIMIT 1", "homes");
	$castlerow = mysql_fetch_array($castlequery);
	if (isset($_POST["submit"])) {
		$repaircost = $_POST["repaircost"];
		$castleid = $_POST["castleid"];
		$ruined = $_POST["ruined"];
		$castlegold = $_POST["castlegold"];
		$fullrepair = $_POST["fullrepair"];
		$page = "<table width='100%'><tr><td class='title'>Repair Home</td></tr></table>";
		
		if (intval($repaircost) < 0) {
		$page .= "<b>You can't negatively repair your home.<p>";
		$page .= "<center><br><a href='home.php'>Back to your Home</a></center>";
	    display($page,"Repair Home");}

		if (intval($repaircost) > $castlegold) {
		$page .= "<b>You do not have ".$repaircost." gold available in your bank.<p>";
		$page .= "<center><br><a href='home.php'>Back to your Home</a></center>";
	    display($page,"Repair Home");}

	    if (intval($repaircost) > $fullrepair) {$repaircost = $fullrepair;}
	    $newhp = $castlerow["currenthp"] + $repaircost;
	    $newgold = $userrow["bank"] - $repaircost;
	   	if ($ruined != 0) {$repaircost = 8500;
	    $newhp = 1;
	    $newgold = $userrow["bank"] - $repaircost;
	    }
	    $updatequery = doquery("UPDATE {{table}} SET bank='$newgold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	    $query = doquery("UPDATE {{table}} SET currenthp='$newhp',ruined='0' WHERE id='$castleid' LIMIT 1", "homes");
	    	$page .= "You have repaired ".$repaircost." worth of damage to the Home.<br>";
		$page .= "Your bank currently has ".$newgold." gold available.<br>";
		$page .= "Your house currently has <b>".$newhp."</b> structural points left out ";
		$page .= "of <b>".$castlerow["maxhp"]."</b> total.<br>";
		$page .= "The current Armor rating is: <b>".$castlerow["armor"]."</b> out of <b>".($castlerow["armorlevel"]*7500)."</b>.<br>";
        $page .= "The current Weaponry rating is: <b>".$castlerow["weaponry"]."</b> out of <b>".($castlerow["weaponrylevel"]*6000)."</b>.<p>";
    		$page .= "<center><br><a href='home.php'>Back to your Home</a></center>";
    		display($page,"Repair Home");
	}
	if (isset($_POST["armor"])) {
		$repaircost = $_POST["repaircost"];
		$castleid = $_POST["castleid"];
		$ruined = $_POST["ruined"];
		$castlegold = $_POST["castlegold"];
		$fullrepair = $_POST["fullrepair"];
		$page = "<table width='100%'><tr><td class='title'>Repair Home</td></tr></table>";
		
				if (intval($repaircost) < 0) {
		$page .= "<b>You can't negatively repair your home.<p>";
		$page .= "<center><br><a href='home.php'>Back to your Home</a></center>";
	    display($page,"Repair Home");}

		if (intval($repaircost) > $castlegold) {
		$page .= "<b>You do not have ".$repaircost." gold available in your bank.<p>";
		$page .= "<center><br><a href='home.php'>Back to your Home</a></center>";
	    display($page,"Repair Home");}

	    if (intval($repaircost) > $fullrepair) {$repaircost = $fullrepair;}
	    $newarmor = $castlerow["armor"] + intval($repaircost/100);
	    $newgold = $userrow["bank"] - $repaircost;
	    $updatequery = doquery("UPDATE {{table}} SET bank='$newgold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	    $query = doquery("UPDATE {{table}} SET armor='$newarmor' WHERE id='$castleid' LIMIT 1", "homes");
	    $page .= "You have repaired ".$repaircost." worth of damage to your Homes armor.<br>";
		$page .= "Your bank currently has ".$newgold." gold available.<br>";
		$page .= "The Home currently has <b>".$castlerow["currenthp"]."</b> structural points left out ";
		$page .= "of <b>".$castlerow["maxhp"]."</b> total.<br>";
		$page .= "The current Armor rating is: <b>".$newarmor."</b> out of <b>".($castlerow["armorlevel"]*100)."</b>.<br>";
        $page .= "The current Weaponry rating is: <b>".$castlerow["weaponry"]."</b> out of <b>".($castlerow["weaponrylevel"]*100)."</b>.<p>";

    	$page .= "<center><br><a href='home.php'>Back to your Home</a></center>";
    	display($page,"Repair Home");
	}
	if (isset($_POST["weaponry"])) {
		$repaircost = $_POST["repaircost"];
		$castleid = $_POST["castleid"];
		$ruined = $_POST["ruined"];
		$castlegold = $_POST["castlegold"];
		$fullrepair = $_POST["fullrepair"];
		$page = "<table width='100%'><tr><td class='title'>Repair Home</td></tr></table>";
		
				if (intval($repaircost) < 0) {
		$page .= "<b>You can't negatively repair your home.<p>";
		$page .= "<center><br><a href='home.php'>Back to your Home</a></center>";
	    display($page,"Repair Home");}

		if (intval($repaircost) > $castlegold) {
		$page .= "<b>You do not have ".$repaircost." gold available in your bank.<p>";
		$page .= "<center><br><a href='home.php'>Back to your Home</a></center>";
	    display($page,"Repair Home");}

	    if (intval($repaircost) > $fullrepair) {$repaircost = $fullrepair;}
	    $newweaponry = $castlerow["weaponry"] + intval($repaircost/105);
	    $newgold = $userrow["bank"] - $repaircost;
	    $updatequery = doquery("UPDATE {{table}} SET bank='$newgold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	    $query = doquery("UPDATE {{table}} SET weaponry='$newweaponry' WHERE id='$castleid' LIMIT 1", "homes");
	    $page .= "You have repaired ".$repaircost." worth of damage to your Homes weaponry.<br>";
		$page .= "Your bank currently has ".$newgold." gold available.<br>";
		$page .= "Your house currently has <b>".$castlerow["currenthp"]."</b> structural points left out ";
		$page .= "of <b>".$castlerow["maxhp"]."</b> total.<br>";
		$page .= "The current Armor rating is: <b>".$castlerow["armor"]."</b> out of <b>".($castlerow["armorlevel"]*100)."</b>.<br>";
		$page .= "The current Weaponry rating is: ".$newweaponry." out of <b>".($castlerow["weaponrylevel"]*100)."</b>.<p>";

    	$page .= "<center><br><a href='home.php'>Back to your Home</a></center>";
    	display($page,"Repair Home");
	}
	if (isset($_POST["cancel"])) {
		header("Location: home.php"); die();
	}
	$page = "<table width='100%'><tr><td class='title'>Repair Home</td></tr></table>";
	$page .= "<p>Welcome to your House stats. Here you can repair anything that needs repairing by using gold from your Bank.<p>";
	$page .= "Your house currently has <b>".$castlerow["currenthp"]."</b> structural points left out ";
	$page .= "of <b>".$castlerow["maxhp"]."</b> total.<br>";
	$page .= "The current Armor rating is: <b>".$castlerow["armor"]."</b> out of <b>".($castlerow["armorlevel"]*100)."</b>.<br>";
	$page .= "The current Weaponry rating is: ".$castlerow["weaponry"]." out of <b>".($castlerow["weaponrylevel"]*100)."</b>.<p>";

	$healdamage = $castlerow["maxhp"] - $castlerow["currenthp"];
	if ($castlerow["ruined"] != 0) { $healdamage = 8500; }
	$page .= "You currently have <b>".$userrow["bank"]."</b> banked gold available.<br>";
	$page .= "How much would you like to spend on repairs? Each price is stated within the box.<br>";
	$page .= "<form action='home.php?do=homestatus' method='post'>";
	$page .= "<input type='text' name='repaircost' value='".$healdamage."' size='6'>";
	$page .= "<input type='hidden' name='castleid' value='".$castlerow["id"]."'>";
	$page .= "<input type='hidden' name='castlegold' value='".$userrow["bank"]."'>";
	$page .= "<input type='hidden' name='ruined' value='".$castlerow["ruined"]."'>";
	$page .= "<input type='hidden' name='fullrepair' value='".$healdamage."'>";
	$page .= "<input type='submit' name='submit' value='Repair Structure'>";
	$page .= "</form><p>";
	$armordamage = ($castlerow["armorlevel"]*100 - $castlerow["armor"])*100;
	$page .= "<form action='home.php?do=homestatus' method='post'>";
	$page .= "<input type='text' name='repaircost' value='".$armordamage."' size='6'>";
	$page .= "<input type='hidden' name='castleid' value='".$castlerow["id"]."'>";
	$page .= "<input type='hidden' name='castlegold' value='".$userrow["bank"]."'>";
	$page .= "<input type='hidden' name='ruined' value='".$castlerow["ruined"]."'>";
	$page .= "<input type='hidden' name='fullrepair' value='".$armordamage."'>";
	$page .= "<input type='submit' name='armor' value='Repair Armor'>";
	$page .= "</form><p>";
	$weaponrydamage = ($castlerow["weaponrylevel"]*100 - $castlerow["weaponry"])*105;
	$page .= "<form action='home.php?do=homestatus' method='post'>";
	$page .= "<input type='text' name='repaircost' value='".$weaponrydamage."' size='5'>";
	$page .= "<input type='hidden' name='castleid' value='".$castlerow["id"]."'>";
	$page .= "<input type='hidden' name='castlegold' value='".$userrow["bank"]."'>";
	$page .= "<input type='hidden' name='ruined' value='".$castlerow["ruined"]."'>";
	$page .= "<input type='hidden' name='fullrepair' value='".$weaponrydamage."'>";
	$page .= "<input type='submit' name='weaponry' value='Repair Weaponry'><br>";
	$page .= "<input type='submit' name='cancel' value='cancel'>";
	$page .= "</form><p>";
    $page .= "<center><br><a href='home.php'>Back to your Home</a></center>";
    display($page,"Repair Home");
}

function dodefense() {
	global $userrow;

$updatequery = doquery("UPDATE {{table}} SET location='Upgrading Defenses' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "homes");
	$castlerow = mysql_fetch_array($castlequery);
	$newcastlegold = $userow["bank"];
	if (mysql_num_rows($castlequery) <= 0) {header("Location: index.php?do=move:0"); die();}
	$page = "<table width='100%'><tr><td class='title'>Upgrading Defenses</td></tr></table>";
	if (isset($_POST["submit"])) {
	} elseif (isset($_POST["armorup"])) {
		if ($userrow["bank"] < $castlerow["armorlevel"]*42500) {
		$page .= "<p>You do not have enough gold to upgrade this Homes Armor!<br>";
		$page .= "It will cost <b>".($castlerow["armorlevel"]*42500)."</b> gold to upgrade to level ";
		$page .= "<b>".($castlerow["armorlevel"]+1)." armor.<p>";
		$page .= "<center><br><a href='home.php'>Back to your House</a></center>";
    		display($page,"Upgrading Defenses");
		}
		$newarmorlevel = $castlerow["armorlevel"]+1;
		$newarmor = $newarmorlevel*100;
		$newcastlegold = $userrow["bank"] + ($castlerow["armorlevel"]*375);
		$newgold = $userrow["bank"] - ($castlerow["armorlevel"]*42500);
		$updatequery = doquery("UPDATE {{table}} SET bank='$newgold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
		$cu = doquery("UPDATE {{table}} SET armorlevel='$newarmorlevel',armor='$newarmor' WHERE id='".$castlerow["id"]."' LIMIT 1", "homes");
		$page .= "<p>You upgraded this Homes Armor!<p>";
		$castlerow["armorlevel"] += 1;
	} elseif (isset($_POST["weaponup"])) {
		if ($userrow["bank"] < $castlerow["weaponrylevel"]*39000) {
		$page .= "<p>You do not have enough gold to upgrade this Homes Weaponry!<br>";
		$page .= "It will cost <b>".($castlerow["weaponrylevel"]*39000)."</b> gold to upgrade to level ";
		$page .= "<b>".($castlerow["weaponrylevel"]+1)." weapons.<p>";
		$page .= "<center><br><a href='home.php'>Back to your House</a></center>";
    		display($page,"Upgrading Defenses");
		}
		$newweaponrylevel = $castlerow["weaponrylevel"]+1;
		$newweaponry = $newweaponrylevel*100;
		$newcastlegold = $userrow["bank"] + ($castlerow["weaponrylevel"]*390);
		$newgold = $userrow["bank"] - ($castlerow["weaponrylevel"]*39000);
		$updatequery = doquery("UPDATE {{table}} SET bank='$newgold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
		$cu = doquery("UPDATE {{table}} SET weaponrylevel='$newweaponrylevel',weaponry='$newweaponry' WHERE id='".$castlerow["id"]."' LIMIT 1", "homes");
		$page .= "<p>You upgraded this Homes Weaponry!<p>";
		$castlerow["weaponrylevel"] += 1;
	}
	$page .= "<p>Gold will be taken from your Bank to upgrade your Home.<p>";
	$page .= "Your home currently has <b>".$castlerow["currenthp"]."</b> structural points left out ";
	$page .= "of <b>".$castlerow["maxhp"]."</b> total.<p>";
	$page .= "<table>";
	if ($castlerow["armorlevel"] <= 29) {
	$page .= "<tr><td>The current Armor level is: <b>".$castlerow["armorlevel"]."</b>.<br> It will cost ".($castlerow["armorlevel"]*42500)." gold to upgrade this stronghold's armor.</td>";
	$page .= "<td><form action='home.php?do=defense' method='POST'>";
	$page .= "<input type='submit' name='armorup' value='Upgrade Armor'></form></td></tr>";
	} else {
	$page .= "<tr><td>The current Armor level is: <b>".$castlerow["armorlevel"]."</b>.<br>";
	$page .= "You are unable to improve the Armor rating any further.</td>";
	$page .= "<td> </td></tr>";
	}
	if ($castlerow["weaponrylevel"] <= 29) {
	$page .= "<tr><td>The current Weaponry level is: <b>".$castlerow["weaponrylevel"]."</b>.<br> It will cost ".($castlerow["weaponrylevel"]*39000)." gold to upgrade this stronghold's weaponry.</td>";
	$page .= "<td><form action='home.php?do=defense' method='POST'>";
	$page .= "<input type='submit' name='weaponup' value='Upgrade Weapons'></form></td></tr>";
	} else {
	$page .= "<tr><td>The current Weaponry level is: <b>".$castlerow["magiclevel"]."</b>.<br>";
	$page .= "You are unable to improve the Weapons rating any further.</td>";
	$page .= "<td> </td></tr>";
	}
	$page .= "</table><p>";
	$page .= "<center><br><a href='home.php'>Back to your House</a></center>";
    display($page,"Upgrading Defenses");
}

function move() {
	global $userrow;

	$castlequery = doquery("SELECT * FROM {{table}} WHERE charname='".$userrow["charname"]."' ", "homes");
	$castlerow = mysql_fetch_array($castlequery);

	if ($userrow["dscales"] < 300) {
		$page = "<table width='100%'><tr><td class='title'>Moving your Home</td></tr></table>";
		$page .= "<p>You do not have enough Dragon Scales to move your Home.<br>";
		$page .= "You need to have at least 300 Dragon Scales to move your Home.<p>";
		$page .= "There are currently <b>".$userrow["dscales"]."</b> Dragon Scales available.<p>";
    		$page .= "<center><br><a href='home.php'>Return to your Home</a></center>";
		display($page, "Moving your Home");
	}
	if ($userrow["currentap"] <= 24) {
		$page = "<table width='100%'><tr><td class='title'>Not Enough Ability Points!</td></tr></table>";
		$page .= "<br><b>You do not have enough AP to perform this action.</b><br><br>";
		$page .= "You must have at least 25 AP in order to move your Home.";
		$page .= "Your AP is refilled for a price of 50 Dragon Scales.<br>";
		$page .= "<p>You may return to your <a href='home.php'>Home</a>, ";
		$page .= "or leave and <a href='index.php'>continue exploring</a>.";
		display($page, "Not Enough AP");
	}

    if (isset($_POST["submit"])) {

$updatequery = doquery("UPDATE {{table}} SET location='Moving Home' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

		$page = "<table width='100%'><tr><td class='title'>Moving your Home</td></tr></table>";
		$page .= "<p>You may move your Home anywhere on the map with the following restrictions:";
		$page .= "<ul><li>The Home can not be within 5 steps of any town.";
		$page .= "<li>The Home can not be within 25 steps of any other Stronghold.";
				$page .= "<li>The Home can not be within 5 steps of any other Home.";
		$page .= "<li>The Home must at least be 300 steps from the edge of the map.<br>";
		$page .= "<i>Maximum 300 latitude or longitude. Too dangerous to go above 300.</i></ul>";
		$page .= "<table width='100%' border='0'>";
		$page .= "<tr><td><form action='home.php?do=move2' method='POST'>";
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
		$page .= "</td><td><form action='home.php'>";
		$page .= "<input type='submit' name='cancel' value='Cancel'></form></td></tr></table>";

		$page .= "<center><br><a href='home.php'>Return to your Home</a></center>";
    } elseif (isset($_POST["cancel"])) {
        header("Location: home.php"); die();

    } else {
	$page = "<table width='100%'><tr><td class='title'>Moving your Home</td></tr></table>";
	$page .= "<p>It costs 300 Dragon Scales and 25 AP to move your Home.<br>";
	$page .= "You currently have ".$userrow["dscales"]." Dragon Scales available.<br>";
	$page .= "Do you wish to move your Home? You will not lose any of your upgrades or guards.<p>";
	$page .= "<form action='home.php?do=move' method='post'>\n";
	$page .= "<input type='submit' name='submit' value='Yes' />  \t";
	$page .= "<input type='submit' name='cancel' value='No' />\n";
	$page .= "</form>\n";
	$page .= "<center><br><a href='home.php'>Return to your Home</a></center>";


    }
    display($page,"Moving your Home");
}

function move2() {
	global $userrow;

	if ($userrow["currentap"] <= 24) {
		$page = "<table width='100%'><tr><td class='title'>Not Enough Ability Points!</td></tr></table>";
		$page .= "<br><b>You do not have enough AP to perform this action.</b><br><br>";
		$page .= "You must have at least 25 AP in order to move your Home. ";
		$page .= "Your AP is refilled for a price of 50 Dragon Scales.<br>";
		$page .= "<p>You may return to your <a href='home.php'>Home</a>, ";
		$page .= "or leave and <a href='index.php'>continue exploring</a>.";
		display($page, "Not Enough AP");
	}

	$page = "<table width='100%'><tr><td class='title'>Moving your Home</td></tr></table>";
    if (!isset($_POST["submit"])) {
	$page .= "Invalid command!<p>";
	$page .= "<center><br><a href='home.php'>Return to your Home</a></center>";
    display($page,"Moving your Home");
    }
    $page = "<table width='100%'><tr><td class='title'>Moving your Home</td></tr></table>";
    $lat = $_POST["latitude"];
    $lon = $_POST["longitude"];
	unset($errors);
	if (($lat > 300) || ($lat < 25)) {
		$errors .= "There are the following errors while moving your Home, please go back and try again:<p><b>Invalid Latitude!</b><br>Latitude must be between 25 and 300<p>";
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
		$page .= "<center><br><a href='home.php'>Back to your Home</a></center>";
		display($page,"Moving your Home");
		die();
	}
	$new = doquery("UPDATE {{table}} SET latitude='$lat',longitude='$lon' WHERE charname='".$userrow["charname"]."' ", "homes");
	$newdscales = $userrow["dscales"] - 300;
	$g = doquery("UPDATE {{table}} SET dscales='$newdscales', home='Yes, but moved' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	$page .= "You have successfully moved your Home to ";

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
	$newap = $userrow["currentap"] - 25;
	$pquery = doquery("UPDATE {{table}} SET currentap='$newap' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	$page .= "<center><br>You must now continue <a href='index.php'>exploring</a> to find your new home location.</center>";
	display($page,"Moving your Home");

}
?>




