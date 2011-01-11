<?php

function serverloadperpage_getmoduleinfo(){
	$info = array(
		"name"=>"Server Load - Per Page",
		"author"=>"Dan Hall",
		"version"=>"2009-10-26",
		"category"=>"Administrative",
		"download"=>"",
		"allowanonymous"=>"true",
		"settings"=>array(
			"time"=>"Total pagegen time since last update,float|0",
			"count"=>"Total page load count since last update,int|0",
			"avg"=>"Average page gen time since last update,float|0",
			"ltime"=>"Total pagegen time at last update,float|0",
			"lcount"=>"Total page load count at last update,int|0",
			"lnoobtime"=>"Total pagegen time among new players at last update,float|0",
			"lnoobcount"=>"Total page load count among new players at last update,int|0",
		),
	);
	
	return $info;
}

function serverloadperpage_install(){
	$performancepage = array(
		'id'=>array('name'=>'id', 'type'=>'int(11) unsigned', 'extra'=>'auto_increment'),
		'page'=>array('name'=>'page', 'type'=>'text'),
		'totaltime'=>array('name'=>'totaltime', 'type'=>'double unsigned'),
		'totalpages'=>array('name'=>'totalpages', 'type'=>'int(11) unsigned'),
		'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'id'),
	);
	require_once("lib/tabledescriptor.php");
	synctable(db_prefix('performancepage'), $performancepage, true);
	module_addhook("footer-performance");
	return true;
}

function serverloadperpage_dohook($hookname, $args){
	global $session;
	switch($hookname){
		case "footer-performance":
			$and = strpos($args['request'],"&c=");
			if ($and){
				$rparts = explode('&c=',$args['request']);
			} else {
				$rparts = explode('?c=',$args['request']);
			}
			$playerpage = substr($rparts[0],0,100);
			$playerpage = mysql_real_escape_string($playerpage);
			$time = $args['gentime'];
			$sql = "UPDATE " . db_prefix("performancepage") . " SET totalpages=totalpages+1, totaltime=totaltime+'$time' WHERE page = '$playerpage'";
			db_query($sql);
			if (!db_affected_rows()){
				$sql = "INSERT LOW_PRIORITY INTO " . db_prefix("performancepage") . " (page, totalpages, totaltime) VALUES ('$playerpage', '1', '$time')";
				db_query($sql);
			}
		break;
	}
	return $args;
}

function serverloadperpage_uninstall(){
	return true;
}

function serverloadperpage_run(){
	global $session;
	page_header("Server Load by Page Execution Times");
	
	//Show player number table
	$sql = "SELECT * FROM " . db_prefix("performancepage") . " ORDER BY totalpages DESC";
	$result = db_query($sql);
	output("`bAverage Page Generation Times by script request`b`n");
	rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' width='100%'>");
	rawoutput("<tr class='trhead'><td>URL</td><td>Total Count</td><td>Total Time</td><td>Average Time / Page</td></tr>");
	for ($i=0;$i<db_num_rows($result);$i++){
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
			rawoutput("<tr class='".($i%2?"trdark":"trlight")."'>");
			rawoutput("<td>".$row['page']."</td><td>".number_format($row['totalpages'])."</td><td>".$row['totaltime']."</td><td>".$bar.round($row['totaltime']/$row['totalpages'],4)."</td></tr>");
		}
	}
	rawoutput("</table>");
	
	page_footer();
}

?>