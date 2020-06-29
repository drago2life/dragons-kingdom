<?php 
include('lib.php'); 
$link = opendb();
$controlquery = doquery("SELECT * FROM {{table}} WHERE id='1' LIMIT 1", "control");
$controlrow = mysql_fetch_array($controlquery);
ob_start("ob_gzhandler");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><? echo $controlrow["gamename"]; ?> Help</title>
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
<a name="top"></a>
<h1><? echo $controlrow["gamename"]; ?> Help: Spells</h1>
[ <a href="helpguide.php">Return to Help</a> | <a href="index.php">Return to the game</a> ]

<br /><br /><hr />

<table width="50%" style="border: solid 1px white" cellspacing="0" cellpadding="0">
<tr><td colspan="8" bgcolor="#ffffff"><center><b>Spells</b></center></td></tr>
<tr><td><b>Name</b></td><td><b>Cost</b></td><td><b>Type</b></td><td><b>Attribute</b></td></tr>
<?
$count = 1;
$itemsquery = doquery("SELECT * FROM {{table}} ORDER BY id", "spells");
while ($itemsrow = mysql_fetch_array($itemsquery)) {
    if ($count == 1) { $color = "bgcolor=\"#ffffff\""; $count = 2; } else { $color = ""; $count = 1; }
    if ($itemsrow["type"] == 1) { $type = "Heal"; }
    elseif ($itemsrow["type"] == 2) { $type = "Hurt"; }
    elseif ($itemsrow["type"] == 3) { $type = "Sleep"; }
    elseif ($itemsrow["type"] == 4) { $type = "+Damage (%)"; }
    elseif ($itemsrow["type"] == 5) { $type = "+Defense (%)"; }
	elseif ($itemsrow["type"] == 6) { $type = "Capture"; }
    echo "<tr><td $color width=\"25%\">".$itemsrow["name"]."</td><td $color width=\"25%\">".$itemsrow["mp"]."</td><td $color width=\"25%\">$type</td><td $color width=\"25%\">".$itemsrow["attribute"]."</td></tr>\n";
}
?>
</table>
<ul>
<li /><b>Heal</b> spells always give you the maximum amount possible, until your current HP is full.
<li /><b>Hurt</b> spells deal X damage (not always the maximum) to the monster, regardless of the monster's armor.
<li /><b>Sleep</b> spells put the monster to sleep. The monster has an X in 15 chance of remaining asleep each turn.
<li /><b>+Damage</b> spells increase your total attack damage by X percent until the end of the fight.
<li /><b>+Defense</b> spells reduce the total damage you take from the monster by X percent until the end of each fight.
<li />
<b>Capture</b> spells capture pets to train and duel other people's pets in the pet arena. The level it has, is the maximum monster level that you can capture with that spell. 
</ul>
<table class="copyright" width="100%"><tr>
<td width="50%" align="center">Recommended for IE & Mozilla Firefox with Screen Resolution of 1024 x 768</td>
<td width="50%" align="right"><a href="http://dk-rpg.com" target="_new">Dragon's Kingdom</a> &copy; 2004 - Modified by Adam Dear     </td>
</tr></table>
</body>
</html>