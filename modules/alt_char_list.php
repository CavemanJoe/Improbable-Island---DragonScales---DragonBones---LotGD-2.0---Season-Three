<?php

function alt_char_list_getmoduleinfo(){
	$info = array(
		"name"=>"Alternate Character Listing",
		"author"=>"Chris Vorndran",
		"version"=>"1.0",
		"category"=>"Administrative",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1343",
		"settings"=>array(
			"Alternate Character Listing Settings,title",
			"pp"=>"How many players should be displayed per page?,int|50",
		),
		"prefs"=>array(
			"allowed"=>"User allowed to view alts?,bool|0",
		),
	);
	return $info;
}

function alt_char_list_install(){
	module_addhook("superuser");
	module_addhook("bioinfo");
	return true;
}

function alt_char_list_uninstall(){
	return true;
}

function alt_char_list_dohook($hookname,$args){
	global $session;
	switch ($hookname){
		case "superuser":
			$allowed = (($session['user']['superuser'] & SU_EDIT_COMMENTS) || get_module_pref('allowed'));
			if ($allowed){
				addnav("Actions");
				addnav("Alternate Character List","runmodule.php?module=alt_char_list");
			}
			break;
		case "bioinfo":
			$allowed = (($session['user']['superuser'] & SU_EDIT_COMMENTS) || get_module_pref('allowed'));
			debug($allowed);
			if ($allowed){
				addnav("Superuser");
				$id = rawurlencode($args['acctid']);
				addnav("View Possible Alts","runmodule.php?module=alt_char_list&id=$id&ret=".urlencode($args['return_link']));
			}
			break;
	}
	return $args;
}

function alt_char_list_run(){
	global $session;
	
	page_header("Alternate Character List");
	
	$op = httpget('op');
	$ac = db_prefix("accounts");
	switch ($op){
		case "":
			$nmf = translate_inline("`inone`i");
			$page = httpget('page');
			$id = rawurldecode(httpget('id'));
			$pp = get_module_setting("pp");	
			$pageoffset = (int)$page;
			if ($pageoffset > 0) $pageoffset--;
			$pageoffset *= $pp;
			$limit = "LIMIT $pageoffset,$pp";
			$sql = "SELECT count(acctid) AS c FROM $ac";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			$total = $row['c'];
			$count = db_num_rows($result);
			if (($pageoffset + $pp) < $total){
				$cond = $pageoffset + $pp;
			}else{
				$cond = $total;
			}
			$extra = "";
			if ($id <> "") $extra = "WHERE acctid = $id";
			$sql = "SELECT acctid, name, login, lastip, uniqueid, emailaddress FROM $ac $extra ORDER BY acctid ASC $limit";
			$acct_name = translate_inline("Character Name (login)");
			$ip = translate_inline("Alts by IP");
			$id = translate_inline("Alts by ID");
			$email = translate_inline("Alts by Email");
			$result = db_query($sql);
			rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
			rawoutput("<tr class='trhead'><td>$acct_name</td><td>$ip</td><td>$id</td><td>$email</td></tr>");
			if (db_num_rows($result)>0){
				$i = 0;
				while($row = db_fetch_assoc($result)){
					$i++;
					rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td>");
					output_notl("`&%s (%s)`0",$row['name'],$row['login']);
					rawoutput("</td><td style='text-align:center;'>");
					$sql_ip = "SELECT name, login FROM $ac WHERE lastip = '{$row['lastip']}' AND acctid != {$row['acctid']}";
					$res_ip = db_query($sql_ip);
					if (db_num_rows($res_ip) > 0){
						while ($row_ip = db_fetch_assoc($res_ip)){
							output_notl("`b%s`b (%s)`n",$row_ip['name'],$row_ip['login']);
						}
					}else{
						output_notl("%s",$nmf);
					}
					rawoutput("</td><td style='text-align:center;'>");
					$sql_id = "SELECT name, login FROM $ac WHERE uniqueid = '{$row['uniqueid']}' AND acctid != {$row['acctid']}";
					$res_id = db_query($sql_id);
					if (db_num_rows($res_id) > 0){
						while ($row_id = db_fetch_assoc($res_id)){
							output_notl("`b%s`b (%s)`n",$row_id['name'],$row_id['login']);
						}
					}else{
						output_notl("%s",$nmf);
					}
					rawoutput("</td><td style='text-align:center;'>");
					$sql_email = "SELECT name, login FROM $ac WHERE emailaddress = '{$row['emailaddress']}' AND acctid != {$row['acctid']}";
					$res_email = db_query($sql_email);
					if (db_num_rows($res_email) > 0){
						while ($row_email = db_fetch_assoc($res_email)){
							output_notl("`b%s`b (%s)`n",$row_email['name'],$row_email['login']);
						}
					}else{
						output_notl("%s",$nmf);
					}
					rawoutput("</td></tr>");
				}
			}
			rawoutput("</table>");
			if ($total > $pp){
				addnav("Pages");
				for ($p = 0; $p < $total; $p += $pp){
					addnav(array("Page %s (%s-%s)", ($p/$pp+1), ($p+1), min($p+$pp,$total)), "runmodule.php?module=alt_char_list&page=".($p/$pp+1));
				}
			}
			break;
		case "search":
			addnav("Main Page","runmodule.php?module=alt_char_list");
			if (httppost('submit')){
				$name = httppost('char_name');
				$search = "%";
				for ($i=0;$i<strlen($name);$i++){
					$search.=substr($name,$i,1)."%";
				}
				debug($search);
				$sql = "SELECT name, acctid, lastip, uniqueid, emailaddress FROM $ac WHERE (name LIKE '$search' OR login LIKE '$search')";
				$res = db_query($sql);
				$count = db_num_rows($res);
				$n = translate_inline("Name");
				$ip = translate_inline("IP");
				$id = translate_inline("ID");
				$email = translate_inline("Email");
				rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
				rawoutput("<tr class='trhead'><td>$n</td><td>$ip</td><td>$id</td><td>$email</td></tr>");
				$i = 0;
				while($row = db_fetch_assoc($res)){
					$i++;
					$id = $row['acctid'];
					rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>");
					rawoutput("<a href='runmodule.php?module=alt_char_list&id=".rawurlencode($id)."'>");
					output_notl("%s", $row['name']);
					rawoutput("</a>");
					addnav("","runmodule.php?module=alt_char_list&id=".rawurlencode($id));
					rawoutput("</td><td>");
					output_notl("%s",$row['lastip']);
					rawoutput("</td><td>");
					output_notl("%s",$row['uniqueid']);
					rawoutput("</td><td>");
					output_notl("%s",$row['emailaddress']);
					rawoutput("</td></tr>");
				}
				rawoutput("</table>");	
			}else{
				$char_name = translate_inline("Character Name");
				rawoutput("<form action='runmodule.php?module=alt_char_list&op=search' method='post'>");
				rawoutput("$char_name: <input type='text' name='char_name'/><br/>");
				rawoutput("<input class='button' type='submit' name='submit' value='".translate_inline("Submit")."'/></form>");
			}
			addnav("","runmodule.php?module=alt_char_list&op=search");
			break;
	}
	addnav("Other Actions");
	addnav("Search","runmodule.php?module=alt_char_list&op=search");
	$ret = urlencode(httpget("ret"));
	if ($ret <> "") addnav("Return to viewing character","bio.php?char=$char&ret=$ret");
	require_once("lib/superusernav.php");
	superusernav();
	page_footer();
}
?>