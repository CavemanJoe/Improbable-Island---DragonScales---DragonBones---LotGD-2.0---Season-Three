<?php
		addnav("Avatar");
		if (get_module_pref("permanent")){
			addnav("Change Avatar Picture (free)","runmodule.php?module=avatar&op=purchase&cost=0");
		} else {
			if (!get_module_pref("bought")) {
				addnav(array("Bio Avatar Picture (%s %s)", $cost,
						translate_inline($cost == 1 ? "point" : "points")),
					"runmodule.php?module=avatar&op=purchase&cost=$cost");
			} else {
				addnav(array("Change Avatar Picture (%s %s)", $changecost,
						translate_inline($changecost == 1 ? "point" : "points")),
					"runmodule.php?module=avatar&op=purchase&cost=$changecost");
			}
			addnav(array("Get unlimited Avatar picture changes (%s points)",get_module_setting("permanent")),"runmodule.php?module=avatar&op=permanent");
		}
?>