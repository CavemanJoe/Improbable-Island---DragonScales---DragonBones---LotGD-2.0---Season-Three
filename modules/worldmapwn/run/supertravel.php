<?php
if ($session['user']['superuser']==true){
	$sql="SELECT * FROM " .db_prefix("cityprefs"); 
	$result=db_query($sql);
	while ($row = db_fetch_assoc($result)) {
			$cityname=$row["cityname"];
			$cityid=$row["cityid"];
			addnav("Cities");
			addnav(array("Go to %s", $cityname), "runmodule.php?module=worldmapwn&op=arrive&dest=$cityid");
	}
	modulehook("worldmapwn-travel-superuser");
}
?>
