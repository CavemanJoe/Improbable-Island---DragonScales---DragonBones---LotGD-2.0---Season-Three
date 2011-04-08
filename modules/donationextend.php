<?php

function donationextend_getmoduleinfo(){
	$info = array(
		"name"=>"Donation-Driven Extended Play",
		"version"=>"2010-03-07",
		"author"=>"Dan Hall",
		"category"=>"Improbable",
		"download"=>"",
		"override_forced_nav"=>true,
		"allowanonymous"=>true,
		"settings"=>array(
			"extend1cost"=>"Cents a day for first Extend,int|15000",
			"extend1amt"=>"Stamina boost for first Extend,int|50000",
			"extend2cost"=>"Cents a day for second Extend,int|25000",
			"extend2amt"=>"Stamina boost for second Extend,int|100000",
			"kitty"=>"Cents in the Kitty,float|0",
			"lasttick"=>"Last tick timestamp,int|0",
			"lastdonator"=>"Name of last donator,text",
		),
		"prefs"=>array(
			"user_shy"=>"Your name will be shown next to the Donations button if you give money to Improbable Island.  Unless you'd like to remain anonymous?,bool|0",
		),
	);
	return $info;
}

function donationextend_install(){
	module_addhook("stamina-newday");
	module_addhook("donation");
	module_addhook("everyfooter");
	return true;
}

function donationextend_uninstall(){
	return true;
}

function donationextend_dohook($hookname,$args){
	global $session;
	switch ($hookname){
		case "donation":
			require_once "lib/gamelog.php";
			gamelog("Donation registered by donationextend");
			$amt = $args['amt'];
			$id = $args['id'];
			$kitty = get_module_setting("kitty");
			$newkitty = $kitty + $amt;
			set_module_setting("kitty",$newkitty);
			
			//set last player
			if (!get_module_pref("user_shy","donationextend",$id)){
				$sql = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid='$id'";
				$result = db_query_cached($sql,"donationextend_lastdonator",300);
				$row = db_fetch_assoc($result);
				$name = $row['name']."`0";
				set_module_setting("lastdonator",$name);
			} else {
				set_module_setting("lastdonator","Anonymous");
			}
		break;
		case "everyfooter":
		
			//debug($args,true);
			
			// Set up and decrement the kitty
			require_once("lib/bars.php");
			$last = get_module_setting("lasttick");
			$ext1 = get_module_setting("extend1cost");
			$player = get_module_setting("lastdonator");
			$player = appoencode(stripslashes($player));
			$elapsed = time()-$last;
			$dropped = 0;
			$droppertenseconds = get_module_setting("extend1cost")/8640;
			if ($elapsed > 10){
				$dropped = $droppertenseconds;
			}
			$oldkitty = get_module_setting("kitty");
			$newkitty = $oldkitty - $dropped;
			if ($newkitty < 0){
				$newkitty = 0;
			}
			
			//set up display output
			$out = "";
			$sql = "SELECT count(acctid) AS c FROM " . db_prefix("accounts") . " WHERE locked=0";
			$result = db_query_cached($sql,"donationextend_totalplayers",1800);
			$row = db_fetch_assoc($result);
			$totalplayers = $row['c'];
			
			if ($newkitty > $ext1){
				$ext2 = get_module_setting("extend2cost");
				if ($newkitty > $ext2){
					$over = $newkitty - $ext2;
					$bar = simplebar($over,$ext2,70,5,"AA00AA","FFFFFF");
					$overdisp = number_format($over/100, 2);
					
					//represent as time left to end of Special Extend period
					$droppersec =  get_module_setting("extend1cost")/86400;
					$secsleft = round($over/$droppersec);
					$expirationtime = time() + $secsleft;
					
					require_once "lib/datetime.php";
					$expirein = reltime($expirationtime,false);
					
					$out .= "<span style='font-size:smaller'><strong>Special Extend active!</strong><br />".$bar."Thank you for your support!  Special Extend expires in ".$expirein."<br /><a href='runmodule.php?module=donationextend' target='_blank' onclick=\"".popup("runmodule.php?module=donationextend").";return false;\">(what's a Special Extend?)</span></a>";
				} else {
					$moreneeded = $ext2 - $newkitty;
					$bar = simplebar($newkitty-$ext1,$ext2-$ext1,70,5,"00FF00","AA00AA");
					$moredisp = number_format($moreneeded/100, 2);
					$out .= "<span style='font-size:smaller'><strong>Extended Play active!</strong><br />".$bar."\$".$moredisp." more for <a href='runmodule.php?module=donationextend' target='_blank' onclick=\"".popup("runmodule.php?module=donationextend").";return false;\">Special Extend</a>!</span>";
				}
			} else {
				$moreneeded = $ext1 - $newkitty;
				$moredisp = number_format($moreneeded/100, 2);
				$bar = fadebar($newkitty,$ext1,57);
				$perplayer = ($moreneeded/100)/$totalplayers;
				if ($perplayer > 1){
					$perplayerdisp = "about ".round((($moreneeded)/$totalplayers), 4)." cents per player";
				} else {
					$perplayerdisp = "about $".number_format((($moreneeded/100)/$totalplayers), 2)." per player";
				}
				$out .= $bar;
				if ($newkitty > 0){
					$out .= "<span style='font-size:smaller'>\$".$moredisp." more (".$perplayerdisp.") for <a href='runmodule.php?module=donationextend' target='_blank' onclick=\"".popup("runmodule.php?module=donationextend").";return false;\">Extended Play</a>!</span>";
				} else if ($session['user']['loggedin']){
					$out .= "<span class='colDkRed'><strong>The kitty is empty!</strong></span>  Improbable Island is entirely dependant on your donations to survive.  When the meter is empty, the Island and its creators are in trouble!  Someone please chuck a couple of bucks in the hat!";
				}
			}
			
			global $template;
			//debug($template);
			if (strpos($template['footer'],"{paypal_extras}")){
			//if (isset($template['{paypal_extras}'])){
				//debug("Yes!");
				$rep = "paypal_extras";
			} else {
				//debug("No!");
				$rep = "paypal";
			}
			
			//$rep = "paypal_extras";
			
			//insert display output into page
			if (!array_key_exists($rep, $args) || !is_array($args[$rep])){
				$args[$rep] = array();
			}
			array_push($args[$rep],$out);
			
			//write values back to database
			if ($elapsed > 10){
				set_module_setting("lasttick",time());
				set_module_setting("kitty",$newkitty);
			}
			
			//debug($args,true);
			
			
			addcharstat("Misc");
			addcharstat("Misc","testing testing woohoo!");
			
			
		break;
		case "stamina-newday":
			$kitty = get_module_setting("kitty");
			if ($kitty >= get_module_setting("extend1cost")){
				require_once "modules/staminasystem/lib/lib.php";
				$bonus = get_module_setting("extend1amt");
				if ($kitty > get_module_setting("extend2cost")){
					$bonus += get_module_setting("extend2amt");
					output("`0Because Improbable Island's players have been especially generous, today is a `b`5Special Extended Play`0`b day - you `2gain`0 some Stamina!`n`n");
				} else {
					output("`0Because Improbable Island's server costs and advertising budget have been covered quite nicely by donations, today is an `b`2Extended Play`0`b day - you `2gain`0 some Stamina!`n`n");
				}
				if ($session['user']['donation']>0){
					$bonus = $bonus*2;
				}
				addstamina($bonus);
			}
		break;
	}
	return $args;
}

function donationextend_run(){
	popup_header("Extended Play");
	$ext1 = get_module_setting("extend1amt");
	$ext2 = $ext1 + get_module_setting("extend2amt");
	$ext1pct = $ext1/10000;
	$ext2pct = $ext2/10000;
	output("Improbable Island exists ad-free thanks to the generous donations of its players.  Because we don't have any advertisers or corporate sponsors, we can enjoy the sort of content that large businesses wouldn't like to be associated with - McDonald's would withdraw funding if they ever found out about our thousand-eyed Mutant Steaks, and Sony's PR department would faint if they came across the Saga of Budget Horse.`n`nWhen you donate money to Improbable Island, you get:`n`nPoints to spend on fabulous advantages in the Hunter's Lodge - including name colourization and custom titles, cigarettes, Requisition, extra New Days and more`nThe satisfaction of supporting indie game development`nBonus Supply Crates dropped on the World Map`nThe warm squishy feeling you get when you know that a few thousand players have some extra Stamina thanks to you!  Which brings us nicely to...`n`n`bExtended Play!`b`nThe Extended Play feature replaces the old Donations Status bar, which simply gave a readout of how much money the Island had taken in over the calendar month (expressed as a percentage of a fast-food checkout worker's wages, because quite frankly I was pretty skint at the time and kind of pessimistic that this crazy run-a-game-as-a-living thing was even gonna work, but that's a different story).  It had some problems.  Because it took into account only the current calendar month, it'd go from, say, 120%% to -20%% overnight when the month changed (starting off in the red because of the server bill), which was a bit jarring.  Also, nothing happened when the bar was full up!  You didn't get any reward!  Boo.`n`n`bThe new Extended Play readout works differently`b - it's always counting down, at a rate roughly equal to what the Island really needs to bring in so that we can pay the server costs, advertise for new players, and so that I can keep doing this as my day job.  It goes up whenever someone makes a donation, and declines smoothly rather than dropping to zero at the start of a new month.`n`n(yes, to program this I had to add up my rent and food bills and such and work out how much money I needed to live on per second - try it sometime, it's an eye-opener)`n`nWhenever the display says \"`bExtended Play Active`b,\" `bevery player on the Island`b gets an extra %s Stamina points (about %s%%) at each and every New Day (yes, even days triggered by Chronospheres and Instant New Days).  Should we ever do so well that we get the \"`bSpecial Extend`b\" message, `beveryone`b gets a %s-point Stamina bonus (about %s%%).  `bIt doesn't matter if you've never donated before`b - it doesn't matter if you ever do.  Every player gets the bonus, donator or not.`n`nHowever, if you've ever accumulated any Supporter Points at all, the Stamina bonus awarded by Extended Play will be `bdoubled.`b`n`n`bDoubled?`b`nFor the avoidance of doubt:`nYes, if you recruited even `ione`i new player who stuck around long enough to get you some referral points, you get the double bonus.  Even if this new player decided they weren't into text adventures after all.`nYes, if you had even one monster idea accepted, you get the double bonus.  Even if you also sent me twenty-odd terrible monster suggestions that made me want to reach through the screen and take away your keyboard so that you would stop.`nYes, if you've never given any money to the site but still did that monster rating thing, you get the bonus.  Even if you said \"screw it\" and just rated every monster at a 3 until you'd sorted out a hundred ratings.`nYes, if one gloomy day back in 2008 you let the moths out of your wallet and grudgingly donated `ione stinkin' dollar`i to support the server from which you sucked half a gigabyte of text every day for the past six months, you get the double bonus.  Even if you shed tears over that dollar while you spent all the points.`nThe double bonus is a permanent thing.  It doesn't go away, even if you've spent all your Supporter Points - you'll get double the benefit from Extended Play if you `iever had`i any Supporter Points.`n`n`bSo when's a good time to donate?`b`nAha, yes!  If the Extended Play readout is always counting down, but new game days happen at specific times, then some times might be better to donate than others!`n`nWell, sort of.  Maybe.  In very specific situations.  It depends on what the readout's at, and how much you plan to donate.  If it's at zero, and you plan to donate a fiver, then it really doesn't matter when the New Day arrives.  If it says we need nine dollars more for Extended Play, but the New Day just started, then donating a tenner will only really do any good for the people who activate their New Days shortly after you donate (for example, by using a Chronosphere or by logging in after the system-wide New Day has already started) - if no more donations come in before the next system-wide New Day, then the Extended Play will have expired by then.  But then, if the meter says we only need a few more dollars for an Extended Play, and the system-wide New Day is only minutes away, someone else will probably step up and put the extra couple of bucks in the hat - and the meter will have been close to Extended Play in part because of your donation.  So it's all swings and roundabouts, really!`n`n`bCan I opt-out of having my name shown as the last donation?  I'm shy.`b`nYes, check your Preferences.`n`nHave fun!",$ext1,$ext1pct,$ext2,$ext2pct);
	popup_footer();
}

?>
