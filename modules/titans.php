<?php

function titans_getmoduleinfo(){
	$info = array(
		"name"=>"Titans",
		"version"=>"2010-02-24",
		"author"=>"Dan Hall",
		"category"=>"Improbable",
		"download"=>"",
		"settings"=>array(
			"spawnchance"=>"Chance out of a thousand that a Titan will spawn (spawns checked once per minute),int|1",
			"lastroll"=>"Timestamp of last Titan spawn roll,int|0",
			"desiredlifetime"=>"Desired lifetime of Titans in seconds,int|21600", //21600 secs = 6 hours
			"totaltitans"=>"Total number of Titans killed,viewonly",
			"totallifetime"=>"Total time that Titans have been alive,viewonly",
			"titanhp"=>"Titan's starting hitpoints,int|1000000",
		),
		"prefs"=>array(
			"info"=>"Player's Titans Info array,text|array()",
		),
	);
	return $info;
}

function titans_install(){
	module_addhook("newday-runonce");
	module_addhook("worldnav");
	$titans = array(
		'id'=>array('name'=>'id', 'type'=>'int(11) unsigned', 'extra'=>'auto_increment'),
		'creature'=>array('name'=>'creature', 'type'=>'text'),
		'battlelog'=>array('name'=>'battlelog', 'type'=>'text'),
		'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'id'),
	);
	require_once("lib/tabledescriptor.php");
	synctable(db_prefix('titans'), $titans, true);
	return true;
}

function titans_uninstall(){
	return true;
}

function titans_dohook($hookname,$args){
	global $session;
	require("modules/titans/dohook/$hookname.php");
	return $args;
}

function titans_run(){
	global $session;
	$titanop = httpget("titanop");
	require_once("modules/titans/run/$titanop.php");
	page_footer();
}

?>
