<?php // strongholds.php :: stronghold stuff

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


##############
if (isset($_GET["do"])) {
	$do = explode(":",$_GET["do"]);

	if ($do[0] == "vault") { dovault(); }	
	    //Sell all ores
    elseif ($do[0] == "sellnuggets") { sellnuggets($do[1]); }	    
    elseif ($do[0] == "sellores") { sellores($do[1]); }
    elseif ($do[0] == "sellores1") { sellores1($do[1]); }
    elseif ($do[0] == "sellores2") { sellores2($do[1]); }
    elseif ($do[0] == "sellores3") { sellores3($do[1]); }
    elseif ($do[0] == "sellores4") { sellores4($do[1]); }
    elseif ($do[0] == "sellores5") { sellores5($do[1]); }
    elseif ($do[0] == "sellores6") { sellores6($do[1]); }
    elseif ($do[0] == "sellores7") { sellores7($do[1]); }
    elseif ($do[0] == "sellores8") { sellores8($do[1]); }
    elseif ($do[0] == "sellores9") { sellores9($do[1]); }
    elseif ($do[0] == "sellores10") { sellores10($do[1]); }
    elseif ($do[0] == "sellores11") { sellores11($do[1]); }
    elseif ($do[0] == "sellores12") { sellores12($do[1]); }
    elseif ($do[0] == "sellores13") { sellores13($do[1]); }
    //Sell all bars    
    elseif ($do[0] == "sellbars") { sellbars($do[1]); }
    elseif ($do[0] == "sellbars1") { sellbars1($do[1]); }
    elseif ($do[0] == "sellbars2") { sellbars2($do[1]); }
    elseif ($do[0] == "sellbars3") { sellbars3($do[1]); }
    elseif ($do[0] == "sellbars4") { sellbars4($do[1]); }
    elseif ($do[0] == "sellbars5") { sellbars5($do[1]); }
    elseif ($do[0] == "sellbars6") { sellbars6($do[1]); }
    elseif ($do[0] == "sellbars7") { sellbars7($do[1]); }
    elseif ($do[0] == "sellbars8") { sellbars8($do[1]); }
    elseif ($do[0] == "sellbars9") { sellbars9($do[1]); }
    elseif ($do[0] == "sellbars10") { sellbars10($do[1]); }
    elseif ($do[0] == "sellbars11") { sellbars11($do[1]); }
    elseif ($do[0] == "sellbars12") { sellbars12($do[1]); } 
      //Sell all gems and nuggets
    elseif ($do[0] == "sellnuggets") { sellnuggets($do[1]); }	
    elseif ($do[0] == "sellgems") { sellgems($do[1]); }
    elseif ($do[0] == "sellgems1") { sellgems1($do[1]); }
    elseif ($do[0] == "sellgems2") { sellgems2($do[1]); }
    elseif ($do[0] == "sellgems3") { sellgems3($do[1]); }
    elseif ($do[0] == "sellgems4") { sellgems4($do[1]); }
    elseif ($do[0] == "sellgems5") { sellgems5($do[1]); }
} 

function sellores() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
        {

	   $title = "Sell Ores";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
        $page .= "Hello, ".$userrow["charname"].".<p>How many Ores would you like to sell me?<br /><br />\n";
        $page .= "Here is a breakdown of how many of each Ore you have, in your Inventory, along with how much I will pay:</b><br /><br />\n";
        $page .= "You have <b>" . number_format($userrow["ore1"]) . "</b> Copper Ores. You can <A href='sell.php?do=sellores1'>sell me these</a> for 10 Gold each.</b><br />\n";
        $page .= "You have <b>" . number_format($userrow["ore2"]) . "</b> Tin Ores. You can <A href='sell.php?do=sellores2'>sell me these</a> for 20 Gold each.</b><br />\n";
        $page .= "You have <b>" . number_format($userrow["ore3"]) . "</b> Iron Ores. You can <A href='sell.php?do=sellores3'>sell me these</a> for 40 Gold each.</b><br />\n";
        $page .= "You have <b>" . number_format($userrow["ore4"]) . "</b> Magic Ores. You can <A href='sell.php?do=sellores4'>sell me these</a> for 60 Gold each.</b><br />\n";
        $page .= "You have <b>" . number_format($userrow["ore5"]) . "</b> Dark Ores. You can <A href='sell.php?do=sellores5'>sell me these</a> for 107 Gold each.</b><br />\n";
        $page .= "You have <b>" . number_format($userrow["ore6"]) . "</b> Bright Ores. You can <A href='sell.php?do=sellores6'>sell me these</a> for 167 Gold each.</b><br />\n";
        $page .= "You have <b>" . number_format($userrow["ore7"]) . "</b> Destiny Ores. You can <A href='sell.php?do=sellores7'>sell me these</a> for 250 Gold each.</b><br />\n";
        $page .= "You have <b>" . number_format($userrow["ore8"]) . "</b> Crystal Ores. You can <A href='sell.php?do=sellores8'>sell me these</a> for 350 Gold each.</b><br />\n";
        $page .= "You have <b>" . number_format($userrow["ore9"]) . "</b> Diamond Ores. You can <A href='sell.php?do=sellores9'>sell me these</a> for 545 Gold each.</b><br />\n";
        $page .= "You have <b>" . number_format($userrow["ore10"]) . "</b> Heros Ores. You can <A href='sell.php?do=sellores10'>sell me these</a> for 663 Gold each.</b><br />\n";
        $page .= "You have <b>" . number_format($userrow["ore11"]) . "</b> Holy Ores. You can <A href='sell.php?do=sellores11'>sell me these</a> for 967 Gold each.</b><br />\n";
        $page .= "You have <b>" . number_format($userrow["ore12"]) . "</b> Mythical Ores. You can <A href='sell.php?do=sellores12'>sell me these</a> for 1,400 Gold each.</b><br />\n";
        $page .= "You have <b>" . number_format($userrow["ore13"]) . "</b> Black Dragon's Ores. You can <A href='sell.php?do=sellores13'>sell me these</a> for 2,333 Gold each.</b><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='Selling Ores' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
 
          
                  $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
  }
  
	   display($page, $title);
}

function sellnuggets() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
      
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $nuggets=$_POST['nuggets'];          
        $nuggets=strip_tags($nuggets);
        $sellcost1=($nuggets*1200);

        if($nuggets<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Gold Nuggets</td></tr></table><p>";
           $page .="You may not sell negative gold nuggets. Stop trying to cheat!<p>";
           $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($nuggets > $userstats3[nuggets])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Gold Nuggets</td></tr></table><p>";
          $page .="You cannot sell that many gold nuggets since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost1', nuggets=nuggets-'$nuggets' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get nuggets");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Gold Nuggets</td></tr></table><p>";
           $page .="You sold your gold nuggets successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellnuggets'>more</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell Gold Nuggets";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Gold Nuggets</td></tr></table><p>";
        $page .= "How many gold nuggets would you like to Sell? I will give you 1,200 Gold per Nugget.\n";
        $page .= "<form action=\"sell.php?do=sellnuggets\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"nuggets\" size=\"5\" value=\"".$userrow["nuggets"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Nuggets\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }
  

function sellores1() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
      
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $ore1=$_POST['ore1'];          
        $ore1=strip_tags($ore1);
        $sellcost1=($ore1*10);

        if($ore1<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
           $page .="You may not sell negative Ores. Stop trying to cheat!<p>";
           $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($ore1 > $userstats3[ore1])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
          $page .="You cannot sell that many Ores since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost1', ore1=ore1-'$ore1' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get Ores");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
           $page .="You sold your Copper Ores successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellores'>more</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell Ores";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
        $page .= "How many Copper Ores would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellores1\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"ore1\" size=\"5\" value=\"".$userrow["ore1"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Copper\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellores'>sell</a> different ores, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }
  
 function sellores2() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $ore2=$_POST['ore2'];          
        $ore2=strip_tags($ore2);
        $sellcost2=($ore2*20);

        if($ore2<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
           $page .="You may not sell negative Ores. Stop trying to cheat!<p>";
                             $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($ore2 > $userstats3[ore2])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
          $page .="You cannot sell that many Ores since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost2', ore2=ore2-'$ore2' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get Ores");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
           $page .="You sold your Tin Ores successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellores'>more</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell Ores";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
        $page .= "How many Tin Ores would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellores2\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"ore2\" size=\"5\" value=\"".$userrow["ore2"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Tin\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellores'>sell</a> different ores, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }
  
function sellores3() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $ore3=$_POST['ore3'];          
        $ore3=strip_tags($ore3);
        $sellcost3=($ore3*40);

        if($ore3<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
           $page .="You may not sell negative Ores. Stop trying to cheat!<p>";
                             $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($ore3 > $userstats3[ore3])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
          $page .="You cannot sell that many Ores since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or conIronue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost3', ore3=ore3-'$ore3' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get Ores");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
           $page .="You sold your Iron Ores successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellores'>more</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell Ores";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
        $page .= "How many Iron Ores would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellores3\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"ore3\" size=\"5\" value=\"".$userrow["ore3"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Iron\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellores'>sell</a> different ores, or conIronue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }  

function sellores4() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $ore4=$_POST['ore4'];          
        $ore4=strip_tags($ore4);
        $sellcost4=($ore4*60);

        if($ore4<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
           $page .="You may not sell negative Ores. Stop trying to cheat!<p>";
                             $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($ore4 > $userstats3[ore4])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
          $page .="You cannot sell that many Ores since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost4', ore4=ore4-'$ore4' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get Ores");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
           $page .="You sold your Magic Ores successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellores'>more</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell Ores";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
        $page .= "How many Magic Ores would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellores4\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"ore4\" size=\"5\" value=\"".$userrow["ore4"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Magic\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellores'>sell</a> different ores, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }   
function sellores5() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $ore5=$_POST['ore5'];          
        $ore5=strip_tags($ore5);
        $sellcost5=($ore5*107);

        if($ore5<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
           $page .="You may not sell negative Ores. Stop trying to cheat!<p>";
                             $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($ore5 > $userstats3[ore5])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
          $page .="You cannot sell that many Ores since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost5', ore5=ore5-'$ore5' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get Ores");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
           $page .="You sold your Dark Ores successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellores'>more</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell Ores";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
        $page .= "How many Dark Ores would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellores5\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"ore5\" size=\"5\" value=\"".$userrow["ore5"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Dark\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellores'>sell</a> different ores, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }   
  
  
  function sellores6() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $ore6=$_POST['ore6'];          
        $ore6=strip_tags($ore6);
        $sellcost6=($ore6*167);

        if($ore6<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
           $page .="You may not sell negative Ores. Stop trying to cheat!<p>";
                             $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($ore6 > $userstats3[ore6])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
          $page .="You cannot sell that many Ores since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost6', ore6=ore6-'$ore6' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get Ores");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
           $page .="You sold your Bright Ores successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellores'>more</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell Ores";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
        $page .= "How many Bright Ores would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellores6\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"ore6\" size=\"5\" value=\"".$userrow["ore6"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Bright\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellores'>sell</a> different ores, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  } 
  
  function sellores7() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $ore7=$_POST['ore7'];          
        $ore7=strip_tags($ore7);
        $sellcost7=($ore7*250);

        if($ore7<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
           $page .="You may not sell negative Ores. Stop trying to cheat!<p>";
                             $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($ore7 > $userstats3[ore7])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
          $page .="You cannot sell that many Ores since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost7', ore7=ore7-'$ore7' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get Ores");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
           $page .="You sold your Destiny Ores successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellores'>more</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell Ores";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
        $page .= "How many Destiny Ores would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellores7\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"ore7\" size=\"5\" value=\"".$userrow["ore7"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Destiny\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellores'>sell</a> different ores, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  } 
  
function sellores8() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $ore8=$_POST['ore8'];          
        $ore8=strip_tags($ore8);
        $sellcost8=($ore8*350);

        if($ore8<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
           $page .="You may not sell negative Ores. Stop trying to cheat!<p>";
                             $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($ore8 > $userstats3[ore8])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
          $page .="You cannot sell that many Ores since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost8', ore8=ore8-'$ore8' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get Ores");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
           $page .="You sold your Crystal Ores successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellores'>more</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell Ores";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
        $page .= "How many Crystal Ores would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellores8\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"ore8\" size=\"5\" value=\"".$userrow["ore8"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Crystal\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellores'>sell</a> different ores, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }   

function sellores9() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $ore9=$_POST['ore9'];          
        $ore9=strip_tags($ore9);
        $sellcost9=($ore9*545);

        if($ore9<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
           $page .="You may not sell negative Ores. Stop trying to cheat!<p>";
                             $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($ore9 > $userstats3[ore9])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
          $page .="You cannot sell that many Ores since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost9', ore9=ore9-'$ore9' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get Ores");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
           $page .="You sold your Diamond Ores successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellores'>more</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell Ores";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
        $page .= "How many Diamond Ores would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellores9\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"ore9\" size=\"5\" value=\"".$userrow["ore9"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Diamond\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellores'>sell</a> different ores, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }   
 function sellores10() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $ore10=$_POST['ore10'];          
        $ore10=strip_tags($ore10);
        $sellcost10=($ore10*663);

        if($ore10<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
           $page .="You may not sell negative Ores. Stop trying to cheat!<p>";
                             $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($ore10 > $userstats3[ore10])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
          $page .="You cannot sell that many Ores since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost10', ore10=ore10-'$ore10' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get Ores");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
           $page .="You sold your Heros Ores successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellores'>more</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell Ores";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
        $page .= "How many Heros Ores would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellores10\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"ore10\" size=\"5\" value=\"".$userrow["ore10"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Heros\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellores'>sell</a> different ores, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }   
  
 function sellores11() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $ore11=$_POST['ore11'];          
        $ore11=strip_tags($ore11);
        $sellcost11=($ore11*967);

        if($ore11<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
           $page .="You may not sell negative Ores. Stop trying to cheat!<p>";
                             $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($ore11 > $userstats3[ore11])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
          $page .="You cannot sell that many Ores since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost11', ore11=ore11-'$ore11' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get Ores");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
           $page .="You sold your Holy Ores successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellores'>more</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell Ores";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
        $page .= "How many Holy Ores would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellores11\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"ore11\" size=\"5\" value=\"".$userrow["ore11"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Holy\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellores'>sell</a> different ores, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }  
  
  function sellores12() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $ore12=$_POST['ore12'];          
        $ore12=strip_tags($ore12);
        $sellcost12=($ore12*1400);

        if($ore12<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
           $page .="You may not sell negative Ores. Stop trying to cheat!<p>";
                             $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($ore12 > $userstats3[ore12])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
          $page .="You cannot sell that many Ores since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost12', ore12=ore12-'$ore12' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get Ores");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
           $page .="You sold your Mythical Ores successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellores'>more</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell Ores";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
        $page .= "How many Mythical Ores would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellores12\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"ore12\" size=\"5\" value=\"".$userrow["ore12"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Mythical\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellores'>sell</a> different ores, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }  
  
  
 function sellores13() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $ore13=$_POST['ore13'];          
        $ore13=strip_tags($ore13);
        $sellcost13=($ore13*2333);

        if($ore13<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
           $page .="You may not sell negative Ores. Stop trying to cheat!<p>";
                             $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($ore13 > $userstats3[ore13])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
          $page .="You cannot sell that many Ores since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost13', ore13=ore13-'$ore13' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get Ores");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
           $page .="You sold your Black Dragon's Ores successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellores'>more</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell Ores";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ores</td></tr></table><p>";
        $page .= "How many Black Dragon's Ores would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellores13\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"ore13\" size=\"5\" value=\"".$userrow["ore13"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Black Dragon's\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellores'>sell</a> different ores, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }    
  
  function sellbars() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
        {

	   $title = "Sell Bars";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Bars</td></tr></table><p>";
        $page .= "Hello, ".$userrow["charname"].".<p>How many Bars would you like to sell me?<br /><br />\n";
        $page .= "Here is a breakdown of how many of each Bar you have, in your Inventory, along with how much I will pay:</b><br /><br />\n";
        $page .= "You have <b>" . number_format($userrow["bar1"]) . "</b> Bronze Bars. You can <A href='sell.php?do=sellbars1'>sell me these</a> for 46 Gold each.</b><br />\n";
        $page .= "You have <b>" . number_format($userrow["bar2"]) . "</b> Iron Bars. You can <A href='sell.php?do=sellbars2'>sell me these</a> for 145 Gold each.</b><br />\n";
        $page .= "You have <b>" . number_format($userrow["bar3"]) . "</b> Magic Bars. You can <A href='sell.php?do=sellbars3'>sell me these</a> for 351 Gold each.</b><br />\n";
        $page .= "You have <b>" . number_format($userrow["bar4"]) . "</b> Dark Bars. You can <A href='sell.php?do=sellbars4'>sell me these</a> for 586 Gold each.</b><br />\n";
        $page .= "You have <b>" . number_format($userrow["bar5"]) . "</b> Bright Bars. You can <A href='sell.php?do=sellbars5'>sell me these</a> for 929 Gold each.</b><br />\n";
        $page .= "You have <b>" . number_format($userrow["bar6"]) . "</b> Destiny Bars. You can <A href='sell.php?do=sellbars6'>sell me these</a> for 1,394 Gold each.</b><br />\n";
        $page .= "You have <b>" . number_format($userrow["bar7"]) . "</b> Crystal Bars. You can <A href='sell.php?do=sellbars7'>sell me these</a> for 2,702 Gold each.</b><br />\n";
        $page .= "You have <b>" . number_format($userrow["bar8"]) . "</b> Diamond Bars. You can <A href='sell.php?do=sellbars8'>sell me these</a> for 4,024 Gold each.</b><br />\n";
        $page .= "You have <b>" . number_format($userrow["bar9"]) . "</b> Heros Bars. You can <A href='sell.php?do=sellbars9'>sell me these</a> for 5,333 Gold each.</b><br />\n";
        $page .= "You have <b>" . number_format($userrow["bar10"]) . "</b> Holy Bars. You can <A href='sell.php?do=sellbars10'>sell me these</a> for 10,402 Gold each.</b><br />\n";
        $page .= "You have <b>" . number_format($userrow["bar11"]) . "</b> Mythical Bars. You can <A href='sell.php?do=sellbars11'>sell me these</a> for 18,441 Gold each.</b><br />\n";
        $page .= "You have <b>" . number_format($userrow["bar12"]) . "</b> Black Dragon's Bars. You can <A href='sell.php?do=sellbars12'>sell me these</a> for 42,896 Gold each.</b><br />\n";
$updatequery = doquery("UPDATE {{table}} SET location='Selling Bars' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
 
          
                  $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
  }
  
	   display($page, $title);
}

function sellbars1() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $bar1=$_POST['bar1'];          
        $bar1=strip_tags($bar1);
        $sellcost1=($bar1*46);

        if($bar1<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
           $page .="You may not sell negative bars. Stop trying to cheat!<p>";
                             $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($bar1 > $userstats3[bar1])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
          $page .="You cannot sell that many bars since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost1', bar1=bar1-'$bar1' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get bars");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
           $page .="You sold your Bronze Bars successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellbars1'>bars</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell bars";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
        $page .= "How many Bronze Bars would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellbars1\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"bar1\" size=\"5\" value=\"".$userrow["bar1"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Bronze\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellbars'>sell</a> different bars, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }

function sellbars2() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $bar2=$_POST['bar2'];          
        $bar2=strip_tags($bar2);
        $sellcost2=($bar2*145);

        if($bar2<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
           $page .="You may not sell negative bars. Stop trying to cheat!<p>";
                             $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($bar2 > $userstats3[bar2])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
          $page .="You cannot sell that many bars since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost2', bar2=bar2-'$bar2' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get bars");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
           $page .="You sold your Iron Bars successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellbars2'>bars</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell bars";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
        $page .= "How many Iron Bars would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellbars2\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"bar2\" size=\"5\" value=\"".$userrow["bar2"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Iron\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellbars'>sell</a> different bars, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }  

function sellbars3() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $bar3=$_POST['bar3'];          
        $bar3=strip_tags($bar3);
        $sellcost3=($bar3*351);

        if($bar3<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
           $page .="You may not sell negative bars. Stop trying to cheat!<p>";
                             $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($bar3 > $userstats3[bar3])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
          $page .="You cannot sell that many bars since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost3', bar3=bar3-'$bar3' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get bars");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
           $page .="You sold your Magic Bars successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellbars3'>bars</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell bars";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
        $page .= "How many Magic Bars would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellbars3\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"bar3\" size=\"5\" value=\"".$userrow["bar3"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Magic\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellbars'>sell</a> different bars, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }

  function sellbars4() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $bar4=$_POST['bar4'];          
        $bar4=strip_tags($bar4);
        $sellcost4=($bar4*586);

        if($bar4<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
           $page .="You may not sell negative bars. Stop trying to cheat!<p>";
                             $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($bar4 > $userstats3[bar4])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
          $page .="You cannot sell that many bars since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost4', bar4=bar4-'$bar4' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get bars");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
           $page .="You sold your Dark Bars successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellbars4'>bars</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell bars";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
        $page .= "How many Dark Bars would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellbars4\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"bar4\" size=\"5\" value=\"".$userrow["bar4"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Dark\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellbars'>sell</a> different bars, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }

  function sellbars5() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $bar5=$_POST['bar5'];          
        $bar5=strip_tags($bar5);
        $sellcost5=($bar5*929);

        if($bar5<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
           $page .="You may not sell negative bars. Stop trying to cheat!<p>";
                             $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($bar5 > $userstats3[bar5])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
          $page .="You cannot sell that many bars since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost5', bar5=bar5-'$bar5' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get bars");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
           $page .="You sold your Bright Bars successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellbars5'>bars</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell bars";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
        $page .= "How many Bright Bars would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellbars5\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"bar5\" size=\"5\" value=\"".$userrow["bar5"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Bright\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellbars'>sell</a> different bars, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }

  function sellbars6() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $bar6=$_POST['bar6'];          
        $bar6=strip_tags($bar6);
        $sellcost6=($bar6*1394);

        if($bar6<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
           $page .="You may not sell negative bars. Stop trying to cheat!<p>";
                             $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($bar6 > $userstats3[bar6])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
          $page .="You cannot sell that many bars since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost6', bar6=bar6-'$bar6' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get bars");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
           $page .="You sold your Destiny Bars successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellbars6'>bars</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell bars";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
        $page .= "How many Destiny Bars would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellbars6\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"bar6\" size=\"5\" value=\"".$userrow["bar6"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Destiny\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellbars'>sell</a> different bars, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }

  function sellbars7() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $bar7=$_POST['bar7'];          
        $bar7=strip_tags($bar7);
        $sellcost7=($bar7*2702);

        if($bar7<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
           $page .="You may not sell negative bars. Stop trying to cheat!<p>";
                             $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($bar7 > $userstats3[bar7])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
          $page .="You cannot sell that many bars since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost7', bar7=bar7-'$bar7' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get bars");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
           $page .="You sold your Crystal Bars successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellbars7'>bars</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell bars";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
        $page .= "How many Crystal Bars would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellbars7\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"bar7\" size=\"5\" value=\"".$userrow["bar7"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Crystal\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellbars'>sell</a> different bars, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }
  
  function sellbars8() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $bar8=$_POST['bar8'];          
        $bar8=strip_tags($bar8);
        $sellcost8=($bar8*4024);

        if($bar8<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
           $page .="You may not sell negative bars. Stop trying to cheat!<p>";
                             $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($bar8 > $userstats3[bar8])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
          $page .="You cannot sell that many bars since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost8', bar8=bar8-'$bar8' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get bars");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
           $page .="You sold your Diamond Bars successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellbars8'>bars</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell bars";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
        $page .= "How many Diamond Bars would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellbars8\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"bar8\" size=\"5\" value=\"".$userrow["bar8"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Diamond\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellbars'>sell</a> different bars, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }
  
  function sellbars9() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $bar9=$_POST['bar9'];          
        $bar9=strip_tags($bar9);
        $sellcost9=($bar9*5333);

        if($bar9<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
           $page .="You may not sell negative bars. Stop trying to cheat!<p>";
                             $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($bar9 > $userstats3[bar9])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
          $page .="You cannot sell that many bars since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost9', bar9=bar9-'$bar9' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get bars");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
           $page .="You sold your Heros Bars successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellbars9'>bars</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell bars";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
        $page .= "How many Heros Bars would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellbars9\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"bar9\" size=\"5\" value=\"".$userrow["bar9"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Heros\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellbars'>sell</a> different bars, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }
  
  function sellbars10() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $bar10=$_POST['bar10'];          
        $bar10=strip_tags($bar10);
        $sellcost10=($bar10*10402);

        if($bar10<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
           $page .="You may not sell negative bars. Stop trying to cheat!<p>";
                             $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($bar10 > $userstats3[bar10])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
          $page .="You cannot sell that many bars since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost10', bar10=bar10-'$bar10' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get bars");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
           $page .="You sold your Holy Bars successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellbars10'>bars</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell bars";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
        $page .= "How many Holy Bars would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellbars10\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"bar10\" size=\"5\" value=\"".$userrow["bar10"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Holy\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellbars'>sell</a> different bars, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }

  function sellbars11() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $bar11=$_POST['bar11'];          
        $bar11=strip_tags($bar11);
        $sellcost11=($bar11*18441);

        if($bar11<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
           $page .="You may not sell negative bars. Stop trying to cheat!<p>";
                             $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($bar11 > $userstats3[bar11])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
          $page .="You cannot sell that many bars since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost11', bar11=bar11-'$bar11' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get bars");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
           $page .="You sold your Mythical Bars successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellbars11'>bars</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell bars";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
        $page .= "How many Mythical Bars would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellbars11\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"bar11\" size=\"5\" value=\"".$userrow["bar11"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Mythical\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellbars'>sell</a> different bars, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }
  
  function sellbars12() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);

        
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $bar12=$_POST['bar12'];          
        $bar12=strip_tags($bar12);
        $sellcost12=($bar12*42896);

        if($bar12<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
           $page .="You may not sell negative bars. Stop trying to cheat!<p>";
                             $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($bar12 > $userstats3[bar12])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
          $page .="You cannot sell that many bars since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost12', bar12=bar12-'$bar12' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get bars");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
           $page .="You sold your Black Dragons Bars successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellbars12'>bars</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell bars";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your bars</td></tr></table><p>";
        $page .= "How many Black Dragons Bars would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellbars12\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"bar12\" size=\"5\" value=\"".$userrow["bar12"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Black Dragons\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellbars'>sell</a> different bars, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }
  
  function sellgems() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
        {

	   $title = "Sell Gems";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Gems</td></tr></table><p>";
        $page .= "How many Gems would you like to sell me?<br /><br />\n";
        $page .= "Here is a breakdown of how many of each Gem you have, in your Inventory, along with how much I will pay:</b><br /><br />\n";
        $page .= "You have <b>" . number_format($userrow["gem1"]) . "</b> Sapphire Gems. You can <A href='sell.php?do=sellgems1'>sell me these</a> for 200 Gold each.</b><br />\n";
        $page .= "You have <b>" . number_format($userrow["gem2"]) . "</b> Emerald Gems. You can <A href='sell.php?do=sellgems2'>sell me these</a> for 400 Gold each.</b><br />\n";
        $page .= "You have <b>" . number_format($userrow["gem3"]) . "</b> Ruby Gems. You can <A href='sell.php?do=sellgems3'>sell me these</a> for 750 Gold each.</b><br />\n";
        $page .= "You have <b>" . number_format($userrow["gem4"]) . "</b> Diamond Gems. You can <A href='sell.php?do=sellgems4'>sell me these</a> for 1,300 Gold each.</b><br />\n";
        $page .= "You have <b>" . number_format($userrow["gem5"]) . "</b> Black Dragon Gems. You can <A href='sell.php?do=sellgems5'>sell me these</a> for 2,000 Gold each.</b><br />\n";

$updatequery = doquery("UPDATE {{table}} SET location='Selling Gems' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
 
          
                  $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
  }
  
	   display($page, $title);
}
  
  function sellgems1() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
      
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $gem1=$_POST['gem1'];          
        $gem1=strip_tags($gem1);
        $sellcost1=($gem1*200);

        if($gem1<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Sapphire Gems</td></tr></table><p>";
           $page .="You may not sell negative Sapphire Gems. Stop trying to cheat!<p>";
           $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($gem1 > $userstats3[gem1])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Sapphire Gems</td></tr></table><p>";
          $page .="You cannot sell that many Sapphire Gems since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost1', gem1=gem1-'$gem1' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get Gems");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Sapphire Gems</td></tr></table><p>";
           $page .="You sold your Sapphire Gems successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellgems'>more</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell Sapphire Gems";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Sapphire Gems</td></tr></table><p>";
        $page .= "How many Sapphire Gems would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellgems1\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"gem1\" size=\"5\" value=\"".$userrow["gem1"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Sapphires\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellgems'>sell</a> different gems, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }
  
  function sellgems2() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
      
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $gem2=$_POST['gem2'];          
        $gem2=strip_tags($gem2);
        $sellcost1=($gem2*400);

        if($gem2<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Emerald Gems</td></tr></table><p>";
           $page .="You may not sell negative Emerald Gems. Stop trying to cheat!<p>";
           $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($gem2 > $userstats3[gem2])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Emerald Gems</td></tr></table><p>";
          $page .="You cannot sell that many Emerald Gems since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost1', gem2=gem2-'$gem2' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get Gems");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Emerald Gems</td></tr></table><p>";
           $page .="You sold your Emerald Gems successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellgems'>more</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell Emerald Gems";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Emerald Gems</td></tr></table><p>";
        $page .= "How many Emerald Gems would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellgems2\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"gem2\" size=\"5\" value=\"".$userrow["gem2"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Sapphires\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellgems'>sell</a> different gems, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }

function sellgems3() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
      
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $gem3=$_POST['gem3'];          
        $gem3=strip_tags($gem3);
        $sellcost1=($gem3*750);

        if($gem3<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ruby Gems</td></tr></table><p>";
           $page .="You may not sell negative Ruby Gems. Stop trying to cheat!<p>";
           $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($gem3 > $userstats3[gem3])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ruby Gems</td></tr></table><p>";
          $page .="You cannot sell that many Ruby Gems since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost1', gem3=gem3-'$gem3' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get Gems");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ruby Gems</td></tr></table><p>";
           $page .="You sold your Ruby Gems successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellgems'>more</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell Ruby Gems";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Ruby Gems</td></tr></table><p>";
        $page .= "How many Ruby Gems would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellgems3\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"gem3\" size=\"5\" value=\"".$userrow["gem3"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Sapphires\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellgems'>sell</a> different gems, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }

  function sellgems4() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
      
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $gem4=$_POST['gem4'];          
        $gem4=strip_tags($gem4);
        $sellcost1=($gem4*1300);

        if($gem4<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Diamond Gems</td></tr></table><p>";
           $page .="You may not sell negative Diamond Gems. Stop trying to cheat!<p>";
           $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($gem4 > $userstats3[gem4])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Diamond Gems</td></tr></table><p>";
          $page .="You cannot sell that many Diamond Gems since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost1', gem4=gem4-'$gem4' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get Gems");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Diamond Gems</td></tr></table><p>";
           $page .="You sold your Diamond Gems successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellgems'>more</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell Diamond Gems";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Diamond Gems</td></tr></table><p>";
        $page .= "How many Diamond Gems would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellgems4\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"gem4\" size=\"5\" value=\"".$userrow["gem4"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Sapphires\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellgems'>sell</a> different gems, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }

  
  function sellgems5() {
global $userrow, $controlrow;


    $townquery = doquery("SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) != 1) { display("<p>You cannot access this Area outside of town. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
    $townrow = mysql_fetch_array($townquery);
      
  
    if(isset($_POST['submit']))
    {
	
	  $userstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
      $userstats2=mysql_query($userstats) or die("Could not get user stats");
      $userstats3=mysql_fetch_array($userstats2);
       
        $gem5=$_POST['gem5'];          
        $gem5=strip_tags($gem5);
        $sellcost1=($gem5*2000);

        if($gem5<=0)
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Black Dragon Gems</td></tr></table><p>";
           $page .="You may not sell negative Black Dragon Gems. Stop trying to cheat!<p>";
           $page .="<p>You may return to <A href='index.php'>town</a> or continue exploring using the compass to the right.";
        }
        
        elseif($gem5 > $userstats3[gem5])
        {
        	        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Black Dragon Gems</td></tr></table><p>";
          $page .="You cannot sell that many Black Dragon Gems since you don't have that amount in your Inventory. You may return to <A href='index.php'>town</a> or continue exploring.";
        }
           else
        {
           $getarmy="update dk_users set gold=gold+'$sellcost1', gem5=gem5-'$gem5' where charname='".$userrow["charname"]."'";
           mysql_query($getarmy) or die("Can't get Gems");
                   $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Black Dragon Gems</td></tr></table><p>";
           $page .="You sold your Black Dragon Gems successfully. You may now go back to <A href='index.php'>town</a> or sell some <A href='sell.php?do=sellgems'>more</a>.";
        }
      
	   }
	   
	   elseif (isset($_POST["cancel"])) {

        header("Location: index.php"); die();

       }
	   else {
	   $title = "Sell Black Dragon Gems";
        $page = "<table width='100%' border='1'><tr><td class='title'>Sell your Black Dragon Gems</td></tr></table><p>";
        $page .= "How many Black Dragon Gems would you like to Sell?\n";
        $page .= "<form action=\"sell.php?do=sellgems5\" method=\"post\">\n";           
        $page .= "<br /><input type=\"text\" name=\"gem5\" size=\"5\" value=\"".$userrow["gem5"]."\" /> <input type=\"submit\" name=\"submit\" value=\"Sell Sapphires\" /><br />\n";
        $page .= "</form>\n";
          
                  $page .="You may return to <A href='index.php'>town</a>, or <A href='sell.php?do=sellgems'>sell</a> different gems, or continue exploring using the compass to the right.";
		}
  
	   display($page, $title);
  }
 
?>