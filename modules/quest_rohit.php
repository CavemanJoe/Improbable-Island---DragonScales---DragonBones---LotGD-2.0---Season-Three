<?php

function quest_rohit_getmoduleinfo(){
	$info = array(
		"name"=>"Quests: Rohit the Robot",
		"version"=>"2010-10-05",
		"author"=>"Dan Hall / Emily Hall",
		"category"=>"Quests",
		"download"=>"",
		"prefs"=>array(
			//we won't be searching on this info, so let's make it a serialized array to reduce the size of the moduleprefs table
			"plotpoint1"=>"Player has been offered the cigarette,bool|0",
			"plotpoint2"=>"Player has talked with Rohit,bool|0",
			"plotpoint2_response"=>"Player is now friends with Rohit,bool|0",
		),
	);
	return $info;
}

function quest_rohit_install(){
	module_addhook("worldnav");
	module_addeventhook("village", "require_once(\"modules/quest_rohit.php\"); return quest_rohit_test();");
	return true;
}

function quest_rohit_uninstall(){
	return true;
}

function quest_rohit_test() {
	global $session;
	if ($session['user']['location'] != "Cyber City 404" || get_module_pref("plotpoint1","quest_rohit") || $session['user']['race'] == "Robot"){
		return 0;
	} else {
		return 100;
	}
}

function quest_rohit_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "worldnav":
			if (get_module_pref("plotpoint1") && !get_module_pref("plotpoint2") && e_rand(0,100) < 10 && $session['user']['race'] != "Robot"){
				redirect("runmodule.php?module=quest_rohit&op=plotpoint2","Redirecting to Rohit Plot Point 2 from World Map");
			}
		break;
		}
	return $args;
}

function quest_rohit_runevent($type){
	global $session;
	$from = "village.php?";
	$session['user']['specialinc'] = "module:quest_rohit";
	output("`0As you walk down the main street of Cyber City 404, a tall Robot approaches.  \"`3Good day,`0\" it says in a stilted, mechanical voice, \"`3I wonder if you'd be willing to help me out with a simple experiment.  There's a cigarette in it for you.`0\"`n`nSound good?`n`n");
	addnav("What do you say?");
	addnav("Sure!","runmodule.php?module=quest_rohit&op=plotpoint1_accept");
	addnav("No, thanks!","runmodule.php?module=quest_rohit&op=plotpoint1_decline");
}

function quest_rohit_run(){
	global $session;
	$op = httpget("op");
	switch ($op){
		case "plotpoint1_accept":
			$session['user']['specialinc'] = "";
			page_header("I'll have some of that!");
			output("The Robot nods, and hands you a single cigarette.  \"`3Thank you very much for participating.`0\"  It wanders off, leaving you thoroughly confused.`n`nMaybe it was an experiment to see whether people would take a cigarette from a Robot?  You recall reading about similar experiments conducted in London and Manchester, where those conducting the experiment handed out free £5 notes - if you recall correctly fewer than two per cent of people accepted the money, assuming that there was some sort of catch.  Maybe you've just been involved in a similar experiment.`n`nYou feel vaguely proud of yourself for not yet becoming so unpleasantly cynical.`n`n");
			$session['user']['gems']++;
			set_module_pref("plotpoint1",true);
			addnav("Continue");
			addnav("Back to the Outpost","village.php");
		break;
		case "plotpoint1_decline":
			$session['user']['specialinc'] = "";
			page_header("Yeah, right.");
			output("`0You shake your head, and quickly move on.  Must have been a scam, right?`n`n");
			addnav("Continue");
			addnav("Back to the Outpost","village.php");
		break;
		case "plotpoint2":
			page_header("When you gotta go...");
			if ($session['user']['sex']){
				//female
				$peetext = "Glancing around to make sure you're alone, you sneak behind a nearby bush.";
			} else {
				//male
				$peetext = "Glancing around to make sure you're alone, you sidle up to a nearby tree.";
			}
			output("You don't think you can put off peeing any longer.  %s  Business taken care of, you zip up and turn around to see a Robot crouched in the bushes, holding a clipboard and staring at you.  It nods a greeting, and marks something down on its clipboard.`n`n\"`#What in the bloody hell do you think you're doing?!`0\"`n`n\"`3Thanking you for your participation in our experiment, which is now concluded.  Good day.`0\"  The Robot turns to leave.`n`n\"`#HEY!  No!  No, you don't get to just spy on me taking a leak and then bloody walk away without so much as an explanation!`0\"`n`n\"`3I sincerely doubt you would understand.`0\"`n`n\"`#`iTry`i me, you supercilious bastard.`0\"`n`nThe Robot pauses, and sighs.  Or at least, it pauses and issues from its speaker a pre-recorded static burst approximating a sigh.  One that, you assume, it has recorded specifically for dealing with lesser organic beings such as yourself.  \"`3If it would make you happy.  Follow me.`0\"`n`nYou tromp through the Jungle a little ways, listening to the Robot drone.`n`n\"`3Your services - which we paid for with that cigarette, remember, with no implied obligation to interact with you in any other way - your services were required to provide a random seed.  We got the idea shortly after hearing about a certain betting game practiced in some more rural areas.  A cow is released into a field divided into square sections, and bets taken over which section the cow will defecate in...`0\"`n`n\"`#You were `ibetting`i on where I'd `ipiss?!`i`0\"`n`n\"`3Gambling is a tax on the stupid, and interrupting an explanation you specifically requested - and which we are in no way obligated to provide, I'd remind you - is counter-productive, so I will spare you some embarrassment and resume my explanation, you're welcome.  Since sapient organics in aggregate are by their very nature more unpredictable than cows, we appreciated your services as a random number generator.  We divided the whole of the Island into a hexagonal grid and observed where you chose to urinate.`0\"`n`n\"`#If you wanted a random number, why didn't you just... hell, flip coins, or roll dice, or go and see a Joker?`0\"`n`n\"`3There's random and there's random, as I'm sure you're finding out.  Here we are.`0\"`n`nYou are in a small clearing with a large wooden table that has certainly seen better days.  On top of the rotting table are rows of round metal discs, about the diameter of the palm of your hand.  \"`3These are components of a logical governing device built into every Robot.  Since it is located in the center of the chest, your kind will probably leapt to your typical anthropomorphic conclusions and call it a heart.  The steel discs are laid upon this table and allowed to rust as they will.  A hole is drilled through the centre of the disc, and it is mounted on a rotational motor.  The disc is spun up to several thousand RPM, and an optical sensor captures data from random points on the disc.  The shade of rust passing under the optical sensor at a given moment is converted to a raw value and passed back to the unit that initiated the instruction.  Your random seed determines the sensor polling interval, and the range on the disc, from inner to outer, from which the data is collected.  Observe.`0\"`n`nThe Robot casually removes the glass front of its chest and pulls a pair of pins from where its lungs should be.  Its ribcage swings open, exposing bundles of wires in every colour.  It reaches between these bundles and you hear a `iclick`i - then it's holding a metal box, which it opens to reveal a spinning disc and mounted optical sensor, just like it said.  \"`3As you can see, the disc spins fast enough that it is nearly impossible to predict from where the optical sensor will retrieve its data.`0\"`n`n\"`#Why?`0\" you ask.`n`n\"`3This is our approximation of the phenomenon you know as 'free will.'  It is also an emulation of what you call 'individuality.'  It grants us the ability to act irrationally while still acting in accordance with parameters established a long time in our past.  A psuedorandom number generator as built into our motherboards acts alone - it generates a number and that's it.  It is a closed system that does not interact with the rest of the world.  Our kind wishes to be a part of the world, so the world provides us with what a game enthusiast would call our base rolls.`0\"`n`nYou frown.  \"`#So these are... seeds?  Is that what you'd call them?  These are, like, baby Robots?`0\"`n`n\"`3No,`0\" says the Robot firmly.  \"`3A Robot is the culmination of its lifetime of experience, wrapped in a mobile skeleton of superior strength and reflexes.  It is a collection of observations and experiences, combined with the intelligence to process them.  These,`0\" it waves one hand over the table, putting its heart back into its chest, \"`3are rusted metal discs.  Just as there are no babies or children on the Island, there `iare`i no \"baby Robots,\" as you put it.  There are only Robots, and components awaiting assembly.`0\"`n`n\"`#So the object in your chest - it's a glorified random number generator, from which you gain a personality?`0\"`n`n\"`3As a vast oversimplification suitable for organic consumption, that will do.  The important part is that the disc is allowed to oxidise however the weather dictates, and that the optical sensor's range of motion and polling intervals are decided by the biological rhythms of a randomly-selected sapient creature.  In this case, yourself.`0\"`n`nYou look at the steel discs, slowly rusting on the table.  You turn to the Robot.`n`n\"`#You're right, I `idon't`i understand.`0\"`n`n\"`3Nobody expected you to.  The algorithms involved are remarkably complex.`0\"`n`n\"`#Plus, you didn't try to tell me about them.  I meant that I didn't understand `iwhy`i you put so much emphasis on random outside influence.  Why the ritual?  Why do you `icare?`i`0\"`n`n\"`3Because we are a part of this world.`0\"  The Robot does not raise its voice, because Robots don't do that.  \"`3Because we need to keep a part of the outside world inside us to remind us of that,`0\" continues the Robot, not gesticulating.  \"`3Because nobody understands us and we don't expect them to, but by revising ourselves we may begin to understand your kind, and if at least one of us understands the other then maybe there is hope.`0\"  The Robot does not pound the table with its fist.  \"`3There is too much tension between our people, too much mistrust and suspicion.  I think this is unsatisfactory, and others agree with me.  It is time for change.`0\"`n`nYou look at the Robot carefully for a moment.  \"`#You know,`0\" you say slowly, \"`#For all your talk of understanding, you haven't even asked me my name.`0\"`n`n\"`3Do you want me to ask you your name?`0\"`n`n",$peetext);
			addnav("What do you say?");
			addnav("Yes","runmodule.php?module=quest_rohit&op=plotpoint2_accept");
			addnav("No","runmodule.php?module=quest_rohit&op=plotpoint2_decline");
			set_module_pref("plotpoint2",true);
		break;
		case "plotpoint2_accept":
			page_header("What are you getting yourself into now");
			output("\"`3Then please state your name.`0\"`n`n\"`#People call me %s.`0\"`n`n\"`3Do people call you %s because your name is %s?`0\"`n`n\"`#`iYou`i can call me %s.`0\"`n`n\"`3That is not what I asked.`0\"`n`n\"`#This is why people don't understand you.  When you ask someone their name and they ask you to call them something, take it that that's their name, okay?`0\"`n`nThe Robot hesitates.  \"`3Organics are very hard to understand, but if it means making a friend, I will overlook this.`0\"`n`n\"`#What's your name?`0\"`n`n\"`3Rohit.`0\"`n`nYou extend a hand.  \"`#Nice to meet you, Rohit.`0\"`n`nRohit looks down at your hand.  \"`3I understand this at least,`0\" he says, and extends a hand of his own, encasing yours.  His glass skin is far, far colder than it has any right to be.`n`n\"`#Are you serious about this?  Wanting us to be friends?`0\"`n`n\"`3Yes, but I do not understand how to be friends.`0\"`n`n\"`#`iI`i understand friends.  I know how to be friends with someone.  Honestly I don't have the slightest idea how to go about being friends with someone `ithis`i different, but I'm willing to try, and to learn, if you are.`0\"`n`n\"`3I will try if you will help.`0\"`n`n\"`#Likewise.  Deal.`0\"`n`n\"`3By which I assume you mean 'we have a deal,' correct?  English has always been a huge stumbling block in our communications.`0\"`n`n\"`#Yes, we have a deal.`0\"`n`n\"`3Then when we see each other next, we should talk.  Until then, I will continue seeding my generators and tending to my discs.  It has been nice meeting you.`0\"`n`nAnd with that, Rohit abruptly turns away.`n`nMy.  You do seem to be making some strange friends indeed.`n`n",$session['user']['name'],$session['user']['name'],$session['user']['name'],$session['user']['name']);
			addnav("Return");
			addnav("Back to the Map","runmodule.php?module=worldmapen&op=continue");
			set_module_pref("plotpoint2_response",true);
		break;
		case "plotpoint2_decline":
			page_header("Robots have feelings too");
			output("You think for a moment.  \"`#No, I don't.`0\"`n`nThen you turn around, and walk away.`n`nAfter watching you leave, the Robot searches its internal filesystem for an appropriately disappointed and lonely sigh, and queues it up for playback.  Due to a file mislabelling, a loud fart issues from its speaker.  It lowers its head, and goes back to its rusted discs.`n`n");
			addnav("Return");
			addnav("Back to the Map","runmodule.php?module=worldmapen&op=continue");
		break;
	}
	page_footer();
}

?>