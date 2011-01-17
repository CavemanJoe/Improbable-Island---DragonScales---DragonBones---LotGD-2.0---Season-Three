<?php

global $session;
define("ALLOW_ANONYMOUS",true);
define("OVERRIDE_FORCED_NAV",true);

require_once "common.php";

$now = time();
$expiresin = strtotime($session['user']['laston']) + 600;
$section = $_REQUEST['section'];
if ($now > $expiresin || ($session['user']['chatloc'] != $section  && $session['user']['chatloc']."_aux" != $section)){
	echo "Chat disabled due to inactivity";
} else {
	require_once "lib/commentary.php";
	$message = $_REQUEST['message'];
	$limit = $_REQUEST['limit'];
	$talkline = $_REQUEST['talkline'];
	$returnlink = urlencode($_REQUEST['returnlink']);
	$showmodlink = $_REQUEST['showmodlink'];
	
	// echo("test!");
	// echo($returnlink);
	// echo($_REQUEST['returnlink']);
	echo($_REQUEST['message']);
	echo($_REQUEST['limit']);
	echo($_REQUEST['talkline']);
	
	$commentary = preparecommentaryblock($section,$message,$limit,$talkline,$schema=false,$skipfooter=false,$customsql=false,$skiprecentupdate=false,$showmodlink,$returnlink);
	$commentary = appoencode("`n".$commentary."`n",true);
	echo($commentary);
}
saveuser();



?>