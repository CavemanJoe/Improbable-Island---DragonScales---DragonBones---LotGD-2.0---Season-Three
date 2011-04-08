<?php

function hunterslodge_customtitle_define_item(){
	set_item_setting("cannotdiscard","true","hunterslodge_customtitle");
	set_item_setting("description","This mysteriously-official-looking document allows you to change your in-game title to something of your own choosing.","hunterslodge_customtitle");
	set_item_setting("dkpersist","true","hunterslodge_customtitle");
	set_item_setting("giftable","true","hunterslodge_customtitle");
	set_item_setting("image","hunterslodge_customtitle.png","hunterslodge_customtitle");
	set_item_setting("inventorylocation","lodgebag","hunterslodge_customtitle");
	set_item_setting("lodge","true","hunterslodge_customtitle");
	set_item_setting("context_lodge","true","hunterslodge_customtitle");
	set_item_setting("context_forest","true","hunterslodge_customtitle");
	set_item_setting("context_village","true","hunterslodge_customtitle");
	set_item_setting("context_worldmap","true","hunterslodge_customtitle");
	set_item_setting("lodge_cost","75","hunterslodge_customtitle");
	set_item_setting("lodge_longdesc","This official-looking document lets you change your title - that is, the first part of your character's name, which could be \"Rookie,\" \"Contestant,\" \"Returning Contestant\" all the way up to silly things like \"Grand High Supreme Badass\" - to something of your own devising. You can use colour codes in there too, up to a maximum of 25 characters. Once you've changed your title, it stays that way until you change it again.`n`nThis item lets you change your title once; to change it again, you'll need another Title Change document. If you're a heavy roleplayer, this can get expensive, so you might want to have a look at the Super-Official Title Change Document, which gives you unlimited title changes for one price.`n`nYou can use this item in Jungles, while Travelling, in Outposts and in the Hunter's Lodge.","hunterslodge_customtitle");
	set_item_setting("verbosename","Title Change Document","hunterslodge_customtitle");
	set_item_setting("require_file","hunterslodge_customtitle.php","hunterslodge_customtitle");
	set_item_setting("call_function","hunterslodge_customtitle_use","hunterslodge_customtitle");
}

function hunterslodge_customtitle_permanent_define_item(){
	set_item_setting("cannotdiscard","true","hunterslodge_customtitle_permanent");
	set_item_setting("description","This mysteriously-official-looking document allows you to change your in-game title to something of your own choosing. The extra seal makes this document Officially Even More Official, and allows you to change your title as often as you like!","hunterslodge_customtitle_permanent");
	set_item_setting("dkpersist","true","hunterslodge_customtitle_permanent");
	set_item_setting("giftable","true","hunterslodge_customtitle_permanent");
	set_item_setting("image","hunterslodge_customtitle_permanent.png","hunterslodge_customtitle_permanent");
	set_item_setting("inventorylocation","lodgebag","hunterslodge_customtitle_permanent");
	set_item_setting("lodge","true","hunterslodge_customtitle_permanent");
	set_item_setting("context_lodge","true","hunterslodge_customtitle_permanent");
	set_item_setting("context_forest","true","hunterslodge_customtitle_permanent");
	set_item_setting("context_village","true","hunterslodge_customtitle_permanent");
	set_item_setting("context_worldmap","true","hunterslodge_customtitle_permanent");
	set_item_setting("lodge_cost","595","hunterslodge_customtitle_permanent");
	set_item_setting("lodge_longdesc","This official-looking document lets you change your title - that is, the first part of your character's name, which could be \"Rookie,\" \"Contestant,\" \"Returning Contestant\" all the way up to silly things like \"Grand High Supreme Badass\" - to something of your own devising. You can use colour codes in there too, up to a maximum of 25 characters. Once you've changed your title, it stays that way until you change it again.`n`nThis item lets you change your title as many times as you like. If you're buying this as a present, remember that `4only unused Title Change documents can be gifted!`0 Nobody gives second-hand bits of paper as presents.`n`nYou can use this item in Jungles, while Travelling, in Outposts and in the Hunter's Lodge.","hunterslodge_customtitle_permanent");
	set_item_setting("verbosename","Super-Official Title Change Document","hunterslodge_customtitle_permanent");
	set_item_setting("require_file","hunterslodge_customtitle.php","hunterslodge_customtitle_permanent");
	set_item_setting("call_function","hunterslodge_customtitle_permanent_use","hunterslodge_customtitle_permanent");
}

function hunterslodge_customtitle_use($args){
	redirect("runmodule.php?module=hunterslodge_customtitle&op=change&context=".$args['context']);
}

function hunterslodge_customtitle_permanent_use($args){
	redirect("runmodule.php?module=hunterslodge_customtitle&op=change&free=1&context=".$args['context']);
}

?>