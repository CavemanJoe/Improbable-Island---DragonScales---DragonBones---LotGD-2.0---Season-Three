<?php

//todo:
//kittania training school required
//hook in to staminafood module


function meatsystem_getmoduleinfo(){
	$info = array(
		"name"=>"Meat System",
		"author"=>"Dan Hall AKA CavemanJoe, ImprobableIsland.com",
		"version"=>"2009-04-11",
		"category"=>"General",
		"download"=>"",
		"prefs-creatures"=>array(
			"Meat System,title",
			"meat1"=>"How many portions of Crap Meat will this creature yield for an experienced MeatSmith?,int|0",
			"meat2"=>"How many portions of Half-Decent Meat will this creature yield for an experienced MeatSmith?,int|0",
			"meat3"=>"How many portions of Tasty Meat will this creature yield for an experienced MeatSmith?,int|0",
		),
		"prefs"=>array(
			"Meat System User Prefs,title",
			"carcasses"=>"Array of creature carcasses that can be cleaned,text|0",
			"meatsmith"=>"User has graduated Meat School,bool|0",
			"meat1"=>"How many portions of Crap Meat does the player have,int|0",
			"meat2"=>"How many portions of Half-Decent Meat does the player have,int|0",
			"meat3"=>"How many portions of Tasty Meat does the player have,int|0",
			"hooks-since-last"=>"How many Forest or WorldNav hooks have been triggered since last monster killed,int|0",
		)
	);
	return $info;
}

function meatsystem_install(){
	module_addhook("battle-victory");
	module_addhook("creatureencounter");
	module_addhook("forest");
	module_addhook("worldnav");
	module_addhook("newday");
	require_once "modules/staminasystem/lib/lib.php";
	install_action("Cleaning the Carcass",array(
		"maxcost"=>15000,
		"mincost"=>2000,
		"firstlvlexp"=>500,
		"expincrement"=>1.07,
		"costreduction"=>130,
		"class"=>"Meat"
	));
	install_action("Cooking",array(
		"maxcost"=>8000,
		"mincost"=>1000,
		"firstlvlexp"=>500,
		"expincrement"=>1.05,
		"costreduction"=>70,
		"class"=>"Meat"
	));
	return true;
}

function meatsystem_uninstall(){
	require_once "modules/staminasystem/lib/lib.php";
	uninstall_action("Cleaning the Carcass");
	uninstall_action("Cooking");
	return true;
}

function meatsystem_dohook($hookname,$args){
	global $session;
	if (get_module_pref("meatsmith")==1){
		switch($hookname){
			case "creatureencounter":
				$blank = array();
				set_module_pref("carcasses", $blank);
				set_module_pref("hooks-since-last",0);
			break;
			case "newday":
				if (get_module_pref("meat1") > 0 || get_module_pref("meat2") > 0 || get_module_pref("meat3") > 0){
					output("Your Meat, exposed to the harsh tropical climate and various Improbable bacteria, is crawling away from you on a bed of maggots.  You consider chasing it, but soon decide that neither you nor anybody else would want to eat it in its current state.  With a heavy heart you wave goodbye as it slowly disappears into the jungle.`n`n");
				}
				$blank = array();
				set_module_pref("carcasses", $blank);
				set_module_pref("hooks-since-last",0);
				set_module_pref("meat1",0);
				set_module_pref("meat2",0);
				set_module_pref("meat3",0);
			break;
			case "battle-victory":
				if ($args['type'] == "forest" || $args['type'] == "world"){
					$carcasses = unserialize(get_module_pref("carcasses"));
					if (!is_array($carcasses)) {
						$carcasses = array();
					}
					if (get_module_objpref("creatures", $args['creatureid'], "meat1") > 0 || get_module_objpref("creatures", $args['creatureid'], "meat2") > 0 || get_module_objpref("creatures", $args['creatureid'], "meat3") > 0){
						$carcasses[] = $args['creatureid'];
					}
					set_module_pref("carcasses", serialize($carcasses));
				}
			break;
			case "worldnav":
				$carcasses = unserialize(get_module_pref("carcasses"));
				if (!is_array($carcasses)) {
					$carcasses = array();
				}
				increment_module_pref("hooks-since-last",1);
				if (get_module_pref("hooks-since-last") > 1){
					$carcasses = array();
					set_module_pref("carcasses",$carcasses);
					set_module_pref("hooks-since-last",0);
				}
				if (count($carcasses) > 0 || get_module_pref("meat1") > 0 || get_module_pref("meat2") > 0 || get_module_pref("meat3") > 0){
				addnav("MmmeeeEEEaaat");
				}
				if (count($carcasses) > 0){
					addnav("Clean Carcasses","runmodule.php?module=meatsystem&from=world");
				}
				if (get_module_pref("meat1") > 0 || get_module_pref("meat2") > 0 || get_module_pref("meat3") > 0){
					if (get_module_pref("fullness","staminafood") < 100){
						$cookcost = stamina_getdisplaycost("Cooking");
						addnav(array("Cook up some meat (`Q%s%%`0)", $cookcost),"runmodule.php?module=meatsystem&op=cook&from=world");
					}
				}
			break;
			case "forest":
				$carcasses = unserialize(get_module_pref("carcasses"));
				if (!is_array($carcasses)) {
					$carcasses = array();
				}
				increment_module_pref("hooks-since-last",1);
				if (get_module_pref("hooks-since-last") > 1){
					$carcasses = array();
					set_module_pref("carcasses",$carcasses);
					set_module_pref("hooks-since-last",0);
				}
				if (count($carcasses) > 0 || get_module_pref("meat1") > 0 || get_module_pref("meat2") > 0 || get_module_pref("meat3") > 0){
				addnav("MmmeeeEEEaaat");
				}
				if (count($carcasses) > 0){
					addnav("Clean Carcasses","runmodule.php?module=meatsystem&from=forest");
				}
				if (get_module_pref("meat1") > 0 || get_module_pref("meat2") > 0 || get_module_pref("meat3") > 0){
					if (get_module_pref("fullness","staminafood") < 100){
						$cookcost = stamina_getdisplaycost("Cooking");
						addnav(array("Cook up some meat (`Q%s%%`0)", $cookcost),"runmodule.php?module=meatsystem&op=cook&from=forest");
					}
				}
			break;
		}
	}
	return $args;
}

function meatsystem_run(){
	global $session;
	require_once "modules/staminasystem/lib/lib.php";

	page_header("Meat!");
	addnav("Meat Skills");
	$from = httpget('from');
	
	switch (httpget('op')){
		case "clean":
			$creatureid = httpget('creatureid');
			$carcass = httpget('carcass');
			$meat1 = get_module_objpref("creatures", $creatureid, "meat1");
			$meat2 = get_module_objpref("creatures", $creatureid, "meat2");
			$meat3 = get_module_objpref("creatures", $creatureid, "meat3");
			
			$amber = get_stamina();
			$return = process_action("Cleaning the Carcass");
			
			if ($return['lvlinfo']['levelledup']==true){
				output("`n`c`b`0You gained a level in Cleaning Carcasses!  You are now level %s!  This action will cost fewer Stamina points now, so you can butcher more creatures each day!`b`c`n",$return['lvlinfo']['newlvl']);
			}
			
			$failchance = e_rand(0,100);
			if ($failchance > $amber){
				//failure - the nice meat gets turned into Crap Meat
				$meat1 += $meat2;
				$meat1 += $meat3;
				output("`4You sit down to clean the carcass.  Your exhausted, clumsy incisions make a mockery of the choicest cuts - what is left over can only be described as Crap Meat.  %s bite's-worth, to be precise.  It looks like it was hacked into chunks by a blind woodsman.",$meat1);
				increment_module_pref("meat1",$meat1);
				$carcasses = unserialize(get_module_pref("carcasses"));
				unset($carcasses[$carcass]);
				$carcasses = array_values($carcasses);
				set_module_pref("carcasses", serialize($carcasses));
			} else {
				//success - all meat is sorted
				output("You spend a few minutes up to your elbows in gore, and getting rather hungry.`n");
				if ($meat1 > 0){
					output("You tear off enough to make %s rough bite's-worth of what the locals affectionately call Crap Meat.  It's mostly wobbling chunks of stinking yellow fat, intermingled with the occasional squirmy tendon.`n", $meat1);
				}
				if ($meat2 > 0){
					output("You make swift work of the fattier muscle, and before too long you have %s rough bite's-worth of Half-Decent Meat.`n", $meat2);
				}
				if ($meat3 > 0){
					output("The red, tender slivers of muscle slide easily from the bone, and you wind up with %s rough bite's-worth of Tasty Meat.`n", $meat3);
				}
				increment_module_pref("meat1",$meat1);
				increment_module_pref("meat2",$meat2);
				increment_module_pref("meat3",$meat3);
				$carcasses = unserialize(get_module_pref("carcasses"));
				unset($carcasses[$carcass]);
				$carcasses = array_values($carcasses);
				set_module_pref("carcasses", serialize($carcasses));
				if (get_module_pref("meat1") > 0 || get_module_pref("meat2") > 0 || get_module_pref("meat3") > 0){
					if (get_module_pref("fullness","staminafood") < 100){
						$cookcost = stamina_getdisplaycost("Cooking");
						addnav(array("Cook up some meat (`Q%s%%`0)", $cookcost),"runmodule.php?module=meatsystem&op=cook&from=".$from);
						} else {
						addnav("You're too full to cook, let alone eat","");
					}
				}
			}
		break;
		case "cook":
			output("You whip your your camping stove.  It's time to cook!`n`nWhat will you put in the pan?  You can fit up to 20 bite's-worth of meat in there.  Right now you have %s bite's-worth of Crap Meat, %s bite's-worth of Half-Decent Meat, and %s bite's-worth of Tasty Meat.`n`n",get_module_pref("meat1"),get_module_pref("meat2"),get_module_pref("meat3"));
			rawoutput("<form action='runmodule.php?module=meatsystem&op=cookfinal&from=".$from."' method='POST'>");
			rawoutput("Put in <input name='meat1' width='2' value='0'> bite's-worth of Crap Meat.<br />");
			rawoutput("Put in <input name='meat2' width='2' value='0'> bite's-worth of Half-Decent Meat.<br />");
			rawoutput("Put in <input name='meat3' width='2' value='0'> bite's-worth of Tasty Meat.<br />");
			rawoutput("<input type='submit' class='button' value='".translate_inline("Cook!")."'>");
			rawoutput("</form>");
			addnav("", "runmodule.php?module=meatsystem&op=cookfinal&from=".$from);
		break;
		case "cookfinal":
			$meat1 = httppost("meat1");
			$meat2 = httppost("meat2");
			$meat3 = httppost("meat3");
			
			debug($meat1);
			debug($meat2);
			debug($meat3);
			
			//check for the dumbass player cooking meat that they don't have
			if ($meat1 > get_module_pref("meat1") || $meat2 > get_module_pref("meat2") || $meat3 > get_module_pref("meat3")){
				output("You don't `ihave`i that much meat!`n`n");
				addnav("Sorry, I forgot how to count for a second there.  Let's try this again.","runmodule.php?module=meatsystem&op=cook&from=".$from);
				break;
			}
			
			//check for the dumbass player inputting a negative number
			if ($meat1 < 0 || $meat2 < 0 || $meat3 < 0){
				output("You want to cook `inegative`i meat?  How very Zen of you.`n`n");
				addnav("Abandon your efforts to produce the opposite of meat and try again, pretending that you weren't just trying to cheat.","runmodule.php?module=meatsystem&op=cook&from=".$from);
				break;
			}
			
			//check for the dumbass player trying to put too much meat in the pan
			$totalmeat = ($meat1 + $meat2 + $meat3);
			if ($totalmeat > 20){
				output("Your pan can't hold that much meat, pal.`n`n");
				addnav("Try again, without filling the pan up so much","runmodule.php?module=meatsystem&op=cook&from=".$from);
				break;
			}
			
			//Stamina interaction, including consequences and level-up details.
			$amber = get_stamina();
			$return = process_action("Cooking");
			
			if ($return['lvlinfo']['levelledup']==true){
				output("`n`c`b`0You gained a level in Cooking!  You are now level %s!  This action will cost fewer Stamina points now, so you can cook more tasty meals each day!`b`c`n",$return['lvlinfo']['newlvl']);
			}
			
			$failchance = e_rand(0,100);
			if ($failchance > $amber){
				output("`4You put your meat into the pan, and sit down to stir-fry it.  The hypnotic motion and white-noise sizzling, combined with your tiredness, sends you staring into space.  While your concentration is impaired, the meat bursts into flames.  You jerk back into awareness, and look down sadly at the flaming chunks.  Bummer.");
				for ($i = 0; $i < $meat1; $i++){
					increment_module_pref("meat1",-1);
				}
				
				for ($i = 0; $i < $meat2; $i++){
					increment_module_pref("meat2",-1);
				}
				
				for ($i = 0; $i < $meat3; $i++){
					increment_module_pref("meat3",-1);
				}
				break;
			}
			
			
			//we can now assume that the player is not some sort of cheating reprobate, or trying to cook while dog-tired, and do some cooking!
			for ($i = 0; $i < $meat1; $i++){
				increment_module_pref("nutrition","staminafood",1);
				increment_module_pref("fat","staminafood",3);
				increment_module_pref("fullness","staminafood",1);
				increment_module_pref("meat1",-1);
				addstamina(1000);
			}
			
			for ($i = 0; $i < $meat2; $i++){
				increment_module_pref("nutrition","staminafood",2);
				increment_module_pref("fat","staminafood",2);
				increment_module_pref("fullness","staminafood",1);
				increment_module_pref("meat2",-1);
				addstamina(2000);
			}
			
			for ($i = 0; $i < $meat3; $i++){
				increment_module_pref("nutrition","staminafood",3);
				increment_module_pref("fat","staminafood",1);
				increment_module_pref("fullness","staminafood",1);
				increment_module_pref("meat3",-1);
				addstamina(5000);
			}
			
			output("You fry up your lovely meaty loveliness, and sit down to eat.  You gain some Stamina!`n`n");
		break;
	}

	$carcasses = unserialize(get_module_pref("carcasses"));
	if (!is_array($carcasses)) {
		$carcasses = array();
	}
	if (count($carcasses) > 0){
	output("You look at the spoils of your most recent battle.  They lie bloodied and broken on the ground.  What will you do with them?");
		foreach ($carcasses as $carcassnum => $creatureid){
			$sql = "SELECT creaturename FROM " . db_prefix("creatures") . " WHERE creatureid = " . $creatureid . " ";
			$result = db_query($sql);
			$creature = db_fetch_assoc($result);
			$cleancost = stamina_getdisplaycost("Cleaning the Carcass");
			addnav(array("Clean the carcass of %s (`Q%s%%`0)", $creature['creaturename'], $cleancost), "runmodule.php?module=meatsystem&op=clean&creatureid=".$creatureid."&carcass=".$carcassnum."&from=".$from);
		}
	} else if (httpget('op') != "cook" && httpget('op') != "cookfinal"){
		output("Now only bloody bones lie strewn around the area.  May as well go back, unless you're getting hungry.");
	}
	
	addnav("Fuck it");
	if ($from == "forest"){
		addnav("Return to the Jungle","forest.php");
	} else if ($from == "world"){
		addnav("Return to the World Map","runmodule.php?module=worldmapen&op=continue");
	}
	
	page_footer();
	return true;
}
?>