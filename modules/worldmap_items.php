<?php

function worldmap_items_getmoduleinfo(){
	$info = array(
		"name"=>"Items on World Map",
		"version"=>"2010-09-10",
		"author"=>"Dan Hall",
		"category"=>"Map",
		"download"=>"",
	);
	return $info;
}

function worldmap_items_install(){
	module_addhook("worldnav");
	module_addhook("inventory");
	module_addhook("inventory-predisplay");
	return true;
}

function worldmap_items_uninstall(){
	return true;
}

function worldmap_items_dohook($hookname,$args){
	global $session, $itemprefs;
	switch($hookname){
		case "inventory":
			if ($args['context']=="worldmap"){
				$inv = $args['inventory'];
				foreach($inv AS $itemid => $prefs){
					if ($prefs['dropworldmap']){
//						debug("Found something!");
						addnav("","inventory.php?items_context=".$args['context']."&items_mapdrop=".$itemid);
						$args['inventory'][$itemid]['inventoryactions'].="<a href=\"inventory.php?items_context=".$args['context']."&items_mapdrop=".$itemid."\">Drop this item on the Map</a> | ";
						$args['inventory'][$itemid]['cannotdiscard']=true;
					}
				}
			}
		break;
		case "inventory-predisplay":
			if ($args['context']=="worldmap"){
				$drop = httpget("items_mapdrop");
				if ($drop){
					$loc = get_module_pref("worldXYZ","worldmapen");
					//set_item_pref("worldmap_location",$loc,$drop);
					change_item_owner($drop,"worldmap_".$loc);
				}
			}
		break;
		case "worldnav":
			$ploc = implode(",",$args);
			$squarestuff = load_inventory("worldmap_".$ploc, true);
			//$squarestuff = worldmap_items_getsquare($ploc);
			//debug($squarestuff);
			$itemid = httpget('item-pickup');
			
			if ($itemid){
				//Pick up iitems
				if (httpget('pickupall')){
					//Pick up all iitems
					//$squarestuff = worldmap_items_getsquare($ploc,false);
					$itemtype = $squarestuff[$itemid]['item'];
					//debug($itemtype);
					$pickup = array();
					foreach($squarestuff AS $sqitemid => $sqprefs){
						//debug($sqprefs);
						if ($sqprefs['item'] == $itemtype){
							$pickup[]=$sqitemid;
						}
					}
					//debug($pickup);
					change_item_owner($pickup,$session['user']['acctid']);
					output("`0You pick up the %s and put them in your backpack.`n",$squarestuff[$itemid]['plural']);
				} else {
					//Pick up single iitem
					if ($squarestuff[$itemid]){
						output("`0You pick up the %s and put it in your backpack.`n",$squarestuff[$itemid]['verbosename']);
						change_item_owner($itemid,$session['user']['acctid']);
					} else {
						output("`0You bend over to pick up the item, but it's suddenly not there anymore!  Some crafty bastard has pinched it from right under your nose!`n");
					}
				}
				//reload this square's inventory
				$squarestuff = load_inventory("worldmap_".$ploc, true);
			}
			
			$squarestuff = group_items($squarestuff);
			
			foreach($squarestuff AS $itemid => $prefs){
				$showmsg = true;
				if ($prefs['quantity'] > 1){
					addnav("Pick up items");
					addnav(array("Pick up %s",$prefs['verbosename']),"runmodule.php?module=worldmapen&op=continue&item-pickup=".$itemid);
					addnav(array("Pick up all %s",$prefs['plural']),"runmodule.php?module=worldmapen&op=continue&item-pickup=".$itemid."&pickupall=true");
					output("`0There are %s %s here.`n`n",$prefs['quantity'],$prefs['plural']);
				} else {
					addnav("Pick up items");
					addnav(array("Pick up %s",$prefs['verbosename']),"runmodule.php?module=worldmapen&op=continue&item-pickup=".$itemid);
					output("`0There is a %s here.`n`n",$prefs['verbosename']);
				}
			}
			
			if ($showmsg && $session['user']['dragonkills'] < 1 && $session['user']['level'] < 10){
				output("`JLogs and stone are only really useful if you're building a Dwelling.  To build a Dwelling, you'll need a Land Claim stake from Improbable Central, which will set you back 100 Cigarettes.  If you're not building a Dwelling, then there's not much reason to pick them up (logs and stone are really heavy!).  This message will disappear once you've got a few more levels under your belt.`0`n");
			}
			break;
		}
	return $args;
}

function worldmap_items_run(){
	global $session;
	page_footer();
}

function worldmap_items_getsquare($xyz,$group=true){
	global $itemprefs,$itemsettings;
	$ret = array();
	$sql = "SELECT id,owner FROM ".db_prefix("items_prefs")." WHERE setting='worldmap_location' AND value='$xyz'";
	$result = db_query($sql);
	
	$load = array();
	while ($row = db_fetch_assoc($result)){
		if (!$row['owner']){
			$load[]=$row['id'];
		}
	}
	
	$ret = load_non_player_inventory($load);
	
	if (count($ret)>1 && $group){
		$ret = group_items($ret);
	}
	
	return $ret;
}

?>