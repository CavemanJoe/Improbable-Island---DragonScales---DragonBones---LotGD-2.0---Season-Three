<?php
// addnews ready
// translator ready
// mail ready
require_once("lib/villagenav.php");

function forest($noshowmessage=false) {
	global $session,$playermount;
	tlschema("forest");
//	mass_module_prepare(array("forest", "validforestloc"));
	addnav("Heal");
	addnav("H?Hospital Tent","healer.php");
	addnav("Fight");
	addnav("L?Look for Something to Kill","forest.php?op=search");
	if ($session['user']['level']>1)
		addnav("S?Go Slumming","forest.php?op=search&type=slum");
	addnav("T?Go Thrillseeking","forest.php?op=search&type=thrill");
	if (getsetting("suicide", 0)) {
		if (getsetting("suicidedk", 10) <= $session['user']['dragonkills']) {
			addnav("*?Search `\$Suicidally`0", "forest.php?op=search&type=suicide");
		}
	}
	if ($session['user']['level']>=15  && $session['user']['seendragon']==0){
		// Only put the green dragon link if we are a location which
		// should have a forest.   Don't even ask how we got into a forest()
		// call if we shouldn't have one.   There is at least one way via
		// a superuser link, but it shouldn't happen otherwise.. We just
		// want to make sure however.
		$isforest = 0;
		$vloc = modulehook('validforestloc', array());
		foreach($vloc as $i=>$l) {
			if ($session['user']['location'] == $i) {
				$isforest = 1;
				break;
			}
		}
		if ($isforest || count($vloc)==0) {
			addnav("G?`@Seek Out the Green Dragon","forest.php?op=dragon");
		}
	}
	addnav("Other");
	villagenav();
	
	if ($noshowmessage!=true){
		$foresttext = array();
		tlschema();
		$foresttext[] = translate_inline("The Jungle, home to the vicious creatures of Doktor Improbable's obscene laboratories - and to various evildoers of all descriptions.`n`nThe thick foliage of the jungle restricts your view to only a few yards in most places.  The paths would be imperceptible except for your trained eye.  You move as silently as a soft breeze across the thick moss covering the ground, wary to avoid stepping on a twig or any of the numerous pieces of bleached bone that populate the jungle floor, lest you betray your presence to one of the vile beasts that wander this place.`n`nThen you think \"`#Sod it,`0\" and tear off looking for something to kill.`n`n");
		$foresttext = modulehook("forest-desc",$foresttext);
		output($foresttext);
	}
	modulehook("forest", array());
	module_display_events("forest", "forest.php");
	addnav("Inventory");
	addnav("View your Inventory","inventory.php?items_context=forest");
	tlschema();
}

?>
