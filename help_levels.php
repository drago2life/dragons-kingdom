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
<h1><? echo $controlrow["gamename"]; ?> Help: Levels</h1>
[ <a href="helpguide.php">Return to Help</a> | <a href="index.php">Return to the game</a> ]

<br /><br /><hr />

<ul>
  <li />
  <a href="#1">Sorceress Table</a>
  <li />
  <a href="#2">Barbarian Table</a>
  <li />
  <a href="#3">Paladin Table</a>
  <li />
  <a href="#4">Ranger Table</a>
    <li />
  <a href="#5">Necromancer Table</a>
    <li />
  <a href="#6">Druid Table</a>
    <li />
  <a href="#7">Assassin Table</a>
</ul>
<a name="1" id="1"></a>
<table width="50%" style="border: solid 1px white" cellspacing="0" cellpadding="0">
<tr><td colspan="10" bgcolor="#ffffff"><center><b><? echo $controlrow["class1name"]; ?> Levels</b></center></td></tr>
<tr><td><b>Level</b><td><b>Exp.</b></td><td><b>Attributes</b></td><td><b>HP</b></td><td><b>MP</b></td><td><b>TP</b></td><td><b>AP</b></td><td><b>Strength</b></td><td><b>Dexterity</b></td><td><b>Spell</b></td></tr>
<?
$count = 1;
$itemsquery = doquery("SELECT id,1_attributes,1_exp,1_hp,1_mp,1_tp,1_ap,1_strength,1_dexterity,1_spells FROM {{table}} ORDER BY id", "levels");
$spellsquery = doquery("SELECT * FROM {{table}} ORDER BY id", "spells");
$spells = array();
while ($spellsrow = mysql_fetch_array($spellsquery)) {
    $spells[$spellsrow["id"]] = $spellsrow;
}
while ($itemsrow = mysql_fetch_array($itemsquery)) {
    if ($count == 1) { $color = "bgcolor=\"#ffffff\""; $count = 2; } else { $color = ""; $count = 1; }
    if ($itemsrow["1_spells"] != 0) { $spell = $spells[$itemsrow["1_spells"]]["name"]; } else { $spell = "<span class=\"light\">None</span>"; }
    if ($itemsrow["id"] != 131) { echo "<tr><td $color width=\"12%\">".$itemsrow["id"]."</td><td $color width=\"12%\">".number_format($itemsrow["1_exp"])."</td><td $color width=\"12%\">".$itemsrow["1_attributes"]."</td><td $color width=\"12%\">".$itemsrow["1_hp"]."</td><td $color width=\"12%\">".$itemsrow["1_mp"]."</td><td $color width=\"12%\">".$itemsrow["1_tp"]."</td><td $color width=\"12%\">".$itemsrow["1_ap"]."</td><td $color width=\"12%\">".$itemsrow["1_strength"]."</td><td $color width=\"12%\">".$itemsrow["1_dexterity"]."</td><td $color width=\"12%\">$spell</td></tr>\n"; }
}
?>
</table>
<p>[ <a href="#top">Top</a> ]</p>
<a name="2" id="2"></a>
<table width="50%" style="border: solid 1px white" cellspacing="0" cellpadding="0">
<tr><td colspan="10" bgcolor="#ffffff"><center><b><? echo $controlrow["class2name"]; ?> Levels</b></center></td></tr>
<tr><td><b>Level</b><td><b>Exp.</b></td><td><b>Attributes</b></td><td><b>HP</b></td><td><b>MP</b></td><td><b>TP</b></td><td><b>AP</b></td><td><b>Strength</b></td><td><b>Dexterity</b></td><td><b>Spell</b></td></tr>
<?
$count = 1;
$itemsquery = doquery("SELECT id,2_attributes,2_exp,2_hp,2_mp,2_tp,2_ap,2_strength,2_dexterity,2_spells FROM {{table}} ORDER BY id", "levels");
$spellsquery = doquery("SELECT * FROM {{table}} ORDER BY id", "spells");
$spells = array();
while ($spellsrow = mysql_fetch_array($spellsquery)) {
    $spells[$spellsrow["id"]] = $spellsrow;
}
while ($itemsrow = mysql_fetch_array($itemsquery)) {
    if ($count == 1) { $color = "bgcolor=\"#ffffff\""; $count = 2; } else { $color = ""; $count = 1; }
    if ($itemsrow["2_spells"] != 0) { $spell = $spells[$itemsrow["2_spells"]]["name"]; } else { $spell = "<span class=\"light\">None</span>"; }
    if ($itemsrow["id"] != 131) { echo "<tr><td $color width=\"12%\">".$itemsrow["id"]."</td><td $color width=\"12%\">".number_format($itemsrow["2_exp"])."</td><td $color width=\"12%\">".$itemsrow["2_attributes"]."</td><td $color width=\"12%\">".$itemsrow["2_hp"]."</td><td $color width=\"12%\">".$itemsrow["2_mp"]."</td><td $color width=\"12%\">".$itemsrow["2_tp"]."</td><td $color width=\"12%\">".$itemsrow["2_ap"]."</td><td $color width=\"12%\">".$itemsrow["2_strength"]."</td><td $color width=\"12%\">".$itemsrow["2_dexterity"]."</td><td $color width=\"12%\">$spell</td></tr>\n"; }
}
?>
</table>
<p>[ <a href="#top">Top</a> ] </p>
<a name="3" id="3"></a>
<table width="50%" style="border: solid 1px white" cellspacing="0" cellpadding="0">
<tr><td colspan="10" bgcolor="#ffffff"><center><b><? echo $controlrow["class3name"]; ?> Levels</b></center></td></tr>
<tr><td><b>Level</b><td><b>Exp.</b></td><td><b>Attributes</b></td><td><b>HP</b></td><td><b>MP</b></td><td><b>TP</b></td><td><b>AP</b></td><td><b>Strength</b></td><td><b>Dexterity</b></td><td><b>Spell</b></td></tr>
<?
$count = 1;
$itemsquery = doquery("SELECT id,3_attributes,3_exp,3_hp,3_mp,3_tp,3_ap,3_strength,3_dexterity,3_spells FROM {{table}} ORDER BY id", "levels");
$spellsquery = doquery("SELECT * FROM {{table}} ORDER BY id", "spells");
$spells = array();
while ($spellsrow = mysql_fetch_array($spellsquery)) {
    $spells[$spellsrow["id"]] = $spellsrow;
}
while ($itemsrow = mysql_fetch_array($itemsquery)) {
    if ($count == 1) { $color = "bgcolor=\"#ffffff\""; $count = 2; } else { $color = ""; $count = 1; }
    if ($itemsrow["3_spells"] != 0) { $spell = $spells[$itemsrow["3_spells"]]["name"]; } else { $spell = "<span class=\"light\">None</span>"; }
    if ($itemsrow["id"] != 131) { echo "<tr><td $color width=\"12%\">".$itemsrow["id"]."</td><td $color width=\"12%\">".number_format($itemsrow["3_exp"])."</td><td $color width=\"12%\">".$itemsrow["3_attributes"]."</td><td $color width=\"12%\">".$itemsrow["3_hp"]."</td><td $color width=\"12%\">".$itemsrow["3_mp"]."</td><td $color width=\"12%\">".$itemsrow["3_tp"]."</td><td $color width=\"12%\">".$itemsrow["3_ap"]."</td><td $color width=\"12%\">".$itemsrow["3_strength"]."</td><td $color width=\"12%\">".$itemsrow["3_dexterity"]."</td><td $color width=\"12%\">$spell</td></tr>\n"; }
}
?>
</table>
<p>[ <a href="#top">Top</a> ] </p>
<a name="4" id="4"></a>
<table width="50%" style="border: solid 1px white" cellspacing="0" cellpadding="0">
<tr><td colspan="10" bgcolor="#ffffff"><center><b><? echo $controlrow["class4name"]; ?> Levels</b></center></td></tr>
<tr><td><b>Level</b><td><b>Exp.</b></td><td><b>Attributes</b></td><td><b>HP</b></td><td><b>MP</b></td><td><b>TP</b></td><td><b>AP</b></td><td><b>Strength</b></td><td><b>Dexterity</b></td><td><b>Spell</b></td></tr>
<?
$count = 1;
$itemsquery = doquery("SELECT id,4_attributes,4_exp,4_hp,4_mp,4_tp,4_ap,4_strength,4_dexterity,4_spells FROM {{table}} ORDER BY id", "levels");
$spellsquery = doquery("SELECT * FROM {{table}} ORDER BY id", "spells");
$spells = array();
while ($spellsrow = mysql_fetch_array($spellsquery)) {
    $spells[$spellsrow["id"]] = $spellsrow;
}
while ($itemsrow = mysql_fetch_array($itemsquery)) {
    if ($count == 1) { $color = "bgcolor=\"#ffffff\""; $count = 2; } else { $color = ""; $count = 1; }
    if ($itemsrow["4_spells"] != 0) { $spell = $spells[$itemsrow["4_spells"]]["name"]; } else { $spell = "<span class=\"light\">None</span>"; }
    if ($itemsrow["id"] != 131) { echo "<tr><td $color width=\"12%\">".$itemsrow["id"]."</td><td $color width=\"12%\">".number_format($itemsrow["4_exp"])."</td><td $color width=\"12%\">".$itemsrow["4_attributes"]."</td><td $color width=\"12%\">".$itemsrow["4_hp"]."</td><td $color width=\"12%\">".$itemsrow["4_mp"]."</td><td $color width=\"12%\">".$itemsrow["4_tp"]."</td><td $color width=\"12%\">".$itemsrow["4_ap"]."</td><td $color width=\"12%\">".$itemsrow["4_strength"]."</td><td $color width=\"12%\">".$itemsrow["4_dexterity"]."</td><td $color width=\"12%\">$spell</td></tr>\n"; }
}
?>
</table>
<p>[ <a href="#top">Top</a> ] </p>
<a name="5" id="5"></a>
<table width="50%" style="border: solid 1px white" cellspacing="0" cellpadding="0">
<tr><td colspan="10" bgcolor="#ffffff"><center><b><? echo $controlrow["class5name"]; ?> Levels</b></center></td></tr>
<tr><td><b>Level</b><td><b>Exp.</b></td><td><b>Attributes</b></td><td><b>HP</b></td><td><b>MP</b></td><td><b>TP</b></td><td><b>AP</b></td><td><b>Strength</b></td><td><b>Dexterity</b></td><td><b>Spell</b></td></tr>
<?
$count = 1;
$itemsquery = doquery("SELECT id,5_attributes,5_exp,5_hp,5_mp,5_tp,5_ap,5_strength,5_dexterity,5_spells FROM {{table}} ORDER BY id", "levels");
$spellsquery = doquery("SELECT * FROM {{table}} ORDER BY id", "spells");
$spells = array();
while ($spellsrow = mysql_fetch_array($spellsquery)) {
    $spells[$spellsrow["id"]] = $spellsrow;
}
while ($itemsrow = mysql_fetch_array($itemsquery)) {
    if ($count == 1) { $color = "bgcolor=\"#ffffff\""; $count = 2; } else { $color = ""; $count = 1; }
    if ($itemsrow["5_spells"] != 0) { $spell = $spells[$itemsrow["5_spells"]]["name"]; } else { $spell = "<span class=\"light\">None</span>"; }
    if ($itemsrow["id"] != 131) { echo "<tr><td $color width=\"12%\">".$itemsrow["id"]."</td><td $color width=\"12%\">".number_format($itemsrow["5_exp"])."</td><td $color width=\"12%\">".$itemsrow["5_attributes"]."</td><td $color width=\"12%\">".$itemsrow["5_hp"]."</td><td $color width=\"12%\">".$itemsrow["5_mp"]."</td><td $color width=\"12%\">".$itemsrow["5_tp"]."</td><td $color width=\"12%\">".$itemsrow["5_ap"]."</td><td $color width=\"12%\">".$itemsrow["5_strength"]."</td><td $color width=\"12%\">".$itemsrow["5_dexterity"]."</td><td $color width=\"12%\">$spell</td></tr>\n"; }
}
?>
</table>
<p>[ <a href="#top">Top</a> ] </p>
<a name="6" id="6"></a>
<table width="50%" style="border: solid 1px white" cellspacing="0" cellpadding="0">
<tr><td colspan="10" bgcolor="#ffffff"><center><b><? echo $controlrow["class6name"]; ?> Levels</b></center></td></tr>
<tr><td><b>Level</b><td><b>Exp.</b></td><td><b>Attributes</b></td><td><b>HP</b></td><td><b>MP</b></td><td><b>TP</b></td><td><b>AP</b></td><td><b>Strength</b></td><td><b>Dexterity</b></td><td><b>Spell</b></td></tr>
<?
$count = 1;
$itemsquery = doquery("SELECT id,6_attributes,6_exp,6_hp,6_mp,6_tp,6_ap,6_strength,6_dexterity,6_spells FROM {{table}} ORDER BY id", "levels");
$spellsquery = doquery("SELECT * FROM {{table}} ORDER BY id", "spells");
$spells = array();
while ($spellsrow = mysql_fetch_array($spellsquery)) {
    $spells[$spellsrow["id"]] = $spellsrow;
}
while ($itemsrow = mysql_fetch_array($itemsquery)) {
    if ($count == 1) { $color = "bgcolor=\"#ffffff\""; $count = 2; } else { $color = ""; $count = 1; }
    if ($itemsrow["6_spells"] != 0) { $spell = $spells[$itemsrow["6_spells"]]["name"]; } else { $spell = "<span class=\"light\">None</span>"; }
    if ($itemsrow["id"] != 131) { echo "<tr><td $color width=\"12%\">".$itemsrow["id"]."</td><td $color width=\"12%\">".number_format($itemsrow["6_exp"])."</td><td $color width=\"12%\">".$itemsrow["6_attributes"]."</td><td $color width=\"12%\">".$itemsrow["6_hp"]."</td><td $color width=\"12%\">".$itemsrow["6_mp"]."</td><td $color width=\"12%\">".$itemsrow["6_tp"]."</td><td $color width=\"12%\">".$itemsrow["6_ap"]."</td><td $color width=\"12%\">".$itemsrow["6_strength"]."</td><td $color width=\"12%\">".$itemsrow["6_dexterity"]."</td><td $color width=\"12%\">$spell</td></tr>\n"; }
}
?>
</table>
<p>[ <a href="#top">Top</a> ] </p>
<a name="7" id="7"></a>
<table width="50%" style="border: solid 1px white" cellspacing="0" cellpadding="0">
<tr><td colspan="10" bgcolor="#ffffff"><center><b><? echo $controlrow["class7name"]; ?> Levels</b></center></td></tr>
<tr><td><b>Level</b><td><b>Exp.</b></td><td><b>Attributes</b></td><td><b>HP</b></td><td><b>MP</b></td><td><b>TP</b></td><td><b>AP</b></td><td><b>Strength</b></td><td><b>Dexterity</b></td><td><b>Spell</b></td></tr>
<?
$count = 1;
$itemsquery = doquery("SELECT id,7_attributes,7_exp,7_hp,7_mp,7_tp,7_ap,7_strength,7_dexterity,7_spells FROM {{table}} ORDER BY id", "levels");
$spellsquery = doquery("SELECT * FROM {{table}} ORDER BY id", "spells");
$spells = array();
while ($spellsrow = mysql_fetch_array($spellsquery)) {
    $spells[$spellsrow["id"]] = $spellsrow;
}
while ($itemsrow = mysql_fetch_array($itemsquery)) {
    if ($count == 1) { $color = "bgcolor=\"#ffffff\""; $count = 2; } else { $color = ""; $count = 1; }
    if ($itemsrow["7_spells"] != 0) { $spell = $spells[$itemsrow["7_spells"]]["name"]; } else { $spell = "<span class=\"light\">None</span>"; }
    if ($itemsrow["id"] != 131) { echo "<tr><td $color width=\"12%\">".$itemsrow["id"]."</td><td $color width=\"12%\">".number_format($itemsrow["7_exp"])."</td><td $color width=\"12%\">".$itemsrow["7_attributes"]."</td><td $color width=\"12%\">".$itemsrow["7_hp"]."</td><td $color width=\"12%\">".$itemsrow["7_mp"]."</td><td $color width=\"12%\">".$itemsrow["7_tp"]."</td><td $color width=\"12%\">".$itemsrow["7_ap"]."</td><td $color width=\"12%\">".$itemsrow["7_strength"]."</td><td $color width=\"12%\">".$itemsrow["7_dexterity"]."</td><td $color width=\"12%\">$spell</td></tr>\n"; }
}
?>
</table>
<p>[ <a href="#top">Top</a> ]</p>
<p>Experience points listed are total values up until that point. All other values are just the new amount that you gain for each level. </p>
<table class="copyright" width="100%"><tr>
<td width="50%" align="center">Recommended for IE & Mozilla Firefox with Screen Resolution of 1024 x 768</td>
<td width="50%" align="right"><a href="http://dk-rpg.com" target="_new">Dragon's Kingdom</a> &copy; 2004 - Modified by Adam Dear     </td>
</tr></table>
</body>
</html>