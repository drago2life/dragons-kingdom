<?php // crafting.php :: Crafting skill
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
//Must vote
if (($userrow["poll"] != "Voted") && ($userrow["level"] >= "3")) { header("Location: poll.php"); die(); }
if ($controlrow["gameopen"] == 0) { 
			header("Location: index.php"); die();
}

##############
##############

global $userrow, $numqueries;

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



if($userrow["quest4"] == "Half Complete") { //If user is poisoned from quest 4
      $title = "Crafting";
      $page = "<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=crafting\">Crafting Main</a></td><td class='title'><center>Crafting</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";
	  $page .= "You cannot use this Skill because you are poisoned and too weak to do any heavy duty work right now. You must give yourself time to gain the strength again.<br /><br />\n";
      $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to continue exploring.<br /><br />\n";
 		
     
}else{

    // DK Crafting
    $title = 'Crafting';

    if(!isset($craftid)){
    
       $page .= 'You must specify something to Craft. Please return to <a href=index.php>town</a> and try again.';
    
    } else {

/* INSTRUCTION :
--------------------------
   A) in the variable script where the hiding function is set, put in between the ' ' the name of the
   div that contains the button. (example: hiding('crafting'); 

   B) in the "if(!array_key_exists" check for the name of div that contain the button
   vhere you put the script that will be added to the body. The name in between the ' ' must be the
   name of the div of the button that is going to hide. 

   C) give a name to the div that contains the button. (exampe : crafting) . This div must have a
   style (with visibility set to hidden) in it to hide it during the pageloading --->
   style='visibility: hidden'. 
   
   NOTE the name of the div must be in the id property (ex: <div
   id="crafting" style="visibility: hidden">)

   D) put the same name for the div that contains the delay of loading message but add div to it.
   (example endurancediv). For this div a style is not necessary but for a good look you can add this
   ---> style="color: red; text-align: center"

   NOTE the name of the div must be in the id property. ex: <div
   id="endurancediv" style="color: red; text-align: center">

*/    
	  //in this variable put the name of the div in the function
	  global $script;
      $script="hide('crafting');"; //A)

	  if(!array_key_exists("crafting",$_GET)){ //B)
		setcookie ("dkgame-counter", "0", $expiretime, "", "", 0);
		 
               
               
               $page="<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=crafting\">Crafting Main</a></td><td class='title'><center>Crafting</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";
               $page = $page . '<center><font color=blue>You gather together the needed items, to begin to Craft.</font>';
               $page = $page . '<br><br>';
               $page = $page . '<div id="display"></div></center>'; 
			    
               //In this variable set the names of the div as explained in the instruction
			   $page.="
<center><div style='color: red; text-align: center' id='craftingdiv'>Crafting button load delayed to prevent Power Clicking and reduce Server Load.</div><div id='crafting' style='visibility: hidden'>
<form action='$PHP_SELF'> 
<input type='hidden' name='craftid' value='$craftid'><input type='submit' id='crafting' name='crafting' value='Start Crafting' onclick=\"carrot2('crafting');\"><input type='hidden' id='counter' name='counter' value='0'></form><div></center> "; //C) and D)
	  }else{
		$counter=$_GET["counter"];
		$test=$_COOKIE["dkgame-counter"];		
		$counter++;
		$test++;
		if($test!==$counter){
			setcookie ("dkgame-counter", "-1", $expiretime, "", "", 0);
			die('You have refreshed. Please remember to use the appropriate button. Refreshing is not permitted.<br><br>If you feel this is an error, try clearing out your browsers cookies.<br><br>To continue click <a href="skills.php?do=crafting">here</a>');
		}
		$expiretime = time()+31536000;
		setcookie ("dkgame-counter", $test, $expiretime, "", "", 0);
	
        $craftquery = doquery("SELECT * FROM {{table}} WHERE id='".$craftid."'", "crafting");
       
        if(mysql_num_rows($craftquery) != 1) { 
			die("Cheat attempt sent to administrator."); 
		}//if(mysql_num_rows($craftquery) != 1) { 
              
		
        $craftrow = mysql_fetch_array($craftquery);
        $random1 = rand(0,$userrow["crafting"]);
        $random2 = rand(0,$craftrow["level"]);
        $craftxp = $craftrow["level"] * 5 + 846;
        $crafting = $userrow["crafting"];
        
        $nuggets = $userrow["nuggets"] -1;
        
        $levelup = $userrow["crafting"] +1;
        $craftingxp = $userrow["craftingxp"];
        $tolevel = $crafting * 565 * $crafting + (($userrow["crafting"] * 25)* $crafting);
        $xptolevel = $craftingxp + $craftxp;       
        $fatlimit = $userrow["currentfat"];
        $fatlimit = $fatlimit + 7; 
        
        	$bpquery = doquery("SELECT * FROM {{table}} WHERE playerid='".$userrow["id"]."' AND itemtype = '3' AND location = '1'", "itemstorage");
	mysql_num_rows($bpquery);
        
        if($fatlimit <= $userrow["maxfat"]){
        	if (mysql_num_rows($bpquery) < 3){ //Not enough BP slots
           if($userrow["crafting"] > $craftrow["level"] || $userrow["crafting"] == $craftrow["level"]){
             if($nuggets >= "0"){
             		if($userrow["string"] >= $craftrow["string"]){ 
 				if(($userrow["gem1"] >= $craftrow["gem1"]) AND ($userrow["gem2"] >= $craftrow["gem2"]) AND ($userrow["gem3"] >= $craftrow["gem3"]) AND ($userrow["gem4"] >= $craftrow["gem4"]) AND ($userrow["gem5"] >= $craftrow["gem5"])){
    //SUCESSFUL COURSE 
    
              $page="<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=crafting\">Crafting Main</a></td><td class='title'><center>Crafting</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";			  
              $page = $page . '<center>You Craft a '.$craftrow["name"].' Successfully! Your item was carefully placed into your Backpack.';
              $page = $page . '<br><br>';
              $page = $page . '<font color=green>You gain '.$craftxp.' Crafting experience points.</font>';
              $page = $page . '<br><br>';
              $page = $page . '<div id="display"></div>';
              $query = doquery("INSERT INTO {{table}} SET  playerid='".$userrow["id"]."', location='1', itemtype='3', itemid='".$craftid."' ", "itemstorage");
              $query = doquery("UPDATE {{table}} SET string=string-'".$craftrow["string"]."', gem1=gem1-'".$craftrow["gem1"]."', gem2=gem2-'".$craftrow["gem2"]."', gem3=gem3-'".$craftrow["gem3"]."', gem4=gem4-'".$craftrow["gem4"]."', gem5=gem5-'".$craftrow["gem5"]."', currentfat=currentfat+7, craftingxp=craftingxp+'".$craftxp."', nuggets=nuggets-1 WHERE id='".$userrow["id"]."' LIMIT 1", "users");

              if($xptolevel > $tolevel){
                $page = $page . '<br><br>';
                $page = $page . '<font color=green><b>You have just Advanced to Level '.$levelup.' in Crafting.</b></font></center><br>';
                $query = doquery("UPDATE {{table}} SET crafting=crafting+1 WHERE id='".$userrow["id"]."' LIMIT 1", "users");
				
              }//if($xptolevel > $tolevel){
              
              if ($userrow["guildname"] != "-") {
        
              	$guildexp = $userrow["crafting"];
       
        $query = doquery("UPDATE {{table}} SET craft_pool=craft_pool+'$guildexp' WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
        $page .= "<br><center><font color=blue>You receive a bonus of $guildexp experience to your Guilds Crafting Experience Pool.</font></center><br /><br />\n";
        
} 
              
              $page.="
<center><div style='color: red; text-align: center' id='craftingdiv'>Crafting button load delayed to prevent Power Clicking and reduce Server Load.</div><div id='crafting'  style='visibility: hidden'>
<form action='$PHP_SELF'> 
<input type='hidden' name='craftid' value='$craftid'><input type='submit' id='crafting' name='crafting' value='Continue Crafting' onclick=\"carrot2('crafting');\"><input type='hidden' id='counter' name='counter' value='$counter'></form><div></center>"; //C) and D)
			
              
              } else {

              
               $page= "<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=crafting\">Crafting Main</a></td><td class='title'><center>Crafting</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";
			   
               $page = $page . '<center><font color=red>You dont have the required Gems to Craft a '.$craftrow["name"].'.</font>';
               $page = $page . '<br><br>';
               
                         }// if($nuggets >= "0"){
                         
                         				} else {
//UNSUCESSFUL COURSE ";
// Anti power clicking craft

    
              
               $page= "<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=crafting\">Crafting Main</a></td><td class='title'><center>Crafting</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";
			   
               $page = $page . '<center><font color=red>You dont have enough String to continue.</font>';
               $page = $page . '<br><br>';
               
                         }// if($nuggets >= "0"){
              	
				} else {
//UNSUCESSFUL COURSE ";
// Anti power clicking craft

    
              
               $page= "<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=crafting\">Crafting Main</a></td><td class='title'><center>Crafting</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";
			   
               $page = $page . '<center><font color=red>You dont have enough Gold Nuggets to continue.</font>';
               $page = $page . '<br><br>';
               
                         }// if($nuggets >= "0"){
	      } else {
    
             $page = '<center>';
             $page = "<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=crafting\">Crafting Main</a></td><td class='title'><center>Crafting</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";
             $page = $page . '<center><font color=red>You need an Crafting Level of at least '.$craftrow["level"].' to Craft this item.</font></center>';
             $page = $page . '<br><br>';
	      }
               } else {
    
       $page = '<center>';
       $page = "<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=forging\">Crafting Main</a></td><td class='title'><center>Forging</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";
       $page = $page . '<center><font color=red>You do not have the room in your backpack to store this item.  Please go to your <a href=index.php?do=backpack>backpack</a> to clear out some room</font></center>';   
      
             
             
          }//if($userrow["crafting"] > $craftrow["level"] || $userrow["crafting"] == $craftrow["level"]){
	    } else {
    
          $page = '<center>';
          $page = "<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=crafting\">Crafting Main</a></td><td class='title'><center>Crafting</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";
          $page = $page . '<center><font color=red>You are too fatigued to continue. Please go and restore your fatigue.</font></center>';

        }//if($fatlimit < $userrow["maxfat"]){

	  }//if(!array_key_exists("mine",$_GET)){  
  }//if(!isset($craftid)){

   
    // End Crafting Script
}// Too hurt to continue
 display($page, $title);
?>