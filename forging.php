<?php // forging.php :: Forging Anvils

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
	$forgeid=$_GET["forgeid"];
}else{
	$forgeid=NULL;
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
    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
##############

       if($userrow["quest4"] == "Half Complete") { //If user is poisoned from quest 4
        $title = "Forging Anvils";
        $page="<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=forging\">Anvils</a></td><td class='title'><center>Forging</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";
        $page .= "You cannot use this Skill because you are poisoned and too weak to do any heavy duty work right now. You must give yourself time to gain the strength again.<br /><br />\n";
        $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to continue exploring.<br /><br />\n";

      }else{
      

    // DK Forging
    $title = 'Forging';
    
    if(!isset($forgeid)){
    
    $page = 'You must specify an Item to forge. Please return to <a href=index.php>town</a> and try again.';
    
   } else {
      global $script;
      $script="hide('forging');";


	  if(!array_key_exists("forging",$_GET)){ 
		setcookie ("dkgame-counter", "0", $expiretime, "", "", 0);
		 
               
               
               $page="<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=forging\">Anvils</a></td><td class='title'><center>Forging</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";
               $page = $page . '<center><font color=blue>You begin to place your Bars onto the Anvil.</font>';
               $page = $page . '<br><br>';
               $page = $page . '<div id="display"></div></center>';
			   //$page.= "<center><form action='$PHP_SELF'><input type='hidden' name='forgeid' value='$forgeid'><input type='submit' id='forging' name='forging' value='Start Forging'><input type='hidden' id='counter' name='counter' value='0'></form></center>";
			   $page.="
<center><div style='color: red; text-align: center' id='forgingdiv'>Forging button load delayed to prevent Power Clicking and reduce Server Load.</div><div id='mining'  style='visibility: hidden'><div id='forging'>
<form action='$PHP_SELF'> 
<input type='hidden' name='forgeid' id='forgeid' value='$forgeid'><input type='submit' id='forging' name='forging' value='Start forging' onclick=\"carrot2('forging');\"><input type='hidden' id='counter' name='counter' value='0'></form><div></center>";
	  }else{
		 $counter=$_GET["counter"];
		 $test=$_COOKIE["dkgame-counter"];		
		 $counter++;
		 $test++;
		 if($test!==$counter){
			setcookie ("dkgame-counter", "-1", $expiretime, "", "", 0);
			die('You have refreshed. Please remember to use the appropriate button. Refreshing is not permitted.<br><br>If you feel this is an error, try clearing out your browsers cookies.<br><br>To continue click <a href="skills.php?do=forging">here</a>');
		 }
		 $expiretime = time()+31536000;
		 setcookie ("dkgame-counter", $test, $expiretime, "", "", 0);


         $forgequery = doquery("SELECT * FROM {{table}} WHERE id='".$forgeid."'", "forging");
         
         if(mysql_num_rows($forgequery) != 1) { 
         	die("Cheat attempt sent to administrator."); 
         }
	
         $forgerow = mysql_fetch_array($forgequery);
         $forgexp = $forgerow["level"] * 2 + 517;
         $itemid = $forgerow["itemid"];
         $forging = $userrow["forging"];
         $levelup = $userrow["forging"] +1;
         $forgingxp = $userrow["forgingxp"];
         $tolevel = $forging * 565 * $forging + (($userrow["forging"] * 25)* $forging);
         $xptolevel = $forgingxp + $forgexp;
         $fatlimit = $userrow["currentfat"];
         $fatlimit = $fatlimit + 15;
         
	$bpquery = doquery("SELECT * FROM {{table}} WHERE playerid='".$userrow["id"]."' AND itemtype = '1' AND location = '1'", "itemstorage");
	mysql_num_rows($bpquery);
         
         if($fatlimit <= $userrow["maxfat"]){
         	
         	if (mysql_num_rows($bpquery) < 3){ //Not enough BP slots
         	
            if($userrow["forging"] > $forgerow["level"] || $userrow["forging"] == $forgerow["level"]){
             
             
              if(($userrow["bar1"] >= $forgerow["bar1"]) AND ($userrow["bar2"] >= $forgerow["bar2"]) AND ($userrow["bar3"] >= $forgerow["bar3"]) AND ($userrow["bar4"] >= $forgerow["bar4"]) AND ($userrow["bar5"] >= $forgerow["bar5"]) AND ($userrow["bar6"] >= $forgerow["bar6"]) AND ($userrow["bar7"] >= $forgerow["bar7"]) AND ($userrow["bar8"] >= $forgerow["bar8"]) AND ($userrow["bar9"] >= $forgerow["bar9"]) AND ($userrow["bar10"] >= $forgerow["bar10"]) AND ($userrow["bar11"] >= $forgerow["bar11"]) AND ($userrow["bar12"] >= $forgerow["bar12"])){
            //  
       
               $page = '<center>';
               $page = "<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=forging\">Anvils</a></td><td class='title'><center>Forging</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";
               $page = $page . '<center>You place your Bars onto the Anvil and Forge a <b>'.$forgerow["itemname"].'</b>! Your item was carefully placed into your Backpack.';
               $page = $page . '<br><br>';
               $page = $page . '<font color=green>You gain '.$forgexp.' Forging experience points.</font>';
               $page = $page . '<br><br>';
               $page = $page . '<div id="display"></div>';
                $page.="
<center><div style='color: red; text-align: center' id='forgingdiv'>Forging button load delayed to prevent Power Clicking and reduce Server Load.</div><div id='mining'  style='visibility: hidden'><div id='forging'>
<form action='$PHP_SELF'> 
<input type='hidden' name='forgeid' id='forgeid' value='$forgeid'><input type='submit' id='forging' name='forging' value='Forge some more' onclick=\"carrot2('forging');\"><input type='hidden' id='counter' name='counter' value='$counter'></form><div></center>";
                
               $query = doquery("INSERT INTO {{table}} SET  playerid='".$userrow["id"]."', location='1', itemtype='1', itemid='$itemid' ", "itemstorage");
               $query = doquery("UPDATE {{table}} SET bar1=bar1-'".$forgerow["bar1"]."', bar2=bar2-'".$forgerow["bar2"]."', bar3=bar3-'".$forgerow["bar3"]."', bar4=bar4-'".$forgerow["bar4"]."', bar5=bar5-'".$forgerow["bar5"]."', bar6=bar6-'".$forgerow["bar6"]."', bar7=bar7-'".$forgerow["bar7"]."', bar8=bar8-'".$forgerow["bar8"]."', bar9=bar9-'".$forgerow["bar9"]."', bar10=bar10-'".$forgerow["bar10"]."', bar11=bar11-'".$forgerow["bar11"]."', bar12=bar12-'".$forgerow["bar12"]."', currentfat=currentfat+15, forgingxp=forgingxp+'".$forgexp."' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
       
              if($xptolevel > $tolevel){
                $page = $page . '<br><br>';
                $page = $page . '<b><font color=green>You have just Advanced to Level '.$levelup.' in Forging.</b></font></center>';   
                $query = doquery("UPDATE {{table}} SET forging=forging+'1' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
              }
              
                  if ($userrow["guildname"] != "-") {
        
              	$guildexp = $userrow["forging"];
       
        $query = doquery("UPDATE {{table}} SET forge_pool=forge_pool+'$guildexp' WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
        $page .= "<br><center><font color=blue>You receive a bonus of $guildexp experience to your Guilds Forging Experience Pool.</font></center><br /><br />\n";
        
} 
    
            } else {//if($userrow["ore1"] > $forgerow["ore1"] AND 
    
              $page = '<center>';
              $page = "<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=forging\">Anvils</a></td><td class='title'><center>Forging</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";    
              $page = $page . '<center><font color=red>You dont have the needed Bars to create this item.</font></center>';    
              $page = $page . '<br><br>';
    
           }
         } else {
    
           $page = '<center>';
           $page = "<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=forging\">Anvils</a></td><td class='title'><center>Forging</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";    
           $page = $page . '<center><font color=red>You need a Forging Level of at least '.$forgerow["level"].' to Forge this item.</font></center>';   
           $page = $page . '<br><br>';
    
        }
              } else {
    
       $page = '<center>';
       $page = "<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=forging\">Anvils</a></td><td class='title'><center>Forging</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";
       $page = $page . '<center><font color=red>You do not have the room in your backpack to store this item.  Please go to your <a href=index.php?do=backpack>backpack</a> to clear out some room</font></center>';   
      }
        
      } else {
    
       $page = '<center>';
       $page = "<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=forging\">Anvils</a></td><td class='title'><center>Forging</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";
       $page = $page . '<center><font color=red>You are too fatigued to continue. Please go and restore your fatigue.</font></center>';   
      }
     }
   }
 }
 display($page, $title);
    // End Forging Script


?>