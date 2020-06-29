<?php // fatigue.php :: Verify Code and restore fatigue

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
//Must vote
if (($userrow["poll"] != "Voted") && ($userrow["level"] >= "3")) { header("Location: poll.php"); die(); }
if ($controlrow["gameopen"] == 0) { 
			header("Location: index.php"); die();
}

	// Include the main file containing the class. - *REQUIRED
	include('FormProtect.php');

	// Create a new instance of FormProtect - *REQUIRED
	$FormProtect	= new FormProtect_Class;

	// Grab the code that was entered on the signup page - *REQUIRED
	$enteredCode	= $_POST['code'];

	// Pass the entered code to the verify function in the class and capture the result in $result. - *REQUIRED
	$result = $FormProtect->verifyCode($enteredCode);

	// If the returned result is true then the code is correct.
	global $userrow;
    
	if ($result == "true") {
	{
		// Code was correct!
		echo "";
	global $userrow;		
    $uq = doquery("UPDATE {{table}} SET currentfat='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
    display("<table width='100%'><tr><td class='title'>Restore Fatigue</td></tr></table><p><font color=green>The code you entered is correct!</font><p>You get setup camp using your Desert Tent and rest for a while, and Restored all your Fatigue back to 0%.<p>You can now continue what you were doing:<p><ul><li /><a href='index.php'>Town or Exploring</a><li /><a href='skills.php?do=endurance'>Endurance Courses</a><li /><a href='skills.php?do=mining'>Mining Field</a><li /><a href='skills.php?do=smelting'>Smelting Furnace</a><li /><a href='skills.php?do=forging'>Forging Anvils</a><li /><a href='skills.php?do=crafting'>Crafting</a><li /><a href='skills.php?do=prayer'>Prayer Sanctuary</a></ul>", "Restore Fatigue");
	}
	}
	else
	{
		// Code was denied!
		display("<table width='100%'><tr><td class='title'>Restore Fatigue</td></tr></table><p>The code that you entered is incorrect. Please go <a href=\"javascript: history.go(-1)\">back</a> and try again. Remember that letters are case sensative.", "Restore Fatigue");
	}

?>