<?php

function improbablestuffgifting_getmoduleinfo(){
	$info = array(
		"name"=>"Improbable Stuff Gifting",
		"version"=>"2009-02-07",
		"author"=>"Dan Hall",
		"category"=>"Improbable",
		"download"=>"",
		"prefs"=>array(
			"item1"=>"Small Medkits ready to be delivered,int|0",
			"item2"=>"Large Medkits ready to be delivered,int|0",
			"item3"=>"Energy Drinks ready to be delivered,int|0",
			"item4"=>"Nicotine Gum ready to be delivered,int|0",
			"item5"=>"Power Pills ready to be delivered,int|0",
			"item6"=>"One-Shot Teleporters ready to be delivered,int|0",
			"item7"=>"BANG Grenades ready to be delivered,int|0",
			"item8"=>"WHOOMPH Grenades ready to be delivered,int|0",
			"item9"=>"ZAP Grenades ready to be delivered,int|0",
			"item10"=>"Monster Repellent Sprays ready to be delivered,int|0",
			"item11"=>"Ration Packs ready to be delivered,int|0",
			"item12"=>"Improbability Bombs ready to be delivered,int|0",
			"item13"=>"Kitten Cards ready to be delivered,int|0",
			"msg"=>"Message to be directed to the player,text|",
			"giftsgiven"=>"Total number of gifts given by this player,int|0",
		),
	);
	return $info;
}
function improbablestuffgifting_install(){
	module_addhook("village");
	module_addhook("newday");
	return true;
}
function improbablestuffgifting_uninstall(){
	return true;
}
function improbablestuffgifting_dohook($hookname,$args){
	global $session;
	switch($hookname){
	case "village":
		if ($session['user']['location']=="Kittania"){
			tlschema($args['schemas']['marketnav']);
			addnav($args['marketnav']);
			tlschema();
			addnav("The Gift Exchange","runmodule.php?module=improbablestuffgifting&op=start");
		}
		break;
	case "newday":
		$gifts = 0;
		for ($i=0; $i<=12; $i++){
			$gifts += get_module_pref("item".$i);
		}
		if ($gifts >0){
			output("`0`n`c`bSomething `5Lovely`0!`b`c`n`nYou wake up with a large gift-wrapped box by your head.  How odd!  There's no mention of who the gift is from, or clue as to its contents.  A little nervously, you unwrap and open the box.`nInside, you find:`n`n");
			if (get_module_pref("item1")>0){
				output("%s Small Medkit(s)`n",get_module_pref("item1"));
				increment_module_pref("item1number", get_module_pref("item1"), "improbablestuff");
			}
			if (get_module_pref("item2")>0){
				output("%s Large Medkit(s)`n",get_module_pref("item2"));
				increment_module_pref("item2number", get_module_pref("item2"), "improbablestuff");
			}
			if (get_module_pref("item3")>0){
				output("%s Energy Drink(s)`n",get_module_pref("item3"));
				increment_module_pref("item3number", get_module_pref("item3"), "improbablestuff");
			}
			if (get_module_pref("item4")>0){
				output("%s Piece(s) of Nicotine Gum`n",get_module_pref("item4"));
				increment_module_pref("item4number", get_module_pref("item4"), "improbablestuff");
			}
			if (get_module_pref("item5")>0){
				output("%s Power Pill(s)`n",get_module_pref("item5"));
				increment_module_pref("item5number", get_module_pref("item5"), "improbablestuff");
			}
			if (get_module_pref("item6")>0){
				output("%s One-Shot Teleporter(s)`n",get_module_pref("item6"));
				increment_module_pref("item6number", get_module_pref("item6"), "improbablestuff");
			}
			if (get_module_pref("item7")>0){
				output("%s BANG Grenade(s)`n",get_module_pref("item7"));
				increment_module_pref("item7number", get_module_pref("item7"), "improbablestuff");
			}
			if (get_module_pref("item8")>0){
				output("%s WHOOMPH Grenade(s)`n",get_module_pref("item8"));
				increment_module_pref("item8number", get_module_pref("item8"), "improbablestuff");
			}
			if (get_module_pref("item9")>0){
				output("%s ZAP Grenade(s)`n",get_module_pref("item9"));
				increment_module_pref("item9number", get_module_pref("item9"), "improbablestuff");
			}
			if (get_module_pref("item10")>0){
				output("%s Can(s) of Monster Repellent Spray`n",get_module_pref("item10"));
				increment_module_pref("item10number", get_module_pref("item10"), "improbablestuff");
			}
			if (get_module_pref("item11")>0){
				output("%s Ration Pack(s)`n",get_module_pref("item11"));
				increment_module_pref("item11number", get_module_pref("item11"), "improbablestuff");
			}
			if (get_module_pref("item12")>0){
				output("%s Improbability Bomb(s)`n",get_module_pref("item12"));
				increment_module_pref("item12number", get_module_pref("item12"), "improbablestuff");
			}
			if (get_module_pref("item13")>0){
				output("%s Kitten Card(s)`n",get_module_pref("item13"));
				increment_module_pref("item13number", get_module_pref("item13"), "improbablestuff");
				//Expansion idea: give a Warm Fuzzies buff for Kitten Cards
			}
			for ($i=0; $i<=12; $i++){
				set_module_pref("item".$i, 0);
			}
			output("`nHow unexpectedly awesome!`n");
		}
		break;
	}
	return $args;
}

function improbablestuffgifting_run(){
	global $session;
	$op = httpget("op");
	
	page_header("The Gift Exchange");
	
	switch (httpget("op")){
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
			rawoutput("<form action='runmodule.php?module=improbablestuffgifting&op=findplayer' method='post'>");
			rawoutput("<input name='name'>");
			rawoutput("<input type='submit' class='button' value='".translate_inline("search")."'>");
			rawoutput("</form>");
			addnav("","runmodule.php?module=improbablestuffgifting&op=findplayer");
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
			debug($uid);
			debug($lastip);
			$sql = "SELECT uniqueid, acctid, name FROM " . db_prefix("accounts") . " WHERE locked=0 $search ORDER BY name DESC ";
			$result = db_query($sql);
			debug($result);
			output("The KittyMorph nods.  \"`1Okay, let's have a look at who I've got written down here whose name sounds like that...`0\"`n`n");
			
			$matches = 0;
			for($i=0;$i < db_num_rows($result);$i++){
				$row = db_fetch_assoc($result);
				if ($row['uniqueid'] != $session['user']['uniqueid']){
					addnav("","runmodule.php?module=improbablestuffgifting&op=give&id=".$row['acctid']."&sub=first");
					output_notl("<a href='runmodule.php?module=improbablestuffgifting&op=give&id=".$row['acctid']."&sub=first'>".$row['name']."</a>",true);
					output("`n");
					$matches++;
				}
			}
			if($matches == 0 ){
				output("He looks down at his list.  \"`1Oh.  Actually, it doesn't look like there's `ianyone`i matching that description who you can send a present to.  Bummer.  Wanna try that again?`0\"");
			}
			addnav("Return to the Outpost","village.php");
			addnav("Search Again","runmodule.php?module=improbablestuffgifting&op=start");
			break;
		case "give":
			$id = httpget('id');
			//player prefs
			if (httpget('sub')=="first"){
				output("The KittyMorph reaches below his counter and brings up a large box, along with a clipboard on which he writes down the name of your nominated contestant.  \"`1Okay, I've got the name - now let's start filling this up!`0\"");
				addnav("Wait, I've changed my mind.  Let's back up a step.","runmodule.php?module=improbablestuffgifting&op=start&subop=searchagain");
				addnav("Actually, forget the whole thing.","village.php");
			} else {
				output("The KittyMorph takes the item, wraps it in tissue and places it gently in the box. \"`1Marvellous.  Anything else you've got that you'd like to go in here?`0\"");
				increment_module_pref("giftsgiven",1);
				addnav("No, I think that's enough.","runmodule.php?module=improbablestuffgifting&op=start&subop=continue");
			}
			switch (httpget("item")){
				case "smallmedkit":
					increment_module_pref("item1number", -1, "improbablestuff");
					increment_module_pref("item1", -1, "improbablestuffgifting", $id);
					break;
				case "largemedkit":
					increment_module_pref("item2number", -1, "improbablestuff");
					increment_module_pref("item2", 1, "improbablestuffgifting", $id);
					break;
				case "energydrink":
					increment_module_pref("item3number", -1, "improbablestuff");
					increment_module_pref("item3", 1, "improbablestuffgifting", $id);
					break;
				case "nicotinegum":
					increment_module_pref("item4number", -1, "improbablestuff");
					increment_module_pref("item4", 1, "improbablestuffgifting", $id);
					break;
				case "powerpill":
					increment_module_pref("item5number", -1, "improbablestuff");
					increment_module_pref("item5", 1, "improbablestuffgifting", $id);
					break;
				case "teleporter":
					increment_module_pref("item6number", -1, "improbablestuff");
					increment_module_pref("item6", 1, "improbablestuffgifting", $id);
					break;
				case "banggrenade":
					increment_module_pref("item7number", -1, "improbablestuff");
					increment_module_pref("item7", 1, "improbablestuffgifting", $id);
					break;
				case "whoomphgrenade":
					increment_module_pref("item8number", -1, "improbablestuff");
					increment_module_pref("item8", 1, "improbablestuffgifting", $id);
					break;
				case "zapgrenade":
					increment_module_pref("item9number", -1, "improbablestuff");
					increment_module_pref("item9", 1, "improbablestuffgifting", $id);
					break;
				case "repellentspray":
					increment_module_pref("item10number", -1, "improbablestuff");
					increment_module_pref("item10", 1, "improbablestuffgifting", $id);
					break;
				case "rationpack":
					increment_module_pref("item11number", -1, "improbablestuff");
					increment_module_pref("item11", 1, "improbablestuffgifting", $id);
					break;
				case "improbabilitybomb":
					increment_module_pref("item12number", -1, "improbablestuff");
					increment_module_pref("item12", 1, "improbablestuffgifting", $id);
					break;
				case "kittencard":
					increment_module_pref("item13number", -1, "improbablestuff");
					increment_module_pref("item13", 1, "improbablestuffgifting", $id);
					break;
			}
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
			addnav("Gifting");
			if ($playersmallmedkit > 0){
				addnav(array("Give a Small Medkit (%s remaining)",$playersmallmedkit),"runmodule.php?module=improbablestuffgifting&op=give&item=smallmedkit&id=$id");
			}
			if ($playerlargemedkit > 0){
				addnav(array("Give a Large Medkit (%s remaining)",$playerlargemedkit),"runmodule.php?module=improbablestuffgifting&op=give&item=largemedkit&id=$id");
			}
			if ($playerenergydrink > 0){
				addnav(array("Give an Energy Drink (%s remaining)",$playerenergydrink),"runmodule.php?module=improbablestuffgifting&op=give&item=energydrink&id=$id");
			}
			if ($playernicotinegum > 0){
				addnav(array("Give a piece of Nicotine Gum (%s remaining)",$playernicotinegum),"runmodule.php?module=improbablestuffgifting&op=give&item=nicotinegum&id=$id");
			}
			if ($playerpowerpill > 0){
				addnav(array("Give a Power Pill (%s remaining)",$playerpowerpill),"runmodule.php?module=improbablestuffgifting&op=give&item=powerpill&id=$id");
			}
			if ($playerteleporter > 0){
				addnav(array("Give a One-Shot Teleporter (%s remaining)",$playerteleporter),"runmodule.php?module=improbablestuffgifting&op=give&item=teleporter&id=$id");
			}
			if ($playerbanggrenade > 0){
				addnav(array("Give a Bang Grenade (%s remaining)",$playerbanggrenade),"runmodule.php?module=improbablestuffgifting&op=give&item=banggrenade&id=$id");
			}
			if ($playerwhoomphgrenade > 0){
				addnav(array("Give a WHOOMPH Grenade (%s remaining)",$playerwhoomphgrenade),"runmodule.php?module=improbablestuffgifting&op=give&item=whoomphgrenade&id=$id");
			}
			if ($playerzapgrenade > 0){
				addnav(array("Give a ZAP Grenade (%s remaining)",$playerzapgrenade),"runmodule.php?module=improbablestuffgifting&op=give&item=zapgrenade&id=$id");
			}
			if ($playerrepellentspray > 0){
				addnav(array("Give a can of Monster Repellent Spray (%s remaining)",$playerrepellentspray),"runmodule.php?module=improbablestuffgifting&op=give&item=repellentspray&id=$id");
			}
			if ($playerrationpack > 0){
				addnav(array("Give a Ration Pack (%s remaining)",$playerrationpack),"runmodule.php?module=improbablestuffgifting&op=give&item=rationpack&id=$id");
			}
			if ($playerimprobabilitybomb > 0){
				addnav(array("Give an Improbability Bomb (%s remaining)",$playerimprobabilitybomb),"runmodule.php?module=improbablestuffgifting&op=give&item=improbabilitybomb&id=$id");
			}
			if ($playerkittencard > 0){
				addnav(array("Give a Kitten Card (%s remaining)",$playerkittencard),"runmodule.php?module=improbablestuffgifting&op=give&item=kittencard&id=$id");
			}
			break;
	}
	page_footer();
	return $args;
}
?>