<?php

global $session;

$rawpref = get_module_pref("sleepingat","improbablehousing");
if ($rawpref!="nowhere"){
	debug("They slept in a house!");
	$pref = unserialize($rawpref);
	require_once "modules/improbablehousing/lib/lib.php";
	$hid = $pref['house'];
	$rid = $pref['room'];
	$slot = $pref['slot'];
	$house = improbablehousing_gethousedata($hid);
	if ($house['data']['rooms'][$rid]['sleepslots'][$slot]['occupier']==$session['user']['acctid']){
		require_once "modules/staminasystem/lib/lib.php";
		$stam = $house['data']['rooms'][$rid]['sleepslots'][$slot]['stamina'];
		debug($stam);
		addstamina($stam);
		output("`0You got a good night's sleep - must have been the relative luxury of sleeping in a house.  `b`2You gain some Stamina.`0`b`n`n");
	}
}

?>