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
<b><u>DK Administration</u></b><br /><br />
<font color=red><b>Links:</b><br /></font>
<a href="admin.php">Staff Forum</a><br />
<a href="http://www.dkscript.com/admin/mod.php">Mod Panel</a><br />
<a href="../index.php">Game Home</a><br />
<a href="../admingforum.php">Guild Forum</a><br /><br />
<a href="status.php">Server Status</a><br />
<a href="clean.php">Clear Up Script</a><br /><br />

<font color=red><b>Primary Settings:</b><br /></font>
<a href="admin.php?do=main">Main Settings</a><br />
<a href="admin.php?do=news">Post News</a><br />
<a href="admin.php?do=viewnews">Edit News</a><br />
<a href="admin.php?do=viewcomments">Edit Comments</a><br />
<a href="admin.php?do=users">Edit Accounts</a> - <a href="http://www.dk-rpg.com/admin/admin.php?do=edituser:1">Admin</a><br />
<a href="admin.php?do=onlineusers">Edit Online Accounts</a><br />
<a href="admin.php?do=chat">Edit Chat</a><br /><br />

<font color=red><b>Game Mail:</b><br /></font>
<a href="admin.php?do=mailall">Global Mailing</a><br />
<a href="admin.php?do=mailmod">Mail Mods</a><br /><br />

<font color=red><b>Game Settings:</b><br /></font>
<a href="http://www.dkscript.com/demo/poll.php?do=admin:addpoll">Add Poll</a> - <a href="http://www.dkscript.com/demo/poll.php?do=admin:closepoll">Close Poll</a><br />
<a href="admin.php?do=guilds">Edit Guilds</a><br />
<a href="admin.php?do=strongholds">Edit Strongholds</a><br />
<a href="admin.php?do=items">Edit Items</a><br />
<a href="admin.php?do=drops">Edit Drops</a><br />
<a href="admin.php?do=towns">Edit Towns</a><br />
<a href="admin.php?do=monsters">Edit Monsters</a><br />
<a href="admin.php?do=levels">Edit Levels</a><br />
<a href="admin.php?do=spells">Edit Spells</a><br /><p>
<font color=red><b>Forums:</b><br /></font>
<a href="admin.php?do=gforum">Edit Guild Forum</a><br />
<a href="admin.php?do=general">Edit General</a><br />
<a href="admin.php?do=support">Edit Support</a><br />
<a href="admin.php?do=suggestion">Edit Suggestions</a><br />
<a href="admin.php?do=market">Edit Market Forum</a><br />
</td><td>
{{content}}
</table><br />
<table class="copyright" width="100%"><tr>
<td width="50%" align="center">Recommended for IE & Mozilla Firefox with Screen Resolution of 1024 x 768</td>
<td width="50%" align="right"><a href="http://dkscript.com" target="_new">DK Script</a> &copy; 2004-2006 Created by Adam Dear     </td>
</tr></table>
</body>
</html>
THEVERYENDOFYOU;
?>