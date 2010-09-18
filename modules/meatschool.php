<?php

function meatschool_getmoduleinfo(){
	$info = array(
		"name"=>"Meat School",
		"author"=>"Dan Hall AKA CavemanJoe, ImprobableIsland.com",
		"version"=>"2009-04-13",
		"category"=>"Village",
		"download"=>"",
	);
	return $info;
}

function meatschool_install(){
	$condition = "if (\$session['user']['location'] == \"Kittania\") {return true;} else {return false;};";
	module_addhook("village",false,$condition);
	return true;
}

function meatschool_uninstall(){
	return true;
}

function meatschool_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "village":
		addnav($args['fightnav']);
		tlschema();
		addnav("Maiko's Cookery Academy","runmodule.php?module=meatschool&op=start");
		break;
	}
	return $args;
}

function meatschool_run(){
	global $session;

	page_header("Maiko's Cookery Academy");
	
	switch (httpget('op')){
		case "start":
			if (get_module_pref("meatsmith","meatsystem") == 0){
				//this is the player's first trip to the Meat School
				output("\"`%Don't worry, it won't feel a thing!`0\"`n`nThe voice comes through confidently - one that's clearly used to carrying across a room full of students.  A teacher's voice.  You step into the hall.`n`nTwenty or so students sit on slightly rickety-looking chairs inside, facing a raised platform.  You barely have time to register the petite, smiling KittyMorph woman before a loud `iBOOM-ch-KA`i draws your attention to the captive bolt pistol in her hand, and the cow-sized, green-furred beast that is at this very moment falling to the floor with a bass-heavy `ithump`i.  Several students squirm in their seats.`n`nThe teacher looks in your direction, and gives you a smile as she puts down the bolt pistol and picks up a pair of razor-sharp knives.  \"`%A new recruit!  I'll bring you up to speed real quick - I'm Maiko, and today we're learning how to cook Bewilderbeest steaks.  From scratch!  Come in, sit down, the first lesson's free!`0\"`n`nYou nervously make your way to a chair and take a seat.  \"`%James, tell me why it's important to begin the bleeding process right away.`0\"`n`nA sick-looking youth stutters a reply.  \"S-s-so it doesn't wake up?\"`n`n\"`%No.  No, if you've kept your gun looked after and done the stunning properly, it won't wake up from that.  The stunning pretty well destroys the forebrain - the animal's alive only in a very clinical sense of the term.  In every sense that matters, to you or to it, it's dead.  No, we start the bleeding process straight away because the blood pressure increases the longer we leave it, and that can rupture blood vessels, and cause muscular haemorrhages - which just makes the meat spoil faster.  Also, when you're out killing monsters in the jungle, if you don't get the meat from them straight away, something's apt to take the carcass the moment you turn your back, be it Midgets or other monsters.  So we butcher the monsters the moment they're unconscious, right?  Now, watch as I make the cuts.  First one, down here - and now we pull that out, and if you want to, you can tie a knot in it.  Second one, this way, and then again, and now we reach in and...  One more...  Aah!  There we go.  Hold these for me, James.  And now - see?  Just like that.  Can you see, there, at the back?  Good.  Look here - the meat can sometimes jump around a little bit.  Don't be freaked out, it's not still alive.  That's just the muscle reacting to the air - it's not used to this much oxygen.  See?  This bit in my hand is still twitching around, even though it's not attached to anything.  I'd like to point out as I'm doing this that it won't always be possible to do it so neatly while you're cleaning the animals that tried to kill you - but just do the best you can.  We're only interested in the best cuts, these ones here - the Zombies and Humans will take the other meat, and the Midgets will only take the... well, James, you could probably sell `ithose`i to them.  Now, we'll do a couple more over here, and...  Um... you there, the new recruit, are you okay?  You look a bit, um...`0\"`n`nVarious students turn around to look at you.  \"Fine,\" you squeak.`n`n\"`%Jolly good then - come on, up you come, let's see if you can do this bit.  You'll soon learn to swim if we chuck you in the deep end, eh?`0\"  You gulp, and head up to the platform.  Right into the deep end.`n`nAn hour later, Maiko shakes your hand.  \"`%That was a very successful first lesson!  If you want to gain some experience in field-dressing, cleaning and cooking, I can offer private tuition at ten Requisition per lesson.  But to be honest - you're a natural!`0\"`n`nYou're not entirely sure how to take that.`n`n`c`bYou have gained two new skills!`b`nYou are now at level one in Carcass Cleaning and Cooking!`c");
				require_once "modules/staminasystem/lib/lib.php";
				get_player_action("Cleaning the Carcass");
				get_player_action("Cooking");
				set_module_pref("meatsmith",1,"meatsystem");
			} else {
				//lessons
				require_once "modules/staminasystem/lib/lib.php";
				$amber = get_stamina();
				if ($amber == 100){
					output("Maiko greets you at the door with a wide grin.  \"`%Back for some more training, eh?  Ten Req per lesson, and I keep the meat.  How's that sound?`0\"`n`n`JMaiko, like other teachers in Improbable Island, can help you level up some of your skills.  When you pay to train with Maiko, you'll use as much Stamina in performing your chosen actions as normal, but you'll receive two and a half times the experience.  Higher levels in Cooking and Carcass Cleaning will make these actions cost fewer Stamina points.`n`n`0Will you train with Maiko?`n`n");
					addnav("Train with Maiko");
					if ($session['user']['gold']>=10){
						$cleancost = stamina_getdisplaycost("Cleaning the Carcass");
						addnav(array("Pay 10 Req for a Carcass Cleaning lesson (`Q%s%%`0)", $cleancost),"runmodule.php?module=meatschool&op=train&train=clean");
						$cookcost = stamina_getdisplaycost("Cooking");
						addnav(array("Pay 10 Req for a Cookery lesson (`Q%s%%`0)", $cookcost),"runmodule.php?module=meatschool&op=train&train=cook");
					} else {
						addnav("You don't have enough money.  No lessons for you.","");
					}
				} else {
					output("Maiko greets you at the door with a wide grin.  \"`%Back for some more training, eh?  Well, you look half-asleep to me.  Training won't do you any good at all if you're too tired to take it in.  Go and get some rest, or a coffee or something, then we'll talk.`0\"  She shoos you out of the door.");
				}
			}
			addnav("Leave this place");
			addnav("Return to Kittania","village.php");
		break;
		case "train":
			if (is_module_active("medals")){
				require_once "modules/medals.php";
				medals_award_medal("maiko_train","Maiko's Meat School","This player took lessons at Maiko's Meat School!","medal_maiko.png");
			}
			$session['user']['gold']-=10;
			require_once("modules/staminasystem/lib/lib.php");
			switch (httpget('train')){
				case "clean":
					apply_stamina_buff('maikoclean', array(
						"name"=>"Maiko\'s Training",
						"action"=>"Cleaning the Carcass",
						"costmod"=>1,
						"expmod"=>2.5,
						"rounds"=>1,
						"roundmsg"=>"",
						"wearoffmsg"=>"",
					));
					output("Maiko shows you a big smile.  \"`%Another butchery lesson!  Great stuff.  Let's get started!`0\"`n`nMaiko grabs her knives and captive bolt pistol, you don your gloves, and the two of you spend the next little while up to your elbows in warm, still-twitching muscle.  Under Maiko's careful watch, her gentle hands occasionally guiding yours to make a difficult cut, you learn one or two things that you didn't know before.");
					$return = process_action("Cleaning the Carcass");
					output("You receive %s experience in Cleaning the Carcass.`n`n",$return['exp_earned']);
					if ($return['lvlinfo']['levelledup']==true){
						output("`c`b`0You gained a level in Cleaning Carcasses!  You are now level %s!  This action will cost fewer Stamina points now, so you can butcher more creatures each day!`b`c`n",$return['lvlinfo']['newlvl']);
					}
				break;
				case "cook":
					apply_stamina_buff('maikocook', array(
						"name"=>"Maiko\'s Training",
						"action"=>"Cooking",
						"costmod"=>1,
						"expmod"=>2.5,
						"rounds"=>1,
						"roundmsg"=>"",
						"wearoffmsg"=>"",
					));
					output("Maiko shows you a big smile.  \"`%Another cookery lesson!  Great stuff.  Let's get started!`0\"`n`nMaiko grabs her pans and some ingredients, and you don the 'hilarious' apron Maiko has so thoughtfully provided.  Under Maiko's helpful guidance, you learn one or two things that you didn't know before.");
					$return = process_action("Cooking");
					output("You receive %s experience in Cooking.`n`n",$return['exp_earned']);
					if ($return['lvlinfo']['levelledup']==true){
						output("`n`c`b`0You gained a level in Cooking!  You are now level %s!  This action will cost fewer Stamina points now, so you can cook more tasty meals each day!`b`c`n",$return['lvlinfo']['newlvl']);
					}
				break;
			}
			$amber = get_stamina();
			if ($amber == 100){
				addnav("More Training");
				if ($session['user']['gold']>=10){
					$cleancost = stamina_getdisplaycost("Cleaning the Carcass");
					addnav(array("Pay 10 Req for a Carcass Cleaning lesson (`Q%s%%`0)", $cleancost),"runmodule.php?module=meatschool&op=train&train=clean");
					$cookcost = stamina_getdisplaycost("Cooking");
					addnav(array("Pay 10 Req for a Cookery lesson (`Q%s%%`0)", $cookcost),"runmodule.php?module=meatschool&op=train&train=cook");
				} else {
					addnav("You don't have enough money.  No more lessons for you.","");
				}
			} else {
				output("Maiko shows you a grin.  \"`%Well, that was a lot of fun.  But I can see you're getting tired - no point in training any more today, I'm afraid.`0\"  She shoos you out of the door.");
			}
			addnav("Leave this place");
			addnav("Return to Kittania","village.php");
		break;
	}

	page_footer();
	return true;
}
?>