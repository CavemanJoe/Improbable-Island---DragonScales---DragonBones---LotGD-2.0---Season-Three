<?php

//----------------------------------------------------
// STAMINA COST
// Returns the stamina cost for a paticular hex code
//----------------------------------------------------
function worldmapwn_stamina_cost($hexcode, $userid=false){
	require_once("modules/worldmapwn/config/terrain.php");
	global $terrains;
	list($code1,$code2)=explode("^",$hexcode);
	$terrain1=$terrains[$code1]["terraintype"];
	$terrain2=$terrains[$code2]["terraintype"];
	require_once("modules/staminasystem/lib/lib.php");
	if ($userid==false){
	$cost1=stamina_calculate_buffed_cost($terrain1);
	$cost2=stamina_calculate_buffed_cost($terrain2);
	} else {
	$cost1=stamina_calculate_buffed_cost($terrain1, $userid);
	$cost2=stamina_calculate_buffed_cost($terrain2, $userid);
	}
	$basecost=($cost1+$cost2) / 2;
	$roadbonus=get_module_setting("roadbonus","worldmapwn");
	$cost=$basecost*$roadbonus;
	return $cost;
}

//----------------------------------------------------
// EXPERIENCE GAIN
// Returns the experience gain from hex (with buffs)
//----------------------------------------------------
function worldmapwn_stamina_exp($hexcode, $userid=false){
	require_once("modules/worldmapwn/config/terrain.php");
	global $terrains;
	list($code1,$code2)=explode("^",$hexcode);
	$terrain1=$terrains[$code1]["terraintype"];
	$terrain2=$terrains[$code2]["terraintype"];
	require_once("modules/staminasystem/lib/lib.php");
	if ($userid==false){
	$exp1=stamina_calculate_buffed_exp($terrain1);
	$exp2=stamina_calculate_buffed_exp($terrain2);
	} else {
	$exp1=stamina_calculate_buffed_exp($terrain1, $userid);
	$exp2=stamina_calculate_buffed_exp($terrain2, $userid);
	}
	$exp=($cost1+$cost2) / 2;
	return $exp;

}

//----------------------------------------------------
// ACTION
// Process moving
//NOTE: Incomplete Same for the function below, which is copied over from staminasystem
//----------------------------------------------------
function worldmapwn_stamina_action($hexcode, $userid=false){
	require_once("modules/worldmapwn/config/terrain.php");
	global $terrains;
	list($code1,$code2)=explode("^",$hexcode);
	$terrain1=$terrains[$code1]["terraintype"];
	$terrain2=$terrains[$code2]["terraintype"];
	require_once("modules/staminasystem/lib/lib.php");
	if ($userid==false){
	$exp1=stamina_calculate_buffed_exp($terrain1);
	$exp2=stamina_calculate_buffed_exp($terrain2);
	} else {
	$exp1=stamina_calculate_buffed_exp($terrain1, $userid);
	$exp2=stamina_calculate_buffed_exp($terrain2, $userid);
	}
	$exp=($cost1+$cost2) / 2;
	return $exp;

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
?>
