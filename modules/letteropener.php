<?php
function letteropener_getmoduleinfo(){
	$info = array(
		"name"=>"Letter opener",
		"version"=>"20070112",
		"author"=>"<a href='http://www.sixf00t4.com' target=_new>`^Sixf00t4</a>",
		"category"=>"Administrative",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1084",
		"description"=>"Admin can read and browse YOMs.",
		"vertxtloc"=>"http://www.legendofsix.com/",
		"settings"=>array(
			"Letter opener Settings,title",
			"num"=>"How many latest YOMs to show at a time?,int|30",
			"outbox"=>"Select message from Nightborn's outbox db?,bool|0",
			"msg"=>"What should be displayed at the top of the YOM pages?,textarea|`^NOTE: Staff may on occassion browse or monitor YOMs.",
			),
		"prefs"=>array(
			"Letter opener Preferences,title",
			"letteraccess"=>"Permission to use Letter opener?,bool|0",
		),
	);
	return $info;
}

function letteropener_install(){
	if (!is_module_active('letteropener')){
		output("`2Installing Letter opener Module.`n");
		output("`b`4Be sure to set access for Admin and Moderators from User Settings!`b`n");
	}
	set_module_pref("letteraccess",1,"letteropener");
	module_addhook("footer-popup");
	module_addhook("superuser");
	return true;
}

function letteropener_uninstall(){
	output("`2Un-Installing Letter opener Module.`n");
	return true;
}

function letteropener_dohook($hookname,$args){
	global $session,$SCRIPT_NAME,$battle;
	switch($hookname){
		case "footer-popup":
			if (stristr($SCRIPT_NAME, "mail")==true) {
				$msg=get_module_setting("msg");
				output_notl("`n`n`c`b$msg`b`c`n");
			}
		break;
		
		case "superuser":
			if (get_module_pref('letteraccess')) addnav("Letter opener","runmodule.php?module=letteropener");
		break;
	}
return $args;  
    }

function letteropener_run(){
	global $session;
    page_header("Letter opener");

    require_once("common.php");
    require_once("lib/systemmail.php");
    require_once("lib/sanitize.php");
    require_once("lib/http.php");
	$maildb="mail";
	if(get_module_setting("outbox")) $maildb="mailoutbox";
    
    $op = httpget('op');
    $order = "acctid";
    if ($sort!="") $order = "$sort";
    $display = 0;
    $query = httppost('q');
    if ($query === false) $query = httpget('q');
    
    
    addnav("Back to the grotto","superuser.php");
    addnav(array("Show last %s YOMs",get_module_setting("num")),"runmodule.php?module=letteropener&op=lastfew");
    if ($op=="read"){
        $id = httpget('id');
        
        
        
        $sql = "SELECT msgfrom,msgto from " . db_prefix($maildb) . " where messageid=\"".$id."\"";
        $result = db_query($sql);
        $row = db_fetch_assoc($result);
        $acctid = $row['msgto'];
        
        $sqlz = "SELECT login from " . db_prefix("accounts") . " where acctid=\"".$acctid."\"";
        $result = db_query($sqlz);
        $rowz = db_fetch_assoc($result);
        $login = $rowz['login'];
        
        addnav("Read Someone else's mail","runmodule.php?module=letteropener");
		addnav("~");
		addnav(array("All YOMs to %s",$login),"runmodule.php?module=letteropener&op=to&to=$login");
        addnav(array("All YOMs from %s",$login),"runmodule.php?module=letteropener&op=from&from=$login");
        $sql = "SELECT " . db_prefix($maildb) . ".*,". db_prefix("accounts"). ".name,login FROM " . db_prefix($maildb) ." LEFT JOIN " . db_prefix("accounts") . " ON ". db_prefix("accounts") . ".acctid=" . db_prefix($maildb). ".msgfrom WHERE msgto=\"".$acctid."\" AND messageid=\"".$id."\"";
        $result = db_query($sql);
        if (db_num_rows($result)>0){
            $row = db_fetch_assoc($result);
            tlschema("mail");
            if ((int)$row['msgfrom']==0){
                $row['name']=translate_inline("`i`^System`0`i");
                if (is_array(unserialize($row['subject']))) {
                    $row['subject'] = unserialize($row['subject']);
                    $row['subject'] =
                        call_user_func_array("sprintf_translate", $row['subject']);
                }
                if (is_array(unserialize($row['body']))) {
                    $row['body'] = unserialize($row['body']);
                    $row['body'] =
                        call_user_func_array("sprintf_translate", $row['body']);
                }
            }
            tlschema();
            if (!$row['seen']) output("`b`#NEW`b`n");
            else output("`n");

            if ((int)$row['msgfrom']!=0){
				addnav("Or");
				//$othername=$row['msgfrom'];
				//$sql="select login from ".db_prefix("accounts")." where acctid=$othername";
				//$result = db_query($sql);
				$othername=$row['login'];
				addnav(array("All YOMs to %s",$othername),"runmodule.php?module=letteropener&op=to&to=$othername");
				addnav(array("All YOMs from %s",$othername),"runmodule.php?module=letteropener&op=from&from=$othername");
			}
            output("`b`2From:`b `^%s`n",$row['name']);
            output("`b`2Subject:`b `^%s`n",$row['subject']);
            output("`b`2Sent:`b `^%s`n",$row['sent']);
            output_notl("<hr>`n",true);
            output_notl(str_replace("\n","`n",$row['body']));
            output_notl("`n<hr>`n",true);
            rawoutput("<table width='50%' border='0' cellpadding='0' cellspacing='5'><tr>");
            rawoutput("<td align='right'>&nbsp;</td>");
            rawoutput("</tr><tr>");
            $sql = "SELECT messageid FROM ".db_prefix($maildb)." WHERE msgto='{$acctid}' AND messageid < '$id' ORDER BY messageid DESC LIMIT 1";
            $result = db_query($sql);
            if (db_num_rows($result)>0){
                $row = db_fetch_assoc($result);
                $pid = $row['messageid'];
            }else{
                $pid = 0;
            }
            $sql = "SELECT messageid FROM ".db_prefix($maildb)." WHERE msgto='{$acctid}' AND messageid > '$id' ORDER BY messageid  LIMIT 1";
            $result = db_query($sql);
            if (db_num_rows($result)>0){
                $row = db_fetch_assoc($result);
                $nid = $row['messageid'];
            }else{
                $nid = 0;
            }
            $prev = translate_inline("< Previous");
            $next = translate_inline("Next >");
            rawoutput("<td nowrap='true'>");
            if ($pid > 0){ rawoutput("<a href='runmodule.php?module=letteropener&op=read&id=$pid' class='motd'>".htmlentities($prev)."</a>");
                addnav("","runmodule.php?module=letteropener&op=read&id=$pid");}
            else{ rawoutput(htmlentities($prev));}
            rawoutput("</td><td nowrap='true'>");
            if ($nid > 0){ rawoutput("<a href='runmodule.php?module=letteropener&op=read&id=$nid' class='motd'>".htmlentities($next)."</a>");
                addnav("","runmodule.php?module=letteropener&op=read&id=$nid");}
            else{ rawoutput(htmlentities($next));}
            rawoutput("</td>");
            rawoutput("</tr></table>");
        }
    }elseif($op=="lastfew"){
        output("Here are the last %s non-system YOMs",get_module_setting("num"));
        $sql="select * from ".db_prefix($maildb)." where msgfrom>0 ORDER BY messageid DESC limit ".get_module_setting("num")."";
        $res=db_query($sql);
        $to=translate_inline("To");
        $from=translate_inline("From");
        require_once("lib/sanitize.php");
        for ($i=0;$i<db_num_rows($res);$i++){  
            $row=db_fetch_assoc($res);
            $sql2="select name from ".db_prefix("accounts")." where acctid=".$row['msgto']."";
            $res2=db_query($sql2);
            $row2=db_fetch_assoc($res2);
            $toname=color_sanitize($row2['name']);
            $sql3="select name from ".db_prefix("accounts")." where acctid=".$row['msgfrom']."";
            $res3=db_query($sql3);
            $row3=db_fetch_assoc($res3);
            $fromname=color_sanitize($row3['name']);
            rawoutput("<table border=1 width=100%><tr><td>$from :$fromname - ".date("M d, h:i a",strtotime($row['sent']))." - $to : $toname</td></tr><tr><td>".$row['body']."</td></tr></table><br>");
        }
    }elseif($op==""){
        output("Whose mail would you like to read?`n");
        rawoutput("<form action='runmodule.php?module=letteropener' method='POST'>");
        rawoutput("<input name='q' id='q'>");
        $se = translate_inline("Search");
        rawoutput("<input type='submit' class='button' value='$se'>");
        rawoutput("</form>");
        rawoutput("<script language='JavaScript'>document.getElementById('q').focus();</script>");
        addnav("","runmodule.php?module=letteropener");
        
        $searchresult = false;
        $where = "";
        $op="";
        $sql = "SELECT acctid,login,name FROM " . db_prefix("accounts");
        if ($query != "") {
            $where = "WHERE login='$query' OR name='$query'";
            $searchresult = db_query($sql . " $where  ORDER BY '$order' LIMIT 2");
        }
    
        if ($query !== false || $searchresult) {
            if (db_num_rows($searchresult) != 1) {
                $where="WHERE login LIKE '%$query%' OR acctid LIKE '%$query%' OR name LIKE '%$query%' OR emailaddress LIKE '%$query%' OR lastip LIKE '%$query%' OR uniqueid LIKE '%$query%' OR gentimecount LIKE '%$query%' OR level LIKE '%$query%'";
                $searchresult = db_query($sql . " $where  ORDER BY '$order' LIMIT 101");
            }
            if (db_num_rows($searchresult)<=0){
                output("`\$No results found`0");
                $where="";
            }elseif (db_num_rows($searchresult)>100){
                output("`\$Too many results found, narrow your search please.`0");
                $op="";
                $where="";
            }else{
                $op="";
                $display=1;
            }
        }
        
            if ($display == 1){
            $q = "";
            if ($query) {
                $q = "&q=$query";
            }
    
            $acid =translate_inline("AcctID");
            $login =translate_inline("Login");
            $nm =translate_inline("Name");
    
            $rn=0;
            $oorder = "";
            while ($row=db_fetch_assoc($searchresult)) {
                $laston = relativedate($row['laston']);
                $loggedin =
                    (date("U") - strtotime($row['laston']) <
                     getsetting("LOGINTIMEOUT",900) && $row['loggedin']);
                if ($loggedin)
                    $laston=translate_inline("`#Online`0");
                $row['laston']=$laston;
                if ($row[$order]!=$oorder) $rn++;
                $oorder = $row[$order];
				rawoutput("<table align=center border=1 width=350>");
				rawoutput("<tr class='trhead'><td>$acid: ");
                output_notl("`&%s`0", $row['acctid'],true);
				rawoutput("</td><td>$login: ");
                output_notl("`&%s`0", $row['login'],true);
				rawoutput("</td>");
                rawoutput("<td rowspan=2 align=left nowrap>");
                addnav("","runmodule.php?module=letteropener&op=to&to={$row['login']}");
                addnav("","runmodule.php?module=letteropener&op=from&from={$row['login']}");
                $to=translate_inline("All messages `#to`& this person");
                $from=translate_inline("All messages `#from`& this person");
                output_notl("<a href='runmodule.php?module=letteropener&op=to&to={$row['login']}'>`&&#149;%s`7</a>",$to,true);
                rawoutput("<br>");
                output_notl("<a href='runmodule.php?module=letteropener&op=from&from={$row['login']}'>`&&#149;%s`7</a>",$from,true);
                rawoutput("</td></tr><tr><td colspan=2>");
                output_notl("`&%s`7", $row['name'],true);
				rawoutput("</td></tr></table><Br>");
			}
        } 
    }elseif($op=="to"){
        $subject="";
        $body="";
        $row = "";
        addnav("Read someone else's mail","runmodule.php?module=letteropener");
        $to = httpget('to');
        $from = httpget('from');
        if ($to!=""){
            $sql = "SELECT acctid,login,name superuser FROM " . db_prefix("accounts") . " WHERE login=\"$to\"";
            $result = db_query($sql);
            $row = db_fetch_assoc($result);
            $sql = "SELECT acctid FROM " . db_prefix("accounts") . " WHERE login='".$row['login']."'";
            $result = db_query($sql);
            $row2 = db_fetch_assoc($result);
            $acctid=$row2['acctid'];
            rawoutput("<table>");
            $session['message']="";
            $sql = "SELECT subject,messageid," . db_prefix("accounts") . ".name,msgfrom,seen,sent FROM " . db_prefix($maildb) . " LEFT JOIN " . db_prefix("accounts") . " ON " . db_prefix("accounts") . ".acctid=" . db_prefix($maildb) . ".msgfrom WHERE msgto=\"".$acctid."\" ORDER BY sent DESC";
            $result = db_query($sql);
            if (db_num_rows($result)>0){
				while ($row = db_fetch_assoc($result)) {
            		tlschema("mail");
                    if ((int)$row['msgfrom']==0){
                        $row['name']=translate_inline("`i`^System`0`i");
                        if (is_array(unserialize($row['subject']))) {
                            $row['subject'] = unserialize($row['subject']);
                            $row['subject'] =
                                call_user_func_array("sprintf_translate",
                                        $row['subject']);
                        }
                    }
                    tlschema();
                    $id=$row['messageid'];
                    output_notl("<tr>",true);
                    output_notl("<td nowrap><img src='images/".($row['seen']?"old":"new")."scroll.GIF' width='16' height='16' alt='".($row['seen']?"Old":"New")."'></td>",true);
                    output_notl("<td><a href='runmodule.php?module=letteropener&op=read&id=$id&login=$to'>",true);
                    addnav("","runmodule.php?module=letteropener&op=read&id=$id&login=$to");			
                    if (trim($row['subject'])==""){	output("`i(No Subject)`i");}
                    else{				output_notl($row['subject']);}
                    output_notl("</a></td><td><a href='runmodule.php?module=letteropener&op=read&id=$id&login=$to'>",true);
                                addnav("","runmodule.php?module=letteropener&op=read&id=$id&login=$to");
                    output("- from %s",$row['name']);
                    output_notl("</a></td><td><a href='runmodule.php?module=letteropener&op=read&id=$id&login=$to'>".date("M d, h:i a",strtotime($row['sent']))."</a></td>",true);
                    addnav("","runmodule.php?module=letteropener&op=read&id=$id&login=$to");
                    output_notl("</tr>",true);
                } //}  
            }else{
                output("`iThey have no mail.`i");
            }
        }elseif (db_num_rows($result)==0){
            output("`@No one was found who matches \"%s\".  ",stripslashes($to));
            $try = translate_inline("Please try again");
            output_notl("<a href='runmodule.php?module=letteropener'>$try</a>.",true);
            popup_footer();
            exit();
        }else{
            output_notl("<select name='to' id='to' onChange='check_su_warning();'>",true);
            $superusers = array();
            for ($i=0;$i<db_num_rows($result);$i++){
                $row = db_fetch_assoc($result);
                output_notl("<option value=\"".HTMLEntities($row['login'])."\">",true);
                output_notl("%s", full_sanitize($row['name']));
                if (($row['superuser'] & SU_GIVES_YOM_WARNING) &&
                        !($row['superuser'] & SU_OVERRIDE_YOM_WARNING)) {
                    array_push($superusers,$row['login']);
                }
            }
            output_notl("</select>`n",true);
        }
        output_notl("</table>",true);

    }elseif($op=="from"){
        $subject="";
        $body="";
        $row = "";
        addnav("Read someone else's mail","runmodule.php?module=letteropener");
        $from = httpget('from');
        if ($from!=""){
            $sql = "SELECT acctid,login,name superuser FROM " . db_prefix("accounts") . " WHERE login=\"$from\"";
            $result = db_query($sql);
            $row = db_fetch_assoc($result);
            $sql = "SELECT acctid FROM " . db_prefix("accounts") . " WHERE login='".$row['login']."'";
            $result = db_query($sql);
            $row2 = db_fetch_assoc($result);
            $acctid=$row2['acctid'];
            output_notl("<table>",true);
            $session['message']="";
            $sql = "SELECT subject,messageid," . db_prefix("accounts") . ".name,msgto,seen,sent FROM " . db_prefix($maildb) . " LEFT JOIN " . db_prefix("accounts") . " ON " . db_prefix("accounts") . ".acctid=" . db_prefix($maildb) . ".msgto WHERE msgfrom=\"".$acctid."\" ORDER BY sent DESC";
            $result = db_query($sql);
            if (db_num_rows($result)>0){
                for ($i=0;$i<db_num_rows($result);$i++){
                    $row = db_fetch_assoc($result);
                    $sql2="Select name from ".db_prefix("accounts")." where acctid=".$row['msgto']."";
					$result2 = db_query($sql2);
                    $row2 = db_fetch_assoc($result2);
					$toname=$row2['name'];
                    $id=$row['messageid'];
                    output_notl("<tr>",true);
                    output_notl("<td nowrap><img src='images/".($row['seen']?"old":"new")."scroll.GIF' width='16' height='16' alt='".($row['seen']?"Old":"New")."'></td>",true);
                    output_notl("<td><a href='runmodule.php?module=letteropener&op=read&id=$id&login=$from'>",true);
                    if (trim($row['subject'])==""){	output("`i(No Subject)`i");}
                    else{				output_notl($row['subject']);}
                    output_notl("</a></td><td><a href='runmodule.php?module=letteropener&op=read&id=$id&login=$from'>",true);
                    addnav("","runmodule.php?module=letteropener&op=read&id=$id&login=$from");
					output("- to %s",$toname);
                    output_notl("</a></td><td><a href='runmodule.php?module=letteropener&op=read&id=$id&login=$from'>".date("M d, h:i a",strtotime($row['sent']))."</a></td>",true);
                    output_notl("</tr>",true);
                } //}
            }else{
                output("`iThey have not sent any mail.`i");
            }
        }elseif (db_num_rows($result)==0){
                output("`@No one was found who matches \"%s\".  ",stripslashes($from));
                $try = translate_inline("Please try again");
                output_notl("<a href='runmodule.php?module=letteropener'>$try</a>.",true);
                popup_footer();
                exit();
            }else{
                output_notl("<select name='to' id='to' onChange='check_su_warning();'>",true);
                $superusers = array();
                for ($i=0;$i<db_num_rows($result);$i++){
                    $row = db_fetch_assoc($result);
                    output_notl("<option value=\"".HTMLEntities($row['login'])."\">",true);
                    output_notl("%s", full_sanitize($row['name']));
                    if (($row['superuser'] & SU_GIVES_YOM_WARNING) &&
                            !($row['superuser'] & SU_OVERRIDE_YOM_WARNING)) {
                        array_push($superusers,$row['login']);
                    }
                }
                output_notl("</select>`n",true);
            }
            output_notl("</table>",true);
    }
    page_footer();
}
?>