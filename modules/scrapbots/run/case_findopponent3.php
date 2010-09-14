<?php

output("TODO: Check if opponent has scrapbots, can be attacked etc`n`n");
$name = httpget('name');
$sql = "SELECT acctid FROM " . db_prefix("accounts") . " WHERE login='$name'";

$result = db_query($sql);
$row = db_fetch_assoc($result);

require_once("modules/scrapbots/battle-2.php");
scrapbots_battle($row['acctid']);

?>