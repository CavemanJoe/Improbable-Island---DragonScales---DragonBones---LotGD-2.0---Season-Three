<?php

function hofclandk_getmoduleinfo(){
	$info = array(
		"name"=>"Hall of Fame: Clan Dragon Kills",
		"author"=>"Chris Vorndran",
		"version"=>"1.0",
		"category"=>"General",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=70",
		"vertxtloc"=>"http://dragonprime.net/users/Sichae/",
		"description"=>"This module will display DKs of a clan in the Hall of Fame",
		"settings"=>array(
			"Hall of Fame: Clan Dragon Kills Settings,title",
			"pp"=>"How many listings Per Page,int|50",
		),
	);
	return $info;
}
function hofclandk_install(){
	module_addhook("header-hof");
	return true;
}
function hofclandk_uninstall(){
	return true;
}
function hofclandk_dohook($hookname,$args){
	global $session;
	switch ($hookname){
		case "header-hof":
			addnav("Clan Rankings");
			addnav("Clan Dragon Kills","runmodule.php?module=hofclandk");
			break;
		}
	return $args;
}
function hofclandk_run(){
	global $session;
	page_header("Clan Dragon Kills");
	$ac = db_prefix("accounts");
	$cl = db_prefix("clans");
	$op = httpget('op');
	$pp = get_module_setting("pp");
	$page = (int)httpget('page');
	$pageoffset = (int)$page;
	if ($pageoffset > 0) $pageoffset--;
	$pageoffset *= $pp;
	$from = $pageoffset+1;
	$limit = "LIMIT $pageoffset,$pp";
	$sql = "SELECT COUNT(clanid) AS c FROM $cl";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$total = $row['c'];
	$count = db_num_rows($result);
	if ($from + $pp < $total){
		$cond = $pageoffset + $pp;
	}else{
		$cond = $total;
	}
	$sql = "SELECT sum($ac.dragonkills) AS dks, count($ac.clanid) AS memcount, $cl.clanname,$ac.clanid FROM $ac 
			INNER JOIN $cl 
			ON $ac.clanid=$cl.clanid 
			WHERE $ac.clanid != 0 
			GROUP BY $ac.clanid 
			ORDER BY dks DESC, 
			memcount ASC $limit";
	$res = db_query($sql);
	rawoutput("<big>");
	output("`c`b`^Clan Dragon Kill Rankings`b`c`0`n");
	rawoutput("</big>");
	$rank = translate_inline("Rank");
	$name = translate_inline("Clan Name");
	$mem = translate_inline("# of Members");
	$dk = translate_inline("Dragonkills");
	$ratio = translate_inline("DK to Member Ratio");
	rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
	rawoutput("<tr class='trhead'><td>$rank</td><td align='center'>$name</td><td>$mem</td><td>$dk</td><td>$ratio</td></tr>");
	if (db_num_rows($res)>0){
		$i = 0;
		while($row = db_fetch_assoc($res)){
			$i++;
			if ($row['clanid']==$session['user']['clanid']){
				rawoutput("<tr class='trhilight'><td align='center'>");
			} else {
				rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td align='center'>");
			}
			output_notl("$i");
			rawoutput("</td><td align='center'>");
			output_notl("`^%s`0",$row['clanname']);
			rawoutput("</td><td align='center'>");
			output_notl("`@%s`0",$row['memcount']);
			rawoutput("</td><td align='center'>");
			output_notl("`@%s`0",$row['dks']);
			rawoutput("</td><td align='center'>");
			output_notl("`@%s`0",round($row['dks']/$row['memcount'],2));
			rawoutput("</td></tr>");
		}
	}
	rawoutput("</table>");
	if ($total>$pp){
		addnav("Pages");
		for ($p=0;$p<$total;$p+=$pp){
			addnav(array("Page %s (%s-%s)", ($p/$pp+1), ($p+1), min($p+$pp,$total)), "runmodule.php?module=hofclandk&page=".($p/$pp+1));
		}
	}
addnav("Leave");
addnav("Return to HoF","hof.php");
page_footer();
}
?>