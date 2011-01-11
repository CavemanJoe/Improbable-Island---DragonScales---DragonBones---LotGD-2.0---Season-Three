<?php

//WHOOMPH GRENADE

function whoomphgrenade_define_item(){
	set_item_setting("allowtransfer","fight","whoomphgrenade");
	set_item_setting("context_fight","1","whoomphgrenade");
	set_item_setting("cratefind","100","whoomphgrenade");
	set_item_setting("description","An incendiary device that sets your foes on fire!","whoomphgrenade");
	set_item_setting("destroyafteruse","true","whoomphgrenade");
	set_item_setting("eboy","true","whoomphgrenade");
	set_item_setting("giftable","true","whoomphgrenade");
	set_item_setting("image","whoomphgrenade.png","whoomphgrenade");
	set_item_setting("verbosename","WHOOMPH Grenade","whoomphgrenade");
	set_item_setting("weight","0.8","whoomphgrenade");
	set_item_setting("require_file","whoomphgrenade.php","whoomphgrenade");
	set_item_setting("call_function","whoomphgrenade_use","whoomphgrenade");
}

function whoomphgrenade_use($args){
	apply_buff('whoomphgrenade', array(
		"rounds"=>-1,
		"expireafterfight"=>true,
		"minioncount"=>1,
		"minbadguydamage"=>"2+round(*0.4,0);",
		"maxbadguydamage"=>"1+round(*0.1,0);",
		"startmsg"=>"`^You pull the pin on your grenade and toss it at {badguy}. The grenade explodes with a satisfying WHOOMPH, close enough to set your foe aflame!`0",
		"effectmsg"=>"`^Your enemy beats at the flames, but it's still on fire! `^{damage}`^ damage has been done in this round!",
		"schema"=>"iitems-catcher"
	));
	return $args;
}

?>