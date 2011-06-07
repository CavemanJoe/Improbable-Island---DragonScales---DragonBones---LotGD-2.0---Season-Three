<?php

global $session,$badguy,$battle;
require_once "modules/titans/lib/lib.php";

page_header("Titan!");

$titanid = httpget("titanid");
//battle - do this in a function in the lib file?
$titan = titans_load_battle($titanid);
titans_show_log($titan);
if ($battle){
	if (httpget('op')=="run"){
		titans_leave_battle($titan);
		redirect("runmodule.php?module=worldmapen&op=continue&fledtitan=true");
	}
	include_once("battle.php");
	if ($victory || $titan['creature']['creaturehealth']<1){
		//kill the titan
		$titan = titans_kill_titan($titan);
	} else if ($defeat){
		titans_save_battle($titan);
		titans_show_comrades($titan);
		titans_leave_battle_ko($titan);
		require_once("lib/forestoutcomes.php");
		//set the player's Location properly so they don't get bumped back to IC
		$session['user']['location'] = get_module_pref("lastCity","worldmapen");
		forestdefeat(array($badguy),", a Titan");		
	} else {
		require_once("lib/fightnav.php");
		fightnav(true,true,"runmodule.php?module=titans&titanop=battle&titanid=".$titanid, true);
	}
}
if (!$titan['battlelog']['killed']){
	titans_save_battle($titan);
}
titans_show_comrades($titan);

page_footer();

?>