<?php
		if (!get_module_setting("navdisplay")&& !get_module_setting("bioheaddisplay"))
		if (get_module_pref("user_seeavatar") &&
				get_module_pref("bought", "avatar", $args['acctid']) && get_module_pref("user_seeotheravatars")) {
			$set = get_module_pref("setname", "avatar", $args['acctid']);
			$race = strtolower($args['race']);
			if ($args['sex'] == SEX_MALE) {
				$gender = "male";
			} else {
				$gender = "female";
			}
			$image = avatar_getimage($race, $gender, $set,true,$args['acctid']);
			rawoutput("<table><tr><td valign='top'>");
			output("`^Avatar:`0`n");
			rawoutput("</td><td valign='top'>$image</td></tr></table>");
			//rawoutput($image);
		}
?>