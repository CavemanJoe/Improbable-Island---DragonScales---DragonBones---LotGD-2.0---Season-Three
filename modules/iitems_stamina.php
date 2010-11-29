<?php

//todo: Stamina buff support
//todo: Action Processing support

function iitems_stamina_getmoduleinfo(){
	$info=array(
		"name"=>"IItems - Stamina",
		"version"=>"2009-06-16",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"IItems",
		"download"=>"",
	);
	return $info;
}

function iitems_stamina_install(){
	module_addhook("iitems-use-item");
	module_addhook("iitems-superuser");
	return true;
}

function iitems_stamina_uninstall(){
	return true;
}

function iitems_stamina_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "iitems-use-item":
			if ($args['master']['stamina_add']){
				require_once("modules/staminasystem/lib/lib.php");
				addstamina($args['master']['stamina_add']);
			} else if ($args['master']['stamina_remove']){
				require_once("modules/staminasystem/lib/lib.php");
				removestamina($args['master']['stamina_remove']);
			}
			break;
		case "iitems-superuser":
			output("`bIItems: Stamina Support`b`n");
			output("`bstamina_add`b - add or remove Stamina points.`n`n");
			break;
	}
	return $args;
}

function iitems_stamina_run(){
	return true;
}
?>