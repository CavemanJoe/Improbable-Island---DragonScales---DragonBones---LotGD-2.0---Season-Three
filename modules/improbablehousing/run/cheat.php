<?php

global $session;

$hid = httpget('hid');
$rid = httpget('rid');
require_once "modules/improbablehousing/lib/lib.php";
$house = improbablehousing_gethousedata($hid);

page_header("Cheat");
addnav("Cheat!");
addnav("Cheat a new room","runmodule.php?module=improbablehousing&op=cheat&sub=newroom&hid=$hid&rid=$rid");
addnav("Cheat a blank house","runmodule.php?module=improbablehousing&op=cheat&sub=resethouse&hid=$hid&rid=$rid");
addnav("Cheat loads of Stamina","runmodule.php?module=improbablehousing&op=cheat&sub=stamina&hid=$hid&rid=$rid");

switch (httpget('sub')){
	case "stamina":
		output("Adding stamina");
		require_once "modules/staminasystem/lib/lib.php";
		addstamina(10000000);
	break;
	case "newroom":
		output("You've started a new build job to create a new room.  Have at it!`n`n");
		$newjob = array(
			"name"=>"New Room Construction",
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
					"req"=>2,
					"done"=>0,
					"desc"=>"You set about hammering and sawing.  Before too long, the new room is one step closer to completion.",
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
					"req"=>2,
					"done"=>0,
					"desc"=>"You set about chiseling and tinkering.  Before too long, the new room is one step closer to completion.",
				),
			),
			"completioneffects"=>array(
				"newrooms"=>array(
					0=>array(
						"name"=>"Extension",
						"enterfrom"=>$rid,
						"size"=>1,
						"desc"=>"You're standing in a small, undecorated extension room.",
						"sleepslots"=>array(
							0=>array(
								"name"=>"Floor space",
								"stamina"=>50000,
							),
							1=>array(
								"name"=>"Floor space",
								"stamina"=>50000,
							),
						),
					),
				),
				"msg"=>"The new room is now complete!",
			),
		);
		$house['data']['buildjobs'][]=$newjob;
	break;
	case "resethouse":
		output("House reset to blank.");
		$house['data'] = array(
			"rooms"=>array(
				0=>array(
					"name"=>"Main Room",
					"size"=>1,
					"desc"=>"You're standing in a small, undecorated room.",
					"sleepslots"=>array(
						0=>array(
							"name"=>"Floor space",
							"stamina"=>50000,
						),
						1=>array(
							"name"=>"Floor space",
							"stamina"=>50000,
						),
					),
				),
			),
			"name"=>"One-room dwelling belonging to ".$session['user']['name'],
			"desc_exterior"=>"You see a single-room dwelling belonging to ".$session['user']['name'],
		);
	break;
}

improbablehousing_sethousedata($house);
improbablehousing_bottomnavs($house);
page_footer();

?>