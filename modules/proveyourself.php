<?php

function proveyourself_getmoduleinfo(){
	$info = array(
		"name"=>"Proving Grounds",
		"author"=>"Dan Hall",
		"version"=>"2010-08-31",
		"category"=>"Improbable",
		"download"=>"",
		"prefs"=>array(
			"highscore"=>"Player's high score,int|0",
			"playedtoday"=>"Player has played today,bool|0",
		),
	);
	return $info;
}

function proveyourself_install(){
	$condition = "if (\$session['user']['location'] == \"Pleasantville\") {return true;} else {return false;};";
	module_addhook("village",false,$condition);
	module_addhook("footer-hof");
	return true;
}

function proveyourself_uninstall(){
	return true;
}

function proveyourself_dohook($hookname,$args){
	global $session;
	switch($hookname){
	case "village":
		tlschema($args['schemas']['fightnav']);
		addnav($args['fightnav']);
		tlschema();
		addnav("Nightmare Court","runmodule.php?module=proveyourself&op=start");
	break;
	case "footer-hof":
		addnav("Warrior Rankings");
		addnav("Nightmare Enthusiasts", "runmodule.php?module=proveyourself&op=hof");
	break;
	}
	return $args;
}

function proveyourself_run(){
	global $session,$badguy,$battle;
	$op = httpget('op');
	page_header("Nightmare Court");
	switch ($op){
		case "start":
			output("You head inside a large warehouse-type building.  A black-robed Mutant in a small reception area notices you and steps forward.  \"`4Welcome,`0\" he says, spreading his three misshapen arms dramatically.  \"`4This is a place where all of my kind will one day come to face their deepest fears.`0\"`n`nAh, the Obligatory Drama.  You nod and smile patiently as he explains.`n`n\"`4In this place, we teach how to accept one's limitations, and how to face and fight one's nightmares regardless.  It's a deeply intense and spiritual experience`0\"`n`n\"`#How much?`0\" you ask.`n`n\"`4Ah, the unfortunate topic of coin, how sadly necessary in a world obsessed with material wealth...`0\"`n`n\"`#`iHow much.`i`0\"`n`n\"`4It'll cost you a cigarette.`0\"`n`n\"`#Right, then.`0\"`n`n");
			if (!get_module_pref("playedtoday") && $session['user']['gems'] && $session['user']['hitpoints'] >= $session['user']['maxhitpoints']){
				addnav("What will you do?");
				addnav("Fight","runmodule.php?module=proveyourself&op=enter");
				addnav("Flee","village.php");
			} else if (get_module_pref("playedtoday")){
				addnav("You've already played today");
				addnav("Back to the Outpost","village.php");
			} else if (!$session['user']['gems']){
				addnav("You don't have a Cigarette handy");
				addnav("Back to the Outpost","village.php");
			} else if ($session['user']['hitpoints']<$session['user']['maxhitpoints']){
				addnav("You need to heal up first");
				addnav("Back to the Outpost","village.php");
			}
		break;
		case "enter":
			output("You drop your cigarette into an unnecessarily ornate box, and the Mutant ushers you through a wooden door and into the mouth of a long, dark, red-carpeted corridor.`n`n\"`4Drink this,`0\" he says, passing you a simple wooden goblet filled with a sticky-looking fluid.  You sniff cautiously, then drink.  It tastes of dust and steel.`n`n\"`4Now remember; nothing in here can hurt you unless you allow it to.`0\"  His voice drops to a whisper as he presses a battery-operated torch into your hand.  \"`4Good luck.`0\"`n`nThe door closes gently behind him, and a faint `iclick`i is heard.`n`nYou flick on the torch and take a few steps away from the single overhead lamp.`n`nYou begin the long walk down the corridor.  Before long the red carpet runs out, leaving you walking on a bare concrete floor.`n`nAs you proceed further away from the light, the corridor becomes narrower, and cracks appear every few feet in the plaster.  Another half a minute of putting one foot in front of the other, and you look down to find that you're now walking on steel treadplate.`n`nYou mark off the cracks in the walls as you proceed, noting their increasing frequency.  Within another minute, the walls are crumbling away to reveal a cold chain-link fence, behind which, nothing but darkness.`n`nYou feel the steel give way to rust which bends worryingly beneath your feet, little pieces of dead metal sticking to your boots and falling back with a quiet tinny rustling.`n`nYou still cannot see the end of the corridor.`n`nThe sour metallic scent of oily, corroded metal lingers around oxidized chain-link fences.  You tread warily around the holes in the floor; even with your torch, you can't see what's underneath the thinning treadplate.  You see something shining in the distance, and quicken your pace.  A heavy steel door.`n`nYou look back over your shoulder.  You cannot see the entrance.  Through the steel door, then.  You grip the handle to the right, and pull.`n`nYou hadn't realized how silent this place was until you opened the door; a throaty rushing noise issued from its tracks as corroded bearings spun in dust-blackened oil.  You step through.`n`nIs the battery in your torch dying?`n`nYou pull out your lighter, and ignite.`n`nThe flame is a dull, dirty orange, plumes of soot-darkened smoke billowing from your mundane, butane-driven lighter.`n`nYou tell yourself that the torch is not dying; its cone of light is not dimming to a deep orange, not turning redder and dimmer by the second.  None of that is happening.`n`nThe torch is just as bright as ever.  The problem is that the darkness is getting thicker.`n`nWithin seconds you are blind, and aware of how far away you are from other people, and medical attention.`n`nAware that if you get knocked out here, you won't be going to the FailBoat.`n`nYou feel the hot, damp breath on your face before you hear the growl.`n`n");
			addnav("Continue");
			addnav("Continue","runmodule.php?module=proveyourself&op=beginfighting");
		break;
		case "beginfighting":
			$session['user']['gems']--;
			restore_buff_fields();
			$badguy = array();
			$badguy['creaturename']="Nightmare";
			$badguy['creatureweapon']="Serrated Blades";
			$badguy['creaturelevel']=1;
			$badguy['creaturegold']= 0;
			$badguy['creatureexp'] = 0;
			$badguy['creaturehealth'] = 1000000000000;
			$badguy['creatureattack'] = 1;
			$badguy['creaturedefense'] = 1;
			$badguy['hidehitpoints'] = 1;
			calculate_buff_fields();
			$badguy['playerstarthp']=$session['user']['hitpoints'];
			$badguy['diddamage']=0;
			$badguy['type'] = 'nightmare';
			$badguy['creatureaiscript'] = "require(\"ai/nightmare.php\");";
			$session['user']['badguy']=createstring($badguy);
			
			$battle = true;
		break;
		case "run":
			output("You can't run from your nightmare.`n`n");
			$op = "fight";
			httpset('op', $op);
			$battle = true;
		break;
		case "fight":
			$battle = true;
		break;
		case "end":
			$points = httpget("points");
			$highscore = get_module_pref("highscore");
			output("You regain your senses while rolling around on the floor, waving your weapon at the empty air.  You're at the end of the corridor.  You look back to see the entryway not thirty feet away, and your Mutant friend striding along the corridor.`n`n\"`4Feeling better now?`0\"`n`n\"`#What the `ihell`i was in that drink?`0\"`n`n\"`4Died, did you?  Ah, well.  It's an experience, anyway.`0\"  He looks around at the chunks you've taken out of the walls.  \"`4Well, I reckon you knocked about %s hitpoints out of your imagination.  Naturally it'll all go on the Hall O' Fame.`0\"`n`nYou take a deep breath and begin to point a recriminating finger.  After a moment you shrug, get to your feet and leave, smacking the Mutant around the back of the head as you go.`n`n",$points);
			if ($points > $highscore){
				output("`4You beat your personal record!`0`n`n");
				set_module_pref("highscore",$points);
			}
			addnav("Exit");
			addnav("Back to the Outpost","village.php");
		break;
		case "hof":
			page_header("Nightmare Enthusiasts");
			$acc = db_prefix("accounts");
			$mp = db_prefix("module_userprefs");
			$sql = "SELECT $acc.name AS name,
				$acc.acctid AS acctid,
				$mp.value AS highscore,
				$mp.userid FROM $mp INNER JOIN $acc
				ON $acc.acctid = $mp.userid 
				WHERE $mp.modulename = 'proveyourself' 
				AND $mp.setting = 'highscore' 
				AND $mp.value > 0 ORDER BY ($mp.value+0)	
				DESC limit 200";
			$result = db_query($sql);
			$rank = translate_inline("Damage Points");
			$name = translate_inline("Name");
			output("`n`b`c`4Nightmare Enthusiasts`0`n`n`c`b");
			rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center'>");
			rawoutput("<tr class='trhead'><td align=center>$name</td><td align=center>$rank</td></tr>");
			for ($i=0;$i < db_num_rows($result);$i++){ 
				$row = db_fetch_assoc($result);
				if ($row['name']==$session['user']['name']){
					rawoutput("<tr class='trhilight'><td>");
				}else{
					rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td align=left>");
				}
				output_notl("%s",$row['name']);
				rawoutput("</td><td align=right>");
				output_notl("%s",number_format($row['highscore']));
				rawoutput("</td></tr>");
			}
			rawoutput("</table>");
			addnav("Back to HoF", "hof.php");
			villagenav();
		break;
	}
	if ($battle){
		include_once("battle.php");
		if ($defeat){
			addnav("You have been killed.");
			$session['user']['hitpoints']=$session['user']['maxhitpoints'];
			$points = 1000000000000-$badguy['creaturehealth'];
			addnav("What happens next?","runmodule.php?module=proveyourself&op=end&points=".$points);
		} else if ($victory){
			addnav("Now what?");
			addnav("What happens next?","runmodule.php?module=proveyourself&op=end&points=".$points);
		} else {
			require_once("lib/fightnav.php");
			fightnav(true,true,"runmodule.php?module=proveyourself",true);
		}
	}
	page_footer();
}
?>
