<?php

require_once("lib/http.php");

function getimprobablestuff_getmoduleinfo(){
	$info = array(
		"name"=>"Get Improbable Stuff",
		"version"=>"2008-08-14",
		"author"=>"Dan Hall",
		"category"=>"Travel Specials",
		"download"=>"",
	);
	return $info;
}

function getimprobablestuff_install(){
	module_addeventhook("forest", "return 100;");
	module_addeventhook("travel", "return 100;");
	return true;
}

function getimprobablestuff_uninstall(){
	return true;
}

function getimprobablestuff_dohook($hookname,$args){
	return $args;
}

function getimprobablestuff_runevent($type,$link){
	global $session;
	$smallmedkit=get_module_pref("item1number","improbablestuff");
	$largemedkit=get_module_pref("item2number","improbablestuff");
	$energydrink=get_module_pref("item3number","improbablestuff");
	$nicotinegum=get_module_pref("item4number","improbablestuff");
	$potentpill=get_module_pref("item5number","improbablestuff");
	$teleporter=get_module_pref("item6number","improbablestuff");
	$banggrenade=get_module_pref("item7number","improbablestuff");
	$whoomphgrenade=get_module_pref("item8number","improbablestuff");
	$zapgrenade=get_module_pref("item9number","improbablestuff");
	$repellantspray=get_module_pref("item10number","improbablestuff");
	$rationpack = get_module_pref("item11number","improbablestuff");
	$improbabilitybomb = get_module_pref("item12number","improbablestuff");
	$kittencard = get_module_pref("item13number","improbablestuff");
	$eventtype=e_rand(0,100);
	if ($eventtype<75){
		//They found a single item
		$get=e_rand(1,13);
		if ($get==1){
			output("`0You found a `#Small Medkit!`0`n`n");
			output("A Small Medkit can be used in any Outpost, the Jungle while not fighting, or on the World Map.  It replenishes up to twenty hitpoints!");
			$smallmedkit++;
		}
		if ($get==2){
			output("`0You found a `#Large Medkit!`0`n`n");
			output("A Large Medkit can be used in any Outpost, the Jungle while not fighting, or on the World Map.  It replenishes up to sixty hitpoints!");
			$largemedkit++;
		}
		if ($get==3){
			output("`0You found an `#Energy Drink!`0`n`n");
			output("An Energy Drink can be used in any Outpost, the Jungle while not fighting, or on the World Map.  It grants a small amount of Stamina.");
			$energydrink++;
		}
		if ($get==4){
			output("`0You found a piece of `#Nicotine Gum!`0`n`n");
			output("Nicotine Gum can be used in any Outpost, the Jungle while not fighting, or on the World Map.  It temporarily alleviates the negative buffs that addicted players get when they don't smoke, and it doesn't make you more addicted or spoil a days-without-smoking record.");
			$nicotinegum++;
		}
		if ($get==5){
			output("`0You found a `#Power Pill!`0`n`n");
			output("These odd little pills can be used in any Outpost, the Jungle, during a fight, or on the World Map.  It instantly grants a large amount of Stamina and ten rounds of cellular regeneration!");
			$potentpill++;
		}
		if ($get==6){
			output("`0You found a `#One-Shot Teleporter!`0`n`n");
			output("A One-Shot Teleporter can be used in any Outpost, the Jungle, during a fight, or on the World Map.  It allows you to instantly teleport to an Outpost of your choosing, leaving your aggressors growling and muttering under their breath!");
			$teleporter++;
		}
		if ($get==7){
			output("`0You found a `#BANG Grenade!`0`n`n");
			output("A BANG Grenade can be used while fighting in the Jungle.  It does instant, hefty damage to any enemy!");
			$banggrenade++;
		}
		if ($get==8){
			output("`0You found a `#WHOOMPH Grenade!`0`n`n");
			output("A WHOOMPH Grenade can be used while fighting in the Jungle.  It sets your foe on fire!  Use it at the beginning of a fight for best results.");
			$whoomphgrenade++;
		}
		if ($get==9){
			output("`0You found a `#ZAP Grenade!`0`n`n");
			output("A ZAP Grenade can be used while fighting in the Jungle.  It blinds your opponent, letting you get the boot in without fear of retaliation for a few rounds!");
			$zapgrenade++;
		}
		if ($get==10){
			output("`0You found a can of `#Monster Repellant Spray!`0`n`n");
			output("Monster Repellant Spray can be used in any Outpost, the Jungle, while fighting, or on the World Map.  You can spray it on yourself in the Jungle, Outpost or World Map, or spray it directly onto a monster during combat.  When sprayed on yourself, it reduces your chances of being attacked by any monster on the World Map by twenty-five per cent, and any monsters you do come across won't be as inclined to get close enough to hit you very hard.  The effects last for the rest of the game day, and you can use as many cans per day as you'd like.  Each can used increases the effect.  When sprayed directly onto a monster during combat, the monster's combat efficacy is dramatically, albeit temporarily, reduced.`n`n");
			$repellantspray++;
		}
		if ($get==11){
			output("`0You found a `#Mobile Combat Trooper Individual Ration Pack!`0`n`n");
			output("Ration Packs contain everything a contestant needs to carry on fighting monsters!  A contestant can reliably eat nothing but Ration Packs and survive for several weeks before dying of malnutrition.  They are filling and contain very little fat.  Use them to restore your Stamina, but keep in mind their low nutritional quality - it's probably not a good idea to try to live on these alone.`n`n");
			$rationpack++;
		}
		if ($get==12){
			output("`0You found an `#Improbability Bomb!`0`n`n");
			output("Improbability Bombs can be used against monsters in combat.  Their effects are somewhat unpredictable, and can prove very dangerous to both contestant and monster.  Be careful with these.`n`n");
			$improbabilitybomb++;
		}
		if ($get==13){
			output("`0You found a `#greetings card with a picture of a kitten on it!`0`n`n");
			output("This is exactly the sort of thing that '\$The Watcher`0 goes nuts over.  Maybe she'd like it.`n`n");
			$kittencard++;
		}
	}
	if ($eventtype>=75){
		//They've found a crate, containing some or no items
		output("`0You stumble across a wooden crate, with a small parachute attached.  You spend a few minutes prying it open.`n`n");
		$get1=e_rand(0,100);
		$get2=e_rand(0,100);
		$get3=e_rand(0,100);
		$get4=e_rand(0,100);
		$get5=e_rand(0,100);
		$get6=e_rand(0,100);
		$get7=e_rand(0,100);
		$get8=e_rand(0,100);
		$get9=e_rand(0,100);
		$get10=e_rand(0,100);
		$get11=e_rand(0,100);
		$get12=e_rand(0,100);
		$get13=e_rand(0,100);
		$foundsomething=0;
		if ($get1>=80){
			output("`0You found a `#Small Medkit!`0`n`n");
			output("A Small Medkit can be used in any Outpost, the Jungle while not fighting, or on the World Map.  It replenishes up to twenty hitpoints!`n`n");
			$smallmedkit++;
			$foundsomething=1;
		}
		if ($get2>=80){
			output("`0You found a `#Large Medkit!`0`n`n");
			output("A Large Medkit can be used in any Outpost, the Jungle while not fighting, or on the World Map.  It replenishes up to sixty hitpoints!`n`n");
			$largemedkit++;
			$foundsomething=1;
		}
		if ($get3>=80){
			output("`0You found an `#Energy Drink!`0`n`n");
			output("An Energy Drink can be used in any Outpost, the Jungle while not fighting, or on the World Map.  It grants a small amount of Stamina.`n`n");
			$energydrink++;
			$foundsomething=1;
		}
		if ($get4>=80){
			output("`0You found a piece of `#Nicotine Gum!`0`n`n");
			output("Nicotine Gum can be used in any Outpost, the Jungle while not fighting, or on the World Map.  It temporarily alleviates the negative buffs that addicted contestants get when they don't smoke, and it doesn't make you more addicted or spoil a days-without-smoking record.`n`n");
			$nicotinegum++;
			$foundsomething=1;
		}
		if ($get5>=80){
			output("`0You found a `#Power Pill!`0`n`n");
			output("These odd little pills can be used in any Outpost, the Jungle, during a fight, or on the World Map.  It instantly grants a large amount of Stamina and ten rounds of cellular regeneration!`n`n");
			$potentpill++;
			$foundsomething=1;
		}
		if ($get6>=80){
			output("`0You found a `#One-Shot Teleporter!`0`n`n");

			output("A One-Shot Teleporter can be used while fighting in the Jungle.  It allows you to instantly escape from any fight and teleport back to the Outpost!  Beware, though - if you use it while fighting a monster on the World Map, you'll be instantly transported to Improbable Central.`n`n");
			$teleporter++;
			$foundsomething=1;
		}
		if ($get7>=80){
			output("`0You found a `#BANG Grenade!`0`n`n");
			output("A BANG Grenade can be used while fighting in the Jungle.  It does instant, hefty damage to any enemy!`n`n");
			$banggrenade++;
			$foundsomething=1;
		}
		if ($get8>=80){
			output("`0You found a `#WHOOMPH Grenade!`0`n`n");
			output("A WHOOMPH Grenade can be used while fighting in the Jungle.  It sets your foe on fire!  Use it at the beginning of a fight for best results.`n`n");
			$whoomphgrenade++;
			$foundsomething=1;
		}
		if ($get9>=80){
			output("`0You found a `#ZAP Grenade!`0`n`n");
			output("A ZAP Grenade can be used while fighting in the Jungle.  It blinds your opponent, letting you get the boot in without fear of retaliation for a few rounds!`n`n");
			$zapgrenade++;
			$foundsomething=1;
		}
		if ($get10>=80){
			output("`0You found a can of `#Monster Repellant Spray!`0`n`n");
			output("Monster Repellant Spray can be used in any Outpost, the Jungle, while fighting, or on the World Map.  You can spray it on yourself in the Jungle, Outpost or World Map, or spray it directly onto a monster during combat.  When sprayed on yourself, it reduces your chances of being attacked by any monster on the World Map by twenty-five per cent, and any monsters you do come across won't be as inclined to get close enough to hit you very hard.  The effects last for the rest of the game day, and you can use as many cans per day as you'd like.  Each can used increases the effect.  When sprayed directly onto a monster during combat, the monster's combat efficacy is dramatically, albeit temporarily, reduced.`n`n");
			$repellantspray++;
			$foundsomething=1;
		}
		if ($get11>=80){
			output("`0You found a `#Ration Pack!`0`n`n");
			output("Ration Packs contain everything a contestant needs to carry on fighting monsters!  A contestant can reliably eat nothing but Ration Packs and survive for several weeks before dying of malnutrition.  They are filling and contain very little fat.  Use them to restore your Stamina, but keep in mind their low nutritional quality - it's probably not a good idea to try to live on these alone.`n`n");
			$rationpack++;
			$foundsomething=1;
		}
		if ($get12>=80){
			output("`0You found an `#Improbability Bomb!`0`n`n");
			output("Improbability Bombs can be used against monsters in combat.  Their effects are somewhat unpredictable, and can prove very dangerous to both contestant and monster.  Be careful with these.`n`n");
			$improbabilitybomb++;
			$foundsomething=1;
		}
		if ($get13>=80){
			output("`0You found a `#greetings card with a picture of a kitten on it!`0`n`n");
			output("This is exactly the sort of thing that '\$The Watcher`0 goes nuts over.  Maybe she'd like it?`n`n");
			$kittencard++;
			$foundsomething=1;
		}
		if ($foundsomething==0){
		output("`0The crate is `4empty!`0  Well, that sucks.");
		}
	}
	set_module_pref("item1number",$smallmedkit,"improbablestuff");
	set_module_pref("item2number",$largemedkit,"improbablestuff");
	set_module_pref("item3number",$energydrink,"improbablestuff");
	set_module_pref("item4number",$nicotinegum,"improbablestuff");
	set_module_pref("item5number",$potentpill,"improbablestuff");
	set_module_pref("item6number",$teleporter,"improbablestuff");
	set_module_pref("item7number",$banggrenade,"improbablestuff");
	set_module_pref("item8number",$whoomphgrenade,"improbablestuff");
	set_module_pref("item9number",$zapgrenade,"improbablestuff");
	set_module_pref("item10number",$repellantspray,"improbablestuff");
	set_module_pref("item11number",$rationpack,"improbablestuff");
	set_module_pref("item12number",$improbabilitybomb,"improbablestuff");
	set_module_pref("item13number",$kittencard,"improbablestuff");
}
function getimprobablestuff_run(){
}
?>
