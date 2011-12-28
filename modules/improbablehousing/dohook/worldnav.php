<?php
	$loc = get_module_pref("worldXYZ","worldmapen");
	require_once "modules/improbablehousing/lib/lib.php";
	improbablehousing_shownearbyhouses($loc);
	improbablehousing_stakeable($loc);

?>