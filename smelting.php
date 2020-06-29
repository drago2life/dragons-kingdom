<?php // smelting.php :: Smelting Furnace

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

if(!array_key_exists("smelid",$_GET)){ 
	$smeltid=$_GET["smeltid"];
}else{
	$smeltid=NULL;
}

if ($controlrow["gameopen"] == 0) { 
			header("Location: index.php"); die();
}
// Block user if he/she has been banned.
if ($userrow["authlevel"] == 2 || ($_COOKIE['dk_login'] == 1)) { setcookie("dk_login", "1", time()+999999999999999); 
die("<b>You have been banned</b><p>Your accounts has been banned and you have been placed into the Town Jail. This may well be permanent, or just a 24 hour temporary warning ban. If you want to be unbanned, contact the game administrator by emailing admin@dk-rpg.com."); }
//Must vote
if (($userrow["poll"] != "Voted") && ($userrow["level"] >= "3")) { header("Location: poll.php"); die(); }
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

global $userrow, $numqueries;
##############

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

       if($userrow["quest4"] == "Half Complete") { //If user is poisoned from quest 4
        $title = "Smelting Furnace";
        $page="<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=smelting\">Furnace</a></td><td class='title'><center>Smelting</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";
        $page .= "You cannot use this Skill because you are poisoned and too weak to do any heavy duty work right now. You must give yourself time to gain the strength again.<br /><br />\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to continue exploring.<br /><br />\n";

      }else{
      

    // DK Smelting
    $title = 'Smelting';
    
    if(!isset($smeltid)){
    
    $page = 'You must specify a Bar to smelt. Please return to <a href=index.php>town</a> and try again.';
    
   } else {
      global $script;
      $script="hide('smelting');";


	  if(!array_key_exists("smelting",$_GET)){ 
		setcookie ("dkgame-counter", "0", $expiretime, "", "", 0);
		 
               
               
               $page="<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=smelting\">Furnace</a></td><td class='title'><center>Smelting</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";
               $page = $page . '<center><font color=blue>You begin to place your Ores into the hot furnace.</font>';
               $page = $page . '<br><br>';
               $page = $page . '<div id="display"></div></center>';
			   //$page.= "<center><form action='$PHP_SELF'><input type='hidden' name='smeltid' value='$smeltid'><input type='submit' id='smelting' name='smelting' value='Start Smelting'><input type='hidden' id='counter' name='counter' value='0'></form></center>";
			   $page.="
<center><div style='color: red; text-align: center' id='smeltingdiv'>Smelting button load delayed to prevent Power Clicking and reduce Server Load.</div><div id='mining'  style='visibility: hidden'><div id='smelting'>
<form action='$PHP_SELF'> 
<input type='hidden' name='smeltid' id='smeltid' value='$smeltid'><input type='submit' id='smelting' name='smelting' value='Start smelting' onclick=\"carrot2('smelting');\"><input type='hidden' id='counter' name='counter' value='0'></form><div></center>";
	  }else{
		 $counter=$_GET["counter"];
		 $test=$_COOKIE["dkgame-counter"];		
		 $counter++;
		 $test++;
		 if($test!==$counter){
			setcookie ("dkgame-counter", "-1", $expiretime, "", "", 0);
			die('You have refreshed. Please remember to use the appropriate button. Refreshing is not permitted.<br><br>If you feel this is an error, try clearing out your browsers cookies.<br><br>To continue click <a href="skills.php?do=smelting">here</a>');
		 }
		 $expiretime = time()+31536000;
		 setcookie ("dkgame-counter", $test, $expiretime, "", "", 0);


         $smeltquery = doquery("SELECT * FROM {{table}} WHERE id='".$smeltid."'", "smelting");
         
         if(mysql_num_rows($smeltquery) != 1) { 
         	die("Cheat attempt sent to administrator."); 
         }

         $smeltrow = mysql_fetch_array($smeltquery);
         $smeltxp = $smeltrow["level"] * 3 + 317;
         $smelting = $userrow["smelting"];
         $levelup = $userrow["smelting"] +1;
         $smeltingxp = $userrow["smeltingxp"];
         $tolevel = $smelting * 565 * $smelting + (($userrow["smelting"] * 25)* $smelting);
         $xptolevel = $smeltingxp + $smeltxp;
         $fatlimit = $userrow["currentfat"];
         $fatlimit = $fatlimit + 5; 
         if($fatlimit <= $userrow["maxfat"]){
            if($userrow["smelting"] > $smeltrow["level"] || $userrow["smelting"] == $smeltrow["level"]){
             
             
              if(($userrow["ore1"] >= $smeltrow["ore1"]) AND ($userrow["ore2"] >= $smeltrow["ore2"]) AND ($userrow["ore3"] >= $smeltrow["ore3"]) AND ($userrow["ore4"] >= $smeltrow["ore4"]) AND ($userrow["ore5"] >= $smeltrow["ore5"]) AND ($userrow["ore6"] >= $smeltrow["ore6"]) AND ($userrow["ore7"] >= $smeltrow["ore7"]) AND ($userrow["ore8"] >= $smeltrow["ore8"]) AND ($userrow["ore9"] >= $smeltrow["ore9"]) AND ($userrow["ore10"] >= $smeltrow["ore10"]) AND ($userrow["ore11"] >= $smeltrow["ore11"]) AND ($userrow["ore12"] >= $smeltrow["ore12"]) AND ($userrow["ore13"] >= $smeltrow["ore13"])){
            // if(($userrow["ore1"] > $smeltrow["ore1"]) AND ($userrow["ore2"] > $smeltrow["ore2"])){  
       
               $page = '<center>';
               $page = "<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=smelting\">Furnace</a></td><td class='title'><center>Smelting</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";
               $page = $page . '<center>You place your Ores into the furnace and Smelt 1 '.$smeltrow["name"].'!';
               $page = $page . '<br><br>';
               $page = $page . '<font color=green>You gain '.$smeltxp.' Smelting experience points.</font>';
               $page = $page . '<br><br>';
               $page = $page . '<div id="display"></div>';
                $page.="
<center><div style='color: red; text-align: center' id='smeltingdiv'>Smelting button load delayed to prevent Power Clicking and reduce Server Load.</div><div id='mining'  style='visibility: hidden'><div id='smelting'>
<form action='$PHP_SELF'> 
<input type='hidden' name='smeltid' id='smeltid' value='$smeltid'><input type='submit' id='smelting' name='smelting' value='Smelt some more' onclick=\"carrot2('smelting');\"><input type='hidden' id='counter' name='counter' value='$counter'></form><div></center>";
                
               $query = doquery("UPDATE {{table}} SET ore1=ore1-'".$smeltrow["ore1"]."', ore2=ore2-'".$smeltrow["ore2"]."', ore3=ore3-'".$smeltrow["ore3"]."', ore4=ore4-'".$smeltrow["ore4"]."', ore5=ore5-'".$smeltrow["ore5"]."', ore6=ore6-'".$smeltrow["ore6"]."', ore7=ore7-'".$smeltrow["ore7"]."', ore8=ore8-'".$smeltrow["ore8"]."', ore9=ore9-'".$smeltrow["ore9"]."', ore10=ore10-'".$smeltrow["ore10"]."', ore11=ore11-'".$smeltrow["ore11"]."', ore12=ore12-'".$smeltrow["ore12"]."', ore13=ore13-'".$smeltrow["ore13"]."', currentfat=currentfat+5, smeltingxp=smeltingxp+'".$smeltxp."', bar".$smeltid."=bar".$smeltid."+'1' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
       
              if($xptolevel > $tolevel){
                $page = $page . '<br><br>';
                $page = $page . '<b><font color=green>You have just Advanced to Level '.$levelup.' in Smelting.</b></font></center>';   
                $query = doquery("UPDATE {{table}} SET smelting=smelting+'1' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
              }
              
                  if ($userrow["guildname"] != "-") {
        
              	$guildexp = $userrow["smelting"];
       
        $query = doquery("UPDATE {{table}} SET smelt_pool=smelt_pool+'$guildexp' WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
        $page .= "<br><center><font color=blue>You receive a bonus of $guildexp experience to your Guilds Smelting Experience Pool.</font></center><br /><br />\n";
        
} 
    
            } else {//if($userrow["ore1"] > $smeltrow["ore1"] AND 
    
              $page = '<center>';
              $page = "<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=smelting\">Furnace</a></td><td class='title'><center>Smelting</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";    
              $page = $page . '<center><font color=red>You dont have the needed Ores to create this Bar.</font></center>';    
              $page = $page . '<br><br>';
    
           }
         } else {
    
           $page = '<center>';
           $page = "<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=smelting\">Furnace</a></td><td class='title'><center>Smelting</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";    
           $page = $page . '<center><font color=red>You need a Smelting Level of at least '.$smeltrow["level"].' to smelt this Bar.</font></center>';   
           $page = $page . '<br><br>';
    
        }
      } else {
    
       $page = '<center>';
       $page = "<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=smelting\">Furnace</a></td><td class='title'><center>Smelting</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";
       $page = $page . '<center><font color=red>You are too fatigued to continue. Please go and restore your fatigue.</font></center>';   
      }
     }
   }
 }
 display($page, $title);
    // End Smelting Script


?>