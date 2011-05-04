<?php

function strangerintro_getmoduleinfo(){
	$info = array(
		"name"=>"Stranger Intro",
		"version"=>"2009-07-04",
		"author"=>"Dan Hall",
		"category"=>"Quests",
		"download"=>"",
		"prefs"=>array(
			"Stranger Intro module prefs,title",
			"data"=>"Array of Stranger data,viewonly|a:0:{}",
		),
	);
	return $info;
}

function strangerintro_install(){
	module_addhook("forest");
	module_addhook_priority("prerender",100);
	return true;
}

function strangerintro_uninstall(){
	return true;
}

function strangerintro_dohook($hookname,$args){
	global $session,$strangebrightness,$strangelogo;
	switch($hookname){
		case "forest":
			if (get_module_pref("plotpoint3a","watcher_quests") && $session['user']['dragonkills'] >= 10 && $session['user']['hitpoints'] == $session['user']['maxhitpoints']){
				debug("Watcher ok, dks ok, hp ok");
				$data = unserialize(get_module_pref("data"));
				if (!$data['plotpoint1'] || !isset($data['plotpoint1'])){
					redirect("runmodule.php?module=strangerintro&step=1");
				}
			}
		break;
		case "prerender":
			if ($strangebrightness){
				global $output;
				$output = str_replace("sitecenter","sitecenter-".$strangebrightness,$output);
			}
			if ($strangelogo){
				global $output;
				$output = str_replace("<img src=\"templates/dragonleather/top-center-","<img src=\"templates/dragonleather/top-center-strange-",$output);
			}
		break;
	}
	return $args;
}

function strangerintro_run(){
	global $session;
	$data = unserialize(get_module_pref("data"));
	switch (httpget('step')){
		case 1:
			page_header("Something very strange...");
			output("You're barely past the hospital tent when someone comes to visit you.`n`n\"`7Hello.`0\"`n`nThe voice is male, and light, and sad.  You turn around, and your breath catches in your throat.`n`nHe's tall.  Black suit.  Black shirt.  Black tie.  Black bowler hat.  White cotton gloves.  He wears a long-nosed carnival mask, the eye pieces accented in gold trim, nothing but blackness behind the eye holes.  Underneath the mask, some sort of taut black fabric covers his face, save for a single horizontal crease where his mouth would be.`n`nYour head immediately begins to hurt, an icy dentist's-drill whirring behind your eyes.  The area around this strange man is darker than everywhere else.  \"`#Hello,`0\" you say back to him, trying not to cringe too much.`n`n\"`7What's your name?`0\" says the stranger, his voice dreamlike, as soft as a cloud.  When his mouth opens, you see that the black material extends inside his mouth, down his throat...`n`n\"`#%s,`0\"you say, the pain becoming hard to ignore.`n`nThe stranger notices.  He seems quite concerned.  \"`7What's the wrong matter?`0\"he asks, in his light, half-asleep tone.  \"`7Are you in hurt?`0\" He steps forward.`n`n\"`#No, I'm...`0\"your next words die before they make it to your tongue, and a gentle, casual half-scream rushes up your throat in their place.  \"`#Augh.`0\" Your teeth clench together, and with some effort, you part them, looking at the ground and backing up a little; it seems to help.  \"`#Just got a headache.`0\"`n`n\"`7That's awful,`0\"says the stranger.  You look up at him, and the pain redoubles.  He has no eyes that you can see, just the black cloth behind the mask.  You look back down again, and the pain edges off a little, but then you notice his shadow, the shadow that's `ifacing the wrong way`i, and that sense of `iwrongness`i pierces its drill behind your eyes.  \"`7Let me help you.`0\"`n`n\"`#`iNo!`i`0\" You take another step backwards, stumbling a little.  \"`#I mean... no, thank you.  I'm okay.`0\" You take a deep breath or two, and begin to almost believe yourself.  \"`#What's `iyour`i name?`0\"`n`nThe pointed nose lowers slowly towards the floor as the stranger looks down.  \"`7I don't know,`0\"he says, almost in a whisper.  \"`7I don't think doubt I ever had one.`0\"`n`nAlmost keeping your breathing under control, you notice the tears dripping from the stranger's eye sockets and running down the cheeks of his black carnival mask.  `iExcept they shouldn't be there.`i  If those tears came from his eyes, they should have soaked into the fabric behind the mask.`n`n\"`#Are you...`0\"you swallow, your vision blurring a little as you try to look the stranger in the eye.  \"`#Are you a Joker?`0\"`n`nThe stranger's mask flicks upwards, and he's staring at you again.  The horrifying quickness of the motion reminds you of the way sparrows move, their beaks flickering from point to point at speeds that would break a human's neck.  \"`7I don't think so,`0\"says the man.`n`n\"`#What `iare`i you?`0\"you breathe.`n`nThe man looks towards the sky.  \"`7I'm very stranger,`0\"he says quietly, his gloved fingers playing over each other.  \"`7I think I'm more strangerer than you.`0\"`n`nYou nod.`n`n\"`7If I had some friends then they would tell me what who I am.`0\" He takes a step towards you, and as he moves, you notice the jungle shifting around him - no, not shifting, just `idistorting`i, as though the speed of light was a little slower around him...`n`n\"`#Are you lonely?`0\"you ask with a cringe, as the pain intensifies.`n`n\"`7Oh yes,`0\"says the masked man, his head whicking back and forth in birdlike fits and starts.  \"`7So lonely.  I'm so, so lonely so.  Would you like to have friends with me?`0\" He takes another step forward.  \"`7Should I befriend you?`0\"`n`nYou feel a vague `ipop`i from somewhere inside, and something warm and thick runs from your nose, down your lips.`n`n",$session['user']['name']);
			rawoutput("<img src='templates/dragonleather/mainbg-darker.jpg' height='0' width='0'>");
			rawoutput("<img src='templates/dragonleather/mainbg-darkest.jpg' height='0' width='0'>");
			rawoutput("<img src='templates/dragonleather/top-center-strange-blue.png' height='0' width='0'>");
			rawoutput("<img src='templates/dragonleather/top-center-strange-gold.png' height='0' width='0'>");
			rawoutput("<img src='templates/dragonleather/top-center-strange-green.png' height='0' width='0'>");
			rawoutput("<img src='templates/dragonleather/top-center-strange-purple.png' height='0' width='0'>");
			rawoutput("<img src='templates/dragonleather/top-center-strange-red.png' height='0' width='0'>");
			rawoutput("<img src='templates/dragonleather/top-center-strange-turquoise.png' height='0' width='0'>");
			$data['plotpoint1'] = true;
			set_module_pref("data",serialize($data));
			$session['user']['hitpoints'] = $session['user']['maxhitpoints'] * 0.9;
			addnav("Keep him talking");
			addnav("I haven't met anyone I couldn't be friends with...","runmodule.php?module=strangerintro&step=2");
			addnav("Run");
			addnav("No!","runmodule.php?module=strangerintro&step=run");
		break;
		case 2:
			page_header("Something very wrong...");
			output("The black fabric creases up around the stranger's smile.  \"`7I'm so happy.`0\" He takes another step towards you and you force yourself to smile back.  The warm liquid runs into your mouth, and you wipe your bleeding nose with your hand.`n`n\"`#I'm glad,`0\"you wheeze, shocked at how weak your lungs have suddenly become.  \"`#Do you have any -`0\"something breaks and you stop mid-sentence, stare into the distance for a moment, utterly still.  \"`#Brothers or sisters?`0\"you finish, as the spinning gear inside your head catches and re-engages.`n`n\"`7I don't know,`0\"says the stranger.  \"`7Maybe I can find them,`0\"he whispers, leaning closer, just a few feet away from you now, your headache edging into the red.  \"`7Maybe you can help me to find them.  Maybe I'll `iask you`i to help me to find them.  I'll say please and thank you,`0\"he says quietly, his voice echoing in the center of your head.  \"`7I'll be very polite.  I'll be very gentle.  When I make friends to you.`0\"`n`nHe takes another step, his wrong-way shadow passing over yours, and the tension in your head gives way a little as you feel something trickle from your left ear down your neck.`n`n");
			$session['user']['hitpoints'] = $session['user']['maxhitpoints'] * 0.6;
			addnav("Keep him talking");
			addnav("That sounds like it might be a good adventure...","runmodule.php?module=strangerintro&step=3");
			addnav("Run");
			addnav("`iNo!`i","runmodule.php?module=strangerintro&step=run");
			global $strangelogo;
			$strangelogo = true;
		break;
		case 3:
			page_header("Something very bad...");
			output("The stranger claps his hands together, and cocks his head to the side.  \"`7Oh!  The joy, such joy.`0\"`n`n\"`#Where did you come from?`0\"you say through a mouthful of cotton, absently batting a hand up to the side of your head, smearing your ear's blood around your face.  Someone left an ice-cold throwing axe embedded between your left and right hemispheres, very rude, very discourteous.`n`n\"`7I'm starting to remember,`0\"says the stranger, his tone light and joyful.  \"`7Oh, it's all coming remember to me now!  I `idid`i used to be a Joker, once upon a time!`0\"`n`nYou try to smile, because he's so happy.  Instead, your knees buckle and you fold slowly to the floor.  \"`#I'm so happy for you,`0\"you croak, as your left arm and leg begin to tremble and jerk.  \"`#What happened to... turn... you from a Joker... into...`0\"`n`n`i`#I've never turned down friendship before on this Island.  I can be his friend.  I can be his friend.  He needs a friend.  He needs me so badly.  He wants to befriend me, so badly, it's hurting him, he's desperate.`0`i`n`n`i`#...and if it gets too bad, the hospital tent is `i`#right there,`i`# it's a five-second run, I can call for help...`0`i`n`n\"`#...into what you are now?`0\"`n`nHe moves closer, close enough that you could lunge and kick his feet out from under him and put your foot through his frightening nosey mask and stamp on his ribcage and feel the brittle birdlike bones splintering, the lungs bursting between your toes, and that thought would never ever even occur to you because he's going to be your friend, he's looming over your collapsed form getting ready to have friends with you, and you need a friend right now because your wrists and heels are drumming on the floor and you're starting to bleed from your eyes.`n`nWhen he speaks, he does so softly, quietly, and with infinite sadness.`n`n\"`7I was a bad Joker.`0\"`n`n");
			$session['user']['hitpoints'] = $session['user']['maxhitpoints'] * 0.4;
			addnav("Keep him talking");
			addnav("What happened to you?","runmodule.php?module=strangerintro&step=4");
			addnav("Run");
			addnav("`iNO!`i","runmodule.php?module=strangerintro&step=run");
			global $strangelogo;
			$strangelogo = true;
			global $strangebrightness;
			$strangebrightness = "darker";
		break;
		case 4:
			page_header("Something very dangerous...");
			output("The stranger thinks for a moment, looking at the trees and the sky as though he's never seen them before.  \"`7I think I died.  But I got better.`0\" He looks back at you, and freezing icepicks ram themselves under your eyelids.  \"`7It's good to not be dead.  But... I think I `iam`i still maybe a bit dead.  I can smell it.  I can smell myself being dead.  But it's okay,`0\"he whispers, kneeling down next to your twitching body.  \"`7It's good to still be walking around and making friends to people.  Like you.`0\"`n`nThe world is darkening and turning red.  The stranger's dark aura touches you, and it feels warm, prickly, makes your muscles jump and writhe.`n`nYou can't move.  Your breathing slows to a halt.  With the buzzsaw groping around in your skull, you can't even think.`n`n\"You're hurt,`0\"says the stranger, blank eye pits surveying the blood dripping from your eyes, nose and ears.  Grey and black spots are swimming through the Jungle, multiplying, bringing their friends.  \"`7I think you might be dead.  Yes, I think you might be.  All dead.  Let me have a smell, to make sure.`0\" You're blind.  He leans in to you, and at the moment the mask's nose brushes against your forehead, you feel the muscles twitch and shift to get away from it.  At the same moment, something odd happens in your chest - as though a background noise to which you'd become accustomed had suddenly cut off, leaving you in a black silence deeper than before.`n`nIt was your heart.`n`nYou hear him inhale.`n`nA pause.  \"`7Yes.  Yes, you're dead,`0\"he says, his voice sounding muffled and distant and sad.  \"`7It's horrible, isn't it?`0\"`n`nYou say nothing, because you're dead, the hospital tent just paces away, still hearing the conversations in the nearby Outpost.  The pain is going away - but it's not fading, it feels instead like it's being slowly `ieaten`i by some dreadful anaesthetic, and the grey spots are turning black.  Suddenly you want the pain back.  You want to be alive enough to feel it.`n`n\"`7I have some medicine,`0\"he says, and suddenly the pain rushes back.  \"`7Some medicine for your eyes.`0\" Slowly, through holes in the grey curtain, the stranger appears.  He's standing up, reaching inside his suit jacket - he's taken a couple of steps back, and you feel a lurch in your chest as your heart kicks in again.`n`nEverything is high-contrast, almost blinding.  Against the blackness of his suit, you see him pull out a white hypodermic needle.`n`n\"`7Hold still,`0\"he says, and kneels down again, bringing the pain back.  \"`7Hold very still indeed.`0\" You can't look away - you can't shut your eyes.  The hypodermic is filling the field of view of your right eye, but you can still see the stranger's arms, and in his absent-mindedness he's bent his elbows back the wrong way.  \"`7Being still will be easy for you.  You're dead.  But when you're alive again, hold very still please.`0\" A droplet of something black and oily drips from the point of the needle, now just millimetres away, and falls into the very centre of your pupil.  In your unblinking right eye, the world darkens.`n`nThe point is too close to focus on.  Close enough for your eyeball to feel its coldness.  The stranger is close enough for you to smell him, and he smells of soot and filthy black oilsmoke.  \"`7Sshh, be very still...  This won't hurt.`0\" `n`nYour heart slows down to a whisper again, but you've got more pressing matters on your mind.  The point of the needle touches your cornea, and very gently builds the thinnest, most concentrated point of pressure before penetrating, slipping cold inside you with a `iplup`iping sensation that seems to echo in your skull.  You feel the stranger's needle hesitate a moment, he's being so gentle, so polite, and then he slithers the point to something deeper `iinside`i which gives a little resistance before breaking with an even deeper `ipop`i, and then he presses the plunger.  Coldness rushes into your right eye socket, followed by pressure.`n`nThe pain evaporates.  The stranger withdraws his needle; your pliable cornea holds onto it for a moment before letting go.  \"`7There,`0\"he breathes, standing up.  \"`7That wasn't so bad, was it?`0\"`n`nNeedles pierce and prickle every nerve in your body as sensation returns.  You almost feel like you can move.  Your lungs ache with pleasure as you take in your first breath since the stranger knelt next to you.  Your legs are cold and wet, and you smell urine.`n`n\"`7I expect you feel `imuch`i better after that,`0\"he says, running the needle casually into his mouth and sucking on the tip like a lollipop.  `n`nYou get to your feet.  Suddenly the only thing that hurts any more is your right eye, and you wink it shut.  Behind your eyelid, it feels wet.`n`n\"`7Are we friends now?`0\"asks the stranger.  \"`7Now that my stuff is inside?`0\"`n`nYou turn and run.  You get a good ten paces before you fall, the world spinning around you.  The stranger is right behind you.`n`n\"`7Why do you run?`0\"he asks.  \"`7It doesn't hurt any more, does it?`0\"`n`nNo.  Being around this abhorrent thing doesn't hurt, now.  That seems somehow `iworse`i, as though he's stripped you of your `iright`i to be repulsed.  You feel degraded, tainted, unclean - nothing that's good and wholesome should be able to withstand being around this stranger.`n`n\"`#I can't be friends with you,`0\"you say, as loudly as you can manage - barely a squeak.  \"`#I'm sorry, but I just `ican't`i.`0\"`n`n\"`7Then what will I do?`0\"breathes the stranger, and you look behind you, see his wrong-way shadow behind his black-suited legs, his smart shoes.  \"`7I'm sorry I tricked you, my friend.  But if you're to be my friend, then you have to not hurt around me, my friend, don't you friend?`0\"`n`nYou pull up your foot and piston it out, and the stranger's right kneecap reverses upon impact.  \"`#`iHELP!`i`0\"you cry to the hospital tent, now only feet away.  \"`#`iFUCKING HELP ME!`i`0\" That this should happen to you, so close to the Outpost...  A chill comes over you as you realise that nowhere is safe from the stranger, and something `ithat belongs to him`i is inside you, close to your brain, probably slithering around your memories right now.`n`nThe stranger looks down at his knee, bent the wrong way.  He shifts his weight, and it rebends to the correct position with a sickeningly wet `icrunch`i.  \"`7It's okay,`0\"he says, \"`7You're my friend and I still love you.  It doesn't really hurt.`0\"`n`nYou roll onto your back, look up at the stranger as he leans down to you, and you can see his eyes now, dull orange coals behind his mask.  No pupils, just burning ashes.`n`n\"`7Should I take you to my house?`0\"`n`nYou set your weight on your shoulders and mid-back, and jab both feet into his face.`n`nThe mask cracks.  The stranger screams, a scream like a mirror in a cement mixer, a scream like a cat turned inside-out, a scream like a nuclear bomb whistling through the air, and he folds down to his knees, `ibackwards`i - forgetting in his pain that knees are supposed to bend the other way.  Dark red blood runs from the cracked mask, black smoke rises.`n`nYou scramble to your feet and run, hitting the hospital tent with your shoulder, collapsing onto the floor, and losing consciousness.`n`n");
			$session['user']['hitpoints'] = 0;
			addnav("What happens next?");
			addnav("Continue","runmodule.php?module=strangerintro&step=5");
			global $strangelogo;
			$strangelogo = true;
			global $strangebrightness;
			$strangebrightness = "darkest";
		break;
		case 5:
			page_header("Hospital Tent");
			output("You wake up to the smell of disinfectant, and the sight of a somewhat grizzled-looking heavyset man leaning over you.  You recognise him after a moment as the medic.`n`n\"`6Woken up at last, eh?  So, sleepin' beauty, whose is all this blood then?`0\"`n`nYou frown, testing each body part for pain.  You feel fine, apart from a prickling sensation in your right eye.`n`n\"`#It's mine,`0\"you mumble.  \"`#Came out of me when that bloke came too close.`0\"`n`nThe medic looks up; you trace his gaze to a younger man, wiping his hands on a rag and shaking his head.  The medic looks back down again, locks eyes with you.  \"`6The truth, now, if ye'd be so kind.  Nowt short of a massive 'emhorrage coulda done this to ya, and the scans din't pick up `inothing.`i  Also, `iwhat`i bloke?`0\"`n`n\"`#I `iam`i telling you the truth,`0\" you say with a little more force, propping yourself up on your elbows - \"`#I was bleeding from my ears!  And my eyes!  Every fucking hole in my head was `igushing`i!`0\"`n`n\"`6Bollocks,`0\"says the medic.  \"`6There's not a physical thing wrong wi' yer but a poke in the eye.  I set some rapid healin' gel on it, by the way.  It's gonna itch like an absolute `ibastard`i for few hours, but other than that yer eye'll be fine.  Oh, an' there was `ione`i more thing.`0\"`n`n\"`#And what's that?`0\"you ask, quickly becoming exasperated.`n`n\"`6Actually, there's `iseveral`i more things,`0\"says the medic, not at all kindly.  \"`6Firstly, I got a call from `$'er on the Boat`6 while you were out.  Seems a few of 'er cameras went wonky while she was watchin' you, and she's `inot`i happy about it.`0\"`n`nYou blink.  \"`#Wonky?`0\"`n`n\"`6She din't go into specifics, but I reckon she'll probably wanna talk to you about it at some point when she's not so busy.  Yer can expect a good hidin' from her when you least expect it.`0\"`n`n\"`#Damn it!`0\" Your fingers curl into fists.  \"`#So she didn't see what happened?`0\"`n`n\"`6She just told me her cameras had gone on the blink, don't shoot the messenger.  The other thing...`0\"`n`n\"`#What the hell is it `inow`i?`0\"`n`nHe looks up briefly at the assistant, then back down to you.  \"`6Whatever the bloody hell you were doin' out there, it gave yer a creepin' dose.  You've 'ad radiation exposure.`0\"`n`nYou stare at the man.  \"`#How `imuch`i radiation?`0\"`n`n\"`6Enough that you should make damn sure you keep yer cancer jabs up to date, mate.  Also, yer probably gonna lose a bit o' hair.  Oh, an' you can expect 'eadaches, fevers, blood in yer shit, pukin', bleedin' under yer skin in ugly purple blotches, all o' that fun stuff.  I stuck yer full o' counters while you were under, so the bad stuff shouldn't last more'n a couple o' days.  Once the numbin' agent wears off, you'll probably feel the injection sites - there were about sixty of 'em.  You looked like a bloody porcupine for a while, there.  An' I don't know if you've studied the effects o' radiation and Improbability when they get mixed together, but...`0\"`n`nYou bury your face in your hands.  \"`#So I'm hot.`0\"`n`n\"`6Yeah.  Yer' `iemitting.`i  If you've got a special someone, someone who's gonna have regular exposure to ya -`0\"you cringe at the word `iexposure`i, the sense that you're now a dangerous thing to be around - \"`6you might wanna sleep apart from 'em for now.  Don't go cuddlin' 'em too much, kind o' thing.`0\"`n`nYou feel a lump in your throat.  \"`#Damn it,`0\"you croak.  \"`#I was just trying to make friends with the bloody thing, it seemed so lonely...`0\"`n`n\"`6Some dangerous fuckin' friends you've got, pal!`0\"says the medic.  \"`6Now get on yer way.  We need this bed.`0\"`n`nSlowly, you get to your feet, deciding that if you ever see that black-suited man again, you'll have to either run like hell or put him on the ground.`n`n\"`6Oh, and mate,`0\"says the medic, as you wobble towards the tent flap.  You turn around.`n`n\"`6Whatever,`0\"he says slowly, jabbing you in the ribs with a finger to punctuate his words, \"`6the `ifuck`i, you were `idoing`i out there, `inever, ever, ever`i do it again.  I don't wanna be the one who 'as to sort you out while I'm wearin' a lead plate over me bollocks.  You understand?`0\"`n`nYou nod wearily, and step out of the tent.`n`nThe blood from your massive hemorrage - which apparently didn't happen - lies crusting the grass, just between those two trees.  This thing, this `icontamination,`i happened practically right outside the Outpost.`n`nYou shiver, and head back towards the sounds of people.`n`n");
			$session['user']['hitpoints'] = $session['user']['maxhitpoints'];
			addnav("Try to forget that happened.");
			addnav("Continue into the Outpost, where it's safer...","village.php");
			$data['plotpoint1'] = true;
			$data['plotpoint2'] = true;
			set_module_pref("data",serialize($data));
		break;
		case "run":
			page_header("Run like hell");
			output("\"`#I'm sorry, I can't be friends with you,`0\"you say.  \"`#I just `ican't`i.`0\"`n`nThe stranger raises his hands, palms outward in what he surely hopes is a non-threatening gesture - and it would be, were his thumbs not pointing outwards instead of in.`n`nYou turn and run, leaving the awful thing behind you.`n`n");
			addnav("Try to forget that happened.");
			addnav("Continue into the Outpost, where it's safer...","village.php");
		break;
	}
	page_footer();
}
?>