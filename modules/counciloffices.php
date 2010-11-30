<?php

function counciloffices_getmoduleinfo(){
	$info = array(
		"name"=>"Council Offices",
		"author"=>"Cousjava",
		"version"=>"2010-11-30",
		"category"=>"Council Offices",
		"download"=>"",
	);
	return $info;
}

function counciloffices_install(){
	module_addhook("village");
	module_addhook("onslaught");
	return true;
}

function counciloffices_uninstall(){
	return true;
}

function counciloffices_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "village":
			tlschema($args['schemas']['fightnav']);
			addnav($args['fightnav']);
			tlschema();
			addnav("Council Offices","runmodule.php?module=counciloffices&councilop=enter");
			break;
		case "onslaught":break;
	}
	return $args;
}

function counciloffices_run(){
	global $session;
	page_header("Council Offices");
	switch (httpget('councilop')){
		case "enter":
			switch ($session['user']['location']){
				case "NewHome":
					output("The \"Council Offices\" of this Outpost amount to a tiny hut with a man inside reading a newspaper behind a desk.  He looks up as you come in.`n`n\"`1Can I help you?`0\"`n`n");
					break;
				case "Kittania":
					output("The \"Council Offices\" of this Outpost amount to a tiny hut with a patient-looking KittyMorph sat behind a desk inside.  She looks up as you come in.`n`n\"`1What can I do for you?`0\"`n`n");
					break;
				case "New Pittsburgh":
					output("The \"Council Offices\" of this Outpost amount to a tiny hut with a patient-looking Zombie sat behind a desk inside.  She looks up as you come in.`n`n\"`1What can I do for you?`0\"`n`n");
					break;
				case "Squat Hole":
					output("You step into the dilapidated Council Offices.  For a moment, you believe yourself to be alone; then, you notice the shining bald head sat behind the desk.  A squeaky voice shouts \"`1Y'arright there chuck, what d'ya want?`0\"`n`n");
					break;
				case "Pleasantville":
					output("The \"Council Offices\" of this Outpost amount to a tiny hut with a patient-looking Mutant sat behind a desk inside.  He looks up as you come in.`n`n\"`1What can I do for you?`0\"`n`n");
					break;
				case "Cyber City 404":
					output("The \"Council Offices\" of this Outpost amount to a tiny hut with a stern-looking Robot sat behind a desk inside.  He looks up as you come in.`n`n\"`1State your request.`0\"`n`n");
					break;
				case "AceHigh":
					output("The \"Council Offices\" of this Outpost amount to a tiny hut with an immaculately-dressed woman sat reading a newspaper behind a desk.  She looks up as you come in, eyes giving off a faint green glow.`n`n\"`1What can I do for you?`0\"`n`n");
					break;
				case "Improbable Central":
					output("The \"Council Offices\" of this Outpost amount to a tiny hut with a man inside reading a newspaper behind a desk.  He looks up as you come in.`n`n\"`1Can I help you?`0\"`n`n");
					break;
				default: 
					output("The \"Council Offices\" is really just one office, containing a bored-looking man. `1\"What can I do for you?\" he says.`n`n");
					break;
			}
			addnav("State your business.");
			addnav("You know, I don't have a clue what I came in here for.  Back to the Outpost.","village.php");
			addnav("What's the monster situation like here?","runmodule.php?module=counciloffices&councilop=monster");
			addnav("Where's worst off?","runmodule.php?module=counciloffices&councilop=maxmonsters");
			break;
		case "monster":
		require_once("modules/onslaught.php");
		require_once("modules/cityprefs/lib.php");
		/*if ($cid=="none"){
		$cid = get_cityprefs_cityid("location",$session['user']['location']);
		}
		$sql = "SELECT value FROM ".db_prefix("module_objprefs")." WHERE modulename='onslaught' AND objid=$cid";
		$res = db_query($sql);
		$row = db_fetch_assoc($res);
		$localmonsters = $row['value'];*/
		$localmonsters = counciloffices_localmonsters();
			switch ($session['user']['location']){
				case "NewHome":					
					output("The human looks at a sheet of paper in front of him. \"`1Ah, yes,`0\" he says. \"`1There are about %s monsters around here.`0\"`n`n",e_rand($localmonsters*0.9,$localmonsters*1.1));
					break;
				case "Kittania":
					output("The kittymorph pauses a moment. \"`1That's it?`0\" she says. \"`1Oh, there are about %s monsters around,`0\" quite possibly just pulling the number from the top of her head.`n`n",e_rand($localmonsters*0.8,$localmonsters*1.2));
					break;
				case "New Pittsburgh":
					output("The zombie smiles at you. \"`1There are about %s BRAAAAIIIINS waiting to be eaten out there.`0\"`n`n",e_rand($localmonsters*0.9,$localmonsters*1.1));
					break;
				case "Squat Hole":
					output("\"`1Why shud I care?`0\" come the answer. \"`1Mi' be 'round %s, dick'ead.`n`n",e_rand($localmonsters*0.8,$localmonsters*1.2));
					break;
				case "Pleasantville":
					output("The mutant sighs. \"`1There are %s monsters out there. It proves once and for all we're doomed, all doomed.`0\"`n`n",e_rand($localmonsters*0.9,$localmonsters*1.1));
					break;
				case "Cyber City 404":
					output("The robot checks a scanner.\"`1There are %s monsters in this area, with a margin of 5 per cent`0\"`n`n",e_rand($localmonsters*0.95,$localmonsters*1.05));
					break;
				case "AceHigh":
					output("The joker nods in response to your question, and `gvanishes`0 in a puff of green smoke. You think you perhaps should leave, but a moment later she reappears. There is some blood on here which you are `isure`i wasn't there before.\"`1There are %s monsters herabouts,`0\" she says.\"`1Currently.\"`n`n",e_rand($localmonsters*0.9,$localmonsters*1.1));
					break;
				case "Improbable Central":
					output("The man sighs, folds up his newspaper and disappears through a door benind the desk. A moment later he comes out again, and gives the answer.\"`1There are about %s monsters out there. Okay?`0\" He sits down again and reopens the newspaper, which you notice is last week's `iEnquirer`i, in which he seems to be doing the crossword.`n`n",e_rand($localmonsters*0.9,$localmonsters*1.1));
					break;
			}
			addnav("Okay");
			addnav("Stay in offices","runmodule.php?module=counciloffices&councilop=stay");
			addnav("Return to outpost","village.php");
			//addnav("Where's worst off?","runmodule.php?module=counciloffices&councilop=maxmonsters");
			break;
		case "stay":
			addnav("What now?");
			addnav("Return to Outpost","village.php");
			addnav("What's the monster situation like here?","runmodule.php?module=counciloffices&councilop=monster");
			addnav("Where's worst off?","runmodule.php?module=counciloffices&councilop=maxmonsters");
			break;
		case "maxmonsters":
			addnav("What now?");
			addnav("Stay here","runmodule.php?module=counciloffices&councilop=stay");
			addnav("Return to Outpost","village.php");
			$maxn = counciloffices_maxmonsters();
			$maxcity = $maxn["city"];
			$maxmonsters = e_rand($maxn["monsters"]*0.9,$maxn["monsters"]*1.1);
			
			
			switch ($session['user']['location']){
				case "NewHome":					
					output("The man looks down at one of the many sheets of paper on his desk. His brow creases in a worried frown. \"`1%s is in quite a bit a danger,`0\" he says. \"`1There are %s monsters out there. It would be useful if you went and helped out, if you feel able to travel.`n`n",$maxcity,$maxmonsters);
					break;
				case "Kittania":
					output("The kittymorph looks at you. \"`1I've heard that %s is in a bit of trouble,`0\" she says. \"`1What with so many monsters around. I've heard there are %s out there!`0\" `n`n",$maxcity,$maxmonsters);
					break;
				case "New Pittsburgh":
					output("\"`1There are %s BRAAIINS waiting ot be eaten outside %s,`0\" the zombie says with a hint of bitterness.`n`n",$maxmonsters,$maxcity);
					break;
				case "Squat Hole":
					output("\"`1I dunno 'bout others. 'Eard in the pub t'at %s has %s monsters.`0\"`n`n",$maxcity,$maxmonsters);
					break;
				case "Pleasantville":
					output("The mutant looks a bit woried. at your request, but then it lookied woried before. \"`1There are %s monsters attacking %s. It will fall. They always do.`0\"`n`n",$maxmonsters,$maxcity);
					break;
				case "Cyber City 404":
					output("The robot checks a panel of scanners and performs several thousand calculations in the next millisecond before answering.\"`1There are %s monsters in the area of %s with an error margin of 10 per cent.`0\"`n`n",$maxmonsters,$maxcity);
					break;
				case "AceHigh":
					output("The joker smiles. \"`1Oh is that all?`0\" she says and `gvanishes`0. She is gone quite a while, and you turn to the door to leave when she reappears.\"`1 %s is what I think is in the most danger. I reckon there are %s monsters out there.`0\" It might not be good idea to know `ihow`i she got that count, so you make your olite thanks and leave.`n`n");
					break;
				case "Improbable Central":
					output("The man, without turning from his newspaper, tells you \"there are %s monsters attacking %s, according to the latest reports,`0\" which you presume are in the newspaper. The man carries on in a quieter voice with the crossword. \"`1Nine letters, friends you have not met, first letter S. Hmm.`0\"`n`n");//STRANGERS
					break;
				default:
				output("\"`1Oh dear, %s seems to be in quite a bit of trouble, there are %s monsters there.`0\"`n`n",$maxcity,$maxmonsters);
			}
			break;
			//for debugging purposes only, delete later
			if ($maxn["city"]==null)
			{
				output("maxncity was null");
			}
			if ($maxn["city"]=="none")
			{
				output("maxnxcity was none");
			}
			//output("maxncity with double is %s.",$maxn["city"]);
			//output("maxncity with single is %s",$maxn['city']);
			break;
	}
	modulehook("counciloffices");
	page_footer();
}

function counciloffices_localmonsters($cid="none"){
	global $session;
	require_once "modules/cityprefs/lib.php";
	if ($cid=="none"){
		$cid = get_cityprefs_cityid("location",$session['user']['location']);
	}
	$creatures = get_module_objpref("city",$cid,"creatures","onslaught");
	return $creatures;
}

function counciloffices_maxmonsters(){
	global $session;
	require_once "modules/cityprefs/lib.php";
	if ($cid=="none"){
		$cid = get_cityprefs_cityid("location",$session['user']['location']);
	}
	$sql = "SELECT * FROM ".db_prefix("module_objprefs")." WHERE modulename='onslaught' AND setting='creatures' ORDER BY value DESC LIMIT 1";
	$result=mysql_query($sql);
	//$cid=mysql_result($result,0,"objid");
	$maxmonsters=mysql_result($result,0,"value");
	$cid=mysql_result($result,0,"objid");
	//$maxcname=get_cityprefs_cityname('cityprefs',$cid);
	$maxcname=counciloffices_getcityname($cid);
	//FOR DEBUGGING
	$maxm = array("monsters"=>$maxmonsters,"city"=>$maxcname);
	return $maxm;
}

function counciloffices_getcityname($cid)
{
	$where=$cid;
	$sql="SELECT * FROM ".db_prefix("cityprefs")." WHERE cityid=\"$where\"";	
	$result=mysql_query($sql);
	$num=mysql_num_rows($result);	
	$res=mysql_result($result,0,'cityname');	
	return $res;
	
}
?>
=======
<?php

function counciloffices_getmoduleinfo(){
	$info = array(
		"name"=>"Council Offices",
		"author"=>"Cousjava",
		"version"=>"2010-11-29",
		"category"=>"Council Offices",
		"download"=>"",
	);
	return $info;
}

function counciloffices_install(){
	module_addhook("village");
	return true;
}

function counciloffices_uninstall(){
	return true;
}

function counciloffices_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "village":
			tlschema($args['schemas']['fightnav']);
			addnav($args['fightnav']);
			tlschema();
			addnav("Council Offices","runmodule.php?module=counciloffices&councilop=enter");
			break;
	}
	return $args;
}

function counciloffices_run(){
	global $session;
	page_header("Council Offices");
	switch (httpget('councilop')){
		case "enter":
			switch ($session['user']['location']){
				case "NewHome":
					output("The \"Council Offices\" of this Outpost amount to a tiny hut with a man inside reading a newspaper behind a desk.  He looks up as you come in.`n`n\"`1Can I help you?`0\"`n`n");
					break;
				case "Kittania":
					output("The \"Council Offices\" of this Outpost amount to a tiny hut with a patient-looking KittyMorph sat behind a desk inside.  She looks up as you come in.`n`n\"`1What can I do for you?`0\"`n`n");
					break;
				case "New Pittsburgh":
					output("The \"Council Offices\" of this Outpost amount to a tiny hut with a patient-looking Zombie sat behind a desk inside.  She looks up as you come in.`n`n\"`1What can I do for you?`0\"`n`n");
					break;
				case "Squat Hole":
					output("You step into the dilapidated Council Offices.  For a moment, you believe yourself to be alone; then, you notice the shining bald head sat behind the desk.  A squeaky voice shouts \"`1Y'arright there chuck, what d'ya want?`0\"`n`n");
					break;
				case "Pleasantville":
					output("The \"Council Offices\" of this Outpost amount to a tiny hut with a patient-looking Mutant sat behind a desk inside.  He looks up as you come in.`n`n\"`1What can I do for you?`0\"`n`n");
					break;
				case "Cyber City 404":
					output("The \"Council Offices\" of this Outpost amount to a tiny hut with a stern-looking Robot sat behind a desk inside.  He looks up as you come in.`n`n\"`1State your request.`0\"`n`n");
					break;
				case "AceHigh":
					output("The \"Council Offices\" of this Outpost amount to a tiny hut with an immaculately-dressed woman sat reading a newspaper behind a desk.  She looks up as you come in, eyes giving off a faint green glow.`n`n\"`1What can I do for you?`0\"`n`n");
					break;
				case "Improbable Central":
					output("The \"Council Offices\" of this Outpost amount to a tiny hut with a man inside reading a newspaper behind a desk.  He looks up as you come in.`n`n\"`1Can I help you?`0\"`n`n");
					break;
			}
			addnav("State your business.");
			addnav("You know, I don't have a clue what I came in here for.  Back to the Outpost.","village.php");
			addnav("What's the monster situation like here?","runmodule.php?module=counciloffices&councilop=monster");
			addnav("Where's worst off?","runmodule.php?module=counciloffices&councilop=maxmonsters");
			break;
		case "monster":
		require_once("modules/onslaught.php");
		require_once("modules/cityprefs/lib.php");
		/*if ($cid=="none"){
		$cid = get_cityprefs_cityid("location",$session['user']['location']);
		}
		$sql = "SELECT value FROM ".db_prefix("module_objprefs")." WHERE modulename='onslaught' AND objid=$cid";
		$res = db_query($sql);
		$row = db_fetch_assoc($res);
		$localmonsters = $row['value'];*/
		$localmonsters = counciloffices_localmonsters();
			switch ($session['user']['location']){
				case "NewHome":					
					output("The human looks at a sheet of paper in front of him. \"`1Ah, yes,`0\" he says. \"`1There are about %s monsters around here.`0\"`n`n",e_rand($localmonsters*0.9,$localmonsters*1.1));
					break;
				case "Kittania":
					output("The kittymorph pauses a moment. \"`1That's it?`0\" she says. \"`1Oh, there are about %s monsters around,`0\" quite possibly just pulling the number from the top of her head.`n`n",e_rand($localmonsters*0.8,$localmonsters*1.2));
					break;
				case "New Pittsburgh":
					output("The zombie smiles at you. \"`1There are about %s BRAAAAIIIINS waiting to be eaten out there.`0\"`n`n",e_rand($localmonsters*0.9,$localmonsters*1.1));
					break;
				case "Squat Hole":
					output("\"`1Why shud I care?`0\" come the answer. \"`1Mi' be 'round %s, dick'ead.`n`n",e_rand($localmonsters*0.8,$localmonsters*1.2));
					break;
				case "Pleasantville":
					output("The mutant sighs. \"`1There are %s monsters out there. It proves once and for all we're doomed, all doomed.`0\"`n`n",e_rand($localmonsters*0.9,$localmonsters*1.1));
					break;
				case "Cyber City 404":
					output("The robot checks a scanner.\"`1There are %s monsters in this area, with a margin of 5 per cent`0\"`n`n",e_rand($localmonsters*0.95,$localmonsters*1.05));
					break;
				case "AceHigh":
					output("The joker nods in response to your question, and `gvanishes`0 in a puff of green smoke. You think you perhaps should leave, but a moment later she reappears. There is some blood on here which you are `isure`i wasn't there before.\"`1There are %s monsters herabouts,`0\" she says.\"`1Currently.\"`n`n",e_rand($localmonsters*0.9,$localmonsters*1.1));
					break;
				case "Improbable Central":
					output("The man sighs, folds up his newspaper and disappears through a door benind the desk. A moment later he comes out again, and gives the answer.\"`1There are about %s monsters out there. Okay?`0\" He sits down again and reopens the newspaper, which you notice is last week's `iEnquirer`i, in which he seems to be doing the crossword.`n`n",e_rand($localmonsters*0.9,$localmonsters*1.1));
					break;
			}
			addnav("Okay");
			addnav("Stay in offices","runmodule.php?module=counciloffices&councilop=stay");
			addnav("Return to outpost","village.php");
			//addnav("Where's worst off?","runmodule.php?module=counciloffices&councilop=maxmonsters");
			break;
		case "stay":
			addnav("What now?");
			addnav("Return to Outpost","village.php");
			addnav("What's the monster situation like here?","runmodule.php?module=counciloffices&councilop=monster");
			addnav("Where's worst off?","runmodule.php?module=counciloffices&councilop=maxmonsters");
			break;
		case "maxmonsters":
			addnav("What now?");
			addnav("Stay here","runmodule.php?module=counciloffices&councilop=stay");
			addnav("Return to Outpost","village.php");
			$maxn = counciloffices_maxmonsters();
			$maxcity = $maxn["city"];
			$maxmonsters = e_rand($maxn["monsters"]*0.9,$maxn["monsters"]*1.1);
			
			
			switch ($session['user']['location']){
				case "NewHome":					
					output("The man looks down at one of the many sheets of paper on his desk. His brow creases in a worried frown. \"`1%s is in quite a bit a danger,`0\" he says. \"`1There are %s monsters out there. It would be useful if you went and helped out, if you feel able to travel.`n`n",$maxcity,$maxmonsters);
					break;
				case "Kittania":
					output("The kittymorph looks at you. \"`1I've heard that %s is in a bit of trouble,`0\" she says. \"`1What with so many monsters around. I've heard there are %s out there!`0\" `n`n",$maxcity,$maxmonsters);
					break;
				case "New Pittsburgh":
					output("\"`1There are %s BRAAIINS waiting ot be eaten outside %s,`0\" the zombie says with a hint of bitterness.`n`n",$maxmonsters,$maxcity);
					break;
				case "Squat Hole":
					output("\"`1I dunno 'bout others. 'Eard in the pub t'at %s has %s monsters.`0\"`n`n",$maxcity,$maxmonsters);
					break;
				case "Pleasantville":
					output("The mutant looks a bit woried. at your request, but then it lookied woried before. \"`1There are %s monsters attacking %s. It will fall. They always do.`0\"`n`n",$maxmonsters,$maxcity);
					break;
				case "Cyber City 404":
					output("The robot checks a panel of scanners and performs several thousand calculations in the next millisecond before answering.\"`1There are %s monsters in the area of %s with an error margin of 10 per cent.`0\"`n`n",$maxmonsters,$maxcity);
					break;
				case "AceHigh":
					output("The joker smiles. \"`1Oh is that all?`0\" she says and `gvanishes`0. She is gone quite a while, and you turn to the door to leave when she reappears.\"`1 %s is what I think is in the most danger. I reckon there are %s monsters out there.`0\" It might not be good idea to know `ihow`i she got that count, so you make your olite thanks and leave.`n`n");
					break;
				case "Improbable Central":
					output("The man, without turning from his newspaper, tells you \"there are %s monsters attacking %s, according to the latest reports,`0\" which you presume are in the newspaper. The man carries on in a quieter voice with the crossword. \"`1Nine letters, friends you have not met, first letter S. Hmm.`0\"`n`n");//STRANGERS
					break;
				default:
				output("\"`1Oh dear, %s seems to be in quite a bit of trouble, there are %s monsters there.`0\"`n`n",$maxcity,$maxmonsters);
			}
			break;
			//for debugging purposes only, delete later
			if ($maxn["city"]==null)
			{
				output("maxncity was null");
			}
			if ($maxn["city"]=="none")
			{
				output("maxnxcity was none");
			}
			//output("maxncity with double is %s.",$maxn["city"]);
			//output("maxncity with single is %s",$maxn['city']);
			break;
	}
	modulehook("counciloffices");
	page_footer();
}

function counciloffices_localmonsters($cid="none"){
	global $session;
	require_once "modules/cityprefs/lib.php";
	if ($cid=="none"){
		$cid = get_cityprefs_cityid("location",$session['user']['location']);
	}
	$creatures = get_module_objpref("city",$cid,"creatures","onslaught");
	return $creatures;
}

function counciloffices_maxmonsters(){
	global $session;
	require_once "modules/cityprefs/lib.php";
	if ($cid=="none"){
		$cid = get_cityprefs_cityid("location",$session['user']['location']);
	}
	$sql = "SELECT * FROM ".db_prefix("module_objprefs")." WHERE modulename='onslaught' AND setting='creatures' ORDER BY value DESC LIMIT 1";
	$result=mysql_query($sql);
	//$cid=mysql_result($result,0,"objid");
	$maxmonsters=mysql_result($result,0,"value");
	$cid=mysql_result($result,0,"objid");
	//$maxcname=get_cityprefs_cityname('cityprefs',$cid);
	$maxcname=counciloffices_getcityname($cid);
	//FOR DEBUGGING
	$maxm = array("monsters"=>$maxmonsters,"city"=>$maxcname);
	return $maxm;
}

function counciloffices_getcityname($cid)
{
	$where=$cid;
	$sql="SELECT * FROM ".db_prefix("cityprefs")." WHERE cityid=\"$where\"";	
	$result=mysql_query($sql);
	$num=mysql_num_rows($result);	
	$res=mysql_result($result,0,'cityname');	
	return $res;
	
}
?>
>>>>>>> 8b5d92281350005db7709911b00777e80705dd6e
