<?php

function onslaught_getmoduleinfo(){
	$info = array(
		"name"=>"Onslaught",
		"version"=>"2009-10-26",
		"author"=>"Dan Hall",
		"category"=>"Improbable",
		"download"=>"",
		"settings"=>array(
			"spawn"=>"Creature Spawn Rate,int|100",
		),
		"prefs-city"=>array(
			"breachpoint"=>"Number of Creatures that must be present before this Outpost's walls are breached,int|1000",
			"attraction"=>"Creatures attracted to this Outpost?,int|100",
			"creatures"=>"Creatures currently stalking this Outpost,int|0",
			"onslaught_started"=>"Timestamp at which Onslaught of this Outpost began,int|0",
			"info"=>"Outpost's Onslaught Info array,text|array()",
		),
		"prefs"=>array(
			"info"=>"Player's Onslaught Info array,text|array()",
		),
	);
	return $info;
}
function onslaught_install(){
	module_addhook("village");
	module_addhook("newday");
	return true;
}
function onslaught_uninstall(){
	return true;
}
function onslaught_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "village":
			$lv = onslaught_checkbreach();
			if ($lv>=100){
				redirect("runmodule.php?module=onslaught&op=start");
			} else {
				switch ($session['user']['location']){
					case "NewHome":
						switch ($lv){
							case ($lv<10):
								output("`0NewHome seems quiet at the moment - by which you mean there are no monsters actively banging on the gates.  The people manning the turret-mounted machine guns are chatting jovially with each other and sharing cigarettes.  For now at least, NewHome is safe.`n`n");
							break;
							case ($lv<25):
								output("`0You glance upwards, towards the Monster Defence Turrets.  Their custodians are idly scanning the horizon.  From time to time, a gunner will nudge his or her fellow guard and point somewhere off in the middle distance.  NewHome remains well-guarded.`n`n");
							break;
							case ($lv<50):
								output("`0The brave men and women atop the Monster Defence Turrets seem more alert than usual - they attentively focus on the surrounding Jungle, keeping watch over their territory.  Every now and then a gun will swivel suddenly and take aim, only for trigger fingers to become relaxed as a slavering beast is confirmed as nothing more than a shadow or a rustle in the breeze.`n`n");
							break;
							case ($lv<70):
								output("`0Shots have recently been fired from the Monster Defence Turrets, and the smell of gunpowder hangs in the air.  The atmosphere is tense, guarded - the people of NewHome glance nervously at their Outpost's buttresses, wondering if people would think them paranoid if they just, y'know, shored them up a little bit.  A few new planks here and there.  Nothing to worry about.  Nothing at all.`n`n");
							break;
							case ($lv<80):
								output("`0Bursts of machine gun fire can occasionally be heard from the Monster Defence Turrets.  Their custodians laugh and cheer as monsters fall - most of their days are slow, tedious, made up of perpetual smoking and the occasional unfortunately misdetected rabbit, and it's nice to get some action once in a while.  The people of NewHome go about their daily business, but their smiles seem strained, as if pasted on.  Some of them are casually inspecting the Outpost walls, taking measurements and carrying hammers.`n`n");
							break;
							case ($lv<90):
								output("`0Things up in the Monster Defence Turrets are getting a little tense.  The joyful laughter from the gunners first slowed then stopped as their unexpected fun gave way to unexpected challenge.  The atmosphere is charged - shops are still open, but you have to knock to be let in.  Hands are gently gripping weapons.  Talk is subdued.  Cold sweat is trickling down spines.  A few citizens are making themselves busy hammering new planks to the Outpost walls to reinforce them.  NewHome is, it would seem, under threat.`n`n");
							break;
							case ($lv<95):
								output("`0The guns in the Monster Defence Turrets haven't stopped firing for the past few minutes.  Conversation is subdued under frequent bursts of fire.  The gunners swear and call down for more ammunition, their hands sweaty, their faces red.  Outside the Outpost, bullets thump into earth and flesh, churning the grass into brown and red ruin.  Inside, citizens run back and forth between the Outpost walls, the turrets, the ammunition stores and the lumber piles, wondering why the hell they didn't reinforce the walls yesterday, or the week before, or the month before that.`n`neBoy's is doing a roaring trade in first aid kits and grenades, and Sheila is staying open for just as long as she can, serving Customers Who Aren't Looters At All, No Sir, with one hand on her favourite shotgun.  As for the other merchants, well...`n`nThe Museum is closed and locked up tight, as is the Hunter's Lodge, the Council Offices, Corporal Punishment's Basic Training, the Bank, the Comms Tent, the Strategy Hut, Mike's Chop Shop...  even greasy old Joe is standing outside his diner, sharpening a large meat cleaver.  NewHome is preparing for the worst.`n`n");
								blocknav("lodge.php");
								blocknav("runmodule.php?module=basictraining&op=start");
								blocknav("runmodule.php?module=counciloffices&councilop=enter");
								blocknav("runmodule.php?module=newhomemuseum&op=lobby");
								blocknav("bank.php");
								blocknav("gypsy.php");
								blocknav("runmodule.php?module=staminafood&op=start&location=nh");
								blocknav("runmodule.php?module=strategyhut");
								blocknav("stables.php");
							break;
							case ($lv<100):
								output("`0All conversation in the Outpost square has ceased.  Screaming and cursing can be heard from overhead, the custodians of the Monster Defence Turrets throwing out all the lead they can.  The angle of their fire has lowered over the past few minutes, and now powerful shoulders crunch at the Outpost's frail wooden walls, serrated claws splinter the defences.  The gunners' hands are numb, their ears ringing, their throats on fire, their barrels glowing red.`n`nAll you can smell is the gunner's smoke.  All you can hear is their fire.  You see people screaming, but you don't hear them.  You see the gunners frantically gesturing for more ammunition, but there isn't enough to go around.  Doors have gone missing from shops and turned up nailed to the Outpost walls.  Still some stragglers stumble into the Outpost from the melee outside, covered in blood and panting, showering all the thanks in the world on the gunners, their preferred deity, or both.`n`nMister Stern sits on the steps of his museum polishing his glasses, a shiny little six-shot revolver lying on the steps beside him.  He puts his glasses back on and picks the gun up, looking it over as though he's not sure what to do with it.  Corporal Punishment sits beside Mister Stern, one arm around the thin man, the other resting on his own pistol.  Over at Sheila's, Mike is hovering indecisively around the door with a single red rose in one hand and a machete in the other.`n`nHeads turn as a single mighty BANG pushes the Northernmost Outpost walls inwards, splintering the buttresses.  A long, black claw is quickly hacked off by a nearby citizen, and lies twitching on the ground.`n`nNewHome prepares for death or glory.`n`n");
							break;
						}
					break;
					case "Kittania":
					break;
					case "New Pittsburgh":
					break;
					case "Squat Hole":
					break;
					case "Pleasantville":
					break;
					case "Cyber City 404":
					break;
					case "AceHigh":
					break;
					case "Improbable Central":
					break;
				}
			}
			break;
		}
	return $args;
}

function onslaught_run(){
}

function onslaught_spawn(){
}

function onslaught_checkbreach(){
	global $session;
	$cid = get_cityprefs_cityid("location",$session['user']['location']);
	$breachpoint = get_module_objpref("city",$cid,"breachpoint");
	$creatures = get_module_objpref("city",$cid,"creatures");
	return ($creatures/$breachpoint)*100;
}

?>