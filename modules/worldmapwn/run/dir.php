<?php
$dir=httpget("dir");
//switch (httpget("dir")){//This sets the users new location
if ($dir=="setloc"){
	$loc=httpget('loc');
	$session['user']['location']=$loc;
} elseif ($dir=="n"){
	$start=$session['user']['location'];
	list($x,$y,$z)=explode(",",$start);
	$locchange=$y-1;
	$newloc=$x.",".$locchange.",".$z;
	$session['user']['location']=$newloc;
} elseif ($dir=="ne"){
	$start=$session['user']['location'];
	list($x,$y,$z)=explode(",",$start);
	$locchangex=$x+1;
	if ($x % 2 ==0){
		$locchangey=$y;
	} else {
		$locchangey=$y-1;
	}
	$newloc=$locchangex.",".$locchangey.",".$z;
	$session['user']['location']=$newloc;
} elseif ($dir=="nw"){
	$start=$session['user']['location'];
	list($x,$y,$z)=explode(",",$start);
	$locchangex=$x+1;
	if ($x % 2 ==0){
		$locchangey=$y;
	} else {
	$locchangey=$y-1;
	}
	$newloc=$locchangex.",".$locchangey.",".$z;
	$session['user']['location']=$newloc;
} elseif ($dir=="s"){
	$start=$session['user']['location'];
	list($x,$y,$z)=explode(",",$start);
	$locchange=$y+1;
	$newloc=$x.",".$locchange.",".$z;
	$session['user']['location']=$newloc;
} elseif ($dir=="se"){
	$start=$session['user']['location'];
	list($x,$y,$z)=explode(",",$start);
	$locchangex=$x+1;
	if ($x % 2 ==0){
	$locchangey=$y+1;
	} else {
	$locchangey=$y;
	}
	$newloc=$locchangex.",".$locchangey.",".$z;
	$session['user']['location']=$newloc;
} elseif ($dir=="sw"){
	$start=$session['user']['location'];
	list($x,$y,$z)=explode(",",$start);
	$locchangex=$x+1;
	if ($x % 2 ==0){
		$locchangey=$y+1;
	} else {
		$locchangey=$y;
	}
	$newloc=$locchangex.",".$locchangey.",".$z;
	$session['user']['location']=$newloc;
} elseif ($dir=="begin"){
	require_once("lib/cityprefs.php");
	$cid = get_cityprefs_cityid("location",$session['user']['location']);
	debug($cid);
	$cname=$session['user']['location'];
	debug($cname);
	$cityloc = get_module_objpref("city",$cid,"worldXYZ");
	debug($cityloc);
	$session['user']['location']=$cityloc;
	require_once("modules/worldmapwn/lib/lib.php");
	worldmapwn_leavecity($cname);
	modulehook("worldmapwn-travel");
}

?>
