<?php

function improbablehousing_secretrooms_getmoduleinfo(){
	$info=array(
		"name"=>"Improbable Housing: Secret Rooms",
		"version"=>"2010-07-20",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"Housing",
		"download"=>"",
		"settings"=>array(
			"cost"=>"Cost in Donator Points for a Secret Room,int|500",
		),
	);
	return $info;
}

function improbablehousing_secretrooms_install(){
	module_addhook("commentarycommand");
	module_addhook("improbablehousing_interior");
	return true;
}

function improbablehousing_secretrooms_uninstall(){
	return true;
}

function improbablehousing_secretrooms_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "commentarycommand":
			if (!httpget("hid")){
				break;
			} else {
				require_once "modules/improbablehousing/lib/lib.php";
				$hid = httpget("hid");
				$rid = httpget("rid");
				$house = improbablehousing_gethousedata(httpget("hid"));
				
				$secrets = array();
				foreach($house['data']['rooms'] AS $rkey=>$rvals){
					if ($rkey!=$rid){
						if ($rvals['enterfrom']==$rid && $rvals['hidden']){
							$secrets[$rkey] = $rvals;
						} else if ($rkey==$house['data']['rooms'][$rid]['enterfrom']){
							$secrets[$rkey] = $rvals;
						}
					}
				}
				
				if (count($secrets)){
					foreach ($secrets AS $rkey => $rvals){
						if (count($rvals['triggers'])){
							foreach($rvals['triggers'] AS $trigger){
								if ($args['command']==$trigger){
									$args['processed']=1;
									if (improbablehousing_canenter_room($house,$rkey)){
										redirect("runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=$rkey");
									} else {
										output_notl("%s`n`n",$rvals['lockreject']);
									}
								}
							}
						}
					}
				}
				
			}
			break;
		case "improbablehousing_interior":
			$hid = $args['hid'];
			$rid = $args['rid'];
			$house = $args['house'];
			if (improbablehousing_getkeytype($house,$rid)==100){
					addnav("Donator Features");
					addnav("Build a Secret Room","runmodule.php?module=improbablehousing_secretrooms&op=start&hid=$hid&rid=$rid");
				if ($house['data']['rooms'][$rid]['hidden']){
					addnav("Secret Room Features");
					addnav("Manage this room's trigger phrases","runmodule.php?module=improbablehousing_secretrooms&op=manage&hid=$hid&rid=$rid");
				}
			}
		break;
	}
	return $args;
}

function improbablehousing_secretrooms_run(){
	global $session;
	page_header("Secret Rooms");
	require_once "modules/improbablehousing/lib/lib.php";
	$hid = httpget("hid");
	$rid = httpget("rid");
	$house = improbablehousing_gethousedata($hid);
	$cost = get_module_setting("cost");
	$roomname = $house['data']['rooms'][$rid]['name'];
	
	switch (httpget('op')){
		case "start":
			output("`5Secret Rooms`0 are rooms that exist in your Dwelling and operate just like normal rooms - except that there's no nav link for players to reach them by.  Instead, a player will be instantly transported from the current room to your `5Secret Room`0 by typing a command into their chat box.  Commands in Secret Rooms work like commands in other parts of the game - anything entered in ALL CAPS is treated as a command, rather than as speech.  If you've never typed \"PAUSE\" in a chat box, give it a go.`n`nYou as the secret room owner can assign as many key phrases per room as you would like.  For example, you might assign the key phrases \"`#EXAMINE BOOKCASE`0\", \"`&LOOK BEHIND BOOKCASE`0\", and perhaps a few others, to have the player discover a traditional secret passage.  Or, you could assign the phrase \"`#OPEN SESAME`0\" to reveal a cleverly-hidden door.`n`nIn all other aspects, `5Secret Rooms`0 operate exactly like normal rooms - they can be extended, decorated, and can have more rooms branch from them (including other `5Secret Rooms`0).  They can even be locked just like normal rooms - if a player tries to enter a locked `5Secret Room`0 to which he or she doesn't have access, they'll get bounced straight back as usual.`n`n`5Secret Rooms`0 are a supporter-only feature, cost %s Supporter Points per room, and come pre-assembled.  No construction materials are necessary; `5Secret Rooms`0 arrive ready to decorate.`n`nYou're currently building a `5Secret Room`0 that can be accessed from the room called \"`b%s`b`0\" - are you sure that this is the room you want to build from?`n`n",$cost,$roomname);
			addnav("Want a Secret Room?");
			$donationavailable = $session['user']['donation'] - $session['user']['donationspent'];
			if ($donationavailable >= $cost){
				addnav(array("Yes, build a Secret Room branching from this one (%s Points)",$cost),"runmodule.php?module=improbablehousing_secretrooms&op=buy&hid=$hid&rid=$rid");
			} else {
				addnav("Ah, but you don't have enough points!","");
			}
			addnav("Cancel and return to the last room","runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=$rid");
		break;
		case "buy":
			$session['user']['donationspent']+=$cost;
			
			//log purchase
			$sql = "INSERT INTO ".db_prefix("purchaselog")." (acctid,purchased,amount,data,giftwrap,timestamp) VALUES ('".$session['user']['acctid']."','housing_secretroom','".$cost."','none','0','".date("Y-m-d H:i:s")."')";
			db_query($sql);
			
			output("You've bought a `5Secret Room!`0  To enter it, just type \"`#SECRET`0\" into the chat box in the room called \"%s\" (you can delete this trigger phrase and set up some new ones once you're inside the secret room).`n`nHave fun!`n`n",$roomname);
			$newroom = array(
				"name"=>"Secret Room",
				"size"=>1,
				"enterfrom"=>$rid,
				"desc"=>"You're standing in a small, undecorated secret room.",
				"sleepslots"=>array(
				),
				"hidden"=>1,
				"triggers"=>array(
					0=>"SECRET",
				),
				"lockreject"=>"You do all the right things, but you still can't get in.  Maybe the room is locked?",
			);
			$house['data']['rooms'][]=$newroom;
			improbablehousing_sethousedata($house);
			addnav("Let's get busy!");
			addnav("Back to your Dwelling","runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=$rid");
		break;
		case "manage":
			if (httpget("save")){
				$posted = httpallpost();
				$nphrases = array();
				foreach($posted AS $post => $phrase){
					$nphrase = stripslashes($phrase);
					if ($nphrase!="" && substr($post,0,6)=="phrase"){
						$nphrases[]=strtoupper($nphrase);
					}
				}
				$house['data']['rooms'][$rid]['triggers']=$nphrases;
				improbablehousing_sethousedata($house);
			}
			$phrases = $house['data']['rooms'][$rid]['triggers'];
			output("You're currently managing the secret room called \"`b%s`b.`0\"  You can set this room's trigger phrases and lock rejection notices below.`n`nRemember, the text that the player inputs must be an exact match.  There's no extra charge for adding more trigger phrases, so more is often better.`n`nTo erase a trigger phrase, just blank the box.`n`n",$roomname);
			
			rawoutput("<form action='runmodule.php?module=improbablehousing_secretrooms&op=manage&save=true&hid=$hid&rid=$rid' method='POST'>");
			rawoutput("<table border='0' cellpadding='2' cellspacing='2'>");
			$phrasecount = 0;
			
			foreach($phrases AS $phrase){
				$class=($phrasecount%2?"trlight":"trdark");
				$phrasecount++;
				rawoutput("<tr class='$class'><td><input name=\"phrase$phrasecount\" value=\"$phrase\"></td></tr>");
			}
			rawoutput("</table>");
			output("`nNow add up to ten new phrases.  Boxes left blank will be ignored.  To add more, just save and you'll get another ten slots.");
			$extracount = $phrasecount+10;
			rawoutput("<table border='0' cellpadding='2' cellspacing='2'>");
			for ($i=$phrasecount;$i<=$extracount;$i++){
				$class=($phrasecount%2?"trlight":"trdark");
				$phrasecount++;
				rawoutput("<tr class='$class'><td><input name=\"phrase$phrasecount\" value=''></td></tr>");
			}
			rawoutput("</table>");
			rawoutput("<input type='submit' class='button' value='".translate_inline("Save")."'");
			rawoutput("</form>");
			addnav("","runmodule.php?module=improbablehousing_secretrooms&op=manage&save=true&hid=$hid&rid=$rid");
			addnav("Return");
			addnav("Back to the Dwelling","runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=$rid");
		break;
	}

	page_footer();
	
	return true;
}
?>