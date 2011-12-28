<?php

function achievements_getmoduleinfo(){
	$info = array(
		"name"=>"achievements System",
		"version"=>"2009-12-15",
		"author"=>"Dan Hall",
		"category"=>"Improbable",
		"download"=>"",
		"prefs"=>array(
			"achievements"=>"Player's achievements Array,viewonly|array()",
		),
	);
	return $info;
}
function achievements_install(){
	module_addhook("bioinfo");
	return true;
}
function achievements_uninstall(){
	return true;
}
function achievements_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "bioinfo":
			achievements_show_achievements($args['acctid']);
			break;
		}
	return $args;
}
function achievements_run(){
}
function achievements_show_achievements($acctid=false){
	global $session;
	if (!$acctid){
		$acctid = $session['user']['acctid'];
	}
	$info = unserialize(get_module_pref("achievements","achievements"));
	foreach ($info AS $key => $vals){
		debug($vals);
	}
}
function achievements_award_medal($sname, $vname, $desc, $icon, $acctid=false){
	global $session;
	if (!$acctid){
		$acctid = $session['user']['acctid'];
	}
	$ach = array("name"=>$name,"desc"=>$desc,"icon"=>$icon);
	$info = unserialize(get_module_pref("achievements","achievements"));
	$info[$sname]=array($ach);
	set_module_pref("achievements",serialize($info),"achievements",$acctid);
}

?>
