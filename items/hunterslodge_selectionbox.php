<?php

function hunterslodge_selectionbox_define_item(){
	set_item_setting("cannotdiscard","true","hunterslodge_selectionbox");
	set_item_setting("description","A large, heavy rosewood box containing a selection of items from the Hunter's Lodge and eBoy's Trading Station, lacquered to a high finish.","hunterslodge_selectionbox");
	set_item_setting("destroyafteruse","true","hunterslodge_selectionbox");
	set_item_setting("dkpersist","true","hunterslodge_selectionbox");
	set_item_setting("context_forest","true","hunterslodge_selectionbox");
	set_item_setting("context_village","true","hunterslodge_selectionbox");
	set_item_setting("context_worldmap","true","hunterslodge_selectionbox");
	set_item_setting("giftable","true","hunterslodge_selectionbox");
	set_item_setting("image","hunterslodge_selectionbox.png","hunterslodge_selectionbox");
	set_item_setting("inventorylocation","lodgebag","hunterslodge_selectionbox");
	set_item_setting("lodge","true","hunterslodge_selectionbox");
	set_item_setting("lodge_cost","699","hunterslodge_selectionbox");
	set_item_setting("lodge_stock",200,"hunterslodge_selectionbox");
	set_item_setting("lodge_limited",true,"hunterslodge_selectionbox");
	set_item_setting("lodge_longdesc","The Hunter's Lodge Selection Box is a heavy rosewood box with a burl inlay, lacquered to a high shine.  It contains two BANG Grenades, two WHOOMPH Grenades, two ZAP Grenades, two Crate Sniffers, two Energy Drinks, two Improbability Bombs, two Large Medkits, two cans of Monster Repellent Spray, two pieces of Nicotine Gum, two One-Shot Teleporters, two Ration Packs and two Small Medkits, as well as a Builder's Brew, a Picture Frame, a small box of Cigarettes, a Custom Armour Kit, a Name Colourization Kit, a Cunning Disguise, a Title Change Document, a small bag of Requisition, a small bundle of Quills, a Collar and a Weapon Disguise.  The total value of the box's contents adds up to 1,200 Supporter Points, not counting the worth of the eBoy's items, so it's a pretty good deal - and out of 700 points you'll still have a point left over for gift-wrapping.`n`nThe Hunter's Lodge Selection Box can be given as a present - in fact, that's what it's designed for.  Upon using the box from your Inventory, the box disappears (after some suitably fancy flavour text) and is replaced with the items it carries.  You can either give the box itself as a single gift, or you can open the box and send each item individually.`n`n(descriptions of impressive weight and size are for flavour only.  Like all other Hunter's Lodge items, the game will treat this box as if it's weightless, so your backpack remains unencumbered - at least until you open it)","hunterslodge_selectionbox");
	set_item_setting("usetext","You press the brass lever on the front of the box, and the lid opens slowly on silent hinges.  The inside of the box is upholstered in soft, black velvet.  Packed tightly inside are two BANG Grenades, two WHOOMPH Grenades, two Crate Sniffers, two Energy Drinks, two Improbability Bombs, two Large Medkits, two cans of Monster Repellent Spray, two pieces of Nicotine Gum, two One-Shot Teleporters, two Ration Packs and two Small Medkits from eBoy's Trading Station.`n`nBut hang on just a moment - like in a fine box of chocolates, there's another layer underneath!  You lift up a velveted wooden panel to find a Builder's Brew, a Picture Frame, a small box of Cigarettes, a Custom Armour Kit, a Name Colourization Kit, a Cunning Disguise, a Title Change Document, a small bag of Requisition, a small bundle of Quills, a Collar and a Weapon Disguise from the Hunter's Lodge!`n`nStuffing the contents of the box into your now-bulging Backpack and Lodge Bag, the now-empty rosewood box closes itself and scuttles off into the Jungle to seek its fortune.","hunterslodge_selectionbox");
	set_item_setting("verbosename","Hunter's Lodge Selection Box","hunterslodge_selectionbox");
	set_item_setting("require_file","hunterslodge_selectionbox.php","hunterslodge_selectionbox");
	set_item_setting("call_function","hunterslodge_selectionbox_use","hunterslodge_selectionbox");
}

function hunterslodge_selectionbox_use($args){
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
	
	//lodge items
	give_item("buildersbrew");
	give_item("hunterslodge_avatar");
	give_item("hunterslodge_cigs_small");
	give_item("hunterslodge_customarmour");
	give_item("hunterslodge_customcolours");
	give_item("hunterslodge_customrace");
	give_item("hunterslodge_customtitle");
	give_item("hunterslodge_customweapon");
	give_item("hunterslodge_namedmount");
	give_item("hunterslodge_req_small");
	give_item("hunterslodge_specialcomments_small");
	
	return $args;
}

?>