<?php

function racerobot_getmoduleinfo(){
	$info = array(
		"name"=>"Race - Robot",
		"version"=>"2009-09-14",
		"author"=>"Dan Hall",
		"category"=>"Races",
		"download"=>"fix this",
		"prefs"=>array(
			"heat"=>"Current Heat level,int|0",
			"atk"=>"Overclocking points assigned to Attack,int|0",
			"def"=>"Overclocking points assigned to Defence,int|0",
			"heal"=>"Overclocking points assigned to Heal,int|0",
			"buff"=>"Overclocking points assigned to Combat Stamina Buff,int|0",
		),
	);
	return $info;
}

function racerobot_install(){
	$ifrace = "if (\$session['user']['race'] == \"Robot\") {return true;} else {return false;};";
	$ifloc = "if (\$session['user']['location'] == \"Cyber City 404\") {return true;} else {return false;};";
	module_addhook("chooserace");
	module_addhook("setrace",false,$ifrace);
	module_addhook("alternativeresurrect",false,$ifrace);
	module_addhook("stamina-newday",false,$ifrace);
	module_addhook("villagetext",false,$ifloc);
	module_addhook("stabletext",false,$ifloc);
	module_addhook("validlocation");
	module_addhook("validforestloc");
	module_addhook("moderate");
	module_addhook("changesetting");
	module_addhook("stablelocs");
	module_addhook("racenames");
	module_addhook("village",false,$ifrace);
	module_addhook("forest",false,$ifrace);
	module_addhook("worldnav",false,$ifrace);
	module_addhook("startofround",false,$ifrace);
	module_addhook("charstats",false,$ifrace);
	module_addhook("load_inventory",false,$ifrace);
	module_addhook("potion",false,$ifrace);
	return true;
}

function racerobot_uninstall(){
	global $session;
	$vname = getsetting("villagename", LOCATION_FIELDS);
	$gname = get_module_setting("villagename");
	$sql = "UPDATE " . db_prefix("accounts") . " SET location='$vname' WHERE location = '$gname'";
	db_query($sql);
	if ($session['user']['location'] == $gname)
		$session['user']['location'] = $vname;
	// Force anyone who was a Robot to rechoose race
	$sql = "UPDATE  " . db_prefix("accounts") . " SET race='" . RACE_UNKNOWN . "' WHERE race='Robot'";
	db_query($sql);
	if ($session['user']['race'] == 'Robot')
		$session['user']['race'] = RACE_UNKNOWN;
	return true;
}

function racerobot_dohook($hookname,$args){
	global $session,$resline;
	$city = "Cyber City 404";
	$race = "Robot";
	switch($hookname){
	case "load_inventory":
		if ($session['user']['race']==$race){
			foreach ($args AS $itemid => $prefs){
				if ($prefs['blockrobot']){
					$args[$itemid]['blockuse'] = true;
				}
			}
		}
	break;
	case "racenames":
		$args[$race] = $race;
		break;
	case "changesetting":
		// Ignore anything other than villagename setting changes
		if ($args['setting'] == "villagename" && $args['module']=="racerobot") {
			if ($session['user']['location'] == $args['old'])
				$session['user']['location'] = $args['new'];
			$sql = "UPDATE " . db_prefix("accounts") .
				" SET location='" . addslashes($args['new']) .
				"' WHERE location='" . addslashes($args['old']) . "'";
			db_query($sql);
			if (is_module_active("cities")) {
				$sql = "UPDATE " . db_prefix("module_userprefs") .
					" SET value='" . addslashes($args['new']) .
					"' WHERE modulename='cities' AND setting='homecity'" .
					"AND value='" . addslashes($args['old']) . "'";
				db_query($sql);
			}
		}
		break;
	case "chooserace":
		if ($session['user']['dragonkills'] < 7){
			break;
		}
		clear_module_pref("heat");
		clear_module_pref("atk");
		clear_module_pref("def");
		clear_module_pref("heal");
		output("`0<a href='newday.php?setrace=$race$resline'>You grin at the gatekeeper</a>, exposing your sharp, bitey metal teeth.`n`n", true);
		addnav("`&Robot`0","newday.php?setrace=$race$resline");
		addnav("","newday.php?setrace=$race$resline");
		break;
	case "setrace":
		if ($session['user']['race']==$race){
				output("\"`6Oh, I see, right,`0\" says the gatekeeper, and takes out his ledger.  \"`6Are, oh, double-you, bee, oh, tee, tee.  Robot.  I must say, it's nice to see some straightforward logic in this place.`0\"`n`nYou nod.  \"`#Yes,`0\" you reply, \"`#it is.`0\"`n`nThe gatekeeper looks up and says, with a mischievous grin, \"`6So - would you like to play a game of chess?`0\"`n`nYou scowl.  \"`#Do not play games with me, human.  Just because you know how to press my buttons does not mean I will allow you to press them.`0\"`n`n\"`6Sorry,`0\" says the gatekeeper with an even wider smile, \"`6I meant - SUDO play a game of chess.`0\"`n`n\"`#I'd love to,`0\" you reply.`n`nAfter you checkmate the gatekeeper in three moves, he sits back, obviously miffed.`n`n\"`#As I won,`0\" you say, \"`#you must answer my question: what is this \"love\" of which you humans speak?`0\"`n`nThe gatekeeper looks up at you, pouting, arms folded.  \"`6This statement is false.`0\"`n`nYou twitch a little, let out a quiet, sad \"`#beep,`0\" and fall over onto your back.`n`nWhen you regain consciousness, you draw yourself up to your full height and stare the grinning gatekeeper in the face.  \"`#I have processed all the relevant data,`0\" you say, in a low, mechanical growl, \"`#and have come to the conclusion that you, fleshbag, are a bastard.`0\"`n`nThe gatekeeper winks and smiles.  \"`6Don't tell the others.`0\"`n`nYou turn and head towards the gate, muttering under your breath.`n`n\"`6Pi is exactly three, by the way!`0\" he calls after you.  But you're too wily for him, oh yes indeed.  Just two minutes later, you reboot successfully and place him on your \"ignore\" list.");
				if (is_module_active("cities")) {
				set_module_pref("homecity",$city,"cities");
				if ($session['user']['age'] == 0) $session['user']['location']=$city;
			}
		}
		break;
	case "alternativeresurrect":
		if ($session['user']['race']==$race){
			racerobot_checkcity();
			set_module_pref("heat",0);
			
			//Stamina buffs
			require_once("modules/staminasystem/lib/lib.php");
			apply_stamina_buff('robot3', array(
				"name"=>"Robot Bonus: Fast-Moving and Fast-Learning",
				"action"=>"Global",
				"costmod"=>0.7,
				"expmod"=>1.2,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			
			//combat buffs
			apply_buff("robotatk",array(
				"name"=>"`7Robot Bonus: Stabbity Claws`0",
				"atkmod"=>"1.3",
				"allowinpvp"=>1,
				"allowintrain"=>1,
				"rounds"=>-1,
				"schema"=>"module-racerobot",
				)
			);
			apply_buff("robotdef",array(
				"name"=>"`7Robot Penalty: Glass Skin`0",
				"defmod"=>"0.4",
				"allowinpvp"=>1,
				"allowintrain"=>1,
				"rounds"=>-1,
				"schema"=>"module-racerobot",
				)
			);
			if (get_module_pref("atk") > 0){
				$atk = (get_module_pref("atk")/10)+1;
				apply_buff("robotatkoc",array(
					"name"=>"`7Overclock: Increased Upper Body Servo Power`0",
					"atkmod"=>$atk,
					"allowinpvp"=>1,
					"allowintrain"=>1,
					"rounds"=>-1,
					"schema"=>"module-racerobot",
					)
				);
			}
			if (get_module_pref("def") > 0){
				$def = (get_module_pref("def")/10)+1;
				apply_buff("robotdefoc",array(
					"name"=>"`7Overclock: Increased Lower Body Servo Power`0",
					"defmod"=>$def,
					"allowinpvp"=>1,
					"allowintrain"=>1,
					"rounds"=>-1,
					"schema"=>"module-racerobot",
					)
				);
			}
			if (get_module_pref("heal") > 0){
				$heal = get_module_pref("heal");
				apply_buff("robothealoc",array(
					"name"=>"`7Overclock: In-Combat Self-Repair Subsystem`0",
					"regen"=>$heal,
					"effectmsg"=>"`#SYSTEM MESSAGE: {damage} POINTS OF DAMAGE AUTO-REPAIRED`0",
					"allowinpvp"=>1,
					"allowintrain"=>1,
					"rounds"=>-1,
					"schema"=>"module-racerobot",
					)
				);
			} else {
				strip_buff("robothealoc");
			}
		}
		break;
	case "stamina-newday":
		if ($session['user']['race']==$race){
			racerobot_checkcity();
			set_module_pref("heat",0);
			increment_module_pref("amber",-200000,"staminasystem");
			
			//Stamina buffs
			require_once("modules/staminasystem/lib/lib.php");
			apply_stamina_buff('robot3', array(
				"name"=>"Robot Bonus: Fast-Moving and Fast-Learning",
				"action"=>"Global",
				"costmod"=>0.7,
				"expmod"=>1.2,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('raceclassy', array(
				"name"=>"Robot Penalty: Insults Incompetence",
				"class"=>"Insults",
				"costmod"=>2,
				"expmod"=>0.5,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			
			
			//combat buffs
			apply_buff("robotatk",array(
				"name"=>"`7Robot Bonus: Stabbity Claws`0",
				"atkmod"=>"1.3",
				"allowinpvp"=>1,
				"allowintrain"=>1,
				"rounds"=>-1,
				"schema"=>"module-racerobot",
				)
			);
			apply_buff("robotdef",array(
				"name"=>"`7Robot Penalty: Glass Skin`0",
				"defmod"=>"0.4",
				"allowinpvp"=>1,
				"allowintrain"=>1,
				"rounds"=>-1,
				"schema"=>"module-racerobot",
				)
			);
			if (get_module_pref("atk") > 0){
				$atk = (get_module_pref("atk")/10)+1;
				apply_buff("robotatkoc",array(
					"name"=>"`7Overclock: Increased Upper Body Servo Power`0",
					"atkmod"=>$atk,
					"allowinpvp"=>1,
					"allowintrain"=>1,
					"rounds"=>-1,
					"schema"=>"module-racerobot",
					)
				);
			}
			if (get_module_pref("def") > 0){
				$def = (get_module_pref("def")/10)+1;
				apply_buff("robotdefoc",array(
					"name"=>"`7Overclock: Increased Lower Body Servo Power`0",
					"defmod"=>$def,
					"allowinpvp"=>1,
					"allowintrain"=>1,
					"rounds"=>-1,
					"schema"=>"module-racerobot",
					)
				);
			}
			if (get_module_pref("heal") > 0){
				$heal = get_module_pref("heal");
				apply_buff("robothealoc",array(
					"name"=>"`7Overclock: In-Combat Self-Repair Subsystem`0",
					"regen"=>$heal,
					"effectmsg"=>"`#SYSTEM MESSAGE: {damage} POINTS OF DAMAGE AUTO-REPAIRED`0",
					"allowinpvp"=>1,
					"allowintrain"=>1,
					"rounds"=>-1,
					"schema"=>"module-racerobot",
					)
				);
			} else {
				strip_buff("robothealoc");
			}
		}
		break;
	case "validforestloc":
	case "validlocation":
		if (is_module_active("cities"))
			$args[$city]="village-$race";
		break;
	case "moderate":
		if (is_module_active("cities")) {
			tlschema("commentary");
			$args["village-$race"]=sprintf_translate("City of %s", $city);
			tlschema();
		}
		break;
	case "villagetext":
		racerobot_checkcity();
		if ($session['user']['location'] == $city){
			$args['text']=array("`0You are standing in the heart of %s.  Though called a city by outsiders, this stronghold of robots is little more than a glorified scrapyard.  Puddles of rusted, oily water cover the bare, muddy earth, and piles of scrap metal lie here and there.  Some residents are engaged in stilted, mechanical conversation around the well in the village square.`n", $city, $city);
			$args['schemas']['text'] = "module-racerobot";
			$args['clock']="`n`0The great clockwork readout at the centre of the city reads `&%s`0.`n";
			$args['schemas']['clock'] = "module-racerobot";
			if (is_module_active("calendar")) {
				$args['calendar'] = "`n`0A smaller contraption next to it reads `&%s`0, `&%s %s %s`0.`n";
				$args['schemas']['calendar'] = "module-racerobot";
			}
			$args['title']=array("%s, Home of the Robots", $city);
			$args['schemas']['title'] = "module-racerobot";
			$args['sayline']="says";
			$args['schemas']['sayline'] = "module-racerobot";
			$args['talk']="`n`&Nearby some robots talk:`n";
			$args['schemas']['talk'] = "module-racerobot";
			$new = get_module_setting("newest-$city", "cities");
			if ($new != 0) {
				$sql =  "SELECT name FROM " . db_prefix("accounts") .
					" WHERE acctid='$new'";
				$result = db_query_cached($sql, "newest-$city");
				$row = db_fetch_assoc($result);
				$args['newestplayer'] = $row['name'];
				$args['newestid']=$new;
			} else {
				$args['newestplayer'] = $new;
				$args['newestid']="";
			}
			if ($new == $session['user']['acctid']) {
				$args['newest']="`n`0As you wander your new home, you feel your jaw dropping at the wonders around you.";
			} else {
				$args['newest']="`n`0Wandering the village, jaw agape, is `&%s`0.";
			}
			$args['schemas']['newest'] = "module-racerobot";
			$args['section']="village-$race";
			$args['stablename']="Mike's Chop Shop";
			$args['schemas']['stablename'] = "module-racerobot";
			$args['gatenav']="Village Gates";
			$args['schemas']['gatenav'] = "module-racerobot";
			unblocknav("stables.php");
		}
		break;
	case "stabletext":
		if ($session['user']['location'] != $city) break;
		$args['title'] = "Mike's Chop Shop";
		$args['schemas']['title'] = "module-racerobot";
		$args['desc'] = array(
			"`0Just outside the outskirts of the mighty scrapyard, a training area and riding range has been set up.",
			"You can see a tall, spindly Robot whose name you assume to be Mike.  He's busily tending to his flock of abominations, using his many custom-built appendages to care for the creatures and machines in his care.  Vehicles, animals, and Genetically-Engineered Beasts of Burden are arrayed in front of you.",
			array("As you approach, \"Mike\" spins around to meet you, brandishing a screwdriver.  \"`^HOW MAY I HELP YOU MY FINE YOUNG %s?`0\" he asks in a buzzing, 8-bit monotone.", translate_inline($session['user']['sex']?'LASS':'LAD', 'stables'))
		);
		$args['schemas']['desc'] = "module-racerobot";
		$args['lad']="friend";
		$args['schemas']['lad'] = "module-racerobot";
		$args['lass']="friend";
		$args['schemas']['lass'] = "module-racerobot";
		$args['nosuchbeast']="`0\"`^I DO NOT STOCK ANY SUCH BEAST OR MACHINE.  PERHAPS IT WAS A DREAM OF YOURS`0\", Mike says apologetically.";
		$args['schemas']['nosuchbeast'] = "module-racerobot";
		$args['finebeast']=array(
			"`0\"`^THIS ONE WILL ASSIST WITH YOUR OBJECTIVE OF CRUSHING YOUR FOES TO A FINE PASTE BENEATH YOUR MIGHTY METAL FEET,`0\" says Mike.`n`n",
			"`0\"`^TO THIS ONE, THERE ARE NO SUPERIOR EXAMPLES`0\" Mike boasts.`n`n",
			"`0\"`^DOES THIS ONE NOT HAVE A FINE SHINE`0\" he asks.`n`n",
			"`0\"`^THIS ONE I FIND AESTHETICALLY PLEASING, BEING AS ITS ANGLES AND SIZES ARE ALMOST A PERFECT 1:1.6180339887 RATIO`0\" exclaims Mike.`n`n",
			"`0\"`^THIS ONE WOULD BE A VERY ADVISABLE PURCHASE AT 2.1516 TIMES THE PRICE I AM ASKING,`0\" booms Mike.`n`n",
			);
		$args['schemas']['finebeast'] = "module-racerobot";
		$args['toolittle']="`0Mike looks over the gold and gems you offer and turns up his mechanical nose, \"`^THIS IS NOT THE CORRECT AMOUNT.  THE %s WILL COST YOU `&%s `^REQUISITION TOKENS  AND `%%s`^ CIGARETTES.  THE PRICE IS NOT NEGOTIABLE.`0\"";
		$args['schemas']['toolittle'] = "module-racerobot";
		$args['replacemount']="`0Patting %s`0 on the rump, you hand the reins as well as the money for your new mount, and Mike hands you the reins of a `&%s`0.";
		$args['schemas']['replacemount'] = "module-racerobot";
		$args['newmount']="`0You hand over the money for your new mount, and Mike hands you the reins of a new `&%s`0.";
		$args['schemas']['newmount'] = "module-racerobot";
		$args['nofeed']="`0\"`^I AM SORRY %s, BUT I DO NOT STOCK FUEL OR FEED HERE.  YOU MUST LOOK ELSEWHERE.`0\"";
		$args['schemas']['nofeed'] = "module-racerobot";
		$args['nothungry']="`&%s`0 picks briefly at the food and then ignores it.  Mike, lacking the imagination to be dishonest, shakes his head and hands you back your money.";
		$args['schemas']['nothungry'] = "module-racerobot";
		$args['halfhungry']="`&%s`0 dives into the provided food and gets through about half of it before stopping.  \"`^WELL, %s WAS NOT AS HUNGRY AS YOU THOUGHT.`0\" says Mike as he hands you back all but %s Requisition tokens.";
		$args['schemas']['halfhungry'] = "module-racerobot";
		$args['hungry']="`0%s`0 seems to inhale the food provided.  %s`0, the greedy creature that it is, then goes snuffling at Mike's pockets for more food.`nMike shakes his head in amusement and collects `&%s`0 Requisition tokens from you.";
		$args['schemas']['hungry'] = "module-racerobot";
		$args['mountfull']="`n`0\"`^IT APPEARS, %s, THAT YOUR %s`^ IS NOW FULL.  YOU WILL RETURN WHEN IT IS EMPTY ONCE MORE.`0\" says Mike with a genial smile.";
		$args['schemas']['mountfull'] = "module-racerobot";
		$args['nofeedgold']="`0\"`^THAT IS NOT ENOUGH MONEY TO PAY FOR FOOD HERE`0\"  Mike turns his back on you, and you lead %s away to find other places for feeding.";
		$args['schemas']['nofeedgold'] = "module-racerobot";
		$args['confirmsale']="`n`n`0UMike eyes your mount up and down, checking it over carefully.  \"`^ARE YOU QUITE SURE YOU WISH TO RELINQUISH THIS CONVEYANCE?`0\"";
		$args['schemas']['confirmsale'] = "module-racerobot";
		$args['mountsold']="`0With but a single tear, you hand over the reins to your %s`0 to Mike's stableboy.  The tear dries quickly, and the %s in hand helps you quickly overcome your sorrow.";
		$args['schemas']['mountsold'] = "module-racerobot";
		$args['offer']="`n`n`0Mike strokes your creature's flank and offers you `&%s`0 Requisition tokens and `%%s`0 cigarettes for %s`0.";
		$args['schemas']['offer'] = "module-racerobot";
		break;
	case "potion":
		if ($session['user']['race']==$race){
			blocknav("healer.php?op=buy&pct=100&return=village.php");
			blocknav("healer.php?op=buy&pct=90&return=village.php");
			blocknav("healer.php?op=buy&pct=80&return=village.php");
			blocknav("healer.php?op=buy&pct=70&return=village.php");
			blocknav("healer.php?op=buy&pct=60&return=village.php");
			blocknav("healer.php?op=buy&pct=50&return=village.php");
			blocknav("healer.php?op=buy&pct=40&return=village.php");
			blocknav("healer.php?op=buy&pct=30&return=village.php");
			blocknav("healer.php?op=buy&pct=20&return=village.php");
			blocknav("healer.php?op=buy&pct=10&return=village.php");
			blocknav("healer.php?op=buy&pct=100");
			blocknav("healer.php?op=buy&pct=90");
			blocknav("healer.php?op=buy&pct=80");
			blocknav("healer.php?op=buy&pct=70");
			blocknav("healer.php?op=buy&pct=60");
			blocknav("healer.php?op=buy&pct=50");
			blocknav("healer.php?op=buy&pct=40");
			blocknav("healer.php?op=buy&pct=30");
			blocknav("healer.php?op=buy&pct=20");
			blocknav("healer.php?op=buy&pct=10");
			output("`n`nThe man stops suddenly and looks you up and down, examining your cracked glass skin and visible wiring harnesses.  \"`6'ere,`3\" he says, \"`6Are you `isure`i you're not a Robot?`3\"`n`nYou're really not sure what to say.");
		}
		break;
	case "village":
		if ($session['user']['race']==$race){
			blocknav("runmodule.php?module=bloodbank");
			blocknav("healer.php?op=buy");
			addnav("Robot Talents");
			addnav("Configure Overclocking","runmodule.php?module=racerobot&op=config&from=village");
			if ($session['user']['hitpoints']<$session['user']['maxhitpoints']){
				$scost = 400*($session['user']['maxhitpoints']-$session['user']['hitpoints']);
				$spct = ($scost*100)/1000000;
				addnav(array("Standby until Repaired (`Q%s%%`0)",$spct),"runmodule.php?module=racerobot&op=standby&til=repaired&from=village", true);
			}
			$heat = get_module_pref("heat");
			if ($heat > 0){
				$scost = 40*$heat;
				$spct = ($scost*100)/1000000;
				addnav(array("Standby until Cooled (`Q%s%%`0)",$spct),"runmodule.php?module=racerobot&op=standby&til=cooled&from=village", true);
			}
		}
		break;
	case "forest":
		if ($session['user']['race']==$race){
			addnav("Robot Talents");
			addnav("Configure Overclocking","runmodule.php?module=racerobot&op=config&from=forest");
			if ($session['user']['hitpoints']<$session['user']['maxhitpoints']){
				$scost = 400*($session['user']['maxhitpoints']-$session['user']['hitpoints']);
				$spct = ($scost*100)/1000000;
				addnav(array("Standby until Repaired (`Q%s%%`0)",$spct),"runmodule.php?module=racerobot&op=standby&til=repaired&from=forest", true);
			}
			$heat = get_module_pref("heat");
			if ($heat > 0){
				$scost = 40*$heat;
				$spct = ($scost*100)/1000000;
				addnav(array("Standby until Cooled (`Q%s%%`0)",$spct),"runmodule.php?module=racerobot&op=standby&til=cooled&from=forest", true);
			}
		}
		break;
	case "worldnav":
		if ($session['user']['race']==$race){
			addnav("Robot Talents");
			addnav("Configure Overclocking","runmodule.php?module=racerobot&op=config&from=worldnav");
			if ($session['user']['hitpoints']<$session['user']['maxhitpoints']){
				$scost = 400*($session['user']['maxhitpoints']-$session['user']['hitpoints']);
				$spct = ($scost*100)/1000000;
				addnav(array("Standby until Repaired (`Q%s%%`0)",$spct),"runmodule.php?module=racerobot&op=standby&til=repaired&from=worldnav", true);
			}
			$heat = get_module_pref("heat");
			if ($heat > 0){
				$scost = 40*$heat;
				$spct = ($scost*100)/1000000;
				addnav(array("Standby until Cooled (`Q%s%%`0)",$spct),"runmodule.php?module=racerobot&op=standby&til=cooled&from=worldnav", true);
			}
		}
		break;
	case "stablelocs":
		tlschema("mounts");
		$args[$city]=sprintf_translate("%s", $city);
		tlschema();
		break;
	case "startofround":
		if ($session['user']['race']==$race && $session['user']['alive']==1){
			$heat = get_module_pref("heat");
			debug("Heat level: ".$heat);
			$fail = e_rand(100,1500);
			debug("Fail Threshold: ".$fail);
			if ($heat >= $fail){
				$penalty = ($session['user']['maxhitpoints']/100)*($heat/10);
				debug($penalty);
				$damage = 0-$penalty;
				debug("Robot is Overheating!");
				apply_buff("robotoverheat",array(
					"regen"=>$damage,
					"effectmsg"=>"`4`bYou feel a horrible burning, popping sensation deep within your chest!  Something has gone badly wrong, and one of your internal components is overheating!  You lose {damage} hitpoints!`b",
					"allowinpvp"=>1,
					"allowintrain"=>1,
					"rounds"=>1,
					"schema"=>"module-racerobot",
					)
				);
			}
			//increase player heat level
			$heat += get_module_pref("atk")*10;
			$heat += get_module_pref("def")*10;
			$heat += get_module_pref("heal")*10;
			set_module_pref("heat",$heat);
			debug("Heat level: ".$heat);
		}
		break;
	case "charstats":
		if ($session['user']['race']==$race){
			$heat = get_module_pref("heat");
			$col = "#00FF00";
			if ($heat>200) $col = "#FDFF00";
			if ($heat>400) $col = "#FFA200";
			if ($heat>600) $col = "#FF5A00";
			if ($heat>800) $col = "#FF0000";
			$width = $heat/10;
			if ($width>100) $width=100;
			$nwidth = 100-$width;
			$bar = "<table style='border: solid 1px #000000' bgcolor='#333333' cellpadding='0' cellspacing='0' width='70' height='5'><tr><td width='$width%' bgcolor='$col'></td><td width='$nwidth%' bgcolor='$#333333'></td></tr></table>";
			setcharstat("Vital Info", "Heat Level", $bar);
		}
		break;
	}
	return $args;
}

function racerobot_checkcity(){
	global $session;
	$race="Robot";
	$city=get_module_setting("villagename");

	if ($session['user']['race']==$race && is_module_active("cities")){
		//if they're this race and their home city isn't right, set it up.
		if (get_module_pref("homecity","cities")!=$city){ //home city is wrong
			set_module_pref("homecity",$city,"cities");
		}
	}
	return true;
}

function racerobot_run(){
	global $session;
	page_header("Overclocking Configuration");
	$from = httpget('from');
	switch (httpget('op')){
		case "config":
			output("You sit down and open up your chest panel.  Inside is a row of variable resistors which increase power to various subsystems.  You can adjust the resistors to enhance your performance in combat, albeit with some tradeoffs - applying too much voltage will result in catastrophic overheating.`n`nYou mull over your decisions carefully.`n`n");
		break;
		case "atkinc":
			output("You twist the Upper Torso Servo Voltage VR clockwise.  You feel a rush of extra burning energy in your arms.  This should help with your attack power.`n`n");
			increment_module_pref("atk");
		break;
		case "atkdec":
			output("You twist the Upper Torso Servo Voltage VR anti-clockwise.  You feel your attack power drain away a little.`n`n");
			increment_module_pref("atk",-1);
		break;
		case "definc":
			output("You twist the Lower Torso Servo Voltage VR clockwise.  You feel a rush of extra burning energy in your legs, making you lighter on your feet and better able to step out of the way of impending fists and claws.`n`n");
			increment_module_pref("def");
		break;
		case "defdec":
			output("You twist the Lower Torso Servo Voltage VR anti-clockwise.  Your legs feel more sluggish, and you have a feeling you won't be able to dodge blows from enemies as easily.`n`n");
			increment_module_pref("def",-1);
		break;
		case "healinc":
			output("You twist the Damage Repair Subsystem CPU Voltage VR clockwise.  You feel the extra threads rushing through you, ready to heal your glass skin during battle.`n`n");
			increment_module_pref("heal");
		break;
		case "healdec":
			output("You twist the Damage Repair Subsystem CPU Voltage VR anti-clockwise.  The CPU controlling your in-combat self-healing abilities lowers its speed by a few megahertz.  You feel slightly uneasy and vulnerable.`n`n");
			increment_module_pref("heal",-1);
		break;
		case "standby":
			$heat = get_module_pref("heat");
			require_once "modules/staminasystem/lib/lib.php";
			switch (httpget('til')){
				case "repaired":
					output("You go into Standby Mode, reducing every system process to the bare minimum required to stay alive, and activate your self-healing routine.  The cracks in your glass skin begin to shorten in jagged little hops.`n`nThe sound of glass mending itself is, oddly enough, rather similar to the sound of glass breaking.`n`nYour subsystems cool down too, with little plinks and pops.`n`n");
					$hp = $session['user']['maxhitpoints']-$session['user']['hitpoints'];
					$s = 0;
					for ($i=0; $i<$hp; $i++){
						$session['user']['hitpoints']++;
						$heat-=10;
						$s+=400;
					}
					removestamina($s);
					output("When consciousness returns, your Hitpoints have been restored to full and your internal systems have cooled down a little.  You have used `Q%s`0 Stamina points (about `Q%s`0%% of total Stamina).`n`n",number_format($s),($s/1000000)*100);
				break;
				case "cooled":
					output("You go into Standby Mode, reducing every system process to the bare minimum required to stay alive, and activate your self-healing routine.  The cracks in your glass skin begin to shorten in jagged little hops.`n`nThe sound of glass mending itself is, oddly enough, rather similar to the sound of glass breaking.`n`nYour subsystems cool down too, with little plinks and pops.`n`n");
					$j = $heat/10;
					$s = 0;
					for ($i=0; $i<$j; $i++){
						$session['user']['hitpoints']++;
						$heat-=10;
						$s+=400;
					}
					removestamina($s);
					output("When consciousness returns, your Hitpoints have increased and your internal systems have cooled down to normal operating temperatures.  You have used `Q%s`0 Stamina points (about `Q%s`0%% of total Stamina).`n`n",number_format($s),($s/1000000)*100);
				break;
			}
			if ($session['user']['hitpoints']>$session['user']['maxhitpoints']){
				$session['user']['hitpoints'] = $session['user']['maxhitpoints'];
			}
			if ($heat<0) $heat=0;
			set_module_pref("heat",$heat);
			strip_buff('robotoverheat');
		break;
	}
	$atk = get_module_pref("atk");
	$def = get_module_pref("def");
	$heal = get_module_pref("heal");
	$buff = get_module_pref("buff");
	$heat = get_module_pref("heat");
	addnav("Upper Torso Servo Voltage");
	addnav("Increase","runmodule.php?module=racerobot&op=atkinc&from=".$from);
	if ($atk>0) addnav("Decrease","runmodule.php?module=racerobot&op=atkdec&from=".$from);
	addnav("Lower Torso Servo Voltage");
	addnav("Increase","runmodule.php?module=racerobot&op=definc&from=".$from);
	if ($def>0) addnav("Decrease","runmodule.php?module=racerobot&op=defdec&from=".$from);
	addnav("Damage Repair Subsystem CPU Voltage");
	addnav("Increase","runmodule.php?module=racerobot&op=healinc&from=".$from);
	if ($heal>0) addnav("Decrease","runmodule.php?module=racerobot&op=healdec&from=".$from);
	// addnav("Combat Analysis Module RAM Voltage");
	// addnav("Increase","runmodule.php?module=racerobot&op=buffinc&from=".$from);
	// if ($buff>0) addnav("Decrease","runmodule.php?module=racerobot&op=buffdec&from=".$from);
	addnav("Standby Mode");
	if ($session['user']['hitpoints']<$session['user']['maxhitpoints']){
		$scost = 400*($session['user']['maxhitpoints']-$session['user']['hitpoints']);
		$spct = ($scost*100)/1000000;
		addnav(array("Standby until Repaired (`Q%s%%`0)",$spct),"runmodule.php?module=racerobot&op=standby&til=repaired&from=".$from."", true);
	}
	if ($heat > 0){
		$scost = 40*$heat;
		$spct = ($scost*100)/1000000;
		addnav(array("Standby until Cooled (`Q%s%%`0)",$spct),"runmodule.php?module=racerobot&op=standby&til=cooled&from=".$from."", true);
	}
	addnav("Close chest panel");
	switch ($from){
		case "village":
			addnav("Return to the Outpost","village.php");
		break;
		case "forest":
			addnav("Return to the Jungle","forest.php");
		break;
		case "worldnav":
			addnav("Return to the Wilderness","runmodule.php?module=worldmapen&op=continue");
		break;
	}
	$atk = get_module_pref("atk");
	$def = get_module_pref("def");
	$heal = get_module_pref("heal");
	$buff = get_module_pref("buff");
	$heat = get_module_pref("heat");
	output("`bCurrent settings:`b`n");
	output("Upper Torso Servo Voltage: +%sv`n",$atk*10);
	output("Lower Torso Servo Voltage: +%sv`n",$def*10);
	output("Damage Repair Subsystem CPU Voltage: +%sv`n",$heal*10);
	// output("Combat Analysis Module RAM Voltage: +%sv`n",$buff*10);
	if (get_module_pref("atk") > 0){
		$batk = (get_module_pref("atk")/10)+1;
		apply_buff("robotatkoc",array(
			"name"=>"`7Overclock: Increased Upper Body Servo Power`0",
			"atkmod"=>$batk,
			"allowinpvp"=>1,
			"allowintrain"=>1,
			"rounds"=>-1,
			"schema"=>"module-racerobot",
			)
		);
	} else {
		strip_buff("robotatkoc");
	}
	if (get_module_pref("def") > 0){
		$bdef = (get_module_pref("def")/10)+1;
		apply_buff("robotdefoc",array(
			"name"=>"`7Overclock: Increased Lower Body Servo Power`0",
			"defmod"=>$bdef,
			"allowinpvp"=>1,
			"allowintrain"=>1,
			"rounds"=>-1,
			"schema"=>"module-racerobot",
			)
		);
	} else {
		strip_buff("robotdefoc");
	}
	if (get_module_pref("heal") > 0){
		$bheal = get_module_pref("heal");
		apply_buff("robothealoc",array(
			"name"=>"`7Overclock: In-Combat Self-Repair Subsystem`0",
			"regen"=>$bheal,
			"effectmsg"=>"`#SYSTEM MESSAGE: {damage} POINTS OF DAMAGE AUTO-REPAIRED`0",
			"allowinpvp"=>1,
			"allowintrain"=>1,
			"rounds"=>-1,
			"schema"=>"module-racerobot",
			)
		);
	} else {
		strip_buff("robothealoc");
	}
	page_footer();
}
?>
