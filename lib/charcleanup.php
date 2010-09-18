<?php

function char_cleanup_allinone($ids,$type){
	//if an acctid doesn't need to be erased, just remove it from this array via the modulehook

	$hook = array(
		"deltype"=>$type,
		"ids"=>$ids,
	);
	$hook = modulehook("delete_character",$hook);
	$ids = $hook['ids'];
	
	if (count($ids)==0){
		return false;
	}
	
	$joined = join($ids,",");
	
	//erase items
	$getitems = "SELECT id FROM ".db_prefix("items_player")." WHERE owner IN ($joined)";
	$items_result = db_query($getitems);
	$items = array();
	while ($row = db_fetch_assoc($items_result)){
		$items[]=$row['id'];
	}
	$items_joined = join($items,",");
	$eraseprefs = "DELETE FROM ".db_prefix("items_prefs")." WHERE id IN ($items_joined)";
	$eraseitems = "DELETE FROM ".db_prefix("items_player")." WHERE id IN ($items_joined)";
	db_query($eraseprefs);
	db_query($eraseitems);

	//erase everything else
	$eraseoutput = "DELETE FROM ".db_prefix("accounts_output")." WHERE acctid IN ($joined)";
	db_query($eraseoutput);
	$erasecommentary = "DELETE FROM ".db_prefix("commentary")." WHERE author IN ($joined)";
	db_query($erasecommentary);
	$erasemoduleprefs = "DELETE FROM ".db_prefix("module_userprefs")." WHERE userid IN ($joined)";
	db_query($erasemoduleprfs);
	
	// Clean up any clan positions held by this character
	$leader = CLAN_LEADER;
	$checkclan = "SELECT clanrank,clanid FROM ".db_prefix("accounts")." WHERE acctid IN ($joined) AND clanid != 0 AND clanrank = $leader";
	$clanresults = db_query($checkclan);
	
	while ($row = db_fetch_assoc($clanresults)){
		$cid = $row['clanid'];
		// We need to auto promote or disband the clan.
		$sql = "SELECT name,acctid,clanrank FROM " . db_prefix("accounts") .
			" WHERE clanid=$cid AND clanrank > " . CLAN_APPLICANT . " AND acctid<>$id ORDER BY clanrank DESC, clanjoindate";
		$res = db_query($sql);
		if (db_num_rows($res)) {
			// Okay, we can promote if needed
			$row = db_fetch_assoc($res);
			if ($row['clanrank'] != CLAN_LEADER) {
				// No other leaders, promote this one
				$id1 = $row['acctid'];
				$sql = "UPDATE " . db_prefix("accounts") .
					" SET clanrank=" . CLAN_LEADER . " WHERE acctid=$id1";
				db_query($sql);
			}
		} else {
			// this clan needs to be disbanded.
			$sql = "DELETE FROM " . db_prefix("clans") . " WHERE clanid=$cid";
			db_query($sql);
			// And just in case we goofed, no players associated with a
			// deleted clan  This shouldn't be important, but.
			$sql = "UPDATE " . db_prefix("accounts") . " SET clanid=0,clanrank=0,clanjoindate='0000-00-00 00:00;00' WHERE clanid=$cid";
			db_query($sql);
		}
	}
	
	//finally, delete the acctids themselves
	$eraseaccounts = "DELETE FROM ".db_prefix("accounts")." WHERE acctid IN ($joined)";
}

function char_cleanup($id, $type)
{
	// this function handles the grunt work of character cleanup.

	// Run any modules hooks who want to deal with character deletion, or stop it
	$return = modulehook("delete_character",
			array("acctid"=>$id, "deltype"=>$type, "dodel"=>true));
			
	if(!$return['dodel']) return false;

	//erase the player's items
	require_once "lib/items.php";
	items_delete_character($id);
	
	// delete the output field from the accounts_output table introduced in 1.1.1
	
	db_query("DELETE FROM " . db_prefix("accounts_output") . " WHERE acctid=$id;");

	// delete the comments the user posted, necessary to have the systemcomments with acctid 0 working

	db_query("DELETE FROM " . db_prefix("commentary") . " WHERE author=$id;");

	// Clean up any clan positions held by this character
	$sql = "SELECT clanrank,clanid FROM " . db_prefix("accounts") .
		" WHERE acctid=$id";
	$res = db_query($sql);
	$row = db_fetch_assoc($res);
	if ($row['clanid'] != 0 && $row['clanrank'] == CLAN_LEADER) {
		$cid = $row['clanid'];
		// We need to auto promote or disband the clan.
		$sql = "SELECT name,acctid,clanrank FROM " . db_prefix("accounts") .
			" WHERE clanid=$cid AND clanrank > " . CLAN_APPLICANT . " AND acctid<>$id ORDER BY clanrank DESC, clanjoindate";
		$res = db_query($sql);
		if (db_num_rows($res)) {
			// Okay, we can promote if needed
			$row = db_fetch_assoc($res);
			if ($row['clanrank'] != CLAN_LEADER) {
				// No other leaders, promote this one
				$id1 = $row['acctid'];
				$sql = "UPDATE " . db_prefix("accounts") .
					" SET clanrank=" . CLAN_LEADER . " WHERE acctid=$id1";
				db_query($sql);
			}
		} else {
			// this clan needs to be disbanded.
			$sql = "DELETE FROM " . db_prefix("clans") . " WHERE clanid=$cid";
			db_query($sql);
			// And just in case we goofed, no players associated with a
			// deleted clan  This shouldn't be important, but.
			$sql = "UPDATE " . db_prefix("accounts") . " SET clanid=0,clanrank=0,clanjoindate='0000-00-00 00:00;00' WHERE clanid=$cid";
			db_query($sql);
		}
	}

	// Delete any module user prefs
	module_delete_userprefs($id);
	
	return true;
}

?>
