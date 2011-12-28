<?php

/*

TODO: Negative effects from exhaustion
(chance of injury from amber, chance of KO from red)


//DEPRECATED
//This functionality is in a seperate module instead.

*/

	// page_header("Gathering Materials");
	// $type=httpget('mat');
	// $item=httpget('item');
	// if ($type=="wood"){
		// $lv = process_action("Logging");
		// if ($lv['lvlinfo']['levelledup']==true){
			// output("`n`c`b`0You gained a level in Logging!  You are now level %s!  This action will cost fewer Stamina points now, so you can lumberjack away more each day!`b`c`n",$lv['lvlinfo']['newlvl']);
		// }
		// iitems_give_item("wood");
		// iitems_use_item($item);
		// output("`0You hack away until you have what can only be described as a bloody enormous log, suitable as a part of a quaint little cabin.  It couldn't possibly fit into your backpack, but you stuff it in anyway.`n");
	// } else if ($type=="stone"){
		// $lv = process_action("Stonecutting");
		// if ($lv['lvlinfo']['levelledup']==true){
			// output("`n`c`b`0You gained a level in Stonecutting!  You are now level %s!  This action will cost fewer Stamina points now, so you can cut more stone in a single day!`b`c`n",$lv['lvlinfo']['newlvl']);
		// }
		// iitems_give_item("stone");
		// iitems_use_item($item);
		// output("`0You hack away until you have a neat, roughly-squarish stone suitable for building a lovely cottage.  For some reason you think that your backpack would be a really good place to put it.`n");
	// }
	// require_once "modules/improbablehousing/lib/lib.php";
	// improbablehousing_showgather();
	// addnav("Other");
	// addnav("Show Inventory","runmodule.php?module=iitems&op=inventory&from=worldnav");
	// addnav("Return to the World Map","runmodule.php?module=worldmapen&op=continue");
?>