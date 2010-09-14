<?php

global $session;

$hid = httpget('hid');
require_once "modules/improbablehousing/lib/lib.php";
$house = improbablehousing_gethousedata($hid);

page_header("It's locked!");
output("You could have sworn the door was unlocked a moment ago!`n`n");
addnav("Hmm.");
addnav("Go out and come back in again, maybe someone will have unlocked it.","runmodule.php?module=improbablehousing&op=exit&hid=$hid");
page_footer();

?>