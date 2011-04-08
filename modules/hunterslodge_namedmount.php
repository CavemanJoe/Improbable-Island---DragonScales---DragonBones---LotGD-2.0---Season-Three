<?php

function hunterslodge_namedmount_getmoduleinfo(){
	$info = array(
		"name"=>"Hunter's Lodge: Named Mount",
		"version"=>"2010-08-25",
		"author"=>"Dan Hall",
		"category"=>"Lodge IItems",
		"download"=>"",
		"prefs"=>array(
			"Named Mount User Preferences,title",
			"mountname"=>"The player's Mount Name,text",
		),
	);
	return $info;
}

//iitems required:
//hunterslodge_namedmount
//hunterslodge_namedmount_permanent
//Don't use the "destroyafteruse" parameter for these iitems

function hunterslodge_namedmount_install(){
	//module_addhook("iitems-use-item");
	//module_addhook("everyhit-loggedin");
	module_addhook("bio-mount");
	module_addhook("stable-mount");
	return true;
}

function hunterslodge_namedmount_uninstall(){
	return true;
}

function hunterslodge_namedmount_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "stable-mount":
			global $playermount;
			$name = get_module_pref("mountname");
			if (isset($playermount['mountname'])) {
				$playermount['basename']=$playermount['mountname'];
				if ($name > "") {
					$playermount['mountname']=$name." `0the ".$playermount['basename'] . "`0";
					$playermount['newname']="$name`0";
				}
			}
			break;
		case "bio-mount":
			$name = get_module_pref("mountname","hunterslodge_namedmount",$args['acctid']);
			if ($name!=""){
				//debug($name);
				if (isset($args['mountname'])) {
					$args['basename']=$args['mountname'];
					if ($name > "") {
						$args['mountname']=$name." `0the ".$args['basename'] . "`0";
						$args['newname']="$name`0";
					}
				}
			}
			break;
		}
	return $args;
}

function hunterslodge_namedmount_run(){
	require_once("lib/sanitize.php");
	require_once("lib/names.php");
	global $session;
	global $playermount;
	$op = httpget("op");
	$free = httpget("free");
	$context = httpget("context");
	switch ($context){
		case "village":
			$backlink = "village.php";
		break;
		case "forest":
			$backlink = "forest.php";
		break;
		case "worldmap":
			$backlink = "runmodule.php?module=worldmapen&op=continue";
		break;
		case "lodge":
			$backlink = "runmodule.php?module=iitems_hunterslodge&op=start";
		break;
	}

	page_header("Name your Mount");
	
	switch($op){
		case "change":
			output("Want to change your Mount's name?  No problem.  Enter your desired name in the box below.  You've got 25 characters to play around with.`n(leave this blank to disable mount naming)`n`n");
			rawoutput("<form action='runmodule.php?module=hunterslodge_namedmount&op=confirm&context=$context&free=".$free."' method='POST'>");
			rawoutput("<input id='input' name='newname' width='25' maxlength='25' value='".htmlentities($race, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."'>");
			rawoutput("<input type='submit' class='button' value='Preview'>");
			rawoutput("</form>");
			addnav("", "runmodule.php?module=hunterslodge_namedmount&op=confirm&context=$context&free=".$free);
			addnav("Cancel");
			addnav("Don't set a mount name, just go back to where I came from",$backlink);
		break;
		case "confirm":
			$newname = httppost("newname");
			$sub = httpget("sub");
			$newname = comment_sanitize($newname);
			$newname = substr($newname,0,25);
			if ($newname){
				output("Your Mount's name is now:`n%s`0 the %s`nWould you like to set your mount's name now?`n`n",$newname,$playermount['mountname']);
			} else {
				output("You've chosen to go back to having an unnamed Mount.  Are you sure that's what you want?`n`n");
			}
			addnav("Confirm");
			addnav("Set mount name","runmodule.php?module=hunterslodge_namedmount&op=set&free=$free&context=$context&newname=".rawurlencode($newname));
			addnav("Cancel");
			addnav("Don't set a mount name, just go back to where I came from",$backlink);
		break;
		case "set":
			$newname = rawurldecode(httpget("newname"));
			output("You now ride %s`0 the %s!`n`n",$newname,$playermount['mountname']);
			set_module_pref("mountname",$newname);
			if (!$free){
				$id = has_item("hunterslodge_namedmount");
				delete_item($id);
			}
			addnav("Return");
			addnav("Back to where I came from",$backlink);
		break;
	}
	page_footer();


}
?>