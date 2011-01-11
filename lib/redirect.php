<?php
// translator ready
// addnews ready
// mail ready
function redirect($location,$reason=false){
	global $session,$REQUEST_URI;
	// This function is deliberately not localized.  It is meant as error
	// handling.
	if (strpos($location,"badnav.php")===false) {
		//deliberately html in translations so admins can personalize this, also in once scheme
		$session['allowednavs']=array();
		addnav("",$location);
		addnav("",HTMLEntities($location, ENT_COMPAT, getsetting("charset", "ISO-8859-1")));
		$session['output']="<a href=\"".HTMLEntities($location, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\">".translate_inline("Click here.","badnav")."</a>";
		$session['output'].=translate_inline("<br><br><b>You've got a BadNav!</b>  <a href=\"http://enquirer.improbableisland.com/dokuwiki/doku.php?id=badnav\">Click here to find out what that is.</a>  If you see this message consistently, please add your tuppence'orth to <a href='http://enquirer.improbableisland.com/forum/viewtopic.php?showtopic=19239'>this forum thread</a>.<br /><br />If you cannot leave this page by clicking the first link above, notify the staff via <a href='petition.php'>petition</a> and tell them what you were doing just before this happened.  Also copy and paste everything that appears below this message.  Thanks!<br><br>BADNAV REPORT<br>Attempted redirect: \"".$location."\"<br>Sanitized attempted redirect: \"".HTMLEntities($location, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\"<br>Redirect reason: \"".$reason."\"","badnav");
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