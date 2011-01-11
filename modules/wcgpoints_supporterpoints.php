<?php

function wcgpoints_supporterpoints_getmoduleinfo(){
	$info = array(
		"name"=>"Cobblestone Cottage - Supporter Points",
		"author"=>"Dan Hall AKA CavemanJoe, ImprobableIsland.com",
		"version"=>"2010-10-08",
		"category"=>"WCG Points",
		"download"=>"",
		"settings"=>array(
			"Cobblestone Cottage Supporter Point Settings,title",
			"initialrequirement"=>"Required seconds of runtime for the first reward,int|86400",
			"initialreward"=>"Supporter Points granted as initial reward,int|500",
		),
		"prefs"=>array(
			"Cobblestone Cottage Supporter Points Prefs,title",
			"gotfirst"=>"Has the player received their initial bunch of points,bool|0",
		)
	);
	return $info;
}

function wcgpoints_supporterpoints_install(){
	module_addhook("wcgpoints_increased");
	module_addhook("wcg-features-desc");
	return true;
}

function wcgpoints_supporterpoints_uninstall(){
	return true;
}

function wcgpoints_supporterpoints_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "wcgpoints_increased":
			if (get_module_pref("gotfirst")){
				$session['user']['donation'] += 5;
				output("`5`bYou have five extra Supporter Points!`b  Thank you for contributing to humanitarian research.`0`n`n");
			} else if (get_module_pref("runtime","wcgpoints") > get_module_setting("initialrequirement")){
				$session['user']['donation'] += get_module_setting("initialreward");
				require_once "lib/systemmail.php";
				$subj = "You've got Supporter Points!";
				$body = "As a thank-you for helping us to support humanitarian research via grid computing, we've given you 500 Supporter Points completely free of charge.  Thank you so much!  Remember that you'll get five extra Supporter Points every time your World Community Grid points increase (which is usually every 24 hours, if you let the client run regularly).  Have fun!";
				systemmail($session['user']['acctid'],$subj,$body);
				set_module_pref("gotfirst",true);
			}
		break;
		case "wcg-features-desc":
			output("`bFree Supporter Points`b: After you've run the World Community Grid client for at least 24 hours (it doesn't have to be all at once - an hour here and an hour there will soon add up), you'll get 500 Supporter Points completely free of charge.  After that, you'll get 5 Supporter Points every time World Community Grid reports that your Cobblestones have increased.`n`n");
		break;
	}
	return $args;
}

function wcgpoints_supporterpoints_run(){
}

?>