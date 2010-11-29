<?php

function worldmapchat_getmoduleinfo(){
	$info = array(
		"name"=>"World Map Chat",
		"version"=>"2009-09-02",
		"author"=>"Dan Hall",
		"category"=>"Experimental",
		"download"=>"",
	);
	return $info;
}
function worldmapchat_install(){
	module_addhook_priority("worldnav",20);
	return true;
}
function worldmapchat_uninstall(){
	return true;
}
function worldmapchat_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "worldnav":
			addnav("Lonely?");
			addnav("Look around for other people","runmodule.php?module=worldmapchat");
			break;
		}
	return $args;
}
function worldmapchat_run(){
	global $session;
	page_header("Who is here?");
	output("Because the more out-of-the-way areas of Improbable Island can provide a shred more privacy than the town squares, you can sometimes find people hanging around and just chatting.  If, that is, they've not hidden themselves too well.`n`nYou look around yourself to see if there are any travellers nearby who you can chinwag with.`n`n");
	require_once("lib/commentary.php");
	addcommentary();
	$loc = get_module_pref("worldXYZ","worldmapen");
	viewcommentary("worldmap-".$loc,"Chat with others who walk this path...",25);
	addnav("Continue your Journey");
	addnav("Back to the World Map","runmodule.php?module=worldmapen&op=continue");
	page_footer();
}
?>