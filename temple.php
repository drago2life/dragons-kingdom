<?php // temple.php :: Temple of Rebirth Altars.
require_once('lib.php');

//include('login.php');
include('cookies.php');
$link = opendb();
$userrow = checkcookies();
if ($userrow == false) {
	//die("X");
    if (isset($_GET["do"])) {
        if ($_GET["do"] == "verify") { header("Location: users.php?do=verify"); die(); }
    }
    header("Location: login.php?do=login"); die();
}

$controlquery = doquery("SELECT * FROM {{table}} WHERE id='1' LIMIT 1", "control");
$controlrow = mysql_fetch_array($controlquery);
$page = "<center><a href='index.php'>Return to the Game</a><br></center>";
if ($controlrow["gameopen"] == 0) { 
			header("Location: index.php"); die();
}
//Must vote
if (($userrow["poll"] != "Voted") && ($userrow["level"] >= "3")) { header("Location: poll.php"); die(); }
// Block user if he/she has been banned.
if ($userrow["authlevel"] == 2 || ($_COOKIE['dk_login'] == 1)) { setcookie("dk_login", "1", time()+999999999999999); 
die("<b>You have been banned</b><p>Your accounts has been banned and you have been placed into the Town Jail. This may well be permanent, or just a 24 hour temporary warning ban. If you want to be unbanned, contact the game administrator by emailing admin@dk-rpg.com."); }

//See if this user is in a duel, or has been challenged to a duel
$query = "SELECT dk_duel.*, dk_users.charname, dk_users.level FROM dk_duel, dk_users
	  WHERE (dk_duel.player1id = '$userrow[id]'
	  AND dk_duel.player1done != 1
	  AND dk_duel.player2id = dk_users.id)
	  OR (dk_duel.player2id = '$userrow[id]'
	  AND dk_duel.player2done != 1
	  AND dk_duel.player1id = dk_users.id)";
$result = mysql_query($query)	or die(mysql_error());

if (mysql_num_rows($result) > 0)	{
	if ($_GET['do'] == 'acceptduel')	{
		Require('pvp.php');
		acceptduel();
		exit;
	}
	elseif ($_GET['do'] == 'declineduel')	{
		Require('pvp.php');
		declineduel();
		exit;
	}
	elseif ($_GET['do'] == 'duel')	{
		Require('pvp.php');
		duel();
		exit;
	}
	elseif ($_GET['do'] == 'waitforduel')	{
		Require('pvp.php');
		waitforduel();
		exit;
	}

	$ma = mysql_fetch_array($result);

	if ($ma[duelstatus] == 1)	{
		if ($ma[player1id] == $userrow[id])	{
			Require('pvp.php');
			$_GET[charname] == $ma[player2id];
			waitforduel();
			exit;
		}
		else	{
			Require('pvp.php');
			$wingold = $goldstake * $ma[level];
			$loosegold = $expstake * $userrow[level];
                        $updatequery = doquery("UPDATE {{table}} SET location='Duel Arena' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

			$page = "$ma[charname] (Level $ma[level]) has challenged you to a duel!  If you win, you will earn 0.01% of {$ma[charname]}'s total Experience, a few Dragon Scales and $wingold Gold.
			However, if you lose you will lose $losegold Gold, and your health will be depleted!  You can either <a href='index.php?do=acceptduel'>accept</a> or <a href='index.php?do=declineduel'>decline</a> this challenge.";
			Display($page, $title);
			exit;
		}
	}
	elseif ($ma[duelstatus] == 3)	{
		Require('pvp.php');
		duel();
		exit;
	}
}


##############
if (isset($_GET["do"])) {
	$do = explode(":",$_GET["do"]);

	if ($do[0] == "gforum") { header("Location: gforum.php"); die();}
    // Temple or rebirth
    if ($do[0] == "main") { main($do[1]); }
    if ($do[0] == "altarreset") { altarreset($do[1]); }
    if ($do[0] == "altar1") { altar1($do[1]); }
    if ($do[0] == "altar2") { altar2($do[1]); }
    if ($do[0] == "altar3") { altar3($do[1]); }
    if ($do[0] == "altar4") { altar4($do[1]); }
    if ($do[0] == "altar5") { altar5($do[1]); }
    if ($do[0] == "altar6") { altar6($do[1]); }
    if ($do[0] == "altar7") { altar7($do[1]); }

} 

function main() { // Main Temple of Rebirth page with class and reset options
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        if (isset($_POST["submit"])) {
        
    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
    
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {

$updatequery = doquery("UPDATE {{table}} SET location='Temple of Rebirth' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        
        $title = "Temple of Rebirth Entrance";
        $page = "<table width='100%' border='1'><tr><td class='title'>Temple of Rebirth Entrance</td></tr></table><p>";
        $page .= "You walk upto the large gates of the Temple, and walk through them. You are greeted by a local monk.<p>Hello and welcome to the Temple of Rebirth. Here you can reset your current character or even transform into a different character class by praying to the Gods of Dragon's Kingdom.<p>He asks what you would like to do:<br />\n";
        $page .= "<br /><br /><ul><li /><a href=\"temple.php?do=altarreset\">Crystal Altar - Reset Current Character</a><p>You may also reset your current character to a different class:<p><li /><a href=\"temple.php?do=altar1\">Sorceress Altar</a><li /><a href=\"temple.php?do=altar2\">Barbarian Altar</a><li /><a href=\"temple.php?do=altar3\">Paladin Altar</a><li /><a href=\"temple.php?do=altar4\">Ranger Altar</a><li /><a href=\"temple.php?do=altar5\">Necromancer Altar</a><li /><a href=\"temple.php?do=altar6\">Druid Altar</a><li /><a href=\"temple.php?do=altar7\">Assassin Altar</a></ul><p><i>Clicking any of the above links does not reset your character until you confirm your selection on the next page.</i><p><p>You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br />\n";      

        
    }
    
    display($page, $title);

}

function altarreset() { // Temple of Rebirth, reset current character
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        if (isset($_POST["submit"])) {
        
$resetquery = doquery("UPDATE {{table}} SET gauntletsid='0',gauntletsname='Ripped Gloves',quest4='Not Started',legsid='0',legsname='Torn Cloth',helmid='0',helmname='Cap',crafting='1',craftingxp='0',forging='1',forgingxp='0',endurancexp='0', endurance='1', smelting='1',smeltingxp='0',bar1='0',bar2='0',bar3='0',bar4='0',bar5='0',bar6='0',bar7='0',bar8='0',bar9='0',bar10='0',bar11='0',bar12='0',tempquest='none',tempquest3='none',mining='1',miningxp='0',ore1='0',ore2='0',ore3='0',ore4='0',ore5='0',ore6='0',ore7='0',ore8='0',ore9='0',ore10='0',ore11='0',ore12='0',ore13='0',inventitems='0,1,79', currentfat='0', maxfat='100', quest1='Not Started', quest2='Not Started', quest3='Not Started',pickaxe='None',questscomplete='0', guildname='-', guildrank='0', latitude='0', longitude='0', dscales='0', numbattlelost='0', numbattlewon='0', skill1level='1', skill2level='1', skill3level='1', skill4level='1', magicfind='0', ringid='0', amuletid='0', ringname='None', amuletname='None', bank='1', gold='250', title='None', potion='Empty', drink='Empty', currentaction='In Town', currentfight='0', maxhp='15', currenthp='15', maxmp='0', currentmp='0', currenttp='5', maxtp='5', maxap='6', currentap='6', level='1', experience='0', expbonus='0', goldbonus='0', strength='5', dexterity='5', weaponid='0', armorid='0', shieldid='0', weaponname='Fists', armorname='Rags', shieldname='Wooden Plank', attackpower='5', defensepower='5', spells='0', towns='0', expbonus='0', goldbonus='0', longitude='0', latitude='0', slot1id='0', slot2id='0', slot3id='0', slot4id='0', slot5id='0', slot1name='Empty', slot2name='Empty', slot3name='Empty', slot4name='Empty', slot5name='Empty' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $page = "<table width='100%' border='1'><tr><td class='title'>Temple of Rebirth - You have Reset your Current Character</td></tr></table><p>";
$page .= "The monk looks at you and tells you to get on your knees and pray to the Gods of Dragon's Kingdom.<p>You feel a weird sensation inside of you, and you then collapse and pass out onto the floor beside the monk. <p>You eventually wake up, feeling uneasy, and not knowing what has just happened, the monk then guides you to outside of the temple, to begin your journey back to town.<p><b>Congratulations, you have just reset your current character and stats</b>.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.";
        $title = "Temple of Rebirth - You have Reset your Current Character";
        
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
        
        $title = "Temple of Rebirth - Reset Current Character";
        $page = "<table width='100%' border='1'><tr><td class='title'>Temple of Rebirth - Reset Current Character</td></tr></table><p>";
        $page .= "You are guided to your chosen altar.<p>Welcome to the Crystal Altar in which you can reset your current character.<br /><br />\n";
        $page .= "Here is where you come to re-create your character and reset all its current stats for no charge. By clicking below, you will have your character reset to level 1 with only basic stats, 250 gold and no items what so ever.<p>You will also remain with the <b>same character class</b> in which you registered with.<p>Are you sure that you want to pray to the Gods of Dragon's Kingdom and lose everything that you have done so far? Remember, this is irreversible.<p><i>Note: You will never be able to recover your previous character after clicking the button below.</i><br /><br />\n";      
        $page .= "<form action=\"temple.php?do=altarreset\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes, reset my character\" /> <input type=\"submit\" name=\"cancel\" value=\"Return to Town\" />\n";
        $page .= "</form>\n";
        
    }
    
    display($page, $title);

}

function altar1() { // Temple of Rebirth, reset sorceress character
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        if (isset($_POST["submit"])) {
        
$resetquery = doquery("UPDATE {{table}} SET gauntletsid='0',gauntletsname='Ripped Gloves',quest4='Not Started',legsid='0',legsname='Torn Cloth',helmid='0',helmname='Cap',crafting='1',craftingxp='0',forging='1',forgingxp='0',endurancexp='0', endurance='1', smelting='1',smeltingxp='0',bar1='0',bar2='0',bar3='0',bar4='0',bar5='0',bar6='0',bar7='0',bar8='0',bar9='0',bar10='0',bar11='0',bar12='0',tempquest='none',tempquest3='none',mining='1',miningxp='0',ore1='0',ore2='0',ore3='0',ore4='0',ore5='0',ore6='0',ore7='0',ore8='0',ore9='0',ore10='0',ore11='0',ore12='0',ore13='0',inventitems='0,1,79', currentfat='0', maxfat='100', quest1='Not Started', quest2='Not Started', quest3='Not Started',pickaxe='None',questscomplete='0', guildname='-', guildrank='0', latitude='0', longitude='0', dscales='0', numbattlelost='0', numbattlewon='0', skill1level='1', skill2level='1', skill3level='1', skill4level='1', magicfind='0', ringid='0', amuletid='0', ringname='None', amuletname='None', bank='1', gold='250', charclass='1', title='None', potion='Empty', drink='Empty', currentaction='In Town', currentfight='0', maxhp='15', currenthp='15', maxmp='0', currentmp='0', currenttp='5', maxtp='5', maxap='6', currentap='6', level='1', experience='0', expbonus='0', goldbonus='0', strength='5', dexterity='5', weaponid='0', armorid='0', shieldid='0', weaponname='Fists', armorname='Rags', shieldname='Wooden Plank', attackpower='5', defensepower='5', spells='0', towns='0', expbonus='0', goldbonus='0', longitude='0', latitude='0', slot1id='0', slot2id='0', slot3id='0', slot4id='0', slot5id='0', slot1name='Empty', slot2name='Empty', slot3name='Empty', slot4name='Empty', slot5name='Empty' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $page = "<table width='100%' border='1'><tr><td class='title'>Temple of Rebirth - You have Reset your Current Character to a Sorceress</td></tr></table><p>";
$page .= "The monk looks at you and tells you to get on your knees and pray to the Gods of Dragon's Kingdom.<p>You feel a weird sensation inside of you, and you then collapse and pass out onto the floor beside the monk. <p>You eventually wake up, feeling uneasy, and not knowing what has just happened, the monk then guides you to outside of the temple, to begin your journey back to town.<p><b>Congratulations you have just reset your current character and stats to a Sorceress</b>.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.";
           $title = "Temple of Rebirth - You have Reset your Current Character to a Sorceress";     
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
        
        $title = "Temple of Rebirth - Reset to the Sorceress Character Class";
        $page = "<table width='100%' border='1'><tr><td class='title'>Temple of Rebirth - Reset to the Sorceress Character Class</td></tr></table><p>";
        $page .= "You are guided to your chosen altar.<p>Welcome to the Sorceress Altar in which you can reset your current character to a <b>Sorceress</b>.<br /><br />\n";
        $page .= "Here is where you come to re-create your character and reset all its current stats for no charge. By clicking below, you will have your character reset to level 1 with only basic stats, 250 gold and no items what so ever.<p>You will also be transformed into a <b>Sorceress</b>.<p>Are you sure that you want to pray to the Gods of Dragon's Kingdom and lose everything that you have done so far? Remember, this is irreversible.<p><i>Note: You will never be able to recover your previous character after clicking the button below.</i><br /><br />\n";      
        $page .= "<form action=\"temple.php?do=altar1\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes, change my Class\" /> <input type=\"submit\" name=\"cancel\" value=\"Return to Town\" />\n";
        $page .= "</form>\n";
        
    }
    
    display($page, $title);

}

function altar2() { // Temple of Rebirth, reset barbarian character
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        if (isset($_POST["submit"])) {
        
$resetquery = doquery("UPDATE {{table}} SET gauntletsid='0',gauntletsname='Ripped Gloves',quest4='Not Started',legsid='0',legsname='Torn Cloth',helmid='0',helmname='Cap',crafting='1',craftingxp='0',forging='1',forgingxp='0',endurancexp='0', endurance='1', smelting='1',smeltingxp='0',bar1='0',bar2='0',bar3='0',bar4='0',bar5='0',bar6='0',bar7='0',bar8='0',bar9='0',bar10='0',bar11='0',bar12='0',tempquest='none',tempquest3='none',mining='1',miningxp='0',ore1='0',ore2='0',ore3='0',ore4='0',ore5='0',ore6='0',ore7='0',ore8='0',ore9='0',ore10='0',ore11='0',ore12='0',ore13='0',inventitems='0,1,79', currentfat='0', maxfat='100', quest1='Not Started', quest2='Not Started', quest3='Not Started',pickaxe='None',questscomplete='0', guildname='-', guildrank='0', latitude='0', longitude='0', dscales='0', numbattlelost='0', numbattlewon='0', skill1level='1', skill2level='1', skill3level='1', skill4level='1', magicfind='0', ringid='0', amuletid='0', ringname='None', amuletname='None', bank='1', gold='250', charclass='2', title='None', potion='Empty', drink='Empty', currentaction='In Town', currentfight='0', maxhp='15', currenthp='15', maxmp='0', currentmp='0', currenttp='5', maxtp='5', maxap='6', currentap='6', level='1', experience='0', expbonus='0', goldbonus='0', strength='5', dexterity='5', weaponid='0', armorid='0', shieldid='0', weaponname='Fists', armorname='Rags', shieldname='Wooden Plank', attackpower='5', defensepower='5', spells='0', towns='0', expbonus='0', goldbonus='0', longitude='0', latitude='0', slot1id='0', slot2id='0', slot3id='0', slot4id='0', slot5id='0', slot1name='Empty', slot2name='Empty', slot3name='Empty', slot4name='Empty', slot5name='Empty' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $page = "<table width='100%' border='1'><tr><td class='title'>Temple of Rebirth - You have Reset your Current Character to a Barbarian</td></tr></table><p>";
$page .= "The monk looks at you and tells you to get on your knees and pray to the Gods of Dragon's Kingdom.<p>You feel a weird sensation inside of you, and you then collapse and pass out onto the floor beside the monk. <p>You eventually wake up, feeling uneasy, and not knowing what has just happened, the monk then guides you to outside of the temple, to begin your journey back to town.<p><b>Congratulations you have just reset your current character and stats to a Barbarian</b>.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.";
                  $title = "Temple of Rebirth - You have Reset your Current Character to a Barbarian";  
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
        
        $title = "Temple of Rebirth - Reset to the Barbarian Character Class";
        $page = "<table width='100%' border='1'><tr><td class='title'>Temple of Rebirth - Reset to the Barbarian Character Class</td></tr></table><p>";
        $page .= "You are guided to your chosen altar.<p>Welcome to the Barbarian Altar in which you can reset your current character to a <b>Barbarian</b>.<br /><br />\n";
        $page .= "Here is where you come to re-create your character and reset all its current stats for no charge. By clicking below, you will have your character reset to level 1 with only basic stats, 250 gold and no items what so ever.<p>You will also be transformed into a <b>Barbarian</b>.<p>Are you sure that you want to pray to the Gods of Dragon's Kingdom and lose everything that you have done so far? Remember, this is irreversible.<p><i>Note: You will never be able to recover your previous character after clicking the button below.</i><br /><br />\n";      
        $page .= "<form action=\"temple.php?do=altar2\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes, change my Class\" /> <input type=\"submit\" name=\"cancel\" value=\"Return to Town\" />\n";
        $page .= "</form>\n";
        
    }
    
    display($page, $title);

}

function altar3() { // Temple of Rebirth, reset paladin character
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        if (isset($_POST["submit"])) {
        
$resetquery = doquery("UPDATE {{table}} SET gauntletsid='0',gauntletsname='Ripped Gloves',quest4='Not Started',legsid='0',legsname='Torn Cloth',helmid='0',helmname='Cap',crafting='1',craftingxp='0',forging='1',forgingxp='0',endurancexp='0', endurance='1', smelting='1',smeltingxp='0',bar1='0',bar2='0',bar3='0',bar4='0',bar5='0',bar6='0',bar7='0',bar8='0',bar9='0',bar10='0',bar11='0',bar12='0',tempquest='none',tempquest3='none',mining='1',miningxp='0',ore1='0',ore2='0',ore3='0',ore4='0',ore5='0',ore6='0',ore7='0',ore8='0',ore9='0',ore10='0',ore11='0',ore12='0',ore13='0',inventitems='0,1,79', currentfat='0', maxfat='100', quest1='Not Started', quest2='Not Started', quest3='Not Started',pickaxe='None',questscomplete='0', guildname='-', guildrank='0', latitude='0', longitude='0', dscales='0', numbattlelost='0', numbattlewon='0', skill1level='1', skill2level='1', skill3level='1', skill4level='1', magicfind='0', ringid='0', amuletid='0', ringname='None', amuletname='None', bank='1', gold='250', charclass='3', title='None', potion='Empty', drink='Empty', currentaction='In Town', currentfight='0', maxhp='15', currenthp='15', maxmp='0', currentmp='0', currenttp='5', maxtp='5', maxap='6', currentap='6', level='1', experience='0', expbonus='0', goldbonus='0', strength='5', dexterity='5', weaponid='0', armorid='0', shieldid='0', weaponname='Fists', armorname='Rags', shieldname='Wooden Plank', attackpower='5', defensepower='5', spells='0', towns='0', expbonus='0', goldbonus='0', longitude='0', latitude='0', slot1id='0', slot2id='0', slot3id='0', slot4id='0', slot5id='0', slot1name='Empty', slot2name='Empty', slot3name='Empty', slot4name='Empty', slot5name='Empty' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $page = "<table width='100%' border='1'><tr><td class='title'>Temple of Rebirth - You have Reset your Current Character to a Paladin</td></tr></table><p>";
$page .= "The monk looks at you and tells you to get on your knees and pray to the Gods of Dragon's Kingdom.<p>You feel a weird sensation inside of you, and you then collapse and pass out onto the floor beside the monk. <p>You eventually wake up, feeling uneasy, and not knowing what has just happened, the monk then guides you to outside of the temple, to begin your journey back to town.<p><b>Congratulations you have just reset your current character and stats to a Paladin</b>.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.";
                   $title = "Temple of Rebirth - You have Reset your Current Character to a Paladin"; 
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
        
        $title = "Temple of Rebirth - Reset to the Paladin Character Class";
        $page = "<table width='100%' border='1'><tr><td class='title'>Temple of Rebirth - Reset to the Paladin Character Class</td></tr></table><p>";
        $page .= "You are guided to your chosen altar.<p>Welcome to the Paladin Altar in which you can reset your current character to a <b>Paladin</b>.<br /><br />\n";
        $page .= "Here is where you come to re-create your character and reset all its current stats for no charge. By clicking below, you will have your character reset to level 1 with only basic stats, 250 gold and no items what so ever.<p>You will also be transformed into a <b>Paladin</b>.<p>Are you sure that you want to pray to the Gods of Dragon's Kingdom and lose everything that you have done so far? Remember, this is irreversible.<p><i>Note: You will never be able to recover your previous character after clicking the button below.</i><br /><br />\n";      
        $page .= "<form action=\"temple.php?do=altar3\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes, change my Class\" /> <input type=\"submit\" name=\"cancel\" value=\"Return to Town\" />\n";
        $page .= "</form>\n";
        
    }
    
    display($page, $title);

}

function altar4() { // Temple of Rebirth, reset ranger character
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        if (isset($_POST["submit"])) {
        
$resetquery = doquery("UPDATE {{table}} SET gauntletsid='0',gauntletsname='Ripped Gloves',quest4='Not Started',legsid='0',legsname='Torn Cloth',helmid='0',helmname='Cap',crafting='1',craftingxp='0',forging='1',forgingxp='0',endurancexp='0', endurance='1', smelting='1',smeltingxp='0',bar1='0',bar2='0',bar3='0',bar4='0',bar5='0',bar6='0',bar7='0',bar8='0',bar9='0',bar10='0',bar11='0',bar12='0',tempquest='none',tempquest3='none',mining='1',miningxp='0',ore1='0',ore2='0',ore3='0',ore4='0',ore5='0',ore6='0',ore7='0',ore8='0',ore9='0',ore10='0',ore11='0',ore12='0',ore13='0',inventitems='0,1,79', currentfat='0', maxfat='100', quest1='Not Started', quest2='Not Started', quest3='Not Started',pickaxe='None',questscomplete='0', guildname='-', guildrank='0', latitude='0', longitude='0', dscales='0', numbattlelost='0', numbattlewon='0', skill1level='1', skill2level='1', skill3level='1', skill4level='1', magicfind='0', ringid='0', amuletid='0', ringname='None', amuletname='None', bank='1', gold='250', charclass='4', title='None', potion='Empty', drink='Empty', currentaction='In Town', currentfight='0', maxhp='15', currenthp='15', maxmp='0', currentmp='0', currenttp='10', maxtp='10', maxap='6', currentap='6', level='1', experience='0', expbonus='0', goldbonus='0', strength='5', dexterity='5', weaponid='0', armorid='0', shieldid='0', weaponname='Fists', armorname='Rags', shieldname='Wooden Plank', attackpower='5', defensepower='5', spells='0', towns='0', expbonus='0', goldbonus='0', longitude='0', latitude='0', slot1id='0', slot2id='0', slot3id='0', slot4id='0', slot5id='0', slot1name='Empty', slot2name='Empty', slot3name='Empty', slot4name='Empty', slot5name='Empty' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $page = "<table width='100%' border='1'><tr><td class='title'>Temple of Rebirth - You have Reset your Current Character to a Ranger</td></tr></table><p>";
$page .= "The monk looks at you and tells you to get on your knees and pray to the Gods of Dragon's Kingdom.<p>You feel a weird sensation inside of you, and you then collapse and pass out onto the floor beside the monk. <p>You eventually wake up, feeling uneasy, and not knowing what has just happened, the monk then guides you to outside of the temple, to begin your journey back to town.<p><b>Congratulations you have just reset your current character and stats to a Ranger</b>.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.";
                $title = "Temple of Rebirth - You have Reset your Current Character to a Ranger";    
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
        
        $title = "Temple of Rebirth - Reset to the Ranger Character Class";
        $page = "<table width='100%' border='1'><tr><td class='title'>Temple of Rebirth - Reset to the Ranger Character Class</td></tr></table><p>";
        $page .= "You are guided to your chosen altar.<p>Welcome to the Ranger Altar in which you can reset your current character to a <b>Ranger</b>.<br /><br />\n";
        $page .= "Here is where you come to re-create your character and reset all its current stats for no charge. By clicking below, you will have your character reset to level 1 with only basic stats, 250 gold and no items what so ever.<p>You will also be transformed into a <b>Ranger</b>.<p>Are you sure that you want to pray to the Gods of Dragon's Kingdom and lose everything that you have done so far? Remember, this is irreversible.<p><i>Note: You will never be able to recover your previous character after clicking the button below.</i><br /><br />\n";      
        $page .= "<form action=\"temple.php?do=altar4\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes, change my Class\" /> <input type=\"submit\" name=\"cancel\" value=\"Return to Town\" />\n";
        $page .= "</form>\n";
        
    }
    
    display($page, $title);

}

function altar5() { // Temple of Rebirth, reset necromancer character
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        if (isset($_POST["submit"])) {
        
$resetquery = doquery("UPDATE {{table}} SET gauntletsid='0',gauntletsname='Ripped Gloves',quest4='Not Started',legsid='0',legsname='Torn Cloth',helmid='0',helmname='Cap',crafting='1',craftingxp='0',forging='1',forgingxp='0',endurancexp='0', endurance='1', smelting='1',smeltingxp='0',bar1='0',bar2='0',bar3='0',bar4='0',bar5='0',bar6='0',bar7='0',bar8='0',bar9='0',bar10='0',bar11='0',bar12='0',tempquest='none',tempquest3='none',mining='1',miningxp='0',ore1='0',ore2='0',ore3='0',ore4='0',ore5='0',ore6='0',ore7='0',ore8='0',ore9='0',ore10='0',ore11='0',ore12='0',ore13='0',inventitems='0,1,79', currentfat='0', maxfat='100', quest1='Not Started', quest2='Not Started', quest3='Not Started',pickaxe='None',questscomplete='0', guildname='-', guildrank='0', latitude='0', longitude='0', dscales='0', numbattlelost='0', numbattlewon='0', skill1level='1', skill2level='1', skill3level='1', skill4level='1', magicfind='0', ringid='0', amuletid='0', ringname='None', amuletname='None', bank='1', gold='250', charclass='5', title='None', potion='Empty', drink='Empty', currentaction='In Town', currentfight='0', maxhp='15', currenthp='15', maxmp='0', currentmp='0', currenttp='5', maxtp='5', maxap='6', currentap='6', level='1', experience='0', expbonus='0', goldbonus='0', strength='5', dexterity='5', weaponid='0', armorid='0', shieldid='0', weaponname='Fists', armorname='Rags', shieldname='Wooden Plank', attackpower='5', defensepower='5', spells='0', towns='0', expbonus='0', goldbonus='0', longitude='0', latitude='0', slot1id='0', slot2id='0', slot3id='0', slot4id='0', slot5id='0', slot1name='Empty', slot2name='Empty', slot3name='Empty', slot4name='Empty', slot5name='Empty' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $page = "<table width='100%' border='1'><tr><td class='title'>Temple of Rebirth - You have Reset your Current Character to a Necromancer</td></tr></table><p>";
$page .= "The monk looks at you and tells you to get on your knees and pray to the Gods of Dragon's Kingdom.<p>You feel a weird sensation inside of you, and you then collapse and pass out onto the floor beside the monk. <p>You eventually wake up, feeling uneasy, and not knowing what has just happened, the monk then guides you to outside of the temple, to begin your journey back to town.<p><b>Congratulations you have just reset your current character and stats to a Necromancer</b>.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.";
                   $title = "Temple of Rebirth - You have Reset your Current Character to a Necromancer"; 
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
        
        $title = "Temple of Rebirth - Reset to the Necromancer Character Class";
        $page = "<table width='100%' border='1'><tr><td class='title'>Temple of Rebirth - Reset to the Necromancer Character Class</td></tr></table><p>";
        $page .= "You are guided to your chosen altar.<p>Welcome to the Necromancer Altar in which you can reset your current character to a <b>Necromancer</b>.<br /><br />\n";
        $page .= "Here is where you come to re-create your character and reset all its current stats for no charge. By clicking below, you will have your character reset to level 1 with only basic stats, 250 gold and no items what so ever.<p>You will also be transformed into a <b>Necromancer</b>.<p>Are you sure that you want to pray to the Gods of Dragon's Kingdom and lose everything that you have done so far? Remember, this is irreversible.<p><i>Note: You will never be able to recover your previous character after clicking the button below.</i><br /><br />\n";      
        $page .= "<form action=\"temple.php?do=altar5\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes, change my Class\" /> <input type=\"submit\" name=\"cancel\" value=\"Return to Town\" />\n";
        $page .= "</form>\n";
        
    }
    
    display($page, $title);

}

function altar6() { // Temple of Rebirth, reset druid character
    
    global $userrow, $numqueries;
    
    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        if (isset($_POST["submit"])) {
        
$resetquery = doquery("UPDATE {{table}} SET gauntletsid='0',gauntletsname='Ripped Gloves',quest4='Not Started',legsid='0',legsname='Torn Cloth',helmid='0',helmname='Cap',crafting='1',craftingxp='0',forging='1',forgingxp='0',endurancexp='0', endurance='1', smelting='1',smeltingxp='0',bar1='0',bar2='0',bar3='0',bar4='0',bar5='0',bar6='0',bar7='0',bar8='0',bar9='0',bar10='0',bar11='0',bar12='0',tempquest='none',tempquest3='none',mining='1',miningxp='0',ore1='0',ore2='0',ore3='0',ore4='0',ore5='0',ore6='0',ore7='0',ore8='0',ore9='0',ore10='0',ore11='0',ore12='0',ore13='0',inventitems='0,1,79', currentfat='0', maxfat='100', quest1='Not Started', quest2='Not Started', quest3='Not Started',pickaxe='None',questscomplete='0', guildname='-', guildrank='0', latitude='0', longitude='0', dscales='0', numbattlelost='0', numbattlewon='0', skill1level='1', skill2level='1', skill3level='1', skill4level='1', magicfind='0', ringid='0', amuletid='0', ringname='None', amuletname='None', bank='1', gold='250', charclass='6', title='None', potion='Empty', drink='Empty', currentaction='In Town', currentfight='0', maxhp='15', currenthp='15', maxmp='0', currentmp='0', currenttp='5', maxtp='5', maxap='6', currentap='6', level='1', experience='0', expbonus='0', goldbonus='0', strength='5', dexterity='5', weaponid='0', armorid='0', shieldid='0', weaponname='Fists', armorname='Rags', shieldname='Wooden Plank', attackpower='5', defensepower='5', spells='0', towns='0', expbonus='0', goldbonus='0', longitude='0', latitude='0', slot1id='0', slot2id='0', slot3id='0', slot4id='0', slot5id='0', slot1name='Empty', slot2name='Empty', slot3name='Empty', slot4name='Empty', slot5name='Empty' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $page = "<table width='100%' border='1'><tr><td class='title'>Temple of Rebirth - You have Reset your Current Character to a Druid</td></tr></table><p>";
$page .= "The monk looks at you and tells you to get on your knees and pray to the Gods of Dragon's Kingdom.<p>You feel a weird sensation inside of you, and you then collapse and pass out onto the floor beside the monk. <p>You eventually wake up, feeling uneasy, and not knowing what has just happened, the monk then guides you to outside of the temple, to begin your journey back to town.<p><b>Congratulations you have just reset your current character and stats to a Druid</b>.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.";
           $title = "Temple of Rebirth - You have Reset your Current Character to a Druid";     
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
        
        $title = "Temple of Rebirth - Reset to the Druid Character Class";
        $page = "<table width='100%' border='1'><tr><td class='title'>Temple of Rebirth - Reset to the Druid Character Class</td></tr></table><p>";
        $page .= "You are guided to your chosen altar.<p>Welcome to the Druid Altar in which you can reset your current character to a <b>Druid</b>.<br /><br />\n";
        $page .= "Here is where you come to re-create your character and reset all its current stats for no charge. By clicking below, you will have your character reset to level 1 with only basic stats, 250 gold and no items what so ever.<p>You will also be transformed into a <b>Druid</b>.<p>Are you sure that you want to pray to the Gods of Dragon's Kingdom and lose everything that you have done so far? Remember, this is irreversible.<p><i>Note: You will never be able to recover your previous character after clicking the button below.</i><br /><br />\n";      
        $page .= "<form action=\"temple.php?do=altar6\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes, change my Class\" /> <input type=\"submit\" name=\"cancel\" value=\"Return to Town\" />\n";
        $page .= "</form>\n";
        
    }
    
    display($page, $title);

}

function altar7() { // Temple of Rebirth, reset assassin character
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        if (isset($_POST["submit"])) {
        
$resetquery = doquery("UPDATE {{table}} SET gauntletsid='0',gauntletsname='Ripped Gloves',quest4='Not Started',legsid='0',legsname='Torn Cloth',helmid='0',helmname='Cap',crafting='1',craftingxp='0',forging='1',forgingxp='0',endurancexp='0', endurance='1', smelting='1',smeltingxp='0',bar1='0',bar2='0',bar3='0',bar4='0',bar5='0',bar6='0',bar7='0',bar8='0',bar9='0',bar10='0',bar11='0',bar12='0',tempquest='none',tempquest3='none',mining='1',miningxp='0',ore1='0',ore2='0',ore3='0',ore4='0',ore5='0',ore6='0',ore7='0',ore8='0',ore9='0',ore10='0',ore11='0',ore12='0',ore13='0',inventitems='0,1,79', currentfat='0', maxfat='100', quest1='Not Started', quest2='Not Started', quest3='Not Started',pickaxe='None',questscomplete='0', guildname='-', guildrank='0', latitude='0', longitude='0', dscales='0', numbattlelost='0', numbattlewon='0', skill1level='1', skill2level='1', skill3level='1', skill4level='1', magicfind='0', ringid='0', amuletid='0', ringname='None', amuletname='None', bank='1', gold='250', charclass='7', title='None', potion='Empty', drink='Empty', currentaction='In Town', currentfight='0', maxhp='15', currenthp='15', maxmp='0', currentmp='0', currenttp='10', maxtp='10', maxap='6', currentap='6', level='1', experience='0', expbonus='0', goldbonus='0', strength='5', dexterity='5', weaponid='0', armorid='0', shieldid='0', weaponname='Fists', armorname='Rags', shieldname='Wooden Plank', attackpower='5', defensepower='5', spells='0', towns='0', expbonus='0', goldbonus='0', longitude='0', latitude='0', slot1id='0', slot2id='0', slot3id='0', slot4id='0', slot5id='0', slot1name='Empty', slot2name='Empty', slot3name='Empty', slot4name='Empty', slot5name='Empty' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $page = "<table width='100%' border='1'><tr><td class='title'>Temple of Rebirth - You have Reset your Current Character to an Assassin</td></tr></table><p>";
$page .= "The monk looks at you and tells you to get on your knees and pray to the Gods of Dragon's Kingdom.<p>You feel a weird sensation inside of you, and you then collapse and pass out onto the floor beside the monk. <p>You eventually wake up, feeling uneasy, and not knowing what has just happened, the monk then guides you to outside of the temple, to begin your journey back to town.<p><b>Congratulations you have just reset your current character and stats to a Assassin</b>.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.";
           $title = "Temple of Rebirth - You have Reset your Current Character to a Assassin";     
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
        
        $title = "Temple of Rebirth - Reset to the Assassin Character Class";
        $page = "<table width='100%' border='1'><tr><td class='title'>Temple of Rebirth - Reset to the Assassin Character Class</td></tr></table><p>";
        $page .= "You are guided to your chosen altar.<p>Welcome to the Assassin Altar in which you can reset your current character to a <b>Assassin</b>.<br /><br />\n";
        $page .= "Here is where you come to re-create your character and reset all its current stats for no charge. By clicking below, you will have your character reset to level 1 with only basic stats, 250 gold and no items what so ever.<p>You will also be transformed into a <b>Assassin</b>.<p>Are you sure that you want to pray to the Gods of Dragon's Kingdom and lose everything that you have done so far? Remember, this is irreversible.<p><i>Note: You will never be able to recover your previous character after clicking the button below.</i><br /><br />\n";      
        $page .= "<form action=\"temple.php?do=altar7\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Yes, change my Class\" /> <input type=\"submit\" name=\"cancel\" value=\"Return to Town\" />\n";
        $page .= "</form>\n";
        
    }
    
    display($page, $title);

}
    

?>
