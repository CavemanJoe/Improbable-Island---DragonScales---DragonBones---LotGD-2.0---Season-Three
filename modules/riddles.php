<?php
// addnews ready
// mail ready
// translator ready

function riddles_getmoduleinfo(){
	$info = array(
		"name"=>"Riddling Gnome",
		"version"=>"1.2",
		"author"=>"Joe Naylor, modifications by Dan Hall to make it all Jokery and Improbable",
		"category"=>"Forest Specials",
		"download"=>"core_module",
		"prefs"=>array(
			"Riddle Module User Preferences,title",
			"canedit"=>"Has access to the riddle editor,bool|0"
		),
	);
	return $info;
}

function riddles_install(){
	if (db_table_exists(db_prefix("riddles"))) {
		debug("Riddles table already exists");
	} else {
		debug("Creating riddles table.");
		// This is pulled out to another file just because it's so big.
		// no reason to parse it every time this module runs.
		require_once("modules/riddles/riddles_install.php");
	}
	module_addhook("superuser");
	module_addeventhook("forest", "return 100;");
	return true;
}

function riddles_uninstall(){
	debug("Dropping riddles table");
	$sql = "DROP TABLE IF EXISTS " . db_prefix("riddles");
	db_query($sql);
	return true;
}

function riddles_dohook($hookname,$args){
	global $session;
	switch($hookname) {
	case "superuser":
		if (($session['user']['superuser'] & SU_EDIT_RIDDLES) ||
				get_module_pref("canedit")) {
			addnav("Module Configurations");
			// Stick the admin=true so that when we can call runmodule
			// it'll work even if the module is deactivated.
			addnav("Riddle Editor",
					"runmodule.php?module=riddles&act=editor&admin=true");
		}
		break;
	}
	return $args;
}

//** Used to remove extra words from the beginning and end of a string
// Note that string will be converted to lowercase
function riddles_filterwords($string)
{
	$string = strtolower($string);

	//Words to remove
	$filterpre = array ( "a", "an", "and", "the", "my", "your", "someones",
		"someone's", "someone", "his", "her", "s");
	//Letters to take off the end
	$filterpost = array ( "s", "ing", "ed");

	//split into array of words
	$filtstr = explode(" ", trim($string));
	foreach ($filtstr as $key => $filtstr1)
		$filtstr[$key] = trim($filtstr1);

	//pop off word if found in $filterpre
	foreach ($filtstr as $key => $filtstr1)
		foreach ($filterpre as $filterpre1)
			if (!strcasecmp($filtstr1, $filterpre1))
				$filtstr[$key] = "";

	//trim off common word endings
	foreach ($filtstr as $key => $filtstr1)
		foreach ($filterpost as $filterpost1)
		if (strlen($filtstr1) > strlen($filterpost1))
			if (!strcasecmp(substr($filtstr1,
							-1*strlen($filterpost1)), $filterpost1))
				$filtstr[$key] =
					substr($filtstr1, 0,
							strlen($filtstr1)-strlen($filterpost1));

	//rebuild filtered input
	$tmpstring = implode("", $filtstr);
	// Make sure we have an answer .. If not, return the original input!
	if ($tmpstring) {
		$string = $tmpstring;
	}

	return $string;
}

function riddles_runevent($type)
{
	require_once("lib/increment_specialty.php");
	global $session;
	// We assume this event only shows up in the forest currently.
	$from = "forest.php?";
	$session['user']['specialinc'] = "module:riddles";

	require_once("lib/partner.php");
	$partner = get_partner();

	$op = httpget('op');
	if ($op=="" || $op=="search"){
		output("You come across a tall man wearing an immaculate white Victorian suit, a top hat, and the sort of smile that you only ever see on tigers who want you to believe that they're kittens.  He leans on a handsome walking stick as he eyes you up and down.`n`n\"Good afternoon,\" says the gentleman.  \"Would you care to play a game with me?\"`n`nYou're pretty sure this guy is a Joker.  They like stuff like this.  And the glowing green eyes were a dead giveaway.`n`n\"What sort of game?\" you ask warily.`n`n\"Well,\" says the gentleman, smartly rapping his staff upon the floor and sauntering towards you, \"I've always been fond of riddles, you see.  Cryptic questions, as it were.  How about if I ask you a riddle, and if you get it right, I'll give you something rather nice?\"`n`nYou smirk.  \"And I presume that if I get it wrong, you'll do something horrible to me.\"`n`n\"Oh,\" says the Joker, smiling even wider, \"`iquite`i horrible, I assure you.  All a part of the service, you know!\"`n`nWhat will you do?");
		addnav("Cryptic Joker");
		addnav("Play his game", $from."op=yes");
		addnav("Run like hell", $from."op=no");
		$session['user']['specialmisc']="";
	}elseif($op=="yes"){
		$subop = httpget('subop');
		if ($subop!="answer"){
			$rid = $session['user']['specialmisc'];
			if (!strpos($rid, "Riddle")) {
				$sq1 = "SELECT * FROM " . db_prefix("riddles") . " ORDER BY rand(".e_rand().")";
			}else{
				// 6 letters in "Riddle"
				$rid = substr($rid, -1*(strlen($rid)-6));
				$sq1 = "SELECT * FROM " . db_prefix("riddles") . " WHERE id=$rid";
			}
			$result = db_query($sq1);
			$riddle = db_fetch_assoc($result);
			$session['user']['specialmisc']="Riddle" . $riddle['id'];
			output("The Joker smiles, leans in close, and whispers to you:`n`n");
			output("`6\"`@%s`6\"`n`n", $riddle['riddle']);
			output("What is your guess?");
			rawoutput("<form action='".$from."op=yes&subop=answer' method='POST'>");
			rawoutput("<input name='guess'>");
			$guess = translate_inline("Guess");
			rawoutput("<input type='submit' class='button' value='$guess'>");
			rawoutput("</form>");
			addnav("",$from."op=yes&subop=answer");
		}else{
			$rid = substr($session['user']['specialmisc'], 6);
			$sq1 = "SELECT * FROM " . db_prefix("riddles") . " WHERE id=$rid";
			$result = db_query($sq1);
			$riddle = db_fetch_assoc($result);

			//*** Get and filter correct answer
			// there can be more than one answer in the database,
			// separated by semicolons (;)
			$answer = explode(";", $riddle['answer']);
			foreach($answer as $key => $answer1) {
				// changed "" to " " below, I believe this is the correct
				// implementation.
				$answer[$key] = preg_replace("/[^[:alnum:]]/"," ",$answer1);
				$answer[$key] = riddles_filterwords($answer1);
			}

			//*** Get and filter players guess
			$guess = httppost('guess');
			$guess = preg_replace("/[^[:alnum:]]/"," ",$guess);
			$guess = riddles_filterwords($guess);

			$correct = 0;
			//changed to 2 on the levenshtein just for compassion's
			// sake :-)  --MightyE
			foreach($answer as $answer1) {
				// Only allow spelling mistakes if te word is long enough
				if (strlen($answer1) > 2) {
					if (levenshtein($guess,$answer1) <= 2) {
						// Allow two letter to be off to compensate for silly
						// spelling mistakes
						$correct = 1;
					}
				} else {
					// Otherwise, they have to be exact
					if ($guess == $answer1) {
						$correct = 1;
					}
				}
			}
			// make sure an empty response from the player is never correct.
			if (!$guess) $correct = 0;

			if ($correct) {
				if (is_module_active("medals")){
					require_once "modules/medals.php";
					medals_award_medal("crypticjoker","Cryptic Answers","This player correctly answered a question from the Cryptic Questions Joker!","medal_crypticjoker.png");
				}
				output("`nThe Joker's face splits into a wide grin.  \"I see you're no stranger to cryptic questions!  Very well, here is your prize.\"`n`nHe produces a long, thin hypodermic needle from his sleeve and, moving with the speed of a cat, slides it into your arm...`n`n`6");
				// It would be nice to have some more consequences
				$rand = e_rand(1, 7);
				switch ($rand){
				case 1:
				case 2:
					output("You feel the chemical beginning to toughen your skin!  Within moments, your defensive powers are dramatically increased!");
					apply_buff("riddlejoker1",array(
						"name"=>"`7Cryptic Questions Joker Needle`0",
						"defmod"=>"1.2",
						"rounds"=>20,
						"wearoff"=>"`4The effects of the Riddling Joker's hypodermic have faded...`0",
						"schema"=>"module-riddles",
						)
					);
					break;
				case 3:
				case 4:
					output("You feel the chemical beginning to heighten your perception!  Within moments, you feel a lot more dangerous!");
					apply_buff("riddlejoker2",array(
						"name"=>"`7Cryptic Questions Joker Needle`0",
						"atkmod"=>"1.2",
						"rounds"=>20,
						"wearoff"=>"`4The effects of the Riddling Joker's hypodermic have faded...`0",
						"schema"=>"module-riddles",
						)
					);
					break;
				case 5:
				case 6:
					output("You feel the chemical beginning to quicken your heartbeat!  Within moments, you feel you have the energy to take on a couple more jungle fights!  You gain some Stamina!");
					$session['user']['turns']++;
					$session['user']['turns']++;
					break;
				case 7:
					output("You feel a burning in your extremities as your muscles begin to knit back together...");
					apply_buff("riddlejoker3",array(
						"name"=>"`7Cryptic Questions Joker Needle`0",
						"regen"=>e_rand(1,12),
						"rounds"=>20,
						"effectmsg"=>"`4The chemical contained in the Riddle Joker's hypodermic surprise heals you for {damage} points.",
						"wearoff"=>"`4The effects of the Riddling Joker's hypodermic have faded...`0",
						"schema"=>"module-riddles",
						)
					);
					break;
				}
			}else{
				output("`nThe Joker smiles.  \"I see.  Well, then, let's see what we have for you...\"`n`nHe produces a slender, wickedly sharp hypodermic needle from an inner pocket and jabs it into your neck before you even have time to react.`n`n`4");

				// It would be nice to have some more consequences
				$rand = e_rand(1, 7);
				switch ($rand){
				case 1:
				case 2:
					output("You feel the chemical beginning to slow you down!  Within moments, your defensive powers are dramatically reduced!");
					apply_buff("riddlejoker4",array(
						"name"=>"`7Cryptic Questions Joker Needle`0",
						"defmod"=>"0.8",
						"rounds"=>10,
						"wearoff"=>"`4The effects of the Riddling Joker's hypodermic have faded...`0",
						"schema"=>"module-riddles",
						)
					);
					break;
				case 3:
				case 4:
					output("You feel the chemical beginning to weaken your muscles!  Within moments, you feel a lot less dangerous!");
					apply_buff("riddlejoker5",array(
						"name"=>"`7Cryptic Questions Joker Needle`0",
						"atkmod"=>"0.8",
						"rounds"=>10,
						"wearoff"=>"`4The effects of the Riddling Joker's hypodermic have faded...`0",
						"schema"=>"module-riddles",
						)
					);
					break;
				case 5:
				case 6:
					output("You suddenly feel drowsy!  You lose some Stamina!");
					$session['user']['turns']--;
					$session['user']['turns']--;
					break;
				case 7:
					output("Your muscles begin to decay inside your skin as you live and breathe!");
					apply_buff("riddlejoker6",array(
						"name"=>"`7Cryptic Questions Joker Needle`0",
						"regen"=>e_rand(-1,-12),
						"rounds"=>10,
						"effectmsg"=>"`4The chemical contained in the Riddle Joker's hypodermic surprise damages you for {damage} points.",
						"effectfailmsg"=>"`4The chemical contained in the Riddle Joker's hypodermic surprise damages you for {damage} points.",
						"wearoff"=>"`4The effects of the Riddling Joker's hypodermic have faded...`0",
						"schema"=>"module-riddles",
						)
					);
					break;
				}
			}
			$session['user']['specialinc']="";
			$session['user']['specialmisc']="";
		}
	}elseif($op=="no"){
		output("`n`6Afraid to look the fool, you decline his challenge.");
		output("He was a little bit creepy anyway.`n");
		output("`6The strange man waves goodbye as you stroll, a little quickly but hating to show it, into the depths of the jungle.");
		$session['user']['specialinc']="";
		$session['user']['specialmisc']="";
	}
	output("`0");
}

function riddles_run(){
	$act = httpget("act");
	if ($act=="editor") riddles_editor();
}

function riddles_editor() {
	global $session;
	require_once("lib/nltoappon.php");


	if (!get_module_pref("canedit")) check_su_access(SU_EDIT_RIDDLES);

	$op = httpget('op');
	$id = httpget('id');

	page_header("Riddle Editor");
	require_once("lib/superusernav.php");
	superusernav();
	addnav("Riddle Editor");
	addnav("Riddle Editor Home","runmodule.php?module=riddles&act=editor&admin=true");
	addnav("Add a riddle","runmodule.php?module=riddles&act=editor&op=edit&admin=true");
	if ($op=="save"){
		$id = httppost('id');
		$riddle = trim(httppost('riddle'));
		$answer = trim(httppost('answer'));
		if ($id > "") {
			$sql = "UPDATE " . db_prefix("riddles") . " SET riddle='".nltoappon($riddle)."', answer='$answer' WHERE id='$id'";
		}else{
			$sql = "INSERT INTO " . db_prefix("riddles") . " (riddle,answer,author) VALUES('".nltoappon($riddle)."','$answer','{$session['user']['login']}')";
		}
		db_query($sql);
		if (db_affected_rows()>0){
			$op = "";
			httpset("op", "");
			output("Riddle saved.");
		}else{
			output("The query was not executed for some reason I can't fathom.");
			output("Perhaps you didn't actually make any changes to the riddle.");
		}
	}elseif ($op=="del"){
		$sql = "DELETE FROM " . db_prefix("riddles") . " WHERE id='$id'";
		db_query($sql);
		$op = "";
		httpset("op", "");
		output("Riddle deleted.");
	}
	if ($op==""){
		$sql = "SELECT * FROM " . db_prefix("riddles");
		$result = db_query($sql);
		$i = translate_inline("Id");
		$ops = translate_inline("Ops");
		$rid = translate_inline("Riddle");
		$ans = translate_inline("Answer");
		$auth = translate_inline("Author");

		rawoutput("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'><tr class='trhead'><td>$i</td><td>$ops</td><td>$rid</td><td>$ans</td><td>$auth</td></tr>");
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			rawoutput("<tr class='".($i%2?"trlight":"trdark")."'>");
			rawoutput("<td valign='top'>");
			output_notl("%s", $row['id']);
			rawoutput("</td><td valign='top'>");
			$conf = translate_inline("Are you sure you wish to delete this riddle?");
			$edit = translate_inline("Edit");
			$del = translate_inline("Delete");
			$elink = "runmodule.php?module=riddles&act=editor&op=edit&id=".$row['id']."&admin=true";
			$dlink = "runmodule.php?module=riddles&act=editor&op=del&id=".$row['id']."&admin=true";
			output_notl("[");
			rawoutput("<a href='$elink'>$edit</a>");
			output_notl("|");
			rawoutput("<a href='$dlink' onClick='return confirm(\"$conf\");'>$del</a>");
			output_notl("]");

			addnav("",$elink);
			addnav("",$dlink);
			rawoutput("</td><td valign='top'>");
			output_notl("`&%s`0", $row['riddle']);
			rawoutput("</td><td valign='top'>");
			output_notl("`#%s`0", $row['answer']);
			rawoutput("</td><td valign='top'>");
			output_notl("`^%s`0", $row['author']);
			rawoutput("</td></tr>");
		}
		rawoutput("</table>");
	}elseif ($op=="edit"){
		$sql = "SELECT * FROM " . db_prefix("riddles") . " WHERE id='$id'";
		$result = db_query($sql);
		rawoutput("<form action='runmodule.php?module=riddles&act=editor&op=save&admin=true' method='POST'>",true);
		addnav("","runmodule.php?module=riddles&act=editor&op=save&admin=true");
		if ($row = db_fetch_assoc($result)){
			output("`bEdit a riddle`b`n");
			$title = "Edit a riddle";
			$i = $row['id'];
			rawoutput("<input type='hidden' name='id' value='$i'>");
		}else{
			output("`bAdd a riddle`b`n");
			$title = "Add a riddle";
			$row = array(
				"riddle"=>"",
				"answer"=>"",
				"author"=>$session['user']['login']);
		}
		$form = array(
			"Riddle,title",
			"riddle"=>"Riddle text,textarea",
			"answer"=>"Answer",
			"author"=>"Author,viewonly"
		);
		require_once("lib/showform.php");
		showform($form, $row);
		rawoutput("</form>");
		output("`^NOTE:`& Separate multiple correct answers with semicolons (;)`n`n");
		output("`7The following are ignored at the start of answers: `&a, an, and, the, my, your, someones, someone's, someone, his, hers`n");
		output("`7The following are ignored at the end of answers: `&s, ing, ed`0`n`n");
		output("`\$NOTE:  Riddles are displayed in the language they are stored in the database.");
		output("Similarly, answers are expected in the language stored in the database.");
	}
	page_footer();
}
?>
