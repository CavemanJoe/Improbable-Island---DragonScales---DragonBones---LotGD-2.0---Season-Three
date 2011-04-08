<?php

global $session;

$hid = httpget('hid');
$rid = httpget('rid');
$sub = httpget('sub');
$key = httpget('key');

$key5cost = 5;
$key10cost = 5;
$key20cost = 10;
$key30cost = 30;
$key100cost = 50;

require_once "modules/improbablehousing/lib/lib.php";
$house = improbablehousing_gethousedata($hid);

page_header("Keys and Locks");

switch ($sub){
	case "start":
		output("`0You can have keys cut and sent to as many people as you like.  There are five types of keys:`n`n`bFront Door Keys`b allow a person to enter a locked Dwelling, but don't extend to any rooms.`n`bAccess Keys`b allow a person to enter a locked room, unless it has been deadbolted.`n`bControl Keys`b allow a person to enter a locked room and to lock or unlock that room, unless it has been deadbolted.`n`bDeadbolt Keys`b allow a person to enter a locked or deadbolted room, lock or unlock that room, and lock or unlock the deadbolt.`n`bMaster Keys`b allow complete control over the Dwelling, with the one caveat that this person cannot create more Master Keys.  That is to say, the player can create new build jobs, kick out sleepers, manage decorations, cut keys and all of the other activities associated with owning this building.  In addition, if your account is deleted but your Dwelling is awesome and you don't tell the moderators or administrator what to do with it, then we'll have to determine for ourselves whether to knock it down or pass it along to someone, and the Master Key record is one of the things we'll look at.  `b`4Do not give out Master Keys lightly.`0`b`n`nOnly `bFront Door Keys`b and `bMaster Keys`b allow a player to enter a locked Dwelling.  All other keys operate on a per-room basis.`n`n`bPlayers who are already inside rooms when you lock them`b will be able to stay inside the room and chat as normal.  You don't have to cut any keys if you want to invite players into a room and then lock it after them for privacy - cutting keys just makes it a lot easier.  Naturally, once a player leaves a room that's locked, they won't be able to get back in again unless they have the proper key (or unless you open the door for them again).`n`n");
		output("The cost of cutting keys is as follows:`nFront Door Key: `b%s`b Cigarettes`nAccess Key: `b%s`b Cigarettes`nControl Key: `b%s`b Cigarettes`nDeadbolt Key: `b%s`b Cigarettes`nMaster Key: `b%s`b Cigarettes`n`n",$key5cost,$key10cost,$key20cost,$key30cost,$key100cost);
		output("`b`4You are cutting a key for the room named \"%s.`4\"`0`b`n`nThe first step in cutting a key is to decide the type of key you'd like cut.`n`n",$house['data']['rooms'][$rid]['name']);
		addnav("Cut Keys");
		$gems = $session['user']['gems'];
		if ($gems>=$key5cost){
			addnav("Cut Front Door Key","runmodule.php?module=improbablehousing&op=keys&sub=search&hid=$hid&rid=$rid&key=frontdoor");
			if ($rid>0){
				if ($gems>=$key10cost){
					addnav("Cut Access Key","runmodule.php?module=improbablehousing&op=keys&sub=search&hid=$hid&rid=$rid&key=access");
					if ($gems>=$key20cost){
						addnav("Cut Control Key","runmodule.php?module=improbablehousing&op=keys&sub=search&hid=$hid&rid=$rid&key=control");
						if ($gems>=$key30cost){
							addnav("Cut Deadbolt Key","runmodule.php?module=improbablehousing&op=keys&sub=search&hid=$hid&rid=$rid&key=deadbolt");
						}
					}
				}
			}
			if ($house['ownedby']==$session['user']['acctid'] && $gems>=$key100cost){
				addnav("Cut Master Key","runmodule.php?module=improbablehousing&op=keys&sub=search&hid=$hid&rid=$rid&key=master");
			}
		} else {
			addnav("You can't afford to cut any keys right now.","");
		}
		addnav("Cancel");
		addnav("Back to the Dwelling","runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=$rid");
	break;
	case "search":
		switch ($key){
			case "frontdoor":
				output("You are cutting a `bFront Door`b key for this Dwelling.  This key will allow a player to enter this Dwelling when it is locked.  It will not allow them to enter any locked rooms, but you can cut keys for those rooms individually if you like.  If the player for whom you're cutting this key already has a Master Key, then you don't need to cut them a Front Door key.`n`n");
			break;
			case "access":
				output("You are cutting an `bAccess`b key for the room called \"%s`0.\"  This key will allow a player to enter this room when it is locked, unless you've deadbolted this room.  It will not allow them to lock or unlock this room - if they enter a locked room, the door will lock automatically behind them.  If you want the player to be able to lock and unlock rooms, you'll need a `bControl`b Key.  If the player to whom you want to give this key already has a `bControl`b key or a `bDeadbolt`b key to this room, or a `bMaster`b key, then `byou don't need to give them an Access key`b - they can already enter this room when it's locked.`n`n",$house['data']['rooms'][$rid]['name']);
			break;
			case "control":
				output("You are cutting a `bControl`b key for the room called \"%s`0.\"  This key will allow a player to enter this room, lock it, or unlock it, unless you've deadbolted the room.  This key will not allow a player to enter, lock or unlock this room when it's deadbolted.  If you want the player to be able to enter or control a deadbolted room, you'll need a `bDeadbolt`b Key.  If the player to whom you want to give this key already has a `bDeadbolt`b key to this room or a `bMaster`b key, then `byou don't need to give them a Control key`b - they can already lock and unlock this room.`n`n",$house['data']['rooms'][$rid]['name']);
			break;
			case "deadbolt":
				output("You are cutting a `bDeadbolt`b key for the room called \"%s`0.\"  This key will allow a player to enter this room under any circumstances, no matter what combination of locks are present on the room.  It will also allow them to lock and unlock both the main and deadbolt locks on this room, and set effects for Mementos they have created.  If the player for whom you're cutting this key already has a `bMaster`b key, then `byou don't need to give them a Deadbolt key`b - they can already enter this room (and any other room) whenever they please.`n`n",$house['data']['rooms'][$rid]['name']);
			break;
			case "master":
				output("`c`b`4WARNING`0`b`nYou have selected to cut a Master Key.  If you go ahead with this, then the player you choose will be able to perform every single action that you've been able to perform on this Dwelling so far.`cIt'll be pretty much as though they own this Dwelling; they'll be able to create new rooms, decorate, give out keys, kick out sleepers, and `beverything else that you can do.`b  The only thing they won't be able to do is give out Master Keys.  If for some reason you disappear from the Island, and the moderators have to figure out what to do with your Dwelling, then the people to whom you give Master Keys might influence the moderators' decision.`c`b`4EXTRA SPECIAL WARNING`b`cIf you give a Master Key to someone and they use it to wreak havoc on your dwelling, `bneither the mods nor the admin will be able to put it back to the way it was before`b.  Likewise, once a Master Key has been given, not even a pack of wild Panzthers will be able to drag it back.  `b`4Please be absolutely, positively sure that this is what you want to do.`b`0`n`n");
			break;
		}
		output("If this is what you want to do, then search for the key's recipient below.  If not, head back to the room.`n`n");
		$search = translate_inline("Search");
		rawoutput("<form action='runmodule.php?module=improbablehousing&op=keys&sub=narrowdown&hid=$hid&rid=$rid&key=$key' method='POST'>");
		addnav("","runmodule.php?module=improbablehousing&op=keys&sub=narrowdown&hid=$hid&rid=$rid&key=$key");
		rawoutput("<input name='name' id='name'>");
		rawoutput("<input type='submit' class='button' value='$search'>");
		rawoutput("</form>");
		addnav("Cancel");
		addnav("Back to the Dwelling","runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=$rid");
	break;
	case "narrowdown":
		$string="%";
		$name = httppost('name');
		for ($x=0;$x<strlen($name);$x++){
			$string .= substr($name,$x,1)."%";
		}
		$sql = "SELECT name,acctid FROM " . db_prefix("accounts") . " WHERE name LIKE '".addslashes($string)."' AND locked=0 ORDER BY name";
		$result = db_query($sql);
		if (db_num_rows($result)<=0){
			output("Sorry, couldn't find anyone who matched that search.`n`n");
		}elseif(db_num_rows($result)>100){
			output("Well, that could be anyone!  Wanna try that again?`n`n");
			output("Who would you like to give this key to?`n`n");
			$search = translate_inline("Search");
			rawoutput("<form action='runmodule.php?module=improbablehousing&op=keys&sub=narrowdown&hid=$hid&rid=$rid&key=$key' method='POST'>");
			addnav("","runmodule.php?module=improbablehousing&op=keys&sub=narrowdown&hid=$hid&rid=$rid&key=$key");
			rawoutput("<input name='name' id='name'>");
			rawoutput("<input type='submit' class='button' value='$search'>");
			rawoutput("</form>");
		}else{
			output("These people matched your search:`n");
			$name = translate_inline("Player");
			rawoutput("<table cellpadding='3' cellspacing='0' border='0'>");
			rawoutput("<tr class='trhead'><td>$name</td></tr>");
			for ($i=0;$i<db_num_rows($result);$i++){
				$row = db_fetch_assoc($result);
				$acctid = $row['acctid'];
				rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td><a href='runmodule.php?module=improbablehousing&op=keys&sub=confirm&hid=$hid&rid=$rid&key=$key&recipient=$acctid'>");
				output_notl("%s", $row['name']);
				rawoutput("</a></td></tr>");
				addnav("","runmodule.php?module=improbablehousing&op=keys&sub=confirm&hid=$hid&rid=$rid&key=$key&recipient=$acctid");
			}
			rawoutput("</table><br />");
		}
		addnav("Cancel");
		addnav("Back to the Dwelling","runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=$rid");
	break;
	case "confirm":
		$recipient = httpget('recipient');
		output("Okay, we've selected a player.`n`n");
		$sql = "SELECT name,sex FROM ".db_prefix("accounts")." WHERE acctid=$recipient";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$player = $row['name']."`0";
		if (!$row['sex']){
			$g1 = "him";
			$g2 = "he";
		} else {
			$g1 = "her";
			$g2 = "she";
		}
		switch ($key){
			case "frontdoor":
				output("You are cutting a `bFront Door`b key for the player named `b\"%s.\"`b  This key will allow %s to enter this Dwelling when it is locked.  It will not allow %s to enter any locked rooms, but you can cut keys for those rooms individually if you like.  If %s already has a Master Key, then you don't need to cut %s a Front Door key.`n`n",$player,$player,$g1,$player,$g1);
				$cost = $key5cost;
			break;
			case "access":
				output("You are cutting an `bAccess`b key for the room called \"%s`0,\" for the player named `b\"%s.\"`b  This key will allow %s to enter this room when it is locked, unless you've deadbolted this room.  It will not allow %s to lock or unlock this room - if %s enters a locked room, the door will lock automatically behind %s.  If you want %s to be able to lock and unlock rooms, you'll need a `bControl`b Key.  If %s already has a `bControl`b key or a `bDeadbolt`b key to this room, or a `bMaster`b key, then `byou don't need to give %s an Access key`b - %s can already enter this room when it's locked.`n`n",$house['data']['rooms'][$rid]['name'],$player,$player,$g1,$g2,$g1,$g1,$g2,$g1,$g2);
				$cost = $key10cost;
			break;
			case "control":
				output("You are cutting a `bControl`b key for the room called \"%s`0,\" for the player named `b\"%s.\"`b  This key will allow %s to enter this room, lock it, or unlock it, unless you've deadbolted the room.  This key will not allow %s to enter, lock or unlock this room when it's deadbolted.  If you want %s to be able to enter or control a deadbolted room, you'll need a `bDeadbolt`b Key.  If %s already has a `bDeadbolt`b key to this room or a `bMaster`b key, then `byou don't need to give %s a Control key`b - %s can already lock and unlock this room.`n`n",$house['data']['rooms'][$rid]['name'],$player,$player,$player,$player,$g2,$g1,$g2);
				$cost = $key20cost;
			break;
			case "deadbolt":
				output("You are cutting a `bDeadbolt`b key for the room called \"%s`0,\" for the player named `b\"%s.\"`b  This key will allow %s to enter this room under any circumstances, no matter what combination of locks are present on the room.  It will also allow %s to lock and unlock both the main and deadbolt locks on this room.  If %s already has a `bMaster`b key, then `byou don't need to give %s a Deadbolt key`b - %s can already enter this room (and any other room) whenever %s pleases.`n`n",$house['data']['rooms'][$rid]['name'],$player,$player,$g1,$player,$g1,$g2,$g2);
				$cost = $key30cost;
			break;
			case "master":
				output("`c`b`4WARNING`0`b`nYou have selected to cut a Master Key.  If you go ahead with this, then `b%s`b will be able to perform every single action that you've been able to perform on this Dwelling so far.`cIt'll be pretty much as though %s owns this Dwelling; %s'll be able to create new rooms, decorate, give out keys, kick out sleepers, and `beverything else that you can do.`b  The only thing %s won't be able to do is give out Master Keys.  If for some reason you disappear from the Island, and the moderators have to figure out what to do with your Dwelling, then the people to whom you give Master Keys might influence the moderators' decision.`c`b`4EXTRA SPECIAL WARNING`b`cIf you give a Master Key to someone and they use it to wreak havoc on your dwelling, `bneither the mods nor the admin will be able to put it back to the way it was before`b.  Likewise, once a Master Key has been given, not even a pack of wild Panzthers will be able to drag it back.  `b`4Please be absolutely, positively sure that this is what you want to do.`b`0`n`n",$player,$player,$g2,$g2);
				$cost = $key100cost;
			break;
		}
		output("You've read the warning, you know what you're doing.  You know that `bkeys can't be taken back`b and that this action can't be undone without changing the locks.  You know that doing this will cost you `b%s cigarettes`b.  You know that players who are already inside unlocked rooms can stay inside those rooms once they're locked (but that if they leave, they won't be able to get back in without the proper key).`n`nTo confirm this action, tick both the checkboxes below and hit \"Do it!\"`n`n`bFINAL CONFIRMATION / NO UNDO / ASK THE MODS TO CHANGE IT AFTERWARDS AND WE'LL JUST POINT AND LAUGH`b`n",$cost);
		rawoutput("<form action='runmodule.php?module=improbablehousing&op=keys&sub=finalconfirm&hid=$hid&rid=$rid&key=$key&recipient=$recipient&cost=$cost' method='POST'>");
		addnav("","runmodule.php?module=improbablehousing&op=keys&sub=finalconfirm&hid=$hid&rid=$rid&key=$key&recipient=$recipient&cost=$cost");
		rawoutput("<input type='checkbox' value='confirm1' name='confirm1'>");
		rawoutput("<input type='checkbox' value='confirm2' name='confirm2'>");
		rawoutput("<input type='submit' class='button' value='Do it!'>");
		rawoutput("</form>");
		addnav("Cancel");
		addnav("Back to the Dwelling","runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=$rid");
	break;
	case "finalconfirm":
		$cost = httpget('cost');
		$recipient = httpget('recipient');
		$confirm1 = httppost('confirm1');
		$confirm2 = httppost('confirm2');
		if ($confirm1 && $confirm2){
			//apply the change, take the money
			switch ($key){
				case "frontdoor":
					$house['data']['frontdoorkeys'][$recipient]=1;
					$session['user']['gems']-=$key5cost;
				break;
				case "access":
					$house['data']['rooms'][$rid]['keys'][$recipient]=10;
					$session['user']['gems']-=$key10cost;
				break;
				case "control":
					$house['data']['rooms'][$rid]['keys'][$recipient]=20;
					$session['user']['gems']-=$key20cost;
				break;
				case "deadbolt":
					$house['data']['rooms'][$rid]['keys'][$recipient]=30;
					$session['user']['gems']-=$key30cost;
				break;
				case "master":
					$house['data']['masterkeys'][$recipient]=1;
					$session['user']['gems']-=$key100cost;
				break;
			}
			output("The key was sent!  We didn't want to spoil the surprise, so we haven't notified the player yet.  You can tell them yourself if you like via Distraction, or we can send them an automated message.  Or you could just wait until they stumble into your Dwelling and notice for themselves.`n`n");
			addnav("Send a message?");
			addnav("Sure!","runmodule.php?module=improbablehousing&op=keys&sub=notify&hid=$hid&rid=$rid&key=$key&recipient=$recipient");
			addnav("No, just go back to the Dwelling","runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=$rid");
			improbablehousing_sethousedata($house);
		} else {
			output("You didn't tick both boxes.  Let's try that again.`n`n");
			$sql = "SELECT name,sex FROM ".db_prefix("accounts")." WHERE acctid=$recipient";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			$player = $row['name']."`0";
			if (!$row['sex']){
				$g1 = "him";
				$g2 = "he";
			} else {
				$g1 = "her";
				$g2 = "she";
			}
			switch ($key){
				case "frontdoor":
					output("You are cutting a `bFront Door`b key for the player named `b\"%s.\"`b  This key will allow %s to enter this Dwelling when it is locked.  It will not allow %s to enter any locked rooms, but you can cut keys for those rooms individually if you like.  If %s already has a Master Key, then you don't need to cut %s a Front Door key.`n`n",$player,$player,$g1,$player,$g1);
					$cost = $key5cost;
				break;
				case "access":
					output("You are cutting an `bAccess`b key for the room called \"%s`0,\" for the player named `b\"%s.\"`b  This key will allow %s to enter this room when it is locked, unless you've deadbolted this room.  It will not allow %s to lock or unlock this room - if %s enters a locked room, the door will lock automatically behind %s.  If you want %s to be able to lock and unlock rooms, you'll need a `bControl`b Key.  If %s already has a `bControl`b key or a `bDeadbolt`b key to this room, or a `bMaster`b key, then `byou don't need to give %s an Access key`b - %s can already enter this room when it's locked.`n`n",$house['data']['rooms'][$rid]['name'],$player,$player,$g1,$g2,$g1,$g1,$g1,$g1,$g2);
					$cost = $key10cost;
				break;
				case "control":
					output("You are cutting a `bControl`b key for the room called \"%s`0,\" for the player named `b\"%s.\"`b  This key will allow %s to enter this room, lock it, or unlock it, unless you've deadbolted the room.  This key will not allow %s to enter, lock or unlock this room when it's deadbolted.  If you want %s to be able to enter or control a deadbolted room, you'll need a `bDeadbolt`b Key.  If %s already has a `bDeadbolt`b key to this room or a `bMaster`b key, then `byou don't need to give %s a Control key`b - %s can already lock and unlock this room.`n`n",$house['data']['rooms'][$rid]['name'],$player,$player,$player,$player,$g2,$g1,$g2);
					$cost = $key20cost;
				break;
				case "deadbolt":
					output("You are cutting a `bDeadbolt`b key for the room called \"%s`0,\" for the player named `b\"%s.\"`b  This key will allow %s to enter this room under any circumstances, no matter what combination of locks are present on the room.  It will also allow %s to lock and unlock both the main and deadbolt locks on this room.  If %s already has a `bMaster`b key, then `byou don't need to give %s a Deadbolt key`b - %s can already enter this room (and any other room) whenever %s pleases.`n`n",$house['data']['rooms'][$rid]['name'],$player,$player,$g1,$player,$g1,$g2,$g2);
					$cost = $key30cost;
				break;
				case "master":
					output("`c`b`4WARNING`0`b`nYou have selected to cut a Master Key.  If you go ahead with this, then `b%s`b will be able to perform every single action that you've been able to perform on this Dwelling so far.`cIt'll be pretty much as though %s owns this Dwelling; %s'll be able to create new rooms, decorate, give out keys, kick out sleepers, and `beverything else that you can do.`b  The only thing %s won't be able to do is give out Master Keys.  If for some reason you disappear from the Island, and the moderators have to figure out what to do with your Dwelling, then the people to whom you give Master Keys might influence the moderators' decision.`c`b`4EXTRA SPECIAL WARNING`b`cIf you give a Master Key to someone and they use it to wreak havoc on your dwelling, `bneither the mods nor the admin will be able to put it back to the way it was before`b.  Likewise, once a Master Key has been given, not even a pack of wild Panzthers will be able to drag it back.  `b`4Please be absolutely, positively sure that this is what you want to do.`b`0`n`n",$player,$player,$g2,$g2);
					$cost = $key100cost;
				break;
			}
			output("You've read the warning, you know what you're doing.  You know that `bkeys can't be taken back`b and that this action can't be undone without changing the locks.  You know that doing this will cost you `b%s cigarettes`b.  You know that players who are already inside unlocked rooms can stay inside those rooms once they're locked (but that if they leave, they won't be able to get back in without the proper key).`n`nTo confirm this action, tick both the checkboxes below and hit \"Do it!\"`n`n`bFINAL CONFIRMATION / NO UNDO / ASK THE MODS TO CHANGE IT AFTERWARDS AND WE'LL JUST POINT AND LAUGH`b`n",$cost);
			rawoutput("<form action='runmodule.php?module=improbablehousing&op=keys&sub=finalconfirm&hid=$hid&rid=$rid&key=$key&recipient=$recipient&cost=$cost' method='POST'>");
			addnav("","runmodule.php?module=improbablehousing&op=keys&sub=finalconfirm&hid=$hid&rid=$rid&key=$key&recipient=$recipient&cost=$cost");
			rawoutput("<input type='checkbox' value='confirm1' name='confirm1'>");
			rawoutput("<input type='checkbox' value='confirm2' name='confirm2'>");
			rawoutput("<input type='submit' class='button' value='Do it!'>");
			rawoutput("</form>");
			addnav("Cancel");
			addnav("Back to the Dwelling","runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=$rid");
		}
	break;
	case "notify":
		$recipient = httpget('recipient');
		require_once "lib/systemmail.php";
		$subj = "You have a Key!";
		switch ($key){
			case "frontdoor":
				$body = "`0You have a front door key for ".$house['data']['name']."!  You can now enter this Dwelling even when it's locked.  Have fun with that!";
			break;
			case "access":
				$body = "`0You have an Access Key for ".$house['data']['rooms'][$rid]['name']." at ".$house['data']['name']."!  You can now enter this room even when it's locked.  Have fun with that!";
			break;
			case "control":
				$body = "`0You have a Control Key for ".$house['data']['rooms'][$rid]['name']." at ".$house['data']['name']."!  You can now lock and unlock this room.  Have fun with that!";
			break;
			case "deadbolt":
				$body = "`0You have a Deadbolt Key for ".$house['data']['rooms'][$rid]['name']." at ".$house['data']['name']."!  You can now lock and unlock this room even when it's deadbolted.  Have fun with that!";
			break;
			case "master":
				$body = "`0You have a Master Key for ".$house['data']['name']."!  You can now control most of this Dwelling's operations!  Have fun with that!";
			break;
		}
		systemmail($recipient,$subj,$body);
		output("Okay, the player's been notified.  Have fun!");
		addnav("All done!");
		addnav("Back to the Dwelling","runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=$rid");
	break;
}

page_footer();

?>