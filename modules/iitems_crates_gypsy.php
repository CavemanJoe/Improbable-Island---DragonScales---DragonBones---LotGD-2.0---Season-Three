<?php

function iitems_crates_gypsy_getmoduleinfo(){
	$info=array(
		"name"=>"IItems - Crate Drop Gypsy Integration",
		"version"=>"2009-09-28",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"IItems",
		"download"=>"",
		"settings"=>array(
			"Comms Hut Crate Spying,title",
			"cost"=>"Cost in cigarettes to see all of the Crate Locations,int|4",
		),
	);
	return $info;
}

function iitems_crates_gypsy_install(){
	module_addhook("gypsy");
	return true;
}

function iitems_crates_gypsy_uninstall(){
	return true;
}

function iitems_crates_gypsy_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "gypsy":
			$crates = unserialize(get_module_setting("crates","iitemcrates"));
			$numcrates=0;
			foreach($crates AS $key => $vals){
				$numcrates++;
			}
			if ($numcrates==1){
				$cout = "Right now there's only one crate on the Island, mind.";
			} else {
				$cout = "Right now there are ".$numcrates." crates out there to find.";
			}
			$cost = get_module_setting("cost");
			if ($cost==1){
				$p = "Cigarette";
			} else {
				$p = "Cigarettes";
			}
			
			if ($numcrates){
			addnav(array("Find Crates (%s %s)",$cost,$p),"runmodule.php?module=iitems_crates_gypsy");
			output("`n`nThe old man leans closer and whispers: \"`!You might also be interested to know that I have some insider information on the locations of supply crates.  Only %s %s for a complete listing of every location.  %s`5\"",$cost,$p,$cout);
			}
		break;
	}
	return $args;
}

function iitems_crates_gypsy_run(){
	global $session;
	page_header("Crate Locations");
	
	$cost = get_module_setting("cost","iitems_crates_gypsy");
	
	if ($cost==1){
		$p = "Cigarette";
	} else {
		$p = "Cigarettes";
	}
	
	if ($session['user']['gems'] >= $cost){
		$session['user']['gems'] -= $cost;
		output("`5You hand over the %s and the old man chuckles.  \"`!Aaah, thank ye kindly.  Now, here we are!  I'd recommend you write these down.  And be quick about it - these crates tend to disappear `ifast!`i`5\"`n`nHe reaches under his desk and brings up a chalkboard with all the locations of the Island's crates written upon it, corresponding to the squares on your crudely-drawn World Map and laid out as X and Y co-ordinates.  You spend a few minutes studying the board.`n`n",$p);
		$crates = unserialize(get_module_setting("crates","iitemcrates"));
		foreach($crates AS $key => $vals){
			output("%s,%s`n",$vals['loc']['x'],$vals['loc']['y']);
		}
	} else {
		output("`5You enthusiastically agree to the price, before realising that you don't actually have that many cigarettes.  Whoops.");
	}
	
	addnav("Leave");
	addnav("Return to the Outpost","village.php");
	
	page_footer();
	
	return true;
}
?>