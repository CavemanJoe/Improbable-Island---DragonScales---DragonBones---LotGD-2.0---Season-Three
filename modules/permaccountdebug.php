<?php

function permaccountdebug_moduleprefs_getmoduleinfo(){
	$info=array(
		"name"=>"permaccountdebug - ModulePrefs",
		"version"=>"20090523",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"permaccountdebug",
		"download"=>"",
	);
	return $info;
}

function permaccountdebug_install(){
	module_addhook("superuser");
	return true;
}

function permaccountdebug_uninstall(){
	return true;
}

function permaccountdebug_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "superuser":
			break;
		case "permaccountdebug-superuser":
			addnav("Permanent Accounts Debug","runmodule.php?peraccountdebug");
			break;
	}
	return $args;
}

function permaccountdebug_run(){
	global $session;
	page_header("Perm Accounts Debug");
	$affected = array();
	$sql = "SELECT acctid,name,regdate,donation,superuser FROM " . db_prefix("accounts") . "";
	$result = db_query($sql);
	for ($i=0;$i<db_num_rows($result);$i++){
		$row = db_fetch_assoc($result);
		if (get_module_pref("purchased","permanentaccount",$row['acctid'])){
			$affected[$row['acctid']] = $row;
		}
	}
	debug($affected);
	page_footer;
	return true;
}
?>