<?php
		$cost = httpget("cost");
		$allowsets=get_module_setting("allowsets");
		$pointsavail = $session['user']['donation'] -
			$session['user']['donationspent'];
		output("`7J. C. Petersen leads you into a back room filled with portraits, and guides you over to a group of images for your race.`n`n");
		output("`n`7J. C. Petersen smiles, \"`&Of course, each race has its own set of images, but these are the possible ones that you can choose right now.  Your image will always reflect your current race.`7\"`n`n");
		output("If you would like to see all of the images, feel free to look around the gallery.`n");
		output("You might also `iadd a portrait on your own`i if you tell him where he has to look for it.`n`n");
		if ($allowsets) addnav("Gallery", "runmodule.php?module=avatar&op=view");

		if ($pointsavail < $cost) {
			if (!get_module_pref("bought")) {
				output("`7He glances at his ledger, \"`&Unfortunately, purchasing a portrait will cost you %s points, and you currently only have %s %s available to spend.`7\"", $cost, $pointsavail, translate_inline($pointsavail==1?"point":"points"));
			} else {
				output("`7He glances at his ledger, \"`&Unfortunately, changing your portrait will cost you %s points, and you currently only have %s %s available to spend.`7\"", $cost, $pointsavail, translate_inline($pointsavail==1?"point":"points"));
			}
		} else {
			if ($allowsets) output("`7He steps back to let you admire the pictures for a moment, \"`&So, does one of these suit you?`7\"");
		}
		$race = strtolower($session['user']['race']);
		if ($session['user']['sex'] == SEX_MALE) {
			$gender = "male";
		} else {
			$gender = "female";
		}
		$button = false;
		if ($pointsavail >= $cost) {
			$button = "Purchase";
			if (get_module_pref("bought")) $button= "Change";
			$button = translate_inline($button);
			if (get_module_setting("allowpersonal")) {
				addnav("Personal Avatar","runmodule.php?module=avatar&op=personal&cost=$cost");
				output("`gYou can also enter an URL to point to an avatar of your choice somewhere on the web.`7`n`n");
			}
			if (get_module_setting("allowupload")) {
				addnav("Upload Avatar","runmodule.php?module=avatar&op=upload&cost=$cost");
				output("`lYou can also upload files up to a size of `v%s`l bytes for use on this server, the pictures cannot be linked from the outside.`n`n`7",get_module_setting('uploadsize'));
			}
			if (get_module_setting("restrictsize")) {
				$maxwidth = get_module_setting("maxwidth");
				$maxheight = get_module_setting("maxheight");
				output("`n`nPlease note that there are regulations concerning the size.`n");
				output("You may not have an avatar that has a width of more than %s pixels or a height of more than %s pixels.",$maxwidth,$maxheight);
				output("`n`nAny larger picture will be scaled to a smaller size.");
			}
		}

		$set = get_module_pref("setname");
		if ($allowsets) {
			rawoutput("<form method='POST' action='runmodule.php?module=avatar&op=yes&cost=$cost'>");
			$image = avatar_get_all_images($race, $gender, $set, $button);
			rawoutput($image);
			rawoutput("</form>");
			addnav("", "runmodule.php?module=avatar&op=yes&cost=$cost");
		} else {
			output("\"`&Oh... I forgot... the local gods don't allow these sets to be used in public, sorry... choose a personal one if you like.`7\", states J.C. Petersen.");
		}
?>