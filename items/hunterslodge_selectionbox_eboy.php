<?php

function hunterslodge_selectionbox_eboy_define_item(){
	set_item_setting("cannotdiscard","true","hunterslodge_selectionbox_eboy");
	set_item_setting("description","A smart wooden box containing two of every item from eBoy's Trading Station.","hunterslodge_selectionbox_eboy");
	set_item_setting("destroyafteruse","true","hunterslodge_selectionbox_eboy");
	set_item_setting("dkpersist","true","hunterslodge_selectionbox_eboy");
	set_item_setting("context_forest","true","hunterslodge_selectionbox_eboy");
	set_item_setting("context_village","true","hunterslodge_selectionbox_eboy");
	set_item_setting("context_worldmap","true","hunterslodge_selectionbox_eboy");
	set_item_setting("giftable","true","hunterslodge_selectionbox_eboy");
	set_item_setting("image","hunterslodge_selectionbox_eboy.png","hunterslodge_selectionbox_eboy");
	set_item_setting("inventorylocation","lodgebag","hunterslodge_selectionbox_eboy");
	set_item_setting("lodge","true","hunterslodge_selectionbox_eboy");
	set_item_setting("lodge_cost","99","hunterslodge_selectionbox_eboy");
	set_item_setting("lodge_stock",400,"hunterslodge_selectionbox_eboy");
	set_item_setting("lodge_limited",true,"hunterslodge_selectionbox_eboy");
	set_item_setting("lodge_longdesc","The eBoy's Trading Station Selection Box is a dark wooden box of satisfying weight.  It contains two BANG Grenades, two WHOOMPH Grenades, two ZAP Grenades, two Crate Sniffers, two Energy Drinks, two Improbability Bombs, two Large Medkits, two cans of Monster Repellent Spray, two pieces of Nicotine Gum, two One-Shot Teleporters, two Ration Packs and two Small Medkits.`n`nThe eBoy's Trading Station Selection Box can be given as a present - in fact, that's what it's designed for.  Upon using the box from your Inventory, the box disappears (after some suitably fancy flavour text) and is replaced with the items it carries.  You can either give the box itself as a single gift, or you can open the box and send each item individually.`n`n(descriptions of impressive weight and size are for flavour only.  Like all other Hunter's Lodge items, the game will treat this box as if it's weightless, so your backpack remains unencumbered - at least until you open it)","hunterslodge_selectionbox_eboy");
	set_item_setting("usetext","You press the brushed stainless-steel lever on the front of the box, and the lid opens slowly on silent hinges.  The inside of the box is upholstered in soft maroon velvet.  Packed tightly inside are two BANG Grenades, two WHOOMPH Grenades, two Crate Sniffers, two Energy Drinks, two Improbability Bombs, two Large Medkits, two cans of Monster Repellent Spray, two pieces of Nicotine Gum, two One-Shot Teleporters, two Ration Packs and two Small Medkits from eBoy's Trading Station.`n`nStuffing the contents of the box into your now-bulging Backpack, the now-empty wooden box closes itself and scuttles off into the Jungle to seek its fortune.","hunterslodge_selectionbox_eboy");
	set_item_setting("verbosename","eBoy's Trading Station Selection Box","hunterslodge_selectionbox_eboy");
	set_item_setting("require_file","hunterslodge_selectionbox_eboy.php","hunterslodge_selectionbox_eboy");
	set_item_setting("call_function","hunterslodge_selectionbox_eboy_use","hunterslodge_selectionbox_eboy");
}

function hunterslodge_selectionbox_eboy_use($args){
	global $session;
	//give all the items in the Selection Box
	//eboy's items
	for ($i=0; $i<2; $i++){
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
	
	return $args;
}

?>