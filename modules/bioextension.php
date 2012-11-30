<?php

function bioextension_getmoduleinfo(){
	$info = array(
		"name"=>"Bio Extension",
		"version"=>"2009-11-10",
		"author"=>"Dan Hall",
		"category"=>"Bio",
		"download"=>"",
		"settings"=>array(
			"threshhold"=>"DP Threshhold to enable this feature,int|500",
			"charlimit"=>"Character limit for Extended Biography,int|10000",
		),
		"prefs"=>array(
			"Extended Biography,title",
			"These options require at least 500 spent-or-unspent Donator Points,note",
			"user_extendedbio"=>"Your ten-thousand-character Extended Biography will be shown in your Bio if you have at least 500 spent-or-unspent Donator Points.  You can use colour codes in here if you like.,textarea|",
			"user_extlink"=>"Insert a link to your external webpage here.  Include http://.  Don't have a website of your own?  Write a wiki page in the Enquirer and link there!,text",
		),
	);
	return $info;
}

function bioextension_install(){
	module_addhook("bioinfo");
	module_addhook("footer-prefs");
	return true;
}

function bioextension_uninstall(){
	return true;
}

function bioextension_dohook($hookname,$args){
	global $session;
		if ($hookname=="bioinfo"){
			$sql = "SELECT donation FROM " . db_prefix("accounts") . " WHERE acctid = '".$args['acctid']."'";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			if ($row['donation']>=get_module_setting("threshhold")){
				$bio=get_module_pref("user_extendedbio","bioextension",$args['acctid']);
				$link=get_module_pref("user_extlink","bioextension",$args['acctid']);
				$bio=str_replace(chr(13),"`n",$bio);
				$bio=stripslashes($bio);
				output("`0%s`0`n`n",$bio);
				if (substr($link,0,5)=="http:"){
					rawoutput("<a href=\"".$link."\">Player's webpage</a><br /><br />");
				}
			}
		} else if ($hookname=="footer-prefs"){
			$bio=get_module_pref("user_extendedbio");
			$limit=get_module_setting("charlimit");
			if (strlen($bio)>$limit) {
				output("`c`4`bWARNING`b`0`c`nYour Extended Bio is oversized by %s characters.  If you navigate away from this page, your Extended Bio will have %s characters indiscriminately cut from the end.  Please edit and re-save your Extended Bio to avoid cuts.",strlen($bio)-$limit,strlen($bio)-$limit);
				$bio=substr($bio,0,$limit);
				set_module_pref("user_extendedbio",$bio);
			}

		}
	return $args;
}

function bioextension_run(){
}

?>