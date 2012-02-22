<?php
/***********************************************
 World Map
 Originally by: Aes
 Updates & Maintenance by: Kevin Hatfield - Arune (khatfield@ecsportal.com)
 Updates & Maintenance by: Roland Lichti - klenkes (klenkes@paladins-inn.de)
 Updates & Maintenance by: Dan Hall - Caveman Joe (cavemanjoe@gmail.com)
 Rewritten by: Cousjava (outingbrains-websitesgeneric@yahoo.co.uk)
 http://www.dragonprime.net
 Updated: Feb 23, 2008
 ************************************************/

//require_once('modules/worldmapwn/lib.php');

function worldmapwn_getmoduleinfo(){
	$info = array(
	"name"=>"World Map",
	"version"=>"0.2",
	"author"=>"Cousjava",
	"category"=>"Map",
	"download"=>"",
	"vertxtloc"=>"",
	"requires"=>array("cities"=>"1.0|This module requires the Multiple Cities module to be installed"),
	"settings"=>array(
		"World Map Settings,title",
		"viewRadius"=>"How many squares far can a player see while traveling?,range,0,10,2",
		"worldmapAcquire"=>"Can the world map be purchased?,bool|1",
		"worldmapCostGold"=>"How much gold does the World Map cost?,int|10000",
		'The world map is not yet avaliable in this version. It can be bought but not viewed, but once the functionality will be added,note',
		"showcompass"=>"Show images/compass.png?,bool|0",
		"compasspoints"=>"8 point compass?,bool|0",
		"showforestnav"=>"Show the forest link in village?,bool|0",
		"wmspecialchance"=>"Chance for a special during travel,int|7",
		"randevent"=>"Random Event -Don't Edit,text|forest",
		"randchance"=>"Percent chance you will get a travel module instead of forest,range,5,100,5",

		"Boundary Messages,title",
		"nBoundary"=>"Northern boundary,text|To the north are the impenetrable mountains of Loa.",
		"eBoundary"=>"Eastern boundary,text|The vast ocean of silence lay to your east.  Long before you can remember ships stopped sailing across to the other continents.  But why?",
		"sBoundary"=>"Southern boundary,text|To the south you can see a great ravine that seems to stretch on forever.",
		"wBoundary"=>"Western boundary,text|To the west lays the barren wasteland of the Goiu desert.  No one has ever survived out there.",
		
		"Gate Messages,title",
		"LeaveGates1"=>"Leave gates of village. (1)|A shiver runs down your back as you face the forest around you.",
		"LeaveGates2"=>"Leave gates of village. (2)|You're all alone now...",
		"LeaveGates3"=>"Leave gates of village. (3)|The sound of the forest settles in around you as you think to yourself what evil must lurk within.",
		"LeaveGates4"=>"Leave gates of village. (4)|Perhaps I should go back in...",
		"LeaveGates5"=>"Leave gates of village. (5)|A howling noise bellows from deep within the forest.  You hear the guards from the other side of the gates yell \"Good Luck!\" and what sounds like \"they'll never make it.",
		
		'Stamina costs are hard-coded in for the default values. However this can be changed on a per-map basis.,note',


		"Map array,title"
		//This is array containing the map files and their settings. This may be moved into their own table later.
		"maps"=>array(1=>array("location"=>"default","moverate"=>1)
				),
			),
		
		"Other Settings,title"
		"wraptype"=>"What type of wrap is there at edge of map?,int|0",
		'0 is for no wrap; 1 is for wrap east-west; 2 is for wrap north-south; 3 is for wrap east-west and north-south ,note',
		

/*
		"manualmove"=>"Turn on Superuser manual movement?,bool|0",
		"viewRadius"=>"How many squares far can a player see while traveling?,range,0,10,2",
		"worldmapAcquire"=>"Can the world map be purchased?,bool|1",
		"worldmapCostGold"=>"How much gold does the World Map cost?,int|10000",
		"enableTerrains"=>"Enable Terrains?,bool|1",
		"showcompass"=>"Show images/compass.png?,bool|0",
		"compasspoints"=>"8 point compass?,bool|0",
		"showcities"=>"Show the cities in the key? / Will show all cities,bool|0",
		"smallmap"=>"Show small map?,bool|1",
		"showforestnav"=>"Show the forest link in village?,bool|0",
		"wmspecialchance"=>"Chance for a special during travel,int|7",
		"randevent"=>"Random Event -Don't Edit,text|forest",
		"randchance"=>"Percent chance you will get a travel module instead of forest,range,5,100,5",
		
		//"Turns and Stamina,title",
		//"useturns"=>"Use one of the player's Turns when they encounter a monster?,bool|0",
		//"allowzeroturns"=>"Allow the fight to go ahead if the player's Turns are zero?,bool|1",
		//"turntravel"=>"Allow the player to trade one of his Turns for this many Travel points (set to zero to disable),int|0",
		//"usestamina"=>"Expanded Stamina system is installed and should be used instead of Travel points,bool|0",		
	
		"Terrain Encounter Settings,title",
		"encounterPlains"=>"Chance of encountering a monster when crossing plains?,int|20",
		"encounterForest"=>"Chance of encountering a monster when crossing dense forests?,int|85",
		"encounterRiver"=>"Chance of encountering a monster when crossing rivers?,int|20",
		"encounterOcean"=>"Chance of encountering a monster when crossing oceans?,int|20",
		"encounterDesert"=>"Chance of encountering a monster when crossing deserts?,int|85",
		"encounterSwamp"=>"Chance of encountering a monster when crossing swamps?,int|85",
		"encounterMount"=>"Chance of encountering a monster when crossing mountains?,int|20",
		"encounterSnow"=>"Chance of encountering a monster when crossing snow?,int|20",
		"encounterEarth"=>"Chance of encountering a monster when under surface?,int|1",
		"encounterAir"=>"Chance of encountering a monster when traveling in the air?,int|0",

		,*/
	),
	"prefs"=>array(
		"World Map User Preferences,title",
		"worldXYZ"=>"World Map X Y Z (separated by commas!)|0,0,0",
		"canedit"=>"Does user have rights to edit the map?,bool|0",
		"lastCity"=>"Where did the user leave from last?|",
		"worldmapbuy"=>"Did user buy map?,bool|0",
		"encounterchance"=>"Player's encounter chance expressed as a percentage of normal,int|100",
		"fuel"=>"The reduced-cost moves that a player has left because of his Mount,int|0",
		"user_blindoutput"=>"BETA OPTION for blind or visually impaired players using a screen reader - Show textual information about your location on the World Map?,bool|0",
	),
	/*"prefs-mounts"=>array(
		"World Map Mount Preferences,title",
		"All values are expressed as a decimal value of normal,note",
		"encounterPlains"=>"Encounter rate for crossing plains?,float|1",
		"encounterForest"=>"Encounter rate for crossing dense forests?,float|1",
		"encounterRiver"=>"Encounter rate for crossing rivers?,float|1",
		"encounterOcean"=>"Encounter rate for crossing oceans?,float|1",
		"encounterDesert"=>"Encounter rate for crossing deserts?,float|1",
		"encounterSwamp"=>"Encounter rate for crossing swamps?,float|1",
		"encounterMount"=>"Encounter rate for crossing mountains?,float|1",
		"encounterSnow"=>"Encounter rate for crossing snow?,float|1",
		"encounterEarth"=>"Encounter rate for crossing earth?,float|1",
		"encounterAir"=>"Encounter rates for crossing air?,float|1",
	)
	);*/
	return $info;
}

function worldmapen_install(){
	if (!is_module_installed("staminasystem")) {
		output("`b`^***** This module requires the stamina system to be installed. *****`b`7");
		return false;
	}
		module_addhook("village");
		module_addhook("villagenav");
		module_addhook("mundanenav");
		module_addhook("superuser");
		//module_addhook("pvpcount"); We're ignoring PvP for now. It will be one of the last things to be added.
		module_addhook("footer-gypsy");
		module_addhook("iitems_eboy_gypsy");//these last are the same, may be simplified later
		module_addhook("changesetting");
		//module_addhook("boughtmount"); Not top piority
		module_addhook("newday");
		module_addhook("items-returnlinks");
	
		$map = array(		//this bit is based on improbablehousing
		'id'=>array('name'=>'id', 'type'=>'int(11) unsigned', 'extra'=>'auto_increment'),
		'desc'=>array('name'=>'ownedby', 'type'=>'int(11) unsigned'),
		'location'=>array('name'=>'location', 'type'=>'text'),
		'data'=>array('name'=>'data', 'type'=>'mediumtext'),
		'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'id'),
		);
	
		synctable(db_prefix('map'), $map,true);//*/
		
		require_once('modules/staminasystem/lib/lib.php');
		install_action("Travelling - Plains",array(
			"maxcost"=>5000,
			"mincost"=>2500,
			"firstlvlexp"=>500,
			"expincrement"=>1.1,
			"costreduction"=>25,
			"class"=>"Travelling"
		));
		install_action("Travelling - Forest",array(
			"maxcost"=>10000,
			"mincost"=>4000,
			"firstlvlexp"=>500,
			"expincrement"=>1.1,
			"costreduction"=>60,
			"class"=>"Travelling"
		));
		install_action("Travelling - Mountains",array(
			"maxcost"=>20000,
			"mincost"=>6000,
			"firstlvlexp"=>500,
			"expincrement"=>1.1,
			"costreduction"=>140,
			"class"=>"Travelling"
		));
		install_action("Travelling - Hills",array(
			"maxcost"=>17000,
			"mincost"=>5000,
			"firstlvlexp"=>500,
			"expincrement"=>1.1,
			"costreduction"=>120,
			"class"=>"Travelling"
		));
		install_action("Travelling - Cave",array(
			"maxcost"=>17000,
			"mincost"=>5000,
			"firstlvlexp"=>500,
			"expincrement"=>1.1,
			"costreduction"=>120,
			"class"=>"Travelling"
		));
		install_action("Travelling - Frozen",array(
			"maxcost"=>25000,
			"mincost"=>7500,
			"firstlvlexp"=>500,
			"expincrement"=>1.1,
			"costreduction"=>175,
			"class"=>"Travelling"
		));
		install_action("Travelling - Sand",array(
			"maxcost"=>6000,
			"mincost"=>3000,
			"firstlvlexp"=>500,
			"expincrement"=>1.1,
			"costreduction"=>30,
			"class"=>"Travelling"
		));
		install_action("Travelling - Swamp",array(
			"maxcost"=>12500,
			"mincost"=>5000,
			"firstlvlexp"=>500,
			"expincrement"=>1.1,
			"costreduction"=>75,
			"class"=>"Travelling"
		));
		install_action("Travelling - Shallow Water",array(
			"maxcost"=>15000,
			"mincost"=>5000,
			"firstlvlexp"=>500,
			"expincrement"=>1.1,
			"costreduction"=>100,
			"class"=>"Travelling"
		));
		install_action("Travelling - Deep Water",array(
			"maxcost"=>25000,
			"mincost"=>7500,
			"firstlvlexp"=>500,
			"expincrement"=>1.1,
			"costreduction"=>175,
			"class"=>"Travelling"
		));
		install_action("Travelling - Flying",array(
			"maxcost"=>15000,
			"mincost"=>5000,
			"firstlvlexp"=>500,
			"expincrement"=>1.1,
			"costreduction"=>76,
			"class"=>"Travelling"
		));
	
	return true;
}

function worldmapwn_uninstall(){
	return true;
}

function worldmapwn_run() {
	require_once('modules/worldmapen/run.php');
	return worldmapen_run_real();
}

function worldmapwn_dohook($hookname,$args){
	global $session;
	
	// If the stamina system module is deactivated, we do nothing.
	if (!is_module_active("staminasystem")) 
		return $args;
	
	if (file_exists("modules/worldmapen/dohook/{$hookname}.php")) {
		require("modules/worldmapen/dohook/{$hookname}.php");
	} else {
		debug("Sorry, I don't have the hook '{$hookname}' programmed.");
	}

	return $args;
}
?>
