<?php
function improbablebandit_getmoduleinfo(){
	$info = array(
		"name"=>"Improbable Bandit",
		"version"=>"0.1",
		"author"=>"Dan Hall",
		"category"=>"Inn",
		"download"=>"",
		"settings"=>array(
			"reelrange"=>"Range of the Reels,int|1100",
			"spiderkittychance"=>"SpiderKitty range,int|300",
			"chainsawchance"=>"Chainsaw range,int|550",
			"improbabilitydrivechance"=>"Improbability Drive range,int|750",
			"doktorimprobablechance"=>"Doktor Improbable range,int|900",
			"kittybikechance"=>"KittyBike range,int|1000",
			"diseasedlungchance"=>"Diseased Lung range,int|1050",
			"spiderkittysmallprize"=>"Prize for one SpiderKitty,int|1",
			"spiderkittymedprize"=>"Prize for two SpiderKitties,int|2",
			"spiderkittylargeprize"=>"Prize for three SpiderKitties,int|4",
			"chainsawprize"=>"Prize for three Chainsaws,int|6",
			"improbabilitydriveprize"=>"Prize for three Improbability Drives,int|10",
			"doktorimprobableprize"=>"Prize for three Doktor Improbables,int|25",
			"kittybikeprize"=>"Prize for three KittyBikes,int|50",
			"diseasedlungprize"=>"Prize for three Diseased Lungs,int|100",
			"desiredpayout"=>"Desired Payout Percentage,int|90",
			"actualpayout"=>"Actual Payout so far,int|0",
			"timesplayed"=>"Times the machine has been played,int|0",
			"moneywon"=>"Total money won,int|0",
		),
		"prefs"=>array(
			"reel1symbol"=>"Reel one stops here,text|`b`4SpiderKitty`0`b",
			"reel2symbol"=>"Reel one stops here,text|`b`4SpiderKitty`0`b",
			"reel3symbol"=>"Reel one stops here,text|`b`4SpiderKitty`0`b",
		),
	);
	return $info;
}

function improbablebandit_install(){
	module_addhook("inn");
	return true;
}

function improbablebandit_uninstall(){
	return true;
}

function improbablebandit_dohook($hookname,$args){
	switch($hookname){
		case "inn":
			addnav(array("One-armed Bandit"),"runmodule.php?module=improbablebandit&op=examine");
			break;
	}
	return $args;
}

function improbablebandit_run(){
	global $session;
	page_header("Dan's Bandit");
	switch (httpget("op")){
		case "examine":		
			output("Dan sees you heading over to the one-armed bandit sat atop the counter, and grins.`n`nThe guide mounted on the front of the ancient-looking mechanical slot machine explains that it accepts and pays only in cigarettes, via an elaborate and gentle validation mechanism designed by Dan himself.  You can't help but admire its craftmanship.`n`nYou look at the hopper on the top of the machine, set up to allow you to fill it with cigarettes and just keep on pulling the handle until you're completely broke.`n`nSurely just one play couldn't hurt?`n`nThe payout structure is mounted on the front of the machine:`n`nSpiderKitty on first reel: 1 cigarette`nSpiderKitties on first and second reels: 2 cigarettes`nThree SpiderKitties: 4 cigarettes`nThree Chainsaws: 6 cigarettes`nThree Improbability Drives: 10 cigarettes`nThree Doktor Improbables: 20 cigarettes`nThree KittyBikes: 50 cigarettes`nThree Diseased Lungs: 100 Cigarettes");
			addnav("Play! (1 cigarette)","runmodule.php?module=improbablebandit&op=play");
			addnav("Sod that, it's a mug's game.","inn.php");
			break;
		case "play":
			//put in the navs for repeat play or walking away
			addnav("Again","runmodule.php?module=improbablebandit&op=play");
			addnav("Enough","inn.php");
			//Check the player actually has a ciggy
			if ($session['user']['gems']==0){
				output("Looking through your pockets, you realise that you don't actually have a cigarette to put in the machine.");
				break;
			}
			if ($session['user']['gems']>=1){
				//Drop a ciggy in the machine, increment the counter of times played
				output("You drop a cigarette into the hopper on the top of the machine.  As you pull the lever, the cigarette is weighed, measured, deemed genuine and swallowed into the depths of the machine.`n`nThe reels spin into a blur, and stop one by one with a satisfying CLUNK.`n`n");
				set_module_setting("timesplayed",get_module_setting("timesplayed")+1);
				$session['user']['gems']--;
				//Spin the first reel and decide what gets shown
				$reel1random=e_rand(1,get_module_setting("reelrange"));
				if($reel1random<get_module_setting("spiderkittychance")){
					set_module_pref("reel1symbol","`b`4SpiderKitty`0`b");
				}
				if($reel1random>=get_module_setting("chainsawchance") && $reel1random<get_module_setting("improbabilitydrivechance")){
					set_module_pref("reel1symbol","`b`%Chainsaw`0`b");
				}
				if($reel1random>=get_module_setting("improbabilitydrivechance") && $reel1random<get_module_setting("doktorimprobablechance")){
					set_module_pref("reel1symbol","`b`^Improbability Drive`0`b");
				}
				if($reel1random>=get_module_setting("doktorimprobablechance") && $reel1random<get_module_setting("kittybikechance")){
					set_module_pref("reel1symbol","`b`&Doktor Improbable`0`b");
				}
				if($reel1random>=get_module_setting("kittybikechance") && $reel1random<get_module_setting("diseasedlungchance")){
					set_module_pref("reel1symbol","`bKittyBike`b");
				}
				if($reel1random>=get_module_setting("diseasedlungchance")){
					set_module_pref("reel1symbol","`b`3Diseased Lung`0`b");
				}
				//Spin the second reel
				$reel2random=e_rand(1,get_module_setting("reelrange"));
				if($reel2random<get_module_setting("spiderkittychance")){
					set_module_pref("reel2symbol","`b`4SpiderKitty`0`b");
				}
				if($reel2random>=get_module_setting("chainsawchance") && $reel2random<get_module_setting("improbabilitydrivechance")){
					set_module_pref("reel2symbol","`b`%Chainsaw`0`b");
				}
				if($reel2random>=get_module_setting("improbabilitydrivechance") && $reel2random<get_module_setting("doktorimprobablechance")){
					set_module_pref("reel2symbol","`b`^Improbability Drive`0`b");
				}
				if($reel2random>=get_module_setting("doktorimprobablechance") && $reel2random<get_module_setting("kittybikechance")){
					set_module_pref("reel2symbol","`b`&Doktor Improbable`0`b");
				}
				if($reel2random>=get_module_setting("kittybikechance")  && $reel2random<get_module_setting("diseasedlungchance")){
					set_module_pref("reel2symbol","`bKittyBike`b");
				}
				if($reel2random>=get_module_setting("diseasedlungchance")){
					set_module_pref("reel2symbol","`b`3Diseased Lung`0`b");
				}
				//Spin the third reel
				$reel3random=e_rand(1,get_module_setting("reelrange"));
				if($reel3random<get_module_setting("spiderkittychance")){
					set_module_pref("reel3symbol","`b`4SpiderKitty`0`b");
				}
				if($reel3random>=get_module_setting("chainsawchance") && $reel3random<get_module_setting("improbabilitydrivechance")){
					set_module_pref("reel3symbol","`b`%Chainsaw`0`b");
				}
				if($reel3random>=get_module_setting("improbabilitydrivechance") && $reel3random<get_module_setting("doktorimprobablechance")){
					set_module_pref("reel3symbol","`b`^Improbability Drive`0`b");
				}
				if($reel3random>=get_module_setting("doktorimprobablechance") && $reel3random<get_module_setting("kittybikechance")){
					set_module_pref("reel3symbol","`b`&Doktor Improbable`0`b");
				}
				if($reel3random>=get_module_setting("kittybikechance")  && $reel3random<get_module_setting("diseasedlungchance")){
					set_module_pref("reel3symbol","`bKittyBike`b");
				}
				if($reel3random>=get_module_setting("diseasedlungchance")){
					set_module_pref("reel3symbol","`b`3Diseased Lung`0`b");
				}
				//Adjust output according to payout percentage
				//recalculate actual payout percentage
				set_module_setting("actualpayout",(get_module_setting("moneywon")/get_module_setting("timesplayed"))*100);
				//set to a good mood
				if(get_module_setting("desiredpayout")>get_module_setting("actualpayout")+5){
					set_module_pref("reel3symbol",get_module_pref("reel2symbol"));
				}
				//set to a bad mood
				if(get_module_setting("desiredpayout")<get_module_setting("actualpayout")-5){
					set_module_pref("reel1symbol","`b`3Diseased Lung`0`b");
				}
				//Output the reels
				output("The first reel stops on the %s symbol.`n",get_module_pref("reel1symbol"));
				output("The second reel stops on the %s symbol.`n",get_module_pref("reel2symbol"));
				output("The third reel stops on the %s symbol.`n`n",get_module_pref("reel3symbol"));
				//Award a prize			
				if(get_module_pref("reel1symbol")=="`b`4SpiderKitty`0`b" && get_module_pref("reel2symbol")!="`b`4SpiderKitty`0`b") {
					output("A cigarette drops into the payout tray!`n");
					$session['user']['gems']+=get_module_setting("spiderkittysmallprize");
					set_module_setting("moneywon",get_module_setting("moneywon")+get_module_setting("spiderkittysmallprize"));
				}
				if(get_module_pref("reel1symbol")=="`b`4SpiderKitty`0`b" && get_module_pref("reel2symbol")=="`b`4SpiderKitty`0`b" && get_module_pref("reel3symbol")!="`b`4SpiderKitty`0`b") {
					output("%s cigarettes tumble into the payout tray!`n",get_module_setting("spiderkittymedprize"));
					$session['user']['gems']+=get_module_setting("spiderkittymedprize");
					set_module_setting("moneywon",get_module_setting("moneywon")+get_module_setting("spiderkittymedprize"));
				}
				if(get_module_pref("reel1symbol")=="`b`4SpiderKitty`0`b" && get_module_pref("reel2symbol")=="`b`4SpiderKitty`0`b" && get_module_pref("reel3symbol")=="`b`4SpiderKitty`0`b") {
					output("%s cigarettes tumble into the payout tray!`n",get_module_setting("spiderkittylargeprize"));
					$session['user']['gems']+=get_module_setting("spiderkittylargeprize");
					set_module_setting("moneywon",get_module_setting("moneywon")+get_module_setting("spiderkittylargeprize"));
				}
				if(get_module_pref("reel1symbol")=="`b`%Chainsaw`0`b" && get_module_pref("reel2symbol")=="`b`%Chainsaw`0`b" && get_module_pref("reel3symbol")=="`b`%Chainsaw`0`b") {
					output("%s cigarettes tumble into the payout tray!`n",get_module_setting("chainsawprize"));
					$session['user']['gems']+=get_module_setting("chainsawprize");
					set_module_setting("moneywon",get_module_setting("moneywon")+get_module_setting("chainsawprize"));
				}
				if(get_module_pref("reel1symbol")=="`b`^Improbability Drive`0`b" && get_module_pref("reel2symbol")=="`b`^Improbability Drive`0`b" && get_module_pref("reel3symbol")=="`b`^Improbability Drive`0`b") {
					output("%s cigarettes tumble into the payout tray!`nDan looks on, shaking his head.`n",get_module_setting("improbabilitydriveprize"));
					$session['user']['gems']+=get_module_setting("improbabilitydriveprize");
					set_module_setting("moneywon",get_module_setting("moneywon")+get_module_setting("improbabilitydriveprize"));
				}
				if(get_module_pref("reel1symbol")=="`b`&Doktor Improbable`0`b" && get_module_pref("reel2symbol")=="`b`&Doktor Improbable`0`b" && get_module_pref("reel3symbol")=="`b`&Doktor Improbable`0`b") {
					output("%s cigarettes tumble into the payout tray!`nDan mutters under his breath, vigorously rubbing his bartop.`n",get_module_setting("doktorimprobableprize"));
					$session['user']['gems']+=get_module_setting("doktorimprobableprize");
					set_module_setting("moneywon",get_module_setting("moneywon")+get_module_setting("doktorimprobableprize"));
				}
				if(get_module_pref("reel1symbol")=="`bKittyBike`b" && get_module_pref("reel2symbol")=="`bKittyBike`b" && get_module_pref("reel3symbol")=="`bKittyBike`b") {
					output("%s cigarettes tumble into the payout tray!  Some overflow onto the floor, but you hurry to pick them up.`nYou straighten up to see Dan stood beside you, nonchalantly scratching his back with a baseball bat.  He flashes you a cheery grin before heading back behind the bar, grumbling.`n",get_module_setting("kittybikeprize"));
					$session['user']['gems']+=get_module_setting("kittybikeprize");
					set_module_setting("moneywon",get_module_setting("moneywon")+get_module_setting("kittybikeprize"));
				}
				if(get_module_pref("reel1symbol")=="`b`3Diseased Lung`0`b" && get_module_pref("reel2symbol")=="`b`3Diseased Lung`0`b" && get_module_pref("reel3symbol")=="`b`3Diseased Lung`0`b") {
					output("The machine lets out a terrible mechanical screeching sound, followed by an awful metallic moan.  All at once, the jackpot pours out into the payout tray, and spills onto the floor in an avalance of %s cigarettes!`nDan buries his face in his hands and weeps.`n",get_module_setting("diseasedlungprize"));
					$session['user']['gems']+=get_module_setting("diseasedlungprize");
					set_module_setting("moneywon",get_module_setting("moneywon")+get_module_setting("diseasedlungprize"));
				}
				output("`nYour stake cigarette drops deep inside the machine, never to be seen again.`n`nThe payout structure is mounted on the front of the machine:`n`nSpiderKitty on first reel: 1 cigarette`nSpiderKitties on first and second reels: 2 cigarettes`nThree SpiderKitties: 4 cigarettes`nThree Chainsaws: 6 cigarettes`nThree Improbability Drives: 10 cigarettes`nThree Doktor Improbables: 20 cigarettes`nThree KittyBikes: 50 cigarettes`nThree Diseased Lungs: 100 Cigarettes");
			}
				break;
	}

	page_footer();
}

?>