<?php

function hunterslodge_avatar_getmoduleinfo(){
	$info = array(
		"name"=>"Hunter's Lodge: Avatar",
		"version"=>"2010-08-24",
		"author"=>"Dan Hall",
		"category"=>"Lodge",
		"download"=>"",
		"prefs"=>array(
			"Avatar User Preferences,title",
			"avatar"=>"URL of the avatar|",
		),
	);
	return $info;
}

function hunterslodge_avatar_install(){
	module_addhook("biotop");
	return true;
}

function hunterslodge_avatar_uninstall(){
	return true;
}

function hunterslodge_avatar_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "biotop":
			$picname=str_replace(" ","",get_module_pref("avatar","hunterslodge_avatar",$args['acctid']));
			$image="<img align='left' src='".$picname."'>";
			if ($picname!=""){
				rawoutput($image);
			}
			break;
		}
	return $args;
}

function hunterslodge_avatar_run(){
	require_once("lib/sanitize.php");
	require_once("lib/names.php");
	global $session;
	$op = httpget("op");
	$free = httpget("free");

	page_header("Choose your Avatar");
	
	switch($op){
		case "change":
			output("Want to change your Avatar?  No problem.  Upload your avatar via the box below.  Please note that NSFW images, stolen artwork or otherwise dodgy avatars will be erased without refund.  Upload files in .jpg or .png format.  Your limits are 100 pixels wide, 100 pixels tall, with a maximum filesize of 100k.  100px * 100px * 100k, simple!`n`n");
			output("Upload your avatar:`n");
			rawoutput("<form method='POST' enctype='multipart/form-data' name='upload' action='runmodule.php?module=hunterslodge_avatar&op=confirm&free=$free'><input type='file' name='file'><br><br><input type='submit' class='button' name='Upload' value='Upload!'></form>");
			addnav("", "runmodule.php?module=hunterslodge_avatar&op=confirm&free=".$free);
			addnav("Cancel");
			addnav("Don't set an Avatar, just go back to the Lodge","runmodule.php?module=iitems_hunterslodge&op=start");
		break;
		case "confirm":
			if(httppost("Upload")) {
				$allowed_types = "(jpg|png)";
				debug(httpallpost());
				debug($_FILES);
				$file=$_FILES["file"];
				debug($file);
				$errors= array(
			       0=>"File Received!",
			       1=>"The uploaded file exceeds the upload_max_filesize directive in php.ini.",
			       2=>"The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.",
			       3=>"The uploaded file was only partially uploaded.",
			       4=>"No file was uploaded.",
			       6=>"Missing a temporary folder.",
				);
				output_notl("`\$".$errors[$error]."`n");
				if (is_uploaded_file($file["tmp_name"])) {
					if(preg_match("/\." . $allowed_types . "$/i", $file["name"])) {
						if($file["size"] <= 102400) {
							$extension=substr($file['name'],strlen($file['name'])-4,4);
							$loginname = str_replace(" ","",$session['user']['login']);
							$filename="images/avatars/".date("YmdHs").$loginname.$extension;
							if(move_uploaded_file($file["tmp_name"], $filename)) {
								$pic_size = @getimagesize($filename); // GD2 required here - else size always is recognized as 0
								$pic_width = $pic_size[0];
								$pic_height = $pic_size[1];
								if ($pic_height <= 100 && $pic_width <= 100){
									output("So, this is what you want to look like?  Click \"Set Avatar\" to confirm.`n`n");
									addnav("Confirm");
									addnav("Set Avatar","runmodule.php?module=hunterslodge_avatar&op=set&free=$free&avatar=".rawurlencode($filename));
									$image="<img align='left' src='".$filename."'>";
									
									rawoutput("<table><tr><td valign='top'>");
									$terms=appoencode(translate_inline("Your Avatar"));
									rawoutput("</td><td valign='top'>$image</td></tr><td></td><td>$terms</td></table>");
								} else {
									output("That picture's too big!  The limit is 100 pixels by 100 pixels.`n`n");
								}
							} else {
								output ("The file could not be uploaded.`n`n");
							}
						} else {
							output ("You may only have a filesize up to 100 kilobytes!`n`n");
						}
					} else {
						output ("That file extension is not supported!`n`n");
					}
				} else {
					output ("You did not specify a file to upload.`n`n");
				}
			}
			output("`0To try again with a different picture, use the form below.`n`n");
			rawoutput("<form method='POST' enctype='multipart/form-data' name='upload' action='runmodule.php?module=hunterslodge_avatar&op=confirm&free=$free'><input type='file' name='file'><br><br><input type='submit' class='button' name='Upload' value='Upload!'></form>");
			addnav("", "runmodule.php?module=hunterslodge_avatar&op=confirm&free=".$free);
			addnav("Cancel");
			addnav("Don't set an Avatar, just go back to the Lodge","runmodule.php?module=iitems_hunterslodge&op=start");
		break;
		case "set":
			$av = httpget("avatar");
			set_module_pref("avatar",$av);
			output("Your Avatar has been changed!`n`n");
			if (!$free){
				$id = has_item("hunterslodge_avatar");
				delete_item($id);
			}
			addnav("Return");
			addnav("Return to the Lodge","runmodule.php?module=iitems_hunterslodge&op=start");
		break;
	}
	page_footer();


}
?>