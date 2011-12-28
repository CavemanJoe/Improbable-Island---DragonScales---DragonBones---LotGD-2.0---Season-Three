<?php

function human_loot_getmoduleinfo(){
	$info = array(
		"name"=>"Race:Human - Obtaining Loot in the Council Offices",
		"author"=>"Dan Hall",
		"version"=>"2009-07-21",
		"category"=>"Council Offices",
		"download"=>"",
		"prefs" => array(
			"Human Council Loot user prefs,title",
			"itemstaken"=>"Number of Items taken today,int|0",
		),
	);
	return $info;
}

function human_loot_install(){
	module_addhook("counciloffices");
	module_addhook("newday");
	return true;
}

function human_loot_uninstall(){
	return true;
}

function human_loot_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "newday":
			if ($session['user']['race']=="Human"){
				set_module_pref("itemstaken",0);
				output("`0As a Human, you can obtain certain basic items free of charge from the Council Offices in NewHome.  There's some stuff there waiting for you now.`n`n");
			}
			break;
		case "counciloffices":
			if ($session['user']['location']=="NewHome" && $session['user']['race']=="Human" && get_module_pref("itemstaken") < 3){
				addnav("Claim your Free Stuff","runmodule.php?module=human_loot");
			}
			break;
	}
	return $args;
}

function human_loot_run(){
	global $session;
	page_header("The Procurement of Appurtenances");
	if (get_module_pref("itemstaken") <= 3 && httpget("give")){
		debug(get_module_pref("itemstaken"));
		give_item(httpget("give"));
		increment_module_pref("itemstaken");
	}
	debug(get_module_pref("itemstaken"));
	switch (get_module_pref("itemstaken")){
		case 0:
			output("\"`1Ah.  Yes, I don't believe you've had your standard equipment issue today.  Well, let's get started, then.`0\"  The man sets his newspaper down, and counts on his fingers.  \"`1We've got BANG Grenades, ZAP Grenades, WHOOMPH Grenades, Small Medkits and Ration Packs.  You can have any three items you like.`0\"`n`nHe unselfconsciously goes back to reading his newspaper while you make up your mind.");
		break;
		case 1:
			output("\"`1Okay.`0\"  The man reaches under his desk and brings up your chosen item, without taking his hands off the newspaper.  \"`1Two more.`0\"");
		break;
		case 2:
			output("\"`1Right-ho.`0\"  The man once again reaches under his desk and brings up your chosen item.  You wonder if he would even notice if someone else came in and took your place.  \"`1Last one, now.`0\"");
		break;
		case 3:
			output("\"`1Right, there we are.`0\"  The man plonks your chosen item down next to the others.  \"`1Have a nice day, and try not to get killed.`0\"");
		break;
	}
	if (get_module_pref("itemstaken") < 3){
		addnav("Gimme a...");
		addnav("BANG Grenade","runmodule.php?module=human_loot&give=banggrenade");
		addnav("WHOOMPH Grenade","runmodule.php?module=human_loot&give=whoomphgrenade");
		addnav("ZAP Grenade","runmodule.php?module=human_loot&give=zapgrenade");
		addnav("Small Medkit","runmodule.php?module=human_loot&give=smallmedkit");
		addnav("Ration Pack","runmodule.php?module=human_loot&give=rationpack");
	} else {
		addnav("Out you go, then.");
		addnav("Leave","village.php");
	}
	page_footer();
}
?>
