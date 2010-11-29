<?php

$stakecost = 100;
$toolcost = 1;

page_header("Suzie's Land Registry and Tool Rental");
switch(httpget('sub')){
	case "start":
		output("You wander into a small hut that smells like fresh sawdust.  Behind the counter is a tall redheaded human woman - Suzie, the owner herself, who gives you a nod as you walk in.`n`n\"`6What'll it be today, then?`0\"`n`n");
		addnav("What?");
		addnav("What's all this about?","runmodule.php?module=improbablehousing&op=landregistry&sub=explain");
	break;
	case "buystake":
		output("You hand over your payment.  Suzie reaches behind the counter and brings out a large metal stake.`n`n\"`6Right, jus' find yer dream location an' stick that in the ground.  I'll sort out the paperwork an' let the council know yer' buildin'.`0\"`n`nYou put the stake in your backpack.");
		give_item('housing_stake');
		$session['user']['gems']-=$stakecost;
		debuglog("spent ".$stakecost." cigarettes on a housing stake.");
	break;
	case "buymasonry":
		output("You hand over your payment.  Suzie reaches beneath the counter and brings up a heavy-looking metal toolbox.`n`n\"`6Right, 'ere you go - 'ammers, chisels, trowels, fillers, spirit level, all sorts in there.  Little packets o' cement mix an' everythin'.`0\"`n`nYou thank Suzie, and stuff the heavy toolbox into your backpack.`n`n");
		give_item('toolbox_masonry');
		$session['user']['gems']-=$toolcost;
	break;
	case "buycarpentry":
		output("You hand over your payment.  Suzie reaches beneath the counter and brings up a heavy-looking metal toolbox.`n`n\"`6Right, 'ere you go - every type of 'and saw imaginable, planes, sandpaper, nails, drills, all sorts in there.  'ave fun with that!`0\"`n`nYou thank Suzie, and stuff the heavy toolbox into your backpack.`n`n");
		give_item('toolbox_carpentry');
		$session['user']['gems']-=$toolcost;
	break;
	case "buydecorating":
		output("You hand over your payment.  Suzie reaches beneath the counter and brings up a heavy-looking metal toolbox splattered with paint.`n`n\"`6Right, 'ere you go - all sorts o' paint, brushes, thinners, stud finder, nails, tacks, lots o' good stuff.  'ave fun with that!`0\"`n`nYou thank Suzie, and stuff the heavy toolbox into your backpack.`n`n");
		give_item('toolbox_decorating');
		$session['user']['gems']-=$toolcost;
	break;
	case "explain":
		output("Suzie stands up, hitching her toolbelt back up to her hips.  \"`6You wanna build a house, right?  Well, first you've gotta understand that it's not something you can really do on your own.  Ye'll have to get yer friends to help out.`0\"  She grins.  \"`6If ye've got any, o'course.  Next, you'll 'ave to sort out the tax, and stake yer claim.  It's a hundred cigs for a stake.  There can only be four houses in a square klick, so ye'd better get crackin' before all the good spots are taken.  Next, gather some wood and stone - we can lend you the tools for that, if you've got the money - bring it to yer plot o' land and start buildin'.  Or, like I say, bring it to yer plot, drop it on the floor an' kick back while your mates knock a house together for you.  Either way, you'll need carpentry an' masonry tools for that too.  All the costs are figured by the day, an' don't worry about bringing the tools back - there's a beacon inside the box that my lads'll find and bring it back for me at midnight.`0\"`n`nShe takes a sip of a very grimy cup of tea as she continues.  \"`6So it's probably not a good idea to go rentin' tools at eleven at night, although some folks do it anyway.  Lot of nutters around 'ere.  Anyway.  We pretty much just do three seperate kits for three different jobs - masonry, carpentry, an' decoratin'.  Decoratin' ones come wi' paint an' that too, and ye can use as much of it as yer like, all one price.  The masonry ones have the kit in 'em to get the rock in the first place, an' the carpentry toolboxes have gear to chop down trees too.`0\"`n`n\"`#What - ladders, band saws, axes, straps, all that stuff in one little toolbox?`0\" you say, suspicious.  \"`#It sounds a bit... well...`0\"`n`nSuzie nods.  \"`6Improbable, aye, I know.  What gets me tickled is that people'd rather stick these bloody heavy toolboxes in their backpacks than carry 'em around by the 'andles.  Well, there's nowt as queer as folk.  Now what'll it be?`0\"");
	break;
}
addnav("Services");
if ($session['user']['gems'] >= $stakecost && !has_item('housing_stake')){
	//Ask the player if they'd like to buy a stake
	addnav("Buy a land claim stake (100 cigarettes)","runmodule.php?module=improbablehousing&op=landregistry&sub=buystake");
} else if ($session['user']['gems']<$stakecost){
	addnav("You don't have enough cigarettes to buy a stake","");
} else {
	addnav("You already have a land claim stake.","");
}
if ($session['user']['gems'] >= $toolcost){
	if (!has_item('toolbox_masonry')){
		addnav("Rent a Masonry toolbox (1 cigarette)","runmodule.php?module=improbablehousing&op=landregistry&sub=buymasonry");
	} else {
		addnav("You already have a Masonry toolbox","");
	}
	if (!has_item('toolbox_carpentry')){
		addnav("Rent a Carpentry toolbox (1 cigarette)","runmodule.php?module=improbablehousing&op=landregistry&sub=buycarpentry");
	} else {
		addnav("You already have a Carpentry toolbox","");
	}
	if (!has_item('toolbox_decorating')){
		addnav("Rent a Decorating toolbox (1 cigarette)","runmodule.php?module=improbablehousing&op=landregistry&sub=buydecorating");
	} else {
		addnav("You already have a Decorating toolbox","");
	}
}


addnav("Return");
addnav("O?Back to the Outpost","village.php");
page_footer();

?>