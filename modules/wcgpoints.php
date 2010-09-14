<?php

function wcgpoints_getmoduleinfo(){
	$info = array(
		"name"=>"Cobblestone Cottage - World Community Grid Points Integration",
		"author"=>"Dan Hall AKA CavemanJoe, ImprobableIsland.com",
		"version"=>"2009-08-10",
		"category"=>"WCG Points",
		"download"=>"",
		"settings"=>array(
			"teamid"=>"WCG Team ID,text|13F2W0053V1",
		),
		"prefs"=>array(
			"Cobblestone Cottage,title",
			"uidok"=>"Player's WCG ID is legit,bool|0",
			"fail"=>"Player has failed to obtain points for some reason and should be prompted to re-enter their Verification Code,bool|0",
			"points"=>"Number of WCG Points accumulated,int|0",
			"spent"=>"Number of Cobblestones spent,int|0",
			"bonus"=>"Number of Cobblestones awarded by the game,int|0",
			"wcgid"=>"Your World Community Grid username,text",
			"user_vcode"=>"Your World Community Grid verification code - available from the 'profile' menu when logged in to the World Community Grid website,text",
		)
	);
	return $info;
}

function wcgpoints_install(){
	module_addhook("gardens");
	module_addhook("newday");
	module_addhook("moderate");
	return true;
}

function wcgpoints_uninstall(){
	return true;
}

function wcgpoints_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "gardens":
			addnav("Cobblestone Cottage","runmodule.php?module=wcgpoints&op=enter");
		break;
		case "newday":
			if (get_module_pref("wcgid")){
				$newpoints = wcgpoints_updatepoints(true);
				if ($newpoints > 1){
					output("`0You find a letter from Jake the Hippy.  It tells you that, by whatever obscure cosmic algorithms Jake uses to decide these things, you've got an extra %s cobblestones waiting for you at Cobblestone Cottage.`n`n",number_format($newpoints));
				} else if (get_module_pref("fail")){
					output("`0You find a letter from Jake the Hippy.  It seems Jake has had some trouble reaching the cosmos.  You may need to update your Verification Code via your Preferences - the menu is available from every Outpost.");
				}
			}
		break;
		case "moderate":
			$args['wcgpoints'] = "Cobblestone Cottage";
		break;
	}
	return $args;
}

function wcgpoints_run(){
	global $session;
	page_header("Cobblestone Cottage");
	
	$submit = translate_inline("Carry On");
	
	switch (httpget('op')){
		case "enter":
			$uid_ok = get_module_pref("uidok");
			output("`4`bWARNING: Cobblestone Cottage is currently in BETA.  Don't expect it to work properly.`b  Please don't send Petitions about this feature - use the chat area below, or the Enquirer.`0`n`n");
			if (get_module_pref("wcgid") && $uid_ok){
				if (!get_module_pref("fail")){
					$points = wcgpoints_getpoints();
					output("`0Jake greets you with a broad smile.  \"`@Welcome back, man.  You've got %s cobblestones waiting to spend.  Have fun!`0\"`n`n",number_format($points));
					modulehook("wcg-features");
				} else {
					//WCG ID present but not verified
					debug("WCG ID not verified");
					output("Jake greets you with a concerned look.  \"`@Man, I've been wondering where you've been.  I asked the Cosmos how many Cobblestones to give you, and it wouldn't answer.  Something about a messed-up Verification Code, whatever that means.`0\"`n`nIt looks like your Verification Code needs to be re-checked - you can find it via your Profile page on the World Community Grid website, and change our record of it via your Preferences in any Outpost.`n`n");
					addnav("Let's do that, then","runmodule.php?module=wcgpoints&op=verify");
				}
			} else {
				debug("No WCG ID entered");
				output("`0You head into a beautiful stone cottage.  A long-haired young man sits behind a desk at the entrance, idly smoking a cigarette.  As you catch his eye, he grins and rests his cigarette in an ashtray.  It smells kinda funny.`n`n\"`@Hey, man.  Welcome to Cobblestone Cottage!`0\"  He shakes your hand.  \"`@The name's Jake.  It doesn't look like you're hooked up yet.  Here, let me fix that for you, and you'll be rolling in cobblestones in no time.`0\"`n`n\"`#Cobblestones?`0\" you ask.`n`n\"`@Cobblestones, man, cobblestones!`0\" exclaims the hippy.  \"`@Whole new local currency - you saw the mess the banks made of the last one!  This stuff is as indie as it `igets`i, man.  You can only spend them in this very cottage.  Let's get you hooked up.`0\"`n`nHe sits down and pulls out a worn leather notebook and a purple-feathered quill pen.  \"`@What's your ID, my friend?`0\"`n`n");
				output("`J`bPinned to the slightly run-down fourth wall of the cottage is this message:`b`n`n`0The ID that the dirty hippy is asking for is your World Community Grid username.`n`n`bWhat's the story, daddy-o?`b`n`nYour computer can cure AIDS and cancer.`n`nNo, seriously.  It can also help provide us with cheap, environmentally-friendly electricity, sort out world hunger, and kick Dengue Fever in the nuts.  It can do a lot, really.  Most people hardly ever use the full capacity of their computer's CPU's.  You'll certainly have unused CPU cycles lying around while you're surfing the Internet or playing this game.  It'd be a terrible shame to waste those cycles.  Why not donate them to a worthy cause?`n`nScientists and humanitarian researchers need all the computing power they can get their hands on in order to analyse protein structures, chemical properties and other computationally-intensive stuff.  You can download a program that'll perform this life-saving research on your very own computer - and it'll run at a very low priority, so that when you do want to use your PC for something computationally intensive like playing a 3D game, your computer will be just as responsive.`n`nThe more CPU cycles you donate to humanitarian research, the more cobblestones Jake will give you.  These cobblestones can be used to play games or obtain other resources inside the Cottage.  See the list at the end of this page to see what you can do with your cobblestones, and check back as we come up with new features.`n`n`bThe nitty-gritty`b`n`nIn a nutshell, World Community Grid is a distributed computing network that performs humanitarian research using your computer's unused CPU cycles.  The project uses the BOINC networked computing client, which runs on Windows, Mac, Linux, FreeBSD and many other platforms, and doesn't affect the normal day-to-day performance of your computer.  When your computer's screensaver is active, the World Community Grid client will request instructions, process them, and send them back.  When your computer is in use, the client goes into Snooze mode and doesn't use any CPU cycles, so you'll likely not notice that the client is running.`n`nHumanitarian work performed by your computer for World Community Grid includes, but is not limited to:`nDeveloping drugs to combat cancer in children and adults;`nDeveloping antiviral medication for new influenza strains;`nCalculating the electronic properties of materials in order to find an organic compound that we can use to make dirt-cheap solar panels;`nDetermining the best options to create a new strain of rice that will provide maximum nutrition and thrive in a harsh environment;`nUncovering new drugs to combat dengue hemorrhagic fever, hepatitis C, West Nile encephalitis, and Yellow fever;`nIdentifying candidate drugs that have the right shape and chemical characteristics to block HIV protease;`nHuman proteome folding.`n`nFor more information or to join, search for World Community Grid in Wikipedia or your favourite search engine.`n`n`bHow World Community Grid interacts with Improbable Island`b`n`nAt each new game day, Jake will reward you with his entirely-made-up currency based on how many World Community Grid points you've accumulated since the previous game day.  More information on how WCG Points are calculated and rewarded can be found via a web search, or on the World Community Grid website.  You may install and run the BOINC client on more than one machine if you wish, under the same WCG username - this will result in more points and thus more cobblestones.`n`nWCG only updates their stats every 24 hours, and the work done by your computer must be verified first - so the XML file that Improbable Island reads to determine your cobblestone awards may be several days behind the actual work done by your computer.  To save undue stress on World Community Grid's servers, cobblestones are only awarded at each new Game Day.  If you joined World Community Grid today, it may be a day or two (or three!) before Jake gives you `iany`i cobblestones at all - however, if you allow your computer to run the BOINC client reasonably often, you should get more cobblestones every day after the first batch arrive.`n`nIf you've been a member of World Community Grid for some time, Jake will give you an initial bag of cobblestones corresponding to the total work done by your computer over the course of your entire WCG membership.  You'll be stinking rich!`n`n`bDISCLAIMER`b`nImprobable Island is not affiliated with or endorsed by IBM or World Community Grid.  In fact, I'd be surprised if they even knew about this.`n`n`bGETTING STARTED`b`nThere are three steps to the process - signing up at World Community Grid, installing the BOINC client, and associating your World Community Grid account with your Improbable Island username.`n`n`bSteps One and Two`b`nIf you're already signed up with World Community Grid, you can skip this bit.`n");
				rawoutput("Head over to <a href=\"http://www.worldcommunitygrid.org/reg/viewRegister.do?teamID=".get_module_setting("teamid")."\" target='new'>World Community Grid</a>, and follow the onscreen instructions to sign up, download and install the WCG BOINC client.");
				output("`n`n`bStep Three`b`nEnter your World Community Grid username and Verification Code into the boxes below.`n");
				rawoutput("<form action='runmodule.php?module=wcgpoints&op=verify' method='POST'>World Community Grid username: <input name='wcgid' value=\"\"><br />World Community Grid Verification Code: <input name='vcode' value=\"\"><br />Your verification code can be found on <a href=\"https://secure.worldcommunitygrid.org/ms/viewMyProfile.do\">this page</a>.  Copy-paste it into the box above, making sure you don't leave any tabs or spaces in there.<br /><input type='submit' class='button' value='$submit'></form>");
				addnav("","runmodule.php?module=wcgpoints&op=verify");
				output("`bFeatures available in Cobblestone Cottage:`b`n`n");
				modulehook("wcg-features-desc");
			}
			require_once("lib/commentary.php");
			addcommentary();
			viewcommentary("wcgpoints","Interject your own opinions about trying to get the damned thing working!",25);
			addnav("Exit to Common Ground","gardens.php");
		break;
		case "verify":
			//Player has entered a WCG ID
			//Check that it exists
			$id = httppost('wcgid');
			$vcode = httppost('vcode');
			$id = str_replace(" ","+",$id);
			$source = "http://www.worldcommunitygrid.org/verifyMember.do?name=".$id."&code=".$vcode;
			
			if ($xmlObj=simplexml_load_file($source)){
				if (!$xmlObj->MemberStat && !$xmlObj->MemberStats->MemberStat){
					debug($xmlObj);
					output("Jake scans his notebook and frowns.  \"`@You sure that's right, man?`0\"`n`n");
					output("Something went wrong.  Please double-check the information you provided - remember that both usernames and verification codes are case-sensitive.`n`nHere's the error that World Community Grid passed back - it may or may not be useful:`n`4`b%s`b`0`nIf there is no error message above, then it's likely that World Community Grid is running its nightly stats update - in which case, try again in an hour or so.`n`n",$xmlObj);
					rawoutput("<form action='runmodule.php?module=wcgpoints&op=verify' method='POST'>World Community Grid username: <input name='wcgid' value=\"\"><br />World Community Grid Verification Code: <input name='vcode' value=\"\"><br />Your verification code can be found on <a href=\"https://secure.worldcommunitygrid.org/ms/viewMyProfile.do\">this page</a>.  Copy-paste it into the box above, making sure you don't leave any tabs or spaces in there.<br /><input type='submit' class='button' value='$submit'></form>");
					addnav("","runmodule.php?module=wcgpoints&op=verify");
				} else {
					//Check that the username isn't already taken
					$sql = "SELECT acctid FROM " . db_prefix("accounts") . "";
					$result = db_query($sql);
					$idlo = strtolower($id);
					for ($i=0;$i<db_num_rows($result);$i++){
						$row = db_fetch_assoc($result);
						$check = strtolower(get_module_pref("wcgid","wcgpoints",$row['acctid']));
						if ($check == $idlo){
							debug("Match found");
							if (get_module_pref("wcgid-verified","wcgpoints",$row['acctid'])){
								output("`bThat World Community Grid username has already been taken!`b`n`n");
								$alreadyexists = 1;
								rawoutput("<form action='runmodule.php?module=wcgpoints&op=verify1' method='POST'>World Community Grid username: <input name='wcgid' value=\"\"><br />World Community Grid Verification Code: <input name='vcode' value=\"\"><br />Your verification code can be found on <a href=\"https://secure.worldcommunitygrid.org/ms/viewMyProfile.do\">this page</a>.  Copy-paste it into the box above, making sure you don't leave any tabs or spaces in there.<br /><input type='submit' class='button' value='$submit'></form>");
								addnav("","runmodule.php?module=wcgpoints&op=verify");
								break;
							}
						}
					}
					if (!$alreadyexists){
						set_module_pref("uidok",true);
						set_module_pref("wcgid",$id);
						set_module_pref("user_vcode",$vcode);
						$points = $xmlObj->MemberStat->StatisticsTotals->Points;
						if (!$points) $points = $xmlObj->MemberStats->MemberStat->StatisticsTotals->Points;
						if (!$points) $points = $xmlObj->MemberStatsWithTeamHistory->MemberStats->MemberStat->StatisticsTotals->Points;
						if (!$points){
							output("The hippy smiles.  \"`@Okay, you're all set.  Come back tomorrow and we'll sort you out with some cobblestones.`0\"`n`nYour account was verified successfully, but no points have been processed yet.  Points sometimes process a day or two late - check back and you'll get some cobblestones soon.`n`n");
						} else {
							set_module_pref("points",$points);
							output("The hippy smiles.  \"`@Okay, you're all set.  Let's sort you out with some cobblestones...`0\"  He reaches underneath his desk and brings out a large bag.  \"`@Here you go, man.  %s cobblestones.  Have fun!`0\"`n`nThose should last you a while...`n`n",number_format($points));
						}
					}
				}
			} else {
				output("Whoops!  The XML file from World Community Grid couldn't be loaded.  This could be because World Community Grid is updating its point totals, in which case try again in an hour.  If it's still not working in a couple of hours, please Petition the admins to find out what went wrong.  Thanks!`n`n");
			}
			addnav("Exit to Common Ground","gardens.php");
		break;
	}
	
	page_footer();
	return true;
}

function wcgpoints_updatepoints(){
	global $session;

	debug("Cottage : wcgpoints_updatepoints started");
	$id = get_module_pref("wcgid","wcgpoints");
	$vc = get_module_pref("user_vcode","wcgpoints");
	
	if ($id == ""){
		debug("No ID!");
		return false;
	}
	
	$id = str_replace(" ","+",$id);
	
	$source = "http://www.worldcommunitygrid.org/verifyMember.do?name=".$id."&code=".$vc;
	$file = @file_get_contents($source);
	
	if ($file){
		$loaderror = strpos($file, "World Community Grid");
		if (!$loaderror){
			$xmlObj = @simplexml_load_file("http://www.worldcommunitygrid.org/verifyMember.do?name=".$id."&code=".$vc);
			if (!$xmlObj){
				debug("Cannot read that World Community Grid XML file.");
				return false;
			} else {
				$debug = get_object_vars($xmlObj);
				debug($debug);
				debug($xmlObj);
				$error = $xmlObj->Error;
				if ($xmlObj->MemberStats){
					$points = $xmlObj->MemberStat->StatisticsTotals->Points;
					if (!$points) $points = $xmlObj->MemberStats->MemberStat->StatisticsTotals->Points;
					if (!$points) $points = $xmlObj->MemberStatsWithTeamHistory->MemberStats->MemberStat->StatisticsTotals->Points;
					if (!$points) return false;
					$lastpoints = get_module_pref("points","wcgpoints");
					if ($points > $lastpoints){
						set_module_pref("points",$points,"wcgpoints");
						$addpoints = ($points - $lastpoints);
						return $addpoints;
					} else {
						return false;
					}
				} else {
					if ($xmlObj=="The code is not correct"){
						set_module_pref("fail",true,"wcgpoints");
					}
					return false;
				}
			}
		} else {
			debug("Cannot get XML file from World Community Grid for that username.");
			return false;
		}
	}
}

function wcgpoints_getpoints($output=false,$userid=false){
	global $session;
	if (!$userid){
		$userid=$session['user']['acctid'];
	}
	$cstones = get_module_pref("points","wcgpoints",$userid);
	$spent = get_module_pref("spent","wcgpoints",$userid);
	$left = $cstones - $spent;
	if ($output){
		output("You have accumulated a total of %s Cobblestones, of which you have spent %s, leaving %s left to spend.`n`n",number_format($cstones),number_format($spent),number_format($left));
	}
	return $left;
}

?>