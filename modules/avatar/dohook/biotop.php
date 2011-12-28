<?php
	if (get_module_setting("navdisplay")) {
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

				// Make the avatar image collapsible away.  Some people view the
				// game from work and having the avatar image makes it VERY
				// obviously a non-work site even in work-friendly skins
				// addnavheader("Avatar", false);
				addnavheader("Avatar");
				global $templatename;
				if ($templatename == "Classic.htm") {
					$image = "<tr><td>$image</td></tr>";
				}
				addnav("$image","!!!addraw!!!",true);
			}
		} elseif (get_module_setting("bioheaddisplay")) {
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
				rawoutput($image);
			}
		}
?>