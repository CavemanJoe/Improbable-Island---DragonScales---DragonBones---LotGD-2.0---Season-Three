<?php
//Slightly modified by JT Traub in the original untranslated.php
	$info = array(
	    "name"=>"Translation Wizard",
		"version"=>"1.43",
		"author"=>"Originally Written by Christian Rutsch`nFilescan by `qEdorian`n`2enhanced by  Oliver Brendel",
		"category"=>"Administrative",
		"download"=>"http://dragonprime.net/users/Nightborn/translationwizard.zip",
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
?>