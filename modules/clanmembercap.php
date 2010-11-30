<?php
function clanmembercap_getmoduleinfo(){
    $info = array(
		"name"=>"Clan Member Cap",
<<<<<<< HEAD
		"version"=>"20070207",
		"author"=>"<a href='http://www.joshuadhall.com' target=_new>Sixf00t4</a>",
		"category"=>"Clan",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1204",
		"vertxtloc"=>"http://www.legendofsix.com/",
		"description"=>"Limits the number of members a clan can have",
        "settings"=>array(
            "clan member cap - Settings,title",
			"lim"=>"How many members are allowed per clan?,int|30",
			"apps"=>"Do applicants count as members?,bool|0",
        ),		
=======
		"version"=>"20101018",
		"author"=>"Dan Hall, based on original Clan Member Cap by Sixf00t4",
		"category"=>"Clan",
		"download"=>"",
		"description"=>"Limits the number of members a clan can have",
        "settings"=>array(
            "clan member cap - Settings,title",
			"lim"=>"How many members are allowed per clan to start?,int|20",
			"apps"=>"Do applicants count as members?,bool|0",
			"cost"=>"Cost in gems to add another member,int|200",
			"costincrease"=>"Exponential cost increase factor per additional member,float|1.1",
        ),
		 "prefs-clans"=>array(
			"Clan Member Cap preferences,title",
			"limit"=>"Member Limit,int|0",
			"costfornext"=>"Cost in gems for next new member,int|0",
			"bank"=>"Gems currently in the Member Cap bank,int|0",
		),
>>>>>>> 8b5d92281350005db7709911b00777e80705dd6e
    );
    return $info;
}

function clanmembercap_install(){
	module_addhook_priority("header-clan",100);
<<<<<<< HEAD
=======
	module_addhook("footer-clan");
>>>>>>> 8b5d92281350005db7709911b00777e80705dd6e
    return true;
}

function clanmembercap_uninstall(){
    return true;
}

function clanmembercap_dohook($hookname,$args){
    global $session;
    $to=httpget('to');
    $op=httpget('op');
<<<<<<< HEAD
    if($op=="apply" && $to>0){
        $apps="";
        if(get_module_setting("apps")==0) $apps="and clanrank>1";
        $sql="select clanid from ".db_prefix("accounts")." where clanid=$to $apps";
        $res=db_query($sql);
        $count=db_num_rows($res);
        if($count>=get_module_setting("lim")){
            redirect("runmodule.php?module=clanmembercap");
        }
    }
=======
	
	switch($hookname){
		case "header-clan":
			if($op=="apply" && $to>0){
				$apps="";
				if(get_module_setting("apps")==0) $apps="and clanrank>1";
				$sql="select clanid from ".db_prefix("accounts")." where clanid=$to $apps";
				$res=db_query($sql);
				$count=db_num_rows($res);
				if($count>=get_module_objpref("clans",$to,"limit","clanmembercap")){
					if (!get_module_objpref("clans",$to,"limit","clanmembercap")){
						$startlimit = get_module_setting("lim");
						$costfornext = get_module_setting("cost");
						set_module_objpref("clans",$to,"limit",$startlimit,"clanmembercap");
						set_module_objpref("costfornext",$to,"limit",$costfornext,"clanmembercap");
					}
					redirect("runmodule.php?module=clanmembercap&clanid=$to&op=bounce");
				}
			}
		break;
		case "footer-clan":
			if($session['user']['clanid']!=0 and httpget("op")==""){
				if($session['user']['clanrank']>0){
				   addnav("Increase Member Cap","runmodule.php?module=clanmembercap&op=start");
				}
			}
		break;
	}
	
>>>>>>> 8b5d92281350005db7709911b00777e80705dd6e
    return $args;
}

function clanmembercap_run() {
    global $session;
<<<<<<< HEAD
	page_header("Clan Full");
    output("This Clan has reached full capacity at %s members.",get_module_setting("lim"));
    addnav("Back to Clan hall","clan.php");
	villagenav();
=======
	
	$op = httpget('op');
	$clanid = $session['user']['clanid'];
	
	switch ($op){
		case "bounce":
			page_header("Clan Full");
			$clanid = httpget("clanid");
			output("This Clan has reached full capacity at %s members.",get_module_objpref("clans",$clanid,"limit","clanmembercap"));
			addnav("Back to Clan hall","clan.php");
			villagenav();
		break;
		case "start":
			page_header("Increase Member Cap");
			if (!get_module_objpref("clans",$clanid,"limit","clanmembercap")){
				$startlimit = get_module_setting("lim","clanmembercap");
				$costfornext = get_module_setting("cost","clanmembercap");
				set_module_objpref("clans",$clanid,"limit",$startlimit,"clanmembercap");
				set_module_objpref("clans",$clanid,"costfornext",$costfornext,"clanmembercap");
			}
			$currentlimit = get_module_objpref("clans",$clanid,"limit","clanmembercap");
			$costfornext = get_module_objpref("clans",$clanid,"costfornext","clanmembercap");
			$bank = get_module_objpref("clans",$clanid,"bank","clanmembercap");
			output("Using a preposterous amount of cigarettes, you can increase the number of members allowed in your Clan.  You can currently have up to %s members - to add another member slot, it'll cost you %s cigarettes.`n`nYou currently have %s cigarettes in your Member Cap bank.  Remember that this is different to the Clan Buffs bank, and you can't transfer cigarettes from one to the other.  Your member cap will be automatically increased once the bank has enough cigarettes in it.`n`nHow many cigarettes will you deposit?`n`n",$currentlimit,$costfornext,$bank);
			$dep = translate_inline("Deposit");
			rawoutput("<form action='runmodule.php?module=clanmembercap&op=deposit' method='POST'>");
			rawoutput("<input id='input' name='amount' width=5 > <input type='submit' class='button' value='$dep'>");
			output("`bEnter 0 or nothing to deposit all of your cigarettes.`b`n`n");
			rawoutput("</form>");
			rawoutput("<script language='javascript'>document.getElementById('input').focus();</script>",true);
			addnav("","runmodule.php?module=clanmembercap&op=deposit");
			addnav("Back to Clan hall","clan.php");
		break;
		case "deposit":
			page_header("Depositing Cigarettes");
			$amount = abs((int)httppost('amount'));
			if ($amount==0){
				$amount=$session['user']['gems'];
			}
			if ($amount > $session['user']['gems']){
				output("`4That's more cigarettes than you have.`0`n`n");
				addnav("Back to Clan hall","clan.php");
				break;
			}
			debuglog("deposited " . $amount . " cigarettes in the clan's member cap bank");
			$session['user']['gems'] -= $amount;
			increment_module_objpref("clans",$clanid,"bank",$amount,"clanmembercap");
			
			$currentlimit = get_module_objpref("clans",$clanid,"limit","clanmembercap");
			$costfornext = get_module_objpref("clans",$clanid,"costfornext","clanmembercap");
			$bank = get_module_objpref("clans",$clanid,"bank","clanmembercap");
			
			if ($bank > $costfornext){
				//level up the member cap
				$currentlimit += 1;
				set_module_objpref("clans",$clanid,"limit",$currentlimit,"clanmembercap");
				
				//take the cigs out of the bank
				$newbank = $bank - $costfornext;
				set_module_objpref("clans",$clanid,"bank",$newbank,"clanmembercap");
				
				//figure out the cost of the next member cap increase
				$inc = get_module_setting("costincrease","clanmembercap");
				$costfornext = round($costfornext * $inc);
				set_module_objpref("clans",$clanid,"costfornext",$costfornext,"clanmembercap");
				
				output("`bYour clan's Member Cap has been increased!`b`n`nThere are now %s cigarettes in the bank.`n`n",$newbank);
			} else {
				output("There are now %s cigarettes in this clan's Member Cap bank, out of %s needed for the next level-up.`n`n",$bank,$costfornext);
			}
			
			addnav("Back to Clan hall","clan.php");
		break;
	}
>>>>>>> 8b5d92281350005db7709911b00777e80705dd6e
	page_footer();
}
?>
