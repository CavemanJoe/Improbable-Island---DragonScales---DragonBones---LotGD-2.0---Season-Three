<?php

//RATION PACK

function rationpack_define_item(){
	set_item_setting("blockrobot","true","rationpack");
	set_item_setting("context_forest","1","rationpack");
	set_item_setting("context_village","1","rationpack");
	set_item_setting("context_worldmap","1","rationpack");
	set_item_setting("cratefind","110","rationpack");
	set_item_setting("description","Ration Packs contain everything a contestant needs to carry on fighting monsters! A contestant can reliably eat nothing but Ration Packs and survive for several weeks before dying of malnutrition. They are filling and contain very little fat. Use them to restore your Stamina, but keep in mind their low nutritional quality - it's probably not a good idea to try to live on these alone.","rationpack");
	set_item_setting("destroyafteruse","true","rationpack");
	set_item_setting("eboy","true","rationpack");
	set_item_setting("giftable","true","rationpack");
	set_item_setting("image","rationpack.png","rationpack");
	set_item_setting("usetext","You tear open the foil packaging and look inside.`n`nAfter a few moments' contemplation, you let out the heartbroken sigh of every soldier with an empty belly and a full Rat Pack.`n`nThe material inside has been designed to withstand being thrown out of a plane, bounced down a mountain, encased in snow and ice, left out in the sun and/or buried in a swamp for up to three years. It contains all the essential nutrients required for a soldier to live on for several weeks without dying of malnutrition. It even comes with dessert in the form of a chocolate bar - one that, according to rumour, has saved the lives of several contestants. Not for its nutritional value, of which there is none, but for its remarkable bullet-stopping tensile strength.`n`nYou suck thoughtfully on the corner of the material. Before long it begins to react with your saliva, breaking up and yielding to your teeth, and rewarding you some Stamina as you begin the arduous process of digestion.`n`nIt beats starving. Just.","rationpack");
	set_item_setting("verbosename","Ration Pack","rationpack");
	set_item_setting("weight","1.5","rationpack");
	set_item_setting("require_file","rationpack.php","rationpack");
	set_item_setting("call_function","rationpack_use","rationpack");
}

function rationpack_use($args){
	require_once "modules/staminasystem/lib/lib.php";
	addstamina(100000);
	increment_module_pref("fullness",40,"staminafood");
	increment_module_pref("fat",40,"staminafood");
	increment_module_pref("nutrition",30,"staminafood");
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