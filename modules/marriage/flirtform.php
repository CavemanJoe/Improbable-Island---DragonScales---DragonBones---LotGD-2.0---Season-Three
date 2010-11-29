<?php
//you can use this function if you want to search for a flirtpartner
function marriage_fform($w,$whereto='runmodule.php?module=marriage&op=loveshack&op2=flirt') {
	global $session;
	$whom = httppost("whom");
	rawoutput("<form action='$whereto&flirtitem=$w&stage=0' method='POST'>");
	addnav("","$whereto&flirtitem=$w&stage=0");
	if ($whom!="") {
		$string="%";
		for ($x=0;$x<strlen($whom);$x++){
			$string .= substr($whom,$x,1)."%";
		}
		if (get_module_setting('sg','marriage')==1) {
			$sql = "SELECT login,sex,name,charm,acctid FROM ".db_prefix("accounts")." WHERE login LIKE '%$whom%' AND acctid<>".$session['user']['acctid']." ORDER BY level,login";
		} else {
			$sql = "SELECT login,sex,name,charm,acctid FROM ".db_prefix("accounts")." WHERE name LIKE '%$string%' AND acctid<>".$session['user']['acctid']." AND sex<>".$session['user']['sex']." ORDER BY level,login";
		}
		$result = db_query($sql);
		$charmlevel=get_module_setting('charmleveldifference','marriage');
		if (db_num_rows($result)!=0) {
			output("`@These users were found:`n`n`c");
			rawoutput("<table cellpadding='3' cellspacing='0' border='0'>");
			rawoutput("<tr class='trhead'><td><center>".translate_inline("Name")."</center></td></tr>");
			for ($i=0;$i<db_num_rows($result);$i++){
				$row = db_fetch_assoc($result);
				if (($row['charm']>($session['user']['charm']+$charmlevel)) && $charmlevel>0) {
					rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td><a href='$whereto&flirtitem=one&name=".urlencode($row['name'])."&stage=1&target=".$row['acctid']."'>");
					addnav("","$whereto&flirtitem=one&name=".urlencode($row['name'])."&stage=1&target=".$row['acctid']);
				} else if (($session['user']['charm']>($row['charm']+$charmlevel)) && $charmlevel>0) {
					rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td><a href='$whereto&flirtitem=two&name=".urlencode($row['name'])."&stage=1&target=".$row['acctid']."'>");
					addnav("","$whereto&flirtitem=two&name=".urlencode($row['name'])."&stage=1&target=".$row['acctid']);				
				} else {
					rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td><a href='$whereto&flirtitem=$w&name=".urlencode($row['name'])."&stage=1&target=".$row['acctid']."'>");
					addnav("","$whereto&flirtitem=$w&name=".urlencode($row['name'])."&stage=1&target=".$row['acctid']);
				}
				output_notl("`0[`^%s`0]",$row['name']);
				rawoutput("</td></tr>");
			}
			rawoutput("</table>");
		} else {
			output("`c`c`@`bA user was not found with that name.`b`c");
		}
		output_notl("`c`n");
	}else{
		if (get_module_pref("supernoflirt")==1){
			output("You are not allowed to flirt because the `^Staff`@ have decided you can't engage in flirting.  If you believe this is an error please contact the `^Staff`@.");
		}elseif (get_module_pref("user_option")==1){
			output("You've decided that you will not allow flirting to distract you from your goals.");
			output("In order to flirt, you'll need to change your preference on flirting.");
		}elseif ($w=="unblock"){
			$stuff3 = explode(',',get_module_pref('blocked'));
			output("You have chosen to block the following players.  Please choose a Player to `^Unblock`@:`n`n");
			output_notl("`c");
			output_notl("<table><tr class='trhead'></tr>",true);
			$pts = 0;
			foreach ($stuff3 as $val) {
				if ($val!="") {
					$sql = "SELECT name,acctid FROM ".db_prefix("accounts")." WHERE acctid='$val' AND locked=0";
					$res = db_query($sql);
					if (db_num_rows($res)!=0) {
						$row = db_fetch_assoc($res);
						$i++;
						rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td><a href='$whereto&flirtitem=$w&name=".urlencode($row['name'])."&stage=1&target=".$row['acctid']."'>");
						output_notl("`0[`^%s`0]",$row['name']);
						rawoutput("</center></td></tr>");
						addnav("","$whereto&flirtitem=$w&name=".urlencode($row['name'])."&stage=1&target=".$row['acctid']);
					}
				}
			}
			if ($i==0){
				output_notl("<tr class='trhilight'><td><center>`^%s</center></td></tr>",translate_inline("No Players Blocked"),true);
			}
			output_notl('</table>',true);
			output_notl("`c");
		}else{
			output("`@Who would you like to");
			if ($w=="drink" || $w=="roses"){
				if ($w=="drink") output("buy a drink for? It's only `^%s gold`@.",get_module_setting("prdrink"));
				if ($w=="roses") output("buy roses for? It's only `^%s gold`@.",get_module_setting("prroses"));
				$apply = translate_inline("Flirt");
			}elseif ($w=="slap") {
				output("slap?");
				$apply = translate_inline("Slap");
			}elseif ($w=="chocolates"){
				output("buy chocolates for? It's only `^%s gold`@.",get_module_setting("cost-chocolates","marriagechocolates"));
				$apply = translate_inline("Flirt");
			}elseif ($w=="block"){
				output("block?");
				$apply = translate_inline("Block");
			}elseif ($w=="shun"){
				output("shun?");
				$apply = translate_inline("Shun");
			}else{
				output("do that to?");
				$apply = translate_inline("Flirt");
			}
			output("`n`nName: ");
			rawoutput("<input name='whom' maxlength='50' value=\"".htmlentities(stripslashes($whom))."\">");
			rawoutput("<input type='submit' class='button' value='$apply'></form>");
			if (get_module_setting('sg','marriage')==1) {
				output("`c(Same gender flirting is allowed.)`c");
			} else {
				output("`c(Same gender flirting is not allowed.)`c");
			}
		}
	}
}
?>