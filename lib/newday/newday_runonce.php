<?php
//newday runonce
	//Let's do a new day operation that will only fire off for
	//one user on the whole server.
	require_once("lib/gamelog.php");
	
	//Get the current time.
	$starttime = microtime(true);
	
	//run the hook.
	$modulestarttime = microtime(true);
	modulehook("newday-runonce",array());
	$moduleendtime = microtime(true);
	$moduletime = $moduleendtime-$modulestarttime;
	gamelog("Newday-runonce modules took $moduletime seconds.","Performance");
	
	//Do some high-load-cleanup

	//Moved from lib/datacache.php
	if (getsetting("usedatacache",0)){
		$handle = opendir($datacachefilepath);
		while (($file = readdir($handle)) !== false) {
			if (substr($file,0,strlen(DATACACHE_FILENAME_PREFIX)) == DATACACHE_FILENAME_PREFIX){
				$fn = $datacachefilepath."/".$file;
				$fn = preg_replace("'//'","/",$fn);
				$fn = preg_replace("'\\\\'","\\",$fn);
				if (is_file($fn) && filemtime($fn) < strtotime("-24 hours")){
					@unlink($fn);
				}
			}
		}
	}
	//Expire Chars
	require_once("lib/expire_chars.php");

	//Clean up old mails
	$sql = "DELETE FROM " . db_prefix("mail") . " WHERE sent<'".date("Y-m-d H:i:s",strtotime("-".getsetting("oldmail",14)."days"))."'";
	db_query($sql);
	massinvalidate("mail");

	//Wipe the news entirely
	$sql = "TRUNCATE TABLE ".db_prefix("news");
	db_query($sql);
	
	//wipe the contents of the whostyping table
	$sql = "TRUNCATE TABLE ".db_prefix("whostyping");
	db_query($sql);

	if (getsetting("expirecontent",180)>0){
		//Clean up debug log, moved from there
               	$timestamp = date("Y-m-d H:i:s",strtotime("-".round(getsetting("expirecontent",180)/10,0)." days"));
      	        $sql = "DELETE FROM " . db_prefix("debuglog") . " WHERE date <'$timestamp'";
 	   	db_query($sql);
       		gamelog("Cleaned up ".db_affected_rows()." from ".db_prefix("debuglog")." older than $timestamp.",'maintenance');

		//Clean up game log
		$timestamp = date("Y-m-d H:i:s",strtotime("-1 month"));
		$sql = "DELETE FROM ".db_prefix("gamelog")." WHERE date < '$timestamp' ";
		db_query($sql);
		gamelog("Cleaned up ".db_prefix("gamelog")." table removing ".db_affected_rows()." older than $timestamp.","maintenance");

		//Clean up old comments
		$sql = "DELETE FROM " . db_prefix("commentary") . " WHERE postdate<'".date("Y-m-d H:i:s",strtotime("-".getsetting("expirecontent",180)." days"))."'";
		db_query($sql);
		gamelog("Deleted ".db_affected_rows()." old comments.","comment expiration");
		
		//Clean up commentary that extends back more than 100 pages (IE more than 2,500 comments)
		//First get each commentary area
		$sql = "SELECT DISTINCT section FROM ".db_prefix("commentary");
		$result = db_query($sql);
		$each = db_num_rows($result);
		for ($i=0; $i<$each; $i++){
			$sectinfo = db_fetch_assoc($result);
			$section = $sectinfo['section'];
			$eachsql = "SELECT postdate,commentid FROM ".db_prefix("commentary")." WHERE section='$section'";
			$sresult = db_query($eachsql);
			$snum = db_num_rows($sresult);
			if ($snum>2500){
				debug("Section ".$section." has more than 2500 comments.");
				$delnum = $snum - 2500;
				debug("We're going to delete ".$delnum." comments.");
				$del1sql = "DELETE FROM ".db_prefix("commentary")." WHERE section='$section' ORDER BY postdate ASC LIMIT $delnum";
				$del1result = db_query($del1sql);
			}
		}
		
		//Clean up old moderated comments

		$sql = "DELETE FROM " . db_prefix("moderatedcomments") . " WHERE moddate<'".date("Y-m-d H:i:s",strtotime("-".getsetting("expirecontent",180)." days"))."'";
		db_query($sql);
		gamelog("Deleted ".db_affected_rows()." old moderated comments.","comment expiration");
	}
	if (strtotime(getsetting("lastdboptimize", date("Y-m-d H:i:s", strtotime("-1 day")))) < strtotime("-1 day")){
		//defrag database
		require_once("lib/newday/dbcleanup.php");
		//empty datacache
		require_once("lib/datacache.php");
		empty_datacache();
	}
	
	require_once("lib/gamelog.php");
	$now = microtime(true);
	$nrtime = $now - $starttime;
	gamelog("Newday-Runonce took $nrtime seconds.","Performance");
?>
