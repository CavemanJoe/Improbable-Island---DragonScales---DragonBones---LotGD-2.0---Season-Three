<?php

function pub_newpittsburgh_getmoduleinfo(){
	$info = array(
		"name"=>"Vigour Mortis",
		"version"=>"2010-09-24",
		"author"=>"Dan Hall / Emily Hall",
		"category"=>"Pubs",
		"download"=>"",
	);
	return $info;
}

function pub_newpittsburgh_install(){
	module_addhook("village");
	return true;
}

function pub_newpittsburgh_uninstall(){
	return true;
}

function pub_newpittsburgh_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "village":
			if ($session['user']['location']=="New Pittsburgh"){
				tlschema($args['schemas']['tavernnav']);
				addnav($args['tavernnav']);
				tlschema();
				addnav("Vigour Mortis","runmodule.php?module=pub_newpittsburgh&op=start");
			}
		break;
		}
	return $args;
}

function pub_newpittsburgh_run(){
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
	page_header("Vigour Mortis");	
	$op = httpget("op");
	switch ($op){
		case "start":
			rawoutput("<table cellpadding=0 cellspacing=0><tr><td><img src='images/pubsigns/newpittsburgh.png' align='left'>");
			$phrases = array(
				1 => "From the corner, the pub quiz appears to have recently finished, and the quizmaster is reading out the answers - Question 1: Brains. Question 2: BRAINS!, Question 3: BRAAAAINS.",
				2 => "George stares at the television, static reflected in his cold, dead eyes.",
				3 => "George turns to the bar and takes stock of his inventory of pickled brainstuffs.",
			);
			$chosenphrase = array_rand($phrases);
			output("You walk through a broken door and pick your way over shattered glass to a battered sideboard.  Around you, a number of congenial zombies shuffle in various directions, groaning and mumbling to one another.  The air is thick with the smell of rotting flesh and dust, and congealing blood stains many a tabletop.  A number of zombies cluster around a ridiculously small CRT television, which is displaying static.  The bartender, George, seems to be discussing the relative merits of brains with a local.`n`n");
			output_notl("%s`n`n",$phrases[$chosenphrase]);
			rawoutput("</td></tr></table>");
			require_once "lib/commentary.php";
			addcommentary();
			viewcommentary("pub_newpittsburgh");
			addnav("Talk to people");
			addnav("George","runmodule.php?module=pub_newpittsburgh&op=bartender");
			modulehook("pub_newpittsburgh");
		break;
		case "continue":
			rawoutput("<table cellpadding=0 cellspacing=0><tr><td><img src='images/pubsigns/newpittsburgh.png' align='left'>");
			output("You rejoin your fellows at the cluttered tables.`n`n");
			rawoutput("</td></tr></table>");
			require_once "lib/commentary.php";
			addcommentary();
			viewcommentary("pub_newpittsburgh");
			addnav("Talk to people");
			addnav("George","runmodule.php?module=pub_newpittsburgh&op=bartender");
			modulehook("pub_newpittsburgh");
		break;
		case "bartender":
			//add a text parser here at some point, I think...
			//maybe make this opening text time-dependent?
			$text = array();
			$text['hello'] = "George shambles over with a grimace.  \"`2Yes, mate.  What'll it be?`0\"`n`n";
			$drinks = array();
			$hook = array();
			$hook['drinks'] = $drinks;
			$hook['text'] = $text;
			$hook = modulehook("pub_newpittsburgh_bartender",$hook);
			$text = $hook['text'];
			//debug($hook);
			$drinks = $hook['drinks'];
			output_notl($text['hello']);
			foreach($drinks AS $key => $vals){
				addnav("What'll it be?");
				if (!$vals['blockdrink']){
					if ($session['user']['gold'] >= $vals['price']){
						addnav(array("%s (%s Req)",$vals['verbosename'],$vals['price']),"runmodule.php?module=pub_newpittsburgh&op=drink&drink=".$key);
					} else {
						addnav(array("%s (%s Req)",$vals['verbosename'],$vals['price']),"");
					}
				}
			}
			if (!$hook['blockreturnnav']){
				addnav("Never mind");
				addnav("Return to the lounge","runmodule.php?module=pub_newpittsburgh&op=continue");
			}
		break;
		case "drink":
			$hook = array(
				"drink" => httpget("drink"),
			);
			$hook = modulehook("pub_newpittsburgh_drink",$hook);
			if (!$hook['blockbarnav']){
				addnav("More!");
				addnav("Get George's attention again","runmodule.php?module=pub_newpittsburgh&op=bartender");
			}
			if (!$hook['blockreturnnav']){
				addnav("That's enough");
				addnav("Return to the lounge","runmodule.php?module=pub_newpittsburgh&op=continue");
			}
		break;
	}
	addnav("Leave");
	addnav("Back to New Pittsburgh","village.php");
	page_footer();
}

?>
