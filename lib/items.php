<?php

/*

require_once("lib/tabledescriptor.php");
$items_player = array(
	'id'=>array('name'=>'id', 'type'=>'int(11) unsigned', 'extra'=>'auto_increment'),
	'item'=>array('name'=>'item', 'type'=>'varchar(255)'),
	'owner'=>array('name'=>'owner', 'type'=>'varchar(255)'),
	'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'id,owner'),
);

//why is the owner a varchar column rather than an int?
//So that items can "belong" to NPC's, places on the World Map and so on

$items_prefs = array(
	'id'=>array('name'=>'id', 'type'=>'int(11) unsigned'),
	'setting'=>array('name'=>'setting', 'type'=>'varchar(255)'),
	'value'=>array('name'=>'value', 'type'=>'text'),
	'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'id,setting'),
);

$items_settings = array(
	'item'=>array('name'=>'item', 'type'=>'varchar(255)'),
	'setting'=>array('name'=>'setting', 'type'=>'varchar(255)'),
	'value'=>array('name'=>'value', 'type'=>'text'),
	'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'item,setting'),
);

synctable(db_prefix('items_player'), $items_player, true);
synctable(db_prefix('items_settings'), $items_settings, true);
synctable(db_prefix('items_prefs'), $items_prefs, true);

*/

/*

=============================================
We do not write the entire $inventory array back at the end of each page load.
We just change values that have been defined by set_item_pref().
The $inventory array is laid out like this:

Inventory
	42
		item = shortsword
		name = Short Sword
		attack = 10
		image = sword.png
		location = lefthand
	97
		item = throwingknife
		name = Throwing Knife
		attack = 2
		image = throwingknife.png
		location = bandolier
	116
		item = throwingknife
		name = Throwing Knife
		attack = 2
		image = throwingknife.png
		location = bandolier

=============================================
*/

function items_delete_character($acctid){
	$sql1 = "SELECT id FROM ".db_prefix("items_player")." WHERE owner = '$acctid'";
	$result = db_query($sql1);
	$ids = array();
	while ($row = db_fetch_assoc($result)){
		$ids[] = $row['id'];
	}
	$sqlp = "DELETE FROM ".db_prefix("items_prefs")." WHERE id IN (";
	
	foreach($ids AS $id){
		$sqlp .= $id.",";
	}
	$sqlp = substr_replace($sqlp,"",-1);
	$sqlp .= ")";
	
	$sqli = "DELETE FROM ".db_prefix("items_player")." WHERE owner = '$acctid'";
	db_query($sqli);
	db_query($sqlp);
	invalidatedatacache("playeritems/playeritems_$acctid");
}

function items_dragonkill($acctid=false){
	//this could be done better, but right now I just want it released damn it
	global $session, $itemprefs, $itemsettings, $inventory;
	
	if (!$acctid){
		$acctid=$session['user']['acctid'];
	}
	
	if (!isset($inventory)){
		load_inventory($acctid);
	}
	
	debug($inventory);
	
	foreach($inventory AS $itemid => $prefs){
		debug($itemid);
		if (!$prefs['dkpersist']){
			debug("Found an item that doesn't belong!");
			delete_item($itemid);
		}
	}
	
	debug($inventory);
	
	invalidatedatacache("playeritems/playeritems_$acctid");
	
}

function get_player_items($acctid){
	global $session, $idstoitems;
	
	$items = datacache("playeritems/playeritems_$acctid");
	
	if (!is_array($items) || !count($items)){	
		$sql = "SELECT item,id FROM ".db_prefix("items_player")." WHERE owner='$acctid'";
		$result = db_query($sql);	
		$items = array();
		$baseitems = array();
		while ($row = db_fetch_assoc($result)){
			$items[$row['id']]['item'] = $row['item'];
			$idstoitems[$row['id']] = $row['item'];
		}
		updatedatacache("playeritems/playeritems_$acctid",$items);
	}
	//debug("Loading player items from database");
	return $items;
}

function get_items_by_id($ids){
	global $idstoitems;
	if (is_array($ids)){
		$sql = "SELECT * FROM ".db_prefix("items_player")." WHERE id IN (";
		foreach($ids AS $id){
			$sql .= $id.",";
		}
		$sql = substr_replace($sql,"",-1);
		$sql .= ")";
	} else {
		$sql = "SELECT * FROM ".db_prefix("items_player")." WHERE id = '$ids'";
	}
	$result = db_query($sql);
	//debug("Loading items by ID from database");
	$items = array();
	while ($row = db_fetch_assoc($result)){
		$items[$row['id']]['item'] = $row['item'];
		$idstoitems[$row['id']] = $row['item'];
	}
	return $items;
}

function load_item_settings(){
	//load settings for all items, put them in $itemsettings
	//we don't cache this because the datacache is lousy at getting large amounts of data via db_fetch_assoc.
	global $itemsettings;
	
	if (!isset($itemsettings) || !is_array($itemsettings)){
		$itemsettings = datacache("item-settings");
	}
	
	if (!isset($itemsettings) || !is_array($itemsettings)){
		$itemsettings = array();
		$ssql = "SELECT * FROM ".db_prefix("items_settings");
		$sresult = db_query($ssql);
		//debug("Loading item settings from database");
		while ($srow = db_fetch_assoc($sresult)){
			$itemsettings[$srow['item']][$srow['setting']] = stripslashes($srow['value']);
		}
		updatedatacache("item-settings",$itemsettings);
	}
}

function load_item_prefs($items){
	global $session, $itemprefs, $updated_itemprefs;
	//load prefs for all items specified in $items, put them into $itemprefs
	
	if (!isset($itemprefs) || !is_array($itemprefs)){
		$itemprefs = array();
	}
	
	if (is_array($items)){
		$psql = "SELECT * FROM ".db_prefix("items_prefs")." WHERE id IN (";
		foreach($items AS $id => $item){
			$psql .= $id.",";
		}
		$psql = substr_replace($psql,"",-1);
		$psql .= ")";
	} else {
		$psql = "SELECT * FROM ".db_prefix("items_prefs")." WHERE id = '$items'";
	}
	//debug("Loading item prefs from database");
	$presult = db_query($psql);
	while ($prow = db_fetch_assoc($presult)){
		$itemprefs[$prow['id']][$prow['setting']] = stripslashes($prow['value']);
	}
	
	// debug($itemprefs);
	if (isset ($updated_itemprefs) && is_array($updated_itemprefs)){
		foreach($updated_itemprefs AS $updateditem => $updatedprefs){
			$itemprefs[$updateditem] = array_merge($itemprefs[$updateditem],$updatedprefs);
		}
	}
}

function load_inventory($acctid=false,$npcflag=false){
	//debug("Loading inventory");
	if (!$npcflag){
		global $session, $itemprefs, $itemsettings, $inventory;
		if (!$acctid){
			$acctid = $session['user']['acctid'];
		}
	} else {
		global $itemprefs, $itemsettings;
	}
	$items = get_player_items($acctid);
	if ((!count($items) || !is_array($items)) && !$npcflag){
		debug("No items found!");
		debug("ASSIGNING STARTER ITEMS");
		//assign starting items
		give_item("bandolier1",false,$acctid);
		give_item("backpack1",false,$acctid);
		$items = get_player_items($acctid);
	}
	load_item_prefs($items);
	
	if (!isset($itemsettings) || !is_array($itemsettings)){
		load_item_settings();
	}
	
	$inventory = $items;
	$weights = array();
	//now take settings and prefs, apply them to the item in question
	
	$ikeys = array_keys($inventory);
	$isize = sizeOf($ikeys);
	for ($i=0; $i<$isize; $i++){
		if (isset($itemsettings[$inventory[$ikeys[$i]]['item']]) && is_array($itemsettings[$inventory[$ikeys[$i]]['item']])) $inventory[$ikeys[$i]] = array_merge($inventory[$ikeys[$i]],$itemsettings[$inventory[$ikeys[$i]]['item']]);
		if (isset($itemprefs[$ikeys[$i]]) && is_array($itemprefs[$ikeys[$i]])) $inventory[$ikeys[$i]] = array_merge($inventory[$ikeys[$i]],$itemprefs[$ikeys[$i]]);
		if (isset($updated_itemprefs[$ikeys[$i]]) && is_array($updated_itemprefs[$ikeys[$i]])) $inventory[$ikeys[$i]] = array_merge($inventory[$ikeys[$i]],$updated_itemprefs[$$ikeys[$i]]);
		if (!$npcflag){
			//put items that don't have a carrier into the "main" inventory
			if (!isset($inventory[$ikeys[$i]]['inventorylocation'])) $inventory[$ikeys[$i]]['inventorylocation'] = "main";
		}
	}
	clear_weights();
	calculate_weights();
	$inventory = modulehook("load_inventory",$inventory);
	
	return $inventory;
}

function load_inventory_doubleoptimized($acctid=false,$npcflag=false){
	if (!$npcflag){
		global $session, $itemprefs, $itemsettings, $inventory;
		if (!$acctid){
			$acctid = $session['user']['acctid'];
		}
	} else {
		global $itemprefs, $itemsettings;
	}
	$sql = "SELECT itemstable.owner AS owner, itemstable.item AS item, itemstable.id AS id, prefstable.setting AS pref, prefstable.value AS prefval, settingstable.setting AS setting, settingstable.value AS settingval FROM ".db_prefix("items_player")." AS itemstable RIGHT JOIN ".db_prefix("items_settings")." AS settingstable ON itemstable.item = settingstable.item LEFT JOIN ".db_prefix("items_prefs")." AS prefstable ON itemstable.id = prefstable.id WHERE itemstable.owner = ".$acctid;
	$result = db_query($sql);
	while ($row = db_fetch_assoc($result)){
		//debug($row);
		$itemprefs[$row['id']][$row['pref']]=$row['prefval'];
		$itemsettings[$row['item']][$row['setting']]=$row['settingval'];
		if (!isset($itemprefs[$row['id']][$row['setting']])){
			$inventory[$row['id']][$row['setting']] = $row['settingval'];
		}
		$inventory[$row['id']][$row['pref']] = $row['prefval'];
		$inventory[$row['id']]['item'] = $row['item'];
		if (!$npcflag){
			//put items that don't have a carrier into the "main" inventory
			if (!$inventory[$row['id']]['inventorylocation']) $inventory[$row['id']]['inventorylocation'] = "main";
		}
	}
	//debug($inventory);
	calculate_weights();
	$inventory = modulehook("load_inventory",$inventory);
	return $inventory;
}

function load_inventory_old($acctid=false,$npcflag=false){
	//debug("Loading inventory");
	if (!$npcflag){
		global $session, $itemprefs, $itemsettings, $inventory;
		if (!$acctid){
			$acctid = $session['user']['acctid'];
		}
	} else {
		global $itemprefs, $itemsettings;
	}
	$items = get_player_items($acctid);
	if ((!count($items) || !is_array($items)) && !$npcflag){
		debug("No items found!");
		debug("ASSIGNING STARTER ITEMS");
		//assign starting items
		give_item("bandolier1",false,$acctid);
		give_item("backpack1",false,$acctid);
		$items = get_player_items($acctid);
	}
	load_item_prefs($items);
	
	if (!isset($itemsettings) || !is_array($itemsettings)){
		load_item_settings();
	}
	
	$inventory = $items;
	$weights = array();
	//now take settings and prefs, apply them to the item in question
	foreach ($inventory AS $id => $vals){
		if (is_array($itemsettings[$vals['item']])) $inventory[$id] = array_merge($vals,$itemsettings[$vals['item']]);
		if (is_array($itemprefs[$id])) $inventory[$id] = array_merge($inventory[$id],$itemprefs[$id]);
		if (is_array($updated_itemprefs[$id])) $inventory[$id] = array_merge($inventory[$id],$updated_itemprefs[$id]);
		if (!$npcflag){
			//put items that don't have a carrier into the "main" inventory
			if (!isset($inventory[$id]['inventorylocation'])) $inventory[$id]['inventorylocation'] = "main";
		}
	}
	calculate_weights();
	$inventory = modulehook("load_inventory",$inventory);
	
	return $inventory;
}

function clear_weights(){
	global $inventory;
	if (!isset($inventory)){
		return false;
	}
	
	foreach($inventory AS $itemid => $prefs){
		if (isset($prefs['weight_current'])){
			unset($inventory[$itemid]['weight_current']);
		}
	}
}

function move_item($itemid, $to){
	//function to move items around in players' Inventories
	global $inventory;
	if (!isset($inventory)){
		load_inventory();
	}
	set_item_pref("inventorylocation",$to,$itemid);
	clear_weights();
	calculate_weights();
}

function calculate_weights(){
	global $inventory;
	if (!isset($inventory)){
		load_inventory();
	}
	
	$weights = array();
	$carriers = array();
	foreach($inventory AS $itemid => $prefs){
		if ($prefs['carrieritem']){
			$carriers[$prefs['carrieritem']] = $itemid;
		} else {
			$weights[$prefs['inventorylocation']] += $prefs['weight'];
		}
	}
	
	$hookweights = array();
	foreach ($weights AS $location => $weight){
		$itemid = $carriers[$location];
		$inventory[$itemid]['weight_current'] = $weight;
		$hookweights[$location] = $inventory[$itemid];
		$hookweights[$location]['weight_current'] = $weight;
	}
	
	modulehook("items_weights",$hookweights);
}

function set_item_pref($setting,$value,$itemid,$reloadinventory=false){
	global $session, $itemprefs, $updated_itemprefs, $inventory;

	if ($itemprefs[$itemid][$setting] == $value) return;

	$itemprefs[$itemid][$setting] = $value;

	if (isset($inventory[$itemid])){
		$inventory[$itemid][$setting] = $value;
	}

	$updated_itemprefs[$itemid][$setting] = $value;
	
	if ($reloadinventory){
		load_inventory();
	}
}

function clear_item_pref($setting,$itemid){
	global $session, $itemprefs, $updated_itemprefs, $inventory;

	if (isset($itemprefs[$itemid][$setting])){
		unset($itemprefs[$itemid][$setting]);
	}
	
	if (isset($inventory[$itemid][$setting])){
		unset($inventory[$itemid][$setting]);
	}
	
	if (isset($updated_itemprefs[$itemid][$setting])){
		unset($updated_itemprefs[$itemid][$setting]);
	}
	
	$sql = "DELETE FROM ".db_prefix("items_prefs")." WHERE id=$itemid AND setting=$setting";
	db_query($sql);
}

function get_item_pref($setting,$itemid){
	global $session, $itemprefs, $itemsettings, $inventory;
	
	if (isset($inventory[$itemid][$setting])){
		return $inventory[$itemid][$setting];
	}
	
	if (isset($itemprefs[$itemid][$setting])){
		return $itemprefs[$itemid][$setting];
	}
	
	load_item_prefs($itemid);
	if (isset($itemprefs[$itemid][$setting])){
		return $itemprefs[$itemid][$setting];
	}
	
	//debug("Unable to find pref ".$setting." for item number ".$itemid." - now checking settings");
	
	//still not defined... we must figure out what sort of item this is, and obtain its settings
	$item = itemid_to_item($itemid);
	//$item = $row['item'];
	
	$set = get_item_setting($setting, $item);
	if ($set){
		return $set;
	}
	
	return false;
}

function get_item_setting($setting,$item){
	global $session, $itemsettings;
	
	if (!isset($itemsettings) || !is_array($itemsettings)){
		load_item_settings();
	}
	
	if (isset($itemsettings[$item][$setting])){
		return $itemsettings[$item][$setting];
	}
	
	//debug("Item setting ".$setting." for item ".$item." not found, returning false");
	$itemsettings[$item][$setting] = false;
	
	return false;
}

function get_item_prefs($itemid){
	global $session, $itemprefs;
	if (isset($itemprefs[$itemid])){
		return $itemprefs[$itemid];
	}
	load_item_prefs($itemid);
	if (isset($itemprefs[$itemid])){
		return $itemprefs[$itemid];
	}
}

function get_item_settings($item){
	global $session, $itemsettings;
	if (isset($itemsettings[$item])){
		return $itemsettings[$item];
	}

	load_item_settings();
	if (isset($itemsettings[$item])){
		return $itemsettings[$item];
	}
	
	debug("Settings for item \"".$item."\" not found, returning empty");
	return array();
}

function set_item_setting($setting,$value,$item){
	global $session, $itemsettings;
	$itemsettings[$item][$setting] = $value;
	$value = addslashes($value);
	$sql = "INSERT INTO ".db_prefix("items_settings")." (item,setting,value) VALUES ('$item','$setting','$value') ON DUPLICATE KEY UPDATE value = VALUES(value)";
	db_query($sql);
	invalidatedatacache("item-settings");
	return $value;
}

function increment_item_setting($setting,$value,$item){
	global $session, $itemsettings;
	$itemsettings[$item][$setting] += $value;
	$newvalue = $itemsettings[$item][$setting];
	$sql = "INSERT INTO ".db_prefix("items_settings")." (item,setting,value) VALUES ('$item','$setting','$newvalue') ON DUPLICATE KEY UPDATE value = VALUES(value)";
	db_query($sql);
	invalidatedatacache("item-settings");
	return $value;
}

function write_item_prefs(){
	//this will need to be called from saveuser.php
	global $updated_itemprefs;
	if (count($updated_itemprefs)){
		$sql = "INSERT INTO " . db_prefix("items_prefs") . " (id,setting,value) VALUES "; 
		foreach($updated_itemprefs AS $itemid=>$prefs){
			foreach($prefs AS $pref=>$value){
				$sql .= "('$itemid','$pref','".addslashes($value)."'),";
			}
		}
		$sql = substr_replace($sql,"",-1);
		$sql .= " ON DUPLICATE KEY UPDATE value = VALUES(value)";
		db_query($sql);
	}
}

function change_item_owner($itemids,$newowner){
	global $itemsettings, $itemprefs, $inventory;
	
	$oldowners = array();
	
	if (is_array($itemids)){
	
		//get the items' old owners, so we can invalidate the cache properly
		if (getsetting("usedatacache",0)){
			$sql = "SELECT owner FROM ".db_prefix("items_player")." WHERE id IN (";
			foreach($itemids AS $id){
				$sql .= $id.",";
				//set_item_pref("inventorylocation","main",$id);
			}
			$sql = substr_replace($sql,"",-1);
			$sql .= ")";
			
			$result = db_query($sql);
			while ($row = db_fetch_assoc($result)){
				$oldowners[]=$row['owner'];
			}
		}
	
		$sql = "UPDATE ".db_prefix("items_player")." SET owner='$newowner' WHERE id IN (";
		foreach($itemids AS $id){
			$sql .= $id.",";
			//set_item_pref("inventorylocation","main",$id);
		}
		$sql = substr_replace($sql,"",-1);
		$sql .= ")";
	} else {
		if (getsetting("usedatacache",0)){
			$sql = "SELECT owner FROM ".db_prefix("items_player")." WHERE id='$itemids'";
			
			$result = db_query($sql);
			while ($row = db_fetch_assoc($result)){
				$oldowners[]=$row['owner'];
			}
		}
		$sql = "UPDATE ".db_prefix("items_player")." SET owner='$newowner' WHERE id='$itemids'";
		//set_item_pref("inventorylocation","main",$itemids);
	}
	
	if ($itemids){
		
		if (is_array($itemids)){
			foreach($itemids AS $id){
				//check whether or not this item also gives its container item, IE lodge bags, shoeboxes
				if (get_item_pref("give_container",$id)){
					//check for the container
					if (!has_item(get_item_pref("give_container",$id),$newowner) && is_numeric($newowner)){
						debug("Giving item container");
						give_item(get_item_pref("give_container",$id),false,$newowner);
					}
				}
			}
		} else {
			if (get_item_pref("give_container",$itemids)){
				//check for the container
				if (!has_item(get_item_pref("give_container",$itemids),$newowner) && is_numeric($newowner)){
					debug("Giving item container");
					give_item(get_item_pref("give_container",$itemids),false,$newowner);
				}
			}
		}
		//debug($sql);
		db_query($sql);
	}

	invalidatedatacache("playeritems/playeritems_$newowner");
	
	foreach($oldowners AS $oldowner){
		invalidatedatacache("playeritems/playeritems_".$oldowner);
	}
	
	//reload inventory
	load_inventory();
};

//=======================================================================
//EOF basic storage/retrieval functionality - begin in-game usage section
//=======================================================================

function give_item($item, $prefs=false, $acctid=false, $skipreload=false){
	//To maximise performance when giving items inside a loop, set $skipreload to true and then call load_inventory() immediately afterwards.  Unless of course you don't need to set prefs for the items, in which case just use give_multiple_items instead.
	global $session, $inventory, $itemsettings;
	if (!$acctid){
		$acctid = $session['user']['acctid'];
	}
	
	if (get_item_setting("unique",$item)){
		//check for duplicate items
		$sql = "SELECT * FROM ".db_prefix("items_player")." WHERE item = '$item' AND owner = '$acctid'";
		$result = db_query($sql);
		if (db_num_rows($result)){
			//output("`c`b`4HORRIBLE HORRIBLE ITEM SYSTEM ERROR`nSomething has gone wrong, and the item system has tried to give you an item of which you should have only one.  Like a backpack or something.  Please report this, and tell us exactly what you did!`0`b`c`n`n");
			return false;
		}
	}
	
	$sql = "INSERT INTO ".db_prefix("items_player")." (item, owner) VALUES ('$item','$acctid')";
	db_query($sql);
	
	//get the id of the last item entered
	$key = mysql_insert_id();
	
	if (is_array($prefs)){
		foreach ($prefs AS $setting=>$value){
			set_item_pref($setting,$value,$key);
		}
	}
	
	//check whether or not this item also gives its container item, IE lodge bags, shoeboxes
	if (get_item_setting("give_container",$item)){
		//check for the container
		if (!has_item(get_item_setting("give_container",$item),$acctid) && is_numeric($acctid)){
			give_item(get_item_setting("give_container",$item),false,$acctid);
		}
	}
	
	invalidatedatacache("playeritems/playeritems_$acctid");
	
	if ($acctid == $session['user']['acctid']){
		//saves a query over loading the inventory every time
		if (!isset($inventory) || !is_array($inventory) && !$skipreload){
			load_inventory();
		} else {
			if (!isset($itemsettings) || !is_array($itemsettings)){
				load_item_settings();
			}
			$inventory[$key] = $itemsettings[$item];
		}
	}
	
	return $key;
}

function give_multiple_items($item,$quantity,$acctid=false){
	//setting prefs with items not supported in this function
	global $session, $inventory;
	if (!$acctid){
		$acctid = $session['user']['acctid'];
	}
	$sql = "INSERT INTO ".db_prefix("items_player")." (item, owner) VALUES ";
	for ($i=0; $i<$quantity; $i++){
		$sql .= "('$item','$acctid'),";
	}
	$sql = substr_replace($sql,"",-1);
	db_query($sql);
	if ($acctid == $session['user']['acctid']){
		//reload inventory - we can't really get around it
		load_inventory();
	}
	
	invalidatedatacache("playeritems/playeritems_$acctid");
}

function delete_item($itemid){
	global $inventory;
	if (getsetting("usedatacache",0)){
		$sql = "SELECT owner FROM ".db_prefix("items_player")." WHERE id='$itemid'";
		$result = db_query($sql);
		while ($row = db_fetch_assoc($result)){
			invalidatedatacache("playeritems/playeritems_".$row['owner']);
		}
	}
	
	$sqli = "DELETE FROM ".db_prefix("items_player")." WHERE id = '$itemid'";
	$sqlp = "DELETE FROM ".db_prefix("items_prefs")." WHERE id = '$itemid'";
	db_query($sqli);
	db_query($sqlp);
	if (isset($inventory[$itemid])){
		unset($inventory[$itemid]);
	}
	
	invalidatedatacache("playeritems/playeritems_$acctid");
	//clear_weights();
	//calculate_weights();
}

function delete_all_items_of_type($item,$acctid=false){
	//returns number of items deleted
	global $session;
	if (!$acctid){
		$acctid = $session['user']['acctid'];
	}
	//first get all the prefs for the items of this type
	$sql1 = "SELECT id FROM ".db_prefix("items_player")." WHERE item = '$item' AND owner = '$acctid'";
	$result = db_query($sql1);
	$ids = array();
	while ($row = db_fetch_assoc($result)){
		$ids[] = $row['id'];
	}
	$count = count($ids);
	
	if ($count > 0){
		$sqlp = "DELETE FROM ".db_prefix("items_prefs")." WHERE id IN (";
		
		foreach($ids AS $id){
			$sqlp .= $id.",";
		}
		$sqlp = substr_replace($sqlp,"",-1);
		$sqlp .= ")";
		
		
		$sqli = "DELETE FROM ".db_prefix("items_player")." WHERE item = '$item' AND owner = '$acctid'";
		db_query($sqli);
		db_query($sqlp);
	}
	invalidatedatacache("playeritems/playeritems_$acctid");
	//reload inventory
	load_inventory();
	return $count;
}

function use_item($item,$context="default"){
	global $session, $inventory;
	
	if (!isset($inventory)){
		load_inventory();
	}
	
	if (!is_numeric($item)){
		$item = has_item($item);
	}
	
	if ($item){
		$useitem = $inventory[$item];
		$useitem['id'] = $item;
		$useitem['context'] = $context;
		$useitem = modulehook("use_item",$useitem);
		if ($useitem['break_use_operation']){
			return false;
		}
		if ($useitem['require_file']){
			require_once "items/".$useitem['require_file'];
		}
		if ($useitem['call_function']){
			$useitem = call_user_func($useitem['call_function'],$useitem);
		}
		if ($useitem['usetext']){
			output("`0%s`0`n`n",$useitem['usetext']);
		}
		if ($useitem['destroyafteruse']){
			delete_item($useitem['id']);
		}
	} else {
		debug("No such item exists in this player's inventory (looked for ".$item.")");
		return false;
	}
}

//returns all the id's of a given item
function get_all_items($item,$acctid=false){
	if (!$acctid){
		global $session, $inventory;
		if (!isset($inventory)){
			load_inventory();
		}
	} else {
		$inventory = load_inventory($acctid,true);
	}
	
	if (is_numeric($item)){
		$item = itemid_to_item($item);
	}
	
	$ret = array();
	foreach($inventory AS $id => $vals){
		if ($vals['item'] == $item){
			$ret[$id] = $vals;
		}
	}
	return $ret;
}

//gets an item type from an item id
function itemid_to_item($itemid){
	global $idstoitems;
	if (isset($idstoitems[$itemid])){
		return $idstoitems[$itemid];
	}
	//debug("item type not in memory, looking up item ".$itemid." from db");
	
	$sql = "SELECT item FROM ".db_prefix("items_player")." WHERE id = '$itemid'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	//debug($row);
	$idstoitems[$itemid] = $row['item'];
	
	//debug($idstoitems[$itemid]);
	
	return $idstoitems[$itemid];
}

//returns either the first suitable item's id, or false.
function has_item($item, $prefs=false, $acctid=false){
	if (!$acctid){
		global $session, $inventory;
		if (!isset($inventory)){
			load_inventory();
		}
	} else {
		$inventory = load_inventory($acctid,true);
	}
	//debug($inventory);
	foreach($inventory AS $id => $vals){
		if ($vals['item']==$item){
			//we have a match - do the prefs line up?
			if (is_array($prefs)){
				$unsuitable = false;
				foreach($prefs AS $setting => $value){
					if ($vals[$setting]!=$value){
						$unsuitable = true;
					}
					if (!$unsuitable){
						return $id;
					}
				}
			} else {
				return $id;
			}
		}
	}
	return false;
}

//returns the quantity of items that the player holds
function has_item_quantity($item, $prefs=false, $acctid=false){
	if (!$acctid){
		global $session, $inventory;
		if (!isset($inventory)){
			load_inventory();
		}
	} else {
		$inventory = load_inventory($acctid,true);
	}
	$quantity = 0;
	foreach($inventory AS $id => $vals){
		if ($vals['item']==$item){
			//we have a match - do the prefs line up?
			if (is_array($prefs)){
				$unsuitable = false;
				foreach($prefs AS $setting => $value){
					if ($vals[$setting]!=$value){
						$unsuitable = true;
					}
					if (!$unsuitable){
						$quantity++;
					}
				}
			} else {
				$quantity++;
			}
		}
	}
	return $quantity;
}

function get_item_with_quantities($item){
	global $session, $inventory;
	if (!isset($inventory)){
		load_inventory();
	}
	$ret = array();
	$ret['quantity'] = 0;
	foreach($inventory AS $itemid => $prefs){
		if ($prefs['item']==$item){
			$ret[]=$item;
			$ret['quantity']+=1;
		}
	}
	return $ret;
}

function get_items_with_prefs($properties,$eval=false,$gt=false){
	//use a single property, or an array of properties, or an array of properties and values
	//example usage:
	/*
	
	//gets player's hats
	$hats = get_items_with_prefs("ishat");
	//gets player's hats with bells on
	$hatswithbells_prefs = array("ishat","hasbells");
	$hatswithbells = get_items_with_prefs($hatswithbells_prefs);
	//gets player's hats that have bells and are red
	$redhats_prefs = array("colour"=>"red","ishat"=>true);
	$redhats = get_items_with_prefs($redhats_prefs,true);
	//gets player's hats that are red, have bells, and are of size two or more
	$bigredhatswithbells_prefs = array("colour"=>"red","ishat"=>true,"hasbells"=>true,"hatsize"=>2);
	$bigredhatswithbells = get_items_with_prefs($bigredhatswithbells_prefs,true,true);
	
	*/
	
	global $session, $inventory;
	if (!isset($inventory)){
		load_inventory();
	}
	$ret = array();
	if (is_array($properties)){
		foreach($inventory AS $id => $prefs){
			if (!$eval){
				foreach($properties AS $property){
					if (isset($prefs[$property])){
						$ret[$id]=$prefs;
					}
				}
			} else if (!$gt){
				foreach($properties AS $setting => $value){
					if ($prefs[$setting]==$value){
						$ret[$id]=$prefs;
					}
				}
			} else {
				foreach($properties AS $setting => $value){
					if ($prefs[$setting]>=$value){
						$ret[$id]=$prefs;
					}
				}
			}
		}
	} else {
		foreach($inventory AS $id => $prefs){
			if (isset($prefs[$properties])){
				$ret[$id]=$prefs;
			}
		}
	}
	if (count($ret)>=1){
		return $ret;
	} else {
		return false;
	}
}

function get_items_with_settings($searchsettings){
	//returns all defined items with these settings
	//use a single property, or an array of properties
	global $itemsettings;
	if (!isset($itemsettings)){
		load_item_settings();
	}
	$ret = array();
	if (is_array($searchsettings)){
		foreach($itemsettings AS $item => $settings){
			foreach($searchsettings AS $searchsetting){
				if (isset($settings[$searchsetting])){
					$settings['item']=$item;
					$ret[$item]=$settings;
				}
			}
		}
	} else {
		foreach($itemsettings AS $item => $settings){
			if (isset($settings[$searchsettings])){
				$settings['item']=$item;
				$ret[$item]=$settings;
			}
		}
	}
	if (count($ret)>=1){
		return $ret;
	} else {
		return false;
	}
}

function sortcarriers($a, $b){
	return strnatcmp($a['carrier_sort'], $b['carrier_sort']);
}

function sortitems($a, $b){
	return strnatcmp($a['item_sort'], $b['item_sort']);
}

function alphacompare($a, $b){
	return strnatcmp($a['verbosename'], $b['verbosename']);
}

function qtycompare($a, $b){
	return strnatcmp($b['quantity'], $a['quantity']);
}

function group_items($items,$sort=false){
	$uni = array();
	foreach($items AS $itemid => $vals){
		$sitem = serialize($vals);
		if (isset($uni[$sitem]['data']['quantity'])){
			$uni[$sitem]['data']['quantity'] += 1;
		} else {
			$uni[$sitem]['data']['quantity'] = 1;
		}
		$uni[$sitem]['data']['itemid'] = $itemid;
		$uni[$sitem]['data'] = array_merge($uni[$sitem]['data'],$vals);
	}
	//debug($uni);
	$ret = array();
	$carriers = array();
	$items = array();
	foreach($uni AS $sarray => $data){
		$ret[$data['data']['itemid']] = $data['data'];
		if ($data['data']['carrieritem']){
			$carriers[$data['data']['itemid']] = $data['data'];
		} else {
			$items[$data['data']['itemid']] = $data['data'];
		}
	}
	
	usort($carriers, 'sortcarriers');
	
	switch ($sort){
		case "qty":
			usort($items,'qtycompare');
		break;
		case "alpha":
			usort($items,'alphacompare');
		break;
		case "key":
			asort($items);
		break;
		case "admin":
			usort($items,'sortitems');
		break;
	}
		
	$ret = $carriers;
	foreach($items AS $key => $vals){
		$ret[] = $vals;
	}
		
	return $ret;
}

function show_item_fightnavs($script){
	global $session, $inventory;
	//TODO: Define links for these
	if (!isset($inventory)){
		load_inventory();
	}
	
	$gr = group_items($inventory);
	foreach($gr AS $sortid => $vals){
		$itemid = $vals['itemid'];
		addnav("Use Fight Items");
		if ($vals['context_fight'] && $vals['inventorylocation']=="fight" && !$vals['blockuse']){
			if ($vals['quantity'] > 1){
				addnav(array("%s (%s)",$vals['verbosename'],$vals['quantity']),$script."op=fight&items_useitem=$itemid");
			} else {
				addnav(array("%s",$vals['verbosename']),$script."op=fight&items_useitem=$itemid");
			}
		}
	}
}

function items_return_link($context,$script=false){
	$returnlinks = array(
		'script' => $script,
		'forest' => "forest.php",
		'village' => "village.php",
	);
	$returnlinks = modulehook("items-returnlinks",$returnlinks);
	// debug($returnlinks);
	// debug($context);
	return $returnlinks[$context];
}

function items_calculate_weights(){
	global $session, $inventory;
	if (!isset($inventory)){
		load_inventory();
	}
	$containerweights = array();
	foreach($inventory AS $itemid => $vals){
		$containerweights[$vals['inventorylocation']]['weight_current']+=$vals['weight'];
		if ($vals['carrieritem']){
			$containerweights[$vals['carrieritem']] = array_merge($containerweights[$vals['carrieritem']],$vals);
		}
	}
	return $containerweights;
}

?>