<?php
// translator ready
// addnews ready
// mail ready

function cities_getmoduleinfo(){
	$info = array(
		"name"=>"Multiple Cities, Simplified Version",
		"version"=>"1.0",
		"author"=>"Eric Stevens, great big chunks ripped out by Dan Hall",
		"category"=>"Village",
		"download"=>"core_module",
		"allowanonymous"=>true,
		"override_forced_nav"=>true,
		"prefs"=>array(
			"Cities User Preferences,title",
			//"traveltoday"=>"How many times did they travel today?,int|0",
			"homecity"=>"User's current home city.|",
		),i
	);
	return $info;
}

function cities_install(){
	return true;
}

function cities_uninstall(){
	return true;
}

function cities_dohook($hookname,$args){
	return $args;
}

function cities_run(){
	return true;
}

?>
