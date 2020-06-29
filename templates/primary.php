<?php
// Server load

$loadresult = @exec('uptime');
preg_match("/load average: ([0-9.]+), ([0-9.]+), ([0-9.]+)/",$loadresult,$avgs);


global $script;

$template = <<<THEVERYENDOFYOU
<html><head>
<script>
function popUp(url){
	window.open(url,"pop","width=650,height=380,toolbars=0,scrollbars=1")
}

function popUp(URL) {
day = new Date();
id = day.getTime();
eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=350');");
}

</script>

<META NAME="Title" CONTENT="Welcome to Dragons Kingdom - An online browser based RPG!">
<META NAME="Author" CONTENT="Adam Dear">
<META NAME="Subject" CONTENT="Dragons Kingdom Online Game">
<META NAME="Description" CONTENT="An online browser based text RPG!">
<META NAME="Keywords" CONTENT="dragon, dragons, kingdom, rpg, role, playing, online game, strategy, multiplayer, adventure, game, text game, browser based game, browser, forums, forum, game forum, paladin, sorceress, barbarian, druid, ranger, assassin, fun, medievil, old english, horse, blacksmith, knight, rsbattle, runescape">
<META NAME="Language" CONTENT="English">
<META NAME="Copyright" CONTENT="Copyright - 2004 - 2005 Dragon's Kingdom">
<META NAME="Designer" CONTENT="Adam Dear">
<META NAME="Publisher" CONTENT="Adam Dear">
<META NAME="Revisit-After" CONTENT="7 Days">
<META NAME="Distribution" CONTENT="Global">
<META NAME="Robots" CONTENT="Index">
<link rel="shortcut icon" href="images/fav.ico" type="image/x-icon" />

<title>{{title}}</title>
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
.news {
  font: 12px verdana;
  font-weight: bold;
  color: #336666;  
}
.profile {
  font: 9px verdana;
  font-weight: none;
  color: #000000;  
}
.light {
  color: #336666;
  
}
.title {
  border: solid 1px black;
  background-color: #999999;
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

<script>
var compassy = 0;
var mining = 0;

function openmappopup()
{
    var popurl="index.php?do=showmap";
    winpops=window.open(popurl,"","width=650,height=635,scrollbars");
}

function move() {
    document.compass.direction.click();
}

function setdir(dir) {
    if (document.compass.direction.disabled)
	document.compass.direction.disabled = false;
    document.compass.direction.value=dir;
}

function getkey(e) {
    if (compassy != 1 || document.forms.length != 3) 
	return;

    var pK = e ? e.which:window.event.keyCode;
    switch (pK) {
	case 56:
	    dir="North";
	    break;
	case 54:
	    dir="East";
	    break;
	case 50:
	    dir="South";
	    break;
	case 52:
	    dir="West";
	    break;
	case 55:
	    dir="North West";
	    break;
	case 57:
	    dir="North East";
	    break;
	case 51:
	    dir="South East";
	    break;
	case 49:
	    dir="South West";
	    break;
	default:
	    dir=false;
	    break;
    }
if (dir) {
  setdir(dir);
  move();
}}

// Compass

function carrot() {
if (compassy == 1)
{document.getElementById('compassdiv').style.visibility = "hidden";
document.getElementById('warningdiv').innerHTML = "Compass hidden to prevent Power Clicking and reduce Server Load.";
document.getElementById('warningdiv').style.visibility = "visible";
compassy++;
}else{setTimeout("anticarrot()",600);}
}
function anticarrot() {
document.getElementById('compassdiv').style.visibility = "visible";
document.getElementById('warningdiv').style.visibility = "hidden";
compassy++}

function carrot2(tag) {  
	  document.getElementById(tag).style.visibility = 'hidden';  
}

function hide(tag) {  
	  document.getElementById(tag).style.visibility = 'hidden';
	  document.getElementById(tag+"div").style.visibility = "visible";
	  setTimeout("antihide('"+tag+"')",2000); 
    
}

function antihide(tag) {
    document.getElementById(tag).style.visibility = 'visible';  
    document.getElementById(tag+"div").style.visibility = 'hidden';
} 

document.onkeypress = getkey;
</script>
</head>
<body onload="carrot();$script"><center>

<table cellspacing="0" width="90%"><tr>
<td class="top" colspan="3">
  <center><table width="100%"><tr><td><center><img src="images/logo.gif" alt="{{dkgamename}}" title="{{dkgamename}}" border="0" /><br>{{mailimage}}</center></td><td style="text-align:right; vertical-align:middle;"></td></tr></table>
</td>
</tr><tr>
<td rowspan="2" class="left">{{leftnav}}</td>
<td class="middle">{{content}}<br></td>
</p></td>
<td rowspan="2" class="right">{{rightnav}}</td>
</tr>
</table><br>
<table class="copyright" width="100%"><tr>
<td width="40%" align="left">[Recommended for <a href="http://mozilla.org/products/firefox/" target="_new">Firefox</a> & <a href="http://microsoft.com" target="_new">IE</a> with Screen Resolution of 1024 x 768]</td>
<td width="25%" align="center">[Queries Used: {{numqueries}}] - [Server Load: $avgs[1]]<br>[Load Time: {{totaltime}}]</td>
<td width="35%" align="right"> [<a href="http://dkscript.com" target="_new">DK Script</a> &copy; 2004-2006 Created by Adam Dear]</td>
</tr></table>
<center><p>
  <p>  
  <p>
    <script type="text/javascript"><!--
google_ad_client = "pub-0173924128190324";
google_ad_width = 468;
google_ad_height = 60;
google_ad_format = "468x60_as";
google_ad_type = "text_image";
google_ad_channel ="";
google_color_border = "999999";
google_color_bg = "cccccc";
google_color_link = "000000";
google_color_url = "666666";
google_color_text = "333333";
//--></script>
    <script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
  </script>
  </center>



</body>
</html>
THEVERYENDOFYOU;
?>