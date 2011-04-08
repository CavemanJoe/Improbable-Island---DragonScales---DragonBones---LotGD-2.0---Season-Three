<?php

require_once("lib/villagenav.php");
require_once("lib/http.php");

function newbfaq_getmoduleinfo(){
	$info = array(
		"name"=>"Newbie FAQ (Rules Reminder that was)",
		"version"=>"2011-01-31",
		"author"=>"Dan Hall, revised by Sylvia Li with input from other mods",
		"category"=>"Improbable",
		"download"=>"",
		"prefs"=>array(
			"Rules Reminder User Preferences,title",
			"seenrules"=>"Has the user read the rules?,bool|0",
			"reminduser"=>"Does this user need a reminder?,bool|0",
			"modnotice"=>"Message shown by a moderator on the introduction page,text",
		),
	);
	return $info;
}
function newbfaq_install(){
	module_addhook("village");
	module_addhook("biostat");
	module_addhook("commentary_talkform");
	return true;
}
function newbfaq_uninstall(){
	return true;
}
function newbfaq_dohook($hookname,$args){
	global $session;
	switch($hookname){
	case "commentary_talkform":
		if (!get_module_pref("seenrules")){
			//debug($args);
			$args['blocktalkform'] = true;
			$args['message'] = "<a href='runmodule.php?module=newbfaq&op=start'>Click here to join in the chinwagging!</a>";
			addnav("","runmodule.php?module=newbfaq&op=start");
		}
	break;
	case "village":
		if (get_module_pref("reminduser")==1){
			addnav("","runmodule.php?module=newbfaq&op=rem");
			redirect("runmodule.php?module=newbfaq&op=rem");
		} else {
			addnav("Info");
			addnav("Frequently Asked Questions","runmodule.php?module=newbfaq&op=faq");
			//addnav("The Rules","runmodule.php?module=newbfaq&op=rules");
			blocknav("petition.php?op=faq");
		}
		break;
	case "biostat":
		$id = $args['acctid'];
		$ret = rawurlencode($args['return_link']);
		if ($session['user']['superuser'] & SU_EDIT_COMMENTS){
			addnav("Superuser");
			addnav("Send rules reminder","runmodule.php?module=newbfaq&op=setrem&id=$id&ret=$ret");
		}
//		break;
	}
	return $args;
}
function newbfaq_start(){
	// $reg = strtotime($session['user']['regdate']);
	
	// if ($reg < 1297377579){
		// output("`4`bYou're not a rookie, are you?`b  Don't worry, we're not reminding you of the rules because you've done something wrong - this is our new FAQ module, and you're seeing it for largely technical reasons.  But hell, since you're here anyway, the rules have changed a little bit, so you might want to refamiliarize yourself with them.  Following is the text that a brand-new player would see upon first trying to chat.`0`n`n");
	// }
	output("So, you want to start chatting, eh?  I can't blame you!  First, though, here's the short list of what you need to know to start playing.`n`n");
	newbfaq_rules();
	output("`bRemember to check the MotD`b`nIf the MotD (Message of the Day) link up at the top of the page is coloured or blinking, it means that there's Something Very Important going on, and you should totally click that link to find out what that Very Important Something might be.`n`n`bFrequently-Asked Questions`b`nWe've got a list of the questions most frequently-asked by new players, which you can see via the link to your left.  Also, Corporal Punishment over in Basic Training in NewHome can always give you a quick refresher course, if you can stomach his SHOUTING.  Also also, the link up at the top of the page, for the Improbable Island Enquirer, will lead you to a sometimes-useful Wiki of Lies.`n`n");
	output("`bAll done!`b`nBy reading this sentence you have been deemed just about knowledgeable enough to get yourself into potentially-amusing trouble, and you can now chatter away to your heart's content!  Feel free to head back into the Outpost and say 'Hello!' to everyone.`n`n`n`n");
	output("`bOr,`b hit the links to the left for the rules in a little more detail, and the Frequently-Asked Questions.  You can return to this guide via the FAQ link in any Outpost.");
	set_module_pref("seenrules",true);
}

function newbfaq_remind(){
	output("A sad-faced clown steps in front of you. A little red name tag pinned to his chest says \"`\$My name is `bMISTER NIPLOFF`b.  How can I help?`0\" Uh-oh, it's `ithat`i Mister Niploff, the one who used to introduce The Rules to everyone! Why is he here now? He reaches into his pocket and pulls out a large pair of scissors, which he prods gently against your chest.`n`n`bClearly he thinks you need a quick reminder about the rules!`b`n`n");
}

function newbfaq_rules(){
	output("`bThe Rules`b`nOn Improbable Island, there are just four short, sweet rules.  Here they are:`n`b1.  `4Don't be a dick.`0`b`n`b2.  `1Don't take it seriously.`0`b`n`b3.  `2No alts`0.`b`n`b4.  `6No kids`0.`b`nThat's it!  Dead simple, right?`n`n");
}

function newbfaq_rules_detail(){
	output("`bThe rules in detail`b`n");
	output("You probably don't need to read about the rules in any great detail, because we've tried to make them as self-explanatory as possible, but if you need a reminder or an example, the next sections might be useful as a reference.`n`n`b`\$Don't be a dick`0`b`n`4This one's easy.  The Island is meant for fun, for you and for everyone.  Enjoy yourself, and don't do things that will spoil other players' fun.`n`nSome things that aren't fun to roleplay: bullying, violent rages, cruelty, self-pity, depression, suicidal tendencies, mental illness, LOL-speak, name-calling, perpetual victimhood, prejudice, politics, poop jokes...  well, you get the idea.  We could go on like that all day, and I'm sure we don't have to tell you how to not be a dick.  Just use common sense and assume the best of people.  No, demonic possession will not be accepted as an excuse.`n`nAlso, `bbe nice in NewHome.`b  `iReally`i nice.  It's the first place new players see, so we want them to want to stay and explore!  Be welcoming, helpful, encouraging and as funny as you like!`0`n`n`b`!Don't take it seriously.`0`b`n`1While Rule 1 is meant to make the Island more fun for everyone, Rule 2 is designed to make it more fun for you in particular.  It doesn't mean your character can't have a serious story here; it means you have to remind yourself sometimes that you and your character are separate entities.  This is, after all, only a silly Internet game.`n`nIf you stay here long enough, the chances are that sooner or later you'll end up breaking Rule 2.  Don't worry about it too much - it happens.  Rule 1 trumps everything, and as long as you're not breaking Rule 1 as well, people will understand.`n`n`b`@No Alts`0`b`n`2An Alt is a secondary character made by the same player.  If these secondary characters are used as mules to shuffle resources around, then the game becomes a meaningless exercise in proving who has the most spare time and the least life outside the Island - and that's not a game anybody wants to win.  If the alts are used for social engineering - like running a clan with one character while applying to a rival clan with another, or using alts as a way to deceive people - then we have problems trusting each other, and Rule 1 comes into play.  Thankfully, the admins and moderators can always see which characters are alts.`n`nRule 3 is special in that if you can break Rule 3 without breaking any of the other rules, then it can be quite fun to break.  Rule 3 gets broken willy-nilly `iall the time`i by players using alts for comedic effect, without using alts for nefarious purposes.  Breaking Rule 3 will usually only earn you Frowny Admin Faces if you break Rule 1 at the same time.`0`n`n`b`^No Kids`0`b`n`6Remember when we asked you if you were over eighteen?  Well, we meant it.  If you were stretching the truth a little bit there, you might want to come back later on, when you really `iare`i over eighteen - 'cause if we find out, we'll have to ban you.`n`nIf you're an adult roleplaying as a minor, then take very careful note of two things; firstly, the in-game text will remind you occasionally that according to canon, there are no children on the Island.  This can be jarring!  Secondly, so much of a `ihint`i of an underage character having any sort of a sex life at all here will get you a permanent ban, without exception.  The Island is hosted in the UK, where the laws are extremely stringent; there's no wriggle room on this at all.`0`n`n`n`bModerators`b`nThe Island's moderators are experienced players who volunteer their time to keep the Island fun for everyone. Mods enforce Rule One, and, if need be, will help you remember Rule Two.  If you're having a problem (socially in the game, or with a game bug, or most anything really), they can help.`n`nThese are the currently active moderators:`n`n`bSessine`b`n%s`n`n`bZolotisty`b`n%s`n`n`bEpaphus`b`n%s`n`n`bEbenezer`b`n%s`n`n",get_module_pref("modnotice","newbfaq",6882),get_module_pref("modnotice","newbfaq",5528),get_module_pref("modnotice","newbfaq",4587),get_module_pref("modnotice","newbfaq",20408));
}

// function newbfaq_more(){
	// output("`bBadnavs`b`nBecause the world is not perfect, a character may sometimes get stuck in what's called a 'badnav' and be unable to go on playing. Don't panic! It's not anything you did. Submit a Petition, and the next mod that sees it will un-stick you.`n`n`bNames`b`nYou'll have noticed there are a lot of unusual character-names. Perhaps you too have an unusual name. While you're still new, it's the time to ask yourself: is this the name I want to use for myself and my roleplay character, `iforever`i?  It's much easier to start over now than it will be later on in the game.`n`n`bFrequently Asked`b`n");
	// rawoutput("<a href=\"runmodule.php?module=newbfaq&op=faq\">Here</a>");
	// output(" are the answers to a few questions most players have when starting out. If you'd rather plunge in and learn by exploring, the FAQ link will be there later if you want it, in the left column."); 
	// addnav("","runmodule.php?module=newbfaq&op=faq");
// }

function newbfaq_faq(){
	output("`bFrequently-Asked Questions for new players`b`n`n");
	output("`bHow do I get pants?`b`nYou can get armour in Sheila's Shack, which will give your character better defence in the jungles. In roleplay, it's up to you -- be creative!`n`n");
	//CMJ: edited to remove refs to Soup and Pants.  If it doesn't actually give clothing that the player doesn't have to roleplay, then it'll confuse non-roleplayers.

	output("`bHow do I emote / do colours?`b`nTo do actions instead of speaking, begin your comment with : or /me. After you've started a comment that way, you'll need to type your own quotation marks, like this:`n`n`&: smiles and says, \"Thanks, that's very kind of you.\"`0`n`^Your Character`& smiles and says, \"Thanks, that's very kind of you.\"`0`n`nTo change the text colour, type `` (the grave accent, right above the tab key on a full-size keyboard; on some laptops, it might be near to the spacebar, or somewhere else entirely.  It shares a key with tilde, the ~ key) followed by a number, letter, or special-character code. The bartender in the `bRaven Inn`b can explain this too, and will let you practice. Example:`n`n`&: smiles and takes a sip. \"``#Thanks, that's very kind of you.``&\" The steaming cocoa is delicious.`0`n`^Your Character`& smiles and takes a sip. \"`#Thanks, that's very kind of you.`&\" The steaming cocoa is delicious.`0`n`n");
	//CMJ: added more detail about the grave key

	output("`b`iRaven Inn`i? I don't see that in NewHome.`b`nYou'll run into the Raven Inn once in a while in the jungle. It features dice games, the lowdown on other players' stats, and a permanent floating bar brawl played for laughs. If you have the Budget Horse as a mount (Best deal ever! Only 2 cigs! at Mike's in NewHome!!) you can find it any time you like.`n`n");

	output("`bWhat is that wall for?`b`nThe wall keeps the outpost safe from monsters outside. NewHome is usually monster-free anyway, because so many people fight in the jungles there. So don't worry about the NewHome wall! Go ahead, spend your stamina in the jungles. If you're going to another outpost, you can check the threat levels in a Council Office before you set out.`n`n");

	output("`bAnything else to spend my stamina on, aside from fighting and repairing the wall?`b`nFighting in the Jungle and beating your masters in the Dojo is your first priority. Get to `i`bat least Level 2`b`i before you try anything else. When you're ready to go beyond that, then yes, there are a number of other ways to use your stamina. You can travel around the Island, you can learn to cook in Kittania, you can master combat insults in Pleasantville... you can even, one day, build a house!`n`n");
	
	output("`bWhat will I see if I travel?`b`nYou will find there's much to explore in the Island's other outposts. You'll also see places that other players have built -- huts, castles, haunted houses, lighthouses, train stations, seaside villages, canyons, quarries, and much, much more. Most of these are unlocked and open to the public. Take your time and savour them!`n`n");

	output("`bWhy is my character starving / overweight?`b`nYour character will need to eat to stay healthy. However, if you eat the wrong kinds of foods, you'll lose stamina. Check out the descriptions; if the text says the food has no nutrients, you'll be malnourished if that's all you eat. If everything you eat is fatty, well. You can guess. Balance your diet! Different outposts have different foods available; some have none.`n`n");

	output("`bHow do I get cigarettes?`b`nYou'll find those tasty, tasty smokesticks once in a while after jungle fights, during your world travels, and at the pinata in the Common Ground. Other Island mini-games sometimes yield cigs too, and try wandering around to check out all the exhibits in the NewHome museum. Mister Stern, the curator, will thank you with some cigarettes if you do him a favour.`n`n");

	output("`bWhen do I fight the Improbability Drive?`b`nWhen you reach level 15, you will be ready to search the Jungles of each outpost for the Improbability Drive.`n`n");

	output("`bHow do I change my race?`b`nAfter you beat the Improbability Drive for the first time, two more races will open up: Kittymorph and Zombie. Other races are unlocked as you progress through more Drive Kills, ending with Joker, after 12 DKs.`n`n");

	output("`bWhat are these playing cards for?`b`nLater on, they'll help you get rail passes -- there are trains that can take you from outpost to outpost. If you have enough cigarettes, you may meet a peddler in one of the train stations who will sell you a card-case.`n`n");

	output("`bBadnavs`b`nBecause the world is not perfect, a character may sometimes get stuck in what's called a 'badnav' and be unable to go on playing. Don't panic! It's not anything you did. Submit a Petition, and the next mod that sees it will un-stick you.`n`n`bNames`b`nYou'll have noticed there are a lot of unusual character-names. Perhaps you too have an unusual name. While you're still new, it's the time to ask yourself: is this the name I want to use for myself and my roleplay character, `iforever`i?  It's much easier to start over now than it will be later on in the game.`n`n");
	
	output("`bWhat if I have a question that's not answered here?`b`nAsk it in `bLocation Four`b, accessible from any outpost. Experienced players like to drop by there to help out, so someone is bound to give you an answer soon. Or, you could also ask in any outpost's `bBanter`b chat. There is also a wiki on the `bEnquirer`b, linked at the top of every page. It's called the ");
	rawoutput("<a href=\"http://enquirer.improbableisland.com/dokuwiki/doku.php\" target=\"_blank\">Wiki of Lies</a>");
	output(" for a reason, but the information is often useful anyway.`n`n(Oh, and if you can put up with the silly SHOUTING, Corporal Punishment in `bBasic Training`b will explain a lot of game details.)");
}

function newbfaq_run(){
	global $session;
	$op = httpget("op");
	switch($op){
		case "start":
			set_module_pref("seenrules",1);
			page_header("Getting Started");
			newbfaq_start();

			//newbfaq_more();
			addnav("Carry on");
			addnav("Get on with it","village.php");
			addnav("Wait a sec");
			addnav("Let me see that FAQ","runmodule.php?module=newbfaq&op=faq");
			addnav("Show me the rules in more detail","runmodule.php?module=newbfaq&op=rules");
		break;

		case "rules":
			page_header("The Rules");
			newbfaq_rules();
			newbfaq_rules_detail();
			addnav("Carry on");
			addnav("Get on with it","village.php");
			addnav("What else?");
			addnav("Show me the Frequently-Asked Questions","runmodule.php?module=newbfaq&op=faq");
			break;

		case "faq":
			page_header("Frequently Asked Questions");
			newbfaq_faq();
			addnav("Carry on");
			addnav("Get on with it","village.php");
			addnav("What else?");
			addnav("Show me the site rules","runmodule.php?module=newbfaq&op=rules");
		break;

		case "setrem":
			page_header("Reminder sent");
			$id = httpget('id');
			$ret = httpget('ret');
			$return = rawurlencode($ret);
			set_module_pref("reminduser",1);
			output("This user will receive a visit from Mister Niploff.");
			addnav("Back");
			addnav("To the bio","bio.php?char=$id&ret=$return");
		break;

		case "rem":
			set_module_pref("reminduser",0);
			page_header("The Rules");
			newbfaq_remind();
			newbfaq_rules();
			newbfaq_rules_detail();
			addnav("I understand, Mister Niploff. I promise to be good in future. Please don't steal my nipples.","village.php");
		break;
	}
	page_footer();
}
?>