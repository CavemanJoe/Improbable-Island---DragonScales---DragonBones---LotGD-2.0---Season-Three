<?php

function racejoker_getmoduleinfo(){
	$info = array(
		"name"=>"Race - Joker",
		"version"=>"2009-09-21",
		"author"=>"Dan Hall",
		"category"=>"Races",
		"download"=>"fix this",
		"prefs" => array(
			"Specialty - Joker Talents User Prefs,title",
			"chain"=>"Joker Card Chain,text",
		),
	);
	return $info;
}

function racejoker_install(){
	module_addhook("chooserace");
	module_addhook("setrace");
	// // module_addhook("alternativeresurrect");
	module_addhook("stamina-newday");
	module_addhook("villagetext");
	module_addhook("stabletext");
	module_addhook("validlocation");
	module_addhook("validforestloc");
	module_addhook("moderate");
	module_addhook("changesetting");
	module_addhook("stablelocs");
	module_addhook("racenames");
	module_addhook("startofround-prebuffs");
	return true;
}

function racejoker_uninstall(){
	global $session;
	$vname = getsetting("villagename", LOCATION_FIELDS);
	$gname = get_module_setting("villagename");
	$sql = "UPDATE " . db_prefix("accounts") . " SET location='$vname' WHERE location = '$gname'";
	db_query($sql);
	if ($session['user']['location'] == $gname)
		$session['user']['location'] = $vname;
	// Force anyone who was a Joker to rechoose race
	$sql = "UPDATE  " . db_prefix("accounts") . " SET race='" . RACE_UNKNOWN . "' WHERE race='Joker'";
	db_query($sql);
	if ($session['user']['race'] == 'Joker')
		$session['user']['race'] = RACE_UNKNOWN;
	return true;
}

function racejoker_dohook($hookname,$args){
	//yeah, the $resline thing is a hack.  Sorry, not sure of a better way
	// to handle this.
	// Pass it as an arg?
	global $session,$resline;
	$city = "AceHigh";
	$race = "Joker";
	switch($hookname){
	case "racenames":
		$args[$race] = $race;
		break;
	case "changesetting":
		// Ignore anything other than villagename setting changes
		if ($args['setting'] == "villagename" && $args['module']=="racejoker") {
			if ($session['user']['location'] == $args['old'])
				$session['user']['location'] = $args['new'];
			$sql = "UPDATE " . db_prefix("accounts") .
				" SET location='" . addslashes($args['new']) .
				"' WHERE location='" . addslashes($args['old']) . "'";
			db_query($sql);
			
			$sql = "UPDATE " . db_prefix("module_userprefs") .
				" SET value='" . addslashes($args['new']) .
				"' WHERE modulename='cities' AND setting='homecity'" .
				"AND value='" . addslashes($args['old']) . "'";
			db_query($sql);
			
		}
		break;
	case "chooserace":
		if ($session['user']['dragonkills'] < 12) break;
		output("`0You grin, and pull a six-sided die from your left ear.  <a href='newday.php?setrace=$race$resline'>\"Well, let's see, shall we?\"</a>`n`n", true);
		addnav("`&Joker`0","newday.php?setrace=$race$resline");
		addnav("","newday.php?setrace=$race$resline");
		break;

	case "setrace":
		if ($session['user']['race']==$race){
			output("The gatekeeper's smile doesn't go away.  Instead, it freezes, locks carefully into place - and the gatekeeper begins to pray that he can persuade it to stay there until you go away, or at least until your eyes stop doing that `@green glowy thing`0.  \"`6Yes,`0\" he says, carefully.  \"`6Yes, that would be fine.`0\"`n`nYou roll your die.  It skitters along, making a clattering sound like bone on wood.  The fact that it's still four feet above the ground doesn't seem to faze the gatekeeper, or if it does, he's very good at not letting it show.`n`nIt finally comes to rest, seven spots facing the sun.  You look up and grin, teeth white and gleaming and not entirely friendly.  \"`#It seems I am a Joker!`0\" you exclaim in a breathy growl.`n`nThe gatekeeper nods, and picks up his journal.  \"`6Very good.  As you say.  Jay.  Oh.  Kay.  Are.  Ee.  Joker.  All done.`0\"  He looks up, to see you preparing to roll your die again.  He opens his mouth to ask you what you're doing, and bites his tongue.  Never ask what a Joker is doing; they might tell you.`n`nYou roll your die.  It skitters along the invisible table, bounces against an invisible wall, and comes up at two.`n`n\"`#Shame,`0\" you say.  \"`#You would have been more interesting with some additional eyes.  Still, if the die says two are enough, then who am I to disagree?`0\"`n`nYou give the gatekeeper a grin and a wink, and head into the outpost.`n`nA minute later, he remembers to exhale.  He shudders and puts the kettle on, hoping that it isn't going to turn out to be one of `ithose`i days.");
			
			set_module_pref("homecity",$city,"cities");
			if ($session['user']['age'] == 0)
				$session['user']['location']=$city;
			
		}
		break;

	case "startofround-prebuffs":
		if ($session['user']['race']==$race && $session['user']['alive']){
			$chain = unserialize(get_module_pref("chain"));
			if (!is_array($chain)){
				$chain=array();
			}
			
			$suit = httpget("suit");
			if (!$suit){
				$suit = e_rand(1,4);
			}
			
			switch ($suit){
				case 1:
					$chain[]="heart";
				break;
				case 2:
					$chain[]="diamond";
				break;
				case 3:
					$chain[]="spade";
				break;
				case 4:
					$chain[]="club";
				break;
			}
			$hearts=0;
			$diamonds=0;
			$spades=0;
			$clubs=0;
			$num=0;
			output("You draw a card from your rather Improbable deck, and add it to your hand.`n");
			foreach($chain AS $card){
				rawoutput("<img src=\"images/".$card.".png\">");
				switch ($card){
					case "heart":
						$hearts++;
					break;
					case "diamond":
						$diamonds++;
					break;
					case "spade":
						$spades++;
					break;
					case "club":
						$clubs++;
					break;
				}
				$num++;
			}
			output_notl("`n");

			if ($hearts == $num || $diamonds == $num || $spades == $num || $clubs == $num){
				if ($num>1){
					output("`0With a grin you realise that all your cards match up - Joker tradition now allows you to expand your hand by one card!`n");
				}
			} else {
				if ($num>1){
					output("`0The bottom card from your hand vanishes.`n");
				}
				array_shift($chain);
			}
			set_module_pref("chain",serialize($chain));
			
			//calculate and award buffs
			$bhearts = round(($session['user']['maxhitpoints']/100) * e_rand(($hearts*$hearts*$hearts)/100,($hearts*$hearts*$hearts)/5));
			$bdiamonds = e_rand($diamonds,($diamonds*$diamonds));
			$bspades = (($spades*$spades*$spades)*0.005)+1;
			$bclubs = (($clubs*$clubs*$clubs)*0.005)+1;
			
			if ($bhearts>1){
				apply_buff("Hearts",array(
					"regen"=>$bhearts,
					"allowinpvp"=>1,
					"allowintrain"=>1,
					"rounds"=>1,
					"expireafterfight"=>1,
					"effectmsg"=>"The mysterious power of your Hearts aura causes you to regenerate {damage} hitpoints...`0",
					"schema"=>"module-racejoker",
				));
			}
			if ($bdiamonds>0){
				if ($bdiamonds==1){
					output("`@The reward hopper mounted to the closest camera is briefly enveloped in crackling green lightning.  You step back and deftly catch the single Requisition token that clatters out of it, before heading straight back into the fight!`0`n");
				} else {
					output("`@The reward hopper mounted to the closest camera is briefly enveloped in crackling green lightning.  You step back and deftly catch the `b%s`b Requisition tokens that pour out of it, before heading straight back into the fight!`0`n",$bdiamonds);
				}
				$session['user']['gold']+=$bdiamonds;
			}
			if ($bspades>1){
				apply_buff("Spades",array(
					"defmod"=>$bspades,
					"allowinpvp"=>1,
					"allowintrain"=>1,
					"rounds"=>1,
					"startmsg"=>"You feel an aura of protection gathering around you!",
					"expireafterfight"=>1,
					"roundmsg"=>"Thanks to the power of the Spades suit, {badguy} is having trouble hitting you!",
					"schema"=>"module-racejoker",
				));
			}
			if ($bclubs>1){
				apply_buff("Clubs",array(
					"atkmod"=>$bclubs,
					"allowinpvp"=>1,
					"allowintrain"=>1,
					"rounds"=>1,
					"startmsg"=>"You feel a burning energy in your muscles...",
					"expireafterfight"=>1,
					"roundmsg"=>"Thanks to the power of the Clubs suit, your attacks are more powerful!",
					"schema"=>"module-racejoker",
				));
			}
		}
		break;
		
	// case "alternativeresurrect":
	case "stamina-newday":
		if ($session['user']['race']==$race){
			clear_module_pref("chain");
			racejoker_checkcity();
			
			//new Joker grenade -> improbabombs routine
			
			$bandolier = 0;
			$blprefs = array(
				"inventorylocation" => "fight",
			);
			$bandolier += has_item_quantity("banggrenade",$blprefs);
			$bandolier += has_item_quantity("whoomphgrenade",$blprefs);
			$bandolier += has_item_quantity("zapgrenade",$blprefs);
			
			$count = 0;
			$count += delete_all_items_of_type("banggrenade");
			$count += delete_all_items_of_type("whoomphgrenade");
			$count += delete_all_items_of_type("zapgrenade");
			
			if ($count){
				output("`0All of your Grenades have turned into Improbability Bombs overnight.  Well, that's what happens when you store explosives around such high levels of Improbability.`n");
				$backpack = $count - $bandolier;
				for ($i=0; $i<$backpack; $i++){
					give_item("improbabilitybomb");
				}
				for ($i=0; $i<$bandolier; $i++){
					give_item("improbabilitybomb",$blprefs);
				}
			}
			
			
			//Combat buffs
			$jokerattack = (e_rand(1,20))/10;
			$jokerdefence = (e_rand(1,20))/10;
			$jokerregen = 0;
			$jokerregenchance = e_rand(1,100);
			if ($jokerregenchance>80) {
				$jokerregen = 5-(e_rand(1,10));
			}
			apply_buff("joker",array(
				"name"=>"Joker Bonus - Improbability",
				"atkmod"=>$jokerattack,
				"defmod"=>$jokerdefence,
				"allowinpvp"=>1,
				"allowintrain"=>1,
				"rounds"=>-1,
				"schema"=>"module-racejoker",
				)
			);
			if ($jokerregen<0){
				output("`n`&Today is a bad day.  Your internal organs appear to have shifted themselves into a rather unfortunate configuration.  Internal bleeding will cause you to lose some hitpoints with every round of battle.`n`0");
				apply_buff("jokerregen",array(
					"regen"=>$jokerregen,
					"allowinpvp"=>1,
					"allowintrain"=>1,
					"rounds"=>-1,
					"effectmsg"=>"`3Your internal bleeding causes you to lose {damage} hitpoints.`0",
					"schema"=>"module-racejoker",
					)
				);
			}
			if ($jokerregen>0){
				output("`n`&Today is a good day.  You appear to have discovered the secret of cellular regeneration.  You will gain hitpoints in every battle.`n`0");
				apply_buff("jokerregen",array(
					"regen"=>$jokerregen,
					"allowinpvp"=>1,
					"allowintrain"=>1,
					"rounds"=>-1,
					"effectmsg"=>"`3Your accelerated cellular regeneration causes you to gain {damage} hitpoints.`0",
					"schema"=>"module-racejoker",
					)
				);
			}
			
			$c1 = (e_rand(10,200)/100);
			$c2 = (e_rand(10,200)/100);
			$c3 = (e_rand(10,200)/100);
			$c4 = (e_rand(10,200)/100);
			$c5 = (e_rand(10,200)/100);
			$e1 = (e_rand(10,200)/100);
			$e2 = (e_rand(10,200)/100);
			$e3 = (e_rand(10,200)/100);
			$e4 = (e_rand(10,200)/100);
			$e5 = (e_rand(10,200)/100);
			
			
			//Stamina Buffs
			require_once("modules/staminasystem/lib/lib.php");
			apply_stamina_buff('joker1', array(
				"name"=>"Joker Improbability Modifier: Travelling",
				"class"=>"Travelling",
				"costmod"=>$c1,
				"expmod"=>$e1,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('joker2', array(
				"name"=>"Joker Improbability Modifier: Cooking and Carcass Cleaning",
				"class"=>"Meat",
				"costmod"=>$c2,
				"expmod"=>$e2,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('joker3', array(
				"name"=>"Joker Improbability Modifier: Hunting",
				"class"=>"Hunting",
				"costmod"=>$c3,
				"expmod"=>$e3,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('joker4', array(
				"name"=>"Joker Improbability Modifier: Combat",
				"class"=>"Combat",
				"costmod"=>$c4,
				"expmod"=>$e4,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			apply_stamina_buff('joker5', array(
				"name"=>"Joker Improbability Modifier: ScrapBots",
				"class"=>"ScrapBots",
				"costmod"=>$c5,
				"expmod"=>$e5,
				"rounds"=>-1,
				"roundmsg"=>"",
				"wearoffmsg"=>"",
			));
			
		}
	break;

	case "validforestloc":
	case "validlocation":
			$args[$city]="village-$race";
		break;	
	case "moderate":		
			tlschema("commentary");
			$args["village-$race"]=sprintf_translate("City of %s", $city);
			tlschema();
		break;
		
	case "villagetext":
		racejoker_checkcity();
		if ($session['user']['location'] == $city){
			$args['text']=array("`0You are standing in the heart of AceHigh.  Though relatively new, this town seems to be prospering.  The houses are built of stone and timber frames, and well-dressed gentlemen and ladies stroll about with impeccable manners.  Every now and then, one of them explodes in an astonishing flash of green light, drawing polite applause from those nearby.`n`n");
			$args['schemas']['text'] = "module-racejoker";
			$args['clock']="`0The great clockwork readout at the centre of the city reads `&%s`0.`n`n";
			$args['schemas']['clock'] = "module-racejoker";
			if (is_module_active("calendar")) {
				$args['calendar'] = "`0A smaller contraption next to it reads `&%s`0, `&%s %s %s`0.`n`n";
				$args['schemas']['calendar'] = "module-racejoker";
			}
			$args['title']=array("%s, Home of the Jokers", $city);
			$args['schemas']['title'] = "module-racejoker";
			$args['sayline']="says";
			$args['schemas']['sayline'] = "module-racejoker";
			$args['talk']="`0Nearby some people converse politely:`n";
			$args['schemas']['talk'] = "module-racejoker";
			$new = get_module_setting("newest-$city", "cities");
			if ($new != 0) {
				$sql =  "SELECT name FROM " . db_prefix("accounts") .
					" WHERE acctid='$new'";
				$result = db_query_cached($sql, "newest-$city");
				$row = db_fetch_assoc($result);
				$args['newestplayer'] = $row['name'];
				$args['newestid']=$new;
			} else {
				$args['newestplayer'] = $new;
				$args['newestid']="";
			}
			if ($new == $session['user']['acctid']) {
				$args['newest']="`n`0As you wander your new home, you feel your jaw dropping at the wonders around you.";
			} else {
				$args['newest']="`n`0Wandering the village, jaw agape, is `&%s`0.";
			}
			$args['schemas']['newest'] = "module-racejoker";
			$args['section']="village-$race";
			$args['stablename']="Mike's Chop Shop";
			$args['schemas']['stablename'] = "module-racejoker";
			$args['gatenav']="Village Gates";
			$args['schemas']['gatenav'] = "module-racejoker";
			unblocknav("stables.php");
		}
		break;
	}
	return $args;
}

function racejoker_checkcity(){
	global $session;
	$race="Joker";
	$city=get_module_setting("villagename");

	if ($session['user']['race']==$race){
		//if they're this race and their home city isn't right, set it up.
		if (get_module_pref("homecity","cities")!=$city){ //home city is wrong
			set_module_pref("homecity",$city,"cities");
		}
	}
	return true;
}

function racejoker_run(){

}
?>
