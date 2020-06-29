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
<h1><? echo $controlrow["gamename"]; ?> Help: Monsters</h1>
[ <a href="helpguide.php">Return to Help</a> | <a href="index.php">Return to the game</a> ]

<br /><br /><hr />

<table width="85%" style="border: solid 1px white" cellspacing="0" cellpadding="0">
<tr><td colspan="12" bgcolor="#ffffff"><center>
  <b>Monsters Statistics - Equipped Items </b>
</center></td></tr>
<tr><td><b>Name</b></td><td><b>Max HP</b></td><td><b>Max Damage</b></td><td><b>Armor</b></td><td><b>Level</b></td><td><b>Bones</b></td>
<td><b>Max Exp</b></td>
<td><b>Max Gold</b></td><td><b>Immunity</b></td>
  <td><strong>Weapon</strong></td>
  <td><strong>Armor</strong></td>
  <td><strong>Shield</strong></td>
</tr>
<?
$count = 1;
$itemsquery = doquery("SELECT * FROM {{table}} WHERE boss!='1' ORDER BY level LIMIT 247", "monsters");
while ($itemsrow = mysql_fetch_array($itemsquery)) {
    if ($count == 1) { $color = "bgcolor=\"#ffffff\""; $count = 2; } else { $color = ""; $count = 1; }
    if ($itemsrow["immune"] == 0) { $immune = "<span class=\"light\">None</span>"; } elseif ($itemsrow["immune"] == 1) { $immune = "Hurt"; } else { $immune = "Hurt & Sleep"; }
    echo "<tr><td $color width=\"30%\">".$itemsrow["name"]."</td><td $color width=\"10%\">".$itemsrow["maxhp"]."</td><td $color width=\"10%\">".$itemsrow["maxdam"]."</td><td $color width=\"10%\">".$itemsrow["armor"]."</td><td $color width=\"10%\">".$itemsrow["level"]."</td><td $color width=\"10%\">".$itemsrow["bones"]."</td><td $color width=\"10%\">".$itemsrow["maxexp"]."</td><td $color width=\"10%\">".$itemsrow["maxgold"]."</td><td $color width=\"20%\">$immune</td><td $color width=\"10%\">".$itemsrow["cweap"]."</td><td $color width=\"10%\">".$itemsrow["carm"]."</td><td $color width=\"10%\">".$itemsrow["cshield"]."</td></tr>\n";
}
?>
</table>
<br />
<li /><i><b>Note:</b> The King Black Dragon is the most strongest monster within the game, and he can appear anywhere throughout the map, not just at the Dragon's Kingdom. He will be difficult to kill, but will provide the best reward. There are also some monsters which are not listed here because they are either special, or quest monsters.</i>
<p>
<table class="copyright" width="100%"><tr>
<td width="50%" align="center">Recommended for IE & Mozilla Firefox with Screen Resolution of 1024 x 768</td>
<td width="50%" align="right"><a href="http://dk-rpg.com" target="_new">Dragon's Kingdom</a> &copy; 2004 - Modified by Adam Dear     </td>
</tr></table>
</body>
</html>