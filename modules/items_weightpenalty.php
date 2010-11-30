<?php

//This is for iitems with functionality impossible to replicate in the native iitems system.

function items_weightpenalty_getmoduleinfo(){
	$info=array(
		"name"=>"Stamina-based Weight Penalty",
		"version"=>"2010-09-14",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"Items",
		"download"=>"",
	);
	return $info;
}

function items_weightpenalty_install(){
	module_addhook("items_weights");
	return true;
}

function items_weightpenalty_uninstall(){
	return true;
}

function items_weightpenalty_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "items_weights":
			require_once "modules/staminasystem/lib/lib.php";
<<<<<<< HEAD
			//debug($args);
			foreach($args AS $carrier=>$prefs){
				if ($prefs['weight_current'] && $prefs['weight_max'] && $prefs['wlimit_uses_buff']){
					$mult = $prefs['weight_current'] / $prefs['weight_max'];
					$sbuffid = "wlimit_".$carrier;
					if ($mult>1){
						//debug("applying buff ".$sbuffid);
=======
			foreach($args AS $carrier=>$prefs){
				if ($prefs['weight_current'] && $prefs['weight_max'] && $prefs['wlimit_use_sbuff']){
					$mult = $prefs['weight_current'] / $prefs['weight_max'];
					$sbuffid = "wlimit_".$carrier;
					if ($mult>1){
>>>>>>> 8b5d92281350005db7709911b00777e80705dd6e
						apply_stamina_buff($sbuffid, array(
							"name"=>$prefs['wlimit_sbuff_name'],
							"action"=>"Global",
							"costmod"=>$mult,
							"expmod"=>1,
							"rounds"=>-1,
							"roundmsg"=>$prefs['wlimit_sbuff_roundmsg'],
							"wearoffmsg"=>"",
						));
					} else {
						//debug("stripping buff ".$sbuffid);
						strip_stamina_buff($sbuffid);
					}
				}
			}
		break;
	}
	return $args;
}

function items_weightpenalty_run(){
	return true;
}
?>