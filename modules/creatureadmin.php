<?php

function creatureadmin_getmoduleinfo(){
	$info = array(
		"name"=>"Improbable Creature Admin Spreadsheet Thing",
		"version"=>"2009-08-17",
		"author"=>"Dan Hall",
		"category"=>"Administrative",
		"download"=>"",
		"prefs"=>array(
			"Improbable Island Creature Spreadsheet Thing,title",
			"access"=>"Player has access to the Creature Admin thing and is not a complete plonker,bool|0",
		),
	);
	return $info;
}
function creatureadmin_install(){
	module_addhook("superuser");
	return true;
}
function creatureadmin_uninstall(){
	return true;
}
function creatureadmin_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "superuser":
			if (get_module_pref("access")){
				addnav("Spreadsheet Creature Admin","runmodule.php?module=creatureadmin&op=start");
			};
		break;
	}
	return $args;
}
function creatureadmin_run(){
	global $session;
	page_header("Spreadsheet Wotsit");
	$op = httpget("op");
	
	switch($op){
		case "start":
			output("This form accepts a tab-delimited series of fields identical to the Google Docs spreadsheet that Zolo, Sessine and I are working on.  To use it, copy a whole row out of that spreadsheet, paste it into the box below, and press the button.`n`n");
			rawoutput("<form action='runmodule.php?module=creatureadmin&op=confirm' method='POST'>");
			rawoutput("<br /><textarea name='creature' cols='40' rows='12'></textarea>");
			rawoutput("<input type='submit' class='button' value='".translate_inline("The Button")."'");
			rawoutput("</form>");
			addnav("","runmodule.php?module=creatureadmin&op=confirm");
		break;
		case "confirm":
			$post = httppost("creature");
			debug($post);
			$c = explode("\t", $post);
			debug($c);
			$creatureid = $c[0];
			$creaturename = $c[1];
			$creaturelevel = $c[2];
			$creatureweapon = $c[3];
			$creaturewin = $c[4];
			$creaturelose = $c[5];
			$description = $c[6];
			
			$t = array();
			$t[1]['name'] = $c[7];
			$t[1]['hp'] = $c[8];
			$t[1]['targatk'] = $c[9];
			$t[1]['targdef'] = $c[10];
			$t[1]['targmsg'] = $c[11];
			$t[1]['depatk'] = $c[12];
			$t[1]['depdef'] = $c[13];
			$t[1]['dephp'] = $c[14];
			$t[1]['depmsg'] = $c[15];
			$t[2]['name'] = $c[16];
			$t[2]['hp'] = $c[17];
			$t[2]['targatk'] = $c[18];
			$t[2]['targdef'] = $c[19];
			$t[2]['targmsg'] = $c[20];
			$t[2]['depatk'] = $c[21];
			$t[2]['depdef'] = $c[22];
			$t[2]['dephp'] = $c[23];
			$t[2]['depmsg'] = $c[24];
			$t[3]['name'] = $c[25];
			$t[3]['hp'] = $c[26];
			$t[3]['targatk'] = $c[27];
			$t[3]['targdef'] = $c[28];
			$t[3]['targmsg'] = $c[29];
			$t[3]['depatk'] = $c[30];
			$t[3]['depdef'] = $c[31];
			$t[3]['dephp'] = $c[32];
			$t[3]['depmsg'] = $c[33];
			$t[4]['name'] = $c[34];
			$t[4]['hp'] = $c[35];
			$t[4]['targatk'] = $c[36];
			$t[4]['targdef'] = $c[37];
			$t[4]['targmsg'] = $c[38];
			$t[4]['depatk'] = $c[39];
			$t[4]['depdef'] = $c[40];
			$t[4]['dephp'] = $c[41];
			$t[4]['depmsg'] = $c[42];
			$t[5]['name'] = $c[43];
			$t[5]['hp'] = $c[44];
			$t[5]['targatk'] = $c[45];
			$t[5]['targdef'] = $c[46];
			$t[5]['targmsg'] = $c[47];
			$t[5]['depatk'] = $c[48];
			$t[5]['depdef'] = $c[49];
			$t[5]['dephp'] = $c[50];
			$t[5]['depmsg'] = $c[51];
			$t[6]['name'] = $c[52];
			$t[6]['hp'] = $c[53];
			$t[6]['targatk'] = $c[54];
			$t[6]['targdef'] = $c[55];
			$t[6]['targmsg'] = $c[56];
			$t[6]['depatk'] = $c[57];
			$t[6]['depdef'] = $c[58];
			$t[6]['dephp'] = $c[59];
			$t[6]['depmsg'] = $c[60];
			
			
			$coresql="UPDATE " . db_prefix("creatures") . " SET creaturename = '$creaturename', creatureweapon = '$creatureweapon', creaturewin = '$creaturewin', creaturelose = '$creaturelose', creaturelevel = '$creaturelevel' WHERE creatureid = $creatureid";
			db_query($coresql);
			
			set_module_objpref("creatures", $creatureid, "description", $description, "creatureaddon");
			set_module_objpref("creatures", $creatureid, "usetargets", 1, "creaturetargets");
			
			for ($i=1; $i<=6; $i++){
				set_module_objpref("creatures",  $creatureid, "target".$i, $t[$i]['name'], "creaturetargets");
				set_module_objpref("creatures",  $creatureid, "hitpoints".$i, $t[$i]['hp'], "creaturetargets");
				set_module_objpref("creatures",  $creatureid, "hitatk".$i, $t[$i]['targatk'], "creaturetargets");
				set_module_objpref("creatures",  $creatureid, "hitdef".$i, $t[$i]['targdef'], "creaturetargets");
				set_module_objpref("creatures",  $creatureid, "hitmsg".$i, $t[$i]['targmsg'], "creaturetargets");
				set_module_objpref("creatures",  $creatureid, "killatk".$i, $t[$i]['depatk'], "creaturetargets");
				set_module_objpref("creatures",  $creatureid, "killdef".$i, $t[$i]['depdef'], "creaturetargets");
				set_module_objpref("creatures",  $creatureid, "killhp".$i, $t[$i]['dephp'], "creaturetargets");
				set_module_objpref("creatures",  $creatureid, "killmsg".$i, $t[$i]['depmsg'], "creaturetargets");
			}
			output("Done!");
		break;
		case "finalize":
		break;
	}
	addnav("Grotto","superuser.php");
	page_footer();
	return $args;
}
?>