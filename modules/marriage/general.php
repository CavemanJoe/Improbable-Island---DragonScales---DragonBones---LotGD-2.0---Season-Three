<?php
$operation = httpget('op2');
$target = httpget('target');
$vicar = '`5Capelthwaite';
$gendervicar = translate_inline('He');
$gendervicarpersonal = translate_inline('himself');
if ($op=='oldchurch') {
	page_header("The Marriage Wing");
} else {
	page_header("The Chapel");
	output("`b`c`&The Chapel`b`c`n");
	$vicar = get_module_setting('vicarname');
	$gendervicar = (get_module_setting('gendervicar')?translate_inline("She"):translate_inline("He"));
	$gendervicarpersonal = (get_module_setting('gendervicar')?translate_inline("herself"):translate_inline("himself"));

}
switch ($operation) {
	case "proposelist":
		require_once("./modules/marriage/proposal.php");
		marriage_plist($op);
		break;
	case "nopropose":
		if (get_module_pref("supernomarry")==1) output("You have been blocked from proposing and being proposed to.  If you feel this has been done in error please contact a staff member.");
		elseif (get_module_pref("user_wed")==1)output("You have chosen to block proposing and being proposed to.  Please go to your preferences to change this if you wish to participate in proposals.");
	break;
	case "propose":
		$allprefs=unserialize(get_module_pref('allprefs'));
		if (get_module_setting('flirttype')) output("`@Having run to your local vicar, you propose.`n`n");
		if (get_module_pref("supernomarry","marriage",$target)==1 || get_module_pref("user_wed","marriage",$target)==1){
			output("The player you are attempting to propose to is not participating in proposals.");
		}elseif (httpget('stage')==1) {
			$i = get_module_pref('proposals','marriage',$target);
			$h = explode(',',$i);
			if (in_array($session['user']['acctid'],$h)) {
				output("`^%s `3frowns, \"`&You've already proposed to that person.`3\"",$vicar);
			} else {
				$mailmessage=array("%s`0`@ has proposed to you.",$session['user']['name']);
				$t = array("`@Proposal!");
				require_once("lib/systemmail.php");
				systemmail($target,$t,$mailmessage);
				array_push($h,$session['user']['acctid']);
				$i = implode(",",$h);
				set_module_pref('proposals',$i,'marriage',$target);
				$allprefs['buyring']=0;
				set_module_pref('allprefs',serialize($allprefs));
				$allprefs=unserialize(get_module_pref('allprefs'));
				debuglog("proposed to player number $target");
				if (get_module_setting('cost')>0) {
					output("`^%s `3says, \"`&Ah, your wedding ring has been sent. Now you just have to wait to see if they accept.`3\"",$vicar);
				} else {
					output("`^%s `3says, \"`&Ah, a wedding ring has been sent. Now you just have to wait to see if they accept.`3\"",$vicar);
				}
			}
		} elseif ((get_module_setting('cost')>0&&$allprefs['buyring']==1) || get_module_setting('cost')==0) {
			output("`^%s `3says, \"`&Please let me know who you are interested in.`3\"`n",$vicar);
			require_once("./modules/marriage/proposal.php");
			marriage_pform($op);
		} else {
			output("`^%s `3says, \"`&You haven't bought an engagement ring! You can't propose.",$vicar);
			output("I have some I could sell, and I'll sell you one.. for a small fee.`3\"");
			addnav("Actions");
			addnav("Ask about a Ring","runmodule.php?module=marriage&op=".$op."&op2=ringbuy&stage=0");
		}
		break;
	case "ringbuy":
		if (httpget('stage')==1) {
			$allprefs=unserialize(get_module_pref('allprefs'));
			$allprefs['buyring']=1;
			set_module_pref('allprefs',serialize($allprefs));
			$allprefs=unserialize(get_module_pref('allprefs'));
			$session['user']['gold']-=get_module_setting('cost');
			//This had to be split so that if flirting is enabled players HAVE to go back to the shack.
			if (get_module_setting("flirttype")==1) output("`^%s `3takes `^%s gold`3 from you. \"`&Now that you have a ring, you just have to propose.  You'll have to go back to the `\$Love `@Shack`& and find your beloved to propose to there.`3\"",$vicar,get_module_setting('cost'));
			else{
				addnav("Actions");
				addnav("Propose","runmodule.php?module=marriage&op2=propose&op=chapel&stage=0");
				blocknav("runmodule.php?module=marriage&op=".$op."&op2=propose");
				output("`^%s `3takes `^%s gold`3 from you. \"`&Now that you have a ring, you just have to propose.`3\"",$vicar,get_module_setting('cost'));
			}
		} else {
			$ringcost=get_module_setting('cost');
			output("`^%s `3reaches into a pocket and takes out a ring and says.",$vicar);
			if ($ringcost>0) output("\"`&This ring costs `^%s gold`&.`3\"",$ringcost);
			else output("\"`&Since you look like such a nice person, I'm going to give this to you.`3\"");
			if ($ringcost<=0){
				addnav("Actions");
				addnav("Buy a Ring","runmodule.php?module=marriage&op=".$op."&op2=ringbuy&stage=1");
			}elseif ($session['user']['gold']<$ringcost) output("`n`nYou show `^%s `3your gold pouch and says, \"`&You haven't got enough for this ring.`3\"",$vicar);
			else {
				output("`n`nAfter taking a look at your gold pouch, `^%s `3 says \"`&You've got enough for this ring.`3\"",$vicar);
				addnav("Actions");
				addnav("Buy a Ring","runmodule.php?module=marriage&op=".$op."&op2=ringbuy&stage=1");
			}
		}
		break;
	case "talk":
		require_once("lib/commentary.php");
		addcommentary();
		output("`@You hear people whispering...`n`0");
		viewcommentary("marriage","Whisper?",25);
		break;
	case "marry":
		$allprefs=unserialize(get_module_pref('allprefs'));
		$allprefs['flirtsfaith']=0;
		set_module_pref('allprefs',serialize($allprefs));
		$stuff = explode(',',get_module_pref('proposals'));
		$i = "";
		foreach ($stuff as $val) {
			if ($val!=""&&$val!=$target&&$val!=$session['user']['acctid']) {
				$i .= ",".$val;
			}
		}
		set_module_pref('proposals',$i);
		$stuff = explode(',',get_module_pref('proposals','marriage',$target));
		$i = "";
		foreach ($stuff as $val) {
			if ($val!=""&&$val!=$target&&$val!=$session['user']['acctid']) {
				$i .= ",".$val;
			}
		}
		set_module_pref('proposals',$i,'marriage',$target);
		$mailmessage=array("`^%s`0`@ has married you!",$session['user']['name']);
		if (get_module_setting("acceptbuff")==1) {
			$allprefst=unserialize(get_module_pref('allprefs','marriage',$target));
			$allprefst['received']=1;
			set_module_pref('allprefs',serialize($allprefst),'marriage',$target);
			$mailmessage=array("`^%s`0`@ has married you! The excitement might not hit you until your next day though!",$session['user']['name']);
		}
		$t = array("`@Marriage!");
		require_once("lib/systemmail.php");
		systemmail($target,$t,$mailmessage);
		$session['user']['marriedto']=$target;
		$sql = "UPDATE " . db_prefix("accounts") . " SET marriedto=".$session['user']['acctid']." WHERE acctid='$target'";
		db_query($sql);
		$sql = "SELECT name,sex FROM ".db_prefix("accounts")." WHERE acctid='$target' AND locked=0";
		$res = db_query($sql);
		$row = db_fetch_assoc($res);
		addnews("`&%s`0`& and %s`0`& were joined today, in joyous matrimony.", $session['user']['name'],$row['name']);
		$first = ($session['user']['sex']?translate_inline("Wife"):translate_inline("Husband"));
		$second = ($row['sex']?translate_inline("Wife"):translate_inline("Husband"));
		if (get_module_setting("sacred")==1){
			output("`@You gather together with `^%s`@ for your wedding ceremony.  The weather clears and the sun shines brightly.",$row['name']);
			output("`n`nYou feel the power of the Vicar's words sink deep into you.  The words `#Faithfulness, Honesty, and Dedication`@ change for you.");
			output("A marriage is not to be taken lightly and you feel the truth of that deep in your heart. After a short but emotional ceremony, `^%s`@ looks up at both of you.`n`n",$vicar);
			output("`^%s `3says \"`&And I pronounce thee %s and %s.`3\"",$vicar,$first,$second);
		}else{
			output("`^%s `3says \"`&And I pronounce thee %s and %s.`3\"",$vicar,$first,$second);
			output("`n`^%s `3says \"`&Don't look so surprised! Nothing is sacred anymore...`3\"",$vicar);
		}
		$allprefs=unserialize(get_module_pref('allprefs'));
		$allprefs['counsel']=0;
		$allprefs['buyring']=0;
		set_module_pref('allprefs',serialize($allprefs));
		
		$allprefsm=unserialize(get_module_pref('allprefs','marriage',$session['user']['marriedto']));
		$allprefsm['counsel']=0;
		$allprefsm['buyring']=0;
		set_module_pref('allprefs',serialize($allprefsm),'marriage',$session['user']['marriedto']);

		invalidatedatacache("marriage-marriedonline");
		invalidatedatacache("marriage-marriedrealm");
		require_once("lib/datetime.php");
		$time=date("Y-m-d H:i:s");
		set_module_objpref("marriage",$session['user']['marriedto'],"marriagedate",$time);
		set_module_objpref("marriage",$session['user']['acctid'],"marriagedate",$time);
		apply_buff('marriage-start',
			array(
				"name"=>"`@Marriage",
				"rounds"=>100,
				"wearoff"=>"`&The elation wears off.",
				"defmod"=>1.83,
				"survivenewday"=>1,
				"roundmsg"=>"`@You are elated at your marriage",
				)
		);
		debuglog("proposal accepted from {$row['name']}");
		break;
	case "reject":
		$stuff = explode(',',get_module_pref('proposals'));
		$i = "";
		foreach ($stuff as $val) {
			if ($val!=""&&$val!=$target&&$val!=$session['user']['acctid']) {
				$i .= ",".$val;
			}
		}
		set_module_pref('proposals',$i);
		$stuff = explode(',',get_module_pref('proposals','marriage',$target));
		$i = "";
		foreach ($stuff as $val) {
			if ($val!=""&&$val!=$target&&$val!=$session['user']['acctid']) {
				$i .= ",".$val;
			}
		}
		set_module_pref('proposals',$i,'marriage',$target);
		$mailmessage=array("%s`0`@ has rejected you as unfit for marriage! You lose some charm.",$session['user']['name']);
		$t = array("`@Rejection!");
		require_once("lib/systemmail.php");
		systemmail($target,$t,$mailmessage);
		if (get_module_setting('counsel')==1) {
			$mailmessage=array(translate_inline("`@Hallo. I am Professor van Lipvig, and I haf been paid by.. benefactors, to counsel you due to your Mishap vith %s`@.`nPlease visit me in ze village."),$session['user']['name']);
			$t = array("`@Professor");
			require_once("lib/systemmail.php");
			systemmail($target,$t,$mailmessage);
			$allprefst=unserialize(get_module_pref('allprefs','marriage',$target));
			$allprefst['counsel']=1;
			set_module_pref('allprefs',serialize($allprefst),'marriage',$target);
		}
		$sql = "SELECT name,sex,charm FROM ".db_prefix("accounts")." WHERE acctid='$target' AND locked=0";
		$res = db_query($sql);
		$row = db_fetch_assoc($res);
		if (get_module_setting('flirtCharis')==1&&$row['charm']!=0) {
			$row['charm']--;
			$sql = "UPDATE " . db_prefix("accounts") . " SET charm=".$row['charm']." WHERE acctid='$target'";
			db_query($sql);
		}
		addnews("`&%s`0`& got a marriage proposal from %s`0`&, which %s`0`& rejected, seeing %s`0`& as '`@Unfit for Marriage.`&'",$session['user']['name'],$row['name'],$session['user']['name'],$row['name']);
		addnews("`&%s`0`& is currently moping around the inn.",$row['name']);
		$x = ($row['sex']?translate_inline("she's"):translate_inline("he's"));
		output("`@You say `3\"`#I don't want to get married to `^%s`#... It's a very sweet gesture, but %s just not my type.`3\"",$row['name'],$x);
		//Added cost<=0 line to prevent people from getting to keep/sell when the ring is free
		if (get_module_setting('cansell')==0 || get_module_setting("cost")<=0) {
			output("`n`n`^%s `3takes the wedding ring from `^%s`3 from you and throws it into the garderobe.",$vicar,$row['name']);
			output("`n`n`^%s `3grins, and says \"`&No second thoughts now.`3\"",$vicar);
		} else {
			output("`n`n`^%s `3takes the wedding ring from `^%s`3 from you and says `3\"`&Do you want to sell it back to me?`3\".",$vicar,$row['name']);
			addnav("Actions");
			addnav("Sell the Ring","runmodule.php?module=marriage&op=".$op."&op2=sellr&i=s&target=".$target);
			addnav("Keep the Ring as a Memento","runmodule.php?module=marriage&op=".$op."&op2=sellr&i=m&target=".$target);
			blocknav("runmodule.php?module=marriage&op=".$op."&op2=talk");
			blocknav("runmodule.php?module=marriage&op=".$op."&op2=propose");
			blocknav("runmodule.php?module=marriage&op=".$op."&op2=proposelist");
			blocknav("gardens.php");
			blocknav("village.php");
		}
		break;
	case "sellr":
		$sql = "SELECT name,sex FROM ".db_prefix("accounts")." WHERE acctid='$target' AND locked=0";
		$res = db_query($sql);
		$row = db_fetch_assoc($res);
		if (httpget('i')=='s'){
			$gold=round(get_module_setting('cost')*get_module_setting("cansell")/100);
			output("`^%s `3pockets the wedding ring from `^%s`3 and gives you `^%s gold`3.",$vicar,$row['name'],$gold);
			$session['user']['gold']+=$gold;
		} else {
			$allprefs=unserialize(get_module_pref('allprefs'));
			$allprefs['buyring']=1;
			set_module_pref('allprefs',serialize($allprefs));
			output("`^%s `3gives you the wedding ring from `^%s`3 back and ties it on a string around your neck.",$vicar,$row['name']);
		}
		break;
	case "divorce":
		require_once("./modules/marriage/divorce.php");
		output("You explain that things are not meant to be and that you need a divorce.");
		output("`^%s `& talks to you for a long time trying to make sure that this is what you really want.",$vicar);
		output("`n`nYou're pretty sure that this is what is best and tell `^%s`& the whole story.",$vicar);
		output("`n`n`^%s `&nods..",$vicar);
		output("`n`n`^%s `3starts a deep chant:",$vicar);
		output("`n`n`c`&Egairram siht dne ekactiurf taerg fo dog.",$vicar);
		output("`nAet rof snub yccohc ekil uoy dluow.`c");
		output("`n`^%s `3bows solemnly, and a `^`bLightning Bolt`b`3 arches from the sky, hitting the ground before your feet..",$vicar);
		output("`n`n`b`@The Marriage is annulled!`b`n");
		marriage_divorce();
		break;
	default:
		if ($op=='oldchurch') {
			output("`@Going through a small door in the side of the Church, you enter a vast side-wing..");
			output("`nAs you enter, %s `@enters, and walks over to a somewhat shabby bench, sits down, and stares at you.",$vicar);
			output("`nExamining the floor, under %s's`@ beady eye, you see bits of old confetti.",$vicar);
			output("`n%s `3says, \"`&This is our Marriage chamber. What do you wish me to do?`3\"",$vicar);
			if (get_module_setting('flirttype')) output("`n`^%s `3reminds you, \"`&You must have been proposed to already.. maybe the Loveshack can help you out.`3\"",$vicar);
		} else {
			if (get_module_setting("location")==0) output("`@In the center of town");
			else output("`@In a small corner of the garden");
			output("stands a small Chapel.");
			output("`n`nAs you enter, a Minister walks over and introduces %s as `^%s`@. %s then walks you over to a somewhat shabby bench, sits down, and invites you to sit.",$gendervicarpersonal,$vicar,$gendervicar);
			output("`n`n`^%s `@says, \"`&This is a Marriage chapel. What do you wish me to do?`@\"",$vicar);
			$dks=get_module_setting("dks");
			if (get_module_setting('flirttype')) output("`n`n\"`&Just a friendly reminder. I can only help you if someone has proposed to you.  I recommend you visit the `\$Love `@Shack`&,`@\" `^%s`@ says to you helpfully.",$vicar);
			elseif ($dks>$session['user']['dragonkills']) output("`n`n\"`&You will not be able to propose to anyone until after you've reached `^%s dragonkill%s`&,`@\" `^%s`@ says to you helpfully.",$dks,translate_inline($dks>1?"s":""),$vicar);
		}
		break;
	}
	addnav("Navigation");
	if (get_module_setting("location")==1) addnav("Return to the Gardens","gardens.php");
	else villagenav();
	addnav("Actions");
	addnav("Talk to Others","runmodule.php?module=marriage&op=".$op."&op2=talk");
	if (get_module_pref("supernomarry")==1 || get_module_pref("user_wed")==1){
		addnav("Proposals Blocked","runmodule.php?module=marriage&op=general&op2=nopropose");
		blocknav("runmodule.php?module=marriage&op=".$op."&op2=proposelist");
		blocknav("runmodule.php?module=marriage&op=".$op."&op2=propose");
	}
	if ($session['user']['marriedto']==0) {
		if (get_module_setting('flirttype')==0 && $session['user']['dragonkills']>=get_module_setting("dks")) addnav("Propose","runmodule.php?module=marriage&op=".$op."&op2=propose");
		addnav("View Proposals","runmodule.php?module=marriage&op=".$op."&op2=proposelist");
	} else {
		addnav("Get a Divorce!","runmodule.php?module=marriage&op=".$op."&op2=divorce");
	}
	page_footer();
?>