<?php
// translator ready
// addnews ready
// mail ready

function rspmutant_getmoduleinfo(){
	$info = array(
		"name"=>"The RSP Mutant",
		"version"=>"2008-11-09",
		"author"=>"Markus Wienhoefer, Improbable changes by Dan Hall",
		"category"=>"Forest Specials",
		"download"=>"",
		"prefs"=>array(
			"playerpoints"=>"Player points at start of game,int|0",
			"gnomepoints"=>"Gnome points at start of game,int|0",
		)
	);
	return $info;
}

function rspmutant_install(){
	module_addeventhook("forest", "return 100;");
	return true;
}

function rspmutant_uninstall(){
	return true;
}

function rspmutant_dohook($hookname,$args){
	return $args;
}

function rspmutant_nav($from)
{
	addnav("Choose your weapon");
	addnav("Rock",$from."op=rock");
	addnav("Paper",$from."op=paper");
	addnav("Scissors",$from."op=scissors");
}

function rspmutant_round($from, $pchoice)
{
	global $session;
	$items = array(1=>"rock", 2=>"paper", 3=>"scissors");
	$items = translate_inline($items);

	$playerpoints=get_module_pref("playerpoints");
	$gnomepoints=get_module_pref("gnomepoints");

	output("The Mutant hides all his arms behind his back and starts to count.`n`n");
	output("`&Three...`^Two...`4One...`n");
	$choice = e_rand(1,3);
	output("Reaching one, he pulls the arm mutated into the disgusting `&%s shape from behind his back.", $items[$choice]);

	if ($choice == 1 && $pchoice == 1) { // rock ties rock
		output("Banging your rock against his hideous stone fist, you realise there's no winner in this fight.`n");
		output("\"`@Hmm... ok... let's try that again, shall we?`0\"`n`n");
	} elseif ($choice == 2 && $pchoice == 1) { // paper beats rock
		output("The Mutant reaches his out hideously deformed paper-like hand, and wraps it gently around your rock.  The dry, crackly skin brushes your fingertips, and you try as hard as you can to hold your vomit.`n`n");
		output("\"`@I win,`0\"  whispers the Mutant, staring directly into your eyes and lightly caressing your fingers.");
		output("You shudder and pull your hand away.`n`n");
		$gnomepoints=$gnomepoints+1;
	} elseif ($choice == 3 && $pchoice == 1) { // scissors beaten by rock
		output("With all your strength, you hurl your rock towards the Mutant's freakish, pointed fingers.  The rock impacts with a sickening CRUNCH.`n`n");
		output("\"`@Well, it looks like you won this round,`0\" says the Mutant, rubbing his injured fingers.`n`n");
		$playerpoints=$playerpoints+1;
	} elseif ($choice == 1 && $pchoice == 2) { // rock beaten by paper
		output("You quickly scribble something on the paper, and hold it out to the Mutant as his crushing rock fist comes hurtling towards your face.  He stops dead to read the paper, upon which you've written the word \"`2FREAK!`0\"  Tears well up in his eyes.`n");
		output("\"`@That's really not fair, but I'll concede a defeat,`0\" says the Mutant.`n`n");
		$playerpoints=$playerpoints+1;
	} elseif ($choice == 2 && $pchoice == 2) { // paper ties paper
		output("You slap the Mutant's hideous hand with your paper.  It doesn't seem to do much damage to either party.`n");
		output("\"`@Hmm... ok... let's try that again, shall we?`0\"`n`n");
	} elseif ($choice == 3 && $pchoice == 2) { // scissors beats paper
		output("The Mutant reaches out with his razor-sharp scissor claws, and neatly slices your paper in two.`n`n");
		output("\"`@I think I won that round,`0\" says the Mutant.");
		output("Seeing that your sheet is completely ruined, he gives you another one.`n`n");
		$gnomepoints=$gnomepoints+1;
	} elseif ($choice == 1 && $pchoice == 3) { // rock beats scissors
		output("You hold up your scissors to defend yourself, but the Mutant simply punches you in the stomach.  You double up, your scissors falling from your fingers.`n`n");
		output("\"`@Looks like I won that round,`0\" says the Mutant.");
		output("Sad from the lost point, you go to retrieve your \"weapon\".`n`n");
		$gnomepoints=$gnomepoints+1;
	} elseif ($choice == 2 && $pchoice == 3) { // paper beaten by scissors
		output("The Mutant's freakish papery hand reaches up to smother you, but a quick snip from your scissors cuts you some airholes.`n");
		output("\"`@Don't worry, I can't actually feel much in that hand.  You win that round,`0\" says the Mutant.`n`n");
		$playerpoints=$playerpoints+1;
	} elseif ($choice == 3 && $pchoice == 3) { // scissors ties scissors
		output("You lunge at the Mutant with your scissors, whose blades interlock with his razor-sharp claws.  It takes a few minutes for you to disentangle yourselves.`n");
		output("\"`@Hmm... ok... let's try that again, shall we?`0\"`n`n");
	}

	output("You now have %s %s while the Mutant has %s %s.",
			$playerpoints, translate_inline($playerpoints==1?"point":"points"),
			$gnomepoints, translate_inline($gnomepoints==1?"point":"points"));
	set_module_pref("playerpoints",$playerpoints);
	set_module_pref("gnomepoints",$gnomepoints);

	if ($playerpoints==2||$gnomepoints==2){
		if ($playerpoints==2) {
			rspmutant_wingame();
		}else{
			rspmutant_loosegame();
		}
	} else{
		output("`n`nThe mutant returns his hands behind his back. \"`@Time for the next round, I believe.`0\"");
		rspmutant_nav($from);
	}
}

function rspmutant_runevent($type)
{
	global $session;
	$from = "forest.php?";
	$session['user']['specialinc'] = "module:rspmutant";
	$op = httpget('op');
	if ($op=="" || $op=="search"){
		output("As you head into the jungle, searching for prey, you suddenly see a strange sight in a clearing not far from where you stand.`n`n");
		output("The creature appears to be a very badly deformed Mutant.  He bears three arms, each more hideously freakish than the last.  His first arm ends in a large, rock-hard knuckle, the size of your head.  The second arm splits into two at his elbow, each half covered in razor-sharp teeth.  The third arm appears to be a rudimentary wing of some fashion, made out of tiny, hollow bones and skin so thin you can see through it.`n`n");
		output("Noticing the big leather bag strung to his belt, you think about talking to him, or perhaps trying to mug him.`n`n");
		addnav("Talk to him",$from."op=talk");
		addnav("Better not",$from."op=back");
	} elseif ($op == "back") {
		$session['user']['specialinc'] = "";
		output("You turn away from his abomination, and try to scrub the memory from your mind.`n`n");
	} elseif ($op == "talk") {
		output("You decide to enter the clearing.`n`n");
		output("Seeing you, the Mutant begins to hobble towards you.  \"`@Why hello there!  Would you like to play with me?  I'm so desperately lonely.`0\"`n`n");
		output("You look the poor creature up and down.");
		addnav("Play his game",$from."op=rspgame");
		addnav("Run like hell",$from."op=dontplay");
	} elseif ($op == "rspgame") {
		output("\"`#OK, let's play your game then,`0\" you hear yourself say.");
		output("The Mutant claps his hands together in glee - a sight you really, really don't want to see again - and hobbles to a little tree stump nearby that you hadn't noticed until now.");
		output("When he comes back, he has a little sack strung over his \"stone hand\", from which he hands you a rock, a pair of scissors and a sheet of paper.`n`n");
		output("\"`@It's very easy,\"  he continues.  ");
		output("\"`@Just choose one of the weapons I have given you, and on the count of three we begin to fight.`0\"`n`n");
		output("\"`#You've got to be kidding me, right?`0\"  You look at the meager offerings on display.");
		rspmutant_nav($from);
		//paranoia reset of points if coming from timeout
		set_module_pref("playerpoints",0);
		set_module_pref("gnomepoints",0);
	} elseif ($op == "rock") {
		output("Planning on just knocking this strange guy out in one sure stroke, you choose the rock.`n`n");
		rspmutant_round($from, 1);
	} elseif ($op == "paper") {
		output("You sigh, and choose the piece of paper.  Sure, why the hell not?`n`n");
		rspmutant_round($from, 2);
	} elseif ($op == "scissors") {
		output("Cutting is never a bad idea, you think, and prepare to show the pair of scissors.`n`n");
		rspmutant_round($from, 3);
	} elseif ($op == "dontplay") {
		$session['user']['specialinc'] = "";
		output("You tear off into the Jungle, shouting \"`#FREAK!`0\" behind you for good measure.`n`n");
	}
	output_notl("`0");
}

function rspmutant_wingame(){
	global $session;
	output("`n`n\"`@Seems like we have a winner!`0\" says the Mutant.  ");
	output("\"`@Now, what can I give you as a prize...`0\"`n`n");
	$goldwon=e_rand(1,3)*$session['user']['level']*10;
	output("He reaches into the bag on his belt and hands you %s Requisition tokens.`n`n", $goldwon);
	debuglog("won $goldwon from the RSP gnome");
	output("\"`@Next time we meet, I won't be this easy to beat.`0\"");
	output("With these words he heads into the Jungle, dragging his hideous arms behind him.");
	$session['user']['gold']+=$goldwon;
	set_module_pref("playerpoints",0);
	set_module_pref("gnomepoints",0);
}

function rspmutant_loosegame(){
	global $session;
	output("`n`n\"`@See, I told you I would be a tough contender.`0\"`n`n");
	output("Something `iundescribable`i whips out of his pants with lightning speed, and latches onto your forehead.");
	output("A second later you are unable to move.`n`n");
	$goldlost=e_rand(1,3)*$session['user']['level']*10;
	if ($session['user']['gold']==0) {
		$goldlost = 0;
		output("The Mutant looks into your pockets, seemingly searching for something.");
		output("\"`@You should have told me you have nothing to give...`0\" he adds after a while.`n`n");
	}elseif ($goldlost>$session['user']['gold']){
		$goldlost = $session['user']['gold'];
	}
	if ($goldlost) {
		output("Seeing that his poison worked, he reaches into your pockets and gently withdraws `&%s Requisition.",$goldlost);
		output("\"`@Fair is fair.`0\"`n`n");
		$session['user']['gold'] -= $goldlost;
		debuglog("lost $goldlost Req to the RSP Mutant");
	}
	output("Whistling a merry tune he saunters off and leaves you staring blankly, as you realize you just lost to the infamous RSP Mutant.");
	set_module_pref("playerpoints",0);
	set_module_pref("gnomepoints",0);
}

function rspmutant_run(){
}

?>
