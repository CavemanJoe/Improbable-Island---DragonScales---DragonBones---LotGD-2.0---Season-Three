<?php

function buildinghitpoints_getmoduleinfo(){
	$info = array(
		"name"=>"Building Hitpoints",
		"version"=>"2010-03-09",
		"author"=>"Dan Hall",
		"category"=>"Improbable",
		"download"=>"",
		"prefs-city"=>array(
			"buildinghitpoints"=>"Outpost's Building Hitpoints array,text|array()",
		),
	);
	return $info;
}

function buildinghitpoints_install(){
	module_addhook("village");
	return true;
}

function buildinghitpoints_uninstall(){
	return true;
}

function buildinghitpoints_dohook($hookname,$args){
	global $session;
	switch ($hookname){
		case "village":

		break;
	}
	return $args;
}

function buildinghitpoints_run(){
	//show page repairing building, using Stamina actions from Dwellings system
	//When hitpoints are more than maxhitpoints, allow use of the Reinforcement skill to increase beyond normal hitpoint capacity, similar to how reinforcing outpost walls works
}

function buildinghitpoints_register_building($id,$buildingname,$blocknav,$maxhitpoints,$city,$hitpoints=false){
	$info = buildinghitpoints_get_data($city);
	if (!$hitpoints){
		$hitpoints = $maxhitpoints;
	}
	$info['data'][$id] = array(
		"buildingname"=>$buildingname,
		"blocknav"=>$blocknav,
		"maxhitpoints"=>$maxhitpoints,
		"hitpoints"=>$hitpoints,
	);
}

function buildinghitpoints_get_data($cid){
	//Grab all the data from a particular Outpost
	if (!is_numeric($cid)){
		require_once "modules/cityprefs/lib.php";
		$cid = get_cityprefs_cityid($cid);
	}
	$data = get_module_objpref("city",$cid,"buildinghitpoints");
	$ret = array();
	$ret['data'] = @unserialize($data);
	$ret['cid'] = $cid;
	return $data;
}

function buildinghitpoints_set_data($info){
	set_module_objpref("city",$info['cid'],"buildinghitpoints",serialize($info['data']));
}

function buildinghitpoints_determine_navs($info){
	foreach($info['data'] AS $key=>$vals){
		if ($vals['hitpoints']<$vals['maxhitpoints']){
			blocknav($vals['blocknav']);
			addnav("Repairs");
			addnav(array("Repair %s",$vals['buildingname']),"runmodule.php?module=buildinghitpoints&op=repair&city=".$info['cid']."&building=".$key);
		} else {
			addnav("Reinforcements");
			addnav(array("Reinforce %s",$vals['buildingname']),"runmodule.php?module=buildinghitpoints&op=repair&city=".$info['cid']."&building=".$key);
		}
	}
}

?>
