<?php

//MONSTER REPELLENT SPRAY

function monsterrepellentspray_define_item(){
	set_item_setting("allowtransfer","fight","monsterrepellentspray");
	set_item_setting("context_fight",true,"monsterrepellentspray");
	set_item_setting("context_forest",true,"monsterrepellentspray");
	set_item_setting("context_village",true,"monsterrepellentspray");
	set_item_setting("context_worldmap",true,"monsterrepellentspray");
	set_item_setting("cratefind",70,"monsterrepellentspray");
	set_item_setting("description","Monster Repellent Spray can be applied to the skin and clothes to keep monsters away, or applied directly to monsters during a fight to provoke a pepper-spray-like reaction.","monsterrepellentspray");
	set_item_setting("destroyafteruse",true,"monsterrepellentspray");
	set_item_setting("eboy",true,"monsterrepellentspray");
	set_item_setting("image","monsterrepellentspray.png","monsterrepellentspray");
	set_item_setting("verbosename","Monster Repellent Spray","monsterrepellentspray");
	set_item_setting("weight","0.5","monsterrepellentspray");
	set_item_setting("require_file","monsterrepellentspray.php","monsterrepellentspray");
	set_item_setting("call_function","monsterrepellentspray_use","monsterrepellentspray");
}

function monsterrepellentspray_use($args){
	if ($args['context']=="fight"){
		apply_buff('rspray_fight', array(
			"startmsg"=>"`#You pull out a can of Monster Repellent Spray, and spray it liberally on the enemy!`n",
			"name"=>"`^Repellent Spray Attack",
			"rounds"=>10,
			"badguyatkmod"=>0.4,
			"badguydefmod"=>0.4,
			"roundmsg"=>"{badguy} is coughing, choking and all runny-nosed, and cannot attack or defend as effectively!",
			"wearoff"=>"The effects of your Monster Repellent Spray seem to have worn off...`n",
			"expireafterfight"=>1,
			"schema"=>"iitems-catcher"
		));
	} else {
		apply_buff('rspray_normal', array(
			"name"=>"`^Repellent Spray",
			"rounds"=>-1,
			"badguyatkmod"=>0.8,
			"badguydefmod"=>0.8,
			"roundmsg"=>"{badguy} can't stand the smell of your Monster Repellent Spray, and doesn't want to get too close!",
			"schema"=>"iitems-catcher"
		));
		set_item_pref("encounterchance",50,"worldmapen");
		output("You liberally douse yourself with an entire can of Monster Repellent Spray.  For the rest of this game day, your chances of encountering a monster on the Island Map have been halved, and monsters you do encounter will be reluctant to attack you as hard.`n`n");
	}
	delete_item($args['id']);
}

?>