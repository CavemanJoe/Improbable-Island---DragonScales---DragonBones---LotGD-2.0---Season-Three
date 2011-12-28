<?php

function lodgegems_getmoduleinfo(){
	$info = array(
		"name"=>"Lodge Gems",
		"author"=>"Sixf00t4",
		"version"=>"20060105",
		"category"=>"Lodge",
		"download"=>"http://dragonprime.net/users/sixf00t4/lodgegems.zip",
		"description"=>"Allows players to trade lodge points for gems",
		"vertxtloc"=>"http://dragonprime.net/users/sixf00t4/",
		"settings"=>array(
			"Lodge gems Settings,title",
			"cost1"=>"Donation cost for 1st rank,int|15",
			"gems1"=>"Gems amount for 1st rank,int|1",
			"cost2"=>"Donation cost for 2nd rank,int|25",
			"gems2"=>"Gems amount for 2nd rank,int|2",
			"cost3"=>"Donation cost for 3rd rank,int|50",
			"gems3"=>"Gems amount for 3rd rank,int|5", 
        ),
	);
	return $info;
}
function lodgegems_install(){
	module_addhook("lodge");
	module_addhook("pointsdesc");
	return true;
}
function lodgegems_uninstall(){
	return true;
}
function lodgegems_dohook($hookname,$args){
	global $session;
    $cost1 = get_module_setting("cost1","lodgegems");
    $cost2 = get_module_setting("cost2","lodgegems");
    $cost3 = get_module_setting("cost3","lodgegems");    

    $gems1 = get_module_setting("gems1","lodgegems");
    $gems2 = get_module_setting("gems2","lodgegems");
    $gems3 = get_module_setting("gems3","lodgegems");

	switch ($hookname){
		case "lodge":
			$donationsleft = ($session['user']['donation'] - $session['user']['donationspent']);
			addnav("Buy Cigarettes");
			if ($donationsleft >= $cost1) addnav(array("`^%s gems - `#(%s Points)",$gems1,$cost1),"runmodule.php?module=lodgegems&op=cost1&gems=gems1");
			if ($donationsleft >= $cost2) addnav(array("`^%s gems - `#(%s Points)",$gems2,$cost2),"runmodule.php?module=lodgegems&op=cost2&gems=gems2");
			if ($donationsleft >= $cost3) addnav(array("`^%s gems - `#(%s Points)",$gems3,$cost3),"runmodule.php?module=lodgegems&op=cost3&gems=gems3");

			break;
		case "pointsdesc":
			$args['count']++;
			$format = $args['format'];
			$str = translate("Trade in donation points for gems.");
			$str = sprintf($str, get_module_setting("cost1"), get_module_setting("cost2"));
			output($format, $str, true);
			break;
		}
	return $args;
}
function lodgegems_run(){
	global $session;
    
	$cost = httpget('op');
    $gems = httpget('gems');

$cost=get_module_setting("$cost");
$gems=get_module_setting("$gems");
    
	page_header("JCP's Hunter's Lodge");
    
	output("`)JCP sits around bags and bags of gems.  You hand him your voucher for `#%s donation points`0, and he puts `^%s gems`0 in an pouch and tosses it at you lightly.",$cost,$gems);
	$session['user']['donationspent']+=$cost;	
    $session['user']['gems']+=$gems;
	$config = unserialize($session['user']['donationconfig']);
    if (!is_array($config))
       $config = array();
    $config = array_push($config, "spent $cost points for $gems gems in the lodge.");
    $session['user']['donationconfig'] = $config;
    addnav("Return to the Lodge","lodge.php");
    page_footer();
}
?>