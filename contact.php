<?php 
include('cookies.php');
include('lib.php'); 
$link = opendb();
$controlquery = doquery("SELECT * FROM {{table}} WHERE id='1' LIMIT 1", "control");
$controlrow = mysql_fetch_array($controlquery);
ob_start("ob_gzhandler");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><? echo $controlrow["gamename"]; ?> Contact Support</title>
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
  background-color: #999999;
  font: 9px verdana;
}
</style>
</head>
<body>
<table width="100%"><tr><td><center><img src="images/logo.gif" alt="Dragon's Kingdom" title="Dragon's Kingdom" border="0" />

<a name="top"></a>
<h1><? echo $controlrow["gamename"]; ?> Contact Support</h1>
 </center>
[ <a href="index.php">Return to the game</a> ]
  <br />
  <br />
  
If you have a problem, found a bug  or for a different reason, then please feel free to contact us on: support@yourdomain.com. Please remember to give as much information as possible, along with your username/character name.</p>
<p>We aim to reply within 24 hours.</p>
  <br />
<hr />
<a name="top" id="top"></a><br />
<br /><br /><br /><br /><p><p><p>
<table class="copyright" width="95%"><tr>
<td width="50%" align="center">Recommended for IE & Mozilla Firefox with Screen Resolution of 1024 x 768</td>
<td width="50%" align="right"><a href="http://dkscript.com" target="_new">DK Script</a> &copy; 2004-2006 Created by Adam Dear     </td>
</tr></table>
</body>
</html>




