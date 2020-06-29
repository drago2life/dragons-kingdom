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
<h1><? echo $controlrow["gamename"]; ?> Help: Skill Table</h1>
<p>[ <a href="helpguide.php">Return to Help</a> | <a href="index.php">Return to the game</a> ]</p>
<hr />
<?

echo "<br>";

$current_level = 0;

$color_change = 1;

?>


<table cellspacing="0" cellpadding="3" style="border: 1px solid #FFFFFF;">
	<tr>
		<td bgcolor="#FFFFFF" align="center" colspan="2"><b>Non-Combat Skills</b></td>
	</tr>
	<tr>
		<td width="80"><b>Level</b></td>
		<td width="80"><b>Experience</b></td>

		
	</tr>
	
<?

while ($current_level <= 249 ) {
	
	$xp_required = $current_level * 565 * $current_level + (($current_level * 25) * $current_level);
	$next_level = $current_level + 1;
	
	
	
	if($color_change == 0) {	
		echo "<tr><td>$next_level</td><td>$xp_required</td></tr>";
		$color_change = 1;
	}
	elseif($color_change == 1) {
		echo "<tr><td bgcolor=#FFFFFF>$next_level</td><td bgcolor=#FFFFFF>$xp_required</td></tr>";
		$color_change = 0;
	}

	
	$current_level++;
}

?>

</table>
<p>[ <a href="#top">Top</a> ] <br />
</p>
<table class="copyright" width="100%"><tr>
<td width="50%" align="center">Recommended for IE & Mozilla Firefox with Screen Resolution of 1024 x 768</td>
<td width="50%" align="right"><a href="http://dk-rpg.com" target="_new">Dragon's Kingdom</a> &copy; 2004 - Modified by Adam Dear     </td>
</tr></table>
</body>
</html>