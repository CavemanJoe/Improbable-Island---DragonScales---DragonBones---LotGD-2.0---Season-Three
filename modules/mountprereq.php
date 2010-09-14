<?php

function mountprereq_getmoduleinfo(){
	$info=array(
		"name"=>"Mount Prerequisites and Bestiary",
		"version"=>"1.01",
		"author"=>"`#Iori`0, adapted from Mount Rarity by`\$ Red Yates`0. With additions by `4Thanatos and Aelia`0.",
		"category"=>"Mounts",
		"download"=>"here",
		"settings"=>array(
			"Bestiary - Settings,title",
			"boolview"=>"Enable the Bestiary in the Stable?,bool|1",
			"viewactiveonly"=>"Only allow activated mounts to be shown in the bestiary?,bool|1",
			"boolmountrarity"=>"Display rarity of mounts if Mount Rarity module is installed and mount is not available all the time?,bool|1",
			"boolmountupgrade"=>"Displays what the mount upgrades to if Mount Upgrade module is installed and if applicable?,bool|1",
			"boolshowcost"=>"Displays cost of mount in the Bestiary?,bool|1",
			"Stables Mount-Buff Description - Settings,title",
			"togglename"=>"Enable mount buff name description for mounts being sold at Stables?,bool|1",
			"toggleround"=>"Enable mount buff rounds description?,bool|1",
			"toggleatkmod"=>"Enable mount buff attack bonus description?,bool|1",
			"toggledefmod"=>"Enable mount buff defense bonus description?,bool|1",
			"toggleregen"=>"Enable mount buff regen description?,bool|1",
			"toggledmgshld"=>"Enable mount buff damadgeshield description?,bool|1",
			"togglelifetap"=>"Enable mount buff lifetap description?,bool|1",
			"toggleminioncount"=>"Enable mount buff minioncount description?,bool|1",
			"togglemaxbadguydamage"=>"Enable mount buff max badguy damage description?,bool|0",
			"togglebadguyatkmod"=>"Enable mount buff badguy attack bonus description?,bool|0",
			"togglebadguydefmod"=>"Enable mount buff badguy defense bonus description?,bool|0",
			"togglebadguydmgmod"=>"Enable mount buff badguy damage bonus description?,bool|0",
			"toggleinv"=>"Enable mount buff invulnerablility description if true?,bool|0",
			"toggleffs"=>"Enable mount new day turns bonus description?,bool|1",	
			"toggleupgradeview"=>"Enable viewing of upgraded mount?,bool|1",	
			"toggleupgradedks"=>"Enable mount upgrade dk req description?,bool|1",	
			"toggleupgradelevels"=>"Enable mount upgrade level req description?,bool|1",	
			"toggleupgradedays"=>"Enable mount upgrade day req description?,bool|1",	
			"toggleupgradeffs"=>"Enable mount upgrade forest fight req description?,bool|1",	
		),
		"prefs"=>array(
			"viewed"=>"Has the user ever viewed the bestiary?,bool|0",
			"vieweddk"=>"Has the user viewed the bestiary this DK?,bool|0",
		),
		"prefs-mounts"=>array(
			"Mount Purchase Alignment Prerequisites,title",
			"boolalign"=>"Alignment condition for availability,enum,0,Ignore alignment,1,Below Low alignment,2,Between Low and High,3,Above High allignment|0",
			"alignlo"=>"`\$Low Alignment,int|0",
			"alignhi"=>"`2High Alignment,int|0",
			"Mount Purchase Favor Prerequisites,title",
			"favorreq"=>"Required favor,int|0",
			"favorcost"=>"Cost of favor for mount purchase,int|0",
			"Mount will not be available if either of the above conditions is not met.,note",
			"Mount Purchase Charm Prerequisites,title",
			"charmreq"=>"`&Required Charm,int|0",
			"Mount Purchase Donation Points Prerequisites,title",
			"donationreq"=>"Required available Donation Points,int|0",
			"donationcost"=>"Cost of Donation Points for mount purchase,int|0",
			"Mount will not be available if either of the above conditions is not met.,note",
			"Mount Purchase Race Prerequisites,title",
			"racereq"=>"Required Race,text|",
			"Mount Purchase Gender Prerequisites,title",
			"sexreq"=>"Required player gender,enum,0,male,1,female,2,ignore|2",
			"Bestiary Mount Preferences,title",
			"boolshow"=>"The page for this mount appears in the bestiary if player has enough DKs?,bool|1",
			"boolshowupgrade"=>"The page for this mount's upgrade appears in the bestiary?,bool|1",
			"outputtext"=>"Description or further infomation you would like to have dispalyed for this mount in the bestiary,text|",
			"Bestiary Mount-Buff Description - Manual Display,title",
			"Configuring these prefs overrides the default description generated from the mount buff as well as view or hide toggle settings for the different fields,note",
			"It is recommended that these remain blank unless you know what you are doing!,note",
			"buffname"=>"Name of Mount Buff displayed,|",
			"buffrounds"=>"Total buff rounds displayed,|",
			"buffatkmod"=>"Attack Modifier displayed,|",
			"buffdefmod"=>"Defense Modifier displayed,|",
			"buffregen"=>"Regen displayed,|",
			"buffdamagshield"=>"Damageshield displayed,|",
			"bufflifetap"=>"Lifetap displayed,|",
			"buffminioncount"=>"Minioncount displayed,|",
			"buffmaxbadguydamage"=>"Max badguy damage displayed,|",
			"buffbadguyatkmod"=>"Badguy Attack Modifier displayed,|",
			"buffbadguydefmod"=>"Badguy Defense Modifier displayed,|",
			"buffbadguydmgmod"=>"Badguy Damage Modifier displayed,|",
			"buffinv"=>"Invulnerability displayed,|",
			"ffs"=>"Extra Forest Fights displayed,|",
			"upgradedks"=>"Upgrade DK req displayed,|",
			"upgradelevels"=>"Upgrade level req displayed,|",
			"upgradedays"=>"Upgrade day req displayed,|",
			"upgradeffs"=>"Upgrade forest fight req displayed,|",
		),
	);
	return $info;
}

function mountprereq_install(){
	module_addhook("stables-nav");
	module_addhook("boughtmount");
	module_addhook("dragonkill");
	return true;
}

function mountprereq_uninstall(){
	return true;
}

function mountprereq_dohook($hookname, $args){
	global $session;
	global $playermount;
	switch($hookname){
	case "boughtmount":
		$id = $session['user']['hashorse'];
		if ($id > 0) {
			$favorcost = get_module_objpref("mounts",$id,"favorcost");
			$donationcost = get_module_objpref("mounts",$id,"donationcost");
			$session['user']['deathpower'] -= $favorcost;
			$session['user']['donationspent'] += $donationcost;
			if ($favorcost != 0) {
				$claim = ($favorcost > 0 ? "claims" : "rewards");
				$from = ($favorcost > 0 ? "from" : "to");
				if ($favorcost < 0) {
					$favorcostpos = -($favorcost);
				} else {
					$favorcostpos = $favorcost;
				}
				output("`n`\$Ramius`6 makes a brief appearance, and %s`\$ %s favor`6 %s you.`n",$claim,$favorcostpos,$from);
			}
			if ($donationcost != 0) {
				output("`n`6A `&representative`6 from the Lodge walks in from the door, and deducts`7 %s points`6 from your ledger.",$donationcost);
			}
		}
		break;

	case "dragonkill":
		set_module_pref('vieweddk',0);
		break;				

	case "stables-nav":
		$sql="SELECT mountid FROM ".db_prefix("mounts")." WHERE mountactive=1";
		$result=db_query($sql);
		while($row=db_fetch_assoc($result)) {
			$id=$row['mountid'];
			if (is_module_active('alignment') && get_module_objpref("mounts",$id,"boolalign") != 0) {
				$align=get_module_pref('alignment','alignment');
				$alignlo=get_module_objpref("mounts",$id,"alignlo");
				$alignhi=get_module_objpref("mounts",$id,"alignlo");
				if (get_module_objpref("mounts",$id,"boolalign") == 1 && $align > $alignlo) {
					blocknav("stables.php?op=examine&id=$id");
				} elseif (get_module_objpref("mounts",$id,"boolalign") == 2 && $align < $alignlo || $align > $alignhi) {
					blocknav("stables.php?op=examine&id=$id");
				} elseif (get_module_objpref("mounts",$id,"boolalign") == 3 && $align < $alignhi) {
					blocknav("stables.php?op=examine&id=$id");
				}
			}
			$favorreq = get_module_objpref("mounts",$id,"favorreq");
			$favorcost = get_module_objpref("mounts",$id,"favorcost");
			$favor = $session['user']['deathpower'];
			if ($favor < $favorreq || $favor < $favorcost) {
				blocknav("stables.php?op=examine&id=$id");
			}
			$charm = $session['user']['charm'];
			$charmreq = get_module_objpref("mounts",$id,"charmreq");
			if ($charm < $charmreq) {
				blocknav("stables.php?op=examine&id=$id");
			}
			$donationreq = get_module_objpref("mounts",$id,"donationreq");
			$donationcost = get_module_objpref("mounts",$id,"donationcost");
			$donationavail = $session['user']['donation'] - $session['user']['donationspent'];
			if ($donationavail < $donationreq || $donationavail < $donationcost) {
				blocknav("stables.php?op=examine&id=$id");
			}
			$racereq = get_module_objpref("mounts",$id,"racereq");
			if ($racereq != "" && $session['user']['race'] != $racereq) {
				blocknav("stables.php?op=examine&id=$id");
			}
			$sexreq = get_module_objpref("mounts",$id,"sexreq");
			if ($sexreq != 2 && $session['user']['sex'] != $sexreq) {
				blocknav("stables.php?op=examine&id=$id");
			}
		}
		if (get_module_setting('boolview') == 1) {
			addnav("Other");
			addnav("Bestiary","runmodule.php?module=mountprereq&op=bestiary");
		}
		break;
	}
	return $args;
}

function mountprereq_run() {
      global $session;
	$op = httpget('op');
	$id=httpget("id");

	if ($op=="bestiary") {
		page_header("Bestiary");
		if (get_module_setting('viewactiveonly') == 1) {
			$sql = "SELECT mountname,mountid,mountdkcost FROM " . db_prefix("mounts") .  " WHERE mountactive=1 ORDER BY mountdkcost,mountcostgems,mountcostgold";
		} else {
			$sql = "SELECT mountname,mountid,mountdkcost FROM " . db_prefix("mounts") .  " ORDER BY mountdkcost,mountcostgems,mountcostgold";
		}
		$result = db_query($sql);
		$end = "";
		if (get_module_pref('viewed') == 0) {
			$wonder = "You briefly wonder why you never noticed this here before.";
			set_module_pref('viewed',1);
			set_module_pref('vieweddk',1);
		} elseif (get_module_pref('vieweddk') == 0) {
			$wonder = "It seems to have changed somewhat since you last saw it.";
			$end = "Feeling a little more confident since the last time you were here, you also decide to look up on some of the entries that you previously ignored.";
			set_module_pref('vieweddk',1);
		} else {
			$wonder = "It does not seem to have changed since you last saw it.";
			$end = "`nAs the list descends down the page, more and more fantasic creatures make their appearance. Feeling a little overwhelmed, you decide to ignore the entries of the beasts that seem out of your league."; 
		}
		output("You discover a dusty tome left on a stool. %s `nGingerly, you prise open the heavy cover, and discover a Table of Contents page. How helpful!`n%s",$wonder,$end);
		addnav("View the entry on...");
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			if ($row['mountdkcost'] <= $session['user']['dragonkills'] && get_module_objpref("mounts",$row['mountid'],"boolshow") != 0) {
				addnav(array("View %s`0",$row['mountname']),"runmodule.php?module=mountprereq&op=browse&id={$row['mountid']}");
			}
		}
		addnav("Close the book");
		addnav("Return from whence you came","stables.php");
	}
	if ($op=="browse") {
		page_header("Bestiary");
		$sql = "SELECT * FROM " . db_prefix("mounts") . " WHERE mountid='$id'";
		$result = db_query_cached($sql, "mountdata-$id", 3600);
		if (db_num_rows($result)<=0){
			output("You find that the book is actually full of empty pages!");
		} else {
			$mount = db_fetch_assoc($result);
			$buff=unserialize($mount['mountbuff']);
			if (get_module_setting('boolshowcost') == 1) {
				output("`c`&-=%s`&=-`7`c`n`n",$mount['mountname']);
				output("`b`c`#Costs`b`c`7");
				$goldcost = "";
				$gemcost = "";
				$donationcost = "";
				$favorcost = "";
				$upgrade = "";
				if (get_module_objpref("mounts",$id,"favorcost") != 0) {
					$favorcost = '`n'. get_module_objpref("mounts",$id,"favorcost"). ' `$Favor`7';
				}
				if (get_module_objpref("mounts",$id,"donationcost") != 0) {
					$donationcost = '`n'. get_module_objpref("mounts",$id,"donationcost"). ' Donation Point(s)';
				}
				if ($mount['mountcostgold'] != "" && $mount['mountcostgold'] != 0) {
					$goldcost = '`n'. $mount['mountcostgold']. ' `^Gold`7';
				}
				if ($mount['mountcostgems'] != "" && $mount['mountcostgems'] != 0) {
					$gemcost = '`n'. $mount['mountcostgems']. ' `%Gem(s)`7';
				}
				if ($mount['mountfeedcost'] != "" && $mount['mountfeedcost'] != 0 && $buff['rounds'] > 0) {
					$cost = $mount['mountfeedcost']*$session['user']['level'];
					$feedcost = '`nFeeding costs '. $cost. ' `6Gold`7';
				}
				output("`c$goldcost $gemcost $favorcost $donationcost $feedcost`c`n");
			}
			if (get_module_objpref("mounts",$id,"buffatkmod") == "") {
				if ($buff['atkmod'] != "" && get_module_setting('toggleatkmod') == 1) {
					$var = mountprereq_calc($buff['atkmod']);
					$atkmod = round($var*100-100,2);
					if ($atkmod != 0) {
						if ($atkmod > 0) {
							$sign1 = '+';
						}
						$atk = '`n'. $sign1. $atkmod. '% attack';
					}
				}
			} else {
				$atk = "`nAttack Modifier: ". get_module_objpref("mounts",$id,"buffatkmod");
			}
			if (get_module_objpref("mounts",$id,"buffdefmod") == "") {
				if ($buff['defmod'] != "" && get_module_setting('toggledefmod') == 1) {
					$var0 = mountprereq_calc($buff['defmod']);
					$defmod = round($var0*100-100,2);
					if ($defmod != 0) {
						if ($defmod > 0) {
							$sign2 = '+';
						}
						$def = '`n'. $sign2. $defmod. '% defense';
					}
				}
			} else {
				$def = "`nDefense Modifier: ". get_module_objpref("mounts",$id,"buffdefmod");
			}
			if (get_module_objpref("mounts",$id,"buffregen") == "") {
				if ($buff['regen'] != "" && get_module_setting('toggleregen') == 1) {
					$var1 = round(mountprereq_calc($buff['regen']),0);
					if ($var1 != 0) {
						if ($var1 > 0) {
							$sign3 = '+';
						}
						$regen = '`n'. $sign3. $var1. 'HP regeneration per round';
					}
				}
			} else {
				$regen = "`nRegen: ". get_module_objpref("mounts",$id,"buffregen");
			}
			if (get_module_objpref("mounts",$id,"buffdamageshield") == "") {
				if ($buff['damageshield'] != "" && get_module_setting('toggledamageshield') == 1) {
					$var2 = mountprereq_calc($buff['damageshield']);
					$damageshieldmod = round($var2*100,2);
					if ($damageshieldmod != 0) {
						if ($damageshieldmod >= 0) {
							$sign4 = '+';
						}
						$damageshield = '`n'. $sign4. $damageshieldmod. '% damage shield';
					}
				}
			} else {
				$damageshield = "`nDamageshield: ". get_module_objpref("mounts",$id,"buffdamageshield");
			}
			if (get_module_objpref("mounts",$id,"bufflifetap") == "") {
				if ($buff['lifetap'] != "" && get_module_setting('togglelifetap') == 1) {
					$var3 = mountprereq_calc($buff['lifetap']);
					$lifetapmod = round($var3*100,2);
					if ($lifetapmod != 0) {
						if ($lifetapmod >= 0) {
							$sign5 = '+';
						}
						$lifetap = '`n'. $sign5. $lifetapmod. '% HP leech';
					}
				}
			} else {
				$lifetap = "`nLifetap: ". get_module_objpref("mounts",$id,"bufflifetap");
			}
			if (get_module_objpref("mounts",$id,"buffminioncount") == "") {
				if ($buff['minioncount'] != "" && get_module_setting('toggleminioncount') == 1) {
					$var4 = round(mountprereq_calc($buff['minioncount']),0);
					if ($var4 != 0) {
						$minioncount = '`n'. $var4. ' attack(s) maximum per round';
					}
				}
			} else {
				$minioncount = "`nMaximum number of Attacks per round: ". get_module_objpref("mounts",$id,"buffminioncount");
			}
			if (get_module_objpref("mounts",$id,"buffmaxbadguydamage") == "") {
				if ($buff['maxbadguydamage'] != "" && get_module_setting('togglemaxbadguydamage') == 1) {
					$var5 = round(mountprereq_calc($buff['maxbadguydamage']),0);
					if ($var5 != 0) {
						$maxbadguydamage = '`n'. $var5. ' maximum damage per attack';
					}
				}
			} else {
				$maxbadguydamage = "`nMaximum damage per Attack per round: ". get_module_objpref("mounts",$id,"buffmaxbadguydamage");
			}
			if (get_module_objpref("mounts",$id,"buffbadguyatkmod") == "") {
				if ($buff['badguyatkmod'] != "" && get_module_setting('togglebadguyatkmod') == 1) {
					$var6 = mountprereq_calc($buff['badguyatkmod']);
					$badguyatk = round($var6*100-100,2);
					if ($badguyatk != 0) {
						if ($badguyatk >= 0) {
							$sign6 = '+';
						}
						$badguyatkmod = '`n'. $sign6. $badguyatk. '% enemy attack';
					}
				}
			} else {
				$badguyatkmod = "`nEnemy Attack Modifier: ". get_module_objpref("mounts",$id,"buffbadguyatkmod");
			}
			if (get_module_objpref("mounts",$id,"buffbadguydefmod") == "") {
				if ($buff['badguydefmod'] != "" && get_module_setting('togglebadguydefmod') == 1) {
					$var7 = mountprereq_calc($buff['badguydefmod']);
					$badguydef = round($var7*100-100,2);
					if ($badguydef != 0) {
						if ($badguydef >= 0) {
							$sign7 = '+';
						}
						$badguydefmod = '`n'. $sign7. $badguydef. '% enemy defense';
					}
				}
			} else {
				$badguydefmod = "`nEnemy Defense Modifier: ". get_module_objpref("mounts",$id,"buffbadguydefmod");
			}
			if (get_module_objpref("mounts",$id,"buffbadguydmgmod") == "") {
				if ($buff['badguydmgmod'] != "" && get_module_setting('togglebadguydmgmod') == 1) {
					$var8 = mountprereq_calc($buff['badguydmgmod']);
					$badguydmg = round($var8*100-100,2);
					if ($badguydmg != 0) {
						if ($badguydmg >= 0) {
							$sign8 = '+';
						}
						$badguydmgmod ='`n'. $sign8. $badguydmg. '% enemy damage modifier';
					}
				}
			} else {
				$badguydmgmod = "`nEnemy Damage Modifier: ". get_module_objpref("mounts",$id,"badguydmgmod");
			}
			if (get_module_objpref("mounts",$id,"buffinv") == "") {
				if ($buff['invulnerable'] != "" && get_module_setting('toggleinv') == 1) {
					$var9 = round(mountprereq_calc($buff['invulnerable']),0);
					if ($var9 == 1) {
						$var9 = "INVULNERABLE!!";
						$inv = '`n'. $var9;
					}
				}
			} else {
				$inv = "`nInvulnerability: ". get_module_objpref("mounts",$id,"buffinv");
			}
			if (get_module_objpref("mounts",$id,"ffs") == "") {
				if ($mount['mountforestfights'] != "" && $mount['mountforestfights'] != 0 && get_module_setting('toggleffs') == 1) {
					$ffs = '`n'. $mount[mountforestfights]. ' extra turn(s) per day';
				}
			} else {
				$ffs = "`nExtra Forest Fights each day: ". get_module_objpref("mounts",$id,"ffs");
			}
			if (get_module_objpref("mounts",$id,"mountspd","speed") < 0) {
				$spd = '`n'. abs(get_module_objpref("mounts",$id,"mountspd","speed")). ' Agility penalty';
			} elseif (get_module_objpref("mounts",$id,"mountspd","speed") > 0) {
				$spd = '`n'. get_module_objpref("mounts",$id,"mountspd","speed"). ' Agility bonus';
			} else {
				$spd = '';
			}
			if (get_module_objpref("mounts",$id,"buffrounds") == "") {
				if ($buff['rounds'] != "" && $buff['rounds'] != 0 && get_module_setting('toggleround') == 1) {
					$round = $buff['rounds'];
					if($round < 0) $round = 'Permanent';
					$rounds = '`n'.$round. ' Rounds';
				}
			} else {
				$rounds = "`nRounds: ". get_module_objpref("mounts",$id,"buffrounds");
			}
			if (get_module_objpref("mounts",$id,"buffname") == "") {
				if ($buff['name'] != "" && get_module_setting('togglename') == 1) {
					$name = $buff['name'];
				}
			} else {
				$name = get_module_objpref("mounts",$id,"buffname");
			}
			if (is_module_active('mountupgrade') && get_module_setting('boolmountupgrade') == 1 && get_module_objpref("mounts",$id,"upgradeto","mountupgrade") > 0) {
				$upgradeto = get_module_objpref("mounts",$id,"upgradeto","mountupgrade");
				$sql = "SELECT mountname FROM " . db_prefix("mounts") . " WHERE mountid='$upgradeto'";
				$result = db_query($sql);
				$upgrademount = db_fetch_assoc($result);
				$upgrade = '`n`nupgrades to '. $upgrademount['mountname']. '`7';
				if (get_module_objpref("mounts",$id,"upgradedks") == "") {
					if (get_module_objpref("mounts",$id,"reqdks","mountupgrade") != "" && get_module_objpref("mounts",$id,"reqdks","mountupgrade") != 0 && get_module_setting('toggleupgradedks') == 1) {
						$upgradedks = '`n'. get_module_objpref("mounts",$id,"reqdks","mountupgrade") . ' DKs required to upgrade';
					}
				} else {
					$upgradedks = "`nDKs required for upgrade: ". get_module_objpref("mounts",$id,"upgradedks");
				}
				if (get_module_objpref("mounts",$id,"upgradelevels") == "") {
					if (get_module_objpref("mounts",$id,"reqlevels","mountupgrade") != "" && get_module_objpref("mounts",$id,"reqlevels","mountupgrade") != 0 && get_module_setting('toggleupgradelevels') == 1) {
						$upgradelevels = '`n'. get_module_objpref("mounts",$id,"reqdks","mountupgrade") . ' levelss required to upgrade';
					}
				} else {
					$upgradelevels = "`nLevels required for upgrade: ". get_module_objpref("mounts",$id,"upgradelevels");
				}
				if (get_module_objpref("mounts",$id,"upgradedays") == "") {
					if (get_module_objpref("mounts",$id,"reqdays","mountupgrade") != "" && get_module_objpref("mounts",$id,"reqdays","mountupgrade") != 0 && get_module_setting('toggleupgradedays') == 1) {
						$upgradedays = '`n'. get_module_objpref("mounts",$id,"reqdays","mountupgrade") . ' Days required to upgrade';
					}
				} else {
					$upgradedays = "`nDays required for upgrade: ". get_module_objpref("mounts",$id,"upgradedays");
				}
				if (get_module_objpref("mounts",$id,"upgradeffs") == "") {
					if (get_module_objpref("mounts",$id,"reqffs","mountupgrade") != "" && get_module_objpref("mounts",$id,"reqffs","mountupgrade") != 0 && get_module_setting('toggleupgradeffs') == 1) {
						$upgradeffs = '`n'. get_module_objpref("mounts",$id,"reqffs","mountupgrade") . ' forest fights required to upgrade';
					}
				} else {
					$upgradeffs = "`nForest fights required for upgrade: ". get_module_objpref("mounts",$id,"upgradeffs");
				}
				if (getmodulesetting('toggleupgradeview') && get_module_objpref("mounts",$id,"boolshowupgrade") == 1) {
					addnav("View Upgrade");
					addnav(array("View %s`0",$upgrademount['mountname']),"runmodule.php?module=mountprereq&op=browse&id={$upgradeto}");
				}
			}
			output("`n`b`c`@Special Abilities`7`b`c");
			output("`c`7$name`7 $rounds $atk $def $regen $damageshield $lifetap $minioncount $maxbadguydamage $badguyatkmod $badguydefmod $badguydmgmod $ffs $spd $upgrade $upgradedks $upgradelevels $upgradedays $upgradeffs`c`n");
			output("`n`c`b`^Ownership Requirements`7`b`c");
			$dkreq = "";
			$charmreq = "";
			$recareq = "";
			$sexreq = "";
			$donationreq = "";
			$favorreq = "";
			$alignreq = "";
			if ($mount['mountdkcost'] != 0 && $mount['mountdkcost'] != "") $dkreq = "`nDragonkills: ". $mount['mountdkcost'];
			if (get_module_objpref("mounts",$id,"charmreq") != 0) $charmreq = "`nCharm: ". get_module_objpref("mounts",$id,"charmreq");
			if (get_module_objpref("mounts",$id,"racereq") != "") $racereq = "`n". get_module_objpref("mounts",$id,"racereq"). " `0Race";
			if (get_module_objpref("mounts",$id,"sexreq") != 2) {
				if (get_module_objpref("mounts",$id,"sexreq") == 0) {
					$sex = "`!Male`7";
				} else { 
					$sex = "`rFemale`7";
				}
				$sexreq = "`n$sex owners";
			}
			if (get_module_objpref("mounts",$id,"favorreq") != 0) $favorreq = "`nFavor: ". get_module_objpref("mounts",$id,"favorreq");
			if (get_module_objpref("mounts",$id,"donationreq") != 0) $donationreq = "`nDonation Points: ". get_module_objpref("mounts",$id,"donationreq");
			if (is_module_active('alignment') && get_module_objpref("mounts",$id,"boolalign") != 0) {
				if (get_module_objpref("mounts",$id,"boolalign") == 1) {
					$align = "`nBelow `4". get_module_objpref("mounts",$id,"alignlo"). "`7 alignment.`n";
				} elseif (get_module_objpref("mounts",$id,"boolalign") == 3 ) {
					$align = "`nAbove `@". get_module_objpref("mounts",$id,"alignhi"). "`7 alignment.`n";
				} else {
					$align = "`nBetween `4". get_module_objpref("mounts",$id,"alignlo"). "`7 and `@". get_module_objpref("mounts",$id,"alignhi"). "`7 alignment.";
				}
			}
			output("`c$dkreq $charmreq $favorreq $alignreq $donationreq $racereq $sexreq`7 $align`0`c`n");
			if (is_module_active('mountrarity') && get_module_setting('boolmountrarity') == 1 && get_module_objpref("mounts",$id,"rarity","mountrarity") != 100) {
				$rare = get_module_objpref("mounts",$id,"rarity","mountrarity");
				if ($rare <= 20) {
					$raretext = '`b`$extremely rare`b`7!!';
				} elseif ($rare <= 40) {
					$raretext = '`@rare`7!';
				} elseif ($rare <= 60) {
					$raretext = 'uncommon.';
				} elseif ($rare <= 80) {
					$raretext = 'common.';
				} else {
					$raretext = 'very common.';
				}
				output("`n`c`b`&Miscellaneous Infomation`c`b");
				output("`c`7The`& %s`7 is %s`c`n",$mount['mountname'],$raretext);
			}
			if (get_module_objpref("mounts",$id,"outputtext") != "") {
				output("`n`c`Q`i%s`7`i`c`n",stripslashes(get_module_objpref("mounts",$id,"outputtext")));
			}
			addnav("Go Back");
			addnav("Flip back to the Contents page","runmodule.php?module=mountprereq&op=bestiary");

		}
	}
page_footer();
}
//code by Thanatos
function mountprereq_calc($value){
global $session;
$value=preg_replace("/<([A-Za-z0-9]+)\\|([A-Za-z0-9]+)>/","get_module_pref('\\2','\\1')",$value);
$value=preg_replace("/<([A-Za-z0-9]+)>/","\$session['user']['\\1']",$value);
eval('$value='.$value.";");
return $value;
}
?>