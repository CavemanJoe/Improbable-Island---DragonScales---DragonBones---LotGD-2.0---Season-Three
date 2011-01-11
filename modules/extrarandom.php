<?php

function extrarandom_getmoduleinfo(){
	$info = array(
		"name"=>"Extra Random Forest Encounters",
		"version"=>"2010-05-06",
		"author"=>"Dan Hall",
		"category"=>"Experimental",
		"download"=>"",
		"prefs"=>array(
			"lastcreatures"=>"Serialized array of the most recent creatures the player has seen,text|array()",
		),
	);
	return $info;
}

function extrarandom_install(){
	module_addhook_priority("choosebadguy",10);
	return true;
}

function extrarandom_uninstall(){
	return true;
}

function extrarandom_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "choosebadguy":
			//If we've seen this creature before, choose a new one
			$pcreatures = @unserialize(get_module_pref("lastcreatures"));
			if (!is_array($pcreatures)){
				$pcreatures=array();
			}
			if ($args['creatureid']>1){
				global $attempts;
				if (in_array($args['creatureid'],$pcreatures)){
					if ($attempts < 3){
						//re-roll, getting a creature from the same level
						debug("We've seen ".$args['creaturename']." too recently - switching monster");
						$lvl = $args['creaturelevel'];
						$id = $args['creatureid'];
						if ($args['forest']){
							$sql = "SELECT * FROM " . db_prefix("creatures") . " WHERE creaturelevel = $lvl AND forest=1 AND creatureid != $id ORDER BY rand(".e_rand().") LIMIT 1";
						} else if ($args['graveyard']){
							$sql = "SELECT * FROM " . db_prefix("creatures") . " WHERE creaturelevel = $lvl AND forest=1 AND creatureid != $id ORDER BY rand(".e_rand().") LIMIT 1";
						}
						$result = db_query($sql);
						$args = db_fetch_assoc($result);
						$attempts++;
						$args = modulehook("choosebadguy",$args);
					} else {
						debug("Screw it!  Too many attempts!");
					}
				}
				$pcreatures[]=$args['creatureid'];
				$count = count($pcreatures);
				$des = 8;
				if ($count>$des){
					for ($i=0; $i<($count-$des); $i++){
						array_shift($pcreatures);
					}
				}
				set_module_pref("lastcreatures",serialize($pcreatures));
			}
			break;
		}
	return $args;
}

function extrarandom_run(){
}

?>