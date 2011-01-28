<?php

//run this for the new accounts_everypage table
// require_once("lib/tabledescriptor.php");
// $accounts_everypage = array(
	// 'acctid'=>array('name'=>'acctid', 'type'=>'int(11) unsigned'),
	// 'allowednavs'=>array('name'=>'allowednavs', 'type'=>'mediumtext'),
	// 'laston'=>array('name'=>'laston', 'type'=>'datetime'),
	// 'gentime'=>array('name'=>'gentime', 'type'=>'double'),
	// 'gentimecount'=>array('name'=>'gentimecount', 'type'=>'int(11) unsigned'),
	// 'gensize'=>array('name'=>'gensize', 'type'=>'int(11) unsigned'),
	// 'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'acctid'),
// );
// synctable(db_prefix('accounts_everypage'), $accounts_everypage, true);

$baseaccount = array();
function do_forced_nav($anonymous,$overrideforced){
	global $baseaccount, $session,$REQUEST_URI;
	rawoutput("<!--\nAllowAnonymous: ".($anonymous?"True":"False")."\nOverride Forced Nav: ".($overrideforced?"True":"False")."\n-->");
	//debug($session,true);
	if (isset($session['loggedin']) && $session['loggedin']){
		
		$acctid = $session['user']['acctid'];
		
		$session['user'] = datacache("accounts/account_".$acctid,60);
		
		if (!is_array($session['user'])){
			$sql = "SELECT *  FROM ".db_prefix("accounts")." WHERE acctid = '".$acctid."'";
			$result = db_query($sql);
			if (db_num_rows($result)==1){
				$session['user']=db_fetch_assoc($result);
				updatedatacache("accounts/account_".$session['user']['acctid'],$session['user']);
			} else {
				debug($session,true);
				$session=array();
				$session['message']=translate_inline("`4Your login information is incorrect!`0","login");
				redirect("index.php","Account Disappeared!");
			}
			db_free_result($result);
		}
		
		$baseaccount = $session['user'];
		$session['bufflist']=unserialize($session['user']['bufflist']);
		if (!is_array($session['bufflist'])) $session['bufflist']=array();
		$session['user']['dragonpoints']=unserialize($session['user']['dragonpoints']);
		$session['user']['prefs']=unserialize($session['user']['prefs']);
		if (!is_array($session['user']['dragonpoints'])) $session['user']['dragonpoints']=array();
		
		
		//get allowednavs
		/*
		accounts_everypage table includes:
			acctid (primary key, unique)
			allowednavs
			laston
			gentime
			gentimecount
			gensize
		*/
		$sql = "SELECT * FROM ".db_prefix("accounts_everypage")." WHERE acctid = '".$session['user']['acctid']."'";
		$result = db_query($sql);
		if (db_num_rows($result)==1){
			//debug("Getting fresh info from accounts_everypage");
			$row = db_fetch_assoc($result);
			$session['user']['allowednavs'] = $row['allowednavs'];
			$session['user']['laston'] = $row['laston'];
			$session['user']['gentime'] = $row['gentime'];
			$session['user']['gentimecount'] = $row['gentimecount'];
			$session['user']['gensize'] = $row['gensize'];
		} else {
			$sql = "INSERT INTO ".db_prefix("accounts_everypage")." (acctid,allowednavs,laston,gentime,gentimecount,gensize) VALUES ('".$session['user']['acctid']."','".$session['user']['allowednavs']."','".$session['user']['laston']."','".$session['user']['gentime']."','".$session['user']['gentimecount']."','".$session['user']['gensize']."')";
			db_query($sql);
		}
		
		if (is_array(unserialize($session['user']['allowednavs']))){
			$session['allowednavs']=unserialize($session['user']['allowednavs']);
		}else{
			$session['allowednavs']=array($session['user']['allowednavs']);
		}
		
		if (!$session['user']['loggedin'] || ( (date("U") - strtotime($session['user']['laston'])) > getsetting("LOGINTIMEOUT",900)) ){
			$session=array();
			redirect("index.php?op=timeout","Account not logged in but session thinks they are.");
		}
		
		db_free_result($result);
		
		//old stuff from here
		// if (db_num_rows($result)==1){
			// $session['user']=db_fetch_assoc($result);
			// $baseaccount = $session['user'];
			// $session['bufflist']=unserialize($session['user']['bufflist']);
			// if (!is_array($session['bufflist'])) $session['bufflist']=array();
			// $session['user']['dragonpoints']=unserialize($session['user']['dragonpoints']);
			// $session['user']['prefs']=unserialize($session['user']['prefs']);
			// if (!is_array($session['user']['dragonpoints'])) $session['user']['dragonpoints']=array();
			// if (is_array(unserialize($session['user']['allowednavs']))){
				// $session['allowednavs']=unserialize($session['user']['allowednavs']);
			// }else{
				// $session['allowednavs']=array($session['user']['allowednavs']);
			// }
			// if (!$session['user']['loggedin'] || ( (date("U") - strtotime($session['user']['laston'])) > getsetting("LOGINTIMEOUT",900)) ){
				// $session=array();
				// redirect("index.php?op=timeout","Account not logged in but session thinks they are.");
			// }
		// }else{
			// $session=array();
			// $session['message']=translate_inline("`4Error, your login was incorrect`0","login");
			// redirect("index.php","Account Disappeared!");
		// }
		// db_free_result($result);
		
		//debug($session);
		
		//check the nav exists in the session's allowednavs array
		if (isset($session['allowednavs'][$REQUEST_URI]) && $session['allowednavs'][$REQUEST_URI] && $overrideforced!==true){
			//The nav is fine
			//clear the navs - more navs will be added as the script the player is currently viewing loads and executes
			$session['allowednavs']=array();
		}else{
			if ($overrideforced!==true){
				//This nav is not fine at all.  Redirect the player to badnav.php.
				$session['badnav'] = 1;
				redirect("badnav.php","Navigation not allowed to $REQUEST_URI");
			}
		}
	}else{
		if (!$anonymous){
			$session['message']=translate_inline("You are not logged in, this may be because your session timed out.","login");
			redirect("index.php?op=timeout&nli=true","Not logged in: $REQUEST_URI");
		}
	}
}
?>
