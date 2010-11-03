<?php

function timeandweather_getmoduleinfo(){
	$info = array(
		"name"=>"Time and Weather",
		"version"=>"2010-10-27",
		"author"=>"Dan Hall",
		"category"=>"Improbable",
		"download"=>"",
		"settings"=>array(
			"currentweather"=>"The current weather code,int|4",
			"lastweather"=>"The previous weather code,int|4",
			"lastupdate"=>"Timestamp of last weather change,int|0",
			"changeevery"=>"Change the weather once per this number of real seconds,int|1200",
		),
	);
	return $info;
}

function timeandweather_install(){
	module_addhook("charstats");
	return true;
}

function timeandweather_uninstall(){
	return true;
}

function timeandweather_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "charstats":
			$info = timeandweather_getcurrent();
			$definitiontext = array(
				1 => array(
					1 => "Cold and Frosty",
					2 => "Cold and Misty",
					3 => "Cool and Dewy",
					4 => "Warm and Clear",
					5 => "Cool and Drizzly",
					6 => "Dark and Rainy",
					7 => "Dark and Stormy",
				),
				2 => array(
					1 => "Cool and Misty",
					2 => "Mild and Dewy",
					3 => "Mild and Clear",
					4 => "Warm and Clear",
					5 => "Cool and Drizzly",
					6 => "Dark and Rainy",
					7 => "Dark and Stormy",
				),
				3 => array(
					1 => "Hot and Humid",
					2 => "Hot and Sunny",
					3 => "Warm and Sunny",
					4 => "Clear and Sunny",
					5 => "Light Showers",
					6 => "Heavy Rain",
					7 => "Thunderstorms",
				),
				4 => array(
					1 => "Hot and Humid",
					2 => "Hot and Sunny",
					3 => "Warm and Sunny",
					4 => "Clear and Sunny",
					5 => "Light Showers",
					6 => "Heavy Rain",
					7 => "Thunderstorms",
				),
				5 => array(
					1 => "Hot and Humid",
					2 => "Warm and Bright",
					3 => "Clear and Bright",
					4 => "Cool and Bright",
					5 => "Cloudy Skies",
					6 => "Darkening Rain",
					7 => "Dark and Stormy",
				),
				6 => array(
					1 => "Warm and Damp",
					2 => "Mild and Damp",
					3 => "Mild and Clear",
					4 => "Cool and Clear",
					5 => "Dark and Humid",
					6 => "Dark and Rainy",
					7 => "Dark and Stormy",
				),
				7 => array(
					1 => "Cold and Bright",
					2 => "Chilly and Light",
					3 => "Clear and Still",
					4 => "Warm and Humid",
					5 => "Dark and Humid",
					6 => "Pitch Black Rain",
					7 => "Black Storm",
				),
			);
			addcharstat("Game State");
			$stat = $definitiontext[$info['timezone']][$info['weather']];
			addcharstat("Current Weather:",$stat);
			break;
		}
	return $args;
}

function timeandweather_run(){
	return true;
}

function timeandweather_getcurrent(){
	global $session;
	require_once "lib/datetime.php";
	$tdet = gametimedetails();
	$now = $tdet['secssofartoday'];
	//debug ($now);
	timeandweather_update();
	$ret = array();
	$ret['time'] = $now;
	//get coarse timezone
	switch ($now){
		case $now > 79200:
		case $now < 14400:
			//night
			$zone = 7;
		break;
		case $now > 77400:
			//dusk
			$zone = 6;
		break;
		case $now > 75600:
			//sunset
			$zone = 5;
		break;
		case $now > 43200:
			//afternoon
			$zone = 4;
		break;
		case $now > 18000:
			//morning
			$zone = 3;
		break;
		case $now > 16200:
			//sunrise
			$zone = 2;
		break;
		case $now > 14400:
			//dawn
			$zone = 1;
		break;
	}
	$ret['timezone'] = $zone;
	$ret['weather'] = get_module_setting("currentweather","timeandweather");
	$change = get_module_setting("lastweather","timeandweather") - get_module_setting("currentweather","timeandweather");
	$ret['change'] = $change;
	//debug($ret);
	return $ret;
}

function timeandweather_update(){
	$now = time();
	$last = get_module_setting("lastupdate","timeandweather");
	$change = get_module_setting("changeevery","timeandweather");
	$changeat = $last + $change;
	//debug($changeat);
	if ($now > $changeat){
		set_module_setting("lastupdate",$now,"timeandweather");
		//time to change the weather
		$old = get_module_setting("currentweather","timeandweather");
		set_module_setting("lastweather",$old,"timeandweather");
		$new = e_rand(-2,2);
		increment_module_setting("currentweather",$new,"timeandweather");
		if (get_module_setting("currentweather","timeandweather") > 7){
			set_module_setting("currentweather",7,"timeandweather");
		} else if (get_module_setting("currentweather","timeandweather") < 1){
			set_module_setting("currentweather",1,"timeandweather");
		}
	}
}

?>