<?php

function iitems_trader_getmoduleinfo(){
	$info=array(
		"name"=>"IItems - Player Trading",
		"version"=>"2010-06-17",
		"author"=>"Sylvia Li",
		"category"=>"IItems",
		"download"=>"",
		"prefs"=>array(
			"hastraded"=>"User has performed at least one trade,int|0",
		),
	);
	return $info;
}

function iitems_trader_install(){
	module_addhook("biostat");
	module_addhook("iitems-give-item");
	return true;
}

function iitems_trader_uninstall(){
	return true;
}

function iitems_trader_dohook($hookname,$args){
	global $session;
//	global $module_prefs;
	switch($hookname){
		case "biostat":
			$id = $args['acctid'];
			$ret = rawurlencode($args['return_link']);
			require_once "modules/iitems/lib/lib.php";
			$tradables = iitems_has_property("tradable",true,true,false,false,"all");
			if (is_array($tradables)){
				if ($id <> $session['user']['acctid']){
					$sql = "SELECT uniqueid FROM ".db_prefix('accounts')." WHERE acctid = ".$id;
					$result = db_query($sql);
					$altcheck = db_fetch_assoc($result);
					if (!($altcheck['uniqueid'] == $session['user']['uniqueid']) || 
					   ($session['user']['superuser'] & SU_DEVELOPER)){
						$bioname = $args['name'];
						addnav("Trade!");
						addnav(array("Give an item to %s",$bioname),"runmodule.php?module=iitems_trader&op=trade&id=$id&ret=$ret");
					}
				}
			}
			if ($session['user']['superuser'] & SU_EDIT_COMMENTS){
				addnav("Superuser");
				addnav("Recent Trade History","runmodule.php?module=iitems_trader&op=history&id=$id&ret=$ret");
			}
		break;
		case "iitems-give-item":
			if(isset($args['master']['feature'])){
				$args['player']['feature'] = $args['master']['feature'];
			}
		break;
	}	//end $hookname switch
	return $args;
}

function iitems_trader_run(){
	global $session;
	global $module_prefs;
	require_once "modules/iitems/lib/lib.php";
	$op = httpget('op');
	$id = httpget('id');
	$ret = httpget('ret');
	$return = rawurlencode($ret);
	$tradables = iitems_has_property("tradable",true,true,false,false,"all");
	$sql = "SELECT uniqueid, acctid, name, sex FROM ".db_prefix('accounts')." WHERE acctid = ".$id;
	$result = db_query($sql);
	$tradewith = db_fetch_assoc($result);
	$bioname = $tradewith['name'];
	if ($tradewith['sex']==0){
		$subj="he";
		$obj="him";
		$poss="his";
	}else{
		$subj="she";
		$obj="her";
		$poss="her";
	}

	$hookargs = array();
	$hookargs['tradables'] = $tradables;
	$hookargs['tradewith'] = $tradewith;
	$hookargs['return'] = $return;
	// This hook is to allow modules to remove items from the tradables list if
	// the proposed trading partner does not qualify -- for instance, has no card case.
//	debug("before iitems_tradables-top hook:");
//	debug($hookargs);
	$hookargs = modulehook("iitems_tradables-top",$hookargs);
//	debug("after iitems_tradables-top hook:");
//	debug($hookargs);

	$tradables = $hookargs['tradables'];
	
	page_header("Trade!");
	switch ($op){
		case "trade":
			$cantrade = true;
			if (!isset($hookargs['specialdesc'])){
				switch (count($tradables)){
					case 0:
						$phrase = "nothing";
						$cantrade = false;
					break;
					case 1:
						$phrase = "one item";
					break;
					default:
						$phrase = count($tradables)." different items";
					break;
				}
			} else {
				switch (count($tradables)-1){
					case 0:
						$phrase = "nothing, except ";
					break;
					case 1:
						$phrase = "one item and ";
					break;
					default:
						$phrase = (count($tradables)-1)." different items and ";
					break;
				}
				$phrase .= $hookargs['specialdesc'];
			}
			output("Searching through your possessions, you find you have %s you could give to %s.`n`n",$phrase,$bioname);
//			debug($tradables);
			if ($cantrade){
				addnav("Trade");
				foreach($tradables AS $key => $details){
					addnav(array("(%s) %s",$details['quantity'],$details['verbosename']),"runmodule.php?module=iitems_trader&op=tradewarn&id=$id&item=$key&ret=$return");
				}
				$hookargs = modulehook("iitems_tradables-link",$hookargs);
			}
		break;
		case "tradewarn":
			$item = httpget('item');
			if (!get_module_pref('hastraded')){
				output("Ah, so you're interested in trading your `b%s`b to someone. All right, that could be a very good move, but listen. You only get to control your own actions. What other people do is up to them. Either... make sure you've agreed on the trade beforehand and `b%s`b is someone you trust to give you whatever you're trading this item for... or think of yourself as a kind philanthropist who's generously giving things away.`n`nNo, seriously. Trading is between you and the other person. Talk to %s, negotiate with %s, haggle with %s, conclude the deal. If %s doesn't deliver the goods this time, hey, you'll know who %s is. You don't have to deal with %s again. The admin and game mods are not police and `iwill not`i intervene, so don't even ask.`n`nWith that obligatory warning out of the way...`0`n`n",$tradables[$item]['verbosename'],$bioname, $obj,$obj,$obj,$subj,$subj,$obj);
			}
			output("You're quite sure you want to trade your %s to %s?`n`n",$tradables[$item]['verbosename'],$bioname);
			addnav("Trade");
			addnav("Yes, do it!","runmodule.php?module=iitems_trader&op=tradefinal&id=$id&item=$item&ret=$return");
			addnav("Wait, no, not that...","runmodule.php?module=iitems_trader&op=trade&id=$id&ret=$return");
		break;
		case "tradefinal":
			$item = httpget('item');
			$inventory = iitems_get_player_inventory();
			foreach($inventory AS $ikey => $details){
				if ($details['itemid'] == $tradables[$item]['itemid']){
					$itemkey = $ikey;
				}
			}

// This hook is to allow special handling of the transfer for collectibles that need to get fancy.
// (Example - marbles.) When using this hook, you'll have to:
//      Do your own transfer to the other player
//      Change the $item-th row in $hookargs['tradables'] to contain the itemid and verbosename
//        you want to appear in the trading history and the 'Mission accomplished' message
//      Set $hookargs['specialhandling'] to 1

			$hookargs['specialhandling'] = 0;
			//	debug("before iitems_tradables-final hook:");
			//	debug($hookargs);
			$hookargs = modulehook("iitems_tradable-final",$hookargs);
			//	debug("after iitems_tradables-final hook:");
			//	debug($hookargs);

			$tradables = $hookargs['tradables'];
			if (!isset($hookargs['specialdesc'])){
				iitems_give_item($tradables[$item]['itemid'],$id);
				debug($itemkey);
				debug($inventory);
				iitems_discard_item($itemkey);
				$message = $tradables[$item]['feature'].": ".$tradables[$item]['itemid']." (".$tradables[$item]['verbosename'].") to ".$bioname.".";
				debuglog($message,$id,false,"trade");
				output("Mission accomplished! %s now has your %s.",$bioname,$tradables[$item]['verbosename']);
			}
			set_module_pref("hastraded",1);
		break;
		case "history":
			output("`bRecent Trade History of %s`b`n`n",$bioname);
			$dblog = db_prefix('debuglog');
			$accts = db_prefix('accounts');
			$sql = "SELECT date, actor, target, message FROM ".$dblog." WHERE actor = ".$id." AND field = 'trade' ORDER BY date DESC";
			$history = array();
			$result = db_query($sql);
			if (db_num_rows($result) > 0){
				output("Performed the following trades:`n");
				rawoutput("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>",true);
				rawoutput("<tr class='trhead'><td>timestamp</td><td>(rel)</td><td>trading action</td></tr>");
				$classcount = 1;
				for ($i=0;$i<db_num_rows($result);$i++){
					$classcount++;
					$class = ($classcount%2?"trdark":"trlight");
					rawoutput("<tr class='$class'><td>");
					$row = db_fetch_assoc($result);
					$time = strtotime($row['date']) + ($session['user']['prefs']['timeoffset'] * 60 * 60);
					$abstime = date("m/d h:ia",$time);
					$reltime = reltime(strtotime($row['date']));
					output($abstime);
					rawoutput("</td><td>");
					output($reltime);
					rawoutput("</td><td>");
					output("traded %s`n",$row['message']);
					rawoutput("</td></tr>");			// end the row
				}		// end of for
				rawoutput("</table>");
			} else {
				output("Performed no trades.`n");
			}

			$sql = "SELECT date, actor, name, target, message FROM ".$dblog." LEFT JOIN ".$accts." ON ".$dblog.".actor = ".$accts.".acctid WHERE target = ".$id." AND field = 'trade' ORDER BY date DESC";
			$rhistory = array();
			$result = db_query($sql);
			if (db_num_rows($result) > 0){
				output("`nReceived the following items in trade:`n");
				rawoutput("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>",true);
				rawoutput("<tr class='trhead'><td>timestamp</td><td>(rel)</td><td>who</td><td>trading action</td></tr>");
				$classcount = 1;
				for ($i=0;$i<db_num_rows($result);$i++){
					$classcount++;
					$class = ($classcount%2?"trdark":"trlight");
					rawoutput("<tr class='$class'><td>");
					$row = db_fetch_assoc($result);
					$time = strtotime($row['date']) + ($session['user']['prefs']['timeoffset'] * 60 * 60);
					$abstime = date("m/d h:ia",$time);
					$reltime = reltime(strtotime($row['date']));
					output($abstime);
					rawoutput("</td><td>");
					output($reltime);
					rawoutput("</td><td>");
					output($row['name']);
					rawoutput("</td><td>");
					output("traded %s`n",$row['message']);
					rawoutput("</td></tr>");			// end the row
				}		// end of for
				rawoutput("</table>");
			} else {
				output("`nReceived no items in trade.`n");
			}
		break;
	}
	addnav("Back");
	addnav(array("To %s bio",$poss),"bio.php?char=$id&ret=$return");
	page_footer();
	return true;
}
?>
