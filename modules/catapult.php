<?php

function catapult_getmoduleinfo(){
	$info = array(
		"name"=>"Improbable Travel Agents",
		"version"=>"2008-06-24",
		"author"=>"Dan Hall, based loosely on the Travel Agent mod by Andrea Britt, which was based on HitchHike by Shannon Brown",
		"category"=>"General",
		"download"=>"",
		"settings"=>array(
			"basecost"=>"Base cost of ticket,int|500",
			"cost"=>"Additonal Cost per level over base cost to travel,int|50",
		),
	);
	return $info;
}
function catapult_install(){
	module_addhook("village");
	return true;
}
function catapult_uninstall(){
	return true;
}
function catapult_dohook($hookname,$args){
	global $session;
	switch($hookname){
	case "village":
		tlschema($args['schemas']['fightnav']);
		addnav($args['fightnav']);
		tlschema();
		addnav("Visit the Travel Agents","runmodule.php?module=catapult&op=examine");
		break;
	}
	return $args;
}
function catapult_run(){
	global $session;
	$thisvillage = $session['user']['location'];
	$vloc = array();
	$vname = getsetting("villagename", LOCATION_FIELDS);
	$vloc[$vname] = "village";
	$vloc = modulehook("validlocation", $vloc);
	ksort($vloc);
	reset($vloc);
	$op = httpget("op");
	$budgetcost = (get_module_setting("cost")*$session['user']['level']+get_module_setting("basecost"));
	$firstclasscost = $budgetcost*2;
	if ($op=="examine") {
		page_header("Improbable Island Travel Agency");
		output("A cheerful-looking woman sits behind a makeshift desk in a small hut.  \"Hi!\" she calls.  \"We can take you to any outpost on the Island.  Only %s Requisition for a first-class ticket, or %s for a budget-class ticket.  Where would you like to go?\"`n`nYou eye her suspiciously.  Your experience with blonde, bespectacled women sat behind large desks has been largely negative.",$firstclasscost,$budgetcost);
		if ($session['user']['gold'] >= ($budgetcost)) {
			if ($session['user']['gold'] >= ($firstclasscost)) {
				addnav("First-Class Travel");
				foreach($vloc as $loc=>$val) {
					if ($loc == $session['user']['location']) continue;
					addnav(array("Go to %s", $loc), "runmodule.php?module=catapult&op=firstclass&cname=".htmlentities($loc));
				}
			}
			addnav("Budget-Class Travel");
			foreach($vloc as $loc=>$val) {
				if ($loc == $session['user']['location']) continue;
				addnav(array("Go to %s", $loc), "runmodule.php?module=catapult&op=budget&cname=".htmlentities($loc));
			}
		}
		addnav("Never Mind");
		addnav("No thanks, I'll walk.","village.php");
	}
	if ($op=="budget") {
		$session['user']['gold']-=$budgetcost;
		$cname = httpget("cname");
		$session['user']['location']=$cname;
		$session['user']['hitpoints'] = $session['user']['hitpoints']*0.01;
		if ($session['user']['hitpoints'] < 1){
			$session['user']['hitpoints'] = 1;
		}
		addnav(array("Dust yourself off and explore %s",$cname),"village.php");
		page_header("Improbable Island Travel Agency");
		output("Mere moments after paying for your ticket, you find yourself mounted atop what can only be described as a bloody enormous catapult.  You look down at the team of midgets stretching out the bungee cords and spinning their elevation wheels so that the crosshairs line up with your chosen destination.`n`n\"You know,\" you call down to the woman in the ticket booth, \"With every passing second, this seems like less and less of a good idea.\"`n`nThe blonde giggles.  \"Nothing to worry about, I assure you!  Just keep your head back, and try to go limp, okay?\"`n`n\"Actually,\" you protest in what you hope is a very polite tone of voice as the blonde positions herself behind you, one hand on the release lever, \"I really think that I'd like to change my mi`iYEEEEEEEEEEEEeeeeeeeeeeaaaaaaaaaaaarrrrghheythisisactuallykindafun`i`bOW`b!\"`n`nYou roll with the fall and lie staring at the sun, bleeding copiously all over the shop.`n`n`bYou have arrived at your destination`b.  Better get your sorry, broken arse to a hospital tent.");
	}
	if ($op=="firstclass") {
		$session['user']['gold']-=$firstclasscost;
		$cname = httpget("cname");
		$session['user']['location']=$cname;
		$session['user']['hitpoints'] = $session['user']['hitpoints']*0.9;
		if ($session['user']['hitpoints'] < 1){
			$session['user']['hitpoints'] = 1;
		}
		addnav(array("Dust yourself off and explore %s",$cname),"village.php");
		page_header("Improbable Island Travel Agency");
		output("Mere moments after paying for your ticket, you find yourself mounted atop what can only be described as a bloody enormous catapult.  You look down at the team of midgets stretching out the bungee cords and spinning their elevation wheels so that the crosshairs line up with your chosen destination.`n`n\"Could you explain to me,\" you call down to the woman in the ticket booth, \"What the difference is between first-class and budget-class?\"`n`nThe blonde grins.  \"Well, with a first class ticket, if you land in a tree it'll be by accident rather than design.\"`n`n\"Actually,\" you protest in what you hope is a very polite tone of voice as the blonde positions herself behind you, one hand on the release lever, \"I really think that I'd like to change my mi`iYEEEEEEEEEEEEeeeeeeeeeeaaaaaaaaaaaarrrrghheythisisactuallykindafun`i`bOW`b!\"`n`nYou roll with the fall and lie staring at the sun, in marginally less pain than you would have been on a budget ticket.`n`n`bYou have arrived at your destination`b.");
	}
	page_footer();
	return $args;
}
?>