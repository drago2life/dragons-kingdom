<?php // users.php :: Handles user account functions.

include('lib.php');
$link = opendb();

if (isset($_GET["do"])) {
    
    $do = $_GET["do"];
    if ($do == "register") { register(); }
    elseif ($do == "lostpassword") { lostpassword(); }
    elseif ($do == "changepassword") { changepassword(); }
    elseif ($do == "username") { username(); }  
}

function register() { // Register a new account.
    
    $controlquery = doquery("SELECT * FROM {{table}} WHERE id='1' LIMIT 1", "control");
    $controlrow = mysql_fetch_array($controlquery);
    
    if (isset($_POST["submit"])) {
        
        extract($_POST);
        
        $errors = 0; $errorlist = "";
        
        // Process username.
        if ($username == "") { $errors++; $errorlist .= "Username field is required.<br />"; }
        if (preg_match("/[^A-z0-9_\-]/", $username)==1) { $errors++; $errorlist .= "Username must be alphanumeric and contain no spaces. The following characters are not allowed: - _ \ / [ ] ^<br />"; } // Thanks to "Carlos Pires" from php.net!
        $usernamequery = doquery("SELECT username FROM {{table}} WHERE username='$username' LIMIT 1","users");
        if (mysql_num_rows($usernamequery) > 0) { $errors++; $errorlist .= "Username already taken - unique username required.<br />"; }
        
        // Process charname.
        if ($charname == "") { $errors++; $errorlist .= "Character Name field is required.<br />"; }
        if (preg_match("/[^A-z0-9_\-]/", $charname)==1) { $errors++; $errorlist .= "Character Name must be alphanumeric and contain no spaces. The following characters are not allowed: - _ \ / [ ] ^<br />"; } // Thanks to "Carlos Pires" from php.net!
        $characternamequery = doquery("SELECT charname FROM {{table}} WHERE charname='$charname' LIMIT 1","users");
        if (mysql_num_rows($characternamequery) > 0) { $errors++; $errorlist .= "Character Name already taken - unique Character Name required.<br />"; }
    
        // Process email address.
        if ($email1 == "") { $errors++; $errorlist .= "Email field is required.<br />"; }
        if (! is_email($email1)) { $errors++; $errorlist .= "Email isn't valid.<br />"; }
        $emailquery = doquery("SELECT email FROM {{table}} WHERE email='$email1' LIMIT 1","users");
        
        // Process password.
        if ($password1 == "" || $password2 == "") { $errors++; $errorlist .= "Password field is required.<br />"; }
        if (preg_match("/[^A-z0-9_\-]/", $password1)==1) { $errors++; $errorlist .= "Password must be alphanumeric and contain no spaces. The following characters are not allowed: - _ \ / [ ] ^<br />"; } // Thanks to "Carlos Pires" from php.net!
        if ($password1 != $password2) { $errors++; $errorlist .= "Passwords don't match.<br />"; }
        $password = md5($password1);
        
        if ($errors == 0) {
            
            if ($controlrow["verifyemail"] == 1) {
                $verifycode = "";
                for ($i=0; $i<8; $i++) {
                    $verifycode .= chr(rand(65,90));
                }
            } else {
                $verifycode='1';
            }
            
            $query = doquery("INSERT INTO {{table}} SET id='',regdate=NOW(),verify='1',username='$username',password='$password',email='$email1',charname='$charname',charclass='$charclass',name='$name',gender='$gender',country='$country',msn='$msn',aim='$aim',yim='$yim',icq='$icq'", "users") or die(mysql_error());
            
            if ($controlrow["verifyemail"] == 1) {
                if (sendregmail($email1, $verifycode) == true) {
                    $page = "Your account was created successfully.<br /><br />You should receive a Registration email shortly.<br /><br />You may now continue to the <a href=\"login.php?do=login\">Login Page</a>.";
                } else {
                    $page = "Your account was created successfully.<br /><br />However, there was a problem sending your registration email.<br /><br />You may now continue to the <a href=\"login.php?do=login\">Login Page</a>.";
                }
            } else {
                $page = "Your account was created succesfully.<br /><br />You may now continue to the <a href=\"login.php?do=login\">Login Page</a> and continue playing ".$controlrow["gamename"]."!";
            }
            
        } else {
            
            $page = "The following error(s) occurred when your account was being made:<br /><span style=\"color:red;\">$errorlist</span><br />Please go back and try again.";
            
        }
        
    } else {
        
        $page = gettemplate("register");
        if ($controlrow["verifyemail"] == 1) { 
            $controlrow["verifytext"] = "<br /><span class=\"small\">A verification code will be sent to the address above, and you will not be able to log in without first entering the code. Please be sure to enter your correct email address.</span>";
        } else {
            $controlrow["verifytext"] = "";
        }
        $page = parsetemplate($page, $controlrow);
        
    }
    
    $topnav = "<a href=\"login.php?do=login\"><img src=\"images/button_login.gif\" alt=\"Log In\" border=\"0\" /></a><a href=\"users.php?do=register\"><img src=\"images/button_register.gif\" alt=\"Register\" border=\"0\" /></a><a href=\"help.php\"><img src=\"images/button_help.gif\" alt=\"Help\" border=\"0\" /></a>";
    display($page, "Register", false, false, false);
    
}

function username() { // Resends the users their username
	if (isset($_POST["submit"])) {
		$query = doquery("SELECT * FROM {{table}} WHERE email='$_POST[email]' LIMIT 1", "users");
		$user = mysql_fetch_array($query);
		if (!$user[id])
			die("No such email address!");


		$controlquery = doquery("SELECT * FROM {{table}} WHERE id='1' LIMIT 1", "control");
		$controlrow = mysql_fetch_array($controlquery);
		extract($controlrow);
		$uname = $user['username'];
		$emailaddress = $user['email'];
    
		$email = <<<END
You or someone using your email address recently signed up for an account on the $gamename server, located at $gameurl.

This email is sent due to you or someone requesting your username. This may be due to you forgotting or losing it.

Username: $uname

If you were not the person who signed up for the game, please disregard this message. You will not be emailed again.
END;
		mymail($emailaddress, "$gamename Username", $email);
		$page = "Your Username has been sent to that email address.<p>Return to the main <a href=index.php>page</a>.";
		display($page, "Request Username", false, false, false);
	} else {
		$page = gettemplate("username");
		display($page, "Request Username", false, false, false);
	}
}
function lostpassword() {
    
    if (isset($_POST["submit"])) {
        extract($_POST);
        $userquery = doquery("SELECT email FROM {{table}} WHERE email='$email' LIMIT 1","users");
        if (mysql_num_rows($userquery) != 1) { die("No account with that email address. Please go back and try again. If you are having difficulties, please contact support via the main page."); }
        $newpass = "";
        for ($i=0; $i<8; $i++) {
            $newpass .= chr(rand(65,90));
        }
        $md5newpass = md5($newpass);
        $updatequery = doquery("UPDATE {{table}} SET password='$md5newpass' WHERE email='$email' LIMIT 1","users");
        if (sendpassemail($email,$newpass) == true) {
            display("Your new password was emailed to the address you provided.<br /><br />Once you receive it, you may <a href=\"login.php?do=login\">Log In</a> and continue playing.<br /><br /><p>You may find if you have registered with the same email address more than once, that your first registered account gets a new password, and not the one you anticipated. Contact the administrator for support.<p>Thank you.","Lost Password",false,false,false);
        } else {
            display("There was an error sending your new password.<br /><br />Please check with the game administrator for more information.<br /><br />We apologize for the inconvience.","Lost Password",false,false,false);
        }
        die();
    }
    $page = gettemplate("lostpassword");
    $topnav = "<a href=\"login.php?do=login\"><img src=\"images/button_login.gif\" alt=\"Log In\" border=\"0\" /></a><a href=\"users.php?do=register\"><img src=\"images/button_register.gif\" alt=\"Register\" border=\"0\" /></a><a href=\"help.php\"><img src=\"images/button_help.gif\" alt=\"Help\" border=\"0\" /></a>";
    display($page, "Lost Password", false, false, false);
    
}

function changepassword() {
    
    if (isset($_POST["submit"])) {
        extract($_POST);
        $userquery = doquery("SELECT * FROM {{table}} WHERE username='$username' LIMIT 1","users");
        if (mysql_num_rows($userquery) != 1) { die("No account with that username."); }
        $userrow = mysql_fetch_array($userquery);
        if ($userrow["password"] != md5($oldpass)) { die("The old password you provided was incorrect."); }
        if (preg_match("/[^A-z0-9_\-]/", $newpass1)==1) { die("New password must be alphanumeric."); } // Thanks to "Carlos Pires" from php.net!
        if ($newpass1 != $newpass2) { die("New passwords don't match."); }
        $realnewpass = md5($newpass1);
        $updatequery = doquery("UPDATE {{table}} SET password='$realnewpass' WHERE username='$username' LIMIT 1","users");
        if (isset($_COOKIE["dkgame"])) { setcookie("dkgame", "", time()-100000, "/", "", 0); }
        display("Your password was changed successfully.<br /><br />You have been logged out of the game to avoid cookie errors.<br /><br />Please <a href=\"login.php?do=login\">log back in</a> to continue playing.","Change Password",false,false,false);
        die();
    }
    $page = gettemplate("changepassword");
    $topnav = "<a href=\"login.php?do=login\"><img src=\"images/button_login.gif\" alt=\"Log In\" border=\"0\" /></a><a href=\"users.php?do=register\"><img src=\"images/button_register.gif\" alt=\"Register\" border=\"0\" /></a><a href=\"help.php\"><img src=\"images/button_help.gif\" alt=\"Help\" border=\"0\" /></a>";
    display($page, "Change Password", false, false, false); 
    
}

function sendpassemail($emailaddress, $password) {
    
    $controlquery = doquery("SELECT * FROM {{table}} WHERE id='1' LIMIT 1", "control");
    $controlrow = mysql_fetch_array($controlquery);
    extract($controlrow);
    
$email = <<<END
You or someone using your email address submitted a Lost Password application on the $gamename server, located at $gameurl. 

We have issued you a new password so you can log back into the game.

Your new password is: $password

If this password does not work for your account, then this may mean that you have more than one account registered with this email address. This means that you have just reset your first registered accounts password, and not the one you anticipated. Contact the administrator for support.

Thanks for playing.
END;

    $status = mymail($emailaddress, "$gamename Lost Password", $email);
    return $status;
    
}

function sendregmail($emailaddress, $vercode) {
    
	$query = doquery("SELECT * FROM {{table}} WHERE email='$_POST[email]' LIMIT 1", "users");
	$user = mysql_fetch_array($query);
	
	$controlquery = doquery("SELECT * FROM {{table}} WHERE id='1' LIMIT 1", "control");
    $controlrow = mysql_fetch_array($controlquery);
    extract($controlrow);
    
		$username = $user['username'];
		$password = $user['password'];
    
$email = <<<END
Hello Adventurer,

You or someone using your email address recently signed up for an account on the $gamename server, located at $gameurl.

If you have problems logging into the game or have any other problems please don't hesitate to contact the administrator.

Note: When signing in for the first time, if it flash's the login page and brings you back to the page you originally started with, you will need to enable cookies correctly to login. 

Thank you for registering,
DK Administrator.
END;


    $status = mymail($emailaddress, "$gamename Account Details", $email);
    return $status;
    
}

function mymail($to, $title, $body, $from = '') { // thanks to arto dot PLEASE dot DO dot NOT dot SPAM at artoaaltonen dot fi.

    $controlquery = doquery("SELECT * FROM {{table}} WHERE id='1' LIMIT 1", "control");
    $controlrow = mysql_fetch_array($controlquery);
    extract($controlrow);
    

  $from = trim($from);

  if (!$from) {
   $from = '<'.$controlrow["adminemail"].'>';
  }

  $rp    = $controlrow["adminemail"];
  $org    = '$gameurl';
  $mailer = 'PHP';

  $head  = '';
  $head  .= "Content-Type: text/plain \r\n";
  $head  .= "Date: ". date('r'). " \r\n";
  $head  .= "Return-Path: $rp \r\n";
  $head  .= "From: $from \r\n";
  $head  .= "Sender: $from \r\n";
  $head  .= "Reply-To: $from \r\n";
  $head  .= "Organization: $org \r\n";
  $head  .= "X-Sender: $from \r\n";
  $head  .= "X-Priority: 3 \r\n";
  $head  .= "X-Mailer: $mailer \r\n";

  $body  = str_replace("\r\n", "\n", $body);
  $body  = str_replace("\n", "\r\n", $body);

  return mail($to, $title, $body, $head);
  
}


?>