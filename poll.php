<?PHP
include('lib.php');
include('cookies.php');
$link = opendb();
$controlquery = doquery("SELECT * FROM {{table}} WHERE id='1' LIMIT 1", "control");
$controlrow = mysql_fetch_array($controlquery);
$userrow = checkcookies();
if ($userrow == false) { 
    if (isset($_GET["do"])) {
        if ($_GET["do"] == "verify") { header("Location: users.php?do=verify"); die(); }
    }
    header("Location: login.php?do=login"); die(); 
}
// Close game.
if ($controlrow["gameopen"] == 0) { display("<table width='100%' border='1'><tr><td class='title'>Dragon's Kingdom is Closed</td></tr></table><p><center><img src='images/main.gif' alt=\"Dragon's Kingdom RPG\"></center><p>Dragon's Kingdom is currently closed for maintanence and/or updates.<p><b>Estimated Time:</b> ".$controlrow["updatetime"]."<br><b>Information:</b> ".$controlrow["info"]."<p>Please check back later.","Game Closed For Updates"); die(); }
// Force verify if the user isn't verified yet.
if ($controlrow["verifyemail"] == 1 && $userrow["verify"] != 1) { header("Location: users.php?do=verify"); die(); }
// Block user if he/she has been banned.
if ($userrow["authlevel"] == 2 || ($_COOKIE['dk_login'] == 1)) { setcookie("dk_login", "1", time()+999999999999999); 
die("<b>You have been banned</b><p>Your accounts has been banned and you have been placed into the Town Jail. This may well be permanent, or just a 24 hour temporary warning ban. If you want to be unbanned, contact the game administrator by emailing admin@dk-rpg.com."); }
if ($userrow["tutorial"] == 0) { header("Location: tutorial.php"); die(); } //Not done the tutorial
$title = 'Poll - ';

if (isset($_GET["do"])) {
    $do = explode(":",$_GET["do"]);
	if ($do[0] == "view") { view_poll($do[1]); }
	elseif ($do[0] == "archive") { archive($do[1]); }
	elseif ($do[0] == "vote") { vote($do[1], $do[2]); }
	elseif ($do[0] == "admin") { admin($do[1]); }

} else { show_polls(); }

function show_polls() {
	global $userrow, $title;
    $updatequery = doquery("UPDATE {{table}} SET location='Viewing Polls' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	$query = doquery("SELECT * FROM {{table}} WHERE type=1 ORDER BY id DESC", "poll");
	$page = "<table width='100%' border='1'><tr><td class='title'>Poll List</td></tr></table><p>";
	if ($userrow["poll"] != "Voted") {
	$page .= "You must vote before you can continue playing the game.<p>";
	} else {
	$page .= "Thank you for taking the time to vote. You can view old Polls by clicking beneath the Closed Polls section, or you may go view the results of current Open Polls.<p>";
	}
	$page .= '<table width="100%"><tr><td style="padding:1px; background-color:black;"><table width="100%" style="margins:0px;" cellspacing="1" cellpadding="3">';
	$page .= '<tr><th width="50%" style="background-color:#dddddd;">Poll Question</th><th width="30%" style="background-color:#dddddd;">Date Opened & Closed</th></tr>\n';

	if (!mysql_num_rows($query))
        $page .= '<tr><td style="background-color:#ffffff;" colspan="4"><b>There are no open polls.</b></td></tr>\n';
	else {
		while ($poll = mysql_fetch_assoc($query)) {
				$aquery = doquery("SELECT id, charname FROM {{table}} WHERE id=".$poll['voter']." LIMIT 1", "users");
				$author = mysql_fetch_assoc($aquery);
            	$page .= '<tr><td style="background-color:#ffffff;"><a href="poll.php?do=view:'.$poll['id'].'">'.$poll['question'].'</a></td><td style="background-color:#ffffff;">'.prettydate($poll['open_date']).'</td></tr>\n';
		}
	}
	$page .= '<tr><th colspan="3" style="background-color:#dddddd;"><div style="TEXT-ALIGN: center">Closed Polls</div></th></tr>';
	$query = doquery("SELECT * FROM {{table}} WHERE type=3 ORDER BY id DESC", "poll");
	if (!mysql_num_rows($query))
        $page .= '<tr><td style="background-color:#ffffff;" colspan="4"><b>There are no closed polls.</b></td></tr>\n';
	else {
		while ($poll = mysql_fetch_assoc($query)) {
				$aquery = doquery("SELECT id, charname FROM {{table}} WHERE id=".$poll['voter']." LIMIT 1", "users");
				$author = mysql_fetch_assoc($aquery);
            	$page .= '<tr><td style="background-color:#eeeeee;"><a href="poll.php?do=view:'.$poll['id'].'">'.$poll['question'].'</a></td><td style="background-color:#eeeeee;">'.prettydate($poll['open_date']).' - '.prettydate($poll['closed_date']).'</td></tr>\n';
		}
	}


    $page .= '</table></td></tr></table><br><br>You may <a href=index.php>return</a> to what you were doing, or use the compass on the right to start exploring.';
	display($page, $title);
}

function view_poll($id) {
	global $userrow, $title;
	$id = intval($id);
	$vote_1 = 0;
	$vote_2 = 0;
	$vote_3 = 0;
	$vote_4 = 0;
	$query = doquery("SELECT * FROM {{table}} WHERE id=".$id." LIMIT 1", "poll");
	$poll = mysql_fetch_assoc($query);
	if ($poll['type'] == 2) { header("Location: poll.php"); die(); }
	if ($poll['type'] == 3) { header("Location: poll.php?do=archive:".$poll['id']); die(); }

	$query = doquery("SELECT * FROM {{table}} WHERE parent=".$poll['id'], "poll");
	$total_votes = mysql_num_rows($query);
	while ($avote = mysql_fetch_assoc($query)) {
		if ($avote['ans1'] == '1')
			$vote_1++;
		if ($avote['ans2'] == '1')
			$vote_2++;
		if ($avote['ans3'] == '1')
			$vote_3++;
		if ($avote['ans4'] == '1')
			$vote_4++;
	}
	$page = "<table width='100%' border='1'><tr><td class='title'>Open Poll</td></tr></table><p>";
	$page .= '<table cellspacing="2" cellpadding="0" width="100%"><tr><td colspan="3"><div style="TEXT-ALIGN: center"><b>'.$poll['question'].'</b></div></td></tr>';
	$query = doquery("SELECT * FROM {{table}} WHERE type=2 AND voter=".$userrow['id']." AND parent=".$poll['id']." LIMIT 1", "poll");
	if (!mysql_num_rows($query)) {
		if (!empty($poll['ans1']))
			$page .= '<tr><td>'.$poll['ans1'].'</td><td><a href="poll.php?do=vote:'.$poll['id'].':1">Vote</a></td></tr>';
		if (!empty($poll['ans2']))
			$page .= '<tr><td>'.$poll['ans2'].'</td><td><a href="poll.php?do=vote:'.$poll['id'].':2">Vote</a></td></tr>';
		if (!empty($poll['ans3']))
			$page .= '<tr><td>'.$poll['ans3'].'</td><td><a href="poll.php?do=vote:'.$poll['id'].':3">Vote</a></td></tr>';
		if (!empty($poll['ans4']))
			$page .= '<tr><td>'.$poll['ans4'].'</td><td><a href="poll.php?do=vote:'.$poll['id'].':4">Vote</a></td></tr>';
	} else {
		$page .= '<tr><td><b>Answer</b></td><td><b>Votes</b></td><td><b>Percentage</b></td></tr>';
		if (!empty($poll['ans1'])) {
			$percent = ($total_votes > 0) ? round((($vote_1/$total_votes) * 100), 0) : 0;
			$page .= '<tr><td>'.$poll['ans1'].'</td><td>'.$vote_1.'</td><td>'.$percent.'%</td></tr>';
			$page .= '<td style="padding:0px; width:300px; height:5px; border:solid 1px black; vertical-align:bottom;"><div style="padding:0px; width:'.(($percent / 100) * 300).'px; border-top:solid 1px black; background-image:url(images/bars_red.gif);"><img src="images/bars_red.gif" alt="" /></div></td></tr>';
		}
		if (!empty($poll['ans2'])) {
			$percent = ($total_votes > 0) ? round((($vote_2/$total_votes) * 100), 0) : 0;
			$page .= '<tr><td>'.$poll['ans2'].'</td><td>'.$vote_2.'</td><td>'.$percent.'%</td></tr>';
			$page .= '<td style="padding:0px; width:300px; height:5px; border:solid 1px black; vertical-align:bottom;"><div style="padding:0px; width:'.(($percent / 100) * 300).'px; border-top:solid 1px black; background-image:url(images/bars_red.gif);"><img src="images/bars_red.gif" alt="" /></div></td></tr>';
		}
		if (!empty($poll['ans3'])) {
			$percent = ($total_votes > 0) ? round((($vote_3/$total_votes) * 100), 0) : 0;
			$page .= '<tr><td>'.$poll['ans3'].'</td><td>'.$vote_3.'</td><td>'.$percent.'%</td></tr>';
			$page .= '<td style="padding:0px; width:300px; height:5px; border:solid 1px black; vertical-align:bottom;"><div style="padding:0px; width:'.(($percent / 100) * 300).'px; border-top:solid 1px black; background-image:url(images/bars_red.gif);"><img src="images/bars_red.gif" alt="" /></div></td></tr>';
		}
		if (!empty($poll['ans4'])) {
			$percent = ($total_votes > 0) ? round((($vote_4/$total_votes) * 100), 0) : 0;
			$page .= '<tr><td>'.$poll['ans4'].'</td><td>'.$vote_4.'</td><td>'.$percent.'%</td></tr>';
			$page .= '<td style="padding:0px; width:300px; height:5px; border:solid 1px black; vertical-align:bottom;"><div style="padding:0px; width:'.(($percent / 100) * 300).'px; border-top:solid 1px black; background-image:url(images/bars_red.gif);"><img src="images/bars_red.gif" alt="" /></div></td></tr>';
		}
		$page .= '<tr><td><b>Totals</b></td><td><b>'.$total_votes.'</b></td></tr>';
	}
	$page .= '</table><br><br>You may <a href=index.php>return</a> to what you were doing, or use the compass on the right to start exploring.';
	$title .= $poll['question'];
	display($page, $title);

}

function vote($id, $option) {
	global $userrow, $title;
	$id = intval($id);
	$option = intval($option);

	$query = doquery("SELECT * FROM {{table}} WHERE id=".$id." LIMIT 1", "poll");
	$poll = mysql_fetch_assoc($query);
	if ($poll['type'] == 3) { header("Location: poll.php?do=view:".$poll['id']); die(); }
	if ($poll['type'] == 2) { header("Location: poll.php"); die(); }

	$query = doquery("SELECT * FROM {{table}} WHERE parent=".$id." AND voter=".$userrow['id']." LIMIT 1", "poll");
	if (mysql_num_rows($query))
		display("You have already voted in that poll!", "Poll - Error");
	if ($option == 1)
		$action = "ans1='1', ans2='', ans3='', ans4=''";
	if ($option == 2)
		$action = "ans1='', ans2='1', ans3='', ans4=''";
	if ($option == 3)
		$action = "ans1='', ans2='', ans3='1', ans4=''";
	if ($option == 4)
		$action = "ans1='', ans2='', ans3='', ans4='1'";
	$action .= ', voter='.$userrow['id'].', parent='.$id;
	doquery("INSERT INTO {{table}} SET ".$action." ", "poll");
	$updatequery = doquery("UPDATE {{table}} SET poll='Voted' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	header("Location: poll.php?do=view:".$id);

}


function archive($id) {
	global $userrow, $title;
	$id = intval($id);
	$vote_1 = 0;
	$vote_2 = 0;
	$vote_3 = 0;
	$vote_4 = 0;
	$query = doquery("SELECT * FROM {{table}} WHERE id=".$id." LIMIT 1", "poll");
	$poll = mysql_fetch_assoc($query);
	if ($poll['type'] == 1) { header("Location: poll.php?do=view:".$poll['id']); die(); }
	if ($poll['type'] == 2) { header("Location: poll.php"); die(); }

	$votes = explode(':', $poll['closed_cache']);
	$total_votes = $votes[0] + $votes[1] + $votes[2] + $votes[3];
	$vote_1 = $votes[0];
	$vote_2 = $votes[1];
	$vote_3 = $votes[2];
	$vote_4 = $votes[3];
	$page = "<table width='100%' border='1'><tr><td class='title'>Closed Poll</td></tr></table><p>";
	$page .= '<table cellspacing="2" cellpadding="0" width="100%"><tr><td colspan="3"><div style="TEXT-ALIGN: center"><b>'.$poll['question'].'</b></div></td></tr>';
	$page .= '<tr><td><b>Answer</b></td><td><b>Votes</b></td><td><b>Percentage</b></td></tr>';
	if (!empty($poll['ans1'])) {
		$percent = ($total_votes > 0) ? round((($vote_1/$total_votes) * 100), 0) : 0;
		$page .= '<tr><td>'.$poll['ans1'].'</td><td>'.$vote_1.'</td><td>'.$percent.'%</td></tr>';
		$page .= '<td style="padding:0px; width:300px; height:5px; border:solid 1px black; vertical-align:bottom;"><div style="padding:0px; width:'.(($percent / 100) * 300).'px; border-top:solid 1px black; background-image:url(images/bars_red.gif);"><img src="images/bars_red.gif" alt="" /></div></td></tr>';
	}
	if (!empty($poll['ans2'])) {
		$percent = ($total_votes > 0) ? round((($vote_2/$total_votes) * 100), 0) : 0;
		$page .= '<tr><td>'.$poll['ans2'].'</td><td>'.$vote_2.'</td><td>'.$percent.'%</td></tr>';
		$page .= '<td style="padding:0px; width:300px; height:5px; border:solid 1px black; vertical-align:bottom;"><div style="padding:0px; width:'.(($percent / 100) * 300).'px; border-top:solid 1px black; background-image:url(images/bars_red.gif);"><img src="images/bars_red.gif" alt="" /></div></td></tr>';
	}
	if (!empty($poll['ans3'])) {
		$percent = ($total_votes > 0) ? round((($vote_3/$total_votes) * 100), 0) : 0;
		$page .= '<tr><td>'.$poll['ans3'].'</td><td>'.$vote_3.'</td><td>'.$percent.'%</td></tr>';
		$page .= '<td style="padding:0px; width:300px; height:5px; border:solid 1px black; vertical-align:bottom;"><div style="padding:0px; width:'.(($percent / 100) * 300).'px; border-top:solid 1px black; background-image:url(images/bars_red.gif);"><img src="images/bars_red.gif" alt="" /></div></td></tr>';
	}
	if (!empty($poll['ans4'])) {
		$percent = ($total_votes > 0) ? round((($vote_4/$total_votes) * 100), 0) : 0;
		$page .= '<tr><td>'.$poll['ans4'].'</td><td>'.$vote_4.'</td><td>'.$percent.'%</td></tr>';
		$page .= '<td style="padding:0px; width:300px; height:5px; border:solid 1px black; vertical-align:bottom;"><div style="padding:0px; width:'.(($percent / 100) * 300).'px; border-top:solid 1px black; background-image:url(images/bars_red.gif);"><img src="images/bars_red.gif" alt="" /></div></td></tr>';
	}
	$page .= '<tr><td><b>Totals</b></td><td><b>'.$total_votes.'</b></td></tr>';

	$page .= '</table>';
	$page .= '<br><br>You may <a href=index.php>return</a> to what you were doing, or use the compass on the right to start exploring.';
	$title .= $poll['question'];
	display($page, $title);

}

function admin($section) {
	global $userrow, $title;

	if ($userrow["authlevel"] != 1) { die("You must have administrator privileges to use the Poll control panel."); }

	if ($section == 'addpoll') {
		$title .= 'Add a poll';
		if (isset($_POST['submit'])) {
			$question = htmlspecialchars($_POST['question']);
			$ans1 = htmlspecialchars($_POST['ans1']);
			$ans2 = htmlspecialchars($_POST['ans2']);
			$ans3 = htmlspecialchars($_POST['ans3']);
			$ans4 = htmlspecialchars($_POST['ans4']);
			doquery("INSERT INTO {{table}} SET type=1, question='".$question."', ans1='".$ans1."', ans2='".$ans2."', ans3='".$ans3."', ans4='".$ans4."', open_date=NOW(), voter=".$userrow['id'], "poll");
			header("Location: poll.php");
			die();
		} else {
			$page = "<table width='100%' border='1'><tr><td class='title'>Add a Poll</td></tr></table><p>";
			$page .= '<form action="poll.php?do=admin:addpoll" method="POST"><table>';
			$page .= '<tr><td>Question</td><td><input type="text" name="question" size="50" /></td></tr>';
			$page .= '<tr><td>Answer 1</td><td><input type="text" name="ans1" size="50" /></td></tr>';
			$page .= '<tr><td>Answer 2</td><td><input type="text" name="ans2" size="50" /></td></tr>';
			$page .= '<tr><td>Answer 3</td><td><input type="text" name="ans3" size="50" /></td></tr>';
			$page .= '<tr><td>Answer 4</td><td><input type="text" name="ans4" size="50" /></td></tr>';
			$page .= '<tr><td>Submit</td><td><input type="submit" name="submit" value="Create Poll" /></td></tr>';
			$page .= '</table></form>';
		}
	}
	if ($section == 'closepoll') {
		$title .= 'Close polls';
		if (isset($_POST['submit'])) {
			foreach($_POST as $a => $b) {
				if ($a != "submit") {
					$query = doquery("SELECT * FROM {{table}} WHERE id={$a} LIMIT 1", "poll");
					$poll = mysql_fetch_assoc($query);

					$query = doquery("SELECT * FROM {{table}} WHERE parent=".$poll['id'], "poll");
					$total_votes = mysql_num_rows($query);
					$vote_1 = 0;
					$vote_2 = 0;
					$vote_3 = 0;
					$vote_4 = 0;
					while ($avote = mysql_fetch_assoc($query)) {
						if ($avote['ans1'] == '1')
							$vote_1++;
						if ($avote['ans2'] == '1')
							$vote_2++;
						if ($avote['ans3'] == '1')
							$vote_3++;
						if ($avote['ans4'] == '1')
							$vote_4++;
					}
					$vote_cache = $vote_1.':'.$vote_2.':'.$vote_3.':'.$vote_4;
					doquery("UPDATE {{table}} SET type=3, closed_date=NOW(), closed_cache='".$vote_cache."' WHERE id={$a}", "poll");
					doquery("DELETE FROM {{table}} WHERE parent={$a}", "poll");
                    header("Location: poll.php");
		                    	die();
				}
			}
		} else {
			$query = doquery("SELECT * FROM {{table}} WHERE type=1", "poll");
			$page = "<table width='100%' border='1'><tr><td class='title'>Close Poll</td></tr></table><p>";
			$page .= '<form action="poll.php?do=admin:closepoll" method="post"><table>';
			$page .= '<tr><td>Poll</td><td>Close?</td></tr>';
			while ($poll = mysql_fetch_assoc($query))
				$page .= '<tr><td>'.$poll['question'].'</td><td><input type="checkbox" name="'.$poll['id'].'" value="yes" /></td></tr>';
			$page .= '<tr><td>Submit</td><td><input type="submit" name="submit" value="Close" /></td></tr>';
			$page .= '</table></form>';
		}
	}
	display($page, $title);
}
?>