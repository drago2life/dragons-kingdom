<?php // pvp.php :: Handles all PvP fighting action.

$goldstake = 50; // Gold per level that winner will recive.
$expstake = 0.001; // EXP of of the total of opponets the winner will recive.
$refreshrate = 5; // Time (in seconds) between refreshes, when waiting for players.
$timelimit = 90; // The limit (in seconds) that a player has to submit an action.
$scalemod = 2; // The maximum number of scales the winner can recive is multiplied by this number.

function arena()	{ // Gives people a place to find and fight other players (index.php?do=arena)
/*	$query = "SELECT * FROM dk_duel";

	$result = mysql_query($query)	or die(mysql_error());

				echo mysql_num_rows($result) . " rows in set.<br />
				<table class='tbl' cellspacing='0'><tr>";

				while ($mf = mysql_fetch_field($result))	{
					echo "<th>$mf->name</th>";
				}

				while ($ma = mysql_fetch_array($result))	{
					echo "</tr><tr>";
					for ($i = 0; $i < mysql_num_fields($result); $i++)	{
						echo "<td>$ma[$i]</td>";
					}
				}

				echo "</tr></table>";
*/
	global $userrow, $goldstake, $expstake;

$updatequery = doquery("UPDATE {{table}} SET location='Duel Arena' WHERE id='".$userrow["id"]."' LIMIT 1", "users");

	$title = "Duel Arena";

	$gold = $goldstake * $userrow[level];
	$page = "<table width='100%' border='1'><tr><td class='title'>Duel Arena</td></tr></table><p>";
	$page .= "Welcome to the Duel Arena.  <p>Here you can find and wait for people to challenge to a Player vs. Player duel!
	If you win the duel, you will earn a small amount of Experience, a few Dragon Scales, and you will also receive a Gold reward.  <p>The amount of Gold and Experience you will get is stated by the player's name.  However, if you lose, you will lose $gold gold, and your health will be depleted. <p>You may switch off Duel Requests from your player options link, which will remove you from this list.<p>You must have $gold gold on hand to duel.<br />
	<br />
	Users online in the last 60 seconds: (Click on their name to duel them)<br />";

	if ($userrow[gold] >= $gold)	{
		$query = "SELECT id, charname, level, gold, experience, guildname FROM dk_users
			  WHERE UNIX_TIMESTAMP(onlinetime) >= '".(time()-60)."'
			  AND id != '$userrow[id]'
			  AND authlevel != '1'
		      AND duellist != '0'
			  ORDER BY charname";

		$result = mysql_query($query);

		if (mysql_num_rows($result) > 0) { $page .= "<ul>"; }

		while ($ma = mysql_fetch_array($result))	{
				$wingold = $ma[level] * $goldstake;
				if ($wingold > $ma[gold])	{
					$wingold = $ma[gold];
				}

				$winexp = floor($ma[experience] * $expstake);

				$page .= "<li><a href='index.php?do=startduel&id=$ma[id]'>$ma[charname]</a> (Level: $ma[level], Gold: $wingold, Experience: $winexp, Guild: $ma[guildname])</li>";
		}
		$page .= "</ul>";
		if (mysql_num_rows($result) < 1)	{
			$page .= "<p><font color='red'>There is currently no one online to duel, or they have disallowed duel requests from their options menu.</font><br /><br />";
		}
		else { $page .= "</ul>"; }
	}
	else	{
		$page .= "<font color='red'>You must have at least <b>$gold</b> Gold on hand to duel!</font><br /><br />";
	}

	$page .= "<p><font color='red'>Note: All duels are logged. If you are caught cheating, or trying to take advantage, you will be banned immediately.</font><br /><br />You may return to <A href='index.php'>town</a> if you have changed your mind or continue exploring using the compass image on the right.";

	display($page, $title);
}

function startduel() { // Registers the duel in the database
	global $userrow, $goldstake;

	if (empty($_GET[id]))	{
		header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "" . "index.php");
	}

	$gold = $goldstake * $userrow[level];
	if ($userrow[gold] < $gold)	{
				$title = "Duel Error";
		$page = "<table width='100%' border='1'><tr><td class='title'>Duel Arena</td></tr></table><p>";
		$page = "You can not currently challenge that player because you must have at least $gold Gold in your hand.  You may continue exploring by using the compass images to the right.";
		display($page, $title);
		exit;
	}

	$query = "SELECT duelid FROM dk_duel
		  WHERE (player1id = '$_GET[id]'
		  AND player1done != 1)
		  OR (player2id = '$_GET[id]'
		  AND player2done != 1)";

	$result = mysql_query($query);

	if (mysql_num_rows($result) > 0)	{
		$title = "Duel Error";
		$page = "<table width='100%' border='1'><tr><td class='title'>Duel Arena</td></tr></table><p>";
		$page = "The player you are challenging is already in a duel.  You can not currently challenge that player.  You may return to the <a href='index.php?do=arena'>Duel Arena</a> or use the compass to start exploring.";
		display($page, $title);
		exit;
	}

	$query = "SELECT id, charname, maxhp, maxmp, ipaddress FROM dk_users
		  WHERE id = '$_GET[id]'
		  AND UNIX_TIMESTAMP(onlinetime) >= '".(time()-180)."'";
	$result = mysql_query($query);

	if (mysql_num_rows($result) != 1)	{
		$title = "Duel Error";
	    $page = "<table width='100%' border='1'><tr><td class='title'>Duel Arena</td></tr></table><p>";
		$page = "Either a character with the id '$_GET[id]' doesn't exist or that player isn't online. There might be an error with your account. You may return to the <a href='index.php?do=arena'>Duel Arena</a> or use the compass to start exploring.";
		display($page, $title);
	}
	else	{
		$ma = mysql_fetch_array($result);
	}

	if ($ma[ipaddress] == $_SERVER["REMOTE_ADDR"])	{
	    $page = "<table width='100%' border='1'><tr><td class='title'>Duel Arena</td></tr></table><p>";
		$page = "Error: You can't duel people that have the same IP address as yours.  This is to prevent cheating.";
		display($page, "Duel Error");
	}

	$datetime = date("Y-m-d H:i:s");

	$query = "INSERT INTO dk_duel VALUES
		 ('', '1', '$userrow[id]', '$userrow[maxhp]', '$userrow[maxmp]', '$datetime', '', '', '', '', '0', '0', '0,0', '0', '$ma[id]', '$ma[maxhp]', '$ma[maxmp]', '', '', '', '', '', '0', '0', '0,0', '0')";
	mysql_query($query)	or die(mysql_error());

	header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "" . "index.php?do=waitforduel&charname=$ma[charname]");
	exit;
}

function waitforduel() { // A duel waiting screen that automaticly refreshes every 10 seconds.
	global $userrow, $refreshrate;

	if ($_GET['cancel'] == 1)	{
		$query = "DELETE FROM dk_duel
			  WHERE player1id = '$userrow[id]' ";
		mysql_query($query);

		$title = "Duel Cancelled";
		$page = "<table width='100%' border='1'><tr><td class='title'>Duel Arena</td></tr></table><p>";
		$page = "You have cancelled the duel.  You may return to the <a href='index.php?do=arena'>Duel Arena</a>, or use the compass to start exploring.";

		display($page, $title);
		exit;
	}

	$query = "SELECT * FROM dk_duel
		  WHERE player1id = '$userrow[id]' ";
	$result = mysql_query($query);

	$ma = mysql_fetch_array($result);

	$datetime = date("Y-m-d H:i:s");

	$currenttimestamp = time();
	$startedtimestamp = strtotime("$ma[player1lr]");
	$timeout = $startedtimestamp + 60;

	if ($currenttimestamp > $timeout)	{
		$to = 1;
	}

	if ($ma[duelstatus] == 1 && $to != 1 )	{
		$title = "Waiting for Opponent";
		$page = "<table width='100%' border='1'><tr><td class='title'>Duel Arena</td></tr></table><p>";
		$page = "A duel challenge has been sent to $_GET[charname].  That player must accept the duel challenge within 1 minute for the duel to start.  Please wait...<br /><br />If you no longer want to duel, you can <a href='index.php?do=waitforduel&amp;cancel=1'>cancel</a> the duel challenge.";

		header("Refresh: $refreshrate; URL=http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "" . "index.php?do=waitforduel&charname=$_GET[charname]");

		display($page, $title);
		exit;
	}
	elseif ($ma[duelstatus] == 1 && $to == 1 )	{
		$query = "DELETE FROM dk_duel
			  WHERE player1id = '$userrow[id]' ";
		mysql_query($query);

		$title = "Challenge Timed Out";
		$page = "<table width='100%' border='1'><tr><td class='title'>Duel Arena</td></tr></table><p>";
		$page = "$_GET[charname] has not responded to your challenge within 1 minute.  The challenge has timed out, and is therefore cancelled.  You may return to the <a href='index.php?do=arena'>Duel Arena</a> or use the compass to start exploring.";

		display($page, $title);
		exit;
	}
	elseif ($ma[duelstatus] == 2)	{
		$query = "DELETE FROM dk_duel
			  WHERE player1id = '$userrow[id]' ";
		mysql_query($query);

		$title = "Duel Declined";
		$page = "$_GET[charname] has declined your duel challenge.  You may return to the <a href='index.php?do=arena'>Duel Arena</a> or use the compass to start exploring.";

		display($page, $title);
		exit;
	}
	elseif ($ma[duelstatus] == 3)	{
		header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "" . "index.php?do=duel");
		exit;
	}
}

function acceptduel() { // The challenged player signles that (s)he accepts the duel
	global $userrow;

	$datetime = date("Y-m-d H:i:s");

	$query = "SELECT * FROM dk_duel
		  WHERE player2id = '$userrow[id]' ";
	$result = mysql_query($query);

	if (mysql_num_rows($result) != 1)	{
		$title = "Duel Error";
		$page = "<table width='100%' border='1'><tr><td class='title'>Duel Arena</td></tr></table><p>";
		$page = "This duel challenge is no longer active.  It may have been cancelled, or it may have timed-out.  You may return to what you were <a href='index.php'>doing</a>, or use the compass to start exploring.";

		display($page, $title);
		exit;
	}
	else	{
		$query = "UPDATE dk_duel
			  SET duelstatus = 3,
			      player2lr = '$datetime'
			  WHERE player2id = '$userrow[id]' ";
		mysql_query($query)	or die(mysql_error());

		header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "" . "index.php?do=duel");
	}
}

function declineduel() { // The challenged player signles that (s)he does not accept the duel
	global $userrow;

	$datetime = date("Y-m-d H:i:s");

	$query = "UPDATE dk_duel
		  SET duelstatus = 2,
		      player2lr = '$datetime'
		  WHERE player2id = '$userrow[id]' ";
	mysql_query($query);

	header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "" . "index.php");
}

function duel()	{ // Does the PvP duel
#####################################
	//DO STARTING STUFF

	global $userrow, $timelimit, $refreshrate, $goldstake, $expstake, $scalemod;

	$datetime = date("Y-m-d H:i:s");

	$query = "SELECT player1id FROM dk_duel
		  WHERE (player1id = '$userrow[id]'
		  OR player2id = '$userrow[id]')
		  AND duelstatus = '3'
		  ORDER BY player1lr DESC
		  LIMIT 0, 1 ";
	$result = mysql_query($query)	or die(mysql_error());

	$ma = mysql_fetch_array($result);

	if ($ma[player1id] == $userrow[id])	{
		$x = 1;
		$y = 2;
	}
	else	{
		$x = 2;
		$y = 1;
	}

	$query = "SELECT dk_duel.*, dk_users.* FROM dk_duel, dk_users
		  WHERE dk_duel.player{$y}id = dk_users.id
		  AND player{$x}id = '$userrow[id]'
		  AND duelstatus = '3'
		  AND player{$x}done != '1' ";
	$result = mysql_query($query);

	if (mysql_num_rows($result) < 1)	{
		$title = "Duel Error";
		$page = "<table width='100%' border='1'><tr><td class='title'>Duel Arena</td></tr></table><p>";
		$page = "This duel doesn't exist!  An error may have occured, or the duel may be over.  You may return to the <a href='index.php?do=arena'>Duel Arena</a> or use the compass to start exploring.";

		display($page, $title);
		exit;
	}
	else	{
		$ma = mysql_fetch_array($result);
	}

	//END
#####################################
	//DEFINE VARS

	$pxid = "player{$x}id";
	$pyid = "player{$y}id";

	$pxhp = "player{$x}hp";
	$pyhp = "player{$y}hp";

	$pxmp = "player{$x}mp";
	$pymp = "player{$y}mp";

	$pxlr = "player{$x}lr";
	$pylr = "player{$y}lr";

	$pxla = "player{$x}la";
	$pyla = "player{$y}la";

	$pxls = "player{$x}ls";
	$pyls = "player{$y}ls";

	$pxpa = "player{$x}pa";
	$pypa = "player{$y}pa";

	$pxps = "player{$x}ps";
	$pyps = "player{$y}ps";

	$pxta = "player{$x}ta";
	$pyta = "player{$y}ta";

	$pxse = "player{$x}se";
	$pyse = "player{$y}se";

	$pxex = "player{$x}ex";
	$pyex = "player{$y}ex";

	$pxdone = "player{$x}done";
	$pydone = "player{$y}done";


	$currenttimestamp = strtotime($datetime);

	$xstartedtimestamp = strtotime($ma[$pxlr]);
	$xtimeout = $xstartedtimestamp + $timelimit;

	$ystartedtimestamp = strtotime($ma[$pylr]);
	$ytimeout = $ystartedtimestamp + $timelimit;

	//END
#####################################
	//DO BATTLE

	$bonus = explode(",", "$ma[$pxex]");

	$userrow[attackpower] += $bonus[0];
	$userrow[defensepower] += $bonus[1];

	$bns = explode(",", "$ma[$pyex]");

	$ma[attackpower] += $bns[0];
	$ma[defensepower] += $bns[1];

	if ($ma[duelstatus] == '3')	{
		if ($_POST['action'] == "Continue to Fight" || $_POST['action'] == "Cast Spell" || $_POST['action'] == "Concede" || $_POST['action'] == "Continue...")	{
			$ystatus = $ma[$pyse];
			$xstatus = $ma[$pxse];
			if ($_POST['action'] == "Continue to Fight")	{
				$la = 1;
				$dodgerand = rand(1, 70);
				if (sqrt($ma[dexterity]) < $dodgerand)	{
					$attackrand = rand(3, 10) / 10;
					$defendrand = rand(3, 10) / 10;

					$attack = $userrow[attackpower] * $attackrand;
					$defense = $ma[defensepower] * $defendrand;

					$xdamage = $attack - $defense;

					if ($xdamage < 1)	{
						$xdamage = 1;
						$ls = 2;
					}
					else	{
						$criticalrand = rand(1, 100);
						if (sqrt($userrow[strength]) > $criticalrand)	{
							$critrand = rand(20, 30) / 10;
							$xdamage *= $critrand;
							$ls = 3;
						}
						else	{
							$ls = 1;
						}
					}
				}
				else	{
					$ls = 4;
				}
				$xdamage = floor($xdamage);
				$ls .= ",$xdamage";
			}
			elseif ($_POST['action'] == "Cast Spell")	{
				$la = 2;
				$ls = $_POST['ls'];

				$query = "SELECT * FROM dk_spells
					  WHERE id = '$_POST[ls]' ";
				$result = mysql_query($query);
				$sma = mysql_fetch_array($result);

				if ($sma[type] == '1')	{
					$fail = rand(1, 5);
					if ($fail == 1)	{
						$ls = 0;
					}
					else	{
						$xrecover = $sma[attribute];
					}
				}
				elseif ($sma[type] == '2')	{
					$fail = rand(1, 5);
					if ($fail == 1)	{
						$ls = 0;
					}
					else	{
						$xdamage = $sma[attribute];
                        			$xdamage = $xdamage / 3;
                        			$xdamage = floor($xdamage);
					}
				}
				elseif ($sma[type] == '3')	{
					$fail = rand(1, 3);
					if ($fail == 1)	{
						$ls = 0;
					}
					else	{
						$ystatus = "1,".$sma[attribute];
					}
				}
				elseif ($sma[type] == '4')	{
					$fail = rand(1, 4);
					if ($fail == 1)	{
						$ls = 0;
					}
					else	{
						$bonus[0] = $sma[attribute];
					}
				}
				elseif ($sma[type] == '5')	{
					$fail = rand(1, 4);
					if ($fail == 1)	{
						$ls = 0;
					}
					else	{
						$bonus[1] = $sma[attribute];
					}
				}
				$xmpused = $sma[mp];
			}
			elseif ($_POST['action'] == "Concede")	{
				$la = 3;
				$theywin = 1;
			}
			elseif ($_POST['action'] == "Continue...")	{
				$sleeprand = rand(1, 20);
				$sleeper = explode(",", $ma[$pxse]);
				if ($sleeprand > $sleeper[1])	{
					$la = 4;
					$xstatus = 0;
				}
				else	{
					$la = 4;
					$xstatus = "1,".$sleeper[1];
				}
			}

			$query = "UPDATE dk_duel
				  SET player{$x}pa = '$ma[$pxla]',
				      player{$x}ps = '$ma[$pxls]',
				      player{$x}la = '$la',
				      player{$x}ls = '$ls',
				      player{$x}lr = '$datetime',
				      player{$x}ta = player{$x}ta + 1,
				      player{$x}hp = player{$x}hp + '$xrecover',
				      player{$y}hp = player{$y}hp - '$xdamage',
				      player{$x}mp = player{$x}mp - '$xmpused',
				      player{$x}se = '$xstatus',
				      player{$y}se = '$ystatus',
				      player{$x}ex = '$bonus[0], $bonus[1]'
				  WHERE duelid = '$ma[duelid]' ";
			mysql_query($query)	or die(mysql_error());

			header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "" . "index.php?do=duel");
			exit;
		}
	}

	//END
#####################################
	//DISPLAY

	if ($ma[$pxta] == '0')	{
		$title = "Duel Begins!";
		$page = "<table width='100%' border='1'><tr><td class='title'>Duel Arena</td></tr></table><p>";
		$page = "The duel between you and $ma[charname] has begun!  Select what you would like to do within the next $timelimit seconds.<br /><br />";
	}
	elseif ($ma[$pxta] > $ma[$pyta])	{
		if ($currenttimestamp > $ytimeout)	{
			$title = "Oppoent Gone!";
			$page .= "<table width='100%' border='1'><tr><td class='title'>Duel Arena</td></tr></table><p>";
			$page .= "Your opponent didn't submit an action within $timelimit seconds, therefore they forfeit.<br /><br />";
			$youwin = 1;

			$query = "UPDATE dk_duel
				  SET player{$x}la = '5'
				  WHERE duelid = '$ma[duelid]' ";
			mysql_query($query)	or die(mysql_error());
		}
		else	{
			$title = "Waiting for Opponent";
			$page = "<table width='100%' border='1'><tr><td class='title'>Duel Arena</td></tr></table><p>";
			$page = "Waiting for Opponent to submit an action. If the other player does not submit an action quick enough, you will automatically win.";
			Header("Refresh: $refreshrate; URL=http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "" . "index.php?do=duel");
			Display($page, $title);
		}
	}
	elseif ($ma[$pyla] == '5')	{
		$title = "Time's Up!";
		$page .= "<table width='100%' border='1'><tr><td class='title'>Duel Arena</td></tr></table><p>";
		$page .= "You didn't submit an action within $timelimit seconds!  You have forfeited the duel!<br /><br />";
		$theywin = 1;
	}
	else	{
		if ($ma[$pxta] < $ma[$pyta])	{
			$playeryla = $ma[$pypa];
			$playeryls = $ma[$pyps];
		}
		else	{
			$playeryla = $ma[$pyla];
			$playeryls = $ma[$pyls];
		}

		//Your Blow...
		$title = "Dueling!";
		if ($ma[$pxla] == '1' && $playeryla != '3')	{
			$atk = explode(",", $ma[$pxls]);
			if ($atk[0] == 1)	{
				$page .= "You attack $ma[charname] for $atk[1] damage!<br /><br />";
			}
			elseif ($atk[0] == 2)	{
				$page .= "<font color=green>You attack $ma[charname], but you only scratched their armor, dealing 1 damage!</font><br /><br />";
			}
			elseif ($atk[0] == 3)	{
				$page .= "<font color=green>You attack $ma[charname] landing an <b>Excellent Hit</b>, dealing $atk[1] damage!</font><br /><br />";
			}
			elseif ($atk[0] == 4)	{
				$page .= "<font color=red>$ma[charname] dodges your attack!</font><br /><br />";
			}
			else	{
				$page .= "You attempt to attack $ma[charname], but a rapid chicken gets in you way, breaking your concentration, causing you to trip!  I think an error has occured!<br /><br />";
			}
		}
		elseif ($ma[$pxla] == '2' && $playeryla != '3')	{
			if ($ma[$pxls] == 0)	{
				$page.= "<font color='red'>You failed casting a spell!</font><br /><br />";
			}
			else	{
				$query = "SELECT * FROM dk_spells
					  WHERE id = '$ma[$pxls]' ";
				$result = mysql_query($query);
				$sma = mysql_fetch_array($result);

				if ($sma[type] == '1')	{
					$page .= "<font color=blue>You cast $sma[name] and gain $sma[attribute] health.</font><br /><br />";
				}
				elseif ($sma[type] == '2')	{
					$dmg = $sma[attribute] / 3;
					$dmg = floor($dmg);
					$page .= "<font color=blue>You cast $sma[name] dealing $dmg damage to $ma[charname]!</font><br /><br />";
				}
				elseif ($sma[type] == '3')	{
					$page .= "<font color=blue>You cast $sma[name], causing $ma[charname] to fall fast asleep!</font><br /><br />";
				}
				elseif ($sma[type] == '4')	{
					$page .= "<font color=blue>You cast $sma[name], increasing your attack by $sma[attribute]. If you cast another enhanced damage spell, it will not improve any further.</font><br /><br />";

				}
				elseif ($sma[type] == '5')	{
					$page .= "<font color=blue>You cast $sma[name], increasing your defense by $sma[attribute]. If you cast another enhanced defense spell, it will not improve any further.</font><br /><br />";

				}
			}
		}
		elseif ($ma[$pxla] == '3')	{
			$page .= "You concede to $ma[charname].<br /><br />";
		}
		elseif ($ma[$pxla] == '4' && $playeryla != '3')	{
			if ($ma[$pxse] == '0')	{
				$page .= "<font color=green>You wake up!</font><br /><br />";
			}
			else	{
				$page .= "<font color=red>You continue to sleep..</font><br /><br />";
				$dosleep = 1;
			}
		}

		//Their Blow...
		if ($playeryla == '1' && $ma[$pxla] != '3')	{
			$atk = explode(",", $playeryls);
			if ($atk[0] == 1)	{
				$page .= "<font color=red>$ma[charname] attacks you dealing $atk[1] damage!</font><br /><br />";
			}
			elseif ($atk[0] == 2)	{
				$page .= "<font color=red>$ma[charname] attacks you, but only scratched your armor, dealing 1 damage!</font><br /><br />";
			}
			elseif ($atk[0] == 3)	{
				$page .= "<font color=red>$ma[charname] attacks you landing an <b>Excellent Hit</b>, dealing $atk[1] damage!</font><br /><br />";
			}
			elseif ($atk[0] == 4)	{
				$page .= "<font color=green>You dodge $ma[charname]'s attack!</font><br /><br />";
			}
			else	{
				$page .= "$ma[charname] attacks you, but seems to have trouble getting around a chicken.  Foolish human!<br /><br />";
			}
			$ydamage = $atk[1];
		}
		elseif ($playeryla == '2' && $ma[$pxla] != '3')	{
			if ($playeryls == 0)	{
				$page.= "<font color='red'>$ma[charname] failed casting a spell!</font><br /><br />";
			}
			else	{
				$query = "SELECT * FROM dk_spells
					  WHERE id = '$playeryls' ";
				$result = mysql_query($query)	or die(mysql_error());
				$sma = mysql_fetch_array($result);

				if ($sma[type] == '1')	{
					$page .= "<font color=blue>$ma[charname] casts $sma[name] and gains $sma[attribute] health.</font><br /><br />";
					$yrecover = $sma[attribute];
				}
				elseif ($sma[type] == '2')	{
					$dmg = $sma[attribute] / 3;
					$dmg = floor($dmg);
					$page .= "<font color=blue>$ma[charname] casts $sma[name], dealing $dmg damage to you!</font><br /><br />";
					$ydamage = $dmg;
				}
				elseif ($sma[type] == '3')	{
					$page .= "<font color=blue>$ma[charname] casts $sma[name], causing you to fall fast asleep!</font><br /><br />";
					$dosleep = 1;
				}
				elseif ($sma[type] == '4')	{
					$page .= "<font color=blue>$ma[charname] casts $sma[name], increasing their attack by $sma[attribute]</font><br /><br />";
				}
				elseif ($sma[type] == '5')	{
					$page .= "<font color=blue>$ma[charname] casts $sma[name], increasing their defense by $sma[attribute]</font><br /><br />";
				}
			}
			$ympused = $sma[mp];
		}
		elseif ($playeryla == '3' && $ma[$pxla] != '3')	{
			$page .= "<font color=green>$ma[charname] concedes to you!</font><br /><br />";
		}
		elseif ($playeryla == '3' && $ma[$pxla] == '3')	{
			$page .= "<font color=green>$ma[charname] also chooses to concede.</font><br /><br />";
		}
		elseif ($playeryla == '4' && $ma[$pxla] != '3')	{
			if ($ma[$pyse] == '0')	{
				$page .= "<font color=red>$ma[charname] wakes up!</font><br /><br />";
			}
			else	{
				$page .= "<font color=green>$ma[charname] continues to sleep..</font><br /><br />";
			}
		}
	}

	//END
#####################################
	//FIX HP

	if ($ma[$pxta] == $ma[$pyta])	{
		if ($ma[$pxhp] > $userrow[maxhp])	{
			$query = "UPDATE dk_duel
				  SET player{$x}hp = '$userrow[maxhp]' ";
			mysql_query($query)	or die(mysql_error());
		}
	}

	//END
#####################################
	//CHECK WIN

	if ($ma[$pxta] == $ma[$pyta])	{
		if ($ma[$pxhp] <= 0 || $ma[$pxla] == '3')	{
			$theywin = 1;
		}
		if ($ma[$pyhp] <= 0 || $ma[$pyla] == '3')	{
			$youwin = 1;
		}
	}

	//END
#####################################
	//DO WIN

	if ($youwin == 1 && $theywin != 1)	{
		$prizegold = $goldstake * $ma[level];
		$prizeexp = floor($expstake * $ma[experience]);
		if ($prizegold > $ma[gold])	{
			$prizegold = $ma[gold];
		}

		if ($ma[level] < 10) { $scaleint = 1; }
		elseif ($ma[level] > 9 && $ma[level] < 20) { $scaleint = 2; }
		elseif ($ma[level] > 19 && $ma[level] < 30) { $scaleint = 3; }
		elseif ($ma[level] > 29 && $ma[level] < 40) { $scaleint = 4; }
		elseif ($ma[level] > 39 && $ma[level] < 50) { $scaleint = 5; }
		elseif ($ma[level] > 49 && $ma[level] < 60) { $scaleint = 6; }
		elseif ($ma[level] > 59 && $ma[level] < 70) { $scaleint = 7; }
		elseif ($ma[level] > 69 && $ma[level] < 80) { $scaleint = 8; }
		elseif ($ma[level] > 79 && $ma[level] < 90) { $scaleint = 9; }
		elseif ($ma[level] > 89 && $ma[level] < 100) { $scaleint = 10; }
		elseif ($ma[level] > 99) { $scaleint = 10; }
		$maxscale = $scaleint * $scalemod;

		$prizescale = rand(1, $maxscale);
		$page .= "<font color=green>You have defeated $ma[charname] in a duel!  <p>You have gained $prizeexp experience, $prizegold gold, and $prizescale dragon scale(s)!</font>";

		if ($ma[$pxdone] != 1)	{
			$query = "UPDATE dk_users
				  SET numbattlewon  = numbattlewon + 1,
				      gold = gold + $prizegold,
				      dscales = dscales + $prizescale,
				      experience = experience + $prizeexp
				  WHERE id = '$userrow[id]' ";
			mysql_query($query)	or die(mysql_error());

			$query = "UPDATE dk_duel
				  SET player{$x}done = '1'
				  WHERE duelid = '$ma[duelid]' ";
			mysql_query($query);
		}
		if ($ma[$pydone] == 1)	{
			$query = "DELETE FROM dk_duel
				  WHERE duelid = '$ma[duelid]' ";
			mysql_query($query);
		}
	}
	elseif ($theywin == 1 && $youwin != 1)	{
		$prizegold = $goldstake * $userrow[level];

		if ($prizegold > $userrow[gold])	{
			$prizegold = $userrow[gold];
		}
	    $page .= "<table width='100%' border='1'><tr><td class='title'>Duel Arena</td></tr></table><p>";
		$page .= "<font color=red>You have been defeated by $ma[charname] in a duel!  You have lost $prizegold gold";

		if ($ma[$pxla] != 3)	{
			$page .=", and your HP has been depleted!</font>";
		}
		else	{
			$page .= "!";
		}

		if ($ma[$pxdone] != '1')	{
			if ($userrow['gold'] < $prizegold) {
				$loser_gold = $userrow['gold'];
			}
			else {
				$loser_gold = $prizegold;
			}

			$query = "UPDATE dk_users
				  SET numbattlelost = numbattlelost + 1,
				      gold = gold - $loser_gold
				  WHERE id = '$userrow[id]' ";
			mysql_query($query)	or die(mysql_error());

			if ($ma[pxla] != '3')	{
				$query = "UPDATE dk_users
					  SET currenthp = '0'
					  WHERE id = '$userrow[id]' ";
				mysql_query($query);
			}

			$query = "UPDATE dk_duel
				  SET player{$x}done = '1'
				  WHERE duelid = '$ma[duelid]' ";
			mysql_query($query);
		}
		if ($ma[$pydone] == 1)	{
			$query = "DELETE FROM dk_duel
				  WHERE duelid = '$ma[duelid]' ";
			mysql_query($query);
		}
	}
	elseif ($youwin == 1 && $theywin == 1)	{
		$page .= "<table width='100%' border='1'><tr><td class='title'>Duel Arena</td></tr></table><p>";
		$page .= "<font color=blue>Both players have been defeated in this duel!  It ends in a draw!</font>";

		if ($ma[$pydone] == 1)	{
			$query = "DELETE FROM dk_duel
				  WHERE duelid = '$ma[duelid]' ";
			mysql_query($query);
		}
		else	{
			$query = "UPDATE dk_duel
				  SET player{$x}done = '1'
				  WHERE duelid = '$ma[duelid]' ";
			mysql_query($query);
		}
	}

	if (($youwin == 1 || $theywin == 1) || ($youwin == 1 && $theywin == 1))	{
		if ($x == '1')	{
			$page .= "<br /><br />You may return to the <a href='index.php?do=arena'>Duel Arena</a>, or use the compass to start exploring.";
		}
		else	{
			$page .= "<br /><br />You may return to what you were <a href='index.php'>doing</a>, or use the compass to continue exploring.";
		}
	}

	//END
#####################################
	//SHOW ACTIONS

	else	{
		$magicmenu = "<select name='ls'>";

		$spells = explode(",", $userrow[spells]);

		if (isset($spells[0]))	{
			$query = "SELECT * FROM dk_spells
				  WHERE (type = '1' OR type = '2' OR type = '3' OR type='4' OR type='5')
				  AND mp <= '$ma[$pxmp]'
				  AND (";
			for ($i = 0; isset($spells[$i]); $i++)	{
				if ($i != 0)	{
					$query .= " OR ";
				}
				$query .= "id = '$spells[$i]'";
			}
			$query .= ")";
			$result = mysql_query($query)	or die(mysql_error() . " QUERY: $query");

			while ($sma = mysql_fetch_array($result))	{
				$magicmenu .= "<option value='$sma[id]'>$sma[name]</option>";
				$mm++;
			}
		}
		else	{
			$magicmenu .= "<option>You have no Spells</option>";
			$dis = "disabled='disabled'";
		}

		if ($mm < 1)	{
			$magicmenu .="<option>Not enough MP!</option>";
			$dis = "disabled='disabled'";
		}

		$magicmenu .= "</select>";

		$yourhppercent = $ma[$pxhp] / $userrow[maxhp] * 100;
		$yourmppercent = $ma[$pxmp] / $userrow[maxmp] * 100;
		$theirhppercent = $ma[$pyhp] / $ma[maxhp] * 100;
		$theirmppercent = $ma[$pymp] / $ma[maxmp] * 100;

		$yourhppercent = round($yourhppercent, 1);
		$yourmppercent = round($yourmppercent, 1);
		$theirhppercent = round($theirhppercent, 1);
		$theirmppercent = round($theirmppercent, 1);

		if ($ma[$pxta] != '0')	{
			$page .= "Your HP: $ma[$pxhp] / $userrow[maxhp] ({$yourhppercent}%)<br />
			Your MP: $ma[$pxmp] / $userrow[maxmp] ({$yourmppercent}%)<br />
			{$ma[charname]}'s HP: $ma[$pyhp] / $ma[maxhp] ({$theirhppercent}%)<br />
			{$ma[charname]}'s MP: $ma[$pymp] / $ma[maxmp] ({$theirmppercent}%)<br />
			<br />";
		}

		$page .= "What do you want to do?<br />
		<form action='index.php?do=duel' method='post'>";

		$se = explode(",", $ma[$pxse]);

		if ($se[0] == '1' && $dosleep = '1')	{
			$page .= "You are asleep!<br />
			<input type='submit' name='action' value='Continue...'><br />";
		}
		else	{
			$page .= "<input type='submit' name='action' value='Continue to Fight'><br />
			$magicmenu <input type='submit' name='action' value='Cast Spell' {$dis}><br />
			<input type='submit' name='action' value='Concede' disabled='disabled'>	<br />";
		}

		Header("Refresh: $timelimit; URL=http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "" . "index.php?do=duel");

	}

	//END
#####################################
	//DO THE DISPLAY

	display($page, $title);

	//END
#####################################
}

/*

CODED BY MARK SAUVERWALD FOR ADAM DEAR & DK-RPG.COM
E-mail: namadoor@gmail.com
Web Development by Auroria

*/

?>