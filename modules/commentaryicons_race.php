<?php

function commentaryicons_race_getmoduleinfo(){
	$info = array(
		"name"=>"Commentary Icons: Race",
		"version"=>"2010-06-02",
		"author"=>"Dan Hall",
		"category"=>"Commentary Icons",
		"download"=>"",
		"prefs"=>array(
			"Commentary Icons: Race,title",
			"user_showmyrace" => "Show an icon for my character's race in commentary areas,bool|1",
			"user_showraces" => "Show icons for other characters' Races in commentary areas,bool|1",
		),
	);
	return $info;
}
function commentaryicons_race_install(){
	module_addhook_priority("postcomment",10);
	module_addhook("commentbuffer");
	return true;
}
function commentaryicons_race_uninstall(){
	return true;
}
function commentaryicons_race_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "postcomment":
			if (get_module_pref("user_showmyrace")){
				$racefilename = strtolower($session['user']['race']);
				$racefilename = strtr($racefilename, " '-_.!,", "");
				$racefilename = str_replace(" ", "", $racefilename);
				if ($session['user']['sex'] == 0){
					$gender = "m";
					$gendertitle = "Male";
				} else {
					$gender = "f";
					$gendertitle = "Female";
				}
				$racefilename .= $gender;
				$args['info']['icons']['race']=array(
					'icon' => "images/icons/races/".$racefilename.".png",
					'mouseover' => $gendertitle." ".$session['user']['race'],
				);
			}
			break;
		case "commentbuffer":
			if (!get_module_pref("user_showraces")){
				foreach($args AS $line=>$details){
					if (isset($details['info']['icons']['race'])){
						unset($args[$line]['info']['icons']['race']);
					}
				}
			}
		break;
		}
	return $args;
}

function commentaryicons_race_run(){
}
?>