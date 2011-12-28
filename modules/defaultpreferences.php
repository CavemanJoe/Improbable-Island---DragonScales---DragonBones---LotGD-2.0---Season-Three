<?php

function defaultpreferences_getmoduleinfo(){
	$info = array(
		"name"=>"Default Preferences entries",
		"version"=>"2009-06-02",
		"author"=>"Dan Hall",
		"category"=>"Administrative",
		"download"=>"",
		"prefs"=>array(
			"Default Preferences Entries,title",
			"hasbeenset"=>"Have the default preferences entries been set?,bool|0"
		),
	);
	return $info;
}
function defaultpreferences_install(){
	module_addhook("newday");
	return true;
}
function defaultpreferences_uninstall(){
	return true;
}
function defaultpreferences_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "newday":
			if (get_module_pref("hasbeenset")==0){
				$session['user']['prefs']['timestamp']=2;
				$session['user']['prefs']['emailonmail']=1;
				$session['user']['prefs']['systemmail']=1;
				set_module_pref("hasbeenset", 1);
			}
			break;
		}
	return $args;
}
function defaultpreferences_run(){
}
?>