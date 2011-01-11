<?php

//start new stuff

/*
=======================================================
GET COMPATIBLE ACCESSORIES
$item['mountaccessory'] must be set.
$item['formount'] can be a comma-separated list of Mounts for which this can be an accessory, or it can be "all".

EXAMPLE:
$item['mountaccessory']=true;
$item['formount']="1,7,27"; //can be used with mountids 1, 7, and 27
=======================================================
*/
function iitems_mountaccessories_get_compatible_accessories($mountid=false){
	$ret = array();
	global $session;
	if (!$mountid) $mountid = $session['user']['hashorse'];
	if (!$mountid) return $ret;
	require_once "modules/iitems/lib/lib.php";
	$allitems = iitems_get_all_item_details();
	$mountaccessories = array();
	//select all iitems that are Mount Accessories
	foreach ($allitems AS $item => $details){
		if ($details['mountaccessory']) $mountaccessories[$item]=$details;
	}
	//check to see if there are no available mount accessories before we go further
	if (!count($mountaccessories)) return $ret;
	//select all Mount Accessories that are appropriate for this Mount
	foreach ($mountaccessories AS $item => $details){
		//is the player in the right place?
		if ($details['stablelocation'] == $session['user']['location'] || $details['stablelocation'] == "all"){
			if ($details['formount']=="all"){
				$ret[$item]=$details;
			} else {
				$compatibilitylist = explode(",",$details['formount']);
				foreach($compatibilitylist AS $cmount){
					if ($cmount==$mountid) $ret[$item]=$details;
				}
			}
		}
	}
	return $ret;
}

function iitems_mountaccessories_get_player_accessories($acctid=false){
	global $session;
	if (!$acctid) $acctid = $session['user']['acctid'];
	require_once "modules/iitems/lib/lib.php";
	$ret = array();
	
	$inv = iitems_get_player_inventory($acctid);
	//debug($inv);
	if (!count($inv)) return $ret;
	foreach ($inv AS $item => $details){
		if ($details['mountaccessory']) $ret[$details['itemid']]=$details;
	}
	return $ret;
}

//strips all Mount Accessories that are no longer appropriate, and refunds a proportion of the gemcost (and gold if specified), returns an array with the refunded gems and gold
function iitems_mountaccessories_strip_player_accessories($payback=0.6,$returngold=false){
	global $session;
	require_once "modules/iitems/lib/lib.php";
	$ret = array();
	
	$inv = iitems_get_player_inventory();
	$allitems = iitems_get_all_item_details();
	if (!count($inv)) return $ret;
	foreach ($inv AS $item => $details){
		$master = $allitems[$details['itemid']];
		if ($master['mountaccessory']){
			$safe=0;
			$compatibilitylist = explode(",",$master['formount']);
			foreach($compatibilitylist AS $cmount){
				if ($cmount==$session['user']['hashorse']) $safe=1;
			}
			if (!$safe){
				//remove the item, pay back the money
				$gold = ceil($master['goldcost']*$payback);
				$gems = ceil($master['gemcost']*$payback);
				$session['user']['gems'] += $gems;
				$ret['gems'] += $gems;
				if ($returngold){
					$session['user']['gold'] += $gold;
					$ret['gold'] += $gold;
				}
				iitems_discard_item($item);
			}
		}
	}
	return $ret;
}

// function iitems_mountaccessories_is_compatible($mountid=false){
	// global $session;
	// if (!$mountid) $mountid = $session['user']['hashorse'];
	// if (!$mountid) return $ret;
// }

//end new stuff

?>