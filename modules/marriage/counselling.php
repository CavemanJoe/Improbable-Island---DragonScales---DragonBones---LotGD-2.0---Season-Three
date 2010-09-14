<?php
$operation = httpget('op2');
if ($operation=="") $operation="general";
page_header("Marriage Counselling");
output("`c`b`@Counselling`c`b`n");
addnav("Actions");
switch($operation) {
	case "general":
		if (get_module_setting("location")==0) output("`@Walking to the edge of town,");
		else output("`@Walking to a small building in the Gardens,");
		output("you look around for `^Professor van Lipvig's`@ office.");
		output("`n`nNot finding it, you walk back, and see a large wooden building that you had never noticed before.");
		output("`n`nWalking up to it a voice yells out '`^Come in,`@' from a nearby window.");
		if (get_module_setting("location")==0) villagenav();
		else addnav("Return to the Gardens","gardens.php");
		addnav("Enter the Office","runmodule.php?module=marriage&op=counselling&op2=enter");
		break;
	case "enter":
		output("`@Pushing the carved door open ever so quitely, you enter into a spacious, yet opulent chamber.");
		output("Looking around, you see chairs, plush rugs, and hunting trophies.");
		output("Walking to the end of the warm room, you see many herbal candles and a burning fire.");
		output("Behind you, a melodious voice says '`^Van Lipvig's office, how may I help you.`@'");
		output("`n`nAlmost jumping out of your skin, you turn around to see a small woman sat at her desk.");
		output("Nodding imperceptibly, she speaks, '`^Ah, %s`0`^. Van Lipvig is with someone else at the moment. Please sit down.`@'",$session['user']['name']);
		if (get_module_setting("location")==0) villagenav();
		else addnav("Return to the Gardens","gardens.php");
		addnav("To the Waiting Area","runmodule.php?module=marriage&op=counselling&op2=wait");
		break;
	case "wait":
		addnews("%s`0`@ had to go to a Social Counsellor due to a rejected Marriage Proposal!",$session['user']['name']);
		$allprefs=unserialize(get_module_pref('allprefs'));
		$allprefs['counsel']=0;
		set_module_pref('allprefs',serialize($allprefs));
		output("`@Sitting on a stuffed bear, you study the room and its paintings, many of which you feel you have seen before.");
		output("One is of a Green Dragon fighting a constant battle, and yet another contains a scene of a street hawker....");
		output("`n`nClosing one eye to study the painting, you start to feel drowsy.. then someone wakes you up. '`^%s`0`^, the Doctor will see you now.`@",$session['user']['name']);
		addnav("See Van Lipvig","runmodule.php?module=marriage&op=counselling&op2=checkin");
		break;
	case "checkin":
		output("`@Standing up, you walk past the same painting of a Dragon that you saw earlier and through a brass door.");
		output("Emerging through it, you see yet another spacious chamber, and walk through it.");
		output("If the other room was Plush, this could only be described as minimalist.");
		output("`n`nAs you exit this chamber, you walk into an office where a short stumpy man sits at a large desk. He has a moustache that stretches out on either side of him. You survey the room to see a couch and a clockstand at the other end of the chamber.");
		output("`n`n'`&Ah, mah fviend. Ah am here to prevent another Socielle Incident.. but fahgive me, I ahm Professor Van Lipvig, socielle extraordinaire.`@'");
		output("`n`n`@As he says this, you are reminded of a large toad, and have to stop yourself from laughing.");
		output("`n`n`@'`&Vell, mah tachitarn fah-wend, puh-lease seet on mah couch.`@'");
		addnav("Sit on the Couch","runmodule.php?module=marriage&op=counselling&op2=couch");
		break;
	case "couch":
		$array=array("a green dragon","a set of frolicking sheep","a large piece of cake","a pixie","a gem");
		$array=$array[array_rand($array)];
		output("`@Once you are sitting on the couch, he asks you to stare at his clock, and tell him what you see..");
		output("`n`n'`^I see %s,`@' you say.",translate_inline($array));
		output("`n`n'`&Really? That is vierd. This is supposed to be a previliminary eye test.`@'");
		addnav("Stare into a Candle","runmodule.php?module=marriage&op=counselling&op2=see");
		break;
	case "see":
		$array=array("a green dragon","a tan dragon","a large swamp","an elephant with green pixies jumping around it","a troll","a vivid sunset");
		$array=$array[array_rand($array)];
		output("`@With you still sitting on the couch, he asks you to stare at a candle, and tell him what you see..");
		output("`n`n'`^I see %s,`@' you say.",translate_inline($array));
		output("`n`n'`&Keep that in mind as ve do ze next stage.`@'");
		addnav("The Next Stage","runmodule.php?module=marriage&op=counselling&op2=dream");
		break;
	case "dream":
		output("`@'`&Please lie down and try to go to sleep.`@' Lipvig says.");
		output("`n`nNot sure about going to sleep in the same room as this weird person, you manage to clear your head, and you are soon in the land of sleep...");
		output("`n`nHalfway through a weird dream, you are woken up and asked to explain what you saw.");
		output("`n`n'`^I saw a large room, filled with riches,`@' you say.");
		addnav("The Next Stage","runmodule.php?module=marriage&op=counselling&op2=materi");
		break;
	case "materi":
		output("`@'`&I have it!`@' Lipvig exclaims.");
		output("`n`n'`&I think you hunger for material riches...`@' Lipvig states.");
		output("`n`n'`&I must help you on your road.,. I was ordered...`@' Lipvig says, in an unhappy tone.");
		output("`n`nLipvig throws a powder over you...`n`n`%");
		switch (e_rand(1,10)) {
			case 1:
				$session['user']['gems']++;
				output("Something in the powder hits you on the head.  You look at Lipvig with an evil eye until you realize that it was a gem!");
			break;
			case 2: case 3:
				$exp=get_module_setting("exp");
				if ($exp>0){
					output("You feel more experienced.");
					$session['user']['experience']+=$exp;
				}else{
					output("You feel a `^gold piece`% hit you on the head.");
					$session['user']['gold']++;
				}
			break;
			case 4: case 5: case 6:
				$gold=get_module_setting("gold")*$session['user']['level'];
				if ($exp>1){
					$session['user']['gold']+=$gold;
					output("You feel richer.");
				}else{
					output("You feel a `^gold piece`% hit you on the head.");
					$session['user']['gold']++;
				}
			break;
			case 7: case 8: case 9: case 10:
				output("You feel a `^gold piece`% hit you on the head.");
				$session['user']['gold']++;
			break;
		}
		addnav("Leave","runmodule.php?module=marriage&op=counselling&op2=l");
		break;
	default:
		output("`@You stand up, not really sure how this helped you, but you are sure it must have done something.");
		output("`n`nYou leave.");
		if (get_module_setting("location")==0) villagenav();
		else addnav("Return to the Gardens","gardens.php");
		break;
}
page_footer();
?>