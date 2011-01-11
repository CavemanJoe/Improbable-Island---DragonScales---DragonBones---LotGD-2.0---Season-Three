<?php

function ranksystem_getmoduleinfo(){
	$info = array(
		"name"=>"Rank System",
		"version"=>"2008-12-09",
		"author"=>"Dan Hall",
		"category"=>"General",
		"download"=>"",
		"prefs"=>array(
			"Rank System,title",
			"rank"=>"Player's current difficulty ranking,int|1",
			"rank1"=>"Player has completed this many standard drive runs,int|0",
			"rank2"=>"Player has completed this many Rank 2 drive runs,int|0",
			"rank3"=>"Player has completed this many Rank 3 drive runs,int|0",
			"rank4"=>"Player has completed this many Rank 4 drive runs,int|0",
			"rank5"=>"Player has completed this many Rank 5 drive runs,int|0",
			"rank6"=>"Player has completed this many Rank 6 drive runs,int|0",
			"rank7"=>"Player has completed this many Rank 7 drive runs,int|0",
			"rank8"=>"Player has completed this many Bastard Rank drive runs,int|0",
		),
	);
	return $info;
}
function ranksystem_install(){
	module_addhook("forest");
	module_addhook("dragonkill");
	module_addhook("ramiusfavors");
	module_addhook("newday");
	module_addhook("alter-gemchance");
	module_addhook("biostat");
	module_addhook("healmultiply");
	module_addhook("village");
	return true;
}
function ranksystem_uninstall(){
	return true;
}
function ranksystem_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "forest":
			if ($session['user']['dragonkills'] > 0 && get_module_pref("rank")==0){
				redirect("runmodule.php?module=ranksystem&op=offer","Rank System kicking in");
			};
			ranksystem_applyrankbuff();
			break;
		case "dragonkill":
			$i = get_module_pref("rank");
			increment_module_pref("rank".$i,1);
			set_module_pref("rank",0);
			break;
		case "ramiusfavors":
			if (get_module_pref("rank") > 1){
				addnav("Ask The Watcher to lower your Rank","runmodule.php?module=ranksystem&op=change");
			}
			break;
		case "newday":
			ranksystem_applyrankbuff();
			break;
		case "alter-gemchance":
			global $options;
			if ($options['type'] == "forest") {
				$modifier = get_module_pref("rank") * get_module_pref("rank");
				$chancereduction = ($args['chance']/100)*$modifier;
				$args['chance'] -= $chancereduction;
			}
			break;
		case "healmultiply":
			switch (get_module_pref("rank")){
				case 8:
					$args['alterpct'] = 2.0;
				break;
			}
		break;
		case "biostat":
			output("`0This player has defeated the Improbability Drive on the following difficulty ranks:`n`n");
			$totalrank = 0;
			for ($i=1;$i<=8;$i++){
				$victories = get_module_pref("rank".$i,"ranksystem",$args['acctid']);
				if ($i < 8){
					output("Rank %s: %s time(s)`n",$i,$victories);
				}
				$totalrank += ($victories * $i);
			}
			output("`4Bastard Rank`0: %s time(s)`n",get_module_pref("rank8","ranksystem",$args['acctid']));
			if (get_module_pref("rank","ranksystem",$args['acctid'])<8){
				output("They are currently attempting a Rank %s Drive Kill.`n",get_module_pref("rank","ranksystem",$args['acctid']));
			} else {
				output("This player is currently attempting a `4Bastard Rank`0 Drive Kill!`n");
			}
			$acctid = $args['acctid'];
			$sql = "SELECT dragonkills FROM ".db_prefix("accounts")." WHERE acctid = $acctid";
			$result = db_fetch_assoc(db_query($sql));
			if ($result['dragonkills']>0){
				$averagerank = round(($totalrank / $result['dragonkills']),2);
				output("Their average rank is %s.`n`n",$averagerank);
			}
			break;
		case "village":
			// if (get_module_pref("rank")>7){
				// blocknav("lodge.php");
				// addnav("You can't use the Hunter's Lodge while you're on a Bastard Rank Drive Kill!","");
			// }
			if ($session['user']['dragonkills'] > 0 && get_module_pref("rank")==0){
				redirect("runmodule.php?module=ranksystem&op=offer","Rank System kicking in");
			};
			ranksystem_applyrankbuff();
		break;
		}
	return $args;
}
function ranksystem_run(){
	global $session;
	page_header("Judge Yourself");
	$op = httpget("op");
	$rank = httpget("rank");
	switch($op){
		case "offer":
			page_header("Judge Yourself");
			output("`0As you head into the Outpost, `\$The Watcher`0 appears in front of you and holds up her hand.  You stop where you are.`n`n\"`7We've noticed the audience ratings improving already, and we think you might be the cause.`0\"`n`nYou try to cover your surprise.  Didn't you just get here?`n`n\"`7If you feel like you're up to it,`0\" continues `\$The Watcher`0, looking you up and down, \"`7We can use certain... `imechanisms`i, to make the monsters appear a little harder to you.  It should make for good television, and of course you'll get an increased reward in cigarettes.  Well, do you think you're hard enough?`0\"`n`nYou think for a moment.");
			addnav("Choose a Rank");
			addnav("Proceed with standard difficulty","runmodule.php?module=ranksystem&op=start&rank=1");
			addnav("Proceed at Level Two","runmodule.php?module=ranksystem&op=start&rank=2");
			addnav("Proceed at Level Three","runmodule.php?module=ranksystem&op=start&rank=3");
			addnav("Proceed at Level Four","runmodule.php?module=ranksystem&op=start&rank=4");
			addnav("Proceed at Level Five","runmodule.php?module=ranksystem&op=start&rank=5");
			addnav("Proceed at Level Six","runmodule.php?module=ranksystem&op=start&rank=6");
			addnav("Proceed at Level Seven","runmodule.php?module=ranksystem&op=start&rank=7");
			if (get_module_pref("rank7","ranksystem")>0){
				addnav("`4Bastard Rank`0");
				addnav("`JReady to kick it up a notch?","");
				addnav("`4`bBAAAAASTAAAARD RAAAAAANK!`0","runmodule.php?module=ranksystem&op=start&rank=8");
			}
			page_footer();
			break;
		case "start":
			if ($rank==8){
				output("`\$The Watcher`0 laughs.  \"`7Right, then.  Bastard Rank it is.  I'll see you when my boys bring you back to my Retraining Vessel in a mop bucket.`0\"  Without further ado, she wanders off behind you, chuckling softly.`n`nYou are now attempting a Bastard Rank Drive Kill.");
			} else {
				output("`\$The Watcher`0 smiles.  \"`7Right enough.  Good luck out there.  If it gets too hard for you, come and see me on the FailBoat and I'll lower your rank for you.`0\"  Without further ado, she wanders off behind you.`n`nYour Rank has now been set to level %s.",$rank);
			}
			set_module_pref("rank",$rank);
			ranksystem_applyrankbuff();
			addnav("Let's rock!");
			addnav("Get on with it","village.php");
			break;
		case "change":
			$rank = get_module_pref("rank");
			$rank--;
			output("`\$The Watcher`0 rests her chin on her hand and sighs.`n`n\"`7It's not easy, is it?`0\" she asks.  \"`7Thought you were harder than you actually were, did you?  Well, I'll knock you down to Rank %s.  Maybe you'll be ready for the higher ranks later.`0\"`n`nShe turns back to her monitors.",$rank);
			set_module_pref("rank",$rank);
			ranksystem_applyrankbuff();
			addnav("Return to the Retraining Pits","graveyard.php");
			break;
		}
	page_footer();
	return $args;
}
function ranksystem_applyrankbuff(){
	$rank = get_module_pref("rank","ranksystem");
	switch ($rank){
		case 1:
			strip_buff('rank');
			break;
		case 2:
			apply_buff('rank',array(
				"rounds"=>-1,
				"badguyatkmod"=>1.1,
				"badguydefmod"=>1.1,
				"survivenewday"=>1,
				"schema"=>"module-rank",
			));
			break;
		case 3:
			apply_buff('rank',array(
				"rounds"=>-1,
				"badguyatkmod"=>1.25,
				"badguydefmod"=>1.25,
				"survivenewday"=>1,
				"schema"=>"module-rank",
			));
			break;
		case 4:
			apply_buff('rank',array(
				"rounds"=>-1,
				"badguyatkmod"=>1.5,
				"badguydefmod"=>1.5,
				"survivenewday"=>1,
				"schema"=>"module-rank",
			));
			break;
		case 5:
			apply_buff('rank',array(
				"rounds"=>-1,
				"badguyatkmod"=>2,
				"badguydefmod"=>2,
				"survivenewday"=>1,
				"schema"=>"module-rank",
			));
			break;
		case 6:
			apply_buff('rank',array(
				"rounds"=>-1,
				"badguyatkmod"=>4,
				"badguydefmod"=>4,
				"survivenewday"=>1,
				"schema"=>"module-rank",
			));
			break;
		case 7:
			apply_buff('rank',array(
				"rounds"=>-1,
				"badguyatkmod"=>10,
				"badguydefmod"=>10,
				"survivenewday"=>1,
				"schema"=>"module-rank",
			));
			break;
		case 8:
			apply_buff('rank',array(
				"rounds"=>-1,
				"badguyatkmod"=>25,
				"badguydefmod"=>25,
				"survivenewday"=>1,
				"schema"=>"module-rank",
			));
			break;
	}
}
?>