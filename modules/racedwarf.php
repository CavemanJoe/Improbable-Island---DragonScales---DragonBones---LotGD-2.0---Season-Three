<?php

function racedwarf_getmoduleinfo(){
	$info = array(
		"name"=>"Race - Dwarf",
		"version"=>"2008-11-08",
		"author"=>"Cousjava, based on the Humans race by Eric Stevens",//Dwarf race by Eric Stevens and Mutant Race by Dan Hall were also used in the construction of this race.
		"category"=>"Races",
		"download"=>"",
		"settings"=>array(
			"Dwarven Race Settings,title",
			"villagename"=>"Name for the dwarven village|Thavatrid",
		),
	);
	return $info;
}

function racedwarf_install(){
	module_addhook("chooserace");
	module_addhook("setrace");
	module_addhook("stamina-newday");
	// module_addhook("alternativeresurrect");
	module_addhook("villagetext");
	module_addhook("validlocation");
	module_addhook("validforestloc");
	module_addhook("moderate");
	module_addhook("changesetting");
	//module_addhook("stablelocs");
	//module_addhook("stabletext");
	module_addhook("racenames");
	module_addhook("battle-victory");
	module_addhook("fightnav-specialties");
	module_addhook("apply-specialties");
	return true;
}

function racedwarf_uninstall(){
	global $session;
	$vname = get_module_setting("villagename", "racedwarf");;
	$gname = get_module_setting("villagename");
	$sql = "UPDATE " . db_prefix("accounts") . " SET location='$vname' WHERE location = '$gname'";
	db_query($sql);
	if ($session['user']['location'] == $gname)
		$session['user']['location'] = $vname;
	// Force anyone who was a Dwarf to rechoose race
	$sql = "UPDATE  " . db_prefix("accounts") . " SET race='" . RACE_UNKNOWN . "' WHERE race='Dwarf'";
	db_query($sql);
	if ($session['user']['race'] == 'Dwarf')
		$session['user']['race'] = RACE_UNKNOWN;
	return true;
}

function racedwarf_dohook($hookname,$args){
	global $session,$resline;
	$city = get_module_setting("villagename");
	$race = "Dwarf";
	switch($hookname){
	case "racenames":
		$args[$race] = $race;
		break;
	case "changesetting":
		// Ignore anything other than villagename setting changes
		if ($args['setting'] == "villagename" && $args['module']=="racedwarf") {
			if ($session['user']['location'] == $args['old'])
				$session['user']['location'] = $args['new'];
			$sql = "UPDATE " . db_prefix("accounts") .
				" SET location='" . addslashes($args['new']) .
				"' WHERE location='" . addslashes($args['old']) . "'";
			db_query($sql);
				$sql = "UPDATE " . db_prefix("module_userprefs") .
					" SET value='" . addslashes($args['new']) .
					"' WHERE modulename='cities' AND setting='homecity'" .
					"AND value='" . addslashes($args['old']) . "'";
				db_query($sql);
			
		}
		break;
	case "chooserace":
		if ($session['user']['dragonkills'] < 5)
			break;
		output("`0You raise your axe to the gatekeepers face.  <a href='newday.php?setrace=$race$resline'>\"Don't \"</a>`n`n", true);
		addnav("`&Dwarf`0","newday.php?setrace=$race$resline");
		addnav("","newday.php?setrace=$race$resline");
		break;
	case "setrace":
		if ($session['user']['race']==$race){
			output("`0The gatekeeper puts on his glasses.  \"`6Oh,`0\" he says, quietly.`n`n\"`#Yes,`0\" you reply.  \"`#Oh.`0\"`n`nYou stare at each other for a moment.  The colour slowly drains from the gatekeeper's face.`n`n\"`6Well,`0\" says the gatekeeper, to break the silence.  \"`6Let's get you signed in, shall we?`0\"  He swallows uncomfortably, and takes out his ledger.  \"`6Em, you, tee, you, enn, tee.  Mu-hluck!`0\"  He swallows again, wiping his mouth on the back of his sleeve.  \"`6Mutant.  Sorry.  Have you been like this all your li... blugh.`0\"  A little blob of vomit plops onto his ledger.  He takes a deep breath.  \"`6Have you been like this all your life?`0\"`n`n\"`#Yes,`0\" you reply.  \"`#Since my dear mother first excreted me from her poor, wretched womb, looked down, screamed \"Dear God what IS that `iTHING`i\" and died immediately from a combination of horror, shame and embarrassment, I have been the hideous abomination that you see before you now.  And you needn't apologise, I'm used to it.`0\"  You shake your head sadly.  \"`#So, so very used to it.`0\"`n`n\"`6Well, that's... hruuuu`iuuuuuuuuu`iuuuurgh!  Oh, sweet Jesus Mary and Joseph!`0\"`n`n\"`#That's okay, let it all out.  Isn't it strange how there's always carrots in there?`0\" you ask.  \"`#You don't have to look up if you don't want to.  I know that the sight of your own expulsions are preferable to my horrendous visage.`0\"`n`n\"`6Blarghle,`0\" says the gatekeeper.`n`n\"`#Like I say, I'm used to it.`0\"  You turn and head toward the gate, leaving a trail of slime behind you.");
			
				set_module_pref("homecity",$city,"cities");
				if ($session['user']['age'] == 0)
					$session['user']['location']=$city;
			
		}
		break;
	case "stamina-newday":
	case "alternativeresurrect":
		if ($session['user']['race']==$race){
			racemutant_checkcity();
			
			
			//Stamina Buffs
			require_once("modules/staminasystem/lib/lib.php");
			apply_stamina_buff('Dwarf2', array(
				"name"=>"Dwarf Bonus: Combat Skills",
				"class"=>"Combat",
				"costmod"=>0.8,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('Dwarf4', array(
				"name"=>"Dwarf Bonus: Mining Proficiency",
				"action"=>"Scavenging for Scrap",
				"costmod"=>0.8,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('Dwarf5', array(
				"name"=>"Dwarf Bonus: Blacksmithing Skills",
				"action"=>"Metalworking",
				"costmod"=>0.8,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('Dwarf6', array(
				"name"=>"Dwarf Bonus: Soldering Skills",
				"action"=>"Soldering",
				"costmod"=>0.8,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));

			apply_stamina_buff('Dwarf8', array(
				"name"=>"Dwarf Bonus: Carcass Cleaning Skills",
				"action"=>"Cleaning the Carcass",
				"costmod"=>0.8,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('Dwarf0', array(
				"name"=>"Dwarf Penalty: Learning Difficulties",
				"class"=>"Global",
				"costmod"=>1,
				"expmod"=>0.5,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('Dwarf1', array(
				"name"=>"Dwarf Penalty: Hunting Inexperience",
				"class"=>"Hunting",
				"costmod"=>1.1,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('raceclassy', array(
				"name"=>"Dwarf Bonus: Insults Proficiency",
				"class"=>"Insults",
				"costmod"=>0.5,
				"expmod"=>1.1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			
			//Combat Buffs
			apply_buff("racialbenefit1",array(
				"name"=>"`7Dwarf Bonus: Leathery Hide`0",
				"defmod"=>"1.1",
				"allowinpvp"=>1,
				"allowintrain"=>1,
				"rounds"=>-1,
				"schema"=>"module-racedwarf",
				)
			);
			apply_buff("racialbenefit2",array(
				"name"=>"`7Dwarf Bonus: Combat Appendages`0",
				"atkmod"=>"1.1",
				"allowinpvp"=>1,
				"allowintrain"=>1,
				"rounds"=>-1,
				"schema"=>"module-racedwarf",
				)
			);
		}
		break;
	case "creatureencounter":
		if ($session['user']['race']==$race){
			//get those folks who haven't manually chosen a race
			racedwarf_checkcity();
			$args['creaturegold']=round($args['creaturegold']*0.8,0);
			$args['creatureexp']=round($args['creatureexp']*0.8,0);
		}
		break;
	case "battle-victory":
		if ($session['user']['race']==$race && $session['user']['alive']==false){
			$args['creatureexp']=round($args['creatureexp']*0.8);
		}
		break;

	case "validforestloc":
	case "validlocation":
			$args[$city]="village-$race";
		break;
	case "moderate":		
			tlschema("commentary");
			$args["village-$race"]=sprintf_translate("City of %s", $city);
			tlschema();
		break;
	case "villagetext":
		racedwarf_checkcity();
		if ($session['user']['location'] == $city){
			$args['text']=array("`0You are standing in the heart of %s.  It's a massive cave, deep under the mountains. Massive pillars support the  great roof, which is so far overhead you can barely see it. The pillars are made of crystal. On them is a picture a dwarf. The dwarf is surronded by dwarves. The artwork relates to the founding of %s in the year 965.`n`n",$city, $city);
			$args['schemas']['text'] = "module-racedwarf";
			$args['clock']="`0A large clock powered by water pumps in the village square reads `&%s`0.`n`n";
			$args['schemas']['clock'] = "module-racedwarf";
			if (is_module_active("calendar")) {
				$args['calendar'] = "`0A smaller dial beneath reads `&%s`0, `&%s %s %s`0.`n`n";
				$args['schemas']['calendar'] = "module-racedwarf";
			}
			$args['title']=array("%s, Home of the Dwarves", $city);
			$args['schemas']['title'] = "module-racedwarf";
			$args['sayline']="says";
			$args['schemas']['sayline'] = "module-racedwarf";
			$args['talk']="`&Nearby some dwarves talk about mining in gruff voices:`n";
			$args['schemas']['talk'] = "module-racemutant";
			$new = get_module_setting("newest-$city", "cities");
			if ($new != 0) {
				$sql =  "SELECT name FROM " . db_prefix("accounts") .
					" WHERE acctid='$new'";
				$result = db_query_cached($sql, "newest-$city");
				$row = db_fetch_assoc($result);
				$args['newestplayer'] = $row['name'];
				$args['newestid']=$new;
			} else {
				$args['newestplayer'] = $new;
				$args['newestid']="";
			}
			if ($new == $session['user']['acctid']) {
				$args['newest']="`n`0You wander around the cave, caressing the stone surfaces, examining it all in great detail.";
			} else {
				$args['newest']="`n`0Wandering the village, caressing the stone surfaces, is `&%s`0.";
			}
			$args['schemas']['newest'] = "module-racedwarf";
			$args['section']="village-$race";
			$args['stablename']="Mike's Chop Shop";
			$args['schemas']['stablename'] = "module-racedwarf";
			$args['gatenav']="Gates to Outside";
			$args['fightnav']="Miner's Street";
			$args['marketnav']="The Forge";
			$args['tavernnav']="Blacksmith's Lane";
			$args['schemas']['gatenav'] = "module-racedwarf";
			unblocknav("stables.php");
		}
		break;
	case "stabletext":
		if ($session['user']['location'] != $city) break;
		$args['title'] = "Mike's Chop Shop";
		$args['schemas']['title'] = "module-racedwarf";
		$args['desc'] = array(
			"`6Just next door to the Clan Halls, an organic-looking cave leads off.  You head inside.`n`n",
			array("As you venture inside the small cave, you notice a young dwarf tending to a nearby... something.  Something bloody indescribable.  \"`#Hello?`6\" you call.  The young man turns around.  \"`#Agh,`6\" you say, quietly.  Then, you smile sheepishly.  \"`#I mean... hi!`6\"`n`n\"`^Don't worry, I'm used to it.  The name's Mike, and what can I do for you today, my good %s?`6\" You try your hardest not to stare, because you know that if you stare, you'll end up counting under your breath to see how many eyes he has, and that would be rude.", translate_inline($session['user']['sex']?'madam':'sir', 'stables'))
		);
		$args['schemas']['desc'] = "module-racedwarf";
		$args['lad']="friend";
		$args['schemas']['lad'] = "module-racedwarf";
		$args['lass']="friend";
		$args['schemas']['lass'] = "module-racedwarf";
		$args['nosuchbeast']="`6\"`^They're onlt legends, you idiot!`6\" Mike laughs at you.";
		$args['schemas']['nosuchbeast'] = "module-racedwarf";
		$args['toolittle']="`6Mike looks over the handful of currency you offered.  \"`^Hmm.  Well, you see, the price for this %s was actually `&%s `^Requisition tokens and `%%s`^ cigarettes.  My apologies for any misunderstandings.`6\"";
		$args['schemas']['toolittle'] = "module-racedwarf";
		$args['replacemount']="`6You sadly watch Mike lead your %s`6 away, along with your cigarettes.  However, when he returns, he brings with him a nice new `&%s`6 which makes you feel a little better.";
		$args['schemas']['replacemount'] = "module-racedwarf";
		$args['newmount']="`6You hand over your currency.  Within moments, you become the proud recipient of a lovely new `&%s`6!";
		$args['schemas']['newmount'] = "module-racedwarf";
		$args['confirmsale']="`n`n`6Mike eyes your mount up and down, checking it over carefully.  Probably seeing it in ways that you can only imagine.  \"`^My, this is indeed a fine specimen.  Are you quite sure you wish to part with it?`6\"";
		$args['schemas']['confirmsale'] = "module-racedwarf";
		$args['mountsold']="`6With but a single tear, you hand your %s`6 over to Mike.  The tear dries quickly, and the %s in hand helps you quickly overcome your sorrow.";
		$args['schemas']['mountsold'] = "module-racedwarf";
		$args['offer']="`n`n`6Mike offers you `&%s`6 Requisition and `%%s`6 Cigarettes for %s`6.";
		$args['schemas']['offer'] = "module-racemutant";
		break;

	case "stablelocs":
		tlschema("mounts");
		$args[$city]=sprintf_translate("The City of %s", $city);
		tlschema();
		break;
	}
	return $args;
}

function racedwarf_checkcity(){
	global $session;
	$race="Dwarf";
	$city= get_module_setting("villagename");

	if ($session['user']['race']==$race){
		//if they're this race and their home city isn't right, set it up.
		if (get_module_pref("homecity","cities")!=$city){ //home city is wrong
			set_module_pref("homecity",$city,"cities");
		}
	}
	return true;
}

function racedwarf_run(){

}
?>
