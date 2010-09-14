<?php

/*
=======================================================
GET PLAYER INVENTORY
Returns the player's inventory in an array.  This is basically an unassociated multidimensional array with item data in each slot.
=======================================================
*/

function iitems_get_player_inventory($userid=false) {
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	$items = unserialize(get_module_pref("items", "iitems", $userid));
	if (!is_array($items)) {
		$items = array();
		set_module_pref("items", serialize($items), "iitems", $userid);
	}
	//Check if array key zero is set - this makes iitems_has_item work a bit nicer
	if (isset($items[0])){
		$items[]=$items[0];
		unset($items[0]);
		set_module_pref("items", serialize($items), "iitems", $userid);
	}
	return $items;
}

/*
=======================================================
SET PLAYER INVENTORY
Reserializes and writes back the player's inventory.
=======================================================
*/

function iitems_set_player_inventory($inventory=false,$userid=false) {
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	if (!is_array($inventory)){
		return false;
	}
	//debug($inventory);
	set_module_pref("items", serialize($inventory), "iitems", $userid);
}

/*
=======================================================
GET ITEM DETAILS
Returns master details array of the item whose localname is given.
=======================================================
*/

function iitems_get_item_details($localname){
	$sql = "SELECT id,localname,data FROM " . db_prefix("iitems") . " WHERE localname = '$localname'";
	$result = db_query_cached($sql,"iitems-".$localname);
	$row=db_fetch_assoc($result);
	$sarray = unserialize($row['data']);
	return $sarray;
}

/*
=======================================================
GET ALL ITEM DETAILS
Returns master details array of all items in the database - for giving random items, for example.
=======================================================
*/

function iitems_get_all_item_details(){
	$sarray = array();
	$sql = "SELECT id,localname,data FROM " . db_prefix("iitems") . "";
	$result = db_query($sql);
	for ($i=0;$i<db_num_rows($result);$i++){
		$row=db_fetch_assoc($result);
		$sarray[$row['localname']] = unserialize($row['data']);
	}
	return $sarray;
}


/*
=======================================================
BASIC PLAYER FUNCTIONALITY
Now beginning basic player functionality section.
=======================================================
*/

/*
=======================================================
GIVE ITEM
Gives an item to a player by copying the item array into the player's inventory.  Includes a hook to modify the item in question.
If the item type is "Normal," then when an item already exists in the player's Inventory the Quantity is increased by one.
If the item type is "Special," then a new copy of the item is created and its "Instance" flag is set, helping the player to distinguish between copies of the same item.
Modules hooking into "iitems-give-item" should add to their $args array any parameters which should be copied from the database to the player's inventory array - for example, stats that can be altered on a per-player, per-item basis, such as item hitpoints, usepoints, or condition.  Hooks should also be added, to save looking up the item at each hook.
If modules hooking into "iitems-give-item" add the 'blockadd' parameter, the item will not be added.
Returns true if adding item was successful, false if unsuccessful.
=======================================================
*/

function iitems_give_item($itemid,$userid=false,$itemarray=false){
	global $session;
	//debug("giving iitem ".$itemid);
	if ($userid == false) $userid = $session['user']['acctid'];
	
	$inventory = iitems_get_player_inventory($userid);
	
	if (is_array($itemarray)){
		//here, we've been given an item array.  It's probably a special item IN FACT IT'S PROBABLY A BLOODY GIFT BOX WITH SOMETHING INSIDE, so let's treat it like one.  This will do until we rewrite this bag of utter toss.
		$inventory[]=$itemarray;
	} else {
		$iteminfo = iitems_get_item_details($itemid);
		if (!$iteminfo){
			debug("ERROR: undefined iitem (trying to give ".$itemid." to acctid ".$userid);
			return false;
		}
		
		$item = array();
		$item['itemid'] = $itemid;
		$item['verbosename'] = $iteminfo['verbosename'];
		//default location to store item is in main inventory - change via modulehook "iitems-give-item" if necessary
		if ($iteminfo['inventorylocation']){
			$item['inventorylocation']=$iteminfo['inventorylocation'];
		} else {
			$item['inventorylocation']="main";
		}
		if ($iteminfo['villagehooknav']) $item['villagehooknav'] = true;
		if ($iteminfo['foresthooknav']) $item['foresthooknav'] = true;
		if ($iteminfo['worldnavhooknav']) $item['worldnavhooknav'] = true;
		if ($iteminfo['fightnav']) $item['fightnav'] = true;
		if ($iteminfo['blocktransfer']) $item['blocktransfer'] = true;
		
		//To save database lookups, every time we send an item through a hook, we send it as part of an array.
		$hookitem = array();
		$hookitem['master'] = $iteminfo;
		$hookitem['player'] = $item;
		
		$hookitem = modulehook("iitems-give-item",$hookitem);
		$item = $hookitem['player'];
		
		if ($item['blockadd']){
			debug("Blockadd param inserted");
			debug("Failed to give iitem because of blockadd parameter (trying to give ".$itemid." to acctid ".$userid);
			return false;
		}
		
		//Check to see if we're adding an inventory-type item, IE backpack etc, and if so, add it and return early:
		if ($iteminfo['type']=="inventory"){
			$inventory[$iteminfo['inventorylocation']] = $item;
			debug("Item is inventory-type item");
			set_module_pref("items", serialize($inventory), "iitems", $userid);
			return true;
		}
		
		//Now find out whether the player already has this item
		$invid = iitems_has_item($itemid, $inventory, $userid, $item['inventorylocation']);
		if ($invid===false){
			//player does not have the item, copy it to the player's Inventory
			if ($iteminfo['type']=="normal"){
				//set initial quantity
				$item['quantity'] = 1;
			}
			$inventory[] = $item;
		} else {
			//Player already has the item - check for special items
			if ($iteminfo['type']=="special"){
				$instance = 1;
				$inventory2 = $inventory;
				foreach($inventory2 AS $inventoryitem => $details){
					if ($details['verbosename'] == $item['verbosename']){
						$instance++;
					}
				}
				$item['instance'] = $instance;
				$inventory[] = $item;
			} else if ($iteminfo['type']=="normal"){
				$inventory[$invid]['quantity'] += 1;
			} else {
				debug("ERROR: Item type not set (trying to give ".$itemid." to acctid ".$userid);
				return false;
			}
		}
	}
	
	//debug($inventory);
	set_module_pref("items", serialize($inventory), "iitems", $userid);
	debug("SUCCESS: Item ".$itemid." given successfully to account ".$userid);
	return true;
}

/*
=======================================================
PLAYER HAS ITEM ID?
Does the player already have an item which matches this itemid or verbose name?  Returns the item's numerical array key if true, false if false.
If player's inventory is passed, saves a database lookup.

TODO:
This really needs to return an array containing the master iitem, the player iitem, and the inventory key. :-/

TODO:
Sometimes an iitem will be in slot 0 - this means you have to do something like:
if (!iitems_has_item || iitems_has_item===0)
rather than just !iitems_has_item.

TODO:
Having to pass the invloc each time is a pain in the ass.

=======================================================
*/

function iitems_has_item($search,$inventory=false,$userid=false,$invloc="main"){
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	//get item info and player inventory
	if (!is_array($inventory)) $inventory = iitems_get_player_inventory($userid);
	
	if (is_numeric($search)){
		if (isset($inventory[$search]) && $inventory[$search]['inventorylocation']==$invloc){
			return $search;
		}
	}
	foreach($inventory AS $item => $details){
		if ($details['itemid']==$search  && $details['inventorylocation']==$invloc){
			return $item;
		}
	}
	foreach($inventory AS $item => $details){
		if ($details['verbosename']==$search  && $details['inventorylocation']==$invloc){
			return $item;
		}
	}
	return false;
}

/*
=======================================================
GET IITEM
Returns an array containing the player's matching iitems and the master iitem.  Handy for basic functionality.

Example - curse all player's swords:
$inv = iitems_get_player_inventory();
$item = iitems_get_item("sword",false,$inv);
foreach($item['player'] AS $item=>$vals){
	$inv[$item]['cursed']=1;
	$inv[$item]['attackpower']-=1;
}
iitems_set_player_inventory($inv);

Example - make all player's throwing knives rusty if they are kept in his coal shed:
$inv = iitems_get_player_inventory();
$item = iitems_get_item("throwingknife",false,$inv);
foreach($item['player'] AS $item=>$vals){
	if ($vals['inventorylocation']=="coalshed"){
		$inv[$item]['rust']+=1;
		$inv[$item]['attackpower']-=1;
	}
}
iitems_set_player_inventory($inv);

=======================================================
*/

function iitems_get_item($search,$userid=false,$inventory=false){
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	//get item info and player inventory
	if (!is_array($inventory)) $inventory = iitems_get_player_inventory($userid);
	$ret = array();
	foreach($inventory AS $item => $details){
		if (($details['itemid']==$search || $details['verbosename']==$search) && ($details['inventorylocation']==$invloc || $invloc = "all")){
			$ret['player']=$details;
		}
	}
	$ret['master']=iitems_get_item_details($search);
	return $ret;
}

/*
=======================================================
PLAYER HAS SUITABLE ITEM?
Does the player have an iitem that has a suitable property?
Returns an array of all usable items and their properties.

Example usage:
Player has a Chainsaw with woodcutting ability of 8.  Standard woodcutting ability of a Chainsaw is 10 - the player's Chainsaw has gotten rusty.
Player also has a hacksaw with woodcutting ability of 2.

iitems_has_property("woodcutting",8);
returns chainsaw iitem.

iitems_has_property("woodcutting",9);
returns false.

iitems_has_property("woodcutting");
returns chainsaw iitem.
returns hacksaw iitem.

iitems_has_property("woodcutting",9,true);
returns chainsaw item (third parameter, if set to true, checks only the master database iitem and not the player's iitem)

=======================================================
*/

function iitems_has_property($property,$val=1,$checkmaster=false,$inventory=false,$userid=false,$invloc="main"){
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	//get item info and player inventory
	if (!is_array($inventory)) $inventory = iitems_get_player_inventory($userid);

	$return = array();

	foreach($inventory AS $item => $details){
		if (!$checkmaster && $details[$property]>=$val && ($details['inventorylocation']==$invloc || $invloc=="all")){
			$return[$item]=$inventory[$item];
		}
		if ($checkmaster){
			$master = iitems_get_item_details($details['itemid']);
			if ($master[$property]>=$val){
				$return[$item]=$inventory[$item];
			}
		}
	}
	
	if (count($return)){
		return $return;
	} else {
		return false;
	}
}


/*
=======================================================
USE ITEM
If an itemid is entered, this function uses the first item in the player's Inventory array that matches the itemid of the database master item.  Useful for 'normal' type items, where it doesn't matter which instance of an item is used.
If an array key is entered, this function uses that particular item in the player's Inventory array.  Useful for when a player will, for example, favour his shiniest throwing knife.
If modules hooking in to iitems-use-item add the "destroynow" parameter to their $args['player'] array, the item will be removed.
If modules hooking in to iitems-use-item add the "break_use_operation" parameter to their $args['player'] array, the operation will break and the item will be returned to the player.
=======================================================
*/

function iitems_use_item($item,$userid=false,$invloc){
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	
	$inventory = iitems_get_player_inventory($userid);
	//debug($inventory);
	
	//Does the player have the item?
	$inventorykey = iitems_has_item($item, $inventory, $userid, $invloc);

	if ($inventorykey === false){
		return false;
	} else {
		//The player has the item
		$item = array();
		$item['player'] = $inventory[$inventorykey];
		$item['player']['inv_key'] = $inventorykey;
		$item['master'] = iitems_get_item_details($item['player']['itemid']);
		$item['inventory'] = $inventory;
		
		$item = modulehook("iitems-use-item",$item);
		if ($item['player']['break_use_operation']){
			return false;
		}
		$inventory = $item['inventory'];
		
		//Output text - modified text if inserted by hooking modules, standard text if not.
		if ($item['player']['usetext']){
			output("%s`n`n",stripslashes($item['player']['usetext']));
		} else if ($item['master']['usetext']){
			output("%s`n`n",stripslashes($item['master']['usetext']));
		}
		
		//Handle destroying, using up or otherwise removing items
		if ($item['player']['destroynow'] || $item['master']['destroyafteruse']){
			if ($item['player']['quantity']<=1){
				unset($inventory[$inventorykey]);
			} else {
				$inventory[$inventorykey]['quantity']--;
			}
		} else {
			$inventory[$inventorykey] = $item['player'];
		}
		
		$item = modulehook("iitems-use-item-after",$item);
		
		//debug($inventory);
		set_module_pref("items", serialize($inventory), "iitems", $userid);
	}
}

/*
=======================================================
DISCARD ITEM
Supply an inventory key, and this will discard that item.
If the item to be discarded is an Inventory item, other items that were occupying its space will be unceremoniously dumped into the "main" inventory.
Alternatively, supply an iitem id and this will indiscriminately discard the first iitem it comes across of that type.
=======================================================
*/

function iitems_discard_item($key,$invloc="main"){
	global $session;
	debug("Discarding item key ".$key);
	$inventory = iitems_get_player_inventory();
	
	if (is_numeric($key)){
		$loc = $inventory[$key]['inventorylocation'];
	} else {
		debug("Not numeric");
		$key = iitems_has_item($key,$inventory,false,$invloc);
		debug("Key is ".$key);
	}
	$type = $inventory[$key]['type'];
	if ($type=="inventory"){
		debug("Discarding inventory item");
		foreach ($inventory AS $item => $details){
			if ($details['inventorylocation'] == $loc){
				$details['inventorylocation'] = "main";
			}
		}
	}
	if ($inventory[$key]['quantity'] > 1){
			//debug("Lowering Quantity");
			$inventory[$key]['quantity']--;
		} else {
			//debug("Unsetting Key");
			unset($inventory[$key]);
		}
	
	set_module_pref("items", serialize($inventory), "iitems");
}

/*
=======================================================
DISCARD ALL ITEMS
Supply an inventory key, and this will discard all of that item.
If the item to be discarded is an Inventory item, other items that were occupying its space will be unceremoniously dumped into the "main" inventory.
Returns quantity of items discarded.
=======================================================
*/

function iitems_discard_all_items($key){
	global $session;
	debug("Discarding item");
	
	$inventory = iitems_get_player_inventory();
	$loc = $inventory[$key]['inventorylocation'];
	$type = $inventory[$key]['type'];
	
	if (is_numeric($key)){
		$loc = $inventory[$key]['inventorylocation'];
		$type = $inventory[$key]['type'];
		if ($type=="inventory"){
			debug("Discarding inventory item");
			foreach ($inventory AS $item => $details){
				if ($details['inventorylocation'] == $loc){
					$details['inventorylocation'] = "main";
				}
			}
		}
	} else {
		$key = iitems_has_item($key);
	}
	
	$qty = $inventory[$key]['quantity'];
	unset($inventory[$key]);

	set_module_pref("items", serialize($inventory), "iitems");
	return $qty;
}

/*
=======================================================
SHOW FIGHT ITEMS
Gives navs for items usable in forest fights.  To keep things simple, for now at least, we're going to assume that you can't use iitems in PVP, Master or Graveyard fights.
If the "fightinventory" module setting is true, then the player will only be able to use items with ['inventorylocation'] set to "fight", IE they have moved the item into their bandolier, utility belt, holster or similar - if not, all items with ['fightnav'] set will be allowed.
=======================================================
*/

function iitems_show_fight_items($script){
	global $session;

	$inventory = iitems_get_player_inventory();
	
	foreach ($inventory AS $item => $details){
		if ($details['fightnav']){
			if ($details['inventorylocation']=="fight" || !(get_module_setting("fightinventory","iitems"))){
				if ($details['quantity']){
					addnav("Use Items");
					addnav(array("Use %s (%s left)",$details['verbosename'],$details['quantity']),$script."op=fight&skill=iitems&item=$item", true);
				} else {
					addnav("Use Items");
					if ($details['instance']){
						addnav(array("Use %s %s",$details['verbosename'],$details['instance']),$script."op=fight&skill=iitems&item=$item", true);
					} else {
						addnav(array("Use %s",$details['verbosename']),$script."op=fight&skill=iitems&item=$item", true);
					}
				}
			}
		}
	}
}

/*
=======================================================
TRANSFER ITEM
Transfers an item from the "main" inventory (IE a backpack or rucksack) to a different Inventory owned by the same player.  For example, transfer an item from the backpack to the bandolier, to be used in forest fights - or transfer an item into a safe box in a dwelling.
=======================================================
*/

function iitems_transfer_item($key,$transferto){
	global $session;
	$inventory = iitems_get_player_inventory();
	$central = iitems_get_item_details($inventory[$key]['itemid']);
	
	if ($central['type']=="normal"){
		$newid = iitems_has_item($inventory[$key]['itemid'],$newinventory,false,$transferto);
		if ($newid || $newid===0){
			$inventory[$newid]['quantity'] += 1;
		} else {
			$newitem = $inventory[$key];
			$newitem['quantity'] = 1;
			$newitem['inventorylocation']=$transferto;
			$inventory[]=$newitem;
		}
		$inventory[$key]['quantity'] -= 1;
		if ($inventory[$key]['quantity'] <= 0){
			unset($inventory[$key]);
		}
	}
	else {
		$inventory[$key]['inventorylocation'] = $transferto;
	}
	set_module_pref("items", serialize($inventory), "iitems");
}

/*
=======================================================
SHOW INVENTORY
Shows the player's inventory, with descriptions of each item.
Gives a link to use items that can be used in this area.
Gives a link to discard items that are discardable.
Built-in functionality allows to transfer items to the bandolier for use in forest fights, if the "fightinventory" modulesetting is true and the player has a bandolier item (IE 'type'=inventory, 'wlimit_capacity' is set, 'inventorylocation'=fight).
=======================================================
*/

function iitems_show_inventory($from, $loc="main", $userid=false){
	global $session, $masteriitems;
	if ($userid === false) $userid = $session['user']['acctid'];
	
	modulehook("iitems-inventory-top");
	$inventory = iitems_get_player_inventory($userid);

	//Preload master records of all iitems in player's posession
	$masteriitems = array();
	foreach($inventory AS $key => $details){
		if (!isset($masteriitems[$details['itemid']])){
			$masteriitems[$details['itemid']]=iitems_get_item_details($details['itemid']);
		}
	}
	
	//Obtain list of carrier iitems
	$carriers = array();
	foreach($inventory AS $key => $details){
		if ($masteriitems[$details['itemid']]['type']=="inventory"){
			$carriers[$key]=$details;
		}
	}
	//debug($carriers);
	
	//display carrier
	rawoutput("<table width=100% style='border: dotted 1px #000000'><tr><td>");
	$central = $masteriitems[$inventory[$loc]['itemid']];
	if ($central['image']) rawoutput("<table width=100% cellpadding=0 cellspacing=0><tr><td>");
	output("`b%s`b`n",$inventory[$loc]['verbosename']);
	output("%s`n",stripslashes($central['description']));
	$hookitem = array();
	$hookitem['master'] = $central;
	$hookitem['player'] = $inventory[$loc];
	$hookitem['inventorykey'] = $loc;
	modulehook("iitems-inventory", $hookitem);
	if ($central['image']) rawoutput("</td><td align='right'><img src=\"images/iitems/".$central['image']."\"></td></tr></table>");
	unset($inventory[$loc]);
	
	//display items
	rawoutput("<table width=100% style='border: dotted 1px #000000; margin-left:10px; padding-right:-10px;'>");
	$classcount=1;	
	foreach($inventory AS $key => $details){
		if ($details['inventorylocation']==$loc && $details['type']!="inventory"){
			$central = $masteriitems[$details['itemid']];
			$classcount++;
			$class=($classcount%2?"trdark":"trlight");
			rawoutput("<tr class='$class'><td>");
			if ($central['image']) rawoutput("<table width=100% cellpadding=0 cellspacing=0><tr><td width=100px align=center><img src=\"images/iitems/".$central['image']."\"></td><td>");
			if (!isset($details['instance']) || $details['instance'] == 1){
				output("`b%s`b`n",$details['verbosename']);
			} else {
				output("`b%s (%s)`b`n",$details['verbosename'],$details['instance']);
			}
			if ($details['quantity']) output("Quantity: %s`n",$details['quantity']);
			
			$hookitem = array();
			$hookitem['master'] = $central;
			$hookitem['player'] = $details;
			$hookitem['inventorykey'] = $key;
			$hookitem = modulehook("iitems-inventory-intercept", $hookitem);
			
			//if module adds the "blockuse" param to the $player array, the item cannot be used.
			if ($hookitem['player'][$from.'hooknav'] && !$hookitem['player']['blockuse']){
				rawoutput("<a href=\"runmodule.php?module=iitems&op=useitem&key=$key&from=$from&invloc=".$hookitem['player']['inventorylocation']."\">Use this item</a><br />");
				addnav("","runmodule.php?module=iitems&op=useitem&key=$key&from=$from&invloc=".$hookitem['player']['inventorylocation']);
			}
			if (!$hookitem['master']['cannotdiscard']){
				$itemid = $hookitem['player']['verbosename'];
				rawoutput("<a href=\"runmodule.php?module=iitems&op=discarditem&key=$key&from=$from\">Discard this item</a><br />");
				addnav("","runmodule.php?module=iitems&op=discarditem&key=$key&from=$from");
			}
			if ($hookitem['master']['fightnav'] && get_module_setting("fightinventory","iitems")){
				//this item can be used in combat, therefore it's safe to assume we can transfer it to the bandolier.
				if ($loc!="fight"){
					$transferto = "fight";
				} else {
					$transferto = "main";
				}
				rawoutput("<a href=\"runmodule.php?module=iitems&op=transferitem&key=$key&from=$from&transferto=$transferto\">Transfer this item to your other Inventory</a><br />");
				addnav("","runmodule.php?module=iitems&op=transferitem&key=$key&from=$from&transferto=$transferto");
			}
			
			output("%s`n",stripslashes($hookitem['master']['description']));
			
			$finalhookitem = array();
			$finalhookitem['master'] = $hookitem['master'];
			$finalhookitem['player'] = $hookitem['player'];
			$finalhookitem['inventorykey'] = $key;
			modulehook("iitems-inventory", $finalhookitem);
			if ($finalhookitem['master']['image']) rawoutput("</td></tr></table>");
			rawoutput("</td></tr>");
		}
	}
	rawoutput("</table>");
	rawoutput("</td></tr></table>");
}

/*
=======================================================
SHOW INVENTORY
Shows the player's inventory, with descriptions of each item.
Gives a link to use items that can be used in this area.
Gives a link to discard items that are discardable.
Allows transfer of iitems and, if using weight limits, shows weights and prohibits transfer if hard weight limits are enforced on non-main inventories.
=======================================================
*/

function iitems_show_inventory_new($from, $userid=false){
	global $session, $masteriitems, $weightinfo;
	if ($userid === false) $userid = $session['user']['acctid'];
	
	modulehook("iitems-inventory-top");
	$inventory = iitems_get_player_inventory($userid);
	//debug($inventory);

	//Preload master records of all iitems in player's posession
	$masteriitems = array();
	foreach($inventory AS $key => $details){
		if (!isset($masteriitems[$details['itemid']])){
			$masteriitems[$details['itemid']]=iitems_get_item_details($details['itemid']);
		}
	}
	
	//Obtain list of carrier iitems
	$carriers = array();
	foreach($inventory AS $key => $details){
		if ($masteriitems[$details['itemid']]['type']=="inventory"){
			$carriers[$key]=iitems_get_item_details($details['itemid']);
		}
		if ($masteriitems[$details['itemid']]['inventorylocation']=="mount"){
			$addmount = 1;
		}
	}
	//debug($carriers);
	if ($addmount){
		$carriers['mount']=array(
			'verbosename'=>"Mount",
			'description'=>"Here's all the equipment currently attached to your Mount, not counting saddlebags and other carrier items.",
			'blocktransfer'=>"true", //blocks all transfer into or out of this carrier
		);
	}
	foreach($carriers AS $ckey => $cdetails){
		//display carrier
		rawoutput("<table width=100% style='border: dotted 1px #000000'><tr><td>");
		$central = $masteriitems[$inventory[$ckey]['itemid']];
		if ($central['image']) rawoutput("<table width=100% cellpadding=0 cellspacing=0><tr><td>");
		$vn = $inventory[$ckey]['verbosename'];
		if (!$vn) $vn = $carriers[$ckey]['verbosename'];
		$desc = stripslashes($central['description']);
		if (!$desc) $desc = stripslashes($carriers[$ckey]['description']);
		output("`b%s`b`n",$vn);
		output("%s`n",$desc);
		$hookitem = array();
		$hookitem['master'] = $central;
		$hookitem['player'] = $inventory[$ckey];
		$hookitem['inventorykey'] = $ckey;
		modulehook("iitems-inventory", $hookitem);
		//debug($hookitem);
		if ($hookitem['master']['image']) rawoutput("</td><td align='right'><img src=\"images/iitems/".$hookitem['master']['image']."\"></td></tr></table>");
		
		//display items
		rawoutput("<table width=100% style='border: dotted 1px #000000; margin-left:10px; padding-right:-10px;'>");
		$classcount=1;	
		foreach($inventory AS $key => $details){
			if ($details['inventorylocation']==$ckey && $masteriitems[$details['itemid']]['type']!="inventory"){
				$central = $masteriitems[$details['itemid']];
				$classcount++;
				$class=($classcount%2?"trdark":"trlight");
				rawoutput("<tr class='$class'><td>");
				if ($central['image']) rawoutput("<table width=100% cellpadding=0 cellspacing=0><tr><td width=100px align=center><img src=\"images/iitems/".$central['image']."\"></td><td>");
				if (!isset($details['instance']) || $details['instance'] == 1){
					output("`b%s`b`n",stripslashes($details['verbosename']));
				} else {
					output("`b%s (%s)`b`n",$details['verbosename'],$details['instance']);
				}
				if ($details['quantity']) output("Quantity: %s`n",$details['quantity']);
				
				$hookitem = array();
				$hookitem['master'] = $central;
				$hookitem['player'] = $details;
				$hookitem['inventorykey'] = $key;
				$hookitem = modulehook("iitems-inventory", $hookitem);
				//debug($hookitem);
				
				//if module adds the "blockuse" param to the $player array, the item cannot be used.
				if (($hookitem['player'][$from.'hooknav'] || $hookitem['master'][$from.'hooknav']) && !$hookitem['player']['blockuse']){
					rawoutput("<a href=\"runmodule.php?module=iitems&op=useitem&key=$key&from=$from&invloc=".$hookitem['player']['inventorylocation']."\">Use this item</a><br />");
					addnav("","runmodule.php?module=iitems&op=useitem&key=$key&from=$from&invloc=".$hookitem['player']['inventorylocation']);
				}
				if (!$hookitem['master']['cannotdiscard']){
					$itemid = $hookitem['player']['verbosename'];
					rawoutput("<a href=\"runmodule.php?module=iitems&op=discarditem&key=$key&from=$from\">Discard this item</a><br />");
					addnav("","runmodule.php?module=iitems&op=discarditem&key=$key&from=$from");
				}
				
				//Evaluate potential carriers for this iitem
				if (!$carriers[$hookitem['player']['inventorylocation']]['blocktransfer']){ //the carrier that the item is currently in doesn't restrict transfer of items
					foreach($carriers AS $ckey1 => $cdetails1){
						if (!$cdetails1['blocktransfer'] || $hookitem['master']['allowtransfer']==$ckey1){ //the carrier to be evaluated does not restrict transfer of iitems in OR the item is excluded
							if ($hookitem['player']['inventorylocation']!=$ckey1){ //the iitem is not already in the carrier we're evaluating
								if (!$hookitem['master']['blockcarrier_'.$ckey1]){ //the iitem is not blocked from being in this carrier
									if (!$hookitem['master']['blocktransfer'] || $hookitem['master']['allowtransfer']==$ckey1){ //the iitem is not blocked from being transferred completely OR the iitem is allowed to be transferred to only this carrier
										if ($ckey1=="fight" && !$hookitem['master']['fightnav']) continue; //skip if we're looking at the fight carrier and this thing can't go in fights
										$cvname = $cdetails1['verbosename'];
										$transferto = $ckey1;
										//check hard weight limits
										//debug($weightinfo);
										if ($weightinfo[$ckey1]['wlimit_hardlimit']){
											//debug($ckey1." has hard weight limit");
											//check weight
											if ($weightinfo[$ckey1]['max'] < ($weightinfo[$ckey1]['current'] + $hookitem['player']['weight'])){
												//rawoutput("This item won't fit in your $cvname<br />");
												continue;
											}
										}
										rawoutput("<a href=\"runmodule.php?module=iitems&op=transferitem&key=$key&from=$from&transferto=$transferto\">Transfer this item to your $cvname</a><br />");
										addnav("","runmodule.php?module=iitems&op=transferitem&key=$key&from=$from&transferto=$transferto");
										//debug("Item can be transferred to ".$transferto);
									}
								}
							}
						}
					}
				}
				
				output("%s`n",stripslashes($hookitem['master']['description']));
				
				$finalhookitem = array();
				$finalhookitem['master'] = $hookitem['master'];
				$finalhookitem['player'] = $hookitem['player'];
				$finalhookitem['inventorykey'] = $key;
				//modulehook("iitems-inventory", $finalhookitem);
				if ($finalhookitem['master']['image']) rawoutput("</td></tr></table>");
				rawoutput("</td></tr>");
			}
		}
		rawoutput("</table>");
		rawoutput("</td></tr></table><br />");
	}
}

?>
