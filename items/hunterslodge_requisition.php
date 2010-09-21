<?php

function hunterslodge_requisition_define_item(){

	set_item_setting("cannotdiscard","true","hunterslodge_req_large");
	set_item_setting("description"," bag containing twenty-five thousand Requistion tokens. Use this item to obtain the Requisition for yourself, or give the item to a friend.","hunterslodge_req_large");
	set_item_setting("destroyafteruse","true","hunterslodge_req_large");
	set_item_setting("dkpersist","true","hunterslodge_req_large");
	set_item_setting("context_forest","true","hunterslodge_req_large");
	set_item_setting("giftable","true","hunterslodge_req_large");
	set_item_setting("image","hunterslodge_req_large.png","hunterslodge_req_large");
	set_item_setting("inventorylocation","lodgebag","hunterslodge_req_large");
	set_item_setting("lodge","true","hunterslodge_req_large");
	set_item_setting("lodge_cost","1000","hunterslodge_req_large");
	set_item_setting("lodge_longdesc","Bags of Requisition tokens in your Inventory can't be lost to Thieving Midget Bastards until the Requisition is taken out of the bag. Bags like this also make it easier to give Requisition to your friends. You can use this item on the World Map, in Outposts, or in the Jungle.","hunterslodge_req_large");
	set_item_setting("playergold","25000","hunterslodge_req_large");
	set_item_setting("usetext","You tip the bag over and pour its contents into your pockets.","hunterslodge_req_large");
	set_item_setting("verbosename","Large bag of Requisition Tokens","hunterslodge_req_large");
	set_item_setting("context_village","true","hunterslodge_req_large");
	set_item_setting("context_worldmap","true","hunterslodge_req_large");
	set_item_setting("require_file","hunterslodge_requisition.php","hunterslodge_req_large");
	set_item_setting("call_function","hunterslodge_requisition_large_use","hunterslodge_req_large");

	set_item_setting("cannotdiscard","true","hunterslodge_req_medium");
	set_item_setting("description"," bag containing twelve thousand Requistion tokens. Use this item to obtain the Requisition for yourself, or give the item to a friend.","hunterslodge_req_medium");
	set_item_setting("destroyafteruse","true","hunterslodge_req_medium");
	set_item_setting("dkpersist","true","hunterslodge_req_medium");
	set_item_setting("context_forest","true","hunterslodge_req_medium");
	set_item_setting("giftable","true","hunterslodge_req_medium");
	set_item_setting("image","hunterslodge_req_medium.png","hunterslodge_req_medium");
	set_item_setting("inventorylocation","lodgebag","hunterslodge_req_medium");
	set_item_setting("lodge","true","hunterslodge_req_medium");
	set_item_setting("lodge_cost","500","hunterslodge_req_medium");
	set_item_setting("lodge_longdesc","Bags of Requisition tokens in your Inventory can't be lost to Thieving Midget Bastards until the Requisition is taken out of the bag. Bags like this also make it easier to give Requisition to your friends. You can use this item on the World Map, in Outposts, or in the Jungle.","hunterslodge_req_medium");
	set_item_setting("playergold","12000","hunterslodge_req_medium");
	set_item_setting("usetext","You tip the bag over and pour its contents into your pockets.","hunterslodge_req_medium");
	set_item_setting("verbosename","Medium bag of Requisition Tokens","hunterslodge_req_medium");
	set_item_setting("context_village","true","hunterslodge_req_medium");
	set_item_setting("context_worldmap","true","hunterslodge_req_medium");
	set_item_setting("require_file","hunterslodge_requisition.php","hunterslodge_req_medium");
	set_item_setting("call_function","hunterslodge_requisition_medium_use","hunterslodge_req_medium");

	set_item_setting("cannotdiscard","true","hunterslodge_req_small");
	set_item_setting("description"," bag containing two thousand Requistion tokens. Use this item to obtain the Requisition for yourself, or give the item to a friend.","hunterslodge_req_small");
	set_item_setting("destroyafteruse","true","hunterslodge_req_small");
	set_item_setting("dkpersist","true","hunterslodge_req_small");
	set_item_setting("context_forest","true","hunterslodge_req_small");
	set_item_setting("giftable","true","hunterslodge_req_small");
	set_item_setting("image","hunterslodge_req_small.png","hunterslodge_req_small");
	set_item_setting("inventorylocation","lodgebag","hunterslodge_req_small");
	set_item_setting("lodge","true","hunterslodge_req_small");
	set_item_setting("lodge_cost","100","hunterslodge_req_small");
	set_item_setting("lodge_longdesc","Bags of Requisition tokens in your Inventory can't be lost to Thieving Midget Bastards until the Requisition is taken out of the bag. Bags like this also make it easier to give Requisition to your friends. You can use this item on the World Map, in Outposts, or in the Jungle.","hunterslodge_req_small");
	set_item_setting("playergold","2000","hunterslodge_req_small");
	set_item_setting("usetext","You tip the bag over and pour its contents into your pockets.","hunterslodge_req_small");
	set_item_setting("verbosename","Small bag of Requisition Tokens","hunterslodge_req_small");
	set_item_setting("context_village","true","hunterslodge_req_small");
	set_item_setting("context_worldmap","true","hunterslodge_req_small");
	set_item_setting("require_file","hunterslodge_requisition.php","hunterslodge_req_small");
	set_item_setting("call_function","hunterslodge_requisition_small_use","hunterslodge_req_small");

}

function hunterslodge_requisition_large_use($args){
	$session['user']['gold'] += 25000;
	return $args;
}

function hunterslodge_requisition_medium_use($args){
	$session['user']['gold'] += 12000;
	return $args;
}

function hunterslodge_requisition_small_use($args){
	$session['user']['gold'] += 2000;
	return $args;
}

?>