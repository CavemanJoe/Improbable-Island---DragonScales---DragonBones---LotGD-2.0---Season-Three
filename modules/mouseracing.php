<?php

function mouseracing_getmoduleinfo(){
	$info = array(
		"name"=>"Mouse Racing",
		"version"=>"2010-09-23",
		"author"=>"Dan Hall",
		"category"=>"Req Gambling",
		"download"=>"",
		"settings"=>array(
			"played"=>"Total money played,int|0",
			"won"=>"Total money won,int|0",
			"jackpot"=>"Jackpot on the purple mouse,int|200",
		)
	);
	return $info;
}

function mouseracing_install(){
	module_addhook("pub_kittania");
	return true;
}

function mouseracing_uninstall(){
	return true;
}

function mouseracing_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "pub_kittania":
			addnav("Games");
			addnav("Play Mouse Racing","runmodule.php?module=mouseracing&op=start");
		break;
		}
	return $args;
}

function mouseracing_run(){
	global $session;
	
	page_header("Mouse Racing");
	$jackpot = get_module_setting("jackpot");
	
	$op = httpget("op");
	switch ($op){
		case "start":
			output("You wander over to a wall-mounted, glass-fronted wooden box.  Five brightly-coloured metal mouse shapes sit behind the glass, and five coin slots are mounted invitingly to their left.  Each mouse sits across a long, thin channel that runs across the board towards a chequered line.  Beneath each of these channels is another one, angled slightly upwards.  On each mouse is a number that represents how many times your stake each mouse will pay if you win.`n`nA set of numeric reels at the bottom of the machine shows the current payoff for the purple mouse - a handsome `5%s`0 Requisition tokens.`n`nMiu-Miu sees you eyeballing the machine.  \"`2It's ten Req a token, love.  They're right here, behind the bar.`0\"`n`nWill you play?  And if so, how many tokens?  You can bet on any mouse or combination or mice, and each mouse that you bet on will need its own 10-Req token.  Check the boxes and Bet!`n`n",number_format($jackpot));
			addnav("Forget it");
			addnav("Turn away","runmodule.php?module=pub_kittania&op=continue");
			
			$mice = array(
				1=>1,
				2=>1,
				3=>1,
				4=>1,
				5=>1,
			);
			
			$bets = array();
			
			mouseracing_showboard($mice,$bets,$jackpot);
		break;
		case "play":
			$jackpot = httpget("jackpot");
			//get bets
			$bets = array();
			$post = httpallpost();
			foreach($post AS $beton){
				$bets[$beton]=true;
			}

			$finish = 30;
			$mice = array(
				1=>1,
				2=>1,
				3=>1,
				4=>1,
				5=>1,
			);
			$winnings = array(
				1 => 20,
				2 => 40,
				3 => 60,
				4 => 80,
				5 => $jackpot,
			);
			$slidevals = array(
				1 => 42,
				2 => 34,
				3 => 30,
				4 => 28,
				5 => 20, //20
			);
			
			$amountbet = count($bets) * 10;
			
			//check the player made a bet
			if (!$amountbet){
				output("You didn't bet anything!`n`n");
				mouseracing_showboard($mice,$bets,$jackpot);
				addnav("That's enough");
				addnav("Turn away","runmodule.php?module=pub_kittania&op=continue");
				break;
			}
			
			increment_module_setting("played",$amountbet);
			
			//check the player has enough money to cover the bet
			if ($session['user']['gold'] >= $amountbet){
				//play the game!
				if ($amountbet==10){
					$cointxt = "token";
					$slottxt = "slot";
				} else {
					$cointxt = "tokens";
					$slottxt = "slots";
				}
				output("You insert your %s into the relevant %s and press the \"Start\" button.  A low hum can be heard, which builds up to a high-pitched whine.  A long arm swipes across the board from right to left, pushing the mice back to their starting positions, before retracting back into its slot on the right-hand side.`n`nYou hear what sounds like a clutch mechanism engaging, and the whine dips in pitch a little, and then there's a ferocious clatter as five little steel ball bearings are violently propelled against plates behind the mice, one ball for each mouse.  After bouncing off the plates, the balls roll back down towards the flywheels that flicked them.  Depending on the position of the flywheel notches in the split-second in which the ball makes contact, the ball sometimes fires out so fast you can't see it move; other times it barely make it to the mouse.`n`nThe mouse at the top seems to be held in place with a little less tension, and the ones below look like they're held increasingly tightly.  The red mouse jerks along the track a few centimeters every time its ball makes contact, while the purple mouse barely seems to notice the impacts.`n`nYou watch the mice scuttle jerkily along the track as the machine clatters away.  It makes a hell of a racket, just like a good arcade machine should.`n`n",$cointxt,$slottxt);
				$session['user']['gold'] -= $amountbet;
				$running = true;
				$wonreq = 0;
				$jackpot += 5;
				while ($running){
					$mouse = e_rand(1,5);
					$slidenom = $slidevals[$mouse]*10;
					$slide = e_rand($slidenom/2,$slidenom*2);
					$slide = $slide/100;
					$mice[$mouse] += $slide;
					
					if ($mice[$mouse] >= 5 && !$seen5){
						$seen5 = true;
						mouseracing_showboard($mice);
					}
					if ($mice[$mouse] >= 10 && !$seen10){
						$seen10 = true;
						mouseracing_showboard($mice);
					}
					if ($mice[$mouse] >= 15 && !$seen15){
						$seen15 = true;
						mouseracing_showboard($mice);
					}
					if ($mice[$mouse] >= 20 && !$seen20){
						$seen20 = true;
						mouseracing_showboard($mice);
					}
					if ($mice[$mouse] >= 25 && !$seen25 && $mice[$mouse] < 30){
						$seen25 = true;
						mouseracing_showboard($mice);
					}
					
					if ($mice[$mouse] >= $finish){
						$running = false;
						if (array_key_exists($mouse,$bets)){
							$wonreq += $winnings[$mouse];
							if ($mouse==5){
								set_module_setting("jackpot",200,"mouseracing");
								$jackpot = 200;
							}
						}
						mouseracing_showboard($mice,$bets,$jackpot);
					}
				}
				if ($wonreq){
					output("A bell rings, and tokens clatter into the payout slot.  You won %s Requisition!`n`n",$wonreq);
					$session['user']['gold'] += $wonreq;
					increment_module_setting("won",$wonreq,"mouseracing");
				} else {
					output("You didn't win anything this time...`n`n");
				}
				increment_module_setting("jackpot",5,"mouseracing");
			} else {
				output("You don't have enough money to cover that bet!`n`n");
				mouseracing_showboard($mice,$bets,$jackpot);
			}
			
			addnav("That's enough");
			addnav("Turn away","runmodule.php?module=pub_kittania&op=continue");
		break;
	}
	page_footer();
}

function mouseracing_showboard($mice,$bets=false,$jackpot=200){
	global $session;
	foreach($mice AS $mouse => $position){
		//calculate which image to show
		$img = floor(($position*1.2) / 6);
		if ($img < 1){
			$img = 1;
		}
		if ($img > 6){
			//it's vanishingly unlikely, but hey, it could happen
			$img = 6;
		}
		$mice[$mouse] = $img;
	};
	if (is_array($bets)){
		foreach ($bets AS $bet => $beton){
			$bets[$bet]="CHECKED";
		}
		$jackpot = get_module_setting("jackpot","mouseracing");
		rawoutput("<form action='runmodule.php?module=mouseracing&op=play&jackpot=$jackpot' method='post'>");
		rawoutput("<table cellpadding=0 cellspacing=0>");
		rawoutput("<tr><td width='50px' align='center'></td>");
		rawoutput("<td><img src='images/mouseracing/mouseracing_top.jpg'></td></tr>");
		rawoutput("<tr><td width='50px' align='center'><input type='checkbox' name='bet1' value='1' ".$bets[1]."></td>");
		rawoutput("<td><img src='images/mouseracing/mouseracing_".$mice[1]."_1.jpg'></td></tr>");
		rawoutput("<tr><td width='50px' align='center'><input type='checkbox' name='bet2' value='2' ".$bets[2]."></td>");
		rawoutput("<td><img src='images/mouseracing/mouseracing_".$mice[2]."_2.jpg'></td></tr>");
		rawoutput("<tr><td width='50px' align='center'><input type='checkbox' name='bet3' value='3' ".$bets[3]."></td>");
		rawoutput("<td><img src='images/mouseracing/mouseracing_".$mice[3]."_3.jpg'></td></tr>");
		rawoutput("<tr><td width='50px' align='center'><input type='checkbox' name='bet4' value='4' ".$bets[4]."></td>");
		rawoutput("<td><img src='images/mouseracing/mouseracing_".$mice[4]."_4.jpg'></td></tr>");
		rawoutput("<tr><td width='50px' align='center'><input type='checkbox' name='bet5' value='5' ".$bets[5]."></td>");
		rawoutput("<td><img src='images/mouseracing/mouseracing_".$mice[5]."_5.jpg'></td></tr>");
		rawoutput("<tr><td width='50px' align='center'><input type='submit' class='button' value='".translate_inline("Bet!")."'></td>");
		rawoutput("<td><img src='images/mouseracing/mouseracing_bottom.jpg'></td></tr>");
		rawoutput("</table><br /><br />");
		rawoutput("</form>");
		output("Current Jackpot: `5%s Req`0`n`n",$jackpot);
		addnav("","runmodule.php?module=mouseracing&op=play&jackpot=$jackpot");
	} else {
		rawoutput("<table cellpadding=0 cellspacing=0>");
		rawoutput("<tr><td width='50px' align='center'></td><td><img src='images/mouseracing/mouseracing_top.jpg'></td></tr>");
		rawoutput("<tr><td width='50px' align='center'></td><td><img src='images/mouseracing/mouseracing_".$mice[1]."_1.jpg'></td></tr>");
		rawoutput("<tr><td width='50px' align='center'></td><td><img src='images/mouseracing/mouseracing_".$mice[2]."_2.jpg'></td></tr>");
		rawoutput("<tr><td width='50px' align='center'></td><td><img src='images/mouseracing/mouseracing_".$mice[3]."_3.jpg'></td></tr>");
		rawoutput("<tr><td width='50px' align='center'></td><td><img src='images/mouseracing/mouseracing_".$mice[4]."_4.jpg'></td></tr>");
		rawoutput("<tr><td width='50px' align='center'></td><td><img src='images/mouseracing/mouseracing_".$mice[5]."_5.jpg'></td></tr>");
		rawoutput("<tr><td width='50px' align='center'></td><td><img src='images/mouseracing/mouseracing_bottom.jpg'></td></tr>");
		rawoutput("</table><br /><br />");
	}
}

?>