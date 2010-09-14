<?php

function commentaryicons_paranoia_getmoduleinfo(){
	$info = array(
		"name"=>"Commentary Icons: Paranoia",
		"version"=>"2010-06-24",
		"author"=>"Dan Hall",
		"category"=>"Commentary Icons",
		"download"=>"",
	);
	return $info;
}
function commentaryicons_paranoia_install(){
	module_addhook("commentbuffer-preformat");
	return true;
}
function commentaryicons_paranoia_uninstall(){
	return true;
}
function commentaryicons_paranoia_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "commentbuffer-preformat":
			foreach($args AS $key=>$vals){
				$args[$key]['comment']="You're gonna die, ".$session['user']['name']."`0.";
			}
		break;
		}
	return $args;
}

function commentaryicons_paranoia_run(){
}
?>