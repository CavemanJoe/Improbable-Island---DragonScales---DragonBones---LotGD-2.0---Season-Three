<?php

function insultschool_getmoduleinfo(){
	$info = array(
		"name"=>"Insult School",
		"author"=>"Dan Hall AKA CavemanJoe, ImprobableIsland.com",
		"version"=>"2009-09-23",
		"category"=>"Insults",
		"download"=>"",
	);
	return $info;
}

function insultschool_install(){
	$condition = "if (\$session['user']['location'] == \"Pleasantville\") {return true;} else {return false;};";
	module_addhook("village",false,$condition);
	return true;
}

function insultschool_uninstall(){
	return true;
}

function insultschool_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "village":
			addnav($args['fightnav']);
			tlschema();
			addnav("Cuthbert's Academy of Lingual Defence","runmodule.php?module=insultschool&op=start");
		break;
	}
	return $args;
}

function insultschool_run(){
	global $session;

	page_header("Cuthbert's Academy of Lingual Defence");
	
	switch (httpget('op')){
		case "start":
			if (!get_module_pref("able","insults")){
				//this is the player's first trip to the Insults Academy
				
				//ronsengi
				
				output("\"`qAh.  Not seen you here before.`0\"`n`nA three-eyed, broad-mouthed Mutant sits behind a desk, wearing a loose-fitting black garment reminiscent of a kimono.  \"`qMy name's Cuthbert, and this is my Academy of Lingual Defence.`0\"`n`n\"`#Lingual Defence?`0\" you ask.`n`n\"`qLingual Defence,`0\" replies Cuthbert proudly.  \"`qA perfect supplement to your preferred martial art, perfected over twenty years by my good self.`0\"`n`n\"`#I see.`0\"  You look around the room.  \"`#I don't see any weaponry?`0\"`n`n\"`qNot `iphysical`i weaponry, no.  The art of Lingual Defence, or Ronsen-Kiai, doesn't require any equipment except for every now and then in training.`0\"  He grins.  \"`qThat's not a `iprecise`i translation, by the way, but I find throwing a bit of Japanese in there helps with the marketing.`0\"`n`nYou nod, looking around at the empty room and the rather sparse student list hanging on one wall.  You decide to indulge the guy - it seems he could use the business.  \"`#Can you show me a bit of this Ronsen-Kiai?`0\"`n`nCuthbert shows you an excited grin.  \"`qAbsolutely!  Here, take this.`0\"  He reaches below his desk and hands you a heavy wooden axe handle, before moving to the center of the room.  \"`qNow come and attack me with that.`0\"  He grins.  \"`qDon't be shy, now.  I'll tell you when to stop.`0\"`n`nYou shrug, heft the axe handle, and rush towards him, bringing your weapon down in a crushing arc onto his head, which isn't there.  The Mutant has stepped to the inside, and now stands close enough for you to feel his breath.`n`n\"`qFUCKWIT!`0\" roars the Mutant as you swing the axe handle towards his sides.  \"`qI've seen `iMOLD`i-`0\" the tip of the axe handle sails harmlessly past his stomach - \"`q...swing an axe handle better than that!  Holy shit you're slow!`0\"  The Mutant takes a step back and another breath, and you flick the axe handle around so that you can charge him with the tip.  \"`qAnd FAT!  Jesus Christ, your arse looks like two Volkswagens parked side-by-side!`0\" the Mutant steps back and away from your axe handle, then takes a very rapid step first towards and then past you, your axe handle chasing but not quite catching his thighs.  You switch into opposite stance, to see the Mutant giving you the finger.  \"`qAnd stupid!  Damn, you're denser than that axe handle!`0\"  Man, that's distracting.  You piston your leading leg upwards, hoping to catch the Mutant in the chest.  The Mutant catches your foot and sniffs.  \"`qAnd the stench of your feet could kill a concrete elephant at twenty paces!`0\"  Enraged, you slam your leading leg downwards while pouncing from your back leg, using the Mutant's arms like a set of stairs so that you can kick his stupid mean face like a football.`n`nYou land poorly.`n`nThe Mutant stands over you.  \"`qYou imbecilic, mewling, malodorous pervert.  You feeble-minded, cloth-eared, illigitimate buffoon.  You twisted, disgusting, Thatcher-licking dullard.  Your father fucks sheep and your mother says \"Baaa.\"  You're a disgrace.  An obscenity.  An embarrassment.\"`n`n\"`#Stop!`0\" you cry.  \"`#Why're you saying these things?`0\"`n`nThe Mutant leans down and helps you to your feet.  \"`qBecause they distract you,`0\" he says kindly.  \"`qThey get you so riled up that you try silly maneouvers like the one that just ended with you winded on the floor.  And even though I can tell that you're a better fighter than me, and would have stoved my head in under different circumstances, I was still able to get away with not being hit because a part of you was paying attention to what I was saying.`0\"`n`nYou blink.  \"`#`iThat's`i Ronsen-Kai?  Spouting a load of insults while fighting?`0\"`n`nThe Mutant nods, smiling.  \"`qTwenty cigarettes for your first lesson.  You'll learn the two starter techniques, Coarse and Confusing.  Then when you can prove to me that you're ready, I'll teach you the third technique - Classy, or as some call it, \"Shakespearean.\"  So.  Are you interested in learning how to fight with your tongue as well as your fists and feet?`0\"`n`nWell?  `iAre`i you?");
				addnav("Learn a new combat technique for 20 cigarettes?");
				if ($session['user']['gems']>=20){
					addnav("Hell yes!","runmodule.php?module=insultschool&op=learnbasic");
				} else {
					addnav("No, because you don't have 20 cigarettes.","village.php");
				}
			} else {
				//lessons
				require_once "modules/staminasystem/lib/lib.php";
				$amber = get_stamina();
				if ($amber == 100){
					output("Cuthbert greets you with a firm handshake.  \"`qHello again!  Back for some more training, eh?  Well, you've completed the basic course, so I can give you refresher lessons at 25 Requisition each.`0\"`n`n");
					$coarse = get_player_action("Insults - Coarse");
					$confusing = get_player_action("Insults - Confusing");
					$level = $coarse['lvl']+$confusing['lvl'];
					if ($level>20){
						if (!get_module_pref("ableclassy","insults")){
							output("Cuthbert pauses for a moment and looks you up and down.  \"`qYou know,`0\" he says, \"`qI think you might be ready to learn some Classy insults.  It'll be another 20 cigarettes for the basic Classy course, if you're interested.`0\"`n`n");
							addnav("Train with Cuthbert");
							if ($session['user']['gems']>=20){
								addnav("Pay 20 Cigarettes to learn the Classy Insults action","runmodule.php?module=insultschool&op=learnclassy");
							}
						}
					}
					output("`JCuthbert, like other teachers in Improbable Island, can help you level up some of your skills.  When you pay to train with Cuthbert, you'll use as much Stamina in performing your chosen actions as normal, but you'll receive two and a half times the experience.  Higher levels in any given Stamina action will make that action cost fewer Stamina points.  Higher levels in Insults skills will also improve your chances of casting successful Insults, and reduce the chances of fumbling.`n`n`0Will you train with Cuthbert?`n`n");
					addnav("Train with Cuthbert");
					if ($session['user']['gold']>=25){
						$coarsecost = stamina_getdisplaycost("Insults - Coarse");
						addnav(array("Pay 25 Req for a lesson in Coarse Insults (`Q%s%%`0)", $coarsecost),"runmodule.php?module=insultschool&op=train&train=coarse");
						$confusingcost = stamina_getdisplaycost("Insults - Confusing");
						addnav(array("Pay 25 Req for a lesson in Confusing Insults (`Q%s%%`0)", $confusingcost),"runmodule.php?module=insultschool&op=train&train=confusing");
						if (get_module_pref("ableclassy","insults")){
							$classycost = stamina_getdisplaycost("Insults - Classy");
							addnav(array("Pay 25 Req for a lesson in Classy Insults (`Q%s%%`0)", $classycost),"runmodule.php?module=insultschool&op=train&train=classy");
						}
					} else {
						addnav("You don't have enough Requisition to improve your existing Insults skills.","");
					}
				} else {
					output("Cuthbert greets you with a firm handshake.  He grins.  \"`qI know what you're thinking - you want more lessons, huh?  Well, you look half-asleep to me.  This isn't something you should try without a good night's rest.  Come see me tomorrow.`0\"");
				}
			}
			addnav("Leave this place");
			addnav("Return to Pleasantville","village.php");
		break;
		case "train":
			$session['user']['gold']-=25;
			if (is_module_active("medals")){
				require_once "modules/medals.php";
				medals_award_medal("cuthbert_train","Cuthbert's Academy of Lingual Defence","This player took lessons at Cuthbert's Academy of Lingual Defence!","medal_cuthbert.png");
			}
			require_once("modules/staminasystem/lib/lib.php");
			switch (httpget('train')){
				case "coarse":
					apply_stamina_buff('traincoarse', array(
						"name"=>"Cuthbert's Training",
						"action"=>"Insults - Coarse",
						"costmod"=>1,
						"expmod"=>2.5,
						"rounds"=>1,
						"roundmsg"=>"",
						"wearoffmsg"=>"",
					));
					require_once "modules/insults.php";
					$i1 = insults_coarse();
					$i2 = insults_coarse();
					$i3 = insults_coarse();
					$i4 = insults_coarse();
					$i5 = insults_coarse();
					output("Cuthbert shows you a big smile.  \"`qIt's good to train with you again.  Coarse insults it is.  Let's get started!`0\"`n`nOutside the hut, for the next half an hour or so, people stop to listen to the bizzarre and filthy profanities spouted from within.`n`n");
					output("`i\"`#%s!`0\"`n`n\"`q%s!`0\"`n`n\"`#%s!`0\"`n`n\"`q%s!`0\"`n`n\"`#%s!`0\"`n`n\"`qVery good!`0\"",$i1,$i2,$i3,$i4,$i5);
					$return = process_action("Insults - Coarse");
					output("You receive %s experience in Coarse Insults.`n`n",$return['exp_earned']);
					if ($return['lvlinfo']['levelledup']==true){
						output("`c`b`0You gained a level in Coarse Insults!  You are now level %s!  This action will cost fewer Stamina points now, and you have a higher chance of casting a successful Insult!`b`c`n",$return['lvlinfo']['newlvl']);
					}
				break;
				case "confusing":
					apply_stamina_buff('trainconfusing', array(
						"name"=>"Cuthbert's Training",
						"action"=>"Insults - Confusing",
						"costmod"=>1,
						"expmod"=>2.5,
						"rounds"=>1,
						"roundmsg"=>"",
						"wearoffmsg"=>"",
					));
					require_once "modules/insults.php";
					$i1 = insults_confusing();
					$i2 = insults_confusing();
					$i3 = insults_confusing();
					$i4 = insults_confusing();
					$i5 = insults_confusing();
					output("Cuthbert shows you a big smile.  \"`qIt's good to train with you again.  Confusing insults it is.  Let's get started!`0\"`n`nOutside the hut, for the next half an hour or so, people stop to listen to the bizzarre and surreal propositions spouted from within.`n`n");
					output("`i\"`#%s!`0\"`n`n\"`q%s!`0\"`n`n\"`#%s!`0\"`n`n\"`q%s!`0\"`n`n\"`#%s!`0\"`n`n\"`qVery good!`0\"",$i1,$i2,$i3,$i4,$i5);
					$return = process_action("Insults - Confusing");
					output("You receive %s experience in Confusing Insults.`n`n",$return['exp_earned']);
					if ($return['lvlinfo']['levelledup']==true){
						output("`c`b`0You gained a level in Confusing Insults!  You are now level %s!  This action will cost fewer Stamina points now, and you have a higher chance of casting a successful Insult!`b`c`n",$return['lvlinfo']['newlvl']);
					}
				break;
				case "classy":
					apply_stamina_buff('trainclassy', array(
						"name"=>"Cuthbert's Training",
						"action"=>"Insults - Classy",
						"costmod"=>1,
						"expmod"=>2.5,
						"rounds"=>1,
						"roundmsg"=>"",
						"wearoffmsg"=>"",
					));
					require_once "modules/insults.php";
					$i1 = insults_classy();
					$i2 = insults_classy();
					$i3 = insults_classy();
					$i4 = insults_classy();
					$i5 = insults_classy();
					output("Cuthbert shows you a big smile.  \"`qIt's good to train with you again.  Classy insults it is.  Let's get started!`0\"`n`nOutside the hut, for the next half an hour or so, people stop to listen to the witty and scathing insults spouted from within.`n`n");
					output("`i\"`#You %s!`0\"`n`n\"`qI retort that you are a %s!`0\"`n`n\"`#I've never seen such a %s!`0\"`n`n\"`qIt is a matter of public record that you are a %s!`0\"`n`n\"`#And proud of it!  You sir, on the other hand, are a %s!`0\"`n`n\"`qVery good!`0\"`n`n`i",$i1,$i2,$i3,$i4,$i5);
					$return = process_action("Insults - Classy");
					output("You receive %s experience in Classy Insults.`n`n",$return['exp_earned']);
					if ($return['lvlinfo']['levelledup']==true){
						output("`c`b`0You gained a level in Classy Insults!  You are now level %s!  This action will cost fewer Stamina points now, and you have a higher chance of casting a successful Insult!`b`c`n",$return['lvlinfo']['newlvl']);
					}
				break;
			}
			$amber = get_stamina();
			if ($amber == 100){
				addnav("More Training");
					if ($session['user']['gold']>=25){
						$coarsecost = stamina_getdisplaycost("Insults - Coarse");
						addnav(array("Pay 25 Req for a lesson in Coarse Insults (`Q%s%%`0)", $coarsecost),"runmodule.php?module=insultschool&op=train&train=coarse");
						$confusingcost = stamina_getdisplaycost("Insults - Confusing");
						addnav(array("Pay 25 Req for a lesson in Confusing Insults (`Q%s%%`0)", $confusingcost),"runmodule.php?module=insultschool&op=train&train=confusing");
						if (get_module_pref("ableclassy","insults")){
							$classycost = stamina_getdisplaycost("Insults - Classy");
							addnav(array("Pay 25 Req for a lesson in Classy Insults (`Q%s%%`0)", $classycost),"runmodule.php?module=insultschool&op=train&train=classy");
						}
					} else {
						addnav("You don't have enough Requisition to improve your existing Insults skills.","");
					}
			} else {
				output("Cuthbert shows you a grin.  \"`qWell, that was a lot of fun.  But I can see you're getting tired - no point in training any more today, I'm afraid.`0\"");
			}
			addnav("Leave this place");
			addnav("Return to Pleasantville","village.php");
		break;
		case "learnbasic":
			$session['user']['gems']-=20;
			output("\"`qExcellent!  Let's get started.  There are three classes of Insults - Coarse, Confusing, and Classy.  Coarse insults are the most vulgar sort, Confusing tend to be surreal and off-putting, and Classy insults are more formal and refined.  Each type has different effects upon the foe, and different difficulties.  Obviously, you'll get better at each one with time - I'm only going to teach you the Coarse and Confusing types today, and you can come back to learn the Classy insults when I think you're ready.`0\"`n`n\"`qCoarse insults are quite predictable.  They usually have only minor effects upon the enemy, but if they backfire the damage won't be too great either.  Obviously, once in a while you'll either land a critical insult or suffer a catastrophic fumble, but for the most part you'll find this happens more rarely with Coarse insults.`0\"`n`n\"`qConfusing insults are a little more erratic - your chances of fumbling or scoring a critical emotional hit are increased when compared with Coarse insults.  Similarly, the effects are slightly more potent.`0\"`n`n\"`qClassy insults go even further than Confusing insults, with potentially devastating effects - but, of course, those effects can backfire and just as easily throw `iyou`i off-kilter.`0\"`n`n\"`qRemember, a poorly-cast insult can backfire against you, so use these techniques carefully.`0\"`n`n\"`qWhen you first start using Ronsen-Kiai, you'll probably end up doing yourself about as much harm as good.  But don't be discouraged - as you gain more experience in each Insult type, you'll get the hang of it.  Now let's get started.`0\"`n`nFor the next few hours, passers-by are shocked by the profanities, obscene suggestions, bizarre propositions and scathing retorts emanating from the Academy.  By the time Cuthbert shakes your hand and sends you out into the world, you're sweating, red-faced and sore-throated - but you feel a sense of true accomplishment.`n`n`c`bYou have gained two new skills!`b`nYou are now qualified to cast Coarse and Confusing Insults against your foes!`c");
			set_module_pref("able",1,"insults");
			addnav("Leave this place");
			addnav("Return to Pleasantville","village.php");
		break;
		case "learnclassy":
			$session['user']['gems']-=20;
			output("\"`qExcellent!  Let's get started.  Remember, Classy insults are more unpredictable than others, but their effects are greater, so use them carefully.`0\"For the next few hours, passers-by are shocked by the bizarre, Shakespearean language emanating from the Academy.  By the time Cuthbert shakes your hand and sends you out into the world, you're sweating, red-faced and sore-throated - but you feel a sense of true accomplishment.`n`n`c`bYou have gained a new skill!`b`nYou are now qualified to cast Classy Insults against your foes!`c");
			set_module_pref("ableclassy",1,"insults");
			addnav("Leave this place");
			addnav("Return to Pleasantville","village.php");
		break;
	}
	page_footer();
	return true;
}
?>