<?php

function racekittymorph_getmoduleinfo(){
	$info = array(
		"name"=>"Race - Kittymorph",
		"version"=>"2009-07-01",
		"author"=>"Dan Hall, based on the Humans race by Eric Stevens",
		"category"=>"Races",
		"download"=>"fix this",
	);
	return $info;
}

function racekittymorph_install(){
	module_addhook("chooserace");
	module_addhook("setrace");
	// module_addhook("alternativeresurrect");
	module_addhook("stamina-newday");
	module_addhook("villagetext");
	module_addhook("validlocation");
	module_addhook("validforestloc");
	module_addhook("moderate");
	module_addhook("changesetting");
	module_addhook("stablelocs");
	module_addhook("stabletext");
	module_addhook("racenames");
	module_addhook("battle-victory");
	module_addhook("creatureencounter");
	return true;
}

function racekittymorph_uninstall(){
	global $session;
	$vname = getsetting("villagename", LOCATION_FIELDS);
	$gname = "kittania";
	$sql = "UPDATE " . db_prefix("accounts") . " SET location='$vname' WHERE location = '$gname'";
	db_query($sql);
	if ($session['user']['location'] == $gname)
		$session['user']['location'] = $vname;
	// Force anyone who was a Kittymorph to rechoose race
	$sql = "UPDATE  " . db_prefix("accounts") . " SET race='" . RACE_UNKNOWN . "' WHERE race='Kittymorph'";
	db_query($sql);
	if ($session['user']['race'] == 'Kittymorph')
		$session['user']['race'] = RACE_UNKNOWN;
	return true;
}

function racekittymorph_dohook($hookname,$args){
	global $session,$resline;
	$city = "Kittania";
	$race = "Kittymorph";
	switch($hookname){
	case "racenames":
		$args[$race] = $race;
		break;
	case "changesetting":
		// Ignore anything other than villagename setting changes
		if ($args['setting'] == "villagename" && $args['module']=="racekittymorph") {
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
		if ($session['user']['dragonkills'] == 0 ){
			break;
		}
		output("`0You smile, and turn around to show the gatekeeper your tail.  <a href='newday.php?setrace=$race$resline'>\"Um... notice anything unusual?\"</a>`n`n", true);
		addnav("`&Kittymorph`0","newday.php?setrace=$race$resline");
		addnav("","newday.php?setrace=$race$resline");
		break;
	case "setrace":
		if ($session['user']['race']==$race){
			output("\"`6Oh, I see, right,`0\" says the gatekeeper, looking down at his ledger.  \"`6A kittymorph, then, okay, let's see, here...  Kay, eye, tee, ee, em, oh, are, eff.  Kittymorph.`0\"  He looks up again.  \"`6Um.  You can turn around again, now.`0\"`n`nYou oblige.  \"`#Sorry.`0\"`n`n\"`6Don't worry about it.  Have you always been like this?`0\"`n`n\"`#Since I was a kitten,`0\" you reply.  \"`#I don't know what that crazy woman back there was talking about; she says I fell out of a plane and hit my head.  Rubbish.`0\"`n`n\"`6Of course,`0\" says the gatekeeper, smiling.  \"`6You would have landed on your feet, wouldn't you?`0\"`n`n\"`#That's right.  Although...`0\"  You look down, puzzled.  \"`#Some things just don't add up...`0\" you mutter.`n`n\"`6Well, don't worry,`0\" says the gatekeeper.  \"`6Just head into town and get some nice clothes, and you'll sort everything out, I'm sure.  If you wear clothes, that is.`0\"`n`nYou grin.  \"`#When it suits me.`0\"  You saunter off through the gates.  You don't really know how to walk any other way.");
			if (is_module_active("cities")) {
				set_module_pref("homecity",$city,"cities");
				if ($session['user']['age'] == 0) $session['user']['location']=$city;
			}
		}
		break;
	case "alternativeresurrect":
	case "stamina-newday":
		if ($session['user']['race']==$race){
			racekittymorph_checkcity();
			
			//Stamina buffs
			require_once("modules/staminasystem/lib/lib.php");
			apply_stamina_buff('kittymorph3', array(
				"name"=>"KittyMorph Bonus: Travelling Speed",
				"class"=>"Travelling",
				"costmod"=>0.8,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('kittymorph4', array(
				"name"=>"KittyMorph Bonus: Cooking and Carcass Cleaning Expertise",
				"class"=>"Meat",
				"costmod"=>0.7,
				"expmod"=>1.2,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('kittymorph1', array(
				"name"=>"KittyMorph Penalty: Hunting Indifference",
				"class"=>"Hunting",
				"costmod"=>1.1,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('kittymorph2', array(
				"name"=>"KittyMorph Penalty: Combat Indifference",
				"class"=>"Combat",
				"costmod"=>1.1,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('kittymorph5', array(
				"name"=>"KittyMorph Penalty: Technical Ineptitude",
				"class"=>"ScrapBots",
				"costmod"=>1.1,
				"expmod"=>0.8,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('raceclassy', array(
				"name"=>"KittyMorph Bonus: Classy Insults Proficiency",
				"action"=>"Insults - Classy",
				"costmod"=>0.9,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('raceconfusing', array(
				"name"=>"KittyMorph Bonus: Confusing Insults Proficiency",
				"action"=>"Insults - Confusing",
				"costmod"=>0.5,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			
			//combat buffs
			apply_buff("racialbenefit1",array(
				"name"=>"`7KittyMorph Penalty: Slender`0",
				"defmod"=>"0.8",
				"allowinpvp"=>1,
				"allowintrain"=>1,
				"rounds"=>-1,
				"schema"=>"module-racekittymorph",
				)
			);
			apply_buff("racialbenefit2",array(
				"name"=>"`7KittyMorph Bonus: Claws`0",
				"atkmod"=>"1.2",
				"allowinpvp"=>1,
				"allowintrain"=>1,
				"rounds"=>-1,
				"schema"=>"module-racekittymorph",
				)
			);
		}
		break;
	case "creatureencounter":
		if ($session['user']['race']==$race){
			//get those folks who haven't manually chosen a race
			racekittymorph_checkcity();
			if ($session['user']['armordef'] == 0){
				apply_buff("nudekitty",array(
					"name"=>"`7KittyMorph Bonus: Nude Fighting`0",
					"badguyatkmod"=>0.7,
					"badguydefmod"=>0.7,
					"allowinpvp"=>1,
					"allowintrain"=>1,
					"rounds"=>-1,
					"roundmsg"=>"Because you are fighting completely starkers, {badguy} is hilariously distracted and cannot attack or defend as effectively!",
					"expireafterfight"=>1,
					"schema"=>"module-racekittymorph",
					)
				);
			}
		}
		break;
	case "battle-victory":
		if ($session['user']['race']==$race){
			if (!$session['user']['alive']){
				debug($args['creatureexp']);
				$args['creatureexp']=round($args['creatureexp'] * 1.3);
				debug($args['creatureexp']);
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
		racekittymorph_checkcity();
		if ($session['user']['location'] == $city){
			$args['text']=array("`0You are standing in the heart of Kittania.  Though officially registered as a city, it only earned that title because there were so many sentient creatures living there.`n`nIn reality, the city is a very basic affair, little more than a pack living together in common conditions.  Kittymorphs are a little too lazy to build much.`n");
			$args['schemas']['text'] = "module-racekittymorph";
			$args['clock']="`n`0From the position of the sun in the sky, you reckon it's about `&%s`0.`n";
			$args['schemas']['clock'] = "module-racekittymorph";
			if (is_module_active("calendar")) {
				$args['calendar'] = "`n`0Scrawled in dust on the floor is the current date, `&%s`0, `&%s %s %s`0.`n";
				$args['schemas']['calendar'] = "module-racekittymorph";
			}
			$args['title']=array("%s, Home of the Kittymorphs", $city);
			$args['schemas']['title'] = "module-racekittymorph";
			$args['sayline']="says";
			$args['schemas']['sayline'] = "module-racekittymorph";
			$args['talk']="`n`&Nearby some kittymorphs talk:`n";
			$args['schemas']['talk'] = "module-racekittymorph";
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
				$args['newest']="`n`0As you wander your new home, you feel your jaw dropping at the wonderful smells around you.";
			} else {
				$args['newest']="`n`0Wandering the village, jaw agape and buck naked, is `&%s`0.";
			}
			$args['schemas']['newest'] = "module-racekittymorph";
			$args['section']="village-$race";
			$args['stablename']="Mike's Chop Shop";
			$args['schemas']['stablename'] = "module-racekittymorph";
			$args['gatenav']="Outpost Gates";
			$args['fightnav']="Toothclaw Close";
			$args['marketnav']="The Cul-De-Sac of Sparkly Things";
			$args['tavernnav']="Distraction Avenue";
			$args['schemas']['gatenav'] = "module-racekittymorph";
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
		$args['schemas']['title'] = "module-racekittymorph";
		$args['desc'] = array(
			"`6Just next door to the Clan Halls, a rather basic-looking set of stables has been erected.`n`n",
			array("As you head inside, you notice an obvious lack of the typical stable smell.  Apparently KittyMorphs, for all their laziness, like to keep things clean.`n`n\"`^Well hello there!  What can I do for you, my good %s?`6\" asks a grey-furred KittyMorph male whose name you're absolutely `icertain`i is not \"Mike.\"", translate_inline($session['user']['sex']?'lady':'man', 'stables'))
		);
		$args['schemas']['desc'] = "module-racekittymorph";
		$args['lad']="friend";
		$args['schemas']['lad'] = "module-racekittymorph";
		$args['lass']="friend";
		$args['schemas']['lass'] = "module-racekittymorph";
		$args['nosuchbeast']="`6\"`^Hmm.  Not heard of that one,`6\" Mike says apologetically.";
		$args['schemas']['nosuchbeast'] = "module-racekittymorph";
		$args['toolittle']="`6Mike looks over the handful of currency you offered.  \"`^Aha.  Well, the price for this %s, you see, was actually `&%s `^Requisition  and `%%s`^ cigarettes.  Maybe you miscounted?`6\"";
		$args['schemas']['toolittle'] = "module-racekittymorph";
		$args['replacemount']="`6You sadly watch Mike lead your %s`6 away, along with your cigarettes.  However, when he returns, he brings with him a nice new `&%s`6 which makes you feel a little better.";
		$args['schemas']['replacemount'] = "module-racekittymorph";
		$args['newmount']="`6You hand over your currency.  Within moments, you become the proud recipient of a lovely new `&%s`6!";
		$args['schemas']['newmount'] = "module-racekittymorph";
		$args['confirmsale']="`n`n`6Mike eyes your mount up and down, checking it over carefully.  \"`^Yes, yes, that's a really nice example, right there - are you quite sure you want to part with it?`6\"";
		$args['schemas']['confirmsale'] = "module-racekittymorph";
		$args['mountsold']="`6With but a single tear, you hand your %s`6 over to Mike.  The tear dries quickly, and the %s in hand helps you quickly overcome your sorrow.";
		$args['schemas']['mountsold'] = "module-racekittymorph";
		$args['offer']="`n`n`6Mike offers you `&%s`6 Requisition and `%%s`6 Cigarettes for %s`6.";
		$args['schemas']['offer'] = "module-racekittymorph";
		break;
	}
	return $args;
}

function racekittymorph_checkcity(){
	global $session;
	$race="Kittymorph";
	$city="Kittania";

	if ($session['user']['race']==$race && is_module_active("cities")){
		//if they're this race and their home city isn't right, set it up.
		if (get_module_pref("homecity","cities")!=$city){ //home city is wrong
			set_module_pref("homecity",$city,"cities");
		}
	}
	return true;
}

function racekittymorph_run(){

}
?>
