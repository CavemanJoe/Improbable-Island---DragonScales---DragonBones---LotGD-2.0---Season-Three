<?php

function commentaryicons_armour_getmoduleinfo(){
	$info = array(
		"name"=>"Commentary Icons: Armour",
		"version"=>"2010-06-02",
		"author"=>"Dan Hall",
		"category"=>"Commentary Icons",
		"download"=>"",
		"prefs"=>array(
			"Commentary Icons: Armour,title",
			"user_showmyarmour" => "Show an icon for my character's armour in commentary areas,bool|1",
			"user_showarmour" => "Show other people's armour in commentary areas,bool|1",
		),
	);
	return $info;
}
function commentaryicons_armour_install(){
	module_addhook_priority("postcomment",70);
	module_addhook("commentbuffer");
	return true;
}
function commentaryicons_armour_uninstall(){
	return true;
}
function commentaryicons_armour_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "postcomment":
			if (get_module_pref("user_showmyarmour")){
				$armourfilename = strtolower($session['user']['armor']);
				$armourfilename = strtr($armourfilename, " '-_.!+1234567890,", "                  ");
				$armourfilename = str_replace(" ", "", $armourfilename);
				$armourfilename = "images/icons/armour/".$armourfilename.".png";
				if (file_exists($armourfilename)){
					$armour = "<img src=\"".$armourfilename."\" alt=\"armour: ".$session['user']['armor']."\" title=\"armour: ".$session['user']['armor']."\">";
					$args['info']['mouseover']['armour']="`n<img src=\"".$armourfilename."\">`n".$session['user']['armor']."`n";
				} else {
					$armour = "Armour: ".$session['user']['armor'];
					$args['info']['mouseover']['armour']="`nArmour: ".$session['user']['armor'];
				}
				// $args['info']['icons']['armour']=array(
					// 'icon' => $armourfilename,
					// 'mouseover' => "armour: ".$session['user']['armor'],
				// );
			}
			break;
		case "commentbuffer":
			if (!get_module_pref("user_showarmour")){
				foreach($args AS $line=>$details){
					if (isset($details['info']['mouseover']['armour'])){
						unset($args[$line]['info']['mouseover']['armour']);
					}
				}
			}
		break;
		}
	return $args;
}

function commentaryicons_armour_run(){
}
?>