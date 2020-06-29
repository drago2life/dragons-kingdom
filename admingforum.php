<?php // general.php :: Internal forums script for the game.

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

if ($userrow == false) { die("Please log in to the <a href=\"../login.php?do=login\">game</a> before using the control panel."); }
if ($userrow["authlevel"] != 1) { die("You must have administrator privileges to use the Admin Guild Forum."); }

$controlquery = doquery("SELECT * FROM {{table}} WHERE id='1' LIMIT 1", "control");
$controlrow = mysql_fetch_array($controlquery);
$page = "<center><a href='index.php'>Return to the Game</a><br></center>";
if ($controlrow["gameopen"] == 0) { 
			header("Location: index.php"); die();
}
if (isset($_GET["do"])) {
	$do = explode(":",$_GET["do"]);

	if ($do[0] == "thread") { showthread($do[1], $do[2]); }
	elseif ($do[0] == "new") { newthread(); }
	elseif ($do[0] == "reply") { reply(); }
	elseif ($do[0] == "list") { donothing($do[1]); }

} else { donothing(0); }

function donothing($start=0) {
global $userrow;
  
      $query2 = doquery("SELECT * FROM {{table}} WHERE pin='1' ORDER BY newpostdate DESC LIMIT 20", "gforum");
 $page = "<table width='100%' border='1'><tr><td class='title'>Guild Forum</td></tr></table><p>";

 $page .= "<hr /><table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"4\" style=\"background-color:#dddddd;\"><center>Only Administrators can Pin Threads for Guilds. Please message one if you think one of your Threads is good enough to be Pinned.</center></th></tr><tr><th width=\"44%\" style=\"background-color:#dddddd;\">Pinned Threads</th><th width=\"2%\" style=\"background-color:#dddddd;\">Replies</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Author</th><th  width=\"30%\" style=\"background-color:#dddddd;\">Last Post</th></tr>\n";
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
                $page .= "<tr><td style=\"background-color:#ffffff;\">".$namelink2."<a href=\"admingforum.php?do=thread:".$row["id"].":0\">".$row["title"]."</a></td><td style=\"background-color:#ffffff;\">".$row["replies"]."</td><td style=\"background-color:#ffffff;\">".$row["author"]."</td><td style=\"background-color:#ffffff;\">".$row["newpostdate"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">".$namelink2."<a href=\"admingforum.php?do=thread:".$row["id"].":0\">".$row["title"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["replies"]."</td><td style=\"background-color:#eeeeee;\">".$row["author"]."</td><td style=\"background-color:#eeeeee;\">".$row["newpostdate"]."</td></tr>\n";
			$count = 1;
		}
	  }
    }

    $page .= "</table></td></tr></table><hr />";

$query= doquery("SELECT * FROM {{table}} WHERE parent='0' AND pin!='1' ORDER BY newpostdate DESC LIMIT ".$start.",12", "gforum");
$fullquery = doquery("SELECT * FROM {{table}} WHERE parent='0' AND pin!='1' ORDER BY newpostdate", "gforum");
 $page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"4\" style=\"background-color:#dddddd;\"><center><a href=\"admingforum.php?do=new\">Create a New Thread</a></center></th></tr><tr><th width=\"44%\" style=\"background-color:#dddddd;\">Thread Title</th><th width=\"2%\" style=\"background-color:#dddddd;\">Replies</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Author</th><th  width=\"30%\" style=\"background-color:#dddddd;\">Last Post</th></tr>\n";
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
                $page .= "<tr><td style=\"background-color:#ffffff;\">".$namelink."<a href=\"admingforum.php?do=thread:".$row["id"].":0\">".$row["title"]."</a></td><td style=\"background-color:#ffffff;\">".$row["replies"]."</td><td style=\"background-color:#ffffff;\">".$row["author"]."</td><td style=\"background-color:#ffffff;\">".$row["newpostdate"]."</td></tr>\n";
			$count = 2;
		} else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">".$namelink."<a href=\"admingforum.php?do=thread:".$row["id"].":0\">".$row["title"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["replies"]."</td><td style=\"background-color:#eeeeee;\">".$row["author"]."</td><td style=\"background-color:#eeeeee;\">".$row["newpostdate"]."</td></tr>\n";
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
		$page .= "<a href='admingforum.php?do=list:".$pagestart."'>".$pagelink."</a>   ";}
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

    $row2 = mysql_fetch_array($query2);

 $page = "<table width='100%' border='1'><tr><td class='title'>Guild Forum - View Thread</td></tr></table><p>";
    $page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><td colspan=\"2\" style=\"background-color:#dddddd;\"><b><a href=\"admingforum.php\">Guild Forum</a> :: ".$row2["title"]."</b></td></tr>\n";
    $count = 1;

    while ($row = mysql_fetch_array($query)) {

		 $query3 = doquery("SELECT postcount FROM {{table}} WHERE charname='".$row["author"]."' LIMIT 1", "users");
	$row3 = mysql_fetch_array($query3);

        if ($count == 1) {
            $page .= "<tr><td width=\"25%\" style=\"background-color:#ffffff; vertical-align:top;\"><span class=\"small\"><b>".$row["author"]."</b><br />Posts: ".$row3["postcount"]."<br /><br />".prettyforumdate($row["postdate"])."</td><td style=\"background-color:#ffffff; vertical-align:top;\">".nl2br($row["content"])."</td></tr>\n";
            $count = 2;
        } else {
            $page .= "<tr><td width=\"25%\" style=\"background-color:#eeeeee; vertical-align:top;\"><span class=\"small\"><b>".$row["author"]."</b><br />Posts: ".$row3["postcount"]."<br /><br />".prettyforumdate($row["postdate"])."</td><td style=\"background-color:#eeeeee; vertical-align:top;\">".nl2br($row["content"])."</td></tr>\n";
            $count = 1;
        }
    }

    $page .= "</table></td></tr></table><br />";

$query = doquery("SELECT * FROM {{table}} WHERE id='$id' OR parent='$id' ORDER BY id LIMIT $start,50", "gforum");
$row = mysql_fetch_array($query);
if ($row["close"] == 1)  {
 $page .= "<center><img src=\"img/padlock.gif\"><br><b>This thread has been Closed</b></center><p>";

    } else {

    $page .= "<table width=\"100%\"><tr><td><b>Reply To This Thread:</b><br /><form action=\"admingforum.php?do=reply\" method=\"post\"><input type=\"hidden\" name=\"parent\" value=\"$id\" /><input type=\"hidden\" name=\"title\" value=\"Re: ".$row2["title"]."\" /><textarea name=\"content\" rows=\"7\" cols=\"40\"></textarea><br /><input type=\"submit\" name=\"submit\" value=\"Submit\" /> <input type=\"reset\" name=\"reset\" value=\"Reset\" /></form></td></tr></table>";

}

$page .= "You may return to the <a href=\"admingforum.php\">guild forum</a> main page, or use the compass on the right to start exploring.<br />\n";

    display($page, "Guild Forum");

}

function reply() {

    global $userrow;
	extract($_POST);

	$query = doquery("INSERT INTO {{table}} SET id='',postdate=NOW(),newpostdate=NOW(),author='".$userrow["charname"]."',parent='$parent',replies='0',title='$title',content='$content'", "gforum");
	$query2 = doquery("UPDATE {{table}} SET newpostdate=NOW(),replies=replies+1 WHERE id='$parent' LIMIT 1", "gforum");
        $query = doquery("UPDATE {{table}} SET postcount=postcount+1 WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	header("Location: admingforum.php?do=thread:$parent:0");
	die();

}

function newthread() {

    global $userrow;

    if (isset($_POST["submit"])) {

        extract($_POST);

        $query = doquery("INSERT INTO {{table}} SET id='',postdate=NOW(),newpostdate=NOW(),author='".$userrow["charname"]."',parent='0',replies='0',title='$title',content='$content'", "gforum");
        $query = doquery("UPDATE {{table}} SET postcount=postcount+1 WHERE id='".$userrow["id"]."' LIMIT 1", "users");
         header("Location: admingforum.php");
        die();
    }
     $page = "<table width='100%' border='1'><tr><td class='title'>Guild Forum - Create Thread</td></tr></table><p>";
    $page .= "<table width=\"100%\"><tr><td><b>Create a New Thread:</b><br /><br/ ><form action=\"admingforum.php?do=new\" method=\"post\">Title:<br /><input type=\"text\" name=\"title\" size=\"50\" maxlength=\"50\" /><br /><br />Message:<br /><textarea name=\"content\" rows=\"7\" cols=\"40\"></textarea><br /><br /><input type=\"submit\" name=\"submit\" value=\"Submit\" /> <input type=\"reset\" name=\"reset\" value=\"Reset\" /></form></td></tr></table>";
$page .= "You may return to the <a href=\"admingforum.php\">guild forum</a> main page, or use the compass on the right to start exploring.<br />\n";

display($page, "Guild Forum");

}

?>