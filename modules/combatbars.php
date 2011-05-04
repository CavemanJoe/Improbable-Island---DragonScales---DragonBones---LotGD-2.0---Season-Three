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
	global $session, $enemies, $companions;
	switch($hookname){
	case "battle":
		rawoutput("<div id=\"combatbars\"></div>");
		break;
	case "endofpage":
		$maxlen = combatbars_getleadingzeroes();
		rawoutput("<script style=\"text/javascript\" language=\"javascript\">document.getElementById(\"combatbars\").innerHTML = '';</script>");
		combatbars_showbar($session['user']['hitpoints'],$session['user']['maxhitpoints'],$session['user']['name'],$maxlen);
		if (count($companions)){
			foreach($companions AS $companion=>$vals){
				combatbars_showbar($vals['hitpoints'],$session['user']['maxhitpoints'],$vals['name'],$maxlen);
			}
		}
		foreach ($enemies as $index=>$badguy) {
			if (!$badguy['hidehitpoints']){
				combatbars_showbar($badguy['creaturehealth'],$session['user']['maxhitpoints'],$badguy['creaturename'],$maxlen);
			} else {
				combatbars_showhiddenbar($badguy['creaturename']);
			}
		}
		break;
	}
	return $args;
}

function combatbars_getleadingzeroes(){
	global $session, $enemies, $companions;
	$maxlen = 0;
	$len = strlen((string) $session['user']['maxhitpoints']);
	if ($len > $maxlen) $maxlen = $len;
	if (count($companions)){
		foreach($companions AS $companion=>$vals){
			$len = strlen((string) $vals['hitpoints']);
			if ($len > $maxlen) $maxlen = $len;
		}
	}
	foreach ($enemies as $index=>$badguy) {
		if (!$badguy['hidehitpoints']){
			$len = strlen((string) $badguy['creaturehealth']);
			if ($len > $maxlen) $maxlen = $len;
		}
	}
	debug($maxlen);
	return $maxlen;
}

function combatbars_showbar($cur,$max,$name,$maxlen=0){
	$pct = round($cur/$max * 100, 0);
	$pixeloffset = 800-($pct*2);
	if ($cur>0){
		$numdisp = number_format($cur);
	} else {
		$numdisp = "K.O.";
	}
	//$hpout = combatbars_hitpoints($cur,$maxlen);
	$hpout = nixienumbers($cur,2);
	$nhpout = addslashes($hpout);
	rawoutput("<script style=\"text/javascript\" language=\"javascript\">document.getElementById(\"combatbars\").innerHTML += '<table cellpadding=0 cellspacing=0><tr><td>".$nhpout."</td><td><table cellpadding=0 cellspacing=0><tr><td width=\"220px\"><div style=\"width:200; height:25; background:url(/modules/combatbars/lifebar-bottom.png); border:1px solid #000000;\"><div style=\"width: 195; height: 25; padding-left:5px; background: url(/modules/combatbars/lifebar-top.png); background-repeat: no-repeat; line-height: 25px; background-position: -".$pixeloffset."px\"><font color=#ffffff><b>".$numdisp."</b></font></div></div></td><td><b>".addslashes(appoencode($name))."</b></font></td></tr></table></td></tr></table>';</script>");
	//rawoutput("<script style=\"text/javascript\" language=\"javascript\">document.getElementById(\"combatbars\").innerHTML += '<table cellpadding=0 cellspacing=0><tr><td width=\"220px\"><div style=\"width:200; height:25; background:url(/modules/combatbars/lifebar-bottom.png); border:1px solid #000000;\"><div style=\"width: 195; height: 25; padding-left:5px; background: url(/modules/combatbars/lifebar-top.png); background-repeat: no-repeat; line-height: 25px; background-position: -".$pixeloffset."px\"><font color=#ffffff><b>".$numdisp."</b></font></div></div></td><td><b>".addslashes(appoencode($name))."</b></font></td></tr></table>';</script>");
}

function nixienumbers($number,$size=1){
	//convert to string
	if ($number > 0){
		$number = "$number";
	} else {
		$number = "0";
	}
	$len = strlen($number);
	$str = "";
	$tsep = -1;
	for ($i=$len-1; $i>=0; $i--){
		$stradd="<img src='images/numbers/nixies/".$size."/nixie_".$number[$i].".png'>";
		$tsep++;
		if ($tsep==3){
			$stradd.="&nbsp;&nbsp;&nbsp;";
			$tsep=0;
		}
		$str = $stradd.$str;
	}
	while ($tsep < 2){
		$stradd = "<img src='images/numbers/nixies/".$size."/nixie_blank.png'>";
		$str = $stradd.$str;
		$tsep++;
	}
	return $str;
}

function combatbars_hitpoints($hp,$maxlen=0){
	if ($hp > 0){
		$number = "$hp";
		$len = strlen($number);
		$str = "";
		$tsep = -1;
		for ($i=$len-1; $i>=0; $i--){
			$stradd="<img src=\"images/numbers/".$number[$i].".png\">";
			$tsep++;
			if ($tsep==3){
				$stradd.="<img src=\"images/numbers/sep.png\">";
				$stradd.="<img src=\"images/numbers/tsep.png\">";
				$stradd.="<img src=\"images/numbers/sep.png\">";
				$tsep=0;
			} else {
				if ($i != $len-1){
					$stradd.="<img src=\"images/numbers/sep.png\">";
				}
			}
			$str = $stradd.$str;
		}
		for ($i = 0; $i<$maxlen-$len; $i++){
			$str = "<img src=\"images/numbers/blank.png\">".$str;
		}
	} else {
		$str = "";
		for ($i = 0; $i<$maxlen-1; $i++){
			$str .= "<img src=\"images/numbers/blank.png\">";
		}
		$str .= "<img src=\"images/numbers/0.png\">";
	}
	return $str;
}

function combatbars_showhiddenbar($name){
	$pixeloffset = 0;
	$numdisp = "???";
	rawoutput("<script style=\"text/javascript\" language=\"javascript\">document.getElementById(\"combatbars\").innerHTML += '<table cellpadding=0 cellspacing=0><tr><td width=\"220px\"><div style=\"width:200; height:25; background:url(/modules/combatbars/lifebar-bottom.png); border:1px solid #000000;\"><div style=\"width: 195; height: 25; padding-left:5px; background: url(/modules/combatbars/lifebar-top.png); background-repeat: no-repeat; line-height: 25px; background-position: -".$pixeloffset."px\"><font color=#ffffff><b>".$numdisp."</b></font></div></div></td><td><b>".addslashes(appoencode($name))."</b></font></td></tr></table>';</script>");
}
?>