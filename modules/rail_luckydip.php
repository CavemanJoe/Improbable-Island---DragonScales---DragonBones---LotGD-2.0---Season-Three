<?php

/* Lucky Dip */
/* ver 1.0 by Shannon Brown => SaucyWench -at- gmail -dot- com */
/* 10th Nov 2004 */

/* Rail Lucky Dip */
/* adapted for use with Improbable Island Railway by Sylvia Li */
/* 16th June 2010 */

require_once "modules/rail/lib.php";
require_once "common.php";

function rail_luckydip_getmoduleinfo(){
	$info = array(
		"name"=>"Improbable Railway - Lucky Dip",
		"version"=>"2010-06-16",
		"author"=>"Shannon Brown - adapted by Sylvia Li for Improbable Rail",
		"category"=>"Improbable Rail",
		"settings"=>array(
			"Lucky Dip on the Train - Settings,title",
			"tryallowed"=>"How many tries may the player have?,int|3",
			"cost"=>"Price to play small?,int|2",
			"lcost"=>"Price to play large?,int|5",
			"ridechance"=>"Elias will be riding the train one time out of how many?,int|10",
		),
		"prefs"=>array(
			"Lucky Dip on the Train - User Preferences,title",
			"trytoday"=>"How many times has the player tried today?,int|0",
		),
	);
	return $info;
}

function rail_luckydip_install(){
	module_addhook("newday");
	module_addhook("ironhorse-onboard");
	return true;
}

function rail_luckydip_uninstall(){
	return true;
}

function rail_luckydip_dohook($hookname,$args){
	global $session;
	switch($hookname){
   	case "newday":
		set_module_pref("trytoday",0);
		break;
	case "ironhorse-onboard":
		$hid = $args['hid'];
		$rid = $args['rid'];
		$ridechance=get_module_setting("ridechance");
		if (e_rand(1,$ridechance) == 1){
			output("`2Elias is here! He has set up his brightly-colored bins in the Parlour Car and is being mobbed by at least a dozen excited, laughing children.`0`n`n");
			addnav("Actions");
			addnav("Play Lucky Dip","runmodule.php?module=rail_luckydip&op=start&hid=$hid&rid=$rid");
		}
		break;
	}
	return $args;
}

function rail_luckydip_run() {
	global $session;
	$op = httpget('op');
	$hid = httpget('hid');
	$rid = httpget('rid');
	$cost=get_module_setting("cost");
	$lcost=get_module_setting("lcost");
	$tryallowed=get_module_setting("tryallowed");
	$trytoday=get_module_pref("trytoday");

	page_header("Roving Lucky Dip");
	output("`&`c`bElias and his `2Lucky Dip`b`c`n");
	switch ($op){
		case "start":
			if ($trytoday>=$tryallowed){
				output("`2Much as you'd like to play again, Elias only gives you a friendly nod and turns to the child beside you.`0`n`n");
				addnav("Thank him");
			} elseif($session['user']['gold']<$cost) {
				output("`n`2Much as you'd like to play, your wallet doesn't hold enough to pay for the privilege. Still, there's always time to exchange a few words.`0`n`n");
				addnav("Smile and");
			} else {
				output("`2You peer at the small colored packages with interest.");
				output("Elias stands behind the brightly-colored bins.`n`n");
				output("`2\"`&Hello traveller! ");
				output("So you think yourself lucky? ");
				output("There are many treasures in these boxes!`2\"`n`n");
				output("`2He motions to the smaller box, and then to the larger one. ");
				output("`&\"%s requisition for the small, %s for the large. ",$cost,$lcost);
				output("Who knows what you will find?`2\"`0`n`n");
				addnav("Dip your hand");
				addnav(array("Small (%s req)",$cost),"runmodule.php?module=rail_luckydip&op=small&hid=$hid&rid=$rid");
				if ($session['user']['gold']>=$lcost){
				addnav(array("Large (%s req)",$lcost),"runmodule.php?module=rail_luckydip&op=large&hid=$hid&rid=$rid");
				}
			}
		break;
		case "small":
			$binsize = "small";
		case "large":
			if ($trytoday>=$tryallowed){
				output("`2Much as you'd like to play again, Elias only gives you a friendly nod and turns to the child beside you.`0`n`n");
				addnav("Thank him");
			} elseif($session['user']['gold']<$cost) {
				output("`n`2Much as you'd like to play, your wallet doesn't hold enough to pay for the privilege. Still, there's always time to exchange a few words.`0`n`n");
				addnav("Smile");
			} else {
				$trytoday++;
				set_module_pref("trytoday",$trytoday);

				$gift=(e_rand(1,4));
				if ($binsize=="small") {
					$dipchance=(e_rand(1,25));
					$session['user']['gold']-=$cost;
//					debuglog("spent $cost gold on a lucky dip.");
					output("`7You hand Elias your %s requisition, and reach one arm into the blue and white box. ",$cost);
				}else{
					$dipchance=(e_rand(1,50));		// Elias has a warped sense of humour
					$session['user']['gold']-=$lcost;
//					debuglog("spent $lcost gold on a lucky dip.");
					output("`2You hand Elias your %s requisition, and reach one arm into the red and white box. ",$lcost);
				}
				output("Dragging a package out, you unwrap it with excitement. ");
				output("Elias smiles.`n`n");
				output("`2\"`&So you see, a treasure! ");

				if ($dipchance==1){
					output("A treasure indeed! ");
					output("I hope you shall keep it safe!`2\"`n`n");
					if (rail_collector_findcard()){
						output("`2In your hands is a `6playing card`2!");
					} else {
						output("`2In your hands is a `6calle shell`2!");
						$callecount=get_module_pref("callecount","calletrader");
						$callecount++;
						set_module_pref("callecount",$callecount,"calletrader");
					}
					output("`7You're rather amazed to find such a treasure in a simple lucky dip!");
				}elseif ($gift==4){
					output("I hope you shall keep it safe!\"`n`n");
					output("`2In your hands is a `5cigarette`2!");
					$session['user']['gems']++;
				}elseif ($gift==3){
					output("I hope you shall spend it wisely!`2\"`n`n");
					output("`2You look down to find `610 requisition`2.");
					$session['user']['gold']+=10;
				}elseif ($gift==2){
					output("You shall have hours of joy playing with such a treasure!`2\"`n`n");
					output("`2You look down to find a cheap `6children's toy`2.");
					output("`2You grin -- and play with it amusedly for a few minutes before handing it to the nearest small child, who is delighted.");
				}else{
					output("I hope you enjoy it!\"");
					output("`n`n`7You look down to find a small iced cookie.");
					output("`n`n`^You bite into it with joy!");
					// Don't let it heal them too far
					if ($session['user']['hitpoints'] <=
							$session['user']['maxhitpoints']*1.1) {
						$session['user']['hitpoints']*=1.05;
						output("`@You feel healthy!");
						$addstam=(e_rand(1,4));
						if ($addstam==1) {
							require_once "modules/staminasystem/lib/lib.php";
							addstamina(25000);
							output("`@You feel `@vigorous!");
						}
					}
				}

				addnav("Try again");
				addnav(array("Small (%s req)",$cost),"runmodule.php?module=rail_luckydip&op=small&hid=$hid&rid=$rid");
				if ($session['user']['gold']>=$lcost){
					addnav(array("Large (%s req)",$lcost),"runmodule.php?module=rail_luckydip&op=large&hid=$hid&rid=$rid");
				}
			}
		break;
	}	// end switch
	
	addnav("Leave","runmodule.php?module=rail_ironhorse&op=board&hid=$hid&rid=$rid");
	page_footer();
}

?>
