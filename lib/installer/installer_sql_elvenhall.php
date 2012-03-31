<?php
function elvenhall_sql($version){
	//transfers userprefs for cities over.
	if ($version=="1.2.1.1 Elvenhall Edition"){
		$sql="SELECT `userid`, `value` FROM ".db_prefix("module_userprefs")." WHERE modulename='cities' AND setting='homecity'";
		$result = db_query($sql);
		$rownums=db_num_rows($result);
		output("Updating homecity userprefs...");
		$sql="INSERT INTO ".db_prefix("userprefs")." (`setting`, `userid`, `value`) VALUES ";
		$currentrow=0;
		while($row=db_fetch_array($result)){
			$currentrow++;
			$sql.="('homecity', {$row['userid']}, '{$row['value']}')";
			if ($currentrow!=$rownums){
				$sql.=",";	
			}
		}

		$result = db_query($sql);
		return;
	}
	//transfers stamina settings over
	if ($version=="1.2.1.2 Elvenhall Edition"){
		output("Updating Stamina actionsarray settings...");
		$sql="SELECT `value` FROM ".db_prefix("module_settings")." WHERE modulename='staminasystem' AND setting='actionsarray'";
		$result=db_query($sql);
		$array=db_fetch_array($result);
		$sql="INSERT INTO ".db_prefix("settings")." (`setting`,`value`) VALUES ('staminasystem_actionsarray, $array)";

		output("Updating Stamina turns_emulation_base settings...");
		$sql="SELECT `value` FROM ".db_prefix("module_settings")." WHERE modulename='staminasystem' AND setting='turns_emulation_base'";
		$result=db_query($sql);
		$array=db_fetch_array($result);
		$sql="INSERT INTO ".db_prefix("settings")." (`setting`,`value`) VALUES ('staminasystem_turns_emulation_base, $array)";

		output("Updating Stamina turns_emulation_ceiling settings...");
		$sql="SELECT `value` FROM ".db_prefix("module_settings")." WHERE modulename='staminasystem' AND setting='turns_emulation_ceiling'";
		$result=db_query($sql);
		$array=db_fetch_array($result);
		$sql="INSERT INTO ".db_prefix("settings")." (`setting`,`value`) VALUES ('staminasystem_turns_emulation_ceiling, $array)";
		return;
	}
	//transfers stamina userprefs over
	if ($version=="1.2.1.3 Elvenhalls Edition"){
		//stamina actions
		output("Updating stamina actions userprefs...");
		$sql="SELECT `userid`, `value` FROM ".db_prefix("module_userprefs")." WHERE modulename='staminasystem' AND setting='actions'";
		$result = db_query($sql);

		$rownums=db_num_rows($result);

		$sql="INSERT INTO ".db_prefix("userprefs")." (`setting`, `userid`, `value`) VALUES ";
		$currentrow=0;
		while($row=db_fetch_array($result)){
			$currentrow++;
			$sql.="('stamina_actions', {$row['userid']}, '{$row['value']}')";
			if ($currentrow!=$rownums){
				$sql.=",";	
			}
		}
		$result = db_query($sql);

		//stamina amber
		output("Updating stamina amber userprefs...");
		$sql="SELECT `userid`, `value` FROM ".db_prefix("module_userprefs")." WHERE modulename='staminasystem' AND setting='amber'";
		$result = db_query($sql);

		$rownums=db_num_rows($result);

		$sql="INSERT INTO ".db_prefix("userprefs")." (`setting`, `userid`, `value`) VALUES ";
		$currentrow=0;
		while($row=db_fetch_array($result)){
			$currentrow++;
			$sql.="('stamina_amber', {$row['userid']}, '{$row['value']}')";
			if ($currentrow!=$rownums){
				$sql.=",";	
			}
		}
		$result = db_query($sql);

		//stamina buffs
		output("Updating stamina amber userprefs...");
		$sql="SELECT `userid`, `value` FROM ".db_prefix("module_userprefs")." WHERE modulename='staminasystem' AND setting='buffs'";
		$result = db_query($sql);

		$rownums=db_num_rows($result);

		$sql="INSERT INTO ".db_prefix("userprefs")." (`setting`, `userid`, `value`) VALUES ";
		$currentrow=0;
		while($row=db_fetch_array($result)){
			$currentrow++;
			$sql.="('stamina_buffs', {$row['userid']}, '{$row['value']}')";
			if ($currentrow!=$rownums){
				$sql.=",";	
			}
		}
		$result = db_query($sql);

		//stamina red
		output("Updating stamina red userprefs...");
		$sql="SELECT `userid`, `value` FROM ".db_prefix("module_userprefs")." WHERE modulename='staminasystem' AND setting='red'";
		$result = db_query($sql);

		$rownums=db_num_rows($result);

		$sql="INSERT INTO ".db_prefix("userprefs")." (`setting`, `userid`, `value`) VALUES ";
		$currentrow=0;
		while($row=db_fetch_array($result)){
			$currentrow++;
			$sql.="('stamina_red', {$row['userid']}, '{$row['value']}')";
			if ($currentrow!=$rownums){
				$sql.=",";	
			}
		}
		$result = db_query($sql);

		//stamina amount
		output("Updating stamina amount userprefs...");
		$sql="SELECT `userid`, `value` FROM ".db_prefix("module_userprefs")." WHERE modulename='staminasystem' AND setting='stamina'";
		$result = db_query($sql);

		$rownums=db_num_rows($result);

		$sql="INSERT INTO ".db_prefix("userprefs")." (`setting`, `userid`, `value`) VALUES ";
		$currentrow=0;
		while($row=db_fetch_array($result)){
			$currentrow++;
			$sql.="('stamina_amount', {$row['userid']}, '{$row['value']}')";
			if ($currentrow!=$rownums){
				$sql.=",";	
			}
		}
		$result = db_query($sql);

		//stamina minihof
		output("Updating stamina minihof userprefs...");
		$sql="SELECT `userid`, `value` FROM ".db_prefix("module_userprefs")." WHERE modulename='staminasystem' AND setting='user_minihof'";
		$result = db_query($sql);

		$rownums=db_num_rows($result);

		$sql="INSERT INTO ".db_prefix("userprefs")." (`setting`, `userid`, `value`) VALUES ";
		$currentrow=0;
		while($row=db_fetch_array($result)){
			$currentrow++;
			$sql.="('stamina_minihof', {$row['userid']}, '{$row['value']}')";
			if ($currentrow!=$rownums){
				$sql.=",";	
			}
		}
		$result = db_query($sql);
	}//*/
	return true;
}

?>
