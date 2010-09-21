<?php

function eboy_getmoduleinfo(){
	$info=array(
		"name"=>"eBoy's Trading Station",
		"version"=>"2009-10-01",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"Items",
		"download"=>"",
	);
	return $info;
}

function eboy_install(){
	module_addhook("village");
	module_addhook("newday-runonce");
	module_addhook("items-returnlinks");
	return true;
}

function eboy_uninstall(){
	return true;
}

function eboy_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "village":
			tlschema($args['schemas']['marketnav']);
			addnav($args['marketnav']);
			tlschema();
			addnav("eBoy's Trading Station","runmodule.php?module=eboy&op=start");
			break;
		case "newday-runonce":
			
			$eboyitems = get_items_with_settings("eboy");
			
			//get number of players
			$sql = "SELECT count(acctid) AS c FROM " . db_prefix("accounts") . " WHERE locked=0";
			$result = db_query_cached($sql,"numplayers",600);
			$row = db_fetch_assoc($result);
			$numplayers = $row['c'];
			
			$sql = "SELECT * from ".db_prefix("cityprefs");
            $result=db_query($sql);
			$numrows = db_num_rows($result);
			for ($i = 0; $i < $numrows; $i++){
				$row = db_fetch_assoc($result);
				$cid = $row['cityid'];
				foreach($eboyitems AS $item => $settings){
					//Advance Multiplier
					if ($settings['eboy_multiplier_'.$cid]){
						if ($settings['stock'] < ($numplayers/10)){
							increment_item_setting("eboy_multiplier_".$cid,0.1,$item);
						} else if ($settings['stock'] > ($numplayers/5)){
							increment_item_setting("eboy_multiplier_".$cid,-0.1,$item);
						}
						//stop prices staying ridiculously low
						if (get_item_setting("eboy_multiplier_".$cid,$item) < 0.1) set_item_setting("eboy_multiplier_".$cid,0.1,$item);
						//or going ridiculously high
						if (get_item_setting("eboy_multiplier_".$cid,$item) > 50) set_item_setting("eboy_multiplier_".$cid,50,$item);
					} else {
						set_item_setting("eboy_multiplier_".$cid,1,$item);
					}
					if (!isset($settings['stock'])){
						set_item_setting("eboy_stock_".$cid,1,$item);
					}
					increment_item_setting("eboy_multiplier_".$cid,$settings['eboy_dailyadd'],$item);
				}
			}
			break;
		case "items-returnlinks":
			$args['eboy'] = "runmodule.php?module=eboy&op=start";
			break;
	}
	return $args;
}

function eboy_run(){
	global $session, $inventory;
	if (!isset($inventory)){
		load_inventory();
	}
	page_header("eBoy's Trading Station");
	addnav("Buy Items");
	addnav("Sell Items");
	require_once "modules/cityprefs/lib.php";
	$cid = get_cityprefs_cityid("location",$session['user']['location']);
	
	$item = httpget('item');
	if ($item){
		$curstock = get_item_setting("eboy_stock_".$cid, $item);
		$curprice = get_item_setting("eboy_price_".$cid, $item);
	}
	
	eboy_updateprices();
	$eboy_info = get_items_with_settings("eboy");
	
	if (httpget('op')=="start"){
		output("Already fearing the worst, you head into eBoy's Trading Station.`n`nYour fears are well-founded.  The place is packed with the heaving, sweaty bodies of the hardcore capitalist, shouting \"`iBuy, buy!`i\" and \"`iSell, sell!`i\" and \"`iPut down the chainsaw and let's talk about this!`i\"`n`neBoy himself - although you suspect that this place, like Mike's Chop Shop, is a franchise of which you'll find one in every outpost, so is his name really eBoy?  Whoever he is, he stands on an elevated section of floor behind a tall mahogany counter, grabbing money with one hand and tossing grenades and ration packs over his shoulder with the other.  His arms are a blur.  His speech is the unintelligible, rapid-fire gabble of a professional auctioneer.  His eyes bulge and swivel.  You know he's loving this.`n`n");
	}
	
	if (httpget('op')=="buy"){
		$pprice = httpget('price');
		if ($pprice == $curprice){
			if ($curstock > 0){
				give_item($item);
				$session['user']['gold'] -= $curprice;
				increment_item_setting("eboy_stock_".$cid, -1, $item);
				output("You fight your way to the front of the crowd and shout your order up to eBoy, slapping your Requisition tokens down on the counter.  Before you can blink, your money is gone and the item you desired sits in its place.  You grab it hastily and stuff it into your backpack as eBoy turns to deal with the next customer.`n`n");
			} else {
			output("eBoy turns to you and gibbers.`n`n\"Sorrymatejustsoldthelastoneyougottabefasteritsthequickandthedeadaroundherepal.\"  He turns to the next customer in line, leaving you trying to piece together whatever it is that he just said.`n`n");
			}
		} else {
			output("You fight your way to the front of the crowd and shout your order up to eBoy, slapping your Requisition tokens down on the counter.  eBoy turns to you and gibbers.`n`n\"Idontmeantobefunnymatebutlookattheboardthepricehasjustbloodychangedagainyousureyoustillwannabuy?\"  After giving you exactly one microsecond to consider what the hell he just asked, he rolls his eyes and turns to the next customer in line.`n`n");
		}
	}
	
	if (httpget('op')=="sell"){
		$pprice = httpget('price');
		$requiredprefs = array(
			"inventorylocation" => "main",
		);
		$itemid = has_item($item,$requiredprefs);
		if ($pprice == $curprice){
			delete_item($itemid);
			if ($curstock < 3){
				$buy = round($curprice*0.5);
			} else {
				$buy = round($curprice*0.7);
			}
			$session['user']['gold'] += $buy;
			increment_item_setting("eboy_stock_".$cid, 1, $item);
			output("You barge your way through the crowd like a battleship through an ice floe, and toss your item up to eBoy.  eBoy snatches the item out of the air with his left hand while tossing your Requisition tokens back at you with his right, and goes on to serve the next customer.`n`n");
		} else {
			output("You fight your way to the front of the crowd and shout your order up to eBoy, slapping your Requisition tokens down on the counter.  eBoy turns to you and gibbers.`n`n\"Idontmeatobefunnymatebutlookattheboardthepricehasjustbloodychangedagainyousureyoustillwannasell?\"  After giving you exactly one microsecond to consider what the hell he just asked, he rolls his eyes and turns to the next customer in line.`n`n");
		}
	}
	
	output("You look up above eBoy's head at the trading board, the values of each commodity displayed on a mechanical readout which clatters and changes constantly.`n`n");
	$sellable_inventory = array();
	foreach($inventory AS $itemid => $prefs){
		if (!$prefs['carrieritem'] && $prefs['eboy'] && $prefs['inventorylocation']=="main"){
			$sellable_inventory[$prefs['item']]['quantity'] += 1;
			$sellable_inventory[$prefs['item']]['itemids'][] = $itemid;
		}
	}
	
	eboy_updateprices();
	$eboy_info = get_items_with_settings("eboy");
	
	//inventory-type display routine
	rawoutput("<table width=100% style='border: dotted 1px #000000;'>");
	$classcount=1;
	foreach($eboy_info AS $item => $settings){		
		if ($settings['eboy_stock_'.$cid] >0 ){
			$stock = number_format($settings['eboy_stock_'.$cid])." available";
		} else {
			$stock = "`4Sold Out`0";
		}
		$classcount++;
		$class=($classcount%2?"trdark":"trlight");
		rawoutput("<tr class='$class'><td>");
		if ($settings['image']) rawoutput("<table width=100% cellpadding=0 cellspacing=0><tr><td width=100px align=center><img src=\"images/items/".$settings['image']."\"></td><td>");
		output("`b%s`b`n",stripslashes($settings['verbosename']));
		output("%s`n",stripslashes($settings['description']));
		if ($settings['weight']){
			output("Weight: %s kg`n",$settings['weight']);
		}
		rawoutput("<table width=100%><tr><td width=50%>");
		output("`bStock:`b %s",$stock);
		if ($settings['eboy_stock_'.$cid] < 3){
			$buy = round($settings['eboy_price_'.$cid]*0.5);
		} else {
			$buy = round($settings['eboy_price_'.$cid]*0.7);
		}
		if ($settings['eboy_price_'.$cid] > 0){
			output("`n`7Buying at: %s Requisition",number_format($buy));
		} else {
			output("`nNot Buying");
		}
		if ($settings['eboy_stock_'.$cid] > 0){
			output("`n`2Selling at: %s Requisition`n`n",number_format($settings['eboy_price_'.$cid]));
		} else {
			output("`n`n");
		}
		rawoutput("</td><td width=50%>");
		$requiredprefs = array(
			"inventorylocation" => "main",
		);
		if ($sellable_inventory[$item]){
			addnav("Sell Items");
			if ($settings['eboy_stock_'.$cid] < 3){
				$buy = round($settings['eboy_price_'.$cid]*0.5);
			} else {
				$buy = round($settings['eboy_price_'.$cid]*0.7);
			}
			if ($sellable_inventory[$item]['quantity'] > 0 && $settings['eboy_price_'.$cid] > 0){
				addnav(array("%s (%s available)",$settings['verbosename'],$sellable_inventory[$item]['quantity']),"runmodule.php?module=eboy&op=sell&item=".$item."&price=".$settings['eboy_price_'.$cid], true);
				addnav("","runmodule.php?module=eboy&op=sell&item=".$item."&price=".$settings['eboy_price_'.$cid]);
				rawoutput("<a href=\"runmodule.php?module=eboy&op=sell&item=".$item."&price=".$settings['eboy_price_'.$cid]."\">Sell for <strong>".number_format($buy)." </strong> Requisition (".$sellable_inventory[$item]['quantity']." available to sell)</a><br />");
			}
		} else {
				output("`&You don't have any of this item to sell.`0`n");
		}
		if ($session['user']['gold'] >= $settings['eboy_price_'.$cid] && $settings['eboy_stock_'.$cid]){
			addnav("Buy Items");
			addnav(array("%s (%s Req)",$settings['verbosename'],$settings['eboy_price_'.$cid]),"runmodule.php?module=eboy&op=buy&item=".$item."&price=".$settings['eboy_price_'.$cid], true);
			addnav("","runmodule.php?module=eboy&op=buy&item=".$item."&price=".$settings['eboy_price_'.$cid]);
			rawoutput("<a href=\"runmodule.php?module=eboy&op=buy&item=".$item."&price=".$settings['eboy_price_'.$cid]."\">Buy for <strong>".number_format($settings['eboy_price_'.$cid])."</strong> Requisition</a><br />");
		} else {
			if (!$settings['eboy_stock_'.$cid]){
				output("`&There are no items of this type available.`0`n");
			} else {
				$difference = $settings['eboy_price_'.$cid]-$session['user']['gold'];
				output("`&You need another %s Requisition to buy this item.`0`n",number_format($difference));
			}
		}
		rawoutput("</td></tr></table>");
		if ($settings['image']) rawoutput("</td></tr></table>");
		rawoutput("</td></tr>");
	}
	rawoutput("</td></tr></table>");
	
	addnav("Leave");
	addnav("Return to the Outpost","village.php");
	addnav("Inventory");
	addnav("View your Inventory","inventory.php?items_context=eboy");
	//set_module_objpref("city",$cid,"eboytrades-intelligent",serialize($info));
	page_footer();
	
	return true;
}

function eboy_updateprices(){
	global $session;
	
	$eboy_info = get_items_with_settings("eboy");
	
	require_once "modules/cityprefs/lib.php";
	$cid = get_cityprefs_cityid("location",$session['user']['location']);
	
	$sql = "SELECT count(acctid) AS c FROM " . db_prefix("accounts") . " WHERE locked=0";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$totalplayers = $row['c'];
	
	foreach($eboy_info AS $item => $settings){
		if ($settings['eboy_stock_'.$cid]){
			if (!$settings['eboy_multiplier_'.$cid] || !isset($settings['eboy_multiplier_'.$cid])) $settings['eboy_multiplier_'.$cid] = 1;
			$newprice = round(($totalplayers / $settings['eboy_stock_'.$cid]) * $settings['eboy_multiplier_'.$cid]);
			set_item_setting("eboy_price_".$cid,$newprice,$item);
			$eboy_info['eboy_price_'.$cid] = $newprice;
		} else {
			if (!$settings['eboy_multiplier_'.$cid] || !isset($settings['eboy_multiplier_'.$cid])) $settings['eboy_multiplier_'.$cid] = 1;
			$newprice = round($totalplayers*$settings['eboy_multiplier_'.$cid]);
			set_item_setting("eboy_price_".$cid,$newprice,$item);
			$eboy_info['eboy_price_'.$cid] = $newprice;
		}
		if ($settings['eboy_price_'.$cid] < 10){
			set_item_setting("eboy_price_".$cid,10,$item);
		}
	}
}

?>