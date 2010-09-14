<?php

function improbablestuff_getmoduleinfo(){
	$info = array(
		"name"=>"Improbable Stuff",
		"version"=>"2008-08-29",
		"author"=>"Dan Hall",
		"category"=>"General",
		"download"=>"",
		"prefs"=>array(
			"item1number"=>"How many Small Medkits does the player have,int|0",
			"item2number"=>"How many Large Medkits does the player have,int|0",
			"item3number"=>"How many Energy Drinks does the player have,int|0",
			"item4number"=>"How many pieces of Nicotine Gum does the player have,int|0",
			"item5number"=>"How many Power Pills does the player have,int|0",
			"item6number"=>"How many One-Shot Teleporters does the player have,int|0",
			"item7number"=>"How many BANG Grenades does the player have,int|0",
			"item8number"=>"How many WHOOMPH Grenades does the player have,int|0",
			"item9number"=>"How many ZAP Grenades does the player have,int|0",
			"item10number"=>"How many cans of Monster Repellant Spray does the player have,int|0",
			"item11number"=>"How many Ration Packs does the player have,int|0",
			"item12number"=>"How many Improbability Bombs does the player have,int|0",
			"item13number"=>"How many Kitten Cards does the player have,int|0",
			"repellantbuff"=>"Monster's attack value - expressed as a percentage of normal - used by the Monster Repellant Spray buff,int|100",
		),
	);
	return $info;
}
function improbablestuff_install(){
	module_addhook("forest");
	module_addhook("village");
	module_addhook("fightnav-specialties");
	module_addhook("apply-specialties");
	module_addhook("worldnav");
	module_addhook("count-travels");
	module_addhook("newday");
	module_addhook("ramiusfavors");
	module_addhook("dragonkill");
	return true;
}
function improbablestuff_uninstall(){
	return true;
}
function improbablestuff_dohook($hookname,$args){
	global $session;
	$smallmedkit = get_module_pref("item1number");
	$largemedkit = get_module_pref("item2number");
	$energydrink = get_module_pref("item3number");
	$nicotinegum = get_module_pref("item4number");
	$potentpill = get_module_pref("item5number");
	$teleporter = get_module_pref("item6number");
	$banggrenade = get_module_pref("item7number");
	$whoomphgrenade = get_module_pref("item8number");
	$zapgrenade = get_module_pref("item9number");
	$repellantspray = get_module_pref("item10number");
	$rationpack = get_module_pref("item11number");
	$improbabilitybomb = get_module_pref("item12number");
	$kittencard = get_module_pref("item13number");
	$traveladd = get_module_pref("traveladd");
	$spec = "improbablestuff";
	switch($hookname){
	case "forest":
		if ($smallmedkit>=1 || $largemedkit>=1 || $energydrink>=1 || $nicotinegum>=1 || $potentpill>=1 || $repellantspray>=1 || $rationpack>=1){
			addnav("`0`bUse Equipment`b`0");
		}
		if ($smallmedkit>0){
			addnav(array("Use Small Medkit (%s remaining)",$smallmedkit),"runmodule.php?module=improbablestuff&use=smallmedkit&from=forest");
		}
		if ($largemedkit>0){
			addnav(array("Use Large Medkit (%s remaining)",$largemedkit),"runmodule.php?module=improbablestuff&use=largemedkit&from=forest");
		}
		if ($energydrink>0){
			addnav(array("Use Energy Drink (%s remaining)",$energydrink),"runmodule.php?module=improbablestuff&use=energydrink&from=forest");
		}
		if ($nicotinegum>0){
			addnav(array("Use Nicotine Gum (%s remaining)",$nicotinegum),"runmodule.php?module=improbablestuff&use=nicotinegum&from=forest");
		}
		if ($potentpill>0){
			addnav(array("Use Power Pill (%s remaining)",$potentpill),"runmodule.php?module=improbablestuff&use=potentpill&from=forest");
		}
		if ($teleporter>0){
			addnav(array("Use One-Shot Teleporter (%s remaining)", $teleporter),
				"runmodule.php?module=improbablestuff&use=teleporter", true);
		}
		if ($session['user']['race']!="Robot" && $session['user']['race']!="Gobot" && $session['user']['race']!="Foebot" && $session['user']['race']!="Joker" && $session['user']['race']!="Stranger" && $rationpack > 0 && get_module_pref("fullness", "staminafood") < 100){
			addnav(array("Use Ration Pack (%s remaining)", $rationpack),
				"runmodule.php?module=improbablestuff&use=rationpack&from=forest", true);
		}
		if ($repellantspray>0){
			addnav(array("Use Monster Repellant Spray (%s remaining)",$repellantspray),"runmodule.php?module=improbablestuff&use=repellantspray&from=forest");
		}
		break;
	case "village":
		if ($smallmedkit>=1 || $largemedkit>=1 || $energydrink>=1 || $nicotinegum>=1 || $potentpill>=1 || $repellantspray>=1 || $rationpack>=1){
			addnav("`0`bUse Equipment`b`0");
		}
		if ($smallmedkit>0){
			addnav(array("Use Small Medkit (%s remaining)",$smallmedkit),"runmodule.php?module=improbablestuff&use=smallmedkit&from=village");
		}
		if ($largemedkit>0){
			addnav(array("Use Large Medkit (%s remaining)",$largemedkit),"runmodule.php?module=improbablestuff&use=largemedkit&from=village");
		}
		if ($energydrink>0){
			addnav(array("Use Energy Drink (%s remaining)",$energydrink),"runmodule.php?module=improbablestuff&use=energydrink&from=village");
		}
		if ($nicotinegum>0){
			addnav(array("Use Nicotine Gum (%s remaining)",$nicotinegum),"runmodule.php?module=improbablestuff&use=nicotinegum&from=village");
		}
		if ($potentpill>0){
			addnav(array("Use Power Pill (%s remaining)",$potentpill),"runmodule.php?module=improbablestuff&use=potentpill&from=village");
		}
		if ($teleporter>0){
			addnav(array("Use One-Shot Teleporter (%s remaining)", $teleporter),
				"runmodule.php?module=improbablestuff&use=teleporter", true);
		}
		if ($session['user']['race']!="Robot" && $session['user']['race']!="Gobot" && $session['user']['race']!="Foebot" && $session['user']['race']!="Joker" && $session['user']['race']!="Stranger" && $rationpack > 0 && get_module_pref("fullness", "staminafood") < 100){
			addnav(array("Use Ration Pack (%s remaining)", $rationpack),
				"runmodule.php?module=improbablestuff&use=rationpack&from=village", true);
		}
		if ($repellantspray>0){
			addnav(array("Use Monster Repellant Spray (%s remaining)",$repellantspray),"runmodule.php?module=improbablestuff&use=repellantspray&from=village");
		}
		strip_buff('ibomb7a');
		strip_buff('ibomb7b');
		strip_buff('ibomb8');
		break;
	case "worldnav":
		if ($smallmedkit>=1 || $largemedkit>=1 || $energydrink>=1 || $nicotinegum>=1 || $potentpill>=1 || $repellantspray>=1 || $rationpack>=1){
			addnav("`0`bUse Equipment`b`0");
		}
		if ($smallmedkit>0){
			addnav(array("Use Small Medkit (%s remaining)",$smallmedkit),"runmodule.php?module=improbablestuff&use=smallmedkit&from=worldnav");
		}
		if ($largemedkit>0){
			addnav(array("Use Large Medkit (%s remaining)",$largemedkit),"runmodule.php?module=improbablestuff&use=largemedkit&from=worldnav");
		}
		if ($energydrink>0){
			addnav(array("Use Energy Drink (%s remaining)",$energydrink),"runmodule.php?module=improbablestuff&use=energydrink&from=worldnav");
		}
		if ($nicotinegum>0){
			addnav(array("Use Nicotine Gum (%s remaining)",$nicotinegum),"runmodule.php?module=improbablestuff&use=nicotinegum&from=worldnav");
		}
		if ($potentpill>0){
			addnav(array("Use Power Pill (%s remaining)",$potentpill),"runmodule.php?module=improbablestuff&use=potentpill&from=worldnav");
		}
		if ($teleporter>0){
			addnav(array("Use One-Shot Teleporter (%s remaining)", $teleporter),
				"runmodule.php?module=improbablestuff&use=teleporter", true);
		}
		if ($session['user']['race']!="Robot" && $session['user']['race']!="Gobot" && $session['user']['race']!="Foebot" && $session['user']['race']!="Joker" && $session['user']['race']!="Stranger" && $rationpack > 0 && get_module_pref("fullness", "staminafood") < 100){
			addnav(array("Use Ration Pack (%s remaining)", $rationpack),
				"runmodule.php?module=improbablestuff&use=rationpack&from=worldnav", true);
		}
		if ($repellantspray>0){
			addnav(array("Use Monster Repellant Spray (%s remaining)",$repellantspray),"runmodule.php?module=improbablestuff&use=repellantspray&from=worldnav");
		}
		break;
	case "fightnav-specialties":
		$script = $args['script'];
		if ($banggrenade>=1 || $whoomphgrenade>=1 || $zapgrenade>=1 || $teleporter>=1 || $potentpill>=1 || $repellantspray>=1){
			addnav("`0`bUse Equipment`b`0");
		}
		if ($banggrenade>=1){
			addnav(array("`4BANG Grenade`7 (%s left)`0", $banggrenade),
				$script."op=fight&skill=$spec&l=1", true);
		}
		if ($whoomphgrenade>=1){
			addnav(array("`^WHOOMPH Grenade`7 (%s left)`0", $whoomphgrenade),
				$script."op=fight&skill=$spec&l=2", true);
		}
		if ($zapgrenade>=1){
			addnav(array("`#ZAP Grenade`7 (%s left)`0", $zapgrenade),
				$script."op=fight&skill=$spec&l=3", true);
		}
		if ($potentpill>=1){
			addnav(array("Use Power Pill (%s remaining)",$potentpill),
			$script."op=fight&skill=$spec&l=4", true);
		}
		if ($teleporter>=1){
			addnav(array("`7One-Shot Teleporter`7 (%s left)`0", $teleporter),
				"runmodule.php?module=improbablestuff&use=teleporter", true);
		}
		if ($repellantspray>=1){
			addnav(array("Use Monster Repellant Spray (%s remaining)",$repellantspray),
			$script."op=fight&skill=$spec&l=5", true);
		}
		if ($improbabilitybomb>=1){
			addnav(array("Use Improbability Bomb (%s remaining)",$improbabilitybomb),
			$script."op=fight&skill=$spec&l=6", true);
		}
		break;
	case "ramiusfavors":
		if ($kittencard>=1){
			addnav("Other");
			addnav(array("K?`0Give `\$The Watcher`0 a Kitten Card (%s left)`0", $kittencard),
				"runmodule.php?module=improbablestuff&use=kittencard", true);
		}
		break;
	case "newday":
		set_module_pref("traveladd",0);
		set_module_pref("encounterchance",100,"worldmapen");
		set_module_pref("repellantbuff",100);
		break;
	case "dragonkill":
		set_module_pref("item1number",0);
		set_module_pref("item2number",0);
		set_module_pref("item3number",0);
		set_module_pref("item4number",0);
		set_module_pref("item5number",0);
		set_module_pref("item6number",0);
		set_module_pref("item7number",0);
		set_module_pref("item8number",0);
		set_module_pref("item9number",0);
		set_module_pref("item10number",0);
		set_module_pref("item11number",0);
		set_module_pref("item12number",0);
		set_module_pref("item13number",0);
		set_module_pref("traveladd",0);
		set_module_pref("encounterchance",100,"worldmapen");
		set_module_pref("repellantbuff",100);
		break;
	case "apply-specialties":
			$skill = httpget('skill');
			$l = httpget('l');
			if ($skill==$spec){
				if ($banggrenade >= 1){
					switch($l){
					case 1:
						apply_buff('is1', array(
							"startmsg"=>"`4You pull the pin on your grenade and toss it at {badguy}!",
							"rounds"=>1,
							"minioncount"=>1,
							"minbadguydamage"=>"5+round(<attack>*1.0,0);",
							"maxbadguydamage"=>"10+round(<attack>*2.0,0);",
							"effectmsg"=>"`4The grenade explodes close enough to {badguy}`4 to do `^{damage}`4 damage!",
							"schema"=>"module-improbablestuff"
						));
						set_module_pref("item7number", get_module_pref("item7number")-1);
						break;
					}
				}
				if ($whoomphgrenade >= 1){
					switch($l){
					case 2:
						apply_buff('is2', array(
							"startmsg"=>"`^You pull the pin on your grenade and toss it at {badguy}.  The grenade explodes with a satisfying WHOOMPH, close enough to set your foe aflame!",
							"name"=>"`^WHOOMPH Grenade",
							"rounds"=>-1,
							"minioncount"=>1,
							"minbadguydamage"=>"1+round(<attack>*0.1,0);",
							"maxbadguydamage"=>"2+round(<attack>*0.4,0);",
							"effectmsg"=>"`^Your enemy beats at the flames, but it's still on fire!  `^{damage}`^ damage has been done in this round!",
							"expireafterfight"=>1,
							"schema"=>"module-improbablestuff"
						));
						set_module_pref("item8number", get_module_pref("item8number")-1);
						break;
					}
				}
				if ($zapgrenade >= 1){
					switch($l){
					case 3:
						apply_buff('is3', array(
							"startmsg"=>"`#You pull the pin on your grenade and toss it at {badguy}`#, shielding your eyes.  After a blinding flash, your foe is left dazed and confused!",
							"name"=>"`^ZAP Grenade",
							"rounds"=>e_rand(2,7),
							"badguyatkmod"=>0,
							"badguydefmod"=>0,
							"roundmsg"=>"{badguy} is blinded, deafened and thoroughly confused, and flails wildly while you pummel it!`n",
							"wearoff"=>"{badguy}`# feels some coherence return, and lunges at you!`n",
							"expireafterfight"=>1,
							"schema"=>"module-improbablestuff"
						));
						set_module_pref("item9number", get_module_pref("item9number")-1);
						break;
					}
				}
				if ($potentpill >= 1){
					switch($l){
					case 4:
						apply_buff('is4', array(
							"startmsg"=>"`#You hastily swallow a Power Pill.  You feel immediately revitalised!  You regain some Stamina, and you are now regenerating some hitpoints!`n",
							"name"=>"`^Power Pill",
							"rounds"=>10,
							"regen"=>"ceil(<maxhitpoints>/10)+1;",
							"effectmsg"=>"The effects of the Power Pill heal you for {damage} points.",
							"wearoff"=>"The Power Pill effects have worn off.`n",
							"schema"=>"module-improbablestuff"
						));
						set_module_pref("item5number", get_module_pref("item5number")-1);
						require_once("modules/staminasystem/lib/lib.php");
						addstamina(250000);
						break;
					}
				}
				if ($repellantspray >= 1){
					switch($l){
					case 5:
						apply_buff('is5', array(
							"startmsg"=>"`#You pull out a can of Monster Repellant Spray, and spray it liberally on the enemy!`n",
							"name"=>"`^Repellant Spray",
							"rounds"=>10,
							"badguyatkmod"=>0.5,
							"badguydefmod"=>0.5,
							"roundmsg"=>"{badguy} is coughing, choking and all runny-nosed, and cannot attack or defend as effectively!",
							"wearoff"=>"The effects of your Monster Repellant Spray seem to have worn off...`n",
							"expireafterfight"=>1,
							"schema"=>"module-improbablestuff"
						));
						set_module_pref("item10number", get_module_pref("item10number")-1);
						break;
					}
				}
				if ($improbabilitybomb >=1){
					switch($l){
					case 6:
						$effect = e_rand(1,9);
						if (has_buff("ibomb7a") && $effect == 7){
							$effect = 3;
						}
						if (has_buff("ibomb8") && $effect == 8){
							$effect = 3;
						}
						apply_buff('startmsg', array(
							"rounds"=>1,
							"atkmod"=>1,
							"startmsg"=>"`0You light the fuse on the Improbability Bomb and toss it towards your opponent.",
							"schema"=>"module-improbablestuff"
						));
						increment_module_pref("item12number",-1);
						switch ($effect){
							case 1:
								apply_buff('ibomb1', array(
									"rounds"=>1,
									"atkmod"=>1,
									"startmsg"=>"`0The bomb bursts into a shower of Requisition tokens!  Blimey, there must be about a thousand of them!  What's more, all these tokens are swirling into the air and nose-diving straight into your pocket.  Result!",
									"schema"=>"module-improbablestuff"
								));
								$gold = e_rand(900,1100);
								$session['user']['gold'] += $gold;
								break;
							case 2:
								apply_buff('ibomb2', array(
									"rounds"=>1,
									"atkmod"=>1,
									"startmsg"=>"`0The fuse fizzes and sparks, until eventually... it goes out.  The bomb is gone.  However, there's a tasty, tasty cigarette in its place!  You grab it before your enemy gets the chance.",
									"schema"=>"module-improbablestuff"
								));
								$session['user']['gems']++;
								break;
							case 3:
								apply_buff('ibomb3', array(
									"rounds"=>1,
									"minioncount"=>1,
									"minbadguydamage"=>"5+round(<attack>*1.0,0);",
									"maxbadguydamage"=>"5+round(<attack>*3.0,0);",
									"effectmsg"=>"`4The bomb explodes close enough to {badguy}`4 to do `^{damage}`4 damage!",
									"schema"=>"module-improbablestuff"
								));
								break;
							case 4:
								apply_buff('ibomb4', array(
									"rounds"=>1,
									"minioncount"=>1,
									"minbadguydamage"=>1000000,
									"maxbadguydamage"=>1000000000,
									"effectmsg"=>"`4The fuse fizzles down until the bomb ignites with an tiny, dense explosion.  The gravity well draws {badguy}`4 in!  Within moments, {badguy} fuses with the matter that made up the original bomb in an ultra-dense, ultra-high gravity ball of dark matter, which then pops with a faint \"crack,\" leaving behind only a smoking area of burned ground about the size of a penny.  The damage done to the enemy, as if it matters at this stage, was `^{damage}`4 points!",
									"schema"=>"module-improbablestuff"
								));
								break;
							case 5:
								redirect("runmodule.php?module=improbablestuff&use=teleporter&bomb=1");
								break;
							case 6:
								apply_buff('ibomb6', array(
									"rounds"=>1,
									"atkmod"=>1,
									"startmsg"=>"`0The Improbability Bomb breaks open, bathing you in a cool white light.  When it fades, you feel calm, self-confident and somehow more attractive.  Pretty useless in a combat situation, but hey, it's nice to be feel good about yourself.  You gain some Charm.",
									"schema"=>"module-improbablestuff"
								));
								$session['user']['charm']+=2;
								break;
							case 7:
								apply_buff('ibomb7a', array(
									"rounds"=>4,
									"minioncount"=>8,
									"minbadguydamage"=>0,
									"maxbadguydamage"=>5,
									"startmsg"=>"The bomb begins to roll around the theater of combat, bouncing off rocks like a pinball - and firing out showers of white-hot sparks!",
									"effectmsg"=>"`2A glowing spark leaps onto {badguy}, burning it for {damage} points!",
									"schema"=>"module-improbablestuff",
									"expireafterfight"=>1,
								));
								apply_buff('ibomb7b', array(
									"rounds"=>4,
									"minioncount"=>8,
									"mingoodguydamage"=>0,
									"maxgoodguydamage"=>5,
									"effectmsg"=>"`4A white-hot spark attaches to you, burning you for {damage} points!",
									"schema"=>"module-improbablestuff",
									"wearoff"=>"The bomb fizzles out and sends out one last dying volley of sparks.",
									"expireafterfight"=>1,
								));
								break;
							case 8:
								apply_buff('ibomb8', array(
									"startmsg"=>"`0The bomb uncurls, revealing a little `5Purple Monster!`0",
									"rounds"=>-1,
									"name"=>"`5Purple Monster`0",
									"minioncount"=>1,
									"minbadguydamage"=>5,
									"maxbadguydamage"=>50,
									"effectmsg"=>"`5The Purple Monster leaps towards {badguy} and bites down hard for {damage} damage!`0",
									"schema"=>"module-improbablestuff",
									"wearoff"=>"`5The Purple Monster, seeing its business here concluded, disappears with a faint 'pop.'",
									"expireafterfight"=>1,
								));
								break;
							case 9:
								apply_buff('ibomb9', array(
									"rounds"=>1,
									"minioncount"=>1,
									"mingoodguydamage"=>100,
									"maxgoodguydamage"=>500,
									"effectmsg"=>"`4Before the bomb even leaves your hand, it blows up in your face!  The explosion causes {damage} points!",
									"schema"=>"module-improbablestuff",
									"expireafterfight"=>1,
								));
								break;
						}
						break;
					}
				}
			}
		break;
	}
	return $args;
}
// function improbablestuff_runevent(){
			// $item6adjust = get_module_pref("item6number", "improbablestuff");
			// $item6adjust--;
			// set_module_pref("item6number", $item6adjust, "improbablestuff");
			// output("`2You step back from your foe, smile, and hit the switch on your One-Shot Teleporter.  One obligatory blinding flash of light and pain later, you're standing in the middle of an Outpost!`n`n");
			// $session['user']['specialinc'] = "";
// }
function improbablestuff_run(){
	global $session;
	$from = httpget("from");
	$use = httpget("use");
	page_header("Using Supplies");
	if ($use=="smallmedkit"){
		$session['user']['hitpoints']+=20;
		if ($session['user']['hitpoints'] > $session['user']['maxhitpoints']){
			$session['user']['hitpoints'] = $session['user']['maxhitpoints'];
		}
		output("You take a few moments to patch yourself up using a Small Medkit.  Your hitpoints have been restored to %s.",$session[user][hitpoints]);
		set_module_pref("item1number", get_module_pref("item1number")-1);
	}
	if ($use=="largemedkit"){
		$session['user']['hitpoints']+=60;
		if ($session['user']['hitpoints'] > $session['user']['maxhitpoints']){
			$session['user']['hitpoints'] = $session['user']['maxhitpoints'];
		}
		output("You take a few moments to patch yourself up using a Large Medkit.  Your hitpoints have been restored to %s.",$session[user][hitpoints]);
		set_module_pref("item2number", get_module_pref("item2number")-1);
	}
	if ($use=="energydrink"){;
		increment_module_pref("nutrition", 5, "staminafood");
		require_once("modules/staminasystem/lib/lib.php");
		addstamina(25000);
		set_module_pref("item3number", get_module_pref("item3number")-1);
		output("You unscrew the cap on your Energy Drink and gulp it down.`n`nYou gain some Stamina!");
	}
	if ($use=="nicotinegum"){
		$addiction = get_module_pref("addiction","smoking");
		$betweensmokes = (250-$addiction);
		set_module_pref("betweensmokes",$betweensmokes,"smoking");
		apply_buff("smoking",array(
			"allowinpvp"=>1,
			"allowintrain"=>1,
			"rounds"=>-1,
			"schema"=>"module-smoking",
		));
		set_module_pref("item4number", get_module_pref("item4number")-1);
		output("You chomp down on your Nicotine Gum, and you feel the addiction shakes fade away.");
	}
	if ($use=="repellantspray"){
		$encounter = get_module_pref("encounterchance","worldmapen");
		$buffvalue = get_module_pref("repellantbuff");
		$encounter-=25;
		$buffvalue-=10;
		$buffvalue = $buffvalue/100;
		if ($encounter<0){
			$encounter=0;
		}
		apply_buff('is5', array(
			"name"=>"`^Repellant Spray",
			"rounds"=>-1,
			"badguyatkmod"=>$buffvalue,
			"badguydefmod"=>$buffvalue,
			"roundmsg"=>"{badguy} can't stand the smell of your Monster Repellant Spray, and doesn't want to get too close!",
			"wearoff"=>"The effects of your Monster Repellant Spray seem to have worn off...`n",
			"schema"=>"module-improbablestuff"
		));
		set_module_pref("encounterchance",$encounter,"worldmapen");
		set_module_pref("repellantbuff",$buffvalue);
		set_module_pref("item10number", get_module_pref("item10number")-1);
		output("You liberally douse yourself with an entire can of Monster Repellant Spray.  For the rest of this game day, your chances of encountering a monster on the Island Map have been reduced by twenty-five per cent, and monsters you do encounter will be reluctant to attack you.  Your current encounter rate is %s percent of normal, which can be affected by terrain type and other factors.  You can continue soaking your skin and clothes in more cans of the stuff if you like!",$encounter);
	}
	if ($use=="potentpill"){
		apply_buff('is4', array(
			"startmsg"=>"`#After swallowing your Power Pill, you feel immediately revitalised!  You gain some Stamina and you are now regenerating some hitpoints!`n",
			"name"=>"`^Power Pill",
			"rounds"=>10,
			"regen"=>"ceil(<maxhitpoints>/10)+1;",
			"effectmsg"=>"The effects of the Power Pill heal you for {damage} points.",
			"wearoff"=>"The Power Pill effects have worn off.`n",
			"schema"=>"module-improbablestuff"
		));
		set_module_pref("item5number", get_module_pref("item5number")-1);
		output("After swalling your Power Pill, you feel immediately revitalised!  You gain some Stamina and will regenerate hitpoints for the next ten rounds of battle!`n");
		require_once("modules/staminasystem/lib/lib.php");
		addstamina(250000);
	}
	if ($use=="rationpack"){
		require_once("modules/staminasystem/lib/lib.php");
		output("You tear open the foil packaging and look inside.`n`nAfter a few moments' contemplation, you let out the heartbroken sigh of every soldier with an empty belly and a full Rat Pack.`n`nThe material inside has been designed to withstand being thrown out of a plane, bounced down a mountain, encased in snow and ice, left out in the sun and/or buried in a swamp for up to three years.  It contains all the essential nutrients required for a soldier to live on for several weeks without dying of malnutrition.  It even comes with dessert in the form of a chocolate bar - one that, according to rumour, has saved the lives of several contestants.  Not for its nutritional value, of which there is none, but for its remarkable bullet-stopping tensile strength.`n`nYou suck thoughtfully on the corner of the material.  Before long it begins to react with your saliva, breaking up and yielding to your teeth, and rewarding you some Stamina as you begin the arduous process of digestion.`n`nIt beats starving.  Just.");
		increment_module_pref("item11number",-1);
		increment_module_pref("fullness", 40, "staminafood");
		increment_module_pref("nutrition", 30, "staminafood");
		increment_module_pref("fat", 10, "staminafood");
		addstamina(100000);
		$full = get_module_pref("fullness", "staminafood");
		if ($full < 0){
			output("`n`nYou still feel as though you haven't eaten in days.");
		}
		if ($full >= 0 && $full < 50){
			output("`n`nYou feel a little less hungry.");
		}
		if ($full >= 50 && $full < 100){
			output("`n`nYou still feel as though you've got room for more!");
		}
		if ($full >= 100){
			output("`n`nYou're stuffed!  You feel as though you can't possibly eat anything more today.");
		}
	}
	if ($use=="teleporter"){
		$to=httpget("to");
		if ($to==""){
			if (httpget("bomb")==1){
				output("You light the fuse on your Improbability Bomb.  Before you have the chance to toss it at your enemy, it writhes and shifts in your hand, turning into a One-Shot Teleporter - with its relocation matrix already enabled!  ");
			} else {
				output("You press the Big Red Button on your One-Shot Teleporter.  ");
			}
			output("One obligatory blinding flash of light and pain later, you find yourself floating around in empty black nothingness!`n`nA flashing red light and an annoying BEEPing noise from your device insists that you select a destination, and quickly, before you find yourself stuck here or imploded.");
			set_module_pref("item6number", get_module_pref("item6number")-1);
			$vloc = array();
			$vname = getsetting("villagename", LOCATION_FIELDS);
			$vloc[$vname] = "village";
			$vloc = modulehook("validlocation", $vloc);
			ksort($vloc);
			reset($vloc);
			foreach($vloc as $loc=>$val) {
				addnav(array("Go to %s", $loc), "runmodule.php?module=improbablestuff&use=teleporter&to=".htmlentities($loc));
			}
		} else {
			output("You quickly select an outpost from the list.  With a sudden jolt, you find yourself standing in the middle of your chosen outpost!  You look around for your teleporting device, but realise that it must have only teleported you, not itself.  What a piece of junk.");
			$session['user']['location']=$to;
			$session['user']['specialinc'] = "";
			addnav("Back to the Outpost","village.php");
		}
	}
	if ($use=="kittencard"){
		//expansion, possibly for S3 - more types of kittens for variable Favour rewards.  Ginger ones are her favourite.
		output("`0You nervously hold out the greetings card.  `\$The Watcher`0 looks down at your offering.`n`nShe smiles, taking the card and placing it neatly on her desk with the others.  \"`7Aren't you just `iadorable`i?`0\" she says.  You're not sure whether she's talking about you or the kitten.`n`nYou gain `\$30`0 Favour with `\$The Watcher!`0`n`n");
		$session['user']['deathpower']+=30;
		increment_module_pref("item13number",-1);
		addnav("Yay for kittens!","graveyard.php?op=question");
	}
	//Send them back where they came from
	if ($from=="village"){
		addnav("Back to the Outpost","village.php");
	}
	if ($from=="forest"){
		addnav("Back to the Jungle","forest.php");
	}
	if ($from=="worldnav"){
		addnav("Back to the World Map","runmodule.php?module=worldmapen&op=continue");
	}
	page_footer();
	return $args;
}
?>