<?php

$hid = httpget('hid');
$rid = httpget('rid');

page_header("Create a new Build Job");

require_once "modules/improbablehousing/lib/lib.php";
$house=improbablehousing_gethousedata($hid);

switch(httpget('sub')){
	case "increasesize":
		output("Increasing the size of a room allows more people to sleep overnight and get their Stamina boost.`n`nFor a room extension that will allow one more sleeper, you'll have to perform the Masonry action five times, and the Carpentry action twenty times.  Of course, you'll also need the tools and materials associated with those actions.  As with all build jobs, your friends can help too.`n`nYou are about to set up a build job to increase the size of the room called \"`b%s`b.`0\"  Do you want to create this build job?`n`n",$house['data']['rooms'][$rid]['name']);
		$stackedincreases = 0;
		if (count($house['data']['buildjobs'])>0){
			foreach ($house['data']['buildjobs'] AS $job => $data){
				if (count($data['completioneffects']['rooms']) > 0){
					foreach ($data['completioneffects']['rooms'] AS $jobroom => $rdata){
						if ($jobroom == $rid && $rdata['deltas']['size']){
							$stackedincreases += 1;
						}
					}
				}
			}
		}
		if ($stackedincreases){
			if ($stackedincreases == 1){
				output("`c`b`\$Careful!`0`b`nYou've already set up a build job to increase the size of this room!  If you add a new build job, then you and your friends will be able to perform this room size increase once the current room size increase is finished.  In other words, you can have multiple build jobs queued up per room, but you can only add materials to them sequentially.`c`n`n");
			} else {
				output("`c`b`\$Careful!`0`b`nYou've already set up %s build jobs to increase the size of this room!  If you add a further build job, then you and your friends will have to complete the room size increases in sequence.`c`n`n",$stackedincreases);
			}
		}
		addnav("Confirm");
		addnav("Yeah, do it!","runmodule.php?module=improbablehousing&op=buildjobs&sub=increasesize-confirm&hid=$hid&rid=$rid");
	break;
	case "newroom":
		output("You can add a new room to your dwelling, resulting in a new, lockable chatroom that can, once expanded, accomodate more sleepers - but be warned, it'll be nearly as big a job as it was to erect the dwelling in the first place.`n`nFor a new room, you'll have to perform the Masonry action twenty times, and the Carpentry action seventy times.  Naturally you'll also need tools and materials.  After then, you can decorate as normal.  As with all build jobs, your friends can help too.`n`n`bBe careful`b - rooms branch off each other, so that you can do things like ensuite bathrooms, upstairs corridors and other rooms that can't be accessed without first going through other rooms.  You're currently building an extra room that can be accessed from the room called \"%s`0.\"  Make sure this is what you want.  You'll still be able to make new rooms branching off the same room, and rooms branching off this new room, and rooms branching off those rooms and so on.`n`nDo you want to create this build job?`n`n",$house['data']['rooms'][$rid]['name']);
		$stackedrooms = 0;
		if (count($house['data']['buildjobs'])>0){
			foreach ($house['data']['buildjobs'] AS $job => $data){
				if (count($data['completioneffects']['newrooms']) > 0){
					foreach ($data['completioneffects']['newrooms'] AS $jobroom => $rdata){
						if ($rdata['enterfrom'] == $rid){
							$stackedrooms += 1;
						}
					}
				}
			}
		}
		if ($stackedrooms){
			if ($stackedrooms == 1){
				output("`c`b`\$Careful!`0`b`nYou've already set up a build job to create a new room branching from this one!  If you add a new build job, then you and your friends will be able to perform this new room creation once the current room size creation is finished.  In other words, you can have multiple build jobs queued up per room, but you can only add materials to them sequentially.`c`n`n");
			} else {
				output("`c`b`\$Careful!`0`b`nYou've already set up a build job to create %s new rooms branching from this one!  If you add a further build job, then you and your friends will have to complete the new rooms in sequence.`c`n`n",$stackedrooms);
			}
		}
		addnav("Confirm");
		addnav("Yeah, do it!","runmodule.php?module=improbablehousing&op=buildjobs&sub=newroom-confirm&hid=$hid&rid=$rid");
	break;
	case "increasesize-confirm":
		output("You've started a new build job to increase the size of this room!`n`n");
		$newjob = array(
			"name"=>"Room Size Increase (".$house['data']['rooms'][$rid]['name']."`0)",
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
					"req"=>20,
					"done"=>0,
					"desc"=>"You set about hammering and sawing.  Before too long, the room expansion is one step closer to completion.",
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
					"req"=>5,
					"done"=>0,
					"desc"=>"You set about chiseling and tinkering.  Before too long, the room expansion is one step closer to completion.",
				),
			),
			"completioneffects"=>array(
				"rooms"=>array(
					$rid=>array(
						"deltas"=>array(
							"size"=>1,
						),
						"newsleepslots"=>array(
							0=>array(
								"name"=>"Floor space",
								"stamina"=>50000,
								"desc"=>"You settle down for a good night's kip on the floor.",
							),
						),
					),
				),
				"msg"=>"The room expansion is now complete!",
			),
		);
		$house['data']['buildjobs'][]=$newjob;
		improbablehousing_sethousedata($house);
	break;
	case "newroom-confirm":
		output("You've started a new build job to create a new room.  Have at it!`n`n");
		$newjob = array(
			"name"=>"New Room Construction (extension from ".$house['data']['rooms'][$rid]['name']."`0)",
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
					"req"=>70,
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
					"req"=>20,
					"done"=>0,
					"desc"=>"You set about chiseling and tinkering.  Before too long, the new room is one step closer to completion.",
				),
			),
			"completioneffects"=>array(
				"newrooms"=>array(
					0=>array(
						"name"=>"Extension",
						"size"=>1,
						"enterfrom"=>$rid,
						"desc"=>"You're standing in a small, undecorated extension room.",
						"sleepslots"=>array(
						),
					),
				),
				"msg"=>"The new room is now complete!",
			),
		);
		$house['data']['buildjobs'][]=$newjob;
		improbablehousing_sethousedata($house);
	break;
}

improbablehousing_bottomnavs($house,$rid);
page_footer();

?>