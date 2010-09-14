<?php
// This file will let you restrict a set of skins to donators only.
// This is ideal if you use advertising to offset your server bill, but don't want to have to show adverts to contributing players.
// Players can buy access to ad-free skins at a cost of 1dp per game day.
// You can set how many Donator Points it costs to gain permanent access to the Donator-Only skins.
// You can also make this feature free for anyone with more than a certain number of spent or unspent Donator Points.
// Any skin which has "donator" in its title will be affected by this module.  For example, a skin called "improbable-donator.htm" will be locked out unless a player has access to it.
function donatorskin_getmoduleinfo(){
	$info = array(
		"name"=>"Donator Skin",
		"author"=>"Dan Hall",
		"version"=>"2008-08-05",
		"download"=>"fix_this",
		"category"=>"Lodge",
		"settings"=>array(
			"Permanent Account Module Settings,title",
			"freebiepoints"=>"How many donator points must the player have accumulated - spent or unspent - before donator skins become free forever?,int|5000",
			"buyingpoints"=>"How many donator points does it cost to buy permanent access to donator skins if the player has less points accumulated than is specified above?,int|1000",
			"permanentpurchased"=>"How many players have bought this item from the Lodge in exchange for Donator Points, IE not by getting it for free?,int|0",
			"dayspurchased"=>"How many ad-free game days have been purchased, not counting permanent purchases?,int|0",
		),
		"prefs"=>array(
			"Donator Skin User Preferences,title",
			"haspermanent"=>"Does this player have permanent access to the donator skins?,int|0",
			"daysleft"=>"How many game days does this player have left?,int|0",
		),
	);
	return $info;
}

function donatorskin_install(){
	module_addhook("lodge");
	module_addhook("pointsdesc");
	module_addhook("village");
	module_addhook("forest");
	module_addhook("newday");
	return true;
}
function donatorskin_uninstall(){
	return true;
}

function donatorskin_dohook($hookname,$args){
	global $session;
	$buycost = get_module_setting("buyingpoints");
	$freecost = get_module_setting("freebiepoints");
	$haspermanent = get_module_pref("haspermanent");
	$daysleft = get_module_pref("daysleft");
	switch($hookname){
	case "pointsdesc":
		$args['count']++;
		$format = $args['format'];
		$str = "Buy permanent access to ad-free and donator-only display skins for %s Donator Points, or just buy temporary access for 1dp per game day.  Once you've accumulated more than %s points in total, spent or unspent, this purchase becomes free.";
		$str = translate($str);
		$str = sprintf($str, get_module_setting("buyingpoints"),
				get_module_setting("freebiepoints"));
		output($format, $str, true);
		break;
	case "lodge":
		if ($haspermanent==1){
			break;
		};
		$cost = "";
		$buycost = get_module_setting("buyingpoints");
		$freecost = get_module_setting("freebiepoints");
		if (get_module_setting("freebiepoints") <= $session['user']['donation']){
			$cost = translate_inline("free");
		}
		if (get_module_setting("freebiepoints") > $session['user']['donation']){
			$cost = sprintf_translate("%s points for permanent access, or 1 point per game day", $buycost);
		}
		addnav(array("Donator-Only Skins (%s)", $cost),
				"runmodule.php?module=donatorskin&op=donatorskin");
		break;
	case "village":
		if ($haspermanent==0 && $daysleft==0){
			$currenttheme=$_COOKIE['template'];
			$contains='donator';
			$pos = strpos($currenttheme, $contains);
			if ($pos === false) {
			    break;
			} else {
				output("`n`b`0It looks like you're using a donator-only display skin.  But you've either run out of days, or you haven't bought any!  You can buy access to Donator-Only skins in the Hunter's Lodge.  Setting your skin back to the system default now.`b`n");
				setcookie("template","",strtotime("+45 days"));
			}
		}
		break;
	case "forest":
		if ($haspermanent==0 && $daysleft==0){
			$currenttheme=$_COOKIE['template'];
			$contains='donator';
			$pos = strpos($currenttheme, $contains);
			if ($pos === false) {
			    break;
			} else {
				output("`n`b`0It looks like you're using a donator-only display skin.  But you've either run out of days, or you haven't bought any!  You can buy access to Donator-Only skins in the Hunter's Lodge.  Setting your skin back to the system default now.`b`n");
				setcookie("template","",strtotime("+45 days"));
			}
		}
		break;
	case "newday":
		if ($haspermanent==0 && $daysleft>0){
			$daysleft--;
			set_module_pref("daysleft",$daysleft);
			output("`n`nYou have `b%s`b game days of access to Donator-Only skins left.  More days can be obtained at the Hunter's Lodge.`n",$daysleft);
		}
		if ($haspermanent==0 && $daysleft==0){
			$currenttheme=$_COOKIE['template'];
			$contains='donator';
			$pos = strpos($currenttheme, $contains);
			if ($pos === false) {
			    break;
			} else {
				output("`n`b`0It looks like you're using a donator-only display skin.  But you've either run out of days, or you haven't bought any!  You can buy access to Donator-Only skins in the Hunter's Lodge.  Setting your skin back to the system default now.`b`n");
				setcookie("template","",strtotime("+45 days"));
			}
		}
		break;
	}
	return $args;
}

function donatorskin_run(){
	global $session;
	$op = httpget("op");
	$buycost = get_module_setting("buyingpoints");
	$freecost = get_module_setting("freebiepoints");
	$totaldonation = $session['user']['donation'];
	$donationsleft = ($session['user']['donation'] - $session['user']['donationspent']);
	page_header("Hunter's Lodge");
	if ($op=="donatorskin"){
		output("`3`bDonator-Only Display Skins`b`0`n`n");		
		if ($totaldonation < $freecost){
			if ($donationsleft>=$buycost){
				output("`7You have enough points to obtain permanent access to Donator-Only Skins.  This procedure will cost %s Donator Points.  Just click the button and you're all set.`n`n",$buycost);
				addnav("Give me permanent access!","runmodule.php?module=donatorskin&op=permanent");
			}
			output("Individual days days of access to Donator-Only skins are available, at a cost of one Donator Point per day.");
			if ($donationsleft>=1){
			addnav("Enable Donator-Only Skins for today","runmodule.php?module=donatorskin&op=buy1");
			}
			if ($donationsleft>=10){
			addnav("Enable Donator-Only Skins for ten days","runmodule.php?module=donatorskin&op=buy10");
			}
			if ($donationsleft>=100){
			addnav("Enable Donator-Only Skins for a hundred days","runmodule.php?module=donatorskin&op=buy100");
			}
		}
		if ($totaldonation >= $freecost){
			output("`7Because you have earned sufficient points, you can now get access to Donator-Only Skins for free.  Just click the button and you're all set.");
			addnav("Give me permanent access!","runmodule.php?module=donatorskin&op=permanent");
		}
		addnav("Cancel and Return to the Lodge","lodge.php");
	}
	if ($op=="permanent"){
		output("`3`bPermanent Access to Donator Skins`b`0`n`n");
		output("`7Congratulations, you now have permanent access to Donator-Only Skins.  Head on into your Preferences menu and choose any skin you like!");
		addnav("Return to the Lodge","lodge.php");
		if ($totaldonation < $freecost){
			//take the money and run
			set_module_setting("permanentpurchased", get_module_setting("permanentpurchased")+1);
			$session['user']['donationspent']+=get_module_setting("buyingpoints");
		}
		//Actually give the permanent access
		set_module_pref("haspermanent",1);
	}
	if ($op=="buy1"){
		output("`3`bDonator Skins`b`0`n`n");
		output("`7You just bought a single day of access to Donator-Only Skins.  Head on into your Preferences menu and choose any skin you like!");
		addnav("Return to the Lodge","lodge.php");
		if ($totaldonation>0){
			//take the money and run
			set_module_setting("dayspurchased", get_module_setting("dayspurchased")+1);
			$session['user']['donationspent']+=1;
			set_module_pref("daysleft", get_module_pref("daysleft")+1);
		}
	}
	if ($op=="buy10"){
		output("`3`bDonator Skins`b`0`n`n");
		output("`7You just bought ten days of access to Donator-Only Skins.  Head on into your Preferences menu and choose any skin you like!");
		addnav("Return to the Lodge","lodge.php");
		if ($totaldonation>=10){
			//take the money and run
			set_module_setting("dayspurchased", get_module_setting("dayspurchased")+10);
			$session['user']['donationspent']+=10;
			set_module_pref("daysleft", get_module_pref("daysleft")+10);
		}
	}
	if ($op=="buy100"){
		output("`3`bDonator Skins`b`0`n`n");
		output("`7You just bought a hundred days of access to Donator-Only Skins.  Head on into your Preferences menu and choose any skin you like!");
		addnav("Return to the Lodge","lodge.php");
		if ($totaldonation>=100){
			//take the money and run
			set_module_setting("dayspurchased", get_module_setting("dayspurchased")+100);
			$session['user']['donationspent']+=100;
			set_module_pref("daysleft", get_module_pref("daysleft")+100);
		}
	}
	output("`n`nTechnical note: the information telling the game what display skin to use is stored in a cookie on your computer, not in the game's database.  If you play Improbable Island on multiple computers or devices, be sure to set your skin on each machine that you use.");
	page_footer();
}
?>
