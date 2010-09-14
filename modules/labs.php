<?php

function labs_getmoduleinfo(){
	$info = array(
		"name"=>"Improbable Labs",
		"version"=>"2010-01-22",
		"author"=>"Dan Hall",
		"category"=>"Improbable Labs",
		"download"=>"",
	);
	return $info;
}
function labs_install(){
	module_addhook("village");
	return true;
}
function labs_uninstall(){
	return true;
}
function labs_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "village":
            tlschema($args['schemas']['tavernnav']);
			addnav($args['tavernnav']);
            tlschema();
			switch ($session['user']['location']){
				case "NewHome":
					addnav("The Compound","runmodule.php?module=labs");
				break;
				case "New Pittsburgh":
					addnav("The Braining Grounds","runmodule.php?module=labs");
				break;
				case "Kittania":
					addnav("The Grove","runmodule.php?module=labs");
				break;
				case "Squat Hole":
					addnav("Mash Street","runmodule.php?module=labs");
				break;
				case "Pleasantville":
					addnav("The Den of Creation","runmodule.php?module=labs");
				break;
				case "Cyber City 404":
					addnav("Robot-Generated Content Zone","runmodule.php?module=labs");
				break;
				case "AceHigh":
					addnav("Lucky Dip Lane","runmodule.php?module=labs");
				break;
				case "Improbable Central":
					addnav("Labs Boulevard","runmodule.php?module=labs");
				break;
			}
			break;
		}
	return $args;
}
function labs_run(){
	global $session;
	switch ($session['user']['location']){
		case "NewHome":
			page_header("The Compound");
			output("`0`n`n");
			modulehook("labs-nh");
		break;
		case "New Pittsburgh":
			page_header("The Braining Grounds");
			output("`0`n`n");
			modulehook("labs-np");
		break;
		case "Kittania":
			page_header("The Grove");
			output("`0`n`n");
			modulehook("labs-ki");
		break;
		case "Squat Hole":
			apage_header("Mash Street");
			output("`0`n`n");
			modulehook("labs-sq");
		break;
		case "Pleasantville":
			page_header("The Den of Creation");
			output("`0`n`n");
			modulehook("labs-pl");
		break;
		case "Cyber City 404":
			page_header("Robot-Generated Content Zone");
			output("`0`n`n");
			modulehook("labs-cc");
		break;
		case "AceHigh":
			page_header("Lucky Dip Lane");
			output("`0`n`n");
			modulehook("labs-ah");
		break;
		case "Improbable Central":
			page_header("Labs Boulevard");
			output("`0`n`n");
			modulehook("labs-ic");
		break;
	}
	addnav("Exit");
	addnav("O?Back to the Outpost","village.php");
	page_footer();
}
?>