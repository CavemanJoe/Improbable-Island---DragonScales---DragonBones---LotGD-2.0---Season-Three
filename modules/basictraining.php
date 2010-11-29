<?php

function basictraining_getmoduleinfo(){
	$info = array(
		"name"=>"Basic Training",
		"author"=>"Dan Hall",
		"version"=>"2009-02-21",
		"category"=>"Improbable",
		"download"=>"",
		"prefs"=>array(
		//	"visited"=>"Player has visited Basic Training,bool|0",
			"shoutingmatch"=>"The length of the player's shouting match with Corporal Punishment,int|0",
		),
	);
	
	return $info;
}

function basictraining_install(){
	$condition = "if (\$session['user']['location'] == \"NewHome\") {return true;} else {return false;};";
	module_addhook("village",false,$condition);
	module_addhook("newday");
	return true;
}

function basictraining_uninstall(){
	return true;
}

function basictraining_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "village":
			tlschema($args['schemas']['fightnav']);
			addnav($args['fightnav']);
			tlschema();
			addnav("Basic Training","runmodule.php?module=basictraining&op=start");
			break;
		case "newday":
			if (!get_module_pref("visited")){
				blocknav("village.php");
				blocknav("news.php");
				addnav("Get on with it","runmodule.php?module=basictraining&op=start");
			}
		}
	return $args;
}

function basictraining_run(){
	global $session;
	$op = httpget("op");
	page_header("Basic Training");
	switch ($op){
		case "start":
			if (get_module_pref("visited") == 0){
				set_module_pref("visited", 1);
				output("`0A uniformed, medallion-festooned, barrel-chested brute of a man greets you at the Basic Training compound just inside the Outpost gates.  His moustache is broad enough to have its own postcode.`n`n\"`2RAAAAAIIIIGHT THEN!`0\" he bellows, his cheeks red and his eyes bulging with barely-suppressed bloodlust.  \"`2My name is Corporal `iPUNISHMENT!`i  You may refer to me as \"Corporal,\" \"Sir,\" or \"Your 'ighness.\"  My job is to educate freshly-baked recruits like you, and prepare them for their moments of `iGLORY!`i\"`n`n`0You take note of the Corporal's bulging forehead veins, and seriously consider fleeing into the Jungle.`n`n\"`2NAOHW!  The `ifirst`i exercise we're going to perform today is a `irole-playing exercise`i!  I want you to imagine that you're not really here, but instead, you're sat in a comfy chair somewhere about seventy years ago with a computer or a mobile phone, playin' some sort of \"browser-based text adventure game\" and just `ireadin'`i about your experiences 'ere today!  And `ithen`i I want you to reach out towards your keyboard, and press the 'F' key!  That's 'F' for Foxtrot!  An' if you have JavaScript disabled, it's likely that nothing will happen, and you can simply click on the link and ignore anything I say about the keyboard!`0\"`n`nYou gawp at the man, now convinced that he's living in some sort of fantasy world.");
				addnav("Oh dear.");
				addnav("F?Fear for your life","runmodule.php?module=basictraining&op=continue");
			} else {
				output("`0\"`2NOW!  What ELSE can I 'elp you with today, young recruit?`0\"");
				addnav("Questions");
				addnav("Tell me how I go about destroying this Improbability Drive.","runmodule.php?module=basictraining&op=improbabilitydrive-destroy");
				addnav("Tell me about this Head-Up Display you mentioned.","runmodule.php?module=basictraining&op=hud");
				addnav("Tell me about fighting monsters.","runmodule.php?module=basictraining&op=combat");
				addnav("Tell me about backpacks and bandoliers.","runmodule.php?module=basictraining&op=backpackbandolier");
				addnav("Tell me about travelling to different Outposts.","runmodule.php?module=basictraining&op=travel");
				addnav("Tell me why you shout so much.","runmodule.php?module=basictraining&op=shout");
				addnav("Leave Basic Training");
				addnav("Exit to NewHome","village.php");
				}
			break;
		case "continue":
			output("The Corporal beams at you.  \"`2`iEXCELLENT!`i  We might just make a soldier of you yet!`0\"`n`nYou shake your head, still not sure what's going on.  It's not like you said or did anything other than worry about your safety.  He just kind of paused for a second, then carried on.`n`n\"`2AS you can see, it is possible to use the KEYBOARD to move around instead of the MOUSE, should you so prefer it!  Simply press the key that is a-`iAHAIGHLIGHTED!`i  HERE is a BACKPACK and BANDOLIER in which to keep your EQUIPMENT.`0\"  He hands you a shoddy-looking backpack, and an even shoddier-looking bandolier.  You shrug, and put them both on.  \"`2Now, let's talk about your head-up Statistics display.  This can be found to the RIGHT of your SCREEN!`0\"`n`nYou look around.  You don't see anything of the sort.  Nor do you see any type of screen.  But you decide to go along with it, and nod and smile at the appropriate points.`n`n\"`2The two most IMPORTANT pieces of BASIC INFORMATION that you NEED to KNOW in order to `iSURVIVE`i,\"`0 bellows the Corporal, spittle flying from his lips, \"`2are your HITPOINTS and your STAMINA!  When your HITPOINTS are gone, you'll be taking a trip to see...`0\" He looks left and right, then leans forward conspiratorially.  \"`2You know.  `\$Her.`0\"`n`nYou wonder whether he's scared of `\$The Watcher`0, and also how the hell he did that `\$thing`0 with his voice.`n`n\"`2MOST of the things that you can do on the Island will require some amount of STAMINA!  When your Stamina is low, you run the risk of making MISTAKES or even LOSING CONSCIOUSNESS because you are becoming EXHAUSTED!  STAMINA is restored to MAXIMUM once every new GAME DAY.  NOW, do you have any QUESTIONS, or shall I move on?`0\"`n`nYou think for a moment, unsure of whether to ask.  Eventually you decide to go for it.  \"`#Yes.  Why do you measure everything you see in terms of numbers and statistics in some sort of computer game?`0\"`n`nThe Corporal smiles.  \"`2For the SAME reason you have your FISTS strapped to your BACK, sunshine.`0\"`n`nWell, that cleared that up.`n`n\"`2If you take NOTHING else from my INSTRUCTION, just remember these FOUR SIMPLE THINGS.`n`n\"ONE: Keep an eye on your STAMINA, and try not to let it go too far into the ORANGE.  You can REPLENISH it by waiting for the next new GAME DAY, or by EATING, DRINKING or SMOKING.`n`n\"TWO: Keep an eye on your HITPOINTS, and go to the HOSPITAL TENT to recover them.  All healing is FREE at Level One.`n`n\"THREE: Don't walk around with lots of REQUISITION in your pockets, because if you get KNOCKED OUT, all your MONEY will be STOLEN by MIDGETS.  Put it in the BANK, where it will be MARGINALLY SAFER.`n`n\"FOUR: BEFORE you go GALLAVANTING into the JUNGLE like a LAMB to the `iSLAUGHTER`i, for GOD'S SAKE `iBUY SOMETHING WITH WHICH TO DEFEND YOURSELF!`i  SHEILA'S SHACK has a very NICE range of WEAPONRY and ARMOUR available for purchase, and that should be your FIRST stop when you head into the Outpost, followed by the COUNCIL OFFICES where you can obtain free GRENADES, RATION PACKS, or MEDKITS.`0\"  The Corporal wipes his now-sodden moustache on his sleeve.  \"`2I now pronounce you JUST ABOUT fit to SURVIVE for LONG enough to get CONFUSED, and you may LEAVE this compound if you wish.  UNLESS you have FURTHER questions, which I will answer `iNOW!`i`0\"  He shrugs.  \"`2Or, hell, I'll be here when you come back.  I don't get out much.`0\"");
			addnav("Questions");
			addnav("Tell me about this Island, and the Improbability Drive.","runmodule.php?module=basictraining&op=island");
			addnav("What is my objective here?","runmodule.php?module=basictraining&op=objective");
			addnav("Tell me more about this Head-Up Display of yours.","runmodule.php?module=basictraining&op=hud");
			addnav("What's this backpack and bandolier for?","runmodule.php?module=basictraining&op=backpackbandolier");
			addnav("Leave Basic Training");
			addnav("Exit to NewHome","village.php");
			break;
		case "island":
			output("\"`2MANY years ago, a man called Professor HAWTON tried to create an OVERUNITY DEVICE!  That is to say, a type of GENERATOR that OUTPUTS more power than it CONSUMES, in blatant disregard for the second law of THERMODYNAMICS and the law of CONSERVATION of `iENERGY!`i  He was SENT here obstensibly to work on his machine in an environment without ELECTROMAGNETIC INTEFERENCE, but the TRUTH of the matter is that he was sent HERE because NOBODY at his university could STOMACH the man!`0\"  You wonder if that might be because Hawton shouted as much as Corporal Punishment.  \"`2LONG story SHORT, he must have been at least PARTIALLY successful - because with the help of a similarly banished assistant, he created a machine that generates a type of energy that the human race has NEVER before EXPERIENCED!  THAT energy is what created this place, along with the unusual FLORA and FAUNA that INHABIT it!  For a more DETAILED account, and information about the various POPULACES and HISTORY of the Island, you'd do well to visit the MUSEUM just down the road.`0\"");
			addnav("Questions");
			addnav("Is \"unusual fauna\" a euphemism for \"horrible, drooling monster\", and if so, what do I do about those?","runmodule.php?module=basictraining&op=combat");
			addnav("Remind me what I'm even doing in this awful place?","runmodule.php?module=basictraining&op=objective");
			addnav("Leave Basic Training");
			addnav("Exit to NewHome","village.php");
			break;
		case "objective":
			output("\"`2Your OBJECTIVE is, of course, to DESTROY or DISABLE the Improbability Drive!  HOWEVER, a LOT of people seem to be more interested in such NAMBY-BLOODY-PAMBY activities as travelling between Outposts to trade commodities, building ROBOTS out of BITS of SCRAP, perfecting their COOKING skills or just CHATTING amongst themselves!  It `iMAKES MY BLOOD BOIL!`i`0\"  He's not kidding.  You can see flecks of foam clinging to his moustache.  \"`2GOD, WHAT I'D DO FOR A DECENT WAR!  Trying to get that lot out there ORGANIZED is like trying to herd CATS!`0\"  He hyperventilates for a moment, then calms himself.  \"`2Well, I might as well give you the full run-down.  Where should I start?`0\"");
			addnav("Questions");
			addnav("Remind me why this Island is here to begin with, and just what this Improbability Drive actually is?","runmodule.php?module=basictraining&op=island");
			addnav("Tell me how I go about destroying this Improbability Drive.","runmodule.php?module=basictraining&op=improbabilitydrive-destroy");
			addnav("Tell me about this Head-Up Display you mentioned.","runmodule.php?module=basictraining&op=hud");
			addnav("What's this backpack and bandolier for?","runmodule.php?module=basictraining&op=backpackbandolier");
			addnav("Change the subject");
			addnav("Ask about something else","runmodule.php?module=basictraining&op=start");
			addnav("Bloody hell mate, you don't half shout a lot.","runmodule.php?module=basictraining&op=shout");
			addnav("Leave Basic Training");
			addnav("Exit to NewHome","village.php");
			break;
		case "improbabilitydrive-destroy":
			output("\"`2As far as we KNOW, destroying the Improbability Drive is like DESTROYING any other MONSTER.  The HARD part is FINDING the damned thing!  RUMOUR has it that it can ONLY be found by people who are a HIGH enough COMBAT LEVEL.`0\"");
			addnav("Questions");
			addnav("Combat Level?  Is that something from the Head-Up Display?","runmodule.php?module=basictraining&op=hud-level");
			addnav("Tell me about combat.","runmodule.php?module=basictraining&op=combat");
			addnav("Change the subject");
			addnav("Ask about something else","runmodule.php?module=basictraining&op=start");
			addnav("Leave Basic Training");
			addnav("Exit to NewHome","village.php");
			break;
		case "shout":
			$npcact = e_rand(1,8);
			$npcspk = e_rand(1,8);
			$pla = e_rand(1,8);
			switch ($npcact){
				case 1:
					output("The Corporal's eyes open wide at the accusation, and appear to bulge out of his head.  \"`2");
					break;
				case 2:
					output("The Corporal's face flushes to an even deeper red, reminding you of an overcooked lobster.  \"`2");
					break;
				case 3:
					output("The pulsating vein in the Corporal's forehead throbs ominously.  \"`2");
					break;
				case 4:
					output("The Corporal sputters, wipes the spittle from his moustache, and shouts.  \"`2");
					break;
				case 5:
					output("The Corporal's right eye bores into your soul, while his left begins to swivel slowly towards the sky, exposing some rather worrying ruptured blood vessels.  \"`2");
					break;
				case 6:
					output("The Corporal stands in silence for a moment.  His left eye twitches, twice.  Then, he lunges forward.  \"`2");
					break;
				case 7:
					output("The Corporal's eyebrows descend, narrowing his eyes to bloodshot slits.  He advances upon you, with a hiss of \"`2");
					break;
				case 8:
					output("The Corporal takes a deep breath, buttons popping comically off his uniform, and says \"`2");
					break;
			}
			switch ($npcspk){
				case 1:
					output("`iDO I BOLLOCKS!`i");
					break;
				case 2:
					output("`iTHAT`i is a load of `iUTTER DRIVEL!`i  I've never SHOUTED before in my LIFE!");
					break;
				case 3:
					output("`iI'VE NEVER HEARD SUCH NONSENSE IN ALL MY LIFE!`i");
					break;
				case 4:
					output("`iNO I BLOODY WELL DO `bNOT`b YOU ARROGANT TWERP!`i");
					break;
				case 5:
					output("`iTHAT`i is an `iUNFOUNDED`i and `iSLANDEROUS `bACCUSATION!`b`i");
					break;
				case 6:
					output("`iBULLSHIT!`i  UTTER, TOTAL `iBLOODY WANKING `bBULLSHIT`b!`i`0\"  He takes a deep breath, and twitches a couple of times.  \"`2ARSE!");
					break;
				case 7:
					output("I'm not shouting now, am I?  No.`0\" The Corporal stands quietly, breathing deeply.  \"`2No.  Now, I'm relaxed.  Everything is fine.  So long as nobody `i`bBLOODY ACCUSES ME OF SHOUTING WITHIN MY BLOODY EARSHOT!`b`i");
					break;
				case 8:
					output("`iI DON'T SHOUT!  `bTHIS`b ISN'T `bSHOUTING`b!  DO YOU WANT TO SEE `bSHOUTING`b?  `bDO`b YOU?  THEN JUST KEEP ON COMPLAINING ABOUT MY SHOUTING!`i");
					break;
			}
			output("`0\"");
			addnav("Oh really?");
			switch ($pla){
				case 1:
					addnav("You bloody do, though, don't you?","runmodule.php?module=basictraining&op=shout");
					break;
				case 2:
					addnav("Oh, you totally do.","runmodule.php?module=basictraining&op=shout");
					break;
				case 3:
					addnav("You're shouting `iright now,`i dude.","runmodule.php?module=basictraining&op=shout");
					break;
				case 4:
					addnav("Yes you do.","runmodule.php?module=basictraining&op=shout");
					break;
				case 5:
					addnav("Yes, yes I'm afraid you do.","runmodule.php?module=basictraining&op=shout");
					break;
				case 6:
					addnav("I have it on good authority that you shout at least three words out of every sentence.","runmodule.php?module=basictraining&op=shout");
					break;
				case 7:
					addnav("It is a matter of public record that you, sir, shout at every available opportunity.","runmodule.php?module=basictraining&op=shout");
					break;
				case 8:
					addnav("You're a bad liar, and you shout too.","runmodule.php?module=basictraining&op=shout");
					break;
			}
			addnav("Stop this childish nonsense.");
			addnav("Ask him a question, quick.","runmodule.php?module=basictraining&op=start");
			increment_module_pref("shoutingmatch");
			if (is_module_active("medals" && get_module_pref("shoutingmatch")>=10)){
				require_once "modules/medals.php";
				medals_award_medal("punishment_shoutingmatch","Sore Throat","This player engaged in a very long shouting match with Corporal Punishment!","medal_shoutingmatch.png");
			}
			break;
		case "backpackbandolier":
			output("The Corporal nods, and takes a deep breath.  \"`2Your BACKPACK and BANDOLIER are there for you to put THINGS in.  As a HUMAN contestant, you will receive a set of GRENADES once per day.`0\"`n`nYou frown, and look down.  \"`#Wouldn't it be better if I got, say... some pants?`0\"`n`n\"`2`iYOU'LL GET WHAT'S GIVEN AND BLEEDIN' WELL LIKE IT!`i  You can always sell the Grenades at eBoy's place, I suppose, if LUXURIES like PANTS are really THAT `iIMPORTANT`i to you, YOUR BLEEDIN' HIGHNESS.  `iAS I WAS SAYING`i.  You `iCAN'T`i go rummaging around in your Backpack in the MIDDLE of a FIGHT!  THAT'S what your BANDOLIER is for!  If you want to use a GRENADE or another item in a FIGHT, you had better open up your INVENTORY and TRANSFER the item to your BANDOLIER, so that it's EASILY GRABBABLE!`0\"`n`nThat seems kind of arbitrary and oversimplified, but it makes sense - at least to the sort of madman who sees head-up displays and keyboards wherever he looks.`n`n\"`2Your BACKPACK and BANDOLIER each have a WEIGHT limit.  You can EXCEED this limit, but if you do, EVERY ACTION that you take will cost you more STAMINA than usual.  You can INCREASE your limits by obtaining HIGHER-QUALITY equipment, and the Luggage Hut in Improbable Central can help you with THAT.`0\"");
			addnav("Questions");
			addnav("Ask about Travelling.","runmodule.php?module=basictraining&op=travel");
			addnav("Remind me why this Island is here to begin with, and just what this Improbability Drive actually is?","runmodule.php?module=basictraining&op=island");
			addnav("Tell me how I go about destroying this Improbability Drive.","runmodule.php?module=basictraining&op=improbabilitydrive-destroy");
			addnav("Tell me about this Head-Up Display you mentioned.","runmodule.php?module=basictraining&op=hud");
			addnav("Change the subject");
			addnav("Ask about something else","runmodule.php?module=basictraining&op=start");
			addnav("Leave Basic Training");
			addnav("Exit to NewHome","village.php");
			break;
		case "hud":
			output("\"`2The HEAD-UP DISPLAY on the right-hand side of your SCREEN contains various STATISTICS about your good self!  What would you like to know about?`0\"");
			addnav("Ask about Statistics");
			addnav("Level","runmodule.php?module=basictraining&op=hud-level");
			addnav("Hitpoints","runmodule.php?module=basictraining&op=hud-hitpoints");
			addnav("Attack","runmodule.php?module=basictraining&op=hud-attack");
			addnav("Defence","runmodule.php?module=basictraining&op=hud-defence");
			addnav("Stamina","runmodule.php?module=basictraining&op=stamina");
			addnav("Requisition","runmodule.php?module=basictraining&op=requisition");
			addnav("Cigarettes","runmodule.php?module=basictraining&op=cigarettes");
			addnav("Experience","runmodule.php?module=basictraining&op=experience");
			addnav("Next Day","runmodule.php?module=basictraining&op=newday");
			addnav("Weapons and Armour","runmodule.php?module=basictraining&op=weaponsandarmour");
			addnav("Buffs","runmodule.php?module=basictraining&op=buffs");
			addnav("Change the subject");
			addnav("Ask about something else","runmodule.php?module=basictraining&op=start");
			addnav("Leave Basic Training");
			addnav("Exit to NewHome","village.php");
			break;
		case "hud-level":
			output("\"`2The LEVEL shown in your HEAD-UP DISPLAY is a simple measure of your COMBAT SKILL!`0\" barks the Corporal.  \"`2Every time you defeat a VILE CREATURE in COMBAT, you will earn some EXPERIENCE points!  Once you have ENOUGH Experience points, you'll be able to challenge your MASTER at the DOJO in your home town, and ascend one LEVEL!  This will earn you TEN extra HITPOINTS and one extra point of both ATTACK and DEFENCE!  When you reach a HIGH enough COMBAT LEVEL, you will be able to take on the `iIMPROBABILITY DRIVE`i and make me a very PROUD Corporal `iindeed!`i`0\"");
			addnav("Ask about Statistics");
			addnav("Hitpoints","runmodule.php?module=basictraining&op=hud-hitpoints");
			addnav("Attack","runmodule.php?module=basictraining&op=hud-attack");
			addnav("Defence","runmodule.php?module=basictraining&op=hud-defence");
			addnav("Stamina","runmodule.php?module=basictraining&op=stamina");
			addnav("Requisition","runmodule.php?module=basictraining&op=requisition");
			addnav("Cigarettes","runmodule.php?module=basictraining&op=cigarettes");
			addnav("Experience","runmodule.php?module=basictraining&op=experience");
			addnav("Next Day","runmodule.php?module=basictraining&op=newday");
			addnav("Weapons and Armour","runmodule.php?module=basictraining&op=weaponsandarmour");
			addnav("Buffs","runmodule.php?module=basictraining&op=buffs");
			addnav("Tangents");
			addnav("Ask about Combat","runmodule.php?module=basictraining&op=combat");
			addnav("Ask about destroying the Improbability Drive","runmodule.php?module=basictraining&op=improbabilitydrive-destroy");
			addnav("Change the subject");
			addnav("Ask about something else","runmodule.php?module=basictraining&op=start");
			addnav("Leave Basic Training");
			addnav("Exit to NewHome","village.php");
			break;
		case "hud-hitpoints":
			output("\"`2The HITPOINTS shown to the right of your screen are a simple measure of how healthy, or how `iHORRIBLY MAIMED`i, you happen to be at any given time.  You will lose HITPOINTS whenever you are `iINJURED,`i either in COMBAT or DOING SOMETHING SILLY!  The easiest way to recouperate Hitpoints is to go to the HOSPITAL Tent in the Jungle.  At Level One, all services provided by the Hospital Tent are FREE of CHARGE.  From Level Two onwards, it's always cheaper to heal, say, ten hitpoints' worth of DAMAGE than it is to heal FIVE hitpoints of damage TWICE.  So it's best to let yourself get a bit BEATEN UP before asking for the services of your local mmm-mmmm...`0\"  Tendons stand out on the Corporal's neck, and the vein in his scarlet forehead throbs disconcertingly.  \"`2MMM`iMEDIC!`i`0\"`n`nHe takes a breath and carries on.  \"`2If your hitpoints drop to ZERO, you will lose CONSCIOUSNESS and find yourself stuck on the FAILBOAT until such time as you can ESCAPE!`0\"");
			addnav("Ask about Statistics");
			addnav("Level","runmodule.php?module=basictraining&op=hud-level");
			addnav("Attack","runmodule.php?module=basictraining&op=hud-attack");
			addnav("Defence","runmodule.php?module=basictraining&op=hud-defence");
			addnav("Stamina","runmodule.php?module=basictraining&op=stamina");
			addnav("Requisition","runmodule.php?module=basictraining&op=requisition");
			addnav("Cigarettes","runmodule.php?module=basictraining&op=cigarettes");
			addnav("Experience","runmodule.php?module=basictraining&op=experience");
			addnav("Next Day","runmodule.php?module=basictraining&op=newday");
			addnav("Weapons and Armour","runmodule.php?module=basictraining&op=weaponsandarmour");
			addnav("Buffs","runmodule.php?module=basictraining&op=buffs");
			addnav("Tangents");
			addnav("Ask about Combat","runmodule.php?module=basictraining&op=combat");
			addnav("Ask about the FailBoat","runmodule.php?module=basictraining&op=failboat");
			addnav("Change the subject");
			addnav("Ask about something else","runmodule.php?module=basictraining&op=start");
			addnav("Leave Basic Training");
			addnav("Exit to NewHome","village.php");
			break;
		case "hud-attack":
			output("The Corporal grimaces.  \"`2I would have thought that would have been BLEEDIN' OBVIOUS, don't you?  Your ATTACK rating is a simple measure of how EFFECTIVE you are in COMBAT!  A high LEVEL and good WEAPONS will `iINCREASE`i this number.  The HIGHER the number, the HIGHER your chance of landing a successful blow, and doing a lot of damage!  BUFFS can also affect your Attack rating.\"");
			addnav("Ask about Statistics");
			addnav("Level","runmodule.php?module=basictraining&op=hud-level");
			addnav("Hitpoints","runmodule.php?module=basictraining&op=hud-hitpoints");
			addnav("Defence","runmodule.php?module=basictraining&op=hud-defence");
			addnav("Stamina","runmodule.php?module=basictraining&op=stamina");
			addnav("Requisition","runmodule.php?module=basictraining&op=requisition");
			addnav("Cigarettes","runmodule.php?module=basictraining&op=cigarettes");
			addnav("Experience","runmodule.php?module=basictraining&op=experience");
			addnav("Next Day","runmodule.php?module=basictraining&op=newday");
			addnav("Weapons and Armour","runmodule.php?module=basictraining&op=weaponsandarmour");
			addnav("Buffs","runmodule.php?module=basictraining&op=buffs");
			addnav("Tangents");
			addnav("Ask about Combat","runmodule.php?module=basictraining&op=combat");
			addnav("Change the subject");
			addnav("Ask about something else","runmodule.php?module=basictraining&op=start");
			addnav("Leave Basic Training");
			addnav("Exit to NewHome","village.php");
			break;
		case "hud-defence":
			output("\"`2Your DEFENCE rating establishes how RESILIENT you are to ATTACKS from VARIOUS AGGRESSORS!  A high DEFENCE rating will allow you to take HEAVIER blows without losing as many HITPOINTS.  It will ALSO allow you to deal more frequent COUNTER-ATTACKS!  You can RAISE your DEFENCE rating with ARMOUR, by going UP a LEVEL, or with BUFFS!\"");
			addnav("Ask about Statistics");
			addnav("Level","runmodule.php?module=basictraining&op=hud-level");
			addnav("Hitpoints","runmodule.php?module=basictraining&op=hud-hitpoints");
			addnav("Attack","runmodule.php?module=basictraining&op=hud-attack");
			addnav("Stamina","runmodule.php?module=basictraining&op=stamina");
			addnav("Requisition","runmodule.php?module=basictraining&op=requisition");
			addnav("Cigarettes","runmodule.php?module=basictraining&op=cigarettes");
			addnav("Experience","runmodule.php?module=basictraining&op=experience");
			addnav("Next Day","runmodule.php?module=basictraining&op=newday");
			addnav("Weapons and Armour","runmodule.php?module=basictraining&op=weaponsandarmour");
			addnav("Buffs","runmodule.php?module=basictraining&op=buffs");
			addnav("Tangents");
			addnav("Ask about Combat","runmodule.php?module=basictraining&op=combat");
			addnav("Change the subject");
			addnav("Ask about something else","runmodule.php?module=basictraining&op=start");
			addnav("Leave Basic Training");
			addnav("Exit to NewHome","village.php");
			break;
		case "stamina":
			output("\"`2Your STAMINA is a measure of how TIRED you are!  Whenever you are about to do something that will consume STAMINA, the amount of Stamina the action will cost will be displayed to the RIGHT of the relevant LINK!  If you are a SIGHTED player, you will see that the STAMINA amount is highlighted in ORANGE!  If you are a BLIND or VISUALLY IMPAIRED player using a SCREEN READER like NVDA or... I suppose... JAWS - `0\" he takes a moment to spit on the ground at the very mention of whatever the hell JAWS is - \"`2you can assume that ANYTHING in BRACKETS expressed as a PERCENTAGE is a STAMINA cost reading!`0\"`n`n\"`2Players used to playing LEGEND of the GREEN DRAGON will be NEW to STAMINA, so I'll try to EXPLAIN it as BEST I can HERE.  When you are TIRED, your chances of SUCCEEDING in any given ACTION or in COMBAT are `iDIMINISHED!`i  When your Stamina is in the RED, you are in SEVERE peril of simply LOSING CONSCIOUSNESS!  Over time you will LEVEL UP in certain particular SKILLS - in a NUTSHELL, the MORE you perform a particular ACTION and the HIGHER your LEVEL for that ACTION, the FEWER Stamina points that ACTION will COST to PERFORM.  `iSO!`i  If you enjoy TRAVELLING more than COMBAT, and do it more REGULARLY, then you will find that your character will EVENTUALLY become very PROFICIENT in TRAVEL, and you will be able to travel FURTHER in a SINGLE DAY.  Think of it like a CHARACTER CLASS, except that rather than CHOOSING or CREATING a class at the start of the GAME, your class is `iAUTOMATICALLY`i created by the game, ACCORDING to the way in which you play, `iJUST FOR YOU!`i`0\"`n`nAgain with the computer game analogies.  And it sounds like a bloody complex game he's describing.  You're very, very glad that you don't live in this man's world.`n`nYou nod and smile as the madman continues his gibbering.`n`n\"`2AS you explore the ISLAND, you will very likely come across PEOPLE who can teach you new SKILLS.  To get an OVERVIEW of your SKILLS, along with any BONUSES or PENALTIES to STAMINA COST or ACTION EXPERIENCE GAIN, just click the STAMINA link in your HEAD-UP DISPLAY.  BONUSES and PENALTIES to the cost and experience gain of individual ACTIONS can be applied by different MOUNTS, various items of FOOD and DRINK, certain PILLS, and various OTHER items and events.`0\"`n`n\"`#You know,`0\" you mumble, \"`#this all sounds `ihorrendously`i complicated.`0\"`n`n\"`2YES, it DOES!  `iHOWEVER`i, in MY experience of teaching NEWBIES like YOU, nearly ALL of them have found the system very EASY to use after a minute or two of USING it.  This is probably just one of those things that's a `ilot`i easier to learn by doing it, rather than by having me explain how it works.`0\"`n`nYou shrug.  I guess we'll see about that.");
			addnav("Ask about Statistics");
			addnav("Level","runmodule.php?module=basictraining&op=hud-level");
			addnav("Hitpoints","runmodule.php?module=basictraining&op=hud-hitpoints");
			addnav("Attack","runmodule.php?module=basictraining&op=hud-attack");
			addnav("Defence","runmodule.php?module=basictraining&op=hud-defence");
			addnav("Requisition","runmodule.php?module=basictraining&op=requisition");
			addnav("Cigarettes","runmodule.php?module=basictraining&op=cigarettes");
			addnav("Experience","runmodule.php?module=basictraining&op=experience");
			addnav("Next Day","runmodule.php?module=basictraining&op=newday");
			addnav("Weapons and Armour","runmodule.php?module=basictraining&op=weaponsandarmour");
			addnav("Buffs","runmodule.php?module=basictraining&op=buffs");
			addnav("Tangents");
			addnav("Ask about Combat","runmodule.php?module=basictraining&op=combat");
			addnav("Ask about Travel","runmodule.php?module=basictraining&op=travel");
			addnav("Ask about Backpacks and Bandoliers","runmodule.php?module=basictraining&op=backpackbandolier");
			addnav("Ask about Mounts","runmodule.php?module=basictraining&op=mounts");
			addnav("Ask about food and drink","runmodule.php?module=basictraining&op=foodanddrink");
			addnav("Change the subject");
			addnav("Ask about something else","runmodule.php?module=basictraining&op=start");
			addnav("Leave Basic Training");
			addnav("Exit to NewHome","village.php");
			break;
		case "requisition":
			output("\"`2REQUISITION tokens are little silver COINS that are dispensed from HOPPERS mounted underneath the CAMERAS that you see EVERYWHERE on the Island.  When you defeat a MONSTER, some TOKENS will be EJECTED from the HOPPER.  These tokens can be used to procure GOODS and SERVICES.  They are an official currency, but SOME merchants will refuse to accept them, opting for CIGARETTES instead.`0\"");
			addnav("Ask about Statistics");
			addnav("Level","runmodule.php?module=basictraining&op=hud-level");
			addnav("Hitpoints","runmodule.php?module=basictraining&op=hud-hitpoints");
			addnav("Attack","runmodule.php?module=basictraining&op=hud-attack");
			addnav("Defence","runmodule.php?module=basictraining&op=hud-defence");
			addnav("Stamina","runmodule.php?module=basictraining&op=stamina");
			addnav("Cigarettes","runmodule.php?module=basictraining&op=cigarettes");
			addnav("Experience","runmodule.php?module=basictraining&op=experience");
			addnav("Next Day","runmodule.php?module=basictraining&op=newday");
			addnav("Weapons and Armour","runmodule.php?module=basictraining&op=weaponsandarmour");
			addnav("Buffs","runmodule.php?module=basictraining&op=buffs");
			addnav("Change the subject");
			addnav("Ask about something else","runmodule.php?module=basictraining&op=start");
			addnav("Leave Basic Training");
			addnav("Exit to NewHome","village.php");
			break;
		case "cigarettes":
			output("\"`2CIGARETTES can be either SMOKED or used in TRADE.  They can be obtained VERY occasionally in COMBAT.  SMOKING a cigarette will result in GREATER COMBAT EFFICACY, but can also lead to CRAVINGS.  Some merchants INSIST on being paid in CIGARETTES rather than in REQUISITION tokens.  Also, AMERICAN players should remember that SOME people on the ISLAND are BRITISH, myself included, and may refer to cigarettes as `iFAGS`i, which I understand is also a rather INSULTING American term for a GAY gentleman.  This is a distinction that both AMERICAN and BRITISH players should be `iAWARE`i of, because sometimes `iMISUNDERSTANDINGS`i can occur!  In general, if someone says that, for example, they need more fags in order to get a good Mount, you should give them the BENEFIT of the DOUBT and assume that they're BRITISH and are, in fact, talking about DELICIOUS CIGARETTES.`0\"");
			addnav("Ask about Statistics");
			addnav("Level","runmodule.php?module=basictraining&op=hud-level");
			addnav("Hitpoints","runmodule.php?module=basictraining&op=hud-hitpoints");
			addnav("Attack","runmodule.php?module=basictraining&op=hud-attack");
			addnav("Defence","runmodule.php?module=basictraining&op=hud-defence");
			addnav("Stamina","runmodule.php?module=basictraining&op=stamina");
			addnav("Requisition","runmodule.php?module=basictraining&op=requisition");
			addnav("Experience","runmodule.php?module=basictraining&op=experience");
			addnav("Next Day","runmodule.php?module=basictraining&op=newday");
			addnav("Weapons and Armour","runmodule.php?module=basictraining&op=weaponsandarmour");
			addnav("Buffs","runmodule.php?module=basictraining&op=buffs");
			addnav("Change the subject");
			addnav("Ask about something else","runmodule.php?module=basictraining&op=start");
			addnav("Leave Basic Training");
			addnav("Exit to NewHome","village.php");
			break;
		case "experience":
			output("\"`2EXPERIENCE is awarded with every VILE CREATURE you dispatch in the JUNGLE!  With enough EXPERIENCE, you will be able to CHALLENGE your MASTER and ascend one LEVEL.`0\"");
			addnav("Ask about Statistics");
			addnav("Level","runmodule.php?module=basictraining&op=hud-level");
			addnav("Hitpoints","runmodule.php?module=basictraining&op=hud-hitpoints");
			addnav("Attack","runmodule.php?module=basictraining&op=hud-attack");
			addnav("Defence","runmodule.php?module=basictraining&op=hud-defence");
			addnav("Stamina","runmodule.php?module=basictraining&op=stamina");
			addnav("Requisition","runmodule.php?module=basictraining&op=requisition");
			addnav("Cigarettes","runmodule.php?module=basictraining&op=cigarettes");
			addnav("Next Day","runmodule.php?module=basictraining&op=newday");
			addnav("Weapons and Armour","runmodule.php?module=basictraining&op=weaponsandarmour");
			addnav("Buffs","runmodule.php?module=basictraining&op=buffs");
			addnav("Tangents");
			addnav("Ask about Combat","runmodule.php?module=basictraining&op=combat");
			addnav("Change the subject");
			addnav("Ask about something else","runmodule.php?module=basictraining&op=start");
			addnav("Leave Basic Training");
			addnav("Exit to NewHome","village.php");
			break;
		case "newday":
			output("\"`2In Improbable Island, there are several new GAME DAYS per REAL DAY.  At a new GAME DAY, your HITPOINTS are restored to their MAXIMUM, as is your STAMINA, and most BUFFS are removed or reset.  All players who spent the night on the FAILBOAT due to their own INEXPERIENCE, poor JUDGEMENT or simply BAD LUCK, will be RESTORED to the Island at the beginning of each new DAY.`0\"`n`n\"`2Since few people have the spare TIME to play every GAME DAY, a certain number of GAME DAYS can be stored up for later use, simply by not logging in.`0\"");
			addnav("Ask about Statistics");
			addnav("Level","runmodule.php?module=basictraining&op=hud-level");
			addnav("Hitpoints","runmodule.php?module=basictraining&op=hud-hitpoints");
			addnav("Attack","runmodule.php?module=basictraining&op=hud-attack");
			addnav("Defence","runmodule.php?module=basictraining&op=hud-defence");
			addnav("Stamina","runmodule.php?module=basictraining&op=stamina");
			addnav("Requisition","runmodule.php?module=basictraining&op=requisition");
			addnav("Cigarettes","runmodule.php?module=basictraining&op=cigarettes");
			addnav("Experience","runmodule.php?module=basictraining&op=experience");
			addnav("Weapons and Armour","runmodule.php?module=basictraining&op=weaponsandarmour");
			addnav("Buffs","runmodule.php?module=basictraining&op=buffs");
			addnav("Tangents");
			addnav("Ask about the FailBoat","runmodule.php?module=basictraining&op=failboat");
			addnav("Change the subject");
			addnav("Ask about something else","runmodule.php?module=basictraining&op=start");
			addnav("Leave Basic Training");
			addnav("Exit to NewHome","village.php");
			break;
		case "weaponsandarmour":
			output("\"`2WEAPONS and ARMOUR can be purchased from SHEILA'S SHACK, and will improve your ATTACK and DEFENCE ratings.`0\"");
			addnav("Ask about Statistics");
			addnav("Level","runmodule.php?module=basictraining&op=hud-level");
			addnav("Hitpoints","runmodule.php?module=basictraining&op=hud-hitpoints");
			addnav("Attack","runmodule.php?module=basictraining&op=hud-attack");
			addnav("Defence","runmodule.php?module=basictraining&op=hud-defence");
			addnav("Stamina","runmodule.php?module=basictraining&op=stamina");
			addnav("Requisition","runmodule.php?module=basictraining&op=requisition");
			addnav("Cigarettes","runmodule.php?module=basictraining&op=cigarettes");
			addnav("Experience","runmodule.php?module=basictraining&op=experience");
			addnav("Next Day","runmodule.php?module=basictraining&op=newday");
			addnav("Buffs","runmodule.php?module=basictraining&op=buffs");
			addnav("Change the subject");
			addnav("Ask about something else","runmodule.php?module=basictraining&op=start");
			addnav("Leave Basic Training");
			addnav("Exit to NewHome","village.php");
			break;
		case "buffs":
			output("\"`2BUFFS are temporary, semi-permanent or permanent MODIFIERS to your ATTACK and/or DEFENCE ratings, or the ATTACK and DEFENCE ratings of the MONSTER you happen to be FIGHTING.  You will come across these as you EXPLORE.`0\"");
			addnav("Ask about Statistics");
			addnav("Level","runmodule.php?module=basictraining&op=hud-level");
			addnav("Hitpoints","runmodule.php?module=basictraining&op=hud-hitpoints");
			addnav("Attack","runmodule.php?module=basictraining&op=hud-attack");
			addnav("Defence","runmodule.php?module=basictraining&op=hud-defence");
			addnav("Stamina","runmodule.php?module=basictraining&op=stamina");
			addnav("Requisition","runmodule.php?module=basictraining&op=requisition");
			addnav("Cigarettes","runmodule.php?module=basictraining&op=cigarettes");
			addnav("Experience","runmodule.php?module=basictraining&op=experience");
			addnav("Next Day","runmodule.php?module=basictraining&op=newday");
			addnav("Weapons and Armour","runmodule.php?module=basictraining&op=weaponsandarmour");
			addnav("Change the subject");
			addnav("Ask about something else","runmodule.php?module=basictraining&op=start");
			addnav("Leave Basic Training");
			addnav("Exit to NewHome","village.php");
			break;
		case "mounts":
			output("\"`2MOUNTS are creatures or machines that you can RIDE AROUND.  Some of these Mounts will FIGHT with you in COMBAT!`0\" spits the Corporal.  \"`2OTHERS will allow you to travel FUTHER in a single DAY!  You can buy a Mount at Mike's Chop Shop.  Most Chop Shops will only accept CIGARETTES as PAYMENT!`0\"");
			addnav("Questions");
			addnav("Ask about Combat","runmodule.php?module=basictraining&op=combat");
			addnav("Ask about Travel","runmodule.php?module=basictraining&op=travel");
			addnav("Ask about Stamina","runmodule.php?module=basictraining&op=stamina");
			addnav("Ask about Buffs","runmodule.php?module=basictraining&op=buffs");
			addnav("Ask about Cigarettes","runmodule.php?module=basictraining&op=cigarettes");
			addnav("Change the subject");
			addnav("Ask about something else","runmodule.php?module=basictraining&op=start");
			addnav("Leave Basic Training");
			addnav("Exit to NewHome","village.php");
			break;
		case "foodanddrink":
			output("\"`2EATING and DRINKING are ESSENTIAL for your continued SURVIVAL!`0\" barks the Corporal. \"`2If you do not keep yourself WELL-FED, you run the risk of waking up with less STAMINA than you would normally have!  Likewise, if you only eat foods high in fat or low in nutritional content, you will ALSO suffer ill-effects!  HOWEVER, if you eat only good-quality, nutritious food, you will find yourself with MORE Stamina each DAY!`0\"");
			addnav("Questions");
			addnav("Ask about Stamina","runmodule.php?module=basictraining&op=stamina");
			addnav("Ask about Buffs","runmodule.php?module=basictraining&op=buffs");
			addnav("Ask about Game Days","runmodule.php?module=basictraining&op=newday");
			addnav("Change the subject");
			addnav("Ask about something else","runmodule.php?module=basictraining&op=start");
			addnav("Leave Basic Training");
			addnav("Exit to NewHome","village.php");
			break;
		case "failboat":
			output("\"`2The FAILBOAT is where you will be sent if you are found UNCONSCIOUS!`0\" barks the Corporal. \"`2If you are DEFEATED in COMBAT, or if you LOSE CONSCIOUSNESS due to EXHAUSTION, this is where you'll end up!  To get off the FailBoat and back onto the Island, you'll have to impress `\$THE WATCHER.`2  She will let you back on to the Island when you either undergo retraining to her satisfaction, or when the next Game Day arrives.`0\"");
			addnav("Questions");
			addnav("Ask about Stamina","runmodule.php?module=basictraining&op=stamina");
			addnav("Ask about Combat","runmodule.php?module=basictraining&op=combat");
			addnav("Ask about Game Days","runmodule.php?module=basictraining&op=newday");
			addnav("Change the subject");
			addnav("Ask about something else","runmodule.php?module=basictraining&op=start");
			addnav("Leave Basic Training");
			addnav("Exit to NewHome","village.php");
			break;
		case "combat":
			output("\"`2The EASIEST way to get your feet wet in COMBAT is to march PROUDLY into the JUNGLE and start LOOKING for TROUBLE!`0\" yells the Corporal, his moustache jiggling as he grins. \"`2Better WEAPONS and ARMOUR will IMPROVE your chances of success, as will certain BUFFS.  When you are INJURED, be sure to HEAL yourself using MEDKITS, or at the HOSPITAL TENT.  Every MONSTER you DEFEAT will grant both EXPERIENCE and REQUISITION!  Sometimes you will come across a MONSTER that is BIGGER than you, and it would be a good idea to `iRUN LIKE HELL!`i  Sometimes the monster will CHASE you as you flee, in which case you may wish to either ABANDON the chase and go back to FIGHTING, or CONTINUE trying to run, GIBBERING and SCREAMING and `iFLAILING YOUR ARMS`i as you do so.  If you are DEFEATED in COMBAT, you will be knocked UNCONSCIOUS.  You will `iLOSE`i a certain amount of EXPERIENCE.  You will LOSE `iALL`i of the REQUISITION tokens that you have on your person.  And you `iWILL`i be `iSENT`i to the `iFAILBOAT!`i  THIS is why it is IMPORTANT to know when to RUN, when to FIGHT, when to use GRENADES and other OFFENSIVE items, and to `iFREQUENTLY BANK YOUR REQUISTION`i.  And REMEMBER, you can ONLY use items in COMBAT that you have placed in your `iBANDOLIER`i.  You DON'T want to be rummaging around in your BACKPACK during a FIGHT, sunshine!`0\"");
			addnav("Questions");
			addnav("Ask about Stamina","runmodule.php?module=basictraining&op=stamina");
			addnav("Ask about Requisition","runmodule.php?module=basictraining&op=requisition");
			addnav("Ask about Weapons and Armour","runmodule.php?module=basictraining&op=weaponsandarmour");
			addnav("Ask about Experience","runmodule.php?module=basictraining&op=experience");
			addnav("Ask about Buffs","runmodule.php?module=basictraining&op=buffs");
			addnav("Ask about the FailBoat","runmodule.php?module=basictraining&op=failboat");
			addnav("Change the subject");
			addnav("Ask about something else","runmodule.php?module=basictraining&op=start");
			addnav("Leave Basic Training");
			addnav("Exit to NewHome","village.php");
			break;
		case "travel":
			output("\"`2TRAVEL between different OUTPOSTS can be a DANGEROUS and LONELY affair!`0\" yells the Corporal. \"`2You will be accosted by more MONSTERS in some terrain types than in others!  Likewise, navigating different types of TERRAIN will cost MORE or LESS Stamina!  Certain MOUNTS will make TRAVEL easier, and reduce the chances of being ACCOSTED by a `iMONSTER`i on the way!  Bear in mind that the more TIRED you are, the SLOWER you will move, and the more VULNERABLE you will be to MONSTER attack!  The two EASIEST Outposts to get to from here are KITTANIA, the home of the KITTYMORPHS, and IMPROBABLE CENTRAL, the `iCAPITAL`i CITY.  To get to EITHER of these places, head NORTH EAST from here.  When you get to a FORK in the PATH, head `iNORTH`i for Improbable CENTRAL, or `iEAST`i for KITTANIA!  Remember to make sure you are fully HEALED, and STOCK UP on MEDKITS if possible before you go, because there are NO HEALERS out there in the wilderness.`0\"");
			addnav("Questions");
			addnav("Ask about Stamina","runmodule.php?module=basictraining&op=stamina");
			addnav("Ask about Mounts","runmodule.php?module=basictraining&op=mounts");
			addnav("Ask about Combat","runmodule.php?module=basictraining&op=combat");
			addnav("Change the subject");
			addnav("Ask about something else","runmodule.php?module=basictraining&op=start");
			addnav("Leave Basic Training");
			addnav("Exit to NewHome","village.php");
			break;
		}
	page_footer();
}
?>