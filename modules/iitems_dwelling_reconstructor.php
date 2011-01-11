<?php

function iitems_dwelling_reconstructor_getmoduleinfo(){
	$info = array(
		"name"=>"IItems - Emergency Dwelling Reconstructor",
		"version"=>"2010-08-26",
		"author"=>"Dan Hall",
		"category"=>"Emergency",
		"download"=>"",
	);
	return $info;
}

//iitems required:
//iitems_dwelling_reconstructor
//iitems_dwelling_reconstructor_permanent
//Don't use the "destroyafteruse" parameter for these iitems

function iitems_dwelling_reconstructor_install(){
	module_addhook("iitems-use-item");
	return true;
}

function iitems_dwelling_reconstructor_uninstall(){
	return true;
}

function iitems_dwelling_reconstructor_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "iitems-use-item":
			if ($args['player']['itemid'] == "dwelling_reconstructor"){
				require_once "modules/iitems/lib/lib.php";
				require_once "modules/staminasystem/lib/lib.php";
				iitems_give_item('toolbox_decorating');
				iitems_give_item('toolbox_carpentry');
				iitems_give_item('toolbox_masonry');
				addstamina(1000000000000000000000000000);
				set_module_pref("encounterchance",0,"worldmapen");
				$session['user']['gems']+=100;
				output("One octillion Stamina points added.  Toolboxes for masonry, carpentry and decorating added.  100 Cigarettes added.  World Map encounter rate reduced to zero.  Have fun with that.`n`n");
			}
			break;
		}
	return $args;
}

function iitems_dwelling_reconstructor_run(){
}
?>