<?php

function hunterslodge_customarmour_define_item(){
	set_item_setting("cannotdiscard","true","hunterslodge_customarmour");
	set_item_setting("description","This carefully-concocted kit of paint, tinfoil and a large hammer allows you to customize the outward appearance of your armour. In game terms, that means you get to rename it - if you want to strut around in a fur-trimmed suit, this is how you do it.","hunterslodge_customarmour");
	set_item_setting("dkpersist","true","hunterslodge_customarmour");
	set_item_setting("giftable","true","hunterslodge_customarmour");
	set_item_setting("image","hunterslodge_customarmour.png","hunterslodge_customarmour");
	set_item_setting("inventorylocation","lodgebag","hunterslodge_customarmour");
	set_item_setting("lodge","true","hunterslodge_customarmour");
	set_item_setting("context_lodge","true","hunterslodge_customarmour");
	set_item_setting("lodge_cost","100","hunterslodge_customarmour");
	set_item_setting("lodge_longdesc","This Armour Customisation Kit (consisting of a can of paint, some tinfoil and a large hammer) will allow you to set the name of your armour to anything you like. You get 25 characters to play about with, and other players will be able to see your custom weapon in commentary mouseover areas. If you're roleplaying a character with an unusual taste in clothing which to which Sheila's Shack doesn't cater, then this is the item for you. Once you set your custom armour, buying new armour will only change your stats and buffs; the name will always reflect what you chose when you customised your armour.`n`nThis item lets you customise your armour once; if you want to change it again, you'll need another Armour Customisation Kit. If you can see yourself doing this a lot, you'll save money by buying the Deluxe Armour Customisation Kit, which gives you unlimited armour name changes.`n`nYou can use this item from any Hunter's Lodge.","hunterslodge_customarmour");
	set_item_setting("verbosename","Custom Armour Kit","hunterslodge_customarmour");
	set_item_setting("require_file","hunterslodge_customarmour.php","hunterslodge_customarmour");
	set_item_setting("call_function","hunterslodge_customarmour_use","hunterslodge_customarmour");
}

function hunterslodge_customarmour_permanent_define_item(){
	set_item_setting("cannotdiscard","true","hunterslodge_customarmour_permanent");
	set_item_setting("description","The deluxe version of the Armour Customization Kit is specially tuned with certain Improbable Powers of Time and Space (and Groucho glasses). It gives you unlimited armour name changes. Change your armour as often as you like!","hunterslodge_customarmour_permanent");
	set_item_setting("dkpersist","true","hunterslodge_customarmour_permanent");
	set_item_setting("giftable","true","hunterslodge_customarmour_permanent");
	set_item_setting("image","hunterslodge_customarmour_permanent.png","hunterslodge_customarmour_permanent");
	set_item_setting("inventorylocation","lodgebag","hunterslodge_customarmour_permanent");
	set_item_setting("lodge","true","hunterslodge_customarmour_permanent");
	set_item_setting("context_lodge","true","hunterslodge_customarmour_permanent");
	set_item_setting("lodge_cost","1000","hunterslodge_customarmour_permanent");
	set_item_setting("lodge_longdesc","This Deluxe Armour Customisation Kit (consisting of a can of paint, some tinfoil and a large hammer, all cunningly disguised so nobody will ever know) will allow you to set the name of your armour to anything you like. You get 25 characters to play about with, and other players will be able to see your custom armour in commentary mouseover areas. If you're roleplaying a character with an unusual taste in clothing which to which Sheila's Shack doesn't cater, then this is the item for you. Once you set your custom armour, buying new armour will only change your stats and buffs; the name will always reflect what you chose when you customised your armour.`n`nThis item lets you customise your armour as many times as you like without having to buy it again, but remember - `4if you're buying this as a present, remember not to use it! Only new, unused items can be gifted to other players!`0`n`nYou can use this item from any Hunter's Lodge.","hunterslodge_customarmour_permanent");
	set_item_setting("verbosename","Deluxe Custom Armour Kit","hunterslodge_customarmour_permanent");
	set_item_setting("require_file","hunterslodge_customarmour.php","hunterslodge_customarmour");
	set_item_setting("call_function","hunterslodge_customarmour_permanent_use","hunterslodge_customarmour");
	
}

function hunterslodge_customarmour_use($args){
	redirect("runmodule.php?module=hunterslodge_customarmour&op=change");
}

function hunterslodge_customarmour_permanent_use($args){
	redirect("runmodule.php?module=hunterslodge_customarmour&op=change&free=1");
}

?>