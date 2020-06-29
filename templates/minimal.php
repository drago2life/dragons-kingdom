<?php
$template = <<<THEVERYENDOFYOU
<head>

<META NAME="Title" CONTENT="Welcome to Dragons Kingdom - An online browser based RPG!">
<META NAME="Author" CONTENT="Adam Dear">
<META NAME="Subject" CONTENT="Dragons Kingdom Online Game">
<META NAME="Description" CONTENT="An online browser based RPG!">
<META NAME="Keywords" CONTENT="dragon, dragons, kingdom, rpg, role, playing, online game, strategy, multiplayer, game, text game, browser based game, browser, forums, forum, game forum, knight, mage, paladin, warrior, ranger, fun, medievil, old english, horse, rsbattle, runescape, space, planets, earth, ufo, spaceship">
<META NAME="Language" CONTENT="English">
<META NAME="Copyright" CONTENT="© Copyright © 2004 Dragons Kingdom">
<META NAME="Designer" CONTENT="Adam Dear">
<META NAME="Publisher" CONTENT="Adam Dear">
<META NAME="Revisit-After" CONTENT="7 Days">
<META NAME="Distribution" CONTENT="Global">
<META NAME="Robots" CONTENT="Index">

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
</head>
<body>
<script language="JavaScript1.2"> 

if (document.all){ 
document.onkeydown = function (){ 
var key_f5 = 116; // 116 = F5 

if (key_f5==event.keyCode){ 
event.keyCode = 27; 

return false; 
} 
} 
} 

</script> 

<center>
{{content}}
</center></body>
</html>
THEVERYENDOFYOU;
?>