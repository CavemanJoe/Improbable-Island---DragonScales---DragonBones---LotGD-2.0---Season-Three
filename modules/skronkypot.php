<?php

require_once("lib/villagenav.php");
require_once("lib/http.php");

function skronkypot_getmoduleinfo(){
	$info = array(
		"name"=>"Skronky Pot",
		"version"=>"0.1",
		"author"=>"Dan Hall",
		"category"=>"Village",
		"download"=>"",
		"settings"=>array(
			"capacity"=>"Capacity of the Skronky Pot,int|200",
			"reward"=>"How much Requisition is given per lungful of phlegm,int|25",
			"contents"=>"Current contents of the Skronky Pot,int|0",
			"maxdailyhacks"=>"Maximum number of times per day a user is allowed to contribute,int|10",
		),
		"prefs"=>array(
			"dailyhacks"=>"How many times has the user contributed today,int|0",
			"skronkyvirgin"=>"Is this their first time at the Skronky Pot,int|1",
		),
	);
	return $info;
}
function skronkypot_install(){
	$condition = "if (\$session['user']['location'] == \"Squat Hole\") {return true;} else {return false;};";
	module_addhook("village",false,$condition);
	module_addhook("newday");
	return true;
}
function skronkypot_uninstall(){
	return true;
}
function skronkypot_dohook($hookname,$args){
	global $session;
	switch($hookname){
	case "village":
		tlschema($args['schemas']['fightnav']);
		addnav($args['fightnav']);
		tlschema();
		addnav("Skronky Pot","runmodule.php?module=skronkypot&op=examine");
		break;
	case "newday":
		set_module_pref("dailyhacks",0);
		break;
	}
	return $args;
}
function skronkypot_run(){
	global $session;
	page_header("The Skronky Pot");
	switch (httpget("op")){
		case "examine":
			// have they been introduced to the pleasures of the Skronky Pot?
			if (get_module_pref("skronkyvirgin")==0){
				output("`0Two midgets sit smoking around the Skronky Pot.  They give you a wave and a one-fingered salute as they recognise you, and beckon you towards the fetid bucket.`n`n");
			}
			if (get_module_pref("skronkyvirgin")==1){
				output("`0`0Two midgets sit smoking around a bucket filled with what appears to be thick yellow custard.`n`nOne of them looks up as you lean in to get a closer look.`n`n\"`^What, mate, you wanna contribute?`0\" he asks in an annoying squeaky voice.  \"`^Give ya twenny-five Req per hack.`0\"`n`n\"`#Contribute?`0\" you ask, bemused.  \"`#I'm not sure I follow you.`0\"`n`n\"`^You thick or summat, dick'ead?  Jus' cough yer lung butter inter the bucket, yer get free money, innit?  It's a valuable commodity, is that.`0\"`n`nWith a horrible realisation dawning upon you, you look into the bucket.  Several cigarette butts are floating inside.  The contents smell like bacon and nicotine.`n`n\"`#Why?  What the hell do you use it for?`0\" you ask, incredulous.`n`n\"`^Makin' fings fer der yoomans.  Soap an' dat.  Cangles.  Cheap ciggies, once we've boiled an' strained der nikkitine out an' mixed it wi' some nettles.  Fookin' posh toss an' shite, innit.`0\"`n`nYou resist the urge to be violently ill as you ponder what to do.`n`n");
				set_module_pref("skronkyvirgin",1);
			}
			// evalutate the fullness of the Skronky Pot and warn the player if it's dangerous
			$capacity = get_module_pref("capacity");
			$contents = get_module_pref("contents");
			if ($capacity>($contents+10)){
				output("Looks like the Skronky Pot's getting pretty full.`n`n");
			}
			//add navs
			addnav("Make a contribution?");
			addnav("Hack your phlegm into the gloriously ghastly Skronky Pot!","runmodule.php?module=skronkypot&op=ohgodno");
			addnav("My soul's worth more than that.","village.php");
			break;
		case "ohgodno":
			// have they already coughed up everything they've got?
			// yup
			if (get_module_pref("dailyhacks")>get_module_setting("maxdailyhacks")){
				output("You cough, and strain, but nothing comes out.  You're done for today.`n`n");
				addnav("I do feel about two pounds lighter...");
				addnav("O?Back to the Outpost","village.php");
			}
			// nope
			if (get_module_pref("dailyhacks")<=get_module_setting("maxdailyhacks")){
				output("Getting down on your knees in front of the bucket, you grimly grip its edges and cough and hack until a sizeable chunk of greenish awfulness drops in with a sad, wet \"plop\".`n`n");
				// increment the foulness index
				set_module_setting("contents",get_module_setting("contents")+1);
				// did they - oh horror of horrors - KNOCK OVER THE SKRONKY POT?
				if (get_module_setting("contents")>get_module_setting("capacity")){
					// punish the player
					output("`0You realise something's wrong.  Time appears to slow down as you sit, frozen in horror, as the Skronky Pot first wobbles, then tips, then, in awful slow-motion, falls over.`n`nIts contents, fouler than anything you'd ever encountered in even your sweatiest, most urine-soaked nightmares, splash liberally over your crotch and thighs.`n`nAs the smell of the fermented bottom portion of the bucket hits you, you pass out and fall onto your back, and into the Skronky Pot's now-liberated contents.`n`nWhen you come to, you remain unaware of your surroundings for a blissful half-second.  When faced with the full horror of what has happened, you immediately and violently lose control of your stomach, bowels and bladder.  Onlookers will talk for years about the explosive humanoid fountain of grief and mourning that came into their lives that day.`n`nAfter a full five minutes of uninterrupted full-body, every-orifice retching, you lie stinking in the baking sun, not daring to contemplate whether this is truly the lowest point in your life, or whether things could possibly get worse.  Then you hear the sound of laughter, and realise that you're surrounded on all sides by pointing, laughing midgets.`n`nYou knocked over the Skronky Pot, and lost all of your Charm points as a result.  Don't worry, Chuck - things can only get better from here.`n`n");
					$session['user']['charm']=0;
					// reset the contents and capacity
					$newcapacity=e_rand(0,100);
					set_module_setting("capacity",$newcapacity);
					set_module_setting("contents",0);
					if (is_module_active("medals")){
						require_once "modules/medals.php";
						medals_award_medal("tippedskronky","Skronky Disaster","This player unfortunately tipped over the Skronky Pot...","medal_tippedskronky.png");
					}
				}
				// give them their reward
				$session['user']['gold']+=get_module_setting("reward");
				set_module_pref("dailyhacks",get_module_pref("dailyhacks")+1);
				output("Eyes streaming, you sit up, and the midget to your left pats you on your back and hands over your reward.`n`n");
				// add their navs so they can get the hell out of here
				addnav("Is that everything?");
				addnav("Wait, I'm not done yet.","runmodule.php?module=skronkypot&op=ohgodno");
				addnav("Thank God that's over.","village.php");
			}
			break;
	}
	page_footer();
}
?>