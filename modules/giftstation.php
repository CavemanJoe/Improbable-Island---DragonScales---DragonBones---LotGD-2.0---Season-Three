<?php

function giftstation_getmoduleinfo(){
	$info=array(
		"name"=>"Item Gifting Station",
		"version"=>"2010-09-14",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"Items",
		"download"=>"",
	);
	return $info;
}

function giftstation_install(){
	module_addhook("gardens");
	module_addhook("newday");
	return true;
}

function giftstation_uninstall(){
	return true;
}

function giftstation_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "gardens":
			addnav("Gifting Station","runmodule.php?module=giftstation&op=start");
			break;
		case "newday":
			$gifts = load_inventory("giftstation_".$session['user']['acctid'],true);
			if (count($gifts)>0){
				debug($gifts);
				foreach($gifts AS $boxid => $prefs){
					change_item_owner($boxid,$session['user']['acctid']);
				}
				output("`5`b`nThere's a present here!`b  How awesome!  You pick it up and put it in your Backpack.`0`n`n");
			}
			break;
	}
	return $args;
}

function giftstation_run(){
	global $session;
	page_header("The Gifting Station");
	$op=httpget('op');
	$points = $session['user']['donation']-$session['user']['donationspent'];
	
	switch ($op){
		case "start":
			if (httpget("subop")=="continue"){
				output("The KittyMorph shows you a big smile, closing the box and beginning to wrap it up.  \"`1Okay, that'll be delivered first thing in the morning.  Should make for a nice surprise when they wake up.  All gifts are anonymous by default, so if you want them to know who sent the package, you'd better send them a Distraction.  Anyone else you'd like to send a gift to?`0\"`n`n");
				
				$player = httpget('player');
				$itemid = httpget('item');
				$box = httpget('box');
				$free = httpget('free');
				
				$boxid = give_item($box,false,"giftstation_".$player);
				change_item_owner($itemid,$boxid);
				set_item_pref("giftbox_contains",$itemid,$boxid);
				
				if (!$free){
					$session['user']['donationspent']+=1;
					$points = $session['user']['donation']-$session['user']['donationspent'];
				}
				
				addnav("No, I think that'll do, thanks.","gardens.php");
			} else if (httpget("subop")=="searchagain"){
				output("The KittyMorph nods, scratching out the contestant's name on his clipboard.  \"`1No problem.  Let's take it from the top.`0\"`n`n");
				addnav("Actually, forget the whole thing.","gardens.php");
			} else {
				output("`0You head into the Gift Exchange.  A black-furred KittyMorph stands behind a counter.`n`n\"`1Hey there!`0\" he says.  \"`1As part of our continued diplomatic efforts, we provide a gift-wrapping service for items you'd like to send to your fellow contestants.  Interested?`0\"`n`n");
				output("It costs one Supporter Point to gift-wrap and transfer (nearly) any item to any player.  You currently have %s Supporter Points.`n`n",number_format($points));
				addnav("No thanks.","gardens.php");
			}
			if ($points > 0){
				output("Search below for a player to send a present to.");
				rawoutput("<form action='runmodule.php?module=giftstation&op=findplayer' method='post'>");
				rawoutput("<input name='name'>");
				rawoutput("<input type='submit' class='button' value='".translate_inline("search")."'>");
				rawoutput("</form>");
				addnav("","runmodule.php?module=giftstation&op=findplayer");
			} else {
				output("You don't have any Supporter Points!`n`n");
			}
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
					addnav("","runmodule.php?module=giftstation&op=chooseitem&player=".$row['acctid']);
					output_notl("<a href='runmodule.php?module=giftstation&op=chooseitem&player=".$row['acctid']."'>".$row['name']."</a>",true);
					output("`n");
					$matches++;
				}
			}
			if($matches == 0 ){
				output("He looks down at his list.  \"`1Oh.  Actually, it doesn't look like there's `ianyone`i matching that description who you can send a present to.  Bummer.  Wanna try that again?`0\"");
			}
			addnav("Return to Common Ground","gardens.php");
			addnav("Search Again","runmodule.php?module=giftstation&op=start");
			break;
		case "chooseitem":
			$player = httpget('player');
			
			output("The KittyMorph reaches below his counter and brings up a clipboard on which he writes down the name of your nominated contestant.  \"`1Okay, I've got the name - now, what would you like to send?`0\"`n`n");

			$giftables = get_items_with_prefs("giftable");
			$giftables = group_items($giftables);
			foreach($giftables AS $itemid => $prefs){
				if ($prefs['freegift']){
					addnav("Send for free");
					addnav(array("%s (%s available)",$prefs['verbosename'],$prefs['quantity']),"runmodule.php?module=giftstation&op=choosewrapping&item=$itemid&player=$player&free=true");
				} else {
					addnav("Send for one Supporter Point");
					addnav(array("%s (%s available)",$prefs['verbosename'],$prefs['quantity']),"runmodule.php?module=giftstation&op=choosewrapping&item=$itemid&player=$player&free=false");
				}
			}
			addnav("Cancel?");
			addnav("Wait, I've changed my mind.  Let's back up a step.","runmodule.php?module=giftstation&op=start&subop=searchagain");
			addnav("Actually, forget the whole thing.","gardens.php");
			break;
		case "choosewrapping":
			$player = httpget('player');
			$itemid = httpget('item');
			$free = httpget('free');
			output("The KittyMorph nods, taking the item from you and beginning to wrap it in tissue paper.  \"`1They should be pleased with that.  Care to choose a box, while I wrap this up?`0\"`n`nClick on any box to confirm the gift.`n`n");
			$boxes = get_items_with_settings("giftwrap");
			foreach($boxes AS $boxid => $prefs){
				$img = $prefs['image'];
				rawoutput("<a href='runmodule.php?module=giftstation&op=start&subop=continue&player=$player&item=$itemid&box=$boxid&free=$free'><img src='images/items/$img'></a>");
				addnav("","runmodule.php?module=giftstation&op=start&subop=continue&player=$player&item=$itemid&box=$boxid&free=$free");
			}
			addnav("Cancel?");
			addnav("Wait, I've changed my mind.  Let's back up a step.","runmodule.php?module=giftstation&op=start&subop=searchagain");
			addnav("Actually, forget the whole thing.","gardens.php");
		break;
	}

	page_footer();
	
	return true;
}
?>