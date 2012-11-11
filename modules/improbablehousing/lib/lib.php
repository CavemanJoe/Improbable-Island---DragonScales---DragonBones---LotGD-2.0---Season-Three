<?php
require_once("modules/iitems/lib/lib.php");
function get_building_pref($pref,$hid){
	$sql = "SELECT value FROM ".db_prefix("building_prefs")." WHERE pref='$pref' AND hid='$hid'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	return $row['value'];
}

function set_building_pref($pref,$value,$hid){
	$value = addslashes($value);
	$sql = "INSERT INTO ".db_prefix("building_prefs")." (hid,pref,value) VALUES ('$hid','$pref','$value') ON DUPLICATE KEY UPDATE value = VALUES(value)";
	db_query($sql);
	//debug($sql);
}

function clear_building_pref($pref,$hid){
	$sql = "DELETE FROM ".db_prefix("building_prefs")." WHERE pref='$pref' AND hid='$hid'";
	db_query($sql);
}

function improbablehousing_getkeytype($house,$rid,$acctid=false){
	global $session;
	if ($acctid == false){
		$acctid = $session['user']['acctid'];
	}
	if (get_module_pref("superuser","improbablehousing")){
		return 100;
	}
	if (isset($house['data']['masterkeys'][$acctid]) || $house['ownedby']==$acctid){
		return 100;
	} else {
		return $house['data']['rooms'][$rid]['keys'][$acctid];
	}
}

function improbablehousing_getnearbyhouses($loc){
	global $session;
	//$sql = "SELECT hid,ownedby FROM " . db_prefix("buildings") . " WHERE location = '$loc'";
	$sql = "SELECT * FROM ".db_prefix("buildings")." WHERE location = '$loc'";
	$result = db_query_cached($sql,"housing/housing_location_".$loc);
	//$result = db_query($sql);
	//todo: cache this query, or rather, invalidate the cache properly after staking a claim
	$n = db_num_rows($result);
	//debug($n);
	
	if (!$n){
		return null;
	} else {
		$r = array();
		for ($i=0;$i<$n;$i++){
			$row=db_fetch_assoc($result);
			$house = improbablehousing_gethousedata($row['hid']);
			$r[]=$house;
		}
	}
	return $r;
}

// function improbablehousing_getnearbyhouses($loc){
	// global $session;
	// $sql = "SELECT id,ownedby,data FROM " . db_prefix("improbabledwellings") . " WHERE location = '$loc'";
	// $result = db_query_cached($sql,"housing/housing_location_".$loc);
	// //todo: cache this query, or rather, invalidate the cache properly after staking a claim
	// //$result = db_query($sql);
	// $n = db_num_rows($result);
	
	// if (!$n){
		// return null;
	// } else {
		// $r = array();
		// for ($i=0;$i<$n;$i++){
			// $row=db_fetch_assoc($result);
			// $house = array(
				// "id"=>$row['id'],
				// "ownedby"=>$row['ownedby'],
				// "data"=>unserialize($row['data']),
			// );
			// $r[]=$house;
		// }
	// }
	// return $r;
// }

function improbablehousing_shownearbyhouses($loc){
	global $session;
	$list = improbablehousing_getnearbyhouses($loc);
	$nlist = count($list);
	if ($nlist){
		for ($i=0; $i<$nlist; $i++){
			addnav("Nearby Dwellings");
			$house = $list[$i];
			$house = improbablehousing_canenter_house($house);
			if ($house['canenter']){
				addnav(array("Enter %s",$house['data']['name']),"runmodule.php?module=improbablehousing&op=interior&hid=".$house['id']."&rid=0");
			} else {
				addnav(array("You cannot enter %s",$house['data']['name']),"");
			}
			output_notl("`0%s`0`n`n",$house['data']['desc_exterior']);
		}
	}
	return true;
}

//Run through a bunch of conditions to see whether the player can enter a given house.  Return the whole house array, plus 'canenter' if the player can enter.
function improbablehousing_canenter_house($house){
	global $session;
	//first, we check to see if the house belongs to the player.
	if ($house['ownedby']==$session['user']['acctid']) $house['canenter']=1;
	//check to see if the house is unlocked and public.
	if (!$house['data']['locked']) $house['canenter']=1;
	//Check to see if the player has a front door key or a master key
	if ($house['data']['masterkeys'][$session['user']['acctid']]) $house['canenter']=1;
	if ($house['data']['frontdoorkeys'][$session['user']['acctid']]) $house['canenter']=1;
	if (get_module_pref("superuser","improbablehousing")){
		$house['canenter']=1;
	}
	//todo: keys, modulehook, clan lock, banned players
	return $house;
}

function improbablehousing_canenter_room($house,$rid){
	global $session;
	$canenter = 0;
	//first, we check to see if the house belongs to the player.
	if ($house['ownedby']==$session['user']['acctid']) $canenter=1;
	//check to see if the house is unlocked and public.
	if (!$house['data']['rooms'][$rid]['locked']) $canenter=1;
	//check to see if the player has a deadbolt key or above
	if (improbablehousing_getkeytype($house,$rid)>=30) $canenter=1;
	//check to see if the player has a regular key
	if (improbablehousing_getkeytype($house,$rid)>=10){
		//is the room deadbolted?
		if ($house['data']['rooms'][$rid]['locked']<2){
			$canenter=1;
		}
	}
	if (get_module_pref("superuser","improbablehousing")){
		$canenter=1;
	}
		
	return $canenter;
}

function improbablehousing_list_occupants($house,$rid){
	global $session;
	if (count($house['data']['rooms'][$rid]['occupants'])>1){
		output("Looking around, you see the following people:`n");
		foreach ($house['data']['rooms'][$rid]['occupants'] AS $acctid=>$name){
			if ($acctid!=$session['user']['acctid']){
				output_notl("`0%s`0`n",$name);
			}
		}
		output_notl("`n");
	}
}

function improbablehousing_bottomnavs($house,$rid=false){
$hid = $house['id'];
addnav("Leave");
if ($rid){
	addnav(array("Back to %s",$house['data']['rooms'][$rid]['name']),"runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=$rid");
}
addnav("D?Back to the Dwelling Entrance","runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=0");
addnav("M?Back to the Island Map","runmodule.php?module=improbablehousing&op=exit&hid=$hid");
}

//Is a particular location stakeable?  That is, are there fewer than four houses already here, and is the terrain suitable?
function improbablehousing_stakeable($loc){
	global $session;

	//require_once("modules/iitems/lib/lib.php");

	if (iitems_has_item('housing_stake')){
		//debug("Has Stake");
		list($worldmapX, $worldmapY, $worldmapZ) = explode(",", $loc);
		require_once "modules/worldmapen/lib.php";
		$terrain = worldmapen_getTerrain($worldmapX,$worldmapY,$worldmapZ);
		
		if ($terrain['type']!="River" && $terrain['type']!="Ocean" && $terrain['type']!="Swamp"){
			//For now, no river, ocean or swamp houses
			$list = improbablehousing_getnearbyhouses($loc);
			$nlist = count($list);
			//todo: make this a setting
			$maxhousespersquare = 4;
			if ($nlist<$maxhousespersquare){
				addnav("Set up a new dwelling");
				addnav("Stake your claim!","runmodule.php?module=improbablehousing&op=stakeclaim");
			} else {
				addnav("Set up a new dwelling");
				addnav("You can't set up a new dwelling here because there are already too many houses.");
			}			
		}
	}
}

// function improbablehousing_gethousedata($houseid){
	// $sql = "SELECT ownedby,data,location FROM " . db_prefix("improbabledwellings") . " WHERE id = '$houseid'";
	// $result = db_query($sql);
	// $row=db_fetch_assoc($result);
	// $data=$row['data'];
	// $data=stripslashes($data);
	// $data=unserialize($data);
	// $ret = array(
		// "ownedby"=>$row['ownedby'],
		// "id"=>$houseid,
		// "data"=>$data,
		// "location"=>$row['location'],
	// );
	// //debug($ret);
	// return $ret;
// }

// function improbablehousing_sethousedata_old($house){
	// // debug("Setting house data: ");
	// // debug($house);
	// $id = $house['id'];
	// $data = $house['data'];
	// if (!isset($house['data']['rooms'][0]['size']) && $house['data']['buildjobs'][0]['name']!="Initial Dwelling Construction"){
		// redirect("runmodule.php?module=improbablehousing&op=error&sub=manualfix&hid=".$id);
		// return false;
	// }

	// $data = serialize($data);
	// $data = addslashes($data);
	
	// //check the data can be unserialized
	// $test = stripslashes($data);
	// $test = unserialize($test);
	// if (!isset($test['name'])){
		// redirect("runmodule.php?module=improbablehousing&op=error&sub=unzip&hid=".$id);
		// return false;
	// }
	
	// if (strlen($data)>15000000){
		// redirect("runmodule.php?module=improbablehousing&op=error&sub=overflow&hid=".$id);
		// return false;
	// } else {
		// $sql = "UPDATE ".db_prefix("improbabledwellings")." SET data='$data' WHERE id='$id'";
		// db_query($sql);
		// //now check that the data can be read again
		// $csql = "SELECT data FROM " . db_prefix("improbabledwellings") . " WHERE id = '$id'";
		// //TODO: Cache and properly invalidate this query
		// $cresult = db_query($csql);
		// $crow=db_fetch_assoc($cresult);
		// $cdata=$crow['data'];
		// $errmarg = strlen(stripslashes($cdata)) - strlen(stripslashes($data));
		// debug($errmarg);
		// if ($errmarg > 10 || $errmarg < -10){
			// require_once("lib/systemmail.php");
			// systemmail(1,"Dwelling error!","HouseID: ".$house['id']." Old Data: ".$data." - New Data: ".$cdata);
			// mail("cavemanjoe@gmail.com","Dwelling error plaintext!","HouseID: ".$house['id']." Old Data: ".$data." - New Data: ".$cdata,"From: ".getsetting("gameadminemail","postmaster@localhost"));
		// }
	// }
// }

function improbablehousing_gethousedata($houseid){
	//query our four tables: buildings, building_prefs, rooms, room_prefs - and bring them all together in an array
	
	//NEW VERSION
	$bsql = "SELECT * FROM ".db_prefix("buildings")." WHERE hid='$houseid'";
	$bresult = db_query($bsql);
	$bpsql = "SELECT * FROM ".db_prefix("building_prefs")." WHERE hid='$houseid'";
	$bpresult = db_query($bpsql);
	$rsql = "SELECT * FROM ".db_prefix("rooms")." WHERE hid='$houseid'";
	$rresult = db_query($rsql);
	$rpsql = "SELECT * FROM ".db_prefix("room_prefs")." WHERE hid='$houseid'";
	$rpresult = db_query($rpsql);
	
	$ret = array();
	
	$brow = db_fetch_assoc($bresult);
	$ret['ownedby'] = $brow['ownedby'];
	$ret['id'] = $houseid;
	$ret['location'] = $brow['location'];
	
	$data = array();
	while ($bprow = db_fetch_assoc($bpresult)){
		//debug($bprow);
		$val = stripslashes($bprow['value']);
		$vala = @unserialize($val);
		if ($vala === false && $val !== 'b:0;') {
			// woops, that didn't appear to be anything serialized
			$val = stripslashes($bprow['value']);
		} else {
			$val = $vala;
		}
		$data[$bprow['pref']] = $val;
		//debug($val);
	}
	while ($rrow = db_fetch_assoc($rresult)){
		$data['rooms'][$rrow['rid']] = array();
	}
	while ($rprow = db_fetch_assoc($rpresult)){
		$val = stripslashes($rprow['value']);
		$vala = @unserialize($val);
		if ($vala === false && $val !== 'b:0;') {
			// woops, that didn't appear to be anything serialized
			$val = stripslashes($rprow['value']);
		} else {
			$val = $vala;
		}
		$data['rooms'][$rprow['rid']][$rprow['pref']] = $val;
		//debug($val);
	}
	
	$ret['data'] = $data;
	
	//debug($ret);
	return $ret;
}

function improbablehousing_sethousedata($house){
	//Anything not contained in the $rooms array gets turned into building prefs
	//Anything contained within a room gets placed into room prefs
	//In all cases, check first whether or not the data is an array so it can be serialized before adding back to the database
	//We'll run the old sethousedata code alongside the new sethousedata code, so that there are two copies of all Dwellings - one old, one new - and then once we've verified that the new system works and can retrieve data without issue, we'll switch the get_house_data function to the new version.
	//================================OLD VERSION==============================
	// debug("Setting house data: ");
	// debug($house);
	// $id = $house['id'];
	// $data = $house['data'];
	// if (!isset($house['data']['rooms'][0]['size']) && $house['data']['buildjobs'][0]['name']!="Initial Dwelling Construction"){
		// redirect("runmodule.php?module=improbablehousing&op=error&sub=manualfix&hid=".$id);
		// return false;
	// }

	// $data = serialize($data);
	// $data = addslashes($data);
	
	// //check the data can be unserialized
	// $test = stripslashes($data);
	// $test = unserialize($test);
	// if (!isset($test['name'])){
		// redirect("runmodule.php?module=improbablehousing&op=error&sub=unzip&hid=".$id);
		// return false;
	// }
	
	// if (strlen($data)>15000000){
		// redirect("runmodule.php?module=improbablehousing&op=error&sub=overflow&hid=".$id);
		// return false;
	// } else {
		// $sql = "UPDATE ".db_prefix("improbabledwellings")." SET data='$data' WHERE id='$id'";
		// db_query($sql);
		// //now check that the data can be read again
		// $csql = "SELECT data FROM " . db_prefix("improbabledwellings") . " WHERE id = '$id'";
		// //TODO: Cache and properly invalidate this query
		// $cresult = db_query($csql);
		// $crow=db_fetch_assoc($cresult);
		// $cdata=$crow['data'];
		// $errmarg = strlen(stripslashes($cdata)) - strlen(stripslashes($data));
		// // debug($errmarg);
		// if ($errmarg > 10 || $errmarg < -10){
			// require_once("lib/systemmail.php");
			// systemmail(1,"Dwelling error!","HouseID: ".$house['id']." Old Data: ".$data." - New Data: ".$cdata);
			// mail("cavemanjoe@gmail.com","Dwelling error plaintext!","HouseID: ".$house['id']." Old Data: ".$data." - New Data: ".$cdata,"From: ".getsetting("gameadminemail","postmaster@localhost"));
		// }
	// }
	//================================NEW VERSION==============================
	// debug("Setting house data: ");
	// debug($house);
	$hid = $house['id'];
	$ownedby = $house['ownedby'];
	$location = $house['location'];
	$bsql = "REPLACE INTO ".db_prefix("buildings")." (hid,ownedby,location) VALUES ('$hid','$ownedby','$location')";
	db_query($bsql);
	//Set rooms (handle new rooms etc)
	if (count($house['data']['rooms'])){
		$rsql = "INSERT IGNORE INTO " . db_prefix("rooms") . " (hid,rid) VALUES ";
		$rpsql = "INSERT INTO " . db_prefix("room_prefs") . " (hid,rid,pref,value) VALUES ";
		foreach($house['data']['rooms'] AS $rid=>$prefs){
			$rsql .= "('$hid','$rid'),";
			foreach ($prefs AS $pref=>$val){
				if (is_array($val)){
					$val = serialize($val);
				}
				$val = addslashes($val);
				$rpsql .= "('$hid','$rid','$pref','$val'),";
			}
		}
		$rsql = substr_replace($rsql,"",-1);
		$rpsql = substr_replace($rpsql,"",-1);
		$rpsql .= " ON DUPLICATE KEY UPDATE value = VALUES(value)";
		db_query($rsql);
		db_query($rpsql);
	}
	$bpsql = "INSERT INTO " . db_prefix("building_prefs") . " (hid,pref,value) VALUES ";
	foreach($house['data'] AS $pref=>$val){
		// debug($pref);
		if ($pref=="rooms"){
			// debug("skipping");
			continue;
		} else {
			if (is_array($val)){
				$val = serialize($val);
			}
			$val = addslashes($val);
			$bpsql .= "('$hid','$pref','$val'),";
		}
	}
	
	if (!isset($house['data']['dec'])){
		$erasedec = "DELETE FROM ".db_prefix("building_prefs")." WHERE pref = 'buildjobs' AND hid = '$hid'";
		db_query($erasedec);
	}
	
	$bpsql = substr_replace($bpsql,"",-1);
	$bpsql .= " ON DUPLICATE KEY UPDATE value = VALUES(value)";
	db_query($bpsql);
}

function improbablehousing_sleeplinks($house, $rid){
	global $session;
	$hid = $house['id'];
	if (!$house['data']['rooms'][$rid]['blocksleep']){
		$sleepslots = $house['data']['rooms'][$rid]['sleepslots'];
		if (count($sleepslots)){
			foreach($sleepslots AS $slot=>$vals){
				if ($vals['occupier']){
					$acctid = $vals['occupier'];
					$sql = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid='$acctid'";
					$result = db_query_cached($sql,"playernames/playername_".$acctid);
					$row = db_fetch_assoc($result);
					addnav("Sleeping spaces");
					addnav(array("%s occupied by %s`0",$vals['name'],$row['name']),"");
					//do kickout navs if the player is the house owner
					if (improbablehousing_getkeytype($house,$rid)>=100){
						addnav("Kick out Sleepers");
						addnav(array("Kick out %s`0",$row['name']),"runmodule.php?module=improbablehousing&op=sleep&sub=kickout&hid=".$house['id']."&rid=".$rid."&slot=".$slot);
					}
				} else {
					//it's an empty slot, shall we kip in it?
					addnav("Sleeping spaces");
					addnav(array("%s available`0",$vals['name']),"runmodule.php?module=improbablehousing&op=sleep&sub=start&hid=$hid&rid=$rid&slot=$slot");
				}
			}
		}
	}
}

function improbablehousing_itemlinks($house,$rid){
	$hid = $house['id'];
	$items = load_inventory("dwelling_".$hid."_".$rid,true);
	$roomitems = array();
	if (count($items) > 0){
		foreach($items AS $itemid=>$prefs){
			$roomitems[$prefs['roomslot']] = $itemid;
		}
	}
	$numslots = $house['rooms'][$rid]['itemslots'];
	for ($i=0; $i<=$numslots; $i++){
		if ($roomitems[$numslots]){
			//there's an item here - show its links
			$itemid = $roomitems[$numslots];
			//when the item is occupied or otherwise unusable, change its link text pref and clear its link pref
			addnav("Furniture");
			if (!get_item_pref("dwellings_link",$itemid)){
				addnav(array("%s",get_item_pref("dwellings_linktext",$itemid)),"runmodule.php?module=improbablehousing&op=useitem&hid=$hid&rid=$rid&itemid=$itemid");
			} else if (get_item_pref("dwellings_linktext",$itemid)){
				addnav(array("%s",get_item_pref("dwellings_linktext",$itemid)),"");
			}
			//show a link to pick up the item
			if (improbablehousing_getkeytype($house,$rid)>=100 && !$vals['occupier']){
				addnav(array("Pick up %s",get_item_pref("verbosename",$roomitems[$slot])),"runmodule.php?module=improbablehousing&op=pickupitem&hid=$hid&rid=$rid&item=".$roomitems[$slot]);
			}
		} else {
			//there isn't an item here - show links for standard sleeping slots and links to place an item here
			//sigh...
			//TODO
		}
	}
}

function improbablehousing_show_decorating_management_navs($house){
	//This is the function that allows a player to write their own custom description.
	$hid = $house['id'];
	$rooms = $house['data']['rooms'];
	addnav("Decoration Management");
	foreach($rooms AS $key=>$vals){
		addnav(array("Manage %s",$vals['name']),"runmodule.php?module=improbablehousing&op=decorate&subop=start&hid=$hid&rid=$key");
	}
	addnav("Manage exterior decoration","runmodule.php?module=improbablehousing&op=decorate&subop=extdesc&hid=$hid");
}

function improbablehousing_show_decorating_jobs($house){
	//This shows links to help decorate a room
	require_once "modules/staminasystem/lib/lib.php";
	
	if (iitems_has_item('toolbox_decorating')){
		$hasitem = 1;
	}
	if (get_stamina()==100){
		$hasstamina = 1;
	}
	$hid = $house['id'];
	
	$rooms = $house['data']['rooms'];
	if (mass_suspend_stamina_buffs("wlimit")){
		$restorebuffs = true;
		//debug("suspended");
	}
	$cost = stamina_getdisplaycost("Decorating");
	$show = 0;
	foreach($rooms AS $key=>$vals){
		if (isset($vals['dec']) && $vals['dec']['req']>$vals['dec']['done']){
			if ($hasitem && $hasstamina){
			addnav("Painting and Decorating");
			addnav(array("Decorate %s (`Q%s%%`0)",$vals['name'],$cost),"runmodule.php?module=improbablehousing&op=decorate&subop=decorate&hid=$hid&rid=$key");
			} else if ($hasitem){
				addnav("Painting and Decorating");
				addnav("You're too tired to do any sprucing up right now.","");
			} else if ($hasstamina){
				addnav("Painting and Decorating");
				addnav("You don't have the right gear to do any decorating.","");
			}
		}
	}
	if (isset($house['data']['dec']) && $house['data']['dec']['req']>$house['data']['dec']['done']){
		if ($hasitem && $hasstamina){
			addnav("Painting and Decorating");
			addnav(array("Decorate the dwelling's exterior (`Q%s%%`0)",$cost),"runmodule.php?module=improbablehousing&op=decorate&subop=decorate_exterior&hid=$hid");
		} else if ($hasitem){
			addnav("Painting and Decorating");
			addnav("You're too tired to do any sprucing up right now.","");
		} else if ($hasstamina){
			addnav("Painting and Decorating");
			addnav("You don't have the right gear to do any decorating.","");
		}
	}
	
	if ($restorebuffs){
		restore_all_stamina_buffs();
	}	
}

function improbablehousing_new_build_jobs($house,$rid){
	//show new potential build jobs, including room expansion and new room addition
		$hid = $house['id'];
		addnav("New Build Jobs");
		if (isset($rid)){
			//make sure that there isn't already a job going on to increase room size
			// if (count($house['data']['buildjobs'])>0){
				// foreach ($house['data']['buildjobs'] AS $job => $data){
					// if (count($data['completioneffects']['rooms']) > 0){
						// foreach ($data['completioneffects']['rooms'] AS $jobroom => $rdata){
							// if ($jobroom == $rid && $rdata['deltas']['size']){
								// $blocksizeincrease = true;
							// }
						// }
					// }
				// }
			// }
			if (!$blocksizeincrease){
				addnav("Increase the size of this room","runmodule.php?module=improbablehousing&op=buildjobs&hid=$hid&sub=increasesize&rid=".$rid);
			}
		addnav("Add a new Room","runmodule.php?module=improbablehousing&op=buildjobs&hid=$hid&sub=newroom&rid=".$rid);
	}
}

function improbablehousing_show_build_jobs($house){
	//run through the build jobs and show percentage to completion, along with navs to use the relevant item and its associated Stamina cost.
	//For easyness, don't allow the player to build if they're in Amber stamina
	$jobs = $house['data']['buildjobs'];
	require_once "modules/staminasystem/lib/lib.php";
	
	if (mass_suspend_stamina_buffs("wlimit")){
		$restorebuffs = true;
	}
	
	$storestock = $house['data']['store'];
	$displayjobs=array();
	if (count($jobs)){
		foreach($jobs AS $key=>$vals){
			if (!isset($displayjobs[$vals['name']])){
				addnav(array("%s",$vals['name']));
				foreach($vals['jobs'] AS $skey=>$svals){
					$cando=1;
					$displayjobs[$vals['name']][$svals['name']]['req']=$svals['req'];
					$displayjobs[$vals['name']][$svals['name']]['done']=$svals['done'];
					//check completion
					if ($svals['completed']){
						$cando = 0;
					}
					//Check Stamina
					if (get_stamina()<100){
						$cando = 0;
						$nostam = 1;
						$displayjobs[$vals['name']][$svals['name']]['nostamina']=true;
					}
					$store = array();
					$player = array();
					foreach($svals['iitems'] AS $ikey=>$iitem){
						$store[$ikey] = $house['data']['store'][$ikey];
						if (iitems_has_item($ikey)){
							$player[$ikey] = 1;
						}
					}
					//check that each item required is present in either the store or the player's inventory... this could be neater...
					$svals['useplayer'] = true;
					foreach($svals['iitems'] AS $ikey=>$iitem){
						if (!$store[$ikey] && !$player[$ikey]){
							$cando = 0;
							$displayjobs[$vals['name']][$svals['name']]['noitem']=true;
						}
						if ($store[$ikey] > 0){
							$svals['usestore']=true;
						}
						if (!$player[$ikey]){
							$svals['useplayer']=false;
						}
					}
					// debug($store);
					// debug($player);
					
					if ($cando){
						$scost=0;
						foreach($svals['actions'] AS $akey=>$action){
							$scost += stamina_getdisplaycost($akey);
						}
						if ($svals['useplayer']){
							addnav(array("%s (`Q%s%%`0)",$svals['name'],$scost),"runmodule.php?module=improbablehousing&op=build&hid=".$house['id']."&job=".$key."&subjob=".$skey);
						}
						if ($svals['usestore']){
							addnav(array("%s (using store) (`Q%s%%`0)",$svals['name'],$scost),"runmodule.php?module=improbablehousing&op=build&hid=".$house['id']."&job=".$key."&subjob=".$skey."&store=true");
						}
					}
				}
			}
		}
	}
	if (count($displayjobs)){
		output("`b`0Current Construction Jobs`b`n");
		foreach($displayjobs AS $job=>$sub){
			output_notl("`0->%s`n",$job);
			foreach ($sub AS $sjob=>$vals){
				$jobout="<table><tr><td>";
				require_once "lib/bars.php";
				$bar = fadebar($vals['done'],$vals['req']);
				if ($vals['completed']){
					$jobout.="-->".$sjob." </td><td>".$bar."</td> ";
				} else {
					$jobout.="-->".$sjob." </td><td>".$bar."</td><td> ".$vals['done']."/".$vals['req']." ";
					if ($vals['nostamina']){
						$jobout.="(you don't have enough Stamina to do this job) ";
					}
					if ($vals['noitem']){
						$jobout.="(you don't have all the items you need to do this job) ";
					}
				}
				$jobout.="</td></tr></table>";
				rawoutput($jobout);
			}
			output_notl("`n");
		}
		output_notl("`n");
	}
	if ($nostam){
		output("You have the brains to know that doing construction work when you're anything but wide awake is an idea so bad you're not even willing to entertain it.`n`n");
	}
	
	if ($restorebuffs){
		restore_all_stamina_buffs();
	}
}

?>
