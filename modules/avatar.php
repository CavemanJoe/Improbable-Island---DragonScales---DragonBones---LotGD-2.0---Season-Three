<?php
/*
1.01 added a validation function + you can now enter a URL for an avatar. Okay, this won't help that much if player want to cheat, but it's a security issue

*/
function avatar_getmoduleinfo() {
	$info = array(
		"name" => "Bio Avatars",
		"version" => "1.01",
		"author" => "JT Traub`n`&modified by `2Oliver Brendel",
		"category" => "Lodge",
		"download" => "core_module",
		"settings" => array(
			"Bio Avatar Settings,title",
			"cost"=>"What is the cost of having an avatar?,int|500",
			"changecost"=>"What is the cost of changing your avatar?,int|25",
			"permanent"=>"Cost of unlimited Avatar changes,int|1000",
			"allowpersonal"=>"Allow personal avatars entered via URL?,bool|1",
			"allowsets"=>"Allow sets to be selected by the player?,bool|1",
			"Upload Settings,title",
			"allowupload"=>"Allow users to be uploaded directly?,bool|1",
			"uploaddir"=>"If yes - what folder (needs 777 permissions!) should be used locally (do not add a / at the end and use relative from the document root downwards!)?,text|avatars",
			"the default is avatars - which is in your lotgd root the folder avatars. You need to create that folder and give it the proper permissions!,note",
			"it is also commonsense to put a .htaccess file into it to deny all accesses from outside the host,note",
			"uploadsize"=>"How many bytes can future uploaded avatars have?,int|50000",
			"Size restrictions,title",
			"restrictsize"=>"Is the size restricted?,bool|1",
			"maxwidth"=>"Max. width of personal avatars (Pixel),range,20,400,20|200",
			"maxheight"=>"Max. height of personal avatars (Pixel),range,20,400,20|200",
			"Display Settings,title",
			"Note: If you do not show the avatar in the nav then it will be displayed in the bio itself,note",
			"navdisplay"=>"Display the avatar in the nav,bool|0",
			"Note: if you have it in the bio then do you want to have it at the top? If not it will be a simple line more,note",
			"bioheaddisplay"=>"Display the avatar at the top of the bioinfo text?,bool|1",
		),
		"prefs"=>array(
			"Bio Avatar User Preferences,title",
			"bought"=>"Has the player bought an avatar yet?,bool,0",
			"setname"=>"Which set is the player using?|vixy1",
			"avatar"=>"URL of personal your avatar|",
			"user_seeavatar"=>"Show your avatar in your user bio?,bool|1",
			"user_seeotheravatars"=>"Show other avatars to their bio?,bool|1",
			"validated"=>"Is this avatar validated?,bool|0",
			"permanent"=>"Player has bought unlimited free avatar changes,bool|0",
		),
	);
	return $info;
}

function avatar_install() {
	module_addhook("lodge");
	module_addhook("pointsdesc");
	module_addhook("superuser");
	module_addhook("header-superuser");
	// Let's get our hook at the top.
	module_addhook_priority("biotop", 5);
	//for normal display
	module_addhook_priority("bioinfo",50);
	return true;
}

function avatar_uninstall() {
	return true;
}

function avatar_dohook($hookname, $args) {
	global $session;
	$cost = get_module_setting("cost");
	$changecost = get_module_setting("changecost");
	require_once("modules/avatar/func.php");
	require("modules/avatar/dohook/$hookname.php");
	return $args;
}


function avatar_run() {
	global $session;
	$op = httpget("op");
	require_once("modules/avatar/func.php");
	if ($op=='validate') {
		page_header("Avatar Validation");
	} else {
		page_header("Hunter's Lodge");
	}
	require("modules/avatar/run/$op.php");
	if ($op!='validate') {
		addnav("Return");
		addnav("L?Return to Lodge", "lodge.php");
	}
	page_footer();
}


?>
