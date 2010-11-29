<?php

/*

TODO
Level-up text
Classy insults

Mechanism for getting these talents:
Player goes to a hut in Pleasantville, encounters emo bloke who offers to teach him coarse and confusing for 10 ciggies, will teach classy for an additional 10 cigs once the player has passed lv10 in either Coarse or Confusing.

*/

function insults_getmoduleinfo(){
	$info = array(
		"name"=>"Insults System",
		"version"=>"2009-09-21",
		"author"=>"Dan Hall",
		"category"=>"Insults",
		"download"=>"",
		"prefs" => array(
			"Insults System user data,title",
			"able"=>"Player has completed Insults Class,bool|0",
			"ableclassy"=>"Player can do Classy insults,bool|0",
			"classy"=>"Player's modifier for Classy insults success,int|1",
			"coarse"=>"Player's modifier for Coarse insults success,int|1",
			"confusing"=>"Player's modifier for Confusing insults success,int|1",
		),
	);
	return $info;
}

function insults_install(){
	module_addhook("fightnav-specialties");
	module_addhook("apply-specialties");
	require_once "modules/staminasystem/lib/lib.php";
	install_action("Insults - Coarse",array(
		"maxcost"=>2000,
		"mincost"=>1500,
		"firstlvlexp"=>2500,
		"expincrement"=>1.02,
		"costreduction"=>5,
		"class"=>"Insults"
	));
	install_action("Insults - Confusing",array(
		"maxcost"=>2000,
		"mincost"=>1500,
		"firstlvlexp"=>2500,
		"expincrement"=>1.025,
		"costreduction"=>5,
		"class"=>"Insults"
	));
	install_action("Insults - Classy",array(
		"maxcost"=>2000,
		"mincost"=>1500,
		"firstlvlexp"=>2500,
		"expincrement"=>1.03,
		"costreduction"=>5,
		"class"=>"Insults"
	));
	return true;
}

function insults_uninstall(){
	require_once "modules/staminasystem/lib/lib.php";
	uninstall_action("Insults - Coarse");
	uninstall_action("Insults - Confusing");
	uninstall_action("Insults - Classy");
	return true;
}

function insults_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "fightnav-specialties":
			if (get_module_pref("able")){
				$script = $args['script'];
				require_once "modules/staminasystem/lib/lib.php";
				addnav("Ronsen-Kiai");
				if (get_module_pref("ableclassy")) addnav(array("Classy (`Q+%s%%`0)",stamina_getdisplaycost("Insults - Classy",3)),$script."op=fight&skill=insult&l=3");
				addnav(array("Confusing (`Q+%s%%`0)",stamina_getdisplaycost("Insults - Confusing",3)),$script."op=fight&skill=insult&l=2");
				addnav(array("Coarse (`Q+%s%%`0)",stamina_getdisplaycost("Insults - Coarse",3)),$script."op=fight&skill=insult&l=1");
			}
			break;
		case "apply-specialties":
			if (httpget('skill')=="insult"){
				require_once "modules/staminasystem/lib/lib.php";
				switch (httpget("l")){
					case 1:
						//Coarse insults
						$act = process_action("Insults - Coarse");
						//todo - level up text
						$l = insults_roll("Coarse");
						$insult = insults_coarse();
						switch ($l){
							case 1:
								apply_buff('insults-coarse', array(
									"startmsg"=>"`0You take a deep breath to unleash a witty and disgusting remark about your foe's mother.  Unfortunately, you picked the exact wrong time to do so - the creature smashes you right in the face.",
									"rounds"=>1,
									"atkmod"=>0,
									"defmod"=>0,
									"schema"=>"module-insults"
								));
							break;
							case 2:
								apply_buff('insults-coarse', array(
									"startmsg"=>"`0You face your attacker, and let loose with a flurry of profanities.  Actually - no, no you don't.  You get a bit tongue-tied, and end up saying \"`#Yeah, you... your mother likes... oh, just leave me alone, you big bully!`0\"  The creature laughs at you, and smacks you upside the head while you pout.",
									"rounds"=>1,
									"atkmod"=>0.6,
									"defmod"=>0.6,
									"schema"=>"module-insults"
								));
							break;
							case 3:
								apply_buff('insults-coarse', array(
									"startmsg"=>"`0You take a breath to unleash a devastating insult, but notice your enemy lunging for a vicious attack.  You were `ialmost`i caught off-guard, but managed to go into the defensive at the last moment - your counterattack won't be as powerful in this round, though.",
									"rounds"=>1,
									"atkmod"=>0.6,
									"defmod"=>1.2,
									"schema"=>"module-insults"
								));
							break;
							case 4:
								apply_buff('insults-coarse', array(
									"startmsg"=>"`0You reach into yourself and pull out the darkest, smelliest insult you can find - but you don't quite get a chance to use it, as you are distracted by a flurry of blows from your enemy.  Well, at least you didn't fumble it this time.",
									"rounds"=>1,
									"atkmod"=>1,
									"schema"=>"module-insults"
								));
							break;
							case 5:
								$msg = "`0You grin cheerfully at your enemy, and let loose a casual slur.`n\"`#".$insult.",`0\" you snicker.  Your foe is enraged!  It hurls itself at you with a furious growl, attacking hard - but letting its guard down in its anger!";
								apply_buff('insults-coarse', array(
									"startmsg"=>$msg,
									"rounds"=>1,
									"badguyatkmod"=>1.2,
									"badguydefmod"=>0.6,
									"schema"=>"module-insults"
								));
							break;
							case 6:
								$msg = "`0You show your enemy a disconcerting leer, and bring the emotional pain.`n\"`#".$insult."!`0\" you cry.  You can tell you've struck a deep blow!  Your enemy is demoralized, and can neither attack nor defend as effectively for this round!";
								apply_buff('insults-coarse', array(
									"startmsg"=>$msg,
									"rounds"=>1,
									"badguyatkmod"=>0.6,
									"badguydefmod"=>0.6,
									"schema"=>"module-insults"
								));
							break;
							case 7:
								$msg = "`0You draw a deep breath, look your foe straight in the eye, and bellow \"`#".$insult."!`0\"`nBirds scatter from the trees and your enemy stands stunned, open-mouthed.  You take this opportunity to really put the boot in!";
								apply_buff('insults-coarse', array(
									"startmsg"=>$msg,
									"rounds"=>1,
									"badguyatkmod"=>0,
									"badguydefmod"=>0,
									"schema"=>"module-insults"
								));
							break;
						}
						if ($act['lvlinfo']['levelledup']==true){
							output("`n`c`b`0You gained a level in Coarse Insults!  You are now level %s!  This action will cost fewer Stamina points now, so you can execute more sailor-mouthed insults per day!  Also, your odds of success for this Insults class have been slightly improved!`b`c`n",$act['lvlinfo']['newlvl']);
						}
					break;
					case 2:
						//Confusing insults
						$act = process_action("Insults - Confusing");
						//todo - level up text
						$l = insults_roll("Confusing");
						$insult = insults_confusing();
						switch ($l){
							case 1:
								apply_buff('insults-confusing', array(
									"startmsg"=>"`0You take a deep breath to unleash a bewildering proposition upon your enemy.  Unfortunately, you picked the exact wrong time to do so - the creature smashes you right in the face.",
									"rounds"=>1,
									"atkmod"=>0,
									"defmod"=>0,
									"schema"=>"module-insults"
								));
							break;
							case 2:
								apply_buff('insults-confusing', array(
									"startmsg"=>"`0You face your attacker, and let loose with a surreal and bemusing insult that twists their brain into knots.  Actually - no, no you don't.  You get a bit tongue-tied, and end up saying \"`#Lick my... thing... with your... your thing!  You `iTHING!`i`0\"  The creature laughs at you, and smacks you upside the head while you pout.",
									"rounds"=>1,
									"atkmod"=>0.5,
									"defmod"=>0.5,
									"schema"=>"module-insults"
								));
							break;
							case 3:
								apply_buff('insults-confusing', array(
									"startmsg"=>"`0You take a breath to unleash a devastating and confusing slur, but notice your enemy lunging for a vicious attack.  You were `ialmost`i caught off-guard, but managed to go into the defensive at the last moment - your counterattack won't be as powerful in this round, though.",
									"rounds"=>1,
									"atkmod"=>0.5,
									"defmod"=>1.2,
									"schema"=>"module-insults"
								));
							break;
							case 4:
								apply_buff('insults-confusing', array(
									"startmsg"=>"`0You reach deep inside and pull out the most bewildering phrase you can find - but you don't quite get a chance to use it, as you are distracted by a flurry of blows from your enemy.  Well, at least you didn't fumble it this time.",
									"rounds"=>1,
									"atkmod"=>1,
									"schema"=>"module-insults"
								));
							break;
							case 5:
								$msg = "`0You lean forward and whisper your insult with a knowing smile.`n\"`#".$insult.".`0\"  Your foe is confused and enraged!  It hurls itself at you with a furious growl, attacking hard - but letting its guard down in its anger!";
								apply_buff('insults-confusing', array(
									"startmsg"=>$msg,
									"rounds"=>1,
									"badguyatkmod"=>1.2,
									"badguydefmod"=>0.5,
									"schema"=>"module-insults"
								));
							break;
							case 6:
								$msg = "`0You grin cheerfully at your enemy, and offer a subtle and complex proposition.`n\"`#".$insult."!`0\" you cry.  You can tell you've struck a deep blow!  Your enemy is distracted, and can neither attack nor defend as effectively for this round!";
								apply_buff('insults-confusing', array(
									"startmsg"=>$msg,
									"rounds"=>1,
									"badguyatkmod"=>0.5,
									"badguydefmod"=>0.5,
									"schema"=>"module-insults"
								));
							break;
							case 7:
								$msg = "`0You draw a deep breath, look your foe straight in the eye, and bellow \"`#".$insult."!`0\"`nBirds scatter from the trees and your enemy stands stunned, open-mouthed and bewildered.  You take this opportunity to really put the boot in!";
								apply_buff('insults-confusing', array(
									"startmsg"=>$msg,
									"rounds"=>1,
									"badguyatkmod"=>0,
									"badguydefmod"=>0,
									"schema"=>"module-insults"
								));
							break;
						}
						if ($act['lvlinfo']['levelledup']==true){
							output("`n`c`b`0You gained a level in Confusing Insults!  You are now level %s!  This action will cost fewer Stamina points now, so you can forward more bizarre propositions per day!  Also, your odds of success for this Insults class have been slightly improved!`b`c`n",$act['lvlinfo']['newlvl']);
						}
					break;
					case 3:
						//Classy insults
						$act = process_action("Insults - Classy");
						//todo - level up text
						$l = insults_roll("Classy");
						$insult = insults_classy();
						switch ($l){
							case 1:
								apply_buff('insults-classy', array(
									"startmsg"=>"`0You draw breath to unleash a devastating, Churchill-esque derision.  Unfortunately, you picked the exact wrong time to do so - the creature catches you off-guard and smashes you right in the face.",
									"rounds"=>1,
									"atkmod"=>0,
									"defmod"=>0,
									"schema"=>"module-insults"
								));
							break;
							case 2:
								apply_buff('insults-classy', array(
									"startmsg"=>"`0You face your attacker, and let loose with a discourtesy that shakes your foe to its very core.  Actually - no, no you don't.  You suffer a momentary lapse in wit and end up saying \"`#You are a... bloody... flibbling... oh, just piss off!`0\"  The creature laughs at you, and smacks you upside the head while you fume impotently.",
									"rounds"=>1,
									"atkmod"=>0.5,
									"defmod"=>0.5,
									"schema"=>"module-insults"
								));
							break;
							case 3:
								apply_buff('insults-classy', array(
									"startmsg"=>"`0You take a breath to unleash a devastating and distressing slur, but notice your enemy lunging for a vicious attack.  You were `ialmost`i caught off-guard, but managed to go into the defensive at the last moment - your counterattack won't be as powerful in this round, though.",
									"rounds"=>1,
									"atkmod"=>0.5,
									"defmod"=>1.2,
									"schema"=>"module-insults"
								));
							break;
							case 4:
								apply_buff('insults-classy', array(
									"startmsg"=>"`0You summon all your wit and ingenuity to concoct a truly powerful accusation - but you don't quite get a chance to use it, as you are distracted by a flurry of blows from your enemy.  Well, at least you didn't fumble it this time.",
									"rounds"=>1,
									"atkmod"=>1,
									"schema"=>"module-insults"
								));
							break;
							case 5:
								$msg = "`0You strike a pose and cast out your insult.`n\"`#You ".$insult."!`0\"  Your foe is enraged!  It hurls itself at you with a furious growl, attacking hard - but letting its guard down in its anger!";
								apply_buff('insults-classy', array(
									"startmsg"=>$msg,
									"rounds"=>1,
									"badguyatkmod"=>1.2,
									"badguydefmod"=>0.5,
									"schema"=>"module-insults"
								));
							break;
							case 6:
								$msg = "`0You step back for a moment, and cast out a truly vile accusation.`n\"`#You are the biggest ".$insult." that it has ever been my misfortune to encounter!`0\" you cry.  You can tell you've struck a deep blow!  Your enemy is most vexed and distressed, and can neither attack nor defend as effectively for this round!";
								apply_buff('insults-classy', array(
									"startmsg"=>$msg,
									"rounds"=>1,
									"badguyatkmod"=>0.5,
									"badguydefmod"=>0.5,
									"schema"=>"module-insults"
								));
							break;
							case 7:
								$msg = "`0You draw a deep breath, look your foe straight in the eye, and call in a firm, assertive voice \"`#I have it on good authority that you, my friend, are a ".$insult."!`0\"`nBirds scatter from the trees and your enemy stands stunned, open-mouthed and bewildered.  You take this opportunity to really put the boot in!";
								apply_buff('insults-classy', array(
									"startmsg"=>$msg,
									"rounds"=>1,
									"badguyatkmod"=>0,
									"badguydefmod"=>0,
									"schema"=>"module-insults"
								));
							break;
						}
						if ($act['lvlinfo']['levelledup']==true){
							output("`n`c`b`0You gained a level in Classy Insults!  You are now level %s!  This action will cost fewer Stamina points now, so you can cast more disdainful accusations per day!  Also, your odds of success for this Insults class have been slightly improved!`b`c`n",$act['lvlinfo']['newlvl']);
						}
					break;
				}
			}
			break;
		}
	return $args;
}

function insults_run(){
	global $session;
}

function insults_roll($type){
	require_once "lib/bell_rand.php";
	require_once "modules/staminasystem/lib/lib.php";
	
	//Coarse are more predictably average, Classy can go really well or really poorly, Confusing is somewhere in the middle.
	//Insult efficacy is affected by Action level.
	
	if ($type=="Classy"){
		$r = bell_rand(100);
		$a = get_player_action("Insults - Classy");
		$r += ($a['lvl']/5);
	}
	if ($type=="Confusing"){
		$r = bell_rand(10,90);
		$a = get_player_action("Insults - Confusing");
		$r += ($a['lvl']/5);
	}
	if ($type=="Coarse"){
		$r = bell_rand(20,80);
		$a = get_player_action("Insults - Coarse");
		$r += ($a['lvl']/5);
	}
	
	if ($r <= 0) return 1;
	if ($r <= 20) return 2;
	if ($r <= 40) return 3;
	if ($r <= 60) return 4;
	if ($r <= 80) return 5;
	if ($r <= 100) return 6;
	return 7;
}

function insults_coarse(){
	$c1 = array("Piss off","Up yours","Feck off","Get bent");
	$c2 = array("wank","arse","penis","bum","armpit","dog","fart","tit");
	$c3 = array("stain","face","nose","hair","lips","burgular","head","breath","licker");
	$d1 = array_rand($c1);
	$d2 = array_rand($c2);
	$d3 = array_rand($c3);
	$insult = $c1[$d1].", ".$c2[$d2]."-".$c3[$d3];
	return $insult;
}

function insults_confusing(){
	$c1 = array("Paint","Wank","Mend","Bury","Poke","Squeeze","Shave","Plunder","Tweak","Fondle","Spank","Eat","Chew","Lick","Sniff","Slap","Bite","Taste","Inhale","Embiggen");
	$c2 = array("fence","hamster","mum","cousin","grandma","chops","gerbil","mettle","teeth","bits","nipples","socks","kettle","toes");
	$c3 = array("Chewbacca","thrift","no","big","spiffy","fumble","small","light","twonk","bubble","hairy","cutie","furry","scary");
	$c4 = array("head","lips","brain","face","toes","nipples","teeth","pie","bum");
	$d1 = array_rand($c1);
	$d2 = array_rand($c2);
	$d3 = array_rand($c3);
	$d4 = array_rand($c4);
	$insult = $c1[$d1]." my ".$c2[$d2].", ".$c3[$d3]."-".$c4[$d4];
	return $insult;
}

function insults_classy(){
	$c1 = array("blithering","muttering","drooling","slobbering","blubbering","mewling","conniving","churlish","mangled","gorbellied","lumpish","goatish","impertinent","surly","ruttish","venomous","pribbling","craven","errant","weedy");
	$c2 = array("beef","guts","rump","pork","sewer","fat","dizzy","drunken","urchin","rude","tickle","flatulence","armpit","motley","fully","boil","dim","poorly","ill","beetle");
	$c3 = array("minded","brained","gaited","pated","kneed","breath'd","headed","breeding","kissing","bladder'd","eyed","nurtured","witted","biting","eating","fragranc'd","faced","fed","chewing","skinned");
	$c4 = array("peasant","boar-pig","bugbear","clotpole","codpiece","strumpet","pumpion","ratsbane","commoner","horn-beast","harpy","prole","dewberry","moldwarp","vassal","ratstail","giglet","maggot","scut","knave","rapscallion","scallywag","hooligan");
	$d1 = array_rand($c1);
	$d2 = array_rand($c2);
	$d3 = array_rand($c3);
	$d4 = array_rand($c4);
	$insult = $c1[$d1].", ".$c2[$d2]."-".$c3[$d3]." ".$c4[$d4];
	return $insult;
}

?>