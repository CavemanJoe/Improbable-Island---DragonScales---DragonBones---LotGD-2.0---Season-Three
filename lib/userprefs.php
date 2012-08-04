<?php

function delete_userprefs($user){
	$sql = "DELETE FROM " . db_prefix("userprefs") . " WHERE userid='$user'";
	db_query($sql);
}

function get_all_userprefs($name){
	$sql="SELECT value FROM ".db_prefix("userprefs")." WHERE setting='$name'";
	$result = db_query($sql);
	$values=array();
	while ($row = db_fetch_assoc($result)){
		$values[$row['setting']]=$row['value'];
		//$module_prefs[$user][$module][$row['setting']] = $row['value'];
	}//end while
	return $values;
}

function get_userpref($name,$user=false){
	global $session;
	//global 
	$userprefs;
	if ($user===false) {
		if(isset($session['user']['loggedin']) && $session['user']['loggedin']) $user = $session['user']['acctid'];
		else $user = 0;
	}
	//if ($userprefs[$user][$name]){
	//	return $userprefs[$user][$name];
	//}
	$sql="SELECT value FROM ".db_prefix("userprefs")." WHERE userid='$user' AND setting='$name'";
	$r=db_query($sql);
	$row=db_fetch_assoc($r);
	$return=$row['value'];
	$userprefs[$user][$name]=$row['value'];
	//debug("$name is $return where user is $user");
	return $return;
}

function set_userpref($name,$value,$user=false){
	global $session, $userprefs;
	if ($user === false){ $uid=$session['user']['acctid'];}
	else {$uid = $user;}
	
	$sql="UPDATE ".db_prefix("userprefs")." SET value='".addslashes($value)."' WHERE setting='$name' AND userid='$uid'";
	//output($sql);
	$r=db_query($sql);
	$userprefs[$user][$name]=$value;	
	/*load_module_prefs($module, $uid);
	
	//don't write to the DB if the user isn't logged in.
	if (!$session['user']['loggedin'] && !$user) {
		// We do need to save to the loaded copy here however
		$module_prefs[$uid][$module][$name] = $value;
		return;
	}

	if (isset($module_prefs[$uid][$module][$name])){
		$sql = "UPDATE " . db_prefix("module_userprefs") . " SET value='".addslashes($value)."' WHERE modulename='$module' AND setting='$name' AND userid='$uid'";
		db_query($sql);
	}else{
		$sql = "INSERT INTO " . db_prefix("module_userprefs"). " (modulename,setting,userid,value) VALUES ('$module','$name','$uid','".addslashes($value)."')";
		db_query($sql);
	}
	$module_prefs[$uid][$module][$name] = $value;*/
}

function increment_userpref($name,$value=1,$user=false){
	global $userprefs,$session;
	$value = (float)$value;
	if ($user === false) $uid=$session['user']['acctid'];
	else $uid = $user;
	
	/*
	//don't write to the DB if the user isn't logged in.
	if (!$session['user']['loggedin'] && !$user) {
		// We do need to save to the loaded copy here however
		$module_prefs[$uid][$module][$name] += $value;
		return;
	}
	*/

	
	$sql = "UPDATE " . db_prefix("module_userprefs") . " SET value=value+$value WHERE modulename='$module' AND setting='$name' AND userid='$uid'";
	db_query($sql);
	$userprefs[$user][$name]+=$value;
	/*$module_prefs[$uid][$module][$name] += $value;
	}else{
		$sql = "INSERT INTO " . db_prefix("module_userprefs"). " (modulename,setting,userid,value) VALUES ('$module','$name','$uid','".addslashes($value)."')";
		db_query($sql);
		$module_prefs[$uid][$module][$name] = $value;
	}*/
	return;
}

function clear_userpref($name,$user=false){
 	global $userprefs,$session;
	if ($user === false) $uid=$session['user']['acctid'];
	else $uid = $user;
	/*
	//don't write to the DB if the user isn't logged in.
	if (!$session['user']['loggedin'] && !$user) {
		// We do need to trash the loaded copy here however
		unset($prefs[$uid][$module][$name]);
		return;
	}
	*/
	
	$sql = "DELETE FROM " . db_prefix("userprefs") . " WHERE setting='$name' AND userid='$uid'";
	db_query($sql);
	unset($userprefs[$uid]);
	return;
}

function load_userprefs($user=false){
	global $session,$userprefs;
	if ($user===false) $user = $session['user']['acctid'];
		
		$module_prefs[$user][$module] = array();
		$sql = "SELECT setting,value FROM " . db_prefix("userprefs") . " WHERE userid='$user'";
		$result = db_query($sql);
		$values=array();
		while ($row = db_fetch_assoc($result)){
			$values[$row['setting']]=$row['value'];
			//$module_prefs[$user][$module][$row['setting']] = $row['value'];
		}//end while
		$userprefs[$user]=$values;
		return $values;
		
}//end function

function createchar_userprefsn($name){
$sql = "SELECT acctid FROM " . db_prefix("accounts") . " WHERE login='$name'";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			$args = httpallpost();
			$i = $row['acctid'];
			$stamina=0;
			$red=200000;
			$amber=400000;
			$array=array();
			$actions=serialize(unserialize(getsetting("stamina_actionsarray", "")));
			$buffs=serialize($array);
			$user_minhof=true;
			$sql="INSERT IGNORE INTO ".db_prefix("userprefs")." (`setting`, `userid`, `value`) VALUES ('stamina_amount', $i, '$stamina')";
			$result=db_query($sql);
			$return[1][0]=$result;
			$sql="INSERT IGNORE INTO ".db_prefix("userprefs")." (`setting`, `userid`, `value`) VALUES ('stamina_red', $i, '$red')";
			$result=db_query($sql);
			$return[1][1]=$result;
			$sql="INSERT IGNORE INTO ".db_prefix("userprefs")." (`setting`, `userid`, `value`) VALUES ('stamina_amber', $i, '$amber')";
			$result=db_query($sql);
			$return[1][2]=$result;
			$sql="INSERT IGNORE INTO ".db_prefix("userprefs")." (`setting`, `userid`, `value`) VALUES ('stamina_actions', $i, '$actions')";
			$result=db_query($sql);
			$return[1][3]=$result;
			$sql="INSERT IGNORE INTO ".db_prefix("userprefs")." (`setting`, `userid`, `value`) VALUES ('stamina_buffs', $i, '$buffs')";
			$result=db_query($sql);
			$return[1][4]=$result;
			$sql="INSERT IGNORE INTO ".db_prefix("userprefs")." (`setting`, `userid`, `value`) VALUES ('stamina_minihof', $i, '$user_minihof')";
			$result=db_query($sql);
			$return[1][5]=$result;
}

function createchar_userprefsid($uid){
			$stamina=0;
			$red=200000;
			$amber=400000;
			$array=array();
			$actions=serialize($array);
			$buffs=serialize($array);
			$user_minhof=true;
			$sql="INSERT IGNORE INTO ".db_prefix("userprefs")." (`setting`, `userid`, `value`) VALUES ('stamina_amount', $uid, '$stamina')";
			$result=db_query($sql);
			$return[1][0]=$result;
			$sql="INSERT IGNORE INTO ".db_prefix("userprefs")." (`setting`, `userid`, `value`) VALUES ('stamina_red', $uid, '$red')";
			$result=db_query($sql);
			$return[1][1]=$result;
			$sql="INSERT IGNORE INTO ".db_prefix("userprefs")." (`setting`, `userid`, `value`) VALUES ('stamina_amber', $uid, '$amber')";
			$result=db_query($sql);
			$return[1][2]=$result;
			$sql="INSERT IGNORE INTO ".db_prefix("userprefs")." (`setting`, `userid`, `value`) VALUES ('stamina_actions', $uid, '$actions')";
			$result=db_query($sql);
			$return[1][3]=$result;
			$sql="INSERT IGNORE INTO ".db_prefix("userprefs")." (`setting`, `userid`, `value`) VALUES ('stamina_buffs', $uid, '$buffs')";
			$result=db_query($sql);
			$return[1][4]=$result;
			$sql="INSERT IGNORE INTO ".db_prefix("userprefs")." (`setting`, `userid`, `value`) VALUES ('stamina_minihof', $uid, '$user_minihof')";
			$result=db_query($sql);
			$return[1][5]=$result;
}
?>
