<?php

function findcomm_getmoduleinfo(){
	$info = array(
		"name"=>"Finding Commentary",
		"author"=>"Chris Vorndran",
		"version"=>"1.02",
		"category"=>"Administrative",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=53",
		"vertxtloc"=>"http://dragonprime.net/users/Sichae/",
	);
	return $info;
}
function findcomm_install(){
	module_addhook("superuser");
	module_addhook("biostat");
	return true;
}
function findcomm_uninstall(){
	return true;
}
function findcomm_dohook($hookname,$args){
	global $session;
	switch ($hookname){
		case "superuser":
			if ($session['user']['superuser'] & SU_EDIT_COMMENTS){
				addnav("Actions");
				addnav("Find User Comments","runmodule.php?module=findcomm&op=enter");
			}
			break;
		case "biostat":
			if ($session['user']['superuser'] & SU_EDIT_COMMENTS){
				addnav("Superuser");
				addnav("Find User Comments","runmodule.php?module=findcomm&op=list&id={$args['acctid']}");
			}			
		}
	return $args;
}
function findcomm_run(){
	global $session;
	$op = httpget('op');
	page_header("Find Commentary");
	if ($op != "enter") addnav("Find Another Name","runmodule.php?module=findcomm&op=enter");
	switch ($op){
		case "enter":
			output("Type in the name of the person whose commentary you wish to find.`n`n");
			rawoutput("<form action='runmodule.php?module=findcomm&op=list' method='post'>");
			rawoutput("Name: <input type='text' name='name'>");
			rawoutput("<input type='submit' class='button' value='".translate_inline("Find")."'></form>");
			addnav("","runmodule.php?module=findcomm&op=list");
			break;
		case "list":
			$del = httppost('del');
			if ($del != ""){
				$sql = "SELECT " .
					db_prefix("commentary").".*,".db_prefix("accounts").".name,".
					db_prefix("accounts").".login, ".db_prefix("accounts").".clanrank,".
					db_prefix("clans").".clanshort FROM ".db_prefix("commentary").
					" INNER JOIN ".db_prefix("accounts")." ON ".
					db_prefix("accounts").".acctid = " . db_prefix("commentary").
					".author LEFT JOIN ".db_prefix("clans")." ON ".
					db_prefix("clans").".clanid=".db_prefix("accounts").
					".clanid WHERE commentid IN ('".join("','",array_keys($del))."')";
				$res = db_query($sql);
				while ($row = db_fetch_assoc($res)){
					$sql = "INSERT LOW_PRIORITY INTO ".db_prefix("moderatedcomments")." (moderator,moddate,comment) 
							VALUES ('{$session['user']['acctid']}','".date("Y-m-d H:i:s")."','".addslashes(serialize($row))."')";
					db_query($sql);
				}
				$sql = "DELETE FROM ".db_prefix("commentary")." 
						WHERE commentid IN ('".join("','",array_keys($del))."')";
				db_query($sql);
				output("Comments Deleted.`n`n");
			}
			$id = httpget('id');
			if ($id == ""){
				$name = httppost('name');
				$search = "%";
				for ($i = 0; $i < strlen($name); $i++){
					$search .= substr($name,$i,1)."%";
				}
				debug($search);
				$sql = "SELECT name,acctid FROM ".db_prefix("accounts")." 
						WHERE (name LIKE '$search' OR login LIKE '$search') 
						LIMIT 25";
				$res = db_query($sql);
				$count = db_num_rows($res);
				$n = translate_inline("Name");
				rawoutput("<table border=0 cellpadding=2 align='center' cellspacing=1 bgcolor='#999999'>",true);
				rawoutput("<tr class='trhead'><td>$n</td></tr>");
				$i = 0;
				while($row = db_fetch_assoc($res)){
					$i++;
					$ac = $row['acctid'];
					rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>");
					rawoutput("<a href='runmodule.php?module=findcomm&op=list&id=".rawurlencode($ac)."'>");
					output_notl("%s", $row['name']);
					rawoutput("</a>");
					addnav("","runmodule.php?module=findcomm&op=list&id=".rawurlencode($ac));
					rawoutput("</td></tr>");
				}
				rawoutput("</table>");		
			}else{
				$sql = "SELECT DISTINCT section FROM ".db_prefix("commentary")." 
						WHERE author='$id' 
						ORDER BY section ASC";
				$res = db_query($sql);
				$section = translate_inline("Section");
				$comments = translate_inline("Comments");
				rawoutput("<form action='runmodule.php?module=findcomm&op=list&id=$id' method='post'>");
				rawoutput("<table border=0 cellpadding=2 align='center' cellspacing=1 bgcolor='#999999'>",true);
				rawoutput("<tr class='trhead'><td>$section</td><td>$comments</td></tr>");
				$i = 0;
				while($row = db_fetch_assoc($res)){
					$i++;
					$ac = $row['acctid'];
					rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>");
					output_notl("`b%s`b`0", $row['section']);
					rawoutput("</td><td>");
					output_notl("%s",findcomm_findcomment($row['section'],$id),true);
					rawoutput("</td></tr>");
				}
				rawoutput("</table>");	
				rawoutput("<input type='submit' class='button' value='".translate_inline("Delete")."'></form>");
				addnav("","runmodule.php?module=findcomm&op=list&id=$id");
			}
			break;
		}
	require_once("lib/superusernav.php");
	superusernav();
	page_footer();
}
function findcomm_findcomment($section,$id){
	$sql = "SELECT comment,commentid,postdate FROM ".db_prefix("commentary")." 
			WHERE section='$section' 
			AND author='$id' 
			ORDER BY commentid ASC";
	$res = db_query($sql);
	$str = "";
	$i = 0;
	require_once("lib/datetime.php");
	while($row = db_fetch_assoc($res)){
		$relative_time = reltime(strtotime($row['postdate']));
		$str .= "<input name='del[{$row['commentid']}]' type='checkbox'> - ";
		$str .= "`0($relative_time) " . $row['comment']."`n";
	}
	return $str;
}
?>