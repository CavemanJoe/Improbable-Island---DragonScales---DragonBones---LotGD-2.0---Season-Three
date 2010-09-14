<?php

$hid = httpget('hid');
$rid = httpget('rid');

page_header("Materials Store");

require_once "modules/improbablehousing/lib/lib.php";
require_once "modules/iitems/lib/lib.php";
$house=improbablehousing_gethousedata($hid);
$keytype = improbablehousing_getkeytype($house,$rid);

//set initial store iitems
if (!isset($house['data']['store']['wood'])){
	$house['data']['store']['wood'] = 0;
	improbablehousing_sethousedata($house);
}
if (!isset($house['data']['store']['stone'])){
	$house['data']['store']['stone'] = 0;
	improbablehousing_sethousedata($house);
}

$giveitem = httpget('giveiitem');
$giveall = httpget('giveall');
$takeitem = httpget('takeiitem');
$takeall = httpget('takeall');
if ($giveitem){
	if (!$giveall){
		iitems_discard_item($giveitem);
		$house['data']['store'][$giveitem]+=1;
		output("You drop the item in the Dwelling's storehold.  The item that you dropped can now only be used for building, or taken out by the owner or someone with a Master Key.`n`n");
	} else {
		//give all iitems
		$q = iitems_discard_all_items($giveitem);
		$house['data']['store'][$giveitem]+=$q;
	}
	improbablehousing_sethousedata($house);
} else if ($takeitem){
	if ($takeall){
		$q = $house['data']['store'][$takeitem];
		for ($i=0; $i<$q; $i++){
			iitems_give_item($takeitem);
			$house['data']['store'][$takeitem]-=1;
		}
		output("Okay.  You pick up every single one, and stuff them all into your Backpack.`n`n");
	} else {
		iitems_give_item($takeitem);
		$house['data']['store'][$takeitem]-=1;
		output("Okay.  You pick up the item and stuff it into your Backpack.`n`n");
	}
	improbablehousing_sethousedata($house);
}

output("You can drop materials directly into this Dwelling, if you like.  However, once materials have been dropped in a Dwelling, they can only be used for construction within this Dwelling.  Only the Dwelling owner (or someone with an equivalent key) can remove them from the Dwelling.  This can help prevent materials from going missing unexpectedly (or, if you prefer, being pinched by Thieving Midget Bastards or Other Unsavoury Types), and it can be a nice surprise for the Dwelling owner.`n`n");
output("Currently, this Materials Store holds:`n");
addnav("Give Materials");
foreach($house['data']['store'] AS $storeitem => $number){
	$storeitemdetails[$storeitem] = iitems_get_item($storeitem);
	//debug($storeitemdetails);
	if ($storeitemdetails[$storeitem]['player']['quantity'] > 0){
		$hasitems = 1;
		addnav(array("Give a %s (you have %s)",$storeitemdetails[$storeitem]['master']['verbosename'],$storeitemdetails[$storeitem]['player']['quantity']),"runmodule.php?module=improbablehousing&op=store&giveiitem=$storeitem&hid=$hid&rid=$rid");
	}
	if ($storeitemdetails[$storeitem]['player']['quantity'] > 1){
		addnav(array("Give all your %s",$storeitemdetails[$storeitem]['master']['plural']),"runmodule.php?module=improbablehousing&op=store&giveiitem=$storeitem&giveall=true&hid=$hid&rid=$rid");
	}
	
	if ($keytype>=100){
		if ($number > 0){
			addnav(array("Take a %s",$storeitemdetails[$storeitem]['master']['verbosename']),"runmodule.php?module=improbablehousing&op=store&takeiitem=$storeitem&hid=$hid&rid=$rid");
		}
		if ($number > 1){
			addnav(array("Take all %s",$storeitemdetails[$storeitem]['master']['plural']),"runmodule.php?module=improbablehousing&op=store&takeiitem=$storeitem&takeall=true&hid=$hid&rid=$rid");
		}
	}
	
	if ($number == 1){
		output("1 %s`0`n",$storeitemdetails[$storeitem]['master']['verbosename']);
	} else {
		output("%s %s`0`n",$number,$storeitemdetails[$storeitem]['master']['plural']);
	}
}
if (!$hasitems){
	addnav("You have nothing to give right now.");
}

improbablehousing_bottomnavs($house,$rid);
page_footer();

?>