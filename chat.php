<head>
<META HTTP-EQUIV="Refresh"
CONTENT="60; URL=chat.php">
<title>Player Chat</title>
</head>
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
<?php //

echo "<table width=\"100%\"><tr>
  <td width=\"50%\" class=\"title\"><div align=\"left\">Player Chat - [<a href=\"javascript: self.close()\">Close</a>]</div></td>
  <td width=\"50%\" class=\"title\"><div align=\"right\">[<a href=\"javascript:location.reload()\" target=\"_self\">Refresh Chat</a>] [<A HREF=\"commands.php\">Commands</A>]</div></td>
</tr></table><br><iframe src=\"index.php?do=chat\" name=\"sbox\" width=\"100%\" height=\"275\" frameborder=\"0\" id=\"bbox\">Your browser does not support inline frames! The Player Chat will not be available until you upgrade to a newer <a href=http://www.mozilla.org target=_new>browser</a>.</iframe>"
?>