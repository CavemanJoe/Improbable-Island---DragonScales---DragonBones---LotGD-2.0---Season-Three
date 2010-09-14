<?php

function iitems_mountaccessories_getmoduleinfo(){
	$info=array(
		"name"=>"Iitems: Mount Accessories",
		"version"=>"2010-05-18",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"Mount Accessories",
		"download"=>"",
	);
	return $info;
}

function iitems_mountaccessories_install(){
	module_addhook("stables_footer");
	module_addhook("boughtmount");
	module_addhook("soldmount");
	module_addhook("iitems-give-item");
	module_addhook("iitems-superuser");
	return true;
}

function iitems_mountaccessories_uninstall(){
	return true;
}

function iitems_mountaccessories_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "boughtmount":
		case "soldmount":
			//strip Mount Accessories from this Mount, give partial refund
			require_once "modules/iitems_mountaccessories/lib/lib.php";
			$refund = iitems_mountaccessories_strip_player_accessories();
			if ($refund['gold']){
				output("`0You receive %s Requisition from selling back the equipped Mount Accessories.`n",$refund['gold']);
			}
			if ($refund['gems'] > 1){
				output("`0You receive %s Cigarettes from selling back the equipped Mount Accessories.`n",$refund['gems']);
			} else if ($refund['gems'] == 1){
				output("`0You receive one cigarette from selling back the equipped Mount Accessories.`n");
			}
			break;
		case "stables_footer":
			if ($session['user']['hashorse']>0){
				addnav("Browse Mount Accessories","runmodule.php?module=iitems_mountaccessories&op=browse");
			}
			break;
		case "iitems-give-item":
			if ($args['master']['mountaccessory']){
				$args['player']['inventorylocation']="mount";
				$args['player']['mountaccessory']=true;
			}
		break;
		case "iitems-superuser":
			output("`0`bMount Accessories`b`n");
			output("Use Goldcost and Gemcost to determine the cost of this item in the Stables.`n");
			output("Things will get very complicated very quickly if this item has any weight.`n");
			output("Don't forget to unset \"destroyafteruse\" and set \"useatnewday\" if you want a Mount Accessory that gives scaling benefits atop the original Mount.`n");
			output("`bmountaccessory`b: Set this to True to declare this as a Mount Accessory.`n");
			output("`bstablelocation`b: Accessory is available if this matches the player's Location.  Set to \"all\" to ignore player's location.`n");
			output("`bformount`b: Can be a comma-separated list of mountids for which this accessory is appropriate, or it can be set to \"all\" as above.`n`n");
		break;
	}
	return $args;
}

function iitems_mountaccessories_run(){
	global $session;
	require_once "modules/iitems_mountaccessories/lib/lib.php";
	require_once "modules/iitems/lib/lib.php";
	$op = httpget('op');
	page_header("Mount Accessories");
	switch($op){
		case "browse":
			$text = array();
			$text['mountaccessores_starttext'] = "`0Merick directs you to a rack of accessories for your current Mount.`n`n\"Now, I have to be warnin' yer,\" says Merick, \"I'm quite happy to give yer a trade-in on any beasties ye might buy from me, but I don't be takin' no returns on the accessories.  There's nae market in pre-owned add-ons, y'see.\"`n`nHere are the accessories available for your Mount:`n`n";
			$text = modulehook("stabletext",$text);
			output("%s",$text['mountaccessories_starttext']);
			$mountid = $session['user']['hashorse'];
			$accs = iitems_mountaccessories_get_compatible_accessories($mountid);
			$playeraccs = iitems_mountaccessories_get_player_accessories();
			//debug($playeraccs);
			if (count($accs)){
				//inventory-type display routine
				rawoutput("<table width=100% style='border: dotted 1px #000000;'>");
				$classcount = 0;
				foreach ($accs AS $acc => $details){
					//debug($acc);
					//debug($details);
					$classcount++;
					$class=($classcount%2?"trdark":"trlight");
					rawoutput("<tr class='$class'><td>");
					if ($details['image']) rawoutput("<table width=100% cellpadding=0 cellspacing=0><tr><td width=100px align=center><img src=\"images/iitems/".$central['image']."\"></td><td>");
					output("`b%s`b`n",stripslashes($details['verbosename']));
					output("%s`n",stripslashes($details['description']));
					if (is_module_active("iitems_weightandmass") && isset($details['weight'])){
						output("Weight: %s kg`n",$details['weight']);
					}
					output("`0Cost:`n");
					if ($details['goldcost']) output("`0`b%s`b Requisition`n",$details['goldcost']);
					if ($details['gemcost']) output("`0`b%s`b Cigarettes`n",$details['gemcost']);
					//evaluate whether the player already has this accessory
					if (!isset($playeraccs[$acc])){
						//evaluate whether the player can afford this accessory
						if ($session['user']['gems'] >= $details['gemcost'] && $session['user']['gold'] >= $details['goldcost']){
							//purchase link
							rawoutput("<a href=\"runmodule.php?module=iitems_mountaccessories&op=buy&acc=$acc\">Purchase this Accessory</a><br />");
							addnav("","runmodule.php?module=iitems_mountaccessories&op=buy&acc=$acc");
						}
					} else {
						output("`0You already own this accessory.`n");
					}
					if ($details['image']) rawoutput("</td></tr></table>");
					rawoutput("</td></tr>");
				}
				rawoutput("</table>");
			} else {
				output("`0There are no accessories available for your Mount.  Pah.`n`n");
			}
			addnav("Return to the Stables","stables.php");
		break;
		case "buy":
			$acc=httpget("acc");
			
			iitems_give_item($acc);
			
			debug($acc);
			
			addnav("Return to the Stables","stables.php");
		break;
	}
	page_footer();
	return true;
}
?>
