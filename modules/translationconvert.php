<?php

function translationconvert_getmoduleinfo(){
	$info = array(
		"name"=>"Translations Convertor Thing",
		"version"=>"2010-06-08",
		"author"=>"Dan Hall",
		"category"=>"Administrative",
		"download"=>"",
		"prefs"=>array(
			"Translation Convertor,title",
			"access"=>"Player has access to the Translation Convertor and is not a complete plonker,bool|0",
		),
	);
	return $info;
}

function translationconvert_install(){
	module_addhook("superuser");
	return true;
}

function translationconvert_uninstall(){
	return true;
}

function translationconvert_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "superuser":
			debug("tconvert!");
			if (get_module_pref("access","translationconvert")){
				debug("Access!");
				addnav("Translation Convertor","runmodule.php?module=translationconvert");
			} else {
				debug("No access!");
			}
		break;
	}
	return $args;
}

function translationconvert_run(){
	global $session;
	page_header("Translations Convertor Thing");
	output("Outputting all known translations, so that you can do a find-and-replace in the files themselves and we can stop doing this silly translate thing.`n`n");

	if (httpget('delete')){
		$del = httpget('delete');
		$sql = "UPDATE ".db_prefix("translations")." SET version='updated' WHERE tid=$del";
		db_query($sql);
	}
	
	$sql = "SELECT * FROM ".db_prefix("translations")." WHERE version='dragonbones' ORDER BY uri";
	$result = db_query($sql);
	$total = 0;
	while ($row = db_fetch_assoc($result)){
		if ($row['intext']!=$row['outtext']){
			$total++;
			rawoutput("<a href=\"runmodule.php?module=translationconvert&delete=".$row['tid']."\">MARK</a>");
			addnav("","runmodule.php?module=translationconvert&delete=".$row['tid']);
			output_notl("`n`0`b%s`b:",$row['uri']);
			rawoutput("<table width=100%><tr><td width=50% border=1px solid #cccccc>".$row['intext']."</td><td width=50%>".$row['outtext']."</td></tr></table>");
			output_notl("`n`n");
		}
	}
	debug($total);	
	
	addnav("Back to the Grotto","superuser.php");
	page_footer();
}

?>