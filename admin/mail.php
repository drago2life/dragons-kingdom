<?php //emails user from admin's email

//stuff I'm not sure is needed, but is there just in case...
include('../lib.php');
include('../cookies.php');
$link = opendb();
$userrow = checkcookies();
if ($userrow == false) { die("Please log in to the <a href=\"../login.php?do=login\">game</a> before using the control panel."); }
if ($userrow["authlevel"] != 1) { die("You must have administrator privileges to use the control panel."); }

//The main mail function
    $controlquery = doquery("SELECT * FROM {{table}} WHERE id='1' LIMIT 1", "control");
    $controlrow = mysql_fetch_array($controlquery);
    extract($controlrow);
$adminemail = $controlrow['adminemail'];
if ($action == "send")
{
 $sub = $_POST['sub'];
 $mes = $_POST['mes'];
 $rec = $_POST['rec'];
 $frm = $_POST['frm'];
 mail("$rec", "$sub", "$mes", "From: $frm");
$page = "Email Sent!"; 
 }
 else 
 {
  $page = "<form method=\"POST\" action=\"$PHP_SELF?action=send\">
  
  To:<br><input type=\"text\" name=\"rec\" value=\"$to\"> <p>
  From:<br><input type=\"text\" name=\"frm\" value=\"$adminemail\"><p>
  Subject:<br><input type=\"text\" name=\"sub\" value=\"Dragons Kingdom\"><p>
  Message:<br><textarea name=\"mes\" rows=\"10\" cols=\"35\"></textarea>
  <p><input type=\"submit\" value=\"Send Email\"/> <input type=\"reset\" value=\"Clear\"/></form>";
   }
admindisplay($page, "Send Email");
?>