<?php
install_action("Fighting - Standard",array(
			"maxcost"=>2000,
			"mincost"=>500,
			"firstlvlexp"=>2000,
			"expincrement"=>1.1,
				"costreduction"=>15,
		"class"=>"Combat"
		));
		install_action("Running Away",array(
			"maxcost"=>1000,
			"mincost"=>200,
			"firstlvlexp"=>500,
			"expincrement"=>1.05,
			"costreduction"=>8,
			"class"=>"Combat"
		));
		//triggers when a player loses more than 10% of his total hitpoints in a single round
		install_action("Taking It on the Chin",array(
			"maxcost"=>2000,
			"mincost"=>200,
			"firstlvlexp"=>5000,
			"expincrement"=>1.1,
			"costreduction"=>15,
			"class"=>"Combat"
		));
			install_action("Hunting - Normal",array(
			"maxcost"=>25000,
			"mincost"=>10000,
			"firstlvlexp"=>1000,
			"expincrement"=>1.08,
			"costreduction"=>150,
			"class"=>"Hunting"
		));
		install_action("Hunting - Big Trouble",array(
			"maxcost"=>30000,
			"mincost"=>10000,
			"firstlvlexp"=>1000,
			"expincrement"=>1.08,
			"costreduction"=>200,
			"class"=>"Hunting"
		));
		install_action("Hunting - Easy Fights",array(
			"maxcost"=>20000,
			"mincost"=>10000,
				"firstlvlexp"=>1000,
			"expincrement"=>1.08,
			"costreduction"=>100,
			"class"=>"Hunting"
		));
		install_action("Hunting - Suicidal",array(
			"maxcost"=>35000,
			"mincost"=>10000,
			"firstlvlexp"=>1000,
			"expincrement"=>1.08,
			"costreduction"=>250,
			"class"=>"Hunting"
		));
?>
