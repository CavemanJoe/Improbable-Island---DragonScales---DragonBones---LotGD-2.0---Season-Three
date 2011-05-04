<?php

function countdown_getmoduleinfo(){
    $info = array(
        "name"=>"Season Two Countdown",
        "version"=>"2009-07-04",
        "author"=>"Dan Hall",
        "category"=>"Improbable",
        "download"=>"",
    );
    return $info;
}

function countdown_install(){
	module_addhook("everyheader");
    return true;
}

function countdown_uninstall(){
    return true;
}

function countdown_dohook($hookname,$args){
    global $session;
	switch($hookname){
    case "everyheader":
		// $end = countdown_timeleft(0,1246827600);
		// if ($end){
			// output("`c`b`\$%s:%s:%s:%s`0`b`c`n`n",$end[0],$end[1],$end[2],$end[3]);
		// } else {
			// output("`c`b`\$STARTING CHANGEOVER`nLOGGING CHARACTERS OUT`nWARMING UP`0`b`c`n`n");
			output("`c`b`\$Improbable Island will be down for a couple of minutes while we fix a bug in the commentary system.  When it comes back online, please CLOSE GLOBAL BANTER and LEAVE WHATEVER PAGE YOU'RE ON before returning.  If you're in a house or an Outpost, go outside and come back in.  If you're in the Jungle, go into an Outpost and leave again.  Whatever you were doing while chatting, go and do something else for a second or so, and then come back again, and everything should be right as rain.  Thank you.`0`b`c`n`n");
			if ($session['user']['acctid'] != 1){
				$session['user']['loggedin'] = 0;
			}
		// }
		break;
    }
	return $args;
}

function countdown_run(){
}

function countdown_timeleft($time_left=0, $endtime=null) { 
	if($endtime != null) $time_left = $endtime - time();
	if($time_left > 0) {
		$days = floor($time_left / 86400);
		$time_left = $time_left - $days * 86400;
		$hours = floor($time_left / 3600);
		$time_left = $time_left - $hours * 3600;
		$minutes = floor($time_left / 60);
		$seconds = $time_left - $minutes * 60;
	} else {
		return false;
	}
	if ($days<10) $days = sprintf("%02d", $days);
	if ($hours<10) $hours = sprintf("%02d", $hours);
	if ($minutes<10) $minutes = sprintf("%02d", $minutes);
	if ($seconds<10) $seconds = sprintf("%02d", $seconds);
	return array($days, $hours, $minutes, $seconds); 
}

?>