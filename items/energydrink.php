<?php

//ENERGY DRINK

function energydrink_define_item(){
	set_item_setting("context_forest","1","energydrink");
	set_item_setting("context_village","1","energydrink");
	set_item_setting("context_worldmap","1","energydrink");
	set_item_setting("cratefind","60","energydrink");
	set_item_setting("description","It's everything you ever wanted, and it comes in a `iROCKET CAN`i. Grants extra Stamina when used.","energydrink");
	set_item_setting("destroyafteruse","true","energydrink");
	set_item_setting("eboy","true","energydrink");
	set_item_setting("giftable","true","energydrink");
	set_item_setting("image","energydrink.png","energydrink");
	set_item_setting("usetext","`0You wolf down the Energy Drink for an immediate Stamina boost. You feel `4powerful!`0","energydrink");
	set_item_setting("verbosename","Energy Drink","energydrink");
	set_item_setting("weight","0.6","energydrink");
	set_item_setting("require_file","energydrink.php","energydrink");
	set_item_setting("call_function","energydrink_use","energydrink");
}

function energydrink_use($args){
	require_once "modules/staminasystem/lib/lib.php";
	addstamina(50000);
	increment_module_pref("fullness",5,"staminafood");
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

?>