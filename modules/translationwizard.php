<?php
//For versioninfos just take a look at /modules/translationwizard/versions.txt

// Okay, someone wants to use this outside of normal game flow.. no real harm
define("OVERRIDE_FORCED_NAV",true);
// Translate Untranslated Strings
// Originally Written by Christian Rutsch
// Slightly modified by JT Traub

function translationwizard_getmoduleinfo(){
	//Slightly modified by JT Traub in the original untranslated.php
	$info = array(
	    "name"=>"Translation Wizard",
		"version"=>"1.44",
		"author"=>"Originally Written by Christian Rutsch`nFilescan by `qEdorian`n`2enhanced by  Oliver Brendel",
		"category"=>"Administrative",
		"download"=>"http://lotgd-downloads.com",
		"settings"=>array(
		"Translation Wizard - Preferences,title",
		"blocktrans"=>"Block the Untranslated Text in the grotto,bool|0",
		"blockcentral"=>"Block the Central Translations Section in the wizard,bool|0",
		"query"=>"Use nested query (don't works with lower mysql servers),bool|0",
		"page"=>"How many results per page for fixing/checking,int|20",
		"Restrictions are: search+edit the translations table + truncate untranslated,note",
		"restricted"=>"Has the wizard restrictions for some users?,bool|0",
		"This is only for skilled users! Its not finding everything yet,note",
		"and your untranslated gets filled quickly if you begin to use this at start,note",
		"but if you want to scan new modules automatically on install - here it is,note",
		"autoscan"=>"Scan automatically modules upon install and insert into untranslated,bool|0",
		"translationdelete"=>"Ask if translations should be deleted at uninstallation of a module,bool|0",
		),
		"prefs"=>array(
		    "Translation Wizard - User prefs,title",
			"language"=>"Languages for the Wizard,enum,en,English,de,Deutsch,pt,Portuguese,dk,Danish,es,Español,it,Italiano,ru,Russian,ee, Estonian(Eesti)",
			"Note: don't change this if you don't need to... it is set up in the Translation Wizard!,note",
			"allowed"=>"Does this user have unrestricted access to the wizard?,bool|0",
			"Note: This is only active if the restriction settings is 'true' in the module settings,note",
			"view"=>"Use advanced view (shows more),bool|0",
		),
		);
    return $info;
}

function translationwizard_install(){
	module_addhook("superuser");
	module_addhook("header-modules");
	if (is_module_active("translationwizard")) output_notl("`c`b`$ Module Translationwizard updated`b`c`n`n");
		$wizard=array(
		'tid'=>array('name'=>'tid', 'type'=>'int(11) unsigned', 'extra'=>'auto_increment'),
		'language'=>array('name'=>'language', 'type'=>'varchar(10)'),
		'uri'=>array('name'=>'uri', 'type'=>'varchar(255)'),
		'intext'=>array('name'=>'intext', 'type'=>'text'),
		'outtext'=>array('name'=>'outtext', 'type'=>'text'),
		'author'=>array('name'=>'author', 'type'=>'varchar(50)'),
		'version'=>array('name'=>'version', 'type'=>'varchar(50)'),
		'key-PRIMARY' => array('name'=>'PRIMARY', 'type'=>'primary key', 'unique'=>'1', 'columns'=>'tid'),
		'key-one'=> array('name'=>'language', 'type'=>'key', 'unique'=>'0', 'columns'=>'language,uri'),
		'key-two'=> array('name'=>'uri', 'type'=>'key', 'unique'=>'0', 'columns'=>'uri'),
		);
		require_once("lib/tabledescriptor.php");
		synctable(db_prefix("temp_translations"), $wizard, true);
	return true;
}

function translationwizard_uninstall()
{
  output_notl ("Performing Uninstall on Translation Wizard. Thank you for using!`n`n");
 if(db_table_exists(db_prefix("temp_translations"))){
	db_query("DROP TABLE ".db_prefix("temp_translations"));
	}
  return true;
}


function translationwizard_dohook($hookname, $args){
	global $session;
	require("./modules/translationwizard/translationwizard_dohook.php");
	return $args;
}

function translationwizard_run(){
	global $session,$logd_version,$coding;
	check_su_access(SU_IS_TRANSLATOR); //check again Superuser Access
	$op = httpget('op');
	page_header("Translation Wizard");
	//get some standards
	$languageschema=get_module_pref("language","translationwizard");
	//these lines grabbed the local scheme, in 1.1.0 there is a setting for it
	$coding=getsetting("charset", "ISO-8859-1");
	$viewsimple=get_module_pref("view","translationwizard");
	$mode = httpget('mode');
	$namespace = httppost('ns');
	$from = httpget('from');
	$page = get_module_setting(page);
	if (httpget('ns')<>"" && $namespace=="") $namespace=httpget('ns'); //if there is no post then there is maybe something to get
	$trans = httppost("transtext");
	if (is_array($trans))  //setting for any intexts you might receive
		{
		$transintext = $trans;
		}else
		{
		if ($trans) $transintext = array($trans);
		else $transintext = array();
		}
	$trans = httppost("transtextout");
	if (is_array($trans)) //setting for any outtexts you might receive
		{
		$transouttext = $trans;
		}else
		{
		if ($trans) $transouttext = array($trans);
		else $transouttext = array();
		}
	//end of the header
	if ($op=="")  $op="default";
	require("./modules/translationwizard/errorhandler.php");	
	require("./modules/translationwizard/$op.php");
	require_once("lib/superusernav.php");
	superusernav();
	require("./modules/translationwizard/build_nav.php");
	page_footer();
}

?>
