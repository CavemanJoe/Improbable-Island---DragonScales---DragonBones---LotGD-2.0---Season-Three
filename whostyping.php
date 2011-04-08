<?php

global $session;
define("ALLOW_ANONYMOUS",true);
define("OVERRIDE_FORCED_NAV",true);
require_once "common.php";

//$start = getmicrotime(true);



$section = $_REQUEST['section'];
$updateplayer = $_REQUEST['updateplayer'];
$name = addslashes($session['user']['name']);
$now = time();

$session['iterations'] += 1;

$old = $now - 2;

//update time
if ($updateplayer){
	$sql = "INSERT INTO ".db_prefix("whostyping")." (time,name,section) VALUES ('$now','$name','$section') ON DUPLICATE KEY UPDATE time = VALUES(time), section = VALUES(section)";
	db_query($sql);
	//echo("Updating player");
	//erase old entries once per ten seconds
	$lastdigit = substr($now,-1);
	if ($lastdigit=="0"){
		$delsql = "DELETE FROM ".db_prefix("whostyping")." WHERE time < $old";
		db_query($delsql);
	}
}

//retrieve, deleting as appropriate
$sql = "SELECT * FROM ".db_prefix("whostyping")." WHERE section='$section' AND time >= $old";
$result = db_query($sql);
$disp = array();
while ($row = db_fetch_assoc($result)){
	$disp[]=$row['name'];
}

db_free_result($result);

//display
foreach($disp AS $name){
	$encodedname = appoencode($name."`0 takes a breath...`n");
	echo($encodedname);
}

unset($disp);

// $end = getmicrotime(true);
// $total = $end - $start;
//echo("CavemanJoe is debugging in the middle of the night, and this cycle of whostyping.php took this long: ");
//echo($total);
//echo("test!");
//echo($session['iterations']);

exit();

?>