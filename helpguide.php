<?php // helpguide.php :: Help Guide

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

$updatequery = doquery("UPDATE {{table}} SET location='Help Guide' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
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
	elseif ($do[0] == "beginners") { beginners($do[1]); }
    elseif ($do[0] == "guilds") { guilds($do[1]); }
    elseif ($do[0] == "skills") { skills($do[1]); }    
    elseif ($do[0] == "faqs") { faqs($do[1]); }    
    elseif ($do[0] == "rules") { rules($do[1]); }          	
    elseif ($do[0] == "tos") { tos($do[1]); } 
	elseif ($do[0] == "tutorial") { tutorial($do[1]); }
	elseif ($do[0] == "first") { first($do[1]); }	
    elseif ($do[0] == "second") { second($do[1]); }	    
    elseif ($do[0] == "magicfind") { magicfind($do[1]); }     	

} else { donothing(); }


function donothing() {
	global $userrow;

  	$page = "<table width=\"100%\"><tr><td class=\"title\">Help Guide</td></tr></table>";



$page .= <<<END
<table>
<tr><td>
<br /><hr />

<table width="430" height="202" border="0">
  <tr>
    <td height="47" colspan="2"><h3>Help Guide Contents</h3></td>
  </tr>
  <tr>
    <td width="218" height="149">
<ul>
<li /><a href="#intro">Introduction</a>
<li /><a href="#classes">Character Classes</a>
<li /><a href="helpguide.php?do=beginners">Beginners Guides</a>
<li />
<a href="helpguide.php?do=guilds">Guilds and Strongholds </a>
<li /><a href="helpguide.php?do=skills">Skills Information</a>    
<li /><a href="helpguide.php?do=magicfind">Magic Find</a>   
<p>
<li /><a href="helpguide.php?do=rules">Official DK Rules</a>
      <li /><a href="helpguide.php?do=tutorial">View Tutorial Again</a>
<li /><a href="helpguide.php?do=faqs">Frequently Asked Questions (FAQ's)</a>
</ul>
 </td>
    <td width="196"><ul>
      <li />    
      <a href="help_items.php">Items & Drops</a>
      <li />    
      <a href="help_monsters.php">Monsters Stats</a>
      <li />    
      <a href="help_spells.php">Spells</a>
     <li />    
      <a href="help_skill_levels.php">Skills Experience</a> 
      <li />    
      <a href="help_levels.php">Character Levels, Attributes and Experience</a>
    </ul></td>
  </tr>
</table>
<hr />
<a name="intro"></a><h3>Introduction</h3>
Welcome to the world of Dragon's Kingdom, an Online Browser Based RPG. <p>

Long ago in a desolate wasteland, a king had risen up. He was known as the King Black Dragon, and his loyal followers were a dangerous bunch. For many Millennia he ruled over mankind, and slowly kingdoms started to rise, the first being the kingdom of Valour. This kingdom quickly flourished, and a hero rose up from amongst them and ventured to slay the Dragon King, and many thought that this would prove to be a useless endeavour. He too felt the same, but he easily slayed the followers, and went on to slay the King. He was successful and ended the tyrannical reign.
<p>
Among all of this a new kingdom was unnoticed, known as the Necromancer Valley. Many of the followers lived there and sorcerers moved in over time, and in a matter of years they were able to get all of the things needed to bring back their King. At first he was weak, and grew stronger as they empowered him, using all the magic they had. He is now planning to take over and rule again, but he is more evil than ever, and more powerful than before. He exiled his loyal followers, and saviours, and killed many of them. He now issues a new challenge for anyone brave enough to try and slay him, and his new follower, his sons. All efforts until now have been proven to be futile, but now it is your time to rise up and accept the challenge of this tyrant. 
<p>
Will you be able to slay him and bring peace back into the kingdoms? Or will all of our efforts just make him stronger, and create a new darker rule.

<p>
You must take the role of a bold adventurer from the Kingdom Of Valour that
must now have the courage to defeat the
monsters of Dragon's Kingdom. Although only carrying just your wits,
bravery and gold,
you must fight to survive this hostile wilderness and maybe, one day,
become the true
warrior and slay the King Black Dragon.
<p>Choose your profession, whether to be a mighty Sorceress, a worthy
Barbarian,
a powerful Paladin, a skillful Ranger, a dark Necromancer, a deadly
Druid or a killer Assassin. Choose your armament to suit your skill and
match your personality.
Choose your path, and begin your journey of exploring the world of
Dragon's Kingdom.
<p>
[ <a href="#top">Top</a> ]

<hr />
<h3><a name="classes"></a>Character Classes</h3>
There are five character classes in the game. The main differences
between the classes are what spells you get
access to, the speed with which you level up, and the amount of 
HP/MP/strength/dexterity
you gain per level. Below
is a basic outline of each of the character classes. For more detailed
information about the characters, please
view the Levels table at the bottom of this page.<br />
<br />
<b>Sorceress</b>
<ul>
<li />Fast level-ups
<li />High hit points
<li />High magic points
<li />Low strength
<li />Low dexterity
<li />Medium travel points
<li />Normal ability points
<li />5 heal spells
<li />5 hurt spells
<li />4 sleep spells
<li />3 +defense spells
<li />1 +attack spells
</ul>
<ul>
  <li />
  Notes: Sorceress has the majority of the spells, and the best spells
in the game. They also have very high magic points and high hit points.
Down points are her strength and dexterity but these are not needed for
a Sorceress.
</ul>
<b>Barbarian</b>
<ul>
<li />Medium level-ups
<li />Medium hit points
<li />Low magic points
<li />High strength
<li />Low dexterity
<li />Medium travel points
<li />Normal ability points
<li />3 heal spells
<li />3 hurt spells
<li />3 sleep spells
<li />3 +defense spells
<li />3 +attack spells
</ul>
<ul>
  <li />
  Notes: The Barbarian has very high strength for maximum damage with a
medium amount of hit points. Downsides of the Barbarian are that they
don't get many good spells and have low dexterity.
</ul>
<b>Paladin</b>
<ul>
<li />Slow level-ups
<li />Medium hit points
<li />Medium magic points
<li />Low strength
<li />High dexterity
<li />Medium travel points
<li />Normal ability points
<li />4 heal spells
<li />4 hurt spells
<li />4 sleep spells
<li />2 +defense spells
<li />2 +attack spells
</ul>
<ul>
  <li />
  Notes: The Paladin has high dexterity for maximum agility along with
medium hit and magic points. Downsides are the low strength and slow
level ups.
</ul>
<b>Ranger</b>
<ul>
<li />Fast-Medium level-ups
<li />Low-Medium hit points
<li />
Medium-High magic points
<li />Low strength
<li />High dexterity
<li />High travel points
<li />Normal ability points
<li />2 heal spells
<li />5 hurt spells
<li />2 sleep spells
<li />2 +defense spells
<li />3 +attack spells
</ul>
<ul>
  <li />
  Notes: The Ranger has high dexterity and high travel points for
maximum agility and traveling across the lands of Dragon's Kingdom.
Downsides are low hit points and low strength.
</ul>
<b>Necromancer</b>
<ul>
<li />Fast level-ups
<li />High hit points
<li />High magic points
<li />Low-Medium strength
<li />Low dexterity
<li />Medium travel points
<li />Normal ability points
<li />
2 heal spells
<li />
4 hurt spells
<li />
3 sleep spells
<li />3 +defense spells
<li />
2 +attack spells
</ul>
<ul>
  <li />Notes: The Necromancer is very similar to the Sorceress. It has
fast level ups, some of the best spells (mainly attack and defensive
curses) and high hit points. Downsides are its low dexterity.
</ul>
<b>Druid</b>
<ul>
<li />Medium level-ups
<li />
Medium hit points
<li />Low magic points
<li />High strength
<li />Low dexterity
<li />Medium travel points
<li />Normal ability points
<li />
3 heal spells
<li />
3 hurt spells
<li />
2 sleep spells
<li />
2+defense spells
<li />
2+attack spells
</ul>
<ul>
  <li />
  Notes: The Druid is similar to the Barbarian, it has very high
strength for maximum damage with a medium amount of hit points. This
character is an all rounder-type character.
</ul>
<b>Assassin</b>
<ul>
<li />
Slow level-ups
<li />Medium hit points
<li />
Medium magic points
<li />High strength
<li />High dexterity
<li />
High travel points
<li />Normal ability points
<li />
2 heal spells
<li />
5 hurt spells
<li />
1 sleep spells
<li />
1+defense spells
<li />
2+attack spells
</ul>
<ul>
  <li />
  Notes: The Assassin has very high strength for maximum damage and is
very similar to the Ranger with high dexterity and travel points.
Downsides are that they don't get much magic points to use their unique
spells.
</ul>
[ <a href="#top">Top</a> ]

<br /><br /><hr />

[ <a href="#top">Top</a> ]



<br /><br />
</td></tr>
</table>
END;

    display($page,"Help Guide");
}

function beginners() {
	global $userrow;

  	$page = "<table width=\"100%\"><tr><td class=\"title\">Help Guide</td></tr></table>";



$page .= <<<END
<table>
<tr><td>


[ <a href="helpguide.php">Return to Main</a> ]

<br /><br /><hr />

<h3><a name="begin"></a>Beginners Guide to Getting Started</h3>

So you've just signed up, and you're not really sure what to do... This
guide is here to fix that.
The main guide is in normal text, tips are in <i>italics</i> and
warnings are in <b>bold</b>.
<p>
First things first take a look around the Kingdom Of Valour. Click on 'Local
Blacksmith' to see the Weapons and armour available there, and buy a
few basic items such as a club and some clothes.
<p><i>Tip: I don't recommend you spend all your money, because you'll
need some spare for resting at the inn. However if you do happen to spend
all your money click on 'Local Tavern' and click to talk to people, after 
which
click 'Wealthy Gambler' and he shall give you 25 gold so you may rest at 
Inn.</i>
<p>
<b>Warning: However if you click on 'Wealthy Gambler' while you have a lot
of money on you or in bank he shall take all of it and you'll be stuck with 
25 gold.
So remember to read <i> Everything </i></b>
<p>
Once you're equipped, take a look at the battlefield map... it's not
particularly important, but it looks cool. It can also give you a some what 
general
idea where other Towns are located if you decide to walk to them. <i> You 
get the map
of a Town for free if you walk there instead of buying it.</i>
<p>
Now it's time to start exploring, click the North, South, East and West
buttons to move a step in your chosen direction. Sooner or later you
will come across monsters.
Choose to fight them and see how you do. If you beat the monster, you
will recieve some gold, some experience and sometimes an item, which
will help you in battle. If you're losing, don't hesitate to run, since
healing is much cheaper than dying.
<p><b>Warning: Every 5 steps away from town you go (eg. 5N, 10N, 15N...)
the monsters get stronger. At level 1, even the weaker monsters can be
dangerous at low level.</b>
<p><i>Tip: The easiest way to explore at first is just to walk around
the town. This way you're never far from safety. Also remember when moving
to click the compass directions, as clicking the button below always moves 
you North.</i>
<p>
As your HP falls (from enemies hurting you) the green bar will drop.
When about half your health has gone, it will turn yellow, and when you're
really in trouble it will turn red. When the bar is getting low, or
even turns red, it's a good idea to heal, either using the heal spell (if
you have it) or by travelling back to the inn. Resting in the Kingdom of Valour
inn costs 3 gold, and restores your Hitpoints, Magic points and Travel
points to maximum.
<p>
Getting to level 2 is tough, but so long as you heal when you need to
you should get there. When you do get there, you'll probably find you
need to heal less often and can defeat enemies faster.
<p><i>Tip: If you've learnt the heal spell, use it whenever your HP is
dropping, and try and avoid having to use it in battle, since it gives
the opponent a free attack.</i>
<p>
You'll now be able to train up further and at the same time earn a bit
of money. When you've got enough, buy better items from the blacksmith.
<p><i>Tip: Buy the best things you can: it may be tempting to go for the
cheaper option, but it just means you'll have to save up for longer to
get the better item.</i>
If you've bought everything from the Kingdom Of Valour, buy the Raminant
map from the travel shop and go there to buy items.
<p><b>Warning: Don't start exploring from Raminant (not yet, anyway) the
monsters around there are very strong. Ignore this warning at your own
peril.</b>
<p>
Later you'll be able to explore further and defeat stronger monsters,
collecting a lot of gold and experience on the way.
<p>
Good luck on your travels, Adventurer...
<p>
<b>Written by Dave Mongoose.</b>
<p>
[ <a href="#top">Top</a> ]
<br /><br /><hr />

<h3><a name="intown"></a>Basics - Playing The Game: In Town</h3>
When you first begin a New Game you start out in Kingdom of Valour
and the town has quite a few areas to explore. As will be explained.
Clicking <b>Town Square</b>, <i>Skill Shrine</i>, <b>Gambling Den</b>,
<i>Guild Courtyard</i>, <b>Temple of Rebirth</b>, <i>Hall of Fame</i>,
<b>Out of Town Jail</b> and <i>Daily Bonus Area</i> will all bring you
to new areas of the town.<br />
<br /><b>View Town Map</b> This function at the moment is still under
construction and will eventually be available.<br />
<br />
To heal yourself, click the  <b>Rest at the Inn</b> link at the top of the
town square screen. Each town's Inn has a different price - some towns
are cheap, others are expensive. No matter what town you're in, the Inns
always serve the same function: they restore your current
hit points, magic points, and travel points to their maximum amounts.
Out in the field, you are free to use healing spells to restore
your hit points, but when you run low on magic points, the only way to
restore them is at an Inn.<br />
<br />
<b>Town Bank</b> is a place where you may store any gold you have.
At the moment you either have to deposit all of your money or withdraw
all of your money. It is very useful; because when you die with gold on you
you lose half of it. So using the bank is a very smart thing to do. You can also trade gold between other players but you will be charged a tax. <b>Hold
your gold on your person at your own risk.</b><br />
<br /><b>Local Tavern</b> is a place where you may talk to NPCs 
(Non-playable
Characters.) or buy drinks. These drinks have unique effects, and wear off 
when you heal
at the Inn. Here are the drinks and what they do.<br />
<br /><b>Mug of Ale:</b> Increases the chance for monster drops.<br />
<br /><b>Shot of Whiskey:</b> Increases the chances of winning at the 
casino. (Gambling Den area.)<br />
<br /><b>Dragons Special:</b> Increases the chances of getting an excellent 
hit.<br />
<br />
Buying weapons and armor is accomplished through the appropriately-named
<b>Town Blacksmith</b> link. Not every item is available in
every town, so in order to get the most powerful items, you'll need to
explore some of the outer towns. Once you've clicked the link,
you are presented with a list of items available in this town's store.
To the left of each item is an icon that represents its type:
weapon, armor or shield. The amount of attack/defense, as well as
the item's price, are displayed to the right of the item name.
You'll notice that some items have a red asterisk (<span 
class="highlight">*</span>)
next to their names. These are items that come
with special attributes that modify other parts of your character
profile. See the Items & Drops table at the bottom of this page for
more information about special items.<br /><br />
Maps are the third function in towns. Buying a map to a town places the
town in your Travel To box in the right status panel. Once
you've purchased a town's map, you can click its name from your Travel
To box and you will jump to that town. Travelling this way
costs travel points, though, and you'll only be able to visit towns if
you have enough travel points. The maps can be bought at the <b>Travel 
Store</b>
or you can get town maps by walking to the other towns.<br />
<br /><b>Warning: Walking to other towns at a low level will probably be the 
death
of your character, so walk to another town at your own risk.</b><br />
<br /><b>Market Place</b> is where you can buy jewellery or potions. The
<b>Potion Stall</b> has three potions at the moment which will temporarily
increase your stats. They, like tavern drinks, wear off when you rest at the 
Inn.
Potions available are the ones listed.
<b>Ogres Potion:</b> Adds a 0-7% increase to strength. Costs 620 Gold.
<b>Goblins Potion</b> Adds a 0-7% increase to dexeterity. Costs 620 Gold.
<b>Dragons Potion</b> Adds a 0-7% increased chance to prevent pre-emp hits. 
Costs 720 Gold.<br />
<br /> The <b>Jewellers Stall</b> has several different types of Rings and Amulets to find which
add to your Magic Find. You can find more information about this by clicking <a href="helpguide.php?do=magicfind">here</a>.<br />
<br />
<b>Hall of Legends</b> is a place where you can get a title for your 
character and
profile depending on your characters level. You also receive a nice reward of a new and unique spell for each title you claim. <br />
<br />
<b>Skill Shrine</b> when clicked brings you to a place where you can 
train the four
skills that every class starts with. At the moment they are the only skills 
and one day each
class will have their own unique skills. Skills cost money to level up 
instead of training them
by clicking over an over. The skills are quite expensive but are worth it in 
the long run.
Skills get better after level 11. Until then you'll have to wait and try to 
be patient.
Here is a list of the current basic skills every class gets.<br />
<br /><b>Wisdom:</b> This gives you bonus experience after a fight.<br />
<br /><b>Stone Skin:</b> This absorbs a percentage of damage you receive 
from a monster attacks based on the level.<br />
<br /><b>Monks Mind:</b> This gives you bonus damage during a fight.<br />
<br /><b>Fortune:</b> This gives you bonus gold after a fight.<br />
<br /><i>Tip: Skills are also effected by stats. ie: Monks Mind does more 
damage if you
have high strength. The higher your strength the more damage it does.</i><br 
/>
<br /><b>Gambling Den</b> is a place where you can risk your money by 
gambling
on the mini games. I suggest playing Hi & Lo as it's the easiest one and you 
wont be wasting
money.<br />
<br /><b>Guild Courtyard</b> is still under construction. However when it 
does
come out, it will be where one can more or less buy a Guild or apply for 
one.<br />
<br /><b>Hall of Fame</b> is where you can see the top players in certain 
categories.
Such includes most money, highest level for each class, highest skill levels 
and highest overall
player in level.<br />
<br /><b>Out of Town Jail</b> shows the players who have been banned for 
different reasons
so make sure to review the rules.<br />
<br />
<b>Daily Bonus Area:</b> This is where you can come every 24hours to claim your reward. In return we ask you support this site and click the adverts once a day. Unless of course you send a 
<b>Donation</b> as that
would also be extremely helpful.<br />
<br />
The final function in towns is displaying game information and
statistics. This includes the latest news post made by the game
administrator, a list of players who have been online recently, and
other aspects of the game.<br /><br />
[ <a href="#top">Top</a> ]

<br /><br /><hr />

<h3><a name="exploring"></a>Basics - Playing The Game: Exploring &
Fighting</h3>
Once you're done in town, you are free to start exploring the world. Use
the compass images on the right status panel to move around.
The game world is basically a huge map, divided into squares. Each
quadrant is HERE spaces
square. The first town is usually located at (0N,0E). Click the North
button from the first town, and now you'll be at (1N,0E).
Likewise, if you now click the West button, you'll be at (1N,1W).
Monster levels increase with every 5 spaces you move outward
from (0N,0E).<br />
<br />
While you're exploring, you will occasionally run into monsters. As in
pretty much any other RPG game, you and the monster take turns
hitting each other in an attempt to reduce each other's hit points to
zero. Once you run into a monster, the Exploring screen changes
to the Fighting screen.<br /><br />
When a fight begins, you'll see the monster's name, level and hit points,
and the game will ask you for your first command. You then get to
pick whether you want to fight, use a spell, or run away. Note, though,
that sometimes the monster has the chance to hit you
first.<br />
<br />
The Fight button is pretty straightforward: you attack the monster, and
the amount of damage dealt is based on your attack power and
the monster's armor. On top of that, there are two other things that can
happen: an Excellent Hit, which doubles your total attack
damage; and a monster dodge, which results in you doing no damage to the
monster.<br /><br />
The Spell button allows you to pick an available spell and cast it. See
the Spells list at the bottom of this page for more information
about spells.<br /><br />
Finally, there is the Run button, which lets you run away from a fight
if the monster is too powerful. Be warned, though: it is
possible for the monster to block you from running and attack you. So if
your hit points are low, you may fare better by staying
around monsters that you know can't do much damage to you.<br /><br />
Once you've had your turn, the monster also gets his turn. It is also
possible for you to dodge the monster's attack and take no
damage.<br /><br />
The end result of a fight is either you or the monster being knocked
down to zero hit points. If you win, the monster dies and will
give you a certain amount of experience and gold. There is also a chance
that the monster will drop an item, which you can put into
one of the five inventory slots to give you extra points in your
character profile. If you lose and die, half of your gold is taken
away - however, you are given back a few hit points to help you make it
back to town (for example, if you don't have enough gold to
pay for an Inn, and need to kill a couple low-level monsters to get the
money).<br />
<br />
When the fight is over, you can continue exploring until you find
another monster to beat into submission.<br /><br />
[ <a href="#top">Top</a> ]

<br /><br /><hr />

<h3><a name="status"></a>Basics - Playing The Game: Status Panels</h3>
There are two status panels on the game screen: left and right.<br /><br
/>
The right panel inclues your current location and play status (In Town,
Exploring, Fighting etc), compass images for movement, quick spells and your purchased maps for jumping between towns.<br />
<br />
The left panel displays some character statistics, quick items and your inventory.<br />
<br />
The Character section shows the most important character statistics. It
also displays the status bars for your current hit points,
magic points, travel points and the amount of exp required to level up.
These status bars are coloured either green, black, blue, yellow or red
depending on your current amount of each
stat. There is also a link to pop up your list of extended statistics,
which shows more detailed character information.<br />
<br />
The Fast Spells section lists any Heal spells you've learned. You may
use these links any time you are in town or exploring to cast
the heal spell. These may not be used during fights, however - you have
to use the Spells box on the fight screen for that.<p>
[ <a href="#top">Top</a> ]

<br />
<hr />

<p>
</td></tr>
</table>
END;

    display($page,"Help Guide");
}

function faqs() {
	global $userrow;

  	$page = "<table width=\"100%\"><tr><td class=\"title\">Help Guide</td></tr></table>";



$page .= <<<END
<table>
<tr><td>


[ <a href="helpguide.php">Return to Main</a> ]

<br /><br /><hr />



<h3><a name="faqs"></a>Frequently Asked Questions (FAQ's)</h3>
<p>For login support and information please click <a href="help_login.php">here</a>. <br />
<p><strong>Q:</strong> I can't login. Everytime I do, the login page flash's and brings me back to the main page again. 
<p><strong>A:</strong> You need cookies enabled correctly. To find out how to do this, click <a href="help_login.php">here</a>. 
<hr />
<p><strong>Q:</strong> I didn't receive my verification code, or it doesn't work. Where can I get help or a new code?
<p><strong>A:</strong> Use<a href="http://www.dk-rpg.com/dk/contact.php"> this link</a> to contact the administrator, give him your account name and email address and he will either send you a new code or verifiy your account for you. 
<hr />
<p><strong>Q:</strong> Sometimes while exploring or moving about town, I get error messages which say random words on a blank page such as &quot;parse error&quot;. What are these?
<p><strong>A:</strong> This happens usually when the administrator is updating a file or two. Simply ignore it since it isn't anything to worry about. 
<hr />
<p><strong>Q:</strong> I've found a bug, what do I do about it?
<p><strong>A:</strong> Use the report a bug link in your Player Options link within game to contact the administrator. Or you can click <a href="http://www.dk-rpg.com/dk/contact.php">here</a>. Be sure to explain fully what the problem is. 
<hr />
<p><strong>Q:</strong> How do I get items for my item slots?
      
<p><b>A:</b> Everytime you kill a monster they have a chance of dropping
an item. How good the item is depends on the level of the monster. Other items do help drops to appear more frequently. See
the Help guide for more information on what items there are.

<br />
<hr />

<p><b>Q:</b> How do I use the items in my item slots?

<p><b>A:</b> You do not need to use the items, they have a permanent
effect on your stats until replaced by another item.

<br /><hr />

<p><b>Q:</b> What is TP?

<p><b>A:</b> TP stands for Travel Points, these are used after
purchasing town maps which allow you to travel to other towns by using
your travel points.
<hr />
<br />
<br />
<strong>Q:</strong> What are Dragon Scales?
<p><strong>A:</strong> These are known as the Curreny for guilds. They will be used to purchase a guild, and to buy other items within the guild and to build Strongholds to fend off enemy guilds.
<hr />

<p><b>Q:</b> I'm a Sorceress - Why do I not have any Magic Points?

<p><b>A:</b> All characters start with 0 Magic Points, but you will
start to gain them when you get past level 1.

A sorceress does indeed gain a lot more MP than other class's. <br />
<br /><hr />

<p><b>Q:</b> Howcome when I sometimes find an item, and I click to
accept it - I get taken back to 'Exploring'?
<p>Note: This is <strong>very</strong> rare to actually happen.

<p><b>A:</b> - Bad/incorrect item drops: it's the browser's fault.
Sometimes it'll get the page with the drop form stuck in the cache, and
randomly bring it back up for no apparent reason. So it looks like you've
got a drop, but you really don't. When you click to try to get it, the
game thinks you're just trying to cheat it and sends you back to the
regular exploring screen.

<p>To resolve this, simply clear your cache by doing the following:

<p>On Internet Explorer -

<br>- Click 'Tools'
<br>- Then Click 'Internet Options' which is at the bottom of the list
<br>- Then Click 'Delete Files'
<br>- Then tick the box saying 'Delete all offline content'
<br>- Press OK

<p>Finished. There isnt really a way of fixing this, its the browsers
cache fault.

<br /><br /><hr />
<p>

[ <a href="#top">Top</a> ]

<br /><br />
</td></tr>
</table>
END;

    display($page,"Help Guide");
}

function guilds() {
	global $userrow;

  	$page = "<table width=\"100%\"><tr><td class=\"title\">Help Guide</td></tr></table>";



$page .= <<<END
<table>
<tr><td>


<p>[ <a href="helpguide.php">Return to Main</a> ]

  <br />
</p>
<p><ul>
<li /><a href="#guild">Guilds</a>
<li /><a href="#strong">Strongholds  Pet Arena</a>
<li /><a href="#att">Attacking Strongholds</a><br />
</ul>
<hr />
<br />
<h3><a name="guild" id="guild"></a>Guilds <br /></h3>
<p>Guilds are a recent feature in Dragon's Kingdom which let you join a 'group' of people for special benefits. These benefits come from areas called &quot;strongholds&quot; which guilds can build using 2500 Dragon Scales (it may seem like a lot, but new guilds are given 2750 Dragon Scales for free). Dragon Scales and AP are very imporatant for guilds. They both allow you to perform certain actions. If you need more AP, find some monster drops with the element. Keep in mind you may not see all the links mentioned because certain links are restricted to higher ranked members. The guild founder decides the ranks of members. Here are the ranks: </p>
<table width="200" border="1" bordercolor="#000000">
  <tr bordercolor="#000000">
    <td><strong>*Rank Name</strong></td>
    <td><strong>Rank Number </strong></td>
  </tr>
  <tr bordercolor="#000000">
    <td> New Member&nbsp; </td>
    <td>0-10</td>
  </tr>
  <tr bordercolor="#000000">
    <td> Member&nbsp; </td>
    <td>10-30</td>
  </tr>
  <tr bordercolor="#000000">
    <td> Junior Member&nbsp; </td>
    <td>30-60</td>
  </tr>
  <tr bordercolor="#000000">
    <td> Elite Member&nbsp; </td>
    <td>60-80</td>
  </tr>
  <tr bordercolor="#000000">
    <td> Hero Member&nbsp; </td>
    <td>80-90</td>
  </tr>
  <tr bordercolor="#000000">
    <td> Legend Member</td>
    <td>90-100</td>
  </tr>
  <tr bordercolor="#000000">
    <td>Co Leader </td>
    <td>100-150</td>
  </tr>
  <tr bordercolor="#000000">
    <td>Leader</td>
    <td>150-200</td>
  </tr>
  <tr bordercolor="#000000">
    <td>Founder</td>
    <td>200+</td>
  </tr>
</table>
<p><em>*The rank name is altered and changed by your Guild Founder, but its default names are listed in the table above. </em></p>
<p>Guild Courtyard exclusive Areas, which are only viewable by Members: <br />
  <br />
<strong>Guild News:</strong> This is where members can view the news posted by guild Admins. <p>
  <br />
<strong>Experience Pools:</strong> You may send experience to any member of your guild by typing in their ID number and the desired amount of experience. A small amount of experience is collected from each Guild member as they battle Monsters or train Skills which then collects into the Pools below, ready to be shared out to your Guild. <p>
 <br />
<strong>Guild Forum:</strong> This is where all Guild Members can take advantage of a Private Forum. Each guild has their unique Private Forum, and only those Members can view their own Forum. <br />
  <br />
  <strong>View Other Guilds:</strong> You can look at the other guilds here, along with their description and Dragon Scale cost. <br />
  <br />
  <strong>Leave the Guild:</strong> This allows you to leave the guild to join another one, or not be in one at all. </p>
<p>There are a couple of other areas within the Guild Courtyard, which are also in the Stronghold. These are explained in more detail below. </p>
<p>[ <a href="#top">Top</a> ] </p>
<hr />
<h3><a name="strong" id="strong"></a>Strongholds &amp; Pet Arena</h3>
<p><strong>Guild Events: </strong>Here you can see who joined/left your guild and who attacked it. It records everything that happens. <br />
<br />
<strong>Guild Temple:</strong> Coming soon. <br />
<br />
<strong>Pet Arena:</strong> This is where you can raise and duel pets. I'll explain a little bit more about it. <br />
<br />
<strong>Capturing Pets:</strong> To receive your first capture spell, talk to the wizard in the tavern (taverns are found in every town). This is a level 15 capture spell, so that means it can capture monsters of the level 15 or below. You must weaken an enemy to half or lower health before you can even try to capture it. It can take a few tries before you actually capture the monster. You can capture up to 5 pets. Once you capture it, enter a stronghold and go to the Pet Arena to train, raise, and duel your pet. Here are the sections of the Pet Arena. <br />
<br />
<strong>View Pets:</strong> This is where you can view the statistics of your captured pets. <br />
<strong><br />
Feed Pets:</strong> For a price of Dragon Scales, you can feed your pets here for them to regain HP. There are four meals to feed your pet here. The first one is a Treat, which gives your pet a few HP back. The second one is a Snack, which restores a small amount of health. The third one is a Meal, which restores up to 80% of your pet's HP. The final one is a MegaMeal, which fully restores your pet's HP. <br />
<strong><br />
Buy Spells:</strong> You can buy different capture spells here for Dragon Scales. You can get up to a level 120 capture spell. Here are the spells and their price in Dragon Scales: <br />
<br />
Lvl30 Capture: Costs 100 Dragon Scales. <br />
Lvl45 Capture: Costs 300 Dragon Scales. <br />
Lvl60 Capture: Costs 500 Dragon Scales. <br />
Lvl75 Capture: Costs 850 Dragon Scales. <br />
Lvl100 Capture: Costs 1600 Dragon Scales. <br />
Lvl120 Capture: Costs 2100 Dragon Scales. <br />
<br />
<strong>Release Pets:</strong> This is the area where you can release pets back into the wild to make room for new ones. A pet that is released CANNOT be re-obtained, though a pet of the same species can. So think wisely before you release one. <br />
<strong><br />
Practice Duels:</strong> This costs 1 Dragon Scale to do. Once you pay the price, you can duel a practice monster until you feel like stopping. It's to test your pet's statistics, basically. <br />
<br />
<strong>Duel Others:</strong> You can fight other characters pets here. This also costs 1 Dragon Scale. First, you have to choose a pet to fight. Then, you must choose an opponent. If you win the duel, your pet will receive experience and you will gain some Dragon Scales. <br />
<strong><br />
Train Pets:</strong> Your pets start out with 3 Skill Points. You can use each Skill Point to raise your pet's stats. For every level you get, you will receive an additional 3 Skill Points. To gain a level, duel other pets to receive a random amount of experience. You can check how much you need for a level in the View Pets page, along with how much exp your pet currently has. Remember that every time you level up, your experience will be set back to 0. <br />
<br />
<strong>Summon Souls:</strong> Here you can summon cave souls for a price (3 of the most powerful ones, but most rewarding). It costs 1 AP to view this page. <br />
<strong><br />
Stronghold Portals:</strong> In here you can pay 10 AP to move to a stronghold in your guild. This can benefit you by moving you to a good training location without having to walk there, plus you can also enter a stronghold from that point. <br />
<br />
<strong>Back to Town:</strong> This will teleport you back to the town you were last in. It costs 3 AP to view this page.| <br />
<br />
<strong>Repair Stronghold:</strong> Here you can use your guild's banked money to repair your structure, armor, magic, and weaponry. <br />
<br />
<strong>Attack Others:</strong> You can command your troops to attack other strongholds from here. Keep in mind that you lose some of your troops during the attack. It takes 5 AP to attack another stronghold. <br />
<br />
<strong>Fortify Defenses: </strong>You can take money from out of your own pocket and from your Stronghold Storage here to upgrade your guild in 3 categories (Armor, Magic, and Weaponry). Each level costs more than the last to upgrade. Upgrading these stats make your stronghold more powerful and less prone to being walked to and destroyed. <br />
<br />
<strong>Recruit Troops: </strong>Here you can recruit troops to help during your attacks on other strongholds via your own stronghold. Each troop costs 1 Dragon Scale to recruit, and each of the 3 types has a different effect. Vipers destroy magic, Golems damage weaponry, and Gargoyles weaken armor. <br />
<br />
<strong>Stronghold Statistics:</strong> You can view all of the strongholds statistics here, including its levels and gold. <br />
<br />
<strong>Member Listing:</strong> You can view all guild members, their level, their Dragon Scales, and their rank here. If you have the ability to change a members rank, you can simply click on their name to do so. This also reveals their other stats. <br />
<br />
<strong>Buy Unique Items:</strong> Coming soon. <br />
<br />
<strong>High Stake Gambling:</strong> You can play 2 games here similar to the gambling den, but with one difference: Instead of gold, there's Dragon Scales that you can win/risk. The two games are Pick a Shell and Wheel of Fortune. In Pick a Shell you have to choose between 3 shells. One of them contains Dragon Scales. In Wheel of Fortune, you can spin the wheel and receive a random number. If this number is good enough, you can win Dragon Scales. <br />
<br />
<strong>Guild Vault:</strong> This is basically like a bank with the ability to withdraw/deposit a certain amount. It is linked directly to the Town Bank. <br />
<br />
<strong>Resting Area:</strong> For 15 Dragon Scales, you can rest to restore your HP, MP, and TP. It does not restore AP. <br />
<br />
<strong>Restore AP: </strong>For 50 Dragon Scales, your can completely restore your AP here. <br />
<br />
<strong>Stronghold Storage:</strong> You can donate gold and Dragon Scales to your guild/stronghold in this section. <br />
<br />
<strong>Edit Guild (Guild Admin):</strong> You can edit many aspects of your guild here. These aspects include: The cost in Dragon Scales to join, the guild tag (short for your guild name), the guild password, the name of the guild ranks, any guild news, and your guild description. <br />
<strong><br />
Build a Stronghold (Guild Admin):</strong> In this section of a stronghold, you can build another stronghold at the price of 2500 Dragon Scales and 25 AP. When building a stronghold, you can also choose its location. <br />
<strong><br />
Guild Magics (Guild Admin):</strong> Possibly coming soon. </p>
<p>[ <a href="#top">Top</a> ] </p>
<hr />
<p>
<h3><a name="att" id="att"></a>Attacking Strongholds </h3>
<p>Sieging: When you walk to someones stronghold, you can siege it. To find out their coordinates, check under Attack Others in the stronghold. During a siege, you do damage to the stronghold as well as steal Dragon Scales and gold. You also may get caught, and the guards will do minor damage to you while taking you outside the stronghold. This will cost you 1 AP. </p>
<p>Attack Others: You can command your troops to attack other strongholds from here. Keep in mind that you lose some of your troops during the attack. It takes 5 AP to attack another stronghold. You can access this within a Stronghold. </p>
<p><br />
[ <a href="#top">Top</a> ]</p>
</p>
</tr></table>
END;

    display($page,"Help Guide");
}

function skills() {
	global $userrow;

  	$page = "<table width=\"100%\"><tr><td class=\"title\">Help Guide</td></tr></table>";



$page .= <<<END
<table>
<tr><td>


[ <a href="helpguide.php">Return to Main</a> ]

<br /><br /><hr />



<h3>Skill Shrine: Skills</h3>
<p><b>Skill Shrine</b><strong> - Combat</strong> - When clicked it brings you to a place where you can train the four skills that every class starts with. At the moment they are the only skills and one day each class will have their own unique skills. Skills cost money to level up instead of training them by clicking over an over. The skills are quite expensive but are worth it in the long run. Skills get <strong>extremely</strong> better after level 11. Until then you'll have to wait and try to be patient. Here is a list of the current basic skills every class gets.<br />
  <br />
  <b>Wisdom:</b> This gives you bonus experience after a fight.<br />
  <br />
  <b>Stone Skin:</b> This absorbs a percentage of damage you receive from a monster attacks based on the level.<br />
  <br />
  <b>Monks Mind:</b> This gives you bonus damage during a fight.<br />
  <br />
<b>Fortune:</b> This gives you bonus gold after a fight.</p>
<p>Note: Remember that after level 11, these skills greatly improve considerably and will be well worth the money you pay. For example, if you have level 11 Fortune, you will get around 600 more gold PER monster kill. You cannot go above level 15 in any skill. </p>
<p><strong>Non - Combat Skills </strong></p>
<p><strong>Mining:</strong> Mining Ores is a great way of gaining Gold. You must complete the Lost Fortune skill to be able to mine due to you not having a pickaxe. Once you have mined some Ores, simple visit the Blacksmith at the Town Square, to sell them.</p>
<p><strong>Smelting:</strong> Smelting is linked to Mining. Once you have some mined Ores, you can then turn them into Bars. Bars can be sold in the same place as the Ores, and they will be sold for a lot more gold. Eventually, you can use the Bars to create your very own items.</p>
<p><strong>Endurance:</strong> This skill is an obstacle course. Everytime you level, you will gain 1 to your maximum AP, and 2% to your maximum Fatigue (FP). Be warned that you do get hurt if you fail to do the obstacles. If you go down to 0 HP, you will die and lose some gold and dragon scales. </p>


<br /><br /><hr />
<p>

[ <a href="#top">Top</a> ]

<br /><br />
</td></tr>
</table>
END;

    display($page,"Help Guide");
}

function magicfind() {
	global $userrow;

  	$page = "<table width=\"100%\"><tr><td class=\"title\">Help Guide</td></tr></table>";



$page .= <<<END
<table>
<tr><td>


[ <a href="helpguide.php">Return to Main</a> ]

<br /><br /><hr />



<h3>Magic Find: Explained</h3>
<p>You can gain more Magic Find from Jewellery, such as Rings and Amulets. To keep it brief, Magic Find will do the following things to help you:<br />
  <br />
- Give bonus gold from treasure chests, souls and corpses,  <br />
- Give bonus dragon scales from treasure chests, souls and corpses,  <br />
- Give bonus experience from souls,  <br />
- Give you a slightly more chance of finding a monster drop and winning while gambling,  <br />
- And many other things around the game.<br />

<br />
<br /><hr />
<p>

[ <a href="#top">Top</a> ]

<br /><br />
</td></tr>
</table>
END;

    display($page,"Magic Find");
}

function rules() {
	global $userrow;

  	$page = "<table width=\"100%\"><tr><td class=\"title\">Help Guide</td></tr></table>";



$page .= <<<END
<table>
<tr><td>



[ <a href="helpguide.php">Return to Main</a> ]

<br /><br /><hr />

                <p class="text1">You should also read our <a href="helpguide.php?do=tos">Terms of Service</a>. </p>
                <p class="text1">Please read these rules regularly to remember them carefully. If for whatever reason they are changed, it will be shown in the news<em>. </em></p>
                <p class="news"><em>If you break any of these rules, there will be action taken and if neccessary, this website will and may take legal action against you. </em>
                <h3>Game Rules:</p></h3>
                <p class="text1">1) Absolutely no cheating is allowed! If you are caught cheating then you account will automatically be deleted with no questions asked along with a possible permanent IP ban meaning you won't be able to access this site and game again. This includes anything that gives you an unfair advantage, to others in any way what so ever. </p>
                <p class="text1">2) If there are any bugs, or glitches which allow you to cheat in anyway, they must be reported straight away and not be abused for your own personal gain.</p>
                <p class="text1">3) You should respect the administrators and not give them any hassle otherwise you will be facing a ban.</p>
                <p class="text1">4) You should not try to get to areas in the game which the administrator has switched off by manually typing in the link, this is called cheating!</p>
                <p class="text1">5) You are not allowed multiple accounts of the same class. If I match up your IP address's, you will find yourself banned with no questions asked. However, you CAN have an account for each character class. This means, you can have only 7 accounts, 1 Paladin, 1 Sorceress, 1 Barbarian, 1 Necromancer, 1 Druid, 1 Assassin and 1 Ranger. </p>
                <p class="text1">6) No typing in the link and definitely no Power Clicking. Power clicking is when you click multiple times in the space of a short time on the compass. This is not allowed! </p>
                <p class="text1">7) 


Account trading or selling for whatever currency or item is not allowed. If you are caught trying to buy or sell another account, all accounts in the process of being sold or traded will be banned, no questions asked. This includes: selling for real money, trading for items, gold, or any other way of giving somebody else your account.</p>
                <p class="text1">8) You are ONLY allowed 1 window open at a time while playing. However, you CAN have more than 1 window open IF its nothing relating to the main game. eg: You can have one window be the game in which you play in, and the second can be the forum or help guide. </p>
<p class="text1">9) The administrator is always right (even if you think he isn't). If you don't like it, leave. </p>
<hr />
<h3>Forum Rules:</h3>
 1) There will be no excessive use of profanity.
<p class="text1">
2) There will be no racial, ethnic, gender based insults or any other personal discriminations. </p>
<p class="text1">
3) Pornography, Warez, or any other illegal transactions may not be posted or included in members profiles. </p>
<p class="text1">
4) Images may be posted as long as they are not explicive or offensive. </p>
<p class="text1">
5) Advertising, commercial-related or competing products are not allowed. No advertising of any such is allowed. However, some links are allowed. </p>
<p class="text1">
6) Do not attack any staff or support members, they volunteer their time to help users, and abuse towards them will not be accepted. </p>
<p class="text1">
7) Try not to bring back old topics which no one has replied to for a few weeks. </p>
<p class="text1">8) Most importantly, do NOT spam with useless messages otherwise I will ban you on the spot. </p>
<p class="text1">9) Do not ask Quest related questions in the forum, if you do this too often, you will be faced with a ban or warning. </p>
<hr />
<h3>Player Chat:</h3>
  <p class="text1">1) 


 There will be no excessive use of profanity.</p>
                <p class="text1">2) 


 There will be no racial, ethnic, gender based insults or any other personal discriminations.</p>
                <p class="text1">3) 


 Advertising, commercial-related or competing products are not allowed. Nor is posting your email address on there. </p>
                <p class="text1">4) 


 Do not attack any staff or support members, they volunteer their time to help users, and abuse towards them will not be accepted.</p>
                <p class="text1">5) 
 There should not be any flaming of any kind. Respect each other.                
                <p class="text1">6) If you say something negative towards the actual game of DK, in a serious way then you will be faced with a warning or ban. If you really don't like it, get lost. Simple as.                
                <p class="text1">8) Do not ask Quest related questions in chat, if you do this too often, you will be faced with a ban or warning.                
                <p class="text1">9) Administrators have the final say over everything.
                <p align="center" class="text1"><strong>Please use common sense when using the player chat and game.</strong></p>                  
                <p class="news">If any of these rules are broken then you will most probably have your IP address permanently banned. On some occassions, you will just be banned from the player chat, and not the game depending on certain circumstances.</p>
                 </p></p>
<br /><hr /><br />
<br />
[ <a href="#top">Top</a> ]

<br /><br />
</td></tr>
</table>
END;

    display($page,"Help Guide");
}

function tos() {
	global $userrow;

  	$page = "<table width=\"100%\"><tr><td class=\"title\">Help Guide</td></tr></table>";



$page .= <<<END
<table>
<tr><td>



[ <a href="helpguide.php">Return to Main</a> ]

<br /><br /><hr />
These Terms of Service, known here after as the &quot;ToS&quot;, is subject to change at anytime. <br />
<br />
Major changes to the ToS will be announced as an Update presented at logon, although it is the responsibility of the player to be aware of and in compliance with the ToS at all times. Failure to do so may result in the immediate deletion of your account. <br />
<br />
This ToS applies to all players of this game. <br />
<br />
<strong>Connectivity </strong><br />
This RPG known as &quot;Dragon's Kingdom: An Online Browser Based RPG!”, does not guarantee connectivity of our site at all times.
<p><strong>Game </strong><br />
  This game is not intended for persons under thirteen years of age, if you are younger than you will require parents permission. Some content may be inappropriate for young children. By signing up for an account you are stating you are twelve years old or older. Players found to be underage will have their accounts terminated by the site staff. <br />
  <br />
  You are allotted only one account per class if you comply to the following. If you wish to play using multiple accounts, that is allowed for in game purposes other then abusing activities that are on a per account basis. Individuals who are found to have multiple accounts and are using them to purposely gain an advantage over other players will have all involved accounts deleted immediately and without warning. Do not use your other accounts to attempt to gain an unfair advantage over other users through special offers, daily activities, gaining gold, dragon scales, etc. unless otherwise authorized by the admin. <br />
  <br />
  Account sharing (that is the continued use of an account by multiple persons), for any purpose, is strictly prohibited, unless otherwise authorized by the admin. <br />
  <br />
  <strong>Censorship/Decency </strong><br />
"Dragon's Kingdom" is for the most part censored. However verbally abusing players and staff is considered poor etiquette on the players part. You may be banned from chat rooms, or muted from posting on message boards if you verbally abuse staff or other players. Also finding ways around censors could get you a tempt ban or a warning, there after being warned a full ban could be processed. <br />
  <br />
  <strong>Clan Censorship/Decency </strong><br />
Clans, for the most part, are privately administered entities. Game decency laws do not apply to clan sections and settings that are not viewable by non-members. What is and isn't acceptable in this situation is up to the clan administration, provided it does not infringe on other game law.<br><br>
  <strong>Game Law: </strong><br />
  Users agree to follow all game laws, and understand that violation will result in the given punishment. <br />
  <br />
  <strong>Intellectual Rights </strong><br />
  Any individual who attempts to reverse engineer, hack, gain unauthorized entry into or undermine the system integrity of “Dragon's Kingdom” (including attempting to access any internal part of the game from an offsite location) will be dealt with to the fullest extent of the law. <br />
  <br />
  <strong>Terminating Your Account </strong><br />
  Individuals who no longer wish to play "Dragon's Kingdom" may terminate their accounts by not logging into their accounts. Accounts may be deleted at the discretion of the admininistrator or/and owner. <br />
  <br />
  <strong>User Generated Content: </strong><br />
  To the extent that portions of this Site provide users an opportunity to post and/or exchange information, ideas, and opinions (&quot;Postings&quot;) in chatrooms and message boards, and in descriptions of clans, please be advised that Postings do not necessarily reflect the views of DK, (Dragon's Kingdom). Although we periodically monitor exchanged and posted information, in no event does “Dragon's Kingdom” assume or have any responsibility or liability for any Postings or for any claims, damages or losses resulting from their use and/or appearance on or in conjunction with this Site or elsewhere. Users remain solely responsible for the content of their messages and postings. However, “Dragon's Kingdom” reserves the right to edit, delete, or refuse to post any Posting that violates these Terms and Conditions, as well as revoke the privileges of any user who does not comply with these Terms and Conditions. <br />
  <br />
  <strong>Operational Interference </strong><br />
  Users agree not to, through use of software or other means, interfere with site operation and page content. Any automated software written to interact with the game can connect no more than once in a one minute period. Users agree to follow Administrator requests and instructions. <br />
  <br />
  <strong>Force Majeure </strong><br />
  DK shall be not responsible for any failure to perform its obligations under this Agreement (Connectivity, loss of account information) if such failure is caused by events or conditions beyond DK's reasonable control. <br />
  <br />
  <strong>Applicable Law and Court </strong><br />
  Any dispute arising in connection with the conclusion, the validity, the interpretation, the implementation or performance of these terms of service shall be of the exclusive jurisdiction of the Courts of London, England. <br />
  <br />
  <strong>Addendum </strong><br />
Other sections of the game may have it's own rules (such as the chat room.) These rules are an addition to the ToS, not a replacement. Violation of these rules is considered to be a violation of the ToS and will be dealt with accordingly. Please familiarize yourself with all game rules before playing. Failure to be familiar with the game rules is not an excuse for breaking them and will not be accepted as a defense of your account. </p>
<p><strong>Copyright</strong>
<br>All images and content on this website are their true owners and should not be taken without permission. Dragon's Kingdom is &copy; 2004 - Created by Adam Dear <br />
  <br />
  THIS SITE AND ALL MATERIALS CONTAINED ON IT ARE DISTRIBUTED AND TRANSMITTED ON AN &quot;AS IS&quot; AND &quot;AS AVAILABLE&quot; BASIS, WITHOUT WARRANTIES OF ANY KIND, EITHER EXPRESS OR IMPLIED. TO THE FULLEST EXTENT PERMISSIBLE UNDER APPLICABLE LAW, DRAGON'S KINGDOM DISCLAIMS ALL WARRANTIES, EXPRESS OR IMPLIED, INCLUDING, WITHOUT LIMITATION, WARRANTIES OF MERCHANTABILITY OR FITNESS FOR A PARTICULAR PURPOSE. DRAGON'S KINGDOM DOES NOT WARRANT THAT THE FUNCTIONS CONTAINED IN THE SITE OR MATERIALS WILL BE UNINTERRUPTED OR ERROR-FREE, THAT DEFECTS WILL BE CORRECTED, OR THAT THIS SITE OR THE SERVERS THAT MAKES IT AVAILABLE ARE FREE OF VIRUSES OR OTHER HARMFUL COMPONENTS. DRAGON'S KINGDOM DOES NOT WARRANT OR MAKE ANY REPRESENTATIONS REGARDING THE USE OR THE RESULTS OF THE USE OF THE MATERIALS IN THIS SITE WITH REGARD TO THEIR CORRECTNESS, ACCURACY, RELIABILITY, OR OTHERWISE. THE ENTIRE RISK AS TO THE QUALITY, ACCURACY, ADEQUACY, COMPLETENESS, CORRECTNESS AND VALIDITY OF ANY MATERIAL RESTS WITH YOU. YOU (I.E., NOT DRAGON'S KINGDOM) ASSUME THE COMPLETE COST OF ALL NECESSARY SERVICING, REPAIR, OR CORRECTION. APPLICABLE LAW MAY NOT ALLOW THE EXCLUSION OF IMPLIED WARRANTIES, SO THE ABOVE EXCLUSION MAY NOT APPLY TO YOU. <br />
  <br />
  TO THE FULLEST EXTENT PERMISSIBLE PURSUANT TO APPLICABLE LAW, DRAGON'S KINGDOM, ITS AFFILIATES, AND THEIR RESPECTIVE OFFICERS, DIRECTORS, EMPLOYEES, AGENTS, LICENSORS, REPRESENTATIVES, AND THIRD PARTY PROVIDERS TO THE SITE WILL NOT BE LIABLE FOR DAMAGES OF ANY KIND INCLUDING, WITHOUT LIMITATION, COMPENSATORY, CONSEQUENTIAL, INCIDENTAL, INDIRECT, SPECIAL OR SIMILAR DAMAGES, THAT MAY RESULT FROM THE USE OF, OR THE INABILITY TO USE, THE MATERIALS CONTAINED ON THIS SITE, WHETHER THE MATERIAL IS PROVIDED OR OTHERWISE SUPPLIED BY DRAGON'S KINGDOM OR ANY THIRD PARTY. <br />
  <br />
  NOT WITH STANDING THE FOREGOING, IN NO EVENT SHALL DRAGON'S KINGDOM HAVE ANY LIABILITY TO YOU FOR ANY CLAIMS, DAMAGES, LOSSES, AND CAUSES OF ACTION (WHETHER IN CONTRACT, TORT OR OTHERWISE) EXCEEDING THE AMOUNT PAID BY YOU, IF ANY, FOR ACCESSING THIS SITE. <br />
  <br />
USE OF THE SITE CONSTITUTES AGREEMENT WITH THE CURRENT TERMS AND AND CONDITIONS. </p>


<br /><hr /><br />
<br />
[ <a href="#top">Top</a> ]

<br /><br />
</td></tr>
</table>
END;

    display($page,"Help Guide");
}

function tutorial() {
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



  [ <a href="helpguide.php?do=first">Continue Tutorial</a> ]
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

<br /><br />  [ <a href="helpguide.php?do=second">Continue Tutorial</a> ]
  

  
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
off on. You gain a Desert Tent and a new player potion to begin your journey.</i><p>
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


    display($page,"Tutorial");

}
?>