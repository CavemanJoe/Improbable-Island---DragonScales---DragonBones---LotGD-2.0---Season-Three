<?php
// ====================================================================================
// This rail/lib.php provides the following functions:
//
// DWELLING LOCATION functions - used to get modulehooks and to tailor behaviour to a dwelling
//		rail_peddler_getloc()
//		rail_ironhorse_getlocs()
//		rail_collector_getlocs()
//
// GENERAL FUNCTIONS - used throughout the feature
//		rail_hascard($searchitem)
//		rail_discard_item($search,$invloc=false)
//
// IRON HORSE functions - used on board the train
//		rail_ironhorse_canboard()
//		rail_ironhorse_activatepass($pass)
//		rail_ironhorse_cleanup($hid)
//
// COLLECTOR functions - used for the card collecting part of the feature
//		rail_collector_valuehand()
//		rail_collector_emptyhand()
//		rail_collector_findcard()
// ====================================================================================

// DWELLING LOCATION functions: 
// to install feature in live game, swap in the other version of the array assignments.
//
function rail_peddler_getloc(){
	// Live Location, uncomment for live game
	// Grand Concourse East, map square 14,11
	
	$loc = array(	
		"peddlerhid"=>257,
		"peddlerrid"=>0,
	);
	
	/*
	// Test Location, comment out for live game
	// Grand Concourse East, map square 14,11	
	$loc = array(
		"peddlerhid"=>251,
		"peddlerrid"=>0,
	);
	*/
	return $loc;
}

function rail_ironhorse_getlocs(){
	// Array of locations where people can get off (and usually board) the train.
	// Each location element is an array with the following information:
	//   key=>(	house id of station, 
	//			room id of platform, 
	//			verbose stop-name used in the link, 
	//			last city name for later failboat restoration etc, 
	//			platform description shown when leaving the train at this station)
	//
	$locs = array(
	

	// LIVE GAME
	//   AH (10,30) - Lucky Dip: Lucky Dip Station	
	//   CC (22,38) - The Scrapyard: The Terminus	
	//   IC (14, 4) - Grand Concourse East: Track A
	//   KT (21,12) - Nepeta Halt: Longhouse
	//   NH (11, 5) - Main Street Station: Passenger Hall	
	//   NP ( 4,15) - NECROPOLIS STATION: Waiting Room
	//   PV (13,23) - Pleasantville Station: Waiting Room
	//   SH ( 8,17) - West Skronky Siding: Waiting Room
	//   WA (21,19) - Abandoned Waystation: Derelict Train Yard

	"AH" => array( 18, 4, "Lucky Dip", "AceHigh",
	"The back door of the Lucky Dip is painted green. The air here is cool and fresh, fragrant with pine, and faintly... could that be a small hint of iced cookies, lingering yet? Perhaps it is only the sweet smell of cake wafting from nearby -- or death. It might be death."),
	"CC" => array( 81, 3, "The Terminus", "Cyber City 404",
	"The cold dark Terminus platform smells of rust, of oil, and long-ago broken dreams."),
	"IC" => array(257, 1, "Grand Concourse", "Improbable Central",
	"The hurrying crowds on the Grand Concourse platform don't look up because they are too busy to gawk. Besides, they've seen it already. What, you think they're tourists? They've got trains to catch, appointments to keep. Welcome to Improbable Central!"),
	"KT" => array(261, 0, "Nepeta Halt", "Kittania",
	"Kittania's jungle is warm, humid, scented with jasmine and rain. Brightly feathered birds screech and twitter beyond the platform."),
	"NH" => array(260, 0, "Main Street", "NewHome",
	"Ah yes, NewHome! where tea and sympathy are offered in equal measure to naked shivering newcomers. And sandwiches, do not `iever`i forget about the sandwiches!"),
	"NP" => array(259, 0, "Necropolis", "New Pittsburgh",
	"Zombies and beaches. What's not to like?"),
	"PV" => array(263, 0, "Pleasantville", "Pleasantville",
	"Neat and unreflective, that pretty much sums up Pleasantville. Oh... and the `ibest`i steaks."),
	"SH" => array(258, 0, "Skronky Siding", "Squat Hole",
	"It'll be a long muddy trail through the swamps before you get to Squat Hole."),
	"WA" => array(227,12, "The Waystation", " ",
	"Wait, what happened? What is this place? This isn't where you meant to get off!"),
	"DB" => array ( 57,64, "The Subway Station", " ",
	"Wait, what happened? What is this place? This isn't where you meant to get off!"),
	);
	
	/*
	// TEST STOPS
	"AH" => array ( 47, 0, "Twisted Spoon", "AceHigh",					// 13,11
	"The back door of the Lucky Dip is painted green. The air here is cool and fresh, fragrant with pine, and faintly... could that be a small hint of iced cookies, lingering yet? Perhaps it is only the sweet smell of cake wafting from nearby -- or death. It might be death."),
	"CC" => array (250, 1, "Legba's Back Step", "Cyber City 404",		// 12,10
	"The cold dark Terminus platform smells of rust, of oil, and long-ago broken dreams."),
	"IC" => array (251, 1, "Grand Concourse", "Improbable Central",		// 14,11
	"The hurrying crowds on the Grand Concourse platform don't look up because they are too busy to gawk. Besides, they've seen it already. What, you think they're tourists? They've got trains to catch, appointments to keep. Welcome to Improbable Central!"),
	"KT" => array ( 20, 0, "The Darjeeling", "Kittania",				// 14,10
	"Kittania's jungle is warm, humid, scented with jasmine and rain. Brightly feathered birds screech and twitter beyond the platform."),
	"NH" => array ( 16, 0, "Soup and Pants", "NewHome",					// 13,11
	"Ah yes, NewHome! where tea and sympathy are offered in equal measure to naked shivering newcomers. And sandwiches, do not `iever`i forget about the sandwiches!"),
	"NP" => array (217, 0, "Testing Warehouse", "New Pittsburgh",		// 12,10
	"Zombies and beaches. What's not to like?"),
	"PV" => array ( 80, 2, "Silverhall LDD", "Pleasantville",			// 13,12
	"Neat and unreflective, that pretty much sums up Pleasantville. Oh... and the `ibest`i steaks."),
	"SH" => array (  1, 0, "Mushroom Cottage", "Squat Hole",			// 13,10
	"It'll be a long muddy trail through the swamps before you get to Squat Hole."),
	"WA" => array (114, 0, "The Haunted House", " ",					// 14,12
	"Wait, what happened? What is this place? This isn't where you meant to get off!"),
	
	);		// end of $locs array assignment
	*/
	return $locs;
}

function rail_collector_getlocs(){
	// Array of locations where people may or may not find a playing card.
	// Each location element is an array with the following information:
	//   key=>(	house id, 
	//			room id, 
	//			a descriptive phrase about where the card can be found) 
	//
	// The key is not currently used by the code and could be anything; right now it's just a mnemonic.
	//
	$locs = array(
	

	// LIVE GAME card-finding locations
	"foilwench" => array(193,0,"half-buried in the litter box"),
	"blueberry" => array(294,0,"wind-blown into the branches"),
	"olddojo" => array(177,0,"under an exercise mat"),
	"trendykitchen" => array(187,4,"tucked unobtrusively between two bottles of red wine in the wine rack"),
	"farmkitchen" => array(397,0,"beside the basket of new-laid eggs"),
	"crowbar" => array(305,1,"while snooping in a filing cabinet that's definitely none of your business, but hey. As long as you're here, right? What the hell, that sign is just rude"),
	"pubgames" => array(10,1,"folded in half and wedged under the leg of one of the card tables"),
	"pillows" => array(313,1,"under one of the pillows"),
	"cellar" => array(63,0,"half-hidden behind one of the barrels"),
	"hotspring" => array(221,1,"in a stack of lemon-fresh clean white towels"),
	"mineralogy" => array(419,14,"weighted down by a small, dark green sample with veins of ochre red. The neat but slightly worrying legend-card underneath the playing card reads `iThe Plug of the Lake`i. Huh. Well"),
	"questions" => array(419,26,"almost obscured by a hand-written ad with tear-off tabs offering a second-hand pet couch to a good home"),
	"printer" => array(300,4,"on top of one of the type cabinets"),
	"marblearch" => array(180,17,"wedged between the needle and the dial"),
	"hedgemaze" => array(282,7,"inside a windmill"),
	"fishpond" => array(282,8,"in the gravel beside the bench"),
	"paddlewheeler" => array(296,11,"caught in an eddy of wind behind the smokestacks"),
	"numeralii" => array(61,13,"in the drawer"),
	"hanger" => array(249,2,"on the workbench beside a tumbled heap of cogs and gears"),
	"weather" => array(373,0,"on the floor next to the desk"),
	"parlour" => array(223,4,"under a loose brick by the disused fireplace"),
	"closet" => array(223,6,"in the bottom of one of the boxes"),
	"hauntedkitchen" => array(223,7,"under the dusty but untarnished cream jug"),
	"topiary" => array(223,27,"pushed into the dry crusted mouth of the east-facing copper fish"),
	"schoolroom" => array(223,26,"among the pages of sheet music"),
	"study" => array(223,17,"laid across the open bible"),
	"hauntedbathroom" => array(223,15,"on the threshold, just as you're closing the door"),
	"risingsun" => array(393,7,"when you almost have one foot on the platform, the other foot on the train"),
	"coachhouse" => array(57,66,"on the cracked leather seat of the pony-trap"),
	"recground" => array(57,12,"lying on the poker-table"),
	"dumbwaiter" => array(57,62,"just lying there in one corner of the red-tiled floor"),
	"omnibus" => array(57,84,"whirled by the wind practically into your hands. One of the friskily gambolling old people does try to snatch it from you, but you are much too fast for her"),
	"anarchy" => array(21,0,"stapled to the bulletin board under a hand-lettered sign that says EVIDENCE OF BOURGEOIS OPPRESSION. You surreptitiously palm it when `JAshtu`2 is not looking"),
	"irishtavern" => array(26,5,"left behind on your table by some earlier patron"),
	"firepit" => array(6,1,"in the loose sand near the firepit, right beside the hedgehog-fan stand of marshmallow-roasting sticks"),
	"tardis" => array(138,20,"on the glassy floor"),
	"pirate" => array(128,2,"pinned by a dirk to the main mast"),
	"library" => array(41,0,"in the tiny clutching paws of a wide-eyed gremlin. You offer an H you defeated in the Jungle, and soon complete the trade"),

	/*	
	// TEST card-finding locations
	"games" => array(217,4,"under one leg of a card table"),
	"kitchen" => array(217,3,"right out on the kitchen counter"),
	"workshop" => array(250,0,"behind a pile of tools"),
	"library" => array(41,0,"tucked between the pages of a book"),
	*/
	);
	return $locs;
}

//
// GENERAL FUNCTIONS - used throughout the feature
//

function rail_hascard($search){
	// this function is here because often all we want is a simple: yes or no, does player have x?
	require_once("modules/iitems/lib/lib.php");
	if ((has_item($search) === false)){
		return false;
	} else {
		return true;
	}
}

function rail_discard_item($search){
	// we need this because delete_item takes a numeric key, and we want to delete by name
	// this will delete the first one found
	global $session, $inventory;
	if (!isset($inventory)){
		load_inventory();
	}
	
	$found = has_item($search);

	if ($found === false){
		return false;
	} else {
		delete_item($found);
		return true;
	}
}

//
// IRON HORSE functions - used on board the train
//

function rail_ironhorse_canboard(){
	if (rail_hascard("railpass_active")
	||  rail_hascard("railpassfirst_active")
	||  rail_hascard("railpass")
	||  rail_hascard("railpassfirst")){
		return true;
	} else {
		return false;
	}
}

function rail_ironhorse_activatepass($pass){
	// remove railpass or railpassfirst, and replace with railpass_active or railpassfirst_active
	$activepass = $pass."_active";
	rail_discard_item($pass);
	give_item($activepass);
	return true;
}

function rail_ironhorse_cleanup($hid){
	// code here is copied from exit.php in improbablehousing/run.
	// the train extracts the player from the station dwelling 
	//   so we need to remove all traces that they were there.
	global $session;
	$house = improbablehousing_gethousedata($hid);

	// Run through the rooms and make sure the player isn't registered as 
	// being in them or sleeping in them
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
//				output("`0Occupant %s: %s`0`n",$okey,$house['data']['rooms'][$rkey]['occupants'][$okey]);
//				output("okey..%s, acctid..%s`n",$okey,$session['user']['acctid']);
				if ($okey == $session['user']['acctid']){
//					output("- unset`n");
					unset($house['data']['rooms'][$rkey]['occupants'][$okey]);
				}
			}
		}
	}

	improbablehousing_sethousedata($house);
	clear_module_pref("sleepingat","improbablehousing");

	return true;
}

//
// COLLECTOR functions - used for the card collecting part of the feature
//

function rail_collector_valuehand(){
	// returns an array with 'value' equal to the number of rail passes the hand is worth
	// and 'firstclass' indicating which kind of pass they'd get
	global $session, $inventory;
	$handvalue = array();
	if (!isset($inventory)){
		load_inventory();
	}
	$items = group_items($inventory);
//	debug($items);
	$passcount = 0;
	$cardcount = 0;
	foreach ($items AS $ptr => $details){
//		debug($ptr);
//		debug($details);
		$pos = strpos($details['item'],"railcard");
		if ($pos === 0){		// Boolean false if not found; pos 0 if found in, eg, "railcard01"
			$passcount += ($details['quantity'] - 1);
			$cardcount += $details['quantity'];
		}
	}
	$passcount++;
	
	if ($cardcount >= 5){
		$handvalue['value'] = $passcount;
	} else {
		$handvalue['value'] = 0;
	}
	 
	if (rail_hascard("railcard50")){
		$handvalue['firstclass'] = 1;
	} else {
		$handvalue['firstclass'] = 0;
	}
	
	return $handvalue;
}

function rail_collector_emptyhand(){
	// returns the number of cards that have been removed
	global $session, $inventory;
	if (!isset($inventory)){
		load_inventory();
	}
	$items = group_items($inventory);
	$qty = 0;
	foreach ($items AS $ptr => $details){
		$itemname = $details['item'];
		$pos = strpos($itemname,"railcard");
		if ($pos === 0){		// Boolean false if not found; pos 0 if found in, eg, "railcard01"
			$qty = $qty + delete_all_items_of_type($itemname);
		}
	}
	return $qty;
}

function rail_collector_findcard(){
	// returns false if the user has no card case, or if they've reached their cardluck limit.
	// otherwise picks a random card to give them and returns true.
	global $session;
	if (rail_hascard("cardcase")){
		debug("Got the case");
		$cardstoday = get_module_pref("cardstoday","rail_collector");
		if ($cardstoday < get_module_pref("cardluck","rail_collector")){
			$jokerchance = get_module_setting("jokerchance","rail_collector");
			$joke = false;
			if (rail_hascard("railcard50")){
				$joke = true;
			}
			if (!$joke && (e_rand(1,$jokerchance) == 1)){
				// card is a joker
				give_item("railcard50");
			} else {
				// pick a card, any card!
				$suit = e_rand(0,4);
				$pips = e_rand(0,9);
				$cardgiven = "railcard".$suit.$pips;
				give_item($cardgiven);
			}
			$cardstoday = $cardstoday + 1;
			
			set_module_pref("cardstoday",$cardstoday,"rail_collector");
			return true;
		} else {
			debug("No more cards for you today...");
			// output("No more cards for you today!!!");
			return false;
		}
	} else {
		// output("You have no place to put a card!");
		return false;
	}
}

?>
