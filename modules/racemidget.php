<?php

function racemidget_getmoduleinfo(){
	$info = array(
		"name"=>"Race - Midget",
		"version"=>"2009-06-29",
		"author"=>"Dan Hall, based on the Humans race by Eric Stevens",
		"category"=>"Races",
		"download"=>"fix this",
		"settings"=>array(
			"villagename"=>"name of village,text|Squat Hole"
		),
		"prefs" => array(
			"Specialty - Midget Rage user prefs,title",
			"midgetrage"=>"Midget Rage points,int|0",
		),
	);
	return $info;
}

function racemidget_install(){
	module_addhook("chooserace");
	module_addhook("setrace");
	module_addhook("stamina-newday");
	module_addhook("villagetext");
	module_addhook("validlocation");
	module_addhook("validforestloc");
	module_addhook("moderate");
	module_addhook("changesetting");
	module_addhook("stablelocs");
	module_addhook("stabletext");
	module_addhook("racenames");
	module_addhook("fightnav-specialties");
	module_addhook("apply-specialties");
	module_addhook("startofround");
	module_addhook("endofround");
	// module_addhook("alternativeresurrect");
	module_addhook("creatureencounter");
	module_addhook("battle-victory");
	return true;
}

function racemidget_uninstall(){
	global $session;
	$vname = getsetting("villagename", LOCATION_FIELDS);
	$gname = "Squat Hole";
	$sql = "UPDATE " . db_prefix("accounts") . " SET location='$vname' WHERE location = '$gname'";
	db_query($sql);
	if ($session['user']['location'] == $gname)
		$session['user']['location'] = $vname;
	// Force anyone who was a Midget to rechoose race
	$sql = "UPDATE  " . db_prefix("accounts") . " SET race='" . RACE_UNKNOWN . "' WHERE race='Midget'";
	db_query($sql);
	if ($session['user']['race'] == 'Midget')
		$session['user']['race'] = RACE_UNKNOWN;
	return true;
}

function racemidget_dohook($hookname,$args){
	global $session,$resline;
	$city = get_module_setting("villagename");
	$race = "Midget";
	static $damagestart = 0;
	switch($hookname){
	case "racenames":
		$args[$race] = $race;
		break;
	case "changesetting":
		// Ignore anything other than villagename setting changes
		if ($args['setting'] == "villagename" && $args['module']=="racemidget") {
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
		if ($session['user']['dragonkills'] < 3) break;
		output("`0You stand on tiptoes, thrust a tiny upraised middle finger at the gatekeeper's face, and say <a href='newday.php?setrace=$race$resline'>\"Does it fookin' LOOK like I'm a yooman, dick'ead?\"</a>`n`n", true);
		addnav("`&Midget`0","newday.php?setrace=$race$resline");
		addnav("","newday.php?setrace=$race$resline");
		break;
	case "setrace":
		if ($session['user']['race']==$race){
			set_module_pref("midgetrage",0);
			output("`0\"`6All right, all right, you don't have to shout,`0\" says the gatekeeper.  He pulls out his ledger.  \"`6Right, then.  Em, eye, jay, eye, tee.  Midget.`0\"`n`n\"`#That's right,`0\" you say, hoping that he doesn't cotton on to the fact that you can't read or write.  \"`#And don't forget it, innit?`0\"`n`n\"`6Have you been like this all your life?`0\"`n`n\"`#What's it to you, pal?!`0\"`n`nThe gatekeeper sighs.  \"`6Nothing, just making conversation.  Off you go, then.`0\"`n`nIncensed, you scream up at the gatekeeper.  \"`#Don't tell ME to fook off!  I'll go where I fookin' well like!`0\"`n`n\"`6If I remember rightly,`0\" says the gatekeeper, \"`6you came here so that you could enter the outpost, right?  Well, there's nothing stopping you now.`0\"`n`n\"`#Don'tchu fookin' get clever with me, mate!  I'll fookin' deck yer!  Come on then, right here, right now!  I'll fookin' FLOOR yer, dick'ead!`0\"`n`nThe gatekeeper sighs again, steps out through a door in the back of his hut, walks around to the front and kicks you, very hard, in the crotch.  You sail over the wall like a football.`n`n\"`6Have a pleasant stay,`0\" calls the gatekeeper.");
			
				set_module_pref("homecity",$city,"cities");
				if ($session['user']['age'] == 0)
					$session['user']['location']=$city;
			
		}
		break;
	case "stamina-newday":
	case "alternativeresurrect":
		if ($session['user']['race']==$race){
			racemidget_checkcity();
			set_module_pref("midgetrage",0);
			
			//Stamina buffs
			require_once("modules/staminasystem/lib/lib.php");
			apply_stamina_buff('midget2', array(
				"name"=>"Midget Bonus: Combat Skills",
				"class"=>"Combat",
				"costmod"=>0.5,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('midget1', array(
				"name"=>"Midget Bonus: Hunting Skills",
				"class"=>"Hunting",
				"costmod"=>0.5,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('midget4', array(
				"name"=>"Midget Bonus: Scavenging Proficiency",
				"action"=>"Scavenging for Scrap",
				"costmod"=>0.5,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('midget8', array(
				"name"=>"Midget Bonus: Carcass Cleaning Skills",
				"action"=>"Cleaning the Carcass",
				"costmod"=>0.8,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('midget0', array(
				"name"=>"Midget Penalty: Stupidity",
				"class"=>"Global",
				"costmod"=>1,
				"expmod"=>0.25,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('midget3', array(
				"name"=>"Midget Penalty: Stubby Little Legs",
				"class"=>"Travelling",
				"costmod"=>2,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('midget5', array(
				"name"=>"Midget Penalty: Metalwork Incompetence",
				"action"=>"Metalworking",
				"costmod"=>1.8,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('midget6', array(
				"name"=>"Midget Penalty: Soldering Incompetence",
				"action"=>"Soldering",
				"costmod"=>1.8,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('midget7', array(
				"name"=>"Midget Penalty: Programming Ultra-Incompetence",
				"action"=>"Programming",
				"costmod"=>2,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('midget9', array(
				"name"=>"Midget Penalty: Cooking Ineptitude",
				"action"=>"Cooking",
				"costmod"=>1.2,
				"expmod"=>1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('raceclassy', array(
				"name"=>"Midget Penalty: Classy Insults Super-Duper Incompetence",
				"action"=>"Insults - Classy",
				"costmod"=>5,
				"expmod"=>0.25,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('raceconfusing', array(
				"name"=>"Midget Penalty: Confusing Insults Confusion",
				"action"=>"Insults - Confusing",
				"costmod"=>2,
				"expmod"=>0.25,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('racecoarse', array(
				"name"=>"Midget Bonus: Coarse Insults Proficiency",
				"action"=>"Insults - Coarse",
				"costmod"=>0.5,
				"expmod"=>0.25,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			
			//Combat Buffs
			apply_buff("racialbenefit1",array(
				"name"=>"`7Midget Bonus: Freakish Strength`0",
				"atkmod"=>"1.2",
				"allowinpvp"=>1,
				"allowintrain"=>1,
				"rounds"=>-1,
				"schema"=>"module-racemidget",
				)
			);
			apply_buff("racialbenefit2",array(
				"name"=>"`7Midget Bonus: Easily Missed`0",
				"defmod"=>"1.2",
				"allowinpvp"=>1,
				"allowintrain"=>1,
				"rounds"=>-1,
				"schema"=>"module-racemidget",
				)
			);
			
		}
		break;
	case "creatureencounter":
		if ($session['user']['race']==$race){
			//get those folks who haven't manually chosen a race
			racemidget_checkcity();
			$args['creaturegold']=round($args['creaturegold']*1.2,0);
		}
		break;
	case "battle-victory":
		if ($session['user']['race']==$race){
			//get those folks who haven't manually chosen a race
			racemidget_checkcity();
			if (!$session['user']['alive']){
				debug($args['creatureexp']);
				$args['creatureexp']=round($args['creatureexp']/2);
				debug($args['creatureexp']);
			}
		}
		break;
	case "startofround":
		if ($session['user']['race'] == $race) {
			$damagestart = ($session['user']['hitpoints']/$session['user']['maxhitpoints'])*100;
		}
		break;
	case "endofround":
		if ($session['user']['race'] == $race) {
			$damage = $damagestart - (($session['user']['hitpoints']/$session['user']['maxhitpoints'])*100);
			if ($damage > 0){
				debug("Midget Rage increment: ".$damage);
				increment_module_pref("midgetrage",$damage);
			}
		}
		break;
	case "fightnav-specialties":
		if ($session['user']['race'] == $race) {
			addnav("Midget Rage");
			$rage = round(get_module_pref("midgetrage"),2);
			if ($rage > 100){
				$script = $args['script'];
				addnav("Invoke Midget Rage!",$script."op=fight&skill=midgetrage");
			} else {
				$notrage = 100 - $rage;
				addnav("$rage%<br /><table style='border: solid 1px #000000' bgcolor='#cc0000' cellpadding='0' cellspacing='0' width='70' height='5'><tr><td width='$rage' bgcolor='#ffff00'></td><td width='$notrage'></td></tr></table>","",true);
			}
		}
		break;
	case "apply-specialties":
		if ($session['user']['race'] == $race) {
			$skill = httpget('skill');
			if ($skill=="midgetrage"){
				apply_buff("midgetrage", array(
					"startmsg"=>"`4Bug-eyed, screaming and foaming at the mouth like a Daily Mail columnist talking about Political Correctness Gone Mad, you fly into a terrifying Midget Rage!",
					"name"=>"`^Midget Rage`0",
					"rounds"=>5,
					"atkmod"=>2,
					"wearoff"=>"`4You feel yourself coming down from your Midget Rage.`0",
					"schema"=>"module-racemidget"
				));
				set_module_pref("midgetrage",0);
			}
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
		racemidget_checkcity();
		if ($session['user']['location'] == $city){
			$args['text']=array("`0You are standing in the heart of Squat Hole.  Man, this place has gone downhill.`n`nRusted-out cars compete for space with empty cider cans, and there's a general atmosphere of meanness about.  Piles of dead skunks and excrement lie steaming and glistening by the side of the road.  All the windows are broken, and the whole place could do with a good scrubbing, but there's no chance for investment around here - there is truth in the rumour that copper wire was invented by two Midgets fighting over a penny.`n`n");
			$args['schemas']['text'] = "module-racemidget";
			$args['clock']="`0From the strength of the overpowering odour, you reason that it is approximately `&%s`0.`n`n";
			$args['schemas']['clock'] = "module-racemidget";
			if (is_module_active("calendar")) {
				$args['calendar'] = "`0Written in blood on a nearby wall is `&%s`0, `&%s %s %s`0.`n`n";
				$args['schemas']['calendar'] = "module-racemidget";
			}
			$args['title']=array("%s, Home of the Midgets", $city);
			$args['schemas']['title'] = "module-racemidget";
			$args['sayline']="says";
			$args['schemas']['sayline'] = "module-racemidget";
			$args['talk']="`0Nearby some midgets stand around smoking hand-rolled cigarettes, using a decomposing chicken carcass as an ashtray.  Occasionally they shout at each other in irritating, squeaky voices:`n`n";
			$args['schemas']['talk'] = "module-racemidget";
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
				$args['newest']="`n`0As you wander your new home, you feel your jaw dropping at just how ghastly everything is.";
			} else {
				$args['newest']="`n`0Wandering the village, gawping at the state of disarray and looking as though someone had just dumped a boxfull of dead rats on their kitchen table is `&%s`0.";
			}
			$args['schemas']['newest'] = "module-racemidget";
			$args['section']="village-$race";
			$args['stablename']="Mike's Chop Shop";
			$args['schemas']['stablename'] = "module-racemidget";
			$args['gatenav']="Manky, Rusted Gates";
			$args['fightnav']="Brawlin' Street, like";
			$args['marketnav']="Shopliftin' Avenue, innit";
			$args['tavernnav']="All That Other Shite Road, ya dick'head";
			$args['schemas']['gatenav'] = "module-racemidget";
			unblocknav("stables.php");
		}
		break;
	case "stabletext":
		if ($session['user']['location'] != $city) break;
		$args['title'] = "Mike's Chop Shop";
		$args['schemas']['title'] = "module-racemidget";
		$args['desc'] = array(
			"`6Just next door to the Clan Halls, a scruffy-looking warehouse has been erected.  The stench from the general area is indescribable - even worse than the pervasive funk that hangs over Squat Hole in general.  You hold your nose, and head inside.`n`n",
			array("As you venture further into the building, Mike scowls at you.  \"`^Wha' the fook do `iyou`i want, %s?`6\" he asks in an irritating, squeaky voice.", translate_inline($session['user']['sex']?'bitch':'dick\'ead', 'stables'))
		);
		$args['schemas']['desc'] = "module-racemidget";
		$args['lad']="friend";
		$args['schemas']['lad'] = "module-racemidget";
		$args['lass']="friend";
		$args['schemas']['lass'] = "module-racemidget";
		$args['nosuchbeast']="`6\"`^Y'wot?  Wha' tha fook's tha'?`6\" Mike says apologetically.";
		$args['schemas']['nosuchbeast'] = "module-racemidget";
		$args['toolittle']="`6Mike looks over the handful of currency you offered.  \"`^Yer fookin' numpty.  I said, fer this 'ere %s, yer lookin' at `&%s `^Req  and `%%s`^ fags.  Nah fook off an' come back when ye've got it.`6\"";
		$args['schemas']['toolittle'] = "module-racemidget";
		$args['replacemount']="`6You sadly watch Mike lead your %s`6 away, along with your cigarettes.  However, when he returns, he brings with him a nice new `&%s`6 which makes you feel a little better.";
		$args['schemas']['replacemount'] = "module-racemidget";
		$args['newmount']="`6You hand over your currency, being careful not to touch the midget.  Within moments, you become the proud recipient of a lovely new `&%s`6!";
		$args['schemas']['newmount'] = "module-racemidget";
		$args['confirmsale']="`n`n`6Mike eyes your mount up and down, checking it over carefully.  \"`^Yeah, it's no' bad.  I guess.  Yer sure ye wanna flog it?`6\"";
		$args['schemas']['confirmsale'] = "module-racemidget";
		$args['mountsold']="`6With but a single tear, you hand your %s`6 over to Mike.  The tear dries quickly, and the %s in hand helps you quickly overcome your sorrow.";
		$args['schemas']['mountsold'] = "module-racemidget";
		$args['offer']="`n`n`6Mike offers you `&%s`6 Requisition and `%%s`6 Cigarettes for %s`6.";
		$args['schemas']['offer'] = "module-racemidget";
		break;
	case "stablelocs":
		tlschema("mounts");
		$args[$city]=sprintf_translate("The Village of %s", $city);
		tlschema();
		break;

	}
	return $args;
}

function racemidget_checkcity(){
	global $session;
	$race="Midget";
	$city="Squat Hole";

	if ($session['user']['race']==$race){
		//if they're this race and their home city isn't right, set it up.
		if (get_module_pref("homecity","cities")!=$city){ //home city is wrong
			set_module_pref("homecity",$city,"cities");
		}
	}
	return true;
}

function racemidget_run(){

}
?>
