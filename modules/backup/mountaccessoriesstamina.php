<?php

/*

This module provides interaction between Mount Accessories and the Stamina System, by allowing any number of Stamina buffs to be added.

To accomplish the effects of running this in a module:

apply_stamina_buff('ultra-sexy-buff-for-sexins', array(
	"name"=>"Ultra Sexy Buff for Sexins",
	"action"=>"Sexins",
	"costmod"=>0.5,
	"expmod"=>0.8,
	"rounds"=>5,
	"roundmsg"=>"You're good at this Sexins thing!",
	"wearoffmsg"=>"You are not good any longer!",
));
apply_stamina_buff('testy-buff-for-fighting', array(
	"name"=>"Awesome Test Buff for Travel over Plains",
	"action"=>"Travel - Plains",
	"costmod"=>0.5,
	"expmod"=>0.8,
	"rounds"=>5,
	"roundmsg"=>"Travelling over Plains only costs half as much as usual for some reason!",
	"wearoffmsg"=>"It's stopped now!",
));

You would enter this into the Mount Accessories editor, for the accessory of your choice:

numberofstaminabuffs=>2;;
staminabuffref1=>ultra-sexy-buff-for-sexins;;
staminabuffname1=>Ultra Sexy Buff for Sexins;;
staminabuffaction1=>Sexins;;
staminabuffcostmod1=>0.5;;
staminabuffexpmod1=>0.8;;
staminabuffrounds1=>5;;
staminabuffroundmsg1=>You're good at this Sexins thing!;;
staminabuffwearoffmsg1=>You are not good any longer!;;
staminabuffref2=>testy-buff-for-fighting;;
staminabuffname2=>Awesome Test Buff for Travel Over Plains;;
staminabuffaction2=>Travel - Plains;;
staminabuffcostmod2=>0.5;;
staminabuffexpmod2=>0.8;;
staminabuffrounds2=>5;;
staminabuffroundmsg2=>Travelling over Plains only costs half as much as usual for some reason!;;
staminabuffwearoffmsg2=>It's stopped now!

*/

require_once "modules/mountaccessories/lib.php";

function mountaccessoriesstamina_getmoduleinfo(){
	$info=array(
		"name"=>"Mount Accessories - Stamina System Integration",
		"version"=>"0.1 2009-01-13",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"Mount Accessories",
		"download"=>"",
	);
	return $info;
}

function mountaccessoriesstamina_install(){
	module_addhook("mountaccessories_apply_accessory");
	module_addhook("mountaccessories_strip_accessory");
	return true;
}

function mountaccessoriesstamina_uninstall(){
	return true;
}

function mountaccessoriesstamina_dohook($hookname,$args){
	global $session, $acc;
	switch($hookname){
		case "mountaccessories_apply_accessory":
			require_once("modules/staminasystem/lib/lib.php");
			$numberofbuffs = $acc['numberofstaminabuffs'];
			for ($i=0; $i<=$numberofbuffs; $i++){
				apply_stamina_buff($acc['staminabuffref'.$i], array(
					"name"=>$acc['staminabuffname'.$i],
					"action"=>$acc['staminabuffaction'.$i],
					"class"=>$acc['staminabuffclass'.$i],
					"costmod"=>$acc['staminabuffcostmod'.$i],
					"expmod"=>$acc['staminabuffexpmod'.$i],
					"rounds"=>$acc['staminabuffrounds'.$i],
					"roundmsg"=>$acc['staminabuffroundmsg'.$i],
					"wearoffmsg"=>$acc['staminabuffwearoffmsg'.$i],
				));
			}
			break;
		case "mountaccessories_strip_accessory":
			require_once("modules/staminasystem/lib/lib.php");
			$numberofbuffs = $acc['numberofstaminabuffs'];
			for ($i=0; $i<=$numberofbuffs; $i++){
				strip_stamina_buff($acc['staminabuffref'.$i]);
			}
			break;
	}
	return $args;
}

?>
