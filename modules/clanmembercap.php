<?php
function clanmembercap_getmoduleinfo(){
    $info = array(
		"name"=>"Clan Member Cap",
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
    );
    return $info;
}

function clanmembercap_install(){
	module_addhook_priority("header-clan",100);
    return true;
}

function clanmembercap_uninstall(){
    return true;
}

function clanmembercap_dohook($hookname,$args){
    global $session;
    $to=httpget('to');
    $op=httpget('op');
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
    return $args;
}

function clanmembercap_run() {
    global $session;
	page_header("Clan Full");
    output("This Clan has reached full capacity at %s members.",get_module_setting("lim"));
    addnav("Back to Clan hall","clan.php");
	villagenav();
	page_footer();
}
?>
