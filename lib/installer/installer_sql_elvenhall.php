<?php
function elvenhall_sql($version){
	output("Version is %s", $version);
	//transfers userprefs for cities over.
	if ($version=="2.0.0 Elvenhall Edition"){		
		$sql="SELECT `userid`, `value` FROM ".db_prefix("module_userprefs")." WHERE modulename='cities' AND setting='homecity'";
		$result = db_query($sql);
		$rownums=db_num_rows($result);
		output("`nUpdating homecity userprefs...");
		$sql="INSERT INTO ".db_prefix("userprefs")." (`setting`, `userid`, `value`) VALUES ";
		$currentrow=0;
		while($row=db_fetch_assoc($result)){
			$currentrow++;
			$sql.="('homecity', {$row['userid']}, '{$row['value']}')";
			if ($currentrow!=$rownums){
				$sql.=",";	
			}
		}

		$result = db_query($sql);
		return;
	} else 	if ($version=="2.1.0 Elvenhall Edition"){//transfers stamina settings over
		output("`nUpdating Stamina actionsarray settings...");
		$sql="SELECT value FROM ".db_prefix("module_settings")." WHERE modulename='staminasystem' AND setting='actionsarray'";
		$result=db_query($sql);
		$array=db_fetch_assoc($result);
		$array=serialize(unserialize($array['value']));
		$sql="INSERT INTO ".db_prefix("settings")." VALUES ('stamina_actionsarray', '$array')";
		$result=db_query($sql);

		output("Updating Stamina turns_emulation_base settings...");
		$sql="SELECT value FROM ".db_prefix("module_settings")." WHERE modulename='staminasystem' AND setting='turns_emulation_base'";
		$result=db_query($sql);
		$array=db_fetch_assoc($result);
		$array=$array['value'];
		$sql="INSERT INTO ".db_prefix("settings")." VALUES ('stamina_turns_base', '$array')";
		$result=db_query($sql);

		output("Updating Stamina turns_emulation_ceiling settings...");
		$sql="SELECT value FROM ".db_prefix("module_settings")." WHERE modulename='staminasystem' AND setting='turns_emulation_ceiling'";
		$result=db_query($sql);
		$array=db_fetch_assoc($result);
		$array=$array['value'];
		$sql="INSERT INTO ".db_prefix("settings")." VALUES ('stamina_turns_ceilin', '$array')";
		$result=db_query($sql);
		return;
	} else 	if ($version=="2.1.3 Elvenhall Edition"){	//transfers stamina userprefs over
		//stamina actions
		output("`nUpdating stamina actions userprefs...");
		$sql="SELECT `userid`, `value` FROM ".db_prefix("module_userprefs")." WHERE modulename='staminasystem' AND setting='actions'";
		$result = db_query($sql);

		$rownums=db_num_rows($result);

		$sql="INSERT INTO ".db_prefix("userprefs")." (`setting`, `userid`, `value`) VALUES ";
		$currentrow=0;
		while($row=db_fetch_assoc($result)){
			$currentrow++;
			$sql.="('stamina_actions', {$row['userid']}, '{$row['value']}')";
			if ($currentrow!=$rownums){
				$sql.=",";	
			}
		}
		$result = db_query($sql);

		//stamina amber
		output("`nUpdating stamina amber userprefs...");
		$sql="SELECT `userid`, `value` FROM ".db_prefix("module_userprefs")." WHERE modulename='staminasystem' AND setting='amber'";
		$result = db_query($sql);

		$rownums=db_num_rows($result);

		$sql="INSERT INTO ".db_prefix("userprefs")." (`setting`, `userid`, `value`) VALUES ";
		$currentrow=0;
		while($row=db_fetch_assoc($result)){
			$currentrow++;
			$sql.="('stamina_amber', {$row['userid']}, '{$row['value']}')";
			if ($currentrow!=$rownums){
				$sql.=",";	
			}
		}
		$result = db_query($sql);

		//stamina buffs
		output("`nUpdating stamina amber userprefs...");
		$sql="SELECT `userid`, `value` FROM ".db_prefix("module_userprefs")." WHERE modulename='staminasystem' AND setting='buffs'";
		$result = db_query($sql);

		$rownums=db_num_rows($result);

		$sql="INSERT INTO ".db_prefix("userprefs")." (`setting`, `userid`, `value`) VALUES ";
		$currentrow=0;
		while($row=db_fetch_assoc($result)){
			$currentrow++;
			$sql.="('stamina_buffs', {$row['userid']}, '{$row['value']}')";
			if ($currentrow!=$rownums){
				$sql.=",";	
			}
		}
		$result = db_query($sql);

		//stamina red
		output("`nUpdating stamina red userprefs...");
		$sql="SELECT `userid`, `value` FROM ".db_prefix("module_userprefs")." WHERE modulename='staminasystem' AND setting='red'";
		$result = db_query($sql);

		$rownums=db_num_rows($result);

		$sql="INSERT INTO ".db_prefix("userprefs")." (`setting`, `userid`, `value`) VALUES ";
		$currentrow=0;
		while($row=db_fetch_assoc($result)){
			$currentrow++;
			$sql.="('stamina_red', {$row['userid']}, '{$row['value']}')";
			if ($currentrow!=$rownums){
				$sql.=",";	
			}
		}
		$result = db_query($sql);

		//stamina amount
		output("`nUpdating stamina amount userprefs...");
		$sql="SELECT `userid`, `value` FROM ".db_prefix("module_userprefs")." WHERE modulename='staminasystem' AND setting='stamina'";
		$result = db_query($sql);

		$rownums=db_num_rows($result);

		$sql="INSERT INTO ".db_prefix("userprefs")." (`setting`, `userid`, `value`) VALUES ";
		$currentrow=0;
		while($row=db_fetch_assoc($result)){
			$currentrow++;
			$sql.="('stamina_amount', {$row['userid']}, '{$row['value']}')";
			if ($currentrow!=$rownums){
				$sql.=",";	
			}
		}
		$result = db_query($sql);

		//stamina minihof
		output("`nUpdating stamina minihof userprefs...");
		$sql="SELECT `userid`, `value` FROM ".db_prefix("module_userprefs")." WHERE modulename='staminasystem' AND setting='user_minihof'";
		$result = db_query($sql);

		$rownums=db_num_rows($result);

		$sql="INSERT INTO ".db_prefix("userprefs")." (`setting`, `userid`, `value`) VALUES ";
		$currentrow=0;
		while($row=db_fetch_assoc($result)){
			$currentrow++;
			$sql.="('stamina_minihof', {$row['userid']}, '{$row['value']}')";
			if ($currentrow!=$rownums){
				$sql.=",";	
			}
		}
		$result = db_query($sql);
	} else if ($version=="2.2.0 Elvenhall Edition"){
		require_once("lib/stamina/stamina.php");
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
	}
	
	return true;
}

?>
