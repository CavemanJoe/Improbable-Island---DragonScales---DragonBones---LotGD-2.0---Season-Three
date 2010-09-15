<?php

global $session;
page_header("Rebooting Dwelling");

require_once "modules/improbablehousing/lib/lib.php";
$hid = httpget('hid');
$house = improbablehousing_gethousedata($hid);
	
$house['data'] = array(
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

improbablehousing_sethousedata($house);
invalidatedatacache("housing/housing_location_".$loc);
$loc = get_module_pref("worldXYZ","worldmapen");

output("Dwelling rebooted.  Dwelling reconstructor item granted.");
give_item("dwelling_reconstructor");
	
addnav("Exit");
addnav("Back to the World Map","runmodule.php?module=worldmapen&op=continue");
page_footer();

?>