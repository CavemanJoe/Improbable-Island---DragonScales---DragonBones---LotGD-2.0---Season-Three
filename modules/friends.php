<?php

function friends_getmoduleinfo(){
	$info = array(
		"name"=>"Stripped-Down Friends List",
		"version"=>"2009-09-24",
		"author"=>"Dan Hall",
		"category"=>"Social",
		"override_forced_nav"=>true,
		"download"=>"",
	);
	return $info;
}
function friends_install(){
	// module_addhook("worldnav",20);
	return true;
}
function friends_uninstall(){
	return true;
}
function friends_dohook($hookname,$args){
	global $session;
	// switch($hookname){
		// case "charstats":
			// addnav("Lonely?");
			// addnav("Look around for other people","runmodule.php?module=friends");
			// break;
		// }
	return $args;
}
function friends_run(){
	global $session;
	popup_header("Who's Online");
	//Output online characters list
	$sql="SELECT name,laston,loggedin FROM " . db_prefix("accounts") . " WHERE locked=0 AND loggedin=1 AND laston>'".date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds"))."' ORDER BY laston DESC";
	$result = db_query($sql);
	rawoutput("<table>");
	while ($row = db_fetch_assoc($result)) {
		rawoutput("<tr><td>");
		output("%s",$row['name']);
		rawoutput("</td><td>");
		output("%s",$row['laston']);
		rawoutput("</td></tr>");
	}
	rawoutput("</table>");
	popup_footer();
}
?>