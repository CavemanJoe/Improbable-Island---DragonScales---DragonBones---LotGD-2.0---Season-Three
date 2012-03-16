<?php

//FUNCTION: Findcityloc
//This function find the location of a city, based on its id
//
function worldmapwn_findcityloc($cid){
	$sql="SELECT value FROM " .db_prefix("module_objprefs") . " WHERE modulename='worldmapwn' AND objtype='city' AND objid ='$cid'";
	db_query("");
}

//FUNCTION: Findcity
// This function finds a city with the coords $location
//
function worldmapwn_findcity($location=false){
	if ($location==false){
		global $session;
		$location=$session['user']['location'];
	}
	$sql="SELECT objid FROM " .db_prefix("module_objprefs") . " WHERE modulename='worldmapwn' AND objtype='city' AND value='$location'";
	$result=db_query($sql);
	$rows=db_num_rows($result);
	require_once("modules/cityprefs/lib.php");
	//$cnames[];
	for ($i=0;$i<$rows;$i++){
			$row = db_fetch_assoc($result);
			$cnames[]=get_cityprefs_cityname($row);
	}
	
	return $cnames;
}
?>
