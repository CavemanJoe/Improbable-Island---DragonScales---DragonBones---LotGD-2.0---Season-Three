<?php
tlschema("faq");
popup_header("General Questions");
$c = translate_inline("Return to Contents");
rawoutput("<a href='petition.php?op=faq'>$c</a><hr>");
output("`n`n`c`bGeneral questions`b`c`n");
output("`^1. What is the purpose of this game?`n");
output("`@To get chicks.`n");
output("Seriously, though. The purpose is to slay the green dragon.`n`n");
output("`^2. How do I find the green dragon?`n");
output("`@You can't.`n");
output("Well, sort of.");
output("You can't find her until you've reached a certain level.");
output("When you're at that level, it will be immediately obvious.`n`n");
output("`^3. How do I increase my level?`n");
output("`@Send us money.`n");
output("No, don't send money - you increase your experience by fighting creatures in the forest.");
output("Once you've gotten enough experience, you can challenge your master in the village.`n`n");
output("Well, you can send us money if you want (see PayPal link).`n`n");
output("`^4. Why can't I beat my master?`n");
output("`@He's far too wily for the likes of you.`n");
output("Did you ask him if you have enough experience?`n");
output("Have you tried purchasing some armor or weapons in the village?`n`n");
output("`^5. I used up all my turns. How do I get more?`n");
output("`@Send money.`n");
output("No, put your wallet away.");
output("There *are* a few ways to get an extra turn or two, but by and large you just have to wait for tomorrow.");
output("When a new day comes you'll have more energy.`n");
output("Don't bother asking us what those few ways are - some things are fun to find on your own.`n`n");
output("`^6. When does a new day start?`n");
output("`@Right after the old one ends.`n`n");
output("`^7. Arghhh, you guys are killing me with your smart answers - can't you just give me a straight answer?`n");
output("`@Nope.`n");
output("Well, okay, new days correspond with the clock in the village (can also be viewed from other places).");
output("When the clock strikes midnight, expect a new day to begin.");
output("The number of times a clock in LotGD strikes midnight per calendar day may vary by server.");
output("Beta server has 4 play days per calendar day, main server at LotGD.net has 2.");
output("Other servers depend on the admin.`n");
output("This server has %s days per calendar day.`n`n", getsetting("daysperday", 2));
output("`^8. Something's gone wrong!!!  How do I let you know?`n");
output("`@Send money.");
output("Better yet, send a petition.");
output("A petition should not say 'this doesn't work' or 'I'm broken' or 'I can't log in' or 'yo.  Sup?'");
output("A petition *should* be very complete in describing *what* doesn't work.");
output("Please tell us what happened, what the error message is (copy and paste is your friend), when it occurred, and anything else that may be helpful.");
output("\"`3I'm broken`@\" is not helpful.");
output("\"`3There are salmon flying out of my monitor when I log in`@\" is much more descriptive as well as humorous, although there's not much we can do about it.");
output("In general, please be patient with these requests - many people play the game, and as long as the admin is swamped with 'yo - Sup?' petitions, it will take some time to sift through them.`n`n");
output("`^9. What if all I have to say is 'yo - sup?'?`n");
output("`@If you don't have something nice (or useful, or interesting, or creative that adds to the general revelry of the game) to say, don't say anything.`n");
output("But if you do want to converse with someone, send them an email through Ye Olde Post Office.`n`n");
output("`^10. How do I use emotes?`n");
output("`@Type :, ::, or /me before your text.`n`n");
output("`^11. What's an emote?`n");
output("`&Farmgirl AnObviousAnswer punches you in the gut.`n");
output("`@That's an emote.");
output("You can emote in the village if you want to do an action rather than simply speaking.`n`n");
output("`^12. How do you get colors in your name?`n");
output("`@Eat funny mushrooms.`n");
output("No, put that mushroom away, colors are given out by a site's admin for a variety of reasons -- for example it might signify that the character was integral to the beta-testing process - finding a bug, helping to create creatures, etc, or being married to the admin (*cough*Appleshiner*cough*).");
output("Check with your admins to find out how they grant colors.`n`n");
output("`^13. Sup dOOd, iz it cool 2 uz common IM wurds in the village? Cuz u no, it's faster. R u down wit that?`n");
output("`@NO, for the love of Pete, use full words and good grammar, PLEASE!");
output("These are not words: U, R, Ur, Cya, K, Kay, d00d, L8tr, sup, na and anything else like that!`n`n");
output("`^14. Does that Curious Looking Rock have a purpose?`n");
output("`@Of course it does! It confuses newbies!`n");
output("Seriously, a wise man once said, \"`3Good things come to those who wait.`@\"");
output("This should also be applied to the Rock.`n`n");
output("`^15. Wow, there are mounts AND familiars in the stables! Can I have one of each?`n");
output("`@(Oh for the love of... [we get this question a lot])`n");
output("No! Not! Nix! Nada! You can only have one creature at a time.");
output("Not two. Certainly not three. Four is right out. Five? You must be joking!");
output("Now all together now, HOW many companion creatures can you have at a time?`n`n");
output("`^16. Why not?`n");
output("`@Because we're big meanies. Actually, the game's code just doesn't allow for that right now.");
output("It might in the next version, then again it might not. Please stop asking!`n`n");
output("`^17. What's with the <CLAN> thingies before peoples' names?`n");
output("`3<`2CLAN`3> `&Clan Member ClanMember strikes you with the flat side of his weapon.`n");
output("`3\"`@You dare claim ignorance of my clan's famous deeds? I am `\$ClanMember`@, a member of the mighty clan CLAN, which is short for Completely Ludicrous And Nonsensical! We have performed many a deed after having planned it in the privacy of our `%Clan Hall`@.");
output("Our leaders and officers are among the mightiest in the land. Bolstered by the support of our friends and clanmates, we prevail!`3\"`n`n");
output("`^18. I am so confused! What is going on in the village square/Garden/Inn/etc.?`n");
output("`@A hubbub.`n");
output("The fact is that because there's so many people, there can be several conversations happening at once in any given area.");
output("Also, the thing to understand is that not all the comments are posted immediately, and that sometimes a person won't press the refresh or add button for several minutes, during which time more comments could have been said that the person missed while they were typing their own.");
output("Not to worry, though. Don't be shy, join in!`n`n");
rawoutput("<hr><a href='petition.php?op=faq'>$c</a>");
?>