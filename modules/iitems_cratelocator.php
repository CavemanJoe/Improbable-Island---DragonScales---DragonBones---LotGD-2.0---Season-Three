<?php

//This is for iitems with functionality impossible to replicate in the native iitems system.

function iitems_cratelocator_getmoduleinfo(){
	$info=array(
		"name"=>"IItems - Crate Locator",
		"version"=>"2009-09-30",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"IItems",
		"download"=>"",
	);
	return $info;
}

function iitems_cratelocator_install(){
	module_addhook("iitems-use-item");
	module_addhook("worldnav");
	return true;
}

function iitems_cratelocator_uninstall(){
	return true;
}

function iitems_cratelocator_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "iitems-use-item":
			if ($args['player']['itemid']=="cratelocator"){
				$crates = unserialize(get_module_setting("crates"));
				output("`0You thumb the switch on your Crate Locator.  It buzzes and hisses for a moment, exhausting its primitive battery sending out a radio ping to nearby Crates.`n`n");
				$crates = unserialize(get_module_setting("crates","iitemcrates"));
				debug($crates);
				$ploc = get_module_pref("worldXYZ","worldmapen");
				list($px, $py, $pz) = explode(",", $ploc);
				
				$pxlow = $px-5;
				$pxhigh = $px+5;
				$pylow = $py-5;
				$pyhigh = $py+5;
				
				if (!is_array($crates)) {
					$crates = array();
				}
				$count = 0;
				foreach($crates AS $key => $vals){
					if ($vals['loc']['x'] >= $pxlow && $vals['loc']['x'] <= $pxhigh && $vals['loc']['y'] >= $pylow && $vals['loc']['y'] <= $pyhigh){
						$count++;
					}
				}
				output("It displays, weakly, the number `\$`b%s`b`0 in dull red LED's before its radio module catches fire.`n`n",$count);
			}
			break;
	}
	return $args;
}

function iitems_cratelocator_run(){
}
?>