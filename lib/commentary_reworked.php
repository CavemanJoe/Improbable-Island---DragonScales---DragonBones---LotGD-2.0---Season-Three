<?php
// translator ready
// addnews ready
// mail ready
require_once("lib/datetime.php");
require_once("lib/sanitize.php");
require_once("lib/http.php");

$comsecs = array();
function commentarylocs() {
	global $comsecs, $session;
	if (is_array($comsecs) && count($comsecs)) return $comsecs;

	$vname = getsetting("villagename", LOCATION_FIELDS);
	$iname = getsetting("innname", LOCATION_INN);
	tlschema("commentary");
	$comsecs['village'] = sprintf_translate("%s Square", $vname);
	if ($session['user']['superuser'] & ~SU_DOESNT_GIVE_GROTTO) {
		$comsecs['superuser']=translate_inline("Grotto");
	}
	$comsecs['shade']=translate_inline("Land of the Shades");
	$comsecs['grassyfield']=translate_inline("Grassy Field");
	$comsecs['inn']="$iname";
	$comsecs['motd']=translate_inline("MotD");
	$comsecs['veterans']=translate_inline("Veterans Club");
	$comsecs['hunterlodge']=translate_inline("Hunter's Lodge");
	$comsecs['gardens']=translate_inline("Gardens");
	$comsecs['waiting']=translate_inline("Clan Hall Waiting Area");
	if (getsetting("betaperplayer", 1) == 1 && @file_exists("pavilion.php")) {
		$comsecs['beta']=translate_inline("Pavilion");
	}
	tlschema();
	// All of the ones after this will be translated in the modules.
	$comsecs = modulehook("moderate", $comsecs);
	rawoutput(tlbutton_clear());
	return $comsecs;
}

function addcommentary() {
	global $session, $emptypost;
	$section = httppost('section');
	$talkline = httppost('talkline');
	$schema = httppost('schema');
	$comment = trim(httppost('insertcommentary'));
	$counter = httppost('counter');
	$remove = URLDecode(httpget('removecomment'));
	if ($remove>0) {
		$return = '/' . httpget('returnpath');
		$section = httpget('section');
        $sql = "SELECT " .
                db_prefix("commentary").".*,".db_prefix("accounts").".name,".
                db_prefix("accounts").".acctid, ".db_prefix("accounts").".clanrank,".
                db_prefix("clans").".clanshort FROM ".db_prefix("commentary").
                " INNER JOIN ".db_prefix("accounts")." ON ".
                db_prefix("accounts").".acctid = " . db_prefix("commentary").
                ".author LEFT JOIN ".db_prefix("clans")." ON ".
                db_prefix("clans").".clanid=".db_prefix("accounts").
                ".clanid WHERE commentid=$remove";
		$row = db_fetch_assoc(db_query($sql));
		$sql = "INSERT LOW_PRIORITY INTO ".db_prefix("moderatedcomments").
			" (moderator,moddate,comment) VALUES ('{$session['user']['acctid']}','".date("Y-m-d H:i:s")."','".addslashes(serialize($row))."')";
		db_query($sql);
		$sql = "DELETE FROM ".db_prefix("commentary")." WHERE commentid='$remove';";
		db_query($sql);
		invalidatedatacache("comments-$section");
// *** DRAGONBG.COM CORE PATCH START ***
		invalidatedatacache("comments-");
// *** DRAGONBG.COM CORE PATCH END ***		
		$session['user']['specialinc']==''; //just to make sure he was not in a special
		$return = cmd_sanitize($return);
		$return = substr($return,strrpos($return,"/")+1);
		if (strpos($return,"?")===false && strpos($return,"&")!==false){
			$x = strpos($return,"&");
			$return = substr($return,0,$x-1)."?".substr($return,$x+1);
		}
		debug($return);
		redirect($return);
	}
	if (array_key_exists('commentcounter',$session) && $session['commentcounter']==$counter) {
		if ($section || $talkline || $comment) {
			$tcom = color_sanitize($comment);
			if ($tcom == "" || $tcom == ":" || $tcom == "::" || $tcom == "/me"){
				$emptypost = 1;
			} else {
				injectcommentary($section, $talkline, $comment);
			}
		}
	}
}

function injectsystemcomment($section,$comment) {
	//function lets gamemasters put in comments without a user association...be careful, it is not trackable who posted it
	if (strncmp($comment, "/game", 5) !== 0) {
		$comment = "/game" . $comment;
	}
	injectrawcomment($section,0,$comment);
}

function injectrawcomment($section, $author, $comment, $name=false, $info=false){
	if ($info){
		$info=@serialize($info);
	}
	$sql = "INSERT INTO " . db_prefix("commentary") . " (postdate,section,author,comment,name,info) VALUES ('".date("Y-m-d H:i:s")."','$section',$author,\"$comment,$name,$info\")";
	db_query($sql);
	invalidatedatacache("comments-{$section}");
	// invalidate moderation screen also.
// *** DRAGONBG.COM CORE PATCH START ***	
	invalidatedatacache("comments-");
// *** DRAGONBG.COM CORE PATCH END ***
}

function injectcommentary($section, $talkline, $comment) {
	global $session,$doublepost;
	// Make the comment pristine so that we match on it correctly.
	$comment = stripslashes($comment);
	$doublepost=0;
	$emptypost = 0;
	$colorcount = 0;
	if ($comment !="") {
		$commentary = str_replace("`n","",soap($comment));
		//removed for performance
		// $y = strlen($commentary);
		// for ($x=0;$x<$y;$x++){
			// if (substr($commentary,$x,1)=="`"){
				// $colorcount++;
				// if ($colorcount>=getsetting("maxcolors",10)){
					// $commentary = substr($commentary,0,$x).color_sanitize(substr($commentary,$x));
					// $x=$y;
				// }
				// $x++;
			// }
		// }

		$info = array();
		$clanid = $session['user']['clanid'];
		if ($clanid){
			$clansql = "SELECT clanname,clanshort FROM ".db_prefix("clans")." WHERE clanid='$clanid'";
			$clanresult = db_query($clansql);
			$clanrow = db_fetch_assoc($clanresult);
			$info['clanname'] = $clanrow['clanname'];
			$info['clanshort'] = $clanrow['clanshort'];
			$info['clanid'] = $clanid;
		}
		
		$args = array('commentline'=>$commentary, 'commenttalk'=>$talkline, 'info'=>$info, 'name'=>$session['user']['name']);
		$args = modulehook("commentary", $args);

		if ($args['ignore']==1){
			//Ignore this comment, it is likely a side-effect of using the Special switch
			return;
		}

		$commentary = $args['commentline'];
		$talkline = $args['commenttalk'];
		$info = $args['info'];
		$name = $args['name'];
		$talkline = translate_inline($talkline);

		//Clean up the comment a bit
		$commentary = preg_replace("'([^[:space:]]{45,45})([^[:space:]])'","\\1 \\2",$commentary);
		$commentary = addslashes($commentary);

		// do an emote if the area has a custom talkline and the user
		// isn't trying to emote already.
		if ($talkline!="says" && substr($commentary,0,1)!=":" &&
				substr($commentary,0,2)!="::" &&
				substr($commentary,0,3)!="/me" &&
				substr($commentary,0,5) != "/game") {
			$commentary = ":`3$talkline, \\\"`#$commentary`3\\\"";
		}
		
		// Sort out /game switches
		if (substr($commentary,0,5)=="/game" && ($session['user']['superuser']&SU_IS_GAMEMASTER)==SU_IS_GAMEMASTER) {
			//handle game master inserts now, allow double posts
			injectsystemcomment($section,$commentary);
		} else {
			//This query checks for double posts
			$sql = "SELECT comment,author FROM " . db_prefix("commentary") . " WHERE section='$section' ORDER BY commentid DESC LIMIT 1";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			db_free_result($result);
			if ($row['comment']!=stripslashes($commentary) || $row['author']!=$session['user']['acctid']){
				//Not a double post, inject the comment
				injectrawcomment($section, $session['user']['acctid'], $commentary, $session['user']['name'], $info);
				$session['user']['laston']=date("Y-m-d H:i:s");
			} else {
				$doublepost = 1;
			}
		}
	}
}

function commentdisplay($intro, $section, $message="Interject your own commentary?",$limit=10,$talkline="says",$schema=false) {
	// Let's add a hook for modules to block commentary sections
	$args = modulehook("blockcommentarea", array("section"=>$section));
	if (isset($args['block']) && ($args['block'] == "yes"))
		return;

	if ($intro) output($intro);
	viewcommentary($section, $message, $limit, $talkline, $schema);
}

function viewcommentary($section,$message="Interject your own commentary?",$limit=10,$talkline="says",$schema=false) {
 	global $session,$REQUEST_URI,$doublepost, $translation_namespace;
	global $emptypost;
	global $chatloc;
	$chatloc = $section;

	if($section) {
		rawoutput("<a name='$section'></a>");
		// Let's add a hook for modules to block commentary sections
		$args = modulehook("blockcommentarea", array("section"=>$section));
		if (isset($args['block']) && ($args['block'] == "yes"))
			return;
	}

	//stops people from clicking on Bio links in the MoTD
	$nobios = array("motd.php"=>true);
	if (!array_key_exists(basename($_SERVER['SCRIPT_NAME']),$nobios)) $nobios[basename($_SERVER['SCRIPT_NAME'])] = false;
	if ($nobios[basename($_SERVER['SCRIPT_NAME'])]){
		$linkbios=false;
	} else {
		$linkbios=true;
	}

	if ($doublepost) output("`\$`bDouble post?`b`0`n");
	if ($emptypost) output("`\$`bWell, they say silence is a virtue.`b`0`n");	

	// Needs to be here because scrolling through the commentary pages, entering a bio, then scrolling again forward
	// then re-entering another bio will lead to $com being smaller than 0 and this will lead to an SQL error later on.
	$com=(int)httpget("comscroll");
	if ($com < 0) $com = 0;
	$cc = false;
	if (httpget("comscroll") !== false && (int)$session['lastcom'] == $com+1){
		$cid = (int)$session['lastcommentid'];
	} else {
		$cid = 0;
	}

	$session['lastcom'] = $com;

	//getting clans takes up far too much in the way of resources.  What we really need is a brand new commentary table with an info field, into which the clan ranks and icons and such can go.
	//Functionality that keeps information updated such as the player's name, their clan, whether or not they're banned etc. can go away.
	if (!$cid) $cid=1;
	$sql = "SELECT * FROM ".db_prefix("commentary")." WHERE commentid > '$cid' AND section='$section' ORDER BY commentid DESC LIMIT ".($com*$limit).",$limit";
	$result = db_query($sql);
	$commentbuffer = array();
	while ($row = db_fetch_assoc($result)){
		$row['info']=@unserialize($row['info']);
		if (!is_array($row['info'])){
			$row['info']=array();
		}
		$commentbuffer[]=$row;
	}
	
	debug($commentbuffer);
	$commentbuffer = modulehook("commentbuffer",$commentbuffer);
	debug($commentbuffer);
	
	$rowcount = count($commentbuffer);
	if ($rowcount > 0){
		$session['lastcommentid'] = $commentbuffer[0]['commentid'];
	}

	//obtain return link
	$scriptname=substr($_SERVER['SCRIPT_NAME'],strrpos($_SERVER['SCRIPT_NAME'],"/")+1);
	$pos=strpos($_SERVER['REQUEST_URI'],"?");
	$return=$scriptname.($pos==false?"":substr($_SERVER['REQUEST_URI'],$pos));
	$one=(strstr($return,"?")==false?"?":"&");
	
	//figure out whether to handle absolute or relative time
	if (!array_key_exists('timestamp', $session['user']['prefs'])){
		$session['user']['prefs']['timestamp'] = 0;
	}
	$session['user']['prefs']['timeoffset'] = round($session['user']['prefs']['timeoffset'],1);
	
	//this array of userids means that with a single query we can figure out who's online and nearby
	$acctidstoquery=array();
	
	//prepare the actual comment line part of the comment
	for ($i=0; $i < $rowcount; $i++){
		$thiscomment="";
		$row = $commentbuffer[$i];
		$row['acctid']=$row['author'];
		$acctidstoquery[]=$row['author'];
		$row['comment'] = comment_sanitize($row['comment']);
		if (substr($row['comment'],0,1)==":" || substr($row['comment'],0,3)=="/me") {
			$row['skiptalkline']=true;
		}
		if (substr($row['comment'],0,5)=="/game" && !$row['name'] || $row['info']['gamecomment']){
			$row['gamecomment']=true;
			$row['skiptalkline']=true;
			$row['info']['icons']=array();
		}
		if ($linkbios){
			$row['biolink']=true;
		}
		if ($session['user']['superuser'] & SU_EDIT_COMMENTS){
			$row['modlink']=true;
		}
		if ($row['modlink']){
			$thiscomment.="`2[<a href='".$return.$one."removecomment=".$row['commentid']."&returnpath=".URLEncode($return)."'>$del</a>`2] ";
			addnav("",$return.$one."removecomment=".$row['commentid']."&returnpath=".URLEncode($return)."");
		}
		if ($row['biolink'] && !$row['gamecomment']){
			$bio = "bio.php?char=".$row['acctid']."&ret=".URLEncode($_SERVER['REQUEST_URI']);
			$thiscomment.="<a href=\"$bio\">`0".$row['name']."`0</a> ";
			addnav("",$bio);
		}
		if (!$row['skiptalkline']){
			$thiscomment.=$talkline." \"`#";
		}
		$thiscomment.=str_replace("&amp;","&",htmlentities($row['comment'], ENT_COMPAT, getsetting("charset", "ISO-8859-1")))."`0\"";
		$commentbuffer[$i]['comment']=$thiscomment;
		$commentbuffer[$i]['icons']=$row['info']['icons'];
		$commentbuffer[$i]['time']=strtotime($row['postdate']);
		if ($session['user']['prefs']['timestamp']==1) {
			if (!isset($session['user']['prefs']['timeformat'])) $session['user']['prefs']['timeformat'] = "[m/d h:ia]";
			$time = strtotime($row['postdate']) + ($session['user']['prefs']['timeoffset'] * 60 * 60);
			$s=date("`7" . $session['user']['prefs']['timeformat'] . "`0 ",$time);
			$commentbuffer[$i]['displaytime'] = $s;
		} elseif ($session['user']['prefs']['timestamp']==2) {
			$s=reltime(strtotime($row['postdate']));
			$commentbuffer[$i]['displaytime'] = "`7($s)`0 ";
		}
	}
	
	//get offline/online/nearby status
	$acctids = join(',',$acctidstoquery);
	$onlinesql = "SELECT acctid, laston, loggedin, chatloc FROM ".db_prefix("accounts")." WHERE acctid IN ($acctids)";
	$onlineresult = db_query($onlinesql);
	$onlinestatus = array();
	$offline = date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds"));
	while ($row = db_fetch_assoc($onlineresult)){
		$onlinestatus[$row['acctid']]=$row;
	}
	for ($i=0; $i < $rowcount; $i++){
		$row = $commentbuffer[$i];
		if ($onlinestatus[$row['author']]['laston'] < $offline || !$onlinestatus[$row['author']]['loggedin']){
			$commentbuffer[$row]['online']=0;
			$commentbuffer[$row]['icons'][]="images/offline.png";
		} else if ($onlinestatus[$row['author']]['chatloc']==$chatloc){
			$commentbuffer[$row]['online']=2;
			$commentbuffer[$row]['icons'][]="images/nearby.png";
		} else {
			$commentbuffer[$row]['online']=1;
			$commentbuffer[$row]['icons'][]="images/online.png";
		}
	}
	
	$finaloutput="";
	//output the comments!
	for ($i=0; $i < $rowcount; $i++){
		$row = $commentbuffer[$i];
		$icons = $row['icons'];
		foreach($icons AS $icon){
			$finaloutput.=$icon;
		}
		$finaloutput.=$row['displaytime'];
		$finaloutput.=$row['comment'];
		output_notl("$finaloutput`n");
	}
	
	
	
	
	
	
	

	
	
	// debug($commentbuffer);

	//output the comments
	// ksort($outputcomments);
	// reset($outputcomments);
	// $sections = commentarylocs();
	// $needclose = 0;
	
	// while (list($sec,$v)=each($outputcomments)){
		// if ($sec!="x") {
			// output_notl("`n<hr><a href='moderate.php?area=%s'>`b`^%s`0`b</a>`n",$sec, isset($sections[$sec]) ? $sections[$sec] : "($sec)", true);
			// addnav("", "moderate.php?area=$sec");
		// }
		// reset($v);
		// while (list($key,$val)=each($v)){
			// $args = array('commentline'=>$val,'area'=>$section);
			// $args = modulehook("viewcommentary", $args);
			// $val = $args['commentinfo'].$args['commentline'];
			// output_notl($val, true);
		// }
	// }
	
	//Output page jumpers
	// $sql = "SELECT count(commentid) AS c FROM " . db_prefix("commentary") . " WHERE section='$section'";
	// $r = db_query($sql);
	// $val = db_fetch_assoc($r);
	// $val = round($val['c'] / $limit + 0.5,0) - 1;
	rawoutput("<table cellpadding=0 cellspacing=5 width=100%><tr><td valign=\"top\" width=50%>");

	if ($session['user']['loggedin']) {
		if ($message!="X"){
			$message="`n`@$message`n";
			output($message);
			talkform($section,$talkline,$limit,$schema);
		}
	}

	$jump = false;
	if (!isset($session['user']['prefs']['nojump']) || $session['user']['prefs']['nojump'] == false) {
		$jump = true;
	}
	
	//new-style commentary display with page numbers
	// if (!$cc) db_free_result($result);
	// tlschema();
	// if ($needclose) modulehook("}collapse");
	rawoutput("</td><td valign=\"top\" width=50%>");
	$nlink = comscroll_sanitize($REQUEST_URI);
	$nlink = str_replace("?&","?",$nlink);
	if (!strpos($nlink,"?")) $nlink = str_replace("&","?",$nlink);
	$nlink .= "&refresh=1";
	
	//reinstating back and forward links
	output_notl("`n");
	$prev = translate_inline("&lt;&lt;");
	$next = translate_inline("&gt;&gt;");

	if ($rowcount>=$limit || $cid>0){
		$req = comscroll_sanitize($REQUEST_URI)."&comscroll=".($com+1);
		$req = str_replace("?&","?",$req);
		if (!strpos($req,"?")) $req = str_replace("&","?",$req);
		$req .= "&refresh=1";
		if ($jump) {
			$req .= "#$section";
		}
		output_notl("<a href=\"$req\">$prev</a> ",true);
		addnav("",$req);
	}
	output_notl("<a href=\"$nlink\">Refresh Commentary</a>",true);
	if ($com>0 || ($cid > 0 && $newadded > $limit)){
		$req = comscroll_sanitize($REQUEST_URI)."&comscroll=".($com-1);
		$req = str_replace("?&","?",$req);
		if (!strpos($req,"?")) $req = str_replace("&","?",$req);
		$req .= "&refresh=1";
		if ($jump) {
			$req .= "#$section";
		}
		output_notl(" <a href=\"$req\">$next</a>",true);
		addnav("",$req);
	}
	//
	addnav("",$nlink);
	output("`n`n`0Jump to commentary page:");
	for ($i=$val; $i>=0; $i--){
		$nlink = comscroll_sanitize($REQUEST_URI)."&comscroll=".($i);
		$nlink = str_replace("?&","?",$nlink);
		if (!strpos($nlink,"?")) $nlink = str_replace("&","?",$nlink);
		$nlink .= "&refresh=1";
		if ($jump) {
			$nlink .= "#$section";
		}
		$ndisp = 1+$val-$i;
		if (httpget('comscroll')!=$i){
			output_notl("<a href=\"$nlink\">$ndisp</a> ",true);
			addnav("",$nlink);
		} else {
			output_notl("`@$ndisp`0 ",true);
		}
	}
	modulehook("commentaryoptions");
	rawoutput("</td></tr></table");
	
	// *** AJAX CHAT MOD START ***
	//modulehook("viewcommentaryfooter");
	// *** AJAX CHAT MOD END ***
}

function talkform($section,$talkline,$limit=10,$schema=false){
	require_once("lib/forms.php");
	global $REQUEST_URI,$session,$translation_namespace;
	if ($schema===false) $schema=$translation_namespace;
	tlschema("commentary");

	$jump = false;
	if (isset($session['user']['prefs']['nojump']) && $session['user']['prefs']['nojump'] == true) {
		$jump = true;
	}

	$counttoday=0;
	if (substr($section,0,5)!="clan-"){
		$sql = "SELECT author FROM " . db_prefix("commentary") . " WHERE section='$section' AND postdate>'".date("Y-m-d 00:00:00")."' ORDER BY commentid DESC LIMIT $limit";
		$result = db_query($sql);
		while ($row=db_fetch_assoc($result)){
			if ($row['author']==$session['user']['acctid']) $counttoday++;
		}
		if (round($limit/2,0)-$counttoday <= 0 && getsetting('postinglimit',1)){
			if ($session['user']['superuser']&~SU_DOESNT_GIVE_GROTTO){
				output("`n`)(You'd be out of posts if you weren't a superuser or moderator.)`n");
			}else{
				output("`n`)(You are out of posts for the time being.  Once some of your existing posts have moved out of the comment area, you'll be allowed to post again.)`n");
				return false;
			}
		}
	}
	if (translate_inline($talkline,$schema)!="says")
		$tll = strlen(translate_inline($talkline,$schema))+11;
		else $tll=0;
	$req = comscroll_sanitize($REQUEST_URI)."&comment=1";
	$req = str_replace("?&","?",$req);
	if (!strpos($req,"?")) $req = str_replace("&","?",$req);
	if ($jump) {
		$req .= "#$section";
	}
	addnav("",$req);

	// *** AJAX CHAT MOD START ***
	//output_notl("<form action=\"$req\" method='POST' autocomplete='false'>",true);
	$args1 = array(
			"formline"	=>	"<form action=\"$req\" id='commentaryform' method='post' autocomplete='false'",
			"section"	=>	$section,
			"talkline"	=>	$talkline,
			"schema"	=>	$schema
		);
		$args1 = modulehook("commentarytalkline",$args1);
		rawoutput('<div id="commentaryformcontainer">');
		output_notl($args1['formline'] . ">",true);
	// *** AJAX CHAT MOD END ***
	
	previewfield("insertcommentary", $session['user']['name'], $talkline, true, array("size"=>"40", "maxlength"=>200-$tll));
	rawoutput("<input type='hidden' name='talkline' value='$talkline'>");
	rawoutput("<input type='hidden' name='schema' value='$schema'>");
	rawoutput("<input type='hidden' name='counter' value='{$session['counter']}'>");
	$session['commentcounter'] = $session['counter'];
	if ($section=="X"){
		$vname = getsetting("villagename", LOCATION_FIELDS);
		$iname = getsetting("innname", LOCATION_INN);
		$sections = commentarylocs();
		reset ($sections);
		output_notl("<select name='section'>",true);
		while (list($key,$val)=each($sections)){
			output_notl("<option value='$key'>$val</option>",true);
		}
		output_notl("</select>",true);
	}else{
		output_notl("<input type='hidden' name='section' value='$section'>",true);
	}
	$add = htmlentities(translate_inline("Add"), ENT_QUOTES, getsetting("charset", "ISO-8859-1"));

	// *** DRAGONBG.COM CORE PATCH START***
	output_notl("<input type='submit' class='button' value='$add'>	",true);
		modulehook("commentarytrail",array());	
	// *** DRAGONBG.COM CORE PATCH END***

	// *** AJAX CHAT MOD START ***
	//if (round($limit/2,0)-$counttoday < 3 && getsetting('postinglimit',1)){
	//	output("`)(You have %s posts left today)`n`0",(round($limit/2,0)-$counttoday));
	//}
	rawoutput("<div id='previewtext'></div></form>");
	rawoutput('</div>');
	// *** AJAX CHAT MOD END ***
	
	tlschema();
}
?>