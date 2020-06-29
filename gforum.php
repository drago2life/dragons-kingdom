<?php // gforum.php :: Internal forums script for the game.

include('lib.php');
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
die("<b>You have been banned</b><p>Your accounts has been banned and you have been placed into the Town Jail. This may well be permanent, or just a 24 hour temporary warning ban. If you want to be unbanned, contact the game administrator by emailing admin@domain.com."); }
//No guild
if ($userrow["guildname"] == "-") { display("<p>You are not in a Guild. You must join a guild to access this area. Return to the <a href='index.php'>Game</a>","Guild Forum"); die(); }

$updatequery = doquery("UPDATE {{table}} SET location='Guild Forum' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
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

	if ($do[0] == "thread") { showthread($do[1], $do[2]); }
    elseif ($do[0] == "editpost") { editpost($do[1]); }
	elseif ($do[0] == "new") { newthread(); }
	elseif ($do[0] == "reply") { reply(); }
	elseif ($do[0] == "delete") { delete($do[1]); }
	elseif ($do[0] == "list") { donothing($do[1]); }

} else { donothing(0); }

function donothing($start=0) {
global $userrow;


  
      $query2 = doquery("SELECT * FROM {{table}} WHERE pin='1' AND guildname='".$userrow["guildname"]."' ORDER BY newpostdate DESC LIMIT 20", "gforum");
 $page = "<table width='100%' border='1'><tr><td class='title'>Guild Forum</td></tr></table><p>";

 $page .= "<hr /><p><table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"4\" style=\"background-color:#dddddd;\"><center>Only Administrators can Pin Threads for Guilds. Please message one if you think one of your Threads is good enough to be Pinned.</center></th></tr><tr><th width=\"44%\" style=\"background-color:#dddddd;\">Pinned Threads</th><th width=\"2%\" style=\"background-color:#dddddd;\">Replies</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Author</th><th  width=\"30%\" style=\"background-color:#dddddd;\">Last Post</th></tr>\n";
	$count = 1;
    if (mysql_num_rows($query2) == 0) {
       $page .= "<tr><td style='background-color:#ffffff;' colspan='4'><b>No threads Pinned.</b></td></tr>\n";
    } else {
      while ($row = mysql_fetch_array($query2)) {
	  	if ($row["close"] != "1") {
	  		$namelink2 = "<font color=red><b>Pinned:<b/></font> ";
	  	} else {
	  		$namelink2 = "<img src='img/padlock.gif'><font color=red><b>Pinned:<b/></font> ";
	  	}
		if ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\">".$namelink2."<a href=\"gforum.php?do=thread:".$row["id"].":0\">".$row["title"]."</a></td><td style=\"background-color:#ffffff;\">".$row["replies"]."</td><td style=\"background-color:#ffffff;\">".$row["author"]."</td><td style=\"background-color:#ffffff;\">".$row["newpostdate"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">".$namelink2."<a href=\"gforum.php?do=thread:".$row["id"].":0\">".$row["title"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["replies"]."</td><td style=\"background-color:#eeeeee;\">".$row["author"]."</td><td style=\"background-color:#eeeeee;\">".$row["newpostdate"]."</td></tr>\n";
			$count = 1;
		}
	  }
    }

    $page .= "</table></td></tr></table><hr />";

$query= doquery("SELECT * FROM {{table}} WHERE parent='0' AND pin!='1' AND guildname='".$userrow["guildname"]."' ORDER BY newpostdate DESC LIMIT ".$start.",12", "gforum");
$fullquery = doquery("SELECT * FROM {{table}} WHERE parent='0' AND pin!='1' AND guildname='".$userrow["guildname"]."' ORDER BY newpostdate", "gforum");
 $page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"4\" style=\"background-color:#dddddd;\"><center><a href=\"gforum.php?do=new\">Create a New Thread</a></center></th></tr><tr><th width=\"44%\" style=\"background-color:#dddddd;\">Thread Title</th><th width=\"2%\" style=\"background-color:#dddddd;\">Replies</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Author</th><th  width=\"30%\" style=\"background-color:#dddddd;\">Last Post</th></tr>\n";
	$count = 1;
    if (mysql_num_rows($query) == 0) {
       $page .= "<tr><td style='background-color:#ffffff;' colspan='4'><b>No threads in Guild forum.</b></td></tr>\n";
    } else {
      while ($row = mysql_fetch_array($query)) {
	  	if ($row["close"] != "1") {
	  		$namelink = "";
	  	} else {
	  		$namelink = "<img src='img/padlock.gif'>";
	  	}
		if ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\">".$namelink."<a href=\"gforum.php?do=thread:".$row["id"].":0\">".$row["title"]."</a></td><td style=\"background-color:#ffffff;\">".$row["replies"]."</td><td style=\"background-color:#ffffff;\">".$row["author"]."</td><td style=\"background-color:#ffffff;\">".$row["newpostdate"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">".$namelink."<a href=\"gforum.php?do=thread:".$row["id"].":0\">".$row["title"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["replies"]."</td><td style=\"background-color:#eeeeee;\">".$row["author"]."</td><td style=\"background-color:#eeeeee;\">".$row["newpostdate"]."</td></tr>\n";
			$count = 1;
		}
	  }
    }

	$page .= "<tr><td colspan='5' style='background-color:#dddddd;'><center> Pages [ ";
    $numpages = intval(mysql_num_rows($fullquery)/12);
	for($pagenum = 0; $pagenum <= $numpages; $pagenum++) {
		$pagestart = $pagenum*12;
		$pagelink = $pagenum + 1;
		if ($start != $pagestart) {
		$page .= "<a href='gforum.php?do=list:".$pagestart."'>".$pagelink."</a>   ";}
		else {
		$page .= "<i>".$pagelink."</i>   ";}
	}
	$page .= " ]</center></td></tr>";
    $page .= "</table></td></tr></table><hr />";
    $page .= "<p>You may return to what you were <a href=\"index.php\">doing</a>, or use the compass on the right to start exploring.<br />\n";

    display($page, "Guild Forum");

}

function showthread($id, $start) {

global $userrow, $controlrow;

    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' OR parent='$id' ORDER BY id LIMIT $start,50", "gforum");
    $query2 = doquery("SELECT title FROM {{table}} WHERE id='$id' LIMIT 1", "gforum");
    $row1 = mysql_fetch_array($query);
    $row2 = mysql_fetch_array($query2);

    if ($row1["guildname"] != $userrow["guildname"]) {
    $page = "<table width='100%' border='1'><tr><td class='title'>Guild Forum - View Thread</td></tr></table><p>[<a href=\"#bottom\">Go to Bottom</a>]<p>";
    $page .= "This thread is invalid or belongs to a posting for another Guilds Forum.<br>";
    $page .= "You are only able to view postings for your own Guild. Cheat attempt sent to administrator.<p>";
	display($page, "Error");
    }

 $page = "<table width='100%' border='1'><tr><td class='title'>Guild Forum - View Thread</td></tr></table><p>";
    $page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><td colspan=\"2\" style=\"background-color:#dddddd;\"><b><a href=\"gforum.php\">Guild Forum</a> :: ".$row2["title"]."</b></td></tr>\n";
    $count = 1;
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' OR parent='$id' ORDER BY id LIMIT $start,50", "gforum");
    
    while ($row = mysql_fetch_array($query)) {

		 $query3 = doquery("SELECT postcount,authlevel,customtitle,avatarlink FROM {{table}} WHERE charname='".$row["author"]."' LIMIT 1", "users");
	$row3 = mysql_fetch_array($query3); 
	
		 $authorquery = doquery("SELECT id FROM {{table}} WHERE charname='".$row["author"]."' ", "users");
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
	$titi = $row3["customtitle"];
    $titi2 = $row3["avatarlink"];//Get avatar link and title
	  	if ($row3["authlevel"] == "1") { //Admin avatar
	  		$avatar = "Status: Administrator<br><img src=\"gfx/avataradmin.gif\" alt=\"Administrator\"><p>";
	  	}
	  	elseif ($row3["authlevel"] == "3") { //Mod
	  		$avatar = "Status: Moderator<br><img src=\"$titi2\" alt=\"$titi\" width=\"80\" height=\"80\"><p>";
	  	} else {		
	  		$avatar = "Status: Member<br><img src=\"$titi2\" alt=\"$titi\" width=\"60\" height=\"60\"><p>";
	  	}
        if ($count == 1) {
            $page .= "<tr><td width=\"25%\" style=\"background-color:#ffffff; vertical-align:top;\"><span class=\"small\"><b>".$row["author"]."</b><br />".$avatar."Posts: ".$row3["postcount"]."<br />".prettyforumdate($row["postdate"])."</td><td style=\"background-color:#ffffff; vertical-align:top;\">".nl2br($row["content"])."<br><br><hr /><style=\"background-color:#eeeeee; vertical-align:bottom;\">[<a href=\"index.php?do=onlinechar:".$authorrow["id"]."\">View Profile</a>] [<a href=\"gforum.php?do=editpost:".$row["id"]."\">Edit Post</a>]</td></tr>\n";
            $count = 2;
        } else {
            $page .= "<tr><td width=\"25%\" style=\"background-color:#eeeeee; vertical-align:top;\"><span class=\"small\"><b>".$row["author"]."</b><br />".$avatar."Posts: ".$row3["postcount"]."<br />".prettyforumdate($row["postdate"])."</td><td style=\"background-color:#eeeeee; vertical-align:top;\">".nl2br($row["content"])."<br><br><hr /><style=\"background-color:#eeeeee; vertical-align:bottom;\">[<a href=\"index.php?do=onlinechar:".$authorrow["id"]."\">View Profile</a>] [<a href=\"gforum.php?do=editpost:".$row["id"]."\">Edit Post</a>]</td></tr>\n";
            $count = 1;
        }
    }

    $page .= "</table></td></tr></table><br />";

$query = doquery("SELECT * FROM {{table}} WHERE id='$id' OR parent='$id' ORDER BY id LIMIT $start,50", "gforum");
$row = mysql_fetch_array($query);
if ($row["close"] == 1)  {
 $page .= "<a name=\"bottom\"></a>[<a href=\"#top\">Go to Top</a>]<p><center><img src=\"img/padlock.gif\"><br><b>This thread has been Closed</b></center><p>";

    } else {

    $page .= "<a name=\"bottom\"></a>[<a href=\"#top\">Go to Top</a>]<p><table width=\"100%\"><tr><td><b>Reply To This Thread:</b><br /><form action=\"gforum.php?do=reply\" method=\"post\"><input type=\"hidden\" name=\"parent\" value=\"$id\" /><input type=\"hidden\" name=\"title\" value=\"Re: ".$row2["title"]."\" /><textarea name=\"content\" rows=\"7\" cols=\"40\"></textarea><br /><input type=\"submit\" name=\"submit\" value=\"Submit\" /> <input type=\"reset\" name=\"reset\" value=\"Reset\" /></form></td></tr></table>";

}

$page .= "You may return to the <a href=\"gforum.php\">guild forum</a> main page, or use the compass on the right to start exploring.<br />\n";

    display($page, "Guild Forum");

}

function reply() {

    global $userrow;

if ($userrow["authlevel"] == 4){ die( //Mute a player from chatting and game mailing
"Your account has been muted from the Player Chat, posting Comments, the Forum and from Game Mails.<p>This is most probably temporary due to you breaking the rules, or causing problems.<p>Please return to what you were previously doing.");
	}
	extract($_POST);
	$castlequery = doquery("SELECT * FROM {{table}} WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
	$grow = mysql_fetch_array($castlequery);
	$query = doquery("INSERT INTO {{table}} SET id='',postdate=NOW(),newpostdate=NOW(),author='".$userrow["charname"]."',parent='$parent',replies='0',guildname='".$grow["name"]."',title='$title',content='$content'", "gforum");
	$query2 = doquery("UPDATE {{table}} SET newpostdate=NOW(),replies=replies+1 WHERE id='$parent' LIMIT 1", "gforum");
        $query = doquery("UPDATE {{table}} SET postcount=postcount+1 WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	header("Location: gforum.php?do=thread:$parent:0");
	die();

}

function newthread() {

    global $userrow;
if ($userrow["authlevel"] == 4){ die( //Mute a player from chatting and game mailing
"Your account has been muted from the Player Chat, posting Comments, the Forum and from Game Mails.<p>This is most probably temporary due to you breaking the rules, or causing problems.<p>Please return to what you were previously doing.");
	}
    if (isset($_POST["submit"])) {

        extract($_POST);
	$castlequery = doquery("SELECT * FROM {{table}} WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
	$grow = mysql_fetch_array($castlequery);
        $query = doquery("INSERT INTO {{table}} SET id='',postdate=NOW(),newpostdate=NOW(),author='".$userrow["charname"]."',parent='0',replies='0',guildname='".$grow["name"]."',title='$title',content='$content'", "gforum");
        $query = doquery("UPDATE {{table}} SET postcount=postcount+1 WHERE id='".$userrow["id"]."' LIMIT 1", "users");
         header("Location: gforum.php");
        die();
    }
     $page = "<table width='100%' border='1'><tr><td class='title'>Guild Forum - Create Thread</td></tr></table><p>";
    $page .= "<table width=\"100%\"><tr><td><b>Create a New Thread:</b><br /><br/ ><form action=\"gforum.php?do=new\" method=\"post\">Title:<br /><input type=\"text\" name=\"title\" size=\"50\" maxlength=\"50\" /><br /><br />Message:<br /><textarea name=\"content\" rows=\"7\" cols=\"40\"></textarea><br /><br /><input type=\"submit\" name=\"submit\" value=\"Submit\" /> <input type=\"reset\" name=\"reset\" value=\"Reset\" /></form></td></tr></table>";
$page .= "You may return to the <a href=\"gforum.php\">guild forum</a> main page, or use the compass on the right to start exploring.<br />\n";

display($page, "Guild Forum");

}

function editpost($id) {
 global $userrow;

    if (isset($_POST["submit"])) {

        extract($_POST);
        $errors = 0;
        $errorlist = "";
        if ($content == "") { $errors++; $errorlist .= "Content is required, return to the <a href=\"gforum.php\">Support Forum</a>.<br />"; }
       if ($title == "") { $errors++; $errorlist .= "Title is required, return to the <a href=\"gforum.php\">Support Forum</a>. If you wish to delete your whole Post, simply add a small comment saying you have removed it.<br />"; }


        
        if ($errors == 0) { 
            $query = doquery("UPDATE {{table}} SET title='$title', content='$content' WHERE id='$id' LIMIT 1", "gforum");
            display("Your Post was successfully updated. Return to the <a href=\"gforum.php\">Guild Forum</a>.","Edit Post");
        } else {
            display("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Post");
        }        
        
    }   
$idquery = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "gforum");
	$idrow = mysql_fetch_array($idquery);
	if ($idrow["author"] != $userrow["charname"]) {
        $page .= "<table width='100%' border='1'><tr><td class='title'>Guild Forum - Edit Denied</td></tr></table><p>";
	$page .= "You cannot edit this Post! This Post doesn't belong to you. Return to the <a href='gforum.php'>Guild Forum</a>.<br>";
	display($page, "Edit Post");
	}          
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "gforum");
    $row = mysql_fetch_array($query);

$page = <<<END
<table width="100%"><tr><td class="title">Edit Post</td></tr></table>
<form action="gforum.php?do=editpost:$id" method="post">
<table width="90%">
<tr><td width="20%">Author:</td><td>{{author}} - <a href="gforum.php?do=delete:$id">Delete Permanently</a></td></tr>
<tr><td width="20%">Post Date:</td><td>{{postdate}}</td></tr><br>
<tr><td width="20%">Title:</td><td><input type="text" name="title" size="50" maxlength="50" value="{{title}}" /></td></tr>
<tr><td width="20%">Content:</td><td><textarea name="content" rows="7" cols="40">{{content}}</textarea></td></tr>
</table>
<input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" />
</form>
Return to the <a href="gforum.php">Guild Forum</a>
END;
    
    $page = parsetemplate($page, $row);
    display($page, "Edit Post");
    
}

function delete($id) {
	 global $userrow;
	$idquery = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "gforum");
	$idrow = mysql_fetch_array($idquery);
	if ($idrow["author"] != $userrow["charname"]) {
        $page .= "<table width='100%' border='1'><tr><td class='title'>gforum Forum - Edit Denied</td></tr></table><p>";
	$page .= "You cannot delete this Post! This Post doesn't belong to you. Return to the <a href='gforum.php'>Guild Forum</a>.<br>";
	display($page, "Delete Post");
	} 
	    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "gforum");
    $row = mysql_fetch_array($query);

	$query = doquery("DELETE FROM {{table}} WHERE id='$id' LIMIT 1", "gforum");
	$query = doquery("UPDATE {{table}} SET postcount=postcount-1 WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	header("Location: gforum.php");
	die();

}
?>