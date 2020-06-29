<?php // helpguide.php :: Help Guide

require_once('lib.php');

//include('login.php');
include('cookies.php');
$link = opendb();
$userrow = checkcookies();
	if ($userrow["tutorial"] == 1) { header("Location: index.php"); die(); } //Already done the tutorial
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
if ($userrow["authlevel"] == 2 || ($_COOKIE['dk_login'] == 1)) { setcookie("dk_login", "1", time()+999999999999999); 
die("<b>You have been banned</b><p>Your accounts has been banned and you have been placed into the Town Jail. This may well be permanent, or just a 24 hour temporary warning ban. If you want to be unbanned, contact the game administrator by emailing admin@dk-rpg.com."); }

$updatequery = doquery("UPDATE {{table}} SET location='Tutorial' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
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

	if ($do[0] == "gforum") { header("Location: gforum.php"); die();}
	elseif ($do[0] == "first") { first($do[1]); }
    elseif ($do[0] == "second") { second($do[1]); }	

} else { donothing(); }


function donothing() {
	global $userrow;

$charname = $userrow["charname"];
  	$page = "<table width=\"100%\"><tr><td class=\"title\">Tutorial</td></tr></table>";



$page .= <<<END
<table>
<tr>
  <td height="113" colspan="2"><hr />
    <h3>Dragon's Kingdom Tutorial</h3>
    You slowly open your eyes, and look at your surroundings. It looks like you are in some kind of inn. There is a shabbily bearded man beside your bed. He notices that you're awake and smiles.
    <p>Strange Man: Well hello there, you're finally awake. I was wondering when you were going to come-to. You've been out for some time now. What's your name?</td>
  </tr>
<tr>
  <td width="118"><img src="images/monsters/8.gif" border="0" alt="Drakelor" /></td>
  <td width="635">Your memories come flooding back. You remember your name is $charname and where you came from, but not how you got to this place. You tell him what your name is, then ask him where you are. He looks at you for a moment before making his reply.</td>
</tr>
<tr>
  <td colspan="2">

  
Strange Man: You're in the local inn, in the Kingdom of Valour, on the region of Dragon's Kingdom.
  That bump on your head must of did more than I thought. Anyway, I'm Herak, the owner
  of this inn. A man brought you here and paid for your stay, and told me to look after you.
  He was kind of a strange fellow, dressed in a large cloak. He also told me to teach you
  the basics of adventuring.
<p>

Herak: The first thing to do is to go to town. The Kingdom of Valour is the main town area. 
<p>Here you will see 3 panels, firstly the left panel contains basic player information and six coloured status bars:



<ul>
  <li>Hit Points (HP) - This is your Health, which will be drained as you are hurt.  </li>
 <li>Magic Points (MP) - Used only for Spells.  </li>
  <li>Ability Points (AP) - Useful for Guild activities. </li>
   <li>Travel Points (TP) - These are used to travel from town to town, as you gain more maps.   </li>
    <li>Fatigue Percent (FP) - Tracks your current Fatigue level, when it is full, you must rest using your Desert Tent.  </li>
     <li>Experience Points (Exp) - Lets you know how close you are to leveling your character level up.  </li>

       </ul>		 
	<p>
		

Herak: Directly under the status bars you will find these menus, along with another menu below the Compass to the right: 

<ul>
  
	<li>Quick Spell List - This menu is used for Spells which are not used during battle. </li>
	<li>Quick Items List - This is similar to your Quick Spells, but instead, it involves your Held Items. </li>
	<li>Links - Various different links to view other areas of the game, such as your private Game Mails and Player Options. </li>
 </ul>	

 <p>
 
Herak: The right panel contains the compass images which is how you navigate
your way through the battle field by clicking a direction, as well as showing
your current location. You may also click the link to view the battle field map, which will show
 your current location, as you move.  Also listed are the towns
which you have either discovered or bought the maps to, which are used to quickly travel to other towns.

 
	<p>
	Finally, there is the main central panel which displays all the current actions of what's happening during playing
	including links to other areas in town, depending on your current action and location. You will first notice many links to different areas within town.<p>



  [ <a href="tutorial.php?do=first">Continue Tutorial</a> ]
  <br /><br /><center><img src="images/monsters/246.gif" border="0" alt="King Black Dragon" /></a></center>
  

<br /><hr />

<br /><br /></td>
  </tr>
</table>
END;

    display($page,"Tutorial");
}

function first() {
	global $userrow;

  	$page = "<table width=\"100%\"><tr><td class=\"title\">Tutorial</td></tr></table>";



$page .= <<<END
<table>
<tr>
  <td height="147" colspan="2"><hr />
    <h3>Dragon's Kingdom Tutorial</h3>
    <p> Herak: Let's discuss exploring and combat. You can explore Dragon's Kingdom by using the compass on your right. While exploring you can find treasure chests, which may have some gold, or even a few dragon scales. You will also encounter a wide assortment of monsters to fight. Killing monsters will gain you experience, gold, dragon scales and items. You may attack the creature with your weapon, or cast a spell on it. Using spells will deplete your magic points. Beware though - the deeper you travel into the world, the greater the risks - and rewards. I also advise you to keep a few healing items with you, just in case. But you can't explore forever, eventually you will become tired and need to rest.<p>To start exploring, click on a direction on the compass image. Every 5 steps increases the monsters level by 1. So stay close to town to survive. If you die, you will lose 1/2 of your held Gold, and 2/3 of your Dragon Scales!</td>
  </tr>
<tr>
  <td width="91"><img src="images/icon_chest.gif" border="0" alt="Treasure Chest" /></td>
  <td width="662"><p>Herak: Now for mining and smelting. These skills will be two of your most profitable skills in Dragon's Kingdom. You may go to the Mining Field outside of town to mine, but note - to mine, first you will need to get a pickaxe somehow. Mining isn't easy work, and you will tire quite easily. With your ores that you mine, you can take those to the Smelting Furnace and make them into bars, which are even more profitable. I hear the local blacksmith will buy any ores or bars which you may have.
    <p> Herak: Ah endurance. This skill will determine how long you can go for without a nap, and how many Ability Points you can get. Ability Points are for teleporting to your guild strongholds and other functions in your guild. There are many guilds you may join - if you are accepted.</td>
</tr>
<tr>
  <td colspan="2"><p><center><img src="images/stronghold.jpg" alt="Stronghold" border="0" align="center" /></center></a></p>
    <p>Herak: There are skills, which you can pay gold to level up. Go to the skill shrine to
      level these different skills up. There are currently four skills that you can level up here.
      They will either give you more gold per monster kill, give you more experience per
      monster kill, block against some damage, and even add extra damage onto your attack.
      </p>

<br /><br />  [ <a href="tutorial.php?do=second">Continue Tutorial</a> ]
  

  
<br /><hr /></td>
  </tr>
</table>


END;

    display($page,"Tutorial");
}

function second() {
	global $userrow;

  	$page = "<table width=\"100%\"><tr><td class=\"title\">Tutorial</td></tr></table>";



$page .= <<<END
<table>
<tr>
  <td height="147" colspan="2"><hr />
    <h3>Dragon's Kingdom Tutorial</h3>
He starts walking away, and you get up and follow him.
<p>
Herak: Now I hear that there are some folks who require some assistance up at the Quest
Tower. You may want to check there to see what needs to be done. You will probably get some
 rewards. 
  <p><img src="images/monsters/10.gif" alt="Scamp" border="0" align="right" />
 <p>Herak: Oh and I almost forgot. That man left these for you
<p><i>He hands you a leather bag which contains some gold and some items for you to start
off on. You gain a Desert Tent and a New Player Potion to begin your journey.</i><p>
Herak: You can use these items I given to you from using the Quick Items menu, from the left hand side.<p>
Herak: You may want to see the local blacksmith for some basic gear. He can be found in the
town square. Ah one final thing, visit the daily bonus arena once a day, since the people over there are very helpful.<p>
Herak: Come back here any time if you're in need of healing. I can let you rest here for a small sum of gold.

Herak: Good luck! he tells you as you walk out of the door, and into the world known as
Dragon's Kingdom, you're going to need it.

    <p>
    
  [ <a href="index.php">Begin Playing</a> ] [ <a href="helpguide.php?do=tutorial">View Tutorial Again</a> ] [ <a href="helpguide.php">View Help Guide</a> ]
  
<p>
<img src="images/troops.gif" border="0" alt="Troops" />
  
<br /><hr />

<br /><br /></td>
  </tr>
</table>
END;

        $inventitemsquery = doquery("SELECT id FROM {{table}}","inventitems");
        $userinventitems = explode(",",$userrow["inventitems"]);
        //add Desert Tent, new player potion and mark tutorial as read.
        array_push($userinventitems, 1,79);
        $new_userinventitems = implode(",",$userinventitems);
        $userid = $userrow["id"];
        doquery("UPDATE {{table}} SET inventitems='$new_userinventitems',tutorial='1' WHERE id='$userid' LIMIT 1", "users");



    display($page,"Tutorial");

}
?>