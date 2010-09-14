<?php

function forestchat_getmoduleinfo(){
	$info = array(
		"name"=>"Forest Chat",
		"version"=>"2009-09-02",
		"author"=>"Dan Hall",
		"category"=>"Experimental",
		"download"=>"",
	);
	return $info;
}
function forestchat_install(){
	module_addhook("forest");
	return true;
}
function forestchat_uninstall(){
	return true;
}
function forestchat_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "forest":
			addnav("Lonely?");
			addnav("Chat with nearby contestants","runmodule.php?module=forestchat");
			break;
		}
	return $args;
}
function forestchat_run(){
	global $session;
	page_header("Who is here?");
	output("You look around yourself to see if there are any warriors nearby who you can chinwag with.`n`n");
	require_once("lib/commentary.php");
	addcommentary();
	$loc = substr($session['user']['location'],0,10);
	viewcommentary("forest-".$loc,"Chat with others who walk this path...",25);
	addnav("Continue");
	addnav("Back to the Jungle","forest.php");
	page_footer();
}
?>