<?php

function titans_spawn_roll($spawnchance=false){
	$lastspawn = get_module_setting("lastroll","titans");
	$now = time();
	$nextspawn = $lastspawn + 60;
	$secsleft = $nextspawn-$now;
	//debug("Next Titan spawn roll in ".$secsleft." seconds.");
	if ($now > $nextspawn){
		set_module_setting("lastroll",$now,"titans");
		//spawn roll
		if ($spawnchance===false){
			$spawnchance = get_module_setting("spawnchance","titans");
		}
		$roll = e_rand(1,1000);
		debug("Rolling for spawn, need a ".$spawnchance." or less, rolled a ".$roll);
		if ($roll <= $spawnchance){
			titans_spawn_titan();
		}
	}
}

function titans_spawn_titan($x=false,$y=false){
	debug("Spawning Titan!");
	//we need to make a Titan appear on an edge of the World Map.
	$titan = array();
	//Get X and Y co-ordinates of a random location on the edge of the world map.
	if ($x===false){
		$x = e_rand(1,get_module_setting("worldmapsizeX","worldmapen"));
	}
	if ($y===false){
		$y = e_rand(1,get_module_setting("worldmapsizeY","worldmapen"));
	}
	$max = e_rand(0,1);
	$drop = e_rand(0,1);
	if ($drop){
		if ($max){
			$x = 1;
		} else {
			$x = get_module_setting("worldmapsizeX","worldmapen");
		}
	} else {
		if ($max){
			$y = 1;
		} else {
			$y = get_module_setting("worldmapsizeY","worldmapen");
		}
	}
	$titan['location']['x'] = $x;
	$titan['location']['y'] = $y;
	
	//Create a Titan's battle array.
	//Right now we'll just have the one sort of Titan, and we'll put in functionality for different types later.
	$titan['creaturename']="Titan";
	$titan['creatureweapon']="Stinging Limpets";
	$titan['creaturelevel']=20;
	$titan['creaturegold']=1000;
	$titan['creatureexp']=0;
	$titan['creaturehealth']=get_module_setting("titanhp","titans");
	$atk = e_rand(6,15);
	$def = e_rand(6,15);
	$titan['creatureattack']=$atk;
	$titan['creaturedefense']=$def;
	$titan['multiplayer']=1;
	$titan['spawnhp']=$titan['creaturehealth'];
	//for now, let's have a Titan that moves one square every two hours
	$titan['move_every'] = 7200; //7200 seconds = 2 hrs
	$now = time();
	$titan['lastmove'] = $now;
	
	//Set the Titan's destination - choose a random city, get its World Map values.
	$cities=array();
	$sql = "select * from ".db_prefix("cityprefs");
	$result=db_query($sql);
	$numcities = db_num_rows($result);
	for ($i = 0; $i < $numcities; $i++){
		$row = db_fetch_assoc($result);
		$cities[]=$row;
	}
	$targetkey = e_rand(0,($numcities-1));
	$titan['destination']['cityid']=$cities[$targetkey]['cityid'];
	$titan['destination']['cityname']=$cities[$targetkey]['cityname'];
	$titan['destination']['x']=get_module_setting($cities[$targetkey]['cityname']."X","worldmapen");
	$titan['destination']['y']=get_module_setting($cities[$targetkey]['cityname']."Y","worldmapen");
	
	//write the titan back to the database
	$battlelog = array();
	$battlelog['timeofbirth'] = $now;
	$battlelog = serialize($battlelog);
	$creature = serialize($titan);
	
	$sql = "INSERT INTO ".db_prefix("titans")." (creature,battlelog) VALUES ('$creature','$battlelog')";
	db_query($sql);
	debug($sql);
}

function titans_get_all_titans(){
	$sql = "SELECT * FROM ".db_prefix("titans");
	$result = db_query($sql);
	$ret = array();
	while ($row = db_fetch_assoc($result)){
		$titan=array();
		$titan['creature'] = unserialize($row['creature']);
		$titan['battlelog'] = unserialize($row['battlelog']);
		$ret[]=$titan;
	}
	return $ret;
}

function titans_check_move($titan){
	if (!is_array($titan)){
		$titan = titans_get_titan($titan);
	}
	if (!$titan['battlelog']['killed']){
		$now = time();
		$nextmove = $titan['creature']['lastmove']+$titan['creature']['move_every'];
		if ($now > $nextmove){
			$titan['creature']['lastmove']=$now;
			$titan = titans_move_titan($titan);
		}
	}
	return $titan;
}

function titans_move_titan($titan){
	//move a Titan one square towards its target
	if (!is_array($titan)){
		$titan = titans_get_titan($titan);
	}
	
	if ($titan['creature']['location']['x'] > $titan['creature']['destination']['x']) $titan['creature']['location']['x']--;
	if ($titan['creature']['location']['x'] < $titan['creature']['destination']['x']) $titan['creature']['location']['x']++;
	if ($titan['creature']['location']['y'] > $titan['creature']['destination']['y']) $titan['creature']['location']['y']--;
	if ($titan['creature']['location']['y'] < $titan['creature']['destination']['y']) $titan['creature']['location']['y']++;
	
	//todo: destroy village walls once reached
	if ($titan['creature']['location']['x'] == $titan['creature']['destination']['x'] && $titan['creature']['location']['y'] == $titan['creature']['destination']['y']){
		//destroy walls
		set_module_objpref("city",$titan['creature']['destination']['cityid'],"defences",0,"onslaught");
		debug("Destroying walls!");
	}
	titans_set_titan($titan);
	return $titan;
}

function titans_show_nearby_titans($xyz=false,$range=5){
	global $session;
	if (!$xyz){
		$xyz = get_module_pref("worldXYZ","worldmapen");
	}
	list($px, $py, $pz) = explode(",", $xyz);
	$sql = "SELECT * FROM ".db_prefix("titans");
	$result = db_query($sql);
	$nearby = array();
	while ($row = db_fetch_assoc($result)){
		$titan = array();
		$titan['creature'] = unserialize($row['creature']);
		$titan['battlelog'] = unserialize($row['battlelog']);
		
		$titan = titans_check_move($titan);
		
		$tx = $titan['creature']['location']['x'];
		$ty = $titan['creature']['location']['y'];
		
		$stop=0;
		//debug($tx.",".$ty);
		if ($tx > $px+$range || $tx < $px-$range || $ty > $py+$range || $ty < $py-$range || $titan['battlelog']['killed']){
			$stop=1;
		}
		
		if (!$stop){
			if ($tx!=$px || $ty!=$py){
				$msg = "`0The ground shakes with bass-heavy rumbles as a mighty `bTitan`b lumbers around somewhere to the ";
				if ($ty < $py){
					$msg.="south";
				} else if ($ty > $py){
					$msg.="north";
				}
				if ($tx!=$px && $ty!=$py){
					$msg.="-";
				}
				if ($tx < $px){
					$msg.="west";
				} else if ($tx > $px){
					$msg.="east";
				}
				$msg.=".  It appears to be heading towards ".$titan['creature']['destination']['cityname']."!";
				output_notl("%s`n`n",$msg);
			} else {
				//this Titan is right on top of the player!
				output("`0A mighty Titan towers above you.  Craning your neck back and straining your eyes you can see tiny white specks on its underbelly - they're barnacles, each one as big as your hand.  The shock wave of every stupid, lumbering step threatens to throw you off-balance.  Enormous deep red limpets cling to its feet, each one equipped with poisonous stingers.  They don't bother you - not having feet, they can't leap from the Titan and chase you.  They stick themselves to the Titan so that once it begins its journey to the Outpost to which it is (unfathomably) attracted, they can eat any foolish warriors who try to stop the Titan without the aid of close allies.  If this Titan reaches %s, then all hope for that Outpost is lost - the walls will come down at the Titan's first blow.`n`n",$titan['creature']['destination']['cityname']);
				//is anybody fighting this Titan now?
				$combatants = $titan['battlelog']['combatants'];
				$numcombatants = 0;
				if (count($combatants)>0){
					foreach($combatants AS $key=>$vals){
						if ($vals['active']) $numcombatants++;
					}
					if ($numcombatants>1){
						output("You can see some fellow contestants hacking away at the Titan's feet and the armoured limpets that cling to them.  You can join in, if that seems like a good idea to you - or, you can let the Titan continue on its Outpost-destroying path.  Few people could blame you either way.`n`n");
					}
				}
				//add navigation
				addnav("Feeling like a hero?");
				addnav("Battle the Titan!","runmodule.php?module=titans&titanop=beginbattle&titanid=".$row['id']);
			}
		}
	}
}

function titans_set_titan($titan){
	//Update a titan
	//debug($titan);
	$titanid = $titan['creature']['titanid'];
	$creature = serialize($titan['creature']);
	$battlelog = serialize($titan['battlelog']);
	//debug($battlelog);
	$sql = "UPDATE ".db_prefix("titans")." SET creature='$creature', battlelog='$battlelog' WHERE id='$titanid'";
	db_query($sql);
}

function titans_get_titan($titanid){
	$sql = "SELECT id,creature,battlelog FROM " . db_prefix("titans") . " WHERE id = '$titanid'";
	$result = db_query($sql);
	$row=db_fetch_assoc($result);
	if (!$row['creature']){
		return false;
	}
	$row['creature'] = unserialize($row['creature']);
	$row['battlelog'] = unserialize($row['battlelog']);
	if (!isset($row['battlelog']['combatants'])){
		$row['battlelog']['combatants']=array();
	}
	$row['creature']['titanid']=$row['id'];
	return $row;
}

function titans_load_battle($id){
	global $session,$badguy,$battle;
	$titan = titans_get_titan($id);
	if ($titan && !$titan['battlelog']['killed']){
		restore_buff_fields();
		$creature = $titan['creature'];
		$creature['titaninfo']['badguy']['hpstart'] = $creature['creaturehealth'];
		$creature['titaninfo']['player']['hpstart'] = $session['user']['hitpoints'];
		$badguy = $creature;
		calculate_buff_fields();
		$session['user']['badguy']=createstring($badguy);
		$battle = true;
		return($titan);
	} else {
		redirect("runmodule.php?module=titans&titanop=battleover");
		return false;
	}
}

function titans_save_battle($titan){
	global $session,$badguy,$battle;
	$titan['creature'] = $badguy;
	$titan['battlelog'] = titans_update_log($titan);
	titans_set_titan($titan);
}

function titans_leave_battle($titan,$acctid=false){
	global $session;
	if (!$acctid) $acctid = $session['user']['acctid'];
	$titan['battlelog']['combatants'][$acctid]['active']=0;
	$newlog = array(
		"pid"=>$session['user']['acctid'],
		"left"=>1,
	);
	$titan['battlelog'][]=$newlog;
	titans_set_titan($titan);
}

function titans_leave_battle_ko($titan,$acctid=false){
	global $session;
	if (!$acctid) $acctid = $session['user']['acctid'];
	$titan['battlelog']['combatants'][$acctid]['active']=0;
	$newlog = array(
		"pid"=>$session['user']['acctid'],
		"died"=>1,
	);
	$titan['battlelog'][]=$newlog;
	titans_set_titan($titan);
}

function titans_show_comrades($titan){
	global $session;
	$battlelog = $titan['battlelog'];
	if (count($battlelog['combatants'])>1){
		require_once "modules/combatbars.php";
		foreach($battlelog['combatants'] AS $acctid=>$vals){
			if ($acctid!=$session['user']['acctid'] && $vals['active']){
				combatbars_showbar($vals['hp'],$vals['maxhp'],"`2Comrade: `0".$vals['name']);
			}
		}
	}
}

function titans_update_log($titan){
	global $session;
	$creature = $titan['creature'];
	$battlelog = $titan['battlelog'];
	$titandamage = $creature['titaninfo']['badguy']['hpstart'] - $creature['creaturehealth'];
	$playerdamage = $creature['titaninfo']['player']['hpstart'] - $session['user']['hitpoints'];
	$newlog = array(
		"tdmg"=>$titandamage,
		"pdmg"=>$playerdamage,
		"thp"=>$creature['creaturehealth'],
		"php"=>$session['user']['hitpoints'],
		"pid"=>$session['user']['acctid'],
	);
	if (isset($battlelog['combatants'][$session['user']['acctid']]['dks']) && $battlelog['combatants'][$session['user']['acctid']]['dks'] < $session['user']['dragonkills']){
		unset($battlelog['combatants'][$session['user']['acctid']]);
	} else {
		$battlelog['combatants'][$session['user']['acctid']]['active']=1;
		$battlelog['combatants'][$session['user']['acctid']]['hp']=$session['user']['hitpoints'];
		$battlelog['combatants'][$session['user']['acctid']]['maxhp']=$session['user']['maxhitpoints'];
		$battlelog['combatants'][$session['user']['acctid']]['name']=$session['user']['name'];
		$battlelog['combatants'][$session['user']['acctid']]['dmg']+=$titandamage;
		$battlelog['combatants'][$session['user']['acctid']]['dks']=$session['user']['dragonkills'];
	}
	if ($battlelog['combatants'][$session['user']['acctid']]['hp'] <= 0){
		$newlog["died"]=1;
	}
	$battlelog['log'][]=$newlog;
	//remove log entries beyond 50
	if (count($battlelog['log']) > 50){
		$first = reset(array_keys($battlelog['log']));
		unset($battlelog['log'][$first]);
	}
	$battlelog['combatants'][$session['user']['acctid']]['lastlog']=end(array_keys($battlelog['log']));
	return ($battlelog);
}

function titans_show_log($titan){
	global $session;
	$battlelog = $titan['battlelog'];
	$lastlog = $battlelog['combatants'][$session['user']['acctid']]['lastlog'];
	if (count($battlelog)>1){
		$end = @end(@array_keys($battlelog['log']));
		if ($lastlog < $end){
			output("`0`bBattle Log:`b`n");
			for ($i=$lastlog+1; $i<=$end; $i++){
				$id = $battlelog['log'][$i]['pid'];
				$name = $battlelog['combatants'][$id]['name'];
				if ($name){
					if ($battlelog['log'][$i]['left']){
						output("`0%s`0 left the battle`n",$name);
					} else if ($battlelog['log'][$i]['died']){
						output("`0%s`0 dealt `2%s`0 damage, and got knocked out!`n",$name,$battlelog['log'][$i]['tdmg']);
					} else if ($battlelog['log'][$i]['pdmg'] > 0){
						output("`0%s`0 dealt `2%s`0 damage, and took `4%s`0 damage.`n",$name,$battlelog['log'][$i]['tdmg'],$battlelog['log'][$i]['pdmg']);
					} else if ($battlelog['log'][$i]['pdmg'] < 0){
						output("`0%s`0 dealt `2%s`0 damage, and healed for `4%s`0 damage.`n",$name,$battlelog['log'][$i]['tdmg'],0-$battlelog['log'][$i]['pdmg']);
					} else {
						output("`0%s`0 dealt `2%s`0 damage`n",$name,$battlelog['log'][$i]['tdmg']);
					}
				}
			}
			output("`n");
		}
	}
}

function titans_kill_titan($titan){
	global $session;
	//debug("Killing Titan!");
	//debug($titan);
	if (!$titan['battlelog']['killed']){
		//debug("Titan is not dead, let's fix that!");
		$titan['battlelog']['killingblow_name']=$session['user']['name'];
		$titan['battlelog']['killingblow_acctid']=$session['user']['acctid'];
		$titan['battlelog']['killed']=1;
		$now = time();
		$titan['battlelog']['timeofdeath']=$now;
		//Save that the Titan is dead, in the hopes that it will solve some issues with doubled-up req payouts as well as killing blow payouts. -HgB
		titans_save_battle($titan);
		
		//determine lifetime, update averages, alter maximum Titan hitpoints if necessary
		$lifetime = $now - $titan['battlelog']['timeofbirth'];
		$desiredlifetime = get_module_setting("desiredlifetime","titans");
		$curhp = get_module_setting("titanhp","titans");
		$curspawnchance = get_module_setting("spawnchance","titans");
		$curtotaltitans = get_module_setting("totaltitans","titans");
		$curtotaltime = get_module_setting("totallifetime","titans");
		$newtotaltitans = $curtotaltitans+1;
		$newtotaltime = $curtotaltime+$lifetime;
		
		$avglifetime = $newtotaltime/$newtotaltitans;
		
		//allow a large deviation in the current data before immediately changing
		if ($lifetime < $desiredlifetime*0.5){
			$newhp = $curhp*1.05;
		} else if ($lifetime > $desiredlifetime*2){
			$newhp = $curhp*0.95;
		} else {
			$newhp = $curhp;
		}
		
		//allow a small deviation in the historical data before changing
		if ($lifetime < $desiredlifetime*0.95){
			$newhp = round(ceil($curhp*1.05),-2);
			increment_module_setting("spawnchance",1,"titans");
		} else if ($lifetime > $desiredlifetime*1.05){
			$newhp = round(floor($curhp*0.95),-2);
			if ($curspawnchance > 1){
				increment_module_setting("spawnchance",-1,"titans");
			}
		}
		if ($newhp>1000000){
			set_module_setting("titanhp",$newhp,"titans");
		}
		set_module_setting("totaltitans",$newtotaltitans,"titans");
		set_module_setting("totallifetime",$newtotaltime,"titans");

		titans_award_req($titan);
		titans_set_titan($titan);
	}
	output("`0The Titan falls to the earth with a mighty crash, and lies still.  All combatants are immediately issued a Requisition reward to their bank accounts.  Offshore, `4The Watcher`0 smiles.`n`n");
	addnav("Crisis averted!");
	addnav("Back to the Map","runmodule.php?module=worldmapen&op=continue");
	return $titan;
}

function titans_sweep_dead_titans(){
	$sql = "SELECT * FROM ".db_prefix("titans");
	$result = db_query($sql);
	while ($row = db_fetch_assoc($result)){
		$log = unserialize($row['battlelog']);
		if ($log['killed']){
			//delete this Titan
			$delsql = "DELETE FROM ".db_prefix("titans")." WHERE id=".$row['id'];
			db_query($delsql);
		}
	}
	return true;
}

//awards Requisition to players who helped to slay the Titan
function titans_award_req($titan){
	global $session;
	debug($titan);
	require_once("lib/systemmail.php");
	$treq = 100000; //adjust this up or down - this is the Req that will be distributed out to players accordingly
	$kbreq = 1000; //Bonus for scoring the killing blow against a Titan
	$players = $titan['battlelog']['combatants'];
	$reqperpoint = $treq/$titan['creature']['spawnhp']; //Use the hitpoints that the Titan originally spawned with
	foreach ($players AS $key=>$vals){
		//figure out how much to pay them
		$pay = round($vals['dmg']*$reqperpoint);
		if ($key == $titan['battlelog']['killingblow_acctid']){
			$pay+=$kbreq;
			$msg = "`0As thanks for your heroic efforts in bringing down the Titan, `4The Watcher`0 has awarded you with ".number_format($pay)." Requisition tokens, deposited straight into your bank account.  This includes a bonus of 1,000 Requisition tokens for dealing the killing blow.  Have fun with those!";
			debug($key." is the same as ".$titan['battlelog']['killingblow_acctid'].", adding extra Req");
		} else {
			//now mail them to let them know
			$msg = "`0As thanks for your heroic efforts in bringing down the Titan, `4The Watcher`0 has awarded you with ".number_format($pay)." Requisition tokens, deposited straight into your bank account.  Have fun with those!  ".$titan['battlelog']['killingblow_name']."`0 scored the killing blow, and gets an additional 1,000 Requisition tokens.  Maybe next time it'll be you!";
		}
		
		$sql = "UPDATE ".db_prefix("accounts")." SET goldinbank=goldinbank+$pay WHERE acctid=$key";
		db_query($sql);
		debug("Adding ".$pay." gold to acctid ".$key);
		systemmail($key,"Titan Rewards",$msg);
	}
	return($titan);
}

?>