<?php

function hunterslodge_customrace_getmoduleinfo(){
	$info = array(
		"name"=>"Hunter's Lodge: Custom Race",
		"version"=>"2010-08-19",
		"author"=>"Dan Hall",
		"category"=>"Lodge IItems",
		"download"=>"",
		"prefs"=>array(
			"Custom Race User Preferences,title",
			"customrace"=>"The player's Custom Race,text",
		),
	);
	return $info;
}

function hunterslodge_customrace_install(){
	module_addhook_priority("postcomment",100);
	return true;
}

function hunterslodge_customrace_uninstall(){
	return true;
}

function hunterslodge_customrace_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "postcomment":
			$race = get_module_pref("customrace");
			if ($race || $race!=""){
				if ($session['user']['sex'] == 0){
					$gendertitle = "Male";
				} else {
					$gendertitle = "Female";
				}
				$args['info']['icons']['race']=array(
					'icon' => "images/icons/races/customrace.png",
					'mouseover' => $gendertitle." ".$race,
				);
			}
			break;
		}
	return $args;
}

function hunterslodge_customrace_run(){
	require_once("lib/sanitize.php");
	require_once("lib/names.php");
	global $session;
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

	page_header("Choose your Custom Race");
	
	switch($op){
		case "change":
			output("Want to change your Custom Race?  No problem.  Enter your desired race in the box below.  You've got 25 characters to play around with.`n(leave this blank to disable custom race naming and return to default, game-supplied race names)`n`n");
			rawoutput("<form action='runmodule.php?module=hunterslodge_customrace&op=confirm&context=$context&free=".$free."' method='POST'>");
			$race = get_module_pref("customrace");
			rawoutput("<input id='input' name='newrace' width='25' maxlength='25' value='".htmlentities($race, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."'>");
			rawoutput("<input type='submit' class='button' value='Preview'>");
			rawoutput("</form>");
			addnav("", "runmodule.php?module=hunterslodge_customrace&op=confirm&context=$context&free=".$free);
			addnav("Cancel");
			addnav("Don't set a custom race, just go back to where I came from",$backlink);
		break;
		case "confirm":
			$newrace = httppost("newrace");
			$sub = httpget("sub");
			$newrace = str_replace("`","",$newrace);
			$newrace = comment_sanitize($newrace);
			$newrace = substr($newrace,0,25);
			if ($newrace){
				output("Your new custom race is:`n%s`nWould you like to set your new Race now?`n`n",$newrace);
			} else {
				output("You've chosen to go back to the default, game-supplied races.  Are you sure that's what you want?`n`n");
			}
			addnav("Confirm");
			addnav("Set custom race","runmodule.php?module=hunterslodge_customrace&op=set&free=$free&context=$context&newrace=".rawurlencode($newrace));
			addnav("Cancel");
			addnav("Don't set a custom race, just go back to where I came from",$backlink);
		break;
		case "set":
			$newrace = rawurldecode(httpget("newrace"));
			output("Your custom race has been set to %s!`n`n",$newrace);
			set_module_pref("customrace",$newrace);
			if (!$free){
				$id = has_item("hunterslodge_customrace");
				delete_item($id);
			}
			addnav("Return");
			addnav("Back to where I came from",$backlink);
		break;
	}
	page_footer();


}
?>