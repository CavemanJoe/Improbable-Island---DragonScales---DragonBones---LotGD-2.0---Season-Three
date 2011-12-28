<?php

function referral_bonus_getmoduleinfo(){
	$info = array(
		"name"=>"Referral Bonus",
		"version"=>"2010-02-08",
		"author"=>"Dan Hall",
		"category"=>"Lodge",
		"download"=>"",
		"settings"=>array(
			"daysperbonus"=>"New Days that a player must have before triggering a bonus,int|1",
			"pointsperbonus"=>"Donator Points awarded per bonus,int|1",
		),
		"prefs"=>array(
			"days"=>"Counter that triggers a bonus when equal to daysperbonus setting,int|0",
		)
	);
	return $info;
}

function referral_bonus_install(){
	module_addhook("newday");
	return true;
}

function referral_bonus_uninstall(){
	return true;
}

function referral_bonus_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "newday":
			if (get_module_pref("days")>=get_module_setting("daysperbonus")){
				increment_module_pref("days");
				$sql = "UPDATE " . db_prefix("accounts") . " SET donation=donation+".get_module_setting("pointsperbonus")." WHERE acctid={$session['user']['referer']}";
				db_query($sql);
				clear_module_pref("days");
			}
			break;
		}
	return $args;
}

function referral_bonus_run(){
}

?>