<?php

function hunterslodge_customtitle_getmoduleinfo(){
	$info = array(
		"name"=>"Hunter's Lodge: Custom Title",
		"version"=>"2010-08-24",
		"author"=>"Dan Hall",
		"category"=>"Lodge IItems",
		"download"=>"",
	);
	return $info;
}

//iitems required:
//hunterslodge_customtitle
//hunterslodge_customtitle_permanent
//Don't use the "destroyafteruse" parameter for these iitems

function hunterslodge_customtitle_install(){
	//module_addhook("iitems-use-item");
	return true;
}

function hunterslodge_customtitle_uninstall(){
	return true;
}

function hunterslodge_customtitle_dohook($hookname,$args){
	// global $session;
	// switch($hookname){
		// case "iitems-use-item":
			// if ($args['player']['itemid'] == "hunterslodge_customtitle"){
				// $args['player']['inventorylocation']="lodgebag";
				// redirect("runmodule.php?module=hunterslodge_customtitle&op=change");
			// } else if ($args['player']['itemid'] == "hunterslodge_customtitle_permanent"){
				// $args['player']['inventorylocation']="lodgebag";
				// redirect("runmodule.php?module=hunterslodge_customtitle&op=change&free=1");
			// }
			// break;
		// }
	return $args;
}

function hunterslodge_customtitle_run(){
	require_once("lib/sanitize.php");
	require_once("lib/names.php");
	global $session;
	$op = httpget("op");
	$free = httpget("free");

	page_header("Choose your Custom Title");
	
	switch($op){
		case "change":
			output("Ready to change your Title?  No problem.  Enter your desired Title in the box below.  You've got 25 characters to play with, including colour codes.`n`n");
			titlechange_form();
			addnav("Cancel");
			addnav("Don't change colours, just go back to the Lodge","runmodule.php?module=iitems_hunterslodge&op=start");
		break;
		case "confirm":
			$ntitle = rawurldecode(httppost('newname'));
			$ntitle=newline_sanitize($ntitle);

			if ($ntitle=="") {
				$ntitle = "`0";
			}

			$ntitle = preg_replace("/[`][cHw]/", "", $ntitle);
			$ntitle = sanitize_html($ntitle);

			$nname = get_player_basename();
			output("`0Your new title will look like this: %s`0`n", $ntitle);
			output("`0Your entire name will look like: %s %s`0`n`n", $ntitle, $nname);
			output("Do you want to set the new title now?`n`n");

			output("`0Try a different title below, if you like.`n`n");
			titlechange_form();
			
			addnav("Confirm");
			addnav("Set the new Title","runmodule.php?module=hunterslodge_customtitle&op=set&free=$free&newname=".rawurlencode($ntitle));
			addnav("Cancel");
			addnav("Don't change your Title, just go back to the Lodge","runmodule.php?module=iitems_hunterslodge&op=start");
		break;
		case "set":
			$ntitle=rawurldecode(httpget('newname'));
			$fromname = $session['user']['name'];
			$newname = change_player_ctitle($ntitle);
			$session['user']['ctitle'] = $ntitle;
			$session['user']['name'] = $newname;
			
			output("You are now known as %s!`0`n`n",$session['user']['name']);
			if (!$free){
				$id = has_item("hunterslodge_customtitle");
				delete_item($id);
			}
			addnav("Return");
			addnav("Return to the Lodge","runmodule.php?module=iitems_hunterslodge&op=start");
		break;
	}
	page_footer();
}

function titlechange_form() {
	$otitle = get_player_title();
	if ($otitle=="`0") $otitle="";
	output("Your title is currently: ");
	rawoutput($otitle);
	output_notl("`0`n");
	output("`0`nWhich renders as: %s`n`n", $otitle);
	rawoutput("<form action='runmodule.php?module=hunterslodge_customtitle&op=confirm' method='POST'>");
	rawoutput("<input id='input' name='newname' width='25' maxlength='25' value='".htmlentities($otitle, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."'>");
	rawoutput("<input type='submit' class='button' value='Preview'>");
	rawoutput("</form>");
	addnav("", "runmodule.php?module=hunterslodge_customtitle&op=confirm");
}
?>