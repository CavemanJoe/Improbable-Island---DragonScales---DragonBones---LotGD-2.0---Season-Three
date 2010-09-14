<?php

function pubjokes_getmoduleinfo(){
	$info = array(
		"name"=>"Pub Jokes",
		"version"=>"1.0",
		"author"=>"Dan Hall, AKA CavemanJoe",
		"category"=>"Inn Specials",
		"download"=>""
	);
	return $info;
}

function pubjokes_install(){
	module_addeventhook("inn", "require_once(\"modules/pubjokes.php\"); 100;");
	return true;
}

function pubjokes_uninstall(){
	return true;
}

function pubjokes_runevent($type)
{
	global $session;
	$jokenum = e_rand(1,8);
	$landlord = getsetting("barkeep", "`)Cedrik");
	switch($jokenum){
		case 1:
			output("A tall, hairy man enters the pub and strolls over to the bar.  The frog sulking on the top of his bald head draws some stares.  %s looks him up and down, and after a moment's thought, nods up at the frog and asks \"So, mate - where did you get that?\"`n\"Well, it started off as a wart on my arse,\" says the frog.`n`n",$landlord);
			break;
		case 2:
			output("A small, mottled brown duck walks into the pub.  He waddles over to the bar, hops onto a stool, and fixes %s with an intense stare.  You watch closely, knowing that the two are about to face off in a battle of wits.`n\"Got any bread?\" asks the duck.`n\"No,\" replies %s.`n\"Got any bread?\" asks the duck.`n\"No,\" replies %s.`n\"Got any bread?\"`n\"No.\"`n\"Got any bread?\"`n\"No.\"`n\"Got any bread?\"`n\"No.\"`n\"Got any bread?\"`n\"`iNo.`i\"`n\"Got any bread?\"`n\"NO!\"`n\"Got any bread?\"`n\"No, and the next time you ask, I'm going to nail your damned beak to this bar.\"`nThe duck looks up at %s's fierce expression, and appears to reach a decision.`n\"Got any nails?\"`n\"No.\"`n\"Got any bread?\"`n%s's hands move too fast for you to see, but the loud hammering sound and the squeals of indignant quacking pain serve to paint you a pretty good picture.`n\"I lied about the nails,\" says %s with a smile, and goes back to serving drinks.`n`n",$landlord,$landlord,$landlord,$landlord,$landlord,$landlord);
			break;
		case 3:
			output("A severely drunk man blunders past you, laughing and slurring \"ALL MIDGETS ARE BASTARDS!\"`nA figure lurking in the shadows at the corners of the pub calls after him: \"Hey, I take offense at that!\"`nThe drunkard turns around and slurs back, \"Why, mate, 're you a Midget or summat?\"`n\"No, I'm a bastard.\"`n`n");
			break;
		case 4:
			output("A very attractive woman walks up to the bar. She gestures alluringly to Mick, the assistant bartender, who comes over immediately.  When he arrives, she seductively signals that he should bring his face closer to hers. When he does so, she begins to gently caress his full beard.`n\"Are you %s?\", she asks, softly stroking his face with both hands.`n\"Actually, no,\" he replies.\"Can you get him for me? I need to speak to him,\" she says, running her hands beyond his beard and into his hair.`n\"He's just downstairs changing the barrels,\" breathes Mick. \"Is there anything I can do?\"`n\"Yes, there is. I need you to give him a message,\" she continues, slyly popping a couple of her fingers into his mouth and allowing him to suck them gently.`n\"Wh-what should I tell him?\" Mick somehow manages to stammer, his ears as red as emergency flares.`n\"Tell him,\" she whispers, \"There is no toilet paper or hand soap in the ladies room.\"`n`n",$landlord);
			break;
		case 5:
			output("A wriggling SpiderKitty prances over to the bar, climbs up onto a stool and asks for a beer.  %s begins to pour, chuckling.  \"You know,\" he says, \"I named this pub after you.\"`n\"What,\" says the prancing SpiderKitty, \"You named your pub Dave?\"`n`n", $landlord);
			break;
		case 6:
			output("A horse walks into the pub and up to the bar.  %s says, \"Why the long face?\"`nThe horse shrugs. \"Crap jokes, mainly.\"`n\"Ah.\"`n`n", $landlord);
			break;
		case 7:
			output("A man walks in to the pub.  %s, seeing him, pulls his shotgun from under the bar.  \"OUT!\" he shouts.  \"YOU BLOODY WELL KNOW YOU'VE BEEN BARRED!\"`nAs the man sheepishly leaves, you ask %s what the problem was.  He turns to you, shaking his head.`n\"The sign that said \"Wet Floor\" was supposed to be a `icaution`i, not a request.\"`nYou wondered why your boots were sticking so bad.`n`n", $landlord, $landlord);
			break;
		case 8:
			output("You notice a man quite the worse for wear, staggering out of the pub with his pet giraffe in tow - similarly inebriated, the giraffe falls over onto its side, and the man keeps walking.  %s calls after him - \"Hey!  You can't leave that lyin' there!\"`nThe man turns around and gets as far as \"It's not a lion, it's a...\" before the lion bursts from its disguise and drags him screaming into the jungle.  The regulars shrug and go back to their pints.`n`n",$landlord);
			break;
	}
}



function pubjokes_run(){

}

?>