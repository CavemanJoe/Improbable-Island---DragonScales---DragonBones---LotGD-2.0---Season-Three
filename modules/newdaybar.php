<?php

/*
 * Title:       New Day Bar
 * Date:	Sep 06, 2004
 * Version:	1.2
 * Author:      Joshua Ecklund
 * Email:       m.prowler@cox.net
 * Purpose:     Add a countdown timer for the new day to "Personal Info"
 *              status bar.
 *
 * --Change Log--
 *
 * Date:    	Jul 30, 2004
 * Version:	1.0
 * Purpose:     Initial Release
 *
 * Date:        Aug 01, 2004
 * Version:     1.1
 * Purpose:     Various changes/fixes suggested by JT Traub (jtraub@dragoncat.net)
 *
 * Date:        Sep 06, 2004
 * Version:     1.2
 * Purpose:     Updated to use functions included in 0.9.8-prerelease.3
 *
 */

function newdaybar_getmoduleinfo(){
	$info = array(
		"name"=>"New Day Bar",
		"version"=>"1.2",
		"author"=>"Joshua Ecklund",
        "download"=>"http://dragonprime.net/users/mProwler/newdaybar.zip",
		"category"=>"Stat Display",
	);
	return $info;
}

function newdaybar_install(){
	module_addhook("charstats");
	return true;
}

function newdaybar_uninstall(){
	return true;
}

function newdaybar_dohook($hookname,$args){
	global $session;

	switch($hookname){
		case "charstats":
			require_once("lib/datetime.php");
			$gt = gametimedetails();
			setcharstat("Time", "Game Time", gmdate("g:i a",$gt['gametime']));
			setcharstat("Time", "New day in:", date("H:i:s",secondstonextgameday()));
			break;
	}
	return $args;
}

function newdaybar_run(){

}
?>
