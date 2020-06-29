<?PHP


function addpost($topic) {
	global $userrow;

if ($userrow["level"] < 3) { die( //Level restriction
"You must be at least level 3 or above to post a Comment");
}
if ($userrow["authlevel"] == 4){ die( //Mute a player from chatting and game mailing
"Your account has been muted from the Player Chat, posting Comments, the Forum and from Game Mails.<p>This is most probably temporary due to you breaking the rules, or causing problems.<p>Please return to what you were previously doing.");
	}
        $comment = $_POST['comment'];
	if ($message = '' || $message = ' ' || !$message) // Blank post
		header("Location: index.php");
	doquery("INSERT INTO {{table}} SET topic=$topic,time=NOW(),poster=$userrow[id],post='$comment'", "comments");
	$query = doquery("UPDATE {{table}} SET postcount=postcount+1 WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	header("Location: index.php?do=comments:$topic");
}
function read($topic) {
	$title = "Post Comments";
	$query = doquery("SELECT * FROM {{table}} WHERE id=$topic LIMIT 1", "news");
	$newsrow = mysql_fetch_assoc($query);
							    $newsrow = str_replace(":)", "<img src='images/smilies/smile.gif'>", $newsrow); //16 Smilies
			    $newsrow = str_replace(":(", "<img src='images/smilies/sad.gif'>", $newsrow); 			     
		        $newsrow = str_replace(":P", "<img src='images/smilies/tongue.gif'>", $newsrow);
			    $newsrow = str_replace(";)", "<img src='images/smilies/wink.gif'>", $newsrow); 
			    $newsrow = str_replace("(ha)", "<img src='images/smilies/biggrin.gif'>", $newsrow);
			    $newsrow = str_replace("^^", "<img src='images/smilies/rolleyes.gif'>", $newsrow); 
			    $newsrow = str_replace("o.O", "<img src='images/smilies/freak.gif'>", $newsrow);
			    $newsrow = str_replace(":$", "<img src='images/smilies/embaressed.gif'>", $newsrow);
			    $newsrow = str_replace("(c)", "<img src='images/smilies/cool.gif'>", $newsrow); 
			    $newsrow = str_replace(":@", "<img src='images/smilies/mad.gif'>", $newsrow); 
			    $newsrow = str_replace(":/", "<img src='images/smilies/umm.gif'>", $newsrow); 	
			    $newsrow = str_replace(":O", "<img src='images/smilies/shocked.gif'>", $newsrow); 
			    $newsrow = str_replace(":?", "<img src='images/smilies/ques-tion.gif'>", $newsrow); 	
			    $newsrow = str_replace(":!", "<img src='images/smilies/exclamation.gif'>", $newsrow); 
			    $newsrow = str_replace(":D", "<img src='images/smilies/lol.gif'>", $newsrow); 
			    $newsrow = str_replace(":%", "<img src='images/smilies/drool.gif'>", $newsrow); 
	$page = "<table width=\"95%\"><tr><td class=\"title\">Latest News Posts</td></tr><tr><td>\n";
	$page .= "<span class=\"news\">[".prettydate($newsrow["postdate"])."] ".$newsrow["title"]." - By ".$newsrow["author"]."</span><br /><br />".nl2br($newsrow["content"]);
	$page .= "</td></tr></table>\n";


	$page .= "<br><table width=\"95%\"><tr><td class=\"title\">Post Comments</td></tr>\n";
	$query = doquery("SELECT * FROM {{table}} WHERE topic=$topic ORDER BY id ASC", "comments");
	while ($com = mysql_fetch_assoc($query)) {
		$pquery = doquery("SELECT * FROM {{table}} WHERE id=".$com['poster']." LIMIT 1", "users");
		$person = mysql_fetch_assoc($pquery);
						    $com = str_replace(":)", "<img src='images/smilies/smile.gif'>", $com); //16 Smilies
			    $com = str_replace(":(", "<img src='images/smilies/sad.gif'>", $com); 			     
		        $com = str_replace(":P", "<img src='images/smilies/tongue.gif'>", $com);
			    $com = str_replace(";)", "<img src='images/smilies/wink.gif'>", $com); 
			    $com = str_replace("(ha)", "<img src='images/smilies/biggrin.gif'>", $com);
			    $com = str_replace("^^", "<img src='images/smilies/rolleyes.gif'>", $com); 
			    $com = str_replace("o.O", "<img src='images/smilies/freak.gif'>", $com);
			    $com = str_replace(":$", "<img src='images/smilies/embaressed.gif'>", $com);
			    $com = str_replace("(c)", "<img src='images/smilies/cool.gif'>", $com); 
			    $com = str_replace(":@", "<img src='images/smilies/mad.gif'>", $com); 
			    $com = str_replace(":/", "<img src='images/smilies/umm.gif'>", $com); 	
			    $com = str_replace(":O", "<img src='images/smilies/shocked.gif'>", $com); 
			    $com = str_replace(":?", "<img src='images/smilies/ques-tion.gif'>", $com); 	
			    $com = str_replace(":!", "<img src='images/smilies/exclamation.gif'>", $com); 
			    $com = str_replace(":D", "<img src='images/smilies/lol.gif'>", $com); 
			    $com = str_replace(":%", "<img src='images/smilies/drool.gif'>", $com); 
		$page .= "<tr><td><span class=\"light\"><a href=\"index.php?do=onlinechar:".$person["id"]."\">".$person['charname']."</a> - <a href=\"index.php?do=editcomment:".$com["id"]."\">Edit</a> - Posts: ".$person['postcount']." - [".prettydate($com["time"])."]</span><br />".nl2br($com["post"])." <hr></td></tr>";
	}
	$page .= "</table><br>\n";
	$page .= "<font color=red><b>Warning:</b> Posting useless posts which isn't related to the News Article may result in a warning, or a possible ban.</font><p><form action=index.php?do=post_comment:$topic method=post><textarea name=comment cols=30 rows=5></textarea><br /><input type=submit name=submit value='Post Comments' /></form><br />";
	$page .= "<br />Go <a href=index.php>back</a> to town.";

	display($page, $title);
}

function editcomment($id) {
 global $userrow;

    if (isset($_POST["submit"])) {

        extract($_POST);
        $errors = 0;
        $errorlist = "";
        if ($post == "") { $errors++; $errorlist .= "Content is required, return to <a href=\"index.php\">Town</a>.<br />"; }

        
        if ($errors == 0) { 
            $query = doquery("UPDATE {{table}} SET post='$post' WHERE id='$id' LIMIT 1", "comments");
            display("Your Comment was successfully updated. Return to <a href=\"index.php\">Town</a>.","Edit Comment");
        } else {
            display("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Comment");
        }        
        
    }   
$idquery = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "comments");
	$idrow = mysql_fetch_array($idquery);
	if ($idrow["poster"] != $userrow["id"]) {
        $page .= "<table width='100%' border='1'><tr><td class='title'>Edit Comment - Denied</td></tr></table><p>";
	$page .= "You cannot edit this Comment! This Comment doesn't belong to you. Return to <a href='index.php'>Town</a>.<br>";
	display($page, "Edit Comment");
	}          
    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "comments");
    $row = mysql_fetch_array($query);

$page = <<<END
<table width="100%"><tr><td class="title">Edit Comment</td></tr></table>
<form action="index.php?do=editcomment:$id" method="post">
<table width="90%">
<tr><td width="20%"><a href="index.php?do=delete:$id">Delete Permanently</a></td></tr>
<tr><td width="20%">Comment:</td><td><textarea name="post" rows="5" cols="30">{{post}}</textarea></td></tr>
</table>
<input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" />
</form>
Return to the <a href="index.php">Town</a>
END;
    
    $page = parsetemplate($page, $row);
    display($page, "Edit Comment");
    
}

function delete($id) {
	 global $userrow;
	$idquery = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "comments");
	$idrow = mysql_fetch_array($idquery);
	if ($idrow["poster"] != $userrow["id"]) {
        $page .= "<table width='100%' border='1'><tr><td class='title'>Edit Comment - Denied</td></tr></table><p>";
	$page .= "You cannot delete this Comment! This Comment doesn't belong to you. Return to <a href='index.php'>Town</a>.<br>";
	display($page, "Delete Post");
	} 
	    $query = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "comments");
    $row = mysql_fetch_array($query);

	$query = doquery("DELETE FROM {{table}} WHERE id='$id' LIMIT 1", "comments");
	$query = doquery("UPDATE {{table}} SET postcount=postcount-1 WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	header("Location: index.php");
	die();

}
