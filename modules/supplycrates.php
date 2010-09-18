<?php

function supplycrates_getmoduleinfo(){
	$info = array(
		"name"=>"Supply Crates",
		"author"=>"Dan Hall",
		"version"=>"2010-09-17",
		"category"=>"Iitems",
		"download"=>"",
		"settings"=>array(
			"donationaddition"=>"Add one crate for this many donator points in a single transaction,int|100",
			"maxitems"=>"Maximum number of items to add to each crate,int|10",
			"minitems"=>"Minimum number of items to add to each crate,int|2",
			),
		"prefs"=>array(
			"cratesopened"=>"How many Crates has the player opened?,int|0",
			),
		);
	return $info;
}

function supplycrates_install(){
	module_addhook("donation");
	return true;
}

function supplycrates_uninstall(){
	return true;
}

function supplycrates_dohook($hookname,$args){
	global $session;
	switch ($hookname){
		case "donation":
			$amt = $args['amt'];
			$donationbonus = floor($args['amt']/get_module_setting("donationaddition"));
			
			for ($i=0; $i<$donationbonus; $i++){
				$x = e_rand(1,get_module_setting("worldmapsizeX","worldmapen"));
				$y = e_rand(1,get_module_setting("worldmapsizeY","worldmapen"));
				$owner = "worldmap_".$x.",".$y.",1";
				give_item("supplycrate",false,$owner);
			}
		break;
	}
	return $args;
}
?>
