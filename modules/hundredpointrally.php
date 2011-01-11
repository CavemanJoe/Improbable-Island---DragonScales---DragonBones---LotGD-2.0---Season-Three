<?php

function hundredpointrally_getmoduleinfo(){
	$info = array(
		"name"=>"World Map Rally",
		"version"=>"2010-11-16",
		"author"=>"Dan Hall",
		"category"=>"Map",
		"download"=>"",
		"override_forced_nav"=>true,
		"settings"=>array(
			"data" => "Game's hundred-point Rally data,text|array()",
			"joincost" => "Cost to join a Rally in cigarettes,int|4",
			"jackpotseed" => "Jackpot increases by joincost * 0.75.  Seed the Jackpot with this many cigarettes,int|20",
		),
		"prefs"=>array(
			"data" => "Player's current Rally data,text|array()",
			"won" => "Number of Rallies won by this player,int|0",
		),
	);
	return $info;
}

function hundredpointrally_install(){
	module_addhook("village");
	module_addhook("worldnav");
	module_addhook("charstats");
	return true;
}

function hundredpointrally_uninstall(){
	return true;
}

function hundredpointrally_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "village":
			if ($session['user']['location'] == "Cyber City 404"){
				tlschema($args['schemas']['tavernnav']);
				addnav($args['tavernnav']);
				tlschema();
				addnav("Rally Headquarters","runmodule.php?module=hundredpointrally&op=start");
			}
		break;
		case "worldnav":
			hundredpointrally_checknext();
			$player = @unserialize(get_module_pref("data","hundredpointrally"));
			if (!is_array($player)){
				$player = array();
				set_module_pref("data",serialize($player),"hundredpointrally");
			} else {
				//debug($player);
				if ($player['activecurrent']){
					if (!$player['sequencedpoints']){
						$player['sequencedpoints'] = 1;
					}
					$pxyz = get_module_pref("worldXYZ","worldmapen");
					$data = unserialize(get_module_setting("data","hundredpointrally"));
					//debug($data);
					if ($player['sequencedpoints'])
					if ($pxyz == $data['current']['sequencedpoints'][$player['sequencedpoints']]){
						//the player is at the point!
						output("`c`b`QYou have reached a Sequenced Rally Point!`0`b`c`n");
						if ($player['sequencedpoints'] == 50 && count($player['unorderedpoints']) == 50){
							//they won the rally!
							hundredpointrally_winrally();
							output("`c`b`QYou won the Hundred-Point Rally!`0`b`c`n");
							$stop = true;
						} else {
							if ($player['sequencedpoints'] < 50) $player['sequencedpoints']++;
							$data['current']['competitors'][$session['user']['acctid']]['points']++;
							$data['current']['competitors'][$session['user']['acctid']]['name']=$session['user']['name'];
							set_module_setting("data",serialize($data),"hundredpointrally");
							set_module_pref("data",serialize($player),"hundredpointrally");
						}
					} else {
						if (!@in_array($pxyz,$player['unorderedpoints']) && in_array($pxyz,$data['current']['unorderedpoints'])){
							//the player has hit an unsequenced point!
							output("`c`b`QYou have reached an Unordered Rally Point!`0`b`c`n");
							if ($player['sequencedpoints'] == 50 && count($player['unorderedpoints']) == 49){
								//they won the rally!
								hundredpointrally_winrally();
								output("`c`b`QYou won the Hundred-Point Rally!`0`b`c`n");
								$stop = true;
							} else {
								$player['unorderedpoints'][]=$pxyz;
								$data['current']['competitors'][$session['user']['acctid']]['points']++;
								$data['current']['competitors'][$session['user']['acctid']]['name']=$session['user']['name'];
								set_module_setting("data",serialize($data),"hundredpointrally");
								set_module_pref("data",serialize($player),"hundredpointrally");
							}
						}
					}
					if (!$stop && $player['sequencedpoints'] < 50){
						list($nx, $ny, $nz) = explode(",", $data['current']['sequencedpoints'][$player['sequencedpoints']]);
						list($px, $py, $pz) = explode(",", $pxyz);
						output("`QSequenced Rally Point %s of 50: ",$player['sequencedpoints']);
						if ($px > $nx){
							output("%s West",$px - $nx);
						} else if ($px < $nx){
							output("%s East", $nx - $px);
						}
						if ($px != $nx && $py != $ny){
							output_notl(", ");
						} else if ($py == $ny){
							output_notl(".");
						}
						if ($py > $ny){
							output("%s South",$py - $ny);
						} else if ($py < $ny){
							output("%s North",$ny - $py);
						}
						output_notl("`n");
					}
					if (!$stop && count($player['unorderedpoints']) < 50){
						output("`Q%s of 50 Unordered Rally Points hit.  <a href='runmodule.php?module=hundredpointrally&op=showpoints' target='new'>Click here to see the Unordered Rally Points</a> that you must hit to win.`0`n",count($player['unorderedpoints']),true);
					}
					output_notl("`n");
				} else if ($player['activenext']){
					$data = unserialize(get_module_setting("data","hundredpointrally"));
					if ($data['next']['starttime']){
						$nextstarttime = reltime($data['next']['starttime']);
						output("`c`QNext Hundred-Point Rally begins in %s`0`c`n",$nextstarttime);
					}
				}
			}
		break;
		}
	return $args;
}

function hundredpointrally_run(){
	global $session;
	page_header("Rally Headquarters");
	$op = httpget('op');
	
	//load settings
	$joincost = get_module_setting("joincost","hundredpointrally");
	$data = @unserialize(get_module_setting("data","hundredpointrally"));
	if (!is_array($data)){
		$data = array();
		set_module_setting("data",serialize($data),"hundredpointrally");
	}
	
	//load pref
	$player = @unserialize(get_module_pref("data","hundredpointrally"));
	if (!is_array($player)){
		$player = array();
		set_module_pref("data",serialize($player),"hundredpointrally");
	}
	
	switch($op){
		case "start":
			do_forced_nav(false,false);
			require_once "lib/datetime.php";
			$nextstarttime = reltime($data['next']['starttime'],false);
			if (!$data['next']['starttime']) $nextstarttime = "Undetermined amount of time";
			$seed = get_module_setting("jackpotseed","hundredpointrally");
			if (!isset($data['next']['jackpot']) || $data['next']['jackpot'] < $seed){
				$data['next']['jackpot'] = $seed;
				set_module_setting("data",serialize($data),"hundredpointrally");
			}
			output("You head into a building decked out with chequered flags.  It smells of petrol and burning rubber, although there are no cars or motorbikes to be seen.  Noticing that the receptionist is a Robot, you surmise that she must be pumping the scent in artificially in order to create atmosphere.  Which figures, really.`n`n\"`7Hello!`0\" she says in a tinny, cheery warble.  \"`7Are you here to sign up for the Hundred-Point Rally?  The next one starts in -`0\" her voice becomes stilted and mechanical for a moment - \"`&%s TO RACE START JACKPOT VALUE OF %s CIGARETTES ENTRY FEE OF %s CIGARETTES`7.`0\"`n`n",strtoupper($nextstarttime),$data['next']['jackpot'],$joincost);
			if ($data['current']['open']){
				$currentstarttime = reltime($data['current']['starttime'],false);
				output("\"`7Or, if you prefer, the current Rally is still open.  It started -`0\" the same mechanical, halting tone - \"`&%s AGO CURRENT JACKPOT %s CIGARETTES`7.  You'd have a better chance if you waited for the next one, but you can join this one if you're impatient.  It'll cost you the same either way.\"`n`n",strtoupper($currentstarttime),$data['current']['jackpot']);
			}
			addnav("Join a Rally");
			if ($session['user']['gems'] >= $joincost){
				if (!$player['activenext']) addnav("Join the `bnext`b Hundred-Point Rally","runmodule.php?module=hundredpointrally&op=joinnext");
				if (!$player['activecurrent'] && $data['current']['open']) addnav("Join the `bcurrent`b Hundred-Point Rally","runmodule.php?module=hundredpointrally&op=joincurrent");
			}
			addnav("What?");
			addnav("Explain this Rally business to me.","runmodule.php?module=hundredpointrally&op=explain");
			addnav("Exit");
			villagenav();
		break;
		case "explain":
			do_forced_nav(false,false);
			output("\"`7It's very simple,`0\" says the Robot.  \"`7The Hundred-Point Rally is a race around the World Map.  The winner takes the jackpot - the more people signed up for the Rally, the bigger the Jackpot.  You'll be given a series of fifty co-ordinates that you have to visit in sequence, and you'll also be given another fifty co-ordinates that can be visited in any order you like.  The challenge is not only in speed, but also in your ability to determine the most efficient path to hit all one hundred co-ordinates.  Because the race can be quite long, and because the co-ordinates are only revealed once the race has begun, it may or may not be a good idea to spend some time plotting out your route before heading to the first Rally Point.  Each new Rally starts exactly twenty-four hours `&WARNING SYSTEM CLOCK ERROR WHAT THE HELL WHY IS 24 HOURS THE SAME AS SIX DAYS`7 after the last one ends, and a Rally can last for up to 48 hours.`0\"`n`nYou nod.  \"`#Could you explain that part about the system clock error again?`0\"`n`n\"`&What part?`0\"`n`n\"`#Ah.`0\"`n`n`JTo ensure that players from different time zones have a fair shake every now and then, Rallies aren't tied to game time.  When the Robot says \"24 hours,\" she means 24 hours of your time, not game time (which runs six times faster).  If you're signing up for the next Rally and a start time isn't determined yet, you'll get a Distraction exactly 24 hours before the race begins, and another one at the drop of the chequered flag.  We highly recommend that you have Distractions sent to your E-mail if you're a Rally fan, and you can find the relevant option in your Preferences menu in any Outpost.`n`nRallies end as soon as someone hits all one hundred points - so, for example, if a Rally starts at midnight and ends at 2am, the next Rally will start at 2am the next day - and if `ithat`i Rally takes three hours to complete, then the Rally ends at 5am and the next Rally begins at 5am the next day.  This way, we eventually end up going all the way around the clock, giving players in different timezones a chance to compete in the Rally.`0");
			addnav("Okay, then.");
			addnav("Back to Reception","runmodule.php?module=hundredpointrally&op=start");
		break;
		case "joinnext":
			do_forced_nav(false,false);
			output("You hand over your Cigarettes.  \"`7Very well.  Your entry has been accepted and you will be notified via Distraction when the race starts.  In the meantime, I suggest you buy a very, very fast Mount.`0\"  The Robot tries to smile.  It doesn't come out too well.");
			$data['next']['competitors'][$session['user']['acctid']]['points'] = 0;
			$data['next']['competitors'][$session['user']['acctid']]['name'] = $session['user']['name'];
			$player['activenext'] = true;
			$session['user']['gems'] -= $joincost;
			$data['next']['jackpot'] += floor($joincost*0.75);
			set_module_pref("data",serialize($player),"hundredpointrally");
			set_module_setting("data",serialize($data),"hundredpointrally");
			addnav("Exit");
			villagenav();
		break;
		case "joincurrent":
			do_forced_nav(false,false);
			output("You hand over your Cigarettes.  \"`7Very well.  Your entry has been accepted.  Now you had better get out there and race!`0\"  The Robot tries to smile.  It doesn't come out too well.");
			$data['current']['competitors'][$session['user']['acctid']]['points'] = 0;
			$data['current']['competitors'][$session['user']['acctid']]['name'] = $session['user']['name'];
			$session['user']['gems'] -= $joincost;
			$data['current']['jackpot'] += floor($joincost*0.75);
			$player['activecurrent'] = true;
			set_module_pref("data",serialize($player),"hundredpointrally");
			set_module_setting("data",serialize($data),"hundredpointrally");
			addnav("Exit");
			villagenav();
		break;
		case "showpoints":
			popup_header("Rally Points");
			rawoutput("<table cellpadding=5 cellspacing=5 border=0><tr><td valign='top'>");
			output("`0`bSequenced Points`b`nHit each of these World Map points in the prescribed order:`n");
			foreach($data['current']['sequencedpoints'] AS $order => $loc){
				list($sx, $sy, $sz) = explode(",", $data['current']['sequencedpoints'][$order]);
				if ($player['sequencedpoints']>$order){
					rawoutput("<del>");
					output("Point %s: `@%s,%s`0`n",$order,$sx,$sy);
					rawoutput("</del>");
				} else if ($player['sequencedpoints']<$order){
					output("Point %s: `\$%s,%s`0`n",$order,$sx,$sy);
				} else {
					output(">> Point %s: `Q%s,%s`0`n",$order,$sx,$sy);
				}
			}
			rawoutput("</td><td valign='top'>");
			output("`0`bUnordered Points`b`nHit each of these World Map points in whatever order seems best to you:`n");
			foreach($data['current']['unorderedpoints'] AS $order => $loc){
				list($ux, $uy, $uz) = explode(",", $loc);
				if (@in_array($loc,$player['unorderedpoints'])){
					rawoutput("<del>");
					output("`@%s,%s`0`n",$ux,$uy);
					rawoutput("</del>");
				} else {
					output("`\$%s,%s`0`n",$ux,$uy);
				}
			}
			rawoutput("</td><td valign='top'>");
			
			$rankings = hundredpointrally_getrankings();
			output("`bCurrent Rankings`b`n");
			foreach($rankings AS $rank => $info){
				if ($info['acctid'] == $session['user']['acctid']) output("`J`b>>`b ");
				output("#%s: %s`0 (%s points)`n",$rank+1,$info['name'],$info['points']);
			}
			rawoutput("</td></tr></table>");
			popup_footer();
		break;
	}
	page_footer();
}

function hundredpointrally_getrankings(){
	global $session;
	$data = unserialize(get_module_setting("data","hundredpointrally"));
	//debug($data);
	$rankings = array();
	foreach($data['current']['competitors'] AS $key => $vals){
		$vals['acctid'] = $key;
		$rankings[$key] = $vals;
	}
	usort($rankings, 'pointscompare');
	return $rankings;
}

function pointscompare($a, $b){
	return strnatcmp($b['points'], $a['points']);
}

function hundredpointrally_winrally(){
	//run this when a player has won a rally
	//todo: actually win the rally, award cigs and so forth
	global $session;
	$data = unserialize(get_module_setting("data","hundredpointrally"));

	//first, award the player with their cigarettes
	$session['user']['gems'] += $data['current']['jackpot'];
	output("You won %s Cigarettes!`n`n",$data['current']['jackpot']);

	$next = time();
	$next += 86400;
	$data['next']['starttime'] = $next;
	require_once "lib/systemmail.php";

	//notify players in this rally that it's over
	foreach($data['current']['competitors'] AS $key => $vals){
		$to = $key;
		$subj = "The Hundred-Point Rally has ended!";
		$body = "This is just a quick message to let you know that the Hundred-Point Rally has been won!  ".$session['user']['name']." finished all one hundred points first, and takes home ".$data['current']['jackpot']." Cigarettes as the prize.  The next Rally starts in 24 hours!";
		systemmail($to,$subj,$body);
		//clear their prefs
		clear_module_pref("data","hundredpointrally",$to);
	}
	
	//close this rally
	$data['current']['open'] = 0;
	$data['current']['endtime'] = time();

	//notify players signed up for the next rally that a start time has been chosen
	if (is_array($data['next']['competitors']) && count($data['next']['competitors']) > 1){
		foreach($data['next']['competitors'] AS $key => $vals){
			$to = $key;
			$subj = "The next Hundred-Point Rally begins in 24 hours!";
			$body = "This is just a quick message to let you know that the starting time for the next Hundred-Point Rally has been set!  The next Rally will begin 24 hours from when you get this message!";
			systemmail($to,$subj,$body);
		}
	}

	set_module_setting("data",serialize($data),"hundredpointrally");
}

function hundredpointrally_checknext(){
	$data = unserialize(get_module_setting("data","hundredpointrally"));
	if ($data['next']['starttime']){
		$now = time();
		if ($now > $data['next']['starttime']){
			hundredpointrally_startnextrally();
		}
		//set_module_setting("data",serialize($data),"hundredpointrally");
	}
}

function hundredpointrally_startnextrally(){
	$data = unserialize(get_module_setting("data","hundredpointrally"));
	$data['last'] = $data['current'];
	$data['current'] = $data['next'];
	unset($data['next']);

	//set the current rally's points
	$xmax = get_module_setting("worldmapsizeX","worldmapen");
	$ymax = get_module_setting("worldmapsizeY","worldmapen");
	$pointstaken = array();
	//sequenced co-ordinates
	for ($i=1; $i<=50; $i++){
		$proposedpoint = e_rand(1,$xmax).",".e_rand(1,$ymax).",1";
		//make sure we don't use the same point twice
		while ($pointstaken[$proposedpoint]){
			debug("Point ".$proposedpoint." already taken, choosing again");
			$proposedpoint = e_rand(1,$xmax).",".e_rand(1,$ymax).",1";
		}
		$pointstaken[$proposedpoint] = true;
		$data['current']['sequencedpoints'][$i] = $proposedpoint;
	}
	for ($i=1; $i<=50; $i++){
		$proposedpoint = e_rand(1,$xmax).",".e_rand(1,$ymax).",1";
		//make sure we don't use the same point twice
		while ($pointstaken[$proposedpoint]){
			debug("Point ".$proposedpoint." already taken, choosing again");
			$proposedpoint = e_rand(1,$xmax).",".e_rand(1,$ymax).",1";
		}
		$pointstaken[$proposedpoint] = true;
		$data['current']['unorderedpoints'][$i] = $proposedpoint;
	}
	//unordered co-ordinates
	$data['current']['open'] = true;

	//notify the players lined up for the next rally that the rally has begun
	require_once "lib/systemmail.php";
	if (count($data['current']['competitors']) > 0){
		foreach($data['current']['competitors'] AS $key => $vals){
			$to = $key;
			$subj = "The next Hundred-Point Rally has begun!";
			$body = "This is just a quick message to let you know that the next Hundred-Point Rally has begun!  Head out into the wilderness to find the first Rally Point!";
			systemmail($to,$subj,$body);
			//set them up with the first point
			$player = array();
			$player['sequencedpoints'] = 1;
			$player['activecurrent'] = true;
			$player['activenext'] = false;
			set_module_pref("data",serialize($player),"hundredpointrally",$to);
		}
	}
	set_module_setting("data",serialize($data),"hundredpointrally");
}

?>