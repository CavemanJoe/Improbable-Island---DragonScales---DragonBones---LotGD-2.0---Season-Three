<?php
function elvenhall_sql($version){
	//transfers userprefs for cities over.
	if ($version=="1.2.1.1 Elvenhall Edition"){
		$sql="SELECT `userid`, `value` FROM ".db_prefix("module_userprefs")." WHERE modulename='setting' AND setting='homecity'";
		$result = db_query($sql);
		$rownums=db_num_rows($result);

		$sql="INSERT INTO ".db_prefix("userprefs")." (, `setting`, `userid`, `value`) VALUES ";
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

		$sql="SELECT `value` FROM ".db_prefix("module_settings")." WHERE modulename='staminasystem' AND setting='actionsarray'";
		$result=db_query($sql);
		$array=db_fetch_array($result);
		$sql="INSERT INTO ".db_prefix("settings")." (`setting`,`value`) VALUES ('staminasystem_actionsarray, $array)";

		$sql="SELECT `value` FROM ".db_prefix("module_settings")." WHERE modulename='staminasystem' AND setting='turns_emulation_base'";
		$result=db_query($sql);
		$array=db_fetch_array($result);
		$sql="INSERT INTO ".db_prefix("settings")." (`setting`,`value`) VALUES ('staminasystem_turns_emulation_base, $array)";

		$sql="SELECT `value` FROM ".db_prefix("module_settings")." WHERE modulename='staminasystem' AND setting='turns_emulation_ceiling'";
		$result=db_query($sql);
		$array=db_fetch_array($result);
		$sql="INSERT INTO ".db_prefix("settings")." (`setting`,`value`) VALUES ('staminasystem_turns_emulation_ceiling, $array)";
		return;
	}
	//transfers stamina userprefs over
	/*if ($version=="1.2.1.3 Elvenhalls Edition"){
		$sql="SELECT `userid`, `value` FROM ".db_prefix("module_userprefs")." WHERE modulename='setting' AND setting='homecity'";
		$result = db_query($sql);

		$rownums=db_num_rows($result);

		$sql="INSERT INTO ".db_prefix("userprefs")." (`modulename`, `setting`, `userid`, `value`) VALUES ";
		$currentrow=0;
		while($row=db_fetch_array($result)){
			$currentrow++;
			$sql.="('ztestmovement', 'user_extendedbio', {$row['userid']}, '{$row['value']}')";
			if ($currentrow!=$rownums){
				$sql.=",";	
			}
		}

		$result = db_query($sql);
	}*/
	return true;
}

?>
