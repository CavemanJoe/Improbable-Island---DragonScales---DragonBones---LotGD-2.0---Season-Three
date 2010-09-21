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
			//debug($args);
			foreach($args AS $carrier=>$prefs){
				if ($prefs['weight_current'] && $prefs['weight_max']){
					$mult = $prefs['weight_current'] / $prefs['weight_max'];
					$sbuffid = "wlimit_".$carrier;
					if ($mult>1){
						//debug("applying buff ".$sbuffid);
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