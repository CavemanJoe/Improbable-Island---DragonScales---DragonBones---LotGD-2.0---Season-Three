<?php

function findthebrain_getmoduleinfo(){
	$info = array(
		"name"=>"Find The Brain",
		"version"=>"2010-10-11",
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

function findthebrain_install(){
	module_addhook("pub_newpittsburgh");
	return true;
}

function findthebrain_uninstall(){
	return true;
}

function findthebrain_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "pub_newpittsburgh":
		case "village":
			addnav("Games");
			addnav("Play Find The Brain","runmodule.php?module=findthebrain&op=start");
		break;
		}
	return $args;
}

function findthebrain_run(){
	global $session;
	
	page_header("Find The Brain");
	
	$op = httpget("op");
	switch ($op){
		case "start":
			output("You wander over to a wall-mounted, glass-fronted wooden box.  Behind the glass, six holes are cut into the wood.  Below the glass, six coin slots gape invitingly.  A plate mounted to the bottom of the machine reads:`n`n`3`bINSTRUCTIONS`b`n1) Insert coins into slots`n2) Turn Handle`n3) BRAAAAAINS`n`n`bRULES`b`nWhen the handle is turned, a brain symbol will appear.  If your coin was in the slot corresponding to where the brain appears, the machine will pay five coins back.  You may bet on as many slots as you like.  In each round of play, multiple brains can appear, and multiple prizes can be paid.`0`n`nYou take this to mean that if you bet on all six slots, and all six slots contain brains, you'll win 30 coins back.  Which, given that each token is worth 10 Req, translates to 300 Requisition tokens.  Nice.`n`n");
			addnav("Forget it");
			addnav("Turn away","runmodule.php?module=pub_newpittsburgh&op=continue");
			
			$brains = array(
				1=>0,
				2=>0,
				3=>0,
				4=>0,
				5=>0,
				6=>0,
			);
			
			$bets = array();
			
			findthebrain_showboard($brains,$bets);
		break;
		case "play":
			//get bets
			$bets = array();
			$post = httpallpost();
			foreach($post AS $beton){
				$bets[$beton]=true;
			}

			$brains = array(
				1=>0,
				2=>0,
				3=>0,
				4=>0,
				5=>0,
				6=>0,
			);
			
			$amountbet = count($bets) * 10;
			
			//check the player made a bet
			if (!$amountbet){
				output("You didn't bet anything!`n`n");
				findthebrain_showboard($brains,$bets);
				addnav("That's enough");
				addnav("Turn away","runmodule.php?module=pub_newpittsburgh&op=continue");
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
				output("You insert your %s into the relevant %s and turn the handle.  There's a series of satisfying clanking and ratcheting sounds, and then a metal plate appears in a window, painted to look like a brain.`n`n",$cointxt,$slottxt);
				$session['user']['gold'] -= $amountbet;
				
				$choose = 1;
				$bonuschance = e_rand(0,100);
				while ($bonuschance > 82){
					$choose += 1;
					$bonuschance = e_rand(0,100);
				}
				
				for ($i=0; $i<$choose; $i++){
					$chosen = e_rand(1,6);
					if (!$brains[$chosen] && $i>0){
						output("There's a quiet sound of a spring straining against pressure, and then another brain shoots into place with a `ikerchunk.`i`n`n");
					}
					$brains[$chosen] = 1;
				}
				
				foreach($bets AS $key=>$value){
					if ($brains[$key]){
						$wonreq+=50;
					}
				}
				
				if ($wonreq){
					output("A bell rings, and tokens clatter into the payout slot.  You won %s Requisition!`n`n",$wonreq);
					$session['user']['gold'] += $wonreq;
					increment_module_setting("won",$wonreq,"findthebrain");
				} else {
					output("You didn't win anything this time...`n`n");
				}
			} else {
				output("You don't have enough money to cover that bet!`n`n");
			}
			findthebrain_showboard($brains,$bets);
			
			addnav("That's enough");
			addnav("Turn away","runmodule.php?module=pub_newpittsburgh&op=continue");
		break;
	}
	page_footer();
}

function findthebrain_showboard($brains,$bets=false){
	global $session;
	$images = array();
	foreach($brains AS $brain => $value){
		if ($value==0){
			$images[$brain] = "images/findthebrain/findthebrain_".$brain."_off.jpg";
		} else {
			$images[$brain] = "images/findthebrain/findthebrain_".$brain."_on.jpg";
		}
	}
	if (is_array($bets)){
		foreach ($bets AS $bet => $beton){
			$bets[$bet]="CHECKED";
		}
		rawoutput("<form action='runmodule.php?module=findthebrain&op=play' method='post'>");
		rawoutput("<table cellpadding=0 cellspacing=0>");
		rawoutput("<tr><td colspan=3><img src='images/findthebrain/findthebrain_top.jpg'></td></tr>");
		rawoutput("<tr><td><img src='".$images[1]."'></td><td><img src='".$images[2]."'></td><td><img src='".$images[3]."'></td></tr>");
		rawoutput("<tr><td colspan=3><img src='images/findthebrain/findthebrain_middle.jpg'></td></tr>");
		rawoutput("<tr><td><img src='".$images[4]."'></td><td><img src='".$images[5]."'></td><td><img src='".$images[6]."'></td></tr>");
		rawoutput("<tr><td colspan=3><img src='images/findthebrain/findthebrain_bottom.jpg'></td></tr>");
		
		rawoutput("<tr><td align=center><input type='checkbox' name='bet1' value='1' ".$bets[1]."></td><td align=center><input type='checkbox' name='bet2' value='2' ".$bets[2]."></td><td align=center><input type='checkbox' name='bet3' value='3' ".$bets[3]."></td></tr>");
		rawoutput("<tr><td align=center><input type='checkbox' name='bet4' value='4' ".$bets[4]."></td><td align=center><input type='checkbox' name='bet5' value='5' ".$bets[5]."></td><td align=center><input type='checkbox' name='bet6' value='6' ".$bets[6]."></td></tr>");
		rawoutput("</table><br /><br />");
		rawoutput("<input type='submit' class='button' value='".translate_inline("Bet!")."'>");
		rawoutput("</form>");
		addnav("","runmodule.php?module=findthebrain&op=play");
	}
}

?>