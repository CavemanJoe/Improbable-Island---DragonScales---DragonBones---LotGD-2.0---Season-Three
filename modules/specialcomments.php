<?php

function specialcomments_getmoduleinfo(){
	$info = array(
		"name"=>"Special Comments",
		"version"=>"2009-03-15",
		"author"=>"Dan Hall",
		"category"=>"Experimental",
		"download"=>"",
		"prefs"=>array(
			"Special Comments,title",
			"commentsleft"=>"How many Special Comments does this player have left?,int|0",
		),
	);
	return $info;
}
function specialcomments_install(){
	module_addhook("commentary");
	module_addhook("lodge");
	module_addhook("postcomment");
	return true;
}
function specialcomments_uninstall(){
	return true;
}
function specialcomments_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "lodge":
			addnav("Use Points");
			$donationsleft = ($session['user']['donation'] - $session['user']['donationspent']);
			addnav("Special Comments");
			if ($donationsleft >= 25){
				addnav("One Special Comment (25 Points)","runmodule.php?module=specialcomments&op=buy1");
			} else {
				addnav("One Special Comment (25 Points)","");
			}
			if ($donationsleft >= 100){
				addnav("Five Special Comments (100 Points)","runmodule.php?module=specialcomments&op=buy5");
			} else {
				addnav("Five Special Comments (100 Points)","");
			}
			if ($donationsleft >= 1000){
				addnav("Sixty Special Comments (1000 Points)","runmodule.php?module=specialcomments&op=buy60");
			} else {
				addnav("Sixty Special Comments (1000 Points)","");
			}
			if ($donationsleft >= 2000){
				addnav("Two hundred Special Comments (2000 Points)","runmodule.php?module=specialcomments&op=buy200");
			} else {
				addnav("Two hundred Special Comments (2000 Points)","");
			}
			break;
		case "pointsdesc":
			$args['count']++;
			$format = $args['format'];
			$str = translate("Use Special Comments - these are comments that you can post in the chat areas without your name attached.  Very useful for special effects, roleplaying, and general mischief.");
			$str = sprintf($str, 25, 100);
			output($format, $str, true);
			break;
		case "postcomment":
			require_once("lib/commentary.php");
			if (substr($args['commentline'], 0, 8) == "/special"){
				if (get_module_pref("commentsleft") > 0){
					increment_module_pref("commentsleft", -1);
					$args['info']['gamecomment']=1;
					$length = strlen($args['commentline']);
					$args['commentline'] = substr($args['commentline'],8,$length);
					$args['commenttalk'] = "";
					if (get_module_pref("commentsleft")==1){
						output("`n`c`b`4You have one Special Comment left!`4`b`c`n");
					} else if (get_module_pref("commentsleft")){
						output("`n`c`b`4You have %s Special Comments left!`4`b`c`n",get_module_pref("commentsleft"));
					} else {
						output("`n`c`b`4That was your last Special Comment!`4`b`c`n");
					}
				} else {
					$args['ignore'] = 1;
					output("`n`c`b`4You don't have any Special Comments left!`4`b`c`n");
				}
			}
			//debug($args);
			break;
		}
	return $args;
}

function specialcomments_run(){
	global $session;
    
	page_header("The Hunter's Lodge");
	
	$config = unserialize($session['user']['donationconfig']);
    if (!is_array($config))
       $config = array();	
	
	if (httpget('op') == "buy1"){
		$session['user']['donationspent']+=25;
		increment_module_pref("commentsleft",1);
		$config = array_push($config, "spent 25 points for 1 Special Comment in the lodge.");
	}
	if (httpget('op') == "buy5"){
		$session['user']['donationspent']+=100;
		increment_module_pref("commentsleft",5);
		$config = array_push($config, "spent 100 points for 5 Special Comments in the lodge.");
	}
	if (httpget('op') == "buy60"){
		$session['user']['donationspent']+=1000;
		increment_module_pref("commentsleft",60);
		$config = array_push($config, "spent 1000 points for 60 Special Comments in the lodge.");
	}
	if (httpget('op') == "buy200"){
		$session['user']['donationspent']+=2000;
		increment_module_pref("commentsleft",200);
		$config = array_push($config, "spent 2000 points for 200 Special Comments in the lodge.");
	}
	
	$session['user']['donationconfig'] = $config;
	
	output("You now have %s Special Comments stored up.  To use them, simply use the /special switch, like this:`n/special Admin CavemanJoe saunters through town, oblivious to the fact that he's stark bollock naked.`n/special A cold wind blows through the Outpost, carrying with it a somewhat chilling sense of foreboding.`n/special The clock at the center of AceHigh strikes thirteen, and the Jokers' eyes flash `4red`0 for a second.`n`nColour codes can be used within Special Comments.`n`n`bA quick note on the etiquette of using Special Comments:`b`nThe cost of posting a Special Comment is deliberately high, to ensure that players think carefully about what they want to say.  By all means, use them for mischief, but keep it playful rather than malicious.  If a group of players is engaging in a roleplaying exercise in a given Outpost, try to avoid stepping on the toes of their plotline.  Special Comments are anonymous, but are subject to the same rules of the rest of the Island (the two rules being \"Dont be a dick\" and \"Don't take it seriously\"), and drama-inducing comments will be deleted without refund.  If in doubt, ask a moderator.  Have fun!`n`n", get_module_pref("commentsleft"));

	addnav("Return to the Lodge","lodge.php");
	page_footer();
}
?>