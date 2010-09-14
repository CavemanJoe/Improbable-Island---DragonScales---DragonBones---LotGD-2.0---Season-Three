<?php

function timedcombat_teach_getmoduleinfo(){
	$info = array(
		"name" => "Timed Combat - Teach a Player",
		"author" => "Dan Hall",
		"version" => "2009-10-28",
		"download" => "",
		"category" => "Experimental",
		"prefs" => array(
			"taught"=>"Number of players this player has taught,int|0",
			"taughttoday"=>"Player has taught another player today,bool|0",
		),
	);
	return $info;
}

function timedcombat_teach_install(){
	module_addhook("biostat");
	module_addhook("newday");
	return true;
}

function timedcombat_teach_uninstall(){
	return true;
}

function timedcombat_teach_dohook($hookname,$args){
	global $session,$last_timestamp;
	switch ($hookname) {
		case "newday":
			set_module_pref("taughttoday",0);
		break;
		case "biostat":
			if (httpget('op')=="teachtimedcombat"){
				set_module_pref("able",true,"timedcombat",$args['acctid']);
				debug(get_module_pref("able","timedcombat",$args['acctid']));
				set_module_pref("taughttoday",1);
				increment_module_pref("taught");
				output("`bYou have successfully taught this character how to do Timed Combat!`b`n");
				if (is_module_active("medals")){
					require_once "modules/medals.php";
					medals_award_medal("timedcombat_teach","Time Tutor","This player taught another player how to do Timed Combat!","medal_timeteacher.png");
				}
				require_once "lib/systemmail.php";
				$subj = $session['user']['name']." taught you a new skill!";
				$body = "You can now perform Timed Combat in fights!  If you time your fight commands correctly, you'll get a double-attack and double-defence bonus!  The bonus applies to everything you do in combat.  Try timing your five-round auto-fights - one correct hit wins you five rounds of extra power!  If you don't want to muck about with counting under your breath, you can ignore the new skill and carry on fighting as you've always done.  Get four perfect hits in a row and you can teach other players too!  Have fun!";
				systemmail($args['acctid'],$subj,$body);
			}
			$ret = httpget('ret');
			if ($args['acctid']!=$session['user']['acctid'] && !get_module_pref("taughttoday") && get_module_pref("maxchain","timedcombat")>=4){
				//get the players' chat locations from commentaryinfo.php - it's handy!
				$tloc = get_module_pref("loc","commentaryinfo");
				$sloc = get_module_pref("loc","commentaryinfo",$args['acctid']);
				if ($tloc==$sloc && !get_module_pref("able","timedcombat",$args['acctid'])){
					output("This player doesn't know how to do Timed Combat.  You can teach one student per game day.`n`n");
					addnav("Teach this player the Timed Combat skill","bio.php?op=teachtimedcombat&char=".$args['acctid']."&ret=".$ret);
				}
			}
			break;
	}
	return $args;
}

function timedcombat_teach_run(){
}
?>
