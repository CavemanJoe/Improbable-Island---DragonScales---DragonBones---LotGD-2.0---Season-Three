<?php

require_once("lib/villagenav.php");
require_once("lib/http.php");

function cakeordeath_getmoduleinfo(){
	$info = array(
		"name"=>"Cake Or Death",
		"version"=>"2009-10-02",
		"author"=>"Dan Hall",
		"category"=>"Village",
		"download"=>"",
		"settings"=>array(
			"counter"=>"How many more slices of cake will be given before DEATH occurs?,int|100",
		),
	);
	return $info;
}
function cakeordeath_install(){
	$condition = "if (\$session['user']['location'] == \"AceHigh\") {return true;} else {return false;};";
	module_addhook("village",false,$condition);
	return true;
}
function cakeordeath_uninstall(){
	return true;
}
function cakeordeath_dohook($hookname,$args){
	global $session;
	switch($hookname){
	case "village":
		// if ($session['user']['location'] == get_module_setting("location")) {
			tlschema($args['schemas']['fightnav']);
			addnav($args['fightnav']);
			tlschema();
			addnav("Cake Or Death","runmodule.php?module=cakeordeath&op=examine");
		// }
		break;
	}
	return $args;
}
function cakeordeath_run(){
	global $session;
	page_header("Cake Or Death");
	switch (httpget("op")){
		case "examine":
			// Tell the player what the deal with Cake Or Death is
			$counter=number_format(get_module_setting("counter"));
			output("`0A shiny wooden table sits back from the main street.  Behind it, a man sits idly reading the Improbable Island Enquirer.  Before him, sat on the table, is a large sponge cake.  Above him is a banner, displaying the name of his game:`b'`5Cake`0 or `4Death!`0'`b`n`nHe sees you pondering the sign, and calls over to you.  `b'`5Cake`0 or `4Death!`0  `b`5Cake`0 or `4Death!`0' he cries.  'Ninety-nine per cent chance of `b`5Cake`0`b!'`n`nIt's not often that an immaculately-dressed gentleman with glowing green eyes offers you a 99% chance of cake.  What would you like to do?");
			//add navs
			addnav("CAKE!","runmodule.php?module=cakeordeath&op=play");
			addnav("Back away slowly","village.php");
			break;
		case "play":
			$counter=get_module_setting("counter");
			addnav("Back to the Outpost","village.php");
			if ($counter>0){
				output("The green-eyed gentleman hands you a slice of cake, on a paper plate.  You thank him, and walk away merrily wolfing down your prize.`n`nYou feel `5Full Of Cake!`0");
				set_module_setting("counter", get_module_setting("counter")-1);
				apply_buff('tastycake', array(
					"name"=>"`5Full Of Cake`0",
					"rounds"=>10,
					"atkmod"=>1.1,
					"defmod"=>1.1,
					"roundmsg"=>"`5The cake you ate earlier has boosted your energy!`n",
					"schema"=>"module-cakeordeath"
				));
			}
			if ($counter<=0){
				output("The green-eyed gentleman hands you a slice of cake, on a paper plate.  You thank him, and walk away merrily wolfing down your prize.`n`nYou feel `5Full Of Cake!`0`n`nMoments later, the slow-acting poison starts to take effect.  The world begins to melt in front of you.  Grey spots dance on the edges of your vision.  Behind you, a green-eyed monster offers you another slice of cake, laughing and pointing.`n`nYou curse your luck as the hallucinations begin to kick in.");
				set_module_setting("counter", 100);
				apply_buff('failcake', array(
					"name"=>"`5Full Of FailCake`0",
					"rounds"=>-1,
					"regen"=>-10,
					"startmsg"=>"`5You are walking on pink icing.  The sky is made of jam.  Your eyes are two cherries.  That cake was awesome.`0`n",
					"roundmsg"=>"`5The poisoned cake saps your strength, and you lose ten hitpoints!`0`n",
					"schema"=>"module-cakeordeath"
				));
				if (is_module_active("medals")){
					require_once "modules/medals.php";
					medals_award_medal("failcake","Failcake Fancier","This player was unfortunate at the Cake or Death stand...","medal_failcake.png");
				}
			}
			break;
	}
	page_footer();
}
?>