<?php

	$pointsavailable=$session['user']['donation']-$session['user']['donationspent'];
	$permcost = get_module_setting("permanent");
	output("For %s Donator Points, you can change your Avatar as often as you like without paying again.`n`n",$permcost);
	addnav("Unlimited Changes");
	if ($pointsavailable>=$permcost){
		addnav(array("Get permanent free Avatar changes (%s Points)",$permcost),"runmodule.php?module=avatar&op=permanentconfirm");
	} else {
		addnav(array("Sorry, but you need %s more Donator Points for this option.",$permcost-$pointsavailable),"");
	}

?>