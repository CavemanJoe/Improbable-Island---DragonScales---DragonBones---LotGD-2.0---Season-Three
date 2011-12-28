<?php
// TODO:
// Add a thingy into the user's Bio to show that they're a permanent account holder
function permanentaccount_getmoduleinfo(){
	$info = array(
		"name"=>"Permanent Account (IItemized version)",
		"author"=>"Dan Hall",
		"version"=>"2010-08-24",
		"download"=>"fix_this",
		"category"=>"Lodge IItems",
		"prefs"=>array(
			"Permanent Accounts User Preferences,title",
			"purchased"=>"Has a Permanent Account been purchased?,int|0",
		),
	);
	return $info;
}

function permanentaccount_install(){
	module_addhook("iitems-use-item");
	module_addhook("delete_character");
	return true;
}
function permanentaccount_uninstall(){
	return true;
}

function permanentaccount_dohook($hookname,$args){
	global $session;
	switch($hookname){
	case "iitems-use-item":
		if ($args['player']['itemid'] == "hunterslodge_permanentaccount"){
			if (get_module_pref("purchased")){
				$args['player']['usetext']="`c`b`4You already have a Permanent Account!`0`b`c`n";
				$args['master']['destroyafteruse']=0;
			} else {
				set_module_pref("purchased",1);
			}
		}
		break;
	}
	return $args;
}

function permanentaccount_run(){
}
?>
