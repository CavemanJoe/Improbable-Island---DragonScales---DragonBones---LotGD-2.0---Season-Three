<?php
define("OVERRIDE_FORCED_NAV",true);
// ver 1.1 now send mail for megausers to all online or all players on site
function massmail_getmoduleinfo(){
	$info = array(
		"name" => "Mass Mail",
		"override_forced_nav"=>true,
		"author" => "`b`&Ka`6laza`&ar`b",
		"version" => "1.1",
		"download" => "http://dragonprime.net/index.php?module=Downloads;catd=18",
		"category" => "Mail",
		"description" => "Clanmail all online members, admin mail all or online users",
		);
	return $info;
}
function massmail_install(){
	module_addhook("mailfunctions");
	return true;
}
function massmail_uninstall(){
	return true;
}
function massmail_dohook($hookname,$args){
	global $session,$SCRIPT_NAME;
	$op=httpget('op');
	switch ($hookname){
		case "mailfunctions":
		if ($session['user']['clanrank'] == CLAN_LEADER || $session['user']['clanrank'] == CLAN_OFFICER){
			//code copied and modified from Oliver Brendels Outbox
			$clanmail = translate_inline("ClanMail");
			array_push($args, array("runmodule.php?module=massmail&op=clanmail", $clanmail));
			addnav ("","runmodule.php?module=massmail&op=clanmail");
			//end of copied code
		}
		if ($session['user']['superuser']& SU_MEGAUSER){
			//code copied and modified from Oliver Brendels Outbox
			$allo = translate_inline("All Online");
			array_push($args, array("runmodule.php?module=massmail&op=adminonline", $allo));
			addnav ("","runmodule.php?module=massmail&op=adminonline");
			$all = translate_inline("All");
			array_push($args, array("runmodule.php?module=massmail&op=adminall", $all));
			addnav ("","runmodule.php?module=massmail&op=adminall");
			//end of copied code
		}
		
		break;
    }
    return $args;
}
function massmail_run(){
	
	global $session;
	$op=httpget('op');
	popup_header("Ye Olde Poste Office");
	rawoutput("<table width='50%' border='0' cellpadding='0' cellspacing='2'>");
	rawoutput("<tr><td>");
	$t = translate_inline("Back to the Ye Olde Poste Office");
	rawoutput("<a href='mail.php'>$t</a></td><td>");
	rawoutput("</td></tr></table>");
	output_notl("`n`n");
	$clanid=$session['user']['clanid'];
	$body=httppost('body');
	$subject="`^Clan Mail";
	$time = date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT", 900)." sec"));
	$name = $session['user']['name'];
	require_once("lib/systemmail.php");
	switch($op){
		case "clanmail":
		//copied and modified from Chris Vorndrans Bulletin
		if ($body == ""){
			rawoutput("<form action='runmodule.php?module=massmail&op=clanmail' method='POST'>");
			output("`n`^Clan Mail:`n`n");
			rawoutput("<textarea name=\"body\" rows=\"10\" cols=\"60\" class=\"input\"></textarea>");
			rawoutput("<input type='submit' class='button' value='".translate_inline("Send")."'></form>");
			rawoutput("</form>");
		}else{
			$sql = "SELECT * FROM " . db_prefix("accounts"). " WHERE clanid = '$clanid'";
			$res = db_query($sql);
			for ($i = 0; $i < db_num_rows($res); $i++){
				$row = db_fetch_assoc($res);
				
				systemmail($row['acctid'],$subject,$body);
			}
			output("`^Message has been sent.`0");
		}
		addnav("","runmodule.php?module=massmail&op=clanmail");
		break;

		case "adminall":
		if ($body==""){
			rawoutput("<form action='runmodule.php?module=massmail&op=adminall' method='POST'>");
			output("`n`^Send to All Players:`n`n");
			rawoutput("<textarea name=\"body\" rows=\"10\" cols=\"60\" class=\"input\"></textarea>");
			rawoutput("<input type='submit' class='button' value='".translate_inline("Send")."'></form>");
			rawoutput("</form>");
		}else{
			$sql = "SELECT * FROM " . db_prefix("accounts");
			$res = db_query($sql);
			for ($i = 0; $i < db_num_rows($res); $i++){
				$row = db_fetch_assoc($res);
				systemmail($row['acctid'],"`^Server News from ".$name,$body);
			}
			output("Your mail was sent to all players");
		}
		addnav("","runmodule.php?module=massmail&op=adminall");
		break;
		case "adminonline":
		if ($body==""){
			rawoutput("<form action='runmodule.php?module=massmail&op=adminonline' method='POST'>");
			output("`n`^Send to all online:`n`n");
			rawoutput("<textarea name=\"body\" rows=\"10\" cols=\"60\" class=\"input\"></textarea>");
			rawoutput("<input type='submit' class='button' value='".translate_inline("Send")."'></form>");
			rawoutput("</form>");
		}else{
			$sql = "SELECT * FROM " . db_prefix("accounts"). " WHERE loggedin = 1 AND laston > '$time'";
			$res = db_query($sql);
			for ($i = 0; $i < db_num_rows($res); $i++){
				$row = db_fetch_assoc($res);
				systemmail($row['acctid'],"`^Server News from ".$name,$body);
			}
			output("Your Mail was sent to all online");
		}
		addnav("","runmodule.php?module=massmail&op=adminonline");
		break;
		//end of copied code
	}
	popup_footer();
}
?>