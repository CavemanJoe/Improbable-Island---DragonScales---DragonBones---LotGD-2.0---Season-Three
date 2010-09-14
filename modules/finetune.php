<?php

function finetune_getmoduleinfo(){
	$info=array(
		"name"=>"Fine-Tune Game Balance Adjuster",
		"version"=>"2009-07-10",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"Game Balancing",
		"download"=>"",
		"allowanonymous"=>"true",
		"settings"=>array(
			"Healer's Hut Settings,title",
			"healermultiplier"=>"Multiplier for Healer's Hut prices,float|1.0",
		),
	);
	return $info;
}

function finetune_install(){
	module_addhook("healmultiply");
	return true;
}

function finetune_uninstall(){
	return true;
}

function finetune_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "healmultiply":
			debug($args);
			$args['alterpct'] = get_module_setting("healermultiplier");
		break;
	}
	return $args;
}

function finetune_run(){
	return true;
}
?>