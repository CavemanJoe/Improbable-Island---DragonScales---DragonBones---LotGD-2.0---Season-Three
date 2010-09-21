<?php

/*
Altered core files to make the Stamina system work,based on 1.1.1:

battle.php
Added two hooks, one at the start of each round and one at the end

newday.php
Commented out portions of the code pertaining to spending DK points on forest fights
Commented out "Turns for today set to [whatever]"
*/

/*
=======================================================
GET DEFAULT ACTION LIST
Returns arrays for every Action default.
=======================================================
*/

function get_default_action_list() {
	$actions = unserialize(get_module_setting("actionsarray", "staminasystem"));
	if (!is_array($actions)) {
		$actions = array();
		set_module_setting("actionsarray", serialize($actions), "staminasystem");
	}
	$actions = unserialize(get_module_setting("actionsarray", "staminasystem"));
	return $actions;
}

/*
=======================================================
GET PLAYER ACTION LIST
Returns arrays for every action for the given player.
=======================================================
*/

function get_player_action_list($userid=false) {
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	$actions = unserialize(get_module_pref("actions", "staminasystem", $userid));
	if (!is_array($actions)) {
		$actions = array();
		set_module_pref("actions", serialize($actions), "staminasystem", $userid);
	}
	$actions=unserialize(get_module_pref("actions", "staminasystem", $userid));
	return $actions;
}

/*
=======================================================
GET ACTION DETAILS
Returns full info array for a given Action in a player's inventory.
Also sets default values if the player has not yet performed that action.
Returns False if the action is not installed.
=======================================================
*/

function get_player_action($action, $userid=false) {
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	$playeractions=unserialize(get_module_pref("actions","staminasystem",$userid));
	//Check to see if this action is set for this player, and if not, set it
	if (!isset($playeractions[$action])){
		//debug("Action ".$action." not set!");
		$defaultactions = get_default_action_list();
		if (isset($defaultactions[$action])){
			//debug($defaultactions[$action]);
			$playeractions[$action] = $defaultactions[$action];
			$playeractions[$action]['lvl'] = 0;
			$playeractions[$action]['naturalcost'] = $defaultactions[$action]['maxcost'];
			set_module_pref("actions", serialize($playeractions), "staminasystem", $userid);
			return($playeractions[$action]);
		} else {
			return false;
		}
	} else {
		return($playeractions[$action]);
	}
}

/*
*******************************************************
INSTALL ACTION
Used in modules' Install fields, this sets the default values for this Action.
*******************************************************
*/

function install_action($actionname, $action){
	global $session;
	$defaultactions = get_default_action_list();
	$defaultactions[$actionname] = $action;
	set_module_setting("actionsarray",serialize($defaultactions),"staminasystem");
	return true;
}

/*
*******************************************************
UNINSTALL ACTION
Cleans up all data pertaining to an action.  Use this in your module's Uninstall function.
*******************************************************
*/

function uninstall_action($actionname) {
	//Remove information from the actions array
	$defaultactions = get_default_action_list();
	unset($defaultactions[$actionname]);
	set_module_setting("actionsarray",serialize($defaultactions),"staminasystem");
	//Now remove the action from each user's modulepref
	$sql = "SELECT acctid FROM ".db_prefix("accounts")."";
	$results = db_query($sql);
	for ($i=0; $i<db_num_rows($results);$i++){
		$row = db_fetch_assoc($results);
		$playeractions = unserialize(get_module_pref("actions","staminasystem",$row['acctid']));
		unset($playeractions[$actionname]);
		set_module_pref("actions",serialize($playeractions),"staminasystem",$row['acctid']);
	}
	return true;
}

/*
*******************************************************
SET A BUFF
Temporarily increase or reduce the cost of and/or experience gained from performing an action.
*******************************************************
*/

function apply_stamina_buff($referencename, $buff, $userid=false){
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	$bufflist = unserialize(get_module_pref("buffs", "staminasystem", $userid));
	$bufflist[$referencename] = $buff;
	set_module_pref("buffs", serialize($bufflist), "staminasystem", $userid);
}

/*
*******************************************************
CALCULATE ACTION COST
Returns the cost of performing an action, taking buffs into account.
*******************************************************
*/

function stamina_calculate_buffed_cost($action, $userid=false){
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	$active_action_buffs = stamina_get_active_buffs($action, $userid);
	debug($active_action_buffs,true);
	$actiondetails = get_player_action($action, $userid);
	//debug($actiondetails);
	$naturalcost = $actiondetails['naturalcost'];
	$buffedcost = $naturalcost;
	if (is_array($active_action_buffs)){
		foreach($active_action_buffs as $key => $values){
			$buffedcost = $buffedcost * $values['costmod'];
		}
	}
	return $buffedcost;
}

/*
*******************************************************
CALCULATE EXPERIENCE GAIN
Returns the experience gained for the given action, taking buffs into account
*******************************************************
*/

function stamina_calculate_buffed_exp($action, $userid=false){
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	$active_action_buffs = stamina_get_active_buffs($action, $userid);
	$actiondetails = get_player_action($action, $userid);
	$buffedexp = 100;
	if (is_array($active_action_buffs) && $active_action_buffs){
		foreach($active_action_buffs as $buff => $values){
			$buffedexp = round($buffedexp * $values['expmod']);
		}
	}
	return $buffedexp;
}

/*
*******************************************************
GET ACTIVE BUFFS
Returns an array of buffs that relate to a particular action, or class of actions.
*******************************************************
*/

function stamina_get_active_buffs($action, $userid=false){
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	
	$bufflist = unserialize(get_module_pref("buffs", "staminasystem", $userid));
	$actiondetails = get_player_action($action, $userid);
	
	if (is_array($bufflist)) {
		foreach($bufflist as $buff => $values){
			if ($values['action'] == $action || $values['action']=="Global" || $values['class']==$actiondetails['class']){
				$active_action_buffs[$buff] = $values;
			}
		}
	}
	return($active_action_buffs);
}

/*
*******************************************************
ADVANCE BUFFS
Removes a round from buffs related to the given action, then removes the buff from the array if rounds are zero.
Also outputs round and wearoff messages.
*******************************************************
*/

function stamina_advance_buffs($action, $userid=false) {
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	
	$bufflist = unserialize(get_module_pref("buffs", "staminasystem", $userid));
	$actiondetails = get_player_action($action, $userid);
	
	$write=0;
	
	if (is_array($bufflist)){
		foreach($bufflist as $buff => $values){
			if ($values['action'] == $action || $values['action']=="Global" || $values['class']==$actiondetails['class']){
				if ($values['roundmsg']) output_notl("%s`n",stripslashes($values['roundmsg']));
				if ($values['rounds'] > 0){
					$values['rounds']--;
					$write=1;
				}
				if ($values['rounds']==0){
					if ($values['wearoffmsg']) output_notl("%s`n",stripslashes($values['wearoffmsg']));
					$write=1;
					unset($bufflist[$buff]);
				} else {
					$bufflist[$buff]=$values;
				}
			}
		}
	}
	if ($write){
		if (count($bufflist)!=0){
			set_module_pref("buffs", serialize($bufflist), "staminasystem", $userid);
		} else {
			set_module_pref("buffs", "array()", "staminasystem", $userid);
		}
	}
	return true;
}

/*
*******************************************************
STRIP A BUFF
Removes a Stamina buff.
*******************************************************
*/

function strip_stamina_buff($buff, $userid=false){
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	$bufflist = unserialize(get_module_pref("buffs", "staminasystem", $userid));
	if (is_array($bufflist)){
		unset($bufflist[$buff]);
		set_module_pref("buffs", serialize($bufflist), "staminasystem", $userid);
	}
}

/*
*******************************************************
REMOVE ALL BUFFS
Empties the player's Buffs array.  Used at newday.
*******************************************************
*/

function stamina_strip_all_buffs($userid=false) {
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	set_module_pref("buffs", "array()", "staminasystem", $userid);
	return true;
}

/*
*******************************************************
GET DISPLAY COST
Returns a percentage of the player's total Stamina that is used when performing this action, by default to three decimal places.
*******************************************************
*/

function stamina_getdisplaycost($action, $precision=3, $userid=false){
	global $session;
	$costval = stamina_calculate_buffed_cost($action, $userid);
	$costpct = round(($costval/1000000)*100, $precision);
	return $costpct;
}

/*
*******************************************************
TAKE ACTION COST
Calculates buffs, removes stamina, and returns the amount taken.
*******************************************************
*/

function stamina_take_action_cost($action, $userid=false) {
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	$totalcost = stamina_calculate_buffed_cost($action, $userid);
	removestamina($totalcost, $userid);
	return $totalcost;
}

/*
*******************************************************
AWARD EXPERIENCE
Calculates buffs, awards experience, returns experience awarded.
*******************************************************
*/

function stamina_award_exp($action, $userid=false) {
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	$totalexp = stamina_calculate_buffed_exp($action, $userid);
	$actionlist = get_player_action_list($userid);
	$actionlist[$action]['exp'] += $totalexp;
	set_module_pref("actions",serialize($actionlist),"staminasystem",$userid);
	return $totalexp;
}


/*
*******************************************************
PROCESS ACTION
Calculates buffs, awards experience, upgrades level, removes cost, advances buffs, returns Stamina used and Experience gained.
*******************************************************
*/

function process_action($action, $userid=false) {
	global $session, $actions_used;
	if ($userid === false) $userid = $session['user']['acctid'];
	$info_to_return = array("points_used" => 0, "exp_earned" => 0);
	$info_to_return['points_used']  = stamina_take_action_cost($action, $userid);
	$info_to_return['exp_earned']  = stamina_award_exp($action, $userid);
	stamina_advance_buffs($action, $userid);
	$info_to_return['lvlinfo'] = stamina_level_up($action, $userid);
	
	$actions_used[$action]['exp_earned']+=$info_to_return['exp_earned'];
	$actions_used[$action]['lvlinfo']=$info_to_return['lvlinfo'];
	
	//We want to put a ladder of some sort in here, where the player can see the player above them in the HOF and the player below them as well.
	
	return $info_to_return;
}

//
// GET STAMINA VALUES
// Returns the current Stamina values for the player.
// Syntax:
// get_stamina(type, realvalue, userid);
/*
Type:
0 = Red
1 (default) = Amber
2 = Green
3 = Total
4 = Starting value (will not return as percentage)

Realvalue:
false (default) = Returns a percentage value of total.
true = Returns actual value.

Example usage:

$stamina = get_stamina();
Will return a percentage of the amber stamina value for the current player, so that the module author can adjust the outcome based on how knackered the player is.
You can return the red, green and total values too.  Example:

$red = get_stamina(0);
Returns the red value as a percentage.

$green = get_stamina(2, 1);
Returns the green value in terms of actual Stamina points.

$total = get_stamina(3, 1);
Returns the player's total Stamina points.

*/

function get_stamina($type = 1, $realvalue = false, $userid = false) {
	global $session;
	
	if ($userid === false) $userid = $session['user']['acctid'];
	
	$totalstamina = get_module_pref("stamina", "staminasystem", $userid);
	$maxstamina = 1000000;
	$totalpct = ($totalstamina/$maxstamina)*100;
	$redpoint = get_module_pref("red", "staminasystem", $userid);
	$amberpoint = get_module_pref("amber", "staminasystem", $userid);
	
	$greenmax = $maxstamina - $redpoint - $amberpoint;
	$greenvalue = $totalstamina - $redpoint - $amberpoint;
	$greenpct = ($greenvalue/$greenmax)*100;
	if ($greenvalue < 0) {
		$greenvalue = 0;
		$greenpct = 0;
	}
	
	$ambermax = $amberpoint;
	$ambervalue = $totalstamina - $redpoint;
	$amberpct = ($ambervalue/$ambermax)*100;
	if ($ambervalue < 0) {
		$ambervalue = 0;
		$amberpct = 0;
	}
	if ($ambervalue > $amberpoint) {
		$ambervalue = $amberpoint;
		$amberpct = 100;
	}
	
	$redmax = $redpoint;
	$redvalue = $totalstamina;
	$redpct = ($redvalue/$redmax)*100;
	if ($redvalue > $redpoint) {
		$redvalue = $redpoint;
		$redpct = 100;
	}
	
	switch ($type) {
		case 0:
			if ($realvalue === false){
				$returnvalue = $redpct;
			} else {
				$returnvalue = $redvalue;
			}
			break;
		case 1:
			if ($realvalue === false){
				$returnvalue = $amberpct;
			} else {
				$returnvalue = $ambervalue;
			}
			break;
		case 2:
			if ($realvalue === false){
				$returnvalue = $greenpct;
			} else {
				$returnvalue = $greenvalue;
			}
			break;
		case 3:
			if ($realvalue === false){
				$returnvalue = $totalpct;
			} else {
				$returnvalue = $totalstamina;
			}
			break;
		case 4:
			$returnvalue = $maxstamina;
			break;
	}
	
	return $returnvalue;
}


/*
*******************************************************
LEVEL UP
Determines whether the player is ready to level up, levels up if appropriate, returns start and end of EXP range for this level OR player has levelled up.
*******************************************************
*/

function stamina_level_up($action, $userid = false) {
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];

	$returninfo = array();
	$stop = 0;
	
	$actions = get_player_action_list($userid);
	if ($actions[$action]['lvl']>=100){
		return false;
	}
	
	while ($stop == 0){
		$actions = get_player_action_list($userid);
		$currentexp = $actions[$action]['exp'];
		$currentlvl = $actions[$action]['lvl'];
		$first = $actions[$action]['firstlvlexp'];
		$increment = $actions[$action]['expincrement'];
		$stop = 1;
		//Determine the next level's EXP requirements
		$addup = array();
		$addup[0] = $first;
		for ($i=1; $i<=100; $i++){
			$addup[$i] = round($addup[$i-1]*$increment);
		}

		$levels = array();
		$levels[0] = $first;

		for ($i=1; $i<=100; $i++){
			$levels[$i] = ($levels[$i-1] + $addup[$i]);
		}
		
		if ($currentlvl != 0){
			$currentlvlexp = $levels[$currentlvl];
		} else {
			$currentlvlexp = 0;
		}
		
		$nextlvlexp = $levels[$currentlvl];
		$currentlvlexp = $levels[$currentlvl-1];
		
		$returninfo['exp'] = $currentexp;
		$returninfo['lvl'] = $currentlvl;
		$returninfo['nextlvlexp'] = $nextlvlexp;
		$returninfo['currentlvlexp'] = $currentlvlexp;
		
		//Check if player's exp is more than level requirement, and level up if true
		if ($currentexp > $nextlvlexp && $actions[$action]['lvl']<=100){
			$stop = 0;
			//level up
			$actions[$action]['lvl']++;
			//reduce costs
			$actions[$action]['naturalcost'] -= $actions[$action]['costreduction'];
			//write back array to modulepref
			set_module_pref("actions", serialize($actions), "staminasystem", $userid);
			//set "levelledup" to true, so that the module can output levelling up text
			$returninfo['levelledup'] = true;
			$returninfo['newlvl'] = $actions[$action]['lvl'];
		}
	}
	return $returninfo;
}

/*
*******************************************************
PROCESS NEW DAY
Strips buffs, resets Stamina to starting value.
*******************************************************
*/

function stamina_process_newday($userid = false) {
	global $session;
	
	if ($userid === false) $userid = $session['user']['acctid'];
	
	modulehook("stamina-newday-intercept");
	// remove buffs
	stamina_strip_all_buffs($userid);

	$startingstamina = 1000000;
	set_module_pref("stamina",$startingstamina,"staminasystem",$userid);
	set_module_pref("amber",400000,"staminasystem",$userid);
	set_module_pref("red",200000,"staminasystem",$userid);
	
	modulehook("stamina-newday");
	
	return true;
}

/*
*******************************************************
PROCESS DRAGON KILL
Retains a percentage of experience points, and resets action costs
NOW DEPRECATED - The Stamina system no longer removes any info between Dragon Kills.
*******************************************************
*/

// function stamina_process_dragonkill($userid = false){
	// global $session;
	// if ($userid === false) $userid = $session['user']['acctid'];
	// $actions = get_player_action_list($userid);
	// foreach($actions AS $key => $values){
		// $values['dkexp'] = round(($values['exp'] / 100) * $values['dkpct']) + $values['dkexp'];
		// $values['exp'] = $values['dkexp'];
		// $values['level'] = 0;
		// $values['naturalcost'] = $values['maxcost'];
		// $actions[$key]=$values;
	// };
	// set_module_pref("actions", serialize($actions), "staminasystem", $userid);
	// return true;
// }

/*
*******************************************************
ADD AND REMOVE STAMINA
Simple functions to add or remove Stamina from players.
*******************************************************
*/

function addstamina($amount, $userid = false){
	global $session;
	
	//debug("Adding ".$amount." Stamina points");
	if ($userid === false) $userid = $session['user']['acctid'];
	$newstamina = get_module_pref("stamina", "staminasystem", $userid) + $amount;
	set_module_pref("stamina",$newstamina,"staminasystem",$userid);
	
	return $newstamina;
}

function removestamina($amount, $userid = false){
	global $session;
	
	if ($userid === false) $userid = $session['user']['acctid'];
	
	
	$newstamina = get_module_pref("stamina", "staminasystem", $userid) - $amount;
	if ($newstamina < 0){
		$newstamina = 0;
	}
	set_module_pref("stamina",$newstamina,"staminasystem",$userid);
	
	return $newstamina;
}

?>