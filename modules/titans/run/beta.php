<?php

global $session,$badguy,$battle;
require_once "modules/titans/lib/lib.php";

page_header("Titans!");


//battle - do this in a function in the lib file?
$titan = titans_load_battle(1);
titans_show_log($titan);
if ($battle){
	include_once("battle.php");
	if (httpget('op')=="run"){
		titans_leave_battle($titan);
		redirect("runmodule.php?module=worldmapen&op=continue&fledtitan=true");
	}
	if ($victory){
		//award player with exp
		//send players Req to their bank based on the battlelog
	} else if ($defeat){
		titans_save_battle($titan);
		titans_show_comrades($titan);
		titans_leave_battle($titan);
		require_once("lib/forestoutcomes.php");
		forestdefeat($badguy,", a Titan");
	} else {
		require_once("lib/fightnav.php");
		fightnav(true,true,"runmodule.php?module=titans&titanop=beta");
	}
}
titans_save_battle($titan);
titans_show_comrades($titan);



addnav("Village","village.php");

page_footer();

?>