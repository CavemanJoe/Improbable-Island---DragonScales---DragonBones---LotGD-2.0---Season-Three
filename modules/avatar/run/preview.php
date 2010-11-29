<?php
		$cost=httpget("cost");
		$url=httppost('url');
		output("`7J.C.P. takes a good look at you.`n");
		output("So this is how you want to look like?`n`n");
		$image="<img align='left' src='".$url."' ";
		if (get_module_setting("restrictsize")) {
			//stripped lines from Anpera's avatar module =)
			$maxwidth = get_module_setting("maxwidth");
			$maxheight = get_module_setting("maxheight");
			$pic_size = @getimagesize($url); // GD2 required here - else size always is recognized as 0
			$pic_width = $pic_size[0];
			$pic_height = $pic_size[1];
			if ($pic_width > $maxwidth) $image.=" width=\"$maxwidth\" ";
			if ($pic_height > $maxheight) $image.=" height=\"$maxheight\" ";
		}
		$image.=">";
		rawoutput("<table><tr><td valign='top'>");
		output("`^Avatar:`0`n");
		rawoutput("</td><td valign='top'>$image</td></tr><td></td><td>$url</td></table>");
		addnav("Yes","runmodule.php?module=avatar&op=yes&cost=$cost&url=".rawurlencode($url));
		addnav("No","runmodule.php?module=avatar&op=personal&cost=$cost");
?>