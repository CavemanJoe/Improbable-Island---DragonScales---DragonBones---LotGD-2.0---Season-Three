<?php

//BANG GRENADE

function cratesniffer_define_item(){
	set_item_setting("context_worldmap","1","cratesniffer");
	set_item_setting("cratefind","20","cratesniffer");
	set_item_setting("description","This crudely-built device has enough power to send out a radio pulse to any supply crates within 3 klicks of your current location. It then displays the number of pings it receives back from supply crates, effectively telling you how many crates are nearby.  Typically these cheaply-made devices only survive a single use.","cratesniffer");
	set_item_setting("eboy","true","cratesniffer");
	set_item_setting("image","cratesniffer.png","cratesniffer");
	set_item_setting("verbosename","Crate Sniffer","cratesniffer");
	set_item_setting("weight","1.8","cratesniffer");
	set_item_setting("require_file","cratesniffer.php","cratesniffer");
	set_item_setting("call_function","cratesniffer_use","cratesniffer");
}

function cratesniffer_use($args){
	output("`0You thumb the switch on your Crate Sniffer.  It buzzes and hisses for a moment, exhausting its primitive battery sending out a radio ping to nearby Crates.`n`n");
	$crates = unserialize(get_module_setting("crates","iitemcrates"));
	$ploc = get_module_pref("worldXYZ","worldmapen");
	list($px, $py, $pz) = explode(",", $ploc);
	
	$pxlow = $px-3;
	$pxhigh = $px+3;
	$pylow = $py-3;
	$pyhigh = $py+3;
	
	if (!is_array($crates)) {
		$crates = array();
	}
	$count = 0;
	foreach($crates AS $key => $vals){
		if ($vals['loc']['x'] >= $pxlow && $vals['loc']['x'] <= $pxhigh && $vals['loc']['y'] >= $pylow && $vals['loc']['y'] <= $pyhigh){
			$count++;
		}
	}
	output("It displays, weakly, the number `\$`b%s`b`0 in dull red LED's before its radio module catches fire.`n`n",$count);
	delete_item($args['id']);
}

?>