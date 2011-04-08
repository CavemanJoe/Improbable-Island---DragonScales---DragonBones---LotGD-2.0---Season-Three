<?php

function watcher_quests_getmoduleinfo(){
	$info = array(
		"name"=>"Watcher Quests",
		"version"=>"2009-07-04",
		"author"=>"Dan Hall",
		"category"=>"The Watcher",
		"download"=>"",
		"prefs"=>array(
			"Watcher Quest module prefs,title",
			"plotpoint1"=>"Has the player seen the first plot point?,int|0",
			"plotpoint2"=>"Has the player gotten a bum in the face?,int|0",
			"plotpoint3"=>"Has the player asked The Watcher about her silly turtleneck?,int|0",
			"plotpoint3a"=>"Has the player received a letter from The Watcher?,int|0",
		),
	);
	return $info;
}

function watcher_quests_install(){
	module_addhook("village");
	module_addhook("mausoleum");
	return true;
}

function watcher_quests_uninstall(){
	return true;
}

function watcher_quests_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "village":
			//require_once("lib/http.php");
			if ($session['user']['level'] >= 3 && get_module_pref("plotpoint1")==0){
				addnav("","runmodule.php?module=watcher_quests&plotpoint=1");
				redirect("runmodule.php?module=watcher_quests&plotpoint=1","Watcher plot point 1");
			}
			if (get_module_pref("plotpoint3") && !get_module_pref("plotpoint3a")){
				$body = "Thank you for talking to me.  It honestly meant a lot more than I let on.`n`nI would love to come and see you sometime soon, but I have to figure out when I can take the evening off.  It might be a while.`n`nMeanwhile, I hope you'll be okay with this, but I have to act around you the same way I would if we didn't know each other; I could get in a lot of trouble if I'm anything but my usual self while I'm on the Vessel.  I hope you understand.`n`nWith any luck, I'll talk to you properly again soon.`n`n-Watcher";
				require_once("lib/systemmail.php");
				systemmail($session['user']['acctid'],"Thank You",$body,3517);
				set_module_pref("plotpoint3a",true);
			}
			if ($session['user']['dragonkills'] >= 3 && get_module_pref("plotpoint2")==0 && $session['user']['gems'] > 1){
				addnav("","runmodule.php?module=watcher_quests&plotpoint=2");
				redirect("runmodule.php?module=watcher_quests&plotpoint=2","Watcher plot point 2");
			}
			break;
		case "mausoleum":
			if ($session['user']['dragonkills'] >= 5 && get_module_pref("plotpoint2")==1 && get_module_pref("plotpoint3")==0){
				output("`n`n`\$The Watcher`0's forehead looks rather shiny.  She tugs distractedly at the collar of her turtleneck.`n`n");
				addnav("That's a shiny, shiny Watcher.");
				addnav("Ask what the deal is with that turtleneck","runmodule.php?module=watcher_quests&plotpoint=3");
			}
		break;
	}
	return $args;
}

function watcher_quests_run(){
	global $session;
	switch (httpget('plotpoint')){
		case 1:
			page_header("Meanwhile, somewhere offshore...");
			output("`\$The Watcher`0 is in her control room, and about to boot her computer.  She yawns, setting her cup of tea down on the shelf next to the keyboard.  She takes a seat, puts her foot on the clutch, switches on the heating coils and presses the starter button.`n`nNothing happens.`n`n\"`&Arse,`0\" she mutters, remembering that the starter motor died a couple of days ago.  She takes a brief stroll around to the back of the computer, picking up the six-foot-long iron crank handle as she passes.`n`nWith a total electromechanical equivalent of one hundred and twenty eight kilobytes of memory, starting this three-ton supercomputer by hand requires three strong men, lots of sweat, and language that would make a sailor blush.  `\$The Watcher`0 inserts the crank handle, pushes her glasses up onto the bridge of her nose, grabs the handle, takes a firm stance and gives it a spin.`n`nThe five-litre diesel engine, currently running on used vegetable oil, coughs into life.  Its heavy, thumping compressions send little shockwaves through the floor.  `\$The Watcher`0 pulls out the crank handle, pats the computer's casing, and returns to her seat.`n`nNobody saw her start the machine, either today or yesterday.  She intends to let the technicians know about the broken starter motor, but it keeps slipping her mind.  Probably because people are already noticing little things about her.  People are already talking.  People's smiles already seem a little frozen when they wish her a good morning.  If they knew that she had just started a three-ton supercomputer by hand, the worry in their eyes might turn to naked fear, and her already-shrinking circle of close friends might just shrink a little more.  Of course, she could always lie - just go up to the technicians tomorrow morning, declare that the starter had just broken, and ask the strong tech lads to give a girl a hand.  One more little white lie probably wouldn't hurt.`n`nShe puts her foot on the clutch and wrenches the monitor into gear.  As she slowly lets the clutch up, a mounted arm begins to spin, generating a warm breeze onto `\$The Watcher`0's face.  Arranged in one straight row on the arm are a dozen flickering thumbnail-sized light bulbs.`n`n`\$The Watcher`0 noticed something was wrong about six months ago.  She was lifting herself by her arms on a pull-up bar as part of her morning exercise routine.  Her mind wandered as she listened to the waves crashing outside.  Within ten pull-ups she was remembering the hours she used to spend playing pirates with Paul Stevens, both of them no older than seven or eight.  The radio was announcing the time when she realised she'd been doing pull-ups for over an hour.`n`nAs the mounted arm spins faster and faster, words begin to form in the blur, generated by the rapidly-flickering lamps and `\$The Watcher`0's persistence of vision.  When they converge into a clear picture, `\$The Watcher`0 sets the auto-throttle and takes her foot off the clutch.`n`nEvery day that she remains near the Island, she grows stronger.  Even if she hasn't bothered to exercise, even if she's pushed herself so hard she should have been exhausted.  Even if she's stuffed her face with junk food, even if she's starved herself all day.  No matter what she does, she becomes more powerful with each passing minute.  And with every extra foot-pound of force she can generate, she feels a little more unsure of her own capacity to handle this sort of power - and a little more lonely.`n`nShe laces her fingers together, pushes them outwards with a series of satisfying cracks and pops, and begins to press keys, pull levers, push pedals.  The supercomputer responds with the clatter of a million relays and mechanical sequencers, audible even over the roar of the diesel engine.  Words and numbers appear in the air where the swinging monitor arm now spins at several thousand rotations per minute.`n`nAfter a few minutes, the analysis routine is complete.  One name appears in the list of results.  One contestant who seems to be gaining strength nearly as quickly as `\$The Watcher`0 did herself.  One person who might, perhaps, understand what is happening to `\$The Watcher`0.  One person with whom she could potentially share a secret.`n`n\"`&%s`&... Huh.  Bit of a funny name,`0\" says `\$The Watcher`0 quietly, \"`&but I'll be watching you.`0\"",$session['user']['name']);
			set_module_pref("plotpoint1",1);
			addnav("Continue");
			addnav("Get on with it","village.php");
			break;
		case 2:
			page_header("Something Improbable!");
			output("`0As you head into the Outpost, something big and black appears in front of you, moving at speed.  It impacts on your face, knocking you on your back.  As you push yourself up onto your elbows, you try to make sense of what just happened.`n`nYou're not `ientirely`i sure, but you think... as strange as it sounds... it might have been someone's arse.`n`nYou replay the scene - yup.  Someone's arse, in a black skirt.  You only know one person on this Island who wears a black skirt.`n`n`\$The Watcher`0 - or at least, the top half of her - leans out of the air.  Her bespectacled eyes lock onto yours, and her face lights up in a huge grin.  \"`&Hey, it's you!  Well, isn't this a coincidence!`0\"  She reaches out to you.  You extend a hand, ready for her to help you up and, perhaps, explain where her legs are - and why she just beat you to the ground with her now-invisible posterior.`n`nNothing of the sort happens.  Instead, `\$The Watcher`0 expertly steals a cigarette from your pocket, lights it, gives you a cheery wink and leans back, disappearing right before your eyes.`n`nYou lie for a moment, staring at the space she just occupied.`n`n\"`#RIGHT THEN!`0\" you cry, in the vain hope that `\$The Watcher`0 can still hear you.  Birds scatter from the trees.  \"`#So THAT'S the sort of day this is going to be!  OKAY!  Thanks for the heads-up!`0\"  You get to your feet.  \"`#I'll expect to be mugged by a floating pair of boobs any second now!`0\"`n`nA passing contestant looks your way, lip curled up in distaste.  \"`2Weirdo,`0\" she mutters, before continuing on her way.");
			set_module_pref("plotpoint2",1);
			$session['user']['gems']--;
			addnav("Damn it.");
			addnav("Dust yourself off and head into the Outpost","village.php");
			break;
		case 3:
			page_header("The Deal with the Turtleneck");
			output("\"`&What?`0\"`n`n\"`#I mean... um.\"  You swallow.  \"`#The turtleneck.  It seems that every time I see you, you're... well, it's the red turtleneck, every time.  I mean... aren't you a bit hot, in that?`0\"`n`n`\$The Watcher`0 frowns at you for a moment.  Then she smiles, sits back in her chair and chuckles softly.  \"`&Well, it's pretty much all marketing, mate.`0\"`n`n\"`#Marketing?`0\"`n`n`\$The Watcher`0 leans forwards, pushing her glasses up onto her nose.  \"`&Well, you see, it's like this.  Since this particular artform was created, women in my line of work have been expected to fall into one of two categories.  We're either useless pansies whose only function is to be kidnapped and then saved by powerful men; or, we're strong, capable warriors with amazing bodies.  But if you go for being strong and capable, you're expected to have a regrettable tendency to wear `ipreposterously`i impractical armour.  Since I didn't think I'd suit the \"useless pansy\" trope, I decided to opt for the latter anyway.`0\"`n`nYou blink.  You open your mouth, then close it again and choose your words carefully, sensing an awkward discussion in your near future - one that would probably, sooner or later, end up revolving around chainmail bikinis.  \"`#...preposterously impractical armour?`0\"`n`n\"`&A calf-length black skirt and a turtleneck?  In `ithis`i weather?`0\"`n`n\"`#Oh.`0\"`n`n\"`&It's a bloody `inylon`i skirt, too.  You wouldn't believe the amount of static electricity this thing generates.  I suppose I could top it all off with a black leather jacket, or a nice knitted scarf, if I wanted to go the whole hog.  But honestly, my heart just isn't in it.`0\"`n`n\"`#Yeah, that would be... yeah.`0\"  Something nags at you.  \"`#You mentioned something... you said \"since this artform was created.\"  Which artform is that, exactly?`0\"`n`n`\$The Watcher`0 frowns.  \"`&Televised post-EMP warfare.`0\"  She laughs.  \"`&What did you `ithink`i I was talking about?`0\"`n`n\"`#Never mind.`0\"`n`n\"`&I draw the line at the boots, though.  You've got to have practical boots.`0\"  She leans back and a pair of steel-toed black leather monoliths clomp onto the desk.  \"`&I mean, technically these are bloke's boots, but I've always had quite broad feet, and I find men's boots tend to hold up better than women's and I'm `ibabbling`i, aren't I.`0\"`n`nIt wasn't phrased as a question.  `\$The Watcher`0 looks down at her boots, and there's a somewhat awkward pause.  \"`#Not really,`0\" you say.`n`n\"`&It felt like it.`0\"  With a heavy `ithump,`i she sets her feet back onto the floor.  \"`&To me, anyway... it's kinda hard to tell, really.  Sorry.`0\"  She toys distractedly with a pen.  \"`&My conversation skills are rusty because nobody talks to me.`0\"`n`nThere was something very disconcerting about the way she said that last sentence.  Not whining; not complaining; not even seeking interaction, really.  Just stating a fact.  Symptom and cause.  Like it's something she's gotten thoroughly used to.`n`n\"`#Really?`0\"`n`n\"`&Yes.  I suppose people must think of me like they think of, oh, I don't know... let's say an undertaker, or a repairman, or... or an angry boss, or something.  Contract says I've gotta give you guys a rough time down here in the Pits, but...  Most of it is just for show, and it doesn't give much room for creativity, and when you live on a boat and you do a boring job, you don't end up with much to talk about on the rare occasions when someone `idoes`i come along and try to engage you.  It's just the same damned thing, day after day.  Honestly sometimes I feel like I'm `iprogrammed`i to do this stuff.`0\"`n`nThere's another awkward pause.  You wonder how you got here from turtlenecks.`n`n\"`&I mean, I tried, like, wearing weird necklaces and stuff,`0\" `\$The Watcher`0 continues.  \"`&You know, you wear something old or unusual or interesting, it's like a conversation prop, sort of thing.  Sooner or later somebody's gonna come along and say \"Hey, that's an interesting... thing,\" and you tell the story, and... well, it's a `istart,`i you know?  But that doesn't work when you're contractually obliged to scowl at people, and even if you weren't... people don't feel too talkative when they're beaten half to death and running on adrenaline.`0\"  She looks up at you.  \"`&I'm glad to see you're still managing to be sociable.`0\"`n`nYou smile nervously.  It's the only way you can smile, right now.  \"`#I try my best.`0\"`n`n`\$The Watcher`0 gives a little nervous laugh, and glances back towards a monitor.  \"`&Sorry.  Listen to me going on about my problems like I don't have a Retraining Vessel to run.  It's just I only ever see people when something goes wrong, y'know?`0\"`n`nYou sense yourself edging over a precipice.  Unable to stop yourself, the words are out of your mouth before you've had a chance to consider what you're letting yourself in for.`n`n\"`#Well, drop me a line sometime when things `iaren't`i going wrong.`0\"`n`n`\$The Watcher`0 looks back up at you.  She shows a little hopeful grin, and rests her chin on her hand.  \"`&Yeah?`0\"`n`n\"`#Yeah,`0\" you say, feeling your future changing as you speak.  \"`#We'll go get a beer or something.`0\"`n`n`\$The Watcher`0's grin spreads wider.  \"`&Heaven knows I could use one.`0\"`n`nYou smile at each other for a moment.  `\$The Watcher`0's smile is warm, relieved, and joyful.  Your own smile is doing a really, really good job of emulating hers.`n`n\"`&The only way to get here is to be knocked out,`0\" says `\$The Watcher`0.  \"`&So I'll come and see you, instead.`0\"  You suspect she's thinking something like `iI made a friend!  We'll talk about monsters and shotguns and boots and everything!`i  \"`&I'll drop you a line, like you say.`0\"`n`n\"`#Great,`0\" you reply.  \"`#I'll look forward to it.`0\"`n`n\"`&I'm gonna let you get back to the mainland, now.  Thanks for talking to me.  Only...`0\"`n`n\"`#Yes?`0\"`n`n\"`&Sorry in advance for the gas.  I've got to keep up appearances.  Network might be watching.`0\"`n`nYou nod.  \"`#Okay.`0\"`n`n\"`&Try to look surprised.`0\"  She leaps out of her chair, slams her hands down on the desk, and ejects the vapour into your face with a sound like a hissing cat.  You collapse gently to the floor, wondering how the hell you've managed to make such a dangerous friend.");
			addnav("What have you gotten yourself into?");
			addnav("Return to Improbable Island","shades.php");
			$session['user']['resurrections']++;
			$session['user']['hitpoints'] = $session['user']['maxhitpoints'];
			$buffs = unserialize(get_module_pref("bufflist","alternativeresurrect"));
			if (is_array($buffs)){
				foreach ($buffs AS $buff=>$values){
					apply_buff($buff,$values);
				}
			}
			clear_module_pref("bufflist","alternativeresurrect");
			set_module_pref("plotpoint3",1);
			break;
	}
	page_footer();
}
?>