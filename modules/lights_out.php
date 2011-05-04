<?php

function lights_out_getmoduleinfo(){
	$info = array(
		"name"=>"Lights Out",
		"version"=>"2010-02-02",
		"author"=>"Dan Hall",
		"category"=>"Improbable",
		"download"=>"",
		"settings"=>array(
			"moneyin"=>"Amount of money taken in,int|0",
			"moneyout"=>"Amount of money paid out,int|0",
		),
		"prefs"=>array(
			"lights"=>"Player's Lights array,textarea|array()",
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
	
	//playtesting
	$numlights = 9;
	$lightsperrow = 3;
	$startlights = 5;
	$maxchangesperlight = 4;
	
	switch (httpget('op')){
		case "start":
			$lights = array();
			for ($lamp=1; $lamp<=9; $lamp++){
				$lights[$lamp]['status']=0;
				
				$numberofchanges = e_rand(2,4);
				
				$lampstochange = array(
					1 => "1",
					2 => "2",
					3 => "3",
					4 => "4",
					5 => "5",
					6 => "6",
					7 => "7",
					8 => "8",
					9 => "9",
				);
				
				unset($lampstochange[$lamp]);
				
				$switches = array();
				for ($c = 1; $c <= $numberofchanges; $c++){
					$sw = array_rand($lampstochange);
					$switches[] = $sw;
					unset($lampstochange[$sw]);
				}
				$lights[$lamp]['changes'] = $switches;
			}
			debug($lights);
			
			//Now randomly poke some lights
			for ($i=1; $i<=$startlights; $i++){
				$sw = e_rand(1,$numlights);
				$lights = lights_out_switch($sw,$lights);
			}
			
			$spref = serialize($lights);
			set_module_pref("lights",$spref);
			$moves=0;
			$jackpot = 200;
			lights_out_show($lights,$moves,$jackpot);
		break;
		case "switch":
			$moves=httpget('moves');
			$moves++;
			$jackpot = httpget('jackpot');
			output("Current jackpot: %s`n",$jackpot);
			$lights = lights_out_switch(httpget('switch'));
			$jackpot += 5;
			lights_out_show($lights,$moves,$jackpot);
			$lit = lights_out_check($lights);
			if (!$lit){
				output("Congratulations!  You won %s Req, for your stake of %s!",$jackpot,$moves*10);
			} else {
				output("There are %s lights left to turn off.  You have made %s moves.`n",$lit,$moves);
			}
		break;
	}
	page_footer();
}

function lights_out_show($lights=false,$moves=0,$jackpot=200){
	global $session;
	if (!$lights){
		$lights=unserialize(get_module_pref("lights","lights_out"));
	}
	$numlights = 9;
	$lightsperrow = 3;
	$row = 0;
	for ($i=1; $i<=$numlights; $i++){
		$row++;
		if ($lights[$i]['status']){
			$img = "images/cogswitch/cogswitch_".$i."_on.png";
		} else {
			$img = "images/cogswitch/cogswitch_".$i."_off.png";
		}
		rawoutput("<a href=\"runmodule.php?module=lights_out&op=switch&switch=".$i."&moves=".$moves."&jackpot=".$jackpot."\"><img src=\"".$img."\" style='border-none'></a>");
		addnav("","runmodule.php?module=lights_out&op=switch&switch=$i&moves=$moves&jackpot=$jackpot");
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
	debug($lights);
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