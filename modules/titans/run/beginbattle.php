<?php

global $session,$badguy,$battle;
require_once "modules/titans/lib/lib.php";

page_header("Titan!");

$titanid = httpget("titanid");

//this file is only really here so that we can check the Titan hasn't been killed in between the player finding it and clicking on the battle link.
$titan = titans_get_titan($titanid);
if ($titan['battlelog']['killed'] || !$titan){
	//debug($titan);
	output("`0You bound over to the Titan with a big silly bloodlust-grin plastered over your face - but when you get there, you find it already dead!`n`n");
	addnav("Well, that's a bummer.");
	addnav("Back to the map","runmodule.php?module=worldmapen&op=continue");
} else {
	redirect("runmodule.php?module=titans&titanop=battle&titanid=".$titanid);
}

page_footer();

?>