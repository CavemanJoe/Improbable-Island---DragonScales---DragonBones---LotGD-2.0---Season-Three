<?php

function mementos_getmoduleinfo(){
	$info = array(
		"name"=>"Mementos: Player-Created Roleplaying Items",
		"version"=>"2010-03-04",
		"author"=>"Dan Hall",
		"category"=>"Items",
		"download"=>"",
	);
	return $info;
}

function mementos_install(){
	module_addhook("hunterslodge");
	module_addhook("inventory");
	module_addhook("inventory-predisplay");
	module_addhook("bioinfo");
	return true;
}

function mementos_uninstall(){
	return true;
}

function mementos_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "hunterslodge":
			addnav("Custom Items");
			addnav("Memento Forge","runmodule.php?module=mementos&op=start");
		break;
		case "inventory":
			$inv = $args['inventory'];
			$sort = $args['sort'];
			foreach($inv AS $itemid => $prefs){
				if ($prefs['item']=="memento"){
					//debug($prefs);
					addnav("","inventory.php?items_context=".$args['context']."&memento_togglebioshow=".$prefs['itemid']."&items_sort=$sort");
					// if (!isset($prefs['memento_showinbio'])){
						// $prefs['memento_showinbio'] = 0;
					// }
					if (!$prefs['memento_showinbio']){
						$args['inventory'][$itemid]['inventoryactions'].=appoencode("`4Hidden from Bio`0 (<a href=\"inventory.php?items_context=".$args['context']."&memento_togglebioshow=".$prefs['itemid']."&items_sort=$sort\">show</a>) | ",true);
					} else {
						$args['inventory'][$itemid]['inventoryactions'].=appoencode("`2Visible in Bio`0 (<a href=\"inventory.php?items_context=".$args['context']."&memento_togglebioshow=".$prefs['itemid']."&items_sort=$sort\">hide</a>) | ",true);
					}
				}
			}
		break;
		case "inventory-predisplay":
			$id = httpget("memento_togglebioshow");
			if ($id){
				if (get_item_pref("memento_showinbio",$id)){
					output("`\$You have chosen to hide this Memento from your Bio.`0`n");
					set_item_pref("memento_showinbio","",$id);
				} else {
					output("`@You have chosen to show this Memento in your Bio.`0`n");
					set_item_pref("memento_showinbio","1",$id);
				}
				output("Multiple Mementos of the same type are only shown once on your Bio page.`n`n");
			}
		break;
		case "bioinfo":
			$inv = load_inventory($args['acctid'],true);
			$gr = group_items($inv,false);
			
			$mem = array();
			foreach($gr AS $id=>$prefs){
				if ($prefs['memento_showinbio']){
					$mem[$id] = $prefs;
				}
			}
			
			if (count($mem)){
				output("`bMementos`b`n");
				rawoutput("<table width=100% style='border: dotted 1px #000000;'>");
				foreach($mem AS $id=>$prefs){
					$classcount+=1;
					$class=($classcount%2?"trdark":"trlight");
					rawoutput("<tr class='$class'><td>");
					output("`b%s`b`0`n",stripslashes($prefs['verbosename']));
					output("%s`0`n`n",stripslashes($prefs['description']));
					rawoutput("</td></tr>");
				}
				rawoutput("</table>");
				output("`n`n");
			}
		break;
		}
	return $args;
}

function mementos_run(){
	global $session;
	page_header("Memento Forge");
	$op = httpget('op');
	$pointsavailable = $session['user']['donation'] - $session['user']['donationspent'];
	require_once "modules/wcgpoints.php";
	$cstones = wcgpoints_getpoints();
	switch (httpget('op')){
		case "start":
			$moulds = load_inventory("mementomoulds_".$session['user']['acctid'],true);
			if (count($moulds)){
				output("Here are your Memento moulds.`n`n");
				//if there are moulds
				rawoutput("<table width=100% style='border: dotted 1px #000000;'>");
				$classcount=1;
				$moulds = modulehook("mementos",$moulds);
				foreach($moulds AS $id=>$prefs){
					$classcount++;
					$class=($classcount%2?"trdark":"trlight");
					rawoutput("<tr class='$class'><td>");
					output("`b%s`b`0`n",stripslashes($prefs['verbosename']));
					output("%s`0`n`n",stripslashes($prefs['description']));
					output("`bUse Text`b:%s`0`n`n",stripslashes($prefs['usetext']));
					output("You can make another %s of these, at `5`b%s`b Supporter Points`0 or `6`b%s`b CobbleStones`0 each.`n",$prefs['mouldusesleft'],$prefs['memento_spcost'],$prefs['memento_cscost']);
					rawoutput("<a href='runmodule.php?module=mementos&op=makecopy&itemid=".$id."'>Make a copy</a><br />");
					addnav("","runmodule.php?module=mementos&op=makecopy&itemid=".$id);
					if (is_array($prefs['memento_forge_actions']) && count($prefs['memento_forge_actions'])){
						foreach($prefs['memento_forge_actions'] AS $action){
							rawoutput($action);
						}
					}
					rawoutput("</td></tr>");
				}
				rawoutput("</table>");
			} else {
				//if there are no moulds
				output("You're about to ask the grinning Joker proprietor about these Memento things you've been hearing about, when suddenly the knowledge rushes into your head unbidden.`n`nMementos are player-created objects that can be given to other players.  They grant no in-game advantages, but are fun for roleplaying.  Players often roleplay giving items to each other, but with Mementos, those items can appear in Inventories and act like 'real' in-game items.`n`n`n`bWhat you need to know about Mementos`b`nMementos are weightless, and occupy the 'Shoebox' portion of the player's Inventory.  Mementos survive Drive Kills, and don't go away when 'used.'  They can only be destroyed by being discarded by the player.  They can also be put down and picked up on map squares, and gifted to other players for free (anonymously if desired) via Common Ground's Gifting Station.`n`nYou can specify the Name (shown in the Inventory) of the Memento, the Plural form of the name, the Description (shown in the Inventory), and the Use Text (shown when the player 'uses' the Memento).`n`n`n`bCosts`b`nThe first time you create any given Memento, a mould will be made for it.  These moulds cost `5Supporter Points`0.  The rate is one `5Supporter Point`0 per two characters of the Description, and one `5Supporter Point`0 per two characters of the Use Text (if any).  The moulds can be used to make up to fifty copies of the Memento to give to other players (the initial Memento comes with the mould).  You can pay for these copies with `5Supporter Points`0 (at a cost of one quarter of the original mould price) or `6CobbleStones`0 (at a rate of double the cost of the original mould price plus one `5Supporter Point`0).`n`n`n`bExample Memento`b`n`bName:`b Red Music Box`n`bPlural:`b Red Music Boxes`n`bDescription:`b An ornate red wooden music box, decorated with cut garnets and shiny stainless-steel edging.`n`bUse Text`b: You open up the music box.  Inside is a tiny demon, smoking a cigarette.  Noticing that his house is open, he hurriedly stamps out his smoke and launches into a stirring rendition of 'NewHome is Full of Noobs.'  You close the lid quickly.`n`n`bCost:`b`n92-character Description = `546 Supporter Points`0`n238-character Use Text = `5119 Supporter Points`0`nTotal = `5165 Supporter Points`0 for the mould and the first Memento.  Extra copies at `542 Supporter Points`0 OR `6330 CobbleStones`0 and `51 Supporter Point`0 each.`n`n");
			}
			addnav("Memento Stuff");
			addnav("Create new Memento Mould","runmodule.php?module=mementos&op=new");
		break;
		case "makecopy":
			$giveitem = false;
			$itemid = httpget("itemid");
			//$item = itemid_to_item($itemid);
			$cscost = get_item_pref("memento_cscost",$itemid);
			$spcost = get_item_pref("memento_spcost",$itemid);
			$name = get_item_pref("verbosename",$itemid);
			$plural = get_item_pref("plural",$itemid);
			$desc = get_item_pref("description",$itemid);
			$usetext = get_item_pref("usetext",$itemid);
			$moulduses = get_item_pref("mouldusesleft",$itemid);
			$originalcost = get_item_pref("memento_originalcost",$itemid);
			
			if (httpget("buywithcobblestones")){
				if ($cstones >= $cscost){
					output("You've bought a new %s`0 using `6CobbleStones`0!  Would you like to buy another, or head back to the Memento Forge menu?`n`n",$name);
					$giveitem = true;
					increment_module_pref("spent",$cscost,"wcgpoints");
					$cstones = wcgpoints_getpoints();
					$session['user']['donationspent'] += 1;
				} else {
					output("`4You don't have enough `6CobbleStones`0 to make another copy of that Memento, I'm afraid.`n`n");
				}
			} else if (httpget("buywithsupporterpoints")){
				if ($pointsavailable >= $spcost){
					output("You've bought a new %s`0 using `5Supporter Points`0!  Would you like to buy another, or head back to the Memento Forge menu?`n`n",$name);
					$giveitem = true;
					$session['user']['donationspent'] += $spcost;
				} else {
					output("`4You don't have enough `5Supporter Points`0 to make another copy of that Memento, I'm afraid.`n`n");
				}
			}
			
			$pointsavailable = $session['user']['donation'] - $session['user']['donationspent'];
			
			if ($giveitem){
				$prefs = get_item_prefs($itemid);
				unset($prefs["mouldusesleft"]);
				$prefs['memento_originalitem'] = $itemid;
				give_item("memento",$prefs);
				$moulduses--;
				set_item_pref("mouldusesleft",$moulduses,$itemid);
			}
			
			if ($moulduses > 0){
				output("You're about a make a new copy of the Memento called `b%s`b`0.  This will cost `5%s Supporter Points`0 or `6%s CobbleStones`0 and `5one Supporter Point`0.  You now have `5%s Supporter Points`0 and `6%s CobbleStones`0, and this mould will make `b%s`0 more Mementos.`n`n",$name,$spcost,$cscost,number_format($pointsavailable),number_format($cstones),$moulduses);
				addnav("Buy");
				if ($pointsavailable >= $spcost){
					addnav("Buy with `5Supporter Points`0","runmodule.php?module=mementos&op=makecopy&buywithsupporterpoints=true&itemid=".$itemid);
				} else {
					addnav("Not enough `5Supporter Points`0","");
				}
				if ($cstones >= $cscost && $pointsavailable){
					addnav("Buy with `6CobbleStones`0","runmodule.php?module=mementos&op=makecopy&buywithcobblestones=true&itemid=".$itemid);
				} else {
					addnav("Not enough `6CobbleStones`0 (or you don't have a `5Supporter Point`0 left)","");
				}
			} else {
				output("This mould is knackered!  You can't make any more copies from this mould.  However, you can re-forge the mould if you like, for `5%s Supporter Points`0.`n`n",$originalcost);
				addnav("Buy");
				if ($pointsavailable >= $originalcost){
					addnav("Re-forge the mould","runmodule.php?module=mementos&op=reforge&itemid=".$itemid);
				} else {
					addnav("Not enough `5Supporter Points`0 to re-forge the mould","");
				}
			}
			addnav("Return");
			addnav("Memento Forge","runmodule.php?module=mementos&op=start");
		break;
		case "reforge":
			$itemid = httpget("itemid");
			output("You've successfully re-forged the mould for your Memento.`n`n");
			set_item_pref("mouldusesleft",50,$itemid);
			addnav("Return");
			addnav("Memento Forge","runmodule.php?module=mementos&op=start");
		break;
		case "new":
			output("You're making a new Memento Mould now.  Create your desired item and hit Submit.  You can use colour codes and italics just like in commentary, and you can use bold too, using the ``b switch (remember to close your bolds and italics with another ``b or ``i!).`n`nRemember to use ``n for a new line rather than pressing Enter.  Use ``n``n for a line break between paragraphs.`n`n");
			rawoutput("<form action='runmodule.php?module=mementos&op=calculate' method='POST'>Memento Name (maximum 100 characters): <input name='name' id='name'><br /><br />Plural (maximum 100 characters): <input name='plural' id='plural'><br /><br />Memento Description:<br />");
			require_once "lib/forms.php";
			previewfield_countup("description");
			
			rawoutput("<br /><br />Text shown when using the Memento:<br />");
			previewfield_countup("usetext");
			rawoutput("<br /><input type=submit>");
			addnav("","runmodule.php?module=mementos&op=calculate");
			addnav("Start Again");
			addnav("Memento Forge","runmodule.php?module=mementos&op=start");
		break;
		case "calculate":
			output("Here's a preview of your Memento:`n`n");
			$rname = httppost("name");
			$rplural = httppost("plural");
			$rdesc = httppost("description");
			$rusetext =  httppost("usetext");
			
			$dname = stripslashes($rname);
			$dplural = stripslashes($rplural);
			$ddesc = stripslashes($rdesc);
			$dusetext = stripslashes($rusetext);
			
			output("`bName:`b %s`0 (%s`0)`n`bDescription:`b %s`0`n`bUse Text:`b %s`0`n`n",$dname,$dplural,$ddesc,$dusetext);
			$totalcost = ceil((strlen($ddesc) + strlen($dusetext))/2);
			output("Total cost: `5`b%s`b Supporter Points`0",$totalcost);
			if ($pointsavailable >= $totalcost){
				if (strlen($name) <= 100 && strlen($plural) <= 100){
					addnav("Confirm");
					addnav("Buy it!","runmodule.php?module=mementos&op=confirmnew&name=".urlencode($dname)."&plural=".urlencode($dplural)."&desc=".urlencode($ddesc)."&usetext=".urlencode($dusetext)."&cost=".$totalcost);
				} else {
					output("`4`bThat name is too long!`b  Both the Name and the Plural have to be below 100 characters each.`0");
					rawoutput("<form action='runmodule.php?module=mementos&op=calculate' method='POST'>Memento Name (maximum 100 characters): <input name='name' id='name'><br /><br />Plural (maximum 100 characters): <input name='plural' id='plural'><br /><br />Memento Description:<br /><textarea name='description' id='description' rows='6' cols='60'>".$ddesc."</textarea><br /><br />Text shown when using the Memento:<br /><textarea name='usetext' id='usetext' rows='6' cols='60'>".$dusetext."</textarea><br /><input type=submit>");
					addnav("","runmodule.php?module=mementos&op=calculate");
				}
			} else {
				addnav("Whoops");
				addnav("You don't have that many Supporter Points, I'm afraid.","");
			}
			addnav("Start Again");
			addnav("Memento Forge","runmodule.php?module=mementos&op=start");
		break;
		case "confirmnew":
			output("You've made a mould for your new Memento, and the first Memento from that mould is now in your Inventory!");
			
			$cost = httpget("cost");
			$session['user']['donationspent']+=$cost;
			
			$name = urldecode(httpget("name"));
			$plural = urldecode(httpget("plural"));
			$desc = urldecode(httpget("desc"));
			$usetext =  urldecode(httpget("usetext"));
			
			// debug($name);
			// debug($desc);
			// debug($usetext);
			
			$prefs = array(
				"mouldusesleft" => 50,
				"verbosename" => $name,
				"plural" => $plural,
				"description" => $desc,
				"usetext" => $usetext,
				"memento_spcost" => ceil($cost/4),
				"memento_cscost" => $cost*2,
				"memento_originalcost" => $cost,
				"memento_author" => $session['user']['acctid'],
			);
			
			//create the mould, track its id
			$id = give_item("memento",$prefs,"mementomoulds_".$session['user']['acctid'],true);
			debug($id);
			$prefs['memento_originalitem'] = $id;
			set_item_pref("memento_originalitem",$id,$id);
			
			//give the player the initial memento
			give_item("memento",$prefs);
			
			
			addnav("Return");
			addnav("Memento Forge","runmodule.php?module=mementos&op=start");
		break;
	}
	addnav("Return");
	addnav("Back to the Hunter's Lodge","runmodule.php?module=iitems_hunterslodge&op=start");
	page_footer();
}

?>