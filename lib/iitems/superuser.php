<?php

/*
=======================================================
SUPERUSER EDITOR
=======================================================
*/

// function iitems_superuser_item_menu(){
	// output("Select an item to edit, or create a new item.`n");
	// massinvalidate("iitems");
	// $sql = "SELECT id,localname,data FROM " . db_prefix("iitems");
	// $result = db_query($sql);
	// for ($i=0;$i<db_num_rows($result);$i++){
		// $row=db_fetch_assoc($result);
		// $item = unserialize($row['data']);
		// if (isset($item['verbosename'])){
			// rawoutput("<a href=\"runmodule.php?module=iitems&op=superuser&superop=edit&id=".$row['id']."\">".$item['verbosename']." (".$row['localname'].")</a> (<a href=\"runmodule.php?module=iitems&op=superuser&superop=give&id=".$row['localname']."\">Give</a>) (<a href=\"runmodule.php?module=iitems&op=superuser&superop=delete&id=".$row['id']."\">Delete</a>) (<a href=\"runmodule.php?module=iitems&op=superuser&superop=copy&id=".$row['id']."\">Copy</a>)<br />");
		// } else {
			// rawoutput("<a href=\"runmodule.php?module=iitems&op=superuser&superop=edit&id=".$row['id']."\">UnNamed Item, itemid ".$row['localname']."</a> (<a href=\"runmodule.php?module=iitems&op=superuser&superop=give&id=".$row['localname']."\">Give</a>) (<a href=\"runmodule.php?module=iitems&op=superuser&superop=delete&id=".$row['id']."\">Delete</a>) (<a href=\"runmodule.php?module=iitems&op=superuser&superop=copy&id=".$row['id']."\">Copy</a>)<br />");
		// }
		// addnav("","runmodule.php?module=iitems&op=superuser&superop=edit&id=".$row['id']);
		// addnav("","runmodule.php?module=iitems&op=superuser&superop=delete&id=".$row['id']);
		// addnav("","runmodule.php?module=iitems&op=superuser&superop=copy&id=".$row['id']);
		// addnav("","runmodule.php?module=iitems&op=superuser&superop=give&id=".$row['localname']);
	// }
// }

function iitems_superuser_item_menu(){
	output("Select an item to edit, or create a new item.`n");
	massinvalidate("iitems");
	$sql = "SELECT id,localname,data FROM " . db_prefix("iitems");
	$result = db_query($sql);
	$allitems = array();
	$allkeys = array();
	$colcount = 0;
	for ($i=0;$i<db_num_rows($result);$i++){
		$row=db_fetch_assoc($result);
		$item = unserialize($row['data']);
		$item['id'] = $row['id'];
		$allitems[$row['localname']]=$item;
		if (count($item) > $colcount){
			$colcount = count($item);
		}
		foreach($item AS $key => $val){
			$allkeys[$key]=1;
		}
	}
	rawoutput("<table width=100% cellpadding=1 cellspacing=1 border=1><tr><td>Actions</td>");
	foreach($allkeys AS $key=>$val){
		rawoutput("<td>".$key."</td>");
	}
	rawoutput("</tr>");
	
	foreach ($allitems AS $itemid => $vals){
		
		rawoutput("<tr><td><a href=\"runmodule.php?module=iitems&op=superuser&superop=edit&id=".$vals['id']."\">".$vals['verbosename']." (".$itemid.")</a> (<a href=\"runmodule.php?module=iitems&op=superuser&superop=give&id=".$itemid."\">Give</a>) (<a href=\"runmodule.php?module=iitems&op=superuser&superop=delete&id=".$vals['id']."\">Delete</a>) (<a href=\"runmodule.php?module=iitems&op=superuser&superop=copy&id=".$vals['id']."\">Copy</a>)<br />");
		addnav("","runmodule.php?module=iitems&op=superuser&superop=edit&id=".$vals['id']);
		addnav("","runmodule.php?module=iitems&op=superuser&superop=delete&id=".$vals['id']);
		addnav("","runmodule.php?module=iitems&op=superuser&superop=copy&id=".$vals['id']);
		addnav("","runmodule.php?module=iitems&op=superuser&superop=give&id=".$itemid);
		foreach ($allkeys AS $key => $val){
			rawoutput("<td>");
			if ($key == "image"){
				rawoutput("<img src=\"images/iitems/".$vals['image']."\">");
			} else {
				output_notl("%s",$allitems[$itemid][$key]);
			}
			rawoutput("</td>");
		}
		rawoutput("</tr>");
	}
	rawoutput("</table>");
}

function iitems_superuser(){
	global $session;
	$superop = httpget('superop');
	switch($superop){
		case "export":
			page_header("Export an IItem");
			$itemid = httpget("exportitemid");
			$sql = "SELECT id,localname,data FROM " . db_prefix("iitems") . " WHERE localname = '$itemid'";
			$result = db_query_cached($sql,"iitems-".$localname);
			$row=db_fetch_assoc($result);
			$exportcode = $localname."|BREAK|".$data;
			rawoutput("<textarea>");
			rawoutput($exportcode);
			rawoutput("</textarea>");
			addnav("Back to the IItem Editor","runmodule.php?module=iitems&op=superuser&superop=start");
		break;
		case "import":
			page_header("Import an IItem");
			$itemid = httpget("exportitemid");
			$sql = "SELECT id,localname,data FROM " . db_prefix("iitems") . " WHERE localname = '$itemid'";
			$result = db_query_cached($sql,"iitems-".$localname);
			$row=db_fetch_assoc($result);
			$exportcode = $localname."|BREAK|".$data;
			rawoutput("<textarea>");
			rawoutput($exportcode);
			rawoutput("</textarea>");
			addnav("Back to the IItem Editor","runmodule.php?module=iitems&op=superuser&superop=start");
		break;
		case "start":
			page_header("IItems");
			iitems_superuser_item_menu();
			addnav("Return to the Grotto","superuser.php");
			addnav("Create a new Item","runmodule.php?module=iitems&op=superuser&superop=create");
		break;
		case "give":
			page_header("Giving IItem");
			output("Giving the item.`n`n");
			$id = httpget('id');
			require_once "modules/iitems/lib/lib.php";
			$success = iitems_give_item($id);
			if (!$success){
				output("Item could not be given.`n`n");
			} else {
				output("Item given successfully.`n`n");
			}
			iitems_superuser_item_menu();
			addnav("Back to the Item Editor","runmodule.php?module=iitems&op=superuser&superop=start");
		break;
		case "edit":
			page_header("Editing Item");
			$id = httpget('id');
			if (httpget('subop')=="save"){
				$posted = httpallpost();
				debug($posted);
				$sarray = array();
				$prearray = array();
				foreach($posted AS $ele => $val){
					if (substr($ele, 0, 3) == "ele"){
						$num = substr($ele, 3, 5);
						// $sarray[$val]=$val;
						$prearray[$num]['ele'] = $val;
					} else if (substr($ele, 0, 3) == "val"){
						$num = substr($ele, 3, 5);
						$prearray[$num]['val'] = $val;
					}
				}
				foreach($prearray AS $num => $vals){
					if ($vals['ele'] != ""){
						$sarray[$vals['ele']] = $vals['val'];
					}
				}
				debug($sarray);
				$data = serialize($sarray);
				$data = addslashes($data);
				$sql="UPDATE " . db_prefix("iitems") . " SET data = '$data' WHERE id = $id";
				db_query($sql);
				output("Array has been reserialised and saved.");			
			}
			
			$sql = "SELECT id,localname,data FROM " . db_prefix("iitems") . " WHERE id = $id";
			$result = db_query($sql);
			$row=db_fetch_assoc($result);
			debug($row['data']);
			$sarray = unserialize($row['data']);
			
			if (!is_array($sarray)) {
				$sarray = array();
			}
			output("`bEntries handled by IItems core:`b`n");
			output("`bverbosename`b (REQUIRED): A verbose name for the item that is shown to the player.`n");
			output("`btype`b (REQUIRED): normal, special or inventory.  A 'normal' item is a generic consumable item such as a grenade.  If a player has ten grenades, they will all do the same thing in the same way.  'special' items are disposable items that can have different stats assigned to them - a player might have five throwing knives, but some of them will be rustier than others.`nWhen giving a 'normal' item, the 'quantity' parameter in the player's Inventory array is increased by one for that item.  When giving a 'special' item, if an item of the same verbose name is present in the Inventory array, a fresh one is added with the name 'Verbose Name (x)', where 'x' is an incrementing number.`nWhere an item is given the 'inventory' type `iby an advanced admin`i, adding the item will give the player another Inventory in which to store things.  Use this for things like backpacks, bandoliers, saddle bags, quivers and so forth.`n");
			output("`bimage`b: Specify an image to use in the Inventory here.  All images are stored in images/iitems, so just give the filename.  Example: medkit.png (references yoursite.com/images/iitems/medkit.png)`n");
			output("`bdestroyafteruse`b: When set, the item will be destroyed after a single use.`n");
			output("`bdkpersist`b: When set, this item will be preserved in the player's Inventory across Dragon Kills.`n");
			output("`bdescription`b: Description of the item shown in the player's Inventory.`n");
			output("`busetext`b: Text shown when item is used.  Can be modified by a module when using an item if necessary.`n");
			output("`bdestroytext`b: Text shown when item is destroyed.`n");
			output("`buseatnewday`b: When true, iitem is automatically used at newday.  Useful for things that provide a buff each day.`n");
			output("`bcannotdiscard`b: When true, item cannot be discarded by the player.`n");
			output("`binventorylocation`b: Will place the item in the inventory specified (eg main (for bandolier), fight (for backpack)) upon giving the item.  When combined with an item type set to \"inventory,\" this determines the type of inventory (eg main or fight).  Setting this to \"mount\" has special handling.`n");
			output("`bvillagehooknav`b: When set, the item is usable from the player's Inventory link in the Village.`n");
			output("`bforesthooknav`b: When set, the item is usable from the player's Inventory link in the Forest.`n");
			output("`bworldnavhooknav`b: When set, the item is usable from the player's Inventory link on the World Map, if installed.`n");
			output("`bgoldcost`b: Cost in gold for whatever shop this item is available in.`n");
			output("`bgemcost`b: Cost in gems for whatever shop this item is available in.`n`n");
			output("`bHelp text supplied by other IItems support modules:`b`n");
			modulehook("iitems-superuser");
			output("`n`bInstructions:`b`nTo delete an entry, just blank the boxes.  You can use both single and double quotes in your values, but avoid doing so in your variable names.  Have fun!`n");
			rawoutput("<form action='runmodule.php?module=iitems&op=superuser&superop=edit&subop=save&id=$id' method='POST'>");
			rawoutput("<table border='0' cellpadding='2' cellspacing='2'>");
			rawoutput("<tr><td>Variable Name</td><td>Value</td></tr>");
			$elementcount = 0;
			$valuecount = 0;
			foreach($sarray AS $element => $value){
				$class=($elementcount%2?"trlight":"trdark");
				$elementcount++;
				$valuecount++;
				$element = stripslashes($element);
				$value = stripslashes($value);
				rawoutput("<tr class='$class'><td><input name=\"ele$elementcount\" value=\"$element\"></td><td><textarea class='input' name='val$valuecount' cols='20' rows='3'>$value</textarea></td></tr>");
			}
			rawoutput("</table>");
			output("`nNow add up to ten new parameters.  Parameters left blank will be ignored.  To add more, just save and you'll get another ten slots.");
			$extracount = $elementcount+10;
			rawoutput("<table border='0' cellpadding='2' cellspacing='2'>");
			rawoutput("<tr><td>Variable Name</td><td>Value</td></tr>");
			for ($i=$elementcount;$i<=$extracount;$i++){
				$class=($elementcount%2?"trlight":"trdark");
				$elementcount++;
				$valuecount++;
				rawoutput("<tr class='$class'><td><input name=\"ele$elementcount\" value=''></td><td><textarea class='input' name='val$valuecount' cols='20' rows='3'></textarea></td></tr>");
			}
			rawoutput("</table>");
			rawoutput("<input type='submit' class='button' value='".translate_inline("Save")."'");
			rawoutput("</form>");
			addnav("","runmodule.php?module=iitems&op=superuser&superop=edit&subop=save&id=$id");
			addnav("Item Editor Main Page","runmodule.php?module=iitems&op=superuser&superop=start");
			addnav("Back to the Grotto","superuser.php");
		break;
		case "create":
			page_header("Create a new Item");
			if (httpget('sub')=="insert"){
				$localname = httppost('localname');
				$sql = "INSERT INTO ".db_prefix("iitems")." (localname,data) VALUES ('$localname','0')";
				db_query($sql);
				output("Item %s Created.`n`n",$localname);
				iitems_superuser_item_menu();
			} else {
				output("Enter a local ID for this item.  Keep it short, memorable, and absent of spaces or special characters.`n");
				rawoutput("<form action='runmodule.php?module=iitems&op=superuser&superop=create&sub=insert' method='POST'>");
				rawoutput("<input name='localname' value='localname'>");
				rawoutput("<input type='submit' class='button' value='".translate_inline("Save")."'");
				addnav("","runmodule.php?module=iitems&op=superuser&superop=create&sub=insert");
			}
			addnav("Item Editor Main Page","runmodule.php?module=iitems&op=superuser&superop=start");
		break;
		case "copy":
			page_header("Copying an Item");
			$original = httpget('id');
			output("This creates a copy of the previously-selected item, under a new itemid.`n`n");
			if (httpget('sub')=="copy"){
				$localname = httppost('localname');
				$sql = "SELECT data FROM ".db_prefix("iitems")." WHERE id = $original";
				$result = db_query($sql);
				$row=db_fetch_assoc($result);
				$data = $row['data'];
				$data = addslashes($data);
				$sql = "INSERT INTO ".db_prefix("iitems")." (localname,data) VALUES ('$localname','$data')";
				db_query($sql);
				output("Item %s Created.`n`n",$localname);
				iitems_superuser_item_menu();
			} else {
				output("Enter an itemid for this item.  Keep it short, memorable, and absent of spaces or special characters.`n");
				rawoutput("<form action='runmodule.php?module=iitems&op=superuser&superop=copy&sub=copy&id=".$original."' method='POST'>");
				rawoutput("<input name='localname' value='localname'>");
				rawoutput("<input type='submit' class='button' value='".translate_inline("Save")."'");
				addnav("","runmodule.php?module=iitems&op=superuser&superop=copy&sub=copy&id=".$original);
			}
			addnav("Item Editor Main Page","runmodule.php?module=iitems&op=superuser&superop=start");
		break;
		case "delete":
			page_header("You sure about that?");
			$id = httpget('id');
			if (httpget('delete')=="delete"){
				$sql = "DELETE FROM " . db_prefix("iitems") . " WHERE id = '$id'";
				db_query($sql);
				output("The item in question has been fed to the Midgets.  I hope you really `iwere`i sure, 'cause it's gonna be a bastard to get it back.`n");
				addnav("Item Editor Main Page","runmodule.php?module=iitems&op=superuser&superop=start");
				iitems_superuser_item_menu();
			} else {
				output("Now, are you really, really, `ireally`i sure you want to do that?  You're probably used to this sort of thing by now, but I'll still mention that there's no undo, and once an item is deleted it's `ideleted.`i  Like, forever.`n");
				addnav("Yes");
				addnav("Yes, I'm sure, get on with it","runmodule.php?module=iitems&op=superuser&superop=delete&id=".$id."&delete=delete");
				addnav("No");
				addnav("No!  That's not what I wanted to do at all!","runmodule.php?module=iitems&op=superuser&superop=start");
			}
		break;
	}
	page_footer();
	return true;
}
?>