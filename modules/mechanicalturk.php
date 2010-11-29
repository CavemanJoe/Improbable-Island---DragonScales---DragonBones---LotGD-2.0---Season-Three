<?php

function mechanicalturk_getmoduleinfo(){
	$info = array(
		"name"=>"Mechanical Turk",
		"author"=>"Dan Hall",
		"version"=>"2008-09-24",
		"category"=>"Administrative",
		"download"=>"",
		"settings"=>array(
			"Mechanical Turk Module Settings,title",
			"addpoints"=>"How many Donator Points will be awarded when the player's monster is accepted?,int|50",
		),
	);
	
	return $info;
}

function mechanicalturk_install(){
	$mechanicalturk = array(
		'creatureid'=>array('name'=>'creatureid', 'type'=>'int(11) unsigned', 'extra'=>'auto_increment'),
		'creaturename'=>array('name'=>'creaturename', 'type'=>'text'),
		'creatureweapon'=>array('name'=>'creatureweapon', 'type'=>'text'),
		'creaturewin'=>array('name'=>'creaturewin', 'type'=>'text'),
		'creaturelose'=>array('name'=>'creaturelose', 'type'=>'text'),
		'creaturelevel'=>array('name'=>'creaturelevel', 'default'=>'0', 'type'=>'int(11) unsigned'),
		'forest'=>array('name'=>'forest', 'default'=>'0', 'type'=>'int(11) unsigned'),
		'graveyard'=>array('name'=>'graveyard', 'default'=>'0', 'type'=>'int(11) unsigned'),
		'description'=>array('name'=>'description', 'type'=>'text'),
		'submittedby'=>array('name'=>'submittedby', 'type'=>'text'),
		'uid'=>array('name'=>'uid', 'type'=>'int(11) unsigned'),
		'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'creatureid'),
	);
	require_once("lib/tabledescriptor.php");
	synctable(db_prefix('mechanicalturk'), $mechanicalturk, true);
	module_addhook("forest");
	module_addhook("superuser");
	return true;
}

function mechanicalturk_uninstall(){
	$sql = 'DROP TABLE IF EXISTS '.db_prefix( 'mechanicalturk' );
	db_query( $sql );
	return true;
}

function mechanicalturk_dohook($hookname,$args){
	global $session, $enemies;
	switch($hookname){
	case "forest":
		addnav("Other");
		addnav("Report a monster sighting","runmodule.php?module=mechanicalturk&creatureaction=report");
		break;
	case "superuser":
		addnav("Show list of submitted monsters","runmodule.php?module=mechanicalturk&creatureaction=showsubmitted");
		break;
	}
	return $args;
}

function mechanicalturk_run(){
	global $session;
	require_once("common.php");
	require_once("lib/http.php");
	page_header("Report a Monster Sighting");
	$points = get_module_setting("addpoints");
	switch (httpget("creatureaction")){
		case "report":
			require_once("lib/showform.php");
			// $level = 1;
			output("You head into a little hut on the outskirts of the Outpost, close to where the clearing turns to jungle.`n`nAn excitable-looking man sits behind a desk, a pair of binoculars slung around his neck.`n`n\"`2Hello!`0\" he calls to you, practically bouncing with geeky excitement.  \"`2Have you come to report a sighting of a new monster?  I do so enjoy writing them up!`0\"  He opens a little ledger and whips out a pen, ready for your report.`n`nYou've heard rumours about this guy.  Billing himself as a monster expert, he listens to the reports of new monsters that players find, and writes them down in his book.  Sauntering into his hut and providing deadly serious reports of completely made-up monsters is a game that many contestants enjoy playing.  However, given the aura of Improbability surrounding his innocent-looking ledger, the rumours contain a dark side as well; sometimes, if a made-up monster is deemed to be Improbable enough by the standards of whatever strange powers control the Island, it takes on a physical form in the Jungle...`n`n");
			output("As you're thinking this, a splintering CRUNCH from your left causes you to jump three feet into the air.  `%Admin `4Caveman`\$Joe`0 is standing in the remains of the fourth wall of the hut, holding a large axe.  He strikes an attractive pose and says \"`4This is Improbable Island's monster submission hut.  Think of a new monster, and submit it here.  If your idea is accepted, you'll get %s Donator Points!`0\"`n`nHe turns to the little man sat behind the desk, who at this moment is picking bits of wood out of his tea, hair and person in general.  `%Admin `4Caveman`\$Joe`0 shows him a smile.  \"`4I'd say 'Sorry about your wall,' mate, but I'm not.  All part of the job, you see.`0\"`n`nWith that, he walks back out of the hole in the fourth wall, attaches his Admin Goggles, leaps into the sky and flies away.`n`n",$points);
			output("\"`2What a very, very strange man,`0\" says the little man behind the desk.  Quietly.`n`n");
			output("`4`b<a href=\"http://enquirer.improbableisland.com/dokuwiki/doku.php?id=terrible_monster_suggestions\">Here are the monster submission guidelines</a>.`b`0  You MUST read these first if you want any chance at all of your monster being accepted, I really can't stress this enough.`n`n",true);
			rawoutput("When writing descriptions, please use `n to go down one line, and `n`n to leave a blank line.  That's `n, not 'n.  The ` key is usually in the top left corner of your keyboard - it's the same key you use for colour codes.");
			$form = array(
				"Creature Properties,title",
				"creatureid"=>"Creature id,hidden",
				"creaturename"=>"Creature Name",
				"creatureweapon"=>"Weapon",
				"creaturewin"=>"Win Message (Displayed when the `bcreature kills the player`b)",
				"creaturelose"=>"Death Message (Displayed when the `bplayer kills the creature`b)",
				// 18 to make a non-forest available monster
				// (ie, graveyard only)_
				"creaturelevel"=>"Level,range,1,17,1",
				"forest"=>"Creature is in Jungle?,bool",
				"graveyard"=>"Creature is on FailBoat?,bool",
				"description"=>"A long description of the creature",
			);
			$row = array("creatureid"=>0);
			addnav("I honestly don't have any ideas.  Back to the Jungle.","forest.php");
			rawoutput("<form action='runmodule.php?module=mechanicalturk&creatureaction=save' method='POST'>");
			showform($form, $row);
			rawoutput("</form>");
			addnav("", "runmodule.php?module=mechanicalturk&creatureaction=save");
			break;
		case "save":
			$creatureid = httppost('creatureid');
			$creaturename = httppost('creaturename');
			$creatureweapon = httppost('creatureweapon');
			$creaturewin = httppost('creaturewin');
			$creaturelose = httppost('creaturelose');
			$creaturelevel = httppost('creaturelevel');
			$forest = httppost('forest');
			$graveyard = httppost('graveyard');
			$description = httppost('description');
			$submittedby = $session['user']['name'];
			$uid = $session['user']['acctid'];
//			$creatureid = db_insert_id();
//			$creatureid++;
//			$sql = 'LOCK TABLES '.db_prefix( 'mechanicalturk' ).' WRITE;';
//			db_query( $sql );
//			$sql = "INSERT INTO ".db_prefix("mechanicalturk")."(creatureid,creaturename,creatureweapon,creaturewin,creaturelose,creaturelevel,forest,graveyard,submittedby)	VALUES ($creatureid,$creaturename,$creatureweapon,$creaturewin,$creaturelose,$creaturelevel,$forest,$graveyard,$submittedby)";
			$sql = "INSERT INTO ".db_prefix("mechanicalturk")." (creaturename,creatureweapon,creaturewin,creaturelose,creaturelevel,forest,graveyard,description,submittedby,uid) VALUES ('" . mysql_real_escape_string($creaturename) . "','" . mysql_real_escape_string($creatureweapon) . "','" . mysql_real_escape_string($creaturewin) . "','" . mysql_real_escape_string($creaturelose) . "','" . (int)$creaturelevel . "','" . (int)$forest . "','" . (int)$graveyard . "','" . mysql_real_escape_string($description) . "','".mysql_real_escape_string($submittedby)."',$uid)";
//			$result = db_query($sql);
			db_query( $sql );
			debug ($sql);
//			$sql = 'UNLOCK TABLES;';
//			db_query( $sql );
			output("`4The monster \"`^%s`4\" has been submitted.`n`nDue to the high volume of monster submissions, it may take several days or even weeks before you hear back from us.  Please be patient!`0`n`nThe little man behind the desk looks around, confused.  \"Who said that?!\"`n`nYou decide it'd be best to get out of here.", $creaturename );
			addnav("Back to the Jungle","forest.php");
			break;
		case "showsubmitted":
			$sql = "SELECT creatureid,creaturename,creatureweapon,creaturewin,creaturelose,creaturelevel,forest,graveyard,description,submittedby,uid FROM " . db_prefix("mechanicalturk");
			$result = db_query($sql);
			for ($i=0;$i<db_num_rows($result);$i++){
				$row=db_fetch_assoc($result);
				output("Monster submission by %s`n`n",$row['submittedby']);
				output("%s`n`n",stripslashes($row['description']));
				output("You have encountered %s which lunges at you with %s!`n`n",stripslashes($row['creaturename']),stripslashes($row['creatureweapon']));
				output("Creature win message: %s`n",stripslashes($row['creaturewin']));
				output("Creature lose message: %s`n",stripslashes($row['creaturelose']));
				output("Creature level: %s`n",$row['creaturelevel']);
				output("Jungle: %s - FailBoat: %s`n`n",$row['forest'],$row['graveyard']);
				rawoutput("<a href=\"runmodule.php?module=mechanicalturk&creatureaction=edit&id=".$row['creatureid']."\">Edit</a> | <a href=\"runmodule.php?module=mechanicalturk&creatureaction=reject&op=1&id=".$row['creatureid']."\">Reject nonspecifically</a> |  <a href=\"runmodule.php?module=mechanicalturk&creatureaction=reject&op=2&id=".$row['creatureid']."\">Reject because of writing</a> | <a href=\"runmodule.php?module=mechanicalturk&creatureaction=reject&op=3&id=".$row['creatureid']."\">Reject but ask for rewrite</a> | <a href=\"runmodule.php?module=mechanicalturk&creatureaction=reject&op=4&id=".$row['creatureid']."\">Reject because of pop culture refs</a> | <a href=\"runmodule.php?module=mechanicalturk&creatureaction=accept&id=".$row['creatureid']."\">Accept this monster</a>");
				addnav("", "runmodule.php?module=mechanicalturk&creatureaction=edit&id=".$row['creatureid']);
				addnav("", "runmodule.php?module=mechanicalturk&creatureaction=reject&op=1&id=".$row['creatureid']);
				addnav("", "runmodule.php?module=mechanicalturk&creatureaction=reject&op=2&id=".$row['creatureid']);
				addnav("", "runmodule.php?module=mechanicalturk&creatureaction=reject&op=3&id=".$row['creatureid']);
				addnav("", "runmodule.php?module=mechanicalturk&creatureaction=reject&op=4&id=".$row['creatureid']);
				addnav("", "runmodule.php?module=mechanicalturk&creatureaction=accept&id=".$row['creatureid']);
				output("`n`n====================`n`n");
			}
			addnav("Back to the Superuser grotto","superuser.php");
			break;
		case "edit":
			$id=httpget("id");
			require_once("lib/showform.php");
			addnav("Back to the Jungle","forest.php");
			$form = array(
				"Creature Properties,title",
				"creatureid"=>"Creature id,hidden",
				"creaturename"=>"Creature Name",
				"creatureweapon"=>"Weapon",
				"creaturewin"=>"Win Message (Displayed when the creature kills the player)",
				"creaturelose"=>"Death Message (Displayed when the creature is killed by the player)",
				// 18 to make a non-forest available monster
				// (ie, graveyard only)_
				"creaturelevel"=>"Level,range,1,18,1",
				"forest"=>"Creature is in Jungle?,bool",
				"graveyard"=>"Creature is on FailBoat?,bool",
				"description"=>"A long description of the creature",
			);
			$sql = "SELECT creatureid,creaturename,creatureweapon,creaturewin,creaturelose,creaturelevel,forest,graveyard,description,submittedby,uid FROM " . db_prefix("mechanicalturk") . " WHERE creatureid = $id";
			$result = db_query($sql);
			$row=db_fetch_assoc($result);
			debug ($row);
			$row['creaturename'] = stripslashes($row['creaturename']);
			$row['creatureweapon'] = stripslashes($row['creatureweapon']);
			$row['creaturewin'] = stripslashes($row['creaturewin']);
			$row['creaturelose'] = stripslashes($row['creaturelose']);
			$row['description'] = stripslashes($row['description']);
			rawoutput("<form action='runmodule.php?module=mechanicalturk&creatureaction=update' method='POST'>");
			showform($form, $row);
			rawoutput("</form>");
			addnav("", "runmodule.php?module=mechanicalturk&creatureaction=update");
			addnav("Back to the submission list","runmodule.php?module=mechanicalturk&creatureaction=showsubmitted");
			addnav("Back to the Superuser grotto","superuser.php");
			break;
		case "update":
			addnav("Back to the submission list","runmodule.php?module=mechanicalturk&creatureaction=showsubmitted");
			addnav("Back to the Superuser grotto","superuser.php");
			$creatureid = httppost('creatureid');
			$creaturename = httppost('creaturename');
			$creatureweapon = httppost('creatureweapon');
			$creaturewin = httppost('creaturewin');
			$creaturelose = httppost('creaturelose');
			$creaturelevel = httppost('creaturelevel');
			$forest = httppost('forest');
			$graveyard = httppost('graveyard');
			$description = httppost('description');
			$sql="UPDATE " . db_prefix("mechanicalturk") . " SET creaturename = '$creaturename', creatureweapon = '$creatureweapon', creaturewin = '$creaturewin', creaturelose = '$creaturelose', creaturelevel = '$creaturelevel', forest = $forest, graveyard = $graveyard, description = '$description' WHERE creatureid = $creatureid";
			db_query( $sql );
			debug ($sql);
			output("All done!");
			break;
		case "reject":
			$id=httpget("id");
			$op=httpget("op");
			$sql = "SELECT creatureid,creaturename,creatureweapon,creaturewin,creaturelose,creaturelevel,forest,graveyard,description,submittedby,uid FROM " . db_prefix("mechanicalturk") . " WHERE creatureid = $id";
			$result = db_query($sql);
			$row=db_fetch_assoc($result);
			if ($op==1){
				$message = translate_mail(array('It\'s not good news to hear, but I\'m afraid your monster idea (the one named %s) just wasn\'t what we were looking for.  Please feel free to try again with a new idea, though!  Please don\'t feel too bad, because less than thirty per cent of submissions are accepted.  For your reference, the creature description is as follows: %s',$row['creaturename'],$row['description']));
			};
			if ($op==2){
				$message = translate_mail(array('It\'s not good news to hear, but I\'m afraid your monster idea (the one named %s) was rejected because of spelling, grammar and/or flow issues.  We heartily recommend taking a look at Elements of Style (Google it).  Spending the half an hour it takes to read it will permanently improve your writing skills, and it can also be read for free online, so we think it\'s a very good deal!  Please don\'t feel too bad, because less than thirty per cent of submissions are accepted.  For your reference, the creature description is as follows: %s',$row['creaturename'],$row['description']));
			};
			if ($op==3){
				$message = translate_mail(array('It\'s not good news to hear, but I\'m afraid your monster idea (the one named %s) was rejected.  But it\'s not all bad news - in this case, we thought your monster idea had potential, and would like to see it rewritten and expanded upon, perhaps with a longer or more detailed description.  So please feel free to edit it and resubmit!  Please don\'t feel too bad, because less than thirty per cent of submissions are accepted.  For your reference, the creature description is as follows: %s',$row['creaturename'],$row['description']));
			};
			if ($op==4){
				$message = translate_mail(array('It\'s not good news to hear, but I\'m afraid your monster idea (the one named %s) was rejected because it contained a pop culture reference that was either already done to death, too obscure or not funny enough.  Remember, although pop culture references aren\'t specifically disallowed, they must be approximately fifty per cent more awesome than other entries that do not contain pop culture references.  Please don\'t feel too bad, because less than thirty per cent of submissions are accepted.  For your reference, the creature description is as follows: %s',$row['creaturename'],$row['description']));
			};
			require_once("lib/systemmail.php");
			systemmail($row['uid'],"Your monster has been rejected!",$message);
			$sql = "DELETE FROM " . db_prefix("mechanicalturk") . " WHERE creatureid = '$id'";
			db_query( $sql );
			output("The monster has been deleted, and the author notified.");
			addnav("Show list of submitted monsters","runmodule.php?module=mechanicalturk&creatureaction=showsubmitted");
			break;
		case "accept":
			$id=httpget("id");
			$sql = "SELECT creaturename,creatureweapon,creaturewin,creaturelose,creaturelevel,forest,graveyard,description,submittedby,uid FROM " . db_prefix("mechanicalturk") . " WHERE creatureid = $id";
			$result = db_query($sql);
			$row=db_fetch_assoc($result);
			debug ($row);
			$row['creaturename'] = stripslashes($row['creaturename']);
			$row['creatureweapon'] = stripslashes($row['creatureweapon']);
			$row['creaturewin'] = stripslashes($row['creaturewin']);
			$row['creaturelose'] = stripslashes($row['creaturelose']);
			$row['description'] = stripslashes($row['description']);
			output("Sending this to creatures.php.");
			require_once("lib/showform.php");
			$form = array(
				"Creature Properties,title",
				"creatureid"=>"Creature id,hidden",
				"creaturename"=>"Creature Name",
				"creatureweapon"=>"Weapon",
				"creaturewin"=>"Win Message (Displayed when the creature kills the player)",
				"creaturelose"=>"Death Message (Displayed when the creature is killed by the player)",
				// 18 to make a non-forest available monster
				// (ie, graveyard only)_
				"creaturelevel"=>"Level,range,1,18,1",
				"forest"=>"Creature is in forest?,bool",
				"graveyard"=>"Creature is in graveyard?,bool",
				"creatureaiscript"=>"Creature's A.I.,textarearesizeable",
			);
			rawoutput("<form action='creatures.php?op=save' method='POST'>");
			showform($form, $row);
			rawoutput("</form>");
			output("Monster description:`n`n");
			rawoutput("".$row['description']."");
			output("`n`n`bCOPY THIS TO YOUR CLIPBOARD NOW.`b  The Description is not automated and must be input manually.");
			$message = translate_mail(array('Congratulations!  Your monster idea (the one named %s) has been accepted!  Your Donator Points have also been applied to your account.  Enjoy them!',$row['creaturename']));
			require_once("lib/systemmail.php");
			systemmail($row['uid'],"Your monster idea has been accepted!",$message);
			addnav("","creatures.php?op=save");
			$acctid = $row['uid'];
			addnav("Go back to the list of submitted monsters","runmodule.php?module=mechanicalturk&creatureaction=showsubmitted");
			$sql="UPDATE ".db_prefix("accounts")." SET donation=donation+$points WHERE acctid=$acctid";
			db_query($sql);
			$sql = "DELETE FROM " . db_prefix("mechanicalturk") . " WHERE creatureid = '$id'";
			db_query($sql);
			break;
	}
	page_footer();
}
?>