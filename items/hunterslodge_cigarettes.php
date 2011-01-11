<?php

function hunterslodge_cigarettes_define_item(){

set_item_setting("cannotdiscard","true","hunterslodge_cigs_large");
set_item_setting("description","A box of seventy-five cigarettes. Use this item to obtain the cigarettes for yourself, or give the item to a friend.","hunterslodge_cigs_large");
set_item_setting("destroyafteruse","true","hunterslodge_cigs_large");
set_item_setting("dkpersist","true","hunterslodge_cigs_large");
set_item_setting("context_forest","true","hunterslodge_cigs_large");
set_item_setting("giftable","true","hunterslodge_cigs_large");
set_item_setting("image","hunterslodge_cigs_large.png","hunterslodge_cigs_large");
set_item_setting("inventorylocation","lodgebag","hunterslodge_cigs_large");
set_item_setting("lodge","true","hunterslodge_cigs_large");
set_item_setting("lodge_cost","1000","hunterslodge_cigs_large");
set_item_setting("lodge_longdesc","Cigarettes can be used for many things that Requisition cannot, as I'm sure you're aware by now. These cigarettes, in prepackaged quantities, are easy to give as presents. You can use this item on the World Map, in Outposts, or in the Jungle.","hunterslodge_cigs_large");
set_item_setting("usetext","You open up the box and stuff its delicious contents into your pockets.","hunterslodge_cigs_large");
set_item_setting("verbosename","Large box of Cigarettes","hunterslodge_cigs_large");
set_item_setting("context_village","true","hunterslodge_cigs_large");
set_item_setting("context_worldmap","true","hunterslodge_cigs_large");
set_item_setting("require_file","hunterslodge_cigarettes.php","hunterslodge_cigarettes_large");
set_item_setting("call_function","hunterslodge_cigarettes_large_use","hunterslodge_cigarettes_large");

set_item_setting("cannotdiscard","true","hunterslodge_cigs_medium");
set_item_setting("description","A box of thirty cigarettes. Use this item to obtain the cigarettes for yourself, or give the item to a friend.","hunterslodge_cigs_medium");
set_item_setting("destroyafteruse","true","hunterslodge_cigs_medium");
set_item_setting("dkpersist","true","hunterslodge_cigs_medium");
set_item_setting("context_forest","true","hunterslodge_cigs_medium");
set_item_setting("giftable","true","hunterslodge_cigs_medium");
set_item_setting("image","hunterslodge_cigs_medium.png","hunterslodge_cigs_medium");
set_item_setting("inventorylocation","lodgebag","hunterslodge_cigs_medium");
set_item_setting("lodge","true","hunterslodge_cigs_medium");
set_item_setting("lodge_cost","500","hunterslodge_cigs_medium");
set_item_setting("lodge_longdesc","Cigarettes can be used for many things that Requisition cannot, as I'm sure you're aware by now. These cigarettes, in prepackaged quantities, are easy to give as presents. You can use this item on the World Map, in Outposts, or in the Jungle.","hunterslodge_cigs_medium");
set_item_setting("usetext","You open up the box and stuff its delicious contents into your pockets.","hunterslodge_cigs_medium");
set_item_setting("verbosename","Medium box of Cigarettes","hunterslodge_cigs_medium");
set_item_setting("context_village","true","hunterslodge_cigs_medium");
set_item_setting("context_worldmap","true","hunterslodge_cigs_medium");
set_item_setting("require_file","hunterslodge_cigarettes.php","hunterslodge_cigarettes_medium");
set_item_setting("call_function","hunterslodge_cigarettes_medium_use","hunterslodge_cigarettes_medium");

set_item_setting("cannotdiscard","true","hunterslodge_cigs_small");
set_item_setting("description","A box of five cigarettes. Use this item to obtain the cigarettes for yourself, or give the item to a friend.","hunterslodge_cigs_small");
set_item_setting("destroyafteruse","true","hunterslodge_cigs_small");
set_item_setting("dkpersist","true","hunterslodge_cigs_small");
set_item_setting("context_forest","true","hunterslodge_cigs_small");
set_item_setting("giftable","true","hunterslodge_cigs_small");
set_item_setting("image","hunterslodge_cigs_small.png","hunterslodge_cigs_small");
set_item_setting("inventorylocation","lodgebag","hunterslodge_cigs_small");
set_item_setting("lodge","true","hunterslodge_cigs_small");
set_item_setting("lodge_cost","100","hunterslodge_cigs_small");
set_item_setting("lodge_longdesc","Cigarettes can be used for many things that Requisition cannot, as I'm sure you're aware by now. These cigarettes, in prepackaged quantities, are easy to give as presents. You can use this item on the World Map, in Outposts, or in the Jungle.","hunterslodge_cigs_small");
set_item_setting("usetext","You open up the box and stuff its delicious contents into your pockets.","hunterslodge_cigs_small");
set_item_setting("verbosename","Small box of Cigarettes","hunterslodge_cigs_small");
set_item_setting("context_village","true","hunterslodge_cigs_small");
set_item_setting("context_worldmap","true","hunterslodge_cigs_small");
set_item_setting("require_file","hunterslodge_cigarettes.php","hunterslodge_cigarettes_small");
set_item_setting("call_function","hunterslodge_cigarettes_small_use","hunterslodge_cigarettes_small");

}

function hunterslodge_cigarettes_large_use($args){
	global $session;
	$session['user']['gems'] += 75;
	debug($session['user']['gems']);
	return $args;
}

function hunterslodge_cigarettes_medium_use($args){
	global $session;
	$session['user']['gems'] += 30;
	debug($session['user']['gems']);
	return $args;
}

function hunterslodge_cigarettes_small_use($args){
	global $session;
	$session['user']['gems'] += 5;
	debug($session['user']['gems']);
	return $args;
}

?>