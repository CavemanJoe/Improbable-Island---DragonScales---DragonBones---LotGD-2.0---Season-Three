<?php
function vendingmachine_getmoduleinfo(){
    $info = array(
        "name"=>"Vending Machine",
        "version"=>"2011-12-28 1.0.0",
        "author"=>"Cousjava",
        "category"=>"Village",
        "download"=>"https://github.com/Cousjava/Improbable-Island",
        "settings"=>array(         
		"location"=>"The ID of the village that it is in,int|1",
		"daysstationary"=>"Number of game days the machine stays in one place,int|42",
		"daysinplace"=>"Number of game days the machine has been in its current location,int|0",
                ),
        "prefs"=>array(
                        "playedtoday"=>"Times the player has used the vending machine today,int|0",
                ),
        ); 
   return $info;
}

function vendingmachine_install(){
        module_addhook("village");
	module_addhook("newday");
        return true;
}


function vendingmachine_uninstall(){
	return true;
}

function vendingmachine_dohook($hookname,$args){
    global $session;
    switch($hookname){
   	case "newday":
		$daysinplace=get_module_setting("daysinplace")+1;
		$daysstat=get_module_setting("daysstationary");
		if($daysinplace==$daysstat){//spent enough time in one place
			$query="COUNT(*) FROM ".db_prefix("cityprefs");
			$result=mysql_query($query);
			$cities=mysql_result($result,0);
			$newcityid=e_rand(1,$cities);			
			set_module_pref("location",$newcityid);
			set_module_pref("daysinplace",0);
		} else {
			set_module_pref("daysinplace",$daysinplace);
		}
		set_module_pref("playedtoday",0);
		break;
	case "village":
		$pcid = get_cityprefs_cityid("location",$session['user']['location']);
		$vcid = get_module_setting("location");
		if ($pcid==$vcid){
			addnav($args["tavernnav"]);
			addnav("Vending Machine","runmodule.php?module=vendingmachine");
		}
		break;
	}
    return $args;
}

function vendingmachine_run(){
	global $session;
	page_header("Vending Machine");
	$playedtoday = get_module_pref("playedtoday");
	switch (httpget('op')){
		default:
 
			output("`0In front of you stands an old, battered vending machine. To the right is a slot where you can put in some req. To the left you can see soem items. There are a few cigarettes in it, a slice of `5CAKE`0, a few other items and, incredibly, a bar of `4c`5h`qo`ec`Eo`tl`Ta`Yt`Pe`0. Will you a try?`n`n");
			//Now, let's see how to add in a variable in an output statement... Damn, I'm making this sound a lot more complicated than it actually is.  Just read it, it'll be quicker than me trying to explain it.  We use %s as placeholders for our variables.
			output("As far as you can recall, you've tried to get items out of here %s times today.`n`n",$playedtoday);
			//You remember if statements, right?  Of course you do.
			//They haven't played today, so let's give them some links to click.
			addnav("Put in some money","");
			addnav("Pay 10 req","runmodule.php?module=example&op=ten");
			addnav("Pay 50 req","runmodule.php?module=example&op=fifty");
			addnav("Pay 100 req","runmodule.php?module=example&op=hundred");
			addnav("Shake the machine to try to get something.","runmodule.php?module=example&op=shake");
			
		break;
		case "ten":
			addnav("Vending Machine","");
			if ($session['user']['gold']<10){
				output("You try to put 10 requisition tokens in, and then realise you don't have that many. Oops.");
				addnav("Try to shake something out of it anyway","runmodule.php?module=example&op=shake");
				addnav("Return to Outpost","village.php");break;
			}
			$session['user']['gold'] -= 10;
			//Only paying 10 req, this is going to be a low-risk option for people with not much money.
			$chance = e_rand(1,100);
			if ($chance <=1 && $playedtoday==1){//1% chance of a cig, but only on first attempt on the day
				
				output("`0You put 10 requisition tokens into the machine. You hear the them bounc down through the mechanism. After a moment, out pops a cigarette! `n`n");
				$session['user']['gems']++;
			} elseif ($chance <21) {//20% chance of getting your money back.
				output("You put 10 requisition tokens in the machine. They slide doen and drop back out of the refunds slot.`n`n");
				$session['user']['gold'] += 10;
				addnav("Return to Outpost","village.php");
				
			}elseif ($chance<41){//20% chance of  just losing your money
				output("You put 10 requisition tokens in the machine. Nothing happens. You stand there, looking foolish for a little while, but still nothing happens. What now?");
				addnav("Try to shake something out of it anyway","runmodule.php?module=example&op=shake");
				addnav("Return to Outpost","village.php");

			}elseif ($chance<60){//19% chance of 50 req
				output("You put 10 requisition tokens in the machine. They rattle down and pop back out of the refund slot. Looking a bit more closely at them you notice there seem to be than you put in.");
				$session['user']['gold'] += 50;
				addnav("Return to Outpost","village.php");

			}elseif ($chance<80){//20% chance of losing between 5 and 25 req
				$lostreq=e_rand(5,25);
				output("You put 10 requisition tokens in the machine. As you do so you feel a rustling in your pocket. You turn around to see a young midget scampering away, with an older one berating it. \"`3You fookin' plonker, yer not meant ter let them feel you!`0\"A check of your pockets shows that you've lost %s req.",$lostreq);
				$session['user']['gold'] -= $lostreq;
				addnav("Return to Outpost","village.php");

			}else{//20% chance of gettting a losing 10% health for a gain of stamina
				$losthitpoints=$session['user']['hitpoints']*0.1;
				
				$losthitpoints=20;
				
				output("You put 10 requisition tokens in the machine. Nothing seems to happen. Annoyed, you hit it hard, injuring your fist, causing you to lose %s hitpoints. You hear a rattling noise and out pops a blue potion. Do you drink it?",$losthitpoints);
				addnav("Yes","runmodule.php?module=vendingmachine&op=drinkblue");
				addnav("No","village.php");
			}

			increment_module_pref("playedtoday",1);
			//There isn't a decrement_module_pref function - if we wanted to take one away, we'd do the same as we've just done, but make the final argument -1 instead.
			//and that's the end of door number one!
		break;
		case "drinkblue":
			output("You drink the potion. You feel energised! You gain some stamina.");
			addstamina(2500);
			addnav("Return to the outpost","village.php");
		case "fifty":
			//door one was a bit boring.  A bit low-stakes.  A bit dull.  Let's play with the numbers and liven things up a bit.
			$chance = e_rand(1,100);
			if ($chance<=10){
				output("You open the door and a cigarette falls out, followed by two hundred and fifty Requisition tokens!  How very Improbable.`n`n");
				$session['user']['gems']++;
				$session['user']['gold'] += 250;
				//Yes, cigarettes were originally gems in Legend of the Green Dragon!
			} else {
				//Since getting a cigarette AND a couple of hundred Req is a pretty awesome prize, we're only giving it a ten per cent chance of appearing.  The other ninety times out of a hundred, the Cake or Death guy shows up and gives you some poisoned cake.  This code is lifted pretty well straight out of the code for Cake or Death.
				//The Cake or Death man doesn't exactly KO the player.  When you KO a player, you have to mess about with taking away all their navs and making sure they end up on the FailBoat's Daily News page, and it's a bit of a pain in the bum.  So I like to just poison the crap out of them instead! :D
				output("You open the door to find a green-eyed gentleman standing behind it.  He hands you a slice of cake, on a paper plate!  You thank him, and walk away merrily wolfing down your prize.`n`nYou feel `5Full Of Cake!`0`n`nMoments later, the slow-acting poison starts to take effect.  The world begins to melt in front of you.  Grey spots dance on the edges of your vision.  Behind you, a green-eyed monster offers you another slice of cake, laughing and pointing.`n`nYou curse your luck as the hallucinations begin to kick in.");
				//We're gonna do an example buff now.  I could type about this all night, but you can learn more about buffs at http://wiki.dragonprime.net/index.php?title=Buffs.
				apply_buff('failcake', array(
					"name"=>"`5Full Of FailCake`0",
					"rounds"=>-1,
					"regen"=>-10,
					"startmsg"=>"`5You are walking on pink icing.  The sky is made of jam.  Your eyes are two cherries.  That cake was awesome.`0`n",
					"roundmsg"=>"`5The poisoned cake saps your strength, and you lose ten hitpoints!`0`n",
					"schema"=>"module-cakeordeath"
				));
			}
			set_module_pref("playedtoday",1);
			increment_module_pref("playedtotal",1);
			
		break;
		case "hundred":
			
			require_once "modules/staminasystem/lib/lib.php";
			
			$chance = e_rand(1,100);
			
			if ($chance<=90){
				output("You open the door and a soft white light envelopes you.  You feel mildly energized.  You gain some Stamina!`n`n");
				addstamina(5000);
				
			} else {
				
				output("You open the door and a huge round boulder comes rolling out!  You lose a whole load of Stamina running away from it, until you gain the common sense to just step out of its way!`n`n");
				removestamina(50000);
				
			}
			
			set_module_pref("playedtoday",1);
			
			increment_module_pref("playedtotal",1);
			
		break;
		case "shake":
			
			require_once "modules/staminasystem/lib/lib.php";
			
			$chance = e_rand(1,100);
			
			if ($chance<=90){
				output("You open the door and a soft white light envelopes you.  You feel mildly energized.  You gain some Stamina!`n`n");
				addstamina(5000);
				
			} else {
				
				output("You open the door and a huge round boulder comes rolling out!  You lose a whole load of Stamina running away from it, until you gain the common sense to just step out of its way!`n`n");
				removestamina(50000);
				
			}
			
			set_module_pref("playedtoday",1);
			
			increment_module_pref("playedtotal",1);
			
		break;
	}
	addnav("Go back where you came from","runmodule.php?module=labs");

	page_footer();

}

?>
