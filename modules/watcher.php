<?php
function watcher_getmoduleinfo(){
	$info = array(
		"name"=>"The Watcher",
		"version"=>"1.0",
		"author"=>"DaveS",
		"category"=>"Administrative",
		"download"=>"",
		"settings"=>array(
			"Watcher,title",
			"location"=>"Where should the Watch Lists appear: Grotto or HoF?,enum,0,Grotto,1,HoF,2,Both|0",
			"pp"=>"Number of listings per page:,int|40",
			"list1"=>"`^Activate Total Gold Watch List (Includes breakdown between gold on hand and gold in bank)?,bool|0",
			"warn1"=>"`^Send notice if Total Gold is greater than or equal to X (0 for no notice):,int|0",
			"list2"=>"`%Activate Gems Watch List?,bool|0",
			"warn2"=>"`%Send notice if Total Gems are greater than or equal to X (0 for no notice):,int|0",
			"list3"=>"`&Activate Charm Watch List?,bool|0",
			"warn3"=>"`&Send notice if Total Charm is greater than or equal to X (0 for no notice):,int|0",
			"list4"=>"`@Activate Maximum Hitpoints Watch List?,bool|0",
			"`@This is a corrected Maximum Hitpoints Count: It does not include hitpoints gained from levels or DKs,note",
			"warn4"=>"`@Send notice if Maximum Hitpoints is greater than or equal to X (0 for no notice):,int|0",
			"list5"=>"`^Activate Attack Watch List?,bool|0",
			"`^This is a corrected Attack: It does not include attack gained from levels/weapon/DKs,note",
			"warn5"=>"`^Send notice if Attack is greater than or equal to X (0 for no notice):,int|0",
			"list6"=>"`6Activate Weapon Damage Watch List?,bool|0",
			"warn6"=>"`6Send notice if Weapon Damage is greater than or equal to X (0 for no notice):,int|0",
			"list7"=>"`#Activate Defense Watch List?,bool|0",
			"`#This is a corrected Defense: It does not include defense gained from levels/armor/DKs,note",
			"warn7"=>"`#Send notice if Defense is greater than or equal to X (0 for no notice):,int|0",
			"list8"=>"`3Activate Armor Defense Watch List?,bool|0",
			"warn8"=>"`3Send notice if Armor Defense is greater than or equal to X (0 for no notice):,int|0",
			"list9"=>"`2Activate Alignment Watch List?,bool|0",
			"warn9"=>"`2Send notice if Alignment is greater than or equal to X (0 for no notice):,int|0",
			"warn10"=>"`2Send notice if Alignment is less than X (0 for no notice):,int|0",
		),
		"prefs"=>array(
			"Watcher,title",
			"allow"=>"Allow this player to review allowed HoFs?,bool|0",
			"receive"=>"Should this player receive warning letters about players?,bool|0",
			"warn1"=>"Has a warning been sent out regarding this player's Gold?,enum,0,Not Needed,1,Sent,2,Disregard|0",
			"warn2"=>"Has a warning been sent out regarding this player's Gems?,enum,0,Not Needed,1,Sent,2,Disregard|0",
			"warn3"=>"Has a warning been sent out regarding this player's Charm?,enum,0,Not Needed,1,Sent,2,Disregard|0",
			"warn4"=>"Has a warning been sent out regarding this player's Max Hitpoints?,enum,0,Not Needed,1,Sent,2,Disregard|0",
			"warn5"=>"Has a warning been sent out regarding this player's Attack?,enum,0,Not Needed,1,Sent,2,Disregard|0",
			"warn6"=>"Has a warning been sent out regarding this player's Weapon Damage?,enum,0,Not Needed,1,Sent,2,Disregard|0",
			"warn7"=>"Has a warning been sent out regarding this player's Defense?,enum,0,Not Needed,1,Sent,2,Disregard|0",
			"warn8"=>"Has a warning been sent out regarding this player's Armor Defense?,enum,0,Not Needed,1,Sent,2,Disregard|0",
			"warn9"=>"Has a warning been sent out regarding this player's Alignment?,enum,0,Not Needed,1,Sent,2,Disregard|0",
		),
	);
	return $info;
}
function watcher_install(){
	module_addhook("footer-hof");
	module_addhook("footer-user");
	module_addhook("superuser");
	module_addhook("newday");
	return true;
}
function watcher_uninstall(){
	return true;
}
function watcher_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "footer-hof":
			if (($session['user']['superuser'] & SU_EDIT_USERS) && get_module_setting("location")>0){
				addnav("Monitoring Lists");
				addnav("Watch Lists","runmodule.php?module=watcher&op=hof&op2=1");
				addnav("Warning Lists","runmodule.php?module=watcher&op=hofwarn&op2=1");
				addnav("Disregard Lists","runmodule.php?module=watcher&op=hofdisregard&op2=1");
				addnav("DK Point Spending","runmodule.php?module=watcher&op=hofdkpointall&op2=1");
			}
		break;
		case "superuser":
			if (($session['user']['superuser'] & SU_EDIT_USERS) && (get_module_setting("location")==0 || get_module_setting("location")==2)){
				addnav("Monitoring Lists");
				addnav("Watch Lists","runmodule.php?module=watcher&op=watchlist&op2=1");
				addnav("Warning Lists","runmodule.php?module=watcher&op=warnlist&op2=1");
				addnav("Disregard Lists","runmodule.php?module=watcher&op=disregardlist&op2=1");
				addnav("DK Point Spending","runmodule.php?module=watcher&op=dkpointall&op2=1");
			}	
		break;
		case "footer-user":
			$userid = httpget("userid");
			$op = httpget('op');
			if ($op=="edit"){
				addnav("Operations");
				addnav("Monitoring","runmodule.php?module=watcher&op=monitor&userid=$userid");
				addnav("DK Point Spending","runmodule.php?module=watcher&op=dkpoints&userid=$userid");
			}
		break;
		case "newday":
			//Check to see if this player has too much Gold
			if (get_module_setting("list1")==1){
				if ($session['user']['gold']+$session['user']['goldinbank']>=get_module_setting("warn1") && get_module_setting("warn1")>0 && get_module_pref("warn1")==0){
					set_module_pref("warn1",1);
					$sql = "SELECT acctid,value FROM ".db_prefix('module_userprefs')." LEFT JOIN ".db_prefix('accounts')." ON (acctid = userid) WHERE modulename='watcher' and setting='receive' and value ='1'";
					$result = db_query($sql);
					$title=translate_inline(array("`4Excessive `^Gold`4 Warning: `^%s",$session['user']['name']));
					$mailmessage=translate_inline(array("`\$Warning Notice:`n`4This is a notice that %s`4 has exceeded the set amount of total gold with `^%s gold`4.",$session['user']['name'],$session['user']['gold']+$session['user']['goldinbank']));
					for ($i=0;$i<db_num_rows($result);$i++){
						$row = db_fetch_assoc($result);
						require_once("lib/systemmail.php");
						systemmail($row['acctid'],$title,$mailmessage);
					}
				}elseif ($session['user']['gold']+$session['user']['goldinbank']<get_module_setting("warn1") && get_module_setting("warn1")>0 && get_module_pref("warn1")==1){
					set_module_pref("warn1",0);
				}
			}
			//Check to see if this player has too many Gems
			if (get_module_setting("list2")==1){
				if ($session['user']['gems']>=get_module_setting("warn2") && get_module_setting("warn2")>0 && get_module_pref("warn2")==0){
					set_module_pref("warn2",1);
					$sql = "SELECT acctid,value FROM ".db_prefix('module_userprefs')." LEFT JOIN ".db_prefix('accounts')." ON (acctid = userid) WHERE modulename='watcher' and setting='receive' and value ='1'";
					$result = db_query($sql);
					$mailmessage=translate_inline(array("`\$Warning Notice:`n`4This is a notice that %s`4 has exceeded the set amount of total gems with `^%s `%gems`4.",$session['user']['name'],$session['user']['gems']));
					$title=translate_inline(array("`4Excessive `%Gems `4Warning: `^%s",$session['user']['name']));
					for ($i=0;$i<db_num_rows($result);$i++){
						$row = db_fetch_assoc($result);
						require_once("lib/systemmail.php");
						systemmail($row['acctid'],$title,$mailmessage);
					}
				}elseif ($session['user']['gems']<get_module_setting("warn2") && get_module_setting("warn2")>0 && get_module_pref("warn2")==1){
					set_module_pref("warn2",0);
				}
			}
			//Check to see if this player has too much charm
			if (get_module_setting("list3")==1){
				if ($session['user']['charm']>=get_module_setting("warn3") && get_module_setting("warn3")>0 && get_module_pref("warn3")==0){
					set_module_pref("warn3",1);
					$sql = "SELECT acctid,value FROM ".db_prefix('module_userprefs')." LEFT JOIN ".db_prefix('accounts')." ON (acctid = userid) WHERE modulename='watcher' and setting='receive' and value ='1'";
					$result = db_query($sql);
					$mailmessage=translate_inline(array("`\$Warning Notice:`n`4This is a notice that %s`4 has exceeded the set amount of total charm with `^%s `&charm`4.",$session['user']['name'],$session['user']['charm']));
					$title=translate_inline(array("`4Excessive `&Charm `4Warning: `^%s",$session['user']['name']));
					for ($i=0;$i<db_num_rows($result);$i++){
						$row = db_fetch_assoc($result);
						require_once("lib/systemmail.php");
						systemmail($row['acctid'],$title,$mailmessage);
					}
				}elseif ($session['user']['charm']<get_module_setting("warn3") && get_module_setting("warn3")>0 && get_module_pref("warn3")==1){
					set_module_pref("warn3",0);
				}
			}
			//Check to see if this player has too many Max Hitpoints
			if (get_module_setting("list4")==1){
				$dkpoints = 0;
				reset($session['user']['dragonpoints']);
				while(list($key,$val)=each($session['user']['dragonpoints'])){
					if ($val=="hp") $dkpoints+=5;
				}
				$hps=$session['user']['maxhitpoints'] - $dkpoints - ($session['user']['level']*10);
				if ($hps>=get_module_setting("warn4") && get_module_setting("warn4")>0 && get_module_pref("warn4")==0){
					set_module_pref("warn4",1);
					$sql = "SELECT acctid,value FROM ".db_prefix('module_userprefs')." LEFT JOIN ".db_prefix('accounts')." ON (acctid = userid) WHERE modulename='watcher' and setting='receive' and value ='1'";
					$result = db_query($sql);
					$mailmessage=translate_inline(array("`\$Warning Notice:`n`4This is a notice that %s`4 has exceeded the set amount of Maximum Hitpoints with `^%s `% Extra Max HPs`4.",$session['user']['name'],$hps));
					$title=translate_inline(array("`4Excessive `&Max Hitpoints `4Warning: `^%s",$session['user']['name']));
					for ($i=0;$i<db_num_rows($result);$i++){
						$row = db_fetch_assoc($result);
						require_once("lib/systemmail.php");
						systemmail($row['acctid'],translate_inline("`4Excessive `& `4Warning"),$mailmessage);
						systemmail($row['acctid'],$title,$mailmessage);
					}
				}elseif ($hps<get_module_setting("warn4") && get_module_setting("warn4")>0 && get_module_pref("warn4")==1){
					set_module_pref("warn4",0);
				}
			}
			//Check to see if this player has too much attack
			if (get_module_setting("list5")==1){
				$at = 0;
				reset($session['user']['dragonpoints']);
				while(list($key,$val)=each($session['user']['dragonpoints'])){
					if ($val=="at") $at++;
				}
				$atk=$session['user']['attack'] - $at - $session['user']['level'] - $session['user']['weapondmg'];
				if ($atk>=get_module_setting("warn5") && get_module_setting("warn5")>0 && get_module_pref("warn5")==0){
					set_module_pref("warn5",1);
					$sql = "SELECT acctid,value FROM ".db_prefix('module_userprefs')." LEFT JOIN ".db_prefix('accounts')." ON (acctid = userid) WHERE modulename='watcher' and setting='receive' and value ='1'";
					$result = db_query($sql);
					$mailmessage=translate_inline(array("`\$Warning Notice:`n`4This is a notice that %s`4 has exceeded the set amount of Attack points with `^%s `%Extra Attack Points`4.",$session['user']['name'],$atk));
					$title=translate_inline(array("`4Excessive `&Attack `4Warning: `^%s",$session['user']['name']));
					for ($i=0;$i<db_num_rows($result);$i++){
						$row = db_fetch_assoc($result);
						require_once("lib/systemmail.php");
						systemmail($row['acctid'],$title,$mailmessage);
					}
				}elseif ($atk<get_module_setting("warn5") && get_module_setting("warn5")>0 && get_module_pref("warn5")==1){
					set_module_pref("warn5",0);
				}
			}
			//Check to see if this player has too much weapon damage
			if (get_module_setting("list6")==1){
				if ($session['user']['weapondmg']>=get_module_setting("warn6") && get_module_setting("warn6")>0 && get_module_pref("warn6")==0){
					set_module_pref("warn6",1);
					$sql = "SELECT acctid,value FROM ".db_prefix('module_userprefs')." LEFT JOIN ".db_prefix('accounts')." ON (acctid = userid) WHERE modulename='watcher' and setting='receive' and value ='1'";
					$result = db_query($sql);
					$mailmessage=translate_inline(array("`\$Warning Notice:`n`4This is a notice that %s`4 has exceeded the set amount of Weapon Damage points with a weapon with an attack of `^%s`4.",$session['user']['name'],$session['user']['weapondmg']));
					$title=translate_inline(array("`4Excessive `&Weapon Damage `4Warning: `^%s",$session['user']['name']));
					for ($i=0;$i<db_num_rows($result);$i++){
						$row = db_fetch_assoc($result);
						require_once("lib/systemmail.php");
						systemmail($row['acctid'],$title,$mailmessage);
					}
				}elseif ($session['user']['weapondmg']<get_module_setting("warn6") && get_module_setting("warn6")>0 && get_module_pref("warn6")==1){
					set_module_pref("warn6",0);
				}
			}
			//Check to see if this player has too much defense
			if (get_module_setting("list7")==1){
				$de = 0;
				reset($session['user']['dragonpoints']);
				while(list($key,$val)=each($session['user']['dragonpoints'])){
					if ($val=="de") $de++;
				}
				$def=$session['user']['defense'] - $de - $session['user']['level'] - $session['user']['armordef'];
				if ($def>=get_module_setting("warn7") && get_module_setting("warn7")>0 && get_module_pref("warn7")==0){
					set_module_pref("warn6",1);
					$sql = "SELECT acctid,value FROM ".db_prefix('module_userprefs')." LEFT JOIN ".db_prefix('accounts')." ON (acctid = userid) WHERE modulename='watcher' and setting='receive' and value ='1'";
					$result = db_query($sql);
					$mailmessage=translate_inline(array("`\$Warning Notice:`n`4This is a notice that %s`4 has exceeded the set amount of Defense points with `^%s `%Extra Defense Points`4.",$session['user']['name'],$def));
					$title=translate_inline(array("`4Excessive `&Defense `4Warning: `^%s",$session['user']['name']));
					for ($i=0;$i<db_num_rows($result);$i++){
						$row = db_fetch_assoc($result);
						require_once("lib/systemmail.php");
						systemmail($row['acctid'],$title,$mailmessage);
					}
				}elseif ($def<get_module_setting("warn7") && get_module_setting("warn7")>0 && get_module_pref("warn7")==1){
					set_module_pref("warn7",0);
				}
			}
			//Check to see if this player has too much Armor Defense
			if (get_module_setting("list8")==1){
				if ($session['user']['armordef']>=get_module_setting("warn8") && get_module_setting("warn8")>0 && get_module_pref("warn8")==0){
					set_module_pref("warn8",1);
					$sql = "SELECT acctid,value FROM ".db_prefix('module_userprefs')." LEFT JOIN ".db_prefix('accounts')." ON (acctid = userid) WHERE modulename='watcher' and setting='receive' and value ='1'";
					$result = db_query($sql);
					$mailmessage=translate_inline(array("`\$Warning Notice:`n`4This is a notice that %s`4 has exceeded the set amount of Weapon Damage points with a weapon with an attack of `^%s`4.",$session['user']['name'],$session['user']['weapondmg']));
					$title=translate_inline(array("`4Excessive `&Weapon Damage `4Warning: `^%s",$session['user']['name']));
					for ($i=0;$i<db_num_rows($result);$i++){
						$row = db_fetch_assoc($result);
						require_once("lib/systemmail.php");
						systemmail($row['acctid'],$title,$mailmessage);
					}
				}elseif ($session['user']['armordef']<get_module_setting("warn8") && get_module_setting("warn8")>0 && get_module_pref("warn8")==1){
					set_module_pref("warn8",0);
				}
			}
			//Check to see if player has too high/low of Alignment
			if (get_module_setting("list9")==1 && is_module_active("alignment")){
				$align=get_module_setting("alignment","alignment");
				if ($align>=get_module_setting("warn9") && get_module_setting("warn9")<>0 && get_module_pref("warn9")==0) $fault=1;
				elseif ($align<=get_module_setting("warn10") && get_module_setting("warn10")<>0 && get_module_pref("warn10")==0) $fault=2;
				else $fault=0;
				if ($fault>0){
					set_module_pref("warn9",1);
					$sql = "SELECT acctid,value FROM ".db_prefix('module_userprefs')." LEFT JOIN ".db_prefix('accounts')." ON (acctid = userid) WHERE modulename='watcher' and setting='receive' and value ='1'";
					$result = db_query($sql);
					if ($fault==1){
						$mailmessage=translate_inline(array("`\$Warning Notice:`n`4This is a notice that %s`4 has exceeded the set amount of Good Alignment points with an alignment `^%s`4.",$session['user']['name'],$align));
						$title=translate_inline(array("`4Excessive `@Good Alignment `4Warning: `^%s",$session['user']['name']));
					}else{
						$mailmessage=translate_inline(array("`\$Warning Notice:`n`4This is a notice that %s`4 has exceeded the set amount of Evil Alignment points with an alignment of `^%s`4.",$session['user']['name'],$align));
						$title=translate_inline(array("`4Excessive `\$Evil Alignment `4Warning: `^%s",$session['user']['name']));
					}
					for ($i=0;$i<db_num_rows($result);$i++){
						$row = db_fetch_assoc($result);
						require_once("lib/systemmail.php");
						systemmail($row['acctid'],$title,$mailmessage);
					}
				}elseif ($align<get_module_setting("warn9") && get_module_setting("warn9")>0 && get_module_pref("warn9")==1) set_module_pref("warn9",0);
				elseif ($align<get_module_setting("warn10") && get_module_setting("warn10")>0 && get_module_pref("warn10")==1) set_module_pref("warn10",0);
			}
		break;
	}
	return $args;
}
function watcher_run(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	$id = httpget("userid");
if ($op=="dkpointall"||$op=="hofdkpointall"){

}
if ($op=="dkpoints"){
	page_header("DK Point Usage");
	$subop1 = httpget('subop1');
	if ($id=="") $id=1;
	output("Search for Another Player: ");
	$search = translate_inline("Search");
	rawoutput("<form action='runmodule.php?module=watcher&op=dkpoints&subop1=search&userid=$id' method='POST'><input name='name' id='name'><input type='submit' class='button' value='$search'></form>");
	addnav("","runmodule.php?module=watcher&op=dkpoints&subop1=search&userid=$id");
	if ($subop1=="search"){
		$search = "%";
		$name = httppost('name');
		for ($i=0;$i<strlen($name);$i++){
			$search.=substr($name,$i,1)."%";
		}
		$sql = "SELECT acctid,name,level,login,dragonkills FROM " . db_prefix("accounts") . " WHERE (locked=0 AND name LIKE '$search') ORDER BY level DESC";
		$result = db_query($sql);
		$max = db_num_rows($result);
		if ($max > 100) {
			output("^Listing first 100:");
			$max = 100;
		}
		$o = translate_inline("Op");
		$n = translate_inline("Name");
		$l = translate_inline("Login");
		$a = translate_inline("AcctID");
		$le = translate_inline("Level");
		$dk = translate_inline("Dragonkills");
		rawoutput("<table align=center> <tr class='trhead'><td>$o</td><td>$n</td><td>$l</td><td>$a</td><td>$le</td><td>$dk</td></tr>");
		for ($i=0;$i<$max;$i++){
			$n=$n+1;
			$row = db_fetch_assoc($result);
			$playerid=$row['acctid'];
			rawoutput("<tr class='".($n%2?"trdark":"trlight")."'><td><a href='runmodule.php?module=watcher&op=dkpoints&userid=$playerid'>");
			output_notl("[ Select ]");
			rawoutput("</a></td><td>");
			output_notl("`&%s", $row['name']);
			rawoutput("</td><td>");
			output_notl("%s", $row['login']);
			rawoutput("</td><td align=center>");
			output_notl("%s", $row['acctid']);
			rawoutput("</td><td align=center>");
			output_notl("`^%s", $row['level']);
			rawoutput("</td><td align=center>");
			output_notl("`^%s", $row['dragonkills']);
			rawoutput("</td></tr>");
			addnav("","runmodule.php?module=watcher&op=dkpoints&userid=$playerid");
		}
		rawoutput("</table>");
	}else{
		$sql = "SELECT acctid,level,name,dragonpoints,dragonkills FROM ".db_prefix("accounts")." WHERE acctid='$id'";
		$res = db_query($sql);
		$row = db_fetch_assoc($res);
		$gname = $row['name'];
		output("`b`c`&DK Point Use for `^%s`b`c",$gname);
		output("`c`iReminder: 1 DK Point gives 5 Max Hitpoints`c`i`n");
		$tname=translate_inline("Name");
		$tlevel=translate_inline("Level");
		$tdks=translate_inline("Dragon Kills");
		$thp=translate_inline("Max Hitpoints");
		$tff=translate_inline("Turns");
		$tatk=translate_inline("Attack");
		$tdef=translate_inline("Defense");
		rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
		rawoutput("<tr class='trhead'><td>$tname</td><td>$tlevel</td><td>$tdks</td><td>$thp</td><td>$tff</td><td>$tatk</td><td>$tdef</td></tr>");
		$dkpoints = 0;
		$atk=0;
		$def=0;
		$turn=0;
		$dp = @unserialize($row["dragonpoints"]);
		if (!is_array($dp)) $dp = array();
		reset($dp);
		while(list($key,$val)=each($dp)){
			if ($val=="hp") $dkpoints+=5;
			if ($val=="at") $atk++;
			if ($val=="de") $def++;
			if ($val=="ff") $turn++;
		}
		rawoutput("<tr class='trlight'><td>");
		output("`^%s",$row['name']);
		rawoutput("</td><td align='center'>");
		output("`@%s",$row['level']);
		rawoutput("</td><td align='center'>");
		output("`@%s",$row['dragonkills']);
		rawoutput("</td><td align='center'>");
		output("`@%s",$dkpoints);
		rawoutput("</td><td align='center'>");
		output("`@%s",$turn);
		rawoutput("</td><td align='center'>");
		output("`@%s",$atk);
		rawoutput("</td><td align='center'>");
		output("`@%s",$def);
		rawoutput("</td></tr>");
		rawoutput("</table>");
	}
	addnav("Refresh","runmodule.php?module=watcher&op=dkpoints&userid=$id");
	addnav("Monitor User","runmodule.php?module=watcher&op=monitor&userid=$id");
	addnav("Edit User","user.php?op=edit&userid=$id");
	addnav("Return to the Grotto","superuser.php");
	villagenav();
}
if ($op=="monitor"){
	page_header("Monitoring");
	$subop1 = httpget('subop1');
	if ($id=="") $id=1;
	output("Search for Another Player: ");
	$search = translate_inline("Search");
	rawoutput("<form action='runmodule.php?module=watcher&op=monitor&subop1=search&userid=$id' method='POST'><input name='name' id='name'><input type='submit' class='button' value='$search'></form>");
	addnav("","runmodule.php?module=watcher&op=monitor&subop1=search&userid=$id");
	if ($subop1=="search"){
		$search = "%";
		$name = httppost('name');
		for ($i=0;$i<strlen($name);$i++){
			$search.=substr($name,$i,1)."%";
		}
		$sql = "SELECT acctid,name,level,login FROM " . db_prefix("accounts") . " WHERE (locked=0 AND name LIKE '$search') ORDER BY level DESC";
		$result = db_query($sql);
		$max = db_num_rows($result);
		if ($max > 100) {
			output("^Listing first 100:");
			$max = 100;
		}
		$o = translate_inline("Op");
		$n = translate_inline("Name");
		$l = translate_inline("Login");
		$a = translate_inline("AcctID");
		$le = translate_inline("Level");
		rawoutput("<table align=center> <tr class='trhead'><td>$o</td><td>$n</td><td>$l</td><td>$a</td><td>$le</td></tr>");
		for ($i=0;$i<$max;$i++){
			$n=$n+1;
			$row = db_fetch_assoc($result);
			$playerid=$row['acctid'];
			rawoutput("<tr class='".($n%2?"trdark":"trlight")."'><td><a href='runmodule.php?module=watcher&op=monitor&userid=$playerid'>");
			output_notl("[ Select ]");
			rawoutput("</a></td><td>");
			output_notl("`&%s", $row['name']);
			rawoutput("</td><td>");
			output_notl("%s", $row['login']);
			rawoutput("</td><td align=center>");
			output_notl("%s", $row['acctid']);
			rawoutput("</td><td align=center>");
			output_notl("`^%s", $row['level']);
			rawoutput("</td></tr>");
			addnav("","runmodule.php?module=watcher&op=monitor&userid=$playerid");
		}
		rawoutput("</table>");
	}else{
		$sql = "SELECT acctid,name,goldinbank,gold,charm,level,gems,attack,weapon,armor,weapondmg,armordef,defense,dragonpoints,maxhitpoints,level,dragonkills FROM ".db_prefix("accounts")." WHERE acctid='$id'";
		$res = db_query($sql);
		$row = db_fetch_assoc($res);
		$gname = $row['name'];
		output("`b`c`&Monitoring for `^%s`b`c`0`n",$gname);
		output("`b`c`\$Bold Red Indicates Player Has Exceeded Notice Amounts`b`c`n");
		$item=translate_inline("Item");
		$result=translate_inline("Result");
		if ($value>=get_module_setting("warn".$op2) && get_module_setting("warn".$op2)>0) $c="`\$`b";
		else $c="`^";
		rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
		rawoutput("<tr class='trhead'><td>$item</td><td>$result</td></tr>");
		if (get_module_setting("list1")==1){
			$value=$row['goldinbank']+$row['gold'];
			if ($row['goldinbank']+$row['gold']>=get_module_setting("warn1") && get_module_setting("warn1")>0) $c="`\$`b";
			else $c="`^";
			rawoutput("<tr class='trlight'><td>");
			output("`^Total Gold");
			rawoutput("</td><td align='right'>");
			output("%s%s%s",$c,$row['goldinbank']+$row['gold'],$c);
			rawoutput("</td></tr><tr class='trlight'><td>");
			output("`^Gold On Hand");
			rawoutput("</td><td align='right'>");
			output("`^%s",$row['gold']);
			rawoutput("</td></tr><tr class='trlight'><td>");
			output("`^Gold In Bank");
			rawoutput("</td><td align='right'>");
			output("`^%s",$row['goldinbank']);
			rawoutput("</td></tr>");
		}
		if (get_module_setting("list2")==1){
			$value=$row['gems'];
			if ($value>=get_module_setting("warn2") && get_module_setting("warn2")>0) $c="`\$`b";
			else $c="`%";
			rawoutput("<tr class='trlight'><td>");
			output("`%Gems");
			rawoutput("</td><td align='right'>");
			output("%s%s%s",$c,$value,$c);
			rawoutput("</td></tr>");
		}
		if (get_module_setting("list3")==1){
			$value=$row['charm'];
			if ($value>=get_module_setting("warn3") && get_module_setting("warn3")>0) $c="`\$`b";
			else $c="`&";
			rawoutput("<tr class='trlight'><td>");
			output("`&Charm");
			rawoutput("</td><td align='right'>");
			output("%s%s%s",$c,$value,$c);
			rawoutput("</td></tr>");
		}
		if (get_module_setting("list4")==1){
			$dkpoints = 0;
			$value=0;
			$dp = @unserialize($row["dragonpoints"]);
			if (!is_array($dp)) $dp = array();
			reset($dp);
			while(list($key,$val)=each($dp)){
				if ($val=="hp") $dkpoints+=5;
			}
			$value=$row['maxhitpoints'] - $dkpoints - ($row['level']*10);
			if ($value>=get_module_setting("warn4") && get_module_setting("warn4")>0) $c="`\$`b";
			else $c="`@";
			rawoutput("<tr class='trlight'><td>");
			output("`@Total Max Hitpoints");
			rawoutput("</td><td align='right'>");
			output("`@%s",$row['maxhitpoints']);
			rawoutput("</td></tr><tr class='trlight'><td>");
			output("`@Corrected Max Hitpoints");
			rawoutput("</td><td align='right'>");
			output("%s%s%s",$c,$value,$c);
			rawoutput("</td></tr><tr class='trlight'><td>");
			output("`@Hitpoints from DKs");
			rawoutput("</td><td align='right'>");
			output("`@%s",$dkpoints);
			rawoutput("</td></tr><tr class='trlight'><td>");
			output("`@Hitpoints from Level");
			rawoutput("</td><td align='right'>");
			output("`@%s",$row['level']*10);
			rawoutput("</td></tr>");
		}
		if (get_module_setting("list5")==1 || get_module_setting("list6")==1){
			$dkatk = 0;
			$atk = 0;
			$dp = @unserialize($row["dragonpoints"]);
			if (!is_array($dp)) $dp = array();
			reset($dp);
			while(list($key,$val)=each($dp)){
				if ($val=="at") $dkatk++;	
			}
			$atk=$row['attack'] - $dkatk - $row['level'] - $row['weapondmg'];
			if ($atk>=get_module_setting("warn5") && get_module_setting("warn5")>0) $c="`\$`b";
			else $c="`&";
			if ($row['weapondmg']>=get_module_setting("warn6") && get_module_setting("warn6")>0) $d="`\$`b";
			else $d="`&";
			rawoutput("<tr class='trlight'><td>");
			output("`&Total Attack");
			rawoutput("</td><td align='right'>");
			output("`&%s",$row['attack']);
			rawoutput("</td></tr><tr class='trlight'><td>");
			output("`&Corrected Attack");
			rawoutput("</td><td align='right'>");
			output("%s%s%s",$c,$atk,$c);
			rawoutput("</td></tr><tr class='trlight'><td>");
			output("`&Attack from DKs");
			rawoutput("</td><td align='right'>");
			output("`&%s",$dkatk);
			rawoutput("</td></tr><tr class='trlight'><td>");
			output("`&Attack from Level");
			rawoutput("</td><td align='right'>");
			output("`&%s",$row['level']);
			rawoutput("</td></tr><tr class='trlight'><td>");
			output("`&Attack from Weapon");
			rawoutput("</td><td align='right'>");
			output("%s%s%s",$d,$row['weapondmg'],$d);
			rawoutput("</td></tr><tr class='trlight'><td>");
			output("`&Weapon");
			rawoutput("</td><td align='right'>");
			output("`&%s",$row['weapon']);
			rawoutput("</td></tr>");
		}
		if (get_module_setting("list7")==1 || get_module_setting("list8")==1){
			$dkdef = 0;
			$def = 0;
			$dp = @unserialize($row["dragonpoints"]);
			if (!is_array($dp)) $dp = array();
			reset($dp);
			while(list($key,$val)=each($dp)){
				if ($val=="de") $dkdef++;	
			}
			$def=$row['defense'] - $dkdef - $row['level'] - $row['armordef'];
			if ($def>=get_module_setting("warn7") && get_module_setting("warn7")>0) $c="`\$`b";
			else $c="`#";
			if ($row['armordef']>=get_module_setting("warn8") && get_module_setting("warn8")>0) $d="`\$`b";
			else $d="`#";
			rawoutput("<tr class='trlight'><td>");
			output("`#Total Defense");
			rawoutput("</td><td align='right'>");
			output("`#%s",$row['defense']);
			rawoutput("</td></tr><tr class='trlight'><td>");
			output("`#Corrected Defense");
			rawoutput("</td><td align='right'>");
			output("%s%s%s",$c,$def,$c);
			rawoutput("</td></tr><tr class='trlight'><td>");
			output("`#Defense from DKs");
			rawoutput("</td><td align='right'>");
			output("`#%s",$dkdef);
			rawoutput("</td></tr><tr class='trlight'><td>");
			output("`#Defense from Level");
			rawoutput("</td><td align='right'>");
			output("`#%s",$row['level']);
			rawoutput("</td></tr><tr class='trlight'><td>");
			output("`#Defense from Armor");
			rawoutput("</td><td align='right'>");
			output("%s%s%s",$d,$row['armordef'],$d);
			rawoutput("</td></tr><tr class='trlight'><td>");
			output("`#Armor");
			rawoutput("</td><td align='right'>");
			output("`#%s",$row['armor']);
			rawoutput("</td></tr>");
		}
		if (get_module_setting("list9")==1 && is_module_active("alignment")){
			$value=get_module_pref("alignment","alignment");
			if ($value>=get_module_setting("warn9") && get_module_setting("warn9")>0) $c="`\$`b";
			elseif ($value<=get_module_setting("warn10") && get_module_setting("warn10")>0) $c="`\$`b";
			else $c="`&";
			rawoutput("<tr class='trlight'><td>");
			output("`^Alignment");
			rawoutput("</td><td align='right'>");
			output("%s%s%s",$c,$value,$c);
			rawoutput("</td></tr>");
		}
		rawoutput("</table>");
	}
	addnav("Refresh","runmodule.php?module=watcher&op=monitor&userid=$id");
	addnav("DK Point Use","runmodule.php?module=watcher&op=dkpoints&userid=$id");
	addnav("Edit User","user.php?op=edit&userid=$id");
	addnav("Return to the Grotto","superuser.php");
	villagenav();
}
if ($op=="dkpointall"||$op=="hofdkpointall"){
	addnav("Monitoring");
	if ($op2=="") $op2=1;
	if ($op=="hofdkpointall"){
		addnav("Watch Lists","runmodule.php?module=watcher&op=hof&op2=$op2");
		addnav("Warn Lists","runmodule.php?module=watcher&op=hofwarn&op2=$op2");
		addnav("Disregard Lists","runmodule.php?module=watcher&op=hofdisregard&op2=$op2");
		addnav("Navigation");
		addnav("Back to HoF", "hof.php");
	}else{
		addnav("Watch Lists","runmodule.php?module=watcher&op=watchlist&op2=$op2");
		addnav("Warn Lists","runmodule.php?module=watcher&op=warnlist&op2=$op2");
		addnav("Disregard Lists","runmodule.php?module=watcher&op=disregardlist&op2=$op2");
		addnav("Navigation");
		addnav("Return to the Grotto","superuser.php");
	}
	villagenav();
	page_header("DK Point Distribution");
	$perpage=get_module_setting("pp");
	$subop = httpget('subop');
	if ($subop=="") $subop=1;
	$min = (($subop-1)*$perpage);
	$max = $perpage*$subop;
	$sql = "SELECT acctid,level,name,dragonpoints,dragonkills FROM ".db_prefix("accounts")."";
	$res = db_query($sql);
	$number=0;
	$new_array = array();
	$new_array2 = array();
	$new_array3 = array();
	$new_array4 = array();
	$new_array5 = array();
	$new_array6 = array();
	$new_array7 = array();
	for ($i=0;$i<db_num_rows($res);$i++){
		$row = db_fetch_assoc($res);
		$dkpoints = 0;
		$atk=0;
		$def=0;
		$turn=0;
		$dp = @unserialize($row["dragonpoints"]);
		if (!is_array($dp)) $dp = array();
		reset($dp);
		while(list($key,$val)=each($dp)){
			if ($val=="hp") $dkpoints+=5;
			if ($val=="at") $atk++;
			if ($val=="de") $def++;
			if ($val=="ff") $turn++;
		}
		
		$new_array[$row['name']] = $row['dragonkills'];
		$new_array2[$row['name']] = $row['acctid'];
		$new_array3[$row['name']] = $row['level'];
		$new_array4[$row['name']] = $dkpoints;
		$new_array5[$row['name']] = $turn;
		$new_array6[$row['name']] = $atk;
		$new_array7[$row['name']] = $def;
		$newarray2=serialize($new_array2);
		$newarray3=serialize($new_array3);
		$newarray4=serialize($new_array4);
		$newarray5=serialize($new_array5);
		$newarray6=serialize($new_array6);
		$newarray7=serialize($new_array7);
		$number++;
	}
	$totalpages=ceil($number/$perpage);
	addnav("Pages");
	if ($totalpages>1){
		for($i = 0; $i < $totalpages; $i++) {
			$j=$i+1;
			$minpage = (($j-1)*$perpage)+1;
			$maxpage = $perpage*$j;
			if ($maxpage>$number) $maxpage=$number;
			if ($maxpage==$minpage) addnav(array("Page %s (%s)", $j, $minpage), "runmodule.php?module=watcher&op=$op&subop=$j&op2=$op2");
			else addnav(array("Page %s (%s-%s)", $j, $minpage, $maxpage), "runmodule.php?module=watcher&op=$op&subop=$j&op2=$op2");
		}
	}

	output("`b`c`@Dragon Kill Point Distribution`c`b");
	output("`c`iReminder: 1 DK Point gives 5 Max Hitpoints`c`i`n");
	output("`c`iClick on Name to Edit User`c`i`n");
	$rank=translate_inline("Rank");
	$tname=translate_inline("Name");
	$tlevel=translate_inline("Level");
	$tdks=translate_inline("Dragon Kills");
	$thp=translate_inline("Max Hitpoints");
	$tff=translate_inline("Turns");
	$tatk=translate_inline("Attack");
	$tdef=translate_inline("Defense");
	rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
	rawoutput("<tr class='trhead'><td>$rank</td><td>$tname</td><td>$tlevel</td><td>$tdks</td><td>$thp</td><td>$tff</td><td>$tatk</td><td>$tdef</td></tr>");
	$n=0;
	arsort($new_array);
	foreach($new_array AS $name => $value){
		$n=$n+1;
		if ($n>$min && $n<=$max && $number>0){
			if ($name==$session['user']['name']) rawoutput("<tr class='trhilight'><td>");
			else rawoutput("<tr class='".($n%2?"trdark":"trlight")."'><td>");
			output_notl("`&%s",$n);
			rawoutput("</td><td>");
			$array2=unserialize($newarray2);
			$array3=unserialize($newarray3);
			$array4=unserialize($newarray4);
			$array5=unserialize($newarray5);
			$array6=unserialize($newarray6);
			$array7=unserialize($newarray7);
			$id=$array2[$name];
			output("<a href=\"user.php?op=edit&userid=$id\">`0[`^$name`0]</a>",true);
			addnav("","user.php?op=edit&userid=$id");
			rawoutput("</td><td>");
			output_notl("`c`@%s`c",$array3[$name]);
			rawoutput("</td><td>");
			output_notl("`c`@%s`c",$value);
			rawoutput("</td><td>");
			output_notl("`c`@%s`c",$array4[$name]);
			rawoutput("</td><td>");
			output_notl("`c`@%s`c",$array5[$name]);
			rawoutput("</td><td>");
			output_notl("`c`@%s`c",$array6[$name]);
			rawoutput("</td><td>");
			output_notl("`c`@%s`c",$array7[$name]);
		}
		rawoutput("</td></tr>");
	}
	rawoutput("</table>");
}
if ($op=="hof" || $op=="hofwarn" || $op=="hofdisregard" || $op=="warnlist" || $op=="watchlist" || $op=="disregardlist"){
	$array=translate_inline(array("","Gold","Gems","Charm","Corrected Max Hitpoints","Corrected Attack","Weapon Damage","Corrected Defense","Armor Defense","Alignment - Good","Alignment - Evil"));
	if ($op3=="disregard" || $op3=="reset"){
		$sql = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid='$id'";
		$res = db_query($sql);
		$row = db_fetch_assoc($res);
		$gname = $row['name'];
		if ($op3=="disregard"){
			set_module_pref("warn".$op2,2,"watcher",$id);
			output("`i`^%s `@disregarded for `^%s`@.`i`n",$array[$op2],$gname);
		}elseif($op3=="reset"){
			set_module_pref("warn".$op2,0,"watcher",$id);
			output("`i`^%s `@to be re-evaluated for `^%s`@.`i`n",$array[$op2],$gname);
		}
	}
	if ($op=="hofdisregard" || $op=="disregardlist") addnav("Disregard Lists");
	elseif ($op=="hofwarn" || $op=="warnlist") addnav("Warn Lists");
	else addnav("Watch Lists");
	for ($i=1;$i<=10;$i++) {
		if ($i==10 || $i==9){
			if (get_module_setting("list9")==1 && is_module_active("alignment")) addnav(array("%s", $array[$i]),"runmodule.php?module=watcher&op=$op&op2=$i");
		}else{
			if (get_module_setting("list".$i)==1) addnav(array("%s", $array[$i]),"runmodule.php?module=watcher&op=$op&op2=$i");
		}
	}
	addnav("Other Lists");
	if ($op2=="") $op2=1;
	if ($op=="disregardlist" || $op=="hofdisregard"){
		$danger=translate_inline("Disregarded ");
		$ph=translate_inline("Disregard List");
		$action=translate_inline("Re-Evaluate");
		if ($op=="hofdisregard"){
			addnav("Watch Lists","runmodule.php?module=watcher&op=hof&op2=$op2");
			addnav("Warn Lists","runmodule.php?module=watcher&op=hofwarn&op2=$op2");
		}else{
			addnav("Watch Lists","runmodule.php?module=watcher&op=watchlist&op2=$op2");
			addnav("Warn Lists","runmodule.php?module=watcher&op=warnlist&op2=$op2");
		}
	}elseif ($op=="warnlist" || $op=="hofwarn"){
		$danger=translate_inline("Excessive ");
		$ph=translate_inline("Warning List");
		$action=translate_inline("Disregard");
		if ($op=="hofwarn"){
			addnav("Watch Lists","runmodule.php?module=watcher&op=hof&op2=$op2");
			addnav("Disregard Lists","runmodule.php?module=watcher&op=hofdisregard&op2=$op2");
		}else{
			addnav("Watch Lists","runmodule.php?module=watcher&op=watchlist&op2=1");
			addnav("Disregard Lists","runmodule.php?module=watcher&op=disregardlist&op2=$op2");
		}
	}else{
		$danger="";
		$ph=translate_inline("Watch List");
		$action=translate_inline("Disregard");
		if ($op=="hof"){
			addnav("Warn Lists","runmodule.php?module=watcher&op=hofwarn&op2=$op2");
			addnav("Disregard Lists","runmodule.php?module=watcher&op=hofdisregard&op2=$op2");
		}else{
			addnav("Warn Lists","runmodule.php?module=watcher&op=warnlist&op2=$op2");
			addnav("Disregard Lists","runmodule.php?module=watcher&op=disregardlist&op2=$op2");
		}
	}
	if ($op=="hofdisregard" || $op=="hofwarn" || $op=="hof"){
		addnav("DK Point Use","runmodule.php?module=watcher&op=hofdkpointall");
		addnav("Navigation");
		addnav("Back to HoF", "hof.php");
	}else{
		addnav("DK Point Use","runmodule.php?module=watcher&op=dkpointall");
		addnav("Navigation");
		addnav("Return to the Grotto","superuser.php");
	}
	villagenav();
	page_header("%s",$ph);
	$perpage=get_module_setting("pp");
	$subop = httpget('subop');
	if ($subop=="") $subop=1;
	$min = (($subop-1)*$perpage);
	$max = $perpage*$subop;
	$sql = "SELECT superuser,acctid,name,goldinbank,gold,charm,level,gems,attack,weapon,armor,weapondmg,armordef,defense,dragonpoints,maxhitpoints,level,dragonkills FROM ".db_prefix("accounts")."";
	$res = db_query($sql);
	$number=0;
	$new_array = array();
	$new_array2 = array();
	$new_array3 = array();
	$new_array4 = array();
	$new_array5 = array();
	$new_array6 = array();
	$new_array7 = array();
	for ($i=0;$i<db_num_rows($res);$i++){
		$row = db_fetch_assoc($res);
		$check=0;
		$id=$row['acctid'];
		$new_array2[$row['name']] = $id;
		//Gold
		if ($op2==1){
			$goldr=$row['goldinbank']+$row['gold'];
			if ($goldr=="") $gold=0;
			if ((($op=="hofdisregard" || $op=="disregardlist") && get_module_pref("warn1","watcher",$id)==2) || (($op=="hofwarn" || $op=="warnlist") && get_module_pref("warn1","watcher",$id)==1)||(($op=="hof"||$op=="watchlist") && get_module_pref("warn1","watcher",$id)<2)){
				$new_array[$row['name']] = $goldr;
				$new_array3[$row['name']] = $row['gold'];
				$new_array4[$row['name']] = $row['goldinbank'];
				$newarray2=serialize($new_array2);
				$newarray3=serialize($new_array3);
				$newarray4=serialize($new_array4);
				$number++;
			}
		//Gems
		}elseif($op2==2){
			$gemsr=$row['gems'];
			if ($gemsr=="") $gemsr=0;
			if ((($op=="hofdisregard" || $op=="disregardlist") && get_module_pref("warn2","watcher",$id)==2) || (($op=="hofwarn" || $op=="warnlist") && get_module_pref("warn2","watcher",$id)==1)||(($op=="hof"||$op=="watchlist") && get_module_pref("warn2","watcher",$id)<2)){
				$new_array[$row['name']] = $gemsr;
				$newarray2=serialize($new_array2);
				$number++;
			}
		//Charm
		}elseif ($op2==3){
			$charmr=$row['charm'];
			if ($charmr=="") $charmr=0;
			if ((($op=="hofdisregard" || $op=="disregardlist") && get_module_pref("warn3","watcher",$id)==2) || (($op=="hofwarn" || $op=="warnlist") && get_module_pref("warn3","watcher",$id)==1)||(($op=="hof"||$op=="watchlist") && get_module_pref("warn3","watcher",$id)<2)){
				$new_array[$row['name']] = $charmr;
				$newarray2=serialize($new_array2);
				$number++;
			}
		//Max Hitpoints
		}elseif ($op2==4){
			$dkpoints = 0;
			$hps=0;
			$dp = @unserialize($row["dragonpoints"]);
			if (!is_array($dp)) $dp = array();
			reset($dp);
			while(list($key,$val)=each($dp)){
				if ($val=="hp") $dkpoints+=5;
			}
			$hps=$row['maxhitpoints'] - $dkpoints - ($row['level']*10);
			if ($hps=="") $hps=0;
			if ((($op=="hofdisregard" || $op=="disregardlist") && get_module_pref("warn4","watcher",$id)==2) || (($op=="hofwarn" || $op=="warnlist") && get_module_pref("warn4","watcher",$id)==1)||(($op=="hof"||$op=="watchlist") && get_module_pref("warn4","watcher",$id)<2)){
				$new_array[$row['name']] = $hps;
				$new_array3[$row['name']] = $row['maxhitpoints'];
				$new_array4[$row['name']] = $row['level'];
				$new_array5[$row['name']] = $dkpoints;
				$newarray2=serialize($new_array2);
				$newarray3=serialize($new_array3);
				$newarray4=serialize($new_array4);
				$newarray5=serialize($new_array5);
				$number++;
			}
		//Attack or Weapon Damage
		}elseif ($op2==5 || $op2==6){
			$dkatk = 0;
			$atk = 0;
			$dp = @unserialize($row["dragonpoints"]);
			if (!is_array($dp)) $dp = array();
			reset($dp);
			while(list($key,$val)=each($dp)){
				if ($val=="at") $dkatk++;	
			}
			$atk=$row['attack'] - $dkatk - $row['level'] - $row['weapondmg'];
			if ($op2==5){
				if ((($op=="hofdisregard" || $op=="disregardlist") && get_module_pref("warn5","watcher",$id)==2) || (($op=="hofwarn" || $op=="warnlist") && get_module_pref("warn5","watcher",$id)==1)||(($op=="hof"||$op=="watchlist") && get_module_pref("warn5","watcher",$id)<2)){
					$new_array[$row['name']] = $atk;
					$new_array3[$row['name']] = $row['attack'];
					$new_array4[$row['name']] = $dkatk;
					$new_array5[$row['name']] = $row['weapondmg'];
					$new_array6[$row['name']] = $row['level'];
					$new_array7[$row['name']] = $row['weapon'];
					$newarray2=serialize($new_array2);
					$newarray3=serialize($new_array3);
					$newarray4=serialize($new_array4);
					$newarray5=serialize($new_array5);
					$newarray6=serialize($new_array6);
					$newarray7=serialize($new_array7);
					$number++;
				}
			}else{
				if ((($op=="hofdisregard" || $op=="disregardlist") && get_module_pref("warn6","watcher",$id)==2) || (($op=="hofwarn" || $op=="warnlist") && get_module_pref("warn6","watcher",$id)==1)||(($op=="hof"||$op=="watchlist") && get_module_pref("warn6","watcher",$id)<2)){
					$new_array[$row['name']] = $row['weapondmg'];
					$new_array3[$row['name']] = $row['attack'];
					$new_array4[$row['name']] = $atk;
					$new_array5[$row['name']] = $dkatk;
					$new_array6[$row['name']] = $row['level'];
					$new_array7[$row['name']] = $row['weapon'];
					$newarray2=serialize($new_array2);
					$newarray3=serialize($new_array3);
					$newarray4=serialize($new_array4);
					$newarray5=serialize($new_array5);
					$newarray6=serialize($new_array6);
					$newarray7=serialize($new_array7);
					$number++;
				}
			}
		//Defense or Armor Defense
		}elseif ($op2==7 || $op2==8){
			$dkdef = 0;
			$def = 0;
			$dp = @unserialize($row["dragonpoints"]);
			if (!is_array($dp)) $dp = array();
			reset($dp);
			while(list($key,$val)=each($dp)){
				if ($val=="de") $dkdef++;
			}
			$def=$row['defense'] - $dkdef - $row['level'] - $row['armordef'];
			if ($op2==7){
				if ((($op=="hofdisregard" || $op=="disregardlist") && get_module_pref("warn7","watcher",$id)==2) || (($op=="hofwarn" || $op=="warnlist") && get_module_pref("warn7","watcher",$id)==1)||(($op=="hof"||$op=="watchlist") && get_module_pref("warn7","watcher",$id)<2)){
					$new_array[$row['name']] = $def;
					$new_array3[$row['name']] = $row['defense'];
					$new_array4[$row['name']] = $dkdef;
					$new_array5[$row['name']] = $row['armordef'];
					$new_array6[$row['name']] = $row['level'];
					$new_array7[$row['name']] = $row['armor'];
					$newarray2=serialize($new_array2);
					$newarray3=serialize($new_array3);
					$newarray4=serialize($new_array4);
					$newarray5=serialize($new_array5);
					$newarray6=serialize($new_array6);
					$newarray7=serialize($new_array7);
					$number++;
				}
			}else{
				if ((($op=="hofdisregard" || $op=="disregardlist") && get_module_pref("warn8","watcher",$id)==2) || (($op=="hofwarn" || $op=="warnlist") && get_module_pref("warn8","watcher",$id)==1)||(($op=="hof"||$op=="watchlist") && get_module_pref("warn8","watcher",$id)<2)){
					$new_array[$row['name']] = $row['armordef'];
					$new_array3[$row['name']] = $row['defense'];
					$new_array4[$row['name']] = $def;
					$new_array5[$row['name']] = $dkdef;
					$new_array6[$row['name']] = $row['level'];
					$new_array7[$row['name']] = $row['armor'];
					$newarray2=serialize($new_array2);
					$newarray3=serialize($new_array3);
					$newarray4=serialize($new_array4);
					$newarray5=serialize($new_array5);
					$newarray6=serialize($new_array6);
					$newarray7=serialize($new_array7);
					$number++;
				}
			}
		//Alignment
		}else{
			$align=get_module_pref("alignment","alignment",$id);
			if ($align=="") $align=0;
			if ((($op=="hofdisregard" || $op=="disregardlist") && get_module_pref("warn9","watcher",$id)==2) || (($op=="hofwarn" || $op=="warnlist") && get_module_pref("warn9","watcher",$id)==1)||(($op=="hof"||$op=="watchlist") && get_module_pref("warn9","watcher",$id)<2)){
				$new_array[$row['name']] = $align;
				$newarray2=serialize($new_array2);
				$number++;
			}
		}
	}
	$totalpages=ceil($number/$perpage);
	addnav("Pages");
	if ($totalpages>1){
		for($i = 0; $i < $totalpages; $i++) {
			$j=$i+1;
			$minpage = (($j-1)*$perpage)+1;
			$maxpage = $perpage*$j;
			if ($maxpage>$number) $maxpage=$number;
			if ($maxpage==$minpage) addnav(array("Page %s (%s)", $j, $minpage), "runmodule.php?module=watcher&op=$op&subop=$j&op2=$op2");
			else addnav(array("Page %s (%s-%s)", $j, $minpage, $maxpage), "runmodule.php?module=watcher&op=$op&subop=$j&op2=$op2");
		}
	}
	$rankt = translate_inline("Rank");
	$namet = translate_inline("Name");
	$totalt = $danger.$array[$op2];
	if ($op2==9) $totaltitle=translate_inline("Alignment - Good First");
	elseif ($op2==10) $totaltitle=translate_inline("Alignment - Evil First");
	else $totaltitle=$array[$op2];
	output("`b`c`\$Total %s`c`b",$danger.$totaltitle);
	output("`c`iClick on Name to Edit User`c`i");
	if ($op2==4) output("`n`c`iCorrected Max Hitpoints do not include points from levels or dragonkills`i`c");
	elseif ($op2==5 || $op2==6) output("`n`c`iCorrected Attack does not include points from levels, weapon, or dragonkills`i`c");
	elseif ($op2==7 || $op2==8) output("`n`c`iCorrected Defense does not include points from levels, armor, or dragonkills`i`c");
	output_notl("`n");
	rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
	if ($op2==1){
		$totalt=translate_inline("Total Gold");
		$goldh=translate_inline("Gold on Hand");
		$gibh=translate_inline("Gold in Bank");
		rawoutput("<tr class='trhead'><td>$rankt</td><td>$namet</td><td>$totalt</td><td>$goldh</td><td>$gibh</td><td>$action</td></tr>");
	}elseif($op2==2 || $op2==3){
		rawoutput("<tr class='trhead'><td>$rankt</td><td>$namet</td><td>$totalt</td><td>$action</td></tr>");
	}elseif ($op2==4){
		$totalmh=translate_inline("Corrected Max HPs");
		$totalt=translate_inline("Total Max HPs");
		$tlevel=translate_inline("Max HPs from Level");
		$hdkh=translate_inline("Max HPs from Dks");
		rawoutput("<tr class='trhead'><td>$rankt</td><td>$namet</td><td align='center'>$totalt</td><td align='center'>$totalmh</td><td align='center'>$tlevel</td><td align='center'>$hdkh</td><td>$action</td></tr>");
	}elseif ($op2==5 || $op2==6){
		$tcoratk=translate_inline("Corrected Attack");
		$ttotatk=translate_inline("Total Attack");
		$tadk=translate_inline("Attack from Dks");
		$twpndmg=translate_inline("Weapon Damage");
		$tlevel=translate_inline("Level");
		$tweapon=translate_inline("Weapon");
		if ($op2==5) rawoutput("<tr class='trhead'><td>$rankt</td><td>$namet</td><td align='center'>$tcoratk</td><td align='center'>$ttotatk</td><td align='center'>$tadk</td><td align='center'>$twpndmg</td><td>$tlevel</td><td>$tweapon</td><td>$action</td></tr>");
		else rawoutput("<tr class='trhead'><td>$rankt</td><td>$namet</td><td align='center'>$twpndmg</td><td align='center'>$ttotatk</td><td align='center'>$tcoratk</td><td align='center'>$tadk</td><td>$tlevel</td><td>$tweapon</td><td>$action</td></tr>");
	}elseif ($op2==7 || $op2==8){
		$tcoratk=translate_inline("Corrected Defense");
		$ttotatk=translate_inline("Total Defense");
		$tadk=translate_inline("Defense from Dks");
		$twpndmg=translate_inline("Armor Defense");
		$tlevel=translate_inline("Level");
		$tweapon=translate_inline("Armor");
		if ($op2==7) rawoutput("<tr class='trhead'><td>$rankt</td><td>$namet</td><td align='center'>$tcoratk</td><td align='center'>$ttotatk</td><td align='center'>$tadk</td><td align='center'>$twpndmg</td><td align='center'>$tlevel</td><td>$tweapon</td><td>$action</td></tr>");
		else rawoutput("<tr class='trhead'><td>$rankt</td><td>$namet</td><td align='center'>$twpndmg</td><td align='center'>$ttotatk</td><td align='center'>$tcoratk</td><td align='center'>$tadk</td><td align='center'>$tlevel</td><td>$tweapon</td><td>$action</td></tr>");	
	}elseif ($op2==9 || $op2==10){
		$totalt=translate_inline("Alignment");
		rawoutput("<tr class='trhead'><td>$rankt</td><td>$namet</td><td>$totalt</td><td>$action</td></tr>");
	}
	$n=0;
	if ($op2<10) arsort($new_array);
	else asort($new_array);
	foreach($new_array AS $name => $value){
		$n=$n+1;
		if ($n>$min && $n<=$max && $number>0){
			if ($name==$session['user']['name']) rawoutput("<tr class='trhilight'><td>");
			else rawoutput("<tr class='".($n%2?"trdark":"trlight")."'><td>");
			if ($value>=get_module_setting("warn".$op2) && get_module_setting("warn".$op2)>0) $c="`\$`b";
			else $c="`^";
			output_notl("`&%s",$n);
			rawoutput("</td><td>");
			$array2=unserialize($newarray2);
			$id=$array2[$name];
			output("<a href=\"user.php?op=edit&userid=$id\">`0[`&$c$name$c`0]</a>",true);
			addnav("","user.php?op=edit&userid=$id");
			rawoutput("</td><td>");
			if ($op2==1){
				$array3=unserialize($newarray3);
				$array4=unserialize($newarray4);
				output_notl("`c%s%s%s`c`0",$c,$value,$c);
				rawoutput("</td><td>");
				output_notl("`c%s%s%s`c`0",$c,$array3[$name],$c);
				rawoutput("</td><td>");
				output_notl("`c%s%s%s`c`0",$c,$array4[$name],$c);
			}elseif ($op2==2 || $op2==3 || $op2==9|| $op2==10){
				output_notl("`c%s%s%s`c`0",$c,$value,$c);
			}elseif ($op2==4){
				$array3=unserialize($newarray3);
				$array4=unserialize($newarray4);
				$array5=unserialize($newarray5);
				output_notl("`c%s%s%s`c`0",$c,$array3[$name],$c);
				rawoutput("</td><td>");
				output_notl("`c%s%s%s`c`0",$c,$value,$c);
				rawoutput("</td><td>");
				output_notl("`c%s%s%s`c`0",$c,$array4[$name]*10,$c);
				rawoutput("</td><td>");
				output_notl("`c%s%s%s`c`0",$c,$array5[$name],$c);
			}elseif ($op2>=5 && $op2<=8){
				$array3=unserialize($newarray3);
				$array4=unserialize($newarray4);
				$array5=unserialize($newarray5);
				$array6=unserialize($newarray6);
				$array7=unserialize($newarray7);
				output_notl("`c%s%s%s`c",$c,$value,$c);
				rawoutput("</td><td>");
				output_notl("`c%s%s%s`c",$c,$array3[$name],$c);
				rawoutput("</td><td>");
				output_notl("`c%s%s%s`c",$c,$array4[$name],$c);
				rawoutput("</td><td>");
				output_notl("`c%s%s%s`c",$c,$array5[$name],$c);
				rawoutput("</td><td>");
				output_notl("`c%s%s%s`c",$c,$array6[$name],$c);
				rawoutput("</td><td>");
				output_notl("%s%s%s",$c,$array7[$name],$c);
			}
			rawoutput("</td><td>");
			if ($op=="disregardlist" || $op=="hofdisregard"){
				output("<a href=\"runmodule.php?module=watcher&op=$op&op2=$op2&op3=reset&userid=$id\">`0[`&Re-Evaluate`0]</a>",true);
				addnav("","runmodule.php?module=watcher&op=$op&op2=$op2&op3=reset&userid=$id");
			}else{
				output("<a href=\"runmodule.php?module=watcher&op=$op&op2=$op2&op3=disregard&userid=$id\">`0[`&Disregard`0]</a>",true);
				addnav("","runmodule.php?module=watcher&op=$op&op2=$op2&op3=disregard&userid=$id");
			}
			rawoutput("</td></tr>");
			}
	}
	if ($number==0){
		rawoutput("<tr class='trlight'><td colspan='10' align='center'>");
		output("None");
		rawoutput("</td></tr>");
	}
	rawoutput("</table>");
}
page_footer();	
}
?>