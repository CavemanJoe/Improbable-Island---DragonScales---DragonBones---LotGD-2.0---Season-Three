<?php

function counciloffices_getmoduleinfo(){
	$info = array(
		"name"=>"Council Offices",
		"author"=>"Dan Hall",
		"version"=>"2009-07-04",
		"category"=>"Council Offices",
		"download"=>"",
	);
	return $info;
}

function counciloffices_install(){
	module_addhook("village");
	return true;
}

function counciloffices_uninstall(){
	return true;
}

function counciloffices_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "village":
			tlschema($args['schemas']['fightnav']);
			addnav($args['fightnav']);
			tlschema();
			addnav("Council Offices","runmodule.php?module=counciloffices&councilop=enter");
			break;
	}
	return $args;
}

function counciloffices_run(){
	global $session;
	page_header("Council Offices");
	switch (httpget('councilop')){
		case "enter":
			switch ($session['user']['location']){
				case "NewHome":
					output("The \"Council Offices\" of this Outpost amount to a tiny hut with a man inside reading a newspaper behind a desk.  He looks up as you come in.`n`n\"`1Can I help you?`0\"`n`n");
					break;
				case "Kittania":
					output("The \"Council Offices\" of this Outpost amount to a tiny hut with a patient-looking KittyMorph sat behind a desk inside.  She looks up as you come in.`n`n\"`1What can I do for you?`0\"`n`n");
					break;
				case "New Pittsburgh":
					output("The \"Council Offices\" of this Outpost amount to a tiny hut with a patient-looking Zombie sat behind a desk inside.  She looks up as you come in.`n`n\"`1What can I do for you?`0\"`n`n");
					break;
				case "Squat Hole":
					output("You step into the dilapidated Council Offices.  For a moment, you believe yourself to be alone; then, you notice the shining bald head sat behind the desk.  A squeaky voice shouts \"`1Y'arright there chuck, what d'ya want?`0\"`n`n");
					break;
				case "Pleasantville":
					output("The \"Council Offices\" of this Outpost amount to a tiny hut with a patient-looking Mutant sat behind a desk inside.  He looks up as you come in.`n`n\"`1What can I do for you?`0\"`n`n");
					break;
				case "Cyber City 404":
					output("The \"Council Offices\" of this Outpost amount to a tiny hut with a stern-looking Robot sat behind a desk inside.  He looks up as you come in.`n`n\"`1State your request.`0\"`n`n");
					break;
				case "AceHigh":
					output("The \"Council Offices\" of this Outpost amount to a tiny hut with an immaculately-dressed woman sat reading a newspaper behind a desk.  She looks up as you come in, eyes giving off a faint green glow.`n`n\"`1What can I do for you?`0\"`n`n");
					break;
				case "Improbable Central":
					output("The \"Council Offices\" of this Outpost amount to a tiny hut with a man inside reading a newspaper behind a desk.  He looks up as you come in.`n`n\"`1Can I help you?`0\"`n`n");
					break;
			}
			addnav("State your business.");
			addnav("O?You know, I don't have a clue what I came in here for.  Back to the Outpost.","village.php");
			break;
	}
	modulehook("counciloffices");
	page_footer();
}
?>
