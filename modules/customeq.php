<?php

// Custom Weapon and Armor
// 23 Jan 2005
// ver 1.0 by Booger - bigredx (a) sci -dot- fi


require_once("lib/http.php");

function customeq_getmoduleinfo(){
	$info = array(
		"name"=>"Custom Equipment (simplified version)",
		"author"=>"CavemanJoe, based on code by Booger",
		"version"=>"1.0",
		"category"=>"Lodge",
		"download"=>"",
		"settings"=>array(
			"Custom Equipment Module Settings,title",
			"weaponcost"=>"How many points will the first custom weapon cost?,int|100",
			"armorcost"=>"How many points will the first custom armor cost?,int|100",
			"extraweapon"=>"How many points will subsequent weapon changes cost?,int|0",
			"extraarmor"=>"How many points will subsequent armor changes cost?,int|0",
			"permanentarmor"=>"How many donator points for free unlimited armor changes?,int|1000",
			"permanentweapon"=>"How many donator points for free unlimited weapon changes?,int|1000",
		),
		"prefs"=>array(
			"Custom Equipment Preferences,title",
			"weaponname"=>"Players custom weapon,|",
			"armorname"=>"Players custom armor,|",
			"boughtweapon"=>"Player has bought a custom Weapon,bool|0",
			"boughtarmor"=>"Player has bought custom Armor,bool|0",
			"permanentarmor"=>"Player has permanent free armor changes,bool|0",
			"permanentweapon"=>"Player has permanent free weapon changes,bool|0",
		),
	);
	return $info;
}

function customeq_install(){
	module_addhook("lodge");
	module_addhook("pointsdesc");
	module_addhook("charstats");
	return true;
}

function customeq_uninstall(){
	return true;
}

function customeq_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "pointsdesc":
			$args['count']++;
			$format = $args['format'];
			$wcost = get_module_setting("weaponcost");
			$acost = get_module_setting("armorcost");
			$xwcost = get_module_setting("extraweapon");
			$xacost = get_module_setting("extraarmor");
			if ($keep == 0){
				$basestr = translate("A custom %s costs %s points");
				$extrastr = translate(" for the first change and %s points for subsequent changes.");
			}elseif ($keep == 1){
				$basestr = translate("Renaming your %s until you kill the dragon costs %s points.");
			}else {
				$basestr = translate("Renaming your %s until you get a new one costs %s points.");
			}
			if ($xwcost && $keep == 0){
				$wstr = sprintf($basestr.$extrastr, translate_inline("weapon"), $wcost, $xwcost);
				}else{
				$wstr = sprintf($basestr.".", translate_inline("weapon"), $wcost);
			}
			if ($xwcost && $keep == 0){
				$astr = sprintf($basestr.$extrastr, translate_inline("armor"), $acost, $xacost);
			}else{
				$astr = sprintf($basestr.".", translate_inline("armor"), $acost);
			}
			output($format, $wstr, true);
			output($format, $astr, true);
		break;
		case "lodge":
			// $wcost = get_module_setting("extraweapon");
			// if ($wcost < 1 || !$weapon || $keep)
				// $wcost = get_module_setting("weaponcost");
			// $acost = get_module_setting("extraarmor");
			// if ($acost < 1 || !$armor || $keep)
				// $acost = get_module_setting("armorcost");
			// addnav(array("Custom Weapon (%s points)", $wcost),
					// "runmodule.php?module=customeq&op=buy&subop=weapon");
			// addnav(array("Custom Armor (%s points)", $acost),
					// "runmodule.php?module=customeq&op=buy&subop=armor");
			$wcost = get_module_setting("weaponcost");
			$acost = get_module_setting("armorcost");
			$xwcost = get_module_setting("extraweapon");
			$xacost = get_module_setting("extraarmor");
			$boughtwpn = get_module_pref("boughtweapon");
			$boughtarm = get_module_pref("boughtarmor");
			$permwpncost = get_module_setting("permanentweapon");
			$permarmcost = get_module_setting("permanentarmor");
			$haspermwpn = get_module_pref("permanentweapon");
			$haspermarm = get_module_pref("permanentarmor");
			addnav("Custom Equipment");
			//weapon
			if ($haspermwpn){
				addnav("Set custom Weapon (free)","runmodule.php?module=customeq&op=buy&subop=weapon");
			} else {
				if ($boughtwpn){
					addnav(array("Set custom Weapon Name (%s points)",$xwcost),"runmodule.php?module=customeq&op=buy&subop=weapon");
				} else {
					addnav(array("Set custom Weapon Name (%s points)",$wcost),"runmodule.php?module=customeq&op=buy&subop=weapon");
				}
				addnav(array("Get unlimited Weapon Name changes (%s points)",$permwpncost),"runmodule.php?module=customeq&op=permanentweapon");
			}
			//armour
			if ($haspermarm){
				addnav("Set custom Armour (free)","runmodule.php?module=customeq&op=buy&subop=armor");
			} else {
				if ($boughtarm){
					addnav(array("Set custom Armour (%s points)",$xacost),"runmodule.php?module=customeq&op=buy&subop=armor");
				} else {
					addnav(array("Set custom Armour (%s points)",$acost),"runmodule.php?module=customeq&op=buy&subop=armor");
				}
				addnav(array("Get unlimited Armour Name changes (%s points)",$permarmcost),"runmodule.php?module=customeq&op=permanentarmor");
			}
		break;
		case "charstats":
			$weapon = get_module_pref("weaponname");
			$armor = get_module_pref("armorname");
			if ($weapon!=""){
				$session['user']['weapon']=$weapon;
			}
			if ($armor!=""){
				$session['user']['armor']=$armor;
			}
		break;
	}
	return $args;
}

function customeq_form($subop){
	$eq = translate_inline($subop);
	output("What would you like to name your %s?`0`n(leave this blank to disable custom equipment naming and return to default, game-supplied equipment names)", $eq);
	$prev = translate_inline("Preview");
	rawoutput("<form action='runmodule.php?module=customeq&op=preview&subop=".$subop."' method='POST'><input name='newname' value=\"\"> <input type='submit' class='button' value='$prev'></form>");
	addnav("","runmodule.php?module=customeq&op=preview&subop=".$subop);
}

function customeq_run(){
	global $session;
	$op = httpget("op");
	$subop = httpget("subop");
	$weapon = get_module_pref("weaponname");
	$armor = get_module_pref("armorname");
	
	if (get_module_pref("boughtweapon")){
		$wcost = get_module_setting("extraweapon");
	} else {
		$wcost = get_module_setting("weaponcost");
	}
	if (get_module_pref("boughtarmor")){
		$acost = get_module_setting("extraarmor");
	} else {
		$acost = get_module_setting("armorcost");
	}
	
	$pointsavailable = $session['user']['donation'] - $session['user']['donationspent'];
	page_header("Hunter's Lodge");
	
	$permwpncost = get_module_setting("permanentweapon");
	$permarmcost = get_module_setting("permanentarmor");
	
	$haspermwpn = get_module_pref("permanentweapon");
	$haspermarm = get_module_pref("permanentarmor");
	
	if ($haspermwpn){
		$wcost = 0;
	}
	
	if ($haspermarm){
		$acost = 0;
	}
	
	if ($op=="permanentweapon"){
		page_header("Unlimited Custom Weapon Changes");
		output("For %s Donator Points, you can change your Custom Weapon name as often as you like without paying again.`n`n",$permwpncost);
		addnav("Unlimited Changes");
		if ($pointsavailable>=$permwpncost){
			addnav(array("Get permanent free weapon name changes (%s Points)",$permwpncost),"runmodule.php?module=customeq&op=permanentweaponconfirm");
		} else {
			addnav(array("Sorry, but you need %s more Donator Points for this option.",$permwpncost-$pointsavailable),"");
		}
		addnav("Cancel","lodge.php");
		page_footer();
	}
	if ($op=="permanentweaponconfirm"){
		page_header("Unlimited Custom Weapon Changes");
		output("You've got unlimited Custom Weapon Changes!");
		addnav("Back to the Lodge","lodge.php");
		set_module_pref("permanentweapon",1);
		$session['user']['donationspent']+=$permwpncost;
		page_footer();
	}
	if ($op=="permanentarmor"){
		page_header("Unlimited Custom Weapon Changes");
		output("For %s Donator Points, you can change your Custom Armour name as often as you like without paying again.`n`n",$permarmcost);
		addnav("Unlimited Changes");
		if ($pointsavailable>=$permarmcost){
			addnav(array("Get permanent free armour name changes (%s Points)",$permarmcost),"runmodule.php?module=customeq&op=permanentarmorconfirm");
		} else {
			addnav(array("Sorry, but you need %s more Donator Points for this option.",$permarmcost-$pointsavailable),"");
		}
		addnav("Cancel","lodge.php");
		page_footer();
	}
	if ($op=="permanentarmorconfirm"){
		page_header("Unlimited Custom Armour Changes");
		output("You've got unlimited Custom Armour Changes!");
		addnav("Back to the Lodge","lodge.php");
		set_module_pref("permanentarmor",1);
		$session['user']['donationspent']+=$permarmcost;
		page_footer();
	}
	
	
	if ($op == "buy"){
		addnav("L?Return to the Lodge","lodge.php");
		output("`7J. C. Petersen smiles at you, \"`&So, you're interested in purchasing custom equipment.`7\"`n");
		if (($subop == "weapon" && $pointsavailable < $wcost) || ($subop == "armor" && $pointsavailable < $acost)){
			output("`nHe consults his book silently for a moment and then turns to you. \"`&I'm terribly sorry, but you only have %s points available.`7\"`n", $pointsavailable);
			if ($subop == "weapon"){
				output("`n\"`&A custom weapon costs %s points.`7\"`n`n",$wcost);
			}else{
				output("`n\"`&A custom armor costs %s points.`7\"`n`n",$acost);
			}
		}else{
			if ($subop == "weapon"){
				output("`n\"`&A custom weapon costs %s points.`7\"`n`n",$wcost);
			}else{
				output("`n\"`&A custom armor costs %s points.`7\"`n`n",$acost);
			}
			output("\"`&Unfortunately you may not use colors in the name.`7\"`0`n`n");
			customeq_form($subop);
		}
	}elseif ($op == "preview"){
		addnav("L?Return to the Lodge","lodge.php");
		$newname = rawurldecode(httppost("newname"));
		$newname = stripslashes($newname);
		$newname = str_replace("`0", "", $newname);
		$newname = preg_replace("/[+-][0-9]+/", "", $newname);
		$newname = trim($newname);
		$newname = sanitize($newname);
		$eq = translate_inline($subop);
		if ($newname){
			output("`7You have chosen to name your %s %s.`n", $eq, $newname);
			output(" Is this the name you want?`0`n");
			addnav("C?Confirm","runmodule.php?module=customeq&op=confirm&subop=".$subop."&newname=".rawurlencode($newname));
		}else{
			output("`7You did not choose a valid name for your %s!`0`n", $eq);
		}
		addnav("a?Choose another name","runmodule.php?module=customeq&op=buy&subop=".$subop."");
	}elseif ($op == "confirm"){
		addnav("L?Return to the Lodge","lodge.php");
		$newname = rawurldecode(httpget("newname"));
		$newname = stripslashes($newname);
		$eq = translate_inline($subop);
		output("`7Your %s has been changed.`0`n", $eq);
		if ($subop == "weapon"){
			set_module_pref("weaponname",$newname);
			set_module_pref("boughtweapon",true);
			$session['user']['donationspent'] += $wcost;
			debuglog ("spent $wcost lodge points changing weapon to $newname.");
		}else{
			set_module_pref("armorname",$newname);
			set_module_pref("boughtarmor",true);
			$session['user']['donationspent'] += $acost;
			debuglog ("spent $wcost lodge points changing armor to $newname.");
		}
	}
	page_footer();
}
?>
