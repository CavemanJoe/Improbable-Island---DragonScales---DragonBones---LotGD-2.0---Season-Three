<?php

function staminasystem_ocre__getmoduleinfo(){
	$info=array(
		"name"=>"Expanded Stamina System - Core",
		"version"=>"20090329",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"override_forced_nav"=>true,
		"category"=>"Stamina",
		"download"=>"",
		"settings"=>array(
			"actionsarray"=>"Array of actions in the game that use Stamina,viewonly",
			"turns_emulation_base"=>"Use an approximation of turns for events that are not hooked in yet - and if so then a Turn is worth at least this much Stamina (set to zero to disable),int|20000",
			"turns_emulation_ceiling"=>"One turn is worth at most this amount,int|30000",
		),
		"prefs"=>array(
			"stamina"=>"Player's current Stamina,int|0",
			//"daystamina"=>"Player's New Day Stamina,int|1000000",
			"red"=>"Amount of the bar taken up in Red Stamina levels,int|200000",
			"amber"=>"Amount of the bar taken up in Amber Stamina levels,int|400000",
			"actions"=>"Player's Actions array,textarea|array()",
			"buffs"=>"Player's Buffs array,textarea|array()",
			"user_minihof"=>"Show me the mini-HOF for Stamina-related actions,bool|true",
		)
	);
	return $info;
}
if(!(is_array(unserialize(get_module_setting("actionsarray"))))){
	set_module_setting("actionsarray",serialize(array()),"staminasystem");
}

//module_addhook_priority("charstats",99);
//module_addhook("superuser");
//module_addhook_priority("newday",99);
//module_addhook("dragonkill");


?>
