<?php

function improbablehousing_getmoduleinfo(){
	$info = array(
		"name"=>"Improbable Housing",
		"version"=>"2009-11-09",
		"author"=>"Dan Hall",
		"category"=>"Housing",
		"download"=>"",
		"prefs"=>array(
			"sleepingat"=>"Player is sleeping at...,text|nowhere",
			"superuser"=>"Player is a debugger or superuser who can wander around houses,bool|0",
		),
	);
	return $info;
}

function improbablehousing_install(){
	module_addhook("worldnav");
	module_addhook("village");
	module_addhook("newday");
	module_addhook("stamina-newday");
	require_once("lib/tabledescriptor.php");
	$housing = array(
		'id'=>array('name'=>'id', 'type'=>'int(11) unsigned', 'extra'=>'auto_increment'),
		'ownedby'=>array('name'=>'ownedby', 'type'=>'int(11) unsigned'),
		'location'=>array('name'=>'location', 'type'=>'text'),
		'data'=>array('name'=>'data', 'type'=>'mediumtext'),
		'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'id'),
	);
	
	synctable(db_prefix('improbabledwellings'), $housing, true);
	
	$buildings = array(
		'hid'=>array('name'=>'hid', 'type'=>'int(11) unsigned', 'extra'=>'auto_increment'),
		'ownedby'=>array('name'=>'ownedby', 'type'=>'int(11) unsigned'),
		'location'=>array('name'=>'location', 'type'=>'text'),
		'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'hid'),
	);
	$building_prefs = array(
		'hid'=>array('name'=>'hid', 'type'=>'int(11) unsigned'),
		'pref'=>array('name'=>'pref', 'type'=>'varchar(50)'),
		'value'=>array('name'=>'value', 'type'=>'text'),
		'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'hid,pref'),
	);
	$rooms = array(
		'hid'=>array('name'=>'hid', 'type'=>'int(11) unsigned'),
		'rid'=>array('name'=>'rid', 'type'=>'int(11) unsigned'),
		'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'hid,rid'),
	);
	$room_prefs = array(
		'hid'=>array('name'=>'hid', 'type'=>'int(11) unsigned'),
		'rid'=>array('name'=>'rid', 'type'=>'int(11) unsigned'),
		'pref'=>array('name'=>'pref', 'type'=>'varchar(50)'),
		'value'=>array('name'=>'value', 'type'=>'text'),
		'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'hid,rid,pref'),
	);
	
	
	
	synctable(db_prefix('buildings'), $buildings, true);
	synctable(db_prefix('building_prefs'), $building_prefs, true);
	synctable(db_prefix('rooms'), $rooms, true);
	synctable(db_prefix('room_prefs'), $room_prefs, true);
	
	// 'module_userprefs'=>array(
		// 'modulename'=>array(
			// 'name'=>'modulename', 'type'=>'varchar(50)'
			// ),
		// 'setting'=>array(
			// 'name'=>'setting', 'type'=>'varchar(50)'
			// ),
		// 'userid'=>array(
			// 'name'=>'userid', 'type'=>'int(11) unsigned', 'default'=>'0'
			// ),
		// 'value'=>array(
			// 'name'=>'value', 'type'=>'text', 'null'=>'1'
			// ),
		// 'key-PRIMARY'=>array(
			// 'name'=>'PRIMARY',
			// 'type'=>'primary key',
			// 'unique'=>'1',
			// 'columns'=>'modulename,setting,userid'
			// ),
		// 'key-modulename'=>array(
			// 'name'=>'modulename', 'type'=>'key', 'columns'=>'modulename,userid'
			// ),
		// 'key-userid'=>array( // Speed up char deletion, takes a lot of space, though
			// 'name'=>'userid', 'type'=>'key', 'columns'=>'userid'
			// ),
		// ),
	
	//synctable(db_prefix('improbabledwellings'), $housing, true);
	
	
	require_once("modules/staminasystem/lib/lib.php");
	install_action("Masonry",array(
		"maxcost"=>50000,
		"mincost"=>20000,
		"firstlvlexp"=>500,
		"expincrement"=>1.05,
		"costreduction"=>300,
		"class"=>"Building"
	));
	install_action("Carpentry",array(
		"maxcost"=>50000,
		"mincost"=>20000,
		"firstlvlexp"=>500,
		"expincrement"=>1.05,
		"costreduction"=>300,
		"class"=>"Building"
	));
	install_action("Decorating",array(
		"maxcost"=>50000,
		"mincost"=>20000,
		"firstlvlexp"=>500,
		"expincrement"=>1.05,
		"costreduction"=>300,
		"class"=>"Building"
	));
	return true;
}

function improbablehousing_uninstall(){
	return true;
}

function improbablehousing_dohook($hookname,$args){
	global $session;
	require("modules/improbablehousing/dohook/$hookname.php");
	return $args;
}

function improbablehousing_run(){
	global $session;
	$op = httpget("op");
	require_once("modules/improbablehousing/run/$op.php");
	page_footer();
}

?>