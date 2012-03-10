<?php

function racemutant_getmoduleinfo(){
	$info = array(
		"name"=>"Race - Mutant",
		"version"=>"2008-11-08",
		"author"=>"Dan Hall, based on the Humans race by Eric Stevens",
		"category"=>"Races",
		"download"=>"fix this",
		"prefs" => array(
			"Specialty - Noodly Appendage user prefs,title",
			"skill"=>"Points in Noodly Appendage,int|0",
			"uses"=>"How many uses of Noodly Appendage,int|0",
		),
	);
	return $info;
}

function racemutant_install(){
	module_addhook("chooserace");
	module_addhook("setrace");
	module_addhook("stamina-newday");
	// module_addhook("alternativeresurrect");
	module_addhook("villagetext");
	module_addhook("validlocation");
	module_addhook("validforestloc");
	module_addhook("moderate");
	module_addhook("changesetting");
	module_addhook("stablelocs");
	module_addhook("stabletext");
	module_addhook("racenames");
	module_addhook("battle-victory");
	module_addhook("fightnav-specialties");
	module_addhook("apply-specialties");
	return true;
}

function racemutant_uninstall(){
	global $session;
	$vname = getsetting("villagename", LOCATION_FIELDS);
	$gname = "Pleasantville";
	$sql = "UPDATE " . db_prefix("accounts") . " SET location='$vname' WHERE location = '$gname'";
	db_query($sql);
	if ($session['user']['location'] == $gname)
		$session['user']['location'] = $vname;
	// Force anyone who was a Mutant to rechoose race
	$sql = "UPDATE  " . db_prefix("accounts") . " SET race='" . RACE_UNKNOWN . "' WHERE race='Mutant'";
	db_query($sql);
	if ($session['user']['race'] == 'Mutant')
		$session['user']['race'] = RACE_UNKNOWN;
	return true;
}

function racemutant_dohook($hookname,$args){
	global $session,$resline;
	$city = "Pleasantville";
	$race = "Mutant";
	switch($hookname){
	case "racenames":
		$args[$race] = $race;
		break;
	case "changesetting":
		// Ignore anything other than villagename setting changes
		if ($args['setting'] == "villagename" && $args['module']=="racemutant") {
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
		output("`0You show the gatekeeper a sad smile.  <a href='newday.php?setrace=$race$resline'>\"That's a very insensitive question.\"</a>`n`n", true);
		addnav("`&Mutant`0","newday.php?setrace=$race$resline");
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
			apply_stamina_buff('mutant2', array(
				"name"=>"Mutant Bonus: Combat Skills",
				"class"=>"Combat",
				"costmod"=>0.8,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('mutant4', array(
				"name"=>"Mutant Bonus: Scavenging Proficiency",
				"action"=>"Scavenging for Scrap",
				"costmod"=>0.8,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('mutant5', array(
				"name"=>"Mutant Bonus: Metalwork Skills",
				"action"=>"Metalworking",
				"costmod"=>0.8,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('mutant6', array(
				"name"=>"Mutant Bonus: Soldering Skills",
				"action"=>"Soldering",
				"costmod"=>0.8,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));

			apply_stamina_buff('mutant8', array(
				"name"=>"Mutant Bonus: Carcass Cleaning Skills",
				"action"=>"Cleaning the Carcass",
				"costmod"=>0.8,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('mutant0', array(
				"name"=>"Mutant Penalty: Learning Difficulties",
				"class"=>"Global",
				"costmod"=>1,
				"expmod"=>0.5,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('mutant1', array(
				"name"=>"Mutant Penalty: Hunting Inexperience",
				"class"=>"Hunting",
				"costmod"=>1.1,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('raceclassy', array(
				"name"=>"Mutant Bonus: Insults Proficiency",
				"class"=>"Insults",
				"costmod"=>0.5,
				"expmod"=>1.1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			
			//Combat Buffs
			apply_buff("racialbenefit1",array(
				"name"=>"`7Mutant Bonus: Leathery Hide`0",
				"defmod"=>"1.1",
				"allowinpvp"=>1,
				"allowintrain"=>1,
				"rounds"=>-1,
				"schema"=>"module-racemutant",
				)
			);
			apply_buff("racialbenefit2",array(
				"name"=>"`7Mutant Bonus: Combat Appendages`0",
				"atkmod"=>"1.1",
				"allowinpvp"=>1,
				"allowintrain"=>1,
				"rounds"=>-1,
				"schema"=>"module-racemutant",
				)
			);
		}
		break;
	case "creatureencounter":
		if ($session['user']['race']==$race){
			//get those folks who haven't manually chosen a race
			racemutant_checkcity();
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
		racemutant_checkcity();
		if ($session['user']['location'] == $city){
			$args['text']=array("`0You are standing in the heart of Pleasantville.  It's all very clean, but there are no reflective surfaces - every window has either been boarded up or slavered with a thick coating of slime to prevent reflections.`n`nLooking around at the state of some of your fellow contestants, you think you can see why.`n`n");
			$args['schemas']['text'] = "module-racemutant";
			$args['clock']="`0A clock in the village square reads `&%s`0.`n`n";
			$args['schemas']['clock'] = "module-racemutant";
			if (is_module_active("calendar")) {
				$args['calendar'] = "`0A smaller dial beneath reads `&%s`0, `&%s %s %s`0.`n`n";
				$args['schemas']['calendar'] = "module-racemutant";
			}
			$args['title']=array("%s, Home of the Mutants", $city);
			$args['schemas']['title'] = "module-racemutant";
			$args['sayline']="says";
			$args['schemas']['sayline'] = "module-racemutant";
			$args['talk']="`&Nearby some mutants drone endlessly about their problems:`n";
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
				$args['newest']="`n`0You wander around the village, sliding your noodly appendages over every surface, examining it all in great detail.";
			} else {
				$args['newest']="`n`0Wandering the village, touching everything with hideously freaky noodles, is `&%s`0.";
			}
			$args['schemas']['newest'] = "module-racemutant";
			$args['section']="village-$race";
			$args['stablename']="Mike's Chop Shop";
			$args['schemas']['stablename'] = "module-racemutant";
			$args['gatenav']="Outpost Gates";
			$args['fightnav']="Oppression Street";
			$args['marketnav']="Capitalism Avenue";
			$args['tavernnav']="Despair Lane";
			$args['schemas']['gatenav'] = "module-racemutant";
			unblocknav("stables.php");
		}
		break;
	case "stabletext":
		if ($session['user']['location'] != $city) break;
		$args['title'] = "Mike's Chop Shop";
		$args['schemas']['title'] = "module-racemutant";
		$args['desc'] = array(
			"`6Just next door to the Clan Halls, a scruffy-looking warehouse has been erected.  You head inside.`n`n",
			array("As you venture inside the building, you notice a young man tending to a nearby... something.  Something bloody indescribable.  \"`#Hello?`6\" you call.  The young man turns around.  \"`#Agh,`6\" you say, quietly.  Then, you smile sheepishly.  \"`#I mean... hi!`6\"`n`n\"`^Don't worry, I'm used to it.  The name's Mike, and what can I do for you today, my good %s?`6\" You try your hardest not to stare, because you know that if you stare, you'll end up counting under your breath to see how many eyes he has, and that would be rude.", translate_inline($session['user']['sex']?'madam':'sir', 'stables'))
		);
		$args['schemas']['desc'] = "module-racemutant";
		$args['lad']="friend";
		$args['schemas']['lad'] = "module-racemutant";
		$args['lass']="friend";
		$args['schemas']['lass'] = "module-racemutant";
		$args['nosuchbeast']="`6\"`^I'm sorry, I've never heard of such a thing,`6\" Mike says apologetically.";
		$args['schemas']['nosuchbeast'] = "module-racemutant";
		$args['toolittle']="`6Mike looks over the handful of currency you offered.  \"`^Hmm.  Well, you see, the price for this %s was actually `&%s `^Requisition tokens and `%%s`^ cigarettes.  My apologies for any misunderstandings.`6\"";
		$args['schemas']['toolittle'] = "module-racemutant";
		$args['replacemount']="`6You sadly watch Mike lead your %s`6 away, along with your cigarettes.  However, when he returns, he brings with him a nice new `&%s`6 which makes you feel a little better.";
		$args['schemas']['replacemount'] = "module-racemutant";
		$args['newmount']="`6You hand over your currency.  Within moments, you become the proud recipient of a lovely new `&%s`6!";
		$args['schemas']['newmount'] = "module-racemutant";
		$args['confirmsale']="`n`n`6Mike eyes your mount up and down, checking it over carefully.  Probably seeing it in ways that you can only imagine.  \"`^My, this is indeed a fine specimen.  Are you quite sure you wish to part with it?`6\"";
		$args['schemas']['confirmsale'] = "module-racemutant";
		$args['mountsold']="`6With but a single tear, you hand your %s`6 over to Mike.  The tear dries quickly, and the %s in hand helps you quickly overcome your sorrow.";
		$args['schemas']['mountsold'] = "module-racemutant";
		$args['offer']="`n`n`6Mike offers you `&%s`6 Requisition and `%%s`6 Cigarettes for %s`6.";
		$args['schemas']['offer'] = "module-racemutant";
		break;

	case "stablelocs":
		tlschema("mounts");
		$args[$city]=sprintf_translate("The Village of %s", $city);
		tlschema();
		break;
	}
	return $args;
}

function racemutant_checkcity(){
	global $session;
	$race="Mutant";
	$city="Pleasantville";

	if ($session['user']['race']==$race){
		//if they're this race and their home city isn't right, set it up.
		if (get_module_pref("homecity","cities")!=$city){ //home city is wrong
			set_module_pref("homecity",$city,"cities");
		}
	}
	return true;
}

function racemutant_run(){

}
?>
