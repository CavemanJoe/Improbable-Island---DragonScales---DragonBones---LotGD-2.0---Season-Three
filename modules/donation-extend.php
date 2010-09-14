<?php

function titans_getmoduleinfo(){
	$info = array(
		"name"=>"Titans",
		"version"=>"2010-02-24",
		"author"=>"Dan Hall",
		"category"=>"Improbable",
		"download"=>"",
		"settings"=>array(
			"spawnchance"=>"Chance of a spawn at newday out of 100,int|20",
		),
		"prefs"=>array(
			"info"=>"Player's titans Info array,text|array()",
			"user_optin"=>"You are safe from the effects of Outpost invasions until you pass level ten in your first Drive Kill.  Would you like to opt-in anyway?,bool|0",
		),
	);
	return $info;
}

function titans_install(){
	module_addhook("newday-runonce");
	module_addhook("battle");
	module_addhook("endofpage");
	//todo: remove village hook
	module_addhook("village");
	$titans = array(
		'id'=>array('name'=>'id', 'type'=>'int(11) unsigned', 'extra'=>'auto_increment'),
		'location'=>array('name'=>'location', 'type'=>'text'),
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
