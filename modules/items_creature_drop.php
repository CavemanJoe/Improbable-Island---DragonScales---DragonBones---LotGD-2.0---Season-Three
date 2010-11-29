<?php

require_once "modules/iitems/lib/lib.php";

function iitems_moduleprefs_getmoduleinfo(){
	$info=array(
		"name"=>"IItems - ModulePrefs",
		"version"=>"20090523",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"IItems",
		"download"=>"",
	);
	return $info;
}

function iitems_moduleprefs_install(){
	module_addhook("iitems-use-item");
	module_addhook("iitems-superuser");
	return true;
}

function iitems_moduleprefs_uninstall(){
	return true;
}

function iitems_moduleprefs_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "iitems-use-item":
			if ($args['master']['modulepref'] && $args['master']['modulepref_module'] && $args['modulepref_type']){
				$module = $args['master']['modulepref_module'];
				$pref = $args['master']['modulepref'];
				$val = $args['master']['modulepref_val'];
				if ($args['master']['modulepref_type'] == "increment"){
					increment_module_pref($pref, $val, $module);
				} else if ($args['master']['modulepref_type'] == "set"){
					set_module_pref($pref, $val, $module);
				}
			}
			break;
		case "iitems-superuser":
			output("`bIItems: Module Prefs`b`n");
			output("Alter module prefs upon use of the item.  Good for modules where items could be used, but which don't support IItems yet.`n");
			output("`bmodulepref`b - the name of the modulepref to alter.`n");
			output("`bmodulepref_module`b - the name of the module to which this pref belongs.`n");
			output("`bmodulepref_type`b - either \"increment\" or \"set\", for incrementing or setting moduleprefs.`n");
			output("`bmodulepref_val`b - the value to set or increment.`n");
			output("Example usage:`nmodulepref = availablestays | modulepref_module = inncoupons | modulepref_type = increment | modulepref_val = 5.  Gives the player five free stays at the Inn when this item is used.`n`n");
			break;
	}
	return $args;
}

function iitems_moduleprefs_run(){
	return true;
}
?>