<?php
function turretdefense_getmoduleinfo(){
    $info = array(
        "name"=>"Turret Defense",
        "version"=>"2010-11-18 1.2.7",
        "author"=>"Cousjava, with help from Devin",
        "category"=>"Improbable Labs",
        "download"=>"",
        "settings"=>array(         
           "maxcobbles"=>"What is the maximun number of cobblestones that can be loaded? A setting of -1 means no limit,int|0",
           "mincobbles"=>"What is the minimum number of cobblestones that can be loaded? This most be positive to ensure a workable system,int|0",
           "cobbledamage"=>"How much damage does each cobblestone do?,int|1",
            "minthreatlevel"=>"The minimum threat level before you can man the turrets,int|40",
	"minmonsterhp"=>"The minimum hp for a monster in a quickload,int|50",
	"maxmonsterhp"=>"The maximim hp for a monster in a quickfight,int|150",
	"monstereachchance"=>"The percentage chance for a monster to reach the player after escaping a turret's fire, int|0"
                ),
        "prefs-city"=>array(
                        "citycobbles"=>"The cobblestones stored int the city's supply,int|0",
                ),
        ); 
   return $info;
}

function turretdefense_install(){
        module_addhook("village");    
        return true;
}


function turretdefense_uninstall(){
	return true;
}

function turretdefense_dohook($hookname,$args){
	global $session;
	switch ($hookname)
	{
	case "village":
		addnav($args["gatenav"]);
		addnav("Man the turrets!","runmodule.php?module=turretdefense");break;	
	default: break;
	}
	return $args;
}

function turretdefense_run()
{
	global $session;
	page_header("Man the turrets!");	
	$minthreat = get_module_setting($name,$module=false);
	$wcgpointsava = get_module_pref("points","wcgpoints")-get_module_pref("spent","wcgpoints");
	$wcgspent = get_module_pref("spent","wcgpoints");
	require_once "modules/cityprefs/lib.php";
	$cid = get_cityprefs_cityid("location",$session['user']['location']);
	switch (httpget('op')){	
	
	
		default:
			switch ($session['user']['location']){
			 case "Improbable Central": output("You climb to the top of the walls, joining the mix of races. A grinning joker turns from the kittymorph he was talking to.\"`qHello there,`0\" he says. \"`qCome to help?\"`n`n");break;
			 case "Cyber City 404": output("You climb to the tob of the walls where a robot is standing guard on the lasers. As you arrive, it swivels towards you. \"`qProgram mutual Assistance?`0\" it offers.`n`n");break;
			 case "NewHome": output("You climb to the top of the Monster Defence Turrets. One of the humans turns to face you. \"`qHere to help?`0\"`n`n");break;
			 case "Kittania": output("You climb to the top of the walls to join the archers. One of the nearby kittymorphs stretches, then turns towards you. \"`qYou're here?`0\" she says. \"`qGood. We `imight`ineed some help.\"`n`n");break;
			 case "New Pittsburgh": output("You climb to the top of the walls. A zombie turns towards you. \"`qBRAAAIIINNS`0\"`n`n");break;
			 case "Squat Hole": output("You stagger up to the top of the fairly low walls. One of the midget guards pulls a cigarette breifly. \"`qYer think yer can 'elp?`0\" He squints at you. \"`qWha's yer fookin' beer?`0\" `n`n"); break;
			 case "Plesantville": output("You climb up to the top of the walls. A mutant tuns to look at you with one of its heads. \"`gHere to help?`0\" it dejects. \"`gWe'll need it,`0\" it predicts with normal mutnat gloominess.`n`n"); break;
			 case "AceHigh": output("You climb to the top of the walls, where the defenders are standing. \"`qHere to help?`0\" one grins.`n`n"); break;
			 default:output("You climb to the top of the walls, where the defenders are standing ready.`n`n");	break;
			}
					
			addnav("Return to Outpost","village.php");
			addnav("Load Cobblestones","runmodule.php?module=turretdefense&op=load");
			require("modules/onslaught.php");
			$threatlvl = onslaught_checkmonsters();
			if ($threatlvl >= $minthreat)
			{
			addnav("Fire turrets!","runmodule.php?module=turretdefense&op=fire");
			}
			break;
			
			
		case "load":
			$citycobbles = turretdefense_checkcobbles();
			output("You decide you are going to help the defense efforts by adding some ammunition. How much do you want to add?`n`n");
			output("You see that there are %s cobbles here already",$citycobbles);
			output("`n`n`0You have `2$wcgpointsava `0cobblestones remaining.");
			rawoutput("<form method=\"post\" action=\"runmodule.php?module=turretdefense&op=loafed\" name=\"loadstones\">Cobblestones:<input type=\"text\" name=\"givecobblestones\"/><input type=\"hidden\" name=\"submitted\" value=\"true\"><input type=\"submit\" value=\"Load\"</form>");
			
			addnav("Go back a step","runmodule.php?module=turretdefense");
			addnav("Return to Outpost","village.php");
			break;
			
			
		case "loafed":		
			if ($_POST[submitted] == true)
			{
				require_once "modules/cityprefs/lib.php";
+				$givecobbles +=$_POST[givecobblestones];
				//$givecobbles +=$_POST[givecobblestones_submitted];
				set_module_pref("spent",$wcgspent+$givecobbles,"wcgpoints");
				//$cid = get_cityprefs_cityid("location",$session['user']['location']);
				$oldcitycobbles = turretdefense_checkcobbles();
				$newdef=$givecobbles + $oldcitycobbles;
				if ($cid){
							set_module_objpref("city",$cid,"citycobbles",$newdef);
						}
				switch ($session['user']['location']){
			 		case "Improbable Central": output("You put %s cobblestones down. The guards quickly pick it up, to fire in various ways.`n`n",$givecobbles);break;
			 		case "Cyber City 404": output("You put %s cobblestones into a machine. Somehow, it manages to turn the cobblestones into fuel for the laser turrets.`n`n",$givecobbles);break;
			 		case "NewHome": output("You put %s cobblestones down. One of the guards thanks you, and immediatly starts chiselling at them, turning them into ammuntion for the machine guns.`n`n", $givecobbles);break;
			 		case "Kittania": output("You put %s cobblestones down. A kittymorph thanks you. Fletching materials are also lying around, ready to turn them into arrows, but no one is doing anything with just yet.`n`n",$givecobbles);break;
			 		case "New Pittsburgh": output("You put %s cobblestones down. What the zombies do with them, you're not quite sure. But they must have `isome`i use for them, even if it only to break the monsters' heads open to get at the brains.`n`n",$givecobbles);break;
					 case "Squat Hole": output("You put %s cobblestones down in a loose pile. The midgets ignore it, too busy drinking another can of beer.`n`n",$givecobbles); break;
			 		case "Plesantville": output("You put %s cobblestones down. A mutant immediatly picks them up, and starts turning them into rifle bullets as fast as only someone with four arms can.`n`n",$givecobbles); break;
					 case "AceHigh": output("You put %s cobblestones down.  It is immediatly sorted into s series of small piles. Looking around, you see cobblestones only in small piles. What the jokers use them for, you're not sure, but you're also not sure if you `iwant`i to know.`n`n",$givecobbles); break;
			 default:output("You put %s cobblestones down, to be turned into ammuntion.`n`n",$givecobbles);break;
				}								
			}
			addnav("Stay at Turrets","runmodule.php?module=turretdefense");
			addnav("Return to Outpost","village.php");break;
			
			
		case "fire":
			require("modules/onslaught.php");
			$lv = onslaught_checkmonsters();
				$num = onslaught_nummonsters();
				$def = onslaught_checkwalls();
				$stopchance = e_rand(0,100);
				if ($lv>100 && $def<$num && $stopchance > 80){
					output("Before you have a chance to fire, a mosnter runs right up to you and attacks! You'd better kill it befire you fire at the rest of the oncoming horde.`n`n");
					addnav("Oh dear...");
					addnav("Fight!","runmodule.php?module=onslaught&op=start&nodesc=1");
				} else {
					addnav("What will you do?");
					output("How many cobbles will you load?");
					rawoutput("<form method=\"post\" action=\"runmodule.php?module=turretdefense&op=fight\" name=\"loaded\"><input type=\"text\" name=\"loadcobblestones\" /><input type=\"hidden\" name=\"loadcobbles_submitted\" value=\"true\">");
					addnav("","runmodule.php?module=turretdefense&op=fight");				
			}			
			addnav("Return to Outpost","village.php");break;
			
			
		case "fight":
			if ($_POST[loadcobbles_submitted] == true){
				$loadedcobbles = $_POST[loadcobbles];
				$cobbles = turretdefense_checkcobbles();
				if ($loadedcobbles <= $cobbles){
					$minload = get_module_setting("mincobbles");
					if ($loadedcobbles > $minload){
					//$maxload = get_module_setting("maxcobbles");
					//$maxload = get_module_setting("maxcobbles");
					if ($loadedcobbles < $maxload){
						$cid = get_cityprefs_cityid("location",$session['user']['location']);
						$oldcitycobbles = turretdefense_checkcobbles();
						$newdef=$oldcitycobbles - $loadedcobbles;
						if ($cid){
							set_module_objpref("city",$cid,"citycobbles",$newdef);
						}
						$quickfighthp = e_rand(get_module_setting("minmonsterhp"),get_module_setting("maxmonsterhp"));
						$cobbledamage = $loadedcobbles * get_module_setting("cobbledamage");
						global $badguy;
						turretdefense_getenemy();
						require_once("modules/onslaught.php");
						require_once "modules/staminasystem/lib.php";
						$stamina = get_stamina();
						if ($cobbledamage >= $quickfighthp && e_rand(100)<=onslaught_checkmonsters() && e_rand(50)<$stamina)
						{
							switch ($session['user']['location']){
			 	case "Improbable Central": 
			 		if ($session['user']['race'] == Human)
			 		{
			 			output("You fire your machine gun into the oncoming horde, killing a %s.",$badguy['creaturename']);
			 			break;
			 		}
			 		if ($session['user']['race'] == Midget)
			 		{
			 			output("You toss a cobblestone into the oncoming horde, knocking %s on the head and killing it.",$badguy['creaturename']);
			 			break;
			 		}
			 		if ($session['user']['race'] == Zombie)
			 		{
			 			output("Refrained from giving into your more primal urges, you just pick up a nearby weapon and fire it, somehow killing a %s.",$badguy['creaturename']);
			 			break;
			 		}
			 		if ($session['user']['race'] == Kittymorph)
			 		{
			 			output("You fire your bow at the oncoming horde, managing to kil a ^%s.",$badguy['creaturename']);
			 			break;
			 		}
			 		if ($session['user']['race'] == Mutant)
			 		{
			 			output("You fire your rifle into mass of monsters, some of which have the same number of limbs as you. But depsite that, you have no qualms about killing a %s with your weapon.",$badguy['creaturename']);
			 			break;
			 		}
			 		if ($session['user']['race'] == Robot)
			 		{
			 			output("Using your high-spec vision, you carefully fire your laser, which burns right through a %s, killing it.",$badguy['creaturename']);
			 			break;
			 		}
			 		if ($session['user']['race'] == Joker)
			 		{
			 			output("You pick up a cobblestone. You bring it up to your mouth and you gently blow on it. It flies away into the horde of monsters. There is a bright green flash, and a moment later the shockwave reaches the city. There is a stunned silence for a moment. One of the humans says \"`qWow,`0\" and you take a bow, as the monster continue in their frenzied rush against the city.`n`n");
			 			break;
			 		}
			 		if ($session['user']['race'] == Stranger)
			 		{
			 			output("A %s disappears from within the horde...",$badguy['creaturename']);
			 			break;
			 		}
			 		output("You fire the turret, and it fires straight into the mass of monsters, killing %s outright.");
			 		//There may be some way of getting the switch to work, otherwise I'll just you the if statements.
			 		/*switch($session['user']['race']{
			 			case "Human": output("You fire your machine gun into the oncoming horde, killing a %s.",$badguy['creaturename']);
			 			break;
			 			case "Midget": output("You toss a cobblestone into the oncoming horde, knocking %s on the head and killing it.",$badguy['creaturename']);
			 			break;
			 			case "Zombie": output("Refrained from giving into your more primal urges, you just pick up a nearby weapon and fire it, somehow killing a %s.",$badguy['creaturename']);
			 			break;
			 			case "Kittymorph": output("You fire your bow at the oncoming horde, managing to kil a ^%s.",$badguy['creaturename']);
			 			break;
			 			case "Mutant": output("You fire your rifle into mass of monsters, some of which have the same number of limbs as you. But depsite that, you have no qualms about killing a %s with your weapon.",$badguy['creaturename']);
			 			break;
			 			case "Robot": output("Using your high-spec vision, you carefully fire your laser, which burns right through a %s, killing it.",$badguy['creaturename']);
			 			break;
			 			case "Joker": output("You pick up a cobblestone. You bring it up to your mouth and you gently blow on it. It flies away into the horde of monsters. There is a bright green flash, and a moment later the shockwave reaches the city. There is a stunned silence for a moment. One of the humans says \"`qWow,`0\" and you take a bow, as the monster continue in their frenzied rush against the city.`n`n");
			 			break;
			 			case "Stranger": output("A %s disappears from within the horde...",$badguy['creaturename']);
			 			break;
			 			default: output("You fire the turret, and it fires straight into the mass of monsters, killing %s outright.");	
			 			break;
			 	}*/
			 break;
			 case "Cyber City 404": output("You carefully aim the laser with its optimized scope, and fire it bang on target, killing a %s outright.`n`n",$badguy['creaturename']);break;
			 case "NewHome": output("You fire one of the machine guns in a quick burst, managing to kill a %s in the process.`n`n",$badguy['creaturename']);break;
			 case "Kittania": output("You fire your bow, which manages to kill %sn`n",$badguy);break;
			 case "New Pittsburgh": output("You desperatly fire the cobblestone with a match. It lands in the mass of monsters where it explodes, killing %s. Destroying a monster completly is the only way a zombie won't be distracted.`n`n",$badguy['creaturename']);break;
			 case "Squat Hole": output("You toss a cobblestone at the enemy. It manages to knock %s on the head and kill it. \"`#Got your conk!`0\" you jeer. `n`n",$badguy['creaturename']); break;
			 case "Plesantville": output("You fire a rifle, which goes straight and manages to kill a %s. \"`gWell done,`0\"a nearby guard praises. \"`gBut there's more. We will never defeat them all.`0\"`n`n",$badguy['creaturename']); break;
			 case "AceHigh": output("You flick a cobblestone into the oncoming horde. It seems to travel far too slowly to manage to hit anything , but it finally does hit a %s that didn't get out of the way. When you next manage to see after the explosion, you just see a patch of green, green grass.`n`n", $badguy['creaturename']); break;
			 default:output("You fire the turret, and it fires straight into the mass of monsters, killing %s outright.",$badguy['creaturename']); break;
			}
							
							addnav("Return");
							addnav("Outpost","village.php");
							addnav("Reload","runmodule.php?module=turretdefense&op=fire");
							require_once "modules/cityprefs/lib.php";
							//$cid = get_cityprefs_cityid("location",$session['user']['location']);
							if ($cid){
								$creatures = get_module_objpref("city",$cid,"creatures");
								$creatures--;
								if ($creatures>0){
									set_module_objpref("city",$cid,"creatures",$creatures);
								}
							}
						} else {
							require_once("modules/onslaught.php");
							$monsterreachc = get_module_setting("monsterreachchance")*onslaught_checkmonsters();
							require_once("modules/staminasystem/lib.php");
							$stamina = get_stamina();
							$monsterreach = e_rand(100)*$stamina;
							if ($monsterreach < $monsterreachc){
								global $battle;
								if ($battle==true){
								output("Before you have time to reload something rushes up from behind...`n`nYou have encountered %s!`n`n",$badguy['creaturename']);
									apply_buff('start',array(
										"name"=>"Monster Suprise",
										"rounds"=>1,
										"wearoff"=>"The advantage of suprise worn off, you can now fight slighly harder.",
										"atkmod"=>0.9,
										"defmof"=>0.9,
										"expireafterfight"=>1,
										"startmsg"=>"The monster takes you by suprise, leaving you to fumble your weapon!",
										"schema"=>"module-turretdefense",
									));
									apply_buff('cobbleland',array(
										"name"=>"",
										"rounds"=>1,
										"wearoff"=>"",
										"minioncount"=>1,
										"maxbadguydamage"=>$cobbledamage,
										"effectmsg"=>"The cobblestone you fired reaches {badguy} and manages to do {damage} damage.",
										"schema"=>"module-turretdefense"			
									));
									while ($battle==true){
									include_once("battle.php");}
		if ($victory){
			$experience=e_rand($badguy['creatureexp']*1.25, $badguy['creatureexp']*2);
			$experience=round($experience);
			output("`#You receive `6%s `#experience!`n",$experience);
			$session['user']['experience']+=$experience;
			addnav("Phew!");
			addnav("Reload","runmodule.php?module=turretdefense&op=fire");
			addnav("Return to Outpost","village.php");
		} elseif ($defeat){
			require_once("lib/forestoutcomes.php");
			forestdefeat(array($badguy),"in an Outpost");
		} else {
			require_once("lib/fightnav.php");
			fightnav(true,true);
		}	
							
								} else {//battle
								switch ($session['user']['location']){
			 		case "Improbable Central": 
			 		
					if ($session['user']['race'] == Human)
			 		{
			 			output("You fire your machine gun into the oncoming horde, but miss completly.`n`n");
			 			break;
			 		}
			 		if ($session['user']['race'] == Midget)
			 		{
			 			output("You toss a cobblestone into the oncoming horde, but fail to hit anything.`n`n");
			 			break;
			 		}
			 		if ($session['user']['race'] == Zombie)
			 		{
			 			output("Refrained from giving into your more primal urges, you just pick up a nearby weapon and fire it. Due to your inexpertise, you fail to hit anything.`n`n");
			 			break;
			 		}
			 		if ($session['user']['race'] == Kittymorph)
			 		{
			 			output("You fire your bow at the oncoming horde, which just goes staright into the ground.`n`n");
			 			break;
			 		}
			 		if ($session['user']['race'] == Mutant)
			 		{
			 			output("You fire your rifle into mass of monsters, but your limbs get in each others way and stop you from firing accurately. You miss.`n`n");
			 			break;
			 		}
			 		if ($session['user']['race'] == Robot)
			 		{
			 			output("Despit using your high-spec vision, your laser just burns a hole through air. Nothing is harmed much. Not even thin air.`n`n"); break;
			 			break;
			 		}
			 		if ($session['user']['race'] == Joker)
			 		{
			 			output("You pick up a cobblestone. You bring it up to your mouth and you gently blow on it. It flies away into the horde of monsters. It then slowly sinks down. Nothing more happens to it.`n`n");
			 			break;
			 		}
			 		if ($session['user']['race'] == Stranger)
			 		{
			 			output("Nothing happens...");
			 			break;
			 		}
			 		output("You fire into the oncoming horde, but fail to hit anything.`n`n");
			 		/*switch ($session['user']['race']{
			 	case: "Human": output("You fire your machine gun into the oncoming horde, but miss completly.`n`n");break;
			 	case "Midget": output("You toss a cobblestone into the oncoming horde, but fail to hit anything.`n`n"); break;
			 	case "Zombie": output("Refrained from giving into your more primal urges, you just pick up a nearby weapon and fire it. Due to your inexpertise, you fail to hit anything.`n`n"); break;
			 	case "Kittymorph": output("You fire your bow at the oncoming horde, which just goes staright into the ground.`n`n");break;
			 	case "Mutant": output("You fire your rifle into mass of monsters, but your limbs get in each others way and stop you from firing accurately. You miss.`n`n"); break;
			 	case "Robot": output("Despit using your high-spec vision, your laser just burns a hole through air. Nothing is harmed much. Not even thin air.`n`n"); break;
			 	case "Joker": output("You pick up a cobblestone. You bring it up to your mouth and you gently blow on it. It flies away into the horde of monsters. It then slowly sinks down. Nothing more happens to it.`n`n"); break;
			 	case "Stranger": output("Nothing happens..."); break;
			 	default: output("You fire into the oncomeing horde, but fail to hit anything.`n`n");break;
			 	}*/
			 	break;
			 		case "Cyber City 404": output("You fire the laser turret, but somewhow, despite its high-spec scope, it misses.`n`n");break;
			 		case "NewHome": output("You fire the machine gun, its wide spread of damage amanaging to hit several monsters, but only just.They carry on just as before, tearing down the walls.`n`n");break;
			 		case "Kittania": output("You fire the arrow into the oncoming horde, but miss all of the monsters.`n`n");break;
			 		case "New Pittsburgh": output("You fire the cobblestone into the monsters, but it detonates too soon, failing to kill anything.`n`n");break;
					 case "Squat Hole": output("You toss a cobblestone into the mass of monsters, but you are so full of drink that you miss completly. A nearby midget laughs at you. \"`q'Ere, innit possible to aim, yer fookin' plonker?`0\"`n`n"); break;
			 		case "Plesantville": output("You fire your rifle into the oncoming horde, but your bullet just sails by, missing  completly..`n`n"); break;
					 case "AceHigh": output("You flick the cobblestone into the oncoming horde. It flies through all the gaps between the monsters and down rabbit hole, where it explodes. The poor ordinary rabbit is blown up into the air. The monsters are unharmed.`n`n"); break;
			 default:output("You fire into the oncoming horde, but fail to hit anything.`n`n");break;
								}
								addnav("Try Again");
								addnav("Reload","runmodule.php?module=turretdefense&op=fire");
								addnav("Return to Outpost","village.php");
								}}//battle				
					}}//quickfight				
				} else {//maxload
					switch ($session['user']['location']){
			 		case "Improbable Central": output("You pick up a pile of cobblestones to use with your weapon, but are stopped by one of the guards. \"Don't try to use so many at once,`0\" he says. \"`qWe don't have an unlimited supply, so try to fire only as many as will kill the monster.\"`n`n");break;
			 		case "Cyber City 404": output("You input the amount of cobblestones you want into the input board of the turret, but an error comes up. \"`qERROR 5102: INPUT EXCEEDS MAXMUIUM ALLOWED FOR OPTIMUM DEFENCE\". Maybe it would be better with less cobblestones.`n`n");break;
			 		case "NewHome": output("You pick up a large amount of cobblestones to thread through your machine gun, but are stopped by one of the other guards. \"`qDon't put so many in,`0\" he says. \"`qWe only have a rather limited supply, so only put a few in a time, just enough to kill the monster, so the supply lasts.`0\"`n`n");break;
			 		case "Kittania": output("You pick up are large punch of arrows, but are stopped by another kittymorph. \"Don't use quite so much. The supply won't last forever, so we're only allowed to take so much at a time. But you can always come back for more. If you want to.`0\"`n`n");break;
			 		case "New Pittsburgh": output("You pick up a load of cobblestones, but `n`n");break;
					 case "Squat Hole": output("You are try to pick up the mass of cobblestones, but fail utterly. They fall out of your arms and slip onto the ground. One of the nearby midgets laughs at you. \"`qYer great plonker. 'Ow do yer expect to toss em all? 'Cause yer a blok'ead!\"`n`n"); break;
			 		case "Plesantville": output("You are about to put a load of cobblestone bullets in your rifle, but are stopped by one of the guards. \"`qDon't put so many in,`0\" he cautions. \"`qIf you try to force too many in, the rifle will explode. Though it might anyway. And even if you do fire it, it'll probably miss.`0\" it adds with typical mutant cheerfulness.`n`n"); break;
					 case "AceHigh": output("You reach down to pick up a load of cobblestoones, but just as you do, one of the other guards stops you with a panicked look on his face. \"`qDon't pick up so many,`0\" he says.`n`n\"`#Why?`0\" you ask.`n`n\"`qBecuase when you have too many, it reaches critical mass and the whole thing explodes here. In the outpost.`0\"`n`n"); break;
			 default:output("You put a load of cobbles into the turret, put are stopped by one of the other guards. \"`qDon't put so many in there,`0\" he says. \"`qWe only have a limited supply, so try to make them last.`0\"`n`n");break;
								}
					
					addnav("Try again!");
					addnav("Reload","runmodule.php?module=turretdefense&op=fire");
				}//maxload
				}else{
				output("Oops! You didn't put in any cobbles!");
				addnav("Try again!");
				addnav("Reload","runmodule.php?module=turretdefense&op=fire");
				}				
			} else {// more than exist cobbles
			output("Oops! You tired to load more cobbles than actually exist.");
			addnav("Try again!");
			addnav("Reload","runmodule.php?module=turretdefense&op=fire");
			}//more than exist cobbles
		}//end loadcobblessubmit
			
			
	//end of switch
	page_footer();
}//end of run

function turretdefense_checkcobbles($cid="none"){
	global $session;
	if ($cid=="none"){
		require_once "modules/cityprefs/lib.php";
		$cid = get_cityprefs_cityid("location",$session['user']['location']);
	}
	$def = get_module_objpref("city",$cid,"citycobbles");
	$def = round($def);
	return $def;
}

function turretdefense_getenemy(){
	global $session,$badguy,$battle;
	$sql = "SELECT * FROM " . db_prefix("creatures") . " WHERE forest = 1 ORDER BY rand(".e_rand().") LIMIT 1";
	$result = db_query($sql);
	restore_buff_fields();
	if (db_num_rows($result) == 0) {
		// There is nothing in the database to challenge you,
		// let's give you a doppleganger.
		$badguy = array();
		$badguy['creaturename']="An evil doppleganger of ".$session['user']['name'];
		$badguy['creatureweapon']=$session['user']['weapon'];
		$badguy['creaturelevel']=$session['user']['level'];
		$badguy['creaturegold']= rand(($session['user']['level'] * 1),($session['user']['level'] * 10));
		$badguy['creatureexp'] = round($session['user']['experience']/10, 0);
		$badguy['creaturehealth']=$session['user']['maxhitpoints'];
		$badguy['creatureattack']=$session['user']['attack'];
		$badguy['creaturedefense']=$session['user']['defense'];
	} else {
		$badguy = db_fetch_assoc($result);
		require_once("lib/forestoutcomes.php");
		$badguy = buffbadguy($badguy);
	}
	calculate_buff_fields();
	$badguy['playerstarthp']=$session['user']['hitpoints'];
	$badguy['diddamage']=0;
	$badguy['type'] = 'onslaught';
	$session['user']['badguy']=createstring($badguy);
	$battle = true;
}
?>
