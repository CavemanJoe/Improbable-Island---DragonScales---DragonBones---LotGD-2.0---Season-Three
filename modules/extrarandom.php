<?php

function extrarandom_getmoduleinfo(){
	$info = array(
		"name"=>"Extra Random Forest Encounters",
		"version"=>"2010-05-06",
		"author"=>"Dan Hall",
		"category"=>"Experimental",
		"download"=>"",
		"prefs"=>array(
			"forestqueue"=>"Queue of creatures to send from the Forest,text|array()",
			"graveyardqueue"=>"Queue of creatures to send from the Graveyard,text|array()",
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
			if ($args['creaturelevel'] == $session['user']['level'] && $args['creatureid'] > 1){
				$lvl = $args['creaturelevel'];
				if ($args['forest']){
					$q = @unserialize(get_module_pref("forestqueue"));
					if (!is_array($q) || count($q['creatures']) < 2 || $q['level'] != $session['user']['level']){
						debug("Queue regen");
						$q=array();
						$sql = "SELECT creatureid FROM ".db_prefix("creatures")." WHERE creaturelevel = $lvl AND forest=1";
						$result = db_query($sql);
						while ($row = db_fetch_assoc($result)){
							$q['creatures'][]=$row['creatureid'];
						}
					}
				} else if ($args['graveyard']){
					$q = @unserialize(get_module_pref("graveyardqueue"));
					if (!is_array($q) || count($q['creatures']) < 2 || $q['level'] != $session['user']['level']){
						$q=array();
						$sql = "SELECT creatureid FROM ".db_prefix("creatures")." WHERE creaturelevel = $lvl AND graveyard=1";
						$result = db_query($sql);
						while ($row = db_fetch_assoc($result)){
							$q['creatures'][]=$row['creatureid'];
						}
					}
				}
				
				$q['level'] = $session['user']['level'];
				
				//debug($q);
				
				$entry = array_rand($q['creatures']);
				$id = $q['creatures'][$entry];
				unset($q['creatures'][$entry]);
				
				if ($args['forest']){
					set_module_pref("forestqueue",serialize($q));
				} else if ($args['graveyard']){
					set_module_pref("graveyardqueue",serialize($q));
				}
				
				$sql = "SELECT * FROM ".db_prefix("creatures")." WHERE creatureid = $id";
				$result = db_query($sql);
				if (db_num_rows($result) == 0) {
					return $args;
				} else {
					$args = db_fetch_assoc($result);
				}
			}
		
		
			// //If we've seen this creature before, choose a new one
			// $pcreatures = @unserialize(get_module_pref("lastcreatures"));
			// if (!is_array($pcreatures)){
				// $pcreatures=array();
			// }
			// if ($args['creatureid']>1){
				// global $attempts;
				// if (in_array($args['creatureid'],$pcreatures)){
					// if ($attempts < 3){
						// //re-roll, getting a creature from the same level
						// debug("We've seen ".$args['creaturename']." too recently - switching monster");
						// $lvl = $args['creaturelevel'];
						// $id = $args['creatureid'];
						// if ($args['forest']){
							// $sql = "SELECT * FROM " . db_prefix("creatures") . " WHERE creaturelevel = $lvl AND forest=1 AND creatureid != $id ORDER BY rand(".e_rand().") LIMIT 1";
						// } else if ($args['graveyard']){
							// $sql = "SELECT * FROM " . db_prefix("creatures") . " WHERE creaturelevel = $lvl AND forest=1 AND creatureid != $id ORDER BY rand(".e_rand().") LIMIT 1";
						// }
						// $result = db_query($sql);
						// $args = db_fetch_assoc($result);
						// $attempts++;
						// $args = modulehook("choosebadguy",$args);
					// } else {
						// debug("Screw it!  Too many attempts!");
					// }
				// }
				// $pcreatures[]=$args['creatureid'];
				// $count = count($pcreatures);
				// $des = 8;
				// if ($count>$des){
					// for ($i=0; $i<($count-$des); $i++){
						// array_shift($pcreatures);
					// }
				// }
				// set_module_pref("lastcreatures",serialize($pcreatures));
			// }
			break;
		}
	return $args;
}

function extrarandom_run(){
}

?>