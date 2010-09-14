<?php

$make = httpget('make');
$playerscrap = scrapbots_get_player_scrap();

require_once "modules/staminasystem/lib/lib.php";

$stamina = get_stamina();
$failchance = e_rand(1,100);
if ($failchance <= $stamina){
	output("`4You're getting tired.`0  Tired people tend to be accident-prone.  Just sayin'.`n");
}

if ($failchance > $stamina){
	output("`4`c`bDisaster!`b`c`0`n");
	switch ($make){
		case "axle":
			output("`\$You grind and buff away at the Rusted Shaft until you accidentally slice it into two useless halves.  You swear, and toss them into the Scrap pile.");
			$playerscrap['data']['normalitems'][0] -= 1;
			$metalwork = process_action("Metalworking");
			break;
		case "steelplate":
			output("`\$You grind and buff away at the Rusted Steel Plate until the surface is devoid of rust - but completely ruined by your clumsy, exhausted ministrations.  You toss it back into the scrap pile, cursing at yourself.");
			$playerscrap['data']['normalitems'][1] -= 1;
			$metalwork = process_action("Metalworking");
			break;
		case "largegirderfromrusted":
			output("`\$You grind and buff away at the Rusted Large Girder until all the rust is gone, leaving only nice polished steel.  You turn away for a moment to put away your grinder, and when you turn back, it's gone.  Perhaps it was stolen by Midgets, or perhaps you're so tired you imagined the whole thing.");
			$playerscrap['data']['normalitems'][2] -= 1;
			$metalwork = process_action("Metalworking");
			break;
		case "motherboard":
			output("`\$You hook up the Broken Motherboard to the testing console, and after a little bit of tinkering with your soldering iron and multimeter, it's even more broken than it was before you started.  You toss it back into the Scrap pile.");
			$playerscrap['data']['normalitems'][3] -= 1;
			$solder = process_action("Soldering");
			break;
		case "romchip":
			output("`\$You pop the corrupted ROM Chip into your flash device, and after a bit of tinkering and cursing, it bursts into flames.  Whoops.");
			$playerscrap['data']['normalitems'][4] -= 1;
			$program = process_action("Programming");
			break;
		case "servofromrusted":
			output("`\$You grind and buff away at the Rusted Servo until it begins to give a little.  Before too long, it begins to regain its full range of motion, at which point you give it a couple of twists too hard and smash the gearbox.  You toss the now-useless hunk of metal onto the Scrap pile.");
			$playerscrap['data']['normalitems'][5] -= 1;
			$metalwork = process_action("Metalworking");
			break;
		case "wiringharness":
			output("`\$You spend a little while cutting the wires to the correct length and soldering connectors onto the ends.  Before too long, you have a pretty tidy wiring harness.  For a two-inch-long ScrapBot.  Nice going, dumbass.");
			$playerscrap['data']['normalitems'][6] -= 1;
			$solder = process_action("Soldering");
			break;
		case "battery":
			output("`\$You head outside with your dead battery, trip, and fall onto it.  Lucky that woke you up, 'cause there's acid all over the floor.  Smooth.");
			$playerscrap['data']['normalitems'][7] -= 1;
			break;
		case "wheelfrompunctured":
			output("`\$You take the punctured wheel to the office, where the Robots inside take it and then deny all knowledge of ever seeing it.  You're too tired to argue.");
			$playerscrap['data']['normalitems'][8] -= 1;
			break;
		case "wheelfromrusted":
			output("`\$You grind and buff away at the wheel until all the rust is gone, leaving only nice polished steel and a hilarious amount of punctures caused by your errant grinding.  This wheel is nice and shiny, but the tyre is beyond repair.  You toss it into the scrap pile and wonder what to destroy next.");
			$playerscrap['data']['normalitems'][9] -= 1;
			$metalwork = process_action("Metalworking");
			break;
		case "smallgirderfromrusted":
			output("`\$You grind and buff away at the little girder until all the rust is gone, and then you realise that the whole damn thing was `imade`i of rust to begin with.  If you were less tired, you probably would have noticed that before you wasted all that time buffing it.");
			$playerscrap['data']['normalitems'][10] -= 1;
			$metalwork = process_action("Metalworking");
			break;
		case "cmos":
			output("`\$You plug the tiny CMOS sensor into your test station and give it a jolly good seeing to with your soldering iron and multimeter.  Before long, it's not just broken but `iliterally on fire.`i  You congratulate yourself on a job well done, and toss its blackened remains into the Scrap pile.");
			$playerscrap['data']['normalitems'][11] -= 1;
			$solder = process_action("Soldering");
			break;
		case "lens":
			output("`\$You cut the bottom from the bottle and spend a little while grinding and polishing until it serves as a decent lens.  Then you drop it.  Whoops.");
			$playerscrap['data']['normalitems'][12] -= 1;
			$metalwork = process_action("Metalworking");
			break;
		case "servofrombroken":
			output("`\$You hook up the broken servo to a power source, and get to work with your soldering iron.  Before long it's on fire, because you wired it up straight to the mains without a step-down in between.  You toss the now-useless servo out of the window.");
			$playerscrap['data']['normalitems'][13] -= 1;
			$solder = process_action("Soldering");
			break;
		case "drivefrombroken":
			output("`\$You hook up the broken drive motor and take your soldreing iron to the dodgy connections inside.  After a little bit of poking and prodding, the smoke comes out.  You toss the now-useless drive motor out of the window and into the Scrap pile.");
			$playerscrap['data']['normalitems'][14] -= 1;
			$solder = process_action("Soldering");
			break;
		case "drivefromrusted":
			output("`\$You grind and buff away at the rusted drive motor for a while, until you accidentally grind right through the drive shaft.  Cursing, you toss the whole lot into the Scrap pile.");
			$playerscrap['data']['normalitems'][15] -= 1;
			$metalwork = process_action("Metalworking");
			break;
		case "armour":
			output("`\$You grind and cut and weld, and before long, your two steel plates have been converted into a complete and utter shambles.  Making armour while tired is probably not a good idea.  You reflect on this while tossing the mangled plates into the Scrap pile.");
			$playerscrap['data']['rareitems'][1] -= 2;
			$metalwork = process_action("Metalworking");
			break;
		case "platesfromgirders":
			output("`\$After a lot of cutting and welding, you eventually end up with no steel girders, and no steel plates either.  Just a load of random scrap metal, which you hurl angrily into the scrap pile, vowing never to try to do this while tired again.");
			$playerscrap['data']['rareitems'][8] -= 3;
			$metalwork = process_action("Metalworking");
			break;
		case "twosmallgirders":
			output("`\$It takes quite a while to saw through such a heavy girder, but that's just what you do.  Eventually you end up with one mid-sized girder and one tiny girder, the ends pointing at 45-degree angles.  They are of no use to man nor beast, and you throw them into the Scrap pile outside with a sigh.");
			$playerscrap['data']['rareitems'][2] -= 1;
			$metalwork = process_action("Metalworking");
			break;
		case "largegirderfromtwosmall":
			output("`\$You don your welding mask and get to work.  Before too long, the two small girders have become two small girders with huge, ugly welds all over them.  They look like tumours, and they scare you a little.  You throw them outside, into the Scrap pile, for some other contestant to have nightmares over.");
			$playerscrap['data']['rareitems'][8] -= 2;
			$metalwork = process_action("Metalworking");
			break;
		case "chassis":
			output("`\$You grind and weld and swear for a little while, and when you're done, you have a pretty solid-looking chassis.  A stiff breeze blows through your workshop, and it collapses immediately.  You toss the parts into the Scrap pile, and reconsider doing any more work until you're better-rested.");
			$playerscrap['data']['rareitems'][2] -= 1;
			$playerscrap['data']['rareitems'][8] -= 2;
			$metalwork = process_action("Metalworking");
			break;
		case "servolimb":
			output("`\$You grind and buff and strip and cut and weld and solder for a good long while, and by the time you're done, you're holding a big pile of crap.  It doesn't resemble an arm so much as a... well, a collection of scrap that's been tossed together by someone in dire need of some coffee.  Disgusted with yourself, you hurl it into the Scrap pile outside.");
			$playerscrap['data']['rareitems'][1] -= 1;
			$playerscrap['data']['rareitems'][8] -= 1;
			$playerscrap['data']['rareitems'][11] -= 1;
			$solder = process_action("Soldering");
			$metalwork = process_action("Metalworking");
			break;
		case "cameraeye":
			output("`\$You lay your tools out ready for the task, and then drop both the lens and the CMOS sensor at once.  A quick sweep-up later, you're back where you started, albeit with fewer spare parts.");
			$playerscrap['data']['rareitems'][9] -= 1;
			$playerscrap['data']['rareitems'][10] -= 1;
			$solder = process_action("Soldering");
			$metalwork = process_action("Metalworking");
			break;
	}
} else {
	switch ($make){
		case "axle":
			output("You grind and buff away at the Rusted Shaft until it turns into a shiny new Axle.");
			$playerscrap['data']['rareitems'][0] += 1;
			$playerscrap['data']['normalitems'][0] -= 1;
			$metalwork = process_action("Metalworking");
			break;
		case "steelplate":
			output("You grind and buff away at the Rusted Steel Plate until all the rust is gone, leaving only nice polished steel.");
			$playerscrap['data']['rareitems'][1] += 1;
			$playerscrap['data']['normalitems'][1] -= 1;
			$metalwork = process_action("Metalworking");
			break;
		case "largegirderfromrusted":
			output("You grind and buff away at the Rusted Large Girder until all the rust is gone, leaving only nice polished steel.");
			$playerscrap['data']['rareitems'][2] += 1;
			$playerscrap['data']['normalitems'][2] -= 1;
			$metalwork = process_action("Metalworking");
			break;
		case "motherboard":
			output("You hook up the Broken Motherboard to the testing console, and after a little bit of tinkering with your soldering iron and multimeter, it's running just fine.");
			$playerscrap['data']['rareitems'][3] += 1;
			$playerscrap['data']['normalitems'][3] -= 1;
			$solder = process_action("Soldering");
			break;
		case "romchip":
			output("You pop the ROM Chip into your flash device, and after a bit of tinkering and cursing, it's ready to be rewritten with new code.");
			$playerscrap['data']['rareitems'][4] += 1;
			$playerscrap['data']['normalitems'][4] -= 1;
			$program = process_action("Programming");
			break;
		case "servofromrusted":
			output("You grind and buff away at the Rusted Servo until it begins to give a little.  Before too long, it's retained its full range of motion.");
			$playerscrap['data']['rareitems'][11] += 1;
			$playerscrap['data']['normalitems'][5] -= 1;
			$metalwork = process_action("Metalworking");
			break;
		case "wiringharness":
			output("You spend a little while cutting the wires to the correct length and soldering connectors onto the ends.  Before too long, you have a pretty tidy wiring harness.");
			$playerscrap['data']['rareitems'][5] += 1;
			$playerscrap['data']['normalitems'][6] -= 1;
			$solder = process_action("Soldering");
			break;
		case "battery":
			output("You take your Dead Battery to the office and ask for a charge.  Within a few minutes, the Robots in attendance have sorted it out quite nicely, and you hand over your money.");
			$playerscrap['data']['rareitems'][6] += 1;
			$playerscrap['data']['normalitems'][7] -= 1;
			$session['user']['gold'] -= 40;
			break;
		case "wheelfrompunctured":
			output("You take the wheel over to the office, where the Robots inside happily apply one of their precious repair patches, charging you 40 Requisition for the privelage.");
			$playerscrap['data']['rareitems'][7] += 1;
			$playerscrap['data']['normalitems'][8] -= 1;
			$session['user']['gold'] -= 40;
			break;
		case "wheelfromrusted":
			output("You grind and buff away at the wheel until all the rust is gone, leaving only nice polished steel.");
			$playerscrap['data']['rareitems'][7] += 1;
			$playerscrap['data']['normalitems'][9] -= 1;
			$metalwork = process_action("Metalworking");
			break;
		case "smallgirderfromrusted":
			output("You grind and buff away at the little girder until all the rust is gone, leaving only nice polished steel.");
			$playerscrap['data']['rareitems'][8] += 1;
			$playerscrap['data']['normalitems'][10] -= 1;
			$metalwork = process_action("Metalworking");
			break;
		case "cmos":
			output("You plug the tiny CMOS sensor into your test station and give it a jolly good seeing to with your soldering iron and multimeter.  Before long, it's capturing images quite nicely.");
			$playerscrap['data']['rareitems'][9] += 1;
			$playerscrap['data']['normalitems'][11] -= 1;
			$solder = process_action("Soldering");
			break;
		case "lens":
			output("You cut the bottom from the bottle and spend a little while grinding and polishing until it serves as a decent lens.");
			$playerscrap['data']['rareitems'][10] += 1;
			$playerscrap['data']['normalitems'][12] -= 1;
			$metalwork = process_action("Metalworking");
			break;
		case "servofrombroken":
			output("You hook up the broken servo to a power source, and get to work with your soldering iron.  Before long it's whirring away pretty smoothly.");
			$playerscrap['data']['rareitems'][11] += 1;
			$playerscrap['data']['normalitems'][13] -= 1;
			$solder = process_action("Soldering");
			break;
		case "drivefrombroken":
			output("You hook up the broken drive motor and take your soldreing iron to the dodgy connections inside.  After a little bit of poking and prodding it spins into life.");
			$playerscrap['data']['rareitems'][12] += 1;
			$playerscrap['data']['normalitems'][14] -= 1;
			$solder = process_action("Soldering");
			break;
		case "drivefromrusted":
			output("You grind and buff away at the rusted drive motor for a while, and eventually it begins to spin freely.");
			$playerscrap['data']['rareitems'][12] += 1;
			$playerscrap['data']['normalitems'][15] -= 1;
			$metalwork = process_action("Metalworking");
			break;
		case "armour":
			output("You grind and cut and weld, and before long, your two steel plates have been converted into a wicked set of plate armour.");
			$playerscrap['data']['veryrareitems'][0] += 1;
			$playerscrap['data']['rareitems'][1] -= 2;
			$metalwork = process_action("Metalworking");
			break;
		case "platesfromgirders":
			output("After a lot of cutting and welding, you eventually end up with some nice Steel Plates.");
			$playerscrap['data']['rareitems'][8] -= 3;
			$playerscrap['data']['rareitems'][1] += 5;
			$metalwork = process_action("Metalworking");
			break;
		case "twosmallgirders":
			output("It takes quite a while to saw through such a heavy girder, but that's just what you do.  Eventually you end up with two small girders.");
			$playerscrap['data']['rareitems'][8] += 2;
			$playerscrap['data']['rareitems'][2] -= 1;
			$metalwork = process_action("Metalworking");
			break;
		case "largegirderfromtwosmall":
			output("You don your welding mask and get to work.  Before too long, the two small girders have become one large one.");
			$playerscrap['data']['rareitems'][2] += 1;
			$playerscrap['data']['rareitems'][8] -= 2;
			$metalwork = process_action("Metalworking");
			break;
		case "chassis":
			output("You grind and weld and swear for a little while, and when you're done, you have a pretty solid-looking chassis.");
			$playerscrap['data']['rareitems'][2] -= 1;
			$playerscrap['data']['rareitems'][8] -= 2;
			$playerscrap['data']['veryrareitems'][1] += 1;
			$metalwork = process_action("Metalworking");
			break;
		case "servolimb":
			output("You grind and buff and strip and cut and weld and solder for a good long while, and by the time you're done, you're holding a fully-functional Servo Limb with some nasty-looking claws.");
			$playerscrap['data']['rareitems'][1] -= 1;
			$playerscrap['data']['rareitems'][8] -= 1;
			$playerscrap['data']['rareitems'][11] -= 1;
			$playerscrap['data']['veryrareitems'][2] += 1;
			$solder = process_action("Soldering");
			$metalwork = process_action("Metalworking");
			break;
		case "cameraeye":
			output("You grind and buff and solder away until eventually you have a fully-functional webcam-like device for your ScrapBot.");
			$playerscrap['data']['rareitems'][9] -= 1;
			$playerscrap['data']['rareitems'][10] -= 1;
			$playerscrap['data']['veryrareitems'][3] += 1;
			$solder = process_action("Soldering");
			$metalwork = process_action("Metalworking");
			break;
	}
}
output("`n`n");

if ($solder['lvlinfo']['levelledup']==true){
	output("`n`c`b`0You gained a level in Soldering!  You are now level %s!  This action will cost fewer Stamina points now, so you can solder up more circuitry and wiring harnesses each day!`b`c`n",$solder['lvlinfo']['newlvl']);
}
if ($program['lvlinfo']['levelledup']==true){
	output("`n`c`b`0You gained a level in Programming!  You are now level %s!  This action will cost fewer Stamina points now, so you can geek out even more every day!`b`c`n",$program['lvlinfo']['newlvl']);
}
if ($metalwork['lvlinfo']['levelledup']==true){
	output("`n`c`b`0You gained a level in Metalworking!  You are now level %s!  This action will cost fewer Stamina points now, so you can grind and cut and weld more each day!`b`c`n",$metalwork['lvlinfo']['newlvl']);
}

set_module_pref("scrap", serialize($playerscrap), "scrapbots");

?>