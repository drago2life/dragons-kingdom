<?php // install.php :: creates/populates database tables on a new installation.

include('config.php');
include('lib.php');
$link = opendb();
$start = getmicrotime();

if (isset($_GET["page"])) {
    $page = $_GET["page"];
    if ($page == 2) { second(); }
    elseif ($page == 3) { third(); }
    elseif ($page == 4) { fourth(); }
    elseif ($page == 5) { five(); }
	elseif ($page == 6) { six(); }
    else { first(); }
} else { first(); }

// Thanks to Predrag Supurovic from php.net for this function!
function dobatch ($p_query) {
  $query_split = preg_split ("/[;]+/", $p_query);
  foreach ($query_split as $command_line) {
   $command_line = trim($command_line);
   if ($command_line != '') {
     $query_result = mysql_query($command_line);
     if ($query_result == 0) {
       break;
     };
   };
  };
  return $query_result;
}

function first() { // First page - show warnings and gather info.
    
$page = <<<END
<html>
<head>
<title>DK Installation</title>
</head>
<body>
<b>DK Installation: Page One</b><br /><br />
<b>NOTE:</b> Please ensure you have filled in the correct values in config.php before continuing. Installation will fail if these values are not correct. Also, the MySQL database needs to already exist. This installer script will take care of setting up its structure and content, but the database itself must already exist on your MySQL server before the installer will work.<br /><br />
Installation for DK is a simple two-step process: set up the database tables, then create the admin user. After that, you're done.<br /><br />

Click the button below to continue. Please be aware that it may take several minutes before the next few pages fully loads. Do-not close the window until it is complete.<br /><br />
<form action="install.php?page=2" method="post">
<input type="submit" name="complete" value="Continue Setup" /><p>If for whatever reason the installation fails or doesn't fully load, please restart the installation or manually upload the database. This is due to the large amount of data being imported.<br />
</form>
</body>
</html>
END;
echo $page;
die();
  
}

function second() { // Second page - set up the database tables.
    
    global $dbsettings;
    echo "<html><head><title>DK Installation</title></head><body><b>DK Installation: Page Two, Part A</b><br /><br />";
    $prefix = $dbsettings["prefix"];
	$arena = $prefix . "_arena";
	$chat = $prefix . "_chat";
    $comments = $prefix . "_comments";
    $control = $prefix . "_control";
    $crafting = $prefix . "_crafting";	
    $drops = $prefix . "_drops";
    $duel = $prefix . "_duel";
    $endurance = $prefix . "_endurance";
	$forging = $prefix . "_forging";
	$gamemail = $prefix . "_gamemail";
	$general = $prefix . "_general";
	$gforum = $prefix . "_gforum";
    $guilds = $prefix . "_guilds";
    $homes = $prefix . "_homes";
	$inventitems = $prefix . "_inventitems";
    $items = $prefix . "_items";
    $itemstorage = $prefix . "_itemstorage";
    $jewellery = $prefix . "_jewellery";
    $levels = $prefix . "_levels";
    $marketforum = $prefix . "_marketforum";
	$mining = $prefix . "_mining";
    $monsters = $prefix . "_monsters";
    $news = $prefix . "_news";
	$playermarket = $prefix . "_playermarket";
	$poll = $prefix . "_poll";
	$smelting = $prefix . "_smelting";
	$souls = $prefix . "_souls";
    $spells = $prefix . "_spells";
	$staff = $prefix . "_staff";
	$strongholds = $prefix . "_strongholds";
	$suggestions = $prefix . "_suggestions";
	$support = $prefix . "_support";
    $towns = $prefix . "_towns";
    $users = $prefix . "_users";
    
    if (isset($_POST["complete"])) { $full = true; } else { $full = false; }
    

$query = <<<END
CREATE TABLE `$arena` (
  `id` smallint(5) NOT NULL auto_increment,
  `name` varchar(30) NOT NULL default '',
  `type` varchar(30) NOT NULL default '',
  `species` smallint(5) NOT NULL default '1',
  `trainer` varchar(30) NOT NULL default '',
  `maxhp` smallint(5) unsigned NOT NULL default '10',
  `maxmp` smallint(6) unsigned NOT NULL default '5',
  `currenthp` smallint(5) unsigned NOT NULL default '5',
  `currentmp` smallint(5) unsigned NOT NULL default '5',
  `maxdam` smallint(5) NOT NULL default '10',
  `dexterity` smallint(5) NOT NULL default '10',
  `armor` smallint(5) NOT NULL default '0',
  `magicarmor` tinyint(5) NOT NULL default '0',
  `level` smallint(5) NOT NULL default '1',
  `experience` smallint(5) unsigned NOT NULL default '0',
  `gold` smallint(5) unsigned NOT NULL default '0',
  `immune` varchar(75) NOT NULL default '0',
  `wins` smallint(5) unsigned NOT NULL default '0',
  `losses` smallint(5) unsigned NOT NULL default '0',
  `lastwin` varchar(90) NOT NULL default '',
  `lastloss` varchar(90) NOT NULL default '',
  `skillpoints` tinyint(4) NOT NULL default '3',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Arena table created.<br />"; } else { echo "Error creating Arena table."; }
unset($query);

    
$query = <<<END
CREATE TABLE `$chat` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `posttime` time NOT NULL default '00:00:00',
  `author` varchar(30) NOT NULL default '',
  `touser` varchar(30) NOT NULL default '',
  `babble` varchar(150) NOT NULL default '',
  PRIMARY KEY  (`id`)

) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Chat table created.<br />"; } else { echo "Error creating Chat table."; }
unset($query);

	
	$query = <<<END
CREATE TABLE `$comments` (
  `id` bigint(255) NOT NULL auto_increment,
  `topic` bigint(255) NOT NULL default '0',
  `time` datetime NOT NULL default '0000-00-00 00:00:00',
  `poster` bigint(255) NOT NULL default '0',
  `post` text NOT NULL,
  UNIQUE KEY `id` (`id`)

) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Comments table created.<br />"; } else { echo "Error creating Comments table."; }
unset($query);

$query = <<<END
	CREATE TABLE `$control` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `gamename` varchar(50) NOT NULL default '',
  `gamesize` smallint(5) unsigned NOT NULL default '0',
  `dunsize` smallint(5) unsigned NOT NULL default '0',
  `gameopen` tinyint(3) unsigned NOT NULL default '0',
  `gameurl` varchar(200) NOT NULL default '',
  `adminemail` varchar(100) NOT NULL default '',
  `updatetime` varchar(30) NOT NULL default '-',
  `info` text NOT NULL,
  `class1name` varchar(50) NOT NULL default '',
  `class2name` varchar(50) NOT NULL default '',
  `class3name` varchar(50) NOT NULL default '',
  `class4name` varchar(50) NOT NULL default '',
  `class5name` varchar(50) NOT NULL default '',
  `class6name` varchar(50) NOT NULL default '',
  `class7name` varchar(50) NOT NULL default '',
  `diff1name` varchar(50) NOT NULL default '',
  `diff1mod` float unsigned NOT NULL default '0',
  `compression` tinyint(3) unsigned NOT NULL default '0',
  `verifyemail` tinyint(3) unsigned NOT NULL default '0',
  `shownews` tinyint(3) unsigned NOT NULL default '0',
  `displaychat` tinyint(3) unsigned NOT NULL default '0',
  `forumopen` tinyint(3) unsigned NOT NULL default '0',
  `mostonline` smallint(5) unsigned NOT NULL default '0',

  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Control table created.<br />"; } else { echo "Error creating Control table."; }
unset($query);

$query = <<<END
INSERT INTO `$control` VALUES (1, 'Dragon''s Kingdom', 600, 200, 1, 'http://www.dkscriptcom/demo/index.php', 'support@dkscript.com', 'Not too long...', 'Game update + Fixes. Sorry for the inconvenience.', 'Sorceress', 'Barbarian', 'Paladin', 'Ranger', 'Necromancer', 'Druid', 'Assassin', 'Rank 3', 1, 1, 1, 1, 1, 1, 50);
END;
if (dobatch($query) == 1) { echo "Control table populated.<br />"; } else { echo "Error populating Control table."; }
unset($query);

$query = <<<END
CREATE TABLE `$crafting` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(30) NOT NULL default '',
  `level` int(3) NOT NULL default '1',
  `gem1` int(3) unsigned NOT NULL default '0',
  `gem2` int(3) unsigned NOT NULL default '0',
  `gem3` int(3) unsigned NOT NULL default '0',
  `gem4` int(3) unsigned NOT NULL default '0',
  `gem5` int(3) unsigned NOT NULL default '0',
  `string` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Crafting table created.<br />"; } else { echo "Error creating Crafting table."; }
unset($query);

$query = <<<END
INSERT INTO `$crafting` VALUES (1, 'Sapphire Ring', 1, 1, 0, 0, 0, 0, 0);
INSERT INTO `$crafting` VALUES (2, 'Sapphire Amulet', 15, 1, 0, 0, 0, 0, 1);
INSERT INTO `$crafting` VALUES (3, 'Emerald Ring', 30, 0, 1, 0, 0, 0, 0);
INSERT INTO `$crafting` VALUES (4, 'Emerald Amulet', 45, 0, 1, 0, 0, 0, 1);
INSERT INTO `$crafting` VALUES (5, 'Ruby Ring', 70, 0, 0, 2, 0, 0, 0);
INSERT INTO `$crafting` VALUES (6, 'Ruby Amulet', 90, 0, 0, 2, 0, 0, 1);
INSERT INTO `$crafting` VALUES (7, 'Diamond Ring', 115, 0, 0, 0, 3, 0, 0);
INSERT INTO `$crafting` VALUES (8, 'Diamond Amulet', 130, 0, 0, 0, 3, 0, 1);
INSERT INTO `$crafting` VALUES (9, 'Black Dragons Ring', 160, 0, 0, 0, 0, 5, 0);
INSERT INTO `$crafting` VALUES (10, 'Black Dragons Amulet', 175, 0, 0, 0, 0, 5, 1)

END;


if (dobatch($query) == 1) { echo "Crafting table populated.<br />"; } else { echo "Error populating Crafting table."; }
unset($query);

$query = <<<END

CREATE TABLE `$drops` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `name` varchar(30) NOT NULL default '',
  `mlevel` smallint(5) unsigned NOT NULL default '0',
  `type` smallint(5) unsigned NOT NULL default '0',
  `attribute1` varchar(30) NOT NULL default '',
  `attribute2` varchar(30) NOT NULL default '',
  `requirement` smallint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Drops table created.<br />"; } else { echo "Error creating Drops table."; }
unset($query);

$query = <<<END
INSERT INTO `$drops` VALUES (1, 'Blessed Life', 1, 1, 'maxhp,10', 'X', 0);
INSERT INTO `$drops` VALUES (2, 'Sacred Life', 10, 1, 'maxhp,25', 'X', 5);
INSERT INTO `$drops` VALUES (3, 'Angelic Life', 25, 1, 'maxhp,50', 'X', 15);
INSERT INTO `$drops` VALUES (4, 'Magical Rock', 3, 1, 'maxmp,10', 'X', 0);
INSERT INTO `$drops` VALUES (5, 'Magical Eye', 12, 1, 'maxmp,25', 'X', 5);
INSERT INTO `$drops` VALUES (6, 'Magical Enchantment', 25, 1, 'maxmp,50', 'X', 15);
INSERT INTO `$drops` VALUES (7, 'Devil''s Scale', 12, 1, 'defensepower,25', 'X', 5);
INSERT INTO `$drops` VALUES (8, 'Devil''s Plate', 30, 1, 'defensepower,50', 'X', 25);
INSERT INTO `$drops` VALUES (9, 'Devil''s Claw', 12, 1, 'attackpower,25', 'X', 5);
INSERT INTO `$drops` VALUES (10, 'Devil''s Tooth', 30, 1, 'attackpower,50', 'X', 25);
INSERT INTO `$drops` VALUES (11, 'Devil''s Tear', 35, 1, 'strength,50', 'X', 30);
INSERT INTO `$drops` VALUES (12, 'Devil''s Wing', 35, 1, 'dexterity,50', 'X', 30);
INSERT INTO `$drops` VALUES (13, 'Demon''s Sin', 35, 1, 'maxhp,-50', 'strength,50', 30);
INSERT INTO `$drops` VALUES (14, 'Demon''s Fall', 35, 1, 'maxmp,-50', 'strength,50', 30);
INSERT INTO `$drops` VALUES (15, 'Demon''s Lie', 45, 1, 'maxhp,-100', 'strength,100', 40);
INSERT INTO `$drops` VALUES (16, 'Demon''s Hate', 45, 1, 'maxmp,-100', 'strength,100', 40);
INSERT INTO `$drops` VALUES (17, 'Angel''s Joy', 28, 1, 'maxhp,25', 'strength,25', 20);
INSERT INTO `$drops` VALUES (18, 'Angel''s Rise', 30, 1, 'maxhp,50', 'strength,50', 25);
INSERT INTO `$drops` VALUES (19, 'Angel''s Truth', 35, 1, 'maxhp,75', 'strength,75', 30);
INSERT INTO `$drops` VALUES (20, 'Angel''s Love', 40, 1, 'maxhp,100', 'strength,100', 35);
INSERT INTO `$drops` VALUES (21, 'Seraph''s Joy', 31, 1, 'maxmp,25', 'dexterity,25', 25);
INSERT INTO `$drops` VALUES (22, 'Seraph''s Rise', 35, 1, 'maxmp,50', 'dexterity,50', 30);
INSERT INTO `$drops` VALUES (23, 'Seraph''s Truth', 36, 1, 'maxmp,75', 'dexterity,75', 30);
INSERT INTO `$drops` VALUES (24, 'Seraph''s Love', 41, 1, 'maxmp,100', 'dexterity,100', 35);
INSERT INTO `$drops` VALUES (25, 'Ruby', 50, 1, 'maxhp,150', 'X', 45);
INSERT INTO `$drops` VALUES (26, 'Pearl', 50, 1, 'maxmp,150', 'X', 45);
INSERT INTO `$drops` VALUES (27, 'Emerald', 50, 1, 'strength,150', 'X', 45);
INSERT INTO `$drops` VALUES (28, 'Topaz', 50, 1, 'dexterity,150', 'X', 45);
INSERT INTO `$drops` VALUES (29, 'Obsidian', 50, 1, 'attackpower,150', 'X', 45);
INSERT INTO `$drops` VALUES (30, 'Diamond', 50, 1, 'defensepower,150', 'X', 45);
INSERT INTO `$drops` VALUES (31, 'Memory Drop', 42, 1, 'expbonus,10', 'X', 35);
INSERT INTO `$drops` VALUES (32, 'Fortune Drop', 46, 1, 'goldbonus,10', 'X', 40);
INSERT INTO `$drops` VALUES (33, 'Bronze Charm', 8, 1, 'maxtp,3', 'maxhp,5', 10);
INSERT INTO `$drops` VALUES (34, 'Silver Charm', 21, 1, 'maxtp,12', 'maxhp,15', 21);
INSERT INTO `$drops` VALUES (35, 'Gold Charm', 38, 1, 'maxtp,25', 'maxhp,25', 37);
INSERT INTO `$drops` VALUES (36, 'Platinum Charm', 51, 1, 'maxtp,30', 'maxhp,25', 52);
INSERT INTO `$drops` VALUES (37, 'Sorceress Charm', 45, 1, 'maxmp,125', 'maxhp,60', 40);
INSERT INTO `$drops` VALUES (38, 'Barbarian Charm', 45, 1, 'strength,125', 'maxhp,60', 40);
INSERT INTO `$drops` VALUES (39, 'Paladin Charm', 45, 1, 'attackpower,125', 'maxhp,60', 40);
INSERT INTO `$drops` VALUES (40, 'Ranger Charm', 45, 1, 'dexterity,125', 'maxhp,60', 40);
INSERT INTO `$drops` VALUES (41, 'Jewel of Fortune', 62, 1, 'goldbonus,15', 'maxhp,15', 55);
INSERT INTO `$drops` VALUES (42, 'Jewel of Hope', 65, 1, 'maxhp,165', 'maxmp,15', 58);
INSERT INTO `$drops` VALUES (43, 'Jewel of Strength', 67, 1, 'strength,165', 'maxhp,15', 60);
INSERT INTO `$drops` VALUES (44, 'Jewel of Speed', 69, 1, 'dexterity,165', 'maxhp,15', 60);
INSERT INTO `$drops` VALUES (45, 'Jewel of Experience', 72, 1, 'expbonus,15', 'maxhp,15', 65);
INSERT INTO `$drops` VALUES (46, 'Greater Sorceress Charm', 75, 1, 'maxmp,185', 'maxhp,90', 68);
INSERT INTO `$drops` VALUES (47, 'Greater Barbarian Charm', 78, 1, 'strength,185', 'maxhp,90', 70);
INSERT INTO `$drops` VALUES (48, 'Greater Paladin Charm', 81, 1, 'attackpower,155', 'maxhp,90', 75);
INSERT INTO `$drops` VALUES (49, 'Greater Ranger Charm', 83, 1, 'dexterity,185', 'maxhp,90', 75);
INSERT INTO `$drops` VALUES (50, 'Shiney Gift', 91, 1, 'defensepower,165', 'maxhp,35', 85);
INSERT INTO `$drops` VALUES (51, 'Heavens Gift', 94, 1, 'maxtp,35', 'maxhp,45', 87);
INSERT INTO `$drops` VALUES (52, 'Gods Gift', 97, 1, 'goldbonus,16', 'maxhp,30', 90);
INSERT INTO `$drops` VALUES (53, 'Rare Crystal Fragment (small)', 103, 1, 'expbonus,16', 'maxhp,85', 95);
INSERT INTO `$drops` VALUES (54, 'Rare Crystal Fragment (medium)', 109, 1, 'expbonus,17', 'maxhp,95', 100);
INSERT INTO `$drops` VALUES (55, 'Rare Crystal Fragment (large)', 116, 1, 'expbonus,18', 'maxhp,105', 105);
INSERT INTO `$drops` VALUES (56, 'King Black Dragon''s Tooth', 120, 1, 'dexterity,250', 'maxhp,100', 105);
INSERT INTO `$drops` VALUES (57, 'King Black Dragon''s Skin', 120, 1, 'maxmp,250', 'maxhp,100', 105);
INSERT INTO `$drops` VALUES (58, 'King Black Dragon''s Scale', 120, 1, 'goldbonus,25', 'maxhp,100', 105);
INSERT INTO `$drops` VALUES (59, 'King Black Dragon''s Tongue', 120, 1, 'maxhp,250', 'maxmp,100', 105);
INSERT INTO `$drops` VALUES (60, 'King Black Dragon''s Heart', 120, 1, 'strength,250', 'maxhp,100', 105);
INSERT INTO `$drops` VALUES (61, 'Necromancer Charm', 45, 1, 'maxmp,120', 'maxhp,65', 40);
INSERT INTO `$drops` VALUES (62, 'Greater Necromancer Charm', 75, 1, 'maxmp,180', 'maxhp,95', 68);
INSERT INTO `$drops` VALUES (63, 'Druid Charm', 45, 1, 'strength,115', 'maxhp,70', 40);
INSERT INTO `$drops` VALUES (64, 'Greater Druid Charm', 75, 1, 'strength,175', 'maxhp,100', 68);
INSERT INTO `$drops` VALUES (65, 'Assassin Charm', 45, 1, 'dexterity,145', 'maxhp,50', 40);
INSERT INTO `$drops` VALUES (66, 'Greater Assassin Charm', 75, 1, 'dexterity,195', 'maxhp,80', 68);
INSERT INTO `$drops` VALUES (67, 'Spirits Tail', 14, 1, 'maxap,10', 'maxhp,15', 5);
INSERT INTO `$drops` VALUES (68, 'Spirits Rage', 29, 1, 'maxap,25', 'maxhp,20', 20);
INSERT INTO `$drops` VALUES (69, 'Spirits Body', 51, 1, 'maxap,40', 'maxhp,25', 40);
INSERT INTO `$drops` VALUES (70, 'Spirits Terror', 83, 1, 'maxap,55', 'maxhp,30', 70);
INSERT INTO `$drops` VALUES (71, 'Angel''s Teaser', 29, 1, 'maxhp,35', 'strength,35', 24);
INSERT INTO `$drops` VALUES (72, 'Seraph''s Teaser', 29, 1, 'maxmp,35', 'dexterity,35', 24);
INSERT INTO `$drops` VALUES (73, 'Small Ruby', 35, 1, 'maxhp,75', 'strength,25', 30);
INSERT INTO `$drops` VALUES (74, 'Small Pearl', 35, 1, 'maxmp,75', 'dexterity,25', 30);
INSERT INTO `$drops` VALUES (75, 'Small Emerald', 35, 1, 'strength,75', 'maxhp,25', 30);
INSERT INTO `$drops` VALUES (76, 'Small Topaz', 35, 1, 'dexterity,75', 'maxmp,25', 30);
INSERT INTO `$drops` VALUES (77, 'Small Obsidian', 35, 1, 'attackpower,75', 'defensepower,25', 30);
INSERT INTO `$drops` VALUES (78, 'Small Diamond', 35, 1, 'defensepower,75', 'attackpower,25', 30);
INSERT INTO `$drops` VALUES (98, 'Brilliant Gift', 95, 1, 'defensepower,200', 'maxhp,70', 90);
INSERT INTO `$drops` VALUES (79, 'Fortune Trinket', 30, 1, 'goldbonus,5', 'X', 25);
INSERT INTO `$drops` VALUES (80, 'Experience Trinket', 30, 1, 'expbonus,5', 'X', 25);
INSERT INTO `$drops` VALUES (81, 'Large Ruby', 65, 1, 'maxhp,165', 'strength,50', 60);
INSERT INTO `$drops` VALUES (82, 'Large Pearl', 65, 1, 'maxmp,165', 'dexterity,50', 60);
INSERT INTO `$drops` VALUES (83, 'Large Emerald', 65, 1, 'strength,165', 'maxhp,50', 60);
INSERT INTO `$drops` VALUES (84, 'Large Topaz', 65, 1, 'dexterity,165', 'maxmp,50', 60);
INSERT INTO `$drops` VALUES (85, 'Large Obsidian', 65, 1, 'attackpower,165', 'defensepower,50', 60);
INSERT INTO `$drops` VALUES (86, 'Large Diamond', 65, 1, 'defensepower,165', 'attackpower,50', 60);
INSERT INTO `$drops` VALUES (87, 'Double Diamond', 95, 1, 'defensepower,195', 'attackpower,75', 90);
INSERT INTO `$drops` VALUES (88, 'Ultimate Sorceress Charm', 88, 1, 'maxmp,225', 'maxhp,120', 83);
INSERT INTO `$drops` VALUES (89, 'Ultimate Barbarian Charm', 88, 1, 'strength,225', 'maxhp,120', 83);
INSERT INTO `$drops` VALUES (90, 'Ultimate Paladin Charm', 88, 1, 'attackpower,195', 'maxhp,120', 83);
INSERT INTO `$drops` VALUES (91, 'Ultimate Ranger Charm', 88, 1, 'dexterity,225', 'maxhp,120', 83);
INSERT INTO `$drops` VALUES (92, 'Ultimate Necromancer Charm', 88, 1, 'maxmp,220', 'maxhp,125', 83);
INSERT INTO `$drops` VALUES (93, 'Ultimate Druid Charm', 88, 1, 'strength,205', 'maxhp,125', 83);
INSERT INTO `$drops` VALUES (94, 'Ultimate Assassin Charm', 88, 1, 'dexterity,225', 'maxhp,105', 83);
INSERT INTO `$drops` VALUES (95, 'Gem Of Prosperity', 103, 1, 'goldbonus,20', 'expbonus,5', 98);
INSERT INTO `$drops` VALUES (96, 'Evasive Treasure', 85, 1, 'dexterity,175', 'goldbonus,10', 80);
INSERT INTO `$drops` VALUES (97, 'Progressive Renewal', 85, 1, 'strength,175', 'expbonus,10', 80);
INSERT INTO `$drops` VALUES (99, 'Amazing Gift', 95, 1, 'expbonus,10', 'goldbonus,10', 90);
INSERT INTO `$drops` VALUES (100, 'Profound Deity', 105, 1, 'expbonus,15', 'goldbonus,15', 100);
END;
if (dobatch($query) == 1) { echo "Drops table populated.<br />"; } else { echo "Error populating Drops table."; }
unset($query);
	
$query = <<<END
CREATE TABLE `$duel` (
  `duelid` int(11) NOT NULL auto_increment,
  `duelstatus` int(11) NOT NULL default '0',
  `player1id` int(11) NOT NULL default '0',
  `player1hp` int(11) NOT NULL default '0',
  `player1mp` int(11) unsigned NOT NULL default '0',
  `player1lr` datetime NOT NULL default '0000-00-00 00:00:00',
  `player1la` int(11) NOT NULL default '0',
  `player1ls` varchar(255) NOT NULL default '0',
  `player1pa` int(11) NOT NULL default '0',
  `player1ps` varchar(255) NOT NULL default '0',
  `player1ta` int(11) NOT NULL default '0',
  `player1se` varchar(255) NOT NULL default '0',
  `player1ex` varchar(255) NOT NULL default '0',
  `player1done` int(11) NOT NULL default '0',
  `player2id` int(11) NOT NULL default '0',
  `player2hp` int(11) NOT NULL default '0',
  `player2mp` int(11) unsigned NOT NULL default '0',
  `player2lr` datetime NOT NULL default '0000-00-00 00:00:00',
  `player2la` int(11) NOT NULL default '0',
  `player2ls` varchar(255) NOT NULL default '0',
  `player2pa` int(11) NOT NULL default '0',
  `player2ps` varchar(255) NOT NULL default '0',
  `player2ta` int(11) NOT NULL default '0',
  `player2se` varchar(255) NOT NULL default '0',
  `player2ex` varchar(255) NOT NULL default '0',
  `player2done` int(11) NOT NULL default '0',
  PRIMARY KEY  (`duelid`)

) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Duel table created.<br />"; } else { echo "Error creating Duel table."; }
unset($query);


$query = <<<END
CREATE TABLE `$endurance` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(30) NOT NULL default '',
  `level` int(3) NOT NULL default '1',
  `fatigue` smallint(4) NOT NULL default '2',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Endurance table created.<br />"; } else { echo "Error creating Endurance table."; }
unset($query);

$query = <<<END
INSERT INTO `$endurance` VALUES (1, 'go across the Log', 1, 2);
INSERT INTO `$endurance` VALUES (2, 'climb the Net', 30, 3);
INSERT INTO `$endurance` VALUES (3, 'go through the Pipe', 65, 4);
INSERT INTO `$endurance` VALUES (4, 'walk across the Stepping Stone', 95, 5);
INSERT INTO `$endurance` VALUES (5, 'use the Rope Swing', 150, 6);
INSERT INTO `$endurance` VALUES (6, 'go through the whole Course', 215, 7);

END;
if (dobatch($query) == 1) { echo "Endurance table populated.<br />"; } else { echo "Error populating Endurance table."; }
unset($query);

$query = <<<END
CREATE TABLE `$forging` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `itemid` tinyint(3) unsigned NOT NULL default '0',
  `itemname` varchar(40) NOT NULL default '',
  `level` tinyint(3) unsigned NOT NULL default '1',
  `bar1` int(3) NOT NULL default '0',
  `bar2` int(3) NOT NULL default '0',
  `bar3` int(3) NOT NULL default '0',
  `bar4` int(3) NOT NULL default '0',
  `bar5` int(3) NOT NULL default '0',
  `bar6` int(3) NOT NULL default '0',
  `bar7` int(3) NOT NULL default '0',
  `bar8` int(3) NOT NULL default '0',
  `bar9` int(3) NOT NULL default '0',
  `bar10` int(3) NOT NULL default '0',
  `bar11` int(3) NOT NULL default '0',
  `bar12` int(3) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Forging table created.<br />"; } else { echo "Error creating Forging table."; }
unset($query);

$query = <<<END
INSERT INTO `$forging` VALUES (1, 4, 'Dagger', 7, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (2, 5, 'Hatchet', 13, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (3, 6, 'Axe', 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (4, 7, 'Brand', 19, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (5, 8, 'Poleaxe', 36, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (6, 9, 'Broadsword', 38, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (7, 10, 'Battle Axe', 47, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (8, 11, 'Claymore', 50, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (9, 12, 'Dark Axe', 65, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (10, 13, 'Dark Sword', 74, 0, 0, 0, 15, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (11, 14, 'Bright Sword', 97, 0, 0, 0, 0, 16, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (12, 15, 'Magic Sword', 63, 0, 0, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (13, 16, 'Destiny Blade', 117, 0, 0, 0, 0, 0, 18, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (14, 21, 'Chain Mail', 22, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (15, 22, 'Bronze Plate', 28, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (16, 23, 'Iron Plate', 40, 0, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (17, 24, 'Magic Armor', 58, 0, 0, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (18, 25, 'Dark Armor', 77, 0, 0, 0, 16, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (19, 26, 'Bright Armor', 90, 0, 0, 0, 0, 16, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (20, 27, 'Destiny Raiment', 107, 0, 0, 0, 0, 0, 15, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (21, 29, 'Buckler', 10, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (22, 30, 'Small Shield', 34, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (23, 31, 'Large Shield', 66, 0, 0, 0, 9, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (24, 32, 'Silver Shield', 80, 0, 0, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (25, 33, 'Destiny Aegis', 100, 0, 0, 0, 0, 0, 10, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (26, 34, 'Black Dragons Sword', 244, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 199);
INSERT INTO `$forging` VALUES (27, 35, 'Black Dragons Armor', 248, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 212);
INSERT INTO `$forging` VALUES (28, 36, 'Black Dragons Kite', 238, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 190);
INSERT INTO `$forging` VALUES (29, 37, 'Black Dragons Axe', 250, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 250);
INSERT INTO `$forging` VALUES (30, 45, 'Crystal Blade', 142, 0, 0, 0, 0, 0, 0, 62, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (31, 46, 'Diamond Gladius', 166, 0, 0, 0, 0, 0, 0, 0, 110, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (32, 47, 'Heros Pilum', 186, 0, 0, 0, 0, 0, 0, 0, 0, 237, 0, 0, 0);
INSERT INTO `$forging` VALUES (33, 48, 'Holy Falcata', 212, 0, 0, 0, 0, 0, 0, 0, 0, 0, 167, 0, 0);
INSERT INTO `$forging` VALUES (34, 49, 'Mythical Kopis', 229, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 163, 0);
INSERT INTO `$forging` VALUES (35, 55, 'Crystal Aegis', 125, 0, 0, 0, 0, 0, 0, 32, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (36, 56, 'Diamond Hoplitic', 148, 0, 0, 0, 0, 0, 0, 0, 57, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (37, 57, 'Hero Scutum', 188, 0, 0, 0, 0, 0, 0, 0, 0, 260, 0, 0, 0);
INSERT INTO `$forging` VALUES (38, 58, 'Holy Lekythos', 190, 0, 0, 0, 0, 0, 0, 0, 0, 0, 64, 0, 0);
INSERT INTO `$forging` VALUES (39, 59, 'Mythical Enchantment', 215, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 130, 0);
INSERT INTO `$forging` VALUES (40, 60, 'Crystal Chainmail', 133, 0, 0, 0, 0, 0, 0, 51, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (41, 61, 'Diamond Chainmail', 157, 0, 0, 0, 0, 0, 0, 0, 90, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (42, 62, 'Heros Corbridge', 178, 0, 0, 0, 0, 0, 0, 0, 0, 202, 0, 0, 0);
INSERT INTO `$forging` VALUES (43, 63, 'Holy Cuirass', 202, 0, 0, 0, 0, 0, 0, 0, 0, 0, 155, 0, 0);
INSERT INTO `$forging` VALUES (44, 64, 'Mythical Cuirass', 222, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 156, 0);
INSERT INTO `$forging` VALUES (45, 68, 'Enchanted Javelin', 45, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (46, 70, 'Dark Javelin', 67, 0, 0, 0, 12, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (47, 72, 'Bright Javelin', 95, 0, 0, 0, 0, 16, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (48, 74, 'Magic Javelin', 52, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (49, 76, 'Destiny Javelin', 115, 0, 0, 0, 0, 0, 17, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (50, 78, 'Crystal Javelin', 137, 0, 0, 0, 0, 0, 0, 60, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (51, 80, 'Diamond Javelin', 160, 0, 0, 0, 0, 0, 0, 0, 91, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (52, 82, 'Heros Javelin', 180, 0, 0, 0, 0, 0, 0, 0, 0, 215, 0, 0, 0);
INSERT INTO `$forging` VALUES (53, 84, 'Holy Javelin', 205, 0, 0, 0, 0, 0, 0, 0, 0, 0, 157, 0, 0);
INSERT INTO `$forging` VALUES (54, 86, 'Mythical Javelin', 227, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 158, 0);
INSERT INTO `$forging` VALUES (55, 88, 'Black Dragons Javelin', 240, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 195);
INSERT INTO `$forging` VALUES (56, 101, 'Claw', 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (57, 102, 'Battle Claw', 32, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (58, 103, 'Dark Claw', 69, 0, 0, 0, 12, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (59, 104, 'Bright Claw', 92, 0, 0, 0, 0, 16, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (60, 105, 'Magic Claw', 54, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (61, 106, 'Destiny Claw', 112, 0, 0, 0, 0, 0, 17, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (62, 107, 'Crystal Claw', 139, 0, 0, 0, 0, 0, 0, 60, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (63, 108, 'Diamond Claw', 163, 0, 0, 0, 0, 0, 0, 0, 91, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (64, 109, 'Heros Claw', 183, 0, 0, 0, 0, 0, 0, 0, 0, 215, 0, 0, 0);
INSERT INTO `$forging` VALUES (65, 110, 'Holy Claw', 208, 0, 0, 0, 0, 0, 0, 0, 0, 0, 157, 0, 0);
INSERT INTO `$forging` VALUES (66, 111, 'Mythical Claw', 232, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 164, 0);
INSERT INTO `$forging` VALUES (67, 112, 'Black Dragons Claw', 242, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 197);
INSERT INTO `$forging` VALUES (68, 113, 'Warhammer', 73, 0, 0, 0, 14, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (69, 114, 'Steel Warhammer', 82, 0, 0, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (70, 115, 'Crystal Warhammer', 120, 0, 0, 0, 0, 0, 0, 3, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (71, 119, 'Bronze Helm', 4, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (72, 120, 'Iron Helm', 30, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (73, 121, 'Magic Helm', 56, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (74, 122, 'Dark Helm', 71, 0, 0, 0, 12, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (75, 123, 'Bright Helm', 85, 0, 0, 0, 0, 14, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (76, 124, 'Destiny Helm', 103, 0, 0, 0, 0, 0, 13, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (77, 125, 'Crystal Helm', 122, 0, 0, 0, 0, 0, 0, 24, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (78, 126, 'Diamond Helm', 145, 0, 0, 0, 0, 0, 0, 0, 42, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (79, 127, 'Hero Full Helm', 170, 0, 0, 0, 0, 0, 0, 0, 0, 165, 0, 0, 0);
INSERT INTO `$forging` VALUES (80, 128, 'Holy Full Helm', 193, 0, 0, 0, 0, 0, 0, 0, 0, 0, 126, 0, 0);
INSERT INTO `$forging` VALUES (81, 129, 'Mythical Full Helm', 217, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 139, 0);
INSERT INTO `$forging` VALUES (82, 130, 'Black Dragons Large Helm', 236, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 175);
INSERT INTO `$forging` VALUES (83, 131, 'Bronze Leg Armor', 25, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (84, 132, 'Iron Leg Armor', 43, 0, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (85, 133, 'Magic Leg Armor', 60, 0, 0, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (86, 134, 'Dark Leg Armor', 75, 0, 0, 0, 16, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (87, 135, 'Bright Leg Armor', 87, 0, 0, 0, 0, 16, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (88, 136, 'Destiny Leg Armor', 110, 0, 0, 0, 0, 0, 16, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (89, 137, 'Crystal Leg Armor', 127, 0, 0, 0, 0, 0, 0, 35, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (90, 138, 'Diamond Leg Armor', 154, 0, 0, 0, 0, 0, 0, 0, 87, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (91, 139, 'Heros Leg Armor', 173, 0, 0, 0, 0, 0, 0, 0, 0, 185, 0, 0, 0);
INSERT INTO `$forging` VALUES (92, 140, 'Holy Leg Armor', 199, 0, 0, 0, 0, 0, 0, 0, 0, 0, 155, 0, 0);
INSERT INTO `$forging` VALUES (93, 141, 'Mythical Leg Armor', 220, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 156, 0);
INSERT INTO `$forging` VALUES (94, 142, 'Black Dragons Leg Armor', 246, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 200);
INSERT INTO `$forging` VALUES (95, 147, 'Destiny Gauntlets', 105, 0, 0, 0, 0, 0, 15, 0, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (96, 148, 'Crystal Gauntlets', 130, 0, 0, 0, 0, 0, 0, 48, 0, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (97, 149, 'Diamond Gauntlets', 151, 0, 0, 0, 0, 0, 0, 0, 83, 0, 0, 0, 0);
INSERT INTO `$forging` VALUES (98, 150, 'Heros Gauntlets', 176, 0, 0, 0, 0, 0, 0, 0, 0, 200, 0, 0, 0);
INSERT INTO `$forging` VALUES (99, 151, 'Holy Gauntlets', 196, 0, 0, 0, 0, 0, 0, 0, 0, 0, 155, 0, 0);
INSERT INTO `$forging` VALUES (100, 152, 'Mythical Gauntlets', 225, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 157, 0);
INSERT INTO `$forging` VALUES (101, 153, 'Black Dragons Plated Gauntlets', 235, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 170);

END;
if (dobatch($query) == 1) { echo "Forging table populated.<br />"; } else { echo "Error populating Forging table."; }
unset($query);

$query = <<<END
CREATE TABLE `$gamemail` (
  `id` int(11) NOT NULL auto_increment,
  `postdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` varchar(30) NOT NULL default '',
  `subject` varchar(35) NOT NULL default 'New Message',
  `recipient` varchar(30) NOT NULL default '',
  `content` text NOT NULL,
  `save` tinyint(3) NOT NULL default '0',
  `mread` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)

) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Game Mail table created.<br />"; } else { echo "Error creating Game Mail table."; }
unset($query);



$query = <<<END
CREATE TABLE `$general` (
  `id` int(11) NOT NULL auto_increment,
  `postdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `newpostdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` varchar(30) NOT NULL default '',
  `parent` int(11) NOT NULL default '0',
  `replies` int(11) NOT NULL default '0',
  `close` tinyint(1) NOT NULL default '0',
  `pin` tinyint(1) NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `content` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "General Forum table created.<br />"; } else { echo "Error creating General Forum table."; }
unset($query);



$query = <<<END
CREATE TABLE `$gforum` (
  `id` int(11) NOT NULL auto_increment,
  `postdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `newpostdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` varchar(30) NOT NULL default '',
  `parent` int(11) NOT NULL default '0',
  `replies` int(11) NOT NULL default '0',
  `close` tinyint(1) NOT NULL default '0',
  `pin` tinyint(1) NOT NULL default '0',
  `guildname` varchar(30) NOT NULL default '-',
  `title` varchar(100) NOT NULL default '',
  `content` text NOT NULL,
  PRIMARY KEY  (`id`)

) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Guild Forum table created.<br />"; } else { echo "Error creating Guild Forum table."; }
unset($query);



$query = <<<END
CREATE TABLE `$guilds` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(30) NOT NULL default '',
  `tag` char(3) NOT NULL default '-',
  `members` smallint(5) unsigned NOT NULL default '1',
  `dscales` smallint(10) unsigned NOT NULL default '2750',
  `password` varchar(15) NOT NULL default '',
  `joincost` mediumint(8) NOT NULL default '150',
  `private` tinyint(1) NOT NULL default '0',
  `experience` int(10) NOT NULL default '0',
  `level` tinyint(3) NOT NULL default '1',
  `founder` varchar(30) NOT NULL default '',
  `description` text NOT NULL,
  `news` text NOT NULL,
  `exp_pool` int(99) unsigned NOT NULL default '0',
  `mine_pool` int(99) unsigned NOT NULL default '0',
  `smelt_pool` int(99) unsigned NOT NULL default '0',
  `endurance_pool` int(99) unsigned NOT NULL default '0',
  `craft_pool` int(99) unsigned NOT NULL default '0',
  `forge_pool` int(99) unsigned NOT NULL default '0',
  `prayer_pool` int(99) unsigned NOT NULL default '0',
  `rank1name` varchar(20) NOT NULL default 'New Member',
  `rank2name` varchar(20) NOT NULL default 'Member',
  `rank3name` varchar(20) NOT NULL default 'Junior Member',
  `rank4name` varchar(20) NOT NULL default 'Elite Member',
  `rank5name` varchar(20) NOT NULL default 'Hero Member',
  `rank6name` varchar(20) NOT NULL default 'Legend Member',
  PRIMARY KEY  (`id`)

) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Guilds table created.<br />"; } else { echo "Error creating Guilds table."; }
unset($query);



#### End 
    global $start;
    $time = round((getmicrotime() - $start), 4);
    echo "<br />Database setup part A complete in $time seconds.<br /><br /><a href=\"install.php?page=3\">Click here to continue with part B of the installation.</a></body></html>";
    die();
    
}


function third() { // Third page - set up the database tables.
    
    global $dbsettings;
    echo "<html><head><title>DK Installation</title></head><body><b>DK Installation: Page Three, Part B</b><br /><br />";
    $prefix = $dbsettings["prefix"];
	$arena = $prefix . "_arena";
	$chat = $prefix . "_chat";
    $comments = $prefix . "_comments";
    $control = $prefix . "_control";
    $crafting = $prefix . "_crafting";	
    $drops = $prefix . "_drops";
    $duel = $prefix . "_duel";
    $endurance = $prefix . "_endurance";
	$forging = $prefix . "_forging";
	$gamemail = $prefix . "_gamemail";
	$general = $prefix . "_general";
	$gforum = $prefix . "_gforum";
    $guilds = $prefix . "_guilds";
    $homes = $prefix . "_homes";
	$inventitems = $prefix . "_inventitems";
    $items = $prefix . "_items";
    $itemstorage = $prefix . "_itemstorage";
    $jewellery = $prefix . "_jewellery";
    $levels = $prefix . "_levels";
    $marketforum = $prefix . "_marketforum";
	$mining = $prefix . "_mining";
    $monsters = $prefix . "_monsters";
    $news = $prefix . "_news";
	$playermarket = $prefix . "_playermarket";
	$poll = $prefix . "_poll";
	$smelting = $prefix . "_smelting";
	$souls = $prefix . "_souls";
    $spells = $prefix . "_spells";
	$staff = $prefix . "_staff";
	$strongholds = $prefix . "_strongholds";
	$suggestions = $prefix . "_suggestions";
	$support = $prefix . "_support";
    $towns = $prefix . "_towns";
    $users = $prefix . "_users";
    
    if (isset($_POST["complete"])) { $full = true; } else { $full = false; }
    


$query = <<<END
CREATE TABLE `$homes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `latitude` smallint(5) NOT NULL default '0',
  `longitude` smallint(5) NOT NULL default '0',
  `charname` varchar(30) NOT NULL default '',
  `ruined` tinyint(3) NOT NULL default '0',
  `armor` smallint(5) unsigned NOT NULL default '100',
  `weaponry` smallint(5) unsigned NOT NULL default '100',
  `armorlevel` tinyint(3) unsigned NOT NULL default '1',
  `weaponrylevel` tinyint(3) unsigned NOT NULL default '1',
  `currenthp` smallint(6) unsigned NOT NULL default '2500',
  `maxhp` smallint(6) unsigned NOT NULL default '2500',
  `guards` smallint(5) unsigned NOT NULL default '5',
  PRIMARY KEY  (`id`)

) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Homes table created.<br />"; } else { echo "Error creating Homes table."; }
unset($query);


$query = <<<END
CREATE TABLE `$inventitems` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `type` int(3) unsigned NOT NULL default '0',
  `combatOK` tinyint(1) unsigned NOT NULL default '1',
  `questitem` int(2) NOT NULL default '0',
  `name` varchar(30) NOT NULL default '',
  `buycost` int(10) unsigned NOT NULL default '0',
  `strength` smallint(5) unsigned NOT NULL default '0',
  `charges` tinyint(3) unsigned NOT NULL default '1',
  `description` varchar(250) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Inventory Items table created.<br />"; } else { echo "Error creating Inventory Items table."; }
unset($query);

$query = <<<END
INSERT INTO `$inventitems` VALUES (1, 1, 1, 0, 'New Player Potion', 20, 5, 1, 'A few sips of healing potion for new players.');
INSERT INTO `$inventitems` VALUES (2, 1, 0, 0, 'Healing Herb', 35, 10, 1, 'A small frond of special healing herb');
INSERT INTO `$inventitems` VALUES (3, 1, 0, 0, 'Healing Bundle', 1200, 55, 60, 'Bundle of healing herb fronds.');
INSERT INTO `$inventitems` VALUES (4, 1, 0, 0, 'Regeneration Pearl', 15000, 2000, 10, 'Restores the wounded to full health.');
INSERT INTO `$inventitems` VALUES (5, 1, 0, 0, 'Elixir', 15000, 2000, 1, 'Very strong potion that heals all wounds.');
INSERT INTO `$inventitems` VALUES (6, 1, 0, 0, 'Revival Jewel', 25000, 2000, 10, 'Jewel that fully restores Health.');
INSERT INTO `$inventitems` VALUES (8, 1, 0, 0, 'Lifespring Gem', 30000, 250, 55, 'Gem infused with healing light.');
INSERT INTO `$inventitems` VALUES (9, 2, 0, 0, 'Bubble Blast', 100, 50, 1, 'Bubble of Arcane energy that can damage enemies.');
INSERT INTO `$inventitems` VALUES (10, 2, 0, 0, 'Arcane Arrow', 500, 150, 1, 'Small arrow enchanted with extra damage power.');
INSERT INTO `$inventitems` VALUES (11, 2, 0, 0, 'Slime', 1000, 35, 10, 'Acidic slime made from venomous slimes.');
INSERT INTO `$inventitems` VALUES (12, 2, 0, 0, 'Bogg Juice', 10000, 350, 10, 'Strong acid made from venomous bugs.');
INSERT INTO `$inventitems` VALUES (13, 3, 0, 0, 'Sleep Pollen', 100, 10, 0, 'Small bag of sleep-inducing pollen.');
INSERT INTO `$inventitems` VALUES (14, 3, 0, 0, 'Tiresome Dart', 1000, 40, 1, 'Dart coated with sleeping toxin.');
INSERT INTO `$inventitems` VALUES (15, 4, 0, 0, 'Poison Dart', 50, 20, 0, 'Something to help battling monsters');
INSERT INTO `$inventitems` VALUES (16, 4, 0, 0, 'Snake Venom', 1000, 100, 0, 'Glands from a snake used to poison monsters.');
INSERT INTO `$inventitems` VALUES (17, 4, 0, 0, 'Poison Bolt', 65535, 100, 99, 'Unleashes a powerful toxin to poison enemies.');
INSERT INTO `$inventitems` VALUES (18, 5, 0, 0, 'Gem of Decay', 10000, 40, 10, 'Severely weakens a monster if they are not immune.');
INSERT INTO `$inventitems` VALUES (19, 5, 0, 0, 'Weakness Charm', 4000, 5, 80, 'Weakens an enemy by a small amount.');
INSERT INTO `$inventitems` VALUES (20, 9, 0, 0, 'Chaos Gem', 500, 4, 50, 'Unleash the raw power of chance.');
INSERT INTO `$inventitems` VALUES (21, 9, 0, 0, 'Pair of Dice', 1000, 6, 90, 'Feeling lucky?  Roll them bones!');
INSERT INTO `$inventitems` VALUES (22, 10, 0, 0, 'Death Bringer', 5000, 50, 10, 'Hammer enchanted to kill with one strike.');
INSERT INTO `$inventitems` VALUES (23, 10, 0, 0, 'Soultrap Charm', 2500, 25, 50, 'Ancient charm with the power to kill.');
INSERT INTO `$inventitems` VALUES (24, 11, 1, 0, 'Safeportal Scroll', 100, 1, 1, 'Magic Scroll which casts the Safeportal spell.');
INSERT INTO `$inventitems` VALUES (25, 12, 0, 0, 'Fire Gem', 100, 50, 1, 'Small Gem enchanted with the power of fire.');
INSERT INTO `$inventitems` VALUES (26, 12, 0, 0, 'Ice Gem', 100, 50, 1, 'Small Gem enchanted with the power of frost.');
INSERT INTO `$inventitems` VALUES (27, 12, 0, 0, 'Dirtstrike Gem', 100, 50, 1, 'Small Gem enchanted with the power of earth.');
INSERT INTO `$inventitems` VALUES (28, 12, 0, 0, 'Flame Jewel', 500, 200, 1, 'Large jewel enchanted with the power of flame.');
INSERT INTO `$inventitems` VALUES (29, 12, 0, 0, 'Glacial Jewel', 500, 200, 1, 'Large jewel enchanted with the power of ice.');
INSERT INTO `$inventitems` VALUES (30, 12, 0, 0, 'Lightning Jewel', 500, 200, 1, 'Large jewel enchanted with the power of lightning.');
INSERT INTO `$inventitems` VALUES (31, 12, 0, 0, 'Stonestrike Jewel', 500, 200, 1, 'Large jewel enchanted with the power of stone.');
INSERT INTO `$inventitems` VALUES (32, 12, 0, 0, 'Spark Gem', 100, 50, 1, 'Small Gem enchanted with the power of electricity.');
INSERT INTO `$inventitems` VALUES (33, 13, 0, 0, 'Manaspring Gem', 2500, 50, 20, 'Strange jewel infused with mystical energies.');
INSERT INTO `$inventitems` VALUES (34, 13, 0, 0, 'Special Rose', 5000, 70, 20, 'Its strange fragrance refreshes magic');
INSERT INTO `$inventitems` VALUES (35, 13, 1, 0, 'Dry Root', 200, 15, 10, 'Used to restore some magic power.');
INSERT INTO `$inventitems` VALUES (36, 13, 0, 0, 'Mana Herb', 350, 30, 15, 'A herb to restore magic.');
INSERT INTO `$inventitems` VALUES (37, 13, 0, 0, 'Magical Elixer', 32000, 9999, 40, 'Ancient remedy to restore to maximum magic points.');
INSERT INTO `$inventitems` VALUES (38, 16, 1, 0, 'Teleport Amulet', 50000, 1, 255, 'A rare amulet that allows unlimited teleporting.');
INSERT INTO `$inventitems` VALUES (39, 16, 1, 0, 'Teleport Scroll', 1000, 1, 20, 'Magic Scroll which casts the Teleport spell.');
INSERT INTO `$inventitems` VALUES (40, 18, 0, 0, 'Willow Tree Extract', 7500, 215, 5, 'An ancient pain remedy to restore health.');
INSERT INTO `$inventitems` VALUES (41, 19, 0, 0, 'Pet Net', 50, 11, 1, 'A small net used to capture enemies for the Arena. Only levels 10 and below monsters.');
INSERT INTO `$inventitems` VALUES (42, 19, 0, 0, 'Tangle bag', 1000, 21, 15, 'A strong net used to capture enemies for the Arena. Only levels 20 and below monsters.');
INSERT INTO `$inventitems` VALUES (43, 19, 0, 0, 'Webbed Orb', 1600, 31, 25, 'A powerful net used to capture enemies for the Arena. Only levels 30 and below monsters.');
INSERT INTO `$inventitems` VALUES (44, 13, 0, 0, 'Perfect Sapphire', 40000, 9999, 70, 'Special item to restore to maximum magic points. Usually able to be used quite a few times.');
INSERT INTO `$inventitems` VALUES (45, 21, 0, 0, 'Fresh Cut Flowers', 10, 2, 0, 'In memory of a lost friend.');
INSERT INTO `$inventitems` VALUES (46, 21, 0, 0, 'Tomb Stone', 400, 25, 0, 'In memory of a lost friend.');
INSERT INTO `$inventitems` VALUES (47, 21, 0, 0, 'Bottle of Scotch', 50, 5, 0, 'In memory of a lost friend.');
INSERT INTO `$inventitems` VALUES (48, 21, 0, 0, 'Bronzed Weapon', 200, 20, 0, 'In memory of a lost friend.');
INSERT INTO `$inventitems` VALUES (49, 21, 0, 0, 'Memorial Flag', 10, 10, 0, 'In memory of a lost friend.');
INSERT INTO `$inventitems` VALUES (50, 21, 0, 0, 'Bronze Plaque', 60, 15, 0, 'In memory of a lost friend.');
INSERT INTO `$inventitems` VALUES (51, 3, 1, 0, 'Cave Weed', 500, 25, 1, 'A powerful weed that makes monsters sleepy and confused.');
INSERT INTO `$inventitems` VALUES (53, 17, 0, 0, 'Antidote', 50, 5, 1, 'Instantly cures poison status');
INSERT INTO `$inventitems` VALUES (54, 17, 0, 0, 'Purity Locket', 5000, 5, 95, 'Instantly cures poison status.');
INSERT INTO `$inventitems` VALUES (55, 20, 1, 0, 'Guild Scroll', 0, 0, 1, 'Used to form your own Guild');
INSERT INTO `$inventitems` VALUES (56, 5, 1, 0, 'Silver Needle', 50000, 1, 99, 'Small, yet deadly accurate.');
INSERT INTO `$inventitems` VALUES (57, 13, 1, 0, 'Manaspring Jewel', 20000, 70, 70, 'Strange jewel infused with mystical energies');
INSERT INTO `$inventitems` VALUES (72, 25, 0, 0, 'Rare Cave Weed', 65535, 500, 1, 'Free your mind.');
INSERT INTO `$inventitems` VALUES (71, 27, 1, 0, 'Souls Orb', 1, 500, 1, 'Crystalised Essence of Experience.');
INSERT INTO `$inventitems` VALUES (70, 26, 1, 0, 'Souls Jewel', 1, 500, 1, 'Crystalised Essence of Experience.');
INSERT INTO `$inventitems` VALUES (69, 25, 1, 0, 'Souls Gem', 1, 500, 1, 'Crystalised Essence of Experience.');
INSERT INTO `$inventitems` VALUES (73, 15, 1, 0, 'Ability Herb', 10000, 5, 1, 'A small frond of special ability herb');
INSERT INTO `$inventitems` VALUES (74, 15, 1, 0, 'Ability Bundle', 21000, 12, 25, 'Bundle of ability herb fronds.');
INSERT INTO `$inventitems` VALUES (75, 15, 1, 0, 'Precious Stone', 35000, 20, 15, 'Magical stone which restores ability points.');
INSERT INTO `$inventitems` VALUES (76, 15, 1, 0, 'Ability Elixir', 46000, 25, 5, 'Very strong potion that restores AP.');
INSERT INTO `$inventitems` VALUES (77, 15, 1, 0, 'Stamina Jewel', 74000, 35, 20, 'Jewel that fully restores AP.');
INSERT INTO `$inventitems` VALUES (78, 15, 1, 0, 'Endurance Gem', 116000, 75, 60, 'Gem infused with the power to restore AP.');
INSERT INTO `$inventitems` VALUES (79, 28, 1, 1, 'Desert Tent', 50, 115, 101, 'A simple Tent to restore Fatigue, designed for the Desert terrain.');
INSERT INTO `$inventitems` VALUES (82, 14, 0, 0, 'Travel Herb', 15, 10, 1, 'A small frond of a special travel herb');
INSERT INTO `$inventitems` VALUES (83, 14, 0, 0, 'Travellers Bundle', 200, 25, 70, 'Bundle of travel herb fronds.');
INSERT INTO `$inventitems` VALUES (84, 14, 0, 0, 'Adventurers Potion', 400, 55, 50, 'Restores travel points.');
INSERT INTO `$inventitems` VALUES (85, 14, 0, 0, 'Elixir of Distance', 600, 75, 15, 'Very strong potion that restores travel points.');
INSERT INTO `$inventitems` VALUES (86, 14, 0, 0, 'Travellers Jewel', 1000, 100, 75, 'Jewel that fully restores travel points.');
INSERT INTO `$inventitems` VALUES (87, 14, 0, 0, 'Travel Gem', 1800, 125, 80, 'Gem infused with light to restore travel points.');
INSERT INTO `$inventitems` VALUES (88, 29, 1, 1, 'Precious Ring', 0, 0, 101, 'A precious Ring covered in blood. It looks expensive.');
INSERT INTO `$inventitems` VALUES (89, 30, 1, 1, 'Bag of Ingredients', 0, 0, 101, 'A Bag of Ingredients for Lucas.');
INSERT INTO `$inventitems` VALUES (90, 31, 1, 1, 'Empty Vial', 0, 0, 101, 'An empty vial, used for potions.');
INSERT INTO `$inventitems` VALUES (91, 32, 1, 1, 'Rare Potent Herb', 0, 0, 101, 'A rare potent herb. What could this Herb do?');
INSERT INTO `$inventitems` VALUES (92, 33, 1, 1, 'Empty Bucket', 0, 0, 101, 'An empty bucket. Cant be much use being empty.');
INSERT INTO `$inventitems` VALUES (93, 34, 1, 1, 'Bucket of Healing Water', 0, 0, 101, 'A Bucket of Healing Water taken from a Healing Pool.');
INSERT INTO `$inventitems` VALUES (94, 35, 1, 0, 'Special Desert Tent', 0, 0, 101, 'Stirlocks Tent to restore Fatigue, designed for the Desert terrain.');
INSERT INTO `$inventitems` VALUES (95, 36, 1, 1, 'Empty Crystal Jar', 0, 0, 101, 'An Empty Crystal Jar which looks useless. Bought from Narcillas Port.');
INSERT INTO `$inventitems` VALUES (96, 37, 1, 1, 'Crystal Jar with Ingredients', 0, 0, 101, 'A Crystal Jar containg the ingredients which Magnus placed into it.');
INSERT INTO `$inventitems` VALUES (97, 38, 1, 1, 'Crystal Jar with a Parasite', 0, 0, 101, 'A Crystal Jar containing a Parasite which you captured from a Corpse.');
INSERT INTO `$inventitems` VALUES (98, 39, 1, 1, 'Kings Grace Flower', 0, 0, 101, 'A Kings Grace Flower, picked from outside the Abandoned Ruins.');
INSERT INTO `$inventitems` VALUES (99, 40, 1, 0, 'Ring Mould', 100, 0, 101, 'Used to craft Rings.');
INSERT INTO `$inventitems` VALUES (100, 41, 1, 0, 'Amulet Mould', 150, 0, 101, 'Used to craft Amulets.');
INSERT INTO `$inventitems` VALUES (101, 50, 1, 0, 'Slice of Adams Cake', 0, 0, 1, 'Mmmm... Its a slice of Adams Birthday Cake! Looks tasty!');

END;
if (dobatch($query) == 1) { echo "Inventory Items table populated.<br />"; } else { echo "Error populating Inventory Items table."; }
unset($query);

$query = <<<END
CREATE TABLE `$items` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `type` tinyint(3) unsigned NOT NULL default '0',
  `name` varchar(30) NOT NULL default '',
  `buycost` int(10) unsigned NOT NULL default '0',
  `mlevel` smallint(5) unsigned NOT NULL default '0',
  `attribute` smallint(5) unsigned NOT NULL default '0',
  `special` varchar(50) NOT NULL default '',
  `requirement` smallint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Items table created.<br />"; } else { echo "Error creating Items table."; }
unset($query);

$query = <<<END
INSERT INTO `$items` VALUES (1, 1, 'Stick', 8, 0, 2, 'X', 0);
INSERT INTO `$items` VALUES (2, 1, 'Branch', 30, 0, 4, 'X', 0);
INSERT INTO `$items` VALUES (3, 1, 'Club', 40, 0, 5, 'X', 0);
INSERT INTO `$items` VALUES (4, 1, 'Dagger', 90, 0, 8, 'X', 0);
INSERT INTO `$items` VALUES (5, 1, 'Hatchet', 150, 0, 12, 'X', 0);
INSERT INTO `$items` VALUES (6, 1, 'Axe', 200, 0, 16, 'X', 0);
INSERT INTO `$items` VALUES (7, 1, 'Brand', 300, 0, 25, 'X', 0);
INSERT INTO `$items` VALUES (8, 1, 'Poleaxe', 700, 0, 35, 'X', 0);
INSERT INTO `$items` VALUES (9, 1, 'Broadsword', 800, 0, 45, 'X', 0);
INSERT INTO `$items` VALUES (10, 1, 'Battle Axe', 1200, 5, 50, 'X', 0);
INSERT INTO `$items` VALUES (11, 1, 'Claymore', 2000, 10, 60, 'X', 0);
INSERT INTO `$items` VALUES (12, 1, 'Dark Axe', 3000, 15, 100, 'expbonus,-5', 0);
INSERT INTO `$items` VALUES (13, 1, 'Dark Sword', 30000, 20, 100, 'expbonus,10', 25);
INSERT INTO `$items` VALUES (14, 1, 'Bright Sword', 62000, 30, 150, 'maxmp,50', 30);
INSERT INTO `$items` VALUES (15, 1, 'Magic Sword', 4500, 15, 125, 'expbonus,-10', 0);
INSERT INTO `$items` VALUES (16, 1, 'Destiny Blade', 85000, 40, 250, 'strength,50', 35);
INSERT INTO `$items` VALUES (17, 2, 'Skivvies', 25, 0, 2, 'goldbonus,10', 0);
INSERT INTO `$items` VALUES (18, 2, 'Clothes', 50, 0, 5, 'X', 0);
INSERT INTO `$items` VALUES (19, 2, 'Leather Armor', 75, 0, 10, 'X', 0);
INSERT INTO `$items` VALUES (20, 2, 'Hard Leather Armor', 150, 0, 25, 'X', 0);
INSERT INTO `$items` VALUES (21, 2, 'Chain Mail', 300, 0, 30, 'X', 0);
INSERT INTO `$items` VALUES (22, 2, 'Bronze Plate', 900, 5, 50, 'X', 5);
INSERT INTO `$items` VALUES (23, 2, 'Iron Plate', 2000, 10, 100, 'X', 10);
INSERT INTO `$items` VALUES (24, 2, 'Magic Armor', 4000, 15, 125, 'maxmp,50', 15);
INSERT INTO `$items` VALUES (25, 2, 'Dark Armor', 32000, 20, 150, 'expbonus,-10', 20);
INSERT INTO `$items` VALUES (26, 2, 'Bright Armor', 50000, 30, 175, 'expbonus,10', 25);
INSERT INTO `$items` VALUES (27, 2, 'Destiny Raiment', 70000, 40, 200, 'dexterity,50', 35);
INSERT INTO `$items` VALUES (28, 3, 'Reed Shield', 50, 0, 3, 'X', 0);
INSERT INTO `$items` VALUES (29, 3, 'Buckler', 100, 0, 6, 'X', 0);
INSERT INTO `$items` VALUES (30, 3, 'Small Shield', 500, 5, 15, 'X', 5);
INSERT INTO `$items` VALUES (31, 3, 'Large Shield', 18000, 15, 35, 'X', 15);
INSERT INTO `$items` VALUES (32, 3, 'Silver Shield', 30000, 25, 75, 'X', 20);
INSERT INTO `$items` VALUES (33, 3, 'Destiny Aegis', 45000, 40, 125, 'maxhp,50', 35);
INSERT INTO `$items` VALUES (34, 1, 'Black Dragons Sword', 27100000, 120, 510, 'strength,120', 95);
INSERT INTO `$items` VALUES (35, 2, 'Black Dragons Armor', 29250000, 120, 465, 'dexterity,105', 95);
INSERT INTO `$items` VALUES (36, 3, 'Black Dragons Kite', 25500000, 120, 305, 'maxhp,75', 95);
INSERT INTO `$items` VALUES (37, 1, 'Black Dragons Axe', 34500000, 120, 635, 'strength,185', 95);
INSERT INTO `$items` VALUES (38, 1, 'Wooden Staff', 42, 0, 4, 'maxmp,15', 0);
INSERT INTO `$items` VALUES (39, 1, 'Enchanted Staff', 165, 5, 12, 'maxmp,30', 0);
INSERT INTO `$items` VALUES (40, 1, 'Dark Staff', 16000, 20, 55, 'maxmp,70', 15);
INSERT INTO `$items` VALUES (41, 1, 'Bright Staff', 37000, 30, 70, 'maxmp,95', 25);
INSERT INTO `$items` VALUES (42, 1, 'Magic Staff', 2550, 15, 45, 'maxmp,65', 5);
INSERT INTO `$items` VALUES (43, 1, 'Destiny Staff', 67000, 40, 105, 'maxmp,115', 35);
INSERT INTO `$items` VALUES (44, 1, 'Black Dragons Staff', 27000000, 120, 175, 'maxmp,215', 95);
INSERT INTO `$items` VALUES (45, 1, 'Crystal Blade', 535000, 50, 260, 'strength,75', 45);
INSERT INTO `$items` VALUES (46, 1, 'Diamond Gladius', 1425000, 60, 310, 'expbonus,10', 55);
INSERT INTO `$items` VALUES (47, 1, 'Heros Pilum', 3960000, 70, 255, 'maxhp,75', 65);
INSERT INTO `$items` VALUES (48, 1, 'Holy Falcata', 5640000, 90, 375, 'strength,75', 75);
INSERT INTO `$items` VALUES (49, 1, 'Mythical Kopis', 9585000, 105, 415, 'maxmp,110', 85);
INSERT INTO `$items` VALUES (50, 1, 'Crystal Staff', 415400, 50, 120, 'maxmp,130', 45);
INSERT INTO `$items` VALUES (51, 1, 'Diamond Staff', 1150000, 60, 135, 'maxmp,145', 55);
INSERT INTO `$items` VALUES (52, 1, 'Heros Staff', 3187500, 70, 150, 'maxmp,160', 65);
INSERT INTO `$items` VALUES (53, 1, 'Holy Staff', 5125000, 90, 160, 'maxmp,175', 75);
INSERT INTO `$items` VALUES (54, 1, 'Mythical Staff', 8750000, 105, 175, 'maxmp,190', 85);
INSERT INTO `$items` VALUES (55, 3, 'Crystal Aegis', 279000, 50, 140, 'maxhp,-25', 45);
INSERT INTO `$items` VALUES (56, 3, 'Diamond Hoplitic', 730000, 60, 165, 'dexterity,15', 55);
INSERT INTO `$items` VALUES (57, 3, 'Hero Scutum', 4350000, 70, 185, 'dexterity,25', 65);
INSERT INTO `$items` VALUES (58, 3, 'Holy Lekythos', 2100000, 90, 215, 'maxhp,-50', 75);
INSERT INTO `$items` VALUES (59, 3, 'Mythical Enchantment', 7520000, 105, 225, 'dexterity,50', 85);
INSERT INTO `$items` VALUES (60, 2, 'Crystal Chainmail', 440000, 50, 230, 'maxhp,75', 45);
INSERT INTO `$items` VALUES (61, 2, 'Diamond Chainmail', 1150000, 60, 250, 'maxhp,-50', 55);
INSERT INTO `$items` VALUES (62, 2, 'Heros Corbridge', 3350000, 70, 300, 'maxhp,-75', 65);
INSERT INTO `$items` VALUES (63, 2, 'Holy Cuirass', 5200000, 90, 355, 'dexterity,70', 75);
INSERT INTO `$items` VALUES (65, 1, 'Wooden Bow', 24, 0, 4, 'dexterity,8', 0);
INSERT INTO `$items` VALUES (66, 1, 'Wooden Javelin', 25, 0, 4, 'strength,7', 0);
INSERT INTO `$items` VALUES (67, 1, 'Enchanted Bow', 145, 5, 14, 'dexterity,15', 0);
INSERT INTO `$items` VALUES (68, 1, 'Enchanted Javelin', 154, 5, 15, 'strength,14', 0);
INSERT INTO `$items` VALUES (69, 1, 'Dark Bow', 24500, 20, 80, 'dexterity,30', 25);
INSERT INTO `$items` VALUES (70, 1, 'Dark Javelin', 23790, 20, 95, 'strength,25', 25);
INSERT INTO `$items` VALUES (71, 1, 'Bright Bow', 53400, 30, 120, 'dexterity,34', 30);
INSERT INTO `$items` VALUES (72, 1, 'Bright Javelin', 50050, 30, 125, 'strength,32', 30);
INSERT INTO `$items` VALUES (73, 1, 'Magic Bow', 3150, 15, 62, 'dexterity,22', 0);
INSERT INTO `$items` VALUES (74, 1, 'Magic Javelin', 2980, 15, 70, 'strength,20', 0);
INSERT INTO `$items` VALUES (75, 1, 'Destiny Bow', 81000, 40, 140, 'dexterity,40', 35);
INSERT INTO `$items` VALUES (76, 1, 'Destiny Javelin', 78560, 40, 150, 'strength,40', 35);
INSERT INTO `$items` VALUES (77, 1, 'Crystal Bow', 528000, 50, 160, 'dexterity,58', 45);
INSERT INTO `$items` VALUES (78, 1, 'Crystal Javelin', 514500, 50, 173, 'strength,49', 45);
INSERT INTO `$items` VALUES (79, 1, 'Diamond Bow', 1200000, 60, 178, 'dexterity,67', 55);
INSERT INTO `$items` VALUES (80, 1, 'Diamond Javelin', 1160050, 60, 190, 'strength,60', 55);
INSERT INTO `$items` VALUES (81, 1, 'Heros Bow', 3750000, 70, 195, 'dexterity,80', 65);
INSERT INTO `$items` VALUES (82, 1, 'Heros Javelin', 3587500, 70, 208, 'strength,72', 65);
INSERT INTO `$items` VALUES (83, 1, 'Holy Bow', 5300500, 90, 242, 'dexterity,91', 75);
INSERT INTO `$items` VALUES (84, 1, 'Holy Javelin', 5250000, 90, 255, 'strength,85', 75);
INSERT INTO `$items` VALUES (85, 1, 'Mythical Bow', 9487500, 105, 305, 'dexterity,105', 85);
INSERT INTO `$items` VALUES (86, 1, 'Mythical Javelin', 9267500, 105, 325, 'strength,100', 85);
INSERT INTO `$items` VALUES (87, 1, 'Black Dragons Bow', 27100000, 120, 405, 'dexterity,165', 95);
INSERT INTO `$items` VALUES (88, 1, 'Black Dragons Javelin', 26400050, 120, 425, 'strength,152', 95);
INSERT INTO `$items` VALUES (64, 2, 'Mythical Cuirass', 9150000, 105, 395, 'expbonus,10', 85);
INSERT INTO `$items` VALUES (89, 1, 'Bone Wand', 42, 0, 3, 'maxmp,16', 0);
INSERT INTO `$items` VALUES (90, 1, 'Enchanted Wand', 165, 5, 11, 'maxmp,33', 5);
INSERT INTO `$items` VALUES (91, 1, 'Dark Wand', 16000, 20, 51, 'maxmp,75', 15);
INSERT INTO `$items` VALUES (92, 1, 'Bright Wand', 37000, 30, 65, 'maxmp,99', 25);
INSERT INTO `$items` VALUES (93, 1, 'Magic Wand', 2550, 15, 41, 'maxmp,68', 10);
INSERT INTO `$items` VALUES (94, 1, 'Destiny Wand', 67000, 40, 99, 'maxmp,118', 35);
INSERT INTO `$items` VALUES (95, 1, 'Crystal Wand', 415400, 50, 115, 'maxmp,134', 45);
INSERT INTO `$items` VALUES (96, 1, 'Diamond Wand', 1150000, 60, 129, 'maxmp,148', 55);
INSERT INTO `$items` VALUES (97, 1, 'Heros Wand', 3187500, 70, 141, 'maxmp,164', 65);
INSERT INTO `$items` VALUES (98, 1, 'Holy Wand', 5125000, 90, 155, 'maxmp,179', 75);
INSERT INTO `$items` VALUES (99, 1, 'Mythical Wand', 8750000, 105, 168, 'maxmp,195', 85);
INSERT INTO `$items` VALUES (100, 1, 'Black Dragons Wand', 27000000, 120, 174, 'maxmp,218', 95);
INSERT INTO `$items` VALUES (101, 1, 'Claw', 25, 0, 5, 'dexterity,8', 0);
INSERT INTO `$items` VALUES (102, 1, 'Battle Claw', 154, 5, 17, 'dexterity,16', 5);
INSERT INTO `$items` VALUES (103, 1, 'Dark Claw', 23790, 20, 99, 'dexterity,26', 15);
INSERT INTO `$items` VALUES (104, 1, 'Bright Claw', 50050, 30, 130, 'dexterity,33', 25);
INSERT INTO `$items` VALUES (105, 1, 'Magic Claw', 2980, 15, 70, 'dexterity,21', 10);
INSERT INTO `$items` VALUES (106, 1, 'Destiny Claw', 78560, 40, 155, 'dexterity,41', 35);
INSERT INTO `$items` VALUES (107, 1, 'Crystal Claw', 514500, 50, 177, 'dexterity,49', 45);
INSERT INTO `$items` VALUES (108, 1, 'Diamond Claw', 1160050, 60, 193, 'dexterity,61', 55);
INSERT INTO `$items` VALUES (109, 1, 'Heros Claw', 3587500, 70, 218, 'dexterity,73', 65);
INSERT INTO `$items` VALUES (110, 1, 'Holy Claw', 5250000, 90, 258, 'dexterity,86', 75);
INSERT INTO `$items` VALUES (111, 1, 'Mythical Claw', 9667500, 105, 328, 'dexterity,101', 85);
INSERT INTO `$items` VALUES (112, 1, 'Black Dragons Claw', 26700050, 120, 428, 'dexterity,153', 95);
INSERT INTO `$items` VALUES (113, 1, 'Warhammer', 28000, 15, 100, 'expbonus,4', 10);
INSERT INTO `$items` VALUES (114, 1, 'Steel Warhammer', 30000, 20, 101, 'expbonus,5', 15);
INSERT INTO `$items` VALUES (115, 1, 'Crystal Warhammer', 32000, 25, 103, 'expbonus,6', 20);
INSERT INTO `$items` VALUES (116, 1, 'Orb of Fortune', 3500, 5, 55, 'goldbonus,7', 5);
INSERT INTO `$items` VALUES (117, 1, 'Orb of Richness', 7000, 10, 56, 'goldbonus,8', 10);
INSERT INTO `$items` VALUES (118, 1, 'Orb of Greed', 10000, 15, 57, 'goldbonus,9', 15);
INSERT INTO `$items` VALUES (119, 4, 'Bronze Helm', 56, 0, 3, 'X', 0);
INSERT INTO `$items` VALUES (120, 4, 'Iron Helm', 120, 0, 12, 'maxap,1', 10);
INSERT INTO `$items` VALUES (121, 4, 'Magic Helm', 3000, 5, 30, 'maxap,3', 15);
INSERT INTO `$items` VALUES (122, 4, 'Dark Helm', 25000, 15, 50, 'maxap,5', 20);
INSERT INTO `$items` VALUES (123, 4, 'Bright Helm', 42000, 20, 65, 'maxap,7', 25);
INSERT INTO `$items` VALUES (124, 4, 'Destiny Helm', 60000, 40, 85, 'maxap,9', 35);
INSERT INTO `$items` VALUES (125, 4, 'Crystal Helm', 210000, 50, 105, 'maxap,12', 45);
INSERT INTO `$items` VALUES (126, 4, 'Diamond Helm', 530000, 60, 130, 'maxap,15', 55);
INSERT INTO `$items` VALUES (127, 4, 'Hero Full Helm', 2700000, 70, 155, 'maxap,18', 65);
INSERT INTO `$items` VALUES (128, 4, 'Holy Full Helm', 4120000, 90, 185, 'maxap,21', 75);
INSERT INTO `$items` VALUES (129, 4, 'Mythical Full Helm', 8120000, 105, 210, 'maxap,25', 85);
INSERT INTO `$items` VALUES (130, 4, 'Black Dragons Large Helm', 23500000, 120, 245, 'maxap,32', 95);
INSERT INTO `$items` VALUES (131, 5, 'Bronze Leg Armor', 400, 0, 12, 'X', 0);
INSERT INTO `$items` VALUES (132, 5, 'Iron Leg Armor', 2000, 0, 25, 'maxtp,1', 10);
INSERT INTO `$items` VALUES (133, 5, 'Magic Leg Armor', 4200, 5, 35, 'maxtp,2', 15);
INSERT INTO `$items` VALUES (134, 5, 'Dark Leg Armor', 32000, 15, 55, 'maxtp,3', 20);
INSERT INTO `$items` VALUES (135, 5, 'Bright Leg Armor', 50000, 20, 80, 'maxtp,4', 25);
INSERT INTO `$items` VALUES (136, 5, 'Destiny Leg Armor', 73000, 40, 95, 'maxtp,5', 35);
INSERT INTO `$items` VALUES (137, 5, 'Crystal Leg Armor', 310000, 50, 120, 'maxtp,6', 45);
INSERT INTO `$items` VALUES (138, 5, 'Diamond Leg Armor', 1100000, 60, 160, 'maxtp,7', 55);
INSERT INTO `$items` VALUES (139, 5, 'Heros Leg Armor', 3050000, 70, 190, 'maxtp,8', 65);
INSERT INTO `$items` VALUES (140, 5, 'Holy Leg Armor', 5100000, 90, 255, 'maxtp,9', 75);
INSERT INTO `$items` VALUES (141, 5, 'Mythical Leg Armor', 9100000, 105, 295, 'maxtp,10', 85);
INSERT INTO `$items` VALUES (142, 5, 'Black Dragons Leg Armor', 27250000, 120, 365, 'maxtp,15', 95);
INSERT INTO `$items` VALUES (143, 6, 'Leather Gloves', 134, 0, 12, 'maxmp,8', 0);
INSERT INTO `$items` VALUES (144, 6, 'Dark Gloves', 17790, 20, 45, 'maxmp,13', 15);
INSERT INTO `$items` VALUES (145, 6, 'Bright Gloves', 39050, 30, 70, 'maxmp,16', 25);
INSERT INTO `$items` VALUES (146, 6, 'Magic Gloves', 2680, 15, 30, 'maxmp,11', 10);
INSERT INTO `$items` VALUES (147, 6, 'Destiny Gauntlets', 68560, 40, 82, 'maxmp,20', 35);
INSERT INTO `$items` VALUES (148, 6, 'Crystal Gauntlets', 414500, 50, 95, 'maxmp,31', 45);
INSERT INTO `$items` VALUES (149, 6, 'Diamond Gauntlets', 1060050, 60, 106, 'maxmp,40', 55);
INSERT INTO `$items` VALUES (150, 6, 'Heros Gauntlets', 3287500, 70, 115, 'maxmp,46', 65);
INSERT INTO `$items` VALUES (151, 6, 'Holy Gauntlets', 5050000, 90, 132, 'maxmp,53', 75);
INSERT INTO `$items` VALUES (152, 6, 'Mythical Gauntlets', 9167500, 105, 155, 'maxmp,67', 85);
INSERT INTO `$items` VALUES (153, 6, 'Black Dragons Plated Gauntlets', 22700050, 120, 315, 'maxmp,235', 95);
INSERT INTO `$items` VALUES (154, 1, 'Adams Old Socks', 4294967295, 130, 65535, 'X', 1);

END;
if (dobatch($query) == 1) { echo "Items table populated.<br />"; } else { echo "Error populating Items table."; }
unset($query);

$query = <<<END
CREATE TABLE `$itemstorage` (
  `isid` int(11) NOT NULL auto_increment,
  `playerid` int(11) default NULL,
  `itemtype` int(1) default NULL,
  `itemid` int(11) default NULL,
  `location` int(1) default NULL,
  PRIMARY KEY  (`isid`)
) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Item Storage table created.<br />"; } else { echo "Error creating Item Storage table."; }
unset($query);

$query = <<<END
CREATE TABLE `$jewellery` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `type` tinyint(3) unsigned NOT NULL default '0',
  `name` varchar(30) NOT NULL default '',
  `buycost` int(10) unsigned NOT NULL default '0',
  `attribute` smallint(5) unsigned NOT NULL default '0',
  `requirement` smallint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Jewellery table created.<br />"; } else { echo "Error creating Jewellery table."; }
unset($query);

$query = <<<END
INSERT INTO `$jewellery` VALUES (1, 1, 'Sapphire Ring', 16000, 4, 0);
INSERT INTO `$jewellery` VALUES (2, 2, 'Sapphire Amulet', 16500, 5, 0);
INSERT INTO `$jewellery` VALUES (3, 1, 'Emerald Ring', 95400, 7, 15);
INSERT INTO `$jewellery` VALUES (4, 2, 'Emerald Amulet', 98500, 9, 15);
INSERT INTO `$jewellery` VALUES (5, 1, 'Ruby Ring', 420000, 12, 30);
INSERT INTO `$jewellery` VALUES (6, 2, 'Ruby Amulet', 427500, 14, 30);
INSERT INTO `$jewellery` VALUES (7, 1, 'Diamond Ring', 1300000, 17, 50);
INSERT INTO `$jewellery` VALUES (8, 2, 'Diamond Amulet', 1450000, 20, 50);
INSERT INTO `$jewellery` VALUES (9, 1, 'Black Dragons Ring', 2850000, 26, 75);
INSERT INTO `$jewellery` VALUES (10, 2, 'Black Dragons Amulet', 3000000, 30, 75);

END;
if (dobatch($query) == 1) { echo "Jewellery table populated.<br />"; } else { echo "Error populating Jewellery table."; }
unset($query);

$query = <<<END
CREATE TABLE `$levels` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `1_attributes` smallint(5) unsigned NOT NULL default '0',
  `2_attributes` smallint(5) unsigned NOT NULL default '0',
  `3_attributes` smallint(5) unsigned NOT NULL default '0',
  `4_attributes` smallint(5) unsigned NOT NULL default '0',
  `5_attributes` smallint(5) unsigned NOT NULL default '0',
  `6_attributes` smallint(5) unsigned NOT NULL default '0',
  `7_attributes` smallint(5) unsigned NOT NULL default '0',
  `1_exp` int(8) unsigned NOT NULL default '0',
  `1_hp` smallint(5) unsigned NOT NULL default '0',
  `1_mp` smallint(5) unsigned NOT NULL default '0',
  `1_tp` smallint(5) unsigned NOT NULL default '0',
  `1_strength` smallint(5) unsigned NOT NULL default '0',
  `1_dexterity` smallint(5) unsigned NOT NULL default '0',
  `1_spells` tinyint(3) unsigned NOT NULL default '0',
  `2_exp` int(8) unsigned NOT NULL default '0',
  `2_hp` smallint(5) unsigned NOT NULL default '0',
  `2_mp` smallint(5) unsigned NOT NULL default '0',
  `2_tp` smallint(5) unsigned NOT NULL default '0',
  `2_strength` smallint(5) unsigned NOT NULL default '0',
  `2_dexterity` smallint(5) unsigned NOT NULL default '0',
  `2_spells` tinyint(3) unsigned NOT NULL default '0',
  `3_exp` int(8) unsigned NOT NULL default '0',
  `3_hp` smallint(5) unsigned NOT NULL default '0',
  `3_mp` smallint(5) unsigned NOT NULL default '0',
  `3_tp` smallint(5) unsigned NOT NULL default '0',
  `3_strength` smallint(5) unsigned NOT NULL default '0',
  `3_dexterity` smallint(5) unsigned NOT NULL default '0',
  `3_spells` tinyint(3) unsigned NOT NULL default '0',
  `4_exp` int(8) unsigned NOT NULL default '0',
  `4_hp` smallint(5) unsigned NOT NULL default '0',
  `4_mp` smallint(5) unsigned NOT NULL default '0',
  `4_tp` smallint(5) unsigned NOT NULL default '0',
  `4_strength` smallint(5) unsigned NOT NULL default '0',
  `4_dexterity` smallint(5) unsigned NOT NULL default '0',
  `4_spells` tinyint(3) unsigned NOT NULL default '0',
  `5_exp` int(8) unsigned NOT NULL default '0',
  `5_hp` smallint(5) unsigned NOT NULL default '0',
  `5_mp` smallint(5) unsigned NOT NULL default '0',
  `5_tp` smallint(5) unsigned NOT NULL default '0',
  `5_strength` smallint(5) unsigned NOT NULL default '0',
  `5_dexterity` smallint(5) unsigned NOT NULL default '0',
  `5_spells` tinyint(3) unsigned NOT NULL default '0',
  `6_exp` int(8) unsigned NOT NULL default '0',
  `6_hp` smallint(5) unsigned NOT NULL default '0',
  `6_mp` smallint(5) unsigned NOT NULL default '0',
  `6_tp` smallint(5) unsigned NOT NULL default '0',
  `6_strength` smallint(5) unsigned NOT NULL default '0',
  `6_dexterity` smallint(5) unsigned NOT NULL default '0',
  `6_spells` tinyint(3) unsigned NOT NULL default '0',
  `7_exp` int(8) unsigned NOT NULL default '0',
  `7_hp` smallint(5) unsigned NOT NULL default '0',
  `7_mp` smallint(5) unsigned NOT NULL default '0',
  `7_tp` smallint(5) unsigned NOT NULL default '0',
  `7_strength` smallint(5) unsigned NOT NULL default '0',
  `7_dexterity` smallint(5) unsigned NOT NULL default '0',
  `7_spells` tinyint(3) unsigned NOT NULL default '0',
  `1_ap` smallint(5) unsigned NOT NULL default '1',
  `2_ap` smallint(5) unsigned NOT NULL default '1',
  `3_ap` smallint(5) unsigned NOT NULL default '1',
  `4_ap` smallint(5) unsigned NOT NULL default '1',
  `5_ap` smallint(5) unsigned NOT NULL default '1',
  `6_ap` smallint(5) unsigned NOT NULL default '1',
  `7_ap` smallint(5) unsigned NOT NULL default '1',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Levels table created.<br />"; } else { echo "Error creating Levels table."; }
unset($query);

$query = <<<END
INSERT INTO `$levels` VALUES (1, 0, 0, 0, 0, 0, 0, 0, 0, 15, 0, 0, 0, 0, 0, 0, 15, 0, 0, 0, 0, 0, 0, 15, 0, 0, 0, 0, 0, 0, 15, 0, 0, 0, 0, 0, 0, 15, 0, 0, 0, 0, 0, 0, 15, 0, 0, 0, 0, 0, 0, 15, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$levels` VALUES (2, 0, 0, 0, 0, 0, 0, 0, 15, 2, 10, 1, 0, 1, 1, 18, 2, 4, 1, 7, 1, 1, 20, 2, 5, 1, 0, 7, 1, 17, 2, 5, 2, 0, 2, 1, 16, 2, 10, 1, 0, 1, 1, 18, 2, 4, 1, 7, 1, 1, 20, 2, 5, 2, 7, 2, 1, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (3, 0, 0, 0, 0, 0, 0, 0, 45, 3, 4, 2, 1, 2, 0, 54, 2, 3, 2, 3, 2, 0, 60, 2, 3, 2, 1, 3, 0, 50, 2, 4, 2, 1, 3, 0, 45, 3, 4, 2, 1, 2, 0, 54, 2, 3, 2, 3, 2, 0, 60, 2, 4, 2, 3, 3, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (4, 0, 0, 0, 0, 0, 0, 0, 105, 3, 3, 2, 1, 2, 6, 126, 2, 3, 2, 3, 2, 0, 140, 2, 4, 2, 1, 3, 0, 118, 2, 3, 2, 2, 3, 0, 105, 3, 3, 2, 1, 2, 6, 126, 2, 3, 2, 3, 2, 0, 140, 2, 3, 2, 3, 3, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (5, 0, 0, 0, 0, 0, 0, 0, 195, 2, 5, 2, 0, 1, 0, 234, 2, 4, 2, 2, 1, 6, 260, 2, 4, 2, 0, 2, 6, 225, 2, 4, 2, 0, 2, 0, 195, 2, 5, 2, 0, 1, 0, 234, 2, 4, 2, 2, 1, 43, 260, 2, 4, 2, 2, 2, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (6, 0, 0, 0, 0, 0, 0, 0, 330, 4, 5, 2, 2, 3, 0, 396, 3, 4, 2, 4, 3, 0, 440, 3, 5, 2, 2, 4, 0, 372, 3, 5, 2, 2, 4, 22, 330, 4, 5, 2, 2, 3, 0, 396, 3, 4, 2, 4, 3, 0, 440, 3, 5, 2, 4, 4, 51, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (7, 0, 0, 0, 0, 0, 0, 0, 532, 3, 4, 2, 1, 2, 11, 639, 2, 3, 2, 3, 2, 0, 710, 2, 3, 2, 1, 3, 0, 601, 2, 4, 2, 0, 4, 0, 532, 3, 4, 2, 1, 2, 11, 639, 2, 3, 2, 3, 2, 0, 710, 2, 4, 2, 3, 4, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (8, 0, 0, 0, 0, 0, 0, 0, 835, 2, 4, 2, 0, 1, 0, 1003, 2, 3, 2, 2, 1, 11, 1115, 2, 4, 2, 0, 2, 11, 920, 2, 4, 2, 0, 2, 11, 835, 2, 4, 2, 0, 1, 0, 1003, 2, 3, 2, 2, 1, 0, 1115, 2, 4, 2, 2, 2, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (9, 0, 0, 0, 0, 0, 0, 0, 1290, 5, 3, 2, 3, 4, 2, 1549, 4, 2, 2, 5, 4, 0, 1722, 4, 2, 2, 3, 5, 0, 1432, 3, 3, 2, 2, 7, 0, 1290, 5, 3, 2, 3, 4, 0, 1549, 3, 2, 2, 5, 4, 0, 1722, 3, 3, 2, 5, 7, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (10, 10, 13, 14, 12, 10, 13, 14, 1973, 10, 3, 2, 4, 3, 0, 2369, 10, 2, 2, 6, 3, 0, 2633, 10, 3, 2, 4, 4, 0, 2194, 10, 3, 2, 4, 4, 2, 1973, 10, 3, 2, 4, 3, 0, 2369, 10, 2, 2, 6, 3, 0, 2633, 10, 3, 2, 6, 4, 2, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (11, 0, 0, 0, 0, 0, 0, 0, 2997, 5, 2, 2, 3, 4, 0, 3598, 4, 1, 2, 5, 4, 2, 3999, 4, 1, 2, 3, 5, 2, 3287, 4, 2, 2, 3, 4, 0, 2997, 5, 2, 2, 3, 4, 0, 3598, 4, 1, 2, 5, 4, 48, 3999, 4, 2, 2, 5, 4, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (12, 0, 0, 0, 0, 0, 0, 0, 4533, 4, 2, 2, 2, 3, 7, 5441, 4, 1, 2, 4, 3, 0, 6047, 4, 2, 2, 2, 4, 0, 4992, 4, 2, 2, 2, 4, 0, 4533, 4, 2, 2, 2, 3, 7, 5441, 4, 1, 2, 4, 3, 0, 6047, 4, 2, 2, 4, 4, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (13, 0, 0, 0, 0, 0, 0, 0, 6453, 4, 3, 2, 2, 3, 0, 7745, 4, 2, 2, 4, 3, 0, 8607, 4, 2, 2, 2, 4, 0, 7098, 4, 3, 2, 2, 4, 0, 6453, 4, 3, 2, 2, 3, 0, 7745, 4, 2, 2, 4, 3, 0, 8607, 4, 3, 2, 4, 4, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (14, 0, 0, 0, 0, 0, 0, 0, 8853, 5, 4, 2, 3, 4, 17, 10625, 4, 3, 2, 5, 4, 7, 11807, 4, 4, 2, 3, 5, 7, 9701, 3, 4, 2, 3, 6, 0, 8853, 5, 4, 2, 3, 4, 17, 10625, 3, 3, 2, 5, 4, 46, 11807, 3, 4, 2, 5, 6, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (15, 0, 0, 0, 0, 0, 0, 0, 11853, 5, 5, 2, 3, 4, 0, 14225, 4, 4, 2, 5, 4, 0, 15808, 4, 4, 2, 3, 5, 0, 13426, 4, 5, 2, 3, 5, 23, 11853, 5, 5, 2, 3, 4, 0, 14225, 4, 4, 2, 5, 4, 0, 15808, 4, 5, 2, 5, 5, 52, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (16, 0, 0, 0, 0, 0, 0, 0, 15603, 5, 3, 2, 3, 4, 0, 18725, 5, 2, 2, 5, 4, 0, 20807, 5, 3, 2, 3, 5, 0, 17035, 4, 3, 2, 3, 6, 0, 15603, 5, 3, 2, 3, 4, 0, 18725, 4, 2, 2, 5, 4, 0, 20807, 4, 3, 2, 5, 6, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (17, 0, 0, 0, 0, 0, 0, 0, 20290, 4, 2, 2, 2, 3, 12, 24350, 4, 1, 2, 4, 3, 0, 27057, 4, 1, 2, 2, 4, 0, 22328, 4, 2, 2, 2, 4, 0, 20290, 4, 2, 2, 2, 3, 12, 24350, 4, 1, 2, 4, 3, 0, 27057, 4, 2, 2, 4, 4, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (18, 0, 0, 0, 0, 0, 0, 0, 25563, 4, 2, 2, 2, 3, 0, 30678, 3, 1, 2, 4, 3, 14, 34869, 3, 2, 2, 2, 4, 17, 28759, 3, 2, 2, 1, 5, 0, 25563, 4, 2, 2, 2, 3, 0, 30678, 3, 1, 2, 4, 3, 44, 34869, 3, 2, 2, 4, 5, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (19, 0, 0, 0, 0, 0, 0, 0, 31495, 4, 5, 2, 2, 3, 0, 37797, 3, 4, 2, 4, 3, 0, 43657, 3, 4, 2, 2, 4, 0, 34647, 3, 5, 2, 2, 4, 0, 31495, 4, 5, 2, 2, 3, 0, 37797, 3, 4, 2, 4, 3, 0, 43657, 3, 5, 2, 4, 4, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (20, 12, 15, 16, 14, 12, 15, 16, 38169, 10, 6, 2, 3, 3, 0, 45805, 10, 5, 2, 5, 3, 0, 53543, 10, 6, 2, 3, 4, 0, 42367, 10, 6, 2, 3, 5, 0, 38169, 10, 6, 2, 3, 3, 0, 45805, 10, 5, 2, 5, 3, 0, 53543, 10, 6, 2, 5, 5, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (21, 0, 0, 0, 0, 0, 0, 0, 45676, 4, 4, 2, 2, 3, 0, 54814, 4, 3, 2, 4, 3, 0, 64664, 4, 3, 2, 2, 4, 0, 49982, 4, 4, 2, 2, 4, 0, 45676, 4, 4, 2, 2, 3, 0, 54814, 4, 3, 2, 4, 3, 0, 64664, 4, 4, 2, 4, 4, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (22, 0, 0, 0, 0, 0, 0, 0, 54121, 5, 5, 2, 3, 4, 0, 64949, 4, 4, 2, 5, 4, 12, 77175, 4, 5, 2, 3, 5, 12, 60100, 4, 5, 2, 3, 6, 0, 54121, 5, 5, 2, 3, 4, 0, 64949, 4, 4, 2, 5, 4, 0, 77175, 4, 5, 2, 5, 6, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (23, 0, 0, 0, 0, 0, 0, 0, 63622, 5, 3, 2, 3, 4, 0, 76350, 4, 2, 2, 5, 4, 0, 91250, 4, 2, 2, 3, 5, 0, 69950, 4, 3, 2, 3, 5, 0, 63622, 5, 3, 2, 3, 4, 0, 76350, 4, 2, 2, 5, 4, 0, 91250, 4, 3, 2, 5, 5, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (24, 0, 0, 0, 0, 0, 0, 0, 74310, 5, 5, 2, 3, 4, 0, 89176, 4, 4, 2, 5, 4, 0, 107083, 4, 5, 2, 3, 5, 0, 82113, 3, 5, 2, 3, 6, 0, 74310, 5, 5, 2, 3, 4, 0, 89176, 3, 4, 2, 5, 4, 0, 107083, 3, 5, 2, 5, 6, 53, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (25, 0, 0, 0, 0, 0, 0, 0, 86334, 4, 4, 2, 2, 3, 3, 103605, 3, 3, 2, 4, 3, 17, 124895, 3, 3, 2, 2, 4, 14, 99754, 3, 3, 2, 3, 4, 0, 86334, 4, 4, 2, 2, 3, 0, 103605, 3, 3, 2, 4, 3, 13, 124895, 3, 3, 2, 4, 4, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (26, 0, 0, 0, 0, 0, 0, 0, 99861, 6, 3, 2, 4, 5, 0, 119837, 5, 2, 2, 6, 5, 0, 144933, 5, 3, 2, 4, 6, 0, 109893, 5, 4, 2, 3, 6, 0, 99861, 6, 3, 2, 4, 5, 0, 119837, 5, 2, 2, 6, 5, 0, 144933, 5, 4, 2, 6, 6, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (27, 0, 0, 0, 0, 0, 0, 0, 115078, 6, 2, 2, 4, 5, 0, 138098, 5, 1, 2, 6, 5, 0, 167475, 5, 1, 2, 4, 6, 0, 126588, 4, 2, 2, 4, 6, 0, 115078, 6, 2, 2, 4, 5, 0, 138098, 4, 1, 2, 6, 5, 0, 167475, 4, 2, 2, 6, 6, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (28, 0, 0, 0, 0, 0, 0, 0, 132197, 4, 2, 2, 2, 3, 0, 158641, 4, 1, 2, 4, 3, 0, 192835, 4, 2, 2, 2, 4, 0, 145419, 4, 2, 2, 2, 4, 0, 132197, 4, 2, 2, 2, 3, 0, 158641, 4, 1, 2, 4, 3, 0, 192835, 4, 2, 2, 4, 4, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (29, 0, 0, 0, 0, 0, 0, 0, 151456, 6, 3, 2, 4, 5, 0, 181751, 5, 2, 2, 6, 5, 3, 221365, 5, 2, 2, 4, 6, 3, 166604, 5, 3, 2, 4, 6, 0, 151456, 6, 3, 2, 4, 5, 33, 181751, 5, 2, 2, 6, 5, 3, 221365, 5, 3, 2, 6, 6, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (30, 14, 17, 18, 16, 14, 17, 18, 248929, 10, 4, 3, 4, 3, 0, 314723, 10, 3, 3, 6, 4, 0, 401575, 10, 4, 3, 4, 5, 0, 281826, 10, 4, 3, 4, 5, 0, 248929, 10, 4, 3, 4, 3, 0, 314723, 10, 3, 3, 6, 4, 0, 401575, 10, 4, 3, 6, 5, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (31, 0, 0, 0, 0, 0, 0, 0, 375238, 5, 5, 3, 3, 4, 8, 450292, 4, 3, 3, 5, 4, 0, 550179, 4, 3, 3, 3, 5, 0, 412765, 3, 5, 3, 3, 5, 0, 375238, 5, 5, 3, 3, 4, 0, 450292, 3, 3, 3, 5, 4, 0, 550179, 3, 5, 3, 5, 5, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (32, 0, 0, 0, 0, 0, 0, 0, 427334, 6, 4, 3, 4, 5, 0, 512806, 5, 3, 3, 6, 5, 0, 627357, 5, 4, 3, 4, 6, 0, 470070, 5, 4, 3, 4, 6, 0, 427334, 6, 4, 3, 4, 5, 0, 512806, 5, 3, 3, 6, 5, 0, 627357, 5, 4, 3, 6, 6, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (33, 0, 0, 0, 0, 0, 0, 0, 485940, 5, 4, 3, 3, 4, 0, 583132, 5, 3, 3, 5, 4, 0, 714181, 5, 3, 3, 3, 5, 0, 534536, 5, 4, 3, 3, 5, 0, 485940, 5, 4, 3, 3, 4, 0, 583132, 5, 3, 3, 5, 4, 0, 714181, 5, 4, 3, 5, 5, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (34, 0, 0, 0, 0, 0, 0, 0, 551870, 6, 4, 3, 4, 5, 0, 662248, 5, 3, 3, 6, 5, 8, 811858, 5, 4, 3, 4, 6, 8, 607059, 5, 4, 3, 4, 7, 0, 551870, 6, 4, 3, 4, 5, 0, 662248, 5, 3, 3, 6, 5, 0, 811858, 5, 4, 3, 6, 7, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (35, 0, 0, 0, 0, 0, 0, 0, 626040, 5, 3, 3, 3, 4, 0, 751254, 4, 2, 3, 5, 4, 0, 921739, 4, 2, 3, 3, 5, 0, 688647, 4, 3, 3, 3, 5, 24, 626040, 5, 3, 3, 3, 4, 0, 751254, 4, 2, 3, 5, 4, 0, 921739, 4, 3, 3, 5, 5, 56, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (36, 0, 0, 0, 0, 0, 0, 0, 709482, 4, 3, 3, 2, 3, 18, 851384, 5, 2, 3, 4, 3, 0, 1045357, 5, 3, 3, 2, 4, 0, 780433, 4, 3, 3, 2, 4, 0, 709482, 4, 3, 3, 2, 3, 0, 851384, 4, 2, 3, 4, 3, 0, 1045357, 4, 3, 3, 4, 4, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (37, 0, 0, 0, 0, 0, 0, 0, 803354, 5, 4, 3, 3, 4, 0, 964029, 5, 3, 3, 5, 4, 0, 1184427, 5, 3, 3, 3, 5, 0, 883692, 5, 4, 3, 3, 5, 0, 803354, 5, 4, 3, 3, 4, 0, 964029, 5, 3, 3, 5, 4, 0, 1184427, 5, 4, 3, 5, 5, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (38, 0, 0, 0, 0, 0, 0, 0, 908958, 6, 5, 3, 4, 5, 0, 1090753, 5, 4, 3, 6, 5, 15, 1340879, 5, 5, 3, 4, 6, 18, 999856, 5, 5, 3, 4, 6, 0, 908958, 6, 5, 3, 4, 5, 0, 1090753, 5, 4, 3, 6, 5, 47, 1340879, 5, 5, 3, 6, 6, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (39, 0, 0, 0, 0, 0, 0, 0, 1027761, 6, 4, 3, 4, 5, 0, 1233318, 5, 3, 3, 6, 5, 0, 1516887, 5, 3, 3, 4, 6, 0, 1130540, 5, 4, 3, 4, 6, 0, 1027761, 6, 4, 3, 4, 5, 0, 1233318, 5, 3, 3, 6, 5, 0, 1516887, 5, 4, 3, 6, 6, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (40, 16, 18, 20, 18, 16, 19, 20, 1161414, 15, 3, 3, 5, 5, 13, 1393703, 15, 2, 3, 7, 5, 0, 1714896, 15, 3, 3, 5, 6, 0, 1277559, 15, 3, 3, 5, 6, 0, 1161414, 15, 3, 3, 5, 5, 35, 1393703, 15, 2, 3, 7, 5, 0, 1714896, 15, 3, 3, 7, 6, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (41, 0, 0, 0, 0, 0, 0, 0, 1311775, 7, 3, 3, 5, 2, 0, 1574134, 6, 2, 3, 7, 2, 0, 1937654, 6, 2, 3, 5, 3, 0, 1442955, 5, 3, 3, 3, 5, 0, 1311775, 7, 3, 3, 5, 2, 0, 1574134, 5, 2, 3, 7, 2, 0, 1937654, 5, 3, 3, 7, 5, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (42, 0, 0, 0, 0, 0, 0, 0, 1480930, 7, 4, 3, 5, 6, 0, 1777119, 6, 3, 3, 7, 6, 0, 2188256, 6, 4, 3, 5, 7, 0, 1629025, 6, 4, 3, 5, 7, 0, 1480930, 7, 4, 3, 5, 6, 0, 1777119, 6, 3, 3, 7, 6, 0, 2188256, 6, 4, 3, 7, 7, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (43, 0, 0, 0, 0, 0, 0, 0, 1671224, 8, 5, 3, 6, 7, 0, 2005476, 7, 4, 3, 8, 7, 0, 2470182, 7, 4, 3, 6, 8, 0, 1838350, 7, 5, 3, 6, 7, 0, 1671224, 8, 5, 3, 6, 7, 0, 2005476, 7, 4, 3, 8, 7, 0, 2470182, 7, 5, 3, 8, 7, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (44, 0, 0, 0, 0, 0, 0, 0, 1885309, 6, 3, 3, 4, 5, 0, 2262377, 5, 2, 3, 6, 5, 0, 2752108, 5, 3, 3, 4, 6, 0, 2073843, 5, 3, 3, 4, 6, 0, 1885309, 6, 3, 3, 4, 5, 0, 2262377, 5, 2, 3, 6, 5, 0, 2752108, 5, 3, 3, 6, 6, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (45, 0, 0, 0, 0, 0, 0, 0, 2126153, 5, 8, 3, 3, 4, 4, 2519278, 5, 8, 3, 5, 4, 37, 3034034, 5, 8, 3, 3, 5, 41, 2322716, 5, 8, 3, 3, 6, 0, 2126153, 5, 8, 3, 3, 4, 0, 2519278, 5, 8, 3, 5, 4, 0, 3034034, 5, 8, 3, 5, 6, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (46, 0, 0, 0, 0, 0, 0, 0, 2366997, 6, 5, 3, 4, 5, 0, 2776179, 5, 4, 3, 6, 5, 0, 3315959, 5, 5, 3, 4, 6, 0, 2571588, 5, 5, 3, 4, 6, 0, 2366997, 6, 5, 3, 4, 5, 0, 2776179, 5, 4, 3, 6, 5, 0, 3315959, 5, 5, 3, 6, 6, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (47, 0, 0, 0, 0, 0, 0, 0, 2607841, 7, 4, 3, 5, 6, 0, 3033080, 6, 3, 3, 7, 6, 0, 3597885, 6, 3, 3, 5, 7, 0, 2820461, 6, 4, 3, 4, 7, 0, 2607841, 7, 4, 3, 5, 6, 0, 3033080, 6, 3, 3, 7, 6, 0, 3597885, 6, 4, 3, 7, 7, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (48, 0, 0, 0, 0, 0, 0, 0, 2848685, 6, 4, 3, 4, 5, 0, 3289981, 5, 3, 3, 6, 5, 0, 3879811, 5, 4, 3, 4, 6, 0, 3069333, 4, 4, 3, 4, 7, 0, 2848685, 6, 4, 3, 4, 5, 0, 3289981, 4, 3, 3, 6, 5, 0, 3879811, 4, 4, 3, 6, 7, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (49, 0, 0, 0, 0, 0, 0, 0, 3089529, 5, 3, 3, 3, 4, 0, 3546882, 4, 2, 3, 5, 4, 0, 4161737, 4, 2, 3, 3, 5, 0, 3318206, 4, 3, 3, 3, 5, 0, 3089529, 5, 3, 3, 3, 4, 0, 3546882, 4, 2, 3, 5, 4, 0, 4161737, 4, 3, 3, 5, 5, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (50, 18, 20, 22, 20, 18, 21, 22, 3330373, 15, 3, 3, 5, 5, 0, 3803781, 15, 2, 3, 7, 5, 0, 4443663, 15, 3, 3, 5, 6, 0, 3567077, 15, 3, 3, 5, 6, 25, 3330373, 15, 3, 3, 5, 5, 0, 3803781, 15, 2, 3, 7, 5, 0, 4443663, 15, 3, 3, 7, 6, 58, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (51, 0, 0, 0, 0, 0, 0, 0, 3571217, 6, 2, 3, 4, 5, 9, 4060683, 5, 1, 3, 6, 5, 40, 4725588, 5, 1, 3, 4, 6, 13, 3815950, 5, 2, 3, 4, 6, 0, 3571217, 6, 2, 3, 4, 5, 9, 4060683, 5, 1, 3, 6, 5, 0, 4725588, 5, 2, 3, 6, 6, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (52, 0, 0, 0, 0, 0, 0, 0, 3812061, 7, 2, 3, 5, 6, 0, 4317584, 6, 1, 3, 7, 6, 0, 5007514, 6, 2, 3, 5, 7, 0, 4064823, 6, 2, 3, 5, 7, 0, 3812061, 7, 2, 3, 5, 6, 0, 4317584, 6, 1, 3, 7, 6, 0, 5007514, 6, 2, 3, 7, 7, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (53, 0, 0, 0, 0, 0, 0, 0, 4052905, 8, 2, 3, 6, 7, 0, 4574485, 7, 1, 3, 8, 7, 0, 5289440, 7, 1, 3, 6, 8, 0, 4313695, 6, 3, 3, 5, 8, 0, 4052905, 8, 2, 3, 6, 7, 0, 4574485, 6, 1, 3, 8, 7, 0, 5289440, 6, 3, 3, 8, 8, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (54, 0, 0, 0, 0, 0, 0, 0, 4293749, 8, 4, 3, 6, 7, 0, 4831386, 7, 3, 3, 8, 7, 0, 5571366, 7, 4, 3, 6, 8, 0, 4562568, 7, 4, 3, 5, 8, 0, 4293749, 8, 4, 3, 6, 7, 0, 4831386, 7, 3, 3, 8, 7, 0, 5571366, 7, 4, 3, 8, 8, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (55, 0, 0, 0, 0, 0, 0, 0, 4534593, 7, 4, 3, 5, 6, 0, 5088287, 6, 3, 3, 7, 6, 0, 5853292, 6, 3, 3, 5, 7, 0, 4811440, 6, 4, 3, 5, 7, 0, 4534593, 7, 4, 3, 5, 6, 0, 5088287, 6, 3, 3, 7, 6, 0, 5853292, 6, 4, 3, 7, 7, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (56, 0, 0, 0, 0, 0, 0, 0, 4775437, 7, 4, 3, 5, 6, 0, 5345188, 6, 3, 3, 7, 6, 0, 6135217, 6, 4, 3, 5, 7, 9, 5060313, 6, 4, 3, 5, 7, 0, 4775437, 7, 4, 3, 5, 6, 0, 5345188, 6, 3, 3, 7, 6, 0, 6135217, 6, 4, 3, 7, 7, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (57, 0, 0, 0, 0, 0, 0, 0, 5016281, 6, 5, 3, 4, 5, 0, 5602089, 6, 4, 3, 6, 5, 0, 6417143, 6, 4, 3, 4, 6, 0, 5309185, 6, 5, 3, 4, 6, 29, 5016281, 6, 5, 3, 4, 5, 0, 5602089, 6, 4, 3, 6, 5, 0, 6417143, 6, 5, 3, 6, 6, 54, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (58, 0, 0, 0, 0, 0, 0, 0, 5257125, 5, 5, 3, 3, 4, 0, 5858990, 5, 4, 3, 5, 4, 38, 6699069, 5, 5, 3, 3, 5, 0, 5558058, 5, 5, 3, 3, 5, 0, 5257125, 5, 5, 3, 3, 4, 0, 5858990, 5, 4, 3, 5, 4, 49, 6699069, 5, 5, 3, 5, 5, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (59, 0, 0, 0, 0, 0, 0, 0, 5497969, 8, 3, 3, 6, 7, 0, 6115891, 7, 2, 3, 8, 7, 0, 6980995, 7, 2, 3, 6, 8, 0, 5806930, 7, 3, 3, 5, 8, 0, 5497969, 8, 3, 3, 6, 7, 0, 6115891, 7, 2, 3, 8, 7, 0, 6980995, 7, 3, 3, 8, 8, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (60, 20, 22, 24, 22, 20, 23, 24, 6238813, 15, 4, 4, 6, 6, 19, 7004904, 15, 3, 4, 8, 6, 0, 8018177, 15, 4, 4, 6, 7, 42, 6621858, 15, 4, 4, 5, 8, 0, 6238813, 15, 4, 4, 6, 6, 34, 7004904, 15, 3, 4, 8, 6, 0, 8018177, 15, 4, 4, 8, 8, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (61, 0, 0, 0, 0, 0, 0, 0, 6788813, 8, 5, 4, 6, 7, 0, 7624904, 7, 4, 4, 8, 7, 0, 8788177, 7, 4, 4, 6, 8, 0, 7226858, 7, 6, 4, 5, 8, 0, 6788813, 8, 5, 4, 6, 7, 0, 7624904, 7, 4, 4, 8, 7, 0, 8788177, 7, 6, 4, 8, 8, 57, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (62, 0, 0, 0, 0, 0, 0, 0, 7388813, 8, 4, 4, 6, 7, 0, 8384904, 7, 3, 4, 8, 7, 0, 9628177, 7, 4, 4, 6, 8, 0, 7886848, 7, 5, 4, 5, 8, 0, 7388813, 8, 4, 4, 6, 7, 0, 8384904, 7, 3, 4, 8, 7, 0, 9628177, 7, 5, 4, 8, 8, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (63, 0, 0, 0, 0, 0, 0, 0, 8038813, 9, 5, 4, 7, 8, 0, 9164904, 8, 4, 4, 9, 8, 0, 10538177, 8, 4, 4, 7, 9, 0, 8601858, 8, 5, 4, 6, 10, 0, 8038813, 9, 5, 4, 7, 8, 0, 9164904, 8, 4, 4, 9, 8, 0, 10538177, 8, 5, 4, 9, 10, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (64, 0, 0, 0, 0, 0, 0, 0, 8738813, 5, 5, 4, 3, 4, 0, 10004904, 5, 4, 4, 5, 4, 0, 11518177, 5, 5, 4, 3, 5, 0, 9371858, 5, 5, 4, 3, 6, 0, 8738813, 5, 5, 4, 3, 4, 0, 10004904, 5, 4, 4, 5, 4, 0, 11518177, 5, 5, 4, 5, 6, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (65, 0, 0, 0, 0, 0, 0, 0, 9488813, 6, 4, 4, 4, 5, 0, 10904904, 6, 3, 4, 6, 5, 0, 12568177, 6, 3, 4, 4, 6, 0, 10196858, 6, 4, 4, 3, 6, 0, 9488813, 6, 4, 4, 4, 5, 0, 10904904, 6, 3, 4, 6, 5, 0, 12568177, 6, 4, 4, 6, 6, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (66, 0, 0, 0, 0, 0, 0, 0, 10288813, 8, 4, 4, 6, 7, 0, 11864904, 8, 3, 4, 8, 7, 0, 13688177, 8, 4, 4, 6, 8, 0, 11076848, 8, 4, 4, 4, 10, 0, 10288813, 8, 4, 4, 6, 7, 4, 11864904, 8, 3, 4, 8, 7, 0, 13688177, 8, 4, 4, 8, 10, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (67, 0, 0, 0, 0, 0, 0, 0, 11138813, 7, 3, 4, 5, 6, 0, 12884904, 7, 2, 4, 7, 6, 0, 14878177, 7, 2, 4, 5, 7, 0, 12011858, 7, 3, 4, 5, 7, 0, 11138813, 7, 3, 4, 5, 6, 0, 12884904, 7, 2, 4, 7, 6, 0, 14878177, 7, 3, 4, 7, 7, 55, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (68, 0, 0, 0, 0, 0, 0, 0, 12038813, 9, 3, 4, 7, 8, 0, 13964904, 8, 2, 4, 9, 8, 0, 16138177, 8, 3, 4, 7, 9, 0, 13001858, 8, 3, 4, 8, 8, 0, 12038813, 9, 3, 4, 7, 8, 0, 13964904, 8, 2, 4, 9, 8, 0, 16138177, 8, 3, 4, 9, 8, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (69, 0, 0, 0, 0, 0, 0, 0, 12988813, 5, 4, 4, 3, 4, 0, 15104904, 5, 3, 4, 5, 4, 0, 18868177, 5, 3, 4, 3, 5, 0, 14046858, 5, 4, 4, 3, 5, 0, 12988813, 5, 4, 4, 3, 4, 0, 15104904, 5, 3, 4, 5, 4, 0, 18868177, 5, 4, 4, 5, 5, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (70, 22, 24, 26, 24, 22, 25, 26, 13988813, 20, 4, 4, 6, 6, 5, 16304904, 20, 3, 4, 8, 6, 39, 20408177, 20, 4, 4, 6, 7, 0, 15146858, 20, 4, 4, 6, 7, 30, 13988813, 20, 4, 4, 6, 6, 0, 16304904, 20, 3, 4, 8, 6, 41, 20408177, 20, 4, 4, 8, 7, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (71, 0, 0, 0, 0, 0, 0, 0, 15088813, 5, 5, 4, 3, 4, 0, 17624904, 5, 4, 4, 5, 4, 0, 22088177, 5, 4, 4, 3, 5, 0, 16356858, 5, 5, 4, 3, 5, 0, 15088813, 5, 5, 4, 3, 4, 36, 17624904, 5, 4, 4, 5, 4, 0, 22088177, 5, 5, 4, 5, 5, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (72, 0, 0, 0, 0, 0, 0, 0, 16288813, 6, 2, 4, 4, 5, 0, 19064904, 5, 1, 4, 6, 5, 0, 23908177, 5, 2, 4, 4, 6, 0, 17676858, 5, 2, 4, 4, 6, 0, 16288813, 6, 2, 4, 4, 5, 0, 19064904, 5, 1, 4, 6, 5, 0, 23908177, 5, 2, 4, 6, 6, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (73, 0, 0, 0, 0, 0, 0, 0, 17588813, 8, 4, 4, 6, 7, 0, 20624904, 8, 3, 4, 8, 7, 0, 25868177, 8, 3, 4, 6, 8, 0, 19106858, 8, 4, 4, 6, 8, 0, 17588813, 8, 4, 4, 6, 7, 0, 20624904, 8, 3, 4, 8, 7, 0, 25868177, 8, 4, 4, 8, 8, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (74, 0, 0, 0, 0, 0, 0, 0, 18988813, 7, 5, 4, 5, 6, 0, 22303904, 6, 4, 4, 7, 6, 0, 26668177, 6, 5, 4, 5, 7, 0, 20646858, 6, 5, 4, 5, 7, 0, 18988813, 7, 5, 4, 5, 6, 0, 22303904, 6, 4, 4, 7, 6, 0, 26668177, 6, 5, 4, 7, 7, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (75, 0, 0, 0, 0, 0, 0, 0, 20588813, 5, 3, 4, 3, 4, 0, 24104904, 5, 2, 4, 5, 4, 0, 27968177, 5, 2, 4, 3, 5, 0, 22296858, 5, 3, 4, 3, 5, 27, 20588813, 5, 3, 4, 3, 4, 0, 24104904, 5, 2, 4, 5, 4, 0, 27968177, 5, 3, 4, 5, 5, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (76, 0, 0, 0, 0, 0, 0, 0, 22288813, 6, 3, 4, 4, 5, 0, 26024904, 6, 2, 4, 6, 5, 0, 30208177, 6, 3, 4, 4, 6, 0, 24056858, 5, 3, 4, 6, 5, 0, 22288813, 6, 3, 4, 4, 5, 0, 26024904, 5, 2, 4, 6, 5, 0, 30208177, 5, 3, 4, 6, 5, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (77, 0, 0, 0, 0, 0, 0, 0, 23088813, 6, 4, 4, 4, 5, 0, 28064904, 7, 3, 4, 6, 5, 0, 32588177, 7, 3, 4, 4, 6, 0, 25926858, 7, 3, 4, 5, 5, 0, 23088813, 6, 4, 4, 4, 5, 0, 28064904, 7, 3, 4, 6, 5, 0, 32588177, 7, 3, 4, 6, 5, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (78, 0, 0, 0, 0, 0, 0, 0, 25888813, 7, 4, 4, 5, 6, 0, 30224904, 7, 3, 4, 7, 6, 0, 37768177, 7, 4, 4, 5, 7, 0, 27906858, 7, 5, 4, 6, 5, 0, 25888813, 7, 4, 4, 5, 6, 0, 30224904, 7, 3, 4, 7, 6, 0, 37768177, 7, 5, 4, 7, 5, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (79, 0, 0, 0, 0, 0, 0, 0, 27788813, 8, 4, 4, 6, 7, 10, 32504904, 7, 3, 4, 8, 7, 0, 39168177, 7, 3, 4, 6, 8, 0, 29996858, 6, 4, 4, 6, 8, 0, 27788813, 8, 4, 4, 6, 7, 32, 32504904, 6, 3, 4, 8, 7, 45, 39168177, 6, 4, 4, 8, 8, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (80, 24, 26, 28, 26, 24, 27, 28, 29788813, 20, 5, 4, 6, 7, 0, 34904904, 20, 4, 4, 8, 7, 0, 40568177, 20, 5, 4, 6, 8, 0, 32196858, 20, 5, 4, 4, 10, 26, 29788813, 20, 5, 4, 6, 7, 0, 34904904, 20, 4, 4, 8, 7, 0, 40568177, 20, 5, 4, 8, 10, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (81, 0, 0, 0, 0, 0, 0, 0, 31688813, 7, 3, 4, 5, 6, 0, 37544904, 7, 2, 4, 7, 6, 0, 43648177, 7, 2, 4, 5, 7, 0, 34616858, 7, 4, 4, 4, 7, 0, 31688813, 7, 3, 4, 5, 6, 0, 37544904, 7, 2, 4, 7, 6, 0, 43648177, 7, 4, 4, 7, 7, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (82, 0, 0, 0, 0, 0, 0, 0, 34088813, 6, 4, 4, 4, 5, 0, 40424904, 5, 3, 4, 6, 5, 0, 47008177, 5, 4, 4, 4, 6, 0, 37256858, 4, 5, 4, 4, 6, 0, 34088813, 6, 4, 4, 4, 5, 0, 40424904, 4, 3, 4, 6, 5, 0, 47008177, 4, 5, 4, 6, 6, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (83, 0, 0, 0, 0, 0, 0, 0, 36688813, 6, 2, 4, 4, 5, 0, 43544904, 6, 1, 4, 6, 5, 0, 50648177, 6, 1, 4, 4, 6, 0, 40116858, 6, 2, 4, 3, 6, 0, 36688813, 6, 2, 4, 4, 5, 0, 43544904, 6, 1, 4, 6, 5, 0, 50648177, 6, 2, 4, 6, 6, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (84, 0, 0, 0, 0, 0, 0, 0, 39488813, 5, 4, 4, 3, 4, 0, 46904904, 5, 3, 4, 5, 4, 0, 54568177, 5, 4, 4, 3, 5, 0, 43196858, 5, 5, 4, 3, 4, 0, 39488813, 5, 4, 4, 3, 4, 0, 46904904, 5, 3, 4, 5, 4, 0, 54568177, 5, 5, 4, 5, 4, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (85, 0, 0, 0, 0, 0, 0, 0, 42488813, 7, 4, 4, 5, 6, 0, 50504904, 6, 3, 4, 7, 6, 0, 58768177, 6, 3, 4, 5, 7, 0, 46496858, 4, 6, 4, 4, 7, 0, 42488813, 7, 4, 4, 5, 6, 0, 50504904, 4, 3, 4, 7, 6, 0, 58768177, 4, 6, 4, 7, 7, 59, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (86, 0, 0, 0, 0, 0, 0, 0, 45688813, 8, 5, 4, 6, 7, 0, 54344904, 8, 4, 4, 8, 7, 0, 63248177, 8, 5, 4, 6, 8, 0, 50016858, 8, 6, 4, 5, 8, 0, 45688813, 8, 5, 4, 6, 7, 0, 54344904, 8, 4, 4, 8, 7, 0, 63248177, 8, 6, 4, 8, 8, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (87, 0, 0, 0, 0, 0, 0, 0, 49088813, 8, 10, 4, 6, 7, 20, 58424904, 7, 3, 4, 8, 7, 0, 68008177, 7, 3, 4, 6, 8, 0, 53756858, 6, 8, 4, 7, 10, 0, 49088813, 8, 10, 4, 6, 7, 0, 58424904, 6, 3, 4, 8, 7, 0, 68008177, 6, 8, 4, 8, 10, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (88, 0, 0, 0, 0, 0, 0, 0, 52688813, 9, 5, 4, 7, 8, 0, 62744904, 8, 4, 4, 9, 8, 0, 73048177, 8, 5, 4, 7, 9, 0, 57716858, 7, 7, 4, 6, 9, 0, 52688813, 9, 5, 4, 7, 8, 0, 62744904, 7, 4, 4, 9, 8, 0, 73048177, 7, 7, 4, 9, 9, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (89, 0, 0, 0, 0, 0, 0, 0, 56488813, 5, 2, 4, 3, 4, 0, 67304904, 5, 1, 4, 5, 4, 0, 78368177, 5, 1, 4, 3, 5, 0, 61896858, 4, 2, 4, 3, 5, 0, 56488813, 5, 2, 4, 3, 4, 0, 67304904, 4, 1, 4, 5, 4, 0, 78368177, 4, 2, 4, 5, 5, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (90, 26, 28, 30, 28, 26, 29, 30, 60488813, 20, 10, 5, 7, 8, 0, 72104904, 20, 10, 5, 9, 8, 20, 83968177, 20, 10, 5, 7, 9, 20, 66296858, 18, 10, 5, 7, 10, 20, 60488813, 20, 10, 5, 7, 8, 0, 72104904, 18, 10, 5, 9, 8, 0, 83968177, 18, 10, 5, 9, 10, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (91, 0, 0, 0, 0, 0, 0, 0, 64738813, 5, 3, 5, 3, 4, 0, 77204904, 5, 2, 5, 5, 4, 0, 89918177, 5, 2, 5, 3, 5, 0, 70971858, 4, 3, 5, 3, 5, 0, 64738813, 5, 3, 5, 3, 4, 0, 77204904, 4, 2, 5, 5, 4, 0, 89918177, 4, 3, 5, 5, 5, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (92, 0, 0, 0, 0, 0, 0, 0, 69238813, 6, 3, 5, 4, 5, 0, 82604904, 4, 2, 5, 6, 5, 0, 96218177, 4, 3, 5, 4, 6, 0, 75921858, 4, 3, 5, 4, 6, 0, 69238813, 6, 3, 5, 4, 5, 0, 82604904, 4, 2, 5, 6, 5, 0, 96218177, 4, 3, 5, 6, 6, 20, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (93, 0, 0, 0, 0, 0, 0, 0, 73988813, 8, 4, 5, 6, 7, 0, 88304904, 6, 2, 5, 8, 7, 0, 102868177, 6, 2, 5, 6, 8, 0, 81146858, 4, 7, 5, 6, 8, 0, 73988813, 8, 4, 5, 6, 7, 0, 88304904, 4, 2, 5, 8, 7, 0, 102868177, 4, 7, 5, 8, 8, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (94, 0, 0, 0, 0, 0, 0, 0, 78988813, 4, 4, 5, 3, 3, 0, 94304904, 4, 3, 5, 5, 3, 0, 109878177, 4, 4, 5, 3, 4, 0, 86646858, 3, 4, 5, 3, 5, 0, 78988813, 4, 4, 5, 3, 3, 20, 94304904, 3, 3, 5, 5, 3, 20, 109878177, 3, 4, 5, 5, 5, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (95, 0, 0, 0, 0, 0, 0, 0, 84238813, 3, 3, 5, 5, 2, 0, 100604904, 4, 2, 5, 7, 2, 0, 117218177, 4, 2, 5, 5, 3, 0, 92421858, 3, 3, 5, 3, 5, 0, 84238813, 3, 3, 5, 5, 2, 0, 100604904, 3, 2, 5, 7, 2, 0, 117218177, 3, 3, 5, 7, 5, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (96, 0, 0, 0, 0, 0, 0, 0, 89728813, 5, 3, 5, 4, 3, 0, 107204904, 5, 2, 5, 7, 3, 0, 124918177, 5, 3, 5, 4, 4, 0, 98471858, 5, 4, 5, 3, 4, 0, 89728813, 5, 3, 5, 4, 3, 0, 107204904, 5, 2, 5, 7, 3, 0, 124918177, 5, 4, 5, 7, 4, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (97, 0, 0, 0, 0, 0, 0, 0, 95488813, 5, 4, 5, 4, 5, 0, 114104904, 5, 3, 5, 7, 5, 0, 132968177, 5, 3, 5, 4, 6, 0, 104796858, 4, 4, 5, 4, 6, 0, 95488813, 5, 4, 5, 4, 5, 0, 114104904, 4, 3, 5, 7, 5, 0, 132968177, 4, 4, 5, 7, 6, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (98, 0, 0, 0, 0, 0, 0, 0, 101488813, 4, 5, 5, 4, 3, 0, 121304904, 4, 3, 5, 7, 3, 0, 141368177, 4, 4, 5, 4, 4, 0, 111396858, 4, 5, 5, 3, 5, 0, 101488813, 4, 5, 5, 4, 3, 0, 121304904, 4, 3, 5, 7, 3, 0, 141368177, 4, 5, 5, 7, 5, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (99, 0, 0, 0, 0, 0, 0, 0, 107738813, 40, 6, 6, 5, 5, 21, 128804904, 50, 5, 5, 7, 5, 0, 150118177, 50, 6, 5, 6, 7, 0, 118271858, 45, 6, 5, 4, 7, 28, 107738813, 40, 6, 6, 6, 5, 0, 128804904, 45, 5, 5, 7, 5, 0, 150118177, 45, 6, 5, 7, 7, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (100, 28, 30, 32, 30, 28, 31, 32, 114238813, 25, 6, 6, 4, 4, 0, 136604904, 30, 4, 5, 6, 5, 0, 159218177, 25, 4, 4, 5, 6, 0, 125421858, 25, 5, 4, 4, 7, 0, 114238813, 25, 6, 6, 5, 4, 0, 136604904, 25, 4, 5, 6, 5, 0, 159218177, 25, 5, 4, 6, 7, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (101, 0, 0, 0, 0, 0, 0, 0, 120988813, 5, 3, 5, 3, 4, 0, 144754904, 5, 2, 5, 5, 4, 0, 168718177, 5, 2, 5, 3, 5, 0, 132846858, 4, 3, 5, 3, 5, 0, 120988813, 5, 3, 5, 3, 4, 0, 144754904, 5, 2, 5, 5, 4, 0, 168718177, 5, 2, 5, 3, 5, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (102, 0, 0, 0, 0, 0, 0, 0, 127988813, 7, 3, 4, 5, 6, 0, 153204904, 4, 2, 5, 6, 5, 0, 178668177, 4, 3, 5, 4, 6, 0, 140546858, 4, 3, 5, 4, 6, 0, 127988813, 7, 3, 4, 5, 6, 0, 153204904, 4, 2, 5, 6, 5, 0, 178668177, 4, 3, 5, 4, 6, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (103, 0, 0, 0, 0, 0, 0, 0, 135238813, 8, 4, 5, 6, 7, 0, 161954904, 6, 2, 5, 8, 7, 0, 189118177, 6, 2, 5, 6, 8, 0, 148521858, 4, 7, 5, 6, 8, 0, 135238813, 8, 4, 5, 6, 7, 0, 161954904, 6, 2, 5, 8, 7, 0, 189118177, 6, 2, 5, 6, 8, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (104, 0, 0, 0, 0, 0, 0, 0, 142738813, 4, 4, 5, 3, 3, 0, 171004904, 4, 2, 5, 7, 2, 0, 200118177, 4, 4, 5, 3, 4, 0, 156771858, 6, 8, 4, 7, 10, 0, 142738813, 4, 4, 5, 3, 3, 0, 171004904, 4, 2, 5, 7, 2, 0, 200118177, 4, 4, 5, 3, 4, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (105, 0, 0, 0, 0, 0, 0, 0, 150488813, 3, 3, 5, 5, 2, 0, 182604904, 6, 3, 4, 7, 6, 0, 211718177, 4, 2, 5, 5, 3, 0, 165296858, 7, 7, 4, 6, 9, 0, 150488813, 3, 3, 5, 5, 2, 0, 182604904, 6, 3, 4, 7, 6, 0, 211718177, 4, 2, 5, 5, 3, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (106, 0, 0, 0, 0, 0, 0, 0, 158488813, 5, 3, 5, 4, 3, 0, 190004904, 8, 4, 4, 8, 7, 0, 223968177, 5, 3, 5, 4, 4, 0, 174096858, 4, 2, 4, 3, 5, 0, 158488813, 5, 3, 5, 4, 3, 0, 190004904, 8, 4, 4, 8, 7, 0, 223968177, 5, 3, 5, 4, 4, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (107, 0, 0, 0, 0, 0, 0, 0, 166738813, 3, 3, 5, 5, 2, 0, 199954904, 5, 1, 4, 5, 4, 0, 236918177, 8, 3, 4, 6, 8, 0, 181171858, 5, 5, 4, 3, 4, 0, 166738813, 3, 3, 5, 5, 2, 0, 199954904, 5, 1, 4, 5, 4, 0, 236918177, 8, 3, 4, 6, 8, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (108, 0, 0, 0, 0, 0, 0, 0, 175238813, 5, 3, 5, 4, 3, 0, 210204904, 7, 3, 4, 8, 7, 0, 250618177, 5, 2, 4, 3, 5, 0, 190521858, 4, 6, 4, 4, 7, 0, 175238813, 5, 3, 5, 4, 3, 0, 210204904, 7, 3, 4, 8, 7, 0, 250618177, 5, 2, 4, 3, 5, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (109, 0, 0, 0, 0, 0, 0, 0, 183988813, 5, 4, 5, 4, 5, 0, 220754904, 4, 3, 5, 7, 3, 0, 265118177, 4, 4, 5, 4, 4, 0, 200146858, 4, 5, 5, 3, 5, 0, 183988813, 5, 4, 5, 4, 5, 0, 220754904, 4, 3, 5, 7, 3, 0, 265118177, 4, 4, 5, 4, 4, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (110, 30, 32, 34, 32, 30, 33, 34, 192988813, 20, 10, 5, 7, 8, 0, 231604904, 20, 10, 5, 9, 8, 0, 280468177, 25, 4, 4, 5, 6, 0, 210046858, 25, 5, 4, 4, 7, 0, 192988813, 20, 10, 5, 7, 8, 0, 231604904, 20, 10, 5, 9, 8, 0, 280468177, 25, 4, 4, 5, 6, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (116, 0, 0, 0, 0, 0, 0, 0, 252763813, 6, 4, 6, 5, 4, 0, 302704904, 9, 5, 5, 9, 8, 0, 393218177, 6, 4, 6, 5, 5, 0, 275746858, 5, 3, 5, 4, 6, 0, 252763813, 6, 4, 6, 5, 4, 0, 302704904, 9, 5, 5, 9, 8, 0, 393218177, 6, 4, 6, 5, 5, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (115, 0, 0, 0, 0, 0, 0, 0, 242113813, 4, 4, 6, 6, 3, 0, 290104904, 7, 4, 5, 8, 7, 0, 371718177, 5, 3, 6, 6, 3, 0, 264046858, 8, 8, 5, 7, 10, 0, 242113813, 4, 4, 6, 6, 3, 0, 290104904, 7, 4, 5, 8, 7, 0, 371718177, 5, 3, 6, 6, 3, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (114, 0, 0, 0, 0, 0, 0, 0, 231738813, 5, 5, 6, 4, 4, 0, 277804904, 5, 3, 6, 8, 3, 0, 351368177, 5, 5, 6, 4, 5, 0, 252646858, 7, 9, 5, 8, 11, 0, 231738813, 5, 5, 6, 4, 4, 0, 277804904, 5, 3, 6, 8, 3, 0, 351368177, 5, 5, 6, 4, 5, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (113, 0, 0, 0, 0, 0, 0, 0, 221638813, 9, 5, 6, 7, 8, 0, 265804904, 7, 3, 6, 9, 7, 0, 332118177, 7, 3, 6, 7, 9, 0, 241546858, 5, 8, 6, 7, 9, 0, 221638813, 9, 5, 6, 7, 8, 0, 265804904, 7, 3, 6, 9, 7, 0, 332118177, 7, 3, 6, 7, 9, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (112, 0, 0, 0, 0, 0, 0, 0, 211813813, 8, 4, 5, 6, 7, 0, 254104904, 5, 3, 6, 7, 6, 0, 313918177, 5, 4, 6, 5, 7, 0, 230746858, 5, 4, 6, 5, 7, 0, 211813813, 8, 4, 5, 6, 7, 0, 254104904, 5, 3, 6, 7, 6, 0, 313918177, 5, 4, 6, 5, 7, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (111, 0, 0, 0, 0, 0, 0, 0, 202263813, 6, 4, 6, 4, 5, 0, 242704904, 6, 3, 6, 6, 5, 0, 296718177, 6, 3, 6, 4, 6, 0, 220246858, 5, 4, 6, 4, 6, 0, 202263813, 6, 4, 6, 4, 5, 0, 242704904, 6, 3, 6, 6, 5, 0, 296718177, 6, 3, 6, 4, 6, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (117, 0, 0, 0, 0, 0, 0, 0, 263688813, 4, 4, 6, 6, 3, 0, 315604904, 6, 2, 5, 6, 5, 0, 415918177, 9, 4, 5, 7, 9, 0, 287746858, 6, 6, 5, 4, 5, 0, 263688813, 4, 4, 6, 6, 3, 0, 315604904, 6, 2, 5, 6, 5, 0, 415918177, 9, 4, 5, 7, 9, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (118, 0, 0, 0, 0, 0, 0, 0, 274888813, 6, 4, 6, 5, 4, 0, 328804904, 8, 4, 5, 9, 8, 0, 439868177, 6, 3, 5, 4, 6, 0, 300046858, 5, 7, 5, 5, 8, 0, 274888813, 6, 4, 6, 5, 4, 0, 328804904, 8, 4, 5, 9, 8, 0, 439868177, 6, 3, 5, 4, 6, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (119, 0, 0, 0, 0, 0, 0, 0, 286363813, 6, 5, 6, 5, 6, 0, 342304904, 5, 4, 6, 8, 4, 0, 465118177, 7, 3, 6, 7, 9, 0, 313646858, 5, 6, 6, 4, 6, 0, 286363813, 6, 5, 6, 5, 6, 0, 342304904, 5, 4, 6, 8, 4, 0, 465118177, 7, 3, 6, 7, 9, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (120, 32, 34, 36, 34, 32, 35, 36, 298113813, 25, 15, 6, 8, 9, 0, 356104904, 25, 15, 6, 10, 9, 0, 491718177, 30, 5, 5, 6, 7, 0, 326546858, 30, 6, 5, 5, 8, 0, 298113813, 25, 15, 6, 8, 9, 0, 356104904, 25, 15, 6, 10, 9, 0, 491718177, 30, 5, 5, 6, 7, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (121, 0, 0, 0, 0, 0, 0, 0, 310138813, 5, 5, 6, 4, 4, 0, 370204904, 5, 4, 6, 8, 4, 0, 519718177, 5, 5, 6, 4, 5, 0, 339746858, 5, 3, 5, 4, 6, 0, 310138813, 5, 5, 6, 4, 4, 0, 370204904, 5, 4, 6, 8, 4, 0, 519718177, 5, 5, 6, 4, 5, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (122, 0, 0, 0, 0, 0, 0, 0, 322438813, 4, 4, 6, 6, 3, 0, 384604904, 6, 3, 6, 6, 5, 0, 549168177, 5, 3, 6, 6, 3, 0, 353246858, 6, 6, 5, 4, 5, 0, 322438813, 4, 4, 6, 6, 3, 0, 384604904, 6, 3, 6, 6, 5, 0, 549168177, 5, 3, 6, 6, 3, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (123, 0, 0, 0, 0, 0, 0, 0, 335013813, 6, 4, 6, 5, 4, 0, 399304904, 5, 3, 6, 7, 6, 0, 580118177, 6, 4, 6, 5, 5, 0, 367046858, 5, 7, 5, 5, 8, 0, 335013813, 6, 4, 6, 5, 4, 0, 399304904, 5, 3, 6, 7, 6, 0, 580118177, 6, 4, 6, 5, 5, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (124, 0, 0, 0, 0, 0, 0, 0, 347863813, 4, 4, 6, 6, 3, 0, 414304904, 7, 3, 6, 9, 7, 0, 612618177, 6, 3, 5, 4, 6, 0, 381246858, 5, 6, 6, 4, 6, 0, 347863813, 4, 4, 6, 6, 3, 0, 414304904, 7, 3, 6, 9, 7, 0, 612618177, 6, 3, 5, 4, 6, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (125, 0, 0, 0, 0, 0, 0, 0, 360988813, 6, 4, 6, 5, 4, 0, 429604904, 5, 3, 6, 8, 3, 0, 646718177, 5, 4, 6, 5, 7, 0, 395746858, 5, 4, 6, 4, 6, 0, 360988813, 6, 4, 6, 5, 4, 0, 429604904, 5, 3, 6, 8, 3, 0, 646718177, 5, 4, 6, 5, 7, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (126, 0, 0, 0, 0, 0, 0, 0, 374388813, 6, 5, 6, 5, 6, 0, 445204904, 7, 4, 5, 8, 7, 0, 682468177, 7, 3, 6, 7, 9, 0, 410546858, 5, 4, 6, 5, 7, 0, 374388813, 6, 5, 6, 5, 6, 0, 445204904, 7, 4, 5, 8, 7, 0, 682468177, 7, 3, 6, 7, 9, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (127, 0, 0, 0, 0, 0, 0, 0, 388063813, 6, 4, 6, 4, 5, 0, 461104904, 9, 5, 5, 9, 8, 0, 719918177, 5, 5, 6, 4, 5, 0, 425646858, 5, 8, 6, 7, 9, 0, 388063813, 6, 4, 6, 4, 5, 0, 461104904, 9, 5, 5, 9, 8, 0, 719918177, 5, 5, 6, 4, 5, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (128, 0, 0, 0, 0, 0, 0, 0, 402013813, 8, 4, 5, 6, 7, 0, 477304904, 6, 2, 5, 6, 5, 0, 759118177, 5, 3, 6, 6, 3, 0, 441046858, 7, 9, 5, 8, 11, 0, 402013813, 8, 4, 5, 6, 7, 0, 477304904, 6, 2, 5, 6, 5, 0, 759118177, 5, 3, 6, 6, 3, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (129, 0, 0, 0, 0, 0, 0, 0, 416238813, 9, 5, 6, 7, 8, 0, 493804904, 8, 4, 5, 9, 8, 0, 800118177, 9, 4, 5, 7, 9, 0, 456746858, 8, 8, 5, 7, 10, 0, 416238813, 9, 5, 6, 7, 8, 0, 493804904, 8, 4, 5, 9, 8, 0, 800118177, 9, 4, 5, 7, 9, 0, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `$levels` VALUES (130, 34, 36, 38, 36, 34, 37, 38, 430738813, 25, 15, 6, 8, 9, 0, 510604904, 25, 15, 6, 10, 9, 0, 842968177, 30, 5, 5, 6, 7, 0, 472746858, 30, 6, 5, 5, 8, 0, 430738813, 25, 15, 6, 8, 9, 0, 510604904, 25, 15, 6, 10, 9, 0, 842968177, 30, 5, 5, 6, 7, 0, 1, 1, 1, 1, 1, 1, 1);
END;
if (dobatch($query) == 1) { echo "Levels table populated.<br />"; } else { echo "Error populating Levels table."; }
unset($query);


$query = <<<END
CREATE TABLE `$marketforum` (
  `id` int(11) NOT NULL auto_increment,
  `postdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `newpostdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` varchar(30) NOT NULL default '',
  `parent` int(11) NOT NULL default '0',
  `replies` int(11) NOT NULL default '0',
  `close` tinyint(1) NOT NULL default '0',
  `pin` tinyint(1) NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `content` text NOT NULL,
  PRIMARY KEY  (`id`)

) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Market Forum table created.<br />"; } else { echo "Error creating Market Forum table."; }
unset($query);


$query = <<<END
CREATE TABLE `$mining` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(30) NOT NULL default '',
  `level` int(3) NOT NULL default '1',
  `gemname` varchar(30) NOT NULL default 'None',
  `gemtype` smallint(5) unsigned NOT NULL default '0',
  `requirement` int(3) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Mining table created.<br />"; } else { echo "Error creating Mining table."; }
unset($query);

$query = <<<END
INSERT INTO `$mining` VALUES (1, 'Copper', 1, 'Sapphire', 1, 3);
INSERT INTO `$mining` VALUES (2, 'Tin', 10, 'Sapphire', 1, 15);
INSERT INTO `$mining` VALUES (3, 'Iron', 25, 'Sapphire', 1, 25);
INSERT INTO `$mining` VALUES (4, 'Magic', 40, 'Emerald', 2, 35);
INSERT INTO `$mining` VALUES (5, 'Dark', 60, 'Emerald', 2, 45);
INSERT INTO `$mining` VALUES (6, 'Bright', 75, 'Emerald', 2, 51);
INSERT INTO `$mining` VALUES (7, 'Destiny', 95, 'Ruby', 3, 57);
INSERT INTO `$mining` VALUES (8, 'Crystal', 115, 'Ruby', 3, 64);
INSERT INTO `$mining` VALUES (9, 'Diamond', 140, 'Ruby', 3, 71);
INSERT INTO `$mining` VALUES (10, 'Heros', 170, 'Diamond', 4, 77);
INSERT INTO `$mining` VALUES (11, 'Holy', 190, 'Diamond', 4, 83);
INSERT INTO `$mining` VALUES (12, 'Mythical', 215, 'Diamond', 4, 90);
INSERT INTO `$mining` VALUES (13, 'Black Dragons', 235, 'Black Dragon', 5, 96);

END;
if (dobatch($query) == 1) { echo "Mining table populated.<br />"; } else { echo "Error populating Mining table."; }
unset($query);


$query = <<<END
CREATE TABLE `$monsters` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `cweap` varchar(30) NOT NULL default 'None',
  `carm` varchar(30) NOT NULL default 'None',
  `cshield` varchar(30) NOT NULL default 'None',
  `maxhp` smallint(5) unsigned NOT NULL default '0',
  `maxdam` smallint(5) unsigned NOT NULL default '0',
  `armor` smallint(5) unsigned NOT NULL default '0',
  `level` smallint(5) unsigned NOT NULL default '0',
  `maxexp` smallint(5) unsigned NOT NULL default '0',
  `maxgold` smallint(5) unsigned NOT NULL default '0',
  `boss` smallint(3) NOT NULL default '0',
  `bones` tinyint(3) unsigned NOT NULL default '0',
  `immune` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Monsters table created.<br />"; } else { echo "Error creating Monsters table."; }
unset($query);

$query = <<<END
INSERT INTO `$monsters` VALUES (1, 'Blue Slime', 'None', 'None', 'None', 4, 2, 1, 1, 1, 2, 0, 0, 0);
INSERT INTO `$monsters` VALUES (2, 'Red Slime', 'None', 'None', 'None', 5, 4, 1, 1, 2, 3, 0, 0, 0);
INSERT INTO `$monsters` VALUES (3, 'Critter', 'Claws', 'Hard Skin', 'None', 6, 5, 2, 1, 4, 2, 0, 0, 0);
INSERT INTO `$monsters` VALUES (4, 'Creature', 'Claws', 'Hard Skin', 'None', 10, 8, 2, 2, 4, 2, 0, 0, 0);
INSERT INTO `$monsters` VALUES (5, 'Shadow', 'Dark Claws', 'Shadow Skin', 'Dark Shield', 10, 9, 3, 2, 6, 2, 0, 0, 1);
INSERT INTO `$monsters` VALUES (6, 'Drake', 'Fangs', 'Scales', 'Wings', 11, 10, 3, 2, 8, 3, 0, 0, 0);
INSERT INTO `$monsters` VALUES (7, 'Shade', 'None', 'None', 'None', 12, 10, 3, 3, 10, 3, 0, 0, 1);
INSERT INTO `$monsters` VALUES (8, 'Drakelor', 'Fangs', 'Hard Scales', 'Wings', 14, 12, 4, 3, 10, 3, 0, 0, 0);
INSERT INTO `$monsters` VALUES (9, 'Silver Slime', 'None', 'Silver Shell', 'None', 15, 160, 235, 30, 21, 800, 0, 0, 2);
INSERT INTO `$monsters` VALUES (10, 'Scamp', 'Heister Dagger', 'Black \r\nCloak', 'Small Buckler', 16, 13, 5, 4, 15, 5, 0, 0, 0);
INSERT INTO `$monsters` VALUES (11, 'Raven', 'Beak', 'Thick Feathers', 'Wings', 16, 13, 5, 4, 18, 6, 0, 1, 0);
INSERT INTO `$monsters` VALUES (12, 'Scorpion', 'Poisonous Stinger', 'Hard \r\nShell', 'None', 18, 14, 6, 5, 20, 7, 0, 1, 0);
INSERT INTO `$monsters` VALUES (13, 'Illusion', 'None', 'None', 'None', 20, 15, 6, 5, 20, 7, 0, 0, 1);
INSERT INTO `$monsters` VALUES (14, 'Nightshade', 'None', 'None', 'None', 22, 16, 6, 6, 24, 8, 0, 0, 0);
INSERT INTO `$monsters` VALUES (15, 'Drakemal', 'Fangs', 'Rough Scales', 'Wings', 22, 18, 7, 6, 24, 8, 0, 0, 0);
INSERT INTO `$monsters` VALUES (16, 'Shadow Raven', 'Strong Beak', 'Dark \r\nFeathers', 'Wings', 24, 18, 7, 6, 26, 9, 0, 1, 1);
INSERT INTO `$monsters` VALUES (17, 'Ghost', 'None', 'None', 'None', 24, 20, 8, 6, 28, 9, 0, 0, 0);
INSERT INTO `$monsters` VALUES (18, 'Frost Raven', 'Sharp Beak', 'Ice \r\nFeathers', 'Wings', 26, 20, 8, 7, 30, 10, 0, 0, 0);
INSERT INTO `$monsters` VALUES (19, 'Rogue Scorpion', 'Poisonous Stinger', 'Hard Shell', 'None', 28, 22, 9, 7, 32, 11, 0, 1, 0);
INSERT INTO `$monsters` VALUES (20, 'Ghoul', 'None', 'None', 'None', 29, 24, 9, 7, 34, 11, 0, 0, 0);
INSERT INTO `$monsters` VALUES (21, 'Magician', 'Wand', 'Robes', 'None', 30, 24, 10, 8, 36, 12, 0, 1, 0);
INSERT INTO `$monsters` VALUES (22, 'Rogue', 'Standard Dirk', 'Leather \r\nArmor', 'Small Buckler', 30, 25, 12, 8, 40, 13, 0, 0, 0);
INSERT INTO `$monsters` VALUES (23, 'Drakefin', 'Fangs', 'Tough Scales', 'Wings', 32, 26, 12, 8, 40, 13, 0, 0, 0);
INSERT INTO `$monsters` VALUES (24, 'Shimmer', 'None', 'None', 'None', 32, 26, 14, 8, 45, 15, 0, 0, 1);
INSERT INTO `$monsters` VALUES (25, 'Fire Raven', 'Sharp Beak', 'Fire \r\nFeathers', 'Wings', 34, 28, 14, 9, 45, 15, 0, 1, 0);
INSERT INTO `$monsters` VALUES (26, 'Dybbuk', 'Short Sword', 'Leather \r\nArmor', 'Small Shield', 34, 28, 14, 9, 50, 17, 0, 0, 0);
INSERT INTO `$monsters` VALUES (27, 'Knave', 'Dirk', 'Hard Leather Armor', 'Buckler', 36, 30, 15, 9, 52, 17, 0, 0, 0);
INSERT INTO `$monsters` VALUES (28, 'Goblin', 'Short Spear', 'Wolf Hide', 'None', 36, 30, 15, 10, 54, 18, 0, 0, 0);
INSERT INTO `$monsters` VALUES (29, 'Skeleton', 'Iron Mace', 'Iron Armor', 'Large Shield', 38, 30, 18, 10, 58, 19, 0, 1, 0);
INSERT INTO `$monsters` VALUES (30, 'Dark Slime', 'None', 'None', 'None', 38, 32, 18, 10, 62, 21, 0, 0, 0);
INSERT INTO `$monsters` VALUES (31, 'Silver Scorpion', 'Poisonous \r\nStinger', 'Silver Shell', 'None', 55, 165, 375, 40, 60, 1800, 0, 2, 2);
INSERT INTO `$monsters` VALUES (32, 'Mirage', 'None', 'None', 'None', 40, 32, 20, 11, 64, 21, 0, 0, 1);
INSERT INTO `$monsters` VALUES (33, 'Sorceror', 'Bone Wand', 'Dark Robes', 'None', 41, 33, 22, 11, 68, 23, 0, 1, 0);
INSERT INTO `$monsters` VALUES (34, 'Imp', 'Tiny Dagger', 'Tiny Rags', 'None', 42, 34, 22, 12, 70, 23, 0, 0, 0);
INSERT INTO `$monsters` VALUES (35, 'Nymph', 'Stick', 'Silk Clothes', 'None', 43, 35, 22, 12, 70, 23, 0, 0, 0);
INSERT INTO `$monsters` VALUES (36, 'Scoundrel', 'Sharp Dirk', 'Hard \r\nLeather Hide', 'Large Buckler', 43, 35, 22, 12, 75, 25, 0, 0, 0);
INSERT INTO `$monsters` VALUES (37, 'Megaskeleton', 'Two Handed Sword', 'Steel Armor', 'None', 44, 36, 24, 13, 78, 26, 0, 3, 0);
INSERT INTO `$monsters` VALUES (38, 'Grey Wolf', 'Sharp Claws', 'Silver \r\nHide', 'None', 44, 36, 24, 13, 82, 27, 0, 1, 0);
INSERT INTO `$monsters` VALUES (39, 'Phantom', 'None', 'None', 'None', 46, 38, 24, 14, 85, 28, 0, 0, 1);
INSERT INTO `$monsters` VALUES (40, 'Specter', 'None', 'None', 'None', 46, 38, 24, 14, 90, 30, 0, 0, 0);
INSERT INTO `$monsters` VALUES (41, 'Dark Scorpion', 'Poisonous Stinger', 'Dark Shell', 'None', 48, 40, 26, 15, 95, 32, 0, 2, 1);
INSERT INTO `$monsters` VALUES (42, 'Warlock', 'Bone Wand', 'Thick Robes', 'None', 48, 40, 26, 15, 100, 33, 0, 1, 1);
INSERT INTO `$monsters` VALUES (43, 'Orc', 'Orc Scimitar', 'Heavy Orc \r\nArmor', 'Large Orc Shield', 49, 42, 28, 15, 104, 35, 0, 1, 0);
INSERT INTO `$monsters` VALUES (44, 'Sylph', 'Dual Daggers', 'Silk Gown', 'None', 49, 42, 28, 15, 106, 35, 0, 0, 0);
INSERT INTO `$monsters` VALUES (45, 'Wraith', 'None', 'None', 'None', 50, 45, 30, 16, 108, 36, 0, 0, 0);
INSERT INTO `$monsters` VALUES (46, 'Hellion', 'Wooden Bat', 'Thick Hide', 'None', 50, 45, 30, 16, 110, 37, 0, 0, 0);
INSERT INTO `$monsters` VALUES (47, 'Bandit', 'Dual Dirks', 'Thick Cloak', 'None', 52, 45, 30, 16, 114, 38, 0, 0, 0);
INSERT INTO `$monsters` VALUES (48, 'Ultraskeleton', 'Battle Axe', 'Rags', 'Arm Buckler', 52, 46, 32, 16, 116, 39, 0, 4, 0);
INSERT INTO `$monsters` VALUES (49, 'Dark Wolf', 'Dark Claws', 'Dark \r\nHide', 'None', 54, 47, 36, 17, 120, 40, 0, 1, 1);
INSERT INTO `$monsters` VALUES (50, 'Troll', 'Huge Club', 'Thick Skin', 'None', 56, 48, 36, 17, 120, 40, 0, 1, 0);
INSERT INTO `$monsters` VALUES (51, 'Werewolf', 'Sharp Claws', 'Thick \r\nHide', 'None', 56, 48, 38, 17, 124, 41, 0, 1, 0);
INSERT INTO `$monsters` VALUES (52, 'Hellcat', 'Sharp Claws', 'Thick \r\nHide', 'None', 58, 50, 38, 18, 128, 43, 0, 1, 0);
INSERT INTO `$monsters` VALUES (53, 'Spirit', 'None', 'None', 'None', 58, 50, 38, 18, 132, 44, 0, 0, 0);
INSERT INTO `$monsters` VALUES (54, 'Nisse', 'None', 'None', 'None', 60, 52, 40, 19, 132, 44, 0, 0, 0);
INSERT INTO `$monsters` VALUES (55, 'Dawk', 'None', 'None', 'None', 60, 54, 40, 19, 136, 45, 0, 0, 0);
INSERT INTO `$monsters` VALUES (56, 'Figment', 'None', 'None', 'None', 64, 55, 42, 19, 140, 47, 0, 0, 1);
INSERT INTO `$monsters` VALUES (57, 'Hellhound', 'Large Claws', 'Hard \r\nHide', 'None', 66, 56, 44, 20, 140, 47, 0, 2, 0);
INSERT INTO `$monsters` VALUES (58, 'Wizard', 'Crystal Wand', 'Crystal \r\nRobes', 'None', 66, 56, 44, 20, 144, 48, 0, 1, 0);
INSERT INTO `$monsters` VALUES (59, 'Uruk', 'Uruk Orc Axe', 'Heavy Uruk \r\nArmor', 'Uruk Shield', 68, 58, 44, 20, 146, 49, 0, 0, 0);
INSERT INTO `$monsters` VALUES (60, 'Siren', 'None', 'White Silk Gown', 'None', 278, 442, 850, 50, 8000, 120, 0, 0, 2);
INSERT INTO `$monsters` VALUES (61, 'Megawraith', 'None', 'None', 'None', 70, 60, 46, 21, 155, 52, 0, 2, 0);
INSERT INTO `$monsters` VALUES (62, 'Dawkin', 'None', 'None', 'None', 70, 60, 46, 21, 155, 52, 0, 0, 0);
INSERT INTO `$monsters` VALUES (63, 'Grey Bear', 'Bear Claws', 'Thick Bear \r\nHide', 'None', 70, 62, 48, 21, 160, 53, 0, 1, 0);
INSERT INTO `$monsters` VALUES (64, 'Haunt', 'None', 'None', 'None', 72, 62, 48, 22, 160, 53, 0, 0, 0);
INSERT INTO `$monsters` VALUES (65, 'Hellbeast', 'Large Claws', 'Thick \r\nHide', 'None', 74, 64, 50, 22, 165, 55, 0, 2, 0);
INSERT INTO `$monsters` VALUES (66, 'Fear', 'None', 'None', 'None', 76, 66, 52, 23, 165, 55, 0, 0, 0);
INSERT INTO `$monsters` VALUES (67, 'Beast', 'Sharp Claws', 'Thick Hide', 'None', 76, 66, 52, 23, 170, 57, 0, 2, 0);
INSERT INTO `$monsters` VALUES (68, 'Ogre', 'Large Club', 'Rags', 'None', 78, 68, 54, 23, 170, 57, 0, 2, 0);
INSERT INTO `$monsters` VALUES (69, 'Dark Bear', 'Dark Claws', 'Dark Bear \r\nHide', 'None', 80, 70, 56, 24, 175, 58, 0, 2, 1);
INSERT INTO `$monsters` VALUES (70, 'Fire', 'None', 'None', 'None', 80, 72, 56, 24, 175, 58, 0, 0, 0);
INSERT INTO `$monsters` VALUES (71, 'Poltergeist', 'None', 'None', 'None', 84, 74, 58, 25, 180, 60, 0, 0, 0);
INSERT INTO `$monsters` VALUES (72, 'Fright', 'None', 'None', 'None', 86, 76, 58, 25, 180, 60, 0, 0, 0);
INSERT INTO `$monsters` VALUES (73, 'Lycan', 'Wolf Claws', 'Rough Hide', 'None', 88, 78, 60, 25, 185, 62, 0, 0, 0);
INSERT INTO `$monsters` VALUES (74, 'Terra Elemental', 'None', 'None', 'None', 88, 80, 62, 25, 185, 62, 0, 0, 1);
INSERT INTO `$monsters` VALUES (75, 'Necrolate', 'None', 'None', 'None', 90, 80, 62, 26, 190, 63, 0, 0, 0);
INSERT INTO `$monsters` VALUES (76, 'Ultrawraith', 'None', 'None', 'None', 90, 82, 64, 26, 190, 63, 0, 0, 0);
INSERT INTO `$monsters` VALUES (77, 'Dawkor', 'None', 'None', 'None', 92, 82, 64, 26, 195, 65, 0, 0, 0);
INSERT INTO `$monsters` VALUES (78, 'Werebear', 'Sharp Claws', 'Thick \r\nWerebear Hide', 'None', 92, 84, 65, 26, 195, 65, 0, 2, 0);
INSERT INTO `$monsters` VALUES (79, 'Brute', 'Sharp Claws', 'Rough Hide', 'None', 94, 84, 65, 27, 200, 67, 0, 2, 0);
INSERT INTO `$monsters` VALUES (80, 'Large Beast', 'Large Claws', 'Thick \r\nHide', 'None', 96, 88, 66, 27, 200, 67, 0, 2, 0);
INSERT INTO `$monsters` VALUES (81, 'Horror', 'None', 'None', 'None', 96, 88, 68, 27, 210, 70, 0, 0, 0);
INSERT INTO `$monsters` VALUES (82, 'Flame', 'None', 'None', 'None', 100, 90, 70, 28, 210, 70, 0, 0, 0);
INSERT INTO `$monsters` VALUES (83, 'Lycanthor', 'Sharp Claws', 'Rough \r\nHide', 'None', 100, 90, 70, 28, 210, 70, 0, 0, 0);
INSERT INTO `$monsters` VALUES (84, 'Wyrm', 'Sharp Claws', 'Dark Scales', 'Wings', 100, 92, 72, 28, 220, 73, 0, 0, 0);
INSERT INTO `$monsters` VALUES (85, 'Aero Elemental', 'None', 'None', 'None', 104, 94, 74, 29, 220, 73, 0, 0, 1);
INSERT INTO `$monsters` VALUES (86, 'Dawkare', 'None', 'None', 'None', 106, 96, 76, 29, 220, 73, 0, 0, 0);
INSERT INTO `$monsters` VALUES (87, 'Large Brute', 'Large Fangs', 'Thick \r\nHide', 'None', 108, 98, 78, 29, 230, 77, 0, 0, 0);
INSERT INTO `$monsters` VALUES (88, 'Frost Wyrm', 'Sharp Claws', 'Frost \r\nScales', 'Wings', 110, 100, 80, 30, 230, 77, 0, 0, 0);
INSERT INTO `$monsters` VALUES (89, 'Knight', 'Long Sword', 'Heavy Steel \r\nArmor', 'Kite Shield', 110, 102, 80, 30, 240, 80, 0, 1, 0);
INSERT INTO `$monsters` VALUES (90, 'Lycanthra', 'Sharp Claws', 'Thick \r\nHide', 'None', 112, 104, 82, 30, 240, 80, 0, 0, 0);
INSERT INTO `$monsters` VALUES (91, 'Terror', 'None', 'None', 'None', 115, 108, 84, 31, 250, 83, 0, 0, 0);
INSERT INTO `$monsters` VALUES (92, 'Blaze', 'None', 'None', 'None', 118, 108, 84, 31, 250, 83, 0, 0, 0);
INSERT INTO `$monsters` VALUES (93, 'Aqua Elemental', 'None', 'None', 'None', 120, 110, 90, 31, 260, 87, 0, 0, 1);
INSERT INTO `$monsters` VALUES (94, 'Fire Wyrm', 'Sharp Claws', 'Fire \r\nScales', 'Wings', 120, 110, 90, 32, 260, 87, 0, 0, 0);
INSERT INTO `$monsters` VALUES (95, 'Lesser Wyvern', 'Large Claws', 'Purple Scales', 'Wings', 122, 110, 92, 32, 270, 90, 0, 1, 0);
INSERT INTO `$monsters` VALUES (96, 'Doomer', 'None', 'None', 'None', 124, 112, 92, 32, 270, 90, 0, 0, 0);
INSERT INTO `$monsters` VALUES (97, 'Armor Knight', 'Two Handed Sword', 'Heavy Mithril Armor', 'None', 130, 115, 95, 33, 280, 93, 0, 1, 1);
INSERT INTO `$monsters` VALUES (98, 'Wyvern', 'Large Claws', 'Blue \r\nScales', 'Wings', 134, 120, 95, 33, 290, 97, 0, 0, 0);
INSERT INTO `$monsters` VALUES (99, 'Nightmare', 'None', 'None', 'None', 138, 125, 100, 33, 300, 100, 0, 0, 0);
INSERT INTO `$monsters` VALUES (100, 'Fira Elemental', 'Blazing Rapier', 'SunChain', 'None', 140, 125, 100, 34, 310, 103, 0, 0, 1);
INSERT INTO `$monsters` VALUES (101, 'Megadoomer', 'Deadly Blade', 'Black \r\nPlate', 'None', 140, 128, 105, 34, 320, 107, 0, 0, 0);
INSERT INTO `$monsters` VALUES (102, 'Greater Wyvern', 'Large Claws', 'Green Scales', 'Wings', 145, 130, 105, 34, 335, 112, 0, 2, 0);
INSERT INTO `$monsters` VALUES (103, 'Advocate', 'Justice Whip', 'Blue \r\nArmor', 'None', 148, 132, 108, 35, 350, 117, 0, 0, 0);
INSERT INTO `$monsters` VALUES (104, 'Strong Knight', 'Dragon Crest \r\nSword', 'Mithril Chain', 'Black Dragon Shield', 150, 135, 110, 35, 365, 122, 0, 0, 0);
INSERT INTO `$monsters` VALUES (105, 'Liche', 'Poisoned Mace', 'Large \r\nSteel Plate', 'Black Dragon Shield', 150, 135, 110, 35, 380, 127, 0, 0, 0);
INSERT INTO `$monsters` VALUES (106, 'Ultradoomer', 'Giant Sword', 'Great \r\nMithril Scales', 'Death Shield', 155, 140, 115, 36, 395, 132, 0, 0, 0);
INSERT INTO `$monsters` VALUES (107, 'Fanatic', 'Steel Saber', 'Green \r\nrobes', 'None', 160, 140, 115, 36, 410, 137, 0, 0, 0);
INSERT INTO `$monsters` VALUES (108, 'Green Dragon', 'Poison Claws', 'Green Dragon Scales', 'Wings', 360, 190, 195, 36, 425, 142, 0, 0, 0);
INSERT INTO `$monsters` VALUES (109, 'Fiend', 'Black Scimitar', 'Dark \r\nArmor', 'Dark Shield', 160, 145, 120, 37, 445, 148, 0, 0, 0);
INSERT INTO `$monsters` VALUES (110, 'Greatest Wyvern', 'Large Claws', 'Green Scales', 'Wings', 162, 150, 120, 37, 465, 155, 0, 0, 0);
INSERT INTO `$monsters` VALUES (111, 'Lesser Devil', 'Dark Sword', 'Fiery \r\nArmor', 'None', 164, 150, 120, 37, 485, 162, 0, 0, 0);
INSERT INTO `$monsters` VALUES (112, 'Liche Master', 'Poisoned Battle \r\nAxe', 'Black Dragon Hide', 'Tamers Shield', 168, 155, 125, 38, 505, 168, 0, 0, 0);
INSERT INTO `$monsters` VALUES (113, 'Zealot', 'Zealot Sword', 'Zealot \r\nArmor', 'Zealot Shield', 168, 155, 125, 38, 530, 177, 0, 1, 0);
INSERT INTO `$monsters` VALUES (114, 'Serafiend', 'Mithril Scimitar', 'Dark Chain', 'None', 170, 155, 125, 38, 555, 185, 0, 0, 0);
INSERT INTO `$monsters` VALUES (115, 'Pale Knight', 'Holy Sabier', 'Light \r\nArmor', 'White Shield', 175, 160, 130, 39, 580, 193, 0, 1, 0);
INSERT INTO `$monsters` VALUES (116, 'Blue Dragon', 'Freeze Breath', 'Frosted Armor', 'Wings', 490, 190, 215, 39, 605, 202, 0, 5, 1);
INSERT INTO `$monsters` VALUES (117, 'Obsessive', 'Madman Blade', 'Crazy Armor', 'None', 280, 260, 235, 40, 630, 210, 0, 0, 0);
INSERT INTO `$monsters` VALUES (118, 'Devil', 'Whip Of Hell', 'Dooms \r\nChain', 'Dark Kite Shield', 284, 264, 235, 40, 666, 222, 0, 1, 0);
INSERT INTO `$monsters` VALUES (119, 'Liche Prince', 'Poisoned Giant \r\nSword', 'Unholy Armor', 'None', 290, 268, 238, 40, 660, 220, 0, 2, 2);
INSERT INTO `$monsters` VALUES (120, 'Cherufiend', 'Deadly Claws', 'Dark \r\nChain', 'None', 295, 270, 240, 41, 690, 230, 0, 0, 0);
INSERT INTO `$monsters` VALUES (121, 'Red Dragon', 'Fire Breath', 'Magma', 'Wings', 600, 330, 295, 41, 720, 240, 0, 5, 0);
INSERT INTO `$monsters` VALUES (122, 'Greater Devil', 'Nightmare Blade', 'Dark Lord Robes', 'None', 330, 380, 345, 41, 750, 250, 0, 2, 1);
INSERT INTO `$monsters` VALUES (123, 'Renegade', 'Renegade Blade', 'Elephant Skin Cloak', 'Red Dragon Shield', 205, 385, 240, 42, 780, 260, 0, 0, 0);
INSERT INTO `$monsters` VALUES (124, 'Archfiend', 'Demonic Longbow', 'Ranger Cape', 'None', 270, 230, 290, 42, 810, 270, 0, 0, 0);
INSERT INTO `$monsters` VALUES (125, 'Liche Lord', 'Staff Of The Calling', 'Kings Chain', 'None', 366, 490, 305, 42, 850, 283, 0, 2, 0);
INSERT INTO `$monsters` VALUES (126, 'Greatest Devil', 'Sinester Trident \r\nOf Hate', 'Demon Hide Armor', 'Soul Shield', 215, 195, 160, 43, 890, 297, 0, 3, 0);
INSERT INTO `$monsters` VALUES (127, 'Dark Knight', 'Sword Of Darkness', 'Black Plate', 'Black Kite Shield', 320, 300, 260, 43, 930, 310, 0, 2, 0);
INSERT INTO `$monsters` VALUES (128, 'Giant', 'Mithril Boulders', 'Giant \r\nSteel Armor', 'None', 320, 330, 265, 43, 970, 323, 0, 1, 0);
INSERT INTO `$monsters` VALUES (129, 'Shadow Dragon', 'Poisonous Breath', 'Shadow Scales Of Deception', 'Wings', 725, 390, 340, 44, 1010, 337, 0, 5, 1);
INSERT INTO `$monsters` VALUES (130, 'Liche King', 'Tainted Staff Of The \r\nUnholy', 'Royal Dark Armor', 'None', 325, 305, 270, 44, 1050, 350, 0, 2, 1);
INSERT INTO `$monsters` VALUES (131, 'Incubus', 'Death Sword', 'Skull \r\nArmor', 'Dark Shield', 330, 305, 475, 44, 1100, 367, 0, 2, 1);
INSERT INTO `$monsters` VALUES (132, 'Traitor', 'Disgraceful Flamberge', 'Black Dragon Leather', 'None', 330, 305, 375, 45, 1150, 383, 0, 0, 0);
INSERT INTO `$monsters` VALUES (133, 'Demon', 'Dark Claws', 'Fire Armor', 'Dark Round Shield', 340, 410, 380, 45, 1200, 400, 0, 1, 0);
INSERT INTO `$monsters` VALUES (134, 'Dark Dragon', 'Black Flame Breath', 'Dark Scales', 'Wings', 745, 545, 420, 45, 1250, 417, 0, 5, 1);
INSERT INTO `$monsters` VALUES (135, 'Insurgent', 'Death Whip', 'Mithril \r\nChain', 'None', 450, 420, 490, 46, 1300, 433, 0, 0, 0);
INSERT INTO `$monsters` VALUES (136, 'Leviathan', 'Unholy Staff', 'Dark \r\nPlate', 'None', 355, 325, 390, 46, 1350, 450, 0, 0, 0);
INSERT INTO `$monsters` VALUES (137, 'Grey Daemon', 'Grey Rapier', 'Daemon \r\nChain', 'Misty Shield', 360, 330, 490, 46, 1400, 467, 0, 2, 0);
INSERT INTO `$monsters` VALUES (138, 'Succubus', 'Opal Blade', 'Unholy \r\nPlate Mail', 'None', 465, 340, 400, 47, 1360, 487, 0, 3, 1);
INSERT INTO `$monsters` VALUES (139, 'Demon Prince', 'Sword Of \r\nDestruction', 'Dark Chain', 'None', 370, 440, 350, 47, 1420, 507, 0, 3, 0);
INSERT INTO `$monsters` VALUES (140, 'Black Dragon', 'Black Claws', 'Black', 'Wings', 875, 430, 415, 47, 1580, 527, 0, 6, 2);
INSERT INTO `$monsters` VALUES (141, 'Nihilist', 'Skull Sword', 'Baby \r\nDragon Armor', 'Death Round Shield', 380, 450, 475, 47, 1540, 547, 0, 0, 0);
INSERT INTO `$monsters` VALUES (142, 'Behemoth', 'Poisoned Claws', 'Dark \r\nThick Hide', 'None', 385, 460, 480, 48, 1600, 567, 0, 2, 0);
INSERT INTO `$monsters` VALUES (143, 'Demagogue', 'Earth Blade', 'Nature \r\nChain', 'RedWood Shield', 390, 360, 480, 48, 1660, 587, 0, 0, 0);
INSERT INTO `$monsters` VALUES (144, 'Demon Lord', 'Demon Haliber', 'Dark \r\nPlate', 'Soul Shield', 400, 370, 520, 48, 1720, 607, 0, 3, 2);
INSERT INTO `$monsters` VALUES (145, 'Red Daemon', 'Inferno Whip', 'None', 'Flame Shield', 410, 380, 490, 48, 1780, 627, 0, 2, 0);
INSERT INTO `$monsters` VALUES (146, 'Colossus', 'Giant Cub', 'Black \r\nDragon Leather', 'None', 420, 410, 490, 49, 1840, 647, 0, 2, 0);
INSERT INTO `$monsters` VALUES (147, 'Demon King', 'Demon Excalibur', 'Royal Unholy Armor', 'Spiked Shield', 430, 400, 550, 49, 2000, 667, 0, 2, 0);
INSERT INTO `$monsters` VALUES (148, 'Dark Daemon', 'Dark Saber', 'Dark \r\nChain', 'None', 440, 420, 560, 50, 2100, 733, 0, 2, 1);
INSERT INTO `$monsters` VALUES (149, 'Titan', 'Eternal Sword', 'Godly \r\nPlate Mail', 'None', 460, 440, 490, 50, 2300, 800, 0, 3, 0);
INSERT INTO `$monsters` VALUES (150, 'Black Daemon', 'Deadly Battle Axe', 'Black Chain', 'Black Kite Shield', 400, 400, 620, 51, 2700, 1000, 0, 3, 1);
INSERT INTO `$monsters` VALUES (151, 'Hydra', 'Two-Headed Fangs', 'Thick \r\nScale Hide', 'None', 555, 450, 555, 52, 1915, 1010, 0, 0, 1);
INSERT INTO `$monsters` VALUES (152, 'Phalanx', 'Spears', 'Iron Armor', 'Iron Kite Shield', 562, 465, 562, 53, 1920, 1015, 0, 0, 2);
INSERT INTO `$monsters` VALUES (153, 'Warlord', 'Blood Sword', 'Death \r\nChain', 'Death Shield', 575, 460, 575, 54, 1925, 1020, 0, 2, 1);
INSERT INTO `$monsters` VALUES (154, 'Nyad', 'Fists', 'Silk Gown', 'None', 580, 470, 580, 55, 1930, 1025, 0, 0, 2);
INSERT INTO `$monsters` VALUES (155, 'Griffin', 'Talons', 'Thick Feathered \r\nHide', 'Wings', 580, 472, 580, 56, 1935, 1030, 0, 0, 1);
INSERT INTO `$monsters` VALUES (156, 'Dryad', 'Star Dagger', 'Silk Star \r\nGown', 'Tiny Arm Buckler', 569, 485, 569, 57, 1940, 1035, 0, 0, 2);
INSERT INTO `$monsters` VALUES (157, 'Overseer', 'Onyx Staff', 'Celtic \r\nRobes', 'None', 590, 496, 590, 58, 1945, 1040, 0, 3, 1);
INSERT INTO `$monsters` VALUES (158, 'Guardian', 'Sword of Stone', 'Armor \r\nof Stone', 'Shield of Stone', 602, 490, 602, 59, 1950, 1045, 0, 3, 2);
INSERT INTO `$monsters` VALUES (159, 'Basilisk', 'Poisonous Breath', 'Thick Dragon Hide', 'None', 600, 500, 600, 60, 1955, 1050, 0, 0, 1);
INSERT INTO `$monsters` VALUES (160, 'Spitting Cobra', 'Poison Spit', 'Scale Hide', 'None', 650, 510, 650, 61, 1960, 1055, 0, 1, 2);
INSERT INTO `$monsters` VALUES (161, 'Troll', 'Mithril Club', 'Grey Hide', 'None', 650, 515, 650, 61, 1965, 1060, 0, 0, 1);
INSERT INTO `$monsters` VALUES (162, 'Dark Giant', 'Shadow Warhammer', 'Dark Leather', 'Shadow Arm Buckler', 640, 512, 640, 62, 1970, 1065, 0, 2, 2);
INSERT INTO `$monsters` VALUES (163, 'Serpant', 'Poisonous Fangs', 'Scale \r\nHide', 'None', 625, 525, 625, 62, 1975, 1070, 0, 1, 1);
INSERT INTO `$monsters` VALUES (164, 'Python', 'Sharp Fangs', 'Thick Scale \r\nHide', 'None', 638, 530, 638, 63, 1980, 1075, 0, 1, 2);
INSERT INTO `$monsters` VALUES (165, 'Centuar', 'Woodland Longbow', 'Hard \r\nLeather', 'None', 655, 525, 655, 64, 1985, 1080, 0, 0, 0);
INSERT INTO `$monsters` VALUES (166, 'Beserker', 'Double-sided Battleaxe', 'Shadow Leather', 'None', 680, 560, 680, 64, 1990, 1085, 0, 0, 0);
INSERT INTO `$monsters` VALUES (167, 'Red Mamba', 'Poisonous Venom', 'Dark \r\nRed Scales', 'None', 704, 520, 704, 65, 1995, 1090, 0, 0, 0);
INSERT INTO `$monsters` VALUES (168, 'Deadly Spider', 'Poisonous Bite', 'Thick Spider Hide', 'None', 695, 560, 695, 65, 2000, 1095, 0, 1, 1);
INSERT INTO `$monsters` VALUES (169, 'Thor', 'Mjolner Hammer of Thunder', 'Bronze Chain', 'None', 750, 565, 750, 66, 2005, 1100, 0, 0, 2);
INSERT INTO `$monsters` VALUES (170, '4 Headed Hydra', 'Four-Headed \r\nFangs', 'Dark Thick Hide', 'None', 740, 575, 740, 66, 2010, 1105, 0, 0, 2);
INSERT INTO `$monsters` VALUES (171, 'Dark Minion', 'Nine-Tailed Whip', 'Dark Chain', 'Dark Arm-Buckler', 750, 570, 750, 67, 2015, 1110, 0, 3, 1);
INSERT INTO `$monsters` VALUES (172, 'Ghost Lord', 'Spirit Scimitar', 'Spirit Captain Gear', 'Spirit Buckler', 721, 581, 721, 68, 2020, 1115, 0, 0, 1);
INSERT INTO `$monsters` VALUES (173, 'Chief Warlord', 'Doom Hammer', 'Doom \r\nPlate', 'Doom Shield', 715, 574, 715, 69, 2025, 1120, 0, 3, 2);
INSERT INTO `$monsters` VALUES (174, 'Soul Caster', 'Soul Catcher Hook', 'Soul Plate', 'Shield of Souls', 745, 590, 745, 70, 2030, 1125, 0, 0, 1);
INSERT INTO `$monsters` VALUES (175, 'Mountain Troll', 'Giant Stone Club', 'Thick Grey Hide', 'None', 769, 600, 769, 70, 2035, 1130, 0, 2, 2);
INSERT INTO `$monsters` VALUES (176, 'Mountain Goat', 'Goat Horns', 'Soft \r\nHide', 'None', 781, 605, 781, 70, 2040, 1135, 0, 1, 1);
INSERT INTO `$monsters` VALUES (177, 'Lebrachaun', 'Wooden Cane', 'Green \r\nClothes', 'None', 775, 602, 775, 71, 2045, 1140, 0, 1, 0);
INSERT INTO `$monsters` VALUES (178, 'Tyr', 'Fists', 'Bronze Chain', 'None', 779, 615, 779, 71, 2050, 1145, 0, 0, 1);
INSERT INTO `$monsters` VALUES (179, 'Drunken Pirate', 'Half-Empty \r\nBottle', 'Pirate Clothes', 'Wooden Shield', 790, 612, 790, 72, 2055, 1150, 0, 1, 2);
INSERT INTO `$monsters` VALUES (180, 'Giant Squid', 'Giant Tentacles', 'Jelly Body', 'None', 798, 625, 798, 73, 2060, 1155, 0, 0, 2);
INSERT INTO `$monsters` VALUES (181, 'Red Orc', 'Orc Scimitar', 'Red Orc \r\nPlate', 'Red Orc Shield', 803, 630, 803, 73, 2065, 1160, 0, 2, 1);
INSERT INTO `$monsters` VALUES (182, 'Lava Golem', 'Molten Rock-Fists', 'Molten Rock Body', 'Molten Rock Arms', 805, 635, 805, 74, 2070, 1165, 0, 0, 1);
INSERT INTO `$monsters` VALUES (183, 'Giant Wurm', 'Giant Mouth', 'Squirmy \r\nHide', 'None', 832, 680, 832, 74, 2075, 1170, 0, 0, 1);
INSERT INTO `$monsters` VALUES (184, 'Anaconda', 'Giant Fangs', 'Scale \r\nHide', 'None', 840, 650, 840, 75, 2080, 1175, 0, 2, 1);
INSERT INTO `$monsters` VALUES (185, 'Reckless Phalanx', 'Steel Swords', 'Steel Armor', 'Steel Shield', 850, 630, 850, 76, 2085, 1180, 0, 0, 1);
INSERT INTO `$monsters` VALUES (186, '5 Headed Hydra', 'Five-Headed \r\nFangs', 'Dark Thick Hide', 'None', 845, 650, 845, 77, 2090, 1185, 0, 0, 1);
INSERT INTO `$monsters` VALUES (187, 'Lava Ghost', 'Fire Spitter', 'Astral \r\nBody', 'None', 863, 660, 863, 77, 2095, 1190, 0, 0, 1);
INSERT INTO `$monsters` VALUES (188, 'Chaos Giant', 'Chaos Club', 'Chaos \r\nLeather', 'None', 888, 665, 888, 78, 2100, 1195, 0, 3, 2);
INSERT INTO `$monsters` VALUES (189, 'Abyss Troll', 'Twisted Root-Club', 'Hard Twisted Leather', 'None', 845, 670, 845, 79, 2105, 1200, 0, 1, 2);
INSERT INTO `$monsters` VALUES (190, 'King Cobra', 'Fangs of Venom', 'Thick Scale Hide', 'None', 895, 680, 895, 80, 2110, 1205, 0, 2, 2);
INSERT INTO `$monsters` VALUES (191, 'Black Mamba', 'Poisonous Spit', 'Black Scale Hide', 'None', 901, 690, 901, 80, 2115, 1210, 0, 2, 0);
INSERT INTO `$monsters` VALUES (192, 'High Lord', 'Long Sword of the \r\nLords', 'Elegant Robes', 'Shield of Lords', 950, 698, 950, 80, 2120, 1215, 0, 3, 2);
INSERT INTO `$monsters` VALUES (193, 'Elder', 'Oak Staff', 'Cloak of the \r\nForest', 'None', 941, 705, 941, 81, 2125, 1220, 0, 0, 1);
INSERT INTO `$monsters` VALUES (194, 'Supreme Warlord', 'Dagger of \r\nSilence', 'Shadow Cloak', 'Shadow Buckler', 924, 710, 924, 82, 2130, 1225, 0, 3, 1);
INSERT INTO `$monsters` VALUES (195, 'Demigod', 'Desirk Sword', 'Desirk \r\nPlatemail', 'Desirk Shield', 905, 725, 905, 83, 2135, 1230, 0, 2, 1);
INSERT INTO `$monsters` VALUES (196, 'Undead Giant', 'Battered Club', 'Torn Rags', 'None', 935, 725, 935, 84, 2140, 1235, 0, 0, 1);
INSERT INTO `$monsters` VALUES (197, 'Soul Searcher', 'Soul Hook', 'Soul \r\nPlate', 'Soul Shield', 956, 730, 956, 84, 2145, 1240, 0, 0, 1);
INSERT INTO `$monsters` VALUES (198, 'Yetter', 'Twin Daggers', 'Black \r\nLeather', 'Bone Shield', 962, 735, 962, 84, 2150, 1245, 0, 0, 2);
INSERT INTO `$monsters` VALUES (199, 'Dark Goblin', 'Poison-Tipped Spear', 'Black Rags', 'None', 978, 725, 978, 85, 2155, 1250, 0, 2, 2);
INSERT INTO `$monsters` VALUES (200, 'Merdusa', 'Sword & Bow', 'Leather & \r\nScales', 'Arm-Gauntlet Buckler', 1080, 1140, 980, 86, 2160, 1255, 0, 2, 2);
INSERT INTO `$monsters` VALUES (201, 'Winged Snake', 'Serpent Fangs', 'Green Scale Hide', 'Wings', 985, 1145, 985, 87, 2165, 1260, 0, 2, 0);
INSERT INTO `$monsters` VALUES (202, '6 Headed Hydra', 'Six-Headed Fangs', 'Thick Grey Hide', 'None', 1196, 745, 996, 88, 2170, 1265, 0, 0, 0);
INSERT INTO `$monsters` VALUES (203, 'Undead Lord', 'Shadow Two-handed \r\nSword', 'Shadow Steel Armor', 'Shadow Buckler', 1150, 750, 950, 89, 2175, 1270, 0, 0, 1);
INSERT INTO `$monsters` VALUES (204, 'Undead Dragon', 'Poisonous Breath', 'Dragon Bone Body', 'Bone Wings', 3199, 795, 1009, 90, 2180, 1275, 0, 0, 2);
INSERT INTO `$monsters` VALUES (205, 'Undead Corpse', 'Cracked Club', 'Tattered Clothes', 'None', 1165, 780, 965, 90, 2185, 1280, 0, 0, 1);
INSERT INTO `$monsters` VALUES (206, 'Giant Minotaur', 'Magic Longbow', 'Black Leather Vest', 'Black Arm-Buckler', 1122, 790, 1002, 91, 2190, 1285, 0, 4, 2);
INSERT INTO `$monsters` VALUES (207, 'Guardian Wurm', 'Sticky Salvia', 'Squirmy Hide', 'None', 1110, 780, 1010, 91, 2195, 1290, 0, 0, 2);
INSERT INTO `$monsters` VALUES (208, 'Giant Elder', 'Staff of Nature', 'Cloak of Nature', 'None', 1216, 790, 1016, 92, 2200, 1295, 0, 0, 1);
INSERT INTO `$monsters` VALUES (209, 'King Hydra', 'Nine-Headed Fangs', 'Dark-Blue Thick Hide', 'None', 1512, 798, 1012, 93, 2205, 1300, 0, 0, 2);
INSERT INTO `$monsters` VALUES (210, 'Frozen Gaunt', 'Six-inch Knife', 'Cracked Frozen Cloak', 'None', 1230, 804, 1030, 94, 2210, 1305, 0, 0, 1);
INSERT INTO `$monsters` VALUES (211, 'Evil Spirit', 'Dark Spirit Blade', 'Spirit Cage', 'Dark Spirit Shield', 1225, 815, 1025, 94, 2215, 1310, 0, 0, 1);
INSERT INTO `$monsters` VALUES (212, 'Mystic Demigod', 'Masitoc Spear', 'Masitoc Chain', 'Masitoc Kite Shield', 1236, 820, 1036, 95, 2220, 1315, 0, 0, 2);
INSERT INTO `$monsters` VALUES (213, 'Lost Soul', 'None', 'None', 'None', 1252, 805, 1052, 95, 2225, 1320, 0, 0, 1);
INSERT INTO `$monsters` VALUES (214, 'Dark Wizard', 'Dark Bone Wand', 'Robe of Shadows', 'None', 1245, 825, 1045, 96, 2230, 1325, 0, 0, 2);
INSERT INTO `$monsters` VALUES (215, 'Shape Shifter', 'None', 'None', 'None', 1269, 830, 1069, 97, 2235, 1330, 0, 0, 0);
INSERT INTO `$monsters` VALUES (216, 'Dark Mountain Goat', 'Dark Horns', 'Soft Hide', 'None', 1375, 838, 1075, 97, 2240, 1335, 0, 2, 0);
INSERT INTO `$monsters` VALUES (217, 'Death Dragon', 'Black-Fire Breath', 'Black Scales', 'Wings', 4285, 1245, 1385, 98, 2245, 1340, 0, 6, 1);
INSERT INTO `$monsters` VALUES (218, 'Venom Spider', 'Poisonous Bite', 'Spider Hide', 'None', 1075, 850, 1075, 99, 2250, 1345, 0, 1, 1);
INSERT INTO `$monsters` VALUES (219, 'Death Lord', 'Bastard Sword of \r\nHate', 'Plate of Darkness', 'Shield of Darkness', 1082, 853, 1082, 100, 2255, 1350, 0, 2, 2);
INSERT INTO `$monsters` VALUES (220, 'Fire Gaunt', 'Fire Spitter', 'Tattered Rags', 'None', 1295, 860, 1095, 101, 2260, 1355, 0, 0, 2);
INSERT INTO `$monsters` VALUES (221, 'Dark Yetter', 'Steel Long Sword', 'Steel Plate', 'Steel Kite Shield', 1391, 870, 1091, 101, 2265, 1360, 0, 0, 2);
INSERT INTO `$monsters` VALUES (222, 'Death Bat', 'Fangs', 'Semi-Thick \r\nHide', 'Wings', 1299, 875, 1099, 102, 2270, 1365, 0, 1, 2);
INSERT INTO `$monsters` VALUES (223, 'Forsaken', 'Blade of the Lost', 'Plate of the Lost', 'Shield of the Lost', 1302, 891, 1102, 103, 2275, 1370, 0, 0, 2);
INSERT INTO `$monsters` VALUES (224, 'Chaos Lord', 'Chaos Battle Axe', 'Chaos Battle Plate', 'Chaos Kite Shield', 1360, 908, 1160, 104, 2280, 1375, 0, 3, 2);
INSERT INTO `$monsters` VALUES (225, 'Gold Slime', 'None', 'None', 'None', 1550, 910, 1205, 105, 2003, 2850, 0, 0, 2);
INSERT INTO `$monsters` VALUES (226, 'Necromancer', 'Bone Staff', 'Cloak \r\nof Flesh', 'None', 1550, 950, 1250, 105, 2290, 1500, 0, 2, 2);
INSERT INTO `$monsters` VALUES (227, 'Ranger', 'Bow of Accuracy', 'Forest \r\nLeather', 'None', 1347, 960, 1247, 106, 2295, 1500, 0, 2, 2);
INSERT INTO `$monsters` VALUES (228, 'Paladin', 'Excalibur', 'Holy Plate', 'Holy Kite Shield', 1435, 940, 1235, 106, 2300, 1500, 0, 2, 2);
INSERT INTO `$monsters` VALUES (229, 'Barbarian', 'Double-sided \r\nBattleaxe', 'Bear Hide', 'None', 1750, 955, 1400, 107, 2305, 1500, 0, 2, 2);
INSERT INTO `$monsters` VALUES (230, 'Sorceress', 'Staff of Magic', 'Light \r\nRobes', 'None', 1254, 975, 1254, 108, 2310, 1500, 0, 2, 0);
INSERT INTO `$monsters` VALUES (231, 'Luciuege', 'Fists', 'Rags', 'None', 1656, 970, 1356, 109, 2315, 1550, 0, 1, 2);
INSERT INTO `$monsters` VALUES (232, 'Golem Of Fire', 'Fireballs', 'Rock-Hard Body', 'Rock-Hard Arms', 1950, 1000, 1450, 110, 2320, 1600, 0, 0, 1);
INSERT INTO `$monsters` VALUES (233, 'King Black Dragons Young', 'Razor', 'Scale Hide', 'Wings', 7155, 3950, 4555, 110, 3325, 2650, 0, 9, 1);
INSERT INTO `$monsters` VALUES (234, 'Dark Titan', 'Club of the Stars', 'Midnight Leather', 'None', 2120, 1200, 1620, 111, 2330, 1700, 0, 4, 1);
INSERT INTO `$monsters` VALUES (235, 'Ent', 'Thick-Wooden Hands', 'Thick \r\nBark', 'None', 3090, 1321, 1690, 112, 2335, 1750, 0, 3, 0);
INSERT INTO `$monsters` VALUES (236, 'Golem Of Darkness', 'Black \r\nFireballs', 'Rock-Hard Body', 'Rock-Hard Arms', 3154, 1456, 1654, 113, 2340, 1800, 0, 0, 2);
INSERT INTO `$monsters` VALUES (237, 'Fallen Angel', 'Sabre of the \r\nFallen', 'Plate of the Fallen', 'Shield of the Fallen', 2598, 1598, 1698, 113, 2345, 1850, 0, 0, 2);
INSERT INTO `$monsters` VALUES (238, 'Black Gargoyle', 'Razor-Sharp \r\nClaws', 'Thick Rock-like Hide', 'None', 2800, 1698, 1700, 114, 2350, 1900, 0, 0, 2);
INSERT INTO `$monsters` VALUES (239, 'Dark Succubus', 'Dagger of \r\nSeduction', 'Black Leather', 'None', 2952, 1786, 1752, 115, 2355, 1950, 0, 4, 2);
INSERT INTO `$monsters` VALUES (240, 'Odin', 'Odins Blade', 'Iron Will \r\nArmor', 'None', 3260, 1800, 1760, 116, 2360, 2000, 0, 2, 1);
INSERT INTO `$monsters` VALUES (241, 'Deformed Ape', 'Fists', 'Semi-Thick \r\nHide', 'None', 3921, 1805, 1721, 117, 2365, 2050, 0, 3, 2);
INSERT INTO `$monsters` VALUES (242, 'Escaped Psychopath', 'Throwing \r\nKnives', 'Cloak of Silence', 'None', 4105, 1890, 1705, 117, 2370, 2100, 0, 1, 2);
INSERT INTO `$monsters` VALUES (243, 'Demigoddess', 'Dervas Blade', 'Dervas Chain', 'Dervas Shield', 5760, 1965, 1760, 118, 2375, 2200, 0, 2, 2);
INSERT INTO `$monsters` VALUES (244, 'King Black Dragons Guard', 'Blade of \r\nDragons', 'Black Dragon Hide', 'Shadow Shield', 7509, 2050, 2309, 119, 2500, 2670, 0, 7, 2);
INSERT INTO `$monsters` VALUES (245, 'King Black Dragons Minions', 'Spear \r\nof Poison', 'Armor of Wisdom', 'None', 8505, 2275, 2505, 119, 2850, 3000, 0, 8, 2);
INSERT INTO `$monsters` VALUES (246, 'King Black Dragon', 'Flame Breath of Poison', 'Scales of the Dragon God', 'Wings of the Dragon God', 14250, 6605, 7655, 120, 9000, 12000, 0, 12, 2);
INSERT INTO `$monsters` VALUES (247, 'Venomous Slime', 'None', 'None', 'None', 140, 83, 64, 19, 0, 0, 1, 0, 2);
INSERT INTO `$monsters` VALUES (248, 'Dead Corpse', 'A Limb', 'Rags', 'Monsters Head', 325, 131, 72, 31, 2652, 3717, 1, 0, 2);
INSERT INTO `$monsters` VALUES (249, 'Castle Knight', 'Long Sword', 'Chain Mail', 'Iron Shield', 640, 421, 569, 37, 0, 0, 1, 2, 2);
END;
if (dobatch($query) == 1) { echo "Monsters table populated.<br />"; } else { echo "Error populating Monsters table."; }
unset($query);









#### End 
    global $start;
    $time = round((getmicrotime() - $start), 4);
    echo "<br />Database setup part B complete in $time seconds.<br /><br /><a href=\"install.php?page=4\">Click here to continue with part C of the installation.</a></body></html>";
    die();
    
}

function fourth() { // Fourth page - set up the database tables.
    
    global $dbsettings;
    echo "<html><head><title>DK Installation</title></head><body><b>DK Installation: Page Four, Part C</b><br /><br />";
    $prefix = $dbsettings["prefix"];
	$arena = $prefix . "_arena";
	$chat = $prefix . "_chat";
    $comments = $prefix . "_comments";
    $control = $prefix . "_control";
    $crafting = $prefix . "_crafting";	
    $drops = $prefix . "_drops";
    $duel = $prefix . "_duel";
    $endurance = $prefix . "_endurance";
	$forging = $prefix . "_forging";
	$gamemail = $prefix . "_gamemail";
	$general = $prefix . "_general";
	$gforum = $prefix . "_gforum";
    $guilds = $prefix . "_guilds";
    $homes = $prefix . "_homes";
	$inventitems = $prefix . "_inventitems";
    $items = $prefix . "_items";
    $itemstorage = $prefix . "_itemstorage";
    $jewellery = $prefix . "_jewellery";
    $levels = $prefix . "_levels";
    $marketforum = $prefix . "_marketforum";
	$mining = $prefix . "_mining";
    $monsters = $prefix . "_monsters";
    $news = $prefix . "_news";
	$playermarket = $prefix . "_playermarket";
	$poll = $prefix . "_poll";
	$smelting = $prefix . "_smelting";
	$souls = $prefix . "_souls";
    $spells = $prefix . "_spells";
	$staff = $prefix . "_staff";
	$strongholds = $prefix . "_strongholds";
	$suggestions = $prefix . "_suggestions";
	$support = $prefix . "_support";
    $towns = $prefix . "_towns";
    $users = $prefix . "_users";
    
    if (isset($_POST["complete"])) { $full = true; } else { $full = false; }
    


$query = <<<END
CREATE TABLE `$news` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `author` varchar(30) NOT NULL default 'Adam',
  `title` varchar(30) NOT NULL default '',
  `postdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `content` text NOT NULL,
  `month` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)

) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "News table created.<br />"; } else { echo "Error creating News table."; }
unset($query);

$query = <<<END
INSERT INTO `$news` VALUES (1, 'Admin', 'First Post', '2006-06-15 20:27:46', 'Welcome to the DK Script Demo. This is your first news post. You can edit this or post more news within the admin panel.', 0);
END;
if (dobatch($query) == 1) { echo "News table populated.<br />"; } else { echo "Error populating News table."; }
unset($query);


$query = <<<END
CREATE TABLE `$playermarket` (
  `pmid` int(11) NOT NULL auto_increment,
  `playerid` int(11) NOT NULL default '0',
  `itemtype` int(11) NOT NULL default '0',
  `itemid` int(11) NOT NULL default '0',
  `itemprice` int(20) NOT NULL default '0',
  `datelisted` int(14) NOT NULL default '0',
  `endtime` int(14) NOT NULL default '0',
  `comments` varchar(255) default NULL,
  PRIMARY KEY  (`pmid`)

) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Player Market table created.<br />"; } else { echo "Error creating Player Market table."; }
unset($query);




$query = <<<END

CREATE TABLE `$poll` (
  `id` int(10) NOT NULL auto_increment,
  `type` tinyint(3) NOT NULL default '2',
  `question` varchar(255) NOT NULL default '',
  `ans1` varchar(255) NOT NULL default '',
  `ans2` varchar(255) NOT NULL default '',
  `ans3` varchar(255) NOT NULL default '',
  `ans4` varchar(255) NOT NULL default '',
  `open_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `closed_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `closed_cache` varchar(255) NOT NULL default '',
  `voter` int(10) NOT NULL default '0',
  `parent` int(10) NOT NULL default '0',
  UNIQUE KEY `id` (`id`)

) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Poll table created.<br />"; } else { echo "Error creating Poll table."; }
unset($query);

$query = <<<END
CREATE TABLE `$smelting` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(30) NOT NULL default '',
  `level` int(3) NOT NULL default '1',
  `ore1` int(3) NOT NULL default '0',
  `ore2` int(3) NOT NULL default '0',
  `ore3` int(3) NOT NULL default '0',
  `ore4` int(3) NOT NULL default '0',
  `ore5` int(3) NOT NULL default '0',
  `ore6` int(3) NOT NULL default '0',
  `ore7` int(3) NOT NULL default '0',
  `ore8` int(3) NOT NULL default '0',
  `ore9` int(3) NOT NULL default '0',
  `ore10` int(3) NOT NULL default '0',
  `ore11` int(3) NOT NULL default '0',
  `ore12` int(3) NOT NULL default '0',
  `ore13` int(3) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Smelting table created.<br />"; } else { echo "Error creating Smelting table."; }
unset($query);

$query = <<<END
INSERT INTO `$smelting` VALUES (1, 'Bronze Bar', 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$smelting` VALUES (2, 'Iron Bar', 30, 0, 1, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$smelting` VALUES (3, 'Magic Bar', 45, 0, 0, 2, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$smelting` VALUES (4, 'Dark Bar', 65, 0, 0, 2, 0, 3, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$smelting` VALUES (5, 'Bright Bar', 80, 0, 0, 2, 0, 0, 3, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO `$smelting` VALUES (6, 'Destiny Bar', 100, 0, 0, 2, 0, 0, 0, 3, 0, 0, 0, 0, 0, 0);
INSERT INTO `$smelting` VALUES (7, 'Crystal Bar', 120, 0, 0, 3, 0, 0, 0, 0, 4, 0, 0, 0, 0, 0);
INSERT INTO `$smelting` VALUES (8, 'Diamond Bar', 145, 0, 0, 3, 0, 0, 0, 0, 0, 4, 0, 0, 0, 0);
INSERT INTO `$smelting` VALUES (9, 'Heros Bar', 175, 0, 0, 3, 0, 0, 0, 0, 0, 0, 4, 0, 0, 0);
INSERT INTO `$smelting` VALUES (10, 'Holy Bar', 195, 0, 0, 4, 0, 0, 0, 0, 0, 0, 0, 6, 0, 0);
INSERT INTO `$smelting` VALUES (11, 'Mythical Bar', 220, 0, 0, 4, 0, 0, 0, 0, 0, 0, 0, 0, 8, 0);
INSERT INTO `$smelting` VALUES (12, 'Black Dragons Bar', 245, 1, 1, 5, 0, 0, 0, 0, 0, 0, 0, 0, 0, 15);

END;
if (dobatch($query) == 1) { echo "Smelting table populated.<br />"; } else { echo "Error populating Smelting table."; }
unset($query);

$query = <<<END
CREATE TABLE `$souls` (
  `id` int(6) unsigned NOT NULL default '0',
  `hp` int(50) unsigned NOT NULL default '0',
  `def` int(50) unsigned NOT NULL default '0',
  `attack` int(50) unsigned NOT NULL default '0',
  `exp` int(50) unsigned NOT NULL default '0',
  `gold` int(50) unsigned NOT NULL default '0',
  `dscales` int(50) unsigned NOT NULL default '0',
  `type` tinyint(3) unsigned NOT NULL default '0'

) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Souls table created.<br />"; } else { echo "Error creating Souls table."; }
unset($query);






$query = <<<END
CREATE TABLE `$spells` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(30) NOT NULL default '',
  `mp` smallint(5) unsigned NOT NULL default '0',
  `attribute` smallint(5) unsigned NOT NULL default '0',
  `type` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Spells table created.<br />"; } else { echo "Error creating Spells table."; }
unset($query);

$query = <<<END
INSERT INTO `$spells` VALUES (1, 'Heal', 5, 10, 1);
INSERT INTO `$spells` VALUES (2, 'Revive', 10, 25, 1);
INSERT INTO `$spells` VALUES (3, 'Life', 20, 55, 1);
INSERT INTO `$spells` VALUES (4, 'Breath', 45, 135, 1);
INSERT INTO `$spells` VALUES (5, 'Healing Armor', 85, 275, 1);
INSERT INTO `$spells` VALUES (6, 'Hurt', 5, 15, 2);
INSERT INTO `$spells` VALUES (7, 'Pain', 12, 35, 2);
INSERT INTO `$spells` VALUES (8, 'Maim', 25, 70, 2);
INSERT INTO `$spells` VALUES (9, 'Rend', 40, 100, 2);
INSERT INTO `$spells` VALUES (10, 'Frozen Orb', 50, 130, 2);
INSERT INTO `$spells` VALUES (11, 'Sleep', 10, 6, 3);
INSERT INTO `$spells` VALUES (12, 'Dream', 30, 8, 3);
INSERT INTO `$spells` VALUES (13, 'Nightmare', 60, 11, 3);
INSERT INTO `$spells` VALUES (14, 'Craze', 10, 10, 4);
INSERT INTO `$spells` VALUES (15, 'Rage', 20, 25, 4);
INSERT INTO `$spells` VALUES (16, 'Fury', 30, 50, 4);
INSERT INTO `$spells` VALUES (17, 'Ward', 10, 10, 5);
INSERT INTO `$spells` VALUES (18, 'Fend', 20, 25, 5);
INSERT INTO `$spells` VALUES (19, 'Barrier', 30, 50, 5);
INSERT INTO `$spells` VALUES (20, 'Dark Spirit', 85, 13, 3);
INSERT INTO `$spells` VALUES (21, 'Summon Black Dragon''s Soul', 105, 85, 4);
INSERT INTO `$spells` VALUES (22, 'Magic Bolt', 9, 20, 2);
INSERT INTO `$spells` VALUES (23, 'Fire Bolt', 18, 52, 2);
INSERT INTO `$spells` VALUES (24, 'Cold Bolt', 33, 85, 2);
INSERT INTO `$spells` VALUES (25, 'Multiple Strike', 39, 100, 2);
INSERT INTO `$spells` VALUES (26, 'Frozen Strike', 45, 115, 2);
INSERT INTO `$spells` VALUES (27, 'Guided Shot', 38, 55, 4);
INSERT INTO `$spells` VALUES (28, 'Exploding Strike', 70, 185, 2);
INSERT INTO `$spells` VALUES (29, 'Dodge', 38, 55, 5);
INSERT INTO `$spells` VALUES (30, 'Evade', 45, 60, 5);
INSERT INTO `$spells` VALUES (31, 'Penetrate', 60, 70, 4);
INSERT INTO `$spells` VALUES (32, 'Bone Spirit', 65, 155, 2);
INSERT INTO `$spells` VALUES (33, 'Weaken', 35, 77, 5);
INSERT INTO `$spells` VALUES (34, 'Bone Shield', 42, 90, 5);
INSERT INTO `$spells` VALUES (35, 'Summon Golem', 40, 60, 4);
INSERT INTO `$spells` VALUES (36, 'Amplify Damage', 60, 76, 4);
INSERT INTO `$spells` VALUES (37, 'Shout', 20, 27, 5);
INSERT INTO `$spells` VALUES (38, 'War Cry', 30, 53, 5);
INSERT INTO `$spells` VALUES (39, 'Frenzy', 30, 52, 4);
INSERT INTO `$spells` VALUES (40, 'Torture', 60, 11, 3);
INSERT INTO `$spells` VALUES (41, 'Healing Aura', 45, 136, 1);
INSERT INTO `$spells` VALUES (42, 'Fanaticism Aura', 20, 26, 4);
INSERT INTO `$spells` VALUES (43, 'Swipe', 5, 15, 2);
INSERT INTO `$spells` VALUES (44, 'Fissure Attack', 25, 72, 2);
INSERT INTO `$spells` VALUES (45, 'Armageddon', 40, 105, 2);
INSERT INTO `$spells` VALUES (46, 'Wind Shield', 35, 77, 5);
INSERT INTO `$spells` VALUES (47, 'Hurricane Armor', 42, 90, 5);
INSERT INTO `$spells` VALUES (48, 'Fury Swipe', 40, 60, 4);
INSERT INTO `$spells` VALUES (49, 'Raging Swipe', 60, 76, 4);
INSERT INTO `$spells` VALUES (51, 'Claw', 5, 15, 2);
INSERT INTO `$spells` VALUES (52, 'Claw Frenzy', 12, 36, 2);
INSERT INTO `$spells` VALUES (53, 'Blade Strike', 25, 72, 2);
INSERT INTO `$spells` VALUES (54, 'Phoenix Blade', 40, 105, 2);
INSERT INTO `$spells` VALUES (55, 'Dragons Claw', 50, 145, 2);
INSERT INTO `$spells` VALUES (56, 'Claw Block', 35, 77, 5);
INSERT INTO `$spells` VALUES (57, 'Fade', 42, 90, 5);
INSERT INTO `$spells` VALUES (58, 'Raging Claw', 40, 60, 4);
INSERT INTO `$spells` VALUES (59, 'Summon Dark Shadow', 60, 76, 4);
INSERT INTO `$spells` VALUES (60, 'Peasants Spell', 15, 38, 2);
INSERT INTO `$spells` VALUES (61, 'Slayers Spell', 30, 86, 2);
INSERT INTO `$spells` VALUES (62, 'Champions Spell', 45, 135, 2);
INSERT INTO `$spells` VALUES (63, 'Heros Spell', 60, 195, 2);
INSERT INTO `$spells` VALUES (64, 'Legends Spell', 95, 390, 2);
INSERT INTO `$spells` VALUES (65, 'Lvl15 Capture', 65, 16, 6);
INSERT INTO `$spells` VALUES (66, 'Lvl30 Capture', 90, 31, 6);
INSERT INTO `$spells` VALUES (67, 'Lvl45 Capture', 165, 46, 6);
INSERT INTO `$spells` VALUES (68, 'Lvl60 Capture', 210, 61, 6);
INSERT INTO `$spells` VALUES (69, 'Lvl75 Capture', 365, 76, 6);
INSERT INTO `$spells` VALUES (70, 'Lvl100 Capture', 505, 101, 6);
INSERT INTO `$spells` VALUES (71, 'Lvl120 Capture', 600, 121, 6);
END;
if (dobatch($query) == 1) { echo "Spells table populated.<br />"; } else { echo "Error populating Spells table."; }
unset($query);

$query = <<<END
CREATE TABLE `$staff` (
  `id` int(11) NOT NULL auto_increment,
  `postdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `newpostdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` varchar(30) NOT NULL default '',
  `parent` int(11) NOT NULL default '0',
  `replies` int(11) NOT NULL default '0',
  `close` tinyint(1) NOT NULL default '0',
  `pin` tinyint(1) NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `content` text NOT NULL,
  PRIMARY KEY  (`id`)

) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Staff Forum table created.<br />"; } else { echo "Error creating Staff Forum table."; }
unset($query);


$query = <<<END
CREATE TABLE `$strongholds` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `strholdname` varchar(15) NOT NULL default 'No Name',
  `latitude` smallint(5) NOT NULL default '0',
  `longitude` smallint(5) NOT NULL default '0',
  `guildname` varchar(30) NOT NULL default '-',
  `guildid` char(3) NOT NULL default '1',
  `founder` varchar(30) NOT NULL default 'Admin',
  `ruined` tinyint(3) NOT NULL default '0',
  `armor` smallint(5) unsigned NOT NULL default '100',
  `magic` smallint(5) unsigned NOT NULL default '100',
  `weaponry` smallint(5) unsigned NOT NULL default '50',
  `armorlevel` tinyint(3) unsigned NOT NULL default '1',
  `magiclevel` tinyint(3) unsigned NOT NULL default '1',
  `weaponrylevel` tinyint(3) unsigned NOT NULL default '1',
  `spells` varchar(50) NOT NULL default '0',
  `gold` mediumint(9) unsigned NOT NULL default '25000',
  `currenthp` smallint(6) unsigned NOT NULL default '990',
  `currentmp` smallint(6) unsigned NOT NULL default '500',
  `maxhp` smallint(6) unsigned NOT NULL default '1000',
  `maxmp` smallint(6) unsigned NOT NULL default '500',
  `experience` tinyint(3) unsigned NOT NULL default '0',
  `level` tinyint(3) unsigned NOT NULL default '2',
  `productivity` tinyint(4) NOT NULL default '50',
  `snails` smallint(5) unsigned NOT NULL default '25',
  `kelplings` smallint(5) unsigned NOT NULL default '25',
  `minnows` smallint(5) unsigned NOT NULL default '25',
  PRIMARY KEY  (`id`)

) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Strongholds table created.<br />"; } else { echo "Error creating Strongholds table."; }
unset($query);


$query = <<<END
CREATE TABLE `$suggestions` (
  `id` int(11) NOT NULL auto_increment,
  `postdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `newpostdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` varchar(30) NOT NULL default '',
  `parent` int(11) NOT NULL default '0',
  `replies` int(11) NOT NULL default '0',
  `close` tinyint(1) NOT NULL default '0',
  `pin` tinyint(1) NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `content` text NOT NULL,
  PRIMARY KEY  (`id`)

) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Suggestions Forum table created.<br />"; } else { echo "Error creating Suggestions Forum table."; }
unset($query);


$query = <<<END
CREATE TABLE `$support` (
  `id` int(11) NOT NULL auto_increment,
  `postdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `newpostdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` varchar(30) NOT NULL default '',
  `parent` int(11) NOT NULL default '0',
  `replies` int(11) NOT NULL default '0',
  `close` tinyint(1) NOT NULL default '0',
  `pin` tinyint(1) NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `content` text NOT NULL,
  PRIMARY KEY  (`id`)

) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Support Forum table created.<br />"; } else { echo "Error creating Support Forum table."; }
unset($query);


$query = <<<END
CREATE TABLE `$towns` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `name` varchar(30) NOT NULL default '',
  `owner` varchar(30) NOT NULL default 'Not Available',
  `latitude` smallint(6) NOT NULL default '0',
  `longitude` smallint(6) NOT NULL default '0',
  `innprice` smallint(4) NOT NULL default '0',
  `pool` smallint(3) NOT NULL default '15',
  `restap` smallint(4) NOT NULL default '50',
  `aleprice` smallint(4) NOT NULL default '0',
  `whiskprice` smallint(4) NOT NULL default '0',
  `dragprice` smallint(4) NOT NULL default '0',
  `ogreprice` smallint(4) NOT NULL default '0',
  `gobprice` varchar(4) NOT NULL default '0',
  `dragpotprice` varchar(4) NOT NULL default '0',
  `rank1` mediumint(7) NOT NULL default '30000',
  `rank2` mediumint(7) NOT NULL default '25000',
  `rank3` mediumint(7) NOT NULL default '20000',
  `mapprice` mediumint(7) NOT NULL default '0',
  `travelpoints` smallint(5) unsigned NOT NULL default '0',
  `itemslist` text NOT NULL,
  `inventitemslist` text NOT NULL,
  `jewellerylist` text NOT NULL,
  `description` text NOT NULL,
  `ts_description` text NOT NULL,
  `requirement` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Towns table created.<br />"; } else { echo "Error creating Towns table."; }
unset($query);

$query = <<<END
INSERT INTO `$towns` VALUES (1, 'Kingdom Of Valour', 'Not Available', 0, 0, 3, 15, 50, 15, 112, 125, 600, '600', '700', 30000, 25000, 20000, 1, 5, '1,2,3,17,18,19,28,29,38,65,66,89,101,119,131,143', '79,1,2,35,41,73,82,36', '1,2', 'The heart of the wilderness, a large and bustling city stands tall. Towers and bastions decorate the ramparts, and a solid portcullis guards the inner city from monsters. This is the Kingdom of Valour, the place where your journey begins.', 'A man, armed with a steel tipped spear and full plate armor, walks past you as you gaze around the town square of the Kingdom of Valour. Looking around, you see many sights... a comforting inn, a rowdy tavern, a hard-working blacksmith and a bustling market. Feeling slightly lost and bemused, you wander aimlessly, making your way towards whatever might take your fancy.', 0);
INSERT INTO `$towns` VALUES (2, 'Raminant', 'Not Available', 274, 172, 10, 15, 50, 17, 115, 132, 615, '615', '715', 30000, 25000, 20000, 50, 21, '2,3,4,18,19,29,39,67,68,102,120,132,144', '79,2,3,35,36,42,73,74,82,83', '3,4', 'Having travelled far to the north-east, a small village looms in front of you. Wooden gates open slowly as the guards realise you are no enemy, and you are quickly ushered inside the walls of Raminant.', 'As you look around the town square, the difference between this small village and the Kingdom of Valour is quickly apparent. Small urchins lie in doorways, sheltering from the elements, and the marketstalls are far more sparse. Despite this, there is far more warmth here than at the Kingdom, and you feel yourself relaxing instantly.', 0);
INSERT INTO `$towns` VALUES (3, 'Brisbann Woods', 'Not Available', 72, -251, 25, 15, 50, 112, 125, 142, 630, '630', '730', 30000, 25000, 20000, 115, 25, '2,3,4,5,18,19,20,29.30,69,70,103,121,133,145', '79,3,4,36,42,74,82,83', '3,4,5', 'Fatigued from your travels, you find yourself staring at a forest. Dismayed, you start towards them, thinking to continue your journey, but as you do, a gateway begins to appear in the trees. Quiet, unknown, yet highly eventful, the town of Brisbann Woods beckons you in.', 'Looking around you in amazement, you see a variety of stores intricately weaved between the trees, looking amazingly natural, yet completely habitable. A lithe Elven maiden walks up to you, offering you a tour of their small residence.', 0);
INSERT INTO `$towns` VALUES (4, 'Nemarik', 'Not Available', -324, 127, 40, 15, 50, 118, 132, 148, 645, '645', '745', 30000, 25000, 20000, 225, 30, '5,6,8,10,12,21,22,23,29,30,40,71,72,123,134,147', '79,2,3,4,33,42,74,82,83', '5,6', 'An arrow flies past, missing your shoulder by barely a whisker. Jumping back in shock, you see archers standing on the ramparts of a castle dwarfing the Kingdom of Valour. You stand before the gates of Nemarik, renowned for it`s fine weaponry, and busy lifestyle.', 'As you enter the town square of Nemarik, a small company of men walk briskly up to you, taking your hands and bundling you to the floor.\r\n\r\n"Apologies traveller, but this is town protocol. You will be required to leave your weapons at the gate, you may collect them as you leave."\r\n\r\nShaken but unharmed, you get up, relieved of your weapon and armor. The town remains inviting however, and the stores beckon.', 0);
INSERT INTO `$towns` VALUES (5, 'Narcillas Port', 'Not Available', -367, -296, 60, 15, 50, 135, 158, 180, 660, '660', '760', 30000, 25000, 20000, 500, 60, '4,7,9,11,13,21,22,23,29,30,31,73,74,104,124,135,148', '79,2,3,4,35,33,74,75,82,83', '3,4,6', 'Wind whistles in your ears, and you hear the cry of seagulls in the distance, waves cracking against the cliffs. A ship moves smoothly through the water, coming to rest at your next destination, Narcillas Port.', 'Strolling casually through the throng of people eager to board the large ships docking, you note many independent merchants plying their trade. The clean and fresh sea air fills your lungs, and you nearly miss a small thief attempting to run off with your wallet. Grabbing him by the collar, you retrieve your money, giving him a sharp cuff round the head before letting him run off.', 0);
INSERT INTO `$towns` VALUES (6, 'Abandoned Ruins', 'Not Available', 474, 364, 90, 15, 50, 150, 178, 1105, 675, '675', '775', 30000, 25000, 20000, 1000, 70, '10,11,12,13,14,23,24,30,31,41,75,76,105,125,136', '79,3,4,33,34,40,43,75,82,83', '5,6,7', 'A dry twig cracks under your feet as you move through the deathly quiet shell of what was once a house. Looking around, you see vast towers, some standing, some desecrated, and stones scattered across the floor. At the far end, some merchants have set up stalls, selling rare artifacts excavated from the Abandoned Ruins.', 'Noticing an artifact at your feet, you reach for it, thinking it may be valuable. As you take hold of it, however, it crumbles under your grasp. Cursing, you look around, seeing a few merchants with their wares.\r\n\r\n"No point looking for anything here, laddie... all the artifacts worth taking have been excavated. We would be more than happy to sell you some, though..."\r\n\r\nSighing, you go to look about their inventories.', 0);
INSERT INTO `$towns` VALUES (7, 'Bleadd Pit', 'Not Available', 33, -518, 100, 15, 50, 155, 185, 1115, 690, '690', '790', 30000, 25000, 20000, 5000, 110, '12,13,14,15,24,25,26,32,42,77,78,106,125,137', '79,3,4,34,43,74,75,82,83', '5,7', 'Having travelled to the west for longer than you remember, you stop briefly for a break. Feeling a rumble from somewhere, you move, but not before the ground opens up, dropping you over a thousand feet. Saying a prayer, you prepare for your death... and open your eyes, unharmed, in a vast cavern, lit by\r\nlow burning torches. A small gnome wanders up to you, greeting you to the western outpost of Bleadd Pit.', 'Shaking your head to get rid of the dizziness caused from the fall, you follow the gnome to the centre of the Pit. Well organized and efficient, gnomes run around everywhere, carrying metals and goods to stores, a continual flow of activity. You walk into the middle, and are nearly flattened by a horde of gnomes, all racing towards a funny looking tavern built into a mound of rock. Shaking your head, this time from amazement, you sit down, pondering where next to go in this quite incredible place.', 0);
INSERT INTO `$towns` VALUES (8, 'Nuberia', 'Not Available', 250, -289, 320, 15, 50, 175, 198, 1132, 705, '705', '805', 30000, 25000, 20000, 38000, 160, '16,27,33,43,45,50,79,80,126,138,139,149', '79,3,4,41,42,76,82,83', '4,5,6', 'A small plate drops from above, landing at your feet. Looking curiously at it, you wonder what it does, when suddenly it flies back up into the air, with you on board. Wondering if this is your end, you hold on for dear life, until you stop, alighting at the gates of a castle. You are now above the clouds, at the fabled city of Nuberia.', 'Looking around in complete shock, it slowly dawns on you that you are above the clouds. A golden skinned warrior, clad in silver armor, greets you to the town.\r\n\r\n"Good to see you, surface-dweller. Surprised to be here, eh? It happens... The equipment here is forged by masters, make sure you dont leave without purchasing some, ok?"', 0);
INSERT INTO `$towns` VALUES (9, 'Dragons Kingdom', 'Not Available', -7, 507, 1200, 15, 50, 1170, 1255, 1410, 800, '800', '900', 30000, 25000, 20000, 2755000, 245, '34,35,36,49,54,59,63,87,96,110,111,129,141,152', '79,5,6,8,37,44,57,40,43,78,86,87', '7,8,9', 'Colours alight all around you, blue, red, green, gold, silver, all blending together in a mystical dance. As they separate, you see their true forms emerging... you see the vast forms of Dragons flying above your head. One soars impossibly close to the ground, a vast gold, scooping you up solidly, yet gently, with his feet. As he takes you away, you realise you have stumbled across a mythical land, close to your destination... The Dragon''s Kingdom.', 'You suddenly feel very small, with a huge dragon escorting you through their domain. All the dragons outweigh you many times over, yet they all seem fearful of something. In perfect english, the Gold dragon tells you why.\r\n\r\n"You see friend... its the King Black Dragon. They are all fearful of him, and rightly so... he has slain many innocent dragons for no real reason. If you are thinking of confronting him, be very careful... he is truly dangerous. Rest here before continuing... and make good use of our stores."', 0);
INSERT INTO `$towns` VALUES (10, 'Black Dragons Lair', 'Not Available', -68, 554, 1350, 15, 50, 1250, 1360, 1525, 950, '950', '1050', 30000, 25000, 20000, 3972500, 275, '34,35,36,37,44,64,88,100,112,129,130,142,153', '79,5,6,8,44,40,43,78,86,87', '7,8,9,10', 'A bead of sweat drips from your forehead, trickling down your face, mirroring the water running down the cave ahead of you. This is the climax of your journey... this is what you have been working for. A deafening roar sounds, echoing around the vast mountainrange you stand in... You have arrived at the Black Dragon''s Lair.', 'A small man wanders up to you, seemingly blind, and carrying a small sack over his shoulder.\r\n\r\n"So, ye be seeking the King? He not be wanting guests today... walk warily, traveller. I be having any items ye might be needing, and ye can rest up here if ye need to. But take me advice friend, and dont be disturbing me King, cos he be mighty irritable when people try killing him..."', 0);
INSERT INTO `$towns` VALUES (11, 'Kayuga Forest', 'Not Available', 522, -379, 505, 15, 50, 1150, 1245, 1395, 750, '750', '850', 30000, 25000, 20000, 705000, 185, '46,51,55,56,60,81,82,97,98,107,126,140', '79,5,6,40,43,76,84', '7,8', 'A bird trills in the trees, singing happily. A small spring tinkles melodically, pure clear water inviting you to drink. Tired and weary, you advance... and the mirage disappears, leaving you with a dark, dank forest, and a host of strange looking half human, half horse creatures. This is Kayuga Forest, the home of the Centaurs.', 'A centaur offers you a large mug filled with a strange liquid... afraid to seem rude, you take a deep swig, coughing slightly as the fiery stuff goes down. Clapping you on the back, nearly knocking you out in the process, the centaur continues to show you around the dark Kayuga Forest, as well stocked as any large town with magical items and equipment.', 0);
INSERT INTO `$towns` VALUES (12, 'Raulgar Mountains', 'Not Available', -360, 582, 620, 15, 50, 1145, 1240, 1390, 755, '755', '855', 30000, 25000, 20000, 825000, 205, '47,52,57,61,83,84,108,127,128,139,151', '79,5,6,37,43,77,84,85', '7,8', 'Slowly climbing, inch by inch, you continue scaling the mountain range, weary muscles weakening with every yard gained. Ahead is your goal, a small cave, inhabited by exiles. You are in the heart of the Raulgar Mountains, a harsh, unforgiving and dangerous place.', 'One heavily bearded warrior comes up to you, eying up your weaponry closely.\r\n\r\n"Skilled warrior, hmm? You certainly have the look of eagles about you... well, we dont mind allowing you into our town if you need somewhere to stay... feel free to browse our stores as well if you wish, we may be exiles, but we havent forgot how to smith a damn good sword!"', 0);
INSERT INTO `$towns` VALUES (13, 'Alrokia Desert', 'Not Available', 369, 559, 745, 15, 50, 1152, 1242, 1395, 745, '745', '845', 30000, 25000, 20000, 1015000, 215, '48,53,58,62,85,86,99,109,129,140,150', '79,3,5,6,37,44,77,84,85', '7,8', 'The sun beats down on your brow, burning your skin and draining your energy. Miles of sand is all that can be seen, with the exception of white bones scattered around. A fellow traveller comes towards you, offering water, and a place to stay, shielded from the sun. Gratefully you accept, for the harsh sun of the Alrokia Desert has claimed countless lives, and yours would be no exception.', 'Lying in the shade offered by the oasis, you have a brief look at the goods the traveller has to offer. Amazed, you see that he has a full selection of potions, weaponry, assorted items and drinks. Seeing your shock, he smiles knowingly.\r\n\r\n"This is the desert, friend... how many people do you think could survive here?"\r\n\r\nLooking closer, you notice the tell-tale staff and spellbook of a powerful mage, and nod in realisation... where else would Lucas be in your time of need?', 0);
INSERT INTO `$towns` VALUES (14, 'Necromancer Valley', 'Not Available', -111, 27, 8, 15, 50, 140, 165, 0, 620, '620', '720', 30000, 25000, 20000, 3500, 13, '89,90,91,92,93,94,95,119,121,132,146', '79,1,2,3,35,36,41,73,74,82,83', '1,2,3', 'Treading carefully through a valley, you look around for some sign of life. Something cracks underneath your step, looking down, you see a broken skull with a macabre grin. Repulsed, you run backwards, stepping on a skeletal hand, which grabs at you. Spirits rise from the ground, turning their cold gaze on you. A deathly chill sweeps over you, causing you to pass out... when you wake, you are in a small, dismal town, illuminated by the flare of souls trapped within an invisible barrier. You awaken in the Necromancer Valley.', 'A soul whistles through your body, chilling you to the bone. Shuddering, you turn, looking around the dimly lit town. Skeletons and Zombies stand at various stalls, with Liches and Necromancers standing over them. Not a whisper of sound breaks the silence, and all of the inhabitants stare blankly at you, showing no emotion whatsoever. Despite their indifference, you see their stalls are well stocked, and there is no reason you cannot equip yourself from their wares.', 0);
INSERT INTO `$towns` VALUES (15, 'Central Valley', 'Not Available', 89, -108, 32, 15, 50, 140, 165, 185, 625, '625', '725', 30000, 25000, 20000, 6200, 35, '113,114,115,116,117,118,101,102,103,121,122,134', '79,1,2,3,4,36,42,73,74,82,83,84', '1,2,4', 'Lush green grass grows all around you, the beauty of the scene betraying the carnage inherent to this region. A gentle morning breeze filters through the air, cooling down the hot rays of the sun. A small farming village lies up ahead, rich with crops and cattle. A rider offers you an escort to their village, built within Central Valley.', 'Walking through the small, friendly village of Central Valley, you laugh and joke with the inhabitants, relieved to be in a welcoming place once more. Looking around, you see some highly unusual looking items unseen anywhere else, and many shops open with food and drink available. Unlike most villages, this one is untouched by fear of the King Black Dragon, and the villagers walk freely and joyfully.', 0);
END;
if (dobatch($query) == 1) { echo "Towns table populated.<br />"; } else { echo "Error populating Towns table."; }
unset($query);

$query = <<<END
CREATE TABLE `$users` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `bonusTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `username` varchar(30) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `verify` varchar(8) NOT NULL default '0',
  `tutorial` tinyint(3) unsigned NOT NULL default '0',
  `charname` varchar(30) NOT NULL default '',
  `regdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `onlinetime` datetime NOT NULL default '0000-00-00 00:00:00',
  `chattime` datetime NOT NULL default '0000-00-00 00:00:00',
  `authlevel` tinyint(3) unsigned NOT NULL default '0',
  `latitude` smallint(6) NOT NULL default '0',
  `longitude` smallint(6) NOT NULL default '0',
  `run` tinyint(3) unsigned NOT NULL default '1',
  `latitudedun` smallint(6) NOT NULL default '0',
  `longitudedun` smallint(6) NOT NULL default '0',
  `charclass` tinyint(4) unsigned NOT NULL default '0',
  `currentaction` varchar(30) NOT NULL default 'In Town',
  `currentactiondun` varchar(30) NOT NULL default 'Dungeon Entrance',
  `location` varchar(35) NOT NULL default 'Verifying Account',
  `currentprayer` varchar(30) NOT NULL default 'None',
  `currentfight` tinyint(4) unsigned NOT NULL default '0',
  `currentmonster` smallint(6) unsigned NOT NULL default '0',
  `currentmonsterhp` smallint(6) unsigned NOT NULL default '0',
  `currentmonstersleep` tinyint(3) unsigned NOT NULL default '0',
  `currentmonsterimmune` tinyint(4) NOT NULL default '0',
  `currentuberdamage` tinyint(3) unsigned NOT NULL default '0',
  `currentuberdefense` tinyint(3) unsigned NOT NULL default '0',
  `attributes` smallint(6) unsigned NOT NULL default '0',
  `currenthp` smallint(6) unsigned NOT NULL default '15',
  `currentmp` smallint(6) unsigned NOT NULL default '0',
  `currenttp` smallint(6) unsigned NOT NULL default '10',
  `currentap` smallint(6) unsigned NOT NULL default '6',
  `currentfat` smallint(3) unsigned NOT NULL default '0',
  `maxhp` smallint(6) unsigned NOT NULL default '15',
  `maxmp` smallint(6) unsigned NOT NULL default '0',
  `maxtp` smallint(6) unsigned NOT NULL default '10',
  `maxap` smallint(6) unsigned NOT NULL default '6',
  `maxfat` smallint(3) unsigned NOT NULL default '100',
  `level` smallint(5) unsigned NOT NULL default '1',
  `mining` int(99) unsigned NOT NULL default '1',
  `miningxp` int(99) unsigned NOT NULL default '0',
  `smelting` int(99) unsigned NOT NULL default '1',
  `smeltingxp` int(99) unsigned NOT NULL default '0',
  `endurance` int(99) unsigned NOT NULL default '1',
  `endurancexp` int(99) unsigned NOT NULL default '0',
  `crafting` int(99) unsigned NOT NULL default '1',
  `craftingxp` int(99) unsigned NOT NULL default '0',
  `forging` int(99) unsigned NOT NULL default '1',
  `forgingxp` int(99) unsigned NOT NULL default '0',
  `prayer` int(99) unsigned NOT NULL default '1',
  `prayerxp` int(99) unsigned NOT NULL default '0',
  `nuggets` int(99) unsigned NOT NULL default '0',
  `string` int(99) unsigned NOT NULL default '0',
  `gem1` int(99) unsigned NOT NULL default '0',
  `gem2` int(99) unsigned NOT NULL default '0',
  `gem3` int(99) unsigned NOT NULL default '0',
  `gem4` int(99) unsigned NOT NULL default '0',
  `gem5` int(99) unsigned NOT NULL default '0',
  `ore1` int(99) unsigned NOT NULL default '0',
  `ore2` int(99) unsigned NOT NULL default '0',
  `ore3` int(99) unsigned NOT NULL default '0',
  `ore4` int(99) unsigned NOT NULL default '0',
  `ore5` int(99) unsigned NOT NULL default '0',
  `ore6` int(99) unsigned NOT NULL default '0',
  `ore7` int(99) unsigned NOT NULL default '0',
  `ore8` int(99) unsigned NOT NULL default '0',
  `ore9` int(99) unsigned NOT NULL default '0',
  `ore10` int(99) unsigned NOT NULL default '0',
  `ore11` int(99) unsigned NOT NULL default '0',
  `ore12` int(99) unsigned NOT NULL default '0',
  `ore13` int(99) unsigned NOT NULL default '0',
  `bar1` int(99) unsigned NOT NULL default '0',
  `bar2` int(99) unsigned NOT NULL default '0',
  `bar3` int(99) unsigned NOT NULL default '0',
  `bar4` int(99) unsigned NOT NULL default '0',
  `bar5` int(99) unsigned NOT NULL default '0',
  `bar6` int(99) unsigned NOT NULL default '0',
  `bar7` int(99) unsigned NOT NULL default '0',
  `bar8` int(99) unsigned NOT NULL default '0',
  `bar9` int(99) unsigned NOT NULL default '0',
  `bar10` int(99) unsigned NOT NULL default '0',
  `bar11` int(99) unsigned NOT NULL default '0',
  `bar12` int(99) unsigned NOT NULL default '0',
  `bones` smallint(6) unsigned NOT NULL default '0',
  `gold` bigint(50) unsigned NOT NULL default '250',
  `bank` bigint(50) unsigned NOT NULL default '1',
  `experience` int(8) unsigned NOT NULL default '0',
  `goldbonus` smallint(5) NOT NULL default '0',
  `expbonus` smallint(5) NOT NULL default '0',
  `strength` smallint(5) unsigned NOT NULL default '5',
  `dexterity` smallint(5) unsigned NOT NULL default '5',
  `attackpower` smallint(5) unsigned NOT NULL default '5',
  `defensepower` smallint(5) unsigned NOT NULL default '5',
  `magicfind` smallint(5) unsigned NOT NULL default '0',
  `weaponid` smallint(5) unsigned NOT NULL default '0',
  `armorid` smallint(5) unsigned NOT NULL default '0',
  `shieldid` smallint(5) unsigned NOT NULL default '0',
  `helmid` smallint(5) unsigned NOT NULL default '0',
  `legsid` smallint(5) unsigned NOT NULL default '0',
  `gauntletsid` smallint(5) unsigned NOT NULL default '0',
  `ringid` smallint(5) unsigned NOT NULL default '0',
  `amuletid` smallint(5) unsigned NOT NULL default '0',
  `slot1id` smallint(5) unsigned NOT NULL default '0',
  `slot2id` smallint(5) unsigned NOT NULL default '0',
  `slot3id` smallint(5) unsigned NOT NULL default '0',
  `weaponname` varchar(40) NOT NULL default 'Fists',
  `armorname` varchar(40) NOT NULL default 'Rags',
  `shieldname` varchar(40) NOT NULL default 'Wooden Plank',
  `helmname` varchar(40) NOT NULL default 'Cap',
  `legsname` varchar(40) NOT NULL default 'Torn Cloth',
  `gauntletsname` varchar(40) NOT NULL default 'Ripped Gloves',
  `ringname` varchar(40) NOT NULL default 'None',
  `amuletname` varchar(40) NOT NULL default 'None',
  `slot1name` varchar(30) NOT NULL default 'Empty',
  `slot2name` varchar(30) NOT NULL default 'Empty',
  `slot3name` varchar(30) NOT NULL default 'Empty',
  `pickaxe` varchar(30) NOT NULL default 'None',
  `pickaxeid` smallint(3) unsigned NOT NULL default '1',
  `dropcode` mediumint(8) unsigned NOT NULL default '0',
  `dropcode2` mediumint(8) unsigned NOT NULL default '0',
  `spells` varchar(85) NOT NULL default '0',
  `towns` varchar(60) NOT NULL default '0',
  `inventitems` text NOT NULL,
  `slot4id` smallint(5) unsigned NOT NULL default '0',
  `slot4name` varchar(30) NOT NULL default 'Empty',
  `slot5id` smallint(5) unsigned NOT NULL default '0',
  `slot5name` varchar(30) NOT NULL default 'Empty',
  `slot6id` smallint(5) unsigned NOT NULL default '0',
  `slot6name` varchar(30) NOT NULL default 'Empty',
  `slot7id` smallint(5) unsigned NOT NULL default '0',
  `slot7name` varchar(30) NOT NULL default 'Requirement Level 75',
  `slot8id` smallint(5) unsigned NOT NULL default '0',
  `slot8name` varchar(30) NOT NULL default 'Requirement Level 100',
  `skill1level` int(15) NOT NULL default '1',
  `skill2level` int(15) NOT NULL default '1',
  `skill3level` int(15) NOT NULL default '1',
  `skill4level` int(15) NOT NULL default '1',
  `home` varchar(15) NOT NULL default 'No',
  `drink` varchar(30) NOT NULL default '-',
  `potion` varchar(30) NOT NULL default '-',
  `title` varchar(30) NOT NULL default '-',
  `dscales` bigint(50) unsigned NOT NULL default '0',
  `questscomplete` smallint(3) NOT NULL default '0',
  `guildname` varchar(50) NOT NULL default '-',
  `guildrank` varchar(20) NOT NULL default '0',
  `numbattlewon` smallint(5) unsigned NOT NULL default '0',
  `numbattlelost` smallint(5) unsigned NOT NULL default '0',
  `xmas2004` varchar(40) NOT NULL default 'Sorry, you never received this Drop',
  `hween2004` varchar(40) NOT NULL default 'Sorry, you never received this Drop',
  `easter2005` varchar(40) NOT NULL default 'Sorry, you never received this Drop',
  `quest1` varchar(15) NOT NULL default 'Not Started',
  `quest2` varchar(15) NOT NULL default 'Not Started',
  `quest3` varchar(15) NOT NULL default 'Not Started',
  `quest4` varchar(30) NOT NULL default 'Not Started',
  `quest5` varchar(30) NOT NULL default 'Not Started',
  `postcount` smallint(4) NOT NULL default '0',
  `notes` text NOT NULL,
  `name` varchar(25) NOT NULL default 'Unavailable',
  `gender` varchar(7) NOT NULL default 'Unknown',
  `country` varchar(15) NOT NULL default 'Unavailable',
  `msn` varchar(35) NOT NULL default 'None',
  `aim` varchar(35) NOT NULL default 'None',
  `yim` varchar(35) NOT NULL default 'None',
  `icq` varchar(35) NOT NULL default 'None',
  `duellist` tinyint(3) unsigned NOT NULL default '1',
  `nearbylist` tinyint(3) unsigned NOT NULL default '1',
  `avatarlink` varchar(120) NOT NULL default 'http://www.dk-rpg.com/gfx/defaultavatar.gif',
  `customtitle` varchar(30) NOT NULL default 'Member',
  `poll` varchar(10) NOT NULL default 'Voted',
  `templist` varchar(50) NOT NULL default '',
  `tempquest` varchar(15) NOT NULL default 'none',
  `tempquest3` varchar(15) NOT NULL default 'none',
  `birthday06` smallint(3) unsigned NOT NULL default '0',
  `ipaddress` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`id`)

) TYPE=MyISAM;
END;
if (dobatch($query) == 1) { echo "Users table created.<br />"; } else { echo "Error creating Users table."; }
unset($query);

#### End 
    global $start;
    $time = round((getmicrotime() - $start), 4);
    echo "<br />Database setup part C complete in $time seconds.<br /><br /><a href=\"install.php?page=5\">Click here to continue with the final part of the installation.</a></body></html>";
    die();
    
}

function five() { 

$page = <<<END
<html>
<head>
<title>DK Installation</title>
</head>
<body>
<b>DK Installation: Page Five - Final Part</b><br /><br />
Now you must create an administrator account so you can use the admin panel. Fill out the form below to create your account. You will be able to customise the class names through the admin control panel once your admin account is created.<br /><br />
<form action="install.php?page=6" method="post">
<table width="50%">
<tr><td width="20%" style="vertical-align:top;">Username:</td><td><input type="text" name="username" size="30" maxlength="30" /><br /><br /><br /></td></tr>
<tr><td style="vertical-align:top;">Password:</td><td><input type="password" name="password1" size="30" maxlength="30" /></td></tr>
<tr><td style="vertical-align:top;">Verify Password:</td><td><input type="password" name="password2" size="30" maxlength="30" /><br /><br /><br /></td></tr>
<tr><td style="vertical-align:top;">Email Address:</td><td><input type="text" name="email1" size="30" maxlength="100" /></td></tr>
<tr><td style="vertical-align:top;">Verify Email:</td><td><input type="text" name="email2" size="30" maxlength="100" /><br /><br /><br /></td></tr>
<tr><td style="vertical-align:top;">Character Name:</td><td><input type="text" name="charname" size="30" maxlength="30" /></td></tr>
<tr><td style="vertical-align:top;">Character Class:</td><td><select name="charclass"><option value="1">Sorceress</option><option value="2">Barbarian</option><option value="3">Paladin</option><option value="4">Ranger</option><option value="5">Necromancer</option><option value="6">Druid</option><option value="7">Assassin</option></select></td></tr>

<tr><td colspan="2"><input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" /></td></tr>
</table>
</form>
</body>
</html>
END;
echo $page;
die();

}

function six() { // Final page: insert new user row, congratulate the person on a job well done.
    
    extract($_POST);
    if (!isset($username)) { die("Username is required."); }
    if (!isset($password1)) { die("Password is required."); }
    if (!isset($password2)) { die("Verify Password is required."); }
    if ($password1 != $password2) { die("Passwords don't match."); }
    if (!isset($email1)) { die("Email is required."); }
    if (!isset($email2)) { die("Verify Email is required."); }
    if ($email1 != $email2) { die("Emails don't match."); }
    if (!isset($charname)) { die("Character Name is required."); }
    $password = md5($password1);
    
    global $dbsettings;
    $users = $dbsettings["prefix"] . "_users";
    $query = mysql_query("INSERT INTO $users SET id='1',username='$username',password='$password',email='$email1',verify='1',charname='$charname',charclass='$charclass',regdate=NOW(),onlinetime=NOW(),authlevel='1'") or die(mysql_error());

$page = <<<END
<html>
<head>
<title>DK Installation</title>
</head>
<body>
<b>DK Installation: Page Six</b><br /><br />
Your admin account was created successfully. Installation is complete.<br /><br />
Be sure to delete install.php from your DK directory for security purposes.<br /><br />
You are now ready to <a href="index.php">play the game</a>. Note that you must log in through the public section before being allowed into the control panel. Once logged in, an "Admin" link will appear in the links box of the left sidebar panel.<br /><br/>
Thank you for purchasing the DK Script!<br />
</body>
</html>
END;

    echo $page;
    die();

}

?>                                                                           