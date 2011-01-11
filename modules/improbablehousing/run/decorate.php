<?php

$hid = httpget('hid');
$rid = httpget('rid');

require_once "modules/improbablehousing/lib/lib.php";
require_once "modules/staminasystem/lib/lib.php";
$house=improbablehousing_gethousedata($hid);
$room=$house['data']['rooms'][$rid];

page_header("Painting and Decorating");

if (mass_suspend_stamina_buffs("wlimit")){
	$restorebuffs = true;
}

switch(httpget('subop')){
	case "extdesc":
		$extdesc = $house['data']['desc_exterior'];
		output("`0On Improbable Island, a dab of paint here and there can make the difference between a shack and a mansion.`n`nRedecorating the outside of your dwelling will allow you to change the name and external description.  The longer the description, the more Stamina you'll have to use - but changing the name is free.`n`nThe current description for your dwelling's exterior is:`n`n%s`n`nYou can change this via the box below.  Sorry, no bold, italics or new lines in external descriptions.  Also please keep it clean, or at least cleanish.  Remember to use the format \"You see (your description),\" or \"To the north, (your description) can be observed towering into the sky,\" or similar language that demonstrates what's around the player.`n`n",$extdesc);
		rawoutput("<form action='runmodule.php?module=improbablehousing&op=decorate&subop=extdesc_change&hid=$hid' method='POST'>Dwelling name: <input name='name' id='name' value=\"".$house['data']['name']."\"><br />Dwelling description:<br /><textarea name='newdesc' id='newdesc' rows='6' cols='60'>".$extdesc."</textarea><input type=submit>");
		addnav("","runmodule.php?module=improbablehousing&op=decorate&subop=extdesc_change&hid=$hid");
	break;
	case "extdesc_change":
		$newdesc = stripslashes(httppost('newdesc'));
		$newname = stripslashes(httppost('name'));
		if (strlen($newname)<55 && strlen($newdesc)>1 && !strpos($newname,"`") && !strpos($newdesc,"`n") && !strpos($newdesc,"`c") && !strpos($newdesc,"`b") && !strpos($newdesc,"`i") && !strpos($newdesc,"`h")){
			output("`0`bThe name of this dwelling has been changed`b to %s`0.  Here is the new description, which `bhasn't been changed`b yet.  Check it carefully!  This will be displayed to other players looking around on the Island Map.`n`n---Begin---`n%s`n`0---End---`n`n",$newname,$newdesc);
			$iterations = ceil(strlen($newdesc)/5);
			output("To make this your new exterior description, you'll have to perform the Decorating action `b%s`b times (your friends can help too).  Any currently-active decorating jobs on the exterior description will be cancelled.`n`n",$iterations);
			addnav("Change the external description?");
			addnav("No, I just wanted to change the name","runmodule.php?module=improbablehousing&op=decorate&subop=extdesc&hid=$hid");
			addnav("Yes, set up a new Decorating job","runmodule.php?module=improbablehousing&op=decorate&subop=extdesc_confirm&hid=$hid");
			$house['data']['name']=$newname;
			$house['data']['proposed_extdesc']['desc']=$newdesc;
			$house['data']['proposed_extdesc']['req']=$iterations;
			improbablehousing_sethousedata($house);
		} else {
			output("The name of this dwelling is too long or contains colour codes (or the description contains new lines, bold, or italics).  You've got 55 characters to play with, there's no formatting allowed in the name, and no line breaks, bold or italics allowed in the external description.  Let's give that another go.`n`n");
			rawoutput("<form action='runmodule.php?module=improbablehousing&op=decorate&subop=extdesc_change&hid=$hid' method='POST'>Dwelling name: <input name='name' id='name' value=\"".$house['data']['name']."\"><br />Dwelling description:<br /><textarea name='newdesc' id='newdesc' rows='6' cols='60'>".$extdesc."</textarea><input type=submit>");
		addnav("","runmodule.php?module=improbablehousing&op=decorate&subop=extdesc_change&hid=$hid");
		}
	break;
	case "extdesc_confirm":
		output("Okay!  Your new decoration description is set.  Now you'd better get decorating!`n`n");
		$house['data']['dec']=$house['data']['proposed_extdesc'];
		unset($house['data']['proposed_extdesc']);
		$house['data']['dec']['done']=0;
		improbablehousing_sethousedata($house);
	break;
	case "decorate_exterior":
		if ($restorebuffs){
			output("`0Correctly figuring that your backpack and bandolier would only slow you down, you shrug them off and place them off to one side.  It's not like anyone's gonna nick them in here.`n`n");
		}
		$actinfo=process_action("Decorating");
		if ($actinfo['lvlinfo']['levelledup']){
			output("`n`c`b`0You gained a level in Decorating!  You are now level %s!  This action will cost fewer Stamina points now.`b`c`n",$actinfo['lvlinfo']['newlvl']);
		}
		$house['data']['dec']['done']+=1;
		//check if the job's done
		if ($house['data']['dec']['done']==$house['data']['dec']['req']){
			output("The decorating job is completed!  You step back and admire your work.  Pretty nice!`n`n");
			$house['data']['desc_exterior']=$house['data']['dec']['desc'];
			unset($house['data']['dec']);
			$erasedec = "DELETE FROM ".db_prefix("building_prefs")." WHERE pref = 'dec' AND hid = '$hid'";
			db_query($erasedec);
			$loc = $house['location'];
			invalidatedatacache("housing/housing_location_".$loc);
		} else {
			output("You set about painting and decorating.  Before too long, the dwelling is one step closer to being fully decorated.`n`n");
			require_once "lib/bars.php";
			$bar = fadebar($house['data']['dec']['done'],$house['data']['dec']['req'],150,5);
			rawoutput("Decoration completion status: ".$bar);
			addnav("Decorate some more?");
			if (get_stamina()==100){
				$cost = stamina_getdisplaycost("Decorating");
				addnav(array("Decorate the dwelling's exterior (`Q%s%%`0)",$cost),"runmodule.php?module=improbablehousing&op=decorate&subop=decorate_exterior&hid=$hid");
			} else {
				addnav("You're too tired to do any more sprucing up right now.","");
			}
		}
		improbablehousing_sethousedata($house);
	break;
	case "start":
		output("`0On Improbable Island, a dab of paint here and there can make the difference between a shack and a mansion.`n`nDecorating a room will allow you to change its internal description.  The longer the description, the more you'll have to decorate - but like with building, your friends can help you out.  You can also change the name of the room, at a maximum of 55 characters.  Changing the name of a room doesn't have any Stamina or materials cost - after all, you can very easily call a room a study when it's clearly a kitchen.  It's just that everyone will point and laugh at you.`n`nThe current description for this room is:`n`n%s`0`n`nYou can change this description via the box below.  Use ``n for a new line, ``b for bold, ``i for italics.  Make sure to close your bold and italics with another ``b or ``i (for example, ``ithis text``i is italicised, and ``bthis text``b is bold).`n`n`c`b`4WARNING: NEVER USE A WORD PROCESSOR`b`nWord processors insert \"helpful\" special characters - they change quotation marks so that they lean to the right or left, they convert ...'s into single-character ellipses, they turn hyphens into unparseable garbage and in general they just bugger everything up in order to make it look pretty.  These shenanigans can sometimes cause Horrible Problems when trying to retrieve your house's data, and they make matters really complicated whenever we try to restore data from a backup.  Type your description either into a plain-text editor or straight into this box, and use an online spellchecker.  `iDon't use a word processor.`i  Word processors and the Internet do not mix.`c`0`n`n",$room['desc']);
		//show form to change description
		$roomname=$room['name'];
		rawoutput("<form action='runmodule.php?module=improbablehousing&op=decorate&subop=change&hid=$hid&rid=$rid' method='POST'><input name='name' id='name' value='$roomname'><br /><textarea rows='6' cols='60' name='newdesc' id='newdesc'>".$room['desc']."</textarea><input type=submit>");
		addnav("","runmodule.php?module=improbablehousing&op=decorate&subop=change&hid=$hid&rid=$rid");
	break;
	case "change":
		$newdesc = stripslashes(httppost('newdesc'));
		$newname = stripslashes(httppost('name'));
		if (strlen($newname)<55 && strlen($newdesc)>1){
			output("`0`bThe name of this room has been changed`b to %s`0.  Here is the new description, which `bhasn't been changed`b yet.  Check it carefully!  This will be displayed to other players entering your dwelling.`n`n---Begin---`n%s`n`0---End---`n`n",$newname,$newdesc);
			$iterations = ceil(strlen($newdesc)/20);
			output("To make this your new room description, you'll have to perform the Decorating action `b%s`b times (your friends can help too).  Any currently-active decorating jobs on this room will be cancelled.`n`n",$iterations);
			addnav("Change the room description?");
			addnav("No, I just wanted to change the name","runmodule.php?module=improbablehousing&op=decorate&subop=start&hid=$hid&rid=$rid");
			addnav("Yes, set up a new Decorating job to change this room's description","runmodule.php?module=improbablehousing&op=decorate&subop=confirm&hid=$hid&rid=$rid");
			$house['data']['rooms'][$rid]['name']=$newname;
			$house['data']['rooms'][$rid]['proposed_dec']['desc']=$newdesc;
			$house['data']['rooms'][$rid]['proposed_dec']['req']=$iterations;
			improbablehousing_sethousedata($house);
		} else {
			output("The name of your room is too long.  You've got 55 characters to play with, including colour codes.  Let's give that another go.`n`n");
			rawoutput("<form action='runmodule.php?module=improbablehousing&op=decorate&subop=change&hid=$hid&rid=$rid' method='POST'><input name='name' id='name' value=\"$newname\"><br /><textarea rows='6' cols='60' name='newdesc' id='newdesc'>$newdesc</textarea><input type=submit>");
			addnav("","runmodule.php?module=improbablehousing&op=decorate&subop=change&hid=$hid&rid=$rid");
		}
	break;
	case "confirm":
		output("Okay!  Your new decoration description is set.  Now you'd better get decorating!`n`n");
		$house['data']['rooms'][$rid]['dec']=$house['data']['rooms'][$rid]['proposed_dec'];
		unset($house['data']['rooms'][$rid]['proposed_dec']);
		$house['data']['rooms'][$rid]['dec']['done']=0;
		improbablehousing_sethousedata($house);
	break;
	case "decorate":
		if ($restorebuffs){
			output("`0Correctly figuring that your backpack and bandolier would only slow you down, you shrug them off and place them off to one side.  It's not like anyone's gonna nick them in here.`n`n");
		}
		$actinfo=process_action("Decorating");
		if ($actinfo['lvlinfo']['levelledup']){
			output("`n`c`b`0You gained a level in Decorating!  You are now level %s!  This action will cost fewer Stamina points now.`b`c`n",$actinfo['lvlinfo']['newlvl']);
		}
		$house['data']['rooms'][$rid]['dec']['done']+=1;
		//check if the job's done
		if ($house['data']['rooms'][$rid]['dec']['done']==$house['data']['rooms'][$rid]['dec']['req']){
			output("The decorating job is completed!  You step back and admire your work.  Pretty nice!`n`n");
			$house['data']['rooms'][$rid]['desc']=$house['data']['rooms'][$rid]['dec']['desc'];
			unset($house['data']['rooms'][$rid]['dec']);
			$eraseroomdec = "DELETE FROM ".db_prefix("room_prefs")." WHERE pref = 'dec' AND hid = '$hid' AND rid = '$rid'";
			db_query($eraseroomdec);
		} else {
			output("You set about painting and decorating.  Before too long, the room is one step closer to being fully decorated.`n`n");
			require_once "lib/bars.php";
			$bar = fadebar($house['data']['rooms'][$rid]['dec']['done'],$house['data']['rooms'][$rid]['dec']['req'],150,5);
			rawoutput("Decoration completion status: ".$bar);
			addnav("Decorate some more?");
			if (get_stamina()==100){
				$cost = stamina_getdisplaycost("Decorating");
				addnav(array("Decorate some more (`Q%s%%`0)",$cost),"runmodule.php?module=improbablehousing&op=decorate&subop=decorate&hid=$hid&rid=$rid");
			} else {
				addnav("You're too tired to do any more sprucing up right now.","");
			}
		}
		improbablehousing_sethousedata($house);
	break;
}

if ($restorebuffs){
	restore_all_stamina_buffs();
}

improbablehousing_bottomnavs($house);
page_footer();

?>