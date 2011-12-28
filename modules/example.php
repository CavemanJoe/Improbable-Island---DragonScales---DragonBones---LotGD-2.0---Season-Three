<?php
//Example module for Improbable Island Player-Created Modules!
//Hello!  Admin CavemanJoe here, taking you on an adventure in coding!
//This is a simple module that will let the player head into a decrepit hut and choose one of three doors.  Each of the three doors has something good or bad behind it.  The player can only play once a day, and can win or lose some Requisition, win a large amount of Requisition and a cigarette, become poisoned, or gain or lose Stamina.
//In this module, we'll learn how to output text, add navigation links, generate a random number, use if statements, set a modulepref for information stored in the Improbable Island database pertaining to a particular player, hook into the Improbable Island code and alter things at specific points, affect the player's stats and apply buffs!  Probably a few other things too!  That's a lot of stuff to learn, so you'd better make a cup of tea!

//Let's get started by defining our first function.  Every function starts with the filename of the module (minus the .php bit).  So, the name of this function is example_getmoduleinfo, because the filename of this module is example.php.  This is the getmoduleinfo() function, which just tells the game what the module's all about.  Every module needs this!  See if you can understand the following code, and I'll show you what it does in the comments after.

function example_getmoduleinfo(){
    $info = array(
        "name"=>"Three-Door Shuffle",
        "version"=>"2009-04-19, 0.1",
        "author"=>"Dan Hall",
        "category"=>"Improbable Labs",
        "download"=>"",
        "prefs"=>array(
            "Three-Door Shuffle prefs,title",
			"playedtoday"=>"Has the player played Three-Door-Shuffle today?,bool|0",
			"playedtotal"=>"How many times has the player played Three-Door-Shuffle in total?,int|0"
        )
    );
    return $info;
}

//Okay, let's break that down.  $info is an array, which is passed back by the module when the game snoops at it (see the "return $info" bit at the end?  That's what that does, it returns the $info array to the rest of the game).  There are entries inside $info.  This is what they mean:
//name: The name of the module!
//version: In Improbable Island, we use the date as our version number, in the format yyyy-mm-dd, Japanese-style.  Not because it's cool and exotic - but so that when we're looking at a list of all our files sorted alphabetically by name, the most recent one is always at the top.  Sometimes, after the date, we'll put a comma and then an "official" version number which we'll increment if the module has changed considerably.  This is the first release of this module, so we'll call it 0.1 to denote its beta status.  Most Improbable Island modules stay in beta for approximately forever.
//author: Put your name and/or player name in here!
//category: This is just so that I can tell in my module manager which modules have been contributed by players.  For all the modules that you want to write, the category is always "Improbable Labs."  If they're suitable for DragonPrime, we can change this later on.
//download: We leave this blank for now.  If your module could, with minor tweaks, become a standard Legend of the Green Dragon module (for example, by changing the text - Requisition to Gold, Cigarettes to Gems and so on), then I'll submit it to DragonPrime.net for you (or you can do it yourself and save me the hassle) and they'll give it a download link so that other LotGD admins can enjoy it too!
//prefs:  User moduleprefs!  This information is unique to each player.  This will become clear later on.  Let's use inline comments here - my notes are in [square braces].

        // "prefs"=>array([I AM PREFS AND I AM AN ARRAY!  TREMBLE BEFORE ME, AND IF YOU DON'T UNDERSTAND WHAT AN ARRAY IS, GOOGLE FOR IT!]
            // "Three-Door Shuffle prefs,title",[Putting this here makes my User Editor look more neat and tidy]
			// "playedtoday"=>"Has the player played Three-Door-Shuffle today?,bool|0",[The interesting bit here is the "bool|0" bit.  That means that this pref entry is a boolean value - that is, it can only be true or false, with 1 meaning true and 0 meaning false.  It's a simple yes/no question!  The 0 is what it will be set to by default.  So, the first time the player encounters the module, they'll be starting with the "playedtoday" pref set to 0 - or, in other words, they haven't played today!]
			// "playedtotal"=>"How many times has the player played Three-Door-Shuffle in total?,int|0"[This one's different - this is an integer.  That means a number from zero to whatever!  We'll be using this purely for illustration purposes, to show what an integer is and to show how to change it and output the results.  Woo!]
        // )

//And that's the getmoduleinfo function!  What's next?  Oh, this!  The "install" function is where we define our hooks.  A hook is basically what happens when the game program goes through all its lines of code, and comes across a bit that says "oh, before you go on, have a look at these other files."  When we hook into a part of the game's code - or even into another module - we're basically making our module put its hand up and say "Me!  Me!  Look at me, here!"
//In more complicated modules we'll add Stamina actions, create database tables and all sorts of other things.  But for now, we just want to do some hooks.  So:

function example_install(){
	module_addhook("newday");
	module_addhook("labs-nh");
    return true;
}

//So now, when the player wakes up at the dawn of a new day, our module will do something!  It will also do something when the player is looking at the Labs player-created-modules area in NewHome!  Possible hooks are labs-*, where * denotes the name of the city (nh, np, ki, sq, pl, cc, ah, or ic).

function example_uninstall(){
    return true;
}

//We didn't create any databases or install any Stamina actions when we installed the module, so there's nothing to clean up here.  So we just do a return true; and carry on our merry way.  The game is clever enough to know to remove hook records automatically when a module is uninstalled.
//Now, here's where we actually _do something_ within those hooks!

function example_dohook($hookname,$args){
    global $session;
	//The $session variable is another array, and it's global.  We'll likely need this everywhere we go!
    switch($hookname){
	//We're looking at the $hookname variable, which is passed through for us when the game looks at this file to see what to do in a particular hook.
   	case "newday":
		//This is what to do at a new day!
		set_module_pref("playedtoday",0);
		//It's the start of a new day, so the player hasn't played today.  So, we'll set that modulepref (the first argument) to zero (the second argument).  It's pretty straightforward!
		break;
		//We're not talking about what to do at a new day any more!
	case "labs-nh":
		//Now we're talking about what to do in the Player-Created Modules section!
		//The player's in the right place for this module to run, so let's add a link to run our module.  The first argument is the text to show, the second argument is the link.
		addnav("Three-Door Shuffle","runmodule.php?module=example");
		//Now we'll break, so that the game knows we're not talking about the improbable_pcm hook anymore.
		break;
	//another curly bracket tells the game that we're not talking about hooks at all anymore!  This ends the switch($hookname) bit that we started earlier.
	}
    return $args;
}

//Now we get to the exciting part - we're going to actually run the module in a page all of its own!

function example_run() {
	//Yup, we're still gonna need that $session array!
    global $session;
	//If we don't declare a page header - IE a title for the page - then the page simply won't load.  At all.  The player will be left staring at a blank white screen and muttering "What muppet coded this?"  Never forget!
	page_header("Do the Three-Door Shuffle!");
	//We need to retrieve, from the database, the pref for whether or not the player's played today.  We'll need it soon.  Let's assign it to a variable - it'll save typing later on, and make the size of this file smaller.  While we're at it, let's get the other pref as well.
	$playedtoday = get_module_pref("playedtoday");
	$playedtotal = get_module_pref("playedtotal");

	//httpget lets us grab a part of the page's URL.  The first time the player encounters this module, they'll go to "improbableisland.com/runmodule.php?module=example[which is the filename of the module without the .php bit, remember]" - note the conspicuous absence of 'op' in there.  As they move through the pages, they'll go to "improbableisland.com/runmodule.php?module=example&op=[something]".  It's that [something] that we want to get!  We traditionally call the variable 'op' which is short for 'operation,' but you can call it whatever you like.  You can even get fancy and use multiple httpget requests for more complex modules with addresses like "improbableisland.com/runmodule.php?module=breaktheserver&action=breakitagain&weapon=axe&target=harddrive&force=allofitbabyyeah", but this module's just about the basics.
	//Oh, and you remember "switch", right?  You met earlier.
	switch (httpget('op')){
		default:
			//We're talking now about an instance where there is no 'op' in the page's URL.  This must mean that the player's on the first page of the module.  let's set the scene!
			//Our first output statement!  This is like echo, but it's actually a special function built into Legend of the Green Dragon which will put in our colour codes and make sure we're not doing anything silly.  
			output("`0Some old Joker geezer shows you three doors, and says that there's something nice or nasty behind each one.  Whatever, we'll put some proper flavour text in here later.  This module would `inever`i have gotten past CavemanJoe with flavour text this lame.  God, he's such an asshole.  Good thing he won't be reading this.`n`nAnyway, we're using tilde-n for new lines.  Just testin' that.  And when we want to output something in quotation marks, we have to escape it with a slash, like this:`n`n\"`3Come and look in my doors!`0\" shouts the old geezer.  Traditionally characters' colour codes will change only inside their quotation marks, descriptive text will usually be black, and the player's text will usually have colourcode #.  So, you've got three doors in front of you; now whatcha gonna do about it?  HUH?`n`n");
			//Now, let's see how to add in a variable in an output statement... Damn, I'm making this sound a lot more complicated than it actually is.  Just read it, it'll be quicker than me trying to explain it.  We use %s as placeholders for our variables.
			output("You've played a total of %s times.`n`n",$playedtotal);
			//If we want to do more than one variable in an output statement, we basically just use more %s's and more commas:
			output("I SAID, you've played a total of %s times.  That's %s times!`n`n",$playedtotal,$playedtotal);
			//You remember if statements, right?  Of course you do.
			if ($playedtoday == 0){
				//They haven't played today, so let's give them some links to click.
				addnav("Open the first door!","runmodule.php?module=example&op=door1");
				addnav("Open the second door!","runmodule.php?module=example&op=door2");
				addnav("Open the third door!","runmodule.php?module=example&op=door3");
			} else {
				//give them an unclickable link that goes nowhere!
				addnav("I'm not gonna do jack, pal.  I've already done this today.","");
			}
		break;
		case "door1":
			//The player went for the first door.  Now, let's give the first door a fifty-fifty chance of something nice versus something nasty.
			//Gimme a random number between one and a hundred, and we'll call it $chance:
			$chance = e_rand(1,100);
			//is the random number less than or equal to fifty?
			if ($chance <=50){
				//yes, the random number is less than or equal to fifty.  Output some flavour text!
				output("You've won some gold OH MY GOODNESS I MEAN REQUISITION.  Proper flavour text for that picky bastard Admin CavemanJoe.`n`n");
				//Give the player five gold pieces, because the game doesn't know that we call gold Requisition!  That'll help them in their quest to slay the dragon I MEAN THE IMPROBABILITY DRIVE
				$session['user']['gold'] += 5;
			} else {
				//$chance is NOT equal to or less than fifty.  In other words, it's 51 or more!
				output("A midget steals some Requisition or something.`n`n");
				//Oh dear - what happens if the player has LESS than 5 Requisition?  They'll get a negative number showing in their stats bar!  It'll get sorted out back to zero on their next page load, but in the meantime it just looks ugly!  So, we'll do another IF statement.  This one's fairly self-explanatory:
				if ($session['user']['gold']>=5){
					$session['user']['gold'] -= 5;
				} else {
					$session['user']['gold'] = 0;
				}
			}
			//What are we forgetting?  The player has played today, so we must update their pref so they can't play again!
			set_module_pref("playedtoday",1);
			//Now, we'll increase the number of times they've played by one.
			increment_module_pref("playedtotal",1);
			//There isn't a decrement_module_pref function - if we wanted to take one away, we'd do the same as we've just done, but make the final argument -1 instead.
			//and that's the end of door number one!
		break;
		case "door2":
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
			//What are we forgetting?  The player has played today, so we must update their pref so they can't play again!
			set_module_pref("playedtoday",1);
			//Now, we'll increase the number of times they've played by one.
			increment_module_pref("playedtotal",1);
			//There isn't a decrement_module_pref function - if we wanted to take one away, we'd do the same as we've just done, but make the final argument -1 instead.
			//We're done with Door Two!
		break;
		case "door3":
			//in Door 3 we'll be interacting with my Stamina system.  The Stamina system isn't part of Legend of the Green Dragon - in LotGD, and in Improbable Island prior to Season Two, we used Turns instead.  I've designed the Stamina system to be very easy to use!  Adding and removing Stamina is a piece of cake - just use the functions addstamina(x); or removestamina(x);, where x is the number of Stamina points to add or remove.
			//since the Stamina system is in itself a (rather large) module and not part of the core, whenever we want to use it we've got to tell the game to find its lib file so it knows what we're talking about when we use its functions.  So, let's do that:
			require_once "modules/staminasystem/lib/lib.php";
			//Now we can get on with it, knowing that our functions for interacting with Stamina are already loaded.
			$chance = e_rand(1,100);
			//We'll make this door rather a nice door.  Most of the time, anyway.
			if ($chance<=90){
				output("You open the door and a soft white light envelopes you.  You feel mildly energized.  You gain some Stamina!`n`n");
				addstamina(5000);
				//See how easy that was?
			} else {
				//But every now and then, this door will be an absolute bastard.
				output("You open the door and a huge round boulder comes rolling out!  You lose a whole load of Stamina running away from it, until you gain the common sense to just step out of its way!`n`n");
				removestamina(50000);
				//We don't even need to check if the player has 50,000 Stamina points to take away, because the Stamina system is clever and checks for us!
			}
			//What are we forgetting?  The player has played today, so we must update their pref so they can't play again!
			set_module_pref("playedtoday",1);
			//Now, we'll increase the number of times they've played by one.
			increment_module_pref("playedtotal",1);
			//There isn't a decrement_module_pref function - if we wanted to take one away, we'd do the same as we've just done, but make the final argument -1 instead.
			//And that's it for door number three!
		break;
	}
	
	//Now, we're at the end of our possible decisions and outcomes.  Because we're out of the switch, we can add in some things that we'll always need, in this module anyway.  We'll give the player a link to take them back to the player-created modules area, so that they can get out of this module, and we'll call page_footer so that all of this information is displayed to the player.
	
	addnav("Go back where you came from","runmodule.php?module=labs");
	
	//If we don't call page_footer, none of what we've just coded up will be shown to the player!  This is just as important as page_header!
	page_footer();
	
	//...and we're done!  I hope you've enjoyed reading along in this module and learning about programming for Improbable Island and Legend of the Green Dragon!
	//So, what can you do with what you already know?  Well, how about modifying this very file?  Here are some things for you to try:
	//1.  (easy) Add a fourth door.  That should be pretty easy!  But what to put behind it?  Maybe it can give different sorts of buffs?  The Buffs page in the DragonPedia will help you.  Use your imagination!
	//2.  (easyish) Right now, that counter is just a counter, nothing more.  Surely we could do something interesting with that!  Maybe, after playing the game more than x number of times, a fifth or even sixth door will become available?
	//3.  (harder) Maybe the number of times the game has been played can affect the player's luck?  How would you do that?
	//4.  (harder) By adding another pref in the moduleinfo function, we can make this module more complex and exciting.  A simple boolean pref could, for example, determine whether a player has an item or not - a key to the seventh door, for example...
	
	//Have fun!
}
?>
