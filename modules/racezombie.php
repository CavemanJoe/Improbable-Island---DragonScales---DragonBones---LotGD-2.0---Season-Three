<?php

function racezombie_getmoduleinfo(){
	$info = array(
		"name"=>"Race - Zombie",
		"version"=>"2008-11-08",
		"author"=>"Dan Hall, based on the Humans race by Eric Stevens",
		"category"=>"Races",
		"download"=>"fix this",
	);
	return $info;
}

function racezombie_install(){
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
	return true;
}

function racezombie_uninstall(){
	global $session;
	$vname = getsetting("villagename", LOCATION_FIELDS);
	$gname = "New Pittsburgh";
	$sql = "UPDATE " . db_prefix("accounts") . " SET location='$vname' WHERE location = '$gname'";
	db_query($sql);
	if ($session['user']['location'] == $gname)
		$session['user']['location'] = $vname;
	// Force anyone who was a Zombie to rechoose race
	$sql = "UPDATE  " . db_prefix("accounts") . " SET race='" . RACE_UNKNOWN . "' WHERE race='Zombie'";
	db_query($sql);
	if ($session['user']['race'] == 'Zombie')
		$session['user']['race'] = RACE_UNKNOWN;
	return true;
}

function racezombie_dohook($hookname,$args){
	global $session,$resline;
	$city = "New Pittsburgh";
	$race = "Zombie";
	switch($hookname){
	case "racenames":
		$args[$race] = $race;
		break;
	case "changesetting":
		// Ignore anything other than villagename setting changes
		if ($args['setting'] == "villagename" && $args['module']=="racezombie") {
			if ($session['user']['location'] == $args['old'])
				$session['user']['location'] = $args['new'];
			$sql = "UPDATE " . db_prefix("accounts") .
				" SET location='" . addslashes($args['new']) .
				"' WHERE location='" . addslashes($args['old']) . "'";
			db_query($sql);
			if (is_module_active("cities")) {
				$sql = "UPDATE " . db_prefix("module_userprefs") .
					" SET value='" . addslashes($args['new']) .
					"' WHERE modulename='cities' AND setting='homecity'" .
					"AND value='" . addslashes($args['old']) . "'";
				db_query($sql);
			}
		}
		break;
	case "chooserace":
		if ($session['user']['dragonkills'] == 0)
			break;
		output("`0<a href='newday.php?setrace=$race$resline'>\"BRAAAAAAAAAAAAINS.\"</a>`n`n", true);
		addnav("`0Zombie`0","newday.php?setrace=$race$resline");
		addnav("","newday.php?setrace=$race$resline");
		break;
	case "setrace":
		if ($session['user']['race']==$race){
			output("`0The hairy man nods.  \"`6All right, all right, a simple \"I'm a zombie\" would have done.`0\"  He looks down and writes in his ledger.  \"`6Zed, oh, em, bee, why.  Zombie.  There.`0\"`n`nYou give the man a sheepish look.  \"`#I didn't actually mean to say that.  It just slipped out.`0\"`n`nHe smiles.  \"`6That's okay, I know how it is.  So, how did you get here?`0\"`n`n\"`#As far as I know, it was on a jet PLAAAAAAAAINS oh God!`0\"  You clap your hands over your mouth.`n`nThe gatekeeper chuckles.  \"`6Works every time.`0\"`n`nYou scowl at him.  \"`#Get on with the form.`0\"`n`n\"`6So, have you been a zombie all your life?`0\"`n`nYou pause.  \"`#I think so.  I mean...  Growing up, I think I played with the other zombie kids in zombie school, but...  Hmm.  It's amazing that there would have `ibeen`i a zombie school in the first place.`0\"  You give the man a very serious look.  \"`#This really doesn't make much sense at all, does it?`0\"`n`n\"`6Few things do around here, sunshine.  Do yourself a favour and don't think about it too much.  Unless you want your head to explode.`0\"`n`nThe word rushes up your throat like vomit.  \"`#BRAAAAAAAAAINS!`0\"`n`n\"`6Yes, that's right, brains.  Lovely juicy brains, splattering around all over the shop.  Yum!`0\"`n`nYou scowl at the gatekeeper while convulsing again, your mouth operating of its own accord.  \"`#BRAAAAAAAAAINS you know, that's really not funny.`0\"`n`n\"`6I know.  I shouldn't make fun.  It's a tough life for zombies.  You have it very hard, don't you?  Bits falling off all the time?  Uncontrollable larynx and primeval, base reactions if something sets you off?`0\"`n`n\"`#That's not true.`0\"  You shoot the gatekeeper a withering glare.  \"`#You've just been perpetuating the tired old Zombie stereotype.  And I, for one, am sick of the constant prejudice that comes from people like you.`0\"`n`n\"`6All right, all right, I meant no offense.  I was just having a little joke.`0\"`n`nGrudgingly, you remember your manners.  \"`#Apology accepted.  I didn't mean to get angry.  It's just hard, you know?`0\"`n`n\"`6I know,`0\" says the gatekeeper with a twinkle in his eye.  \"`6You don't need to explain.`0\"`n`n\"`#EXPLAAAAAAAAAAAAAAAAAAINS!  Oh, `ifuck you`i.`0\"  You storm off towards the gate.`n`nBehind you, the gatekeeper cries \"`6You know what, I think it might rain!`0\"`n`n\"`#RAAAAAAAAAINS!`0\" says your mouth.  \"`3`iFuck you, old man,`i`0\" says your internal monologue.  \"`3`iFuck you and your tasty, tasty brains.`i`0\"");
			if (is_module_active("cities")) {
				set_module_pref("homecity",$city,"cities");
				if ($session['user']['age'] == 0)
					$session['user']['location']=$city;
			}
		}
		break;
	case "alternativeresurrect":
	case "stamina-newday":
		if ($session['user']['race']==$race){
			racezombie_checkcity();
			
			//Stamina buffs
			require_once("modules/staminasystem/lib/lib.php");
			apply_stamina_buff('Zombie2', array(
				"name"=>"Zombie Bonus: Combat Endurance",
				"class"=>"Combat",
				"costmod"=>0.8,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('Zombie1', array(
				"name"=>"Zombie Bonus: Hunting Endurance",
				"class"=>"Hunting",
				"costmod"=>0.8,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('Zombie8', array(
				"name"=>"Zombie Bonus: Carcass Cleaning Skills",
				"action"=>"Cleaning the Carcass",
				"costmod"=>0.8,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('Zombie0', array(
				"name"=>"Zombie Penalty: Slow of Mind",
				"class"=>"Global",
				"costmod"=>1,
				"expmod"=>0.8,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('Zombie3', array(
				"name"=>"Zombie Penalty: Slow-Moving",
				"class"=>"Travelling",
				"costmod"=>1.2,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('Zombie4', array(
				"name"=>"Zombie Penalty: Scavenging Slowness",
				"action"=>"Scavenging for Scrap",
				"costmod"=>1.2,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('Zombie5', array(
				"name"=>"Zombie Penalty: Metalwork Ineptitude",
				"action"=>"Metalworking",
				"costmod"=>1.4,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('Zombie6', array(
				"name"=>"Zombie Penalty: Soldering Ineptitude",
				"action"=>"Soldering",
				"costmod"=>1.4,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('Zombie7', array(
				"name"=>"Zombie Penalty: Programming Ineptitude",
				"action"=>"Programming",
				"costmod"=>1.4,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('Zombie9', array(
				"name"=>"Zombie Penalty: Cooking Ineptitude",
				"action"=>"Cooking",
				"costmod"=>1.2,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('raceclassy', array(
				"name"=>"Zombie Bonus: Insults Proficiency",
				"class"=>"Insults",
				"costmod"=>0.8,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));

			//Combat Buffs
			apply_buff("racialbenefit1",array(
				"name"=>"`0Zombie Bonus: No Pain`0",
				"defmod"=>"1.2",
				"allowinpvp"=>1,
				"allowintrain"=>1,
				"rounds"=>-1,
				"schema"=>"module-racezombie",
				)
			);
			apply_buff("racialbenefit2",array(
				"name"=>"`0Zombie Bonus: Leathery Fists`0",
				"atkmod"=>"1.1",
				"allowinpvp"=>1,
				"allowintrain"=>1,
				"rounds"=>-1,
				"schema"=>"module-racezombie",
				)
			);
		}
		break;
	case "creatureencounter":
		if ($session['user']['race']==$race){
			//get those folks who haven't manually chosen a race
			racezombie_checkcity();
			$args['creaturegold']=round($args['creaturegold']*1.2,0);
		}
		break;
	case "battle-victory":
		if ($session['user']['race']==$race && $session['user']['alive']==false){
			if (!$session['user']['alive']){
				$args['creatureexp']=round($args['creatureexp']*0.8);
			}
		}
	break;

	case "validforestloc":
	case "validlocation":
		if (is_module_active("cities"))
			$args[$city]="village-$race";
		break;
	case "moderate":
		if (is_module_active("cities")) {
			tlschema("commentary");
			$args["village-$race"]=sprintf_translate("City of %s", $city);
			tlschema();
		}
		break;
	case "villagetext":
		racezombie_checkcity();
		if ($session['user']['location'] == $city){
			$args['text']=array("`0You are standing in the heart of New Pittsburgh.  This place used to be populated almost entirely by humans, but... well, you know zombies.`n`n");
			$args['schemas']['text'] = "module-racezombie";
			$args['clock']="`n`0From the strength of the overpowering odour, you reason that it is approximately `0%s`0.`n";
			$args['schemas']['clock'] = "module-racezombie";
			if (is_module_active("calendar")) {
				$args['calendar'] = "`n`0Written in blood on a nearby wall is `0%s`0, `0%s %s %s`0.`n";
				$args['schemas']['calendar'] = "module-racezombie";
			}
			$args['title']=array("%s, Home of the Zombies", $city);
			$args['schemas']['title'] = "module-racezombie";
			$args['sayline']="says";
			$args['schemas']['sayline'] = "module-racezombie";
			$args['talk']="`n`0Nearby some zombies talk:`n";
			$args['schemas']['talk'] = "module-racezombie";
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
				$args['newest']="`n`0As you wander your new home, you feel your jaw dropping at the wonderful smells around you.  The smells of BRAAAAAAINS.";
			} else {
				$args['newest']="`n`0Wandering the village, jaw agape and eyeballs swivelling in their sockets, is `0%s`0.";
			}
			$args['schemas']['newest'] = "module-racezombie";
			$args['section']="village-$race";
			$args['stablename']="Mike's Chop Shop";
			$args['schemas']['stablename'] = "module-racezombie";
			$args['gatenav']="Outpost Gates";
			$args['fightnav']="Brains Avenue";
			$args['marketnav']="Shiny Street";
			$args['tavernnav']="Hugs Boulevard";
			$args['schemas']['gatenav'] = "module-racezombie";
			unblocknav("stables.php");
		}
		break;
	case "stablelocs":
		tlschema("mounts");
		$args[$city]=sprintf_translate("The Village of %s", $city);
		tlschema();
		break;
	case "stabletext":
		if ($session['user']['location'] != $city) break;
		$args['title'] = "Mike's Chop Shop";
		$args['schemas']['title'] = "module-racezombie";
		$args['desc'] = array(
			"`0Just next door to the Clan Halls, a scruffy-looking warehouse has been erected.  It looks pretty similar to the other buildings - blood splattered along every wall, groups of undead monsters shambling around, cries of \"BRAAAAAAAAINS!\" emanating from within - you know, the usual.  You head inside.`n`n",
			array("As you venture inside the building, a young Zombie woman shuffles towards you on her one good leg.  \"`^Good day to you, %s - my name is Mike, and what may I do for you today?`0\" You reason that \"Mike\" must be short for \"Michelle\" or something - or perhaps this whole franchise thing is going a bit too far.", translate_inline($session['user']['sex']?'madam':'sir', 'stables'))
		);
		$args['schemas']['desc'] = "module-racezombie";
		$args['lad']="friend";
		$args['schemas']['lad'] = "module-racezombie";
		$args['lass']="friend";
		$args['schemas']['lass'] = "module-racezombie";
		$args['nosuchbeast']="`0\"`^I'm sorry, I've never heard of such a thing,`0\" Mike says apologetically.";
		$args['schemas']['nosuchbeast'] = "module-racezombie";
		$args['toolittle']="`0Mike looks over the handful of currency you offered.  \"`^Hmm.  Well, you see, the price for this %s was actually `0%s `^Requisition tokens and `%%s`^ cigarettes.  My apologies for any misunderstandings.`0\"";
		$args['schemas']['toolittle'] = "module-racezombie";
		$args['replacemount']="`0You sadly watch Mike lead your %s`0 away, along with your cigarettes.  However, when she returns, she brings with her a nice new `0%s`0 which makes you feel a little better.";
		$args['schemas']['replacemount'] = "module-racezombie";
		$args['newmount']="`0You hand over your currency.  Within moments, you become the proud recipient of a lovely new `0%s`0!";
		$args['schemas']['newmount'] = "module-racezombie";
		$args['confirmsale']="`n`n`0Mike eyes your mount up and down, checking it over carefully.  \"`^My, this is indeed a fine specimen.  Are you quite sure you wish to part with it?`0\"";
		$args['schemas']['confirmsale'] = "module-racezombie";
		$args['mountsold']="`0With but a single tear, you hand your %s`0 over to Mike.  The tear dries quickly, and the %s in hand helps you quickly overcome your sorrow.";
		$args['schemas']['mountsold'] = "module-racezombie";
		$args['offer']="`n`n`0Mike offers you `0%s`0 Requisition and `%%s`0 Cigarettes for %s`0.";
		$args['schemas']['offer'] = "module-racezombie";
		break;

	}
	return $args;
}

function racezombie_checkcity(){
	global $session;
	$race="Zombie";
	$city=get_module_setting("villagename");

	if ($session['user']['race']==$race && is_module_active("cities")){
		//if they're this race and their home city isn't right, set it up.
		if (get_module_pref("homecity","cities")!=$city){ //home city is wrong
			set_module_pref("homecity",$city,"cities");
		}
	}
	return true;
}

function racezombie_run(){

}
?>
