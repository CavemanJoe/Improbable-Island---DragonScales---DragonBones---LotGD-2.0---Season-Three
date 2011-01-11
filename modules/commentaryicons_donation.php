<?php

function commentaryicons_donation_getmoduleinfo(){
	$info = array(
		"name"=>"Commentary Icons: Donation",
		"version"=>"2010-06-02",
		"author"=>"Dan Hall",
		"category"=>"Commentary Icons",
		"download"=>"",
		"prefs"=>array(
			"Commentary Info: Donator Status,title",
			"user_showmydonation"=>"Show my Site Supporter icon in commentary areas,bool|1",
			"user_showdonations"=>"Show other people's Site Supporter icons in commentary areas,bool|1",
		),
	);
	return $info;
}
function commentaryicons_donation_install(){
	module_addhook_priority("postcomment",20);
	module_addhook("commentbuffer");
	return true;
}
function commentaryicons_donation_uninstall(){
	return true;
}
function commentaryicons_donation_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "postcomment":
			$donation = $session['user']['donation'];
			if (get_module_pref("user_showmydonation")){
				if ($donation >= 2000){
					$args['info']['icons']['donator']=array(
						'icon' => "images/icons/donator/donator3.png",
						'mouseover' => "Extra-Awesome Site Supporter",
					);
				} else if ($donation >= 1000){
					$args['info']['icons']['donator']=array(
						'icon' => "images/icons/donator/donator2.png",
						'mouseover' => "Special Site Supporter",
					);
				} else if ($donation >= 100){
					$args['info']['icons']['donator']=array(
						'icon' => "images/icons/donator/donator1.png",
						'mouseover' => "Site Supporter",
					);
				}
			}
		break;
		case "commentbuffer":
			if (!get_module_pref("user_showdonations")){
				foreach($args AS $line=>$details){
					if (isset($details['info']['icons']['donator'])){
						unset($args[$line]['info']['icons']['donator']);
					}
				}
			}
		break;
	}
	return $args;
}

function commentaryicons_donation_run(){
}
?>