<?php
$template = <<<THEVERYENDOFYOU
<head>
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
<body><center>
<table width="90%"><tr>
<td width="150" style="border-right: solid 1px black;">
<b><u>DK Moderator Panel</u></b><br /><br />
<font color=red><b>Links:</b></font><br />
<a href="mod.php">Staff Forum</a><br />
<a href="mod.php?do=mailadmin">Mail Admin</a><br />
<a href="../index.php">Game Home</a><br /><br />



<font color=red><b>Mod Menu:</b></font><br />
<a href="mod.php?do=chat">Edit & Delete Chat</a><br />
<a href="mod.php?do=closechat">Close Chat</a><br />
<a href="mod.php?do=users">Ban & Mute Accounts</a><br />
<a href="mod.php?do=onlineusers">Ban & Mute Online Accounts</a><br />
<a href="mod.php?do=viewcomments">Edit Comments</a><br />
<br><font color=red><b>Forum Menu:</b></font><br />
<a href="mod.php?do=general">Edit General</a><br />
<a href="mod.php?do=support">Edit Support</a><br />
<a href="mod.php?do=suggestion">Edit Suggestions</a><br />
<a href="mod.php?do=market">Edit Market Forum</a><br />
</td><td>
{{content}}
</table><br />
<table class="copyright" width="100%"><tr>
<td width="50%" align="center">Recommended for IE & Mozilla Firefox with Screen Resolution of 1024 x 768</td>
<td width="50%" align="right"><a href="http://dkscript.com" target="_new">DK Script</a> &copy; 2004 - 2006 - Created by Adam Dear     </td>
</tr></table>
</body>
</html>
THEVERYENDOFYOU;
?>