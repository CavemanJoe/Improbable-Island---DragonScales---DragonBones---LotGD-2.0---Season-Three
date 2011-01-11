<?php

function improbablehousing_teleportbeacon_getmoduleinfo(){
	$info=array(
		"name"=>"Improbable Housing: Teleportation Beacons",
		"version"=>"2010-11-30",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"Housing",
		"download"=>"",
		"settings"=>array(
			"costperday"=>"Cost in Cigarettes to fuel a Teleportation Beacon for a single game day,int|4",
			"startupcost"=>"Cost in Cigarettes to buy a Teleportation Beacon,int|50",
		),
	);
	return $info;
}

function improbablehousing_teleportbeacon_install(){
	module_addhook("improbablehousing_interior");
	module_addhook("newday_runonce");
	module_addhook("teleport");
	return true;
}

function improbablehousing_teleportbeacon_uninstall(){
	return true;
}

function improbablehousing_teleportbeacon_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "improbablehousing_interior":
			$hid = $args['hid'];
			$rid = $args['rid'];
			$house = $args['house'];
			//debug($house);
			if (!$rid){
				if (!isset($house['data']['beacon_fuel']) && !isset($house['data']['beacon_cigsleft']) && improbablehousing_getkeytype($house,$rid)==100){
					addnav("Teleportation Beacon");
					addnav("About Teleportation Beacons","runmodule.php?module=improbablehousing_teleportbeacon&op=start&hid=$hid");
				}
				if (isset($house['data']['beacon_fuel'])){
					addnav("Teleportation Beacon");
					addnav("Buy fuel for this building's Teleportation Beacon","runmodule.php?module=improbablehousing_teleportbeacon&op=buyfuel&hid=$hid");
				} else if ($house['data']['beacon_cigsleft']){
					addnav("Teleportation Beacon");
					addnav("Donate cigarettes towards buying a Teleportation Beacon","runmodule.php?module=improbablehousing_teleportbeacon&op=donate&hid=$hid");
				}
			}
		break;
		case "teleport":
		
			//new version
			debug("woo!");
			$sql = "SELECT hid,value FROM ".db_prefix("building_prefs")." WHERE pref='beacon_fuel'";
			$result = db_query($sql);
			while ($row = db_fetch_assoc($result)){
				if ($row['value'] > 0){
					$hid = $row['hid'];
					$sql2 = "SELECT value FROM ".db_prefix("building_prefs")." WHERE pref='name' AND hid='$hid'";
					$result2 = db_query($sql2);
					$row2 = db_fetch_assoc($result2);
					addnav("Dwellings");
					addnav(array("%s`0",$row2['value']),"runmodule.php?module=improbablehousing&op=interior&hid=$hid");
				}
			}
		break;
		case "newday-runonce":
			//loop through the beacons and deduct days of fuel
			$sql = "UPDATE ".db_prefix("room_prefs")." SET value=value-1 WHERE pref='beacon_fuel'";
			db_query($sql);
		break;
	}
	return $args;
}

function improbablehousing_teleportbeacon_run(){
	global $session;
	page_header("Teleportation Beacon");
	require_once "modules/improbablehousing/lib/lib.php";
	$hid = httpget("hid");
	$costperday = get_module_setting("costperday");
	$startupcost = get_module_setting("startupcost");

	$currentfuel = get_building_pref("beacon_fuel",$hid);
	$cigstogo = get_building_pref("beacon_cigsleft",$hid);
	
	if ($currentfuel < 0){
		set_building_pref("beacon_fuel",0,$hid);
	}

	switch (httpget('op')){
		case "start":
			output("A Teleportation Beacon is a large, heavy piece of machinery involving hundreds of feet of pipes, compressors, satellite dishes, a very large generator, and a human-sized sealed chamber with very thick walls.  When one of these machines is installed in your home, people will have the option to enter your house using a One-Shot Teleporter, landing in the first room of the house.`n`nThe Robots will sell you a Teleportation Beacon for %s cigarettes, and the mildly radioactive fuel that drives the generator will cost %s cigarettes per game day, paid in advance.  This is all very expensive, so there's a bank function for your friends to help out.  Would you like to start a fund to buy a Teleportation Beacon for this Dwelling?",$startupcost,$costperday);
			addnav("Ooh!");
			addnav("Yes, start a fund.","runmodule.php?module=improbablehousing_teleportbeacon&op=startfund&hid=$hid");
			addnav("Hmm.");
			addnav("No, back to the house...","runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=0");
		break;
		case "startfund":
			output("You've started a fund for a Teleportation Beacon!");
			set_building_pref("beacon_cigsleft",$startupcost,$hid);
			addnav("Woo!");
			addnav("Back to the house...","runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=0");
		break;
		case "buyfuel":
			if (httpget('give')){
				$session['user']['gems']-=$costperday;
				$currentfuel += 1;
				set_building_pref("beacon_fuel",$currentfuel,$hid);
				output("You pull out %s Cigarettes and drop them in the box next to the machine.  There's now enough cigarettes available to pay for %s game days' worth of fuel!`n`n",$costperday,$currentfuel);
			}
			output("This building is equipped with a Teleportation Beacon that allows people to teleport directly to its entrance using a One-Shot Teleporter.  Unfortunately, it requires expensive fuel in order to run.  Each fuel cell costs %s cigarettes, and will fuel the Teleportation Beacon for one game day.  You can donate cigarettes to the running costs of the machine here - right now, it's got enough fuel to keep going for another %s days.`n`n",$costperday,$currentfuel);
			addnav("Give Cigarettes");
			if ($session['user']['gems']>=$costperday){
				addnav(array("Buy a day's worth of fuel (%s cigarettes)",$costperday),"runmodule.php?module=improbablehousing_teleportbeacon&op=buyfuel&give=true&hid=$hid");
			} else {
				addnav("You don't have enough cigarettes!","");
			}
			addnav("Return");
			addnav("Back to the house...","runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=0");
		break;
		case "donate":
			if (httpget('error')){
				output("`4Hmm.  Wanna try that again?`0`n`n");
			}
			output("The owner of this Dwelling is saving up for a Teleportation Beacon.  A Teleportation Beacon is a large, heavy piece of machinery involving hundreds of feet of pipes, compressors, satellite dishes, a very large generator, and a human-sized sealed chamber with very thick walls.  When one of these machines is installed in a Dwelling, people will have the option to enter using a One-Shot Teleporter, landing in the first room of the house.`n`nTo buy the beacon and give it its first day's worth of fuel, we need another %s cigarettes.  Will you donate cigarettes?`n`n",$cigstogo);
			rawoutput("<form action='runmodule.php?module=improbablehousing_teleportbeacon&op=donate_finish&hid=$hid' method='POST'>");
			rawoutput("Donate <input id='input' name='amount' width=5 > cigarettes: <input type='submit' class='button' value='donate'>");
			output_notl("`n`n");
			rawoutput("</form>");
			addnav("Return");
			addnav("Back to the house...","runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=0");
			addnav("","runmodule.php?module=improbablehousing_teleportbeacon&op=donate_finish&hid=$hid");
		break;
		case "donate_finish":
			$donated = httppost('amount');
			if ($donated > $session['user']['gems'] || !$donated || $donated<0 || !is_numeric($donated)){
				redirect("runmodule.php?module=improbablehousing_teleportbeacon&op=donate&hid=$hid&error=true");
			} else {
				$session['user']['gems'] -= $donated;
				$newtotal = $cigstogo - $donated;
				set_building_pref("beacon_cigsleft",$newtotal,$hid);
				output("You merrily toss %s cigarettes into the pot.`n`n",$donated);
				if ($newtotal<=0){
					output("The teleportation beacon is now finished and operational!`n`n");
					clear_building_pref("beacon_cigsleft",$hid);
					set_building_pref("beacon_fuel",1,$hid);
				} else {
					output("Now we just need %s more!`n`n",$newtotal);
				}
				addnav("Return");
				addnav("Back to the house...","runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=0");
			}
		break;
	}

	page_footer();
	
	return true;
}

?>