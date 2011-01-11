<?php

function hunterslodge_healthinsurance_define_item(){

	set_item_setting("cannotdiscard","true","hunterslodge_healthinsurance");
	set_item_setting("description","A certificate allowing free healing, valid for one real-world week after its first use.","hunterslodge_healthinsurance");
	set_item_setting("destroyafteruse",false,"hunterslodge_healthinsurance");
	set_item_setting("dkpersist","true","hunterslodge_healthinsurance");
	set_item_setting("giftable","true","hunterslodge_healthinsurance");
	set_item_setting("image","hunterslodge_healthinsurance.png","hunterslodge_healthinsurance");
	set_item_setting("inventorylocation","lodgebag","hunterslodge_healthinsurance");
	set_item_setting("lodge","true","hunterslodge_healthinsurance");
	set_item_setting("lodge_cost","500","hunterslodge_healthinsurance");
	set_item_setting("lodge_limited",true,"hunterslodge_healthinsurance");
	set_item_setting("lodge_stock",250,"hunterslodge_healthinsurance");
	set_item_setting("lodge_longdesc","This certificate allows the bearer to receive free healing at any Hospital Tent on Improbable Island.  It's valid for one real-world week, and the clock starts ticking the first time you visit the Hospital Tent.","hunterslodge_healthinsurance");
	set_item_setting("verbosename","Health Insurance Certificate","hunterslodge_healthinsurance");
	set_item_setting("require_file","hunterslodge_healthinsurance.php","hunterslodge_healthinsurance");
	set_item_setting("call_function","hunterslodge_healthinsurance_use","hunterslodge_healthinsurance");

}

function hunterslodge_healthinsurance_use($args){
	global $session;
	$id = $args['id'];
	$expirationtime = get_item_pref("expiration_timestamp",$id);
	if (!$expirationtime){
		$expires = time() + 604800;
		set_item_pref("expiration_timestamp",$expires,$id);
		output("`0This is the first time you've used this certificate.  It will expire exactly one week from now.`n`n");
	} else {
		if (time() > $expirationtime){
			output("This certificate has now expired!`n`n");
			delete_item($id);
		} else {
			require_once "lib/datetime.php";
			$expirein = reltime($expirationtime,false);
			output("This certificate will expire in %s.`n`n",$expirein);
		}
	}
	$args['destroyafteruse'] = false;
	return $args;
}

?>