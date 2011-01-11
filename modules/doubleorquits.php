<?php

function doubleorquits_getmoduleinfo(){
	$info = array(
		"name"=>"Double or Quits",
		"version"=>"2010-10-20",
		"author"=>"Dan Hall",
		"category"=>"Req Gambling",
		"download"=>"",
		"settings"=>array(
			"played"=>"Total money played,int|0",
			"won"=>"Total money won,int|0",
		)
	);
	return $info;
}

function doubleorquits_install(){
	module_addhook("pub_squathole");
	return true;
}

function doubleorquits_uninstall(){
	return true;
}

function doubleorquits_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "pub_squathole":
			addnav("Games");
			addnav("Play Double or Quits","runmodule.php?module=doubleorquits&op=start");
		break;
		}
	return $args;
}

function doubleorquits_run(){
	global $session;
	
	page_header("Twice as much or git tae fook");
	
	$op = httpget("op");
	$sub = httpget("sub");
	switch ($op){
		case "start":
			output("You wander over to a wall-mounted wooden box, covered in carved graffiti.  The window in the front is so grimy you can barely see in, and some wag has daubed in the dust a misspelled plea for somebody to give it a quick wipe.  Fat chance.`n`nChlamydia sees you examining the machine.  She scuttles across the bar towards you.  \"`2It's one o' them double or quits games, but we've called it 'Twice as much or git tae fook,' luv.  Ye put yer money in the slot an' turn the 'andle, an' 'alf the time it gives yer twice as much back.  It's dead easy, petal, you've jus' gotta know when te stop.`0\"  She gestures over her shoulder.  \"`2It'd do nowt but pay out all me profits, 'cept these silly buggers 'ere never know when tae say enough's enough.  Twenny Req a go, darlin'.`0\"`n`nSo it's a very basic double-or-nothing type game.  Well, it'd `ihave`i to be simple, otherise your average Midget wouldn't be able to play it.`n`n");
			if ($session['user']['race']!="Midget"){
				output("You kneel down in front of the machine - it's mounted at bellybutton height - and contemplate whether to play or not.`n`n");
			} else {
				output("You know for damned certain that you're smarter than the average Midget.  You stare up at the machine, wondering which way to turn the handle.`n`n");
			}
			rawoutput("<img src='images/doubleorquits/doubleorquits_lose.jpg'><br />");
			if ($session['user']['gold']>=20){
				addnav("Play");
				addnav("Play (20 Req)","runmodule.php?module=doubleorquits&op=play");
			} else {
				addnav("Play");
				addnav("Ah, but you do not have twenty Requisition tokens, do you?","");
			}
			addnav("Forget it");
			addnav("Turn away","runmodule.php?module=pub_squathole&op=continue");
		break;
		case "play":
			$session['user']['gold'] -= 20;
			increment_module_setting("played",20);
			if (e_rand(0,100) > 50){
				rawoutput("<img src='images/doubleorquits/doubleorquits_40.jpg'><br /><br />");
				output("`bYou won!`b  The machine offers you 40 Req, or a chance to gamble it for a chance to get 80 Req.`n`n");
				addnav("Buttons");
				addnav("Gamble","runmodule.php?module=doubleorquits&op=gamble&prize=80");
				addnav("Accept","runmodule.php?module=doubleorquits&op=accept&prize=40");
			} else {
				rawoutput("<img src='images/doubleorquits/doubleorquits_lose.jpg'><br /><br />");
				output("`bYou lost!`b`n`n");
				if ($session['user']['gold']>=20){
					addnav("Play");
					addnav("Play (20 Req)","runmodule.php?module=doubleorquits&op=play");
				}
				addnav("That's enough");
				addnav("Turn away","runmodule.php?module=pub_squathole&op=continue");
			}
		break;
		case "gamble":
			$prize = httpget('prize');
			if (e_rand(0,1)){
				$newprize = $prize * 2;
				rawoutput("<img src='images/doubleorquits/doubleorquits_".$prize.".jpg'><br /><br />");
				output("`bYou won!`b  The machine offers you %s Req, or a chance to gamble it for a chance to get %s Req.`n`n",$prize, $newprize);
				addnav("Buttons");
				addnav("Gamble","runmodule.php?module=doubleorquits&op=gamble&prize=$newprize");
				addnav("Accept","runmodule.php?module=doubleorquits&op=accept&prize=$prize");
			} else {
				rawoutput("<img src='images/doubleorquits/doubleorquits_lose.jpg'><br /><br />");
				output("You lost the gamble.  Shoulda taken what it offered.`n`n");
				if ($session['user']['gold']>=20){
					addnav("Play");
					addnav("Play (20 Req)","runmodule.php?module=doubleorquits&op=play");
				}
				addnav("That's enough");
				addnav("Turn away","runmodule.php?module=pub_squathole&op=continue");
			}
		break;
		case "accept":
			$prize = httpget('prize');
			increment_module_setting("won",$prize);
			$session['user']['gold']+=$prize;
			output("You turn the 'accept' handle, and %s Requisition tokens are vomited out of the machine.  The payout hopper being stuffed full of cigarette ends and random detritus, the tokens clatter noisly on the floor.`n`n",$prize);
			if ($session['user']['gold']>=20){
				addnav("Play again");
				addnav("Play (20 Req)","runmodule.php?module=doubleorquits&op=play");
			}
			addnav("That's enough");
			addnav("Turn away","runmodule.php?module=pub_squathole&op=continue");
		break;
	}
	debug(get_module_setting("played"));
	debug(get_module_setting("won"));
	page_footer();
}

?>