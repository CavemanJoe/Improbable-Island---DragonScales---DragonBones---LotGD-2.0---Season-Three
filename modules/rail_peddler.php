<?php

require_once "modules/iitems/lib/lib.php";
require_once "modules/rail/lib.php";
require_once "common.php";

function rail_peddler_getmoduleinfo(){
	$info = array(
		"name"=>"Improbable Railway - Peddler",
		"version"=>"2010-05-09",
		"author"=>"Sylvia Li",
		"category"=>"Improbable Rail",
		"download"=>"",
		"settings"=>array(
			"Rail Pass Peddler - Settings,title",
			"peddlerprice"=>"Cost of card case in cigs:,int|10",
		),
	);
	return $info;
}

function rail_peddler_install(){
	module_addhook("improbablehousing_interior");
	return true;
}

function rail_peddler_uninstall(){
	return true;
}

function rail_peddler_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "improbablehousing_interior":
			$hid = $args['hid'];
			$rid = $args['rid'];
			$loc = rail_peddler_getloc();
			if (($hid == $loc['peddlerhid']) && ($rid == $loc['peddlerrid'])){
				// We're in the right place to meet the peddler
				$price = get_module_setting("peddlerprice");
				if ($session['user']['gems'] >= $price && !rail_hascard("cardcase")){
					// note: this is a one-time character event, so we won't restrict tries per day.
					switch(e_rand(1,5)){
						case 1:
							output("Among hurrying crowds in the concourse, a scruffy figure approaches a distant passer-by. At first the contestant shakes his head, then looks more interested. He digs out his tobacco pouch and receives something, you can't quite make out what, in exchange for a handful of cigarettes.`n`n");
							break;
						case 2:
							output("Behind you a far-off voice calls out, \"`2Best levver, best levver cases! Bloody `bbadass`b cases! Bleedin' ace! Brung in special fer yer, an' cheap at twice the price!`0\"`n`nWhen you turn around, though, you can't make out who it might have been.`n`n");
							break;
						case 3:
							output("A voice at your elbow says, \"`2Ey mate, y'looks like yer knows what's o'clock. Looky 'ere. `bgen`b-oo-`bwine`b levver, hand tooled an' all, sale price today jus' special fer ye, on'y `b%s`b bleedin' ciggies. What d'yer say, hey?`0\"`n`nYou examine the small leather case thrust under your nose. It does seem quite well-made, though you're not sure what you'd use it for.`n`n\"`2C'mon mate, it's ace, all da badasses and Holy Cheese showoffs are buying 'em up. When dey're gone, dey're gone! Dis here's yer lucky chance, I haveta make a sale today b'fore I kin go home ta care fer me old muvver. I'm offring it ta yer b'low me bleedin' cost!`0\"`n`nYou eye the case, pondering. You do have the cigs. The seller doesn't `ilook`i much like the Shady Salesman...`n`n",$price);
							addnav("What do you do?");
							addnav("Buy the case","runmodule.php?module=rail_peddler&op=buy");
							addnav("Pass on the offer","runmodule.php?module=rail_peddler&op=pass");
							break;
					}

				} else if(rail_hascard("cardcase")){
					// this hook lets the peddler have a continued existence as a rumormonger
					$args = modulehook("railpeddler-hascase",$args);
				}
			}
		break;
	}
	return $args;
}

function rail_peddler_run(){
	global $session, $inventory;
	$op = httpget("op");
	$loc = rail_peddler_getloc();
	$hid = $loc["peddlerhid"];
	$rid = $loc["peddlerrid"];
	$price = get_module_setting("peddlerprice");
	if ($op == "buy"){
		$headertext = "Why sure, I'll take one of those!";
	} else {
		$headertext = "Go away, I'm busy.";
	}
	page_header($headertext);
	
	switch($op){
		case "buy":
			output("The scruffy vendor's eyes gleam. Was that a fla`gs`0h of green, or no? \"`2Verra wise, %s. Ye willna be sorry!`0\"`n`n",translate_inline($session['user']['sex']?'lass':'lad'));
			give_item("cardcase");
//			if (!$success){
//				output("Then -- a frown. \"`2Wait, no, I canna sell t'ye. Ye'd better petition the Big Boss, tell 'im t'see what's gaen wrong wiv all dis.`0\"`n`n");
//			} else {
			output("You have a fine leather card case. Pleased, you admire its soft texture. What an excellent purchase you have made!`n`n");
//			}
			$session['user']['gems']-=$price;
			break;
		case "pass":
			output("\"`2Bleedin' smart-arse.`0\" The scruffy vendor shrugs and goes off to pester someone else.`n`n");
			break;
		}
	addnav("Back to the Concourse","runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=$rid");
	
	page_footer();
}

?>