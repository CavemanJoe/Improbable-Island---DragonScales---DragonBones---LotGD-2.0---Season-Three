<?php

function counciloffices_getmoduleinfo(){
	$info = array(
		"name"=>"Council Offices",
		"author"=>"Cousjava",
		"version"=>"2012-02-25",
		"category"=>"Council Offices",
		"download"=>"",
		"settings"=>array(
			"baseprice"=>"The base amount to be paid,int|50",
			"distancepayout"=>"Average payout per square per level,int|10",),
		"prefs"=>array(
			"deliveredtoday"=>"Has the user delivered a parcel today,bool|0",
			"hasparcel"=>"The user has a parcel in their posession,bool|0",
			"deliveryprice"=>"The price they will get paid when they deliver,int|0",
			"destination"=>"Destination of parcel,text",
			"parcelslost"=>"The total no of parcel lost by the user,int|0",
			"parcelsdelivered"=>"The total of parcels delivered,int|0",
			"dayslost"=>"days since last lost parcel,int|0",)
		
	);
	return $info;
}

function counciloffices_install(){
	module_addhook("village");
	module_addhook("newday");
	module_addhook("oneshotteleporter");
	return true;
}

function counciloffices_uninstall(){
	return true;
}

function counciloffices_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "village":
			tlschema($args['schemas']['fightnav']);
			addnav($args['fightnav']);
			tlschema();
			addnav("Council Offices","runmodule.php?module=counciloffices&councilop=enter");
			break;
		case "newday":
			set_module_pref("deliveredtoday",0);
			increment_module_pref("dayslost",1);
			break;
		case "oneshotteleporter":
			set_module_pref("hasparcel",0);
			set_module_pref("dayslost",0);
			increment_module_pref("parcelslost",1);
			break;
	}
	return $args;
}

function counciloffices_run(){
	global $session;
	page_header("Council Offices");
	switch (httpget('councilop')){
		case "enter":
			switch ($session['user']['location']){
				case "NewHome":
					output("The \"Council Offices\" of this Outpost amount to a tiny hut with a man inside reading a newspaper behind a desk.  He looks up as you come in.`n`n\"`1Can I help you?`0\"`n`n");
					break;
				case "Kittania":
					output("The \"Council Offices\" of this Outpost amount to a tiny hut with a patient-looking KittyMorph sat behind a desk inside.  She looks up as you come in.`n`n\"`1What can I do for you?`0\"`n`n");
					break;
				case "New Pittsburgh":
					output("The \"Council Offices\" of this Outpost amount to a tiny hut with a patient-looking Zombie sat behind a desk inside.  She looks up as you come in.`n`n\"`1What can I do for you?`0\"`n`n");
					break;
				case "Squat Hole":
					output("You step into the dilapidated Council Offices.  For a moment, you believe yourself to be alone; then, you notice the shining bald head sat behind the desk.  A squeaky voice shouts \"`1Y'arright there chuck, what d'ya want?`0\"`n`n");
					break;
				case "Pleasantville":
					output("The \"Council Offices\" of this Outpost amount to a tiny hut with a patient-looking Mutant sat behind a desk inside.  He looks up as you come in.`n`n\"`1What can I do for you?`0\"`n`n");
					break;
				case "Cyber City 404":
					output("The \"Council Offices\" of this Outpost amount to a tiny hut with a stern-looking Robot sat behind a desk inside.  He looks up as you come in.`n`n\"`1State your request.`0\"`n`n");
					break;
				case "AceHigh":
					output("The \"Council Offices\" of this Outpost amount to a tiny hut with an immaculately-dressed woman sat reading a newspaper behind a desk.  She looks up as you come in, eyes giving off a faint green glow.`n`n\"`1What can I do for you?`0\"`n`n");
					break;
				case "Improbable Central":
					output("The \"Council Offices\" of this Outpost amount to a tiny hut with a man inside reading a newspaper behind a desk.  He looks up as you come in.`n`n\"`1Can I help you?`0\"`n`n");
					break;
			}
			//modulehook("counciloffices");
			addnav("State your business.");
			addnav("You know, I don't have a clue what I came in here for.  Back to the Outpost.","village.php");
			$delivered=get_module_pref("deliveredtoday");
			$gotparcel=get_module_pref("hasparcel");
			if ($delivered!=true && $gotparcel==false){
			addnav("Do you have a parcel that needs to be delivered?","runmodule.php?module=counciloffices&councilop=getparcel");}
			$gotparcel=get_module_pref("hasparcel");
			if ($gotparcel==true){
			addnav("Deliver parcel","runmodule.php?module=counciloffices&councilop=deliver");}
			break;
		case "getparcel":
			
			require_once("modules/cityprefs/lib.php");
			$cid = get_cityprefs_cityid("city",$session['user']['location']);
			output("cid is $cid");
			$dest=counciloffices_randcity($cid);
			$distances=array(//this is contains the presumed distances traveled. all terrains are given equal weight. Feel free to change.
			"NewHome"=>array("Kittania"=>9,"Squat Hole"=>12,"New Pittsburgh"=>10,"Improbable Central"=>6,"Pleasantville"=>19,"AceHigh"=>25,"Cyber City 404"=>33),
			"Kittania"=>array("NewHome"=>9,"Squat Hole"=>10,"New Pittsburgh"=>16,"Improbable Central"=>7,"Pleasantville"=>12,"AceHigh"=>18,"Cyber City 404"=>26),
			"Squat Hole"=>array("NewHome"=>12,"Kittania"=>10,"New Pittsburgh"=>6,"Improbable Central"=>6,"AceHigh"=>13,"Pleasantville"=>7,"Cyber City 404"=>21),
			"New Pittsburgh"=>array("NewHome"=>10,"Kittania"=>16,"Squat Hole"=>6,"Improbable Central"=>9,"Pleasantville"=>10,"AceHigh"=>15,"Cyber City 404"=>23),
			"Improbable Central"=>array("NewHome"=>6,"Kittania"=>7,"Squat Hole"=>6,"New Pittsburgh"=>9,"AceHigh"=>19,"Pleasantville"=>13,"Cyber City 404"=>27),
			"Pleasantville"=>array("NewHome"=>19,"Kittania"=>12,"Squat Hole"=>7,"New Pittsburgh"=>10,"Improbable Central"=>13,"AceHigh"=>6,"Cyber City 404"=>14),
			"AceHigh"=>array("NewHome"=>25,"Kittania"=>18,"New Pittsburgh"=>15,"Squat Hole"=>13,"Improbable Central"=>19,"Pleasantville"=>6,"Cyber City 404"=>12),
			"Cyber City 404"=>array("NewHome"=>33,"Kittania"=>26,"New Pittsburgh"=>23,"Squat Hole"=>21,"Improbable Central"=>27,"Pleasantville"=>14,"AceHigh"=>12),
			);
			$baseprice=get_module_setting("baseprice");
			$distprice=get_module_setting("distancepayout");
			if ($distances[$session['user']['location']][$dest]){
				$pay=$baseprice+$distprice*$session['user']['level']*$distances[$session['user']['location']][$dest];
			} else {
				$defaultdistance=5;
				$pay=$baseprice+$distprice*$session['user']['level']*$defaultdistance;
			}
			//for debugging, delete later
			output("Deliver to dest $dest");

			output("`1\"We need this delivered within 3 days to %s. Get it there, and you'll be paid %s. Do you want it?`0\"",$dest,$pay);
			addnav("Take parcel");
			addnav("Yes","runmodule.php?module=counciloffices&councilop=take&dest=$dest&pay=$pay");
			addnav("No","runmodule.php?module=counciloffices&councilop=enter");
			break;
		case "take":
			addnav("Hang Around","runmodule.php?module=counciloffices&councilop=enter");
			addnav("Return to Outpost","village.php");
			$dest=httpget('dest');
			$pay=httpget('pay');
			output("\"`1Here's the parcel. Remember to take it to %s quickly.`0\"",$dest);
			set_module_pref("hasparcel",1);
			set_module_pref("destination",$dest);
			set_module_pref("deliveryprice",$pay);
			break;
		case "deliver":
			addnav("What now?");
			addnav("Return to Outpost","village.php");
			addnav("Hang around","runmodule.php?module=counciloffices&councilop=enter");
			$pay=get_module_pref("deliveryprice");
			output("You hand over the parcel, and get %s requisition tokens in return.`n`n",$pay);
			$session['user']['gold']+=$pay;
			set_module_pref("hasparcel",0);
			set_module_pref("deliveredtoday",1);
			increment_module_pref("parcelsdelivered",1);
			break;
	}
	modulehook("counciloffices");
	page_footer();
}

function counciloffices_randcity($cid){
	$sql="SELECT cityname FROM ".db_prefix("cityprefs")." WHERE cityid!='$cid' ORDER BY RAND() LIMIT 1";	
	$result=mysql_query($sql);
	if (!$result) {
    		output('Could not query:' . mysql_error());
	}
	//$num=mysql_num_rows($result);	
	$res=mysql_result($result,0);	
	return $res;
	
}

?>
