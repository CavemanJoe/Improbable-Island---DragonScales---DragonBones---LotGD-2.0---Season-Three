<?php


require_once("modules/scrapbots/lib.php");
require_once("modules/staminasystem/lib/lib.php");
$botid = httpget("bot");
$playerscrap = scrapbots_get_player_scrap();

$sql = "SELECT id,owner,name,activated,hitpoints,brains,brawn,briskness,junglefighter,retreathp FROM ".db_prefix("scrapbots")." WHERE id = $botid";
$result = db_query($sql);
$bot=db_fetch_assoc($result);

//process repairs
if (httpget("sub")=="repair"){
	$reps = httpget("reps");
	for ($i=0; $i < $reps; $i++){
		$bot['hitpoints'] += 10;
		$metalwork = process_action("Metalworking");
		debug("Repairing");
	}
	if ($bot['hitpoints'] >= ($bot['brawn']*10)){
		output("You get to work with your welder, and after a little while your ScrapBot is fully repaired and ready to rock!`n`n");
		$bot['hitpoints'] = ($bot['brawn']*10);
	} else {
		output("You get to work with your welder, and after a few minutes you manage to repair some of the damage.`n`n");
	}
	if ($metalwork['lvlinfo']['levelledup']==true){
		output("`n`c`b`0You gained a level in Metalworking!  You are now level %s!  This action will cost fewer Stamina points now, so you can grind and cut and weld more each day!`b`c`n",$return['lvlinfo']['newlvl']);
	}
	$sql="UPDATE " . db_prefix("scrapbots") . " SET hitpoints = ".$bot['hitpoints']." WHERE id = $botid";
	db_query($sql);
}

//process upgrades
if (httpget("sub")=="upgrade"){
	$cat = httpget("cat");
	$lvl = httpget("lvl");
	$sol = 0;
	$met = 0;
	$pro = 0;
	switch ($cat){
		case "brains":
			switch ($lvl){
				case 2:
				case 3:
				case 4:
				case 5:
					$playerscrap['data']['rareitems'][4] -= 1;
					break;
				case 6:
				case 7:
				case 8:
				case 9:
					$playerscrap['data']['rareitems'][4] -= 2;
					break;
				case 10:
					$playerscrap['data']['rareitems'][4] -= 3;
					break;
			}
			output("You have successfully upgraded your ScrapBot's Brains to Level %s!`n`n",$lvl);
			$sql="UPDATE " . db_prefix("scrapbots") . " SET brains = $lvl WHERE id = $botid";
			db_query($sql);
			$sol = 1;
			$pro = 1;
		break;
		case "brawn":
			switch ($lvl){
				case 2:
					$playerscrap['data']['veryrareitems'][0] -= 1;
					$met = 1;
					break;
				case 3:
				case 4:
					$playerscrap['data']['veryrareitems'][0] -= 1;
					$playerscrap['data']['veryrareitems'][2] -= 1;
					$met = 1;
					$sol = 1;
					break;
				case 5:
					$playerscrap['data']['veryrareitems'][0] -= 2;
					$playerscrap['data']['rareitems'][6] -= 1;
					$met = 1;
					$sol = 1;
					break;
				case 6:
					$playerscrap['data']['veryrareitems'][0] -= 3;
					$met = 1;
					break;
				case 7:
					$playerscrap['data']['veryrareitems'][0] -= 1;
					$playerscrap['data']['rareitems'][6] -= 1;
					$playerscrap['data']['veryrareitems'][2] -= 1;
					$met = 1;
					$sol = 1;
					break;
				case 8:
					$playerscrap['data']['veryrareitems'][0] -= 2;
					$playerscrap['data']['rareitems'][6] -= 1;
					$playerscrap['data']['veryrareitems'][2] -= 1;
					$met = 1;
					$sol = 1;
					break;
				case 9:
					$playerscrap['data']['veryrareitems'][0] -= 4;
					$met = 1;
					break;
				case 10:
					$playerscrap['data']['veryrareitems'][0] -= 4;
					$playerscrap['data']['rareitems'][6] -= 1;
					$playerscrap['data']['veryrareitems'][2] -= 1;
					$met = 1;
					$sol = 1;
					$pro = 1;
					break;
			}
			$hitpoints = $lvl * 10;
			output("You have successfully upgraded your ScrapBot's Brawn to Level %s!`n`n",$lvl);
			$sql="UPDATE " . db_prefix("scrapbots") . " SET brawn = $lvl, hitpoints = $hitpoints WHERE id = $botid";
			db_query($sql);
		break;
		case "briskness":
			switch ($lvl){
				case 2:
				case 3:
					$playerscrap['data']['rareitems'][6] -= 1;
					$sol = 1;
					break;
				case 4:
				case 5:
				case 6:
					$playerscrap['data']['rareitems'][6] -= 1;
					$playerscrap['data']['rareitems'][12] -= 1;
					$sol = 1;
					$met = 1;
					break;
				case 7:
				case 8:
					$playerscrap['data']['rareitems'][6] -= 2;
					$playerscrap['data']['rareitems'][12] -= 1;
					$sol = 1;
					$met = 1;
					break;
				case 9:
				case 10:
					$playerscrap['data']['rareitems'][6] -= 2;
					$playerscrap['data']['rareitems'][12] -= 2;
					$sol = 1;
					$met = 1;
					break;
			}
			output("You have successfully upgraded your ScrapBot's Briskness to Level %s!`n`n",$lvl);
			$sql="UPDATE " . db_prefix("scrapbots") . " SET briskness = $lvl WHERE id = $botid";
			db_query($sql);
		break;
	}
	if ($sol == 1){
		$solder = process_action("Soldering");
	}
	if ($pro == 1){
		$program = process_action("Programming");
	}
	if ($met == 1){
		$metalwork = process_action("Metalworking");
	}
	
	if ($solder['lvlinfo']['levelledup']==true){
		output("`n`c`b`0You gained a level in Soldering!  You are now level %s!  This action will cost fewer Stamina points now, so you can solder up more circuitry and wiring harnesses each day!`b`c`n",$solder['lvlinfo']['newlvl']);
	}
	if ($program['lvlinfo']['levelledup']==true){
		output("`n`c`b`0You gained a level in Programming!  You are now level %s!  This action will cost fewer Stamina points now, so you can geek out even more every day!`b`c`n",$program['lvlinfo']['newlvl']);
	}
	if ($metalwork['lvlinfo']['levelledup']==true){
		output("`n`c`b`0You gained a level in Metalworking!  You are now level %s!  This action will cost fewer Stamina points now, so you can grind and cut and weld more each day!`b`c`n",$metalwork['lvlinfo']['newlvl']);
	}	
	
	set_module_pref("scrap", serialize($playerscrap), "scrapbots");
}



$sql = "SELECT id,owner,name,activated,hitpoints,brains,brawn,briskness,junglefighter,retreathp FROM ".db_prefix("scrapbots")." WHERE id = $botid";
$result = db_query($sql);
$bot=db_fetch_assoc($result);

output("You are now managing the ScrapBot with CPUID %s, otherwise known as %s.`n`n",$bot['id'],$bot['name']);

if (httpget("sub") == "activate"){
	output("You tentatively press the switch to activate your ScrapBot.  Within moments it lurches to life, servo arms flailing wildly.  You feel so proud of little %s!`n`n",$bot['name']);
	$sql="UPDATE " . db_prefix("scrapbots") . " SET activated = 1 WHERE id = $botid";
	db_query($sql);
	$bot['activated'] = 1;
}

if (httppost("pct") > 0 && httppost("pct") < 100){
	$pct = httppost("pct");
	$sql="UPDATE " . db_prefix("scrapbots") . " SET retreathp = $pct WHERE id = $botid";
	db_query($sql);
	debug($pct);
	output("The ScrapBot has been programmed to flee if it has less than %s%% of its maximum hitpoints remaining.`n`n",$pct);
	$bot['retreathp'] = $pct;
}

if (httpget("sub") == "junglefight"){
	if (httpget("setting")==1){
		output("This ScrapBot has now been programmed to frolic around the Jungle while you sleep, beating up everything it comes across.  Be sure to check it daily for any repairs it might need!`n`n");
		$sql="UPDATE " . db_prefix("scrapbots") . " SET junglefighter = 1 WHERE id = $botid";
		db_query($sql);
		$bot['junglefighter'] = 1;
	} elseif (httpget("setting")==0){
		if ($bot['brains'] < 4){
			output("This ScrapBot has now been instructed not to venture into the Jungle.`n`n");
		} else {
			output("This ScrapBot has now been programmed to Scavenge for Scrap instead of fighting in the Jungle.`n`n");
		}
		$sql="UPDATE " . db_prefix("scrapbots") . " SET junglefighter = 0 WHERE id = $botid";
		db_query($sql);
		$bot['junglefighter'] = 0;
	}
}

if ($bot['activated']==0){
	addnav("Programming");
	addnav("Activate this ScrapBot","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=activate");
	output("This ScrapBot is not yet activated.  Remember that once activated, ScrapBots cannot be deactivated.`n`n");
}

if ($bot['junglefighter'] == 0 && $bot['brains'] >= 3){
	addnav("Programming");
	addnav("Program this ScrapBot to fight in the Jungle","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=junglefight&setting=1");
}

if ($bot['junglefighter'] == 1 && $bot['brains'] >= 4){
	addnav("Programming");
	addnav("Program this ScrapBot to scavenge in the Scrapyard","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=junglefight&setting=0");
} elseif ($bot['junglefighter'] == 1){
	addnav("Programming");
	addnav("Deactivate this ScrapBot's Jungle Fighting subroutine","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=junglefight&setting=0");
}

if ($bot['brains'] >= 2){
	rawoutput("<form action='runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=setretreathp' method='POST'>");
	rawoutput("This ScrapBot should flee when it has less than <input name='pct' width='5' value='".$bot['retreathp']."'>% of its maximum hitpoints remaining.<br />");
	rawoutput("<input type='submit' class='button' value='".translate_inline("Set")."'>");
	rawoutput("</form>");
	addnav("", "runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=setretreathp");
}

if ($bot['brains'] != 10 || $bot['brawn'] != 10 || $bot['briskness'] != 10){
	output("`0`bUpgrades`b`n`n");
	//show available upgrades
	//brains
	if ($bot['brains'] == 1){
		output("You can upgrade this ScrapBot's Brains to Level 2 using a ROM Chip and your Soldering and Programming skills.`nUpgrading to Level 2 will give this ScrapBot the know-how to get out of a fight if it's too badly damaged.  You'll be able to set the hitpoints level at which the ScrapBot will attempt to escape.`nThe odds of escaping successfully depend largely upon the ScrapBot's Briskness value.");
		if ($playerscrap['data']['rareitems'][4] >= 1){
			addnav("Upgrades");
			addnav("Upgrade Brains","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=upgrade&cat=brains&lvl=2");
		} else {
			output("Unfortunately you don't have a spare ROM Chip to hand.`n");
		}
	}
	if ($bot['brains'] == 2){
		output("You can upgrade this ScrapBot's Brains to Level 3 using a ROM Chip and your Soldering and Programming skills.`nUpgrading to Level 3 will allow this ScrapBot to fight in the Jungle for you.  Each morning, it'll bring you the Requisition it earns.  Obviously `\$The Watcher`0 doesn't pay as much for creatures killed by ScrapBots, as they only attack the smaller jungle creatures.`nThe maximum number of fights the bot engages in depends on its Briskness level.  ScrapBots take damage from Jungle fights just like they do from other ScrapBots, so you should set a retreat hitpoint percentage before sending your ScrapBot out to fight - or it might not come back at all.`n");
		if ($playerscrap['data']['rareitems'][4] >= 1){
			addnav("Upgrades");
			addnav("Upgrade Brains","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=upgrade&cat=brains&lvl=3");
		} else {
			output("Unfortunately you don't have a spare ROM Chip to hand.`n");
		}
	}
	if ($bot['brains'] == 3){
		output("You can upgrade this ScrapBot's Brains to Level 4 using a ROM Chip and your Soldering and Programming skills.`nUpgrading to Level 4 will allow this ScrapBot to optionally Scavenge common items in the ScrapYard for you, rather than fighting in the Jungle.  Each morning it will bring you what it finds.  Higher Briskness levels allow the Bot to work faster, resulting in more Scrap.`n");
		if ($playerscrap['data']['rareitems'][4] >= 1){
			addnav("Upgrades");
			addnav("Upgrade Brains","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=upgrade&cat=brains&lvl=4");
		} else {
			output("Unfortunately you don't have a spare ROM Chip to hand.`n");
		}
	}
	if ($bot['brains'] == 4){
		output("You can upgrade this ScrapBot's Brains to Level 5 using a ROM Chip and your Soldering and Programming skills.`nUpgrading to Level 5 will allow this ScrapBot to self-repair.  Its hitpoints will automatically be restored to their full capacity overnight.`n");
		if ($playerscrap['data']['rareitems'][4] >= 1){
			addnav("Upgrades");
			addnav("Upgrade Brains","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=upgrade&cat=brains&lvl=5");
		} else {
			output("Unfortunately you don't have a spare ROM Chip to hand.`n");
		}
	}
	if ($bot['brains'] == 5){
		output("You can upgrade this ScrapBot's Brains to Level 6 using two ROM Chips and your Soldering and Programming skills.`nUpgrading to Level 6 will allow this ScrapBot to reprogram other ScrapBots it comes across in a battle.  If an enemy ScrapBot is reprogrammed, it immediately joins your army.`n");
		if ($playerscrap['data']['rareitems'][4] >= 2){
			addnav("Upgrades");
			addnav("Upgrade Brains","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=upgrade&cat=brains&lvl=6");
		} else {
			output("Unfortunately you don't have a spare ROM Chip to hand.`n");
		}
	}
	if ($bot['brains'] == 6){
		output("You can upgrade this ScrapBot's Brains to Level 7 using two ROM Chips and your Soldering and Programming skills.`nUpgrading to Level 7 will increase the accuracy of this ScrapBot's Reprogramming routine, dramatically increasing the chances of successfully reprogramming an enemy ScrapBot.`n");
		if ($playerscrap['data']['rareitems'][4] >= 2){
			addnav("Upgrades");
			addnav("Upgrade Brains","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=upgrade&cat=brains&lvl=7");
		} else {
			output("Unfortunately you don't have two spare ROM Chips to hand.`n");
		}
	}
	if ($bot['brains'] == 7){
		output("You can upgrade this ScrapBot's Brains to Level 8 using two ROM Chips and your Soldering and Programming skills.`nUpgrading to Level 8 will increase the accuracy of this ScrapBot's Reprogramming routine, dramatically increasing the chances of successfully reprogramming an enemy ScrapBot.`n");
		if ($playerscrap['data']['rareitems'][4] >= 2){
			addnav("Upgrades");
			addnav("Upgrade Brains","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=upgrade&cat=brains&lvl=8");
		} else {
			output("Unfortunately you don't have two spare ROM Chips to hand.`n");
		}
	}
	if ($bot['brains'] == 8){
		output("You can upgrade this ScrapBot's Brains to Level 9 using two ROM Chips and your Soldering and Programming skills.`nUpgrading to Level 9 will increase the accuracy of this ScrapBot's Reprogramming routine, dramatically increasing the chances of successfully reprogramming an enemy ScrapBot.`n");
		if ($playerscrap['data']['rareitems'][4] >= 2){
			addnav("Upgrades");
			addnav("Upgrade Brains","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=upgrade&cat=brains&lvl=9");
		} else {
			output("Unfortunately you don't have two spare ROM Chips to hand.`n");
		}
	}
	if ($bot['brains'] == 9){
		output("You can upgrade this ScrapBot's Brains to Level 10 using three ROM Chips and your Soldering and Programming skills.`nUpgrading to Level 10 will increase the accuracy of this ScrapBot's Reprogramming routine, dramatically increasing the chances of successfully reprogramming an enemy ScrapBot.`n");
		if ($playerscrap['data']['rareitems'][4] >= 3){
			addnav("Upgrades");
			addnav("Upgrade Brains","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=upgrade&cat=brains&lvl=10");
		} else {
			output("Unfortunately you don't have three spare ROM Chips to hand.`n");
		}
	}
	if ($bot['brains'] == 10){
		output("No updates available for Brains`n");
	}
	output("`n");

	//brawn===================================================================================================================================
	//brawn===================================================================================================================================
	//brawn===================================================================================================================================

	if ($bot['brawn'] == 1){
		output("You can upgrade this ScrapBot's Brawn to Level 2 using a plate of Armour and your Metalworking skill.`nUpgrading a ScrapBot's Brawn will allow it to hit harder and take more damage.`n");
		if ($playerscrap['data']['veryrareitems'][0] >= 1){
			addnav("Upgrades");
			addnav("Upgrade Brawn","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=upgrade&cat=brawn&lvl=2");
		} else {
			output("Unfortunately you don't have any spare Armour.`n");
		}
	}
	if ($bot['brawn'] == 2){
		output("You can upgrade this ScrapBot's Brawn to Level 3 using a plate of Armour, a Servo Limb and your Metalworking and Soldering skills.`nUpgrading a ScrapBot's Brawn will allow it to hit harder and take more damage.`n");
		if ($playerscrap['data']['veryrareitems'][0] >= 1 && $playerscrap['data']['veryrareitems'][2] >= 1){
			addnav("Upgrades");
			addnav("Upgrade Brawn","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=upgrade&cat=brawn&lvl=3");
		} else {
			output("Unfortunately you don't have enough spare parts.`n");
		}
	}
	if ($bot['brawn'] == 3){
		output("You can upgrade this ScrapBot's Brawn to Level 4 using a plate of Armour, a Servo Limb and your Metalworking and Soldering skills.`nUpgrading a ScrapBot's Brawn will allow it to hit harder and take more damage.`n");
		if ($playerscrap['data']['veryrareitems'][0] >= 1 && $playerscrap['data']['veryrareitems'][2] >= 1){
			addnav("Upgrades");
			addnav("Upgrade Brawn","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=upgrade&cat=brawn&lvl=4");
		} else {
			output("Unfortunately you don't have enough spare parts.`n");
		}
	}
	if ($bot['brawn'] == 4){
		output("You can upgrade this ScrapBot's Brawn to Level 5 using a two plates of Armour, a Battery and your Metalworking and Soldering skills.`nUpgrading a ScrapBot's Brawn will allow it to hit harder and take more damage.`n");
		if ($playerscrap['data']['veryrareitems'][0] >= 2 && $playerscrap['data']['rareitems'][6] >= 1){
			addnav("Upgrades");
			addnav("Upgrade Brawn","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=upgrade&cat=brawn&lvl=5");
		} else {
			output("Unfortunately you don't have enough spare parts.`n");
		}
	}
	if ($bot['brawn'] == 5){
		output("You can upgrade this ScrapBot's Brawn to Level 6 using three plates of Armour and your Metalworking skill.`nUpgrading a ScrapBot's Brawn will allow it to hit harder and take more damage.`n");
		if ($playerscrap['data']['veryrareitems'][0] >= 3){
			addnav("Upgrades");
			addnav("Upgrade Brawn","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=upgrade&cat=brawn&lvl=6");
		} else {
			output("Unfortunately you don't have enough spare parts.`n");
		}
	}
	if ($bot['brawn'] == 6){
		output("You can upgrade this ScrapBot's Brawn to Level 7 using a plate of Armour, a Servo Limb, a Battery and your Metalworking and Soldering skills.`nUpgrading a ScrapBot's Brawn will allow it to hit harder and take more damage.`n");
		if ($playerscrap['data']['veryrareitems'][0] >= 1 && $playerscrap['data']['rareitems'][6] >= 1 && $playerscrap['data']['veryrareitems'][2] >= 1){
			addnav("Upgrades");
			addnav("Upgrade Brawn","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=upgrade&cat=brawn&lvl=7");
		} else {
			output("Unfortunately you don't have enough spare parts.`n");
		}
	}
	if ($bot['brawn'] == 7){
		output("You can upgrade this ScrapBot's Brawn to Level 8 using two plates of Armour, a Servo Limb, a Battery and your Metalworking and Soldering skills.`nUpgrading a ScrapBot's Brawn will allow it to hit harder and take more damage.`n");
		if ($playerscrap['data']['veryrareitems'][0] >= 2 && $playerscrap['data']['rareitems'][6] >= 1 && $playerscrap['data']['veryrareitems'][2] >= 1){
			addnav("Upgrades");
			addnav("Upgrade Brawn","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=upgrade&cat=brawn&lvl=8");
		} else {
			output("Unfortunately you don't have enough spare parts.`n");
		}
	}
	if ($bot['brawn'] == 8){
		output("You can upgrade this ScrapBot's Brawn to Level 9 using four plates of Armour and your Metalworking skill.`nUpgrading a ScrapBot's Brawn will allow it to hit harder and take more damage.`n");
		if ($playerscrap['data']['veryrareitems'][4] >= 1){
			addnav("Upgrades");
			addnav("Upgrade Brawn","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=upgrade&cat=brawn&lvl=9");
		} else {
			output("Unfortunately you don't have enough spare parts.`n");
		}
	}
	if ($bot['brawn'] == 9){
		output("You can upgrade this ScrapBot's Brawn to Level 8 using four plates of Armour, a Servo Limb, a Battery and your Metalworking, Soldering and Programming skills.`nUpgrading a ScrapBot's Brawn will allow it to hit harder and take more damage.`n");
		if ($playerscrap['data']['veryrareitems'][0] >= 4 && $playerscrap['data']['rareitems'][6] >= 1 && $playerscrap['data']['veryrareitems'][2] >= 1){
			addnav("Upgrades");
			addnav("Upgrade Brawn","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=upgrade&cat=brawn&lvl=10");
		} else {
			output("Unfortunately you don't have enough spare parts.`n");
		}
	}
	if ($bot['brawn'] == 10){
		output("No updates available for Brawn`n");
	}
	output("`n");

	//briskness===================================================================================================================================
	//briskness===================================================================================================================================
	//briskness===================================================================================================================================


	if ($bot['briskness'] == 1){
		output("You can upgrade this ScrapBot's Briskness to Level 2 using a Battery and your Soldering skill.`nUpgrading a ScrapBot's Briskness allows it to move faster, performing more actions per day and fleeing from battle (or chasing down fleeing enemy ScrapBots) more easily.`n");
		if ($playerscrap['data']['rareitems'][6] >= 1){
			addnav("Upgrades");
			addnav("Upgrade Briskness","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=upgrade&cat=briskness&lvl=2");
		} else {
			output("Unfortunately you don't have enough spare parts.`n");
		}
	}
	if ($bot['briskness'] == 2){
		output("You can upgrade this ScrapBot's Briskness to Level 3 using a Battery and your Soldering skill.`nUpgrading a ScrapBot's Briskness allows it to move faster, performing more actions per day and fleeing from battle (or chasing down fleeing enemy ScrapBots) more easily.`n");
		if ($playerscrap['data']['rareitems'][6] >= 1){
			addnav("Upgrades");
			addnav("Upgrade Briskness","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=upgrade&cat=briskness&lvl=3");
		} else {
			output("Unfortunately you don't have enough spare parts.`n");
		}
	}
	if ($bot['briskness'] == 3){
		output("You can upgrade this ScrapBot's Briskness to Level 4 using a Battery, a Drive Motor and your Soldering and Metalwork skills.`nUpgrading a ScrapBot's Briskness allows it to move faster, performing more actions per day and fleeing from battle (or chasing down fleeing enemy ScrapBots) more easily.`n");
		if ($playerscrap['data']['rareitems'][6] >= 1 && $playerscrap['data']['rareitems'][12] >= 1){
			addnav("Upgrades");
			addnav("Upgrade Briskness","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=upgrade&cat=briskness&lvl=4");
		} else {
			output("Unfortunately you don't have enough spare parts.`n");
		}
	}
	if ($bot['briskness'] == 4){
		output("You can upgrade this ScrapBot's Briskness to Level 5 using a Battery, a Drive Motor and your Soldering and Metalwork skills.`nUpgrading a ScrapBot's Briskness allows it to move faster, performing more actions per day and fleeing from battle (or chasing down fleeing enemy ScrapBots) more easily.`n");
		if ($playerscrap['data']['rareitems'][6] >= 1 && $playerscrap['data']['rareitems'][12] >= 1){
			addnav("Upgrades");
			addnav("Upgrade Briskness","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=upgrade&cat=briskness&lvl=5");
		} else {
			output("Unfortunately you don't have enough spare parts.`n");
		}
	}
	if ($bot['briskness'] == 5){
		output("You can upgrade this ScrapBot's Briskness to Level 6 using a Battery, a Drive Motor and your Soldering and Metalwork skills..`nUpgrading a ScrapBot's Briskness allows it to move faster, performing more actions per day and fleeing from battle (or chasing down fleeing enemy ScrapBots) more easily.`n");
		if ($playerscrap['data']['rareitems'][6] >= 1 && $playerscrap['data']['rareitems'][12] >= 1){
			addnav("Upgrades");
			addnav("Upgrade Briskness","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=upgrade&cat=briskness&lvl=6");
		} else {
			output("Unfortunately you don't have enough spare parts.`n");
		}
	}
	if ($bot['briskness'] == 6){
		output("You can upgrade this ScrapBot's Briskness to Level 7 using two Batteries, a Drive Motor and your Soldering and Metalwork skills.`nUpgrading a ScrapBot's Briskness allows it to move faster, performing more actions per day and fleeing from battle (or chasing down fleeing enemy ScrapBots) more easily.`n");
		if ($playerscrap['data']['rareitems'][6] >= 2 && $playerscrap['data']['rareitems'][12] >= 1){
			addnav("Upgrades");
		addnav("Upgrade Briskness","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=upgrade&cat=briskness&lvl=7");
		} else {
			output("Unfortunately you don't have enough spare parts.`n");
		}
	}
	if ($bot['briskness'] == 7){
		output("You can upgrade this ScrapBot's Briskness to Level 8 using two Batteries, a Drive Motor and your Soldering and Metalwork skills.`nUpgrading a ScrapBot's Briskness allows it to move faster, performing more actions per day and fleeing from battle (or chasing down fleeing enemy ScrapBots) more easily.`n");
		if ($playerscrap['data']['rareitems'][6] >= 2 && $playerscrap['data']['rareitems'][12] >= 1){
			addnav("Upgrades");
			addnav("Upgrade Briskness","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=upgrade&cat=briskness&lvl=8");
		} else {
			output("Unfortunately you don't have enough spare parts.`n");
		}
	}
	if ($bot['briskness'] == 8){
		output("You can upgrade this ScrapBot's Briskness to Level 9 using two Batteries, two Drive Motors and your Soldering and Metalwork skills.`nUpgrading a ScrapBot's Briskness allows it to move faster, performing more actions per day and fleeing from battle (or chasing down fleeing enemy ScrapBots) more easily.`n");
		if ($playerscrap['data']['rareitems'][6] >= 2 && $playerscrap['data']['rareitems'][12] >= 2){
			addnav("Upgrades");
			addnav("Upgrade Briskness","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=upgrade&cat=briskness&lvl=9");
		} else {
			output("Unfortunately you don't have enough spare parts.`n");
		}
	}
	if ($bot['briskness'] == 9){
		output("You can upgrade this ScrapBot's Briskness to Level 10 using two Batteries, two Drive Motors and your Soldering and Metalwork skills.`nUpgrading a ScrapBot's Briskness allows it to move faster, performing more actions per day and fleeing from battle (or chasing down fleeing enemy ScrapBots) more easily.`n");
		if ($playerscrap['data']['rareitems'][6] >= 2 && $playerscrap['data']['rareitems'][12] >= 2){
			addnav("Upgrades");
			addnav("Upgrade Briskness","runmodule.php?module=scrapbots&op=managescrapbot&bot=$botid&sub=upgrade&cat=briskness&lvl=10");
		} else {
			output("Unfortunately you don't have enough spare parts.`n");
		}
	}
	if ($bot['briskness'] == 10){
		output("No updates available for Briskness`n");
	}
	output("`n");
}
?>