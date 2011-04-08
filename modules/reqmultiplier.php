<?php
// TODO:
// Add a thingy into the user's Bio to show that they're a permanent account holder
function reqmultiplier_getmoduleinfo(){
	$info = array(
		"name"=>"Requisition Multiplier",
		"author"=>"Dan Hall",
		"version"=>"2011-03-23",
		"download"=>"",
		"category"=>"Improbable",
		"prefs"=>array(
			"multiplier"=>"Player's Requisition multiplier,float|1.0",
		),
	);
	return $info;
}

function reqmultiplier_install(){
	module_addhook("creatureencounter");
	module_addhook_priority("newday",0);
	return true;
}
function reqmultiplier_uninstall(){
	return true;
}

function reqmultiplier_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "creatureencounter":
			$args['creaturegold']=round($args['creaturegold']*get_module_pref("multiplier"),0);
			break;
		case "newday":
			clear_module_pref("multiplier");
		break;
	}
	return $args;
}

function reqmultiplier_run(){
}

function reqmultiplier_change($new){
	global $session;
	$old = get_module_pref("multiplier","reqmultiplier");
	if ($new >= $old){
		set_module_pref("multiplier",$new,"reqmultiplier");
		return true;
	} else {
		return false;
	}
}
?>
