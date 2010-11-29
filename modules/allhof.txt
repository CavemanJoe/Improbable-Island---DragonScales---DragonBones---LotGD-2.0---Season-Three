<?php
function allhof_getmoduleinfo(){
	$info = array(
		"name"=>"HoF in All Villages",
		"version"=>"1.0",
		"author"=>"DaveS, idea by Caesar",
		"category"=>"Administrative",
		"download"=>"",
		"requires"=>array(		   "cities"=>"Core",		), 
	);
	return $info;
}
function allhof_install(){
	module_addhook("village");
	return true;
}
function allhof_uninstall(){
	return true;
}
function allhof_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "village":
			if ($session['user']['location'] != getsetting("villagename", LOCATION_FIELDS)){
				tlschema($args['schemas']['infonav']);
				addnav($args['infonav']);
				tlschema();
				addnav("Hall o' Fame","hof.php?hof=1");
			}
		break;
	}
	return $args;
}
function allhof_run(){
}
?>