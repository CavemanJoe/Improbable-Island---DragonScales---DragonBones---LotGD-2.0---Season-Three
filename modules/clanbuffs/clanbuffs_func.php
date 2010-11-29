<?php
function apply_clan_buff_for_one() {
	global $session;
	strip_buff("clanbuff");
	strip_buff("clanbuff2");
	strip_buff("clanbuff3");
	if (get_module_setting("allowatk") && get_module_objpref("clans", $session['user']['clanid'],"atkactive"))
		$atkmod=1+get_module_setting("eatkbase")+get_module_objpref("clans", $session['user']['clanid'],"atklevel")*get_module_setting("eatkinc");
	else
		$atkmod=1;

	if (get_module_setting("allowdef") && get_module_objpref("clans", $session['user']['clanid'],"defactive"))
		$defmod=1+get_module_setting("edefbase")+get_module_objpref("clans", $session['user']['clanid'],"deflevel")*get_module_setting("edefinc");
	else
		$defmod=1;

	if (get_module_setting("allowdrain") && get_module_objpref("clans", $session['user']['clanid'],"drainactive"))
		$lifetap=get_module_setting("edrainbase")+get_module_objpref("clans", $session['user']['clanid'],"drainlevel")*get_module_setting("edraininc");
	else
		$lifetap=0;

	$allowinpvp=get_module_setting("allowinpvp");
	$allowintrain=get_module_setting("allowintrain");

	if (get_module_setting("allowult") && get_module_objpref("clans", $session['user']['clanid'],"ultactive"))
		$rounds=-1;
	else
		$rounds=get_module_setting("eroundbase")+get_module_objpref("clans", $session['user']['clanid'],"roundlevel")*get_module_setting("eroundinc");

	if (get_module_setting("allowthorn") && get_module_objpref("clans", $session['user']['clanid'],"thornactive"))
		$damageshield=get_module_setting("ethornbase")+get_module_objpref("clans", $session['user']['clanid'],"thornlevel")*get_module_setting("ethorninc");
	else
		$damageshield=0;

	if (get_module_setting("allowregen") && get_module_objpref("clans", $session['user']['clanid'],"regenactive"))
		$regen=get_module_setting("eregenbase")+get_module_objpref("clans", $session['user']['clanid'],"regenlevel")*get_module_setting("eregeninc");
	else
		$regen=0;

	apply_buff("clanbuff",array(
		"name"=>"`^Clan Aura`0",
		"atkmod"=>$atkmod,
		"defmod"=>$defmod,
		"lifetap"=>$lifetap,
		"effectmsg"=>sprintf_translate("`6Your Clan Aura allows you to absorb `^{damage}`6 of the damage you dealt to {badguy}!"),
		"allowinpvp"=>$allowinpvp,
		"allowintrain"=>$allowintrain,
		"rounds"=>$rounds,
		"roundmsg"=>"Your Clan's Aura strengthens you!",
		"schema"=>"module-clanbuffs",
		)
	);
	apply_buff("clanbuff2",array(
		"damageshield"=>$damageshield,
		"effectmsg"=>sprintf_translate("`6Your Clan Aura allows you to reflect `^{damage}`6 of the damage you received {badguy}!"),
		"allowinpvp"=>$allowinpvp,
		"allowintrain"=>$allowintrain,
		"rounds"=>$rounds,
		"schema"=>"module-clanbuffs",
		)
	);
	apply_buff("clanbuff3",array(
		"regen"=>$regen."*<level>",
		"effectmsg"=>sprintf_translate("`6Your Clan Aura allows you to regenerate `^{damage}`6 damage!"),
		"allowinpvp"=>$allowinpvp,
		"allowintrain"=>$allowintrain,
		"rounds"=>$rounds,
		"schema"=>"module-clanbuffs",
		)
	);

}

function remake_costs() {
	$cost = array();
	
	$total=0;

	if (get_module_setting("allowatk")) {
	$cost['atk']['active']=get_module_setting("atkaprice");
	$cost['atk'][0]=0;
	$cost['atk'][1]=get_module_setting("atkbase");
	for ($i=2; $i<=get_module_setting("maxatk"); $i++)
		$cost['atk'][$i]=$cost['atk'][$i-1]+$i*get_module_setting("atkinc");
	$total+=$cost['atk'][get_module_setting("maxatk")]+$cost['atk']['active'];
	}

	if (get_module_setting("allowdef")) {
	$cost['def']['active']=get_module_setting("defaprice");
	$cost['def'][0]=0;
	$cost['def'][1]=get_module_setting("defbase");
	for ($i=2; $i<=get_module_setting("maxdef"); $i++)
		$cost['def'][$i]=$cost['def'][$i-1]+$i*get_module_setting("definc");
	$total+=$cost['def'][get_module_setting("maxdef")]+$cost['def']['active'];
	}

	if (get_module_setting("allowdrain")) {
	$cost['drain']['active']=get_module_setting("drainaprice");
	$cost['drain'][0]=0;
	$cost['drain'][1]=get_module_setting("drainbase");
	for ($i=2; $i<=get_module_setting("maxdrain"); $i++)
		$cost['drain'][$i]=$cost['drain'][$i-1]+$i*get_module_setting("draininc");
	$total+=$cost['drain'][get_module_setting("maxdrain")]+$cost['drain']['active'];
	}

	if (get_module_setting("allowthorn")) {
	$cost['thorn']['active']=get_module_setting("thornaprice");
	$cost['thorn'][0]=0;
	$cost['thorn'][1]=get_module_setting("thornbase");
	for ($i=2; $i<=get_module_setting("maxthorn"); $i++)
		$cost['thorn'][$i]=$cost['thorn'][$i-1]+$i*get_module_setting("thorninc");
	$total+=$cost['thorn'][get_module_setting("maxthorn")]+$cost['thorn']['active'];
	}

	if (get_module_setting("allowregen")) {
	$cost['regen']['active']=get_module_setting("regenaprice");
	$cost['regen'][0]=0;
	$cost['regen'][1]=get_module_setting("regenbase");
	for ($i=2; $i<=get_module_setting("maxregen"); $i++)
		$cost['regen'][$i]=$cost['regen'][$i-1]+$i*+get_module_setting("regeninc");
	$total+=$cost['regen'][get_module_setting("maxregen")]+$cost['regen']['active'];
	}

	$cost['round'][0]=0;
	$cost['round'][1]=get_module_setting("roundbase");
	for ($i=2; $i<=get_module_setting("maxround"); $i++)
		$cost['round'][$i]=$cost['round'][$i-1]+$i*+get_module_setting("roundinc");
	$total+=$cost['round'][get_module_setting("maxround")];


	if (get_module_setting("allowult")) {
		$cost['ult']['allow']=$total;
		$cost['ult']['active']=get_module_setting("ultaprice");
		$total+=$cost['ult']['active'];
	}

	$cost['total']=$total;

	set_module_setting("costarray",serialize($cost));
	set_module_setting("remakecost",0);
}

function calculate_level() {
	global $session;
	$cost = unserialize(get_module_setting("costarray"));
	
	$total = 0;

	if (get_module_setting("allowatk") && get_module_objpref("clans", $session['user']['clanid'],"atkactive")) {
		$total+= $cost['atk']['active'];
		$total+= $cost['atk'][get_module_objpref("clans", $session['user']['clanid'],"atklevel")];
	}

	if (get_module_setting("allowdef") && get_module_objpref("clans", $session['user']['clanid'],"defactive")) {
		$total+= $cost['def']['active'];
		$total+= $cost['def'][get_module_objpref("clans", $session['user']['clanid'],"deflevel")];
	}

	if (get_module_setting("allowdrain") && get_module_objpref("clans", $session['user']['clanid'],"drainactive")) {
		$total+= $cost['drain']['active'];
		$total+= $cost['drain'][get_module_objpref("clans", $session['user']['clanid'],"drainlevel")];
	}

	if (get_module_setting("allowthorn") && get_module_objpref("clans", $session['user']['clanid'],"thornactive")) {
		$total+= $cost['thorn']['active'];
		$total+= $cost['thorn'][get_module_objpref("clans", $session['user']['clanid'],"thornlevel")];
	}

	if (get_module_setting("allowregen") && get_module_objpref("clans", $session['user']['clanid'],"regenactive")) {
		$total+= $cost['regen']['active'];
		$total+= $cost['regen'][get_module_objpref("clans", $session['user']['clanid'],"regenlevel")];
	}

	$total+= $cost['round'][get_module_objpref("clans", $session['user']['clanid'],"roundlevel")];

	if (get_module_setting("allowult") && get_module_objpref("clans", $session['user']['clanid'],"ultactive")) {
		$total+= $cost['ult']['active'];
	}

	if ($total == $cost['ult']['allow']) set_module_objpref("clans", $session['user']['clanid'],"ultready",1);
	$total = round($total/$cost['total']*100,1);
	set_module_objpref("clans", $session['user']['clanid'],"totallevel",$total);
}

function apply_clan_buff() {
	global $session;
	$clanid=$session['user']['clanid'];
	$sql = "SELECT acctid FROM ".db_prefix("accounts")." WHERE clanid = $clanid AND clanrank>0";
	$result = db_query($sql);
	for ($i=0; $i<db_num_rows($result); $i++) {
		$row = db_fetch_assoc($result);
		set_module_pref("refreshbuff",1,"clanbuffs",$row['acctid']);
	}
}
?>