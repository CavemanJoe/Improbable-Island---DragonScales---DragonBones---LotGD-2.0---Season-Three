<?php

function improbablehousing_furnitureshop_getmoduleinfo(){
	$info=array(
		"name"=>"Improbable Housing: Furniture Shop",
		"version"=>"2010-11-29",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"Housing",
		"download"=>"",
	);
	return $info;
}

function improbablehousing_furnitureshop_install(){
	$condition = "if (\$session['user']['location'] == \"Improbable Central\") {return true;} else {return false;};";
	module_addhook("village",false,$condition);
	module_addhook("improbablehousing_sleepslot");
	return true;
}

function improbablehousing_furnitureshop_uninstall(){
	return true;
}

function improbablehousing_furnitureshop_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "village":
			tlschema($args['schemas']['marketnav']);
			addnav($args['marketnav']);
			tlschema();
			addnav("Cadfael's Furniture","runmodule.php?module=improbablehousing_furnitureshop&op=start");
			break;
		case "improbablehousing_sleepslot":
			$hid = $args['hid'];
			$house = $args['house'];
			$rid = $args['rid'];
			$slot = $args['slot'];
			// debug($rid);
			// debug($house);
			if (improbablehousing_getkeytype($house,$rid)>=100){
				//player has a master key, or owns the Dwelling
				$furniture = get_items_with_prefs("furniture");
				// debug($furniture);
				if (is_array($furniture)){
					addnav("Put down Furniture");
					foreach($furniture AS $key => $vals){
						//debug($vals);
						addnav("Install Furniture");
						addnav(array("Put down %s",$vals['verbosename']),"runmodule.php?module=improbablehousing_furnitureshop&op=drop&item=".$key."&hid=".$args['house']['id']."&rid=".$args['rid']."&slot=".$args['slot']);
					}
				}
			}
		break;
	}
	return $args;
}

function sortbyprice($a, $b){
	return strnatcmp($a['goldcost'], $b['goldcost']);
}

function improbablehousing_furnitureshop_run(){
	global $session;
	page_header("Cadfael's Furniture");

	//have the beds and such allowed to be carried in backpack (insert suitable hundred-kilo-bed-in-backpack-lols) or carried by a secondary character in a new Inventory space (we'll call it Delivery Boy - will need a suitably silly photo).
	switch (httpget('op')){
		case "start":
			output("Cadfael greets you with a preposterously strong Welsh accent.  \"`3Well hey there!  Take a look around, all one 'undred per cent natural and me own work!`0\"`n`n");
			//find items that are furniture!
			$furniture = get_items_with_settings("furniture");
			//debug($furniture);
			
			usort($furniture,'sortbyprice');
			
			rawoutput("<table width=100% style='border: dotted 1px #000000;'>");
			foreach($furniture AS $sort=>$vals){
				$key = $vals['item'];
				$classcount+=1;
				$class=($classcount%2?"trdark":"trlight");
				rawoutput("<tr class='$class'><td>");

				if ($vals['image']) rawoutput("<table width=100% cellpadding=0 cellspacing=0><tr><td width=100px align=center><img src=\"images/items/".$vals['image']."\"></td><td>");
				output("`b%s`b`n",stripslashes($vals['verbosename']));
				output("%s`0`n",stripslashes($vals['description']));
				if ($vals['weight']){
					output("Weight: %s kg`n`0",$vals['weight']);
				}
				rawoutput("<table width=100%><tr><td width=50%>");
				if ($vals['goldcost'] && $vals['gemcost']){
					$disp = "`b".number_format($vals['goldcost'])."`b Requisition and `b".number_format($vals['gemcost'])."`b Cigarettes";
					$sdisp = $vals['goldcost']." Req, ".$vals['gemcost']." Cigs";
				} else if ($vals['goldcost']){
					$disp = "`b".number_format($vals['goldcost'])."`b Requisition";
					$sdisp = $vals['goldcost']." Req";
				} else {
					$disp = "`b".number_format($vals['goldcost'])."`b Cigarettes";
					$sdisp = $vals['goldcost']." Cigs";
				}
				if ($session['user']['gold'] >= $vals['goldcost'] && $session['user']['gems'] >= $vals['gemcost']){
					addnav("Buy Items");
					addnav(array("%s (%s)",$vals['verbosename'],$sdisp),"runmodule.php?module=improbablehousing_furnitureshop&op=buy&item=".$key, true);
					addnav("","runmodule.php?module=improbablehousing_furnitureshop&op=buy&item=".$key);
					rawoutput("<a href=\"runmodule.php?module=improbablehousing_furnitureshop&op=buy&item=".$key."\">Buy for ".appoencode($disp)."</a><br />");
				} else {
					output("Price: %s`n",$disp);
					output("`&You can't afford this item right now.`0`n");
				}
				rawoutput("</td></tr></table>");
				if ($vals['image']) rawoutput("</td></tr></table>");
				rawoutput("</td></tr>");
			}
			rawoutput("</td></tr></table>");	
			addnav("Custom Furniture");
			addnav("Enquire about Custom Furniture","runmodule.php?module=improbablehousing_furnitureshop&op=custom&sub=start");
		break;
		case "buy":
			$item = httpget("item");
			$goldcost = get_item_setting("goldcost",$item);
			$gemcost = get_item_setting("gemcost",$item);
			$name = get_item_setting("verbosename",$item);
			if ($goldcost && $gemcost){
				$disp = "`b".number_format($goldcost)."`b Requisition and `b".number_format($gemcost)."`b Cigarettes";
			} else if ($goldcost){
				$disp = "`b".number_format($goldcost)."`b Requisition";
			} else {
				$disp = "`b".number_format($goldcost)."`b Cigarettes";
			}
			output("Cadfael nods.  \"`3So, you're after a %s then, are ye?  That'll be %s, please.`0\"`n`n",$name,$disp);
			addnav("Confirmation");
			addnav("Confirm sale","runmodule.php?module=improbablehousing_furnitureshop&op=confirmbuy&item=".$item);
			addnav("Wait, I've changed my mind!","runmodule.php?module=improbablehousing_furnitureshop&op=start");
		break;
		case "confirmbuy":
			$item = httpget("item");
			$goldcost = get_item_setting("goldcost",$item);
			$gemcost = get_item_setting("gemcost",$item);
			$name = get_item_setting("verbosename",$item);
			$session['user']['gold']-=$goldcost;
			$session['user']['gems']-=$gemcost;
			give_item($item);
			output("You hand over your hard-won currency to Cadfael, who grins and says \"`3Ah, much obliged!  Pleasure doin' business... with...`0\" he stops, bemused.  \"`3Are... are you trying to fit that into a `ibackpack?`i`0\"`n`nAfter several minutes of grunting and sweating, your backpack now resembles a... well, a barely-held-together layer of canvas stretched drum-tight into the shape of a %s.  With a very small %s bent double underneath.  Cadfael shakes his head and mutters something about it taking all sorts.`n`n",$name,$session['user']['race']);
		break;
		case "drop":
			page_header("");
			$itemid = httpget('item');
			debug($itemid);
			$hid = httpget('hid');
			require_once "modules/improbablehousing/lib/lib.php";
			$house = improbablehousing_gethousedata($hid);
			$rid = httpget('rid');
			$slot = httpget('slot');
			$house['data']['rooms'][$rid]['sleepslots'][$slot]['stamina']=get_item_pref("sleepslot_stamina",$itemid);
			$house['data']['rooms'][$rid]['sleepslots'][$slot]['name']=get_item_pref("sleepslot_name",$itemid);
			$house['data']['rooms'][$rid]['sleepslots'][$slot]['desc']=get_item_pref("sleepslot_desc",$itemid);
			$house['data']['rooms'][$rid]['sleepslots'][$slot]['multicapacity']=get_item_pref("sleepslot_multicapacity",$itemid);
			use_item($itemid);
			improbablehousing_sethousedata($house);
			addnav("Continue");
			addnav(array("Return to %s",$house['data']['rooms'][$rid]['name']),"runmodule.php?module=improbablehousing&op=interior&hid=".$hid."&rid=".$rid);
		break;
		case "custom":
			$sub = httpget('sub');
			switch ($sub){
				case "start":
					output("\"`3Oh yeah, I get lots of custom orders,`0\" says Cadfael, grinning.  \"`3Mostly for Mutants and Robots.  Interestin' stuff, really, tryin' to figure out where all the extra arms and legs would go.  I can make any of my furniture pieces look like just about anythin'.  Custom work'll set ye back five ciggies as a startin' price, an' goes up from there according to complexity.  You got somethin' in mind?`0\"`n`n`JCadfael can take any piece of furniture intended for sleeping on and assign a new name and description to the sleeping slot that it'll take up.  If you want your houseguests to be able to sleep on top of a bookcase, or inside a cleverly-concealed secret foldaway bed, or in a zero-gravity chamber (yes, Cadfael's carpentry skills are `ijust that good`i), then bring one of his furniture pieces here.`0`n`n");
					$furniture = get_items_with_prefs("furniture");
					if (is_array($furniture)){
						addnav("Customize Furniture");
						foreach($furniture AS $key => $vals){
							addnav(array("Customize %s",$vals['verbosename']),"runmodule.php?module=improbablehousing_furnitureshop&op=custom&sub=desc&item=".$key);
						}
					}
					addnav("Return");
					addnav("Back to the Furniture List","runmodule.php?module=improbablehousing_furnitureshop&op=start");
				break;
				case "desc":
					//get the descriptions from the player
					$item = httpget('item');
					output("\"`3Right, then - what'll this be called?`0\"`n`n`0Enter a title for the furniture piece here.  This is what's displayed in the nav links in your Dwelling - it'll read something like \"(your title) Available\" or \"(your title) Occupied by Admin CavemanJoe\" - you've got 25 characters to play about with, and no colour or formatting codes are allowed here.`n`n");
					$oldtitle = get_item_pref("sleepslot_name",$item);
					$olddesc = get_item_pref("sleepslot_desc",$item);
					rawoutput("<form action='runmodule.php?module=improbablehousing_furnitureshop&op=custom&sub=confirm&item=".$item."' method='POST'>");
					rawoutput("<input id='newtitle' name='newtitle' width='25' maxlength='25' value='".$oldtitle."'>");
					output("`n`nNow enter a description for the new furniture piece.  This is what's displayed to players when they settle down for a good night's kip.  The longer the description, the more it'll cost.  You can use colour and formatting codes here - remember to use ``n for new lines.`n`n");
					rawoutput("<textarea name='newdesc' id='newdesc' rows='6' cols='60'>".$olddesc."</textarea><input type=submit></form>");
					addnav("Return");
					addnav("Back to the Furniture List","runmodule.php?module=improbablehousing_furnitureshop&op=start");
					addnav("","runmodule.php?module=improbablehousing_furnitureshop&op=custom&sub=confirm&item=".$item);
				break;
				case "confirm":
					$newtitle = httppost('newtitle');
					$newdesc = httppost('newdesc');
					$item = httpget('item');
					
					$newdesc = str_replace("\n","`n",$newdesc);
					$newtitle = str_replace("`","",$newtitle);
					$newtitle = substr($newtitle,0,25);
					
					$newtitle = stripslashes($newtitle);
					$newdesc = stripslashes($newdesc);
					//show the descriptions
					output("Your new title is:`n%s`0`n`nYour new description is:`n%s`0`n`n",$newtitle,$newdesc);
					
					$cigcost = 5 + (floor(strlen($newdesc)/50));
					output("This much work will cost %s cigarettes.`n`n",$cigcost);
					
					set_item_pref("proposed_sleepslot_name",$newtitle,$item);
					set_item_pref("proposed_sleepslot_desc",$newdesc,$item);
					
					if ($session['user']['gems'] >= $cigcost){
						addnav("Continue");
						addnav("Pay the man!","runmodule.php?module=improbablehousing_furnitureshop&op=custom&sub=confirmfinal&item=".$item."&cost=".$cigcost);
					} else {
						output("You don't have enough cigarettes for that!`n`n");
					}
					addnav("Return");
					addnav("Back to the Furniture List","runmodule.php?module=improbablehousing_furnitureshop&op=start");
				break;
				case "confirmfinal":
					//take the cigaretes, change the prefs
					$item = httpget('item');
					$cost = httpget('cost');
					$session['user']['gems'] -= $cost;
					output("Cadfael takes your cigarettes and furniture with a smile, and disappears into the back of the shop.  You hear muffled sounds of sawing, hammering, swearing and so forth, and after half an hour or so he returns.  \"`3Here you go - might not look much different `inow,`i but wait 'til you get it in yer house.`0\"`n`nYour furniture is now customized!");
					
					$newtitle = get_item_pref("proposed_sleepslot_name",$item);
					$newdesc = get_item_pref("proposed_sleepslot_desc",$item);
					set_item_pref("verbosename","Customized Furniture (".$newtitle.")",$item);
					set_item_pref("sleepslot_name",$newtitle,$item);
					set_item_pref("sleepslot_desc",$newdesc,$item);
					set_item_pref("description","This furniture was modified at Cadfael's shop in Improbable Central.",$item);
					
					addnav("Return");
					addnav("Back to the Furniture List","runmodule.php?module=improbablehousing_furnitureshop&op=start");
				break;
			}
		break;
	}

	if (httpget('op')!="drop"){
		addnav("Exit");
		addnav("Back to the Outpost","village.php");
	}
	page_footer();
	
	return true;
}
?>