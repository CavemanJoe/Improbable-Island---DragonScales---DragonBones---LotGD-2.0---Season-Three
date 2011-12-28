<?php
		$cost = httpget("cost");
		if (get_module_pref("permanent")){
			$cost=0;
		}
		$set = httppost("set");
		$url = httpget("url");
		output("`7J. C. Petersen grins broadly, \"`&Excellent.  I'll take care of that for you right now.`7\"");
		if ($url!='') output("`n`nA moderator has to validate your avatar. Please be patient.");
		debug("avatar bought for $cost points");
		$session['user']['donationspent'] += $cost;
		set_module_pref("bought", 1);
		if ($set) set_module_pref("setname", $set);
		set_module_pref("avatar",$url);
		set_module_pref("validated",0);
?>