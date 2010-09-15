<?php

//NICOTINE GUM

function nicotinegum_define_item(){
	set_item_setting("context_forest","1","nicotinegum");
	set_item_setting("context_village","1","nicotinegum");
	set_item_setting("context_worldmap","1","nicotinegum");
	set_item_setting("cratefind","50","nicotinegum");
	set_item_setting("description","Nicotine Gum helps to keep the shakes under control if you've been smoking too much.","nicotinegum");
	set_item_setting("destroyafteruse","true","nicotinegum");
	set_item_setting("eboy","true","nicotinegum");
	set_item_setting("giftable","true","nicotinegum");
	set_item_setting("image","nicotinegum.png","nicotinegum");
	set_item_setting("usetext","`0You chomp down on your Nicotine Gum, and feel the addiction shakes fade a little.`0","nicotinegum");
	set_item_setting("verbosename","Nicotine Gum","nicotinegum");
	set_item_setting("weight","0.01","nicotinegum");
	set_item_setting("require_file","nicotinegum.php","nicotinegum");
	set_item_setting("call_function","nicotinegum_use","nicotinegum");
}

function nicotinegum_use($args){
	$addiction = get_module_pref("addiction","smoking");
	$betweensmokes = (250-$addiction);
	set_module_pref("betweensmokes",$betweensmokes,"smoking");
	apply_buff("smoking",array(
		"allowinpvp"=>1,
		"allowintrain"=>1,
		"rounds"=>-1,
		"schema"=>"module-smoking",
	));
}

?>