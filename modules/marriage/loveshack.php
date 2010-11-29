<?php
$allprefs=unserialize(get_module_pref('allprefs'));
$allprefs['inShack']=1;
set_module_pref('allprefs',serialize($allprefs));
$allprefs=unserialize(get_module_pref('allprefs'));
page_header("The Loveshack");
output("`c`b`@The `\$Love`@ Shack`b`c`n");
$link = "runmodule.php?module=marriage&op=loveshack";
$operation = httpget('op2');
$target = httpget('target');
$bartender = get_module_setting("bartendername");
$genderbartender = (get_module_setting('genderbartender')?translate_inline("she"):translate_inline("he"));
$genterbartenderp = (get_module_setting('genderbartender')?translate_inline("herself"):translate_inline("himself"));
$title = (get_module_setting('genderbartender')?translate_inline("Lady"):translate_inline("Lord"));

$items=array(
	"roses"=>"`%{mname}`^ bought some expensive roses for you, at the loveshack!`n`%{mname}'s`^ flirt points increased by `@{pts}`^ with you!",
	"drink"=>"`%{mname}`^ bought you a drink at the loveshack!`n`%{mname}'s`^ flirt points increased by `@{pts}`^ with you!",
	"slap"=>"`^{mname}`\$ just `^slapped`\$ you, at the loveshack! You aren't going to stand for that, are you?`n`%{mname}'s`% flirt points decreased by {pts} points with you.",
	"ignore"=>"`%{mname} `^just ignored you at the loveshack. All your flirt points to  `%{mname}`^ have been removed.",
	"kiss"=>"`%{mname}`^ planted a kiss on your lips!`n`%{mname}'s`^ flirt points increased by `@{pts}`^ with you!",
	"fail"=>"`^{mname}`@ attempted to flirt with you, but having heard `^{mname}`@ saying '`&{gen} is slightly substandard compared to my usual fare`@', you walk off in an understandable huff. `^{mname}'s`@ flirt points have decreased by {pts} points with you.",
	"shun"=>"`^{mname}`@ has decided to shun you.  All the flirt points you have received from them have been removed.  Perhaps you should find out why they decided to do this to you.",
	"block"=>"`^{mname}`% has decided to block you.  You will not be able to flirt with them anymore.",
	"unblock"=>"`%{mname}`^ has decided to unblock you.  You may now flirt with them again.",
	'mailheader-roses'=>'`%Roses`^ from `%{mname}`^!',
	'mailheader-drink'=>'`^A Drink`% from `^{mname}`%!',
	'mailheader-slap'=>'`@A SLAP!!!',
	'mailheader-ignore'=>'`^BYE BYE!',
	'mailheader-kiss'=>'`@A KISS!',
	'mailheader-fail'=>'`@Failed Flirt!',
	'mailheader-shun'=>'`@You have been `^Shunned`@!',
	'mailheader-block'=>'`%Flirt Block',
	'mailheader-unblock'=>'`@Flirting Unblocked',
	'cost-roses'=>get_module_setting('prroses'),
	'cost-drink'=>get_module_setting('prdrink'),
	'points-roses'=>get_module_setting('poroses'),
	'points-drink'=>get_module_setting('podrink'),
	'points-slap'=>get_module_setting('poslap'),
	'points-kiss'=>get_module_setting('pokiss'),
	'shortcut'=>array(), //insert here the items you want to have a shortcut at the flirt selection
	);
$items = modulehook("marriage-items", $items);
switch ($operation) {
	default:
		if (get_module_pref("supernoflirt")==1){
			output("You try to enter the `\$Love`@ Shack, but you are unable to do so because the `^Staff`@ have decided you can't engage in flirting.  If you believe this is an error please contact the `^Staff`@.");
		}elseif (get_module_pref("user_option")==1){
			output("You think about entering the `\$Love`@ Shack, but you've decided that you will not allow flirting to distract you from your goals.");
			output("In order to enter, you'll need to change your preference on flirting.");
		}else{
			output("`@As you stroll towards an imposing building, you notice a red heart-shaped door in the side...");
			output("Walking towards the garish portal, a strange feeling comes over you and a song pops into your head.");
			output("`n`n`c`&Bang, bang, bang on the door baby.`n`@The `\$Love`@ Shack`& is a little old place where`n We can get together!`c`n");
			output("`@Shivering, you snap out of a semi-trance.. What on earth was that?");
			output("`n`nYou knock on the ornamented gateway and wait for the door to open. Soon enough, you find yourself entering the `\$Love`@ Shack`@.");
			output("`n`nSomeone strolls up to you, and begins to speak...");
			output("`3\"`&Hello, My name is %s`& and I am a part-time Bartender as well as the owner of this establishment.`3\"",$bartender);
			output("`n`n`3\"`&How may I help you?`3\"");
		}
	break;
	case "blpropose":
		output("Unfortunately this player is not participating in proposals.");
	break;
	case "blproposing":
		if (get_module_pref("supernomarry")==1) output("You have been blocked from proposing and being proposed to.  If you feel this has been done in error please contact a staff member.");
		elseif (get_module_pref("user_wed")==1) output("You have chosen to block proposing and being proposed to.  Please go to your preferences to change this if you wish to participate in proposals.");
	break;
	case "rules":
		$cld=get_module_setting("charmleveldifference");
		$dks=get_module_setting("dks");
		rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
		rawoutput("<tr class='trhead'><td>");
		output("`c`bRules of Flirting`c`b");
		rawoutput("</td></tr>");
		rawoutput("<tr class='trlight'><td>");
		output("`\$1. `^Buying someone a drink`\$, `^roses`\$, or `^something nice`\$ like that will increase your flirt points to them.`n");
		output("`@2. `^Kissing`@ someone will increase your flirt points to them even more. You can only kiss someone who's flirted with you in the past.`n");
		output("`\$3. `^Slapping`\$ will cause your flirt points to a person to decrease. You can't slap someone if they don't have any flirt points from you. Other mean activities may have a cost.  If you don't have any flirt points with a person and you try to be mean to them, you'll end up wasting your money.`n");
		output("`@4. `^Shunning`@ a person will cause your flirt points to that person to go to zero.`n");
		output("`\$6. `^Ignoring`\$ someone will cause their flirt points to you to go to zero. You can't ignore someone if they haven't flirted with you.`n");
		output("`@7. If you have received at least `^%s`@ flirt point%s from a player, you can `^Propose to them`@! If you send at least `^%s`@ flirt point%s to a person, they can `^Propose to you`@.`n",get_module_setting('flirtmuch'),translate_inline(get_module_setting('flirtmuch')>1?"s":""),get_module_setting('flirtmuch'),translate_inline(get_module_setting('flirtmuch')>1?"s":""));
		output("`\$8. You may choose to `^Block`\$ problematic players.  These players will not be able to flirt with you.`n");
		output("`@9. Some players may decide to `#not allow flirting`@.  You will not be able to receive flirts or to flirt with these players. Players that choose to disallow flirting do not lose their flirt points. You may `^Shun`@ or `^Ignore`@ someone who is not flirting but they will NOT receive a YOM regarding the event. Other players have `#blocked proposals`@.  You may flirt with them, but they will not be eligible for marriage.`n");
		output("`\$10. Just like in real life, you can `^Shun`\$,`^ Block`\$, and `^Ignore`\$ someone even if you're married to them.");
		if (get_module_setting("charmnewday")>0) output("However, you probably won't stay married to them very long if you do.");
		output("`n`@11. If you decide to flirt with someone other than your spouse, your spouse is bound to find out.  Consider this carefully!!`n");
		output("`\$12. Sometimes your flirt doesn't work and the person you're talking to doesn't like it. That happens sometimes.`n");
		if ($dks>0 && $cld>0){
			output("`@13. You may not be able to start flirting with a person if your `&Charm`@ is more than `&%s Charm Point%s`@ higher or lower than the player you are flirting with.",$cld,($cld>1?"s":""));
			output("`\$14. You will need to have at least `^%s dragon kills`\$ before you can propose to someone.`n",get_module_setting("dks"));
		}else{
			if ($cld>0) output("`@13. You may not be able to start flirting with a person if your `&Charm`@ is more than `&%s Charm Point%s`@ higher or lower than the player you are flirting with.",$cld,($cld>1?"s":""));
			if ($dks>0) output("`@13. You will need to have at least `^%s dragon kills`@ before you can propose to someone.`n",get_module_setting("dks"));
		}
		rawoutput("</td></tr>");
		rawoutput("</table>");
	break;
	case "pointvalue":
		$type=translate_inline("Item/Action");
		$fps=translate_inline("Flirt Points");
		$cost=translate_inline("Gold");
		$drink=translate_inline("Drink");
		$fpdrink=get_module_setting("podrink");
		$codrink=get_module_setting("prdrink");
		if ($codrink==0) $codrink=translate_inline("Free");
		$roses=translate_inline("Roses");
		$fproses=get_module_setting("poroses");
		$coroses=get_module_setting("prroses");
		if ($coroses==0) $coroses=translate_inline("Free");
		$slap=translate_inline("Slap");
		$fpslap=get_module_setting("poslap")*-1;
		$kiss=translate_inline("Kiss");
		$fpkiss=get_module_setting("pokiss");
		$free=translate_inline("Free");
		rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
		rawoutput("<tr class='trhead'><td>$type</td><td><center>$fps</td><td>$cost</td></tr>");
		rawoutput("<tr class='trlight'><td>$drink</td><td><center>$fpdrink</center></td><td><center>$codrink</center></td></tr>");
		rawoutput("<tr class='trdark'><td>$roses</td><td><center>$fproses</center></td><td><center>$coroses</center></td></tr>");
		rawoutput("<tr class='trlight'><td>$kiss</td><td><center>$fpkiss</center></td><td><center>$free</center></td></tr>");
		modulehook("marriage-pointvalue");
		rawoutput("<tr class='trdark'><td>$slap</td><td><center>$fpslap</center></td><td><center>$free</center></td></tr>");
		$shun=translate_inline("Shun");
		$ignore=translate_inline("Ignore");
		$special=translate_inline("Special*");
		$block=translate_inline("Block");
		rawoutput("<tr class='trhilight'><td>$shun</td><td><center>$special</center></td><td><center>$free</center></td></tr>");
		rawoutput("<tr class='trhilight'><td>$ignore</td><td><center>$special</center></td><td><center>$free</center></td></tr>");
		rawoutput("<tr class='trhilight'><td>$block</td><td><center>$special</center></td><td><center>$free</center></td></tr>");
		rawoutput("</td></tr>");
		rawoutput("</table>");
		output("`n*Special:`n`2Shun: This will reduce all your flirt points TO a person to zero.");
		output("`n`@Ignore: This will reduce all your flirt points FROM a person to zero.");
		output("`n`2Block: This will reduce all your flirt points TO and FROM a person to zero and will prevent them from flirting with you anymore.");
	break;
	case "noflirt":
		if (get_module_pref("supernoflirt")==1){
			output("`@Unfortunately you are not allowed to flirt.  This decision has been implemented by the staff.  Please contact them if you believe you are receiving this message in error.");
		}else{
			if (get_module_pref("user_option")==1){
				output("If you wish to allow flirting, please go to your 'Preferences' and change 'Would you like to prevent all flirting' to allow flirting.");
			}else{
				output("If you wish to disallow flirting, please go to your 'Preferences' and change 'Would you like to prevent all flirting' to disallow flirting.");
			}
		}
	break;
	case "flirt":
		$stage = httpget('stage');
		$flirtitem = httpget('flirtitem');
		if ($stage=='') $stage = 0;
		//identifies if the player has been blocked by target
		$blocked=0;
		$bl = explode(',',get_module_pref('blocked','marriage',$target));
		foreach ($bl as $val) {
			if ($val!="") {
				$sql = "SELECT name,acctid FROM ".db_prefix("accounts")." WHERE acctid='$val' AND locked=0";
				$res = db_query($sql);
				if (db_num_rows($res)!=0) {
					$row = db_fetch_assoc($res);
					if ($session['user']['acctid']==$row['acctid']) $blocked=1;
				}
			}
		}
		//If the player hasn't chosen a flirt yet
		if ($stage==0) {
			require_once("./modules/marriage/flirtform.php");
			marriage_fform($flirtitem);
		//Does the player that is to be flirted with have 'No Flirting' Chosen; does NOT send a YoM to block/shun/ignore
		}elseif ((get_module_pref("user_option","marriage",$target)==1 || get_module_pref("supernoflirt","marriage",$target)==1) && $flirtitem!='block' && $flirtitem!='shun' && $flirtitem!='ignore'){
			$name = urldecode(httpget('name'));
			output("`^%s`% does not flirt.  You are not able to flirt with them.",$name);
		//Confirms if the target has chosen to block this [user] (Unless they are blocking/unblocking)
		}elseif ($blocked==1 && $flirtitem!='block' && $flirtitem!='unblock'){
			output("You cannot do anything with this player except to block or unblock them as you have been blocked by them.");
		//Clear to flirt!
		} else {
			$gendertarget = (httpget('gendertarget')?translate_inline("She"):translate_inline("He"));
			$gendertargetp = (httpget('gendertarget')?translate_inline("her"):translate_inline("his"));
			$gendertargets = (httpget('gendertarget')?translate_inline("she"):translate_inline("he"));
			$gendertargett = (httpget('gendertarget')?translate_inline("her"):translate_inline("him"));
			$name = urldecode(httpget('name'));
			//Blocking or unblocking does NOT count towards flirt points and is not restricted by flirt limitations
			$continuef=0;
			if ($flirtitem=='block' || $flirtitem=='unblock') $continuef=1;
			$allprefs=unserialize(get_module_pref('allprefs'));
			if ($continuef==0){
				$allprefs['flirtsToday']=$allprefs['flirtsToday']+1;
				set_module_pref('allprefs',serialize($allprefs));
				$allprefs=unserialize(get_module_pref('allprefs'));
			}
			if ($allprefs['flirtsToday']<=get_module_setting('maxDayFlirt') || $continuef==1) {
				//if ($flirtitem!='ignore') $pts = get_module_setting('po'.$flirtitem);
				$haspaid = true;
				$pr = $items["cost-".$flirtitem];
				if ($session['user']['gold']>=$pr && $pr>0) {
					$session['user']['gold']-=$pr;
					output("`@You pay `^%s`@ Gold...`n`n",$pr);
					} elseif ($pr>0) {
					output("`@Cheapo! You don't have enough gold for that! You need `^%s`@ Gold!",$pr);
					$haspaid=false;
				}
				//debug("Paid:".$pr." Item:".$flirtitem);
				if ($haspaid) {
					$failchance = get_module_setting('fail');
					$random = e_rand(1,100);
					if ($random<=$failchance&&$flirtitem!='ignore'&&$flirtitem!='slap') $flirtitem = "fail";
					switch ($flirtitem) {
						case "one": //auto-fail if flirtpoints are too far away
							output("`^%s`% realizes your intentions and looks disgustedly at you. Maybe you should get more charming to impress `^%s`%.",$name,$name);
						break;
						case "two": //auto-fail if flirtpoints are too far away
							output("`%You take a good look at `^%s`%. You don't think that the charm of `^%s`% is attracting you and therefore walk away.",$name,$name);
						break;
						case "fail":
							marriage_modifyflirtpoints($target,-$items["points-".$flirtitem]);
							output("`^%s`% realizes your intentions and looks disgustedly at you. Your Flirt Points decrease with `^%s`% by `^%s`%.",$name,$name,$items["points-".$flirtitem]);
							marriage_flirtdec();
						break;
						case "unblock":
							$ublo = get_module_pref('blocked');
							$my = explode(',',$ublo);
							$str = "";
							foreach ($my as $val) {
								if ($val==$target){
								}else{
									$str .= $val.",";
									if ($val>0) $count++;
								}
							}
							set_module_pref('blocked',$str);
							if ($count==0) clear_module_pref("blocked");
							output("`^You have chosen to unblock `%%s`^. They will receive a notice that you have unblocked them.  If the need arises you may block them again at any time.",$name);
						break;
						case "block":
							//Blocking is the same as shunning AND ignoring along with preventing the blocked player from flirting anymore
							$blo = get_module_pref('blocked');
							$my = explode(',',$blo);
							$str = "";
							foreach ($my as $val) {
								if ($val!='') {
									if ($val==$target) $done=1;
									else $str .= $val.",";
								}
							}
							$str.=$target;
							set_module_pref('blocked',$str);
							if ($done==1) {
								output("`%Despite your desires to block `^%s`% again, you can only block a person once.",$name);
								$nomail=1;
							}else{
								//Get the amount of flirt points:
								$list = unserialize(get_module_pref('flirtsreceived'));
								if ($list=="") $list=array();
								if (sizeof($list)>0) {
									while (list($name,$points)=each ($list)) {
										$sql = "SELECT name,acctid FROM ".db_prefix("accounts")." WHERE acctid='".substr($name,1)."' AND locked=0";
										$res = db_query($sql);
										if (db_num_rows($res)!=0) {
											$row = db_fetch_assoc($res);
											if ($row['acctid']==$target) $amount=-$points;
										}
									}
								}
								//Identify who as the user
								$who=$session['user']['acctid'];
								//Remove the points
								$list=get_module_pref('flirtssent','marriage',$target);
								$list=unserialize($list);
								if ($list=="") $list=array();
								if (array_key_exists("S".$who,$list)) {
									$list["S".$who]+=$amount; //even when negative
								} else {
									$list=array_merge(array("S".$who=>$amount),$list);
								}
								set_module_pref('flirtssent',serialize($list),'marriage',$target);
								//now for the received ones
								$list=get_module_pref('flirtsreceived','marriage',$who);
								$list=unserialize($list);
								if ($list=="") $list=array();
								if (array_key_exists("S".$target,$list)) {
									$list["S".$target]+=$amount; //even when negative
								} else {
									$list=array_merge(array("S".$target=>$amount),$list);
								}
								set_module_pref('flirtsreceived',serialize($list),'marriage',$who);
								marriage_modifyflirtpoints($target,-marriage_getflirtpoints($target),$session['user']['acctid'],false);
								output("`%You have chosen to block `^%s`%. They will receive a notice that you have blocked them. All your flirt points to and from them have been removed. If your situation improves you can unblock them if desired.",$name);
							}
						break;
						case "shun":
							//Shunning someone will cause their flirt points from you to go to zero
							//This should remove the $target's flirts from [user]
							//This should also remove the flirts received from $target on [user]
							$list = unserialize(get_module_pref('flirtssent'));
							if ($list=="") $list=array();
							if (sizeof($list)>0) {
								while (list($name,$points)=each ($list)) {
									$name=substr($name,1);
									$sql = "SELECT name,acctid FROM ".db_prefix("accounts")." WHERE acctid='$name' AND locked=0";
									$res = db_query($sql);
									if (db_num_rows($res)!=0) {
										$row = db_fetch_assoc($res);
										if ($row['acctid']==$target) $a=$points;
									}
								}
							}
							$name = urldecode(httpget('name'));
							if ($a<=0){
								output("`%You decide you want to shun `^%s`%, but since you don't have any flirt points to %s you really aren't in a position to shun %s.",$name,$gendertargett,$gendertargett);
								$nomail=1;
							}else{
								marriage_modifyflirtpoints($target,-marriage_getflirtpoints($target),$session['user']['acctid'],false);
								output("`@You have decided to shun `^%s`@ and your Flirt Points to %s`@ are now zero.",$name,$gendertargett);
								if (get_module_pref("user_option","marriage",$target)==1){
									output("`n`n`^%s`@ has chosen to not receive flirtation from anyone anymore.  Due to this decision, %s will `\$NOT`@ receive notice that you have chosen to shun %s.",$name,$gendertargets,$gendertargett);
									$nomail=1;
								}
							}
						break;
						case "ignore":
							//Ignoring someone will cause their flirt points to you to go to zero
							//This should remove the [user]'s flirts from $target
							//This should also remove the flirts received from [user] on $target
							//Get the amount of flirt points:
							$list = unserialize(get_module_pref('flirtsreceived'));
							if ($list=="") $list=array();
							if (sizeof($list)>0) {
								while (list($name,$points)=each ($list)) {
									$sql = "SELECT name,acctid FROM ".db_prefix("accounts")." WHERE acctid='".substr($name,1)."' AND locked=0";
									$res = db_query($sql);
									if (db_num_rows($res)!=0) {
										$row = db_fetch_assoc($res);
										if ($row['acctid']==$target) $amount=-$points;
									}
								}
							}
							//Identify who as the user
							$who=$session['user']['acctid'];
							//Remove the points
							$list=get_module_pref('flirtssent','marriage',$target);
							$list=unserialize($list);
							if ($list=="") $list=array();
							if (array_key_exists("S".$who,$list)) {
								$list["S".$who]+=$amount; //even when negative
							} else {
								$list=array_merge(array("S".$who=>$amount),$list);
							}
							set_module_pref('flirtssent',serialize($list),'marriage',$target);
							//now for the received ones
							$list=get_module_pref('flirtsreceived','marriage',$who);
							$list=unserialize($list);
							if ($list=="") $list=array();
							if (array_key_exists("S".$target,$list)) {
								$list["S".$target]+=$amount; //even when negative
							} else {
								$list=array_merge(array("S".$target=>$amount),$list);
							}
							$name = urldecode(httpget('name'));
							set_module_pref('flirtsreceived',serialize($list),'marriage',$who);
							output("`@You have decided to ignore `^%s`@ and your Flirt Points from %s`@ are now zero.",$name,$gendertargett);				
							if (get_module_pref("user_option","marriage",$target)==1){
								output("`n`n`^%s`@ has chosen to not receive flirtation from anyone anymore.  Due to this decision, %s will `\$NOT`@ receive notice that you have chosen to ignore %s.",$name,$gendertargets,$gendertargett);
								$nomail=1;
							}
						break;
						case "drink":
							marriage_modifyflirtpoints($target,$items["points-".$flirtitem]);
							output("`^%s`@ emphatically thanks you for the drink. Your points increase with `^%s`@ by `^%s`@.",$name,$name,$items["points-".$flirtitem]);
						break;
						case "roses":
							marriage_modifyflirtpoints($target,$items["points-".$flirtitem]);
							output("`^%s`@ gasps with delight! Your points increase with `^%s`@ by `^%s`@.",$name,$name,$items["points-".$flirtitem]);
						break;
						case "slap":
							$list = unserialize(get_module_pref('flirtssent'));
							if ($list=="") $list=array();
							if (sizeof($list)>0) {
								while (list($name,$points)=each ($list)) {
									$name=substr($name,1);
									$sql = "SELECT name,acctid FROM ".db_prefix("accounts")." WHERE acctid='$name' AND locked=0";
									$res = db_query($sql);
									if (db_num_rows($res)!=0) {
										$row = db_fetch_assoc($res);
										if ($row['acctid']==$target) $a=$points;
									}
								}
							}
							$name = urldecode(httpget('name'));
							//Prevents you from slapping someone you haven't flirted with
							if ($a<=0){
								output("`%You decide you want to slap `^%s`%, but since you don't have any flirt points to %s you really aren't in a position to slap %s.",$name,$gendertargett,$gendertargett);
								$nomail=1;
							}else{
								//this line prevents negative points if the poslap is higher than the current points
								if ($a<get_module_setting("poslap")){
									$points=$a;
									output("`%%s`% stares at you in anger, as %s`% feels the slap... Your Flirt Points decrease with `^%s`% by `^%s`% and they are now zero.",$gendertarget,$name,$name,$a);
								}else{
									$points=get_module_setting("poslap");
									output("`%%s`% stares at you in anger, as %s`% feels the slap... Your Flirt Points decrease with `^%s`% by `^%s`%.",$gendertarget,$name,$name,$points);
								}
								marriage_modifyflirtpoints($target,-$points,$session['user']['acctid'],false);
							}
						break;
						case "kiss":
							marriage_modifyflirtpoints($target,$items["points-".$flirtitem]);
							output("`@As `^%s`@ nods at you, you reach for `&%s`@ and kiss `&%s`@ lucious lips!! Your points increase with `^%s`@ by `^%s`@.",$bartender,$name,$name,$name,$items["points-".$flirtitem]);
						break;
						default:
						if ($items["points-".$flirtitem]<0){
							$list = unserialize(get_module_pref('flirtssent'));
							if ($list=="") $list=array();
							if (sizeof($list)>0) {
								while (list($name,$points)=each ($list)) {
									$name=substr($name,1);
									$sql = "SELECT name,acctid FROM ".db_prefix("accounts")." WHERE acctid='$name' AND locked=0";
									$res = db_query($sql);
									if (db_num_rows($res)!=0) {
										$row = db_fetch_assoc($res);
										if ($row['acctid']==$target) $a=$points;
									}
								}
							}
							$name = urldecode(httpget('name'));
							//Prevents you from being mean to someone you haven't flirted with
							if ($a<=0){
								output("`%You want to be mean, but since you don't have any flirt points with them, you can't do that.");
								$nomail=1;
							}else{
								//these lines prevent negative points if the value of the mean action is higher than the current points
								if (-$a>$items['points-'.$flirtitem]) $a = -$a;
								else $a=$items['points-'.$flirtitem];
								marriage_modifyflirtpoints($target,$a);
								require_once("./lib/substitute.php");
								$originalsubst=array('{name}','{gen}','{mname}','{pts}');
								$subst = array($name,$gendertarget,$session['user']['name'],$a);
								output(substitute_array($items['output-'.$flirtitem],$originalsubst,$subst));
							}
						}else{
							marriage_modifyflirtpoints($target,$items['points-'.$flirtitem]);
							require_once("./lib/substitute.php");
							$originalsubst=array('{name}','{gen}','{mname}','{pts}');
							$subst = array($name,$gendertarget,$session['user']['name'],$items["points-".$flirtitem]);
							output(substitute_array($items['output-'.$flirtitem],$originalsubst,$subst));
						}
						break;
					}
					$allprefs=unserialize(get_module_pref('allprefs'));
					if ($items["points-".$flirtitem]>0 && $allprefs['flirtspouse']==0 && get_module_setting("reqflbuff")==1 && $target==$session['user']['marriedto']){
						apply_buff('marriage-married',
							array(
								"name"=>"`^".$name."'s`@ vitality!",
								"rounds"=>40,
								"wearoff"=>"`%You feel lonely.`@",
								"defmod"=>1.03,
								"roundmsg"=>"`^".$name."`@ watches over you.",
							)
						);
						$allprefs['flirtspouse']=1;
						set_module_pref('allprefs',serialize($allprefs));
						$allprefs=unserialize(get_module_pref('allprefs'));
						output("`n`nYour flirtatious nature with your spouse gives you new vitality!");
					}
					if (isset($items["mailheader-".$flirtitem])) {
						//Mail prevented if target has blocked [user] OR there aren't enough flirt points for action.
						if ($nomail==0){
							$title = $items["mailheader-".$flirtitem];
							$text = $items[$flirtitem];
							require_once("./lib/substitute.php");
							$originalsubst=array('{name}','{gen}','{mname}','{pts}');
							$subst = array($name,$gendertarget,$session['user']['name'],$items["points-".$flirtitem]);
							$title= substitute_array($title,$originalsubst,$subst);
							$text= substitute_array($text,$originalsubst,$subst);
							require_once("lib/systemmail.php");
							systemmail($target,$title,$text);
						//debug("ac:".$target." and title:".$title." and text: ".$text);
						}
					}
				}
			} else {
				output("`@Erm.. you can't flirt any more today, pal!");
			}
		}
	break;
	case "talk":
		$max=get_module_setting('maxDayFlirt');
		$allprefs=unserialize(get_module_pref('allprefs'));
		$ft=$allprefs['flirtsToday'];
		if ($ft>$max){
			$allprefs['flirtsToday']=$max;
			set_module_pref('allprefs',serialize($allprefs));
			$allprefs=unserialize(get_module_pref('allprefs'));
			$ft=$allprefs['flirtsToday'];
		}
		output("`^%s`@ says \"`&I am a `\$%s of Love`&! I can help you with your problems. I've been watching your progress and here's what I have on file about you:`@\"`n`n`@",$bartender,$title);
		if ($ft==0)output("You haven't flirted with anyone today, but you can flirt a total of");
		else output("You've flirted `%%s`@ time%s today out of a possible",$ft,translate_inline($ft>1?"s":""));
		output("`%%s`@ time%s.`n`n",$max,translate_inline($max>1?"s":""));
		require_once("./modules/marriage/flirtlist.php");
		marriage_flist($items);
	break;
	case "bar":
		addnav("Drinks");
		modulehook("ale", array());
		require_once("lib/commentary.php");
		addcommentary();
		output("`@As you sit down at the bar, %s`@ inquires as to if you would like a drink.`nLooking around, you nod and talk to other patrons.`n",$bartender);
		viewcommentary("loveshack","`#Discourse?`@",25,"discourses");
	break;
}
addnav("Navigation");
if (get_module_setting("location")==1) addnav("Return to the Gardens","gardens.php");
else villagenav();
addnav("Actions");
if (get_module_pref("user_option")==0 && get_module_pref("supernoflirt")==0){
	addnav("`\$No Flirting",$link."&op2=noflirt");
	addnav("Rules of Flirting",$link."&op2=rules");
	addnav("Point Values",$link."&op2=pointvalue");
	addnav(array("`^Talk to %s `@`i(Your Flirt Status)`i",$bartender),$link."&op2=talk");
	addnav("The Bar",$link."&op2=bar");
	addnav("Flirt Actions");
	addnav("Buy Someone a Drink",$link."&op2=flirt&flirtitem=drink");
	addnav("Buy Someone Roses",$link."&op2=flirt&flirtitem=roses");
	addnav("Slap Someone",$link."&op2=flirt&flirtitem=slap");
	addnav("Shun Someone",$link."&op2=flirt&flirtitem=shun");
	addnav("Block Someone",$link."&op2=flirt&flirtitem=block");
	if (get_module_pref("blocked")!="") addnav("Unblock Someone",$link."&op2=flirt&flirtitem=unblock");
}elseif (get_module_pref("supernoflirt")==1) addnav("Flirting Restriction",$link."&op2=noflirt");
else addnav("Allow Flirting",$link."&op2=noflirt");
if (get_module_setting("newlist")==0){
	addnav("Love & Lust");
	addnav("Newly Weds","runmodule.php?module=marriage&op=newlyweds");
}
page_footer();
?>