<?php // heal.php :: Handles stuff from the Quick Spells menu. (Healing spells only... other spells are handled in fight.php.)

function healspells($id) {
    
    global $userrow;
    
    $userspells = explode(",",$userrow["spells"]);
    $spellquery = doquery("SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "spells");
    $spellrow = mysql_fetch_array($spellquery);
    
    // All the various ways to error out.
    $spell = false;
    foreach ($userspells as $a => $b) {
        if ($b == $id) { $spell = true; }
    }
    if ($spell != true) { display("<table width='100%' border='1'><tr><td class='title'>Not Learnt</td></tr></table><p>You have not yet learned this spell. Please go <a href='index.php'>back</a> and try again.", "Error"); die(); }
    if ($spellrow["type"] != 1) { display("<table width='100%' border='1'><tr><td class='title'>Unknown Spell</td></tr></table><p>This is not a healing spell. Please go <a href='index.php'>back</a>  and try again.", "Error"); die(); }
    if ($userrow["currentaction"] == "Fighting") { display("<table width='100%' border='1'><tr><td class='title'>Healing Spell</td></tr></table><p>You cannot use the Quick Spells list during a fight. Please go <a href='index.php'>back</a>  and select the Healing Spell you wish to use from the Spells box on the main fighting screen to continue.", "Error"); die(); }
    if ($userrow["currentmp"] < $spellrow["mp"]) { display("<table width='100%' border='1'><tr><td class='title'>Healing Spell</td></tr></table><p>You do not have enough Magic Points to cast this spell. Please go <a href='index.php'>back</a>  and try again.", "Error"); die(); }
    if ($userrow["currentaction"] == "Cave") { display("<table width='100%' border='1'><tr><td class='title'>Healing Spell</td></tr></table><p>You cannot use the Quick Spells list in a Cave or Healing Pool. Please go <a href='index.php'>back</a>  and continue exploring.", "Error"); die(); }
    if ($userrow["currentaction"] == "Healing Pool") { display("<table width='100%' border='1'><tr><td class='title'>Healing Spell</td></tr></table><p>You cannot use the Quick Spells list in a Cave or Healing Pool. Please go <a href='index.php'>back</a>  and continue exploring.", "Error"); die(); }
    if ($userrow["currenthp"] == $userrow["maxhp"]) { display("<table width='100%' border='1'><tr><td class='title'>Healing Spell</td></tr></table><p>Your Hit Points are already full. You don't need to use a Healing spell now. Please <a href='index.php'>continue</a>  what you were doing.", "Error"); die(); }
    
    $newhp = $userrow["currenthp"] + $spellrow["attribute"];
    if ($userrow["maxhp"] < $newhp) { $spellrow["attribute"] = $userrow["maxhp"] - $userrow["currenthp"]; $newhp = $userrow["currenthp"] + $spellrow["attribute"]; }
    $newmp = $userrow["currentmp"] - $spellrow["mp"];
    
    $updatequery = doquery("UPDATE {{table}} SET currenthp='$newhp', currentmp='$newmp' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
    
    display("<table width='100%' border='1'><tr><td class='title'>Healing Spell</td></tr></table><p>You have cast the ".$spellrow["name"]." spell, and gained ".$spellrow["attribute"]." Hit Points. You can now continue <a href=\"index.php\">exploring</a>, or cast the <a href=\"index.php?do=spell:".$spellrow["id"]."\">".$spellrow["name"]."</a> spell again.", "Healing Spell");
    die();
    
}

?>