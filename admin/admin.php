<?php // admin.php :: primary administration script.

include('../lib.php');
include('../cookies.php');
$link = opendb();
$userrow = checkcookies();
if ($userrow == false) { die("Please log in to the <a href=\"../login.php?do=login\">game</a> before using the control panel."); }
if ($userrow["authlevel"] != 1) { die("You must have administrator privileges to use the control panel."); }
$controlquery = doquery("SELECT * FROM {{table}} WHERE id='1' LIMIT 1", "control");
$controlrow = mysql_fetch_array($controlquery);

if (isset($_GET["do"])) {
    $do = explode(":",$_GET["do"]);
    
    if ($do[0] == "main") { main(); }
    elseif ($do[0] == "items") { items(); }
    elseif ($do[0] == "edititem") { edititem($do[1]); }
    elseif ($do[0] == "drops") { drops(); }
    elseif ($do[0] == "editdrop") { editdrop($do[1]); }
    elseif ($do[0] == "towns") { towns(); }
    elseif ($do[0] == "edittown") { edittown($do[1]); }
    elseif ($do[0] == "monsters") { monsters(); }
    elseif ($do[0] == "editmonster") { editmonster($do[1]); }
    elseif ($do[0] == "levels") { levels(); }
    elseif ($do[0] == "editlevel") { editlevel(); }
    elseif ($do[0] == "spells") { spells(); }
    elseif ($do[0] == "editspell") { editspell($do[1]); }
    elseif ($do[0] == "edituser") { edituser($do[1]); }
    elseif ($do[0] == "strongholds") { strongholds(); }
    elseif ($do[0] == "editstronghold") { editstronghold($do[1]); }
    elseif ($do[0] == "guilds") { guilds(); }
    elseif ($do[0] == "editguild") { editguild($do[1]); }
    elseif ($do[0] == "general") { general(); }
    elseif ($do[0] == "editgeneral") { editgeneral($do[1]); }
    elseif ($do[0] == "support") { support(); }
    elseif ($do[0] == "editsupport") { editsupport($do[1]); }
    elseif ($do[0] == "suggestion") { suggestion(); }
    elseif ($do[0] == "editsuggestion") { editsuggestion($do[1]); }
    elseif ($do[0] == "market") { market(); }
    elseif ($do[0] == "editmarket") { editmarket($do[1]); }
    elseif ($do[0] == "gforum") { gforum(); }
    elseif ($do[0] == "editgforum") { editgforum($do[1]); }
    elseif ($do[0] == "chat") { chat(); }
    elseif ($do[0] == "editchat") { editchat($do[1]); }
    elseif ($do[0] == "viewnews") { viewnews(); }
    elseif ($do[0] == "editnews") { editnews($do[1]); }
    elseif ($do[0] == "news") { addnews(); }
    elseif ($do[0] == "delete") { func_delete($do[1],$do[2]);}
    elseif ($do[0] == "mailall") { mailall(); }
    elseif ($do[0] == "mailmod") { mailmod(); }    
    elseif ($do[0] == "users") { dolistmembers($do[1]); }
    elseif ($do[0] == "onlineusers") { onlineusers($do[1]); }
    
    elseif ($do[0] == "viewcomments") { viewcomments(); }
    elseif ($do[0] == "editcomments") { editcomments($do[1]); }
    
    elseif ($do[0] == "thread") { showthread($do[1], $do[2]); }
    elseif ($do[0] == "editpost") { editpost($do[1]); }
	elseif ($do[0] == "new") { newthread(); }
	elseif ($do[0] == "reply") { reply(); }
	elseif ($do[0] == "delete") { delete($do[1]); }
	elseif ($do[0] == "list") { donothing($do[1]); }
        
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
                $page .= "<tr><td style=\"background-color:#ffffff;\">".$namelink2."<a href=\"admin.php?do=thread:".$row["id"].":0\">".$row["title"]."</a></td><td style=\"background-color:#ffffff;\">".$row["replies"]."</td><td style=\"background-color:#ffffff;\">".$row["author"]."</td><td style=\"background-color:#ffffff;\">".$row["newpostdate"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">".$namelink2."<a href=\"admin.php?do=thread:".$row["id"].":0\">".$row["title"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["replies"]."</td><td style=\"background-color:#eeeeee;\">".$row["author"]."</td><td style=\"background-color:#eeeeee;\">".$row["newpostdate"]."</td></tr>\n";
			$count = 1;
		}
	  }
    }

    $page .= "</table></td></tr></table><hr />";

$query= doquery("SELECT * FROM {{table}} WHERE parent='0' AND pin!='1' ORDER BY newpostdate DESC LIMIT ".$start.",12", "staff");
$fullquery = doquery("SELECT * FROM {{table}} WHERE parent='0' AND pin!='1' ORDER BY newpostdate", "staff");
 $page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"4\" style=\"background-color:#dddddd;\"><center><a href=\"admin.php?do=new\">Create a New Thread</a></center></th></tr><tr><th width=\"44%\" style=\"background-color:#dddddd;\">Thread Title</th><th width=\"2%\" style=\"background-color:#dddddd;\">Replies</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Author</th><th  width=\"30%\" style=\"background-color:#dddddd;\">Last Post</th></tr>\n";
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
                $page .= "<tr><td style=\"background-color:#ffffff;\">".$namelink."<a href=\"admin.php?do=thread:".$row["id"].":0\">".$row["title"]."</a></td><td style=\"background-color:#ffffff;\">".$row["replies"]."</td><td style=\"background-color:#ffffff;\">".$row["author"]."</td><td style=\"background-color:#ffffff;\">".$row["newpostdate"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">".$namelink."<a href=\"admin.php?do=thread:".$row["id"].":0\">".$row["title"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["replies"]."</td><td style=\"background-color:#eeeeee;\">".$row["author"]."</td><td style=\"background-color:#eeeeee;\">".$row["newpostdate"]."</td></tr>\n";
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
		$page .= "<a href='admin.php?do=list:".$pagestart."'>".$pagelink."</a>   ";}
		else {
		$page .= "<i>".$pagelink."</i>   ";}
	}
	$page .= " ]</center></td></tr>";
    $page .= "</table></td></tr></table><hr />";

    admindisplay($page, "Staff Forum");
    
}

function showthread($id, $start) {

global $controlrow, $userrow; 


    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' OR parent='$id' ORDER BY id LIMIT $start,50", "staff");
    $query2 = doquery("SELECT title FROM {{table}} WHERE id='$id' LIMIT 1", "staff");
    $row2 = mysql_fetch_array($query2);
    
 $page = "<table width='100%' border='1'><tr><td class='title'>Staff Forum - View Thread</td></tr></table><p>[<a href=\"#bottom\">Go to Bottom</a>]<p>";
    $page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><td colspan=\"2\" style=\"background-color:#dddddd;\"><b><a href=\"admin.php\">Staff Forum</a> :: ".$row2["title"]."</b></td></tr>\n";
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
	  		$avatar = "Status: Administrator<br><img src=\"gfx/avataradmin.gif\" alt=\"Administrator\"><p>";
	  	}
	  	elseif ($row3["authlevel"] == "3") { //Mod
	  		$avatar = "Status: Moderator<br><img src=\"$titi2\" alt=\"$titi\" width=\"80\" height=\"80\"><p>";
	  	} else {		
	  		$avatar = "Status: Member<br><img src=\"$titi2\" alt=\"$titi\" width=\"60\" height=\"60\"><p>";
	  	}
        if ($count == 1) {
            $page .= "<tr><td width=\"25%\" style=\"background-color:#ffffff; vertical-align:top;\"><span class=\"small\"><b>".$row["author"]."</b><br />".$avatar."Posts: ".$row3["postcount"]."<br />".prettyforumdate($row["postdate"])."</td><td style=\"background-color:#ffffff; vertical-align:top;\">".nl2br($row["content"])."<br><br><hr /><style=\"background-color:#eeeeee; vertical-align:bottom;\">[<a href=\"index.php?do=onlinechar:".$authorrow["id"]."\">View Profile</a>] [<a href=\"admin.php?do=editpost:".$row["id"]."\">Edit Post</a>]</td></tr>\n";
            $count = 2;
            
        } else {
            $page .= "<tr><td width=\"25%\" style=\"background-color:#eeeeee; vertical-align:top;\"><span class=\"small\"><b>".$row["author"]."</b><br />".$avatar."Posts: ".$row3["postcount"]."<br />".prettyforumdate($row["postdate"])."</td><td style=\"background-color:#eeeeee; vertical-align:top;\">".nl2br($row["content"])."<br><br><hr /><style=\"background-color:#eeeeee; vertical-align:bottom;\">[<a href=\"index.php?do=onlinechar:".$authorrow["id"]."\">View Profile</a>] [<a href=\"admin.php?do=editpost:".$row["id"]."\">Edit Post</a>]</td></tr>\n";
            $count = 1;
        }
    }
    
    

    $page .= "</table></td></tr></table><br />";

$query = doquery("SELECT * FROM {{table}} WHERE id='$id' OR parent='$id' ORDER BY id LIMIT $start,50", "staff");
$row = mysql_fetch_array($query);
if ($row["close"] == 1)  {
 $page .= "<a name=\"bottom\"></a>[<a href=\"#top\">Go to Top</a>]<p><center><img src=\"img/padlock.gif\"><br><b>This thread has been Closed</b></center><p>";

    } else {

    $page .= "<a name=\"bottom\"></a>[<a href=\"#top\">Go to Top</a>]<p><table width=\"100%\"><tr><td><b>Reply To This Thread:</b><br /><form action=\"admin.php?do=reply\" method=\"post\"><input type=\"hidden\" name=\"parent\" value=\"$id\" /><input type=\"hidden\" name=\"title\" value=\"Re: ".$row2["title"]."\" /><textarea name=\"content\" rows=\"7\" cols=\"40\"></textarea><br /><input type=\"submit\" name=\"submit\" value=\"Submit\" /> <input type=\"reset\" name=\"reset\" value=\"Reset\" /></form></td></tr></table>";

}
     
    
    admindisplay($page, "Staff Forum");
    
}

function reply() {

    global $userrow;
	extract($_POST);

	$query = doquery("INSERT INTO {{table}} SET id='',postdate=NOW(),newpostdate=NOW(),author='".$userrow["charname"]."',parent='$parent',replies='0',title='$title',content='$content'", "staff");
	$query2 = doquery("UPDATE {{table}} SET newpostdate=NOW(),replies=replies+1 WHERE id='$parent' LIMIT 1", "staff");
        $query = doquery("UPDATE {{table}} SET postcount=postcount+1 WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	header("Location: admin.php?do=thread:$parent:0");
	die();
	
}

function newthread() {

    global $userrow;
    
    if (isset($_POST["submit"])) {

        extract($_POST);

        $query = doquery("INSERT INTO {{table}} SET id='',postdate=NOW(),newpostdate=NOW(),author='".$userrow["charname"]."',parent='0',replies='0',title='$title',content='$content'", "staff");
        $query = doquery("UPDATE {{table}} SET postcount=postcount+1 WHERE id='".$userrow["id"]."' LIMIT 1", "users");
         header("Location: admin.php");
        die();
    }
     $page = "<table width='100%' border='1'><tr><td class='title'>Staff Forum - Create Thread</td></tr></table><p>";
    $page .= "<table width=\"100%\"><tr><td><b>Create a New Thread:</b><br /><br/ ><form action=\"admin.php?do=new\" method=\"post\">Title:<br /><input type=\"text\" name=\"title\" size=\"50\" maxlength=\"50\" /><br /><br />Message:<br /><textarea name=\"content\" rows=\"7\" cols=\"40\"></textarea><br /><br /><input type=\"submit\" name=\"submit\" value=\"Submit\" /> <input type=\"reset\" name=\"reset\" value=\"Reset\" /></form></td></tr></table>";
   
  
admindisplay($page, "Staff Forum");
    
}

function editpost($id) {
 global $userrow;

    if (isset($_POST["submit"])) {

        extract($_POST);
        $errors = 0;
        $errorlist = "";
        if ($content == "") { $errors++; $errorlist .= "Content is required, return to the <a href=\"admin.php\">Support Forum</a>.<br />"; }
       if ($title == "") { $errors++; $errorlist .= "Title is required, return to the <a href=\"admin.php\">Support Forum</a>. If you wish to delete your whole Post, simply add a small comment saying you have removed it.<br />"; }


        
        if ($errors == 0) { 
            $query = doquery("UPDATE {{table}} SET title='$title', content='$content' WHERE id='$id' LIMIT 1", "staff");
            admindisplay("Your Post was successfully updated. Return to the <a href=\"admin.php\">Staff Forum</a>.","Edit Post");
        } else {
            admindisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Post");
        }        
        
    }   
$idquery = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "staff");
	$idrow = mysql_fetch_array($idquery);
	if ($idrow["author"] != $userrow["charname"]) {
        $page .= "<table width='100%' border='1'><tr><td class='title'>Staff Forum - Edit Denied</td></tr></table><p>";
	$page .= "You cannot edit this Post! This Post doesn't belong to you. Return to the <a href='index.php'>Game</a>.<br>";
	admindisplay($page, "Edit Post");
	}          
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "staff");
    $row = mysql_fetch_array($query);

$page = <<<END
<table width="100%"><tr><td class="title">Edit Post</td></tr></table>
<form action="admin.php?do=editpost:$id" method="post">
<table width="90%">
<tr><td width="20%">Author:</td><td>{{author}} - <a href="admin.php?do=delete:$id">Delete Permanently</a></td></tr>
<tr><td width="20%">Post Date:</td><td>{{postdate}}</td></tr>
<tr><td width="20%">Title:</td><td><input type="text" name="title" size="50" maxlength="50" value="{{title}}" /></td></tr>
<tr><td width="20%">Content:</td><td><textarea name="content" rows="7" cols="40">{{content}}</textarea></td></tr>
</table>
<input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" />
</form>
END;
    
    $page = parsetemplate($page, $row);
    admindisplay($page, "Edit Post");
    
}

function delete($id) {
	 global $userrow;
	$idquery = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "staff");
	$idrow = mysql_fetch_array($idquery);
	if ($idrow["author"] != $userrow["charname"]) {
        $page .= "<table width='100%' border='1'><tr><td class='title'>Staff Forum - Edit Denied</td></tr></table><p>";
	$page .= "You cannot delete this Post! This Post doesn't belong to you. Return to the <a href='index.php'>Game</a>.<br>";
	admindisplay($page, "Delete Post");
	} 
	    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "staff");
    $row = mysql_fetch_array($query);

	$query = doquery("DELETE FROM {{table}} WHERE id='$id' LIMIT 1", "staff");
	$query = doquery("UPDATE {{table}} SET postcount=postcount-1 WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	header("Location: admin.php");
	die();

}

function main() {
    
    if (isset($_POST["submit"])) {
        extract($_POST);
        $errors = 0;
        $errorlist = "";
        if ($gamename == "") { $errors++; $errorlist .= "Game name is required.<br />"; }
        if (($gamesize % 5) != 0) { $errors++; $errorlist .= "Map size must be divisible by five.<br />"; }
        if (!is_numeric($gamesize)) { $errors++; $errorlist .= "Map size must be a number.<br />"; }
        if ($class1name == "") { $errors++; $errorlist .= "Class 1 name is required.<br />"; }
        if ($class2name == "") { $errors++; $errorlist .= "Class 2 name is required.<br />"; }
        if ($class3name == "") { $errors++; $errorlist .= "Class 3 name is required.<br />"; }
        if ($class4name == "") { $errors++; $errorlist .= "Class 4 name is required.<br />"; }
        if ($class5name == "") { $errors++; $errorlist .= "Class 5 name is required.<br />"; }
        if ($class6name == "") { $errors++; $errorlist .= "Class 6 name is required.<br />"; }
        if ($class7name == "") { $errors++; $errorlist .= "Class 7 name is required.<br />"; }
        
        if ($errors == 0) { 
            $query = doquery("UPDATE {{table}} SET gamename='$gamename',gamesize='$gamesize',updatetime='$updatetime',info='$info',class1name='$class1name',class2name='$class2name',class3name='$class3name',class4name='$class4name',class5name='$class5name',class6name='$class6name',class7name='$class7name',gameopen='$gameopen',verifyemail='$verifyemail',gameurl='$gameurl',adminemail='$adminemail',shownews='$shownews',displaychat='$displaychat' WHERE id='1' LIMIT 1", "control");
            admindisplay("Settings updated.","Main Settings");
        } else {
            admindisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Main Settings");
        }
    }
    
    global $controlrow;
    
$page = <<<END
<b><u>Main Settings</u></b><br />
These options control several major settings for the overall game engine.<br /><br />
<form action="admin.php?do=main" method="post">
<table width="90%">
<tr><td width="20%"><span class="highlight">Game Open:</span></td><td><select name="gameopen"><option value="1" {{open1select}}>Open</option><option value="0" {{open0select}}>Closed</option></select><br /><span class="small">Close the game if you are upgrading or working on settings and don't want to cause odd errors for end-users. Closing the game will completely halt all activity.</span></td></tr>
<tr><td width="20%">Update Time:</td><td><input type="text" name="updatetime" size="20" maxlength="30" value="{{updatetime}}" /><br /></td></tr>
<tr><td width="20%">Information:</td><td><textarea name="info" rows="5" cols="35">{{info}}</textarea><br /></td></tr>
<tr><td width="20%">Game Name:</td><td><input type="text" name="gamename" size="30" maxlength="50" value="{{gamename}}" /><br /><span class="small">Default is "Dragons Kingdom". Change this if you want to change to call your game something different.</span></td></tr>
<tr><td width="20%">Game URL:</td><td><input type="text" name="gameurl" size="50" maxlength="100" value="{{gameurl}}" /><br /><span class="small">Please specify the full URL to your game installation ("http://www.server.com/dkpath/index.php").  This gets used in the registration email sent to users. If you leave this field blank or incorrect, users may not be able to register correctly.</span></td></tr>
<tr><td width="20%">Admin Email:</td><td><input type="text" name="adminemail" size="30" maxlength="100" value="{{adminemail}}" /><br /><span class="small">Please specify your email address. This gets used when the game has to send an email to users.</span></td></tr>
<tr><td width="20%">Map Size:</td><td><input type="text" name="gamesize" size="3" maxlength="3" value="{{gamesize}}" /><br /><span class="small">Default is 250. This is the size of each map quadrant. Note that monster levels increase every 5 spaces, so you should ensure that you have at least (map size / 5) monster levels total, otherwise there will be parts of the map without any monsters, or some monsters won't ever get used. Ex: with a map size of 250, you should have 50 monster levels total.</span></td></tr>
<tr><td width="20%">Page Compression:</td><td><select name="compression"><option value="0" {{selectcomp0}}>Disabled</option><option value="1" {{selectcomp1}}>Enabled</option></select><br /><span class="small">Enable page compression if it is supported by your server, and this will greatly reduce the amount of bandwidth required by the game.</span></td></tr>
<tr><td width="20%">Email Verification:</td><td><select name="verifyemail"><option value="0" {{selectverify0}}>Disabled</option><option value="1" {{selectverify1}}>Enabled</option></select><br /><span class="small">Make users verify their email address for added security.</span></td></tr>
<tr><td width="20%">Display Chat:</td><td><select name="displaychat"><option value="0" {{selectdisplaychat0}}>No</option><option value="1" {{selectdisplaychat1}}>Yes</option></select><br /><span class="small">Toggle display of the Player Chat.</td></tr>
<tr><td width="20%">Show News:</td><td><select name="shownews"><option value="0" {{selectnews0}}>No</option><option value="1" {{selectnews1}}>Yes</option></select><br /><span class="small">Toggle display of the Latest News box in towns.</td></tr>
<tr><td width="20%">Class 1 Name:</td><td><input type="text" name="class1name" size="20" maxlength="50" value="{{class1name}}" /><br /></td></tr>
<tr><td width="20%">Class 2 Name:</td><td><input type="text" name="class2name" size="20" maxlength="50" value="{{class2name}}" /><br /></td></tr>
<tr><td width="20%">Class 3 Name:</td><td><input type="text" name="class3name" size="20" maxlength="50" value="{{class3name}}" /><br /></td></tr>
<tr><td width="20%">Class 4 Name:</td><td><input type="text" name="class4name" size="20" maxlength="50" value="{{class4name}}" /><br /></td></tr>
<tr><td width="20%">Class 5 Name:</td><td><input type="text" name="class5name" size="20" maxlength="50" value="{{class5name}}" /><br /></td></tr>
<tr><td width="20%">Class 6 Name:</td><td><input type="text" name="class6name" size="20" maxlength="50" value="{{class6name}}" /><br /></td></tr>
<tr><td width="20%">Class 7 Name:</td><td><input type="text" name="class7name" size="20" maxlength="50" value="{{class7name}}" /><br /></td></tr>
</table>
<input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" />
</form>
END;

    if ($controlrow["compression"] == 0) { $controlrow["selectcomp0"] = "selected=\"selected\" "; } else { $controlrow["selectcomp0"] = ""; }
    if ($controlrow["compression"] == 1) { $controlrow["selectcomp1"] = "selected=\"selected\" "; } else { $controlrow["selectcomp1"] = ""; }
    if ($controlrow["verifyemail"] == 0) { $controlrow["selectverify0"] = "selected=\"selected\" "; } else { $controlrow["selectverify0"] = ""; }
    if ($controlrow["verifyemail"] == 1) { $controlrow["selectverify1"] = "selected=\"selected\" "; } else { $controlrow["selectverify1"] = ""; }
    if ($controlrow["shownews"] == 0) { $controlrow["selectnews0"] = "selected=\"selected\" "; } else { $controlrow["selectnews0"] = ""; }
    if ($controlrow["shownews"] == 1) { $controlrow["selectnews1"] = "selected=\"selected\" "; } else { $controlrow["selectnews1"] = ""; }
     if ($controlrow["displaychat"] == 0) { $controlrow["selectdisplaychat0"] = "selected=\"selected\" "; } else { $controlrow["selectdisplaychat0"] = ""; }
    if ($controlrow["displaychat"] == 1) { $controlrow["selectdisplaychat1"] = "selected=\"selected\" "; } else { $controlrow["selectdisplaychat1"] = ""; }
 
    if ($controlrow["showonline"] == 0) { $controlrow["selectonline0"] = "selected=\"selected\" "; } else { $controlrow["selectonline0"] = ""; }
    if ($controlrow["showonline"] == 1) { $controlrow["selectonline1"] = "selected=\"selected\" "; } else { $controlrow["selectonline1"] = ""; }
    if ($controlrow["gameopen"] == 1) { $controlrow["open1select"] = "selected=\"selected\" "; } else { $controlrow["open1select"] = ""; }
    if ($controlrow["gameopen"] == 0) { $controlrow["open0select"] = "selected=\"selected\" "; } else { $controlrow["open0select"] = ""; }

    $page = parsetemplate($page, $controlrow);
    admindisplay($page, "Main Settings");

}

function onlineusers()

{
global $userrow;
    $page = "<br>Click on a Players ID to Edit their profile. Players who are highlighted Red, are Administrators and Players who are highlighted Green, are Moderators. Players who are Blue, are muted from Chat.<p></center>";

  $onlinequery = doquery("SELECT * FROM {{table}} WHERE UNIX_TIMESTAMP(onlinetime) >= '".(time()-300)."' ORDER BY charname", "users");
         $page .= "<table width=\"75%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"5\" style=\"background-color:#dddddd;\"><center>There have been " . mysql_num_rows($onlinequery) . " user(s) Online within the last few minutes, in order<br>of character name: </center></th></tr><tr><th width=\"5%\" style=\"background-color:#dddddd;\">Player ID</th><th width=\"30%\" style=\"background-color:#dddddd;\">Character Name</th><th width=\"5%\" style=\"background-color:#dddddd;\">Level</th><th width=\"30%\" style=\"background-color:#dddddd;\">Currently</th></tr>\n";

    $count = 1;


     while ($onlinerow = mysql_fetch_array($onlinequery)) {
        if ($onlinerow["authlevel"] == 1){
	   $page .= "<tr style=\"background-color:red;\"><td><center><a href=\"admin.php?do=edituser:".$onlinerow["id"]."\">".$onlinerow["id"]."</a></center></td><td>".$onlinerow["charname"]."</td><td><center>".$onlinerow["level"]."</center></td><td>Unknown</td></tr>\n";

	}elseif($onlinerow["authlevel"] == 3) {
           $page .= "<tr style=\"background-color:green;\"><td><center><a href=\"admin.php?do=edituser:".$onlinerow["id"]."\">".$onlinerow["id"]."</a></center></td><td>".$onlinerow["charname"]."</td><td><center>".$onlinerow["level"]."</center></td><td>".$onlinerow["location"]."</td></tr>\n";
        
		}elseif($onlinerow["authlevel"] == 4) {
           $page .= "<tr style=\"background-color:blue;\"><td><center><a href=\"admin.php?do=edituser:".$onlinerow["id"]."\">".$onlinerow["id"]."</a></center></td><td>".$onlinerow["charname"]."</td><td><center>".$onlinerow["level"]."</center></td><td>".$onlinerow["location"]."</td></tr>\n";
       
	
	
	}else{
	   if ($count == 1) {
             $page .= "<tr><td style=\"background-color:#ffffff;\"><center><a href=\"admin.php?do=edituser:".$onlinerow["id"]."\">".$onlinerow["id"]."</a></center></td><td style=\"background-color:#ffffff;\">".$onlinerow["charname"]."</td><td style=\"background-color:#ffffff;\"><center>".$onlinerow["level"]."</center></td><td style=\"background-color:#ffffff;\">".$onlinerow["location"]."</td></tr>\n";
             $count = 2;
           } else {
             $page .= "<tr><td style=\"background-color:#eeeeee;\"><center><a href=\"admin.php?do=edituser:".$onlinerow["id"]."\">".$onlinerow["id"]."</a></center></td><td style=\"background-color:#eeeeee;\">".$onlinerow["charname"]."</td><td style=\"background-color:#eeeeee;\"><center>".$onlinerow["level"]."</center></td><td style=\"background-color:#eeeeee;\">".$onlinerow["location"]."</td></tr>\n";
             $count = 1;
           }
        }
     }

    $page .= "</table></td></tr></table>";

    admindisplay($page, "Online Users");
}

function dolistmembers ($filter) {
	global $userrow;

	if (!isset($filter)) { $filter = "A";}

	$page .= "<b><u>Edit Users</u></b><br />Click on the character name to edit their information. Edit <a href='admin.php?do=edituser:1'>Admin</a> Profile.<p><center>";

	$page .= "[ <a href='admin.php?do=users:A'>A</a> ";
	$page .= " <a href='admin.php?do=users:B'>B</a> ";
	$page .= " <a href='admin.php?do=users:C'>C</a> ";
	$page .= " <a href='admin.php?do=users:D'>D</a> ";
	$page .= " <a href='admin.php?do=users:E'>E</a> ";
	$page .= " <a href='admin.php?do=users:F'>F</a> ";
	$page .= " <a href='admin.php?do=users:G'>G</a> ";
	$page .= " <a href='admin.php?do=users:H'>H</a> ";
	$page .= " <a href='admin.php?do=users:I'>I</a> ";
	$page .= " <a href='admin.php?do=users:J'>J</a> ";
	$page .= " <a href='admin.php?do=users:K'>K</a> ";
	$page .= " <a href='admin.php?do=users:L'>L</a> ";
	$page .= " <a href='admin.php?do=users:M'>M</a> ";
	$page .= " <a href='admin.php?do=users:N'>N</a> ";
	$page .= " <a href='admin.php?do=users:O'>O</a> ";
	$page .= " <a href='admin.php?do=users:P'>P</a> ";
	$page .= " <a href='admin.php?do=users:Q'>Q</a> ";
	$page .= " <a href='admin.php?do=users:R'>R</a> ";
	$page .= " <a href='admin.php?do=users:S'>S</a> ";
	$page .= " <a href='admin.php?do=users:T'>T</a> ";
	$page .= " <a href='admin.php?do=users:U'>U</a> ";
	$page .= " <a href='admin.php?do=users:V'>V</a> ";
	$page .= " <a href='admin.php?do=users:W'>W</a> ";
	$page .= " <a href='admin.php?do=users:X'>X</a> ";
	$page .= " <a href='admin.php?do=users:Y'>Y</a> ";
	$page .= " <a href='admin.php?do=users:Z'>Z</a> ]<br><br></center>";
	$charquery = doquery("SELECT * FROM {{table}} WHERE charname LIKE '".$filter."%' ORDER by charname", "users");
	$page .= "<center><table width='90%' style='border: solid 1px black' cellspacing='0' cellpadding='0'>";
	$page .= "<center><tr><td colspan=\"7\" bgcolor=\"#ffffff\"><center><b>Dragons Kingdom Players</b></center></td></tr>";
	$page .= "<tr><td><b>Character Name</b></td><td><b>User Name</b></td><td><b>Email</b></td><td><b>Verify Code</b></td></tr>";
	$count = 2;
	while ($charrow = mysql_fetch_array($charquery)) {
		
		if ($count == 1) { $color = "bgcolor='#ffffff'"; $count = 2; }
		else { $color = "bgcolor='#eeeeee'"; $count = 1;}
		$page .= "<tr><td ".$color." width='15%'>";
		if ($userrow["guildrank"] >= 100) {
		$page .= "<a href='admin.php?do=edituser:".$charrow["id"]."'>".$charrow["charname"]."</a>";}
		else {
		$page .= "<a href='admin.php?do=edituser:".$charrow["id"]."'>".$charrow["charname"]."</a>";}
		$page .= "</td>";
		$page .= "<td ".$color." width='25%'>".$charrow["username"]."</td>";
		$page .= "<td ".$color." width='5%'>".$charrow["email"]."</td>";
		$page .= "<td ".$color." width='20%'>".$charrow["verify"]."</td>";
	  	$page .= "</tr>";
	}
	$page .= "</table></center>";

	admindisplay($page, "List Users");

}

function mailall() {

    global $userrow;

    if (isset($_POST["submit"])) {
        extract($_POST);
    $content = str_replace("'", "\'", $content);
    $content = trim($content);
	  $content = "<b><font color=red><u>Global Mailing sent from the Administrator</u>:</b></font>\n\n". $content;
	  $subject = "". $subject;
	  $page = "<table width=\"100%\"><tr><td><b>Global Mailing:</b><br /><br/ >";
	  $c = 0;
        $mailallquery = doquery("SELECT charname FROM {{table}} WHERE verify='1'", "users");
	  while ($charrow = mysql_fetch_array($mailallquery)) {
		$recipient = $charrow["charname"];
		$c += 1;
           	$query = doquery("INSERT INTO {{table}} SET postdate=NOW(),author='".$userrow["charname"]."',recipient='$recipient',subject='$subject',content='$content'", "gamemail");

        }

    	  $page .= "Your message has been sent to all ".$c." players of the game.<p>";
    	      admindisplay($page, "Global Mail");
    }

    $page = "<table width=\"100%\"><tr><td><b>Global Mailing:</b><br /><br/ >";
    $page .= "Enter the message below and it will be sent to all players of the game.<p>";
    $page .= "<form action=\"admin.php?do=mailall\" method=\"post\">";
    $page .= "Subject:<br />";
    $page .= "<input type=\"text\" name=\"subject\" size=\"35\" maxlength=\"35\" /><br><br>";
    $page .= "Message:<br />";
    $page .= "<textarea name=\"content\" rows=\"7\" cols=\"40\"></textarea><br><br>";
    $page .= "<input type=\"submit\" name=\"submit\" value=\"Send Mails\" /> ";
    $page .= "<input type=\"reset\" name=\"reset\" value=\"Reset\" />";
    $page .= "</form></td></tr></table>";
    admindisplay($page, "Global Mail");

}

function mailmod() {

    global $userrow;

    if (isset($_POST["submit"])) {
        extract($_POST);
    $content = str_replace("'", "\'", $content);
    $content = trim($content);
	  $content = "<b><font color=red><u>Mod Mailing sent from the Administrator</u>:</b></font>\n\n". $content;
	  $subject = "". $subject;
	  $page = "<table width=\"100%\"><tr><td><b>Mod Mailing:</b><br /><br/ >";
	  $c = 0;
        $mailallquery = doquery("SELECT charname FROM {{table}} WHERE authlevel='3'", "users");
	  while ($charrow = mysql_fetch_array($mailallquery)) {
		$recipient = $charrow["charname"];
		$c += 1;
           	$query = doquery("INSERT INTO {{table}} SET postdate=NOW(),author='".$userrow["charname"]."',recipient='$recipient',subject='$subject',content='$content'", "gamemail");

        }

    	  $page .= "Your message has been sent to all ".$c." mod players of the game.<p>";
    	      admindisplay($page, "Global Mail");
    }

    $page = "<table width=\"100%\"><tr><td><b>Mod Mailing:</b><br /><br/ >";
    $page .= "Enter the message below and it will be sent to all mod players of the game.<p>";
    $page .= "<form action=\"admin.php?do=mailmod\" method=\"post\">";
    $page .= "Subject:<br />";
    $page .= "<input type=\"text\" name=\"subject\" size=\"35\" maxlength=\"35\" /><br><br>";
    $page .= "Message:<br />";
    $page .= "<textarea name=\"content\" rows=\"7\" cols=\"40\"></textarea><br><br>";
    $page .= "<input type=\"submit\" name=\"submit\" value=\"Send Mails\" /> ";
    $page .= "<input type=\"reset\" name=\"reset\" value=\"Reset\" />";
    $page .= "</form></td></tr></table>";
    admindisplay($page, "Mod Mail");

}

function items() {
    
    $query = doquery("SELECT id,name FROM {{table}} ORDER BY id", "items");
    $page = "<b><u>Edit Items</u></b><br />Click an item's name to edit it.<br /><br /><table width=\"50%\">\n";
    $count = 1;
    while ($row = mysql_fetch_array($query)) {
        if ($count == 1) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">".$row["id"]."</td><td style=\"background-color: #eeeeee;\"><a href=\"admin.php?do=edititem:".$row["id"]."\">".$row["name"]."</a></td></tr>\n"; $count = 2; }
        else { $page .= "<tr><td width=\"8%\" style=\"background-color: #ffffff;\">".$row["id"]."</td><td style=\"background-color: #ffffff;\"><a href=\"admin.php?do=edititem:".$row["id"]."\">".$row["name"]."</a></td></tr>\n"; $count = 1; }
    }
    if (mysql_num_rows($query) == 0) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">No items found.</td></tr>\n"; }
    $page .= "</table>";
    admindisplay($page, "Edit Items");
    
}

function edititem($id) {
    
    if (isset($_POST["submit"])) {
        
        extract($_POST);
        $errors = 0;
        $errorlist = "";
        if ($name == "") { $errors++; $errorlist .= "Name is required.<br />"; }
        if ($buycost == "") { $errors++; $errorlist .= "Cost is required.<br />"; }
        if (!is_numeric($buycost)) { $errors++; $errorlist .= "Cost must be a number.<br />"; }
        if ($attribute == "") { $errors++; $errorlist .= "Attribute is required.<br />"; }
        if (!is_numeric($attribute)) { $errors++; $errorlist .= "Attribute must be a number.<br />"; }
        if ($special == "" || $special == " ") { $special = "X"; }
        
        if ($errors == 0) { 
            $query = doquery("UPDATE {{table}} SET name='$name',type='$type',buycost='$buycost',attribute='$attribute',special='$special' WHERE id='$id' LIMIT 1", "items");
            admindisplay("Item updated.","Edit Items");
        } else {
            admindisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Items");
        }        
        
    }   
        
    
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "items");
    $row = mysql_fetch_array($query);

$page = <<<END
<b><u>Edit Items</u></b><br /><br />
<form action="admin.php?do=edititem:$id" method="post">
<table width="90%">
<tr><td width="20%">ID:</td><td>{{id}}</td></tr>
<tr><td width="20%">Name:</td><td><input type="text" name="name" size="30" maxlength="30" value="{{name}}" /></td></tr>
<tr><td width="20%">Type:</td><td><select name="type"><option value="1" {{type1select}}>Weapon</option><option value="2" {{type2select}}>Armor</option><option value="3" {{type3select}}>Shield</option></select></td></tr>
<tr><td width="20%">Cost:</td><td><input type="text" name="buycost" size="5" maxlength="10" value="{{buycost}}" /> gold</td></tr>
<tr><td width="20%">Attribute:</td><td><input type="text" name="attribute" size="5" maxlength="10" value="{{attribute}}" /><br /><span class="small">How much the item adds to total attackpower (weapons) or defensepower (armor/shields).</span></td></tr>
<tr><td width="20%">Special:</td><td><input type="text" name="special" size="30" maxlength="50" value="{{special}}" /><br /><span class="small">Should be either a special code or <span class="highlight">X</span> to disable. Edit this field very carefully because mistakes to formatting or field names can create problems in the game.</span></td></tr>
</table>
<input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" />
</form>
<b>Special Codes:</b><br />
Special codes can be added in the item's Special field to give it extra user attributes. Special codes are in the format <span class="highlight">attribute,value</span>. <span class="highlight">Attribute</span> can be any database field from the Users table - however, it is suggested that you only use the ones from the list below, otherwise things can get freaky. <span class="highlight">Value</span> may be any positive or negative whole number. For example, if you want a weapon to give an additional 50 max hit points, the special code would be <span class="highlight">maxhp,50</span>.<br /><br />
Suggested user fields for special codes:<br />
maxhp - max hit points<br />
maxmp - max magic points<br />
maxtp - max travel points<br />
goldbonus - gold bonus, in percent<br />
expbonus - experience bonus, in percent<br />
strength - strength (which also adds to attackpower)<br />
dexterity - dexterity (which also adds to defensepower)<br />
attackpower - total attack power<br />
defensepower - total defense power
END;
    
    if ($row["type"] == 1) { $row["type1select"] = "selected=\"selected\" "; } else { $row["type1select"] = ""; }
    if ($row["type"] == 2) { $row["type2select"] = "selected=\"selected\" "; } else { $row["type2select"] = ""; }
    if ($row["type"] == 3) { $row["type3select"] = "selected=\"selected\" "; } else { $row["type3select"] = ""; }
    
    $page = parsetemplate($page, $row);
    admindisplay($page, "Edit Items");
    
}

function drops() {
    
    $query = doquery("SELECT id,name FROM {{table}} ORDER BY id", "drops");
    $page = "<b><u>Edit Drops</u></b><br />Click an item's name to edit it.<br /><br /><table width=\"50%\">\n";
    $count = 1;
    while ($row = mysql_fetch_array($query)) {
        if ($count == 1) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">".$row["id"]."</td><td style=\"background-color: #eeeeee;\"><a href=\"admin.php?do=editdrop:".$row["id"]."\">".$row["name"]."</a></td></tr>\n"; $count = 2; }
        else { $page .= "<tr><td width=\"8%\" style=\"background-color: #ffffff;\">".$row["id"]."</td><td style=\"background-color: #ffffff;\"><a href=\"admin.php?do=editdrop:".$row["id"]."\">".$row["name"]."</a></td></tr>\n"; $count = 1; }
    }
    if (mysql_num_rows($query) == 0) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">No items found.</td></tr>\n"; }
    $page .= "</table>";
    admindisplay($page, "Edit Drops");
    
}

function editdrop($id) {
    
    if (isset($_POST["submit"])) {
        
        extract($_POST);
        $errors = 0;
        $errorlist = "";
        if ($name == "") { $errors++; $errorlist .= "Name is required.<br />"; }
        if ($mlevel == "") { $errors++; $errorlist .= "Monster level is required.<br />"; }
        if (!is_numeric($mlevel)) { $errors++; $errorlist .= "Monster level must be a number.<br />"; }
        if ($attribute1 == "" || $attribute1 == " " || $attribute1 == "X") { $errors++; $errorlist .= "First attribute is required.<br />"; }
        if ($attribute2 == "" || $attribute2 == " ") { $attribute2 = "X"; }
        
        if ($errors == 0) { 
            $query = doquery("UPDATE {{table}} SET name='$name',mlevel='$mlevel',attribute1='$attribute1',attribute2='$attribute2' WHERE id='$id' LIMIT 1", "drops");
            admindisplay("Item updated.","Edit Drops");
        } else {
            admindisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Drops");
        }        
        
    }   
        
    
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "drops");
    $row = mysql_fetch_array($query);

$page = <<<END
<b><u>Edit Drops</u></b><br /><br />
<form action="admin.php?do=editdrop:$id" method="post">
<table width="90%">
<tr><td width="20%">ID:</td><td>{{id}}</td></tr>
<tr><td width="20%">Name:</td><td><input type="text" name="name" size="30" maxlength="30" value="{{name}}" /></td></tr>
<tr><td width="20%">Monster Level:</td><td><input type="text" name="mlevel" size="5" maxlength="10" value="{{mlevel}}" /><br /><span class="small">Minimum monster level that will drop this item.</span></td></tr>
<tr><td width="20%">Attribute 1:</td><td><input type="text" name="attribute1" size="30" maxlength="50" value="{{attribute1}}" /><br /><span class="small">Must be a special code. First attribute cannot be disabled. Edit this field very carefully because mistakes to formatting or field names can create problems in the game.</span></td></tr>
<tr><td width="20%">Attribute 2:</td><td><input type="text" name="attribute2" size="30" maxlength="50" value="{{attribute2}}" /><br /><span class="small">Should be either a special code or <span class="highlight">X</span> to disable. Edit this field very carefully because mistakes to formatting or field names can create problems in the game.</span></td></tr>
</table>
<input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" />
</form>
<b>Special Codes:</b><br />
Special codes are used in the two attribute fields to give the item properties. The first attribute field must contain a special code, but the second one may be left empty ("X") if you wish. Special codes are in the format <span class="highlight">attribute,value</span>. <span class="highlight">Attribute</span> can be any database field from the Users table - however, it is suggested that you only use the ones from the list below, otherwise things can get freaky. <span class="highlight">Value</span> may be any positive or negative whole number. For example, if you want a weapon to give an additional 50 max hit points, the special code would be <span class="highlight">maxhp,50</span>.<br /><br />
Suggested user fields for special codes:<br />
maxhp - max hit points<br />
maxmp - max magic points<br />
maxtp - max travel points<br />
goldbonus - gold bonus, in percent<br />
expbonus - experience bonus, in percent<br />
strength - strength (which also adds to attackpower)<br />
dexterity - dexterity (which also adds to defensepower)<br />
attackpower - total attack power<br />
defensepower - total defense power
END;
    
    $page = parsetemplate($page, $row);
    admindisplay($page, "Edit Drops");
    
}

function towns() {
    
    $query = doquery("SELECT id,name FROM {{table}} ORDER BY id", "towns");
    $page = "<b><u>Edit Towns</u></b><br />Click an town's name to edit it.<br /><br /><table width=\"50%\">\n";
    $count = 1;
    while ($row = mysql_fetch_array($query)) {
        if ($count == 1) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">".$row["id"]."</td><td style=\"background-color: #eeeeee;\"><a href=\"admin.php?do=edittown:".$row["id"]."\">".$row["name"]."</a></td></tr>\n"; $count = 2; }
        else { $page .= "<tr><td width=\"8%\" style=\"background-color: #ffffff;\">".$row["id"]."</td><td style=\"background-color: #ffffff;\"><a href=\"admin.php?do=edittown:".$row["id"]."\">".$row["name"]."</a></td></tr>\n"; $count = 1; }
    }
    if (mysql_num_rows($query) == 0) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">No towns found.</td></tr>\n"; }
    $page .= "</table>";
    admindisplay($page, "Edit Towns");
    
}

function edittown($id) {
    
    if (isset($_POST["submit"])) {
        
        extract($_POST);
        $errors = 0;
        $errorlist = "";
        if ($name == "") { $errors++; $errorlist .= "Name is required.<br />"; }
        if ($latitude == "") { $errors++; $errorlist .= "Latitude is required.<br />"; }
        if (!is_numeric($latitude)) { $errors++; $errorlist .= "Latitude must be a number.<br />"; }
        if ($longitude == "") { $errors++; $errorlist .= "Longitude is required.<br />"; }
        if (!is_numeric($longitude)) { $errors++; $errorlist .= "Longitude must be a number.<br />"; }
        if ($innprice == "") { $errors++; $errorlist .= "Inn Price is required.<br />"; }
        if (!is_numeric($innprice)) { $errors++; $errorlist .= "Inn Price must be a number.<br />"; }
        if ($mapprice == "") { $errors++; $errorlist .= "Map Price is required.<br />"; }
        if (!is_numeric($mapprice)) { $errors++; $errorlist .= "Map Price must be a number.<br />"; }

        if ($travelpoints == "") { $errors++; $errorlist .= "Travel Points is required.<br />"; }
        if (!is_numeric($travelpoints)) { $errors++; $errorlist .= "Travel Points must be a number.<br />"; }
        if ($itemslist == "") { $errors++; $errorlist .= "Items List is required.<br />"; }
        
        if ($errors == 0) { 
            $query = doquery("UPDATE {{table}} SET name='$name',latitude='$latitude',longitude='$longitude',innprice='$innprice',mapprice='$mapprice',gobprice='$gobprice',ogreprice='$ogreprice',mapprice='$mapprice',dragpotprice='$dragpotprice',dragprice='dragprice',aleprice='$aleprice',whiskprice='$whiskprice',itemslist='$itemslist',amulet1='$amulet1',amulet2='$amulet2',amulet3='$amulet3',ring1='$ring1',ring2='$ring2',ring3='$ring3', WHERE id='$id' LIMIT 1", "towns");
            admindisplay("Town updated.","Edit Towns");
        } else {
            admindisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Towns");
        }        
        
    }   
        
    
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "towns");
    $row = mysql_fetch_array($query);

$page = <<<END
<b><u>Edit Towns</u></b><br /><br />
<form action="admin.php?do=edittown:$id" method="post">
<table width="90%">
<tr><td width="20%">ID:</td><td>{{id}}</td></tr>
<tr><td width="20%">Name:</td><td><input type="text" name="name" size="30" maxlength="30" value="{{name}}" /></td></tr>
<tr><td width="20%">Latitude:</td><td><input type="text" name="latitude" size="5" maxlength="10" value="{{latitude}}" /><br /><span class="small">Positive or negative integer.</span></td></tr>
<tr><td width="20%">Longitude:</td><td><input type="text" name="longitude" size="5" maxlength="10" value="{{longitude}}" /><br /><span class="small">Positive or negative integer.</span></td></tr>
<tr><td width="20%">Inn Price:</td><td><input type="text" name="innprice" size="5" maxlength="10" value="{{innprice}}" /> gold</td></tr>
<tr><td width="20%">Ale Price:</td><td><input type="text" name="aleprice" size="5" maxlength="10" value="{{aleprice}}" /> gold<br /><span class="small"></span></td></tr>
<tr><td width="20%">Shot of Whiskey Price:</td><td><input type="text" name="whiskprice" size="5" maxlength="10" value="{{whiskprice}}" /> gold<br /><span class="small"></span></td></tr>
<tr><td width="20%">Dragons Special Price:</td><td><input type="text" name="dragprice" size="5" maxlength="10" value="{{dragprice}}" /> gold<br /><span class="small"></span></td></tr>
<tr><td width="20%">Ogres Potion Price:</td><td><input type="text" name="ogreprice" size="5" maxlength="10" value="{{ogreprice}}" /> gold<br /><span class="small"></span></td></tr>
<tr><td width="20%">Goblins Potion Price:</td><td><input type="text" name="gobprice" size="5" maxlength="10" value="{{gobprice}}" /> gold<br /><span class="small"></span></td></tr>
<tr><td width="20%">Dragons Potion Price:</td><td><input type="text" name="dragpotprice" size="5" maxlength="10" value="{{dragpotprice}}" /> gold<br /><span class="small"></span></td></tr>
<tr><td width="20%">Amulet 1 Price</td><td><input type="text" name="amulet1" size="5" maxlength="10" value="{{amulet1}}" /> gold<br /><span class="small"></span></td></tr>
<tr><td width="20%">Amulet 2 Price</td><td><input type="text" name="amulet2" size="5" maxlength="10" value="{{amulet2}}" /> gold<br /><span class="small"></span></td></tr>
<tr><td width="20%">Amulet 3 Price:</td><td><input type="text" name="amulet3" size="5" maxlength="10" value="{{amulet3}}" /> gold<br /><span class="small"></span></td></tr>
<tr><td width="20%">Ring 1 Price:</td><td><input type="text" name="ring1" size="5" maxlength="10" value="{{ring1}}" /> gold<br /><span class="small"></span></td></tr>
<tr><td width="20%">Ring 2 Price:</td><td><input type="text" name="ring2" size="5" maxlength="10" value="{{ring2}}" /> gold<br /><span class="small"></span></td></tr>
<tr><td width="20%">Ring 3 Price:</td><td><input type="text" name="ring3" size="5" maxlength="10" value="{{ring3}}" /> gold<br /><span class="small"></span></td></tr>
<tr><td width="20%">Map Price:</td><td><input type="text" name="mapprice" size="5" maxlength="10" value="{{mapprice}}" /> gold<br /><span class="small">How much it costs to buy the map to this town.</span></td></tr>
<tr><td width="20%">Travel Points:</td><td><input type="text" name="travelpoints" size="5" maxlength="10" value="{{travelpoints}}" /><br /><span class="small">How many TP are consumed when travelling to this town.</span></td></tr>

<tr><td width="20%">Items List:</td><td><input type="text" name="itemslist" size="30" maxlength="200" value="{{itemslist}}" /><br /><span class="small">Comma-separated list of item ID numbers available for purchase at this town. (Example: <span class="highlight">1,2,3,6,9,10,13,20</span>)</span></td></tr>
</table>
<input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" />
</form>
END;
    
    $page = parsetemplate($page, $row);
    admindisplay($page, "Edit Towns");
    
}

function monsters() {
    
    global $controlrow;
    
    $statquery = doquery("SELECT * FROM {{table}} ORDER BY level DESC LIMIT 1", "monsters");
    $statrow = mysql_fetch_array($statquery);
    
    $query = doquery("SELECT id,name FROM {{table}} ORDER BY id", "monsters");
    $page = "<b><u>Edit Monsters</u></b><br />";
    
    if (($controlrow["gamesize"]/5) != $statrow["level"]) {
        $page .= "<span class=\"highlight\">Note:</span> Your highest monster level does not match with your entered map size. Highest monster level should be ".($controlrow["gamesize"]/5).", yours is ".$statrow["level"].". Please fix this before opening the game to the public.<br /><br />";
    } else { $page .= "Monster level and map size match. No further actions are required for map compatibility.<br /><br />"; }
    
    $page .= "Click an monster's name to edit it.<br /><br /><table width=\"50%\">\n";
    $count = 1;
    while ($row = mysql_fetch_array($query)) {
        if ($count == 1) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">".$row["id"]."</td><td style=\"background-color: #eeeeee;\"><a href=\"admin.php?do=editmonster:".$row["id"]."\">".$row["name"]."</a></td></tr>\n"; $count = 2; }
        else { $page .= "<tr><td width=\"8%\" style=\"background-color: #ffffff;\">".$row["id"]."</td><td style=\"background-color: #ffffff;\"><a href=\"admin.php?do=editmonster:".$row["id"]."\">".$row["name"]."</a></td></tr>\n"; $count = 1; }
    }
    if (mysql_num_rows($query) == 0) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">No towns found.</td></tr>\n"; }
    $page .= "</table>";
    admindisplay($page, "Edit Monster");
    
}

function editmonster($id) {
    
    if (isset($_POST["submit"])) {
        
        extract($_POST);
        $errors = 0;
        $errorlist = "";
        if ($name == "") { $errors++; $errorlist .= "Name is required.<br />"; }
        if ($maxhp == "") { $errors++; $errorlist .= "Max HP is required.<br />"; }
        if (!is_numeric($maxhp)) { $errors++; $errorlist .= "Max HP must be a number.<br />"; }
        if ($maxdam == "") { $errors++; $errorlist .= "Max Damage is required.<br />"; }
        if (!is_numeric($maxdam)) { $errors++; $errorlist .= "Max Damage must be a number.<br />"; }
        if ($armor == "") { $errors++; $errorlist .= "Armor is required.<br />"; }
        if (!is_numeric($armor)) { $errors++; $errorlist .= "Armor must be a number.<br />"; }
        if ($level == "") { $errors++; $errorlist .= "Monster Level is required.<br />"; }
        if (!is_numeric($level)) { $errors++; $errorlist .= "Monster Level must be a number.<br />"; }
        if ($maxexp == "") { $errors++; $errorlist .= "Max Exp is required.<br />"; }
        if (!is_numeric($maxexp)) { $errors++; $errorlist .= "Max Exp must be a number.<br />"; }
        if ($maxgold == "") { $errors++; $errorlist .= "Max Gold is required.<br />"; }
        if (!is_numeric($maxgold)) { $errors++; $errorlist .= "Max Gold must be a number.<br />"; }
        
        if ($errors == 0) { 
            $query = doquery("UPDATE {{table}} SET name='$name',maxhp='$maxhp',maxdam='$maxdam',armor='$armor',level='$level',maxexp='$maxexp',maxgold='$maxgold',immune='$immune' WHERE id='$id' LIMIT 1", "monsters");
            admindisplay("Monster updated.","Edit monsters");
        } else {
            admindisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit monsters");
        }        
        
    }   
        
    
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "monsters");
    $row = mysql_fetch_array($query);

$page = <<<END
<b><u>Edit Monsters</u></b><br /><br />
<form action="admin.php?do=editmonster:$id" method="post">
<table width="90%">
<tr><td width="20%">ID:</td><td>{{id}}</td></tr>
<tr><td width="20%">Name:</td><td><input type="text" name="name" size="30" maxlength="30" value="{{name}}" /></td></tr>
<tr><td width="20%">Max Hit Points:</td><td><input type="text" name="maxhp" size="5" maxlength="10" value="{{maxhp}}" /></td></tr>
<tr><td width="20%">Max Damage:</td><td><input type="text" name="maxdam" size="5" maxlength="10" value="{{maxdam}}" /><br /><span class="small">Compares to player's attackpower.</span></td></tr>
<tr><td width="20%">Armor:</td><td><input type="text" name="armor" size="5" maxlength="10" value="{{armor}}" /><br /><span class="small">Compares to player's defensepower.</span></td></tr>
<tr><td width="20%">Monster Level:</td><td><input type="text" name="level" size="5" maxlength="10" value="{{level}}" /><br /><span class="small">Determines spawn location and item drops.</span></td></tr>
<tr><td width="20%">Max Experience:</td><td><input type="text" name="maxexp" size="5" maxlength="10" value="{{maxexp}}" /><br /><span class="small">Max experience gained from defeating monster.</span></td></tr>
<tr><td width="20%">Max Gold:</td><td><input type="text" name="maxgold" size="5" maxlength="10" value="{{maxgold}}" /><br /><span class="small">Max gold gained from defeating monster.</span></td></tr>
<tr><td width="20%">Immunity:</td><td><select name="immune"><option value="0" {{immune0select}}>None</option><option value="1" {{immune1select}}>Hurt Spells</option><option value="2" {{immune2select}}>Hurt & Sleep Spells</option></select><br /><span class="small">Some monsters may not be hurt by certain spells.</span></td></tr>
</table>
<input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" />
</form>
END;
    
    if ($row["immune"] == 1) { $row["immune1select"] = "selected=\"selected\" "; } else { $row["immune1select"] = ""; }
    if ($row["immune"] == 2) { $row["immune2select"] = "selected=\"selected\" "; } else { $row["immune2select"] = ""; }
    if ($row["immune"] == 3) { $row["immune3select"] = "selected=\"selected\" "; } else { $row["immune3select"] = ""; }
    
    $page = parsetemplate($page, $row);
    admindisplay($page, "Edit Monsters");
    
}

function spells() {
    
    $query = doquery("SELECT id,name FROM {{table}} ORDER BY id", "spells");
    $page = "<b><u>Edit Spells</u></b><br />Click an spell's name to edit it.<br /><br /><table width=\"50%\">\n";
    $count = 1;
    while ($row = mysql_fetch_array($query)) {
        if ($count == 1) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">".$row["id"]."</td><td style=\"background-color: #eeeeee;\"><a href=\"admin.php?do=editspell:".$row["id"]."\">".$row["name"]."</a></td></tr>\n"; $count = 2; }
        else { $page .= "<tr><td width=\"8%\" style=\"background-color: #ffffff;\">".$row["id"]."</td><td style=\"background-color: #ffffff;\"><a href=\"admin.php?do=editspell:".$row["id"]."\">".$row["name"]."</a></td></tr>\n"; $count = 1; }
    }
    if (mysql_num_rows($query) == 0) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">No spells found.</td></tr>\n"; }
    $page .= "</table>";
    admindisplay($page, "Edit Spells");
    
}

function editspell($id) {
    
    if (isset($_POST["submit"])) {
        
        extract($_POST);
        $errors = 0;
        $errorlist = "";
        if ($name == "") { $errors++; $errorlist .= "Name is required.<br />"; }
        if ($mp == "") { $errors++; $errorlist .= "MP is required.<br />"; }
        if (!is_numeric($mp)) { $errors++; $errorlist .= "MP must be a number.<br />"; }
        if ($attribute == "") { $errors++; $errorlist .= "Attribute is required.<br />"; }
        if (!is_numeric($attribute)) { $errors++; $errorlist .= "Attribute must be a number.<br />"; }
        
        if ($errors == 0) { 
            $query = doquery("UPDATE {{table}} SET name='$name',mp='$mp',attribute='$attribute',type='$type' WHERE id='$id' LIMIT 1", "spells");
            admindisplay("Spell updated.","Edit Spells");
        } else {
            admindisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Spells");
        }        
        
    }   
        
    
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "spells");
    $row = mysql_fetch_array($query);

$page = <<<END
<b><u>Edit Spells</u></b><br /><br />
<form action="admin.php?do=editspell:$id" method="post">
<table width="90%">
<tr><td width="20%">ID:</td><td>{{id}}</td></tr>
<tr><td width="20%">Name:</td><td><input type="text" name="name" size="30" maxlength="30" value="{{name}}" /></td></tr>
<tr><td width="20%">Magic Points:</td><td><input type="text" name="mp" size="5" maxlength="10" value="{{mp}}" /><br /><span class="small">MP required to cast spell.</span></td></tr>
<tr><td width="20%">Attribute:</td><td><input type="text" name="attribute" size="5" maxlength="10" value="{{attribute}}" /><br /><span class="small">Numeric value of the spell's effect. Ties with type, below.</span></td></tr>
<tr><td width="20%">Type:</td><td><select name="type"><option value="1" {{type1select}}>Heal</option><option value="2" {{type2select}}>Hurt</option><option value="3" {{type3select}}>Sleep</option><option value="4" {{type4select}}>Uber Attack</option><option value="5" {{type5select}}>Uber Defense</option><option value="6" {{type6select}}>Capture</option></select><br /><span class="small">- Heal gives player back [attribute] hit points.<br />- Hurt deals [attribute] damage to monster.<br />- Sleep keeps monster from attacking ([attribute] is monster's chance out of 15 to stay asleep each turn).<br />- Uber Attack increases total attack damage by [attribute] percent.<br />- Uber Defense increases total defense from attack by [attribute] percent.</span></td></tr>
</table>
<input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" />
</form>
END;

    if ($row["type"] == 1) { $row["type1select"] = "selected=\"selected\" "; } else { $row["type1select"] = ""; }
    if ($row["type"] == 2) { $row["type2select"] = "selected=\"selected\" "; } else { $row["type2select"] = ""; }
    if ($row["type"] == 3) { $row["type3select"] = "selected=\"selected\" "; } else { $row["type3select"] = ""; }
    if ($row["type"] == 4) { $row["type4select"] = "selected=\"selected\" "; } else { $row["type4select"] = ""; }
    if ($row["type"] == 5) { $row["type5select"] = "selected=\"selected\" "; } else { $row["type5select"] = ""; }
    if ($row["type"] == 6) { $row["type6select"] = "selected=\"selected\" "; } else { $row["type6select"] = ""; }
    
    $page = parsetemplate($page, $row);
    admindisplay($page, "Edit Spells");
    
}

function levels() {

    $query = doquery("SELECT id FROM {{table}} ORDER BY id DESC LIMIT 1", "levels");
    $row = mysql_fetch_array($query);
    
    $options = "";
    for($i=2; $i<$row["id"]; $i++) {
        $options .= "<option value=\"$i\">$i</option>\n";
    }
    
$page = <<<END
<b><u>Edit Levels</u></b><br />Select a level number from the dropdown box to edit it.<br /><br />
<form action="admin.php?do=editlevel" method="post">
<select name="level">
$options
</select> 
<input type="submit" name="go" value="Submit" />
</form>
END;

    admindisplay($page, "Edit Levels");
    
}

function viewnews() {
    
    global $controlrow;
    
    $statquery = doquery("SELECT * FROM {{table}} ORDER BY id DESC LIMIT 1", "news");
    $statrow = mysql_fetch_array($statquery);
    
    $query = doquery("SELECT id,postdate FROM {{table}} ORDER BY id", "news");
    $page = "<b><u>Edit News</u></b><br />";
    
    $page .= "Click a news post to edit it.<br /><br /><table width=\"50%\">\n";
    $count = 1;
    while ($row = mysql_fetch_array($query)) {
        if ($count == 1) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">".$row["id"]."</td><td style=\"background-color: #eeeeee;\"><a href=\"admin.php?do=editnews:".$row["id"]."\">".$row["postdate"]."</a></td></tr>\n"; $count = 2; }
        else { $page .= "<tr><td width=\"8%\" style=\"background-color: #ffffff;\">".$row["id"]."</td><td style=\"background-color: #ffffff;\"><a href=\"admin.php?do=editnews:".$row["id"]."\">".$row["postdate"]."</a></td></tr>\n"; $count = 1; }
    }
    if (mysql_num_rows($query) == 0) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">No news found.</td></tr>\n"; }
    $page .= "</table>";
    admindisplay($page, "Edit News");
    
}

function editnews($id) {
    
    if (isset($_POST["submit"])) {
        
        extract($_POST);
        $errors = 0;
        $errorlist = "";
        if ($content == "") { $errors++; $errorlist .= "Content is required.<br />"; }
        if ($postdate == "") { $errors++; $errorlist .= "Post Date is required.<br />"; }


        
        if ($errors == 0) { 
            $query = doquery("UPDATE {{table}} SET postdate='$postdate', content='$content' WHERE id='$id' LIMIT 1", "news");
            admindisplay("News updated.","Edit News");
        } else {
            admindisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit News");
        }        
        
    }   
        
    
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "news");
    $row = mysql_fetch_array($query);

$page = <<<END
<b><u>Edit News</u></b><br /><br />
<form action="admin.php?do=editnews:$id" method="post">
<table width="90%">
<tr><td width="20%">Post ID:</td><td>{{id}}</td></tr>
<tr><td width="20%">Post Date:</td><td><input type="text" name="postdate" size="20" maxlength="20" value="{{postdate}}" /></td></tr>
<tr><td width="20%">Content:</td><td><textarea name="content" rows="15" cols="45">{{content}}</textarea></td></tr>

</table>
<input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" />
</form>
END;
    
    $page = parsetemplate($page, $row);
    admindisplay($page, "Edit News");
    
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
        if ($count == 1) { $page .= "<tr><td style=\"background-color: #eeeeee;\"><a href=\"admin.php?do=editcomments:".$row["id"]."\">".$row["post"]."</a></td></tr>\n"; $count = 2; }
        else { $page .= "<tr><td style=\"background-color: #ffffff;\"><a href=\"admin.php?do=editcomments:".$row["id"]."\">".$row["post"]."</a></td></tr>\n"; $count = 1; }
    }
    if (mysql_num_rows($query) == 0) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">No comments found.</td></tr>\n"; }
    $page .= "</table>";
    admindisplay($page, "Edit Comments");
    
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
            admindisplay("Comment was successfully updated.","Edit Comment");
        } else {
            admindisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Comment");
        }        
        
    }   
      
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "comments");
    $row = mysql_fetch_array($query);

$page = <<<END
<form action="admin.php?do=editcomments:$id" method="post">
<table width="90%">
<tr><td width="20%"><a href="admin.php?do=delete:$id:comments">Delete Permanently</a></td></tr>
<tr><td width="20%">Comment:</td><td><textarea name="post" rows="5" cols="30">{{post}}</textarea></td></tr>
</table>
<input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" />
</form>
END;
    
    $page = parsetemplate($page, $row);
    admindisplay($page, "Edit Comment");
    
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
        if ($count == 1) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">".$row["id"]."</td><td style=\"background-color: #eeeeee;\"><a href=\"admin.php?do=editgeneral:".$row["id"]."\">".$row["title"]."</a></td></tr>\n"; $count = 2; }
        else { $page .= "<tr><td width=\"8%\" style=\"background-color: #ffffff;\">".$row["id"]."</td><td style=\"background-color: #ffffff;\"><a href=\"admin.php?do=editgeneral:".$row["id"]."\">".$row["title"]."</a></td></tr>\n"; $count = 1; }
    }
    if (mysql_num_rows($query) == 0) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">No posts found.</td></tr>\n"; }
    $page .= "</table>";
    admindisplay($page, "Edit Staff Forum");
    
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
            admindisplay("Post updated.","Edit Staff Forum");
        } else {
            admindisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Staff Forum");
        }        
        
    }   
        
    
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "general");
    $row = mysql_fetch_array($query);

$page = <<<END
<b><u>Edit Staff Forum</u></b><br /><br />
<form action="admin.php?do=editgeneral:$id" method="post">
<table width="90%">
<tr><td width="20%">Post ID:</td><td>{{id}} - <a href="admin.php?do=delete:$id:general">Delete</a></td></tr>
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
    admindisplay($page, "Edit Staff Forum");
    
}

function func_delete($id,$table) { 

	$query = doquery("DELETE FROM {{table}} WHERE id='$id'", $table);
	if($query===true){
		admindisplay("Post deleted successfully.","Delete Post or Thread");
	}else{
       admindisplay('The delete was <b>NOT</b> successful<br><br>Please go back and try again.',"Edit Staff Forum"); 
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
        if ($count == 1) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">".$row["id"]."</td><td style=\"background-color: #eeeeee;\"><a href=\"admin.php?do=editsupport:".$row["id"]."\">".$row["title"]."</a></td></tr>\n"; $count = 2; }
        else { $page .= "<tr><td width=\"8%\" style=\"background-color: #ffffff;\">".$row["id"]."</td><td style=\"background-color: #ffffff;\"><a href=\"admin.php?do=editsupport:".$row["id"]."\">".$row["title"]."</a></td></tr>\n"; $count = 1; }
    }
    if (mysql_num_rows($query) == 0) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">No posts found.</td></tr>\n"; }
    $page .= "</table>";
    admindisplay($page, "Edit Support Forum");
    
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
            admindisplay("Post updated.","Edit Support Forum");
        } else {
            admindisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Support Forum");
        }        
        
    }   
        
    
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "support");
    $row = mysql_fetch_array($query);


$page = <<<END
<b><u>Edit Support Forum</u></b><br /><br />
<form action="admin.php?do=editsupport:$id" method="post">
<table width="90%">
<tr><td width="20%">Post ID:</td><td>{{id}} - <a href="admin.php?do=delete:$id:support">Delete</a></td></tr>
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
    admindisplay($page, "Edit Support Forum");
    
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
        if ($count == 1) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">".$row["id"]."</td><td style=\"background-color: #eeeeee;\"><a href=\"admin.php?do=editsuggestion:".$row["id"]."\">".$row["title"]."</a></td></tr></tr>\n"; $count = 2; }
        else { $page .= "<tr><td width=\"8%\" style=\"background-color: #ffffff;\">".$row["id"]."</td><td style=\"background-color: #ffffff;\"><a href=\"admin.php?do=editsuggestion:".$row["id"]."\">".$row["title"]."</a></td></tr>\n"; $count = 1; }
    }
    if (mysql_num_rows($query) == 0) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">No posts found.</td></tr>\n"; }
    $page .= "</table>";
    admindisplay($page, "Edit Suggestion Forum");
    
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
            admindisplay("Post updated.","Edit Suggestion Forum");
        } else {
            admindisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Suggestion Forum");
        }        
        
    }   
        
    
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "suggestions");
    $row = mysql_fetch_array($query);

$page = <<<END
<b><u>Edit Suggestion Forum</u></b><br /><br />
<form action="admin.php?do=editsuggestion:$id" method="post">
<table width="90%">
<tr><td width="20%">Post ID:</td><td>{{id}} - <a href="admin.php?do=delete:$id:suggestions">Delete</a></td></tr>
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
    admindisplay($page, "Edit Suggestion Forum");
    
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
        if ($count == 1) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">".$row["id"]."</td><td style=\"background-color: #eeeeee;\"><a href=\"admin.php?do=editmarket:".$row["id"]."\">".$row["title"]."</a></td></tr></tr>\n"; $count = 2; }
        else { $page .= "<tr><td width=\"8%\" style=\"background-color: #ffffff;\">".$row["id"]."</td><td style=\"background-color: #ffffff;\"><a href=\"admin.php?do=editmarket:".$row["id"]."\">".$row["title"]."</a></td></tr>\n"; $count = 1; }
    }
    if (mysql_num_rows($query) == 0) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">No posts found.</td></tr>\n"; }
    $page .= "</table>";
    admindisplay($page, "Edit Market Forum");
    
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
            admindisplay("Post updated.","Edit Market Forum");
        } else {
            admindisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Market Forum");
        }        
        
    }   
        
    
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "marketforum");
    $row = mysql_fetch_array($query);

$page = <<<END
<b><u>Edit Market Forum</u></b><br /><br />
<form action="admin.php?do=editmarket:$id" method="post">
<table width="90%">
<tr><td width="20%">Post ID:</td><td>{{id}} - <a href="admin.php?do=delete:$id:marketforum">Delete</a></td></tr>
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
    admindisplay($page, "Edit Market Forum");
    
}

function gforum() {
    
    global $controlrow;
    
    $statquery = doquery("SELECT * FROM {{table}} ORDER BY id DESC LIMIT 1", "gforum");
    $statrow = mysql_fetch_array($statquery);
    
    $query = doquery("SELECT id,title,guildname FROM {{table}} ORDER BY id", "gforum");
    $page = "<b><u>Edit Guild Forum</u></b><br />";
    
    $page .= "Click a posts name to edit it.<br /><br /><table width=\"50%\">\n";
    $count = 1;
    while ($row = mysql_fetch_array($query)) {
        if ($count == 1) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">".$row["id"]."</td><td style=\"background-color: #eeeeee;\"><a href=\"admin.php?do=editgforum:".$row["id"]."\">".$row["title"]."</a></td><td style=\"background-color: #eeeeee;\"><a href=\"admin.php?do=editgforum:".$row["id"]."\">".$row["guildname"]."</a></td></tr>\n"; $count = 2; }
        else { $page .= "<tr><td width=\"8%\" style=\"background-color: #ffffff;\">".$row["id"]."</td><td style=\"background-color: #ffffff;\"><a href=\"admin.php?do=editgforum:".$row["id"]."\">".$row["title"]."</a></td><td style=\"background-color: #ffffff;\"><a href=\"admin.php?do=editgforum:".$row["id"]."\">".$row["guildname"]."</a></td></tr>\n"; $count = 1; }
    }
    if (mysql_num_rows($query) == 0) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">No posts found.</td></tr>\n"; }
    $page .= "</table>";
    admindisplay($page, "Edit Guild Forum");
    
}

function editgforum($id) {
    
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
            $query = doquery("UPDATE {{table}} SET postdate='$postdate',guildname='$guildname',newpostdate='$newpostdate',author='$author',parent='$parent',replies='$replies',title='$title',content='$content',close='$close',pin='$pin' WHERE id='$id' LIMIT 1", "gforum");
            admindisplay("Post updated.","Edit Guild Forum");
        } else {
            admindisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Guild Forum");
        }        
        
    }   
        
    
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "gforum");
    $row = mysql_fetch_array($query);

$page = <<<END
<b><u>Edit Guild Forum</u></b><br /><br />
<form action="admin.php?do=editgforum:$id" method="post">
<table width="90%">
<tr><td width="20%">Post ID:</td><td>{{id}} - <a href="admin.php?do=delete:$id:gforum">Delete</a></td></tr>
<tr><td width="20%">Post Date:</td><td><input type="text" name="postdate" size="20" maxlength="20" value="{{postdate}}" /><br>Please dont edit this unless its neccessary</td></tr>
<tr><td width="20%">New Post Date:</td><td><input type="text" name="newpostdate" size="20" maxlength="20" value="{{newpostdate}}" /><br>Please dont edit this unless its neccessary</td></tr>
<tr><td width="20%">Author:</td><td><input type="text" name="author" size="30" maxlength="30" value="{{author}}" /><br>Please dont edit this unless its neccessary</td></tr>
<tr><td width="20%">Guild Name:</td><td><input type="text" name="guildname" size="30" maxlength="30" value="{{guildname}}" /></td></tr>
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
    admindisplay($page, "Edit Guild Forum");
    
}


function guilds() {
    
    global $controlrow;
    
    $statquery = doquery("SELECT * FROM {{table}} ORDER BY id DESC LIMIT 1", "guilds");
    $statrow = mysql_fetch_array($statquery);
    
    $query = doquery("SELECT id,name FROM {{table}} ORDER BY id", "guilds");
    $page = "<b><u>Edit Guilds</u></b><br />";
    
    $page .= "Click a Guilds name to edit it.<br /><br /><table width=\"50%\">\n";
    $count = 1;
    while ($row = mysql_fetch_array($query)) {
        if ($count == 1) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">".$row["id"]."</td><td style=\"background-color: #eeeeee;\"><a href=\"admin.php?do=editguild:".$row["id"]."\">".$row["name"]."</a></td></tr>\n"; $count = 2; }
        else { $page .= "<tr><td width=\"8%\" style=\"background-color: #ffffff;\">".$row["id"]."</td><td style=\"background-color: #ffffff;\"><a href=\"admin.php?do=editguild:".$row["id"]."\">".$row["name"]."</a></td></tr>\n"; $count = 1; }
    }
    if (mysql_num_rows($query) == 0) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">No towns found.</td></tr>\n"; }
    $page .= "</table>";
    admindisplay($page, "Edit Guild");
    
}

function editguild($id) {
    
    if (isset($_POST["submit"])) {
        
        extract($_POST);
        $errors = 0;

        
        if ($errors == 0) { 
            $query = doquery("UPDATE {{table}} SET members='$members',name='$name',tag='$tag',dscales='$dscales',password='$password',joincost='$joincost',private='$private',level='$level',experience='$experience',founder='$founder',rank1name='$rank1name',rank2name='$rank2name',rank3name='$rank3name',rank4name='$rank4name',rank5name='$rank5name',rank6name='$rank6name' WHERE id='$id' LIMIT 1", "guilds");
            admindisplay("Guild updated.","Edit Guilds");
        } else {
            admindisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Guilds");
        }        
        
    }   
        
    
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "guilds");
    $row = mysql_fetch_array($query);

$page = <<<END
<b><u>Edit Guilds</u></b><br /><br />
<form action="admin.php?do=editguild:$id" method="post">
<table width="90%">
<tr><td width="20%">Guild ID:</td><td>{{id}}</td></tr>
<tr><td width="20%">Guild Name:</td><td><input type="text" name="name" size="30" maxlength="30" value="{{name}}" /></td></tr>
<tr><td width="20%">Guild Tag:</td><td><input type="text" name="tag" size="5" maxlength="5" value="{{tag}}" /></td></tr>
<tr><td width="20%">Members:</td><td><input type="text" name="members" size="10" maxlength="10" value="{{members}}" /><br /><span class="small">Total Members</span></td></tr>

<tr><td width="20%">Dragon Scales:</td><td><input type="text" name="dscales" size="10" maxlength="10" value="{{dscales}}" /><br /><span class="small">Total Members</span></td></tr>
<tr><td width="20%">Password:</td><td><input type="text" name="password" size="12" maxlength="12" value="{{password}}" /><br /><span class="small">Password to Join</span></td></tr>
<tr><td width="20%">Join Cost:</td><td><input type="text" name="joincost" size="5" maxlength="10" value="{{joincost}}" /><br /><span class="small">Joining Cost</span></td></tr>
<tr><td width="20%">Password On/Off:</td><td><input type="text" name="private" size="10" maxlength="10" value="{{private}}" /><br /><span class="small">0 = Off / 1 = On </span></td></tr>
<tr><td width="20%">Level:</td><td><input type="text" name="level" size="5" maxlength="5" value="{{level}}" /><br /><span class="small">Max gold gained from defeating monster.</span></td></tr>
<tr><td width="20%">Experience:</td><td><input type="text" name="experience" size="5" maxlength="10" value="{{experience}}" /><br /><span class="small">Max gold gained from defeating monster.</span></td></tr>
<tr><td width="20%">Guild Founder:</td><td><input type="text" name="founder" size="30" maxlength="30" value="{{founder}}" /></td></tr>
<tr><td width="20%">Rank 1:</td><td><input type="text" name="rank1name" size="20" maxlength="20" value="{{rank1name}}" /></td></tr>
<tr><td width="20%">Rank 2:</td><td><input type="text" name="rank2name" size="20" maxlength="20" value="{{rank2name}}" /></td></tr>
<tr><td width="20%">Rank 3:</td><td><input type="text" name="rank3name" size="20" maxlength="20" value="{{rank3name}}" /></td></tr>
<tr><td width="20%">Rank 4:</td><td><input type="text" name="rank4name" size="20" maxlength="20" value="{{rank4name}}" /></td></tr>
<tr><td width="20%">Rank 5:</td><td><input type="text" name="rank5name" size="20" maxlength="20" value="{{rank5name}}" /></td></tr>
<tr><td width="20%">Rank 6:</td><td><input type="text" name="rank6name" size="20" maxlength="20" value="{{rank6name}}" /></td></tr>
</table>
<input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" />
</form>
END;
    
    $page = parsetemplate($page, $row);
    admindisplay($page, "Edit Guilds");
    
}

function strongholds() {
    
    global $controlrow;
    
    $statquery = doquery("SELECT * FROM {{table}} ORDER BY id DESC LIMIT 1", "strongholds");
    $statrow = mysql_fetch_array($statquery);
    
    $query = doquery("SELECT id,guildname FROM {{table}} ORDER BY id", "strongholds");
    $page = "<b><u>Edit Strongholds</u></b><br />";
    
    $page .= "Click a Strongholds name to edit it.<br /><br /><table width=\"50%\">\n";
    $count = 1;
    while ($row = mysql_fetch_array($query)) {
        if ($count == 1) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">".$row["id"]."</td><td style=\"background-color: #eeeeee;\"><a href=\"admin.php?do=editstronghold:".$row["id"]."\">".$row["guildname"]."</a></td></tr>\n"; $count = 2; }
        else { $page .= "<tr><td width=\"8%\" style=\"background-color: #ffffff;\">".$row["id"]."</td><td style=\"background-color: #ffffff;\"><a href=\"admin.php?do=editstronghold:".$row["id"]."\">".$row["guildname"]."</a></td></tr>\n"; $count = 1; }
    }
    if (mysql_num_rows($query) == 0) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">No towns found.</td></tr>\n"; }
    $page .= "</table>";
    admindisplay($page, "Edit Stronghold");
    
}

function editstronghold($id) {
    
    if (isset($_POST["submit"])) {
        
        extract($_POST);
        $errors = 0;

        
        if ($errors == 0) { 
            $query = doquery("UPDATE {{table}} SET latitude='$latitude',longitude='$longitude',guildname='$guildname',founder='$founder',ruined='$ruined',armor='$armor',magic='$magic',weaponry='$weaponry',armorlevel='$armorlevel',magiclevel='$magiclevel',weaponrylevel='$weaponrylevel',spells='$spells',gold='$gold',currenthp='$currenthp',maxhp='$maxhp',currentmp='$currentmp',maxmp='$maxmp',experience='$experience',level='$level',productivity='$productivity',snails='$snails',kelplings='$kelplings',minnows='$minnows' WHERE id='$id' LIMIT 1", "strongholds");
            admindisplay("Stronghold updated.","Edit Strongholds");
        } else {
            admindisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Strongholds");
        }        
        
    }   
        
    
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "strongholds");
    $row = mysql_fetch_array($query);

$page = <<<END
<b><u>Edit Strongholds</u></b><br /><br />
<form action="admin.php?do=editstronghold:$id" method="post">
<table width="90%">
<tr><td width="20%">Stronghold ID:</td><td>{{id}}</td></tr>
<tr><td width="20%">Guild ID:</td><td>{{guildid}}</td></tr>
<tr><td width="20%">Latitude:</td><td><input type="text" name="latitude" size="5" maxlength="5" value="{{latitude}}" /></td></tr>
<tr><td width="20%">Longitude:</td><td><input type="text" name="longitude" size="5" maxlength="5" value="{{longitude}}" /></td></tr>
<tr><td width="20%">Guild Name:</td><td><input type="text" name="guildname" size="30" maxlength="30" value="{{guildname}}" /></td></tr>
<tr><td width="20%">Founder:</td><td><input type="text" name="founder" size="30" maxlength="30" value="{{founder}}" /></td></tr>
<tr><td width="20%">Ruined:</td><td><input type="text" name="ruined" size="5" maxlength="5" value="{{ruined}}" /></td></tr>
<tr><td width="20%">Armor:</td><td><input type="text" name="armor" size="7" maxlength="7" value="{{armor}}" /></td></tr>
<tr><td width="20%">Magic:</td><td><input type="text" name="magic" size="7" maxlength="7" value="{{magic}}" /></td></tr>
<tr><td width="20%">Weaponry:</td><td><input type="text" name="weaponry" size="7" maxlength="7" value="{{weaponry}}" /></td></tr>
<tr><td width="20%">Armor Level:</td><td><input type="text" name="armorlevel" size="5" maxlength="5" value="{{armorlevel}}" /></td></tr>
<tr><td width="20%">Magic Level:</td><td><input type="text" name="magiclevel" size="5" maxlength="5" value="{{magiclevel}}" /></td></tr>
<tr><td width="20%">Weaponry Level:</td><td><input type="text" name="weaponrylevel" size="5" maxlength="5" value="{{weaponrylevel}}" /></td></tr>
<tr><td width="20%">Spells:</td><td><input type="text" name="spells" size="5" maxlength="5" value="{{spells}}" /></td></tr>
<tr><td width="20%">Strongholds Gold:</td><td><input type="text" name="gold" size="10" maxlength="10" value="{{gold}}" /></td></tr>
<tr><td width="20%">Current HP:</td><td><input type="text" name="currenthp" size="7" maxlength="7" value="{{currenthp}}" /></td></tr>
<tr><td width="20%">Max HP:</td><td><input type="text" name="maxhp" size="7" maxlength="7" value="{{maxhp}}" /></td></tr>
<tr><td width="20%">Current MP:</td><td><input type="text" name="currentmp" size="7" maxlength="7" value="{{currentmp}}" /></td></tr>
<tr><td width="20%">Max MP:</td><td><input type="text" name="maxmp" size="7" maxlength="7" value="{{maxmp}}" /></td></tr>
<tr><td width="20%">Experience:</td><td><input type="text" name="experience" size="6" maxlength="6" value="{{experience}}" /></td></tr>
<tr><td width="20%">Level:</td><td><input type="text" name="level" size="5" maxlength="5" value="{{level}}" /></td></tr>
<tr><td width="20%">Productivity:</td><td><input type="text" name="productivity" size="6" maxlength="6" value="{{productivity}}" /></td></tr>
<tr><td width="20%">Vipers:</td><td><input type="text" name="snails" size="7" maxlength="7" value="{{snails}}" /></td></tr>
<tr><td width="20%">Golems:</td><td><input type="text" name="kelplings" size="7" maxlength="7" value="{{kelplings}}" /></td></tr>
<tr><td width="20%">Gargoyles:</td><td><input type="text" name="minnows" size="7" maxlength="7" value="{{minnows}}" /></td></tr>
</table>
</table>
</table>
</table>
<input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" />
</form>
END;
    
    $page = parsetemplate($page, $row);
    admindisplay($page, "Edit Strongholds");
    
}

function editlevel() {

    if (!isset($_POST["level"])) { admindisplay("No level to edit.", "Edit Levels"); die(); }
    $id = $_POST["level"];
    
    if (isset($_POST["submit"])) {
        
        extract($_POST);
        $errors = 0;
        $errorlist = "";
        if ($_POST["one_exp"] == "") { $errors++; $errorlist .= "Class 1 Experience is required.<br />"; }
        if ($_POST["one_hp"] == "") { $errors++; $errorlist .= "Class 1 HP is required.<br />"; }
        if ($_POST["one_mp"] == "") { $errors++; $errorlist .= "Class 1 MP is required.<br />"; }
        if ($_POST["one_tp"] == "") { $errors++; $errorlist .= "Class 1 TP is required.<br />"; }
        if ($_POST["one_strength"] == "") { $errors++; $errorlist .= "Class 1 Strength is required.<br />"; }
        if ($_POST["one_dexterity"] == "") { $errors++; $errorlist .= "Class 1 Dexterity is required.<br />"; }
        if ($_POST["one_spells"] == "") { $errors++; $errorlist .= "Class 1 Spells is required.<br />"; }
        if (!is_numeric($_POST["one_exp"])) { $errors++; $errorlist .= "Class 1 Experience must be a number.<br />"; }
        if (!is_numeric($_POST["one_hp"])) { $errors++; $errorlist .= "Class 1 HP must be a number.<br />"; }
        if (!is_numeric($_POST["one_mp"])) { $errors++; $errorlist .= "Class 1 MP must be a number.<br />"; }
        if (!is_numeric($_POST["one_tp"])) { $errors++; $errorlist .= "Class 1 TP must be a number.<br />"; }
        if (!is_numeric($_POST["one_strength"])) { $errors++; $errorlist .= "Class 1 Strength must be a number.<br />"; }
        if (!is_numeric($_POST["one_dexterity"])) { $errors++; $errorlist .= "Class 1 Dexterity must be a number.<br />"; }
        if (!is_numeric($_POST["one_spells"])) { $errors++; $errorlist .= "Class 1 Spells must be a number.<br />"; }

        if ($_POST["two_exp"] == "") { $errors++; $errorlist .= "Class 2 Experience is required.<br />"; }
        if ($_POST["two_hp"] == "") { $errors++; $errorlist .= "Class 2 HP is required.<br />"; }
        if ($_POST["two_mp"] == "") { $errors++; $errorlist .= "Class 2 MP is required.<br />"; }
        if ($_POST["two_tp"] == "") { $errors++; $errorlist .= "Class 2 TP is required.<br />"; }
        if ($_POST["two_strength"] == "") { $errors++; $errorlist .= "Class 2 Strength is required.<br />"; }
        if ($_POST["two_dexterity"] == "") { $errors++; $errorlist .= "Class 2 Dexterity is required.<br />"; }
        if ($_POST["two_spells"] == "") { $errors++; $errorlist .= "Class 2 Spells is required.<br />"; }
        if (!is_numeric($_POST["two_exp"])) { $errors++; $errorlist .= "Class 2 Experience must be a number.<br />"; }
        if (!is_numeric($_POST["two_hp"])) { $errors++; $errorlist .= "Class 2 HP must be a number.<br />"; }
        if (!is_numeric($_POST["two_mp"])) { $errors++; $errorlist .= "Class 2 MP must be a number.<br />"; }
        if (!is_numeric($_POST["two_tp"])) { $errors++; $errorlist .= "Class 2 TP must be a number.<br />"; }
        if (!is_numeric($_POST["two_strength"])) { $errors++; $errorlist .= "Class 2 Strength must be a number.<br />"; }
        if (!is_numeric($_POST["two_dexterity"])) { $errors++; $errorlist .= "Class 2 Dexterity must be a number.<br />"; }
        if (!is_numeric($_POST["two_spells"])) { $errors++; $errorlist .= "Class 2 Spells must be a number.<br />"; }
                
        if ($_POST["three_exp"] == "") { $errors++; $errorlist .= "Class 3 Experience is required.<br />"; }
        if ($_POST["three_hp"] == "") { $errors++; $errorlist .= "Class 3 HP is required.<br />"; }
        if ($_POST["three_mp"] == "") { $errors++; $errorlist .= "Class 3 MP is required.<br />"; }
        if ($_POST["three_tp"] == "") { $errors++; $errorlist .= "Class 3 TP is required.<br />"; }
        if ($_POST["three_strength"] == "") { $errors++; $errorlist .= "Class 3 Strength is required.<br />"; }
        if ($_POST["three_dexterity"] == "") { $errors++; $errorlist .= "Class 3 Dexterity is required.<br />"; }
        if ($_POST["three_spells"] == "") { $errors++; $errorlist .= "Class 3 Spells is required.<br />"; }
        if (!is_numeric($_POST["three_exp"])) { $errors++; $errorlist .= "Class 3 Experience must be a number.<br />"; }
        if (!is_numeric($_POST["three_hp"])) { $errors++; $errorlist .= "Class 3 HP must be a number.<br />"; }
        if (!is_numeric($_POST["three_mp"])) { $errors++; $errorlist .= "Class 3 MP must be a number.<br />"; }
        if (!is_numeric($_POST["three_tp"])) { $errors++; $errorlist .= "Class 3 TP must be a number.<br />"; }
        if (!is_numeric($_POST["three_strength"])) { $errors++; $errorlist .= "Class 3 Strength must be a number.<br />"; }
        if (!is_numeric($_POST["three_dexterity"])) { $errors++; $errorlist .= "Class 3 Dexterity must be a number.<br />"; }
        if (!is_numeric($_POST["three_spells"])) { $errors++; $errorlist .= "Class 3 Spells must be a number.<br />"; }

        if ($_POST["four_exp"] == "") { $errors++; $errorlist .= "Class 4 Experience is required.<br />"; }
        if ($_POST["four_hp"] == "") { $errors++; $errorlist .= "Class 4 HP is required.<br />"; }
        if ($_POST["four_mp"] == "") { $errors++; $errorlist .= "Class 4 MP is required.<br />"; }
        if ($_POST["four_tp"] == "") { $errors++; $errorlist .= "Class 4 TP is required.<br />"; }
        if ($_POST["four_strength"] == "") { $errors++; $errorlist .= "Class 4 Strength is required.<br />"; }
        if ($_POST["four_dexterity"] == "") { $errors++; $errorlist .= "Class 4 Dexterity is required.<br />"; }
        if ($_POST["four_spells"] == "") { $errors++; $errorlist .= "Class 4 Spells is required.<br />"; }
        if (!is_numeric($_POST["four_exp"])) { $errors++; $errorlist .= "Class 4 Experience must be a number.<br />"; }
        if (!is_numeric($_POST["four_hp"])) { $errors++; $errorlist .= "Class 4 HP must be a number.<br />"; }

        if (!is_numeric($_POST["four_mp"])) { $errors++; $errorlist .= "Class 4 MP must be a number.<br />"; }
        if (!is_numeric($_POST["four_tp"])) { $errors++; $errorlist .= "Class 4 TP must be a number.<br />"; }
        if (!is_numeric($_POST["four_strength"])) { $errors++; $errorlist .= "Class 4 Strength must be a number.<br />"; }
        if (!is_numeric($_POST["four_dexterity"])) { $errors++; $errorlist .= "Class 4 Dexterity must be a number.<br />"; }
        if (!is_numeric($_POST["four_spells"])) { $errors++; $errorlist .= "Class 4 Spells must be a number.<br />"; }

        if ($_POST["five_exp"] == "") { $errors++; $errorlist .= "Class 5 Experience is required.<br />"; }
        if ($_POST["five_hp"] == "") { $errors++; $errorlist .= "Class 5 HP is required.<br />"; }
        if ($_POST["five_mp"] == "") { $errors++; $errorlist .= "Class 5 MP is required.<br />"; }
        if ($_POST["five_tp"] == "") { $errors++; $errorlist .= "Class 5 TP is required.<br />"; }
        if ($_POST["five_strength"] == "") { $errors++; $errorlist .= "Class 5 Strength is required.<br />"; }
        if ($_POST["five_dexterity"] == "") { $errors++; $errorlist .= "Class 5 Dexterity is required.<br />"; }
        if ($_POST["five_spells"] == "") { $errors++; $errorlist .= "Class 5 Spells is required.<br />"; }
        if (!is_numeric($_POST["five_exp"])) { $errors++; $errorlist .= "Class 5 Experience must be a number.<br />"; }
        if (!is_numeric($_POST["five_hp"])) { $errors++; $errorlist .= "Class 5 HP must be a number.<br />"; }
        if (!is_numeric($_POST["five_mp"])) { $errors++; $errorlist .= "Class 5 MP must be a number.<br />"; }
        if (!is_numeric($_POST["five_tp"])) { $errors++; $errorlist .= "Class 5 TP must be a number.<br />"; }
        if (!is_numeric($_POST["five_strength"])) { $errors++; $errorlist .= "Class 5 Strength must be a number.<br />"; }
        if (!is_numeric($_POST["five_dexterity"])) { $errors++; $errorlist .= "Class 5 Dexterity must be a number.<br />"; }
        if (!is_numeric($_POST["five_spells"])) { $errors++; $errorlist .= "Class 5 Spells must be a number.<br />"; }

        if ($_POST["six_exp"] == "") { $errors++; $errorlist .= "Class 6 Experience is required.<br />"; }
        if ($_POST["six_hp"] == "") { $errors++; $errorlist .= "Class 6 HP is required.<br />"; }
        if ($_POST["six_mp"] == "") { $errors++; $errorlist .= "Class 6 MP is required.<br />"; }
        if ($_POST["six_tp"] == "") { $errors++; $errorlist .= "Class 6 TP is required.<br />"; }
        if ($_POST["six_strength"] == "") { $errors++; $errorlist .= "Class 6 Strength is required.<br />"; }
        if ($_POST["six_dexterity"] == "") { $errors++; $errorlist .= "Class 6 Dexterity is required.<br />"; }
        if ($_POST["six_spells"] == "") { $errors++; $errorlist .= "Class 6 Spells is required.<br />"; }
        if (!is_numeric($_POST["six_exp"])) { $errors++; $errorlist .= "Class 6 Experience must be a number.<br />"; }
        if (!is_numeric($_POST["six_hp"])) { $errors++; $errorlist .= "Class 6 HP must be a number.<br />"; }
        if (!is_numeric($_POST["six_mp"])) { $errors++; $errorlist .= "Class 6 MP must be a number.<br />"; }
        if (!is_numeric($_POST["six_tp"])) { $errors++; $errorlist .= "Class 6 TP must be a number.<br />"; }
        if (!is_numeric($_POST["six_strength"])) { $errors++; $errorlist .= "Class 6 Strength must be a number.<br />"; }
        if (!is_numeric($_POST["six_dexterity"])) { $errors++; $errorlist .= "Class 6 Dexterity must be a number.<br />"; }
        if (!is_numeric($_POST["six_spells"])) { $errors++; $errorlist .= "Class 6 Spells must be a number.<br />"; }
        
        if ($_POST["seven_exp"] == "") { $errors++; $errorlist .= "Class 7 Experience is required.<br />"; }
        if ($_POST["seven_hp"] == "") { $errors++; $errorlist .= "Class 7 HP is required.<br />"; }
        if ($_POST["seven_mp"] == "") { $errors++; $errorlist .= "Class 7 MP is required.<br />"; }
        if ($_POST["seven_tp"] == "") { $errors++; $errorlist .= "Class 7 TP is required.<br />"; }
        if ($_POST["seven_strength"] == "") { $errors++; $errorlist .= "Class 7 Strength is required.<br />"; }
        if ($_POST["seven_dexterity"] == "") { $errors++; $errorlist .= "Class 7 Dexterity is required.<br />"; }
        if ($_POST["seven_spells"] == "") { $errors++; $errorlist .= "Class 7 Spells is required.<br />"; }
        if (!is_numeric($_POST["seven_exp"])) { $errors++; $errorlist .= "Class 7 Experience must be a number.<br />"; }
        if (!is_numeric($_POST["seven_hp"])) { $errors++; $errorlist .= "Class 7 HP must be a number.<br />"; }
        if (!is_numeric($_POST["seven_mp"])) { $errors++; $errorlist .= "Class 7 MP must be a number.<br />"; }
        if (!is_numeric($_POST["seven_tp"])) { $errors++; $errorlist .= "Class 7 TP must be a number.<br />"; }
        if (!is_numeric($_POST["seven_strength"])) { $errors++; $errorlist .= "Class 7 Strength must be a number.<br />"; }
        if (!is_numeric($_POST["seven_dexterity"])) { $errors++; $errorlist .= "Class 7 Dexterity must be a number.<br />"; }
        if (!is_numeric($_POST["seven_spells"])) { $errors++; $errorlist .= "Class 7 Spells must be a number.<br />"; }

if ($errors == 0) { 
$updatequery = <<<END
UPDATE {{table}} SET
1_exp='$one_exp', 1_hp='$one_hp', 1_mp='$one_mp', 1_tp='$one_tp', 1_strength='$one_strength', 1_dexterity='$one_dexterity', 1_spells='$one_spells',
2_exp='$two_exp', 2_hp='$two_hp', 2_mp='$two_mp', 2_tp='$two_tp', 2_strength='$two_strength', 2_dexterity='$two_dexterity', 2_spells='$two_spells',
3_exp='$three_exp', 3_hp='$three_hp', 3_mp='$three_mp', 3_tp='$three_tp', 3_strength='$three_strength', 3_dexterity='$three_dexterity', 3_spells='$three_spells',
4_exp='$four_exp', 4_hp='$four_hp', 4_mp='$four_mp', 4_tp='$four_tp', 4_strength='$four_strength', 4_dexterity='$four_dexterity', 4_spells='$four_spells',
5_exp='$five_exp', 5_hp='$five_hp', 5_mp='$five_mp', 5_tp='$five_tp', 5_strength='$five_strength', 5_dexterity='$five_dexterity', 5_spells='$five_spells'
6_exp='$six_exp', 6_hp='$six_hp', 6_mp='$six_mp', 6_tp='$six_tp', 6_strength='$six_strength', 6_dexterity='$six_dexterity', 6_spells='$six_spells',
7_exp='$seven_exp', 7_hp='$seven_hp', 7_mp='$seven_mp', 7_tp='$seven_tp', 7_strength='$seven_strength', 7_dexterity='$seven_dexterity', 7_spells='$seven_spells'

WHERE id='$id' LIMIT 1
END;
			$query = doquery($updatequery, "levels");
            admindisplay("Level updated.","Edit Levels");
        } else {
            admindisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Spells");
        }        
        
    }   
        
    
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "levels");
    $row = mysql_fetch_array($query);
    global $controlrow;
    $class1name = $controlrow["class1name"];
    $class2name = $controlrow["class2name"];
    $class3name = $controlrow["class3name"];
    $class4name = $controlrow["class4name"];
    $class5name = $controlrow["class5name"];
    $class6name = $controlrow["class6name"];
    $class7name = $controlrow["class7name"];

$page = <<<END
<b><u>Edit Levels</u></b><br /><br />
Experience values for each level should be the cumulative total amount of experience up to this point. All other values should be only the new amount to add this level.<br /><br />
<form action="admin.php?do=editlevel" method="post">
<input type="hidden" name="level" value="$id" />
<table width="90%">
<tr><td width="20%">ID:</td><td>{{id}}</td></tr>

<tr><td colspan="2" style="background-color:#cccccc;">&nbsp;</td></tr>

<tr><td width="20%">$class1name Experience:</td><td><input type="text" name="one_exp" size="10" maxlength="8" value="{{1_exp}}" /></td></tr>
<tr><td width="20%">$class1name HP:</td><td><input type="text" name="one_hp" size="5" maxlength="5" value="{{1_hp}}" /></td></tr>
<tr><td width="20%">$class1name MP:</td><td><input type="text" name="one_mp" size="5" maxlength="5" value="{{1_mp}}" /></td></tr>
<tr><td width="20%">$class1name TP:</td><td><input type="text" name="one_tp" size="5" maxlength="5" value="{{1_tp}}" /></td></tr>
<tr><td width="20%">$class1name Strength:</td><td><input type="text" name="one_strength" size="5" maxlength="5" value="{{1_strength}}" /></td></tr>
<tr><td width="20%">$class1name Dexterity:</td><td><input type="text" name="one_dexterity" size="5" maxlength="5" value="{{1_dexterity}}" /></td></tr>
<tr><td width="20%">$class1name Spells:</td><td><input type="text" name="one_spells" size="5" maxlength="3" value="{{1_spells}}" /></td></tr>

<tr><td colspan="2" style="background-color:#cccccc;">&nbsp;</td></tr>

<tr><td width="20%">$class2name Experience:</td><td><input type="text" name="two_exp" size="10" maxlength="8" value="{{2_exp}}" /></td></tr>
<tr><td width="20%">$class2name HP:</td><td><input type="text" name="two_hp" size="5" maxlength="5" value="{{2_hp}}" /></td></tr>
<tr><td width="20%">$class2name MP:</td><td><input type="text" name="two_mp" size="5" maxlength="5" value="{{2_mp}}" /></td></tr>
<tr><td width="20%">$class2name TP:</td><td><input type="text" name="two_tp" size="5" maxlength="5" value="{{2_tp}}" /></td></tr>
<tr><td width="20%">$class2name Strength:</td><td><input type="text" name="two_strength" size="5" maxlength="5" value="{{2_strength}}" /></td></tr>
<tr><td width="20%">$class2name Dexterity:</td><td><input type="text" name="two_dexterity" size="5" maxlength="5" value="{{2_dexterity}}" /></td></tr>
<tr><td width="20%">$class2name Spells:</td><td><input type="text" name="two_spells" size="5" maxlength="3" value="{{2_spells}}" /></td></tr>

<tr><td colspan="2" style="background-color:#cccccc;">&nbsp;</td></tr>

<tr><td width="20%">$class3name Experience:</td><td><input type="text" name="three_exp" size="10" maxlength="8" value="{{3_exp}}" /></td></tr>
<tr><td width="20%">$class3name HP:</td><td><input type="text" name="three_hp" size="5" maxlength="5" value="{{3_hp}}" /></td></tr>
<tr><td width="20%">$class3name MP:</td><td><input type="text" name="three_mp" size="5" maxlength="5" value="{{3_mp}}" /></td></tr>
<tr><td width="20%">$class3name TP:</td><td><input type="text" name="three_tp" size="5" maxlength="5" value="{{3_tp}}" /></td></tr>
<tr><td width="20%">$class3name Strength:</td><td><input type="text" name="three_strength" size="5" maxlength="5" value="{{3_strength}}" /></td></tr>
<tr><td width="20%">$class3name Dexterity:</td><td><input type="text" name="three_dexterity" size="5" maxlength="5" value="{{3_dexterity}}" /></td></tr>
<tr><td width="20%">$class3name Spells:</td><td><input type="text" name="three_spells" size="5" maxlength="3" value="{{3_spells}}" /></td></tr>

<tr><td colspan="2" style="background-color:#cccccc;">&nbsp;</td></tr>

<tr><td width="20%">$class4name Experience:</td><td><input type="text" name="four_exp" size="10" maxlength="8" value="{{4_exp}}" /></td></tr>
<tr><td width="20%">$class4name HP:</td><td><input type="text" name="four_hp" size="5" maxlength="5" value="{{4_hp}}" /></td></tr>
<tr><td width="20%">$class4name MP:</td><td><input type="text" name="four_mp" size="5" maxlength="5" value="{{4_mp}}" /></td></tr>
<tr><td width="20%">$class4name TP:</td><td><input type="text" name="four_tp" size="5" maxlength="5" value="{{4_tp}}" /></td></tr>
<tr><td width="20%">$class4name Strength:</td><td><input type="text" name="four_strength" size="5" maxlength="5" value="{{4_strength}}" /></td></tr>
<tr><td width="20%">$class4name Dexterity:</td><td><input type="text" name="four_dexterity" size="5" maxlength="5" value="{{4_dexterity}}" /></td></tr>
<tr><td width="20%">$class4name Spells:</td><td><input type="text" name="four_spells" size="5" maxlength="3" value="{{4_spells}}" /></td></tr>

<tr><td colspan="2" style="background-color:#cccccc;">&nbsp;</td></tr>

<tr><td width="20%">$class5name Experience:</td><td><input type="text" name="five_exp" size="10" maxlength="8" value="{{5_exp}}" /></td></tr>
<tr><td width="20%">$class5name HP:</td><td><input type="text" name="five_hp" size="5" maxlength="5" value="{{5_hp}}" /></td></tr>
<tr><td width="20%">$class5name MP:</td><td><input type="text" name="five_mp" size="5" maxlength="5" value="{{5_mp}}" /></td></tr>
<tr><td width="20%">$class5name TP:</td><td><input type="text" name="five_tp" size="5" maxlength="5" value="{{5_tp}}" /></td></tr>
<tr><td width="20%">$class5name Strength:</td><td><input type="text" name="five_strength" size="5" maxlength="5" value="{{5_strength}}" /></td></tr>
<tr><td width="20%">$class5name Dexterity:</td><td><input type="text" name="five_dexterity" size="5" maxlength="5" value="{{5_dexterity}}" /></td></tr>
<tr><td width="20%">$class5name Spells:</td><td><input type="text" name="five_spells" size="5" maxlength="3" value="{{5_spells}}" /></td></tr>

<tr><td colspan="2" style="background-color:#cccccc;">&nbsp;</td></tr>

<tr><td width="20%">$class6name Experience:</td><td><input type="text" name="six_exp" size="10" maxlength="8" value="{{6_exp}}" /></td></tr>
<tr><td width="20%">$class6name HP:</td><td><input type="text" name="six_hp" size="5" maxlength="5" value="{{6_hp}}" /></td></tr>
<tr><td width="20%">$class6name MP:</td><td><input type="text" name="six_mp" size="5" maxlength="5" value="{{6_mp}}" /></td></tr>
<tr><td width="20%">$class6name TP:</td><td><input type="text" name="six_tp" size="5" maxlength="5" value="{{6_tp}}" /></td></tr>
<tr><td width="20%">$class6name Strength:</td><td><input type="text" name="six_strength" size="5" maxlength="5" value="{{6_strength}}" /></td></tr>
<tr><td width="20%">$class6name Dexterity:</td><td><input type="text" name="six_dexterity" size="5" maxlength="5" value="{{6_dexterity}}" /></td></tr>
<tr><td width="20%">$class6name Spells:</td><td><input type="text" name="six_spells" size="5" maxlength="3" value="{{6_spells}}" /></td></tr>

<tr><td colspan="2" style="background-color:#cccccc;">&nbsp;</td></tr>

<tr><td width="20%">$class7name Experience:</td><td><input type="text" name="seven_exp" size="10" maxlength="8" value="{{7_exp}}" /></td></tr>
<tr><td width="20%">$class7name HP:</td><td><input type="text" name="seven_hp" size="5" maxlength="5" value="{{7_hp}}" /></td></tr>
<tr><td width="20%">$class7name MP:</td><td><input type="text" name="seven_mp" size="5" maxlength="5" value="{{7_mp}}" /></td></tr>
<tr><td width="20%">$class7name TP:</td><td><input type="text" name="seven_tp" size="5" maxlength="5" value="{{7_tp}}" /></td></tr>
<tr><td width="20%">$class7name Strength:</td><td><input type="text" name="seven_strength" size="5" maxlength="5" value="{{7_strength}}" /></td></tr>
<tr><td width="20%">$class7name Dexterity:</td><td><input type="text" name="seven_dexterity" size="5" maxlength="5" value="{{7_dexterity}}" /></td></tr>
<tr><td width="20%">$class7name Spells:</td><td><input type="text" name="seven_spells" size="5" maxlength="3" value="{{7_spells}}" /></td></tr>

</table>
<input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" />
</form>
END;
    
    $page = parsetemplate($page, $row);
    admindisplay($page, "Edit Levels");
    
}

function edituser($id) {
    
    if (isset($_POST["submit"])) {
        
        extract($_POST);
        $errors = 0;
        $errorlist = "";
        if ($email == "") { $errors++; $errorlist .= "Email is required.<br />"; }
        if ($verify == "") { $errors++; $errorlist .= "Verify is required.<br />"; }
        if ($charname == "") { $errors++; $errorlist .= "Character Name is required.<br />"; }
        if ($authlevel == "") { $errors++; $errorlist .= "Auth Level is required.<br />"; }
        if ($latitude == "") { $errors++; $errorlist .= "Latitude is required.<br />"; }
        if ($longitude == "") { $errors++; $errorlist .= "Longitude is required.<br />"; }
        if ($charclass == "") { $errors++; $errorlist .= "Character Class is required.<br />"; }
        if ($currentaction == "") { $errors++; $errorlist .= "Current Action is required.<br />"; }
        if ($currentfight == "") { $errors++; $errorlist .= "Current Fight is required.<br />"; }
        
        if ($currentmonster == "") { $errors++; $errorlist .= "Current Monster is required.<br />"; }
        if ($currentmonsterhp == "") { $errors++; $errorlist .= "Current Monster HP is required.<br />"; }
        if ($currentmonstersleep == "") { $errors++; $errorlist .= "Current Monster Sleep is required.<br />"; }
        if ($currentmonsterimmune == "") { $errors++; $errorlist .= "Current Monster Immune is required.<br />"; }
        if ($currentuberdamage == "") { $errors++; $errorlist .= "Current Uber Damage is required.<br />"; }
        if ($currentuberdefense == "") { $errors++; $errorlist .= "Current Uber Defense is required.<br />"; }
        if ($currenthp == "") { $errors++; $errorlist .= "Current HP is required.<br />"; }
        if ($currentmp == "") { $errors++; $errorlist .= "Current MP is required.<br />"; }
        if ($currenttp == "") { $errors++; $errorlist .= "Current TP is required.<br />"; }
        if ($maxhp == "") { $errors++; $errorlist .= "Max HP is required.<br />"; }

        if ($maxmp == "") { $errors++; $errorlist .= "Max MP is required.<br />"; }
        if ($maxtp == "") { $errors++; $errorlist .= "Max TP is required.<br />"; }
        if ($level == "") { $errors++; $errorlist .= "Level is required.<br />"; }
        if ($gold == "") { $errors++; $errorlist .= "Gold is required.<br />"; }
        if ($bank == "") { $errors++; $errorlist .= "Banked Gold is required.<br />"; }
        if ($experience == "") { $errors++; $errorlist .= "Experience is required.<br />"; }
        if ($goldbonus == "") { $errors++; $errorlist .= "Gold Bonus is required.<br />"; }
        if ($expbonus == "") { $errors++; $errorlist .= "Experience Bonus is required.<br />"; }
        if ($strength == "") { $errors++; $errorlist .= "Strength is required.<br />"; }
        if ($dexterity == "") { $errors++; $errorlist .= "Dexterity is required.<br />"; }
        if ($attackpower == "") { $errors++; $errorlist .= "Attack Power is required.<br />"; }
        if ($skill1level == "") { $errors++; $errorlist .= "Wisdom Level is required.<br />"; }
        if ($skill2level == "") { $errors++; $errorlist .= "Stone Skin Level is required.<br />"; }
        if ($skill3level == "") { $errors++; $errorlist .= "Monks Mind Level is required.<br />"; }
        if ($skill4level == "") { $errors++; $errorlist .= "Fortune Level is required.<br />"; }

        if ($defensepower == "") { $errors++; $errorlist .= "Defense Power is required.<br />"; }
        if ($weaponid == "") { $errors++; $errorlist .= "Weapon ID is required.<br />"; }
        if ($armorid == "") { $errors++; $errorlist .= "Armor ID is required.<br />"; }
        if ($shieldid == "") { $errors++; $errorlist .= "Shield ID is required.<br />"; }
        if ($slot1id == "") { $errors++; $errorlist .= "Slot 1 ID is required.<br />"; }
        if ($slot2id == "") { $errors++; $errorlist .= "Slot 2 ID is required.<br />"; }
        if ($slot3id == "") { $errors++; $errorlist .= "Slot 3 ID is required.<br />"; }
        if ($slot4id == "") { $errors++; $errorlist .= "Slot 4 ID is required.<br />"; }
        if ($slot5id == "") { $errors++; $errorlist .= "Slot 5 ID is required.<br />"; }
                if ($slot6id == "") { $errors++; $errorlist .= "Slot 6 ID is required.<br />"; }
        if ($slot7id == "") { $errors++; $errorlist .= "Slot 7 ID is required.<br />"; }
        if ($slot8id == "") { $errors++; $errorlist .= "Slot 8 ID is required.<br />"; }
        if ($weaponname == "") { $errors++; $errorlist .= "Weapon Name is required.<br />"; }
        if ($armorname == "") { $errors++; $errorlist .= "Armor Name is required.<br />"; }
        if ($shieldname == "") { $errors++; $errorlist .= "Shield Name is required.<br />"; }

        if ($slot1name == "") { $errors++; $errorlist .= "Slot 1 Name is required.<br />"; }
        if ($slot2name == "") { $errors++; $errorlist .= "Slot 2 Name is required.<br />"; }
        if ($slot3name == "") { $errors++; $errorlist .= "Slot 3 Name is required.<br />"; }
        if ($slot4name == "") { $errors++; $errorlist .= "Slot 4 Name is required.<br />"; }
        if ($slot5name == "") { $errors++; $errorlist .= "Slot 5 Name is required.<br />"; }
        if ($slot6name == "") { $errors++; $errorlist .= "Slot 6 Name is required.<br />"; }
        if ($slot7name == "") { $errors++; $errorlist .= "Slot 7 Name is required.<br />"; }
        if ($slot8name == "") { $errors++; $errorlist .= "Slot 8 Name is required.<br />"; }
        if ($dscales == "") { $errors++; $errorlist .= "Dragon Scales is required.<br />"; }
        if ($title == "") { $errors++; $errorlist .= "Title Name is required.<br />"; }
        if ($dropcode == "") { $errors++; $errorlist .= "Drop Code is required.<br />"; }
        if ($spells == "") { $errors++; $errorlist .= "Spells is required.<br />"; }
        if ($towns == "") { $errors++; $errorlist .= "Towns is required.<br />"; }
        
        if (!is_numeric($authlevel)) { $errors++; $errorlist .= "Auth Level must be a number.<br />"; }
        if (!is_numeric($latitude)) { $errors++; $errorlist .= "Latitude must be a number.<br />"; }
        if (!is_numeric($longitude)) { $errors++; $errorlist .= "Longitude must be a number.<br />"; }
        if (!is_numeric($charclass)) { $errors++; $errorlist .= "Character Class must be a number.<br />"; }
        if (!is_numeric($currentfight)) { $errors++; $errorlist .= "Current Fight must be a number.<br />"; }
        if (!is_numeric($currentmonster)) { $errors++; $errorlist .= "Current Monster must be a number.<br />"; }
        if (!is_numeric($currentmonsterhp)) { $errors++; $errorlist .= "Current Monster HP must be a number.<br />"; }
        if (!is_numeric($currentmonstersleep)) { $errors++; $errorlist .= "Current Monster Sleep must be a number.<br />"; }
        
        if (!is_numeric($currentmonsterimmune)) { $errors++; $errorlist .= "Current Monster Immune must be a number.<br />"; }
        if (!is_numeric($currentuberdamage)) { $errors++; $errorlist .= "Current Uber Damage must be a number.<br />"; }
        if (!is_numeric($currentuberdefense)) { $errors++; $errorlist .= "Current Uber Defense must be a number.<br />"; }
        if (!is_numeric($currenthp)) { $errors++; $errorlist .= "Current HP must be a number.<br />"; }
        if (!is_numeric($currentmp)) { $errors++; $errorlist .= "Current MP must be a number.<br />"; }
        if (!is_numeric($currenttp)) { $errors++; $errorlist .= "Current TP must be a number.<br />"; }
        if (!is_numeric($currentap)) { $errors++; $errorlist .= "Current AP must be a number.<br />"; }
        if (!is_numeric($currentfat)) { $errors++; $errorlist .= "Current Fatigue must be a number.<br />"; }
        if (!is_numeric($maxhp)) { $errors++; $errorlist .= "Max HP must be a number.<br />"; }
        if (!is_numeric($maxmp)) { $errors++; $errorlist .= "Max MP must be a number.<br />"; }
        if (!is_numeric($maxtp)) { $errors++; $errorlist .= "Max TP must be a number.<br />"; }
        if (!is_numeric($maxap)) { $errors++; $errorlist .= "Max AP must be a number.<br />"; }
        if (!is_numeric($maxfat)) { $errors++; $errorlist .= "Max Fatigue must be a number.<br />"; }
        if (!is_numeric($level)) { $errors++; $errorlist .= "Level must be a number.<br />"; }
        
        if (!is_numeric($gold)) { $errors++; $errorlist .= "Gold must be a number.<br />"; }
        if (!is_numeric($bank)) { $errors++; $errorlist .= "Banked Gold must be a number.<br />"; }
        if (!is_numeric($experience)) { $errors++; $errorlist .= "Experience must be a number.<br />"; }
        if (!is_numeric($goldbonus)) { $errors++; $errorlist .= "Gold Bonus must be a number.<br />"; }
        if (!is_numeric($expbonus)) { $errors++; $errorlist .= "Experience Bonus must be a number.<br />"; }
        if (!is_numeric($strength)) { $errors++; $errorlist .= "Strength must be a number.<br />"; }
        if (!is_numeric($dexterity)) { $errors++; $errorlist .= "Dexterity must be a number.<br />"; }
        if (!is_numeric($attackpower)) { $errors++; $errorlist .= "Attack Power must be a number.<br />"; }
        if (!is_numeric($defensepower)) { $errors++; $errorlist .= "Defense Power must be a number.<br />"; }
        if (!is_numeric($skill1level)) { $errors++; $errorlist .= "Wisdom Level must be a number.<br />"; }
        if (!is_numeric($skill2level)) { $errors++; $errorlist .= "Stone Skin Level must be a number.<br />"; }
        if (!is_numeric($skill3level)) { $errors++; $errorlist .= "Monks Mind Level must be a number.<br />"; }
        if (!is_numeric($skill4level)) { $errors++; $errorlist .= "Fortune Level must be a number.<br />"; }
        if (!is_numeric($weaponid)) { $errors++; $errorlist .= "Weapon ID must be a number.<br />"; }
        if (!is_numeric($armorid)) { $errors++; $errorlist .= "Armor ID must be a number.<br />"; }
        
        if (!is_numeric($shieldid)) { $errors++; $errorlist .= "Shield ID must be a number.<br />"; }
        if (!is_numeric($slot1id)) { $errors++; $errorlist .= "Slot 1 ID  must be a number.<br />"; }
        if (!is_numeric($slot2id)) { $errors++; $errorlist .= "Slot 2 ID must be a number.<br />"; }
        if (!is_numeric($slot3id)) { $errors++; $errorlist .= "Slot 3 ID must be a number.<br />"; }
        if (!is_numeric($slot4id)) { $errors++; $errorlist .= "Slot 4 ID must be a number.<br />"; }
        if (!is_numeric($slot5id)) { $errors++; $errorlist .= "Slot 5 ID must be a number.<br />"; }
        
        if (!is_numeric($slot6id)) { $errors++; $errorlist .= "Slot 6 ID must be a number.<br />"; }
        if (!is_numeric($slot7id)) { $errors++; $errorlist .= "Slot 7 ID must be a number.<br />"; }
        if (!is_numeric($slot8id)) { $errors++; $errorlist .= "Slot 8 ID must be a number.<br />"; }
        if (!is_numeric($dscales)) { $errors++; $errorlist .= "Dragon Scales must be a number.<br />"; }
        if (!is_numeric($dropcode)) { $errors++; $errorlist .= "Drop Code must be a number.<br />"; }
        
        if ($errors == 0) { 
$updatequery = <<<END
UPDATE {{table}} SET
email="$email", verify="$verify", charname="$charname", authlevel="$authlevel", latitude="$latitude",
longitude="$longitude", charclass="$charclass", currentaction="$currentaction", location="$location", currentfight="$currentfight",
currentmonster="$currentmonster", currentmonsterhp="$currentmonsterhp", currentmonstersleep="$currentmonstersleep", currentmonsterimmune="$currentmonsterimmune", currentuberdamage="$currentuberdamage",
currentuberdefense="$currentuberdefense", currenthp="$currenthp", currentmp="$currentmp", currenttp="$currenttp", currentap="$currentap", currentfat="$currentfat", maxhp="$maxhp",
maxmp="$maxmp", maxtp="$maxtp", maxap="$maxap", maxfat="$maxfat", level="$level", gold="$gold", experience="$experience",
goldbonus="$goldbonus", expbonus="$expbonus", strength="$strength", dexterity="$dexterity", attackpower="$attackpower",
defensepower="$defensepower", weaponid="$weaponid", armorid="$armorid", shieldid="$shieldid", slot1id="$slot1id",
slot2id="$slot2id", slot3id="$slot3id", weaponname="$weaponname", armorname="$armorname", shieldname="$shieldname",
slot1name="$slot1name", slot2name="$slot2name", slot3name="$slot3name", dropcode="$dropcode", spells="$spells",
towns="$towns", slot4id="$slot4id", templist='$templist', inventitems='$inventitems',slot4name="$slot4name", slot5id="$slot5id", slot5name="$slot5name", slot5id="$slot5id", slot5name="$slot5name", slot6id="$slot6id", slot7name="$slot7name", slot8id="$slot8id", slot8name="$slot8name", dscales="$dscales", skill1level="$skill1level",  skill2level="$skill2level",  skill3level="$skill3level",  skill4level="$skill4level", guildname="$guildname", guildrank="$guildrank", templist="$templist", potion="$potion", drink="$drink", numbattlewon="$numbattlewon", numbattlelost="$numbattlelost", title="$title", bank="$bank" WHERE id="$id" LIMIT 1
END;
			$query = doquery($updatequery, "users");
            admindisplay("User updated.","Edit Users");
        } else {
            admindisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Users");
        }        
        
    }   
        
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "users");
    $row = mysql_fetch_array($query);
    global $controlrow;
    $diff1name = $controlrow["diff1name"];
    $diff2name = $controlrow["diff2name"];
    $diff3name = $controlrow["diff3name"];
    $class1name = $controlrow["class1name"];
    $class2name = $controlrow["class2name"];
    $class3name = $controlrow["class3name"];
    $class4name = $controlrow["class4name"];
    $class5name = $controlrow["class5name"];
    $class6name = $controlrow["class6name"];
    $class7name = $controlrow["class7name"];

$page = <<<END
<b><u>Edit Users</u></b><br /><br />
<form action="admin.php?do=edituser:$id" method="post">
<table width="90%">
<tr><td width="20%">ID:</td><td>{{id}}</td></tr>
<tr><td width="20%">Username:</td><td>{{username}}</td></tr>
<tr><td width="20%">Email:</td><td><input type="text" name="email" size="30" maxlength="100" value="{{email}}" /></td></tr>
<tr><td width="20%"><a href="mail.php?to={{email}}">Send Email</td></tr>
<tr><td width="20%">Verify:</td><td><input type="text" name="verify" size="30" maxlength="8" value="{{verify}}" /></td></tr>
<tr><td width="20%">Character Name:</td><td><input type="text" name="charname" size="30" maxlength="30" value="{{charname}}" /></td></tr>
<tr><td width="20%">Register Date:</td><td>{{regdate}}</td></tr>
<tr><td width="20%">Last Online:</td><td>{{onlinetime}}</td></tr>
<tr><td width="20%">IP Address:</td><td>{{ipaddress}}</td></tr>
<tr><td width="20%">Auth Level:</td><td><select name="authlevel"><option value="0" {{auth0select}}>User</option><option value="1" {{auth1select}}>Admin</option><option value="2" {{auth2select}}>Blocked</option><option value="3" {{auth3select}}>Moderator</option><option value="4" {{auth4select}}>Mute User</option></select><br /><span class="small">Set to "Blocked" to temporarily (or permanently) ban a user.</span></td></tr>

<tr><td colspan="2" style="background-color:#cccccc;">&nbsp;</td></tr>

<tr><td width="20%">Latitude:</td><td><input type="text" name="latitude" size="5" maxlength="6" value="{{latitude}}" /></td></tr>
<tr><td width="20%">Longitude:</td><td><input type="text" name="longitude" size="5" maxlength="6" value="{{longitude}}" /></td></tr>
<tr><td width="20%">Character Class:</td><td><select name="charclass"><option value="1" {{class1select}}>$class1name</option><option value="2" {{class2select}}>$class2name</option><option value="3" {{class3select}}>$class3name</option><option value="4" {{class4select}}>$class4name</option><option value="5" {{class5select}}>$class5name</option><option value="6" {{class6select}}>$class6name</option><option value="7" {{class7select}}>$class7name</option></select></td></tr>

<tr><td colspan="2" style="background-color:#cccccc;">&nbsp;</td></tr>

<tr><td width="20%">Current Action:</td><td><input type="text" name="currentaction" size="30" maxlength="30" value="{{currentaction}}" /></td></tr>
<tr><td width="20%">Current Location:</td><td><input type="text" name="location" size="30" maxlength="30" value="{{location}}" /></td></tr>
<tr><td width="20%">Current Fight:</td><td><input type="text" name="currentfight" size="5" maxlength="4" value="{{currentfight}}" /></td></tr>
<tr><td width="20%">Current Monster:</td><td><input type="text" name="currentmonster" size="5" maxlength="6" value="{{currentmonster}}" /></td></tr>
<tr><td width="20%">Current Monster HP:</td><td><input type="text" name="currentmonsterhp" size="5" maxlength="6" value="{{currentmonsterhp}}" /></td></tr>
<tr><td width="20%">Current Monster Sleep:</td><td><input type="text" name="currentmonsterimmune" size="5" maxlength="3" value="{{currentmonsterimmune}}" /></td></tr>
<tr><td width="20%">Current Monster Immune:</td><td><input type="text" name="currentmonstersleep" size="5" maxlength="3" value="{{currentmonstersleep}}" /></td></tr>
<tr><td width="20%">Current Uber Damage:</td><td><input type="text" name="currentuberdamage" size="5" maxlength="3" value="{{currentuberdamage}}" /></td></tr>
<tr><td width="20%">Current Uber Defense:</td><td><input type="text" name="currentuberdefense" size="5" maxlength="3" value="{{currentuberdefense}}" /></td></tr>

<tr><td colspan="2" style="background-color:#cccccc;">&nbsp;</td></tr>

<tr><td width="20%">Current HP:</td><td><input type="text" name="currenthp" size="5" maxlength="6" value="{{currenthp}}" /></td></tr>
<tr><td width="20%">Current MP:</td><td><input type="text" name="currentmp" size="5" maxlength="6" value="{{currentmp}}" /></td></tr>
<tr><td width="20%">Current TP:</td><td><input type="text" name="currenttp" size="5" maxlength="6" value="{{currenttp}}" /></td></tr>
<tr><td width="20%">Current AP:</td><td><input type="text" name="currentap" size="5" maxlength="6" value="{{currentap}}" /></td></tr>
<tr><td width="20%">Current Fatigue:</td><td><input type="text" name="currentfat" size="5" maxlength="6" value="{{currentfat}}" /></td></tr>

<tr><td width="20%">Max HP:</td><td><input type="text" name="maxhp" size="5" maxlength="6" value="{{maxhp}}" /></td></tr>
<tr><td width="20%">Max MP:</td><td><input type="text" name="maxmp" size="5" maxlength="6" value="{{maxmp}}" /></td></tr>
<tr><td width="20%">Max TP:</td><td><input type="text" name="maxtp" size="5" maxlength="6" value="{{maxtp}}" /></td></tr>
<tr><td width="20%">Max AP:</td><td><input type="text" name="maxap" size="5" maxlength="6" value="{{maxap}}" /></td></tr>
<tr><td width="20%">Max Fatigue:</td><td><input type="text" name="maxfat" size="5" maxlength="6" value="{{maxfat}}" /></td></tr>

<tr><td colspan="2" style="background-color:#cccccc;">&nbsp;</td></tr>

<tr><td width="20%">Level:</td><td><input type="text" name="level" size="5" maxlength="5" value="{{level}}" /></td></tr>
<tr><td width="20%">Gold:</td><td><input type="text" name="gold" size="12" maxlength="12" value="{{gold}}" /></td></tr>
<tr><td width="20%">Banked Gold:</td><td><input type="text" name="bank" size="12" maxlength="12" value="{{bank}}" /></td></tr>
<tr><td width="20%">Experience:</td><td><input type="text" name="experience" size="10" maxlength="15" value="{{experience}}" /></td></tr>
<tr><td width="20%">Gold Bonus:</td><td><input type="text" name="goldbonus" size="5" maxlength="5" value="{{goldbonus}}" /></td></tr>
<tr><td width="20%">Experience Bonus:</td><td><input type="text" name="expbonus" size="5" maxlength="5" value="{{expbonus}}" /></td></tr>
<tr><td width="20%">Strength:</td><td><input type="text" name="strength" size="5" maxlength="5" value="{{strength}}" /></td></tr>
<tr><td width="20%">Dexterity:</td><td><input type="text" name="dexterity" size="5" maxlength="5" value="{{dexterity}}" /></td></tr>
<tr><td width="20%">Attack Power:</td><td><input type="text" name="attackpower" size="5" maxlength="5" value="{{attackpower}}" /></td></tr>
<tr><td width="20%">Defense Power:</td><td><input type="text" name="defensepower" size="5" maxlength="5" value="{{defensepower}}" /></td></tr>
<tr><td width="20%">Wisdom Level:</td><td><input type="text" name="skill1level" size="5" maxlength="5" value="{{skill1level}}" /></td></tr>
<tr><td width="20%">Stone Skin Level:</td><td><input type="text" name="skill2level" size="5" maxlength="5" value="{{skill2level}}" /></td></tr>
<tr><td width="20%">Monks Mind Level:</td><td><input type="text" name="skill3level" size="5" maxlength="5" value="{{skill3level}}" /></td></tr>
<tr><td width="20%">Fortune Level:</td><td><input type="text" name="skill4level" size="5" maxlength="5" value="{{skill4level}}" /></td></tr>
<tr><td width="20%">Dragon Scales:</td><td><input type="text" name="dscales" size="10" maxlength="10" value="{{dscales}}" /></td></tr>
<tr><td width="20%">Title:</td><td><input type="text" name="title" size="30" maxlength="30" value="{{title}}" /></td></tr>
<tr><td width="20%">Guild Name:</td><td><input type="text" name="guildname" size="30" maxlength="30" value="{{guildname}}" /></td></tr>
<tr><td width="20%">Guild Rank:</td><td><input type="text" name="guildrank" size="5" maxlength="5" value="{{guildrank}}" /></td></tr>

<tr><td colspan="2" style="background-color:#cccccc;">&nbsp;</td></tr>

<tr><td width="20%">Weapon ID:</td><td><input type="text" name="weaponid" size="5" maxlength="5" value="{{weaponid}}" /></td></tr>
<tr><td width="20%">Armor ID:</td><td><input type="text" name="armorid" size="5" maxlength="5" value="{{armorid}}" /></td></tr>
<tr><td width="20%">Shield ID:</td><td><input type="text" name="shieldid" size="5" maxlength="5" value="{{shieldid}}" /></td></tr>
<tr><td width="20%">Slot 1 ID:</td><td><input type="text" name="slot1id" size="5" maxlength="5" value="{{slot1id}}" /></td></tr>
<tr><td width="20%">Slot 2 ID:</td><td><input type="text" name="slot2id" size="5" maxlength="5" value="{{slot2id}}" /></td></tr>
<tr><td width="20%">Slot 3 ID:</td><td><input type="text" name="slot3id" size="5" maxlength="5" value="{{slot3id}}" /></td></tr>
<tr><td width="20%">Slot 4 ID:</td><td><input type="text" name="slot4id" size="5" maxlength="5" value="{{slot4id}}" /></td></tr>
<tr><td width="20%">Slot 5 ID:</td><td><input type="text" name="slot5id" size="5" maxlength="5" value="{{slot5id}}" /></td></tr>
<tr><td width="20%">Slot 6 ID:</td><td><input type="text" name="slot6id" size="5" maxlength="5" value="{{slot6id}}" /></td></tr>
<tr><td width="20%">Slot 7 ID:</td><td><input type="text" name="slot7id" size="5" maxlength="5" value="{{slot7id}}" /></td></tr>
<tr><td width="20%">Slot 8 ID:</td><td><input type="text" name="slot8id" size="5" maxlength="5" value="{{slot8id}}" /></td></tr>



<tr><td width="20%">Weapon Name:</td><td><input type="text" name="weaponname" size="30" maxlength="30" value="{{weaponname}}" /></td></tr>
<tr><td width="20%">Armor Name:</td><td><input type="text" name="armorname" size="30" maxlength="30" value="{{armorname}}" /></td></tr>
<tr><td width="20%">Shield Name:</td><td><input type="text" name="shieldname" size="30" maxlength="30" value="{{shieldname}}" /></td></tr>
<tr><td width="20%">Slot 1 Name:</td><td><input type="text" name="slot1name" size="30" maxlength="30" value="{{slot1name}}" /></td></tr>
<tr><td width="20%">Slot 2 Name:</td><td><input type="text" name="slot2name" size="30" maxlength="30" value="{{slot2name}}" /></td></tr>
<tr><td width="20%">Slot 3 Name:</td><td><input type="text" name="slot3name" size="30" maxlength="30" value="{{slot3name}}" /></td></tr>
<tr><td width="20%">Slot 4 Name:</td><td><input type="text" name="slot4name" size="30" maxlength="30" value="{{slot4name}}" /></td></tr>
<tr><td width="20%">Slot 5 Name:</td><td><input type="text" name="slot5name" size="30" maxlength="30" value="{{slot5name}}" /></td></tr>
<tr><td width="20%">Slot 6 Name:</td><td><input type="text" name="slot6name" size="30" maxlength="30" value="{{slot6name}}" /></td></tr>
<tr><td width="20%">Slot 7 Name:</td><td><input type="text" name="slot7name" size="30" maxlength="30" value="{{slot7name}}" /></td></tr>
<tr><td width="20%">Slot 8 Name:</td><td><input type="text" name="slot8name" size="30" maxlength="30" value="{{slot8name}}" /></td></tr>


<tr><td width="20%">Tavern Drink:</td><td><input type="text" name="drink" size="30" maxlength="30" value="{{drink}}" /></td></tr>
<tr><td width="20%">Potion:</td><td><input type="text" name="potion" size="30" maxlength="30" value="{{potion}}" /></td></tr>
<tr><td width="20%">Duel Wins:</td><td><input type="text" name="numbattlewon" size="5" maxlength="5" value="{{numbattlewon}}" /></td></tr>
<tr><td width="20%">Duel Loss's:</td><td><input type="text" name="numbattlelost" size="5" maxlength="10" value="{{numbattlelost}}" /></td></tr>


<tr><td colspan="2" style="background-color:#cccccc;">&nbsp;</td></tr>
<tr><td width="20%">Temp List:</td><td><input type="text" name="templist" size="10" maxlength="10" value="{{templist}}" /></td></tr>
<tr><td width="20%">Drop Code:</td><td><input type="text" name="dropcode" size="5" maxlength="8" value="{{dropcode}}" /></td></tr>
<tr><td width="20%">Spells:</td><td><input type="text" name="spells" size="80" maxlength="150" value="{{spells}}" /></td></tr>
<tr><td width="20%">Items:</td><td><input type="text" name="inventitems" size="80" maxlength="150" value="{{inventitems}}" /></td></tr>
<tr><td width="20%">Towns:</td><td><input type="text" name="towns" size="80" maxlength="150" value="{{towns}}" /></td></tr>

</table>
<input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" />
</form>
END;

    if ($row["authlevel"] == 0) { $row["auth0select"] = "selected=\"selected\" "; } else { $row["auth0select"] = ""; }
    if ($row["authlevel"] == 1) { $row["auth1select"] = "selected=\"selected\" "; } else { $row["auth1select"] = ""; }
    if ($row["authlevel"] == 2) { $row["auth2select"] = "selected=\"selected\" "; } else { $row["auth2select"] = ""; }
    if ($row["authlevel"] == 3) { $row["auth3select"] = "selected=\"selected\" "; } else { $row["auth3select"] = ""; }
    if ($row["authlevel"] == 4) { $row["auth4select"] = "selected=\"selected\" "; } else { $row["auth4select"] = ""; }    
    if ($row["charclass"] == 1) { $row["class1select"] = "selected=\"selected\" "; } else { $row["class1select"] = ""; }
    if ($row["charclass"] == 2) { $row["class2select"] = "selected=\"selected\" "; } else { $row["class2select"] = ""; }
    if ($row["charclass"] == 3) { $row["class3select"] = "selected=\"selected\" "; } else { $row["class3select"] = ""; }
    if ($row["charclass"] == 4) { $row["class4select"] = "selected=\"selected\" "; } else { $row["class4select"] = ""; }
    if ($row["charclass"] == 5) { $row["class5select"] = "selected=\"selected\" "; } else { $row["class5select"] = ""; }
    if ($row["charclass"] == 6) { $row["class6select"] = "selected=\"selected\" "; } else { $row["class6select"] = ""; }
    if ($row["charclass"] == 7) { $row["class7select"] = "selected=\"selected\" "; } else { $row["class7select"] = ""; }
    if ($row["difficulty"] == 1) { $row["diff1select"] = "selected=\"selected\" "; } else { $row["diff1select"] = ""; }
    if ($row["difficulty"] == 2) { $row["diff2select"] = "selected=\"selected\" "; } else { $row["diff2select"] = ""; }
    if ($row["difficulty"] == 3) { $row["diff3select"] = "selected=\"selected\" "; } else { $row["diff3select"] = ""; }
    
    $page = parsetemplate($page, $row);
    admindisplay($page, "Edit Users");
    
}

function addnews() {
     global $userrow;
    if (isset($_POST["submit"])) {
        
        extract($_POST);
        $errors = 0;
        $errorlist = "";
        if ($content == "") { $errors++; $errorlist .= "Content is required.<br />"; }
        if ($title == "") { $errors++; $errorlist .= "Title is required.<br />"; }
        if ($errors == 0) { 
            $query = doquery("INSERT INTO {{table}} SET id='',postdate=NOW(),content='$content',title='$title'", "news");
            admindisplay("News post added.","Add News");
        } else {
            admindisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Add News");
        }        
        
    }   
        
$page = <<<END
<b><u>Add A News Post</u></b><br /><br />
<form action="admin.php?do=news" method="post">
Type the title and content for your News post and then click Submit to add it.<br /><br>
<input type="text" name="title" size="30" maxlength="30"/><p>
<textarea name="content" rows="5" cols="50"></textarea><br />
<input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" />
</form>
END;
    
    admindisplay($page, "Add News");
    
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
        if ($count == 1) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">".$row["id"]."</td><td style=\"background-color: #eeeeee;\"><a href=\"admin.php?do=editchat:".$row["id"]."\">".$row["author"]."</a></td><td style=\"background-color: #eeeeee;\"><a href=\"admin.php?do=editchat:".$row["id"]."\">".$row["babble"]."</a></td></tr>\n"; $count = 2; }
        else { $page .= "<tr><td width=\"8%\" style=\"background-color: #ffffff;\">".$row["id"]."</td><td style=\"background-color: #ffffff;\"><a href=\"admin.php?do=editchat:".$row["id"]."\">".$row["author"]."</a></td><td style=\"background-color: #ffffff;\"><a href=\"admin.php?do=editchat:".$row["id"]."\">".$row["babble"]."</a></td></tr>\n"; $count = 1; }
    }
    if (mysql_num_rows($query) == 0) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">No chat messages found.</td></tr>\n"; }
    $page .= "</table>";
    admindisplay($page, "Edit Chat");
    
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
            admindisplay("Message updated.","Edit Chat");
        } else {
            admindisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Chat");
        }        
        
    }   
        
    
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "chat");
    $row = mysql_fetch_array($query);

$page = <<<END
<b><u>Edit Chat</u></b><br /><br />
<form action="admin.php?do=editchat:$id" method="post">
<table width="90%">
<tr><td width="20%">Message ID:</td><td>{{id}} - <a href="admin.php?do=delete:$id:chat">Delete</a></td></tr>
<tr><td width="20%">Message Time:</td><td><input type="text" name="posttime" size="20" maxlength="20" value="{{posttime}}" /><br>Please dont edit this unless its neccessary</td></tr>
<tr><td width="20%">Character Name:</td><td><input type="text" name="author" size="30" maxlength="30" value="{{author}}" /><br>Please dont edit this unless its neccessary</td></tr>
<tr><td width="20%">Text:</td><td><textarea name="babble" rows="7" cols="40">{{babble}}</textarea><br></td></tr>

</table>
<input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" />
</form>
END;
    
    $page = parsetemplate($page, $row);
    admindisplay($page, "Edit Chat");
    
}
    
?>