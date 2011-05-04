<?php

function hunterslodge_selectionbox_deluxe_define_item(){
	set_item_setting("cannotdiscard","true","hunterslodge_selectionbox_deluxe");
	set_item_setting("description","A black and red wooden box, lacquered to a deep, mirror shine, containing a selection of items from the Hunter's Lodge and eBoy's Trading Station.","hunterslodge_selectionbox_deluxe");
	set_item_setting("destroyafteruse","true","hunterslodge_selectionbox_deluxe");
	set_item_setting("dkpersist","true","hunterslodge_selectionbox_deluxe");
	set_item_setting("context_forest","true","hunterslodge_selectionbox_deluxe");
	set_item_setting("context_village","true","hunterslodge_selectionbox_deluxe");
	set_item_setting("context_worldmap","true","hunterslodge_selectionbox_deluxe");
	set_item_setting("giftable","true","hunterslodge_selectionbox_deluxe");
	set_item_setting("image","hunterslodge_selectionbox_deluxe.png","hunterslodge_selectionbox_deluxe");
	set_item_setting("inventorylocation","lodgebag","hunterslodge_selectionbox_deluxe");
	set_item_setting("lodge","true","hunterslodge_selectionbox_deluxe");
	set_item_setting("lodge_cost","1499","hunterslodge_selectionbox_deluxe");
	set_item_setting("lodge_stock",100,"hunterslodge_selectionbox_deluxe");
	set_item_setting("lodge_limited",true,"hunterslodge_selectionbox_deluxe");
	set_item_setting("lodge_longdesc","The Deluxe Hunter's Lodge Selection Box is a long, heavy wooden case lacquered and buffed to a mirror shine.  It contains four BANG Grenades, four WHOOMPH Grenades, four ZAP Grenades, four Crate Sniffers, four Energy Drinks, four Improbability Bombs, four Large Medkits, four cans of Monster Repellent Spray, four pieces of Nicotine Gum, four One-Shot Teleporters, four Ration Packs and four Small Medkits from eBoy's Trading Station, along with a Builder's Brew, a Picture Frame, a `blarge`b box of Cigarettes, a Custom Armour Kit, a Name Colourization Kit, a Cunning Disguise, a Title Change Document, a `blarge`b bag of Requisition, a `b50-bundle`b of Quills, a Collar and a Weapon Disguise.  It's a pretty good deal!`n`nThe Deluxe Selection Box can be given as a present - in fact, that's what it's designed for.  Upon using the box from your Inventory, the box disappears (after some suitably fancy flavour text) and is replaced with the items it carries.  You can either give the box itself as a single gift, or you can open the box and send each item individually.`n`n(descriptions of impressive weight and size are for flavour only.  Like all other Hunter's Lodge items, the game will treat this box as if it's weightless, so your backpack remains unencumbered - at least until you open it)","hunterslodge_selectionbox_deluxe");
	set_item_setting("usetext","You press the inlaid brass button on the front of the box, and the lid opens slowly on silent hinges.  The inside of the box is upholstered in soft black velvet.  Packed tightly inside are four BANG Grenades, four WHOOMPH Grenades, four Crate Sniffers, four Energy Drinks, four Improbability Bombs, four Large Medkits, four cans of Monster Repellent Spray, four pieces of Nicotine Gum, four One-Shot Teleporters, four Ration Packs and four Small Medkits from eBoy's Trading Station.`n`nBut hang on just a moment - like in a fine box of chocolates, there's another layer underneath!  You lift up a velveted wooden panel to find a Builder's Brew, a Picture Frame, a large box of Cigarettes, a Custom Armour Kit, a Name Colourization Kit, a Cunning Disguise, a Title Change Document, a large bag of Requisition, a 50-bundle of Quills, a Collar and a Weapon Disguise from the Hunter's Lodge!`n`nStuffing the contents of the box into your now-bulging Backpack and Lodge Bag, the now-empty wooden box closes itself and scuttles off into the Jungle to seek its fortune.","hunterslodge_selectionbox_deluxe");
	set_item_setting("verbosename","Deluxe Selection Box","hunterslodge_selectionbox_deluxe");
	set_item_setting("require_file","hunterslodge_selectionbox_deluxe.php","hunterslodge_selectionbox_deluxe");
	set_item_setting("call_function","hunterslodge_selectionbox_deluxe_use","hunterslodge_selectionbox_deluxe");
}

function hunterslodge_selectionbox_deluxe_use($args){
	global $session;
	//give all the items in the Selection Box
	//eboy's items
	for ($i=0; $i<4; $i++){
		give_item("banggrenade");
		give_item("cratesniffer");
		give_item("energydrink");
		give_item("improbabilitybomb");
		give_item("largemedkit");
		give_item("monsterrepellentspray");
		give_item("nicotinegum");
		give_item("oneshotteleporter");
		give_item("rationpack");
		give_item("smallmedkit");
		give_item("whoomphgrenade");
		give_item("zapgrenade");
	}
	
	//lodge items
	give_item("buildersbrew");
	give_item("hunterslodge_avatar");
	give_item("hunterslodge_cigs_large");
	give_item("hunterslodge_customarmour");
	give_item("hunterslodge_customcolours");
	give_item("hunterslodge_customrace");
	give_item("hunterslodge_customtitle");
	give_item("hunterslodge_customweapon");
	give_item("hunterslodge_namedmount");
	give_item("hunterslodge_req_large");
	give_item("hunterslodge_specialcomments_50");
	
	return $args;
}

?>