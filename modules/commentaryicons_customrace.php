<?php

function commentaryicons_customrace_getmoduleinfo(){
	$info = array(
		"name"=>"Commentary Icons: Custom Race",
		"version"=>"2010-06-29",
		"author"=>"Dan Hall",
		"category"=>"Commentary Icons",
		"download"=>"",
		"settings"=>array(
			"Custom Race Module Settings,title",
			"initialpoints"=>"How many donator points needed to get first custom race change?,int|500",
			"extrapoints"=>"How many additional donator points needed for subsequent custom race changes?,int|25",
			"permanent"=>"How many donator points for free unlimited race changes?,int|1000",
			"totalpoints"=>"Total number of points spent on this module,int|0",
		),
		"prefs"=>array(
			"Custom Race User Preferences,title",
			"customrace"=>"The player's Custom Race,text",
			"permanent"=>"Player has permanent free changes,bool|0",
			"numchanges"=>"Number of times player has changed their custom race,int|0",
		),
	);
	return $info;
}
function commentaryicons_customrace_install(){
	module_addhook_priority("postcomment",100);
	module_addhook("lodge");
	module_addhook("pointsdesc");
	return true;
}
function commentaryicons_customrace_uninstall(){
	return true;
}
function commentaryicons_customrace_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "postcomment":
			$race = get_module_pref("customrace");
			if ($race || $race!=""){
				if ($session['user']['sex'] == 0){
					$gendertitle = "Male";
				} else {
					$gendertitle = "Female";
				}
				$args['info']['icons']['race']=array(
					'icon' => "images/icons/races/customrace.png",
					'mouseover' => $gendertitle." ".$race,
				);
			}
			break;
		case "lodge":
			$costfirst = get_module_setting("initialpoints");
			$costsub = get_module_setting("extrapoints");
			$costperm = get_module_setting("permanent");
			$hasperm = get_module_pref("permanent");
			addnav("Custom Race");
			if (!$hasperm){
				$playerpoints = $session['user']['donation']-$session['user']['donationspent'];
				if (get_module_pref("numchanges")){
					if ($playerpoints<$costsub){
						addnav(array("Change Custom Race (%s Points)",$costsub),"");
					} else {
						addnav(array("Change Custom Race (%s Points)",$costsub),"runmodule.php?module=commentaryicons_customrace&op=change");
					}
				} else {
					if ($playerpoints >= $costfirst){
						addnav(array("Set Custom Race (%s Points)",$costfirst),"runmodule.php?module=commentaryicons_customrace&op=firstchange");
					} else {
						addnav(array("Set Custom Race (%s Points)",$costfirst),"");
					}
				}
				addnav(array("Get permanent free Custom Race changes (%s Points)",$costperm),"runmodule.php?module=commentaryicons_customrace&op=permanent");
			} else {
				addnav("Set Custom Race (free)","runmodule.php?module=commentaryicons_customrace&op=change");
			}
		break;
		case "pointsdesc":
			$args['count']++;
			$format = $args['format'];
			$str = "The ability to choose a custom Race description for commentary areas costs %s points initially and %s points for every change thereafter.";
			$str = translate($str);
			$str = sprintf($str, get_module_setting("initialpoints"), get_module_setting("extrapoints"));
			output($format, $str, true);
		break;
		}
	return $args;
}

function commentaryicons_customrace_run(){
	require_once("lib/sanitize.php");
	require_once("lib/names.php");
	global $session;
	$op = httpget("op");

	page_header("Custom Races");
	
	$costfirst = get_module_setting("initialpoints");
	$costsub = get_module_setting("extrapoints");
	$costperm = get_module_setting("permanent");
	$hasperm = get_module_pref("permanent");
	$playerpoints = $session['user']['donation']-$session['user']['donationspent'];
	
	switch($op){
		case "change":
			output("Want to change your Custom Race?  No problem.  Enter your desired race in the box below.  You've got 25 characters to play around with.`n(leave this blank to disable custom race naming and return to default, game-supplied race names)`n`n");
			rawoutput("<form action='runmodule.php?module=commentaryicons_customrace&op=confirm&sub=change' method='POST'>");
			$race = get_module_pref("customrace");
			rawoutput("<input id='input' name='newrace' width='25' maxlength='25' value='".htmlentities($race, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."'>");
			rawoutput("<input type='submit' class='button' value='Preview'>");
			rawoutput("</form>");
			addnav("", "runmodule.php?module=commentaryicons_customrace&op=confirm&sub=change");
		break;
		case "firstchange":
			output("Changing your Custom Race will affect how other players see you.  In commentary areas, your race will show up as whatever you select here (the icon will be a little silver question mark, with the name of your race embedded in hover text like the other Race icons).  In the game mechanics, your race will be unchanged.  Think of it like a costume; you'll still be a %s inside, but your friends will think of you as whatever you enter in the box below.  You've got 25 characters to play around with.`n(leave this blank to disable custom race naming and return to default, game-supplied race names)`n`n",$session['user']['race']);
			rawoutput("<form action='runmodule.php?module=commentaryicons_customrace&op=confirm&sub=firstchange' method='POST'>");
			$race = get_module_pref("customrace");
			rawoutput("<input id='input' name='newrace' width='25' maxlength='25' value='".htmlentities($race, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."'>");
			rawoutput("<input type='submit' class='button' value='Preview'>");
			rawoutput("</form>");
			addnav("", "runmodule.php?module=commentaryicons_customrace&op=confirm&sub=firstchange");
		break;
		case "permanent":
			output("Buying permanent free changes means you'll pay %s points now, and then you can change your custom race as often as you like without paying again.`n`n",$costperm);
			addnav("Unlimited Custom Race changes");
			if ($playerpoints>=$costperm){
				addnav(array("Buy permanent access (%s Points)",$costperm),"runmodule.php?module=commentaryicons_customrace&op=set&type=perm");
			} else {
				addnav(array("Sorry, but you need %s more points to do that.",$costperm-$playerpoints),"");
			}
			addnav("Cancel","lodge.php");
		break;
		case "confirm":
			$newrace = httppost("newrace");
			$sub = httpget("sub");
			$newrace = str_replace("`","",$newrace);
			$newrace = comment_sanitize($newrace);
			$newrace = substr($newrace,0,25);
			output("Your new custom race is:`n%s`nWould you like to set your new Race now?",$newrace);
			addnav("Confirm");
			switch ($sub){
				case "change":
					if ($hasperm){
						addnav("Set Custom Race (free)","runmodule.php?module=commentaryicons_customrace&op=set&newrace=".rawurlencode($newrace)."&type=change");
					} else {
						addnav(array("Set Custom Race (%s Points)",$costsub),"runmodule.php?module=commentaryicons_customrace&op=set&newrace=".rawurlencode($newrace)."&type=change");
					}
				break;
				case "firstchange":
					if ($hasperm){
						addnav("Set Custom Race (free)","runmodule.php?module=commentaryicons_customrace&op=set&newrace=".rawurlencode($newrace)."&type=change");
					} else {
						addnav(array("Set Custom Race (%s Points)",$costfirst),"runmodule.php?module=commentaryicons_customrace&op=set&newrace=".rawurlencode($newrace)."&type=firstchange");
					}
				break;
			}
		break;
		case "set":
			$newrace = rawurldecode(httpget("newrace"));
			switch (httpget("type")){
				case "change":
					output("Your custom race has been set to %s!`n`n",$newrace);
					set_module_pref("customrace",$newrace);
					increment_module_pref("numchanges");
					if (!get_module_pref("permanent")){
						$session['user']['donationspent']+=$costsub;
						increment_module_setting("totalpoints",$costsub);
					}
				break;
				case "firstchange":
					output("Your custom race has been set to %s!`n`n",$newrace);
					set_module_pref("customrace",$newrace);
					increment_module_pref("numchanges");
					if (!get_module_pref("permanent")){
						$session['user']['donationspent']+=$costfirst;
						increment_module_setting("totalpoints",$costfirst);
					}
				break;
				case "perm":
					output("You've got permanent free custom race changes!  Woo!`n`n");
					set_module_pref("permanent",1);
					$session['user']['donationspent']+=$costperm;
				break;
			}
		break;
	}
	addnav("Return");
	addnav("L?Return to the Lodge","lodge.php");
	page_footer();


}
?>