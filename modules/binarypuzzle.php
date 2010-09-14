<?php

function binarypuzzle_getmoduleinfo(){
	$info = array(
		"name"=>"Binary Puzzle Thing",
		"version"=>"2010-02-02",
		"author"=>"Dan Hall",
		"category"=>"Improbable",
		"download"=>"",
		"settings"=>array(
			"played"=>"Total coins played,int|0",
		),
		"prefs"=>array(
			"switches"=>"Player's Switches array,textarea|array()",
			"clues"=>"Player's Clues array,textarea|array()",
			"bestmoves"=>"Player's lowest number of moves,int|0",
		)
	);
	return $info;
}

function binarypuzzle_install(){
	module_addhook("village");
	return true;
}

function binarypuzzle_uninstall(){
	return true;
}

function binarypuzzle_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "village":
			addnav("Play Binary Puzzle Thing","runmodule.php?module=binarypuzzle&op=start");
			break;
		}
	return $args;
}

function binarypuzzle_run(){
	global $session;
	page_header("Binary Puzzle Thing!");
	addnav("O?Back to the Outpost","village.php");
	switch (httpget('op')){
		case "start":
			//first set up the board
			$switches = array(
			1=>array("val"=>1,"status"=>0),
			2=>array("val"=>2,"status"=>0),
			3=>array("val"=>4,"status"=>0),
			4=>array("val"=>8,"status"=>0),
			5=>array("val"=>16,"status"=>0),
			6=>array("val"=>32,"status"=>0),
			7=>array("val"=>64,"status"=>0),
			8=>array("val"=>128,"status"=>0),
			);
			shuffle($switches);
			set_module_pref("switches",serialize($switches));
			//Now set up the clues
			$clues=array();
			for ($i=1; $i<=8; $i++){
				$toprange = $i*32;
				$bottomrance = $toprange-32;
				$clues[$i]=e_rand($bottomrange,$toprange);
			}			$goal = e_rand(129,255);
			set_module_pref("clues",serialize($clues));
			binarypuzzle_show($switches,$goal);
		break;
		case "switch":
			$sw = httpget('switch');
			$goal = httpget('goal');
			$switches = binarypuzzle_switch($sw);
			binarypuzzle_show($switches,$goal);
		break;
	}
	page_footer();
}

function binarypuzzle_show($switches=0,$goal){
	global $session;
	if (!$switches){
		$switches = unserialize(get_module_pref("switches","binarypuzzle"));
	}
	$total = 0;
	foreach ($switches AS $switch=>$vals){
		$sw = $switch;
		if ($vals['status']){
			$total+=$vals['val'];
		}
		rawoutput("<a href=\"runmodule.php?module=binarypuzzle&op=switch&switch=".$sw."&goal=".$goal."\">".$vals['status']."</a>");
		addnav("","runmodule.php?module=binarypuzzle&op=switch&switch=$sw&goal=$goal");
	}
	output("`nGoal number: %s`n",$goal);
	//Now check to see if any of the clues are active
	$clues = unserialize(get_module_pref("clues","binarypuzzle"));
	foreach ($clues AS $clue){
		if ($clue == $total){
			output("`@Clue number: %s`n",$clue);
		} else {
			output("`0Clue number: %s`n",$clue);
		}
	}
}

function binarypuzzle_switch($switch,$switches=0){
	global $session;
	if (!$switches){
		$switches = unserialize(get_module_pref("switches","binarypuzzle"));
	}
	if (!$switches[$switch]['status']){
		$switches[$switch]['status']=1;
	} else {
		$switches[$switch]['status']=0;
	}
	set_module_pref("switches",serialize($switches),"binarypuzzle");
	return $switches;
}
?>