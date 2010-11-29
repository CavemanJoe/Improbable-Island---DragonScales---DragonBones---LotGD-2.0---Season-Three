<?php
// Configure Starting Equipment by Torne
// This module released under the Creative Commons
// Attribution-NonCommercial-ShareAlike 2.5 licence, available here:
// http://creativecommons.org/licenses/by-nc-sa/2.5/

// Version 1.0: First and hopefully only release
//              Functionality of the module should be trivial enough to prevent
//              the need for future changes ;)

function configstartingeq_getmoduleinfo(){
	$info = array(
		"name"=>"Configure Starting Equipment",
		"version"=>"1.0",
		"author"=>"Torne",
		"category"=>"General",
		"download"=>"http://dragonprime.net/users/Torne/configstartingeq.zip",
		"description"=>"Allow the starting equipment to be changed from the usual Fists and T-Shirt",
		"settings"=>array(
			"Starting Equipment Settings,title",
			"startingweapon"=>"Weapon to start a new character with|Fists",
			"startingarmor"=>"Armor to start a new character with|T-Shirt",
		),
	);
	return $info;
}

function configstartingeq_install(){
	module_addhook("process-create");
	module_addhook("dragonkill");
	return true;
}

function configstartingeq_uninstall(){
	return true;
}

function configstartingeq_dohook($hookname, $args){
	global $session;
	switch($hookname){
	case "process-create":
		$sql = "UPDATE " . db_prefix("accounts") . " SET weapon='" .
					 addslashes(get_module_setting("startingweapon")) . "', armor='" .
					 addslashes(get_module_setting("startingarmor")) . "' WHERE acctid=" .
					 $args['acctid'];
		db_query($sql);
		break;
	case "dragonkill":
		$session['user']['weapon'] = get_module_setting("startingweapon");
		$session['user']['armor'] = get_module_setting("startingarmor");
		break;
	}
	return $args;
}

