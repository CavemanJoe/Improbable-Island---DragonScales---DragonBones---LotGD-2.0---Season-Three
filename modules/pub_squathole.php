<?php

function pub_squathole_getmoduleinfo(){
	$info = array(
		"name"=>"Booz",
		"version"=>"2010-10-25",
		"author"=>"Dan Hall / Emily Hall",
		"category"=>"Pubs",
		"download"=>"",
	);
	return $info;
}

function pub_squathole_install(){
	module_addhook("village");
	return true;
}

function pub_squathole_uninstall(){
	return true;
}

function pub_squathole_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "village":
			if ($session['user']['location']=="Squat Hole"){
				tlschema($args['schemas']['tavernnav']);
				addnav($args['tavernnav']);
				tlschema();
				addnav("Booz","runmodule.php?module=pub_squathole&op=start");
			}
		break;
		}
	return $args;
}

function pub_squathole_run(){
	global $session;
	$td = gametimedetails();
	$gt = $td['secssofartoday'];
	//8400 seconds in a day
	//3600 seconds in an hour
	//midnight: 0
	//1am: 3600
	//2am: 7200
	//3am: 10800
	//4am: 14400
	//5am: 18000
	//6am: 21600
	//7am: 25200
	//8am: 28800
	//9am: 32400
	//10am: 36000
	//11am: 39600
	//12pm midday: 43200
	//1pm: 46800
	//2pm: 50400
	//3pm: 54000
	//4pm: 57600
	//5pm: 61200
	//6pm: 64800
	//7pm: 68400
	//8pm: 72000
	//9pm: 75600
	//10pm: 79200
	//11pm: 82800
	//11:59:59: 86399
	//midnight: 0
	page_header("Booz");
	$op = httpget("op");
	switch ($op){
		case "start":
			rawoutput("<table cellpadding=0 cellspacing=0><tr><td><img src='images/pubsigns/squathole_1.png' align='left'>");
			$phrases = array(
				1 => "A number of Midgets stand around drinking pints of Wanker, and holding what sounds like a dirty limerick contest.  None of them are quite clever enough to know what a limerick is, but they're giving it their best shot.",
				2 => "A small knot of Midgets are brawling on the top of a table, spilling beer and foul secretions everywhere. It looks like they've been at it for awhile, and the pub is starting to show the damage.  The ratty blue tarp above the grouping is letting in the elements, which doesn't deter any of the fighting throng.  Bottles, mugs and fists are brandished, and one particularly angry Midget has torn off bits of a table and looks disappointed that there's no chandelier for him to swing from.",
				3 => "You think the sign once read 'Mourners of Life Poetry Club,' but that surely can't be right.",
			);
			$chosenphrase = array_rand($phrases);
			output("`0Booz, the Squat Hole pub, is basically a series of drooping tarpaulins, unsteadily propped up with branches and a few stakes.  Its mostly knee-high patrons sit on empty beer kegs, resting their drinks or their feet on other kegs.  The stench of armpit and stale beer hangs in the air.  Someone has propped a few rotting boards on top of more kegs - this is the bar.  A tiny woman scampers back and forth across it, doling out alcohol to patrons who are temporarily still upright.  Her name is Chlamydia.  You know, like the flower.`n`n%s`n`n",$phrases[$chosenphrase]);
			rawoutput("</td></tr></table>");
			require_once "lib/commentary.php";
			addcommentary();
			viewcommentary("pub_squathole");
			addnav("Talk to people");
			addnav("Chlamydia","runmodule.php?module=pub_squathole&op=bartender");
			modulehook("pub_squathole");
		break;
		case "continue":
			rawoutput("<table cellpadding=0 cellspacing=0><tr><td><img src='images/pubsigns/squathole_1.png' align='left'>");
			output("You head back into the main section of the pub, ducking under flappy bits of tarpaulin.`n`n");
			rawoutput("</td></tr></table>");
			require_once "lib/commentary.php";
			addcommentary();
			viewcommentary("pub_squathole");
			addnav("Talk to people");
			addnav("Chlamydia","runmodule.php?module=pub_squathole&op=bartender");
			modulehook("pub_squathole");
		break;
		case "bartender":
			//add a text parser here at some point, I think...
			//maybe make this opening text time-dependent?
			$text = array();
			$text['hello'] = "Chlamydia shows you a smile and squeaks at you.  \"`2Yes luv, wha' c'n I gitcha?`0\"`n`n";
			$drinks = array();
			$hook = array();
			$hook['drinks'] = $drinks;
			$hook['text'] = $text;
			$hook = modulehook("pub_squathole_bartender",$hook);
			$text = $hook['text'];
			//debug($hook);
			$drinks = $hook['drinks'];
			output_notl($text['hello']);
			foreach($drinks AS $key => $vals){
				addnav("Wha' indeed?");
				if (!$vals['blockdrink']){
					if ($session['user']['gold'] >= $vals['price']){
						addnav(array("%s (%s Req)",$vals['verbosename'],$vals['price']),"runmodule.php?module=pub_squathole&op=drink&drink=".$key);
					} else {
						addnav(array("%s (%s Req)",$vals['verbosename'],$vals['price']),"");
					}
				}
			}
			if (!$hook['blockreturnnav']){
				addnav("Never mind");
				addnav("Return to the lounge","runmodule.php?module=pub_squathole&op=continue");
			}
		break;
		case "drink":
			$hook = array(
				"drink" => httpget("drink"),
			);
			$hook = modulehook("pub_squathole_drink",$hook);
			if (!$hook['blockbarnav']){
				addnav("More!");
				addnav("Get Chlamydia's attention again","runmodule.php?module=pub_squathole&op=bartender");
			}
			if (!$hook['blockreturnnav']){
				addnav("That's enough");
				addnav("Return to the lounge","runmodule.php?module=pub_squathole&op=continue");
			}
		break;
	}
	addnav("Leave");
	addnav("Back to Squat Hole","village.php");
	page_footer();
}

?>