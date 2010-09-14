<?php

function mapchat_getmoduleinfo(){
	$info = array(
		"name"=>"Map Chat Tents Test",
		"author"=>"Dan Hall",
		"version"=>"2009-08-08",
		"category"=>"Map",
		"download"=>"",
		"settings"=>array(
			"Map Chat Settings,title",
			"placedchats"=>"World Map XYZ co-ords for chat,viewonly",
			),
		);
	return $info;
}

function mapchat_install(){
	module_addhook("worldnav");
	module_addhook("newday");
	return true;
}

function mapchat_uninstall(){
	return true;
}

//We want to let the players add a new chat area to the Map.
//Player will be able to buy Tents and Tent Food in the Hunter's Lodge.  Cost will be 50dp for a Tent, and 2dp for a portion of Tent Food big enough to feed the tent for one game day.
//Tents are IItems.  Player buys a Tent, then positions it on the Map themselves.
//Wild Tents will be spawned in a random Map location once per game day, and Tent Food added to IItem Crates, so those without DP's still have a chance.
//Capturing a wild Tent places a Tent IItem into the player's IItems array, for them to place themselves.
//Make sure player has a Tent iitem to add, and that there isn't another Tent nearby.  They will fight.
//Make sure they're not trying to set up a Tent on water.  Tents cannot swim.
//Tents cannot be private, as of yet.  Possible future functionality: teach your Tent to recognize faces and only let in those who are invited, in return for additional DP's.  For now, just get the basic module done.
//Sleeping in a Tent offers a mild Stamina boost for the next day.
//Entries in a Tent array:
//Life - starts at 10, and decreases by one each game day.  If this value gets to 0, the Tent dies and the chatspace is deleted.  Anyone can feed a Tent, using Tent Food, which raises this number by 1 for every portion of Tent Food fed to the Tent.
//Age - this is incremented by one per game day.  Maybe later on, Tents that have been around for a while will have additional functionality via add-on modules.
//Owner - who placed the Tent?
//x and y map co-ords
//


function mapchat_dohook($hookname,$args){
	global $session;
	switch ($hookname){
		case "worldnav":
			$chats = unserialize(get_module_setting("placedchats"));
			$ploc = get_module_pref("worldXYZ","worldmapen");
			$chatarea = 0;
			if (!is_array($chats)) {
				$chats = array();
			}
			foreach($chats AS $key => $vals){
				if ($ploc == $vals['loc']['x'].",".$vals['loc']['y'].",1"){
					output("`0There's a little tent here.`n");
					addnav("Investigate Tent","runmodule.php?module=mapchat&xyz=".$ploc);
					$chatarea = 1;
					break;
				}
			}
			if (!$chatarea){
				
			}
		break;
	}
	return $args;
}

function mapchat_run(){
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
