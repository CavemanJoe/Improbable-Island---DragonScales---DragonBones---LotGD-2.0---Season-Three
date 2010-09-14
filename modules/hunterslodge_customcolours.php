<?php

function hunterslodge_customcolours_getmoduleinfo(){
	$info = array(
		"name"=>"Hunter's Lodge: Custom Colours",
		"version"=>"2010-08-19",
		"author"=>"Dan Hall",
		"category"=>"Lodge IItems",
		"download"=>"",
	);
	return $info;
}

//iitems required:
//hunterslodge_customcolours
//hunterslodge_customcolours_permanent
//Don't use the "destroyafteruse" parameter for these iitems

function hunterslodge_customcolours_install(){
	return true;
}

function hunterslodge_customcolours_uninstall(){
	return true;
}

function hunterslodge_customcolours_dohook($hookname,$args){
	global $session;
	return $args;
}

function hunterslodge_customcolours_run(){
	require_once("lib/sanitize.php");
	require_once("lib/names.php");
	global $session;
	$op = httpget("op");
	$free = httpget("free");

	page_header("Choose your Custom Colours");
	
	switch($op){
		case "change":
			output("Want to change your name colours?  No problem.  Enter your desired name, using colour codes, in the box below.  You've got 30 characters to play around with, including colour codes.`n`n");
			namecolour_form();
			addnav("Cancel");
			addnav("Don't change colours, just go back to the Lodge","runmodule.php?module=iitems_hunterslodge&op=start");
		break;
		case "confirm":
			$newname = httppost("newname");
			$newname = str_replace("`0", "", httppost("newname"));
			$newname = preg_replace("/[`][cHw]/", "", $newname);
			$regname = get_player_basename();
			$comp1 = strtolower(sanitize($regname));
			$comp2 = strtolower(sanitize($newname));
			$err = 0;
			if ($comp1 != $comp2) {
				if (!$err) output("`4`bInvalid name`b`0`n");
				$err = 1;
				output("Your new name must contain only the same characters as your current name; you can add or remove colors, and you can change the capitalization, but you may not add or remove anything else. You chose %s.`0`n`n", $newname);
			}
			if (strlen($newname) > 30) {
				if (!$err) output("`4`bInvalid name`b`0`n");
				$err = 1;
				output("Your new name is too long.  Including the color markups, you are not allowed to exceed 30 characters in length.`n`n");
			}

			if (!$err) {
				output("`0Your name will look this this: %s`n`n`0Do you want to set your new name colours now?`n`n", $newname);
				addnav("Confirm");
				addnav("Set the new name colours","runmodule.php?module=hunterslodge_customcolours&op=set&free=$free&newname=".rawurlencode($newname));
				addnav("Cancel");
				addnav("Don't change colours, just go back to the Lodge","runmodule.php?module=iitems_hunterslodge&op=start");
			} else {
				addnav("Cancel");
				addnav("Don't change colours, just go back to the Lodge","runmodule.php?module=iitems_hunterslodge&op=start");
			}
			output("`0Change your name again below, if you like.`n`n");
			namecolour_form();
		break;
		case "set":
			$fromname = $session['user']['name'];
			$newname = change_player_name(rawurldecode(httpget('newname')));
			$session['user']['name'] = $newname;
			
			output("You are now known as %s!`0`n`n",$session['user']['name']);
			if (!$free){
				$id = has_item("hunterslodge_customcolours");
				delete_item($id);
			}
			addnav("Return");
			addnav("Return to the Lodge","runmodule.php?module=iitems_hunterslodge&op=start");
		break;
	}
	page_footer();
}

function namecolour_form() {
	$regname = get_player_basename();
	rawoutput("Your current name is: ".$regname);
	output("`0`nWhich renders as: %s`0`n`n",$regname);
	if (httppost("newname")){
		$val = httppost("newname");
	} else {
		$val = htmlentities($regname, ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
	}
	rawoutput("<form action='runmodule.php?module=hunterslodge_customcolours&op=confirm&free=".$free."' method='POST'>");
	rawoutput("<input id='input' name='newname' width='30' maxlength='30' value='".$val."'>");
	rawoutput("<input type='submit' class='button' value='Preview'>");
	rawoutput("</form>");
	addnav("", "runmodule.php?module=hunterslodge_customcolours&op=confirm&free=".$free);
}
?>