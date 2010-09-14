<?php

function scrapbots_getmoduleinfo(){
	$info = array(
		"name"=>"ScrapBots",
		"version"=>"2009-02-12",
		"author"=>"Dan Hall",
		"category"=>"Improbable",
		"download"=>"",
		"prefs"=>array(
			"scrap"=>"Player's Scrap Items array,viewonly|none",
			"gentleman"=>"Player is subject to the Gentleman's Agreement,bool|0",
			"retreatpct"=>"Player's whole army flees if more than this percentage of its ScrapBots are removed from the fight,int|0",
		),
		"settings"=>array(
			"scrap"=>"Scrap Items array,viewonly|none",
		),
	);
	return $info;
}
function scrapbots_install(){
	$scrapbots = array(
		'id'=>array('name'=>'id', 'type'=>'int(11) unsigned', 'extra'=>'auto_increment'),
		'owner'=>array('name'=>'owner', 'type'=>'int(11) unsigned'),
		'name'=>array('name'=>'name', 'type'=>'text'),
		'activated'=>array('name'=>'activated', 'type'=>'int(11) unsigned'),
		'hitpoints'=>array('name'=>'hitpoints', 'type'=>'int(11) unsigned'),
		'brains'=>array('name'=>'brains', 'type'=>'int(11) unsigned'),
		'brawn'=>array('name'=>'brawn', 'type'=>'int(11) unsigned'),
		'briskness'=>array('name'=>'briskness', 'type'=>'int(11) unsigned'),
		'junglefighter'=>array('name'=>'junglefighter', 'type'=>'int(11) unsigned'),
		'retreathp'=>array('name'=>'retreathp', 'type'=>'int(11) unsigned'),
		'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'id'),
	);
	require_once("lib/tabledescriptor.php");
	synctable(db_prefix('scrapbots'), $scrapbots, true);
	module_addhook("village");
	module_addhook("newday");
	require_once "modules/staminasystem/lib/lib.php";
	install_action("Scavenging for Scrap",array(
		"maxcost"=>10000,
		"mincost"=>2500,
		"firstlvlexp"=>1500,
		"expincrement"=>1.1,
		"costreduction"=>75,
		"class"=>"ScrapBots"
	));
	install_action("Metalworking",array(
		"maxcost"=>10000,
		"mincost"=>3000,
		"firstlvlexp"=>1500,
		"expincrement"=>1.1,
		"costreduction"=>60,
		"class"=>"ScrapBots"
	));
	install_action("Soldering",array(
		"maxcost"=>10000,
		"mincost"=>3000,
		"firstlvlexp"=>1500,
		"expincrement"=>1.1,
		"costreduction"=>60,
		"class"=>"ScrapBots"
	));
	install_action("Programming",array(
		"maxcost"=>10000,
		"mincost"=>3000,
		"firstlvlexp"=>1500,
		"expincrement"=>1.1,
		"costreduction"=>60,
		"class"=>"ScrapBots"
	));
	return true;
}
function scrapbots_uninstall(){
	uninstall_action("Scavenging for Scrap");
	uninstall_action("Metalworking");
	uninstall_action("Soldering");
	uninstall_action("Programming");
	return true;
}
function scrapbots_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "village":
			addnav("ScrapBots Testing","runmodule.php?module=scrapbots");
			break;
		case "newday":
			require_once("modules/scrapbots/lib.php");
			scrapbots_newday_healers();
			scrapbots_newday_junglefights();
			scrapbots_newday_scavengers();
			break;
		}
	return $args;
}
function scrapbots_run(){
	global $session;
	page_header("ScrapBots testing");
	if ($session['user']['alive']==0){
		addnav("Daily News","news.php");
		page_footer();
		break;
	} else {
		addnav("Scavenge");
		addnav("Scavenge x 1","runmodule.php?module=scrapbots&scavenge=1");
		addnav("Scavenge x 10","runmodule.php?module=scrapbots&scavenge=10");
		addnav("Village","village.php");
		addnav("Search for an Opponent","runmodule.php?module=scrapbots&op=findopponent");
		require_once("modules/scrapbots/lib.php");
		$op = httpget('op');
		if ($op){
			include("scrapbots/run/case_$op.php");
		}
		if (httpget('reset')==1){
			set_module_pref("scrap",0);
		}
		if (httpget('scavenge')==1){
			scrapbots_scavenge();
			output("`n`n");
		}
		if (httpget('scavenge')==10){
			scrapbots_scavenge(10);
			output("`n`n");
		}
		if (httpget('testfight')==1){
			require_once("modules/scrapbots/battle-2.php");
			scrapbots_battle(96);
		}
		scrapbots_list_player_scrap();
		scrapbots_list_combinations();
		scrapbots_list_scrapbots();
		scrapbots_list_requirements();
		addnav("Debug");
		addnav("Clear Scrap array","runmodule.php?module=scrapbots&reset=1");
		addnav("Fight against playerid 96","runmodule.php?module=scrapbots&testfight=1");
	}
	page_footer();
}
?>