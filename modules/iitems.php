<?php

require_once "modules/iitems/lib/lib.php";

function iitems_getmoduleinfo(){
	$info=array(
		"name"=>"IItems",
		"version"=>"20090503",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"IItems",
		"download"=>"",
		"prefs"=>array(
			"items"=>"Player's Items array,viewonly|array()",
			"superuser"=>"Player has Item Editor access,bool|0",
		),
		"settings"=>array(
			"fightinventory"=>"Use seperate inventory for items usable in forest fights?  Requires iitems_weights and staminasystem,bool|0",
		)
	);
	return $info;
}

function iitems_install(){
	$items = array(
		'id'=>array('name'=>'id', 'type'=>'int(11) unsigned', 'extra'=>'auto_increment'),
		'localname'=>array('localname'=>'name', 'type'=>'text'),
		'data'=>array('name'=>'data', 'type'=>'text'),
		'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'id'),
	);
	require_once("lib/tabledescriptor.php");
	synctable(db_prefix('iitems'), $items, true);
	module_addhook("superuser");
	module_addhook("village");
	module_addhook("forest");
	module_addhook("worldnav");
	module_addhook("fightnav-specialties");
	module_addhook("apply-specialties");
	module_addhook("dragonkill");
	module_addhook("newday");
	return true;
}

function iitems_uninstall(){
	return true;
}

function iitems_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "superuser":
			if (get_module_pref("superuser")==1){
				addnav("Item Editor","runmodule.php?module=iitems&op=superuser&superop=start");
			}
		break;
		case "village":
		case "forest":
		case "worldnav":
			addnav("Inventory");
			addnav("Show Inventory","runmodule.php?module=iitems&op=inventory&from=".$hookname);
		break;
		case "fightnav-specialties":
			$script = $args['script'];
			iitems_show_fight_items($script);
		break;
		case "apply-specialties":
			if (httpget('skill')=="iitems"){
				$item = httpget('item');
				iitems_use_item($item,false,"fight");
			}
		break;
		case "dragonkill":
			$oldinventory = iitems_get_player_inventory();
			$newinventory = array();
			foreach ($oldinventory AS $key => $vals){
				$details = iitems_get_item_details($vals['itemid']);
				if ($details['dkpersist']){
					$newinventory[$key] = $vals;
				}
			}
			set_module_pref("items", serialize($newinventory), "iitems");
		break;
		case "newday":
			$use = iitems_has_property("useatnewday",1,true,false,false,"all");
			debug($use);
			if (count($use) && is_array($use)){
				foreach($use AS $item => $details){
					iitems_use_item($details['itemid'],false,$details['inventorylocation']);
				}
			}
		break;
	}
	return $args;
}

function iitems_run(){
	global $session;
	$op = httpget('op');
	$from = httpget('from');
	$key = httpget('key');
	$transferto = httpget('transferto');
	$invloc = httpget('invloc');
	page_header("Your Inventory");
	switch($op){
		case "superuser":
			require_once("modules/iitems/lib/superuser.php");
			iitems_superuser();
		break;
		case "useitem":			
			iitems_use_item($key,false,$invloc);
		break;
		case "transferitem":			
			iitems_transfer_item($key,$transferto);
		break;
		case "discarditem":			
			iitems_discard_item($key);
		break;
		case "inventory":
			iitems_show_inventory_new($from);
		break;
	}
	modulehook("iitems-show-inventory");
	// iitems_show_inventory($from,"main");
	// if (get_module_setting("fightinventory")){
		// output_notl("`n`n");
		// iitems_show_inventory($from,"fight");
	// }
	iitems_show_inventory_new($from);
	switch ($from){
		case "village":
			addnav("Return");
			addnav("Return to the Outpost","village.php");
			break;
		case "forest":
			addnav("Return");
			addnav("Return to the Jungle","forest.php");
			break;
		case "worldnav":
			addnav("Return");
			addnav("Return to the World Map","runmodule.php?module=worldmapen&op=continue");
			break;
		case "lodge":
			addnav("Return");
			addnav("Return to the Hunter's Lodge","runmodule.php?module=iitems_hunterslodge&op=start");
	}
	modulehook("iitems_inventory_from");
	page_footer();
	return true;
}
?>
