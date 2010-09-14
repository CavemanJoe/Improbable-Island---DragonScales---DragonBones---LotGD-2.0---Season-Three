<?php

function improbablehousing_furnitureshop_getmoduleinfo(){
	$info=array(
		"name"=>"Improbable Housing: Furniture Shop",
		"version"=>"2010-06-27",
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
			
			rawoutput("<table width=100% style='border: dotted 1px #000000;'>");
			foreach($furniture AS $key=>$vals){
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
			$itemid = httpget('item');
			$hid = httpget('hid');
			require_once "modules/improbablehousing/lib/lib.php";
			$house = improbablehousing_gethousedata($hid);
			$rid = httpget('rid');
			$slot = httpget('slot');
			$house['data']['rooms'][$rid]['sleepslots'][$slot]['stamina']=get_item_pref("sleepslot_stamina",$itemid);
			$house['data']['rooms'][$rid]['sleepslots'][$slot]['name']=get_item_pref("sleepslot_name",$itemid);
			$house['data']['rooms'][$rid]['sleepslots'][$slot]['desc']=get_item_pref("sleepslot_desc",$itemid);
			use_item($itemid);
			improbablehousing_sethousedata($house);
			addnav("Continue");
			addnav(array("Return to %s",$house['data']['rooms'][$rid]['name']),"runmodule.php?module=improbablehousing&op=interior&hid=".$hid."&rid=".$rid);
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