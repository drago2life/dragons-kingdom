<?php


$data .= "
<style>
td,body
{
    font-family: Arial, Helvetica, sans-serif;
    font-size: 8pt;
    color: #444444;
}
</style>
<br>
    <center>
     <div style=\"border-bottom:1px #999999 solid;width:250;\"><b>
       <font size='2' color='#3896CC'><b>Dragon's Kingdom Service Status</b></font></b>
     </div>
   </center>
<br>";

//configure script
$timeout = "1";

//set service checks
$port[1] = "80";       $service[1] = "Apache";                  $ip[1] ="";
$port[2] = "21";       $service[2] = "FTP";                     $ip[2] ="";
$port[3] = "3306";     $service[3] = "MYSQL";                   $ip[3] ="";
$port[4] = "25";       $service[4] = "Email(POP3)";             $ip[4] ="";
$port[5] = "143";      $service[5] = "Email(IMAP)";             $ip[5] ="";
$port[6] = "2095";     $service[6] = "Webmail";                 $ip[6] ="";
$port[7] = "2082";     $service[7] = "Cpanel";                  $ip[7] ="";
$port[8] = "80";       $service[8] = "Internet Connection";     $ip[8] ="google.com";
$port[9] = "2086";     $service[9] = "WHM";                     $ip[9] ="";

//
// NO NEED TO EDIT BEYOND HERE
// UNLESS YOU WISH TO CHANGE STYLE OF RESULTS
//

//count arrays
$ports = count($port);
$ports = $ports + 1;
$count = 1;

//beggin table for status
$data .= "<table width='250' border='1' cellspacing='0' cellpadding='3' style='border-collapse:collapse' bordercolor='#333333' align='center'>";

while($count < $ports){

     if($ip[$count]==""){
       $ip[$count] = "localhost";
     }

        $fp = @fsockopen("$ip[$count]", $port[$count], $errno, $errstr, $timeout);
        if (!$fp) {
            $data .= "<tr><td>$service[$count]</td><td bgcolor='#FFC6C6'>Offline </td></tr>";
        } else {
            $data .= "<tr><td>$service[$count]</td><td bgcolor='#D9FFB3'>Online</td></tr>";
        }
    $count++;

}

//close table
$data .= "</table>";

echo $data;
//
// SERVER INFORMATION
//

$data1 .= "
<br>
    <center>
     <div style=\"border-bottom:1px #999999 solid;width:250;\"><b>
       <font size='2' color='#3896CC'><b>Dragon's Kingdom Server Information</b></font></b>
     </div>
   </center><BR>";

$data1 .= "<table width='250' border='1' cellspacing='0' cellpadding='3' style='border-collapse:collapse'

bordercolor='#333333' align='center'>";

//GET SERVER LOADS
$loadresult = @exec('uptime');
preg_match("/load average: ([0-9.]+), ([0-9.]+), ([0-9.]+)/",$loadresult,$avgs);


//GET SERVER UPTIME
  $uptime = explode(' up ', $loadresult);
  $uptime = explode(',', $uptime[1]);
  $uptime = $uptime[0].', '.$uptime[1];

$data1 .= "<tr><td>Server Load Averages </td><td>$avgs[1], $avgs[2], $avgs[3]</td>\n";
$data1 .= "<tr><td>Server Load Limit </td><td>10.00</td>\n";
$data1 .= "<tr><td>Server Uptime        </td><td>$uptime                     </td></tr>";
$data1 .= "</table>";
echo $data1;
?><p><center><font size='1' color='#3896CC'>When the Server Load reachs the same or above as the Server Load limit,<br>DK will temporary close to cool down the server.</font><p><a href="admin.php">Return to Admin Panel</a></center>
<head><style type="text/css">

body {
  background-image: url(http://www.dk-rpg.com/images/background.jpg);

</style></head>