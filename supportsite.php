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
<title><? echo $controlrow["gamename"]; ?> Support Site</title>
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
<body>
<table width="100%"><tr><td><center><img src="images/logo.gif" alt="Dragon's Kingdom" title="Dragon's Kingdom" border="0" />

<a name="top"></a>
<h1><? echo $controlrow["gamename"]; ?> Support Site </h1></center>
[ <a href="index.php">Return to the game</a> ]

<br /><br /><hr />

<p>Do you want to Support Dragon's Kingdom, and keep it alive? Well, look no further. There are a number of ways in which you can help DK to be improved, and to stay online.
<hr />
<strong>Donate:</strong>
<p>Use the Donate button below to donate money via Paypal. You can register an account on Paypal for free. However small your donation might be, it will help. Be sure to contact the administrator if you donate, since you will get some kind of reward, depending on your donation amount.
<p><form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but04.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
</form> 
<p>You dont have to donate via Paypal, you can also send Cash, Cheque or do a Bank Transfer. Use <a href="contact.php">this link</a> to get more information on this, such as an address to send cash to. You may also use <a href="http://www.xe.com/ucc/" target="_blank">this currency converter</a> too. </p>
<hr />
<strong>Buy DK Items for Real Cash: </strong>
<p>This is exactly what the name suggests. Buy items which are in Dragon's Kingdom, for real money/cash. If you wish to purchase either some gold, dragon scales, an item, or whatever you want, then please use <a href="store.php">this link</a>. If nothing on here interests you then you can contact support and state exactly what you want, and an estimated price. </p>
<hr />
<strong>Advertise DK:</strong>
<p>Do you have a website? This is a very simple thing to do, all you require is to place one of these 4 adverts onto your website, and make the image link to www.Domain.com. You can't get much simpler than that! Just use the code provided below to get the image to show, or you may save the image to your website space:</p>
<p><a href="http://dkcript.com"><img src="http://dkscript.com/gfx/banner1.gif" alt="Click here to play Dragons Kingdom, an Online Browser Based RPG!" border="0" /></a></p>
<p>
  <textarea name="textarea" cols="60" rows="3"><a href="http://dkscript.com"><img src="http://dkscript.com/gfx/banner1.gif" alt="Click here to play Dragons Kingdom, an Online Browser Based RPG!" border="0" /></a></textarea>
</p>
<p><a href="http://dkscript.com"><img src="http://dkscript.com/gfx/banner2.gif" alt="Click here to play Dragons Kingdom, an Online Browser Based RPG!" border="0" /></a></p>
<p><textarea name="textarea" cols="60" rows="3"><a href="http://dkscript.com"><img src="http://dkscript.com/gfx/banner2.gif" alt="Click here to play Dragons Kingdom, an Online Browser Based RPG!" border="0" /></a></textarea></p>
<p><a href="http://dkscript.com"><img src="http://dkscript.com/gfx/banner3.gif" alt="Click here to play Dragons Kingdom, an Online Browser Based RPG!" border="0" /></a></p>
<p>
  <textarea name="textarea2" cols="60" rows="3"><a href="http://dkscript.com"><img src="http://dkscript.com/gfx/banner3.gif" alt="Click here to play Dragons Kingdom, an Online Browser Based RPG!" border="0" /></a></textarea>
</p>
<p><a href="http://dkscript.com"><img src="http://dkscript.com/gfx/miniban.gif" alt="Click here to play Dragons Kingdom, an Online Browser Based RPG!" border="0" /></a>
<p>
<textarea name="textarea" cols="60" rows="3"><a href="http://dkscript.com"><img src="http://dkscript.com/gfx/miniban.gif" alt="Click here to play Dragons Kingdom, an Online Browser Based RPG!" border="0" /></a></textarea>
</p>
<hr />
<strong>Clicking Adverts: </strong>
<p>I'd say this is the most easiest way in which you can help. Everyday when you collect your Daily Bonus from the Daily Bonus Arena located <a href="search.php">here</a>, please spare a few seconds of your time to click those adverts. Each time you click and let the page load, DK gets a small amount of money, this is used to keep DK running.</p>
<p>Whichever option you might use from above, the Dragon's Kingdom owner wishs to Thank You for your support. </p>
<hr /><br />

<br /><br />
<table class="copyright" width="100%"><tr>
<td width="50%" align="center">Recommended for IE & Mozilla Firefox with Screen Resolution of 1024 x 768</td>
<td width="50%" align="right"><a href="http://dkscript.com" target="_new">DK Script</a> &copy; 2004-2006 Created by Adam Dear     </td>
</tr></table>
</body>
</html>
