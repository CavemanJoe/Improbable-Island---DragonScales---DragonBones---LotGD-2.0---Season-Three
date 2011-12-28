<?php

function commentarycommands_flavour_getmoduleinfo(){
	$info=array(
		"name"=>"Commentary Commands: Flavour Text",
		"version"=>"2010-07-23",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"Commentary Commands",
		"download"=>"",
	);
	return $info;
}

function commentarycommands_flavour_install(){
	module_addhook_priority("commentarycommand",100);
	return true;
}

function commentarycommands_flavour_uninstall(){
	return true;
}

function commentarycommands_flavour_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "commentarycommand":
			if (substr_count($args['command'],"PUMPKIN")){
				$args['processed']=1;
				output("`c`b`QWhat Pumpkin?`0`b`c`n");
			}
			if (substr_count($args['command'],"PAUSE")){
				$args['processed']=1;
				output("`c`2`bGAME PAUSED`b`nYou pause the Persistent-Browser-Based-Text-Adventure-That-Has-No-Realtime-Content.  Nothing happens.`c`n");
			}
			if (substr_count($args['command'],"SAVE")){
				$args['processed']=1;
				output("`c`2`bGame Saved.`b`nGood thing you found this command, or everything would have been lost when you logged out!  Make sure to type it in every chat box you come across, just in case.`c`n");
			}
			if (substr_count($args['command'],"LOAD")){
				$args['processed']=1;
				output("`c`2`bLoad Failed.`b`nPress Play on tape to retry.`c`n");
			}
			if (substr_count($args['command'],"EJECT")){
				$args['processed']=1;
				output("`c`2`bTape Ejected.`b`nPut it back!  PUT IT BACK!`c`n");
			}
			if (($args['section']=="village-Human_aux" || $args['section']=="village-Human") && substr_count($args['command'],"MUSEUM")){
				output("`c`2You look over towards the Museum.  It's a fairly basic hut, on top of which someone has posted a sign saying \"NewHome Museum of Improbable Things.\"  Maybe you should head over there and check it out.`0`c`n");
				$args['processed']=1;
			}
			if (($args['section']=="village-Human_aux" || $args['section']=="village-Human") && substr_count($args['command'],"TRAINING")){
				output("`c`2You look over towards the Basic Training compound.  Even from here you can hear Corporal Punishment yelling at a bewildered Rookie.  He's shouting something about a text parser; honestly you're glad you're not around to hear it.  The man talks a load of bollocks.`0`c`n");
				$args['processed']=1;
			}
			if (($args['section']=="village-Kittymorph_aux" || $args['section']=="village-Kittymorph") && (substr_count($args['command'],"MAIKO") || substr_count($args['command'],"COOKERY"))){
				output("`c`2You look over towards Maiko's Cookery Academy.  After a few moments, Maiko herself appears at a window.  She catches your eye and waves to you, before closing the blinds.  Moments later, you hear a muffled iBOOM-ch-KA`i from within, followed by a heavy thump, and some low moans.  Clearly Maiko is in the middle of a class.`0`c`n");
				$args['processed']=1;
			}
			if (($args['section']=="village_aux" || $args['section']=="village") && substr_count($args['command'],"ROCK")){
				output("`c`2You look at the Rock.`n`nMan, that is one classy rock.`0`c`n");
				$args['processed']=1;
			}
			if (($args['section']=="village_aux" || $args['section']=="village") && (substr_count($args['command'],"PUB") || substr_count($args['command'],"SPIDERKITTY") || substr_count($args['command'],"PRANCING"))){
				output("`c`2You look over towards the Prancing SpiderKitty.  A rabbi, a priest, a minister and a horse are all heading inside.  This could get ugly.`0`c`n");
				$args['processed']=1;
			}
			if (($args['section']=="village-Midget_aux" || $args['section']=="village-Midget") && substr_count($args['command'],"SKUNK")){
				output("`c`2You'd really rather not do `ianything`i involving the dead skunks, thank you very much.`0`c`n");
				$args['processed']=1;
			}
			if (($args['section']=="village-Midget_aux" || $args['section']=="village-Midget") && substr_count($args['command'],"SKRONKY")){
				output("`c`2You look over towards the Skronky Pot.  Then you look away again, `iquickly`i.`0`c`n");
				$args['processed']=1;
			}
		break;
	}
	return $args;
}

function commentarycommands_flavour_run(){
	return true;
}
?>