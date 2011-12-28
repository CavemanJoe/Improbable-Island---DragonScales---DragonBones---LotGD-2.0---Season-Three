<?php
require_once("lib/http.php");
require_once("common.php");
require_once("lib/villagenav.php");
require_once("modules/worldmapen.php");

function specificmaplocation_getmoduleinfo(){
	$info = array(
		"name"=>"Specific Map Location",
		"author"=>"Dan Hall",
		"version"=>"2008-11-09",
		"category"=>"Map",
		"download"=>"",
		"settings"=>array(
			"Cig Rush Settings,title",
			"finder1"=>"Who found box 1?|Nobody",
			"finder2"=>"Who found box 2?|Nobody",
			"finder3"=>"Who found box 3?|Nobody",
			"finder4"=>"Who found box 4?|Nobody",
			"finder5"=>"Who found box 5?|Nobody",
			"finder6"=>"Who found box 6?|Nobody",
			"finder7"=>"Who found box 7?|Nobody",
			"finder8"=>"Who found box 8?|Nobody",
			"finder9"=>"Who found box 9?|Nobody",
			"finder10"=>"Who found box 10?|Nobody",
			),
		);
	return $info;
}

function specificmaplocation_install(){
	module_addhook("worldnav");
	return true;
}

function specificmaplocation_uninstall(){
	return true;
}

function specificmaplocation_dohook($hookname,$args){
	global $session;
	switch ($hookname){
		case "worldnav":
			if (get_module_setting("finder1") == "Nobody" && get_module_pref("worldXYZ","worldmapen") == "14,37,1"){
				output("You found one of the ten boxes of cigarettes!  You open it up to find a hundred of the damned things!  Woohoo!");
				$session['user']['gems']+=100;
				set_module_setting("finder1",$session['user']['name']);
			}
			if (get_module_setting("finder2") == "Nobody" && get_module_pref("worldXYZ","worldmapen") == "6,33,1"){
				output("You found one of the ten boxes of cigarettes!  You open it up to find a hundred of the damned things!  Woohoo!");
				$session['user']['gems']+=100;
				set_module_setting("finder2",$session['user']['name']);
			}
			if (get_module_setting("finder3") == "Nobody" && get_module_pref("worldXYZ","worldmapen") == "17,31,1"){
				output("You found one of the ten boxes of cigarettes!  You open it up to find a hundred of the damned things!  Woohoo!");
				$session['user']['gems']+=100;
				set_module_setting("finder3",$session['user']['name']);
			}
			if (get_module_setting("finder4") == "Nobody" && get_module_pref("worldXYZ","worldmapen") == "25,26,1"){
				output("You found one of the ten boxes of cigarettes!  You open it up to find a hundred of the damned things!  Woohoo!");
				$session['user']['gems']+=100;
				set_module_setting("finder4",$session['user']['name']);
			}
			if (get_module_setting("finder5") == "Nobody" && get_module_pref("worldXYZ","worldmapen") == "7,21,1"){
				output("You found one of the ten boxes of cigarettes!  You open it up to find a hundred of the damned things!  Woohoo!");
				$session['user']['gems']+=100;
				set_module_setting("finder5",$session['user']['name']);
			}
			if (get_module_setting("finder6") == "Nobody" && get_module_pref("worldXYZ","worldmapen") == "22,20,1"){
				output("You found one of the ten boxes of cigarettes!  You open it up to find a hundred of the damned things!  Woohoo!");
				$session['user']['gems']+=100;
				set_module_setting("finder6",$session['user']['name']);
			}
			if (get_module_setting("finder7") == "Nobody" && get_module_pref("worldXYZ","worldmapen") == "14,17,1"){
				output("You found one of the ten boxes of cigarettes!  You open it up to find a hundred of the damned things!  Woohoo!");
				$session['user']['gems']+=100;
				set_module_setting("finder7",$session['user']['name']);
			}
			if (get_module_setting("finder8") == "Nobody" && get_module_pref("worldXYZ","worldmapen") == "3,9,1"){
				output("You found one of the ten boxes of cigarettes!  You open it up to find a hundred of the damned things!  Woohoo!");
				$session['user']['gems']+=100;
				set_module_setting("finder8",$session['user']['name']);
			}
			if (get_module_setting("finder9") == "Nobody" && get_module_pref("worldXYZ","worldmapen") == "22,6,1"){
				output("You found one of the ten boxes of cigarettes!  You open it up to find a hundred of the damned things!  Woohoo!");
				$session['user']['gems']+=100;
				set_module_setting("finder9",$session['user']['name']);
			}
			if (get_module_setting("finder10") == "Nobody" && get_module_pref("worldXYZ","worldmapen") == "4,3,1"){
				output("You found one of the ten boxes of cigarettes!  You open it up to find a hundred of the damned things!  Woohoo!");
				$session['user']['gems']+=100;
				set_module_setting("finder10",$session['user']['name']);
			}
		break;
	}
	return $args;
}
?>
