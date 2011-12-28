<?php
	$action = httpget('action');
	$op = httpget('op');
	page_header("Newly Weds");
	addnav('Navigation');
	if (get_module_setting("newlist")==0) addnav("To the Loveshack","runmodule.php?module=marriage&op=loveshack");
	elseif (get_module_setting("newlist")==1 || (get_module_setting("newlist")==0 && get_module_setting("flirttype")==0)) addnav('To the Garden','gardens.php');
	elseif (get_module_setting("newlist")==3){
		addnav("Other");
		addnav("Back to HoF","hof.php");
	}
	villagenav();
	addnav("List");
	addnav("Currently Online","runmodule.php?module=marriage&op=newlyweds");
	$playersperpage=50;

	$sql = "SELECT count(acctid) AS c FROM " . db_prefix("accounts") . " WHERE (marriedto<>0 AND marriedto<>4294967295) AND locked=0";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$totalplayers = $row['c'];

	$action = httpget('action');
	$page = httpget('page');
	$search = "";

	if ($op=="search"){
		$search="%";
		$n = httppost('name');
		for ($x=0;$x<strlen($n);$x++){
			$search .= substr($n,$x,1)."%";
		}
		$search=" AND a.login LIKE '".addslashes($search)."'";
	} else {
		$pageoffset = (int)$page;
		if ($pageoffset>0) $pageoffset--;
		$pageoffset*=$playersperpage;
		$from = $pageoffset+1;
		$to = min($pageoffset+$playersperpage,$totalplayers);
	}

	$limit=" LIMIT $pageoffset,$playersperpage ";
	addnav("Pages");
	for ($i=0;$i<$totalplayers;$i+=$playersperpage){
		addnav(array("Page %s (%s-%s)", $i/$playersperpage+1, $i+1, min($i+$playersperpage,$totalplayers)), "runmodule.php?module=marriage&op=newlyweds&page=".($i/$playersperpage+1));
	}

	if ($page=="" && $action==""){
		$title = translate_inline("Married Warriors Currently Online");
		$sql = "SELECT a.name as name,b.name as partnername,a.acctid as acctid,a.login as login,a.alive as alive,a.location as location,a.race as race,a.sex as sex,a.marriedto as marriedto,a.laston as laston,a.loggedin as loggedin,a.lastip as lastip,a.uniqueid as uniqueid FROM " . db_prefix("accounts") . " as a LEFT JOIN ".db_prefix("accounts")." as b ON a.marriedto=b.acctid WHERE a.locked=0 AND (a.marriedto<>0 AND a.marriedto<>4294967295) AND a.loggedin=1 AND a.laston>'".date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds"))."' ORDER BY a.level DESC, a.dragonkills DESC, a.login ASC";
		$result = db_query_cached($sql,"marriage-marriedonline");
	} else {
		$title = sprintf_translate("Married Warriors in the realm (Page %s: %s-%s of %s)", ($pageoffset/$playersperpage+1), $from, $to, $totalplayers);
		rawoutput(tlbutton_clear());
		$sql = "SELECT a.name as name,b.name as partnername,a.acctid as acctid,a.login as login,a.alive as alive,a.location as location,a.race as race,a.sex as sex,a.marriedto as marriedto,a.laston as laston,a.loggedin as loggedin,a.lastip as lastip,a.uniqueid as uniqueid FROM " . db_prefix("accounts") . " as a LEFT JOIN ".db_prefix("accounts")." as b ON a.marriedto=b.acctid WHERE a.locked=0 AND (a.marriedto<>0 AND a.marriedto<>4294967295) $search"."ORDER BY a.level DESC, a.dragonkills DESC, a.login ASC $limit";
		$result = db_query_cached($sql,"marriage-marriedrealm$page");
	}



	$max = db_num_rows($result);
	if ($max>100) {
		output("`\$Too many names match that.  Showing only the first 100.`0`n");
		$max = 100;
	}

	output_notl("`c`b".$title."`b");

	$alive = translate_inline("Alive");
	$name = translate_inline("Name");
	$clan = translate_inline("Clan");
	$loc = translate_inline("Location");
	$race = translate_inline("Race");
	$sex = translate_inline("Sex");
	$who = translate_inline("Married To");
	$marriagedate = translate_inline("Marriage Date");
	$online = translate_inline("`#(Online)");
	$unlogged= translate_inline("`4long ago");

	rawoutput("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>",true);
	rawoutput("<tr class='trhead'><td>$alive</td><td>$name</td><td>$loc</td><td>$race</td><td>$sex</td><td>$who</td><td>$marriagedate</td></tr>");
	$writemail = translate_inline("Write Mail");
	if ($max==0) {
		output_notl("<tr class='trhilight'><td colspan='8'><center>`^`b`c%s`c`b</center></td></tr>",translate_inline("No one is married!"),true);
	}
	for($i=0;$i<$max;$i++){
		$row = db_fetch_assoc($result);
		rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td>",true);
		$a = translate_inline($row['alive']?"`1Yes`0":"`4No`0");
		output_notl("%s", $a);
		rawoutput("</td><td>");
		if ($session['user']['loggedin']) {
			rawoutput("<a href=\"mail.php?op=write&to=".rawurlencode($row['login'])."\" target=\"_blank\" onClick=\"".popup("mail.php?op=write&to=".rawurlencode($row['login'])."").";return false;\">");
			rawoutput("<img src='images/newscroll.GIF' width='16' height='16' alt='$writemail' border='0'></a>");
			rawoutput("<a href='bio.php?char=".rawurlencode($row['login'])."'>");
			addnav("","bio.php?char=".rawurlencode($row['login'])."");
		}
		output_notl("`&%s`0", $row['name']);
		if ($session['user']['loggedin'])
			rawoutput("</a>");
		rawoutput("</td><td>");
		$loggedin=(date("U") - strtotime($row['laston']) < getsetting("LOGINTIMEOUT",900) && $row['loggedin']);
		output_notl("`&%s`0", $row['location']);
		if ($loggedin) {
			output_notl("%s", $online);
		}
		rawoutput("</td><td>");
		output_notl("%s", translate_inline($row['race'],"race"));
		rawoutput("</td><td>");
		$sex = translate_inline($row['sex']?"`%Female`0":"`!Male`0");
		output_notl("%s", $sex);
		rawoutput("</td><td>");
		//$sql = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid='".$row['marriedto']."' AND locked=0";
		//$res = db_fetch_assoc(db_query($sql));
		//output_notl("%s",$res['name']);
		output_notl("%s",$row['partnername']);
		rawoutput("</td><td>");
		$reg=get_module_objpref("marriage",$row['acctid'],"marriagedate");
		if ($reg=="0000-00-00 00:00:00" ||$reg==0) {
			$reg=$unlogged;
		}
		output_notl("%s",$reg);
		rawoutput("</td></tr>");
	}
	rawoutput("</table>");
	output_notl("`n");
	$search = translate_inline("Search by name: ");
	$search2 = translate_inline("Search");
	rawoutput("<form action='runmodule.php?module=marriage&op=newlyweds&action=search' method='POST'>$search<input name='name'><input type='submit' class='button' value='$search2'></form>");
	addnav("","runmodule.php?module=marriage&op=newlyweds&action=search");
	output_notl("`c");
	page_footer();
?>