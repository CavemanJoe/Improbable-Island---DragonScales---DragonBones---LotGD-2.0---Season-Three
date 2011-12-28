<?php
require_once "modules/rail/lib.php";
require_once "common.php";

// This is for random bits of text and tiny surprises to keep the rail system from being boring

function rail_surprise_getmoduleinfo(){
	$info = array(
		"name"=>"Improbable Railway - Surprise!",
		"version"=>"2010-06-13",
		"author"=>"Sylvia Li",
		"category"=>"Improbable Rail",
		"download"=>"",
	"prefs"=>array(
		"Improbable Railway surprises -  User Preferences,title",
		"staminatoday"=>"Has player's stamina been affected today?,int|0",
		"reqstoday"=>"Has player's cash been affected today?,int|0",
		"cigstoday"=>"Has player's cig total been affected today?,int|0",
		"peddlertoday"=>"Has player met the peddler today?,int|0",
		),
	);
	return $info;
}

function rail_surprise_install(){
	module_addhook("newday");
	module_addhook("ironhorse-onboard");
	module_addhook("railpeddler-hascase");
	return true;
}

function rail_surprise_uninstall(){
	return true;
}

function rail_surprise_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "newday":
			set_module_pref("staminatoday",0);
			set_module_pref("reqstoday",0);
			set_module_pref("cigstoday",0);
			set_module_pref("peddlertoday",0);
		break;
		case "ironhorse-onboard":
			switch(e_rand(1,5)){
				case 1:
					// we can have flavor text
					output("`2Soft gaslight illuminates the red leather, brass fittings, and rich walnut paneling of the luxurious passenger coach. On the pillars between the windows curious painted scenes are framed in gold-leaf scrollwork.`0`n`n");
				break;
				case 2:
					// we can give a small benefit
					output("`2A water-boy passes down the aisle, offering passengers cups of refreshing ice-water. You take a long cooling draught. Ahhh!`0`n`n");
					// they can drink as much water as they like, but they only get the stamina benefit once
					if (!get_module_pref("staminatoday")){
						require_once "modules/staminasystem/lib/lib.php";
						addstamina(25000);
						set_module_pref("staminatoday",1);
						output("`2Right away, you feel more energetic.`0`n`n");
					}
					break;
				case 3:
					// ..or we can get a little more complicated
					output("`2Other passengers crowded onto the train at the last stop, jostling and laughing, swinging their backpacks up into the overhead luggage racks. Most of the seats are filled now... some choose to stand in the aisle. `0");
					if (!get_module_pref("reqstoday")){
						$sweets = e_rand(50,150);
						if (($session['user']['gold'] + $session['user']['goldinbank']) <= $sweets){
							// yes, even though she's senile she *can* sense the difference between a millionaire
							// with an empty wallet, and someone who's genuinely broke.
							$sweets = round($sweets * 1.1);
							output("`2A little old lady mistakes you for her grandson Albert. Despite your token protests, she presses %s requisition into your hands. \"`5For sweets,`2\" she insists.`0`n`n",$sweets);
							$session['user']['gold'] += $sweets;
							set_module_pref("reqstoday",1);
						} else if ($session['user']['gold'] > $sweets) {
							// and something different for the more prosperous who are carrying around cash
							$sweets = round($sweets * .5);
							output("`2Wait, something's wrong... Hey, your wallet is gone! Thieves! Pickpockets! Bastards!`n`nLuckily they didn't find your `ireal`i wallet. You only lost %s req.`0`n`n",$sweets);
							$session['user']['gold'] -= $sweets;
							set_module_pref("reqstoday",1);
						}
					}
				break;
				case 4:
					output("`2Tucked between the seat cushions you find a tattered yellow copy of the Enquirer. It's a very old issue -- the headline reads `b`6Season Two Announced`2`b, and turning to the Letters column you note a lively exchange about empty bottles, bugs, fish, nightclubs, and small princesses. The front page article mentions a Secret Feature Revealed: Scrapbots! Hrm.`0`n`n");
				break;
				case 5:
					output("`2The clacking, the swaying, have an almost hypnotic effect. You find yourself wanting to nod off -- but there's no way you're going to get any sleep because some boisterous midgets at the far end of the car have started up a game of darts. One of them pins a crudely drawn paper target to the door at the other end, and with much raucous cursing and laughter they have a quick punch-up to see who goes first. Then the darts start flying, never mind the passengers in between.`0`n`n");
					if ($session['user']['hitpoints'] ==
						$session['user']['maxhitpoints']) {
						$session['user']['hitpoints'] = $session['user']['hitpoints'] - e_rand(1,3);
						output("`2Sure enough, soon you feel the sting of a stray dart. \"`#Watch it, you little buggers!`2\" you snarl. \"`#It's not the Raven Inn here, you know!`2\"`0`n`n");
						}
				break;
				// and so on, like that. Eventually there should be about 20 surprises.
			}
		break;
		case "railpeddler-hascase":
			if (e_rand(1,30) == 1){
				output("`2Among hurrying crowds in the concourse, a scruffy figure approaches a distant passer-by. At first the contestant shakes his head, then looks more interested. He digs out his tobacco pouch and receives something in exchange for a handful of cigarettes. You smile at the memory, patting your own fine leather card case.");
				// This section can be used to let the peddler dispense various rumors.
			} else if (!get_module_pref("peddlertoday") && (e_rand(1,2) == 1)){
				output("Behind you a voice calls out, \"`2Best levver, best levver cases! Bloody `bbadass`b cases! Bleedin' ace! Brung in special fer yer, an' cheap at twice the price!`0\"`n`nWhen you turn around... hey, there's the scruffy peddler who sold you your fine leather card case.`n`n");
				$hid = $args['hid'];
				$rid = $args['rid'];
				addnav("Talk to the peddler");
				addnav("Ask about rumors","runmodule.php?module=rail_surprise&op=rumors&hid=$hid&rid=$rid");
			}
		break;
	}
	return $args;
}

function rail_surprise_run(){
	global $session;
	$op = httpget("op");
	switch ($op){
		case "rumors":
			$hid = httpget("hid");
			$rid = httpget("rid");
			page_header("Ask the peddler");
			output("\"`#You there!`0\" you say. \"`#When you're selling those cases I bet you hear a lot of things. Like, who has what around here?`0\"`n`nThe peddler eyes you speculatively. \"`2Might. Might not. Wotcher lookin' fer?`0\"`n`n\"`#I want to find someone with a particular card. See if we can work a trade.`0\"`n`n\"`2Oh, aye? An' which card wud yer be wantin', then?`0\"`n`n");
			rawoutput("<form action='runmodule.php?module=rail_surprise&op=rumorfinish&hid=".$hid."&rid=".$rid."' method='POST'>");
			rawoutput("\"How about the <input name='cardname' width='20' value=''>?\" ");
			rawoutput("<input type='submit' class='button' value='".translate_inline("Ask")."'>");
			rawoutput("</form>");
			addnav("","runmodule.php?module=rail_surprise&op=rumorfinish&hid=$hid&rid=$rid");

			addnav("Forget it");
			addnav("Move on your way","runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=$rid");
			break;
		case "rumorfinish":
			$hid = httpget("hid");
			$rid = httpget("rid");
			$cardname = httppost("cardname");
			global $itemsettings;
			if (!isset($itemsettings)){
				load_item_settings();
			}
			page_header("Ask the peddler");
			$askagain = 0;
			if ($cardname == ""){
				output("The peddler says, \"`2Wot? Yer'll hafter speak up, mate, I dint get a single word o' dat.`0\"`n`n");
				$askagain = 1;
			} else {
				// find out if a card exists with this verbosename
				$itemfound = 0;
				$item = array();
				$itemname = "";
				foreach ($itemsettings AS $ikey => $itemdetails){
					foreach ($itemdetails AS $setting => $value){
						if (($setting == "verbosename") && ($value == $cardname)){
							$itemfound = 1;
							$item = $itemdetails;
							$itemname = $ikey;
							break;
						}
					}
				}
				if ($itemfound === 0){
					output("The peddler says, \"`2Yer've got bloody marbles fer teef, mate. %s? Dat ain't nuffink I heard of.`0\"`n`n",$cardname);
					$askagain = 1;
				} else {
					if (!$item['tradable']){
						output("The peddler looks at you in surprise. \"`2%s? Yer can't trade `idem`i fings, mate! Wotcher goin' on about?`0\"`n`n",$cardname);
						$askagain = 1;
						} else {
							if ($item['feature'] <> "rail"){
								output("The peddler shrugs. \"`2%s? Wouldn' know nuffink abaht dat, mate. Not zactly my trade.`0\"`n`n",$cardname);
								$askagain = 1;
						}
					}
				}
			}
			if ($askagain){
				output ("You try again. \"`#Sorry, my mistake. What I meant to say was...`0\"`n`n");
				rawoutput("<form action='runmodule.php?module=rail_surprise&op=rumorfinish&hid=".$hid."&rid=".$rid."' method="."'POST'>");
				rawoutput("<input name='cardname' width='20' value=''> ");
				rawoutput("<input type='submit' class='button' value='".translate_inline("Ask")."'>");
				rawoutput("</form>");
				output("\"`#Would you have heard who might have that?`0\"");
				addnav("","runmodule.php?module=rail_surprise&op=rumorfinish&hid=$hid&rid=$rid");

				addnav("Forget it");
				addnav("Move on your way","runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=$rid");
			} else {
				// Okay, so we have a request. Is there someone who has this card?
				// This was the old way, finding it in the midst of a serialized array with LIKE:
				// $sql = "SELECT userid FROM ".db_prefix(module_userprefs)." WHERE modulename = 'iitems' AND setting = 'items'"." AND value LIKE '%".$cardname."%' AND userid != '".$session['user']['acctid']."' ORDER BY RAND() LIMIT 1";
				// The new way is easier:
				$sql = "SELECT * from ".db_prefix(items_player)." WHERE item = '".$itemname."' AND owner != '".$session['user']['acctid']."' ORDER BY RAND() LIMIT 1";
				// whether they get an answer or not, they only get to ask this question once a day
				set_module_pref("peddlertoday",1);
				$result = db_query($sql);
				$row = db_fetch_assoc($result);
				$rumor = $row['owner'];
				// Now let's see if the peddler knows, or will admit to knowing, anything about it.
				if (db_num_rows($result) && (e_rand(1,2) == 1) && ($session['user']['gems'] >= 1)){
					output("The peddler gets a knowing look. \"`2Well now, I might have a name fer yer, mate, but it'll cost yer.`0\" There's a meaningful glance at your tobacco pouch.`n`n");
					rawoutput("<form action='runmodule.php?module=rail_surprise&op=bribefinish&hid=".$hid."&rid=".$rid."&r=".$rumor."' method="."'POST'>");
					rawoutput("<input type='submit' class='button' value='".translate_inline("Offer a bribe of one cig")."'>");
					rawoutput("</form>");
				addnav("","runmodule.php?module=rail_surprise&op=bribefinish&hid=$hid&rid=$rid&r=$rumor");
				} else {
					output("\"`2Ehhhh, sorry, mate, ain't heard nuffink 'bout dat. I'll ask around. Might have somefink more fer yer t'morrer.`0\"`n`n");
				}
				addnav("Forget it");
				addnav("Move on your way","runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=$rid");
			}
			break;
			case "bribefinish":
			$hid = httpget("hid");
			$rid = httpget("rid");
			$rumor = httpget("r");			
			page_header("Bribe the peddler");
			$sql = "SELECT name FROM " . db_prefix("accounts") . " WHERE acctid = " . $rumor;
			$result = db_query($sql);
			if (db_num_rows($result) == 1) {
				$row=db_fetch_assoc($result);
				$name = $row['name'];
				output("The peddler accepts the cig with a quick grin. \"`2Yer a right good 'un, mate. Proper ace. Word on the street is, `b%s`b`2 'as got jest wot yer lookin' fer. Best go 'ave a chat soon-like, I'm thinkin', afore it gets traded away to some'un else. Laters!`0\"`n`nBefore you can ask any more questions the scruffy figure wanders off with a cheerful wave and disappears amongst the crowd.",$name);
			} else {
				// this shouldn't happen, but if it does -- hey, the peddler will just lie.
				output("The peddler makes the cig disappear quickly. \"`2Wot I 'ears is, the one 'oo 'as one o' dem is `b`@Elias`2`b hisself. A hard'un t'find, but 'e likes t'elp people, so 'e might be willin' t'elp yer out wiv dis. Good luck wiv da Lucky Dip, mate!\"`n`nWhile you're still blinking, the scruffy figure slips off with a cheerful wave and vanishes into the crowd.");
			}
			$session['user']['gems']--;
			addnav("Thanks!");
			addnav("Move on your way","runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=$rid");
			break;
	}		// end switch $op
	page_footer();
	return true;
}
?>