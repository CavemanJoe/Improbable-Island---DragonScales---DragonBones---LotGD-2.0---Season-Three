<?php

//LARGE MEDKIT

function largemedkit_define_item(){
	set_item_setting("blockrobot","true","largemedkit");
	set_item_setting("context_forest","1","largemedkit");
	set_item_setting("context_village","1","largemedkit");
	set_item_setting("context_worldmap","1","largemedkit");
	set_item_setting("cratefind","80","largemedkit");
	set_item_setting("description","A Large Medkit restores up to sixty hitpoints when used.","largemedkit");
	set_item_setting("destroyafteruse","1","largemedkit");
	set_item_setting("eboy","true","largemedkit");
	set_item_setting("giftable","true","largemedkit");
	set_item_setting("image","largemedkit.png","largemedkit");
	set_item_setting("playerhitpoints","60","largemedkit");
	set_item_setting("usetext","You sit down and patch yourself up. After a few minutes, you're feeling a little better.","largemedkit");
	set_item_setting("verbosename","Large Medkit","largemedkit");
	set_item_setting("weight","2","largemedkit");
	set_item_setting("require_file","largemedkit.php","largemedkit");
	set_item_setting("call_function","largemedkit_use","largemedkit");
}

function largemedkit_use($args){
	global $session;
	$session['user']['hitpoints'] += 60;
	if ($session['user']['hitpoints'] > $session['user']['maxhitpoints']){
		$session['user']['hitpoints'] = $session['user']['maxhitpoints']
	}
}

?>