<?php // gamemail.php :: Internal Game Mail script for the game.

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

	if ($do[0] == "read") { showmail($do[1]); }
	elseif ($do[0] == "outbox") { outbox(); }
	elseif ($do[0] == "saved") { saved(); }	
	elseif ($do[0] == "write") { newmessage(); }
	elseif ($do[0] == "reply") { reply(); }
	elseif ($do[0] == "savemail") { savemail($do[1]); }
	elseif ($do[0] == "delete") { delete($do[1]); }
    elseif ($do[0] == "deleteall") { deleteall(); }
    elseif ($do[0] == "ignorelist") { ignorelist(); }    
	elseif ($do[0] == "list") { donothing($do[1]); }
	else{donothing(0); } 

} else { donothing(0);} 

function donothing($start) {
	global $userrow;  
$u = doquery("UPDATE {{table}} SET location='Game Mail' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	$player = $userrow["charname"];
    $query = doquery("SELECT * FROM {{table}} WHERE recipient='$player' AND save!='1' ORDER BY postdate DESC LIMIT ".$start.",20", "gamemail");
    $fullquery = doquery("SELECT * FROM {{table}} WHERE recipient='$player' AND save!='1' ORDER BY postdate DESC", "gamemail");
    $page = "<table width=\"100%\"><tr><td class=\"title\">Inbox</td></tr></table>";
 $page .= "<hr /><table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"5\" style=\"background-color:#dddddd;\"><br><a href='gamemail.php?do=write'><img src='img/compose.gif' alt='Compose New Mail' title='Compose New Mail' border='0'/></a><a href='gamemail.php'><img src='img/inbox.gif' alt='Inbox' title='Inbox' border='0'/></a> <a href='gamemail.php?do=outbox'><img src='img/outbox.gif' alt='Outbox' title='Outbox' border='0'/></a> <a href='gamemail.php?do=saved'><img src='img/saved.gif' alt='Saved' title='Saved' border='0'/></a> <a href='gamemail.php?do=deleteall'><img src='img/deleteall.gif' alt='Delete All' title='Delete All' border='0'/></a><br><br></th></tr><tr><th width=\"20%\" style=\"background-color:#dddddd;\">From</th><th width=\"50%\" style=\"background-color:#dddddd;\">Mail Subject</th><th width=\"30%\" style=\"background-color:#dddddd;\">Date</th><th  width=\"1%\" style=\"background-color:#dddddd;\">Delete</th></tr>\n";
    $count = 1;
    if (mysql_num_rows($query) == 0) {
        $page .= "<tr><td style=\"background-color:#ffffff;\" colspan=\"4\"><b>No New Messages.</b></td></tr>\n";
    } else {
        while ($row = mysql_fetch_array($query)) {
	  $newicon="";
	  	  $authorquery = doquery("SELECT id FROM {{table}} WHERE charname='".$row["author"]."' LIMIT 1", "users");
	  $authorrow = mysql_fetch_array($authorquery);
	  if ($row["mread"] == "0") { $newicon = "<img src='img/newicon.gif'>"; }
		if ($count == 1) {
            	$page .= "<tr><td style=\"background-color:#ffffff;\"><a href='index.php?do=onlinechar:".$authorrow["id"]."'>".$row["author"]."</a></td><td style=\"background-color:#ffffff;\">".$newicon." <a href=\"gamemail.php?do=read:".$row["id"]."\">".$row["subject"]."</a></td><td style=\"background-color:#ffffff;\">".$row["postdate"]."</td><td style=\"background-color:#ffffff;\"><center><a href=gamemail.php?do=delete:".$row["id"]."><img src='img/btn_delete.gif' border=\"0\"></center></a></td><tr>\n";
			$count = 2;
		} else {
            	$page .= "<tr><td style=\"background-color:#eeeeee;\"><a href='index.php?do=onlinechar:".$authorrow["id"]."'>".$row["author"]."</a></td><td style=\"background-color:#eeeeee;\">".$newicon." <a href=\"gamemail.php?do=read:".$row["id"]."\">".$row["subject"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["postdate"]."</td><td style=\"background-color:#eeeeee;\"><center><a href=gamemail.php?do=delete:".$row["id"]."><img src='img/btn_delete.gif' border=\"0\"></center></a></td><tr>\n";
			$count = 1;
		}
        }
    }
	$page .= "<tr><td colspan='5' style='background-color:#dddddd;'><center> Pages [ ";
    $numpages = intval(mysql_num_rows($fullquery)/20);
	for($pagenum = 0; $pagenum <= $numpages; $pagenum++) {
		$pagestart = $pagenum*20;
		$pagelink = $pagenum + 1;
		if ($start != $pagestart) {
		$page .= "<a href='gamemail.php?do=list:".$pagestart."'>".$pagelink."</a>   ";}
		else {
		$page .= "<i>".$pagelink."</i>   ";}
	}
	$page .= " ]</center></td></tr>";
    $page .= "<tr><td colspan=\"5\" style=\"background-color:#dddddd;\"><center>";
	$page .= "Mails marked with a <img src=\"img/newicon.gif\"> are Unread Messages.<p>All messages sent by the name of <b>Admin</b> are Official messages. Please do not share personal and private information over this for your own safety.<br>";
	$page .= "</center></td></tr></table></table>";
	$page .= "<center><br><a href='index.php'>Return to the Game</a></center>";

    display($page, "Inbox");

}

function showmail($id) {

	global $userrow;

    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' ORDER BY id LIMIT 1", "gamemail");
    $query2 = doquery("SELECT subject FROM {{table}} WHERE id='$id' LIMIT 1", "gamemail");
    $row2 = mysql_fetch_array($query2);
	$readthis = doquery("UPDATE {{table}} SET mread='1' WHERE id='$id'", "gamemail");
	  while ($row = mysql_fetch_array($query)) {
			 $query3 = doquery("SELECT authlevel,customtitle,avatarlink FROM {{table}} WHERE charname='".$row["author"]."' LIMIT 1", "users");
	$row3 = mysql_fetch_array($query3); 
	$titi = $row3["customtitle"];
    $titi2 = $row3["avatarlink"];//Get avatar link and title
	  	if ($row3["authlevel"] == "1") { //Admin avatar
	  		$avatar = "Status: Administrator<br><img src=\"http://dk-rpg.com/gfx/avataradmin.gif\" alt=\"Administrator\"><p>";
	  	}
	  	elseif ($row3["authlevel"] == "3") { //Mod
	  		$avatar = "Status: Moderator<br><img src=\"$titi2\" alt=\"$titi\" width=\"80\" height=\"80\"><p>";
	  	} else {		
	  		$avatar = "Status: Member<br><img src=\"$titi2\" alt=\"$titi\" width=\"60\" height=\"60\"><p>";
	  	}
    $page = "<table width=\"100%\"><tr><td class=\"title\">View Mail</td></tr></table>";
    $page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><td colspan=\"2\" style=\"background-color:#dddddd;\"><b><a href='gamemail.php'>Dragons Kingdom Mail</a> :: ".$row2["subject"]."</b></td><td style=\"background-color:#dddddd;\"><a href=gamemail.php?do=savemail:".$id."><img src='img/savemail.gif' border=\"0\"></a> <a href=gamemail.php?do=delete:".$id."><img src='img/btn_delete.gif' border=\"0\"></a> </td></tr>\n";
    $count = 1;
     	
    			$authorquery = doquery("SELECT id FROM {{table}} WHERE charname='".$row["author"]."' LIMIT 1", "users");
		$authorrow = mysql_fetch_array($authorquery);
		    $row = str_replace(":)", "<img src='images/smilies/smile.gif'>", $row); //16 Smilies
			    $row = str_replace(":(", "<img src='images/smilies/sad.gif'>", $row); 			     
		        $row = str_replace(":P", "<img src='images/smilies/tongue.gif'>", $row);
			    $row = str_replace(";)", "<img src='images/smilies/wink.gif'>", $row); 
			    $row = str_replace("(ha)", "<img src='images/smilies/biggrin.gif'>", $row);
			    $row = str_replace("^^", "<img src='images/smilies/rolleyes.gif'>", $row); 
			    $row = str_replace("o.O", "<img src='images/smilies/freak.gif'>", $row);
			    $row = str_replace(":$", "<img src='images/smilies/embaressed.gif'>", $row);
			    $row = str_replace("(c)", "<img src='images/smilies/cool.gif'>", $row); 
			    $row = str_replace(":@", "<img src='images/smilies/mad.gif'>", $row); 
			    $row = str_replace(":/", "<img src='images/smilies/umm.gif'>", $row); 	
			    $row = str_replace(":O", "<img src='images/smilies/shocked.gif'>", $row); 
			    $row = str_replace(":?", "<img src='images/smilies/ques-tion.gif'>", $row); 	
			    $row = str_replace(":!", "<img src='images/smilies/exclamation.gif'>", $row); 
			    $row = str_replace(":D", "<img src='images/smilies/lol.gif'>", $row); 
			    $row = str_replace(":%", "<img src='images/smilies/drool.gif'>", $row); 
            $page .= "<tr><td width=\"25%\" style=\"background-color:#ffffff; vertical-align:top;\"><span class=\"small\"><b><a href='index.php?do=onlinechar:".$authorrow["id"]."'>".$row["author"]."</a></b><br />".$avatar."<br /><br />".prettyforumdate($row["postdate"])."</td><td style=\"background-color:#ffffff; vertical-align:top;\" colspan=\"2\">".nl2br($row["content"])."</td></tr>\n";
			    $row = str_replace(":)", "<img src='images/smilies/smile.gif'>", $row); //16 Smilies
			    $row = str_replace(":(", "<img src='images/smilies/sad.gif'>", $row); 			     
		        $row = str_replace(":P", "<img src='images/smilies/tongue.gif'>", $row);
			    $row = str_replace(";)", "<img src='images/smilies/wink.gif'>", $row); 
			    $row = str_replace("(ha)", "<img src='images/smilies/biggrin.gif'>", $row);
			    $row = str_replace("^^", "<img src='images/smilies/rolleyes.gif'>", $row); 
			    $row = str_replace("o.O", "<img src='images/smilies/freak.gif'>", $row);
			    $row = str_replace(":$", "<img src='images/smilies/embaressed.gif'>", $row);
			    $row = str_replace("(c)", "<img src='images/smilies/cool.gif'>", $row); 
			    $row = str_replace(":@", "<img src='images/smilies/mad.gif'>", $row); 
			    $row = str_replace(":/", "<img src='images/smilies/umm.gif'>", $row); 	
			    $row = str_replace(":O", "<img src='images/smilies/shocked.gif'>", $row); 
			    $row = str_replace(":?", "<img src='images/smilies/ques-tion.gif'>", $row); 	
			    $row = str_replace(":!", "<img src='images/smilies/exclamation.gif'>", $row); 
			    $row = str_replace(":D", "<img src='images/smilies/lol.gif'>", $row); 
			    $row = str_replace(":%", "<img src='images/smilies/drool.gif'>", $row); 
    $page .= "</table></td></tr></table><br />";
    $page .= "<table width=\"100%\"><tr><td><b>Reply to this Mail:</b><br />";
    $page .= "<form action=\"gamemail.php?do=reply\" method=\"post\">";
    $page .= "<input type=\"hidden\" name=\"author\" value='".$userrow["charname"]."' />";
    $page .= "<input type=\"hidden\" name=\"recipient\" value='".$row["author"]."' />";
    $page .= "Subject: <input type='text' name=\"subject\" value=\"".$row["subject"]."\" /><br>";
    $page .= "<textarea name=\"content\" rows=\"7\" cols=\"40\">";
    $page .= "\n\n___________________________\n";
    $page .= $row["author"]." wrote:\n".$row["content"];
    $page .= "</textarea><br />";
    $page .= "<input type=\"submit\" name=\"submit\" value=\"Send Mail\" />";
    $page .= "<input type=\"reset\" name=\"reset\" value=\"Reset\" />";
    $page .= "</form></td></tr>";

    }
	$page .= "</table>";
    $page .= "<center><p><br><a href='index.php'>Return to the Game</a></center>";
    display($page, "View Mail");

}

function outbox() {
	global $userrow;  
	$player = $userrow["charname"];
    $query = doquery("SELECT * FROM {{table}} WHERE author='$player' ORDER BY postdate DESC LIMIT 25", "gamemail");

    $page = "<table width=\"100%\"><tr><td class=\"title\">Outbox</td></tr></table><p>Here is the last 25 Mails that you have sent out. Here it will tell you whether the other person has Read or Not Read your Mails.";
 $page .= "<hr /><table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"4\" style=\"background-color:#dddddd;\"><br><a href='gamemail.php?do=write'><img src='img/compose.gif' alt='Compose New Mail' title='Compose New Mail' border='0'/></a><a href='gamemail.php'><img src='img/inbox.gif' alt='Inbox' title='Inbox' border='0'/></a> <a href='gamemail.php?do=outbox'><img src='img/outbox.gif' alt='Outbox' title='Outbox' border='0'/></a> <a href='gamemail.php?do=saved'><img src='img/saved.gif' alt='Saved' title='Saved' border='0'/></a> <a href='gamemail.php?do=deleteall'><img src='img/deleteall.gif' alt='Delete All' title='Delete All' border='0'/></a><br><br></th></tr><tr><th width=\"20%\" style=\"background-color:#dddddd;\">Sent To</th><th width=\"50%\" style=\"background-color:#dddddd;\">Mail Subject</th><th width=\"30%\" style=\"background-color:#dddddd;\">Date</th></tr>\n";
    $count = 1;
    if (mysql_num_rows($query) == 0) {
        $page .= "<tr><td style=\"background-color:#ffffff;\" colspan=\"4\"><b>No Sent Messages.</b></td></tr>\n";
    } else {
        while ($row = mysql_fetch_array($query)) {
	  $newicon="";
	  	  $authorquery = doquery("SELECT id FROM {{table}} WHERE charname='".$row["recipient"]."' LIMIT 1", "users");
	  $authorrow = mysql_fetch_array($authorquery);
	  if ($row["mread"] == "0") { $newicon = "<img src='img/unread.gif'>"; }
		if ($count == 1) {
            	$page .= "<tr><td style=\"background-color:#ffffff;\"><a href='index.php?do=onlinechar:".$authorrow["id"]."'>".$row["recipient"]."</a></td><td style=\"background-color:#ffffff;\">".$newicon." <b>".$row["subject"]."</b></td><td style=\"background-color:#ffffff;\">".$row["postdate"]."</td></td><tr>\n";
			$count = 2;
		} else {
            	$page .= "<tr><td style=\"background-color:#eeeeee;\"><a href='index.php?do=onlinechar:".$authorrow["id"]."'>".$row["recipient"]."</a></td><td style=\"background-color:#eeeeee;\">".$newicon." <b>".$row["subject"]."</b></td><td style=\"background-color:#eeeeee;\">".$row["postdate"]."</td></td><tr>\n";
			$count = 1;
		}
        }
    }

    $page .= "<tr><td colspan=\"4\" style=\"background-color:#dddddd;\"><center>";
	$page .= "Last 25 Mails which you have sent. Mails marked with a <img src=\"img/unread.gif\"> are Unread Messages.<p>All messages sent by the name of <b>Admin</b> are Official messages. Please do not share personal and private information over this for your own safety.<br>";
	$page .= "</center></td></tr></table></table>";
	$page .= "<center><br><a href='index.php'>Return to the Game</a></center>";

    display($page, "Outbox");

}

function saved() {
	global $userrow;  

	$player = $userrow["charname"];
    $query = doquery("SELECT * FROM {{table}} WHERE recipient='$player' AND save='1' ORDER BY postdate DESC LIMIT 25", "gamemail");
    $fullquery = doquery("SELECT * FROM {{table}} WHERE recipient='$player'  AND save='1'  ORDER BY postdate DESC", "gamemail");
    $page = "<table width=\"100%\"><tr><td class=\"title\">Saved</td></tr></table>";
 $page .= "<hr /><table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"5\" style=\"background-color:#dddddd;\"><br><br><a href='gamemail.php?do=write'><img src='img/compose.gif' alt='Compose New Mail' title='Compose New Mail' border='0'/></a><a href='gamemail.php'><img src='img/inbox.gif' alt='Inbox' title='Inbox' border='0'/></a> <a href='gamemail.php?do=outbox'><img src='img/outbox.gif' alt='Outbox' title='Outbox' border='0'/></a> <a href='gamemail.php?do=saved'><img src='img/saved.gif' alt='Saved' title='Saved' border='0'/></a> <a href='gamemail.php?do=deleteall'><img src='img/deleteall.gif' alt='Delete All' title='Delete All' border='0'/></a><br><br></th></tr><tr><th width=\"20%\" style=\"background-color:#dddddd;\">From</th><th width=\"50%\" style=\"background-color:#dddddd;\">Mail Subject</th><th width=\"30%\" style=\"background-color:#dddddd;\">Date</th><th  width=\"1%\" style=\"background-color:#dddddd;\">Delete</th></tr>\n";
    $count = 1;
    if (mysql_num_rows($query) == 0) {
        $page .= "<tr><td style=\"background-color:#ffffff;\" colspan=\"4\"><b>No Saved Messages.</b></td></tr>\n";
    } else {
        while ($row = mysql_fetch_array($query)) {
	  $newicon="";
	  	  $authorquery = doquery("SELECT id FROM {{table}} WHERE charname='".$row["author"]."' LIMIT 1", "users");
	  $authorrow = mysql_fetch_array($authorquery);
	  if ($row["mread"] == "0") { $newicon = "<img src='img/newicon.gif'>"; }
		if ($count == 1) {
            	$page .= "<tr><td style=\"background-color:#ffffff;\"><a href='index.php?do=onlinechar:".$authorrow["id"]."'>".$row["author"]."</a></td><td style=\"background-color:#ffffff;\">".$newicon." <a href=\"gamemail.php?do=read:".$row["id"]."\">".$row["subject"]."</a></td><td style=\"background-color:#ffffff;\">".$row["postdate"]."</td><td style=\"background-color:#ffffff;\"><center><a href=gamemail.php?do=delete:".$row["id"]."><img src='img/btn_delete.gif' border=\"0\"></center></a></td><tr>\n";
			$count = 2;
		} else {
            	$page .= "<tr><td style=\"background-color:#eeeeee;\"><a href='index.php?do=onlinechar:".$authorrow["id"]."'>".$row["author"]."</a></td><td style=\"background-color:#eeeeee;\">".$newicon." <a href=\"gamemail.php?do=read:".$row["id"]."\">".$row["subject"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["postdate"]."</td><td style=\"background-color:#eeeeee;\"><center><a href=gamemail.php?do=delete:".$row["id"]."><img src='img/btn_delete.gif' border=\"0\"></center></a></td><tr>\n";
			$count = 1;
		}
        }
    }

    $page .= "<tr><td colspan=\"4\" style=\"background-color:#dddddd;\"><center>";
	$page .= "You can save upto 25 Mails.<p>All messages sent by the name of <b>Admin</b> are Official messages. Please do not share personal and private information over this for your own safety.<br>";
	$page .= "</center></td></tr></table></table>";
	$page .= "<center><br><a href='index.php'>Return to the Game</a></center>";

    display($page, "Saved");

}

function reply() {

    global $userrow;
if ($userrow["authlevel"] == 4){ die( //Mute a player from chatting and game mailing
"Your account has been muted from the Player Chat, posting Comments, the Forum and from Game Mails.<p>This is most probably temporary due to you breaking the rules, or causing problems.<p>Please return to what you were previously doing.");
	}
	extract($_POST);
    $content = trim($content);
	$query = doquery("INSERT INTO {{table}} SET postdate=NOW(),author='$author',recipient='$recipient',subject='$subject',content='$content'", "gamemail");
	header("Location: gamemail.php");
	die();

}

function savemail($message) {

	$updatequery = doquery("UPDATE {{table}} SET save='1' WHERE id='$message' LIMIT 1", "gamemail");
	header("Location: gamemail.php");
	die();
	
}
function delete($message) {

	$query = doquery("DELETE FROM {{table}} WHERE id='$message' LIMIT 1", "gamemail");
	header("Location: gamemail.php");
	die();

}

function deleteall() {
	global $userrow;
	if (isset($_POST["DeleteThemAll"])) {
	$query = doquery("DELETE FROM {{table}} WHERE recipient='".$userrow["charname"]."' AND mread!='0' AND save!='1' ", "gamemail");
	header("Location: gamemail.php");
	die();
	}
	if (isset($_POST["CancelThatPurge"])) {
	header("Location: gamemail.php");
	die();
	}
	$page = "<table width='100%'><tr><td class='title'>Delete All</td></tr></table>";
	$page .= "<p><blink><b>Warning:</b></blink> You are attempting to delete all Game Mails from your inbox!<p>";
	$page .= "Are you sure you wish to delete all of them? ";
	$page .= "If you click on the Delete All button, all messages will be permanently deleted forever. All unread and saved messages will not be deleted.<br>";
	$page .= "<form action='gamemail.php?do=deleteall' method='POST'>";
	$page .= "<input type='submit' name='DeleteThemAll' value='Delete All'> ";
	$page .= "<input type='submit' name='CancelThatPurge' value='Cancel'><p>";
    $page .= "<center><br><a href='index.php'>Return to the Game</a></center>";
    display($page, "Delete All");
}

function newmessage() {

    global $userrow;

if ($userrow["authlevel"] == 4){ die( //Mute a player from chatting and game mailing
"Your account has been muted from the Player Chat, posting Comments, the Forum and from Game Mails.<p>This is most probably temporary due to you breaking the rules, or causing problems.<p>Please return to what you were previously doing.");
	}

	$errorlist = "";
    if (isset($_POST["submit"])) {
        extract($_POST);
	  $subject = trim($subject,"\x7f..\xff\x0..\x1f");
	  $subject = trim($subject).".";
	  $content = trim($content);
	  if ($subject == "") { $errorlist .= "<li><b>Subject Required!</b><br>";}
	  if ($recipient == "Admin") { $errorlist .= "<li><b>You cannot Game Mail the Administrator. If you have a problem or need to Report a Bug, please use the Contact Suppport link.</b><br>";}
	  if ($recipient == "admin") { $errorlist .= "<li><b>You cannot Game Mail the Administrator. If you have a problem or need to Report a Bug, please use the Contact Suppport link.</b><br>";}
	  if ($recipient == "Adam") { $errorlist .= "<li><b>You cannot Game Mail the Administrator. If you have a problem or need to Report a Bug, please use the Contact Suppport link.</b><br>";}
	  if ($recipient == "adam") { $errorlist .= "<li><b>You cannot Game Mail the Administrator. If you have a problem or need to Report a Bug, please use the Contact Suppport link.</b><br>";}
	  if ($recipient == "") { $errorlist .= "<li><b>Recipient Required!</b><br>";}
	  if ($content == "") { $errorlist .= "<li><b>Blank Message!</b><br>";}
	  	else {
        $query = doquery("SELECT charname FROM {{table}} WHERE charname='".$recipient."' LIMIT 1", "users");
        if (mysql_num_rows($query) != 1) { $errorlist .= "<li><b>\"".$recipient."\"</b> is not a valid character name.  Please try again.<br>";}
		}
    if ($errorlist != "") {
    	$page = "<table width=\"100%\"><tr><td class=\"title\">Send Mail Message</td></tr></table>";
    	$page .= "<ul><li><b><u>Error!</u></b>".$errorlist."</ul><br>";
    	$page .= "<table width=\"100%\"><tr><td><b>Send Mail:</b><br /><br/ >";
    	$page .= "<form action=\"gamemail.php?do=write\" method=\"post\">";
    	$page .= "Recipient: <i>(type in the exact character name)</i><br>";
    	$page .= "<input type=\"text\" name=\"recipient\" size=\"30\" value=\"$recipient\" maxlength=\"30\" /><br><br>";
    	$page .= "Subject:<br />";
    	$page .= "<input type=\"text\" name=\"subject\" size=\"35\" value=\"New Message\" maxlength=\"35\"  /><br><br>";
    	$page .= "Message:<br />";
    	$page .= "<textarea name=\"content\" rows=\"7\" cols=\"40\">".$content."</textarea><br><br>";
    	$page .= "<input type=\"submit\" name=\"submit\" value=\"Send Mail\" /> ";
    	$page .= "<input type=\"reset\" name=\"reset\" value=\"Reset\" />";
    	$page .= "</form></td></tr></table>";
		$page .= "<center><a href='index.php'>Return to the Game</a></center>";
    		display($page, "Dragons Kingdom Mail");
    	} else {
      $content = str_replace("'", "`", $content);
        $query = doquery("INSERT INTO {{table}} SET postdate=NOW(),author='".$userrow["charname"]."',recipient='$recipient',subject='$subject',content='$content'", "gamemail");
 		header("Location: gamemail.php");

		die();
		}
	}  extract($_POST);
    $page = "<table width=\"100%\"><tr><td class=\"title\">Send Mail Message</td></tr></table>";
	$page .= "<table width=\"100%\"><tr><td>";
    $page .= "<form action=\"gamemail.php?do=write\" method=\"post\">";
    $page .= "Recipient: <i>(type in the exact character name)</i><br>";
    $page .= "<input type=\"text\" name=\"recipient\" size=\"30\" value=\"$recipient\" maxlength=\"30\" /><br><br>";
    $page .= "Subject:<br />";
    $page .= "<input type=\"text\" name=\"subject\" size=\"35\" value=\"New Message\" maxlength=\"35\" /><br><br>";
    $page .= "Message:<br />";
    $page .= "<textarea name=\"content\" rows=\"7\" cols=\"40\"></textarea><br><br>";
    $page .= "<input type=\"submit\" name=\"submit\" value=\"Send Mail\" /> ";
    $page .= "<input type=\"reset\" name=\"reset\" value=\"Reset\" />";
    $page .= "</form></td></tr></table>";
	$page .= "<center><a href='index.php'>Return to the Game</a></center>"; 
    display($page, "Dragons Kingdom Mail");

}

function ignorelist() {
	global $userrow;

	if (isset($_POST["submit"])) {
		$ignorelist = $_POST["ignorelist"];
		$ignorelist = my_htmlspecialchars($ignorelist);
$updatequery = doquery("UPDATE {{table}} SET ignorelist='$ignorelist' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	} elseif (isset($_POST["cancel"])) {
        header("Location: index.php"); die();

	}
    $userquery = doquery("SELECT * FROM {{table}} WHERE id='".$userrow["id"]."' LIMIT 1", "users");
    $userrow = mysql_fetch_array($userquery);
    

	$page = "<table width='100%'><tr><td class='title'>Ignore List</td></tr></table>";
	$page .= "<p>Welcome to your Personal Player Notepad. Here you can take note of anything that you wish to remember. No one else can see this page but you. Some ignorelist you may wish to save are Soul numbers*, or anything else to help you in your quest to victory.<p>";
	$page .= "<form action='gamemail.php?do=ignorelist' method='POST'><center><table width='75%'>";
    $page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><td colspan=\"0\" style=\"background-color:#dddddd;\"><b>Ignore List</td></tr>\n";
	$page .= "<td bgcolor='#ffffff'><textarea name='ignorelist' cols='60' rows='10' wrap='virtual'>";
	$page .= $userrow["ignorelist"]."</textarea></td></tr>";
$page .= "<tr><td bgcolor='#eeeeee' colspan='2'> </td></tr>";
	$page .= "<tr><td bgcolor='#ffffff' colspan='2'> ";
	$page .= "<center><input type='submit' name='submit' value='Update List'>        -        ";
	$page .= "<input type='submit' name='cancel' value='Cancel'></center></td></tr></table></table></center>";

    	$page .= "<p><i>*Soul numbers become invalid after a short period of time. They merely dissapear.</i><p><center><br>Return to what you were <a href='index.php'>doing</a>.</center>";
    	display($page,"Ignore List");

}

?>