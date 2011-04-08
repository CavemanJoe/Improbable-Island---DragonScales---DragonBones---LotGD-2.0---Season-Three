<?php

function improbablehousing_mementos_getmoduleinfo(){
	$info=array(
		"name"=>"Improbable Housing: Memento Support",
		"version"=>"2011-03-08",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"Housing",
		"download"=>"",
	);
	return $info;
}

function improbablehousing_mementos_install(){
	module_addhook("improbablehousing_interior");
	return true;
}

function improbablehousing_mementos_uninstall(){
	return true;
}

function improbablehousing_mementos_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "improbablehousing_interior":
			$hid = $args['hid'];
			$rid = $args['rid'];
			$house = $args['house'];
			
			$items = get_items_with_prefs("memento_dwellingtrigger_".$hid."_".$rid);
			//debug($items);
			
			//group the items together in case there's multiple ones
			$texts = array();
			if (is_array($items) && count($items)){
				foreach($items AS $id=>$prefs){
					$texts[serialize($prefs["memento_dwellingtrigger_".$hid."_".$rid])] = $prefs["memento_dwellingtrigger_".$hid."_".$rid];
				}
				foreach($texts AS $text){
					output_notl("`0%s`0`n`n",stripslashes($text));
				}
			}
			
			if (improbablehousing_getkeytype($house,$rid)>=30){
				addnav("Donator Features");
				addnav("Memento Effects","runmodule.php?module=improbablehousing_mementos&op=start&hid=$hid&rid=$rid");
			}
		break;
	}
	return $args;
}

function improbablehousing_mementos_run(){
	global $session;
	page_header("Memento Effects");

	$hid = httpget("hid");
	$rid = httpget("rid");
	$itemid = httpget("itemid");
	require_once "modules/improbablehousing/lib/lib.php";
	$house = improbablehousing_gethousedata($hid);
	$roomname = $house['data']['rooms'][$rid]['name'];
	$roomdesc = $house['data']['rooms'][$rid]['desc'];
	
	$pointsavailable = $session['user']['donation'] - $session['user']['donationspent'];
	
	switch (httpget('op')){
		case "start":
			output("You can specify some Effect Text to be shown when a player with a specific Memento enters this room.  This costs one Supporter Point per two characters of Effect Text that you want to use.  The extra text cost is independent of the cost to create Mementos (so if you have thirty Mementos, all alike, you only pay once for the effect text).  For example, if you'd made a Memento called 'Bucket of Soapy Frogs' and gave it to someone, you could set things up so that a frog could leap out of the bucket and whisper a secret password to the bearer of this frog when they enter this room.  Each room can have a different thing happen with a different Memento, and each Memento can have something different happen in any given room - the only limit is your own imagination.`n`nHere's a list of all the Mementos that currently exist that were made by you, along with the Effect Text they'll output when the bearer enters this room.`n`n");
			//get all the current Mementos in the game that were made by this player
			$sql = "SELECT * FROM ".db_prefix("items_prefs")." WHERE setting='memento_author' AND value='".$session['user']['acctid']."'";
			$result = db_query($sql);
			$preload = array();
			while ($row = db_fetch_assoc($result)){
				$preload[$row['id']] = $row['id'];
			}
			load_item_prefs($preload);
			//debug($preload);
			
			//group them together by original memento
			$originals = array();
			foreach($preload AS $id){
				$original = get_item_pref("memento_originalitem",$id);
				if ($original){
					$originals[$original] = get_item_prefs($id);
				}
			}
			
			//debug($originals);
			
			rawoutput("<table width=100% style='border: dotted 1px #000000;'>");
			$classcount=1;
			foreach($originals AS $id => $prefs){
				$classcount++;
				$class=($classcount%2?"trdark":"trlight");
				rawoutput("<tr class='$class'><td>");
				output("`b%s`b`0`n",stripslashes($prefs['verbosename']));
				output("%s`0`n`n",stripslashes($prefs['description']));
				if ($prefs["memento_dwellingtrigger_".$hid."_".$rid]){
					output("`bExtra text for this room:`b`n%s`0`n`n",$prefs["memento_dwellingtrigger_".$hid."_".$rid]);
				}
				rawoutput("<a href='runmodule.php?module=improbablehousing_mementos&op=settext&itemid=".$id."&hid=".$hid."&rid=".$rid."'>Set dwelling text</a>");
				addnav("","runmodule.php?module=improbablehousing_mementos&op=settext&itemid=".$id."&hid=".$hid."&rid=".$rid);
				rawoutput("</td></tr>");
			}
			rawoutput("</table>");
			
			//okay it's getting late and you're fucking falling asleep.  Do this in the morning:
			/*
			
			Get the text that the player wants, and put it in set_item_pref("memento_dwellingtrigger_".$hid."_".$rid);
			Copy that pref to all the Mementos that are copies of the same original
			
			*/
			
		break;
		case "settext":
			require_once "lib/forms.php";
			output("You're about to set the Effect Text associated with this Memento, in this room, of this Dwelling.  When a player enters this room with this Memento, this text will be added on to the end of your room description.  You can use all the same colour and formatting codes that you use when Decorating.`n`n");
			//previewfield("newtext","`~",false,false,array("type"=>"textarea", "class"=>"input", "cols"=>"60", "rows"=>"9", "onKeyDown"=>"sizeCount(this);"));
			rawoutput("<form action='runmodule.php?module=improbablehousing_mementos&op=preview&itemid=".$itemid."&hid=".$hid."&rid=".$rid."' method='POST'>");
			addnav("","runmodule.php?module=improbablehousing_mementos&op=preview&itemid=".$itemid."&hid=".$hid."&rid=".$rid);
			previewfield_countup("newtext");
			rawoutput("<br /><input type=submit>");
			rawoutput("</form>");
			addnav("Back");
			addnav("Memento Effects Main Menu","runmodule.php?module=improbablehousing_mementos&op=start&hid=$hid&rid=$rid");
		break;
		case "preview":
			$newtext = httppost("newtext");
			output("The new Effect Text you've chosen to apply is shown below.`n`n-----`n%s`n-----`0`n`n`bRemember`b, this Effect Text applies `ionly`i to this particular Memento (%s`0), in conjunction with this particular room (%s`0).`n`n",$newtext,get_item_pref("verbosename",$itemid),$roomname);
			$cost = ceil(strlen($newtext)/2);
			output("Total cost: `5`b%s`b Supporter Points`0",$cost);
			if ($pointsavailable >= $cost){
				addnav("Continue");
				addnav("Buy it!","runmodule.php?module=improbablehousing_mementos&op=buy&itemid=".$itemid."&hid=".$hid."&rid=".$rid."&text=".urlencode($newtext));
			} else {
				addnav("Oh dear.");
				addnav("Not enough Supporter Points!","");
			}
			addnav("Back");
			addnav("Memento Effects Main Menu","runmodule.php?module=improbablehousing_mementos&op=start&hid=$hid&rid=$rid");
		break;
		case "buy":
			$newtext = httpget('text');
			debug($newtext);
			$newtext = urldecode($newtext);
			debug($newtext);
			$cost = ceil(strlen($newtext)/2);
			$session['user']['donationspent'] += $cost;
			
			//get all the items like this and apply the pref to all of them
			$sql = "SELECT * FROM ".db_prefix("items_prefs")." WHERE setting='memento_originalitem' AND value='".$itemid."'";
			$result = db_query($sql);
			while ($row = db_fetch_assoc($result)){
				set_item_pref("memento_dwellingtrigger_".$hid."_".$rid,$newtext,$row['id']);
			}
			
			set_item_pref("memento_dwellingtrigger_".$hid."_".$rid,$newtext,$itemid);
			output("Your new Effect Text is set!");
			addnav("Back");
			addnav("Memento Effects Main Menu","runmodule.php?module=improbablehousing_mementos&op=start&hid=$hid&rid=$rid");
		break;
	}
	addnav("Back");
	addnav("Return to the Dwelling","runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=$rid");

	page_footer();
	
	return true;
}
?>