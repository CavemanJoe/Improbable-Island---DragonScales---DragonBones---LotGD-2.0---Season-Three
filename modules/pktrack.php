<?php

function pktrack_getmoduleinfo(){
	$info = array(
		"name"=>"PK Tracking",
		"author"=>"Chris Vorndran",
		"version"=>"1.51",
		"category"=>"Stat Display",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=66",
		"vertxtloc"=>"http://dragonprime.net/users/Sichae/",
		"description"=>"This module will track the amount of PKs (Player Kills) that a user has, and generate a Hall of Fame page from the information.",
		"settings"=>array(
			"PK Tracking Settings,title",
			"wo"=>"Which heading does this fall under,enum,0,Vital Info,1,Personal Info,2,Extra Info|0",
			"losses"=>"Record losses in favour of the defender's pk count?,bool|0",
			"pp"=>"Display how many results in the HoF page,int|50",
			"shz"=>"Show people with zero PKs,bool|1",
			"dispad"=>"Show superusers in HoF listing,enum,0,Yes,1,No|1",
			"This applies to even those that have the 'Account Never Expires' Flag,note",
		),
		"user_prefs"=>array(
			"PK Tracking Prefs,title",
			"user_showpk"=>"Show Player Kills in Info area,bool|1",
		),
		"prefs"=>array(
			"PK Tracking Prefs,title",
			"user_showpk"=>"Show Player Kills in Info area,bool|1",
			"count"=>"Amount of PKs (Player Kills),int|0",
		),
		);
	return $info;
}
function pktrack_install(){
	module_addhook("charstats");
	module_addhook("pvpwin");
	module_addhook("pvploss");
	module_addhook("footer-hof");
	module_addhook("biostat");
	return true;
	}
function pktrack_uninstall(){
	return true;
}
function pktrack_dohook($hookname,$args){
	global $session;
	switch ($hookname){
		case "charstats":
			if (get_module_pref("user_showpk") == 1){
				if (get_module_setting("wo") == 0) $title = translate_inline("Vital Info");
				if (get_module_setting("wo") == 1) $title = translate_inline("Personal Info");
				if (get_module_setting("wo") == 2) $title = translate_inline("Extra Info");
				$name = translate_inline("Player Kills");
				$count = get_module_pref("count");
				setcharstat($title,$name,$count);
			}
			break;
		case "pvpwin":
			debug($args);
			if ($args['options']['type'] == 'pvp') increment_module_pref("count");
			break;
		case "pvploss":
			if (!get_module_setting("losses")) break;
			debug($args);
			if ($args['options']['type'] == 'pvp') increment_module_pref("count",1,"pktrack",$args['badguy']['acctid']);
			break;
		case "biostat":
			$char = $args['acctid'];
			$cpk = get_module_pref("count","pktrack",$char);
			output("`^Player Kills: `@%s`0",$cpk);
			output_notl("`n");
			break;
		case "footer-hof":
			addnav("Warrior Rankings");
			addnav("Player Kills","runmodule.php?module=pktrack&op=hof");
			break;
		}
	return $args;
}
function pktrack_run(){
	global $session;
	$op = httpget('op');
	$page = httpget('page');
	if (get_module_setting("shz") == 1){
		$f = 0;
	}else{
		$f = 1;
	}
	if (get_module_setting("dispad") == 1){
		$hide = SU_HIDE_FROM_LEADERBOARD;
		$g = "AND ((superuser & $hide) = 0)";
	}else{
		$g = "";
	}
	$mu = db_prefix("module_userprefs");
	$ac = db_prefix("accounts");
			
	switch ($op){
		case "hof":
			page_header("Hall of Fame");
			$pp = get_module_setting("pp");
			$pageoffset = (int)$page;
			if ($pageoffset > 0) $pageoffset--;
			$pageoffset *= $pp;
			$from = $pageoffset+1;
			$limit = "LIMIT $pageoffset,$pp";
			$sql = "SELECT COUNT(userid) AS c FROM $mu 
					WHERE modulename = 'pktrack' 
					AND setting = 'count' 
					AND value >= '$f'";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			$total = $row['c'];
			if ($from + $pp < $total){
				$cond = $pageoffset + $pp;
			}else{
				$cond = $total;
			}
			$sql = "SELECT $mu.value, $ac.name, $ac.level FROM $mu , $ac 
					WHERE acctid = userid 
					AND modulename = 'pktrack' 
					AND setting = 'count' 
					AND value >= '$f' $g 
					ORDER BY (value+0) DESC $limit";
			$result = db_query($sql);
			$count = db_num_rows($result);
			$rank = translate_inline("Rank");
			$name = translate_inline("Name");
			$pk = translate_inline("Player Kills");
			$ran = translate_inline("In PvP Range?");
			rawoutput("<big>");
			output("`c`b`^Fiercest Warriors in the Land`b`c`0`n");
			rawoutput("</big>");
			rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
			rawoutput("<tr class='trhead'><td>$rank</td><td>$name</td><td>$pk</td><td>$ran</td></tr>");
			if (db_num_rows($result)>0){
				$i = 0;
				while($row = db_fetch_assoc($result)){
					$i++;
					if ($row['name']==$session['user']['name']){
						rawoutput("<tr class='trhilight'><td>");
					} else {
						rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td>");
					}
					output_notl("$i.");
					rawoutput("</td><td>");
					output_notl("`&%s`0",$row['name']);
					rawoutput("</td><td>");
					output_notl("`c`@%s`c`0",$row['value']);
					rawoutput("</td><td>");
					if ($row['level'] <= ($session['user']['level']+2) && $row['level'] >= ($session['user']['level']-1)){
						$q = translate_inline("Yes");
					}else{
						$q = translate_inline("No");
					}
					if ($row['name'] == $session['user']['name']) $q = translate_inline("N/A");
					output_notl("`c`@%s`c`0",$q);
					rawoutput("</td></tr>");
				}
			}
			rawoutput("</table>");
			if ($total>$pp){
				addnav("Pages");
				for ($p = 0; $p < $count && $cond; $p += $pp){
					addnav(array("Page %s (%s-%s)", ($p/$pp+1), ($p+1), min($p+$pp,$count)), "runmodule.php?module=pktrack&op=hof&page=".($p/$pp+1));
			}
		}
		break;
	}
addnav("Other");
addnav("Back to HoF", "hof.php");
villagenav();
page_footer();
}
?>