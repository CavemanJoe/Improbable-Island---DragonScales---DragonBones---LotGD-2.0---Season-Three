<?php
// translator ready
// addnews ready
// mail ready

require_once("lib/arraytourl.php");

$module_settings = array();
function get_all_module_settings($module=false){
	//returns an associative array of all the settings for the given module
	global $module_settings,$mostrecentmodule;
	if ($module === false) $module = $mostrecentmodule;

	load_module_settings($module);
	return $module_settings[$module];
}

function get_module_setting($name,$module=false){
	global $module_settings,$mostrecentmodule;
	if ($module === false) $module = $mostrecentmodule;

	load_module_settings($module);
	if (isset($module_settings[$module][$name])) {
		return $module_settings[$module][$name];
	}else{
		$info = get_module_info($module);
		if (isset($info['settings'][$name])){
			if (is_array($info['settings'][$name])) {
				$v = $info['settings'][$name][0];
				$x = explode("|", $v);
			} else {
				$x = explode("|",$info['settings'][$name]);
			}
			if (isset($x[1])){
				return $x[1];
			}
		}
		return NULL;
	}
}

function set_module_setting($name,$value,$module=false){
	global $module_settings,$mostrecentmodule;
	if ($module === false) $module = $mostrecentmodule;
	load_module_settings($module);
	if (isset($module_settings[$module][$name])){
		$sql = "UPDATE " . db_prefix("module_settings") . " SET value='".addslashes($value)."' WHERE modulename='$module' AND setting='".addslashes($name)."'";
		db_query($sql);
	}else{
		$sql = "INSERT INTO " . db_prefix("module_settings") . " (modulename,setting,value) VALUES ('$module','".addslashes($name)."','".addslashes($value)."')";
		db_query($sql);
	}
	invalidatedatacache("modulesettings-$module");
	$module_settings[$module][$name] = $value;
}

function increment_module_setting($name, $value=1, $module=false){
	global $module_settings,$mostrecentmodule;
	$value = (float)$value;
	if ($module === false) $module = $mostrecentmodule;
	load_module_settings($module);
	if (isset($module_settings[$module][$name])){
		$sql = "UPDATE " . db_prefix("module_settings") . " SET value=value+$value WHERE modulename='$module' AND setting='".addslashes($name)."'";
		db_query($sql);
	}else{
		$sql = "INSERT INTO " . db_prefix("module_settings") . " (modulename,setting,value) VALUES ('$module','".addslashes($name)."','".addslashes($value)."')";
		db_query($sql);
	}
	invalidatedatacache("modulesettings-$module");
	$module_settings[$module][$name] += $value;
}

function clear_module_settings($module=false){
	global $module_settings,$mostrecentmodule;
	if ($module === false) $module = $mostrecentmodule;
	if (isset($module_settings[$module])){
		debug("Deleted module settings cache for $module.");
		unset($module_settings[$module]);
		invalidatedatacache("modulesettings-$module");
	}
}

function load_module_settings($module){
	global $module_settings;
	if (!isset($module_settings[$module])){
		$module_settings[$module] = array();
		$sql = "SELECT * FROM " . db_prefix("module_settings") . " WHERE modulename='$module'";
		$result = db_query_cached($sql,"modulesettings-$module");
		while ($row = db_fetch_assoc($result)){
			$module_settings[$module][$row['setting']] = $row['value'];
		}//end while
	}//end if
}//end function


function module_delete_objprefs($objtype, $objid)
{
	$sql = "DELETE FROM " . db_prefix("module_objprefs") . " WHERE objtype='$objtype' AND objid='$objid'";
	db_query($sql);
	massinvalidate("objpref-$objtype-$objid");
}

function get_module_objpref($type, $objid, $name, $module=false){
	global $mostrecentmodule;
	if ($module === false) $module = $mostrecentmodule;
	$sql = "SELECT value FROM ".db_prefix("module_objprefs")." WHERE modulename='$module' AND objtype='$type' AND setting='".addslashes($name)."' AND objid='$objid' ";
	$result = db_query_cached($sql, "objpref-$type-$objid-$name-$module", 86400);
	if (db_num_rows($result)>0){
		$row = db_fetch_assoc($result);
		return $row['value'];
	}
	//we couldn't find this elsewhere, load the default value if it exists.
	$info = get_module_info($module);
	if (isset($info['prefs-'.$type][$name])){
		if (is_array($info['prefs-'.$type][$name])) {
			$v = $info['prefs-'.$type][$name][0];
			$x = explode("|", $v);
		} else {
			$x = explode("|",$info['prefs-'.$type][$name]);
		}
		if (isset($x[1])){
			set_module_objpref($type,$objid,$name,$x[1],$module);
			return $x[1];
		}
	}
	return NULL;
}

function set_module_objpref($objtype,$objid,$name,$value,$module=false){
	global $mostrecentmodule;
	if ($module === false) $module = $mostrecentmodule;
	// Delete the old version and insert the new
	$sql = "REPLACE INTO " . db_prefix("module_objprefs") . "(modulename,objtype,setting,objid,value) VALUES ('$module', '$objtype', '$name', '$objid', '".addslashes($value)."')";
	db_query($sql);
	invalidatedatacache("objpref-$objtype-$objid-$name-$module");
}

function increment_module_objpref($objtype,$objid,$name,$value=1,$module=false) {
	global $mostrecentmodule;
	$value = (float)$value;
	if ($module === false) $module = $mostrecentmodule;
	$sql = "UPDATE " . db_prefix("module_objprefs") . " SET value=value+$value WHERE modulename='$module' AND setting='".addslashes($name)."' AND objtype='".addslashes($objtype)."' AND objid=$objid;";
	$result= db_query($sql);
	if (db_affected_rows($result)==0){
		//if the update did not do anything, insert the row
		$sql = "INSERT INTO " . db_prefix("module_objprefs") . "(modulename,objtype,setting,objid,value) VALUES ('$module', '$objtype', '$name', '$objid', '".addslashes($value)."')";
		db_query($sql);
	}
	invalidatedatacache("objpref-$objtype-$objid-$name-$module");
}


function module_delete_userprefs($user){
	$sql = "DELETE FROM " . db_prefix("module_userprefs") . " WHERE userid='$user'";
	db_query($sql);
}

$module_prefs=array();
function get_all_module_prefs($module=false,$user=false){
	global $module_prefs,$mostrecentmodule,$session;
	if ($module === false) $module = $mostrecentmodule;
	if ($user === false) $user = $session['user']['acctid'];
	load_module_prefs($module,$user);

	return $module_prefs[$user][$module];
}

function get_module_pref($name,$module=false,$user=false){
	global $module_prefs,$mostrecentmodule,$session;
	if ($module === false) $module = $mostrecentmodule;
	if ($user===false) {
		if(isset($session['user']['loggedin']) && $session['user']['loggedin']) $user = $session['user']['acctid'];
		else $user = 0;
	}

	if (isset($module_prefs[$user][$module][$name])) {
		return $module_prefs[$user][$module][$name];
	}

	//load here, not before
	load_module_prefs($module,$user);
	//check if *now* it's loaded
	if (isset($module_prefs[$user][$module][$name])) {
		return $module_prefs[$user][$module][$name];
	}

	if (!is_module_active($module)) return NULL;

	//we couldn't find this elsewhere, load the default value if it exists.
	$info = get_module_info($module);
	if (isset($info['prefs'][$name])){
		if (is_array($info['prefs'][$name])) {
			$v = $info['prefs'][$name][0];
			$x = explode("|", $v);
		} else {
			$x = explode("|",$info['prefs'][$name]);
		}
		if (isset($x[1])){
			set_module_pref($name,$x[1],$module,$user);
			return $x[1];
		}
	}
	return NULL;
}

function set_module_pref($name,$value,$module=false,$user=false){
	global $module_prefs,$mostrecentmodule,$session;
	if ($module === false) $module = $mostrecentmodule;
	if ($user === false) $uid=$session['user']['acctid'];
	else $uid = $user;
	load_module_prefs($module, $uid);

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
	$module_prefs[$uid][$module][$name] = $value;
}

function increment_userpref($name,$value=1,$module=false,$user=false){
	global $module_prefs,$mostrecentmodule,$session;
	$value = (float)$value;
	if ($module === false) $module = $mostrecentmodule;
	if ($user === false) $uid=$session['user']['acctid'];
	else $uid = $user;
	load_module_prefs($module, $uid);

	//don't write to the DB if the user isn't logged in.
	if (!$session['user']['loggedin'] && !$user) {
		// We do need to save to the loaded copy here however
		$module_prefs[$uid][$module][$name] += $value;
		return;
	}

	if (isset($module_prefs[$uid][$module][$name])){
		$sql = "UPDATE " . db_prefix("module_userprefs") . " SET value=value+$value WHERE modulename='$module' AND setting='$name' AND userid='$uid'";
		db_query($sql);
		$module_prefs[$uid][$module][$name] += $value;
	}else{
		$sql = "INSERT INTO " . db_prefix("module_userprefs"). " (modulename,setting,userid,value) VALUES ('$module','$name','$uid','".addslashes($value)."')";
		db_query($sql);
		$module_prefs[$uid][$module][$name] = $value;
	}
}

function clear_module_pref($name,$module=false,$user=false){
 	global $module_prefs,$mostrecentmodule,$session;
	if ($module === false) $module = $mostrecentmodule;
	if ($user === false) $uid=$session['user']['acctid'];
	else $uid = $user;
	load_module_prefs($module, $uid);

	//don't write to the DB if the user isn't logged in.
	if (!$session['user']['loggedin'] && !$user) {
		// We do need to trash the loaded copy here however
		unset($module_prefs[$uid][$module][$name]);
		return;
	}

	if (isset($module_prefs[$uid][$module][$name])){
		$sql = "DELETE FROM " . db_prefix("module_userprefs") . " WHERE modulename='$module' AND setting='$name' AND userid='$uid'";
		db_query($sql);
	}
	unset($module_prefs[$uid][$module][$name]);
}

function load_module_prefs($module, $user=false){
	global $module_prefs,$session;
	if ($user===false) $user = $session['user']['acctid'];
	if (!isset($module_prefs[$user])) $module_prefs[$user] = array();
	if (!isset($module_prefs[$user][$module])){
		$module_prefs[$user][$module] = array();
		$sql = "SELECT setting,value FROM " . db_prefix("module_userprefs") . " WHERE modulename='$module' AND userid='$user'";
		$result = db_query($sql);
		while ($row = db_fetch_assoc($result)){
			$module_prefs[$user][$module][$row['setting']] = $row['value'];
		}//end while
	}//end if
}//end function

function get_module_info($shortname){
	global $mostrecentmodule;

	$moduleinfo = array();

	// Save off the mostrecent module.
	$mod = $mostrecentmodule;

	if(injectmodule($shortname,true)) {
		$fname = $shortname."_getmoduleinfo";
		if (function_exists($fname)){
			tlschema("module-$shortname");
			$moduleinfo = $fname();
			tlschema();
			// Don't pick up this text unless we need it.
			if (!isset($moduleinfo['name']) ||
					!isset($moduleinfo['category']) ||
					!isset($moduleinfo['author']) ||
					!isset($moduleinfo['version'])) {
				$ns = translate_inline("Not specified","common");
			}
			if (!isset($moduleinfo['name']))
				$moduleinfo['name']="$ns ($shortname)";
			if (!isset($moduleinfo['category']))
				$moduleinfo['category']="$ns ($shortname)";
			if (!isset($moduleinfo['author']))
				$moduleinfo['author']="$ns ($shortname)";
			if (!isset($moduleinfo['version']))
				$moduleinfo['version']="0.0";
			if (!isset($moduleinfo['download']))
				$moduleinfo['download'] = "";
			if (!isset($moduleinfo['description']))
				$moduleinfo['description'] = "";
		}
		if (!is_array($moduleinfo) || count($moduleinfo)<2){
			$mf = translate_inline("Missing function","common");
			$moduleinfo = array(
					"name"=>"$mf ({$shortname}_getmoduleinfo)",
					"version"=>"0.0",
					"author"=>"$mf ({$shortname}_getmoduleinfo)",
					"category"=>"$mf ({$shortname}_getmoduleinfo)",
					"download"=>"",
				);
		}
	} else {
		// This module couldn't be injected at all.
		return array();
	}
	$mostrecentmodule = $mod;
	if (!isset($moduleinfo['requires']))
		$moduleinfo['requires'] = array();
	return $moduleinfo;
}

function module_wipehooks() {
	global $mostrecentmodule;
	//lock the module hooks table.
	$sql = "LOCK TABLES ".db_prefix("module_hooks")." WRITE";
	db_query($sql);

	//invalidate data caches for module hooks associated with this module.
	$sql = "SELECT location FROM ".db_prefix("module_hooks")." WHERE modulename='$mostrecentmodule'";
	$result = db_query($sql);
	while ($row = db_fetch_assoc($result)){
		invalidatedatacache("hook-".$row['location']);
	}
	invalidatedatacache("moduleprepare");

	debug("Removing all hooks for $mostrecentmodule");
	$sql = "DELETE FROM " . db_prefix("module_hooks"). " WHERE modulename='$mostrecentmodule'";
	db_query($sql);
	//unlock the module hooks table.
	$sql = "UNLOCK TABLES";
	db_query($sql);

	$sql = "DELETE FROM " . db_prefix("module_event_hooks") . " WHERE modulename='$mostrecentmodule'";
	db_query($sql);

}

function module_addeventhook($type, $chance){
	global $mostrecentmodule;
	debug("Adding an event hook on $type events for $mostrecentmodule");
	$sql = "DELETE FROM " . db_prefix("module_event_hooks") . " WHERE modulename='$mostrecentmodule' AND event_type='$type'";
	db_query($sql);
	$sql = "INSERT INTO " . db_prefix("module_event_hooks") . " (event_type,modulename,event_chance) VALUES ('$type', '$mostrecentmodule','".addslashes($chance)."')";
	db_query($sql);
	invalidatedatacache("event-".$type);
}

function module_drophook($hookname,$functioncall=false){
	global $mostrecentmodule;
	if ($functioncall===false)
		$functioncall=$mostrecentmodule."_dohook";
	$sql = "DELETE FROM " . db_prefix("module_hooks") . " WHERE modulename='$mostrecentmodule' AND location='".addslashes($hookname)."' AND function='".addslashes($functioncall)."'";
	db_query($sql);
	invalidatedatacache("hook-".$hookname);
	invalidatedatacache("moduleprepare");
}

/**
  * Called by modules to register themselves for a game module hook point, with default priority.
  * Modules with identical priorities will execute alphabetically.  Modules can only have one hook on a given hook name,
  * even if they call this function multiple times, unless they specify different values for the functioncall argument.
  *
  * @param string $hookname The hook to receive a notification for
  * @param string $functioncall The function that should be called, if not specified, use {modulename}_dohook() as the function
  * @param string $whenactive An expression that should be evaluated before triggering the event, if not specified, none.
   */
function module_addhook($hookname,$functioncall=false,$whenactive=false){
	module_addhook_priority($hookname,50,$functioncall,$whenactive);
}

/**
  * Called by modules to register themselves for a game module hook point, with a given priority -- lower numbers execute first.
  * Modules with identical priorities will execute alphabetically.  Modules can only have one hook on a given hook name,
  * even if they call this function multiple times, unless they specify different values for the functioncall argument.
  *
  * @param string $hookname The hook to receive a notification for
  * @param integer $priority The priority for this hooking -- lower numbers execute first.  < 50 means earlier-than-normal execution, > 50 means later than normal execution.  Priority only affects execution order compared to other events registered on the same hook, all events on a given hook will execute before the game resumes execution.
  * @param string $functioncall The function that should be called, if not specified, use {modulename}_dohook() as the function
  * @param string $whenactive An expression that should be evaluated before triggering the event, if not specified, none.
   */
function module_addhook_priority($hookname,$priority=50,$functioncall=false,$whenactive=false){
	global $mostrecentmodule;
	module_drophook($hookname,$functioncall);

	if ($functioncall===false) $functioncall=$mostrecentmodule."_dohook";
	if ($whenactive === false) $whenactive = '';

	debug("Adding a hook at $hookname for $mostrecentmodule to $functioncall which is active on condition '$whenactive'");
	//we want to do a replace in case there's any garbage left in this table which might block new clean data from going in.
	//normally that won't be the case, and so this doesn't have any performance implications.
	$sql = "REPLACE INTO " . db_prefix("module_hooks") . " (modulename,location,function,whenactive,priority) VALUES ('$mostrecentmodule','".addslashes($hookname)."','".addslashes($functioncall)."','".addslashes($whenactive)."','".addslashes($priority)."')";
	db_query($sql);
	invalidatedatacache("hook-".$hookname);
	invalidatedatacache("moduleprepare");
}

function module_sem_acquire(){
	//DANGER DANGER WILL ROBINSON
	//use of this function can be EXTREMELY DANGEROUS
	//If there is ANY WAY you can avoid using it, I strongly recommend you
	//do so.  That said, I recognize that at times you need to acquire a
	//semaphore so I'll provide a function to accomplish it.

	//PLEASE make sure you call module_sem_release() AS SOON AS YOU CAN.

	//Since Semaphore support in PHP is a compile time option that is off
	//by default, I'll rely on MySQL's semaphore on table lock.  Note this
	//is NOT as efficient as the PHP semaphore because it blocks other
	//things too.
	//If someone is feeling industrious, a smart function that uses the PHP
	//semaphore when available, and otherwise call the MySQL LOCK TABLES
	//code would be sincerely appreciated.
	$sql = "LOCK TABLES " . db_prefix("module_settings") . " WRITE";
	db_query($sql);
}

function module_sem_release(){
	//please see warnings in module_sem_acquire()
	$sql = "UNLOCK TABLES";
	db_query($sql);
}

function module_collect_events($type, $allowinactive=false)
{
	global $session, $playermount;
	global $blocked_modules, $block_all_modules, $unblocked_modules;
	$active = "";
	$events = array();
	if (!$allowinactive) $active = " active=1 AND";

	$sql = "SELECT " . db_prefix("module_event_hooks") . ".* FROM " . db_prefix("module_event_hooks") . " INNER JOIN " . db_prefix("modules") . " ON ". db_prefix("modules") . ".modulename = " . db_prefix("module_event_hooks") . ".modulename WHERE $active event_type='$type' ORDER BY RAND(".e_rand().")";
	$result = db_query_cached($sql,"event-".$type);
	while ($row = db_fetch_assoc($result)){
		// The event_chance bit needs to return a value, but it can do that
		// in any way it wants, and can have if/then or other logical
		// structures, so we cannot just force the 'return' syntax unlike
		// with buffs.
		ob_start();
		$chance = eval($row['event_chance'].";");
		$err = ob_get_contents();
		ob_end_clean();
		if ($err > ""){
			debug(array("error"=>$err,"Eval code"=>$row['event_chance']));
		}
		if ($chance < 0) $chance = 0;
		if ($chance > 100) $chance = 100;
		if (($block_all_modules || array_key_exists($row['modulename'],$blocked_modules) && $blocked_modules[$row['modulename']]) &&
				(!array_key_exists($row['modulename'],$unblocked_modules) || !$unblocked_modules[$row['modulename']])) {
			$chance = 0;
		}
		$events[] = array('modulename'=>$row['modulename'],
				'rawchance' => $chance);
	}

	// Now, normalize all of the event chances
	$sum = 0;
	reset($events);
	foreach($events as $event) {
		$sum += $event['rawchance'];
	}
	reset($events);
	foreach($events as $index=>$event) {
		if ($sum == 0) {
			$events[$index]['normchance'] = 0;
		} else {
			$events[$index]['normchance'] =
				round($event['rawchance']/$sum*100,3);
			// If an event requests 1% chance, don't give them more!
			if ($events[$index]['normchance'] > $event['rawchance'])
				$events[$index]['normchance'] = $event['rawchance'];
		}
	}
	return modulehook("collect-events", $events);
}

function module_events($eventtype, $basechance, $baseLink = false) {
	if ($baseLink === false){
		global $PHP_SELF;
		$baseLink = substr($PHP_SELF,strrpos($PHP_SELF,"/")+1)."?";
	}else{
		//debug("Base link was specified as $baseLink");
		//debug(debug_backtrace());
	}
	if (e_rand(1, 100) <= $basechance) {
		global $PHP_SELF;
		$events = module_collect_events($eventtype);
		$chance = r_rand(1, 100);
		reset($events);
		$sum = 0;
		foreach($events as $event) {
			if ($event['rawchance'] == 0) {
				continue;
			}
			if ($chance > $sum && $chance <= $sum + $event['normchance']) {
				$_POST['i_am_a_hack'] = 'true';
				tlschema("events");
				output("`^`c`bSomething Special!`c`b`0");
				tlschema();
				$op = httpget('op');
				httpset('op', "");
				module_do_event($eventtype, $event['modulename'], false, $baseLink);
				httpset('op', $op);
				return 1;
			}
			$sum += $event['normchance'];
		}
	}
	return 0;
}

function module_do_event($type, $module, $allowinactive=false, $baseLink=false)
{
	global $navsection;

	if ($baseLink === false){
		global $PHP_SELF;
		$baseLink = substr($PHP_SELF,strrpos($PHP_SELF,"/")+1)."?";
	}else{
		//debug("Base link was specified as $baseLink");
		//debug(debug_backtrace());
	}
	// Save off the mostrecent module since having that change can change
	// behaviour especially if a module calls modulehooks itself or calls
	// library functions which cause them to be called.
	if (!isset($mostrecentmodule)) $mostrecentmodule = "";
	$mod = $mostrecentmodule;
	$_POST['i_am_a_hack'] = 'true';
	if(injectmodule($module, $allowinactive)) {
		$oldnavsection = $navsection;
		tlschema("module-$module");
		$fname = $module."_runevent";
		$fname($type,$baseLink);
		tlschema();
		//hook into the running event, but only in *this* running event, not in all
		modulehook("runevent_$module", array("type"=>$type, "baselink"=>$baseLink, "get"=>httpallget(), "post"=>httpallpost()));
		//revert nav section after we're done here.
		$navsection = $oldnavsection;
	}
	$mostrecentmodule=$mod;
}

function event_sort($a, $b)
{
	return strcmp($a['modulename'], $b['modulename']);
}

function module_display_events($eventtype, $forcescript=false) {
	global $PHP_SELF, $session;
	if (!($session['user']['superuser'] & SU_DEVELOPER)) return;
	if ($forcescript === false)
		$script = substr($PHP_SELF,strrpos($PHP_SELF,"/")+1);
	else
		$script = $forcescript;
	$events = module_collect_events($eventtype,true);

	if (!is_array($events) || count($events) == 0) return;

	usort($events, "event_sort");

	tlschema("events");
	output("`n`nSpecial event triggers:`n");
	$name = translate_inline("Name");
	$rchance = translate_inline("Raw Chance");
	$nchance = translate_inline("Normalized Chance");
	rawoutput("<table cellspacing='1' cellpadding='2' border='0' bgcolor='#999999'>");
	rawoutput("<tr class='trhead'>");
	rawoutput("<td>$name</td><td>$rchance</td><td>nchance</td>");
	rawoutput("</tr>");
	$i = 0;
	foreach($events as $event) {
		// Each event is an associative array of 'modulename',
		// 'rawchance' and 'normchance'
		rawoutput("<tr class='" . ($i%2==0?"trdark":"trlight")."'>");
		$i++;
		if ($event['modulename']) {
			$link = "module-{$event['modulename']}";
			$name = $event['modulename'];
		}
		$rlink = "$script?eventhandler=$link";
		$rlink = str_replace("?&","?",$rlink);
		$first = strpos($rlink, "?");
		$rl1 = substr($rlink, 0, $first+1);
		$rl2 = substr($rlink, $first+1);
		$rl2 = str_replace("?", "&", $rl2);
		$rlink = $rl1 . $rl2;
		rawoutput("<td><a href='$rlink'>$name</a></td>");
		addnav("", "$rlink");
		rawoutput("<td>{$event['rawchance']}</td>");
		rawoutput("<td>{$event['normchance']}</td>");
		rawoutput("</tr>");
	}
	rawoutput("</table>");
}

function module_editor_navs($like, $linkprefix)
{
	$sql = "SELECT formalname,modulename,active,category FROM " . db_prefix("modules") . " WHERE infokeys LIKE '%|$like|%' ORDER BY category,formalname";
	$result = db_query($sql);
	$curcat = "";
	while($row = db_fetch_assoc($result)) {
		if ($curcat != $row['category']) {
			$curcat = $row['category'];
			addnav(array("%s Modules",$curcat));
		}
		//I really think we should give keyboard shortcuts even if they're
		//susceptible to change (which only happens here when the admin changes
		//modules around).  This annoys me every single time I come in to this page.
		addnav_notl(($row['active'] ? "" : "`)") . $row['formalname']."`0",
				$linkprefix . $row['modulename']);
	}
}

function module_objpref_edit($type, $module, $id)
{
	$info = get_module_info($module);
	if (count($info['prefs-'.$type]) > 0) {
		$data = array();
		$msettings = array();
		while(list($key, $val) = each($info['prefs-'.$type])) {
			if (is_array($val)) {
				$v = $val[0];
				$x = explode("|", $v);
				$val[0] = $x[0];
				$x[0] = $val;
			} else {
				$x = explode("|", $val);
			}
			$msettings[$key]=$x[0];
			// Set up default
			if (isset($x[1])) $data[$key]=$x[1];
		}
		$sql = "SELECT setting, value FROM " . db_prefix("module_objprefs") . " WHERE modulename='$module' AND objtype='$type' AND objid='$id'";
		$result = db_query($sql);
		while($row = db_fetch_assoc($result)) {
			$data[$row['setting']] = $row['value'];
		}
		tlschema("module-$module");
		showform($msettings, $data);
		tlschema();
	}
}

function module_compare_versions($a,$b){
	//this function returns -1 when $a < $b, 1 when $a > $b, and 0 when $a == $b
	//insert alternate version detection and comparison algorithms here.

	//default case, typecast as float
	$a = (float)$a;
	$b = (float)$b;
	return ($a < $b ? -1 : ($a > $b ? 1 : 0) );
}

function activate_module($module){
	if (!is_module_installed($module)){
		if (!install_module($module)){
			return false;
		}
	}
	$sql = "UPDATE " . db_prefix("modules") . " SET active=1 WHERE modulename='$module'";
	db_query($sql);
	invalidatedatacache("inject-$module");
	massinvalidate("moduleprepare");
	if (db_affected_rows() <= 0){
		return false;
	}else{
		return true;
	}
}

function deactivate_module($module){
	if (!is_module_installed($module)){
		if (!install_module($module)){
			return false;
		}else{
			//modules that weren't installed go to deactivated state by default in install_module
			return true;
		}
	}
	$sql = "UPDATE " . db_prefix("modules") . " SET active=0 WHERE modulename='$module'";
	db_query($sql);
	invalidatedatacache("inject-$module");
	massinvalidate("moduleprepare");
	if (db_affected_rows() <= 0){
		return false;
	}else{
		return true;
	}
}

function uninstall_module($module){
	if (injectmodule($module,true)) {
		$fname = $module."_uninstall";
		output("Running module uninstall script`n");
		tlschema("module-{$module}");
		$fname();
		tlschema();

		output("Deleting module entry`n");
		$sql = "DELETE FROM " . db_prefix("modules") .
			" WHERE modulename='$module'";
		db_query($sql);

		output("Deleting module hooks`n");
		module_wipehooks();

		output("Deleting module settings`n");
		$sql = "DELETE FROM " . db_prefix("module_settings") .
			" WHERE modulename='$module'";
		db_query($sql);
		invalidatedatacache("modulesettings-$module");

		output("Deleting module user prefs`n");
		$sql = "DELETE FROM " . db_prefix("module_userprefs") .
			" WHERE modulename='$module'";
		db_query($sql);

		output("Deleting module object prefs`n");
		$sql = "DELETE FROM " . db_prefix("module_objprefs") .
			" WHERE modulename='$module'";
		db_query($sql);
		invalidatedatacache("inject-$module");
		massinvalidate("moduleprepare");
		return true;
	} else {
		return false;
	}
}

function install_module($module, $force=true){
 	global $mostrecentmodule, $session;
	$name = $session['user']['name'];
	if (!$name) $name = '`@System`0';

	require_once("lib/sanitize.php");
	if (modulename_sanitize($module)!=$module){
		output("Error, module file names can only contain alpha numeric characters and underscores before the trailing .php`n`nGood module names include 'testmodule.php', 'joesmodule2.php', while bad module names include, 'test.module.php' or 'joes module.php'`n");
		return false;
	}else{
		// If we are forcing an install, then whack the old version.
		if ($force) {
			$sql = "DELETE FROM " . db_prefix("modules") . " WHERE modulename='$module'";
			db_query($sql);
		}
		// We want to do the inject so that it auto-upgrades any installed
		// version correctly.
		if (injectmodule($module,true)) {
			// If we're not forcing and this is already installed, we are done
			if (!$force && is_module_installed($module))
				return true;
			$info = get_module_info($module);
			//check installation requirements
			if (!module_check_requirements($info['requires'])){
				output("`\$Module could not installed -- it did not meet its prerequisites.`n");
				return false;
			}else{
				$keys = "|".join(array_keys($info), "|")."|";
				$sql = "INSERT INTO " . db_prefix("modules") . " (modulename,formalname,moduleauthor,active,filename,installdate,installedby,category,infokeys,version,download,description) VALUES ('$mostrecentmodule','".addslashes($info['name'])."','".addslashes($info['author'])."',0,'{$mostrecentmodule}.php','".date("Y-m-d H:i:s")."','".addslashes($name)."','".addslashes($info['category'])."','$keys','".addslashes($info['version'])."','".addslashes($info['download'])."', '".addslashes($info['description'])."')";
				db_query($sql);
				$fname = $mostrecentmodule."_install";
				if (isset($info['settings']) && count($info['settings']) > 0) {
					foreach($info['settings'] as $key=>$val){
						if (is_array($val)) {
							$x = explode("|", $val[0]);
						} else {
							$x = explode("|",$val);
						}
						if (isset($x[1])){
							set_module_setting($key,$x[1]);
							debug("Setting $key to default {$x[1]}");
						}
					}
				}
				if ($fname() === false) {
					return false;
				}
				output("`^Module installed.  It is not yet active.`n");
				invalidatedatacache("inject-$mostrecentmodule");
				massinvalidate("moduleprepare");
				return true;
			}
		} else {
			output("`\$Module could not be injected.");
			output("Module not installed.");
			output("This is probably due to the module file having a parse error or not existing in the filesystem.`n");
			return false;
		}
	}

}

/**
  * Evaluates a PHP Expression
  *
  * @param string $condition The PHP condition to evaluate
  * @return bool The result of the evaluated expression
  */
function module_condition($condition) {
	global $session;
	$result = eval($condition);
	return (bool)$result;
}

function get_module_install_status(){
	// Collect the names of all installed modules.
	$seenmodules = array();
	$seencats = array();
	$sql = "SELECT modulename,category FROM " . db_prefix("modules");
	$result = @db_query($sql);
	if ($result !== false){
		while ($row = db_fetch_assoc($result)) {
			$seenmodules[$row['modulename'].".php"] = true;
			if (!array_key_exists($row['category'], $seencats))
				$seencats[$row['category']] = 1;
			else
				$seencats[$row['category']]++;
		}
	}

	$uninstmodules = array();
	if ($handle = opendir("modules")){
		$ucount=0;
		while (false !== ($file = readdir($handle))){
			if ($file[0] == ".") continue;
			if (preg_match("/\\.php$/", $file) && !isset($seenmodules[$file])){
				$ucount++;
				$uninstmodules[] = substr($file, 0, strlen($file)-4);
			}
		}
	}
	closedir($handle);
	sort($uninstmodules);
	return array('installedcategories'=>$seencats,'installedmodules'=>$seenmodules,'uninstalledmodules'=>$uninstmodules, 'uninstcount'=>$ucount);
}

function get_racename($thisuser=true) {
	if ($thisuser === true) {
		global $session;
		return translate_inline($session['user']['race'],"race");
	} else {
		return translate_inline($thisuser,"race");
	}
}

?>
