<?php
// TODO:
// Add a thingy into the user's Bio to show that they're a permanent account holder
function hunterslodge_permanentaccount_getmoduleinfo(){
	$info = array(
		"name"=>"Permanent Account (IItemized version)",
		"author"=>"Dan Hall",
		"version"=>"2010-09-13",
		"download"=>"",
		"category"=>"Lodge IItems",
	);
	return $info;
}

function hunterslodge_permanentaccount_install(){
	module_addhook("delete_character");
	return true;
}
function hunterslodge_permanentaccount_uninstall(){
	return true;
}

function hunterslodge_permanentaccount_dohook($hookname,$args){
	global $session;
	switch($hookname){
	case "delete_character":
		$acctids = $args['ids'];
		$joined = join($acctids,",");
		$sql = "SELECT * FROM ".db_prefix("items_player")." WHERE acctid IN ($joined) AND item = 'hunterslodge_permanentaccount'";
		$result = db_query($sql);
		
		while ($row = db_fetch_assoc($result)){
			unset($acctids[$row['acctid']]);
		}
		
		$args['ids'] = $acctids;
		break;
	}
	return $args;
}

function hunterslodge_permanentaccount_run(){
}
?>
