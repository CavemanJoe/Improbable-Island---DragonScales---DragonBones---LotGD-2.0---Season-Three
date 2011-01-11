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
	$colours = array(
		"1" => "colDkBlue",
		"2" => "colDkGreen",
		"3" => "colDkCyan",
		"4" => "colDkRed",
		"5" => "colDkMagenta",
		"6" => "colDkYellow",
		"7" => "colDkWhite",
		"~" => "colBlack",
		"!" => "colLtBlue",
		"@" => "colLtGreen",
		"#" => "colLtCyan",
		"$" => "colLtRed",
		"%%" => "colLtMagenta",
		"^" => "colLtYellow",
		"&" => "colLtWhite",
		")" => "colLtBlack",
		"e" => "colDkRust",
		"E" => "colLtRust",
		"g" => "colXLtGreen",
		"G" => "colXLtGreen",
		"j" => "colMdGrey",
		"J" => "colMdBlue",
		"k" => "colaquamarine",
		"K" => "coldarkseagreen",
		"l" => "colDkLinkBlue",
		"L" => "colLtLinkBlue",
		"m" => "colwheat",
		"M" => "coltan",
		"p" => "collightsalmon",
		"P" => "colsalmon",
		"q" => "colDkOrange",
		"Q" => "colLtOrange",
		"R" => "colRose",
		"T" => "colDkBrown",
		"t" => "colLtBrown",
		"V" => "colBlueViolet",
		"v" => "coliceviolet",
		"x" => "colburlywood",
		"X" => "colbeige",
		"y" => "colkhaki",
		"Y" => "coldarkkhaki",
	);
	page_header("Colour Test");
	output("`b`cPlease ensure that your monitor is correctly calibrated before making any changes to your templates.`b  It also helps a lot to have a friend do this with you - people's eyes are very different.`c`n`n");
	rawoutput("An excellent resource for monitor calibration, including test images and tutorials, can be found <a href=\"http://www.normankoren.com/makingfineprints1A.html\"> here.</a>");
	output("`n`n`0We are now testing each colour, to see how it looks against the background for this template.`n`nMake sure that each colour is clear and easily-readable.  It's a good idea to test on a variety of monitors, both CRT and LCD, at different colour depths and resolutions, and in different ambient light.  The ColorZilla and FireBug extensions for FireFox are invaluable for determining appropriate colours.`n`n");
	
	foreach($colours AS $code => $colour){
		output_notl("`0CCode ".$code.", CSS code ".$colour.": `".$code."".$teststring);
	}
	addnav("Back to the Superuser grotto","superuser.php");
	addnav("Reload this page","runmodule.php?module=colourtest");
	page_footer();
}
?>