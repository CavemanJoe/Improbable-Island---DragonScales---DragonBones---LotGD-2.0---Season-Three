<?php
function marriage_getmoduleinfo(){
	$info = array(
		"name"=>"Marriage Expanded",
		"version"=>"5.21",
		"author"=>"CortalUX, Oliver Brendel, expanded by DaveS",
		"override_forced_nav"=>true,
		"category"=>"Marriage",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1068",
		"settings"=>array(
			"Marriage - General,title",
			"location"=>"Where do the Marriage Navs occur?,enum,0,Village,1,Garden|0",
			"newlist"=>"Where does the Newlywed List Occur?,enum,0,Loveshack,1,Garden,2,Village,3,HoF,4,None|0",
			"If flirting is not allowed and you have the the list set to Loveshack it will default to the Garden,note",
			"newloc"=>"Newlywed List Location if in Village:,location|".getsetting("villagename", LOCATION_FIELDS),
			"dmoney"=>"Percent of money on hand sent to divorcee?,range,0,100,1|10",
			"(set to 0 to send nothing to divorcee),note",
			"acceptbuff"=>"Give player who's marriage is accepted or who is divorced a buff next newday?,bool|0",
			"dks"=>"Minimum dks needed to propose:,int|0",
			"sg"=>"Allow same-gender marriage and flirting?,bool|1",
			"oc"=>"Hook into 'oldchurch'?,bool|0",
			"(only if installed),note",
			"Marriage - Wedding Rings,title",
			"cost"=>"Cost of Wedding ring?,int|1500",
			"(set to 0 to turn buying rings off),note",
			"cansell"=>"Percent of value that user can sell the ring if it's rejected?,range,0,100,1|20",
			"(set to 0 to turn off selling ring),note",
			"Marriage - Counselling,title",
			"counsel"=>"Can a user get counselling if he/she is rejected?,bool|1",
			"The following are the results of the random counselling award:,note",
			"exp"=>"Experience gained by going through counselling?,int|250",
			"gold"=>"Gold per level gained by going through counselling?,int|50",
			"Marriage - Flirting,title",
			"loveDrinksAdd"=>"Status of Loveshack Drinks?,hidden|0",
			"flirttype"=>"Must a user flirt before Marriage?,bool|1",
			"If set to 'No' the Love Shack will not appear,note",
			"charmnewday"=>"How many flirt points to deduct on newdays?,int|1",
			"0 sets to -no deduct-,note",
			"reqflbuff"=>"Require player to flirt with spouse before receiving buff each day?,bool|0",
			"charmleveldifference"=>"How many charm points higher or lower cause an automatic fail?,int|20",
			"0 sets to -off-. Refers only to searched players. Once someone has him/her in the list it will work there,note",
			"flirtCharis"=>"Can users lose Charm?,bool|1",
			"flirtAutoDiv"=>"Will being unfaithful auto-divorce you?,bool|0",
			"flirtAutoDivT"=>"If yes- how many times does a user need to be unfaithful before an auto-divorce?,int|2",
			"flirtmuch"=>"How many flirt points must a user have before being able to propose?,range,1,100,1|30",
			"Note: this also counts for the auto-divorce due to too little flirtpoints left,note",
			"pointsautodivorceactive"=>"Does having too little flirtpoints auto-divorce?,bool|0",
			"lall"=>"Show the Love Shack in all villages?,bool|1",
			"loveloc"=>"If no- Where does the Love Shack appear?,location|".getsetting("villagename", LOCATION_FIELDS),
			"bartendername"=>"Name of the Bartender?,int|Jatti",
			"genderbartender"=>"Gender of the Bartender?,enum,0,Male,1,Female|0",
			"maxDayFlirt"=>"Maximum flirts per day?,range,1,100,1|10",
			"podrink"=>"Flirt points for buying someone a drink?,range,1,100,1|2",
			"prdrink"=>"Cost of buying someone a drink?,int|25",
			"poroses"=>"Flirt points for buying someone some roses?,range,1,100,1|10",
			"prroses"=>"Cost of buying someone some roses?,int|40",
			"poslap"=>"Flirt points lost for slapping someone?,range,1,100,1|5",
			"pokiss"=>"Flirt points for kissing someone?,range,1,100,1|12",
			"chancefail"=>"Chance for someone's flirt to fail?,range,0,100,1|10",
			"Marriage - Chapel,title",
			"name"=>"Name of Chapel/Church?,|Bluerock",
			"sacred"=>"Informal or Formal Ceremony?,enum,0,Informal,1,Formal|1",
			"all"=>"Show in all villages?,bool|1",
			"chapelloc"=>"If no- Where does the chapel appear?,location|".getsetting("villagename", LOCATION_FIELDS),
			"vicarname"=>"Name of vicar?,int|Tanat",
			"gendervicar"=>"Gender of vicar?,enum,0,Male,1,Female|1",
			"(these settings are used when the old church hook is turned off),note",
		),
		"prefs"=>array(
			"Marriage - Preferences,title",
			"user_bio"=>"`%Show spouse in Bios?,bool|1",
			"user_stats"=>"`%Show your spouse under your Character stats?,bool|1",
			"user_option"=>"`%Would you like to prevent all flirting?,bool|0",
			"This is only needed if the Love Shack is available.,note",
			"user_wed"=>"`%Would you like to prevent marriage proposals?,bool|0",
			"This is only needed if the Love Shack is NOT available.,note",
			"Either may be overridden by the Staff. Please contact them if you feel this is in error.,note",
			"Marriage - Other,title",
			"`b`\$(only edit beyond this point if you know what you are doing!)`b,note",
			"proposals"=>"`%Proposals..,text|",
			"`@(comma separated for each user id; comma and pipe seperated for each user id with points),note",
			"flirtsreceived"=>"`^Flirts to me..,text|",
			"`@(comma seperated for each user id),note",
			"flirtssent"=>"`^Flirts from me..,text|", 
			"blocked"=>"`^Accounts blocked by this player:,text|",
			"Override to No Flirting,title",
			"wededit"=>"Allow this player to edit marriages in the Grotto?,bool|0",
			"supernoflirt"=>"Would you like this player to be prevented from flirting?,bool|0",
			"supernomarry"=>"Would you like this player to be prevented from marrying?,bool|0",
			"Marriage Allprefs,title",
			"Note: Please edit with caution. Consider using the Allprefs Editor instead.,note",
			"allprefs"=>"Preferences,textarea|",
		),
		"prefs-drinks"=>array(
			"Marriage - Drink Preferences,title",
			"drinkLove"=>"Is this drink served in the Loveshack?,bool|1",
			"loveOnly"=>"Is this drink Loveshack only?,enum,1,No,0,Yes|1",
		),
	);
	return $info;
}

function marriage_install(){
	if (!is_module_active('marriage')){
		output_notl("`n`c`b`QMarriage Module - Installed`0`b`c");
	}else{
		output_notl("`n`c`b`QMarriage Module - Updated`0`b`c");
	}
	module_addhook("drinks-text");
	module_addhook("drinks-check");
	module_addhook("moderate");
	module_addhook("newday");
	module_addhook("changesetting");
	module_addhook_priority("footer-inn",1);
	module_addhook("village");
	module_addhook("footer-hof");
	module_addhook("superuser");
	module_addhook("footer-oldchurch");
	module_addhook("footer-gardens");
	module_addhook("delete_character");
	//module_addhook("charstats");
	module_addhook("faq-toc");
	module_addhook("biostat");
	module_addhook("allprefs");
	module_addhook("allprefnavs");
	if ($SCRIPT_NAME == "modules.php"){
		$module=httpget("module");
		if ($module == "marriage"){
			require_once("modules/marriage/lovedrinks.php");
			marriage_lovedrinks();
		}
	}
	return true;
}

function marriage_uninstall(){
	require_once("modules/marriage/lovedrinks.php");
	marriage_lovedrinksrem();
	output_notl("`n`c`b`QMarriage Module - Uninstalled`0`b`c");
	return true;
}

function marriage_dohook($hookname, $args){
	global $session;
	require("modules/marriage/marriage_dohook.php");
	return $args;
}

function marriage_run(){
	global $session;
	require_once("modules/marriage/marriage_func.php");
	$op = httpget('op');
	if ($op==''|| $op=='chapel' || $op=='oldchurch') 
		require("./modules/marriage/general.php");
		else
		require("./modules/marriage/$op.php");
}
?>