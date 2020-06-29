<?php // admin.php :: primary administration script.

include('../lib.php');
include('../cookies.php');
$link = opendb();
$userrow = checkcookies();
if ($userrow == false) { die("Please log in to the <a href=\"../login.php?do=login\">game</a> before using the control panel."); }
if ($userrow["authlevel"] != 1) { die("You must have administrator privileges to use the control panel."); }
$controlquery = doquery("SELECT * FROM {{table}} WHERE id='1' LIMIT 1", "control");
$controlrow = mysql_fetch_array($controlquery);


    
if (isset($_GET["do"])) {
    $do = explode(":",$_GET["do"]);
    
    if ($do[0] == "main") { main(); }
	elseif ($do[0] == "list") { donothing($do[1]); }
        
} else { donothing(); }
    
function donothing($start=0) {

global $controlrow, $userrow; 

 $page = "<table width='100%' border='1'><tr><td class='title'>Clean Up</td></tr></table><p>";
  

$query = doquery("DELETE FROM {{table}} WHERE id >'0' ", "chat"); //Clear Chat
$query = doquery("DELETE FROM {{table}} WHERE id >'0' ", "souls"); // Clear Souls

$query = doquery("INSERT INTO {{table}} SET posttime='NOW()', author='DK Bot', babble='Player Chat Cleared' ", "chat"); //Chat Bot Message

$page .= "Cleared out: Souls & Chat. Chat Bot Posted.\n";
admindisplay($page, "Daily Clean Up");
    
}
    
?>