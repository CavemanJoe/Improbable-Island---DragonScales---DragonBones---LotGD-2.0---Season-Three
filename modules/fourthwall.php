<?php

function fourthwall_getmoduleinfo(){
	$info = array(
		"name"=>"The Fourth Wall",
		"author"=>"Dan Hall AKA CavemanJoe, ImprobableIsland.com",
		"version"=>"2009-07-04",
		"category"=>"Commentary",
		"download"=>"",
	);
	return $info;
}

function fourthwall_install(){
	module_addhook("village");
	module_addhook("moderate");
	return true;
}

function fourthwall_uninstall(){
	return true;
}

function fourthwall_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "village":
            tlschema($args['schemas']['tavernnav']);
			addnav($args['tavernnav']);
            tlschema();
			addnav("Location Four","runmodule.php?module=fourthwall");
		break;
		case "moderate":
			$args['fourthwall'] = "Location Four";
		break;
	}
	return $args;
}

function fourthwall_run(){
	global $session;
	page_header("The Place Behind the Fourth Wall");
	
	output("Much like the pleasant gardens of Common Ground, The Place Behind the Fourth Wall has an entry point from every Outpost on the Island.  Contestants may enter The Place Behind the Fourth Wall, or Location Four as it is officially known, from any Outpost - but may only return to the Outpost from which they came in.`n`nIt is a bright, blurry place, where it's rather difficult to focus on anything other than the other contestants - and even then, some find it easier to shut their eyes and simply talk and listen.`n`nIn contrast to Common Ground, The Place Behind the Fourth Wall is an area for out-of-character conversations, real-life chat, giving and receiving help with the game, and so on.  As always, official bug reports may be posted in the Enquirer - this isn't the place for them, as conversations can go too quickly for staff to find bug reports.`n`nPlease, no roleplaying in Location Four - the whole rest of the Island is a better place for that.`n`nHave fun!");

	require_once("lib/commentary.php");
	addcommentary();
	viewcommentary("fourthwall","Chatty Chat Chat");
	
	addnav("Return");
	addnav("Back to the Outpost","village.php");
	page_footer();
}

?>