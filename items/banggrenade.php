<?php

//BANG GRENADE

function banggrenade_define_item(){
	set_item_setting("allowtransfer","fight","banggrenade");
	set_item_setting("context_fight","1","banggrenade");
	set_item_setting("cratefind","100","banggrenade");
	set_item_setting("description","A BANG Grenade is your average run-of-the-mill handheld explosive device. Pull the pin, chuck it at an enemy, hope you're on target. 'nuff said.","banggrenade");
	set_item_setting("destroyafteruse","true","banggrenade");
	set_item_setting("eboy","true","banggrenade");
	set_item_setting("image","banggrenade.png","banggrenade");
	set_item_setting("verbosename","BANG Grenade","banggrenade");
	set_item_setting("weight","0.8","banggrenade");
	set_item_setting("require_file","banggrenade.php","banggrenade");
	set_item_setting("call_function","banggrenade_use","banggrenade");
}

function banggrenade_use($args){
	global $session;
	apply_buff('banggrenade', array(
		"rounds"=>1,
		"minioncount"=>1,
		"minbadguydamage"=>"10+round(<attack>*2.0,0);",
		"maxbadguydamage"=>"5+round(<attack>*1.0,0);",
		"startmsg"=>"`4You pull the pin on your grenade and toss it at {badguy}!",
		"effectmsg"=>"`4The grenade explodes close enough to {badguy}`4 to do `^{damage}`4 damage!",
		"schema"=>"iitems-catcher"
	));
}

?>