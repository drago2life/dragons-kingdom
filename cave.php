<?php // cave.php :: Handles all random souls and cave areas

function cave() { // Souls

   global $userrow, $numqueries;

$userquery = doquery("SELECT * FROM {{table}} WHERE id='".$userrow["id"]."' LIMIT 1", "users");

if ($userrow["templist"] == "0") {header("Location: index.php"); die(); }
if ($userrow["templist"] != "cave") {header("Location: index.php"); die(); }
if ($userrow["location"] == "Dead") {header("Location: index.php"); die(); }
    $userrow = mysql_fetch_array($userquery);

       $title = "Inside a Cave";
$updatequery = doquery("UPDATE {{table}} SET templist='cave',currentaction='Cave',location='Inside a Cave' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

        $page = "<table width='100%' border='1'><tr><td class='title'>Cave</td></tr></table><p>";

       $page .= "You enter the dark Cave, you feel a chilling breeze run through your back.<p>You then hear a mysterious noise in the breeze which follows you from behind...<p>Be aware of what approachs you young one.<p>Many Souls appear here and it is unsafe for the likes of you. Are you sure you wish to try and battle the Souls for great rewards in experience, gold and on some rare occassions, Dragon Scales? Be aware as they are very strong. <p>Souls usually leave you with 0 HP to heal yourself, either by using the Healing Pool or an Item. However, If you do die, you will not lose any gold or dragon scales, but you will be sent back to Town. Magic is useless here, so I wouldn't even try using your Healing Spells.<br /><br />\n";
       $page .= "<form action=\"index.php?do=cave\" method=\"post\">\n";

       $page .= "<b>Enter the ID Number of the Soul</b>:<br /><input type=\"text\" name=\"id\" value=\"0\" /><br /><br /><input type=\"submit\" name=\"submit\" value=\"Attack a Soul\" /><br /><p>Note: You can return here and fight Souls at a later time if you fail to kill it in your first attack. However, Souls eventually dissapear, if you do not kill them within a set amount of time.<p>\n";
       $page .= "</form>\n";
if(isset($_POST['submit']))
 {

   $yourstats="SELECT * from dk_users where charname='".$userrow["charname"]."'";
   $yourstats2=mysql_query($yourstats) or die("Could not get your stats");
   $yourstats3=mysql_fetch_array($yourstats2);
   $id=$_POST['id'];
   $playerID=strip_tags($id);
   $oppstats="SELECT * from dk_souls where id='$id'";
   $oppstats2=mysql_query($oppstats) or die("Could not get opponent's stats");
   $oppstats3=mysql_fetch_array($oppstats2);

$oppattack = $oppstats3[attack];
$oppdefense = $oppstats3[def];
$yourattack = $yourstats3[attackpower];
$yourdefense = $yourstats3[defensepower];
$opphp = $oppstats3[hp];
$oppexp = $oppstats3[exp];
$oppgold = $oppstats3[gold];
$oppdscales = $oppstats3[dscales];

if($opphp <= 0) {
$page .="<b><font color=red>You cannot fight this Soul because it is already dead, or it has dissapeared!</font><br /><p>Click <a href='index.php?do=move:0'>here</a> to return to exploring or visit the <a href='index.php?do=pool'>Healing Pool</a> to heal.</b>";
}
elseif($yourstats3[currenthp] < 1) {
			$newhp = ceil($userrow["maxhp"]/4);
			$updatequery = doquery("UPDATE {{table}} SET templist='0',currenthp='$newhp',location='Dead',currentaction='In Town',currentmonster='0',currentmonsterhp='0',currentmonstersleep='0',currentmonsterimmune='0',currentfight='0',latitude='0',longitude='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
			$page .= "<table width='100%'><tr><td class='title'>A Soul killed you!</td></tr></table>";
			$page .= "<p><font color=red>You have died from attacking a Soul.</font></b><br /><br />Luckily, Souls are not interested in your Wealth and you lost none of your Gold or Dragon Scales. However, you have been given back a portion of your hit points to continue your journey.<br /><br /><center><img src=\"images/died.gif\" border=\"0\" alt=\"You have Died\" /></a></center><p>You may now continue back to <a href=\"index.php\">town</a>, and we hope you fair better next time.";
			$townquery = doquery("SELECT * FROM {{table}} WHERE name='".$userrow["lasttown"]."' LIMIT 1", "towns");
			$townrow = mysql_fetch_array($townquery);
			$latitude=$townrow["latitude"];
			$longitude=$townrow["longitude"];
						$updatequery = doquery("UPDATE {{table}} SET latitude='$latitude',longitude='$longitude' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
			
}
   elseif($yourattack - $oppdef > $opphp)
 {
       $ptsgained=$oppexp;
       $ptsgoldgained = $oppgold;
       $ptsdscalesgained = $oppdscales;
       $totalatt = $yourattack - $oppdef;
   if($totalatt > $opphp) { $totalatt = $opphp; }


$page .= "<font color=green>You hit the Soul for $totalatt damage!<br />You have slain Soul Number $oppstats3[id] in a battle and gained $ptsgained exp, and $ptsgoldgained gold! You also gained $ptsdscalesgained Dragon Scales.</font><p><center><img src=\"images/victory.gif\" border=\"0\" alt=\"Victory\" /></a></center><p>Click <a href='index.php?do=move:0'>here</a> to return to exploring or visit the <a href='index.php?do=pool'>Healing Pool</a> to heal.<br />\n";
mysql_query("DELETE FROM `dk_souls` WHERE id='".$oppstats3["id"]."' LIMIT 1");


       $updateyourstats="update dk_users set experience=experience+'$ptsgained', dscales=dscales+'$ptsdscalesgained', gold=gold+'$ptsgoldgained' where charname='".$userrow["charname"]."'";
       mysql_query($updateyourstats) or die("Could not update your stats");
       $updateopp="update dk_souls set hp='0' where id='$id'";
       mysql_query($updateopp) or die(mysql_error());
 }
     elseif($yourattack - $oppdef < $opphp)
     {
	   $yourdef = $userrow["defensepower"];
       $uhit4 = $yourattack;
       $ugothit4 = $oppattack - $yourdef;
	   if($ugothit4 < 1) { $ugothit4 = 1; }
              $opphp = number_format($opphp);

       $page .= "<font color=blue>The Soul has <b><big>$opphp</big></b> Hit Points Remaining!</font><br /><br /><font color=green>You did not manage to kill the Soul. You hit the Soul for $uhit4 damage, but you suffered $ugothit4 damage in the Souls counterattack!</font><br /><p>Click <a href='index.php?do=move:0'>here</a> to return to exploring or visit the <a href='index.php?do=pool'>Healing Pool</a> to heal.<br />\n";

    $killyou="update dk_users set currenthp=currenthp-'$ugothit4' where charname='".$userrow["charname"]."'";
       mysql_query($killyou) or die("Could not kill you");
       $foestats="update dk_souls set hp=hp-'$uhit4'  where id='$id'";

       mysql_query($foestats) or die("Could not dishonor you by updating opponent's stats");

     }

  }

else
{
 $page .= "<p>Click <a href='index.php?do=move:0'>here</a> to return to exploring or visit the <a href='index.php?do=pool'>Healing Pool</a> to heal.";
}

if($userrow[currenthp] < 1) {
			$newhp = ceil($userrow["maxhp"]/4);
			$updatequery = doquery("UPDATE {{table}} SET templist='0',currenthp='$newhp',location='Dead',currentaction='In Town',currentmonster='0',currentmonsterhp='0',currentmonstersleep='0',currentmonsterimmune='0',currentfight='0',latitude='0',longitude='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
			$page .= "<table width='100%'><tr><td class='title'>A Soul killed you!</td></tr></table>";
			$page .= "<p><font color=red>You have died from attacking a Soul.</font></b><br /><br />Luckily, Souls are not interested in your Wealth and you lost none of your Gold or Dragon Scales. However, you have been given back a portion of your hit points to continue your journey.<br /><br /><center><img src=\"images/died.gif\" border=\"0\" alt=\"You have Died\" /></a></center><p>You may now continue back to <a href=\"index.php\">town</a>, and we hope you fair better next time.";
			$townquery = doquery("SELECT * FROM {{table}} WHERE name='".$userrow["lasttown"]."' LIMIT 1", "towns");
			$townrow = mysql_fetch_array($townquery);
			$latitude=$townrow["latitude"];
			$longitude=$townrow["longitude"];
						$updatequery = doquery("UPDATE {{table}} SET latitude='$latitude',longitude='$longitude' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
}

   display($page, $title);

}

function pool() { // Healing Pool
    
     global $userrow, $numqueries;

$userquery = doquery("SELECT * FROM {{table}} WHERE id='".$userrow["id"]."' LIMIT 1", "users");
if ($userrow["templist"] == "0") {header("Location: index.php"); die(); }
if ($userrow["templist"] != "cave") {header("Location: index.php"); die(); }
if ($userrow["location"] == "Dead") {header("Location: index.php"); die(); }
    $userrow = mysql_fetch_array($userquery);

  $updatequery = doquery("UPDATE {{table}} SET templist='cave',currentaction='Healing Pool', location='Healing Pool' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
  
$townquery = doquery("SELECT pool FROM {{table}} LIMIT 1", "towns");
    $townrow = mysql_fetch_array($townquery);
    
    if ($userrow["dscales"] < $townrow["pool"]) { display("You do not have enough Dragon Scales to donate to this Healing Pool.<br /><br />You may return to the <a href=\"index.php?do=cave\">cave</a>, or use the compass to the right to start exploring.", "Healing Pool"); die(); }
    
    if (isset($_POST["submit"])) {
        
        $newdscales = $userrow["dscales"] - $townrow["pool"];
        $query = doquery("UPDATE {{table}} SET dscales='$newdscales', templist='cave', drink='Empty', potion='Empty', currenthp='".$userrow["maxhp"]."',currenttp='".$userrow["maxtp"]."' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $title = "Healing Pool";
        $page = "<table width='100%' border='1'><tr><td class='title'>Healing Pool - Refreshed</td></tr></table><p>";
        $page .= "You feel refreshed and ready to fight those Souls once again, with all current stats to maximum capacity.<br /><br />You may return to the <a href=\"index.php?do=cave\">cave</a>, or use the compass on the right to continue exploring.";
        
    } elseif (isset($_POST["cancel"])) {
        
        header("Location: index.php"); die();
         
    } else {
        
        $title = "Healing Pool";

        $page = "<table width='100%' border='1'><tr><td class='title'>Healing Pool</td></tr></table><p>";
        $page .= "Resting at this Healing Pool will refill your current HP and TP to their maximum levels. It will also remove your current Tavern Drink and Potion if you have recently purchased one, and it will not restore MP.<br /><br />\n";
        $page .= "You must leave a small donation to the Healing Pool of <b>" . $townrow["pool"] . " Dragon Scales</b>.<p>Is that ok?<br /><br />\n";
        $page .= "<form action=\"index.php?do=pool\" method=\"post\">\n";
        $page .= "<input type=\"submit\" name=\"submit\" value=\"Donate\" /><p>You may also try your luck at trying to <a href=\"index.php?do=water\">thieve</a> some Healing Water without the Guard noticing.<p>If you have changed your mind you may return to the <a href=\"index.php?do=cave\">cave</a>, or use the compass on the right to continue exploring.\n";
        $page .= "</form>\n";
        
    }
    
    display($page, $title);
    
}

function water() {
    global $userrow, $controlrow;
    if ($userrow["templist"] == "0") {header("Location: index.php"); die(); }
    
    $updatequery = doquery("UPDATE {{table}} SET location='Thieve Water',templist='cave' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
    
if($userrow["quest2"] == "Not Started") { //Die if quest isnt started at all
        $title = "Thieve Water";
$page = "<table width='100%' border='1'><tr><td class='title'>Thieve Water</td></tr></table><p>";
        $page .= "You have no reason to Thieve Healing Water from the Guard.<br /><br />\n";
        $page .= "<br />You may return to <a href=\"index.php?do=move:0\">exploring</a> or go back to the <a href=\"index.php?do=pool\">Healing Pool</a>.<br />\n";

}
elseif($userrow["tempquest"] == "bucket" && (rand(1,9) )== 7) { ///Chance to find a bucket of water
        $inventitems2 = "0,".$userrow["inventitems"].",0";
        //Take away empty bucket
        $newinventitems2 = str_replace(",92,", ",", $inventitems2);
        $userrow["inventitems"] = $newinventitems2;
        $updatequery = doquery("UPDATE {{table}} SET inventitems='$newinventitems2' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 
        $title = "Thieve Water";
        $page .= "<table width='100%' border='1'><tr><td class='title'>Thieve Water</td></tr></table><p>";
        $page .= "You attempt to Thieve some Healing Water from the Healing Pool, by using your Empty Bucket...<p><font color=green>You are successful and the Guard didn't attempt to attack you due to you being too quick and him not noticing. You better escape quickly or else he may notice!</font><br /><br />\n";
        $page .= "<br />You may return to <a href=\"index.php?do=move:0\">exploring</a> or go back to the <a href=\"index.php?do=pool\">Healing Pool</a>.<br />\n";
         $inventitemsquery = doquery("SELECT id FROM {{table}}","inventitems");
        $userinventitems = explode(",",$userrow["inventitems"]);
        //add Quest 2 Item, bucket of water
        array_push($userinventitems, 93); 
        $new_userinventitems = implode(",",$userinventitems);
        $userid = $userrow["id"];
        doquery("UPDATE {{table}} SET inventitems='$new_userinventitems',tempquest='water' WHERE id='$userid' LIMIT 1", "users");


        }

elseif($userrow["tempquest"] == "bucket" && (rand(1,5) )== 2) { //Do 6 damage if fail
                $title = "Thieve Water";
        $page .= "<table width='100%' border='1'><tr><td class='title'>Thieve Water</td></tr></table><p>";
        $page .= "You attempt to Thieve some Healing Water from the Healing Pool, by using your Empty Bucket...<p><font color=red>The Guard catches you and hurts you for 6 damage!</font><br /><br />\n";
        $page .= "<br />You may return to <a href=\"index.php?do=move:0\">exploring</a> or go back to the <a href=\"index.php?do=pool\">Healing Pool</a>.<br />\n";
                doquery("UPDATE {{table}} SET currenthp=currenthp-6 WHERE id='".$userrow["id"]."' LIMIT 1", "users");

 }
 
elseif($userrow["tempquest"] == "bucket" && (rand(3,5) )== 4) { //Do 4 damage if fail
                $title = "Thieve Water";
        $page .= "<table width='100%' border='1'><tr><td class='title'>Thieve Water</td></tr></table><p>";
        $page .= "You attempt to Thieve some Healing Water from the Healing Pool, by using your Empty Bucket ...<p><font color=red>The Guard catches you and hurts you for 4 damage!</font><br /><br />\n";
        $page .= "<br />You may return to <a href=\"index.php?do=move:0\">exploring</a> or go back to the <a href=\"index.php?do=pool\">Healing Pool</a>.<br />\n";
                doquery("UPDATE {{table}} SET currenthp=currenthp-4 WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        }
elseif($userrow["tempquest"] == "3")  { //Check to see if they have picked up the empty bucket first
        $title = "Thieve Water";
        $page .= "<table width='100%' border='1'><tr><td class='title'>Thieve Water</td></tr></table><p>";
        $page .= "You can't thieve any Healing Water with just your hands. You will need something to carry it in.<br /><br />\n";
        $page .= "<br />You may return to <a href=\"index.php?do=move:0\">exploring</a> or go back to the <a href=\"index.php?do=pool\">Healing Pool</a>.<br />\n";


     }
elseif($userrow["quest2"] == "Complete")  { //Quest complete, prevent from getting more water
        $title = "Thieve Water";
        $page .= "<table width='100%' border='1'><tr><td class='title'>Thieve Water</td></tr></table><p>";
        $page .= "Do you really think I am stupid enough to allow you to thieve more Healing Water from me? Get out of my Cave this instance!<br /><br />\n";
        $page .= "<br />You may return to <a href=\"index.php?do=move:0\">exploring</a> or go back to the <a href=\"index.php?do=pool\">Healing Pool</a>.<br />\n";
           
           
                }
elseif($userrow["tempquest"] == "water")  { //water got, prevent from getting more water
        $title = "Thieve Water";
        $page .= "<table width='100%' border='1'><tr><td class='title'>Thieve Water</td></tr></table><p>";
        $page .= "Do you really think I am stupid enough to allow you to thieve more Healing Water from me? Get out of my Cave this instance!<br /><br />\n";
        $page .= "<br />You may return to <a href=\"index.php?do=move:0\">exploring</a> or go back to the <a href=\"index.php?do=pool\">Healing Pool</a>.<br />\n";
           }
else  { //If fail to thieve water, or dont get hurt, display this.
        $title = "Thieve Water";
        $page .= "<table width='100%' border='1'><tr><td class='title'>Thieve Water</td></tr></table><p>";
        $page .= "You attempt to fill your Empty Bucket up with water.<p>You fail to even get anywhere near to thieving the Healing Water. Be aware next time as the Guard is watching you closely.<br /><br />\n";
        $page .= "<br />You may return to <a href=\"index.php?do=move:0\">exploring</a> or go back to the <a href=\"index.php?do=pool\">Healing Pool</a>.<br />\n";

        
}


        display($page, $title);
              
}

?>