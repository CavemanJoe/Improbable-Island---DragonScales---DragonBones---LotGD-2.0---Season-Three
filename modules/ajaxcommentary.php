<?php
function ajaxcommentary_getmoduleinfo() {
	$info = array(
		"name"		=>	"AJAX Commentary",
		"version"	=>	"1.21",
		"author"	=>	"<a href='http://www.capslog.info/lotgd'>`!Nicholas Moline`0</a>, Petko Bossakov",
		"category"	=>	"Commentary",
		"download"=>"http://bossakov.eu/uploads/logd/ajaxcommentary.zip",
		"settings"	=> array(
			"Live Chat,title",
			"timeout" => "Refresh interval (seconds),int|15"
		),
		"prefs"		=>	array(
			"Live Chat,title",
			"user_useajax"	=>	"Would you like to use the Live Chat System (if you use a screen reader select No)?,bool|1",
			"location" => "Last commentary URL,text|"
		),
		
	);
	return $info;
}

function ajaxcommentary_install() {
	module_addhook("everyheader");
	module_addhook("viewcommentaryheader");
	module_addhook("viewcommentaryfooter");
	module_addhook("commentarytalkline");
	return true;
}

function ajaxcommentary_uninstall() {
	return true;
}

function ajaxcommentary_dohook($hookname,$args) {
	global $session;
	global $lastcommentaryid;
	
	$timeout = get_module_setting('timeout') * 1000;
	
	if (get_module_pref("user_useajax") == 1 && !strstr( $_SERVER['REQUEST_URI'], "/moderate.php" ) && !isset($_REQUEST['comscroll'])) {
		switch($hookname) {
			case "everyheader":
				debug("AJAX");
				set_module_pref('location', '');
				break;
			case "viewcommentaryheader":
				debug("AJAX-VIEWCOMMENTARYHEADER");
				set_module_pref('location', urlencode($_SERVER['REQUEST_URI']));
				
				rawoutput('<script type="text/javascript" src="js/prototype.js"></script>');
				rawoutput('<script type="text/javascript">
<!--
	var lastcommentid = 0;
	var commentarytimerid;
	var pagetimecounterstart = new Date();
	function resetupdatetimer() {
		pagetimecounterstart = new Date();
		dofetchmorecommentary();
	}
	
	function fetchmorecommentary () {
		var pagetimecounterend = new Date();
		if (((pagetimecounterend.getTime() - pagetimecounterstart.getTime()) / 1000 / 60) < 5) {
			dofetchmorecommentary();
		} else {
			$("commentarydisplaydiv").innerHTML += "<br /><center><a href=\"javascript:resetupdatetimer();\">Live Update stopped due to inactivity, click here to reactivate it</a></center><br />";
			clearTimeout(commentarytimerid);
		}
	}
	
	function dofetchmorecommentary () {
		var request = new Ajax.Request(
			"ajaxcommentary.php",
			{
				method: "get",
				parameters: "lastid="+lastcommentid+"&acctid=' . urlencode($session['user']['acctid']) . '&section='.urlencode($args['section']).'&talkline='.urlencode($args['talkline']).'",
				onSuccess: addNewCommentary,
				onFailure: addCommentaryFail
			});
	}
	
	function addNewCommentary(request) {
		clearTimeout(commentarytimerid);
		commentarytimerid  = setTimeout("fetchmorecommentary()", ' . $timeout . ');
		var jsonData = eval("("+request.responseText+")");
		$("commentarydisplaydiv").innerHTML = jsonData.commentary.newcommentary;
		if (jsonData.commentary.replaceform == 1) {
			$("commentaryformcontainer").style.display = "none";
		} else {
			$("commentaryformcontainer").style.display = "";
		}
		lastcommentid = jsonData.commentary.lastid;
	}
	
	function addCommentaryFail(request) {
		clearTimeout(commentarytimerid);
		commentarytimerid  = setTimeout("fetchmorecommentary()", ' . $timeout . ');
	}

// -->
</script>');
				break;
			case "viewcommentary":
				debug("AJAX-VIEWCOMMENTARY");
				break;
			case "viewcommentaryfooter":
				debug("AJAX-VIEWCOMMENTARYFOOTER");
				rawoutput('
<script type="text/javascript">
<!--
	lastcommentid 		= "' . $session['lastcommentid'] . '";
	commentarytimerid	= setTimeout("fetchmorecommentary()", ' . $timeout . ');
//-->
</script>');
				break;
			case "commentarytalkline":
				rawoutput('<script type="text/javascript">
<!--
	function ajaxpostcomment () {
		s = $("inputinsertcommentary").value;
		postcontents = "lastid="+lastcommentid+"&mode=addcomment&insertcommentary="+encodeURIComponent(s)+"&section='.urlencode($args['section']).'&talkline='.urlencode($args['talkline']).'&schema='.urlencode($args['schema']).'"
		var request = new Ajax.Request(
			"ajaxcommentary.php",
			{
				method: "post",
				postBody: postcontents,
				onSuccess: ajaxpostsuccess,
				onFailure: ajaxpostfail
			});
		return false;
	}
	
	function ajaxpostsuccess(request) {
		pagetimecounterstart = new Date();
		$("inputinsertcommentary").value="";
		$("previewtext").innerHTML = "<br />";
		addNewCommentary(request);
	}
	
	function ajaxpostfail(request) {
		//AJAX Post failed, so submit the form manually
		$("commentaryform").submit();
	}

// -->
</script>');
				$args['formline'] = $args['formline'] . ' onsubmit="ajaxpostcomment();return false;"';
				break;
		}
	}
	return $args;
}

function ajaxcommentary_run() {

}
?>
