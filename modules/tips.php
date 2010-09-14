<?php

function tips_getmoduleinfo(){
	$info = array(
		"name"=>"Tips",
		"author"=>"Dan Hall",
		"version"=>"2009-07-21",
		"category"=>"Stat Display",
		"download"=>"",
		"prefs" => array(
			"Human Council Loot user prefs,title",
			"itemstaken"=>"Number of Items taken today,int|0",
		),
	);
	return $info;
}

function tips_install(){
	module_addhook("charstats");
	return true;
}

function tips_uninstall(){
	return true;
}

function tips_dohook($hookname,$args){
	global $session, $charstat_info;
	switch($hookname){
		case "charstats":
			setcharstat("Tips", "<td colspan=2>Have a good look around the Museum - there's more there than meets the eye.", "</td>");
			break;
	}
	return $args;
}

function tips_run(){
	return true;
}
?>
