<?php

global $session;
require_once "modules/improbablehousing/lib/lib.php";
$hid = httpget('hid');
$rid = httpget('rid');
$slot = httpget('slot');
$house = improbablehousing_gethousedata($hid);

page_header("Sleep");

switch(httpget('sub')){
	case "start":
		//make sure the spot isn't occupied already
		if ($house['data']['rooms'][$rid]['sleepslots'][$slot]['occupier'] && !$house['data']['rooms'][$rid]['sleepslots'][$slot]['multicapacity']){
			output("You set up to take your rightful place in this sleeping spot, but someone else has already beaten you to it!  Bah!`n`n");
		} else {
			//set module pref for where the player's sleeping, letting us know where to look for them at newday
			$prefarray = array(
				"house"=>$hid,
				"room"=>$rid,
				"slot"=>$slot,
			);
			$pref = serialize($prefarray);
			set_module_pref("sleepingat",$pref,"improbablehousing");
			//add them to sleeping slots
			$house['data']['rooms'][$rid]['sleepslots'][$slot]['occupier']=$session['user']['acctid'];
			improbablehousing_sethousedata($house);
			//redirect them to the sleeping menu
			redirect("runmodule.php?module=improbablehousing&op=sleep&sub=sleep&hid=$hid&rid=$rid&slot=$slot");
		}
	break;
	case "sleep":
	//Show log out, refresh, new day menu, check for newday, update sleeping pref and house data
	//Check whether this player's acctid is in the sleepers list, or if they've been kicked out
	if ($house['data']['rooms'][$rid]['sleepslots'][$slot]['occupier']!=$session['user']['acctid']){
		output("You've been kicked out of this spot!`n`n");
		clear_module_pref("sleepingat","improbablehousing");
	} else {
		if (httpget('refresh')){
			output("You toss and turn and look out of the window, but the new day has not yet arrived.`n`n");
		} else if (httpget('getup')){
			output("You rise from your slumber.  It's been a good night's sleep!`n`n");
		} else {
			if (isset($house['data']['rooms'][$rid]['sleepslots'][$slot]['desc'])){
				output("%s`n`n",$house['data']['rooms'][$rid]['sleepslots'][$slot]['desc']);
			} else {
				output("You settle down for a good night's kip.`n`n");
			}
			output("Sleeping here will gain you an extra %s Stamina points (about %s%%).`n`n",number_format($house['data']['rooms'][$rid]['sleepslots'][$slot]['stamina']),round($house['data']['rooms'][$rid]['sleepslots'][$slot]['stamina']/10000, 2));
		}
		$session['user']['restorepage']="runmodule.php?module=improbablehousing&op=sleep&sub=sleep&hid=$hid&rid=$rid&slot=$slot&getup=true";
		checkday();
		addnav("Sleep");
		addnav("Log Out","runmodule.php?module=improbablehousing&op=sleep&sub=logout&hid=$hid&rid=$rid&slot=$slot");
		addnav("New Day menu","runmodule.php?module=improbablehousing&op=sleep&sub=ndmenu&hid=$hid&rid=$rid&slot=$slot");
		addnav("Refresh this page","runmodule.php?module=improbablehousing&op=sleep&sub=sleep&hid=$hid&rid=$rid&slot=$slot&refresh=1");
		$hook = array(
			'hid'=>$hid,
			'rid'=>$rid,
			'slot'=>$slot,
			'house'=>$house,
		);
		modulehook("improbablehousing_sleepslot",$hook);
	}
	break;
	case "kickout":
		$house['data']['rooms'][$rid]['sleepslots'][$slot]['occupier']=0;
		improbablehousing_sethousedata($house);
		output("You roughly kick the occupant out of bed.  That'll show 'em!`n`n");
	break;
	case "ndmenu":
	if ($house['data']['rooms'][$rid]['sleepslots'][$slot]['occupier']!=$session['user']['acctid']){
		output("You've been kicked out of bed!`n`n");
		clear_module_pref("sleepingat","improbablehousing");
	} else {
		redirect("runmodule.php?module=daysave&op=start&return=house&hid=".$hid."&rid=".$rid."&sid=".$slot);
	}
	break;
	case "logout":
		if ($session['user']['loggedin']) {
			$session['user']['loggedin'] = 0;
			$session['user']['restorepage'] = "runmodule.php?module=improbablehousing&op=sleep&sub=sleep&hid=$hid&rid=$rid&slot=$slot&getup=true";
			saveuser();
			invalidatedatacache("charlisthomepage");
			invalidatedatacache("list.php-warsonline");
		}
		$session = array();
		redirect("index.php");
	break;
}

improbablehousing_bottomnavs($house,$rid);
page_footer();

?>