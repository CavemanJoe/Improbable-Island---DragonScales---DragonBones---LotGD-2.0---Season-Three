<?php

require_once "modules/iitems/lib/lib.php";

function iitems_edibles_getmoduleinfo(){
	$info=array(
		"name"=>"IItems - Edible IItems",
		"version"=>"20090523",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"IItems",
		"download"=>"",
	);
	return $info;
}

function iitems_edibles_install(){
	module_addhook_priority("iitems-use-item",1);
	module_addhook("iitems-inventory");
	module_addhook("iitems-superuser");
	module_addhook("iitems-use-item-after");
	return true;
}

function iitems_edibles_uninstall(){
	return true;
}

function iitems_edibles_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "iitems-use-item":
			if ($args['master']['edible']){
				$full = get_module_pref("fullness","staminafood");
				if ($full >= 100){
					output("You pick up the tasty morsel, and eye it for a moment.  Then, you put it right back where it was.  You're `ifar`i too full to eat anything more for now.`n`n");
					unset($args['master']);
					unset($args['player']);
					$args['player']['break_use_operation'] = 1;
				} else {
					increment_module_pref("fat",$args['master']['edibles_fat'],"staminafood");
					increment_module_pref("nutrition",$args['master']['edibles_nutrition'],"staminafood");
					increment_module_pref("fullness",$args['master']['edibles_fullness'],"staminafood");
				}
			}
			break;
		case "iitems-use-item-after":
			if ($args['master']['edible']){
				$full = get_module_pref("fullness","staminafood");
				if ($full < 0){
					output("You still feel as though you haven't eaten in days.`n`n");
				}
				if ($full >= 0 && $full < 50){
					output("You feel a little less hungry.`n`n");
				}
				if ($full >= 50 && $full < 100){
					output("You still feel as though you've got room for more!`n`n");
				}
				if ($full >= 100){
					output("You're stuffed!  You feel as though you can't possibly eat anything more today.`n`n");
				}
			}
			break;
		case "iitems-superuser":
			output("`bIItems: Edible IItems`b.  Remember to set a value for Stamina as well.  Remember also that this is only for use with simple edible items that can't be used for other purposes - for example, an apple that can be thrown at an enemy or consumed will require an extension script, and the 'edible' param shouldn't be used in cases like this.`n");
			output("`bedible`b - Set this to enable Edible Items functionality.`n");
			output("`bedibles_nutrition`b - Increase player's Nutrition.`n");
			output("`bedibles_fat`b - Increase player's Fat.`n");
			output("`bedibles_fullness`b - Increase player's Fullness.`n`n");
			break;
	}
	return $args;
}

function iitems_edibles_run(){
	return true;
}
?>