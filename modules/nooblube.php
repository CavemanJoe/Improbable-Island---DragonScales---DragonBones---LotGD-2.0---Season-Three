<?php

function nooblube_getmoduleinfo(){
	$info = array(
		"name"=>"Noob Lube",
		"version"=>"2008-06-28",
		"author"=>"Dan Hall",
		"category"=>"General",
		"download"=>"",
		"settings"=>array(
			"level"=>"Level at 0DK beyond which the Noob Lube module no longer Lubes the Noob,int|10",
			"lube"=>"Favour to add,int|100",
		),
	);
	return $info;
}
function nooblube_install(){
	module_addhook("battle-defeat");
	module_addhook("newday");
	return true;
}
function nooblube_uninstall(){
	return true;
}
function nooblube_dohook($hookname,$args){
	global $session, $badguy;
	switch($hookname){
	case "battle-defeat":
		//check to see if they're alive first, 'cause otherwise it'll add favour when they get wiped out on the FailBoat
		if ($session['user']['alive']==false){
			break;
		}
		//check to see if they've got any hitpoints, for stuff like the Tattoo Monster and defeating their masters
		if ($session['user']['hitpoints']>0){
			break;
		}
		$maxlevel = get_module_setting("level");
		$currentlevel = $session['user']['level'];
		if ($currentlevel < $maxlevel){
			if ($session['user']['dragonkills'] == 0){
				if ($session['user']['deathpower'] < 100){
					$session['user']['deathpower']=get_module_setting("lube");
				}
				$tutormsg = translate_inline("`#What did I tell you about being careful?  Bloody newbies.  Okay, those nice gentlemen coming towards you will pick you up and drag you to my FailBoat, but don't worry about it too much.  Just come and see me below decks and I'll get you put on the next boat back to the Island.  Don't get used to it though, matey - once you hit level ten, you'll have to fight for your freedom like everyone else.");
				if ($tutormsg) tutor_talk("%s", $tutormsg);
			}
		}
		break;
	}
	return $args;
}
?>