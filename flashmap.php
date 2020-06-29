<?php
//including the connect file
include "listheader.php";

//flashmap
$gatherInfo = "SELECT * FROM dk_users WHERE authlevel!='1' ORDER BY onlinetime DESC LIMIT 16"; //you can change the limit, but change it in flash too
$result = mysql_query($gatherInfo);
$n=0;
while($row = mysql_fetch_array($result)){
$n++;
$pl_name = "$row[charname]";
$pl_latitude = ($row[latitude]);
$pl_longitude = ($row[longitude]);
$pl_xpos = ($row[longitude] + 600);
$pl_ypos = (-$row[latitude] + 600);
$name = "name";
$latitude = "latitude";
$longitude = "longitude";
$xpos = "xpos";
$ypos = "ypos";
echo "
wtflol2=1&player$n$name=$pl_name&player$n$latitude=$pl_latitude&player$n$longitude=$pl_longitude&player$n$xpos=$pl_xpos&player$n$ypos=$pl_ypos&wtflol3=1";
};
?>