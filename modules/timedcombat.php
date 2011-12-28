<?php

//Ideas for this module:
//How does the player learn it?
//Either from another player, invite-craze-wise, or from a teacher in exchange for cigarettes.
//Players get the ability to teach other players, one player per game day, once they've passed a maximum chain of seven in a row.  Their teachers will pronounce them masters of the art, and give them a licence to teach other players.  Other players must be physically close to them (IE on the same world map square), and both players must give up Stamina, in order to teach and learn.

function timedcombat_getmoduleinfo(){
	$info = array(
		"name" => "Timed Combat",
		"author" => "Dan Hall",
		"version" => "2009-10-28",
		"download" => "",
		"category" => "Experimental",
		"prefs" => array(
			"able"=>"Player is able to use this ability,bool|0",
			"willing"=>"Player is going to start timed combat on the next turn,bool|0",
			"lasttime"=>"Player's last timestamp,int|0",
			"target"=>"Target number of seconds to have elapsed from last page load,int|5",
			"tries"=>"Total number of attempts player has made,int|0",
			"hits"=>"Total number of perfect timings,int|0",
			"chain"=>"Number of perfect timings in a row,int|0",
			"maxchain"=>"Player's chain high score,int|0",
		),
	);
	return $info;
}

function timedcombat_install(){
	module_addhook("battle");
	module_addhook("creatureencounter");
	module_addhook("gravefight-start");
	return true;
}

function timedcombat_uninstall(){
	return true;
}

function timedcombat_dohook($hookname,$args){
	global $session,$last_timestamp;
	switch ($hookname) {
		case "creatureencounter":
		case "gravefight-start":
			set_module_pref("willing",0);
			if (!get_module_pref("able")){
				if ($session['user']['donation']>=2000){
					set_module_pref("able",true);
				}
			}
			break;
		case "battle":
			if (get_module_pref("able")){
				if (!get_module_pref("willing")){
					$target = e_rand(4,7);
					rawoutput("<table cellpadding=0 cellspacing=0><tr><td width=\"220px\"><div style=\"width:200; height:25; background:url(/images/timedcombat-background.png); border:1px solid #000000;\"></div></td><td>");
					set_module_pref("target",$target);
					output("Next target: `b%s seconds`b",$target);
					rawoutput("</td></tr></table>");
					set_module_pref("willing",true);
					$now = microtime(true);
					set_module_pref("lasttime",$now);
				} else {
					$now = microtime(true);
					$last = get_module_pref("lasttime");
					$target = get_module_pref("target");
					$offset = round($now - $last,1);
					$offset -= $target;
					if ($offset<60){
						$pass = 0;
						while ($offset > $target/2){
							$offset -= $target;
							$pass++;
						}
						$pixeloffset = 91+($offset*20);
						if ($pixeloffset<4) $pixeloffset=4;
						if ($pixeloffset>180) $pixeloffset=180;
						rawoutput("<table cellpadding=0 cellspacing=0><tr><td width=\"220px\"><div style=\"width:200; height:25; background:url(/images/timedcombat-background.png); border:1px solid #000000;\"><div style=\"width: 196; height: 21; padding-left:10px; background: url(/images/timedcombat-pointer.png); background-repeat: no-repeat; line-height: 25px; background-position: ".$pixeloffset."px\"></div></div></td><td>");
						if ($offset==1 || $offset== -1){
							$sdisp = "Second";
						} else {
							$sdisp = "Seconds";
						}
						if ($pass>1){
							output("Repeater: ");
						}
						if ($offset>0.1){
							output("`b%s %s late`b / ",$offset,$sdisp);
							clear_module_pref("chain");
						} else if ($offset<-0.1){
							output("`b%s %s early`b / ",$offset*-1,$sdisp);
							clear_module_pref("chain");
						} else {
							output("`b`@Perfect!`0`b / ");
							increment_module_pref("hits");
							increment_module_pref("chain");
							$chain = get_module_pref("chain");
							if ($chain>1){
								output("`b`@%s-chain!`b`0 / ",$chain);
								if ($chain>get_module_pref("maxchain")){
									set_module_pref("maxchain",$chain);
									output("`b`@New Personal Chain Record!`0`b / ");
									if (get_module_pref("maxchain")==4){
										$subj = "Congratulations!";
										$body = "You've just got your first four-chain, and now you can teach Timed Combat to other players!  You can teach a maximum of one player per Game Day.  To teach a player, ensure that you're both logged in and in the same chatspace, then click on their Bio and you should see the option to teach them the new skill.  Have fun!";
										require_once "lib/systemmail.php";
										systemmail($session['user']['acctid'],$subj,$body);
									}
								}
								if (is_module_active("medals")){
									if ($chain>=25){
										require_once "modules/medals.php";
										medals_award_medal("timedcombat_chain_25","Master of the Metronome","This player got a 25-Chain in Timed Combat!","medal_timedchaingold.png");
									} else if ($chain>=10){
										require_once "modules/medals.php";
										medals_award_medal("timedcombat_chain_10","Split-Second Savant","This player got a 10-Chain in Timed Combat!","medal_timedchainsilver.png");
									} else if ($chain>=5){
										require_once "modules/medals.php";
										medals_award_medal("timedcombat_chain_5","Perfect Timing","This player got a 5-Chain in Timed Combat!","medal_timedchainbronze.png");
									}
								}
							}
							switch (httpget('auto')){
								case "five":
									$rounds=5;
								break;
								case "ten":
									$rounds=10;
								break;
								case "full":
									$rounds=-1;
								break;
								default:
									$rounds=1;
								break;
							}
							apply_buff('timedcombat', array(
								"roundmsg"=>"`0Your perfect timing really makes a difference!",
								"rounds"=>$rounds,
								"atkmod"=>2,
								"defmod"=>2,
								"expireafterfight"=>1,
								"schema"=>"module-timedcombat"
							));
						}
					$newtarget = e_rand(4,7);
					set_module_pref("target",$newtarget);
					output("Next target: `b%s seconds`b",$newtarget);
					rawoutput("</td></tr></table>");
					}
					set_module_pref("lasttime",$now);
				}
			}
			break;
	}
	return $args;
}

function timedcombat_run(){
}
?>
