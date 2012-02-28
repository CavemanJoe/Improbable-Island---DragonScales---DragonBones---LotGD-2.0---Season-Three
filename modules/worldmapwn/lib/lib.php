<?php

function worldmapwn_findxyz($location){
	$sql="";

}

function worldmapwn_findcity($location=false){
	if ($location==false){
		global $session;
		$location=$session['user']['location'];
	}
	$sql="SELECT objcity FROM " .db_prefix("module_objprefs") . " WHERE modulename='worldmapwn' AND objtype='city' AND value='$location'";
	$result=db_query($sql);
	$rows=db_num_rows($result);
	require_once("modules/cityprefs/lib.php");
	for ($i=0;$rows;$i++){
			$row = db_fetch_assoc($result);
			$cnames[$i]=get_cityprefs_cityname($row);
	}
	return cnames;
}
?>
