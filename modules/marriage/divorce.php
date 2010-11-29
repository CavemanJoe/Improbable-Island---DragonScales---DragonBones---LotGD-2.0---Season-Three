<?php
function marriage_divorce() {
	global $session;
	$who = $session['user']['marriedto'];
	if ($who==0) {
		return;
	} else if ($who==INT_MAX) {
		require_once("lib/partner.php");
		$getpartner=get_partner(false);
		$session['user']['marriedto'] = 0;
		addnews("`&%s`% and `&%s`% were divorced today...", $session['user']['name'],$getpartner);
		debuglog("got divorced from $getpartner today.");
		return;
	}
	$session['user']['marriedto'] = 0;
	$sql = "SELECT name,sex FROM ".db_prefix("accounts")." WHERE acctid='$who' AND locked=0";
	$res = db_query($sql);
	if (db_num_rows($res)<1) return;
	$row = db_fetch_assoc($res);
	$sql = "UPDATE " . db_prefix("accounts") . " SET marriedto='0' WHERE acctid='$who'";
	db_query($sql);
	if (get_module_setting("dmoney")>0) $gold=round($session['user']['gold']*get_module_setting("dmoney")/100);
	$mailmessage=array("`^%s`@ has divorced you.",$session['user']['name']);
	$mailmessagg=array("`^%s`@ has divorced you.`n`nYou get `^%s gold`@.",$session['user']['name'],$gold);
	$t = array("`@Divorce!");
	addnews("`&%s`0`% and `&%s`% were divorced today...", $session['user']['name'],$row['name']);
	debuglog($session['user']['login']." got a divorce from {$row['name']}",$who,$session['user']['acctid']);
	debuglog($session['user']['login']." got a divorce from {$row['name']}",$session['user']['acctid'],$who);
	require_once("lib/systemmail.php");
	if (get_module_setting('dmoney')>0&&$gold>0) {
		$sql = "UPDATE " . db_prefix("accounts") . " SET gold=gold + ".$gold." WHERE acctid='$who'";
		$session['user']['gold']=0;
		db_query($sql);
		systemmail($who,$t,$mailmessagg);
		output_notl("`n`n");
		output("`@You notice also that all your gold at hand is gone... \"`&I need this to make myself a new home, thanks...`@\"`n");
	} else {
		systemmail($who,$t,$mailmessage);
	}
	output("`n`@You feel guilty about the divorce.");
	invalidatedatacache("marriage-marriedonline");
	invalidatedatacache("marriage-marriedrealm");
	set_module_objpref("marriage",$who,"marriagedate","0000-00-00 00:00:00");
	set_module_objpref("marriage",$session['user']['acctid'],"marriagedate","0000-00-00 00:00:00");
	
	//Check to make sure the divorced gets a negative buff the next day; otherwise no more marriage buff anymore
	$allprefsr=unserialize(get_module_pref('allprefs','marriage',$who));
	if (get_module_setting("acceptbuff")==1) $allprefsr['received']=2;
	else $allprefsr['received']=0;
	set_module_pref('allprefs',serialize($allprefsr),'marriage',$who);
	
	//prevent from getting the marriage buff anymore
	$allprefs=unserialize(get_module_pref('allprefs'));
	$allprefs['received']=0;
	set_module_pref('allprefs',serialize($allprefs));

	apply_buff('marriage-divorce',
		array(
			"name"=>"`4Divorce Guilt",
			"rounds"=>100,
			"wearoff"=>"`\$You feel no longer guilty about your divorce.",
			"defmod"=>0.83,
			"survivenewday"=>1,
			"roundmsg"=>"`\$Guilt haunts you.",
			)
	);
}
?>