<?php
if (($session['user']['superuser'] & SU_EDIT_USERS) || get_module_pref("canedit")) {
		addnav("Module Configurations");
		addnav("Map file configuration","runmodule.php?module=worldmapwn&op=config&admin=true");
}
?>
