<?php

	require_once "modules/improbablehousing/lib/lib.php";
	require_once "modules/rail/lib.php";

function rail_collector_getmoduleinfo(){
	$info = array(
	"name"=>"Improbable Railway - Collector",
	"version"=>"2010-06-06",
	"author"=>"Sylvia Li",
	"category"=>"Improbable Rail",
	"download"=>"",
	"prefs"=>array(
		"Playing Cards for Rail Passes -  User Preferences,title",
		"cardluck"=>"Player's luck at cards today:,range,0,4,1,int|0",
		"cardstoday"=>"Number of cards player has found today:,int|0",
		"wherefound"=>"Array of where player has found cards today:,viewonly|array()",
		),
	"settings"=>array(
		"Playing Cards for Rail Passes - Settings,title",
		"cardluckchance0"=>"Chance - total 100 - that cardluck will be 0:,int|5",
		"cardluckchance1"=>"Chance - total 100 - that cardluck will be 1:,int|60",
		"cardluckchance2"=>"Chance - total 100 - that cardluck will be 2:,int|25",
		"cardluckchance3"=>"Chance - total 100 - that cardluck will be 3:,int|5",
		"cardluckchance4"=>"Chance - total 100 - that cardluck will be 4:,int|5",
		"jokerchance"=>"One out of how many cards found will be a joker?,int|30",
		"findchance"=>"One out of how many entries into a registered room will find a card?,int|5",
		),
	);
	return $info;
}

function rail_collector_install(){
//	$locs = rail_collector_getlocs();
//	foreach($locs AS $loc => $locarray){
//		$hid = $locarray[0];
//		$rid = $locarray[1];
//		$hookname = "dwelling-".$hid."-".$rid;
//		module_addhook($hookname);
//	}
	module_addhook("improbablehousing_interior");
	module_addhook("newday");
	module_addhook("iitems_tradables-top");
	module_addeventhook("forest", "return 10;");	// very rare
	module_addeventhook("travel", "return 5;");		// *extremely* rare
	return true;
}

function rail_collector_uninstall(){
	return true;
}

function rail_collector_dohook($hookname,$args){
	global $session;
	switch ($hookname){
		case "newday":
			set_module_pref("cardstoday",0);
				
			$wherefound = array();
			set_module_pref("wherefound",serialize($wherefound));

			// calculate cardluck:
			// (we do it this way to have better control over the shape of the curve - bell is too generous)
			$c0 = get_module_setting("cardluckchance0");
			$c2 = get_module_setting("cardluckchance2");
			$c3 = get_module_setting("cardluckchance3");
			$c4 = get_module_setting("cardluckchance4");
			// a little paranoia never hurt anyone
			$c1 = 100 - $c0 - $c2 - $c3 - $c4;
			if ($c1 <= 0){
				// the module settings are fubar'd. Go with the defaults
				$c0=5; $c1=60; $c2=25; $c3=5;
			}
			$t0 = $c0; $t1 = $t0 + $c1; $t2 = $t1 + $c2; $t3 = $t2 + $c3;

			$d100 = e_rand(1,100);
			switch ($d100){
				case ($d100 <= $t0):
					$luck = 0;
					break;
				case ($d100 <= $t1):
					$luck = 1;
					break;
				case ($d100 <= $t2):
					$luck = 2;
					break;
				case ($d100 <= $t3):
					$luck = 3;
					break;
				default:
					$luck = 4;
			}
			set_module_pref("cardluck", $luck);
			if (rail_hascard("cardcase")){
				if ($luck == 0){
					output("`2Something seems out of kilter today, you're not sure what.`0`n");
				}
				if ($luck >= 3){
					output("`2You feel very lucky today! Wonderful things might happen to you.`0`n");
				}
			}

			// award rail passes:
			$val = rail_collector_valuehand();
			if (!$val['firstclass'] && $val['value'] > 0){
				// no Joker, so give them however many regular rail passes they are entitled to:
				for ($i=0; $i<$val['value']; $i++){
					give_item("railpass");
				}
				// empty their hand (and if they had more than 5 cards, well... tough.)
				$qty = rail_collector_emptyhand();
				if ($val['value'] == 1){
					$phrase = "an `bImprobable Island Railway Company RAIL PASS`b";
				} else {
					$phrase = "not just one, but `b".$val['value']." Improbable Island Railway Company RAIL PASSES`b";
				}
				output("`2Uh-oh, your card case feels much lighter. You had %s valuable cards here and they've all been bloody `istolen!`i Filthy midget bast-- wait, what? What's this? In their place, you now have %s! Awesome! ...um, so maybe it wasn't midgets after all. Critical mass, improbability, whatever. Who cares!`0`n",$qty,$phrase);
			}	// end - awarding of rail passes
		break;	// end case 'newday'

		case "iitems_tradables-top":
			// remove any rail-feature tradables (ie, cards) if recipient has no card case.
			if (has_item("cardcase",false,$args['tradewith']['acctid']) === false){
				$playerhascards = false;
				if (is_array($args['tradables'])){
					foreach ($args['tradables'] AS $tkey => $tdetails){
						if ($tdetails['feature'] == "rail"){
							$playerhascards = true;
							unset($args['tradables'][$tkey]);
						}
					}
				}
				if ($playerhascards){
					output("Unfortunately, %s has no place to put any cards.`n`n",$args['tradewith']['name']);
				}
			}
		break;	// end case 'iitems_tradables-top'

		case "improbablehousing_interior":
			$hid = $args['hid'];
			$rid = $args['rid'];
			$locs = rail_collector_getlocs();
			$collectspot = 0;
			$key = "unknown";
			$phrase = "in a shadowy corner of the room";
			foreach($locs AS $loc => $locarray){
				if (($hid == $locarray[0]) && ($rid == $locarray[1])){
					// We're in a place where a card could be found
					$collectspot = 1;
					$key = $loc;
					$phrase = $locarray[2];
					break;		// no need to keep looking through the rest of the array
				}
			}
			if ($collectspot){
				// not allowed to find more than one card in a location
				$wherefound = unserialize(get_module_pref("wherefound"));
				$foundhere = 0;
				if (is_array($wherefound)){
					foreach($wherefound AS $lc => $larray){
						if(($larray[0] == $hid) && ($larray[1] == $rid)){
							// woops, they already found a card in this location today
							$foundhere = 1;
						}
					}
				}
				if (!$foundhere){
					debug("finding card?");
					// all right, let them try their luck
					if (rail_collector_findcard()){
						$wherefound[$key] = array($hid,$rid);
						set_module_pref("wherefound",serialize($wherefound));
						output("`2What luck! You find a smudged, battered old playing card %s. You've heard these can be worth quite a bit to a collector. Carefully you tuck it away in your fine leather card case.`0`n`n",$phrase);
					}
				} else {
					debug("Player has already found a card here.");
				}
			}
		break;
		}			// end $hookname switch
	return $args;
}

function rail_collector_runevent($type,$link){
	global $session;
	if (rail_hascard("cardcase")){
		// yes, findcard also calls hascard, but here they are going to get a different message 
		// to give them a clue that they should go get a cardcase to start playing the collecting game
		if (rail_collector_findcard()){
			output("`2Half-hidden in damp leaves under a bush you notice a smudged, battered old playing card. Who knows, perhaps it might be worth something to a collector. Carefully you wipe it off and tuck it away in your fine leather card case.`0`n`n");
		} else {
			// The event was triggered, but they didn't qualify for a card. Have to do something...
			// this is a bad message, but they're still going to get a 'Something Improbable!' page.
			// Leave as is for now. To-do: change this some other card-related event.
			output("`2Preoccupied with more important matters, you fail to notice a smudged, battered old playing card half-hidden in the damp leaves under a bush.`0`n`n");
		}
	} else {
		output("`2Half-hidden in damp leaves under a bush you notice a smudged, battered old playing card. You can't think what use it could be -- and anyway, you have no place to keep it -- so you don't trouble to pick it up.`0`n`n");
	}
}


function rail_collector_run(){
}

?>
