<?php

function iitems_world_map_gathering_getmoduleinfo(){
	$info = array(
		"name"=>"IItems - World Map Materials Gathering",
		"version"=>"2009-11-25",
		"author"=>"Dan Hall",
		"category"=>"IItems",
		"download"=>"",
	);
	return $info;
}
function iitems_world_map_gathering_install(){
	module_addhook("worldnav");
	require_once("modules/staminasystem/lib/lib.php");
	install_action("Logging",array(
		"maxcost"=>50000,
		"mincost"=>20000,
		"firstlvlexp"=>500,
		"expincrement"=>1.05,
		"costreduction"=>300,
		"class"=>"Building"
	));
	install_action("Stonecutting",array(
		"maxcost"=>50000,
		"mincost"=>20000,
		"firstlvlexp"=>500,
		"expincrement"=>1.05,
		"costreduction"=>300,
		"class"=>"Building"
	));
	return true;
}
function iitems_world_map_gathering_uninstall(){
	return true;
}
function iitems_world_map_gathering_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "worldnav":
			iitems_world_map_gathering_showgather();
			break;
		}
	return $args;
}
function iitems_world_map_gathering_run(){
	global $session;
	require_once("modules/iitems/lib/lib.php");
	page_header("Gathering Materials");
	$type=httpget('mat');
	$item=httpget('item');
	$count=httpget('count');
	if (!$count)
		$count = 1;
	require_once "modules/staminasystem/lib/lib.php";
	if ($type=="wood"){
		for ($i = 0; $i < $count; $i++) {
			$lv = process_action("Logging");
			$stam = get_stamina();
			$rstam = get_stamina(0);
			$failchance = e_rand(0,99);
			if ($failchance<$stam){
				give_item("wood");
				use_item($item,"logging");
				output("`0You hack away until you have what can only be described as a bloody enormous log, suitable as a part of a quaint little cabin.  It couldn't possibly fit into your backpack, but you stuff it in anyway.`n");
				if ($lv['lvlinfo']['levelledup']==true){
					output("`n`c`b`0You gained a level in Logging!  You are now level %s!  This action will cost fewer Stamina points now, so you can lumberjack more lumber each day!`b`c`n",$lv['lvlinfo']['newlvl']);
				}
			} else {
				if ($failchance<$rstam){
					output("You hack away until you have what can only be described as a whole shitload of matchsticks.  The wood just splintered into barely more than pulp under your clumsy, half-asleep paws.`n`nMaybe it'd be a good idea to rest a bit before you try this again.`n`n");
				} else {
					$fail = 1;
					output("You hack away at the tree, your half-asleep mind blissfully ignorant of its wild swaying.  Ignorant, that is, until it falls on you.`n`nYou wake up, naturally, on the FailBoat.`n`n");
					break;
				}
			}
		}
	} else if ($type=="stone"){
		for ($i = 0; $i < $count; $i++) {
			$lv = process_action("Stonecutting");
			$stam = get_stamina();
			$rstam = get_stamina(0);
			$failchance = e_rand(0,99);
			if ($failchance<$stam){
				$success = 1;
				give_item("stone");
				use_item($item,"stonecutting");
				output("`0You hack away until you have what can only be described as a huge-ass lump of stone, very heavy and cumbersome.  For some reason your backpack seems like a really good place to put it.`n");
				if ($lv['lvlinfo']['levelledup']==true){
					output("`n`c`b`0You gained a level in Stonecutting!  You are now level %s!  This action will cost fewer Stamina points now, so you can break more rocks each day!`b`c`n",$lv['lvlinfo']['newlvl']);
				}
			} else {
				if ($failchance<$rstam){
					output("You hack away until you have what can only be described as a whole shitload of gravel.  The rock just smashed into splinters under your clumsy, half-asleep paws.`n`nMaybe it'd be a good idea to rest a bit before you try this again.`n`n");
				} else {
					$fail = 1;
					output("You hack away at the rock, blissfully unaware that another, larger rock is being gently dislodged by your half-asleep ministrations.  Another, larger rock that happens to be balanced precariously on an outcrop just above your head.`n`nYou wake up, naturally, on the FailBoat.`n`n");
					break;
				}
			}
		}
	}
	if (!$fail){
		iitems_world_map_gathering_showgather();
		addnav("Other");
		addnav("Show Inventory","runmodule.php?module=iitems&op=inventory&from=worldnav");
		addnav("Return to the World Map","runmodule.php?module=worldmapen&op=continue");
	} else {
		$session['user']['hitpoints']=0;
		$session['user']['gold']=0;
		$session['user']['alive']=0;
		//set the user's location to the last place on the map they touched
		$session['user']['location'] = get_module_pref("lastCity","worldmapen");
		addnav("Guess what happens now?");
		addnav("That's right.","shades.php");
	}
	page_footer();
}

function iitems_world_map_gathering_showgather($loc=false){
	if (!$loc){
		$loc = get_module_pref("worldXYZ","worldmapen");
	}
	list($worldmapX, $worldmapY, $worldmapZ) = explode(",", $loc);
	require_once "modules/worldmapen/lib.php";
	$terrain = worldmapen_getTerrain($worldmapX,$worldmapY,$worldmapZ);
	if ($terrain['type']=="Forest"){
		require_once "modules/iitems/lib/lib.php";
		//$equipment = get_items_with_prefs("treechopping");
		$equipment = iitems_get_item_details("treechopping");
		if (is_array($equipment)){
			addnav("Material Gathering");
			foreach($equipment AS $key => $vals){
				debug($vals);
				addnav(array("Cut wood using %s",$vals['verbosename']),"runmodule.php?module=iitems_world_map_gathering&mat=wood&item=".$vals['item']);
				addnav("Cut a bunch of wood (x5)","runmodule.php?module=iitems_world_map_gathering&mat=wood&count=5&item=".$vals['item']);
				addnav("Cut a whole shitload of wood (x10)","runmodule.php?module=iitems_world_map_gathering&mat=wood&count=10&item=".$vals['item']);
			}
		}
	} else if ($terrain['type']=="Mount"){
		require_once "modules/iitems/lib/lib.php";
		$equipment = get_items_with_prefs("rockbreaking");
		if (is_array($equipment)){
			addnav("Material Gathering");
			foreach($equipment AS $key => $vals){
//				debug($vals);
				addnav(array("Break stone using %s",$vals['verbosename']),"runmodule.php?module=iitems_world_map_gathering&mat=stone&item=".$vals['item']);
				addnav("Break a bunch of stone (x5)", "runmodule.php?module=iitems_world_map_gathering&mat=stone&count=5&item=".$vals['item']);
				addnav("Break a whole shitload of stone (x10)","runmodule.php?module=iitems_world_map_gathering&mat=stone&count=10&item=".$vals['item']);
			}
		}
	}
}
?>
