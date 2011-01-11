<?php

function emilybundle_getmoduleinfo(){
	$info = array(
		"name"=>"Emily Bundle",
		"version"=>"2010-06-30",
		"author"=>"Dan Hall",
		"category"=>"Lodge",
		"download"=>"",
		"settings"=>array(
			"cost"=>"How much does the bundle cost?,int|2500",
			"stock"=>"How many bundles are left?,int|200",
		),
		"prefs"=>array(
			"bought"=>"Player has bought the bundle,bool|0",
		),
	);
	return $info;
}
function emilybundle_install(){
	module_addhook("lodge");
	return true;
}
function emilybundle_uninstall(){
	return true;
}
function emilybundle_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "lodge":
			$cost = get_module_setting("cost");
			$stock = get_module_setting("stock");
			if ($stock>0 && !get_module_pref("bought")){
				addnav("Special Bundle Deal");
				addnav("Tell me about the Emily Bundle","runmodule.php?module=emilybundle&op=start");
				addnav(array("%s Emily Bundles left!",$stock),"");
			}
		break;
		}
	return $args;
}

function emilybundle_run(){
	global $session;
	$op = httpget("op");

	page_header("The Emily Bundle");
	
	$cost = get_module_setting("cost");
	$stock = get_module_setting("stock");

	$playerpoints = $session['user']['donation']-$session['user']['donationspent'];
	
	switch($op){
		case "start":
			output("So, Improbable Island has been going for a couple of years now and it's turning into something that can keep me alive and with a roof over my head without me having to do website design.  It's turned from a hobby into a full-time job.`n`nSince then, I've gotten married, and my wife, Emily(*), has gotten increasingly frustrated with her job.  She does database work for a hospital; the pay's good, but the hours and the work itself is pretty depressing.`n`nI've noticed lately that my original goal - \"Hey, I'll write a text adventure game!\" - hasn't quite worked out the way I wanted it to, because I thought it'd be about writing.  I figured I'd have a plot, decent characters, a beginning, middle and end - you know, a story.  But lately I've been doing a lot more building of games and systems than I have building of worlds, and I'm beginning to realise that that's what I'm good at.`n`nThe Island needs a writer.`n`nA dedicated writer, so that I can be a dedicated coder.  Recently I've done all sorts of stuff with Titans, Onslaught, new Commentary, Dwellings, and all that jazz - but the last actual piece of world-building writing that I did was in Common Ground, with its time-sensitive description text.  I believe that was before Christmas.`n`nOh, and there were some lions, too.`n`nTo give you an idea of where I am right now, let's say this: the system is in place to extend that level of time-and-context-sensitive detail to every Outpost.  I just don't have any writing to go in the system.`n`nIt's not that I don't like writing, and forgive me for parping my own horn but I don't think it's that I'm not good at it (although that sentence may have been evidence to the contrary).  It's just that I have so much else to do.`n`nI need help.`n`nHey, you know... my wife is an excellent writer.`n`nHonestly I'd be doing this before too long even if I didn't need Emily to help me with the Island.  She hates her job, I love mine, and that's unfair; it's imperative that the Island support both of us if we ever want to have our own house, or raise a family.`n`nWe've done our maths and figured out how much we need per month on which to survive without Emily's workplace sorting out our health insurance (that's the biggest expense, right there), and from that, based on historical data, we've figured out how much of an advertisement boost we'd need to get the Island up to that sort of level, and from that, how much money we'd need to get together by 'x' if we want Emily to be able to quit her job by 'y', with 'y' being \"Before September.\"`n`nIn a nutshell we need to get some bread together so that we can expand our player base enough that casual everyday donations cover us both without me having to tell you all about it every time my laptop breaks, my car breaks down, or my lion gets into an argument he just can't win.`n`nSo, you can give me money so that the Island can have more juicy plot points and a better sense of place, or you can give me money so that Emily and I can be happier and spend more time together.  Both are excellent reasons.  I've got another reason for you:`n`nIntroducing the `5Emily Bundle`0!`n`nThe `5Emily Bundle`0 costs 2,500 Donator Points, and it's worth well over three times as much.  In the Emily Bundle for 2,500 Donator Points (that's twenty-five bucks, for those of you keeping track), you get:`n`n* Permanent, unlimited free Avatar picture changes (never pay to change your Avatar picture again!)`n* Permanent, unlimited free Custom Weapon changes (custom weaponry now sticks around properly!)`n* Permanent, unlimited free Custom Armour changes (likewise!)`n* Permanent, unlimited free Custom Name Colour changes`n* Permanent, inlimited free Title changes`n* Permanent, unlimited free Mount Name changes`n* Permanent, unlimited free Commentary Race Name changes`n* Ten cigarettes`n* Ten thousand Requisition tokens`n* Two extra Chronospheres`n* All your current Chronospheres refilled`n* Ten Special Comments`n* A special, strictly-limited-and-not-to-be-repeated Medal`n`nAnd if you've not already donated, you'll also get room for a ten-thousand character Extended Bio (players who've already donated already have this).`n`nNow, the Emily Bundle is limited to two hundred players.  That should give Emily and I enough of a financial boost to get this plan underway.  If something stupidly awesome happens like the whole stock running out in a day, then I might add another fifty or a hundred or so, but no more than that (I'm not going to say it's strictly limited just to stimulate demand and then make it as common as muck - I want this to be a bit special).`n`nSo, help us recruit a writer and make the Island more awesome, more quickly - grab the Emily Bundle while it's still around to grab!`n(*) Yes, I named the pickle-wench barmaid after my wife.  Looking back, I could have made a better choice for an homage.");
			addnav("Buy the Emily Bundle!");
			if ($playerpoints >= $cost){
				addnav("Yes! (2,500 points)","runmodule.php?module=emilybundle&op=buy");
			} else {
				addnav(array("Just %s more points!",$cost-$playerpoints),"");
			}
		break;
		case "buy":
			set_module_pref("permanent",true,"commentaryicons_customrace");
			set_module_pref("permanent",true,"titlechange");
			set_module_pref("permanent",true,"namedmount");
			set_module_pref("permanent",true,"namecolor");
			set_module_pref("permanent",true,"avatar");
			set_module_pref("permanentarmor",true,"customeq");
			set_module_pref("permanentweapon",true,"customeq");
			$session['user']['gems']+=10;
			$session['user']['goldinbank']+=10000;
			$curspheres = get_module_pref("slots","daysave");
			set_module_pref("slots",$curspheres+2,"daysave");
			set_module_pref("days",$curspheres+2,"daysave");
			increment_module_pref("commentsleft",10,"specialcomments");
			
			require_once "modules/medals.php";
			medals_award_medal("emilybundle","Emily Bundle Supporter","This player bought the Emily Bundle, supporting Improbable Island and its admin!","medal_emilybundle.png");
			
			$session['user']['donationspent']+=$cost;
			increment_module_setting("stock",-1);
			set_module_pref("bought",1);
			
			output("You've got the Emily Bundle!  Thank you so much for supporting the Island.  Have fun!`n`n");
			
			//todo: medal, take points, diminish stock, output thankyou
		break;
	}
	addnav("Return");
	addnav("L?Return to the Lodge","lodge.php");
	page_footer();


}
?>