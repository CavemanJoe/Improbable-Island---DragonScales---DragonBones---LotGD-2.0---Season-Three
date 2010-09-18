<?php
switch($hookname){
	case "allprefs": case "allprefnavs":
		if ($session['user']['superuser'] & SU_EDIT_USERS){
			$id=httpget('userid');
			addnav("Marriage");
			addnav("Marriage Expansion","runmodule.php?module=marriage&op=superuserap&subop=edit&userid=$id");
		}
	break;
	case "footer-hof":
		if (get_module_setting("newlist")==3){
			addnav("Marriages");
			addnav("Newlyweds","runmodule.php?module=marriage&op=newlyweds");
		}
	break;
	case "drinks-text":
		$allprefs=unserialize(get_module_pref('allprefs'));
		if ($allprefs['inShack']==1) {
			$args['title']="The Loveshack";
			$args['barkeep']=get_module_setting("bartendername");
			$args['return']="Sit back at the Bar";
			$args['returnlink']="runmodule.php?module=marriage&op=loveshack&op2=bar";
			$args['demand']="Giggling on the floor, you yell for another drink";
			$args['toodrunk']=" and so ".get_module_setting("bartendername")." places one on the bar.. however, you are too drunk to pick it up, so ".get_module_setting("bartendername")." leaves it to rot..";
			$args['toomany']=get_module_setting("bartendername")." `3apologizes, \"`&You've cleaned the place out.`3\"";
			$array = array("title","barkeep","return","demand","toodrunk","toomany","drinksubs");
			$schemas=array();
			foreach ($array as $val) {
				$schemas[$val]="module-marriage";
			}
			$args['schemas']=$schemas;
			$args['drinksubs']=array(
				"/^he/"=>"^".(get_module_setting('genderbartender')?translate_inline("she"):translate_inline("he")),
				"/ he/"=>" ".(get_module_setting('genderbartender')?translate_inline("she"):translate_inline("he")),
				"/^his/"=>"^".(get_module_setting('genderbartender')?translate_inline("her"):translate_inline("his")),
				"/ his/"=>" ".(get_module_setting('genderbartender')?translate_inline("her"):translate_inline("his")),
				"/^him/"=>"^".(get_module_setting('genderbartender')?translate_inline("her"):translate_inline("him")),
				"/ him/"=>" ".(get_module_setting('genderbartender')?translate_inline("her"):translate_inline("him")),
				"/{barkeep}/"=>get_module_setting("bartendername"),
				"/Violet/"=>translate_inline("a stranger"),
				"/Seth/"=>translate_inline("a stranger"),
			);
		}
	break;
	case "drinks-check":
		$allprefs=unserialize(get_module_pref('allprefs'));
		if ($allprefs['inShack']==1) {
			$args['allowdrink'] = get_module_objpref('drinks',$args['drinkid'],'drinkLove');
		} else {
			$args['allowdrink'] = get_module_objpref('drinks',$args['drinkid'],'loveOnly');
		}
	break;
	case "moderate":
		if (get_module_setting('oc')==0) {
			$args['marriage'] = 'The Chapel';
		} else {
			$args['marriage'] = 'The Old Church';
		}
		if (get_module_setting('flirttype')==1) {
			$args['loveshack'] = 'The Loveshack';
		}
	break;
	case "newday":
		require_once("modules/marriage/marriage_func.php");
		$allprefs=unserialize(get_module_pref('allprefs'));
		$allprefs['flirtsToday']=0;
		$allprefs['flirtspouse']=0;
		$allprefs['inShack']=0;
		set_module_pref('allprefs',serialize($allprefs));
		$allprefs=unserialize(get_module_pref('allprefs'));
		if ($session['user']['marriedto']!=0 && $session['user']['marriedto']!=4294967295) {
			$sql = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid=".$session['user']['marriedto']." AND locked=0";
			$res = db_query($sql);
			$row = db_fetch_assoc($res);
			$namepartner = $row['name'];
			if (db_num_rows($res)<1) {
				$session['user']['marriedto']=0;
				debuglog("divorced with no notice due to death of the spouse");
				output("`n`@You feel sorrow for the death of your spouse.");
				apply_buff('marriage-death',
					array(
						"name"=>"`6Sorrow",
						"rounds"=>80,
						"wearoff"=>"`^You start to recover..",
						"defmod"=>1.10,
						"survivenewday"=>1,
						"roundmsg"=>"`6Sorrow gives you pain. Your pain gives you anger. Your anger gives you strength.",
						)
				);
				set_module_pref('allprefs',serialize($allprefs));
				break;
			}
			//give players whose proposals were accepted their buff
			if ($allprefs['received']==1){
				apply_buff('marriage-start',
					array(
						"name"=>"`@Marriage",
						"rounds"=>40,
						"wearoff"=>"`&The elation wears off.",
						"defmod"=>1.13,
						"roundmsg"=>"`@You are elated at your marriage",
						)
				);
				output("`n`@The fact that you just got married finally hits you. It's going to be a great day!");
			}
			if (marriage_getflirtpoints($session['user']['marriedto']) < get_module_setting('flirtmuch') && get_module_setting('pointsautodivorceactive')) {
				output_notl("`n");
				output("`bWhen  you  wake  up, you find a note next to you, reading`n`5Dear %s`5,`n",$session['user']['name']);
				output("Despite  many  great  kisses, I find that I'm simply no longer attracted to you the way I used to be.`n`n");
				output("Call  me fickle, call me flakey, but I need to move on.");
				output("There are other warriors in the land, and I think some of them are really hot.");
				output("So it's not you, it's me, etcetera etcetera.`n`n");
				output("No hard feelings, Love,`n%s`b`n",$namepartner);
				require_once("./modules/marriage/divorce.php");
				marriage_divorce();
				break;
			} elseif ($allprefs['received']==0 && (get_module_setting("flirttype")==0 || (get_module_setting("reqflbuff")==0 && get_module_setting("flirttype")==1))) {
				output("`n`@You and %s`0`@ spend the night in the inn together, and you both emerge positively glowing!",$namepartner);
				apply_buff('marriage-married',
					array(
							"name"=>array("`\$%s`^'s Love",$namepartner),	
							"rounds"=>60,
							"wearoff"=>"`%You feel lonely.`@",
							"defmod"=>1.03,
							"roundmsg"=>array("`4 %s`@ watches over you",$namepartner),
						)
					);
			}
			$allprefs['received']=0;
			set_module_pref('allprefs',serialize($allprefs));
			$allprefs=unserialize(get_module_pref('allprefs'));
			//decrease points per day
			$howmuch=get_module_setting('charmnewday');
			if ($howmuch) {
				marriage_modifyflirtpoints($session['user']['marriedto'],-$howmuch);
				output("`n`@Your flirt points with `^%s`@ decrease as you are getting more used to each other.",$namepartner);
			}
			if (get_module_setting('flirtAutoDiv')==1&&get_module_setting('flirtAutoDivT')>0) {
				
				if ($allprefs['flirtsfaith']>=get_module_setting('flirtAutoDivT')) {
					$allprefs['flirtsfaith']=0;
					set_module_pref('allprefs',serialize($allprefs));
					if (get_module_setting('oc')==1) $location = 'oldchurch';
					else $location = 'chapel';
					$location='chapel';
					$t = array("`%Uh oh!");
					require_once("lib/systemmail.php");
					if ($session['user']['marriedto']!=0&&$session['user']['marriedto']!=4294967295) {
						$mailmessage=array("%s`0`@ was forced by you to get a divorce, due to being unfaithful.",$session['user']['name']);
						systemmail($session['user']['marriedto'],$t,$mailmessage);
					}
					$mailmessage=array("You were forced to get a divorce, due to being unfaithful.","");
					systemmail($session['user']['acctid'],$t,$mailmessage);
					redirect('runmodule.php?module=marriage&op=chapel&op2=divorce','Auto-Divorce');
				}
			}
		}
		
		//give players who were divorced their buff
		$allprefs=unserialize(get_module_pref('allprefs'));
		if ($allprefs['received']==2){
			apply_buff('marriage-divorce',
				array(
					"name"=>"`4Divorce Sadness",
					"rounds"=>50,
					"wearoff"=>"`\$You no longer feel sad about your divorce.",
					"defmod"=>0.92,
					"survivenewday"=>1,
					"roundmsg"=>"`\$Sadness haunts you.",
				)
			);
			output("`n`\$The fact that you've been divorced finally hits you.  It's going to be a sad day.");
			$allprefs['received']=0;
		}
		set_module_pref('allprefs',serialize($allprefs));
		marriage_seencleanup($session['user']['acctid']);
		marriage_receivedcleanup($session['user']['acctid']);
	break;
  		case "changesetting":
		if ($args['setting'] == "villagename") {
			if ($args['old'] == get_module_setting("chapelloc")) {
				set_module_setting("chapelloc", $args['new']);
			}
		}
		if ($args['setting'] == "oc" && $args['module']=='marriage') {
			if ($args['new']==1&&!is_module_active('oldchurch')) {
				$args['new']=0;
				set_module_setting('oc',0);
				output_notl("`n`c`b`QMarriage Module - Old Church is not installed`0`b`c");
			}
		}
	break;
	case "footer-inn":
		set_module_pref('inShack',0);
		if (httpget('op')) break;
		if ($session['user']['marriedto']!=0&&$session['user']['marriedto']!=4294967295&&is_module_active('lovers')) {
			addnav("Things to do");
			blocknav("runmodule.php?module=lovers&op=flirt",true);
			require_once("lib/partner.php");
			$namepartner=get_partner();
			addnav(array("F?Flirt with %s`0",$namepartner),"runmodule.php?module=marriage&op=innflirt");
		}
	break;
	case "footer-oldchurch":
		if (!is_module_active('oldchurch')) set_module_setting('oc',0);
		$module = httpget('module');
		$op = httpget('op');
		if (get_module_setting('oc')&&$module=='oldchurch'&&$op=='enter') addnav("Marriage Wing","runmodule.php?module=marriage&op=oldchurch");
	break;

	case "footer-gardens":
		if (get_module_setting("newlist")==1 || (get_module_setting("newlist")==0 && get_module_setting("flirttype")==0)){
			addnav("Love & Lust");
			addnav("Newly Weds","runmodule.php?module=marriage&op=newlyweds");
		}
		$allprefs=unserialize(get_module_pref('allprefs'));
		if (get_module_setting("location")==1){
			if (get_module_setting('counsel')==1&&$allprefs['counsel']==1) addnav("Social Counselling","runmodule.php?module=marriage&op=counselling");
			if ($session['user']['location'] == get_module_setting("chapelloc")&&get_module_setting("all")==0&&get_module_setting('oc')==0) addnav(array("%s %s Chapel",$session['user']['location'],get_module_setting('name')),"runmodule.php?module=marriage&op=chapel");
			elseif (get_module_setting("all")==1&&get_module_setting('oc')==0) addnav(array("%s Chapel",get_module_setting('name')),"runmodule.php?module=marriage&op=chapel");
			if (get_module_setting('flirttype')==1) {
				if ($session['user']['location'] == get_module_setting("loveloc")&&get_module_setting("lall")==0) addnav(array("%s Loveshack",$session['user']['location']),"runmodule.php?module=marriage&op=loveshack");
				elseif (get_module_setting("lall")==1) addnav("The Loveshack","runmodule.php?module=marriage&op=loveshack");
			}
			set_module_pref('inShack',0);
			$allprefs['inShack']=0;
			set_module_pref('allprefs',serialize($allprefs));
		}
	break;
	case "village":
		if (get_module_setting("newlist")==2 && $session['user']['location'] == get_module_setting("newloc")){
			tlschema($args['schemas']['tavernnav']);
			addnav($args['tavernnav']);
			tlschema();
			addnav("Newlyweds","runmodule.php?module=marriage&op=newlyweds");
		}
		if (get_module_setting("location")==0){
			tlschema($args['schemas']['tavernnav']);
			addnav($args['tavernnav']);
			tlschema();
			$allprefs=unserialize(get_module_pref('allprefs'));
			if (get_module_setting('counsel')==1&&$allprefs['counsel']==1) addnav("Social Counselling","runmodule.php?module=marriage&op=counselling");
			if ($session['user']['location'] == get_module_setting("chapelloc")&&get_module_setting("all")==0&&get_module_setting('oc')==0) addnav(array("%s %s Chapel",$session['user']['location'],get_module_setting('name')),"runmodule.php?module=marriage&op=chapel");
			elseif (get_module_setting("all")==1&&get_module_setting('oc')==0) addnav(array("%s Chapel",get_module_setting('name')),"runmodule.php?module=marriage&op=chapel");
			if (get_module_setting('flirttype')==1) {
				if ($session['user']['location'] == get_module_setting("loveloc")&&get_module_setting("lall")==0) addnav(array("%s Loveshack",$session['user']['location']),"runmodule.php?module=marriage&op=loveshack");
				elseif (get_module_setting("lall")==1) addnav(array("The Loveshack"),"runmodule.php?module=marriage&op=loveshack");
			}
			$allprefs['inShack']=0;
			set_module_pref('allprefs',serialize($allprefs));
		}
	break;
	case "delete_character":
		//new version for new character cleanup hook
		$acctids = $args['ids'];
		$joined = join($acctids,",");
		
		$sql = "UPDATE ".db_prefix("accounts")." SET marriedto=0 WHERE acctid IN ($joined)";
		db_query($sql);
	
		// $sql = "SELECT name,marriedto FROM ".db_prefix("accounts")." WHERE acctid='{$args['acctid']}' AND locked=0";
		// $res = db_query($sql);
		// if (db_num_rows($res)!=0) {
			// $row = db_fetch_assoc($res);
			// if ($row['marriedto']!=0&&$row['marriedto']!=4294967295) {
				// $mailmessage=array("%s`0`@ has committed suicide by jumping off a cliff.",$row['name']);
				// $t = array("`%Suicide!");
				// require_once("lib/systemmail.php");
				// systemmail($row['marriedto'],$t,$mailmessage);
				// $sql = "UPDATE " . db_prefix("accounts") . " SET marriedto=0 WHERE acctid='{$row['marriedto']}'";
				// db_query($sql);
			// }
		// }
		// $list=get_module_pref('flirtssent');
		// $list=unserialize($list);
		// require_once("./modules/marriage/marriage_func.php");
		// if (!is_array($list)) break;
		// while (list($who,$amount)=each($list)) {
			// marriage_removeplayer($who,$session['user']['acctid']);
		// }
	break;
	case "charstats":
		if ($session['user']['marriedto']!=0&&get_module_pref('user_stats')) {
			require_once("lib/partner.php");
			$partner=get_partner(true);
			setcharstat("Personal Info","Marriage","`^".translate_inline("Married to ").$partner);
		}
	break;
	case "faq-toc":
		$t = translate_inline("`@Frequently Asked Questions on Marriage`0");
		output_notl("&#149;<a href='runmodule.php?module=marriage&op=faq'>$t</a><br/>",true);
		addnav("","runmodule.php?module=marriage&op=faq");
	break;
	case "biostat":
		$char = httpget('char');
		$sql = "SELECT a.acctid as userid, a.marriedto as married, b.name as partnername,a.sex as gender FROM ".db_prefix('accounts')." as a LEFT JOIN ".db_prefix('accounts')." as b ON a.marriedto=b.acctid WHERE a.acctid='$char'";
		$results = db_query($sql);
		$row = db_fetch_assoc($results);
		if ($row['married']!=0 && $row['partnername']!="") {
			if (!get_module_pref('user_bio',"marriage",$row['userid'])) $row['partnername']="`iSecret`i";
			output("`^Spouse: `2%s`n",$row['partnername']); //do it here
		} elseif ($row['married']==INT_MAX) {
			$partner = getsetting("barmaid", "`%Violet");
			if ($row['gender'] != SEX_MALE) {
				$partner = getsetting("bard", "`^Seth");
			}
			if (!get_module_pref('user_bio',"marriage",$row['userid'])) $row['partnername']="`iSecret`i";
			output("`^Spouse: `2%s`n",$partner);
		}
	break;
	case "superuser":
		if (($session['user']['superuser'] & SU_EDIT_USERS)|| get_module_pref("wededit")==1){
			addnav("Editors");
			addnav("Marriage Editor","runmodule.php?module=marriage&op=superuser");
		}
	break;
}
?>