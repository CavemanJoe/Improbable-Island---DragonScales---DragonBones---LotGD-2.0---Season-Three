<?php

//BANG GRENADE

function supplycrate_define_item(){
	set_item_setting("description","A large crate filled with... well, who knows what?","supplycrate");
	set_item_setting("destroyafteruse",true,"supplycrate");
	set_item_setting("dropworldmap",true,"supplycrate");
	set_item_setting("giftable",true,"supplycrate");
	set_item_setting("image","supplycrate.png","supplycrate");
	set_item_setting("plural","Supply Crates","supplycrate");
	set_item_setting("verbosename","Supply Crate","supplycrate");
	set_item_setting("weight","20","supplycrate");
	set_item_setting("require_file","supplycrate.php","supplycrate");
	set_item_setting("call_function","supplycrate_use","supplycrate");
}

function supplycrate_use($args){
	increment_module_pref("cratesopened","supplycrates");
	$found = get_module_pref("cratesopened","supplycrates");
	if (is_module_active("medals")){
		if ($found>250){
			require_once "modules/medals.php";
			medals_award_medal("crate1000","Supreme Crate Finder","This player has opened more than 250 Supply Crates!","medal_crategold.png");
		}
		if ($found>50){
			require_once "modules/medals.php";
			medals_award_medal("crate500","Expert Crate Finder","This player has opened more than 50 Supply Crates!","medal_cratesilver.png");
		}
		if ($found>10){
			require_once "modules/medals.php";
			medals_award_medal("crate100","Supreme Crate Finder","This player has opened more than 10 Supply Crates!","medal_cratebronze.png");
		}
	}
	$crateables = get_items_with_settings("cratefind");
	$randompool = array();
	foreach($crateables AS $item => $prefs){
		for ($i=0; $i<$prefs['cratefind']; $i++){
			$randompool[] = $item;
		}
	}

	output("You spend a few minutes prying open your Supply Crate.`n");

	$giveitems = array();
	$numitems = e_rand(get_module_setting("minitems","supplycrates"),get_module_setting("maxitems","supplycrates"));
	$chosenitems = array_rand($randompool,$numitems);
		
	foreach($chosenitems AS $key => $poolkey){
		$item = $randompool[$poolkey];
		$name = $crateables[$item]['verbosename'];
		output("You find a %s!`n",$name);
		give_item($item);
	}
	return $args;
}

?>