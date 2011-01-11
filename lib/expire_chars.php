<?php
// translator ready
// addnews ready
// mail ready

require_once("lib/constants.php");

$starttime = microtime(true);

$lastexpire = strtotime(getsetting("last_char_expire","0000-00-00 00:00:00"));
//testing val to force char deletion
//$lastexpire = 0;
$needtoexpire = strtotime("-23 hours");
if ($lastexpire < $needtoexpire){
	savesetting("last_char_expire",date("Y-m-d H:i:s"));
	$old = getsetting("expireoldacct",45);
	$new = getsetting("expirenewacct",10);
	$trash = getsetting("expiretrashacct",1);

	# First, get the account ids to delete the user prefs.
	$sql = "SELECT acctid FROM " . db_prefix("accounts") . " WHERE (superuser&".NO_ACCOUNT_EXPIRATION.")=0 AND (1=0\n".($old>0?"OR (laston < \"".date("Y-m-d H:i:s",strtotime("-$old days"))."\")\n":"").($new>0?"OR (laston < \"".date("Y-m-d H:i:s",strtotime("-$new days"))."\" AND level=1 AND dragonkills=0)\n":"").($trash>0?"OR (laston < \"".date("Y-m-d H:i:s",strtotime("-".($trash+1)." days"))."\" AND level=1 AND experience < 10 AND dragonkills=0)\n":"").")";
	$result = db_query($sql);
	$acctids = array();
	while($row = db_fetch_assoc($result)) {
		$acctids[] = $row['acctid'];
	}
	require_once("lib/charcleanup.php");
	char_cleanup_allinone($acctids,$type);

	//seven-day warning
	$old-=7;
	$sql = "SELECT acctid,emailaddress,name,laston FROM " . db_prefix("accounts") . " WHERE 1=0 ".($old>0?"OR (laston < \"".date("Y-m-d H:i:s",strtotime("-$old days"))."\")\n":"")." AND emailaddress!='' AND sentnotice=0 AND (superuser&".NO_ACCOUNT_EXPIRATION.")=0";
	$result = db_query($sql);
	$subject = translate_inline("Improbable Island Character Expiration");
	while ($row = db_fetch_assoc($result)) {
		$body = sprintf_translate("The character known as %s in Improbable Island (%s) is about to expire due to inactivity.  The character was last online at %s.  If you wish to keep this character, you should log on within the week!  If this character has a HyperRing, you can ignore this message.  Thanks!",appoencode($row['name']),getsetting("serverurl","http://".$_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT'] == 80?"":":".$_SERVER['SERVER_PORT']).dirname($_SERVER['REQUEST_URI'])),$row['laston']);
		//mail($row['emailaddress'],$subject,
		//$body,
		//"From: ".getsetting("gameadminemail","postmaster@localhost.com")
		//);
		$sql = "UPDATE " . db_prefix("accounts") . " SET sentnotice=1 WHERE acctid='{$row['acctid']}'";
		db_query($sql);
	}
	$endtime = microtime(true);
	$totaltime = $endtime - $starttime;
	gamelog("Character expiration took $totaltime seconds","Performance");
}
?>
