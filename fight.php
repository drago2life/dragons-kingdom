<?php // fight.php :: Handles all fighting action.

function fight() { // One big long function that determines the outcome of the fight.
 global $userrow, $controlrow;
  if ($userrow["currentaction"] != "Fighting") { display("<p>You cannot do this action without being in a fight already. You may now return to <a href=\"index.php\">exploring</a>.", "Error"); }
 $pagearray = array();
 $playerisdead = 0;

 $pagearray["magiclist"] = "";
 $userspells = explode(",",$userrow["spells"]);
 $spellquery = doquery("SELECT id,name FROM {{table}}", "spells");
 while ($spellrow = mysql_fetch_array($spellquery)) {
     $spell = false;
     foreach ($userspells as $a => $b) {
         if ($b == $spellrow["id"]) { $spell = true; }
     }
     if ($spell == true) {
         $pagearray["magiclist"] .= "<option value=\"".$spellrow["id"]."\">".$spellrow["name"]."</option>\n";
     }
     unset($spell);
 }
 if ($pagearray["magiclist"] == "") { $pagearray["magiclist"] = "<option value=\"0\">None</option>\n"; }
 $magiclist = $pagearray["magiclist"];


    $itemquery = doquery("SELECT * FROM {{table}}","inventitems");
    $useritems = explode(",",$userrow["inventitems"]);
    $pagearray["inventitemlist"] = "";
    $count = 0;
    $anyitems = false;
    while ($count <= 49) {
    	if ($useritems[$count] != 0) {
    	 	$itemquery = doquery("SELECT * FROM {{table}} WHERE id='".$useritems[$count]."' LIMIT 1","inventitems");
    		$itemrow = mysql_fetch_array($itemquery);
			if ($itemrow["combatOK"] != 1) {
    		$pagearray["inventitemlist"] .= "<option value='".$count."'>".$itemrow["name"]."</option>\n";
			}
    		$anyitems = true;
    	}
    	$count += 1;
    }
    if ($anyitems = false) { $pagearray["inventitemlist"] = "<option value=\"0\">None</option>\n"; }
    $itemlist = $pagearray["inventitemlist"];


    $chancetoswingfirst = 1;


 $chancetoswingfirst = 1;

 // First, check to see if we need to pick a monster.
 if ($userrow["currentfight"] == 1) {

     if ($userrow["latitude"] < 0) { $userrow["latitude"] *= -1; } // Equalize negatives.
     if ($userrow["longitude"] < 0) { $userrow["longitude"] *= -1; } // Ditto.
     $maxlevel = floor(max($userrow["latitude"]+5, $userrow["longitude"]+5) / 5); // One mlevel per five spaces.
     if ($maxlevel < 1) { $maxlevel = 1; }
     $minlevel = $maxlevel - 2;
     if ($minlevel < 1) { $minlevel = 1; }

     // Pick a monster.
     $monsterquery = doquery("SELECT * FROM {{table}} WHERE level>='$minlevel' AND level<='$maxlevel' AND boss='0' ORDER BY RAND() LIMIT 1", "monsters");
     $monsterrow = mysql_fetch_array($monsterquery);
     $userrow["currentmonster"] = $monsterrow["id"];
     $userrow["currentmonsterhp"] = rand((($monsterrow["maxhp"]/5)*4),$monsterrow["maxhp"]);
     $userrow["currentmonstersleep"] = 0;
     $userrow["currentmonsterimmune"] = $monsterrow["immune"];

     $chancetoswingfirst = rand(1,10) + ceil(sqrt($userrow["dexterity"]));
     if ($chancetoswingfirst > (rand(1,7) + ceil(sqrt($monsterrow["maxdam"])))) { $chancetoswingfirst = 1; } else { $chancetoswingfirst = 0; }

     unset($monsterquery);
     unset($monsterrow);

 }

 // Next, get the monster statistics.
 $monsterquery = doquery("SELECT * FROM {{table}} WHERE id='".$userrow["currentmonster"]."' LIMIT 1", "monsters");
 $monsterrow = mysql_fetch_array($monsterquery);
 $pagearray["monstername"] = $monsterrow["name"];
$pagearray["monsterid"] = $monsterrow["id"];
 $pagearray["monsterlevel"] = $monsterrow["level"];
  $pagearray["cweap"] = $monsterrow["cweap"];
  $pagearray["carm"] = $monsterrow["carm"];
  $pagearray["cshield"] = $monsterrow["cshield"];

 // Do run stuff.
 if (isset($_POST["run"])) {

     $chancetorun = rand(4,10) + ceil(sqrt($userrow["dexterity"]));
     if ($chancetorun > (rand(1,5) + ceil(sqrt($monsterrow["maxdam"])))) { $chancetorun = 1; } else { $chancetorun = 0; }

     if ($chancetorun == 0) {
         $pagearray["yourturn"] = "You tried to run away, but were blocked in front!<br /><br />";
         $pagearray["monsterhp"] = "Monster's HP: " . $userrow["currentmonsterhp"] . "<br /><br />";
         $pagearray["monsterturn"] = "";
         if ($userrow["currentmonstersleep"] != 0) { // Check to wake up.
             $chancetowake = rand(1,15);
             if ($chancetowake > $userrow["currentmonstersleep"]) {
                 $userrow["currentmonstersleep"] = 0;
                 $pagearray["monsterturn"] .= "The monster has woken up!<br />";
             } else {
                 $pagearray["monsterturn"] .= "The monster is still asleep..<br />";
             }
         }
         if ($userrow["currentmonstersleep"] == 0) { // Only do this if the monster is awake.
             $tohit = ceil(rand($monsterrow["maxdam"]*.5,$monsterrow["maxdam"]));
             $toblock = ceil(rand($userrow["defensepower"]*.75,$userrow["defensepower"])/4);
             $tododge = rand(1,150);
             if ($tododge <= sqrt($userrow["dexterity"])) {
                 $tohit = 0; $pagearray["monsterturn"] .= "You luckily manage to dodge the monster's attack. No damage has been caused...<br />";
                 $persondamage = 0;
             } else {
                 $persondamage = $tohit - $toblock;
                 if ($persondamage < 1) { $persondamage = 1; }
                 if ($userrow["currentuberdefense"] != 0) {
                     $persondamage -= ceil($persondamage * ($userrow["currentuberdefense"]/100));
                 }
                 if ($persondamage < 1) { $persondamage = 1; }
             }
             $pagearray["monsterturn"] .= "The monster attacks you for $persondamage damage.<br /><br />";
             $userrow["currenthp"] -= $persondamage;
             if ($userrow["currenthp"] <= 0) {
                 $newgold = ceil($userrow["gold"]/2);
                 $newhp = ceil($userrow["maxhp"]/4);
                 $newdscales = ceil($userrow["dscales"]/3);
                 $updatequery = doquery("UPDATE {{table}} SET dscales='$newdscales',currenthp='$newhp',currentaction='In Town',location='Dead',currentmonster='0',currentmonsterhp='0',currentmonstersleep='0',currentmonsterimmune='0',currentfight='0',latitude='0',longitude='0',gold='$newgold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
                 $playerisdead = 1;
             }
         }
     }

     $updatequery = doquery("UPDATE {{table}} SET location='Exploring',currentaction='Exploring' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
     header("Location: index.php");
     die();

 // Do fight stuff.
 } elseif (isset($_POST["fight"])) {

     // Your turn.
     $pagearray["yourturn"] = "";
     $tohit = ceil(rand($userrow["attackpower"]*.75,$userrow["attackpower"])/3);
     $toexcellent = rand(1,150);
     if ($toexcellent <= sqrt($userrow["strength"])) { $tohit *= 2; $pagearray["yourturn"] .= "Excellent hit! You sure are a mighty adventurer!</b><br />"; }
     $toblock = ceil(rand($monsterrow["armor"]*.75,$monsterrow["armor"])/3);
     $tododge = rand(1,200);
     if ($tododge <= sqrt($monsterrow["armor"])) {
         $tohit = 0; $pagearray["yourturn"] .= "The monster managed to dodge your attack. No damage has been caused...<br />";
         $monsterdamage = 0;
     } else {
         $monsterdamage = $tohit - $toblock;
         if ($monsterdamage < 1) { $monsterdamage = 1; }
         if ($userrow["currentuberdamage"] != 0) {
             $monsterdamage += ceil($monsterdamage * ($userrow["currentuberdamage"]/100));
         }
     }
     $bdlevel = $userrow["skill3level"];
      $urstr = $userrow["strength"];
      $moblevel = $monsterrow["level"];
      if($moblevel >= $bdlevel) {
      $bonusdmg1 = $bdlevel * ($urstr / (105 - $bdlevel));
     $bonusdmg2 = (float)$bonusdmg1;
settype($bonusdmg1,"float");
$bonusdmg = ceil($bonusdmg2);
      if($bonusdmg < 1) { $bonusdmg = 1; }
}

      else {
      $bonusdmg = $userrow["currentmonsterhp"];
}
if ($userrow["currentmonsterhp"] <= 0) {
          $updatequery = doquery("UPDATE {{table}} SET currentmonsterhp='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
          header("Location: index.php?do=victory");
          die();
}

      $pagearray["yourturn"] .= "You attack the monster using your ".$userrow["weaponname"]." for $monsterdamage damage.<br /><font color=blue>Your Monks Mind skill causes $bonusdmg damage!</font><br />";
   $monsterdamage += $bonusdmg;
      $userrow["currentmonsterhp"] -= $monsterdamage;
      $pagearray["monsterhp"] = "Monster's HP: " . $userrow["currentmonsterhp"] . "<br /><br />";
      if ($userrow["currentmonsterhp"] <= 0) {
          $updatequery = doquery("UPDATE {{table}} SET currentmonsterhp='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
          header("Location: index.php?do=victory");
          die();
      }

     // Monsters turn.
     $pagearray["monsterturn"] = "";
     if ($userrow["currentmonstersleep"] != 0) { // Check to wake up.
         $chancetowake = rand(1,15);
         if ($chancetowake > $userrow["currentmonstersleep"]) {
             $userrow["currentmonstersleep"] = 0;
             $pagearray["monsterturn"] .= "The monster has woken up!<br />";
         } else {
             $pagearray["monsterturn"] .= "The monster is still asleep..<br />";
         }
     }
     if ($userrow["currentmonstersleep"] == 0) { // Only do this if the monster is awake.
         $tohit = ceil(rand($monsterrow["maxdam"]*.5,$monsterrow["maxdam"]));
         $toblock = ceil(rand($userrow["defensepower"]*.75,$userrow["defensepower"])/4);
         $tododge = rand(1,150);
         if ($tododge <= sqrt($userrow["dexterity"])) {
             $tohit = 0; $pagearray["monsterturn"] .= "You manage to avoid and block the monster's attack using your ".$userrow["shieldname"].". No damage has been caused...<br />";
             $persondamage = 0;
         } else {
             $persondamage = $tohit - $toblock;
             if ($persondamage < 1) { $persondamage = 1; }
             if ($userrow["currentuberdefense"] != 0) {
                 $persondamage -= ceil($persondamage * ($userrow["currentuberdefense"]/100));
             }
             if ($persondamage < 1) { $persondamage = 1; }
         }

        $defskilllevel = $userrow["skill2level"];
                      $dmgtakeaway = $persondamage*($defskilllevel/100);
                      if($dmgtakeaway<1) { $dmgtakeaway==1; }

                      $tdmg = $persondamage - $dmgtakeaway;

          $pagearray["monsterturn"] .= "The monster attacks you for $persondamage damage.<br /><font color=red>But your Stone Skin skill absorbed <b>$dmgtakeaway</b> of the damage!</font>";

          $userrow["currenthp"] -= $tdmg;

         if ($userrow["currenthp"] <= 0) {
             $newgold = ceil($userrow["gold"]/2);
             $newhp = ceil($userrow["maxhp"]/4);
             $newdscales = ceil($userrow["dscales"]/3);
             $updatequery = doquery("UPDATE {{table}} SET dscales='$newdscales',currenthp='$newhp',location='Dead',currentaction='In Town',currentmonster='0',currentmonsterhp='0',currentmonstersleep='0',currentmonsterimmune='0',currentfight='0',latitude='0',longitude='0',gold='$newgold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
             $playerisdead = 1;
         }
     }

 // Do spell stuff.
 } elseif (isset($_POST["spell"])) {

     // Your turn.
     $pickedspell = $_POST["userspell"];
     if ($pickedspell == 0) { display("You must select a spell first. Please go <a href=\"index.php\">back</a> and try again.", "Select a Spell"); die(); }

     $newspellquery = doquery("SELECT * FROM {{table}} WHERE id='$pickedspell' LIMIT 1", "spells");
     $newspellrow = mysql_fetch_array($newspellquery);
     $spell = false;
     foreach($userspells as $a => $b) {
         if ($b == $pickedspell) { $spell = true; }
     }
     if ($pickedspell != true) { display("You have not yet learnt this spell. Please go <a href=\"index.php\">back</a> and try again.", "Not learnt this spell yet"); die(); }
     if ($userrow["currentmp"] < $newspellrow["mp"]) { display("You do not have enough Magic Points to cast this spell. Please go <a href=\"index.php\">back</a> and try again.", "Not enough Magic Points"); die(); }

     if ($newspellrow["type"] == 1) { // Heal spell.
         $newhp = $userrow["currenthp"] + $newspellrow["attribute"];
         if ($userrow["maxhp"] < $newhp) { $newspellrow["attribute"] = $userrow["maxhp"] - $userrow["currenthp"]; $newhp = $userrow["currenthp"] + $newspellrow["attribute"]; }
         $userrow["currenthp"] = $newhp;
         $userrow["currentmp"] -= $newspellrow["mp"];
         $pagearray["yourturn"] = "You have cast the ".$newspellrow["name"]." spell, and gained ".$newspellrow["attribute"]." Hit Points.<br /><br />";
     } elseif ($newspellrow["type"] == 2) { // Hurt spell.
         if ($userrow["currentmonsterimmune"] == 0) {
             $monsterdamage = rand((($newspellrow["attribute"]/6)*5), $newspellrow["attribute"]);
             $userrow["currentmonsterhp"] -= $monsterdamage;
             $pagearray["yourturn"] = "You have cast the ".$newspellrow["name"]." spell for $monsterdamage damage.<br /><br />";
         } else {
             $pagearray["yourturn"] = "You have cast the ".$newspellrow["name"]." spell, but the monster is immune to it.<br /><br />";
         }
         $userrow["currentmp"] -= $newspellrow["mp"];
     } elseif ($newspellrow["type"] == 3) { // Sleep spell.
         if ($userrow["currentmonsterimmune"] != 2) {
             $userrow["currentmonstersleep"] = $newspellrow["attribute"];
             $pagearray["yourturn"] = "You have cast the ".$newspellrow["name"]." spell. The monster is asleep.<br /><br />";
         } else {
             $pagearray["yourturn"] = "You have cast the ".$newspellrow["name"]." spell, but the monster is immune to it.<br /><br />";
         }
         $userrow["currentmp"] -= $newspellrow["mp"];
     } elseif ($newspellrow["type"] == 4) { // +Damage spell.
         $userrow["currentuberdamage"] = $newspellrow["attribute"];
         $userrow["currentmp"] -= $newspellrow["mp"];
         $pagearray["yourturn"] = "You have cast the ".$newspellrow["name"]." spell, and will gain ".$newspellrow["attribute"]."% damage until the end of this fight.<br /><br />";
     } elseif ($newspellrow["type"] == 6) { // Pet Capture spell.
         $userrow["currentmp"] -= $newspellrow["mp"];
	     $randomchance = intval(rand(($newspellrow["attribute"]*.75),$newspellrow["attribute"]));
	     if (($randomchance <= $monsterrow["level"]) || ($userrow["currentmonsterhp"] >= ($monsterrow["maxhp"]/2) ) ) {
	 		//backfires!!
	  		$pagearray["yourturn"] .= "<font color=blue>You have used the ".$newspellrow["name"]." spell, ";
	  		$pagearray["yourturn"] .= "but the creature is too strong to be captured!</font><p><br />";
	  	} else {
	  		$petquery = doquery("SELECT * FROM {{table}} WHERE trainer='".$userrow["charname"]."'","arena");
	  		if (mysql_num_rows($petquery) <= 4) {
	  			$updatequery = doquery("UPDATE {{table}} SET currentmonsterhp='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	  			header("Location: index.php?do=capture"); die();
	  		} else {
	  			$pagearray["yourturn"] .= "<font color=red>You have used the ".$newspellrow["name"]." spell but you already have 5 pets captured for the Pet Arena.<p>";
	  			$pagearray["yourturn"] .= "You must release one of your Pets before you can capture more monsters.</font><p>";
	  		}
	  	}

     } elseif ($newspellrow["type"] == 5) { // +Defense spell.
         $userrow["currentuberdefense"] = $newspellrow["attribute"];
         $userrow["currentmp"] -= $newspellrow["mp"];
         $pagearray["yourturn"] = "You have cast the ".$newspellrow["name"]." spell, and will gain ".$newspellrow["attribute"]."% defense until the end of this fight.<br /><br />";
	}


     $pagearray["monsterhp"] = "Monster's HP: " . $userrow["currentmonsterhp"] . "<br /><br />";
     if ($userrow["currentmonsterhp"] <= 0) {
         $updatequery = doquery("UPDATE {{table}} SET currentmonsterhp='0',currenthp='".$userrow["currenthp"]."',currentmp='".$userrow["currentmp"]."' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
         header("Location: index.php?do=victory");
         die();
     }

     // Monsters turn.
     $pagearray["monsterturn"] = "";
     if ($userrow["currentmonstersleep"] != 0) { // Check to wake up.
         $chancetowake = rand(1,15);
         if ($chancetowake > $userrow["currentmonstersleep"]) {
             $userrow["currentmonstersleep"] = 0;
             $pagearray["monsterturn"] .= "The monster has woken up!<br />";
         } else {
             $pagearray["monsterturn"] .= "The monster is still asleep..<br />";
         }
     }
     if ($userrow["currentmonstersleep"] == 0) { // Only do this if the monster is awake.
         $tohit = ceil(rand($monsterrow["maxdam"]*.5,$monsterrow["maxdam"]));
         $toblock = ceil(rand($userrow["defensepower"]*.75,$userrow["defensepower"])/4);
         $tododge = rand(1,150);
         if ($tododge <= sqrt($userrow["dexterity"])) {
             $tohit = 0; $pagearray["monsterturn"] .= "You block the monster's attack with your ".$userrow["shieldname"].". No damage has been caused..<br />";
             $persondamage = 0;
         } else {
             if ($tohit <= $toblock) { $tohit = $toblock + 1; }
             $persondamage = $tohit - $toblock;
             if ($userrow["currentuberdefense"] != 0) {
                 $persondamage -= ceil($persondamage * ($userrow["currentuberdefense"]/100));
             }
             if ($persondamage < 1) { $persondamage = 1; }
         }
         $pagearray["monsterturn"] .= "The monster attacks you and causes $persondamage damage to your hit points.<br /><br />";
         $userrow["currenthp"] -= $persondamage;
         if ($userrow["currenthp"] <= 0) {
             $newgold = ceil($userrow["gold"]/2);
             $newhp = ceil($userrow["maxhp"]/4);
             $newdscales = ceil($userrow["dscales"]/3);
             $updatequery = doquery("UPDATE {{table}} SET dscales='$newdscales',currenthp='$newhp',location='Dead',currentaction='In Town',currentmonster='0',currentmonsterhp='0',currentmonstersleep='0',currentmonsterimmune='0',currentfight='0',latitude='0',longitude='0',gold='$newgold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
             $playerisdead = 1;
         }
     }


// Do item stuff.
 } elseif (isset($_POST["useritem"])) {

     // Your turn.
		$useritems = explode(",",$userrow["inventitems"]);
		$pickeditem = $useritems[$_POST["useritem"]];
		if ($pickeditem == 0) { display("You must select an item first. Please go back and try again.", "Error"); die(); }

		$newitemquery = doquery("SELECT * FROM {{table}} WHERE id='$pickeditem' LIMIT 1", "inventitems");
		$newitemrow = mysql_fetch_array($newitemquery);
		$charges = $newitemrow["charges"];

		if ($newitemrow["type"] == 1) { // Healing items
			$newhp = $userrow["currenthp"] + $newitemrow["strength"];
if ($userrow["maxhp"] < $newhp) { $newitemrow["strength"] = $userrow["maxhp"] - $userrow["currenthp"]; $newhp = $userrow["maxhp"]; }
			$userrow["currenthp"] = $newhp;
			$pagearray["yourturn"] .= "You have used the ".$newitemrow["name"]." and gained ".$newitemrow["strength"]." Hit Points.<br /><br />";


		} elseif ($newitemrow["type"] == 14) { // TP restore items
			$newtp = $userrow["currenttp"] + $newitemrow["strength"];
if ($userrow["maxtp"] < $newtp) { $newitemrow["strength"] = $userrow["maxtp"] - $userrow["currenttp"]; $newtp = $userrow["maxtp"]; }
			$userrow["currenthp"] = $newtp;
			$pagearray["yourturn"] .= "You have used the ".$newitemrow["name"]." and gained ".$newitemrow["strength"]." Travel Points.<br /><br />";


		} elseif ($newitemrow["type"] == 2) {    // Hurt item.
			$immune = strpos($userrow["currentmonsterimmune"], $newitemrow["type"]);
			if (!$immune) {
			$monsterdamage = rand((($newitemrow["strength"]/6)*5), $newitemrow["strength"]);
			$monsterdamage -= intval($monsterdamage*($monsterrow["magicarmor"]/100));
			$monsterdamage += $magicgain;
			$userrow["currentmonsterhp"] -= $monsterdamage;
				if ($monsterdamage < 0) {
				$pagearray["yourturn"] .= "<b>The monster was healed by the ".$newitemrow["name"]."!</b>.<br /><br />";
				} else {
				$pagearray["yourturn"] .= "You have used the ".$newitemrow["name"]." item and dealt $monsterdamage damage to the enemy.<br /><br />";
				}
			} else {
				$pagearray["yourturn"] .= "You have use the ".$newitemrow["name"]." item, but the monster is immune to it.<br /><br />";
			}



			} elseif ($newitemrow["type"] == 3) {  // Sleep item.
			$immune = strpos($userrow["currentmonsterimmune"], $newitemrow["type"]);
				if (!$immune) {
				$userrow["currentmonstersleep"] = $newitemrow["strength"] + 100;
				$pagearray["yourturn"] .= "You have used the ".$newitemrow["name"]." item. The monster is asleep/stunned.<br /><br />";
				} else {
				$pagearray["yourturn"] .= "You have used the ".$newitemrow["name"]." item, but the monster is immune to it.<br /><br />";
				}




			} elseif ($newitemrow["type"] == 4) { // Poison item.
				$immune = strpos($userrow["currentmonsterimmune"], $newitemrow["type"]);
				if (!$immune) {
				$userrow["currentmonsterpoison"] = $newitemrow["strength"];
				$pagearray["yourturn"] .= "You have used the ".$newitemrow["name"]." item and the monster has been poisoned.<br /><br />";
				} else {
				$pagearray["yourturn"] .= "You have used the ".$newitemrow["name"]." item, but the monster is immune to it.<br /><br />";
				}




			} elseif ($newitemrow["type"] == 5) { // Weaken item.
				$immune = strpos($userrow["currentmonsterimmune"], $newitemrow["type"]);
				if (!$immune) {
				$userrow["currentuberdamage"] += $newitemrow["strength"];
				$userrow["currentuberdefense"] += $newitemrow["strength"];
				$userrow["currentmonsterhp"] -= intval($userrow["currentmonsterhp"]*($newitemrow["strength"]/100));
				$pagearray["yourturn"] .= "You have used the ".$newitemrow["name"]." item, and weaken the monster by ".($newitemrow["strength"])."%.<br /><br />";
				} else {
				$pagearray["yourturn"] .= "You have used the ".$newitemrow["name"]." item, but the monster is immune to it.<br /><br />";
				}



			} elseif ($newitemrow["type"] == 6) { // +Damage item.
				$userrow["currentuberdamage"] = $newitemrow["stregnth"];
				$pagearray["yourturn"] .= "You have used the ".$newitemrow["name"]." item, and will gain ".($newitemrow["stregnth"])."% damage until the end of this fight.<br /><br />";



			} elseif ($newitemrow["type"] == 7) { // +Defense item.
				$userrow["currentuberdefense"] = $newitemrow["strength"];
				$pagearray["yourturn"] .= "You have used the ".$newitemrow["name"]." item, and will gain ".($newitemrow["strength"])."% defense until the end of this fight.<br /><br />";



			} elseif ($newitemrow["type"] == 8) { // Drain item.
				$immune = strpos($userrow["currentmonsterimmune"], $newitemrow["type"]);
				if (!$immune) {
				$monsterdamage = rand((($newitemrow["strength"]/6)*5), $newitemrow["strength"]);
				$monsterdamage -= intval($monsterdamage*($monsterrow["magicarmor"]/100));
				$userrow["currentmonsterhp"] -= $monsterdamage;
				$userrow["currenthp"] += $monsterdamage;
				if ($userrow["maxhp"] < $userrow["currenthp"]) {$userrow["currenthp"] = $userrow["maxhp"];}
				$pagearray["yourturn"] .= "You have used the ".$newitemrow["name"]." item and drain $monsterdamage HP from the enemy.<br /><br />";
				} else {
				$pagearray["yourturn"] .= "You have used the ".$newspellrow["name"]." item, but the monster is immune to it.<br /><br />";
				}



			} elseif ($newitemrow["type"] == 9) { // Chance item.
				$immune = strpos($userrow["currentmonsterimmune"], $newitemrow["type"]);
				$randomchance = rand(1,$newitemrow["strength"]);
				if ($randomchance == 1 ) {   //backfires!!
					$monsterdamage = intval(rand((($userrow["level"]/6)*5),$userrow["level"]));
					$userrow["currenthp"] -= $monsterdamage;
					$pagearray["yourturn"] .= "You have used the ".$newitemrow["name"]." item ";
					$pagearray["yourturn"] .= "but it <b>backfires</b> for $monsterdamage damage!<br><br />";
				} else {
					if (!$immune) {
					$monsterdamage = intval(rand(($userrow["level"]*5),$userrow["level"]*7))*3;
					$userrow["currentmonsterhp"] -= $monsterdamage;
					$pagearray["yourturn"] .= "You have used the ".$newitemrow["name"]." item for $monsterdamage damage.<br /><br />";
					} else {
					$pagearray["yourturn"] .= "You have used the ".$newitemrow["name"]." item, but the monster is immune to it.<br /><br />";
					}
				}




			} elseif ($newitemrow["type"] == 10) { // Death item - X out of 100 chance to die.
				$immune = strpos($userrow["currentmonsterimmune"], $newitemrow["type"]);
				if (!$immune) {
				$tempdeath = rand(1,100);
					if ($newitemrow["strength"] <= $tempdeath) {
					$userrow["currentmonsterhp"] = 0;
					} else {
					$pagearray["yourturn"] .= "You have used the ".$newitemrow["name"]." item but it has no effect.<br><br>";
					}
				}




			} elseif ($newitemrow["type"] == 12) { // Elemental item.
				$immune = strpos($userrow["currentmonsterimmune"], $newitemrow["type"]);
				if (!$immune) {
				$monsterdamage = $newitemrow["strength"];
				$userrow["currentmonsterhp"] -= $monsterdamage;
					if ($monsterdamage < 0) {
					$pagearray["yourturn"] .= "<b>The monster was healed by the ".$newitemrow["name"]."!</b>.<br /><br />";
					} else {
					$pagearray["yourturn"] .= "You have used the ".$newitemrow["name"]." item and dealt $monsterdamage damage to the enemy.<br /><br />";
					}
				} else {
				$pagearray["yourturn"] .= "You have used the ".$newitemrow["name"]." item, but the monster is immune to it.<br /><br />";
				}



			} elseif ($newitemrow["type"] == 19) { // Pet Capture item.
				$randomchance = intval(rand(($newitemrow["strength"]/4),$newitemrow["strength"]));
				if (($randomchance <= $monsterrow["level"]) || ($userrow["currentmonsterhp"] >= ($monsterrow["maxhp"]/2) ) ) {   //backfires!!
				$pagearray["yourturn"] .= "You have used the ".$newitemrow["name"]." item, ";
				$pagearray["yourturn"] .= "but the creature is too strong to be captured!<br><br />";
				} else {
				$petquery = doquery("SELECT * FROM {{table}} WHERE trainer='".$userrow["charname"]."'","arena");
					if (mysql_num_rows($petquery) <= 4) {
					$updatequery = doquery("UPDATE {{table}} SET currentmonsterhp='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
					header("Location: index.php?do=capture");
					die();
					} else {
					$pagearray["yourturn"] .= "You have used the ".$newitemrow["name"]." item but you already have 5 pets captured for the Arena.<br />";
					$pagearray["yourturn"] .= "You must release one of your Pets before you can capture more monsters.<p>";
					}
				}



			} elseif ($newitemrow["type"] == 13) { // Magic restore item.
				$mpdrain = $newitemrow["strength"];
				$mpdrain += $magicgain;
				$userrow["currentmp"] += $mpdrain;
				if ($userrow["maxmp"] < $userrow["currentmp"]) {$userrow["currentmp"] = $userrow["maxmp"];}

				$pagearray["yourturn"] .= "You have used the ".$newitemrow["name"]. " item and recover $mpdrain magic points.<br>";


			} elseif ($newitemrow["type"] == 17) { // Antidote item.
				$userrow["status"] = 0;
				$pagearray["yourturn"] .= "You have used the ".$newitemrow["name"]." item and recovered from the Poison.<br /><br />";

			} else {
			$pagearray["yourturn"] .= "You have used an invalid item type. - nothing happens!!<p><br>";
			}

	    $useritems = explode(",",$userrow["inventitems"]);
        if ($charges <= rand(1,100) ) {$useritems[$_POST["useritem"]] = "0";}
	    $newitems = rsort($useritems);
	    $newitems = join(",",$useritems);
$userrow["inventitems"] = $newitems;
		$updatequery = doquery("UPDATE {{table}} SET inventitems='$newitems' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	     $itemquery = doquery("SELECT * FROM {{table}}","inventitems");
    $useritems = explode(",",$userrow["inventitems"]);
    $pagearray["inventitemlist"] = "";
    $count = 0;
    $anyitems = false;
    while ($count <= 49) {
    	if ($useritems[$count] != 0) {
    	 	$itemquery = doquery("SELECT * FROM {{table}} WHERE id='".$useritems[$count]."' LIMIT 1","inventitems");
    		$itemrow = mysql_fetch_array($itemquery);
			if ($itemrow["combatOK"] != 1) {
    		$pagearray["inventitemlist"] .= "<option value='".$count."'>".$itemrow["name"]."</option>\n";
			}
    		$anyitems = true;
    	}
    	$count += 1;
    }
    if ($anyitems = false) { $pagearray["inventitemlist"] = "<option value=\"0\">None</option>\n"; }
    $itemlist = $pagearray["inventitemlist"];

      $monsterquery = doquery("SELECT * FROM {{table}} WHERE id='".$userrow["currentmonster"]."' LIMIT 1", "monsters");
      $monsterrow = mysql_fetch_array($monsterquery);
      $monmaxhp = $mosterrow["maxhp"];
      if ($monmaxhp > $userrow["currentmonsterhp"]) {$userrow["currentmonsterhp"] = $monsterrow["maxhp"];}

        if ($userrow["currentmonsterhp"] <= 0) {
            $updatequery = doquery("UPDATE {{table}} SET currentmonsterhp='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
            header("Location: index.php?do=victory");
            die();
        }

     // Monsters turn.
     $pagearray["monsterturn"] = "";
     if ($userrow["currentmonstersleep"] != 0) { // Check to wake up.
         $chancetowake = rand(1,15);
         if ($chancetowake > $userrow["currentmonstersleep"]) {
             $userrow["currentmonstersleep"] = 0;
             $pagearray["monsterturn"] .= "The monster has woken up!<br />";
         } else {
             $pagearray["monsterturn"] .= "The monster is still asleep..<br />";
         }
     }
     if ($userrow["currentmonstersleep"] == 0) { // Only do this if the monster is awake.
         $tohit = ceil(rand($monsterrow["maxdam"]*.5,$monsterrow["maxdam"]));
         $toblock = ceil(rand($userrow["defensepower"]*.75,$userrow["defensepower"])/4);
         $tododge = rand(1,150);
         if ($tododge <= sqrt($userrow["dexterity"])) {
             $tohit = 0; $pagearray["monsterturn"] .= "You block the monster's attack with your ".$userrow["shieldname"].". No damage has been caused..<br />";
             $persondamage = 0;
         } else {
             if ($tohit <= $toblock) { $tohit = $toblock + 1; }
             $persondamage = $tohit - $toblock;
             if ($userrow["currentuberdefense"] != 0) {
                 $persondamage -= ceil($persondamage * ($userrow["currentuberdefense"]/100));
             }
             if ($persondamage < 1) { $persondamage = 1; }
         }
         $pagearray["monsterturn"] .= "The monster attacks you and causes $persondamage damage to your hit points.<br /><br />";
         $userrow["currenthp"] -= $persondamage;
$pagearray["monsterhp"] = "Monster's HP: " . $userrow["currentmonsterhp"] . "<br /><br />";
         if ($userrow["currenthp"] <= 0) {
             $newgold = ceil($userrow["gold"]/2);
             $newhp = ceil($userrow["maxhp"]/4);
             $newdscales = ceil($userrow["dscales"]/3);
             $updatequery = doquery("UPDATE {{table}} SET dscales='$newdscales',currenthp='$newhp',location='Dead',currentaction='In Town',currentmonster='0',currentmonsterhp='0',currentmonstersleep='0',currentmonsterimmune='0',currentfight='0',latitude='0',longitude='0',gold='$newgold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
             $playerisdead = 1;
         }
     }


 // Do a monsters turn if person lost the chance to swing first. Serves him right!
 } elseif ( $chancetoswingfirst == 0 ) {
     $pagearray["yourturn"] = "The monster unexpectedly attacks before you are ready!<br /><br />";
     $pagearray["monsterhp"] = "Monster's HP: " . $userrow["currentmonsterhp"] . "<br /><br />";
     $pagearray["monsterturn"] = "";
     if ($userrow["currentmonstersleep"] != 0) { // Check to wake up.
         $chancetowake = rand(1,15);
         if ($chancetowake > $userrow["currentmonstersleep"]) {
             $userrow["currentmonstersleep"] = 0;
             $pagearray["monsterturn"] .= "The monster has woken up!<br />";
         } else {
             $pagearray["monsterturn"] .= "The monster is still asleep..<br />";
         }
     }
     if ($userrow["currentmonstersleep"] == 0) { // Only do this if the monster is awake.
         $tohit = ceil(rand($monsterrow["maxdam"]*.5,$monsterrow["maxdam"]));
         $toblock = ceil(rand($userrow["defensepower"]*.75,$userrow["defensepower"])/4);
         $tododge = rand(1,150);
         if ($tododge <= sqrt($userrow["dexterity"])) {
             $tohit = 0; $pagearray["monsterturn"] .= "The monster scratches your ".$userrow["armorname"].". No damage has been caused..<br />";
             $persondamage = 0;
         } else {
             $persondamage = $tohit - $toblock;
             if ($persondamage < 1) { $persondamage = 1; }
             if ($userrow["currentuberdefense"] != 0) {
                 $persondamage -= ceil($persondamage * ($userrow["currentuberdefense"]/100));
             }
             if ($persondamage < 1) { $persondamage = 1; }
         }
         $pagearray["monsterturn"] .= "The monster attacks you for $persondamage damage.<br /><br />";
         $userrow["currenthp"] -= $persondamage;
         if ($userrow["currenthp"] <= 0) {
             $newgold = ceil($userrow["gold"]/2);
             $newhp = ceil($userrow["maxhp"]/4);
             $newdscales = ceil($userrow["dscales"]/3);
             $updatequery = doquery("UPDATE {{table}} SET dscales='$newdscales',currenthp='$newhp',location='Dead',currentaction='In Town',currentmonster='0',currentmonsterhp='0',currentmonstersleep='0',currentmonsterimmune='0',currentfight='0',latitude='0',longitude='0',gold='$newgold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
             $playerisdead = 1;
         }
     }

 } else {
     $pagearray["yourturn"] = "";
     $pagearray["monsterhp"] = "Monster's HP: " . $userrow["currentmonsterhp"] . "<br /><br />";
     $pagearray["monsterturn"] = "";
 }

 $newmonster = $userrow["currentmonster"];

 $newmonsterhp = $userrow["currentmonsterhp"];
 $newmonstersleep = $userrow["currentmonstersleep"];
 $newmonsterimmune = $userrow["currentmonsterimmune"];
 $newuberdamage = $userrow["currentuberdamage"];
 $newuberdefense = $userrow["currentuberdefense"];
 $newfight = $userrow["currentfight"] + 1;
 $newhp = $userrow["currenthp"];
 $newmp = $userrow["currentmp"];

if ($playerisdead != 1) {
$pagearray["command"] = <<<END
What would you like to do next? Be quick as the monster looks extremely fierce...<br /><br />
<form action="index.php?do=fight" method="post">
<input type="submit" name="fight" value="Continue to Fight" /><br /><br />
<select name="userspell"><option value="0">Choose Spell</option>$magiclist</select> <input type="submit" name="spell" value="Cast Spell" /><br /><br />
<select name='useritem'><option value='0'>Choose Item</option>$itemlist</select>
<input type='submit' name='item' value='Use Item'><br><br>
<input type="submit" name="run" value="Run Away.." /><br /><br />
</form>
END;
$monstername = $monsterrow["name"];
 $updatequery = doquery("UPDATE {{table}} SET location='Fighting a $monstername',currentaction='Fighting',currenthp='$newhp',currentmp='$newmp',currentfight='$newfight',currentmonster='$newmonster',currentmonsterhp='$newmonsterhp',currentmonstersleep='$newmonstersleep',currentmonsterimmune='$newmonsterimmune',currentuberdamage='$newuberdamage',currentuberdefense='$newuberdefense' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
} else {
 $pagearray["command"] = "<b><font color=red><p>You have died of a terrible death.</font></b><br /><br />As a consequence, you've lost <u>half of your gold</u> that you were holding and <b>some dragon scales</b>. However, you have been given back a portion of your hit points to continue your journey. You should visit the nearest Inn to heal at the Local Town Square.<br /><br /><center><img src=\"images/died.gif\" border=\"0\" alt=\"You have Died\" /></a></center><p>You may now continue back to <a href=\"index.php\">town</a>, and we hope you fair better next time.";

}

 // Finalize page and display it.
 $template = gettemplate("fight");
 $page = parsetemplate($template,$pagearray);

 display($page, "Fighting");

}

function victory() {

 global $userrow, $controlrow;



 if ($userrow["currentmonsterhp"] != 0) { header("Location: index.php?do=fight"); die(); }
 if ($userrow["currentfight"] == 0) { header("Location: index.php"); die(); }

 $monsterquery = doquery("SELECT * FROM {{table}} WHERE id='".$userrow["currentmonster"]."' LIMIT 1", "monsters");
 $monsterrow = mysql_fetch_array($monsterquery);
if ($_COOKIE["tempquest"]=== '4') {  //Check to see if they have V Slime cookie
setcookie ("tempquest", ""); // Set cookie to empty

		$page = "<table width='100%'><tr><td class='title'>You have defeated the Venomous Slime!</td></tr></table>";
		$page .= "<p>Congratulations. You have defeated the Venomous Slime!<p>You find 5 Dragon Scales lying on the ground, and gained 400 experience points, along with 250 Gold!<br />";
		if ($userrow["currentmonster"] == "247") {   //Venomous Slime for quest 2
			$page .= "<p> You carefully pick up Lucas's young Son.";
            	$page .= " You may now continue <a href='index.php'>exploring</a> or return Lucas's Son to <a href='quests.php?do=assistant'>Lucas</a>.";

            	$updatequery = doquery("UPDATE {{table}} SET experience=experience+400,gold=gold+250,dscales=dscales+5,currentaction='Exploring', currentfight='0',currentmonster='0',currentmonsterhp='0',currentmonstersleep='0',currentmonsterimmune='0',currentuberdamage='0',currentuberdefense='0',location='Venomous Slime',tempquest='5' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

 			display($page, "Defeated a Venomous Slime");
		}
}
		elseif ($_COOKIE["tempquest"]=== 'slime') {  //Check to see if they have V Slime cookie 2
setcookie ("tempquest", ""); // Set cookie to empty

		$page = "<table width='100%'><tr><td class='title'>You have defeated the Venomous Slime!</td></tr></table>";
		$page .= "<p>Congratulations. You have defeated the Venomous Slime, again!<p>You find 5 Dragon Scales lying on the ground, and gained 710 experience points.<br />";
		if ($userrow["currentmonster"] == "247") {   //Venomous Slime for quest 4
			$page .= "<p> You feel even weaker than you did before...";
            	$page .= " You must try and find an antidote before you become too weak to do anything else. Maybe Lucas can help you.<p>You may continue <a href='index.php'>exploring</a>.";

            	$updatequery = doquery("UPDATE {{table}} SET experience=experience+710,dscales=dscales+5,currentaction='Exploring', currentfight='0',currentmonster='0',currentmonsterhp='0',currentmonstersleep='0',currentmonsterimmune='0',currentuberdamage='0',currentuberdefense='0',location='Venomous Slime',tempquest='slimedead' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

 			display($page, "Defeated a Venomous Slime");

		}

		elseif ($_COOKIE["tempquest"]=== 'knight') {  //Check to see if they have knight cookie
setcookie ("tempquest", ""); // Set cookie to empty

		$page = "<table width='100%'><tr><td class='title'>You have defeated the Castle Knight!</td></tr></table>";
		$page .= "<p>Congratulations. You have defeated the Castle Knight!<p>You gained 310 experience points and 15 Gold.<br /><br />";
		if ($userrow["currentmonster"] == "249") {   //Castle Knight
            	$page .= "You may return to the Castles <a href='castle.php?do=main'>main</a> floor.";

            	$updatequery = doquery("UPDATE {{table}} SET experience=experience+310,gold=gold+15,currentaction='Exploring', currentfight='0',currentmonster='0',currentmonsterhp='0',currentmonstersleep='0',currentmonsterimmune='0',currentuberdamage='0',currentuberdefense='0',location='Castle Knight',tempquest='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

 			display($page, "Defeated a Venomous Slime");

		}	}

} else {

	    $monsterquery = doquery("SELECT * FROM {{table}} WHERE id='".$userrow["currentmonster"]."' LIMIT 1", "monsters");
    $monsterrow = mysql_fetch_array($monsterquery);

 $exp = rand((($monsterrow["maxexp"]/6)*5),$monsterrow["maxexp"]);
 if ($exp < 1) { $exp = 1; }
 if ($userrow["expbonus"] != 0) { $exp += ceil(($userrow["expbonus"]/100)*$exp); }
 $newexppercent = $userrow["skill1level"];
      if($userrow["skill1level"]<=10) {
      $exp2 = $exp + $newexppercent/4;
      if($exp2 < 1) { $exp2 = 1; }
      $expbonus = $exp2 - $exp;
      }
      else {

      $exp2 = $exp * $newexppercent/5;
      $expbonus = ceil($exp2 - $exp);
      }
  $gold = rand((($monsterrow["maxgold"]/6)*5),$monsterrow["maxgold"]);
  if ($gold < 1) { $gold = 1; }
  if ($userrow["goldbonus"] != 0) { $gold += ceil(($userrow["goldbonus"]/100)*$exp); }
$newgoldpercent = $userrow["skill4level"];
       if($userrow["skill4level"]<=10) {
       $gold2 = $gold + $newgoldpercent/4;
       $goldbonus = $gold2 - $gold;
       }
       else {

       $gold2 = $gold * $newgoldpercent/8;
       $goldbonus = ceil($gold2 - $gold);
       }

 if ($userrow["experience"] + $exp < 1677721500) { $newexp = $userrow["experience"] + $exp2; $warnexp = ""; } else { $newexp = $userrow["experience"]; $exp = 0; $warnexp = "You have maxed out your experience points."; }
 if ($userrow["gold"] + $gold < 1677721500) { $newgold = $userrow["gold"] + $gold2; $warngold = ""; } else { $newgold = $userrow["gold"]; $gold = 0; $warngold = "You have maxed out your gold."; }

 $levelquery = doquery("SELECT * FROM {{table}} WHERE id='".($userrow["level"]+1)."' LIMIT 1", "levels");
 if (mysql_num_rows($levelquery) == 1) { $levelrow = mysql_fetch_array($levelquery); }

 if ($userrow["level"] < 130) {
     if ($newexp >= $levelrow[$userrow["charclass"]."_exp"]) {
         $newhp = $userrow["maxhp"] + $levelrow[$userrow["charclass"]."_hp"];
         $newmp = $userrow["maxmp"] + $levelrow[$userrow["charclass"]."_mp"];
         $newtp = $userrow["maxtp"] + $levelrow[$userrow["charclass"]."_tp"];
         $newap = $userrow["maxap"] + $levelrow[$userrow["charclass"]."_ap"];
         $newattributes = $userrow["attributes"] + $levelrow[$userrow["charclass"]."_attributes"];
         $newstrength = $userrow["strength"] + $levelrow[$userrow["charclass"]."_strength"];
         $newdexterity = $userrow["dexterity"] + $levelrow[$userrow["charclass"]."_dexterity"];
         $newattack = $userrow["attackpower"] + $levelrow[$userrow["charclass"]."_strength"];
         $newdefense = $userrow["defensepower"] + $levelrow[$userrow["charclass"]."_dexterity"];
         $newlevel = $levelrow["id"];

         if ($levelrow[$userrow["charclass"]."_spells"] != 0) {
             $userspells = $userrow["spells"] . ",".$levelrow[$userrow["charclass"]."_spells"];
             $newspell = "spells='$userspells',";
             $spelltext = "You have learnt a new spell.<br />";
         } else { $spelltext = ""; $newspell=""; }

         $page = "<table width='100%' border='1'><tr><td class='title'>Victory!</td></tr></table><p>Congratulations. You have defeated the ".$monsterrow["name"].".<br />You gain $exp experience. $warnexp <br /><font color=darkorange>As a result of your Wisdom skill you gained an extra <b>$expbonus</b> experience points!</font><br />
<font color=green>As a result of your Fortune skill you gained an extra <b>$goldbonus</b> Gold!</font><br />You gain $gold gold. $warngold <br /><br /><b>You have gained a level!</b><br /><br />You gain ".$levelrow[$userrow["charclass"]."_attributes"]." attribute points.<br />You gain ".$levelrow[$userrow["charclass"]."_hp"]." hit points.<br />You gain ".$levelrow[$userrow["charclass"]."_mp"]." mana points.<br />You gain ".$levelrow[$userrow["charclass"]."_tp"]." travel points.<br />You gain ".$levelrow[$userrow["charclass"]."_ap"]." ability point.<br />You gain ".$levelrow[$userrow["charclass"]."_strength"]." strength.<br />You gain ".$levelrow[$userrow["charclass"]."_dexterity"]." dexterity.<br />$spelltext<br /><p><center><img src=\"images/levelup.gif\" border=\"0\" alt=\"You have Leveled\" /></a></center><p>You can now continue <a href=\"index.php\">exploring</a>.";
         $title = "Courage and Wit have served thee well!";
         $dropcode = "";
     } else {
         $newhp = $userrow["maxhp"];
         $newmp = $userrow["maxmp"];
         $newtp = $userrow["maxtp"];
         $newap = $userrow["maxap"];
         $newattributes = $userrow["attributes"];
         $newstrength = $userrow["strength"];
         $newdexterity = $userrow["dexterity"];
         $newattack = $userrow["attackpower"];
         $newdefense = $userrow["defensepower"];
         $newlevel = $userrow["level"];
         $newspell = "";
         $page = "<table width='100%' border='1'><tr><td class='title'>Victory!</td></tr></table><p>Congratulations. You have defeated the ".$monsterrow["name"].".<br />You gain $exp experience. $warnexp <br /><font color=darkorange>As a result of your Wisdom skill you gained an extra <b>$expbonus</b> experience points!</font><br />You gain $gold gold. $warngold <br /><font color=green>As a result of your Fortune skill you gained an extra <b>$goldbonus</b> Gold!</font><p><center><img src=\"images/victory.gif\" border=\"0\" alt=\"Victory\" /></a></center><br /><br />";

         if($monsterrow["bones"] > 0 && rand(1,3) == 2) {
         $newbones = $userrow["bones"] + $monsterrow["bones"];
$query = doquery("UPDATE {{table}} SET bones='$newbones' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        $page .= "You also pick yourself up <b>".$monsterrow["bones"]."</b> bones from off the ground.</font><br /><br />\n";


}

if ($userrow["guildname"] != "-") {
        $guildexp = $monsterrow["level"];

        $query = doquery("UPDATE {{table}} SET exp_pool=exp_pool+'$guildexp' WHERE name='".$userrow["guildname"]."' LIMIT 1", "guilds");
        $page .= "<font color=blue>You receive a bonus of $guildexp experience to your Guilds Experience Pool.</font><br /><br />\n";

}

// rand(1,3) = 3 means it picks a number between 1 and 3 and if it = 3 it spawns - Random Souls
if(rand(1,35) == 29) {
$randomid = rand(1,15000);
$randhp = rand(40,140);
$randdef = rand(65,135);
$randattack = rand(61,140);
$randexp = rand(50,210) + ($userrow["magicfind"]);
$randgold = rand(150,300) + ($userrow["magicfind"]*2);
$randdscales = rand(0,1) + ($userrow["magicfind"]/15);
$query = doquery("INSERT INTO dk_souls SET id='$randomid',hp='$randhp',type='1',exp='$randexp',gold='$randgold',dscales='$randdscales',def='$randdef',attack='$randattack'", "souls") or die(mysql_error());
$a = doquery("UPDATE {{table}} SET templist='cave' WHERE id='".$userrow["id"]."' ", "users");
$page .="<font color=red>You have stumbled across a <b>Desert Rats Soul</b> with the ID Number of: <b>$randomid</b> <p>The Desert Rats Soul moves away quickly, heading towards a nearby cave. Maybe you should follow it...</font><p><center><a href=\"index.php?do=cave\"><img src=\"images/entercave.jpg\" border=\"0\" alt=\"You may enter the Cave\" /></a></center><p> Or.. <p>";

}

if(rand(1,49) == 19) {
$randomid = rand(1,15000);
$randhp = rand(100,300);
$randdef = rand(150,350);
$randattack = rand(150,350);
$randexp = rand(100,450) + ($userrow["magicfind"]*2);
$randgold = rand(350,600) + ($userrow["magicfind"]*3);
$randdscales = rand(0,1) + ($userrow["magicfind"]/12);
$query = doquery("INSERT INTO dk_souls SET id='$randomid',hp='$randhp',type='2',exp='$randexp',gold='$randgold',dscales='$randdscales',def='$randdef',attack='$randattack'", "souls") or die(mysql_error());
$a = doquery("UPDATE {{table}} SET templist='cave' WHERE id='".$userrow["id"]."' ", "users");
$page .="<font color=red>You have stumbled across a <b>Desert Snakes Soul</b> with the ID Number of: <b>$randomid</b> <p>The Desert Snakes Soul moves away quickly, heading towards a nearby cave. Maybe you should follow it...</font><p><center><a href=\"index.php?do=cave\"><img src=\"images/entercave.jpg\" border=\"0\" alt=\"You may enter the Cave\" /></a></center><p> Or.. <p>";

}
if(rand(1,160) == 71) {
$randomid = rand(15001,30000);
$randhp = rand(350,600);
$randdef = rand(350,550);
$randattack = rand(250,450);
$randexp = rand(300,650) + ($userrow["magicfind"]*2);
$randgold = rand(450,650) + ($userrow["magicfind"]*4);
$randdscales = rand(1,3) + ($userrow["magicfind"]/10);
$query = doquery("INSERT INTO dk_souls SET id='$randomid',hp='$randhp',type='3',exp='$randexp',gold='$randgold',dscales='$randdscales',def='$randdef',attack='$randattack'", "souls") or die(mysql_error());
$a = doquery("UPDATE {{table}} SET templist='cave' WHERE id='".$userrow["id"]."' ", "users");
$page .="<font color=red>You have stumbled across a <b>Soul</b> with the ID Number of: <b>$randomid</b> <p>The Soul moves away quickly, heading towards a nearby cave. Maybe you should follow it...</font><p><center><a href=\"index.php?do=cave\"><img src=\"images/entercave.jpg\" border=\"0\" alt=\"You may enter the Cave\" /></a></center><p> Or.. <p>";

}
if(rand(1,350) == 150) {
$randomid = rand(30001,45000);
$randhp = rand(750,1100);
$randdef = rand(650,950);
$randattack = rand(500,800);
$randexp = rand(500,800) + ($userrow["magicfind"]*3);
$randgold = rand(700,1100) + ($userrow["magicfind"]*6);
$randdscales = rand(1,5) + ($userrow["magicfind"]/9);
$query = doquery("INSERT INTO dk_souls SET id='$randomid',hp='$randhp',type='4',exp='$randexp',gold='$randgold',dscales='$randdscales',def='$randdef',attack='$randattack'", "souls") or die(mysql_error());
$a = doquery("UPDATE {{table}} SET templist='cave' WHERE id='".$userrow["id"]."' ", "users");
$page .="<font color=red>You have stumbled across a <b>Silver Slimes Soul</b> with the ID Number of: <b>$randomid</b> <p>The Silver Slimes Soul moves away quickly, heading towards a nearby cave. Maybe you should follow it...</font><p><center><a href=\"index.php?do=cave\"><img src=\"images/entercave.jpg\" border=\"0\" alt=\"You may enter the Cave\" /></a></center><p> Or.. <p>";

}
if(rand(1,530) == 210) {
$randomid = rand(45001,60000);
$randhp = rand(1150,1950);
$randdef = rand(850,1250);
$randattack = rand(600,1150);
$randexp = rand(600,1400) + ($userrow["magicfind"]*4);
$randgold = rand(900,2450) + ($userrow["magicfind"]*9);
$randdscales = rand(2,7) + ($userrow["magicfind"]/8);
$query = doquery("INSERT INTO dk_souls SET id='$randomid',hp='$randhp',type='5',exp='$randexp',gold='$randgold',dscales='$randdscales',def='$randdef',attack='$randattack'", "souls") or die(mysql_error());
$a = doquery("UPDATE {{table}} SET templist='cave' WHERE id='".$userrow["id"]."' ", "users");
$page .="<font color=red>You have stumbled across a <b>Silver Scorpions Soul</b> with the ID Number of: <b>$randomid</b> <p>The Silver Scorpions Soul moves away quickly, heading towards a nearby cave. Maybe you should follow it...</font><p><center><a href=\"index.php?do=cave\"><img src=\"images/entercave.jpg\" border=\"0\" alt=\"You may enter the Cave\" /></a></center><p> Or.. <p>";

}
if(rand(1,850) == 402) {
$randomid = rand(60001,75000);
$randhp = rand(2000,3650);
$randdef = rand(1950,2500);
$randattack = rand(950,2350);
$randexp = rand(2500,3500) + ($userrow["magicfind"]*6);
$randgold = rand(2200,3600) + ($userrow["magicfind"]*12);
$randdscales = rand(3,12) + ($userrow["magicfind"]/7);
$query = doquery("INSERT INTO dk_souls SET id='$randomid',hp='$randhp',type='6',exp='$randexp',gold='$randgold',dscales='$randdscales',def='$randdef',attack='$randattack'", "souls") or die(mysql_error());
$a = doquery("UPDATE {{table}} SET templist='cave' WHERE id='".$userrow["id"]."' ", "users");
$page .="<font color=red>You have stumbled across a <b>King Black Dragons Soul</b> with the ID Number of: <b>$randomid</b> <p>The King Black Dragons Soul moves away quickly, heading towards a nearby cave. Maybe you should follow it...<font><p><center><a href=\"index.php?do=cave\"><img src=\"images/entercave.jpg\" border=\"0\" alt=\"You may enter the Cave\" /></a></center><p> Or.. <p>";


}
         if (rand(1,70) == 1) { //Normal drops
             $dropquery = doquery("SELECT * FROM {{table}} WHERE mlevel <= '".$monsterrow["level"]."' ORDER BY RAND() LIMIT 1", "drops");
             $droprow = mysql_fetch_array($dropquery);
             $dropcode = "dropcode='".$droprow["id"]."',";
             $page .= "<font color=red><b>This monster has dropped an item!<p></b></font> <a href=\"index.php?do=drop\">Click here</a> to reveal and equip the item, or you may also move on and continue <a href=\"index.php\">exploring</a>.";
         }  elseif (rand(71,325) == 72) { //Equip drops - weapons, armor etc
             $drop2query = doquery("SELECT * FROM {{table}} WHERE mlevel <= '".$monsterrow["level"]."' ORDER BY RAND() LIMIT 1", "items");
             $drop2row = mysql_fetch_array($drop2query);
             $dropcode2 = "dropcode2='".$drop2row["id"]."',";
             $page .= "<font color=red><b>This monster has dropped an item!<p></b></font> <a href=\"index.php?do=dropitem\">Click here</a> to reveal and equip the item, or you may also move on and continue <a href=\"index.php\">exploring</a>.";

         } else {
             $dropcode = "";
             $page .= "You can now continue <a href=\"index.php\">exploring</a>.";
         }

         $title = "Victory!";
     }
 }

$monstername = $monsterrow["name"];
 $updatequery = doquery("UPDATE {{table}} SET location='Defeated a $monstername',currentaction='Exploring',level='$newlevel',attributes='$newattributes',maxhp='$newhp',maxmp='$newmp',maxtp='$newtp',maxap='$newap',strength='$newstrength',dexterity='$newdexterity',attackpower='$newattack',defensepower='$newdefense', $newspell currentfight='0',currentmonster='0',currentmonsterhp='0',currentmonstersleep='0',currentmonsterimmune='0',currentuberdamage='0',currentuberdefense='0',$dropcode experience='$newexp',$dropcode2 gold='$newgold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

 display($page, $title);

}
}

//CHANGED



//******************************************************



//

//Dropitem
/**
 * @desc What happens when a monster drops an item
 * @return void
 */
function dropitem() {
	global $userrow, $numqueries, $backpackitemslots;

	if ($userrow["dropcode2"] == 0)	{
		header("Location: index.php");
		exit;
	}

	$itemsquery = doquery("SELECT * FROM {{table}} WHERE id='".$userrow["dropcode2"]."' LIMIT 1", "items");
	$itemsrow = mysql_fetch_array($itemsquery);
	$updatequery = doquery("UPDATE {{table}} SET location='Monster Drop' WHERE id='".$userrow["id"]."' LIMIT 1", "users");


	if ($itemsrow[type] == 1)	{ //weapon
		$what = "Weapon";
		$whatid = "weaponid";
		$whatname = "weaponname";
	}
	elseif ($itemsrow[type] == 2)	{ //armor
		$what = "Armor";
		$whatid = "armorid";
		$whatname = "weaponname";
	}
	elseif ($itemsrow[type] == 3)	{ //shield
		$what = "Shield";
		$whatid = "shieldid";
		$whatname = "shieldname";
	}
	elseif ($itemsrow[type] == 4)	{ //helm
		$what = "Helmet";
		$whatid = "helmid";
		$whatname = "helmname";
	}
	elseif ($itemsrow[type] == 5)	{ //legs
		$what = "Legs";
		$whatid = "legsid";
		$whatname = "legsname";
	}
	elseif ($itemsrow[type] == 6)	{ //gauntlets
		$what = "Gauntlets";
		$whatid = "gauntletsid";
		$whatname = "gauntletsname";
	}

	$bpquery = doquery("SELECT * FROM {{table}} WHERE playerid = '$userrow[id]' AND itemtype = '1' AND location = '1'", "itemstorage");

	$slotsr = $backpackitemslots - mysql_num_rows($bpquery);

	$page = "<table width='100%' border='1'><tr><td class='title'>Monster Drop - $what</td></tr></table><p>";
	$page .= "The monster dropped a ".$itemsrow["name"]."! Would you like to place this item into your backpack? Dropping this item will lose it, forever. (You currently have space for <b>$slotsr</b> item(s) in your backpack.)<br /><br />";
	if ($slotsr > 0)	{
		$page .= "<form action=\"index.php?do=take&amp;type=e\" method=\"post\"><input type=\"submit\" name=\"submit\" value=\"Yes\" /> <input type=\"submit\" name=\"cancel\" value=\"Drop it\" /></form>";
	}
	else {
		$page .= "<font color='red'>You do not have room in your backpack to carry this item.  You may go to your <a href='index.php?do=backpackitemclean'>backpack</a> to clear room.</font>";
	}

	$title = "Monster Drop";
	display($page, $title);

}

//Take
/**
 * @desc Takes a dropped item into your backpack
 * @return void
 */
function take() { // Update user profile with new item & stats.
	global $userrow, $backpackitemslots, $backpackdropslots;

	if (isset($_POST["cancel"]))	{
//		$updatequery = doquery("UPDATE {{table}} SET $dc = '0' WHERE id = '$userrow[id]'", "users");
		header("Location: index.php");
		exit;
	}

	if ($_GET['type'] == 'e')	{
		if ($userrow["dropcode2"] == 0)	{
//			header("Location: index.php");
			exit;
		}
		else {
			$bpquery = doquery("SELECT * FROM {{table}} WHERE playerid = '$userrow[id]' AND itemtype = '1' AND location = '1'", "itemstorage");
			$slotsr = $backpackitemslots - mysql_num_rows($bpquery);
			if ($slotsr > 0)	{
				$droptype = 1;
				$dc = "dropcode2";
				$droptable = "items";
			}
			else {
				header("Location: index.php");
				exit;
			}
		}
	}
	elseif ($_GET['type'] == 'd')	{
		if ($userrow["dropcode"] == 0)	{
			header("Location: index.php");
			exit;
		}
		else {
			$bpquery = doquery("SELECT * FROM {{table}} WHERE playerid = '$userrow[id]' AND itemtype = '2' AND location = '1'", "itemstorage");
			$slotsr = $backpackdropslots - mysql_num_rows($bpquery);
			if ($slotsr > 0)	{
				$droptype = 2;
				$dc = "dropcode";
				$droptable = "drops";
			}
			else {
				header("Location: index.php");
				exit;
			}
		}
	}

	$additemquery = doquery("INSERT INTO {{table}} VALUES ('', '$userrow[id]', '$droptype', '$userrow[$dc]', '1')", "itemstorage");

	$updatequery = doquery("UPDATE {{table}} SET $dc = '0' WHERE id = '$userrow[id]'", "users");

	$itemsquery = doquery("SELECT * FROM {{table}} WHERE id='$userrow[$dc]' LIMIT 1", "$droptable");
	$itemsrow = mysql_fetch_array($itemsquery);

	$page = "You have placed the $itemsrow[name] into your <a href='index.php?do=backpack'>backpack</a>.  You may go to your <a href='index.php?do=backpack'>backpack</a> to equip the item, or you may continue <a href='index.php'>exploring</a>.";

	display($page, "Item Placed in Backpack");
}


function dropitem2() {
	global $userrow, $numqueries, $backpackdropslots;

	if ($userrow["dropcode"] == 0)	{
		header("Location: index.php");
		exit;
	}

	$itemsquery = doquery("SELECT * FROM {{table}} WHERE id='".$userrow["dropcode"]."' LIMIT 1", "drops");
	$droprow = mysql_fetch_array($itemsquery);
	$updatequery = doquery("UPDATE {{table}} SET location='Monster Drop' WHERE id='".$userrow["id"]."' LIMIT 1", "users");


	$bpquery = doquery("SELECT * FROM {{table}} WHERE playerid = '$userrow[id]' AND itemtype = '2' AND location = '1'", "itemstorage");

	$slotsr = $backpackdropslots - mysql_num_rows($bpquery);

//	die("$slotsr :: $backpackdropslots :: " . mysql_num_rows($bpquery) . " :: SELECT * FROM {{table}} WHERE playerid = '$userrow[id]' AND itemtype = '2' AND location = '1'" );

	$attributearray = array("maxhp"=>"Max HP",
                         "maxmp"=>"Max MP",
                         "maxtp"=>"Max TP",
                         "maxap"=>"Max AP",
                         "defensepower"=>"Defense",
                         "attackpower"=>"Attack",
                         "strength"=>"Strength",
                         "dexterity"=>"Dexterity",
                         "expbonus"=>"Experience Bonus",
                         "goldbonus"=>"Gold Bonus");

	$page = "<table width='100%' border='1'><tr><td class='title'>Monster Drop</td></tr></table><p>";
	$page .= "The monster dropped a <b>".$droprow["name"]."</b>!<br /> This item has the following attribute(s):<br />";

	$attribute1 = explode(",",$droprow["attribute1"]);
	$page .= $attributearray[$attribute1[0]];

	if ($attribute1[1] > 0)	{
		$page .= " +" . $attribute1[1] . "<br />";
	}
	else	{
		$page .= $attribute1[1] . "<br />";
	}

	if ($droprow["attribute2"] != "X")	{
		$attribute2 = explode(",",$droprow["attribute2"]);
		$page .= $attributearray[$attribute2[0]];
	if ($attribute2[1] > 0)	{
		$page .= " +" . $attribute2[1] . "<br />";
	}
	else	{
		$page .= $attribute2[1] . "<br />";
	}
 }

	$page .= "<br />Would you like to place this drop into your backpack?  (You currently have space for <b>$slotsr</b> drop(s) in your backpack.)<br /><br />";
	if ($slotsr > 0)	{
		$page .= "<form action=\"index.php?do=take&amp;type=d\" method=\"post\"><input type=\"submit\" name=\"submit\" value=\"Yes\" /> <input type=\"submit\" name=\"cancel\" value=\"No\" /></form>";
	}
	else {
		$page .= "<font color='red'>You do not have room in your backpack to carry this item.  You may go to your <a href='index.php?do=backpackdropclean'>backpack</a> to clear room.</font>";
	}

	$title = "Monster Drop";
	display($page, $title);
}

function dead() {

 $page = "<b><font color=red>You have died of a painfull and gruesome death!</font></b><br /><br />As a consequence, you've lost <u>half of your gold</u> that you were holding and <b>some dragon scales</b>. However, you have been given back a small portion of your hit points to continue your journey. Remember to return to the Town Square and heal at the Inn.<br /><br /><center><img src=\"images/died.gif\" border=\"0\" alt=\"You have Died\" /></a></center><p>You may now continue back to <a href=\"index.php\">town</a>, and we hope you fair better next time.";

}

function capture() {
    global $userrow, $controlrow;

    $monsterquery = doquery("SELECT * FROM {{table}} WHERE id='".$userrow["currentmonster"]."' LIMIT 1", "monsters");
    $monsterrow = mysql_fetch_array($monsterquery);


  if ($userrow["currentmonsterhp"] != 0) { header("Location: index.php?do=fight"); die(); }
    if ($userrow["currentfight"] == 0) { header("Location: index.php"); die(); }

 $monid = $monsterrow["id"];
 $monspecies = $monsterrow["id"];
 $monname = $monsterrow["name"];
 $montype = $monsterrow["name"];
 $montrainer = $userrow["charname"];
 $monmaxhp = $monsterrow["level"] + (rand(1,$monsterrow["level"]));
 if ($monmaxhp <= 10) {$monmaxhp = 10;}
 $moncurrenthp = $monmaxhp/2;
 $monmaxmp = $monsterrow["level"] + (rand(1,$monsterrow["level"]));
 if ($monmaxmp <= 10) {$monmaxmp = 10;}
 $moncurrenthp = $monsterrow["level"] + (rand(1,$monsterrow["level"]));
 $monmaxdam = $monsterrow["level"] + (rand(1,$monsterrow["level"]));
 $mongold = intval($monsterrow["gold"]/(rand(1,5)));
 $mondexterity = $monsterrow["dexterity"];
 $monarmor = $monsterrow["level"] + (rand(1,$monsterrow["level"]));
 $monmagicarmor = $monsterrow["magicarmor"];
 $monimmune = $monsterrow["immune"];
 $monspecial1type = $monsterrow["specialtype"];
 $monspecial1name = $monsterrow["special1name"];
 $monspecial1strength = $monsterrow["special1strength"];
 $query = doquery("INSERT INTO {{table}} SET id='',name='$monname',type='$montype',species='$monspecies', trainer='$montrainer',maxhp='$monmaxhp',maxmp='$monmaxmp',currenthp='$moncurrenthp', currentmp='0',maxdam='$monmaxdam',dexterity='$mondexterity',armor='$monarmor', magicarmor='$monmagicarmor',level='1',experience='0',gold='$mongold',immune='$monimmune', wins='0',losses='0' ", "arena") or die(mysql_error());
 $updatequery = doquery("UPDATE {{table}} SET location='Pet Captured',currentaction='Exploring',currentfight='0',currentmonster='0',currentmonsterhp='0',currentmonstersleep='0',currentmonsterimmune='0',currentuberdamage='0',currentuberdefense='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");


 $page = "<table width='100%'><tr><td class='title'>You have Captured a Pet!</td></tr></table>";
 $page .= "<p><font color=green> You have captured a ".$monsterrow["name"]."! </font><p>";
 $page .= "Captured monsters become your 'Pets' and can be used to duel other players Pets";
 $page .= " in the Pet Arena within a Stronghold. To use the Arena, you must be a member of a Guild, and ";
 $page .= "locate one of the Strongholds controlled by your Guild.<br>";
 $page .= "You may only capture 5 Pets, and any additional monsters will be immediately ";
 $page .= "released back into the wild. <p><p>If you wish to capture more Pets and already have ";
 $page .= "five, you must go to the Pet Arena and choose the option to Release Pets ";
 $page .= "before you will be able to capture additional Pets.<p>";
 $page .= "<p><p>Now that you have captured a Pet, it is at risk of being attacked by other peoples Pets. You should keep training, feeding and dueling your Pet from within the Pet Arena.<p>";
     $page .= "You can now continue <a href=\"index.php\">exploring</a>.";
     display($page,"You have Captured a Pet!");
}

function corpse() { // Search corpse

    global $userrow, $numqueries;
if ($userrow["templist"] != "corpse") {header("Location: index.php"); die(); }
$updatequery = doquery("UPDATE {{table}} SET location='Searching a Corpse' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

if($userrow["tempquest"] == "nopara") {

        $page .= "<table width='100%' border='1'><tr><td class='title'>Searching a Corpse</td></tr></table><p>";
        $page .= "You slowly get down onto your knees and search this monsters corpse using the tip of your weapon...<p><font color=red>Something small and quick jumps out from the body and hits you for 9 damage!<p>The small creature after biting you, falls limp onto the floor. You quickly attempt to pick it up using Crystal Jar, but the parasite suddenly begins moving quick again - You fail!</font><br /><br />\n";
        $page .= "<br />You may return to <a href=\"index.php\">exploring</a>.<br />\n";
$updatequery = doquery("UPDATE {{table}} SET templist='0',tempquest='noparatwo',currenthp=currenthp-9, currentaction='Exploring' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

}
elseif($userrow["tempquest"] == "noparatwo") {

	        $inventitems2 = "0,".$userrow["inventitems"].",0";
        //Take away Q4 item
        $newinventitems2 = str_replace(",96,", ",", $inventitems2);
        $userrow["inventitems"] = $newinventitems2;
         //add Quest 4 Item
        $inventitemsquery = doquery("SELECT id FROM {{table}}","inventitems");
        $userinventitems = explode(",",$userrow["inventitems"]);

        array_push($userinventitems, 97);
        $new_userinventitems = implode(",",$userinventitems);
        $userid = $userrow["id"];
        doquery("UPDATE {{table}} SET inventitems='$new_userinventitems',templist='gotcorpse',currenthp=currenthp-7, tempquest='gotpara', currentaction='Exploring' WHERE id='$userid' LIMIT 1", "users");
        $page .= "<table width='100%' border='1'><tr><td class='title'>Searching a Corpse</td></tr></table><p>";
        $page .= "You slowly get down onto your knees and search this monsters corpse using the tip of your weapon...<p><font color=red>Something small and quick jumps out from the body and hits you for 7 damage!</font><p><font color=blue>The small creature after biting you, falls limp onto the floor. You quickly pick it up and place it into your Crystal Jar.</font><br /><br />\n";
        $page .= "<br />You may return to <a href=\"index.php\">exploring</a>.<br />\n";
}
elseif (rand(1,6) >= 4) {
    $treasure = intval(rand(1,$userrow["level"])*2) + ($userrow["magicfind"]*3) + 35;
    $newgold = $userrow["gold"] + $treasure;
    if ($newgold > 9999999) {$newgold = $newgold - 9999999;}
    $treasuretype = "Gold";
        $page = "<table width='100%' border='1'><tr><td class='title'>Searching a Corpse</td></tr></table><p>";
        $page .= "You slowly get down onto your knees and search this monsters corpse using the tip of your weapon...<p><font color=green>You find <b>$treasure</b> <b>$treasuretype</b> covered in blood!</font><br /><br />\n";
        $page .= "<br />You may return to <a href=\"index.php\">exploring</a>.<br />\n";
$updatequery = doquery("UPDATE {{table}} SET templist='gotcorpse',gold='$newgold', currentaction='Exploring' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

}

elseif($userrow["quest1"] == "Started" && (rand(1,4) )== 4) {
        $inventitemsquery = doquery("SELECT id FROM {{table}}","inventitems");
        $userinventitems = explode(",",$userrow["inventitems"]);
        //add Quest 1 Item
        array_push($userinventitems, 88);
        $new_userinventitems = implode(",",$userinventitems);
        $userid = $userrow["id"];
        doquery("UPDATE {{table}} SET inventitems='$new_userinventitems',templist='gotcorpse',quest1='Half Complete', currentaction='Exploring' WHERE id='$userid' LIMIT 1", "users");
        $page .= "<table width='100%' border='1'><tr><td class='title'>Searching a Corpse</td></tr></table><p>";
        $page .= "You slowly get down onto your knees and search this monsters corpse using the tip of your weapon...<p><font color=blue>You find a small object lying beside it. You pick it up to discover that its a Precious Ring.</font><br /><br />\n";
        $page .= "<br />You may return to <a href=\"index.php\">exploring</a>.<br />\n";
}
elseif($userrow["tempquest"] == "3" && (rand(1,2) )== 2) {
        $inventitemsquery = doquery("SELECT id FROM {{table}}","inventitems");
        $userinventitems = explode(",",$userrow["inventitems"]);
        //add Quest 2 Item
        array_push($userinventitems, 92);
        $new_userinventitems = implode(",",$userinventitems);
        $userid = $userrow["id"];
        doquery("UPDATE {{table}} SET inventitems='$new_userinventitems',templist='gotcorpse',tempquest='bucket',currenthp=currenthp-11, currentaction='Exploring' WHERE id='$userid' LIMIT 1", "users");
        $page .= "<table width='100%' border='1'><tr><td class='title'>Searching a Corpse</td></tr></table><p>";
        $page .= "You slowly get down onto your knees and search this monsters corpse using the tip of your weapon...<p><font color=blue>You stumble over an Empty Bucket and get your foot stuck. You then fall to the ground! You are hurt for 11 Damage!</font><br /><br />\n";
        $page .= "<br />You may return to <a href=\"index.php\">exploring</a>.<br />\n";
}
    elseif (rand(1,20) == 17) { //Corpse comes back to life, redirect to it.
      $randhp = rand(200,800);
         $updatequery = doquery("UPDATE {{table}} SET currentaction='Fighting',currentfight='2',currentmonster='248',currentmonsterhp='$randhp',currentmonsterimmune='2',location='Fighting a Corpse' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
                    header("Location: index.php?do=fight"); die();

}
elseif (rand(1,23) == 12) {
    $treasure = intval(rand(1,($userrow["level"]/4) + ($userrow["magicfind"]/5)));
    if ($treasure <= 1) {$treasure = 1;}
    $newscales = $userrow["dscales"] + $treasure;
    if ($newscales > 99999) {$newscales = $newscales - 99999;}
    $treasuretype = "Dragon Scale(s)";
        $page .= "<table width='100%' border='1'><tr><td class='title'>Searching a Corpse</td></tr></table><p>";
        $page .= "You slowly get down onto your knees and search this monsters corpse using the tip of your weapon...<p><font color=green>You find <b>$treasure</b> <b>$treasuretype</b> lying beside the monster!</font><br /><br />\n";
        $page .= "<br />You may return to <a href=\"index.php\">exploring</a>.<br />\n";
$updatequery = doquery("UPDATE {{table}} SET templist='gotcorpse',dscales='$newscales', currentaction='Exploring' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
}
elseif (rand(1,12) == 6) {
        $inventitemsquery = doquery("SELECT id FROM {{table}}","inventitems");
        $userinventitems = explode(",",$userrow["inventitems"]);
        //add healing Item
        array_push($userinventitems, 3);
        $new_userinventitems = implode(",",$userinventitems);
        $userid = $userrow["id"];
        doquery("UPDATE {{table}} SET inventitems='$new_userinventitems',templist='gotcorpse', currentaction='Exploring' WHERE id='$userid' LIMIT 1", "users");
        $page .= "<table width='100%' border='1'><tr><td class='title'>Searching a Corpse</td></tr></table><p>";
        $page .= "You slowly get down onto your knees and search this monsters corpse using the tip of your weapon...<p><font color=green>You find a small object lying beside it. You have found a Healing Bundle!</font><br /><br />\n";
        $page .= "<br />You may return to <a href=\"index.php\">exploring</a>.<br />\n";
}
elseif (rand(1,15) == 11) {
	                 $randdam = rand(1,16);
        $page .= "<table width='100%' border='1'><tr><td class='title'>Searching a Corpse</td></tr></table><p>";
        $page .= "You slowly get down onto your knees and search this monsters corpse using the tip of your weapon...<p><font color=red>Something small and quick jumps out from the body and hits you for $randdam damage!</font><br /><br />\n";
        $page .= "<br />You may return to <a href=\"index.php\">exploring</a>.<br />\n";
        doquery("UPDATE {{table}} SET currenthp=currenthp-$randdam,templist='gotcorpse', currentaction='Exploring' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

}

else {

        $page .= "<table width='100%' border='1'><tr><td class='title'>Searching a Corpse</td></tr></table><p>";
        $page .= "You slowly get down onto your knees and search this monsters corpse using the tip of your weapon...<p><font color=red>You find nothing but blood and bones! You gain <b>1</b> bone!</font><br /><br />\n";
        $page .= "<br />You may return to <a href=\"index.php\">exploring</a>.<br />\n";
$updatequery = doquery("UPDATE {{table}} SET templist='gotcorpse', bones=bones+1, currentaction='Exploring' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

    }
 display($page,"Searching a Corpse");
    display($page, $title);

}

function fatigue() {
    global $userrow, $controlrow;
        $fatlimit = $userrow["currentfat"];
        $fatlimit = $fatlimit + $userrow["run"];
        if($fatlimit < $userrow["maxfat"]){ header("Location: index.php"); die(); }
$updatequery = doquery("UPDATE {{table}} SET location='Restore Fatigue' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

if (isset($_POST["totown"])) {

	$pu = doquery("UPDATE {{table}} SET currentaction='In Town', latitude='0', longitude='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	header("Location: index.php"); die();
	}

 $page = "<table width='100%'><tr><td class='title'>You are too tired!</td></tr></table>";
 $page .= "<p><font color=red>You are too tired to continue exploring!</font><p>";
 $page .= "<p>Please Restore your Fatigue using your Desert Tent from your Quick Items menu by resting for a little while.<p>You may also enter the Town Portal below to return to the Kingdom of Valour, for no charge.<p>";

	$page .= "<center><form action='index.php?do=fatigue' method='POST'>";
	$page .= "<input type='submit' name='totown' value='Enter Portal'>";
	$page .= "</form></center>";


      display($page,"Restore Fatigue");
}

function attributes() { // Level up players attributes

    global $userrow;

$updatequery = doquery("UPDATE {{table}} SET location='Spending Attributes' WHERE id='".$userrow["id"]."' LIMIT 1", "users");


	    $page = "<table width='100%' border='1'><tr><td class='title'>Attributes</td></tr></table><p>";

	if (isset($_POST["submit"])) {
		$numtroops = abs(intval($_POST["troops"]));
		$trooptype = $_POST["trooptype"];
		if ($userrow["attributes"] < $numtroops) {
		$page .= "<p><b>You do not have enough Attributes to spend on that stat.</b><p>";

        $page .= "You have <b>".$userrow["attributes"]."</b> attributes remaining to spend. You gain more attributes every 10 levels (the amount depends on your class) and these are then used to increase your stats. Sometimes you may receive attributes for rewards.<br /><br />\n";
        $page .= "Please select the amount of attributes you wish to spend, and for which stat carefully. You <u>cannot</u> reverse this process.<br /><br />\n";


			$page .= "<form action='index.php?do=attributes' method='POST'>";

		$page .= "<input type='text' name='troops' value='0' size='5'> ";
		$page .= "<select name='trooptype'>";
	    $page .= "<option value='maxhp'>Increase HP (+3)</option>";
	    $page .= "<option value='maxmp'>Increase MP (+3)</option>";
	    $page .= "<option value='maxtp'>Increase TP (+2)</option>";
	    $page .= "<option value='maxap'>Increase AP (+1)</option>";
	    $page .= "<option value='strength'>Increase Strength (+2)</option>";
	    $page .= "<option value='dexterity'>Increase Dexterity (+2)</option>";
	    $page .= "<option value='attackpower'>Increase Attack (+1)</option>";
	    $page .= "<option value='defensepower'>Increase Defense (+1)</option></select><br>";
		$page .= "<input type='submit' name='submit' value='Increase Stat'>";
$page .= "<p><center><img src=\"images/levelup.gif\" border=\"0\" alt=\"Spend your Attributes\" /></a></center><p>You may return to what you was <a href=\"index.php\">doing</a>.<p>*Note: This feature isn't enabled yet =)<br />\n";

 		display($page,"Attributes");
 		}
 		if ($trooptype == 'maxhp') {
			$val = $numtroops*3 + $userrow["maxhp"];
 		}
 		elseif ($trooptype == 'maxmp') {
			$val = $numtroops*3 + $userrow["maxmp"];
 		}
 		elseif ($trooptype == 'maxtp') {
        		$val = $numtroops*2 + $userrow["maxtp"];
 		}
 		elseif ($trooptype == 'maxap') {
			$val = $numtroops + $userrow["maxap"];
 		}
 		elseif ($trooptype == 'strength') {
        		$val = $numtroops*2 + $userrow["strength"];
 		}
 		elseif ($trooptype == 'dexterity') {
        		$val = $numtroops*2 + $userrow["dexterity"];
 		}
        	elseif ($trooptype == 'attackpower') {
        		$val = $numtroops + $userrow["attackpower"];
        	}
        	elseif ($trooptype == 'defensepower') {
        		$val = $numtroops + $userrow["defensepower"];
        	}
		$newattributes = $userrow["attributes"] - $numtroops;

		$page .= "<b>You use $numtroops Attributes on your chosen stat.</b><p>";

                $updatequery = doquery("UPDATE {{table}} SET $trooptype='$val', attributes='$newattributes' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

		}
    $newattributes2 = $userrow["attributes"] - $numtroops;
        $page .= "You have <b>$newattributes2</b> attributes remaining to spend. You gain more attributes every 10 levels (the amount depends on your class) and these are then used to increase your stats. Sometimes you may receive attributes for rewards.<br /><br />\n";
        $page .= "Please select the amount of attributes you wish to spend, and for which stat carefully. You <u>cannot</u> reverse this process.<br /><br />\n";
     $page .= "<form action='index.php?do=attributes' method='POST'>";
	$page .= "<input type='text' name='troops' value='0' size='5'> ";
	$page .= "<select name='trooptype'>";
	    $page .= "<option value='maxhp'>Increase HP (+3)</option>";
	    $page .= "<option value='maxmp'>Increase MP (+3)</option>";
	    $page .= "<option value='maxtp'>Increase TP (+2)</option>";
	    $page .= "<option value='maxap'>Increase AP (+1)</option>";
	    $page .= "<option value='strength'>Increase Strength (+2)</option>";
	    $page .= "<option value='dexterity'>Increase Dexterity (+2)</option>";
	    $page .= "<option value='attackpower'>Increase Attack (+1)</option>";
	    $page .= "<option value='defensepower'>Increase Defense (+1)</option></select><br>";
	$page .= "<input type='submit' name='submit' value='Increase Stat'></form>";

	        $page .= "<p><b><u>Stats Information</u>:</b>


	 <br><br>
<b>Hit Points (HP):</b> This is the amount of Health you have and damage you can receive.<br>
<br>
<b>Mana Points (MP):</b> This is used to cast Spells.<br>
<br>
<b>Travel Points (TP):</b> This is used to travel from town to town.<br>
<br>
<b>Ability Points (AP):</b> This is the Ability for you to complete a task. It is mainly used for Guild activities.<br>
<br>

	        <b>Attack:</b> This determines the actual damage you do when you swing.<br>
<br>
<b>Defense:</b> This determines the amount of damage you will block from a monsters swing. (Or another player).<br>

<br>
<b>Strength:</b> This is used to determine the chance of an excellent hit.<br>
<br>
<b>Dexterity:</b> This determines the chances of dodging/blocking monsters hits or running away without getting blocked.<p><center><img src=\"images/levelup.gif\" border=\"0\" alt=\"Spend your Attributes\" /></a></center><p>You may return to what you was <a href=\"index.php\">doing</a>.<br />\n";

	display($page,"Attributes");
}

?>