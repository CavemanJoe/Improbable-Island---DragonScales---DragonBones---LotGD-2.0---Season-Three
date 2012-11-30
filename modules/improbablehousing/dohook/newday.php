<?php
	require_once("modules/iitems/lib/lib.php");
	if (iitems_has_item("toolbox_decorating") || iitems_has_item("toolbox_carpentry") || iitems_has_item("toolbox_masonry")){
		output("You look around for your tools, but find none.  Suzie's staff must taken them back during the night.`n`n");
		delete_all_items_of_type("toolbox_decorating");
		delete_all_items_of_type("toolbox_carpentry");
		delete_all_items_of_type("toolbox_masonry");
	}
?>
