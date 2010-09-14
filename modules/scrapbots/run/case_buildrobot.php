<?php

$playerscrap = scrapbots_get_player_scrap();

require_once "modules/staminasystem/lib/lib.php";

$name1 = array(
	"Grunt",
	"Fisto",
	"Brains",
	"Speedy",
	"Hammer",
	"Nails",
	"Servo",
	"Bob",
	"Brock",
	"Prince",
	"Awesome",
	"Ratchet",
	"Random",
	"Locke",
	"Grawp",
	"Sparky",
	"Bastard"
);
$name2 = array(
	"The Felcher",
	"The Fister",
	"The Hatchet",
	"The Axle",
	"The Hammer",
	"Nails",
	"Servo",
	"The Rammer",
	"The Whittler",
	"The Duke",
	"The Spanker",
	"The Chainsaw",
	"The Prat",
	"The Stock",
	"The Juggernaut",
	"The Plonker",
	"The Bastard"
);
$name3 = array(
	"McGrunt",
	"McFisty",
	"McBrainy",
	"McSpeedy",
	"McHammer",
	"McNails",
	"McServo",
	"McThunderCrotch",
	"McAwesome",
	"McKing",
	"McBadass",
	"McSpanner",
	"McRandom",
	"McBarrel",
	"McJuggernaut",
	"McLaser",
	"McBastard"
);
$r1 = e_rand(0,16);
$r2 = e_rand(0,16);
$r3 = e_rand(0,16);
$name = "".$name1[$r1]." \"".$name2[$r2]."\" ".$name3[$r3]."";
$owner = $session['user']['acctid'];
$sql = "INSERT INTO ".db_prefix("scrapbots")." (owner,name,activated,hitpoints,brains,brawn,briskness,junglefighter,retreathp) VALUES ($owner,'" . mysql_real_escape_string($name) . "',0,10,1,1,1,0,0)";
db_query( $sql );
output("You get to work.  It takes a long, long time, welding and soldering and programming and grinding and smoking and cursing and sweating, but eventually your ScrapBot is complete, and magnificent.  You hook it into the diagnostics station and power it up.`n`nThe diagnostic station queries the CPUID, and returns the ScrapBot's name - %s.  The ScrapBot is ready to activate!`n`n", $name);

$solder = process_action("Soldering");
$program = process_action("Programming");
$metalwork = process_action("Metalworking");

if ($solder['lvlinfo']['levelledup']==true){
	output("`n`c`b`0You gained a level in Soldering!  You are now level %s!  This action will cost fewer Stamina points now, so you can solder up more circuitry and wiring harnesses each day!`b`c`n",$return['lvlinfo']['newlvl']);
}
if ($program['lvlinfo']['levelledup']==true){
	output("`n`c`b`0You gained a level in Programming!  You are now level %s!  This action will cost fewer Stamina points now, so you can geek out even more every day!`b`c`n",$return['lvlinfo']['newlvl']);
}
if ($metalwork['lvlinfo']['levelledup']==true){
	output("`n`c`b`0You gained a level in Metalworking!  You are now level %s!  This action will cost fewer Stamina points now, so you can grind and cut and weld more each day!`b`c`n",$return['lvlinfo']['newlvl']);
}

$playerscrap['data']['veryrareitems'][0] -= 1;
$playerscrap['data']['veryrareitems'][1] -= 1;
$playerscrap['data']['veryrareitems'][2] -= 1;
$playerscrap['data']['veryrareitems'][3] -= 1;
$playerscrap['data']['rareitems'][0] -= 2;
$playerscrap['data']['rareitems'][3] -= 1;
$playerscrap['data']['rareitems'][4] -= 1;
$playerscrap['data']['rareitems'][5] -= 1;
$playerscrap['data']['rareitems'][6] -= 1;
$playerscrap['data']['rareitems'][7] -= 4;
$playerscrap['data']['rareitems'][12] -= 1;

set_module_pref("scrap", serialize($playerscrap), "scrapbots");

?>