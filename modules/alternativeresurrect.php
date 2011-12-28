<?php

//Version 0.3 update by CMJ: buffs are suspended on death, restored on resurrection.  Comment out the link to the Graveyard in news.php (hell, I'm not gonna addnav in there only to blocknav in here again - waste of resources).

function alternativeresurrect_getmoduleinfo(){
	$info = array(
		"name"=>"Alternative Resurrection",
		"version"=>"0.2",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1142",
		"author"=>"Maeher, buff suspension by CavemanJoe",
		"category"=>"Shades",
		"description"=>"This module blocks the normal resurrection. Resurrecting does not give the player a newday anymore.",
		"settings"=>array(
			"hppercentage"=>"Percentage of hitpoints restored upon resurrection,range,1,100,1|1",
			"turnslost"=>"Number of turns lost upon resurrection,int|2",
			"charmlost"=>"Charm lost upon resurrection (A half rotten body smells bad and is not very attractive),int|3",
		),
		"prefs"=>array(
			"bufflist"=>"Player's suspended buff list,viewonly|",
		),
	);
	return $info;
}

function alternativeresurrect_install(){
	module_addhook("ramiusfavors");
	module_addhook("shades");
	module_addhook("newday");
	return true;
}

function alternativeresurrect_uninstall() {
	return true;
}

function alternativeresurrect_dohook($hookname,$args) {
	global $session;
	switch($hookname){
		case "ramiusfavors":
			blocknav("graveyard.php?op=resurrection");
			if($session['user']['deathpower']>=100){
				$deathoverlord = getsetting('deathoverlord','Ramius');
				addnav(array("%s Favors",sanitize($deathoverlord)));
				addnav("e?Resurrection (100 favor)","runmodule.php?module=alternativeresurrect&op=resurrect");
			}
			break;
		case "shades":
			if ($session['user']['bufflist']!="a:0:{}"){
				set_module_pref("bufflist",$session['user']['bufflist']);
			}
			break;
		case "newday":
			clear_module_pref("bufflist");
			break;
	}
	return $args;
}

function alternativeresurrect_run(){
	global $session;
	$op = httpget('op');
	
	if($op == "resurrect"){
		if($session['user']['deathpower']>=100){
			page_header("The Graveyard");
			$session['user']['deathpower']-=100;
			$session['user']['resurrections']++;
			$deathoverlord = getsetting('deathoverlord','Ramius');
			output("`\$%s`0 waves his skeletal arms as he begins to command the very fabric of life.`n`n",$deathoverlord);
			output("\"`)Noitcerruser evah llahs ut...`\$\"  The air begins to crackle around you.`n`n");
			output("\"`)Tnavres o htaed eht morf esir.`\$\" Your soul begins to burn with the pain of a thousand frosty fires.`n`n");
			output("\"`)Enim si htaed revo rewop.`\$\" Gradually you begin to become aware that the fires are dimming and are replaced by the blinding pain last known by your body before it fell.`n`n");
			output("\"`)Niaga ut tnarg oge efil ruoy.`\$\" You begin to look around you, and you watch as your muscles knit themselves back together.`n`n");
			output("\"`)Niaga em ot nruter llahs ut wonk oge rof.`\$\" With a gasp, you laboriously again draw your first breath.`n`n`n");
			$hppercentage = get_module_setting("hppercentage");
			$turnslost = get_module_setting("turnslost");
			$charmlost = get_module_setting("charmlost");
			
			$session['user']['hitpoints'] = max(round($session['user']['maxhitpoints']/100*$hppercentage),1);
			if($hppercentage<100){
				output("`QYou are alive again, but you seem to be far from healthy. You should really see a doctor.`n");
			}
			if($turnslost>0){
				$session['user']['turns']-=$turnslost;
				if($session['user']['turns']<0){
					$session['user']['turns']=0;
				}
				if($turnslost==1){
					output("`QYou realize that you lost `\$one`Q turn wandering among the dead.`n");
				}else{
					output("`QYou realize that you lost `\$%s`Q turns wandering among the dead.`n",$turnslost);
				}
			}
			if($charmlost>0){
				output("`QYou can feel the beginning decay of your own flesh. The breath-taking smell makes you choke ");
				if($session['user']['charm']>0){
					output(". You loose`\$ %s `Qcharm.`n",$charmlost);
					$session['user']['charm']-=$charmlost;
				}else{
					output(" but even the stinking flesh can't make you less attractive, than you already are.`n");
				}
			}
			modulehook("alternativeresurrect");
			addnews("`&%s`& has been returned to the mainland by %s`&.",$session['user']['name'],$deathoverlord);
			addnav("Return to the living","shades.php");

			//restore suspended buffs
			$buffs = unserialize(get_module_pref("bufflist"));
			if (is_array($buffs)){
				foreach ($buffs AS $buff=>$values){
					apply_buff($buff,$values);
				}
			}
			
			clear_module_pref("bufflist");

			page_footer();
		}else{
			page_header("The Graveyard");
			output("You do not have enough favor to resurrect.");
			addnav("Return to the shades","shades.php");
			page_footer();
		}
	}
}
?>