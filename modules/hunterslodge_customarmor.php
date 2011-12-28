<?php

function hunterslodge_customarmour_getmoduleinfo(){
	$info = array(
		"name"=>"Hunter's Lodge: Custom Armour",
		"version"=>"2010-08-19",
		"author"=>"Dan Hall",
		"category"=>"Lodge IItems",
		"download"=>"",
		"prefs"=>array(
			"Custom Armour User Preferences,title",
			"customarmour"=>"The player's Custom Armour,text",
		),
	);
	return $info;
}

//iitems required:
//hunterslodge_customarmour
//hunterslodge_customarmour_permanent
//Don't use the "destroyafteruse" parameter for these iitems

function hunterslodge_customarmour_install(){
	module_addhook("charstats");
	module_addhook("iitems-use-item");
	return true;
}

function hunterslodge_customarmour_uninstall(){
	return true;
}

function hunterslodge_customarmour_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "charstats":
			$armour = get_module_pref("customarmour");
			if ($armour!=""){
				$session['user']['armor']=$armour;
			}
			break;
		case "iitems-use-item":
			if ($args['player']['itemid'] == "hunterslodge_customarmour"){
				$args['player']['inventorylocation']="lodgebag";
				redirect("runmodule.php?module=hunterslodge_customarmour&op=change");
			} else if ($args['player']['itemid'] == "hunterslodge_customarmour_permanent"){
				$args['player']['inventorylocation']="lodgebag";
				redirect("runmodule.php?module=hunterslodge_customarmour&op=change&free=1");
			}
			break;
		}
	return $args;
}

function hunterslodge_customarmour_run(){
	require_once("lib/sanitize.php");
	require_once("lib/names.php");
	global $session;
	$op = httpget("op");
	$free = httpget("free");

	page_header("Choose your Custom armour");
	
	switch($op){
		case "change":
			output("Want to change your Custom Armour?  No problem.  Enter your desired armour in the box below.  You've got 25 characters to play around with.`n(leave this blank to disable custom armour naming and return to default, game-supplied armour names)`n`n");
			rawoutput("<form action='runmodule.php?module=hunterslodge_customarmour&op=confirm&free=".$free."' method='POST'>");
			$armour = get_module_pref("customarmour");
			rawoutput("<input id='input' name='newarmour' width='25' maxlength='25' value='".htmlentities($armour, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."'>");
			rawoutput("<input type='submit' class='button' value='Preview'>");
			rawoutput("</form>");
			addnav("", "runmodule.php?module=hunterslodge_customarmour&op=confirm&free=".$free);
			addnav("Cancel");
			addnav("Don't set custom armour, just go back to the Lodge","runmodule.php?module=iitems_hunterslodge&op=start");
		break;
		case "confirm":
			$newarmour = httppost("newarmour");
			$sub = httpget("sub");
			$newarmour = str_replace("`","",$newarmour);
			$newarmour = comment_sanitize($newarmour);
			$newarmour = substr($newarmour,0,25);
			if ($newarmour){
				output("Your new custom armour is:`n%s`nWould you like to set your new armour now?`n`n",$newarmour);
			} else {
				output("You've chosen to go back to the default, game-supplied armours.  Are you sure that's what you want?`n`n");
			}
			addnav("Confirm");
			addnav("Set custom armour","runmodule.php?module=hunterslodge_customarmour&op=set&free=$free&newarmour=".rawurlencode($newarmour));
			addnav("Cancel");
			addnav("Don't set custom armour, just go back to the Lodge","runmodule.php?module=iitems_hunterslodge&op=start");
		break;
		case "set":
			$newarmour = rawurldecode(httpget("newarmour"));
			output("Your custom armour has been set to %s!`n`n",$newarmour);
			set_module_pref("customarmour",$newarmour);
			if (!$free){
				require_once "modules/iitems/lib/lib.php";
				iitems_discard_item("hunterslodge_customarmour","lodgebag");
			}
			addnav("Return");
			addnav("Return to the Lodge","runmodule.php?module=iitems_hunterslodge&op=start");
		break;
	}
	page_footer();


}
?>