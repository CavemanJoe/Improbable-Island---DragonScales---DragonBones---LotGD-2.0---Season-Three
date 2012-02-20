<?php

/*
//GIT NOTE: I don't know where this text has come from, but I'm leaving it at this anyway. If nessasary, I'll revert back to an earlier commit (commit 34935c523b)
*/

// translator ready
// addnews ready
// mail ready
function redirect($location,$reason=false){
	global $session,$REQUEST_URI;
	// This function is deliberately not localized.  It is meant as error
	// handling.
	if (strpos($location,"badnav.php")===false) {
		$session['encountered_badnav']+=1;

		//deliberately html in translations so admins can personalize this, also in once scheme
		$session['allowednavs']=array();
		addnav("",$location);
		$session['output']=
			"<a href=\"".HTMLEntities($location, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\">".translate_inline("Click here.","badnav")."</a>";
		$session['output'].=translate_inline("<br><br>If you cannot leave this page, notify the staff via <a href='petition.php'>petition</a> and tell them where this happened and what you did. Thanks.","badnav");

		addnav("",HTMLEntities($location, ENT_COMPAT, getsetting("charset", "ISO-8859-1")));

		$session['output']=
			"<a href=\"".HTMLEntities($location, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\">".translate_inline("Click here.","badnav")."</a>";
		$session['output'].=translate_inline("<br><br><b>You've got a BadNav!</b>  This is what happens when the game can't decide what to do with you, and it can be caused by several things:</b><br><br>* Your browser's Back, Forward and Refresh buttons don't work here.  If they did, you could kill a monster, hit Refresh, and get your rewards again.  Trying to use Back, Forward or Refresh can in some circumstances lead to BadNavs.<br>* Trying to play the same game over multiple browser tabs is a pretty certain way to get a BadNav.<br>* Typing a link address into your browser's address bar, rather than just using the hotkeys or links - yeah, that'll probably BadNav you.<br>* Using so-called 'web accelerator' plugins/programs/toolbars.  These programs visit every link inside the page you've just loaded, and cache the results to your hard drive.  They don't really speed anything up more than a little; they're far more likely to leech data transfer from web servers, and cause BadNavs in games like this.  If you're using one of these programs, uninstall it and then run a virus scan (did I mention that they're usually bundled with malware?).<br>* Double-clicking on links or submit buttons.  You don't need to double-click anything on the Internet!<br>* Clicking a link, then changing your mind and clicking another link before the page has finished loading (also known as the \"Oh hell I didn't mean to eat that!\" error)<br>* Timing out during a special event can sometimes lead to a BadNav when you log back into the game.<br>* Being unlucky.<br><br>If you cannot leave this page by clicking the link above, notify the staff via <a href='petition.php'>petition</a> and tell them what you were doing just before this happened.  Also copy and paste everything that appears below this message.  Thanks!<br><br>BADNAV REPORT<br>Attempted redirect: \"".$location."\"<br>Sanitized attempted redirect: \"".HTMLEntities($location, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\"<br>Redirect reason: \"".$reason."\"","badnav");
	} else {
		unset($session['encountered_badnav']);

	}
	restore_buff_fields();
	$session['debug'].="Redirected to $location from $REQUEST_URI.  $reason<br>";
	saveuser();
	@header("Location: $location");
	//echo "<html><head><meta http-equiv='refresh' content='0;url=$location'></head></html>";
	//echo "<a href='$location'>$location</a><br><br>";
	//echo $location;
	//echo $session['debug'];
	exit();
}
?>
