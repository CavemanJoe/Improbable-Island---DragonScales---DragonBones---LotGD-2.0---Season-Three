<?php

function commentaryinfo_getmoduleinfo(){
	$info = array(
		"name"=>"Commentary Info",
		"version"=>"2008-12-04",
		"author"=>"Dan Hall",
		"category"=>"Experimental",
		"download"=>"",
		"prefs"=>array(
			"Commentaryinfo,title",
			"loc"=>"Player Location,text",
			"lastplace"=>"The place where the player started Whispering,text|",
			"user_usecommentaryextras"=>"Show extra information about each player in Commentary areas?,bool|1",
			"user_showweapons"=>"Show weapon icons?,bool|1",
			"user_linebreak"=>"Put a blank line in between lines of commentary?,bool|0",
			"user_showinline"=>"Show icons in the same line as text?,bool|1",
			"user_hidemyinfo"=>"Hide my Race and Weapon icons from other players?,bool|0",
		),
	);
	return $info;
}
function commentaryinfo_install(){
	module_addhook("viewcommentary");
	module_addhook("forest");
	module_addhook("worldnav");
	module_addhook("viewcommentaryheader");
	return true;
}
function commentaryinfo_uninstall(){
	return true;
}
function commentaryinfo_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "viewcommentary":
			if (get_module_pref("user_usecommentaryextras")==0){
				break;
			}
			if (preg_match('/bio\.php\?char=(\d+)\&ret/', $args['commentline'], $matches)) {
				$userid = $matches[1];
			}
			if ($userid == 0){
				if (get_module_pref("user_linebreak")==1){
					rawoutput("<br />");
				}
				break;
			}
			$sql = "SELECT race, sex, donation, weapon, loggedin, laston FROM ".db_prefix("accounts")." WHERE acctid = $userid";
			$result = db_fetch_assoc(db_query_cached($sql,"commentaryinfo-$userid"));
			
			if (!get_module_pref("user_hidemyinfo", "commentaryinfo", $userid)){
				$racefilename = strtolower($result['race']);
				$racefilename = strtr($racefilename, " '-_.!,", "");
				$racefilename = str_replace(" ", "", $racefilename);
				if ($result['sex'] == 0){
					$gender = "m";
					$gendertitle = "Male";
				} else {
					$gender = "f";
					$gendertitle = "Female";
				}
				$racefilename .= $gender;
				$race = "<img src=\"/images/races/".$racefilename.".png\" alt=\"".$gendertitle." ".$result['race']."\" title=\"".$gendertitle." ".$result['race']."\">";
				$weaponfilename = strtolower($result['weapon']);
				$weaponfilename = strtr($weaponfilename, " '-_.!+1234567890,", "                  ");
				$weaponfilename = str_replace(" ", "", $weaponfilename);
				$weaponfilename = "images/weapons/".$weaponfilename.".png";
				if (file_exists($weaponfilename)){
					$weapon = "<img src=\"".$weaponfilename."\" alt=\"Weapon: ".$result['weapon']."\" title=\"Weapon: ".$result['weapon']."\">";
				} else {
					$weapon = "Weapon: ".$result['weapon'];
				}
			}
			
			$offline = date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds"));
			if ($result['laston'] > $offline && $result['loggedin']==1){
				if (get_module_pref("loc", "commentaryinfo", $userid) == get_module_pref("loc", "commentaryinfo")){
					$online = "<img src=\"/modules/commentaryinfo/nearby.png\" alt=\"Nearby\" title=\"Nearby\">";
					if ($userid != $session['user']['acctid']){
						$ret = URLEncode($_SERVER['REQUEST_URI']);
						if (!strpos($ret,"bio.php") && !strpos($ret,"commentaryinfo")){
							$online = "<a href=\"runmodule.php?module=commentaryinfo&op=closetalk&player=$userid&ret=$ret\"><img src=\"/modules/commentaryinfo/nearby.png\" alt=\"Nearby (click to whisper)\" title=\"Nearby (click to whisper)\"></a>";
							addnav("","runmodule.php?module=commentaryinfo&op=closetalk&player=$userid&ret=$ret");
						}
					}
				} else {
					$online = "<img src=\"/modules/commentaryinfo/online.png\" alt=\"Logged In\" title=\"Logged In\">";
				}
			} else {
				$online = "<img src=\"/modules/commentaryinfo/offline.png\" alt=\"Logged Out\" title=\"Logged Out\">";
			}
			if ($result['donation']>100){
				$donation = "<img src=\"/modules/commentaryinfo/donator1.png\" alt=\"Site Supporter\" title=\"Site Supporter\">";
			}
			if ($result['donation']>1000){
				$donation = "<img src=\"/modules/commentaryinfo/donator2.png\" alt=\"Extra Awesome Site Supporter\" title=\"Extra Awesome Site Supporter\">";
			}
			if ($result['donation']>2000){
				$donation = "<img src=\"/modules/commentaryinfo/donator3.png\" alt=\"Ultra Awesome Site Supporter\" title=\"Ultra Awesome Site Supporter\">";
			}
			if (get_module_pref("user_linebreak")==1){
				$out .= "<br />";
			}
			$out .= "$online $close $race $donation";
			if (get_module_pref("user_showweapons")==1){
				$out .= " $weapon";
			}
			if (get_module_pref("user_showinline")==0){
				$out .= "<br />";
			}
			rawoutput("$out");
			break;
		case "forest":
		case "worldnav":
			clear_module_pref("loc");
			break;
		case "viewcommentaryheader":
			set_module_pref("loc",$args['section']);
			invalidatedatacache("commentaryinfo-".$session['user']['acctid']);
			break;
		}
	return $args;
}

function commentaryinfo_run(){
	global $session;
	switch (httpget("op")){
		case "closetalk":
			page_header("Close Talk");
			$player = httpget("player");
			$ret = httpget("ret");
			if ($session['user']['acctid'] > $player){
				$join = "closetalk-".$player."-".$session['user']['acctid'];
			} else {
				$join = "closetalk-".$session['user']['acctid']."-".$player;
			}
			$sql = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid=$player";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			$playername = $row['name'];
			output("`0You gently call out to chat with %s`0.`n`n",$playername);
			require_once "lib/commentary.php";
			addcommentary();
			viewcommentary($join,"Whisper",25);
			addnav("Leave");
			if (strpos($ret,"&refresh=1")){
				$retparts = explode("&refresh=1",$ret);
			} else if (strpos($ret,"?refresh=1")){
				$retparts = explode("?refresh=1",$ret);
			} else if (strpos($ret,"&c=")){
				$retparts = explode("&c=",$ret);
			} else if (strpos($ret,"?c=")){
				$retparts = explode("?c=",$ret);
			} else if (strpos($ret,"?&")){
				$retparts = explode("?&",$ret);
			} 
			debug($retparts);
			$back = rtrim($retparts[0],"?");
			$back = ltrim($back,"/");
			debug($back);
			if ($back=="" || !$back){
				$back = "village.php";
			}
			addnav("Back to where you came from",$back);
			//addnav("Village","village.php");
			debug(unserialize($session['user']['allowednavs']));
		break;
	}
	page_footer();
}
?>