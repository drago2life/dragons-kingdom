<?php // guilds.php :: Guild Courtyard

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

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

$updatequery = doquery("UPDATE {{table}} SET location='Guild Courtyard' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
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
                elseif ($do[0] == "create") { docreate($do[1]); }
	elseif ($do[0] == "join") { dojoin($do[1]); }
	elseif ($do[0] == "view") { doview($do[1]); }
	elseif ($do[0] == "build") { dobuild($do[1]); }
	elseif ($do[0] == "build2") { dobuild2(); }
	elseif ($do[0] == "restap") { dorestap(); }
	elseif ($do[0] == "signup") { dosignup($do[1]); }
	elseif ($do[0] == "passsign") { dosignuppass($do[1]); }
	elseif ($do[0] == "leave") { doleave(); }
	elseif ($do[0] == "vault") { dovault(); }
	elseif ($do[0] == "warp") { dowarp($do[1]); }
	elseif ($do[0] == "users") { dolistmembers($do[1]); }
	elseif ($do[0] == "news") { donews($do[1]); }
	elseif ($do[0] == "temple") { dotemple(); }
elseif ($do[0] == "pools") { pools(); }
elseif ($do[0] == "explist") { explist(); }

} else { donothing(); }


function donothing() {
	global $userrow;

  	$page = "<table width=\"100%\"><tr><td class=\"title\">Guild Courtyard</td></tr></table>";

if (($userrow["guildname"] != "-") && ($userrow["guildname"] != "")) {
	
	    $gquery = doquery("SELECT * FROM {{table}} WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
	$grow = mysql_fetch_array($gquery);
	
$page .= <<<END
<p>Welcome to your Guild menu, please choose your option from the links below. If you do not have a Stronghold yet, then you should build one immediately so that you can access other areas.<p>
<table>
<tr><th>Guild Menu:</th></tr>
<tr><td>
<ul>
<li /><a href="guilds.php?do=users">List Members</a>
<li /><a href="guilds.php?do=explist">Experience Pools</a>
<li /><a href="guilds.php?do=news">Guild News</a>
<li /><a href="gforum.php">Guild Forum</a>
<li /><a href="guilds.php?do=temple">Guild Temple</a>
<li /><a href="guilds.php?do=vault">Guild Vaults</a>
<li /><a href="guilds.php?do=restap">Restore AP</a>
<li /><a href="guilds.php?do=warp">Stronghold Portal</a>
END;
if ($userrow["guildrank"] >= 150) {
$page .= "<li><a href='guilds.php?do=pools'>Distribute Experience</a><li><a href='guilds.php?do=build'>Build a Stronghold</a>";}
$page .= 
<<<END
<li /><a href="guilds.php?do=view">View other Guilds</a><p>
<li /><a href="guilds.php?do=leave">Leave the Guild</a>
</ul><p>Only the Leader and Co Leader has access to other areas of a Guild.
</td></tr>

</table>
<center><br><a href='index.php'>Back to Town</a></center>
END;

}else {   // if not a member of a guild.

$page .= <<<END
<p>Welcome to the Guild Courtyard. It appears you are not currently in a Guild. To join a guild, simply click below to view a list of current guilds available.<p>Features you get from having a Guild is that you can build Strongholds to battle and defend against your enemies, not to mention all the extra features you receive within a Guild and Stronghold such as the Pet Arena. You require <b>30 AP, 500k gold and 1500 Dragon Scales</b> to construct a guild of your own. You do however receive 2750 free Dragon Scales once the Guild is built which is used to build your first Stronghold.<p>

<table>
<tr><th>Guild Menu:</th></tr>
<tr><td>
<ul>
<li /><a href="guilds.php?do=join">Join Guild</a>
<li /><a href="guilds.php?do=create">Create Guild</a>
</ul>
</td></tr>
</table>
<center><br><a href='index.php'>Back to town</a></center>
END;
}
    display($page,"Guild Courtyard");
}

function pools() { // Exp Pools

    global $userrow, $numqueries;

    if ($userrow["guildrank"] < 150) { header("Location: guilds.php"); die();}
    
    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
    
    $gquery = doquery("SELECT * FROM {{table}} WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
	$grow = mysql_fetch_array($gquery);
	

$updatequery = doquery("UPDATE {{table}} SET location='Distributing Experience' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
 

        $title = "Experience Pools";
        $page = "<table width='100%' border='1'><tr><td class='title'>Experience Pools</td></tr></table><p>";
        $page .= "Experience: <b>" . number_format($grow["exp_pool"]). "</b><br />Mining: <b>" . number_format($grow["mine_pool"]). "</b><br />Smelting: <b>" . number_format($grow["smelt_pool"]). "</b><br />Forging: <b>" . number_format($grow["forge_pool"]). "</b><br />Endurance: <b>" . number_format($grow["endurance_pool"]). "</b><br />Crafting: <b>" . number_format($grow["craft_pool"]). "</b><br />Prayer: <b>" . number_format($grow["prayer_pool"]). "</b><p>You may send experience to any member of your guild by typing in their ID number below and the desired amount of experience. A small amount of experience is collected from each Guild member as they battle Monsters or train Skills which then collects into the Pools below, ready to be shared out to your Guild.</b><p><font color=red>Note: All transfers are logged in the Guild events. If you abuse this system by sending lots of experience to yourself, then you may face a possible ban for cheating.</font><br /><br />\n";
     
        
                $page .= "<form action=\"guilds.php?do=pools\" method=\"post\">\n";
        $page .= "<b>ID Number</b>:<br><input type=\"text\" name=\"id\" value=\"0\" /><br /><b>Enter the amount you wish to Transfer</b>:<br /><input type=\"text\" name=\"amount\" value=\"".$grow["exp_pool"]."\" /><br /><input type=\"submit\" name=\"exp_pool\" value=\"Transfer Experience\" /><p>\n";
        $page .= "</form>\n";
           
        $page .= "<form action=\"guilds.php?do=pools\" method=\"post\">\n";
        $page .= "<b>ID Number</b>:<br><input type=\"text\" name=\"id\" value=\"0\" /><br /><b>Enter the amount you wish to Transfer</b>:<br /><input type=\"text\" name=\"amount\" value=\"".$grow["mine_pool"]."\" /><br /><input type=\"submit\" name=\"mine_pool\" value=\"Transfer Mining Experience\" /><p>\n";
        $page .= "</form>\n";
        
                $page .= "<form action=\"guilds.php?do=pools\" method=\"post\">\n";
        $page .= "<b>ID Number</b>:<br><input type=\"text\" name=\"id\" value=\"0\" /><br /><b>Enter the amount you wish to Transfer</b>:<br /><input type=\"text\" name=\"amount\" value=\"".$grow["smelt_pool"]."\" /><br /><input type=\"submit\" name=\"smelt_pool\" value=\"Transfer Smelting Experience\" /><p>\n";
        $page .= "</form>\n";
        
        
          $page .= "<form action=\"guilds.php?do=pools\" method=\"post\">\n";
        $page .= "<b>ID Number</b>:<br><input type=\"text\" name=\"id\" value=\"0\" /><br /><b>Enter the amount you wish to Transfer</b>:<br /><input type=\"text\" name=\"amount\" value=\"".$grow["forge_pool"]."\" /><br /><input type=\"submit\" name=\"forge_pool\" value=\"Transfer Forging Experience\" /><p>\n";
        $page .= "</form>\n";
        
                $page .= "<form action=\"guilds.php?do=pools\" method=\"post\">\n";
        $page .= "<b>ID Number</b>:<br><input type=\"text\" name=\"id\" value=\"0\" /><br /><b>Enter the amount you wish to Transfer</b>:<br /><input type=\"text\" name=\"amount\" value=\"".$grow["endurance_pool"]."\" /><br /><input type=\"submit\" name=\"endurance_pool\" value=\"Transfer Endurance Experience\" /><p>\n";
        $page .= "</form>\n";
        
                $page .= "<form action=\"guilds.php?do=pools\" method=\"post\">\n";
        $page .= "<b>ID Number</b>:<br><input type=\"text\" name=\"id\" value=\"0\" /><br /><b>Enter the amount you wish to Transfer</b>:<br /><input type=\"text\" name=\"amount\" value=\"".$grow["craft_pool"]."\" /><br /><input type=\"submit\" name=\"craft_pool\" value=\"Transfer Crafting Experience\" /><p>\n";
        $page .= "</form>\n";
        
                 $page .= "<form action=\"guilds.php?do=pools\" method=\"post\">\n";
        $page .= "<b>ID Number</b>:<br><input type=\"text\" name=\"id\" value=\"0\" /><br /><b>Enter the amount you wish to Transfer</b>:<br /><input type=\"text\" name=\"amount\" value=\"".$grow["prayer_pool"]."\" /><br /><input type=\"submit\" name=\"prayer_pool\" value=\"Transfer Prayer Experience\" /><p>\n";
        $page .= "</form>\n";       
        
        $page .= "</form><p>If you've changed your mind, you may return back to the <a href=\"guilds.php\">guild courtyard</a>\n";
       
        
if(isset($_POST['exp_pool']))
  {

    $yourstats="SELECT * from dk_guilds where name='".$userrow["guildname"]."'";
    $yourstats2=mysql_query($yourstats) or die("Could not get stats");
    $yourstats3=mysql_fetch_array($yourstats2);
    $id=$_POST['id'];
    $id=strip_tags($id);
    $oppstats="SELECT * from dk_users where id='$id'";
    $oppstats2=mysql_query($oppstats) or die("Could not get player's stats");
    $oppstats3=mysql_fetch_array($oppstats2);

    if($yourstats3["name"]!=$oppstats3["guildname"])
    {
       $page = "You cant send any experience to this player because this person is not in your guild.<br /><br />You may return to the guilds <a href=\"guilds.php?do=pools\">experience pools</a>, or use the compass images on the right to start exploring.";

    }
else {
$postamount = $_POST['amount'];
if($postamount > $yourstats3["exp_pool"] || $postamount < 0) {
$page = "You don't have that much experience in the Guilds experience pool to send.<br /><br />You may return to the guilds <a href=\"guilds.php?do=pools\">experience pools</a>, or use the compass images on the right to start exploring.";
}
else {

$newoppbank = $oppstats3["exp_pool"] + $postamount;

  $yournewbank = $yourstats3["exp_pool"] - $postamount;
 $updateyourstats="update dk_guilds set exp_pool='$yournewbank' where name='".$userrow["guildname"]."'";
        mysql_query($updateyourstats) or die("Could not update stats");
        $updateopp="update dk_users set experience=experience+'$newoppbank' where id='$id'";
        mysql_query($updateopp) or die(mysql_error());
        $page = "You have transfered the <b>$postamount</b> experience successfully from the Guilds experience pool to <b>".$oppstats3["charname"]."</b>.<br /><br />You may continue distributing <a href=\"guilds.php?do=pools\">more experience</a>, or use the compass images on the right to start exploring.";

    $gquery = doquery("SELECT id FROM {{table}} WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
	$grow = mysql_fetch_array($gquery);
	
	  					$logdate = exp_handouts;
						$logpath = "./logs/guild".$grow["id"]."/";
						if (!is_dir($logpath)) {
					 	@mkdir($logpath,0777);
				     	@chmod($logpath,0777);
		    	}
				$logfile =  $logpath.$logdate.".log";
				$logcomments = "--".$userrow["charname"]." sent <b>".$postamount." Experience to ".$oppstats3["charname"]."</b>";
				$logcomments .= " on ".date("r").". <br>\r\n";
				$fp = fopen("$logfile", "a");
				fwrite($fp, "$logcomments");
				fclose ($fp);
        
}
}
}
  
  if(isset($_POST['mine_pool']))
  {

    $yourstats="SELECT * from dk_guilds where name='".$userrow["guildname"]."'";
    $yourstats2=mysql_query($yourstats) or die("Could not get stats");
    $yourstats3=mysql_fetch_array($yourstats2);
    $id=$_POST['id'];
    $id=strip_tags($id);
    $oppstats="SELECT * from dk_users where id='$id'";
    $oppstats2=mysql_query($oppstats) or die("Could not get player's stats");
    $oppstats3=mysql_fetch_array($oppstats2);

    if($yourstats3["name"]!=$oppstats3["guildname"])
    {
       $page = "You cant send any experience to this player because this person is not in your guild.<br /><br />You may return to the guilds <a href=\"guilds.php?do=pools\">experience pools</a>, or use the compass images on the right to start exploring.";

    }
else {
$postamount = $_POST['amount'];
if($postamount > $yourstats3["mine_pool"] || $postamount < 0) {
$page = "You don't have that much Mining experience in the Guilds experience pool to send.<br /><br />You may return to the guilds <a href=\"guilds.php?do=pools\">experience pools</a>, or use the compass images on the right to start exploring.";
}
else {

$newoppbank = $oppstats3["mine_pool"] + $postamount;

  $yournewbank = $yourstats3["mine_pool"] - $postamount;
 $updateyourstats="update dk_guilds set mine_pool='$yournewbank' where name='".$userrow["guildname"]."'";
        mysql_query($updateyourstats) or die("Could not update stats");
        $updateopp="update dk_users set miningxp=miningxp+'$newoppbank' where id='$id'";
        mysql_query($updateopp) or die(mysql_error());
        $page = "You have transfered the <b>$postamount</b> Mining experience successfully from the Guilds experience pool to <b>".$oppstats3["charname"]."</b>.<br /><br />You may continue distributing <a href=\"guilds.php?do=pools\">more experience</a>, or use the compass images on the right to start exploring.";

    $gquery = doquery("SELECT id FROM {{table}} WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
	$grow = mysql_fetch_array($gquery);
	
	  					$logdate = exp_handouts;
						$logpath = "./logs/guild".$grow["id"]."/";
						if (!is_dir($logpath)) {
					 	@mkdir($logpath,0777);
				     	@chmod($logpath,0777);
		    	}
				$logfile =  $logpath.$logdate.".log";
				$logcomments = "--".$userrow["charname"]." sent <b>".$postamount." Mining Experience to ".$oppstats3["charname"]."</b>";
				$logcomments .= " on ".date("r").". <br>\r\n";
				$fp = fopen("$logfile", "a");
				fwrite($fp, "$logcomments");
				fclose ($fp);
        
}

}}

  if(isset($_POST['smelt_pool']))
  {

    $yourstats="SELECT * from dk_guilds where name='".$userrow["guildname"]."'";
    $yourstats2=mysql_query($yourstats) or die("Could not get stats");
    $yourstats3=mysql_fetch_array($yourstats2);
    $id=$_POST['id'];
    $id=strip_tags($id);
    $oppstats="SELECT * from dk_users where id='$id'";
    $oppstats2=mysql_query($oppstats) or die("Could not get player's stats");
    $oppstats3=mysql_fetch_array($oppstats2);

    if($yourstats3["name"]!=$oppstats3["guildname"])
    {
       $page = "You cant send any experience to this player because this person is not in your guild.<br /><br />You may return to the guilds <a href=\"guilds.php?do=pools\">experience pools</a>, or use the compass images on the right to start exploring.";

    }
else {
$postamount = $_POST['amount'];
if($postamount > $yourstats3["smelt_pool"] || $postamount < 0) {
$page = "You don't have that much Smelting experience in the Guilds experience pool to send.<br /><br />You may return to the guilds <a href=\"guilds.php?do=pools\">experience pools</a>, or use the compass images on the right to start exploring.";
}
else {

$newoppbank = $oppstats3["smelt_pool"] + $postamount;

  $yournewbank = $yourstats3["smelt_pool"] - $postamount;
 $updateyourstats="update dk_guilds set smelt_pool='$yournewbank' where name='".$userrow["guildname"]."'";
        mysql_query($updateyourstats) or die("Could not update stats");
        $updateopp="update dk_users set smeltingxp=smeltingxp+'$newoppbank' where id='$id'";
        mysql_query($updateopp) or die(mysql_error());
        $page = "You have transfered the <b>$postamount</b> Smelting experience successfully from the Guilds experience pool to <b>".$oppstats3["charname"]."</b>.<br /><br />You may continue distributing <a href=\"guilds.php?do=pools\">more experience</a>, or use the compass images on the right to start exploring.";

        
            $gquery = doquery("SELECT id FROM {{table}} WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
	$grow = mysql_fetch_array($gquery);
	
	  					$logdate = exp_handouts;
						$logpath = "./logs/guild".$grow["id"]."/";
						if (!is_dir($logpath)) {
					 	@mkdir($logpath,0777);
				     	@chmod($logpath,0777);
		    	}
				$logfile =  $logpath.$logdate.".log";
				$logcomments = "--".$userrow["charname"]." sent <b>".$postamount." Smelting Experience to ".$oppstats3["charname"]."</b>";
				$logcomments .= " on ".date("r").". <br>\r\n";
				$fp = fopen("$logfile", "a");
				fwrite($fp, "$logcomments");
				fclose ($fp);

}

}}


if(isset($_POST['forge_pool']))
  {

    $yourstats="SELECT * from dk_guilds where name='".$userrow["guildname"]."'";
    $yourstats2=mysql_query($yourstats) or die("Could not get stats");
    $yourstats3=mysql_fetch_array($yourstats2);
    $id=$_POST['id'];
    $id=strip_tags($id);
    $oppstats="SELECT * from dk_users where id='$id'";
    $oppstats2=mysql_query($oppstats) or die("Could not get player's stats");
    $oppstats3=mysql_fetch_array($oppstats2);

    if($yourstats3["name"]!=$oppstats3["guildname"])
    {
       $page = "You cant send any experience to this player because this person is not in your guild.<br /><br />You may return to the guilds <a href=\"guilds.php?do=pools\">experience pools</a>, or use the compass images on the right to start exploring.";

    }
else {
$postamount = $_POST['amount'];
if($postamount > $yourstats3["forge_pool"] || $postamount < 0) {
$page = "You don't have that much Forging experience in the Guilds experience pool to send.<br /><br />You may return to the guilds <a href=\"guilds.php?do=pools\">experience pools</a>, or use the compass images on the right to start exploring.";
}
else {

$newoppbank = $oppstats3["forge_pool"] + $postamount;

  $yournewbank = $yourstats3["forge_pool"] - $postamount;
 $updateyourstats="update dk_guilds set forge_pool='$yournewbank' where name='".$userrow["guildname"]."'";
        mysql_query($updateyourstats) or die("Could not update stats");
        $updateopp="update dk_users set forgingxp=forgingxp+'$newoppbank' where id='$id'";
        mysql_query($updateopp) or die(mysql_error());
        $page = "You have transfered the <b>$postamount</b> Forging experience successfully from the Guilds experience pool to <b>".$oppstats3["charname"]."</b>.<br /><br />You may continue distributing <a href=\"guilds.php?do=pools\">more experience</a>, or use the compass images on the right to start exploring.";

        
            $gquery = doquery("SELECT id FROM {{table}} WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
	$grow = mysql_fetch_array($gquery);
	
	  					$logdate = exp_handouts;
						$logpath = "./logs/guild".$grow["id"]."/";
						if (!is_dir($logpath)) {
					 	@mkdir($logpath,0777);
				     	@chmod($logpath,0777);
		    	}
				$logfile =  $logpath.$logdate.".log";
				$logcomments = "--".$userrow["charname"]." sent <b>".$postamount." Forging Experience to ".$oppstats3["charname"]."</b>";
				$logcomments .= " on ".date("r").". <br>\r\n";
				$fp = fopen("$logfile", "a");
				fwrite($fp, "$logcomments");
				fclose ($fp);

}

}}

  if(isset($_POST['endurance_pool']))
  {

    $yourstats="SELECT * from dk_guilds where name='".$userrow["guildname"]."'";
    $yourstats2=mysql_query($yourstats) or die("Could not get stats");
    $yourstats3=mysql_fetch_array($yourstats2);
    $id=$_POST['id'];
    $id=strip_tags($id);
    $oppstats="SELECT * from dk_users where id='$id'";
    $oppstats2=mysql_query($oppstats) or die("Could not get player's stats");
    $oppstats3=mysql_fetch_array($oppstats2);

    if($yourstats3["name"]!=$oppstats3["guildname"])
    {
       $page = "You cant send any experience to this player because this person is not in your guild.<br /><br />You may return to the guilds <a href=\"guilds.php?do=pools\">experience pools</a>, or use the compass images on the right to start exploring.";

    }
else {
$postamount = $_POST['amount'];
if($postamount > $yourstats3["endurance_pool"] || $postamount < 0) {
$page = "You don't have that much Endurance experience in the Guilds experience pool to send.<br /><br />You may return to the guilds <a href=\"guilds.php?do=pools\">experience pools</a>, or use the compass images on the right to start exploring.";
}
else {

$newoppbank = $oppstats3["endurance_pool"] + $postamount;

  $yournewbank = $yourstats3["endurance_pool"] - $postamount;
 $updateyourstats="update dk_guilds set endurance_pool='$yournewbank' where name='".$userrow["guildname"]."'";
        mysql_query($updateyourstats) or die("Could not update stats");
        $updateopp="update dk_users set endurancexp=endurancexp+'$newoppbank' where id='$id'";
        mysql_query($updateopp) or die(mysql_error());
        $page = "You have transfered the <b>$postamount</b> Endurance experience successfully from the Guilds experience pool to <b>".$oppstats3["charname"]."</b>.<br /><br />You may continue distributing <a href=\"guilds.php?do=pools\">more experience</a>, or use the compass images on the right to start exploring.";


            $gquery = doquery("SELECT id FROM {{table}} WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
	$grow = mysql_fetch_array($gquery);
	
	  					$logdate = exp_handouts;
						$logpath = "./logs/guild".$grow["id"]."/";
						if (!is_dir($logpath)) {
					 	@mkdir($logpath,0777);
				     	@chmod($logpath,0777);
		    	}
				$logfile =  $logpath.$logdate.".log";
				$logcomments = "--".$userrow["charname"]." sent <b>".$postamount." Endurance Experience to ".$oppstats3["charname"]."</b>";
				$logcomments .= " on ".date("r").". <br>\r\n";
				$fp = fopen("$logfile", "a");
				fwrite($fp, "$logcomments");
				fclose ($fp);
        
}

}}

 if(isset($_POST['prayer_pool']))
  {

    $yourstats="SELECT * from dk_guilds where name='".$userrow["guildname"]."'";
    $yourstats2=mysql_query($yourstats) or die("Could not get stats");
    $yourstats3=mysql_fetch_array($yourstats2);
    $id=$_POST['id'];
    $id=strip_tags($id);
    $oppstats="SELECT * from dk_users where id='$id'";
    $oppstats2=mysql_query($oppstats) or die("Could not get player's stats");
    $oppstats3=mysql_fetch_array($oppstats2);

    if($yourstats3["name"]!=$oppstats3["guildname"])
    {
       $page = "You cant send any experience to this player because this person is not in your guild.<br /><br />You may return to the guilds <a href=\"guilds.php?do=pools\">experience pools</a>, or use the compass images on the right to start exploring.";

    }
else {
$postamount = $_POST['amount'];
if($postamount > $yourstats3["prayer_pool"] || $postamount < 0) {
$page = "You don't have that much Prayer experience in the Guilds experience pool to send.<br /><br />You may return to the guilds <a href=\"guilds.php?do=pools\">experience pools</a>, or use the compass images on the right to start exploring.";
}
else {

$newoppbank = $oppstats3["prayer_pool"] + $postamount;

  $yournewbank = $yourstats3["prayer_pool"] - $postamount;
 $updateyourstats="update dk_guilds set prayer_pool='$yournewbank' where name='".$userrow["guildname"]."'";
        mysql_query($updateyourstats) or die("Could not update stats");
        $updateopp="update dk_users set prayerxp=prayerxp+'$newoppbank' where id='$id'";
        mysql_query($updateopp) or die(mysql_error());
        $page = "You have transfered the <b>$postamount</b> Prayer experience successfully from the Guilds experience pool to <b>".$oppstats3["charname"]."</b>.<br /><br />You may continue distributing <a href=\"guilds.php?do=pools\">more experience</a>, or use the compass images on the right to start exploring.";


            $gquery = doquery("SELECT id FROM {{table}} WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
	$grow = mysql_fetch_array($gquery);
	
	  					$logdate = exp_handouts;
						$logpath = "./logs/guild".$grow["id"]."/";
						if (!is_dir($logpath)) {
					 	@mkdir($logpath,0777);
				     	@chmod($logpath,0777);
		    	}
				$logfile =  $logpath.$logdate.".log";
				$logcomments = "--".$userrow["charname"]." sent <b>".$postamount." Prayer Experience to ".$oppstats3["charname"]."</b>";
				$logcomments .= " on ".date("r").". <br>\r\n";
				$fp = fopen("$logfile", "a");
				fwrite($fp, "$logcomments");
				fclose ($fp);
        
}

}}

  if(isset($_POST['craft_pool']))
  {

    $yourstats="SELECT * from dk_guilds where name='".$userrow["guildname"]."'";
    $yourstats2=mysql_query($yourstats) or die("Could not get stats");
    $yourstats3=mysql_fetch_array($yourstats2);
    $id=$_POST['id'];
    $id=strip_tags($id);
    $oppstats="SELECT * from dk_users where id='$id'";
    $oppstats2=mysql_query($oppstats) or die("Could not get player's stats");
    $oppstats3=mysql_fetch_array($oppstats2);

    if($yourstats3["name"]!=$oppstats3["guildname"])
    {
       $page = "You cant send any experience to this player because this person is not in your guild.<br /><br />You may return to the guilds <a href=\"guilds.php?do=pools\">experience pools</a>, or use the compass images on the right to start exploring.";

    }
else {
$postamount = $_POST['amount'];
if($postamount > $yourstats3["craft_pool"] || $postamount < 0) {
$page = "You don't have that much Crafting experience in the Guilds experience pool to send.<br /><br />You may return to the guilds <a href=\"guilds.php?do=pools\">experience pools</a>, or use the compass images on the right to start exploring.";
}
else {

$newoppbank = $oppstats3["craft_pool"] + $postamount;

  $yournewbank = $yourstats3["craft_pool"] - $postamount;
 $updateyourstats="update dk_guilds set craft_pool='$yournewbank' where name='".$userrow["guildname"]."'";
        mysql_query($updateyourstats) or die("Could not update stats");
        $updateopp="update dk_users set craftingxp=craftingxp+'$newoppbank' where id='$id'";
        mysql_query($updateopp) or die(mysql_error());
        $page = "You have transfered the <b>$postamount</b> Crafting experience successfully from the Guilds experience pool to <b>".$oppstats3["charname"]."</b>.<br /><br />You may continue distributing <a href=\"guilds.php?do=pools\">more experience</a>, or use the compass images on the right to start exploring.";

    $gquery = doquery("SELECT id FROM {{table}} WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
	$grow = mysql_fetch_array($gquery);
	
	  					$logdate = exp_handouts;
						$logpath = "./logs/guild".$grow["id"]."/";
						if (!is_dir($logpath)) {
					 	@mkdir($logpath,0777);
				     	@chmod($logpath,0777);
		    	}
				$logfile =  $logpath.$logdate.".log";
				$logcomments = "--".$userrow["charname"]." sent <b>".$postamount." Crafting Experience to ".$oppstats3["charname"]."</b>";
				$logcomments .= " on ".date("r").". <br>\r\n";
				$fp = fopen("$logfile", "a");
				fwrite($fp, "$logcomments");
				fclose ($fp);
        
}

}}

    display($page, $title);

}

function explist() { // List all exp pools
    
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

$updatequery = doquery("UPDATE {{table}} SET location='Experience Pools' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
            $gquery = doquery("SELECT * FROM {{table}} WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
	$grow = mysql_fetch_array($gquery);
        $title = "Experience Pools";
        $page = "<table width='100%' border='1'><tr><td class='title'>Experience Pools</td></tr></table><p>";
        $page .= "When you train up your levels in Skills and your Normal Level, you will gain a small amount of experience which is added to the following Guild Pools. When a Founder or Leader feel it is neccessary, they will give out the experience below and share it amongst all their Guild Members:<p>\n";
        $page .= "Experience: <b>" . number_format($grow["exp_pool"]). "</b><br />Mining: <b>" . number_format($grow["mine_pool"]). "</b><br />Smelting: <b>" . number_format($grow["smelt_pool"]). "</b><br />Forging: <b>" . number_format($grow["forge_pool"]). "</b><br />Endurance: <b>" . number_format($grow["endurance_pool"]). "</b><br />Crafting: <b>" . number_format($grow["craft_pool"]). "</b><br />Prayer: <b>" . number_format($grow["prayer_pool"]). "</b><br />\n";
        $page .= "<br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br />\n";      

        
    }
    
    display($page, $title);
    
}


function donews() {
	global $userrow;
	
	    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

$updatequery = doquery("UPDATE {{table}} SET location='Guild News' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

if (isset($_POST["cancel"])) {
        header("Location: guilds.php"); die();

	}
	$castlequery = doquery("SELECT * FROM {{table}} WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
	$grow = mysql_fetch_array($castlequery);
	$page = "<center><table width='100%'><tr><td class='title'>Guild News</td></tr></table></center>";

	$page .= "<p>Below is a news entry which has been posted by your Co-Leader, Leader or Founder for this Guild.<p>";
    $page .= "<center><table width='70%'>";

	$page .= "<center><td bgcolor='#ffffff'><textarea name='news' cols='50' rows='18' wrap='virtual'>";
	$page .= $grow["news"]."</textarea></td></tr>";
	$page .= "</td></tr></table></center>";

    	$page .= "<center><br><a href='guilds.php'>Return to your Guild</a></center>";
    	display($page,"Guild News");

}

function dotemple() {
    global $userrow;
$updatequery = doquery("UPDATE {{table}} SET location='Guild Temple' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
// --------------------------------------------------
// This part is for when you hit the submit button 
// --------------------------------------------------
    if (isset($_POST["submit"])) {
		  $donation = abs(intval($_POST["donation"]));
// --------------------------------------------------
// If you do not have enough gold, let them know it.
// --------------------------------------------------
		 if (($userrow["gold"] < $donation) || ($donation < 5)) {
		    $page = "<table width='100%'><tr><td class='title'>Guild Temple</td></tr></table>";
		  	$page .= "<br><b>You do not have that amount of gold to donate or you must donate a minimum of least 5 gold otherwise the Spirits won't be pleased.</b><br><br>";
	  		$page .= "You may return to <a href='guilds.php?do=temple'>the temple</a>, go back ";
	  		$page .= "to the <a href='guilds.php'>Guild</a>, ";
	  		$page .= "or leave and <a href='index.php?do=move:0'>continue Exploring</a>.";
	  		  display($page, "Guild Temple");
		  }
// --------------------------------------------------
//  assuming they have enough gold, process the donation
// --------------------------------------------------
		$guildquery = doquery("SELECT gold FROM {{table}} WHERE id='".$userrow["laststronghold"]."' LIMIT 1", "strongholds");
      	$guildrow = mysql_fetch_array($guildquery);
        	$newgold = $userrow["gold"] - $donation;
        	$randtax = intval(rand(1,6)+rand(1,6)+rand(1,6)+rand(1,6));
	  	$templeadd = intval($donation / $randtax) + 3;
	  	$templegold = $guildrow["gold"] + $templeadd;
        	$dscalesadd = $guildrow["dscales"] + rand(1,$userrow["level"]);
        	if ($newtemple > 999999) { $templegold = 999999; }
		$query = doquery("UPDATE {{table}} SET gold='$newgold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
		$query = doquery("UPDATE {{table}} SET gold='$templegold' WHERE id='".$userrow["laststronghold"]."' LIMIT 1", "strongholds");
		$gquery = doquery("SELECT dscales FROM {{table}} WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
		$grow = mysql_fetch_array($gquery);
// --------------------------------------------------
// that part above just handles the donation.
// increases the gold available to the stronghold,
// subtracts gold from user, etc.
// --------------------------------------------------
		$page = "<table width='100%'><tr><td class='title'>Guild Temple</td></tr></table>";
        	$page .= "You have donated ".$donation." gold to the Temples.<br />";

		// ------------------------------------------------------
		// -------------- Add stuff here ------------------------
		// ------------------------------------------------------
		$randomchance = rand(1,1000);
// --------------------------------------------------
//  The chance for something good happening is a random number 
// from 1-999, modified by the % of gold donated.
// so if you donate almost all your gold something good will 
// happen more often than not.  To just make it random chance
// remove the part about $favormod.
// for pure chance, change the two lines above to one line
// that simply says 
//          $randomchance=rand(1,1000);
// --------------------------------------------------
		if (($userrow["gold"] == $donation) && ($userrow["bank"] <= 3000)) {
			$page .= "<p><b>The Spirits are pleased that you have donated all your gold!</b><p>";
			$page .= "Your health and magic has been restored,";
			$page .= " Such is the fickle nature of the spirits.<br>";
	  		$page .= "You may return to <a href='guilds.php?do=temple'>the Temple</a>, go back ";
	  		$page .= "to the <a href='guilds.php'>Guild</a>, ";
	  		$page .= "or leave and <a href='index.php?do=move:0'>continue Exploring</a>.";
			$newgold = 0;
			$newhp = $userrow["maxhp"];
			$newmp = $userrow["maxmp"];
			$query = doquery("UPDATE {{table}} SET currenthp='$newhp',currentmp='$newmp',gold='$newgold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
			display($page,"Guild Temple");
		}
// --------------------------------------------------
//  the part above is if you donated all your gold
// and you have less than 5000 in the vault.
// it will restore all HP/MP.   If you have more than 5000
// in the vault, this part will not run and it will continue below.
// I removed this part in my "new" temple script, since it was 
// kinda redundant now that you can just Rest in the strongholds.
// so I would probably recomment removing this whole section 
// and concentrate on the part below.
// --------------------------------------------------


// --------------------------------------------------
// the part below "winning" will run 4% of the time, modified
// by the $favormod.   $favormod is a % of the gold you donated.
// if you give half your gold up, $favormod will be 50%.
// thus, the chance this part will process is 54% (4% + 50%).
// if you give, all your gold ($favormod=100%), you will 
// always "win" when donating to the temple.
// Since we are removing the $favormod, we;ll jsut 
// concentrate on the section below...
// this is something I am updating for my game reset. 
// --------------------------------------------------
		if ($randomchance <= 40) {
			$goldmod = intval($donation * 1.15);
	       		$newgold = $userrow["gold"] + $goldmod;
			$err = "<br>";
			if ($newgold >= 999999) {
				$newgold = 999999;
				$err="<br><i>You can only hold 999999 gold. The rest is taken back by the Spirits.</i><br>";}
			$query = doquery("UPDATE {{table}} SET gold='$newgold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
			$page .= "<p><b>The spirits give your donation back, plus an extra ".intval($donation*.15)." gold!</b><br>";
			$page .= $err;

// --------------------------------------------------
// all that stuff above is just processing if you "win".
// this will run 4% of the time.
// meaning if random number ($randomchance) is less than 40.
// it takes your donation, multiplies it by 25%, and 
// gives you that much gold.
// so if you donate 100 gold, and "win", you will get
// 125 gold back.
// if you want to add more things happen, here is what I would do.
// right above the line below this section 
// which reads      } else {
// I would start some other sections.
// if (($randomchance > 40) && ($randomchance < 50)) {
//	do stuff here
//	}
// if (($randomchance > 50) && ($randomchance < 60)) {
//	do otherstuff here
//	}
// if (($randomchance > 60) && ($randomchance < 100)) {
//	do different stuff here
//	}
// if (($randomchance > 100) && ($randomchance < 120)) {
//	do different stuff here
//	}
// if (($randomchance > 500) && ($randomchance < 600)) {
//	do evendifferent stuff here
//	}
//
// basically take a block of numbers from 1-1000 
// and split it up however you want. larger blocks
// of numbers would obviously run more frequently 
// than groups of 10 (which is a 1% chance to process)
// -------------------------------------------------- 

		} else {
			$page .= "<p><b>The spirits did not respond.</b><p>";
			$page .= "<i>".$templeadd." gold has been added to the Strongholds Storage.</i><p>";
		}
		// ------------------------------------------------------
		// -------------- Add stuff here ------------------------
		// ------------------------------------------------------
// --------------------------------------------------
// that little part above is obviously if the $randomchance
// does not process.  Nothing happens, and it tells you that.
// --------------------------------------------------  
	

		$page .= "You may return to <a href='guilds.php?do=temple'>the Temple</a>, go back ";
	  	$page .= "to the <a href='guilds.php'>Guild</a>, ";
	  	$page .= "or leave and <a href='index.php?do=move:0'>continue Exploring</a>.";
	  	    display($page, "Guild Temple");
// --------------------------------------------------
// that above just outputs the rest of the page to the browser.
// --------------------------------------------------


    } elseif (isset($_POST["cancel"])) {
        header("Location: guilds.php"); die();

// --------------------------------------------------
// that above is just if they press the Cancel button, they are
// returned to the stronghold screen.
// --------------------------------------------------




// --------------------------------------------------
// the part below just displays the page if they are
// just entering the temple.  Change this text as 
// appropriate.
// --------------------------------------------------
    } else {
    	$title = "Guild Temple";
	$page = "<table width='100%'><tr><td class='title'>Guild Temple</td></tr></table><p>";
	$page .= "Welcome to the Guild Temple.  Here you can donate gold in hopes of receiving a ";
	$page .= "Blessing from the Gods of Dragons Kingdom.<p>  If the spirits receive your donation ";
	$page .= "favorably, you may receive additional gold, have your Magic or Health restored, or ";
	$page .= "even gain an increase in one of your stats.  If you are really lucky, you may receive ";
	$page .= "an item or new weapon as a gift. (to come soon)<br> The amount you donate slightly increases your ";
	$page .= "chances of receiving a Blessing, but even five gold can please the spirits sometimes.<br>";
	$page .= "<p>Additionally, a percentage of the gold you donate is given to the Guild itself, and can ";
	$page .= "be used to upgrade/repair strongholds and increase your strongholds level.<p> ";
	$page .= "<table><tr><td><form action='guilds.php?do=temple' method='post'>";
	$page .= "<b>Enter Donation Amount:</b><br>";
	$page .= "<input type='text' size='15' name='donation'>";
	$page .= " <input type='submit' name='submit' value='Donate'>";
	$page .= "<input type='hidden' name='castleid' value='".$userrow["laststronghold"]."'>";
	$page .= "</form></td></tr></table>";
	$page .= "<br />You may return to the <a href='guilds.php'>Guild</a>, ";
	$page .= "or leave and <a href='index.php?do=move:0'>continue exploring</a>.";
	  	    display($page, "Guild Temple");
    }
}

function dowarp ($id){
	global $userrow;
	    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
	if (isset($id)) {
	$cquery = doquery("SELECT * from {{table}} WHERE id='$id' LIMIT 1", "strongholds");
	$crow = mysql_fetch_array($cquery);
	$newlat = $crow["latitude"];
	$newlon = $crow["longitude"];
	$p = doquery("UPDATE {{table}} SET latitude='$newlat',longitude='$newlon',location='Outside a Stronghold',currentaction='Exploring' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	    header("Location: index.php?do=move:0"); die();
}

	if ($userrow["currentap"] <= 9) {
		$page = "<table width='100%'><tr><td class='title'>Not Enough Ability Points!</td></tr></table>";
		$page .= "<br><b>You do not have enough AP to perform this action.</b><br><br>";
		$page .= "You must have at least 10 AP in order to do this action. ";
		$page .= "Your AP is refilled for a price of 50 Dragon Scales.<br>";
		$page .= "<p>You may return to the <a href='guilds.php'>Guild</a>, ";
		$page .= "or leave and <a href='index.php'>continue exploring</a>.";
		display($page, "Not Enough AP");

	}
	$cquery = doquery("SELECT * from {{table}} WHERE guildname='".$userrow["guildname"]."' ORDER BY guildid", "strongholds");
$updatequery = doquery("UPDATE {{table}} SET location='Stronghold Portal' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	$page = "<table width='100%'><tr><td class='title'>Stronghold Portals</td></tr></table>";
	$page .= "The portal links to all strongholds controlled by your Guild.  Please choose a ";
	$page .= "destination, or click below to return to the Guild Courtyard.<br>";
	$page .= "<table><tr><td bgcolor='#ffffff' width='20%'>Stronghold</td>";
	$page .= "<td bgcolor='#ffffff'>Location</td></tr>";
	$count = 0;
	while ($crow=mysql_fetch_array($cquery)) {
		$count += 1;
		if ($crow["latitude"] < 0) {$dlat='S';} else {$dlat='N';}
		if ($crow["longitude"] < 0) {$dlon='W';} else {$dlon='E';}
		if (($count/2) != intval($count/2)) {$colour = "bgcolor='#eeeeee'";} else {$colour="bgcolor='#ffffff'";}
		$page .= "<td ".$colour." width='20%'><a href='guilds.php?do=warp:".$crow["id"]."'>";
		$page .= $count.") ".$userrow["guildname"]."</td><td ".$colour.">";
		$page .= abs($crow["latitude"]).$dlat.", ".abs($crow["longitude"]).$dlon."</a></td></tr>";
	}
	if ($count <= 0) {$page .= "<td colspan='2'>No Strongholds built for your Guild!</td><tr>";}
	$page .= "</table>";
        $newap = $userrow["currentap"] - 10;
	$pquery = doquery("UPDATE {{table}} SET currentap='$newap' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
    $page .= "<center><br><a href='guilds.php'>Back to the Guild</a></center>";
    display($page,"Guild Courtyard");
}

function dorestap() {


     global $userrow, $numqueries;

    $townquery = doquery("SELECT name,restap FROM {{table}} LIMIT 1", "towns");

    $townrow = mysql_fetch_array($townquery);

    if ($userrow["dscales"] < $townrow["restap"]) { display("You do not have enough Dragon Scales to Rest here.<br /><br />You may return to the <a href=\"guilds.php\">Guild Courtyard</a>, or use the compass to the right to start exploring.", "Restore AP"); die(); }

    if (isset($_POST["submit"])) {

        $newdscales = $userrow["dscales"] - $townrow["restap"];
        $query = doquery("UPDATE {{table}} SET dscales='$newdscales', currentap='".$userrow["maxap"]."' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Restore Ability Points";
        $page = "<table width='100%' border='1'><tr><td class='title'>Restore Ability Points</td></tr></table><p>";
        $page .= "You feel refreshed after a long rest, and eating healthily for a short period of time.<br /><br />You may return to the <a href=\"guilds.php\">Guild Courtyard</a>, or use the compass on the right to continue exploring.";

    } elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

    } else {

$updatequery = doquery("UPDATE {{table}} SET location='Restore AP' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

        $title = "Restore Ability Points";
        $page = "<table width='100%' border='1'><tr><td class='title'>Restore Ability Points</td></tr></table><p>";
        $page .= "Ability points are special Stats which are used for certain features such as building a Stronghold, attacking a Stronghold and any other feature within a Stronghold. They are simply your Ability to do specific tasks within a Guild and Stronghold.<br /><br />\n";
        $page .= "<p>To restore your AP completely, it will cost you <b>" . $townrow["restap"] . " Dragon Scales</b>.<p>This will not heal your HP, MP or TP. It also won't remove your Tavern Drink or Potion.<p>Is that ok?<br /><br />\n";
        $page .= "<form action=\"guilds.php?do=restap\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes\" /><p>If you have changed your mind you may return to the <a href=\"guilds.php\">Guild Courtyard</a>, or use the compass on the right to continue exploring.\n";
        $page .= "</form>\n";

    }

    display($page, $title);

}

function dolistmembers ($filter) {
	global $userrow;
	if (!isset($filter)) { $filter = "";}
	$page = "<table width='100%'><tr><td class='title'>Guild Members</td></tr></table>";
	$page .= "<center><p>To view the list of current members in this guild, click on a letter to filter them. You may edit and boot members from within your Stronghold.<p>";
        $page .= "[ <a href='guilds.php?do=users'>*</a> ";
	$page .= " <a href='guilds.php?do=users:A'>A</a> ";
	$page .= " <a href='guilds.php?do=users:B'>B</a> ";
	$page .= " <a href='guilds.php?do=users:C'>C</a> ";
	$page .= " <a href='guilds.php?do=users:D'>D</a> ";
	$page .= " <a href='guilds.php?do=users:E'>E</a> ";
	$page .= " <a href='guilds.php?do=users:F'>F</a> ";
	$page .= " <a href='guilds.php?do=users:G'>G</a> ";
	$page .= " <a href='guilds.php?do=users:H'>H</a> ";
	$page .= " <a href='guilds.php?do=users:I'>I</a> ";
	$page .= " <a href='guilds.php?do=users:J'>J</a> ";
	$page .= " <a href='guilds.php?do=users:K'>K</a> ";
	$page .= " <a href='guilds.php?do=users:L'>L</a> ";
	$page .= " <a href='guilds.php?do=users:M'>M</a> ";
	$page .= " <a href='guilds.php?do=users:N'>N</a> ";
	$page .= " <a href='guilds.php?do=users:O'>O</a> ";
	$page .= " <a href='guilds.php?do=users:P'>P</a> ";
	$page .= " <a href='guilds.php?do=users:Q'>Q</a> ";
	$page .= " <a href='guilds.php?do=users:R'>R</a> ";
	$page .= " <a href='guilds.php?do=users:S'>S</a> ";
	$page .= " <a href='guilds.php?do=users:T'>T</a> ";
	$page .= " <a href='guilds.php?do=users:U'>U</a> ";
	$page .= " <a href='guilds.php?do=users:V'>V</a> ";
	$page .= " <a href='guilds.php?do=users:W'>W</a> ";
	$page .= " <a href='guilds.php?do=users:X'>X</a> ";
	$page .= " <a href='guilds.php?do=users:Y'>Y</a> ";
	$page .= " <a href='guilds.php?do=users:Z'>Z</a> ]<br></center>";

	$charquery = doquery("SELECT * FROM {{table}} WHERE charname LIKE '".$filter."%' AND guildname='".$userrow["guildname"]."' ORDER by charname", "users");
        $updatequery = doquery("UPDATE {{table}} SET location='Guild Members List' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	$page .= "<center>There are <b>".mysql_num_rows($charquery)."</b> characters in your Guild starting: '".$filter."'.</center> ";
	$page .= "<center><table width='90%' style='border: solid 1px black' cellspacing='0' cellpadding='3'>";
	$page .= "<tr><td colspan=\"8\" bgcolor=\"#ffffff\"><center><b>Dragons Kingdom Characters</b></center></td></tr>";
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
		$page .= "".$charrow["charname"]."";}
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
	$page .= "<center><br><a href='guilds.php'>Return to the Guild</a></center>";

	display($page, "Characters of Dragons Kingdom");

}

function doview($start) {
	global $userrow;
	if ($start == '') {$start = 0;}
	$page = "<table width='100%'><tr><td class='title'>Guild Courtyard</td></tr></table>";
$updatequery = doquery("UPDATE {{table}} SET location='Viewing Guilds' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	$page .= "Below is a listing of the public Guilds active within Dragons Kingdom.<br>";
	$query = doquery("SELECT * FROM {{table}} WHERE 1 ORDER BY name LIMIT ".$start.",20", "guilds");
	$fullquery = doquery("SELECT * FROM {{table}} WHERE 1 ORDER BY name", "guilds");
 $page .= "<hr /><table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"6\" style=\"background-color:#dddddd;\"><br><center>Guilds of Dragons Kingdom<p></center></th></tr><tr><th width='20%' style='background-color:#dddddd;'>Name</th><th width=\"15%\" style='background-color:#dddddd;'>Founder</th><th width=\"15%\" style='background-color:#dddddd;'>Tag</th><th width='2%' style='background-color:#dddddd;'>Members</th><th width='2%' style='background-color:#dddddd;'>Cost</th><th width='60%' style='background-color:#dddddd;'>Description</th></tr>\n";
	$count = 1;
    if (mysql_num_rows($query) == 0) {
       $page .= "<tr><td style='background-color:#ffffff;' colspan='3'><b>No Guilds available.</b></td></tr>\n";
    } else {
      while ($row = mysql_fetch_array($query)) {
	  	if ($row["private"] != "1") {
	  		$namelink = "<b>".$row["name"]."</b>";
	  	} else {
	  		$namelink = "<b><img src='img/padlock.gif'>".$row["name"]."</b>";
	  	}
		if ($count == 1) {
          	$page .= "<tr><td style='background-color:#ffffff;'>".$namelink."</td><td style='background-color:#ffffff;'>".$row["founder"]."</td><td style='background-color:#ffffff;'>".$row["tag"]."</td><td style='background-color:#ffffff;'>".$row["members"]."</td><td style='background-color:#ffffff;'>".$row["joincost"]."</td><td style='background-color:#ffffff;'>".$row["description"]."</td><tr>\n";
			$count = 2;
		} else {
            	$page .= "<tr><td style='background-color:#eeeeee;'>".$namelink."</td><td style='background-color:#eeeeee;'>".$row["founder"]."</td><td style='background-color:#eeeeee;'>".$row["tag"]."</td><td style='background-color:#eeeeee;'>".$row["members"]."</td><td style='background-color:#eeeeee;'>".$row["joincost"]."</td><td style='background-color:#eeeeee;'>".$row["description"]."</td><tr>\n";
			$count = 1;
		}
	  }
    }
	$page .= "<tr><td colspan='6' style='background-color:#dddddd;'><center> Pages [ ";
   	$numpages = intval(mysql_num_rows($fullquery)/20);
	for($pagenum = 0; $pagenum <= $numpages; $pagenum++) {
	$pagestart = $pagenum*20;
	$pagelink = $pagenum + 1;
	if ($start != $pagestart) {
		$page .= "<a href='guilds.php?do=view:".$pagestart."'>".$pagelink."</a>   ";
	}else {
		$page .= "<i>".$pagelink."</i>   ";
	}
	}
	$page .= " ]</center></td></tr>";
	$page .= "<tr><td colspan='6' style='background-color:#dddddd;'><center>";
	$page .= "Guilds marked with a <img src=\"img/padlock.gif\"> are password locked.<br>Any Guild which is inactive, or becomes unused with no members within the Guild will be removed without notice. Member limit is a maximum of 50 players.";
	$page .= "</center></td></tr></table></table>";
	$page .= "<center><br><a href='guilds.php'>Return to the Guild</a></center>";
    display($page,"Guild Courtyard");
}


function dovault() {
    global $userrow;
    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
    if (isset($_POST["submit"])) {
		if ($_POST["deposit"] != '') {
		  $deposit = abs(intval($_POST["deposit"]));
		  if ($userrow["gold"] < $deposit) {
		    $page = "<table width=\"100%\"><tr><td class=\"title\">Guild Vault</td></tr></table>";
		  	$page .= "<br><b>You do not have ".$deposit." gold to deposit!</b><br><br>";
	  		$page .= "You may return to <a href='guilds.php?do=vault'>the vaults</a>, go back ";
	  		$page .= "to the <a href='index.php'>Guild Courtyard</a>, ";
	  		$page .= "or leave and <a href=\"index.php\">return to town</a>.";
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
    	$page = "<table width=\"100%\"><tr><td class=\"title\">Guild Vault</td></tr></table>";
        $page .= "You have deposited ".$deposit." gold into the Vaults.<br />";
	  	$page .= "The current Vault balance is: <b>".$newbank."</b> gold.<br /><br />";
	  	$page .= "You may return to <a href=\"guilds.php?do=vault\">the vaults</a>, go back ";
	  	$page .= "to the <a href='guilds.php'>Guild Courtyard</a>, ";
	  	$page .= "or leave and <a href=\"index.php\">or return to town</a>.";
	  	    display($page, "Guild Vault");
		} elseif ($_POST["withdraw"] != '') {
		  $withdraw = abs(intval($_POST["withdraw"]));
		  if ($userrow["bank"] < $withdraw) {
		    $page = "<table width=\"100%\"><tr><td class=\"title\">Town Vault.</td></tr></table>";
		  	$page .= "<br><b>You do not have ".$withdraw." gold to withdraw!!</b><br><br>";
	  		$page .= "You may return to <a href=\"guilds.php?do=vault\">the vaults</a>, go back ";
	  		$page .= "to the <a href='guilds.php'>Guild Courtyard</a>, ";
	  		$page .= "or leave and <a href=\"index.php\">return to town</a>.";
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
    	$page = "<table width=\"100%\"><tr><td class=\"title\">Guild Vault.</td></tr></table>";
        $page .= "You have withdrawn ".$withdraw." gold from the Vault.<br />";
	  	$page .= "The current Vault balance is: <b>".$newbank."</b> gold.<br /><br />";
	  	$page .= "You may return to <a href=\"guilds.php?do=vault\">the vault</a>, go back ";
	  	$page .= "to the <a href=\"guilds.php?\">Guild Courtyard</a>, ";
	  	$page .= "or leave and <a href=\"index.php\">return to town</a>.";
	  		display($page, "Guild Vault");
		}
    } elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

    } else {

$updatequery = doquery("UPDATE {{table}} SET location='Guild Vault' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

    	$title = "Guild Vault";
	$page = "<table width=\"100%\"><tr><td class=\"title\">Guild Vault</td></tr></table>";
	$page .= "Welcome to the Guild Courtyard Vault.  Here you can deposit or withdraw gold.<br>";
	$page .= "Any gold kept in the vaults is safe when you die. ";
	$page .= "The Vault is linked to your gold in your town bank, but with some extra special features.<p>";
	$page .= "Your current vault balance is: <b>".$userrow["bank"]."</b> gold.<p><br>";
	$page .= "<table><tr><td>";
	$page .= "<table><tr><td><form action='guilds.php?do=vault' method='post'>";
	$page .= "Withdraw Gold</td></tr><tr><td>Enter Amount:<br>";
	$page .= "<input type='text' size='15' name='withdraw'><br>";
	$page .= "<input type='submit' name='submit' value='Withdraw'>";
	$page .= "</form></td></tr></table></td>";
	$page .= "<td><table><tr><td><form action='guilds.php?do=vault' method='post'>";
	$page .= "Deposit Gold</td></tr><tr><td>Enter Amount:<br>";
	$page .= "<input type='text' size='15' name='deposit' value='".$userrow["gold"]."'><br>";
	$page .= "<input type='submit' name='submit' value='Deposit'>";
	$page .= "</form></td></tr></table></td></tr></table>";
	$page .= "<br />You may return to the <a href='guilds.php'>Guild Courtyard</a>, ";
	$page .= "or leave and <a href='index.php'>return to town</a>.";
	  	    display($page, "Guild Vault");
    }
}

function dojoin($start) {
	global $userrow;
	if ($start == '') {$start = 0;}

$updatequery = doquery("UPDATE {{table}} SET location='Joining a Guild' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	$page = "<table width='100%'><tr><td class='title'>Guild Courtyard</td></tr></table>";
	$page .= "Below is a listing of the public Guilds available to join, for a fee.<br>";
	$page .= "To join a Guild, simply click on the name of the Guild.  Assuming you have enough Dragon Scales, ";
	$page .= "you will become a member of the Guild and a message will be sent to the Guild leaders to ";
	$page .= "notify them of your membership.<p>The Dragon Scales which you pay will be Donated to the Guild, and then these will be used to improve that Guild.<br>";
	$query = doquery("SELECT * FROM {{table}} WHERE 1 ORDER BY name LIMIT ".$start.",20", "guilds");
	$fullquery = doquery("SELECT * FROM {{table}} WHERE 1 ORDER BY name", "guilds");
 $page .= "<hr /><table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"6\" style=\"background-color:#dddddd;\"><br><center>Guilds of Dragons Kingdom<p></center></th></tr><tr><th width='20%' style='background-color:#dddddd;'>Name</th><th width=\"15%\" style='background-color:#dddddd;'>Founder</th><th width=\"15%\" style='background-color:#dddddd;'>Tag</th><th width='2%' style='background-color:#dddddd;'>Members</th><th width='2%' style='background-color:#dddddd;'>Cost</th><th width='60%' style='background-color:#dddddd;'>Description</th></tr>\n";
	$count = 1;
    if (mysql_num_rows($query) == 0) {
       $page .= "<tr><td style='background-color:#ffffff;' colspan='3'><b>No Guilds available.</b></td></tr>\n";
    } else {
      while ($row = mysql_fetch_array($query)) {
	  	if ($row["private"] != "1") {
	  		$namelink = "<b><a href='guilds.php?do=signup:".$row["id"]."'>".$row["name"]."</a></b>";
	  	} else {
	  		$namelink = "<b><img src='img/padlock.gif'><a href='guilds.php?do=passsign:".$row["id"]."'>".$row["name"]."</a></b>";
	  	}
		if ($count == 1) {
          	$page .= "<tr><td style='background-color:#ffffff;'>".$namelink."</td><td style='background-color:#ffffff;'>".$row["founder"]."</td><td style='background-color:#ffffff;'>".$row["tag"]."</td><td style='background-color:#ffffff;'>".$row["members"]."</td><td style='background-color:#ffffff;'>".$row["joincost"]."</td><td style='background-color:#ffffff;'>".$row["description"]."</td><tr>\n";
			$count = 2;
		} else {
            	$page .= "<tr><td style='background-color:#eeeeee;'>".$namelink."</td><td style='background-color:#eeeeee;'>".$row["founder"]."</td><td style='background-color:#eeeeee;'>".$row["tag"]."</td><td style='background-color:#eeeeee;'>".$row["members"]."</td><td style='background-color:#eeeeee;'>".$row["joincost"]."</td><td style='background-color:#eeeeee;'>".$row["description"]."</td><tr>\n";
			$count = 1;
		}
	  }
    }
	$page .= "<tr><td colspan='6' style='background-color:#dddddd;'><center> Pages [ ";
   	$numpages = intval(mysql_num_rows($fullquery)/20);
	for($pagenum = 0; $pagenum <= $numpages; $pagenum++) {
	$pagestart = $pagenum*20;
	$pagelink = $pagenum + 1;
	if ($start != $pagestart) {
		$page .= "<a href='guilds.php?do=join:".$pagestart."'>".$pagelink."</a>   ";
	}else {
		$page .= "<i>".$pagelink."</i>   ";
	}
	}
	$page .= " ]</center></td></tr>";
	$page .= "<tr><td colspan='6' style='background-color:#dddddd;'><center>";
	$page .= "Guilds marked with a <img src=\"img/padlock.gif\"> are password locked.<br>Any Guild which is inactive, or becomes unused with no members within the Guild will be removed without notice. Member limit is a maximum of 50 players.";
	$page .= "</center></td></tr></table></table>";
	$page .= "<center><br><a href='guilds.php'>Return to the Guild</a></center>";
    display($page,"Guild Courtyard");
}



function dosignup($guildid) {
	global $userrow;

	$joinquery = doquery("SELECT * FROM {{table}} WHERE id='".$guildid."' LIMIT 1", "guilds");
    $joinrow = mysql_fetch_array($joinquery);
	$gquery = doquery("SELECT members FROM {{table}} WHERE id='".$guildid."' LIMIT 1", "guilds");
	$grow = mysql_fetch_array($gquery);
	if ($grow["members"] >= 50) {
		$page = "<table width='100%'><tr><td class='title'>Guild Full</td></tr></table>";
		$page .= "<p>This Guild has maxed out its members and is full. It cannot allow anymore member signups due to the maximum a Guild can have is 50 members. If you really wish to join, you should contact the Guild Founder or Leader.<br>";
		$page .= "<br>You may return to <a href='index.php'>town</a> or continue exploring using the compass to the right.";
    	display($page,"Guild Full");
	}
    if ($userrow["dscales"] < $joinrow["joincost"]) { display("<table width=\"100%\"><tr><td class=\"title\">Joining a Guild</td></tr></table>You do not have enough Dragon Scales to join the ".$joinrow["name"].".<br /><br />You may return to the <a href='index.php'>town</a>, or leave and continue exploring using the compass to the right.", "Join a Guild"); die(); }

    if (isset($_POST["submit"])) {
	$joincost = $_POST["joincost"];
	$newdscales = $userrow["dscales"] - $joincost;
	$guildname = $_POST["name"];
	$guildid = $_POST["guildid"];
	$founder = $_POST["founder"];
	$title = "Joining a Guild";
	$page = "<table width=\"100%\"><tr><td class=\"title\">Joining a Guild.</td></tr></table>";
	$page .= "You have officially joined the ".$guildname." Guild.<br />";
	$page .= "<br />You may return to the <a href='guilds.php'>Guild Courtyard</a>,";
	$page .= " or leave and ";
	$page .= "<a href='index.php'>or return to town</a>.";

      $guildrank = 0;
	if ($founder == $userrow["charname"]) {$guildrank = 250;}
	$q = doquery("UPDATE {{table}} SET dscales='$newdscales',guildname='$guildname',guildrank='$guildrank' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

$dscales = $joincost;  
    $query = doquery("UPDATE {{table}} SET dscales=dscales+$dscales, members=members+1 WHERE id='".$guildid."' LIMIT 1", "guilds");
$content = "New Member Announcement!<br>\n";
	$content .= "This is a notification that a new member has joined the ".$joinrow["name"]." Guild!<br>\n";
	$content .= "<u><b>Member Stats:</b></u><br>";
	$content .= "<b>Character Name:  ".$userrow["charname"]."<br>";
	$content .= "<b>Level: ".$userrow["level"]."<br>";
	$content .= "<b>Dragon Scales: ".$userrow["dscales"]."<br>";
	$content .= "<b>Bank/Vault Gold: ".$userrow["bank"]."<br>";

	$leaderquery = doquery("SELECT charname FROM {{table}} WHERE guildrank >=100 AND guildname='$guildname' ORDER by guildrank", "users");
	while ($leaderrow = mysql_fetch_array($leaderquery)) {
		$query = doquery("INSERT INTO {{table}} SET postdate=NOW(),author='".$userrow["charname"]."',recipient='".$leaderrow["charname"]."',subject='New Member Signup',content='$content'", "gamemail");
		$logdate = "cycle_".intval(date(z)/7);
		$logpath = "logs/guild".$guildid."/";
		if (!is_dir($logpath)) {
		 @mkdir($logpath,0777);
       	 @chmod($logpath,0777);
       	}
		$logfile =  $logpath.$logdate.".log";
		$logcomments = "--<b>".$userrow["charname"]."</b> has joined the ".$guildname." on ".date("r")."<br>\r\n";
		$fp = fopen("$logfile", "a");
		fwrite($fp, "$logcomments");
		fclose ($fp);
	}

    } elseif (isset($_POST["cancel"])) {
        header("Location: guilds.php?do=join"); die();

    } else {

$updatequery = doquery("UPDATE {{table}} SET location='Joining a Guild' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	$gquery = doquery("SELECT members FROM {{table}} WHERE id='".$guildid."' LIMIT 1", "guilds");
	$grow = mysql_fetch_array($gquery);
	if ($grow["members"] >= 50) {
		$page = "<table width='100%'><tr><td class='title'>Guild Full</td></tr></table>";
		$page .= "<p>This Guild has maxed out its members and is full. It cannot allow anymore member signups due to the maximum a Guild can have is 50 members. If you really wish to join, you should contact the Guild Founder or Leader.<br>";
		$page .= "<br>You may return to <a href='index.php'>town</a> or continue exploring using the compass to the right.";
    	display($page,"Guild Full");
	}
	$title = "Joining a Guild";
	$page = "<table width='100%'><tr><td class='title'>Joining a Guild</td></tr></table>";
	$page .= "You are requesting to join the ".$joinrow["name"]." Guild.  This guild has been described as the following:<br />";
	$page .= "<i>".$joinrow["description"]."</i><br><br>\n";
	$page .= "Joining this guild will cost you <b>" . $joinrow["joincost"] . " Dragon Scales</b>.";
	$page .= "<p>You currently have <b>".$userrow["dscales"]."</b> Dragon Scales.<br>";
	$page .= "Is that ok?<br /><br />\n";
	$page .= "<form action='guilds.php?do=signup' method='post'>\n";
	$page .= "<input type='submit' name='submit' value='Yes' />";
	$page .= "<input type='hidden' name='joincost' value='".$joinrow["joincost"]."' />";
	$page .= "<input type='hidden' name='name' value='".$joinrow["name"]."' />";
	$page .= "<input type='hidden' name='guildid' value='".$joinrow["id"]."' />";
	$page .= " <input type='hidden' name='founder' value='".$joinrow["founder"]."' />";
	$page .= "<input type='submit' name='cancel' value='No' />\n";
	$page .= "</form>\n";
	}
		display($page,"Guild Courtyard");
}


function dosignuppass($guildid) {
	global $userrow;

	$joinquery = doquery("SELECT * FROM {{table}} WHERE id='".$guildid."' LIMIT 1", "guilds");
    	$joinrow = mysql_fetch_array($joinquery);


    if (isset($_POST["submit"])) {
		$guildname = $_POST["name"];
		$guildpass = $_POST["guildpass"];
		$guildid = $_POST["guildid"];
		$joinpass = $_POST["joinpass"];
		$founder = $_POST["founder"];
		$user = $_POST["userid"];
		if ($guildpass != $joinpass) {
		$title = "Joining a Guild";
		$page = "<table width=\"100%\"><tr><td class=\"title\">Joining a Guild</td></tr></table>";
		$page .= "<h4>The Password you entered is incorrect!</h4>";
		$page .= "The password for this guild was sent in an email invitation, which is only ";
		$page .= "valid for a short period of time.   If you typed the password correctly, but are ";
		$page .= "still unable to join, please email the leader of this guild.<p>";
		$page .= "You are not allowed to join the ".$guildname." Guild.<br />";
		$page .= "<br />You may return to the <a href='guilds.php'>Guild Courtyard</a>,";
		$page .= " or leave and ";
		$page .= "<a href='index.php'>return to town</a>.";
		display($page, $title);
		die();
		}
	$title = "Joining a Guild";
	$page = "<table width=\"100%\"><tr><td class=\"title\">Joining a Guild</td></tr></table>";
	$page .= "You have officially joined the ".$guildname." Guild.<br />";
	$page .= "<br />You may return to the <a href='guilds.php'>Guild Courtyard</a>,";
	$page .= " or leave and ";
	$page .= "<a href='index.php'>return to town</a>.";
    $query2 = doquery("UPDATE {{table}} SET members=members+1 WHERE id='".$guildid."' LIMIT 1", "guilds");
      $guildrank = 0;
	if ($founder == $userrow["charname"]) {$guildrank = 250;}
	$query = doquery("UPDATE {{table}} SET guildname='$guildname',guildrank='$guildrank' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	$content = "New Member Announcement!<br>\n";
	$content .= "This is a notification that a new member has joined the ".$joinrow["name"]." Guild!<br>\n";
	$content .= "<u><b>Member Stats:</b></u><br>";
	$content .= "<b>Character Name:  ".$userrow["charname"]."<br>";
	$content .= "<b>Level: ".$userrow["level"]."<br>";
	$content .= "<b>Dragon Scales: ".$userrow["dscales"]."<br>";
	$content .= "<b>Bank/Vault Gold: ".$userrow["bank"]."<br>";


	$subject = "New Member Signup";

	$leaderquery = doquery("SELECT charname FROM {{table}} WHERE guildrank >=100 AND guildname='$guildname' ORDER by guildrank", "users");
	while ($leaderrow = mysql_fetch_array($leaderquery)) {
		$query = doquery("INSERT INTO {{table}} SET postdate=NOW(),author='".$userrow["charname"]."',recipient='".$leaderrow["charname"]."',subject='New Member Signup',content='$content'", "gamemail");

		$logdate = "cycle_".intval(date(z)/7);
		$logpath = "logs/guild".$guildid."/";
		if (!is_dir($logpath)) {
		 @mkdir($logpath,0777);
       	 @chmod($logpath,0777);
       	}
		$logfile =  $logpath.$logdate.".log";
		$logcomments = "--<b>".$userrow["charname"]."</b> has joined the ".$guildname." on ".date("r")."<br>\r\n";
		$fp = fopen("$logfile", "a");
		fwrite($fp, "$logcomments");
		fclose ($fp);
	}


    } elseif (isset($_POST["cancel"])) {
        header("Location: guilds.php?do=join"); die();

    } else {
	$title = "Joining a Guild";

	$page = "<table width='100%'><tr><td class='title'>Joining a Guild</td></tr></table>";
	$page .= "You are requesting to join the ".$joinrow["name"]." Guild.  This guild has been described as:<br />";
	$page .= "<i>".$joinrow["description"]."</i><br><br>\n";
	$page .= "To join this guild, please enter the password provided in your invitation email.</b>.";

	$page .= "Is that ok?<br /><br />\n";
	$page .= "<form action='guilds.php?do=passsign' method='post'>\n";
	$page .= "Enter Password: <input type='text' size='20' name='joinpass' />";
	$page .= " <input type='hidden' name='name' value='".$joinrow["name"]."' />";
	$page .= " <input type='hidden' name='guildpass' value='".$joinrow["password"]."' />";
	$page .= " <input type='hidden' name='userid' value='".$userrow["id"]."' />";
	$page .= " <input type='hidden' name='founder' value='".$joinrow["founder"]."' />";
	$page .= " <input type='hidden' name='guildid' value='".$joinrow["id"]."' />";
	$page .= " <input type='submit' name='submit' value='Join' /><br>";
	$page .= "<input type='submit' name='cancel' value='Cancel' />\n";
	$page .= "</form>\n";

	}

		display($page,"Guild Courtyard");
}


function doleave() {
	global $userrow;
    if (isset($_POST["submit"])) {
	$castlequery = doquery("SELECT * FROM {{table}} WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
	$castlerow = mysql_fetch_array($castlequery);
        $title = "Leaving a Guild";
        $page = "<table width=\"100%\"><tr><td class=\"title\">Leaving a Guild.</td></tr></table>";
        $page .= "You have officially left the ".$userrow["guildname"]." Guild.<br>";
        $page .= "<br />You may return to the <a href='guilds.php'>Guild Courtyard</a>,";
        $page .= " or leave and ";
        $page .= "continue exploring using the compass to the right.";
        $query2 = doquery("UPDATE {{table}} SET members=members-1 WHERE name='".$castlerow["name"]."' LIMIT 1", "guilds");
	$q = doquery("UPDATE {{table}} SET guildname='-',guildrank='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
		$logdate = "cycle_".intval(date(z)/7);
		$logpath = "logs/guild".$castlerow["id"]."/";
		if (!is_dir($logpath)) {
		 @mkdir($logpath,0777);
       	 @chmod($logpath,0777);
       	}
		$logfile =  $logpath.$logdate.".log";
		$logcomments = "--<b>".$userrow["charname"]."</b> has left the ".$castlerow["name"]." on ".date("r")."<br>\r\n";
		$fp = fopen("$logfile", "a");
		fwrite($fp, "$logcomments");
		fclose ($fp);
    } elseif (isset($_POST["cancel"])) {

        header("Location: guilds.php"); die();

    } else {

$updatequery = doquery("UPDATE {{table}} SET location='Leaving a Guild' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	$title = "Leaving a Guild";
	$page = "<table width='100%'><tr><td class='title'>Leaving a Guild</td></tr></table>";
	$page .= "You are requesting to leave the ".$userrow["guildname"]." Guild.<p>";
	$page .= "If you remove yourself from the Guild, you will be required to either ";
	$page .= "pay the membership fee in Dragon Scales again to re-join, or if the ";
	$page .= "guild is set to invite-only, you will need to be invited by one of the ";
	$page .= "leaders before being able to enter.<br>";
	$page .= "Is that what you really want?<br /><br />\n";
	$page .= "<form action='guilds.php?do=leave' method='post'>\n";
	$page .= "<input type='submit' name='submit' value='Yes' />  \t";
	$page .= "<input type='submit' name='cancel' value='No' />\n";
	$page .= "</form>\n";
	}
		display($page,"Guild Courtyard");

}




function dobuild() {
	global $userrow;
if ($userrow["guildrank"] < 100) { header("Location: guilds.php"); die();}
	$castlequery = doquery("SELECT * FROM {{table}} WHERE guildname='".$userrow["guildname"]."' ", "strongholds");
	$castlerow = mysql_fetch_array($castlequery);
	$gquery = doquery("SELECT * FROM {{table}} WHERE name='".$userrow["guildname"]."' ", "guilds");

	if (mysql_num_rows($castlequery) >= 7) {
		$page = "<table width='100%'><tr><td class='title'>Build a new Stronghold</td></tr></table>";
		$page .= "<p>You already have 7 Strongholds!<br>";
		$page .= "In order to keep enough room available for other Guilds, you are limited to ";
		$page .= "seven (7) strongholds per Guild.  If you would like to build more strongholds, ";
		$page .= "try forming your own Guild.  It only takes 2000 Dragon Scales and finding a rare artifact ";
		$page .= "known as the 'Guild Charter'.  Using this will allow you to create your own Guild.<p>";
	    $page .= "<center><br><a href='guilds.php'>Return to the Guild</a></center>";
		display($page, "Building a Stronghold.");
	}
	$grow = mysql_fetch_array($gquery);
	if ($grow["dscales"] <= 2500) {
		$page = "<table width='100%'><tr><td class='title'>Build a new Stronghold</td></tr></table>";
		$page .= "<p>There are not enough Dragon Scales available!<br>";
		$page .= "You need to have at least 2500 Dragon Scales to build a new stronghold.<p>";
		$page .= "There are currently <b>".$grow["dscales"]."</b> Dragon Scales available here in your guild.<p>";
		$page .= "To increase the number of Dragon Scales in the stronghold vaults, members must ";
		$page .= "use the Guild Temple or use the Pet Arena to train and battle their captured ";
		$page .= "monsters.  The Temples will randomly increase the available Dragon Scales, and the Arena ";
		$page .= "directly adds the training/battle fees to the Guild totals.<br>";
    		$page .= "<center><br><a href='guilds.php'>Return to the Guild</a></center>";
		display($page, "Building a Stronghold.");
	}
	if ($castlerow["ruined"] != 0) {
		$page = "<table width='100%'><tr><td class='title'>Ruined Stronghold.</td></tr></table>";
		$page .= "<br><b>This Stronghold has been ruined!!</b><br><br>";
		$page .= "You may return to the <a href='guilds.php'>Guild</a>, ";
		$page .= "or leave and <a href='index.php'>continue exploring</a>.";
		display($page, "Ruined Stronghold");

	}
	if ($userrow["currentap"] <= 24) {
		$page = "<table width='100%'><tr><td class='title'>Not Enough Ability Points!</td></tr></table>";
		$page .= "<br><b>You do not have enough AP to perform this action.</b><br><br>";
		$page .= "You must have at least 25 AP in order to build another stronghold. ";
		$page .= "Your AP is refilled for a price of 50 Dragon Scales.<br>";
		$page .= "<p>You may return to the <a href='guilds.php'>Guild</a>, ";
		$page .= "or leave and <a href='index.php'>continue exploring</a>.";
		display($page, "Not Enough AP");
	}

    if (isset($_POST["submit"])) {

$updatequery = doquery("UPDATE {{table}} SET location='Building a Stronghold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

		$page = "<table width='100%'><tr><td class='title'>Build a new Stronghold</td></tr></table>";
		$page .= "You currently have ".mysql_num_rows($castlequery)." strongholds built.<p>";
		$page .= "You may place a Stronghold anywhere on the map with the following restrictions:";
		$page .= "<ul><li>The Stronghold can not be within 5 steps of any town.";
		$page .= "<li>The stronghold can not be within 25 steps of any other Stronghold.";
		$page .= "<li>The stronghold must at least 100 steps from the edge of the map.<br>";
		$page .= "<i>Maximum 500 latitude or longitude. Too dangerous to go above 500.</i></ul>";
		$page .= "<table width='100%' border='0'>";
		$page .= "<tr><td><form action='guilds.php?do=build2' method='POST'>";
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
		$page .= "</td><td><form action='guilds.php'>";
		$page .= "<input type='submit' name='cancel' value='Cancel'></form></td></tr></table>";

		$page .= "<center><br><a href='guilds.php'>Return to the Guild</a></center>";
    } elseif (isset($_POST["cancel"])) {
        header("Location: guilds.php"); die();

    } else {
	$page = "<table width='100%'><tr><td class='title'>Build a new Stronghold</td></tr></table>";
	$page .= "<p>It costs 2500 Dragon Scales to construct a new, level 1 stronghold.<br>";
	$page .= "You currently have ".$grow["dscales"]." Dragon Scales available in your guild<br>";
	$page .= "Do you wish to construct a new stronghold?<p>";
	$page .= "<form action='guilds.php?do=build' method='post'>\n";
	$page .= "<input type='submit' name='submit' value='Yes' />  \t";
	$page .= "<input type='submit' name='cancel' value='No' />\n";
	$page .= "</form>\n";
	$page .= "<center><br><a href='guilds.php'>Return to the Guild</a></center>";


    }
    display($page,"Guild Stronghold");
}

function dobuild2() {
	global $userrow;
if ($userrow["guildrank"] < 100) { header("Location: guilds.php"); die();}
	if ($userrow["currentap"] <= 24) {
		$page = "<table width='100%'><tr><td class='title'>Not Enough Ability Points!</td></tr></table>";
		$page .= "<br><b>You do not have enough AP to perform this action.</b><br><br>";
		$page .= "You must have at least 25 AP in order to build a stronghold. ";
		$page .= "Your AP is refilled for a price of 50 Dragon Scales.<br>";
		$page .= "<p>You may return to the <a href='guilds.php'>Guild</a>, ";
		$page .= "or leave and <a href='index.php'>continue exploring</a>.";
		display($page, "Not Enough AP");
	}

	$page = "<table width='100%'><tr><td class='title'>Building a Stronghold.</td></tr></table>";
    if (!isset($_POST["submit"])) {
	$page .= "Invalid command!<p>";
	$page .= "<center><br><a href='guilds.php'>Return to the Guild</a></center>";
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
		$page .= "<center><br><a href='guilds.php'>Back to the Guild</a></center>";
		display($page,"Building a Stronghold");
		die();
	}
	$guildquery = doquery("SELECT id,dscales,name FROM {{table}} WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
	$grow = mysql_fetch_array($guildquery);
	$new = doquery("INSERT INTO {{table}} SET latitude='$lat',longitude='$lon',guildname='".$grow["name"]."',guildid='".$grow["id"]."',founder='".$userrow["charname"]."',level='2' ", "strongholds");
	$newdscales = $grow["dscales"] - 2500;
	$g = doquery("UPDATE {{table}} SET dscales='$newdscales' WHERE id='".$grow["id"]."' LIMIT 1", "guilds");

	$page .= "You have successfully constructed a Guild Stronghold at ";
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
	$page .= "<center><br><a href='guilds.php'>Back to the Guild</a></center>";
	display($page,"Building a Stronghold");
}

function docreate() {
	global $userrow;
	$gquery = doquery("SELECT * FROM {{table}} WHERE name='".$userrow["guildname"]."' ", "guilds");

	$grow = mysql_fetch_array($gquery);
	
		if ($userrow["guildname"] != '-') {
		$page = "<table width='100%'><tr><td class='title'>Create a Guild</td></tr></table>";
		$page .= "<p>You are already in a Guild. You must leave your current Guild to be able to build your own Guild.";
		$page .= "<p>You may return to the <a href='guilds.php'>Guild Courtyard</a>.";
		display($page, "Create a Guild");
		}
	
	if ($userrow["currentap"] <= 29 || $userrow["dscales"] <= 1499 || $userrow["gold"] <= 499000) {
		$page = "<table width='100%'><tr><td class='title'>Create a Guild</td></tr></table>";
		$page .= "<br>You require 500k in Gold, 1500 Dragon Scales and 30 Ability Points to create a Guild. You will also receive 2750 Dragon Scales once the Guild is created, to form a Stronghold.";
		$page .= "<p>You may return to the <a href='guilds.php'>Guild Courtyard</a>.";
		display($page, "Create a Guild");
	

    } else {
$updatequery = doquery("UPDATE {{table}} SET location='Create a Guild' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
    
		$page = "<table width='100%'><tr><td class='title'>Create a Guild</td></tr></table>";
		$page .= "<p>All guild details can be edited and changed from within your Guild Stronghold, apart from your Guild name. You build your Stronghold after you have created your guild. Please be careful when filling this out, since you will never be able to change your Guild name.<p>You require 500k in Gold, 1500 Dragon Scales and 30 Ability Points to create a Guild. You will also receive 2750 Dragon Scales once the Guild is created, to form a Stronghold.";
		$page .= "<table width='100%' border='0'>";
		$page .= "<tr><td><form action='guilds.php?do=create' method='POST'>";
		$page .= "<b>Enter the details for your Guild:</b><hr></td></tr>";
		$page .= "<tr><td>Guild Name:<br>";
		$page .= "<input type='text' size='15' maxlength='30' name='name'> <i>Your Guilds name</i><br>";
		$page .= "<tr><td><hr></td></tr>";
		$page .= "<tr><td>Guild Tag (Max 3 Letters):<br>";
		$page .= "<input type='text' size='5' maxlength='3' name='tag'> <i>Your Guilds Tag. (Guild Initials)</i><br>";
		$page .= "<tr><td><hr></td></tr>";
		$page .= "<tr><td>Guild Joining Cost:<br>";
		$page .= "<input type='text' size='5' maxlength='8' name='joincost'> <i>Required cost in Dragon Scales to enter your Guild</i><br>";
		$page .= "<tr><td><hr></td></tr>";
		$page .= "<tr><td>Guild Password:<br>";
		$page .= "<input type='text' size='10' maxlength='15' name='password'> <i>A password to enter your Guild.</i><br>";
		$page .= "<tr><td><hr></td></tr>";
		
				$page .= "<tr><td>Guild Description:<br>";
		$page .= "<textarea name=\"description\" rows=\"5\" cols=\"30\"></textarea> <i>A small description about your Guild.</i><br>";
		$page .= "<tr><td><hr></td></tr>";
		
		$page .= "<tr><td><input type='submit' name='submit' value='Create a Guild'></form>";
		$page .= "</td><td><form action='guilds.php'>";
		$page .= "</form></td></tr></table><p>You may return to the <a href='guilds.php'>Guild Courtyard</a>, if you have changed your mind.";
    
    if (isset($_POST["submit"])) {

        extract($_POST);
    
    		
$page = "<table width='100%'><tr><td class='title'>Create a Guild</td></tr></table>";

	$query = doquery("INSERT INTO {{table}} SET name='$name', tag='$tag', password='$password', description='$description', joincost='$joincost', founder='".$userrow["charname"]."'  ", "guilds");
	$newdscales = $userrow["dscales"] - 1500;
	$newgold = $userrow["gold"] - 500000;
	$newap = $userrow["currentap"] - 30;
	$query = doquery("UPDATE {{table}} SET dscales='$newdscales', currentap='$newap', gold='$newgold', guildname='$name', guildrank='200' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	$page .= "<p>You have successfully created your Guild, named <b>".$userrow["guildname"]."</b>. ";
	
	$page .= "<center><br><a href='guilds.php'>Back to the Guild Courtyard</a></center>";
	display($page,"Create a Guild");
    	}  
    
    }
    display($page,"Create a Guild");     
}
?>