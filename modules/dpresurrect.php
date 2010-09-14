<?php
function dpresurrect_getmoduleinfo(){
	$info = array(
		"name"=>"Donator Point Resurrection",
		"version"=>"20090516",
		"download"=>"",
		"author"=>"Dan Hall, AKA CavemanJoe, improbableisland.com",
		"category"=>"Shades",
		"description"=>"This module allows the player to instantly restore themselves to the Island at an admin-settable number of points.",
		"settings"=>array(
			"pointsrequired"=>"Donator Points required for a resurrection,int|25",
			"timestaken"=>"Number of times this option has been taken,int|0",
		),
	);
	return $info;
}

function dpresurrect_install(){
	module_addhook("ramiusfavors");
	return true;
}

function dpresurrect_uninstall() {
	return true;
}

function dpresurrect_dohook($hookname,$args) {
	global $session;
	switch($hookname){
		case "ramiusfavors":
			$donationsleft = ($session['user']['donation'] - $session['user']['donationspent']);
			debug($donationsleft);
			if($donationsleft >= get_module_setting("pointsrequired")){
				addnav("Buy your way off the FailBoat (".get_module_setting("pointsrequired")." Donator Points)","runmodule.php?module=dpresurrect&op=resurrect");
			}
			break;
	}
	return $args;
}

function dpresurrect_run(){
	global $session;
	page_header("Returning to Improbable Island");
	$op = httpget('op');
	if($op == "resurrect"){
		$session['user']['donationspent'] += get_module_setting("pointsrequired");
		output("`\$The Watcher`0 nods.  \"`7I'm not above taking the occasional bribe.  That's what makes the world go round, right?`0\"  You nod sadly.  \"`7Prepare a rowboat, we have a restoration coming through!`0\"`n`nWelcome back to Improbable Island...");
		addnav("Return to Improbable Island","newday.php?resurrection=true");
		set_module_setting("timestaken",get_module_setting("timestaken") + 1);
	}
	page_footer();
}
?>