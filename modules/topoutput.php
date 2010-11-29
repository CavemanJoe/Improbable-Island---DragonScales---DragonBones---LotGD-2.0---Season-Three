<?php

global $output;
debug($output);

function topoutput_getmoduleinfo(){
	$info = array(
		"name"=>"topoutput",
		"version"=>"2010-02-24",
		"author"=>"Dan Hall",
		"category"=>"Improbable",
		"download"=>"",
	);
	return $info;
}

function topoutput_install(){
	module_addhook("everyheader-loggedin");
	module_addhook("everyfooter-loggedin");
	return true;
}

function topoutput_uninstall(){
	return true;
}

function topoutput_dohook($hookname,$args){
	global $session;
	if ($hookname=="everyheader-loggedin"){
		rawoutput("!!!TOP!!!");
	}
	if ($hookname=="everyfooter-loggedin"){
		debug($output);
		str_replace("!!!TOP!!!","Woo yay!",$output);
	}
	return $args;
}

function topoutput_run(){
}

?>
