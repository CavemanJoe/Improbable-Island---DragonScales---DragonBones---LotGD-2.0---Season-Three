<?php

function parsetest_getmoduleinfo(){
	$info = array(
		"name"=>"Parse Test",
		"author"=>"Dan Hall AKA CavemanJoe, ImprobableIsland.com",
		"version"=>"2009-04-11",
		"category"=>"parse",
		"download"=>"",
	);
	return $info;
}

function parsetest_install(){
	module_addhook("superuser");
	return true;
}

function parsetest_uninstall(){
	return true;
}

function parsetest_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "superuser":
			addnav("Parse XML file","runmodule.php?module=parsetest");
		break;
	}
	return $args;
}

function parsetest_run(){
	global $session;
	page_header("Parse Check");
	addnav("Refresh","runmodule.php?module=parsetest");
	
	if (!$xmlObj=simplexml_load_file("http://www.worldcommunitygrid.org/stat/viewMemberInfo.do?userName=CavemanJoe&xml=true")){
	debug("Cannot read that World Community Grid XML file.");
	}
	
	$test = $xmlObj->MemberStat->StatisticsTotals->Points;
	debug($test);
	
	page_footer();
	return true;
}

?>