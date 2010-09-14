<?php
function marriage_pform($backoperation) {
	global $session;
	$whom = httppost("whom");
	rawoutput("<form action='runmodule.php?module=marriage&op=".$backoperation."&op2=propose&stage=0' method='POST'>");
	addnav("","runmodule.php?module=marriage&op=".$backoperation."&op2=propose&stage=0");
	if ($whom!="") {
		$string="%";
		for ($x=0;$x<strlen($whom);$x++){
			$string .= substr($whom,$x,1)."%";
		}
		if (get_module_setting('sg')==1) {
			$sql = "SELECT login,name,acctid FROM ".db_prefix("accounts")." WHERE login LIKE '%$whom%' AND acctid<>".$session['user']['acctid']." AND marriedto=0 ORDER BY level,login";
		} else {
			$sql = "SELECT login,name,acctid FROM ".db_prefix("accounts")." WHERE name LIKE '%$string%' AND acctid<>".$session['user']['acctid']." AND sex<>".$session['user']['sex']." AND marriedto=0 ORDER BY level,login";
		}
		$result = db_query($sql);
		if (db_num_rows($result)!=0) {
			output("`@Please choose from the following people:`n`n`c");
			rawoutput("<table cellpadding='3' cellspacing='0' border='0'>");
			rawoutput("<tr class='trhead'><td><center>Name</center></td></tr>");
			for ($i=0;$i<db_num_rows($result);$i++){
				$n++;
				$row = db_fetch_assoc($result);
				rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td><a href='runmodule.php?module=marriage&op2=propose&op=".$backoperation."&stage=1&target=".$row['acctid']."'>");
				output_notl("`0[`^%s`0]",$row['name']);
				rawoutput("</td></tr>");
				addnav("","runmodule.php?module=marriage&op2=propose&op=".$backoperation."&stage=1&target=".$row['acctid']);
			}
			rawoutput("</table>");
			output_notl("`c");
		} else {
			output("`c`@`bA user was not found with that name.`b`c");
		}
		output_notl("`n");
	}
	if ($n==0)	output("Who do you want to propose to?`n`n");
	else output("Would you like to look for someone else?`n`n");
	rawoutput("<input name='whom' maxlength='50' value=\"".htmlentities(stripslashes($whom))."\">");
	$apply = translate_inline("Search");
	rawoutput("<input type='submit' class='button' value='$apply'></form>");
	output("`c`@(Remember: They can't be married already!)`c");
}

function marriage_plist($op) {
	$stuff = explode(',',get_module_pref('proposals'));
	$n = 0;
	if (get_module_pref("proposals")!="") {
		output("`@The following people have proposed to you... click to marry, or reject them!`n`n`c");
		rawoutput("<table cellpadding='3' cellspacing='0' border='0'>");
		rawoutput("<tr class='trhead'><td colspan='7' align='center'><center>");
		output("Proposals");
		rawoutput("</center></td></tr>");
		$marry=translate_inline("`0[`@Marry`0]");
		$reject=translate_inline("`0[`\$Reject`0]");	
		foreach ($stuff as $val) {
			if ($val!="") {
				$sql = "SELECT name,marriedto FROM ".db_prefix("accounts")." WHERE acctid='$val' AND locked=0";
				$res = db_query($sql);
				$row = db_fetch_assoc($res);
				//We need this row[marriedto] added to prevent people from marrying someone who's already married
				if (db_num_rows($res)!=0 && $row['marriedto']==0) {
					$n++;
					rawoutput("<tr ".($n%2?"trlight":"trdark")."><td><a href='runmodule.php?module=marriage&op=".$op."&target=".$val."&op2=marry'>");
					output_notl("%s",$marry);
					rawoutput("</a></td><td><center>");
					output_notl("`^%s`0",$row['name']);
					rawoutput("</center></td><td><a href='runmodule.php?module=marriage&op=".$op."&target=".$val."&op2=reject'>");
					output_notl("%s",$reject);
					rawoutput("</a></td>");
					addnav("","runmodule.php?module=marriage&op=".$op."&target=".$val."&op2=marry");
					addnav("","runmodule.php?module=marriage&op=".$op."&target=".$val."&op2=reject");
				}
			}
		}
		if ($n==0){
			rawoutput("<tr><td><center>");
			output("`^Aww! No one wants to marry you.");
			rawoutput("</center></td></tr>");
		}
	} else {
		output_notl("`c");
		rawoutput("<table><tr><td><center>");
		output("`^Aww! No one wants to marry you.");
		rawoutput("</center></td></tr>");
	}
	output_notl('</table>',true);
	output_notl("`c");
}
?>