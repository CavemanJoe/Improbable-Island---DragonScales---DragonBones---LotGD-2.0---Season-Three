<?php

function pub_newpittsburgh_drinks_getmoduleinfo(){
	$info = array(
		"name"=>"Vigour Mortis: Drinks",
		"version"=>"2010-09-24",
		"author"=>"Dan Hall / Emily Hall",
		"category"=>"Pubs",
		"download"=>"",
	);
	return $info;
}

function pub_newpittsburgh_drinks_install(){
	module_addhook("pub_newpittsburgh_bartender");
	module_addhook("pub_newpittsburgh_drink");
	return true;
}

function pub_newpittsburgh_drinks_uninstall(){
	return true;
}

function pub_newpittsburgh_drinks_dohook($hookname,$args){
	global $session;
	
	$drinks = array(
		"brainhem" => array(
			"price" => 35,
			"verbosename" => "Brain Hemorrhage",
		),
		"monstermash" => array(
			"price" => 50,
			"verbosename" => "Monster Mash",
		),
		"specialsauce" => array(
			"price" => 65,
			"verbosename" => "Special Sauce",
		),
	);
	
	switch($hookname){
		case "pub_newpittsburgh_bartender":
			if ($session['user']['race']=="Robot"){
				if ($session['user']['sex']){
					$lovelad = "lad";
				} else {
					$lovelad = "love";
				}
				$args['text']['hello'] = "George looks you up and down, eyes sliding over your smooth glass skin.  \"`2You're a Robot, aren't you, ".$lovelad."?`0\"`n`n\"`#That I am.  Are you going to tell me that you don't serve my type in here?`0\"`n`n\"`2On the contrary, Chuck, we'd be `ivery`i pleased to cater to our Robot customers, but we just don't have the power, y'see.  Setting up one of those fancy lamps just takes more amps than our genny can kick out.  You ask most of the pubs on the Island, it's the same way.`0\"`n`n\"`#I see.`0\"`n`n\"`2We've got games, though.  And telly.  You're more than welcome here, it's just...`0\" he shrugs his shoulders and spreads his hands.  \"`2I don't have anything to sell you that you'd want to buy.`0\"`n`n`#Fair enough`0, you suppose.";
			} else {
				$args['drinks'] = $drinks;
			}
		break;
		case "pub_newpittsburgh_drink":
			$drink = httpget("drink");
			switch ($drink){
				case "brainhem":
					$session['user']['gold'] -= 35;
					output("\"`2All right Chuck,`0\" says George, bringing up a jar of pickled brains.  They look far too small to be human, even pickled - perhaps the size of rabbit brains.  The wrinkled grey things bob peacefully around as George carefully pours a measure of the fluid into a shot glass with the help of a funnel.`n`nThe liquid smells a lot like embalming fluid.`n`nTastes like it, too, albeit with a nice alcoholic glow.`n`n");
					output("You feel `2Energetic!`0`n`n");
					apply_buff('pub_newpittsburgh_brainhem', array(
						"name"=>"`2Brain Hemorrhage Fuzzies",
						"rounds"=>15,
						"defmod"=>1.12,
						"roundmsg"=>"Thanks to your Brain Hemorrhage Fuzzies, your defensive movements are more fluid!",
						"wearoff"=>"You feel the effects of the Brain Hemorrhage fade away.",
						"schema"=>"pub_newpittsburgh"
					));
					require_once "modules/staminasystem/lib/lib.php";
					addstamina(5000);
				break;
				case "monstermash":
					$session['user']['gold'] -= 50;
					output("George hands you a tall glass filled with artfully-swirled monster grey matter, garnished with a jolly little paper umbrella and a slice of brainstem.`n`n");
					if ($session['user']['race'] != "Zombie"){
						output("You take a sip.  It's... honestly, it's awful.  It tastes like a mix of chicken and beef fat, raw eggs, and blood.  Lots of blood.  Not meaning to be impolite you knock the glass back quickly, trying to ignore the cold, semi-solid lumps slowly sliding over your tongue and curling down your throat.`n`n");
						output("You feel `4Sick and Grumpy!`0`n`n");
						apply_buff('pub_newpittsburgh_monstermash', array(
							"name"=>"`2Monster Mash Grumps",
							"rounds"=>25,
							"atkmod"=>1.2,
							"roundmsg"=>"`4The awful Zombie drink makes you feel `\$aggressive!",
							"wearoff"=>"`4You feel the effects of the Monster Mash fade away.  Thank goodness for that.",
							"schema"=>"pub_newpittsburgh"
						));
						require_once "modules/staminasystem/lib/lib.php";
						addstamina(5000);
					} else {
						output("You down the contents and lick your lips.`n`n");
						output("You feel `2Energetic!`0`n`n");
						require_once "modules/staminasystem/lib/lib.php";
						addstamina(10000);
						increment_module_pref("nutrition",5,"staminafood");
						apply_buff('pub_newpittsburgh_monstermash', array(
							"name"=>"`2Monster Mash",
							"rounds"=>25,
							"atkmod"=>1.2,
							"defmod"=>1.1,
							"roundmsg"=>"`2The Monster Mash burns warmly in your stomach.  Both your attack and defence are increased!",
							"wearoff"=>"`2The Monster Mash warmth has faded.  Oh well.",
							"schema"=>"pub_newpittsburgh"
						));
					}
				break;
				case "specialsauce":
					$session['user']['gold'] -= 65;
					output("George pours you a short glass full of something from a thick blue bottle.  It's the only thing on the menu that doesn't mention brains, though you wonder if the odd blue liquid wasn't distilled from them.  It smells vaguely of formaldehyde, but has enough of a tongue-numbing alcoholic kick that you don't regret ordering it.`n`n");
					output("You feel `2Warm!`0`n`n");
					apply_buff('pub_newpittsburgh_specialsauce', array(
						"name"=>"`2Special Sauce Warmth",
						"rounds"=>30,
						"defmod"=>1.25,
						"atkmod"=>1.25,
						"roundmsg"=>"That Zombie drink was awesome!  It's like time's slowed down!",
						"wearoff"=>"You feel the effects of the Special Sauce fade away.",
						"schema"=>"pub_newpittsburgh"
					));
				break;
			}
		break;
		}
	return $args;
}

function pub_newpittsburgh_drinks_run(){
	return true;
}

?>