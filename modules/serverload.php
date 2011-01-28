<?php

// $sql = "TRUNCATE TABLE ".db_prefix("performance");
// db_query($sql);
// $sql = "TRUNCATE TABLE ".db_prefix("performancepage");
// db_query($sql);
// $sql = "TRUNCATE TABLE ".db_prefix("performancetime");
// db_query($sql);

function serverload_getmoduleinfo(){
	$info = array(
		"name"=>"Server Performance Statistics",
		"author"=>"Dan Hall",
		"version"=>"2009-10-28",
		"category"=>"Administrative",
		"download"=>"",
		"allowanonymous"=>"true",
		"settings"=>array(
			"time"=>"Total pagegen time since last update,float|0",
			"count"=>"Total page load count since last update,int|0",
			"avg"=>"Average page gen time since last update,float|0",
			"lastupdate"=>"Timestamp of last update,int|0",
			"ltime"=>"Total pagegen time at last update,float|0",
			"lcount"=>"Total page load count at last update,int|0",
			"ldaytime"=>"Total pagegen time at last game day,float|0",
			"ldaycount"=>"Total page load count at last game day,int|0",
			"lnoobtime"=>"Total pagegen time among new players at last update,float|0",
			"lnoobcount"=>"Total page load count among new players at last update,int|0",
			"peakpps"=>"Peak pages per second,float|0",
		),
	);
	
	return $info;
}

function serverload_install(){
	$performance = array(
		'numplayers'=>array('name'=>'numplayers', 'type'=>'int(11) unsigned'),
		'totaltime'=>array('name'=>'totaltime', 'type'=>'double unsigned'),
		'totalpages'=>array('name'=>'totalpages', 'type'=>'int(11) unsigned'),
		'maxpps'=>array('name'=>'maxpps', 'type'=>'double unsigned'),
		'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'numplayers'),
	);
	require_once("lib/tabledescriptor.php");
	synctable(db_prefix('performance'), $performance, true);
	$performancetime = array(
		'timeslice'=>array('name'=>'timeslice', 'type'=>'int(11) unsigned'),
		'totaltime'=>array('name'=>'totaltime', 'type'=>'double unsigned'),
		'totalpages'=>array('name'=>'totalpages', 'type'=>'int(11) unsigned'),
		'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'timeslice'),
	);
	synctable(db_prefix('performancetime'), $performancetime, true);
	module_addhook("player-login");
	module_addhook("player-logout");
	module_addhook("newday-runonce");
	return true;
}

function serverload_dohook($hookname, $args){
	global $session;
	switch($hookname){
		case "player-login":
		case "player-logout":
			serverload_update();
		break;
		case "newday-runonce":
			serverload_update(true);
		break;
	}
	return $args;
}

function serverload_uninstall(){
	return true;
}

function serverload_run(){
	global $session;
	page_header("Current Server Load");
	$load = exec("uptime");
	$load = split("load average:", $load);
	$load = split(", ", $load[1]);
	if (httpget('update')){
		serverload_update(true);
	}
	output("`bCPU Load averages`b`n");
	output("One minute: %s`nFive minutes: %s`nFifteen minutes: %s`n`n",$load[0],$load[1],$load[2]);
	output("Last load registered by game: ".getsetting("systemload_lastload",0)."`n`n");
	addnav("Options");
	addnav("Refresh","runmodule.php?module=serverload");
	addnav("Go to login page","home.php");
	
	$online = 0;
	$noobsonline = 0;
	$loggedintoday = 0;
	$loggedinthisweek = 0;
	$joinedtoday = 0;
	$totalplayers = 0;
	
	$sql = "SELECT regdate, laston, gentimecount, gentime, loggedin FROM " . db_prefix("accounts") . "";
	$result = db_query($sql);
	$rrows = db_num_rows($result);
	for ($i=0;$i<$rrows;$i++){
		$totalplayers++;
		$row = db_fetch_assoc($result);
		$lastontime = strtotime($row['laston']);
		$regtime = strtotime($row['regdate']);
		$curtime = date(U);
		$sincelogon = $curtime - $lastontime;
		$sincereg = $curtime - $regtime;
		if ($sincelogon < getsetting("LOGINTIMEOUT",900) && $row['loggedin'] == 1){
			$online++;
		}
		if ($sincelogon < 86400){
			$loggedintoday++;
		}
		if ($sincelogon < 604800){
			$loggedinthisweek++;
		}
		if ($sincereg < 86400){
			$joinedtoday++;
			if ($sincelogon < getsetting("LOGINTIMEOUT",900) && $row['loggedin'] == 1){
				$noobsonline++;
			}
		}
		$t_time += $row['gentime'];
		$t_count += $row['gentimecount'];
	}
	
	output("`bPlayer Count`b`n");
	output("Total Players: %s`n",$totalplayers);
	output("Joined today: %s`n",$joinedtoday);
	output("Logged in today: %s`n",$loggedintoday);
	output("Logged in this week: %s`n",$loggedinthisweek);
	output("`bOnline players: %s`b`n",$online);
	output("New players online right now: %s`n`n",$noobsonline);
	
	$time = $t_time - get_module_setting("ltime");
	$count = $t_count - get_module_setting("lcount");
	$lastupdate = get_module_setting("lastupdate");

	require_once("lib/datetime.php");
	$timedetails = gametimedetails();
	
	output("`bRecent performance statistics since last login/logout operation`b`n");
	output("Total pages loaded: %s`n",$count);
	output("Total page gen time: %s`n",$time);
	if ($count){
		output("Average page gen time: `b%s`b`n",$time/$count);
		$timesincelast = microtime(true)-$lastupdate;
		$pps = round($count / $timesincelast,3);
		output("Average pages per second since last update: %s`n",$pps);
		//output("Peak pages per second: %s`n",get_module_setting("peakpps"));
		$d_count = $t_count - get_module_setting("ldaycount");
		$dpps = round($d_count / $timedetails['realsecssofartoday'],3);
		output("Average pages per second for this game day: %s`n`n",$dpps);
	}
	output_notl("`n");
	output("`bAll-time statistics`b`nNote: statistics apply to current players only.  Actual all-time numbers are at present unknowable, but are likely to be hundreds or thousands of times larger.  Our server requires many hamster wheels to run.`n");
	$tpgentime = $t_time/$t_count;
	output("All-time page loads: %s`n",number_format($t_count));
	output("All-time page generation time: %s seconds`n",number_format($t_time));
	output("All-time average pagegen time: %s`n",$tpgentime);
	output_notl("`n");
	
	if (get_module_setting("ldaycount")>$t_count){
		set_module_setting("ldaycount",$t_count);
	}
	
	
	//Show player number table
	$sql = "SELECT numplayers, totalpages, totaltime, maxpps FROM " . db_prefix("performance") . "";
	$result = db_query($sql);
	output("`bAverage Page Generation Times by number of online players`b`n");
	rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' width='100%'>");
	rawoutput("<tr class='trhead'><td>Online Players</td><td>Total Count</td><td>Total Time</td><td>Average Time / Page</td><td>PPS</td></tr>");
	$rrows = db_num_rows($result);
	for ($i=0;$i<$rrows;$i++){
		$row = db_fetch_assoc($result);
		if ($row['totalpages']>=1){
			$avg = $row['totaltime']/$row['totalpages'];
			$max = 100;
			$bwidth = round($avg*100);
			$bnonwidth = $max-$bwidth;
			if ($bnonwidth>0){
				$bar = "<table style='border: solid 1px #000000' width='$max' height='7' bgcolor='#333333' cellpadding=0 cellspacing=0><tr><td width='$bwidth' bgcolor='#00ff00'></td><td width='$bnonwidth'></td></tr></table>";
			} else {
				$over = $bwidth-$max;
				$total = $max+$over;
				$bar = "<table style='border: solid 1px #000000' height='7' width='$total' cellpadding=0 cellspacing=0><tr><td width='$max' bgcolor='#990000'></td><td width='$over' bgcolor='#ff0000'></td></tr></table>";
			}
			$improvement = "";
			if ($row['numplayers']==$online){
				rawoutput("<tr class='trhilight'>");
				if (($row['totaltime']/$row['totalpages'])<($time/$count)){
					$improvement = " (increasing)";
				} else {
					$improvement = " (decreasing)";
				}
			} else {
				rawoutput("<tr class='".($i%2?"trdark":"trlight")."'>");
			}
			rawoutput("<td>".$row['numplayers']."</td><td>".number_format($row['totalpages'])."</td><td>".$row['totaltime']."</td><td>".$bar.round($row['totaltime']/$row['totalpages'],4).$improvement."</td><td>".number_format($row['maxpps'],4)."</td></tr>");
		}
	}
	rawoutput("</table>");
	
	//Show time of day table
	$timeslice = floor((time()-strtotime(date("Y-m-d 00:00:00")))/600);
	$sql = "SELECT * FROM " . db_prefix("performancetime") . "";
	$result = db_query($sql);
	$now = date("h:i a");
	output("`n`n`bAverage Page Generation Times by Time of Day (server time)`b`nResults are calculated whenever a player logs in or out, so if it's been a while since a login/logout operation, this data may be slightly inaccurate.  All times are GMT, and the current server time is %s.`n",$now);
	rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' width='100%'>");
	rawoutput("<tr class='trhead'><td>Time Period</td><td>Total Count</td><td>Total Time</td><td>Average Time / Page</td></tr>");
	$rrows = db_num_rows($result);
	for ($i=0;$i<$rrows;$i++){
		$row = db_fetch_assoc($result);
		if ($row['totalpages']>=1){
			$avg = $row['totaltime']/$row['totalpages'];
			$max = 100;
			
			$bwidth = round($avg*100);
			$bnonwidth = $max-$bwidth;
			
			if ($bnonwidth>0){
				$bar = "<table style='border: solid 1px #000000' width='$max' height='7' bgcolor='#333333' cellpadding=0 cellspacing=0><tr><td width='$bwidth' bgcolor='#00ff00'></td><td width='$bnonwidth'></td></tr></table>";
			} else {
				$over = $bwidth-$max;
				$total = $max+$over;
				$bar = "<table style='border: solid 1px #000000' height='7' width='$total' cellpadding=0 cellspacing=0><tr><td width='$max' bgcolor='#990000'></td><td width='$over' bgcolor='#ff0000'></td></tr></table>";
			}
			$improvement_time = "";
			if ($row['timeslice']==$timeslice){
				rawoutput("<tr class='trhilight'>");
				if (($row['totaltime']/$row['totalpages'])<($time/$count)){
					$improvement_time = " (increasing)";
				} else {
					$improvement_time = " (decreasing)";
				}
			} else {
				rawoutput("<tr class='".($i%2?"trdark":"trlight")."'>");
			}
			$timedispstart = strtotime(date("Y-m-d 00:00:00"))+($row['timeslice']*600);
			$timedispend = $timedispstart+600;
			$timedisp = date("h:i a",$timedispstart)." to ".date("h:i a",$timedispstart+600);
			rawoutput("<td>".$timedisp."</td><td>".number_format($row['totalpages'])."</td><td>".$row['totaltime']."</td><td>".$bar.round($row['totaltime']/$row['totalpages'],4).$improvement_time."</td></tr>");
		}
	}
	rawoutput("</table>");

	
	
	page_footer();
}

function serverload_update($day=false){
	$online=0;
	$t_time=0;
	$t_count=0;
	
	//get current ten-minute slice of time
	$timeslice = floor((time()-strtotime(date("Y-m-d 00:00:00")))/600);
	
	$sql = "SELECT laston, gentimecount, gentime FROM " . db_prefix("accounts_everypage") . "";
	$result = db_query($sql);
	$rrows = db_num_rows($result);
	for ($i=0;$i<$rrows;$i++){
		$row = db_fetch_assoc($result);
		$lastontime = strtotime($row['laston']);
		$curtime = date(U);
		$sincelogon = $curtime - $lastontime;
		if ($sincelogon < getsetting("LOGINTIMEOUT",600)){
			$online++;
		}
		$t_time += $row['gentime'];
		$t_count += $row['gentimecount'];
	}
	
	$time = $t_time - get_module_setting("ltime");
	$count = $t_count - get_module_setting("lcount");
	$lastupdate = get_module_setting("lastupdate");	
	
	if ($online){
		//update player number table
		if ($count > 1 && $time > 0){
			$avg = $time/$count;
			$sql = "UPDATE " . db_prefix("performance") . " SET totalpages=totalpages+$count, totaltime=totaltime+$time WHERE numplayers=$online";
			db_query($sql);
			if (!db_affected_rows()){
				$sql = "INSERT LOW_PRIORITY INTO " . db_prefix("performance") . " (numplayers, totalpages, totaltime) VALUES ($online, $count, $time)";
				db_query($sql);
			}
		}
		
		//update time of day table
		if ($count > 1 && $time > 0){
			$avg = $time/$count;
			$sql = "UPDATE " . db_prefix("performancetime") . " SET totalpages=totalpages+$count, totaltime=totaltime+$time WHERE timeslice=$timeslice";
			db_query($sql);
			if (!db_affected_rows()){
				$sql = "INSERT LOW_PRIORITY INTO " . db_prefix("performancetime") . " (timeslice, totalpages, totaltime) VALUES ($timeslice, $count, $time)";
				db_query($sql);
			}
		}
		
		if ($count > 1 && $time > 0){
			$timesincelast = microtime(true)-$lastupdate;
			$pps = round($count / $timesincelast,3);
			//debug($pps,true);
			if ($timesincelast > 0 && $time > 1 && $count > 100){
				$sql = "SELECT maxpps FROM ".db_prefix("performance")." WHERE numplayers=$online";
				$result = db_query($sql);
				$row = db_fetch_assoc($result);
				$oldpps = $row['maxpps'];
				if ($pps > $oldpps){
					$sql = "UPDATE ".db_prefix("performance")." SET maxpps=$pps WHERE numplayers=$online";
					db_query($sql);
				}
			}
		}
		
	}
	
	set_module_setting("ltime",$t_time);
	set_module_setting("lcount",$t_count);
	set_module_setting("lastupdate",microtime(true));

	if (get_module_setting("ldaycount")>$t_count || $day){
		set_module_setting("ldaycount",$t_count);
	}
}

?>