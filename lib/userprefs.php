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
	if ($user===false) {
		if(isset($session['user']['loggedin']) && $session['user']['loggedin']) $user = $session['user']['acctid'];
		else $user = 0;
	}
	//if ($session['user']['userprefs'][$name]){
	//	return $session['user']['userprefs'][$name];
	//}
	$sql="SELECT value FROM ".db_prefix("userprefs")." WHERE userid='$user' AND setting='$name'";
	$r=db_query($sql);
	$row=db_fetch_assoc($r);
	$return=$row['value'];
	//$session['user']['userprefs'][$name]=$return;
	//debug("$name is $return where user is $user");
	return $return;
}

function set_userpref($name,$value,$user=false){
	global $session;
	if ($user === false) $uid=$session['user']['acctid'];
	else $uid = $user;

	$sql="UPDATE ".db_prefix("userprefs")." SET value='".addslashes($value)."' WHERE setting='$name' AND userid='$uid'";
	$r=db_query($sql);
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
	global $module_prefs,$session;
	$value = (float)$value;
	if ($module === false) $module = $mostrecentmodule;
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
	
	/*$module_prefs[$uid][$module][$name] += $value;
	}else{
		$sql = "INSERT INTO " . db_prefix("module_userprefs"). " (modulename,setting,userid,value) VALUES ('$module','$name','$uid','".addslashes($value)."')";
		db_query($sql);
		$module_prefs[$uid][$module][$name] = $value;
	}*/
	return;
}

function clear_userpref($name,$user=false){
 	global $prefs,$session;
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
	
	return;
}

function load_userprefs($user=false){
	global $session;
	if ($user===false) $user = $session['user']['acctid'];


		$module_prefs[$user][$module] = array();
		$sql = "SELECT setting,value FROM " . db_prefix("userprefs") . " WHERE userid='$user'";
		$result = db_query($sql);
		$values=array();
		while ($row = db_fetch_assoc($result)){
			$values[$row['setting']]=$row['value'];
			//$module_prefs[$user][$module][$row['setting']] = $row['value'];
		}//end while
		return $values;
		
}//end function

?>
