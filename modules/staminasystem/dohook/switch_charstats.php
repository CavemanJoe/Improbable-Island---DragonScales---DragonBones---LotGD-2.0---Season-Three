<?php

global $charstat_info, $badguy, $actions_used;
// Commented out because of new modifications to charstats function in pageparts.php
//First we remove the Turns stat...
// if (isset($charstat_info['Vital Info']) && isset($charstat_info['Vital Info']['Turns'])){
	// unset($charstat_info['Vital Info']['Turns']);
// }
// if (isset($charstat_info['Vital Info']) && isset($charstat_info['Vital Info']['Spirits'])){
	// unset($charstat_info['Vital Info']['Spirits']);
// }
// if (isset($charstat_info['Extra Info']) && isset($charstat_info['Extra Info']['Free Travel'])){
	// unset($charstat_info['Extra Info']['Free Travel']);
// }

//Look at the number of Turns we're missing.  Default is ten, and we'll add or remove some Stamina depending, as long as we're not in a fight.
if (get_module_setting("turns_emulation_base")!=0 ){
	if (!isset($badguy)){
		$stamina = e_rand(get_module_setting("turns_emulation_base"),get_module_setting("turns_emulation_ceiling"));
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

if (!isset($charstat_info['Recent Actions'])){
	//Put yer thing down, flip it an' reverse it
	$yarr = array_reverse($charstat_info);
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
	}
}

//Then, since Turns are pretty well baked into core and we don't want to be playing around with adding turns just as they're needed for core to operate, we'll just add ten turns here and forget all about it...
$session['user']['turns'] = 10;



//Display the actual Stamina bar

$stamina = get_module_pref("stamina");
$daystamina = 1000000;
$redpoint = get_module_pref("red");
$amberpoint = get_module_pref("amber");
$redpct = get_stamina(0);
$amberpct = get_stamina(1);
$greenpct = get_stamina(2);

$stat = "<a href='runmodule.php?module=staminasystem&op=show' target='_blank' onclick=\"".popup("runmodule.php?module=staminasystem&op=show").";return false;\">Stamina</a>";

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

// $new .= "<table style='border: solid 1px #000000' bgcolor='#777777' cellpadding='0' cellspacing='0' width='70' height='5'><tr><td width='$pctoftotal%' bgcolor='$color'></td><td width='$pctused%'></td></tr></table>";
setcharstat("Vital Info", $stat, $new);



//Add the "Show Actions" bit
// addcharstat("Vital Info");
// addcharstat("Stamina Details", "<a href='runmodule.php?module=staminasystem&op=show' target='_blank' onclick=\"".popup("runmodule.php?module=staminasystem&op=show").";return false;\">".translate_inline("Show")."</a>");
addnav("","runmodule.php?module=staminasystem&op=show");

?>