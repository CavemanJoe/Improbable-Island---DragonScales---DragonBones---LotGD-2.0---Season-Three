<?php
// translator ready
// addnews ready
// mail ready
require_once("common.php");
require_once("lib/commentary.php");
require_once("lib/http.php");
require_once("lib/events.php");
require_once("lib/experience.php");

tlschema('village');
//mass_module_prepare(array("village","validlocation","villagetext","village-desc"));
// See if the user is in a valid location and if not, put them back to
// a place which is valid
$valid_loc = array();
$vname = getsetting("villagename", LOCATION_FIELDS);
$iname = getsetting("innname", LOCATION_INN);
$valid_loc[$vname]="village";
$valid_loc = modulehook("validlocation", $valid_loc);
if (!isset($valid_loc[$session['user']['location']])) {
	$session['user']['location']=$vname;
}

// $newestname = "";
// $newestplayer = getsetting("newestplayer", "");
// if ($newestplayer == $session['user']['acctid']) {
	// $newtext = "`nYou're the newest member of the village.  As such, you wander around, gaping at the sights, and generally looking lost.";
	// $newestname = $session['user']['name'];
// } else {
	// $newtext = "`n`2Wandering near the inn is `&%s`2, looking completely lost.";
	// if ((int)$newestplayer != 0) {
		// $sql = "SELECT name FROM " . db_prefix("accounts") . " WHERE acctid='$newestplayer'";
		// //$result = db_query_cached($sql, "newest");
		// $result = db_query_cached($sql,"playernames/playername_".$newestplayer);
		// if (db_num_rows($result) == 1) {
			// $row = db_fetch_assoc($result);
			// $newestname = $row['name'];
		// } else {
			// $newestplayer = "";
		// }
	// } else {
		// if ($newestplayer > "") {
			// $newestname = $newestplayer;
		// } else {
			// $newestname = "";
		// }
	// }
// }

// if (!$session['user']['dragonkills'] && !$session['user']['experience'] && $session['user']['hitpoints'] == $session['user']['maxhitpoints'] && $session['user']['age'] < 2 && $session['user']['gold']==80){
	// output("`J`bConfused?`b`nDon't worry.  A lot of new players tend to react to NewHome with cries of 'LOOK AT ALL THOSE LINKS HOLY BALLS WHAT DO I DO,' so you're in good company.  First, you should head into the Council Offices and grab your free stuff for today.  After that, there's no easy answer to the question of 'what do I do' - but we can give you some hints.`n`nIf you're the sort who enjoys beating up monsters, levelling up, inching your way up the Hall O' Fame, you might want to check out the Jungle.  It'd be a good idea to visit Sheila's Shack O' Shiny first, and get yourself some sort of weapon or, y'know, clothing.`nIf you enjoy exploration, there's an awful lot of stuff you can see on the World Map, a lot of which was built by our own players.  Some folks like to just wander around gawping at all the interesting buildings that people have put together.  A can of Monster Repellent Spray from eBoy's Trading Station will make you less likely to be set upon by monsters while you're out looking around.`nIf you want to know more about the world in which Improbable Island is set, or if you're more in the mood to be told a story or to play a quest than to leap straight towards the levelling-up, then the Museum is a good bet.`nIf the accumulation of wealth is what interests you, then it's worth pointing out that the prices at eBoy's Trading Station are different in every Outpost and affected by supply and demand, so it's possible to make a tidy profit buying low and selling high.`nAre you the kind of player who believes that playing with other human beings can be far more fun?  You're absolutely right.  Introduce yourself in the Banter Channel - as web games go, Improbable Island's players are unusually friendly.`nRemember, you can always revisit Basic Training from NewHome if you need to, and don't hesitate to ask questions in the Banter channel or Location Four.`nThis message will disappear after you fight a monster, spend or earn some Requisition, or trigger a new Game Day.`n`n");
// }

$basetext = array(
	"`@`c`b%s Square`b`cThe village of %s hustles and bustles.  No one really notices that you're standing there.  ".
	"You see various shops and businesses along main street.  There is a curious looking rock to one side.  ".
	"On every side the village is surrounded by deep dark forest.`n`n",$vname,$vname
	);
$origtexts = array(
	"text"=>$basetext,
	"clock"=>"The clock on the inn reads `^%s`@.`n",
	"title"=>array("%s Square", $vname),
	"talk"=>"`n`%`@Nearby some villagers talk:`n",
	"sayline"=>"says",
	"newest"=>$newtext,
	"newestplayer"=>$newestname,
	"newestid"=>$newestplayer,
	"gatenav"=>"City Gates",
	"fightnav"=>"Blades Boulevard",
	"marketnav"=>"Market Street",
	"tavernnav"=>"Tavern Street",
	"infonav"=>"Info",
	"othernav"=>"Other",
	"section"=>"village",
	"innname"=>$iname,
	"stablename"=>"Mike's Chop Shop",
	"mercenarycamp"=>"Mercenary Camp",
	"armorshop"=>"Pegasus Armor",
	"weaponshop"=>"MightyE's Weaponry"
	);
$schemas = array(
	"text"=>"village",
	"clock"=>"village",
	"title"=>"village",
	"talk"=>"village",
	"sayline"=>"village",
	"newest"=>"village",
	"newestplayer"=>"village",
	"newestid"=>"village",
	"gatenav"=>"village",
	"fightnav"=>"village",
	"marketnav"=>"village",
	"tavernnav"=>"village",
	"infonav"=>"village",
	"othernav"=>"village",
	"section"=>"village",
	"innname"=>"village",
	"stablename"=>"village",
	"mercenarycamp"=>"village",
	"armorshop"=>"village",
	"weaponshop"=>"village"
	);
// Now store the schemas
$origtexts['schemas'] = $schemas;

// don't hook on to this text for your standard modules please, use "village"
// instead.
// This hook is specifically to allow modules that do other villages to create
// ambience.
$texts = modulehook("villagetext",$origtexts);
//and now a special hook for the village
//$texts = modulehook("villagetext-{$session['user']['location']}",$texts);
$schemas = $texts['schemas'];

tlschema($schemas['title']);
page_header($texts['title']);
tlschema();

addcommentary();
$skipvillagedesc = handle_event("village");
checkday();

if ($session['user']['slaydragon'] == 1) {
	$session['user']['slaydragon'] = 0;
}


if ($session['user']['alive']){ }else{
	redirect("shades.php","Player in village but not alive");
}

if (getsetting("automaster",1) && $session['user']['seenmaster']!=1){
	//masters hunt down truant students
	$level = $session['user']['level']+1;
	$dks = $session['user']['dragonkills'];
	$expreqd = exp_for_next_level($level, $dks);
	if ($session['user']['experience']>$expreqd &&
			$session['user']['level']<15){
		redirect("train.php?op=autochallenge","Master auto-challenge");
	}
}

$op = httpget('op');
$com = httpget('comscroll');
$refresh = httpget("refresh");
$commenting = httpget("commenting");
$comment = httppost('insertcommentary');
// Don't give people a chance at a special event if they are just browsing
// the commentary (or talking) or dealing with any of the hooks in the village.
if (!$op && $com=="" && !$comment && !$refresh && !$commenting) {
	// The '1' should really be sysadmin customizable.
	if (module_events("village", getsetting("villagechance", 0)) != 0) {
		if (checknavs()) {
			page_footer();
		} else {
			// Reset the special for good.
			$session['user']['specialinc'] = "";
			$session['user']['specialmisc'] = "";
			$skipvillagedesc=true;
			$op = "";
			httpset("op", "");
		}
	}
}

tlschema($schemas['gatenav']);
addnav($texts['gatenav']);
tlschema();

addnav("F?Forest","forest.php");
if (getsetting("pvp",1)){
	addnav("S?Slay Other Players","pvp.php");
}
addnav("Q?`%Quit`0 to the fields","login.php?op=logout",true);
if (getsetting("enablecompanions",true)) {
	tlschema($schemas['mercenarycamp']);
	addnav($texts['mercenarycamp'], "mercenarycamp.php");
	tlschema();
}

tlschema($schemas['fightnav']);
addnav($texts['fightnav']);
tlschema();
addnav("D?Joe's Dojo","train.php");
if (@file_exists("lodge.php")) {
	addnav("H?The Hunter's Lodge","lodge.php");
}

tlschema($schemas['marketnav']);
addnav($texts['marketnav']);
tlschema();
//tlschema($schemas['weaponshop']);
//addnav("W?".$texts['weaponshop'],"weapons.php");
//tlschema();
//tlschema($schemas['armorshop']);
//addnav("A?".$texts['armorshop'],"armor.php");
tlschema();
addnav("B?Bank of Improbable","bank.php");
addnav("Comms Tent","gypsy.php");
if (getsetting("betaperplayer", 1) == 1 && @file_exists("pavilion.php")) {
	addnav("E?Eye-catching Pavilion","pavilion.php");
}

tlschema($schemas['tavernnav']);
addnav($texts['tavernnav']);
tlschema();
tlschema($schemas['innname']);
addnav("I?".$texts['innname']."`0","inn.php",true);
tlschema();
tlschema($schemas['stablename']);
addnav("M?".$texts['stablename']."`0","stables.php");
tlschema();

addnav("Common Ground", "gardens.php");
addnav("R?Curious Looking Rock", "rock.php");
if (getsetting("allowclans",1)) addnav("C?Clan Halls","clan.php");

tlschema($schemas['infonav']);
addnav($texts['infonav']);
tlschema();
addnav("N?Daily News","news.php");
addnav("L?List Contestants","list.php");
addnav("o?Hall o' Fame","hof.php");

tlschema($schemas['othernav']);
addnav($texts['othernav']);
tlschema();
addnav("P?Preferences","prefs.php");
if (!file_exists("lodge.php")) {
	addnav("Refer a Friend", "referral.php");
}

tlschema('nav');
addnav("Superuser");
if ($session['user']['superuser'] & SU_EDIT_COMMENTS){
	addnav(",?Comment Moderation","moderate.php");
}
if ($session['user']['superuser']&~SU_DOESNT_GIVE_GROTTO){
  addnav("X?`bSuperuser Grotto`b","superuser.php");
}
if ($session['user']['superuser'] & SU_INFINITE_DAYS){
  addnav("/?New Day","newday.php");
}
tlschema();

if (!$skipvillagedesc) {
	//debug($texts);
	//modulehook("collapse{", array("name"=>"villagedesc-".$session['user']['location']));
	tlschema($schemas['text']);
	output($texts['text']);
	tlschema();
	//modulehook("}collapse");
	//modulehook("collapse{", array("name"=>"villageclock-".$session['user']['location']));
	tlschema($schemas['clock']);
	output($texts['clock'],getgametime());
	tlschema();
	//modulehook("}collapse");
	modulehook("village-desc",$texts);
	//support for a special village-only hook
	//modulehook("village-desc-{$session['user']['location']}",$texts);
	if ($texts['newestplayer'] > "" && $texts['newest']) {
		//modulehook("collapse{", array("name"=>"villagenewest-".$session['user']['location']));
		tlschema($schemas['newest']);
		output($texts['newest'], $texts['newestplayer']);
		tlschema();
		$id = $texts['newestid'];
		if ($session['user']['superuser'] & SU_EDIT_USERS && $id) {
			$edit = translate_inline("Edit");
			rawoutput(" [<a href='user.php?op=edit&userid=$id'>$edit</a>]");
			addnav("","user.php?op=edit&userid=$id");
		}
		output_notl("`n");
		//modulehook("}collapse");
	}
}
modulehook("village",$texts);
//special hook for all villages... saves queries...
//modulehook("village-{$session['user']['location']}",$texts);

if ($skipvillagedesc) output("`n");

$args = modulehook("blockcommentarea", array("section"=>$texts['section']));
if (!isset($args['block']) || $args['block'] != 'yes') {
		tlschema($schemas['talk']);
		output($texts['talk']);
		tlschema();
		// $start = microtime(true);
		dualcommentary($texts['section'],"Speak",25,$texts['sayline'], $schemas['sayline']);
		// $end = microtime(true);
		// $tot = $end - $start;
		// debug($tot);
}

module_display_events("village", "village.php");

addnav("Inventory");
addnav("View your Inventory","inventory.php?items_context=village");

page_footer();
?>
