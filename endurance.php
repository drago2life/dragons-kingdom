<?php // endurance.php :: Endurance skill
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

global $userrow, $numqueries;

    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

if($userrow["currenthp"] <= "0") { //If too hurt to continue
        $title = "Endurance";
        $page = "<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=endurance\">Courses</a></td><td class='title'><center>Endurance</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";
			$page .= "<p><font color=red>You have died from the Endurance Course.</font></b><br /><br />As a consequence, you've lost half of your gold and <b>some dragon scales</b>. You are too hurt to continue. You must go heal yourself at the nearest Inn to continue this Course.<p>You may return to <a href=\"index.php\">town</a> or visit the nearest <a href=\"index.php?do=inn\">inn</a>.<br /><br /><center><img src=\"images/died.gif\" border=\"0\" alt=\"You have Died\" /></a></center><p>We hope you fair better next time.";
			$newgold = ceil($userrow["gold"]/2);
			$newdscales = ceil($userrow["dscales"]/3);
			$updatequery = doquery("UPDATE {{table}} SET dscales='$newdscales',location='Dead',gold='$newgold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

			}
        elseif($userrow["quest4"] == "Half Complete") { //If user is poisoned from quest 4
      $title = "Endurance Courses";
      $page = "<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=endurance\">Courses</a></td><td class='title'><center>Endurance</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";
	  $page .= "You cannot use this Skill because you are poisoned and too weak to do any heavy duty work right now. You must give yourself time to gain the strength again.<br /><br />\n";
      $page .= "You may return to <a href=\"index.php\">town</a>, or use the compass on the right to continue exploring.<br /><br />\n";


      
}else{

    // DK Endurance
    $title = 'Endurance';

    if(!isset($courseid)){
    
       $page .= 'You must specify a Course. Please return to <a href=index.php>town</a> and try again.';
    
    } else {

/* INSTRUCTION :
--------------------------
   A) in the variable script where the hiding function is set, put in between the ' ' the name of the
   div that contains the button. (example: hiding('endurance'); 

   B) in the "if(!array_key_exists" check for the name of div that contain the button
   vhere you put the script that will be added to the body. The name in between the ' ' must be the
   name of the div of the button that is going to hide. 

   C) give a name to the div that contains the button. (exampe : endurance) . This div must have a
   style (with visibility set to hidden) in it to hide it during the pageloading --->
   style='visibility: hidden'. 
   
   NOTE the name of the div must be in the id property (ex: <div
   id="endurance" style="visibility: hidden">)

   D) put the same name for the div that contains the delay of loading message but add div to it.
   (example endurancediv). For this div a style is not necessary but for a good look you can add this
   ---> style="color: red; text-align: center"

   NOTE the name of the div must be in the id property. ex: <div
   id="endurancediv" style="color: red; text-align: center">

*/    
	  //in this variable put the name of the div in the function
	  global $script;
      $script="hide('endurance');"; //A)

	  if(!array_key_exists("endurance",$_GET)){ //B)
		setcookie ("dkgame-counter", "0", $expiretime, "", "", 0);
		 
               
               
               $page="<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=endurance\">Courses</a></td><td class='title'><center>Endurance</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";
               $page = $page . '<center><font color=blue>You do a quick warm up, to begin the Endurance Course.</font>';
               $page = $page . '<br><br>';
               $page = $page . '<div id="display"></div></center>'; 
			    
               //In this variable set the names of the div as explained in the instruction
			   $page.="
<center><div style='color: red; text-align: center' id='endurancediv'>Endurance button load delayed to prevent Power Clicking and reduce Server Load.</div><div id='endurance' style='visibility: hidden'>
<form action='$PHP_SELF'> 
<input type='hidden' name='courseid' value='$courseid'><input type='submit' id='endurance' name='endurance' value='Start Course' onclick=\"carrot2('endurance');\"><input type='hidden' id='counter' name='counter' value='0'></form><div></center> "; //C) and D)
	  }else{
		$counter=$_GET["counter"];
		$test=$_COOKIE["dkgame-counter"];		
		$counter++;
		$test++;
		if($test!==$counter){
			setcookie ("dkgame-counter", "-1", $expiretime, "", "", 0);
			die('You have refreshed. Please remember to use the appropriate button. Refreshing is not permitted.<br><br>If you feel this is an error, try clearing out your browsers cookies.<br><br>To continue click <a href="skills.php?do=endurance">here</a>');
		}
		$expiretime = time()+31536000;
		setcookie ("dkgame-counter", $test, $expiretime, "", "", 0);
	
        $coursequery = doquery("SELECT * FROM {{table}} WHERE id='".$courseid."'", "endurance");
       
        if(mysql_num_rows($coursequery) != 1) { 
			die("Cheat attempt sent to administrator."); 
		}//if(mysql_num_rows($coursequery) != 1) { 
		
        $courserow = mysql_fetch_array($coursequery);
        $random1 = rand(0,$userrow["endurance"]);
        $random2 = rand(0,$courserow["level"]);
        $coursexp = $courserow["level"] * 4 + 216;
        $endurance = $userrow["endurance"];
        $levelup = $userrow["endurance"] +1;
        $endurancexp = $userrow["endurancexp"];
        $tolevel = $endurance * 565 * $endurance + (($userrow["endurance"] * 25)* $endurance);
        $xptolevel = $endurancexp + $coursexp;
        $damage = intval(rand(1,($userrow["endurance"]/3)));;
        $fatiguelvlup = $courserow["fatigue"];          
        $fatlimit = $userrow["currentfat"];
        $fatlimit = $fatlimit + 7; 
        if($fatlimit <= $userrow["maxfat"]){
           if($userrow["endurance"] > $courserow["level"] || $userrow["endurance"] == $courserow["level"]){
             if($random1 > $random2){

    //SUCESSFUL COURSE 
              $page="<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=endurance\">Courses</a></td><td class='title'><center>Endurance</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";			  
              $page = $page . '<center>You '.$courserow["name"].' Successfully!';
              $page = $page . '<br><br>';
              $page = $page . '<font color=green>You gain '.$coursexp.' Endurance experience points.</font>';
              $page = $page . '<br><br>';
              $page = $page . '<div id="display"></div>';
              $query = doquery("UPDATE {{table}} SET currentfat=currentfat+7, endurancexp=endurancexp+'".$coursexp."' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
       
              if($xptolevel > $tolevel){
                $page = $page . '<br><br>';
                $page = $page . '<font color=green><b>You have just Advanced to Level '.$levelup.' in Endurance. You gain '.$fatiguelvlup.' to your Maximum Fatigue and 1 to your Maximum Ability Points!</b></font></center><br>';
                $query = doquery("UPDATE {{table}} SET maxap=maxap+1,maxfat=maxfat+$fatiguelvlup,endurance=endurance+1 WHERE id='".$userrow["id"]."' LIMIT 1", "users");
				
              }//if($xptolevel > $tolevel){
              
              
              if ($userrow["guildname"] != "-") {
        
              	$guildexp = $userrow["endurance"];
       
        $query = doquery("UPDATE {{table}} SET endurance_pool=endurance_pool+'$guildexp' WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
        $page .= "<br><center><font color=blue>You receive a bonus of $guildexp experience to your Guilds Endurance Experience Pool.</font></center><br /><br />\n";
        
} 
              
              $page.="
<center><div style='color: red; text-align: center' id='endurancediv'>Endurance button load delayed to prevent Power Clicking and reduce Server Load.</div><div id='endurance'  style='visibility: hidden'>
<form action='$PHP_SELF'> 
<input type='hidden' name='courseid' value='$courseid'><input type='submit' id='endurance' name='endurance' value='Continue Course' onclick=\"carrot2('endurance');\"><input type='hidden' id='counter' name='counter' value='$counter'></form><div></center>"; //C) and D)
             } else {
//UNSUCESSFUL COURSE ";
// Anti power clicking course

    
              
               $page= "<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=endurance\">Courses</a></td><td class='title'><center>Endurance</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";
			   
               $page = $page . '<center><font color=red>You '.$courserow["name"].' and fail! You are hurt for '.$damage.' Damage!</font>';
               $page = $page . '<br><br>';
               
               $query = doquery("UPDATE {{table}} SET currentfat=currentfat+7,currenthp=currenthp-'".$damage."' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
				
               $page = $page . '<div id="display"></div></center>';
               $page.="
<center><div style='color: red; text-align: center' id='endurancediv'>Endurance button load delayed to prevent Power Clicking and reduce Server Load.</div><div id='endurance'  style='visibility: hidden'>
<form action='$PHP_SELF'> 
<input type='hidden' name='courseid' value='$courseid'><input type='submit' id='endurance' name='endurance' value='Continue Course' onclick=\"carrot2('endurance');\"><input type='hidden' id='counter' name='counter' value='$counter'></form><div></center>"; //C) and D)
             }//if($random1 > $random2){
	      } else {
    
             $page = '<center>';
             $page = "<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=endurance\">Courses</a></td><td class='title'><center>Endurance</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";
             $page = $page . '<center><font color=red>You need an Endurance Level of at least '.$courserow["level"].' to attempt this Course.</font></center>';
             $page = $page . '<br><br>';

          }//if($userrow["endurance"] > $courserow["level"] || $userrow["endurance"] == $courserow["level"]){
	    } else {
    
          $page = '<center>';
          $page = "<table width='100%' border='1'><tr><td class='title'>Back to <a href=\"skills.php?do=endurance\">Courses</a></td><td class='title'><center>Endurance</center></td><td class='title'>Go to <a href=\"index.php\">Town</a></td></tr></table><p>";
          $page = $page . '<center><font color=red>You are too fatigued to continue. Please go and restore your fatigue.</font></center>';

        }//if($fatlimit < $userrow["maxfat"]){

	  }//if(!array_key_exists("mine",$_GET)){  
  }//if(!isset($courseid)){

   
    // End Endurance Script
}// Too hurt to continue
 display($page, $title);
?>