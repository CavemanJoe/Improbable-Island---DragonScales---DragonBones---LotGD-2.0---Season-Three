<?php

function iitems_invshop_getmoduleinfo(){
	$info=array(
		"name"=>"Inventory Item Shop",
		"version"=>"2010-09-15",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"Items",
		"download"=>"",
	);
	return $info;
}

function iitems_invshop_install(){
	$condition = "if (\$session['user']['location'] == \"Improbable Central\") {return true;} else {return false;};";
	module_addhook("village",false,$condition);
	return true;
}

function iitems_invshop_uninstall(){
	return true;
}

function iitems_invshop_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "village":
			tlschema($args['schemas']['marketnav']);
			addnav($args['marketnav']);
			tlschema();
			addnav("The Luggage Hut","runmodule.php?module=iitems_invshop&op=start");
			break;
	}
	return $args;
}

function iitems_invshop_run(){
	global $session;
	page_header("The Luggage Hut");
	
	$backpackprefs = array(
		"carrieritem"=>"main",
	);
	$backpack = get_items_with_prefs($backpackprefs,true);
	// debug($backpack);
	
	foreach($backpack AS $key => $prefs){
		$currentbackpack = $prefs;
		$currentbackpackid = $key;
	}
	
	$bandolierprefs = array(
		"carrieritem"=>"fight",
	);
	$bandolier = get_items_with_prefs($bandolierprefs,true);
	// debug($bandolier);
	foreach($bandolier AS $key => $prefs){
		$currentbandolier = $prefs;
		$currentbandolierid = $key;
	}
	
	global $inventory;
	// debug($inventory);
	
	$tradein_main = round($currentbackpack['invshop_price'] * 0.6);
	$tradein_fight = round($currentbandolier['invshop_price'] * 0.6);
	
	switch (httpget('op')){
		case "start":
			output("`0You head into the Luggage Hut.  A tall woman in her mid-twenties greets you.  From the dimensions of her mullet, you suspect she may be related to Sheila.`n`n\"`2Well, hey there!  Name's Sharon, nice to meet you.  Here for some new luggage?  We've got three different types of Backpacks and Bandoliers available.  Here, take a look around.  I can give you a trade-in value of %s cigarettes on your old backpack, and %s on your old bandolier.`0\"`n`nYou look around the shop a little, finding three different types of Bandoliers and Backpacks on sale:`n`n`bStandard Backpack`b`nA bog-standard backpack made from reasonably good materials.  Comfortably holds up to twenty kilos, and costs ten cigarettes.`n`n`bImproved Backpack`b`nA more well-designed backpack with extra pockets and good balance.  Holds up to forty kilos, and costs fifty cigarettes.`n`n`bAdvanced Backpack`b`nA superior-quality backpack designed by the famous B. P. BackPack, who changed his name by deed poll after working in the backpack design business for sixty years.  The man's completely bonkers, but he knows how to design a damned good backpack.  Comfortably holds up to eighty kilos, and costs one hundred cigarettes.`n`n`n`bStandard Bandolier`b`nA slightly more advanced version of the Basic bandolier given to new recruits.  Holds up to five kilos and costs ten cigarettes.`n`n`bImproved Bandolier`b`nThis leather bandolier looks cool, and has more pockets to store more things to use in fights.  Holds up to seven kilos and costs fifty cigarettes.`n`n`bAdvanced Bandolier`b`nThe Rolls-Royce of bandoliers, these things only tend to get purchased by the serious combat nut.  Holds up to ten kilos, and costs one hundred cigarettes.",$tradein_main,$tradein_fight);
		break;
		case "buy":
			$item = httpget('buy');
			if ($item == "backpack2" || $item == "backpack3" || $item == "backpack4"){
				$invloc = "main";
				$tradein = $tradein_main;
			} else {
				$invloc = "fight";
				$tradein = $tradein_fight;
			}
			$price = get_item_setting("invshop_price",$item);
			//debug($price);
			
			if ($price > ($session['user']['gems'] + $tradein)){
				//todo
				output("Sharon shakes her head, her mullet swaying back and forth.  \"`2Sorry love - even with the trade-in, that's just not going to be enough.`0\"`n`nYou don't actually `ihave`i that many cigarettes.  Shame.");
			} else {
				$has = has_item($item);
				if ($has){
					output("Sharon smiles as she takes your cigarettes.  She pauses when she sees your old equipment.`n`n\"`2Uh, mate...  That's `iexactly`i the same as the one you're buying.  I'm not gonna take your cigs and then give you something that's no different to what you've already got - I'm not Sheila, you know?`0\"`n`nEmbarrassed, you nod, and take back your cigarettes.");
				} else {
					output("Sharon smiles as you hand over your cigarettes and your old equipment.  \"`2Great stuff.  Here you go, one lovely new shiny piece of luggage!  Treat it well, now!`0\"");
					$session['user']['gems'] += $tradein;
					$session['user']['gems'] -= $price;
					$price = $price - $tradein;
					if ($invloc == "main"){
						delete_item($currentbackpackid);
					} else if ($invloc == "fight"){
						delete_item($currentbandolierid);
					}
					give_item($item);
					debuglog("spent ".$price." cigarettes on ".$item." at the Luggage Hut.");
				}
			}
		break;
	}
	addnav("Backpacks");
	addnav("Buy Standard Backpack","runmodule.php?module=iitems_invshop&op=buy&buy=backpack2");
	addnav("Buy Improved Backpack","runmodule.php?module=iitems_invshop&op=buy&buy=backpack3");
	addnav("Buy Advanced Backpack","runmodule.php?module=iitems_invshop&op=buy&buy=backpack4");
	addnav("Bandoliers");
	addnav("Buy Standard bandolier","runmodule.php?module=iitems_invshop&op=buy&buy=bandolier2");
	addnav("Buy Improved bandolier","runmodule.php?module=iitems_invshop&op=buy&buy=bandolier3");
	addnav("Buy Advanced bandolier","runmodule.php?module=iitems_invshop&op=buy&buy=bandolier4");
	addnav("Other");
	addnav("Back to the Outpost","village.php");
	page_footer();
	
	return true;
}
?>