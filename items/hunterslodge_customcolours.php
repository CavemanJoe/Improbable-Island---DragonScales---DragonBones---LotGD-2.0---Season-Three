<?php

function hunterslodge_customcolours_define_item(){
	set_item_setting("cannotdiscard","true","hunterslodge_customcolours");
	set_item_setting("description","These felt-tip pens allow you to change the colours in your name. We're not entirely sure how; maybe you just use them to draw all over your face, or something.","hunterslodge_customcolours");
	set_item_setting("dkpersist","true","hunterslodge_customcolours");
	set_item_setting("giftable","true","hunterslodge_customcolours");
	set_item_setting("image","hunterslodge_customcolours.png","hunterslodge_customcolours");
	set_item_setting("inventorylocation","lodgebag","hunterslodge_customcolours");
	set_item_setting("lodge","true","hunterslodge_customcolours");
	set_item_setting("context_lodge","true","hunterslodge_customcolours");
	set_item_setting("lodge_cost","100","hunterslodge_customcolours");
	set_item_setting("lodge_longdesc","This Name Colourisation Kit (IE a big pack of felt-tip pens) will allow you to add colour codes to your name. If you want a fancy, colourful name, then this is the item for you; you might want to check out the Title Change item, too.`n`nThis item lets you colourise your name once; if you want to change the colours afterwards, you'll need another Name Colourisation Kit. If you can see yourself doing this a lot, you'll save money by buying the Deluxe Name Colourisation Kit, which gives you unlimited colour changes.`n`nYou can use this item from any Hunter's Lodge.","hunterslodge_customcolours");
	set_item_setting("verbosename","Name Colourisation Kit","hunterslodge_customcolours");
	set_item_setting("require_file","hunterslodge_customcolours.php","hunterslodge_customcolours");
	set_item_setting("call_function","hunterslodge_customcolours_use","hunterslodge_customcolours");
}

function hunterslodge_customcolours_permanent_define_item(){
	set_item_setting("cannotdiscard","true","hunterslodge_customcolours_permanent");
	set_item_setting("description","These pens allow you to change the colours in your name as often as you like!","hunterslodge_customcolours_permanent");
	set_item_setting("dkpersist","true","hunterslodge_customcolours_permanent");
	set_item_setting("giftable","true","hunterslodge_customcolours_permanent");
	set_item_setting("image","hunterslodge_customcolours_permanent.png","hunterslodge_customcolours_permanent");
	set_item_setting("inventorylocation","lodgebag","hunterslodge_customcolours_permanent");
	set_item_setting("lodge","true","hunterslodge_customcolours_permanent");
	set_item_setting("context_lodge","true","hunterslodge_customcolours_permanent");
	set_item_setting("lodge_cost","1000","hunterslodge_customcolours_permanent");
	set_item_setting("lodge_longdesc","This Name Colourisation Kit (IE a big pack of felt-tip pens) will allow you to add colour codes to your name. If you want a fancy, colourful name, then this is the item for you; you might want to check out the Title Change item, too.`n`nThis item lets you colourise your name as many times as you want, and as often as you want - but remember, `4only `bunused`b Name Colourisation Kits can be gifted to other players.`0`n`nYou can use this item from any Hunter's Lodge.","hunterslodge_customcolours_permanent");
	set_item_setting("verbosename","Deluxe Name Colourisation Kit","hunterslodge_customcolours_permanent");
	set_item_setting("require_file","hunterslodge_customcolours.php","hunterslodge_customcolours_permanent");
	set_item_setting("call_function","hunterslodge_customcolours_permanent_use","hunterslodge_customcolours_permanent");
}

function hunterslodge_customcolours_use($args){
	redirect("runmodule.php?module=hunterslodge_customcolours&op=change");
}

function hunterslodge_customcolours_permanent_use($args){
	redirect("runmodule.php?module=hunterslodge_customcolours&op=change&free=1");
}

?>