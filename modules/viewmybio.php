<?php

function viewmybio_getmoduleinfo(){
	$info = array(
		"name"=>"View My Bio",
		"version"=>"2009-11-10",
		"author"=>"Dan Hall",
		"category"=>"Bio",
		"download"=>"",
	);
	return $info;
}

function viewmybio_install(){
	module_addhook("commentaryoptions");
	return true;
}

function viewmybio_uninstall(){
	return true;
}

function viewmybio_dohook($hookname,$args){
	global $session;
	switch ($hookname){
		case "commentaryoptions":
			require_once("lib/commentary.php");
			$link = "bio.php?char=".$session['user']['acctid'] ."&ret=".URLEncode(buildcommentarylink("&frombio=true",$_SERVER['REQUEST_URI']));
			debug($link);
			output("`n`n<a href=\"$link\">View my Bio</a>",true);
			addnav("",$link);
		break;
	}
	return $args;
}

function viewmybio_run(){
}

?>