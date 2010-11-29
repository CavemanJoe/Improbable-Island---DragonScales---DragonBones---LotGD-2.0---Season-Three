<?php

function ekd_getmoduleinfo(){
	$info = array(
		"name"=>"Economic Drive Kill",
		"version"=>"2009-08-01",
		"author"=>"Dan Hall",
		"category"=>"Improbable",
		"download"=>"",
		"prefs"=>array(
			"Economic Drive Kill,title",
			"edks"=>"How many EDKs has the player completed,int|0",
		),
	);
	return $info;
}
function ekd_install(){
	module_addhook("counciloffices");
	module_addhook("biostat");
	return true;
}
function ekd_uninstall(){
	return true;
}
function ekd_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "biostat":
			$acctid = $args['acctid'];
			$sql = "SELECT dragonkills FROM ".db_prefix("accounts")." WHERE acctid = $acctid";
			$result = db_fetch_assoc(db_query($sql));
			
			$tdk = $result['dragonkills'];
			$edk = get_module_pref("edks","edk",$acctid);
			$cdk = $tdk - $edk;
			if ($edk > 1 && $tdk > 1){
				output("Out of %s total Drive Kills, %s were Economic Drive Kills.`n",$tdk,$edk);
			} else if ($edk == 1){
				output("This player has completed an Economic Drive Kill.`n");
			}
		break;
		case "counciloffices":
			addnav("Ask about the Gun","runmodule.php?module=edk&op=start");
		break;
	}
	return $args;
}
function ekd_run(){
	global $session;
	page_header("The Gun");
	$op = httpget("op");
	switch($op){
		case "start":
			output("`0\"`#I've heard some silly rumours,`0\" you say, \"`#that some of the folks around here are working on some sort of massive gun, capable of destroying the Drive in one shot.`0\"`n`nThe woman behind the desk shakes her head and tuts.  \"`1Well now, that `iwould`i be silly,`0\" she says quietly.  \"`1Why on Earth would we want to do such a thing?`0\"  She reaches below the desk and brings up a truly enormous weapon that couldn't have possibly fit under there.  \"`1Think you can lift it`0\"`n`n\"`#`iMe?!`i`0\"`n`n\"`1I see nobody else with %s Requisition in their pocket.`0\"`n`n");
			$startcost = 50000;
			
			addnav("Proceed with standard difficulty","runmodule.php?module=edk&op=start");
			page_footer();
			break;
		}
	page_footer();
	return $args;
}
?>