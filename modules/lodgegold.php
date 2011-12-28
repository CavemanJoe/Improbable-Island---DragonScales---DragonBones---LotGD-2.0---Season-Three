<?php

function lodgegold_getmoduleinfo(){
	$info = array(
		"name"=>"Lodge Gold",
		"author"=>"Sixf00t4",
		"version"=>"20060105",
		"category"=>"Lodge",
		"download"=>"http://dragonprime.net/users/sixf00t4/lodgegold.zip",
		"description"=>"Allows players to trade lodge points for gold",
		"vertxtloc"=>"http://dragonprime.net/users/sixf00t4/",
		"settings"=>array(
			"Lodge Gold Settings,title",
			"cost1"=>"Donation cost for 1st rank,int|10",
			"gold1"=>"Gold amount for 1st rank,int|800",
			"cost2"=>"Donation cost for 2nd rank,int|25",
			"gold2"=>"Gold amount for 2nd rank,int|2500",
			"cost3"=>"Donation cost for 3rd rank,int|50",
			"gold3"=>"Gold amount for 3rd rank,int|6000", 
        ),
	);
	return $info;
}
function lodgegold_install(){
	module_addhook("lodge");
	module_addhook("pointsdesc");
	return true;
}
function lodgegold_uninstall(){
	return true;
}
function lodgegold_dohook($hookname,$args){
	global $session;
    $cost1 = get_module_setting("cost1","lodgegold");
    $cost2 = get_module_setting("cost2","lodgegold");
    $cost3 = get_module_setting("cost3","lodgegold");    

    $gold1 = get_module_setting("gold1","lodgegold");
    $gold2 = get_module_setting("gold2","lodgegold");
    $gold3 = get_module_setting("gold3","lodgegold");

	switch ($hookname){
		case "lodge":
			addnav("Buy Requisition");
			$donationsleft = ($session['user']['donation'] - $session['user']['donationspent']);
			if ($donationsleft >= $cost1) addnav(array("`^%s Gold - `#(%s Points)",$gold1,$cost1),"runmodule.php?module=lodgegold&op=cost1&gold=gold1");
			if ($donationsleft >= $cost2) addnav(array("`^%s Gold - `#(%s Points)",$gold2,$cost2),"runmodule.php?module=lodgegold&op=cost2&gold=gold2");
			if ($donationsleft >= $cost3) addnav(array("`^%s Gold - `#(%s Points)",$gold3,$cost3),"runmodule.php?module=lodgegold&op=cost3&gold=gold3");

			break;
		case "pointsdesc":
			$args['count']++;
			$format = $args['format'];
			$str = translate("Trade in donation points for gold.");
			$str = sprintf($str, get_module_setting("cost1"), get_module_setting("cost2"));
			output($format, $str, true);
			break;
		}
	return $args;
}
function lodgegold_run(){
	global $session;
    
	$cost = httpget('op');
    $gold = httpget('gold');

    $cost=get_module_setting("$cost");
    $gold=get_module_setting("$gold");
    
	page_header("JCP's Hunter's Lodge");
    
	output("`)JCP sits around bags and bags of gold.  You hand him your voucher for `#%s donation points`0, and he puts `^%s gold`0 in an pouch and tosses it at you lightly.",$cost,$gold);
	$session['user']['donationspent']+=$cost;	
    $session['user']['gold']+=$gold;
	$config = unserialize($session['user']['donationconfig']);
    if (!is_array($config))
       $config = array();
    $config = array_push($config, "spent $cost points for $gold gold in the lodge.");
    $session['user']['donationconfig'] = $config;
	addnav("Return to the Lodge","lodge.php");
	page_footer();
}
?>