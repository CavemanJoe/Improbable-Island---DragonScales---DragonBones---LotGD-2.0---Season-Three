<?php

function pub_kittania_getmoduleinfo(){
	$info = array(
		"name"=>"The Sunny Spot",
		"version"=>"2010-09-24",
		"author"=>"Dan Hall / Emily Hall",
		"category"=>"Pubs",
		"download"=>"",
	);
	return $info;
}

function pub_kittania_install(){
	module_addhook("village");
	return true;
}

function pub_kittania_uninstall(){
	return true;
}

function pub_kittania_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "village":
			if ($session['user']['location']=="Kittania"){
				tlschema($args['schemas']['tavernnav']);
				addnav($args['tavernnav']);
				tlschema();
				addnav("The Sunny Spot","runmodule.php?module=pub_kittania&op=start");
			}
		break;
		}
	return $args;
}

function pub_kittania_run(){
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
	page_header("The Sunny Spot");	
	$op = httpget("op");
	switch ($op){
		case "start":
			rawoutput("<table cellpadding=0 cellspacing=0><tr><td><img src='images/pubsigns/kittania.png' align='left'>");
			$phrases = array(
				1 => "flirting cheerfully with one of her patrons as she draws a pint",
				2 => "wearily giving directions to a confused Rookie",
				3 => "gossiping idly about the latest chef in town",
			);
			$chosenphrase = array_rand($phrases);
			output("You climb up a short ladder and emerge into the largest treehouse you've ever seen.  Large branches are visible through the transluscent net drape of the ceiling.  A few kittymorphs chat and consume drinks while lounging in some of the many inviting hammocks, while others share a meal on reclining couches.  Large soft pillows are scattered about the floor around trays full of drink and smokeables.   The landlady Miu-Miu is a tawny, middle-aged kittymorph.  Right now she's %s.`n`nThe soft murmur of conversation is punctuated by laughter, and the smell of strange incense hangs in the air, mingling with the fresh green scent of the tree itself.`n`n",$phrases[$chosenphrase]);
			rawoutput("</td></tr></table>");
			require_once "lib/commentary.php";
			addcommentary();
			viewcommentary("pub_kittania");
			addnav("Talk to people");
			addnav("Miu-Miu","runmodule.php?module=pub_kittania&op=bartender");
			modulehook("pub_kittania");
		break;
		case "continue":
			rawoutput("<table cellpadding=0 cellspacing=0><tr><td><img src='images/pubsigns/kittania.png' align='left'>");
			output("You rejoin your fellows in the open space.`n`n");
			rawoutput("</td></tr></table>");
			require_once "lib/commentary.php";
			addcommentary();
			viewcommentary("pub_kittania");
			addnav("Talk to people");
			addnav("Miu-Miu","runmodule.php?module=pub_kittania&op=bartender");
			modulehook("pub_kittania");
		break;
		case "bartender":
			//add a text parser here at some point, I think...
			//maybe make this opening text time-dependent?
			$text = array();
			$text['hello'] = "Miu-Miu shows you a slightly tired smile.  \"`2Yes love, what'll you have?`0\"`n`n";
			$drinks = array();
			$hook = array();
			$hook['drinks'] = $drinks;
			$hook['text'] = $text;
			$hook = modulehook("pub_kittania_bartender",$hook);
			$text = $hook['text'];
			//debug($hook);
			$drinks = $hook['drinks'];
			output_notl($text['hello']);
			foreach($drinks AS $key => $vals){
				addnav("What'll it be?");
				if (!$vals['blockdrink']){
					if ($session['user']['gold'] >= $vals['price']){
						addnav(array("%s (%s Req)",$vals['verbosename'],$vals['price']),"runmodule.php?module=pub_kittania&op=drink&drink=".$key);
					} else {
						addnav(array("%s (%s Req)",$vals['verbosename'],$vals['price']),"");
					}
				}
			}
			if (!$hook['blockreturnnav']){
				addnav("Never mind");
				addnav("Return to the lounge","runmodule.php?module=pub_kittania&op=continue");
			}
		break;
		case "drink":
			$hook = array(
				"drink" => httpget("drink"),
			);
			$hook = modulehook("pub_kittania_drink",$hook);
			if (!$hook['blockbarnav']){
				addnav("More!");
				addnav("Get Miu-Miu's attention again","runmodule.php?module=pub_kittania&op=bartender");
			}
			if (!$hook['blockreturnnav']){
				addnav("That's enough");
				addnav("Return to the lounge","runmodule.php?module=pub_kittania&op=continue");
			}
		break;
	}
	addnav("Leave");
	addnav("Back to Kittania","village.php");
	page_footer();
}

?>