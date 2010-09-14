<?php
require_once("lib/http.php");
require_once("lib/villagenav.php");
function bloodbank_getmoduleinfo(){
	$info = array(
		"name"=>"Blood Bank Donation Center",
		"author"=>"WebPixie",
		"version"=>"1.0",
		"category"=>"Village",
		"download"=>"http://dragonprime.net/users/WebPixie/bloodbank_98.zip",
		"settings"=>array(
		// "bbankall"=>"Is the Blood Bank in all cities?,bool|0",
		// "bbankloc"=>"Where does the bloodbank appear,location|".getsetting("villagename", LOCATION_FIELDS)	
			),
		);
	return $info;
}

function bloodbank_install(){
	$condition = "if (\$session['user']['location'] == \"New Pittsburgh\") {return true;} else {return false;};";
	module_addhook("village",false,$condition);
	module_addhook("changesetting");
	return true;
}

function bloodbank_uninstall(){
	return true;
}

function bloodbank_dohook($hookname,$args){
	global $session;
	switch($hookname){
  	case "village":
		addnav($args["marketnav"]);
		addnav("Blood Donation Center", "runmodule.php?module=bloodbank");
	break;

	}
return $args;
}


function bloodbank_run(){
	global $session;
	$op = httpget("op");
	page_header("Blood Bank Donation Center");
		$loglev = $session['user']['level'];
		$cost = ($loglev * ($session['user']['hitpoints']-1)) + ($loglev*9);
		$cost = round($cost,0);
		$half = ($session['user']['hitpoints']-1)/2;
		$half = round($half,0);
		$cost2 = ($loglev * $half) + ($loglev*9);
		$cost2 = round($cost,0);


if ($op==""){
		output("`3You walk into a sterile room bathed in a harsh white light.");
		output("You notice a man standing in front of a large shelf filled with bottles of red liquid.");
		output("For one moment you think you've made a mistake entering.`n`n");

if($session['user']['level'] == 1){
		output("`3The old vampire glares at you. \"");
		output("`6You look a bit too small to donate blood.");
		output("You should come back when you have another training under your belt and a bit more hearty blood to sell.`3\"");
		output("The man then turns around and ignores you.`n`n");
		output("Might want to train a bit more before you return.");
			addnav("Return");
			villagenav();
		
		
	}elseif($session['user']['hitpoints'] == 1){
		output("`3The old vampire glares at you. \"");
		output("`6You look a bit pale."); 
		output("You should come back when you have blood to sell.`3\" says the hideous thing.");
		output("\"`6Hurry before I change my mind and take your last drop.`3\"");
			addnav("Return");
			villagenav();
	} else {

	if ($session['user']['hitpoints']>1){
		$loglev = log($session['user']['level']);
		$cost = ($loglev * ($session['user']['hitpoints']-1)) + ($loglev*9);
		$cost = round(100*$cost/100,0);
		$half = (($session['user']['hitpoints']-1)/2);
		$half = round($half,0);
		$cost2 = ($loglev * $half) + ($loglev*9);
		$cost2 = round(50*$cost2/100,0);
		output("\"`6Looking to make a donation?`3\" the man remarks.");
		output("\"`6We pay well for your blood if you are in need of coin. `3\"`n`n");
		output("\"`5My blood?  How much?`3\" you ask, ready to be out of the chilling place.`n`n");
		output("The man looks you up and down.  \"`6For your type... `$`b%s`b`6 gold pieces for a complete drain!!",$cost);
		output("You'll have just enough blood left to walk away`3\" he says as it bends over and pulls out a long needle from a drawer.");
		output("`n`nThe size of the needle is enough to drain your blood without help.`n`n");
		output("\"`6We will also buy just a portion of your blood if you are faint of heart... We can offer `$`b%s`b`6 gold pieces for a half drain. `3\" he says with a smirk.",$cost2);
		output("`n`n\"`6Interested?`3\"");
		addnav("Donations");
		addnav("`^Full Donation`0","runmodule.php?module=bloodbank&op=full$returnline");
		addnav("`^Half Donation`0","runmodule.php?module=bloodbank&op=half$returnline");
		

		addnav("`bReturn`b");
		
			villagenav();
		
	    }

	}

     }
switch($op){

	case "full":
		$full = ($session['user']['hitpoints']-1);
		$loglev = log($session['user']['level']);
		$cost = ($loglev * $full) + ($loglev*9);
		$cost = round(100*$cost/100,0);
		$session['user']['hitpoints']=1;
		$session['user']['gold']+=$cost;
		output("`3The old  vampire smiles at you, then takes you to a chair where he painfully inserts a needle into your arm. Slowly the blood starts to drip down the needle's tube.");
		output("You turn pale as your blood slowly drains.`n`n");
		output("You become a bit dizzy from your blood lose.`n`n");
		output("You have had %s health points drained ",$full);
		output("and have earned %s gold coins.`n`n",$cost);
			villagenav();
			break;
	case "half":
		$half = ($session['user']['hitpoints']-1)/2;
		$half = round($half,0);
		$loglev = log($session['user']['level']);
		$cost = ($loglev * $half) + ($loglev*9);
		$cost = round($cost*50/100,0);
		$session['user']['hitpoints']-=$half;
		$session['user']['gold']+=$cost;
		output("`3The old  vampire smiles at you, then takes you to a chair where he painfully inserts a needle into your arm.");
		output(" Slowly the blood starts to drip down the needle's tube.");
		output("You turn pale as your blood slowly drains.`n`n");
		output("You become a bit dizzy from your blood lose.`n`n");
		output("You have had %s health points drained ",$half);
		output("and have earned %s gold coins.`n`n",$cost);
			villagenav();
			break;
}
page_footer();
}
?>