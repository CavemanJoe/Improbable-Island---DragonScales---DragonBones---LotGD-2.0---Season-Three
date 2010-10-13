<?php

function iitems_hunterslodge_getmoduleinfo(){
	$info=array(
		"name"=>"IItems - Hunter's Lodge replacement",
		"version"=>"2010-08-11",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"Lodge IItems",
		"download"=>"",
	);
	return $info;
}

function iitems_hunterslodge_install(){
	module_addhook("village");
	module_addhook("newday");
	module_addhook("superuser");
	module_addhook("items-returnlinks");
	$purchaselog = array(
		'id'=>array('name'=>'id', 'type'=>'int(11) unsigned', 'extra'=>'auto_increment'),
		'acctid'=>array('name'=>'acctid', 'type'=>'int(11) unsigned'),
		'purchased'=>array('name'=>'purchased', 'type'=>'text'),
		'amount'=>array('name'=>'amount', 'type'=>'int(11) unsigned'),
		'data'=>array('name'=>'data', 'type'=>'text'),
		'giftwrap'=>array('name'=>'giftwrap', 'type'=>'text'),
		'timestamp'=>array('name'=>'timestamp', 'type'=>'datetime'),
		'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'id'),
	);
	require_once("lib/tabledescriptor.php");
	synctable(db_prefix('purchaselog'), $purchaselog, true);
	return true;
}

function iitems_hunterslodge_uninstall(){
	return true;
}

function iitems_hunterslodge_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "superuser":
			if ($session['user']['superuser'] & SU_EDIT_USERS){
				addnav("Hunter's Lodge Purchase Log","runmodule.php?module=iitems_hunterslodge&op=superuser");
			}
		break;
		case "village":
			tlschema($args['schemas']['fightnav']);
			addnav($args['fightnav']);
			tlschema();
			blocknav("lodge.php");
			addnav("L?The Hunter's Lodge","runmodule.php?module=iitems_hunterslodge&op=start");
			break;
		case "newday":
			if ($session['user']['referer']>0){
				$sql = "SELECT lastip, uniqueid FROM ".db_prefix("accounts")." WHERE acctid={$session['user']['referer']}";
				$result = db_query($sql);
				$row = db_fetch_assoc($result);
				if ($row['lastip'] != $session['lastip'] && $row['uniqueid']!=$session['uniqueid']){
					$sql = "UPDATE " . db_prefix("accounts") . " SET donation=donation+1 WHERE acctid={$session['user']['referer']}";
					$result = db_query($sql);
				}
			}
		break;
		case "items-returnlinks":
			$args['lodge'] = "runmodule.php?module=iitems_hunterslodge&op=start";
		break;
	}
	return $args;
}

function iitems_hunterslodge_run(){
	global $session;
	page_header("Hunter's Lodge");
	
	$op = httpget('op');
	
	$pointsleft = $session['user']['donation']-$session['user']['donationspent'];
	$pointstotal = $session['user']['donation'];
	$pointsspent = $session['user']['donationspent'];
	
	switch ($op){
		case "superuser":
			$sql = "SELECT * FROM ".db_prefix("purchaselog");
			$result = db_query($sql);
			$transactions = array();
			while ($row = db_fetch_assoc($result)){
				$row['date'] = getdate(strtotime($details['timestamp']));
				$transactions[]=$row;
			}
			//top-selling items
			$topsellers = array();
			foreach ($transactions AS $transaction => $details){
				$topsellers[$details['purchased']]+=1;
			}
			arsort($topsellers);
			debug($topsellers);
		break;
		case "explain":
			output("You give a friendly nod to the proprietor, and open your mouth to ask him a question.`n`nHe grins back at you.`n`nThere's a small `ipop`iping sensation in the centre of your skull, like a muscle abruptly shifting - and you suddenly realise what this place is all about.`n`n`bAbout Supporter Points`b`nImprobable Island is entirely funded by donations from its players.  When you donate, you get Supporter Points, which you can use on items in the Hunter's Lodge.  You get one hundred Supporter Points per US Dollar, and donations are accepted through PayPal.  To donate, click the coin slot to the lower right of your screen.  Always use the \"Site Admin\" PayPal button when donating if you wish to receive Supporter Points (donations made through the \"Author\" button go to Eric Stevens, the author of the game engine on which Improbable Island was originally based - you don't get any Supporter Points for donating through this button).  You can also get Supporter Points by referring new players to the site (click the Referrals link to the left) or sometimes in `4Other Ways`0 which will be announced from time to time.`n`n`bTo give presents`b`nMost of the items in the Hunter's Lodge can be given to other players.  To give a present, view a player's Bio page (by clicking on their name in chat, or by searching for them in the Contestants List) and look for the gifting/trading link.`n`n`bAbout gift-wrapping`b`nItems that can be given as presents can also be gift-wrapped at the time of purchase for one extra Supporter Point.  Gift-wrapping an item on Improbable Island does the same thing that gift-wrapping an item does in real life - it doesn't bestow any tangible benefits, it's just a nice surprise for the giftee.");
			addnav("Okay");
			addnav("Back to the Hunter's Lodge","runmodule.php?module=iitems_hunterslodge&op=start");
			addnav("Referrals","runmodule.php?module=iitems_hunterslodge&op=referrals");
		break;
		case "referrals":
			output("If you help bring new players to Improbable Island, you'll earn one Supporter Point every time those players hit a new Game Day.  To refer players, use this address:`n`n");
			$url = getsetting("serverurl","http://".$_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT']==80?"":":".$_SERVER['SERVER_PORT']) . dirname($_SERVER['REQUEST_URI']));
			if (!preg_match("/\\/$/", $url)) {
				$url = $url . "/";
				savesetting("serverurl", $url);
			}
			output_notl("%shome.php?r=%s`n`n",$url,rawurlencode($session['user']['login']));
			$sql = "SELECT name FROM " . db_prefix("accounts") . " WHERE referer={$session['user']['acctid']} ORDER BY dragonkills,level";
			$result = db_query($sql);
			$number=db_num_rows($result);
			if ($number){
				output("Accounts you've referred:`n");
				for ($i=0;$i<$number;$i++){
					$row = db_fetch_assoc($result);
					output_notl("%s`0`n",$row['name']);
				}
			}
			addnav("Okay");
			addnav("Back to the Hunter's Lodge","runmodule.php?module=iitems_hunterslodge&op=start");
		break;
		case "start":
			output("You head into the Hunter's Lodge.  It's a bright, shiny place, with many expensive-looking items arranged inside glass cabinets.  The proprietor, a snappily-dressed male Joker, grins at you from behind the counter.`n`n");
			
			if (!has_item("lodgebag")){
				output("The joker silently hands you a small bag, with a look that suggests you put your purchases inside.`n`n");
				give_item("lodgebag");
			}
			
			output("`0You have `b`5%s`0`b Supporter Points left, out of `5%s`0 accumulated in total.`n`n",number_format($pointsleft),number_format($pointstotal));
			
			addnav("What's all this about, then?");
			addnav("How do I get supporter points, or give presents to people?","runmodule.php?module=iitems_hunterslodge&op=explain");
			
			$lodgeitems = get_items_with_settings("lodge");
			//debug($lodgeitems);
			
			rawoutput("<table width=100% style='border: dotted 1px #000000;'>");
			
			$boughtitems = array();
			$sql = "SELECT * FROM ".db_prefix("purchaselog")." WHERE acctid = '".$session['user']['acctid']."'";
			$result = db_query($sql);
			while ($row = db_fetch_assoc($result)){
				$boughtitems[$row['purchased']]=1;
			}
			
			foreach($lodgeitems AS $key=>$vals){
				$classcount+=1;
				$class=($classcount%2?"trdark":"trlight");
				rawoutput("<tr class='$class'><td>");
				if ($vals['image']) rawoutput("<table width=100% cellpadding=0 cellspacing=0><tr><td width=100px align=center><img src=\"images/items/".$vals['image']."\"></td><td>");
				output("`b%s`b`n",stripslashes($vals['verbosename']));
				output("%s`0`n",stripslashes($vals['description']));
				if ($vals['weight']){
					output("Weight: %s kg`n`0",$vals['weight']);
				}
				rawoutput("<table width=100%><tr><td width=50%>");
				if ($vals['tradable']){
					output("`5Giftable Item`0`n");
				}
				if ($vals['lodge_cost']){
					$disp = "`b`5".number_format($vals['lodge_cost'])."`0`b Supporter Points";
					$sdisp = $vals['lodge_cost']." Points";
					output("Price: %s`n",$disp);
					if ($vals['lodge_limited']){
						output("`4Limited Item`0: %s available`n",$vals['lodge_stock']);
					}
				}
				
				if (($vals['lodge_limited'] && $vals['lodge_stock'] > 0) || !$vals['lodge_limited']){
					if ($vals['lodge_singlebuy'] && $boughtitems[$key]){
						output("`7You've already obtained and used this item.  No need to get it again!`n");
					} else if ($pointstotal >= $vals['lodge_freebie_threshold'] && $vals['lodge_freebie_threshold']){
						output("`2This item is now `bfree`b!`0`n");
						addnav("","runmodule.php?module=iitems_hunterslodge&op=buy&item=".$key."&freebie=true");
						rawoutput("<a href=\"runmodule.php?module=iitems_hunterslodge&op=buy&item=".$key."&freebie=true\">Get it!</a><br />");
					} else {
						if ($vals['lodge_freebie_threshold']){
							output("`2This item becomes free once you have accumulated more than %s Supporter Points, spent or unspent.`0`n",$vals['lodge_freebie_threshold']);
						}
						if ($pointsleft >= $vals['lodge_cost']){
							addnav("Buy Items");
							addnav(array("%s (%s)",stripslashes($vals['verbosename']),$sdisp),"runmodule.php?module=iitems_hunterslodge&op=buy&item=".$key, true);
							addnav("","runmodule.php?module=iitems_hunterslodge&op=buy&item=".$key);
							rawoutput("<a href=\"runmodule.php?module=iitems_hunterslodge&op=buy&item=".$key."\">More Details / Buy for ".appoencode($disp)."</a><br />");
						} else {
							output("`&You need another %s Supporter Points for this item.`0`n",number_format($vals['lodge_cost']-$pointsleft));
							addnav("","runmodule.php?module=iitems_hunterslodge&op=buy&item=".$key."&cannotafford=1");
							rawoutput("<a href=\"runmodule.php?module=iitems_hunterslodge&op=buy&item=".$key."&cannotafford=1\">More Details</a><br />");
						}
					}
				} else {
					output("`&Sold out!`0`n");
				}
				
				rawoutput("</td></tr></table>");
				if ($vals['image']) rawoutput("</td></tr></table>");
				rawoutput("</td></tr>");
			}
			rawoutput("</td></tr></table>");
		break;
		case "buy":
			$itemid = httpget("item");
			$item = get_item_settings($itemid);
			$free = httpget("freebie");
			$cannotafford = httpget("cannotafford");
			//debug($item);
			if ($item['lodge_longdesc']){
				output_notl("`0%s`0`n`n",stripslashes($item['lodge_longdesc']));
			}
			if (!$cannotafford){
				if (!$free){
					output("You're about to buy a %s for %s Supporter Points, leaving you with %s points available to spend afterwards.`n`n",$item['verbosename'],number_format($item['lodge_cost']),number_format($pointsleft-$item['lodge_cost']));
					addnav("Confirm");
					addnav(array("Buy %s for %s Points",stripslashes($item['verbosename']),$item['lodge_cost']),"runmodule.php?module=iitems_hunterslodge&op=buyconfirm&item=$itemid");
					addnav("No, I don't want to do that!");
					addnav("Back to the Lodge","runmodule.php?module=iitems_hunterslodge&op=start");
				} else {
					redirect("runmodule.php?module=iitems_hunterslodge&op=buyconfirm&item=$itemid&freebie=true");
				}
			} else {
				output("`0You need another %s Supporter Points to get this item.`0`n`n",number_format($item['lodge_cost']-$pointsleft));
				addnav("Okay");
				addnav("Back to the Lodge","runmodule.php?module=iitems_hunterslodge&op=start");
				addnav("How do I get supporter points?","runmodule.php?module=iitems_hunterslodge&op=explain");
			}
		break;
		case "buyconfirm":
			$itemid = httpget("item");
			$iteminside = httpget("item_inside");
			$item = get_item_settings($itemid);
			$free = httpget("freebie");
			if (!$free){
				$cost = $item['lodge_cost'];
				output("You have purchased a %s for %s Supporter Points.  You can find the item in your Inventory.  Thank you for your support!`n`n",$item['verbosename'],$item['lodge_cost']);
				give_item($itemid);
				$session['user']['donationspent']+=$item['lodge_cost'];
			} else {
				//handle freebie items
				give_item($itemid);
				output("`0You now have a %s`0!  Thank you for your support!`n`n",$item['verbosename']);
				$cost = 0;
			}
			if ($item['lodge_limited']){
				increment_item_setting("lodge_stock",-1,$itemid);
			}
			//log purchase
			$sql = "INSERT INTO ".db_prefix("purchaselog")." (acctid,purchased,amount,data,giftwrap,timestamp) VALUES ('" . $session['user']['acctid'] . "','" . $itemid . "','" . $cost . "','" . addslashes(serialize($item)) . "','" . $wrap . "','" . date("Y-m-d H:i:s") ."')";
			//debug($sql);
			db_query($sql);
			addnav("Yay!");
			addnav("Back to the Hunter's Lodge","runmodule.php?module=iitems_hunterslodge&op=start");
		break;
	}

	addnav("Leave");
	addnav("Return to the Outpost","village.php");
	addnav("Inventory");
	addnav("View your Inventory","inventory.php?items_context=lodge");

	page_footer();
	
	return true;
}
?>