<?php
global $userrow;

if ($userrow["nearbylist"] == 1) {

$nearbyquery = doquery("SELECT * FROM {{table}} WHERE UNIX_TIMESTAMP(onlinetime) >= '".(time()-600)."' AND latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' AND charname!='".$userrow["charname"]."' AND authlevel!='1' ORDER BY LEVEL DESC", "users");
	  	
	$nearby = "<p><p><center><table width=\"85%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"5\" style=\"background-color:#dddddd;\"><center>Recently Visited Players in Town - In order of Level</center></th></tr><tr><th width=\"1%\" style=\"background-color:#dddddd;\">Character Name</th><th width=\"5%\" style=\"background-color:#dddddd;\">Location</th><th width=\"2%\" style=\"background-color:#dddddd;\">Level</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Attack</th><th  width=\"10%\" style=\"background-color:#dddddd;\">Mug</th></tr>";

        $count = 2;
        if (mysql_num_rows($nearbyquery) == 0) {
       $nearby .= "<td style='background-color:#ffffff;' colspan='5'><b>No players in Town.</b></tr></td></table></table>\n";
        } else {
	    while ($nearbyrow = mysql_fetch_array($nearbyquery)) {
	    $lastactive = strtotime($nearbyrow['onlinetime']);
	    $nowtime = time();
	    $timesincelast = $nowtime - $lastactive;
	    if ($timesincelast <= 120) { 
	  	$namelink2 = "<a href='index.php?do=startduel&id=".$nearbyrow["id"]."'>Request</a>";
	  	} else {
	  		$namelink2 = "<font color=blue>Idle</font>";
	  	}			
		if ($nearbyrow["latitude"] < 0) { $nearbyrow["latitude"] = $nearbyrow["latitude"] * -1 . "S"; } else { $nearbyrow["latitude"] .= "N"; }
        if ($nearbyrow["longitude"] < 0) { $nearbyrow["longitude"] = $nearbyrow["longitude"] * -1 . "W"; } else { $nearbyrow["longitude"] .= "E"; }

		if ($count == 1) { $color = "bgcolor='#ffffff'"; $count = 2; }
		else { $color = "bgcolor='#eeeeee'"; $count = 1;}
		$nearby .= "<tr><td ".$color." width='15%'>";
		$nearby .= "<a href=\"index.php?do=onlinechar:".$nearbyrow["id"]."\">".$nearbyrow["charname"]."</a></td>";
		$nearby .= "<td ".$color." width='5%'>".$nearbyrow["latitude"].", ".$nearbyrow["longitude"]."</td>";
		$nearby .= "<td ".$color." width='5%'>".$nearbyrow["level"]."</td>";
		
     	$nearby .= "<td ".$color." width='5%'>".$namelink2."</td>";
     	$nearby .= "<td ".$color." width='12%'><font color=green>Safe Zone</font></td>";
	  	$nearby .= "</tr>";
	}
	$nearby .= "</table></table></center>";
	
        }
	 } elseif ($userrow["nearbylist"] == 0) {
	
	 	$nearby .= "Nearby Players list hidden. You can Enable it again by visiting your <a href='index.php?do=hideplayers'>Player Options</a>.";
	 	
    
}

         $userrow["nearby"] = $nearby;
$template = <<<THEVERYENDOFYOU
<table width="100%">
<tr>
  <td colspan="3" class="title"><img src="images/town_{{id}}.gif" alt="Welcome to {{name}}" title="Welcome to {{name}}" /></td>
  </tr>
<tr>
  <td colspan="3"><p>{{description}}
    <p>You see the town square in the distance, amongst many other areas:</p></td>
  </tr>
<tr>
  <td width="69%"><UL>
  <br />  <IMG SRC="images/icon_arrow.gif"> 
    <a href="square.php?do=main">{{name}} Town Square</a>
      <br />  <IMG SRC="images/icon_arrow.gif">   
    <a href="index.php?do=playermarket">Player Market</a>
  <br />  <IMG SRC="images/icon_arrow.gif">   
    <a href="skills.php?do=shrine">Skill Shrine</a>
  <br />  <IMG SRC="images/icon_arrow.gif">   
    <a href="index.php?do=arena">Duel Arena</a>
   <br /> <IMG SRC="images/icon_arrow.gif">  
    <a href="index.php?do=gamble">Gambling Den</a>
   <br /> <IMG SRC="images/icon_arrow.gif">   
    <a href="guilds.php">Guild Courtyard</a>
   <br /> <IMG SRC="images/icon_arrow.gif">  
    <a href="quests.php?do=tower">Quest Tower</a>
   <br /> <IMG SRC="images/icon_arrow.gif">   
    <a href="temple.php?do=main">Temple of Rebirth</a>
   <br /> <IMG SRC="images/icon_arrow.gif">  
    <a href="fame.php?do=main">Hall of Fame</a>
  </UL></td>
  <td width="23%"><img src="images/towns/{{id}}.gif" alt="{{name}}" align="left" title="{{name}}" /></td>
  <td width="8%">&nbsp;</td>
</tr>
<tr>
  <td colspan="3"><UL>
   <br /> <IMG SRC="images/icon_arrow.gif">   
    {{bonus}}
  </UL>
    <!--<br /><IMG SRC="images/icon_arrow.gif"> <a href="search.php">Daily Bonus Arena</a>//--><br>$nearby</td>
  </tr>
<tr>
  <td colspan="3">
      <br />
  <table width="100%">
    <tr><td width="50%">
    </td><td>
    </td></tr>
    </table></td>
  </tr>
</table>

THEVERYENDOFYOU;
?>