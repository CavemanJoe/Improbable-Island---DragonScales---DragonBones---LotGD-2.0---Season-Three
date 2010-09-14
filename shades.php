<?php
// translator ready
// addnews ready
// mail ready
require_once("common.php");
require_once("lib/commentary.php");


tlschema("shades");

page_header("The FailBoat");
addcommentary();
checkday();

if ($session['user']['alive']) redirect("village.php");

output("`0The FailBoat bobs a couple of miles offshore.  In the distance, Improbable Island stares back at you.  It seems to mock you, as much as an island can.`n`nYou wander around the upper deck, wearing a simple set of grey overalls marked with reddish-brown stains, your regular armour confiscated along with your weapon, backpack, bandolier and just about everything else, pride included.`n`nA pair of heavy steel doors lead to the lower decks, which contain the Retraining Pits and... `\$Her.`0  A few contestants are clustered together around the doors, talking in the fast-and-slow-and-fast-again tones of people running entirely on adrenaline.  They're bandaged, and traumatized, and uncertain.  Like you.`n`nThey cast occasional worried glances at the doors to the Retraining Pits, knowing that their best chances at rejoining their friends lie inside - but they're unsure of themselves.  After all, they've already been beaten up once today.`n`n");

output("`0Once every minute, a loudspeaker blares into life.  A certain `\$familiar`0 recorded voice says \"`&The time is now %s.  One more minute has passed since you arrived here; one more minute in which someone else is claiming your glory.  You will be returned to the Island at midnight, or after you have undergone Retraining to my satisfaction, whichever occurs first.  This message will repeat until you bloody well get off my Retraining Vessel.`0\"`n`n",getgametime());
modulehook("shades", array());
commentdisplay("`n`0Nearby, some fellow failies grumble to themselves and, occasionally, each other:`n", "shade","Chat with other Failed contestants",25,"mutters");

addnav("Log out","login.php?op=logout");
addnav("Places");
addnav("Head Below Decks","graveyard.php");

addnav("Info");
addnav("Return to the news","news.php");
addnav("Preferences","prefs.php");

tlschema("nav");

// the mute module blocks players from speaking until they
// read the FAQs, and if they first try to speak when dead
// there is no way for them to unmute themselves without this link.
// addnav("Other");
// addnav("??F.A.Q. (Frequently Asked Questions)", "petition.php?op=faq",false,true);

if ($session['user']['superuser'] & SU_EDIT_COMMENTS){
	addnav("Superuser");
	addnav(",?Comment Moderation","moderate.php");
}
if ($session['user']['superuser']&~SU_DOESNT_GIVE_GROTTO){
	addnav("Superuser");
  addnav("X?Superuser Grotto","superuser.php");
}
if ($session['user']['superuser'] & SU_INFINITE_DAYS){
	addnav("Superuser");
  addnav("/?New Day","newday.php");
}

tlschema();

page_footer();
?>