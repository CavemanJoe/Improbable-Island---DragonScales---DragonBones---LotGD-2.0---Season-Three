<?php

function pub_squathole_drinks_getmoduleinfo(){
	$info = array(
		"name"=>"Booz: Drinks",
		"version"=>"2010-10-25",
		"author"=>"Dan Hall / Emily Hall",
		"category"=>"Pubs",
		"download"=>"",
	);
	return $info;
}

function pub_squathole_drinks_install(){
	module_addhook("pub_squathole_bartender");
	module_addhook("pub_squathole_drink");
	return true;
}

function pub_squathole_drinks_uninstall(){
	return true;
}

function pub_squathole_drinks_dohook($hookname,$args){
	global $session;
	
	$drinks = array(
		"mudwisearse" => array(
			"price" => 5,
			"verbosename" => "Mudwisearse",
		),
		"wanker" => array(
			"price" => 25,
			"verbosename" => "Wanker",
		),
		// "stinkweed" => array(
			// "price" => 40,
			// "verbosename" => "Stinkweed",
		// ),
	);
	
	switch($hookname){
		case "pub_squathole_bartender":
			if ($session['user']['race']=="Robot"){
				$args['text']['hello'] = "You try to smile.  \"`#It's nice to see a pub that caters to my type, outside of Cyber City that is.`0\"`n`n\"`2Only jokin',`0\" squeaks Chlamydia, in a happy little sing-song voice.  \"`2We don't 'ave anythin' fer Robots.  You c'n stick your dick in the light socket if yer like?  Call it twenny Req?`0\"`n`n";
				if ($session['user']['sex']){
					$args['text']['hello'].="\"`#I'm a female Robot.  I do not have a penis to put into your light socket.`0\"`n`n\"`2Sorry love, it's hard to tell wi' you lot.  I've got some jump leads, we could jus' clip 'em to yer tits and -`0\"`n`n\"`#No, thank you.`0\"`n`n";
				} else {
					$args['text']['hello'].="\"`#Thank you, but I am quite sensitively tuned.  The results of what you propose would be disasterous, both to your generator and to me.`0\"`n`n\"`2Ten Req?`0\"`n`n\"`#No, thank you.`0\"`n`n";
				}
			} else {
				$args['drinks'] = $drinks;
			}
		break;
		case "pub_squathole_drink":
			$drink = httpget("drink");
			switch ($drink){
				case "mudwisearse":
					$session['user']['gold'] -= 5;
					output("Chlamydia hands you a jam jar that looks to be full of cloudy swampwater.  It tastes like someone's washed a few glasses of Wanker, and bottled the dishwater.  You hold your nose and take another sip, hoping for a buzz, but it looks like the best you can say about this drink is that it's liquid.`n`n");
					output("You feel... `2honestly very little.`0`n`n");
					apply_buff('pub_squathole_mudwisearse', array(
						"name"=>"`2Crappiest Beer Ever",
						"rounds"=>8,
						"atkmod"=>1.05,
						"roundmsg"=>"The Mudwisearse you imbibed in Squat Hole has made you feel slightly more aggressive.  `iVery`i slightly more aggressive.",
						"wearoff"=>"You feel the effects of the crap beer fade away.",
						"schema"=>"pub_squathole"
					));
				break;
				case "wanker":
					$session['user']['gold'] -= 25;
					output("\"`2'ere you go, Chuck, one lovely pint o' Wanker.`0\"  Chlamydia slops a stingy pint of beer into a glass that looks as if it was last washed in swamp water.  You take a sniff and choke down the taste and smell of moldy, festering laundry.  The alcohol should help sterilize the worst of it, you hope.`n`nAfter a few more swallows, you start to realise why it's called Wanker.`n`n");
					output("You feel `\$`b`iFUCKING ENRAGED.`i`b`0`n`n");
					apply_buff('pub_squathole_wanker', array(
						"name"=>"`4Wanker",
						"rounds"=>25,
						"atkmod"=>1.3,
						"roundmsg"=>"`0The pint of `4Wanker`0 you imbibed in Squat Hole has filled you with muderous rage!",
						"wearoff"=>"You feel the effects of the `4Wanker`0 slowly fade away.",
						"schema"=>"pub_squathole"
					));
				break;
			}
		break;
		}
	return $args;
}

function pub_squathole_drinks_run(){
	return true;
}

?>