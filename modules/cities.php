<?php
// translator ready
// addnews ready
// mail ready

function cities_getmoduleinfo(){
	$info = array(
		"name"=>"Multiple Cities, Simplified Version",
		"version"=>"1.0",
		"author"=>"Eric Stevens, great big chunks ripped out by Dan Hall",
		"category"=>"Village",
		"download"=>"core_module",
		"allowanonymous"=>true,
		"override_forced_nav"=>true,
		"prefs"=>array(
			"Cities User Preferences,title",
			//"traveltoday"=>"How many times did they travel today?,int|0",
			"homecity"=>"User's current home city.|",
		),
		"prefs-drinks"=>array(
			"Cities Drink Preferences,title",
			"servedcapital"=>"Is this drink served in the capital?,bool|1",
		),
	);
	return $info;
}

function cities_install(){
	module_addhook("villagetext");
	module_addhook("validatesettings");
	module_addhook("drinks-check");
	module_addhook("stablelocs");
	module_addhook("camplocs");
	module_addhook("master-autochallenge");
	return true;
}

function cities_uninstall(){
	// This is semi-unsafe -- If a player is in the process of a page
	// load it could get the location, uninstall the cities and then
	// save their location from their session back into the database
	// I think I have a patch however :)
	$city = getsetting("villagename", LOCATION_FIELDS);
	$inn = getsetting("innname", LOCATION_INN);
	$sql = "UPDATE " . db_prefix("accounts") . " SET location='".addslashes($city)."' WHERE location!='".addslashes($inn)."'";
	db_query($sql);
	$session['user']['location']=$city;
	return true;
}

function cities_dohook($hookname,$args){
	global $session;
	$city = getsetting("villagename", LOCATION_FIELDS);
	$home = $session['user']['location']==get_module_pref("homecity");
	$capital = $session['user']['location']==$city;
	switch($hookname){
    case "validatesettings":
		if ($args['dangerchance'] < $args['safechance']) {
			$args['validation_error'] = "Danger chance must be equal to or greater than the safe chance.";
		}
		break;
	case "drinks-check":
		if ($session['user']['location'] == $city) {
			$val = get_module_objpref("drinks", $args['drinkid'], "servedcapital");
			$args['allowdrink'] = $val;
		}
		break;
	case "master-autochallenge":
		global $session;
		if (get_module_pref("homecity")!=$session['user']['location']){
			$info = modulehook("cities-usetravel",
				array(
					"foresttext"=>array("`n`n`^Startled to find your master in %s`^, your heart skips a beat, costing a forest fight from shock.", $session['user']['location']),
					"traveltext"=>array("`n`n`%Surprised at finding your master in %s`%, you feel a little less inclined to be gallivanting around the countryside today.", $session['user']['location']),
					)
				);
			if ($info['success']){
				if ($info['type']=="travel") debuglog("Lost a travel because of being truant from master.");
				elseif ($info['type']=="forest") debuglog("Lost a forest fight because of being truant from master.");
				else debuglog("Lost something, not sure just what, because of being truant from master.");
			}
		}
		break;
	case "villagetext":
		if ($session['user']['location'] == $city){
			// The city needs a name still, but at least now it's a bit
			// more descriptive
			// Let's do this a different way so that things which this
			// module (or any other) resets don't get resurrected.
			$args['text'] = array("All around you, the people of Improbable Central move about their business.  No one seems to pay much attention to you as they all seem absorbed in their own lives and problems.  Along various streets you see many different types of shops, each with a sign out front proclaiming the business done therein.  Off to one side, you see a very curious looking rock which attracts your eye with its strange shape and color.  People are constantly entering and leaving via the city gates to a variety of destinations.`n");
			$args['schemas']['text'] = "module-cities";
			$args['clock']="`n`0The clock on the inn reads `^%s.`0`n";
			$args['schemas']['clock'] = "module-cities";
			// if (is_module_active("calendar")) {
				// $args['calendar']="`n`0You hear a townsperson say that today is `^%1\$s`0, `^%3\$s %2\$s`0, `^%4\$s`0.`n";
				// $args['schemas']['calendar'] = "module-cities";
			// }
			$args['title']=array("%s, the Capital City",$city);
			$args['schemas']['title'] = "module-cities";
			$args['fightnav']="Combat Avenue";
			$args['schemas']['fightnav'] = "module-cities";
			$args['marketnav']="Store Street";
			$args['schemas']['marketnav'] = "module-cities";
			$args['tavernnav']="Ale Alley";
			$args['schemas']['tavernnav'] = "module-cities";
			$args['newestplayer']="";
			$args['schemas']['newestplayer'] = "module-cities";
		}
		if ($home){
			//in home city.
			blocknav("inn.php");
			blocknav("stables.php");
			blocknav("rock.php");
			// blocknav("hof.php");
			blocknav("mercenarycamp.php");
		}elseif ($capital){
			//in capital city.
			//blocknav("forest.php");
			blocknav("train.php");
			blocknav("weapons.php");
			blocknav("armor.php");
		}else{
			//in another city.
			blocknav("train.php");
			blocknav("inn.php");
			blocknav("stables.php");
			blocknav("rock.php");
			blocknav("clans.php");
			// blocknav("hof.php");
			blocknav("mercenarycamp.php");
		}
		break;
	case "stablelocs":
		$args[$city] = sprintf_translate("The City of %s", $city);
		break;
	case "camplocs":
		$args[$city] = sprintf_translate("The City of %s", $city);
		break;
	}
	return $args;
}

function cities_run(){
	return true;
}

?>