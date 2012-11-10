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
		$return[0]=$result;
		return $return;
	} else if ($version=="2.1.0 Elvenhall Edition"){//transfers stamina settings over
		if (is_module_installed("staminasystem")==true){
		/*output("`nUpdating Stamina actionsarray settings...");
		$sql="SELECT value FROM ".db_prefix("module_settings")." WHERE modulename='staminasystem' AND setting='actionsarray'";
		$result=db_query($sql);
		$array=db_fetch_assoc($result);
		$array=serialize(unserialize($array['value']));
		$sql="INSERT INTO ".db_prefix("settings")." VALUES ('stamina_actionsarray', '$array')";
		$result=db_query($sql);
		$return[0]=$result;*/

		output("Updating Stamina turns_emulation_base settings...");
		$sql="SELECT value FROM ".db_prefix("module_settings")." WHERE modulename='staminasystem' AND setting='turns_emulation_base'";
		$result=db_query($sql);
		$array=db_fetch_assoc($result);
		$array=$array['value'];
		$sql="INSERT INTO ".db_prefix("settings")." VALUES ('stamina_turns_base', '$array')";
		$result=db_query($sql);
		$return[1]=$result;

		output("Updating Stamina turns_emulation_ceiling settings...");
		$sql="SELECT value FROM ".db_prefix("module_settings")." WHERE modulename='staminasystem' AND setting='turns_emulation_ceiling'";
		$result=db_query($sql);
		$array=db_fetch_assoc($result);
		$array=$array['value'];
		$sql="INSERT INTO ".db_prefix("settings")." VALUES ('stamina_turns_ceilin', '$array')";
		$result=db_query($sql);
		$return[2]=$result;
		return $return;
		}
	} else if ($version=="2.1.3 Elvenhall Edition"){
		return;
	/*} else if ($version=="2.2.0 Elvenhall Edition"){
		return
	} else if ($version=="2.2.1 Elvenhall Edition"){//set default stamina settings, if staminasystem wasn't already installed
		$return=false;	
		//require_once("lib/stamina/stamina.php");
		//require_once("lib/stamina/defaultactions.php")
//There is an error somewhere in this block, can't tell what but I'm going to bed.
		/*$value=20000;
		$sql="INSERT IGNORE INTO ".db_prefix("settings")." VALUES ('stamina_turns_base', '$value')";
		$result=db_query($sql);
		$return[0]=$result;

		$value=30000;
		$sql="INSERT IGNORE INTO ".db_prefix("settings")." VALUES ('stamina_turns_ceilin', '$value')";
		$result=db_query($sql);
		$return[1]=$result;

		output("Settings for turns emulation has been set, if they weren't already.");*/
		
	} else if ($version=="2.2.0 Elvenhall Edition"){
		
		if ($session['dbinfo']['upgrade']==true && is_module_installed("staminasystem"==true)){
			//takes values from old module userprefs
			output("`nSelecting userprefs...");
			$sql="SELECT userid, value FROM ".db_prefix("module_userprefs")." WHERE modulename='staminasystem AND setting='stamina'";
			$result=db_query($sql);
			while ($row=db_fetch_assoc($result)){
				$uid=$row['userid'];
				$stamuserprefs[$uid]['stamina']=$row['value'];
			}
			
			$sql="SELECT userid, value FROM ".db_prefix("module_userprefs")." WHERE modulename='staminasystem AND setting='red'";
			$result=db_query($sql);
			while ($row=db_fetch_assoc($result)){
				$uid=$row['userid'];
				$stamuserprefs[$uid]['red']=$row['value'];
			}

			$sql="SELECT userid, value FROM ".db_prefix("module_userprefs")." WHERE modulename='staminasystem AND setting='amber'";
			$result=db_query($sql);
			while ($row=db_fetch_assoc($result)){
				$uid=$row['userid'];
				$stamuserprefs[$uid]['amber']=$row['value'];
			}

			$sql="SELECT userid, value FROM ".db_prefix("module_userprefs")." WHERE modulename='staminasystem AND setting='actions'";
			$result=db_query($sql);
			while ($row=db_fetch_assoc($result)){
				$uid=$row['userid'];
				$stamuserprefs[$uid]['actions']=$row['value'];
			}

			$sql="SELECT userid, value FROM ".db_prefix("module_userprefs")." WHERE modulename='staminasystem AND setting='buffs'";
			$result=db_query($sql);
			while ($row=db_fetch_assoc($result)){
				$uid=$row['userid'];
				$stamuserprefs[$uid]['buffs']=$row['value'];
			}

			$sql="SELECT userid, value FROM ".db_prefix("module_userprefs")." WHERE modulename='staminasystem AND setting='user_minihof'";
			$result=db_query($sql);
			while ($row=db_fetch_assoc($result)){
				$uid=$row['userid'];
				$stamuserprefs[$uid]['minihof']=$row['value'];
			}
			$noofusers=count($stamuserprefs);
			output("`nInserting %s userprefs...",$noofusers);
			foreach ($stamuserprefs as $userid=>$userprefs){
				$sql="UPDATE ".db_prefix("accounts")." SET stamina_amount='{$userprefs['stamina']}, stamina_red='{$userprefs['red']}', stamina_amber='{$userprefs['amber']}', stamina_actions={$userprefs['actions']}, stamina_buffs={$userprefs['buffs']}, stamina_minihof={$userprefs['minihof']} WHERE acctid=$userid";
				db_query($sql);
			}//finished with userprefs

			//start putting stamina actions across
			output("`nUpdating Stamina actionsarray settings...");
			$sql="SELECT value FROM ".db_prefix("module_settings")." WHERE modulename='staminasystem' AND setting='actionsarray'";
			$result=db_query($sql);
			$array=db_fetch_assoc($result);
			$array=serialize(unserialize($array['value']));
			$sql="INSERT INTO ".db_prefix("staminaactionsarray")." VALUES ('$array')";
			$result=db_query($sql);
			//end settings

		} else {//if the staminasystem was not already present
			//count no. of accounts
			$sql="SELECT COUNT(*) AS cnt FROM ".db_prefix("accounts");
			$result=db_query($sql);		
			$row=db_fetch_assoc($result);
			$accounts=$row['cnt'];
			
			$array=array();
			$sarray=serialize($array);

			for ($i=1; $i<=$accounts;$i++){
				$sql="UPDATE ".db_prefix("accounts")." SET stamina_amount='0', stamina_red='200000', stamina_amber='400000', stamina_actions=$sarray, stamina_buffs=$sarray, stamina_minihof='0' WHERE acctid=$i";
				db_query($sql);
			}
		}

	} else if ($version=="2.2.1 Elvenhall Edition"){
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
		return true;

	}
}

?>
