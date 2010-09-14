<?php
function marriage_flist(&$items) {
	global $session;
	$list = unserialize(get_module_pref('flirtsreceived'));
	if ($list=="") $list=array();
	output_notl("`c<table><tr class='trhead'><td><center>%s</center></td></tr>",translate_inline('Flirts Received'),true);
	//Flirts Received
	if (sizeof($list)>0) {
		$n=0;
		$allprefs=unserialize(get_module_pref('allprefs'));
		
		$stage=((get_module_setting('cost')>0&&$allprefs['buyring']==1) || get_module_setting('cost')==0);
		//debug("Stage:".$stage);
		while (list($name,$points)=each ($list)) {
			$sql = "SELECT name,acctid FROM ".db_prefix("accounts")." WHERE acctid='".substr($name,1)."' AND locked=0";
			$res = db_query($sql);
			if (db_num_rows($res)!=0) {
				$row = db_fetch_assoc($res);
				if ($points<>0){
					$n++;
					output_notl("<tr class='".($n%2?"trlight":"trdark")."'><td> ",true);
					//output_notl("`@[`^".$row['name']."`@]");
					output("`^%s`@ has `^%s`@ flirt points to you.",$row['name'],$points);
					$links = translate_inline("Options: ");
					$links .= " [".marriage_flink($row['acctid'],"Buy a Drink","drink")."]";
					$links .= " - [".marriage_flink($row['acctid'],"Buy some Roses","roses")."]";
					$links .= " - [".marriage_flink($row['acctid'],"Kiss","kiss")."]";
					foreach ($items['shortcut'] as $key=>$val){
						list($itemname,$navname)= each($val);
						//debug($val);
						//debug("Name:".$itemname." nav:".$navname);
						$links .= " - [".marriage_flink($row['acctid'],$navname,$itemname)."]";
					}
					$links .= " - [".marriage_flink($row['acctid'],"Slap","slap")."]";
					$links .= " - [".marriage_flink($row['acctid'],"Shun","shun")."]";
					$links .= " - [".marriage_flink($row['acctid'],"Ignore","ignore")."]";
					$links .= " - [".marriage_flink($row['acctid'],"Block","block")."]";
					if ($points>=get_module_setting('flirtmuch')&&$session['user']['marriedto']==0) {
						$blah = "";
						if ($session['user']['location'] == get_module_setting("chapelloc")&&get_module_setting("all")==0&&get_module_setting('oc')==0) {
							$blah = 'chapel';
						} elseif (get_module_setting("all")==1&&get_module_setting('oc')==0) {
							$blah = 'chapel';
						} elseif (get_module_setting('oc')==1) {
							$blah = 'oldchurch';
						}
						//stage is set above
						if ($session['user']['dragonkills']>=get_module_setting("dks")){
							if (get_module_pref("supernomarry")==1 || get_module_pref("user_wed")==1){
								$links.=" - [<a href='runmodule.php?module=marriage&op=loveshack&op2=blproposing'>".translate_inline("Propose")."</a>]";
								addnav("","runmodule.php?module=marriage&op=loveshack&op2=blproposing");
							}elseif (get_module_pref("supernomarry","marriage",$row['acctid'])==1 || get_module_pref("user_wed","marriage",$row['acctid'])==1){
								$links.=" - [<a href='runmodule.php?module=marriage&op=loveshack&op2=blpropose'>".translate_inline("Propose")."</a>]";
								addnav("","runmodule.php?module=marriage&op=loveshack&op2=blpropose");
							}else{
								if ($blah!='') {
									$links.=" - [<a href='runmodule.php?module=marriage&op2=propose&op=$blah&stage=$stage&target=".$row['acctid']."'>".translate_inline("Propose")."</a>]";
									addnav("","runmodule.php?module=marriage&op2=propose&op=$blah&stage=$stage&target=".$row['acctid']);
								} else {
									$links.=" - [<a href='runmodule.php?module=marriage&op2=propose&op=chapel&stage=$stage&target=".$row['acctid']."'>".translate_inline("Propose")."</a>]";
									addnav("","runmodule.php?module=marriage&op2=propose&op=chapel&stage=$stage&target=".$row['acctid']);
								}
							}
						}
					}
					rawoutput(marriage_hidedata($links));
					rawoutput("</td></tr>");
				}
			}
		}
		if ($n==0){
			rawoutput("<tr class='trhilight'><td>");
			output_notl("`^");
			output("Aww! No One has flirted with you.");
			rawoutput("</td></tr>");
		}
		rawoutput("</table><br>");
		output_notl("`c");
	} else {
		rawoutput("<tr class='trhilight'><td>");
		output_notl("`^");
		output("Aww! No One has flirted with you.");
		rawoutput("</td></tr></table><br>");
		output_notl("`c");
	}
	//Flirts to Others
	$list = unserialize(get_module_pref('flirtssent'));
	//debug($list);
	if ($list=="") $list=array();
	output_notl("`c<table><tr class='trhead'><td><center>%s</center></td></tr>",translate_inline('Your Flirts to Others'),true);
	if (sizeof($list)>0) {
		$n=0;
		while (list($name,$points)=each ($list)) {
			$name=substr($name,1);
			$sql = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid='$name' AND locked=0";
			$res = db_query($sql);
			if (db_num_rows($res)!=0) {
				$row = db_fetch_assoc($res);
				if ($points<>0){
					$n++;
					output_notl("<tr class='".($n%2?"trlight":"trdark")."'><td> ",true);
					output("`@You have `^%s`@ flirt points to `^%s`@.",$points,$row['name']);
					rawoutput("</td></tr>");
				}
			}
		}
		if ($n==0){
			rawoutput("<tr class='trhilight'><td>");
			output_notl("`^");
			output("Aww! You haven't flirted with anyone.");
			rawoutput("</td></tr>");
		}
		rawoutput("</table><br>");
		output_notl("`c");
	} else {
		rawoutput("<tr class='trhilight'><td>");
		output_notl("`^");
		output("Aww! You haven't flirted with anyone.");
		rawoutput("</td></tr></table><br>");
		output_notl("`c");
	}
	//Blocked Players
	$stuff3 = explode(',',get_module_pref('blocked'));
	$n = 0;
	output_notl("`c");
	output_notl("<table><tr class='trhead'><td><center>%s</center></td></tr>",translate_inline('Players Blocked From Flirting With You'),true);
	$pts = 0;
	foreach ($stuff3 as $val) {
		if ($val!="") {
			$sql = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid='$val' AND locked=0";
			$res = db_query($sql);
			if (db_num_rows($res)!=0) {
				$row = db_fetch_assoc($res);
				$n++;
				output_notl("<tr class='".($n%2?"trlight":"trdark")."'><td><center>",true);
				output("`^%s",$row['name']);
				rawoutput("</center></td></tr>");
			}
		}
	}
	if ($n==0) {
		output_notl("<tr class='trhilight'><td><center>`^%s</center></td></tr>",translate_inline("No Players Blocked"),true);
	}
	output_notl('</table>',true);
	output_notl("`c");
}
?>