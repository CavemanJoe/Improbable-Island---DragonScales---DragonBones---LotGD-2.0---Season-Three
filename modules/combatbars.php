<?php

function combatbars_getmoduleinfo(){
	$info = array(
		"name"=>"Combat Bars",
		"version"=>"2008-08-13",
		"author"=>"Dan Hall",
		"category"=>"General",
		"download"=>"",
	);
	return $info;
}
function combatbars_install(){
	module_addhook("battle");
	module_addhook("endofpage");
	return true;
}
function combatbars_uninstall(){
	return true;
}
function combatbars_dohook($hookname,$args){
	global $session, $enemies;
	switch($hookname){
	case "battle":
		rawoutput("<div id=\"combatbars\"></div>");
		break;
	case "endofpage":
		rawoutput("<script style=\"text/javascript\" language=\"javascript\">document.getElementById(\"combatbars\").innerHTML = '';</script>");
		combatbars_showbar($session['user']['hitpoints'],$session['user']['maxhitpoints'],$session['user']['name']);
		foreach ($enemies as $index=>$badguy) {
			if (!$badguy['hidehitpoints']){
				combatbars_showbar($badguy['creaturehealth'],$session['user']['maxhitpoints'],$badguy['creaturename']);
			} else {
				combatbars_showhiddenbar($badguy['creaturename']);
			}
		}
		break;
	}
	return $args;
}

function combatbars_showbar($cur,$max,$name){
	$pct = round($cur/$max * 100, 0);
	$pixeloffset = 800-($pct*2);
	if ($cur>0){
		$numdisp = number_format($cur);
	} else {
		$numdisp = "K.O.";
	}
	rawoutput("<script style=\"text/javascript\" language=\"javascript\">document.getElementById(\"combatbars\").innerHTML += '<table cellpadding=0 cellspacing=0><tr><td width=\"220px\"><div style=\"width:200; height:25; background:url(/modules/combatbars/lifebar-bottom.png); border:1px solid #000000;\"><div style=\"width: 195; height: 25; padding-left:5px; background: url(/modules/combatbars/lifebar-top.png); background-repeat: no-repeat; line-height: 25px; background-position: -".$pixeloffset."px\"><font color=#ffffff><b>".$numdisp."</b></font></div></div></td><td><b>".addslashes(appoencode($name))."</b></font></td></tr></table>';</script>");
}

function combatbars_showhiddenbar($name){
	$pixeloffset = 0;
	$numdisp = "???";
	rawoutput("<script style=\"text/javascript\" language=\"javascript\">document.getElementById(\"combatbars\").innerHTML += '<table cellpadding=0 cellspacing=0><tr><td width=\"220px\"><div style=\"width:200; height:25; background:url(/modules/combatbars/lifebar-bottom.png); border:1px solid #000000;\"><div style=\"width: 195; height: 25; padding-left:5px; background: url(/modules/combatbars/lifebar-top.png); background-repeat: no-repeat; line-height: 25px; background-position: -".$pixeloffset."px\"><font color=#ffffff><b>".$numdisp."</b></font></div></div></td><td><b>".addslashes(appoencode($name))."</b></font></td></tr></table>';</script>");
}
?>