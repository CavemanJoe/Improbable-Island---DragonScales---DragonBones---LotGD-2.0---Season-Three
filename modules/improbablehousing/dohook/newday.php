<?php
	require_once "modules/iitems/lib/lib.php";
	$pinventory = iitems_get_player_inventory();
	$t1key = iitems_has_item("toolbox_decorating",$pinventory);
	$t2key = iitems_has_item("toolbox_carpentry",$pinventory);
	$t3key = iitems_has_item("toolbox_masonry",$pinventory);
	
	//debug($t1key);
	
	$msg=0;
	if ($t1key || $t1key===0){
		unset($pinventory[$t1key]);
		$msg=1;
	}
	if ($t2key || $t2key===0){
		unset($pinventory[$t2key]);
		$msg=1;
	}
	if ($t3key || $t3key===0){
		unset($pinventory[$t3key]);
		$msg=1;
	}
	if ($msg){
		output("You look around for your tools, but find none.  Suzie's staff must taken them back during the night.`n`n");
		set_module_pref("items", serialize($pinventory), "iitems");
	}
?>