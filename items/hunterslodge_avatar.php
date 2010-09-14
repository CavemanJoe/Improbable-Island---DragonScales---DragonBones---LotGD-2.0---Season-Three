<?php

function hunterslodge_avatar_define_item(){
	set_item_setting("cannotdiscard","true","hunterslodge_avatar");
	set_item_setting("description","This empty picture frame is waiting for your uploaded Avatar, to be displayed in your Bio and in commentary mouseover areas.","hunterslodge_avatar");
	set_item_setting("dkpersist","true","hunterslodge_avatar");
	set_item_setting("image","hunterslodge_avatar.png","hunterslodge_avatar");
	set_item_setting("inventorylocation","lodgebag","hunterslodge_avatar");
	set_item_setting("lodge","true","hunterslodge_avatar");
	set_item_setting("context_lodge","true","hunterslodge_avatar");
	set_item_setting("lodge_cost","100","hunterslodge_avatar");
	set_item_setting("lodge_longdesc","This picture frame allows you to upload a picture of your choosing, to be used in your Bio and in commentary mouseover areas. The image can be up to 100 pixels wide, by 100 pixels tall, and no larger than 100 kilobytes, in .jpg or .png format.`n`nThis item vanishes after using it, so you'll need a new Picture Frame every time you change your Avatar; if you'd like the option to change your Avatar whenever you like, buy the Deluxe Picture Frame instead.`n`nYou can use this item from any Hunter's Lodge.","hunterslodge_avatar");
	set_item_setting("verbosename","Picture Frame","hunterslodge_avatar");
	set_item_setting("require_file","hunterslodge_avatar.php","hunterslodge_avatar");
	set_item_setting("call_function","hunterslodge_avatar_use","hunterslodge_avatar");
}

function hunterslodge_avatar_permanent_define_item(){
	set_item_setting("cannotdiscard","true","hunterslodge_avatar_permanent");
	set_item_setting("description","This fancy picture frame allows you to change your Avatar picture as often as you like!","hunterslodge_avatar_permanent");
	set_item_setting("dkpersist","true","hunterslodge_avatar_permanent");
	set_item_setting("image","hunterslodge_avatar_permanent.png","hunterslodge_avatar_permanent");
	set_item_setting("inventorylocation","lodgebag","hunterslodge_avatar_permanent");
	set_item_setting("lodge","true","hunterslodge_avatar_permanent");
	set_item_setting("context_lodge","true","hunterslodge_avatar_permanent");
	set_item_setting("lodge_cost","1000","hunterslodge_avatar_permanent");
	set_item_setting("lodge_longdesc","This picture frame allows you to upload a picture of your choosing, to be used in your Bio and in commentary mouseover areas. The image can be up to 100 pixels wide, by 100 pixels tall, and no larger than 100 kilobytes, in .jpg or .png format.`n`nThis item allows you to change your avatar picture any time you like without further cost. `4Only picture frames that have never been used can be given to other players,`0 so if you're buying this as a gift, it's a good idea to give it to the recipient straight away.`n`nYou can use this item from any Hunter's Lodge.","hunterslodge_avatar_permanent");
	set_item_setting("verbosename","Fancy Picture Frame","hunterslodge_avatar_permanent");
	set_item_setting("require_file","hunterslodge_avatar.php","hunterslodge_avatar_permanent");
	set_item_setting("call_function","hunterslodge_avatar_permanent_use","hunterslodge_avatar_permanent");
}

function hunterslodge_avatar_use($args){
	redirect("runmodule.php?module=hunterslodge_avatar&op=change");
}

function hunterslodge_avatar_permanent_use($args){
	redirect("runmodule.php?module=hunterslodge_avatar&op=change&free=1");
}

?>