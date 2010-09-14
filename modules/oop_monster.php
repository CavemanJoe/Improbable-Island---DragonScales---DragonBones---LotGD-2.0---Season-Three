<?php

function oop_monster_getmoduleinfo(){
	$info = array(
		"name"=>"Oop Monsters",
		"author"=>"Dan Hall",
		"version"=>"2009-07-21",
		"category"=>"OOP",
		"download"=>"",
	);
	return $info;
}

function oop_monster_install(){
	module_addhook("superuser");
	return true;
}

function oop_monster_uninstall(){
	return true;
}

function oop_monster_dohook($hookname,$args){
	global $session, $charstat_info;
	switch($hookname){
		case "superuser":
			break;
	}
	return $args;
}

function oop_monster_run(){
	return true;
}
?>
