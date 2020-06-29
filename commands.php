<?php 
include('cookies.php');
include('lib.php'); 
$link = opendb();
$controlquery = doquery("SELECT * FROM {{table}} WHERE id='1' LIMIT 1", "control");
$controlrow = mysql_fetch_array($controlquery);
ob_start("ob_gzhandler");

// Login (or verify) if not logged in.
$userrow = checkcookies();
if ($userrow == false) { 
    if (isset($_GET["do"])) {
        if ($_GET["do"] == "verify") { header("Location: users.php?do=verify"); die(); }
    }
    header("Location: login.php?do=login"); die(); 
}
// Close game.
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Commands</title>
<style type="text/css">

body {
  background-image: url(images/background.jpg);
  color: #000000;
  font: 10px verdana;
}
table {
  border-style: none;
  padding: 0px;
  font: 10px verdana;
}
td {
  border-style: none;
  padding: 3px;
  vertical-align: top;
}
td.top {
  border-bottom: solid 2px black;
}
td.left {
  width: 180px;
  border-right: solid 2px black;
}
td.right {
  width: 180px;
  border-left: solid 2px black;
}
a {
    color: #000000;
    text-decoration: none;
    font-weight: bold;
}
a:hover {
    color: #afafaf;
}
 
a.done:link, a.done:hover, a.done:visited{
    background-color: transparent;
	color: #999999;
	text-decoration: none; 
}
.small {
  font: 9px verdana;
}
.highlight {
  color: red;
}
.light {
  color: #000000;
}
.title {
  border: solid 1px black;
  background-color: #afafaf;
  font-weight: bold;
  padding: 4px;
  margin: 3px;
}
.copyright {
  border: solid 1px black;
  background-color: #afafaf;
  font: 9px verdana;
}
</style>
</head>
<body>[ <a href="chat.php">Return to Chat</a> ]<p>
  <br />
  Each smiley symbol is beside the image. </p>
<hr />
<p><img src="images/smilies/wink.gif" width="15" height="15" /> ;) <img src="images/smilies/mad.gif" width="15" height="15" /> :@ <img src="images/smilies/ques-tion.gif" width="15" height="15" /> :? <img src="images/smilies/biggrin.gif" width="15" height="15" /> (ha) </p>
<p><img src="images/smilies/umm.gif" width="15" height="15" /> :/ <img src="images/smilies/lol.gif" width="15" height="15" /> :D <img src="images/smilies/rolleyes.gif" width="15" height="15" /> ^^ <img src="images/smilies/cool.gif" width="15" height="15" /> (c) </p>
<p><img src="images/smilies/tongue.gif" width="15" height="15" /> :P <img src="images/smilies/freak.gif" width="15" height="15" /> o.O <img src="images/smilies/sad.gif" width="15" height="15" /> :( <img src="images/smilies/drool.gif" width="15" height="15" /> :% </p>
<p><img src="images/smilies/smile.gif" width="15" height="15" /> :) <img src="images/smilies/exclamation.gif" width="15" height="15" /> :! <img src="images/smilies/shocked.gif" width="15" height="15" /> :O <img src="images/smilies/embaressed.gif" width="15" height="15" /> :$ </p>

<p>You may also private message users within chat. If I was to private message a character name named "Bob" and ask him how he was, I would write the following:<p>
/m Bob Hi, how are you?<p>
<p>If there is any abuse of the chat, you will be banned. Please read the rules before posting.</p>
<p>If there are any serious problems, please report the posters name and details to the Administrator. </p>
<hr />
<p>&nbsp;</p>
</body>
</html>
