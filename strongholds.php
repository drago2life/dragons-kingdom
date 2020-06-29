<?php // strongholds.php :: stronghold stuff

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

	$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "strongholds");
	if (mysql_num_rows($castlequery) <= 0) { header("Location: index.php?do=move:0"); die(); }
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
	elseif ($do[0] == "build") { dobuild($do[1]); }
	elseif ($do[0] == "build2") { dobuild2(); }
	elseif ($do[0] == "attack") { doattack(); }
	elseif ($do[0] == "attack2") { doattack2($do[1]); }
	elseif ($do[0] == "defense") { dodefense(); }
	elseif ($do[0] == "recruit") { dorecruit(); }
	elseif ($do[0] == "siege") { dosiege(); }
	elseif ($do[0] == "arena") { doarena(); }
	elseif ($do[0] == "rest") { dorest(); }
	elseif ($do[0] == "restap") { dorestap(); }
	elseif ($do[0] == "gstatus") { dogstatus(); }
	elseif ($do[0] == "gportal") { doportals($do[1]); }
	elseif ($do[0] == "townportal") { dotownback(); }
	elseif ($do[0] == "repair") { dorepair(); }
	elseif ($do[0] == "castlestatus") { dosstatus(); }
	elseif ($do[0] == "magics") { magics($do[1]); }
	elseif ($do[0] == "users") { dolistmembers($do[1]); }
	elseif ($do[0] == "editmember") { doeditmembers($do[1]); }
	elseif ($do[0] == "bootmember") { dobootmember($do[1]); }
	elseif ($do[0] == "editguild") { doeditguild(); }
	elseif ($do[0] == "logs") { dologs($do[1]); }
	elseif ($do[0] == "donate") { donate(); }
        elseif ($do[0] == "gamble") { gamble($do[1]); }
        elseif ($do[0] == "summon") { summon($do[1]); }
	elseif ($do[0] == "items") { items($do[1]); }
	elseif ($do[0] == "stats") { dostats($do[1]); }
    elseif ($do[0] == "gforum") { gforum(); }
    elseif ($do[0] == "editgforum") { editgforum($do[1]); }
    elseif ($do[0] == "mailall") { mailall(); }

} else { donothing(); }

function donothing() {
	global $userrow;
	$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' AND guildname='".$userrow["guildname"]."' LIMIT 1", "strongholds");
	if (mysql_num_rows($castlequery) <= 0) { header("Location: index.php?do=move:0"); die(); }

if (($userrow["guildname"] != "-") && ($userrow["guildname"] != "")) {
$page = <<<END
<table width="100%">
<tr><td class="title">Guild Stronghold</td></tr></table>
<p>Welcome to your Guild Stronghold. Please navigate through your Stronghold by using the links below.<p>If you are not the owner of this Guild and Stronghold, you will find that you have fewer links. To steal other Guilds Gold and Dragon Scales you must attack them by walking directly to their Stronghold. Otherwise, you can attack them using the attack link below, but you will not receive their hard earned treasure.<p>Be aware that using or even viewing certain features within a Stronghold will cause your AP to drain.<p>
<table>
<tr><td width="173">
<ul><b><u>General Menu</u></b>
<li /><a href="strongholds.php?do=logs">Guild Events</a>
<li /><a href="strongholds.php?do=rest">Resting Area</a>
<li /><a href="strongholds.php?do=restap">Restore AP</a>
<li /><a href="strongholds.php?do=arena">Pet Arena</a>
<li /><a href="strongholds.php?do=summon">Summon Souls</a>
<li /><a href="strongholds.php?do=gportal">Stronghold Portals</a>
<li /><a href="strongholds.php?do=townportal">Back to Town</a>

</ul>
</td><td width="416">
<ul><b><u>Members Menu</u></b>
<li /><a href="strongholds.php?do=stats">Stronghold Statistics</a>
<li /><a href="gforum.php">Guild Forum</a>
<li /><a href="strongholds.php?do=items">Buy Unique Items</a>
<li /><a href="strongholds.php?do=gamble">High Stake Gambling</a>
<li /><a href="strongholds.php?do=vault">Guild Vault</a>
<li /><a href="strongholds.php?do=donate">Stronghold Storage</a><br><br>
</ul>
</td></tr><tr><td>
<ul><b><u>Combat Menu</u></b>
<li /><a href="strongholds.php?do=castlestatus">Repair Stronghold</a><br>
<li /><a href="strongholds.php?do=attack">Attack Others</a><br>
<li /><a href="strongholds.php?do=defense">Fortify Defenses</a><br>
<li /><a href="strongholds.php?do=recruit">Recruit Troops</a><br>
</ul>
</td><td>
END;
if ($userrow["guildrank"] >= 100) {
$page .= <<<END
<ul><b><u>Guild Admins</u></b>
<li /><a href="strongholds.php?do=editguild">Edit Guild</a>
<li /><a href="strongholds.php?do=gforum">Edit Guild Forum<a>
<li /><a href="strongholds.php?do=users">Edit & Boot Members</a>
<li /><a href="strongholds.php?do=mailall">Guild Global Mailing</a>
<li /><a href="strongholds.php?do=build">Build a Stronghold</</a>
<li /><a href="strongholds.php?do=magics">Guild Magics<a>
</ul>
</td></tr>
END;
}
$page .= "</table><center><br><a href='index.php?do=move:0'>Leave the Stronghold</a></center>";

$u = doquery("UPDATE {{table}} SET currentaction='Stronghold',location='Inside a Stronghold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

$squery = doquery("SELECT * FROM {{table}} WHERE guildname='".$userrow["guildname"]."' LIMIT 1", "strongholds");
$srow = mysql_fetch_array($squery);
if ($srow["guildname"] != $userrow["guildname"]) {

	}


}else {   // if not a member of a guild.

	header("Location: index.php"); die();
}
    display($page,"Guild Stronghold");
}



function dorest() {


     global $userrow, $numqueries;

    $townquery = doquery("SELECT name,pool FROM {{table}} LIMIT 1", "towns");

    $townrow = mysql_fetch_array($townquery);

	$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "strongholds");
	if (mysql_num_rows($castlequery) <= 0) { header("Location: index.php"); die(); }


    if ($userrow["dscales"] < $townrow["pool"]) { display("You do not have enough Dragon Scales to Rest here.<br /><br />You may return to the <a href=\"strongholds.php\">Stronghold</a>, or use the compass to the right to start exploring.", "Resting Area"); die(); }

    if (isset($_POST["submit"])) {

        $newdscales = $userrow["dscales"] - $townrow["pool"];
        $query = doquery("UPDATE {{table}} SET dscales='$newdscales', drink='Empty', potion='Empty', currenthp='".$userrow["maxhp"]."',currentmp='".$userrow["maxmp"]."',currenttp='".$userrow["maxtp"]."' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Resting Area";
        $page = "<table width='100%' border='1'><tr><td class='title'>Resting Area - Refreshed</td></tr></table><p>";
        $page .= "You feel refreshed and ready to fight another day, with all current stats to maximum capacity.<br /><br />You may return to the <a href=\"strongholds.php\">Stronghold</a>, or use the compass on the right to continue exploring.";

    } elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

    } else {

$updatequery = doquery("UPDATE {{table}} SET location='Resting Area' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

        $title = "Resting Area";
        $page = "<table width='100%' border='1'><tr><td class='title'>Resting Area</td></tr></table><p>";
        $page .= "Resting at this Healing Pool will refill your current HP, MP, and TP to their maximum levels. It will also remove your current Tavern Drink and Potion if you have recently purchased one.<br /><br />\n";
        $page .= "A nice rest here will cost you <b>" . $townrow["pool"] . " Dragon Scales</b>.<p>Is that ok?<br /><br />\n";
        $page .= "<form action=\"strongholds.php?do=rest\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes\" /><p>If you have changed your mind you may return to the <a href=\"strongholds.php\">Stronghold</a>, or use the compass on the right to continue exploring.\n";
        $page .= "</form>\n";

    }

    display($page, $title);

}

function mailall() {

    global $userrow;
        $updatequery = doquery("UPDATE {{table}} SET location='Guild Global Mailing' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

    if ($userrow["guildrank"] < 100) { header("Location: strongholds.php"); die();}
    if (isset($_POST["submit"])) {
        extract($_POST);
    $content = str_replace("'", "\'", $content);
    $content = str_replace("`", "\'", $content);
    $content = trim($content);
	  $content = "<b><font color=red><u>Guild Global Mailing</u>:</b></font>\n\n". $content;
	  $subject = "". $subject;
	  $page = "<table width=\"100%\"><tr><td><b>Guild Global Mailing:</b><br /><br/ >";
	  $c = 0;
	  $page = "<table width='100%'><tr><td class='title'>Guild Global Mailing</td></tr></table><p>";
        $mailallquery = doquery("SELECT charname,guildname FROM {{table}} WHERE guildname='".$userrow["guildname"]."'", "users");
	  while ($charrow = mysql_fetch_array($mailallquery)) {
	  	    $content = str_replace("'", "\'", $content);
    $content = str_replace("`", "\'", $content);
    $content = trim($content);
		$recipient = $charrow["charname"];
		$c += 1;
           	$query = doquery("INSERT INTO {{table}} SET postdate=NOW(),author='".$userrow["charname"]."',recipient='$recipient',subject='$subject',content='$content'", "gamemail");

        }

    	  $page .= "<table width=\"100%\"><tr><td><b>Mails Sent:</b><br /><br/ >Your message has been sent to all ".$c." players of the guild.<br /><br />You may return to the <a href=\"strongholds.php\">Stronghold</a>, or use the compass to the right to start exploring.</table>";
    	      display($page, "Guild Global Mail");
    }


    $page .= "<table width=\"100%\"><tr><td><b>Guild Global Mailing:</b><br /><br/ >";
    $page .= "Enter the message below and it will be sent to all players of the guild.<p>";
    $page .= "<form action=\"strongholds.php?do=mailall\" method=\"post\">";
    $page .= "Subject:<br />";
    $page .= "<input type=\"text\" name=\"subject\" value=\"Guild Mail\" size=\"35\" maxlength=\"35\" /><br><br>";
    $page .= "Message:<br />";
    $page .= "<textarea name=\"content\" rows=\"7\" cols=\"40\"></textarea><br><br>";
    $page .= "<input type=\"submit\" name=\"submit\" value=\"Send Mails\" /> ";
    $page .= "<input type=\"reset\" name=\"reset\" value=\"Reset\" />";
    $page .= "</form><br /><br />You may return to the <a href=\"strongholds.php\">Stronghold</a>, or use the compass to the right to start exploring.</td></tr></table>";
    display($page, "Guild Global Mail");

}

function dorestap() {


     global $userrow, $numqueries;

    $townquery = doquery("SELECT name,restap FROM {{table}} LIMIT 1", "towns");

    $townrow = mysql_fetch_array($townquery);

	$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "strongholds");
	if (mysql_num_rows($castlequery) <= 0) { header("Location: index.php"); die(); }


    if ($userrow["dscales"] < $townrow["restap"]) { display("You do not have enough Dragon Scales to Rest here.<br /><br />You may return to the <a href=\"strongholds.php\">Stronghold</a>, or use the compass to the right to start exploring.", "Resting Area"); die(); }

    if (isset($_POST["submit"])) {

        $newdscales = $userrow["dscales"] - $townrow["restap"];
        $query = doquery("UPDATE {{table}} SET dscales='$newdscales', currentap='".$userrow["maxap"]."' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Restore Ability Points";
        $page = "<table width='100%' border='1'><tr><td class='title'>Restore Ability Points</td></tr></table><p>";
        $page .= "You feel refreshed after a long rest, and eating healthily for a short period of time.<br /><br />You may return to the <a href=\"strongholds.php\">Stronghold</a>, or use the compass on the right to continue exploring.";

    } elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

    } else {

$updatequery = doquery("UPDATE {{table}} SET location='Restore AP' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

        $title = "Restore Ability Points";
        $page = "<table width='100%' border='1'><tr><td class='title'>Restore Ability Points</td></tr></table><p>";
        $page .= "Ability points are special Stats which are used for certain features such as building a Stronghold, attacking a Stronghold and any other feature within a Stronghold. They are simply your Ability to do specific tasks within a Guild and Stronghold.<br /><br />\n";
        $page .= "<p>To restore your AP completely, it will cost you <b>" . $townrow["restap"] . " Dragon Scales</b>.<p>This will not heal your HP, MP or TP. It also won't remove your Tavern Drink or Potion.<p>Is that ok?<br /><br />\n";
        $page .= "<form action=\"strongholds.php?do=restap\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes\" /><p>If you have changed your mind you may return to the <a href=\"strongholds.php\">Stronghold</a>, or use the compass on the right to continue exploring.\n";
        $page .= "</form>\n";

    }

    display($page, $title);

}

function dosiege() {
	global $userrow;

$updatequery = doquery("UPDATE {{table}} SET location='Sieging a Stronghold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");


	if ($userrow["currentap"] <= 0) {
		$page = "<table width='100%'><tr><td class='title'>Not Enough Ability Points!</td></tr></table>";
		$page .= "<br><b>You do not have enough AP to perform this action.</b><br><br>";
		$page .= "You must have at least 1 AP in order to do this action. ";
		$page .= "Your AP is refilled for a price of 50 Dragon Scales.<br>";
		$page .= "<p>You may ";
		$page .= "leave and <a href='index.php'>continue exploring</a>.";
		display($page, "Not Enough AP");
}

	$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "strongholds");
	$castlerow = mysql_fetch_array($castlequery);
	if ($castlerow["ruined"] != 0) {
		$page = "<table width='100%'><tr><td class='title'>Attacking a Stronghold</td></tr></table>";
		$page .= "<p>This stronghold, belonging to the ".$castlerow["guildname"]." guild, ";
		$page .= "already lies in ruins.  Any gold or Dragon Scales that were left here are gone by now.<br>";
		$page .= "<center><br><a href='index.php'>continue exploring</a></center>";
    	display($page,"Attacking a Stronghold");
	}

	$userchance = rand(1,intval( $userrow["level"] + $userrow["strength"]));
	$castlechance = rand(1,intval(($castlerow["armor"]) + ($castlerow["snails"]) + ($castlerow["minnows"]) + ($castlerow["kelplings"])));

	if ($userchance >= $castlechance) {

		$page = "<table width='100%'><tr><td class='title'>Attacking a Stronghold</td></tr></table>";
		$page .= "<p><b> You successfully broke into the ".$castlerow["guildname"]." stronghold!</b><p>";
		$newgold = rand(1, intval(sqrt($castlerow["gold"])/(rand(5,10)))+1);
		if (($userrow["gold"] + $newgold) >=999999999999) {$newgold = ($userrow["gold"]+$newgold) - 999999999999;}
		$castlegold = $castlerow["gold"] - $newgold;
		$page .= "You have stolen ".$newgold." gold from the Stronghold.<br>";
		$newdscales = rand(1, 3);
		$page .= "You have stolen ".$newdscales." Dragon Scales from the Stronghold.<br>";


        $newap = $userrow["currentap"] - 1;
	$pquery = doquery("UPDATE {{table}} SET currentap='$newap' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

		$tdmg = intval(sqrt(($userrow["attackpower"]+$userrow["strength"])) - sqrt($castlerow["armor"]+($castlerow["level"]*10)));
		if ($tdmg <= "0") {$tdmg = 1;}
		$newdmg = rand(1, $tdmg);
		$castlehp = $castlerow["currenthp"] - $newdmg;
		$page .= "You have dealt ".$newdmg." damage to the Stronghold! ";
		$page .= "<i>(".$castlehp." left.)</i><br>";
		$ruined = 0;
		$castlelevel = $castlerow["level"];
			$logdate = "cycle_".date(W);
			$logpath = "./logs/guild".$castlerow["guildid"]."/";
			if (!is_dir($logpath)) {
		 	@mkdir($logpath,0777);
       	 	@chmod($logpath,0777);
       		}
		$logfile =  $logpath.$logdate.".log";
		$logcomments = "--<b>".$userrow["charname"]."</b> sieged and broke into your Stronghold at ";
		$logcomments .= $userrow["latitude"].",".$userrow["longitude"]." on ".date("r");
		$logcomments .= " and stole $newgold gold! <br>\r\n";
		$fp = fopen("$logfile", "a");
		fwrite($fp, "$logcomments");
		fclose ($fp);

		if ($castlehp < 1 ) {
		   $page .= "<h3>This stronghold has been ruined!</h3>";
		   $castlegold = intval($castlegold/10);
		   $page .= "The level of this stronghold has been reduced by 1.<br>";
		   $castlelevel -= 1;
		   if ($castlelevel <= 0) {$castlelevel = 1;}
		   $ruined = 1;
			$logdate = "cycle_".date(W);
			$logpath = "./logs/guild".$castlerow["guildid"]."/";
			if (!is_dir($logpath)) {
		 	@mkdir($logpath,0777);
       	 	@chmod($logpath,0777);
       		}
		$logfile =  $logpath.$logdate.".log";
		$logcomments = "***<b>".$userrow["charname"]."</b> sieged and attacked your Stronghold at ";
		$logcomments .= $userrow["latitude"].",".$userrow["longitude"]." on ".date("r");
		$logcomments .= ". The Stronghold was Destroyed!<br>\r\n";
		$fp = fopen("$logfile", "a");
		fwrite($fp, "$logcomments");
		fclose ($fp);
		$logdel=date("W")-5;
		if ($logdel < 1) {$logdel= $logdel -52;}
		$logrem=$logpath."cycle_".$logdel.".log";
		unlink($logrem);
		}
                $newgold += $userrow["gold"];
		$newdscales += $userrow["dscales"];
		if ($castlegold <= 2500) {$castlegold = 2550;}
		$query = doquery("UPDATE {{table}} SET gold='$castlegold',level='$castlelevel',currenthp='$castlehp',ruined='$ruined' WHERE id='".$castlerow["id"]."' LIMIT 1", "strongholds");
		$query = doquery("UPDATE {{table}} SET gold='$newgold',dscales='$newdscales' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
		$page .= "<center><br><a href='index.php'>continue exploring</a></center>";
    	display($page,"Attacking a Stronghold");
	} else {
		$newdmg = intval(($castlerow["weaponry"])/(rand(4,intval(sqrt($userrow["attackpower"])+2)))) + 1;
		$newhp = $userrow["currenthp"] - $newdmg;
		if ($newhp <= 0) {
				$logdate = "cycle_".date(W);
				$logpath = "./logs/guild".$castlerow["guildid"]."/";
				if (!is_dir($logpath)) {
				 @mkdir($logpath,0777);
		       	 @chmod($logpath,0777);
		       	}
				$logfile =  $logpath.$logdate.".log";
				$logcomments = "--<b>".$userrow["charname"]."</b> attacked your Stronghold at ";
				$logcomments .= $userrow["latitude"].",".$userrow["longitude"]." on ".date("r");
				$logcomments .= " but was unable to break in!<br>\r\n";
				$fp = fopen("$logfile", "a");
				fwrite($fp, "$logcomments");
		fclose ($fp);
			$newgold = ceil($userrow["gold"]/2);
			$newhp = ceil($userrow["maxhp"]/4);
			$newdscales = ceil($userrow["dscales"]/3);
			$updatequery = doquery("UPDATE {{table}} SET dscales='$newdscales',currenthp='$newhp',location='Dead',currentaction='In Town',currentmonster='0',currentmonsterhp='0',currentmonstersleep='0',currentmonsterimmune='0',currentfight='0',latitude='0',longitude='0',gold='$newgold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
			$page = "<table width='100%'><tr><td class='title'>Attacking a Stronghold</td></tr></table>";
			$page .= "<p><font color=red>You have died from attacking a Stronghold.</font></b><br /><br />As a consequence, you've lost half of your gold and <b>some dragon scales</b>. However, you have been given back a portion of your hit points to continue your journey.<br /><br /><center><img src=\"images/died.gif\" border=\"0\" alt=\"You have Died\" /></a></center><p>You may now continue back to <a href=\"index.php\">town</a>, and we hope you fair better next time.";
			$townquery = doquery("SELECT * FROM {{table}} WHERE name='".$userrow["lasttown"]."' LIMIT 1", "towns");
			$townrow = mysql_fetch_array($townquery);
			$latitude=$townrow["latitude"];
			$longitude=$townrow["longitude"];
			$updatequery = doquery("UPDATE {{table}} SET latitude='$latitude',longitude='$longitude' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
			$page .= "<center><br><a href='index.php'>continue exploring</a></center>";
		   	display($page,"Attacking a Stronghold");

    	}
		$page = "<table width='100%'><tr><td class='title'>Attacking a Stronghold</td></tr></table>";
		$page .= "<p>You were unable to break into the ".$castlerow["guildname"]." stronghold, and took some ";
		$page .= "minor damage (<i>".$newdmg." hp</i>) as the guards 'escorted' you back outside.<br>";


            $newap = $userrow["currentap"] - 1;
	$pquery = doquery("UPDATE {{table}} SET currentap='$newap' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
		$query = doquery("UPDATE {{table}} SET currenthp='$newhp' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
		$page .= "<center><br><a href='index.php'>continue exploring</a></center>";
    	display($page,"Attacking a Stronghold");
	}
}


function dologs($log) {
	global $userrow;

$updatequery = doquery("UPDATE {{table}} SET location='Stronghold Events' WHERE id='".$userrow["id"]."' LIMIT 1", "users");


	$castlequery = doquery("SELECT * FROM {{table}} WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
	$castlerow = mysql_fetch_array($castlequery);
	if ($log != "") {

		$logpath = "./logs/guild".$castlerow["id"]."/";
		$logfile =  $logpath.$log;
		$logs = $logfile;
		$h = fopen("$logs","r");
			if (!$h) {
			$page .= "unable to open $logs";
			}
		$datafile = fread($h, 1000000);
		fclose ($h);

	        $page = "<table width=\"100%\"><tr><td class=\"title\">Guild Events</td></tr></table>";
		$page .= "<p>Guild logs record information on members joining or leaving, ";
		$page .= "as well as attacks on your strongholds.<br>";
		$page .= "<p><b><u> Guild News & Events</u></b><br>";
		$page .= $datafile;
		$page .= "<br><b><i>- End of Events -</i></b><br>";
		$page .= "<center><br><a href='strongholds.php'>Back to the Guild Stronghold</a></center>";

	} else {

	$page = "<table width=\"100%\"><tr><td class=\"title\">Guild Events</td></tr></table>";
	$page .= "<p>Guild logs record information on members joining or leaving, ";
	$page .= "as well as attacks on your strongholds.<br>";
	$page .= "<p><b><u>Click below to View:</u></b><br><br>";
   	$count = 1;
   	$dirstring = "";
    	$logdate = "cycle_".date(W);
	$logpath = "./logs/guild".$castlerow["id"]."/";
	$mydir = dir($logpath);
	$logfile =  $logpath.$logdate.".log";
   	while(($file = $mydir->read()) !== false) {
         if ($file !== "." && $file !==".." &&  $file !="WS_FTP.LOG" ) {
		$page .= "<li/> <a href='strongholds.php?do=logs:$file'>$file</a><br>";
         }
   	}
   	$mydir->close();
	$page .= "<br><i><b>End of Events</b></i><br>";
	$page .= "<center><br><a href='strongholds.php'>Back to the Guild Stronghold</a></center>";
	}

    	display($page, "Guild Events");

    display($page,"Guild News & Events");
}


function dosstatus() {
	global $userrow;

$updatequery = doquery("UPDATE {{table}} SET location='Repairing Stronghold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");


	$tquery = doquery("SELECT id FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "strongholds");
	$trow = mysql_fetch_array($tquery);
	$currentcastle = $trow["id"];
	$castlequery = doquery("SELECT * FROM {{table}} WHERE id='$currentcastle' LIMIT 1", "strongholds");
	$castlerow = mysql_fetch_array($castlequery);
	if (isset($_POST["submit"])) {
		$repaircost = $_POST["repaircost"];
		$castleid = $_POST["castleid"];
		$ruined = $_POST["ruined"];
		$castlegold = $_POST["castlegold"];
		$fullrepair = $_POST["fullrepair"];
		$page = "<table width='100%'><tr><td class='title'>".$userrow["guildname"]." Guild</td></tr></table>";

						if (intval($repaircost) < 0) {
		$page .= "<b>You can't negatively repair your stronghold.<p>";
		$page .= "<center><br><a href='strongholds.php'>Back to your Stronghold</a></center>";
	    display($page,"Repair Stronghold");}
		
		if (intval($repaircost) > $castlegold) {
		$page .= "<b>You do not have ".$repaircost." gold available at this stronghold.<p>";
		$page .= "<center><br><a href='strongholds.php'>Back to the Stronghold</a></center>";
	    display($page,"Guild Stronghold");}

	    if (intval($repaircost) > $fullrepair) {$repaircost = $fullrepair;}
	    $newhp = $castlerow["currenthp"] + $repaircost;
	    $newgold = $castlerow["gold"] - $repaircost;
	   	if ($ruined != 0) {$repaircost = 8500;
	    $newhp = 1;
	    $newgold = $castlerow["gold"] - $repaircost;
	    }
	    $query = doquery("UPDATE {{table}} SET currenthp='$newhp',ruined='0',gold='$newgold' WHERE id='$castleid' LIMIT 1", "strongholds");
	    	$page .= "You have repaired ".$repaircost." worth of damage to the Stronghold.<br>";
		$page .= "This stronghold currently has ".$newgold." gold available.<br>";
		$page .= "The Stronghold currently has <b>".$newhp."</b> structural points left out ";
		$page .= "of <b>".$castlerow["maxhp"]."</b> total.<br>";
		$page .= "The current Armor rating is: <b>".$castlerow["armor"]."</b> out of <b>".($castlerow["armorlevel"]*7500)."</b>.<br>";
		$page .= "The current Magic level is: <b>".$castlerow["magic"]."</b> out of <b>".($castlerow["magiclevel"]*4500)."</b>.<br>";
		$page .= "The current Weaponry rating is: <b>".$castlerow["weaponry"]."</b> out of <b>".($castlerow["weaponrylevel"]*6000)."</b>.<p>";
    		$page .= "<center><br><a href='strongholds.php'>Back to the Stronghold</a></center>";
    		display($page,"Guild Stronghold");
	}
	if (isset($_POST["armor"])) {
		$repaircost = $_POST["repaircost"];
		$castleid = $_POST["castleid"];
		$ruined = $_POST["ruined"];
		$castlegold = $_POST["castlegold"];
		$fullrepair = $_POST["fullrepair"];
		$page = "<table width='100%'><tr><td class='title'>".$userrow["guildname"]." Guild</td></tr></table>";

		if (intval($repaircost) > $castlegold) {
		$page .= "<b>You do not have ".$repaircost." gold available at this stronghold.<p>";
		$page .= "<center><br><a href='strongholds.php'>Back to the Stronghold</a></center>";
	    display($page,"Guild Stronghold");}

	    if (intval($repaircost) > $fullrepair) {$repaircost = $fullrepair;}
	    $newarmor = $castlerow["armor"] + intval($repaircost/100);
	    $newgold = $castlerow["gold"] - $repaircost;
	    $query = doquery("UPDATE {{table}} SET armor='$newarmor',gold='$newgold' WHERE id='$castleid' LIMIT 1", "strongholds");
	    $page .= "You have repaired ".$repaircost." worth of damage to the Stronghold's armor.<br>";
		$page .= "This stronghold currently has ".$newgold." gold available.<br>";
		$page .= "The Stronghold currently has <b>".$castlerow["currenthp"]."</b> structural points left out ";
		$page .= "of <b>".$castlerow["maxhp"]."</b> total.<br>";
		$page .= "The current Armor rating is: <b>".$newarmor."</b> out of <b>".($castlerow["armorlevel"]*100)."</b>.<br>";
		$page .= "The current Magic level is: <b>".$castlerow["magic"]."</b> out of <b>".($castlerow["magiclevel"]*100)."</b>.<br>";
		$page .= "The current Weaponry rating is: <b>".$castlerow["weaponry"]."</b> out of <b>".($castlerow["weaponrylevel"]*100)."</b>.<p>";

    	$page .= "<center><br><a href='strongholds.php'>Back to the Stronghold</a></center>";
    	display($page,"Guild Stronghold");
	}
	if (isset($_POST["magicarmor"])) {
		$repaircost = $_POST["repaircost"];
		$castleid = $_POST["castleid"];
		$ruined = $_POST["ruined"];
		$castlegold = $_POST["castlegold"];
		$fullrepair = $_POST["fullrepair"];
		$page = "<table width='100%'><tr><td class='title'>".$userrow["guildname"]." Guild</td></tr></table>";

								if (intval($repaircost) < 0) {
		$page .= "<b>You can't negatively repair your stronghold.<p>";
		$page .= "<center><br><a href='strongholds.php'>Back to your Stronghold</a></center>";
	    display($page,"Repair Stronghold");}
		
		if (intval($repaircost) > $castlegold) {
		$page .= "<b>You do not have ".$repaircost." gold available at this stronghold.<p>";
		$page .= "<center><br><a href='strongholds.php'>Back to the Stronghold</a></center>";
	    display($page,"Guild Stronghold");}

	    if (intval($repaircost) > $fullrepair) {$repaircost = $fullrepair;}
	    $newmagic = $castlerow["magic"] + intval($repaircost/90);
	    $newgold = $castlerow["gold"] - $repaircost;
	    $query = doquery("UPDATE {{table}} SET magic='$newmagic',gold='$newgold' WHERE id='$castleid' LIMIT 1", "strongholds");
	    $page .= "You have repaired ".$repaircost." worth of damage to the Stronghold's magic.<br>";
		$page .= "This stronghold currently has ".$newgold." gold available.<br>";
		$page .= "The Stronghold currently has <b>".$castlerow["currenthp"]."</b> structural points left out ";
		$page .= "of <b>".$castlerow["maxhp"]."</b> total.<br>";
		$page .= "The current Armor rating is: <b>".$castlerow["armor"]."</b> out of <b>".($castlerow["armorlevel"]*100)."</b>.<br>";
		$page .= "The current Magic level is: <b>".$newmagic."</b> out of <b>".($castlerow["magiclevel"]*100)."</b>.<br>";
		$page .= "The current Weaponry rating is: ".$castlerow["weaponry"]." out of <b>".($castlerow["weaponrylevel"]*100)."</b>.<p>";

    	$page .= "<center><br><a href='strongholds.php'>Back to the Stronghold</a></center>";
    	display($page,"Guild Stronghold");
	}
	if (isset($_POST["weaponry"])) {
		$repaircost = $_POST["repaircost"];
		$castleid = $_POST["castleid"];
		$ruined = $_POST["ruined"];
		$castlegold = $_POST["castlegold"];
		$fullrepair = $_POST["fullrepair"];
		$page = "<table width='100%'><tr><td class='title'>".$userrow["guildname"]." Guild</td></tr></table>";

								if (intval($repaircost) < 0) {
		$page .= "<b>You can't negatively repair your stronghold.<p>";
		$page .= "<center><br><a href='strongholds.php'>Back to your Stronghold</a></center>";
	    display($page,"Repair Stronghold");}
		
		if (intval($repaircost) > $castlegold) {
		$page .= "<b>You do not have ".$repaircost." gold available at this stronghold.<p>";
		$page .= "<center><br><a href='strongholds.php'>Back to the Stronghold</a></center>";
	    display($page,"Guild Stronghold");}

	    if (intval($repaircost) > $fullrepair) {$repaircost = $fullrepair;}
	    $newweaponry = $castlerow["weaponry"] + intval($repaircost/105);
	    $newgold = $castlerow["gold"] - $repaircost;
	    $query = doquery("UPDATE {{table}} SET weaponry='$newweaponry',gold='$newgold' WHERE id='$castleid' LIMIT 1", "strongholds");
	    $page .= "You have repaired ".$repaircost." worth of damage to the Stronghold's weaponry.<br>";
		$page .= "This stronghold currently has ".$newgold." gold available.<br>";
		$page .= "The Stronghold currently has <b>".$castlerow["currenthp"]."</b> structural points left out ";
		$page .= "of <b>".$castlerow["maxhp"]."</b> total.<br>";
		$page .= "The current Armor rating is: <b>".$castlerow["armor"]."</b> out of <b>".($castlerow["armorlevel"]*100)."</b>.<br>";
		$page .= "The current Magic level is: <b>".$castlerow["magic"]."</b> out of <b>".($castlerow["magiclevel"]*100)."</b>.<br>";
		$page .= "The current Weaponry rating is: ".$newweaponry." out of <b>".($castlerow["weaponrylevel"]*100)."</b>.<p>";

    	$page .= "<center><br><a href='strongholds.php'>Back to the Stronghold</a></center>";
    	display($page,"Guild Stronghold");
	}
	if (isset($_POST["cancel"])) {
		header("Location: strongholds.php"); die();
	}
	$page = "<table width='100%'><tr><td class='title'>Repair Stronghold</td></tr></table>";
	$page .= "Stronghold belongs to the ".$castlerow["guildname"]." Guild. Here is a breakdown of all the Strongholds Stats:<p>";
	$page .= "The Stronghold currently has <b>".$castlerow["currenthp"]."</b> structural points left out ";
	$page .= "of <b>".$castlerow["maxhp"]."</b> total.<br>";
	$page .= "The current Armor rating is: <b>".$castlerow["armor"]."</b> out of <b>".($castlerow["armorlevel"]*100)."</b>.<br>";
	$page .= "The current Magic Armor is: <b>".$castlerow["magic"]."</b> out of <b>".($castlerow["magiclevel"]*100)."</b>.<br>";
	$page .= "The current Weaponry rating is: ".$castlerow["weaponry"]." out of <b>".($castlerow["weaponrylevel"]*100)."</b>.<p>";

	$healdamage = $castlerow["maxhp"] - $castlerow["currenthp"];
	if ($castlerow["ruined"] != 0) { $healdamage = 8500; }
	$page .= "This stronghold currently has <b>".$castlerow["gold"]."</b> gold available.<br>";
	$page .= "How much would you like to spend on repairs? Each price is stated within the box.<br>";
	$page .= "<form action='strongholds.php?do=castlestatus' method='post'>";
	$page .= "<input type='text' name='repaircost' value='".$healdamage."' size='6'>";
	$page .= "<input type='hidden' name='castleid' value='".$castlerow["id"]."'>";
	$page .= "<input type='hidden' name='castlegold' value='".$castlerow["gold"]."'>";
	$page .= "<input type='hidden' name='ruined' value='".$castlerow["ruined"]."'>";
	$page .= "<input type='hidden' name='fullrepair' value='".$healdamage."'>";
	$page .= "<input type='submit' name='submit' value='Repair Structure'>";
	$page .= "</form><p>";
	$armordamage = ($castlerow["armorlevel"]*100 - $castlerow["armor"])*100;
	$page .= "<form action='strongholds.php?do=castlestatus' method='post'>";
	$page .= "<input type='text' name='repaircost' value='".$armordamage."' size='6'>";
	$page .= "<input type='hidden' name='castleid' value='".$castlerow["id"]."'>";
	$page .= "<input type='hidden' name='castlegold' value='".$castlerow["gold"]."'>";
	$page .= "<input type='hidden' name='ruined' value='".$castlerow["ruined"]."'>";
	$page .= "<input type='hidden' name='fullrepair' value='".$armordamage."'>";
	$page .= "<input type='submit' name='armor' value='Repair Armor'>";
	$page .= "</form><p>";
	$magicarmordamage = ($castlerow["magiclevel"]*100 - $castlerow["magic"])*90;
	$page .= "<form action='strongholds.php?do=castlestatus' method='post'>";
	$page .= "<input type='text' name='repaircost' value='".$magicarmordamage."' size='6'>";
	$page .= "<input type='hidden' name='castleid' value='".$castlerow["id"]."'>";
	$page .= "<input type='hidden' name='castlegold' value='".$castlerow["gold"]."'>";
	$page .= "<input type='hidden' name='ruined' value='".$castlerow["ruined"]."'>";
	$page .= "<input type='hidden' name='fullrepair' value='".$magicarmordamage."'>";
	$page .= "<input type='submit' name='magicarmor' value='Repair Magic'>";
	$page .= "</form><p>";
	$weaponrydamage = ($castlerow["weaponrylevel"]*100 - $castlerow["weaponry"])*105;
	$page .= "<form action='strongholds.php?do=castlestatus' method='post'>";
	$page .= "<input type='text' name='repaircost' value='".$weaponrydamage."' size='5'>";
	$page .= "<input type='hidden' name='castleid' value='".$castlerow["id"]."'>";
	$page .= "<input type='hidden' name='castlegold' value='".$castlerow["gold"]."'>";
	$page .= "<input type='hidden' name='ruined' value='".$castlerow["ruined"]."'>";
	$page .= "<input type='hidden' name='fullrepair' value='".$weaponrydamage."'>";
	$page .= "<input type='submit' name='weaponry' value='Repair Weaponry'><br>";
	$page .= "<input type='submit' name='cancel' value='cancel'>";
	$page .= "</form><p>";
    $page .= "<center><br><a href='strongholds.php'>Back to the Stronghold</a></center>";
    display($page,"Repair Stronghold");
}


function doarena() {
	global $userrow;
	$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "strongholds");
	$castlerow = mysql_fetch_array($castlequery);
	if ($castlerow["ruined"] != 0) {
		$page = "<table width='100%'><tr><td class='title'>Ruined Stronghold.</td></tr></table>";
		$page .= "<br><b>This Stronghold has been ruined!!</b><br><br>";
		$page .= "You may return to the <a href='strongholds.php'>Stronghold</a>, ";
		$page .= "or leave and <a href='index.php'>continue exploring</a> using the compass images to the right.";
		display($page, "Ruined Stronghold");

	}
	header("Location: arena.php"); die();
}

function dolistmembers ($filter) {
	global $userrow;

if ($userrow["guildrank"] < 100) { header("Location: strongholds.php"); die();}

	if (!isset($filter)) { $filter = "";}

	$page = "<table width='100%'><tr><td class='title'>Guild Members</td></tr></table>";
	$page .= "<center><p>To view the list of current members in this guild, click on a letter to filter them. Click on the members names to edit their rank, or to boot them from the Guild.<p>";
	$page .= "[ <a href='strongholds.php?do=users'>*</a> ";
	$page .= " <a href='strongholds.php?do=users:A'>A</a> ";
	$page .= " <a href='strongholds.php?do=users:B'>B</a> ";
	$page .= " <a href='strongholds.php?do=users:C'>C</a> ";
	$page .= " <a href='strongholds.php?do=users:D'>D</a> ";
	$page .= " <a href='strongholds.php?do=users:E'>E</a> ";
	$page .= " <a href='strongholds.php?do=users:F'>F</a> ";
	$page .= " <a href='strongholds.php?do=users:G'>G</a> ";
	$page .= " <a href='strongholds.php?do=users:H'>H</a> ";
	$page .= " <a href='strongholds.php?do=users:I'>I</a> ";
	$page .= " <a href='strongholds.php?do=users:J'>J</a> ";
	$page .= " <a href='strongholds.php?do=users:K'>K</a> ";
	$page .= " <a href='strongholds.php?do=users:L'>L</a> ";
	$page .= " <a href='strongholds.php?do=users:M'>M</a> ";
	$page .= " <a href='strongholds.php?do=users:N'>N</a> ";
	$page .= " <a href='strongholds.php?do=users:O'>O</a> ";
	$page .= " <a href='strongholds.php?do=users:P'>P</a> ";
	$page .= " <a href='strongholds.php?do=users:Q'>Q</a> ";
	$page .= " <a href='strongholds.php?do=users:R'>R</a> ";
	$page .= " <a href='strongholds.php?do=users:S'>S</a> ";
	$page .= " <a href='strongholds.php?do=users:T'>T</a> ";
	$page .= " <a href='strongholds.php?do=users:U'>U</a> ";
	$page .= " <a href='strongholds.php?do=users:V'>V</a> ";
	$page .= " <a href='strongholds.php?do=users:W'>W</a> ";
	$page .= " <a href='strongholds.php?do=users:X'>X</a> ";
	$page .= " <a href='strongholds.php?do=users:Y'>Y</a> ";
	$page .= " <a href='strongholds.php?do=users:Z'>Z</a> ]<br></center>";
	$charquery = doquery("SELECT * FROM {{table}} WHERE charname LIKE '".$filter."%' AND guildname='".$userrow["guildname"]."' ORDER by charname", "users");
$updatequery = doquery("UPDATE {{table}} SET location='Guild Members List' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	$page .= "<center>There are <b>".mysql_num_rows($charquery)."</b> characters in your Guild starting: '".$filter."'.</center> ";
	$page .= "<center><table width='90%' style='border: solid 1px black' cellspacing='0' cellpadding='0'>";
	$page .= "<center><tr><td colspan=\"8\" bgcolor=\"#ffffff\"><center><b>Dragons Kingdom Characters</b></center></td></tr>";
	$page .= "<tr><td><b>Name</b></td><td><b>Last Login</b></td><td><b>Dragon Scales</b></td><td><b>Level</b></td><td><b>Guild</b></td></tr>";
	$count = 2;
	$rankquery = doquery("SELECT * FROM {{table}} WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
	$rankrow = mysql_fetch_array($rankquery);
	while ($charrow = mysql_fetch_array($charquery)) {

		if ($charrow["guildrank"] >= "200"){ $rank = "<b>*Founder</b>";}
		if ($charrow["guildrank"] <= "150"){ $rank = "<b>*Leader</b>";}
		if ($charrow["guildrank"] <= "100"){ $rank = "<b>*Co-leader</b>";}
		if ($charrow["guildrank"] <= "90"){ $rank = $rankrow["rank6name"];}
		if ($charrow["guildrank"] <= "80"){ $rank = $rankrow["rank5name"];}
		if ($charrow["guildrank"] <= "60"){ $rank = $rankrow["rank4name"];}
		if ($charrow["guildrank"] <= "30"){ $rank = $rankrow["rank3name"];}
		if ($charrow["guildrank"] <= "10"){ $rank = $rankrow["rank2name"];}
		if ($charrow["guildrank"] <= "2"){ $rank = $rankrow["rank1name"];}

		if ($count == 1) { $color = "bgcolor='#ffffff'"; $count = 2; }
		else { $color = "bgcolor='#eeeeee'"; $count = 1;}
		$page .= "<tr><td ".$color." width='15%'>";
		if ($userrow["guildrank"] >= 100) {
		$page .= "<a href='strongholds.php?do=editmember:".$charrow["id"]."'>".$charrow["charname"]."</a>";}
		else {
		$page .= $charrow["charname"];}
		$page .= "</td>";
		$page .= "<td ".$color." width='25%'>".$charrow["onlinetime"]."</td>";
		$page .= "<td ".$color." width='10%'>".$charrow["dscales"]."</td>";
		$page .= "<td ".$color." width='5%'>".$charrow["level"]."</td>";
		$page .= "<td ".$color." width='20%'>".$rank."</td>";
	  	$page .= "</tr>";
	}
	$page .= "</table></center>";
	$page .= "<center><br><a href='strongholds.php'>Return to the Stronghold</a></center>";

	display($page, "Characters of Dragons Kingdom");

}


function doeditmembers($id) {
	global $userrow;
if ($userrow["guildrank"] < 100) { header("Location: strongholds.php"); die();}
    if (!isset($_POST["submit"])) {

$updatequery = doquery("UPDATE {{table}} SET location='Editing Members' WHERE id='".$userrow["id"]."' LIMIT 1", "users");


	$charquery = doquery("SELECT * FROM {{table}} WHERE id='".$id."' LIMIT 1", "users");
	$charrow = mysql_fetch_array($charquery);
	$rankquery = doquery("SELECT * FROM {{table}} WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
	$rankrow = mysql_fetch_array($rankquery);
	$page = "<table width='100%'><tr><td class='title'>Guild Members</td></tr></table>";
	$page .= "<center><h3>Details for ".$charrow["charname"].":</h3><p>";
	$page .= "<table>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Character Name:</b></td><td bgcolor='#eeeeee'>".$charrow["charname"]."</td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Level:</b></td><td bgcolor='#eeeeee'>".$charrow["level"]."</td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Title:</b></td><td bgcolor='#eeeeee'>".$charrow["title"]."</td></tr>";
        $page .= "<tr><td bgcolor='#eeeeee'><b>Last Online:</b></td><td bgcolor='#eeeeee'>".$charrow["onlinetime"]."</td></tr>";

	$page .= "<tr><td bgcolor='#ffffff'><b>Guild Rank:</b></td><td bgcolor='#ffffff'>";
	$page .= "<form action='strongholds.php?do=editmember:".$id."' method='POST'>";
	$page .= "<select name='userrank'>";
	$page .= "<option value='0'>".$rankrow["rank1name"]."</option>";
	$page .= "<option value='10'>".$rankrow["rank2name"]."</option>";
	$page .= "<option value='30'>".$rankrow["rank3name"]."</option>";
	$page .= "<option value='60'>".$rankrow["rank4name"]."</option>";
	$page .= "<option value='80'>".$rankrow["rank5name"]."</option>";
	$page .= "<option value='90'>".$rankrow["rank6name"]."</option>";
	$page .= "<option value='100'>Co-Leader</option>";
	if ($userrow["guildrank"] >= 200) {
	$page .= "<option value='150'>Leader</option>";}
	$page .= "</select>";
	$page .= "<input type='hidden' name='charID' value='".$charrow["id"]."'>";
	$page .= "<input type='hidden' name='charname' value='".$charrow["charname"]."'>";
	$page .= "<input type='submit' name='submit' value='Submit'></form>";
	$page .= "</td></tr>";

	$page .= "<tr><td bgcolor='#ffffff'><b>Gold:</b></td><td bgcolor='#ffffff'>".$charrow["gold"]."</td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'><b>Bank/Vault Gold:</b></td><td bgcolor='#ffffff'>".$charrow["bank"]."</td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Dragon Scales:</b></td><td bgcolor='#eeeeee'>".$charrow["dscales"]."</td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Hit Points:</b></td><td bgcolor='#eeeeee'>".$charrow["currenthp"]."/".$charrow["maxhp"]."</td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'><b>Magic Points:</b></td><td bgcolor='#ffffff'>".$charrow["currentmp"]."/".$charrow["maxmp"]."</td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Travel Points:</b></td><td bgcolor='#eeeeee'>".$charrow["currenttp"]."/".$charrow["maxtp"]."</td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Ability Points:</b></td><td bgcolor='#eeeeee'>".$charrow["currentap"]."/".$charrow["maxap"]."</td></tr>";

	$page .= "<tr><td bgcolor='#ffffff'><b>Wisdom Level:</b></td><td bgcolor='#ffffff'>".$charrow["skill1level"]."</td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'><b>Stone Skin Level:</b></td><td bgcolor='#ffffff'>".$charrow["skill2level"]."</td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'><b>Monks Mind Level:</b></td><td bgcolor='#ffffff'>".$charrow["skill3level"]."</td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'><b>Fortune Level:</b></td><td bgcolor='#ffffff'>".$charrow["skill4level"]."</td></tr>";

	$page .= "<tr><td bgcolor='#ffffff'><b>Strength:</b></td><td bgcolor='#ffffff'>".$charrow["strength"]."</td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Dexterity:</b></td><td bgcolor='#eeeeee'>".$charrow["dexterity"]."</td></tr>";
        $page .= "<tr><td bgcolor='#eeeeee'><b>Attack Power:</b></td><td bgcolor='#eeeeee'>".$charrow["attackpower"]."</td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'><b>Defense Power:</b></td><td bgcolor='#ffffff'>".$charrow["defensepower"]."</td></tr>";

	$page .= "<tr><td bgcolor='#eeeeee'><b>Weapon:</b></td><td bgcolor='#eeeeee'>".$charrow["weaponname"]."</td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'><b>Armor:</b></td><td bgcolor='#ffffff'>".$charrow["armorname"]."</td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Shield:</b></td><td bgcolor='#eeeeee'>".$charrow["shieldname"]."<br></td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Amulet:</b></td><td bgcolor='#eeeeee'>".$charrow["amulet"]."</td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'><b>Ring:</b></td><td bgcolor='#ffffff'>".$charrow["ring"]."</td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Tavern Drink:</b></td><td bgcolor='#eeeeee'>".$charrow["drink"]."<br></td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Current Potion:</b></td><td bgcolor='#eeeeee'>".$charrow["potion"]."<br></td></tr>";

	$page .= "<tr><td bgcolor='#eeeeee'><b>Slot 1:</b></td><td bgcolor='#eeeeee'>".$charrow["slot1name"]."<br></td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Slot 2:</b></td><td bgcolor='#eeeeee'>".$charrow["slot2name"]."<br></td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Slot 3:</b></td><td bgcolor='#eeeeee'>".$charrow["slot3name"]."<br></td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Slot 4:</b></td><td bgcolor='#eeeeee'>".$charrow["slot4name"]."<br></td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Slot 5:</b></td><td bgcolor='#eeeeee'>".$charrow["slot5name"]."<br></td></tr>";


	$page .= "<tr><td bgcolor='#eeeeee'><b>Duel Wins:</b></td><td bgcolor='#eeeeee'>".$charrow["numbattlewon"]."<br></td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'><b>Duel Loss's:</b></td><td bgcolor='#ffffff'>".$charrow["numbattlelost"]."<br></td></tr></center>";

	$page .= "</table>";
	$page .= "<center><form action='strongholds.php?do=bootmember:".$id."' method='POST'>";
	$page .= "<input type='hidden' name='charID' value='".$charrow["id"]."'>";
	$page .= "<input type='hidden' name='charname' value='".$charrow["charname"]."'>";
	$page .= "<input type='submit' name='submit' value='Boot from Guild'></form>";
	$page .= "<center><br><a href='strongholds.php'>Return to the Stronghold</a></center>";
	display($page, "View Character");
// ------------------------------------------------
    } else {  //if submitting changes.
		$id = $_POST["charID"];
		$rank = $_POST["userrank"];
		$charname = $_POST["charname"];
		$updatequery = doquery("UPDATE {{table}} SET guildrank='$rank' WHERE id='$id' LIMIT 1", "users");
		$page = "<table width='100%'><tr><td class='title'>Guild Members</td></tr></table>";
		$page .= "You have successfully modified ".$charname.".<p>";
    	$page .= "<center><br><a href='strongholds.php'>Return to the Stronghold</a></center>";
    	display($page,"Edit Guild Member");
	}

}

function doattack() {
	global $userrow;

$updatequery = doquery("UPDATE {{table}} SET location='Attacking a Stronghold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");


	$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "strongholds");
	$castlerow = mysql_fetch_array($castlequery);
	if ($castlerow["ruined"] != 0) {
		$page = "<table width='100%'><tr><td class='title'>Ruined Stronghold.</td></tr></table>";
		$page .= "<br><b>This Stronghold has been ruined!!</b><br><br>";
		$page .= "You may return to the <a href='strongholds.php'>Stronghold</a>, ";
		$page .= "or leave and <a href='index.php'>continue exploring</a>.";
		display($page, "Ruined Stronghold");

	}
	if ($userrow["currentap"] <= 4) {
		$page = "<table width='100%'><tr><td class='title'>Not Enough Ability Points!</td></tr></table>";
		$page .= "<br><b>You do not have enough AP to perform this action.</b><br><br>";
		$page .= "You must have at least 5 AP in order to attack another stronghold. ";
		$page .= "Your AP is refilled for a cost of 50 Dragon Scales.<br>";
		$page .= "You may return to the <a href='index.php'>Stronghold</a>, ";
		$page .= "or leave and <a href='index.php'>continue exploring</a>.";
		display($page, "Not Enough AP");
	}

	$cquery = doquery("SELECT * from {{table}} WHERE ruined='0' AND guildname!='".$userrow["guildname"]."' ORDER BY guildname", "strongholds");
	$page = "<table width='100%'><tr><td class='title'>Attack another stronghold</td></tr></table>";
	$page .= "The following is a list of Rival Strongholds.  Please choose an enemy ";
	$page .= "stronghold, or click below to return to the Stronghold.<br>";
	$page .= "<table><tr><td bgcolor='#ffffff' width='20%'><b>Stronghold</b></td>";
	$page .= "<td bgcolor='#ffffff'><b>Location</b></td></tr>";
	$count = 0;
	$clist = "0";
	if (mysql_num_rows($cquery) <= 0) {
		$page .= "<td bgcolor='#eeeeee' colspan='2'>No Strongholds to attack!<br>";
		$page .= "Either there are no strongholds belonging to rival Guilds, or those that ";
		$page .= "exist are already destroyed and simply large piles of rubble.</td></tr></table>";
	    $page .= "<center><br><a href='strongholds.php'>Return to the Stronghold</a></center>";
    	display($page,"Guild Stronghold");
    }
	while ($crow=mysql_fetch_array($cquery)) {
		$clist .= ",".$crow["id"];
		$count += 1;
		if ($crow["latitude"] < 0) {$dlat='S';} else {$dlat='N';}
		if ($crow["longitude"] < 0) {$dlon='W';} else {$dlon='E';}
		if (($count/2) != intval($count/2)) {$colour = "bgcolor='#eeeeee'";} else {$colour="bgcolor='#ffffff'";}
		$page .= "<td ".$colour." width='20%'><a href='strongholds.php?do=attack2:".$count."'>";
		$page .= $count.") ".$crow["guildname"]."</td><td ".$colour.">";
		$page .= abs($crow["latitude"]).$dlat.", ".abs($crow["longitude"]).$dlon."</a></td></tr>";
	}
	$q = doquery("UPDATE {{table}} SET templist='$clist' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	$page .= "</table>";
	$page .= "<p>Your stronghold currently has the following units available to attack:<br>";
	$page .= "<b>".$castlerow["snails"]. "</b> Vipers.  (Vipers destroy Magic)<br>";
	$page .= "<b>".$castlerow["kelplings"]. "</b> Golems.  (Golems damage Weaponry)<br>";
	$page .= "<b>".$castlerow["minnows"]. "</b> Gargoyles.  (Gargoyles weaken Armor)<p>";

    $page .= "<center><br><a href='strongholds.php'>Return to the Stronghold</a></center>";
    display($page,"Guild Stronghold");
}


function doattack2($place) {

	global $userrow;
	$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "strongholds");
	$castlerow = mysql_fetch_array($castlequery);
	if ($castlerow["ruined"] != 0) {
		$page = "<table width='100%'><tr><td class='title'>Ruined Stronghold.</td></tr></table>";
		$page .= "<br><b>This Stronghold has been ruined!!</b><br><br>";
		$page .= "You may return to the <a href='index.php'>Stronghold</a>, ";
		$page .= "or leave and <a href='index.php'>continue exploring</a>.";
		display($page, "Ruined Stronghold");
	}
	if ($userrow["currentap"] <= 4) {
		$page = "<table width='100%'><tr><td class='title'>Not Enough Ability Points!</td></tr></table>";
		$page .= "<br><b>You do not have enough AP to perform this action.</b><br><br>";
		$page .= "You must have at least 5 AP in order to attack another stronghold. ";
		$page .= "Your AP is refilled automatically every day.<br>";
		$page .= "You may return to the <a href='index.php'>Stronghold</a>, ";
		$page .= "or leave and <a href='index.php'>continue exploring</a>.";
		display($page, "Not Enough AP");
	}

	$page = "<table width='100%'><tr><td class='title'>Attack Strongholds.</td></tr></table>";
	$templist = explode(",",$userrow["templist"]);
	$squery = doquery("SELECT * FROM {{table}} WHERE id='".$templist["$place"]."' LIMIT 1", "strongholds");
	if (mysql_num_rows($squery) <= 0){
		$page .= "<b>Invalid Stronghold value!</b><br>";
		$page .= "You have attempted to select an invalid stronghold or this stronghold has been ruined already.";
		$page .= " Please return to the Stronghold and try again. You have a limit of how many attacks you can do in a set amount of time.<br>";
		$page .= "<center><br><a href='strongholds.php'>Return to the Stronghold</a></center>";
		display($page,"The Guild Attacks");
	}
	$srow = mysql_fetch_array($squery);
	$snails1 = $castlerow["snails"];
	$armor2 = $srow["armor"];
	$kelplings1 = $castlerow["kelplings"];
	$magicarmor2 = $srow["magic"];
	$minnows1 = $castlerow["minnows"];
	$weaponry2 = $srow["weaponry"];

	$lostsnails = intval(rand($snails1*.5, $snails1*.75) - rand($magicarmor2*.25,$magicarmor*.5));
	if ($lostsnails <=0 ) {$lostsnails = rand(1,$srow["level"]*5);}
	if ($lostsnails >= $snails1) {$lostsnails = $snails1;}
	$newsnails = $snails1 - $lostsnails;
	$magicdamage= intval(rand($lostsnails*.5,$lostsnails));
	if ($magicdamage <= 0) {$magicdamage = 0;}
	$newmagicarmor = $srow["magic"] - $magicdamage;

	$lostkelps = intval(rand($kelplings1*.5, $kelplings1*.75) - rand($weaponry2*.25,$weaponry*.5));
	if ($lostkelps <=0 ) {$lostkelps = rand(1,$srow["level"]*5);}
	if ($lostkelps >= $kelplings1) {$lostkelps = $kelplings1;}
	$newkelps = $kelplings1 - $lostkelps;
	$weapondamage= intval(rand($lostkelps*.5,$lostkelps));
	if ($weapondamage <= 0) {$weapondamage = 0;}
	$newweaponry = $srow["weaponry"] - $weapondamage;

	$lostminnows = intval(rand($minnows1*.5, $minnows1*.75) - rand($armor2*.25,$armor*.5));
	if ($lostminnows <=0 ) {$lostminnows = rand(1,$srow["level"]*5);}
	if ($lostminnows >= $minnows1) {$lostminnows = $minnows1;}
	$newminnows = $minnows1 - $lostminnows;
	$armordamage= intval(rand($lostminnows*.5,$lostminnows));
	if ($armordamage <= 0) {$armordamage = 0;}
	$newarmor = $srow["armor"] - $armordamage;

	$tempdamage = intval($armordamage*1.5 + $magicdamage*1.25 + $weapondamage);
	$castledamage = intval(rand($tempdamage*.75,$tempdamage));
	$ruined = 0; $castlelevel = $srow["level"];
	$newhp = $srow["currenthp"] - $castledamage;

	$newap = $userrow["currentap"] - 5;
	$uquery = doquery("UPDATE {{table}} SET templist='0',currentap='$newap' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	$page .= "<p><center><img src=\"images/troops.gif\" border=\"0\" alt=\"Troops\" /></a></center><p>You have attacked a Stronghold belonging to the ".$srow["guildname"]." guild. ";
	$page .= "A summary of the battle is below.<p>";

	$page .= "You have lost <b>".$lostsnails."</b> Vipers during the battle.<br>";
	$page .= "You have lost <b>".$lostkelps."</b> Golems during the battle.<br>";
	$page .= "You have lost <b>".$lostminnows."</b> Gargoyles during the battle.<p>";

	$page .= "You have dealt <b>".$magicdamage."</b> damage to the Stronghold's magic defenses.<br>";
	$page .= "You have dealt <b>".$armordamage."</b> damage to the Stronghold's physical defenses.<br>";
	$page .= "You have dealt <b>".$weapondamage."</b> damage to the Stronghold's weaponry.<p>";

	$page .= "You reduced the enemy stronghold's Hit Points by <b>".$castledamage."</b>! <i>(".($srow["currenthp"]-$castledamage)." left)</i><p>";

	$page .= "You have <b>".$newsnails."</b> Vipers left in this stronghold.<br>";
	$page .= "You have <b>".$newkelps."</b> Golems left at this stronghold.<br>";
	$page .= "You have <b>".$newminnows."</b> Gargoyles left at this stronghold.<p>";
	
			    $gquery = doquery("SELECT id FROM {{table}} WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
	$grow = mysql_fetch_array($gquery);
	
	  					$logdate = "cycle_".date(W);
						$logpath = "./logs/guild".$grow["id"]."/";
						if (!is_dir($logpath)) {
					 	@mkdir($logpath,0777);
				     	@chmod($logpath,0777);
		    	}
				$logfile =  $logpath.$logdate.".log";
				$logcomments = "--".$userrow["charname"]." Attacked another strongholed using Troops and lost <b>".$lostsnails." Vipers, ".$lostkelps." Golems and ".$lostminnows." Gargoyles</b>";
				$logcomments .= " on ".date("r").". <br>\r\n";
				$fp = fopen("$logfile", "a");
				fwrite($fp, "$logcomments");
				fclose ($fp);

		$logdate = "cycle_".date(W);
		$logpath = "./logs/guild".$srow["guildid"]."/";
		if (!is_dir($logpath)) {
	 	@mkdir($logpath,0777);
     	@chmod($logpath,0777);
    	}
		$logfile =  $logpath.$logdate.".log";
		$logcomments = "*<b>".$userrow["charname"]."</b> attacked your Stronghold with their troops at ";
		$logcomments .= $userrow["latitude"].",".$userrow["longitude"]." on ".date("r");
		$logcomments .= ". <br>\r\n";
		$fp = fopen("$logfile", "a");
		fwrite($fp, "$logcomments");
		fclose ($fp);

	if ($newhp <= 0) {
		$ruined = 1;
		$newhp = 0;
	   	$page .= "<h3>This stronghold has been ruined!</h3>";
	   	$page .= "The level of this stronghold has been reduced by 1.<p>";
	   	$castlelevel -= 1;
	   	if ($castlelevel <= 0) {$castlelevel = 1;}
	   	$ruined = 1;
		$logdate = "cycle_".date(W);
		$logpath = "./logs/guild".$srow["guildid"]."/";
		if (!is_dir($logpath)) {
	 	@mkdir($logpath,0777);
     	@chmod($logpath,0777);
    	}
		$logfile =  $logpath.$logdate.".log";
		$logcomments = "***<b>".$userrow["charname"]."</b> attacked your Stronghold with their troops at ";
		$logcomments .= $userrow["latitude"].",".$userrow["longitude"]." on ".date("r");
		$logcomments .= ". The Stronghold was Destroyed!<br>\r\n";
		$fp = fopen("$logfile", "a");
		fwrite($fp, "$logcomments");
		fclose ($fp);
	}
	$newxp = $castlerow["experience"] + intval(rand(1,sqrt($castledamage)));
	$newlevel = $castlerow["level"];
	if ($newxp >= 200) {$newxp = 0; $newlevel = $newlevel+1;
	 $page .= "<b>The level of your Stronghold has increased by one!  The current level is: ".$newlevel."!</b><p>";
	}
	if ($newlevel >= 10) {$newlevel = 10;}
	$pquery = doquery("UPDATE {{table}} SET experience='$newxp',level='$newlevel',snails='$newsnails',kelplings='$newkelps',minnows='$newminnows' WHERE id='".$castlerow["id"]."' LIMIT 1", "strongholds");
	$cquery = doquery("UPDATE {{table}} SET currenthp='$newhp',level='$castlelevel',ruined='$ruined',
		armor='$newarmor',magic='$newmagicarmor',weaponry='$newweaponry' WHERE id='".$srow["id"]."' LIMIT 1", "strongholds");

	$page .= "<center><br><a href='strongholds.php'>Return to the Stronghold</a></center>";

	display($page,"The Guild Attacks");
}



function dobuild() {
	global $userrow;
if ($userrow["guildrank"] < 100) { header("Location: strongholds.php"); die();}
$updatequery = doquery("UPDATE {{table}} SET location='Building a Stronghold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");


	$castlequery = doquery("SELECT * FROM {{table}} WHERE guildname='".$userrow["guildname"]."' ", "strongholds");
	$castlerow = mysql_fetch_array($castlequery);
	$gquery = doquery("SELECT * FROM {{table}} WHERE name='".$userrow["guildname"]."' ", "guilds");

	if (mysql_num_rows($castlequery) >= 7) {
		$page = "<table width='100%'><tr><td class='title'>Build a new Stronghold</td></tr></table>";
		$page .= "<p>You already have 7 Strongholds!<br>";
		$page .= "In order to keep enough room available for other Guilds, you are limited to ";
		$page .= "seven (7) strongholds per Guild.  If you would like to build more strongholds, ";
		$page .= "try forming your own Guild.";
		$page .= "<p>";
	    $page .= "<center><br><a href='strongholds.php'>Return to the Stronghold</a></center>";
		display($page, "Building a Stronghold.");
	}
	$grow = mysql_fetch_array($gquery);
	if ($grow["dscales"] <= 2499) {
		$page = "<table width='100%'><tr><td class='title'>Build a new Stronghold</td></tr></table>";
		$page .= "<p>There are not enough Dragon Scales available!<br>";
		$page .= "You need to have at least 2500 Dragon Scales to build a new stronghold.<p>";
		$page .= "There are currently <b>".$grow["dscales"]."</b> Dragon Scales available here in your guild.<p>";
		$page .= "To increase the number of Dragon Scales in the stronghold vaults, members must ";
		$page .= "use the Guild Temple or use the Pet Arena to train and battle their captured ";
		$page .= "monsters.  The Temples will randomly increase the available Dragon Scales, and the Arena ";
		$page .= "directly adds the training/battle fees to the Guild totals.<br>";
    		$page .= "<center><br><a href='strongholds.php'>Return to the Stronghold</a></center>";
		display($page, "Building a Stronghold.");
	}
	if ($castlerow["ruined"] != 0) {
		$page = "<table width='100%'><tr><td class='title'>Ruined Stronghold.</td></tr></table>";
		$page .= "<br><b>This Stronghold has been ruined!!</b><br><br>";
		$page .= "You may return to the <a href='strongholds.php'>Stronghold</a>, ";
		$page .= "or leave and <a href='index.php'>continue exploring</a>.";
		display($page, "Ruined Stronghold");

	}
	if ($userrow["currentap"] <= 24) {
		$page = "<table width='100%'><tr><td class='title'>Not Enough Ability Points!</td></tr></table>";
		$page .= "<br><b>You do not have enough AP to perform this action.</b><br><br>";
		$page .= "You must have at least 25 AP in order to build another stronghold. ";
		$page .= "Your AP is refilled for a price of 50 Dragon Scales.<br>";
		$page .= "<p>You may return to the <a href='strongholds.php'>Stronghold</a>, ";
		$page .= "or leave and <a href='index.php'>continue exploring</a>.";
		display($page, "Not Enough AP");
	}

    if (isset($_POST["submit"])) {

		$page = "<table width='100%'><tr><td class='title'>Build a new Stronghold</td></tr></table>";
		$page .= "You currently have ".mysql_num_rows($castlequery)." strongholds built.<p>";
		$page .= "You may place a Stronghold anywhere on the map with the following restrictions:";
		$page .= "<ul><li>The Stronghold can not be within 5 steps of any town.";
		$page .= "<li>The stronghold can not be within 25 steps of any other Stronghold.";
		$page .= "<li>The stronghold must at least 100 steps from the edge of the map.<br>";
		$page .= "<i>Maximum 500 latitude or longitude. Too dangerous to go above 500.</i></ul>";
		$page .= "<table width='100%' border='0'>";
		$page .= "<tr><td><form action='strongholds.php?do=build2' method='POST'>";
		$page .= "<b>Enter coordinates of new Stronghold below:</b><hr></td></tr>";
		$page .= "<tr><td>Latitude: (25-500)<br>";
		$page .= "<input type='text' length='5' name='latitude'><br>";
		$page .= "<input type='radio' value='1' name='d_lat'>North";
		$page .= "<input type='radio' value='-1' name='d_lat'>South</td></tr>";
		$page .= "<tr><td><hr></td></tr>";
		$page .= "<tr><td>Longitude: (25-500)<br>";
		$page .= "<input type='text' length='5' name='longitude'><br>";
		$page .= "<input type='radio' value='1' name='d_lon'>East";
		$page .= "<input type='radio' value='-1' name='d_lon'>West</td></tr>";
		$page .= "<tr><td><hr></td></tr>";
		$page .= "<tr><td><input type='submit' name='submit' value='Build a Stronghold'></form>";
		$page .= "</td><td><form action='strongholds.php'>";
		$page .= "<input type='submit' name='cancel' value='Cancel'></form></td></tr></table>";

		$page .= "<center><br><a href='strongholds.php'>Return to the Stronghold</a></center>";
    } elseif (isset($_POST["cancel"])) {
        header("Location: strongholds.php"); die();

    } else {
	$page = "<table width='100%'><tr><td class='title'>Build a new Stronghold</td></tr></table>";
	$page .= "<p>It costs 2500 Dragon Scales to construct a new, level 2 stronghold.<br>";
	$page .= "You currently have ".$grow["dscales"]." Dragon Scales available in your guild<br>";
	$page .= "Do you wish to construct a new stronghold?<p>";
	$page .= "<form action='strongholds.php?do=build' method='post'>\n";
	$page .= "<input type='submit' name='submit' value='Yes' />  \t";
	$page .= "<input type='submit' name='cancel' value='No' />\n";
	$page .= "</form>\n";
	$page .= "<center><br><a href='strongholds.php'>Return to the Stronghold</a></center>";


    }
    display($page,"Guild Stronghold");
}

function dobuild2() {
	global $userrow;
if ($userrow["guildrank"] < 100) { header("Location: strongholds.php"); die();}
	if ($userrow["currentap"] <= 24) {
		$page = "<table width='100%'><tr><td class='title'>Not Enough Ability Points!</td></tr></table>";
		$page .= "<br><b>You do not have enough AP to perform this action.</b><br><br>";
		$page .= "You must have at least 25 AP in order to build a stronghold. ";
		$page .= "Your AP is refilled for a price of 50 Dragon Scales.<br>";
		$page .= "<p>You may return to the <a href='strongholds.php'>Stronghold</a>, ";
		$page .= "or leave and <a href='index.php'>continue exploring</a>.";
		display($page, "Not Enough AP");
	}

	$page = "<table width='100%'><tr><td class='title'>Building a Stronghold.</td></tr></table>";
    if (!isset($_POST["submit"])) {
	$page .= "Invalid command!<p>";
	$page .= "<center><br><a href='strongholds.php'>Return to the Stronghold</a></center>";
    display($page,"Guild Courtyard");
    }
    $page = "<table width='100%'><tr><td class='title'>Building a Stronghold.</td></tr></table>";
    $lat = $_POST["latitude"];
    $lon = $_POST["longitude"];
	unset($errors);
	if (($lat > 500) || ($lat < 25)) {
		$errors .= "There are the following errors while constructing your Stronghold, please go back and try again:<p><b>Invalid Latitude!</b><br>Latitude must be between 25 and 500<p>";
	}
	if (($lon > 500) || ($lon < 25)) {
		$errors .= "<b>Invalid Longitude!</b><br>Longitude must be between 25 and 500<p>";
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
		$errors .= "<b>There are other Strongholds nearby!</b><br>";
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
		$page .= "<center><br><a href='strongholds.php'>Back to the Stronghold</a></center>";
		display($page,"Building a Stronghold");
		die();
	}
	$guildquery = doquery("SELECT id,dscales,name FROM {{table}} WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
	$grow = mysql_fetch_array($guildquery);
	$new = doquery("INSERT INTO {{table}} SET latitude='$lat',longitude='$lon',guildname='".$grow["name"]."',guildid='".$grow["id"]."',founder='".$userrow["charname"]."',level='2' ", "strongholds");
	$newdscales = $grow["dscales"] - 2500;
	$g = doquery("UPDATE {{table}} SET dscales='$newdscales' WHERE id='".$grow["id"]."' LIMIT 1", "guilds");

	$page .= "You have successfully constructed a Guild Stronghold at ";
		$direction = 'E';
	if ($lat < 0) {$direction = 'W';}
	$page .= " ".abs($lat).$direction .", ".abs($lon);
		$direction = 'N';
	if ($lon < 0) {$direction='S';}
	$page .= $direction.".";
	$newap = $userrow["currentap"] - 25;
	$pquery = doquery("UPDATE {{table}} SET currentap='$newap' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	$page .= "<center><br><a href='strongholds.php'>Back to the Stronghold</a></center>";
	display($page,"Building a Stronghold");
}


function dotownback() {
	global $userrow;

	if (isset($_POST["totown"])) {

    $newap = $userrow["currentap"] - 3;
	$pu = doquery("UPDATE {{table}} SET currentap='$newap', currentaction='In Town', latitude='0', longitude='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	header("Location: index.php"); die();
	}


	if ($userrow["currentap"] <= 2) {
		$page = "<table width='100%'><tr><td class='title'>Not Enough Ability Points!</td></tr></table>";
		$page .= "<br><b>You do not have enough AP to perform this action.</b><br><br>";
		$page .= "You must have at least 3 AP in order to do this action. ";
		$page .= "Your AP is refilled for a price of 50 Dragon Scales.<br>";
		$page .= "<p>You may return to the <a href='strongholds.php'>Stronghold</a>, ";
		$page .= "or leave and <a href='index.php'>continue exploring</a>.";
		display($page, "Not Enough AP");
	}

	$page = "<table width='100%'><tr><td class='title'>Town Portal</td></tr></table>";
	$page .= "<p>You stand before a small portal which leads back to the Kingdom of Valour for a price of 3 AP.<p> ";
	$page .= "<form action='strongholds.php?do=townportal' method='POST'>";
	$page .= "<input type='submit' name='totown' value='Enter Portal'>";
	$page .= "</form><p>";
	$page .= "You may return to the  <a href='strongholds.php'>Stronghold</a>, ";
	$page .= "or leave and <a href='index.php?do=move:0'>continue exploring</a>.";
	$pquery = doquery("UPDATE {{table}} SET location='Town Portal' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	display($page, "Town Portal");
}


function dovault() {
    global $userrow;

    if (isset($_POST["submit"])) {
		if ($_POST["deposit"] != '') {
		  $deposit = abs(intval($_POST["deposit"]));
		  if ($userrow["gold"] < $deposit) {
		    $page = "<table width=\"100%\"><tr><td class=\"title\">Guild Vault</td></tr></table>";
		  	$page .= "<br><b>You do not have ".$deposit." gold to deposit!</b><br><br>";
	  		$page .= "You may return to <a href='strongholds.php?do=vault'>the vaults</a>, go back ";
	  		$page .= "to the <a href='index.php'>Guild Courtyard</a>, ";
	  		$page .= "or leave and <a href=\"index.php\">continue exploring</a>.";
	  		  display($page, "Guild Vaults");
		  }
        	$newgold = $userrow["gold"] - $deposit;
	  	$newbank = $userrow["bank"] + $deposit;
        	if ($newbank > 99999999) {
		$tmpgold = $newbank - 99999999;
		$newgold += $tmpgold;
		$newbank = 99999999;
		}
        $query = doquery("UPDATE {{table}} SET bank='$newbank',gold='$newgold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
    	$page = "<table width=\"100%\"><tr><td class=\"title\">Guild Vault.</td></tr></table>";
        $page .= "You have deposited ".$deposit." gold into the Vaults<br />";
	  	$page .= "The current Vault balance is: <b>".$newbank."</b> gold.<br /><br />";
	  	$page .= "You may return to <a href=\"strongholds.php?do=vault\">the vaults</a>, go back ";
	  	$page .= "to the <a href='strongholds.php'>Stronghold</a>, ";
	  	$page .= "or leave and <a href=\"index.php\">continue exploring</a>.";
	  	    display($page, "Guild Vaults");
		} elseif ($_POST["withdraw"] != '') {
		  $withdraw = abs(intval($_POST["withdraw"]));
		  if ($userrow["bank"] < $withdraw) {
		    $page = "<table width=\"100%\"><tr><td class=\"title\">Guild Vault</td></tr></table>";
		  	$page .= "<br><b>You do not have ".$withdraw." gold to withdraw!!</b><br><br>";
	  		$page .= "You may return to <a href=\"strongholds.php?do=vault\">the vaults</a>, go back ";
	  		$page .= "to the <a href='strongholds.php'>Stronghold</a>, ";
	  		$page .= "or leave and <a href=\"index.php\">continue exploring</a>.";
	  		  display($page, "Guild Vault");
		  }
        	$newgold = $userrow["gold"] + $withdraw;
	  	$newbank = $userrow["bank"] - $withdraw;
        if ($newgold > 99999999) {
		$tmpgold = $newgold - 99999999;
		$newbank += $tmpgold;
		$newgold = 99999999;
		}
        $query = doquery("UPDATE {{table}} SET bank='$newbank',gold='$newgold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
    	$page = "<table width=\"100%\"><tr><td class=\"title\">Guild Vault</td></tr></table>";
        $page .= "You have withdrawn ".$withdraw." gold from the Vaults.<br />";
	  	$page .= "The current Vault balance is: <b>".$newbank."</b> gold.<br /><br />";
	  	$page .= "You may return to <a href=\"strongholds.php?do=vault\">the vaults</a>, go back ";
	  	$page .= "to the <a href=\"strongholds.php?\">Stronghold</a>, ";
	  	$page .= "or leave and <a href=\"index.php\">continue exploring</a>.";
	  		display($page, "Guild Vault");
		}
    } elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

    } else {

$updatequery = doquery("UPDATE {{table}} SET location='Stronghold Vault' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

    	$title = "Guild Vault";
	$page = "<table width=\"100%\"><tr><td class=\"title\">Guild Vault.</td></tr></table>";
	$page .= "Welcome to the Stronghold Vault.  Here you can deposit or withdraw gold.<br>";
	$page .= "Any gold kept in the vaults is safe when you die. ";
	$page .= "The Vault is linked to your gold in your town bank, but with some extra special features.<p>";
	$page .= "your current vault balance is: <b>".$userrow["bank"]."</b> gold.<p><br>";
	$page .= "<table><tr><td>";
	$page .= "<table><tr><td><form action='strongholds.php?do=vault' method='post'>";
	$page .= "Withdraw Gold</td></tr><tr><td>Enter Amount:<br>";
	$page .= "<input type='text' size='15' name='withdraw'><br>";
	$page .= "<input type='submit' name='submit' value='Withdraw'>";
	$page .= "</form></td></tr></table></td>";
	$page .= "<td><table><tr><td><form action='strongholds.php?do=vault' method='post'>";
	$page .= "Deposit Gold</td></tr><tr><td>Enter Amount:<br>";
	$page .= "<input type='text' size='15' name='deposit' value='".$userrow["gold"]."'><br>";
	$page .= "<input type='submit' name='submit' value='Deposit'>";
	$page .= "</form></td></tr></table></td></tr></table>";
	$page .= "<br />You may return to the <a href='strongholds.php'>Strongholds</a>, ";
	$page .= "or leave and <a href='index.php'>continue exploring</a>.";
	  	    display($page, "Guild Vaults");
    }
}

 function dobootmember($id) {
     global $userrow;
if ($userrow["guildrank"] < 100) { header("Location: strongholds.php"); die();}
$updatequery = doquery("UPDATE {{table}} SET location='Booting a Member' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
    if (isset($_POST["submit"])) {
 	$charID = $_POST["charID"];
 	$charname = $_POST["charname"];
 	$guildname = $userrow["guildname"];
 	if (isset($_POST["confirm"])) {

 		$page = "<table width=\"100%\"><tr><td class=\"title\">Boot Guild Member</td></tr></table>";
 		$page .= "<p>You have booted <b>".$charname."</b> from the ".$guildname.".<br>";
     		$page .= "<center><br><a href='strongholds.php'>Return to the Stronghold</a></center>";
        $query2 = doquery("UPDATE {{table}} SET members=members-1 WHERE name='".$guildname."' LIMIT 1", "guilds");
 		$updatequery = doquery("UPDATE {{table}} SET guildrank='0',guildname='-' WHERE id='$id' LIMIT 1", "users");
    	} else {
 		$page = "<table width=\"100%\"><tr><td class=\"title\">Boot Guild Member</td></tr></table>";
 		$page .= "<p>Are you sure you want to boot <b>".$charname."</b> from the ".$guildname." Guild?<p>";
 		$page .= "<form action='strongholds.php?do=bootmember:".$charID."' method='post'>\n";
 		$page .= "<input type='submit' name='submit' value='Yes'>    ";
 		$page .= "<input type='submit' name='cancel' value='No'>\n";
 		$page .= "<input type='hidden' name='confirm' value='confirm'>";
		$page .= "<input type='hidden' name='charname' value='".$charname."'>";
 		$page .= "</form>\n";
 	}
     }
 	display($page, "Boot Guild Member");
 }



function doeditguild() {
	global $userrow;
if ($userrow["guildrank"] < 100) { header("Location: strongholds.php"); die();}
$updatequery = doquery("UPDATE {{table}} SET location='Editing Guild Settings' WHERE id='".$userrow["id"]."' LIMIT 1", "users");


	if (isset($_POST["submit"])) {
		$name = $_POST["name"];
		$private = $_POST["private"];
		$tag = $_POST["tag"];
		$password = $_POST["password"];
		$joincost = $_POST["joincost"];
		$news = $_POST["news"];
		$news = my_htmlspecialchars($news);
    		$description = trim($description);
		$description = $_POST["description"];
		$description = my_htmlspecialchars($description);
    		$description = trim($description);
		$rank1name = $_POST["rank1name"];
		$rank2name = $_POST["rank2name"];
		$rank3name = $_POST["rank3name"];
		$rank4name = $_POST["rank4name"];
		$rank5name = $_POST["rank5name"];
		$rank6name = $_POST["rank6name"];
		$uquery = doquery("UPDATE {{table}} SET password='$password',tag='$tag',joincost='$joincost',private='$private',news='$news',
				description='$description',rank1name='$rank1name',rank2name='$rank2name',
				rank3name='$rank3name',rank4name='$rank4name',rank5name='$rank5name',
				rank6name='$rank6name' WHERE name='$name' LIMIT 1", "guilds");

	} elseif (isset($_POST["cancel"])) {
        header("Location: strongholds.php"); die();

	}
	$castlequery = doquery("SELECT * FROM {{table}} WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
	$grow = mysql_fetch_array($castlequery);
     if ($grow["private"] == 0) { $grow["selectprivate0"] = "selected=\"selected\" "; } else { $grow["selectprivate0"] = ""; }
    if ($grow["private"] == 1) { $grow["selectprivate1"] = "selected=\"selected\" "; } else { $grow["selectprivate1"] = ""; }
 
	$page = "<table width='100%'><tr><td class='title'>Guild Settings</td></tr></table>";
	$page .= "<p>Edit the settings for your Guild here. Whatever amount of Dragon Scales you set the Join Cost to will mean that when a member joins, your Guild will receive that desired amount of Dragon Scales. You may also add a password to your guild instead.<p>";
	$page .= "<form action='strongholds.php?do=editguild' method='POST'><table width='100%'>";
	$page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><td colspan=\"0\" style=\"background-color:#dddddd;\"><b>Edit your Settings</td></tr>\n";
	$page .= "<tr><td bgcolor='#eeeeee'>Guild Name:</td>";
	$page .= "<td bgcolor='#eeeeee'><b>".$grow["name"]."</b></td></tr>";
	$page .= "<input type='hidden' name='name' value='".$grow["name"]."'>";
	$page .= "<tr><td bgcolor='#ffffff'>Dragon Scales:</td>";
	$page .= "<td bgcolor='#ffffff'><b>".$grow["dscales"]."</b></td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'>Join Option:</td>";
	$page .= "<td bgcolor='#eeeeee'><select name='private'>";	
	$page .= "<option value=\"0\" {{selectprivate0}}>Dragon Scales Cost</option><option value=\"1\" {{selectprivate1}}>Password Required</option></select>";
	$page .= "<tr><td bgcolor='#ffffff'>Join Cost:</td>";
	$page .= "<td bgcolor='#ffffff'><input type='text' name='joincost' value='".$grow["joincost"]."' maxlength='5'><br>";
	$page .= "<i>* only used if 'Dragon Scales' is selected above.</i></td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'>Guild Tag:<p><i>Initals to represent your guild name (no more than 3 letters)</I></td>";
	$page .= "<td bgcolor='#eeeeee'><input type='text' name='tag' value='".$grow["tag"]."' maxlength='3'><br>";
	$page .= "<tr><td bgcolor='#eeeeee'>Password:</td>";
	$page .= "<td bgcolor='#eeeeee'><input type='text' name='password' value='".$grow["password"]."' maxlength='15'><br>";
	$page .= "<i>* only used if 'Password required' is selected above.</i></td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'>Post Guild News:<p><i>Any abuse of the news will result in the Guild being removed, along with a possibility of a personal ban</i></td>";
	$page .= "<td bgcolor='#ffffff'><textarea name='news' cols='30' rows='6' wrap='virtual'>";
	$page .= $grow["news"]."</textarea></td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'>Description:<p><i>Any abuse of the description will result in the Guild being removed, along with a possibilty of a personal ban</i></td>";
	$page .= "<td bgcolor='#ffffff'><textarea name='description' cols='30' rows='6' wrap='virtual'>";
	$page .= $grow["description"]."</textarea></td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'>Rank 1:</td>";
	$page .= "<td bgcolor='#eeeeee'><input type='text' name='rank1name' value='".$grow["rank1name"]."' maxlength='20'></td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'>Rank 2:</td>";
	$page .= "<td bgcolor='#ffffff'><input type='text' name='rank2name' value='".$grow["rank2name"]."' maxlength='20'></td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'>Rank 3:</td>";
	$page .= "<td bgcolor='#eeeeee'><input type='text' name='rank3name' value='".$grow["rank3name"]."' maxlength='20'></td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'>Rank 4:</td>";
	$page .= "<td bgcolor='#ffffff'><input type='text' name='rank4name' value='".$grow["rank4name"]."' maxlength='20'></td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'>Rank 5:</td>";
	$page .= "<td bgcolor='#eeeeee'><input type='text' name='rank5name' value='".$grow["rank5name"]."' maxlength='20'></td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'>Rank 6:</td>";
	$page .= "<td bgcolor='#ffffff'><input type='text' name='rank6name' value='".$grow["rank6name"]."' maxlength='20'></td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee' colspan='2'> </td></tr>";
	$page .= "<tr><td bgcolor='#ffffff' colspan='2'> ";
	$page .= "<input type='submit' name='submit' value='Save Changes'>        -        ";
	$page .= "<input type='submit' name='cancel' value='Cancel'></td></tr></table></table>";
	
     if ($grow["private"] == 0) { $grow["selectprivate0"] = "selected=\"selected\" "; } else { $grow["selectprivate0"] = ""; }
    if ($grow["private"] == 1) { $grow["selectprivate1"] = "selected=\"selected\" "; } else { $grow["selectprivate1"] = ""; }
    	$page .= "<center><br><a href='strongholds.php'>Back to the Stronghold</a></center>";
    	display($page,"Guild Settings");

}



function doportals ($id){
	global $userrow;

$updatequery = doquery("UPDATE {{table}} SET location='Stronghold Portals' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "strongholds");
	$castlerow = mysql_fetch_array($castlequery);
	if ($castlerow["ruined"] != 0) {
		$page = "<table width='100%'><tr><td class='title'>Ruined Stronghold.</td></tr></table>";
		$page .= "<br><b>This Stronghold has been ruined!!</b><br><br>";
		$page .= "You may return to the <a href='index.php'>Stronghold</a>, ";
		$page .= "or leave and <a href='index.php'>continue exploring</a>.";
		display($page, "Ruined Stronghold");

	}
	if (isset($id)) {
	$cquery = doquery("SELECT * from {{table}} WHERE id='$id' LIMIT 1", "strongholds");
	$crow = mysql_fetch_array($cquery);
	$newlat = $crow["latitude"];
	$newlon = $crow["longitude"];

	$p = doquery("UPDATE {{table}} SET latitude='$newlat',longitude='$newlon',currentaction='Exploring' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	    header("Location: index.php?do=move:0"); die();

	}

	if ($userrow["currentap"] <= 9) {
		$page = "<table width='100%'><tr><td class='title'>Not Enough Ability Points!</td></tr></table>";
		$page .= "<br><b>You do not have enough AP to perform this action.</b><br><br>";
		$page .= "You must have at least 10 AP in order to do this action. ";
		$page .= "Your AP is refilled for a price of 50 Dragon Scales.<br>";
		$page .= "<p>You may return to the <a href='strongholds.php'>Stronghold</a>, ";
		$page .= "or leave and <a href='index.php'>continue exploring</a>.";
		display($page, "Not Enough AP");
	}

	$cquery = doquery("SELECT * from {{table}} WHERE guildname='".$userrow["guildname"]."' ORDER BY id", "strongholds");
	$page = "<table width='100%'><tr><td class='title'>Stronghold Portals</td></tr></table>";
	$page .= "The portal links to all strongholds controlled by your Guild.  Please choose a ";
	$page .= "destination, or click below to return to the Stronghold.<br>";
	$page .= "<table><tr><td bgcolor='#ffffff' width='20%'><b>Stronghold</b></td>";
	$page .= "<td bgcolor='#ffffff'><b>Location</b></td></tr>";
	$count = 0;
	while ($crow=mysql_fetch_array($cquery)) {
		$count += 1;
		if ($crow["latitude"] < 0) {$dlat='S';} else {$dlat='N';}
		if ($crow["longitude"] < 0) {$dlon='W';} else {$dlon='E';}
		if (($count/2) != intval($count/2)) {$colour = "bgcolor='#eeeeee'";} else {$colour="bgcolor='#ffffff'";}
		$page .= "<td ".$colour." width='20%'><a href='strongholds.php?do=gportal:".$crow["id"]."'>";
		$page .= $count.") ".$userrow["guildname"]."</td><td ".$colour.">";
		$page .= abs($crow["latitude"]).$dlat.", ".abs($crow["longitude"]).$dlon."</a></td></tr>";
	}
	if ($count <= 0) {$page .= "<td colspan='2'>No Strongholds built for your Guild!</td><tr>";}
	$page .= "</table>";
        $newap = $userrow["currentap"] - 10;
	$pquery = doquery("UPDATE {{table}} SET currentap='$newap' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
    $page .= "<center><br><a href='strongholds.php'>Back to the Stronghold</a></center>";
    display($page,"Guild Stronghold");
}


function dostats() {
	global $userrow;

$updatequery = doquery("UPDATE {{table}} SET location='Stronghold Statistics' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "strongholds");
	$castlerow = mysql_fetch_array($castlequery);

        $g = doquery("SELECT * FROM {{table}} WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
        $grow = mysql_fetch_array($g);
	$newcastlegold = $castlerow["gold"];
	if (mysql_num_rows($castlequery) <= 0) {header("Location: index.php?do=move:0"); die();}
	$page = "<table width='100%'><tr><td class='title'>Stronghold Statistics</td></tr></table>";
	$page .= "<p>Here is a list of available statistics for this current Stronghold. Note that the gold is separate from all Strongholds and each Stronghold has its own unique amount of Gold, and Storage. However, Dragon Scales are not shared.<p>";

$page .= "<center><table>";
	$page .= "<tr><td bgcolor='#ffffff'><b>Founder:</b></td><td bgcolor='#ffffff'>".$castlerow["founder"]."</td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Guild Name:</b></td><td bgcolor='#eeeeee'>".$castlerow["guildname"]."</td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'><b>Stronghold Level:</b></td><td bgcolor='#ffffff'>".$castlerow["level"]."</td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Experience:</b></td><td bgcolor='#eeeeee'>".$castlerow["experience"]."</td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'><b>Stronghold Gold:</b></td><td bgcolor='#ffffff'>".$castlerow["gold"]."</td></tr>";

$page .= "<tr><td bgcolor='#ffffff'><b>Guild Dragon Scales:</b></td><td bgcolor='#ffffff'>".$grow["dscales"]."</td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Armor Level:</b></td><td bgcolor='#eeeeee'>".$castlerow["armorlevel"]."</td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'><b>Magic Level:</b></td><td bgcolor='#ffffff'>".$castlerow["magiclevel"]."</td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Weaponry Level:</b></td><td bgcolor='#eeeeee'>".$castlerow["weaponrylevel"]."</td></tr>";
        $page .= "<tr><td bgcolor='#ffffff'><b>Structural Points (HP):</b></td><td bgcolor='#ffffff'>".$castlerow["currenthp"]." out of ".$castlerow["maxhp"]."</td></tr>";
        $page .= "<tr><td bgcolor='#eeeeee'><b>Current/Total MP:</b></td><td bgcolor='#eeeeee'>".$castlerow["currentmp"]." / ".$castlerow["maxmp"]."</td></tr>";

	$page .= "<tr><td bgcolor='#ffffff'><b>Armor Rating:</b></td><td bgcolor='#ffffff'>".$castlerow["weaponry"]." out of <b>".($castlerow["weaponrylevel"]*100)."</td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'><b>Magic Armor:</b></td><td bgcolor='#eeeeee'>".$castlerow["magic"]."</b> out of <b>".($castlerow["magiclevel"]*100)."</td></tr>";
        $page .= "<tr><td bgcolor='#ffffff'><b>Weaponry Rating:</b></td><td bgcolor='#ffffff'>".$castlerow["armor"]."</b> out of <b>".($castlerow["armorlevel"]*100)."</td></tr>";


	$page .= "<tr><td bgcolor='#eeeeee'><b>Total Vipers:</b></td><td bgcolor='#eeeeee'>".$castlerow["snails"]."</td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'><b>Total Golems:</b></td><td bgcolor='#ffffff'>".$castlerow["kelplings"]."</td></tr>";
        $page .= "<tr><td bgcolor='#eeeeee'><b>Total Gargoyles:</b></td><td bgcolor='#eeeeee'>".$castlerow["minnows"]."</td></tr>";

	$page .= "</table></center>";



	$page .= "<center><br><a href='strongholds.php'>Back to the Stronghold</a></center>";
    display($page,"Stronghold Statistics");
}


function dorecruit() {
	global $userrow;

$updatequery = doquery("UPDATE {{table}} SET location='Recruiting Troops' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "strongholds");
	$castlerow = mysql_fetch_array($castlequery);
	if (mysql_num_rows($castlequery) <= 0) {header("Location: index.php?do=move:0"); die();}
	$page = "<table width='100%'><tr><td class='title'>Recruit Troops</td></tr></table><p><center><img src=\"images/troops.gif\" border=\"0\" alt=\"Troops\" /></a></center>";
	if (isset($_POST["submit"])) {
		$numtroops = abs(intval($_POST["troops"]));
		$trooptype = $_POST["trooptype"];
		if ($userrow["dscales"] < $numtroops) {
		$page .= "<p>You do not have enough Dragon Scales to recruit that many Troops.<br>";
		$page .= "<p>Your stronghold currently has the following units currently available:<br>";
		$page .= "<b>".$castlerow["snails"]. "</b> Vipers.  (Vipers destroy Magic)<br>";
		$page .= "<b>".$castlerow["kelplings"]. "</b> Golems.  (Golems damage Weaponry)<br>";
		$page .= "<b>".$castlerow["minnows"]. "</b> Gargoyles.  (Gargoyles weaken Armor)<p>";
		$page .= "Troops cost 1 Dragon Scale each from out of your own pocket, and not from the guild itself, regardless of type.  How many would you like to hire?<br>";
		$page .= "<form action='strongholds.php?do=recruit' method='POST'>";
		$page .= "Number of Troops: ";
		$page .= "<input type='text' name='troops' value='0' size='5'> ";
		$page .= "<select name='trooptype'>";
		$page .= "<option value='snails'>Vipers</option>";
		$page .= "<option value='kelplings'>Golems</option>";
		$page .= "<option value='minnows'>Gargoyles</option></select><br>";
		$page .= "<input type='submit' name='submit' value='Hire Troops'>";
		$page .= "<center><br><a href='strongholds.php'>Return to the Stronghold</a></center>";
 		display($page,"Recruiting Troops");
 		}
		$castlerow["$trooptype"] = $castlerow["$trooptype"] + $numtroops;
		$newsnails = $castlerow["snails"];
		$newkelps = $castlerow["kelplings"];
		$newminnows = $castlerow["minnows"];
		$newdscales = $userrow["dscales"] - $numtroops;
		$page .= "You hired $numtroops of your chosen Troop type.<br>";
		
		    $gquery = doquery("SELECT id FROM {{table}} WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
	$grow = mysql_fetch_array($gquery);
	
	  					$logdate = troops;
						$logpath = "./logs/guild".$grow["id"]."/";
						if (!is_dir($logpath)) {
					 	@mkdir($logpath,0777);
				     	@chmod($logpath,0777);
		    	}
				$logfile =  $logpath.$logdate.".log";
				$logcomments = "--".$userrow["charname"]." hired <b>".$numtroops." Troops</b>";
				$logcomments .= " on ".date("r").". <br>\r\n";
				$fp = fopen("$logfile", "a");
				fwrite($fp, "$logcomments");
				fclose ($fp);
				
                $updatequery = doquery("UPDATE {{table}} SET dscales='$newdscales' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
		$t = doquery("UPDATE {{table}} SET snails='$newsnails',kelplings='$newkelps', minnows='$newminnows' WHERE id='".$castlerow["id"]."' ", "strongholds");
		}
	$page .= "<p>Your stronghold currently has the following units available:<br>";
	$page .= "<b>".$castlerow["snails"]. "</b> Vipers.  (Vipers destroy Magic)<br>";
	$page .= "<b>".$castlerow["kelplings"]. "</b> Golems.  (Golems damage Weaponry)<br>";
	$page .= "<b>".$castlerow["minnows"]. "</b> Gargoyles.  (Gargoyles weaken Armor)<p>";
	$page .= "Troops cost 1 Dragon Scale each, regardless of type.  How many would you like to hire?<br>";
	$page .= "(Maximum number of each troop is 65535)<br>";
	$page .= "<form action='strongholds.php?do=recruit' method='POST'>";
	$page .= "Number of Troops: ";
	$page .= "<input type='text' name='troops' value='0' size='5'> ";
	$page .= "<select name='trooptype'>";
	$page .= "<option value='snails'>Vipers</option>";
	$page .= "<option value='kelplings'>Golems</option>";
	$page .= "<option value='minnows'>Gargoyles</option></select><br>";
	$page .= "<input type='submit' name='submit' value='Hire Troops'>";
	$page .= "<center><br><a href='strongholds.php'>Return to the Stronghold</a></center>";
	display($page,"Recruiting Troops");
}



function gamble($game) {
 global $userrow;

	$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "strongholds");
	if (mysql_num_rows($castlequery) <= 0) { header("Location: index.php"); die(); }


    if (isset($_POST["submit"])) {
 $game = $_POST["game"];
 $page = "<table width=\"100%\"><tr><td class=\"title\">High Stake Gambling</td></tr></table>";
if ($game == "shells") {
  if ($userrow["dscales"] < 3) {
  $page .= "You do not have enough Dragon Scales to play Shells!<br>";
  $page .= "You may go back to the <a href='strongholds.php'>Stronghold</a>,
";
  $page .= " or leave and ";
  $page .= "continue exploring using the compass</a>.<p>";
  display($page, "High Stake Gambling");
  }
  if (!isset($_POST["shellnumber"])) {
  $page .= "Under one of these shells is hidden a rare blue pearl.";
  $page .= "If you choose the correct shell, you win some Dragon Scales, if not you
";
  $page .= "can always try playing again!<p>";
  $page .= "Please Choose a Shell:";
  $page .= "<table><tr>";
  $page .= "<td align='center'><FORM ACTION='strongholds.php?do=gamble:shells'
method='post'>";
  $page .= "<img SRC='img/gambleshell.gif' ALT='Shell 1'><br>";
  $page .= "<input type='Submit' name='submit' value='Shell 1'>";
  $page .= "<input type='hidden' name='game' value='shells'>";
  $page .= "<input type='hidden' name='shellnumber' value='1'>";
  $page .= "</FORM></td>";
  $page .= "<td align='center'><FORM ACTION='strongholds.php?do=gamble:shells'
method='post'>";
  $page .= "<img SRC='img/gambleshell.gif' ALT='Shell 2'><br>";
  $page .= "<input type='Submit' name='submit' value='Shell 2'>";
  $page .= "<input type='hidden' name='game' value='shells'>";
  $page .= "<input type='hidden' name='shellnumber' value='2'>";
  $page .= "</FORM></td>";
  $page .= "<td align='center'><FORM ACTION='strongholds.php?do=gamble'
method='post'>";
  $page .= "<img SRC='img/gambleshell.gif' ALT='Shell 3'><br>";
  $page .= "<input type='Submit' name='submit' value='Shell 3'>";
  $page .= "<input type='hidden' name='game' value='shells'>";
  $page .= "<input type='hidden' name='shellnumber' value='3'>";
  $page .= "</FORM></td></tr></table>";
  $page .= "Simply Click on a shell to see if you win!<p>";
  $page .= "You may go back to the <a href='strongholds.php'>Stronghold</a>, if you have changed your mind. ";
  display($page,"High Stake Gambling");
  }
  $payout = -3;
  $newdscales = $userrow["dscales"];
  $correct = rand(1,7);
  $page .= "You chose shell number <b>".$_POST["shellnumber"]."</b>...<p>";
  if ($correct == $_POST["shellnumber"]) {
   $payout = $correct*5;
   $page .= "<h3><img src='img/gambleshellw.gif'><br>You Won!</h3>";
   $page .= "You have won a total of <b>".$payout."</b> Dragon Scales!<p>";
  } else {
   $page .= "<img src='img/gambleshell.gif'><br>";
   $page .= "You did not win anything...<p>";
  }
  $newdscales += $payout;
  $page .= "<form action='strongholds.php?do=gamble' method='POST'>";
  $page .= "You may either <input type='Submit' name='submit' value='Play
Again (3 Scales)' onclick='over18sonly();'> ";
  $page .= "<input type='hidden' name='game' value='shells'></form></div>";
  $page .= "or
return to the ";
  $page .= "<a href='strongholds.php'>Stronghold.</a><p>";
  $query = doquery("UPDATE {{table}} SET dscales='$newdscales' WHERE
id='".$userrow["id"]."' LIMIT 1", "users");
  display($page, "High Stake Gambling");




 } elseif ($game == "wheel") {
  if ($userrow["dscales"] < 5) {
  $page .= "You do not have enough Dragon Scales to spin the Wheel!<br>";
  $page .= "You may go back to the <a href='strongholds.php'>Stronghold</a>,
";
  $page .= "or head back to exploring by using the compass.</a>";
  display($page, "High Stake Gambling");
  }
  $payout = -5;
  $newdscales = $userrow["dscales"];
  $chance = rand(1,50);
  $page .= "You spin the Wheel of Fortune and received a
<b>".$chance."</b>...<br>";
  if ($chance <=10) {
   $payout = $chance*4+2;
   $page .= "<h3>You Won!</h3>";
   $page .= "You have won a total of <b>".$payout."</b> Dragon Scales!<p>";
  } else {
   $page .= "<br>";
   $page .= "You did not win anything... Sorry.<p>";
  }
  $newdscales += $payout;
  $page .= "<form action='strongholds.php?do=gamble' method='POST'>";
  $page .= "You may either <input type='Submit' name='submit' value='Play
Again (5 Scales)' onclick='over18sonly();'> ";
  $page .= "<input type='hidden' name='game' value='wheel'></form></div>";
  $page .= "or go back to the <a href='strongholds.php'>Stronghold.</a><p>";
  $query = doquery("UPDATE {{table}} SET dscales='$newdscales' WHERE
id='".$userrow["id"]."' LIMIT 1", "users");
display($page, "High Stake Gambling");
 } else {
  $page .= "<p> ERROR:  You must choose a game to play, or there was an
error processing ";
  $page .= "the request.  Please <a href='strongholds.php?do=gamble'>Go Back</a>
and try again.<p>";
 }

    } else {

$updatequery = doquery("UPDATE {{table}} SET location='High Stake Gambling' WHERE id='".$userrow["id"]."' LIMIT 1", "users");


 $page = "<table width='100%' border='1'><tr><td class='title'>High Stake Gambling</td></tr></table><p>";
 $page .= "<center><table><tr>";

 $page .= "<p>Welcome to the High Stake Gambling area. Games here are unlike the games in the Gambling Den. Here, you lose and win Dragon Scales. Each game is uniquely different to the Den too. Their odds are different, and the reward is obviously different.<p>Soon you will be able to place large amounts of Gold onto our New Game in hope of winning thousands!<p><center><td align='center'><b><u>Pick A Shell!</u></b><br> It only costs
3 Dragon Scales!<br>";
 $page .= "A rare blue pearl has been hidden under one of three shells. ";
 $page .= " If you choose the shell with the pearl under it, you win!";
 $page .= "<form action='strongholds.php?do=gamble:shells' method='POST'>";
 $page .= "<input type='hidden' name='game' value='shells'>";
 $page .= "<input type='Submit' name='submit' value='Play (3 Scales)'>";
 $page .= "</form></center>";

 $page .= "<td align='center'><b><u>Wheel of Fortune!</u></b><br> It only
costs 5 Dragon Scales!<br>";
 $page .= "Spin the wheel for fun and prizes!";
 $page .= "<form action='strongholds.php?do=gamble:wheel' method='POST'>";
 $page .= "<input type='hidden' name='game' value='wheel'>";
 $page .= "<input type='Submit' name='submit' value='Play (5 Scales)'>";
 $page .= "</form></td></table></center>";
 $page .= "Please note that whatever amount of Dragon Scales you pay out, you get it back if you win.<p>You may go back to your <a href='strongholds.php'>Stronghold</a>, if you have changed your mind. ";
    }
 display($page, "High Stake Gambling");
}

function magics() {
	global $userrow;
if ($userrow["guildrank"] < 100) { header("Location: strongholds.php"); die();}
$updatequery = doquery("UPDATE {{table}} SET location='Guild Magics' WHERE id='".$userrow["id"]."' LIMIT 1", "users");


	$page = "<table width='100%'><tr><td class='title'>Guild Magics</td></tr></table>";
	$page .= "<p>Possibly coming soon<p>";
    	$page .= "<center><br><a href='strongholds.php'>Return to Stronghold</a></center>";
    display($page,"Guild Magics");
}

function items() {
	global $userrow;

$updatequery = doquery("UPDATE {{table}} SET location='Unique Items' WHERE id='".$userrow["id"]."' LIMIT 1", "users");


	$page = "<table width='100%'><tr><td class='title'>Unique Items</td></tr></table>";
	$page .= "<p>There are currently no unique items to purchase.<p>";
    	$page .= "<center><br><a href='strongholds.php'>Return to Stronghold</a></center>";
    display($page,"Unique Items");
}

function summon() { // Summon artificial souls - KBD


    global $userrow, $numqueries, $controlrow;

	$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "strongholds");
	if (mysql_num_rows($castlequery) <= 0) { header("Location: index.php"); die(); }


    if ($userrow["gold"] < 25000){ display("You do not have enough gold to Summon any Souls.<br /><br />You may return to your <a href=\"strongholds.php\">Stronghold</a>, or use the direction buttons on the right to start exploring.", "Not enough Gold"); die(); }


	if ($userrow["currentap"] <= 0) {
		$page = "<table width='100%'><tr><td class='title'>Not Enough Ability Points!</td></tr></table>";
		$page .= "<br><b>You do not have enough AP to perform this action.</b><br><br>";
		$page .= "You must have at least 1 AP in order to do this action. ";
		$page .= "Your AP is refilled for a price of 50 Dragon Scales.<br>";
		$page .= "<p>You may return to the <a href='strongholds.php'>Stronghold</a>, ";
		$page .= "or leave and <a href='index.php'>continue exploring</a>.";
		display($page, "Not Enough AP");

}

    if (isset($_POST["submit"])) {

        $newgold = $userrow["gold"] - 25000;
        $query = doquery("UPDATE {{table}} SET gold='$newgold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
$updatequery = doquery("UPDATE {{table}} SET location='Summon Souls' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

        $title = "Summon Souls";
        $page = "<table width=\"100%\"><tr><td class=\"title\">You have Summoned the Souls</td></tr></table>";
        $page .= "You have performed a Summoning of the Souls successfully.<p>King Black Dragons Souls arise one by one, from its fiery pits to spread the blood of the Innocent!<br />";

$randomid = rand(60001,75000);
$randhp = rand(2000,3650);
$randdef = rand(1950,2500);
$randattack = rand(950,2350);
$randexp = rand(2500,3500);
$randgold = rand(2200,3600);
$randdscales = rand(3,12);
$query = doquery("INSERT INTO dk_souls SET id='$randomid',hp='$randhp',type='5',exp='$randexp',gold='$randgold',dscales='$randdscales',def='$randdef',attack='$randattack'", "souls") or die(mysql_error());
$page .="<p>Here are the Souls ID Numbers:<p><center><b>$randomid</b></center><br>";

$randomid = rand(60001,75000);
$randhp = rand(2000,3650);
$randdef = rand(1950,2500);
$randattack = rand(950,2350);
$randexp = rand(2500,3500);
$randgold = rand(2200,3600);
$randdscales = rand(3,12);
$query = doquery("INSERT INTO dk_souls SET id='$randomid',hp='$randhp',type='5',exp='$randexp',gold='$randgold',dscales='$randdscales',def='$randdef',attack='$randattack'", "souls") or die(mysql_error());
$page .="<center><b>$randomid</b></center><br>";


$randomid = rand(60001,75000);
$randhp = rand(2000,3650);
$randdef = rand(1950,2500);
$randattack = rand(950,2350);
$randexp = rand(2500,3500);
$randgold = rand(2200,3600);
$randdscales = rand(3,12);
$query = doquery("INSERT INTO dk_souls SET id='$randomid',hp='$randhp',type='5',exp='$randexp',gold='$randgold',dscales='$randdscales',def='$randdef',attack='$randattack'", "souls") or die(mysql_error());
$page .="<center><b>$randomid</b></center><p>The souls fly off in different directions... Maybe you should try and find them, to receive your rewards.<p><br />You may return to the <a href=\"strongholds.php\">Stronghold</a>, or use the direction buttons on the right to start exploring.";


    } elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

    } else {

        $title = "Summon Souls";
        $page = "<table width=\"100%\"><tr><td class=\"title\">Summon Souls</td></tr></table>";
        $page .= "Performing a Summoning of the Souls will cause 3 King Black Dragons to arise from the Depths of the underground.<br /><br />\n";
        $page .= "To perform the Summoning it will cost you <b> 25,000 gold. </b>. Is that ok?<br /><br />\n";
        $page .= "<form action=\"strongholds.php?do=summon\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes\" /> <input type=\"submit\" name=\"cancel\" value=\"No\" />\n";
        $page .= "</form>\n";

        $newap = $userrow["currentap"] - 1;
	$pquery = doquery("UPDATE {{table}} SET currentap='$newap' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

    }

    display($page, $title);

}

function dodefense() {
	global $userrow;

$updatequery = doquery("UPDATE {{table}} SET location='Fortifying Defenses' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	$castlequery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "strongholds");
	$castlerow = mysql_fetch_array($castlequery);
	$newcastlegold = $castlerow["gold"];
	if (mysql_num_rows($castlequery) <= 0) {header("Location: index.php?do=move:0"); die();}
	$page = "<table width='100%'><tr><td class='title'>Fortify Defenses</td></tr></table>";
	if (isset($_POST["submit"])) {
	} elseif (isset($_POST["armorup"])) {
		if ($castlerow["gold"] < $castlerow["armorlevel"]*42500) {
		$page .= "<p>You do not have enough gold to upgrade this stronghold's Armor!<br>";
		$page .= "It will cost <b>".($castlerow["armorlevel"]*42500)."</b> gold to upgrade to level ";
		$page .= "<b>".($castlerow["armorlevel"]+1)." armor.<p>";
		$page .= "<center><br><a href='strongholds.php'>Back to the Stronghold</a></center>";
    		display($page,"Guild Stronghold");
		}
		$newarmorlevel = $castlerow["armorlevel"]+1;
		$newarmor = $newarmorlevel*100;
		$newcastlegold = $castlerow["gold"] + ($castlerow["armorlevel"]*375);
		$newgold = $castlerow["gold"] - ($castlerow["armorlevel"]*42500);
		$cu = doquery("UPDATE {{table}} SET armorlevel='$newarmorlevel',armor='$newarmor',gold='$newgold' WHERE id='".$castlerow["id"]."' LIMIT 1", "strongholds");
		$castlerow["armorlevel"] += 1;
	} elseif (isset($_POST["magicup"])) {
		if ($castlerow["gold"] < $castlerow["magiclevel"]*35000) {
		$page .= "<p>You do not have enough gold to upgrade this stronghold's Magic!<br>";
		$page .= "It will cost <b>".($castlerow["magiclevel"]*35000)."</b> gold to upgrade to level ";
		$page .= "<b>".($castlerow["magiclevel"]+1)." armor.<p>";
		$page .= "<center><br><a href='strongholds.php'>Back to the Stronghold</a></center>";
    		display($page,"Guild Stronghold");
		}
		$newmagiclevel = $castlerow["magiclevel"]+1;
		$newmagic = $newmagiclevel*100;
		$newcastlegold = $castlerow["gold"] + ($castlerow["magiclevel"]*350);
		$newgold = $castlerow["gold"] - ($castlerow["magiclevel"]*35000);
		$cu = doquery("UPDATE {{table}} SET magiclevel='$newmagiclevel',magic='$newmagic',gold='$newgold' WHERE id='".$castlerow["id"]."' LIMIT 1", "strongholds");
		$page .= "<p>You upgraded this stronghold's Magic level!<p>";
		$castlerow["magiclevel"] += 1;
	} elseif (isset($_POST["weaponup"])) {
		if ($castlerow["gold"] < $castlerow["weaponrylevel"]*39000) {
		$page .= "<p>You do not have enough gold to upgrade this stronghold's Weaponry!<br>";
		$page .= "It will cost <b>".($castlerow["weaponrylevel"]*39000)."</b> gold to upgrade to level ";
		$page .= "<b>".($castlerow["weaponrylevel"]+1)." weapons.<p>";
		$page .= "<center><br><a href='strongholds.php'>Back to the Stronghold</a></center>";
    		display($page,"Guild Stronghold");
		}
		$newweaponrylevel = $castlerow["weaponrylevel"]+1;
		$newweaponry = $newweaponrylevel*100;
		$newcastlegold = $castlerow["gold"] + ($castlerow["weaponrylevel"]*390);
		$newgold = $castlerow["gold"] - ($castlerow["weaponrylevel"]*39000);
		$cu = doquery("UPDATE {{table}} SET weaponrylevel='$newweaponrylevel',weaponry='$newweaponry',gold='$newgold' WHERE id='".$castlerow["id"]."' LIMIT 1", "strongholds");
		$page .= "<p>You upgraded this stronghold's Weaponry!<p>";
		$castlerow["magiclevel"] += 1;
	}
	$page .= "<p>Gold will be taken from the Strongholds Storage to upgrade your Stronghold.<p>This stronghold currently has <b>".$newcastlegold."</b> gold available.<br>";
	$page .= "The Stronghold currently has <b>".$castlerow["currenthp"]."</b> structural points left out ";
	$page .= "of <b>".$castlerow["maxhp"]."</b> total.<p>";
	$page .= "<table>";
	if ($castlerow["armorlevel"] <= 29) {
	$page .= "<tr><td>The current Armor level is: <b>".$castlerow["armorlevel"]."</b>.<br> It will cost ".($castlerow["armorlevel"]*42500)." gold to upgrade this stronghold's armor.</td>";
	$page .= "<td><form action='strongholds.php?do=defense' method='POST'>";
	$page .= "<input type='submit' name='armorup' value='Upgrade Armor'></form></td></tr>";
	} else {
	$page .= "<tr><td>The current Armor level is: <b>".$castlerow["armorlevel"]."</b>.<br>";
	$page .= "You are unable to improve the Armor rating any further.</td>";
	$page .= "<td> </td></tr>";
	}
	if ($castlerow["magiclevel"] <= 29) {
	$page .= "<tr><td>The current Magic level is: <b>".$castlerow["magiclevel"]."</b>.<br> It will cost ".($castlerow["magiclevel"]*35000)." gold to upgrade this stronghold's magic.</td>";
	$page .= "<td><form action='strongholds.php?do=defense' method='POST'>";
	$page .= "<input type='submit' name='magicup' value='Upgrade Magic'></form></td></tr>";
	} else {
	$page .= "<tr><td>The current Magic level is: <b>".$castlerow["magiclevel"]."</b>.<br>";
	$page .= "You are unable to improve the Magic rating any further.</td>";
	$page .= "<td> </td></tr>";
	}
	if ($castlerow["weaponrylevel"] <= 29) {
	$page .= "<tr><td>The current Weaponry level is: <b>".$castlerow["weaponrylevel"]."</b>.<br> It will cost ".($castlerow["weaponrylevel"]*39000)." gold to upgrade this stronghold's weaponry.</td>";
	$page .= "<td><form action='strongholds.php?do=defense' method='POST'>";
	$page .= "<input type='submit' name='weaponup' value='Upgrade Weapons'></form></td></tr>";
	} else {
	$page .= "<tr><td>The current Weaponry level is: <b>".$castlerow["magiclevel"]."</b>.<br>";
	$page .= "You are unable to improve the Weapons rating any further.</td>";
	$page .= "<td> </td></tr>";
	}
	$page .= "</table><p>";
	$page .= "<center><br><a href='strongholds.php'>Back to the Stronghold</a></center>";
    display($page,"Fortifying a Stronghold");
}

function gforum() {
    
    global $userrow,$controlrow;
if ($userrow["guildrank"] < 100) { header("Location: strongholds.php"); die();}
$updatequery = doquery("UPDATE {{table}} SET location='Editing Guild Forum' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

    $statquery = doquery("SELECT * FROM {{table}} ORDER BY id DESC LIMIT 1", "gforum");
    $statrow = mysql_fetch_array($statquery);

    $query = doquery("SELECT id,title,author,guildname FROM {{table}} WHERE guildname='".$userrow["guildname"]."' ORDER BY id", "gforum");
$page = "<table width=\"100%\"><tr><td class=\"title\">Edit Guild Forum</td></tr></table><p>";

    $page .= "Click a post or authors name to edit it. <u>Do not</u> edit the Forum unless you are sure of what you are doing. If you screw it up, its not my problem.<br /><br /><table width=\"50%\">\n";
    $count = 1;
    while ($row = mysql_fetch_array($query)) {
        if ($count == 1) { $page .= "<tr><td style=\"background-color: #eeeeee;\"><a href=\"strongholds.php?do=editgforum:".$row["id"]."\">".$row["title"]."</a></td><td style=\"background-color: #eeeeee;\"><a href=\"strongholds.php?do=editgforum:".$row["id"]."\">".$row["author"]."</a></td></tr>\n"; $count = 2; }
        else { $page .= "<tr><td style=\"background-color: #ffffff;\"><a href=\"strongholds.php?do=editgforum:".$row["id"]."\">".$row["title"]."</a></td><td style=\"background-color: #ffffff;\"><a href=\"strongholds.php?do=editgforum:".$row["id"]."\">".$row["author"]."</a></td></tr>\n"; $count = 1; }
    }
    if (mysql_num_rows($query) == 0) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">No posts found.</td></tr>\n"; }
    $page .= "</table>";
$page .= "<center><br><a href='strongholds.php'>Back to the Stronghold</a></center>";
    display($page, "Edit Guild Forum");
    
}

function editgforum($id) {
global $userrow;
    if ($userrow["guildrank"] < 100) { header("Location: strongholds.php"); die();}
    if (isset($_POST["submit"])) {
        
        extract($_POST);
        $errors = 0;
        $errorlist = "";
        if ($title == "") { $errors++; $errorlist .= "Title is required.<br />"; }
        if ($author == "") { $errors++; $errorlist .= "Author is required.<br />"; }
        if ($content == "") { $errors++; $errorlist .= "Content is required.<br />"; }

        
        if ($errors == 0) { 
            $query = doquery("UPDATE {{table}} SET author='$author',replies='$replies',title='$title',content='$content',close='$close',pin='$pin' WHERE id='$id' LIMIT 1", "gforum");
            display("Post updated. Return to the <a href='strongholds.php'>Stronghold</a> or edit some <a href='strongholds.php?do=gforum'>more</a>.","Edit Guild Forum");
        } else {
            display("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Guild Forum");
        }        
        
    }   
        
    
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "gforum");
    $row = mysql_fetch_array($query);

$page = <<<END
<p><u>Do not</u> edit the Forum unless you are sure of what you are doing. If you screw it up, its not my problem.<br /><br /><table width=\"50%\">
<form action="strongholds.php?do=editgforum:$id" method="post">

<table width="90%">
<tr><td width="20%">Author:</td><td><input type="text" name="author" size="30" maxlength="30" value="{{author}}" /><br>Please dont edit this unless its neccessary</td></tr>
<tr><td width="20%">Close Thread:</td><td><input type="text" name="close" size="2" maxlength="2" value="{{close}}" /><br>1 = Close / 0 = Open. Please ensure you close the <u>thread starter post</u>. ie: The first post which started this thread.</td></tr>
<tr><td width="20%">Pin Thread:</td><td><input type="text" name="pin" size="2" maxlength="2" value="{{pin}}" /><br>1 = Pinned / 0 = Unpinned. Please ensure you Pin the <u>thread starter post</u>. ie: The first post which started this thread.</td></tr>
<tr><td width="20%">Title:</td><td><input type="text" name="title" size="50" maxlength="50" value="{{title}}" /></td></tr>
<tr><td width="20%">Content:</td><td><textarea name="content" rows="7" cols="40">{{content}}</textarea><br>If editing a post, you may want to put that you have edited it so the author knows</td></tr>

</table>
<input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" />
</form>
END;
   	$page .= "<center><br><a href='strongholds.php'>Back to the Stronghold</a></center>";
    $page = parsetemplate($page, $row);
    display($page, "Edit Guild Forum");
    
}

function donate() {
    global $userrow;

$updatequery = doquery("UPDATE {{table}} SET location='Stronghold Storage' WHERE id='".$userrow["id"]."' LIMIT 1", "users");


    if (isset($_POST["submit"])) {
		if ($_POST["dscales"] != '') {
		  $deposit = abs(intval($_POST["dscales"]));
		  if ($userrow["dscales"] < $deposit) {
		    $page = "<table width=\"100%\"><tr><td class=\"title\">Stronghold Storage</td></tr></table>";
		  	$page .= "<br><b>You do not have ".$deposit." Dragon Scales to donate!</b><br><br>";
	  		$page .= "You may return to <a href='strongholds.php?do=donate'>donation</a>, go back ";
	  		$page .= "to the <a href='strongholds.php'>Stronghold</a>, ";
	  		$page .= "or leave and <a href='index.php'>continue exploring</a>.";
	  		  display($page, "Stronghold Storage");
		  }
		  $g = doquery("SELECT * FROM {{table}} WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
		  $grow = mysql_fetch_array($g);
        	$newdscales = $userrow["dscales"] - $deposit;
	  		$dscales = $grow["dscales"] + $deposit;
        	if ($dscales > 999999) {
			$tmpgold = $dscales - 999999;
			$newdscales += $tmpgold;
			$dscales = 999999;
			}
        $query = doquery("UPDATE {{table}} SET dscales='$newdscales' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $query = doquery("UPDATE {{table}} SET dscales='$dscales' WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
    	$page = "<table width='100%'><tr><td class='title'>Guild Vault.</td></tr></table>";
        $page .= "You have donated ".$deposit." Dragon Scales to your Guild.<br />";
	  	$page .= "The current balance for your Guild is: <b>".$dscales."</b> Dragon Scales.<br /><br />";
	  	$page .= "You may return to <a href='strongholds.php?do=donate'>donations</a>, go back ";
	  	$page .= "to the <a href='strongholds.php'>Stronghold</a>, ";
	  	$page .= "or leave and <a href='index.php'>continue exploring</a>.";
	  			  $s = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "strongholds");
		  $srow = mysql_fetch_array($s);
	  					$logdate = "cycle_".date(W);
						$logpath = "./logs/guild".$srow["guildid"]."/";
						if (!is_dir($logpath)) {
					 	@mkdir($logpath,0777);
				     	@chmod($logpath,0777);
		    	}
				$logfile =  $logpath.$logdate.".log";
				$logcomments = "--<b>".$userrow["charname"]."</b> Donated ".$deposit." Dragon Scales to your Guild ";
				$logcomments .= " on ".date("r").". <br>\r\n";
				$fp = fopen("$logfile", "a");
				fwrite($fp, "$logcomments");
				fclose ($fp);

	  	    display($page, "Guild Donation");
		} elseif ($_POST["gold"] != '') {
		  $deposit = abs(intval($_POST["gold"]));
		  if ($userrow["gold"] < $deposit) {
		    $page = "<table width='100%'><tr><td class='title'>Stronghold Storage</td></tr></table>";
		  	$page .= "<br><b>You do not have ".$deposit." gold to donate!</b><br><br>";
	  		$page .= "You may return to <a href='strongholds.php?do=donate'>donation</a>, go back ";
	  		$page .= "to the <a href='strongholds.php'>Stronghold</a>, ";
	  		$page .= "or leave and <a href='index.php'>continue exploring</a>.";
	  		  display($page, "Stronghold Storage");
		  }
		  $s = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "strongholds");
		  $srow = mysql_fetch_array($s);
        	$newgold = $userrow["gold"] - $deposit;
	  		$guildgold = $srow["gold"] + $deposit;
        	if ($guildgold > 999999) {
		$tmpgold = $guildgold - 999999;
		$newgold += $tmpgold;
		$guildgold = 999999;
		}
        $q1 = doquery("UPDATE {{table}} SET gold='$newgold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $q2 = doquery("UPDATE {{table}} SET gold='$guildgold' WHERE id='".$srow["id"]."' LIMIT 1", "strongholds");
    	$page = "<table width='100%'><tr><td class='title'>Stronghold Storage</td></tr></table>";
        $page .= "You have donated ".$deposit." gold to this Stronghold.<br />";
	  	$page .= "The current balance for this Stronghold is: <b>".$guildgold."</b> gold.<br /><br />";
	  	$page .= "You may return to <a href='strongholds.php?do=donate'>donations</a>, go back ";
	  	$page .= "to the <a href='strongholds.php'>Stronghold</a>, ";
	  	$page .= "or leave and <a href='index.php'>continue exploring</a>.";
	  					$logdate = "cycle_".date(W);
						$logpath = "./logs/guild".$srow["guildid"]."/";
						if (!is_dir($logpath)) {
					 	@mkdir($logpath,0777);
				     	@chmod($logpath,0777);
				    	}
						$logfile =  $logpath.$logdate.".log";
						$logcomments = "--<b>".$userrow["charname"]."</b> Donated ".$deposit." Gold to your stronghold at ";
						$logcomments .= $userrow["latitude"].",".$userrow["longitude"]." on ".date("r");
						$logcomments .= ". <br>\r\n";
						$fp = fopen("$logfile", "a");
						fwrite($fp, "$logcomments");
				fclose ($fp);
	  	    display($page, "Stronghold Storage");
		}
    } elseif (isset($_POST["cancel"])) {

        header("Location: strongholds.php"); die();
    } else {
    	$title = "Guild Donation";
    $s = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "strongholds");
	$srow = mysql_fetch_array($s);
	$g = doquery("SELECT * FROM {{table}} WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
	$grow = mysql_fetch_array($g);
	$page = "<table width='100%'><tr><td class='title'>Stronghold Storage</td></tr></table>";
	$page .= "Welcome to the Stronghold Storage.  Here you can share your hard-earned gold or ";
	$page .= "Dragon Scales with this Stronghold.  Dragon Scales are used for various Guild abilities such as building ";
	$page .= "Strongholds and other features. Dragon Scales are shared amongst Strongholds, where as Gold isn't.<br>";
	$page .= "Gold is donated to the specific Stronghold you are in right now, and <b>not</b> shared globally ";
	$page .= "with other Guild Strongholds.  Gold is used to keep Strongholds in good repair, ";
	$page .= "and recruit units to attack others. You cannot withdraw anything from here.<p>";
	$page .= "Your Guild currently has <b>".$grow["dscales"]." Dragon Scales</b> available.<br>";
	$page .= "Your Stronghold currently has <b>".$srow["gold"]." gold</b> available.<br>";
	$page .= "<table><tr><td>";
	$page .= "<table><tr><td><form action='strongholds.php?do=donate' method='post'>";
	$page .= "Deposit Dragon Scales</td></tr><tr><td>Enter Amount:<br>";
	$page .= "<input type='text' size='15' name='dscales' value='".$userrow["dscales"]."'><br>";
	$page .= "<input type='submit' name='submit' value='Donate Dragon Scales'>";
	$page .= "</form></td></tr></table></td>";
	$page .= "<td><table><tr><td><form action='strongholds.php?do=donate' method='post'>";
	$page .= "Deposit Gold</td></tr><tr><td>Enter Amount:<br>";
	$page .= "<input type='text' size='15' name='gold' value='".$userrow["gold"]."'><br>";
	$page .= "<input type='submit' name='submit' value='Donate Gold'>";
	$page .= "</form></td></tr></table></td></tr></table>";
	$page .= "<br />You may return to the <a href=\"strongholds.php\">Stronghold</a>, ";
	$page .= "or leave and <a href='index.php'>continue exploring</a>.";
	  	    display($page, "Stronghold Storage");
    }
}

?>