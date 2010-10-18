<?php

page_header("Stake a Claim");

require_once "modules/improbablehousing/lib/lib.php";
global $session;

//first make sure someone didn't already fill up the space between this player's pageloads
$list = improbablehousing_getnearbyhouses($loc);
$nlist = count($list);
//todo: make this a setting
$maxhousespersquare = 4;
	if ($nlist<$maxhousespersquare){
	//get rid of the stake - make sure it's set to destroy itself after a single use
	use_item("housing_stake");

	//now make a house with a size of zero, set the owner, and so on and so forth
	$ownedby = $session['user']['acctid'];
	$loc = get_module_pref("worldXYZ","worldmapen");
	list($worldmapX, $worldmapY, $worldmapZ) = explode(",", $loc);
	
	$data = array(
		"rooms"=>array(
		),
		"name"=>"Empty Plot belonging to ".$session['user']['name'],
		"buildjobs"=>array(
			0=>array(
				"name"=>"Initial Dwelling Construction",
				"jobs"=>array(
					0=>array(
						"name"=>"Add wood",
						"iitems"=>array(
							"wood"=>1,
							"toolbox_carpentry"=>1,
						),
						"actions"=>array(
							"Carpentry"=>1,
						),
						"req"=>100,
						"done"=>0,
						"desc"=>"You set about hammering and sawing.  Before too long, the dwelling's one step closer to completion.",
					),
					1=>array(
						"name"=>"Add stone",
						"iitems"=>array(
							"stone"=>1,
							"toolbox_masonry"=>1,
						),
						"actions"=>array(
							"Masonry"=>1,
						),
						"req"=>25,
						"done"=>0,
						"desc"=>"You set about chiseling and tinkering.  Before too long, the dwelling's one step closer to completion.",
					),
				),
				"completioneffects"=>array(
					"changes"=>array(
						"name"=>"One-room dwelling belonging to ".$session['user']['name'],
						"desc_exterior"=>"You see a single-room dwelling belonging to ".$session['user']['name'],
					),
					"newrooms"=>array(
						0=>array(
							"name"=>"Main Room",
							"size"=>1,
							"desc"=>"You're standing in a small, undecorated room.",
							"sleepslots"=>array(
							),
						),
					),
					"msg"=>"The dwelling is now complete!",
				),
			),
		),
		"desc_exterior"=>"You see a sign indicating that a plot of land has been reserved for ".$session['user']['name'],
	);
	
	$bsql = "INSERT INTO ".db_prefix("buildings")." (ownedby,location) VALUES ('$ownedby','$loc')";
	db_query($bsql);
	$key = mysql_insert_id();
	
	$buildjobs = $data['buildjobs'];
	
	$buildjobs = serialize($buildjobs);
	$buildjobs = addslashes($buildjobs);
	
	$bpsql = "INSERT INTO ".db_prefix("building_prefs")." (hid,pref,value) VALUES ('$key','name','Empty Plot belonging to ".$session['user']['name']."'),('$key','desc_exterior','You see a sign indicating that a plot of land has been reserved for ".$session['user']['name']."'),('$key','buildjobs','$buildjobs')";
	db_query($bpsql);
	
	$data = serialize($data);	
	$data = addslashes($data);
	$sql = "INSERT INTO ".db_prefix("improbabledwellings")." (ownedby,location,data) VALUES ('$ownedby','$loc','$data')";
	db_query($sql);

	//invalidate cache
	invalidatedatacache("housing/housing_location_".$loc);

	//output confirmation message
	output("`0You hammer your stake into the ground.  Now you can start building a house!`n`n");
	addnav("Return");
	addnav("M?Back to the Island Map","runmodule.php?module=worldmapen&op=continue");
} else {
	output("You go to hammer in your stake, but realize that someone else has just nicked your spot!  Gah!`n`n");
	addnav("Return");
	addnav("M?Back to the Island Map","runmodule.php?module=worldmapen&op=continue");
}
page_footer();

?>