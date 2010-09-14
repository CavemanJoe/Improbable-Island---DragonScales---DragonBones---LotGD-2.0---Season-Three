<?php

function iitems_eboy_gypsy_getmoduleinfo(){
	$info=array(
		"name"=>"IItems - eBoy's Trading Station Gypsy Integration",
		"version"=>"2009-08-04",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"IItems",
		"download"=>"",
		"settings"=>array(
			"Comms Hut eBoy Spying,title",
			"cost"=>"Cost in cigarettes to see all of eBoy's prices,int|2",
		),
	);
	return $info;
}

function iitems_eboy_gypsy_install(){
	module_addhook("gypsy");
	return true;
}

function iitems_eboy_gypsy_uninstall(){
	return true;
}

function iitems_eboy_gypsy_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "gypsy":
			$cost = get_module_setting("cost");
			if ($cost==1){
				$p = "Cigarette";
			} else {
				$p = "Cigarettes";
			}
			addnav(array("Check prices at eBoy's (%s %s)",$cost,$p),"runmodule.php?module=iitems_eboy_gypsy");
			output("`n`nYou can hear a metallic sound from somewhere beneath the desk.  It sounds almost like eBoy's price board.  Sensing your interest, the old man explains: \"`!I can tell you straight away the prices of all items for sale, across every Outpost!  A canny adventurer could make themselves a `ilot`i of money playing the old buy-sell game between Outposts.  Only %s %s for all the info!`5\"",$cost,$p);
		break;
	}
	return $args;
}

function iitems_eboy_gypsy_run(){
	global $session;
	page_header("eBoy's Price Chart");
	require_once "modules/iitems/lib/lib.php";
	
	$cost = get_module_setting("cost","iitems_eboy_gypsy");
	
	if ($cost==1){
		$p = "Cigarette";
	} else {
		$p = "Cigarettes";
	}
	
	if ($session['user']['gems'] >= $cost){
		$session['user']['gems'] -= $cost;
		output("`5You hand over the %s and the old man chuckles.  \"`!Aaah, thank ye kindly.  Now, here we are!  I'd recommend you write these down.  Now mark my words - these prices fluctuate by the `iminute`i!  That eBoy, he's a crafty bastard, y'see.  He sells things for whatever people are willing to buy them for, and not one penny less!  Even if that means putting his prices up the `isecond`i someone buys something!`5\"`n`nHe reaches under his desk and brings up a clattering rectangular machine made out of wood and brass, about the size of a fat telephone directory.  A radio antenna protrudes from one corner.`n`nTrue to his word, the spinning reels show the prices of every commodity in every outpost.  You spend a few minutes studying the readout.`n`n",$p);
		$sql = "select * from ".db_prefix("cityprefs");
		$result=db_query($sql);
		for ($i = 0; $i < db_num_rows($result); $i++){
			$row = db_fetch_assoc($result);
			$cid = $row['cityid'];
			$name = $row['cityname'];
			$eboy = unserialize(get_module_objpref("city",$cid,"eboytrades-intelligent","iitems_eboy_intelligent"));
			output("`b`0%s`b`n",$name);
			rawoutput("<table border=0 cellpadding=3 cellspacing=2><tr class=\"trdark\"><td>Item</td><td>Buying at</td><td>Selling at</td><td>Stock</td></tr>");
			$classcount = 1;
			foreach($eboy AS $key => $details){
				$itemdetails = iitems_get_item_details($key);
				if ($details['price'] < 10) $details['price'] = 10;
				$eboy[$key]['price'] = $details['price'];

				if ($details['stock'] < 3){
					$buy = round($details['price']*0.5);
				} else {
					$buy = round($details['price']*0.7);
				}

				$classcount++;
				$class=($classcount%2?"trdark":"trlight");
				$dname = $itemdetails['verbosename'];
				$dsell = number_format($details['price']);
				$dbuy = number_format($buy);
				$dstock = number_format($details['stock']);
				rawoutput("<tr class='$class'><td>$dname</td><td align=\"center\">$dbuy</td><td align=\"center\">$dsell</td><td align=\"center\">$dstock</td></tr>");
			}
			rawoutput("</table>");
			output("`n`n");
		}
	} else {
		output("`5You enthusiastically agree to the price, before realising that you don't actually have that many cigarettes.  Whoops.");
	}
	
	addnav("Leave");
	addnav("Return to the Outpost","village.php");
	
	page_footer();
	
	return true;
}
?>