<?php

function analytics_getmoduleinfo(){
	$info = array(
		"name"=>"Analytics",
		"category"=>"Administrative",
		"version"=>"2008-07-28",
		"author"=>"Dan Hall, and anyone who wants to join in",
		"download"=>"fix this",
	);
	return $info;
}

function analytics_install(){
	$expchars = array(
		'acctid'=>array('name'=>'acctid', 'type'=>'int(11) unsigned'),
		'dateexpired'=>array('name'=>'dateexpired', 'type'=>'datetime'),
		'datecreated'=>array('name'=>'datecreated', 'type'=>'datetime'),
		'donation'=>array('name'=>'donation', 'default'=>'0', 'type'=>'int(11) unsigned'),
		'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'acctid'),
	);
	require_once("lib/tabledescriptor.php");
	synctable(db_prefix('expchars'), $expchars, true);

	module_addhook("superuser");
	module_addhook("delete_character");
	return true;
}

function analytics_uninstall(){
	return true;
}

function analytics_dohook($hookname,$args){
	switch($hookname){
	case "superuser":
		addnav("Analytics","runmodule.php?module=analytics&op=start");
		break;
	case "delete_character":
		foreach($args['ids'] AS $acctid){
			$sql = "SELECT * FROM ".db_prefix("accounts")." WHERE acctid='$acctid'";
			$result = db_query($sql);
			if (db_num_rows($result) > 0){
				$row = db_fetch_assoc($result);
				debug($row);
				$sql = "INSERT INTO ".db_prefix("expchars")." (acctid,dateexpired,datecreated,donation) VALUES ('" . $row['acctid'] . "','" . date("Y-m-d") . "','" . $row['regdate'] . "','" . $row['donation'] . "')";
				debug($sql);
				db_query($sql);
			}
		}
		break;
	}
	return $args;
}

function analytics_run(){
	global $session;
	addnav("Back to the Grotto","superuser.php");
	addnav("Main Analytics Page","runmodule.php?module=analytics&op=start");
	page_header("Analytics!");
	
	switch(httpget('op')){
		case "start":
			$players = array();
			$totalcurrent = 0;
			
			$dsql = "SELECT * FROM ".db_prefix("paylog");
			$dresult = db_query($dsql);
			$cashdonators = array();
			for ($i=0;$i<db_num_rows($dresult);$i++){
				$row = db_fetch_assoc($dresult);
				$cashdonators[$row['acctid']]+=$row['amount']-$row['txfee'];
			}
			
			$cursql = "SELECT acctid, regdate, donation, donationspent, laston FROM " . db_prefix("accounts") . "";
			$curresult = db_query($cursql);
			for ($i=0;$i<db_num_rows($curresult);$i++){
				$totalcurrent++;
				$currow = db_fetch_assoc($curresult);
				$trimdate = substr($currow['regdate'],0,10);
				$players[$trimdate]['stillalive']['characters'] += 1;
				$players[$trimdate]['stillalive']['donation'] += $currow['donation'];
				if (isset($cashdonators[$currow['acctid']])){
					$players[$trimdate]['cashdonation']+=$cashdonators[$currow['acctid']];
				}
				$comptime = strtotime($currow['laston']);
				$curtime = date(U);
				$sincelogon = $curtime - $comptime;
				if ($sincelogon < 86400){
					$players[$trimdate]['stillalive']['loggedintoday']++;
				}
				if ($sincelogon < 604800){
				$players[$trimdate]['stillalive']['loggedinthisweek']++;
				}
				if ($sincelogon < 1209600){
					$players[$trimdate]['stillalive']['loggedintwoweeks']++;
				}
			}
			
			//get expired accounts, put into an array
			$expsql = "SELECT * FROM " . db_prefix("expchars") . "";
			$expresult = db_query($expsql);
			for ($i=0;$i<db_num_rows($expresult);$i++){
				$exprow = db_fetch_assoc($expresult);
				$trimdate = substr($exprow['datecreated'],0,10);
				$players[$trimdate]['dead']['characters'] += 1;
				$players[$trimdate]['dead']['donation'] += $exprow['donation'];
				if (isset($cashdonators[$exprow['acctid']])){
					$players[$trimdate]['cashdonation']+=$cashdonators[$exprow['acctid']];
				}
			}
			
			ksort($players);
			
			output("`bTotal number of current players: %s`b`n`n",$totalcurrent);
			output("`c`bPlayer Retention`b`c`n");
			rawoutput("<table border='0' cellpadding='2' cellspacing='2'>");
			rawoutput("<tr><td><b>Date</b></td><td><b>Created</b></td><td><b>Active</b></td><td>Logins today / this week / 2 weeks </td><td><b>Retention</b></td><td><b>Donation</b></td><td>Cash Donation</td></tr>");
			
			$class="trlight";
			
			foreach($players as $date=>$vals){
				if ($date != "0000-00-00"){
					$class=(date("W",strtotime($date))%2?"trlight":"trdark");
					$totalcreated = $vals['stillalive']['characters'] + $vals['dead']['characters'];
					$retention = round((($vals['stillalive']['characters']/$totalcreated)*100),2);
					$donation = $vals['stillalive']['donation'] + $vals['dead']['donation'];
					rawoutput("<tr class='$class'><td><b><a href=\"runmodule.php?module=analytics&op=day&day={$date}\">{$date}</a></b></td><td align='left'><img src='images/trans.gif' width='{$totalcreated}' border='1' height='5'>{$totalcreated}</td><td align='left'><img src='images/trans.gif' width='{$vals['stillalive']['characters']}' border='1' height='5'>{$vals['stillalive']['characters']}</td><td align='left'>{$vals['stillalive']['loggedintoday']} / {$vals['stillalive']['loggedinthisweek']} / {$vals['stillalive']['loggedintwoweeks']}</td><td align='left'><img src='images/trans.gif' width='{$retention}' border='1' height='5'>{$retention}%</td><td align='center'>{$donation}</td><td>{$vals['cashdonation']}</td></tr>");
					addnav("","runmodule.php?module=analytics&op=day&day=$date");
				}
			}
			rawoutput("</table>");
			
			//Gauging Regulars
			output("`n`n`c`bRegulars`b`c`n");
			$regsql = "SELECT regdate, dragonkills, laston FROM " . db_prefix("accounts") . "";
			$regresult = db_query($regsql);
			$dkweek = 0;
			$week = 0;
			$fortnight = 0;
			for ($i=0;$i<db_num_rows($regresult);$i++){
				$regrow = db_fetch_assoc($regresult);
				$lastontime = strtotime($regrow['laston']);
				$regtime = strtotime($regrow['regdate']);
				$curtime = date(U);
				$sincelogon = $curtime - $lastontime;
				$sincereg = $curtime - $regtime;
				if ($sincelogon < 604800 && $sincereg > 604800){
					$week++;
				}
				if ($sincelogon < 604800 && $sincereg > 1209600){
					$fortnight++;
				}
				if ($sincelogon < 604800 && $regrow['dragonkills'] > 0){
					$dkweek++;
				}
			}
			
			output("Players with more than one DK who have logged in this week: %s`n",$dkweek);
			output("Players who are more than a week old, and have logged in this week: %s`n",$week);
			output("Players who are more than a fortnight old, and have logged in this week: %s`n",$week);
			
			//Donation Amounts
			$sql = "SELECT donation FROM ".db_prefix("accounts");
			$result = db_query($sql);
			$donations = array();
			for ($i=0;$i<db_num_rows($result);$i++){
				$row=db_fetch_assoc($result);
				$donations[$row['donation']]++;
			}
			ksort($donations);
			output("`n`n`c`bTotal Donation Amounts`b`c`n");
			$i=1;
			rawoutput("<table border='0' cellpadding='2' cellspacing='2'>");
			rawoutput("<tr><td><b>Amount</b></td><td><b>Characters with this many total points or more</b></td></tr>");
			$totalplayers = db_num_rows($result);
			$allplayers = $totalplayers;
			$pct = 100;
			foreach($donations as $amount=>$frequency){
				$class=($i%2?"trlight":"trdark");
				$pct = round((($totalplayers/$allplayers)*100),2);
				rawoutput("<tr class='$class'><td>{$amount}</td><td><img src='images/trans.gif' width='{$totalplayers}' border='1' height='5'>{$totalplayers} ({$pct}%)</td></tr>");
				$i++;
				$totalplayers -= $frequency;
			}
			rawoutput("</table>");
			
			//Donation Amounts Remaining
			$sql = "SELECT donation, donationspent FROM ".db_prefix("accounts");
			$result = db_query($sql);
			$totaldonations = 0;
			$totaldonationspent = 0;
			$donations = array();
			for ($i=0;$i<db_num_rows($result);$i++){
				$row=db_fetch_assoc($result);
				$totaldonations += $row['donation'];
				$totaldonationspent += $row['donationspent'];
				$donationleft = $row['donation'] - $row['donationspent'];
				$donations[$donationleft]++;
			}
			$totaldonationsleft = $totaldonations - $totaldonationspent;
			ksort($donations);
			output("`n`n`c`bDonation Points Remaining`b`c`n");
			$i=1;
			rawoutput("<table border='0' cellpadding='2' cellspacing='2'>");
			rawoutput("<tr><td><b>Amount</b></td><td><b>Characters with this many remaining points or more</b></td></tr>");
			$totalplayers = db_num_rows($result);
			$allplayers = $totalplayers;
			$pct = 100;
			foreach($donations as $amount=>$frequency){
				$class=($i%2?"trlight":"trdark");
				$pct = round((($totalplayers/$allplayers)*100),2);
				rawoutput("<tr class='$class'><td>{$amount}</td><td><img src='images/trans.gif' width='{$totalplayers}' border='1' height='5'>{$totalplayers} ({$pct}%)</td></tr>");
				$i++;
				$totalplayers -= $frequency;
			}
			rawoutput("</table>");
			output("`n`bThere are %s Donator Points in the system from active players, of which %s have been spent, leaving a total of %s left to spend.`b`n`n",$totaldonations,$totaldonationspent,$totaldonationsleft);
		break;
		case "day":
			$date=httpget("day");
			output("Now analysing %s, sorting by most recently logged-on players`n`n",$date);
			
			$players = array();
			
			$cursql = "SELECT name, acctid, level, donation, donationspent, alive, gold, goldinbank, dragonkills, laston, regdate, resurrections, gems FROM " . db_prefix("accounts") . " WHERE regdate LIKE \"%$date%\"";
			$curresult = db_query($cursql);
			for ($i=0;$i<db_num_rows($curresult);$i++){
				$currow = db_fetch_assoc($curresult);
				$players[$currow['laston']] = $currow;
			}
			krsort($players);
			foreach($players AS $player=>$values){
				output("`b%s`b, acctid %s`n",$values['name'],$values['acctid']);
				output("Player is currently %s`n",$values['alive']==0?"dead":"alive");
				output("Player has been defeated %s times`n",$values['resurrections']);
				output("Player is level %s`n",$values['level']);
				output("Player has %s DK's`n",$values['dragonkills']);
				output("Player was last seen %s`n",$values['laston']);
				output("`n`n");
			}
			
		break;
		
	}
	page_footer();
}

?>