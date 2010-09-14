<?php

function improbablestufftrading_getmoduleinfo(){
	$info = array(
		"name"=>"Improbable Stuff Trading",
		"version"=>"2009-02-07",
		"author"=>"Dan Hall",
		"category"=>"Improbable",
		"download"=>"",
		"prefs-city"=>array(
			"smallmedkit-current"=>"Current cost for a Small Medkit,int|50",
			"smallmedkit-min"=>"Minimum cost for a Small Medkit,int|30",
			"smallmedkit-max"=>"Maximum cost for a Small Medkit,int|100",
			"smallmedkit-buypct"=>"What is the buying vs selling percentage of Small Medkits at this Outpost?,int|75",
			"smallmedkit-avail"=>"Number of Small Medkits in stock at this Outpost,int|10",
			"largemedkit-current"=>"Current cost for a largemedkit,int|50",
			"largemedkit-min"=>"Minimum cost for a largemedkit,int|60",
			"largemedkit-max"=>"Maximum cost for a largemedkit,int|200",
			"largemedkit-buypct"=>"What is the buying vs selling percentage of largemedkits at this Outpost?,int|75",
			"largemedkit-avail"=>"Number of Large Medkits in stock at this Outpost,int|10",
			"energydrink-current"=>"Current cost for a energydrink,int|50",
			"energydrink-min"=>"Minimum cost for a energydrink,int|25",
			"energydrink-max"=>"Maximum cost for a energydrink,int|180",
			"energydrink-buypct"=>"What is the buying vs selling percentage of energydrinks at this Outpost?,int|75",
			"energydrink-avail"=>"Number of Energy Drinks in stock at this Outpost,int|10",
			"nicotinegum-current"=>"Current cost for a nicotinegum,int|50",
			"nicotinegum-min"=>"Minimum cost for a nicotinegum,int|20",
			"nicotinegum-max"=>"Maximum cost for a nicotinegum,int|100",
			"nicotinegum-buypct"=>"What is the buying vs selling percentage of nicotinegum at this Outpost?,int|75",
			"nicotinegum-avail"=>"Number of Nicotine Gum in stock at this Outpost,int|10",
			"powerpill-current"=>"Current cost for a powerpill,int|50",
			"powerpill-min"=>"Minimum cost for a powerpill,int|500",
			"powerpill-max"=>"Maximum cost for a powerpill,int|1500",
			"powerpill-buypct"=>"What is the buying vs selling percentage of powerpill at this Outpost?,int|75",
			"powerpill-avail"=>"Number of Power Pills in stock at this Outpost,int|10",
			"teleporter-current"=>"Current cost for a teleporter,int|50",
			"teleporter-min"=>"Minimum cost for a teleporter,int|800",
			"teleporter-max"=>"Maximum cost for a teleporter,int|1200",
			"teleporter-buypct"=>"What is the buying vs selling percentage of teleporter at this Outpost?,int|75",
			"teleporter-avail"=>"Number of teleporters in stock at this Outpost,int|10",
			"banggrenade-current"=>"Current cost for a banggrenade,int|50",
			"banggrenade-min"=>"Minimum cost for a banggrenade,int|20",
			"banggrenade-max"=>"Maximum cost for a banggrenade,int|100",
			"banggrenade-buypct"=>"What is the buying vs selling percentage of banggrenade at this Outpost?,int|75",
			"banggrenade-avail"=>"Number of Bang Grenadesin stock at this Outpost,int|10",
			"whoomphgrenade-current"=>"Current cost for a whoomphgrenade,int|50",
			"whoomphgrenade-min"=>"Minimum cost for a whoomphgrenade,int|20",
			"whoomphgrenade-max"=>"Maximum cost for a whoomphgrenade,int|100",
			"whoomphgrenade-buypct"=>"What is the buying vs selling percentage of whoomphgrenade at this Outpost?,int|75",
			"whoomphgrenade-avail"=>"Number of Whoomph Grenades in stock at this Outpost,int|10",
			"zapgrenade-current"=>"Current cost for a zapgrenade,int|50",
			"zapgrenade-min"=>"Minimum cost for a zapgrenade,int|20",
			"zapgrenade-max"=>"Maximum cost for a zapgrenade,int|100",
			"zapgrenade-buypct"=>"What is the buying vs selling percentage of zapgrenade at this Outpost?,int|75",
			"zapgrenade-avail"=>"Number of Zap Grenades in stock at this Outpost,int|10",
			"repellentspray-current"=>"Current cost for a repellentspray,int|50",
			"repellentspray-min"=>"Minimum cost for a repellentspray,int|300",
			"repellentspray-max"=>"Maximum cost for a repellentspray,int|700",
			"repellentspray-buypct"=>"What is the buying vs selling percentage of repellentspray at this Outpost?,int|75",
			"repellentspray-avail"=>"Number of Monster Repellent Sprays in stock at this Outpost,int|10",
			"rationpack-current"=>"Current cost for a rationpack,int|50",
			"rationpack-min"=>"Minimum cost for a rationpack,int|100",
			"rationpack-max"=>"Maximum cost for a rationpack,int|500",
			"rationpack-buypct"=>"What is the buying vs selling percentage of rationpack at this Outpost?,int|75",
			"rationpack-avail"=>"Number of Ration Packs in stock at this Outpost,int|10",
			"improbabilitybomb-current"=>"Current cost for a improbabilitybomb,int|50",
			"improbabilitybomb-min"=>"Minimum cost for a improbabilitybomb,int|40",
			"improbabilitybomb-max"=>"Maximum cost for a improbabilitybomb,int|400",
			"improbabilitybomb-buypct"=>"What is the buying vs selling percentage of improbabilitybomb at this Outpost?,int|75",
			"improbabilitybomb-avail"=>"Number of Improbability Bombs in stock at this Outpost,int|10",
			"kittencard-current"=>"Current cost for a kittencard,int|50",
			"kittencard-min"=>"Minimum cost for a kittencard,int|200",
			"kittencard-max"=>"Maximum cost for a kittencard,int|1000",
			"kittencard-buypct"=>"What is the buying vs selling percentage of kittencard at this Outpost?,int|75",
			"kittencard-avail"=>"Number of Kitten Cards in stock at this Outpost,int|10",
			"bribe"=>"How much must the player bribe eBoy for the privelage of seeing the global rates card?,int|1500",
			"acceptingbribes"=>"Is eBoy accepting bribes today?,bool|0",
		),
	);
	return $info;
}
function improbablestufftrading_install(){
	module_addhook("village");
	module_addhook("newday-runonce");
	return true;
}
function improbablestufftrading_uninstall(){
	return true;
}
function improbablestufftrading_dohook($hookname,$args){
	global $session;
	switch($hookname){
	case "village":
		tlschema($args['schemas']['marketnav']);
		addnav($args['marketnav']);
		tlschema();
		addnav("eBoy's Trading Station","runmodule.php?module=improbablestufftrading&op=start");
		break;
	case "newday-runonce":
		//Scramble the prices
		//Set up a loop, one to seven
		for ($i=0; $i<=7; $i++){
			set_module_objpref("city", $i, "smallmedkit-current", e_rand(get_module_objpref("city",$cityid,"smallmedkit-min"),get_module_objpref("city",$cityid,"smallmedkit-max")));
			set_module_objpref("city", $i, "largemedkit-current", e_rand(get_module_objpref("city",$cityid,"largemedkit-min"),get_module_objpref("city",$cityid,"largemedkit-max")));
			set_module_objpref("city", $i, "energydrink-current", e_rand(get_module_objpref("city",$cityid,"energydrink-min"),get_module_objpref("city",$cityid,"energydrink-max")));
			set_module_objpref("city", $i, "nicotinegum-current", e_rand(get_module_objpref("city",$cityid,"nicotinegum-min"),get_module_objpref("city",$cityid,"nicotinegum-max")));
			set_module_objpref("city", $i, "powerpill-current", e_rand(get_module_objpref("city",$cityid,"powerpill-min"),get_module_objpref("city",$cityid,"powerpill-max")));
			set_module_objpref("city", $i, "teleporter-current", e_rand(get_module_objpref("city",$cityid,"teleporter-min"),get_module_objpref("city",$cityid,"teleporter-max")));
			set_module_objpref("city", $i, "banggrenade-current", e_rand(get_module_objpref("city",$cityid,"banggrenade-min"),get_module_objpref("city",$cityid,"banggrenade-max")));
			set_module_objpref("city", $i, "whoomphgrenade-current", e_rand(get_module_objpref("city",$cityid,"whoomphgrenade-min"),get_module_objpref("city",$cityid,"whoomphgrenade-max")));
			set_module_objpref("city", $i, "zapgrenade-current", e_rand(get_module_objpref("city",$cityid,"zapgrenade-min"),get_module_objpref("city",$cityid,"zapgrenade-max")));
			set_module_objpref("city", $i, "repellentspray-current", e_rand(get_module_objpref("city",$cityid,"repellentspray-min"),get_module_objpref("city",$cityid,"repellentspray-max")));
			set_module_objpref("city", $i, "rationpack-current", e_rand(get_module_objpref("city",$cityid,"rationpack-min"),get_module_objpref("city",$cityid,"rationpack-max")));
			set_module_objpref("city", $i, "improbabilitybomb-current", e_rand(get_module_objpref("city",$cityid,"improbabilitybomb-min"),get_module_objpref("city",$cityid,"improbabilitybomb-max")));
			set_module_objpref("city", $i, "kittencard-current", e_rand(get_module_objpref("city",$cityid,"kittencard-min"),get_module_objpref("city",$cityid,"kittencard-max")));
			set_module_objpref("city", $i, "bribe", e_rand(1000,3000));
			$bribable = e_rand(0,100);
			if ($bribable < 25){
				//eBoy is unbribable
				set_module_objpref("city", $i, "acceptingbribes", 0);
			} else if ($bribable > 75){
				//eBoy is Bribable - else no change from yesterday
				set_module_objpref("city", $i, "acceptingbribes", 1);
			}
		}
		break;
	}
	return $args;
}

function improbablestufftrading_run(){
	global $session;
	$op = httpget("op");
	
	//city prefs
	//God this is gonna be a mess, would be much better with a serialized item system, perhaps for S3...
	require_once("modules/cityprefs/lib.php");
	$cityid = get_cityprefs_cityid("location",$session['user']['location']);
	
	$smallmedkitcurrent = get_module_objpref("city",$cityid,"smallmedkit-current");
	$smallmedkitmax = get_module_objpref("city",$cityid,"smallmedkit-max");
	$smallmedkitmin = get_module_objpref("city",$cityid,"smallmedkit-min");
	$smallmedkitbuypct = get_module_objpref("city",$cityid,"smallmedkit-buypct");
	$smallmedkitbuy = round(($smallmedkitcurrent/100)*$smallmedkitbuypct);
	$smallmedkitavail = get_module_objpref("city",$cityid,"smallmedkit-avail");
	
	$largemedkitcurrent = get_module_objpref("city",$cityid,"largemedkit-current");
	$largemedkitmax = get_module_objpref("city",$cityid,"largemedkit-max");
	$largemedkitmin = get_module_objpref("city",$cityid,"largemedkit-min");
	$largemedkitbuypct = get_module_objpref("city",$cityid,"largemedkit-buypct");
	$largemedkitbuy = round(($largemedkitcurrent/100)*$largemedkitbuypct);
	$largemedkitavail = get_module_objpref("city",$cityid,"largemedkit-avail");
	
	$energydrinkcurrent = get_module_objpref("city",$cityid,"energydrink-current");
	$energydrinkmax = get_module_objpref("city",$cityid,"energydrink-max");
	$energydrinkmin = get_module_objpref("city",$cityid,"energydrink-min");
	$energydrinkbuypct = get_module_objpref("city",$cityid,"energydrink-buypct");
	$energydrinkbuy = round(($energydrinkcurrent/100)*$energydrinkbuypct);
	$energydrinkavail = get_module_objpref("city",$cityid,"energydrink-avail");

	$nicotinegumcurrent = get_module_objpref("city",$cityid,"nicotinegum-current");
	$nicotinegummax = get_module_objpref("city",$cityid,"nicotinegum-max");
	$nicotinegummin = get_module_objpref("city",$cityid,"nicotinegum-min");
	$nicotinegumbuypct = get_module_objpref("city",$cityid,"nicotinegum-buypct");
	$nicotinegumbuy = round(($nicotinegumcurrent/100)*$nicotinegumbuypct);
	$nicotinegumavail = get_module_objpref("city",$cityid,"nicotinegum-avail");
	
	$powerpillcurrent = get_module_objpref("city",$cityid,"powerpill-current");
	$powerpillmax = get_module_objpref("city",$cityid,"powerpill-max");
	$powerpillmin = get_module_objpref("city",$cityid,"powerpill-min");
	$powerpillbuypct = get_module_objpref("city",$cityid,"powerpill-buypct");
	$powerpillbuy = round(($powerpillcurrent/100)*$powerpillbuypct);
	$powerpillavail = get_module_objpref("city",$cityid,"powerpill-avail");
	
	$teleportercurrent = get_module_objpref("city",$cityid,"teleporter-current");
	$teleportermax = get_module_objpref("city",$cityid,"teleporter-max");
	$teleportermin = get_module_objpref("city",$cityid,"teleporter-min");
	$teleporterbuypct = get_module_objpref("city",$cityid,"teleporter-buypct");
	$teleporterbuy = round(($teleportercurrent/100)*$teleporterbuypct);
	$teleporteravail = get_module_objpref("city",$cityid,"teleporter-avail");
	
	$banggrenadecurrent = get_module_objpref("city",$cityid,"banggrenade-current");
	$banggrenademax = get_module_objpref("city",$cityid,"banggrenade-max");
	$banggrenademin = get_module_objpref("city",$cityid,"banggrenade-min");
	$banggrenadebuypct = get_module_objpref("city",$cityid,"banggrenade-buypct");
	$banggrenadebuy = round(($banggrenadecurrent/100)*$banggrenadebuypct);
	$banggrenadeavail = get_module_objpref("city",$cityid,"banggrenade-avail");
	
	$whoomphgrenadecurrent = get_module_objpref("city",$cityid,"whoomphgrenade-current");
	$whoomphgrenademax = get_module_objpref("city",$cityid,"whoomphgrenade-max");
	$whoomphgrenademin = get_module_objpref("city",$cityid,"whoomphgrenade-min");
	$whoomphgrenadebuypct = get_module_objpref("city",$cityid,"whoomphgrenade-buypct");
	$whoomphgrenadebuy = round(($whoomphgrenadecurrent/100)*$whoomphgrenadebuypct);
	$whoomphgrenadeavail = get_module_objpref("city",$cityid,"whoomphgrenade-avail");
	
	$zapgrenadecurrent = get_module_objpref("city",$cityid,"zapgrenade-current");
	$zapgrenademax = get_module_objpref("city",$cityid,"zapgrenade-max");
	$zapgrenademin = get_module_objpref("city",$cityid,"zapgrenade-min");
	$zapgrenadebuypct = get_module_objpref("city",$cityid,"zapgrenade-buypct");
	$zapgrenadebuy = round(($zapgrenadecurrent/100)*$zapgrenadebuypct);
	$zapgrenadeavail = get_module_objpref("city",$cityid,"zapgrenade-avail");
	
	$repellentspraycurrent = get_module_objpref("city",$cityid,"repellentspray-current");
	$repellentspraymax = get_module_objpref("city",$cityid,"repellentspray-max");
	$repellentspraymin = get_module_objpref("city",$cityid,"repellentspray-min");
	$repellentspraybuypct = get_module_objpref("city",$cityid,"repellentspray-buypct");
	$repellentspraybuy = round(($repellentspraycurrent/100)*$repellentspraybuypct);
	$repellentsprayavail = get_module_objpref("city",$cityid,"repellentspray-avail");
	
	$rationpackcurrent = get_module_objpref("city",$cityid,"rationpack-current");
	$rationpackmax = get_module_objpref("city",$cityid,"rationpack-max");
	$rationpackmin = get_module_objpref("city",$cityid,"rationpack-min");
	$rationpackbuypct = get_module_objpref("city",$cityid,"rationpack-buypct");
	$rationpackbuy = round(($rationpackcurrent/100)*$rationpackbuypct);
	$rationpackavail = get_module_objpref("city",$cityid,"rationpack-avail");
	
	$improbabilitybombcurrent = get_module_objpref("city",$cityid,"improbabilitybomb-current");
	$improbabilitybombmax = get_module_objpref("city",$cityid,"improbabilitybomb-max");
	$improbabilitybombmin = get_module_objpref("city",$cityid,"improbabilitybomb-min");
	$improbabilitybombbuypct = get_module_objpref("city",$cityid,"improbabilitybomb-buypct");
	$improbabilitybombbuy = round(($improbabilitybombcurrent/100)*$improbabilitybombbuypct);
	$improbabilitybombavail = get_module_objpref("city",$cityid,"improbabilitybomb-avail");
	
	$kittencardcurrent = get_module_objpref("city",$cityid,"kittencard-current");
	$kittencardmax = get_module_objpref("city",$cityid,"kittencard-max");
	$kittencardmin = get_module_objpref("city",$cityid,"kittencard-min");
	$kittencardbuypct = get_module_objpref("city",$cityid,"kittencard-buypct");
	$kittencardbuy = round(($kittencardcurrent/100)*$kittencardbuypct);
	$kittencardavail = get_module_objpref("city",$cityid,"kittencard-avail");

	$bribe = get_module_objpref("city",$cityid,"bribe");
	$acceptingbribes = get_module_objpref("city",$cityid,"acceptingbribes");
	
	page_header("eBoy's Trading Station");
	
	switch (httpget("op")){
		case "start":
			output("Already fearing the worst, you head into eBoy's Trading Station.`n`nYour fears are well-founded.  The place is packed with the heaving, sweaty bodies of the hardcore capitalist, shouting \"`iBuy, buy!`i\" and \"`iSell, sell!`i\" and \"`iPut down the chainsaw and let's talk about this!`i\"`n`neBoy himself - although you suspect that this place, like Mike's Chop Shop, is a franchise of which you'll find one in every outpost, so is his name really eBoy?  Whoever he is, he stands on an elevated section of floor behind a tall mahogany counter, grabbing money with one hand and tossing grenades and ration packs over his shoulder with the other.  His arms are a blur.  His speech is the unintelligible, rapid-fire gabble of a professional auctioneer.  His eyes bulge and swivel.  You know he's loving this.`n`n");
			break;
		case "buy":
			$item = httpget("item");
			if (get_module_objpref("city",$cityid,"".$item."-avail") <= 0){
				output("eBoy turns to you and gibbers.`n`n\"Sorrymatejustsoldthelastoneyougottabefasteritsthequickandthedeadaroundherepal.\"  He turns to the next customer in line, leaving you trying to piece together whatever it is that he just said.`n`n");
				break;
			}
			switch ($item){
				case "smallmedkit":
					increment_module_pref("item1number", 1, "improbablestuff");
					$session['user']['gold'] -= $smallmedkitcurrent;
					increment_module_objpref("city",$cityid,"smallmedkit-avail",-1);
					break;
				case "largemedkit":
					increment_module_pref("item2number", 1, "improbablestuff");
					$session['user']['gold'] -= $largemedkitcurrent;
					increment_module_objpref("city",$cityid,"largemedkit-avail",-1);
					break;
				case "energydrink":
					increment_module_pref("item3number", 1, "improbablestuff");
					$session['user']['gold'] -= $energydrinkcurrent;
					increment_module_objpref("city",$cityid,"energydrink-avail",-1);
					break;
				case "nicotinegum":
					increment_module_pref("item4number", 1, "improbablestuff");
					$session['user']['gold'] -= $nicotinegumcurrent;
					increment_module_objpref("city",$cityid,"nicotinegum-avail",-1);
					break;
				case "powerpill":
					increment_module_pref("item5number", 1, "improbablestuff");
					$session['user']['gold'] -= $powerpillcurrent;
					increment_module_objpref("city",$cityid,"powerpill-avail",-1);
					break;
				case "teleporter":
					increment_module_pref("item6number", 1, "improbablestuff");
					$session['user']['gold'] -= $teleportercurrent;
					increment_module_objpref("city",$cityid,"teleporter-avail",-1);
					break;
				case "banggrenade":
					increment_module_pref("item7number", 1, "improbablestuff");
					$session['user']['gold'] -= $banggrenadecurrent;
					increment_module_objpref("city",$cityid,"banggrenade-avail",-1);
					break;
				case "whoomphgrenade":
					increment_module_pref("item8number", 1, "improbablestuff");
					$session['user']['gold'] -= $whoomphgrenadecurrent;
					increment_module_objpref("city",$cityid,"whoomphgrenade-avail",-1);
					break;
				case "zapgrenade":
					increment_module_pref("item9number", 1, "improbablestuff");
					$session['user']['gold'] -= $zapgrenadecurrent;
					increment_module_objpref("city",$cityid,"zapgrenade-avail",-1);
					break;
				case "repellentspray":
					increment_module_pref("item10number", 1, "improbablestuff");
					$session['user']['gold'] -= $repellentspraycurrent;
					increment_module_objpref("city",$cityid,"repellentspray-avail",-1);
					break;
				case "rationpack":
					increment_module_pref("item11number", 1, "improbablestuff");
					$session['user']['gold'] -= $rationpackcurrent;
					increment_module_objpref("city",$cityid,"rationpack-avail",-1);
					break;
				case "improbabilitybomb":
					increment_module_pref("item12number", 1, "improbablestuff");
					$session['user']['gold'] -= $improbabilitybombcurrent;
					increment_module_objpref("city",$cityid,"improbabilitybomb-avail",-1);
					break;
				case "kittencard":
					increment_module_pref("item13number", 1, "improbablestuff");
					$session['user']['gold'] -= $kittencardcurrent;
					increment_module_objpref("city",$cityid,"kittencard-avail",-1);
					break;
			}
			output("You fight your way to the front of the crowd and shout your order up to eBoy, slapping your Requisition tokens down on the counter.  Before you can blink, your money is gone and the item you desired sits in its place.  You grab it hastily and stuff it into your backpack as eBoy turns to deal with the next customer.`n`n");
			break;
		case "sell":
			$item = httpget("item");
			switch ($item){
				case "smallmedkit":
					increment_module_pref("item1number", -1, "improbablestuff");
					$session['user']['gold'] += $smallmedkitbuy;
					increment_module_objpref("city",$cityid,"smallmedkit-avail",1);
					break;
				case "largemedkit":
					increment_module_pref("item2number", -1, "improbablestuff");
					$session['user']['gold'] += $largemedkitbuy;
					increment_module_objpref("city",$cityid,"largemedkit-avail",1);
					break;
				case "energydrink":
					increment_module_pref("item3number", -1, "improbablestuff");
					$session['user']['gold'] += $energydrinkbuy;
					increment_module_objpref("city",$cityid,"energydrink-avail",1);
					break;
				case "nicotinegum":
					increment_module_pref("item4number", -1, "improbablestuff");
					$session['user']['gold'] += $nicotinegumbuy;
					increment_module_objpref("city",$cityid,"nicotinegum-avail",1);
					break;
				case "powerpill":
					increment_module_pref("item5number", -1, "improbablestuff");
					$session['user']['gold'] += $powerpillbuy;
					increment_module_objpref("city",$cityid,"powerpill-avail",1);
					break;
				case "teleporter":
					increment_module_pref("item6number", -1, "improbablestuff");
					$session['user']['gold'] += $teleporterbuy;
					increment_module_objpref("city",$cityid,"teleporter-avail",1);
					break;
				case "banggrenade":
					increment_module_pref("item7number", -1, "improbablestuff");
					$session['user']['gold'] += $banggrenadebuy;
					increment_module_objpref("city",$cityid,"banggrenade-avail",1);
					break;
				case "whoomphgrenade":
					increment_module_pref("item8number", -1, "improbablestuff");
					$session['user']['gold'] += $whoomphgrenadebuy;
					increment_module_objpref("city",$cityid,"whoomphgrenade-avail",1);
					break;
				case "zapgrenade":
					increment_module_pref("item9number", -1, "improbablestuff");
					$session['user']['gold'] += $zapgrenadebuy;
					increment_module_objpref("city",$cityid,"zapgrenade-avail",1);
					break;
				case "repellentspray":
					increment_module_pref("item10number", -1, "improbablestuff");
					$session['user']['gold'] += $repellentspraybuy;
					increment_module_objpref("city",$cityid,"repellentspray-avail",1);
					break;
				case "rationpack":
					increment_module_pref("item11number", -1, "improbablestuff");
					$session['user']['gold'] += $rationpackbuy;
					increment_module_objpref("city",$cityid,"rationpack-avail",1);
					break;
				case "improbabilitybomb":
					increment_module_pref("item12number", -1, "improbablestuff");
					$session['user']['gold'] += $improbabilitybombbuy;
					increment_module_objpref("city",$cityid,"improbabilitybomb-avail",1);
					break;
				case "kittencard":
					increment_module_pref("item13number", -1, "improbablestuff");
					$session['user']['gold'] += $kittencardbuy;
					increment_module_objpref("city",$cityid,"kittencard-avail",1);
					break;
			}
			increment_module_pref("soldtoday", 1);
			output("You barge your way through the crowd like a battleship through an ice flow, and toss your item up to eBoy.  eBoy snatches the item out of the air with his left hand while tossing your Requisition tokens back at you with his right, and goes on to serve the next customer.`n`n");
			break;
	}
	
	//get the latest availability data
	$smallmedkitavail = get_module_objpref("city",$cityid,"smallmedkit-avail");
	$largemedkitavail = get_module_objpref("city",$cityid,"largemedkit-avail");
	$energydrinkavail = get_module_objpref("city",$cityid,"energydrink-avail");
	$nicotinegumavail = get_module_objpref("city",$cityid,"nicotinegum-avail");
	$powerpillavail = get_module_objpref("city",$cityid,"powerpill-avail");
	$teleporteravail = get_module_objpref("city",$cityid,"teleporter-avail");
	$banggrenadeavail = get_module_objpref("city",$cityid,"banggrenade-avail");
	$whoomphgrenadeavail = get_module_objpref("city",$cityid,"whoomphgrenade-avail");
	$zapgrenadeavail = get_module_objpref("city",$cityid,"zapgrenade-avail");
	$repellentsprayavail = get_module_objpref("city",$cityid,"repellentspray-avail");
	$rationpackavail = get_module_objpref("city",$cityid,"rationpack-avail");
	$improbabilitybombavail = get_module_objpref("city",$cityid,"improbabilitybomb-avail");
	$kittencardavail = get_module_objpref("city",$cityid,"kittencard-avail");
	
	//show info board
	output("You look up above eBoy's head at the trading board, the values of each commodity hastily scrawled in chalk early this morning:`n`n");
	output("`0Small Medkits:`n");
	output("Stock available: %s`n",$smallmedkitavail);
	output("`2Selling at: %s Requisition`n",$smallmedkitcurrent);
	output("`7Buying at: %s Requisition`0`n`n",$smallmedkitbuy);
	output("`0Large Medkits:`n");
	output("Stock available: %s`n",$largemedkitavail);
	output("`2Selling at: %s Requisition`n",$largemedkitcurrent);
	output("`7Buying at: %s Requisition`0`n`n",$largemedkitbuy);
	output("`0Energy Drinks:`n");
	output("Stock available: %s`n",$energydrinkavail);
	output("`2Selling at: %s Requisition`n",$energydrinkcurrent);
	output("`7Buying at: %s Requisition`0`n`n",$energydrinkbuy);
	output("`0Nicotine Gum:`n");
	output("Stock available: %s`n",$nicotinegumavail);
	output("`2Selling at: %s Requisition`n",$nicotinegumcurrent);
	output("`7Buying at: %s Requisition`0`n`n",$nicotinegumbuy);
	output("`0Power Pills:`n");
	output("Stock available: %s`n",$powerpillavail);
	output("`2Selling at: %s Requisition`n",$powerpillcurrent);
	output("`7Buying at: %s Requisition`0`n`n",$powerpillbuy);
	output("`0One-Shot Teleporters:`n");
	output("Stock available: %s`n",$teleporteravail);
	output("`2Selling at: %s Requisition`n",$teleportercurrent);
	output("`7Buying at: %s Requisition`0`n`n",$teleporterbuy);
	output("`0BANG Grenades:`n");
	output("Stock available: %s`n",$banggrenadeavail);
	output("`2Selling at: %s Requisition`n",$banggrenadecurrent);
	output("`7Buying at: %s Requisition`0`n`n",$banggrenadebuy);
	output("`0WHOOMPH Grenades:`n");
	output("Stock available: %s`n",$whoomphgrenadeavail);
	output("`2Selling at: %s Requisition`n",$whoomphgrenadecurrent);
	output("`7Buying at: %s Requisition`0`n`n",$whoomphgrenadebuy);
	output("`0ZAP Grenades:`n");
	output("Stock available: %s`n",$zapgrenadeavail);
	output("`2Selling at: %s Requisition`n",$zapgrenadecurrent);
	output("`7Buying at: %s Requisition`0`n`n",$zapgrenadebuy);
	output("`0Monster Repellent Spray:`n");
	output("Stock available: %s`n",$repellentsprayavail);
	output("`2Selling at: %s Requisition`n",$repellentspraycurrent);
	output("`7Buying at: %s Requisition`0`n`n",$repellentspraybuy);
	output("`0Ration Packs:`n");
	output("Stock available: %s`n",$rationpackavail);
	output("`2Selling at: %s Requisition`n",$rationpackcurrent);
	output("`7Buying at: %s Requisition`0`n`n",$rationpackbuy);
	output("`0Improbability Bombs:`n");
	output("Stock available: %s`n",$improbabilitybombavail);
	output("`2Selling at: %s Requisition`n",$improbabilitybombcurrent);
	output("`7Buying at: %s Requisition`0`n`n",$improbabilitybombbuy);
	output("`0Kitten Greetings Cards:`n");
	output("Stock available: %s`n",$kittencardavail);
	output("`2Selling at: %s Requisition`n",$kittencardcurrent);
	output("`7Buying at: %s Requisition`0`n`n",$kittencardbuy);

	//buying navs
	addnav("Buying");
	if ($session['user']['gold'] >= $smallmedkitcurrent && $smallmedkitavail > 0){
		addnav(array("Buy a Small Medkit (%s Requisition)",$smallmedkitcurrent),"runmodule.php?module=improbablestufftrading&op=buy&item=smallmedkit");
	}
	if ($session['user']['gold'] >= $largemedkitcurrent && $largemedkitavail > 0){
		addnav(array("Buy a Large Medkit (%s Requisition)",$largemedkitcurrent),"runmodule.php?module=improbablestufftrading&op=buy&item=largemedkit");
	}
	if ($session['user']['gold'] >= $energydrinkcurrent && $energydrinkavail > 0){
		addnav(array("Buy an Energy Drink (%s Requisition)",$energydrinkcurrent),"runmodule.php?module=improbablestufftrading&op=buy&item=energydrink");
	}
	if ($session['user']['gold'] >= $nicotinegumcurrent && $nicotinegumavail > 0){
		addnav(array("Buy a piece of Nicotine Gum (%s Requisition)",$nicotinegumcurrent),"runmodule.php?module=improbablestufftrading&op=buy&item=nicotinegum");
	}
	if ($session['user']['gold'] >= $powerpillcurrent && $powerpillavail > 0){
		addnav(array("Buy a Power Pill (%s Requisition)",$powerpillcurrent),"runmodule.php?module=improbablestufftrading&op=buy&item=powerpill");
	}
	if ($session['user']['gold'] >= $teleportercurrent && $teleporteravail > 0){
		addnav(array("Buy a One-Shot Teleporter (%s Requisition)",$teleportercurrent),"runmodule.php?module=improbablestufftrading&op=buy&item=teleporter");
	}
	if ($session['user']['gold'] >= $banggrenadecurrent && $banggrenadeavail > 0){
		addnav(array("Buy a Bang Grenade (%s Requisition)",$banggrenadecurrent),"runmodule.php?module=improbablestufftrading&op=buy&item=banggrenade");
	}
	if ($session['user']['gold'] >= $whoomphgrenadecurrent && $whoomphgrenadeavail > 0){
		addnav(array("Buy a WHOOMPH Grenade (%s Requisition)",$whoomphgrenadecurrent),"runmodule.php?module=improbablestufftrading&op=buy&item=whoomphgrenade");
	}
	if ($session['user']['gold'] >= $zapgrenadecurrent && $zapgrenadeavail > 0){
		addnav(array("Buy a ZAP Grenade (%s Requisition)",$zapgrenadecurrent),"runmodule.php?module=improbablestufftrading&op=buy&item=zapgrenade");
	}
	if ($session['user']['gold'] >= $repellentspraycurrent && $repellentsprayavail > 0){
		addnav(array("Buy a can of Monster Repellent Spray (%s Requisition)",$repellentspraycurrent),"runmodule.php?module=improbablestufftrading&op=buy&item=repellentspray");
	}
	if ($session['user']['gold'] >= $rationpackcurrent && $rationpackavail > 0){
		addnav(array("Buy a Ration Pack (%s Requisition)",$rationpackcurrent),"runmodule.php?module=improbablestufftrading&op=buy&item=rationpack");
	}
	if ($session['user']['gold'] >= $improbabilitybombcurrent && $improbabilitybombavail > 0){
		addnav(array("Buy an Improbability Bomb (%s Requisition)",$improbabilitybombcurrent),"runmodule.php?module=improbablestufftrading&op=buy&item=improbabilitybomb");
	}
	if ($session['user']['gold'] >= $kittencardcurrent && $kittencardavail > 0){
		addnav(array("Buy a Kitten Card (%s Requisition)",$kittencardcurrent),"runmodule.php?module=improbablestufftrading&op=buy&item=kittencard");
	}

	//player prefs
	$playersmallmedkit = get_module_pref("item1number", "improbablestuff");
	$playerlargemedkit = get_module_pref("item2number", "improbablestuff");
	$playerenergydrink = get_module_pref("item3number", "improbablestuff");
	$playernicotinegum = get_module_pref("item4number", "improbablestuff");
	$playerpowerpill = get_module_pref("item5number", "improbablestuff");
	$playerteleporter = get_module_pref("item6number", "improbablestuff");
	$playerbanggrenade = get_module_pref("item7number", "improbablestuff");
	$playerwhoomphgrenade = get_module_pref("item8number", "improbablestuff");
	$playerzapgrenade = get_module_pref("item9number", "improbablestuff");
	$playerrepellentspray = get_module_pref("item10number", "improbablestuff");
	$playerrationpack = get_module_pref("item11number", "improbablestuff");
	$playerimprobabilitybomb = get_module_pref("item12number", "improbablestuff");
	$playerkittencard = get_module_pref("item13number", "improbablestuff");	
	
	//selling navs
	addnav("Selling");
	if ($playersmallmedkit > 0){
		addnav(array("Sell a Small Medkit (%s remaining)",$playersmallmedkit),"runmodule.php?module=improbablestufftrading&op=sell&item=smallmedkit");
	}
	if ($playerlargemedkit > 0){
		addnav(array("Sell a Large Medkit (%s remaining)",$playerlargemedkit),"runmodule.php?module=improbablestufftrading&op=sell&item=largemedkit");
	}
	if ($playerenergydrink > 0){
		addnav(array("Sell an Energy Drink (%s remaining)",$playerenergydrink),"runmodule.php?module=improbablestufftrading&op=sell&item=energydrink");
	}
	if ($playernicotinegum > 0){
		addnav(array("Sell a piece of Nicotine Gum (%s remaining)",$playernicotinegum),"runmodule.php?module=improbablestufftrading&op=sell&item=nicotinegum");
	}
	if ($playerpowerpill > 0){
		addnav(array("Sell a Power Pill (%s remaining)",$playerpowerpill),"runmodule.php?module=improbablestufftrading&op=sell&item=powerpill");
	}
	if ($playerteleporter > 0){
		addnav(array("Sell a One-Shot Teleporter (%s remaining)",$playerteleporter),"runmodule.php?module=improbablestufftrading&op=sell&item=teleporter");
	}
	if ($playerbanggrenade > 0){
		addnav(array("Sell a Bang Grenade (%s remaining)",$playerbanggrenade),"runmodule.php?module=improbablestufftrading&op=sell&item=banggrenade");
	}
	if ($playerwhoomphgrenade > 0){
		addnav(array("Sell a WHOOMPH Grenade (%s remaining)",$playerwhoomphgrenade),"runmodule.php?module=improbablestufftrading&op=sell&item=whoomphgrenade");
	}
	if ($playerzapgrenade > 0){
		addnav(array("Sell a ZAP Grenade (%s remaining)",$playerzapgrenade),"runmodule.php?module=improbablestufftrading&op=sell&item=zapgrenade");
	}
	if ($playerrepellentspray > 0){
		addnav(array("Sell a can of Monster Repellent Spray (%s remaining)",$playerrepellentspray),"runmodule.php?module=improbablestufftrading&op=sell&item=repellentspray");
	}
	if ($playerrationpack > 0){
		addnav(array("Sell a Ration Pack (%s remaining)",$playerrationpack),"runmodule.php?module=improbablestufftrading&op=sell&item=rationpack");
	}
	if ($playerimprobabilitybomb > 0){
		addnav(array("Sell an Improbability Bomb (%s remaining)",$playerimprobabilitybomb),"runmodule.php?module=improbablestufftrading&op=sell&item=improbabilitybomb");
	}
	if ($playerkittencard > 0){
		addnav(array("Sell a Kitten Card (%s remaining)",$playerkittencard),"runmodule.php?module=improbablestufftrading&op=sell&item=kittencard");
	}
	
	addnav("Other");
	// todo: add eBoy bribery
	// addnav("Ask eBoy if he's got any insider tips...","runmodule.pnp?module=improbablestufftrading&op=bribe");
	addnav("Return to the Outpost","village.php");
	page_footer();
	return $args;
}
?>