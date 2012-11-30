<?php
//require_once("modules/worldmapwn/config/terrain.php");

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
/*	//this has been fully debugged and works as of 16/03/12 */
	if ($location==false){
		global $session;
		$location=$session['user']['location'];
	}
	$sql="SELECT objid FROM " .db_prefix("module_objprefs") . " WHERE modulename='worldmapwn' AND objtype='city' AND value='$location'";
	$result=db_query($sql);
	$rows=db_num_rows($result);
	require_once("modules/cityprefs/lib.php");
	//debug("rows of citys from findcity is $rows");
	//$cnames[];
	$i=0;
	while ($row = db_fetch_assoc($result)) {
			
			//debug("row fetched is");
			//if ($row==null)debug("null");
			$cnames[$i]["name"]=get_cityprefs_cityname("cityid",$row["objid"]);
			$cnames[$i]["id"]=$row["objid"];
			$i++;
	}
	return $cnames;
}

function worldmapwn_leavecity($cname){
	$outmess=e_rand(1,5);
	/*switch ($outmess){
		case 1:output("`b`&The gates of %s close behind you. A shiver runs down your back as you face the wilderness around you.`0`b",$cname);break;
		case 2:output("`b`&The gates of %s close behind you. You're all alone now...`0`b",$cname);break;
		case 3:output("`b`&The gates of %s close behind you. The sound of the wilderness settles in around you as you think to yourself what evil must lurk within.`0`b",$cname);break;
		case 4:output("`b`&The gates of %s close behind you. Perhaps you should go back in...`0`b",$cname);break;
		case 5:output("`b`&The gates of %s close behind you. A howling noise bellows from deep within the forest.  You hear the guards from the other side of the gates yell \"Good Luck!\" and what sounds like \"they'll never make it.`0`b",$cname);break;

	}*/
	if ($outmess==1){
		output("`b`&The gates of %s close behind you. A shiver runs down your back as you face the wilderness around you.`0`b",$cname);
	} elseif($outmess==2){
		output("`b`&The gates of %s close behind you. You're all alone now...`0`b",$cname);
	} elseif($outmess==3){
		output("`b`&The gates of %s close behind you. The sound of the wilderness settles in around you as you think to yourself what evil must lurk within.`0`b",$cname);
	} elseif($outmess==4){
		output("`b`&The gates of %s close behind you. Perhaps you should go back in...`0`b",$cname);
	} elseif($outmess==5){
		output("`b`&The gates of %s close behind you. A howling noise bellows from deep within the forest.  You hear the guards from the other side of the gates yell \"Good Luck!\" and what sounds like \"they'll never make it.`0`b",$cname);
}
}
?>
