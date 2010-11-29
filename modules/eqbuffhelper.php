<?php

function eqbuffhelper_getmoduleinfo(){
	$info = array(
		"name"=>"Equipment Buff Helper",
		"version"=>"2008-08-25",
		"author"=>"Dan Hall",
		"category"=>"Improbable",
		"download"=>"",
	);
	return $info;
}
function eqbuffhelper_install(){
	module_addhook("forest");
	module_addhook("newday");
	module_addhook("village");
	return true;
}
function eqbuffhelper_uninstall(){
	return true;
}
function eqbuffhelper_dohook($hookname,$args){
	global $session;
	switch($hookname){
	case "forest":
		eqbuffhelper_applyorstrip();
		break;
	case "village":
		eqbuffhelper_applyorstrip();
		break;
	case "newday":
		eqbuffhelper_applyorstrip();
		break;
	}
	return $args;
}
function eqbuffhelper_applyorstrip(){
	$id = get_module_pref("weaponid","mysticalshop");
	if ($id){
		if ($id==80){
			apply_buff('ccfail',array(
				"roundmsg"=>"`\$Your Chainsaw-Chuks have a depressingly predictable effect - complete unpredictability.",
				"name"=>"`\$Chainsaw-Chuk Fail",
				"rounds"=>-1,
				"minioncount"=>6,
				"maxgoodguydamage"=>20,
				"mingoodguydamage"=>0,
				"effectmsg"=>"`\$One of your chainsaws bungees back at you and rips into you for `^{damage} `\$damage!",
				"schema"=>"module-eqbuffhelper"
			));
		}
		if ($id==86 || $id==87){
			apply_buff('sklfail',array(
				"roundmsg"=>"`\$Your SpiderKitty Launcher recoils like a box of a dozen shotguns, spraying wriggling SpiderKitties in every direction.  Including straight back.",
				"name"=>"`\$SpiderKitty Launcher Fail",
				"rounds"=>-1,
				"minioncount"=>10,
				"maxgoodguydamage"=>20,
				"mingoodguydamage"=>0,
				"effectmsg"=>"`\$One of your SpiderKitties attaches itself to your face and scratches you for `^{damage} `\$damage!",
				"effectfailmsg"=>"`\$One of your SpiderKitties attaches itself to your face and scratches you for `^{damage} `\$damage!",
				"schema"=>"module-eqbuffhelper"
			));
		}
	}
	if ($id!=80 && $id!=86 && $id!=87 || !$id){
		strip_buff("sklfail");
		strip_buff("ccfail");
	}
	return $args;
}
?>