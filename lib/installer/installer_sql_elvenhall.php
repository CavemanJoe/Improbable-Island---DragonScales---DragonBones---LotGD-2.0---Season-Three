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
	}
	//transfers stamina settings over
	if ($version=="2.1.0 Elvenhall Edition"){
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
	}
	//transfers stamina userprefs over
	if ($version=="2.1.3 Elvenhall Edition"){
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
	}//*/
	return true;
}

?>
