<?php

function hunterslodge_customweapon_getmoduleinfo(){
	$info = array(
		"name"=>"Hunter's Lodge: Custom Weapon",
		"version"=>"2010-09-13",
		"author"=>"Dan Hall",
		"category"=>"Lodge Items",
		"download"=>"",
		"prefs"=>array(
			"Custom Weapon User Preferences,title",
			"customweapon"=>"The player's Custom Weapon,text",
		),
	);
	return $info;
}

//iitems required:
//hunterslodge_customweapon
//hunterslodge_customweapon_permanent
//Don't use the "destroyafteruse" parameter for these iitems

function hunterslodge_customweapon_install(){
	//module_addhook("charstats");
	module_addhook("newday");
	module_addhook("mysticalshop-buy");
	//module_addhook("iitems-use-item");
	return true;
}

function hunterslodge_customweapon_uninstall(){
	return true;
}

function hunterslodge_customweapon_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "newday":
		case "mysticalshop-buy":
			$weapon = get_module_pref("customweapon");
			if ($weapon!=""){
				$session['user']['weapon']=$weapon;
			}
			break;
		// case "iitems-use-item":
			// if ($args['player']['itemid'] == "hunterslodge_customweapon"){
				// $args['player']['inventorylocation']="lodgebag";
				// redirect("runmodule.php?module=hunterslodge_customweapon&op=change");
			// } else if ($args['player']['itemid'] == "hunterslodge_customweapon_permanent"){
				// $args['player']['inventorylocation']="lodgebag";
				// redirect("runmodule.php?module=hunterslodge_customweapon&op=change&free=1");
			// }
			// break;
		}
	return $args;
}

function hunterslodge_customweapon_run(){
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
	
	page_header("Choose your Custom Weapon");
	
	switch($op){
		case "change":
			output("Want to change your Custom Weapon?  No problem.  Enter your desired weapon in the box below.  You've got 25 characters to play around with.`n(leave this blank to disable custom weapon naming and return to default, game-supplied weapon names)`n`n");
			rawoutput("<form action='runmodule.php?module=hunterslodge_customweapon&op=confirm&free=".$free."&context=".$context."' method='POST'>");
			$weapon = get_module_pref("customweapon");
			rawoutput("<input id='input' name='newweapon' width='25' maxlength='25' value='".htmlentities($weapon, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."'>");
			rawoutput("<input type='submit' class='button' value='Preview'>");
			rawoutput("</form>");
			addnav("", "runmodule.php?module=hunterslodge_customweapon&op=confirm&free=".$free."&context=".$context);
			addnav("Cancel");
			addnav("Don't set a custom weapon, just go back to where I came from",$backlink);
		break;
		case "confirm":
			$newweapon = httppost("newweapon");
			$sub = httpget("sub");
			$newweapon = str_replace("`","",$newweapon);
			$newweapon = comment_sanitize($newweapon);
			$newweapon = substr($newweapon,0,25);
			if ($newweapon){
				output("Your new custom weapon is:`n%s`nWould you like to set your new weapon now?`n`n",$newweapon);
			} else {
				output("You've chosen to go back to the default, game-supplied weapons.  Are you sure that's what you want?`n`n");
			}
			addnav("Confirm");
			addnav("Set custom weapon","runmodule.php?module=hunterslodge_customweapon&op=set&free=$free&context=$context&newweapon=".rawurlencode($newweapon));
			addnav("Cancel");
			addnav("Don't set a custom weapon, just go back to where I came from",$backlink);
		break;
		case "set":
			$newweapon = rawurldecode(httpget("newweapon"));
			if ($newweapon==""){
				output("Your custom weapon name has been removed.  The next time you change your weapon, you'll return to game-supplied weapon names.`n`n");
			} else {
				output("Your custom weapon has been set to %s!`n`n",$newweapon);
				$session['user']['weapon']=$newweapon;
			}
			set_module_pref("customweapon",$newweapon);
			if (!$free){
				$id = has_item("hunterslodge_customweapon");
				delete_item($id);
			}
			addnav("Return");
			addnav("Go back to where I came from",$backlink);
		break;
	}
	page_footer();


}
?>