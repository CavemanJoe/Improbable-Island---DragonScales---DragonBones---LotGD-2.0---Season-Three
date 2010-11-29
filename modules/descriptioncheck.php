<?php

function descriptioncheck_getmoduleinfo(){
	$info = array(
		"name"=>"Creature Description Tester",
		"version"=>"2009-11-11",
		"author"=>"Dan Hall",
		"category"=>"Administrative",
		"download"=>"",
		"prefs"=>array(
			"Creature Descriptions Checker,title",
			"access"=>"Player has access to the Creature Description Checker and is not a complete plonker,bool|0",
		),
	);
	return $info;
}

function descriptioncheck_install(){
	module_addhook("superuser");
	return true;
}

function descriptioncheck_uninstall(){
	return true;
}

function descriptioncheck_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "superuser":
			if (get_module_pref("access")){
				addnav("Creature Descriptions Checker","runmodule.php?module=descriptioncheck&op=start");
			};
		break;
	}
	return $args;
}

function descriptioncheck_run(){
	global $session;
	page_header("Creature Descriptions Checker Thing");
	output("This handy page shows all creature descriptions, win messages, lose messages, and failboat/jungle status, complete with colour codes.`n`nPlease use this page sparingly, as it's kinda resource-intensive.  Ta!`n`n");
	$sql = "SELECT * FROM " . db_prefix("creatures") . " ORDER BY creaturelevel,creaturename";
	$result = db_query($sql);
	$n = db_num_rows($result);
	for ($i=0; $i<$n; $i++){
		$row = db_fetch_assoc($result);
		$desc = stripslashes(get_module_objpref("creatures",  $row['creatureid'], "description", "creatureaddon"));
		if ($desc==""){
			$desc = "No Description!";
		}
		output("`0`bCreature: %s | Weapon: %s | Level: %s | Jungle: %s | FailBoat: %s`nDescription:`b`n-----`n%s`n-----`n`n",$row['creaturename'],$row['creatureweapon'],$row['creaturelevel'],$row['forest'],$row['graveyard'],$desc);
	}
	addnav("Back to the Grotto","superuser.php");
	page_footer();
}

?>