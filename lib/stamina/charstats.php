<?php

global $charstat_info, $badguy, $actions_used;
require_once("lib/stamina/stamina.php");
require_once("lib/userprefs.php");
$version=getsetting("installer_version","1.1.1");
if ($version<"1.2.1.2") {$ismodule=true;}
//Look at the number of Turns we're missing.  Default is ten, and we'll add or remove some Stamina depending, as long as we're not in a fight.
if ($ismodule==true){
	$turnsemulationbase=get_module_setting("turns_emulation_base","staminasystem");
	$turnsemulationceiling=get_module_setting("turns_emulation_ceiling","staminasystem");
}else {
	$turnsemulationbase=getsetting("staminasystem_turns_emulation_base","20000");
	$turnsemulationceiling=getsetting("staminasystem_turns_emulation_ceiling","20000");
}
if ($turnsemulationbase!=0 ){
	if (!isset($badguy)){
		$stamina = e_rand($turnsemulationbase,$turnsemulationceiling);
		while ($session['user']['turns'] < 10){
			$session['user']['turns']++;
			debug("Turns Removed");
			removestamina($stamina);
		}
		while ($session['user']['turns'] > 10){
			$session['user']['turns']--;
			debug("Turns Added");
			addstamina($stamina);
		}
	}
}

//add recent actions to the _top_ of the charstat column
if (!isset($charstat_info['Recent Actions'])){
	//Put yer thing down, flip it an' reverse it
	$yarr = array_reverse($charstat_info);
	$yarr['Action Rankings'] = array();
	$yarr['Recent Actions']=array();
	$charstat_info = array_reverse($yarr);
}

if (isset($actions_used)){
	foreach($actions_used AS $action=>$vals){
		if (!$actions_used[$action]['lvlinfo']['currentlvlexp']){
			$actions_used[$action]['lvlinfo']['currentlvlexp']=1;
		}
		$pct = (($actions_used[$action]['lvlinfo']['exp']-$actions_used[$action]['lvlinfo']['currentlvlexp']) / ($actions_used[$action]['lvlinfo']['nextlvlexp']-$actions_used[$action]['lvlinfo']['currentlvlexp'])) * 100;
		$nonpct = 100 - $pct;
		$disp = "Lv".$actions_used[$action]['lvlinfo']['lvl']." (+`@".$actions_used[$action]['exp_earned']."`^ xp)<table style='border: solid 1px #000000;' bgcolor='red'  cellpadding='0' cellspacing='0' width='70' height='5'><tr><td width='$pct%' bgcolor='white'></td><td width='$nonpct%'></td></tr></table>";
		setcharstat("Recent Actions",$action,$disp);
		
		if ($ismodule==true){
			if (get_module_pref("user_minihof","staminasystem")){
				$st = microtime(true);
				stamina_minihof($action);
				$en = microtime(true);
				$to = $en - $st;
				debug("Minihof: ".$to);
			} else {
				if (get_userpref("stamina_minihof")){
				$st = microtime(true);
				stamina_minihof($action);
				$en = microtime(true);
				$to = $en - $st;
				debug("Minihof: ".$to);
				}
		}	}
	}
}

//debug($actions_used);


//Then, since Turns are pretty well baked into core and we don't want to be playing around with adding turns just as they're needed for core to operate, we'll just add ten turns here and forget all about it...
$session['user']['turns'] = 10;
//these will be go in version 1.3


//Display the actual Stamina bar
//require_once("lib/stamina/stamina.php");
require_once("lib/stamina/stamina.php");
if ($ismodule==true){
	$redpoint = get_module_pref("red","staminasystem");
	$amberpoint = get_module_pref("amber","staminasystem");
}else {
	$redpoint = get_userpref("stamina_red",200000);
	$amberpoint = get_userpref("stamina_amber",400000);
}
$daystamina = 1000000;
$redpct = get_stamina(0);
$amberpct = get_stamina(1);
$greenpct = get_stamina(2);

//$stat = "<a href='runmodule.php?module=staminasystem&op=show' target='_blank' onclick=\"".popup("runmodule.php?module=staminasystem&op=show").";return false;\">Stamina</a>";
$stat = "<a href='stamina.php?op=show' target='_blank' onclick=\"".popup("stamina.php?op=show").";return false;\">Stamina</a>";

$pctoftotal = round($stamina / $daystamina * 100, 2);

$greentotal = round((($daystamina - $redpoint - $amberpoint)/$daystamina)*100);
$ambertotal = round(((($daystamina - $redpoint)/$daystamina)*100) - $greentotal);
$redtotal = (100 - $greentotal) - $ambertotal;

$greenwidth = (($greentotal / 100) * $greenpct);
$amberwidth = (($ambertotal / 100) * $amberpct);
$redwidth = (($redtotal / 100) * $redpct);

$colorgreen = "#00FF00";
$coloramber = "#FFA200";
$colorred = "#FF0000";
$colordarkgreen = "#003300";
$colordarkamber = "#2F1E00";
$colordarkred = "#330000";
$colorbackground = $colordarkgreen;

if ($greenpct == 0){
	$colorgreen = $colordarkamber;
	$colorbackground = $colordarkamber;
}
if ($amberpct == 0){
	$colorgreen = $colordarkred;
	$coloramber = $colordarkred;
	$colorbackground = $colordarkred;
}

$pctgrey = (((100 - $greenwidth)-$amberwidth)-$redwidth);

$new = "";
$new .= "<font color=$colorbackground>$pctoftotal%</font><br><table style='border: solid 1px #000000' bgcolor='$colorbackground' cellpadding='0' cellspacing='0' width='70' height='5'><tr><td width='$redwidth%' bgcolor='$colorred'></td><td width='$amberwidth%' bgcolor='$coloramber'></td><td width='$greenwidth%' bgcolor='$colorgreen'></td><td width='$pctgrey%'></td></tr></table>";

if (!$session['user']['dragonkills'] && $session['user']['age'] <= 1 && $greenpct <= 1){
	$new .= "<br />When you run low on Stamina, you become weaker in combat.  Recover Stamina by eating, drinking, smoking, or using a New Day.";
}

// $new .= "<table style='border: solid 1px #000000' bgcolor='#777777' cellpadding='0' cellspacing='0' width='70' height='5'><tr><td width='$pctoftotal%' bgcolor='$color'></td><td width='$pctused%'></td></tr></table>";
setcharstat("Vital Info", $stat, $new);



//Add the "Show Actions" bit
// addcharstat("Vital Info");
// addcharstat("Stamina Details", "<a href='lib/stamina/show.php' target='_blank' onclick=\"".popup("runmodule.php?module=staminasystem&op=show").";return false;\">".translate_inline("Show")."</a>");
//addnav("","runmodule.php?module=staminasystem&op=show");

addnav("","stamina.php?op=show");
?>
