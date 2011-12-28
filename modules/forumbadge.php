<?php

function forumbadge_getmoduleinfo(){
	$info = array(
		"name"=>"Forum Badge",
		"author"=>"Dan Hall",
		"version"=>"2008-11-22",
		"category"=>"Administrative",
		"download"=>"",
		"allowanonymous"=>"true",
	);
	
	return $info;
}

function forumbadge_install(){
	return true;
}

function forumbadge_uninstall(){
	return true;
}

function forumbadge_run(){
	global $session;
	$acctid = httpget("acctid");
	page_header("Badge!");
	//PHP's GD class functions can create a variety of output image
	//types, this example creates a jpeg
	header("Content-Type: image/jpeg");

	//open up the image you want to put text over
	$im = ImageCreateFromPng("modules/buttonbase.png");

	//The numbers are the RGB values of the color you want to use
	$black = ImageColorAllocate($im, 0, 0, 0);

	//The canvas's (0,0) position is the upper left corner
	//So this is how far down and to the right the text should start
	$start_x = 10;
	$start_y = 20;

	$sql = "SELECT race, login, level, dragonkills FROM ".db_prefix("accounts")." WHERE acctid = $acctid";
	$result = db_fetch_assoc(db_query($sql));
	$name = $result['login'];
	$race = $result['race'];
	$level = "Level ".$result['level'];
	$dragonkills = "Drive Kills: ".$result['dragonkills'];

	debug($result);
	
	//This writes your text on the image in 12 point using verdana.ttf
	//For the type of effects you quoted, you'll want to use a truetype font
	//And not one of GD's built in fonts. Just upload the ttf file from your
	//c: windows fonts directory to your web server to use it.
	Imagettftext($im, 10, 0, 65, 12, $black, 'modules/verdana.ttf', $name);
	Imagettftext($im, 10, 0, 65, 27, $black, 'modules/verdana.ttf', $race);
	Imagettftext($im, 10, 0, 65, 42, $black, 'modules/verdana.ttf', $level);
	Imagettftext($im, 10, 0, 65, 57, $black, 'modules/verdana.ttf', $dragonkills);
	//Creates the jpeg image and sends it to the browser
	//100 is the jpeg quality percentage
	Imagejpeg($im, '', 60);

	ImageDestroy($im); 
	page_footer();
}
?>