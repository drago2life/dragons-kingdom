<?php // other.php contains other stuff which doesnt fit anywhere else such as options

function runon() {
global $userrow, $controlrow;

	    $updatequery = doquery("UPDATE {{table}} SET run='3' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
    header("Location: index.php");
}

function runoff() {
global $userrow, $controlrow;

	    $updatequery = doquery("UPDATE {{table}} SET run='1' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
    header("Location: index.php");
}

function upgrade() { // options page

    global $userrow, $numqueries;

        if (isset($_POST["submit"])) {

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if(mysql_num_rows($townquery) != 1) { die("Cheat attempt sent to administrator."); }
    $townrow = mysql_fetch_array($townquery);

    } elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

    } else {

$updatequery = doquery("UPDATE {{table}} SET location='Upgrade Account' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

        $title = "Upgrade Account";
        $page = "<table width='100%' border='1'><tr><td class='title'>Upgrade Account</td></tr></table><p>";
        $page .= "In the very near future (Still in development), you will be able to upgrade your account and purchase a Premium Membership. More information will be available soon, but here is a list of extra features which Premium Members will get:<p>New Quests<br>More Skills<br>Access to the Castle and Dungeons (different areas to explore)<br>New member items<br>Plus several more features.<p>Check back soon for more information on pricing and updates.<p>\n";
        $page .= "</ul><br />You may return to what you were <a href=\"index.php\">doing</a>, or use the compass on the right to start exploring.<br />\n";


    }

    display($page, $title);

}

function options() { // options page

    global $userrow, $numqueries;

        if (isset($_POST["submit"])) {

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if(mysql_num_rows($townquery) != 1) { die("Cheat attempt sent to administrator."); }
    $townrow = mysql_fetch_array($townquery);

    } elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

    } else {

$updatequery = doquery("UPDATE {{table}} SET location='Player Options' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

        $title = "Player Options";
        $page = "<table width='100%' border='1'><tr><td class='title'>Player Options</td></tr></table><p>";
        $page .= "Welcome to your Player Options. Here you can change your account information such as your password and contact details. Feel free to report a bug or problem by using the appropriate link below, for live support.<p><ul><li /><a href=\"index.php?do=profile\">Edit Profile</a><li /><a href=\"index.php?do=dueloption\">Duel Option</a><li /><a href=\"index.php?do=hideplayers\">Hide Nearby Players</a><li /><a href=\"index.php?do=changeavatar\">Change Avatar</a><li /><a href=\"users.php?do=changepassword\">Change Password</a><li /><a href=\"index.php?do=notes\">Player Notepad</a><li /><a href=\"index.php?do=users\">Player List</a></ul><p>\n";
        $page .= "</ul><br />You may return to what you were <a href=\"index.php\">doing</a>, or use the compass on the right to start exploring.<br />\n";


    }

    display($page, $title);

}

function notes() {
	global $userrow;
$update2query = doquery("UPDATE {{table}} SET location='Player Notepad' WHERE id='".$userrow["id"]."' LIMIT 1", "users");


	if (isset($_POST["submit"])) {
		$notes = $_POST["notes"];
		$notes = my_htmlspecialchars($notes);
$updatequery = doquery("UPDATE {{table}} SET notes='$notes' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	} elseif (isset($_POST["cancel"])) {
        header("Location: index.php"); die();

	}
    $userquery = doquery("SELECT * FROM {{table}} WHERE id='".$userrow["id"]."' LIMIT 1", "users");
    $userrow = mysql_fetch_array($userquery);
    

	$page = "<table width='100%'><tr><td class='title'>Player Notepad</td></tr></table>";
	$page .= "<p>Welcome to your Personal Player Notepad. Here you can take note of anything that you wish to remember. No one else can see this page but you. Some notes you may wish to save are Soul numbers*, or anything else to help you in your quest to victory.<p>";
	$page .= "<form action='index.php?do=notes' method='POST'><center><table width='75%'>";
    $page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><td colspan=\"0\" style=\"background-color:#dddddd;\"><b>Private Notepad</td></tr>\n";
	$page .= "<td bgcolor='#ffffff'><textarea name='notes' cols='60' rows='10' wrap='virtual'>";
	$page .= $userrow["notes"]."</textarea></td></tr>";
$page .= "<tr><td bgcolor='#eeeeee' colspan='2'> </td></tr>";
	$page .= "<tr><td bgcolor='#ffffff' colspan='2'> ";
	$page .= "<center><input type='submit' name='submit' value='Save Changes'>        -        ";
	$page .= "<input type='submit' name='cancel' value='Cancel'></center></td></tr></table></table></center>";

    	$page .= "<p><i>*Soul numbers become invalid after a short period of time. They merely dissapear.</i><p><center><br>Return to what you were <a href='index.php'>doing</a>.</center>";
    	display($page,"Player Notepad");

}

function profile() {
	global $userrow;
$update2query = doquery("UPDATE {{table}} SET location='Editing Profile' WHERE id='".$userrow["id"]."' LIMIT 1", "users");


	if (isset($_POST["submit"])) {
		$name = $_POST["name"];
		$gender = $_POST["gender"];
		$country = $_POST["country"];
		$msn = $_POST["msn"];
		$aim = $_POST["aim"];
		$yim = $_POST["yim"];
		$icq = $_POST["icq"];
$updatequery = doquery("UPDATE {{table}} SET name='$name',gender='$gender',country='$country',msn='$msn',aim='$aim',yim='$yim',icq='$icq' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	} elseif (isset($_POST["cancel"])) {
        header("Location: index.php"); die();

	}
    $userquery = doquery("SELECT * FROM {{table}} WHERE id='".$userrow["id"]."' LIMIT 1", "users");
    $userrow = mysql_fetch_array($userquery);
    

	$page = "<table width='100%'><tr><td class='title'>Edit Player Profile</td></tr></table>";
	$page .= "<p>Welcome to your Personal Profile. Here you can fill in the information below which will be displayed in your Profiles so that other players can easily contact you, and know a little bit about you. If you do not wish to display your contact information then please dont fill in the spaces below.<p>";
	$page .= "<form action='index.php?do=profile' method='POST'><center><table width='75%'>";
	    $page .= "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><td colspan=\"0\" style=\"background-color:#dddddd;\"><b>Edit your Profile</td></tr>\n";
	$page .= "<tr><td bgcolor='#eeeeee'>Your Name:<p><i>Display at your own risk.</i></td>";
	$page .= "<td bgcolor='#eeeeee'><input type='text' name='name' value='".$userrow["name"]."' size='25' maxlength='25'></td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'>Gender:<p><i>Male or Female only!</i></td>";
	$page .= "<td bgcolor='#ffffff'><select name='gender'><option value='Unknown'>Unknown</option><option value='Male'>Male</option><option value='Female'>Female</option></select></td></tr>";
        $page .= "<tr><td bgcolor='#eeeeee'>Country/Location:<p><i>Your country or location</i></td>";
	$page .= "<td bgcolor='#eeeeee'><input type='text' name='country' value='".$userrow["country"]."' size='15' maxlength='15'></td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'>MSN Messenger:<p><i>(MSN)</i></td>";
	$page .= "<td bgcolor='#ffffff'><input type='text' name='msn' value='".$userrow["msn"]."' size='30' maxlength='35'></td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'>AOL Instant Messenger:<p><i>(AIM)</i></td>";
	$page .= "<td bgcolor='#eeeeee'><input type='text' name='aim' value='".$userrow["aim"]."' size='30' maxlength='35'></td></tr>";
	$page .= "<tr><td bgcolor='#ffffff'>Yahoo Instant Messenger:<p><i>(YIM)</i></td>";
	$page .= "<td bgcolor='#ffffff'><input type='text' name='yim' value='".$userrow["yim"]."' size='30' maxlength='35'></td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee'>ICQ:</td>";
	$page .= "<td bgcolor='#eeeeee'><input type='text' name='icq' value='".$userrow["icq"]."' size='30' maxlength='35'></td></tr>";
        $page .= "<tr><td bgcolor='#ffffff' colspan='2'> </td></tr>";
	$page .= "<tr><td bgcolor='#eeeeee' colspan='2'> ";
	$page .= "<center><input type='submit' name='submit' value='Update Profile'>        -        ";
	$page .= "<input type='submit' name='cancel' value='Cancel'></form></center></td></tr></table></table></center>";

    	$page .= "<p><center><br>Return to what you were <a href='index.php'>doing</a>.</center>";
    	display($page,"Edit Player Profile");

}

function forums() { // Main forums

    global $userrow, $numqueries;
			$numquery = doquery("SELECT * FROM {{table}} WHERE parent='0'", "general");
			$genthreads = mysql_num_rows($numquery);
			
						$numquery = doquery("SELECT * FROM {{table}} WHERE parent='0'", "support");
			$supthreads = mysql_num_rows($numquery);
			
						$numquery = doquery("SELECT * FROM {{table}} WHERE parent='0'", "suggestions");
			$sugthreads = mysql_num_rows($numquery);
			
									$numquery = doquery("SELECT * FROM {{table}} WHERE parent='0'", "marketforum");
			$marthreads = mysql_num_rows($numquery);
			
									$numquery = doquery("SELECT * FROM {{table}} WHERE guildname='".$userrow["guildname"]."'", "gforum");
			$guildthreads = mysql_num_rows($numquery);
			
        if (isset($_POST["submit"])) {

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if(mysql_num_rows($townquery) != 1) { die("Cheat attempt sent to administrator."); }
    $townrow = mysql_fetch_array($townquery);

    } elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

    } else {

$updatequery = doquery("UPDATE {{table}} SET location='Game Forums' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

        $title = "Game Forums";
        $page = "<table width='100%' border='1'><tr><td class='title'>Game Forums</td></tr></table><p>";
        $page .= "Welcome to the Game Forums. You must remember to follow the current rules, otherwise you may be found banned.<p>Please do not Spam, and report all bugs via the contact admin link and dont post them in here.<br><hr /><li /><a href=\"general.php\">General Discussion</a><b> - Threads: $genthreads</b><br>Discuss anything in here which doesnt fit into any other catergory. It <u>must</u> be related to DK.<hr /><li /><a href=\"support.php\">Help and Support</a><b> - Threads: $supthreads</b><br>If you have a question, or need some help then post here. Be sure to view the Help Guide first.<hr /><li /><a href=\"suggestions.php\">Suggestions and Improvements</a><b> - Threads: $sugthreads</b><br>If you have a suggestion or an improvement that you wish to see in this game then post it here. Most good suggestions are eventually added in the near future.<hr /><li /><a href=\"mforum.php\">Player Market Forum</a><b> - Threads: $marthreads</b><br>Feel free to post your Market trades and requests in here. Either advertise your item, or request an item. Spam and abuse will not be tolerated.<hr /><li /><a href=\"gforum.php\">Private Guild Forum</a><b> - Threads: $guildthreads</b><br>You must be in a guild to access this area. Post in your Guilds Forum to discuss anything related to your Guild. You are in the ".$userrow["guildname"]." Guild.<hr /><br><font color=red>If you post in a totally inapproriate and wrong section, your post may well be deleted and you will have a warning, which will then lead to a ban. Do <u>not</U> spam the forums.</font><br>\n";
        $page .= "</ul><br />You may return to what you were <a href=\"index.php\">doing</a>, or use the compass on the right to start exploring.<br />\n";


    }

    display($page, $title);

}

function mailadmin() {

    global $userrow;
$updatequery = doquery("UPDATE {{table}} SET location='Reporting a Bug' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

    if (isset($_POST["submit"])) {
        extract($_POST);
    $content = str_replace("'", "\'", $content);
    $content = trim($content);
	  $content = "<b><font color=red><u>Game Mail sent to Administrator</u>:</b></font>\n\n". $content;
        $content = str_replace("'", "\'", $content);
	  $subject = "". $subject;
	  $c = 0;
    $page = "<table width='100%'><tr><td class='title'>Game Mail Administrator</td></tr></table><p>";
        $mailallquery = doquery("SELECT charname FROM {{table}} WHERE charname='Admin'", "users");
	  while ($charrow = mysql_fetch_array($mailallquery)) {
		$recipient = $charrow["charname"];
		$c += 1;
           	$query = doquery("INSERT INTO {{table}} SET postdate=NOW(),author='".$userrow["charname"]."',recipient='$recipient',subject='$subject',content='$content'", "gamemail");

        }

    	  $page .= "Your message has been sent to the Administrator. He will reply soon, please allow upto 48 hours.<p><br />You may return to what you were <a href=\"index.php\">doing</a>, or use the compass on the right to start exploring.";
    	      display($page, "Game Mail Admin");
    }

    $page = "<table width='100%'><tr><td class='title'>Game Mail Administrator</td></tr></table><p>";
    $page .= "Enter the message below and it will be sent to the Administrator.<p>Please do not abuse this feature otherwise you <u>will</u> be banned. Only send serious matters through this Mailing System.<p>";
    $page .= "<form action=\"index.php?do=mailadmin\" method=\"post\">";
    $page .= "Subject:<br />";
    $page .= "<input type=\"text\" name=\"subject\" size=\"50\" value=\"Bug Report\" maxlength=\"50\" /><br><br>";
    $page .= "Message:<br />";
    $page .= "<textarea name=\"content\" rows=\"7\" cols=\"40\"></textarea><br><br>";
    $page .= "<input type=\"submit\" name=\"submit\" value=\"Send Game Mail\" /> ";
    $page .= "<input type=\"reset\" name=\"reset\" value=\"Reset\" />";
    $page .= "</form>";
        $page .= "<br />You may return to what you were <a href=\"index.php\">doing</a>, or use the compass on the right to start exploring.<br />\n";
    display($page, "Game Mail Admin");

}

function doarchive() {
global $userrow, $controlrow;
$updatequery = doquery("UPDATE {{table}} SET location='News Archive' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

 $newsquery = doquery("SELECT * FROM {{table}} ORDER BY id DESC ", "news");
    $townrow["news"] = "<table width=\"100%\"><tr><td class=\"title\">News Archive - Since July 18th 2004</td><td></td><td></td>";

    $townrow["news"] .= "<tr><td><p><a name=\"top\"></a>[<a href=\"#bottom\">Go to Bottom</a>]<p>Return to <a href='index.php'>town</a>, or continue exploring using the compass images to the right. Please note that this page may take a while to load.<p>";
    while ($newsrow = mysql_fetch_array($newsquery)) {
    	$townrow["news"] .= "<span class=\"news\">[".prettydate($newsrow["postdate"])."] ".$newsrow["title"]." - By ".$newsrow["author"]."</span><br /><br />".nl2br($newsrow["content"])."<hr />";
	}
        $townrow["news"] .= "</td></tr></table>\n";

	$page = $townrow["news"]."<p>";

	$page .= "<a name=\"bottom\"></a>[<a href=\"#top\">Go to Top</a>]<p>Return to <a href='index.php'>town</a>, or continue exploring using the compass images to the right.<p>";
    display($page,"News Archive");

}

function changeavatar() { // 

global $userrow, $numqueries;


$updatequery = doquery("UPDATE {{table}} SET location='Change Avatar' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
if (isset($_POST["submit"])) {
$customtitle = $_POST["customtitle"];
$avatarlink = $_POST["avatarlink"];
$title = "Change Avatar";
$query = doquery("UPDATE {{table}} SET customtitle='$customtitle',avatarlink='$avatarlink' WHERE id='".$userrow["id"]."' LIMIT 1","users");
$page = "<table width='100%'><tr><td class='title'>Change Avatar</td></tr></table><p>";
$page .= "Avatar changed successfully.<p>You may return to edit your avatar some <a href=\"index.php?do=changeavatar\">more</a>, return to <a href=\"index.php\">town</a>, or continue exploring using the compass images to the right.<br /> \n";

}
elseif($userrow["authlevel"] == "3") {
$title = "Change Avatar";
$page = "<table width='100%'><tr><td class='title'>Change Avatar</td></tr></table><p>";
$page .= "You may add a 60x60pixels or less Avatar to your Character. Moderators can have upto 80x80 in size. This image will appear in all Forum posts, Game Mails and your Profile.<br /><br />\n";
$page .= "<form action=\"index.php?do=changeavatar\" method=\"post\">\n";
$page .= "<b>Rollover Title:</b><br /> \n";
$page .= "<input type=\"text\" name=\"customtitle\" size=\"30\" maxlength=\"30\" /><br />\n";
$page .= "<b>Avatar Image Link:</b><br /> \n";
$page .= "<input type=\"text\" name=\"avatarlink\" size=\"60\" value=\"http://www\" maxlength=\"120\" /><br />\n";
$page .= "<input type=\"submit\" name=\"submit\" value=\"Upload\" /> <input type=\"reset\" name=\"reset\" value=\"Clear\" />\n";
$page .= "</form>\n";
$titi = $userrow["customtitle"];
$titi2 = $userrow["avatarlink"];
$page .= "<b>Current Avatar:</b><br><br>\n";
$page .= "<img src=\"$titi2\" alt=\"$titi\" width=\"80\" height=\"80\"><p>The default image link is: www.yourdomain.com/gfx/defaultavatar.gif \n";
$page .= "<p>You may return to what you were <a href=\"index.php\">doing</a>, or continue exploring using the compass images to the right.<br /> \n";
$page .= "<br><br>\n";

} else {
$title = "Change Avatar";
$page = "<table width='100%'><tr><td class='title'>Change Avatar</td></tr></table><p>";
$page .= "You may add a 60x60pixels or less Avatar to your Character. Moderators can have upto 80x80 in size. This image will appear in all Forum posts, Game Mails and your Profile.<br /><br />\n";
$page .= "<form action=\"index.php?do=changeavatar\" method=\"post\">\n";
$page .= "<b>Rollover Title:</b><br /> \n";
$page .= "<input type=\"text\" name=\"customtitle\" size=\"30\" maxlength=\"30\" /><br />\n";
$page .= "<b>Avatar Image Link:</b><br /> \n";
$page .= "<input type=\"text\" name=\"avatarlink\" size=\"60\" value=\"http://www\" maxlength=\"120\" /><br />\n";
$page .= "<input type=\"submit\" name=\"submit\" value=\"Upload\" /> <input type=\"reset\" name=\"reset\" value=\"Clear\" />\n";
$page .= "</form>\n";
$titi = $userrow["customtitle"];
$titi2 = $userrow["avatarlink"];
$page .= "<b>Current Avatar:</b><br><br>\n";
$page .= "<img src=\"$titi2\" alt=\"$titi\" width=\"60\" height=\"60\"><p>The default image link is: www.yourdomain.com/gfx/defaultavatar.gif \n";
$page .= "<p>You may return to what you were <a href=\"index.php\">doing</a>, or continue exploring using the compass images to the right.<br /> \n";
$page .= "<br><br>\n";

}

display($page, $title);

}

function dolistmembers ($filter) {
	global $userrow;

	if (!isset($filter)) { $filter = "A";}

	$page = "<table width='100%'><tr><td class='title'>Player List</td></tr></table>";
	$page .= "<center><p>Here is the current list of Players in DK. Accounts are regularly deleted when they become inactive. To view the profiles of Players, click on their name. All are listed in alphabetical order.<p>";

	$page .= "[ <a href='index.php?do=users:A'>A</a> ";
	$page .= " <a href='index.php?do=users:B'>B</a> ";
	$page .= " <a href='index.php?do=users:C'>C</a> ";
	$page .= " <a href='index.php?do=users:D'>D</a> ";
	$page .= " <a href='index.php?do=users:E'>E</a> ";
	$page .= " <a href='index.php?do=users:F'>F</a> ";
	$page .= " <a href='index.php?do=users:G'>G</a> ";
	$page .= " <a href='index.php?do=users:H'>H</a> ";
	$page .= " <a href='index.php?do=users:I'>I</a> ";
	$page .= " <a href='index.php?do=users:J'>J</a> ";
	$page .= " <a href='index.php?do=users:K'>K</a> ";
	$page .= " <a href='index.php?do=users:L'>L</a> ";
	$page .= " <a href='index.php?do=users:M'>M</a> ";
	$page .= " <a href='index.php?do=users:N'>N</a> ";
	$page .= " <a href='index.php?do=users:O'>O</a> ";
	$page .= " <a href='index.php?do=users:P'>P</a> ";
	$page .= " <a href='index.php?do=users:Q'>Q</a> ";
	$page .= " <a href='index.php?do=users:R'>R</a> ";
	$page .= " <a href='index.php?do=users:S'>S</a> ";
	$page .= " <a href='index.php?do=users:T'>T</a> ";
	$page .= " <a href='index.php?do=users:U'>U</a> ";
	$page .= " <a href='index.php?do=users:V'>V</a> ";
	$page .= " <a href='index.php?do=users:W'>W</a> ";
	$page .= " <a href='index.php?do=users:X'>X</a> ";
	$page .= " <a href='index.php?do=users:Y'>Y</a> ";
	$page .= " <a href='index.php?do=users:Z'>Z</a> ]<br></center>";
	$charquery = doquery("SELECT * FROM {{table}} WHERE charname LIKE '".$filter."%' ORDER by charname", "users");
$updatequery = doquery("UPDATE {{table}} SET location='Player List' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	$page .= "<center><table width='90%' style='border: solid 1px black' cellspacing='0' cellpadding='0'>";
	$page .= "<center><tr><td colspan=\"7\" bgcolor=\"#ffffff\"><center><b>Dragons Kingdom Players</b></center></td></tr>";
	$page .= "<tr><td><b>Name</b></td><td><b>Last Login</b></td><td><b>Level</b></td><td><b>Status</b></td></tr>";
	$count = 2;
	$rankquery = doquery("SELECT * FROM {{table}} WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
	$rankrow = mysql_fetch_array($rankquery);
	while ($charrow = mysql_fetch_array($charquery)) {

		if ($charrow["authlevel"] == "0"){ $rank = "<b>Member</b>";}
		if ($charrow["authlevel"] == "3"){ $rank = "<font color=green><b>Moderator</b></font>";}
		if ($charrow["authlevel"] == "1"){ $rank = "<font color=red><b>Administrator</b></font>";}
		if ($charrow["authlevel"] == "2"){ $rank = "<b>Banned</b>";}
		
		if ($count == 1) { $color = "bgcolor='#ffffff'"; $count = 2; }
		else { $color = "bgcolor='#eeeeee'"; $count = 1;}
		$page .= "<tr><td ".$color." width='15%'>";
		if ($userrow["guildrank"] >= 100) {
		$page .= "<a href='index.php?do=onlinechar:".$charrow["id"]."'>".$charrow["charname"]."</a>";}
		else {
		$page .= "<a href='index.php?do=onlinechar:".$charrow["id"]."'>".$charrow["charname"]."</a>";}
		$page .= "</td>";
		$page .= "<td ".$color." width='25%'>".$charrow["onlinetime"]."</td>";
		$page .= "<td ".$color." width='5%'>".$charrow["level"]."</td>";
		$page .= "<td ".$color." width='20%'>".$rank."</td>";
	  	$page .= "</tr>";
	}
	$page .= "</table></center>";
	$page .= "<center><br><a href='index.php'>Return to the Game</a></center>";

	display($page, "Players of Dragons Kingdom");

}

function dueloption() { // Switch duel on and off, from appearing in duel arena

    global $userrow, $numqueries;

        if (isset($_POST["submit"])) { //Allow player to appear in duel arena


        $query = doquery("UPDATE {{table}} SET duellist='1' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Duel Option";
        $page = "<table width='100%' border='1'><tr><td class='title'>Duel Option</td></tr></table><p>";
        $page .= "You now appear on the Duel Arena dueling list.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.";


    } elseif (isset($_POST["cancel"])) { //Disallow

        $query = doquery("UPDATE {{table}} SET duellist='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Duel Option";
        $page = "<table width='100%' border='1'><tr><td class='title'>Duel Option</td></tr></table><p>";
        $page .= "You now don't appear, on the Duel Arena dueling list.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.";


    } else {
$updatequery = doquery("UPDATE {{table}} SET location='Duel Option' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Duel Option";
        $page = "<table width='100%' border='1'><tr><td class='title'>Duel Option</td></tr></table><p>";
        $page .= "Select whether you wish to appear in the Duel Arena, and have Dueling Requests sent to you while you are playing.<p>";
        $page .= "<form action=\"index.php?do=dueloption\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Allow Requests\" /> <input type=\"submit\" name=\"cancel\" value=\"Disallow Requests\" />\n";
        $page .= "</form>\n";$page .= "</ul><br />You may return to what you were <a href=\"index.php\">doing</a>, or use the compass on the right to start exploring.<br />\n";


    }

    display($page, $title);

}

function hideplayers() { // Hide the option to view nearby players while exploring

    global $userrow, $numqueries;

        if (isset($_POST["submit"])) { //Show


        $query = doquery("UPDATE {{table}} SET nearbylist='1' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Nearby Players Option";
        $page = "<table width='100%' border='1'><tr><td class='title'>Nearby Players Option</td></tr></table><p>";
        $page .= "You can now see Nearby Players while exploring.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.";


    } elseif (isset($_POST["cancel"])) { //Hide

        $query = doquery("UPDATE {{table}} SET nearbylist='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Nearby Players Option";
        $page = "<table width='100%' border='1'><tr><td class='title'>Nearby Players Option</td></tr></table><p>";
        $page .= "You now don't see the Nearby Players list while exploring.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.";


    } else {
$updatequery = doquery("UPDATE {{table}} SET location='Nearby Players Option' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Nearby Players Option";
        $page = "<table width='100%' border='1'><tr><td class='title'>Nearby Players Option</td></tr></table><p>";
        $page .= "Select whether you wish to view the Nearby Players list while exploring.<p>";
        $page .= "<form action=\"index.php?do=hideplayers\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Show\" /> <input type=\"submit\" name=\"cancel\" value=\"Hide\" />\n";
        $page .= "</form>\n";$page .= "</ul><br />You may return to what you were <a href=\"index.php\">doing</a>, or use the compass on the right to start exploring.<br />\n";


    }

    display($page, $title);

}

function contact() {
	global $userrow;
$updatequery = doquery("UPDATE {{table}} SET location='Reporting a Bug' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	if (!isset($_POST["submit"])) {
	$page = "<table width='100%'><tr><td class='title'>Contact Support</td></tr></table>";
	$page .= "<table border='1' width='100%'><tr><td>";
	$page .= "<p>If you have a problem, found a bug or for a different reason, then please feel free to contact me using the form below.";
	$page .= "<p><b>Please don't abuse this feature. Only use the form below if it is important otherwise your email will be ignored. If you registered with a fake email then I will not be able to reply to you. All fields are required.</b><p>";
	$page .= "<FORM method='POST' action='index.php?do=contact'>";
	$page .= "<INPUT type='hidden' name='sender_name' value='".$userrow["charname"]."'>";
	$page .= "<INPUT type='hidden' name='sender_email' value='".$userrow["email"]."'>";
	$page .= "<P>Subject:<br>";
	$page .= "<INPUT type='text' name='subject' size='40'>";
	$page .= "<P>Message:<br>";
	$page .= "<textarea name='message' cols='40' rows='10'></textarea><br>";
	$page .= "<INPUT type='submit' name='submit' value='Send Email'>";
    $page .= " <input type=\"reset\" name=\"reset\" value=\"Reset\" /> ";
	$page .= "</FORM></td></tr></table>";
		$page .="<p> You may return to <a href='index.php'>town</a>, or continue exploring using the compass on the right.<p>";
    		display ($page, "Contact Support");

	} else {
		$msg = "Sender Name:\t$_POST[sender_name]\n";
		$msg .= "Sender E-Mail:\t$_POST[sender_email]\n";
		$msg .= "Message:\t$_POST[message]\n\n";
		$recipient = "support@dk-rpg.com";
		$subject = "[Bug Report]: $_POST[subject]\n";
		$mailheaders = "From: $_POST[sender_email]";
		mail( $recipient, $subject, $msg, $mailheaders );


	$page = "<table width='100%'><tr><td class='title'>Contact Support</td></tr></table>";
		$page .= "<p><H3 align=center>Thank you, ".$_POST[sender_name]."</H3>";
		$page .= "<P>Your bug report or problem has been sent to support@dk-rpg.com. Please allow upto 36 hours for a reply (usually a lot sooner), you can view your message and information from below:<br>";
		$page .= "<p><strong>Character Name: ".$_POST[sender_name]."<br>";
		$page .= "Your E-Mail: ".$_POST[sender_email]."<br>";
		$page .= "Subject: ".$_POST[subject]."<br>";
		$page .= "Message: ".$_POST[message]."<p></p></strong>";
		$page .="<p> You may return to <a href='index.php'>town</a>, or continue exploring using the compass on the right.<p>";

    display ($page, "Contact Support");

	}
}
?>
