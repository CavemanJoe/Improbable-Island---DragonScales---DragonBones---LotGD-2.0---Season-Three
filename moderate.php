<?php

require_once("common.php");
require_once("lib/commentary.php");
require_once("lib/sanitize.php");
require_once("lib/http.php");

// tlschema("moderate");

addcommentary();

check_su_access(SU_EDIT_COMMENTS);

global $moderating;
$moderating=1;

page_header("Comment Moderation");

// First, set up the left column navs. These don't change.
require_once("lib/superusernav.php");
superusernav();
addnav("B?Player Bios","bios.php");

addnav("Overviews");
addnav("Recent Comments","moderate.php");
addnav("Natters","moderate.php?op=bio");

addnav("Lookups");
addnav("Dwellings","moderate.php?op=dwell");
addnav("World Map","moderate.php?op=map");
// addnav("Natters","moderate.php?op=bio");

// Get section and display names from other modules with chat spaces
$mods = array();
$mods = modulehook("moderate", $mods);
reset($mods);

// One of the outposts is "village" and the rest are from race modules
// Let's get them all into one array.
$cities = array();
$vname = getsetting("villagename", LOCATION_FIELDS);
$cities['village']['auxarea'] = "village-aux";
$cities['village']['name'] = $vname;

foreach ($mods AS $area=>$name) {
	// strncmp returns 0 if equal
	if (!strncmp($name,"City of ", 8)){
		// move outposts from the $mods array to a $cities array
		$cities[$area]['auxarea'] = $area . "-aux";
		$cities[$area]['name'] = substr($name, 8);
		unset($mods[$area]);
	}
}
reset($mods);

// First get the old names in, the ones that aren't supplied by modules
$iname = getsetting("innname", LOCATION_INN);
$locs = array(
	"gardens" 		=> "Common Ground",
	"shade" 		=> "Failboat",
	"inn" 			=> $iname,
	"hunterlodge" 	=> "Hunter's Lodge",
	"veterans" 		=> "Veterans Club",
	"grassyfield" 	=> "Grassy Field",
	"waiting" 		=> "Clan Hall Waiting Area",
	"motd" 			=> "Message of the Day",
);
if ($session['user']['superuser'] & ~SU_DOESNT_GIVE_GROTTO) {
	$locs["superuser"] = "Superuser Grotto";
}
// Now add in the remaining non-city locations from what's left of the $mods array
foreach ($mods as $area=>$name) {
	$locs[$area] = $name;
}
reset($locs);

addnav("Outposts");
foreach ($cities AS $area=>$citydet){
	addnav(array("%s",$citydet['name']),"moderate.php?op=dual&area=".$area);
}
reset($cities);

addnav("Other Locations");
foreach ($locs as $area=>$name) {
	addnav($name, "moderate.php?op=sect&area=$area");
}
reset($locs);

addnav("Raw View Modes");
addnav("Separate","moderate.php?op=raw&viewmode=separate");
addnav("All","moderate.php?op=raw&viewmode=all");

$op = httpget('op');
switch ($op){
	case "":
	foreach ($cities AS $area=>$citydet){
		output("`b");
		rawoutput("<a href=\"moderate.php?op=dual&area=$area\">".$citydet['name']."</a>");
		output("`b`n`0");
		addnav("","moderate.php?op=dual&area=$area");
		dualcommentary($area,"X",25);
		rawoutput("<hr style=\"border-bottom: 1px dotted #333333; border-top: 0; border-left: 0; border-right: 0;\" />");
	}

	foreach ($locs AS $area=>$name){
		output("`b");
		rawoutput("<a href=\"moderate.php?op=sect&area=$area\">".$name."</a>");
		output("`b`n`0");
		addnav("","moderate.php?op=sect&area=$area");
		viewcommentary($area,"X",25);
		rawoutput("<hr style=\"border-bottom: 1px dotted #333333; border-top: 0; border-left: 0; border-right: 0;\" />");
	}
	break;

	case "bio":
		output("`bNatter Overview`b`n`n");
		$sql = "SELECT DISTINCT section FROM ".db_prefix("commentary")." WHERE section LIKE 'bio%' ORDER BY section DESC";
		$result = db_query($sql);
		$locations = array();
		while ($row=db_fetch_assoc($result)){
			$locations[]=$row['section'];
		}

		foreach($locations AS $key=>$loc){
			$acctid = substr($loc,4);
			$login = moderate_getlogin($acctid);
			if ($login == "Unknown"){
				output("`b%s`b`n",$loc);
			} else {
//				output("`b%s`0`b`n",$login);
				$link = "bio.php?char=".$acctid ."&ret=".URLEncode($_SERVER['REQUEST_URI']);
				output("<a href=\"$link\">`b$login`b`n</a>",true);
				addnav("",$link);
			}
			viewcommentary($loc, "Intervene:", 25);
			rawoutput("<hr style=\"border-bottom: 1px dotted #333333; border-top: 0; border-left: 0; border-right: 0;\" />");
		}
	break;

	case "dwell":
		output("`bDwellings Lookup`b`n`n");
		output("Enter map coordinates:`n");
		rawoutput("<form action='moderate.php?op=listdwell' method='POST'>");
		// Note: Width 2 means a 2-digit number. Set the default location to 13,11 Improbable Central.
		rawoutput("X = <input name='mapX' width='2'> , Y = <input name='mapY' width='2'><br/><br/>");
//		rawoutput("X = <input name='mapX' width='2'> , Y = <input name='mapY' width='2'>, Z = <input name='mapZ' width='2' value='1'><br/><br/>");
		rawoutput("<input type='submit' class='button' value='".translate_inline("List Dwellings")."'>");
		rawoutput("</form>");
		addnav("","moderate.php?op=listdwell");
	break;

	case "listdwell":
		$x = httppost("mapX");
		$y = httppost("mapY");
//		$z = httppost("mapZ");
		// make sure they entered values that are in range for the size of the map.
		$sizeX = get_module_setting("worldmapsizeX","worldmapen");
		$sizeY = get_module_setting("worldmapsizeY","worldmapen");
		// Reminder: If/when Z ever becomes a real dimension, this code will need adapting.
		// Not going to do it now, because... who knows how things will change?
		if ($x <= 0 || $x > $sizeX || $y <= 0 || $y > $sizeY) {
			output("`bOff the Map!`b`n`n");
			output("You entered: x = %s, y = %s.`n", $x, $y);
			output("Valid map coordinates are: x = 1 to %s, and y = 1 to %s. Please try again.", $sizeX, $sizeY);
		}else{
			addnav("","moderate.php?op=dwellmap&x=$x&y=$y");
			redirect("moderate.php?op=dwellmap&x=$x&y=$y");
		}
	break;
	
	case "dwellchat":
		require_once "modules/improbablehousing/lib/lib.php";
		$hid = httpget('hid');
		$rid = httpget('rid');
		$x = httpget('x');
		$y = httpget('y');
		$room = "dwelling-".$hid."-".$rid;
		$maploc = $x.",".$y.",1";
		$house = improbablehousing_gethousedata($hid);
		$hdg = $house['data']['name'].": ".$house['data']['rooms'][$rid]['name'];
		output("`b%s`0`n`n", $hdg);
		rawoutput("<a href=\"moderate.php?op=dwellchat&hid=$hid&rid=$rid&x=$x&y=$y\">".$room."</a>");
		addnav("","moderate.php?op=dwellchat&hid=$hid&rid=$rid&x=$x&y=$y");
		rawoutput(" | <a href=\"moderate.php?op=dwellmap&x=$x&y=$y\">");
		output("Accessible Dwellings at %s",$maploc);
		rawoutput("</a>");
		addnav("","moderate.php?op=dwellmap&x=$x&y=$y");
		output("`b`n");
		viewcommentary($room,"Intervene:",100);
	break;
	
	case "dwellmap":
		$x = httpget('x');
		$y = httpget('y');
			$maploc = $x.",".$y.",1";
			require_once "modules/improbablehousing/lib/lib.php";
			global $session;
			$list = improbablehousing_getnearbyhouses($maploc);
			$nlist = count($list);
			output("`bAccessible Dwellings at %s`b`n", $maploc);
			if ($nlist){
				for ($i=0; $i<$nlist; $i++){
					$house = $list[$i];
					$house = improbablehousing_canenter_house($house);
					if ($house['canenter']){
						// assemble dwelling code here
						$nrooms = count($house['data']['rooms']);
						if ($nrooms){
							output("`n%s`0 (owner: %s)`n",$house['data']['name'], moderate_getlogin($house['ownedby']));
							$hid = $house['id'];
							foreach ($house['data']['rooms'] AS $rid => $roomdet){
								if (improbablehousing_canenter_room($house,$rid)){
									rawoutput("<a href=\"moderate.php?op=dwellchat&hid=$hid&rid=$rid&x=$x&y=$y\">"."[Mod]"."</a>");
									addnav("","moderate.php?op=dwellchat&hid=$hid&rid=$rid&x=$x&y=$y");
									output(" %s`n",$roomdet['name']);
								}
							}
						}
					}
				}
			}
	break;

	case "map";
		output("`bWorld Map Lookup`b`n`n");
		output("Enter map coordinates:`n");
		rawoutput("<form action='moderate.php?op=maplook' method='POST'>");
		// Note: Width 2 means a 2-digit number. Set the default location to 13,11 Improbable Central.
		rawoutput("X = <input name='mapX' width='2'> , Y = <input name='mapY' width='2'><br/><br/>");
//		rawoutput("X = <input name='mapX' width='2'> , Y = <input name='mapY' width='2'>, Z = <input name='mapZ' width='2' value='1'><br/><br/>");
		rawoutput("<input type='submit' class='button' value='".translate_inline("Moderate!")."'>");
		rawoutput("</form>");
		addnav("","moderate.php?op=maplook");
	break;
	
	case "maplook":
		$x = httppost("mapX");
		$y = httppost("mapY");
//		$z = httppost("mapZ");
		// make sure they entered values that are in range for the size of the map.
		$sizeX = get_module_setting("worldmapsizeX","worldmapen");
		$sizeY = get_module_setting("worldmapsizeY","worldmapen");
		// Reminder: If/when Z ever becomes a real dimension, this code will need adapting.
		// Not going to do it now, because... who knows how things will change?
		if ($x <= 0 || $x > $sizeX || $y <= 0 || $y > $sizeY) {
			output("`bOff the Map!`b`n`n");
			output("You entered: x = %s, y = %s.`n", $x, $y);
			output("Valid map coordinates are: x = 1 to %s, and y = 1 to %s. Please try again.", $sizeX, $sizeY);
		}else{
			$square = "worldmap-".$x.",".$y.",1";
			addnav("","moderate.php?op=mapreload&area=$square");
			redirect("moderate.php?op=mapreload&area=$square");
		}
	break;

	case "mapreload":
		$square = httpget('area');
		output("`b");
		rawoutput("<a href=\"moderate.php?op=mapreload&area=$square\">".$square."</a>");
		output("`b`n`0");
		addnav("","moderate.php?op=mapreload&area=$square");
		viewcommentary($square,"Intervene:",100);
	break;

	case "dual":
		$area = httpget('area');
		output("`b");
		rawoutput("<a href=\"moderate.php?op=dual&area=$area\">".$cities[$area]['name']."</a>");
		output("`b`0");
		addnav("","moderate.php?op=dual&area=$area");
//		output("`b%s`b", $cities[$area]['name']);
		dualcommentary($area,"Intervene:",100);
	break;

	case "sect":
		$area = httpget('area');
		$locname = (isset($locs[$area]) ? $locs[$area] : $cities[$area]['name']);
		output("`b");
		rawoutput("<a href=\"moderate.php?op=sect&area=$area\">".$locname."</a>");
		output("`b`n`0");
		addnav("","moderate.php?op=sect&area=$area");
		viewcommentary($area,"Intervene:",100);
	break;

	case "raw":
		$viewmode = httpget('viewmode');
//		output("Raw mode '%s':`n", $viewmode);

		switch ($viewmode){
			case "separate":
				output("`bBy Section Name`b`n`n");
				$sql = "SELECT DISTINCT section FROM ".db_prefix("commentary")." WHERE section NOT LIKE 'dwelling%' AND section NOT LIKE 'bio%' AND section NOT LIKE 'clan%' AND section NOT LIKE 'pet-%'";
				$result = db_query($sql);
				$locations = array();
				while ($row=db_fetch_assoc($result)){
					$locations[]=$row['section'];
				}

				foreach($locations AS $key=>$loc){
					output("`b%s`b`n",$loc);
					viewcommentary($loc, "Intervene:", 25);
					rawoutput("<hr style=\"border-bottom: 1px dotted #333333; border-top: 0; border-left: 0; border-right: 0;\" />");
				}
			break;

			case "all";
				output("`bAll Comments`b`n");
				viewcommentary("all", "X", 200);
			break;
		}
	break;
}

function moderate_getlogin($acctid){
	$sql = "SELECT login FROM " . db_prefix("accounts") . " WHERE acctid = " . $acctid;
	$searchresult = db_query($sql);
	if (db_num_rows($searchresult) != 1) {
		$login = "Unknown";
	} else {
		$i=0;
		$row=db_fetch_assoc($searchresult);
		$login = $row['login'];
	}
	return $login;
}


page_footer();
?>