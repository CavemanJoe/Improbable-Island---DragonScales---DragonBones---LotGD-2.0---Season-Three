<?php
// TODO:
// Add a thingy into the user's Bio to show that they're a permanent account holder
function hunterslodge_healthinsurance_getmoduleinfo(){
	$info = array(
		"name"=>"Hunter's Lodge Health Insurance",
		"author"=>"Dan Hall",
		"version"=>"2010-09-30",
		"download"=>"",
		"category"=>"Lodge Items",
	);
	return $info;
}

function hunterslodge_healthinsurance_install(){
	module_addhook("healmultiply");
	return true;
}
function hunterslodge_healthinsurance_uninstall(){
	return true;
}

function hunterslodge_healthinsurance_dohook($hookname,$args){
	global $session;
	switch($hookname){
	case "healmultiply":
		if ($session['user']['race']!="Robot"){
			if (has_item("hunterslodge_healthinsurance")){
				redirect("runmodule.php?module=hunterslodge_healthinsurance");
			}
		}
		break;
	}
	return $args;
}

function hunterslodge_healthinsurance_run(){
	global $session;
	page_header("Hospital Tent");
	output("The mechanic growls at you.  \"`6So you think you've got a certificate that gives you free healing, do you?`0\"  He snatches the certificate out of your hand.  \"`6Well, we'll just see about that, won't we?  RAFAEL!`0\"`n`nThe top drawer of the mechanic's filing cabinet rattles open and a nose peeks out, followed by a pair of bespectacled eyes and an oily black haircut.  The lawyer oozes out of the filing cabinet, scuttles over to the mechanic and takes the certificate.  He sniffs it, then cautiously nibbles on a corner, before pressing his greasy nose to the paper.`n`n\"`7Let's see, here...`0\" he mutters, in an annoying, nasal tone.  \"`7...an agreement between CavemanJoe, hereafter referred to as His Royal Awesomeness, and the bearer's posterior, hereafter referred to as Your Butt...  Hmm, hmm, I see...  agrees to not bend, fold, spindle or mutilate Your Butt...  yes, yes, all very official, all very legal...  valid for a period of one \"Real World\" week after first use...  oh, no.  Oh, dear.`0\"  He looks towards the mechanic, eyes filling with tears.  \"`7It's airtight!  We can't get out of it!`0\"`n`nThe mechanic snatches the certificate back and gives it a once-over.  The colour leavs his face.  \"`6My God,`0\" he whispers, staring into space.`n`nHe turns to you.  \"`6Get on the table,`0\" he mumbles.  \"`6And tell `ino-one`i about this.`0\"`n`nYou have been fully healed!`n`n");
	$session['user']['hitpoints'] = $session['user']['maxhitpoints'];
	use_item("hunterslodge_healthinsurance");
	addnav("Exit");
	addnav("Back to the Jungle","forest.php");
	page_footer();
}
?>
