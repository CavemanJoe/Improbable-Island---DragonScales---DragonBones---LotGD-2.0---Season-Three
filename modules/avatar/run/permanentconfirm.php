<?php

	output("You've got unlimited Avatar Changes!");
	set_module_pref("permanent",1);
	$session['user']['donationspent']+=get_module_setting("permanent");

?>