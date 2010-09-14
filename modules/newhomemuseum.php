<?php

require_once("lib/villagenav.php");
require_once("lib/http.php");

function newhomemuseum_getmoduleinfo(){
	$info = array(
		"name"=>"Newhome Museum",
		"version"=>"2008-12-03",
		"author"=>"Dan Hall",
		"category"=>"Improbable",
		"download"=>"",
		"prefs"=>array(
			"Newhome Museum module prefs,title",
			"exhibitsseen"=>"How many exhibits has the player looked at?,int|0",
			"seendiary1"=>"Has the player seen Diary Entry 1?,int|0",
			"seendiary2"=>"Has the player seen Diary Entry 2?,int|0",
			"seendiary3"=>"Has the player seen Diary Entry 3?,int|0",
			"seendiary4"=>"Has the player seen Diary Entry 4?,int|0",
			"seendiary5"=>"Has the player seen Diary Entry 5?,int|0",
			"seendiary6"=>"Has the player seen Diary Entry 6?,int|0",
			"seenmemo1"=>"Has the player seen Memo 1?,int|0",
			"seenmemo2"=>"Has the player seen Memo 2?,int|0",
			"seenmemo3"=>"Has the player seen Memo 3?,int|0",
			"seenrace1"=>"Has the player seen Race 1?,int|0",
			"seenrace2"=>"Has the player seen Race 2?,int|0",
			"seenrace3"=>"Has the player seen Race 3?,int|0",
			"seenrace4"=>"Has the player seen Race 4?,int|0",
			"seenrace5"=>"Has the player seen Race 5?,int|0",
			"seenrace6"=>"Has the player seen Race 6?,int|0",
			"seenrace7"=>"Has the player seen Race 7?,int|0",
			"seenrace8"=>"Has the player seen Race 8?,int|0",
			"seenrelic1"=>"Has the player seen Relic 1?,int|0",
			"seenrelic2"=>"Has the player seen Relic 2?,int|0",
			"seenrevo1"=>"Has the player seen Revolution 1?,int|0",
		),
	);
	return $info;
}
function newhomemuseum_install(){
	module_addhook("village");
	return true;
}
function newhomemuseum_uninstall(){
	return true;
}
function newhomemuseum_dohook($hookname,$args){
	global $session;
	switch($hookname){
	case "village":
		if ($session['user']['location'] == "NewHome") {
			tlschema($args['schemas']['fightnav']);
			addnav($args['fightnav']);
			tlschema();
			addnav("NewHome Museum","runmodule.php?module=newhomemuseum&op=lobby");
		}
		break;
	}
	return $args;
}
//Exhibits to include:
//Main hall:
//David Abraham's diary entries from the beginning of the game
//Doktor Improbable's diary entries
//Hall of Diversity:
//Dummies of zombies, kittymorphs, robots, mutants, midgets, gobots, Jokers and humans.
//Hall of Relics:
//Trick-Or-Treat Kid, who was once a prominent feature of NewHome until he was shot in the head for being too sodding annoying.
//Hall of Revolutions:
//An actual, honest-to-God computer from the Island
//A quick explanation of what happened when the EMP went off, and why we found the Island again
function newhomemuseum_run(){
	global $session;
	page_header("NewHome Museum");
	switch (httpget("op")){
		case "lobby":
			$exhibitsseen = get_module_pref("exhibitsseen");
			debug($exhibitsseen);
			output("You spy a house-sized hut with the sign \"NewHome Museum of Improbable Things\" proudly displayed above the door.  You decide to head inside and have a look around.`n`n");
			output("It's refreshingly cool inside the hut, although it does smell rather dusty.  Your eyes take a moment to adjust to the gloom.`n`n");
			modulehook("newhomemuseum");
			output("A sign hangs in the lobby, pointing out what lies through each of the doors leading off from the small room.`n`n");
			addnav("Museum Halls");
			addnav("Head for the Main Hall","runmodule.php?module=newhomemuseum&op=mainhall");
			addnav("Head for the Hall of Diversity","runmodule.php?module=newhomemuseum&op=racehall");
			addnav("Head for the Hall of Relics","runmodule.php?module=newhomemuseum&op=relichall");
			addnav("Head for the Hall of Revolutions","runmodule.php?module=newhomemuseum&op=revolutionhall");
			break;
		case "mainhall":
			if (httpget("examine")==""){
				output("You step into the Main Hall.  For something called a \"Main Hall,\" you expected something a little larger.  This is barely bigger than your living room back home.`n`nTo your left, you can see a series of windowed indentations in the wall, with crinkly, yellowed pieces of paper sitting inside.  To your right you see a similar array of windows, this time with far less scruffy-looking papers inside.");
				addnav("Left Side");
				addnav("Examine the first window","runmodule.php?module=newhomemuseum&op=mainhall&examine=diary1");
				addnav("Examine the second window","runmodule.php?module=newhomemuseum&op=mainhall&examine=diary2");
				addnav("Examine the third window","runmodule.php?module=newhomemuseum&op=mainhall&examine=diary3");
				addnav("Examine the fourth window","runmodule.php?module=newhomemuseum&op=mainhall&examine=diary4");
				addnav("Examine the fifth window","runmodule.php?module=newhomemuseum&op=mainhall&examine=diary5");
				addnav("Examine the sixth window","runmodule.php?module=newhomemuseum&op=mainhall&examine=diary6");
				addnav("Right Side");
				addnav("Examine the first window","runmodule.php?module=newhomemuseum&op=mainhall&examine=memo1");
				addnav("Examine the second window","runmodule.php?module=newhomemuseum&op=mainhall&examine=memo2");
				addnav("Examine the third window","runmodule.php?module=newhomemuseum&op=mainhall&examine=memo3");
			}
			switch (httpget("examine")){
				case "diary1":
					output("A plaque below the window informs you that this diary entry is taken from the journal of David Abraham, assistant to Doktor Improbable.`n`n`n");
					output("`Q11th February, 2072`n`nAfter a hell of a flight, I finally arrived on Island Four this morning! Professor Hawton - Joseph, he insisted - is a massive, wobbling pudding of a man, nothing like his photos. He's the stereotype of a jolly fat bloke, with a crushing handshake and a hearty laugh. I took a liking to him instantly.`n`nAfter a lunch of roast boar and home-brewed real ale - which was delicious, by the way - he showed me his progress on the Improbability Drive, showing off the enhancements and modifications he's created, pointing out the deviations from the original plan, all of which met with my enthusiastic approval.`n`nThe Improbability Drive has the potential to eliminate all of mankind's margins of error. Imagine what we could do with Absolute Certainty! If we could inflate the slimmest chance to the highest mathematical probability! Professor Hawton - Joseph, sorry - highlighted some of the things I hadn't even thought of... what if our soldiers never missed their targets? What if we could easily affect the rate at which cancer cells divide and subdivide? What if we could determine, for ourselves, the chances of being struck by lightning, or hit by a bus, or dying of AIDS?`n`nJoseph seems a little eccentric, but a terribly nice chap - he has been here for twenty years on his own, so it's only natural he should be a tad \"touched.\" I'm sure he'll regain his senses - that's what I'm here for, after all, to keep him company and stop him from going completely off the deep end.`n`nI think this could be the start of a beautiful friendship!");
					addnav("Continue");
					addnav("Back to the Main Hall","runmodule.php?module=newhomemuseum&op=mainhall");
					addnav("Move on to the next diary entry","runmodule.php?module=newhomemuseum&op=mainhall&examine=diary2");
					if (get_module_pref("seendiary1")==0){
						set_module_pref("seendiary1",1);
						increment_module_pref("exhibitsseen",1);
					}
					break;
				case "diary2":
					output("A plaque below the window informs you that this diary entry is taken from the journal of David Abraham, assistant to Doktor Improbable.`n`n`n");
					output("`Q12th February, 2072`n`nJoseph walked in on me while I was having a shower, stripped off his clothes, and jumped in. The presence of his ample frame didn't give me enough room to stand up straight in the shower, and I was constantly buffeted by the folds and valleys of his soapy belly. I found it all a little odd, but what really struck me was that he left his socks on. When I quizzed him about it, he told me that his socks were vital to the Plan. `n`n\"What plan,\" I asked him. `n`n\"PlanplanplanPLANplanplanplan,\" he laughed, enthusiastically rubbing his belly, and then he grabbed my head and rubbed it between his soapy man-boobs.`n`nYup, he's nuts.");
					addnav("Continue");
					addnav("Back to the Main Hall","runmodule.php?module=newhomemuseum&op=mainhall");
					addnav("Move on to the next diary entry","runmodule.php?module=newhomemuseum&op=mainhall&examine=diary3");
					if (get_module_pref("seendiary2")==0){
						set_module_pref("seendiary2",1);
						increment_module_pref("exhibitsseen",1);
					}					
					break;
				case "diary3":
					output("A plaque below the window informs you that this diary entry is taken from the journal of David Abraham, assistant to Doktor Improbable.`n`n`n");
					output("`Q14th February, 2072`n`nJoseph gave me a Valentine's Day card. He'd drawn it himself, in crayon, and signed it \"Doktor Improbable.\" I asked him about the name - he told me he spelt Doktor with a K because he wasn't actually, strictly speaking, a real doctor. Fantastic.`n`nWe rolled dice and flipped coins for the rest of the day, while Joseph fine-tuned the Improbability Drive.");
					addnav("Continue");
					addnav("Back to the Main Hall","runmodule.php?module=newhomemuseum&op=mainhall");
					addnav("Move on to the next diary entry","runmodule.php?module=newhomemuseum&op=mainhall&examine=diary4");
					if (get_module_pref("seendiary3")==0){
						set_module_pref("seendiary3",1);
						increment_module_pref("exhibitsseen",1);
					}
					break;
				case "diary4":
					output("A plaque below the window informs you that this diary entry is taken from the journal of David Abraham, assistant to Doktor Improbable.`n`n`n");
					output("`Q20th February, 2072`n`nI have a feeling we're getting closer. We rolled ten dice this morning, and got ten sixes. After subsequent rolls, we got ten sixes again, then nine sixes and a five, then ten ones, then nine ones and a six, and then five sixes and five ones. They were all supposed to be sets of ten sixes, but it's plain to see that we're making progress.");
					addnav("Continue");
					addnav("Back to the Main Hall","runmodule.php?module=newhomemuseum&op=mainhall");
					addnav("Move on to the next diary entry","runmodule.php?module=newhomemuseum&op=mainhall&examine=diary5");
					if (get_module_pref("seendiary4")==0){
						set_module_pref("seendiary4",1);
						increment_module_pref("exhibitsseen",1);
					}
					break;
				case "diary5":
					output("A plaque below the window informs you that this diary entry is taken from the journal of David Abraham, assistant to Doktor Improbable.`n`n`n");
					output("`Q1st March, 2072`n`nI think we've cracked it! Ten rolls, of ten dice, and sixes across the board!`n`nJoseph, for some reason, isn't happy with the result! He frowned, muttered that he'd have to fine-tune it a little more, and then went back to the computers.`n`nI have no idea what more he'd want to do with this. We've done the basic experiments as an illustration of principle, and that's all we were supposed to do.");
					addnav("Continue");
					addnav("Back to the Main Hall","runmodule.php?module=newhomemuseum&op=mainhall");
					addnav("Move on to the next diary entry","runmodule.php?module=newhomemuseum&op=mainhall&examine=diary6");
					if (get_module_pref("seendiary5")==0){
						set_module_pref("seendiary5",1);
						increment_module_pref("exhibitsseen",1);
					}
					break;
				case "diary6":
					output("A plaque below the window informs you that this diary entry is taken from the journal of David Abraham, assistant to Doktor Improbable.`n`n`n");
					output("`Q7th March, 2072`n`nI'm going to leave this journal where you can find it. I don't have much time. It's all gone wrong. We threw ten dice, which came up nine sixes and a seven, and then five zeroes and five nines, and then ten ones and a Z, and then three parrots, five monkeys, seven cakes and the Ace of Spades. Joseph was laughing and wobbling like a madman the entire time.`n`nImprobability is leaking out into the island like radiation, and everything's going very strange. If you read this, GET OFF THE ISLAND - and if you survive without being turned into something, give my love to my wife, and tell the world that Doktor Improbable is coming.`n`nI don't know what you, as outsiders, can do about this place. If you try to nuke it, then the missile will probably change into a sperm whale or something. If you send soldiers, their weapons might backfire, or not work at all, or turn into bananas. I guess you could make some sick Reality TV show where you launch unarmed people indiscriminately at the Island and see whether or not they survive - note, THIS IS A JOKE, DON'T ACTUALLY DO THIS.`n`nDoktor Improbable, and the Improbability Drive, aren't your only problems. There are other things here, too - some are nice, some not so nice, all insane.`n`nThis is David Abraham, signing off - and I apologise for my part in all this. Forgive me. I knew not what I did.`n`nIf you read this, get away. Get away FAST.");
					addnav("Continue");
					addnav("Back to the Main Hall","runmodule.php?module=newhomemuseum&op=mainhall");
					if (get_module_pref("seendiary6")==0){
						set_module_pref("seendiary6",1);
						increment_module_pref("exhibitsseen",1);
					}
					break;
				case "memo1":
					output("A plaque below the window informs you that this is an internal memo sent from Professor Woodcock of Oxford University.`n`n`n");
					output("`1 14th September, 2074`n`nTo Whom it May Concern;`n`nWhile browsing through some old correspondance in a somewhat vain attempt to organise my predecessor's filing system, I came across a most interesting letter.  I'll keep this brief, as I know you'd like me to get straight to the point - it appears as though there may still be at least one functional computer left in the world.  Further, it may even belong to us.`n`nFrom what I managed to gather from various old letters and memos, we apparently dispatched some poor sap to an island in the middle of nowhere in the late 50's, to work on some sort of quantum-mechanics experiment.  From the letters, he seems a rather eccentric and boisterous sort, which is probably why we offered him this \"opportunity\" in the first place.  Anyway, the poor wretch never came back, and it seems that someone else was going through some old letters as I do now, as there are some more memos sent - like this one - to the department head, asking whatever happened to the man.  Then - please see enclosed - it appears we sent another promising yet disappointingly intolerable young man after Professor Hawton, to keep him company.`n`nNow, I don't know whether these documents were forgotten on purpose or as part of some greater beauracratic gaffe, but it seems to me that the island in question might just be remote enough for our equipment to have been unaffected by the EMP bombs detonated this spring.`n`nGentlemen, I propose that we set sail immediately to Island Four, and see for ourselves whether we now own the last functional computers in the world.  Please meet me in the pub this evening to discuss.`n`nYours faithfully,`n`nPercival Woodcock, B.S.C.");
					addnav("Continue");
					addnav("Move on to the next letter","runmodule.php?module=newhomemuseum&op=mainhall&examine=memo2");
					addnav("Back to the Main Hall","runmodule.php?module=newhomemuseum&op=mainhall");
					if (get_module_pref("seenmemo1")==0){
						set_module_pref("seenmemo1",1);
						increment_module_pref("exhibitsseen",1);
					}
					break;
				case "memo2":
					output("A plaque below the window informs you that this is a letter sent from Professor Kempston of Oxford University, to the British Foreign and Commonwealth Office, carbon-copied to the Ministry of Defence.`n`n`n");
					output("`1 11th June, 2075`n`nDear Sir or Madam,`n`nI hope this letter finds you well.  My name is professor Peter Kempston and I am writing on behalf of the quantum physics department of Oxford University.`n`nWe find ourselves in a most unusual predicament.  Over the course of the past twenty-odd years, we've sent two expeditions to a particular island (please see enclosed map for exact co-ordinates.)  We considered calling the local police when our second expedition failed to come back, but we felt that this remote place may well be beyond their baliwick, and decided to take the matter higher.  I'm ashamed to say we simply forgot about the first expedition.`n`nThe enclosed letters and internal memos will, I hope, explain the matter far more succinctly than I could.`n`nPlease respond by letter or carrier pigeon as soon as you can.`n`nYours sincerely,`n`nProfessor P. Kempston, B.S.C.");
					addnav("Continue");
					addnav("Move on to the next letter","runmodule.php?module=newhomemuseum&op=mainhall&examine=memo3");
					addnav("Back to the Main Hall","runmodule.php?module=newhomemuseum&op=mainhall");
					if (get_module_pref("seenmemo2")==0){
						set_module_pref("seenmemo2",1);
						increment_module_pref("exhibitsseen",1);
					}
					break;
				case "memo3":
					output("A plaque below the window informs you that this is a transcript of a radio conversation between a helicopter pilot and base.  The name of the pilot, and date of the transmission, are lost.`n`nIt goes on to explain, in boring detail, about how the radio and helicopter worked in a post-EMP world.  You give it a quick scan and get the gist of it - the squadron went equipped with the best hardware that the National Army Museum in Chelsea could provide, including a fly-by-wire helicoper, valve radio, optical binoculars, mechanical rifles and so on.  You feel briefly sorry for the poor bastards.`n`n`n");
					output("`4PILOT: The rotors.  Oh my God, the rotors are.  The rotors are flying away.`nBASE: Repeat please.`nPILOT: The rotors are going away from the helicopter.  They are flying away.`nBASE: Do you mean the rotors are broken?  Which ones?`nPILOT: No, they have flown away.  Little wings.`nBASE: Give up command of the helicopter.  Have someone take over.  Fit your gas mask, sit down and breathe.  Confirm.`nPILOT: Little wings.`nSOUND OF RUSHING AIR.  MALE SCREAMS.  MALE LAUGHTER.  QUACKING DUCK.`nEND.");
					addnav("Continue");
					addnav("Back to the Main Hall","runmodule.php?module=newhomemuseum&op=mainhall");
					if (get_module_pref("seenmemo3")==0){
						set_module_pref("seenmemo3",1);
						increment_module_pref("exhibitsseen",1);
					}
					break;
			}
			break;
		case "racehall":
			if (httpget("examine")==""){
				output("You step into the Hall of Diversity.  An array of lifelike mannequins stand before you, depicting various strange-looking humanoid creatures.");
				addnav("Examine mannequins");
				addnav("Examine the human-looking mannequin","runmodule.php?module=newhomemuseum&op=racehall&examine=human");
				addnav("Examine the unhealthy-looking mannequin","runmodule.php?module=newhomemuseum&op=racehall&examine=zombie");
				addnav("Examine the furry mannequin","runmodule.php?module=newhomemuseum&op=racehall&examine=kittymorph");
				addnav("Examine the short mannequin","runmodule.php?module=newhomemuseum&op=racehall&examine=midget");
				addnav("Examine the mannequin with extra limbs","runmodule.php?module=newhomemuseum&op=racehall&examine=mutant");
				addnav("Examine the metal and glass mannequin","runmodule.php?module=newhomemuseum&op=racehall&examine=robot");
				addnav("Examine the mannequin with tyres where its legs should be","runmodule.php?module=newhomemuseum&op=racehall&examine=gobot");
				addnav("Examine the very well-dressed mannequin","runmodule.php?module=newhomemuseum&op=racehall&examine=joker");
			}
			switch (httpget("examine")){
				case "human":
					output("The mannequin depicts a determined-looking man in his mid to late twenties, bearing healthy stubble and a cocky grin.  Biceps seem to strain against the confines of his camouflaged jacket, his rather large pistol aimed towards the shorter mannequin.  Despite being a wax-and-resin dummy, he seems to ooze testosterone.`n`nStanding in front of him, you feel a little inadequate.`n`n`n");
					output("A sign at his feet reads:`n`nHUMAN`nThe brave, determined heroes of the War on Improbability.`n`nSomeone has written in pencil underneath the sign:`nNearly all of them so new they're still wet behind the ears.  Cocky and overconfident.  Beware.");
					addnav("Continue");
					addnav("Back to the Hall of Diversity","runmodule.php?module=newhomemuseum&op=racehall");
					if (get_module_pref("seenrace1")==0){
						set_module_pref("seenrace1",1);
						increment_module_pref("exhibitsseen",1);
					}
					break;
				case "zombie":
					output("The mannequin depicts a gaunt man with green, rotting skin, wearing an army jacket smeared with blood.  His arms are upraised and he appears to be taking a step forward, as though shambling drunkenly towards you for a hug.`n`n`n");
					output("A sign at his feet reads:`n`nZOMBIE`nIt is believed that these poor creatures have been infected with the results of one of Doktor Improbable's other experiments.  Common traits include a shambling gait, lack of social sophistication and table manners, unpleasant odour, involuntary use of the word 'brains' and frequent vomiting-up of their own entrails.`nUnable to feel pain or exhaustion, Zombies can take a lot of punishment in combat over a long period of time.  However, their slowness and general clumsiness means that they cannot attack as effectively.  Their keen eye for shiny objects means that they always seem to have slightly more wealth than other races.`n`nSomeone has written in rusty brown ink - at least, you hope it's rusty brown ink and not dried blood- underneath the sign:`nBRAAAAAAAAAAAAAAAAAAAAIIIIIIIIIIIIIINNNNNNNNNNS.  Kilroy was 'ere.");
					addnav("Continue");
					addnav("Back to the Hall of Diversity","runmodule.php?module=newhomemuseum&op=racehall");
					if (get_module_pref("seenrace2")==0){
						set_module_pref("seenrace2",1);
						increment_module_pref("exhibitsseen",1);
					}
					break;
				case "kittymorph":
					output("The mannequin depicts an obviously - perhaps you could even say 'abundantly' - female humanoid creature, covered from head to toe in thick, sandy fur.  Her ears are pointy and sit on the top of her head, her nose is a little smaller and her eyes a little larger than would be considered natural, and she bears a tail that reaches to the back of her knees.  She has one knee raised as though running and appears to be in the process of disrobing as she does so, showing a considerable amount of bare fur and a wide-eyed, toothy grin.`n`n`n");
					output("A sign at her feet reads:`n`nKITTYMORPH`nNobody knows exactly how these creatures came to exist on the Island.  They appear to be a genetic mutation of sorts.  Common traits include an easygoing attitude to life, an almost complete lack of modesty, comfort in laziness and a tendency to become distracted easily.`nThese creatures' toned and slender bodies cannot cope with heavy blows in combat, but do allow for fast counterattacks and an ability to travel further in a day than most races.  However, Kittymorphs are generally disinclined towards prolonged combat, and become distracted easily.`n`nSomeone has added in pencil:`nShameless exhibitionists.  Bloody unstable personalities too.  Lazy buggers.  Usually list \"carnivore\" as their job titles, despite giving the appearance of being a bunch of vegan hippy types.  Beware.");
					addnav("Continue");
					addnav("Back to the Hall of Diversity","runmodule.php?module=newhomemuseum&op=racehall");
					if (get_module_pref("seenrace3")==0){
						set_module_pref("seenrace3",1);
						increment_module_pref("exhibitsseen",1);
					}
					break;
				case "midget":
					output("You look down at the mannequin before you.  Its head reaches to just below your bellybutton.  You almost mistook it for a child, until you saw the five-o'clock shadow and can of White Lightning cider clutched in one tiny hand.  It wears a filthy grey T-shirt with sweat rings around the armpits and tomato sauce stains down the front.  It sneers up at you, cider can in one hand, the other raised in your direction, middle finger outstretched.`n`n`n");
					output("A sign at his feet reads:`n`nMIDGET`nThese creatures were not born this way, but were in fact changed to their current forms via an overdose of a particular strain of Improbability.  It is important to show real midgets the same respect you would show any other human being, and this can cause awkward situations sometimes - to tell a midget apart from a Midget, simply ask them if they have the time.  If they respond with a belch and a kick to your crotch and then rob you, then they're a Midget, not a midget.`nCommon traits include laziness, foul odour, hygiene problems, lack of communication skills, lack of financial skills, lack of any sort of skills, and extreme violence.  It is often difficult to tell male Midgets from female Midgets, due to the facial hair on both genders.  Midgets of either gender should be considered extremely dangerous.`nMidgets excel in all areas of combat.  Their small size makes them both difficult to hit and likely to fly into a blinding rage at any moment.  However, their stubby little legs give them a notable disadvantage in travelling, and also ensure that they cannot fight as many foes over a long period.`n`nSomeone has added in pencil:`nfuk u! dat dummy maks me luk short! u fukin dik!`n`nSomeone else has added:`nQuiet, you, or I'll step on you.");
					addnav("Continue");
					addnav("Back to the Hall of Diversity","runmodule.php?module=newhomemuseum&op=racehall");
					if (get_module_pref("seenrace4")==0){
						set_module_pref("seenrace4",1);
						increment_module_pref("exhibitsseen",1);
					}
					break;
				case "mutant":
					output("The mannequin depicts a sad-faced man with three extra arms, all of varying sizes.  An extra miniature head grows from his left shoulder, and seems to be a little happier - that may be because it's depicted biting the main head's ear.`n`n`n");
					output("A sign at his feet reads:`n`nMUTANT`nThese contestants were understood to be human at one point, but have been exposed to significant amounts of Improbability, causing these mutations.  Every Mutant is different, and may be endowed with extra appendages of any description.  Mutants tend to be rather sensitive about their condition, and will often take time out of their day to educate other contestants on the challenges of being Mutant.`nIn combat, Mutants are formidable opponents.  Their typically leathery hide allows them to take a lot of damage, while their extra appendages can be used to strike or parry.  However, the relative newness of their mutated bodies, combined with the residual mental image of their former selves, makes it hard for a Mutant to learn from their mistakes and successes.  As a result, they will need to fight more than other races in order to achieve similar skill.`n`nSomeone has written below the sign in pencil:`nDon't let one of these whiny tosspots corner you.  They'll talk your bloody ear off about their noodly protuberances.");
					addnav("Continue");
					addnav("Back to the Hall of Diversity","runmodule.php?module=newhomemuseum&op=racehall");
					if (get_module_pref("seenrace5")==0){
						set_module_pref("seenrace5",1);
						increment_module_pref("exhibitsseen",1);
					}
					break;
				case "robot":
					output("The mannequin in front of you shows a humanoid creature seemingly constructed of metal and glass.  Inside, you can see traces of veins and a nervous system twisting around a wiring harness.  The creature appears to be female, although it's rather difficult to tell.  She stands with her head cocked downwards, as though shy.  Her face is a chrome mask, and behind it reflected light shines back at you from her black, camera-lens eyes.  Sharp-looking spikes extend from her fingertips.`n`n`n");
					output("A sign at her feet reads:`n`nROBOT`nTechnically cyborgs, these creatures are understood to have some human elements.  They are typically rather shy and peaceful life forms, curious about human emotion.  They are capable of simulating human behaviour to a certain extent, although they are incapable of ingesting food or drink.`nRobots are a complicated race, and work very differently from most others.  As their inner workings are too complex to be comprehended by our healers in the Hospital Tent, they have evolved an automatic repair procedure that continually heals them as a background process.  Through redistribution of power and system resources, they can accelerate their self-repairing properties at the expense of combat efficiency, or vice-versa.  In combat, they are glass cannons - their attacks are devastating, but their bodies are very fragile.  Their lightweight frame, efficient power cells and lack of any sensation of exhaustion all combine to create a race that can travel further and fight longer than any organic race.  In short, Robots are delicate, complex, and thoughtful creatures.`n`nSomeone has added in pencil:`nThey're also bloody hilarious.  Try telling them that Pi is exactly three.  That freaks 'em RIGHT out!");
					addnav("Continue");
					addnav("Back to the Hall of Diversity","runmodule.php?module=newhomemuseum&op=racehall");
					if (get_module_pref("seenrace6")==0){
						set_module_pref("seenrace6",1);
						increment_module_pref("exhibitsseen",1);
					}
					break;
				case "gobot":
					output("The display in front of you - you hesitate to call it a mannequin - depicts what appears to be a Robot contestant from the waist up, its lower half replaced by a set of four knobbly tyres and a large diesel-powered engine.  The torso of the machine appears to be even more lightweight and frail-looking than the one on the Robot mannequin.`n`n`n");
					output("A sign at its feet reads:`n`nGOBOT`nGobots are Robots that have modified themselves for faster speeds and longer uptimes.  The addition of a diesel-powered generator and larger battery packs means that this race can travel even further and fight more in a single day.`nGoBots share many of the traits of Robots, but the addition of heavy locomotion equipment necessitates cutting back on armour plating, making them even more susceptible to damage than Robots.  This gives them a not unsignificant disadvantage in combat, as a trade-off against the ability to travel further and fight longer than even their Robot counterparts.`n`nSomeone has added in pencil:`nDon't bother trying to strike up a conversation with them.  They're somewhat hyperactive, and will tear-arse off into the Jungle before you're halfway through saying 'hello,' leaving you coughing on their dust and exhaust fumes.");
					addnav("Continue");
					addnav("Back to the Hall of Diversity","runmodule.php?module=newhomemuseum&op=racehall");
					if (get_module_pref("seenrace7")==0){
						set_module_pref("seenrace7",1);
						increment_module_pref("exhibitsseen",1);
					}
					break;
				case "joker":
					output("The mannequin is dressed in an impeccable light cream Victorian suit, complete with top hat and cane.  He bears a rather sinister grin, and looks human for all extents and purposes.  A set of LED's mounted carefully in the brim of his hat highlight his eyes with a bright green glow.`n`n`n");
					output("A sign by his feet reads:`n`nJOKER`nJokers are Humans who have been infused with vast amounts of Improbability.  They are easily recognised by their glowing green eyes.  Jokers react to the happenings in their lives in ways that other races simply cannot understand.  They are often seen as unpredictable, or even insane.  It is commonly believed that Jokers have the power to manipulate reality itself, making them extremely dangerous.  They tend to enjoy games of chance and mind-altering substances, and their interactions with other races range from playful to deadly.`nIn combat, and in every other area of performance, Jokers are unpredictable.  Each day they wake up different to how they went to sleep.  Their bodies are continually changing in shape, size and strength, so it's entirely impossible to gauge their efficacy as soldiers.  Due to their unpredictable nature, Jokers should be treated with extreme caution.`n`nSomeone has added in pencil:`nIf you see them roll a die, just run.");
					addnav("Continue");
					addnav("Back to the Hall of Diversity","runmodule.php?module=newhomemuseum&op=racehall");
					if (get_module_pref("seenrace8")==0){
						set_module_pref("seenrace8",1);
						increment_module_pref("exhibitsseen",1);
					}
					break;
			}
			break;
		case "relichall":
			if (httpget("examine")==""){
				output("You step into the Hall of Relics.  A selection of familiar equipment brings a lump to your throat, making you nostalgic for a time when life was easier.");
				addnav("Examine machines");
				addnav("Examine the computer","runmodule.php?module=newhomemuseum&op=relichall&examine=computer");
				addnav("Examine the Google","runmodule.php?module=newhomemuseum&op=relichall&examine=google");
			}
			switch (httpget("examine")){
				case "computer":
					output("A computer terminal sits in front of you, behind a glass case.  It looks quite old, but it would have been fairly high-spec in its time - the ReBoard is a folding model, the projection eye is housed seperately to the chassis, and the ThinkCard - or, as some techies call them, the 'Large Dandruff Flake,' or LDF - can be seen through a little window on the side, complete with air holes and nutrient gel.`n`n`n");
					output("There's a little plaque below it.  It reads:`n`nThis is an early 50's computer, thought to have been used by Doktor Improbable himself.  The ReBoard is an early model which was comparable to low-end portable computers at the time.  In later years, the ReBoard would take over from laptops and sub-laptops, providing a foldable VDU-Keyboard device with non-volatile storage memory.`n`nThe attached monitor is a comparatively primitive low-resolution projection apparatus, and would have required special goggles to use.  Thus, these particular types of monitor never really saw commercial success.`n`nThe computer itself is a post-performance-ceiling model by Commodore, featuring the 'ThinkCard' analogue-digital interpreter developed and engineered by Ikawa Laboratories.`n`nThe ThinkCard is an organic attachment that attempted to assess a human being's actions in contrast to their actual intent.  Since their inception, computers have become more intuitive and user-friendly.  The problem as marketed by Ikawa Laboratories was that computers did what you told them to do, which is not necessarily what you actually wanted them to do; the ThinkCard, being more animal than processor, had genuine analogue intelligence and could consequently form a more natural logical interface between human and machine.`n`nThinkCards are generally believed to be at least partially responsible for the technological singularity which led to the EMP bombings, and have consequently been outlawed.");
					addnav("Back to the Hall of Relics","runmodule.php?module=newhomemuseum&op=relichall");
					if (get_module_pref("seenrelic1")==0){
						set_module_pref("seenrelic1",1);
						increment_module_pref("exhibitsseen",1);
					}
					break;
				case "google":
					output("A little red Google sits inside a dusty glass case.  It's a smaller model with a body about the size of two fingers.  Its IO assembly is extended, adding another few inches in the form of a skinny rod.`n`n`n");
					output("A plaque below it reads:`n`nThis is a Google from the early 60's.  These models were designed to be worn on the ear.  The IO assembly - shown here extended, containing directional microphone, projector, camera, antennae and accelerometer - records images, remembers shapes, faces and landmarks, and projects directions and information directly onto the retina, allowing the user to receive an answer to the immortal question used in advertising campaigns across the world - 'Google, where did I put my keys?'`n`nOf course, early Googles were prone to rather embarrassing quirks.  Quite a few users asked their Googles to find some possession, arrows denoting the direction of the item would be superimposed over their vision, and the user would follow the arrows all the way home - only to find that the last place the Google saw it was in a mirror, in the user's hand.`nDespite these drawbacks, by the 70's Googles had all but replaced mobile phones, netbooks and PDA's as communications and mobile computing equipment.");
					addnav("Back to the Hall of Relics","runmodule.php?module=newhomemuseum&op=relichall");
					if (get_module_pref("seenrelic2")==0){
						set_module_pref("seenrelic2",1);
						increment_module_pref("exhibitsseen",1);
					}
					break;
			}
			break;
		case "revolutionhall":
			if (httpget("examine")==""){
				output("You step into the Hall of Revolutions.  A selection of bulky, heavy-looking equipment stands around you.  Many of the displays look half-finished - perhaps this area is still under construction.  There only appears to be one functional exhibit.");
				addnav("Examine machines");
				addnav("Examine the computer","runmodule.php?module=newhomemuseum&op=revolutionhall&examine=computer");
			}
			switch (httpget("examine")){
				case "computer":
					output("A modern computer terminal sits in front of you, behind a glass case.  It's at once oddly beautiful and hideously ugly - its design speaks of combined despair and hope, the rebuilding of an age lost to fear and anger.`n`nIts keyboard - computers have keyboards again, who'd have thought it? - is a DVORAK-layout, microswitched monstrosity reminiscent of a Victorian typewriter, connected to the main chassis with a wiring harness as thick as three fingers.  The display is turned on and running - a large, round blur that appears to hang in midair, displaying bright green text in a fixed-width font, generated by a spinning rod with mounted LED's and powered by your own persistence of vision.  It gives off a pleasant breeze.  It too is connected to the chassis with an enormous wiring harness, the individual wires held in place with cable ties.`n`nThe chassis itself is built into the desk on which the computer sits.  It is a thing of clicking relays and humming vacuum tubes and fizzing electrical valves and clacking mechanical sequencers and shining brass and deep dark wood and polished glass.  It's oddly beautiful, if very, very loud.`n`n`n");
					output("The plaque below it reads:`n`nThis is a modern computer.  It contains no integrated circuitry whatsoever, and thus cannot be damaged by an electromagnetic pulse.  Its design was influenced by several factors; firstly, the very real, very urgent need to rebuild civilisation, particularly with regard to information and communications.  Secondly, the fact that since processors have not been designed or built by unaided human beings for over fifty years, recreating a modern silicon-based computer, even from plans, was impossible.  Thirdly, paranoia about a possible repeat of the EMP incident meant that consumers demanded equipment that could withstand a surge, and were prepared to give up some bells and whistles for it.`nNote the three brass memory allocation pedals beneath the desk, and the throttle lever.  A skilled operator can make this computer perform up to five times as many calculations in one minute as an unskilled operator.  Due to the death of the ThinkCard, computers no longer understand either humans or themselves - they are simply machines again, and must be told explicitly what to do.");
					addnav("Continue");
					addnav("Back to the Hall of Revolutions","runmodule.php?module=newhomemuseum&op=revolutionhall");
					if (get_module_pref("seenrevo1")==0){
						set_module_pref("seenrevo1",1);
						increment_module_pref("exhibitsseen",1);
					}
					break;
			}
	}
	addnav("Return");
	if (httpget("op") != "lobby"){
		addnav("Back to the Lobby","runmodule.php?module=newhomemuseum&op=lobby");
	}
	addnav("Leave the Museum","village.php");
	page_footer();
}
?>