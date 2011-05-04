<?php

function biochat_getmoduleinfo(){
	$info = array(
		"name"=>"Bio Chat",
		"version"=>"2009-11-10",
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

function biochat_install(){
	module_addhook("bioend");
	module_addhook("commentaryoptions");
	return true;
}

function biochat_uninstall(){
	return true;
}

function biochat_dohook($hookname,$args){
	global $session;
	switch ($hookname){
		case "bioend":
			require_once("lib/commentary.php");
			output("`n`n`0%s`0's Natter feed:`n",$args['name']);
			addcommentary();
			$section = "bio-".$args['acctid'];
			viewcommentary($section,"Natter!",25);
			
			if (strpos(httpget('ret'),"bio.php")===false){
				
				set_module_pref("lastplace",httpget('ret'));
			}
			
			$sql = "SELECT COUNT(commentid) AS totalcomments FROM " . db_prefix("commentary") . " WHERE section='$section'";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			$all = $row['totalcomments'];
			set_module_pref("total",$all,"biochat",$args['acctid']);
			
			if ($args['acctid']==$session['user']['acctid']){
				//this is the player looking at his or her own bio
				set_module_pref("seen",$all);
			}
			
			$return = get_module_pref("lastplace");
			if ($return){
				addnav("Been clicking around Bios and Nattering for a while?");
				addnav("Go `iright`i back to where you came from",$return);
			}
			debug($return);
		break;
		case "commentaryoptions":
			if (!strpos($_SERVER['REQUEST_URI'],"char=".$session['user']['acctid']."&") && !strpos($_SERVER['REQUEST_URI'],"global_banter")){
				$link = "bio.php?char=".$session['user']['acctid'] ."&ret=".URLEncode(buildcommentarylink("&frombio=true"));
				$total = get_module_pref("total");
				$seen = get_module_pref("seen");
				output("<a href=\"$link\">View my Bio</a> ",true);
				if ($seen != $total){
					$new = $total-$seen;
					if ($new==1){
						output("(1 unread Natter) ");
					} else {
						output("(%s unread Natters) ", $new);
					}
				}
				addnav("",$link);
			}
		break;
	}
	return $args;
}

function biochat_run(){
}

?>