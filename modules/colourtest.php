<?php

function colourtest_getmoduleinfo(){
	$info = array(
		"name"=>"Test Pattern",
		"author"=>"Dan Hall",
		"version"=>"2008-11-22",
		"category"=>"Administrative",
		"download"=>"",
	);
	
	return $info;
}

function colourtest_install(){
	module_addhook("superuser");
	return true;
}

function colourtest_uninstall(){
	return true;
}

function colourtest_dohook($hookname,$args){
	global $session;
	switch($hookname){
	case "superuser":
		addnav("Run a colour test on this template","runmodule.php?module=colourtest");
		break;
	}
	return $args;
}

function colourtest_run(){
	global $session;
	$teststring = "ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz 1234567890 !@#\$%%^&*()`n";
	page_header("Colour Test");
	output("`b`cPlease ensure that your monitor is correctly calibrated before making any changes to your templates.`b  It also helps a lot to have a friend do this with you - people's eyes are very different.`c`n`n");
	rawoutput("An excellent resource for monitor calibration, including test images and tutorials, can be found <a href=\"http://www.normankoren.com/makingfineprints1A.html\"> here.</a>");
	output("`n`n`0We are now testing each colour, to see how it looks against the background for this template.`n`nMake sure that each colour is clear and easily-readable.  It's a good idea to test on a variety of monitors, both CRT and LCD, at different colour depths and resolutions, and in different ambient light.  The ColorZilla and FireBug extensions for FireFox are invaluable for determining appropriate colours.`n`n");
	output("`0Testing colour codes compatible with v1.0.6 and above`n");
	output("`0CCode 1: `1%s",$teststring);
	output("`0CCode 2: `2%s",$teststring);
	output("`0CCode 3: `3%s",$teststring);
	output("`0CCode 4: `4%s",$teststring);
	output("`0CCode 5: `5%s",$teststring);
	output("`0CCode 6: `6%s",$teststring);
	output("`0CCode 7: `7%s",$teststring);
	output("`0CCode !: `!%s",$teststring);
	output("`0CCode @: `@%s",$teststring);
	output("`0CCode #: `#%s",$teststring);
	output("`0CCode $: `$%s",$teststring);
	output("`0CCode %%: `%%s",$teststring);
	output("`0CCode ^: `^%s",$teststring);
	output("`0CCode &: `&%s",$teststring);
	output("`0CCode ): `)%s",$teststring);
	output("`0CCode q: `q%s",$teststring);
	output("`0CCode Q: `Q%s",$teststring);
	output("`n`0Testing colour codes compatible with v1.1.0 and above`n");
	output("`0CCode ~: `~%s",$teststring);
	output("`0CCode e: `e%s",$teststring);
	output("`0CCode E: `E%s",$teststring);
	output("`0CCode t: `t%s",$teststring);
	output("`0CCode T: `T%s",$teststring);
	output("`0CCode j: `j%s",$teststring);
	output("`0CCode J: `J%s",$teststring);
	output("`0CCode l: `l%s",$teststring);
	output("`0CCode L: `L%s",$teststring);
	output("`0CCode v: `v%s",$teststring);
	output("`0CCode V: `V%s",$teststring);
	output("`0CCode g: `g%s",$teststring);
	output("`0CCode G: `G%s",$teststring);
	output("`0CCode r: `r%s",$teststring);
	output("`0CCode R: `R%s",$teststring);
	output("`n`0`bTesting in bold`n");
	output("`0Testing colour codes compatible with v1.0.6 and above`n");
	output("`0CCode 1: `1%s",$teststring);
	output("`0CCode 2: `2%s",$teststring);
	output("`0CCode 3: `3%s",$teststring);
	output("`0CCode 4: `4%s",$teststring);
	output("`0CCode 5: `5%s",$teststring);
	output("`0CCode 6: `6%s",$teststring);
	output("`0CCode 7: `7%s",$teststring);
	output("`0CCode !: `!%s",$teststring);
	output("`0CCode @: `@%s",$teststring);
	output("`0CCode #: `#%s",$teststring);
	output("`0CCode $: `$%s",$teststring);
	output("`0CCode %%: `%%s",$teststring);
	output("`0CCode ^: `^%s",$teststring);
	output("`0CCode &: `&%s",$teststring);
	output("`0CCode ): `)%s",$teststring);
	output("`0CCode q: `q%s",$teststring);
	output("`0CCode Q: `Q%s",$teststring);
	output("`n`0Testing colour codes compatible with v1.1.0 and above`n");
	output("`0CCode ~: `~%s",$teststring);
	output("`0CCode e: `e%s",$teststring);
	output("`0CCode E: `E%s",$teststring);
	output("`0CCode t: `t%s",$teststring);
	output("`0CCode T: `T%s",$teststring);
	output("`0CCode j: `j%s",$teststring);
	output("`0CCode J: `J%s",$teststring);
	output("`0CCode l: `l%s",$teststring);
	output("`0CCode L: `L%s",$teststring);
	output("`0CCode v: `v%s",$teststring);
	output("`0CCode V: `V%s",$teststring);
	output("`0CCode g: `g%s",$teststring);
	output("`0CCode G: `G%s",$teststring);
	output("`0CCode r: `r%s",$teststring);
	output("`0CCode R: `R%s",$teststring);
	output("`n`b`i`0Testing in italics`n");	
	output("`0Testing colour codes compatible with v1.0.6 and above`n");
	output("`0CCode 1: `1%s",$teststring);
	output("`0CCode 2: `2%s",$teststring);
	output("`0CCode 3: `3%s",$teststring);
	output("`0CCode 4: `4%s",$teststring);
	output("`0CCode 5: `5%s",$teststring);
	output("`0CCode 6: `6%s",$teststring);
	output("`0CCode 7: `7%s",$teststring);
	output("`0CCode !: `!%s",$teststring);
	output("`0CCode @: `@%s",$teststring);
	output("`0CCode #: `#%s",$teststring);
	output("`0CCode $: `$%s",$teststring);
	output("`0CCode %%: `%%s",$teststring);
	output("`0CCode ^: `^%s",$teststring);
	output("`0CCode &: `&%s",$teststring);
	output("`0CCode ): `)%s",$teststring);
	output("`0CCode q: `q%s",$teststring);
	output("`0CCode Q: `Q%s",$teststring);
	output("`n`0Testing colour codes compatible with v1.1.0 and above`n");
	output("`0CCode ~: `~%s",$teststring);
	output("`0CCode e: `e%s",$teststring);
	output("`0CCode E: `E%s",$teststring);
	output("`0CCode t: `t%s",$teststring);
	output("`0CCode T: `T%s",$teststring);
	output("`0CCode j: `j%s",$teststring);
	output("`0CCode J: `J%s",$teststring);
	output("`0CCode l: `l%s",$teststring);
	output("`0CCode L: `L%s",$teststring);
	output("`0CCode v: `v%s",$teststring);
	output("`0CCode V: `V%s",$teststring);
	output("`0CCode g: `g%s",$teststring);
	output("`0CCode G: `G%s",$teststring);
	output("`0CCode r: `r%s",$teststring);
	output("`0CCode R: `R%s",$teststring);
	addnav("Back to the Superuser grotto","superuser.php");
	addnav("Reload this page","runmodule.php?module=colourtest");
	page_footer();
}
?>