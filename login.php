<?php // login.php :: Handles logins and cookies.

require_once('lib.php');
if (isset($_GET["do"])) {
    if ($_GET["do"] == "login") { login(); }
    elseif ($_GET["do"] == "logout") { logout(); }
}

function login() {
    
    include('config.php');
    $link = opendb();
            $controlquery = doquery("SELECT * FROM {{table}} WHERE id='1' LIMIT 1", "control");
        $controlrow = mysql_fetch_array($controlquery);
        $onlinequery = doquery("SELECT * FROM {{table}} WHERE UNIX_TIMESTAMP(onlinetime) >= '".(time()-600)."' ORDER BY experience DESC", "users");
     while ($onlinerow = mysql_fetch_array($onlinequery)) {
     }
	$query = doquery("SELECT id FROM {{table}} ORDER BY id DESC LIMIT 1", "users");
	$last_user = mysql_fetch_assoc($query);
    $page = "<center><br>Players Online: <b>" . mysql_num_rows($onlinequery) . "</b> | Most Online: <b>".$controlrow["mostonline"]."</b> | Registered Players: <b>".number_format($last_user['id'])."</b></center>\n";
    if ($_COOKIE['dk_login'] == 1) {
    	die("<b>You have been banned</b><p>Your accounts has been banned and you have been placed into the Town Jail. This may well be permanent, or just a 24 hour temporary warning ban. If you want to be unbanned, contact the game administrator by emailing admin@dk-rpg.com."); }

    if (isset($_POST["submit"])) {
        
        $query = doquery("SELECT * FROM {{table}} WHERE username='".$_POST["username"]."' AND password='".md5($_POST["password"])."' LIMIT 1", "users");
        if (mysql_num_rows($query) != 1) { die("You have entered an invalid username or password. Please go back and try again. <p>If this problem continues, please contact support via the main login page.<p>Note: DK has a regular Player Prune which means all inactive accounts are deleted permanently. Usually your account has to be at least 3-4 weeks old for this to happen, and be level 1."); }
        $row = mysql_fetch_array($query);
        if (isset($_POST["rememberme"])) { $expiretime = time()+31536000; $rememberme = 1; } else { $expiretime = 0; $rememberme = 0; }
        $cookie = $row["id"] . " " . $row["username"] . " " . md5($row["password"] . "--" . $dbsettings["secretword"]) . " " . $rememberme;
        setcookie("dkgame", $cookie, $expiretime, "/", "", 0);

        header("Location: index.php?do=news");
        die();
        
    }
    
    $page .= gettemplate("login");
    $title = "Welcome to Dragons Kingdom - An online browser based RPG!";
    display($page, $title, false, false, false, false);
    
}
    

function logout() {
    
    setcookie("dkgame", "", time()-100000, "/", "", 0);
    header("Location: login.php?do=login");
    die();
    
}

?>