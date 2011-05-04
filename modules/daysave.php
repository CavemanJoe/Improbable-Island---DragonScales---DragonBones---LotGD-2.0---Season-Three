<?php
// require_once("lib/http.php");
// require_once("common.php");
// require_once("lib/villagenav.php");
// require_once("modules/worldmapen.php");

// v1.1 fixed a bug that caused a possible infinite newday loop when not logging out after using a newday
// V1.2 Fixes newday hook, added debug info by SexyCook
// V1.3 Added hook to jail
// V1.4 commented the debugs that were getting on my nerves, added an output for 0 days, due to translation difficulties.
// V1.5 Fixed the bug that gave new players the max amount of saved days
// V2.0 CMJ update - Donation Points functionality, World Map integration, Chronosphere integration

function daysave_getmoduleinfo(){
	$info = array(
		"name"=>"Game Day Accumulation",
		"author"=>"Exxar, fixes by SexyCook, upgrade by Caveman Joe",
		"version"=>"2.0",
		"category"=>"General",
		"settings"=>array(
			"startdays"=>"Number of game days with which to start a new player,int|2",
			"startslots"=>"Number of game day slots to start,int|2",
			"buyslotcost"=>"Players can buy an extra day slot in return for this many Supporter Points,int|250",
			"fillslotcost"=>"Players have the option to fill up their days when buying a new day slot in exchange for this many Supporter Points per day to be filled,int|10",
			"buydaycost"=>"Players have the option to buy an Instant New Day at any time for this many Supporter Points,int|25",
			"maxbuyday"=>"Players can buy only this many Instant New Days per real Game Day,int|1",
		),
		"prefs"=>array(
			"days"=>"Current number of saved Game Days,int|0",
			"slots"=>"Maximum number of saved Game Days,int|0",
			"instantbuys"=>"Number of Instant New Days bought during this Game Day,int|0",
			"lastlognewday"=>"Next newday after logout,int|5",
			"initsetup"=>"Player has been initially granted their starting settings,bool|0",
			),
		);
	return $info;
}

function daysave_install(){
	module_addhook("newday");
	module_addhook("newday-runonce");
	module_addhook("player-logout");
	module_addhook("village");
	module_addhook("shades");
	module_addhook("worldnav");
	return true;
}

function daysave_uninstall(){
	return true;
}

function daysave_dohook($hookname,$args){
	global $session;
	switch ($hookname){
		case "newday":
			$days=get_module_pref("days");
			$slots=get_module_pref("slots");
			$lastonnextday=get_module_pref("lastlognewday");
			$time=gametimedetails();
			//debug("time:". $time['gametime']);
			$timediff=$time['gametime']-$lastonnextday;
			//debug("timediff: $timediff");
			if ($timediff>86400){
				$addition=floor($timediff/86400);
				$days+=$addition;
				if ($days > $slots) $days=$slots;
				if($lastonnextday<1){
					$days=0;
				}
				set_module_pref("days", $days);
			}
			set_module_pref("lastlognewday", $time['tomorrow']);
		break;
		case "newday-runonce":
			//reset all players' Instant Buys counter
			// $sql = "SELECT acctid FROM " . db_prefix("accounts");
			// $result = db_query($sql);
			// for ($i=0;$i<db_num_rows($result);$i++){
				// $row = db_fetch_assoc($result);
				// clear_module_pref("instantbuys",false,$row['acctid']);
			// }
		break;
		case "player-logout":
			$details=gametimedetails();
			set_module_pref("lastlognewday", $details['tomorrow']);
			break;
		case "village":
			tlschema('daysavenav');
			// if ($session['user']['age'] < 2 && !$session['user']['dragonkills']){
				// require_once "modules/staminasystem/lib/lib.php";
				// if (get_stamina<10){
					// output("`J`bYou're starting to run low on Stamina.`b  As you become more exhausted, the Island becomes a more dangerous and unpredictable place.  You can replenish your Stamina by eating or drinking something.  Or, hey - did you notice the Saved Days link down there?  That might be worth a click.  Like all Rookie-advice messages, this one will likely disappear and quit pestering you pretty soon.`0`n`n");
					// addnav("`JSaved Days`0");
					// addnav("`JNew Day Menu`0","runmodule.php?module=daysave&op=start&return=village");
				// } else {
					// addnav("Saved Days");
					// addnav("New Day Menu","runmodule.php?module=daysave&op=start&return=village");
				// }
			// } else {
				addnav("Saved Days");
				addnav("New Day Menu","runmodule.php?module=daysave&op=start&return=village");
			//}
		break;
		case "shades":
			tlschema('daysavenav');
			addnav("Saved Days");
			if (!$session['user']['resurrections'] && !$session['user']['dragonkills'] && get_module_pref("days")){
				output("`JAdvice for new players: if you try to fight your way back to the Island and fail, then have a look at the New Day Menu.  This message, like all the other blue newbie messages, will go away and quit pestering you pretty soon.`0`n`n");
				addnav("`JNew Day Menu","runmodule.php?module=daysave&op=start&return=shades");
			} else {
				addnav("New Day Menu","runmodule.php?module=daysave&op=start&return=shades");
			}
		break;
		case "worldnav":
			tlschema('daysavenav');
			addnav("Saved Days");
			addnav("New Day Menu","runmodule.php?module=daysave&op=start&return=worldmapen");
		break;
	}
	return $args;
}

function daysave_run(){
	global $session;
	$op = httpget('op');
	$return = httpget('return');
	if ($return=="house"){
		$hid = httpget('hid');
		$rid = httpget('rid');
		$sid = httpget('sid');
	}
	//handle new players
	if (!get_module_pref("initsetup")){
		set_module_pref("slots",get_module_setting("startslots"));
		set_module_pref("days",get_module_setting("startdays"));
		set_module_pref("initsetup",1);
	}
	$days = get_module_pref("days");
	$slots = get_module_pref("slots");
	$startdays = get_module_setting("startdays");
	$startslots = get_module_setting("startslots");
	//$boughttoday = get_module_pref("instantbuys");
	$boughttoday = 0;
	$buyslotcost = get_module_setting("buyslotcost");
	$buydaycost = get_module_setting("buydaycost");
	$fillslotcost = get_module_setting("fillslotcost");
	$maxbuyday = get_module_setting("maxbuyday");
	$dps = $session['user']['donation']-$session['user']['donationspent'];

	page_header("Saved Days");
		switch ($op){
			case "start":
				output("Here are your Chronospheres.  Each coloured sphere represents one saved-up game day.`n`n");
				if ($startdays && $session['user']['dragonkills']<1){
					output("New players start the game with some days already saved up, so that they can get a feel for how this works.`n`n");
				}
				
				for ($full=1; $full<=$days; $full++){
					rawoutput("<img src=\"images/daysphere-full.png\" alt=\"Saved Day\" title=\"Saved Day\">");
				}
				for ($empty=$full; $empty<=$slots; $empty++){
					rawoutput("<img src=\"images/daysphere-empty.png\" alt=\"Empty Day Slot\" title=\"Empty Day Slot\">");
				}
				if ($days==1){
					$daydisp = "Day";
				} else {
					$daydisp = "Days";
				}
				output("`n`nChronospheres essentially allow you to save up game days for later play, simply by not logging in.  As you can see, you have %s Game %s saved up, out of a maximum of %s.`n`n",$days,$daydisp,$slots);
				addnav("Chronofiddling");
				if ($days){
					addnav("Use a saved day","runmodule.php?module=daysave&op=useday&hid=".$hid."&rid=".$rid."&sid=".$sid);
				} else {
					addnav("You have no saved days","");
				}
				addnav("Refresh this page","runmodule.php?module=daysave&op=start&return=".$return."&hid=".$hid."&rid=".$rid."&sid=".$sid);
				if ($maxbuyday==1){
					$maxdisp = "Day";
				} else {
					$maxdisp = "Days";
				}
				addnav("Supporter Options");
				output("Site supporters have several extra options.  As a site supporter, you can instantly start a new Game Day for your character at any time in exchange for %s Supporter Points.  You can also add more Chronospheres, allowing you to save up more days to play later.  Each additional Chronosphere costs %s Supporter Points, and come pre-filled.  When adding Chronospheres, you have the option of refilling your empty Spheres for a discount cost of %s Supporter Points per empty Sphere.`n`nYou currently have %s Supporter Points available.  See the Hunter's Lodge in any Outpost for a more detailed explanation of how to get Supporter Points, and other cool things you can do with them.`n`n`c`b`4Careful!`0`b`cAlways check the time to the next Game Day (displayed under your Stats) before you use a Chronosphere or buy an Instant New Day!  Nothing sucks worse than paying for a new day and then finding out that you would have gotten one in five minutes anyway.`n`nA quick point to note - if you leave the Island without logging out, and then log back in to arrive at this page, the number of Chronospheres displayed may be out of date.  To fix this, click the Refresh link.`n`nAlso be aware that if you've just logged in to see this page after being logged out for a while, you might have a natural New Day (IE one that won't affect your Chronospheres) waiting for you anyway, which will be triggered when you leave this page.",$buydaycost,$buyslotcost,$fillslotcost,number_format($dps));
				if ($dps>=$buydaycost && $boughttoday < $maxbuyday){
					addnav(array("Buy an Instant New Day for %s Supporter Points",$buydaycost),"runmodule.php?module=daysave&op=buyday&hid=".$hid."&rid=".$rid."&sid=".$sid);
				} else if ($dps<$buydaycost){
					addnav("Not enough Supporter Points for an Instant New Day","");
				} else {
					addnav("Instant New Day limit reached","");
				}
				if ($dps>=$buyslotcost){
					addnav(array("Buy an extra Chronosphere for %s Supporter Points",$buyslotcost),"runmodule.php?module=daysave&op=buyslot&return=".$return."&hid=".$hid."&rid=".$rid."&sid=".$sid);
				} else {
					addnav("Not enough Supporter Points for a new Chronosphere","");
				}
				addnav("Exit");
				if ($return=="village") {
					tlschema('nonav');
					villagenav();
				}
				else if($return=="shades") {
					tlschema('nonav');
					addnav("Back to the FailBoat", "shades.php");
				}
				else if($return=="worldmapen") {
					tlschema('nonav');
					addnav("Return to the World Map", "runmodule.php?module=worldmapen&op=continue");
				} else if($return=="house"){
					addnav("Return to the Dwelling","runmodule.php?module=improbablehousing&op=sleep&sub=sleep&hid=".$hid."&rid=".$rid."&slot=".$sid);
				}
				else addnav("Your navs are corrupted!", "badnav.php");
			break;
			case "useday":
				$days-=1;
				if ($days<0) $days=0;
				set_module_pref("days", $days);
				output("You have used one of your Chronospheres.  Now go forth and have fun!`n`n");
				for ($full=1; $full<=$days; $full++){
					rawoutput("<img src=\"images/daysphere-full.png\" alt=\"Saved Day\" title=\"Saved Day\">");
				}
				for ($empty=$full; $empty<=$slots; $empty++){
					rawoutput("<img src=\"images/daysphere-empty.png\" alt=\"Empty Day Slot\" title=\"Empty Day Slot\">");
				}
				debug($session['user']['restorepage']);
				addnav("It is a New Day!","newday.php");
			break;
			case "buyday":
				output("You have bought one new Game Day in exchange for %s Supporter Points, leaving you with %s points left to spend.  Your Chronospheres are unaffected.  Now go forth and have fun!`n`n",$buydaycost,number_format($dps-$buydaycost));
				for ($full=1; $full<=$days; $full++){
					rawoutput("<img src=\"images/daysphere-full.png\" alt=\"Saved Day\" title=\"Saved Day\">");
				}
				for ($empty=$full; $empty<=$slots; $empty++){
					rawoutput("<img src=\"images/daysphere-empty.png\" alt=\"Empty Day Slot\" title=\"Empty Day Slot\">");
				}
				addnav("It is a New Day!","newday.php");
				$session['user']['donationspent']+=$buydaycost;
				increment_module_pref("instantbuys");
				
				//log purchase
				$sql = "INSERT INTO ".db_prefix("purchaselog")." (acctid,purchased,amount,data,giftwrap,timestamp) VALUES ('".$session['user']['acctid']."','newday_instant','".$buydaycost."','none','0','".date("Y-m-d H:i:s")."')";
				db_query($sql);
			break;
			case "buyslot":
				//log purchase
				$sql = "INSERT INTO ".db_prefix("purchaselog")." (acctid,purchased,amount,data,giftwrap,timestamp) VALUES ('".$session['user']['acctid']."','newday_chronosphere','".$buyslotcost."','none','0','".date("Y-m-d H:i:s")."')";
				db_query($sql);
				
				$session['user']['donationspent']+=$buyslotcost;
				increment_module_pref("days");
				increment_module_pref("slots");
				$days = get_module_pref("days");
				$slots = get_module_pref("slots");
				$dps = $session['user']['donation']-$session['user']['donationspent'];
				output("You have bought one additional Chronosphere in exchange for %s Supporter Points, leaving you with %s points left to spend.`n`n",$buyslotcost,number_format($dps));
				for ($full=1; $full<=$days; $full++){
					rawoutput("<img src=\"images/daysphere-full.png\" alt=\"Saved Day\" title=\"Saved Day\">");
				}
				for ($empty=$full; $empty<=$slots; $empty++){
					rawoutput("<img src=\"images/daysphere-empty.png\" alt=\"Empty Day Slot\" title=\"Empty Day Slot\">");
				}
				if ($days<$slots && $dps>=$fillslotcost){
					addnav("Fill up Chronospheres","");
					output("`n`nYou now have the option of refilling your empty Chronospheres for %s Supporter Points each.`n`n",$fillslotcost);
					$empty = $slots-$days;
					for ($i=1; $i<=$empty; $i++){
						$cost = $i*$fillslotcost;
						if ($dps>=$cost){
							if ($i==1){
								$p = "Sphere";
							} else {
								$p = "Spheres";
							}
							addnav(array("Fill up %s %s for %s Supporter Points",$i,$p,$cost),"runmodule.php?module=daysave&op=fillup&fill=".$i."&return=".$return."&hid=".$hid."&rid=".$rid."&sid=".$sid);
						}
					}
				}
				addnav("Return");
				addnav("Back to the menu","runmodule.php?module=daysave&op=start&return=".$return."&hid=".$hid."&rid=".$rid."&sid=".$sid);
			break;
			case "fillup":
				$fill = httpget('fill');
				
				//log purchase
				for ($i=0; $i<$fill; $i++){
					$sql = "INSERT INTO ".db_prefix("purchaselog")." (acctid,purchased,amount,data,giftwrap,timestamp) VALUES ('".$session['user']['acctid']."','newday_fillslot','".$fillslotcost."','none','0','".date("Y-m-d H:i:s")."')";
					db_query($sql);
				}
				
				$session['user']['donationspent']+=($fill*$fillslotcost);
				$dps = $session['user']['donation']-$session['user']['donationspent'];
				increment_module_pref("days",$fill);
				$days = get_module_pref("days");
				if ($fill==1){
					$p = "Chronosphere";
				} else {
					$p = "Chronospheres";
				}
				output("You have filled up %s %s in exchange for %s Supporter Points, leaving you with %s points left to spend.`n`n",$fill,$p,number_format($fill*$fillslotcost),number_format($dps));
				for ($full=1; $full<=$days; $full++){
					rawoutput("<img src=\"images/daysphere-full.png\" alt=\"Saved Day\" title=\"Saved Day\">");
				}
				for ($empty=$full; $empty<=$slots; $empty++){
					rawoutput("<img src=\"images/daysphere-empty.png\" alt=\"Empty Day Slot\" title=\"Empty Day Slot\">");
				}
				addnav("Return","");
				addnav("Back to the menu","runmodule.php?module=daysave&op=start&return=".$return."&hid=".$hid."&rid=".$rid."&sid=".$sid);
			break;
		}
	page_footer();
}
?>
