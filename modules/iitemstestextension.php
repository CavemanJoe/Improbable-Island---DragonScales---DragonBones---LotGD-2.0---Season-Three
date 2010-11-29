<?php

require_once "modules/iitems/lib/lib.php";

function iitemstestextension_getmoduleinfo(){
	$info=array(
		"name"=>"IItems - Test Extension",
		"version"=>"20090503",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"IItems",
		"download"=>"",
	);
	return $info;
}

function iitemstestextension_install(){
	module_addhook("iitems-give-item");
	module_addhook("iitems-use-item");
	module_addhook("iitems-inventory");
	module_addhook("village");
	module_addhook("forest");
	return true;
}

function iitemstestextension_uninstall(){
	return true;
}

function iitemstestextension_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "iitems-give-item":
			if ($args['master']['power']){
				$args['player']['power'] = $args['master']['power'];
			}
			if ($args['master']['donotadd']){
				$args['player']['blockadd'] = 1;
			}
			break;
		case "iitems-use-item":
			$args['player']['usecount'] += 1;
			if ($args['player']['power']){
				$args['player']['power']--;
			}
			break;
		case "iitems-inventory":
			if ($args['player']['usecount']){
				output("You have used this item %s times.`n",$args['player']['usecount']);
			}
			if ($args['player']['power']){
				output("Power: %s points",$args['player']['power']);
			}
			break;
		case "village":
			$inventory = iitems_get_player_inventory();
			foreach($inventory AS $item => $details){
				if ($details['villagehooknav']){
					output("`bYou can use your %s here...`b`n",$details['verbosename']);
				}
			}
			break;
		case "forest":
			break;
	}
	return $args;
}

function iitemstestextension_run(){
	return true;
}
?>