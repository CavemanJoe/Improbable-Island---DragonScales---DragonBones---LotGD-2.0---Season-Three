<?php

function mementos_feelgood_getmoduleinfo(){
	$info=array(
		"name"=>"Mementos: Feelgood Attributes",
		"version"=>"2011-03-23",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"Mementos",
		"download"=>"",
	);
	return $info;
}

function mementos_feelgood_install(){
	module_addhook("mementos");
	module_addhook("bioinfo");
	return true;
}

function mementos_feelgood_uninstall(){
	return true;
}

function mementos_feelgood_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "mementos":
			if (count($args)){
				require_once "modules/mementos_feelgood/attributes.php";
				$attributes = mementos_feelgood_getattributes();
				foreach($args AS $item => $prefs){
					if (isset($prefs['memento_feelgood_attribute'])){
						$args[$item]['memento_forge_actions'][] = stripslashes(appoencode("Attribute \"`5".$attributes[$prefs['memento_feelgood_attribute']]['name']."`0\" set<br />",true));
					} else {
						$args[$item]['memento_forge_actions'][] = "<a href='runmodule.php?module=mementos_feelgood&op=choosestat&mould=".$item."'>Apply an Attribute to this Memento</a><br />";
						addnav("","runmodule.php?module=mementos_feelgood&op=choosestat&mould=".$item);
					}
				}
			}
		break;
		case "bioinfo":
			//get all items the player carries with the 'memento_feelgood_attribute' flag set
			$inv = load_inventory($args['acctid'],true);			
			$stats = array();
			foreach($inv AS $id=>$prefs){
				//debug($prefs);
				if ($prefs['memento_author'] != $args['acctid'] && isset($prefs['memento_feelgood_attribute'])){
					$stats[$prefs['memento_feelgood_attribute']] += 1;
				}
			}
			
			//debug($stats);
			
			if (count($stats)){
				require_once "modules/mementos_feelgood/attributes.php";
				$attributes = mementos_feelgood_getattributes();
				
				output("`bAwesome Things About This Player`b`n");
				foreach($stats AS $key=>$points){
					if ($points > 1){
						$plural = "Points";
					} else {
						$plural = "Point";
					}
					output("`b%s`b %s in `b%s`b`n%s`n`n",$points,$plural,$attributes[$key]['name'],$attributes[$key]['description']);
				}
			}
		break;
	}
	return $args;
}

function mementos_feelgood_run(){
	global $session;
	page_header("Memento Attributes");
	$mould = httpget('mould');
	$itemname = get_item_pref("verbosename",$mould);
	require_once "modules/mementos_feelgood/attributes.php";
	$attributes = mementos_feelgood_getattributes();
	switch (httpget('op')){
		case "choosestat":
			output("`bWhat are Memento Attributes for?`b`nLet someone know how you feel about them by giving them a special Memento with a stat enhancement.  Like Mementos in themselves, these stat enhancements don't do anything to your character's abilities - they're intended only as trifles to flatter and delight.`n`nSee someone engage in a particularly special bit of rhyming roleplay?  Give them a Red Music Box of +1 Musical Mastery.  If someone else gives the player another Memento with the Musical Mastery stat attached, then their Bio will show that they have two points in Musical Mastery.  You get the idea, I'm sure - it's a tool for showing your appreciation or admiration in a playful way.`n`n`bNo really, what are Memento Attributes `ifor?`i  What do they `ido?`i`b`nThey make people feel good.  That's it!`n`n`bHow much do they cost?`b`nNothing!  They're free!  You can only apply one Attribute to any given Memento type, mind - and you can't change your mind afterwards, so choose carefully.`n`n`bHow to use them`b`nFigure out what you want your Memento to mean, and look for something in the list below that's a good match.  When you click, that Attribute will be applied to all the Mementos that came out of this Mould, and all the Mementos that will ever come out of it in the future.`n`n");
			foreach($attributes AS $key => $vals){
				rawoutput("<strong><a href='runmodule.php?module=mementos_feelgood&op=confirm&assignstat=".$key."&mould=".$mould."'></strong>".stripslashes(appoencode($vals['name']))."</a>: ".stripslashes(appoencode($vals['description']))."<br />");
				addnav("","runmodule.php?module=mementos_feelgood&op=confirm&assignstat=".$key."&mould=".$mould);
			}
		break;
		case "confirm":
			$assignstat = httpget('assignstat');
			$statname = $attributes[$assignstat]['name'];
			output("You're about to assign the Attribute \"%s\" to the Memento mould \"%s\".  Are you sure you want to do that?",$statname,$itemname);
			addnav("Yes!");
			addnav("Do it!","runmodule.php?module=mementos_feelgood&op=confirmfinal&assignstat=".$assignstat."&mould=".$mould);
			addnav("No wait hang on");
			addnav("That's not what I want to do AT ALL.","runmodule.php?module=mementos_feelgood&op=choosestat&mould=".$mould);
		break;
		case "confirmfinal":
			$assignstat = httpget('assignstat');
			output("Attribute applied!");
			//get all the items like this and apply the pref to all of them
			$sql = "SELECT * FROM ".db_prefix("items_prefs")." WHERE setting='memento_originalitem' AND value='".$mould."'";
			$result = db_query($sql);
			while ($row = db_fetch_assoc($result)){
				debug("Setting pref for itemid ".$row['id']);
				set_item_pref("memento_feelgood_attribute",$assignstat,$row['id']);
			}
			set_item_pref("memento_feelgood_attribute",$assignstat,$mould);
		break;
	}

	addnav("Return");
	addnav("Memento Forge","runmodule.php?module=mementos&op=start");
	page_footer();
	return true;
}
?>