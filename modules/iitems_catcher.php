<?php

//This is for iitems with functionality impossible to replicate in the native iitems system.

function iitems_catcher_getmoduleinfo(){
	$info=array(
		"name"=>"IItems - Catcher Script",
		"version"=>"2009-06-14",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"IItems",
		"download"=>"",
	);
	return $info;
}

function iitems_catcher_install(){
	module_addhook("iitems-use-item");
	module_addhook("newday");
	return true;
}

function iitems_catcher_uninstall(){
	return true;
}

function iitems_catcher_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "iitems-use-item":
			if ($args['player']['itemid']=="zapgrenade"){
				apply_buff('zapgrenade', array(
					"startmsg"=>"`#You pull the pin on your grenade and toss it at {badguy}`#, shielding your eyes.  After a blinding flash, your foe is left dazed and confused!",
					"name"=>"`^ZAP Grenade",
					"rounds"=>e_rand(3,7),
					"badguyatkmod"=>0.1,
					"badguydefmod"=>0.1,
					"roundmsg"=>"{badguy} is blinded, deafened and thoroughly confused, and flails wildly while you pummel it!",
					"wearoff"=>"{badguy}`# feels some coherence return, and lunges at you!",
					"expireafterfight"=>1,
					"schema"=>"iitems-catcher"
				));
			}
			if ($args['player']['itemid']=="nicotinegum"){
				$addiction = get_module_pref("addiction","smoking");
				$betweensmokes = (250-$addiction);
				set_module_pref("betweensmokes",$betweensmokes,"smoking");
				apply_buff("smoking",array(
					"allowinpvp"=>1,
					"allowintrain"=>1,
					"rounds"=>-1,
					"schema"=>"module-smoking",
				));
			}
			if ($args['player']['itemid']=="oneshotteleporter"){
				require_once "modules/iitems/lib/lib.php";
				iitems_discard_item($args['player']['inv_key']);
				redirect("runmodule.php?module=iitems_catcher&item=oneshotteleporter");
			}
			if ($args['player']['itemid']=="energydrink"){
				//handle Nutrition adding without invoking extra Edibles functionality
				increment_module_pref("nutrition",5,"staminafood");
			}
			if ($args['player']['itemid']=="monsterrepellentspray"){
				if (httpget('skill')=="iitems"){
					//handle using the item in a fight
					apply_buff('rspray_fight', array(
						"startmsg"=>"`#You pull out a can of Monster Repellent Spray, and spray it liberally on the enemy!`n",
						"name"=>"`^Repellent Spray Attack",
						"rounds"=>10,
						"badguyatkmod"=>0.4,
						"badguydefmod"=>0.4,
						"roundmsg"=>"{badguy} is coughing, choking and all runny-nosed, and cannot attack or defend as effectively!",
						"wearoff"=>"The effects of your Monster Repellent Spray seem to have worn off...`n",
						"expireafterfight"=>1,
						"schema"=>"iitems-catcher"
					));
				} else {
					//handle using the item outside of a fight
					apply_buff('rspray_normal', array(
						"name"=>"`^Repellent Spray",
						"rounds"=>-1,
						"badguyatkmod"=>0.8,
						"badguydefmod"=>0.8,
						"roundmsg"=>"{badguy} can't stand the smell of your Monster Repellent Spray, and doesn't want to get too close!",
						"schema"=>"iitems-catcher"
					));
					set_module_pref("encounterchance",50,"worldmapen");
					output("You liberally douse yourself with an entire can of Monster Repellent Spray.  For the rest of this game day, your chances of encountering a monster on the Island Map have been halved, and monsters you do encounter will be reluctant to attack you as hard.`n`n");
				}
			}
			if ($args['player']['itemid']=="improbabilitybomb"){
				$effect = e_rand(1,8);
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
					"schema"=>"iitems-catcher"
				));
				switch ($effect){
					case 1:
						apply_buff('ibomb1', array(
							"rounds"=>1,
							"atkmod"=>1,
							"startmsg"=>"`0The bomb bursts into a shower of Requisition tokens!  Blimey, there must be about a thousand of them!  What's more, all these tokens are swirling into the air and nose-diving straight into your pocket.  Result!",
							"schema"=>"iitems-catcher"
						));
						$gold = e_rand(900,1100);
						$session['user']['gold'] += $gold;
						break;
					case 2:
						apply_buff('ibomb2', array(
							"rounds"=>1,
							"atkmod"=>1,
							"startmsg"=>"`0The fuse fizzes and sparks, until eventually... it goes out.  The bomb is gone.  However, there's a tasty, tasty cigarette in its place!  You grab it before your enemy gets the chance.",
							"schema"=>"iitems-catcher"
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
							"schema"=>"iitems-catcher"
						));
						break;
					case 4:
					case 5:
						apply_buff('ibomb6', array(
							"rounds"=>1,
							"atkmod"=>1,
							"startmsg"=>"`0The Improbability Bomb breaks open, bathing you in a cool white light.  When it fades, you feel calm, self-confident and somehow more attractive.  Pretty useless in a combat situation, but hey, it's nice to be feel good about yourself.  You gain some Charm.",
							"schema"=>"iitems-catcher"
						));
						$session['user']['charm']+=1;
						break;
					case 6:
						apply_buff('ibomb7a', array(
							"rounds"=>4,
							"minioncount"=>8,
							"minbadguydamage"=>0,
							"maxbadguydamage"=>5,
							"startmsg"=>"The bomb begins to roll around the theater of combat, bouncing off rocks like a pinball - and firing out showers of white-hot sparks!",
							"effectmsg"=>"`2A glowing spark leaps onto {badguy}, burning it for {damage} points!",
							"schema"=>"iitems-catcher",
							"expireafterfight"=>1,
						));
						apply_buff('ibomb7b', array(
							"rounds"=>4,
							"minioncount"=>8,
							"mingoodguydamage"=>0,
							"maxgoodguydamage"=>5,
							"effectmsg"=>"`4A white-hot spark attaches to you, burning you for {damage} points!",
							"schema"=>"iitems-catcher",
							"wearoff"=>"The bomb fizzles out and sends out one last dying volley of sparks.",
							"expireafterfight"=>1,
						));
						break;
					case 7:
						apply_buff('ibomb8', array(
							"startmsg"=>"`0The bomb uncurls, revealing a little `5Purple Monster!`0",
							"rounds"=>-1,
							"name"=>"`5Purple Monster`0",
							"minioncount"=>1,
							"minbadguydamage"=>5,
							"maxbadguydamage"=>50,
							"effectmsg"=>"`5The Purple Monster leaps towards {badguy} and bites down hard for {damage} damage!`0",
							"schema"=>"iitems-catcher",
							"wearoff"=>"`5The Purple Monster, seeing its business here concluded, disappears with a faint 'pop.'",
							"expireafterfight"=>1,
						));
						break;
					case 8:
						$maxdmg = $session['user']['maxhitpoints']*2;
						if ($maxdmg < 500){
							$maxdmg = 500;
						}
						$mindmg = $session['user']['hitpoints']*0.5;
						if ($maxdmg < 200){
							$maxdmg = 200;
						}
						apply_buff('ibomb9', array(
							"rounds"=>1,
							"minioncount"=>1,
							"mingoodguydamage"=>$mindmg,
							"maxgoodguydamage"=>$maxdmg,
							"effectmsg"=>"`4Before the bomb even leaves your hand, it blows up in your face!  The explosion causes {damage} points!",
							"schema"=>"iitems-catcher",
							"expireafterfight"=>1,
						));
						break;
				}
			}
			if ($args['player']['itemid']=="cratesniffer"){
				output("`0You thumb the switch on your Crate Sniffer.  It buzzes and hisses for a moment, exhausting its primitive battery sending out a radio ping to nearby Crates.`n`n");
				$crates = unserialize(get_module_setting("crates","iitemcrates"));
				debug($crates);
				$ploc = get_module_pref("worldXYZ","worldmapen");
				list($px, $py, $pz) = explode(",", $ploc);
				
				$pxlow = $px-3;
				$pxhigh = $px+3;
				$pylow = $py-3;
				$pyhigh = $py+3;
				
				if (!is_array($crates)) {
					$crates = array();
				}
				$count = 0;
				foreach($crates AS $key => $vals){
					if ($vals['loc']['x'] >= $pxlow && $vals['loc']['x'] <= $pxhigh && $vals['loc']['y'] >= $pylow && $vals['loc']['y'] <= $pyhigh){
						$count++;
					}
				}
				output("It displays, weakly, the number `\$`b%s`b`0 in dull red LED's before its radio module catches fire.`n`n",$count);
			}
			break;
		case "newday":
			set_module_pref("encounterchance",100,"worldmapen");
			break;
	}
	return $args;
}

function iitems_catcher_run(){
	global $session;
	$item = httpget("item");
	if ($item=="oneshotteleporter"){
		$to=httpget("to");
		if ($to==""){
			page_header("The Void");
			output("You press the button on your One-Shot Teleporter.  One obligatory blinding flash of light and pain later, you find yourself floating around in empty black nothingness!`n`nA flashing red light and an annoying BEEPing noise from your device insists that you select a destination, and quickly, before you find yourself stuck here or imploded.");
			set_module_pref("item6number", get_module_pref("item6number")-1);
			$vloc = array();
			$vname = getsetting("villagename", LOCATION_FIELDS);
			$vloc[$vname] = "village";
			$vloc = modulehook("validlocation", $vloc);
			ksort($vloc);
			reset($vloc);
			addnav("Choose a Destination");
			foreach($vloc as $loc=>$val) {
				addnav(array("Go to %s", $loc), "runmodule.php?module=iitems_catcher&item=oneshotteleporter&to=".htmlentities($loc));
			}
		} else {
			page_header("Back to Reality");
			output("You quickly select an outpost from the list.  With a sudden jolt, you find yourself standing in the middle of your chosen outpost!  You look around for your teleporting device, but realise that it must have only teleported you, not itself.  What a piece of junk.");
			$session['user']['location']=$to;
			$session['user']['specialinc'] = "";
			addnav("Continue");
			addnav("Back to the Outpost","village.php");
		}
	}
	page_footer();
	return true;
}
?>