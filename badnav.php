<?php
// translator ready
// addnews ready
// mail ready
define("OVERRIDE_FORCED_NAV",true);
require_once("common.php");
require_once("lib/villagenav.php");

tlschema("badnav");

if ($session['user']['loggedin'] && $session['loggedin']){
<<<<<<< HEAD
	// if (strpos($session['output'],"<!--CheckNewDay()-->")){
		// checkday();
	// }
=======
	if (strpos($session['output'],"<!--CheckNewDay()-->")){
		checkday();
	}
>>>>>>> 8b5d92281350005db7709911b00777e80705dd6e
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
<<<<<<< HEAD
	if (!is_array($session['allowednavs']) ||
			count($session['allowednavs'])==0 || $row['output']=="") {
=======
	if (!is_array($session['allowednavs']) || count($session['allowednavs'])==0 || $row['output']=="") {
>>>>>>> 8b5d92281350005db7709911b00777e80705dd6e
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
<<<<<<< HEAD
	// echo "BADNAV OUTPUT!<br><br>";
	// var_dump($session);
	echo $row['output'];
	$session['debug']="";
	$session['user']['allowednavs']=$session['allowednavs'];
=======
        if ($session['badnav'] > 2){
            $badnavbox = "<div style=\"background-color:#ffffff; color:#000000;\"><strong>Hey, is something wrong?</strong><br /><br />We're showing you this message because it looks like you've gotten stuck.  Please see <a href=\"http://enquirer.improbableisland.com/dokuwiki/doku.php?id=badnav\">this page</a> for more details about what's going on.  Or, try one of these links instead of the links in the actual page down there - they don't have easily-recognisable names, but one of them might look like what you wanted to do.  Please try the links in the actual, regular page before you click on these links.<br /><br />If you see this message consistently, please add your tuppence'orth to <a href='http://enquirer.improbableisland.com/forum/viewtopic.php?showtopic=19239'>this forum thread</a>.<br /><br />";
            foreach($session['allowednavs'] AS $key=>$vals){
                if (!strpos($key,"superuser") && !strpos($key,"taunt")){
                    $badnavbox.="<a href=\"".$key."\">".$key."</a><br />";
                }
            }
            $badnavbox.="</div>";
            echo $badnavbox;

        }
	echo $row['output'];
	$session['debug']="";
	$session['user']['allowednavs']=$session['allowednavs'];
	$session['badnav']+=1;
>>>>>>> 8b5d92281350005db7709911b00777e80705dd6e
	saveuser();
}else{
	$session=array();
	translator_setup();
	redirect("index.php","Redirected from badnav.php to index.php because the player was not logged in.");
}

?>