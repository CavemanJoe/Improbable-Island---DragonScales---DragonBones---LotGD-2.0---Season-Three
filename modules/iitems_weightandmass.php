<?php

function iitems_weightandmass_getmoduleinfo(){
	$info=array(
		"name"=>"IItems - Weight and Mass",
		"version"=>"20090523",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"IItems",
		"download"=>"",
	);
	return $info;
}

function iitems_weightandmass_install(){
	module_addhook("iitems-inventory");
	module_addhook("iitems-give-item");
	module_addhook("stamina-newday");
	module_addhook("iitems-use-item");
	module_addhook("iitems-use-item-after");
	return true;
}

function iitems_weightandmass_uninstall(){
	return true;
}

function iitems_weightandmass_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "iitems-inventory":
			if ($args['master']['wlimit_capacity'] || $args['master']['weight']){
				$un = "kg";
				if ($args['player']['units']) $un = $args['player']['units'];
				if ($args['player']['weight'] && !$args['player']['quantity']){
					output("Weight: %s kg`n",$args['player']['weight']);
				} else if ($args['player']['weight']){
					output("Weight: %s kg each, %s kg total`n",$args['player']['weight'],$args['player']['weight']*$args['player']['quantity']);
				}
				if ($args['master']['type']=="inventory"){
					$details = iitems_weightandmass_process_weights(true,false,$args['player']['inventorylocation']);
					$cur = $details['current']*5;
					$max = $details['max']*5;
					$left = $max - $cur;
					if ($left >= 0){
						rawoutput("<table><tr><td><table style='border: solid 1px #000000' width='$max' height='7' bgcolor='#333333' cellpadding=0 cellspacing=0><tr><td width='$cur' bgcolor='#00ff00'></td><td width='$left'></td></tr></table></td><td>".$details['current'].$un." / ".$details['max'].$un."</td></tr></table>");
					} else {
						$over = $cur - $max;
						$totalwidth = $max + $over;
						rawoutput("<table><tr><td><table style='border: solid 1px #000000' height='7' width='$totalwidth' cellpadding=0 cellspacing=0><tr><td width='$max' bgcolor='#990000'></td><td width='$over' bgcolor='#ff0000'></td></tr></table></td><td>".$details['current'].$un." / ".$details['max'].$un."</td></tr></table>");
					}
				}
			}
		break;
		case "iitems-give-item":
			if ($args['master']['units']) $args['player']['units'] = $args['master']['units'];
			if ($args['master']['weight']) $args['player']['weight'] = $args['master']['weight'];
			if ($args['master']['wlimit_capacity']) $args['player']['wlimit_capacity'] = $args['master']['wlimit_capacity'];
			if ($args['master']['wlimit_display_name']) $args['player']['wlimit_display_name'] = $args['master']['wlimit_display_name'];
			if ($args['master']['wlimit_sbuff_name']) $args['player']['wlimit_sbuff_name'] = $args['master']['wlimit_sbuff_name'];
			if ($args['master']['wlimit_sbuff_action']) $args['player']['wlimit_sbuff_action'] = $args['master']['wlimit_sbuff_action'];
			if ($args['master']['wlimit_sbuff_roundmsg']) $args['player']['wlimit_sbuff_roundmsg'] = $args['master']['wlimit_sbuff_roundmsg'];
			if ($args['master']['wlimit_use_sbuff']) $args['player']['wlimit_use_sbuff'] = $args['master']['wlimit_use_sbuff'];
			if ($args['master']['wlimit_hardlimit']) $args['player']['wlimit_hardlimit'] = $args['master']['wlimit_hardlimit'];
		case "stamina-newday":
		case "iitems-use-item":
		case "iitems-use-item-after":
		if (get_module_setting("fightinventory","iitems")){
			require_once "modules/iitems_weightandmass.php";
			iitems_weightandmass_process_weights(true);
		}
		break;
	}
	return $args;
}

function iitems_weightandmass_run(){
	return true;
}

function iitems_weightandmass_process_weights($applybuffs=false,$showbars=false,$returnlocation=false){
	global $session,$weightinfo;
	
	require_once "modules/iitems/lib/lib.php";
	$inventory = iitems_get_player_inventory();
	
	$weightinfo = array();
	
	foreach($inventory AS $key => $values){
		if ($values['wlimit_capacity']){
			$weightinfo[$values['inventorylocation']]['max'] += $values['wlimit_capacity'];
			if ($values['wlimit_display_name']) $weightinfo[$values['inventorylocation']]['wlimit_display_name'] = $values['wlimit_display_name'];
			if ($values['wlimit_sbuff_name']) $weightinfo[$values['inventorylocation']]['wlimit_sbuff_name'] = $values['wlimit_sbuff_name'];
			if ($values['wlimit_sbuff_roundmsg']) $weightinfo[$values['inventorylocation']]['wlimit_sbuff_roundmsg'] = $values['wlimit_sbuff_roundmsg'];
			if ($values['wlimit_sbuff_action']) $weightinfo[$values['inventorylocation']]['wlimit_sbuff_action'] = $values['wlimit_sbuff_action'];
			if ($values['wlimit_use_sbuff']) $weightinfo[$values['inventorylocation']]['wlimit_use_sbuff'] = $values['wlimit_use_sbuff'];
			if ($values['wlimit_hardlimit']) $weightinfo[$values['inventorylocation']]['wlimit_hardlimit'] = $values['wlimit_hardlimit'];
		}
		if ($values['quantity']){
			$weightinfo[$values['inventorylocation']]['current'] += $values['weight']*$values['quantity'];
		} else {
			$weightinfo[$values['inventorylocation']]['current'] += $values['weight'];
		}
	}
	
	require_once "modules/staminasystem/lib/lib.php";
	if ($applybuffs){
		foreach ($weightinfo AS $invloc => $details){
			$sbuffid = "wlimit_".$invloc;
			if ($details['current'] > $details['max']){
				if ($details['wlimit_use_sbuff']){
					$penalty = ($details['current'] / $details['max']);
					apply_stamina_buff($sbuffid, array(
						"name"=>$details['wlimit_sbuff_name'],
						"action"=>$details['wlimit_sbuff_action'],
						"costmod"=>$penalty,
						"expmod"=>1,
						"rounds"=>-1,
						"roundmsg"=>$details['wlimit_sbuff_roundmsg'],
						"wearoffmsg"=>"",
					));
				}
			} else {
				strip_stamina_buff($sbuffid);
			}
		}
	}
	
	if ($showbars){
		foreach ($weightinfo AS $invloc => $details){
			$cur = $details['current']*5;
			$max = $details['max']*5;
			$left = $max - $cur;
			if ($left >= 0){
				rawoutput("<table><tr><td>".$details['wlimit_display_name']."</td><td><table style='border: solid 1px #000000' width='$max' height='7' bgcolor='#333333' cellpadding=0 cellspacing=0><tr><td width='$cur' bgcolor='#00ff00'></td><td width='$left'></td></tr></table></td><td>".$details['current']."kg / ".$details['max']."kg</td></tr></table>");
			} else {
				$over = $cur - $max;
				$totalwidth = $max + $over;
				rawoutput("<table><tr><td>".$details['wlimit_display_name']."</td><td><table style='border: solid 1px #000000' height='7' width='$totalwidth' cellpadding=0 cellspacing=0><tr><td width='$max' bgcolor='#990000'></td><td width='$over' bgcolor='#ff0000'></td></tr></table></td><td>".$details['current']."kg / ".$details['max']."kg</td></tr></table>");
			}
		}
	}
	
	if ($returnlocation){
		return $weightinfo[$returnlocation];
	} else {
		return true;
	}
}
?>