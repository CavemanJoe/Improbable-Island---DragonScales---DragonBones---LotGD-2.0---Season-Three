<?php

global $session;

page_header("Titan!");

output("`0The Titan falls to the earth with a mighty crash, and lies still.  All combatants are immediately issued a Requisition reward to their bank accounts.  Offshore, `4The Watcher`0 smiles.`n`n");
addnav("Crisis averted!");
addnav("Back to the Map","runmodule.php?module=worldmapen&op=continue");

page_footer();

?>