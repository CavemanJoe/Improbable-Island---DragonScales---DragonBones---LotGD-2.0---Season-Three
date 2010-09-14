<?php

require_once("lib/http.php");

function misterstern_getmoduleinfo(){
	$info = array(
		"name"=>"Mister Stern",
		"version"=>"2008-12-03",
		"author"=>"Dan Hall",
		"category"=>"Improbable",
		"download"=>"",
		"prefs"=>array(
			"Mister Stern module prefs,title",
			"insubplot"=>"Is the player currently engaged in this subplot?,bool|0",
			"hadtea"=>"Has the player had a cup of tea with Mister Stern?,bool|0",
			"subplotcomplete"=>"Is the subplot complete?,bool|0",
			"gotnh"=>"Player has the relic from NewHome?,bool|0",
			"gotki"=>"Player has the relic from Kittania?,bool|0",
			"gotnp"=>"Player has the relic from New Pittsburgh?,bool|0",
			"gotsq"=>"Player has the relic from Squat Hole?,bool|0",
			"gotpl"=>"Player has the relic from Pleasantville?,bool|0",
			"gotcc"=>"Player has the relic from Cyber City 404?,bool|0",
			"gotah"=>"Player has the relic from AceHigh?,bool|0",
		),
	);
	return $info;
}

function misterstern_install(){
	module_addhook("newhomemuseum");
	module_addhook("counciloffices");
	return true;
}

function misterstern_uninstall(){
	return true;
}

function misterstern_dohook($hookname,$args){
	global $session;
	switch($hookname){
	case "newhomemuseum":
		if (get_module_pref("exhibitsseen","newhomemuseum") > 14){
			//The player has seen enough exhibits, let's start the subplot!
			if (get_module_pref("insubplot")==0 && get_module_pref("subplotcomplete")==0){
				set_module_pref("insubplot",1);
				redirect("runmodule.php?module=misterstern&op=start");
			}
			//The player initially turned down Stern's offer of tea
			if (get_module_pref("hadtea")==0 && get_module_pref("subplotcomplete")==0){
				output("Mister Stern can be seen sat behind his desk.  He shows you a friendly nod and a smile as you walk through the door.`n`n");
				addnav("Mister Stern");
				addnav("So, how about that cup of tea, then?","runmodule.php?module=misterstern&op=tea");
			}
			//The player is in the quest, has had the tea, and is gathering items
			if (get_module_pref("hadtea")==1 && get_module_pref("subplotcomplete")==0){
				if (get_module_pref("gotnh")==1 && get_module_pref("gotki")==1 && get_module_pref("gotnp")==1 && get_module_pref("gotsq")==1 && get_module_pref("gotpl")==1 && get_module_pref("gotcc")==1 && get_module_pref("gotah")==1){
					addnav("Mister Stern");
					addnav("Show Mister Stern what you've found","runmodule.php?module=misterstern&op=show");
					output("Mister Stern can be seen sat behind his desk.  He shows you a friendly nod and a smile as you walk through the door.`n`n");
				} else {
					output("Mister Stern can be seen sat behind his desk.  He shows you a friendly nod and a smile as you walk through the door.`n`n");
					output("You contemplate showing Mister Stern what you've found, but realise that it'd be a lot more impressive if you got something from the outpost for every race and gave them to him all at once.`n`n");
				}
			}
			if (get_module_pref("subplotcomplete")==1){
				output("Mister Stern can be seen behind the desk, but the Museum is so heaving with visitors that you can't get close enough to chat with him!  Clearly your work here has had a positive impact!`n`n");
				if (is_module_active("medals")){
					require_once "modules/medals.php";
					medals_award_medal("mister_stern","Museum Marketeer","This player helped Mister Stern restore his museum to popularity!","medal_museumquest.png");
				}
			}
		}
		break;
	case "counciloffices":
		if ($session['user']['location'] == "NewHome" && get_module_pref("gotnh")==0 && get_module_pref("hadtea")==1){
			addnav("Enquire about exhibits for Mister Stern's museum","runmodule.php?module=misterstern&op=getrelic&location=nh");
		};
		if ($session['user']['location'] == "Kittania" && get_module_pref("gotki")==0 && get_module_pref("hadtea")==1){
			addnav("Enquire about exhibits for Mister Stern's museum","runmodule.php?module=misterstern&op=getrelic&location=ki");
		};
		if ($session['user']['location'] == "New Pittsburgh" && get_module_pref("gotnp")==0 && get_module_pref("hadtea")==1){
			addnav("Enquire about exhibits for Mister Stern's museum","runmodule.php?module=misterstern&op=getrelic&location=np");
		};
		if ($session['user']['location'] == "Squat Hole" && get_module_pref("gotsq")==0 && get_module_pref("hadtea")==1){
			addnav("Enquire about exhibits for Mister Stern's museum","runmodule.php?module=misterstern&op=getrelic&location=sq");
		};
		if ($session['user']['location'] == "Pleasantville" && get_module_pref("gotpl")==0 && get_module_pref("hadtea")==1){
			addnav("Enquire about exhibits for Mister Stern's museum","runmodule.php?module=misterstern&op=getrelic&location=pl");
		};
		if ($session['user']['location'] == "Cyber City 404" && get_module_pref("gotcc")==0 && get_module_pref("hadtea")==1){
			addnav("Enquire about exhibits for Mister Stern's museum","runmodule.php?module=misterstern&op=getrelic&location=cc");
		};
		if ($session['user']['location'] == "AceHigh" && get_module_pref("gotah")==0 && get_module_pref("hadtea")==1){
			addnav("Enquire about exhibits for Mister Stern's museum","runmodule.php?module=misterstern&op=getrelic&location=ah");
		};
		break;
	}
	return $args;
}

function misterstern_run(){
	global $session;
	switch (httpget("op")){
		case "start":
			page_header("Mister Stern");
			$name = $session['user']['name'];
			output("\"`6Hello!`0\"`n`nYour heart puts on its running shoes and tears off around the track.  After roughly half a second of panic, you realise that the voice sounded quite friendly.  Jolly, even.  Male, British - a little upper class, perhaps, but not snooty with it.  Rather warm and sophisticated, in fact.`n`nYou look in the direction of the voice, and in the dim light you make out the outline of a figure standing behind the desk in the lobby.  You step forward.  \"`#Um, hello?`0\" you say.  \"`#I'm sorry, I thought I was alone.`0\"`n`nAs you step closer, the figure walks around from behind his desk.  \"`6I can't blame you, in this gloom,`0\" he says, walking across the room to a window and opening it.  Warm sunlight floods the lobby, little specks of dust dancing and reflecting in the air.  \"`6There,`0\" says the man.  \"`6That's better.`0\"`n`nThe tall, rather slender man wears a grey waistcoat, white shirt, and smart grey trousers.  A little pair of round-framed glasses sits on his nose, its arms connected via a chain around his neck.  When he turns to face you, you can see the outline of a pocket watch resting in his waistcoat pocket, silver chain sparkling.  His age is hard to tell.  Clearly he's been around a few years; he's old enough to make you think of the librarian or dusty old academic who can't quite bring himself to retire, despite it being more than time to do so.`n`nYou can't help but grin.  This guy is the ultimate stereotype of dusty British museum curators.`n`nHe grins back and steps towards you, hand outstretched.  \"`6Mister Havelock Stern, Stern by name but not by nature, pleased to make your acquiaintance.`0\"  You get the impression that he says this every time he meets someone - and that when he said he was pleased to meet you, he actually meant it.  You take the hand, give it a shake, and say \"`#%s`#.  Nice to meet you.`0\"`n`n\"`6Sorry to give you a fright,`0\" says Mister Stern.  \"`6Only I'd just nipped into the cupboard to put the kettle on.  I don't get many visitors here, you see.`0\"`n`n\"`#That's okay,`0\" you say, a little sheepishly.  \"`#Honestly I think I was just full of adrenaline anyway.`0\"`n`nMister Stern shows you another grin.  \"`6Aha.  Did `\$The Watcher`6 send you here, by any chance?  Blonde lady, glasses, looks somewhere in her thirties?  Always wears a red turtleneck?`0\"`n`n\"`#Utterly insane and rather frightening?`0\"`n`nMister Stern chuckles.  \"`6Yes, that's the one.  I see.  Well, you must be a new recruit, then.  Well, I remember my first day.  It's rough, isn't it?  Well, the kettle should be boiled by now, if you'd like a break from it all...?`0\"`n`nYou've been kidnapped and thrust into a war against a reality-altering machine, and now a kindly gentleman is offering you a cup of tea.  Well, what happens next?",$name);
			addnav("Mister Stern");
			addnav("I have to admit, it's all a bit confusing.  A cup of tea sounds nice.","runmodule.php?module=misterstern&op=tea");
			addnav("No, I think I'm doing okay.  Thanks for the offer, but I'd best be on my way.","runmodule.php?module=misterstern&op=notea");
			break;
		case "tea":
			page_header("Mister Stern");
			set_module_pref("hadtea",1);
			output("\"`6Jolly good,`0\" says Mister Stern, and again, you get the impression that he means it.`n`n\"`6So,`0\" he says a few minutes later, while pouring and adulterating the tea, \"`6tell me honestly.  What do you think of the exhibits?`0\"`n`n\"`#To be completely honest,`0\" you say, \"`#I wouldn't have thought there would `ibe`i a museum here.  I mean, this is Improbable Island, after all...`0\" you trail off.  \"`#I still can't believe I'm actually here.  I mean, I watched the show and everything, but you never think your number's gonna come up, do you?  Anyway.  Yes, the exhibits were very nice, but they made me miss the old days a bit.`0\"`n`nMister Stern sits down and hands you your cup.  \"`6Yes,`0\" he says, taking a sip.  \"`6I know exactly what you mean.  It's quite a culture shock, isn't it?  On both sides, I mean - coming to the Island, and getting thrown back into the Renaissance practically overnight.  They're both a big blow to the system, really.`0\"`n`n\"`#Tell me about it,`0\" you mutter into your cup.`n`nMister Stern takes you literally.  \"`6I remember the day the mines went off.  I think we all do, really.  I remember waking up and wondering why my alarm clock wasn't playing.  I assumed we'd had a power cut when I saw the display was blank.`0\"  He blows on his tea.  \"`6So, of course, I checked my phone, and that was dead, too.  Although to be fair, the battery was on its way out, and I always charge it overnight.`0\"  He takes a sip.  \"`6So, I assumed that the battery had run out in the night.  I practically jumped out of bed and ran all around the house trying to find a working clock, worried that I was going to be late.`0\"  He grins.  \"`6I ran a museum in my past life too, you see.  It's become something of a habit.`0\"  You nod and grin back.  \"`6Anyway,`0\" continues Mister Stern, \"`6I picked up the landline to try to call the speaking clock, and couldn't get a dial tone.  That was when I started to think that maybe something very bad had happened.`0\"`n`nMister Stern's eyes seem to focus somewhere in the middle distance, left of your head.  \"`6I remembered I had a little travel alarm clock, powered by a nine volt battery.  I dashed upstairs and rummaged around until I found it.`0\"  He blows on his tea.  \"`6It was dead as a doornail.  I took the battery out, and tested it on my tongue.  It gave a little tingle, so I knew the battery was fine.`0\"  He sips his tea, staring off into nowhere.  \"`6In fact, it gave quite a big tingle - almost as if it had been overcharged.  And that's when I noticed the smell, coming from the alarm clock...`0\"`n`n\"`#I remember that smell,`0\" you mutter.  \"`#Burning solder.`0\"`n`nMister Stern gives you a sad little nod.  You sit in silence for a moment.`n`n\"`6When did you catch on?`0\" asks Mister Stern after some seconds.`n`nYou shrug.  \"`#What, that the world had ended?  I honestly can't remember.  It was a very busy day.  I know my neighbour didn't realise until his car didn't start.  And even then, he didn't understand what was going on.`0\"`n`n\"`6I see,`0\" sighs Mister Stern.  \"`6You know, I think I understand why I don't get many visitors here.  The memories are too much for most people.`0\"`n`nYou nod.  \"`#The other exhibits, though - the dummies, they're not bad.`0\"`n`n\"`6Yes.  Yes, we certainly do need some more exhibits, don't we?  Maybe some cheerful ones.`0\"  Mister Stern looks out of the window.  \"`6You know, there's a world of interesting things out there.  If I could get around a little more, I'd head out and find some things to show off myself.`0\"`n`n\"`#What sort of things?`0\" you ask.`n`n\"`6Oh, just anything interesting, really.  Some herbs from the KittyMorph village, maybe some artefacts from Cyber City, that sort of thing.  I'd like to get a good range of things to show to people, you know?  But since the accident, I just haven't been able to get around too much.`0\"`n`n\"`#Accident?`0\"`n`nMister Stern points to his left knee.  \"`6Shrapnel.  Shrapnel from a piece of the equipment surrounding the Improbability Drive, no less.  It's impossible to walk terribly far, and I daren't use any of these new-fangled teleporter devices - who knows how such a machine would interact with a piece of Improbability-infused metal?`0\"`n`nYou shrug.  \"`#Hell, I'm going to be heading out into the Big Bad Jungle just as soon as I've finished this tea; if I come across anything interesting, I'll be sure to let you know.`0\"`n`nMister Stern smiles.  \"`6I'm sure you'll come across a great `imany`i interesting things.  It's a jungle out there.`0\"`n`nYou grin back.`n`nA few minutes of idle chitchat later, you finish your tea, bid your farewells and leave the Museum.");
			addnav("Continue");
			addnav("Head back into the Outpost","village.php");
			break;
		case "notea":
			page_header("Mister Stern");
			output("\"`6Right enough,`0\" says Mister Stern.  \"`6I expect you've got all sorts of monsters and creatures and things to be getting on with.  The offer remains open, though - I'm here rain or shine.`0\"`n`nYou bid your farewell and head out of the Museum.");
			addnav("Continue");
			addnav("Continue back into the Outpost","village.php");
			break;
		case "getrelic":
			page_header("Got any Relics?");
			output("You ask if there are any interesting things handy that could help illustrate the local culture.`n`n");
			switch ($session['user']['location']){
				case "NewHome":
					output("`0The man behind the desk furrows his brow.  \"`1`iMuseum`i exhibits?  This'll be for that Mister Stern, won't it?`0\"  He rummages in a desk drawer.  \"`1Yup, he's been after some new stuff for his museum for a while now, or so I gather.  Now, let's see...  Ah, maybe this'll do.`0\"  He pulls out what looks like an old service revolver, rusted with age.  \"`1I've been meaning to give this to him for a while.  It's not much good as an actual weapon, but I reckon that's probably the oldest firearm on the whole Island.  Well pre-EMP.  He should like that.`0\"`n`nYou thank the man and make your farewells.");
					give_item("stern_revolver");
					set_module_pref("gotnh",1);
					break;
				case "Kittania":
					output("`0The KittyMorph smiles.  \"`1Oh, Mister Stern must have sent you!  Here, you can have this.`0\"  She reaches into her desk drawer and hands you a dead mouse.`n`nYou stare at it for a moment.`n`n\"`1Oh!  Whoops, sorry...`0\" she takes the mouse back from your hand, giggling as she puts it back in the drawer.  \"`1That's not what I meant to give you at all!  Deary me, what on Earth would a human want with one of `ithose`i?  Here.`0\"`n`nShe hands you a dead rat.`n`nWell, it's better than nothing.  Maybe this is how KittyMorphs pay respect to one another.`n`nMaybe.");
					give_item("stern_rat");
					set_module_pref("gotki",1);
					break;
				case "New Pittsburgh":
					output("The Zombie behind the desk nods, slowly.  She reaches under her desk, and brings up a brain in a jar.  \"`1He's a lovely man.  He can have this BRAAAAAAAAAAIIINS,`0\" she slurs.  \"`1I was saving it for my lunch, but I'm sure it'd be a good conversation piece.  I know our eating habits differ quite a bit.`0\"`n`nYou nod.  \"`#I'm sure it'll prove very useful, thanks.`0\"`n`nYou leave hastily, in case she changes her mind and decides on a fresh brain instead.");
					give_item("stern_brain");
					set_module_pref("gotnp",1);
					break;
				case "Squat Hole":
					output("\"`1Is this for tha' fookin' old geezer in der 'umin town, yeah?`0\"`n`nYou nod.`n`n\"`1Tell 'im 'e can 'ave dis,`0\" says the Midget.  With a loud snort and a cringe-inducing squishing sound, he shifts some phlegm into his mouth and spits into his hand.  He holds it out to you.`n`n\"`1Whassa' fookin' matter, eh?`0\" he asks, seeing that you're less than enthusiastic about his gift.  \"`1I tell yer, you lot've got no fookin' `iclue`i about 'ow we live, do ya?  Thass' a `ivaluable commodity,`i is tha'!  We use it fer all sorts!  This Stern feller, 'e should consider 'imself 'ighly respected fer a gift like dis!`0\"  He looks down at his hand.  \"`1Oh, right.  Never mind, I get yer now.  Sorry mate, I completely forgot me fookin' manners.  I'll get yer a little bag, shall I?`0\"`n`nYou cringe as the Midget pours the contents of his hand into a little sandwich bag, and tosses it to you.  It splats into your hand, mercifully dry but still soft and squishy, and... `iwarm.`i`n`nYou mutter a hasty \"`#Thanks,`0\" and leave quickly before the Midget can give you anything else.");
					give_item("stern_phlegm");
					set_module_pref("gotsq",1);
					break;
				case "Pleasantville":
					output("\"`1This would be for Mister Stern, I assume.  Fine fellow.  Very understanding, easy to talk to.  It's a terrible shame about his accident.`0\"  The Mutant behind the desk hesitates.  \"`1Although to be honest, I've no idea what I can give you.`0\"`n`nYou shrug.  \"`#Anything, really.  Anything that makes it easier to understand Mutants.`0\"`n`nThe Mutant's eyes seem to light up.  \"`1I have some poetry.  Would you like to hear it?`0\"`n`nYou shuffle your feet.  \"`#Ah, well, see, thing is, I've really got to -`0\"`n`n\"`1Black is the pit where my heart once lurked,`0\" says the Mutant, reading from a piece of paper pulled from his pocket.  \"`1`iBlack`i is the pupil of my one working eye.`0\"`n`n\"`#Oh, God...`0\"`n`n\"`1`iBlack`i is the stubbed toenail that sprouts from my left ear.  `iBlack`i is the gunge that seeps from the festering hole in my torso.  `iBLACK`i are the teeth within that festering hole, how I long for a toothbrush that will reach!`0\"`n`nYou begin to feel a little faint.`n`n\"`1`iBlack`i is the colour of my `isoul`i, the colour of my `imind`i, the colour of my heavily-altered `iT-shirt`i, the colour of my `iPAIN!`i  `iBlack`i are the sightless, staring eyes that cover my knees and my penis.`0\"`n`nGrey fuzz seems to be seeping in around the edges of your vision.  You can hear a high-pitched whine, but it doesn't drown out the sounds of the Mutant's poetry.`n`n\"`1`iMy penis!`i  Oh, cruel and sadistic fate!  My instrument of `ilove`i, my rod of passion, studded with the `iblack gems`i that drive women `iscreaming`i and `iurinating`i into the night!  The eyes, the `ieyes!`i  They do not see, they can not look in love and wonder upon a newborn babe or a majestic waterfall!  But if you cut me, do I not bleed?  If you poke my penis-eyes, do they not blink as one in soundless anguish?`0\"`n`n\"`#I'M SURE HE'LL LOVE IT!`0\" you scream.  \"`#THANK YOU VERY VERY MUCH!`0\"  You snatch the paper from his hand, and run gibbering out of the hut.");
					give_item("stern_poetry");
					set_module_pref("gotpl",1);
					break;
				case "Cyber City 404":
					output("\"`1Affirmative.  The fleshling Stern wishes to create a permament record of your primitive, chemical-based knowledge, yes?  An excellent aspiration, given that your memories are flawed and untrustworthy.`0\"  The Robot behind the desk nods his appreciation.  \"`1I will give you this.`0\"`n`nHe hands over a tiny memory card.  \"`#Oh, thank you very much,`0\" you say.  \"`#He seems to be really into his pre-EMP technology.  What is it, exactly?`0\"`n`n\"`1Oddly enough, the collected thoughts and memories of your home town.  A complete record of every personality, ready to be transplanted into Robot bodies should the need arise.  I believe you are in there somewhere.`0\"`n`nYou stare at the card.  Your entire life, and the lives of those around you, on a memory card smaller than a suppository.  You stuff it moodily into your pocket, mutter something like \"`#Thanks,`0\" and head out of the door, leaving the Robot wondering why the display of his superior technology and mental capacity seemed to lose him a potential friend, rather than make one.`n`nHe shrugs.  It seems appropriate to do so.  He's good at shrugging.  He's practiced in front of a mirror.");
					give_item("stern_memorycard");
					set_module_pref("gotcc",1);
					break;
				case "AceHigh":
					output("\"`1You needn't say another word,`0\" says the lady behind the desk, dressed in an immaculate Victorian suit.  \"`1I know what you're here for, and I know who sent you.  Give him this.`0\"`n`nThe green glow around her eyes seems to intensify as she pulls a coin out of her pocket.  She hands it to you, and the light dies away a little.`n`nYou turn it over.  It's an old British penny, one of the really huge ones from the 1940's.  But there's something wrong.  \"`#This is a double-headed one,`0\" you say.`n`n\"`1Flip it,`0\" says the woman, smiling.`n`nYou do as commanded.  When you catch it, both sides show tails.  \"`#Wow, that's really neat!`0\" you say, and flip it again.  It comes up heads on both sides.  \"`#You know, this is probably the best thing I've gotten on this little adventure.`0\"`n`nThe lady nods, watching you flip the coin a third time, clearly enjoying your interest in her artefact.  \"`1It's certainly a fascinating piece.  Full of rather complex equations and Improbabilities.  Each time the faces are reversed, there's a one in seven chance of folding a localised area of time and space, generating a potentially universe-shattering paradox.  Isn't that exciting?`0\"`n`nYou catch the coin and slip it into your pocket.  \"`#I'll just keep this safe in here for now.`0\"");
					give_item("stern_penny");
					set_module_pref("gotah",1);
					break;
			}
			addnav("Return");
			addnav("Back to the Outpost","village.php");
			break;
		case "show":
			page_header("Mister Stern");
			set_module_pref("subplotcomplete",1);
			output("\"`6My goodness!`0\" says Mister Stern, pushing his glasses further up on his nose and examining the array of objects you've just placed on his desk.  \"`6What an excellent find!  Let's see, here...  A dead rat, some phlegm, a rusty pistol, a brain, some sort of memory card, some... goodness, some Mutant poetry from the look of things...`0\"`n`n\"`#Do you think they'll be useful?`0\" you ask, somewhat dubious.`n`n\"`6Oh, yes!  These are superb!`0\" says Mister Stern.  \"`6Oh, and this must be one of those Joker coins!`0\"  He flips it into the air.`n`n\"`6My goodness!`0\" says Mister Stern, pushing his glasses further up on his nose and examining the array of objects you've just placed on his desk.  \"`6What an excellent find!  Let's see, here...  A dead rat, some phlegm, a rusty pistol, a brain, some sort of memory card, some... my goodness, some Mutant poetry from the look of things...`0\"`n`n\"`#Do you think they'll be useful?`0\" you ask, somewhat dubious.`n`n\"`6Oh, yes!  These are superb!`0\" says Mister Stern.  \"`6Oh, and this must be one of those Joker coins!`0\"  He flips it into the air.`n`nYou pull out a cigarette and light it, knowing that there are questions to be asked and answered - like what you're going to do with the rest of your life - but for the moment, at least, you're content to just lie here for a little while.`n`nA metallic creaking sound jars you out of your contemplation. You sit up, staring at the jagged edges of the Improbability Drive's remains, the jagged edges that are now trying to bend themselves back into shape...`n`n\"`6Oh, and this must be one of those Joker coins!`0\"  He flips it into the air.`n`nYou snatch the coin out of the air mid-flip.  \"`#Might want to go easy on that.`0\"`n`n\"`6Oh,`0\" says Mister Stern.  \"`6Yes, I see what you mean.  Gives one a belter of a headache, doesn't it?  Well, listen, I can't thank you enough for all this, but I want to give you your time's worth.`0\"`n`nHe hands you a pack of ten cigarettes.  Result!`n`n\"`#Well, thanks very much,`0\" you say, \"`#and I really should get going.  I've monsters to kill, and I expect you'll be wanting to arrange your exhibits and write up cards for them, and that sort of thing.`0\"`n`n\"`6Indeed, indeed.  Come back any time!  And thank you once again!`0\"");
			delete_item(has_item("stern_revolver"));
			delete_item(has_item("stern_rat"));
			delete_item(has_item("stern_brain"));
			delete_item(has_item("stern_phlegm"));
			delete_item(has_item("stern_poetry"));
			delete_item(has_item("stern_memorycard"));
			delete_item(has_item("stern_penny"));
			$session['user']['gems']+=10;
			addnav("Continue");
			addnav("Back to the Outpost","village.php");
			if (is_module_active("medals")){
				require_once "modules/medals.php";
				medals_award_medal("mister_stern","Museum Marketeer","This player helped Mister Stern restore his museum to popularity!","medal_museumquest.png");
			}
			break;
		}
	page_footer();
}
?>