<?php

//LARGE MEDKIT

function smallmedkit_define_item(){
	set_item_setting("blockrobot","true","smallmedkit");
	set_item_setting("context_forest","1","smallmedkit");
	set_item_setting("context_village","1","smallmedkit");
	set_item_setting("context_worldmap","1","smallmedkit");
	set_item_setting("cratefind","120","smallmedkit");
	set_item_setting("description","A Small Medkit restores up to twenty hitpoints when used.","smallmedkit");
	set_item_setting("destroyafteruse","1","smallmedkit");
	set_item_setting("eboy","true","smallmedkit");
	set_item_setting("giftable","true","smallmedkit");
	set_item_setting("image","smallmedkit.png","smallmedkit");
	set_item_setting("usetext","You sit down and patch yourself up. After a few minutes, you're feeling a little better.","smallmedkit");
	set_item_setting("verbosename","Small Medkit","smallmedkit");
	set_item_setting("weight","1","smallmedkit");
	set_item_setting("require_file","smallmedkit.php","smallmedkit");
	set_item_setting("call_function","smallmedkit_use","smallmedkit");
}

function smallmedkit_use($args){
	global $session;
	$session['user']['hitpoints'] += 20;
	if ($session['user']['hitpoints'] > $session['user']['maxhitpoints']){
		$session['user']['hitpoints'] = $session['user']['maxhitpoints']
	}
}

?>