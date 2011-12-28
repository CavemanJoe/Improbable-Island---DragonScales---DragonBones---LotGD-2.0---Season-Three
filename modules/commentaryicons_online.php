<?php

function commentaryicons_online_getmoduleinfo(){
	$info = array(
		"name"=>"Commentary Icons: Online Status",
		"version"=>"2010-05-27",
		"author"=>"Dan Hall",
		"category"=>"Commentary Icons",
		"download"=>"",
	);
	return $info;
}
function commentaryicons_online_install(){
	module_addhook("commentbuffer");
	return true;
}
function commentaryicons_online_uninstall(){
	return true;
}
function commentaryicons_online_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "commentbuffer":
			$chatloc = $session['user']['chatloc'];
			// debug($args);
			// debug($chatloc,true);
			// $count = count($args);
			// $offline = date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds"));

			// for ($i=0; $i<$count; $i++){
				// if ($args[$i]['session']['laston'] < $offline || !$args[$i]['session']['loggedin']){
					// $args[$i]['info']['online']=0;
					// $icon = array(
						// 'icon' => "images/icons/onlinestatus/offline.png",
						// 'mouseover' => "Offline",
					// );
					// $args[$i]['info']['icons'][]=$icon;
				// } else if ($args[$i]['session']['chatloc']==$chatloc){
					// $args[$i]['info']['online']=2;
					// $icon = array(
						// 'icon' => "images/icons/onlinestatus/nearby.png",
						// 'mouseover' => "Nearby",
					// );
					// $args[$i]['info']['icons'][]=$icon;
				// } else {
					// $args[$i]['info']['online']=1;
					// $icon = array(
						// 'icon' => "images/icons/onlinestatus/online.png",
						// 'mouseover' => "Online",
					// );
					// $args[$i]['info']['icons'][]=$icon;
				// }
			// }
			break;
		}
	return $args;
}

function commentaryicons_online_run(){
}
?>