<?php

function racegobot_getmoduleinfo(){
	$info = array(
		"name"=>"Race - Gobot",
		"version"=>"2008-11-08",
		"author"=>"Dan Hall, based on the Humans race by Eric Stevens",
		"category"=>"Races",
		"settings"=>array(
			"Gobot Race Settings,title",
			"minedeathchance"=>"Chance for Gobots to die in the mine,range,0,100,1|90",
		),
		"prefs" => array(
			"Specialty - Gobot Talents User Prefs,title",
			"skill"=>"Points in Gobot Talents,int|0",
			"uses"=>"How many uses of Gobot Talents are awarded at NewDay,int|0",
			"reset"=>"Reset speciality?,bool|1",
			"status"=>"Overclocked or Underclocked value,int|0",
			"overdrive"=>"Use of Overdrive available,int|0"
		),
	);
	return $info;
}

function racegobot_install(){
	module_addhook("chooserace");
	module_addhook("setrace");
	module_addhook("newday");
	module_addhook("raceminedeath");
	module_addhook("racenames");
//	module_addhook("newday-intercept");
	module_addhook("village");
	module_addhook("count-travels");
	module_addhook("fightnav-specialties");
	module_addhook("apply-specialties");
	module_addhook("potion");
	return true;
}

function racegobot_uninstall(){
	global $session;
	// Force anyone who was a Gobot to rechoose race
	$sql = "UPDATE  " . db_prefix("accounts") . " SET race='" . RACE_UNKNOWN . "' WHERE race='Gobot'";
	db_query($sql);
	if ($session['user']['race'] == 'Gobot')
		$session['user']['race'] = RACE_UNKNOWN;
	return true;
}

function racegobot_dohook($hookname,$args){
	//yeah, the $resline thing is a hack.  Sorry, not sure of a better way
	// to handle this.
	// Pass it as an arg?
	global $session,$resline;
	if (is_module_active("racerobot")) {
		$city = "Cyber City 404";
	} else {
		$city = getsetting("villagename", LOCATION_FIELDS);
	}
	$race = "Gobot";
	switch($hookname){
	case "racenames":
		$args[$race] = $race;
		break;
	case "raceminedeath":
		if ($session['user']['race'] == $race) {
			$args['chance'] = get_module_setting("minedeathchance");
		}
		break;
	case "chooserace":
		if ($session['user']['dragonkills'] < 7)
			break;
		output("`0<a href='newday.php?setrace=$race$resline'>You point downwards, directing the gatekeeper's attention to your knobbly tyres.</a>`n`n ", true);
		addnav("`&Gobot`0","newday.php?setrace=$race$resline");
		addnav("","newday.php?setrace=$race$resline");
		break;
	case "setrace":
		if ($session['user']['race']==$race){
			output("\"Aha,\" says the gatekeeper, taking out his ledger.  \"Say no more.  Gee...\"`nThe roar of your engine cuts him off, and he looks up.`nHe stares in annoyance at the rapidly-diffusing cloud of dust stretching from his hut to the outpost gate.`n\"Well, that's just bloody rude,\" mutters the gatekeeper.  \"I didn't even get to see its face when I mis-spelled its race.  Ah, well...  Can't insult 'em all, I guess.\"`nHe goes back to his crossword.");
			if (is_module_active("cities")) {
				set_module_pref("homecity",$city,"cities");
				if ($session['user']['age'] == 0)
					$session['user']['location']=$city;
			}
		}
		break;
//	case "newday-intercept":
//		if ($session['user']['race']==$race){
//			if(get_module_pref("reset")==1){
//				global $session;
//				$session['user']['specialty'] = "";
//				set_module_pref("reset",0);
//				output("`#SYSTEM MESSAGE - FIRMWARE RECONFIGURATION SUBROUTINE INITIALISED.  LIST OF IMPLANTS AVAILABLE FOR EMULATION DISPLAYED BELOW.  AWAITING INPUT.`n`nADVISORY MESSAGE - PROFICIENCY IN USE OF CURRENTLY-EMULATED IMPLANT WILL BE RETAINED.`n`n`0");
//			}
//		}
//		break;
	case "newday":
		if ($session['user']['race']==$race){
			racegobot_checkcity();
			$bonus = get_module_setting("bonus");
			$one = translate_inline("an");
			$two = translate_inline("two");
			$three = translate_inline("three");
			$word = $bonus==1?$one:$bonus==2?$two:$three;
			$fight = translate_inline("fight");
			$fights = translate_inline("fights");

			$args['turnstoday'] .= ", Race (gobot): $bonus";
			$session['user']['turns']+=$bonus;
			output("`n`&Because you are `7untiring`&, you gain `^four extra`& jungle fights for today!`n`0");
			apply_buff("racialbenefit1",array(
				"name"=>"`7Ultra-Lightweight Frame`0",
				"defmod"=>"0.5",
				"allowinpvp"=>1,
				"allowintrain"=>1,
				"rounds"=>-1,
				"schema"=>"module-racegobot",
				)
			);
			apply_buff("racialbenefit2",array(
				"name"=>"`7Lightning Fists`0",
				"atkmod"=>"1.3",
				"allowinpvp"=>1,
				"allowintrain"=>1,
				"rounds"=>-1,
				"schema"=>"module-racegobot",
				)
			);
			apply_buff("racialbenefit3",array(
				"name"=>"`7Auto-Repair Subroutine`0",
				"regen"=>"ceil(<level>/3);",
				"allowinpvp"=>1,
				"allowintrain"=>1,
				"rounds"=>-1,
				"effectmsg"=>"`#SYSTEM MESSAGE: {damage} POINTS OF DAMAGE AUTO-REPAIRED`0",
				"schema"=>"module-racegobot",
				)
			);
			apply_buff("racialbenefit4",array(
				"name"=>"`7Cold`0",
				"allowinpvp"=>1,
				"allowintrain"=>1,
				"rounds"=>-1,
				"tempstat-charm"=>"- floor(<charm>*0.8)",
				"schema"=>"module-racegobot",
				)
			);
			set_module_pref("reset",1);
			set_module_pref("uses", 4);
			set_module_pref("status", 2);
			set_module_pref("overdrive", 1);
		}
		break;

	case "fightnav-specialties":
		if ($session['user']['race'] == $race) {
			$uses = get_module_pref("uses");
			$status = get_module_pref("status");
			$overdrive = get_module_pref("overdrive");
			$script = $args['script'];
			if ($uses>=1){
				addnav(array("Power Configuration`0", $uses),"");
			if ($status==2){
				addnav(array("`4Overclock`7`0", 1),
						$script."op=fight&skill=$spec&l=1", true);
				addnav(array("`4Underclock`7`0", 1),
						$script."op=fight&skill=$spec&l=2", true);
				}
			if ($status==1){
				addnav(array("`4Reset (currently Underclocked)`7`0", 1),
						$script."op=fight&skill=$spec&l=3", true);
				}
			if ($status==3){
				addnav(array("`4Reset (currently Overclocked)`7`0", 1),
						$script."op=fight&skill=$spec&l=3", true);
				}
			}
			if ($overdrive==1){
				addnav(array("`4Engage OverDrive`0", 1),
						$script."op=fight&skill=$spec&l=5", true);
			}
		}
		break;

	case "apply-specialties":
		if ($session['user']['race'] == $race) {
			$skill = httpget('skill');
			$l = httpget('l');
			$dec = 1;
			if ($skill==$spec){
				if (get_module_pref("uses") >= 1){
					switch($l){
					case 1:
						apply_buff("racialbenefit3",array(
							"startmsg"=>"`#SYSTEM MESSAGE:  OVERCLOCKING IN EFFECT.  REROUTING POWER.  AUTO-REPAIR SUBROUTINE DISABLED`0",
							"name"=>"`7Auto-Repair Subroutine - Disabled`0",
							"allowinpvp"=>1,
							"allowintrain"=>1,
							"rounds"=>-1,
							"schema"=>"module-racegobot",
							)
						);
						apply_buff("racialbenefit2",array(
							"startmsg"=>"`#SYSTEM MESSAGE:  OVERCLOCKING IN EFFECT.  REROUTING POWER.  COMBAT SUBROUTINE ACCELERATED`0",
							"name"=>"`7Lightning Fists - Enhanced`0",
							"atkmod"=>"1.6",
							"allowinpvp"=>1,
							"allowintrain"=>1,
							"rounds"=>-1,
							"schema"=>"module-racegobot",
							)
						);
						set_module_pref("status", '3');
						break;
					case 2:
						apply_buff("racialbenefit3",array(
							"startmsg"=>"`#SYSTEM MESSAGE:  UNDERCLOCKING IN EFFECT.  REROUTING POWER.  AUTO-REPAIR SUBROUTINE ACCELERATED`0",
							"name"=>"`7Auto-Repair Subroutine - Enhanced`0",
							"effectmsg"=>"`#SYSTEM MESSAGE: {damage} POINTS OF DAMAGE AUTO-REPAIRED`0",
							"regen"=>"ceil(<level>);",
							"allowinpvp"=>1,
							"allowintrain"=>1,
							"rounds"=>-1,
							"schema"=>"module-racegobot",
							)
						);
						apply_buff("racialbenefit2",array(
							"startmsg"=>"`#SYSTEM MESSAGE:  UNDERCLOCKING IN EFFECT.  REROUTING POWER.  COMBAT SUBROUTINE DECELERATED`0",
							"name"=>"`7Lightning Fists - Decelerated`0",
							"atkmod"=>"0.8",
							"allowinpvp"=>1,
							"allowintrain"=>1,
							"rounds"=>-1,
							"schema"=>"module-racegobot",
							)
						);
						set_module_pref("status", '1');
						break;
					case 3:
						apply_buff("racialbenefit3",array(
							"startmsg"=>"`#SYSTEM MESSAGE:  POWER REROUTING DISABLED.  SYSTEM RESTORED TO FACTORY SETTINGS`0",
							"name"=>"`7Auto-Repair Subroutine`0",
							"effectmsg"=>"`#SYSTEM MESSAGE: {damage} POINTS OF DAMAGE AUTO-REPAIRED`0",
							"regen"=>"ceil(<level>/3);",
							"allowinpvp"=>1,
							"allowintrain"=>1,
							"rounds"=>-1,
							"schema"=>"module-racegobot",
							)
						);
						apply_buff("racialbenefit2",array(
							"name"=>"`7Lightning Fists`0",
							"atkmod"=>"1.3",
							"allowinpvp"=>1,
							"allowintrain"=>1,
							"rounds"=>-1,
							"schema"=>"module-racegobot",
							)
						);
						set_module_pref("status", '2');
						break;
					case 5:
						apply_buff("overdrive",array(
							"startmsg"=>"`#SYSTEM MESSAGE:  BACKUP BATTERY ENGAGED.  OVERDRIVE ENABLED.`0",
							"name"=>"`4OverDrive`0",
							"regen"=>"<level>",
							"atkmod"=>"1.5",
							"effectmsg"=>"`#SYSTEM MESSAGE:  {damage} POINTS OF DAMAGE AUTO-REPAIRED BY OVERDRIVE SEQUENCE`0",
							"wearoff"=>"`#SYSTEM MESSAGE:  BACKUP BATTERY DEPLETED.  OVERDRIVE DISABLED.",
							"rounds"=>5,
							"schema"=>"module-racegobot",
							)
						);
						set_module_pref("overdrive", '0');
					}
				}
			}
		}
		break;

	case "validforestloc":
	case "validlocation":
		if (is_module_active("cities"))
			$args[$city]="village-$race";
		break;
	case "count-travels":
		if ($session['user']['race']== $race){
		$args['available']=($args['available']+15);
		}
		break;
	case "village":
		if ($session['user']['race']==$race){
			blocknav("runmodule.php?module=bloodbank");
		}
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
		}
		break;
	}
	return $args;
}

function racegobot_checkcity(){
	global $session;
	$race="Gobot";
	if (is_module_active("racerobot")) {
		$city = get_module_setting("villagename", "racerobot");
	} else {
		$city = getsetting("villagename", LOCATION_FIELDS);
	}

	if ($session['user']['race']==$race && is_module_active("cities")){
		//if they're this race and their home city isn't right, set it up.
		if (get_module_pref("homecity","cities")!=$city){ //home city is wrong
			set_module_pref("homecity",$city,"cities");
		}
	}
	return true;
}

function racegobot_run(){

}
?>