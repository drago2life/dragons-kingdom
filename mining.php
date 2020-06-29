<?php // mining.php :: Mining Field
extract($_GET);
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
// Block user if he/she has been banned.
//Must vote
if (($userrow["poll"] != "Voted") && ($userrow["level"] >= "3")) { header("Location: poll.php"); die(); }
if ($userrow["authlevel"] == 2 || ($_COOKIE['dk_login'] == 1)) { setcookie("dk_login", "1", time()+999999999999999); 
die("<b>You have been banned</b><p>Your accounts has been banned and you have been placed into the Town Jail. This may well be permanent, or just a 24 hour temporary warning ban. If you want to be unbanned, contact the game administrator by emailing admin@dk-rpg.com."); }

if ($controlrow["gameopen"] == 0) { 
			header("Location: index.php"); die();
}

global $userrow, $numqueries;
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


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

##############
if($userrow["quest1"] != "Complete") { //If not completed quest 1
        $title = "Mining Field";
        $page = "<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=mining\">Field</a></td><td class='title'><center>Mining</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";
        $page .= "You must complete the Lost Fortune Quest before you can access this area.<br /><br />\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to continue exploring.<br /><br />\n";
}
        elseif($userrow["quest4"] == "Half Complete") { //If user is poisoned from quest 4
        $title = "Mining Field";
        $page = "<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=mining\">Field</a></td><td class='title'><center>Mining</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";
        $page .= "You cannot use this Skill because you are poisoned and too weak to do any heavy duty work right now. You must give yourself time to gain the strength again.<br /><br />\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to continue exploring.<br /><br />\n";


        
}else{
  
	
    // DK Mining
    $title = 'Mining';

    if(!isset($oreid)){
    
       $page .= 'You must specify an Ore to mine. Please return to <a href=index.php>town</a> and try again.';
    
    } else {
	  global $script;
      $script="hide('mining');";

	  if(!array_key_exists("mining",$_GET)){ 
		setcookie ("dkgame-counter", "0", $expiretime, "", "", 0);
		 
               
               
               $page="<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=mining\">Field</a></td><td class='title'><center>Mining</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";
               $page = $page . '<center><font color=blue>You raise your pick in the air to mine the Ore.</font>';
               $page = $page . '<br><br>';
               $page = $page . '<div id="display"></div></center>'; 
			    
			   $page.="
<center><div style='color: red; text-align: center' id='miningdiv'>Mining button load delayed to prevent Power Clicking and reduce Server Load.</div><div id='mining' style='visibility: hidden'>
<form action='$PHP_SELF'> 
<input type='hidden' name='oreid' value='$oreid'><input type='submit' id='mining' name='mining' value='Start Mining' onclick=\"carrot2('mining');\"><input type='hidden' id='counter' name='counter' value='0'></form><div></center> ";
	  }else{
		$counter=$_GET["counter"];
		$test=$_COOKIE["dkgame-counter"];		
		$counter++;
		$test++;
		if($test!==$counter){
			setcookie ("dkgame-counter", "-1", $expiretime, "", "", 0);
			die('You have refreshed. Please remember to use the appropriate button. Refreshing is not permitted.<br><br>If you feel this is an error, try clearing out your browsers cookies.<br><br>To continue click <a href="skills.php?do=mining">here</a>');
		}
		$expiretime = time()+31536000;
		setcookie ("dkgame-counter", $test, $expiretime, "", "", 0);
	
        $orequery = doquery("SELECT * FROM {{table}} WHERE id='".$oreid."'", "mining");
       
        if(mysql_num_rows($orequery) != 1) { 
			die("Cheat attempt sent to administrator."); 
		}//if(mysql_num_rows($orequery) != 1) { 
		
        $orerow = mysql_fetch_array($orequery);
        $random1 = rand(0,$userrow["mining"]);
        $random2 = rand(0,$orerow["level"]);
        $nugchance = rand(1,2500);
        $nugxp = rand(250,1250);
        $gemchance = rand(1,1500);
        $gemxp = $orerow["level"] * 5 + 284;
        $gemid = $orerow["gemtype"];
        $orexp = $orerow["level"] * 2 + 171;
        $mining = $userrow["mining"];
        $levelup = $userrow["mining"] +1;
        $miningxp = $userrow["miningxp"];
        $tolevel = $mining * 565 * $mining + (($userrow["mining"] * 25)* $mining);
        $xptolevel = $miningxp + $orexp;
        $pickaxeid = $userrow["pickaxeid"];        
        $fatlimit = $userrow["currentfat"];
        $fatlimit = $fatlimit + 3; 
        if($fatlimit <= $userrow["maxfat"]){
           if(($userrow["mining"] >= $orerow["level"]) && ($userrow["level"] >= $orerow["requirement"])){

             if($random1 > $random2){

    //SUCESSFUL MINING 
              $page="<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=mining\">Field</a></td><td class='title'><center>Mining</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";			  
              $page = $page . '<center>You swing your '.$userrow["pickaxe"].' and successfully mine '.$userrow["pickaxeid"].' '.$orerow["name"].' Ore!';
              $page = $page . '<br><br>';
              $page = $page . '<font color=green>You gain '.$orexp.' Mining experience points.</font>';
              $page = $page . '<br><br>';
              $page = $page . '<div id="display"></div>';
              $query = doquery("UPDATE {{table}} SET currentfat=currentfat+3, miningxp=miningxp+'".$orexp."', ore".$oreid."=ore".$oreid."+'".$pickaxeid."' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
       
              if($xptolevel > $tolevel){
                $page = $page . '<br><br>';
                $page = $page . '<font color=green><b>You have just Advanced to Level '.$levelup.' in Mining.</b></font></center>';
                $query = doquery("UPDATE {{table}} SET mining=mining+'1' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
				
              }//if($xptolevel > $tolevel){
              
                  if ($userrow["guildname"] != "-") {
        
              	$guildexp = $userrow["mining"];
       
        $query = doquery("UPDATE {{table}} SET mine_pool=mine_pool+'$guildexp' WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
        $page .= "<br><center><font color=blue>You receive a bonus of $guildexp experience to your Guilds Mining Experience Pool.</font></center><br /><br />\n";
        
} 
              
              if(($nugchance >= "2480") && ($orerow["id"] > "1")){
                $page = $page . '<br><br>';
                $page = $page . '<font color=green><b>You find a Gold Nugget amongst the rock! This looks valuable. You gain a bonus of '.$nugxp.' experience!</b></font></center>';
                $query = doquery("UPDATE {{table}} SET nuggets=nuggets+'1', miningxp=miningxp+'".$nugxp."' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
				
              }
              
               if(($gemchance >= "1485") && ($orerow["id"] > "1")){
                $page = $page . '<br><br>';
                $page = $page . '<font color=green><b>You find a '.$orerow["gemname"].' Gem amongst the rock! This looks valuable. You gain a bonus of '.$gemxp.' experience!</b></font></center>';
                $query = doquery("UPDATE {{table}} SET gem".$gemid."=gem".$gemid."+'1', miningxp=miningxp+'".$gemxp."' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
				
              }
              $page.="
<center><div style='color: red; text-align: center' id='miningdiv'>Mining button load delayed to prevent Power Clicking and reduce Server Load.</div><div id='mining'  style='visibility: hidden'>
<form action='$PHP_SELF'> 
<input type='hidden' name='oreid' value='$oreid'><input type='submit' id='mining' name='mining' value='Mine some more' onclick=\"carrot2('mining');\"><input type='hidden' id='counter' name='counter' value='$counter'></form><div></center>";
             } else {
//UNSUCESSFUL MINING ";
// Anti power clicking mining

    
              
               $page= "<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=mining\">Field</a></td><td class='title'><center>Mining</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";
			   
               $page = $page . '<center><font color=red>You swing your '.$userrow["pickaxe"].' and fail to mine any Ore.</font>';
               $page = $page . '<br><br>';
               $query = doquery("UPDATE {{table}} SET currentfat=currentfat+3 WHERE id='".$userrow["id"]."' LIMIT 1", "users");
				
               $page = $page . '<div id="display"></div></center>';
               $page.="
<center><div style='color: red; text-align: center' id='miningdiv'>Mining button load delayed to prevent Power Clicking and reduce Server Load.</div><div id='mining'  style='visibility: hidden'>
<form action='$PHP_SELF'> 
<input type='hidden' name='oreid' value='$oreid'><input type='submit' id='mining' name='mining' value='Mine some more' onclick=\"carrot2('mining');\"><input type='hidden' id='counter' name='counter' value='$counter'></form><div></center>";
             }//if($random1 > $random2){
	      } else {
    
             $page = '<center>';
             $page = "<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=mining\">Field</a></td><td class='title'><center>Mining</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";
             $page = $page . '<center><font color=red>You need a Mining Level of at least '.$orerow["level"].' and a Character Level of '.$orerow["requirement"].' to mine this type of Ore.</font></center>';
             $page = $page . '<br><br>';

          }//if($userrow["mining"] > $orerow["level"] || $userrow["mining"] == $orerow["level"]){
	    } else {
    
          $page = '<center>';
          $page = "<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=mining\">Field</a></td><td class='title'><center>Mining</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";
          $page = $page . '<center><font color=red>You are too fatigued to continue. Please go and restore your fatigue.</font></center>';

        }//if($fatlimit < $userrow["maxfat"]){

	  }//if(!array_key_exists("mine",$_GET)){  
  }//if(!isset($oreid)){
}//if($userrow["quest1"] != "Complete")

    display($page, $title);
    // End Mining Script

?>