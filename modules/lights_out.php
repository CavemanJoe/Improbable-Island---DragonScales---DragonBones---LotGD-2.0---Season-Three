<?php

function lights_out_getmoduleinfo(){
	$info = array(
		"name"=>"Lights Out",
		"version"=>"2010-02-02",
		"author"=>"Dan Hall",
		"category"=>"Improbable",
		"download"=>"",
		"settings"=>array(
			"numlights"=>"Number of lights in total,int|25",
			"lightsperrow"=>"Lights per row to be displayed,int|5",
			"startlights"=>"Randomize this number of lights to begin,int|5",
			"maxchangesperlight"=>"Maximum number of lights that will be changed when one light is toggled,int|4",
		),
		"prefs"=>array(
			"lights"=>"Player's Lights array,textarea|array()",
			"bestmoves"=>"Player's lowest number of moves,int|0",
		)
	);
	return $info;
}

function lights_out_install(){
	module_addhook("village");
	return true;
}

function lights_out_uninstall(){
	return true;
}

function lights_out_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "village":
			addnav("Play Lights Out","runmodule.php?module=lights_out&op=start");
			break;
		}
	return $args;
}

function lights_out_run(){
	global $session;
	page_header("Lights Out!");
	addnav("O?Back to the Outpost","village.php");
	$numlights = get_module_setting("numlights");
	$lightsperrow = get_module_setting("lightsperrow");
	$startlights = get_module_setting("startlights");
	$maxchangesperlight = get_module_setting("maxchangesperlight");
	
	//playtesting
	$numlights = 9;
	$lightsperrow = 3;
	$startlights = 20;
	$maxchangesperlight = 4;
	
	switch (httpget('op')){
		case "start":
			//set up the player's lights array, set all lights to off, figure out which lights are toggled by which buttons
			$lights = array();
			for ($i=1; $i<=$numlights; $i++){
				$lights[$i]['status']=0;
				$lights[$i]['changes']=array();
				$cmax = e_rand(1,$maxchangesperlight);
				for ($c=1; $c<=$cmax; $c++){
					$clights = $i;
					while ($clights==$i){
						$clights = e_rand(1,$numlights);
					}
					$lights[$i]['changes'][]=$clights;
				}
			}
			
			//Now randomly poke some lights
			for ($i=1; $i<=$startlights; $i++){
				$sw = e_rand(1,$numlights);
				$lights = lights_out_switch($sw,$lights);
			}
			
			$spref = serialize($lights);
			set_module_pref("lights",$spref);
			$moves=0;
			lights_out_show($lights,$moves,$numlights,$lightsperrow);
		break;
		case "switch":
			$moves=httpget('moves');
			$moves++;
			$lights = lights_out_switch(httpget('switch'));
			lights_out_show($lights,$moves,$numlights,$lightsperrow);
			$lit = lights_out_check($lights);
			output("There are %s lights left to turn off.  You have made %s moves.`n",$lit,$moves);
		break;
	}
	page_footer();
}

function lights_out_show($lights=false,$moves=0,$numlights=0,$lightsperrow=0){
	global $session;
	if (!$lights){
		$lights=unserialize(get_module_pref("lights","lights_out"));
	}
	if (!$lightsperrow){
		$lightsperrow=get_module_setting("lightsperrow","lights_out");
	}
	if (!$numlights){
		$numlights = get_module_setting("numlights");
	}
	$row = 0;
	for ($i=1; $i<=$numlights; $i++){
		$row++;
		if ($lights[$i]['status']){
			$img = "images/daysphere-full.png";
		} else {
			$img = "images/daysphere-empty.png";
		}
		rawoutput("<a href=\"runmodule.php?module=lights_out&op=switch&switch=".$i."&moves=".$moves."\"><img src=\"".$img."\" style='border-none'></a>");
		addnav("","runmodule.php?module=lights_out&op=switch&switch=$i&moves=$moves");
		if ($row==$lightsperrow){
			rawoutput("<br />");
			$row=0;
		}
	}
}

function lights_out_switch($switch,$lights=false){
	global $session;
	if (!$lights){
		$lights=unserialize(get_module_pref("lights","lights_out"));
	}
	foreach($lights[$switch]['changes'] AS $change){
		if ($lights[$change]['status']==1){
			$lights[$change]['status']=0;
		} else {
			$lights[$change]['status']=1;
		}
	}
	if ($lights[$switch]['status']==1){
		$lights[$switch]['status']=0;
	} else {
		$lights[$switch]['status']=1;
	}
	set_module_pref("lights",serialize($lights),"lights_out");
	return $lights;
}

function lights_out_check($lights){
	global $session;
	$lit = 0;
	foreach ($lights AS $light){
		$lit += $light['status'];
	}
	return $lit;
}

?>