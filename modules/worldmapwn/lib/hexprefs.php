<?php

function get_worldmapwn_hexmodule($lookup,$value,$player=false){
	if($player>0){
		$sql1="select location from ".db_prefix("accounts")." where acctid=$player";
		$res1=db_query($sql1);
		$row1=db_fetch_assoc($res1);
		$lookup="cityname";
		$value=$row1['location'];
	}
	
	if($lookup=='hexid'){$where="hexid=$value";}
	else{$where="hexcode='".addslashes($value)."'";}

	$sql="select module from ".db_prefix("hexprefs")." where $where";
	$res=db_query($sql);
	$row=db_fetch_assoc($res);
	return $row['module'];
}

function get_worldmapwn_hexid($lookup,$value,$player=false){
	if($player>0){
		$sql1="select location from ".db_prefix("accounts")." where acctid=$player";
		$res1=db_query($sql1);
		$row1=db_fetch_assoc($res1);
		$lookup="hexcoord";
		$value=$row1['location'];
	}
	
	if($lookup=='module'){$where="module='".addslashes($value)."'";}
	else{$where="hexcoord='".addslashes($value)."'";}

	$sql="select hexid from ".db_prefix("hexprefs")." where $where";
	$res=db_query_cached($sql,"hexid_".$value);
	$row=db_fetch_assoc($res);
	return $row['cityid'];
}

function get_worldmapwn_cityname($lookup,$value,$player=false){
	if($player>0){
		$sql1="select location from ".db_prefix("accounts")." where acctid=$player";
		$res1=db_query($sql1);
		$row1=db_fetch_assoc($res1);
		return $row1['location'];
	}
	
	if($lookup=='module'){$where="module='".addslashes($value)."'";}
	else{$where="cityid=$value";}

	$sql="select cityname from ".db_prefix("hexprefs")." where $where";
	$res=db_query_cached($sql,"hexid_".$value);
	$row=db_fetch_assoc($res);
	return $row['cityname'];
}


?>
