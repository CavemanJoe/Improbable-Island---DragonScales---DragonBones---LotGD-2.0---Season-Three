<?php

function mementos_getmoduleinfo(){
	$info = array(
		"name"=>"Mementos: Player-Created Roleplaying Items",
		"version"=>"2011-04-19",
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
				output("Here are the original prototypes of Mementos you have made.`n`n");
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
					if ($prefs['mouldusesleft']){
						output("You can make another %s of these, at `6`b250`b CobbleStones`0 and `5`b1`b Supporter Point`0 each.`n",$prefs['mouldusesleft']);
						rawoutput("<a href='runmodule.php?module=mementos&op=makecopy&itemid=".$id."'>Make a copy</a><br />");
					} else if (!isset($prefs['mouldusesleft'])){
						output("You can make a mould for this Memento, enabling you to make copies using `6CobbleStones`0.  It'll cost you `5500 Supporter Points`0.`n");
						rawoutput("<a href='runmodule.php?module=mementos&op=makecopy&itemid=".$id."'>Make a mould</a><br />");
					} else {
						output("This mould is too old and knackered to make any more Mementos, but you can re-forge it for `5500 Supporter Points`0.`n");
						rawoutput("<a href='runmodule.php?module=mementos&op=makecopy&itemid=".$id."'>Make a mould</a><br />");
					}
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
				output("You're about to ask the grinning Joker proprietor about these Memento things you've been hearing about, when suddenly the knowledge rushes into your head unbidden.`n`nMementos are player-created objects that can be given to other players.  They grant no in-game advantages, but are fun for roleplaying.  Players often roleplay giving items to each other, but with Mementos, those items can appear in Inventories and act like 'real' in-game items.`n`n`n`bWhat you need to know about Mementos`b`nMementos are weightless, and occupy the 'Shoebox' portion of the player's Inventory.  Mementos survive Drive Kills, and don't go away when 'used.'  They can only be destroyed by being discarded by the player.  They can also be put down and picked up on map squares, and gifted to other players for free (anonymously if desired) via Common Ground's Gifting Station.`n`nYou can specify the Name (shown in the Inventory) of the Memento, the Plural form of the name, the Description (shown in the Inventory), and the Use Text (shown when the player 'uses' the Memento).`n`n`n`bCosts`b`nYou can create a single Memento at a flat cost of `550 Supporter Points`0.  You can also create Memento Moulds, at a cost of `5500 Supporter Points`0 for the mould and the first Memento, plus `5one Supporter Point`0 and `6250 CobbleStones`0 for each of up to fifty copies of that Memento.  Single Mementos can be upgraded to Memento Moulds later on if you like.`n`n`n`bExample Memento`b`n`bName:`b Red Music Box`n`bPlural:`b Red Music Boxes`n`bDescription:`b An ornate red wooden music box, decorated with cut garnets and shiny stainless-steel edging.`n`bUse Text`b: You open up the music box.  Inside is a tiny demon, smoking a cigarette.  Noticing that his house is open, he hurriedly stamps out his smoke and launches into a stirring rendition of 'NewHome is Full of Noobs.'  You close the lid quickly.`n`nEach Memento can have 100 characters for the Name, 100 characters for the Plural name, 255 characters for the Description, and 1,000 characters for the Use Text.`n`n");
			}
			addnav("Memento Stuff");
			if ($pointsavailable >= 50){
				addnav("Create new single Memento","runmodule.php?module=mementos&op=new&type=single");
			} else {
				addnav("You need 50 Supporter Points for a single Memento","");
			}
			
			if ($pointsavailable >= 500){
				addnav("Create new Memento Mould","runmodule.php?module=mementos&op=new&type=mould");
			} else {
				addnav("You need 500 Supporter Points for a Memento Mould","");
			}
			break;
		case "makecopy":
			$giveitem = false;
			$itemid = httpget("itemid");
			$name = get_item_pref("verbosename",$itemid);
			$plural = get_item_pref("plural",$itemid);
			$desc = get_item_pref("description",$itemid);
			$usetext = get_item_pref("usetext",$itemid);
			$moulduses = get_item_pref("mouldusesleft",$itemid);
			
			if ($moulduses > 0){
				if ($cstones >= $cscost){
					output("You've bought a new %s`0 using `6CobbleStones`0!  Would you like to buy another, or head back to the Memento Forge menu?`n`n",$name);
					$giveitem = true;
					increment_module_pref("spent",250,"wcgpoints");
					$cstones = wcgpoints_getpoints();
					$session['user']['donationspent'] += 1;
					//log purchase
					$logsql = "INSERT INTO ".db_prefix("purchaselog")." (acctid,purchased,amount,data,giftwrap,timestamp) VALUES ('".$session['user']['acctid']."','memento_copy_cobblestone','1','none','0','".date("Y-m-d H:i:s")."')";
					db_query($logsql);
				} else {
					output("`4You don't have enough `6CobbleStones`0 to make another copy of that Memento, I'm afraid.`n`n");
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
				output("You're about a make a new copy of the Memento called `b%s`b`0.  This will cost `6250 CobbleStones`0 and `5one Supporter Point`0.  You now have `5%s Supporter Points`0 and `6%s CobbleStones`0, and this mould will make `b%s`0 more Mementos.`n`n",$name,number_format($pointsavailable),number_format($cstones),$moulduses);
				addnav("Buy");
				if ($cstones >= $cscost && $pointsavailable){
					addnav("Buy a copy","runmodule.php?module=mementos&op=makecopy&itemid=".$itemid);
				} else {
					addnav("Not enough `6CobbleStones`0 (or you don't have a `5Supporter Point`0 left)","");
				}
			} else {
				output("Either this mould is knackered, or this is a unique Memento.  You can't make any more copies.  However, you can make a new mould if you like, for `5500 Supporter Points`0.  This will enable you to make further copies of the Memento for `5one Supporter Point`0 plus `6250 CobbleStones`0 each.`n`n",$originalcost);
				addnav("Buy");
				if ($pointsavailable >= 500){
					addnav("Make a new mould","runmodule.php?module=mementos&op=reforge&itemid=".$itemid);
				} else {
					addnav("Not enough `5Supporter Points`0 to make a new mould","");
				}
			}
			addnav("Return");
			addnav("Memento Forge","runmodule.php?module=mementos&op=start");
		break;
		case "reforge":
			$itemid = httpget("itemid");
			output("You've successfully recreated the mould for your Memento.`n`n");
			set_item_pref("mouldusesleft",50,$itemid);
			addnav("Return");
			addnav("Memento Forge","runmodule.php?module=mementos&op=start");
			$session['user']['donationspent'] += 500;
			$logsql = "INSERT INTO ".db_prefix("purchaselog")." (acctid,purchased,amount,data,giftwrap,timestamp) VALUES ('".$session['user']['acctid']."','memento_mould','500','none','0','".date("Y-m-d H:i:s")."')";
			db_query($logsql);
		break;
		case "new":
			$type = httpget('type');
			if ($type=='single'){
				output("You're making a new unique Memento now.  Create your desired item and hit Submit.  You can use colour codes and italics just like in commentary, and you can use bold too, using the ``b switch (remember to close your bolds and italics with another ``b or ``i!).`n`nRemember to use ``n for a new line rather than pressing Enter.  Use ``n``n for a line break between paragraphs.`n`n");
				rawoutput("<form action='runmodule.php?module=mementos&op=check&type=single' method='POST'>Memento Name (maximum 100 characters): <input name='name' id='name'><br /><br />Plural (maximum 100 characters): <input name='plural' id='plural'><br /><br />Memento Description (maximum 255 characters):<br />");
				addnav("","runmodule.php?module=mementos&op=check&type=single");
			} else if ($type=='mould'){
				output("You're making a new Memento Mould now.  Create your desired item and hit Submit.  You can use colour codes and italics just like in commentary, and you can use bold too, using the ``b switch (remember to close your bolds and italics with another ``b or ``i!).`n`nRemember to use ``n for a new line rather than pressing Enter.  Use ``n``n for a line break between paragraphs.`n`n");
				rawoutput("<form action='runmodule.php?module=mementos&op=check&type=mould' method='POST'>Memento Name (maximum 100 characters): <input name='name' id='name'><br /><br />Plural (maximum 100 characters): <input name='plural' id='plural'><br /><br />Memento Description (maximum 255 characters):<br />");
				addnav("","runmodule.php?module=mementos&op=check&type=mould");
			}
			require_once "lib/forms.php";
			previewfield_countup("description");
			rawoutput("<br /><br />Text shown when using the Memento (maximum 1,000 characters):<br />");
			previewfield_countup("usetext");
			rawoutput("<br /><input type=submit>");
			addnav("Start Again");
			addnav("Memento Forge","runmodule.php?module=mementos&op=start");
		break;
		case "check":
			output("Here's a preview of your Memento:`n`n");
			$rname = httppost("name");
			$rplural = httppost("plural");
			$rdesc = httppost("description");
			$rusetext =  httppost("usetext");
			$type = httpget("type");
			
			$dname = stripslashes($rname);
			$dplural = stripslashes($rplural);
			$ddesc = stripslashes($rdesc);
			$dusetext = stripslashes($rusetext);
			
			output("`bName:`b %s`0 (%s`0)`n`bDescription:`b %s`0`n`bUse Text:`b %s`0`n`n",$dname,$dplural,$ddesc,$dusetext);
			
			if (strlen($dname) <= 100 && strlen($dplural) <= 100 && strlen($ddesc) <= 255 && strlen($dusetext) <= 1000){
				addnav("Confirm");
				if ($type=="single"){
					addnav("Buy it! (`550 Supporter Points`0)","runmodule.php?module=mementos&op=confirm&name=".urlencode($rname)."&plural=".urlencode($rplural)."&desc=".urlencode($rdesc)."&usetext=".urlencode($rusetext)."&type=single");
				} else if ($type=="mould"){
					addnav("Buy it! (`5500 Supporter Points`0)","runmodule.php?module=mementos&op=confirm&name=".urlencode($rname)."&plural=".urlencode($rplural)."&desc=".urlencode($rdesc)."&usetext=".urlencode($rusetext)."&type=mould");
				}
			} else {
				output("`4`bError: Something is too long.  Titles can be up to 100 characters, descriptions up to 255 characters, and usage texts up to 1,000 characters.`b`0`n`n");
				rawoutput("<form action='runmodule.php?module=mementos&op=check&type=".$type."' method='POST'>Memento Name (maximum 100 characters): <input name='name' id='name' value=$rname><br /><br />Plural (maximum 100 characters): <input name='plural' id='plural' value=$rplural><br /><br />Memento Description (maximum 255 characters):<br />");
				require_once "lib/forms.php";
				previewfield_countup("description",255,$rdesc);
				rawoutput("<br /><br />Text shown when using the Memento (maximum 1,000 characters):<br />");
				previewfield_countup("usetext",1000,$rusetext);
				rawoutput("<br /><input type=submit>");
				addnav("","runmodule.php?module=mementos&op=check&type=".$type);
			}
			addnav("Start Again");
			addnav("Memento Forge","runmodule.php?module=mementos&op=start");
		break;
		case "confirm":
			$type = httpget("type");
			$name = urldecode(httpget("name"));
			$plural = urldecode(httpget("plural"));
			$desc = urldecode(httpget("desc"));
			$usetext =  urldecode(httpget("usetext"));
			
			$prefs = array(
				//"mouldusesleft" => 50,
				"verbosename" => $name,
				"plural" => $plural,
				"description" => $desc,
				"usetext" => $usetext,
				// "memento_spcost" => ceil($cost/4),
				// "memento_cscost" => $cost*2,
				// "memento_originalcost" => $cost,
				"memento_author" => $session['user']['acctid'],
			);
			
			if ($type=="single"){
				$cost = 50;
				output("You've made a new Memento.  You can find it in your Inventory!");
			} else if ($type=="mould"){
				$cost = 500;
				output("You've made a mould for your new Memento, and the first Memento from that mould is now in your Inventory!");
				$prefs['mouldusesleft'] = 50;
			}
			
			$session['user']['donationspent'] += $cost;
			
			//log purchase
			$logsql = "INSERT INTO ".db_prefix("purchaselog")." (acctid,purchased,amount,data,giftwrap,timestamp) VALUES ('".$session['user']['acctid']."','memento_".$type."','".$cost."','none','0','".date("Y-m-d H:i:s")."')";
			db_query($logsql);
			
			//create the mould, track its id
			$id = give_item("memento",$prefs,"mementomoulds_".$session['user']['acctid'],true);
			//debug($id);
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