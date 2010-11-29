<?php

global $session;

$hid = httpget('hid');
$rid = httpget('rid');
$deadbolt = httpget('deadbolt');
require_once "modules/improbablehousing/lib/lib.php";
$house = improbablehousing_gethousedata($hid);

page_header("Keys and Locks");

$toggle = httpget('toggle');
if ($toggle=="master"){
	if ($house['data']['locked']==1){
		$house['data']['locked']=0;
	} else {
		$house['data']['locked']=1;
	}
	$loc = $house['location'];
	invalidatedatacache("housing/housing_location_".$loc);
} else if ($toggle){
	if ($deadbolt=="lock"){
		$house['data']['rooms'][$toggle]['locked']=2;
	} else if ($deadbolt=="unlock"){
		if ($house['data']['rooms'][$toggle]['locked']==2){
			//release deadbolt, leave room locked
			$house['data']['rooms'][$toggle]['locked']=1;
		} else {
			//release deadbolt, room is unlocked
			$house['data']['rooms'][$toggle]['locked']=1;
		}
	} else {
		//regular lock, just toggle it
		if ($house['data']['rooms'][$toggle]['locked']==1){
			$house['data']['rooms'][$toggle]['locked']=0;
		} else {
			$house['data']['rooms'][$toggle]['locked']=1;
		}
	}
}

output("Locking a room will ensure people can't get in without a key.  Deadbolting a room will ensure that even people with regular keys can't get in.  People can always escape a room regardless of its locks.  Here's the status of all the locks you have access to:`n`n");

if (improbablehousing_getkeytype($house,$rid)>=100){
	if ($house['data']['locked']){
		$masterlock = "`4Locked`0";
		addnav("Master Lock");
		addnav("Open the Master Lock","runmodule.php?module=improbablehousing&op=locks&toggle=master&hid=$hid&rid=$rid");
	} else {
		$masterlock = "`2Unlocked`0";
		addnav("Master Lock");
		addnav("Close the Master Lock","runmodule.php?module=improbablehousing&op=locks&toggle=master&hid=$hid&rid=$rid");
	}
	output("Dwelling entrance: %s`n",$masterlock);
}

foreach($house['data']['rooms'] AS $rkey=>$rvals){
	if ($rkey!=0){
		addnav("Rooms");
		if ($rvals['locked']==1){
			//locked
			if (improbablehousing_getkeytype($house,$rkey)>=20){
				output("`0%s`0: `4Locked`0`n",$rvals['name']);
				addnav(array("Unlock %s",$rvals['name']),"runmodule.php?module=improbablehousing&op=locks&toggle=$rkey&hid=$hid&rid=$rid");
				if (improbablehousing_getkeytype($house,$rkey)>=30){
					addnav(array("Deadbolt %s",$rvals['name']),"runmodule.php?module=improbablehousing&op=locks&toggle=$rkey&hid=$hid&rid=$rid&deadbolt=lock");
				}
			}
		} else if ($rvals['locked']==2){
			//deadbolted
				output("`0%s`0: `4Deadbolted`0`n",$rvals['name']);
			if (improbablehousing_getkeytype($house,$rkey)>=30){
				addnav(array("Unlock deadbolt on %s",$rvals['name']),"runmodule.php?module=improbablehousing&op=locks&toggle=$rkey&hid=$hid&rid=$rid&deadbolt=unlock");
			}
		} else {
			//unlocked
			if (improbablehousing_getkeytype($house,$rkey)>=20){
				output("`0%s`0: `2Unlocked`0`n",$rvals['name']);
				addnav(array("Lock %s",$rvals['name']),"runmodule.php?module=improbablehousing&op=locks&toggle=$rkey&hid=$hid&rid=$rid");
			}
		}
	}
}

improbablehousing_sethousedata($house);
improbablehousing_bottomnavs($house,$rid);
page_footer();

?>