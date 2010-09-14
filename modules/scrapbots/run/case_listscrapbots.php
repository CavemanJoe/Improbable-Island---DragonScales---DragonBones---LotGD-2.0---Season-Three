<?php

$sql = "SELECT id,owner,name,activated,hitpoints,brains,brawn,briskness,junglefighter,retreathp FROM ".db_prefix("scrapbots")." WHERE owner = ".$session['user']['acctid'];
$result = db_query($sql);

output("`1");
output("The following ScrapBots are in your command:`n`n");
rawoutput("<table width=100%><tr><b><td>Name</td><td>HP</td><td>Brains</td><td>Brawn</td><td>Briskness</td><td>Retreat %</td></b></tr>");

for ($i=0;$i<db_num_rows($result);$i++){
	$row=db_fetch_assoc($result);
	rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td><b>".$row['name']."</b> (CPUID ".$row['id'].")<br />".($row['activated']==1?"`2Activated`0":"`4Deactivated`0")."<br /><a href=\"runmodule.php?module=scrapbots&op=managescrapbot&bot=".$row['id']."\">Manage this ScrapBot</a></td><td>".$row['hitpoints']."</td><td>".$row['brains']."</td><td>".$row['brawn']."</td><td>".$row['briskness']."</td><td>".$row['retreathp']."</td></tr>");
	addnav("","runmodule.php?module=scrapbots&op=managescrapbot&bot=".$row['id']);
}
rawoutput("</table>");
output("`n`n`0");

?>