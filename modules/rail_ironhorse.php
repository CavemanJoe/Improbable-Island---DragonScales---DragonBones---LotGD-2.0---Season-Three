<?php

	require_once "modules/improbablehousing/lib/lib.php";
	require_once "modules/rail/lib.php";
	require_once "lib/commentary.php";

function rail_ironhorse_getmoduleinfo(){
	$info = array(
	"name"=>"Improbable Railway - Iron Horse",
	"version"=>"2010-05-28",
	"author"=>"Sylvia Li",
	"category"=>"Improbable Rail",
	"download"=>"",
	"prefs"=>array(
		"Improbable Railway Iron Horse - User Preferences,title",
		"hasridden"=>"Player has ridden the train,bool|0",
		),
	);
	return $info;
}

function rail_ironhorse_install(){
	// for boarding the train and other stationmaster transactions
	module_addhook("improbablehousing_interior");

//	$locs = rail_ironhorse_getlocs();
//	foreach($locs AS $loc => $locarray){
//		if (!($loc == "WA") && !($loc == "DB")){							// no hook for the waystation
//			$hid = $locarray[0];
//			$rid = $locarray[1];
//			$hookname = "dwelling-".$hid."-".$rid;
//			module_addhook($hookname);
//		}
//	}
	
	// for checking rail pass expiration
	module_addhook("newday");

	// for comment moderation
	module_addhook("moderate");
	return true;
}

function rail_ironhorse_uninstall(){
	return true;
}

function rail_ironhorse_dohook($hookname,$args){
	global $session;
	
	switch ($hookname){
		case "moderate":
			$args['Riding the Train'] = "Riding the Train";
		break;

		case "newday":
			if (rail_discard_item("railpass_active")){
				output("`2The `bImprobable Island Railway Company`b rail pass you used yesterday has mysteriously vanished from your card case! Oh well, they did say that it was only good for one day.`n`n");
			}
			if (rail_discard_item("railpassfirst_active")){
				output("`2The `bImprobable Island Railway Company`b First Class rail pass you used yesterday has mysteriously vanished from your card case! Oh well, they did say that it was only good for one day.`n`n");
			}
		break;
		case "improbablehousing_interior":
			$hid = $args['hid'];
			$rid = $args['rid'];
			$locs = rail_ironhorse_getlocs();
			$station = 0;
			foreach($locs AS $loc => $locarray){
				if (($hid == $locarray[0]) && ($rid == $locarray[1])){
					if (!($loc == "WA") && !($loc == "DB")){
						// Ah, found it! We're in a train station
						$station = 1;
						break;		// no need to keep looking through the rest of the array
					}
				}
			}
			if ($station){
				if (rail_ironhorse_canboard()){
					output("`2A lonesome whistle wails. Distant chuffing grows closer and louder. Is it...? Yes, it is! The train is here! With another very long blast on the whistle, clanging bell, hissing clouds of steam, and wheels screeching steel-on-steel, the great black `~`bsteam engine`b`2 pulls into the station.`0`n`n");
					addnav("All aboard!");
					addnav("Board the train","runmodule.php?module=rail_ironhorse&op=board&hid=$hid&rid=$rid");
				}

				// stationmaster interaction
				$val = rail_collector_valuehand();
//				debug($val);
				if ($val['firstclass'] && $val['value'] > 0){
					output("`2Ah! You seem to be in luck. The busy stationmaster is here today.`0`n`n");
					addnav("Stationmaster");
					addnav("Ask about rail passes","runmodule.php?module=rail_ironhorse&op=stationmaster&hid=$hid&rid=$rid");
				}
			}
				
		break;
	}
	return $args;
}

function rail_ironhorse_run(){
	global $session;
	$op = httpget("op");
	$fromhid = httpget("hid");
	$fromrid = httpget("rid");
	$locs = rail_ironhorse_getlocs();

	switch($op){
		case "board":
			page_header("On the Train!");
			
			// Conductor walks through the train here
			if (rail_hascard("railpass_active")
			||  rail_hascard("railpassfirst_active")){
				// they already have a punched ticket - they're fine
				if (e_rand(1,20) == 1){
				output("`2The `@Conductor `2lurches through the train again, examining passes, taking bribes, exchanging familiar nods with the regular riders.`0`n`n");
				}
			} else {
				if (rail_hascard("railpass")
				&&	rail_hascard("railpassfirst")){
					// they have both - we have to ask them which they want to use
					output("`0The `@Conductor `0examines your passes. \"`2Why, you seem to have both regular and first class passes here. Which would you prefer to use today?`0\"`n`n");
					addnav("Which pass will you use?");
					addnav("Just the regular today, thanks","runmodule.php?module=rail_ironhorse&op=choose&hid=$fromhid&rid=$fromrid&pass=reg");
					addnav("It's first class for me, baby","runmodule.php?module=rail_ironhorse&op=choose&hid=$fromhid&rid=$fromrid&pass=first");
					page_footer();
					break;
				} else {
					// they have one or the other but not both - we can punch automatically
					if (rail_hascard("railpass")) {
						// they have a regular pass
						output("`2The `@Conductor `2peers myopically at your Rail Pass and fumbles with a ticket punch. Ka-`btchik`b! Now you're free to ride the train as much as you want for the rest of the day!`0`n`n");
						rail_ironhorse_activatepass("railpass");
					} else {
						// they have a first class pass
						output("`2The `@Conductor `2inspects your First Class Rail Pass and deferentially produces a ticket punch. Ka-`btchik`b! Now you can ride `ifirst class`i for the rest of the day! Awesome!`0`n`n");
						rail_ironhorse_activatepass("railpassfirst");
					}
				}
			}
			
			$hookargs = array(
				"hid" => $fromhid,
				"rid" => $fromrid,
			);
			modulehook("ironhorse-onboard",$hookargs);
			set_module_pref("hasridden",1);
			output("`2The wheels clatter, the car shakes with the astounding speed. Why, you must be going a good twenty-five miles an hour!`0`n`n");
			addnav("Get off!");
			foreach($locs AS $loc => $locarray){
				if ($loc == "WA" || $loc == "DB") {
					} else {
					if ($locarray[0] == $fromhid){
						addnav(array("`2Back to %s: `0%s",$loc,$locarray[2]),"runmodule.php?module=rail_ironhorse&op=leave&hid=$fromhid&rid=$fromrid&loc=$loc");
					} else {
						addnav(array("`2%s: `0%s",$loc,$locarray[2]),"runmodule.php?module=rail_ironhorse&op=leave&hid=$fromhid&rid=$fromrid&loc=$loc");
					}
				}
			}
			if (rail_hascard("railpassfirst_active")){
				addnav("First Class");
				addnav("Request Stop","runmodule.php?module=rail_ironhorse&op=request&hid=$fromhid&rid=fromrid");
			}
			addcommentary();
			viewcommentary("Riding the Train","Shout over the engine's thunder:");

			page_footer();
			break;
			
		case "leave":
			page_header("You have reached your destination!");
			rail_ironhorse_cleanup($fromhid);
			
			switch (e_rand(1,200)){		// where are we leaving to?
				case 1:
					$loc = "WA";
				break;
				case 2:
					$loc = "DB";
				break;
				default:
					$loc = httpget("loc");
				break;
			}
				
			$tohid = $locs[$loc][0];
			$torid = $locs[$loc][1];

			$house = improbablehousing_gethousedata($locs[$loc][0]);
			$tohousename = $house['data']['name'];
			set_module_pref("worldXYZ",$house['location'],"worldmapen");
			set_module_pref("lastCity",$locs[$loc][3],"worldmapen");
			$session['user']['location'] = "House: ".$tohousename.", Room ".$torid."";
			
				output("`0The `@Conductor `0smiles toothily as you prepare to disembark. \"`2Thank-you for travelling with the `bImprobable Island Railway Company`b. Please don't forget to take all your luggage with you, and have an improbable day!`0\"`n`n");
			output("`2%s`0`n`n",$locs[$loc][4]);
			addnav("Get off the train");
			addnav(array("`2%s`0 Platform",$locs[$loc][2]), "runmodule.php?module=improbablehousing&op=interior&hid=$tohid&rid=$torid");

			if (!($loc == "WA") && !($loc == "DB")){
			addnav("Wait, I've changed my mind");
			addnav("`2Stay `0 on board","runmodule.php?module=rail_ironhorse&op=board&hid=$tohid&rid=$torid");
			} else {
				output("`2You feel stronger after your pleasant train ride, and rather refreshed!`n`n`0");
				if ($session['user']['hitpoints'] <
						$session['user']['maxhitpoints']) {
					$session['user']['hitpoints'] =
						$session['user']['maxhitpoints'];
				}else{
					$session['user']['hitpoints'] =
						($session['user']['hitpoints']*1.1);
				require_once "modules/staminasystem/lib/lib.php";
				addstamina(25000);
				}
			}

			page_footer();
			break;
			
		case "request":
			page_header("First Class has its privileges!");
			output("`2As holder of an Improbable Island Railway Company `bFirst Class`b Rail Pass, you have the privilege of asking the train to let you off anywhere on the map. Yes, even in the deepest ocean, should that be your whim! This is, after all, an `iImprobable`i railway system; it can accomplish the seemingly impossible.`n`nSimply tell the `@Conductor `2where on the Island you would like to be dropped off.`0`n`n");
			rawoutput("<form action='runmodule.php?module=rail_ironhorse&op=requestfinish&hid=".$fromhid."&rid=".$fromrid."' method='POST'>");
			// Note: Width 2 means a 2-digit number. Set the default location to 13,11 Improbable Central.
			rawoutput("X = <input name='stopX' width='2' value='13'> , Y = <input name='stopY' width='2' value='11'><br/><br/>");
			rawoutput("<input type='submit' class='button' value='".translate_inline("Stop here!")."'>");
			rawoutput("</form>");
			addnav("","runmodule.php?module=rail_ironhorse&op=requestfinish&hid=$fromhid&rid=$fromrid");
			addnav("Wait, I've changed my mind");
			addnav("`2Stay `0 on board","runmodule.php?module=rail_ironhorse&op=board&hid=$fromhid&rid=$fromrid");
			
			page_footer();
			break;
			
			case "requestfinish":
			page_header("First Class has its privileges!");
			$x = httppost("stopX");
			$y = httppost("stopY");
			// strip out any non-numeric characters that got entered by mistake
			$x = ereg_replace("[^0-9]", "", $x); 
			$y = ereg_replace("[^0-9]", "", $y); 
			// make sure they entered values that are in range for the size of the map.
			$sizeX = get_module_setting("worldmapsizeX","worldmapen");
			$sizeY = get_module_setting("worldmapsizeY","worldmapen");
			if ($x <= 0 || $x > $sizeX || $y <= 0 || $y > $sizeY) {
				output("`2Sorry mate, stay on the map. Train service doesn't run outside the Improbability bubble. Nice try though.`0");
				addnav("Oops!");
				addnav("Let's try that again","runmodule.php?module=rail_ironhorse&op=request&hid=$fromhid&rid=$fromrid");
			}else{
				output("`0The `@Conductor `0smiles, all sleek and fangsome, as you prepare to disembark. \"`2Thank-you for travelling with the `bImprobable Island Railway Company`b. Please don't forget to take all your luggage with you, and have an improbable day!`0\"`n`n");
				rail_ironhorse_cleanup($fromhid);
				$maploc = $x.",".$y.",1";
				set_module_pref("worldXYZ",$maploc,"worldmapen");
				addnav("Thanks!");
				addnav("`2Leave `0 the train","runmodule.php?module=worldmapen&op=continue");
			}
			page_footer();
			break;

		case "choose":
			$pass = httpget("pass");
			if ($pass == "first"){
				rail_ironhorse_activatepass("railpassfirst");
			} else {
				rail_ironhorse_activatepass("railpass");
			}
			addnav("","runmodule.php?module=rail_ironhorse&op=board&hid=$fromhid&rid=$fromrid");
			redirect("runmodule.php?module=rail_ironhorse&op=board&hid=$fromhid&rid=$fromrid");
			break;
			
		case "stationmaster":
			page_header("The Stationmaster");
			$val = rail_collector_valuehand();
			// note, we only get here if there's a joker in the hand, so only first class passes
			if ($val['value'] > 2){
				$phrase = "that's quite a hand you have there. Quite a hand! I think in this case we could even stretch to `b".$val['value']."`b First Class Rail Passes.";
			} else if ($val['value'] > 1){
				$phrase = "excellent. For that hand, we could easily give you `btwo`b First Class Rail Passes.";
			} else {
				$phrase = "no question about it, definitely a `bFirst Class Rail Pass`b.";
			}
			output("`2Having heard that this eccentric railroad company will sometimes give out a rail pass in exchange for used playing cards, you show the Stationmaster your grubby little collection. \"`#Interested?`2\" you ask.`n`n\"`^Why yes. Those would be worth... let me see. Oh, %s`2\"`0`n`n",$phrase);
			addnav("What do you say?");
			addnav("It's a deal!","runmodule.php?module=rail_ironhorse&op=stationmasterdeal&hid=$fromhid&rid=$fromrid");
			addnav("No thanks, I'll wait","runmodule.php?module=improbablehousing&op=interior&hid=$fromhid&rid=$fromrid");
			page_footer();
			break;
			
		case "stationmasterdeal":
			page_header("The Stationmaster");
			$val = rail_collector_valuehand();
			for ($i=0; $i<$val['value']; $i++){
				give_item("railpassfirst");
			}
			$qty = rail_collector_emptyhand();
			output("`2The Stationmaster thanks you, and the two of you make the exchange to your mutual satisfaction. What on earth the Company `iwants`i with all those old playing cards... well. You've naturally wondered about that from time to time, but they're not saying.`0`n`n");
			addnav("Leave");
			addnav("Return to the platform","runmodule.php?module=improbablehousing&op=interior&hid=$fromhid&rid=$fromrid");
			page_footer();
			break;
	}

}

?>