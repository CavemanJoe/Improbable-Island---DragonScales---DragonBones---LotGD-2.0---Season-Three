<?php

function clatter_getmoduleinfo(){
	$info = array(
		"name"=>"Clatter",
		"version"=>"2009-11-17",
		"author"=>"Dan Hall",
		"category"=>"Req Gambling",
		"download"=>"",
		"prefs"=>array(
			"lit"=>"Player's lights array,text|array()",
			"virgin"=>"Player is new to Clatter,bool|1",
		),
		"settings"=>array(
			"played"=>"Total coins played,int|0",
			"won"=>"Total coins won,int|0",
		)
	);
	return $info;
}
function clatter_install(){
	module_addhook("inn");
	return true;
}
function clatter_uninstall(){
	return true;
}
function clatter_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "inn":
			if (get_module_pref("virgin")){
				output("Mounted on one wall is a funny-looking wooden device with a coin slot and a glass front panel.`n`n");
				addnav("Ask about the funny-looking machine","runmodule.php?module=clatter&op=virgin");
			} else {
				addnav("Play Clatter","runmodule.php?module=clatter&op=start");
			}
		break;
		}
	return $args;
}
function clatter_run(){
	global $session;
	if (httpget('op')=="virgin"){
		page_header("What a curious-looking device.");
		$bk = getsetting('barkeep','`tCedrik');
		rawoutput("<img src=\"images/clatter/clatterboard.jpg\" align=right>");
		set_module_pref("virgin",0);
		output("You head over to the bar and ask %s what the deal is with that weird-looking box thingy on the wall.`n`n%s grins.  \"`%You like it, then?  Built it meself.  It's all relays, no IC's at all.  Runs off an old car battery.`0\"`n`n\"`#It's very nice, yes,`0\" you say carefully, \"`#but what `iis`i it, %s?`0\"`n`n%s beams proudly.  \"`%I just thought of a name fer it this mornin' - I'm gonna call it \"Clatter!\"  You put a coin in the top, see, and it goes down that nailboard.  If it comes out the very middle on the last row of nails, you get to keep it!`0\"`n`nYou frown.  \"`#I'm not sure I like those odds, %s.`0\"`n`n\"`%Ah, there's the clever bit.  Y'see those circles dotted around the nailboard?  If yer coin goes over one o' them on the way down, an electromagnetic slide fires some more coins out at the top.  It's all about chain reactions, y'see.`0\"`n`nYou stroke your chin thoughtfully.  \"`#What sort of coins does it take?`0\"`n`n\"`%It only takes these tokens here, behind the bar.  You can buy 'em from me for 20 Req each, one at a time.`0\"`n`n\"`#Wait, `ione at a time?`i  Doesn't that mean you have to hang around the machine for ages whenever someone plays it?  Who minds the bar?`0\"`n`n%s shrugs.  \"`%Corporal Punishment talked me into doin' it that way.  He kept on at me about savin' clicks and keepin' things simple for the player.  I didn't understand a bloody word of it, but y'know, he's persuasive.  Manages to talk folks out of walking around with their fists strapped together behind their backs, y'know, stuff like that.`0\" %s stares off into the middle distance for a moment. \"`%Not sure where that weird little habit started.  Seems all the cool kids were doin' it at one point.  But yeah, I sell the tokens to ya one at a time.`0\"  You raise a finger and open your mouth, and %s cuts you off.  \"`%`iTrust me,`i it's just `ieasier`i this way.  Anyway, the coins go in the top, an' then if they go over a target, it'll light up.  Next time a coin goes over that target, it'll fire out some more coins an' maybe light up another target.  So d'you fancy a go, or what?`0\"`n`n",$bk,$bk,$bk,$bk,$bk,$bk,$bk,$bk);
	} else {
		page_header("Clatter");
	}
	
	if (httpget('op')=="start"){
		output("The Clatter machine lurks in the corner, tempting you with promises of easy wealth.");
	}
	
	$woncoins=0;
	
	if (httpget('op')=="play"){
		$session['user']['gold']-=20;
		$lit=unserialize(get_module_pref("lit"));
		if (!isset($lit[1])){
			$lit = array();
			$lit[1] = 0;
			$lit[2] = 0;
			$lit[1] = 0;
			$lit[4] = 0;
			$lit[3] = 0;
			$lit[6] = 0;
			$lit[2] = 0;
		}
		$coinsinplay=1;
		$totalcoinsplayed=0;
		output("You plonk down twenty Requisition tokens onto the bar, and blink.  A single Clatter token sits in a beery puddle.  You pick it up and put it into the slot on the top of the machine.`n`n");
		increment_module_setting("played");
		while ($coinsinplay>0){
			$msg="";
			$coinsinplay--;
			$totalcoinsplayed++;
			$start = mt_rand(0,1);
			if ($start){
				$coinposition = 6;
			} else {
				$coinposition = 8;
			}
			
			// output("Dropping coin %s`n",$totalcoinsplayed);
			rawoutput("<table width=100% cellpadding=0 cellspacing=2><tr><td>");
			if ($totalcoinsplayed==1){
				$msg.="Your beer-soaked coin clatters down the nailboard, ";
			} else {
				$msg.="An electromagnetic slide fires with a satisfying KER-CHUNK, and a new coin appears at the top of the nailboard.  It descends upon its merry way, ";
			}
			$bonus=0;
			for ($i=1;$i<=7;$i++){
				if (mt_rand(0,1)){
					$coinposition++;
				} else {
					$coinposition--;
				}
				
				//bounce coins off edges
				if ($coinposition>13){
					$coinposition=12;
				}
				if ($coinposition<1){
					$coinposition=2;
				}
				
				//award bonuses
				if ($i==3 && ($coinposition==3 || $coinposition==11)){
					$bonus=1;
					$msg.="passing through the first target and closing the switch.  ";
					if ($lit[1]){
						$msg.="A relay clicks open and turns off the lamp, while incrementing a counter.  `n";
						$coinsinplay+=1;
						if (!$lit[3]){
							$msg.="The lamp behind the centre target turns on.`n";
						}
						$lit[3]=1;
						$lit[1]=0;
					} else {
						$msg.="A relay clicks closed and the lamps behind the top set of targets switch on.  The next time a coin goes across either of these targets, a bonus coin will be awarded.`n";
						$lit[1]=1;
					}
				} else if (($i==5 && $coinposition==7)){
					$bonus=1;
					$msg.="passing through the centre target and closing the switch.  ";
					if ($lit[3]){
						$msg.="A relay clicks open and turns off the lamp, and three clicks from deep inside the machine suggests a counter is incrementing.`n";
						$coinsinplay+=3;
						if (!$lit[2]){
							$msg.="The lamps behind the lower targets turn on.`n";
						}
						$lit[2]=1;
						$lit[3]=0;
					} else {
						$msg.="A relay clicks closed and the lamp behind the center target turns on.  The next time a coin goes across this target, three bonus coins will be awarded.`n";
						$lit[3]=1;
					}
				} else if (($i==7 && ($coinposition==1 || $coinposition==13))){
					if (!$bonus){
						$msg.="completely missing the one-bonus and three-bonus targets and falling into one of the side channels.  ";
					} else {
						$msg.="As a further awesomeness, the coin then drops down one of the side channels.  ";
					}
					$bonus=1;
					if ($lit[2]){
						$msg.="A relay clicks open and turns off the lamp, and two clicks from deep inside the machine suggests a counter is incrementing.`n";
						$coinsinplay+=2;
						if (!$lit[1]){
							$msg.="The lamp behind the center target turns on.`n";
						}
						$lit[1]=1;
						$lit[2]=0;
					} else {
						$msg.="A relay clicks closed and the lamps behind the bottom set of targets switch on.  The next time a coin goes across either of these targets, two bonus coins will be awarded.`n";
						$lit[2]=1;
					}
				}
				//output image
				rawoutput("<img src=\"images/clatter/".$i."-".$coinposition."-".$lit[$i].".jpg\"><br />");
			}// end individual coin drop
			if ($coinposition==7 && $bonus){
				$msg.="`nThe coin clatters into the centre channel, and falls into the payout chute.  Immediately a hairy hand from behind the bar grabs it and thrusts twenty Requisition tokens towards you.`n`n";
				$woncoins++;
				increment_module_setting("won");
			} else if ($coinposition==7){
				$msg.="missing every single target before being returned to you via the Payout chute at the bottom of the machine.  Immediately a hairy hand from behind the bar grabs it and thrusts twenty Requisition tokens towards you.`n`n";
				$woncoins++;
				increment_module_setting("won");
			} else {
				if (!$bonus){
					$msg.="completely failing to do any good whatsoever and simply disappearing into the depths of the machine.  Bah.`n`n";
				} else {
					$msg.="The coin falls into the depths of the machine.`n`n";
				}
			}
			rawoutput("</td><td>");
			output("%s`n`n",$msg);
			rawoutput("</td></tr></table>");
		} // end page load
		set_module_pref("lit",serialize($lit));
		$winnings = $woncoins*20;
		$session['user']['gold']+=$winnings;
		if ($winnings){
			output("`nYou have won a total of %s Requisition tokens this time, minus your 20 Requisition stake.",$winnings);
			if ($winnings==20){
				output("  In other words, you broke even.  Huzzah!");
			}
		} else {
			output("`nYou didn't win anything this time.");
		}
	}
	
	addnav("Clatter");
	if ($session['user']['gold']>=20){
		addnav("Play Clatter","runmodule.php?module=clatter&op=play");
		addnav("Play Flash version","runmodule.php?module=clatterflash&op=play");
	} else {
		addnav("You can't afford to gamble right now.","");
	}
	addnav("Walk Away","inn.php");
	page_footer();
}
?>