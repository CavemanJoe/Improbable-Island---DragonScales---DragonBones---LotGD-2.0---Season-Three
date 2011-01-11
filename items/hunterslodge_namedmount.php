<?php

function hunterslodge_namedmount_define_item(){
	set_item_setting("cannotdiscard","true","hunterslodge_namedmount");
	set_item_setting("description","This collar lets you assign a name to your Mount, like \"Errold the Zombie Donkey\" and such.","hunterslodge_namedmount");
	set_item_setting("dkpersist","true","hunterslodge_namedmount");
	set_item_setting("giftable","true","hunterslodge_namedmount");
	set_item_setting("image","hunterslodge_namedmount.png","hunterslodge_namedmount");
	set_item_setting("inventorylocation","lodgebag","hunterslodge_namedmount");
	set_item_setting("lodge","true","hunterslodge_namedmount");
	set_item_setting("context_lodge","true","hunterslodge_namedmount");
	set_item_setting("lodge_cost","100","hunterslodge_namedmount");
	set_item_setting("lodge_longdesc","This collar allows you to change the name of your Mount. In your Bio pages, viewable by anyone, your mount will be called \"(name you choose) the (type of your mount),\" for example \"Fido the Budget Horse.\" You can use colour codes in there too, up to a maximum of 25 characters. Once you've changed the name of your Mount, any other Mount you buy thereafter will have the same given name.`n`nThis item lets you change the name of your Mount once - subsequent changes will need more Collars. If you think you might want to change your Mount's name a lot, you might want to look at the Deluxe Collar instead.`n`nYou can use this item from any Hunter's Lodge.","hunterslodge_namedmount");
	set_item_setting("verbosename","Collar","hunterslodge_namedmount");
	set_item_setting("require_file","hunterslodge_namedmount.php","hunterslodge_namedmount");
	set_item_setting("call_function","hunterslodge_namedmount_use","hunterslodge_namedmount");
}

function hunterslodge_namedmount_permanent_define_item(){
	set_item_setting("cannotdiscard","true","hunterslodge_namedmount_permanent");
	set_item_setting("description","This fancy collar lets you change your Mount's name as often as you like!","hunterslodge_namedmount_permanent");
	set_item_setting("dkpersist","true","hunterslodge_namedmount_permanent");
	set_item_setting("giftable","true","hunterslodge_namedmount_permanent");
	set_item_setting("image","hunterslodge_namedmount_permanent.png","hunterslodge_namedmount_permanent");
	set_item_setting("inventorylocation","lodgebag","hunterslodge_namedmount_permanent");
	set_item_setting("lodge","true","hunterslodge_namedmount_permanent");
	set_item_setting("context_lodge","true","hunterslodge_namedmount_permanent");
	set_item_setting("lodge_cost","1000","hunterslodge_namedmount_permanent");
	set_item_setting("lodge_longdesc","This collar allows you to change the name of your Mount. In your Bio pages, viewable by anyone, your mount will be called \"(name you choose) the (type of your mount),\" for example \"Fido the Budget Horse.\" You can use colour codes in there too, up to a maximum of 25 characters. Once you've changed the name of your Mount, any other Mount you buy thereafter will have the same given name.`n`nThis item lets you change the name of your Mount as many times as you like, but remember that `4only unused Collars can be gifted.`0 Secondhand collars covered in spit and fur do not a nice gift make.`n`nYou can use this item from any Hunter's Lodge.","hunterslodge_namedmount_permanent");
	set_item_setting("verbosename","Deluxe Collar","hunterslodge_namedmount_permanent");
	set_item_setting("require_file","hunterslodge_namedmount.php","hunterslodge_namedmount_permanent");
	set_item_setting("call_function","hunterslodge_namedmount_permanent_use","hunterslodge_namedmount_permanent");
}

function hunterslodge_namedmount_use($args){
	global $session, $playermount;
	if (count($playermount)==0){
		output("`0You don't have a Mount to name!`n");
	} else {
		redirect("runmodule.php?module=hunterslodge_namedmount&op=change");
	}
}

function hunterslodge_namedmount_permanent_use($args){
	global $session, $playermount;
	if (count($playermount)==0){
		output("`0You don't have a Mount to name!`n");
	} else {
		redirect("runmodule.php?module=hunterslodge_namedmount&op=change&free=1");
	}
}

?>