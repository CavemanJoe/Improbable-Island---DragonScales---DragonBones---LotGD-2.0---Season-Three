<?php
// translator ready
// addnews ready
// mail ready
require_once("common.php");
require_once("lib/commentary.php");
require_once("lib/sanitize.php");
require_once("lib/http.php");

tlschema("moderate");

addcommentary();

check_su_access(SU_EDIT_COMMENTS);

global $moderating;
$moderating=1;

require_once("lib/superusernav.php");
superusernav();

page_header("Commentary");

// rawoutput("<form action='moderate.php' method='POST'>");
// output("View all commentary since:`n");
// rawoutput("<input name='viewsince' value=\"\" size='46'>");
// output("`nThis uses php's strtotime function.  Enter something in the box above like 'yesterday' or '-10 minutes' or 'last thursday' or '5th june 2010' - PHP will, in most cases, know what to do.`n`n");
// rawoutput("<input type='submit' class='button' value='Go'>");
// rawoutput("</form>");
// addnav("","moderate.php");

addnav("B?Player Bios","bios.php");

addnav("View modes");
addnav("Separate","moderate.php?viewmode=separate");
addnav("All","moderate.php?viewmode=all");
addnav("More and better viewing modes coming soon, guys...","");

$sql = "SELECT DISTINCT section FROM ".db_prefix("commentary")." WHERE section NOT LIKE 'dwelling%' AND section NOT LIKE 'clan%' AND section NOT LIKE 'pet-%'";
$result = db_query($sql);
$locations = array();
addnav("Section-Specific");
while ($row=db_fetch_assoc($result)){
	$locations[]=$row['section'];
	addnav($row['section'],"moderate.php?viewmode=single&section=".$row['section']);
}

switch (httpget("viewmode")){
	case "separate":
		foreach($locations AS $key=>$loc){
			output("`b%s`b`n",$loc);
			viewcommentary($loc);
		}
	break;
	case "all":
		output("Trying to post to \"all\" is a Bad Idea!`n`n");
		viewcommentary("all");
	break;
	case "technical":
		rawoutput("<form action='moderate.php' method='POST'>");
		output("View all commentary since: ");
		rawoutput("<input name='viewsince' value=\"indifferent\" size='46'> (strtotime field)");
		output("(strtotime field)");
		output("From player: ");
		rawoutput("<input name='author' value=\"indifferent\" size='46'> (player's acctid) <input name='name' value=\"indifferent\" size='46'> (player's name)");
		output("Where section is like: ");
		rawoutput("<input name='section' value=\"indifferent\" size='46'>");
		output("Where the comment contains the word or phrase: ");
		rawoutput("<input name='search' value=\"indifferent\" size='46'>");
		output("And the commentid is greater than: ");
		rawoutput("<input name='commentid' value=\"indifferent\" size='46'>");
		rawoutput("<input type='submit' class='button' value='Go'>");
		rawoutput("</form>");
		addnav("","moderate.php");
	break;
	case "single":
		$sec = httpget("section");
		viewcommentary($sec);
	break;
}


//viewcommentary("all","",100,"says",false,true);



// if ($session['user']['superuser'] & SU_AUDIT_MODERATION){
	// addnav("Audit Moderation","moderate.php?op=audit");
// }

// if (httpget('deletecomment')){
	// $del = httpget('deletecomment');
	// $sql = "DELETE FROM ".db_prefix("commentary")." WHERE commentid=$del";
	// db_query($sql);
	// output("Deleted commentid %s",$del);
// }

// $viewsince = httppost('viewsince');
// $section = httpget('section');
// if (!$viewsince){
	// $viewsince = httpget('viewsince');
// }
// if ($viewsince){
	// $since = strtotime($viewsince);
	// $since = date("Y-m-d H:i:s",$since);
	// output("Viewing commentary since %s`n`n",$since);
	// if (!$section){
		// $sql = "SELECT * FROM ".db_prefix("commentary")." WHERE postdate > '$since' AND section NOT LIKE 'dwelling%' AND section NOT LIKE 'clan%' AND section NOT LIKE 'pet-%'";
	// } else {
		// $sql = "SELECT * FROM ".db_prefix("commentary")." WHERE postdate > '$since' AND section = '$section'";
	// }
	// $result = db_query($sql);
	// $sections = array();
	// while ($row = db_fetch_assoc($result)){
		// output_notl("%s: [<a href=\"moderate.php?deletecomment=".$row['commentid']."&viewsince=$since\">DEL</a>][".$row['section']."]%s`0: %s`0`n",$row['postdate'],$row['name'],$row['comment'],true);
		// addnav("","moderate.php?deletecomment=".$row['commentid']."&viewsince=$since");
		// $sections[$row['section']]+=1;
	// }
	// $sections = arsort($sections);
	// foreach($sections AS $section => $count){
		// addnav("Filter by Section");
		// addnav(array("%s (%s)",$section,$count),"moderate.php?viewsince=$viewsince&section=$section");
	// }
// } else {
	// $sql = "SELECT * FROM ".db_prefix("commentary")." WHERE postdate > '$since' AND section NOT LIKE 'dwelling%' AND section NOT LIKE 'clan%' AND section NOT LIKE 'pet-%'";
	// $result = db_query($sql);
	// $sections = array();
	// while ($row = db_fetch_assoc($result)){
		// $sections[$row['section']]+=1;
	// }
	// $sections = arsort($sections);
	// if (count($sections)<2){
		// $sql = "SELECT * FROM ".db_prefix("commentary")." WHERE section NOT LIKE 'dwelling%' AND section NOT LIKE 'clan%' AND section NOT LIKE 'pet-%'";
		// $result = db_query($sql);
		// $sections = array();
		// while ($row = db_fetch_assoc($result)){
			// $sections[$row['section']]+=1;
		// }
	// }
	// foreach($sections AS $section => $count){
		// addnav("Filter by Section");
		// addnav(array("%s (%s)",$section,$count),"moderate.php?viewsince=$viewsince&section=$section");
	// }
// }

// $op = httpget("op");
// if ($op=="commentdelete"){
	// $comment = httppost('comment');
	// if (httppost('delnban')>''){
		// $sql = "SELECT DISTINCT uniqueid,author FROM " . db_prefix("commentary") . " INNER JOIN " . db_prefix("accounts") . " ON acctid=author WHERE commentid IN ('" . join("','",array_keys($comment)) . "')";
		// $result = db_query($sql);
		// $untildate = date("Y-m-d H:i:s",strtotime("+3 days"));
		// $reason = httppost("reason");
		// $reason0 = httppost("reason0");
	
	// // *** DRAGONBG.COM CORE PATCH START ***
		// $default = translate_inline("Banned for comments you posted.");
	// // *** DRAGONBG.COM CORE PATCH END ***
		
		// if ($reason0 != $reason && $reason0 != $default) $reason = $reason0;
		// if ($reason=="") $reason = $default;
		// while ($row = db_fetch_assoc($result)){
			// $sql = "SELECT * FROM " . db_prefix("bans") . " WHERE uniqueid = '{$row['uniqueid']}'";
			// $result2 = db_query($sql);
			// $sql = "INSERT INTO " . db_prefix("bans") . " (uniqueid,banexpire,banreason,banner) VALUES ('{$row['uniqueid']}','$untildate','$reason','".addslashes($session['user']['name'])."')";
			// $sql2 = "UPDATE " . db_prefix("accounts") . " SET loggedin=0 WHERE acctid={$row['author']}";
			// if (db_num_rows($result2)>0){
				// $row2 = db_fetch_assoc($result2);
				// if ($row2['banexpire'] < $untildate){
					// //don't enter a new ban if a longer lasting one is
					// //already here.
					// db_query($sql);
					// db_query($sql2);
				// }
			// }else{
				// db_query($sql);
				// db_query($sql2);
			// }
		// }
	// }
	// if (!isset($comment) || !is_array($comment)) $comment = array();
	// $sql = "SELECT " .
		// db_prefix("commentary").".*,".db_prefix("accounts").".name,".
		// db_prefix("accounts").".login, ".db_prefix("accounts").".clanrank,".
		// db_prefix("clans").".clanshort FROM ".db_prefix("commentary").
		// " INNER JOIN ".db_prefix("accounts")." ON ".
		// db_prefix("accounts").".acctid = " . db_prefix("commentary").
		// ".author LEFT JOIN ".db_prefix("clans")." ON ".
		// db_prefix("clans").".clanid=".db_prefix("accounts").
		// ".clanid WHERE commentid IN ('".join("','",array_keys($comment))."')";
	// $result = db_query($sql);
	// $invalsections = array();
	// while ($row = db_fetch_assoc($result)){
		// $sql = "INSERT LOW_PRIORITY INTO ".db_prefix("moderatedcomments").
			// " (moderator,moddate,comment) VALUES ('{$session['user']['acctid']}','".date("Y-m-d H:i:s")."','".addslashes(serialize($row))."')";
		// db_query($sql);
		// $invalsections[$row['section']] = 1;
	// }
	// $sql = "DELETE FROM " . db_prefix("commentary") . " WHERE commentid IN ('" . join("','",array_keys($comment)) . "')";

	// db_query($sql);
	// $return = httpget('return');
	// $return = cmd_sanitize($return);
	// $return = substr($return,strrpos($return,"/")+1);
	// if (strpos($return,"?")===false && strpos($return,"&")!==false){
		// $x = strpos($return,"&");
		// $return = substr($return,0,$x-1)."?".substr($return,$x+1);
	// }
	// foreach($invalsections as $key=>$dummy) {
		// invalidatedatacache("comments-$key");
	// }
	// //update moderation cache
// // *** DRAGONBG.COM CORE PATCH START ***
	// invalidatedatacache("comments-");
// // *** DRAGONBG.COM CORE PATCH END ***
	// redirect($return);
// }

// $seen = httpget("seen");
// if ($seen>""){
	// $session['user']['recentcomments']=$seen;
// }

// page_header("Comment Moderation");


// if ($op=="audit"){
	// $subop = httpget("subop");
	// if ($subop=="undelete") {
		// $unkeys = httppost("mod");
		// if ($unkeys && is_array($unkeys)) {
			// $sql = "SELECT * FROM ".db_prefix("moderatedcomments")." WHERE modid IN ('".join("','",array_keys($unkeys))."')";
			// $result = db_query($sql);
			// while ($row = db_fetch_assoc($result)){
				// $comment = unserialize($row['comment']);
				// $id = addslashes($comment['commentid']);
				// $postdate = addslashes($comment['postdate']);
				// $section = addslashes($comment['section']);
				// $author = addslashes($comment['author']);
				// $comment = addslashes($comment['comment']);
				// $sql = "INSERT LOW_PRIORITY INTO ".db_prefix("commentary")." (commentid,postdate,section,author,comment) VALUES ('$id','$postdate','$section','$author','$comment')";
				// db_query($sql);
				// invalidatedatacache("comments-$section");
			// }
			// $sql = "DELETE FROM ".db_prefix("moderatedcomments")." WHERE modid IN ('".join("','",array_keys($unkeys))."')";
			// db_query($sql);
		// } else {
			// output("No items selected to undelete -- Please try again`n`n");
		// }
	// }
	// $sql = "SELECT DISTINCT acctid, name FROM ".db_prefix("accounts").
		// " INNER JOIN ".db_prefix("moderatedcomments").
		// " ON acctid=moderator ORDER BY name";
	// $result = db_query($sql);
	// addnav("Commentary");
	// addnav("Sections");
	// addnav("Modules");
	// addnav("Clan Halls");
	// addnav("Review by Moderator");
	// tlschema("notranslate");
	// while ($row = db_fetch_assoc($result)){
		// addnav(" ?".$row['name'],"moderate.php?op=audit&moderator={$row['acctid']}");
	// }
	// tlschema();
	// addnav("Commentary");
	// output("`c`bComment Auditing`b`c");
	// $ops = translate_inline("Ops");
	// $mod = translate_inline("Moderator");
	// $when = translate_inline("When");
	// $com = translate_inline("Comment");
	// $unmod = translate_inline("Unmoderate");
	// rawoutput("<form action='moderate.php?op=audit&subop=undelete' method='POST'>");
	// addnav("","moderate.php?op=audit&subop=undelete");
	// rawoutput("<table border='0' cellpadding='2' cellspacing='0'>");
	// rawoutput("<tr class='trhead'><td>$ops</td><td>$mod</td><td>$when</td><td>$com</td></tr>");
	// $limit = "75";
	// $where = "1=1 ";
	// $moderator = httpget("moderator");
	// if ($moderator>"") $where.="AND moderator=$moderator ";
	// $sql = "SELECT name, ".db_prefix("moderatedcomments").
		// ".* FROM ".db_prefix("moderatedcomments")." LEFT JOIN ".
		// db_prefix("accounts").
		// " ON acctid=moderator WHERE $where ORDER BY moddate DESC LIMIT $limit";
	// $result = db_query($sql);
	// $i=0;

// // *** DRAGONBG.COM CORE PATCH START ***
	// $clanrankcolors=array(CLAN_APPLICANT=>"`!",CLAN_MEMBER=>"`#",CLAN_OFFICER=>"`^",CLAN_LEADER=>"`&", CLAN_FOUNDER=>"`\$");
	// $args = modulehook("clanrankcolors", array("clanrankcolors"=>$clanrankcolors, "clanid"=>$session['user']['clanid']));
	// $clanrankcolors = $args['clanrankcolors'];
// // *** DRAGONBG.COM CORE PATCH END ***
	
	// while ($row = db_fetch_assoc($result)){
		// $i++;
		// rawoutput("<tr class='".($i%2?'trlight':'trdark')."'>");
		// rawoutput("<td><input type='checkbox' name='mod[{$row['modid']}]' value='1'></td>");
		// rawoutput("<td>");
		// output_notl("%s", $row['name']);
		// rawoutput("</td>");
		// rawoutput("<td>");
		// output_notl("%s", $row['moddate']);
		// rawoutput("</td>");
		// rawoutput("<td>");
		// $comment = unserialize($row['comment']);
		// output_notl("`0(%s)", $comment['section']);

// // *** DRAGONBG.COM CORE PATCH START ***
		// if ($comment['clanrank']>0)
			// output_notl("%s<%s%s>`0", $clanrankcolors[$comment['clanrank']],
					// $comment['clanshort'],
					// $clanrankcolors[$comment['clanrank']]);
// // *** DRAGONBG.COM CORE PATCH END ***
					
		// output_notl("%s", $comment['name']);
		// output_notl("-");
		// output_notl("%s", comment_sanitize($comment['comment']));
		// rawoutput("</td>");
		// rawoutput("</tr>");
	// }
	// rawoutput("</table>");
	// rawoutput("<input type='submit' class='button' value='$unmod'>");
	// rawoutput("</form>");
// }


// addnav("Sections");
// tlschema("commentary");
// $vname = getsetting("villagename", LOCATION_FIELDS);
// addnav(array("%s Square", $vname), "moderate.php?area=village");

// if ($session['user']['superuser'] & ~SU_DOESNT_GIVE_GROTTO) {
	// addnav("Grotto","moderate.php?area=superuser");
// }

// addnav("Land of the Shades","moderate.php?area=shade");
// addnav("Grassy Field","moderate.php?area=grassyfield");

// $iname = getsetting("innname", LOCATION_INN);
// // the inn name is a proper name and shouldn't be translated.
// tlschema("notranslate");
// addnav($iname,"moderate.php?area=inn");
// tlschema();

// addnav("MotD","moderate.php?area=motd");
// addnav("Veterans Club","moderate.php?area=veterans");
// addnav("Hunter's Lodge","moderate.php?area=hunterlodge");
// addnav("Gardens","moderate.php?area=gardens");
// addnav("Clan Hall Waiting Area","moderate.php?area=waiting");

// if (getsetting("betaperplayer", 1) == 1 && @file_exists("pavilion.php")) {
	// addnav("Beta Pavilion","moderate.php?area=beta");
// }
// tlschema();

// if ($session['user']['superuser'] & SU_MODERATE_CLANS){
	// addnav("Clan Halls");
	// $sql = "SELECT clanid,clanname,clanshort FROM " . db_prefix("clans") . " ORDER BY clanid";
	// $result = db_query($sql);
	// // these are proper names and shouldn't be translated.
	// tlschema("notranslate");
	// while ($row=db_fetch_assoc($result)){
		// addnav(array("<%s> %s", $row['clanshort'], $row['clanname']),
				// "moderate.php?area=clan-{$row['clanid']}");
	// }
	// tlschema();
// } elseif ($session['user']['superuser'] & SU_EDIT_COMMENTS &&
		// getsetting("officermoderate", 0)) {
	// // the CLAN_OFFICER requirement was chosen so that moderators couldn't
	// // just get accepted as a member to any random clan and then proceed to
	// // wreak havoc.
	// // although this isn't really a big deal on most servers, the choice was
	// // made so that staff won't have to have another issue to take into
	// // consideration when choosing moderators.  the issue is moot in most
	// // cases, as players that are trusted with moderator powers are also
	// // often trusted with at least the rank of officer in their respective
	// // clans.
	// if (($session['user']['clanid'] != 0) &&
			// ($session['user']['clanrank'] >= CLAN_OFFICER)) {
		// addnav("Clan Halls");
		// $sql = "SELECT clanid,clanname,clanshort FROM " . db_prefix("clans") . " WHERE clanid='" . $session['user']['clanid'] . "'";
		// $result = db_query($sql);
		// // these are proper names and shouldn't be translated.
		// tlschema("notranslate");
		// if ($row=db_fetch_assoc($result)){
			// addnav(array("<%s> %s", $row['clanshort'], $row['clanname']),
					// "moderate.php?area=clan-{$row['clanid']}");
		// } else {
			// debug ("There was an error while trying to access your clan.");
		// }
		// tlschema();
	// }
// }
// addnav("Modules");
// $mods = array();
// $mods = modulehook("moderate", $mods);
// reset($mods);

// // These are already translated in the module.
// tlschema("notranslate");
// foreach ($mods as $area=>$name) {
	// addnav($name, "moderate.php?area=$area");
// }
// tlschema();

page_footer();
?>