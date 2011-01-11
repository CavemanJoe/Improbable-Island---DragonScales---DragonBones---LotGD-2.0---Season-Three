<?php

global $session;
define("ALLOW_ANONYMOUS",true);
define("OVERRIDE_FORCED_NAV",true);

require_once "common.php";
require_once "lib/commentary.php";

$section = $_REQUEST['section'];
//$section = "village";

sleep(5);

//todo: get return link properly, check the player's chatloc matches up with the commentary area

$commentary = preparecommentaryblock($section);
echo $section;
$commentary = appoencode($commentary,true);
echo($commentary);

saveuser();



?>