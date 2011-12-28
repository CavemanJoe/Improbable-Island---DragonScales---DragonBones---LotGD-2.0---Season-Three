<?php
define("ALLOW_ANONYMOUS",true);
define("OVERRIDE_FORCED_NAV",true);
	require("common.php");
	require_once("lib/http.php");
	require_once("lib/output_array.php");
	require_once("lib/commentary.php");	
	require_once("modules/lib/JSON.php");
	tlschema("commentary");
	
	$lastid = 0;
	$counttoday=0;
	$addboxreplace = 0;
	$args = modulehook("insertcomment", array("section"=>$_REQUEST['section']));
	if ($_REQUEST['mode'] == "addcomment") {
		$output = '';
		$lastid = getcommentary($_REQUEST['section'],25,$_REQUEST['talkline'],$_REQUEST['lastcommentid']);
		if (array_key_exists("mute",$args) && $args['mute'] && !($session['user']['superuser'] & SU_EDIT_COMMENTS)) {
			$addboxreplace = 1;
			output_notl("%s", $args['mutemsg']);
		} elseif ($counttoday>13 && !($session['user']['superuser']&~SU_DOESNT_GIVE_GROTTO)){
			$addboxreplace = 1;
			output("Sorry, you've exhausted your posts in this section for now.`0`n");
		} elseif (!($session['loggedin'])){
			output("Sorry, your session has timed out.`0`n");
			output("<a href=\"home.php\">Log in again</a>",true);
		} else {
		
			$section = $_REQUEST['section'];
			$talkline = $_REQUEST['talkline'];
			//echo("talkline $talkline ");
			//$talkline = mb_convert_encoding($talkline,getsetting("charset", "ISO-8859-1"),'auto');
			//echo("talkline $talkline ");
			$schema = $_REQUEST['schema'];
			$comment = trim($_REQUEST['insertcommentary']);
			$comment = mb_convert_encoding($comment,getsetting("charset", "ISO-8859-1"),'auto');
			if ($section || $talkline || $comment) {
				$tcom = color_sanitize($comment);
				if ($tcom == "" || $tcom == ":" || $tcom == "::" || $tcom == "/me")
					$emptypost = 1;
				else injectcommentary($section, $talkline, $comment, $schema);

				//echo("injectcommentary($section, $talkline, $comment, $schema)");
				//exit;

			}			
			$output = '';
			$lastid = getcommentary($_REQUEST['section'],25,$_REQUEST['talkline'],$_REQUEST['lastcommentid']);
		}
	} else {
		$output = '';
		$lastid = getcommentary($_REQUEST['section'],25,$_REQUEST['talkline'],$_REQUEST['lastcommentid']);
		if (array_key_exists("mute",$args) && $args['mute'] && !($session['user']['superuser'] & SU_EDIT_COMMENTS)) {
			$addboxreplace = 1;
			output_notl("%s", $args['mutemsg']);
		} elseif ($counttoday>13 && !($session['user']['superuser']&~SU_DOESNT_GIVE_GROTTO)){
			$addboxreplace = 1;
			output("Sorry, you've exhausted your posts in this section for now.`0`n");
		} elseif (13 - $counttoday < 3){
			output("`)(You have %s posts left today)`n`0",(13 - $counttoday));
		}
	}
	$commentaryout = array(
		"commentary" => array(
			"newcommentary"	=>	$output,
			"lastid"	=>	$lastid,
			"replaceform"	=>	$addboxreplace,
		)
	);

	$json = new Services_JSON();		

	print $json->encode($commentaryout);
	
	function getcommentary($section,$limit=25,$talkline="says",$commentid=0) {
		global $session,$REQUEST_URI,$doublepost,$translation_namespace,$counttoday;
		
		$textreturn = "";
		
		if ((int)getsetting("expirecontent",180)>0 && e_rand(1,1000)==1){
			$sql = "DELETE FROM " . db_prefix("commentary") . " WHERE postdate<'".date("Y-m-d H:i:s",strtotime("-".getsetting("expirecontent",180)." days"))."'";
			db_query($sql);
		}
		
		$sql = "SELECT COUNT(commentid) AS newadded FROM " .
			db_prefix("commentary") . " LEFT JOIN " .
			db_prefix("accounts") . " ON " .
			db_prefix("accounts") . ".acctid = " .
			db_prefix("commentary").".author WHERE section='$section' AND " .
			db_prefix("accounts").".locked=0 AND commentid > '$commentid'";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		if ($row['newadded'] < 1) {
			return $commentid;
		}
		$newadded = $row['newadded'];
		
		$commentbuffer = array();
		
//		if ($commentid == 0) { 
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
				".clanid WHERE " . ($section ? "section='$section' AND " : '') .
				"( ".db_prefix("accounts") . ".locked=0 OR ".db_prefix("accounts") .".locked is null ) ".
				"AND commentid > '$cid' " .
				"ORDER BY commentid DESC LIMIT $limit";
				$result = db_query($sql);
				while ($row = db_fetch_assoc($result)) $commentbuffer[] = $row;
/*		} else {
			$sql = "SELECT " . db_prefix("commentary") . ".*, " .
				db_prefix("accounts").".name, " . 
				db_prefix("accounts").".login, " . 
				db_prefix("accounts").".clanrank, " .
				db_prefix("clans").".clanshort FROM " .
				db_prefix("commentary") . " INNER JOIN " .
				db_prefix("accounts") . " ON " .
				db_prefix("accounts").".acctid = " .
				db_prefix("commentary"). ".author LEFT JOIN " .
				db_prefix("clans") . " ON " .
				db_prefix("clans") . ".clanid=" .
				db_prefix("accounts") .".clanid WHERE section = '$section' AND " .
				db_prefix("accounts") . ".locked=0 AND commentid > '$commentid' ORDER BY commentid ASC LIMIT $limit";
				$result = db_query($sql);
				while ($row = db_fetch_assoc($result)) $commentbuffer[] = $row;
				$commentbuffer = array_reverse($commentbuffer);
		}
*/
		
		$rowcount = count($commentbuffer);
		if ($rowcount > 0) {
			$session['lastcommentid'] = $commentbuffer[0]['commentid'];
			$lastcommentid = $commentbuffer[0]['commentid'];
		}
		
		$counttoday=0;
		for ($i=0;$i < $rowcount;$i++) {
			$row = $commentbuffer[$i];
			$row['comment'] = comment_sanitize($row['comment']);
			$commentids[$i] = $row['commentid'];
			if (date("Y-m-d",strtotime($row['postdate']))==date("Y-m-d")){
				if ($row['name']==$session['user']['name']) $counttoday++;
			}
			$x = 0;
			$ft="";
			for ($x=0;strlen($ft)<5 && $x<strlen($row['comment']);$x++){
				if (substr($row['comment'],$x,1)=="`" && strlen($ft)==0) {
					$x++;
				}else{
					$ft.=substr($row['comment'],$x,1);
				}
			}
			
			$location = get_module_pref("location","ajaxcommentary");
			if($location) {
				$link = "bio.php?char=" . $row['author'] .
				"&ret=".$location;
				addnav("", $link);
			} else {
				$link = '';
			}
			
			
			if (substr($ft,0,2)=="::")
				$ft = substr($ft,0,2);
			elseif (substr($ft,0,1)==":")
				$ft = substr($ft,0,1);
			elseif (substr($ft,0,3)=="/me")
				$ft = substr($ft,0,3);
			
			$row['comment'] = holidayize($row['comment'],'comment');
			$row['name'] = holidayize($row['name'],'comment');
			
			$clanrankcolors = array(CLAN_APPLICANT=>"`!",CLAN_MEMBER=>"`#",CLAN_OFFICER=>"`^",CLAN_LEADER=>"`&",CLAN_FOUNDER=>"`\$");
			
			if ($row['clanrank']) {
				$row['name'] = ($row['clanshort']?"{$clanrankcolors[$row['clanrank']]}&lt;`2{$row['clanshort']}{$clanrankcolors[$row['clanrank']]}&gt; `&":"").$row['name'];
			}
			if ($ft=="::"||$ft=="/me" || $ft==":") {
				$x = strpos($row['comment'],$ft);
				if ($x!==false) {
					$op[$i] = str_replace("&amp;","&",HTMLEntities(substr($row['comment'],0,$x), ENT_COMPAT, getsetting("charset", "ISO-8859-1")))."`0" . ($link ? "<a href='$link' style='text-decoration: none'>\n" : "") . "`&{$row['name']}`0".($link ? "</a>" : "")."\n`& ".str_replace("&amp;","&",HTMLEntities(substr($row['comment'],$x+strlen($ft)), ENT_COMPAT, getsetting("charset", "ISO-8859-1")))."`0`n";
					$rawc[$i] = str_replace("&amp;","&",HTMLEntities(substr($row['comment'],0,$x), ENT_COMPAT, getsetting("charset", "ISO-8859-1")))."`0`&{$row['name']}`0`& ".str_replace("&amp;","&",HTMLEntities(substr($row['comment'],$x+strlen($ft)), ENT_COMPAT, getsetting("charset", "ISO-8859-1")))."`0`n";
				}
			}
			if ($ft=="/game" && !$row['name']) {
				$x = strpos($row['comment'],$ft);
				//if ($x!==false){
				 $op[$i] = str_replace("&amp;","&",htmlentities(substr($row['comment'],0,$x), ENT_COMPAT, getsetting("charset", "ISO-8859-1")))."`0`&".str_replace("&amp;","&",htmlentities(substr($row['comment'],$x+strlen($ft)), ENT_COMPAT, getsetting("charset", "ISO-8859-1")))."`0`n";
				//}
			}
			if (!isset($op) || !is_array($op)) $op = array();
			if (!array_key_exists($i,$op) || $op[$i] == "") {
				$op[$i] = "`0" . ($link ? "<a href='$link' style='text-decoration: none'>\n" : "") . "`&{$row['name']}`0".($link ? "</a>" : "")."`3 says, \"`#".str_replace("&amp;","&",HTMLEntities($row['comment'], ENT_COMPAT, getsetting("charset", "ISO-8859-1")))."`3\"`0`n";
				$rawc[$i] = "`&{$row['name']}`3 says, \"`#".str_replace("&amp;","&",HTMLEntities($row['comment'], ENT_COMPAT, getsetting("charset", "ISO-8859-1")))."`3\"`0`n";
			}
			if (!array_key_exists('timestamp', $session['user']['prefs']))
				$session['user']['prefs']['timestamp'] = 0;
			
			if ($session['user']['prefs']['timestamp'] == 1) {
				if (!isset($session['user']['prefs']['timeformat'])) $session['user']['prefs']['timeformat'] = "[m/d h:ia]";
				$time = strtotime($row['postdate']) + ($session['user']['prefs']['timeoffset'] * 60 * 60);
				$s="`7" . date($session['user']['prefs']['timeformat'],$time) . "`0 ";
				$op[$i] = $s.$op[$i];
			} elseif ($session['user']['prefs']['timestamp'] == 2) {
				$s=reltime(strtotime($row['postdate']));
				$op[$i] = "`7($s)`0 ".$op[$i];
			}
			if ($row['postdate']>=$session['user']['recentcomments'])
				$op[$i] = "<img src='images/new.gif' alt='&gt;' width='3' height='5' align='absmiddle'> ".$op[$i];
			$auth[$i] = $row['author'];
			$rawc[$i] = full_sanitize($rawc[$i]);
			$rawc[$i] = htmlentities($rawc[$i], ENT_QUOTES, getsetting("charset", "ISO-8859-1"));
		}
		$outputcomments=array();
		$sect="x";
		
		for(;$i>-1;$i--){
			$out="";
			$out.=$op[$i];
			if (!array_key_exists($sect,$outputcomments) || !is_array($outputcomments[$sect]))
				$outputcomments[$sect] = array();
			array_push($outputcomments[$sect],$out);
		}
		ksort($outputcomments);
		reset($outputcomments);
		$sections = commentarylocs();
		
		while (list($sec,$v)=each($outputcomments)){
			reset($v);
			while(list($key,$val)=each($v)){
				$args = array('commentline'=>$val);
				$args = modulehook("viewcommentary",$args);
				$val = $args['commentline'];
				output_notl($val,true);
			}
		}
		return $lastcommentid;
	}
	
saveuser();
?>
