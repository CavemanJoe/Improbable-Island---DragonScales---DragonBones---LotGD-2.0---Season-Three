<?php

function pub_kittania_drinks_getmoduleinfo(){
	$info = array(
		"name"=>"The Sunny Spot: Drinks",
		"version"=>"2010-09-24",
		"author"=>"Dan Hall / Emily Hall",
		"category"=>"Pubs",
		"download"=>"",
	);
	return $info;
}

function pub_kittania_drinks_install(){
	module_addhook("pub_kittania_bartender");
	module_addhook("pub_kittania_drink");
	return true;
}

function pub_kittania_drinks_uninstall(){
	return true;
}

function pub_kittania_drinks_dohook($hookname,$args){
	global $session;
	
	$drinks = array(
		"cider" => array(
			"price" => 25,
			"verbosename" => "Vegetable Cider",
		),
		"tiggerbalm" => array(
			"price" => 40,
			"verbosename" => "Tigger Balm",
		),
	);
	
	switch($hookname){
		case "pub_kittania_bartender":
			if ($session['user']['race']=="Robot"){
				$args['text']['hello'] = "Miu-Miu blinks.  \"`2Oh, I'm sorry love - we don't have anything for our, um...`0\" she struggles for a moment.  \"`2Our... organically-challenged - I mean our CPU-enhanced...`0\"`n`n\"`#You can say \"Robot,\" you know.  It's not taboo.`0\"`n`n\"`2Right you are, then,`0\" says Miu-Miu, visibly relieved.  \"`2But we `ihave`i got plenty of sun, and you're welcome to sit by the window if you want to charge yourself up, I suppose.`0\"`n`n\"`#Thank you.  I may take you up on that.`0\"`n`n\"`2Is there anything else I can help you with, love?`0\"`n`n";
			} else {
				$args['drinks'] = $drinks;
			}
		break;
		case "pub_kittania_drink":
			$drink = httpget("drink");
			switch ($drink){
				case "cider":
					$session['user']['gold'] -= 25;
					output("\"`2All right love,`0\" says Miu-Miu, setting down the glass in front of you.  She leans forward and murmurs \"`2D'you want a mouse in that, petal?`0\"`n`n");
					if ($session['user']['race']=="KittyMorph"){
						output("You grin, canines peeking out.  Miu-Miu smiles back before reaching under the counter, bringing up a small white mouse by its tail.`n`nThere were days when the custom would be to play with it for a while before dunking it in the cider, but these are more civilised times.  The mouse struggles for precisely one second, then floats serenely with a cross-eyed grin, tiny bubbles rising from its nose.  You knock back the cider and mouse before the bubbles reach the surface, as is the custom of your people these days.  As the mouse slides down your gullet, you wonder idly if political correctness has gone too far.  It's only a bloody `imouse.`i`n`nThere's a tiny jerk inside your stomach.  You grin at Miu-Miu.  \"`#I love it when they hiccup.`0\"`n`n");
					} else {
						output("You give Miu-Miu a long, thoughtful stare.  \"`#`iNo,`i`0\" you say, politely but firmly.`n`n\"`2Fair enough, love, fair enough.  Enjoy.`0\"`n`nThe cider tastes vaguely of apples and grass.`n`n");
					}
					output("You feel `2Energetic!`0`n`n");
					apply_buff('pub_kittania_cider', array(
						"name"=>"`2Warm Cider Fuzzies",
						"rounds"=>15,
						"defmod"=>1.1,
						"roundmsg"=>"Thanks to your Warm Cider Fuzzies, your defensive movements are more fluid!",
						"wearoff"=>"You feel the effects of the Vegetable Cider fade away.",
						"schema"=>"pub_kittania"
					));
					require_once "modules/staminasystem/lib/lib.php";
					addstamina(5000);
				break;
				case "tiggerbalm":
					$session['user']['gold'] -= 40;
					if ($session['user']['race'] != "KittyMorph"){
						output("Miu-Miu raises an eyebrow and gives you an appraising look, before shrugging and pouring a shot of thick green liquid.  \"`2You look big enough to handle it, so where's the harm, I suppose...  but you want to be careful with that stuff,`0\" she says.  \"`2Strictly speaking I'm only supposed to sell it to `ino don't knock it back in one`i - oh, never mind.`0\"`n`nA dribble of burning, herbal-tasting green fluid runs down your chin.  \"`#Gleeshuck,\"`0 you say, as your left eye slowly rotates towards the ceiling.`n`n");
						output("You feel `4Violent!`0`n`n");
						apply_buff('pub_kittania_tiggerbalm', array(
							"name"=>"`2Tigger Balm Shakes",
							"rounds"=>25,
							"atkmod"=>1.4,
							"defmod"=>0.8,
							"roundmsg"=>"`4The Tigger Balm Shakes make you feel more `\$aggressive!",
							"wearoff"=>"`4You feel the Tigger Balm Shakes fade away.  Thank goodness for that.",
							"schema"=>"pub_kittania"
						));
						require_once "modules/staminasystem/lib/lib.php";
						addstamina(6000);
					} else {
						output("You sip gently at the thick, herbal-tasting green fluid.  The fluid is cold, but feels pleasantly warm as it goes down.`n`n");
						output("You feel `2Energetic!`0`n`n");
						require_once "modules/staminasystem/lib/lib.php";
						addstamina(7000);
						apply_buff('pub_kittania_tiggerbalm', array(
							"name"=>"`2Tigger Balm Warmth",
							"rounds"=>25,
							"atkmod"=>1.2,
							"defmod"=>1.1,
							"roundmsg"=>"`2The Tigger Balm burns warmly in your stomach.  Both your attack and defence are increased!",
							"wearoff"=>"`2The Tigger Balm warmth has faded.  Oh well.",
							"schema"=>"pub_kittania"
						));
					}
				break;
			}
		break;
		}
	return $args;
}

function pub_kittania_drinks_run(){
	return true;
}

?>