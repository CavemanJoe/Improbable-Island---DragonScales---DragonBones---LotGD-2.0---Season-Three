<?php

function onslaught_getmoduleinfo(){
	$info = array(
		"name"=>"Onslaught",
		"version"=>"2012-03-17",
		"author"=>"Dan Hall, modified by Cousjava",
		"category"=>"Improbable",
		"download"=>"",
		"settings"=>array(
			"difficulty"=>"Difficulty slider - lower numbers spawn more monsters,int|100",
			"spawnrate"=>"Today's Creature Spawn Rate as a percentage of normal,int|100",
			"spawnevery"=>"Spawn creatures every x seconds,int|600",
			"lastspawn"=>"Timestamp of last Monster Spawn,int|0",
			"testmode"=>"Onslaught is in Test Mode and no output will be generated,bool|1",
			"damagemultiplier"=>"Damage multiplier for Outpost walls,float|1.0",
			"maxcityhealth"=>"Maximum hitpoints a city can have. -1 for no limit.,int|100000000",
		),
		"prefs-city"=>array(
			"breachpoint"=>"Number of Creatures that must be present before this Outpost's walls begin taking damage,int|1000",
			"defences"=>"Outpost's Defence Hitpoints,int|1000000",
			"spawnrate"=>"Today's Spawn Rate for this particular Outpost as a percentage of normal,int|100",
			"creatures"=>"Creatures currently stalking this Outpost,int|0",
			"onslaught_started"=>"Timestamp at which Onslaught of this Outpost began,int|0",
			"info"=>"Outpost's Onslaught Info array,text|array()",
		),
		"prefs"=>array(
			"info"=>"Player's Onslaught Info array,text|array()",
			"justresurrected"=>"Player was just resurrected, and shouldn't be dumped into a village with an active onslaught,bool|0",
			"user_optin"=>"You are safe from the effects of Outpost invasions until you pass level ten in your first Drive Kill.  Would you like to opt-in anyway?,bool|0",
		),
	);
	return $info;
}
function onslaught_install(){
	module_addhook("village");
	module_addhook("newday");
	module_addhook("newday-runonce");
	module_addhook("battle-victory");
	module_addhook("alternativeresurrect");
	module_addhook("worldnav");
	module_addhook("counciloffices");
	module_addhook("creatureencounter");
	require_once("modules/staminasystem/lib/lib.php");
	install_action("Reinforcement",array(
		"maxcost"=>5000,
		"mincost"=>2000,
		"firstlvlexp"=>500,
		"expincrement"=>1.05,
		"costreduction"=>30,
		"class"=>"Building"
	));
	return true;
}
function onslaught_uninstall(){
	return true;
}
function onslaught_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "alternativeresurrect":
			set_module_pref('justresurrected',1);
		break;
		case "counciloffices":
			if (httpget('councilop')!="enter"){
				break;
			}
			output("A mechanical readout mounted on the wall gives the threat levels for the various Outposts on the Island:`n`n");
			$sql = "select * from ".db_prefix("cityprefs");
			$result=db_query_cached($sql,"allcityprefs");
			for ($i = 0; $i < db_num_rows($result); $i++){
				$row = db_fetch_assoc($result);
				$cid = $row['cityid'];
				// debug($row);
				$creatures = get_module_objpref("city",$cid,"creatures");
				// debug($creatures);
				$defences = get_module_objpref("city",$cid,"defences");
				switch ($creatures){
					case ($creatures < 100):
						$condition = "`@Peaceful Days (reduced Requisition payouts)`0";
					break;
					case ($creatures < 250):
						$condition = "`2Quiet Times`0";
					break;
					case ($creatures < 500):
						$condition = "`2Minor Activity`0";
					break;
					case ($creatures < 600):
						$condition = "`qGuarded Atmosphere`0";
					break;
					case ($creatures < 700):
						$condition = "`qIncreased Activity`0";
					break;
					case ($creatures < 800):
						$condition = "`QAssistance Required`0 (increased Requisition payouts unless Outpost is breached)";
					break;
					case ($creatures < 900):
						$condition = "`4Critical Situation`0 (increased Requisition payouts unless Outpost is breached)";
					break;
					case ($creatures > 900):
						$condition = "`$`bInteresting Times`b`0 (doubled Requisition payouts unless Outpost is breached)";
					break;
				}
				if (!$creatures){
					$condition = "`@Peaceful Days (reduced Requisition payouts)`0";
				}
				output("`b%s`b: %s`n",$row['cityname'],$condition);
			}
		break;
		case "newday-runonce":
			$newspawnrate = e_rand(1,200);
			set_module_setting("spawnrate",$newspawnrate);
			onslaught_shuffleoutposts();
		break;
		case "worldnav":
			if (get_module_pref('justresurrected') == 1) set_module_pref('justresurrected',0);
		break;
		case "newday":
			$info = @unserialize(get_module_pref("info"));
			if (isset($info['companions_this_onslaught'])){
				unset($info['companions_this_onslaught']);
				set_module_pref("info",$info);
			}
			set_module_pref('justresurrected',1);
		break;
		case "village":
			onslaught_spawn();
			$lv = onslaught_checkmonsters();
			$num = onslaught_nummonsters();
			$def = onslaught_checkwalls();

			debug("Alert: ".$lv);


			if (!get_module_setting("testmode")){
				if ($session['user']['level'] < 10 && $session['user']['dragonkills']==0 && !get_module_pref("user_optin")){
					$skipeffects=1;
				}
				addnav($args["gatenav"]);
				addnav("Reinforce the Defences","runmodule.php?module=onslaught&op=reinforce");
				if ($lv>100 && $def<$num && !$skipeffects){
					if (get_module_pref('justresurrected')==1) {
						set_module_pref('justresurrected',0);
						redirect("runmodule.php?module=worldmapen&op=beginjourney","Redirected to avoid putting freshly-resurrected player back into an Outpost under Breach");
					} else {
						redirect("runmodule.php?module=onslaught&op=start&nodesc=1","Onslaught internal redirect while reinforcing");
					}
				} else {
					if (get_module_pref('justresurrected') == 1) set_module_pref('justresurrected',0);
					$info = @unserialize(get_module_pref("info"));
					$info['companions_this_onslaught']=array();
					set_module_pref("info",serialize($info));
					switch ($session['user']['location']){
						case "NewHome":
							switch ($lv){
								case ($lv<10):
									output_notl("`0NewHome seems quiet at the moment - by which you mean there are no monsters actively banging on the gates.  The people manning the turret-mounted machine guns are chatting jovially with each other and sharing cigarettes.  For now at least, NewHome is safe.`n`n");
								break;
								case ($lv<25):
									output_notl("`0You glance upwards, towards the Monster Defence Turrets.  Their custodians are idly scanning the horizon.  From time to time, a gunner will nudge his or her fellow guard and point somewhere off in the middle distance.  NewHome remains well-guarded.`n`n");
								break;
								case ($lv<50):
									output_notl("`0The brave men and women atop the Monster Defence Turrets seem more alert than usual - they attentively focus on the surrounding Jungle, keeping watch over their territory.  Every now and then one of the massive chainguns will swivel suddenly and take aim, only for trigger fingers to become relaxed as a slavering beast is confirmed as nothing more than a shadow or a rustle in the breeze.`n`n");
								break;
								case ($lv<60):
									output_notl("`0Shots have recently been fired from the Monster Defence Turrets, and the smell of gunpowder hangs in the air.  The atmosphere is tense, guarded - the people of NewHome glance nervously at their Outpost's buttresses, wondering if people would think them paranoid if they just, y'know, shored them up a little bit.  A few new planks here and there.  Nothing to worry about.  Nothing at all.`n`n");
								break;
								case ($lv<70):
									output_notl("`0Bursts of machine gun fire can occasionally be heard from the Monster Defence Turrets.  Their custodians laugh and cheer as monsters fall - most of their days are slow, tedious, made up of perpetual smoking and the occasional unfortunately misdetected rabbit, and it's nice to get some action once in a while.  The people of NewHome go about their daily business, but their smiles seem strained, as if pasted on.  Some of them are casually inspecting the Outpost walls, taking measurements and carrying hammers.`n`n");
								break;
								case ($lv<80):
									output_notl("`0Things up in the Monster Defence Turrets are getting a little tense.  The joyful laughter from the gunners first slowed then stopped as their unexpected fun gave way to unexpected challenge.  The atmosphere is charged - shops are still open, but you have to knock to be let in.  Hands are gently gripping weapons.  Talk is subdued.  Cold sweat is trickling down spines.  A few citizens are making themselves busy hammering new planks to the Outpost walls to reinforce them.  NewHome is, it would seem, under threat.`n`n");
								break;
								case ($lv<90):
									output_notl("`0The guns in the Monster Defence Turrets haven't stopped firing for the past few minutes.  Conversation is subdued under frequent bursts of fire.  The gunners swear and call down for more ammunition, their hands sweaty, their faces red.  Outside the Outpost, bullets thump into earth and flesh, churning the grass into brown and red ruin.  Inside, citizens run back and forth between the Outpost walls, the turrets, the ammunition stores and the lumber piles, wondering why the hell they didn't reinforce the walls yesterday, or the week before, or the month before that.`n`neBoy's is doing a roaring trade in first aid kits and grenades, and Sheila is staying open for just as long as she can, serving Customers Who Aren't Looters At All, No Sir, with one hand on her favourite shotgun.  As for the other merchants, well...`n`nThe Museum is closed and locked up tight, as is the Hunter's Lodge, the Council Offices, Corporal Punishment's Basic Training, the Strategy Hut, Mike's Chop Shop...  even greasy old Joe is standing outside his diner, sharpening a large meat cleaver.  NewHome is preparing for the worst.`n`n");
									blocknav("lodge.php");
									blocknav("runmodule.php?module=basictraining&op=start");
									blocknav("runmodule.php?module=counciloffices&councilop=enter");
									blocknav("runmodule.php?module=newhomemuseum&op=lobby");
									blocknav("runmodule.php?module=staminafood&op=start&location=nh");
									blocknav("runmodule.php?module=strategyhut");
									blocknav("stables.php");
								break;
								default:
									output_notl("`0All conversation in the Outpost square has ceased.  Screaming and cursing can be heard from overhead, the custodians of the Monster Defence Turrets throwing out all the lead they can.  The angle of their fire has lowered over the past few minutes, and now powerful shoulders crunch at the Outpost's frail wooden walls, serrated claws splintering the defences.  The gunners' hands are numb, their ears ringing, their throats on fire, their barrels glowing red.`n`nAll you can smell is the gunner's smoke.  All you can hear is their fire.  You see people screaming, but you don't hear them.  You see the gunners frantically gesturing for more ammunition, but there isn't enough to go around.  Doors have gone missing from shops and turned up nailed to the Outpost walls.  Still some stragglers stumble into the Outpost from the melee outside, covered in blood and panting, showering all the thanks in the world on the gunners, their preferred deity, or both.`n`nMister Stern sits on the steps of his museum polishing his glasses, a shiny little six-shot revolver lying on the steps beside him.  He puts his glasses back on and picks the gun up, looking it over as though he's not sure what to do with it.  Corporal Punishment sits beside Mister Stern, one arm around the thin man, the other resting on his own pistol.  Over at Sheila's, Mike is hovering indecisively around the door with a single red rose in one hand and a machete in the other.`n`nHeads turn as a single mighty BANG pushes the Northernmost Outpost walls inwards, splintering the buttresses.  A long, black claw is quickly hacked off by a nearby citizen, and lies twitching on the ground.`n`nNewHome prepares for death or glory.`n`n");
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
							}
						break;
						case "Kittania":
							switch ($lv){
								case ($lv<10):
									output_notl("`0Kittania is quiet, as per usual.  The archers patrolling the walls are mostly standing around chatting.  For the moment, Kittania is safe.`n`n");
								break;
								case ($lv<25):
									output_notl("`0You glance upwards, to check on the archers.  They are idly moseying along the perimeter, their bows still slung across their shoulders.  As you watch, one of them tests the point of an arrow with her finger.  Kittania remains well-guarded.`n`n");
								break;
								case ($lv<50):
									output_notl("`0The archers patrolling the Outpost walls seem more alert than usual - one of them is pointing somewhere off in the distance, his friends following the line of his finger.  They are frowning - something that KittyMorphs seldom do.`n`n");
								break;
								case ($lv<60):
									output_notl("`0An arrow runner is jogging along the Outpost walls, handing bundles of extra ammunition to the archers who have now taken up their stations and stand with fingers gently teasing their strings.  Their eyes are wide, their expressions fixed.  Their tails twitch.  It seems that Kittania is preparing for something interesting.`n`n");
								break;
								case ($lv<70):
									output_notl("`0The occasional arrow flies from the Outpost walls with a sound like tearing silk.  The archers adopt the wide-eyed, tail-twitching attack stances of their feline genetic cousins.  Below them, on the Outpost grounds proper, the people of Kittania unhurriedly hammer lengths of timber to the Outpost walls.  Every now and then a claw, slyly extended without the knowledge of its owner, will become hooked or tangled in a piece of clothing or a length of wood - much to its bearer's embarrassment.`n`n");
								break;
								case ($lv<80):
									output_notl("`0Fur stands on end when its owner feels threatened, so the people of Kittania look a little bigger than usual.  They have good reason; arrows are flying from the Outpost walls, and backup archers are climbing the ladders to help.  The dying screams and gurgles of their targets are louder now - the horde is getting close.  Conversation is stilted, claws are peeking out and smiles show more teeth than they should.  Kittania is, it would seem, under threat.`n`n");
								break;
								case ($lv<90):
									output_notl("`0Arrows are flying from the Outpost walls like hail, stabbing into monsters that are now far too close for comfort.  Inside the Outpost, citizens run back and forth between the walls and the lumber piles, wondering why the hell they didn't reinforce the walls yesterday, or the week before, or the month before that.`n`neBoy's is doing a roaring trade in first aid kits and grenades, and Sheila is staying open for just as long as she can, serving Customers Who Aren't Looters At All, No Sir, with one hand on her favourite shotgun.  As for the other merchants, well...`n`nYou look over to the Cool Springs Cafe - it's locked up tight.  Pity.  The underground cafe would have made an excellent place to hide until this all blows over.`n`n");
									blocknav("runmodule.php?module=counciloffices&councilop=enter");
									blocknav("runmodule.php?module=staminafood&op=start&location=ki");
									blocknav("runmodule.php?module=meatschool&op=start");
									blocknav("runmodule.php?module=marriage&op=chapel");
									blocknav("stables.php");
								break;
								default:
									output_notl("`0The rate of arrows flying from the Outpost walls has slowed somewhat, but for all the wrong reasons - the archers reach behind their backs to grab arrows that aren't there.  Below, veteran combat KittyMorphs solemnly assume stances in front of the Outpost gates, their bodies as fixed as stone save for their tails, which twitch and dance with each resounding `icrash`i from the now-splintered walls.  Behind them, ordinary citizens hang around nervously, holding weapons both manufactured and improvised.  Some are holding hands, some are kissing - some are going further, knowing that they might not get another chance.  Some have cheek fur darkened with tears.`n`nMaiko stands at the head of the line, her knives freshly sharpened, her feet planted firmly, a tiny smile dancing around the corners of her mouth.  She catches your eye and gives you a sly wink, as the monsters outside continue tearing down the walls.`n`nKittania is about to find out what it's made of.`n`n");
									blocknav("runmodule.php?module=counciloffices&councilop=enter");
									blocknav("bank.php");
									blocknav("gypsy.php");
									blocknav("runmodule.php?module=staminafood&op=start&location=ki");
									blocknav("runmodule.php?module=meatschool&op=start");
									blocknav("runmodule.php?module=marriage&op=chapel");
									blocknav("stables.php");
									blocknav("runmodule.php?module=meatschool&op=start");
								break;
							}
						break;
						case "New Pittsburgh":
							switch ($lv){
								case ($lv<10):
									output_notl("`0You glance upwards, towards the guards patrolling the walls.  They are unarmed, as is the Zombie tradition, and they honestly don't seem at all worried.  They shuffle around aimlessly as is their fashion, with nothing more than the occasional cry of \"BRAAAAINS.\"  For now at least, things are peaceful in New Pittsburgh.`n`n");
								break;
								case ($lv<25):
									output_notl("`0You glance upwards towards the guards patrolling the Outpost walls.  They shuffle back and forth aimlessly, with the occasional cry of \"BRAAAAINS.\"  But look - one of them is staring off into the distance.  Could there be something interesting out there?  \"BRAAAAAINS?\"  Maybe?  Perhaps?`n`nThe Zombie shrugs dejectedly.  It was just a rabbit.  \"BRAAAAAINS,\" he moans, disappointed.`n`n");
								break;
								case ($lv<50):
									output_notl("`0You glance up towards the guards patrolling the Outpost walls.  One of them is pointing off somewhere in the middle distance.  \"BRAAAAINS!\" he calls to his friends.`n`n\"BRAAAINS?\" they respond.`n`n\"BRAAAAAAAAAAAAAAINS!\" he replies.`n`nThe rallying call is quickly taken up.  Soon more Zombies are staring off into the distance, pointing, salivating onto their clothing.  \"BRAAAAAAAAINS!\"`n`nLooks like the monster activity around New Pittsburgh is increasing somewhat.`n`n");
								break;
								case ($lv<60):
									output_notl("`0\"BRAAAAINS!\"`n`nThe Zombies of New Pittsburgh are shuffling up the staircases that line the Outpost's defensive walls.  The call has been taken up, and answered.  The scent of approaching hordes of fresh monsters has sent the Zombies into... not a frenzy, not exactly, but certainly an anticipatory mood.  They are lining the walls, watching the horizon, sniffing the air and salivating.`n`nMeanwhile, the members of other races inside the Outpost glance at each other nervously.  Zombies never panic when the subject of Outpost defence comes up, but they have been known to get so caught up in the brain-eating that they allow monsters to simply run past them and tear at the Outpost walls.  A few forward-thinking members of the public are casting evaluatory eyes over the Outpost walls, checking the lumber piles.  We shall see if their concerns are well-founded.`n`n");
								break;
								case ($lv<70):
									output_notl("`0The staircases that lead up to the Outpost walls are full of Zombies, who by now are craning their necks to get a better look at the approaching monsters.  They're getting close, now.  You can smell them - and so can the Zombies, whose frequent cries of \"BRAAAAAINS!\" are growing louder, more emphatic.  Soon they will decide they've had enough of watching - soon they will decide that the creatures are close enough to make it worth the effort of lurching out there to devour them.  But not yet.  For now, the Zombies are content to watch, and drool, and twitch, and roll their eyes around in their sockets.`n`nUnless something is done, the Zombies of New Pittsburgh will eat well today - and the other races, the ones left inside the Outpost... well, they're on their own.`n`n");
								break;
								case ($lv<80):
									output_notl("`0Zombies are shuffling down the staircases that line the Outpost walls.  A few minutes ago, they were ascending to watch the approaching horde - now they descend, and make their inexorable way towards the Outpost gates.  Humans, KittyMorphs, Robots, Jokers and Mutants watch them descend, and grimace.  They know that it's time to reinforce the Outpost walls, and they set about the task with resigned determination, if such an emotion can be said to exist.`n`nThe Zombies are preparing to leave New Pittsburgh and devour as many of the approaching monsters as they can.  Things are going to get interesting.`n`n");
								break;
								case ($lv<90):
									output_notl("`0The hordes of monsters outside the Outpost gates has grown ever larger, and the cries of \"BRAAAAINS!\" that echoed through the Outpost not half an hour ago have died down.  In their place, a new murmuring has arisen.`n`n\"OM.  NOM.  NOM.  NOM.  OM.  NOM.  NOM.  NOM.\"  The Zombies' battle cry.  Or their call to dinner.`n`nTheir arms slowly raise into the traditional lunging position as the Zombies begin to file slowly out of the Outpost.`n`nThe non-Zombie population of the Outpost, along with one or two Zombies with somewhat more modern views of warfare, are busying themselves buying up weapons and armour at Sheila's Shack, which remains open by necessity, and grenades and medkits at eBoy's, who remains open out of opportunity.  A lot of other businesses have closed.  One or two civilians try to persuade the Zombies to stay inside the Outpost, to fight alongside them.  They might as well try to bargain with a glacier.`n`nThings are about to get interesting in New Pittsburgh.`n`n");
									blocknav("runmodule.php?module=counciloffices&councilop=enter");
									blocknav("stables.php");
									blocknav("runmodule.php?module=staminafood&op=start&location=np");
								break;
								default:
									output_notl("`0Most of the Zombies have left New Pittsburgh.  They are busy feasting on the creatures that now surround the Outpost.  Outside you can hear the crack of skulls and the slurping of delicious brains - but for every head split open, for every chunk of grey matter that splats upon the ground, one more Zombie is distracted for a few more minutes by his meal - and several more nightmares run past him, to claw at the Outpost gates.  And claw they do.`n`nThe reinforcements, hastily improvised by the non-Zombie civilians now left alone inside the Outpost, are looking shaky.  The hammering and ripping and chopping sounds are coming thick and fast.  Most businesses have closed, their owners either devouring still-warm brains outside or cowering underneath the counters with a shotgun in their hands.`n`nIf something isn't done, if reinforcements are not called in, if the monsters are left unchallenged, if the walls breach - then New Pittsburgh will be overrun in a heartbeat.`n`nFlee, fight, fortify, or fall.  Your move.`n`n");
									blocknav("runmodule.php?module=counciloffices&councilop=enter");
									blocknav("bank.php");
									blocknav("gypsy.php");
									blocknav("stables.php");
									blocknav("runmodule.php?module=staminafood&op=start&location=np");
								break;
							}
						break;
						case "Squat Hole":
							switch ($lv){
								case ($lv<10):
									output_notl("`0Squat Hole appears to be its usual foetid self.  The violence is centered inside the Outpost rather than outside, and the guards patrolling the walls are busy perfecting their skiving skills.  The pile of empty cider cans beneath their posts is growing at the rate of several cans per minute - for the moment at least, Squat Hole is secure under their slightly blurry eyes.`n`n");
								break;
								case ($lv<25):
									output_notl("`0You glance up at the wall patrol.  One Midget is pointing off into the distance, another one trying to focus on what he's indicating.  With a snort of derisive laughter, the watching Midget clouts the pointing Midget around the head.  \"`2Thassa fookin' `irabbit`i yer plonker!`0\"`n`nFor the moment, unless some strange trick of perspective has made the rabbit appear much smaller than it actually is, Squat Hole is still well-guarded.`n`n");
								break;
								case ($lv<50):
									output_notl("`0The patrol Midgets, knowing that someone might be watching, are strolling reluctantly back and forth along the walls, smoking hand-rolled cigarettes and spitting over the sides.  Their banter seems a little more lively - perhaps it's because they sighted, somewhere in the distance, something that would be fun to fight.`n`n");
								break;
								case ($lv<60):
									output_notl("`0\"`2Gerra bloody move on!`0\"`n`nYou look up towards the source of the sound.  It's not often you hear a Midget's voice coming from above, so you know before you look that it'll be one of the wall patrol guards.  \"`2Up there, yer fookin' muppet!`0\"`n`n\"`5Wha' tha fook yer on abaaht, yer dick'ead?`0\"`n`n\"`2Jus' bleein' `ilook,`i will yer?`0\"`n`nThe two Midgets stand in silence for a moment, staring at something outside of the Outpost.`n`n\"`5Fookin' `imiles`i away yet,`0\" says the second Midget, sitting down for another smoke.`n`n");
								break;
								case ($lv<70):
									output_notl("`0You can hear a rumbling from outside - as though from a thousand pairs of feet, or tentacles, or claws.  As the rumbling grows louder, one Midget swears, annoyed at having his smoke break interrupted.  While slowly getting to his feet he snorts, spits, coughs, farts and belches in sequence, then reaches behind himself to scratch at his arse.  Now standing, he stubs out the butt of his previous cigarette before pulling one from behind his ear.  The thundering stampede of the horde outside grows louder as he searches his pockets for a lighter, pulling out spoons, odd change, cigarette ash, half-eaten pizza slices and hairballs.  He finds his lighter and puts it towards its intended purpose, before sauntering over to one of the huge cart-mounted catapults lining the edges of the Outpost.  Taking his cigarette from his mouth for a moment to roll out one more almighty rippling belch, he kicks the tyre closest to him.  He frowns and wrinkles his nose before shrugging and sitting down beside the catapult to finish his cigarette.  Outside, the horde of monsters stampedes ever closer.`n`nApparently Squat Hole is preparing for a bust-up, albeit in the most half-arsed way imaginable.`n`n");
								break;
								case ($lv<80):
									output_notl("`0You look up towards the wall patrol guards, such as they are.  Amazingly, they're all standing!  The guards line the Outpost walls with half-bricks in their hands, tossing them violently towards the approaching horde.  Cries of `i\"`2Gerrahdovvit yer basta'ds!`0\"`i and `i\"`2Tha' all yer got, yer fookin' pansies?`0\"`i and \"`2Fer fook's sake, innit beer o' clock yet?`0\" echo throughout the Outpost.  The Midget tending to the emergency catapults has taken notice, and is endeavouring to finish his cigarette as quickly as possible.`n`nSensing misadventure, a few Midgets are heading over to the timber stores to help shore up the Outpost's defensive walls - but cider doesn't make things easier, and by the time they reach the timber, they've forgotten why they went, and concluded that they must have been on the nick.  Thus the timber supply slowly and quietly diminishes, none of it heading towards the Outpost walls.  Squat Hole may be in trouble.`n`n");
								break;
								case ($lv<90):
									output_notl("`0Most of the shops are closed, their owners very much aware of the threat of looters.  Up on the walls, a hundred squeaky voices yell jeers and insults at the oncoming horde.  The half-bricks are sailing through the air at a rate of knots.  With a reluctant sigh, the catapult attendant Midget stubs out his cigarette and begins winding back the first emergency catapult.  A mob of disgruntled Midgets forms around him, drawn by the spectacle of the catapult attendant actually standing up.  \"`2Right, who's first?`0\" mumbles the attendant Midget around a new cigarette.  A huge, intimidating four-foot brawler of a Midget steps forward, and clambers into the cup.`n`n\"`4Is it arrigh' if I take me knife, like?`0\" he asks quietly, brandishing the weapon in question.`n`nThe mob bursts into peals of laughter.  \"`2You fookin' `isissy`i,`0\" says the attendant, shaking his head and grinning.  He yanks on the release lever, and the Midget sails over the Outpost walls.  Outside you hear the screams and hurried footsteps of terrified monsters from the rough area in which he must have landed.`n`nSquat Hole is in enough trouble that the Midgets are actually doing something about it.  That means a `ilot`i of trouble.`n`n");
									blocknav("runmodule.php?module=counciloffices&councilop=enter");
									blocknav("runmodule.php?module=staminafood&op=start&location=sq");
									blocknav("runmodule.php?module=skronkypot&op=examine");
									blocknav("runmodule.php?module=tynan&op=gym");
									blocknav("stables.php");
								break;
								default:
									output_notl("`0Squat Hole prepares for an invasion in the only way it knows how - by heaving half-bricks at it.  The improvised missles fly from the Outpost walls, followed by barrages of angry Midgets launched by the emergency catapults.  Outside the Outpost where they land, monsters scatter, terrified.`n`nInside the Outpost, the traffic at the timber stores has reached a fever pitch.  Knowing that the walls are in dire need of reinforcement, more than a hundred Midgets have turned up carrying hammers and nails and received their free timber.  As a consequence, more than a hundred new, smaller timber stores have popped into existence, with timber available for low, low prices.`n`nAs you watch, one Midget engages in a heated battle - a slavering beast has ripped a plank clean off the wall from the outside, and the Midget is hanging on for dear life, shaking the plank to try to get the monster to let go.  Eventually she succeeds, and runs off grinning with the plank back to her own little timber store while monsters pour in through the hole.  A few spectating Midgets suddenly have an epiphany, and start tearing planks from the walls for their own stores.`n`nSquat Hole is quite frankly fucked.`n`n");
									blocknav("runmodule.php?module=counciloffices&councilop=enter");
									blocknav("runmodule.php?module=staminafood&op=start&location=sq");
									blocknav("runmodule.php?module=skronkypot&op=examine");
									blocknav("runmodule.php?module=tynan&op=gym");
									blocknav("runmodule.php?module=brothel&op=enter");
									blocknav("bank.php");
									blocknav("gypsy.php");
									blocknav("stables.php");
								break;
							}
						break;
						case "Pleasantville":
							switch ($lv){
								case ($lv<10):
									output_notl("`0Pleasantville appears its usual self.  Its population carries on about the usual business of not looking each other in the eye.  Glancing up at the guards patrolling the walls, you can see that they're calmly and vigilantly keeping a watch over the surrounding area, their rifles still slung over their humped and twisted backs.  Life in Pleasantville goes on without threat.`n`n");
								break;
								case ($lv<25):
									output_notl("`0You glance up at the wall patrol.  One Mutant with eyes the size of dinner plates is staring off into the distance, seemingly fixated on something moving somewhere far off.  One other Mutant is watching - but to her plentiful but regular-sized eyes, the danger is so far off that it seems barely worth commenting on.`n`n");
								break;
								case ($lv<50):
									output_notl("`0You glance upwards, towards the guards patrolling the Outpost walls.  One of them is cleaning his rifle by slowly running one furry, foot-long finger back and forth inside the dismantled barrel.  You stop to watch - it's oddly hypnotic for some reason that you can't quite put your finger on.  As you watch him cleaning his rifle, you notice his expression - staring off into the middle distance at something outside of the outpost.  He looks a little worried - but then, Mutants usually do, don't they?`n`n");
								break;
								case ($lv<60):
									output_notl("`0The guards atop the Outpost walls are giving each other nervous glances.  One or two of them - the ones with unusual finger configurations - are refamiliarising themselves with their rifles, making sure that everything's loaded, oiled up and ready to fire.  At what, we're not yet sure - but there's definitely `isomething`i out there that's making them nervous.`n`n");
								break;
								case ($lv<70):
									output_notl("`0Shots have recently been fired from the Outpost walls, and the guards are looking a little nervous.  There's no real cause for alarm - the shots were fired at targets far off - but any time an Outpost has to use its defences, people tend to get a tad antsy regardless.  Inside the Outpost, a few Mutants are paying a little more attention to the walls - perhaps they could use some reinforcements.`n`n");
								break;
								case ($lv<80):
									output_notl("`0The guards manning the Outpost walls are firing regularly now, and the sharp tang of gunpowder hangs in the air.  One Mutant is running back and forth between the ladders and the ammunition stockpiles; four of his arms are loaded down with ammunition, leaving two arms free for flailing.`n`nCuthbert strides confidently along the Outpost walls, lending moral support and Ronsen-Kiai instructions to the gunners.  Their shouted insults punctuate their gunfire.`n`n\"`6`iWankers!`i`0\"`n`4BANG`0`n\"`8`iMewling halfwits!`i`0\"`n`4BANG`0`n\"`2`iLick my slimy undercarriage, you droning imbecilic son of a two-balled bitch!`i`0\"`4BANG BANG BANG`0`n`nMeanwhile, the folks inside the Outposts are hurriedly hammering new wood to the walls in a last-minute attempt to reinforce them.  Pleasantville is, it would seem, in for something interesting.`n`n");
								break;
								case ($lv<90):
									output_notl("`0The guards atop the Outpost walls are furiously firing at the monsters just outside the outpost gates.  Their fire is deafening, their insults filthy and coarse.  Ammunition runners are wearing out the soles of their shoes, and civilians are frantically hammering new wood to the Outpost walls.  Every few seconds you can hear a bang or a scrape as a monster hits the outer walls before being gunned down.  If someone doesn't do something soon, Pleasantville will be in serious trouble - as it is, the Council Offices, bank, Comms Tent and Mike's Chop Shop have all closed up in preparation for the worst.`n`n");
									blocknav("runmodule.php?module=counciloffices&councilop=enter");
									blocknav("runmodule.php?module=insultschool&op=start");
									blocknav("runmodule.php?module=staminafood&op=start&location=pl");
									blocknav("runmodule.php?module=drpap&op=enter");
									blocknav("stables.php");
								break;
								default:
									output_notl("`0The rate of fire from the guards atop the Outpost walls has slowed, but not in a good way - they are running out of ammunition, and strangled cries can now be heard overhead as monsters begin to scale the walls.  Several Mutant guards have abandoned their now-empty rifles and switched to their combat knives.  Blood trickles down the inside of the Outpost walls, and is quickly covered by hastily reinforced sections thanks to dutiful civilians.`n`nMost shops are closed, given the situation.  Cuthbert has abandoned his post atop the walls, and has taken up position just inside the Outpost, facing the gates.  One large permanent marker is gripped tightly in each hand; improvised yawara for the guy who's always been so adamant about the relative efficacy of pen versus sword.  He sees you watching and gives you a nod as another Mutant runs past him, flailing his many, many arms and screaming something about certain death.`n`nIn all fairness, he's probably got a point.`n`n");
									blocknav("runmodule.php?module=counciloffices&councilop=enter");
									blocknav("runmodule.php?module=insultschool&op=start");
									blocknav("runmodule.php?module=staminafood&op=start&location=pl");
									blocknav("runmodule.php?module=drpap&op=enter");
									blocknav("bank.php");
									blocknav("gypsy.php");
									blocknav("stables.php");
								break;
							}
						break;
						case "Cyber City 404":
							switch ($lv){
								case ($lv<10):
									output_notl("`0Cyber City 404 is quiet, its Robot inhabitants going about their daily business with their usual inhuman efficiency.  The alert level lamps on the Outpost gates are all blue - there is, for the moment, no threat of invasion.`n`n");
								break;
								case ($lv<25):
									output_notl("`0You glance upwards, to check on the lightning turrets that surround the Outpost.  The occasional whine of a servo can be heard as a turret idly tracks something far off.  The alert level lamps on the Outpost gates are all blue - one will occasionally shift to green for a moment as a turret rotates, then back to blue again.  Cyber City 404 is still well-guarded, and its inhabitants are as calm as only Robots know how to be.`n`n");
								break;
								case ($lv<50):
									output_notl("`0The turrets atop the Outpost walls are twitching back and forth, focussing on something out of their firing range.  The alert level lamps on the Outpost gates flicker between blue and green, and the Outpost's inhabitants glance over towards them occasionally to check on them.  Cyber City 404 is safe, for the moment - but some clearly feel that the turrets will have to start firing again soon.`n`n");
								break;
								case ($lv<60):
									output_notl("`0The alert level lamps on the Outpost walls are a solid green.  Diesel generators have started up, their pounding compressions drowning out the sound of the turrets' servo motors as they spin back and forth, tracking creatures just out of their range - if they were sentient, they would undoubtedly be feeling a bit frustrated by now.  As for the sentient creatures within the Outposts - well, they're going about their daily business as per usual, calm and calculating, but one or two of them can be seen carrying extra lengths of timber back and forth between the stockpiles and the Outpost walls.  It seems that the City of Machines is preparing for an altercation.`n`n");
								break;
								case ($lv<70):
									output_notl("`0You glance at the alert level lamps on the Outpost gates.  As you watch, one of them flickers from green to orange, and your shadow is briefly visible against the wall - a split-second later, an almighty `iCRACK`i tears through your eardrums.  You swivel in the direction of the sound, as do several other citizens; one of the lightning turrets has just fired, and steam rises from its conductor as it fades from a dull red back to silver.`n`nAs a mild scent of ozone fills the air, and the ringing in your ears dies down, a handful of Robots turn from their paths and approach the timber stockpiles, ready to start reinforcing the walls.`n`n");
								break;
								case ($lv<80):
									output_notl("`0The alert level lamps on the Outpost gates are flickering between orange and green now, as the creatures outside the Outpost get within firing range.  The turrets are firing once every few seconds, and the scent of ozone hangs heavy in the air.  Robots hurry back and forth between the timber stockpiles and the outpost walls, and a few can be seen refuelling the diesel generators that power the turrets.  Conversation is subdued under the turrets' thunder, and the atmosphere is charged in more ways that one.  Cyber City 404 is, it would seem, in for something big.`n`n");
								break;
								case ($lv<90):
									output_notl("`0The alert level lamps on the Outpost gates are flickering between orange and red - that means that the lightning turrets can't keep up, and have started to overheat.  It's no surprise to anyone, as they've been firing constantly for the past few minutes.  Conversation is next to impossible over the racket.  The residual static from the lightning turrets is making hair stand on end, and ozone hangs heavy in the air.  Your shadow dances against every Outpost wall, and little purple spots dance in front of your eyes.  Robots are now running back and forth between the Outpost walls and the timber stockpiles, reinforcing them as best they can.`n`neBoy's is doing a roaring trade in first aid kits and grenades, and Sheila is staying open for just as long as she can, serving Customers Who Aren't Looters At All, No Sir, with one hand on her favourite shotgun.  As for the other merchants, well...`n`nThe Council Offices and Mike's Chop Shop are closed until further notice, and nobody can blame them.`n`n");
									blocknav("runmodule.php?module=counciloffices&councilop=enter");
									blocknav("stables.php");
								break;
								default:
									output_notl("`0The alert level lamps on the Outpost walls are glowing a steady red, with one alarming exception - one lamp isn't lit at all.  You look up around the turrets, hoping that it's just a dead bulb - but no such luck.  The turret on the far side of the Outpost is on fire, its conductor melted or vapourized away to nothing.  Strobe flashes of lightning pierce through the thick black smoke that issues from the turrets that are still running - four flashes every second.  You can hear the warm bodies exploding into steam and vapour outside, but you can hear even more of them hammering on the Outpost walls, even over the ear-splitting rapid-fire thunder of the remaining turrets.  Most businesses have closed, their owners standing outside the doors and sharpening their already razor-sharp claws.`n`nThe turrets aim downwards for a second, clearing a path for one lone Robot to arrive - she limps in on one glass leg, the other one utterly shattered.  She's covered in blood; not her own, of course, because she doesn't have any to bleed.  Her claws are shiny enough to have been sharpened that morning, but they are notched, bloody, and dull - she clearly came out the winner, but the cost is dear.  One side of her face is a nightmare of crooked glass shards - the other is missing entirely, her wires and fibreoptics trailing loose from her neck.  Her chest and back are a spiderweb of little white cracks, and her brass ribcage is visibly dented and blackened with soot.  She takes three more steps and then falls, her red camera eyes dimming to less than a candle's worth.  She will need assistance from her Robot fellows to reshape her ribcage and resolder her wiring; she will need days or even weeks in strong sunlight for her bizarre glasslike skin to heal its cracks.  First, she must survive the next ten minutes, and with the walls splintering as long black claws peek in, that seems more like a hope than a plan.`n`nCyber City 404 prepares for war.`n`n");
									blocknav("runmodule.php?module=counciloffices&councilop=enter");
									blocknav("bank.php");
									blocknav("gypsy.php");
									blocknav("stables.php");
								break;
							}
						break;
						case "AceHigh":
							switch ($lv){
								case ($lv<10):
									output_notl("`0You glance upwards, towards the Outpost walls and the curious folks patrolling them.  Both genders dress in spectacular suits of a Victorian cut, and most wear top hats.  They stride casually back and forth along the walls, some with canes, some without.  One catches your eye and nods a greeting, all smiles - clearly there is no threat to AceHigh right now.`n`n");
								break;
								case ($lv<25):
									output_notl("`0You take a look overhead, towards the Jokers patrolling the Outpost walls.  They saunter casually back and forth, pausing occasionally to flip a coin or idly roll a die as you might crack your knuckles or scratch your nose.  One Joker squints off into the middle distance, at something that other Jokers haven't noticed yet - and you're not sure whether she's going to tell or not.  Either way, for the time being, AceHigh is still safe.`n`n");
								break;
								case ($lv<50):
									output_notl("`0The Jokers atop the Outpost walls are all staring off in one direction.  They remain silent, and nearly all of them are smiling.  What this means for the people inside the Outpost could be either good or bad, but at least for now the threat - if the Jokers even `iacknowledge`i the approaching monsters as a threat, and not a delightful present - is a long way off.`n`n");
								break;
								case ($lv<60):
									output_notl("`0The Jokers atop the Outpost walls seem excited - some of them have drawn revolvers, and are checking their chambers.  Others toy excitedly with coins or dice.  Below, some people are nervously glancing at the Outpost walls and wondering whether it's time to start reinforcing them a little bit.  It couldn't hurt, right?`n`n");
								break;
								case ($lv<70):
									output_notl("`0Shots have recently been fired from the Outpost walls, and the people of AceHigh go about their business with something of a carnival atmosphere.  The few Jokers with revolvers are intently scanning the horizon, waiting for something to come within range of their oddly glowing bullets - the others play with their cards, or dice, or coins, and wait patiently and smiling for the gunners to be overwhelmed.`n`nThe non-Joker life forms within the Outpost are a sight more worried than their Joker neighbours, and have taken to reinforcing the Outpost walls.  Occasionally a Joker will mutter \"spoilsport\" in their general direction.`n`n");
								break;
								case ($lv<80):
									output_notl("`0Atop the Outpost walls, the Jokers who chose revolvers as their weapons of choice are laughing and cheering as they fire double-handed into the approaching horde of monsters.  Their projectiles leave behind bright green lines which fall slowly to the ground like cobwebs before fading.  Such small bullets do ludicrously disproportionate amounts of damage to their targets; limbs splinter, torsos fly apart, heads disintegrate.  The reports don't sound like gunfire - more like the clap of hands.  The sounds of the projectiles hitting their targets is somewhat more squelchy and bursty.  The whole thing sounds like a hundred people giving a standing ovation while holding gobbets of raw liver.  The Jokers who favoured short-range missiles are standing attentively behind the gunners, waiting for their chance to shine.  The civilians inside the Outpost don't seem to be having nearly as much fun as the Jokers patrolling the walls - they run back and forth between the timber stores and the Outpost walls, knowing that the increased rate of fire means serious trouble.  AceHigh, it seems, is in for something interesting.`n`n");
								break;
								case ($lv<90):
									output_notl("`0Hand-launched projectiles are being thrown over the side of AceHigh's defensive walls.  Cards fly in complex patterns, drawing on warm bodies fine red lines that split and widen when the muscles beneath them are foolish enough to move.  Coins from dead currencies flip upwards and then fall, slamming massive chasms into the ground or erupting huge, sharp stalagmites - heads or tails.  Dice of many sides roll across invisible tables towards their unlucky targets - the mutations they cause are immediate and catastrophic.  The Jokers are having a `igreat`i time.`n`nThe same cannot be said for the non-Joker inhabitants of AceHigh, though - nearly all of them are busy hammering new wood to the Outpost walls in a last-minute attempt to reinforce them.  You see several local business owners helping out - their shops or offices have temporarily closed.  AceHigh is preparing for the worst.`n`n");
									blocknav("runmodule.php?module=counciloffices&councilop=enter");
									blocknav("runmodule.php?module=cakeordeath&op=examine");
									blocknav("stables.php");
								break;
								default:
									output_notl("`0The Jokers guarding the Outpost walls from above are laughing, despite - or perhaps because of - the frequent `icrunch`i as a monster reaches the Outpost walls and embeds its claws.  Most businesses inside the Outpost have closed up shop, and the remaining citizens are beginning to wonder whether it's worth trying to reinforce the walls.  Outside, the most bizarre sounds can be heard as the Jokers' unlikely weapons impact against their targets - but not enough.  The walls are coming down.  AceHigh stands on a coin flip between redemption and destruction.`n`n");
									blocknav("runmodule.php?module=counciloffices&councilop=enter");
									blocknav("runmodule.php?module=cakeordeath&op=examine");
									blocknav("bank.php");
									blocknav("gypsy.php");
									blocknav("stables.php");
								break;
							}
						break;
						case "Improbable Central":
							switch ($lv){
								case ($lv<10):
									output_notl("`0You glance upwards, towards the Outpost walls.  People of every race are patrolling - KittyMorphs with bows, Mutants with sniper rifles, Robots with lightning guns, Humans with assault rifles, Midgets with grenades and half-bricks, Zombies with occasional cries of \"BRAAAAINS.\"  Even the odd Joker can be seen, carrying a deck of cards, or a die, or a coin, or a glowing, pulsating revolver.`n`nNone of them look at all concerned.  One KittyMorph laughs and flirts with a grinning Joker while a scowling Midget watches, disgusted - by the mixed race, or the unmixed gender, or both, you're not sure.  For now at least, it's business as unusual in Improbable Central.`n`n");
								break;
								case ($lv<25):
									output_notl("`0You take a look towards the top of the Outpost walls, upon which the highly Improbable guard can be seen idly patrolling and scanning the horizon for approaching monsters.  One tall Robot is peering off into the distance at something that only her high-resolution camera eyes can detect - something that, for now, is no threat to anyone.`n`n");
								break;
								case ($lv<50):
									output_notl("`0You sneak a quick glance up towards the Outpost walls, and the guards patrolling them.  A handful are gathered at one corner of the Outpost, looking through binoculars at something in the distance.  Some of them look a little troubled - others are still laughing and joking.  Improbable Central remains well-guarded.`n`n");
								break;
								case ($lv<60):
									output_notl("`0The guards are muttering to one another, and casting the occasional glance towards something that doesn't seem all that far-off anymore.  As they watch, a `\$familiar voice`0 issues from a nearby loudspeaker.`n`n\"`&Your attention please.  This is `\$The Watcher.`&  My monster alert metric for Improbable Central has passed fifty.  I needn't remind you that one hundred means almost-certain breach, so those of you not busy patrolling the Outpost walls might wish to head out into the nearby Jungles and thin the herds out a little.  That is all.`0\"`n`nA few civilians give each other worried glances.`n`n");
								break;
								case ($lv<70):
									output_notl("`0Shots have recently been fired from the Outpost walls.  The guards have taken up their stations - KittyMorphs flex their bows, Robots prime their lightning guns, Jokers roll their dice.  An anticipatory air has settled over the Outpost walls, and on the inside, civilians are casting worried glances over the timber stockpiles.  Perhaps it's time to start shoring up those defences, reinforcing the walls.`n`nA `\$familiar voice`0 issues from a nearby loudspeaker.`n`n\"`&Your attention please.  This is `\$The Watcher.`&  My monster alert metric for Improbable Central has passed sixty.  A metric of more than one hundred will easily overwhelm the guards, so the best thing you can do right now is to head out into the Jungle and thin the herds out a bit.  Thank you for your attention.`0\"");
								break;
								case ($lv<80):
									output_notl("`0The Outpost is somewhat louder than usual - the sounds of rifle shots, lightning cracks, Midget profanity and the occasional explosion can be heard from above and outside.  It seems that the horde of slavering monsters approaching the Outpost walls are keeping the guards rather busy.`n`nMeanwhile, inside the Outpost, civilians have taken up hammer and nails and are busy reinforcing the walls with new timber.  As you watch them hammer, a blinding flash of stark green light stabs between the stakes - it's gone in an instant, and in the next heartbeat, you feel the shockwave slam through your chest.`n`nPieces of monster fly into the air and fall into the Outpost, trailing smoke as they tumble, glowing a faint green.  \"`2Nice one, Elias!`0\" says a voice from overhead, and you see a Joker take a bow, before reaching into a large sack and pulling out another innocent-looking seashell.`n`n");
								break;
								case ($lv<90):
									output_notl("`0The guards atop the Outpost walls are increasing their activity somewhat - and the noise is increasing too.  The thunderclaps of lightning from Robots, the cracks of rifle shots from Mutants, the whip of bowstrings from KittyMorphs, and the colourful insults of Midgets drown out much of the conversation below.  Not that there's much conversation going on - right now, folks are more concerned with hurriedly hammering new wood to the Outpost walls in a last-minute attempt to reinforce them before the guards are overwhelmed and the monsters start tearing at the defences.  In fact, some businesses have already ceased trading, and hastily-written signs in the windows declare that they're closed until things have calmed down a bit.`n`nOverhead, the `iplink`i of a coin flip is momentarily audible, far louder than it should be.  Silence follows - you look up and see the muzzles flashing, but no sound is heard.  A Joker catches the coin, smiles, and tosses it over the side.  There's a soft `iwhump,`i followed by a strange popping noise as air rushes into a space that has just created itself.`n`n\"`1Holy shit, did you `isee`i that?!`0\" cries a Human atop the walls.  There's a strange moment where the guards simply stare at the place the coin must have landed - and from the silence outside, you reason the monsters must be doing the same.  Then the spell is broken, and the walls rattle where the monsters embed their claws and begin tearing the place down.`n`nA `\$familiar voice`0 issues from a nearby loudspeaker, sounding rather frustrated.`n`n\"`&This is `\$The Watcher.`&  My monster alert metric for Improbable Central has passed `ininety.`i  This means that...`0\"  The voice hesitates for a moment, sighs.  \"`&Oh, you `iknow`i what it means.  Look, you lot, do `inot`i make me come down there.  I mean it.  That is all.`0\"`n`nImprobable Central is about to find out what it's made of.`n`n");
									blocknav("runmodule.php?module=counciloffices&councilop=enter");
									blocknav("runmodule.php?module=gauntlet");
									blocknav("runmodule.php?module=haberdasher");
									blocknav("runmodule.php?module=iitems_invshop&op=start");
									blocknav("runmodule.php?module=jeweler");
									blocknav("runmodule.php?module=petra");
									blocknav("runmodule.php?module=oldchurch");
									blocknav("runmodule.php?module=villhut");
									blocknav("stables.php");
								break;
								default:
									output_notl("`0You can't help but glance up at the guards patrolling the Outpost walls - the noise is deafening, although where a few minutes ago it was mostly rifle shots and lightning claps, it's now the crash and splinter of the Outpost walls themselves.  Monsters are tearing down the walls, and the Outpost guards are overwhelmed.  If the monsters are not subdued, they will keep on battering the walls - and if the walls are breached, they'll come pouring in and overrun the Outpost.`n`nLooking around, you see a lot of terrified faces.  Some people are hammering new wood to the walls, and some are readying weapons.  All of them are scared - they know that if the walls are breached, it's honestly pretty likely that everyone will die.  Most businesses are closed - Sheila's Shack and eBoy's Trading Station are naturally staying open until the monsters flood the Outpost because hell, what else are they to do?`n`nAs people panic and scream in the streets below, a `\$familiar voice`0 issues from a nearby loudspeaker.`n`n\"`&Bloody hell, you lot...  Do I have to do `ieverything`i myself?  Right, I'm on my way.`0\"`n`nImprobable Central prepares for death or glory.`n`n");
									blocknav("runmodule.php?module=counciloffices&councilop=enter");
									blocknav("runmodule.php?module=gauntlet");
									blocknav("runmodule.php?module=haberdasher");
									blocknav("runmodule.php?module=iitems_invshop&op=start");
									blocknav("runmodule.php?module=jeweler");
									blocknav("runmodule.php?module=petra");
									blocknav("runmodule.php?module=oldchurch");
									blocknav("runmodule.php?module=villhut");
									blocknav("bank.php");
									blocknav("gypsy.php");
									blocknav("inn.php");
									blocknav("stables.php");
								break;
							}
						break;
					}
					output("Outpost wall hitpoints: %s`n`n",number_format($def));
					if (!$session['user']['dragonkills'] && $lv > 50){
						output("`JRookie tip: Help defend the Outpost by fighting monsters in the surrounding Jungle or reinforcing the walls.  If the defenses are overwhelmed and there are not enough wall hitpoints to hold back the monsters, the Outpost will be breached - which is bad.`0`n`n");
						if ($session['user']['level']<10){
							output("`JAs a Rookie under level ten, you won't be forced to fight if the Outpost walls come down.  You can opt-out of this rookie protection via your Preferences.`0`n`n");
						}
					}
				}
			}
			break;
		case "battle-victory":
			//debug($args);
			require_once "modules/cityprefs/lib.php";
			$cid = get_cityprefs_cityid("location",$session['user']['location']);
			if ($cid){
				$creatures = get_module_objpref("city",$cid,"creatures");
				$creatures--;
				//debug($creatures);
				if ($creatures>=0){
					set_module_objpref("city",$cid,"creatures",$creatures);
				}
				if ($creatures < 100){
					$args['creaturegold'] = round($args['creaturegold']*0.75);
			//		debug("lower req");
				} else if ($creatures > 700 && $creatures < 1000){
					$args['creaturegold'] = round($args['creaturegold']*1.5);
			//		debug("higher req");
				} else if ($creatures > 1000){
					$args['creaturegold'] = round($args['creaturegold']*2);
			//		debug("double req");
				} else {
			//		debug("normal req");
				}
			}
			break;
	}
	return $args;
}

function onslaught_run(){
	global $session,$battle,$enemies;
	global $companions,$companion,$newcompanions;
	switch (httpget('op')){
		case "lookaround":
			page_header("What's the situation?");

			$rawnum = onslaught_nummonsters();
			require_once "lib/dialogue.php";
			$vague = vagueify($rawnum);
			output("Taking a moment during a lull in the fighting to look around, you'd say as a rough estimate that there are between %s and %s monsters rampaging through the Outpost.`n`n",$vague['low'],$vague['high']);
			$playercount=0;
			output("Looking around you, you can see the following players engaged in battle alongside you:`n");
			$sql="SELECT name,acctid,level FROM " . db_prefix("accounts") . " WHERE locked=0 AND loggedin=1 AND alive=1 AND location='".$session['user']['location']."' AND laston>'".date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds"))."' ORDER BY level DESC";
			$result = db_query($sql);
			while ($row = db_fetch_assoc($result)) {
				$playercount++;
				output("`0%s`0`n",$row['name']);
			}
			if (!$playercount){
				output("`bNobody!`b  You're fighting all on your lonesome!`n`n");
			} else {
				output_notl("`n");
			}
			$def = onslaught_checkwalls();
			output("You take a quick glance at the walls, and see they've got %s hitpoints, whatever the hell a \"hitpoint\" is.`n`n",$def);
			$breakchance = e_rand(0,100);
			if ($breakchance>50){
				addnav("What will you do?");
				addnav("Reinforce the defences","runmodule.php?module=onslaught&op=reinforce");
				addnav("Get back into the fight","runmodule.php?module=onslaught&op=start&nodesc=1");
				addnav("Run outside and let the Outpost fend for itself","runmodule.php?module=onslaught&op=runmap");
			} else {
				addnav("And here comes another monster!");
				addnav("Fight!","runmodule.php?module=onslaught&op=continue&nodesc=1");
			}
		break;
		case "reinforce":
			page_header("Outpost Walls");
			$lv = onslaught_checkmonsters();
			$num = onslaught_nummonsters();
			$def = onslaught_checkwalls();
			$maxwalls=get_module_setting("maxcityhealth");
			if ($def>=$maxwalls && $maxwalls!=-1){
				output("`0You take a look at the Outpost walls. You search for any weaknesses, but cannot find any. There is nothing to do here.");
				addnav("What will you do?");
				addnav("Return to the Outpost","village.php");
			} else {
				output("`0You take a look at the Outpost walls.  Fortunately there are some lengths of timber, a hammer, and some nails sat conveniently next to a portion that's looking a little worse for wear.`n`nThis Outpost's hitpoints: `b%s`b`n`n",number_format($def));
			
			$stopchance = e_rand(0,100);
			if ($lv>100 && $def<$num && $stopchance > 80){
				output("Before you have a chance to pick up the hammer, you hear the sound of approaching thunderous footsteps from behind you!  You whirl around, dropping the hammer and drawing your weapon, to see a slavering beast bearing down upon you!`n`n");
				addnav("Oh dear...");
				addnav("Fight!","runmodule.php?module=onslaught&op=start&nodesc=1");
			} else {
				addnav("What will you do?");
				require_once "modules/staminasystem/lib/lib.php";
				$cost = stamina_getdisplaycost("Reinforcement");
				if ($noreinforce!=true)
				addnav(array("Reinforce the defences (`Q%s%%`0)",$cost),"runmodule.php?module=onslaught&op=reinforceconfirm");
				addnav("Return to the Outpost","village.php");
			}
			}
		break;
		case "reinforceconfirm":
			$iterations = httpget('iterations');
			if (!$iterations){
				$iterations=1;
			}
			page_header("Outpost Walls");
			$lv = onslaught_checkmonsters();
			$num = onslaught_nummonsters();
			output("`0You pick up the hammer and nails, and set about reinforcing the walls with new wood.`n`n");
			require_once "modules/staminasystem/lib/lib.php";
			for ($i=0; $i<$iterations; $i++){
				$def = onslaught_checkwalls();
				$stopchance = e_rand(0,100);
				if ($lv>100 && $def<$num && $stopchance > 80){
					output("Before you have a chance to pound a single nail, you hear the sound of approaching thunderous footsteps from behind you!  You whirl around, dropping the hammer and drawing your weapon, to see a slavering beast bearing down upon you!`n`n");
					addnav("Oh dear...");
					addnav("Fight!","runmodule.php?module=onslaught&op=start&nodesc=1");
					$nomore=1;
					break;
				} else {
					$act = process_action("Reinforcement");
					$actinfo = get_player_action("Reinforcement");
					$actlvl = $actinfo['lvl'];
					if ($act['lvlinfo']['levelledup']==true){
						output("`n`c`b`0You gained a level in Reinforcement!  You are now level %s!  This action will cost fewer Stamina points now, so reinforcing Outposts will tire you out a little less.`b`c`n",$act['lvlinfo']['newlvl']);
					}
					$stamina = get_stamina();
					$failchance = e_rand(1,100);
					if ($failchance > $stamina){
						output("`4`c`bDisaster!`b`c`0`n");
						$red = get_stamina(0);
						$death = e_rand(0,100);
						if ($death > $red){
							output("`\$Your exhaustion makes itself known - the hammer rebounds rather spectacularly and hits you square in the forehead, knocking you out cold.  The head injury causes you to lose 10% of your experience, and all your Requisition is stolen by opportunistic Midgets.`n`n`0");
							$session['user']['hitpoints']=0;
							$session['user']['experience']=round($session['user']['experience']*0.9);
							$session['user']['gold']=0;
							addnav("It's FailBoat time!");
							addnav("Well, damn.","shades.php");
							$nomore=1;
							break;
						} else {
							output("You're so exhausted that you make a right pig's ear of the job!`n`nThe fresh plank hangs for a second on one lonely nail before falling off.  What a waste of time!`n`n");
						}
					} else {
						$definc = e_rand($actlvl*0.8,$actlvl*1.2);
						if ($definc < 2) $definc = 2;
						$newdef = $def+$definc;
						$maxwalls=get_module_setting("maxcityhealth");
						if ($newdef>=$maxwalls && $maxwalls!=-1){
						$newdef=$maxwalls;
						$definc=$maxwalls-$def;
						}
						output("You hammer the board to the walls, reinforcing them quite nicely and adding %s hitpoints to this Outpost's defences.`n`nThis Outpost's hitpoints: `b%s`b`n`n",$definc,number_format($newdef));
						require_once "modules/cityprefs/lib.php";
						$cid = get_cityprefs_cityid("location",$session['user']['location']);
						if ($cid){
							set_module_objpref("city",$cid,"defences",$newdef);
						}
					}
				}
			}
			if (!$nomore){
				$cost = stamina_getdisplaycost("Reinforcement");
				addnav(array("Wall reinforcement: (`Q%s%%`0)",$cost));
				addnav("Reinforce once","runmodule.php?module=onslaught&op=reinforceconfirm");
				addnav("5?Reinforce x 5","runmodule.php?module=onslaught&op=reinforceconfirm&iterations=5");
				addnav("1?Reinforce x 10","runmodule.php?module=onslaught&op=reinforceconfirm&iterations=10");
				addnav("O?Return to the Outpost","village.php");
			}
		break;
		case "runsuccess":
			page_header("Run Like Hell!");
			output("You manage to flee your opponent.  You stand close to the Outpost gates, watching the carnage inside.  For the moment, everybody and everything is leaving you alone.  That will not last for long.`n`nNearby, someone has abandoned their timber, hammer and nails.  Abandoned or been dragged from, anyway.`n`n");
			addnav("What will you do?");
			addnav("Reinforce the defences","runmodule.php?module=onslaught&op=reinforce");
			addnav("Get back into the fight","runmodule.php?module=onslaught&op=start&nodesc=1");
			addnav("Run outside and let the Outpost fend for itself","runmodule.php?module=onslaught&op=runmap");
			addnav("Take stock of the situation","runmodule.php?module=onslaught&op=lookaround");
		break;
		case "runmap":
			redirect("runmodule.php?module=worldmapen&op=beginjourney","Onslaught - Running Away");
		break;
		case "start":
			page_header("Breach!");
			output("`b`i`4THE WALLS OF %s ARE BREACHED!`0`i`b`nMonsters are pouring into the Outpost through jagged, splintered holes in the Outpost defences!  You picked one `ihell`i of a time to go about your business here!`n`n",strtoupper($session['user']['location']));
			onslaught_getenemy();
		break;
		case "run":
			page_header("Breach!");
			if (e_rand(1, 5) < 3) {
				// They managed to get away.
				$battle = false;
				redirect("runmodule.php?module=onslaught&op=runsuccess","Onslaught: successful running");
			} else {
				output("You try to run, but your enemy gives chase!`n");
				$op = "fight";
				httpset('op', $op);
				$battle = true;
			}
		break;
		case "fight":
			page_header("Breach!");
			$battle = true;
		break;
		case "continue":
			page_header("Breach!");
			$out1 = onslaught_getcompanion();
			$out2 = onslaught_companion_escape();
			if ($out1){
				output("`0%s`n",$out1);
			} else if ($out2){
				output("`0%s`n",$out2);
			}
			onslaught_getenemy();
			if (!$battle){
				$left = onslaught_checkmonsters();
				if ($left>100){
					$breakchance = e_rand(0,100);
						if ($breakchance > 70){
							addnav("Phew!");
							addnav("Looks like everything else is busy...","");
							addnav("Reinforce the defences","runmodule.php?module=onslaught&op=reinforce");
							addnav("Get back into the fight","runmodule.php?module=onslaught&op=start&nodesc=1");
							addnav("Run outside and let the Outpost fend for itself","runmodule.php?module=onslaught&op=runmap");
							addnav("Take stock of the situation","runmodule.php?module=onslaught&op=lookaround");
						} else {
							addnav("They just keep coming!");
							addnav("Here comes another one!","runmodule.php?module=onslaught&op=continue&nodesc=1");
						}
				} else {
					output("There are no more monsters to fight...`n`n");
					//Display cleanup text, tart this up a bit
					addnav("Village","village.php");
				}
			}
		break;
	}
	if ($battle){
		include_once("battle.php");
		// if( isset( $enemies ) && !$pvp )
			// $badguy = &$enemies;
		if ($victory){
			$experience=e_rand($badguy['creatureexp']*1.25, $badguy['creatureexp']*2);
			$experience=round($experience);
			output("`3You receive `6%s `3experience!`n",$experience);
			$session['user']['experience']+=$experience;
			$left = onslaught_checkmonsters();
			if ($left>100){
				$breakchance = e_rand(0,100);
					if ($breakchance > 70){
						addnav("Phew!");
						addnav("Looks like everything else is busy...","");
						addnav("Reinforce the defences","runmodule.php?module=onslaught&op=reinforce");
						addnav("Get back into the fight","runmodule.php?module=onslaught&op=start&nodesc=1");
						addnav("Run outside and let the Outpost fend for itself","runmodule.php?module=onslaught&op=runmap");

						addnav("Take stock of the situation","runmodule.php?module=onslaught&op=lookaround");//double check if this ones included

					} else {
						addnav("They just keep coming!");
						addnav("Here comes another one!","runmodule.php?module=onslaught&op=continue&nodesc=1");
					}
			} else {
				output("You lower your weapon, blood splattered over your clothing.  It looks like you're winning - there are no more monsters to fight just now.`n`n");
				//Display cleanup text, tart this up a bit
				addnav("Village","village.php");
			}
		} elseif ($defeat){
			require_once("lib/forestoutcomes.php");
			forestdefeat(array($badguy),"in an Outpost");
		} else {
			require_once("lib/fightnav.php");
			fightnav(true,true,"runmodule.php?module=onslaught&nodesc=1");
		}
	}
	page_footer();
}

function onslaught_spawn(){
	//Spawns creatures and damages Outpost walls
	//We want to spawn creatures smoothly throughout the day, not all in one big chunk at newday.  So, we'll do this by time.
	$l = get_module_setting("lastspawn");
	$ev = get_module_setting("spawnevery");
	$spawnat = $l+$ev;
	$now = time();
	$left = $spawnat - $now;
	
	//debug("Next spawn: ".$left." seconds");

	//We want the creature spawns to be tied in to player activity, so we'll ignore times when monsters should have spawned but didn't because the function wasn't called during that time.
	if ($now > $spawnat){
		set_module_setting("lastspawn",$now);
		//spawn monsters
		$totalspawn = 0;
		$spawnrate = get_module_setting("spawnrate");
		$difficulty = get_module_setting("difficulty");
		
		//get number of players
		$sql = "SELECT count(acctid) AS c FROM " . db_prefix("accounts") . " WHERE locked=0 AND dragonkills > 1";
		$result = db_query_cached($sql,"playerswithdks",1800);
		$row = db_fetch_assoc($result);
		$numplayers = $row['c'];
		
		//for all cities
		$sql = "select * from ".db_prefix("cityprefs");
		$result=db_query_cached($sql,"allcityprefs");
		for ($i = 0; $i < db_num_rows($result); $i++){
			$row = db_fetch_assoc($result);
			$cid = $row['cityid'];
			$creatures = get_module_objpref("city",$cid,"creatures");
			$outpostrate = get_module_objpref("city",$cid,"spawnrate");
			
			$newcreatures = ceil((($outpostrate/100)*($spawnrate/100)*($numplayers/$difficulty)));

			//debug("Adding ".$newcreatures." creatures to ".$row['cityname']);

			$creatures += $newcreatures;
			set_module_objpref("city",$cid,"creatures",round($creatures));
			//Now damage the walls!  If checkbreach returns more than 80, the monsters will start tearing down the walls.
			if (onslaught_checkmonsters($cid)>=80){
				$olddef = get_module_objpref("city",$cid,"defences");
				$newdef = $olddef - ($creatures * get_module_setting("damagemultiplier","onslaught"));
				if ($newdef<0) $newdef = 0;
				set_module_objpref("city",$cid,"defences",round($newdef));
			}
		}
	}
}

function onslaught_shuffleoutposts(){
	//Randomly determines new weighting patterns for each Outpost
	$sql = "select * from ".db_prefix("cityprefs");
	$result=db_query_cached($sql,"allcityprefs");
	for ($i = 0; $i < db_num_rows($result); $i++){
		$row = db_fetch_assoc($result);
		$cid = $row['cityid'];
		set_module_objpref("city",$cid,"spawnrate",e_rand(1,200));
	}
}

function onslaught_checkmonsters($cid="none"){
	global $session;
	require_once "modules/cityprefs/lib.php";
	if ($cid=="none"){
		$cid = get_cityprefs_cityid("location",$session['user']['location']);
	}
	$creatures = get_module_objpref("city",$cid,"creatures");
	$breachpoint = get_module_objpref("city",$cid,"breachpoint");
	if ($creatures==0){
		return 1;
	} else {
		return ($creatures/$breachpoint)*100;
	}
}

function onslaught_nummonsters($cid="none"){
	global $session;
	require_once "modules/cityprefs/lib.php";
	if ($cid=="none"){
		$cid = get_cityprefs_cityid("location",$session['user']['location']);
	}
	$creatures = get_module_objpref("city",$cid,"creatures");
	return $creatures;
}

function onslaught_checkwalls($cid="none"){
	global $session;
	if ($cid=="none"){
		require_once "modules/cityprefs/lib.php";
		$cid = get_cityprefs_cityid("location",$session['user']['location']);
	}
	$def = get_module_objpref("city",$cid,"defences");
	$def = round($def);
	return $def;
}

function onslaught_getenemy(){
	global $session,$badguy,$battle;
	$sql = "SELECT * FROM " . db_prefix("creatures") . " WHERE forest = 1 ORDER BY rand(".e_rand().") LIMIT 1";
	$result = db_query($sql);
	restore_buff_fields();
	if (db_num_rows($result) == 0) {
		// There is nothing in the database to challenge you,
		// let's give you a doppleganger.
		$badguy = array();
		$badguy['creaturename']="An evil doppleganger of ".$session['user']['name'];
		$badguy['creatureweapon']=$session['user']['weapon'];
		$badguy['creaturelevel']=$session['user']['level'];
		$badguy['creaturegold']= rand(($session['user']['level'] * 15),($session['user']['level'] * 30));
		$badguy['creatureexp'] = round($session['user']['experience']/10, 0);
		$badguy['creaturehealth']=$session['user']['maxhitpoints'];
		$badguy['creatureattack']=$session['user']['attack'];
		$badguy['creaturedefense']=$session['user']['defense'];
	} else {
		$badguy = db_fetch_assoc($result);
		require_once("lib/forestoutcomes.php");
		$badguy = buffbadguy($badguy);
	}
	calculate_buff_fields();
	$badguy['playerstarthp']=$session['user']['hitpoints'];
	$badguy['diddamage']=0;
	$badguy['type'] = 'onslaught';
	$session['user']['badguy']=createstring($badguy);
	$battle = true;
	output("Before you have time to even `iblink,`i let alone call for backup or medical assistance, something rushes up from behind...`n`nYou have encountered %s!`n`n",$badguy['creaturename']);
}

function onslaught_hascompanion(){
	//checks to see if the player already has a companion from the range of special companions in this module
	global $session;
	global $companions;
	if (isset($companions['stern']) || isset($companions['watcher']) || isset($companions['maiko']) || isset($companions['punishment']) || isset($companions['mike']) || isset($companions['dan']) || isset($companions['cake']) || isset($companions['cuthbert']) || isset($companions['skronky'])){
		return true;
	} else {
		return false;
	}
}

function onslaught_hadcompanion($companion){
	//checks to see if the player has had a companion from this module already during this Onslaught
	global $session;
	$info = @unserialize(get_module_pref("info"));
	$cs = $info['companions_this_onslaught'];
	if (isset($info['companions_this_onslaught'][$companion])){
		return true;
	} else {
		return false;
	}
}

function onslaught_getcompanion(){
	global $session;
	$chance = e_rand(0,100);
	//$chance = 100; //debug
	switch ($session['user']['location']){
		case "NewHome":
			$nhchance = e_rand(0,100);
			if ($chance>80 && $nhchance>50 && !onslaught_hascompanion() && !onslaught_hadcompanion("stern")){
			// Mister Stern
				apply_companion('stern', array(
					"name"=>"Mister Stern",
					"hitpoints"=>round($session['user']['hitpoints']*0.4),
					"maxhitpoints"=>round($session['user']['maxhitpoints']*0.4),
					"attack"=>round($session['user']['attack']*0.6),
					"defense"=>round($session['user']['defence']*0.6),
					"dyingtext"=>"`0Mister Stern catches a hard blow to his stomach and doubles over, fighting for breath.`n`n\"`#Are you okay?`0\" you call to him, knowing the answer before you even ask.  Before Mister Stern can reply, however, Corporal Punishment has appeared from seemingly out of nowhere and picked up the slender man in a fireman's lift.`n`n\"`@DON'T YOU WORRY!`0\" he shouts.  \"`@YOU keep on with the MONSTERS, let ME worry about HAVELOCK here!`0\"  With that, he disappears into the fray with Stern draped across his shoulder, his pistols clearing a nice path for him towards the hospital tent.`n",
					"abilities"=>array(
						"fight"=>true,
					),
					"ignorelimit"=>true, // Does not count towards companion limit...
				), true);
				$info = @unserialize(get_module_pref("info"));
				$info['companion_escapetext']="`0Mister Stern pats his left pocket.  \"`6Oh, damn and blast!`0\"`n`n\"`#What is it?`0\" you shout over the roar of battle.`n`nMister Stern frowns.  \"`6I think I'm out of bullets.`0\"`n`n\"`#No problem,`0\" you shout back.  \"`#Pull back for now - if you find any more, you know where to find me!`0\"`n`n\"`6Right-ho,`0\" says Mister Stern, and turns to limp back into the Museum.`n";
				$info['companions_this_onslaught']['stern']=1;
				set_module_pref("info",serialize($info));
				return "You turn from the body of the beast to which you just laid waste, only to see Mister Stern drawing a familiar-looking rusty service revolver!  He levels it at a point between your knees and fires, putting a bullet right between the eyes of the creature reaching up to swipe at your back - the creature that up to a second ago you were quite sure was dead.  It convulsces once and lies still, crosseyed.  You look back towards Mister Stern.`n`n\"`#I thought that thing was too old and rusty to even `iwork`i!`0\"`n`n\"`6So did I,`0\" says Mister Stern, looking down in amazement at the revolver.  He looks up towards you again, and his eyes focus on something just behind and to your right.`n`n\"`6Look out!`0\"`n`n";
			} else if ($chance>80 && $nhchance<50 && !onslaught_hascompanion() && !onslaught_hadcompanion("punishment")){
			//Corporal Punishment
				apply_companion('punishment', array(
					"name"=>"Corporal Punishment",
					"hitpoints"=>round($session['user']['hitpoints']*2),
					"maxhitpoints"=>round($session['user']['maxhitpoints']*2),
					"attack"=>round($session['user']['attack']*2),
					"defense"=>round($session['user']['defence']*2),
					"dyingtext"=>"`0Corporal Punishment falls to his knees, blood staining his uniform.  \"`2DAMN IT!  I'm out of HITPOINTS!`0\"  He drags himself to his feet.  \"`2LUCKILY, if I were to DIE then that would DISRUPT the GAME CONTINUITY!  `iAND WE CAN'T HAVE THAT NOW CAN WE!`i`0\"  He turns and flees, leaving you alone with the creature.  You shrug at each other before resuming your respective combat stances.`n",
					"abilities"=>array(
						"fight"=>true,
					),
					"ignorelimit"=>true, // Does not count towards companion limit...
				), true);
				$info = @unserialize(get_module_pref("info"));
				$info['companion_escapetext']="`0Corporal Punishment laughs a braying, screaming laugh as he empties his revolvers into the carcass, blasting away chunks of monster so large that after a few seconds little more than a lumpy puddle remains.  Then he spins around and runs off.`n`n\"`#Where the bloody hell are you going?!\"`n`n\"`2FUNCTION ONSLAUGHT_COMPANION_ESCAPE WAS CALLED!`0\" he shouts back.  \"`2GAME BALANCE, SORRY!`0\"`n`n\"`#`iWEIRDO!`i`0\" you shout to his retreating form as he disappears into the melee.`n";
				$info['companions_this_onslaught']['punishment']=1;
				set_module_pref("info",serialize($info));
				return "\"`2MARVELLOUS!`0\"`n`nYou spin around.  Corporal Punishment stands before you.  \"`2EXCELLENT job, young RECRUIT!  I believe I will ASSIST you for a short PERIOD, in order to help ALLEVIATE the problems with GAME BALANCE involved in making you fight multiple MONSTERS in `iSEQUENCE`i without the benefit of HEALING between FIGHTS!`0\"  His moustache jiggles obscenely.`n`n\"`#What in the everloving `ihell`i are you talking about?`0\"`n`n\"`i`2THIS!`i`0\" laughs Corporal Punishment, pulling a pair of hilariously oversized revolvers from somewhere inside his jacket.`n`n";
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

function onslaught_stripcompanions(){
	global $session,$companions;
	unset ($companions['stern']);
	unset ($companions['watcher']);
	unset ($companions['maiko']);
	unset ($companions['punishment']);
	unset ($companions['mike']);
	unset ($companions['dan']);
	unset ($companions['cake']);
	unset ($companions['cuthbert']);
	unset ($companions['skronky']);
	$session['user']['companions'] = createstring($companions);
	return true;
}

function onslaught_companion_escape(){
	global $session;
	if (onslaught_hascompanion()){
		$info = @unserialize(get_module_pref("info"));
		$escape = (e_rand(0,80) + ($info['companions_rounds']*10));
		if ($escape>100){
			onslaught_stripcompanions();
			$r = $info['companion_escapetext'];
			unset($info['companion_escapetext']);
			unset($info['companions_rounds']);
			set_module_pref("info",serialize($info));
			return ($r);
		} else {
			$info['companions_rounds']+=1;
			set_module_pref("info",serialize($info));
			return false;
		}
	} else {
		return false;
	}
}

?>
