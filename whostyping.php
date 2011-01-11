<?php

global $session;
define("ALLOW_ANONYMOUS",true);
define("OVERRIDE_FORCED_NAV",true);
require_once "common.php";

// $start = getmicrotime(true);



$section = $_REQUEST['section'];
$updateplayer = $_REQUEST['updateplayer'];
$name = addslashes($session['user']['name']);
$now = time();

$session['iterations'] += 1;
//echo($updateplayer);

/*

require_once("lib/tabledescriptor.php");
$whostyping = array(
	'time'=>array('name'=>'time', 'type'=>'int(11) unsigned'),
	'name'=>array('name'=>'name', 'type'=>'varchar(255)'),
	'section'=>array('name'=>'section', 'type'=>'varchar(255)'),
	'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'name'),
);
synctable(db_prefix('whostyping'), $whostyping, true);


Pass every two seconds.
	JS: Check to see if the number of characters in the player's text box has changed.
		If the number of characters has changed,
			and it isn't zero:
				Call this file with the updateplayer flag true.
				PHP: store their entry in the typing table and update the time in which they were typing.
			and it is zero:
				Call this file with the updateplayer flag false.
				PHP: erase their entry from the typing table.
				
	
	JS: Call this file with the section and the number of characters entered in the box.
	PHP: Check time to see when other players last typed.  If it's been longer than ten seconds, erase the entry.
	JS: Check to see if the number of characters in the player's text box has changed.
		If the number of characters has changed,
			and it isn't zero:
				PHP: store their entry in the typing table and update the time in which they were typing.
			and it is zero:
				PHP: erase their entry from the typing table.
	JS: Store (in javascript memory) the number of characters entered in the player's text box.
	PHP: Retrieve and send back to JS (just echo it) who's typing.
	
Javascript needs to call this file with the section and the number of characters entered.
*/

//update time
if ($updateplayer){
	$sql = "INSERT INTO ".db_prefix("whostyping")." (time,name,section) VALUES ('$now','$name','$section') ON DUPLICATE KEY UPDATE time = VALUES(time), section = VALUES(section)";
	db_query($sql);
	//echo("Updating player");
}

//retrieve, deleting as appropriate
$sql = "SELECT * FROM ".db_prefix("whostyping")." WHERE section='$section'";
$result = db_query($sql);
$now = time();
$disp = array();
while ($row = db_fetch_assoc($result)){
	$disp[]=$row['name'];
}

//erase old entries
$old = $now - 2;
$delsql = "DELETE FROM ".db_prefix("whostyping")." WHERE time < $old";
db_query($delsql);

//display
foreach($disp AS $name){
	$encodedname = appoencode($name."`0 takes a breath...`n");
	echo($encodedname);
}

$end = getmicrotime(true);
// $total = $end - $start;
// echo("CavemanJoe is debugging in the middle of the night, and this cycle of whostyping.php took this long: ");
// echo($total);
//echo("test!");
//echo($session['iterations']);

?>