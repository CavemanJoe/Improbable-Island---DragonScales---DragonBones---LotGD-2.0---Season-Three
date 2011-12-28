<?php

global $session;
page_header("Something Terrible!");

require_once "modules/improbablehousing/lib/lib.php";
$hid = httpget('hid');
$house = improbablehousing_gethousedata($hid);
debug($house);
$rid = httpget('rid');

switch (httpget('sub')){
	case "unzip":
		output("`b`4ERROR:`b CANNOT RELIABLY SERIALIZE DATA`4`0`nThis Dwelling has triggered the error reporting system.  Before writing data back to the database, we tested to see whether this data could be retrieved properly - it could not.  Please copy-paste this message into a Petition, along with the data below, telling us what you did just before you saw this page - if writing a description for a decorating job, please include a copy-paste of the full description.  Thank you.`n`n");
	break;
	case "nodata":
		output("`b`4ERROR:`b NO DATA`4`0`nThis Dwelling has triggered the error reporting system because a matching Dwelling ID could not be found.  Please copy-paste this message into a Petition, telling us what you did just before you saw this page.  Thank you.`n`n");
	break;
	case "failsafe":
		output("`b`4ERROR:`b DWELLING TRIGGERED FAILSAFE`4`0`nThis Dwelling triggered the error reporting system.  Each time Dwelling data is written to the database, it's re-read back again as a failsafe measure - and in this case, the data that went in wasn't the same as the data that came out.  The admin has been automatically made aware of this issue, and will be investigating shortly.  The old data has been E-mailed to the admin and we may, with luck, be able to retrieve the Dwelling.  Thank you.`n`n");
	break;
	case "overflow":
		output("CavemanJoe, author of the Improbable Dwellings module, never thought you'd see this page.  Looks like he was wrong.`n`nWhat just happened is that the serialized array which stores your house has grown so large that it wouldn't fit in a MySQL TEXT column.`n`nWhatever you just tried to do is lost, be it a build job, adding a person to a sleeping space, or writing a beautifully long description.  That sucks, but if we'd have tried to finalise the changes, MySQL would have quietly truncated the data and your entire house would have simply disappeared.  Which is worse.`n`nThis page exists as a just-in-case failsafe - what's just happened to you should never have happened, but since the consequences of it happening were so very very dire, CMJ thought it'd be a good idea to check for it and redirect you to this page if it looked like we were about to do something irreversible.`n`nPlease send a Petition telling the administration that you've ended up at this page, quoting error code `b`4OVERFLOW`b`0 and copy-paste the houseID that appears below - they'll know what to do from there.");
	break;
	case "manualfix":
		output("This dwelling has triggered the error reporting system.  There's something funky going on with this Dwelling, and CavemanJoe needs to take a look at it manually.  If you're the owner of this dwelling, please Petition to get this sorted, quoting error code `b`4MANUALFIX`0`b and the houseID below.  Please also tell us what you were doing just before this page appeared.`n`n");
	break;
	case "enterfrom":
		if ($rid==1){
			redirect("runmodule.php?module=improbablehousing&op=error&sub=fixenterfrom&hid=".$hid."&rid=1&fixto=0");
		}
		if ($house['ownedby']==$session['user']['acctid']){
			output("`b`4ERROR`0`b: now checking to fix room '`b%s`b`0'`n`n",$house['data']['rooms'][$rid]['name']);
			output("This dwelling has triggered the error reporting system.  Due to a bug in an early version of the Dwellings engine, the rooms in this Dwelling aren't set up properly.  Because you are the owner, we might be able to fix this bug now, if you'd like to tell us some information.`n`n`bHow this happened`b`nIn Dwellings, the capability exists for rooms to branch from other rooms.  This is accomplished by allocating the 'enterfrom' variable to each individual room - this variable references the key of another room in the Dwelling array.  For example, the entrance hall of your House will have a roomID of 0, and a staircase might have a roomID of 1 and an 'enterfrom' value of 0 - in other words, you can enter the staircase from the entrance hall.  Further to this, to put a bedroom at the top of the stairs, you would use the 'Create a New Room' link from the staircase page - the new bedroom would then be given a key of 2 (the third room built - we start from zero) and an enterfrom value of 1 (the staircase's roomID was 1).  And so on and so forth.`n`n`bWhat to do`b`nYou're seeing an error because the 'enterfrom' variable was not set in the room called '`b%s`0`b.'  We can solve this error now, if you'd like - simply tell us which room this room should lead from.`n`n`bIF YOU ARE TAKEN BACK TO THIS PAGE AGAIN FROM THE SAME ROOM, or IF FIXING DOESN'T WORK`b`nPlease use the Petition link and include the information below, quoting error code ENTERFROM (TRIGGER ID %s), and we'll sort the Dwelling out manually.",$house['data']['rooms'][$rid]['name'],$rid);
			addnav("WARNING - don't click these links without checking the text on the right!");
			addnav(array("Now healing room `b%s`b",$house['data']['rooms'][$rid]['name']),"");
			addnav("This room should be accessible from:");
			foreach($house['data']['rooms'] AS $rkey=>$rvals){
				if ($rkey!=$rid){
					addnav(array("%s",$rvals['name']),"runmodule.php?module=improbablehousing&op=error&sub=fixenterfrom&hid=".$hid."&rid=".$rid."&fixto=".$rkey);
				}
			}
		} else {
			output_notl("This dwelling has triggered the error reporting system, and the owner must sort out which rooms lead off from which.  If you know the owner, you might want to let them know - but bear in mind that a lot of other people might also be trying to let them know too.");
		}
	break;
	case "fixenterfrom":
		$fixto = httpget('fixto');
		$house['data']['rooms'][$rid]['enterfrom']=$fixto;
		$house['data']['skiperror']=1;
		improbablehousing_sethousedata($house);
		redirect("runmodule.php?module=improbablehousing&op=interior&hid=".$hid);
	break;
}

output("`bHouse ID %s, owned by acctid %s, location %s`b`n`n",$house['id'],$house['ownedby'],$house['location']);
if (($house['ownedby']==$session['user']['acctid']) || $session['user']['acctid']==1){
	output("Because you are the owner of this house, we're showing you the serialized data that comprises it.  You may be here because this data cannot be reliably unserialized.  Here's the data, for your records.  `bYou might need this.  This might be important.`b  For example, you can probably still make out your room descriptions.`n`nIt'd be a `breally good idea`b to copy this data into a plain-text document (IE one ending with the file extension .txt - FOR THE LOVE OF ALL THAT IS GOOD AND PURE IN THIS FRAGILE WORLD OF OURS `b`4DON'T USE A WORD PROCESSOR`0`b!), or to just copy-paste it into an E-mail and send it to yourself.  Don't send this data to the admins, they can see it any time they like - but do keep a copy somewhere safe.`n`n");
	$sql = "SELECT ownedby,data,location FROM " . db_prefix("improbabledwellings") . " WHERE id = '$hid'";
	$result = db_query($sql);
	$row=db_fetch_assoc($result);
	$data=$row['data'];
	rawoutput("<textarea rows='10' cols='50'>");
	rawoutput($data);
	rawoutput("</textarea>");
	output("`n`n`bNUCLEAR OPTION`b`nIf you have been advised that your Dwelling is irrecovable, you can reboot it to an empty plot by using this link.  Ask CMJ for a Dwelling Recovery Item before you do this.  `4MAKE ABSOLUTELY CERTAIN THAT YOU HAVE BACKED UP THE DATA IN THE TEXTAREA ABOVE `bBEFORE`b YOU CLICK THIS LINK.  If you don't, then you'll never be able to rebuild your Dwelling the way it was.`0`n`n");
	rawoutput("<a href=\"runmodule.php?module=improbablehousing&op=reboot&hid=$hid\">Reboot this Dwelling</a>");
	addnav("","runmodule.php?module=improbablehousing&op=reboot&hid=$hid");
}
debug($house);

addnav("Exit");
addnav("Back to the World Map","runmodule.php?module=worldmapen&op=continue");
page_footer();

?>