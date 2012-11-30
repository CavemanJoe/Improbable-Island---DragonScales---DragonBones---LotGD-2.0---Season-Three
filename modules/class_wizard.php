<?php

function class_wizard_getmoduleinfo(){
	$info = array(
		"name" => "Class - Wizard",
		"author" => "Dan Hall, based on generic speciality files by Eric Stevens et al, text modifcations by Cousjava",
		"version" => "2008-11-22",
		"download" => "",
		"category" => "Classes",
		"prefs" => array(
			"Class - Wizard User Prefs,title",
			"primary"=>"Energy replenished at newday,int|100",
			"secondary"=>"Energy left in the body. This is non-replacable,int|500",
			"status"=>"Status of the spells - casting or not,int|0",
			"powerlevel"=>"Current selected spell power output,int|1",
		),
	);
	return $info;
}

function class_wizard_install(){
	$condition = "if (\$session['user']['specialty'] == \"WS\") {return true;} else {return false;};";
	module_addhook("choose-specialty");
	module_addhook("set-specialty");
	module_addhook("fightnav-specialties",false,$condition);
	module_addhook("apply-specialties",false,$condition);
	module_addhook("newday",false,$condition);
	module_addhook("specialtynames");
	module_addhook("specialtymodules");
	module_addhook("specialtycolor");
	module_addhook("dragonkill");
	module_addhook("battle-victory",false,$condition);
	module_addhook("battle-defeat",false,$condition);
	module_addhook("forest",false,$condition);
	module_addhook("village",false,$condition);
	module_addhook("worldnav",false,$condition);
	return true;
}

function class_wizard_uninstall(){
	// Reset the specialty of anyone who had this specialty so they get to
	// rechoose at new day
	$sql = "UPDATE " . db_prefix("accounts") . " SET specialty='' WHERE specialty='WS'";
	db_query($sql);
	return true;
}

function class_wizard_dohook($hookname,$args){
	global $session,$resline;

	$spec = "WS";
	$name = "Wizard";
	$ccode = "`$";

	switch ($hookname) {
	case "dragonkill":
		set_module_pref("primary", 0);
		set_module_pref("secondary", 0);
		set_module_pref("status", 0);
		break;
	case "choose-specialty":
		if ($session['user']['dragonkills'] < 5) {
			break;
		}
		if ($session['user']['specialty'] == "" || $session['user']['specialty'] == '0') {
			addnav("$ccode$name`0","newday.php?setspecialty=$spec$resline");
			output("\"`5You can chose to be a`\$Wizard`5. You must be very careful with this one. You get a small amount of magical energy each day to cast spells, and you also have your own inate magical energies. But once you've used it, its gone.\"`n`n");
		}
		break;
	case "set-specialty":
		if($session['user']['specialty'] == $spec) {
			page_header($name);
			output("`\$You delve into the mysteries of the arcane and learn to cast powerful spells.`n`n");
			//output("It's painted black, and features a small aperature on the front.  The warning symbols for Laser Radiation and Oxidizing Element are displayed in yellow triangles just above your ear.`n`n");
			output("You learn that you can cast spells of any power, but using too powerful spells will quickly use up all your daily energy and start working its way into your life force. `n`nYour teacher's warning s about using up your life force come back to haunt you; use it sparingly, because it has to last you for a long time..");
			clear_module_pref("primary");
			clear_module_pref("secondary");
		}
		break;
	case "specialtycolor":
		$args[$spec] = $ccode;
		break;
	case "specialtynames":
		$args[$spec] = translate_inline($name);
		break;
	case "specialtymodules":
		$args[$spec] = "classwizard";
		break;
	case "newday":
		if ($session['user']['specialty'] == $spec) {
			output("`nYou feel refreshed, and ready to cast more spells today.`n");
			$primary = ($session['user']['level']*2)+20;
			set_module_pref("primary",$primary);
			set_module_pref("status",0);
		}
		set_module_pref("status",0);
		strip_buff('headlaser');
		break;
	case "forest":
		if($session['user']['specialty'] == $spec) {
			if (get_module_pref("primary") > 0 || get_module_pref("secondary") > 0){
				addnav(array("$ccode `bSWizard Abilities`b`0",""));
				addnav(array("Energy: (%s/%s)`0", get_module_pref("primary"), get_module_pref("secondary")),"");
				addnav(array("Spell power: %sKw`0", get_module_pref("powerlevel")),"");
				addnav("Increase spell power`0","runmodule.php?module=implantlaser&op=inc&from=forest");
				if (get_module_pref("powerlevel")>1){
					addnav("Decrease spell power`0","runmodule.php?module=implantlaser&op=dec&from=forest");
				}
			}
		}
		break;
	case "village":
		if($session['user']['specialty'] == $spec) {
			if (get_module_pref("primary") > 0 || get_module_pref("secondary") > 0){
				addnav(array("$ccode `bSWizard Abilities`b`0",""));
				addnav(array("Energy: (%s/%s)`0", get_module_pref("primary"), get_module_pref("secondary")),"");
				addnav(array("Spell power: %sKw`0", get_module_pref("powerlevel")),"");
				addnav("Increase spell power`0","runmodule.php?module=implantlaser&op=inc&from=village");
				if (get_module_pref("powerlevel")>1){
					addnav("Decrease spell power`0","runmodule.php?module=implantlaser&op=dec&from=village");
				}
			}
			set_module_pref("status",0);
			strip_buff('wizardspell');
		}
		break;
	case "worldnav":
		if($session['user']['specialty'] == $spec) {
			if (get_module_pref("primary") > 0 || get_module_pref("secondary") > 0){
				addnav(array("$ccode `bWizard Abilities`b`0",""));
				addnav(array("Energy: (%s/%s)`0", get_module_pref("primary"), get_module_pref("secondary")),"");
				addnav(array("Spell power: %sKw`0", get_module_pref("powerlevel")),"");
				addnav("Increase spell power`0","runmodule.php?module=implantlaser&op=inc&from=worldnav");
				if (get_module_pref("powerlevel")>1){
					addnav("Decrease spell power`0","runmodule.php?module=implantlaser&op=dec&from=worldnav");
				}
			}
			set_module_pref("status",0);
			strip_buff('wizardspell');
		}
		break;
	case "fightnav-specialties":
		if($session['user']['specialty'] == $spec) {
			// Evaluate the number of rounds that the battle has lasted thus far.  Because this is only called once per click, and the user can choose to play five rounds, ten rounds or to the end of the fight, we've got to get the number of rounds by looking at the remaining rounds left in the buff we set up the last time the user clicked to fight.
			if (has_buff("wizardspell")) {
				$roundsplayed = 1000 - $session['bufflist']['wizardspell-roundtrack']['rounds'];
				set_module_pref("primary", get_module_pref("primary") - (get_module_pref("powerlevel") * $roundsplayed));
				if (get_module_pref("primary") < 0){
					$discrepancy = get_module_pref("primary");
					$discrepancy = $discrepancy - $discrepancy - $discrepancy;
					debug ($discrepancy);
					set_module_pref("secondary", get_module_pref("secondary") - $discrepancy);
					set_module_pref("primary",0);
					if (get_module_pref("secondary") < 0){
						set_module_pref("secondary",0);
						set_module_pref("status",0);
						strip_buff('wizardspell');
						strip_buff('wizardspell-roundtrack');
					}
				}
			} else {
				$roundsplayed = 0;
				set_module_pref("status",0);
			}
			apply_buff('wizardspell-roundtrack',array(
				"rounds"=>1000,
				"dmgmod"=>1,
			));
			$script = $args['script'];
			$primary = get_module_pref("primary");
			$secondary = get_module_pref("secondary");
			if ($primary > 0 || $secondary > 0) {
				addnav(array("$ccode `bWizard Abilities`b`0",""));
				addnav(array("Energy: %s (%s)`0", $primary, $secondary),"");
				addnav(array("Spell power: %sKw`0", get_module_pref("powerlevel")),"");
				if (get_module_pref("status") == 0){
					if (($primary+$secondary) > get_module_pref("powerlevel")){
					addnav(array("$ccode &#149; Start casting spells`0"),
							$script."op=fight&skill=$spec&l=on", true);
					} else {
						addnav("Not enough energy to cast spells","");
					}
					addnav(array("$ccode &#149; Increase spell power`0"),
							$script."op=fight&skill=$spec&l=inc", true);
					if (get_module_pref("powerlevel")>1){
						addnav(array("$ccode &#149; Decrease spell power`0"),
								$script."op=fight&skill=$spec&l=dec", true);
					}
				}
				if (get_module_pref("status") == 1){
					addnav(array("$ccode &#149; Stop casting spells`0"),
							$script."op=fight&skill=$spec&l=off", true);
				}
			}
		}
		break;
	case "apply-specialties":
		if($session['user']['specialty'] == $spec) {
			$skill = httpget('skill');
			$l = httpget('l');
			if ($skill==$spec){
				switch($l){
				case "inc":
					set_module_pref("powerlevel",get_module_pref("powerlevel")+1);
					output("`\$You prepare yourself carefuly and get ready to cast stronger spells. You have `bincreased`b your spells's output power by one thaum, taking it up to %s.`n",get_module_pref("powerlevel"));
					break;
				case "dec":
					set_module_pref("powerlevel",get_module_pref("powerlevel")-1);
					output("`\$You prepare yourself carefuly and get ready to cast weaker spells. You have `bincreased`b your spells's output power by one thaum, taking it up to %s.`n",get_module_pref("powerlevel"));
					break;
				case "on":
					if (get_module_pref("secondary")>0 || get_module_pref("primary")>0){
						apply_buff('wizardspell',array(
							"startmsg"=>"`\$Your cast a ball of magical energy at {badguy}!",
							"name"=>"`\$Wizard Spells",
							"effectmsg"=>"`\${badguy} yelps in pain as the energy burns into its body, doing `^{damage}`\$ points' worth of damage!",
							"rounds"=>-1,
							"minioncount"=>1,
							"minbadguydamage"=>ceil(get_module_pref("powerlevel")/2),
							"maxbadguydamage"=>ceil(get_module_pref("powerlevel")*2),
							"schema"=>"module-classwizard"
						));
						set_module_pref("status",1);
					} else {
						output("Your are completly exhausted of magical energies, and cannot cast any spells!");
					}
					break;
				case "off":
					set_module_pref("status",0);
					strip_buff('wizardspell');
					break;
				}
			}
		}
		break;
	case "battle-defeat":
	case "battle-victory":
		if($session['user']['specialty'] == $spec) {
			if (get_module_pref("status")==1){
				set_module_pref("status",0);
				if (has_buff("wizardspell")) {
					$roundsplayed = 1001 - $session['bufflist']['headlaser-roundtrack']['rounds'];
					set_module_pref("primary", get_module_pref("primary") - (get_module_pref("powerlevel") * $roundsplayed));
					if (get_module_pref("primary") < 0){
						set_module_pref("secondary", get_module_pref("secondary") - (get_module_pref("powerlevel") * $roundsplayed));
						set_module_pref("primary",0);
						if (get_module_pref("secondary") < 0){
							set_module_pref("secondary",0);
							set_module_pref("status",0);
							strip_buff('wizardspell');
							strip_buff('wizardspell-roundtrack');
						}
					}
				}
				output("`n`\$You stop casting spells, as there is nothing to attack.`n");
			}
			strip_buff('wizardspell');
		}
		break;
	}
	return $args;
}

function class_wizard_run(){
	global $session;
	page_header("Keep playing with maigc like that and you'll go blind");
	switch (httpget("op")){
		case "inc":
			set_module_pref("powerlevel",get_module_pref("powerlevel")+1);
			output("You prepare yourself carefuly and get ready to cast stronger spells. You have `bincreased`b your spells's output power by one thaum, taking it up to %s.",get_module_pref("powerlevel"));
			break;
		case "dec":
			set_module_pref("powerlevel",get_module_pref("powerlevel")-1);
			output("You prepare yourself carefuly and get ready to cast weaker spells.  You have `bdecreased`b your spells's output power by one thaum, taking it down to %s.",get_module_pref("powerlevel"));
			break;
		}
	switch (httpget("from")){
		case "forest":
			addnav("Increase spell power`0","runmodule.php?module=implantlaser&op=inc&from=forest");
			if (get_module_pref("powerlevel")>1){
				addnav("Decrease spell power`0","runmodule.php?module=implantlaser&op=dec&from=forest");
			}
			addnav("Stop messing with things you don't understand and go back to the Jungle","forest.php");
			break;
		case "village":
			addnav("Increase spell power`0","runmodule.php?module=implantlaser&op=inc&from=village");
			if (get_module_pref("powerlevel")>1){
				addnav("Decrease spell power`0","runmodule.php?module=implantlaser&op=dec&from=village");
			}
			addnav("Stop messing with things you don't understand and go back to the Outpost","village.php");
			break;
		case "worldnav":
			addnav("Increase spell power`0","runmodule.php?module=implantlaser&op=inc&from=worldnav");
			if (get_module_pref("powerlevel")>1){
				addnav("Decrease spell power`0","runmodule.php?module=implantlaser&op=dec&from=worldnav");
			}
			addnav("Stop messing with things you don't understand and go back to the Map","runmodule.php?module=worldmapen&op=continue");
			break;
		}
	page_footer();
}
?>
