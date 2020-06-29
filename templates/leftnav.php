<?php
$template = <<<THEVERYENDOFYOU
<table width="100%">
<tr><td class="title"><img src="images/button_character.gif" alt="Character" title="Character" /></td></tr>
<tr><td>
<font color=#336666><b>{{charname}}</b></font><br />
Level: {{level}}<br />
Gold: {{gold}}<br />
Stored Gold: {{bank}}<br />
Dragon Scales: {{dscales}}<br />
Bones: {{bones}}<br />
Guild: {{guildname}}<br /><br />
Experience: {{experience}} <br />
Mining Exp: {{miningxp}} <br />
Smelting Exp: {{smeltingxp}} <br />
Forging Exp: {{forgingxp}} <br />
Endurance Exp: {{endurancexp}} <br />
Crafting Exp: {{craftingxp}}<br />
  Total Exp: {{totalexp}}<br />
    Skill Total: {{skilltotal}}<br />
    Attributes: {{attributes}} - <a href="index.php?do=attributes">Spend</a><br /><br />
<center>
{{statbars}}</center>
</td></tr>
</table>
<br />
<table width="100%">
<tr><td class="title"><img src="images/button_fastitems.gif" alt="Quick Items" title="Quick Items" /></td></tr>
<tr><td>
<center><a href="index.php?do=viewitems">View All</a><p></center>
{{inventitemslist}}
</td></tr>
</table><br />
<table width="100%">
<tr><td class="title"><img src="images/button_menu.gif" alt="Main Menu" title="Main Menu" /></td></tr>
<tr><td>
{{adminlink}}
{{modlink}}

<DL>
<IMG SRC="images/icon_arrow.gif" ALT="Latest News"> <a href="index.php?do=news">Latest News</a> - <a href="index.php?do=archive">Archive</a><br />
<IMG SRC="images/icon_arrow.gif" ALT="Launch Player Chat"> <A HREF="javascript:popUp('chat.php')">Launch Player Chat</A> <b>({{chatonline}})</b><br />
<IMG SRC="images/icon_arrow.gif" ALT="Game Mail"> <a href="gamemail.php">Game Mail</a><font color=red><b> {{newmail}}</b></font><br />
<IMG SRC="images/icon_arrow.gif" ALT="Whos Online"> <a href="index.php?do=whosonline">Who is Online</a> <b>({{numonline}})</b><br />
<IMG SRC="images/icon_arrow.gif" ALT="DK Rules"> <a href="helpguide.php?do=rules">DK Rules</a><br />
<IMG SRC="images/icon_arrow.gif" ALT="Game Forums"> <a href="index.php?do=forums">Game Forums</a><br />
<p>
<IMG SRC="images/icon_arrow.gif" ALT="Help Guide"> <a href="helpguide.php" target="_blank">Help Guide</a> <br />
<IMG SRC="images/icon_arrow.gif" ALT="Contact Support"> <a href="contact.php">Contact Support</a><br />

<IMG SRC="images/icon_arrow.gif" ALT="View Polls"> <a href="poll.php">View Polls</a><br />
<IMG SRC="images/icon_arrow.gif" ALT="Player Options"> <a href="index.php?do=options">Player Options</a><br />
<IMG SRC="images/icon_arrow.gif" ALT="Log Out"> <a href="login.php?do=logout">Log Out</a>
</DL>
</td></tr>
</table>


THEVERYENDOFYOU;
?>