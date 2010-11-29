<?php
function marriage_modifyflirtpoints($who=1,$amount=0,$from=-1,$punish=true) { //punish checks if flirting is with beloved one
	global $session;
	//now update the sent
	if ($from==-1) $from=$session['user']['acctid'];
	$list=get_module_pref('flirtssent','marriage',$from);
	$list=unserialize($list);
	if ($list=="") $list=array();
	if (array_key_exists("S".$who,$list)) {
		$list["S".$who]+=$amount; //even when negative
		} else {
		$list=array_merge(array("S".$who=>$amount),$list);
		}
	//if ($list["S".$who]<1) $list=array_splice($list,"S".$who,1); //clean up, not working
	set_module_pref('flirtssent',serialize($list),'marriage',$from);
	//now for the received ones
	$list=get_module_pref('flirtsreceived','marriage',$who);
	$list=unserialize($list);
	if ($list=="") $list=array();
	if (array_key_exists("S".$from,$list)) {
		$list["S".$from]+=$amount; //even when negative
		} else {
		$list=array_merge(array("S".$from=>$amount),$list);
		}
	set_module_pref('flirtsreceived',serialize($list),'marriage',$who);
	//if someone flirted not with the person married to
	if ($session['user']['marriedto']!=0&&
		$session['user']['marriedto']!=4294967295&&
		$who!=$session['user']['marriedto'] &&
		$from==$session['user']['acctid']&&
		$punish && $amount>0) {	
		$mailmessage=array("%s`0`@ has been unfaithful to you!",$session['user']['name']);
		$t = array("`%Uh oh!!");
		require_once("lib/systemmail.php");
		systemmail($session['user']['marriedto'],$t,$mailmessage);
		set_module_pref('flirtsfaith',get_module_pref('flirtsfaith')+1);
		output("`n`@`c`bShame on you! Don't be unfaithful!`b`c");;
		marriage_flirtdec();
	}
}

function marriage_getflirtpoints($who) {
	$list=get_module_pref('flirtssent','marriage');
	$list=unserialize($list);
	if (is_array($list)) return $list["S".$who];
	return 0;
}

function marriage_seencleanup($name) {
	$list=get_module_pref('flirtssent','marriage',$name);
	$list=unserialize($list);
	if ($list=="") $list=array();
	$out=array();
	while (list($who,$amount)=each($list)) {
		if ($amount<1) {
			marriage_receivedcleanup(substr($who,1));
		} else {
		$out=array_merge($out,array($who=>$amount));
		}
	}
	set_module_pref('flirtssent',serialize($out),'marriage',$name);
}

function marriage_receivedcleanup($name) {
	$list=get_module_pref('flirtsreceived','marriage',$name);
	$list=unserialize($list);
	if ($list=="") $list=array();
	$out=array();
	while (list($who,$amount)=each($list)) {
		if ($amount<1) {
			//marriage_receivedcleanup(substr($who,1));
		} else {
		$out=array_merge($out,array($who=>$amount));
		}
	}
	set_module_pref('flirtsreceived',serialize($out),'marriage',$name);
}

function marriage_removeplayer($name,$toremove) {
	$remove="S".$toremove;
	$list=get_module_pref('flirtsreceived','marriage',$name);
	$list=unserialize($list);
	if (!is_array($list)) $list=array();
	$out=array_diff_assoc($list,array($remove=>$list[$remove]));
	set_module_pref('flirtsreceived',serialize($out),'marriage',$name);
	$remove="S".$name;
	$list=get_module_pref('flirtssent','marriage',$toremove);
	$list=unserialize($list);
	if (!is_array($list)) $list=array();
	$out=array_diff_assoc($list,array($remove=>$list[$remove]));
	/*while (list($who,$amount)=each($list)) { //argh, obsolete
		if ($who!="S".$toremove) {
		$out=array_merge($out,array($who=>$amount));
		}
	}*/
	set_module_pref('flirtssent',serialize($out),'marriage',$toremove);
}

function marriage_flirtdec() {
	global $session;
	if (get_module_setting('flirtCharis')==1&&$session['user']['charm']>0) {
		if ($session['user']['charm']>0) $session['user']['charm']--;
		output("`n`n`^You LOSE a charm point!");
	}
}

function marriage_hidedata($data="") {
	static $num;
	$code = "";
	if (!is_numeric($num)||empty($num)) $num = 0;
	if ($num==0) rawoutput("<script language=\"JavaScript\">\nfunction marShowAndHide(theId)\n{\n   var el = document.getElementById(theId)\n\n   if (el.style.display==\"none\")\n   {\n      el.style.display=\"block\"; //show element\n   }\n   else\n   {\n      el.style.display=\"none\"; //hide element\n   }\n}\n</script>");
	$num++;
	$text = translate_inline("Show/Hide Data");
	$code .= "<a href=\"#\" onClick = marShowAndHide('marData$num')>$text</a>";
	$code .= "<div id='marData$num' style=\"display:none\">";
	$code .= $data;
	$code .= "</div>";
	return $code;
}

function marriage_flink($ac=1,$text="",$flir="") {
	global $session;
	$code = "";
	$sql = "SELECT login,sex,name,acctid FROM ".db_prefix("accounts")." WHERE acctid=$ac ORDER BY level,login";
	$result = db_query($sql);
	if (db_num_rows($result)!=0) {
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			$code = "<a href='runmodule.php?module=marriage&op=loveshack&op2=flirt&stage=1&gendertarget=".$row['sex']."&flirtitem=$flir&name=".urlencode($row['name'])."&target=".$row['acctid']."'>".translate_inline($text)."</a>";
			addnav("","runmodule.php?module=marriage&op=loveshack&op2=flirt&stage=1&gendertarget=".$row['sex']."&flirtitem=$flir&name=".urlencode($row['name'])."&target=".$row['acctid']);
		}
	}
	return $code;
}
?>