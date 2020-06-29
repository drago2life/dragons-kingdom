<?php // lib.php :: Common functions used throughout the program.

$starttime = getmicrotime();
$numqueries = 0;

if(!function_exists(opendb)){
 function opendb() { // Open database connection.

    include('config.php');
    extract($dbsettings);
    $link = mysql_connect($server, $user, $pass) or die(mysql_error());
    mysql_select_db($name) or die(mysql_error());
    return $link;

}
}

// Handling for servers with magic_quotes turned on.
// Example from php.net.
if (get_magic_quotes_gpc()) {
   function stripslashes_deep($value)
   {
       $value = is_array($value) ?
                   array_map('stripslashes_deep', $value) :

                   stripslashes($value);

       return $value;
   }

   $_POST = array_map('stripslashes_deep', $_POST);
   $_GET = array_map('stripslashes_deep', $_GET);
   $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
}
foreach($_POST as $a=>$b) { $_POST[$a] = addslashes($b); }
foreach($_GET as $a=>$b) { $_GET[$a] = addslashes($b); }



function doquery($query, $table) { // Something of a tiny little database abstraction layer.

    include('config.php');
    global $numqueries;
    $sqlquery = mysql_query(str_replace("{{table}}", $dbsettings["prefix"] . "_" . $table, $query)) or die(mysql_error());
    $numqueries++;
    return $sqlquery;

}

function gettemplate($templatename) { // SQL query for the template.

    $filename = "templates/" . $templatename . ".php";
    include("$filename");
    return $template;

}

function parsetemplate($template, $array) { // Replace template with proper content.

    foreach($array as $a => $b) {
        $template = str_replace("{{{$a}}}", $b, $template);
    }
    return $template;

}

function getmicrotime() { // Used for timing script operations.

    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);

}

function prettydate($uglydate) { // Change the MySQL date format (YYYY-MM-DD) into something friendlier.

    return date("F j, Y", mktime(0,0,0,substr($uglydate, 5, 2),substr($uglydate, 8, 2),substr($uglydate, 0, 4)));

}

function prettyforumdate($uglydate) { // Change the MySQL date format (YYYY-MM-DD) into something friendlier.

    return date("F j, Y", mktime(0,0,0,substr($uglydate, 5, 2),substr($uglydate, 8, 2),substr($uglydate, 0, 4)));

}

function is_email($email) { // 

    return(preg_match("/^[-_.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\.)+(ad|ae|aero|af|ag|ai|al|am|an|ao|aq|ar|arpa|as|at|au|aw|az|ba|bb|bd|be|bf|bg|bh|bi|biz|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|com|coop|cr|cs|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|in|info|int|io|iq|ir|is|it|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|mg|mh|mil|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|museum|mv|mw|mx|my|mz|na|name|nc|ne|net|nf|ng|ni|nl|no|np|nr|nt|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pro|ps|pt|pw|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)$|(([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\.){3}([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$/i",$email));

}

function makesafe($d) {
    
    $d = str_replace("\t","",$d);
    $d = str_replace("<","&#60;",$d);
    $d = str_replace(">","&#62;",$d);
    $d = str_replace("\n","",$d);
    $d = str_replace("|","??",$d);
    $d = str_replace("  "," &nbsp;",$d);
    return $d;
    
}

function my_htmlspecialchars($text) { // 

  $ALLOWABLE_TAGS = array("b", "i", "u", "p", "blockquote", "ol", "ul", "li");
  static $PATTERNS = array();
  static $REPLACEMENTS = array();
  if (count($PATTERNS) == 0) {
   foreach ($ALLOWABLE_TAGS as $tag) {
     $PATTERNS[] = "/&lt;$tag&gt;/i";
     $PATTERNS[] = "/&lt;\/$tag&gt;/i";
     $REPLACEMENTS[] = "<$tag>";
     $REPLACEMENTS[] = "</$tag>";
   }
  }

  $result = str_replace(array(">", "<", "\"", "'"),
                       array("&gt;", "&lt;", "&quot;", "&#039;"),
                       $text);

  $result = preg_replace($PATTERNS, $REPLACEMENTS, $result);

  return $result;



}

function admindisplay($content, $title) { // Finalize page and output to browser.

    global $numqueries, $userrow, $controlrow, $starttime;
    if (!isset($controlrow)) {
        $controlquery = doquery("SELECT * FROM {{table}} WHERE id='1' LIMIT 1", "control");
        $controlrow = mysql_fetch_array($controlquery);
    }

    $template = gettemplate("admin");

    // Make page tags for XHTML validation.
    $xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n"
    . "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"DTD/xhtml1-transitional.dtd\">\n"
    . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n";

    $finalarray = array(
        "title"=>$title,
        "content"=>$content,
        "totaltime"=>round(getmicrotime() - $starttime, 4),
        "numqueries"=>$numqueries);
    $page = parsetemplate($template, $finalarray);
    $page = $xml . $page;

    if ($controlrow["compression"] == 1) { ob_start("ob_gzhandler"); }
    echo $page;
    die();

}

function moddisplay($content, $title) { // Finalize page and output to browser.

    global $numqueries, $userrow, $controlrow, $starttime;
    if (!isset($controlrow)) {
        $controlquery = doquery("SELECT * FROM {{table}} WHERE id='1' LIMIT 1", "control");
        $controlrow = mysql_fetch_array($controlquery);
    }

    $template = gettemplate("mod");

    // Make page tags for XHTML validation.
    $xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n"
    . "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"DTD/xhtml1-transitional.dtd\">\n"
    . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n";

    $finalarray = array(
        "title"=>$title,
        "content"=>$content,
        "totaltime"=>round(getmicrotime() - $starttime, 4),
        "numqueries"=>$numqueries,
        "version"=>$version);
    $page = parsetemplate($template, $finalarray);
    $page = $xml . $page;

    if ($controlrow["compression"] == 1) { ob_start("ob_gzhandler"); }
    echo $page;
    die();

}

function display($content, $title, $chatnav=true, $leftnav=true, $rightnav=true, $badstart=false) { // Finalize page and output to browser.

    global $numqueries, $userrow, $controlrow;

    if (!isset($controlrow)) {
        $controlquery = doquery("SELECT * FROM {{table}} WHERE id='1' LIMIT 1", "control");
        $controlrow = mysql_fetch_array($controlquery);
    }
    if ($badstart == false) { global $starttime; } else { $starttime = $badstart; }

    // Make page tags for XHTML validation.
    $xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n"
    . "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"DTD/xhtml1-transitional.dtd\">\n"
    . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n";

    $template = gettemplate("primary");

    if ($rightnav == true) { $rightnav = gettemplate("rightnav"); } else { $rightnav = ""; }
    if ($leftnav == true) { $leftnav = gettemplate("leftnav"); } else { $leftnav = ""; }
    
    

    if (isset($userrow)) {




        // Get userrow again, in case something has been updated.
        $userquery = doquery("SELECT * FROM {{table}} WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        unset($userrow);
        $userrow = mysql_fetch_array($userquery);
        $userrow["bank"] = number_format($userrow["bank"]);
        // Current town name.
        if ($userrow["currentaction"] == "In Town") {
            $townquery = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
            $townrow = mysql_fetch_array($townquery);
            $userrow["currenttown"] = "<b>Welcome to ".$townrow["name"].".</b><br /><br />";
        } else {
            $userrow["currenttown"] = "<b>Not in a Town</b><p>";
        }
            if ($userrow["currentaction"] == "In Town") {
            $town2query = doquery("SELECT * FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
            $town2row = mysql_fetch_array($town2query);
            $userrow["currenttown2"] = "".$townrow["name"]."";
            }
        // Format various userrow stuffs...
        if ($userrow["latitude"] < 0) { $userrow["latitude"] = $userrow["latitude"] * -1 . "S"; } else { $userrow["latitude"] .= "N"; }
        if ($userrow["longitude"] < 0) { $userrow["longitude"] = $userrow["longitude"] * -1 . "W"; } else { $userrow["longitude"] .= "E"; }
           $userrow["totalexp"] = ($userrow["experience"] + $userrow["miningxp"] + $userrow["endurancexp"] + $userrow["smeltingxp"] + $userrow["muggingxp"] + $userrow["craftingxp"] + $userrow["forgingxp"]);
        $userrow["skilltotal"] = ($userrow["level"] + $userrow["mining"] + $userrow["endurance"] + $userrow["smelting"] + $userrow["mugging"] + $userrow["crafting"] + $userrow["forging"] + $userrow["skill1level"] + $userrow["skill2level"] + $userrow["skill3level"] + $userrow["skill4level"]);
    $userrow["totalexp"] = number_format($userrow["totalexp"]);
        $userrow["tempexp"] = $userrow["experience"];
        $userrow["experience"] = number_format($userrow["experience"]);
        $userrow["miningxp"] = number_format($userrow["miningxp"]);  
        $userrow["smeltingxp"] = number_format($userrow["smeltingxp"]);  
        $userrow["endurancexp"] = number_format($userrow["endurancexp"]);   
         $userrow["craftingxp"] = number_format($userrow["craftingxp"]); 
$userrow["forgingxp"] = number_format($userrow["forgingxp"]);                     
        $userrow["gold"] = number_format($userrow["gold"]);
        $userrow["dscales"] = number_format($userrow["dscales"]);
$userrow["defensepower"] = number_format($userrow["defensepower"]);
$userrow["attackpower"] = number_format($userrow["attackpower"]);
$userrow["attributes"] = number_format($userrow["attributes"]);
$userrow["bones"] = number_format($userrow["bones"]);

        if ($userrow["authlevel"] == 1) { $userrow["adminlink"] = "<IMG SRC='images/icon_arrow.gif' ALT='Admin Panel'> <a href=\"admin/admin.php\">Admin Panel</a><br>"; } else { $userrow["adminlink"] = ""; }
        if ($userrow["authlevel"] == 3) { $userrow["modlink"] = "<IMG SRC='images/icon_arrow.gif' ALT='Mod Panel'> <a href=\"admin/mod.php\">Mod Panel</a><br>"; } else { $userrow["modlink"] = ""; }

        if (($userrow["latitude"] <= 15 && $userrow["latitude"] >= -15) && ($userrow["longitude"] <= 15 && $userrow["longitude"] >= -15) || $userrow["currentaction"] == "In Town") { $userrow["zone"] = "<font color=green><b>Safe Zone</b></font>"; }
     
        else {
         $userrow["zone"] = "<font color=red><b>Danger Zone</b></font>"; 
    }
        
        if ($userrow["run"] == 1) { $userrow["run"] = "Walking"; }
        if ($userrow["run"] == 3) { $userrow["run"] = "Running"; }
        
        // Home Location
        $charname = $userrow["charname"];
     
        $homesquery2 = doquery("SELECT * FROM {{table}} WHERE charname='$charname' ", "homes");
        $userrow["homes"] = "";
        while ($homesrow2 = mysql_fetch_array($homesquery2)) {
        	          if ($homesrow2["latitude"] < 0) { $homesrow2["latitude"] = $homesrow2["latitude"] * -1 . "S"; } else { $homesrow2["latitude"] .= "N"; }
        if ($homesrow2["longitude"] < 0) { $homesrow2["longitude"] = $homesrow2["longitude"] * -1 . "W"; } else { $homesrow2["longitude"] .= "E"; }

                $userrow["homes"] .= "Home Location: ".$homesrow2["latitude"].", ".$homesrow2["longitude"]."</a><br />\n";
            }
        

        // Exp bar.
        $curexp = 0;
        $nextexp = 0;
        $levelquery = doquery("SELECT id,". $userrow["charclass"]."_exp FROM {{table}} WHERE id='".($userrow["level"]+1)."' OR id='".$userrow["level"]."' LIMIT 2", "levels");
        while($levelrow = mysql_fetch_array($levelquery)) {
            if ($levelrow["id"] == $userrow["level"]) { $curexp = $levelrow[$userrow["charclass"]."_exp"]; }
            if ($levelrow["id"] == ($userrow["level"] + 1)) { $nextexp = $levelrow[$userrow["charclass"]."_exp"]; }
        }

        // HP/MP/TP bars.
        $stathp = ceil($userrow["currenthp"] / $userrow["maxhp"] * 150);
        if ($userrow["maxmp"] != 0) { $statmp = ceil($userrow["currentmp"] / $userrow["maxmp"] * 150); } else { $statmp = 0; }
        $stattp = ceil($userrow["currenttp"] / $userrow["maxtp"] * 150);
        $statap = ceil($userrow["currentap"] / $userrow["maxap"] * 150);
        $statfat = ceil($userrow["currentfat"] / $userrow["maxfat"] * 150);        
        $statexp = ceil( ($userrow["tempexp"] - $curexp) / ($nextexp - $curexp) * 150 );
        if($statexp > 150){
        $statexp = 150;}

  // Now make numbers stand out if theyre low.
        if ($userrow["currenthp"] <= ($userrow["maxhp"]/5)) { $userrow["currenthp"] = "<blink><span class=\"highlight\"><b>*".$userrow["currenthp"]."*</b></span></blink>"; }
        if ($userrow["currentmp"] <= ($userrow["maxmp"]/5)) { $userrow["currentmp"] = "<blink><span class=\"highlight\"><b>*".$userrow["currentmp"]."*</b></span></blink>"; }
        if ($userrow["currenttp"] <= ($userrow["maxtp"]/5)) { $userrow["currenttp"] = "<blink><span class=\"highlight\"><b>*".$userrow["currenttp"]."*</b></span></blink>"; }
        if ($userrow["currentap"] <= ($userrow["maxap"]/5)) { $userrow["currentap"] = "<blink><span class=\"highlight\"><b>*".$userrow["currentap"]."*</b></span></blink>"; }
        if ($userrow["currentfat"] >= ($userrow["maxfat"]-15)) { $userrow["currentfat"] = "<blink><span class=\"highlight\"><b>*".$userrow["currentfat"]."*</b></span></blink>"; }

    $level2query = doquery("SELECT ". $userrow["charclass"]."_exp FROM {{table}} WHERE id='".($userrow["level"]+1)."' LIMIT 1", "levels");
    $level2row = mysql_fetch_array($level2query);
    $userrow["nextlevel2"] = number_format($level2row[$userrow["charclass"]."_exp"]);
        $stattable = "<table width=\"150\"><tr><td width=\"150%\">\n";	
        
                $stattable .= "<table cellspacing=\"0\" cellpadding=\"0\"><tr><td style=\"padding:0px; width:150px; height:5px; border:solid 1px black; vertical-align:bottom;\">\n";
        if ($stathp >= 100) { $stattable .= "<div style=\"padding:0px; width:".$stathp."px; border-top:solid 1px black; background-image:url(images/bars_green.gif);\"><img src=\"images/bars_green.gif\" alt=\"\" /></div>"; }
        if ($stathp < 100 && $stathp >= 50) { $stattable .= "<div style=\"padding:0px; width:".$stathp."px; border-top:solid 1px black; background-image:url(images/bars_yellow.gif);\"><img src=\"images/bars_yellow.gif\" alt=\"\" /></div>"; }
        if ($stathp < 50) { $stattable .= "<div style=\"padding:0px; width:".$stathp."px; border-top:solid 1px black; background-image:url(images/bars_red.gif);\"><img src=\"images/bars_red.gif\" alt=\"\" /></div>"; }
        $stattable .= "</tr><tr><td>HP: ".$userrow["currenthp"]." / ".$userrow["maxhp"]."</td></tr></table>\n";
        
        
                $stattable .= "<table cellspacing=\"0\" cellpadding=\"0\"><tr><td style=\"padding:0px; width:150px; height:5px; border:solid 1px black; vertical-align:bottom;\">\n";
        if ($statmp >= 100) { $stattable .= "<div style=\"padding:0px; width:".$statmp."px; border-top:solid 1px black; background-image:url(images/bars_blue.gif);\"><img src=\"images/bars_blue.gif\" alt=\"\" /></div>"; }
        if ($statmp < 100 && $statmp >= 50) { $stattable .= "<div style=\"padding:0px; width:".$statmp."px; border-top:solid 1px black; background-image:url(images/bars_blue.gif);\"><img src=\"images/bars_blue.gif\" alt=\"\" /></div>"; }
        if ($statmp < 50) { $stattable .= "<div style=\"padding:0px; width:".$statmp."px; border-top:solid 1px black; background-image:url(images/bars_blue.gif);\"><img src=\"images/bars_blue.gif\" alt=\"\" /></div>"; }
        $stattable .= "</tr><tr><td>MP: ".$userrow["currentmp"]." / ".$userrow["maxmp"]."</td></tr></table>\n";
        
             $stattable .= "<table cellspacing=\"0\" cellpadding=\"0\"><tr><td style=\"padding:0px; width:150px; height:5px; border:solid 1px black; vertical-align:bottom;\">\n";
        if ($stattp >= 100) { $stattable .= "<div style=\"padding:0px; width:".$stattp."px; border-top:solid 1px black; background-image:url(images/bars_orange.gif);\"><img src=\"images/bars_orange.gif\" alt=\"\" /></div>"; }
        if ($stattp < 100 && $stattp >= 50) { $stattable .= "<div style=\"padding:0px; width:".$stattp."px; border-top:solid 1px black; background-image:url(images/bars_orange.gif);\"><img src=\"images/bars_orange.gif\" alt=\"\" /></div>"; }
        if ($stattp < 50) { $stattable .= "<div style=\"padding:0px; width:".$stattp."px; border-top:solid 1px black; background-image:url(images/bars_orange.gif);\"><img src=\"images/bars_orange.gif\" alt=\"\" /></div>"; }
        $stattable .= "</tr><tr><td>TP: ".$userrow["currenttp"]." / ".$userrow["maxtp"]."</td></tr></table>\n";   
        
          $stattable .= "<table cellspacing=\"0\" cellpadding=\"0\"><tr><td style=\"padding:0px; width:150px; height:5px; border:solid 1px black; vertical-align:bottom;\">\n";
        if ($statap >= 100) { $stattable .= "<div style=\"padding:0px; width:".$statap."px; border-top:solid 1px black; background-image:url(images/bars_purple.gif);\"><img src=\"images/bars_purple.gif\" alt=\"\" /></div>"; }
        if ($statap < 100 && $statap >= 50) { $stattable .= "<div style=\"padding:0px; width:".$statap."px; border-top:solid 1px black; background-image:url(images/bars_purple.gif);\"><img src=\"images/bars_purple.gif\" alt=\"\" /></div>"; }
        if ($statap < 50) { $stattable .= "<div style=\"padding:0px; width:".$statap."px; border-top:solid 1px black; background-image:url(images/bars_purple.gif);\"><img src=\"images/bars_purple.gif\" alt=\"\" /></div>"; }
        $stattable .= "</tr><tr><td>AP: ".$userrow["currentap"]." / ".$userrow["maxap"]."</td></tr></table>\n";
        
              
        $stattable .= "<table cellspacing=\"0\" cellpadding=\"0\"><tr><td style=\"padding:0px; width:150px; height:5px; border:solid 1px black; vertical-align:bottom;\">\n";
        if ($statfat >= 100) { $stattable .= "<div style=\"padding:0px; width:".$statfat."px; border-top:solid 1px black; background-image:url(images/bars_yellow.gif);\"><img src=\"images/bars_yellow.gif\" alt=\"\" /></div>"; }
        if ($statfat < 100 && $statfat >= 50) { $stattable .= "<div style=\"padding:0px; width:".$statfat."px; border-top:solid 1px black; background-image:url(images/bars_yellow.gif);\"><img src=\"images/bars_yellow.gif\" alt=\"\" /></div>"; }
        if ($statfat < 50) { $stattable .= "<div style=\"padding:0px; width:".$statfat."px; border-top:solid 1px black; background-image:url(images/bars_yellow.gif);\"><img src=\"images/bars_yellow.gif\" alt=\"\" /></div>"; }
        $stattable .= "</tr><tr><td>Fatigue: ".$userrow["currentfat"]." / ".$userrow["maxfat"]."</td></tr></table>\n";
        
        
        $stattable .= "<table cellspacing=\"0\" cellpadding=\"0\"><tr><td style=\"padding:0px; width:150px; height:5px; border:solid 1px black; vertical-align:bottom;\">\n";
        if ($statexp >= 100) { $stattable .= "<div style=\"padding:0px; width:".$statexp."px; border-top:solid 1px black; background-image:url(images/bars_black.gif);\"><img src=\"images/bars_black.gif\" alt=\"\" /></div>"; }
        if ($statexp < 100 && $statexp >= 50) { $stattable .= "<div style=\"padding:0px; width:".$statexp."px; border-top:solid 1px black; background-image:url(images/bars_black.gif);\"><img src=\"images/bars_black.gif\" alt=\"\" /></div>"; }
        if ($statexp < 50) { $stattable .= "<div style=\"padding:0px; width:".$statexp."px; border-top:solid 1px black; background-image:url(images/bars_black.gif);\"><img src=\"images/bars_black.gif\" alt=\"\" /></div>"; }

        $stattable .= "</tr><tr><td>Exp: ".$userrow["experience"]." / ".$userrow["nextlevel2"]."<p><center>View: <a href='index.php?do=onlinechar:".$userrow["id"]."'>Profile</a> - <a href='index.php?do=viewpets'>Pets</a><br><a href='index.php?do=backpack'>Backpack</a></center></td></tr></table></table>\n";
                
        $userrow["statbars"] = $stattable;
        
        
        


        $spellquery = doquery("SELECT id,name,type FROM {{table}}","spells");
        $userspells = explode(",",$userrow["spells"]);
        $userrow["magiclist"] = "";
        while ($spellrow = mysql_fetch_array($spellquery)) {
            $spell = false;
            foreach($userspells as $a => $b) {
                if ($b == $spellrow["id"] && $spellrow["type"] == 1) { $spell = true; }
            }
            if ($spell == true) {
                $userrow["magiclist"] .= "<a href=\"index.php?do=spell:".$spellrow["id"]."\">".$spellrow["name"]."</a><br />";
            }
        }
        if ($userrow["magiclist"] == "") { $userrow["magiclist"] = "No Quick Spells"; }

        $inventitemsquery = doquery("SELECT id,name,type FROM {{table}}","inventitems");
        $userinventitems = explode(",",$userrow["inventitems"]);
        $userrow["inventitems"] = "";
        while ($inventitemsrow = mysql_fetch_array($inventitemsquery)) {
            $inventitems = false;
            foreach($userinventitems as $a => $b) {
                if ($b == $inventitemsrow["id"] && $inventitemsrow["type"] == 1) { $inventitems = true; } 
                elseif ($b == $inventitemsrow["id"] && $inventitemsrow["type"] == 11) { $inventitems = true; }
                elseif ($b == $inventitemsrow["id"] && $inventitemsrow["type"] == 13) { $inventitems = true; }
                elseif ($b == $inventitemsrow["id"] && $inventitemsrow["type"] == 14) { $inventitems = true; }
                elseif ($b == $inventitemsrow["id"] && $inventitemsrow["type"] == 15) { $inventitems = true; }
                elseif ($b == $inventitemsrow["id"] && $inventitemsrow["type"] == 16) { $inventitems = true; }
                elseif ($b == $inventitemsrow["id"] && $inventitemsrow["type"] == 17) { $inventitems = true; }
                elseif ($b == $inventitemsrow["id"] && $inventitemsrow["type"] == 18) { $inventitems = true; }
                elseif ($b == $inventitemsrow["id"] && $inventitemsrow["type"] == 20) { $inventitems = true; }
                elseif ($b == $inventitemsrow["id"] && $inventitemsrow["type"] == 21) { $inventitems = true; }
                elseif ($b == $inventitemsrow["id"] && $inventitemsrow["type"] == 25) { $inventitems = true; }
                elseif ($b == $inventitemsrow["id"] && $inventitemsrow["type"] == 26) { $inventitems = true; }
                elseif ($b == $inventitemsrow["id"] && $inventitemsrow["type"] == 27) { $inventitems = true; }
                elseif ($b == $inventitemsrow["id"] && $inventitemsrow["type"] == 28) { $inventitems = true; }
                elseif ($b == $inventitemsrow["id"] && $inventitemsrow["type"] == 29) { $inventitems = true; }
                elseif ($b == $inventitemsrow["id"] && $inventitemsrow["type"] == 30) { $inventitems = true; }
                elseif ($b == $inventitemsrow["id"] && $inventitemsrow["type"] == 31) { $inventitems = true; }
                elseif ($b == $inventitemsrow["id"] && $inventitemsrow["type"] == 32) { $inventitems = true; }
                elseif ($b == $inventitemsrow["id"] && $inventitemsrow["type"] == 33) { $inventitems = true; }
                elseif ($b == $inventitemsrow["id"] && $inventitemsrow["type"] == 34) { $inventitems = true; }
                elseif ($b == $inventitemsrow["id"] && $inventitemsrow["type"] == 35) { $inventitems = true; }
                elseif ($b == $inventitemsrow["id"] && $inventitemsrow["type"] == 36) { $inventitems = true; }
                elseif ($b == $inventitemsrow["id"] && $inventitemsrow["type"] == 37) { $inventitems = true; }
                elseif ($b == $inventitemsrow["id"] && $inventitemsrow["type"] == 38) { $inventitems = true; }
                elseif ($b == $inventitemsrow["id"] && $inventitemsrow["type"] == 39) { $inventitems = true; }
                elseif ($b == $inventitemsrow["id"] && $inventitemsrow["type"] == 40) { $inventitems = true; }
                elseif ($b == $inventitemsrow["id"] && $inventitemsrow["type"] == 41) { $inventitems = true; }
                elseif ($b == $inventitemsrow["id"] && $inventitemsrow["type"] == 50) { $inventitems = true; }
            }
            if ($inventitems == true) {
                $userrow["inventitemslist"] .= "<a href=\"index.php?do=qitem:".$inventitemsrow["id"]."\">".$inventitemsrow["name"]."</a><br />";
            }
        }
        if ($userrow["inventitemslist"] == "") { $userrow["inventitemslist"] = "No Quick Items"; }

// Whos in chat
$online2query = doquery("SELECT * FROM {{table}} WHERE
UNIX_TIMESTAMP(chattime) >= '".(time()-90)."' AND charname!='Admin' ORDER BY charname",
"users");
    $userrow["chatonline"] =  mysql_num_rows($online2query);

        
// Whos online
$onlinequery = doquery("SELECT * FROM {{table}} WHERE
UNIX_TIMESTAMP(onlinetime) >= '".(time()-600)."' ORDER BY charname",
"users");
    $userrow["numonline"] =  mysql_num_rows($onlinequery);
    $online =  mysql_num_rows($onlinequery);
	if (mysql_num_rows($onlinequery) > $controlrow["mostonline"]) { 
		$query = doquery("UPDATE {{table}} SET mostonline='$online' WHERE id='1' LIMIT 1", "control");
	}
// Game mail
$player = $userrow["charname"];
$mailquery = doquery("SELECT * FROM {{table}} WHERE recipient='$player' AND
mread='0' ", "gamemail");
$newmail = mysql_num_rows($mailquery);
$userrow["newmail"] = "(".$newmail.")";

// Game mail image
$player = $userrow["charname"];
$mailimagequery = doquery("SELECT * FROM {{table}} WHERE recipient='$player' AND mread='0' ", "gamemail");
if (mysql_num_rows($mailimagequery) > "0") {
$mailimage = "<a href=\"gamemail.php\"><IMG SRC=\"images/notify_mail.gif\" ALT=\"Check your mail!\" border=\"0\"></a> ";
}


        // Travel To list.
        $townslist = explode(",",$userrow["towns"]);
        $townquery2 = doquery("SELECT * FROM {{table}} ORDER BY id", "towns");
        $userrow["townslist"] = "";
        while ($townrow2 = mysql_fetch_array($townquery2)) {
            $town = false;
            foreach($townslist as $a => $b) {
                if ($b == $townrow2["id"]) { $town = true; }
            }
            if ($town == true) {
                $userrow["townslist"] .= "<IMG SRC=\"images/icon_arrow.gif\"> TP: ".$townrow2["travelpoints"]." <a href=\"index.php?do=gotown:".$townrow2["id"]."\">".$townrow2["name"]."</a><br />\n";
            }
        }

    } else {
        $userrow = array();
    }
    
 

    $finalarray = array(
        "dkgamename"=>$controlrow["gamename"],
        "mailimage"=>$mailimage,        
        "title"=>$title,
        "content"=>$content,
        "rightnav"=>parsetemplate($rightnav,$userrow),
        "leftnav"=>parsetemplate($leftnav,$userrow),
        "totaltime"=>round(getmicrotime() - $starttime, 4),
        "numqueries"=>$numqueries);
    $page = parsetemplate($template, $finalarray);
    $page = $xml . $page;
    if ($controlrow["compression"] == 1) { ob_start("ob_gzhandler"); }
    echo $page;
    die();

}

?>