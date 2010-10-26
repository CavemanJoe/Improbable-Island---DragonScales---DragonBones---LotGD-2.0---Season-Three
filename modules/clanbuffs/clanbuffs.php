<?php
	global $session;

	$op = httpget("op");
	$type = httpget("type");
	if(!isset($_POST['action']))
		$action = httpget("action");

	page_header("Clan Buffs");

	switch($op){

		case "enter":
			if(get_module_objpref("clans", $session['user']['clanid'],"buffactive")==0){
				addnav("Clan Buffs");
				addnav("Deposit Gems","runmodule.php?module=clanbuffs&op=deposit");
				addnav(array("Activate Clan Buff (`5%s Gems`0)",get_module_setting("buffaprice")),"runmodule.php?module=clanbuffs&op=activate&type=buff");
				output("`!Clan buffs are extremely expensive, but potentially very powerful assets.`0`n");
				output("`!There are a total of `^%s`! gems in your clan bank.`0`n", get_module_objpref("clans", $session['user']['clanid'],"gems"));
			}
			else{
				calculate_level();
				addnav("Clan Buffs");
				addnav("Deposit Gems","runmodule.php?module=clanbuffs&op=deposit");
				if ($session['user']['clanrank']>=CLAN_LEADER) {
				if (get_module_setting("allowatk")){
				if(get_module_objpref("clans", $session['user']['clanid'],"atkactive")==0 && get_module_objpref("clans", $session['user']['clanid'],"catsactive") < get_module_setting("maxcats")){
					addnav(array("Activate Atk Mult (`5%s Gems`0)",get_module_setting("atkaprice")),"runmodule.php?module=clanbuffs&op=activate&type=atk");
				}elseif (get_module_objpref("clans", $session['user']['clanid'],"atkactive")==1 && get_module_objpref("clans", $session['user']['clanid'],"atklevel")<get_module_setting("maxatk")){
					addnav(array("Upgrade Atk Mult (`5%s Gems`0)",get_module_setting("atkbase")+(get_module_objpref("clans", $session['user']['clanid'],"atklevel")+1)*get_module_setting("atkinc")),"runmodule.php?module=clanbuffs&op=upgrade&type=atk");
				} }

				if (get_module_setting("allowdef")){
				if(get_module_objpref("clans", $session['user']['clanid'],"defactive")==0 && get_module_objpref("clans", $session['user']['clanid'],"catsactive") < get_module_setting("maxcats")){
					addnav(array("Activate Def Mult (`5%s Gems`0)",get_module_setting("defaprice")),"runmodule.php?module=clanbuffs&op=activate&type=def");
				}elseif (get_module_objpref("clans", $session['user']['clanid'],"defactive")==1 && get_module_objpref("clans", $session['user']['clanid'],"deflevel")<get_module_setting("maxdef")){
					addnav(array("Upgrade Def Mult (`5%s Gems`0)",get_module_setting("defbase")+(get_module_objpref("clans", $session['user']['clanid'],"deflevel")+1)*get_module_setting("definc")),"runmodule.php?module=clanbuffs&op=upgrade&type=def");
				} }

				if (get_module_setting("allowdrain")){
				if(get_module_objpref("clans", $session['user']['clanid'],"drainactive")==0 && get_module_objpref("clans", $session['user']['clanid'],"catsactive") < get_module_setting("maxcats")){
					addnav(array("Activate Drain HP (`5%s Gems`0)",get_module_setting("drainaprice")),"runmodule.php?module=clanbuffs&op=activate&type=drain");
				}elseif (get_module_objpref("clans", $session['user']['clanid'],"drainactive")==1 && get_module_objpref("clans", $session['user']['clanid'],"drainlevel")<get_module_setting("maxdrain")){
					addnav(array("Upgrade Drain HP (`5%s Gems`0)",get_module_setting("drainbase")+(get_module_objpref("clans", $session['user']['clanid'],"drainlevel")+1)*get_module_setting("draininc")),"runmodule.php?module=clanbuffs&op=upgrade&type=drain");
				} }

				if (get_module_setting("allowthorn")){
				if(get_module_objpref("clans", $session['user']['clanid'],"thornactive")==0 && get_module_objpref("clans", $session['user']['clanid'],"catsactive") < get_module_setting("maxcats")){
					addnav(array("Activate Reflect (`5%s Gems`0)",get_module_setting("thornaprice")),"runmodule.php?module=clanbuffs&op=activate&type=thorn");
				}elseif (get_module_objpref("clans", $session['user']['clanid'],"thornactive")==1 && get_module_objpref("clans", $session['user']['clanid'],"thornlevel")<get_module_setting("maxthorn")){
					addnav(array("Upgrade Reflect (`5%s Gems`0)",get_module_setting("thornbase")+(get_module_objpref("clans", $session['user']['clanid'],"thornlevel")+1)*get_module_setting("thorninc")),"runmodule.php?module=clanbuffs&op=upgrade&type=thorn");
				} }

				if (get_module_setting("allowregen")){
				if(get_module_objpref("clans", $session['user']['clanid'],"regenactive")==0 && get_module_objpref("clans", $session['user']['clanid'],"catsactive") < get_module_setting("maxcats")){
					addnav(array("Activate Regen (`5%s Gems`0)",get_module_setting("regenaprice")),"runmodule.php?module=clanbuffs&op=activate&type=regen");
				}elseif (get_module_objpref("clans", $session['user']['clanid'],"regenactive")==1 && get_module_objpref("clans", $session['user']['clanid'],"regenlevel")<get_module_setting("maxregen")){
					addnav(array("Upgrade Regen (`5%s Gems`0)",get_module_setting("regenbase")+(get_module_objpref("clans", $session['user']['clanid'],"regenlevel")+1)*get_module_setting("regeninc")),"runmodule.php?module=clanbuffs&op=upgrade&type=regen");
				} }

				if (get_module_objpref("clans", $session['user']['clanid'],"roundlevel")<get_module_setting("maxround")){
					addnav(array("Upgrade Duration (`5%s Gems`0)",get_module_setting("roundbase")+(get_module_objpref("clans", $session['user']['clanid'],"roundlevel")+1)*get_module_setting("roundinc")),"runmodule.php?module=clanbuffs&op=upgrade&type=round");
				}

				if (get_module_setting("allowult")) {
				if (!get_module_objpref("clans", $session['user']['clanid'],"ultactive") && get_module_setting("allowult") && get_module_objpref("clans", $session['user']['clanid'],"ultready")){
					addnav(array("Infinite Duration (`5%s Gems`0)",get_module_setting("ultaprice")),"runmodule.php?module=clanbuffs&op=activate&type=ult");
				}				
				}}

                  	output("`!Clan buffs are extremely expensive, but potentially very powerful assets.`0`n");
			output("`!There are a total of `^%s`! gems in your clan bank.`0`n`n", get_module_objpref("clans", $session['user']['clanid'],"gems"));
                  	output("`!The stats of your clan's buff are as follows:`0`n");

				if (get_module_setting("allowatk")) {
				if(get_module_objpref("clans", $session['user']['clanid'],"atkactive")){
					output("Attack Multiplier: `!`b%sx (Level %s)`b`0`n",1+get_module_setting("eatkbase")+get_module_objpref("clans", $session['user']['clanid'],"atklevel")*get_module_setting("eatkinc"),get_module_objpref("clans", $session['user']['clanid'],"atklevel")==get_module_setting("maxatk")?"Max":get_module_objpref("clans", $session['user']['clanid'],"atklevel"));
				}else{
					output("Attack Multiplier: `b`)Not Active`b`0`n");
				} }

				if (get_module_setting("allowdef")) {
				if(get_module_objpref("clans", $session['user']['clanid'],"defactive")){
					output("Defense Multiplier: `!`b%sx (Level %s)`b`0`n",1+get_module_setting("edefbase")+get_module_objpref("clans", $session['user']['clanid'],"deflevel")*get_module_setting("edefinc"),get_module_objpref("clans", $session['user']['clanid'],"deflevel")==get_module_setting("maxdef")?"Max":get_module_objpref("clans", $session['user']['clanid'],"deflevel"));
				}else{
					output("Defense Multiplier: `b`)Not Active`b`0`n");
				} }

				if (get_module_setting("allowdrain")) {
				if(get_module_objpref("clans", $session['user']['clanid'],"drainactive")){
					output("HP Drain: `!`b%s (Level %s)`b`0`n",100*(get_module_setting("edrainbase")+get_module_objpref("clans", $session['user']['clanid'],"drainlevel")*get_module_setting("edraininc"))."%",get_module_objpref("clans", $session['user']['clanid'],"drainlevel")==get_module_setting("maxdrain")?"Max":get_module_objpref("clans", $session['user']['clanid'],"drainlevel"));
				}else{
					output("HP Drain: `b`)Not Active`b`0`n");
				} }

				if (get_module_setting("allowthorn")) {
				if(get_module_objpref("clans", $session['user']['clanid'],"thornactive")){
					output("Damage Reflection: `!`b%s (Level %s)`b`0`n",100*(get_module_setting("ethornbase")+get_module_objpref("clans", $session['user']['clanid'],"thornlevel")*get_module_setting("ethorninc"))."%",get_module_objpref("clans", $session['user']['clanid'],"thornlevel")==get_module_setting("maxthorn")?"Max":get_module_objpref("clans", $session['user']['clanid'],"thornlevel"));
				}else{
					output("Damage Reflection: `b`)Not Active`b`0`n");
				} }

				if (get_module_setting("allowregen")) {
				if(get_module_objpref("clans", $session['user']['clanid'],"regenactive")){
					output("Regeneration: `!`b%sHP/Level (Level %s)`b`0`n",get_module_setting("eregenbase")+get_module_objpref("clans", $session['user']['clanid'],"regenlevel")*get_module_setting("eregeninc"),get_module_objpref("clans", $session['user']['clanid'],"regenlevel")==get_module_setting("maxregen")?"Max":get_module_objpref("clans", $session['user']['clanid'],"regenlevel"));
				}else{
					output("Regeneration: `b`)Not Active`b`0`n");
				} }

				if(get_module_setting("allowult") && get_module_objpref("clans", $session['user']['clanid'],"ultactive")){
					output("Duration: `!`bInfinite (Level Max)`b`0`n");
				}else{
					output("Duration: `!`b%s Rounds (Level %s)`b`0`n",get_module_setting("eroundbase")+get_module_objpref("clans", $session['user']['clanid'],"roundlevel")*get_module_setting("eroundinc"),get_module_objpref("clans", $session['user']['clanid'],"roundlevel")==get_module_setting("maxround")?"Max":get_module_objpref("clans", $session['user']['clanid'],"roundlevel"));
				}
				$total=get_module_objpref("clans", $session['user']['clanid'],"totallevel")."%";
				if ($total==100) output("Total Strength: `b`@%s`b`0`n",$total);
				elseif ($total>=90) output("Total Strength: `b`2%s`b`0`n",$total);
				elseif ($total>=80) output("Total Strength: `b`!%s`b`0`n",$total);
				elseif ($total>=70) output("Total Strength: `b`1%s`b`0`n",$total);
				elseif ($total>=60) output("Total Strength: `b`^%s`b`0`n",$total);
				elseif ($total>=50) output("Total Strength: `b`6%s`b`0`n",$total);
				elseif ($total>=40) output("Total Strength: `&%s`0`n",$total);
				elseif ($total>=30) output("Total Strength: `%%s`0`n",$total);
				elseif ($total>=20) output("Total Strength: `\$%s`0`n",$total);
				elseif ($total>=10) output("Total Strength: `4%s`0`n",$total);
				else output("Total Strength: `)%s`0`n",$total);

				
			}		    
		break;


		case "activate":
			addnav("Return to Clan Buffs","runmodule.php?module=clanbuffs&op=enter");
			switch ($type) {
				case "buff":
					$text=translate_inline("become active.");
					break;
				case "atk":
					$text=translate_inline("increase your `!`battack power.");
					break;
				case "def":
					$text=translate_inline("increase your `!`bdefense power.");
					break;
				case "drain":
					$text=translate_inline("`!`bdrain the enemies life.");
					break;
				case "thorn":
					$text=translate_inline("`!`breflect damage.");
					break;
				case "regen":
					$text=translate_inline("`!`bregenerate your life.");
					break;
				case "ult":
					$text=translate_inline("be `!`bpermanently active!");
					break;
			}
			$price=get_module_setting($type."aprice");
			$gems=get_module_objpref("clans", $session['user']['clanid'],"gems");
			if($gems>=$price){
				$gems-=$price;
				increment_module_objpref("clans",$session['user']['clanid'],"catsactive");
				set_module_objpref("clans", $session['user']['clanid'],$type."active",1);
				set_module_objpref("clans", $session['user']['clanid'],"gems",$gems);
				output("You can feel the power of your clan's aura gathering around you.  Your clan's buff will now %s`b`0",$text);
				apply_clan_buff();
			} else {
				output("`\$Your clan bank doesn't have enough gems to purchase the activation!`0");
			}
		break;

		case "upgrade":
			addnav("Return to Clan Buffs","runmodule.php?module=clanbuffs&op=enter");
			switch ($type) {
				case "atk":
					$text=translate_inline("attack power");
					break;
				case "def":
					$text=translate_inline("defense power");
					break;
				case "drain":
					$text=translate_inline("drain power");
					break;
				case "thorn":
					$text=translate_inline("reflection power");
					break;
				case "regen":
					$text=translate_inline("regeneration power");
					break;
				case "round":
					$text=translate_inline("duration");
					break;
			}
			
			$level=get_module_objpref("clans", $session['user']['clanid'],$type."level")+1;
			$max=get_module_setting("max".$type);
			$price=get_module_setting($type."base")+$level*get_module_setting($type."inc");
			$gems=get_module_objpref("clans", $session['user']['clanid'],"gems");
			if($gems>=$price){
				$gems-=$price;
				set_module_objpref("clans", $session['user']['clanid'],$type."level",$level);
				set_module_objpref("clans", $session['user']['clanid'],"gems",$gems);
				if ($level==$max) {
					output("You can feel the power of your clan's aura gathering around you.  Your clan's buff's %s is now `!`bmaximized!`b`0",$text);
				}else{
					output("You can feel the power of your clan's aura gathering around you.  Your clan's buff's %s is now `!`blevel %s!`b`0",$text,$level);
				}
				apply_clan_buff();
			}else{
				output("`\$Your clan bank doesn't have enough gems to purchase the upgrade!`0");
			}
		break;


	//This part shamelessly borrowed from bankmod.php (which was shamelessly borrowed from bank.php)
	case "deposit":
		addnav("Return to Clan Buffs","runmodule.php?module=clanbuffs&op=enter");
		rawoutput("<form action='runmodule.php?module=clanbuffs&op=depositfinish' method='POST'>");
		output("`!There are a total of `^%s`! gems in your clan bank.`0`n", get_module_objpref("clans", $session['user']['clanid'],"gems"));
		output("`!Searching through all your pockets and pouches, you find that you currently have `^%s`! gems on hand.`n`n", $session['user']['gems']);
		output("`!Deposit how much?");
		$dep = translate_inline("Deposit");
		rawoutput(" <input id='input' name='amount' width=5 > <input type='submit' class='button' value='$dep'>");
		output("`n`iEnter 0 or nothing to deposit all of your gems`i");
		rawoutput("</form>");
		rawoutput("<script language='javascript'>document.getElementById('input').focus();</script>",true);
	  addnav("","runmodule.php?module=clanbuffs&op=depositfinish");
	  break;
	
	case "depositfinish":
		addnav("Return to Clan Buffs","runmodule.php?module=clanbuffs&op=enter");
		$amount = abs((int)httppost('amount'));
		if ($amount==0){
			$amount=$session['user']['gems'];
		}
		$notenough = translate_inline("`\$ERROR: Not enough gems on your person to deposit.");
		$depositbalance= translate_inline("`!Your deposit of `^%s `!gems was successful. There is now a total of `^%s`! gems in your clan bank and `^%s`! gems on your person.`6\"");
		if ($amount>$session['user']['gems']){
			output($notenough);
		}else{
			debuglog("deposited " . $amount . " gems in the clan bank");
			$gems = get_module_objpref("clans", $session['user']['clanid'],"gems");
			$gems+=$amount;
			set_module_objpref("clans", $session['user']['clanid'],"gems",$gems);
			$session['user']['gems']-=$amount;
			output($depositbalance,$amount,$gems,$session['user']['gems']);
		}
	break;
	//end shameless borrowing


	case "hof":
	page_header("Hall of Fame");
	$page = httpget('page');
	$pp = 25;
	$pageoffset = (int)$page;
	if ($pageoffset > 0) $pageoffset--;
	$pageoffset *= $pp;
	$limit = "LIMIT $pageoffset,$pp";
	$sql = "SELECT COUNT(*) AS c FROM " . db_prefix("module_objprefs") . " WHERE modulename = 'clanbuffs' AND setting = 'buffactive' AND value > 0";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$total = $row['c'];
	$count = db_num_rows($result);
	if (($pageoffset + $pp) < $total){
		$cond = $pageoffset + $pp;
	}else{
		$cond = $total;
	}
	$sql = "SELECT ".db_prefix("module_objprefs").".value, ".db_prefix("module_objprefs").".objid, ".db_prefix("clans").".clanname FROM " . db_prefix("module_objprefs") . "," . db_prefix("clans") . " WHERE clanid = objid AND modulename = 'clanbuffs' AND setting = 'totallevel' AND value >= 0 ORDER BY (value+0) DESC $limit";
	$result = db_query($sql);
	$rank = translate_inline("Rank");
	$name = translate_inline("Name");
	$atk = translate_inline("Atk Lv.");
	$def = translate_inline("Def Lv.");
	$drain = translate_inline("Drain Lv.");
	$reflect = translate_inline("Reflect Lv.");
	$regen = translate_inline("Regen Lv.");
	$rounds = translate_inline("Rounds");
	$total = translate_inline("Total");
	output("`b`c`!Strongest Clan Buffs In The Land`n`n`c`b");
	rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
	rawoutput("<tr class='trhead'><td>$rank</td><td>$name</td>");
	if (get_module_setting("allowatk")) rawoutput("<td>$atk</td>");
	if (get_module_setting("allowdef")) rawoutput("<td>$def</td>");
	if (get_module_setting("allowdrain")) rawoutput("<td>$drain</td>");
	if (get_module_setting("allowthorn")) rawoutput("<td>$reflect</td>");
	if (get_module_setting("allowregen")) rawoutput("<td>$regen</td>");
	rawoutput("<td>$rounds</td><td>$total</td></tr>");
	if (db_num_rows($result)>0){
		for($i = $pageoffset; $i < $cond && $count; $i++) {
			$row = db_fetch_assoc($result);
			if ($row['objid']==$session['user']['clanid']){
				rawoutput("<tr class='trhilight'><td>");
			}else{
				rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td>");
			}
			$j=$i+1;
			output_notl("$j.");
			rawoutput("</td><td>");
			output_notl("`&%s`0",$row['clanname']);
			rawoutput("</td><td>");
			if (get_module_setting("allowatk")) {
				$temp=get_module_objpref("clans", $row['objid'],'atklevel','clanbuffs',$row['objid']);
				if ($temp==0) $temp="`)N/A`0";
				output_notl("`c`b`Q%s`c`b`0",$temp);
				rawoutput("</td><td>");
			}
			if (get_module_setting("allowdef")) {
				$temp=get_module_objpref("clans", $row['objid'],'deflevel','clanbuffs',$row['objid']);
				if ($temp==0) $temp="`)N/A`0";
				output_notl("`c`b`Q%s`c`b`0",$temp);
				rawoutput("</td><td>");
			}
			if (get_module_setting("allowdrain")) {
				$temp=get_module_objpref("clans", $row['objid'],'drainlevel','clanbuffs',$row['objid']);
				if ($temp==0) $temp="`)N/A`0";
				output_notl("`c`b`Q%s`c`b`0",$temp);
				rawoutput("</td><td>");
			}
			if (get_module_setting("allowthorn")) {
				$temp=get_module_objpref("clans", $row['objid'],'thornlevel','clanbuffs',$row['objid']);
				if ($temp==0) $temp="`)N/A`0";
				output_notl("`c`b`Q%s`c`b`0",$temp);
				rawoutput("</td><td>");
			}
			if (get_module_setting("allowregen")) {
				$temp=get_module_objpref("clans", $row['objid'],'regenlevel','clanbuffs',$row['objid']);
				if ($temp==0) $temp="`)N/A`0";
				output_notl("`c`b`Q%s`c`b`0",$temp);
				rawoutput("</td><td>");
			}
			if (get_module_objpref("clans", $row['objid'],'ultactive','clanbuffs',$row['objid']))
				$temp=translate_inline("Inf");
			else {
			$temp=get_module_objpref("clans", $row['objid'],'roundlevel','clanbuffs',$row['objid']);
			$temp*=get_module_setting("eroundinc");
			$temp+=get_module_setting("eroundbase");
			}
			output_notl("`c`b`Q%s`c`b`0",$temp);
			rawoutput("</td><td>");
			$total=get_module_objpref("clans", $row['objid'],'totallevel','clanbuffs',$row['objid']);
				if ($total==100) $total="`@".$total."%`0";
				elseif ($total>=90) $total="`2".$total."%`0";
				elseif ($total>=80) $total="`!".$total."%`0";
				elseif ($total>=70) $total="`1".$total."%`0";
				elseif ($total>=60) $total="`^".$total."%`0";
				elseif ($total>=50) $total="`6".$total."%`0";
				elseif ($total>=40) $total="`&".$total."%`0";
				elseif ($total>=30) $total="`%".$total."%`0";
				elseif ($total>=20) $total="`\$".$total."%`0";
				elseif ($total>=10) $total="`4".$total."%`0";
				else $total="`)".$total."%`0";
			output_notl("`c`b%s`c`b`0",$total);
			rawoutput("</td></tr>");
        }
	}
	rawoutput("</table>");
	if ($total>$pp){
		addnav("Pages");
		for ($p=0;$p<$total;$p+=$pp){
			addnav(array("Page %s (%s-%s)", ($p/$pp+1), ($p+1), min($p+$pp,$total)), "runmodule.php?module=clanbuffs&op=hof&page=".($p/$pp+1));
		}
	}
	addnav("Other");
	addnav("Back to HoF", "hof.php");
	break;
	}

	if ($op!="hof") addnav("Return to your Clan Hall","clan.php");
	villagenav();
	page_footer();
?>
