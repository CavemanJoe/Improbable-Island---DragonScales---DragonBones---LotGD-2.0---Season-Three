<?php
require_once("modules/worldmapen.php");

function iitemcrates_getmoduleinfo(){
	$info = array(
		"name"=>"IItems - Crates on World Map",
		"author"=>"Dan Hall",
		"version"=>"2009-09-27",
		"category"=>"IItems",
		"download"=>"",
		"settings"=>array(
			"crates"=>"IItem Crates available to find,viewonly|array()",
			"dailyadditions"=>"Base number of IItem Crates to add to the World Map per game day,int|10",
			"donationaddition"=>"Add one crate for this many donator points in a single transaction,int|100",
			"maxitems"=>"Maximum number of items to add to each crate,int|10",
			"minitems"=>"Minimum number of items to add to each crate,int|2",
			),
		"prefs"=>array(
			"cratesfound"=>"How many Crates has the player found?,int|0",
			),
		);
	return $info;
}

function iitemcrates_install(){
	module_addhook("worldnav");
	module_addhook("newday-runonce");
	module_addhook("donation");
	return true;
}

function iitemcrates_uninstall(){
	return true;
}

function iitemcrates_dohook($hookname,$args){
	global $session;
	switch ($hookname){
		case "donation":
			$amt = $args['amt'];
			$donationbonus = floor($args['amt']/get_module_setting("donationaddition"));
			require_once "modules/iitems/lib/lib.php";
			$allitems = iitems_get_all_item_details();
			$items = array();
			$cratefind = array();
			foreach($allitems AS $localid=>$data){
				if ($data['cratefind']){
					$items[$localid] = $data;
					for ($i=0; $i<$data['cratefind']; $i++){
						$cratefind[] = $localid;
					}
				}
			}
			$crates = unserialize(get_module_setting("crates"));
			for ($i=0; $i<$donationbonus; $i++){
				$crate = array();
				//set a random location
				$x = e_rand(1,get_module_setting("worldmapsizeX","worldmapen"));
				$y = e_rand(1,get_module_setting("worldmapsizeY","worldmapen"));
				$loc = array();
				$loc['x'] = $x;
				$loc['y'] = $y;
				$crate['loc']=$loc;
				//set crate contents
				$numitems = e_rand(get_module_setting("minitems"),get_module_setting("maxitems"));
				for ($a=0; $a<$numitems; $a++){
					$add=e_rand(1,count($cratefind));
					$crate['contents'][] = $cratefind[$add-1];
				}
				$crates[]=$crate;
			}
			set_module_setting("crates", serialize($crates));
		break;
		case "worldnav":
			$crates = unserialize(get_module_setting("crates"));
			//debug($crates);
			$ploc = get_module_pref("worldXYZ","worldmapen");
			if (!is_array($crates)) {
				$crates = array();
			}
			foreach($crates AS $key => $vals){
				if ($ploc == $vals['loc']['x'].",".$vals['loc']['y'].",1"){
					require_once "modules/iitems/lib/lib.php";
					output("`bYou found something!`b`nYou come across a wooden crate, with a small parachute attached.  You spend a few minutes prying it open.`n`n");
					foreach($vals['contents'] AS $ckey => $content){
						$itemdetails = iitems_get_item_details($content);
						output("You found a %s!`n",$itemdetails['verbosename']);
						iitems_give_item($content);
					}
					
					increment_module_pref("cratesfound");
					
					$found = get_module_pref("cratesfound");
					if (is_module_active("medals")){
						if ($found>250){
							require_once "modules/medals.php";
							medals_award_medal("crate1000","Supreme Crate Finder","This player has found more than 1000 Supply Crates!","medal_crategold.png");
						}
						if ($found>50){
							require_once "modules/medals.php";
							medals_award_medal("crate500","Expert Crate Finder","This player has found more than 500 Supply Crates!","medal_cratesilver.png");
						}
						if ($found>10){
							require_once "modules/medals.php";
							medals_award_medal("crate100","Supreme Crate Finder","This player has found more than 100 Supply Crates!","medal_cratebronze.png");
						}
					}
					
					
					unset($crates[$key]);
					set_module_setting("crates", serialize($crates));
					modulehook("iitems_findcrate");
					//Break operation - players cannot find more than one item crate in a single move.
					break;
				}
			}
		break;
		case "newday-runonce":
			require_once "modules/iitems/lib/lib.php";
			$allitems = iitems_get_all_item_details();
			$items = array();
			$cratefind = array();
			foreach($allitems AS $localid=>$data){
				if ($data['cratefind']){
					$items[$localid] = $data;
					for ($i=0; $i<$data['cratefind']; $i++){
						$cratefind[] = $localid;
					}
				}
			}
			$crates = unserialize(get_module_setting("crates"));			
			for ($i=0; $i<get_module_setting("dailyadditions"); $i++){
				$crate = array();
				//set a random location
				$x = e_rand(1,get_module_setting("worldmapsizeX","worldmapen"));
				$y = e_rand(1,get_module_setting("worldmapsizeY","worldmapen"));
				$loc = array();
				$loc['x'] = $x;
				$loc['y'] = $y;
				$crate['loc']=$loc;
				//set crate contents
				$numitems = e_rand(get_module_setting("minitems"),get_module_setting("maxitems"));
				for ($a=0; $a<$numitems; $a++){
					$add=e_rand(1,count($cratefind));
					$crate['contents'][] = $cratefind[$add-1];
				}
				$crates[]=$crate;
			}
			set_module_setting("crates", serialize($crates));
		break;
	}
	return $args;
}
?>
