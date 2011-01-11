<?php

function commentaryicons_avatar_getmoduleinfo(){
	$info = array(
		"name"=>"Commentary Icons: Avatars",
		"version"=>"2010-06-02",
		"author"=>"Dan Hall",
		"category"=>"Commentary Icons",
		"download"=>"",
	);
	return $info;
}
function commentaryicons_avatar_install(){
	module_addhook_priority("postcomment",25);
	return true;
}
function commentaryicons_avatar_uninstall(){
	return true;
}
function commentaryicons_avatar_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "postcomment":
			debug("Avatar!");
			if (get_module_pref("bought","avatar") && get_module_pref("avatar","avatar") && get_module_pref("validated","avatar")){
				$args['info']['mouseover'][]="<img src=\"".get_module_pref("avatar","avatar")."\" align=\"left\">";
			}
			break;
		}
	return $args;
}

function commentaryicons_avatar_run(){
}
?>