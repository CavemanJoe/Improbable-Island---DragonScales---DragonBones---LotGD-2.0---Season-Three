<?php

function tents_getmoduleinfo(){
	$info = array(
		"name"=>"Tents",
		"author"=>"Dan Hall",
		"version"=>"2009-08-09",
		"category"=>"Player-Owned Structures",
		"download"=>"",
		"settings"=>array(
			"Tents settings,title",
			"buycost"=>"Cost in Donation Points to buy the Tent IItem,int|100",
			"feedcost"=>"Cost in Donation Points to buy a can of Glop,int|2",
			),
		);
	return $info;
}

function tents_install(){

	$structures = array(
		'id'=>array('name'=>'id', 'type'=>'int(11) unsigned', 'extra'=>'auto_increment'),
		'owner'=>array('name'=>'owner', 'type'=>'int(11) unsigned'),
		'type'=>array('name'=>'type', 'type'=>'text'),
		'location'=>array('name'=>'location', 'type'=>'int(11) unsigned'),
		'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'id'),
	);
	require_once("lib/tabledescriptor.php");
	synctable(db_prefix('structures'), $structures, true);

	module_addhook("worldnav");
	module_addhook("newday");
	module_addhook("lodge");
	module_addhook("pointsdesc");

	return true;
}

function tents_uninstall(){
	return true;
}

function tents_dohook($hookname,$args){
	global $session;
	switch ($hookname){
		case "worldnav":
			$ploc = get_module_pref("worldXYZ","worldmapen");
			$sql = "SELECT owner,type,data FROM " . db_prefix("structures") . " WHERE location = $ploc";
			$result = db_query($sql);
			for ($i=0;$i<db_num_rows($result);$i++){
				//send structure through module hooks
				
			}
			// $chats = unserialize(get_module_setting("placedchats"));
			// $ploc = get_module_pref("worldXYZ","worldmapen");
			// $chatarea = 0;
			// if (!is_array($chats)) {
				// $chats = array();
			// }
			// foreach($chats AS $key => $vals){
				// if ($ploc == $vals['loc']['x'].",".$vals['loc']['y'].",1"){
					// output("`0There's a little tent here.`n");
					// addnav("Investigate Tent","runmodule.php?module=tents&xyz=".$ploc);
					// $chatarea = 1;
					// break;
				// }
			// }
			// if (!$chatarea){
				
			// }
		break;
	}
	return $args;
}

function tents_run(){
	global $session;
	$xyz = httpget('xyz');
	page_header("Tent!");
	output("There's a tent here!");
	require_once("lib/commentary.php");
	addcommentary();
	viewcommentary("worldmap".$xyz,"Map Chat!",25);
	addnav("Go Back","runmodule.php?module=worldmapen&op=continue");
	page_footer();
}
?>
