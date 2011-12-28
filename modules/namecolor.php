<?php
//addnews ready
// mail ready
function namecolor_getmoduleinfo(){
	$info = array(
		"name"=>"Name Colorization",
		"author"=>"Eric Stevens",
		"version"=>"1.0",
		"download"=>"core_module",
		"category"=>"Lodge",
		"settings"=>array(
			"Name Colorization Module Settings,title",
			"initialpoints"=>"How many points will the first color change cost?,int|300",
			"extrapoints"=>"How many points will subsequent color changes cost?,int|25",
			"maxcolors"=>"How many color changes are allowed in names?,int|10",
			"bold"=>"Allow bold?,bool|1",
			"italics"=>"Allow italics?,bool|1",
			"permanent"=>"Cost of unlimited changes,int|1000",
		),
		"prefs"=>array(
			"Name Colorization User Preferences,title",
			"boughtbefore"=>"Has user bought a color change before?,bool|0",
			"permanent"=>"Player has unlimited changes,bool|0",
		),
	);
	return $info;
}

function namecolor_install(){
	module_addhook("lodge");
	module_addhook("pointsdesc");
	return true;
}
function namecolor_uninstall(){
	return true;
}

function namecolor_dohook($hookname,$args){
	global $session;
	switch($hookname){
	case "pointsdesc":
		$args['count']++;
		$format = $args['format'];
		$str = translate("A colored name costs %s points for the first change and %s points for subsequent changes.");
		$str = sprintf($str, get_module_setting("initialpoints"),
				get_module_setting("extrapoints"));
		output($format, $str, true);
		break;
	case "lodge":
		
		// if (get_module_pref("boughtbefore")){
			// $cost = get_module_setting("extrapoints");
		// } else {
			// $cost = get_module_setting("initialpoints");
		// }
		// addnav(array("Colorize Name (%s points)", $cost), "runmodule.php?module=namecolor&op=namechange");
		
		addnav("Name Colour Changes");
		$costfirst = get_module_setting("initialpoints");
		$costsub = get_module_setting("extrapoints");
		$costperm = get_module_setting("permanent");
		$hasperm = get_module_pref("permanent");
		if ($hasperm){
			addnav("Set Custom Name Colours (free)","runmodule.php?module=namecolor&op=namechange");
		} else {
			if (get_module_pref("boughtbefore")) {
				$cost = get_module_setting("extrapoints");
			} else {
				$cost = get_module_setting("initialpoints");
			}
			$cost = sprintf_translate("%s points", $cost);
			addnav(array("Set Custom Name Colours (%s)", $cost),"runmodule.php?module=namecolor&op=namechange");
			addnav(array("Get unlimited Name Colour changes (%s points)", $costperm),"runmodule.php?module=namecolor&op=permanent");
		}
		
		break;
	}
	return $args;
}

function namecolor_form() {
	$regname = get_player_basename();
	output("Your name currently is this:");
	rawoutput($regname);
	output(", which looks like %s`7`n`n", $regname);
	output("How would you like your name to look?`n");
	rawoutput("<form action='runmodule.php?module=namecolor&op=namepreview' method='POST'><input name='newname' value=\"".HTMLEntities($regname, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\"> <input type='submit' class='button' value='Preview'></form>");
	addnav("","runmodule.php?module=namecolor&op=namepreview");
}

function namecolor_run(){
	require_once("lib/sanitize.php");
	require_once("lib/names.php");
	global $session;

	$rebuy = get_module_pref("boughtbefore");
	$cost = get_module_setting( $rebuy ? "extrapoints" : "initialpoints");
	$hasperm = get_module_pref("permanent");
	if ($hasperm){
		$cost = 0;
	}
	$op = httpget("op");
	
	page_header("Hunter's Lodge");
	$pointsavailable=$session['user']['donation']-$session['user']['donationspent'];
	$permcost = get_module_setting("permanent");
	if ($op=="permanent"){
		page_header("Unlimited Name Colour Changes");
		output("For %s Donator Points, you can change your colours as often as you like without paying again.`n`n",$permcost);
		addnav("Unlimited Changes");
		if ($pointsavailable>=$permcost){
			addnav(array("Get permanent free Name Colour changes (%s Points)",$permcost),"runmodule.php?module=namecolor&op=permanentconfirm");
		} else {
			addnav(array("Sorry, but you need %s more Donator Points for this option.",$permcost-$pointsavailable),"");
		}
		addnav("Cancel","lodge.php");
		page_footer();
	}
	if ($op=="permanentconfirm"){
		page_header("Unlimited Name Colour Changes");
		output("You've got unlimited Name Colour Changes!");
		addnav("Back to the Lodge","lodge.php");
		set_module_pref("permanent",1);
		$session['user']['donationspent']+=$permcost;
		page_footer();
	}

	if ($op=="namechange"){
		output("`3`bName Color Change`b`0`n`n");
		output("`7J. C. Petersen smiles at you,");
		if ($hasperm){
			output("\"`&I see you have unlimited changes.  Well, go right ahead.`7\"");
		} else {
			if ($rebuy) {
				output("\"`&Because you have previously spent points on a name color change, subsequent color changes only cost you %s points.`7\"", $cost);
			} else {
				output("\"`&Because this will be your first name color change, it will cost you %s points.  Future name changes will only cost %s points.`7\"",
					$cost, get_module_setting('extrapoints'));
			}
		}
		$pointsavailable = $session['user']['donation'] -
			$session['user']['donationspent'];

		if ($pointsavailable < $cost) {
			output("`n`nHe consults his book silently for a moment and then turns to you. \"`&I'm terribly sorry, but you only have %s points available and a name color change would cost you %s.`7\"", $pointsavailable, $cost);
		} else {
			output("`n`nHe looks you up and down slowly, \"`&Your colorized name *must* contain the same characters as your current name in the same order.  This means that it has to be the same display name, though you may add in or remove colors, or change the capitalization of letters.`7\"`n`n");
			namecolor_form();
		}
		addnav("L?Return to the Lodge","lodge.php");
	}elseif ($op=="namepreview"){
		$regname = get_player_basename();
		$newname = str_replace("`0", "", httppost("newname"));

		if (!get_module_setting("bold")) $newname = str_replace("`b", "", $newname);
		if (!get_module_setting("italics")) $newname = str_replace("`i", "", $newname);
		$newname = preg_replace("/[`][cHw]/", "", $newname);

		$comp1 = strtolower(sanitize($regname));
		$comp2 = strtolower(sanitize($newname));
		$err = 0;
		if ($comp1 != $comp2) {
			if (!$err) output("`3`bInvalid name`b`0`n");
			$err = 1;
			output("Your new name must contain only the same characters as your current name; you can add or remove colors, and you can change the capitalization, but you may not add or remove anything else. You chose %s.`n", $newname);
		}
		if (strlen($newname) > 30) {
			if (!$err) output("`3`bInvalid name`b`0`n");
			$err = 1;
			output("Your new name is too long.  Including the color markups, you are not allowed to exceed 30 characters in length.`n");
		}
		$colorcount = 0;
		for ($x = 0; $x < strlen($newname); $x++) {
			if (substr($newname, $x, 1) == "`") {
				$x++;
				$colorcount++;
			}
		}
		$max = get_module_setting("maxcolors");
		if ($colorcount > $max) {
			if (!$err) output("`3`bInvalid name`b`0`n");
			$err = 1;
			output("You have used too many colors in your name.  You may not exceed %s colors total.`n", $max);
		}
		if (!$err) {
			output("`7Your name will look this this: %s`n`n`7Is this what you wish?`n`n`0", $newname);
			addnav("Confirm Name Change");
			addnav("Yes", "runmodule.php?module=namecolor&op=changename&name=".rawurlencode($newname));
			addnav("No", "runmodule.php?module=namecolor&op=namechange");
		} else {
			output("`n");
			namecolor_form();
			addnav("L?Return to the Lodge","lodge.php");
		}
	} elseif ($op=="changename") {
		$session['user']['donationspent'] += $cost;
		set_module_pref("boughtbefore", 1);
		$fromname = $session['user']['name'];
		$newname = change_player_name(rawurldecode(httpget('name')));
		$session['user']['name'] = $newname;
		addnews("%s`^ has become known as %s.",$fromname,$session['user']['name']);
		output("`7Congratulations, your name is now {$session['user']['name']}`7!`n`n");
		modulehook("namechange", array());
		addnav("L?Return to the Lodge","lodge.php");
	}
	page_footer();
}
?>
