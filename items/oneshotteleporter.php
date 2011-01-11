<?php

//ONE-SHOT TELEPORTER

function oneshotteleporter_define_item(){
	set_item_setting("allowtransfer","fight","oneshotteleporter");
	set_item_setting("context_fight","1","oneshotteleporter");
	set_item_setting("context_forest","1","oneshotteleporter");
	set_item_setting("context_village","1","oneshotteleporter");
	set_item_setting("context_worldmap","1","oneshotteleporter");
	set_item_setting("cratefind","50","oneshotteleporter");
	set_item_setting("description","One-Shot Teleporters allow the user to instantaneously transport to any Outpost on the Island.","oneshotteleporter");
	set_item_setting("destroyafteruse","true","oneshotteleporter");
	set_item_setting("eboy","true","oneshotteleporter");
	set_item_setting("giftable","true","oneshotteleporter");
	set_item_setting("image","oneshotteleporter.png","oneshotteleporter");
	set_item_setting("verbosename","One-Shot Teleporter","oneshotteleporter");
	set_item_setting("weight","2.5","oneshotteleporter");
	set_item_setting("require_file","oneshotteleporter.php","oneshotteleporter");
	set_item_setting("call_function","oneshotteleporter_use","oneshotteleporter");
}

function oneshotteleporter_use($args){
	delete_item($args['id']);
	redirect("runmodule.php?module=oneshotteleporter");
}

?>