<?php

require_once "modules/iitems/lib/lib.php";

function iitems_session_vars_getmoduleinfo(){
	$info=array(
		"name"=>"IItems - Session Vars",
		"version"=>"20090523",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"IItems",
		"download"=>"",
	);
	return $info;
}

function iitems_session_vars_install(){
	module_addhook("iitems-use-item");
	module_addhook("iitems-superuser");
	return true;
}

function iitems_session_vars_uninstall(){
	return true;
}

function iitems_session_vars_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "iitems-use-item":
			if ($args['master']['playergold']) $session['user']['gold'] += $args['master']['playergold'];
			if ($args['master']['playerexperience']) $session['user']['experience'] += $args['master']['playerexperience'];
			if ($args['master']['playergoldinbank']) $session['user']['goldinbank'] += $args['master']['playergoldinbank'];
			if ($args['master']['playerhitpoints']) $session['user']['hitpoints'] += $args['master']['playerhitpoints'];
			if ($args['master']['playergems']) $session['user']['gems'] += $args['master']['playergems'];
			if ($args['master']['playerturns']) $session['user']['turns'] += $args['master']['playerturns'];
			if ($args['master']['playercharm']) $session['user']['charm'] += $args['master']['playercharm'];
			if ($args['master']['playerplayerfights']) $session['user']['playerfights'] += $args['master']['playerplayerfights'];
			if ($args['master']['playerdeathpower']) $session['user']['deathpower'] += $args['master']['playerdeathpower'];
			
			if ($session['user']['gold'] < 0) $session['user']['gold'] = 0;
			if ($session['user']['experience'] < 0) $session['user']['experience'] = 0;
			if ($session['user']['goldinbank'] < 0) $session['user']['goldinbank'] = 0;
			if ($session['user']['hitpoints'] < 0) $session['user']['hitpoints'] = 0;
			if ($session['user']['hitpoints'] > $session['user']['maxhitpoints']) $session['user']['hitpoints'] = $session['user']['maxhitpoints'];
			if ($session['user']['gems'] < 0) $session['user']['gems'] = 0;
			if ($session['user']['turns'] < 0) $session['user']['turns'] = 0;
			if ($session['user']['charm'] < 0) $session['user']['charm'] = 0;
			if ($session['user']['playerfights'] < 0) $session['user']['playerfights'] = 0;
			if ($session['user']['deathpower'] < 0) $session['user']['deathpower'] = 0;
			
			break;
		case "iitems-superuser":
			output("`bIItems: Session Vars`b`n");
			output("Most things in the \$session['user'] array can be altered upon the use of an item.  Prepend the variable name with \"player\", and use negative numbers for negative values.  Should be quite straightforward.`n");
			output("`bplayergold`nplayerexperience`nplayergoldinbank`nplayerhitpoints`nplayergems`nplayerturns`nplayercharm`nplayerplayerfights`nplayerdeathpower`b`n`n");
			break;
	}
	return $args;
}

function iitems_session_vars_run(){
	return true;
}
?>