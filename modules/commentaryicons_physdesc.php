<?php

function commentaryicons_physdesc_getmoduleinfo(){
	$info = array(
		"name"=>"Commentary Icons: Avatar and Physical Description",
		"version"=>"2010-06-02",
		"author"=>"Dan Hall",
		"category"=>"Commentary Icons",
		"download"=>"",
		"prefs"=>array(
			"Commentary Info: Physical Description,title",
			"user_physdesc"=>"Short physical description of your character shown in commentary mouseovers (100 chars max - blank it to hide your description),string,100",
			"user_showmyavatar"=>"Show my avatar in commentary mouseovers,bool|1",
			"user_showdesc"=>"Show other people's physical descriptions in commentary mouseovers,bool|1",
		),
	);
	return $info;
}
function commentaryicons_physdesc_install(){
	module_addhook_priority("postcomment",50);
	module_addhook("commentbuffer");
	return true;
}
function commentaryicons_physdesc_uninstall(){
	return true;
}
function commentaryicons_physdesc_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "postcomment":
			require_once "lib/sanitize.php";
			$mouseover="";
			if (get_module_pref("avatar","hunterslodge_avatar")!="" && get_module_pref("user_showmyavatar")){
				$filename = str_replace(" ","",get_module_pref("avatar","hunterslodge_avatar"));
				$image="<img align='left' src='".$filename."' ";
				$pic_size = @getimagesize($filename); // GD2 required here - else size always is recognized as 0
				$pic_width = $pic_size[0];
				$pic_height = $pic_size[1];
				$resizedwidth=$pic_width;
				$resizedheight=$pic_height;
				if ($pic_height > 100) {
					$resizedheight=100;
					$resizedwidth=round($pic_width*(100/$pic_height));
				}
				if ($resizedwidth > 100) {
					$resizedheight=round($resizedheight*(100/$resizedwidth));
					$resizedwidth=100;
				}
				$image.=" height=\"$resizedheight\"  width=\"$resizedwidth\" ";
				$mouseover.="<table width=100% border=0 cellpadding=1 cellspacing=0><tr><td>".$image.">";
				$closetable=1;
			}
			if (get_module_pref("user_physdesc") && get_module_pref("user_physdesc")!=""){
				$mouseover.=stripslashes(get_module_pref("user_physdesc"));
			}
			if ($mouseover!=""){
				if ($closetable) $mouseover.="</tr></td></table>";
				$args['info']['mouseover']['appearance']=$mouseover."`n";
			}
		break;
		case "commentbuffer":
			if (!get_module_pref("user_showdesc")){
				foreach($args AS $line=>$details){
					if (isset($details['info']['mouseover']['appearance'])){
						unset($args[$line]['info']['mouseover']['appearance']);
					}
				}
			}
		break;
		}
	return $args;
}

function commentaryicons_physdesc_run(){
}
?>