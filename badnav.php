<?php
// translator ready
// addnews ready
// mail ready
define("OVERRIDE_FORCED_NAV",true);
require_once("common.php");
require_once("lib/villagenav.php");

tlschema("badnav");

if ($session['user']['loggedin'] && $session['loggedin']){
	if (strpos($session['output'],"<!--CheckNewDay()-->")){
		checkday();
	}
	while (list($key,$val)=each($session['allowednavs'])){
		//hack-tastic.
		if (
			trim($key)=="" ||
			$key===0 ||
			substr($key,0,8)=="motd.php" ||
			substr($key,0,8)=="mail.php"
		) unset($session['allowednavs'][$key]);
	}
	$sql="SELECT output FROM ".db_prefix("accounts_output")." WHERE acctid={$session['user']['acctid']};";
	$result=db_query($sql);
	$row=db_fetch_assoc($result);
	if (!is_array($session['allowednavs']) || count($session['allowednavs'])==0 || $row['output']=="") {
		$session['allowednavs']=array();
		page_header("Your Navs Are Corrupted");
		if ($session['user']['alive']) {
			villagenav();
			output("Your navs are corrupted, please return to %s.",
					$session['user']['location']);
		} else {
			addnav("Return to Shades", "shades.php");
			output("Your navs are corrupted, please return to the Shades.");
		}
		page_footer();
	}
	// echo "BADNAV OUTPUT!<br><br>";
	// var_dump($session);
	$badnavbox = "<div style=\"background-color:#ffffff; color:#000000;\"><strong>This page isn't live.</strong>  This is an outdated page that we're showing to you because we think it's the last thing you saw.".$session['badnav']."</div>";
	echo $badnavbox;
	echo $row['output'];
	$session['debug']="";
	$session['user']['allowednavs']=$session['allowednavs'];
	$session['badnav']+=1;
	saveuser();
}else{
	$session=array();
	translator_setup();
	redirect("index.php","Redirected from badnav.php to index.php because the player was not logged in.");
}

?>