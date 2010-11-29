<?php
		$cost=httpget("cost");
		$send=translate_inline("Preview");
		$value=get_module_pref("avatar");
		output("`7J.C.P. takes a good look at you.`n");
		output("\"`&So you want a personal picture. In this case, please tell me where I have to look.`7\"`n`n");
		output("URL to your avatar:`n");
		rawoutput("<form method='POST' action='runmodule.php?module=avatar&op=preview&cost=$cost'>");
		rawoutput("<input name='url' type='text' size='40' value='$value'>");
		rawoutput("<input type='submit' class='button' value='$send'>");
		rawoutput("</form>");
		addnav("","runmodule.php?module=avatar&op=preview&cost=$cost");
		if (get_module_setting("restrictsize")) {
			$maxwidth = get_module_setting("maxwidth");
			$maxheight = get_module_setting("maxheight");
			output("`n`nPlease note that there are regulations concerning the size.`n");
			output("You may not have an avatar that has a width of more than %s pixels or a height of more than %s pixels.",$maxwidth,$maxheight);
			output("`n`nAny larger picture will be scaled to a smaller size.");
		}
		addnav("Go back to purchasing","runmodule.php?module=avatar&op=purchase&cost=$cost");
?>