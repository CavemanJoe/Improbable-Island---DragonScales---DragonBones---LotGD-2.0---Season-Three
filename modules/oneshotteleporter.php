<?php

function oneshotteleporter_getmoduleinfo(){
	$info=array(
		"name"=>"One Shot Teleporter",
		"version"=>"2010-09-15",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"Item support modules",
		"download"=>"",
	);
	return $info;
}

function oneshotteleporter_install(){
	return true;
}

function oneshotteleporter_uninstall(){
	return true;
}

function oneshotteleporter_dohook($hookname,$args){
	return $args;
}

function oneshotteleporter_run(){
	global $session;
	$to=httpget("to");
	if ($to==""){
		page_header("The Void");
		output("You press the button on your One-Shot Teleporter.  One obligatory blinding flash of light and pain later, you find yourself floating around in empty black nothingness!`n`nA flashing red light and an annoying BEEPing noise from your device insists that you select a destination, and quickly, before you find yourself stuck here or imploded.");
		$vloc = array();
		$vname = getsetting("villagename", LOCATION_FIELDS);
		$vloc[$vname] = "village";
		$vloc = modulehook("validlocation", $vloc);
		ksort($vloc);
		reset($vloc);
		addnav("Choose a Destination");
		foreach($vloc as $loc=>$val) {
			addnav(array("Go to %s", $loc), "runmodule.php?module=oneshotteleporter&to=".htmlentities($loc));
		}
	} else {
		page_header("Back to Reality");
		output("You quickly select an outpost from the list.  With a sudden jolt, you find yourself standing in the middle of your chosen outpost!  You look around for your teleporting device, but realise that it must have only teleported you, not itself.  What a piece of junk.");
		$session['user']['location']=$to;
		$session['user']['specialinc'] = "";
		addnav("Continue");
		addnav("Back to the Outpost","village.php");
	}
	page_footer();
	return true;
}
?>