<?php

/*
=======================================================
SCRAP ARRAYS
Arrays of normal, rare and very rare Scrap items.
=======================================================
*/

function scrapbots_scrapnames(){
	$scrap = array (
		"normalitems" => array (
			0 => "Rusted Shaft",
			1 => "Rusted Steel Plate",
			2 => "Rusted Large Girder",
			3 => "Broken Motherboard",
			4 => "Corrupted ROM Chip",
			5 => "Rusted Servo",
			6 => "Bundle of Wires",
			7 => "Dead Battery",
			8 => "Punctured Wheel",
			9 => "Rusted Wheel",
			10 => "Rusted Small Girder",
			11 => "Broken CMOS Sensor",
			12 => "Glass Milk Bottle",
			13 => "Broken Servo",
			14 => "Broken Drive Motor",
			15 => "Rusted Drive Motor"
		),

		"rareitems" => array (
			0 => "Axle",
			1 => "Steel Plate",
			2 => "Large Girder",
			3 => "Motherboard",
			4 => "ROM Chip",
			5 => "Wiring Harness",
			6 => "Battery",
			7 => "Wheel",
			8 => "Small Girder",
			9 => "CMOS Sensor",
			10 => "Lens",
			11 => "Servo",
			12 => "Drive Motor"
		),

		"veryrareitems" => array (
			0 => "Armour",
			1 => "Chassis",
			2 => "Servo Limb",
			3 => "Camera Eye"
		),
	);
	return($scrap);
}

/*
=======================================================
GET PLAYER'S SCRAP QUANTITIES
Returns array of player's Scrap and their quantities.
=======================================================
*/

function scrapbots_get_player_scrap($userid=false) {
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	$scrap = unserialize(get_module_pref("scrap", "scrapbots", $userid));
	if (!is_array($scrap)) {
		$scrap = array();
		set_module_pref("scrap", serialize($scrap), "scrapbots", $userid);
	}
	$scrap = unserialize(get_module_pref("scrap", "scrapbots", $userid));
	return $scrap;
}

/*
=======================================================
SCAVENGE
Gives a single Scrap item to a player, its rarity dependant on their Scavenging experience.
=======================================================
*/

function scrapbots_scavenge($reps=1) {
	global $session;
	//todo: hook in Stamina system, factor in remaining Stamina and experience
	
	//Get the player's Scrap
	$playerscrap = scrapbots_get_player_scrap();
	for ($i=0; $i<$reps; $i++){
		//Actually, let's just put in a random chance that the player will trip and impale themselves on a rusty bit of pointy scrap
		require_once "modules/staminasystem/lib/lib.php";
		$return = process_action("Scavenging for Scrap");
		if ($return['lvlinfo']['levelledup']==true){
			output("`n`c`b`0You gained a level in Scavenging for Scrap!  You are now level %s!  This action will cost fewer Stamina points now, so you can Scavenge more each day!`b`c`n",$return['lvlinfo']['newlvl']);
		}
		$stamina = get_stamina();
		$failchance = e_rand(1,100);
		
		if ($stamina < 100){
			output("`4You're getting tired.`0  Clambering around on a big pile of pointy, rusted scrap metal while you're half-asleep carries certain risks.`n");
		}
		if ($failchance > $stamina){
			output("`\$This fact is driven home to you as you slip on an oily bit of sheet metal and fall head-first into the pile!`nYou `blose`b some hitpoints!`n`0");
			$hploss = e_rand(0, floor($session['user']['maxhitpoints']/2));
			$session['user']['hitpoints'] -= $hploss;
			if ($session['user']['hitpoints']<=0){
				output("`\$You begin to feel a bit dizzy from the lack of blood.  Before you know it, you've collapsed, and the friendly local Robots have helped themselves to your cash and summoned the Retraining Personnel!`n");
				$session['user']['alive'] = false;
				$session['user']['gold'] = 0;
				addnav("Whoops.");
				addnav("Daily News","news.php");
				blocknav("runmodule.php?module=scrapbots&scavenge=1");
				blocknav("runmodule.php?module=scrapbots&scavenge=10");
				blocknav("village.php");
				break;
			}
		}
		//dummy vals for now
		$normalchance = 100;
		$rarechance = 66;
		$veryrarechance = 99;
		$scrapnames = scrapbots_scrapnames();
		$scavengerarity = e_rand(1,99);
		if ($scavengerarity <= $normalchance){
			//award normal object
			$award = e_rand(0,15);
			$playerscrap['data']['normalitems'][$award]+=1;
			output("`0You rummage through the scrap pile and come up with a `5%s`0.`n",$scrapnames['normalitems'][$award]);
		} elseif ($scavengerarity > $normalchance && $scavengerarity <= $rarechance){
			$award = e_rand(0,12);
			$playerscrap['data']['rareitems'][$award]+=1;
			output("`0You find a rare item!  A pretty nice `5%s`0.`n",$scrapnames['rareitems'][$award]);
		} elseif ($scavengerarity > $rarechance){
			//award very rare item
			$award = e_rand(0,3);
			$playerscrap['data']['veryrareitems'][$award]+=1;
			output("`0You find a very rare item!  This `5%s`0 looks like it's in good nick!`n",$scrapnames['veryrareitems'][$award]);
		}
	}
	set_module_pref("scrap", serialize($playerscrap), "scrapbots");
	return;
}

/*
=======================================================
NEW DAY ROUTINES
Jungle fighters fight in the Jungle and bring back Requisition.
Scavengers scavenge and bring back Scrap.
Healers heal themselves.
=======================================================
*/

function scrapbots_newday_junglefights() {
	global $session;
	$acctid = $session['user']['acctid'];

	$sql = "SELECT id,name,hitpoints,briskness,brawn,retreathp FROM ".db_prefix("scrapbots")." WHERE owner = $acctid && activated = 1 && brains > 2 && junglefighter = 1";
	$result = db_query($sql);
	$fighters = array();
	for ($i=0; $i<db_num_rows($result); $i++){
		$fighters[$i]=db_fetch_assoc($result);
	}
	
	debug($fighters);
	
	if (count($fighters) == 0){
		return;
	}
	
	output("`bYour ScrapBots have been fighting in the Jungle!`b`n");
	
	$totalgold = 0;
	foreach ($fighters as $fighter){
		$totaldamage = 0;
		$gold = 0;
		$monsters = 0;
		debug($fighter);
		for ($i=0; $i < $fighter['briskness']; $i++){
			$monsters++;
			$damage = e_rand(1,12);
			$totaldamage += $damage;
			$gold += e_rand(1,15);
			$fighter['hitpoints'] -= $damage;
			if ($fighter['hitpoints'] <= 0){
				$destroyed = 1;
				break;
			}
			if ($fighter['hitpoints'] < (($fighter['brawn']/10)*$fighter['retreathp'])){
				if ($monsters > 1){
					$monstertext = $monsters." monsters before fleeing";
				} else {
					$monstertext = $monsters." monster before fleeing";
				}
				$fled = 1;
				break;
			} else {
				if ($monsters > 1){
					$monstertext = $monsters." monsters";
				} else {
					$monstertext = $monsters." monster";
				}
			}
		}
		$id = $fighter['id'];
		$hitpoints = $fighter['hitpoints'];
		if ($destroyed == 1){
			output("%s didn't come back.  With a heavy heart, you reason that it must have been destroyed.`n",$fighter['name']);
			$sql = "DELETE FROM ".db_prefix("scrapbots")." WHERE id = $id";
			db_query($sql);
		} else {
			output("%s killed %s, and bought back %s Requisition!  It took %s damage in the process, taking its hitpoints down to %s.`n",$fighter['name'], $monstertext, $gold, $totaldamage, $fighter['hitpoints']);
			$session['user']['gold'] += $gold;
			$totalgold =+ $gold;
			$sql = "UPDATE ".db_prefix("scrapbots")." SET hitpoints = $hitpoints WHERE id = $id";
			debug($sql);
			db_query($sql);
		}
	}
	output("In total, your ScrapBots bought you %s Requisition this morning.`n`n",$totalgold);
}

function scrapbots_newday_scavengers() {
	global $session;
	$acctid = $session['user']['acctid'];
	
	$playerscrap = scrapbots_get_player_scrap();
	$scrapnames = scrapbots_scrapnames();

	$sql = "SELECT id,name,briskness FROM ".db_prefix("scrapbots")." WHERE owner = $acctid && activated = 1 && brains > 3 && junglefighter = 0";
	$result = db_query($sql);
	$scavengers = array();
	for ($i=0; $i<db_num_rows($result); $i++){
		$scavengers[$i]=db_fetch_assoc($result);
	}
	
	debug($scavengers);
	
	if (count($scavengers) == 0){
		return;
	}
	
	output("`bSome of your ScrapBots have been Scavenging in the Scrapyard!`b`n");
	
	foreach ($scavengers as $scavenger){
		$foundscrap = array();
		output("%s brought you the following:`n",$scavenger['name']);
		for ($i=0; $i < $scavenger['briskness']; $i++){
			$item = e_rand(0,15);
			$foundscrap[$item] += 1;
		}
		foreach ($foundscrap as $scrap => $qty){
			output("%s: %s`n",$scrapnames['normalitems'][$scrap],$qty);
			$playerscrap['data']['normalitems'][$scrap] += $qty;
		}
		output("Good Scrapbot!`n`n");
	}
	set_module_pref("scrap", serialize($playerscrap), "scrapbots");
}

function scrapbots_newday_healers() {
	global $session;
	$acctid = $session['user']['acctid'];

	$sql = "SELECT id,name,hitpoints,brawn FROM ".db_prefix("scrapbots")." WHERE owner = $acctid && activated = 1 && brains > 4";
	$result = db_query($sql);
	$healers = array();
	for ($i=0; $i<db_num_rows($result); $i++){
		$healers[$i]=db_fetch_assoc($result);
	}
	
	if (count($healers) == 0){
		return;
	}
	
	foreach ($healers as $healer){
		if ($healer['hitpoints'] < ($healer['brawn']*10)){
			$healer['hitpoints'] = ($healer['brawn']*10);
			output("%s repaired itself to full health overnight.`n",$healer['name']);
			$id = $healer['id'];
			$hitpoints = $healer['hitpoints'];
			$sql = "UPDATE ".db_prefix("scrapbots")." SET hitpoints = $hitpoints WHERE id = $id";
			db_query($sql);
		}
	}
	output("`n");
}


/*
=======================================================
LIST PLAYER'S SCRAP QUANTITIES
Outputs a list of player's Scrap items and their quantities.
=======================================================
*/

function scrapbots_list_player_scrap($userid=false) {
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];

	$playerscrap = scrapbots_get_player_scrap($userid);
	$scrapnames = scrapbots_scrapnames();
	
	output("`3`bYour Scrap`b`n");
	$hasthings = 0;
	foreach($scrapnames['normalitems'] AS $id => $item){
		$itemqty = $playerscrap['data']['normalitems'][$id];
		if ($itemqty > 0){
			output("%s: %s`n", $item, $itemqty);
			$hasthings = 1;
		}
	}
	if ($hasthings == 0){
		output("You have no Scrap.`n");
	}
	output("`n");
	
	output("`bYour Spare Parts`b`n");
	$hasthings = 0;
	foreach($scrapnames['rareitems'] AS $id => $item){
		$itemqty = $playerscrap['data']['rareitems'][$id];
		if ($itemqty > 0){
			output("%s: %s`n", $item, $itemqty);
			$hasthings = 1;
		}
	}
	if ($hasthings == 0){
		output("You have no Spare Parts.`n");
	}
	output("`n");
	
	output("`bYour Component Assemblies`b`n");
	$hasthings = 0;
	foreach($scrapnames['veryrareitems'] AS $id => $item){
		$itemqty = $playerscrap['data']['veryrareitems'][$id];
		if ($itemqty > 0){
			output("%s: %s`n", $item, $itemqty);
			$hasthings = 1;
		}
	}
	if ($hasthings == 0){
		output("You have no Component Assemblies.`n");
	}
	output("`0`n");
	return;
}

/*
=======================================================
LIST REQUIREMENTS FOR BASIC SCRAPBOT
Outputs a list the basic requirements for a working ScrapBot.
=======================================================
*/

function scrapbots_list_requirements() {
	global $session;

	if ($session['user']['alive']==0){
		return;
	}	
	
	$playerscrap = scrapbots_get_player_scrap();
	$scrapnames = scrapbots_scrapnames();

	output("`&`bThings you still need for a new ScrapBot`b`n");
	if ($playerscrap['data']['veryrareitems'][0] < 1){
		output("Your ScrapBot won't last ten seconds without Armour, which you can make out of steel plate.`n");
	}
	if ($playerscrap['data']['rareitems'][0] < 2){
		output("You still need a pair of Axles, which can be made from steel rods.`n");
	}
	if ($playerscrap['data']['veryrareitems'][1] < 1){
		output("You still need some sort of Chassis - you should be able to knock one together out of old girders.  One long and two short will do it.`n");
	}
	if ($playerscrap['data']['rareitems'][3] < 1){
		output("You still need a Motherboard.  There's bound to be a broken one or two in the Scrap Pile.`n");
	}
	if ($playerscrap['data']['rareitems'][4] < 1){
		output("You can't build a ScrapBot without at least one ROM Chip.  There'll be some in the Scrap Pile.`n");
	}
	if ($playerscrap['data']['rareitems'][6] < 1){
		output("You'll need a working Battery to power your ScrapBot.  Have a rummage in the Scrap Pile to see if you can find one that can be recharged - you'll need to pay a fee of 40 Requisition to use the charging station, though.`n");
	}
	if ($playerscrap['data']['veryrareitems'][2] < 1){
		output("Your ScrapBot will need some method of defending itself - it should be quite trivial to knock up some kind of limb out of an old servo, a small girder and some steel plate.`n");
	}
	if ($playerscrap['data']['rareitems'][7] < 4){
		output("Your ScrapBot won't do anyone any good if it's still sat on blocks.  You need wheels - four of them, to be precise.`n");
	}
	if ($playerscrap['data']['rareitems'][5] < 1){
		output("You'll need something to tie all of these components together, so a wiring harness would be very handy.`n");
	}
	if ($playerscrap['data']['veryrareitems'][3] < 1){
		output("A ScrapBot that cannot see where it is going is a poor creation indeed.  You'll need some sort of a camera - you can make one out of a CMOS Sensor and some sort of lens.`n");
	}
	if ($playerscrap['data']['rareitems'][12] < 1){
		output("All the wheels in the world don't mean a thing unless there's something turning them.  You'll need a Drive Motor if you want your ScrapBot to go anywhere.");
	}
	//Building a whole Robot!
	if ($playerscrap['data']['veryrareitems'][0] >= 1 && $playerscrap['data']['rareitems'][0] >= 2 && $playerscrap['data']['veryrareitems'][1] >= 1 && $playerscrap['data']['rareitems'][3] >= 1 && $playerscrap['data']['rareitems'][4] >= 1 && $playerscrap['data']['rareitems'][6] >= 1 && $playerscrap['data']['veryrareitems'][2] >= 1 && $playerscrap['data']['rareitems'][7] >= 4 && $playerscrap['data']['rareitems'][5] >= 1 && $playerscrap['data']['veryrareitems'][3] >= 1 && $playerscrap['data']['rareitems'][12] >= 1){
		addnav("Building a ScrapBot");
		output("`n`bYou have enough components ready to build a whole ScrapBot using your Metalworking, Soldering and Programming skills!`b  This will take:`nOne Chassis`nTwo Axles`nOne set of Armour`nOne Motherboard`nOne ROM Chip`nOne Battery`nOne Servo Limb`nFour Wheels`nOne Wiring Harness`nOne Camera Eye`nOne Drive Motor`n");
		addnav("Build a ScrapBot!","runmodule.php?module=scrapbots&op=buildrobot");
	}
	output("`n`0");
	return;
}

/*
=======================================================
LIST SCRAPBOTS
Outputs a list of ScrapBots to be managed, including navs.
=======================================================
*/

function scrapbots_list_scrapbots(){
	global $session;
	if ($session['user']['alive']==0){
		return;
	}

	$sql = "SELECT id,owner,name,activated,hitpoints,brains,brawn,briskness,junglefighter,retreathp FROM ".db_prefix("scrapbots")." WHERE owner = ".$session['user']['acctid'];
	$result = db_query($sql);

	if (db_num_rows($result) > 0){
		output("`1`bYour ScrapBots`b`n");
		rawoutput("<table width=100%><tr class='trhead'><td>Name</td><td>HP</td><td>Brains</td><td>Brawn</td><td>Briskness</td><td>Retreat %</td></tr>");
		$scrap = scrapbots_get_player_scrap();
		for ($i=0;$i<db_num_rows($result);$i++){
			
			$row=db_fetch_assoc($result);
			
			$repairlink = "";
			if ($row['hitpoints'] < ($row['brawn']*10)){
				$reps = 1;
				for ($currenthitpoints = $row['hitpoints']; $currenthitpoints < ($row['brawn']*10); $currenthitpoints += 10){
					$repairlink .= "<br /><a href=\"runmodule.php?module=scrapbots&op=managescrapbot&bot=".$row['id']."&sub=repair&reps=".$reps."\">Repair x ".$reps."</a>";
					addnav("","runmodule.php?module=scrapbots&op=managescrapbot&bot=".$row['id']."&sub=repair&reps=$reps");
					$reps++;
				}
			}
			
			$hpmissing = ($row['brawn']*10) - $row['hitpoints'];
			$hplength = $row['brawn']*10;
			$hpbar = "<br /><table height=\"8\" cellpadding=\"0\" cellspacing=\"0\" width=\"".$hplength."\" style=\"border: 1px solid rgb(0, 0, 0);\"><tr><td width=\"".$row['hitpoints']."\" bgcolor=\"#009900\"></td><td width='".$hpmissing."' bgcolor=\"#333333\"</td></tr></table>";
			
			rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td><b>".$row['name']."</b> (CPUID ".$row['id'].")<br />".($row['activated']==1?"Activated":"<a href=\"runmodule.php?module=scrapbots&op=managescrapbot&bot=".$row['id']."&sub=activate\"Activate</a>")."<br /><a href=\"runmodule.php?module=scrapbots&op=managescrapbot&bot=".$row['id']."\">Manage this ScrapBot</a>".$hpbar.$repairlink."</td><td>".$row['hitpoints']."/".$hplength."</td><td>".$row['brains']."</td><td>".$row['brawn']."</td><td>".$row['briskness']."</td><td>".$row['retreathp']."</td></tr>");
			addnav("","runmodule.php?module=scrapbots&op=managescrapbot&bot=".$row['id']);
		}
		rawoutput("</table>");
		output("`n`n`0");
	}
	return;
}

/*
=======================================================
LIST THINGS THAT CAN BE PROCESSED
Outputs a list things that can be processed or combined into other things, and adds navs to do so.
=======================================================
*/

function scrapbots_list_combinations() {
	global $session;

	if ($session['user']['alive']==0){
		return;
	}	
	
	$playerscrap = scrapbots_get_player_scrap();
	$scrapnames = scrapbots_scrapnames();
	$bot = httpget('bot');
	
	//Basic Processing
	output("`2`bThings you can do with your bits`b`n");
	$canbuild = 0;
	if ($playerscrap['data']['normalitems'][0] >= 1){
		output("You can turn a Rusted Shaft into an Axle with a judicious application of your Metalworking skill.`n");
		addnav("Building Spares");
		addnav("Turn Rusted Shaft into Axle","runmodule.php?module=scrapbots&op=combinescrap&bot=".$bot."&&make=axle");
		$canbuild = 1;
	}
	if ($playerscrap['data']['normalitems'][1] >= 1){
		output("You can get the rust off a Steel Plate using your Metalworking skill.`n");
		addnav("Building Spares");
		addnav("Clean up a Rusted Steel Plate","runmodule.php?module=scrapbots&op=combinescrap&bot=".$bot."&&make=steelplate");
		$canbuild = 1;
	}
	if ($playerscrap['data']['normalitems'][2] >= 1){
		output("You can get the rust off a Large Girder using your Metalworking skill.`n");
		addnav("Building Spares");
		addnav("Clean up a Rusted Large Girder","runmodule.php?module=scrapbots&op=combinescrap&bot=".$bot."&&make=largegirderfromrusted");
		$canbuild = 1;
	}
	if ($playerscrap['data']['normalitems'][3] >= 1){
		output("You can repair a Broken Motherboard using your Soldering skill.`n");
		addnav("Building Spares");
		addnav("Repair a Broken Motherboard","runmodule.php?module=scrapbots&op=combinescrap&bot=".$bot."&&make=motherboard");
		$canbuild = 1;
	}
	if ($playerscrap['data']['normalitems'][4] >= 1){
		output("You can reflash a Corrupted ROM Chip ready for rewriting using your Programming skill.`n");
		addnav("Building Spares");
		addnav("Reflash a Corrupted ROM Chip","runmodule.php?module=scrapbots&op=combinescrap&bot=".$bot."&&make=romchip");
		$canbuild = 1;
	}
	if ($playerscrap['data']['normalitems'][5] >= 1){
		output("You can restore a Rusted Servo to working order using your Metalworking skill.`n");
		addnav("Building Spares");
		addnav("Clean up a Rusted Servo","runmodule.php?module=scrapbots&op=combinescrap&bot=".$bot."&&make=servofromrusted");
		$canbuild = 1;
	}
	if ($playerscrap['data']['normalitems'][6] >= 1){
		output("You can quickly turn a Bundle of Wires into a decent Wiring Harness using your Soldering skill.`n");
		addnav("Building Spares");
		addnav("Make a Wiring Harness","runmodule.php?module=scrapbots&op=combinescrap&bot=".$bot."&&make=wiringharness");
		$canbuild = 1;
	}
	if ($playerscrap['data']['normalitems'][7] >= 1 && $session['user']['gold'] >= 40){
		output("You can soon recharge that Dead Battery at the Charging Station, for a fee of 40 Requisition.`n");
		addnav("Building Spares");
		addnav("Recharge a Dead Battery","runmodule.php?module=scrapbots&op=combinescrap&bot=".$bot."&&make=battery");
		$canbuild = 1;
	}
	if ($playerscrap['data']['normalitems'][8] >= 1 && $session['user']['gold'] >= 40){
		output("You can have a Punctured Wheel repaired by professional Robots for 40 Requisition.`n");
		addnav("Building Spares");
		addnav("Have a Punctured Wheel repaired","runmodule.php?module=scrapbots&op=combinescrap&bot=".$bot."&&make=wheelfrompunctured");
		$canbuild = 1;
	}
	if ($playerscrap['data']['normalitems'][9] >= 1){
		output("You can soon polish up a Rusted Wheel using your Metalworking skill.`n");
		addnav("Building Spares");
		addnav("Clean up a Rusted Wheel","runmodule.php?module=scrapbots&op=combinescrap&bot=".$bot."&&make=wheelfromrusted");
		$canbuild = 1;
	}
	if ($playerscrap['data']['normalitems'][10] >= 1){
		output("You can clean the rust from a Rusted Small Girder using your Metalworking skill.`n");
		addnav("Building Spares");
		addnav("Clean up a Rusted Small Girder","runmodule.php?module=scrapbots&op=combinescrap&bot=".$bot."&&make=smallgirderfromrusted");
		$canbuild = 1;
	}
	if ($playerscrap['data']['normalitems'][11] >= 1){
		output("You can soon repair a broken CMOS sensor using your Soldering skill.`n");
		addnav("Building Spares");
		addnav("Repair a Broken CMOS Sensor","runmodule.php?module=scrapbots&op=combinescrap&bot=".$bot."&&make=cmos");
		$canbuild = 1;
	}
	if ($playerscrap['data']['normalitems'][12] >= 1){
		output("You can turn the bottom of a Glass Milk Bottle into a servicable Lens using your Metalworking skill.`n");
		addnav("Building Spares");
		addnav("Turn a Glass Milk Bottle into a Lens","runmodule.php?module=scrapbots&op=combinescrap&bot=".$bot."&&make=lens");
		$canbuild = 1;
	}
	if ($playerscrap['data']['normalitems'][13] >= 1){
		output("You can repair a Broken Servo using your Soldering skill.`n");
		addnav("Building Spares");
		addnav("Repair a Broken Servo","runmodule.php?module=scrapbots&op=combinescrap&bot=".$bot."&&make=servofrombroken");
		$canbuild = 1;
	}
	if ($playerscrap['data']['normalitems'][14] >= 1){
		output("You can repair a Broken Drive Motor using your Soldering skill.`n");
		addnav("Building Spares");
		addnav("Repair a broken Drive Motor","runmodule.php?module=scrapbots&op=combinescrap&bot=".$bot."&&make=drivefrombroken");
		$canbuild = 1;
	}
	if ($playerscrap['data']['normalitems'][15] >= 1){
		output("You can clean up a Rusted Drive Motor using your Metalworking skill.`n");
		addnav("Building Spares");
		addnav("Clean up Rusted Drive Motor","runmodule.php?module=scrapbots&op=combinescrap&bot=".$bot."&&make=drivefromrusted");
		$canbuild = 1;
	}
	if ($canbuild == 0){
		output("You don't have any Scrap that you can upgrade into a spare part.`n");
	}
	
	//Splitting and Combining
	output("`n");
	$canbuild = 0;
	if ($playerscrap['data']['rareitems'][2] >= 1){
		output("You can turn a Large Girder into two Small Girders using your Metalworking skill.`n");
		addnav("Cutting and Welding");
		addnav("Saw a Large Girder in half","runmodule.php?module=scrapbots&op=combinescrap&bot=".$bot."&&make=twosmallgirders");
		$canbuild = 1;
	}
	if ($playerscrap['data']['rareitems'][8] >= 2){
		addnav("Cutting and Welding");
		output("You can make a Large Girder from two Small Girders using your Metalworking skill.`n");
		addnav("Weld two Small Girders together","runmodule.php?module=scrapbots&op=combinescrap&bot=".$bot."&&make=largegirderfromtwosmall");
		$canbuild = 1;
	}
	if ($playerscrap['data']['rareitems'][8] >= 3){
		addnav("Cutting and Welding");
		output("You can make five Steel Plates from three Small Girders using your Metalworking skill.`n");
		addnav("Make five Steel Plates from three Small Girders","runmodule.php?module=scrapbots&op=combinescrap&bot=".$bot."&&make=platesfromgirders");
		$canbuild = 1;
	}
	if ($playerscrap['data']['rareitems'][1] >= 2){
		output("You can make some pretty good Armour out of two Steel Plates using your Metalworking skill.`n");
		addnav("Building Assemblies");
		addnav("Make Armour from two Steel Plates","runmodule.php?module=scrapbots&op=combinescrap&bot=".$bot."&&make=armour");
		$canbuild = 1;
	}
	if ($playerscrap['data']['rareitems'][8] >= 2 && $playerscrap['data']['rareitems'][2] >= 1){
		addnav("Building Assemblies");
		output("You can build a Chassis from two Small Girders and one Large Girder using your Metalworking skill.`n");
		addnav("Build a Chassis","runmodule.php?module=scrapbots&op=combinescrap&bot=".$bot."&&make=chassis");
		$canbuild = 1;
	}
	if ($playerscrap['data']['rareitems'][1] >= 1 && $playerscrap['data']['rareitems'][8] >= 1 && $playerscrap['data']['rareitems'][11] >= 1){
		addnav("Building Assemblies");
		output("You can build a Servo Limb out of a Servo, a Small Girder and a Steel Plate using your Metalworking and Soldering skills.`n");
		addnav("Build a Servo Limb","runmodule.php?module=scrapbots&op=combinescrap&bot=".$bot."&&make=servolimb");
		$canbuild = 1;
	}
	if ($playerscrap['data']['rareitems'][9] >= 1 && $playerscrap['data']['rareitems'][10] >= 1){
		addnav("Building Assemblies");
		output("You can build a Camera Eye from a Lens and a CMOS Sensor using your Metalworking and Soldering skills.`n");
		addnav("Build a Camera Eye","runmodule.php?module=scrapbots&op=combinescrap&bot=".$bot."&&make=cameraeye");
		$canbuild = 1;
	}
	if ($canbuild == 0){
		output("You don't have any spare parts that you can assemble.`n");
	}
	
	output("`n`0");
	return;
}
?>