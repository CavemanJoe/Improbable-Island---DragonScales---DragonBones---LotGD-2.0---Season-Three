<?php

function asides_getmoduleinfo(){
	$info = array(
		"name"=>"Asides",
		"version"=>"2010-05-18",
		"author"=>"Dan Hall",
		"category"=>"Social",
		"download"=>"",
		"prefs"=>array(
			"seen"=>"Messages seen since last check,int|0",
			"total"=>"Total messages in this player's Bio,int|0",
			"lastplace"=>"The place where the player started looking at Bios,text|",
		),
	);
	return $info;
}

function asides_install(){
	module_addhook("viewcommentary");
	return true;
}

function asides_uninstall(){
	return true;
}

function asides_dohook($hookname,$args){
	global $session;
	switch ($hookname){
		case "viewcommentary":
			$tloc = get_module_pref("loc","commentaryinfo");
			$sloc = get_module_pref("loc","commentaryinfo",$args['acctid']);
			
		break;
		case "commentaryoptions":
			if (!strpos($_SERVER['REQUEST_URI'],"char=".$session['user']['acctid']."&")){
				$link = "bio.php?char=".$session['user']['acctid'] ."&ret=".URLEncode($_SERVER['REQUEST_URI']);
				$total = get_module_pref("total");
				$seen = get_module_pref("seen");
				output("`n`n<a href=\"$link\">View my Bio</a>",true);
				if ($seen != $total){
					$new = $total-$seen;
					if ($new==1){
						output("(1 unread Natter)");
					} else {
						output("(%s unread Natters)", $new);
					}
				}
				addnav("",$link);
			}
		break;
	}
	return $args;
}

function asides_run(){
}

?>