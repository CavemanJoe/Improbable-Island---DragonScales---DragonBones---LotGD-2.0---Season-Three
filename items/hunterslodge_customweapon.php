<?php

function hunterslodge_customweapon_define_item(){
	set_item_setting("cannotdiscard","true","hunterslodge_customweapon");
	set_item_setting("description","Useful for roleplayers, this item allows you to change the name of your weapon, shown in your Bio, character stats, and commentary areas.","hunterslodge_customweapon");
	set_item_setting("dkpersist","true","hunterslodge_customweapon");
	set_item_setting("giftable","true","hunterslodge_customweapon");
	set_item_setting("image","hunterslodge_customweapon.png","hunterslodge_customweapon");
	set_item_setting("inventorylocation","lodgebag","hunterslodge_customweapon");
	set_item_setting("lodge","true","hunterslodge_customweapon");
	set_item_setting("context_lodge","true","hunterslodge_customweapon");
	set_item_setting("lodge_cost","100","hunterslodge_customweapon");
	set_item_setting("lodge_longdesc","This Weapon Disguise allows you to set the name of your weapon to anything you'd like. You get 25 characters to play with, and other players will be able to see your weapon name in commentary mouseover areas. If you want to roleplay a character with an unusual weapon that Sheila's Shack doesn't sell, this is the item you'll want to buy. Please note that setting a custom weapon is a permanent deal; although you can still buy new weapons and have their effects take hold normally, the name will always be what you set when you attached your Weapon Disguise, unless you change or reset it with another Weapon Disguise.`n`nThis item lets you change your weapon name once; you'll need more Weapon Disguises for subsequent changes. If you think you're likely to change your weapon name a lot, then you might want to have a look at the Extra-Cunning Weapon Disguise instead.`n`nYou can use this item from any Hunter's Lodge.","hunterslodge_customweapon");
	set_item_setting("verbosename","Weapon Disguise","hunterslodge_customweapon");
	set_item_setting("verbosename","Custom Armour Kit","hunterslodge_customweapon");
	set_item_setting("require_file","hunterslodge_customweapon.php","hunterslodge_customweapon");
	set_item_setting("call_function","hunterslodge_customweapon_use","hunterslodge_customweapon");
}

function hunterslodge_customweapon_permanent_define_item(){
	set_item_setting("cannotdiscard","true","hunterslodge_customweapon_permanent");
	set_item_setting("description","The deluxe version of the Weapon Disguise gives you unlimited weapon name changes. Change your weapon as often as you like!","hunterslodge_customweapon_permanent");
	set_item_setting("dkpersist","true","hunterslodge_customweapon_permanent");
	set_item_setting("giftable","true","hunterslodge_customweapon_permanent");
	set_item_setting("image","hunterslodge_customweapon_permanent.png","hunterslodge_customweapon_permanent");
	set_item_setting("inventorylocation","lodgebag","hunterslodge_customweapon_permanent");
	set_item_setting("lodge","true","hunterslodge_customweapon_permanent");
	set_item_setting("context_lodge","true","hunterslodge_customweapon_permanent");
	set_item_setting("lodge_cost","1000","hunterslodge_customweapon_permanent");
	set_item_setting("lodge_longdesc","This Extra-Cunning Weapon Disguise allows you to set the name of your weapon to anything you'd like. You get 25 characters to play with, and other players will be able to see your weapon name in commentary mouseover areas. If you want to roleplay a character with an unusual weapon that Sheila's Shack doesn't sell, this is the item you'll want to buy. Please note that setting a custom weapon is a permanent deal; although you can still buy new weapons and have their effects take hold normally, the name will always be what you set when you attached your Extra-Cunning Weapon Disguise, unless you change or reset it by using this item again.`n`nThis item lets you change your weapon name as many times as you like, but remember: `4you can only gift or trade Extra-Cunning Weapon Disguises that have never been used.`0 If you're buying this as a present for somebody, it's a good idea to give it to them straight away.`n`nYou can use this item from any Hunter's Lodge.","hunterslodge_customweapon_permanent");
	set_item_setting("verbosename","Extra-Cunning Weapon Disguise","hunterslodge_customweapon_permanent");
	set_item_setting("require_file","hunterslodge_customweapon.php","hunterslodge_customweapon_permanent");
	set_item_setting("call_function","hunterslodge_customweapon_permanent_use","hunterslodge_customweapon_permanent");
}

function hunterslodge_customweapon_use($args){
	redirect("runmodule.php?module=hunterslodge_customweapon&op=change");
}

function hunterslodge_customweapon_permanent_use($args){
	redirect("runmodule.php?module=hunterslodge_customweapon&op=change&free=1");
}

?>