<?php

//ZAP GRENADE

function zapgrenade_define_item(){
	set_item_setting("allowtransfer","fight","zapgrenade");
	set_item_setting("context_fight","1","zapgrenade");
	set_item_setting("cratefind","100","zapgrenade");
	set_item_setting("description","A ZAP Grenade will blind and deafen your enemy, allowing you to quickly put the boot in while they're disoriented. It's very useful for when you just need to hold on a little longer.","zapgrenade");
	set_item_setting("destroyafteruse","true","zapgrenade");
	set_item_setting("eboy","true","zapgrenade");
	set_item_setting("giftable","true","zapgrenade");
	set_item_setting("image","zapgrenade.png","zapgrenade");
	set_item_setting("verbosename","ZAP Grenade","zapgrenade");
	set_item_setting("weight","0.8","zapgrenade");
	set_item_setting("require_file","zapgrenade.php","zapgrenade");
	set_item_setting("call_function","zapgrenade_use","zapgrenade");
}

function zapgrenade_use($args){
	global $session;
	debug("Applying Zap Grenade buff");
	apply_buff('zapgrenade', array(
		"startmsg"=>"`#You pull the pin on your grenade and toss it at {badguy}`#, shielding your eyes.  After a blinding flash, your foe is left dazed and confused!",
		"name"=>"`^ZAP Grenade",
		"rounds"=>e_rand(3,7),
		"badguyatkmod"=>0.1,
		"badguydefmod"=>0.1,
		"roundmsg"=>"{badguy} is blinded, deafened and thoroughly confused, and flails wildly while you pummel it!",
		"wearoff"=>"{badguy}`# feels some coherence return, and lunges at you!",
		"expireafterfight"=>1,
		"schema"=>"iitems-catcher"
	));
}

?>