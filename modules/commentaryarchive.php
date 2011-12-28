<?php

function commentaryarchive_getmoduleinfo(){
	$info = array(
		"name"=>"Commentary Archive",
		"version"=>"2009-09-29",
		"author"=>"Dan Hall",
		"category"=>"Social",
		"download"=>"",
		"override_forced_nav"=>true,
	);
	return $info;
}
function commentaryarchive_install(){
	module_addhook("commentaryoptions");
	return true;
}
function commentaryarchive_uninstall(){
	return true;
}
function commentaryarchive_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "commentaryoptions":
			if ($session['user']['donation']>=1000){
				$sec=$session['user']['chatloc'];
				rawoutput("<a href=\"runmodule.php?module=commentaryarchive\" target='_blank' onclick=\"".popup("runmodule.php?module=commentaryarchive").";return false;\">Export Commentary to HTML</a><br /><br />");
			}
			break;
		}
	return $args;
}
function commentaryarchive_run(){
	global $session;
	popup_header("Commentary Export");
	debug($args);
	if ($session['user']['donation']>=1000){
		$sec=$session['user']['chatloc'];
		if ($sec){
			output("This is a HTML output of the entire Commentary section you're currently viewing, complete with colour codes.  Use it for archiving memorable roleplaying sessions, outputting on forums or blogs, or what-have-you.`n`nTo use it, copy-paste the output in the textarea below into your favourite plain-text editor (notepad will do in a pinch) and save the results as somefilenameoranother.html.`n`nIf you need help archiving, feel free to ask around for assistance in the Enquirer or Location Four.`n`n`bImportant`b - when trying to copy-paste (or even output) a commentary area with hundreds of commentary pages, your computer may hang for a while.  If this happens, quit pressing buttons, go and have a cup of tea and let it get on with it.`n`nHave fun!`n`n");
			$sql = "SELECT " . db_prefix("commentary") . ".*, " .
				db_prefix("accounts").".name, " .
				db_prefix("accounts").".acctid, " .
				db_prefix("accounts").".clanrank, " .
				db_prefix("clans").".clanshort FROM " .
				db_prefix("commentary") . " LEFT JOIN " .
				db_prefix("accounts") . " ON " .
				db_prefix("accounts") . ".acctid = " .
				db_prefix("commentary"). ".author LEFT JOIN " .
				db_prefix("clans") . " ON " . db_prefix("clans") . ".clanid=" .
				db_prefix("accounts") .
				".clanid WHERE " . ($sec ? "section='$sec' AND " : '') .
				"( ".db_prefix("accounts") . ".locked=0 OR ".db_prefix("accounts") .".locked is null ) ".
				"ORDER BY commentid ASC";
			$result = db_query($sql);
			rawoutput("<textarea>");
			rawoutput("<?xml version=\"1.0\" encoding=\"utf-8\"?>");
			rawoutput("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"");
			rawoutput("        \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">");
			rawoutput("<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">");
			rawoutput("<head>");
			rawoutput("	<title>Improbable Island Commentary Export from section ".$sec."</title>");
			rawoutput("	<meta http-equiv=\"content-type\" ");
			rawoutput("		content=\"text/html;charset=utf-8\" />");
			rawoutput("	<meta http-equiv=\"Content-Style-Type\" content=\"text/css\" />");
			rawoutput("	<style type=\"text/css\">");
			rawoutput("		body {background-color: #C9B18B; font-family: georgia, serif; font-size:smaller; color:#111111;}");
			rawoutput("		.colDkBlue    { color: #000040; }");
			rawoutput("		.colDkGreen   { color: #004000; }");
			rawoutput("		.colDkCyan    { color: #004040; }");
			rawoutput("		.colDkRed     { color: #400000; }");
			rawoutput("		.colDkMagenta { color: #400040; }");
			rawoutput("		.colDkYellow  { color: #404000; }");
			rawoutput("		.colDkWhite   { color: #303030; }");
			rawoutput("		.colLtBlue    { color: #000060; }");
			rawoutput("		.colLtGreen   { color: #006000; }");
			rawoutput("		.colLtCyan    { color: #006060; }");
			rawoutput("		.colLtRed     { color: #600000; }");
			rawoutput("		.colLtMagenta { color: #600060; }");
			rawoutput("		.colLtYellow  { color: #606000; }");
			rawoutput("		.colLtWhite   { color: #505050; }");
			rawoutput("		.colLtBlack   { color: #222222; }");
			rawoutput("		.colDkOrange  { color: #4a2100; }");
			rawoutput("		.colLtOrange  { color: #994400; }");
			rawoutput("		.colBlue  	{ color: #0070FF; }");
			rawoutput("		.colLime  	{ color: #DDFFBB; }");
			rawoutput("		.colBlack  	{ color: #000000; }");
			rawoutput("		.colRose 	{ color: #9F819F; }");
			rawoutput("		.colblueviolet 	{ color: #9A5BEE; }");
			rawoutput("		.coliceviolet	{ color: #6D7B9F; }");
			rawoutput("		.colLtBrown 	{ color: #8F7D47; }");
			rawoutput("		.colDkBrown 	{ color: #6b563f; }");
			rawoutput("		.colXLtGreen	{ color: #009900; }");
			rawoutput("		.colAttention 	{ background-color: #00FF00; color: #FF0000; }");
			rawoutput("		.colWhiteBlack 	{ background-color: #FFFFFF; color: #000000; }");
			rawoutput("		.colbeige  { color: #F5F5DC; }");
			rawoutput("		.colkhaki  { color: #F0E68C; }");
			rawoutput("		.coldarkkhaki  { color: #5F5B35; }");
			rawoutput("		.colaquamarine  { color: #7FFFD4; }");
			rawoutput("		.coldarkseagreen  { color: #8FBC8F; }");
			rawoutput("		.collightsalmon  { color: #8F6859; }");
			rawoutput("		.colsalmon  { color: #7F5D4F; }");
			rawoutput("		.colwheat  { color: #F5DEB3; }");
			rawoutput("		.coltan  { color: #D2B48C; }");
			rawoutput("		.colBack  	{ background-color: #00FFFF; color: #000000; }");
			rawoutput("		.colLtLinkBlue { color: #0069AF; }");
			rawoutput("		.colDkLinkBlue { color: #004C7F; }");
			rawoutput("		.colDkRust { color: #8D6060; }");
			rawoutput("		.colLtRust { color: #B07878; }");
			rawoutput("		.colMdBlue { color: #0000F0; }");
			rawoutput("		.colMdGrey { color: #444444; }");
			rawoutput("		.colburlywood { color: #DEB887; }");
			rawoutput("	</style>");
			rawoutput("</head>");
			rawoutput("<body>");
			for ($i=0;$i<db_num_rows($result);$i++){
				$row=db_fetch_assoc($result);
				$row['comment'] = comment_sanitize($row['comment']);
				output("`0".$row['postdate'].": ".$row['name'].": `#".$row['comment']."</span><br />");
			}
			rawoutput("</body>");
			rawoutput("</html>");
			rawoutput("</textarea>");
		} else {
			output("You're not in any commentary area right now.`n`n");
		}
	} else {
		output("Commentary Export is a pretty high-load deal - for that reason, we've restricted it to donators only.  Sorry about that.`n`n");
	}
	
// array(9) {
// 'commentid' = '103'
// 'section' = 'village-Mutant'
// 'author' = '1'
// 'comment' = '1'
// 'postdate' = '2009-09-24 10:52:29'
// 'name' = 'Admin CavemanJoe'
// 'acctid' = '1'
// 'clanrank' = '0'
// 'clanshort' = ''
// }
	
	popup_footer();
}
?>