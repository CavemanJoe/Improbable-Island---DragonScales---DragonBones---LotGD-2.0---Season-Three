<?php

function iitems_worldmapdrop_getmoduleinfo(){
	$info = array(
		"name"=>"IItems: Drop IItems on World Map",
		"version"=>"2009-11-09",
		"author"=>"Dan Hall",
		"category"=>"IItems",
		"download"=>"",
		"settings"=>array(
			"iitemsquares"=>"World map squares and their IItems,viewonly|array()",
		),
	);
	return $info;
}

function iitems_worldmapdrop_install(){
	module_addhook("worldnav");
	module_addhook("iitems-inventory");
	module_addhook("iitems-show-inventory");
	module_addhook("iitems-superuser");
	return true;
}

function iitems_worldmapdrop_uninstall(){
	return true;
}

function iitems_worldmapdrop_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "iitems-show-inventory":
			//debug("Hook?");
			$drop = httpget('dropworldmap');
			if ($drop){
				$squares = unserialize(get_module_setting("iitemsquares"));
				$ploc = get_module_pref("worldXYZ","worldmapen");
				if (httpget('dropmapall')){
					$qty = iitems_discard_all_items(httpget('discard'));
					output("`0You drop the items to your feet.  Maybe someone else will find a use for them.`n");
				} else if (httpget('dropmaphalf')) {
					$q = iitems_discard_all_items(httpget('discard'));
					$half = round($q / 2);
					$qty = $q - $half;
					for ($i=0; $i<$half; $i++){
						iitems_give_item(httpget('discard'));
					}
					output("`0You drop the items to your feet.  Maybe someone else will find a use for it.`n");
				} else {
					iitems_discard_item(httpget('discard'));
					$qty = 1;
					output("`0You drop the item to your feet.  Maybe someone else will find a use for it.`n");
				}
				$squares[$ploc][$drop]+=$qty;
				set_module_setting("iitemsquares", serialize($squares));
			}
		break;
		case "iitems-inventory":
			//debug($args);
			if ($args['master']['dropworldmap'] && httpget('from')=="worldnav"){
				rawoutput("<a href=\"runmodule.php?module=iitems&op=inventory&from=".httpget('from')."&dropworldmap=".$args['player']['itemid']."&discard=".$args['inventorykey']."\">Drop this item on the Map for someone else to pick up</a><br />");
				addnav("","runmodule.php?module=iitems&op=inventory&from=".httpget('from')."&dropworldmap=".$args['player']['itemid']."&discard=".$args['inventorykey']);
				if ($args['player']['quantity']>2){
					rawoutput("<a href=\"runmodule.php?module=iitems&op=inventory&from=".httpget('from')."&dropworldmap=".$args['player']['itemid']."&discard=".$args['inventorykey']."&dropmaphalf=true\">Drop half of these items on the Map for someone else to pick up</a><br />");
					addnav("","runmodule.php?module=iitems&op=inventory&from=".httpget('from')."&dropworldmap=".$args['player']['itemid']."&discard=".$args['inventorykey']."&dropmaphalf=true");
				}
				if ($args['player']['quantity']>1){
					rawoutput("<a href=\"runmodule.php?module=iitems&op=inventory&from=".httpget('from')."&dropworldmap=".$args['player']['itemid']."&discard=".$args['inventorykey']."&dropmapall=true\">Drop all of these items on the Map for someone else to pick up</a><br />");
					addnav("","runmodule.php?module=iitems&op=inventory&from=".httpget('from')."&dropworldmap=".$args['player']['itemid']."&discard=".$args['inventorykey']."&dropmapall=true");
				}
			}
		break;
		case "worldnav":
			$ploc = implode(",",$args);
			$squares = unserialize(get_module_setting("iitemsquares"));
			$itemid = httpget('iitem-pickup');
			if ($itemid){
				require_once "modules/iitems/lib/lib.php";
				$itemdetails = iitems_get_item_details($itemid);
				//Pick up iitems
				if (httpget('alliitems')){
					//Pick up all iitems
					output("`0You pick up the %s %s and put them in your backpack.`n",$$squares[$ploc][$itemid],$itemdetails['plural']);
					for ($i=0; $i<$squares[$ploc][$itemid]; $i++){
						iitems_give_item($itemid);
					};
					unset ($squares[$ploc][$itemid]);
				} else if (httpget('halfiitems')) {
					//Pick up half. Yay, moderation! -HgB
					$count = $squares[$ploc][$itemid];
					$quantity = round($count / 2);
					output("`0You pick up %s of the %s and put them in your backpack.`n",$quantity,$itemdetails['plural']);
					for ($i=0; $i<$quantity; $i++){
						iitems_give_item($itemid);
					};
					$squares[$ploc][$itemid] -= $quantity;
					if ($squares[$ploc][$itemid]===0){
						unset($squares[$ploc][$itemid]);
					}
				} else {
					//Pick up single iitem
					if ($squares[$ploc][$itemid]){
						output("`0You pick up the %s and put it in your backpack.`n",$itemdetails['verbosename']);
						iitems_give_item($itemid);
						$squares[$ploc][$itemid]--;
						if ($squares[$ploc][$itemid]===0){
							unset($squares[$ploc][$itemid]);
						}
					} else {
						output("`0You bend over to pick up the %s, but it's suddenly not there anymore!  Some crafty bastard has pinched it from right under your nose!`n",$itemdetails['verbosename']);
					}
				}
				set_module_setting("iitemsquares", serialize($squares));
			}
			
			//Show iitems that can be picked up
			if (!is_array($squares)) {
				$squares = array();
				set_module_setting("iitemsquares", serialize($squares));
			}
			
			if (is_array($squares[$ploc])){
				if (count ($squares[$ploc])){
					//this square has something in it!
					$iitems = $squares[$ploc];
					addnav("Pick up items");
					require_once "modules/iitems/lib/lib.php";
					foreach ($iitems AS $id => $qty){
						$item = iitems_get_item_details($id);
						if ($qty<1){
							unset($squares[$ploc][$id]);
						}
						$showwarn = 1;
						addnav(array("Pick up %s",$item['verbosename']),"runmodule.php?module=worldmapen&op=continue&iitem-pickup=".$id);
						if ($qty>1){
							output("`0There are %s %s here.`n`n",$qty,$item['plural']);
							if ($qty > 2)
							{
								addnav(array("Pick up half of %s",$item['plural']),"runmodule.php?module=worldmapen&op=continue&iitem-pickup=".$id."&halfiitems=1");
							}
							addnav(array("Pick up all %s",$item['plural']),"runmodule.php?module=worldmapen&op=continue&iitem-pickup=".$id."&alliitems=1");
						} else if ($qty==1){
							output("`0There is a %s here.`n`n",$item['verbosename']);
						}
					}
					if ($showwarn && $session['user']['dragonkills'] < 1 && $session['user']['level'] < 10){
						output("`JLogs and stone are only really useful if you're building a Dwelling.  To build a Dwelling, you'll need a Land Claim stake from Improbable Central, which will set you back 100 Cigarettes.  If you're not building a Dwelling, then there's not much reason to pick them up (logs and stone are really heavy!).  This message will disappear once you've got a few more levels under your belt.`0`n");
					}
				} else {
					//this square is empty, unset it
					unset($squares[$ploc]);
					set_module_setting("iitemsquares", serialize($squares));
				}
			}
			break;
		}
	return $args;
}

function iitems_worldmapdrop_run(){
	global $session;
	page_footer();
}

?>