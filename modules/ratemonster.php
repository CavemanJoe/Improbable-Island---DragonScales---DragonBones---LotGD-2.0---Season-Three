<?php

function ratemonster_getmoduleinfo(){
	$info = array(
		"name"=>"Monster Ratings",
		"version"=>"2010-02-08",
		"author"=>"Dan Hall",
		"category"=>"Improbable",
		"download"=>"",
		"prefs-creatures"=>array(
			"Monster Ratings,title",
			"ratings"=>"How many ratings has this monster received thus far?,int|0",
			"score"=>"Sum of all ratings for this monster,int|0",
		),
		"prefs"=>array(
			"Monster Ratings user prefs,title",
			"info"=>"Array of user's info,viewonly|array()",
		)
	);
	return $info;
}

function ratemonster_install(){
	module_addhook("forest");
	module_addhook("superuser");
	return true;
}

function ratemonster_uninstall(){
	return true;
}

function ratemonster_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "forest":
			$monsterdata = unserialize($session['user']['badguy']);
			$monster = $monsterdata['enemies'][0];
			$mname = $monster['creaturename'];
			if (isset($monster['creatureid'])){
				$mid = $monster['creatureid'];
				//check to see if the player has rated this monster already
				$info = unserialize(get_module_pref("info"));
				$rated = $info['ratedmonsters'];
				if (!is_array($rated)){
					$rated=array();
				}
				if (!in_array($mid,$rated)){
					addnav("Rate your last Monster Encounter");
					addnav("Help us improve Improbable Island - tell us what you thought of your last monster encounter","");
					addnav("Rate a monster!","runmodule.php?module=ratemonster&op=start&monster=$mid");
				}
			}
			break;
		case "superuser":
			addnav("View Monster Ratings","runmodule.php?module=ratemonster&op=superuser");
			break;
		}
	return $args;
}

function ratemonster_run(){
	global $session;
	page_header("Thanks for helping!");
	$cid = httpget('monster');
	switch (httpget('op')){
		case "start":
			//Look up monster name
			$sql = "SELECT creaturename FROM ".db_prefix("creatures")." WHERE creatureid=$cid";
			$result = db_query($sql);
			$creature = db_fetch_assoc($result);
			$cname = $creature['creaturename'];
			output("Your feedback is important.  Information on what monsters you think are the funniest or most interesting, as well as monsters you find boring, will help both staff and players to write better monsters in the future.  When rating a monster, take into account how interesting, amusing, exciting, or utterly crap the monster was.  Right now we're most interested in the quality of the writing - so please don't rate monsters according to their difficulty (we'll be asking about that soon, once we've weeded out the crap monsters).  Thanks!`n`nYou are now rating the monster \"%s.\"`n`n",$cname);
			addnav("Rate this monster");
			addnav("5: Awesome","runmodule.php?module=ratemonster&op=rate&monster=$cid&rating=5");
			addnav("4: Pretty good","runmodule.php?module=ratemonster&op=rate&monster=$cid&rating=4");
			addnav("3: Average","runmodule.php?module=ratemonster&op=rate&monster=$cid&rating=3");
			addnav("2: Poor","runmodule.php?module=ratemonster&op=rate&monster=$cid&rating=2");
			addnav("1: Terrible","runmodule.php?module=ratemonster&op=rate&monster=$cid&rating=1");
		break;
		case "rate":
			$rating = httpget('rating');
			$currentscore = get_module_objpref("creatures", $cid, "score");
			$currentcount = get_module_objpref("creatures", $cid, "ratings");
			$currentscore += $rating;
			$currentcount++;
			set_module_objpref("creatures", $cid, "score", $currentscore);
			set_module_objpref("creatures", $cid, "ratings", $currentcount);
			$avg = round($currentscore/$currentcount,2);
			output("Thank you!  This monster has an average rating of %s over %s votes.`n`n",$avg,$currentcount);
			//Now ensure the player can't vote for this monster again
			$info = unserialize(get_module_pref("info"));
			$info['ratedmonsters'][]=$cid;
			$rated = $info['ratedmonsters'];

			if (count($rated)>=100 && !$info['gotdps']){
				$session['user']['donation']+=1000;
				$info['gotdps']=true;
				output("`c`bYou have Donator Points!`c`bThank you so much for rating all those monsters!  Here's a 1000 Donator Point bonus.  Have fun with that!`n`n");
			}

			set_module_pref("info",serialize($info));
		break;
		case "superuser":
			$out = array();
			//for each monster, retrieve votes and averages
			$sql = "SELECT creaturename, creatureid, creaturelevel FROM ".db_prefix("creatures")."";
			$result = db_query($sql);
			$max = db_num_rows($result);
			for ($i=0; $i<$max; $i++){
				$monster = db_fetch_assoc($result);
				$votes = get_module_objpref("creatures",$monster['creatureid'],"ratings");
				$score = get_module_objpref("creatures",$monster['creatureid'],"score");
				if ($votes){
					$entry = array();
					$entry['name'] = $monster['creaturename'];
					$entry['level'] = $monster['creaturelevel'];
					$entry['id'] = $monster['creatureid'];
					$entry['avg'] = number_format($score/$votes,3);
					$entry['tot'] = $score;
					$entry['num'] = $votes;
					$out[]=$entry;
				}
			}
			usort($out, 'ratings_compare');
			array_reverse($out);
			rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' width='100%'>");
			rawoutput("<tr class='trhead'><td>Creature Name</td><td>Creature Level</td><td>Creature ID</td><td>Vote Count</td><td>Score</td><td>Average Vote</td></tr>");
			$i=0;
			foreach($out AS $key=>$vals){
				$i++;
				rawoutput("<tr class='".($i%2?"trdark":"trlight")."'>");
				rawoutput("<td>".$vals['name']."</td><td>".$vals['level']."</td><td>".$vals['id']."</td><td>".$vals['num']."</td><td>".$vals['tot']."</td><td>".$vals['avg']."</td></tr>");
			}
			rawoutput("</table>");
			addnav("Back to the Grotto","superuser.php");
		break;
	}
	addnav("Back to the game");
	addnav("J?Return to the Jungle","forest.php");
	page_footer();
}

function ratings_compare($a, $b){
	return strnatcmp($a['avg'], $b['avg']);
}

?>
