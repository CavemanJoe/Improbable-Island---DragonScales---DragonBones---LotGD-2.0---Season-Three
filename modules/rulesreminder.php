<?php

require_once("lib/villagenav.php");
require_once("lib/http.php");

function rulesreminder_getmoduleinfo(){
	$info = array(
		"name"=>"Rules Reminder",
		"version"=>"2008-11-09",
		"author"=>"Dan Hall",
		"category"=>"Improbable",
		"download"=>"",
		"prefs"=>array(
			"Rules Reminder User Preferences,title",
			"seenrules"=>"Has the user read the rules?,int|0",
			"reminduser"=>"Does this user need a reminder?,int|0",
		),
	);
	return $info;
}
function rulesreminder_install(){
	module_addhook("forest");
	return true;
}
function rulesreminder_uninstall(){
	return true;
}
function rulesreminder_dohook($hookname,$args){
	global $session;
	switch($hookname){
	case "forest":
		if (get_module_pref("seenrules")==0 || get_module_pref("reminduser")==1){
			addnav("","runmodule.php?module=rulesreminder");
			redirect("runmodule.php?module=rulesreminder");
			return $args;
		}
		break;
	}
	return $args;
}
function rulesreminder_run(){
	global $session;
	page_header("Site Rules");
	if (get_module_pref("seenrules")==0){
		set_module_pref("seenrules",1);
		output("As you prepare to embark upon your newest adventure, a sombre-looking clown steps in front of you.  A little red name tag pinned to his chest says \"My name is MISTER NIPLOFF.  How can I help?\"  He is holding out a piece of paper in his left hand and a bag of rather soggy human nipples in his right.  Given the alternative, you take the piece of paper.`n`n");
		addnav("I couldn't agree more, Mister Niploff.  Please don't steal my nipples.","forest.php");
	}
	if (get_module_pref("reminduser")==1){
		set_module_pref("reminduser",0);
		output("As you prepare to embark upon your latest adventure, a sad-faced clown steps in front of you.  You recognise him as Mister Niploff, the sombre old soul who introduced you to the Game Rules.  But why is he here again?`n`nHe looks even more sombre than usual.  He reaches into his pocket and pulls out a large pair of scissors, which he prods gently against your chest.`n`n`bClearly he thinks you need a quick reminder about the rules!`b`n`nGiving you a pleading look, he pulls out his familiar piece of paper and offers it to you once again.`n`n");
		addnav("I understand, Mister Niploff.  I promise to be good in future.  Please don't steal my nipples.","forest.php");
	}
	output("It reads:`4`n`n`nOn Improbable Island, there are just two rules.  One is designed to make the game more fun for everyone, and the other is designed to make the game more fun for you.  Here they are:`n`n`n1. `bDon't be a dick`b.`nI don't think we really need to clarify what a dick is, because people who need that sort of clarification are probably dicks.  But for the avoidance of doubt, \"don't be a dick\" also encompasses \"don't talk like a dick,\" which is to say omfg pls dont talk liek dis bcos it makes u look liek a dick lol.`n`n2. `bDon't take it seriously`b.`nImprobable Island is only a computer game.  Furthermore, it's a light-hearted and irreverent computer game played over the Internet.  The Internet, as you well know, is composed nearly entirely out of LOLCats and porn.  So, of course, treating Improbable Island as `iSerious Business`i is a rather silly thing to do.  If you take the game too seriously, you won't enjoy it as much, and drama of the non-fun variety is a likely result.`nThis one is a little bit more complex, so I'll give some examples.`nExample 1: Someone PVP's you, and you go on a huge rant about it while refusing to turn off PVP (yes, you can opt-out of PVP).  This is pretty well the same as knocking the chess board off the table in the pub 'cause someone beat you.  The Admin steps in and bitch-slaps you for taking the game too seriously.`nExample 2: Someone makes a joke about a subject tangentially related to something you hold dear to your heart.  You immediately leap in with a rally cry of \"OMG TAHT'S NOT FUNNY YOU'RE WORSE THAN HITLER.\"  The Admin deletes your comments, then steps in and bitch-slaps you for taking the game too seriously.`nExample 3: The admin makes a slight change to the game mechanics, in an attempt to balance things out.  You send him a Distraction saying \"You've ruined the game!  All my hard work for nothing.\"  The Admin laughs at you for equating hard work with playing a computer game, and then steps in and bitch-slaps you for taking the game too seriously.  Then he twiddles his moustache and laughs some more.`n(Note that feedback about the game is encouraged, but if it contains the phrase \"hard work\" in relation to game-playing activity, it will immediately become a target of Admin Mockery regardless of its merit or lack thereof.)`n`n`nThere are also some technical rules that you should know about, although these don't relate to social interaction, or relate only tangentially.`n`n1. `bNo alts`b.`nAn alt is an account that a player creates in order to transfer resources between the alt and the player's main character.  This is cheating, and gives you an advantage over other players that can only be matched by them doing the same thing.  Thus, the game turns into a meaningless exercise in who has the most spare time and the least life outside of the Island.  That's not a game that anyone wants to win.`nUsage of alts for non-cheaty, prank-related purposes is okay.  In fact, this is actively encouraged.`n`n2. `bIf you find an exploit, tell the Admin.`b`nAn exploit is a bug in the game that allows you to gain an advantage over other players.  For example, you might find a way to keep large amounts of Requisition between DK's, or you might find a way to gain more of one resource without expending, or taking the risk of expending, any other resource (such as turns or hitpoints).  If you find one, exploit it, and it goes unnoticed, then the game becomes unbalanced, which can sometimes take a long time to fix.  Please report any and all loopholes and cheaty things to the Admin, using the Petition link.`n`n`nThanks for reading the rules, and have a pleasant stay!");
	page_footer();
}
?>