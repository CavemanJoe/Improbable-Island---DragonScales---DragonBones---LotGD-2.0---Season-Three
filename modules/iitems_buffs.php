<?php

function iitems_buffs_getmoduleinfo(){
	$info=array(
		"name"=>"IItems - Buffs",
		"version"=>"20090523",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"IItems",
		"download"=>"",
	);
	return $info;
}

function iitems_buffs_install(){
	module_addhook("iitems-use-item");
	module_addhook("iitems-superuser");
	return true;
}

function iitems_buffs_uninstall(){
	return true;
}

function iitems_buffs_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "iitems-use-item":
			if ($args['master']['usebuff']){
				$buff=array();
				if (isset($args['master']['buffname'])){
					$buff['name'] = stripslashes($args['master']['buffname']);
				}
				if (isset($args['master']['buffrounds'])){
					$buff['rounds'] = $args['master']['buffrounds'];
				} else {
					$buff['rounds'] = 0;
				}
				if (isset($args['master']['buffwearoffmsg'])){
					$buff['wearoff'] = stripslashes($args['master']['buffwearoffmsg']);
				}
				if (isset($args['master']['buffstartmsg'])){
					$buff['startmsg'] = stripslashes($args['master']['buffstartmsg']);
				}
				if (isset($args['master']['buffeffectmsg'])){
					$buff['effectmsg'] = stripslashes($args['master']['buffeffectmsg']);
				}
				if (isset($args['master']['buffnodmgmsg'])){
					$buff['effectnodmgmsg'] = stripslashes($args['master']['buffnodmgmsg']);
				}
				if (isset($args['master']['buffeffectfailmsg'])){
					$buff['effectfailmsg'] = stripslashes($args['master']['buffeffectfailmsg']);
				}
				if (isset($args['master']['buffatkmod'])){
					$buff['atkmod'] = $args['master']['buffatkmod'];
				} else {
					$buff['atkmod'] = 1;
				}
				if (isset($args['master']['buffdefmod'])){
					$buff['defmod'] = $args['master']['buffdefmod'];
				} else {
					$buff['defmod'] = 1;
				}
				if (isset($args['master']['buffinvulnerable'])){
					$buff['invulnerable'] = $args['master']['buffinvulnerable'];
				}
				if (isset($args['master']['buffexpireafterfight'])){
					$buff['expireafterfight'] = $args['master']['buffexpireafterfight'];
				}
				if (isset($args['master']['buffregen'])){
					$buff['regen'] = $args['master']['buffregen'];
				}
				if (isset($args['master']['buffminioncount'])){
					$buff['minioncount'] = $args['master']['buffminioncount'];
				}
				if (isset($args['master']['buffminbadguydamage'])){
					$buff['minbadguydamage'] = $args['master']['buffminbadguydamage'];
				}
				if (isset($args['master']['buffmaxbadguydamage'])){
					$buff['maxbadguydamage'] = $args['master']['buffmaxbadguydamage'];
				}
				if (isset($args['master']['buffmingoodguydamage'])){
					$buff['mingoodguydamage'] = $args['master']['buffmingoodguydamage'];
				}
				if (isset($args['master']['buffmaxgoodguydamage'])){
					$buff['maxgoodguydamage'] = $args['master']['buffmaxgoodguydamage'];
				}
				if (isset($args['master']['bufflifetap'])){
					$buff['lifetap'] = $args['master']['bufflifetap'];
				}
				if (isset($args['master']['buffdamageshield'])){
					$buff['damageshield'] = $args['master']['buffdamageshield'];
				}
				if (isset($args['master']['buffbadguydmgmod'])){
					$buff['badguydmgmod'] = $args['master']['buffbadguydmgmod'];
				} else {
					$buff['badguydmgmod'] = 1;
				}
				if (isset($args['master']['buffbadguyatkmod'])){
					$buff['badguyatkmod'] = $args['master']['buffbadguyatkmod'];
				} else {
					$buff['badguyatkmod'] = 1;
				}
				if (isset($args['master']['buffbadguydefmod'])){
					$buff['badguydefmod'] = $args['master']['buffbadguydefmod'];
				} else {
					$buff['badguydefmod'] = 1;
				}
				while (list($property,$value)=each($buff)) {
					$buff[$property] = preg_replace("/\\n/", "", $value);
				}
				debug("Iitems_buffs is applying a buff");
				apply_buff($args['player']['itemid'], $buff);
			}
			break;
		case "iitems-superuser":
			output("`bIItems - Buffs`b`nApplies buffs when the item is used.  The variable \"usebuff\" must be set to true before a buff is applied - this is to avoid doing a lot of unnecessary work.  Beyond that, use standard buff variable names, prepending them with \"buff\" IE buffatkmod, buffdefmod, buffdmgshield etc etc.`n`n");
			break;
	}
	return $args;
}

function iitems_buffs_run(){
	return true;
}
?>