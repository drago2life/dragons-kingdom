<?php // mod.php :: primary mod script.

include('../lib.php');
include('../cookies.php');
$link = opendb();
$userrow = checkcookies();
if ($userrow == false) { die("Please log in to the <a href=\"../login.php?do=login\">game</a> before using the control panel."); }
if ($userrow["authlevel"] != 3 && $userrow["authlevel"] != 1) { die("You must have moderator privileges to use the control panel."); }
$controlquery = doquery("SELECT * FROM {{table}} WHERE id='1' LIMIT 1", "control");
$controlrow = mysql_fetch_array($controlquery);

if (isset($_GET["do"])) {
    $do = explode(":",$_GET["do"]);
    
    if ($do[0] == "main") { main(); }
    elseif ($do[0] == "general") { general(); }
    elseif ($do[0] == "editgeneral") { editgeneral($do[1]); }
    elseif ($do[0] == "support") { support(); }
    elseif ($do[0] == "editsupport") { editsupport($do[1]); }
    elseif ($do[0] == "suggestion") { suggestion(); }
    elseif ($do[0] == "editsuggestion") { editsuggestion($do[1]); }
        elseif ($do[0] == "market") { market(); }
    elseif ($do[0] == "editmarket") { editmarket($do[1]); }
   elseif ($do[0] == "chat") { chat(); }
    elseif ($do[0] == "editchat") { editchat($do[1]); }
    elseif ($do[0] == "users") { dolistmembers($do[1]); }
    elseif ($do[0] == "edituser") { edituser($do[1]); }
    elseif ($do[0] == "delete") { func_delete($do[1],$do[2]);}
    elseif ($do[0] == "onlineusers") { onlineusers($do[1]); }
        elseif ($do[0] == "mailadmin") { mailadmin(); }  
        elseif ($do[0] == "viewcomments") { viewcomments(); }
    elseif ($do[0] == "editcomments") { editcomments($do[1]); }
    
    elseif ($do[0] == "thread") { showthread($do[1], $do[2]); }
    elseif ($do[0] == "editpost") { editpost($do[1]); }
	elseif ($do[0] == "new") { newthread(); }
	elseif ($do[0] == "reply") { reply(); }
	elseif ($do[0] == "delete") { delete($do[1]); }
	elseif ($do[0] == "list") { donothing($do[1]); }
	elseif ($do[0] == "closechat") { closechat($do[1]); }

    
} else { donothing(); }

function donothing($start=0) {

      $query2 = doquery("SELECT * FROM {{table}} WHERE pin='1' ORDER BY newpostdate DESC LIMIT 20", "staff");
 $page = "<table width='100%' border='1'><tr><td class='title'>Staff Forum</td></tr></table><p>";
       
 $page .= "<hr /><table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"4\" style=\"background-color:#dddddd;\"><center>Only Administrators and Moderators can Pin Threads.</center></th></tr><tr><th width=\"44%\" style=\"background-color:#dddddd;\">Pinned Threads</th><th width=\"2%\" style=\"background-color:#dddddd;\">Replies</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Author</th><th  width=\"30%\" style=\"background-color:#dddddd;\">Last Post</th></tr>\n";
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
                $page .= "<tr><td style=\"background-color:#ffffff;\">".$namelink2."<a href=\"mod.php?do=thread:".$row["id"].":0\">".$row["title"]."</a></td><td style=\"background-color:#ffffff;\">".$row["replies"]."</td><td style=\"background-color:#ffffff;\">".$row["author"]."</td><td style=\"background-color:#ffffff;\">".$row["newpostdate"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">".$namelink2."<a href=\"mod.php?do=thread:".$row["id"].":0\">".$row["title"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["replies"]."</td><td style=\"background-color:#eeeeee;\">".$row["author"]."</td><td style=\"background-color:#eeeeee;\">".$row["newpostdate"]."</td></tr>\n";
			$count = 1;
		}
	  }
    }

    $page .= "</table></td></tr></table><hr />";

$query= doquery("SELECT * FROM {{table}} WHERE parent='0' AND pin!='1' ORDER BY newpostdate DESC LIMIT ".$start.",12", "staff");
$fullquery = doquery("SELECT * FROM {{table}} WHERE parent='0' AND pin!='1' ORDER BY newpostdate", "staff");
 $page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"4\" style=\"background-color:#dddddd;\"><center><a href=\"mod.php?do=new\">Create a New Thread</a></center></th></tr><tr><th width=\"44%\" style=\"background-color:#dddddd;\">Thread Title</th><th width=\"2%\" style=\"background-color:#dddddd;\">Replies</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Author</th><th  width=\"30%\" style=\"background-color:#dddddd;\">Last Post</th></tr>\n";
	$count = 1;
	
    if (mysql_num_rows($query) == 0) {
       $page .= "<tr><td style='background-color:#ffffff;' colspan='4'><b>No threads in Staff Forum.</b></td></tr>\n";
    } else {
      while ($row = mysql_fetch_array($query)) {
	  	if ($row["close"] != "1") {
	  		$namelink = "";
	  	} else {
	  		$namelink = "<img src='img/padlock.gif'>";
	  	}
		if ($count == 1) {
                $page .= "<tr><td style=\"background-color:#ffffff;\">".$namelink."<a href=\"mod.php?do=thread:".$row["id"].":0\">".$row["title"]."</a></td><td style=\"background-color:#ffffff;\">".$row["replies"]."</td><td style=\"background-color:#ffffff;\">".$row["author"]."</td><td style=\"background-color:#ffffff;\">".$row["newpostdate"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">".$namelink."<a href=\"mod.php?do=thread:".$row["id"].":0\">".$row["title"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["replies"]."</td><td style=\"background-color:#eeeeee;\">".$row["author"]."</td><td style=\"background-color:#eeeeee;\">".$row["newpostdate"]."</td></tr>\n";
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
		$page .= "<a href='mod.php?do=list:".$pagestart."'>".$pagelink."</a>   ";}
		else {
		$page .= "<i>".$pagelink."</i>   ";}
	}
	$page .= " ]</center></td></tr>";
    $page .= "</table></td></tr></table><hr />";

    moddisplay($page, "Staff Forum");
    
}

function showthread($id, $start) {

global $controlrow, $userrow; 


    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' OR parent='$id' ORDER BY id LIMIT $start,50", "staff");
    $query2 = doquery("SELECT title FROM {{table}} WHERE id='$id' LIMIT 1", "staff");
    $row2 = mysql_fetch_array($query2);
    
 $page = "<table width='100%' border='1'><tr><td class='title'>Staff Forum - View Thread</td></tr></table><p>[<a href=\"#bottom\">Go to Bottom</a>]<p>";
    $page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><td colspan=\"2\" style=\"background-color:#dddddd;\"><b><a href=\"mod.php\">Staff Forum</a> :: ".$row2["title"]."</b></td></tr>\n";
    $count = 1;
	
    while ($row = mysql_fetch_array($query)) {

		 $query3 = doquery("SELECT postcount,authlevel,customtitle,avatarlink FROM {{table}} WHERE charname='".$row["author"]."' LIMIT 1", "users");
	$row3 = mysql_fetch_array($query3); 
	
		 $authorquery = doquery("SELECT id FROM {{table}} WHERE charname='".$row["author"]."' ", "users");
	$authorrow = mysql_fetch_array($authorquery); 
				    $row = str_replace(":)", "<img src='smilies/smile.gif'>", $row); //16 Smilies
			    $row = str_replace(":(", "<img src='smilies/sad.gif'>", $row); 			     
		        $row = str_replace(":P", "<img src='smilies/tongue.gif'>", $row);
			    $row = str_replace(";)", "<img src='smilies/wink.gif'>", $row); 
			    $row = str_replace("(ha)", "<img src='smilies/biggrin.gif'>", $row);
			    $row = str_replace("^^", "<img src='smilies/rolleyes.gif'>", $row); 
			    $row = str_replace("o.O", "<img src='smilies/freak.gif'>", $row);
			    $row = str_replace(":$", "<img src='smilies/embaressed.gif'>", $row);
			    $row = str_replace("(c)", "<img src='smilies/cool.gif'>", $row); 
			    $row = str_replace(":@", "<img src='smilies/mad.gif'>", $row); 
			    $row = str_replace(":/", "<img src='smilies/umm.gif'>", $row); 	
			    $row = str_replace(":O", "<img src='smilies/shocked.gif'>", $row); 
			    $row = str_replace(":?", "<img src='smilies/ques-tion.gif'>", $row); 	
			    $row = str_replace(":!", "<img src='smilies/exclamation.gif'>", $row); 
			    $row = str_replace(":D", "<img src='smilies/lol.gif'>", $row); 
			    $row = str_replace(":%", "<img src='smilies/drool.gif'>", $row); 
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
        if ($count == 1) {
            $page .= "<tr><td width=\"25%\" style=\"background-color:#ffffff; vertical-align:top;\"><span class=\"small\"><b>".$row["author"]."</b><br />".$avatar."Posts: ".$row3["postcount"]."<br />".prettyforumdate($row["postdate"])."</td><td style=\"background-color:#ffffff; vertical-align:top;\">".nl2br($row["content"])."<br><br><hr /><style=\"background-color:#eeeeee; vertical-align:bottom;\">[<a href=\"index.php?do=onlinechar:".$authorrow["id"]."\">View Profile</a>] [<a href=\"mod.php?do=editpost:".$row["id"]."\">Edit Post</a>]</td></tr>\n";
            $count = 2;
            
        } else {
            $page .= "<tr><td width=\"25%\" style=\"background-color:#eeeeee; vertical-align:top;\"><span class=\"small\"><b>".$row["author"]."</b><br />".$avatar."Posts: ".$row3["postcount"]."<br />".prettyforumdate($row["postdate"])."</td><td style=\"background-color:#eeeeee; vertical-align:top;\">".nl2br($row["content"])."<br><br><hr /><style=\"background-color:#eeeeee; vertical-align:bottom;\">[<a href=\"index.php?do=onlinechar:".$authorrow["id"]."\">View Profile</a>] [<a href=\"mod.php?do=editpost:".$row["id"]."\">Edit Post</a>]</td></tr>\n";
            $count = 1;
        }
    }
    
    

    $page .= "</table></td></tr></table><br />";

$query = doquery("SELECT * FROM {{table}} WHERE id='$id' OR parent='$id' ORDER BY id LIMIT $start,50", "staff");
$row = mysql_fetch_array($query);
if ($row["close"] == 1)  {
 $page .= "<a name=\"bottom\"></a>[<a href=\"#top\">Go to Top</a>]<p><center><img src=\"img/padlock.gif\"><br><b>This thread has been Closed</b></center><p>";

    } else {

    $page .= "<a name=\"bottom\"></a>[<a href=\"#top\">Go to Top</a>]<p><table width=\"100%\"><tr><td><b>Reply To This Thread:</b><br /><form action=\"mod.php?do=reply\" method=\"post\"><input type=\"hidden\" name=\"parent\" value=\"$id\" /><input type=\"hidden\" name=\"title\" value=\"Re: ".$row2["title"]."\" /><textarea name=\"content\" rows=\"7\" cols=\"40\"></textarea><br /><input type=\"submit\" name=\"submit\" value=\"Submit\" /> <input type=\"reset\" name=\"reset\" value=\"Reset\" /></form></td></tr></table>";

}   
    
    moddisplay($page, "Staff Forum");
    
}

function closechat() {
    
    if (isset($_POST["submit"])) {
        extract($_POST);
        
        if ($errors == 0) { 
            $query = doquery("UPDATE {{table}} SET displaychat='$displaychat' WHERE id='1' LIMIT 1", "control");
            moddisplay("Chat updated.","Close Chat");
        } else {
            moddisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Main Settings");
        }
    }
    
    global $controlrow;
    
$page = <<<END
<b><u>Close Chat</u></b><br />
Only close Chat if it is ABSOLUTELY neccessary or a last resort.<br /><br />
<form action="mod.php?do=closechat" method="post">
<table width="90%">
<tr><td width="20%">Display Chat:</td><td><select name="displaychat"><option value="0" {{selectdisplaychat0}}>No</option><option value="1" {{selectdisplaychat1}}>Yes</option></select><br /><span class="small">Toggle display of the Player Chat.</td></tr>
</table>
<input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" />
</form>
END;

    if ($controlrow["displaychat"] == 0) { $controlrow["selectdisplaychat0"] = "selected=\"selected\" "; } else { $controlrow["selectdisplaychat0"] = ""; }
    if ($controlrow["displaychat"] == 1) { $controlrow["selectdisplaychat1"] = "selected=\"selected\" "; } else { $controlrow["selectdisplaychat1"] = ""; }
 

    $page = parsetemplate($page, $controlrow);
    moddisplay($page, "Close Chat");

}

function reply() {

    global $userrow;
	extract($_POST);

	$query = doquery("INSERT INTO {{table}} SET id='',postdate=NOW(),newpostdate=NOW(),author='".$userrow["charname"]."',parent='$parent',replies='0',title='$title',content='$content'", "staff");
	$query2 = doquery("UPDATE {{table}} SET newpostdate=NOW(),replies=replies+1 WHERE id='$parent' LIMIT 1", "staff");
        $query = doquery("UPDATE {{table}} SET postcount=postcount+1 WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	header("Location: mod.php?do=thread:$parent:0");
	die();
	
}

function newthread() {

    global $userrow;
    
    if (isset($_POST["submit"])) {

        extract($_POST);

        $query = doquery("INSERT INTO {{table}} SET id='',postdate=NOW(),newpostdate=NOW(),author='".$userrow["charname"]."',parent='0',replies='0',title='$title',content='$content'", "staff");
        $query = doquery("UPDATE {{table}} SET postcount=postcount+1 WHERE id='".$userrow["id"]."' LIMIT 1", "users");
         header("Location: mod.php");
        die();
    }
     $page = "<table width='100%' border='1'><tr><td class='title'>Staff Forum - Create Thread</td></tr></table><p>";
    $page .= "<table width=\"100%\"><tr><td><b>Create a New Thread:</b><br /><br/ ><form action=\"mod.php?do=new\" method=\"post\">Title:<br /><input type=\"text\" name=\"title\" size=\"50\" maxlength=\"50\" /><br /><br />Message:<br /><textarea name=\"content\" rows=\"7\" cols=\"40\"></textarea><br /><br /><input type=\"submit\" name=\"submit\" value=\"Submit\" /> <input type=\"reset\" name=\"reset\" value=\"Reset\" /></form></td></tr></table>";
  
  
moddisplay($page, "Staff Forum");
    
}

function editpost($id) {
 global $userrow;

    if (isset($_POST["submit"])) {

        extract($_POST);
        $errors = 0;
        $errorlist = "";
        if ($content == "") { $errors++; $errorlist .= "Content is required, return to the <a href=\"mod.php\">Support Forum</a>.<br />"; }
       if ($title == "") { $errors++; $errorlist .= "Title is required, return to the <a href=\"mod.php\">Support Forum</a>. If you wish to delete your whole Post, simply add a small comment saying you have removed it.<br />"; }


        
        if ($errors == 0) { 
            $query = doquery("UPDATE {{table}} SET title='$title', content='$content' WHERE id='$id' LIMIT 1", "staff");
            moddisplay("Your Post was successfully updated. Return to the <a href=\"mod.php\">Staff Forum</a>.","Edit Post");
        } else {
            moddisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Post");
        }        
        
    }   
$idquery = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "staff");
	$idrow = mysql_fetch_array($idquery);
	if ($idrow["author"] != $userrow["charname"]) {
        $page .= "<table width='100%' border='1'><tr><td class='title'>Staff Forum - Edit Denied</td></tr></table><p>";
	$page .= "You cannot edit this Post! This Post doesn't belong to you. Return to the <a href='index.php'>Game</a>.<br>";
	moddisplay($page, "Edit Post");
	}          
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "staff");
    $row = mysql_fetch_array($query);

$page = <<<END
<table width="100%"><tr><td class="title">Edit Post</td></tr></table>
<form action="mod.php?do=editpost:$id" method="post">
<table width="90%">
<tr><td width="20%">Author:</td><td>{{author}} - <a href="mod.php?do=delete:$id">Delete Permanently</a></td></tr>
<tr><td width="20%">Post Date:</td><td>{{postdate}}</td></tr>
<tr><td width="20%">Title:</td><td><input type="text" name="title" size="50" maxlength="50" value="{{title}}" /></td></tr>
<tr><td width="20%">Content:</td><td><textarea name="content" rows="7" cols="40">{{content}}</textarea></td></tr>
</table>
<input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" />
</form>
END;
    
    $page = parsetemplate($page, $row);
    moddisplay($page, "Edit Post");
    
}

function delete($id) {
	 global $userrow;
	$idquery = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "staff");
	$idrow = mysql_fetch_array($idquery);
	if ($idrow["author"] != $userrow["charname"]) {
        $page .= "<table width='100%' border='1'><tr><td class='title'>Staff Forum - Edit Denied</td></tr></table><p>";
	$page .= "You cannot delete this Post! This Post doesn't belong to you. Return to the <a href='index.php'>Game</a>.<br>";
	moddisplay($page, "Delete Post");
	} 
	    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "staff");
    $row = mysql_fetch_array($query);

	$query = doquery("DELETE FROM {{table}} WHERE id='$id' LIMIT 1", "staff");
	$query = doquery("UPDATE {{table}} SET postcount=postcount-1 WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	header("Location: mod.php");
	die();

}

function mailadmin() {

    global $userrow;

    if (isset($_POST["submit"])) {
        extract($_POST);
    $content = str_replace("'", "\'", $content);
    $content = trim($content);
	  $content = "<b><font color=red><u>Mail from a Moderator</u>:</b></font>\n\n". $content;
	  $subject = "". $subject;
	  $page = "<table width=\"100%\"><tr><td><b>Mail Admin:</b><br /><br/ >";
	  $c = 0;
        $mailallquery = doquery("SELECT charname FROM {{table}} WHERE authlevel='1'", "users");
	  while ($charrow = mysql_fetch_array($mailallquery)) {
		$recipient = $charrow["charname"];
		$c += 1;
           	$query = doquery("INSERT INTO {{table}} SET postdate=NOW(),author='".$userrow["charname"]."',recipient='$recipient',subject='$subject',content='$content'", "gamemail");

        }

    	  $page .= "Your message has been sent to all ".$c." Admin account(s) of the game.<p>";
    	      moddisplay($page, "Mail Admin");
    }

    $page = "<table width=\"100%\"><tr><td><b>Mail Admin:</b><br /><br/ >";
    $page .= "Enter the message below and it will be sent to all admin accounts of the game.<p>";
    $page .= "<form action=\"mod.php?do=mailadmin\" method=\"post\">";
    $page .= "Subject:<br />";
    $page .= "<input type=\"text\" name=\"subject\" value=\"Admin Mail\" size=\"35\" maxlength=\"35\" /><br><br>";
    $page .= "Message:<br />";
    $page .= "<textarea name=\"content\" rows=\"7\" cols=\"40\"></textarea><br><br>";
    $page .= "<input type=\"submit\" name=\"submit\" value=\"Send Mails\" /> ";
    $page .= "<input type=\"reset\" name=\"reset\" value=\"Reset\" />";
    $page .= "</form></td></tr></table>";
    moddisplay($page, "Mail Admin");

}

function viewcomments() {
    
    global $controlrow;
    
    $statquery = doquery("SELECT * FROM {{table}} ORDER BY id DESC LIMIT 1", "comments");
    $statrow = mysql_fetch_array($statquery);
    
    $query = doquery("SELECT id,topic,post FROM {{table}} ORDER BY id", "comments");
    $page = "<b><u>Edit News</u></b><br />";
    
    $page .= "Click a comment to edit it.<br /><br /><table width=\"50%\">\n";
    $count = 1;
    while ($row = mysql_fetch_array($query)) {
        if ($count == 1) { $page .= "<tr><td style=\"background-color: #eeeeee;\"><a href=\"mod.php?do=editcomments:".$row["id"]."\">".$row["post"]."</a></td></tr>\n"; $count = 2; }
        else { $page .= "<tr><td style=\"background-color: #ffffff;\"><a href=\"mod.php?do=editcomments:".$row["id"]."\">".$row["post"]."</a></td></tr>\n"; $count = 1; }
    }
    if (mysql_num_rows($query) == 0) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">No comments found.</td></tr>\n"; }
    $page .= "</table>";
    moddisplay($page, "Edit Comments");
    
}

function editcomments($id) {
 global $userrow;

    if (isset($_POST["submit"])) {

        extract($_POST);
        $errors = 0;
        $errorlist = "";
        if ($post == "") { $errors++; $errorlist .= "Content is required.<br />"; }

        
        if ($errors == 0) { 
            $query = doquery("UPDATE {{table}} SET post='$post' WHERE id='$id' LIMIT 1", "comments");
            moddisplay("Comment was successfully updated.","Edit Comment");
        } else {
            moddisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Comment");
        }        
        
    }   
      
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "comments");
    $row = mysql_fetch_array($query);

$page = <<<END
<form action="mod.php?do=editcomments:$id" method="post">
<table width="90%">
<tr><td width="20%"><a href="mod.php?do=delete:$id:comments">Delete Permanently</a></td></tr>
<tr><td width="20%">Comment:</td><td><textarea name="post" rows="5" cols="30">{{post}}</textarea></td></tr>
</table>
<input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" />
</form>
END;
    
    $page = parsetemplate($page, $row);
    moddisplay($page, "Edit Comment");
    
}

function dolistmembers ($filter) {
	global $userrow;

	if (!isset($filter)) { $filter = "A";}

	$page .= "<b><u>Edit Users</u></b><br />Click on the character name to edit their information. Mods can only ban and mute players.<p><center>";

	$page .= "[ <a href='mod.php?do=users:A'>A</a> ";
	$page .= " <a href='mod.php?do=users:B'>B</a> ";
	$page .= " <a href='mod.php?do=users:C'>C</a> ";
	$page .= " <a href='mod.php?do=users:D'>D</a> ";
	$page .= " <a href='mod.php?do=users:E'>E</a> ";
	$page .= " <a href='mod.php?do=users:F'>F</a> ";
	$page .= " <a href='mod.php?do=users:G'>G</a> ";
	$page .= " <a href='mod.php?do=users:H'>H</a> ";
	$page .= " <a href='mod.php?do=users:I'>I</a> ";
	$page .= " <a href='mod.php?do=users:J'>J</a> ";
	$page .= " <a href='mod.php?do=users:K'>K</a> ";
	$page .= " <a href='mod.php?do=users:L'>L</a> ";
	$page .= " <a href='mod.php?do=users:M'>M</a> ";
	$page .= " <a href='mod.php?do=users:N'>N</a> ";
	$page .= " <a href='mod.php?do=users:O'>O</a> ";
	$page .= " <a href='mod.php?do=users:P'>P</a> ";
	$page .= " <a href='mod.php?do=users:Q'>Q</a> ";
	$page .= " <a href='mod.php?do=users:R'>R</a> ";
	$page .= " <a href='mod.php?do=users:S'>S</a> ";
	$page .= " <a href='mod.php?do=users:T'>T</a> ";
	$page .= " <a href='mod.php?do=users:U'>U</a> ";
	$page .= " <a href='mod.php?do=users:V'>V</a> ";
	$page .= " <a href='mod.php?do=users:W'>W</a> ";
	$page .= " <a href='mod.php?do=users:X'>X</a> ";
	$page .= " <a href='mod.php?do=users:Y'>Y</a> ";
	$page .= " <a href='mod.php?do=users:Z'>Z</a> ]<br><br></center>";
	$charquery = doquery("SELECT * FROM {{table}} WHERE charname LIKE '".$filter."%' ORDER by charname", "users");

	$page .= "<center><table width='90%' style='border: solid 1px black' cellspacing='0' cellpadding='0'>";
	$page .= "<center><tr><td colspan=\"5\" bgcolor=\"#ffffff\"><center><b>Dragons Kingdom Players</b></center></td></tr>";
	$page .= "<tr><td><b>Character Name</b></td><td><b>User Name</b></td></tr>";
	$count = 2;
	while ($charrow = mysql_fetch_array($charquery)) {
		
		if ($count == 1) { $color = "bgcolor='#ffffff'"; $count = 2; }
		else { $color = "bgcolor='#eeeeee'"; $count = 1;}
		$page .= "<tr><td ".$color." width='15%'>";
		if ($userrow["guildrank"] >= 100) {
		$page .= "<a href='mod.php?do=edituser:".$charrow["id"]."'>".$charrow["charname"]."</a>";}
		else {
		$page .= "<a href='mod.php?do=edituser:".$charrow["id"]."'>".$charrow["charname"]."</a>";}
		$page .= "</td>";
		$page .= "<td ".$color." width='25%'>".$charrow["username"]."</td>";
	  	$page .= "</tr>";
	}
	$page .= "</table></center>";

	moddisplay($page, "List Users");

}

function onlineusers()

{
global $userrow;
    $page = "<br>Click on a Players ID to Ban or Mute them. Players who are highlighted Red, are Administrators (usually invisible) and Players who are highlighted Green, are Moderators. Players who are Blue, are muted from Chat.<p></center>";

  $onlinequery = doquery("SELECT * FROM {{table}} WHERE UNIX_TIMESTAMP(onlinetime) >= '".(time()-300)."' ORDER BY charname", "users");
         $page .= "<table width=\"75%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"5\" style=\"background-color:#dddddd;\"><center>There have been " . mysql_num_rows($onlinequery) . " user(s) Online within the last few minutes, in order<br>of character name: </center></th></tr><tr><th width=\"5%\" style=\"background-color:#dddddd;\">Player ID</th><th width=\"30%\" style=\"background-color:#dddddd;\">Character Name</th><th width=\"5%\" style=\"background-color:#dddddd;\">Level</th><th width=\"30%\" style=\"background-color:#dddddd;\">Currently</th></tr>\n";

    $count = 1;


     while ($onlinerow = mysql_fetch_array($onlinequery)) {
     	    if ($onlinerow["location"] == "{{currenttown2}}") {
            $town2query = doquery("SELECT name,longitude,latitude FROM {{table}} WHERE latitude='".$onlinerow["latitude"]."' AND longitude='".$onlinerow["longitude"]."' LIMIT 1", "towns");
            $town2row = mysql_fetch_array($town2query);
            $onlinerow["location"] = "".$town2row["name"]."";
            }
        if ($onlinerow["authlevel"] == 1){
	   $page .= "\n";

	}elseif($onlinerow["authlevel"] == 3) {
           $page .= "<tr style=\"background-color:green;\"><td><center><a href=\"mod.php?do=edituser:".$onlinerow["id"]."\">".$onlinerow["id"]."</a></center></td><td>".$onlinerow["charname"]."</td><td><center>".$onlinerow["level"]."</center></td><td>".$onlinerow["location"]."</td></tr>\n";
        
		}elseif($onlinerow["authlevel"] == 4) {
           $page .= "<tr style=\"background-color:blue;\"><td><center><a href=\"mod.php?do=edituser:".$onlinerow["id"]."\">".$onlinerow["id"]."</a></center></td><td>".$onlinerow["charname"]."</td><td><center>".$onlinerow["level"]."</center></td><td>".$onlinerow["location"]."</td></tr>\n";
       
	
	
	}else{
	   if ($count == 1) {
             $page .= "<tr><td style=\"background-color:#ffffff;\"><center><a href=\"mod.php?do=edituser:".$onlinerow["id"]."\">".$onlinerow["id"]."</a></center></td><td style=\"background-color:#ffffff;\">".$onlinerow["charname"]."</td><td style=\"background-color:#ffffff;\"><center>".$onlinerow["level"]."</center></td><td style=\"background-color:#ffffff;\">".$onlinerow["location"]."</td></tr>\n";
             $count = 2;
           } else {
             $page .= "<tr><td style=\"background-color:#eeeeee;\"><center><a href=\"mod.php?do=edituser:".$onlinerow["id"]."\">".$onlinerow["id"]."</a></center></td><td style=\"background-color:#eeeeee;\">".$onlinerow["charname"]."</td><td style=\"background-color:#eeeeee;\"><center>".$onlinerow["level"]."</center></td><td style=\"background-color:#eeeeee;\">".$onlinerow["location"]."</td></tr>\n";
             $count = 1;
           }
        }
     }

    $page .= "</table></td></tr></table>";

    moddisplay($page, "Online Users");
}

function edituser($id) {
    
    if (isset($_POST["submit"])) {
        
        extract($_POST);
        $errors = 0;
        $errorlist = "";
        if ($authlevel == "") { $errors++; $errorlist .= "Auth Level is required.<br />"; }
        if ($id == "1") { $errors++; $errorlist .= "You cannot edit an Administrators account.<br />"; }     
        
        if ($errors == 0) { 
$updatequery = <<<END
UPDATE {{table}} SET authlevel="$authlevel" WHERE id="$id" LIMIT 1
END;
			$query = doquery($updatequery, "users");
            moddisplay("User updated.","Edit Users");
        } else {
            moddisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Users");
        }        
        
    }   
        
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "users");
    $row = mysql_fetch_array($query);
    global $controlrow;


$page = <<<END
<b><u>Edit Users</u></b><br /><br />
<form action="mod.php?do=edituser:$id" method="post">
<table width="90%">
<tr><td width="20%">ID:</td><td>{{id}}</td></tr>
<tr><td width="20%">Username:</td><td>{{username}}</td></tr>
<tr><td width="20%">Character Name:</td><td>{{charname}}</td></tr>
<tr><td width="20%">Email:</td><td>{{email}}</td></tr>
<tr><td width="20%">Auth Level:</td><td><select name="authlevel"><option value="0" {{auth0select}}>Normal User</option><option value="2" {{auth2select}}>Ban User</option><option value="4" {{auth4select}}>Mute from Chat</option></select><br /></td></tr>
Set to "Ban" to temporarily (or permanently) ban a user. Feel free to do this if its needed, please message me as to why and who you banned afterwards. Same applies to muting players. Please remember to unmute them later to a normal user.
<tr><td width="20%">IP Address:</td><td>{{ipaddress}} - You may wish to compare IP address's to see if someone is using multiple accounts.</td></tr>
</table>
<input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" />
</form>
END;

    if ($row["authlevel"] == 0) { $row["auth0select"] = "selected=\"selected\" "; } else { $row["auth0select"] = ""; }
    if ($row["authlevel"] == 2) { $row["auth2select"] = "selected=\"selected\" "; } else { $row["auth2select"] = ""; }
    if ($row["authlevel"] == 4) { $row["auth4select"] = "selected=\"selected\" "; } else { $row["auth4select"] = ""; }

    
    $page = parsetemplate($page, $row);
    moddisplay($page, "Edit Users");
    
}

function general() {
    
    global $controlrow;
    
    $statquery = doquery("SELECT * FROM {{table}} ORDER BY id DESC LIMIT 1", "general");
    $statrow = mysql_fetch_array($statquery);
    
    $query = doquery("SELECT id,title FROM {{table}} ORDER BY id", "general");
    $page = "<b><u>Edit General Forum</u></b><br />";
    
    $page .= "Click a posts name to edit it.<br /><br /><table width=\"50%\">\n";
    $count = 1;
    while ($row = mysql_fetch_array($query)) {
        if ($count == 1) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">".$row["id"]."</td><td style=\"background-color: #eeeeee;\"><a href=\"mod.php?do=editgeneral:".$row["id"]."\">".$row["title"]."</a></td></tr>\n"; $count = 2; }
        else { $page .= "<tr><td width=\"8%\" style=\"background-color: #ffffff;\">".$row["id"]."</td><td style=\"background-color: #ffffff;\"><a href=\"mod.php?do=editgeneral:".$row["id"]."\">".$row["title"]."</a></td></tr>\n"; $count = 1; }
    }
    if (mysql_num_rows($query) == 0) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">No posts found.</td></tr>\n"; }
    $page .= "</table>";
    moddisplay($page, "Edit General Forum");
    
}

function editgeneral($id) {
    
    if (isset($_POST["submit"])) {
        
        extract($_POST);
        $errors = 0;
        $errorlist = "";
        if ($title == "") { $errors++; $errorlist .= "Title is required.<br />"; }
        if ($author == "") { $errors++; $errorlist .= "Author is required.<br />"; }
        if ($content == "") { $errors++; $errorlist .= "Content is required.<br />"; }
        if ($postdate == "") { $errors++; $errorlist .= "Post Date is required.<br />"; }
        if (!is_numeric($parent)) { $errors++; $errorlist .= "Parent must be a number.<br />"; }
        if (!is_numeric($replies)) { $errors++; $errorlist .= "Replies must be a number.<br />"; }

        
        if ($errors == 0) { 
            $query = doquery("UPDATE {{table}} SET postdate='$postdate',newpostdate='$newpostdate',author='$author',parent='$parent',replies='$replies',title='$title',content='$content',close='$close',pin='$pin' WHERE id='$id' LIMIT 1", "general");
            moddisplay("Post updated.","Edit General Forum");
        } else {
            moddisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit General Forum");
        }        
        
    }   
        
    
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "general");
    $row = mysql_fetch_array($query);

$page = <<<END
<b><u>Edit General Forum</u></b><br /><br />
<form action="mod.php?do=editgeneral:$id" method="post">
<table width="90%">
<tr><td width="20%">Post ID:</td><td>{{id}} - <a href="mod.php?do=delete:$id:general">Delete</a></td></tr>
<tr><td width="20%">Post Date:</td><td><input type="text" name="postdate" size="20" maxlength="20" value="{{postdate}}" /><br>Please dont edit this unless its neccessary</td></tr>
<tr><td width="20%">New Post Date:</td><td><input type="text" name="newpostdate" size="20" maxlength="20" value="{{newpostdate}}" /><br>Please dont edit this unless its neccessary</td></tr>
<tr><td width="20%">Author:</td><td><input type="text" name="author" size="30" maxlength="30" value="{{author}}" /><br>Please dont edit this unless its neccessary</td></tr>
<tr><td width="20%">Parent:</td><td><input type="text" name="parent" size="11" maxlength="11" value="{{parent}}" /><br>Ignore this</td></tr>
<tr><td width="20%">Replies:</td><td><input type="text" name="replies" size="2" maxlength="2" value="{{replies}}" /><br>Please dont edit this unless its neccessary</td></tr>
<tr><td width="20%">Close Thread:</td><td><input type="text" name="close" size="2" maxlength="2" value="{{close}}" /><br>1 = Close / 0 = Open. Please ensure you close the <u>thread starter post</u>. ie: The first post which started this thread.</td></tr>
<tr><td width="20%">Pin Thread:</td><td><input type="text" name="pin" size="2" maxlength="2" value="{{pin}}" /><br>1 = Pinned / 0 = Unpinned. Please ensure you Pin the <u>thread starter post</u>. ie: The first post which started this thread.</td></tr>
<tr><td width="20%">Title:</td><td><input type="text" name="title" size="50" maxlength="50" value="{{title}}" /></td></tr>
<tr><td width="20%">Content:</td><td><textarea name="content" rows="7" cols="40">{{content}}</textarea><br>If editing a post, you may want to put that you have edited it so the author knows</td></tr>

</table>
<input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" />
</form>
END;
    
    $page = parsetemplate($page, $row);
    moddisplay($page, "Edit General Forum");
    
}

function func_delete($id,$table) { 

	$query = doquery("DELETE FROM {{table}} WHERE id='$id'", $table);
	if($query===true){
		moddisplay("Post deleted successfully.","Delete Post or Thread");
	}else{
       moddisplay('The delete was <b>NOT</b> successful<br><br>Please go back and try again.',"Edit General Forum"); 
	} 
	die();

}



function support() {
    
    global $controlrow;
    
    $statquery = doquery("SELECT * FROM {{table}} ORDER BY id DESC LIMIT 1", "support");
    $statrow = mysql_fetch_array($statquery);
    
    $query = doquery("SELECT id,title FROM {{table}} ORDER BY id", "support");
    $page = "<b><u>Edit Support Forum</u></b><br />";
    
    $page .= "Click a posts name to edit it.<br /><br /><table width=\"50%\">\n";
    $count = 1;
    while ($row = mysql_fetch_array($query)) {
        if ($count == 1) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">".$row["id"]."</td><td style=\"background-color: #eeeeee;\"><a href=\"mod.php?do=editsupport:".$row["id"]."\">".$row["title"]."</a></td></tr>\n"; $count = 2; }
        else { $page .= "<tr><td width=\"8%\" style=\"background-color: #ffffff;\">".$row["id"]."</td><td style=\"background-color: #ffffff;\"><a href=\"mod.php?do=editsupport:".$row["id"]."\">".$row["title"]."</a></td></tr>\n"; $count = 1; }
    }
    if (mysql_num_rows($query) == 0) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">No posts found.</td></tr>\n"; }
    $page .= "</table>";
    moddisplay($page, "Edit Support Forum");
    
}

function editsupport($id) {
    
    if (isset($_POST["submit"])) {
        
        extract($_POST);
        $errors = 0;
        $errorlist = "";
        if ($title == "") { $errors++; $errorlist .= "Title is required.<br />"; }
        if ($author == "") { $errors++; $errorlist .= "Author is required.<br />"; }
        if ($content == "") { $errors++; $errorlist .= "Content is required.<br />"; }
        if ($postdate == "") { $errors++; $errorlist .= "Post Date is required.<br />"; }
        if (!is_numeric($parent)) { $errors++; $errorlist .= "Parent must be a number.<br />"; }
        if (!is_numeric($replies)) { $errors++; $errorlist .= "Replies must be a number.<br />"; }

        
        if ($errors == 0) { 
            $query = doquery("UPDATE {{table}} SET postdate='$postdate',newpostdate='$newpostdate',author='$author',parent='$parent',replies='$replies',title='$title',content='$content',close='$close',pin='$pin' WHERE id='$id' LIMIT 1", "support");
            moddisplay("Post updated.","Edit Support Forum");
        } else {
            moddisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Support Forum");
        }        
        
    }   
        
    
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "support");
    $row = mysql_fetch_array($query);


$page = <<<END
<b><u>Edit Support Forum</u></b><br /><br />
<form action="mod.php?do=editsupport:$id" method="post">
<table width="90%">
<tr><td width="20%">Post ID:</td><td>{{id}} - <a href="mod.php?do=delete:$id:support">Delete</a></td></tr>
<tr><td width="20%">Post Date:</td><td><input type="text" name="postdate" size="20" maxlength="20" value="{{postdate}}" /><br>Please dont edit this unless its neccessary</td></tr>
<tr><td width="20%">New Post Date:</td><td><input type="text" name="newpostdate" size="20" maxlength="20" value="{{newpostdate}}" /><br>Please dont edit this unless its neccessary</td></tr>
<tr><td width="20%">Author:</td><td><input type="text" name="author" size="30" maxlength="30" value="{{author}}" /><br>Please dont edit this unless its neccessary</td></tr>
<tr><td width="20%">Parent:</td><td><input type="text" name="parent" size="11" maxlength="11" value="{{parent}}" /><br>Ignore this</td></tr>
<tr><td width="20%">Replies:</td><td><input type="text" name="replies" size="2" maxlength="2" value="{{replies}}" /><br>Please dont edit this unless its neccessary</td></tr>
<tr><td width="20%">Close Thread:</td><td><input type="text" name="close" size="2" maxlength="2" value="{{close}}" /><br>1 = Close / 0 = Open. Please ensure you close the <u>thread starter post</u>. ie: The first post which started this thread.</td></tr>
<tr><td width="20%">Pin Thread:</td><td><input type="text" name="pin" size="2" maxlength="2" value="{{pin}}" /><br>1 = Pinned / 0 = Unpinned. Please ensure you Pin the <u>thread starter post</u>. ie: The first post which started this thread.</td></tr>
<tr><td width="20%">Title:</td><td><input type="text" name="title" size="50" maxlength="50" value="{{title}}" /></td></tr>
<tr><td width="20%">Content:</td><td><textarea name="content" rows="7" cols="40">{{content}}</textarea><br>If editing a post, you may want to put that you have edited it so the author knows</td></tr>

</table>
<input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" />
</form>
END;

    
    $page = parsetemplate($page, $row);
    moddisplay($page, "Edit Support Forum");
    
}

function suggestion() {
    
    global $controlrow;
    
    $statquery = doquery("SELECT * FROM {{table}} ORDER BY id DESC LIMIT 1", "suggestions");
    $statrow = mysql_fetch_array($statquery);
    
    $query = doquery("SELECT id,title FROM {{table}} ORDER BY id", "suggestions");
    $page = "<b><u>Edit Suggestion Forum</u></b><br />";
    
    $page .= "Click a posts name to edit it.<br /><br /><table width=\"50%\">\n";
    $count = 1;
    while ($row = mysql_fetch_array($query)) {
        if ($count == 1) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">".$row["id"]."</td><td style=\"background-color: #eeeeee;\"><a href=\"mod.php?do=editsuggestion:".$row["id"]."\">".$row["title"]."</a></td></tr>\n"; $count = 2; }
        else { $page .= "<tr><td width=\"8%\" style=\"background-color: #ffffff;\">".$row["id"]."</td><td style=\"background-color: #ffffff;\"><a href=\"mod.php?do=editsuggestion:".$row["id"]."\">".$row["title"]."</a></td></tr>\n"; $count = 1; }
    }
    if (mysql_num_rows($query) == 0) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">No posts found.</td></tr>\n"; }
    $page .= "</table>";
    moddisplay($page, "Edit Suggestion Forum");
    
}

function editsuggestion($id) {
    
    if (isset($_POST["submit"])) {
        
        extract($_POST);
        $errors = 0;
        $errorlist = "";
        if ($title == "") { $errors++; $errorlist .= "Title is required.<br />"; }
        if ($author == "") { $errors++; $errorlist .= "Author is required.<br />"; }
        if ($content == "") { $errors++; $errorlist .= "Content is required.<br />"; }
        if ($postdate == "") { $errors++; $errorlist .= "Post Date is required.<br />"; }
        if (!is_numeric($parent)) { $errors++; $errorlist .= "Parent must be a number.<br />"; }
        if (!is_numeric($replies)) { $errors++; $errorlist .= "Replies must be a number.<br />"; }

        
        if ($errors == 0) { 
            $query = doquery("UPDATE {{table}} SET postdate='$postdate',newpostdate='$newpostdate',author='$author',parent='$parent',replies='$replies',title='$title',content='$content',close='$close',pin='$pin' WHERE id='$id' LIMIT 1", "suggestions");
            moddisplay("Post updated.","Edit Suggestion Forum");
        } else {
            moddisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Suggestion Forum");
        }        
        
    }   
        
    
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "suggestions");
    $row = mysql_fetch_array($query);

$page = <<<END
<b><u>Edit Suggestion Forum</u></b><br /><br />
<form action="mod.php?do=editsuggestion:$id" method="post">
<table width="90%">
<tr><td width="20%">Post ID:</td><td>{{id}} - <a href="mod.php?do=delete:$id:suggestions">Delete</a></td></tr>
<tr><td width="20%">Post Date:</td><td><input type="text" name="postdate" size="20" maxlength="20" value="{{postdate}}" /><br>Please dont edit this unless its neccessary</td></tr>
<tr><td width="20%">New Post Date:</td><td><input type="text" name="newpostdate" size="20" maxlength="20" value="{{newpostdate}}" /><br>Please dont edit this unless its neccessary</td></tr>
<tr><td width="20%">Author:</td><td><input type="text" name="author" size="30" maxlength="30" value="{{author}}" /><br>Please dont edit this unless its neccessary</td></tr>
<tr><td width="20%">Parent:</td><td><input type="text" name="parent" size="11" maxlength="11" value="{{parent}}" /><br>Ignore this</td></tr>
<tr><td width="20%">Replies:</td><td><input type="text" name="replies" size="2" maxlength="2" value="{{replies}}" /><br>Please dont edit this unless its neccessary</td></tr>
<tr><td width="20%">Close Thread:</td><td><input type="text" name="close" size="2" maxlength="2" value="{{close}}" /><br>1 = Close / 0 = Open. Please ensure you close the <u>thread starter post</u>. ie: The first post which started this thread.</td></tr>
<tr><td width="20%">Pin Thread:</td><td><input type="text" name="pin" size="2" maxlength="2" value="{{pin}}" /><br>1 = Pinned / 0 = Unpinned. Please ensure you Pin the <u>thread starter post</u>. ie: The first post which started this thread.</td></tr>
<tr><td width="20%">Title:</td><td><input type="text" name="title" size="50" maxlength="50" value="{{title}}" /></td></tr>
<tr><td width="20%">Content:</td><td><textarea name="content" rows="7" cols="40">{{content}}</textarea><br>If editing a post, you may want to put that you have edited it so the author knows</td></tr>

</table>
<input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" />
</form>
END;
   
    $page = parsetemplate($page, $row);
    moddisplay($page, "Edit Suggestion Forum");
    
}
    
function market() {
    
    global $controlrow;
    
    $statquery = doquery("SELECT * FROM {{table}} ORDER BY id DESC LIMIT 1", "marketforum");
    $statrow = mysql_fetch_array($statquery);
    
    $query = doquery("SELECT id,title FROM {{table}} ORDER BY id", "marketforum");
    $page = "<b><u>Edit Market Forum</u></b><br />";
    
    $page .= "Click a posts name to edit it.<br /><br /><table width=\"50%\">\n";
    $count = 1;
    while ($row = mysql_fetch_array($query)) {
        if ($count == 1) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">".$row["id"]."</td><td style=\"background-color: #eeeeee;\"><a href=\"mod.php?do=editmarket:".$row["id"]."\">".$row["title"]."</a></td></tr></tr>\n"; $count = 2; }
        else { $page .= "<tr><td width=\"8%\" style=\"background-color: #ffffff;\">".$row["id"]."</td><td style=\"background-color: #ffffff;\"><a href=\"mod.php?do=editmarket:".$row["id"]."\">".$row["title"]."</a></td></tr>\n"; $count = 1; }
    }
    if (mysql_num_rows($query) == 0) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">No posts found.</td></tr>\n"; }
    $page .= "</table>";
    moddisplay($page, "Edit Market Forum");
    
}

function editmarket($id) {
    
    if (isset($_POST["submit"])) {
        
        extract($_POST);
        $errors = 0;
        $errorlist = "";
        if ($title == "") { $errors++; $errorlist .= "Title is required.<br />"; }
        if ($author == "") { $errors++; $errorlist .= "Author is required.<br />"; }
        if ($content == "") { $errors++; $errorlist .= "Content is required.<br />"; }
        if ($postdate == "") { $errors++; $errorlist .= "Post Date is required.<br />"; }
        if (!is_numeric($parent)) { $errors++; $errorlist .= "Parent must be a number.<br />"; }
        if (!is_numeric($replies)) { $errors++; $errorlist .= "Replies must be a number.<br />"; }

        
        if ($errors == 0) { 
            $query = doquery("UPDATE {{table}} SET postdate='$postdate',newpostdate='$newpostdate',author='$author',parent='$parent',replies='$replies',title='$title',content='$content',close='$close',pin='$pin' WHERE id='$id' LIMIT 1", "marketforum");
            moddisplay("Post updated.","Edit Market Forum");
        } else {
            moddisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Market Forum");
        }        
        
    }   
        
    
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "marketforum");
    $row = mysql_fetch_array($query);

$page = <<<END
<b><u>Edit Market Forum</u></b><br /><br />
<form action="mod.php?do=editmarket:$id" method="post">
<table width="90%">
<tr><td width="20%">Post ID:</td><td>{{id}} - <a href="mod.php?do=delete:$id:marketforum">Delete</a></td></tr>
<tr><td width="20%">Post Date:</td><td><input type="text" name="postdate" size="20" maxlength="20" value="{{postdate}}" /><br>Please dont edit this unless its neccessary</td></tr>
<tr><td width="20%">New Post Date:</td><td><input type="text" name="newpostdate" size="20" maxlength="20" value="{{newpostdate}}" /><br>Please dont edit this unless its neccessary</td></tr>
<tr><td width="20%">Author:</td><td><input type="text" name="author" size="30" maxlength="30" value="{{author}}" /><br>Please dont edit this unless its neccessary</td></tr>
<tr><td width="20%">Parent:</td><td><input type="text" name="parent" size="11" maxlength="11" value="{{parent}}" /><br>Ignore this</td></tr>
<tr><td width="20%">Replies:</td><td><input type="text" name="replies" size="2" maxlength="2" value="{{replies}}" /><br>Please dont edit this unless its neccessary</td></tr>
<tr><td width="20%">Close Thread:</td><td><input type="text" name="close" size="2" maxlength="2" value="{{close}}" /><br>1 = Close / 0 = Open. Please ensure you close the <u>thread starter post</u>. ie: The first post which started this thread.</td></tr>
<tr><td width="20%">Pin Thread:</td><td><input type="text" name="pin" size="2" maxlength="2" value="{{pin}}" /><br>1 = Pinned / 0 = Unpinned. Please ensure you Pin the <u>thread starter post</u>. ie: The first post which started this thread.</td></tr>
<tr><td width="20%">Title:</td><td><input type="text" name="title" size="50" maxlength="50" value="{{title}}" /></td></tr>
<tr><td width="20%">Content:</td><td><textarea name="content" rows="7" cols="40">{{content}}</textarea><br>If editing a post, you may want to put that you have edited it so the author knows</td></tr>

</table>
<input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" />
</form>
END;
   
    $page = parsetemplate($page, $row);
    moddisplay($page, "Edit Market Forum");
    
}

function chat() {
    
    global $controlrow;
    
    $statquery = doquery("SELECT * FROM {{table}} ORDER BY id desc LIMIT 1", "chat");
    $statrow = mysql_fetch_array($statquery);
    
    $query = doquery("SELECT id,author,babble FROM {{table}} ORDER BY id", "chat");
    $page = "<b><u>Edit Chat</u></b><br />";
    
    $page .= "Click the Chat text to edit it.<br /><br /><table width=\"50%\">\n";
    $count = 1;
    while ($row = mysql_fetch_array($query)) {
        if ($count == 1) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">".$row["id"]."</td><td style=\"background-color: #eeeeee;\"><a href=\"mod.php?do=editchat:".$row["id"]."\">".$row["author"]."</a></td><td style=\"background-color: #eeeeee;\"><a href=\"mod.php?do=editchat:".$row["id"]."\">".$row["babble"]."</a></td></tr>\n"; $count = 2; }
        else { $page .= "<tr><td width=\"8%\" style=\"background-color: #ffffff;\">".$row["id"]."</td><td style=\"background-color: #ffffff;\"><a href=\"mod.php?do=editchat:".$row["id"]."\">".$row["author"]."</a></td><td style=\"background-color: #ffffff;\"><a href=\"mod.php?do=editchat:".$row["id"]."\">".$row["babble"]."</a></td></tr>\n"; $count = 1; }
    }
    if (mysql_num_rows($query) == 0) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">No chat messages found.</td></tr>\n"; }
    $page .= "</table>";
    moddisplay($page, "Edit Chat");
    
}

function editchat($id) {
    
    if (isset($_POST["submit"])) {
        
        extract($_POST);
        $errors = 0;
        $errorlist = "";
        if ($author == "") { $errors++; $errorlist .= "Character name is required.<br />"; }
        if ($babble == "") { $errors++; $errorlist .= "Text is required. (Or some kind of explanation as to why it was removed)<br />"; }
        if ($posttime == "") { $errors++; $errorlist .= "Message Date is required.<br />"; }


        
        if ($errors == 0) { 
            $query = doquery("UPDATE {{table}} SET posttime='$posttime',author='$author',babble='$babble' WHERE id='$id' LIMIT 1", "chat");
            moddisplay("Message updated.","Edit Chat");
        } else {
            moddisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Chat");
        }        
        
    }   
        
    
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "chat");
    $row = mysql_fetch_array($query);

$page = <<<END
<b><u>Edit Chat</u></b><br /><br />
<form action="mod.php?do=editchat:$id" method="post">
<table width="90%">
<tr><td width="20%">Message ID:</td><td>{{id}} - <a href="mod.php?do=delete:$id:chat">Delete</a></td></tr>
<tr><td width="20%">Message Time:</td><td><input type="text" name="posttime" size="20" maxlength="20" value="{{posttime}}" /><br>Please dont edit this unless its neccessary</td></tr>
<tr><td width="20%">Character Name:</td><td><input type="text" name="author" size="30" maxlength="30" value="{{author}}" /><br>Please dont edit this unless its neccessary</td></tr>
<tr><td width="20%">Text:</td><td><textarea name="babble" rows="7" cols="40">{{babble}}</textarea><br></td></tr>

</table>
<input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" />
</form>
END;
    
    $page = parsetemplate($page, $row);
    moddisplay($page, "Edit Chat");
    
}
?>