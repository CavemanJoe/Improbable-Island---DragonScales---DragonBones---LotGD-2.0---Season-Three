<?php

/*

require_once("lib/tabledescriptor.php");
$items_player = array(
	'id'=>array('name'=>'id', 'type'=>'int(11) unsigned', 'extra'=>'auto_increment'),
	'item'=>array('name'=>'item', 'type'=>'varchar(255)'),
	'owner'=>array('name'=>'owner', 'type'=>'varchar(255)'),
	'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'id'),
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
}

function items_dragonkill($acctid=false){
	//this could be done better, but right now I just want it released damn it
	global $session, $itemprefs, $itemsettings, $inventory;
	if (!isset($inventory)){
		load_inventory();
	}
	
	foreach($inventory AS $itemid => $prefs){
		if (!$prefs['dkpersist']){
			delete_item($itemid);
		}
	}
}

function get_player_items($acctid){
	global $session;
	$sql = "SELECT * FROM ".db_prefix("items_player")." WHERE owner='$acctid'";
	$result = db_query($sql);
	$items = array();
	while ($row = db_fetch_assoc($result)){
		$items[$row['id']]['item'] = $row['item'];
	}
	return $items;
}

function get_items_by_id($ids){
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
	$items = array();
	while ($row = db_fetch_assoc($result)){
		$items[$row['id']]['item'] = $row['item'];
	}
	return $items;
}

function load_item_settings(){
	//load settings for all items, put them in $itemsettings
	//todo: cache this
	global $itemsettings;
	if (!is_array($itemsettings)){
		$itemsettings = array();
	}
	$ssql = "SELECT * FROM ".db_prefix("items_settings");
	$sresult = db_query($ssql);
	while ($srow = db_fetch_assoc($sresult)){
		$itemsettings[$srow['item']][$srow['setting']] = stripslashes($srow['value']);
	}
}

function load_item_prefs($items){
	global $session, $itemprefs, $updated_itemprefs;
	//load prefs for all items specified in $items, put them into $itemprefs
	
	if (!is_array($itemprefs)){
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
	$presult = db_query($psql);
	while ($prow = db_fetch_assoc($presult)){
		$itemprefs[$prow['id']][$prow['setting']] = stripslashes($prow['value']);
	}
	
	if (is_array($updated_itemprefs)){
		$itemprefs = array_merge($itemprefs,$updated_itemprefs);
	}
	
}

function load_inventory($acctid=false,$npcflag=false){
	if (!$npcflag){
		global $session, $itemprefs, $itemsettings, $inventory;
		if (!$acctid){
			$acctid = $session['user']['acctid'];
		}
	} else {
		global $itemprefs, $itemsettings;
	}
	$items = get_player_items($acctid);
	if ((!is_array($items) || !count($items)) && !$npcflag){
		debug("No items found!");
		debug("ASSIGNING STARTER ITEMS");
		//assign starting items
		give_item("bandolier1",false,$acctid);
		give_item("backpack1",false,$acctid);
		$items = get_player_items($acctid);
	}
	load_item_prefs($items);
	load_item_settings();
	
	$inventory = $items;
	$weights = array();
	//now take settings and prefs, apply them to the item in question
	foreach ($inventory AS $id => $vals){
		if (is_array($itemsettings[$vals['item']])) $inventory[$id] = array_merge($vals,$itemsettings[$vals['item']]);
		if (is_array($itemprefs[$id])) $inventory[$id] = array_merge($inventory[$id],$itemprefs[$id]);
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
	
	//still not defined... we must figure out what sort of item this is, and obtain its settings
	$sql = "SELECT * FROM ".db_prefix("items_player")." WHERE id = '$itemid'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$item = $row['item'];
	
	$setting = get_item_setting($setting, $item);
	
	return false;
}

function get_item_setting($setting,$item){
	global $session, $itemsettings;
	if (isset($itemsettings[$item][$setting])){
		return $itemsettings[$item][$setting];
	}
	
	load_item_settings();
	if (isset($itemsettings[$item][$setting])){
		return $itemsettings[$item][$setting];
	}
	
	debug("Item setting (or pref) not found, returning false");
	return false;
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
	return $value;
}

function increment_item_setting($setting,$value,$item){
	global $session, $itemsettings;
	$itemsettings[$item][$setting] += $value;
	$newvalue = $itemsettings[$item][$setting];
	$sql = "INSERT INTO ".db_prefix("items_settings")." (item,setting,value) VALUES ('$item','$setting','$newvalue') ON DUPLICATE KEY UPDATE value = VALUES(value)";
	db_query($sql);
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
	if (is_array($itemids)){
		$sql = "UPDATE ".db_prefix("items_player")." SET owner='$newowner' WHERE id IN (";
		foreach($itemids AS $id){
			$sql .= $id.",";
			set_item_pref("inventorylocation","main",$id);
		}
		$sql = substr_replace($sql,"",-1);
		$sql .= ")";
	} else {
		$sql = "UPDATE ".db_prefix("items_player")." SET owner='$newowner' WHERE id='$itemids'";
		set_item_pref("inventorylocation","main",$itemids);
	}
	if ($itemids){
		//debug($sql);
		db_query($sql);
		//reload inventory
		load_inventory();
	}
};

//=======================================================================
//EOF basic storage/retrieval functionality - begin in-game usage section
//=======================================================================

function give_item($item, $prefs=false, $acctid=false){
	global $session, $inventory;
	if (!$acctid){
		$acctid = $session['user']['acctid'];
	}
	
	if (get_item_setting("unique",$item)){
		//check for duplicate items
		$sql = "SELECT * FROM ".db_prefix("items_player")." WHERE item = '$item' AND owner = '$acctid'";
		$result = db_query($sql);
		if (db_num_rows($result)){
			output("`c`b`4HORRIBLE HORRIBLE ITEM SYSTEM ERROR`nSomething has gone wrong, and the item system has tried to give you an item of which you should have only one.  Like a backpack or something.  Please report this, and tell us exactly what you did!`0`b`c`n`n");
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
	
	if ($acctid == $session['user']['acctid']){
		//reload inventory
		load_inventory();
	}
	return $key;
}

function delete_item($itemid){
	$sqli = "DELETE FROM ".db_prefix("items_player")." WHERE id = '$itemid'";
	$sqlp = "DELETE FROM ".db_prefix("items_prefs")." WHERE id = '$itemid'";
	db_query($sqli);
	db_query($sqlp);
	//reload inventory
	load_inventory();
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
	
	$sqlp = "DELETE FROM ".db_prefix("items_prefs")." WHERE id IN (";
	
	foreach($ids AS $id){
		$sqlp .= $id.",";
	}
	$sqlp = substr_replace($sqlp,"",-1);
	$sqlp .= ")";
	
	
	$sqli = "DELETE FROM ".db_prefix("items_player")." WHERE item = '$item' AND owner = '$acctid'";
	db_query($sqli);
	db_query($sqlp);
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
		//debug($useitem);
		if ($useitem['break_use_operation']){
			return false;
		}
		if ($useitem['usetext']){
			output("`0%s`n`n",$useitem['usetext']);
		}
		if ($useitem['require_file']){
			require_once "items/".$useitem['require_file'];
		}
		if ($useitem['call_function']){
			call_user_func($useitem['call_function'],$useitem);
		}
		if ($useitem['destroyafteruse']){
			delete_item($useitem['id']);
		}
	} else {
		debug("No such item exists in this player's inventory (looked for ".$item.")");
		return false;
	}
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
					$ret[$item]=$settings;
				}
			}
		}
	} else {
		foreach($itemsettings AS $item => $settings){
			if (isset($settings[$searchsettings])){
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
	// debug($a.",".$b." = ".strnatcmp($a['verbosename'], $b['verbosename']));
	return strnatcmp($a['verbosename'], $b['verbosename']);
}

function qtycompare($a, $b){
	// debug($a.",".$b." = ".strnatcmp($b['quantity'], $a['quantity']));
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
		//debug($data);
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
	debug($returnlinks);
	debug($context);
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