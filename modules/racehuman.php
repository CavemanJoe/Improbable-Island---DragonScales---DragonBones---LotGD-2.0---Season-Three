<?php

function racehuman_getmoduleinfo(){
	$info = array(
		"name"=>"Race - Human (Improbable Edition)",
		"version"=>"2009-07-21",
		"author"=>"Dan Hall, based on core DragonPrime Race files",
		"category"=>"Races",
		"download"=>"fix_this",
		);
	return $info;
}

function racehuman_install(){
	module_addhook("chooserace");
	module_addhook("setrace");
	module_addhook("villagetext");
	module_addhook("validlocation");
	module_addhook("validforestloc");
	module_addhook("moderate");
	module_addhook("changesetting");
	module_addhook("raceminedeath");
	module_addhook("stablelocs");
	module_addhook("stabletext");
	module_addhook("racenames");
	return true;
}

function racehuman_uninstall(){
	global $session;
	$vname = getsetting("villagename", LOCATION_FIELDS);
	$gname = "NewHome";
	$sql = "UPDATE " . db_prefix("accounts") . " SET location='$vname' WHERE location = '$gname'";
	db_query($sql);
	if ($session['user']['location'] == $gname)
		$session['user']['location'] = $vname;
	// Force anyone who was a Human to rechoose race
	$sql = "UPDATE  " . db_prefix("accounts") . " SET race='" . RACE_UNKNOWN . "' WHERE race='Human'";
	db_query($sql);
	if ($session['user']['race'] == 'Human')
		$session['user']['race'] = RACE_UNKNOWN;
	return true;
}

function racehuman_dohook($hookname,$args){
	global $session,$resline;
	$city = "NewHome";
	$race = "Human";
	switch($hookname){
	case "racenames":
		$args[$race] = $race;
		break;
	case "changesetting":
		if ($args['setting'] == "villagename" && $args['module']=="racehuman") {
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
		output("`0You boggle at the question.<a href='newday.php?setrace=$race$resline'>\"Of course I'm human!  What sort of mad place is this?!\"</a>`n`n", true);
		addnav("`&Human`0","newday.php?setrace=$race$resline");
		addnav("","newday.php?setrace=$race$resline");
		break;
	case "setrace":
		if ($session['user']['race']==$race){
			output("`0The hairy man squints at you.  \"`6Sorry, with these Flash grenades going off all the time, you know how it is...`0\"  He reaches underneath his desk, pulls out a pair of glasses and puts them on.  His eyes are magnified four times over.  \"`6Ah, I see now.  Yes, you look human to me.`0\"  He looks down and writes in a little ledger.  \"`6Haitch, you, em, ee, en.  Human.`0\"`n`nYou frown at the Coke-bottle lenses.  \"`#How, exactly, were you doing the crossword?`0\"`n`nThe hairy man leans forward, cups his ear.  \"`6What?`0\"`n`n\"`#Never mind.`0\"`n`n\"`6Not quite blind, no.`0\"`n`nYou shake your head.  \"`#Forget it.`0\"`n`nThe hairy man looks puzzled.  \"`6Regret what?`0\"`n`nYou open your mouth to say something, then close it again and shake your head.`n`nThe gatekeeper shrugs.  \"`6Go on, then.  In you go, and choose your implant.`0\"`n`n\"`#That's it?  \"Are you human,\" that's the whole form?`0\"`n`nThe gatekeeper smiles.  \"`6Paperwork reduction act.`0\"`n`nYou shrug, and walk in to the Outpost.  At least the weather is nice.");
			set_module_pref("homecity",$city,"cities");
			if ($session['user']['age'] == 0) $session['user']['location']=$city;
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
		racehuman_checkcity();
		if ($session['user']['location'] == $city){
			$args['text']=array("`0You are standing in the heart of NewHome.  Though called a city, this stronghold of humans is little more than a fortified village.  The city's low defensive walls are surrounded by rolling plains which gradually turn into dense, moist jungle in one direction, and a stretch of beach and ocean in the other.  Some residents are engaged in conversation outside the Museum.`n");
			$args['schemas']['text'] = "module-racehuman";
			$args['clock']="`n`0A nearby loudspeaker announces that the time is currently `&%s`0.`n";
			$args['schemas']['clock'] = "module-racehuman";
			if (is_module_active("calendar")) {
				$args['calendar'] = "`n`0A smaller contraption next to it reads `&%s`0, `&%s %s %s`0.`n";
				$args['schemas']['calendar'] = "module-racehuman";
			}
			$args['title']=array("%s, City of Humans", $city);
			$args['schemas']['title'] = "module-racehuman";
			$args['sayline']="says";
			$args['schemas']['sayline'] = "module-racehuman";
			$args['talk']="`n`&Nearby some contestants talk:`n";
			$args['schemas']['talk'] = "module-racehuman";
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
				$args['newest']="`n`0As you wander your new home, you stare openly at the various different types of horrendous-looking creatures standing around you.  But that's okay - they can see that you're new, and they don't mind the staring.";
			} else {
				$args['newest']="`n`0Wandering the outpost, gawping at the strange people around, is `&%s`0.";
			}
			$args['schemas']['newest'] = "module-racehuman";
			$args['section']="village-$race";
			$args['gatenav']="Outpost Gates";
			$args['fightnav']="Northern Quadrant";
			$args['marketnav']="Eastern Quadrant";
			$args['tavernnav']="Western Quadrant";
			$args['schemas']['gatenav'] = "module-racehuman";
			unblocknav("stables.php");
		}
		break;
	case "stabletext":
		if ($session['user']['location'] != $city) break;
		$args['title'] = "Mike's Chop Shop";
		$args['schemas']['title'] = "module-racehuman";
		$args['desc'] = array(
			"`6Just next door to the Clan Halls, a rather strange building has been erected, with a very strange smell wafting from within.  An unusual blend of manure and oil.`n`nInside, you are greeted by an array of unusual-looking beasts and vehicles.`n`n",
			array("As you venture further into the building, Mike smiles broadly, \"`^Ahh! how can I help you today, %s?`6\" he asks in a booming voice.", translate_inline($session['user']['sex']?'lass':'lad', 'stables'))
		);
		$args['schemas']['desc'] = "module-racehuman";
		$args['lad']="friend";
		$args['schemas']['lad'] = "module-racehuman";
		$args['lass']="friend";
		$args['schemas']['lass'] = "module-racehuman";
		$args['nosuchbeast']="`6\"`^Never heard of it,`6\" Mike says apologetically.";
		$args['schemas']['nosuchbeast'] = "module-racehuman";
		$args['toolittle']="`6Mike looks over the handful of currency you offered.  \"`^Obviously you misheard my price.  This %s will cost you `&%s `^Requisition  and `%%s`^ Cigarettes.  No more, no less.`6\"";
		$args['schemas']['toolittle'] = "module-racehuman";
		$args['replacemount']="`6You sadly watch Mike lead your %s`6 away, along with your cigarettes.  However, when he returns, he brings with him a nice new `&%s`6 which makes you feel a little better.";
		$args['schemas']['replacemount'] = "module-racehuman";
		$args['newmount']="`6You hand over your currency.  Within moments, you become the proud recipient of a lovely new `&%s`6!";
		$args['schemas']['newmount'] = "module-racehuman";
		$args['confirmsale']="`n`n`6Mike eyes your mount up and down, checking it over carefully.  \"`^It's a nice one, to be sure.  Are you quite sure you want to part with it?`6\"";
		$args['schemas']['confirmsale'] = "module-racehuman";
		$args['mountsold']="`6With but a single tear, you hand your %s`6 over to Mike.  The tear dries quickly, and the %s in hand helps you quickly overcome your sorrow.";
		$args['schemas']['mountsold'] = "module-racehuman";
		$args['offer']="`n`n`6Mike offers you `&%s`6 Requisition and `%%s`6 Cigarettes for %s`6.";
		$args['schemas']['offer'] = "module-racehuman";
		break;
	case "stablelocs":
		tlschema("mounts");
		$args[$city]=sprintf_translate("The Village of %s", $city);
		tlschema();
		break;
	}
	return $args;
}

function racehuman_checkcity(){
	global $session;
	$race="Human";
	$city="NewHome";

	if ($session['user']['race']==$race && is_module_active("cities")){
		//if they're this race and their home city isn't right, set it up.
		if (get_module_pref("homecity","cities")!=$city){ //home city is wrong
			set_module_pref("homecity",$city,"cities");
		}
	}
	return true;
}

function racehuman_run(){

}
?>
