<?php

function hunterslodge_customrace_define_item(){
	set_item_setting("cannotdiscard","true","hunterslodge_customrace");
	set_item_setting("description","This Cunning Disguise allows you to set a custom Race in commentary areas. Wanna be a JokerMorph? Here's how you do it.","hunterslodge_customrace");
	set_item_setting("dkpersist","true","hunterslodge_customrace");
	set_item_setting("image","hunterslodge_customrace.png","hunterslodge_customrace");
	set_item_setting("inventorylocation","lodgebag","hunterslodge_customrace");
	set_item_setting("lodge","true","hunterslodge_customrace");
	set_item_setting("context_lodge","true","hunterslodge_customrace");
	set_item_setting("context_forest","true","hunterslodge_customrace");
	set_item_setting("context_village","true","hunterslodge_customrace");
	set_item_setting("context_worldmap","true","hunterslodge_customrace");
	set_item_setting("lodge_cost","75","hunterslodge_customrace");
	set_item_setting("lodge_longdesc","This Cunning Disguise allows you to dress up like a member of another race. Perhaps even an entirely fictional race. Your race icon in commentary areas will be replaced with a silver question mark; mousing over this symbol reveals the race name that you choose when you use this item. If you want to roleplay as a JokerMorph or other race that isn't playable or might not be strictly canonical, this is the item you need.`n`nThis item will disappear after you set your custom race; you'll need another Cunning Disguise to change your custom race again. If you think you're going to change your race a lot, you might want to look at the Cunning Disguise Cunningly Disguised with a Cunning Disguise, which is a very silly item indeed.`n`nYou can use this item in Jungles, while Travelling, in Outposts and in the Hunter's Lodge.","hunterslodge_customrace");
	set_item_setting("verbosename","Cunning Disguise","hunterslodge_customrace");
	set_item_setting("require_file","hunterslodge_customrace.php","hunterslodge_customrace");
	set_item_setting("call_function","hunterslodge_customrace_use","hunterslodge_customrace");
}

function hunterslodge_customrace_permanent_define_item(){
	set_item_setting("cannotdiscard","true","hunterslodge_customrace_permanent");
	set_item_setting("description","This version of the Cunning Disguise will let you change your Custom Race as often as you want. It's Groucho glasses all the way down!","hunterslodge_customrace_permanent");
	set_item_setting("dkpersist","true","hunterslodge_customrace_permanent");
	set_item_setting("image","hunterslodge_customrace_permanent.png","hunterslodge_customrace_permanent");
	set_item_setting("inventorylocation","lodgebag","hunterslodge_customrace_permanent");
	set_item_setting("lodge","true","hunterslodge_customrace_permanent");
	set_item_setting("context_lodge","true","hunterslodge_customrace_permanent");
	set_item_setting("context_forest","true","hunterslodge_customrace_permanent");
	set_item_setting("context_village","true","hunterslodge_customrace_permanent");
	set_item_setting("context_worldmap","true","hunterslodge_customrace_permanent");
	set_item_setting("lodge_cost","595","hunterslodge_customrace_permanent");
	set_item_setting("lodge_longdesc","In addition to being an extremely silly object all around, this Cunning Disguise Cunningly Disguised with a Cunning Disguise allows you to dress up like a member of another race. Perhaps even an entirely fictional race. Your race icon in commentary areas will be replaced with a silver question mark; mousing over this symbol reveals the race name that you choose when you use this item (up to a maximum of 25 characters). If you want to roleplay as a JokerMorph or other race that isn't playable or might not be strictly canonical, this is the item you need.`n`nThis item lets you change your custom race as many times you as you like, as often as you like. `4Only items that have never been used can be given to other players,`0 so if you're buying this as a gift, it's a good idea to give it to the recipient straight away.`n`nYou can use this item in Jungles, while Travelling, in Outposts and in the Hunter's Lodge.","hunterslodge_customrace_permanent");
	set_item_setting("verbosename","Cunning Disguise Cunningly Disguised with a Cunning Disguise","hunterslodge_customrace_permanent");
	set_item_setting("require_file","hunterslodge_customrace.php","hunterslodge_customrace_permanent");
	set_item_setting("call_function","hunterslodge_customrace_permanent_use","hunterslodge_customrace_permanent");
}

function hunterslodge_customrace_use($args){
	redirect("runmodule.php?module=hunterslodge_customrace&op=change&context=".$args['context']);
}

function hunterslodge_customrace_permanent_use($args){
	redirect("runmodule.php?module=hunterslodge_customrace&op=change&free=1&context=".$args['context']);
}

?>