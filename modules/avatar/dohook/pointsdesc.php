<?php
		$args['count']++;
		$format = $args['format'];
		$str = translate("For %s points, you will get an avatar picture to display on your bio page.  You can change it to a different avatar at a later time for %s additional points.");
		$str = sprintf($str, $cost, $changecost);
		output($format, $str, true);
?>