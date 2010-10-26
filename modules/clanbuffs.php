<?php

//Clan Buffs
//drop into /modules/ and activate, have fun

function clanbuffs_getmoduleinfo(){
        $info = array(
                "name"=>"Clan Buffs",
                "author"=>"Aelia",
                "version"=>"2.1",
                "category"=>"Clan",
                "download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1036",
                "settings"=>array(
                        "Clan Buff settings,title",
				"allowinpvp"=>"Allow clan buffs in PvP, bool|0",
				"allowintrain"=>"Allow clan buffs in Training, bool|0",
				"allowatk"=>"Allow attack upgrade?, bool|1",
				"allowdef"=>"Allow defense upgrade?, bool|1",
				"allowdrain"=>"Allow drain upgrade?, bool|1",
				"allowthorn"=>"Allow thorn upgrade?, bool|1",
				"allowregen"=>"Allow regen upgrade?, bool|1",
				"allowult"=>"Allow infinite duration upgrade after all others?, bool|1",
				"hof"=>"Show HoF page?, bool|1",
				"remakecost"=>"Remake cost array?, bool|0",
				"If you activate or deactivate any functions or change any of the costs or max levels,note",
				"Set this to yes or else the completion percentages will not function correctly,note",
				"maxcats"=>"Maximum number of categories that a clan can max out,int|0",
				"costarray"=>"Costs Array, viewonly",
                        "Prices,title",
				"All prices are in gems,note",
				"buffaprice"=>"Cost to activate buff, int|100",
				"atkaprice"=>"Cost to activate attack, int|150",
				"atkbase"=>"Cost to upgrade attack (base), int|25",
				"atkinc"=>"Cost to upgrade attack (increment), int|25",
				"defaprice"=>"Cost to activate defense, int|150",
				"defbase"=>"Cost to upgrade defense (base), int|25",
				"definc"=>"Cost to upgrade defense (increment), int|25",
				"drainaprice"=>"Cost to activate drain, int|150",
				"drainbase"=>"Cost to upgrade drain (base), int|25",
				"draininc"=>"Cost to upgrade drain (increment), int|25",
				"thornaprice"=>"Cost to activate thorn, int|150",
				"thornbase"=>"Cost to upgrade thorn (base), int|25",
				"thorninc"=>"Cost to upgrade thor (increment), int|25",
				"regenaprice"=>"Cost to activate regen, int|150",
				"regenbase"=>"Cost to upgrade regen (base), int|25",
				"regeninc"=>"Cost to upgrade regen (increment), int|25",
				"roundbase"=>"Cost to upgrade round (base), int|15",
				"roundinc"=>"Cost to upgrade round (increment), int|15",
				"ultaprice"=>"Cost to activate infinite duration?, int|750",
                        "Effects,title",
				"eatkbase"=>"Attack Multiplier (base), floatrange,0,.1,.01|0",
				"eatkinc"=>"Attack Multiplier (increment), floatrange,0,.1,.01|.05",
				"edefbase"=>"Defense Multiplier (base), floatrange,0,.1,.01|0",
				"edefinc"=>"Defense Multiplier (increment), floatrange,0,.1,.01|.05",
				"edrainbase"=>"Drain HP Multiplier (base), floatrange,0,.1,.01|0",
				"edraininc"=>"Drain HP Multiplier (increment), floatrange,0,.1,.01|.03",
				"ethornbase"=>"Thorn Multiplier (base), floatrange,0,.1,.01|0",
				"ethorninc"=>"Thorn Multiplier (increment), floatrange,0,.1,.01|.03",
				"eregenbase"=>"Regen Multiplier (base/level), floatrange,0,1,.05|0",
				"eregeninc"=>"Regen Multiplier (increment/level), floatrange,0,1,.05|.1",
				"eroundbase"=>"Rounds (base), range,0,25,1|0",
				"eroundinc"=>"Rounds (increment), range,0,25,1|10",
                        "Max Levels,title",
				"maxatk"=>"Maximum Attack Level, range,0,25,1|10",
				"maxdef"=>"Maximum Defense Level, range,0,25,1|10",
				"maxdrain"=>"Maximum Drain HP Level, range,0,25,1|10",
				"maxthorn"=>"Maximum Thorn Level, range,0,25,1|10",
				"maxregen"=>"Maximum Regen Level, range,0,25,1|10",
				"maxround"=>"Maximum Round Level, range,0,25,1|15",
                ),
                "prefs"=>array(
                  "Clan Buff user preferences,title",
                  "refreshbuff"=>"Refresh buff on next hit?,bool|0",
		),
                "prefs-clans"=>array(
					"Clan Buffs user preferences,title",
					"buffactive"=>"Buff activated,bool|0",
					"gems"=>"Gems stored in buff bank,int|0",
					"atkactive"=>"Attack Mod Active?,bool|0",
					"atklevel"=>"Attack Level,int|0",
					"defactive"=>"Defense Mod Active?,bool|0",
					"deflevel"=>"Defense Level,int|0",
					"drainactive"=>"Life Drain Mod Active?,bool|0",
					"drainlevel"=>"Life Drain Level,int|0",
					"thornactive"=>"Thorn Mod Active?,bool|0",
					"thornlevel"=>"Thorn Level,int|0",
					"regenactive"=>"Regen Mod Active?,bool|0",
					"regenlevel"=>"Regen Level,int|0",
					"roundlevel"=>"Rounds Level,int|0",
					"ultready"=>"Ready to buy infinite duration?,bool|0",
					"ultactive"=>"Infinite Duration Active,int|0",
					"totallevel"=>"Total Levels,int|0",
					"catsactive"=>"Total Categories Active,int|0",
                ),
        );
        return $info;
}

function clanbuffs_install(){
        include_once("modules/clanbuffs/clanbuffs_func.php");
        remake_costs();
        module_addhook("footer-hof");
        module_addhook("footer-clan");
        module_addhook_priority("village",49);
        module_addhook_priority("forest",49);
        module_addhook_priority("clanhall",49);
        module_addhook("newday");
        module_addhook("changesetting");
        return true;
}

function clanbuffs_uninstall(){
        return true;
}

function clanbuffs_dohook($hookname,$args){
        global $session;
        switch($hookname){
        case "footer-hof":
		if (get_module_setting("hof")) {
             addnav("Warrior Rankings");
             addnav("Clan Buffs","runmodule.php?module=clanbuffs&op=hof");	
		}
	  break;
        case "footer-clan":
             if($session['user']['clanid']!=0 and httpget("op")==""){
                if($session['user']['clanrank']>0){
                   addnav("Clan Buffs","runmodule.php?module=clanbuffs&op=enter");
                }
             }
        break;
        case "village":
        case "forest":
        case "clanhall":
		if (get_module_pref("refreshbuff")) {
		   set_module_pref("refreshbuff",0);
                   include_once("modules/clanbuffs/clanbuffs_func.php");
		   apply_clan_buff_for_one();
                   output("`n`n`c`b`!Your clan's aura has been upgraded and refreshed!`0`b`c`n`n");
		}
        break;
        case "newday":
              if ($session['user']['clanid'] != 0 && $session['user']['clanrank'] > 0) {
                if(get_module_objpref("clans", $session['user']['clanid'],"buffactive")==1){
                   include_once("modules/clanbuffs/clanbuffs_func.php");
                   apply_clan_buff_for_one();
                   output("`n`^Your clan's aura protects you!`n");
                }
             }
        break;
        case "changesetting":
             if($args['setting']=="remakecost" && $args['module']=="clanbuffs") {
                include_once("modules/clanbuffs/clanbuffs_func.php");
                remake_costs();
	     }
        break;
        
        }
        
        return $args;
}

function clanbuffs_run(){
	include_once("modules/clanbuffs/clanbuffs_func.php");
	require("modules/clanbuffs/clanbuffs.php");
}



?>
