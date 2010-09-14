<?php
require_once "modules/titans/lib/lib.php";
if (httpget('fledtitan')){
	output("`0You run from the Titan as fast as your cowardly legs will carry you.  Fortunately the Titan doesn't seem to care, and will not pursue you.`n`n");
}
titans_show_nearby_titans();
titans_spawn_roll();

debug(get_module_pref("lastCity","worldmapen"));
?>