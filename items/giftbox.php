<?php

//GIFT BOXES

function giftbox_define_item(){
	set_item_setting("description","A shiny red gift box. Who knows what's inside?","giftbox1");
	set_item_setting("destroyafteruse","true","giftbox1");
	set_item_setting("dkpersist","true","giftbox1");
	set_item_setting("context_forest","true","giftbox1");
	set_item_setting("giftwrap","true","giftbox1");
	set_item_setting("image","giftbox1.png","giftbox1");
	set_item_setting("lodgehooknav","true","giftbox1");
	set_item_setting("verbosename","Red Gift Box","giftbox1");
	set_item_setting("context_village","true","giftbox1");
	set_item_setting("context_worldmap","true","giftbox1");
	set_item_setting("require_file","giftbox.php","giftbox1");
	set_item_setting("call_function","giftbox_use","giftbox1");

	set_item_setting("description","A shiny dark green gift box. Who knows what's inside?","giftbox2");
	set_item_setting("destroyafteruse","true","giftbox2");
	set_item_setting("dkpersist","true","giftbox2");
	set_item_setting("context_forest","true","giftbox2");
	set_item_setting("giftwrap","true","giftbox2");
	set_item_setting("image","giftbox2.png","giftbox2");
	set_item_setting("lodgehooknav","true","giftbox2");
	set_item_setting("verbosename","Dark Green Gift Box","giftbox2");
	set_item_setting("context_village","true","giftbox2");
	set_item_setting("context_worldmap","true","giftbox2");
	set_item_setting("require_file","giftbox.php","giftbox2");
	set_item_setting("call_function","giftbox_use","giftbox2");

	set_item_setting("description","A shiny purple gift box. Who knows what's inside?","giftbox3");
	set_item_setting("destroyafteruse","true","giftbox3");
	set_item_setting("dkpersist","true","giftbox3");
	set_item_setting("context_forest","true","giftbox3");
	set_item_setting("giftwrap","true","giftbox3");
	set_item_setting("image","giftbox3.png","giftbox3");
	set_item_setting("lodgehooknav","true","giftbox3");
	set_item_setting("verbosename","Purple Gift Box","giftbox3");
	set_item_setting("context_village","true","giftbox3");
	set_item_setting("context_worldmap","true","giftbox3");
	set_item_setting("require_file","giftbox.php","giftbox3");
	set_item_setting("call_function","giftbox_use","giftbox3");

	set_item_setting("description","A shiny blue gift box. Who knows what's inside?","giftbox4");
	set_item_setting("destroyafteruse","true","giftbox4");
	set_item_setting("dkpersist","true","giftbox4");
	set_item_setting("context_forest","true","giftbox4");
	set_item_setting("giftwrap","true","giftbox4");
	set_item_setting("image","giftbox4.png","giftbox4");
	set_item_setting("lodgehooknav","true","giftbox4");
	set_item_setting("verbosename","Blue Gift Box","giftbox4");
	set_item_setting("context_village","true","giftbox4");
	set_item_setting("context_worldmap","true","giftbox4");
	set_item_setting("require_file","giftbox.php","giftbox4");
	set_item_setting("call_function","giftbox_use","giftbox4");

	set_item_setting("description","A shiny turquoise gift box. Who knows what's inside?","giftbox5");
	set_item_setting("destroyafteruse","true","giftbox5");
	set_item_setting("dkpersist","true","giftbox5");
	set_item_setting("context_forest","true","giftbox5");
	set_item_setting("giftwrap","true","giftbox5");
	set_item_setting("image","giftbox5.png","giftbox5");
	set_item_setting("lodgehooknav","true","giftbox5");
	set_item_setting("verbosename","Turquoise Gift Box","giftbox5");
	set_item_setting("context_village","true","giftbox5");
	set_item_setting("context_worldmap","true","giftbox5");
	set_item_setting("require_file","giftbox.php","giftbox5");
	set_item_setting("call_function","giftbox_use","giftbox5");

	set_item_setting("description","A shiny light green gift box. Who knows what's inside?","giftbox6");
	set_item_setting("destroyafteruse","true","giftbox6");
	set_item_setting("dkpersist","true","giftbox6");
	set_item_setting("context_forest","true","giftbox6");
	set_item_setting("giftwrap","true","giftbox6");
	set_item_setting("image","giftbox6.png","giftbox6");
	set_item_setting("lodgehooknav","true","giftbox6");
	set_item_setting("verbosename","Light Green Gift Box","giftbox6");
	set_item_setting("context_village","true","giftbox6");
	set_item_setting("context_worldmap","true","giftbox6");
	set_item_setting("require_file","giftbox.php","giftbox6");
	set_item_setting("call_function","giftbox_use","giftbox6");
}

function giftbox_use($args){
	global $session;
	debug($args);
	$cname = get_item_pref("verbosename",$args['giftbox_contains']);
	output("`0You open up the box to find a %s!`0`n`n",$cname);
	
	if (get_item_pref("inventorylocation",$args['giftbox_contains']) == "lodgebag"){
		if (!has_item("lodgebag")){
			give_item("lodgebag");
		}
	} else {
		clear_item_pref("inventorylocation",$args['giftbox_contains']);
	}
	
	change_item_owner($args['giftbox_contains'],$session['user']['acctid']);
	return $args;
}

?>