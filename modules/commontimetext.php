<?php

function commontimetext_getmoduleinfo(){
	$info = array(
		"name"=>"Common Ground: Time-dependant Flavour Text",
		"version"=>"2010-03-31",
		"author"=>"Dan Hall",
		"category"=>"Time and Weather",
		"download"=>"",
	);
	return $info;
}
function commontimetext_install(){
	module_addhook("gardentext");
	return true;
}
function commontimetext_uninstall(){
	return true;
}
function commontimetext_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "gardentext":
			global $outdoors, $shady, $brightness, $rainy, $override_weather;
			$shady = true;
			$outdoors = true;
			$rainy = false;
			$override_weather = true;
			$td = gametimedetails();
			$gt = $td['secssofartoday'];
			// Time designations:
			// 4am-12pm = morning
			// 12pm-7pm = afternoon
			// 7pm-9pm = dusk
			// 9pm-4am = night
			if ($gt<14400 || $gt>75601){
				//between 9pm and 4am, nighttime
				$brightness = "darkest";
				$args['text']="It is nighttime, but Common Ground is bright and lively.  Hundreds of tiny candle flames from the carnivorous Witchblooms cast a soft, warm light on human skin, a glossy sheen over KittyMorph fur, hard little gleaming flashes over Robot glass.  Every now and then a moth or a mosquito will fly too close to a flame, and its tiny remains will fall into the petals as the Witchbloom's victual.`n`n";
			} else if ($gt>14401 & $gt<43200){
				//4am to 12pm, morning
				//$brightness = "lighter";
				$args['text']="It is morning on Common Ground.  Sleeping Midgets lie around the area - when they awaken, they'll wonder why their empty beer cans have gone missing even as their hangovers remain.  The grass is always just the right length, the flowerbeds always just wild enough to be alive, the trees always just tall enough to provide shade but not leafy enough to blot out the sun.  Nobody has figured out how this happens - maybe there's some grumbling old parkie who comes around and tidies up the place when nobody's looking, or perhaps folks absently pick up here and there without really realising it, or it could even be that Common Ground keeps things pleasant all by itself.`n`n";
			} else if ($gt>43201 & $gt<68400){
				//12pm to 7pm, afternoon
				//$brightness = "lighter";
				$args['text']="It is afternoon on Common Ground.  The air here somehow always has the clear, bright quality that you find on summer afternoons after rainy mornings.  The forest canopy sets soft shadows swaying as trees rustle in warm breeze.  KittyMorphs lie around chatting, as close to nude as manners permit.  Nearby, Midgets leer at the sight and then deny doing so to their friends.  To one end of Common Ground a wounded Robot stands in the sunlight, charging her solar cell as the cracks in her glass skin slowly shorten and disappear with little high-pitched clinking sounds.  Her eyes are dark - she's as close to asleep as Robots ever come.`n`n";
			} else if ($gt>68401 && $gt<75600){
				//7pm to 9pm, dusk
				$brightness = "darker";
				$args['text']="It is dusk on Common Ground.  The air is cool and sharp, the grass almost damp.  If you listen carefully, you can hear the tiny clicks and snaps of the carnivorous Witchblooms as they struggle bravely against their own absurdity.  If they're lucky, they will generate enough of a spark to ignite their tiny candlelike flames, luring moths and other nocturnal insects to their sticky petals.  If they're unlucky, they will try again tomorrow night, lacking the common sense to die as such a ludicrous plant surely must.  A nearby Mutant, knowing a comrade when he sees one, appears to be composing a poem.  You move on quickly before he tries to show it to you.`n`n";
			}
			$args['text'].="Not all of the Improbability Drive's creations are threatening.  Common Ground is a convenient and rather beautiful fold in space and time, distorting our accepted reality and giving us convenience and heightened diplomacy in return.  The gardens of Common Ground have entrances from every Outpost, but contestants can only leave to the Outpost from which they arrived.  Battles and disputes over ownership are pointless.  Violence, oddly enough, just doesn't work - knives go missing from holsters, gunpowder mysteriously fails to flash, heads somehow tend not to be in the right place when clubs swing for them.  It is a peaceful place.`n`n";
			break;
		}
	return $args;
}
function commontimetext_run(){
}
?>