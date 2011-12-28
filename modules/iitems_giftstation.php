<?php

function iitems_giftstation_getmoduleinfo(){
	$info=array(
		"name"=>"IItems - Gifting Station",
		"version"=>"2009-06-07",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"IItems",
		"download"=>"",
		"settings"=>array(
			"itemlimit"=>"Number of items a player can receive in one day,int|5",
			"weightlimit"=>"Total weight of items a player can receive in one day,float|3",
			),
	);
	return $info;
}

function iitems_giftstation_install(){
	module_addhook("village");
	module_addhook("iitems-superuser");
	module_addhook("newday");
	module_addhook("iitems_inventory_from");
	return true;
}

function iitems_giftstation_uninstall(){
	return true;
}

function iitems_giftstation_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "village":
			tlschema($args['schemas']['marketnav']);
			addnav($args['marketnav']);
			tlschema();
			addnav("Gifting Station","runmodule.php?module=iitems_giftstation&op=start");
			break;
		case "iitems-superuser":
			output("`n`bIItems: Gifting Station`b`n");
			output("`bgiftable`b: Allows the item to be gifted from one player to another.`n");
			break;
		case "newday":
			require_once "modules/iitems/lib/lib.php";
			//transfer items from gift inventory to main inventory
			$inventory = iitems_get_player_inventory();
			$giftgiven = 0;
			$gifts=array();
			foreach($inventory AS $key => $details){
				if ($details['inventorylocation']=="giftstation"){
					$giftgiven=1;
					iitems_transfer_item($key,"main");
					$gifts[$details['verbosename']]++;
				}
			}
			if ($giftgiven){
				output("`0`c`bSomething Awesome!`b`cYou wake up to find a shoebox-sized box by your feet.  Curious, and perhaps a little nervous - knowing that it could very well be full of spiders or something else awful - you open it.  Inside, you find:`n");
				foreach($gifts AS $giftname => $val){
					output("%s x %s`n",$giftname, $val);
				}
				output("How awesome!`n`n");
			}
			break;
		case "iitems_inventory_from":
			if (httpget('from')=="giftstation"){
				addnav("Return to the Gifting Station","runmodule.php?module=iitems_giftstation");
			}
			break;
	}
	return $args;
}

function iitems_giftstation_run(){
	global $session;
	page_header("The Gifting Station");
	require_once "modules/iitems/lib/lib.php";
	$inventory = iitems_get_player_inventory();
	$op=httpget('op');
	$itemlimit = get_module_setting("itemlimit");
	$weightlimit = get_module_setting("weightlimit");
	
	switch ($op){
		case "start":
			if (httpget("subop")=="continue"){
				output("The KittyMorph shows you a big smile, closing the box and beginning to wrap it up.  \"`1Okay, that'll be delivered first thing in the morning.  Should make for a nice surprise when they wake up.  All gifts are anonymous by default, so if you want them to know who sent the package, you'd better send them a Distraction.  Anyone else you'd like to send a gift to?`0\"`n`n");
				addnav("No, I think that'll do, thanks.","village.php");
			} else if (httpget("subop")=="searchagain"){
				output("The KittyMorph nods, scratching out the contestant's name on his clipboard.  \"`1No problem.  Let's take it from the top.`0\"`n`n");
				addnav("Actually, forget the whole thing.","village.php");
			} else {
				output("`0You head into the Gift Exchange.  A black-furred KittyMorph stands behind a counter.`n`n\"`1Hey there!`0\" he says.  \"`1As part of our continued diplomatic efforts, we provide a free gift-wrapping service for any consumable items you'd like to send to your fellow contestants.  Interested?`0\"`n`n");
				addnav("No thanks.","village.php");
			}
			output("Search below for a player to send a present to.");
			rawoutput("<form action='runmodule.php?module=iitems_giftstation&op=findplayer' method='post'>");
			rawoutput("<input name='name'>");
			rawoutput("<input type='submit' class='button' value='".translate_inline("search")."'>");
			rawoutput("</form>");
			addnav("","runmodule.php?module=iitems_giftstation&op=findplayer");
			break;
		case "findplayer":
			$search="%";
			$n = httppost('name');
			for ($x=0;$x<strlen($n);$x++){
				$search .= substr($n,$x,1)."%";
			}
			$search=" AND name LIKE '".addslashes($search)."' ";
			$uid = $session['user']['uniqueid'];
			$lastip = $session['user']['lastip'];
			$sql = "SELECT uniqueid, acctid, name FROM " . db_prefix("accounts") . " WHERE locked=0 $search ORDER BY name DESC ";
			$result = db_query($sql);
			output("The KittyMorph nods.  \"`1Okay, let's have a look at who I've got written down here whose name sounds like that...`0\"`n`n");
			
			$matches = 0;
			for($i=0;$i < db_num_rows($result);$i++){
				$row = db_fetch_assoc($result);
				if ($row['uniqueid'] != $session['user']['uniqueid']){
					addnav("","runmodule.php?module=iitems_giftstation&op=give&id=".$row['acctid']."&sub=first");
					output_notl("<a href='runmodule.php?module=iitems_giftstation&op=give&id=".$row['acctid']."&sub=first'>".$row['name']."</a>",true);
					output("`n");
					$matches++;
				}
			}
			if($matches == 0 ){
				output("He looks down at his list.  \"`1Oh.  Actually, it doesn't look like there's `ianyone`i matching that description who you can send a present to.  Bummer.  Wanna try that again?`0\"");
			}
			addnav("Return to the Outpost","village.php");
			addnav("Search Again","runmodule.php?module=iitems_giftstation&op=start");
			break;
		case "give":
			$id = httpget('id');
			$item = httpget('item');
			//Check to see if this box already has something inside - calculate weight and number of items
			$inventory = iitems_get_player_inventory();
			$giftee_all = iitems_get_player_inventory($id);
			$giftee_gifts = array();
			$giftee_weight = 0;
			$giftee_items = 0;
			foreach ($giftee_all AS $key => $details){
				if ($details['inventorylocation']=="giftstation"){
					$giftee_gifts[] = $key;
					$giftee_items++;
					if ($details['weight']){
						$giftee_weight += $details['weight'];
					}
				}
			}
			
			if ($giftee_items < $itemlimit && $giftee_weight < $weightlimit){
				$okaytoadd = true;
			} else {
				$okaytoadd = false;
			}
			
			if (httpget('sub')=="first"){
				output("The KittyMorph reaches below his counter and brings up a package of roughly shoebox-like proportions, along with a clipboard on which he writes down the name of your nominated contestant.  \"`1Okay, I've got the name - here's their box.`0\"`n`n");
				if (!$giftee_items){
					//giftee has no items in their box
					output("\"`1It looks like their box is empty, so let's start filling it up.  We'll send up to %s items at a time, and the box will hold up to %s kilos in weight.`0\"",$itemlimit,$weightlimit);
				} else if ($okaytoadd){
					output("\"`1It looks like their box has something in already, but we can put some more in there.  Right now the box weighs %s kilos, and has %s items inside.  We'll send up to %s items at a time, and the box will hold up to %s kilos in weight.`0\"",$giftee_weight,$giftee_items,$itemlimit,$weightlimit);
				} else {
					output("The KittyMorph peeks inside.  \"`1Sorry - looks like this is full.  We only make one delivery a day, you see.  You can try coming back tomorrow, if you'd like?`0\"`n`n");
				}
				addnav("Wait, I've changed my mind.  Let's back up a step.","runmodule.php?module=iitems_giftstation&op=start&subop=searchagain");
				addnav("Actually, forget the whole thing.","village.php");
			} else {
				//now adding more items
				//check for overweight - shouldn't happen, but players may all come to the gifting screen at once
				if (($inventory[$item]['weight'] + $giftee_weight) <= $weightlimit){
					//we're just going to copy the items straight over into the player's Inventory, not do anything special with them, since at newday they'll be transferred over for us and the transfer will make them display properly.
					$additem = $inventory[$item];
					$additem['inventorylocation']="giftstation";
					unset($additem['instance']);
					unset($additem['quantity']);
					$giftee_all[]=$additem;
					set_module_pref("items", serialize($giftee_all), "iitems", $id);
					output("The KittyMorph takes the item, wraps it in tissue and places it gently in the box. \"`1Marvellous.  Anything else you've got that we can fit in there?`0\"");
					addnav("Nope.","runmodule.php?module=iitems_giftstation&op=start&subop=continue");
				} else {
					output("The KittyMorph looks down into the box and frowns. \"`1Hmm.  That's odd - something else has just appeared in there, and now I can't fit your present in the box.  How very strange!`0\"");
					addnav("Hmm.  Best start over again, I suppose.","runmodule.php?module=iitems_giftstation&op=start&subop=continue");
				}
			}
			
			$inventory = iitems_get_player_inventory();
			$giftee_all = iitems_get_player_inventory($id);
			$giftee_gifts = array();
			$giftee_weight = 0;
			$giftee_items = 0;
			foreach ($giftee_all AS $key => $details){
				if ($details['inventorylocation']=="giftstation"){
					$giftee_gifts[] = $key;
					$giftee_items++;
					if ($details['weight']){
						$giftee_weight += $details['weight'];
					}
				}
			}
			
			if ($giftee_items < $itemlimit && $giftee_weight < $weightlimit){
				$okaytoadd = true;
			} else {
				$okaytoadd = false;
			}
			
			if ($okaytoadd){
				$giftables = array();
				foreach ($inventory AS $key => $details){
					$iteminfo = iitems_get_item_details($details['itemid']);
					//check that the item is in an accessible inventorylocation
					$storageinfo = iitems_get_item_details($inventory[$details['inventorylocation']]['itemid']);
					if ($storageinfo['giftable'] && $iteminfo['giftable'] && $iteminfo['type']!="inventory") $giftables[$key]=$details;
				}
				foreach ($giftables AS $key => $details){
					if (($details['weight'] + $giftee_weight) <= $weightlimit){
						//item is light enough, add navs
						addnav("Items");
						if ($details['quantity'] > 1){
							addnav(array("Give %s (%s available)",$details['verbosename'],$details['quantity']),"runmodule.php?module=iitems_giftstation&op=give&item=".$key."&id=".$id, true);
						} else {
							addnav(array("Give %s",$details['verbosename']),"runmodule.php?module=iitems_giftstation&op=give&item=".$key."&id=".$id, true);
						}
					} else {
						addnav(array("%s is too heavy to give",$details['verbosename']),"", true);
					}
				}
			}
			
			break;
	}

	// addnav("Inventory");
	// addnav("View your Inventory","runmodule.php?module=iitems&op=inventory&from=giftstation");
	page_footer();
	
	return true;
}
?>