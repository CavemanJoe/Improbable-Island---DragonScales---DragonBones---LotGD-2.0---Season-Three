<?php
$op2 = httpget('op2');
$op3 = httpget('op3');
$id1 = httpget('id1');
$id2 = httpget('id2');
$ap = httpget('ap');
$link = "runmodule.php?module=marriage&op=superuser";
$operation = httpget('op2');
page_header("Marriage Edit");
addnav("Navigation");
addnav("Return to the Grotto","superuser.php");
villagenav();
addnav("Marriage");
addnav("Divorce Players",$link);
addnav("Wed Players",$link."&op2=wed");
addnav("Flirting");
addnav("Block Flirting for a Player",$link."&op2=blockf");
addnav("Unblock Flirting for a Player",$link."&op2=unblockf");
addnav("Proposals");
addnav("Block Proposing for a Player",$link."&op2=blockp");
addnav("Unblock Proposing for a Player",$link."&op2=unblockp");
switch ($operation) {
default:
	output("`c`bSuperuser Divorce`c`b`n");
	$playersperpage=50;
	$action = httpget('action');
	$page = httpget('page');
	if ($page=="") $page=1;
	//$search = "";
	$search = translate_inline("Search by name: ");
	$search2 = translate_inline("Search");
	rawoutput("<form action='runmodule.php?module=marriage&op=superuser&action=search' method='POST'>$search<input name='name'><input type='submit' class='button' value='$search2'></form>");
	addnav("","runmodule.php?module=marriage&op=superuser&action=search");
	$action = httpget('action');

	$sql = "SELECT count(acctid) AS c FROM " . db_prefix("accounts") . " WHERE (marriedto<>0 AND marriedto<>4294967295) AND locked=0";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$totalplayers = $row['c'];

	if ($action=="search"){
		$search="%";
		$n = httppost('name');
		for ($x=0;$x<strlen($n);$x++){
			$search .= substr($n,$x,1)."%";
		}
		$search=" AND a.login LIKE '".addslashes($search)."'";
	}
	$pageoffset = (int)$page;
	if ($pageoffset>0) $pageoffset--;
	$pageoffset*=$playersperpage;
	$from = $pageoffset+1;
	$to = min($pageoffset+$playersperpage,$totalplayers);
	$limit=" LIMIT $pageoffset,$playersperpage ";
	if ($totalplayers>$playersperpage){
		addnav("Pages");
		for ($i=0;$i<$totalplayers;$i+=$playersperpage){
			addnav(array("Page %s (%s-%s)", $i/$playersperpage+1, $i+1, min($i+$playersperpage,$totalplayers)), "runmodule.php?module=marriage&op=superuser&page=".($i/$playersperpage+1));
		}
	}
	if ($action=="") $title = sprintf_translate("Current Marriages");
	else $title = sprintf_translate("Search Results");
	
	rawoutput(tlbutton_clear());
	
	if ($action=="") $sql = "SELECT a.name as name,b.name as partnername,a.acctid as acctid,b.acctid as partneracctid,a.login as login,a.alive as alive,a.location as location,a.race as race,a.sex as sex,b.sex as partnersex,a.marriedto as marriedto,a.laston as laston,a.lastip as lastip,a.uniqueid as uniqueid FROM " . db_prefix("accounts") . " as a LEFT JOIN ".db_prefix("accounts")." as b ON a.marriedto=b.acctid WHERE a.locked=0 AND (a.marriedto<>0 AND a.marriedto<>4294967295) ORDER BY a. name ASC $limit";
	else $sql = "SELECT a.name as name,b.name as partnername,a.acctid as acctid,b.acctid as partneracctid,a.login as login,a.alive as alive,a.location as location,a.race as race,a.sex as sex,b.sex as partnersex,a.marriedto as marriedto,a.laston as laston,a.lastip as lastip,a.uniqueid as uniqueid FROM " . db_prefix("accounts") . " as a LEFT JOIN ".db_prefix("accounts")." as b ON a.marriedto=b.acctid WHERE a.locked=0 AND (a.marriedto<>0 AND a.marriedto<>4294967295) $search ORDER BY a. name ASC $limit";

	$result = db_query_cached($sql,"marriage-marriedrealm");

	$max = db_num_rows($result);
	if ($max>100) {
		output("`\$Too many names match that.  Showing only the first 100.`0`n");
		$max = 100;
	}
	output("`^Divorce (No YoM): Causes a divorce for the married players without notification in YoM or the News.`n");
	output("`n`&Divorce (With YoM): Causes a divorce for the married players with notification in YoM AND the News.`n");
	output_notl("`n`c`0%s`n`n",$title);

	$alive = translate_inline("Alive");
	$name = translate_inline("Name");
	$sex = translate_inline("Sex");
	$who = translate_inline("Married To");

	$act1 = translate_inline("Divorce (No YoM)");
	$act2 = translate_inline("Divorce (with YoM)");

	rawoutput("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>",true);
	rawoutput("<tr class='trhead'><td><center>$name</center></td><td><center>$sex</center></td><td><center>$who</center></td><td><center>$sex</center></td><td><center>$act1</center></td><td><center>$act2</center></td></tr>");
	$writemail = translate_inline("Write Mail");
	if ($max==0) {
		output_notl("<tr class='trhilight'><td colspan='8'><center>`^`b`c%s`c`b</center></td></tr>",translate_inline("No one is married!"),true);
	}
	for($i=0;$i<$max;$i++){
		$row = db_fetch_assoc($result);
		rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td>",true);
		
		output_notl("`&%s`0", $row['name']);
		rawoutput("</td><td><center>");

		$sex = translate_inline($row['sex']?"`%Female`0":"`!Male`0");
		output_notl("%s", $sex);
		rawoutput("</td><td>");

		output_notl("%s",$row['partnername']);
		rawoutput("</td><td><center>");
		
		$sex2 = translate_inline($row['partnersex']?"`%Female`0":"`!Male`0");
		output_notl("%s", $sex2);
		rawoutput("</td><td><center>");

		$id1=$row['acctid'];
		$id2=$row['partneracctid'];
		$div1 = translate_inline("[`^Divorce`0]");

		rawoutput("<a href=\"runmodule.php?module=marriage&op=superuser&op2=divorce&op3=0&id1=$id1&id2=$id2\">");
		output_notl("%s",$div1);
		rawoutput("</a>");
		addnav("","runmodule.php?module=marriage&op=superuser&op2=divorce&op3=0&id1=$id1&id2=$id2");
		rawoutput("</center></td><td><center>");
		
		$div2 = translate_inline("[`&Divorce`0]");
		rawoutput("<a href=\"runmodule.php?module=marriage&op=superuser&op2=divorce&op3=1&id1=$id1&id2=$id2\">");
		output_notl("%s",$div2);
		rawoutput("</a>");
		addnav("","runmodule.php?module=marriage&op=superuser&op2=divorce&op3=1&id1=$id1&id2=$id2");
		rawoutput("</center></td></tr>");

	}
	rawoutput("</table>");
	output_notl("`c");
break;
case "divorce":
	output("`c`bSuperuser Divorce`c`b`n");
	$suname=$session['user']['name'];
	$sql1 = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid='$id1' AND locked=0";
	$res1 = db_query($sql1);
	$row1 = db_fetch_assoc($res1);
	$name1=$row1['name'];
	$sql2 = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid='$id2' AND locked=0";
	$res2 = db_query($sql2);
	$row2 = db_fetch_assoc($res2);
	$name2=$row2['name'];
	if ($op3==1){
		require_once("lib/systemmail.php");
		$t = array("`^Royal Decree of Marriage Annullment");
		$mail1=array("`^By Royal Decree, your marriage to `@%s`^ has been annulled by `&%s`^.",$name2,$suname);
		$mail2=array("`^By Royal Decree, your marriage to `@%s`^ has been annulled by `&%s`^.",$name1,$suname);
		systemmail($id1,$t,$mail1);
		systemmail($id2,$t,$mail2);
		addnews("`^The marriage between `&%s`^ and `&%s`^ has been annulled.",$name1,$name2);
		output("`^YoMs have been sent to `&%s`^ and `&%s`^ indicating that you have annulled their marriage. An announcement will be made in the news.`n`n",$name1,$name2);
	}else output("`^You have annulled the marriage of `&%s`^ and `&%s`^ without sending them a message. No announcement will be made in the news.",$name1,$name2);
	debuglog("The annullment of the marriage between {$name1} and {$name2} was processed by {$suname}");
	$sql = "UPDATE " . db_prefix("accounts") . " SET marriedto='0' WHERE acctid='$id1'";
	db_query($sql);
	$sql = "UPDATE " . db_prefix("accounts") . " SET marriedto='0' WHERE acctid='$id2'";
	db_query($sql);
	set_module_objpref("marriage",$id1,"marriagedate",0);
	set_module_objpref("marriage",$id2,"marriagedate",0);
break;
//Find the first person for a Staff Arranged Marriage
case "wed":
	output("`c`bSuperuser Wedding`c`b`n");
	$who = httpget('who');
	if ($who==""){
		output("`nNote: You can only wed two people who are not already married.  If a player is already married, you will need to 'divorce' them first. Players do NOT receive the 1st marriage buff for weddings performed this way.`n`n");
		output("Who is the first person that you wish to choose for the marriage?`n");
		$subop = httpget('subop');
		$search = translate_inline("Search");
		rawoutput("<form action='runmodule.php?module=marriage&op=superuser&op2=wed&subop=search' method='POST'><input name='name' id='name'><input type='submit' class='button' value='$search'></form>");
		addnav("","runmodule.php?module=marriage&op=superuser&op2=wed&subop=search");
		rawoutput("<script language='JavaScript'>document.getElementById('name').focus();</script>");
		if ($subop=="search"){
			$search = "%";
			$name = httppost('name');
			for ($i=0;$i<strlen($name);$i++){
				$search.=substr($name,$i,1)."%";
			}
			$sql = "SELECT name,acctid FROM " . db_prefix("accounts") . " WHERE (locked=0 AND name LIKE '$search') AND marriedto=0 ORDER BY name ASC";
			$result = db_query($sql);
			$max = db_num_rows($result);
			if ($max > 100) {
				output("`n`nToo many names in list.  Please choose from the first 100 or narrow down your search.");
				$max = 100;
			}
			$n = translate_inline("Name");
			rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
			rawoutput("<tr class='trhead'><td><center>$n</center></td></tr>");
			if ($max==0){
				rawoutput("<tr class='trhilight'><td>");
				output_notl("`^");
				output("No Unmarried Players Found by that Name");
				rawoutput("</td></tr></table><br>");
			}else{
				for ($i=0;$i<$max;$i++){
					$row = db_fetch_assoc($result);
					rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td>");
					rawoutput("<a href='runmodule.php?module=marriage&op=superuser&op2=wed&who=".rawurlencode($row['acctid'])."'>");
					output_notl("`0[%s`0]", $row['name']);
					rawoutput("</a></td></tr>");
					addnav("","runmodule.php?module=marriage&op=superuser&op2=wed&who=".rawurlencode($row['acctid']));
				}
			}
		rawoutput("</table>");
		}
	}else{
		$sql = "SELECT name,acctid FROM " . db_prefix("accounts") . " WHERE acctid='$who'";
		$result = db_query($sql);
		if (db_num_rows($result)>0){
			$row = db_fetch_assoc($result);
			$id1 = $row['acctid'];
			$name = $row['name'];
			output("Note: Players will ALWAYS receive a YoM when you marry them. There is no announcement in the news.`n`nPlease choose the player that you would like to have married to `^%s`0.`n`n",$name);
			$search = translate_inline("Search");
			rawoutput("<form action='runmodule.php?module=marriage&op=superuser&op2=wed2&id1=$id1&subop=search' method='POST'><input name='name' id='name'><input type='submit' class='button' value='$search'></form>");
			addnav("","runmodule.php?module=marriage&op=superuser&op2=wed2&id1=$id1&subop=search");
			addnav("Search Again For 1st Person","runmodule.php?module=marriage&op=superuser&op2=wed");
			rawoutput("<script language='JavaScript'>document.getElementById('name').focus();</script>");
		}
	}
break;
//Find the second person for a Staff Arranged Marriage
case "wed2":
	output("`c`bSuperuser Wedding`c`b`n");
	//Gather the name of the 1st person again
	$sql = "SELECT name,acctid FROM " . db_prefix("accounts") . " WHERE acctid='$id1'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$name1 = $row['name'];
	
	$who = httpget('who');
	if ($who==""){
		output("`nSearch again for the player that will be married to `^%s`0:'`c`n",$name1);
		$subop = httpget('subop');
		$search = translate_inline("Search");
		rawoutput("<form action='runmodule.php?module=marriage&op=superuser&op2=wed2&id1=$id1&subop=search' method='POST'><input name='name' id='name'><input type='submit' class='button' value='$search'></form>");
		addnav("","runmodule.php?module=marriage&op=superuser&op2=wed2&id1=$id1&subop=search");
		rawoutput("<script language='JavaScript'>document.getElementById('name').focus();</script>");
		if ($subop=="search"){
			$search = "%";
			$name = httppost('name');
			for ($i=0;$i<strlen($name);$i++){
				$search.=substr($name,$i,1)."%";
			}
			$sql = "SELECT name,acctid FROM " . db_prefix("accounts") . " WHERE (locked=0 AND name LIKE '$search') AND marriedto=0 AND acctid<>$id1 ORDER BY name ASC";
			$result = db_query($sql);
			$max = db_num_rows($result);
			if ($max > 100) {
				output("`n`nToo many names in list.  Please choose from the first 100 or narrow down your search.");
				$max = 100;
			}
			$n = translate_inline("Name");
			rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
			rawoutput("<tr class='trhead'><td><center>$n</center></td></tr>");
			if ($max==0){
				rawoutput("<tr class='trhilight'><td>");
				output_notl("`^");
				output("No Unmarried Players Found by that Name");
				rawoutput("</td></tr></table><br>");
			}else{
				for ($i=0;$i<$max;$i++){
					$row = db_fetch_assoc($result);
					rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td>");
					rawoutput("<a href='runmodule.php?module=marriage&op=superuser&op2=wed2&id1=$id1&who=".rawurlencode($row['acctid'])."'>");
					output_notl("`0[%s`0]", $row['name']);
					rawoutput("</a></td></tr>");
					addnav("","runmodule.php?module=marriage&op=superuser&op2=wed2&id1=$id1&who=".rawurlencode($row['acctid']));
				}
			}
		rawoutput("</table>");
		output_notl("`c");
		}
	}else{
		$sql = "SELECT name,acctid FROM " . db_prefix("accounts") . " WHERE acctid='$who'";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$id2 = $row['acctid'];
		$name2 = $row['name'];
		$confirm=translate_inline("`0[`@Confirm`0]`b`c");
		output("You have chosen to arrange the marriage of `^%s`0 to `^%s`0. Please Confirm this marriage:`n`n`c`b",$name1,$name2);
		rawoutput("<a href=\"runmodule.php?module=marriage&op=superuser&op2=wed3&id1=$id1&id2=$id2\">");
		output_notl("%s",$confirm);
		rawoutput("</a>");
		addnav("","runmodule.php?module=marriage&op=superuser&op2=wed3&id1=$id1&id2=$id2");
		addnav("Confirm Marriage","runmodule.php?module=marriage&op=superuser&op2=wed3&id1=$id1&id2=$id2");		
	}
break;
//Finalize Marriage
case "wed3":
	output("`c`bSuperuser Wedding`c`b`n");
	$suname=$session['user']['name'];
	$sql = "SELECT name,acctid FROM " . db_prefix("accounts") . " WHERE acctid='$id1'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$id1 = $row['acctid'];
	$name1 = $row['name'];

	$sql = "SELECT name,acctid FROM " . db_prefix("accounts") . " WHERE acctid='$id2'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$id2 = $row['acctid'];
	$name2 = $row['name'];
	output("The marriage between `^%s`0 and `^%s`0 is complete.",$name1,$name2);
	require_once("lib/systemmail.php");
	$t = array("`^Royal Decree of Marriage");
	$mail1=array("`^By Royal Decree, you have been married to `@%s`^ by `&%s`^ during a quiet civil ceremony.",$name2,$suname);
	$mail2=array("`^By Royal Decree, you have been married to `@%s`^ by `&%s`^ during a quiet civil ceremony.",$name1,$suname);
	systemmail($id1,$t,$mail1);
	systemmail($id2,$t,$mail2);
	$sql = "UPDATE " . db_prefix("accounts") . " SET marriedto='$id1' WHERE acctid='$id2'";
	db_query($sql);
	$sql = "UPDATE " . db_prefix("accounts") . " SET marriedto='$id2' WHERE acctid='$id1'";
	db_query($sql);
	$time=date("Y-m-d H:i:s");
	set_module_objpref("marriage",$id1,"marriagedate",$time);
	set_module_objpref("marriage",$id2,"marriagedate",$time);
break;
case "blockf":
	output("`c`b`^Block Flirting`b`c`n");
	$who = httpget('who');
	if ($who==""){
		output("Blocking a player from flirting does NOT reset their flirt points. It just prevents them from engaging in any flirting activity. A YoM will be sent to the player after you block them.");
		if (get_module_setting("charmnewday")>0) output("Since the game is set to deduct flirt points with each newday, blocking a player may cause a divorce if they are already married.");
		if (get_module_setting("flirttype")==1) output("``n`nYour system is currently set up to use flirting.");
		else output("`n`n`\$Your system is currently NOT set up to use flirting so changing this setting will have no effect.`^");
		output("`n`nWho do you wish to block from engaging in flirtation?`n");
		$subop = httpget('subop');
		$search = translate_inline("Search");
		rawoutput("<form action='runmodule.php?module=marriage&op=superuser&op2=blockf&subop=search' method='POST'><input name='name' id='name'><input type='submit' class='button' value='$search'></form>");
		addnav("","runmodule.php?module=marriage&op=superuser&op2=blockf&subop=search");
		rawoutput("<script language='JavaScript'>document.getElementById('name').focus();</script>");
		if ($subop=="search"){
			$search = "%";
			$name = httppost('name');
			for ($i=0;$i<strlen($name);$i++){
				$search.=substr($name,$i,1)."%";
			}
			$sql = "SELECT name,acctid FROM " . db_prefix("accounts") . " WHERE (locked=0 AND name LIKE '$search') ORDER BY name ASC";
			//$sql = "SELECT ".db_prefix("module_userprefs").".value, ".db_prefix("accounts").".name, ".db_prefix("accounts").".acctid, ".db_prefix("accounts").".marriedto FROM " . db_prefix("module_userprefs") . "," . db_prefix("accounts") . " WHERE (locked=0 AND name LIKE '$search') AND acctid = userid AND modulename = 'marriage' AND setting = 'supernoflirt' AND value = 0";
			$result = db_query($sql);
			$max = db_num_rows($result);
			if ($max > 100) {
				output("`n`nToo many names in list.  Please choose from the first 100 or narrow down your search.");
				$max = 100;
			}
			$n = translate_inline("Name");
			rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
			rawoutput("<tr class='trhead'><td><center>$n</center></td></tr>");
			if ($max==0){
				rawoutput("<tr class='trhilight'><td>");
				output_notl("`^");
				output("No Unblocked Players Found by That Name");
				rawoutput("</td></tr>");
			}else{
				for ($i=0;$i<$max;$i++){
					$row = db_fetch_assoc($result);
					rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td>");
					rawoutput("<a href='runmodule.php?module=marriage&op=superuser&op2=blockf&who=".rawurlencode($row['acctid'])."'>");
					output_notl("`0[%s`0]", $row['name']);
					rawoutput("</a></td></tr>");
					addnav("","runmodule.php?module=marriage&op=superuser&op2=blockf&who=".rawurlencode($row['acctid']));
				}
			}
		rawoutput("</table>");
		}else{
	//List the currently blocked players
	$pp = 50;
	$pageoffset = (int)$page;
	if ($pageoffset > 0) $pageoffset--;
	$pageoffset *= $pp;
	$limit = "LIMIT $pageoffset,$pp";
	$sql = "SELECT COUNT(*) AS c FROM " . db_prefix("module_userprefs") . " WHERE modulename = 'marriage' AND setting = 'supernoflirt' AND value > 0";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$total = $row['c'];
	$count = db_num_rows($result);
	if (($pageoffset + $pp) < $total){
		$cond = $pageoffset + $pp;
	}else{
		$cond = $total;
	}
	$sql = "SELECT ".db_prefix("module_userprefs").".value, ".db_prefix("accounts").".name, ".db_prefix("accounts").".acctid, ".db_prefix("accounts").".marriedto FROM " . db_prefix("module_userprefs") . "," . db_prefix("accounts") . " WHERE acctid = userid AND modulename = 'marriage' AND setting = 'supernoflirt' AND value > 0 $limit";
	$result = db_query($sql);
	$name = translate_inline("Name");
	$sublock = translate_inline("SU Blocked");
	$selfblock= translate_inline("Voluntarily Blocked");
	$none = translate_inline("No Players Are Superuser Blocked from Flirting");
	$marriedto=translate_inline("Spouse");
	output("`n`cPlayers Currently Blocked from Flirting by Superuser`n`c");
	rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
	rawoutput("<tr class='trhead'><td>$name</td><td>$sublock</td><td>$selfblock</td><td>$marriedto</td></tr>");
	if (db_num_rows($result)>0){
		for($i = $pageoffset; $i < $cond && $count; $i++) {
			$row = db_fetch_assoc($result);
			$id=$row['acctid'];
			if ($row['name']==$session['user']['name']){
				rawoutput("<tr class='trhilight'><td>");
			}else{
				rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td>");
			}
			output_notl("`&%s`0",$row['name']);
			rawoutput("</td><td><center>");
			$block1= translate_inline("Yes");
			output_notl("%s",$block1);
			rawoutput("</center></td><td><center>");
			$bl=get_module_pref("user_option","marriage",$id);
			$block2 = translate_inline($bl?"Yes":"No");
			output_notl("%s",$block2);
			rawoutput("</center></td><td>");
			if ($row['marriedto']>0){
				$sql1 = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid='$id' AND locked=0";
				$res1 = db_query($sql1);
				$row1 = db_fetch_assoc($res1);
				$spouse=$row['name'];
			}else{
				$spouse=translate_inline("None");
			}
			output_notl("%s",$spouse);
			rawoutput("</td></tr>");
        }
	}else  output_notl("<tr class='trlight'><td colspan='7' align='center'>`&$none`0</td></tr>",true);
	rawoutput("</table>");
	if ($total>$pp){
		addnav("Pages");
		for ($p=0;$p<$total;$p+=$pp){
			addnav(array("Page %s (%s-%s)", ($p/$pp+1), ($p+1), min($p+$pp,$total)), "runmodule.php?module=marriage&op=superuser&op2=blockf&page=".($p/$pp+1));
		}
	}
	}
	}else{
		$sql = "SELECT name FROM " . db_prefix("accounts") . " WHERE acctid='$who'";
		$result = db_query($sql);
		if (get_module_pref("supernoflirt","marriage",$who)==1) output("That player already has a superuser block for flirting.`n`n");
		else{
			$suname=$session['user']['name'];
			$row = db_fetch_assoc($result);
			$name = $row['name'];
			set_module_pref("supernoflirt",1,"marriage",$who);
			output("You have chosen to block `0%s`^ from flirting. A YoM has been sent to this player indicating that they are blocked from flirting.`n`n",$name);
			require_once("lib/systemmail.php");
			$t = array("`^Royal Decree to Block Flirting");
			$mail1=array("`^By Royal Decree, you have been blocked from flirting with any players by `&%s`^. If you feel this was done in error please contact `&%s`^.",$suname,$suname);
			systemmail($who,$t,$mail1);
		}
	}
break;
case "unblockf":
	output("`c`b`^Unblock Flirting`b`c`n");
	$who = httpget('who');
	if ($who==""){
		output("Who do you wish to unblock in order to allow flirtation?`n");
		$subop = httpget('subop');
		$search = translate_inline("Search");
		rawoutput("<form action='runmodule.php?module=marriage&op=superuser&op2=unblockf&subop=search' method='POST'><input name='name' id='name'><input type='submit' class='button' value='$search'></form>");
		addnav("","runmodule.php?module=marriage&op=superuser&op2=unblockf&subop=search");
		rawoutput("<script language='JavaScript'>document.getElementById('name').focus();</script>");
		if ($subop=="search"){
			$search = "%";
			$name = httppost('name');
			for ($i=0;$i<strlen($name);$i++){
				$search.=substr($name,$i,1)."%";
			}
			//$sql = "SELECT name,acctid FROM " . db_prefix("accounts") . " WHERE (locked=0 AND name LIKE '$search')";
			$sql = "SELECT ".db_prefix("module_userprefs").".value, ".db_prefix("accounts").".name, ".db_prefix("accounts").".acctid, ".db_prefix("accounts").".marriedto FROM " . db_prefix("module_userprefs") . "," . db_prefix("accounts") . " WHERE (locked=0 AND name LIKE '$search') AND acctid = userid AND modulename = 'marriage' AND setting = 'supernoflirt' AND value = 1 ORDER BY name ASC";
			$result = db_query($sql);
			$max = db_num_rows($result);
			if ($max > 100) {
				output("`n`nToo many names in list.  Please choose from the first 100 or narrow down your search.");
				$max = 100;
			}
			$n = translate_inline("Name");
			rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
			rawoutput("<tr class='trhead'><td><center>$n</center></td></tr>");
			if ($max==0){
				rawoutput("<tr class='trhilight'><td>");
				output_notl("`^");
				output("No Blocked Players Found by That Name");
				rawoutput("</td></tr>");
			}else{
				for ($i=0;$i<$max;$i++){
					rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td>");
					$row = db_fetch_assoc($result);
					rawoutput("<a href='runmodule.php?module=marriage&op=superuser&op2=unblockf&who=".rawurlencode($row['acctid'])."'>");
					output_notl("`0[%s`0]", $row['name']);
					rawoutput("</a></td></tr>");
					addnav("","runmodule.php?module=marriage&op=superuser&op2=unblockf&who=".rawurlencode($row['acctid']));
				}
			}
		rawoutput("</table>");
		}else{
	//List the currently blocked players
	$pp = 50;
	$pageoffset = (int)$page;
	if ($pageoffset > 0) $pageoffset--;
	$pageoffset *= $pp;
	$limit = "LIMIT $pageoffset,$pp";
	$sql = "SELECT COUNT(*) AS c FROM " . db_prefix("module_userprefs") . " WHERE modulename = 'marriage' AND setting = 'supernoflirt' AND value > 0";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$total = $row['c'];
	$count = db_num_rows($result);
	if (($pageoffset + $pp) < $total){
		$cond = $pageoffset + $pp;
	}else{
		$cond = $total;
	}
	$sql = "SELECT ".db_prefix("module_userprefs").".value, ".db_prefix("accounts").".name, ".db_prefix("accounts").".acctid, ".db_prefix("accounts").".marriedto FROM " . db_prefix("module_userprefs") . "," . db_prefix("accounts") . " WHERE acctid = userid AND modulename = 'marriage' AND setting = 'supernoflirt' AND value > 0 $limit";
	$result = db_query($sql);
	$name = translate_inline("Name");
	$sublock = translate_inline("SU Blocked");
	$none = translate_inline("No Players Are Superuser Blocked from Flirting");
	output("`n`cPlayers Currently Blocked from Flirting by Superuser`n`c");
	rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
	rawoutput("<tr class='trhead'><td><center>$name</center></td></tr>");
	if (db_num_rows($result)>0){
		for($i = $pageoffset; $i < $cond && $count; $i++) {
			$row = db_fetch_assoc($result);
			$id=$row['acctid'];
			if ($row['name']==$session['user']['name']){
				rawoutput("<tr class='trhilight'><td>");
			}else{
				rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td>");
			}
			rawoutput("<a href='runmodule.php?module=marriage&op=superuser&op2=unblockf&who=".rawurlencode($row['acctid'])."'>");
			output_notl("`0[%s`0]", $row['name']);
			rawoutput("</a></td></tr>");
			addnav("","runmodule.php?module=marriage&op=superuser&op2=unblockf&who=".rawurlencode($row['acctid']));
        }
	}else  output_notl("<tr class='trlight'><td colspan='7' align='center'>`&$none`0</td></tr>",true);
	rawoutput("</table>");
	if ($total>$pp){
		addnav("Pages");
		for ($p=0;$p<$total;$p+=$pp){
			addnav(array("Page %s (%s-%s)", ($p/$pp+1), ($p+1), min($p+$pp,$total)), "runmodule.php?module=marriage&op=superuser&op2=blockf&page=".($p/$pp+1));
		}
	}
		}
	}else{
		$sql = "SELECT name FROM " . db_prefix("accounts") . " WHERE acctid='$who'";
		$result = db_query($sql);
		if (get_module_pref("supernoflirt","marriage",$who)==0) output("That player already is not currently blocked.`n`n");
		else{
			$suname=$session['user']['name'];
			$row = db_fetch_assoc($result);
			$name = $row['name'];
			set_module_pref("supernoflirt",0,"marriage",$who);
			output("You have chosen to unblock `^%s`^ and allow flirting. A YoM has been sent to this player indicating that they are unblocked from flirting.`n`n",$name);
			require_once("lib/systemmail.php");
			$t = array("`^Royal Decree to Unblock Flirting");
			$mail1=array("`^By Royal Decree, you have been unblocked from flirting by `&%s`^.",$suname);
			systemmail($who,$t,$mail1);
		}
	}
break;
case "blockp":
	output("`c`b`^Block Proposals`b`c`n");
	$who = httpget('who');
	if ($who==""){
		output("Blocking a player from proposals prevents players from proposing OR being proposed to.");
		if (is_module_active("lovers")) output("This does `\$NOT`^ prevent Seth/Violet Marriages.");
		output_notl("`n`n");
		if (get_module_setting("flirttype")==1) output("`\$Your system is currently set up to use flirting. It is more appropriate to block flirting for a character.`^");
		else output("Your system is currently set up for proposals only and this will effectively prevent proposing.");
		output("`n`nWho do you wish to block from proposals?`n");
		$subop = httpget('subop');
		$search = translate_inline("Search");
		rawoutput("<form action='runmodule.php?module=marriage&op=superuser&op2=blockp&subop=search' method='POST'><input name='name' id='name'><input type='submit' class='button' value='$search'></form>");
		addnav("","runmodule.php?module=marriage&op=superuser&op2=blockp&subop=search");
		rawoutput("<script language='JavaScript'>document.getElementById('name').focus();</script>");
		if ($subop=="search"){
			$search = "%";
			$name = httppost('name');
			for ($i=0;$i<strlen($name);$i++){
				$search.=substr($name,$i,1)."%";
			}
			$sql = "SELECT name,acctid FROM " . db_prefix("accounts") . " WHERE (locked=0 AND name LIKE '$search') ORDER BY name ASC";
			//$sql = "SELECT ".db_prefix("module_userprefs").".value, ".db_prefix("accounts").".name, ".db_prefix("accounts").".acctid, ".db_prefix("accounts").".marriedto FROM " . db_prefix("module_userprefs") . "," . db_prefix("accounts") . " WHERE (locked=0 AND name LIKE '$search') AND acctid = userid AND modulename = 'marriage' AND setting = 'supernoflirt' AND value = 0";
			$result = db_query($sql);
			$max = db_num_rows($result);
			if ($max > 100) {
				output("`n`nToo many names in list.  Please choose from the first 100 or narrow down your search.");
				$max = 100;
			}
			$n = translate_inline("Name");
			rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
			rawoutput("<tr class='trhead'><td><center>$n</center></td></tr>");
			if ($max==0){
				rawoutput("<tr class='trhilight'><td>");
				output_notl("`^");
				output("No Unblocked Players Found by That Name");
				rawoutput("</td></tr>");
			}else{
				for ($i=0;$i<$max;$i++){
					$row = db_fetch_assoc($result);
					rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td>");
					rawoutput("<a href='runmodule.php?module=marriage&op=superuser&op2=blockp&who=".rawurlencode($row['acctid'])."'>");
					output_notl("`0[%s`0]", $row['name']);
					rawoutput("</a></td></tr>");
					addnav("","runmodule.php?module=marriage&op=superuser&op2=blockp&who=".rawurlencode($row['acctid']));
				}
			}
		rawoutput("</table>");
		}else{
	//List the currently blocked players
	$pp = 50;
	$pageoffset = (int)$page;
	if ($pageoffset > 0) $pageoffset--;
	$pageoffset *= $pp;
	$limit = "LIMIT $pageoffset,$pp";
	$sql = "SELECT COUNT(*) AS c FROM " . db_prefix("module_userprefs") . " WHERE modulename = 'marriage' AND setting = 'supernomarry' AND value > 0";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$total = $row['c'];
	$count = db_num_rows($result);
	if (($pageoffset + $pp) < $total){
		$cond = $pageoffset + $pp;
	}else{
		$cond = $total;
	}
	$sql = "SELECT ".db_prefix("module_userprefs").".value, ".db_prefix("accounts").".name, ".db_prefix("accounts").".acctid, ".db_prefix("accounts").".marriedto FROM " . db_prefix("module_userprefs") . "," . db_prefix("accounts") . " WHERE acctid = userid AND modulename = 'marriage' AND setting = 'supernomarry' AND value > 0 $limit";
	$result = db_query($sql);
	$name = translate_inline("Name");
	$sublock = translate_inline("SU Blocked");
	$selfblock= translate_inline("Voluntarily Blocked");
	$none = translate_inline("No Players Are Superuser Blocked for Proposals");
	$marriedto=translate_inline("Spouse");
	output("`n`cPlayers Currently Blocked for Proposals by Superuser`n`c");
	rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
	rawoutput("<tr class='trhead'><td>$name</td><td>$sublock</td><td>$selfblock</td><td>$marriedto</td></tr>");
	if (db_num_rows($result)>0){
		for($i = $pageoffset; $i < $cond && $count; $i++) {
			$row = db_fetch_assoc($result);
			$id=$row['acctid'];
			if ($row['name']==$session['user']['name']){
				rawoutput("<tr class='trhilight'><td>");
			}else{
				rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td>");
			}
			output_notl("`&%s`0",$row['name']);
			rawoutput("</td><td><center>");
			$block1= translate_inline("Yes");
			output_notl("%s",$block1);
			rawoutput("</center></td><td><center>");
			$bl=get_module_pref("user_wed","marriage",$id);
			$block2 = translate_inline($bl?"Yes":"No");
			output_notl("%s",$block2);
			rawoutput("</center></td><td>");
			if ($row['marriedto']>0){
				$sql1 = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid='$id' AND locked=0";
				$res1 = db_query($sql1);
				$row1 = db_fetch_assoc($res1);
				$spouse=$row['name'];
			}else{
				$spouse=translate_inline("None");
			}
			output_notl("%s",$spouse);
			rawoutput("</td></tr>");
        }
	}else  output_notl("<tr class='trlight'><td colspan='7' align='center'>`&$none`0</td></tr>",true);
	rawoutput("</table>");
	if ($total>$pp){
		addnav("Pages");
		for ($p=0;$p<$total;$p+=$pp){
			addnav(array("Page %s (%s-%s)", ($p/$pp+1), ($p+1), min($p+$pp,$total)), "runmodule.php?module=marriage&op=superuser&op2=blockp&page=".($p/$pp+1));
		}
	}
	}
	}else{
		$sql = "SELECT name FROM " . db_prefix("accounts") . " WHERE acctid='$who'";
		$result = db_query($sql);
		if (get_module_pref("supernomarry","marriage",$who)==1) output("That player already has a superuser block for proposals.`n`n");
		else{
			$suname=$session['user']['name'];
			$row = db_fetch_assoc($result);
			$name = $row['name'];
			set_module_pref("supernomarry",1,"marriage",$who);
			output("You have chosen to block `0%s`^ for proposals. A YoM has been sent to this player indicating that they are blocked for proposals.`n`n",$name);
			require_once("lib/systemmail.php");
			$t = array("`^Royal Decree to Block Proposals");
			$mail1=array("`^By Royal Decree, you have been blocked for proposals (accepting or proposing) with all players by `&%s`^. If you feel this was done in error please contact `&%s`^.",$suname,$suname);
			systemmail($who,$t,$mail1);
		}
	}
break;
case "unblockp":
	output("`c`b`^Unblock Proposals`b`c`n");
	$who = httpget('who');
	if ($who==""){
		output("Who do you wish to unblock in order to allow proposals?`n");
		$subop = httpget('subop');
		$search = translate_inline("Search");
		rawoutput("<form action='runmodule.php?module=marriage&op=superuser&op2=unblockp&subop=search' method='POST'><input name='name' id='name'><input type='submit' class='button' value='$search'></form>");
		addnav("","runmodule.php?module=marriage&op=superuser&op2=unblockp&subop=search");
		rawoutput("<script language='JavaScript'>document.getElementById('name').focus();</script>");
		if ($subop=="search"){
			$search = "%";
			$name = httppost('name');
			for ($i=0;$i<strlen($name);$i++){
				$search.=substr($name,$i,1)."%";
			}
			//$sql = "SELECT name,acctid FROM " . db_prefix("accounts") . " WHERE (locked=0 AND name LIKE '$search')";
			$sql = "SELECT ".db_prefix("module_userprefs").".value, ".db_prefix("accounts").".name, ".db_prefix("accounts").".acctid, ".db_prefix("accounts").".marriedto FROM " . db_prefix("module_userprefs") . "," . db_prefix("accounts") . " WHERE (locked=0 AND name LIKE '$search') AND acctid = userid AND modulename = 'marriage' AND setting = 'supernomarry' AND value = 1 ORDER BY name ASC";
			$result = db_query($sql);
			$max = db_num_rows($result);
			if ($max > 100) {
				output("`n`nToo many names in list.  Please choose from the first 100 or narrow down your search.");
				$max = 100;
			}
			$n = translate_inline("Name");
			rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
			rawoutput("<tr class='trhead'><td><center>$n</center></td></tr>");
			if ($max==0){
				rawoutput("<tr class='trhilight'><td>");
				output_notl("`^");
				output("No Blocked Players Found by That Name");
				rawoutput("</td></tr>");
			}else{
				for ($i=0;$i<$max;$i++){
					rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td>");
					$row = db_fetch_assoc($result);
					rawoutput("<a href='runmodule.php?module=marriage&op=superuser&op2=unblockp&who=".rawurlencode($row['acctid'])."'>");
					output_notl("`0[%s`0]", $row['name']);
					rawoutput("</a></td></tr>");
					addnav("","runmodule.php?module=marriage&op=superuser&op2=unblockp&who=".rawurlencode($row['acctid']));
				}
			}
		rawoutput("</table>");
		}else{
	//List the currently blocked players
	$pp = 50;
	$pageoffset = (int)$page;
	if ($pageoffset > 0) $pageoffset--;
	$pageoffset *= $pp;
	$limit = "LIMIT $pageoffset,$pp";
	$sql = "SELECT COUNT(*) AS c FROM " . db_prefix("module_userprefs") . " WHERE modulename = 'marriage' AND setting = 'supernomarry' AND value > 0";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$total = $row['c'];
	$count = db_num_rows($result);
	if (($pageoffset + $pp) < $total){
		$cond = $pageoffset + $pp;
	}else{
		$cond = $total;
	}
	$sql = "SELECT ".db_prefix("module_userprefs").".value, ".db_prefix("accounts").".name, ".db_prefix("accounts").".acctid, ".db_prefix("accounts").".marriedto FROM " . db_prefix("module_userprefs") . "," . db_prefix("accounts") . " WHERE acctid = userid AND modulename = 'marriage' AND setting = 'supernomarry' AND value > 0 $limit";
	$result = db_query($sql);
	$name = translate_inline("Name");
	$sublock = translate_inline("SU Blocked");
	$none = translate_inline("No Players Are Superuser Blocked for Proposals");
	output("`n`cPlayers Currently Blocked for Proposals by Superuser`n`c");
	rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
	rawoutput("<tr class='trhead'><td><center>$name</center></td></tr>");
	if (db_num_rows($result)>0){
		for($i = $pageoffset; $i < $cond && $count; $i++) {
			$row = db_fetch_assoc($result);
			$id=$row['acctid'];
			if ($row['name']==$session['user']['name']){
				rawoutput("<tr class='trhilight'><td>");
			}else{
				rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td>");
			}
			rawoutput("<a href='runmodule.php?module=marriage&op=superuser&op2=unblockp&who=".rawurlencode($row['acctid'])."'>");
			output_notl("`0[%s`0]", $row['name']);
			rawoutput("</a></td></tr>");
			addnav("","runmodule.php?module=marriage&op=superuser&op2=unblockp&who=".rawurlencode($row['acctid']));
        }
	}else  output_notl("<tr class='trlight'><td colspan='7' align='center'>`&$none`0</td></tr>",true);
	rawoutput("</table>");
	if ($total>$pp){
		addnav("Pages");
		for ($p=0;$p<$total;$p+=$pp){
			addnav(array("Page %s (%s-%s)", ($p/$pp+1), ($p+1), min($p+$pp,$total)), "runmodule.php?module=marriage&op=superuser&op2=blockf&page=".($p/$pp+1));
		}
	}
		}
	}else{
		$sql = "SELECT name FROM " . db_prefix("accounts") . " WHERE acctid='$who'";
		$result = db_query($sql);
		if (get_module_pref("supernomarry","marriage",$who)==0) output("That player already is not currently blocked.`n`n");
		else{
			$suname=$session['user']['name'];
			$row = db_fetch_assoc($result);
			$name = $row['name'];
			set_module_pref("supernomarry",0,"marriage",$who);
			output("You have chosen to unblock `^%s`^ and allow proposals. A YoM has been sent to this player indicating that they are unblocked from proposals.`n`n",$name);
			require_once("lib/systemmail.php");
			$t = array("`^Royal Decree to Unblock Proposals");
			$mail1=array("`^By Royal Decree, you have been unblocked for proposals by `&%s`^.",$suname);
			systemmail($who,$t,$mail1);
		}
	}
break;
}



page_footer();
?>