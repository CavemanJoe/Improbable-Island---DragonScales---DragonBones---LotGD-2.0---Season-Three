<?php

function hunterslodge_selectionbox_builder_define_item(){
	set_item_setting("cannotdiscard","true","hunterslodge_selectionbox_builder");
	set_item_setting("description","A large and heavy wooden chest, elaborately decorated with brass edging and burl panels, containing a selection of items sure to please dedicated house-builders.","hunterslodge_selectionbox_builder");
	set_item_setting("destroyafteruse","true","hunterslodge_selectionbox_builder");
	set_item_setting("dkpersist","true","hunterslodge_selectionbox_builder");
	set_item_setting("context_forest","true","hunterslodge_selectionbox_builder");
	set_item_setting("context_village","true","hunterslodge_selectionbox_builder");
	set_item_setting("context_worldmap","true","hunterslodge_selectionbox_builder");
	set_item_setting("giftable","true","hunterslodge_selectionbox_builder");
	set_item_setting("image","hunterslodge_selectionbox_builder.png","hunterslodge_selectionbox_builder");
	set_item_setting("inventorylocation","lodgebag","hunterslodge_selectionbox_builder");
	set_item_setting("lodge","true","hunterslodge_selectionbox_builder");
	set_item_setting("lodge_cost","1499","hunterslodge_selectionbox_builder");
	set_item_setting("lodge_stock",100,"hunterslodge_selectionbox_builder");
	set_item_setting("lodge_limited",true,"hunterslodge_selectionbox_builder");
	set_item_setting("lodge_longdesc","The Builder's Selection Box is a large, heavy wooden chest accented with brass fittings.  It contains no fewer than `bten`b Builder's Brews, `bten`b Energy Drinks, `bten`b Ration Packs, `bten`b cans of Monster Repellent Spray, `bten`b One-Shot Teleporters and `btwo large`b boxes of Cigarettes.  In short, it's everything you'd need for a fun few days of building and decorating, along with enough cigarettes to buy some furniture too.  The total value of the box's contents adds up to 4,000 Supporter Points, not counting the worth of the eBoy's items, so it's a pretty good deal - and out of 1,500 points you'll still have a point left over for gift-wrapping.`n`nThe Builder's Selection Box can be given as a present - in fact, that's what it's designed for.  Upon using the box from your Inventory, the box disappears (after some suitably fancy flavour text) and is replaced with the items it carries.  You can either give the box itself as a single gift, or you can open the box and send each item individually.`n`n(descriptions of impressive weight and size are for flavour only.  Like all other Hunter's Lodge items, the game will treat this box as if it's weightless, so your backpack remains unencumbered - at least until you open it)","hunterslodge_selectionbox_builder");
	set_item_setting("usetext","You uncouple the fancy brass catches, and the lid opens slowly on silent hinges.  The inside of the box is upholstered in soft black velvet.  Packed tightly inside are ten Builder's Brews, ten Ration Packs, ten Energy Drinks, ten cans of Monster Repellent Spray, ten One-Shot Teleporters, and two large boxes of cigarettes!`n`nStuffing the contents of the box into your now-bulging Backpack and Lodge Bag, the now-empty wooden box closes itself and scuttles off into the Jungle to seek its fortune.","hunterslodge_selectionbox_builder");
	set_item_setting("verbosename","Builder's Selection Box","hunterslodge_selectionbox_builder");
	set_item_setting("require_file","hunterslodge_selectionbox_builder.php","hunterslodge_selectionbox_builder");
	set_item_setting("call_function","hunterslodge_selectionbox_builder_use","hunterslodge_selectionbox_builder");
}

function hunterslodge_selectionbox_builder_use($args){
	global $session;
	//give all the items in the Selection Box
	for ($i=0; $i<10; $i++){
		give_item("buildersbrew");
		give_item("monsterrepellentspray");
		give_item("oneshotteleporter");
		give_item("rationpack");
		give_item("energydrink");
	}

	give_item("hunterslodge_cigs_large");
	give_item("hunterslodge_cigs_large");
	
	return $args;
}

?>