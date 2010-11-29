<?php

global $session;

$hid = httpget('hid');
require_once "modules/improbablehousing/lib/lib.php";
$house = improbablehousing_gethousedata($hid);

//Run through the rooms and make sure the player isn't registered as being in them or sleeping in them


foreach($house['data']['rooms'] AS $rkey=>$rvals){
	if (isset($rvals['sleepslots'])){
		foreach($rvals['sleepslots'] AS $skey=>$svals){
			if ($svals['occupier']==$session['user']['acctid']){
				unset($house['data']['rooms'][$rkey]['sleepslots'][$skey]['occupier']);
			}
		}
	}
	if (isset($rvals['occupants'])){
		foreach($rvals['occupants'] AS $okey=>$ovals){
			if ($okey == $session['user']['acctid']){
				unset($house['data']['rooms'][$rkey]['occupants'][$okey]);
			}
		}
	}
}

improbablehousing_sethousedata($house);

$session['user']['location']="World";
clear_module_pref("sleepingat","improbablehousing");
redirect("runmodule.php?module=worldmapen&op=continue");

?>