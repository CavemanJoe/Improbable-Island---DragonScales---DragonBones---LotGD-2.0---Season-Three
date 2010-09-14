<?php

/*
=======================================================
GENERAL NOTES
1. The "Attacking" player is the one who performed the search and initiated the attack.
2. The battle is started by the attacking player calling scrapbots_battle($defendingid);
=======================================================
*/

/*
=======================================================
BATTLE CALL
Pass the ID of the defending player, and this will process a battle.
=======================================================
*/

function scrapbots_battle($defenderid){
	global $session;
	$attackerid = $session['user']['acctid'];
	$armies = scrapbots_get_armies($defenderid, $attackerid);
	$duellers = scrapbots_pair_up_scrapbots ($armies);
	return;
}

/*
=======================================================
GET ARMIES
Retrieves the armies of the players involved.
=======================================================
*/

function scrapbots_get_armies($defenderid, $attackerid){
	global $session;
	//get attackers
	$sql = "SELECT id,owner,name,activated,hitpoints,brains,brawn,briskness,junglefighter,retreathp FROM ".db_prefix("scrapbots")." WHERE owner = $attackerid";
	$result = db_query($sql);
	$attacker = array();
	for ($i=0;$i<db_num_rows($result);$i++){
		$attacker[$i]=db_fetch_assoc($result);
	}
	$sql = "SELECT id,owner,name,activated,hitpoints,brains,brawn,briskness,junglefighter,retreathp FROM ".db_prefix("scrapbots")." WHERE owner = $defenderid";
	$result = db_query($sql);
	$defender = array();
	for ($i=0;$i<db_num_rows($result);$i++){
		$defender[$i]=db_fetch_assoc($result);
	}
	debug("Debugging Attacker");
	debug($attacker);
	debug("Debugging Defender");
	debug($defender);
	$armies = array("attacker"=>$attacker, "defender"=>$defender);
	//Set starting vals
	$armies['attacker']['retreatpct'] = get_module_pref("retreatpct","scrapbots",$attackerid);
	$armies['defender']['retreat'] = get_module_pref("retreatpct","scrapbots",$defenderid);
	debug ("Debugging Armies");
	debug($armies);
	return $armies;
}

/*
=======================================================
CHOOSE A SCRAPBOT TO FIGHT FROM AN ARMY
Chooses one ScrapBot from the army passed, taking Briskness value into account
=======================================================
*/

function scrapbots_choose_fighter ($army){
	debug("Choosing fighter, here is the army:");
	debug($army);
	//get a fighter at random
	$fighter = "none";
	while ($fighter == "none"){
		$bot = e_rand(0,(count($army)-2));
		if (e_rand(1,100) < ($army[$bot]['briskness'] * 10)){
			debug ("Fighter Chosen:");
			$fighter = $army[$bot];
			debug ($fighter);
		} else {
		debug("Fighter failed Briskness check.  Choosing again.");
		}
	}
	return $fighter;
}

/*
=======================================================
PAIR UP TWO SCRAPBOTS
Pairs up two ScrapBots to fight, taking their Briskness values into account
=======================================================
*/

function scrapbots_pair_up_scrapbots ($armies){
	debug("Pairing up Bots");
	$attacker = scrapbots_choose_fighter($armies['attacker']);
	debug("Debugging attacker at pairup");
	debug($attacker);
	$defender = scrapbots_choose_fighter($armies['defender']);
	$duellers = array("attacker"=>$attacker, "defender"=>$defender);
	debug("Debugging defender at pairup");
	debug($defender);
	debug("Duellers in this round:");
	debug($duellers);
	return $duellers;
}

/*
=======================================================
FIGHT BETWEEN TWO SCRAPBOTS
Processes a single fight between two ScrapBots
=======================================================
*/

function scrapbots_fight ($duellers){
	debug("Now duelling two ScrapBots");
	debug($army);

	return $fighter;
}

?>