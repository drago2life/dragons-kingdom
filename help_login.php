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
<h1><? echo $controlrow["gamename"]; ?> Help: Login Support </h1>
<p>[ <a href="helpguide.php">Return to Help</a> | <a href="index.php">Return to the game</a> ]

  <br />
</p>
<p>If you have reached this page then you are probably struggling to login. Here is all the information you require to successfully login if you are having difficulties. If you still can't login, please feel free to contact support via email by clicking <a href="contact.php">here</a>. </p>
<p>The main reason why people can't login is due to them not having Cookies enabled correctly. There are two ways of enabling cookies, the 'cookies 1' below is the most easiest way to enable them, but if you still can't enable them then check out 'cookies 2' by following the information below.</p>
<p><ul>
<li />
<a href="#easy">Cookies 1 - Can't Login, my login page flash's! </a>
<li />
<a href="#hard">Cookies 2 - Can't Login</a><br />
<hr />
<br />
<h3><a name="easy" id="easy"></a>Cookies 1: Can't Login - My password and username appears to be correct, but the page simply flash's and sends me back to the login page.</h3>
<p> 1) This is the most easiest way of accomplishing enabling cookies, its also the safest way (recommended). Thanks to Dave Mongoose for this little step by step guide. <br />
  <br />
If you use Internet Explorer and don't want to allow cookies from all sites, but want to play the game, there's a way to only allow this site. Open up Internet Explorer and... <br />
<br />
<br />
<br />
Go to the 'Tools' menu at the top and choose 'Internet Options'. <br />
<br />
Click the tab labelled 'Security', then click the 'Edit' button at the bottom. <br />
<br />
Type 'www.dk-rpg.com', then click 'Allow'. </p>
<p>[ <a href="#top">Top</a> ] </p>
<p>
<h3><a name="hard" id="hard"></a>Cookies 2: I Still Can't Login.</h3>
</p>
<p> Find your Web Browser in the list below to find out exactly how to enable cookies correctly: <br />
  <br />
Internet Explorer 6.x <br />
<br />
1. From the Tools menu choose Internet Options... <br />
2. Go to the Privacy tab <br />
3. You can either: <br />
* Slide the selection bar up or down to use a pre-set configuration <br />
- OR - <br />
* Click on Advanced... <br />
* Check the Override automatic cookie handling box, and select your preference <br />
<br />
Internet Explorer 5.x <br />
<br />
1. From the Tools menu choose Internet Options... <br />
2. Go to the Security tab <br />
3. Click on Custom Level... <br />
4. Scroll down until you see the Cookie options and select your preference <br />
<br />
Internet Explorer 4.x <br />
<br />
1. From the View menu choose Internet Options... <br />
2. Click on the Advanced tab <br />
3. Scroll down to the Security section and choose one of the three options <br />
<br />
Internet Explorer 3.x <br />
<br />
Follow the same instructions above (steps 1-3) then click on the button that says Warn Before Accepting Cookies. <br />
<br />
________________________________________________ <br />
<br />
Netscape Version 7.X <br />
<br />
1. Go to Edit on top of browser <br />
2. Choose Preferences on bottom of list <br />
3. Open the Privacy &amp; Security menu on left hand side of box. <br />
4. On right hand side of box choose either: <br />
* Disable Cookies <br />
* Enable cookies for the originating web site only <br />
* Enable cookies based on privacy settings <br />
* Enable all cookies <br />
5. Press OK <br />
<br />
Netscape Version 6.X <br />
<br />
1. Go to Edit on top of browser <br />
2. Choose Preferences on bottom of list <br />
3. Open the Privacy &amp; Security menu on left hand side of box. <br />
4. On right hand side of box choose either: <br />
* Disable Cookies <br />
* Enable cookies for the originating web site only <br />
* Enable all cookies <br />
5. Press OK <br />
<br />
Netscape Version 4.X <br />
<br />
1. Go to Edit on top of browser <br />
2. Choose Preferences on bottom of list <br />
3. Click on Advanced on left hand side of box. <br />
4. On right hand side of box choose either: <br />
* Accept all cookies <br />
* Accept only cookies that get sent back to the originating server <br />
* Disable cookies <br />
5. Press OK <br />
<br />
Netscape Version 3.X <br />
<br />
1. Go to Options on top of browser <br />
2. Choose Network Preferences from the list <br />
3. Click on the Protocols tab <br />
4. Uncheck the "Accepting a Cookie" checkbox <br />
5. Press OK <br />
<br />
________________________________________________ <br />
<br />
Mozilla 1.x <br />
<br />
1. On the Edit menu, click Preferences. <br />
2. Double-click Privacy &amp; Security. <br />
3. Click Cookies and select Enable All Cookies. <br />
4. Double-click Advanced and click Scripts &amp; Windows (Mozilla 1.1/1.2) or Scripts &amp; Plugins (Mozilla 1.3). <br />
5. Under Allow webpages to, select Create or change cookies and Read Cookies. <br />
6. Click OK. <br />
<br />
_________________________________________________ <br />
<br />
Opera Version 7.X <br />
<br />
1. Go to File on top of browser <br />
2. Choose Preferences on bottom of list <br />
3. Click on Privacy on left hand side of box. <br />
4. On right hand side of box check the " Enable cookies" checkbox. <br />
5. On right hand side of box select an option from the first drop-down list: <br />
* Do not accept cookies <br />
* Display received cookies <br />
* Accept only cookies from selected servers <br />
* Automatically accept all cookies <br />
6. On right hand side of box select an option from the second drop-down list: <br />
* Only accept cookies for the server <br />
* Do not accept third-party cookies <br />
* Display third-party cookies <br />
* Accept from any servers <br />
7. In addition, Opera also provides these options: <br />
* Throw away new cookies on exit <br />
* Display warning for illegal domains <br />
* Accept illegal path <br />
* Display warning for illegal path <br />
8. Press OK <br />
<br />
Opera Version 6.X <br />
<br />
1. Go to File on top of browser <br />
2. Choose Preferences on bottom of list <br />
3. Click on Privacy on left hand side of box. <br />
4. On right hand side of box check the " Enable cookies" checkbox. <br />
5. On right hand side of box select an option from the first drop-down list: <br />
* Do not accept cookies <br />
* Display received cookies <br />
* Accept only cookies from selected servers <br />
* Automatically accept all cookies <br />
6. On right hand side of box select an option from the second drop-down list: <br />
* Only accept cookies for the server <br />
* Do not accept third-party cookies <br />
* Display third-party cookies <br />
* Accept from any servers <br />
7. In addition, Opera also provides these options: <br />
* Throw away new cookies on exit <br />
* Display warning for illegal domains <br />
* Display warning for illegal path <br />
8. Press OK <br />
<br />
Opera Version 5.X <br />
<br />
1. Go to File on top of browser <br />
2. Choose Preferences on bottom of list <br />
3. Click on Privacy on left hand side of box. <br />
4. On right hand side of box select an option from the first drop-down list: <br />
* Do not accept cookies <br />
* Display received cookies <br />
* Accept only cookies from selected servers <br />
* Automatically accept all cookies <br />
5. On right hand side of box select an option from the second drop-down list: <br />
* Only accept cookies for the server <br />
* Do not accept third-party cookies <br />
* Display third-party cookies <br />
* Accept from any servers <br />
6. In addition, Opera also provides these options: <br />
* Throw away new cookies on exit <br />
* Display warning for illegal domains <br />
* Display warning for illegal path <br />
7. Press OK <br />
<br />
________________________________________________ <br />
<br />
AOL 8.0 <br />
<br />
1. From the AOL Toolbar, select Settings. <br />
2. Select Preferences <br />
3. Select Internet Properties (WWW) <br />
4. Select the Privacy tab <br />
5. Select Advanced <br />
6. Deselect override automatic cookie handling button <br />
7. Click OK to exit. <br />
<br />
AOL 7.0 with IE 6.x <br />
<br />
1. From the AOL Toolbar, select Settings. <br />
2. Select Preferences <br />
3. Select Internet Properties (WWW) <br />
4. Select the Privacy tab <br />
5. Select Advanced <br />
6. Deselect override automatic cookie handling button <br />
7. Click OK to exit. <br />
<br />
AOL 7.0 with IE 5.5 <br />
<br />
1. From the AOL Toolbar, select Settings. <br />
2. Select Preferences <br />
3. Select Internet Properties (WWW) <br />
4. Select the Security tab <br />
5. Select the Custom Level tab <br />
6. Under "Allow Cookies that are stored on your computer" click "Enable" <br />
7. Under "Allow per-session cookies (not stored)" click "Enable" <br />
8. Select OK, Yes you want to save the settings <br />
<br />
AOL 6.0 <br />
<br />
1. From the AOL Toolbar, select Settings <br />
2. Select Preferences <br />
3. Select Internet Properties (WWW) <br />
4. Select the Security tab <br />
5. Select the Custom Level tab <br />
6. Under "Allow Cookies that are stored on your computer" click "Enable" <br />
7. Under "Allow per-session cookies (not stored)" click "Enable" <br />
8. Select OK, Yes you want to save the settings <br />
<br />
AOL 5.0 <br />
<br />
1. Go to My AOL <br />
2. Pick WWW <br />
3. Click the Security tab <br />
4. Go to Custom Level <br />
5. Scroll down to find Cookie <br />
6. Click "Enable" <br />
7. Click OK <br />
<br />
AOL 4.0 <br />
<br />
1. Click on Preferences <br />
2. Select on the WWW button <br />
3. Click on the Advanced tab <br />
4. Select the "Accept all cookies" checkbox <br />
<br />
AOL for Windows 3.1 <br />
<br />
Browser does not give you the ability to turn off cookies <br />
<br />
________________________________________________________________ <br />
<br />
<br />
How Do I Delete Cookies? <br />
If you are currently using: <br />
<br />
Internet Explorer 6.x <br />
<br />
1. From the Tools menu choose Internet Options... <br />
2. Go to the General tab <br />
3. You can either: <br />
* Click on Delete Cookies... <br />
* Click on Ok to delete all cookies <br />
- OR - <br />
* Click on Settings... <br />
* Click on View Files... <br />
* From the files listed, find the ones beginning with Cookie and selectively delete the ones you no longer want <br />
<br />
Internet Explorer 5.x <br />
<br />
1. From the Tools menu choose Internet Options... <br />
2. Go to the General tab <br />
3. Click on Settings... <br />
4. Click on View Files... <br />
5. From the files listed, find the ones beginning with Cookie and selectively delete the ones you no longer want <br />
<br />
Internet Explorer 4.x <br />
<br />
1. From the View menu choose Internet Options... <br />
2. Go to the General tab <br />
3. Click on Settings... <br />
4. Click on View Files... <br />
5. From the Edit menu choose Preferences and click Cookies. Selectively delete the cookies you no longer want <br />
<br />
Internet Explorer 3.x <br />
<br />
1. From the View menu choose Options... <br />
2. Click on Advanced <br />
3. Click on View Files <br />
4. From the files listed, selectively delete the cookies you no longer want from the View Files window <br />
<br />
_________________________________________________ <br />
<br />
Netscape Communicator 6.x <br />
<br />
1. From the Edit menu choose Preferences <br />
2. Click on Privacy and Secuity <br />
3. In the box labeled Cookies select View Stored Cookies <br />
4. You can either: <br />
* Click Remove All Cookies to delete all cookies <br />
- OR - <br />
* Click on a cookie you want to remove <br />
* Click Remove Cookie to delete that cookie <br />
<br />
Netscape Communicator 4.x <br />
<br />
1. Netscape bundles all cookies into one file on your hard drive. You'll need to find the file, called cookie.txt on Windows machines (MagicCookie on Macintosh computers) and delete it. <br />
<br />
_________________________________________________ <br />
<br />
Opera All versions <br />
<br />
* Open Opera File Explorer. <br />
* Click File--Open. <br />
* Click Cookies. <br />
* Select the Cookie file. This is cookies4.dat in the main Opera directory. <br />
* Select all cookies from site www.livejournal.com and livejournal.com. <br />
* Click delete. <br />
* Close and relaunch your browser. <br />
<br />
_________________________________________________ <br />
<br />
Mozilla 9+ <br />
<br />
1. From the Mozilla web browser, select Edit --&gt; Preferences from the menu bar. The Preferences dialog box will open. <br />
2. Double-click the Privacy &amp; Security category. <br />
3. Highlight Cookies from the categories. The cookies options will appear in the panel on the right-hand side of the dialog box. <br />
4. Click the Manage Stored Cookies button to open the Cookie Manager dialog box. <br />
5. Click the Site tab to sort the cookies by server name. <br />
6. Scroll down to the livejournal.com and highlight a cookie. <br />
Important: Make sure the "Don't allow removed cookies to be reaccepted later" checkbox is unchecked. <br />
7. Select the Remove Cookie button. <br />
8. Repeat with each cookie from livejournal.com and www.livejournal.com. <br />
9. Select the OK button to finish deleting the cookies and close the Cookie Manager dialog box. <br />
10. Click OK in the Preferences dialog box to save the preferences and close the dialog box. <br />
<br />
________________________________________________ <br />
<br />
AOL <br />
<br />
From the AOL toolbar, click Settings, click Preferences. <br />
From Preferences, click Internet Properties (WWW). <br />
In the Temporary Internet files section, click Delete Files. <br />
Click OK. In addition to removing information from the cache, if you'd like to remove Web material you have saved for offline review, click to select Delete all offline content. <br />
Click OK. <br />
</p>
<p>[ <a href="#top">Top</a> ]  </p>
<hr />
<table class="copyright" width="100%"><tr>
<td width="50%" align="center">Recommended for IE & Mozilla Firefox with Screen Resolution of 1024 x 768</td>
<td width="50%" align="right"><a href="http://dk-rpg.com" target="_new">Dragon's Kingdom</a> &copy; 2004 - Modified by Adam Dear     </td>
</tr></table>
</body>
</html>