<?php
	if ($session['user']['location']=="Improbable Central"){
		tlschema($args['schemas']['fightnav']);
		addnav($args['fightnav']);
		tlschema();
		addnav("Suzie's Hardware","runmodule.php?module=improbablehousing&op=landregistry&sub=start");
	}
?>