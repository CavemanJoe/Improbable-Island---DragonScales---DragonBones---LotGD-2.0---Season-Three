<?php

/*
=======================================================
GENERAL NOTES
1. The "Attacking" player is the one who performed the search and initiated the attack.
2. The battle is started by the attacking player calling scrapbots_battle($defendingid);
=======================================================
*/

function scrapbots_battle($defenderid){
	global $session;
	$attackerid = $session['user']['acctid'];

	/*
	=======================================================
	Retrieve attacking and defending armies from the database along with global retreat percentages, and put them into $attacker and $defender arrays
	=======================================================
	*/	
	
	//get attackers
	$sql = "SELECT id,owner,name,activated,hitpoints,brains,brawn,briskness,junglefighter,retreathp FROM ".db_prefix("scrapbots")." WHERE owner = $attackerid && activated = 1";
	$result = db_query($sql);
	$attacker = array();
	for ($i=0;$i<db_num_rows($result);$i++){
		$attacker[$i]=db_fetch_assoc($result);
	}
	$sql = "SELECT id,owner,name,activated,hitpoints,brains,brawn,briskness,junglefighter,retreathp FROM ".db_prefix("scrapbots")." WHERE owner = $defenderid && activated = 1";
	$result = db_query($sql);
	$defender = array();
	for ($i=0;$i<db_num_rows($result);$i++){
		$defender[$i]=db_fetch_assoc($result);
	}

	$atkretreatpct = get_module_pref("retreatpct","scrapbots",$attackerid);
	$defretreatpct = get_module_pref("retreatpct","scrapbots",$defenderid);
	
	//We're going to put all of the destroyed bots into a special array, so that we can retrieve the scrap from them later on.
	$destroyed = array();
	
	//We'll put the bots that fled into arrays too, so that they can be returned after the fight.
	$atkfled = array();
	$deffled = array();
	
	$war = 1;
	$burn = 0;
	/*
	=======================================================
	WAR IS ON!
	=======================================================
	*/	
	
	while ($war == 1){
		$attacker = array_values($attacker);
		$defender = array_values($defender);
		$burn++;
		if ($burn > 10000){
			//debug("Burned in War loop");
			break;
		}
		//get a fighter from the Attacking army
		$foundattacker = 0;
			while ($foundattacker == 0){
			$burn++;
			if ($burn > 10000){
				//debug("Burned while finding attacker");
				break;
			}
			$atkbot = e_rand(0,(count($attacker)-1));
			if (e_rand(1,100) < ($attacker[$atkbot]['briskness'] * 10)){
				// debug ("Attacker Chosen:");
				$foundattacker = 1;
				// debug ($attacker[$atkbot]);
			} else {
			// debug("Attacker failed Briskness check.  Choosing again.");
			}
		}
		
		//get a fighter from the Defending army
		$founddefender = 0;
		while ($founddefender == 0){
			$burn++;
			if ($burn > 10000){
				//debug("Burned while finding Defender");
				break;
			}
			$defbot = e_rand(0,(count($defender)-1));
			if (e_rand(1,100) < ($defender[$defbot]['briskness'] * 10)){
				// debug ("Defender Chosen:");
				$founddefender = 1;
				// debug ($defender[$defbot]);
			} else {
			// debug("Defender failed Briskness check.  Choosing again.");
			}
		}

		/*
		=======================================================
		Choose which bot strikes first
		=======================================================
		*/

		// if ($attacker[$atkbot]['briskness'] > $defender[$defbot]['briskness']){
			// $turn = "atk";
		// } else if ($attacker[$atkbot]['briskness'] < $defender[$defbot]['briskness']){
			// $turn = "def";
		// } else {
			// $coin = e_rand(0,1);
			// if ($coin == 1){
				// $turn = "atk";
			// } else {
				// $turn = "def";
			// }
		// }
		
		$chance = (($attacker[$atkbot]['briskness']*e_rand(0,100)) - ($defender[$defbot]['briskness']*e_rand(0,100)));
		if ($chance > 0){
			$turn = "atk";
		} else {
			$turn = "def";
		}
		
		// debug($turn);
		
		/*
		=======================================================
		Output flavour text
		=======================================================
		*/

		output("`b`4%s`0 lunges at `2%s`0!`b`n",$attacker[$atkbot]['name'],$defender[$defbot]['name']);
		$atkhp = $attacker[$atkbot]['hitpoints'];
		$defhp = $defender[$defbot]['hitpoints'];
		output("`$`b%s`b`0 / `@`b%s`b`0`n",$atkhp,$defhp);
		if ($turn == "atk"){
			output("`$%s gets the first attack!`n",$attacker[$atkbot]['name']);
		} else {
			output("`@%s gets the first attack!`n",$defender[$defbot]['name']);
		}
		
		/*
		=======================================================
		Bots knock the shit out of each other
		=======================================================
		*/
		$battle = 1;
		$fight = 1;
		$run = 0;
		while ($battle == 1){
			$roundsofrunning = 0;
			$burn++;
			if ($burn > 10000){
				//debug("Burned in Battle loop");
				break;
			}
			while ($fight == 1){
				$burn++;
				if ($burn > 10000){
					//debug("Burned while fighting");
					break;
				}
				if ($defender[$defbot]['hitpoints'] < (($defender[$defbot]['brawn']/10)*$defender[$defbot]['retreathp']) || $attacker[$atkbot]['hitpoints'] < (($attacker[$atkbot]['brawn']/10)*$attacker[$atkbot]['retreathp'])){
					$fight = 0;
					$run = 1;
				}
				if ($turn == "atk"){
					$hitchance = 50 + (($attacker[$atkbot]['briskness'] - $defender[$defbot]['briskness']) * 3);
					if ($attacker[$atkbot]['hitpoints'] < (($attacker[$atkbot]['brawn']/10)*$attacker[$atkbot]['retreathp'])){
						$hitchance = 25 + (($attacker[$atkbot]['briskness'] - $attacker[$atkbot]['briskness']) * 4);
						output("`2%s is a bit too busy running for its life to attack properly!`n",$attacker[$atkbot]['name']);
					}
					if ($hitchance > e_rand(1,100)){
						//Bot lands a successful hit
						$mindamage = 2+(ceil($attacker[$atkbot]['brawn']/2));
						$maxdamage = 5+($attacker[$atkbot]['brawn']*2);
						$damage = e_rand($mindamage, $maxdamage);
						output("`4%s lands a successful hit!  %s takes `$%s`4 damage!`n",$attacker[$atkbot]['name'],$defender[$defbot]['name'],$damage);
						$defender[$defbot]['hitpoints'] -= $damage;
						$atkhp = $attacker[$atkbot]['hitpoints'];
						$defhp = $defender[$defbot]['hitpoints'];
						if ($atkhp < 0){$atkhp=0;}
						if ($defhp < 0){$defhp=0;}
						output("`$`b%s`b`0 / `@`b%s`b`0`n",$atkhp,$defhp);
						//reprogramming
						if ($attacker[$atkbot]['brains']>5 && $defender[$defbot]['hitpoints'] > 0){
							$reprogramchance = (($attacker[$atkbot]['brains'])-($defender[$defbot]['brains'])) * 5;
							if ($reprogramchance > e_rand(1,100)){
								output("`b`4%s pulls off a successful reprogramming!  %s switches sides!`b`n", $attacker[$atkbot]['name'], $defender[$defbot]['name']);
								$atkhp = $attacker[$atkbot]['hitpoints'];
								$defhp = $defender[$defbot]['hitpoints'];
								if ($atkhp < 0){$atkhp=0;}
								if ($defhp < 0){$defhp=0;}
								output("`$`b%s`b`0 / `@`b%s`b`0`n`n",$atkhp,$defhp);
								$defender[$defbot]['owner'] = $attackerid;
								$attacker[] = $defender[$defbot];
								$reprogrammed[] = $defender[$defbot];
								unset($defender[$defbot]);
								$battle = 0;
								$fight = 0;
								$run = 0;
								break;
							}
						}
					} else {
						output("`4%s swings at %s, who dodges out of the way!`n",$attacker[$atkbot]['name'],$defender[$defbot]['name']);
					}
					if ($defender[$defbot]['hitpoints'] <= 0){
						output("`@`b%s flies apart in a glorious display of spinning, burning scrap metal!`b`n`n",$defender[$defbot]['name']);
						$destroyed[] = $defender[$defbot];
						unset($defender[$defbot]);
						$battle = 0;
						$fight = 0;
						$run = 0;
						break;
					}
					$turn = "def";
				} else {
					$hitchance = 50 + (($defender[$defbot]['briskness'] - $attacker[$atkbot]['briskness']) * 3);
					if ($defender[$defbot]['hitpoints'] < (($defender[$defbot]['brawn']/10)*$defender[$defbot]['retreathp'])){
						$hitchance = 25 + (($defender[$defbot]['briskness'] - $attacker[$atkbot]['briskness']) * 4);
						output("`2%s is a bit too busy running for its life to attack properly!`n",$defender[$defbot]['name']);
					}
					if ($hitchance > e_rand(1,100)){
						//Bot lands a successful hit
						$mindamage = 2+(ceil($defender[$defbot]['brawn']/2));
						$maxdamage = 5+($defender[$defbot]['brawn']*2);
						$damage = e_rand($mindamage, $maxdamage);
						output("`2%s lands a successful hit!  %s takes `@%s`2 damage!`n",$defender[$defbot]['name'],$attacker[$atkbot]['name'],$damage);
						$attacker[$atkbot]['hitpoints'] -= $damage;
						$atkhp = $attacker[$atkbot]['hitpoints'];
						$defhp = $defender[$defbot]['hitpoints'];
						if ($atkhp < 0){$atkhp=0;}
						if ($defhp < 0){$defhp=0;}
						output("`$`b%s`b`0 / `@`b%s`b`0`n",$atkhp,$defhp);
						//reprogramming
						if ($defender[$defbot]['brains']>5 && $attacker[$atkbot]['hitpoints'] > 0){
							$reprogramchance = (($defender[$defbot]['brains'])-($attacker[$atkbot]['brains'])) * 5;
							if ($reprogramchance > e_rand(1,100)){
								output("`b`4%s pulls off a successful reprogramming!  %s switches sides!`b`n", $defender[$defbot]['name'], $attacker[$atkbot]['name']);
								$atkhp = $attacker[$atkbot]['hitpoints'];
								$defhp = $defender[$defbot]['hitpoints'];
								if ($atkhp < 0){$atkhp=0;}
								if ($defhp < 0){$defhp=0;}
								output("`$`b%s`b`0 / `@`b%s`b`0`n`n",$atkhp,$defhp);
								$attacker[$atkbot]['owner'] = $defenderid;
								$defender[] = $attacker[$atkbot];
								$reprogrammed[] = $attacker[$atkbot];
								unset($attacker[$atkbot]);
								$battle = 0;
								$fight = 0;
								$run = 0;
								break;
							}
						}
					} else {
						output("`2%s swings at %s, who dodges out of the way!`n",$defender[$defbot]['name'],$attacker[$atkbot]['name']);
					}
					if ($attacker[$atkbot]['hitpoints'] <= 0){
						output("`$`b%s flies apart in a glorious display of spinning, burning scrap metal!`b`n`n",$attacker[$atkbot]['name']);
						$destroyed[] = $attacker[$atkbot];
						unset($attacker[$atkbot]);
						$battle = 0;
						$fight = 0;
						$run = 0;
						break;
					} else {
						$turn = "atk";
					}
				}
				if ($run==1){
					output("`n");
				}
			}
			/*
			=======================================================
			Benny Hill style chase scene
			=======================================================
			*/
			while ($run == 1){
				// $burn++;
				// debug($burn);
				// debug($attacker[$atkbot]);
				// debug($defender[$defbot]);
				// if ($burn > 1000){
					// debug("Burned while running");
					// $war = 0;
					// $battle = 0;
					// break;
				// }
				if ($defender[$defbot]['hitpoints'] < (($defender[$defbot]['brawn']/10)*$defender[$defbot]['retreathp']) && $attacker[$atkbot]['hitpoints'] < (($attacker[$atkbot]['brawn']/10)*$attacker[$atkbot]['retreathp'])){
					output("`0`bBoth ScrapBots, having activated their automatic retreat subroutine - and being of limited intelligence - tear off in opposite directions from each other, flailing their servos madly in the air!`b`n`n");
					$atkfled[] = $attacker[$atkbot];
					unset ($attacker[$atkbot]);
					$deffled[] = $defender[$defbot];
					unset($defender[$defbot]);
					$battle = 0;
					$run = 0;
				}
				if ($defender[$defbot]['hitpoints'] >= (($defender[$defbot]['brawn']/10)*$defender[$defbot]['retreathp']) && $attacker[$atkbot]['hitpoints'] < (($attacker[$atkbot]['brawn']/10)*$attacker[$atkbot]['retreathp'])){
					//attacker is running, defender is chasing
					$distanceatk = 5+(e_rand($attacker[$atkbot]['briskness'],$attacker[$atkbot]['briskness']*10));
					$attacker[$atkbot]['distancetravelled'] += $distanceatk;
					$distancedef = 5+(e_rand($defender[$defbot]['briskness'],$defender[$defbot]['briskness']*10));
					$defender[$defbot]['distancetravelled'] += $distancedef;
					output("`4%s runs like hell, covering a distance of `$%s`4 feet!`n",$attacker[$atkbot]['name'],$distanceatk);
					$distancebetween = ($attacker[$atkbot]['distancetravelled'] - $defender[$defbot]['distancetravelled']);
					if ($distancebetween <= 0){
						$run = 0;
						$fight = 1;
						output("`@%s catches up rather quickly, and sets about maiming the poor fleeing ScrapBot!`n",$defender[$defbot]['name']);
					} else {
						if ($roundsofrunning > 5){
							output("`@%s throws down its hat in disgust, and declares the chase a waste of time, heading back to the main battle.  `b%s gets away!`b`n`n",$defender[$defbot]['name'],$attacker[$atkbot]['name']);
							$atkfled[] = $attacker[$atkbot];
							unset ($attacker[$atkbot]);
							$battle = 0;
							$run = 0;
						} else {
							output("`2%s chases furiously, closing the gap between them to `@%s`2 feet!`n",$defender[$defbot]['name'],$distancebetween);
						}
					}
				} 
				if ($defender[$defbot]['hitpoints'] < (($defender[$defbot]['brawn']/10)*$defender[$defbot]['retreathp']) && $attacker[$atkbot]['hitpoints'] >= (($attacker[$atkbot]['brawn']/10)*$attacker[$atkbot]['retreathp'])){
					//defender is running, attacker is chasing
					$distancedef = 5+(e_rand($defender[$defbot]['briskness'],$defender[$defbot]['briskness']*10));
					$defender[$defbot]['distancetravelled'] += $distancedef;
					$distanceatk = 5+(e_rand($attacker[$atkbot]['briskness'],$attacker[$atkbot]['briskness']*10));
					$attacker[$atkbot]['distancetravelled'] += $distanceatk;
					output("`2%s runs like hell, covering a distance of `@%s`2 feet!`n",$defender[$defbot]['name'],$distancedef);
					$distancebetween = ($defender[$defbot]['distancetravelled'] - $attacker[$atkbot]['distancetravelled']);
					if ($distancebetween <= 0){
						$run = 0;
						$fight = 1;
						output("`$%s catches up rather quickly, and sets about maiming the poor fleeing ScrapBot!`n",$attacker[$atkbot]['name']);
					} else {
						if ($roundsofrunning > 5){
							output("`$%s throws down its hat in disgust, and declares the chase a waste of time, heading back to the main battle.  `b%s gets away!`b`n`n",$attacker[$atkbot]['name'],$defender[$defbot]['name']);
							$deffled[] = $defender[$defbot];
							unset($defender[$defbot]);
							$battle = 0;
							$run = 0;
						} else {
							output("`4%s chases furiously, closing the gap between them to `$%s`$ feet!`n",$attacker[$atkbot]['name'],$distancebetween);
						}
					}
				}
				$roundsofrunning ++;
			}
		}
		
		/*
		=======================================================
		We see if someone's won yet
		=======================================================
		*/

		$atkcount = count($attacker);
		$defcount = count($defender);
		
		if ($atkcount == 0){
			$winner = $defenderid;
			$war = 0;
			output("`c`b`\$You have lost this battle!`0`b`c`n");
		} else if ($defcount == 0){
			$winner = $attackerid;
			$war = 0;
			output("`c`b`@You have won this battle!`0`b`c`n");
		}
		
		/*
		=======================================================
		Output a message saying how many ScrapBots are left on each side
		=======================================================
		*/
		
		$atkcount = count($attacker);
		$defcount = count($defender);
		output("`c`$`b%s`b`0 / `@`b%s`b`0`c`n`n",$atkcount,$defcount); 
		
	}
	//debug("Final Burn rating:");
	//debug($burn);
	
	$winnerscrap = array();
	foreach($destroyed as $bot => $vals){
		//basic components
		//axles
		$winnerscrap[0]+=2;
		//armour, now steel plate
		$winnerscrap[1]+=2;
		//chassis, now large and small girders
		$winnerscrap[2]+=1;
		$winnerscrap[8]+=2;
		//motherboard
		$winnerscrap[3]+=1;
		//ROM Chip
		$winnerscrap[4]+=1;
		//Wiring
		$winnerscrap[5]+=1;
		//Battery
		$winnerscrap[6]+=1;
		//wheels
		$winnerscrap[7]+=4;
		//camera eye, now cmos and lens
		$winnerscrap[9]+=1;
		$winnerscrap[10]+=1;
		//servo limb, now servo, small girder and steel plate
		$winnerscrap[11]+=1;
		$winnerscrap[8]+=1;
		$winnerscrap[1]+=1;
		//drive motor
		$winnerscrap[12]+=1;
		//now the level-specific stuff
		if ($vals['brains'] == 2){
			$winnerscrap[4]+=1;
		}
		if ($vals['brains'] == 3){
			$winnerscrap[4]+=2;
		}
		if ($vals['brains'] == 4){
			$winnerscrap[4]+=3;
		}
		if ($vals['brains'] == 5){
			$winnerscrap[4]+=4;
		}
		if ($vals['brains'] == 6){
			$winnerscrap[4]+=5;
		}
		if ($vals['brains'] == 7){
			$winnerscrap[4]+=7;
		}
		if ($vals['brains'] == 8){
			$winnerscrap[4]+=9;
		}
		if ($vals['brains'] == 9){
			$winnerscrap[4]+=11;
		}
		if ($vals['brains'] == 10){
			$winnerscrap[4]+=14;
		}
		if ($vals['brawn'] == 2){
			//armour
			$winnerscrap[1]+=2;
		}
		if ($vals['brawn'] == 3){
			//armour
			$winnerscrap[1]+=4;
			//servo
			$winnerscrap[8]+=1;
			$winnerscrap[11]+=1;
		}
		if ($vals['brawn'] == 4){
			//armour
			$winnerscrap[1]+=6;
			//servo
			$winnerscrap[8]+=2;
			$winnerscrap[11]+=2;
		}
		if ($vals['brawn'] == 5){
			//armour
			$winnerscrap[1]+=10;
			//servo
			$winnerscrap[8]+=2;
			$winnerscrap[11]+=2;
			//battery
			$winnerscrap[6]+=1;
		}
		if ($vals['brawn'] == 6){
			//armour
			$winnerscrap[1]+=16;
			//servo
			$winnerscrap[8]+=2;
			$winnerscrap[11]+=2;
			//battery
			$winnerscrap[6]+=1;
		}
		if ($vals['brawn'] == 7){
			//armour
			$winnerscrap[1]+=18;
			//servo
			$winnerscrap[8]+=3;
			$winnerscrap[11]+=3;
			//battery
			$winnerscrap[6]+=2;
		}
		if ($vals['brawn'] == 8){
			//armour
			$winnerscrap[1]+=22;
			//servo
			$winnerscrap[8]+=4;
			$winnerscrap[11]+=4;
			//battery
			$winnerscrap[6]+=3;
		}
		if ($vals['brawn'] == 9){
			//armour
			$winnerscrap[1]+=26;
			//servo
			$winnerscrap[8]+=3;
			$winnerscrap[11]+=3;
			//battery
			$winnerscrap[6]+=2;
		}
		if ($vals['brawn'] == 10){
			//armour
			$winnerscrap[1]+=34;
			//servo
			$winnerscrap[8]+=4;
			$winnerscrap[11]+=4;
			//battery
			$winnerscrap[6]+=3;
		}
		if ($vals['briskness'] == 2){
			//battery
			$winnerscrap[6]+=1;
		}
		if ($vals['briskness'] == 3){
			//battery
			$winnerscrap[6]+=2;
		}
		if ($vals['briskness'] == 4){
			//battery
			$winnerscrap[6]+=3;
			//drive motor
			$winnerscrap[12]+=1;
		}
		if ($vals['briskness'] == 5){
			//battery
			$winnerscrap[6]+=4;
			//drive motor
			$winnerscrap[12]+=2;
		}
		if ($vals['briskness'] == 6){
			//battery
			$winnerscrap[6]+=5;
			//drive motor
			$winnerscrap[12]+=3;
		}
		if ($vals['briskness'] == 7){
			//battery
			$winnerscrap[6]+=7;
			//drive motor
			$winnerscrap[12]+=4;
		}
		if ($vals['briskness'] == 8){
			//battery
			$winnerscrap[6]+=9;
			//drive motor
			$winnerscrap[12]+=5;
		}
		if ($vals['briskness'] == 9){
			//battery
			$winnerscrap[6]+=11;
			//drive motor
			$winnerscrap[12]+=7;
		}
		if ($vals['briskness'] == 10){
			//battery
			$winnerscrap[6]+=13;
			//drive motor
			$winnerscrap[12]+=9;
		}
	}
	
	//now destroy about a quarter of it at random
	foreach($winnerscrap as $part => $num){
		$num = ceil($num*(e_rand(5,10)/10));
		$winnerscrap[$part] = $num;
	}
	
	require_once("modules/scrapbots/lib.php");
	$playerscrap = scrapbots_get_player_scrap($winner);
	
	//Sort out the distribution of spare parts
	if ($winner == $attackerid){
		output("`c`bThe Spoils`b`c`n`n");
		output("The following parts are left lying all across the field.  Your surviving ScrapBots will bring them back to your workshop for you.`n");
		$scrapnames = scrapbots_scrapnames();
		foreach($scrapnames['rareitems'] AS $id => $item){
			$itemqty = $winnerscrap[$id];
			if ($itemqty > 0){
				output("%s: %s`n", $item, $itemqty);
			}
		}
		output("`n`n");
		foreach($winnerscrap as $part => $num){
			$playerscrap['data']['rareitems'][$part] += $num;
		}
		set_module_pref("scrap", serialize($playerscrap), "scrapbots");
	} else {
		foreach($winnerscrap as $part => $num){
			$playerscrap['data']['rareitems'][$part] += $num;
		}
		set_module_pref("scrap", serialize($playerscrap), "scrapbots", $winner);
	}
	
	//Now update the database
	//Delete bots that have been destroyed
	foreach($destroyed as $bot => $val){
		$id = $val['id'];
		$sql = "DELETE FROM ".db_prefix("scrapbots")." WHERE id = $id";
		db_query($sql);
	}

	//Restore bots that have fled the battlefield
	foreach($atkfled as $bot => $val){
		$attacker[] = $val;
	}
	foreach($deffled as $bot => $val){
		$defender[] = $val;
	}
	
	//Update owner and hitpoint details for all bots
	foreach($attacker as $bot => $val){
		//debug("updating atk db");
		//debug($val);
		$sql = "UPDATE ".db_prefix("scrapbots")." SET owner = ".$val['owner'].", hitpoints = ".$val['hitpoints']." WHERE id = ".$val['id'];
		//debug($sql);
		db_query($sql);
	}
	
	foreach($defender as $bot => $val){
		//output("Now updating defender db");
		$sql = "UPDATE ".db_prefix("scrapbots")." SET owner = ".$val['owner'].", hitpoints = ".$val['hitpoints']." WHERE id = ".$val['id'];
		//debug($sql);
		db_query($sql);
	}
}

?>