<?php

function homepage_getmoduleinfo(){
	$info=array(
		"name"=>"Improbable Island Home Page",
		"version"=>"2009-07-08",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"Administrative",
		"download"=>"",
		"allowanonymous"=>"true",
	);
	return $info;
}

function homepage_install(){
	module_addhook("onlinecharlist");
	module_addhook("index");
	return true;
}

function homepage_uninstall(){
	return true;
}

function homepage_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "onlinecharlist":
			$args['handled']=1;
			$args['list'] = "";
			$nav = "";
			$op = httpget('op');
			if (httpget('r')){
				$referer = httpget('r');
			}
			
			//Output current game time
			$args['list'].=appoencode(sprintf(translate_inline("`0`bCurrent game time:`b`n%s`n`n"),getgametime()));
			
			//Output time to new day
			$secstonewday = secondstonextgameday();
			$args['list'].=appoencode(sprintf(translate_inline("`0`bNext Game Day in:`b`n`\$%s`0`n`n"),date("G\\".translate_inline("h","datetime").", i\\".translate_inline("m","datetime").", s\\".translate_inline("s","datetime"),$secstonewday)));
			
			//Output newest player
			if (getsetting("homenewestplayer", 1)) {
				$name = "";
				$newplayer = getsetting("newestplayer", "");
				if ($newplayer != 0) {
					$sql = "SELECT name FROM " . db_prefix("accounts") . " WHERE acctid='$newplayer'";
					$result = db_query_cached($sql, "newest", 120);
					$row = db_fetch_assoc($result);
					$name = $row['name'];
				} else {
					$name = $newplayer;
				}
				if ($name != "") {
					$args['list'].=appoencode(sprintf(translate_inline("`0`bNewest Player:`b`n`&%s`0`n`n"), $name));
				}
			}
						
			//Output online characters list
			$sql="SELECT name,alive,location,sex,level,laston,loggedin,lastip,uniqueid FROM " . db_prefix("accounts") . " WHERE locked=0 AND loggedin=1 AND laston>'".date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds"))."' ORDER BY level DESC";
			$result = db_query_cached($sql, "charlisthomepage", 300);
			$args['list'].=appoencode(sprintf(translate_inline("`bOnline Characters (%s players):`b`n"),db_num_rows($result)));
			while ($row = db_fetch_assoc($result)) {
				$args['list'].=appoencode("`^{$row['name']}`n");
				$onlinecount++;
			}
			db_free_result($result);
			if ($onlinecount==0)
				$args['list'].=appoencode(translate_inline("`iNone`i"));
			savesetting("OnlineCount",$onlinecount);
			savesetting("OnlineCountLast",strtotime("now"));
			updatedatacache("charlisthomepage",$args['list']);
			break;
		case "index":
			page_header("Welcome to Improbable Island");
			$r=httpget('r');
			
			//block all standard navs
			blocknav("create.php");
			blocknav("create.php?op=forgot");
			blocknav("news.php");
			blocknav("about.php");
			blocknav("about.php?op=setup");
			blocknav("list.php");
			blocknav("logdnet.php?op=list");
			
			//block navs with referral links, too
			blocknav("create.php?r=".$r);
			blocknav("create.php?op=forgot&r=".$r);
			blocknav("news.php?r=".$r);
			blocknav("about.php?r=".$r);
			blocknav("about.php?op=setup&r=".$r);
			blocknav("list.php?r=".$r);
			blocknav("logdnet.php?op=list&r=".$r);
			
			if (httpget('op')=="timeout"){
				$nav.= translate_inline("Your session has timed out, you must log in again.");
			}
			if (!isset($_COOKIE['lgi'])){
				$nav.=translate_inline("It appears that you may be blocking cookies from this site.  At least session cookies must be enabled in order to use this site.`n");
				$nav.=translate_inline("`b`#If you are not sure what cookies are, please <a href='http://en.wikipedia.org/wiki/WWW_browser_cookie'>read this article</a> about them, and how to enable them.`b`n");
			}
			rawoutput("<script language='JavaScript' src='lib/md5.js'></script>");
			rawoutput("<script language='JavaScript'>
			<!--
			function md5pass(){
				//encode passwords before submission to protect them even from network sniffing attacks.
				var passbox = document.getElementById('password');
				if (passbox.value.substring(0, 5) != '!md5!') {
					passbox.value = '!md5!' + hex_md5(passbox.value);
				}
			}
			//-->
			</script>");
			addnav("Return to the Island");
			$msg=$session['message'];
			$nav.="$msg<form action='login.php' method='POST' onSubmit=\"md5pass();\">Character Name:<br /><input name='name' id=\"name\" accesskey='u' size='12'><br />Password:<br /><input name='password' id=\"password\" accesskey='p' size='12' type='password'\"><br /><input type='submit' value='Log in' class='button'></form>";
			addnav(array("%s",$nav),"",true);
			addnav("Recover a lost password","create.php?op=forgot&or=1");
			addnav("Game Credits");
			addnav("Credits and License Info","runmodule.php?module=homepage&op=gamecredits&r=".$r);
			
			$skins = array();
		    $handle = @opendir("templates");
			// Template directory open failed
			if ($handle){
				addnav("Skin selection");
				while (false != ($file = @readdir($handle))) {
					if (strpos($file,".htm") > 0) {
						array_push($skins, $file);
					}
				}
				// No templates installed!
				if (count($skins) == 0) {
					output("None available");
					break;
				}
				natcasesort($skins); //sort them in natural order
				foreach($skins as $skin) {
					if ($skin == $_COOKIE['template']) {
						addnav(array("%s (selected)",htmlentities(substr($skin, 0, strpos($skin, ".htm")), ENT_COMPAT, getsetting("charset", "ISO-8859-1"))),"");
					} else {
						addnav(array("%s",htmlentities(substr($skin, 0, strpos($skin, ".htm")), ENT_COMPAT, getsetting("charset", "ISO-8859-1"))),"runmodule.php?module=homepage&op=changeskin&skin=$skin&r=$r");
					}
				}
			}
			//Output The Story...
			output("`i`#How did I end up here?`0`i`n`nIt's a question that you must have asked yourself at one point in your life or another. Probably after pulling your head out of the toilet bowl, remembering the little rhyme about drinking wine on top of beer, and then feeling rather poorly again. You might have asked that question while standing naked in the pouring rain holding a coathanger in one hand and a rather large purple sex toy in the other, trying desperately to break into your own car while two policemen stroll up the street towards you. Perhaps you asked it while flushing the contents of your pockets and incinerating your hard drive, or after putting your life savings on \"red 36,\" or while washing the urine out of your clown suit.`n`nHowever, this time, you mean it literally.`n`nOr rather, you would, if you had gotten around to asking that question. You haven't, yet. To be truthful, you're rather reluctant to open your eyes.`n`nYou know that you're lying in grass. You know that you're naked. You know that you can hear birds singing, and the sun on your body, and a warm, gentle breeze. That's okay. That's manageable. You've woken up in less desirable circumstances before.`n`nThe unmistakable roar of a low-flying jet plane going overhead, and the muffled thumps of explosions in the distance... now, that's not so good. That's the sort of thing that you really should pay attention to.`n`nReluctantly, you open your eyes.`n`n`i`#How did I end up here?`0`i`n`nYou're lying on your back in the middle of a grassy clearing. As suspected, a quick glance down confirms that you're as naked as the day you were born. A bulky video camera, mounted in a tree directly above you, pans disinterestedly down your body. It lets out a little whirring noise, one that seems to say \"yeah, whatever. I've seen better.\"`n`nAbout twenty paces ahead, the clearing gradually gives way into dense jungle. You look around you and confirm that this is the case for all directions.`n`nExcept one. A crudely-built wooden fort stands ten paces to your right. The wooden stakes in the ground extend about forty feet in the air, and you can't quite tell how far they go along horizontal dimensions.`n`n`i`#Think. Think. Where am I?`i`n`n`#`iOkay. I know who I am. I know my name. I know my parents' names. I know my address. I even know what I did last night! That rules out long-term memory loss, so what the hell am I doing here?`i`0`n`nYou try to get up, to see just how big this wooden fort thing is, but a sharp pain in your head tells you in no uncertain terms to sit the hell back down again.`n`nAs you collapse, the gates open, and a woman walks out. She looks around herself, and in short order her piercing blue eyes lock on to yours.  She walks over to you, shaking her head.`n`nShe wears a black skirt below her knees, a red turtleneck sweater, and round, copper-framed glasses. Blonde hair tied back in a bun, combined with her clear disdain for your presence, give her a rather severe appearance. When she comes closer, you notice that she's wearing heavy black steel-toe boots, spattered with something reddish-brown.`n`nShe leans over you, stares at you for a moment, and sighs. Her first words to you come in a Southern British accent:`n`n\"`&I don't really have time for this, you know.`0\"`n`nSomething about her voice doesn't sound quite right. It resonates in the center of your skull, almost the way that voices do when you hear them through a pair of headphones.`n`nShe sits down heavily on the grass beside you. Is that `irust`i on her boots?`n`n\"`&Long story short,`0\" she says, making herself comfortable. \"`&Because like I say, I really `idon't`i have time for this.`0\" She points to the wooden fort. \"`&That's an outpost.`0\" She points to the jungle. \"`&That's the Jungle.`0\" She points to herself. \"`&I am `\$The Watcher.`0\" She points down at you. \"`&You are a plonker. No, really, I mean it. You're clearly depriving a village somewhere of an idiot. Sorry to belabour the point, but it's important that we get the Watcher-Plonker relationship established properly, straight away. Otherwise you might get ideas.`0\"`n`nYou stare up at her accusing finger. \"`#What?`0\"`n`nShe presses gently on your nose. \"`&Case in point. Don't ask questions, and don't piss me off today, and you'll be fine.`0\"`n`nYou frown. \"`#What do you mean, I'll be fine?  I can't even remember how I got here!`0\"`n`n`\$The Watcher`0 smiles. \"`&Oh, they must have drugged you up something awful, poor thing.`0\" She looks down at her watch. \"`&Okay, I'll give you a run-down but I've gotta make this really, really quick, now. Don't interrupt.`0\"`n`nShe takes a deep breath. Then she talks very quickly.`n`n\"`&You've been drafted into a war against a machine called the Improbability Drive. It lives somewhere in the jungle, over there. Improbability is leaking out of this bloody thing like radiation, so we've got to blow it up. The whole war is being televised, you've noticed the cameras already, so try not to do anything stupid while the world watches. Your head hurts because the guys who burst into your living room with sticks and a great big sack probably hit you a bit too hard, and you might have landed badly when they tossed you out of the plane. You survived the fall without a parachute because of the Improbability Bubble surrounding the island, which makes the air notably denser about forty feet above sea level. You're naked and unarmed because everything that penetrates the Improbability Bubble gets changed in rather amusing ways, and we didn't want to take that risk. There's blood on my boots because I came across some monsters on the way over here - yes, monsters, stop gawping, you'll get used to them - and you'll either pick up the rest as you go along, or you'll die in a very entertaining fashion.`0\" She smiles. \"`&Either way, it'll make for great television.`0\"`n`nYou open your mouth to ask `\$The Watcher`0 what the hell she's blithering on about, but your words are drowned out by the roar of a passing jet plane and an accompanying female scream.`n`n`\$The Watcher`0 looks up, just in time to see a naked woman make a very undignified landing in a tree, folding herself neatly over a branch. `\$The Watcher`0 grins. \"`&I love it when they land with their arses poking out like that. It makes me feel better about my job.`0\" She stares for a moment, head cocked to the side. \"`&And my arse, too, come to think of it.`0\"`n`nShe takes hold of your hand and yanks you to your feet. \"`&Go through the gate over there. The bloke guarding it is trained to recognise naked newbies like you, and sort out the forms and the implants.\"`n`n\"`#Wait a minute, \"implants?\" What?`0\" you ask, even more scared now than you were two seconds ago.`n`n\"`&What did I tell you about questions? There are other people waiting. Come on, off you go.`0\" She takes you by the shoulders, swivels you towards the gate, and gives you a firm slap on your behind.  You see no other choice but to start walking.`n`n\"`&You there!`0\" calls `\$The Watcher`0 behind you. \"`&Yes, you, with the cellulite! You're in a tree because you've just been thrown out of an aeroplane! You've got to blow up an insane, reality-warping machine before we'll let you go home! You're naked because it's funnier that way! You desperately need a bikini wax, and you can buy weapons in that outpost over there! Stop crying, you're on television!`0\"`n`nYou shudder, and keep walking.`n`nWithin a few paces, you're at the gate of the Outpost. A man with a huge, bushy blonde beard sits in a little hut, writing on a newspaper with a pencil.`n`nYou clear your throat. \"`#Um, excuse me...`0\"`n`nThe man holds up a hand, still looking at his newspaper. \"`6Four-letter word, starts with N, the clue is \"Clad only in skin and innocence.\" Any ideas?`0\"`n`nYou shrug. \"`#Nude?`0\"`n`nThe man leans forward, cupping a hand to his ear. \"`6What was that? You'll have to speak up, they're buggers around here with their loud bloody grenades at every hour of the day and night.`0\"`n`n\"`#Nude,`0\" you respond, a little louder.`n`n\"`6Of course! Newb!`0\" He chuckles heartily, and writes in his paper. \"`6Enn, oh, oh, bee. Newb. Thanks for that. Now, what can I do for you?`0\"`n`n\"`#I honestly have no idea.`0\"`n`n\"`6Ah, so you're a newb yourself?`0\"`n`n\"`#Apparently. At least, according to the blonde woman over there.`0\"`n`nThe hairy man smiles, not unkindly. \"`6Well, let's fill you in on things.  First of all, what should I call you?`0\"`n`n");
			rawoutput("<form action='runmodule.php?module=homepage&op=0&r=$r' method='POST'\">\"<span class=\"colLtCyan\">You can call me <input name='name' id=\"name\" accesskey='u' size='12'>, I guess.</span>\"  It's as good a name as any.");
			rawoutput("<br /><div align=\"center\"><input type='submit' value='Carry On' class='button'></div><br /><br /></form>");
			addnav("","runmodule.php?module=homepage&op=0&r=".$r);
			break;
	}
	return $args;
}

function homepage_run(){
	require_once("common.php");
	require_once("lib/is_email.php");
	require_once("lib/checkban.php");
	require_once("lib/http.php");
	page_header("Welcome to Improbable Island");
	global $session;
	$r = (httpget('r'));
	$name = sanitize_name(getsetting("spaceinname", 0), httppost('name'));
	$pass1= httppost('pass1');
	$pass2= httppost('pass2');
	$sex = (httppost('gender'));
	$email = (httppost('email'));
	$passlen = (int)httppost("passlen");
	if (substr($pass1, 0, 5) != "!md5!" &&
			substr($pass1, 0, 6) != "!md52!") {
		$passlen = strlen($pass1);
	}
	if (!$sex){
		$outputgender = "man";
	} else {
		$outputgender = "woman";
	}
	switch (httpget('op')){
		case "changeskin":
			$skin = httpget('skin');
			if ($skin > "") {
				setcookie("template",$skin ,strtotime("+45 days"));
				$_COOKIE['template']=$skin;
			}
			redirect("index.php?r=$r","Home page module changing skin");
			break;
		case "gamecredits":
			page_header("Game Credits");
			output("Improbable Island's plot, characters, Stamina system, Item system, ScrapBots system, Races, Implants, Mounts and probably a few other things were created by Dan Hall, AKA Admin CavemanJoe.`nMany monsters were written by Improbable Island's players.`nImprobable Island runs on a modified version of Legend of the Green Dragon 1.1.1, DragonPrime Edition, which was created by the good folks at dragonprime.com.`nThe DragonPrime Edition was based on the original Legend of the Green Dragon, created by Eric Stevens and J.T. Traub as a homage to Legend of the Red Dragon by Seth Able.`n`nPut simply, Improbable Island is a hodgepodged Frankenstein of a game, but we get by.`n`nThe source code to Legend of the Green Dragon 1.1.1 +DP is available at dragonprime.net.`nThe source code to the most recent version of Legend of the Green Dragon written by Stevens and Traub (upon which the DragonPrime edition is based) is available at lotgd.net.`nThe source code of Improbable Island will be made available on Improbable Labs shortly - for now, it is available on request.`n`nFor more information about Improbable Island and Legend of the Green Dragon, check out the links to the left.");
			addnav("Further Reading");
			addnav("Module Credits","about.php?op=listmodules&r=".$r);
			addnav("Creative Commons License Info","about.php?op=license&r=".$r);
			addnav("Original Legend of the Green Dragon \"About\" page","about.php?r=".$r);
			addnav("Back to the Home Page");
			addnav("Back","home.php?r=".$r);
		break;
		case 0:
			$tripfilter = 0;
			$sname = strtolower($name);
			if (substr_count($sname,"nigger") || substr_count($sname,"cunt") || (substr_count($sname,"dick") && !substr_count($sname,"dicke")) || substr_count($sname,"faggot") || substr_count($sname,"vagina") || substr_count($sname,"pussy") || substr_count($sname,"shit") || substr_count($sname,"wank") || substr_count($sname,"bollocks") || substr_count($sname,"clitoris") || substr_count($sname,"fuck") || substr_count($sname,"dildo") || substr_count($sname,"tits") || substr_count($sname,"piss") || substr_count($sname,"penis")){
				output("The gatekeeper's biro stops mid-scrawl.  He looks up at you.  \"`6Seriously?`0\" he asks.  \"`6You're gonna walk into a pub, are you, and espy some sweet young thing who puts your heart all aflutter, and when they ask you your name, you're gonna say \"My name's %s, what's yours?\"  Is that right?`0\"`n`nYou shuffle your feet sheepishly.  Let's try this again.`n`n",$name);
				$redoform=1;
			}
			//todo: redirect when numbers appear in name
			$sql = "SELECT name FROM " . db_prefix("accounts") . " WHERE login='$name'";
			$result = db_query($sql);
			if (db_num_rows($result)>0){
				output("\"`6Oh, dear...`0\" says the gatekeeper.  \"`6See, we've already got a %s drafted in, here.`0\"`n`nYou stare at him for a moment.  \"`#What in the hell difference does `ithat`i make?!`0\"`n`n\"`6Well, you see, it's easier if everyone has different names, and, well, the computer that handles these forms...`0\" he shrugs.  \"`6It's not particularly bright.`0\"`n`nYou nod.  Computers haven't been terribly bright since the EMP wars.`n`n\"`6Just put down any old thing,`0\" says the gatekeeper.  \"`6I doubt it'll matter much.`0\"`n`n",$name);
				$redoform=1;
			} else if (strlen($name) < 3){
				output("\"`6Oh, dear...`0\" says the gatekeeper.  \"`6I'm afraid that name's just too short.`0\"`n`nYou stare at him for a moment.  \"`#What in the hell difference does `ithat`i make?!`0\"`n`n\"`6Well, you see, the computer that handles these forms...`0\" he shrugs.  \"`6It's not particularly bright.`0\"`n`nYou nod.  Computers haven't been terribly bright since the EMP wars.`n`n\"`6Just put down any old thing,`0\" says the gatekeeper.  \"`6I doubt it'll matter much.`0\"`n`n");
				$redoform=1;
			} else if (strlen($name) >25){
				output("\"`6Oh, dear...`0\" says the gatekeeper.  \"`6I'm afraid that name's just too long.`0\"`n`nYou stare at him for a moment.  \"`#What in the hell difference does `ithat`i make?!`0\"`n`n\"`6Well, you see, the computer that handles these forms...`0\" he shrugs.  \"`6It's not particularly bright.`0\"`n`nYou nod.  Computers haven't been terribly bright since the EMP wars.`n`n\"`6Just put down any old thing,`0\" says the gatekeeper.  \"`6I doubt it'll matter much.`0\"`n`n");
				$redoform=1;
			}
			if ($redoform) {
				rawoutput("<form action='runmodule.php?module=homepage&op=0&r=$r' method='POST'\">\"<span class=\"colLtCyan\">Well then, I suppose you can call me <input name='name' id=\"name\" accesskey='u' size='12'>.</span>\"  Like you're gonna give him your real name anyway.");
				rawoutput("<br /><div align=\"center\"><input type='submit' value='Carry On' class='button'></div><br /><br /></form>");
				addnav("","runmodule.php?module=homepage&op=0&r=".$r);
			} else {
				output("The gatekeeper smiles.  \"`6Okay, %s - now, if you don't mind my asking, are you over eighteen?`0\"`n`n",$name);
				output("You grimace.  \"`#Mate, I've been drafted into some sort of war.  If I'm under eighteen, `isomeone's`i in trouble.`0\"`n`n\"`6That as may be,\"`0 says the gatekeeper, \"`6I've got to ask anyway, before we go any further.  Just to make sure.`0\"`n`n`n");
				rawoutput("<table width=80% align=center style=\"border: 1px solid #990000;\"><tr><td class=\"trlight\">");
				output("`c`\$`bWARNING`b`c`7Although most folks would consider Improbable Island to be work-safe by virtue of it being entirely text-based, I should warn you that you probably wouldn't want to invite the vicar over for tea and scones while playing.`n`nImprobable Island contains `bfoetid Midget Brothels`b and other rather adult (but very silly) situations, and is intended for equally silly `badults`b.  If you're not an adult yet, why not check out Kingdom of Loathing, zOMG, Legends of Zork, Puzzle Pirates, Adventure Quest - there's loads of other browser games, really.`n`n");
				output("And if you `iare`i an adult, and want to carry on - knowing that you're about to be subjected to a barrage of sex, drugs and sausage rolls - by all means, continue with the story and have fun.`0");
				rawoutput("</td></tr></table>");
				output("`n`nYou realise you've been staring off into space for a moment, and you blink a couple of times.  \"`#Sorry, I think I spaced out for a minute there.`0\"");
				rawoutput("<form action='runmodule.php?module=homepage&op=1&r=$r' method='POST'\">");
				rawoutput("<input type='hidden' name='name' value=\"$name\">");
				rawoutput("<br /><div align=\"center\"><input type='submit' value='Yes, I am indeed over eighteen.  Now how about some pants?' class='button'></div></form>");
				addnav("","runmodule.php?module=homepage&op=1&r=".$r);
			}
		break;
		case 1:
				output("\"`6Yes, yes, we'll sort you out with some pants in just a minute, %s, don't you worry.`0\"  He reaches down out of sight, and pulls up a sheet of paper and a pen.  You see the words \"CONTESTANT REGISTRATION AND PANTS REQUEST FORM 3A\" printed at the top of the page.  Hell, maybe we're getting somewhere.`n`n\"`6Now, might I ask your gender?`0\"`n`nYou hesitate, dumbstruck.  \"`#I'm stood here `istark naked`i, right in front of you.`0\"`n`n\"`6And thanks to all those bloody Zap grenades going off all the time, all I see is a person-shaped blur who won't be getting any pants until they help me fill out this bloody form!`0\" replies the gatekeeper.`n`n",$name);
				rawoutput("<form action='runmodule.php?module=homepage&op=2&r=$r' method='POST'\"><input type='radio' name='gender' value='0' checked>\"<span class=\"colLtCyan\"><em>Male</em>, thank you very much.</span>\"<br /><input type='radio' name='gender' value='1'>\"<span class=\"colLtCyan\"><em>Female</em>, thank you very much.</span>\"");
				rawoutput("<input type='hidden' name='name' value=\"$name\">");
				rawoutput("<br /><div align=\"center\"><input type='submit' value='And is my voice really that androgynous?' class='button'></div><br /><br /></form>");
				addnav("","runmodule.php?module=homepage&op=2&r=".$r);
		break;
		case 2:
			output("\"`6No, %s, you `ido`i sound like a %s, but I like a little joke.`0\" the gatekeeper grins.  ",$name,$outputgender);
			output("\"`6Now, can I just take your E-mail address, please?`0\"`n`nMentally, you file the man under \"Nutters.\"  \"`#Hell, we only got `itelevision`i back about a year ago!  You know full well that computers don't work any more.`0\"`n`n\"`6And they haven't done since the EMP bombings years back, yes.  That's the interesting thing -`0\" he leans forward and lowers his voice.  \"`6I've heard tell that there `iare`i some working computers here.  Proper `isilicon`i ones, not these silly relay-and-vacuum-tube monstrosities we use today, where you think you're rich if you've got sixteen bytes of RAM to play with.  We're talking `iterabytes`i, here!  Just `iimagine`i what we could do with that sort of power!`0\"`n`nYou remember what you did with those resources when you had them, only a few years ago.  You played silly browser games on the Internet.`n`n\"`6Anyway - it's an old, old form, from back in the day when it mattered.  Let's take your E-mail address, so we can have something in the space.`0\"  He grins.  \"`6It's not like I'm going to send you any spam, now, is it?`0\"`n`n");
			rawoutput("<form action='runmodule.php?module=homepage&op=3&r=$r' method='POST'\">\"<span class=\"colLtCyan\">Fine - as far as I remember, it's <input name='email' id=\"email\" accesskey='e' size='25'>.</span>\"");
			rawoutput("<input type='hidden' name='name' value=\"$name\">");
			rawoutput("<input type='hidden' name='gender' value=\"$sex\">");
			rawoutput("<br /><div align=\"center\"><input type='submit' value='NOW may I have some pants?' class='button'></div></form>");
			rawoutput("<br /><br /><table width=80% align=center style=\"border: 1px solid #990000;\"><tr><td class=\"trlight\">");
			output("`7He ain't kidding - Improbable Island doesn't spam.  Your E-mail address is used to recover your password if you forget it, and (optionally) to let you know if someone sends you a message in-game - that's it.  We don't use it for anything else, and we never let anyone else see it.`n`nOr, you can continue without entering an E-mail address - but you won't be notified of anything, and you won't be able to recover your password if you lose it!`n");
			rawoutput("<form action='runmodule.php?module=homepage&op=3&override=1&r=$r' method='POST'\"><input name='email' type='hidden' value='not given'>");
			rawoutput("<input type='hidden' name='name' value=\"$name\">");
			rawoutput("<input type='hidden' name='gender' value=\"$sex\">");
			rawoutput("<br /><div align=\"center\"><input type='submit' value='Continue without an E-mail address' class='button'></div></form>");
			rawoutput("</td></tr></table>");
			addnav("","runmodule.php?module=homepage&op=3&override=1&r=".$r);
		break;
		case 3:
			if ($sexdisplay = "Male"){
				$formalgenderdisplay = "man";
			} else {
				$formalgenderdisplay = "lady";
			}
			
			if (httpget('override')==1){
				output("\"`6Fair enough, then, fair enough - I guess it's pretty pointless asking for an E-mail address when they're impossible to use anyway.`0\"`n`n");
				$carryon = 1;
			} else {
				//validate E-mail
				
				if (!is_email($email)){
					output("The gatekeeper frowns.  \"`6That certainly doesn't `isound`i like an E-mail address.  Are you sure that's right?`0\"`n`nLet's try that again.`n");
				} else {
					//check for duplicates
					$sql = "SELECT login FROM " . db_prefix("accounts") . " WHERE emailaddress='$email'";
					$result = db_query($sql);
					if (db_num_rows($result)>0){
						output("The gatekeeper frowns.  \"`6Are you taking the mick, my good %s?  That E-mail is already in my book, here.  You can only sign up once, you know.`0\"`n`nLet's try that again.`n",$formalgenderdisplay);
					} else {
						//email is good, request password
						output("The gatekeeper grins. \"`6I'm sure if you find those computers, you'll be using that address again soon.`0\"`n`n");
						$carryon = 1;
					}
				}
			}
			
			if ($carryon){
				rawoutput("<script language='JavaScript' src='lib/md5.js'></script>");
				rawoutput("<script language='JavaScript'>
				<!--
				function md5pass(){
					// encode passwords
					var plen = document.getElementById('passlen');
					var pass1 = document.getElementById('pass1');
					plen.value = pass1.value.length;

					if(pass1.value.substring(0, 5) != '!md5!') {
						pass1.value = '!md5!'+hex_md5(pass1.value);
					}
					var pass2 = document.getElementById('pass2');
					if(pass2.value.substring(0, 5) != '!md5!') {
						pass2.value = '!md5!'+hex_md5(pass2.value);
					}

				}
				//-->
				</script>");

				
				
				if (httpget('override')){
					rawoutput("<form action='runmodule.php?module=homepage&op=4&override=1&r=$r' method='POST' onSubmit=\"md5pass();\">");
				} else {
					rawoutput("<form action='runmodule.php?module=homepage&op=4&r=$r' method='POST' onSubmit=\"md5pass();\">");
				}
				rawoutput("<input type='hidden' name='passlen' id='passlen' value='0'>");
				rawoutput("<input type='hidden' name='name' value=\"$name\">");
				rawoutput("<input type='hidden' name='gender' value=\"$sex\">");
				rawoutput("<input type='hidden' name='email' value=\"$email\">");
				output("You hear a rustling in the bushes behind you, and turn to look.  Branches sway a couple of feet above head-height - there's something there.`n`nYou're suddenly even more aware that you're naked and unarmed.`n`nYou turn back to the gatekeeper.  \"`#Is this going to take long?`0\"`n`n\"`6No, no, we're nearly done now.`0\"  He reaches down beneath his counter, and brings up a stack of shiny silver coins.  \"`6Here's eighty Requisition tokens to get you started.`0\"`n`nYou consider asking him where the hell he thinks you're going to keep them, but a low `4`igrowling`i`0 sound changes your mind very quickly.`n`n\"`#Let me in.  Forget the pants, just let me in.`0\"`n`n\"`6One moment, sunshine, one moment.  I need a password from you.`0\"`n`n\"`#You're kidding me!  Did you `ihear`i that?!`0\"`n`n\"`6Yes, I heard it, and you'll get inside faster if you listen to me.  You need to make up a password - at least four characters in length.  I know, it sounds like a silly thing to ask, but there's a very good reason for it - you see...`0\"  The growling intensifies, and you look back towards the jungle.`n`nThere's a face.  It's looking at you from between the very leafy branches of a tree.  It's eight feet off the ground and shaped like a gas mask, its eyes as big as fists and deep, bright orange, shining against its jet black carapace.`n`n\"`#Don't care,`0\" you say quietly.  \"`#Really, really don't care.  Just put down ");
				rawoutput("<input type='password' name='pass1' id='pass1'>");
				output(".`0\"`n`nThe gatekeeper scribbles on his form.  \"`6Sorry, just run that past me again?`0\"`n`n\"`#I said ");
				rawoutput("<input type='password' name='pass2' id='pass2'>");
				output("!`0\"`n`n");
				rawoutput("<div align=\"center\"><input type='submit' value='Now let me in, damn it!' class='button'></div></form>");
			} else {
				//output the E-mail form again
				rawoutput("<form action='runmodule.php?module=homepage&op=3&r=$r' method='POST'\">\"<span class=\"colLtCyan\">Fine - as far as I remember, it's <input name='email' id=\"email\" accesskey='e' size='25'>.</span>\"");
				rawoutput("<input type='hidden' name='name' value=\"$name\">");
				rawoutput("<input type='hidden' name='gender' value=\"$sex\">");
				rawoutput("<br /><div align=\"center\"><input type='submit' value='NOW may I have some pants?' class='button'></div></form>");
				rawoutput("<br /><br /><table width=80% align=center style=\"border: 1px solid #990000;\"><tr><td class=\"trlight\">");
				output("`7He ain't kidding - Improbable Island doesn't spam.  Your E-mail address is used to recover your password if you forget it, and (optionally) to let you know if someone sends you a message in-game - that's it.  We don't use it for anything else, and we never let anyone else see it.`n`nOr, you can continue without entering an E-mail address - but you won't be notified of anything, and you won't be able to recover your password if you lose it!`n");
				rawoutput("<form action='runmodule.php?module=homepage&op=3&override=1&r=$r' method='POST'\"><input name='email' type='hidden' value='Not Given'>");
				rawoutput("<input type='hidden' name='name' value=\"$name\">");
				rawoutput("<input type='hidden' name='gender' value=\"$sex\">");
				rawoutput("<br /><div align=\"center\"><input type='submit' value='Continue without an E-mail address' class='button'></div></form>");
				rawoutput("</td></tr></table>");
				addnav("","runmodule.php?module=homepage&op=3&override=1&r=".$r);
			}
		break;
		case 4:
			output("`0The thing steps out from behind the trees.  Its chitinous body is thin, shiny, and angular.  There are no hands or feet; its legs and arms terminate in sharp points.  Each time it takes a step, it leaves a deep, round piercing in the ground.  You back up against the gatekeeper's hut, unable to take your eyes off the beast.`n`n");
			if ($passlen<=3){
				output("\"`6I'm not sure you heard me right, mate,`0\" says the gatekeeper quietly from behind you.  \"`6I need four characters or more.  No rush.`0\"`n`n");
				$tryagain = true;
			}
			if ($pass1!=$pass2){
				output("\"`6Sorry, mate, I think I heard two different things,`0\" says the gatekeeper quietly from behind you.  \"`6Wanna repeat that?  No rush.`0\"`n`n");
				$tryagain = true;
			}
			if ($tryagain){
				rawoutput("<script language='JavaScript' src='lib/md5.js'></script>");
				rawoutput("<script language='JavaScript'>
				<!--
				function md5pass(){
					// encode passwords
					var plen = document.getElementById('passlen');
					var pass1 = document.getElementById('pass1');
					plen.value = pass1.value.length;

					if(pass1.value.substring(0, 5) != '!md5!') {
						pass1.value = '!md5!'+hex_md5(pass1.value);
					}
					var pass2 = document.getElementById('pass2');
					if(pass2.value.substring(0, 5) != '!md5!') {
						pass2.value = '!md5!'+hex_md5(pass2.value);
					}

				}
				//-->
				</script>");

				if (httpget('override')){
					rawoutput("<form action='runmodule.php?module=homepage&op=4&override=1&r=$r' method='POST' onSubmit=\"md5pass();\">");
				} else {
					rawoutput("<form action='runmodule.php?module=homepage&op=4&r=$r' method='POST' onSubmit=\"md5pass();\">");
				}
				rawoutput("<input type='hidden' name='passlen' id='passlen' value='0'>");
				rawoutput("<input type='hidden' name='name' value=\"$name\">");
				rawoutput("<input type='hidden' name='gender' value=\"$sex\">");
				rawoutput("<input type='hidden' name='email' value=\"$email\">");
				output("\"`#`iSeriously, mate,`i just put down ");
				rawoutput("<input type='password' name='pass1' id='pass1'>");
				output(".`0\"`n`nThe gatekeeper scribbles on his form.  \"`6Sorry, just run that past me again?`0\"`n`n\"`#I said ");
				rawoutput("<input type='password' name='pass2' id='pass2'>");
				output("!`0\"`n`n");
				rawoutput("<div align=\"center\"><input type='submit' value='Now let me in, damn it!' class='button'></div></form>");
			} else {
				//perform a final test on all supplied information, as in create.php
				//password already checked
				$createaccount = 1;
				
				//check name
				$sql = "SELECT name FROM " . db_prefix("accounts") . " WHERE login='$name'";
				$result = db_query($sql);
				//name exists
				if (db_num_rows($result)>0){
					output("name");
					$createaccount = 0;
				}
				
				//name too short or too long
				if (strlen($name) < 3 || strlen($name) > 25){
					output("name");
					$createaccount = 0;
				}
				
				//gender not set
				if ($sex!=1 && $sex!=0){
					output("sex is %s",$sex);
					$createaccount = 0;
				}
				
				//email
				if (!httpget('override')){
					//not overriden
					
					//not an E-mail address
					if (!is_email($email)){
						$createaccount = 0;
						output("email not an email");
					}
					
					//already exists
					$sql = "SELECT login FROM " . db_prefix("accounts") . " WHERE emailaddress='$email'";
					$result = db_query($sql);
					if (db_num_rows($result)>0){
						$createaccount = 0;
						output("email found already");
					}
				}
				
				$args = modulehook("check-create", httpallpost());
				if(isset($args['blockaccount']) && $args['blockaccount']) {
					$msg .= $args['msg'];
					$createaccount = 0;
				}
				
				if ($createaccount){
					//create the account
					
					require_once("lib/titles.php");
					$title = get_dk_title(0, $sex);
					$refer = httpget('r');
					if ($refer>""){
						$sql = "SELECT acctid FROM " . db_prefix("accounts") . " WHERE login='$refer'";
						$result = db_query($sql);
						$ref = db_fetch_assoc($result);
						$referer=$ref['acctid'];
					}else{
						$referer=0;
					}
					$dbpass = "";
					if (substr($pass1, 0, 5) == "!md5!") {
						$dbpass = md5(substr($pass1, 5));
					} else {
						$dbpass = md5(md5($pass1));
					}
					$sql = "INSERT INTO " . db_prefix("accounts") . "
						(name, superuser, title, password, sex, login, laston, uniqueid, lastip, gold, emailaddress, emailvalidation, referer, regdate, race, location)
						VALUES
						('$title $name', '".getsetting("defaultsuperuser",0)."', '$title', '$dbpass', '$sex', '$name', '".date("Y-m-d H:i:s",strtotime("-1 day"))."', '".$_COOKIE['lgi']."', '".$_SERVER['REMOTE_ADDR']."', ".getsetting("newplayerstartgold",50).", '$email', '$emailverification', '$referer', NOW(), 'Human', 'NewHome')";
					db_query($sql);
					if (db_affected_rows(LINK)<=0){
						output("`\$Error`^: Your account was not created for an unknown reason, please try again. ");
					}else{
						$sql = "SELECT acctid FROM " . db_prefix("accounts") . " WHERE login='$name'";
						$result = db_query($sql);
						$row = db_fetch_assoc($result);
						$args = httpallpost();
						$args['acctid'] = $row['acctid'];
						//insert output
						$sql_output = "INSERT INTO " . db_prefix("accounts_output") . " VALUES ({$row['acctid']},'');";
						db_query($sql_output);
						//end
						modulehook("process-create", $args);
						//Project Wonderful Conversion Code
						$pwcode = "<!-- Beginning of Project Wonderful conversion code: -->
						<!-- Conversion Track ID: 12 -->
						<script type=\"text/javascript\">
						<!--var d=document;
						d.projectwonderful_conversion_id = \"12\";
						d.projectwonderful_label = \"\";
						d.projectwonderful_value = \"\";
						//-->
						</script>
						<script type=\"text/javascript\" 
						src=\"http://www.projectwonderfuladservices.com/conversion.js\">
						</script>
						<!-- End of Project Wonderful ad code. -->";
						rawoutput($pwcode);
						if ($emailverification!=""){
							$subj = translate_mail("LoGD Account Verification",0);
							 $msg = translate_mail(array("Login name: %s `n`nIn order to verify your account, you will need to click on the link below.`n`n http://%s?op=val&id=%s `n`nThanks for playing!",$shortname,
								($_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT'] == 80?"":":".$_SERVER['SERVER_PORT']).$_SERVER['SCRIPT_NAME']),
								$emailverification),
								0);
							mail($email,$subj,str_replace("`n","\n",$msg),"From: ".getsetting("gameadminemail","postmaster@localhost.com"));
							output("`4An email was sent to `\$%s`4 to validate your address.  Click the link in the email to activate your account.`0`n`n", $email);
						}else{
							output("\"`#`iLET ME IN!  RIGHT THE HELL NOW!`i`0\"`n`n\"`6Hmm?`0\"  The gatekeeper looks up from his forms.  \"`6Oh!  Yes, I see what you mean.  Wow!`0\" he smiles, as the beast plods closer.  \"`6That's a big one!  Geez, look at the `isize`i of 'im!`0\"`n`n\"`#`iPlease!`i`0\"`n`n\"`6Oh, oh, yes.  Of course.`0\"  He pulls a lever, and the outpost gates swing open.  \"`6In you go, then.`0\"`n`nYou tear through the gates and into the Outpost.`n`nThe gatekeeper chuckles.  \"`6Nice one, Harry!`0\"`n`nGrinning, the monster takes a bow.  \"`4It `inever`i gets old, this,`0\" it says in a broad Yorkshire accent. \"`4You in the pub later?`0\"`n`n\"`6Of course, it's darts night.`0\"`n`n\"`4Right-ho, then.  Ta-raa.`0\"`n`n\"`6See you, Harry.`0\"  The gatekeeper sits back in his chair, lights a cigarette, and carries on with his crossword.`n");
							rawoutput("<div align='center'><form action='login.php' method='POST'>");
							rawoutput("<input name='name' value=\"$name\" type='hidden'>");
							rawoutput("<input name='password' value=\"$pass1\" type='hidden'>");
							$click = translate_inline("Venture into Improbable Island!");
							rawoutput("<input type='submit' class='button' value='$click'>");
							rawoutput("</form></div>");
							output_notl("`n");
							savesetting("newestplayer", $row['acctid']);
						}
					}
					
				} else {
					output("Whoops!  Did you use the back and/or forward buttons?  It looks like your account is already set up.  Head back to the home page and log yourself in!");
					addnav("D'oh!");
					addnav("Back to the homepage","home.php");
				}
			}
		break;
	}
	if ($name){
		addnav("Character Information");
		addnav(array("Character name: %s",$name),"");
		if (httpget('op')!=1 && httpget('op')!=0){
			if ($sex){
				addnav("Gender: Female","");
			} else if (isset($sex)){
				addnav("Gender: Male","");
			}
			if ($email){
				addnav(array("E-mail address: %s",$email),"");
			}
		}
	}
	page_footer();
	return true;
}
?>