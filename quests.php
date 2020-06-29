<?php // quests.php :: Handles all quests.

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
// Block user if he/she has been banned.
//Must vote
if (($userrow["poll"] != "Voted") && ($userrow["level"] >= "3")) { header("Location: poll.php"); die(); }
if ($userrow["authlevel"] == 2 || ($_COOKIE['dk_login'] == 1)) { setcookie("dk_login", "1", time()+999999999999999); 
die("<b>You have been banned</b><p>Your accounts has been banned and you have been placed into the Town Jail. This may well be permanent, or just a 24 hour temporary warning ban. If you want to be unbanned, contact the game administrator by emailing admin@dk-rpg.com."); }

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
##############


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

    if ($do[0] == "tower") { tower($do[1]); }
	elseif ($do[0] == "lostfortune") { lostfortune($do[1]); }
	elseif ($do[0] == "assistant") { assistant($do[1]); }
	elseif ($do[0] == "scientist") { scientist($do[1]); }
	elseif ($do[0] == "fightslime") { fightslime(); }
	elseif ($do[0] == "parasite") { parasite(); }
	elseif ($do[0] == "fightpotionslime") { fightpotionslime(); }	
}
function tower() { // Quest Tower

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

$updatequery = doquery("UPDATE {{table}} SET location='Quest Tower' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

        $title = "Quest Tower";
        $page = "<table width='100%' border='1'><tr><td class='title'>Quest Tower</td></tr></table><p>";
        $page .= "You walk up the swirling stairs within the Tower. There is currently 4 Quests available:<br /><br /><hr /><ul><li /><a href=\"quests.php?do=lostfortune\">Lost Fortune</a><br>Difficulty: Very Easy<br>Requirements: Must be able to kill several low level Monsters.<br>Status: ".$userrow["quest1"]."<hr /><li /><a href=\"quests.php?do=assistant\">Potion Assistant</a><br>Difficulty: Medium<br>Requirements: Must be level 15 or above.<br>Status: ".$userrow["quest2"]."<hr /><li /><a href=\"quests.php?do=scientist\">Mad Scientist</a><br>Difficulty: Medium<br>Requirements: Must of completed the Lost Fortune Quest and be able to mine Iron to complete this Quest.<br>Status: ".$userrow["quest3"]."<hr /><li /><a href=\"quests.php?do=parasite\">The Parasite</a><br>Difficulty: Medium<br>Requirements: Must of completed the Potion Assistant Quest and be Level 15 in Endurance.<br>Status: ".$userrow["quest4"]."</ul><hr /><p>\n";
        $page .= "<p><font color=red>Note: If you drop any Quest Items which are needed for a Quest, it will result in the Quest not being completed, and the Quest will be a Failure. You cannot restart a Quest.</font><br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass on the right to start exploring.<br />\n";


    }

    display($page, $title);

}

function lostfortune() { // Lost fortune quest
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);


if($userrow["quest1"] == "Not Started") { //Start quest

        $title = "Lost Fortune Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>Lost Fortune Quest</td></tr></table><p>";
        $page .= "You notice a young woman standing in the corner of the Quest Tower. She is dressed in fine silks and lace, at odds with the more common dress of the people who usually frequent the Tower. Obviously a person of some means you suspect the reward for helping her would be greater than the usual reward for finding a common farmer's lost pitchfork.<br /><br />\n";
        $page .= "As you approach, she straightens her back and lifts her chin to look down her nose at you. You notice that her eyes are red from crying.<br /><br />\n";
        $page .= "\"Excuse me.\" As you begin, she turns her head away. \"I was wondering if I could be of some assistance?\"<br /><br />\n";
        $page .= "She doesn't reply for several long moments. Just as you are about to find someone else who needs your help, the woman looks at you, dropping her chin only slightly.<br /><br />\n";
        $page .= "\"Oh, you were addressing me?\" she says. \"I'm sorry, I'm just not used to associating with such\" She casts a disapproving eye over your battle-scarred gear. \"Rabble.\"<br /><br />\n";
        $page .= "\"Look, <i>lady</i>,\" you reply, anger boiling up in your throat. \"Maybe I could have taken some extra time this morning to polish my harness, but I think my ability to reduce the average monster to dust is more important than my ability to finish first in a 'most beautiful adventurer' contest.\"<br /><br />\n";
        $page .= "You're more used to people appreciating your offers of help. Turning on your heel, you make to storm off. The woman places a restraining hand on your arm.<br /><br />\n";
        $page .= "\"I'm sorry I acted rudely, it's just that when I came to the Quest Tower for help, I was expecting a hero of the calibre of ".$userrow["charname"].". I was disappointed, but obviously that's not your fault.\"<br /><br />\n";
        $page .= "You smile inwardly and extend a hand in reconciliation. The young woman looks briefly worried at your dirty offering before accepting it.<br /><br />\n";
        $page .= "\"I think you'll find most of the adventurers out there look the same as I do, even the famed ".$userrow["charname"].". It mostly comes with the occupation.\"<br /><br />\n";
        $page .= "She has the decency to look abashed. \"Well, I suppose I should introduce myself, as it seems you will be temporarily in my service.\" Drawing herself up even more, she begins as if giving a speech. \"I am the Princess Larissa.\"<br /><br />\n"; 
        $page .= "Your mouth gapes open. Royalty! You mentally add another thousand gold pieces to the already large reward you had been expecting. The princess continues.<br /><br />\n"; 
        $page .= "\"I was taking my daily stroll through the lands surrounding the castle, when a monster rose up out of the scrub and made its way towards me. At first I thought it would realise who I was and move on, but as I stood there it just kept coming closer and closer. Finally I had no choice but to turn and flee. As I ran I tripped over the corpse of some disgusting animal. I was filthy by the time I reached the town and what is more, I had lost my Precious Ring! All because that monster had the audacity to attack me!\"<br /><br />\n";
        $page .= "You struggle to keep your tone serious.<br /><br />\n";    
        $page .= "\"I shall go in search of your Precious Ring, Princess Larissa, and return here to deliver it to you.\"<br /><br />\n";
        $page .= "\"Oh thank you. It is a family heirloom, and very valuable. I couldn't bear to face my father having lost it.\"<br /><br />\n"; 
        $page .= "To add a sense of theatre, you draw your weapon and brandish it grandiosely at the ceiling. As you stride towards the door, the princess calls out.<br /><br />\n"; 
        $page .= "\"You haven't even given me your name!\"<br /><br />\n";
        $page .= "You reply with a laugh. \"My name is ".$userrow["charname"].", Your Highness.\" Stepping out of the tower and into the daylight, you fail to see the shocked expression on Princess Larissa's face turn to awe.<br /><br />\n";    
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to find that lost Ring!<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='Lost Fortune Quest',quest1='Started' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 

}
elseif($userrow["quest1"] == "Started") { //Remind player what item she needs if they havent got it already
        $title = "Lost Fortune Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>Lost Fortune Quest</td></tr></table><p>";
        $page .= "Princess Larissa is pacing back and forth in the Tower when you return, throwing dirty looks at any person who happens to get too close to her. She sees you enter and instantly her face brightens.<br /><br />\n";
        $page .= "\"You've found my Ring!\" she exclaims.<br /><br />\n";
        $page .= "Sadly shaking your head, you explain that despite much searching you have been unable to locate it. \"Can you give me any more information about where you lost it?\" you ask.<br /><br />\n"; 
        $page .= "The princess thinks for a while, then replies. \"I can't have been that far from town because I managed to run all the way back, especially in these heavy skirts. Other than that, I'm afraid I couldn't tell you.\"<br /><br />\n";
        $page .= "\"I shall just have to keep searching for it, and may luck be on my side.\"<br /><br />\n"; 
        $page .= "\"You will find it, won't you?\" she asks, worried.<br /><br />\n";
        $page .= "You try to smile reassuringly. \"I will not rest until it is found.\"<br /><br />\n";
        $page .= "You hope you do find it soon, otherwise you are going to be mighty tired.<br /><br />\n"; 
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to find that lost Ring!<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='Lost Fortune Quest',quest1='Started' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 

}
elseif($userrow["quest1"] == "Half Complete") { //Quest complete, collect reward and give her the ring
        $title = "Lost Fortune Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>Lost Fortune Quest</td></tr></table><p>";
        $page .= "Princess Larissa is sitting dejectedly against the far wall of the tower. She looks up as you approach her. Barely grimacing at your blood stained clothes, she speaks softly.<br /><br />\n";
        $page .= "\"I suppose you've returned to inform me that my Precious Ring is lost forever.\" She looks on the brink of crying.<br /><br />\n";
        $page .= "Reaching into your belt pouch, you draw forth her Ring. \"You doubted the famous ".$userrow["charname"]."?\" you proclaim. \"Failure and I hath never met!\"<br /><br />\n"; 
        $page .= "The princess jumps from her stool and rushes to embrace you. Just before she actually flings her arms around you, she looks again at your dirty attire and settles for a congratulatory pat on the arm. Her delicately gloved hand snatches the ring from your hand. She notices how bloody the ring is and seizes a corner of your tunic. Before you can prise your clothing from her, she has rubbed the jewelry clean. She holds it up to the light.<br /><br />\n";
        $page .= "\"This is definitely it!\" she exclaims joyously. \"Untarnished and undamaged!\"<br /><br />\n";
        $page .= "She slips it onto a finger and admires its sparkle for an interminably long period. You wait, but she says nothing further before walking towards the Tower's exit. She's going to leave without giving you a reward! Just as you are about to call out, she turns and asks. \"Well, are you coming?\"<br /><br />\n"; 
        $page .= "You follow her outside to where she is opening a trunk set at the rear of a waiting carriage. She pulls out a heavy bundle and hands it to you.<br /><br />\n";
        $page .= "\"The previous owner left this in here when my father bought this carriage for me. I don't want it, but maybe you'll find a use for it.\" She walks around the side of the carriage and a footman opens the door for her. She climbs inside and just before the carriage draws away, she says \"Oh, and have this too. Thank you for your help, mighty ".$userrow["charname"].".\" Another, smaller bundle flies out of the carriage window. You catch it awkwardly.<br /><br />\n"; 
        $page .= "Once the dust from the carriage clears, you start to open your packages.<br /><br />\n";
        $page .= "<font color=green>Congratulations you have completed the Lost Fortune Quest! You have gained a Normal Pickaxe, 1,000 experience points, 12,000 Gold and 15 Dragon Scales!</font><br /><br />\n"; 
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to continue exploring.<br /><br />\n";
$inventitems = "0,".$userrow["inventitems"].",0";
$newinventitems = str_replace(",88,", ",", $inventitems);
$updatequery = doquery("UPDATE {{table}} SET inventitems='$newinventitems', location='Lost Fortune Quest', questscomplete=questscomplete+1, pickaxe='Normal Pickaxe',dscales=dscales+15, experience=experience+1000, gold=gold+12000, quest1='Complete' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 

}
elseif($userrow["quest1"] == "Complete") { //Quest already completed, dont get reward more than once
        $title = "Lost Fortune Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>Lost Fortune Quest</td></tr></table><p>";
        $page .= "You remember seeing Princess Larissa in a recent parade. She was talking happily with her father, the king, and her Precious Ring was on her finger. She gave you a small wave before the parade moved on.<br /><br />\n";
        $page .= "<font color=red>You have already completed the Lost Fortune Quest.</font><br /><br />\n"; 
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to continue exploring.<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='Lost Fortune Quest',quest1='Complete' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 

    }
    
    display($page, $title);
    
}

function assistant() { // Potion Assistant
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

 {
if ($userrow["level"] < 15) { display("<p>Sorry, but you do not currently meet the requirements for this Quest. You must be level 15 or above to access this Quest.<p>Please try again later when you are of a greater level.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass to the right to start exploring.","You do not meet the level requirement"); die(); } 
    
if($userrow["quest2"] == "Not Started") { //Start quest

        $title = "Potion Assistant Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>Potion Assistant Quest</td></tr></table><p>";
        $page .= "<i>Oh no, Oh no! I will never find all these ingredients in time!</i><br /><br />\n";
        $page .= "".$userrow["charname"].": I couldn't help overhearing. What's the matter?<br /><br />\n";
        $page .= "Lucas: I have to come up with an Antidote in time to save my Son from dying. You see, he has been severely poisoned by a rare venomous slime. I have searched high and low for the ingredients that I require, but I just can't find them alone. Time is running out, can you please help me?<br /><br />\n";
        $page .= "".$userrow["charname"].": Sure I can, but I don't see how I can help. But of course I will try to help save your dying Son.<br /><br />\n";
        $page .= "Lucas: I need you to help me find 4 items which I require to finish the Antidote. I need the following items:<br>- An Empty Vial<br>- Special ingredients from a friend<br>- A Rare Potent Herb<br>- and Healing Water.<br /><br />Lucas: Do you think you can get me all those ingredients?<br /><br />\n";
        $page .= "".$userrow["charname"].": I can try my best for you. Can you give me any clue as to where I can find these items?<br /><br />\n"; 
        $page .= "Lucas: First, go find my friend who has the special ingredients that I need. He will be expecting you. At least with them, I can begin finishing my Antidote. Please hurry to my friend, and collect these items in the order in which I say otherwise the Potion will not work.<br /><br />\n"; 
        $page .= "".$userrow["charname"].": Ok, I shall return as soon as I locate your friend.<br /><br />\n";
        $page .= "<i>Lucas Nods, and begins mixing some ingredients up using a pestle and mortar.</i><br /><br />\n";    
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to find his friend!<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='Potion Assistant Quest',quest2='Started',tempquest='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 


    }
    
    elseif($userrow["tempquest"] == "slimedead" || $userrow["tempquest"] == "flower" || $userrow["tempquest"] == "gotflower") { //If Quest 4 has made player weak and need lucas for help.

        $title = "Potion Assistant Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>Potion Assistant Quest</td></tr></table><p>";
        $page .= "<i>Lucas isn't around right now..</i><br /><br />\n";        
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to continue exploring.<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='Potion Assistant Quest' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 
}

elseif($userrow["tempquest"] == "0") { //If not got ingredients from friend, display this.

        $title = "Potion Assistant Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>Potion Assistant Quest</td></tr></table><p>";
        $page .= "Lucas: Please hurry with my ingredients. My son doesnt't have much longer left. You can find my friend, most probably, at the Local Tavern.<br /><br />\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to find his friend!<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='Potion Assistant Quest',quest2='Started',tempquest='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 

    }

elseif($userrow["tempquest"] == "ingredients") { //Found ingredients. Give them to lucas

        $title = "Potion Assistant Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>Potion Assistant Quest</td></tr></table><p>";
        $page .= "".$userrow["charname"].": I have your Ingredients. Here you go.<br /><br />\n";
        $page .= "<i>You hand Lucas the bag of ingredients.</i><br /><br />\n";
        $page .= "Lucas: Thank you. Now I require an Empty Vial to hold my Potion. Please hurry, be as quick as possible.<br /><br />\n";
        $page .= "<i>Lucas continues making his Antidote Potion.</i><br /><br />\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to find an Empty Vial!<br /><br />\n";
$inventitems = "0,".$userrow["inventitems"].",0";
$newinventitems = str_replace(",89,", ",", $inventitems);
$updatequery = doquery("UPDATE {{table}} SET inventitems='$newinventitems', location='Potion Assistant Quest',quest2='Started',tempquest='1' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 

}
elseif($userrow["tempquest"] == "1") { //If not got empty vial from potions stall, display this.

        $title = "Potion Assistant Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>Potion Assistant Quest</td></tr></table><p>";
        $page .= "Lucas: Please hurry with my ingredients. My son doesnt't have much longer left. I next require an Empty Vial.<br /><br />\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to find an Empty Vial!<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='Potion Assistant Quest',quest2='Started',tempquest='1' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 

    }

elseif($userrow["tempquest"] == "vial") { //Found vial. Give them to lucas

        $title = "Potion Assistant Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>Potion Assistant Quest</td></tr></table><p>";
        $page .= "".$userrow["charname"].": I have found an Empty Vial. Here you go.<br /><br />\n";
        $page .= "<i>You hand Lucas the Empty Vial.</i><br /><br />\n";
        $page .= "Lucas: Thanks. Hmmm... now, what do I need next? Ah, yes! Please can you find me a Rare and Potent Herb?<br /><br />\n";
        $page .= "".$userrow["charname"].": A what? Where may I find one of these? I've never heard of such a thing.<br /><br />\n";
        $page .= "Lucas: I'm not entirely sure either. They are exceptionally rare to come across, and I just hope that you can find one somehow. I'm sorry that I am not much help but please keep trying. Without your help, I wouldn't be this far.<br /><br />\n";
        $page .= "".$userrow["charname"].": I'm sorry if I am letting you down, but I don't know whether I can find this. I will try my best, and continue looking.<br /><br />\n";
        $page .= "<i>Lucas continues making his Antidote Potion, looking really miserable.</i><br /><br />\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to find the Rare Potent Herb!<br /><br />\n";
$inventitems = "0,".$userrow["inventitems"].",0";
$newinventitems = str_replace(",90,", ",", $inventitems);
$updatequery = doquery("UPDATE {{table}} SET inventitems='$newinventitems', location='Potion Assistant Quest',quest2='Started',tempquest='2' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 

}
elseif($userrow["tempquest"] == "2") { //If not got rare herb from exploring, display this.

        $title = "Potion Assistant Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>Potion Assistant Quest</td></tr></table><p>";
        $page .= "Lucas: Please hurry with my ingredients. My son doesn't have much longer left. I still need the Rare Potent Herb.<br /><br />\n";
        $page .= "".$userrow["charname"].": I am still looking for one...<br /><br />\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to find the Rare Potent Herb!<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='Potion Assistant Quest',quest2='Started',tempquest='2' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 

    }

elseif($userrow["tempquest"] == "herb") { //Found rare herb. Give them to lucas

        $title = "Potion Assistant Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>Potion Assistant Quest</td></tr></table><p>";
        $page .= "".$userrow["charname"].": I have found the Rare Potent Herb. Here you go.<br /><br />\n";
        $page .= "<i>You hand lucas the Rare Herb.</i><br /><br />\n";
        $page .= "Lucas: Thanks. You took a long time to find this item. Where did you find it?<br /><br />\n";
        $page .= "".$userrow["charname"].": Yes, I'm sorry I took a long time. I found it while exploring outside Town. I guess that was just pure luck!<br /><br />\n";
        $page .= "Lucas: I guess so, well I'm glad you found it since I was worried about this Item. Now, next I need some Healing Water.<br /><br />\n";
        $page .= "".$userrow["charname"].": I think I have heard a rumour regarding Healing Water, located somewhere.. I just can't remember where.<br /><br />\n";
        $page .= "Lucas: I hope you can remember since I think my Son only has a couple of hours left to live.<br /><br />\n";
        $page .= "<i>Lucas continues making his Antidote Potion, looking even more depressed than before.</i><br /><br />\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to find the Healing Water!<br /><br />\n";
$inventitems = "0,".$userrow["inventitems"].",0";
$newinventitems = str_replace(",91,", ",", $inventitems);
$updatequery = doquery("UPDATE {{table}} SET inventitems='$newinventitems', location='Potion Assistant Quest',quest2='Half Complete',tempquest='3' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 

}

elseif($userrow["tempquest"] == "3") { //If not got healing water from cave, display this.

        $title = "Potion Assistant Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>Potion Assistant Quest</td></tr></table><p>";
        $page .= "Lucas: Please hurry with my ingredients. My son only has a matter of hours left to live. I still need some Healing Water. Just a small bucketfull will be more than enough.<br /><br />\n";
        $page .= "".$userrow["charname"].": I am still trying to remember.<br /><br />\n";
        $page .= "<i>Lucas continues making his Antidote Potion. You can see tears coming from his eyes.</i><br /><br />\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to find the Healing Water!<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='Potion Assistant Quest',quest2='Started',tempquest='3' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 

    }
    
    elseif($userrow["tempquest"] == "bucket") { //If not got healing water from cave, BUT found an empty bucket, display this.

        $title = "Potion Assistant Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>Potion Assistant Quest</td></tr></table><p>";
        $page .= "Lucas: Please hurry with my ingredients. My son only has a matter of hours left to live. I still need some Healing Water. An Empty Bucket isn't going to help now, is it?<br /><br />\n";
        $page .= "<i>Lucas continues making his Antidote Potion. You can see tears coming from his eyes.</i><br /><br />\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to find the Healing Water!<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='Potion Assistant Quest',quest2='Started',tempquest='bucket' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 

    }

elseif($userrow["tempquest"] == "water") { //Found healing water. Give it to lucas

        $title = "Potion Assistant Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>Potion Assistant Quest</td></tr></table><p>";
        $page .= "<i>Lucas is on his knees, in tears and screaming at the top of his voice, 'Why me!? Why me?!'</i><br /><br />\n";
        $page .= "".$userrow["charname"].": What's wrong Lucas? I have found the Healing Water. Isn't this the last ingredient you require?<br /><br />\n";
        $page .= "<i>You hand Lucas the Bucket of Healing Water.</i><br /><br />\n";  
        $page .= "Lucas: Thank you, but I fear it is too late. While you were gone, someone came and kidnapped my son! It's terrible! I turned my back for 5 minutes and I then hear a loud scream, as something carried my Son away. I tried to stop the creature, but I got knocked back and I passed out for several minutes. I fear that there is no hope any more. <br /><br />\n";
        $page .= "".$userrow["charname"].": Do you know what direction they went? I can try to catch up with them and rescue your Son.<br /><br />\n";      
        $page .= "Lucas: They headed out of town. I'm not sure exactly where they went, though. Sorry.<br /><br />\n";  
        $page .= "".$userrow["charname"].": I will try to catch up with them both. Everything will be fine, you wait and see, Lucas.<br /><br />\n"; 
        $page .= "<i>Lucas walks off, with his eyes pointing down towards the floor, in tears. He looks like he has given up all hope of saving his Son.</i><br /><br />\n"; 
        $page .= "Go follow the Monster and rescue Lucas's <a href=\"quests.php?do=fightslime\">kidnapped Son</a>.<br /><br />\n";
$inventitems = "0,".$userrow["inventitems"].",0";
$newinventitems = str_replace(",93,", ",", $inventitems);
$updatequery = doquery("UPDATE {{table}} SET inventitems='$newinventitems', location='Potion Assistant Quest',quest2='Half Complete',tempquest='4' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 
}

elseif($userrow["tempquest"] == "4") { //If not killed slime, display this.

        $title = "Potion Assistant Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>Potion Assistant Quest</td></tr></table><p>";
        $page .= "Lucas: Please, please, please hurry! Go catch up with them as quickly as possible and rescue my Son!<br /><br />\n";
        $page .= "Go follow the Monster and rescue Lucas's <a href=\"quests.php?do=fightslime\">kidnapped Son</a>.<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='Potion Assistant Quest', tempquest='4' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 
}
elseif($userrow["tempquest"] == "5") { //If youve killed slime, display this.

        $title = "Potion Assistant Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>Potion Assistant Quest</td></tr></table><p>";
        $page .= "<i>You put Lucas's Son onto his bed. His Father runs towards his bedside to give him a hug. They are both crying, and this is such an emotional situation that even you feel a tear or two coming down your face.</i><br /><br />\n";
        $page .= "<i>Lucas quickly finishes making his Potion, and then places it into the Vial. He then goes back towards his Son, and slowly pours it into his mouth. Several minutes later his Son opens his eyes, and smiles slightly.</i><br /><br />\n";
        $page .= "Lucas: However can I repay you? I am so happy and grateful for all your hard work, I can never express the feeling which is running through me right now.<br /><br />\n";
        $page .= "".$userrow["charname"].": It was nothing; don't mention it. I was born to save innocent lives, such as your Son's.<br /><br />\n";
        $page .= "Lucas: No, no, I insist on giving you some kind of reward.<br /><br />\n";
        $page .= "<i>Lucas dissapears for a moment into another room. He then returns.</i><br /><br />\n";  
        $page .= "Lucas: Here, take this Potion, it may help you on your long journeys. Trust me, it will make you feel a whole lot better! You can also have these small rewards, too. I hope this is enough for all your hard work.<br /><br />\n";
        $page .= "<i>You drink the Potion which Lucas hands to you. You feel great strength running through your body.</i><br /><br />\n"; 
        $page .= "".$userrow["charname"].": Wow, this stuff is good! Thank you ever so much. I will see you around Lucas, I wish you and your Son a happy life.<br /><br />\n";
        $page .= "<font color=green>Congratulations, you have completed the Potion Assistant Quest! You have gained 15 to your Maximum Fatigue, 6 to your Maximum Ability Points and 35,000 Experience points!</font><br /><br />\n"; 
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to continue exploring.<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='Potion Assistant Quest', tempquest='none',questscomplete=questscomplete+1, maxfat=maxfat+15, maxap=maxap+6,experience=experience+35000, quest2='Complete' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 

  }
elseif($userrow["quest2"] == "Complete") { //Quest already completed, dont get reward more than once
        $title = "Potion Assistant Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>Potion Assistant Quest</td></tr></table><p>";
        $page .= "<i>You see Lucas and his Son playing a game through the window, by his bedside.</i><br /><br />\n";
        $page .= "<font color=red>You have already completed the Potion Assistant Quest.</font><br /><br />\n"; 
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to continue exploring.<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='Potion Assistant Quest', quest2='Complete' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 

      
}
}
    display($page, $title);
    }

    
function fightslime() { // Fight Slime - Quest 2

    global $userrow, $numqueries;

      if ($userrow["tempquest"] != 4) { header("Location: index.php"); die(); }

setcookie ("tempquest", $userrow["tempquest"]);
$updatequery = doquery("UPDATE {{table}} SET currentaction='Fighting',currentfight='2',currentmonster='247',currentmonsterhp='140',currentmonsterimmune='2',location='Venomous Slime' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

header("Location: index.php?do=fight"); die();


}    


function scientist() { // Mad Scientist quest
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

if ($userrow["mining"] < 25) { display("<p>Sorry, but you do not currently meet the requirements for this Quest. You must be level 25 in Mining, or above to access this Quest.<p>Please try again later when you are of a greater level.<br /><br />You may return to <a href=\"index.php\">town</a>, or use the compass to the right to start exploring.","You do not meet the level requirement"); die(); } 
 
if($userrow["quest1"] != "Complete") { //If Quest 1 isnt complete, show this.

        $title = "Mad Scientist Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>Mad Scientist Quest</td></tr></table><p>";
        $page .= "<p>Sorry, but you do not currently meet the requirements for this Quest. You must complete the Lost Fortune Quest to access this Quest.<p>Please try again later when you have completed the Lost Fortune Quest.<br /><br /><br /><br />\n";  
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to find that lost Ring!<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='Mad Scientist Quest' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 

}
elseif($userrow["quest3"] == "Not Started") { //Start quest if requirements are met
        $title = "Mad Scientist Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>Mad Scientist Quest</td></tr></table><p>";
        $page .= "<i>Arghh, this is driving me crazy! You stupid... !</i><br /><br />\n";
        $page .= "".$userrow["charname"].": What's the matter? I'm a stupid what?!<br /><br />\n"; 
        $page .= "Mad Scientist: No, this new Project that I am working on isn't going according to plan. I'm far too old to go Mining to gain the resources required to power it. Hang on…what are you doing here? What do you want? Can't you see that I am busy!<br /><br />\n";
        $page .= "".$userrow["charname"].": I overheard you shouting to yourself and came to see what the problem is. It just so happens that I have just obtained my first Pick Axe and I am learning to Mine Ores.<br /><br />\n"; 
        $page .= "<i>You sarcastically think to yourself 'Why is he known as the Mad Scientist?' *Laughs*</i><br /><br />\n"; 
        $page .= "Mad Scientist: Now what is wrong, what are you laughing at? Gah! I must be hearing things in my old age. Hmmm, wait a moment, did you just say that you are learning to Mine Ore? Would you be interested in getting me the Ore that I require to power this Project of mine?<br /><br />\n";     
        $page .= "".$userrow["charname"].": Sounds good. But what do I get from helping you?<br /><br />\n";
        $page .= "Mad Scientist: Well you will be getting mining experience from helping me, and I will throw in some other rewards too.<br /><br />\n";   
        $page .= "".$userrow["charname"].": Ok, well I don't really have anything else better to do. What Ores do you require?<br /><br />\n";
        $page .= "Mad Scientist: I require a lot of Ore, I do warn you. I need the following amount of Ores:<br>- 125 Iron Ores<br>- 60 Tin Ores<br>and 850 Copper Ores.<br /><br />Mad Scientist: Do you think you can get me all those?<br /><br />\n";   
        $page .= "".$userrow["charname"].": Yes of course. You weren’t kidding about there being a lot. I will get to work on them straight away. Which Ores do you require first?<br /><br />\n"; 
        $page .= "Mad Scientist: First, can you get me the 125 Iron Ores. You will require a lot of skill to be able to obtain these.<br /><br />\n";  
        $page .= "".$userrow["charname"].": Ok, I will get to work on them first and return when I have them.<br /><br />\n";
        $page .= "<i>The Mad Scientist continues to shout and scream...</i><br /><br />\n";                                                      
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to go find those Ores.<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='Mad Scientist Quest',quest3='Started' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 
}
elseif(($userrow["ore1"]*1) < 850 && $userrow["tempquest3"] == "copper") { //If user hasnt got the Copper THIRD ORES

        $title = "Mad Scientist Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>Mad Scientist Quest</td></tr></table><p>";
        $page .= "Mad Scientist: I still require 850 Copper Ores, please hurry since I am a very impatient man. You appear to only have ".$userrow["ore1"]." Copper Ores.<br /><br />\n"; 
        $page .= "".$userrow["charname"].": I'm trying, I will return soon.<br /><br />\n";         
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to find those Ores.<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='Mad Scientist Quest', quest3='Half Complete' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 

}
elseif(($userrow["ore1"]*1) >= 850 && $userrow["tempquest3"] == "copper") {// If user has the Copper THIRD ORES, complete quest

        $title = "Mad Scientist Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>Mad Scientist Quest</td></tr></table><p>";
        $page .= "<i>You take your time by placing the Ores into the front of the Project that the Mad Scientist is building. Maybe you should ask the Scientist what he is actually doing, but you don't want to upset him though.</i><br /><br />\n";        
        $page .= "Mad Scientist: Thank you, that's the last lot of Ores done. Now, please don't take offence to this, but please leave me right away while I finish my Project, without any interruptions.<br /><br />\n";
        $page .= "".$userrow["charname"].": What about my rewards? Also, what's the big secret regarding this 'Project', can you not tell me?<br /><br />\n";  
        $page .= "Mad Scientist: No I'm sorry, I can't tell anyone what it is about just yet. If you return later, I will let you in on the secret. Here is your rewards that I promised.<br /><br />\n";                   
        $page .= "<font color=green>Congratulations, you have completed the Mad Scientist Quest! You have gained 45,000 Mining Experience, along with 20 to your maximum TP. You also gain an Ability Elixir and 35,000 Gold!</font><br /><br />\n";   
        $page .= "<i>This quest is to be continued...</i><br /><br />\n";                    
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to continue exploring.<br /><br />\n";
        //Reward
        $inventitemsquery = doquery("SELECT id FROM {{table}}","inventitems");
        $userinventitems = explode(",",$userrow["inventitems"]);
        //add ability elixir and quest rewards
        array_push($userinventitems, 76);
        $new_userinventitems = implode(",",$userinventitems);
        $userid = $userrow["id"];
        doquery("UPDATE {{table}} SET ore1=ore1-850,miningxp=miningxp+45000,maxtp=maxtp+20,gold=gold+35000,questscomplete=questscomplete+1,location='Mad Scientist Quest', quest3='Complete',tempquest3='none',inventitems='$new_userinventitems' WHERE id='$userid' LIMIT 1", "users");        
} 

elseif(($userrow["ore2"]*1) < 60 && $userrow["quest3"] == "Half Complete") { //If user hasnt got the Tin SECOND ORES
        $title = "Mad Scientist Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>Mad Scientist Quest</td></tr></table><p>";
        $page .= "Mad Scientist: I still require 60 Tin Ores, please hurry since I am a very impatient man. You appear to only have ".$userrow["ore2"]." Tin Ores.<br /><br />\n"; 
        $page .= "".$userrow["charname"].": I'm trying, I will return soon.<br /><br />\n";         
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to find those Ores.<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='Mad Scientist Quest', quest3='Half Complete' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 

}
elseif(($userrow["ore2"]*1) >= 60 && $userrow["quest3"] == "Half Complete") {// If user has the Tin SECOND ORES
        $title = "Mad Scientist Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>Mad Scientist Quest</td></tr></table><p>";
        $page .= "<i>You take your time placing the Ores into the front of the Project that the Mad Scientist is building. The mechanical device looks very strange to you.. There's something suspicious going on here.</i><br /><br />\n";        
        $page .= "Mad Scientist: Thank you, that's the second lot of Ores done. Now I require 850 Copper Ores.<br /><br />\n"; 
        $page .= "".$userrow["charname"].": Ok, I will be as quick as I possibly can.<br /><br />\n";         
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to find those Ores.<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET ore2=ore2-60,location='Mad Scientist Quest', quest3='Half Complete',tempquest3='copper' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 
}
elseif(($userrow["ore3"]*1) < 125 && $userrow["quest3"] == "Started") { //If user hasnt got the Iron FIRST ORES

        $title = "Mad Scientist Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>Mad Scientist Quest</td></tr></table><p>";
        $page .= "Mad Scientist: I still require 125 Iron Ores, please hurry since I am a very impatient man. You appear to only have ".$userrow["ore3"]." Iron Ores.<br /><br />\n"; 
        $page .= "".$userrow["charname"].": I'm trying, I will return soon.<br /><br />\n";         
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to find those Ores.<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='Mad Scientist Quest', quest3='Started' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 

}
elseif(($userrow["ore3"]*1) >= 125 && $userrow["quest3"] == "Started") {// If user has the Iron FIRST ORES

        $title = "Mad Scientist Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>Mad Scientist Quest</td></tr></table><p>";
        $page .= "<i>You take your time by placing the Ores into the front of the Project that the Mad Scientist is building. The mechnical device looks very strange to you..</i><br /><br />\n";        
        $page .= "Mad Scientist: Thank you, thats one lot of Ores done. Now I require 60 Tin Ores.<br /><br />\n"; 
        $page .= "".$userrow["charname"].": Ok, I will be as quick as I possibly can.<br /><br />\n";         
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to find those Ores.<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET ore3=ore3-125,location='Mad Scientist Quest', quest3='Half Complete' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 
}

elseif($userrow["quest3"] == "Complete") { //If Quest 3 is complete, show this.

        $title = "Mad Scientist Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>Mad Scientist Quest</td></tr></table><p>";
        $page .= "<i>You hear loud noises coming from where the Mad Scientist is working. It's a shame you can't see what hes upto.</i><br /><br />\n";
        $page .= "<i>This quest is to be continued...</i><br /><br />\n";        
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to continue exploring.<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='Mad Scientist Quest' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 
}
    display($page, $title);
}

function parasite() { // The Parasite quest
    
    global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

if($userrow["quest2"] != "Complete" || $userrow["endurance"] < "15") { //If Quest 2 isnt complete, show this.

        $title = "The Parasite Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>The Parasite Quest</td></tr></table><p>";
        $page .= "<p>You scan the current occupants of the Quest Tower and settle upon a well-dressed man with a bandaged hand. He is furiously sorting through a large sheaf of papers and muttering to himself.<p>\"May I help you?\" you ask politely.<p>He pushes his spectacles back up his nose and looks you over. \"Oh, no, no,\" he mumbles. \"You'll never do. I'd need someone who's completed the Potion Assistant Quest and has an Endurance Level of at least 15.\" 
He returns to his papers and pays you no further attention.<br /><br /><br /><br />\n";  
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to find that lost Ring!<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='The Parasite Quest' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 

}
elseif($userrow["quest4"] == "Not Started") { //Start quest if requirements are met
        $title = "The Parasite Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>The Parasite Quest</td></tr></table><p>";
        $page .= "A well-dressed man with a bandaged hand rushes up to you as soon as you enter the tower.<br /><br />\n";
        $page .= "\"Yes!\" he cries excitedly. \"You will do perfectly. Perfectly!\"<br /><br />\n";         
        $page .= "Without explaining what you would do so perfectly he drags you by the arm to a corner of Quest Tower, which is strewn with countless sheets of paper. As you near you can see that the pages are covered with drawings of different creatures. Some you have seen before, others you have not.<br /><br />\n"; 
        $page .= "\"You of course would know who I am,\" he begins, \"but I am afraid I'm not acquainted with you. One adventurer looks pretty much the same as another to me.\"<br /><br />\n";
        $page .= "\"My name is ".$userrow["charname"].", but no, I'm afraid I don't know who you are.\"<br /><br />\n";  
        $page .= "He looks genuinely shocked by your apparent ignorance. Straightening the lapels of his coat unnecessarily, he announces: \"I am Magnus Von Strappeldinger, Royal Geographer! After providing most of this land with the maps you adventurers use, my current task is to document and categorise all the animals that inhabit it. My task is almost complete, but one creature has thus far eluded me.\"<br /><br />\n";     
        $page .= "\"I have seen many different creatures in my travels,\" you inform Magnus. \"Which one are you interested in?\"<br /><br />\n";
        $page .= "\"It is a small parasite that lives in the corpses of monsters. It only emerges when the corpse is disturbed, either by a person or a foraging animal. I have only ever seen one,\" he holds up his bandaged hand, \"and it did this to me. I want you to capture one and bring it back to me.\"<br /><br />\n";   
        $page .= "\"Easy!\" you begin.<br /><br />\n";
        $page .= "\"Alive,\" Magnus interrupts.<br /><br />\n";   
        $page .= "\"Ah. How would I do that?\"<br /><br />\n"; 
        $page .= "\"You'll need several things to accomplish this task,\" Magnus continues, pacing back and forth as if lecturing. He ticks the items off on his fingers as he explains.<br /><br />\n";  
        $page .= "\"One. You will need an Empty Crystal Jar. The crystal is required for the special resonance with which it calms the parasite. My last jar broke when I was bitten. Two. You will need a Dragon's Special from the Tavern. It helps to keep the parasite alive in the jar. Three. You will need to drink a Stun Potion.\"<br /><br />\n";
        $page .= "You look at Magnus quizzically. \"Why do I need to drink a Stun Potion to catch this parasite? Won't that only slow me down?\"<br /><br />\n";
	$page .= "\"Oh, no, no,\" he assures. \"This is a special Stun Potion that Lucas has made for me. It is harmless to humans, but when the parasite leaves the corpse it will most likely bite you. The Potion in your blood will then stun it, so that you can catch it. But first go and get the Crystal Jar, and then bring it back to me so that I can prepare it for the parasite.\"<br /><br />\n";
                                                      
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to go find that Jar.<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='The Parasite Quest',quest4='Started',tempquest='nojar' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 
}
elseif($userrow["tempquest"] == "nopara") { //If user hasnt got the parasite

        $title = "The Parasite Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>The Parasite Quest</td></tr></table><p>";
        $page .= "Magnus looks up from his papers as you enter.<br /><br />\n";   
        $page .= "\"Have you got the parasite?\" he asks excitedly. You shake you head.<br /><br />\n";
	$page .= "\"You must hurry!\" he urges you, \"before the Stun Potion wears off!\"<br /><br />\n";    
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to find a Parasite.<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='The Parasite Quest' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 

}
elseif($userrow["tempquest"] == "noparatwo") { //If user hasnt got the parasite, but tried once

        $title = "The Parasite Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>The Parasite Quest</td></tr></table><p>";
        $page .= "You hurry across to where Magnus is sorting out his papers.<br /><br />\n";   
        $page .= "\"I found a parasite,\" you inform him, \"but I was unable to catch it.\"<br /><br />\n";
	$page .= "\"So why are you back here?\" he asks.<br /><br />\n";
	$page .= "\"I was worried there wouldn't be enough Stun Potion left in my blood to disable another parasite.\"<br /><br />\n";
	$page .= "\"No, no, no.\" Magnus says, \"there is more than enough. More than enough. Quick, quick, you must be quick!\" He ushers you out the door.<br /><br />\n";    
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to find another Parasite.<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='The Parasite Quest' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 

}
elseif($userrow["tempquest"] == "gotpara") { //If user has got the parasite

        $title = "The Parasite Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>The Parasite Quest</td></tr></table><p>";
        $page .= "You triumphantly hold the Crystal Jar before you as you approach Magnus. The light streaming in from the tall windows highlights the small Parasite, swimming lazily in the Dragon's Special. An excited Magnus rushes towards you and takes the jar reverently into his care.<br /><br />\n";   
        $page .= "\"Thank you so very much. Very much.\"<br /><br />\n";    
        $page .= "\"It wasn't a problem at all,\" you begin, \"just had to…\" Your words trail off as you start to gag and retch. Your stomach feels like it's going to burst, and you're finding it hard to breathe.<br /><br />\n"; 
        $page .= "\"".$userrow["charname"]."!\" Magnus cries, wringing his hands in concern. \"Whatever is the matter?\"<br /><br />\n";  
        $page .= "\"The p...\" you gag. \"The po... the potion!\" You finally get the words out before rushing to the corner of the tower and violently emptying the contents of your stomach on the floor.<br /><br />\n";
        $page .= "\"Lucas assured me that the potion was harmless!\" Magnus wails, staring in horror as a shape starts to form from the vomit on the floor. The shape resolves itself into a <font color=red>Venomous Slime</font> and Magnus grabs the jar and runs screaming from the tower.<br /><br />\n";      
        $page .= "You feel very weak from the potion. You can barely raise your weapon as the Slime advances slowly towards you. Staunchly, you put yourself between it and the door.<br /><br />\n"; 
        $page .= "You should <a href=\"quests.php?do=fightpotionslime\">attack</a> it before it can leave the Quest Tower and threaten the town!<br /><br />\n"; 
$inventitems = "0,".$userrow["inventitems"].",0"; //Remove Jar with Parasites
$newinventitems = str_replace(",97,", ",", $inventitems);
$updatequery = doquery("UPDATE {{table}} SET inventitems='$newinventitems', location='The Parasite Quest', quest4='Half Complete',tempquest='slime' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 
}
elseif($userrow["tempquest"] == "slime") { //If user has completed half of the quest, and needs to kill the slime!

        $title = "The Parasite Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>The Parasite Quest</td></tr></table><p>";
        $page .= "You return to the Quest Tower. Magnus is not around, but the Venomous Slime is still there, busy consuming the remaining half of a chair. Once finished in the Tower it will no doubt attack the town.<br /><br />\n";   
        $page .= "You must <a href=\"quests.php?do=fightpotionslime\">attack</a> it again!<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='The Parasite Quest' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 

}

elseif($userrow["tempquest"] == "slimedead" || $userrow["tempquest"] == "flower" || $userrow["tempquest"] == "gotflower") { //If user has completed half of the quest, and has killed the slime!

        $title = "The Parasite Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>The Parasite Quest</td></tr></table><p>";
        $page .= "Magnus still hasn't returned to the Quest Tower, as far as you can see. You still feel very weak, and doubt you could perform any of the Skills, such as Mining. At least the Venomous Slime is dead!<br /><br />\n";   
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to continue exploring.<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='The Parasite Quest' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 

}
elseif($userrow["tempquest"] == "waiting" && $userrow["drink"] != "Dragons Special") { //If user hasnt got the dragons special

        $title = "The Parasite Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>The Parasite Quest</td></tr></table><p>";
        $page .= "Magnus is busily working over the Crystal Jar when you return. He looks up quickly when you enter.<br /><br />\n";   
        $page .= "\"Okay, okay. Now I need the Dragon's Special. Please may I have it?\" He looks at you expectantly. You look back at him.<br /><br />\n";
	$page .= "\"You do have it, don't you?\" Magnus asks.<br /><br />\n";
	$page .= "\"Well... no,\" you reply somewhat sheepishly.<br /><br />\n";
	$page .= "Magnus throws his hands up in exasperation. \"You can get one at any Local Tavern! Go and get one. Shoo, shoo!\" He chases you out of the Tower, flapping his arms as if he's herding geese.<br /><br />\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to find a Dragon's Special.<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='The Parasite Quest' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 

}
elseif($userrow["tempquest"] == "waiting" && $userrow["drink"] == "Dragons Special") { //If user has got the dragons special

        $title = "The Parasite Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>The Parasite Quest</td></tr></table><p>";
        $page .= "You carefully walk into the Quest Tower, trying not to spill the tankard of Dragon's Special. You almost bump into an old woman who is leaving. She looks at you balefully.<br /><br />\n";   
        $page .= "\"Drinking, at this time of the day! What is the Kingdom coming to?\" She raises her walking stick menacingly, so you quickly scurry over to Magnus before she can act on your threat.<br /><br />\n"; 
        $page .= "Magnus takes the tankard from you and looks into it.<br /><br />\n"; 
        $page .= "\"They seem to serve less and less each time I visit a Tavern,\" he muses.<br /><br />\n";  
        $page .= "\"Well, I did get a little thirsty.\"<br /><br />\n";     
        $page .= "\"Let's just hope that there is enough left.\" Magnus says reproachfully.<br /><br />\n";
        $page .= "He pours the Dragon's Special into the Crystal Jar. The murky liquid swirls in the bottom of the jar and Magnus sprinkles in a measure of spices. The liquid bubbles vigorously, almost overflowing, before calming. You go to take the Jar and set out to find the Parasite, but Magnus stops you with a raised eyebrow.<br /><br />\n";   
        $page .= "\"You still need to drink the Stun Potion,\" he reminds you. \"I'll be back with it in a few moments. Lucas believes it works best when freshly made.\"<br /><br />\n";     
        $page .= "He hurries from the Tower. Several minutes later he returns, holding a small vial of viscous green liquid.<br /><br />\n";  
        $page .= "\"Here,\" he says, proffering you the vial. \"Drink this.\"<br /><br />\n";     
        $page .= "You take a tiny sip of the Potion.<br /><br />\n"; 
        $page .= "\"It's vile!\" you exclaim, screwing up you face. Magnus shrugs sympathetically as you quickly drink the rest of the awful concoction.<br /><br />\n";                 
        $page .= "As you take the Crystal Jar, Magnus urges you to be quick.<br /><br />\n";
	$page .= "\"I'm not sure how long the Stun Potion will last,\" he says.<br /><br />\n";
	$page .= "You leave the Quest Tower to begin your search.<br /><br />\n";                              
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to find a Parasite.<br /><br />\n";
$inventitemsquery = doquery("SELECT id FROM {{table}}","inventitems");
        $userinventitems = explode(",",$userrow["inventitems"]);
        //add jar
        array_push($userinventitems, 96);
        $new_userinventitems = implode(",",$userinventitems);
        $userid = $userrow["id"];

$updatequery = doquery("UPDATE {{table}} SET inventitems='$new_userinventitems', location='The Parasite Quest', drink='Empty', tempquest='nopara' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 
}
elseif($userrow["tempquest"] == "nojar") { //If user hasnt got the jar

        $title = "The Parasite Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>The Parasite Quest</td></tr></table><p>";
        $page .= "Magnus seems to be ordering his papers in reverse alpha-numerical when you return to the tower. You cough softly to draw his attention.<br /><br />\n";   
        $page .= "\"What do you want?\" he asks. Something about his tone makes you think that he doesn't remember who you are.<br /><br />\n";
	$page .= "\"The Parasite?\" you prompt.<br /><br />\n";
	$page .= "\"Ah! Yes, yes,\" he says. \"Now, you hand me the Jar and I'll add a...\"<br /><br />\n";
	$page .= "Magnus starts inspecting your person as if searching for something. Behind his spectacles, his eyes are somewhat reminiscent of an owl's.<br /><br />\n";
	$page .= "\"You do have a Crystal Jar, don't you?\" he finally asks.<br /><br />\n";
	$page .= "\"I was finding it difficult to locate,\" you tell him. \"I was wondering if you remembered where you got your last one.\"<br /><br />\n";
	$page .= "Magnus looks deep in thought for a moment.<br /><br />\n";
	$page .= "\"No. I do not recall where I bought my last one, although I seem to remember buying a rather lovely Ring at the same time. Sorry I can't be of more help.\"<br /><br />\n";
	$page .= "He returns to his ordering, resorting his documents by colour.<br /><br />\n";    
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to find a Crystal Jar.<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='The Parasite Quest' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 

}
elseif($userrow["tempquest"] == "gotjar") { //If user has got the jar

        $title = "The Parasite Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>The Parasite Quest</td></tr></table><p>";
        $page .= "Being careful not to break your brand new Crystal Jar, you return to where Magnus is taking stock of the animals he has categorised.<br /><br />\n";  
        $page .= "\"Is this the right sort of Jar?\" you ask him.<br /><br />\n";         
        $page .= "He takes the Crystal Jar and places it on the desk. Pulling a small mallet out of his coat pocket, he lightly taps the rim. A note of painful purity echoes through the Quest Tower.<br /><br />\n";   
        $page .= "\"Perfect!\" he exclaims. \"Absolutely perfect! Now I need a Dragon's Special drink.\"<br /><br />\n";
	$page .= "\"Ah, that shouldn't be too hard,\" you reply. \"I will return shortly.\"<br /><br />\n";   
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to find a Dragon's Special.<br /><br />\n";
$inventitems = "0,".$userrow["inventitems"].",0"; //Remove Jar
$newinventitems = str_replace(",95,", ",", $inventitems);
$updatequery = doquery("UPDATE {{table}} SET inventitems='$newinventitems', tempquest='waiting',location='The Parasite Quest' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 
}
elseif($userrow["tempquest"] == "magnus") { //If user has cured himself, speak to magnus to complete quest and get reward.

        $title = "The Parasite Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>The Parasite Quest</td></tr></table><p>";
        $page .= "You return to the Quest Tower. Someone has done a good job of clearing up the remains of the Venomous Slime. The damage it caused has been repaired and normal hero-ing business has resumed.<br /><br />\n";        
        $page .= "Magnus looks up sheepishly as you stride to where he is examining the Parasite Jar.<br /><br />\n";   
        $page .= "\"I hope you running off wasn't an attempt to avoid my reward,\" you say sternly, running your fingers over your weapon in what you hope is a menacing fashion.<br /><br />\n"; 
        $page .= "Magnus looks horrified at the thought.<br /><br />\n";              
        $page .= "\"No, no,\" he assures. \"Partly I wanted to protect the Parasite and make sure the Jar wasn't broken by the Slime,\" he pauses, \"but mostly I was terrified out of my mind.\" He looks suitably ashamed.<br /><br />\n";
	$page .= "\"So you have a reward for me?\"<br /><br />\n";
	$page .= "\"Of course. And I am ever so grateful for your help, but now I must really categorise this Parasite. Do excuse me.\" He hands you a large sack before his attention becomes firmly fixed on the Parasite swimming in the Jar. You open the sack and discover you reward.<br /><br />\n";
	$page .= "<font color=green>You have gained 35,000 experience points, 72,000 Gold and 65 Dragon Scales!<br /><br />\n";
	$page .= "Congratulations you have completed The Parasite Quest!</font><br /><br />\n"; 
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right.<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='The Parasite Quest', quest4='Complete', tempquest='none', experience=experience+35000, gold=gold+72000, dscales=dscales+65, questscomplete=questscomplete+1 WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 
}
elseif($userrow["quest4"] == "Complete") { //If Quest 4 is complete, show this.

        $title = "The Parasite Quest";
        $page = "<table width='100%' border='1'><tr><td class='title'>The Parasite Quest</td></tr></table><p>";
        $page .= "Magnus is staring at the small Parasite in the Crystal Jar, furiously scribbling notes on a page. Further bandages attest to his attempts to get a closer look at the creature. You think it best not to disturb his study.<br /><br />\n";
        $page .= "<font color=red>You have already completed The Parasite Quest.</font><br /><br />\n";      
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to continue exploring.<br /><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='The Parasite Quest' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 
}

    display($page, $title);
}

function fightpotionslime() { // Fight Slime AGAIN - Quest 4

    global $userrow, $numqueries;

      if ($userrow["tempquest"] != "slime") { header("Location: index.php"); die(); }

setcookie ("tempquest", $userrow["tempquest"]);
$updatequery = doquery("UPDATE {{table}} SET currentaction='Fighting',currentfight='2',currentmonster='247',currentmonsterhp='581',currentmonsterimmune='2',location='Venomous Slime' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

header("Location: index.php?do=fight"); die();


}    
?>