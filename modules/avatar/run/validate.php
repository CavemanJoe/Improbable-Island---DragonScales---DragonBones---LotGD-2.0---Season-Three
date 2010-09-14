<?php
		$mode=httpget("mode");
		require_once("lib/superusernav.php");
		superusernav();
		check_su_access(SU_AUDIT_MODERATION);
		switch($mode) {
			case "invalidate":
				$search=httppost('search');
				$who=httpget('who');
				if ($who=='') {
					$send=translate_inline("Search");
					output("Whose avatar do you want to invalidate?`n`n");
					rawoutput("<form method='POST' action='runmodule.php?module=avatar&op=validate&mode=invalidate'>");
					rawoutput("<input name='search' type='text' size='40' value=$search>");
					rawoutput("<input type='submit' class='button' value=$send>");
					rawoutput("</form>");
					addnav("","runmodule.php?module=avatar&op=validate&mode=invalidate");
					output_notl("`n`n");
					if ($search) {
						$name="%".$search."%";
						$sql="SELECT u.userid AS acctid,k.name as name, k.login as login FROM ".db_prefix("module_userprefs")." AS u INNER JOIN ".db_prefix("module_userprefs")." AS t RIGHT JOIN ".db_prefix("accounts")." as k ON u.userid=t.userid AND k.acctid=u.userid WHERE u.modulename='avatar' AND u.setting='avatar' AND u.value!='' AND t.modulename='avatar' AND t.setting='validated' AND t.value='1' AND (k.name LIKE '$name' OR k.login LIKE '$name');";
						$result=db_query($sql);
						if (db_num_rows($result)>100) {
							output("There are more than 100 matches. Please specify the user a bit more.");
							addnav("runmodule.php?module=avatar&op=validate&mode=invalidate");
							break;
						}
						if (db_num_rows($result)==0) {
							output("No user with a valid personal avatar found matching this criteria.");
						} else {
							output("Click on the name to YOM a user and on the picture to view it in original size.`n`n");
							rawoutput("<table border='0' cellpadding='2' cellspacing='0' width='100%'>");
							rawoutput("<tr class='trhead'><td>". translate_inline("Picture") ."</td><td>". translate_inline("Name") ."</td><td>".translate_inline("Ops")."</td></tr>");
							$i=0;
							while ($row=db_fetch_assoc($result)) {
								$i++;
								$url=get_module_pref("avatar","avatar",$row['acctid']);
								rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td><a href='$url' target='_blank'><img src='$url' align='center' width='150' height='150'></a></td><td>");
								rawoutput("<a href='mail.php?op=write&to={$row['login']}' class='colLtGreen' target='_blank' onClick=\"".popup("mail.php?op=write&to={$row['login']}").";return false;\">".sanitize($row['name'])."</a></td><td>");
								$inval=translate_inline("Invalidate");
								rawoutput("<a href='runmodule.php?module=avatar&op=validate&mode=invalidate&who={$row['acctid']}'>$inval</a></td></tr>");
								addnav("","runmodule.php?module=avatar&op=validate&mode=invalidate&who={$row['acctid']}");
							}
							rawoutput("</table>");
						}
					}

				} else {
					set_module_pref("validated",0,"avatar",$who);
					output("Avatar invalidated and user notified!");
					require_once("./lib/systemmail.php");
					systemmail($who,array("Your avatar has been invalidated!"),array("`^As of now, your avatar has been invalidated because it did meet the requirements of this server.`nYou won't be refunded points for a violation.`n`n If you replace the picture at this URL, petition about it and we will reevaluate your avatar.`nIf you decide to use another URL, the normal validation process will take place automatically.`n`nRegards`n%s`^`n `&Staff",$session['user']['name']));

				}
				addnav("Avatars");
				addnav("Return to the main menu","runmodule.php?module=avatar&op=validate");

				break;
			case "validate":
				if (httpget('giveok')) {
					output("`lAvatar has been validated!`0`n`n");
					set_module_pref("validated",1,"avatar",httpget('user'));
					require_once("./lib/systemmail.php");
					systemmail(httpget('user'),array("Your avatar has been validated!"),array("`^As of now, your avatar will be visible.`n`nRegards`n%s`^`n `&Staff",$session['user']['name']));
				} else {
					output_notl("`n`n");
				}
				$who=httpget('who');
				if ($who) {
					$where="AND a.acctid='$who'";
				} else 
					$where='';
				$sql="SELECT a.login as login, u.userid as acctid ,a.name as name,a.emailaddress as email FROM ".db_prefix("module_userprefs")." AS u INNER JOIN ".db_prefix("module_userprefs")." AS t RIGHT JOIN ".db_prefix("accounts")." AS a ON u.userid=t.userid AND a.acctid=u.userid WHERE u.modulename='avatar' AND u.setting='avatar' AND u.value!='' AND t.modulename='avatar' AND t.setting='validated' AND t.value!='1' $where ORDER BY rand(".e_rand().") LIMIT 1;";
				$result=db_query($sql);
				$num=db_num_rows($result);
				if ($num==0) {
					output("No avatars left! You validated the last one (for now).");
					break;
				}
				$row=db_fetch_assoc($result);
				$url=get_module_pref("avatar","avatar",$row['acctid']);
				$image="<img align='left' src='".$url."' ";
				if (get_module_setting("restrictsize")) {
					$maxwidth = get_module_setting("maxwidth");
					$maxheight = get_module_setting("maxheight");
					$pic_size = @getimagesize($picname); 
					$pic_width = $pic_size[0];
					$pic_height = $pic_size[1];
					//other arguments are channels,bits etc
					
					//aspect ratio. We are scaling for height/width ratio
					$resizedwidth=$pic_width;
					$resizedheight=$pic_height;
					if ($pic_height > $maxheight) {
						$resizedheight=$maxheight;
						$resizedwidth=round($pic_width*($maxheight
/$pic_height));
					}
					if ($resizedwidth > $maxwidth) {
						$resizedheight=round($resizedheight*($maxwidth
/$resizedwidth));
						$resizedwidth=$maxwidth;
						
					}
					$image.=" height=\"$resizedheight\"  width=\"$resizedwidth\" ";
				}
				$image.=">";
				rawoutput("<table><tr><td valign='top'>");
				output("`^Avatar:`0`n");
				rawoutput("</td><td valign='top'>$image</td></tr><td></td><td>$url</td></table>");				addnav("Actions");
				output("Username: ");
				rawoutput("<a href='mail.php?op=write&to={$row['login']}' class='colLtGreen' target='_blank' onClick=\"".popup("mail.php?op=write&to={$row['login']}").";return false;\">".sanitize($row['name'])."</a>");
				output_notl("`n");
				output("Email: ");
				rawoutput("<a href='mailto:{$row['email']}'>{$row['email']}</a>");
				output_notl("`n`n");
				output("Click on the name to YOM and click the email to email this user.");
				addnav("Avatars");
				addnav("Return to the main menu","runmodule.php?module=avatar&op=validate");
				addnav("Validate this Avatar","runmodule.php?module=avatar&op=validate&mode=validate&giveok=1&user={$row['acctid']}");
				addnav("Ignore this Avatar","runmodule.php?module=avatar&op=validate&mode=validate");

				break;
				
			default:
				$user=httpget('user');
				if ($user) {
					if (httpget('giveok') || httpget('deny')) {
						$ok=(get_module_pref('validated','avatar',$user)==0?1:0);
					}
				}
				if (httpget('giveok') && $ok) {
					$sql="SELECT name FROM ".db_prefix('accounts')."  WHERE acctid=".((int)$user);
					$result=db_query($sql);
					$row=db_fetch_assoc($result);
					$username=$row['name'];				
					output("`lAvatar has been validated!`0`n`n");
					set_module_pref("validated",1,"avatar",$user);
					require_once("./lib/systemmail.php");
					systemmail($user,array("Your avatar has been validated!"),array("`^As of now, your avatar will be visible.`n`nRegards`n%s`^`n `&Staff",$session['user']['name']));
					debuglog(sprintf("Avatar was validated by %s - mailed",sanitize($session['user']['name'])),$user,$user);
					$text="/me`3 validated the avatar of `%$username`3 located at ".get_module_pref('avatar','avatar',$user);
					require_once("lib/commentary.php");
					injectrawcomment("AvatarVal",$session['user']['acctid'],$text);
				} elseif (httpget('deny') && $ok) {
					$sql="SELECT name FROM ".db_prefix('accounts')."  WHERE acctid=".((int)$user);
					$result=db_query($sql);
					$row=db_fetch_assoc($result);
					$username=$row['name'];
					output("`lAvatar has been set to default picture and user notified to provide a new link!`0`n`n");
					$text="/me`3 denied the avatar of `%$username`3 located at ".get_module_pref('avatar','avatar',$user);
					set_module_pref("validated",1,"avatar",$user);
					set_module_pref("avatar","modules/avatar/default.jpg","avatar",$user);
					require_once("./lib/systemmail.php");
					systemmail($user,array("`xYour avatar has `\$NOT`x been validated!"),array("`^Your picture is not within the local policies/rules. Please choose a new one and petition/mail the appropriate link to us. If you want to upload a new one and need the points back, please also do so.`n`nRegards`n%s`^`n `&Staff",$session['user']['name']));
					debuglog(sprintf("Avatar was not found suitable by %s - reset and mailed",sanitize($session['user']['name'])),$user,$user);
					require_once("lib/commentary.php");
					injectrawcomment("AvatarVal",$session['user']['acctid'],$text);
				} else {
					output_notl("`n`n");
				}

				$sql="SELECT count(u.userid) AS counter FROM ".db_prefix("module_userprefs")." AS u INNER JOIN ".db_prefix("module_userprefs")." AS t ON u.userid=t.userid AND u.modulename='avatar' AND u.setting='avatar' AND u.value!='' AND t.modulename='avatar' AND t.setting='validated' AND t.value!='1';";
				$result=db_query($sql);
				$num=db_fetch_assoc($result);
				output("Currently there are %s avatars waiting for validation.`n`n",$num['counter']);
				addnav("Actions");
				if ($num['counter']>0) {
					addnav("Random Validate Avatars","runmodule.php?module=avatar&op=validate&mode=validate");
					$sql="SELECT a.login as login, u.userid as acctid ,a.name as name,a.emailaddress as email FROM ".db_prefix("module_userprefs")." AS u INNER JOIN ".db_prefix("module_userprefs")." AS t RIGHT JOIN ".db_prefix("accounts")." AS a ON u.userid=t.userid AND a.acctid=u.userid WHERE u.modulename='avatar' AND u.setting='avatar' AND u.value!='' AND t.modulename='avatar' AND t.setting='validated' AND t.value!='1' ORDER BY a.login LIMIT 20;";
				$result=db_query($sql);
				output("Click on the name to YOM a user and on a picture to view it in original size.`n`n");
				rawoutput("<table border='0' cellpadding='2' cellspacing='0' width='100%'>");
	rawoutput("<tr class='trhead'><td>". translate_inline("Picture") ."</td><td>". translate_inline("Name") ."</td><td>".translate_inline("Ops")."</td></tr>");
				$i=0;
				$details=translate_inline("Details");
				$validate=translate_inline("Validate directly");
				$deny=translate_inline("Deny+Set Default Picture");
				while ($row=db_fetch_assoc($result)) {
					$i=!$i;
					$url=get_module_pref("avatar","avatar",$row['acctid']);
					rawoutput("<tr class='".($i?"trlight":"trdark")."'><td><a href='$url' target='_blank'><img src='$url' align='center' width='150' height='150'></a></td><td>");
					rawoutput("<a href='mail.php?op=write&to={$row['login']}' class='colLtGreen' target='_blank' onClick=\"".popup("mail.php?op=write&to={$row['login']}").";return false;\">".sanitize($row['name'])."</a></td><td>");
					rawoutput("<a href='runmodule.php?module=avatar&op=validate&mode=validate&who={$row['acctid']}'>$details</a><br><br>");
					addnav("","runmodule.php?module=avatar&op=validate&mode=validate&who={$row['acctid']}");
					rawoutput("<a href='runmodule.php?module=avatar&op=validate&mode=&giveok=1&user={$row['acctid']}'>$validate</a><br><br>");
					rawoutput("<a href='runmodule.php?module=avatar&op=validate&mode=&deny=1&user={$row['acctid']}'>$deny</a><br><br></td></tr>");
					addnav("","runmodule.php?module=avatar&op=validate&mode=&deny=1&user={$row['acctid']}");
					addnav("","runmodule.php?module=avatar&op=validate&mode=&giveok=1&user={$row['acctid']}");
					
				}
				rawoutput("</table>");
				
				}
				addnav("Invalidate Avatars","runmodule.php?module=avatar&op=validate&mode=invalidate");
			break;
		}
require_once("lib/commentary.php");
addcommentary();	
commentdisplay("`n`n`@Validation Discussions`n","AvatarVal","Talk",30,"says");
?>