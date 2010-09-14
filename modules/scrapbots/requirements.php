<?php

/*
=======================================================
REQUIREMENT ARRAYS
Arrays of Scrap items required to create and update ScrapBots.
=======================================================
*/

function scrapbots_requirements(){
	$requirements = array (
		"scrapbot" => array (
			"rareitems" => array (
				0 => 2,
				3 => 1,
				4 => 1,
				5 => 1,
				6 => 1,
				7 => 4,
				12 => 1
			),
			"veryrareitems" => array(
			0 => 1,
			1 => 1,
			2 => 1,
			3 => 1
			),
			"actions" => array(
				"Metalworking",
				"Soldering",
				"Programming"
			),
		),
		"brains" => array (
			2 => array (
				"rareitems" => array (
					4 => 1,
				),
				"skills" => array (
					"Soldering",
					"Programming",
				),
			),
			3 => array (
				"rareitems" => array (
					4 => 1,
				),
				"skills" => array (
					"Soldering",
					"Programming",
				),
			),
			4 => array (
				"rareitems" => array (
					4 => 1,
				),
				"skills" => array (
					"Soldering",
					"Programming",
				),
			),
			5 => array (
				"rareitems" => array (
					4 => 1,
				),
				"skills" => array (
					"Soldering",
					"Programming",
				),
			),
			6 => array (
				"rareitems" => array (
					4 => 2,
				),
				"skills" => array (
					"Soldering",
					"Programming",
				),
			),
			7 => array (
				"rareitems" => array (
					4 => 2,
				),
				"skills" => array (
					"Soldering",
					"Programming",
				),
			),
			8 => array (
				"rareitems" => array (
					4 => 2,
				),
				"skills" => array (
					"Soldering",
					"Programming",
				),
			),
			9 => array (
				"rareitems" => array (
					4 => 2,
				),
				"skills" => array (
					"Soldering",
					"Programming",
				),
			),
			10 => array (
				"rareitems" => array (
					4 => 3,
				),
				"skills" => array (
					"Soldering",
					"Programming",
				),
			),
		),
		"brawn" => array (
			2 => array (
				"veryrareitems" => array (
					0 => 1,
				),
				"skills" => array (
					"Metalworking",
				),
			),
			3 => array (
				"veryrareitems" => array (
					0 => 1,
					2 => 1,
				),
				"skills" => array (
					"Metalworking",
				),
			),
			4 => array (
				"veryrareitems" => array (
					0 => 1,
					2 => 1,
				),
				"skills" => array (
					"Metalworking",
				),
			),
			5 => array (
				"veryrareitems" => array (
					0 => 2,
				),
				"rareitems" => array (
					6 => 1,
				}
				"skills" => array (
					"Metalworking",
					"Soldering",
				),
			),
			6 => array (
				"veryrareitems" => array (
					0 => 3,
				),
				"skills" => array (
					"Metalworking",
				),
			),
			7 => array (
				"veryrareitems" => array (
					0 => 1,
					2 => 1,
				),
				"rareitems" => array (
					6 => 1,
				}
				"skills" => array (
					"Metalworking",
					"Soldering",
				),
			),
			8 => array (
				"veryrareitems" => array (
					0 => 2,
					2 => 1,
				),
				"rareitems" => array (
					6 => 1,
				}
				"skills" => array (
					"Metalworking",
					"Soldering",
				),
			),
			9 => array (
				"veryrareitems" => array (
					0 => 4,
				),
				"skills" => array (
					"Metalworking",
				),
			),
			10 => array (
				"veryrareitems" => array (
					0 => 4,
					2 => 1,
				),
				"rareitems" => array (
					6 => 1,
				),
				"skills" => array (
					"Metalworking",
					"Soldering",
					"Programming",
				),
			),
		),
		"briskness" => array (
			2 => array (
				"rareitems" => array (
					6 => 1,
				),
				"skills" => array (
					"Soldering",
				),
			),
			3 => array (
				"rareitems" => array (
					6 => 1,
				),
				"skills" => array (
					"Soldering",
				),
			),
			4 => array (
				"rareitems" => array (
					6 => 1,
					12 => 1,
				),
				"skills" => array (
					"Soldering",
					"Metalworking",
				),
			),
			5 => array (
				"rareitems" => array (
					6 => 1,
					12 => 1,
				),
				"skills" => array (
					"Soldering",
					"Metalworking",
				),
			),
			6 => array (
				"rareitems" => array (
					6 => 1,
					12 => 1,
				),
				"skills" => array (
					"Soldering",
					"Metalworking",
				),
			),
			7 => array (
				"rareitems" => array (
					6 => 2,
					12 => 1,
				),
				"skills" => array (
					"Soldering",
					"Metalworking",
				),
			),
			8 => array (
				"rareitems" => array (
					6 => 2,
					12 => 1,
				),
				"skills" => array (
					"Soldering",
					"Metalworking",
				),
			),
			9 => array (
				"rareitems" => array (
					6 => 2,
					12 => 2,
				),
				"skills" => array (
					"Soldering",
					"Metalworking",
				),
			),
			10 => array (
				"rareitems" => array (
					6 => 2,
					12 => 2,
				),
				"skills" => array (
					"Soldering",
					"Metalworking",
				),
			),
		),
	);
	return($requirements);
}
?>