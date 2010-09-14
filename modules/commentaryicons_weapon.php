<?php

function commentaryicons_weapon_getmoduleinfo(){
	$info = array(
		"name"=>"Commentary Icons: Weapons",
		"version"=>"2010-06-02",
		"author"=>"Dan Hall",
		"category"=>"Commentary Icons",
		"download"=>"",
		"prefs"=>array(
			"Commentary Icons: Weapons,title",
			"user_showmyweapon" => "Show an icon for my character's weapon in commentary areas,bool|1",
			"user_showweapons" => "Show other people's weapons in commentary areas,bool|1",
		),
	);
	return $info;
}
function commentaryicons_weapon_install(){
	module_addhook_priority("postcomment",70);
	module_addhook("commentbuffer");
	return true;
}
function commentaryicons_weapon_uninstall(){
	return true;
}
function commentaryicons_weapon_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "postcomment":
			if (get_module_pref("user_showmyweapon")){
				$weaponfilename = strtolower($session['user']['weapon']);
				$weaponfilename = strtr($weaponfilename, " '-_.!+1234567890,", "                  ");
				$weaponfilename = str_replace(" ", "", $weaponfilename);
				$weaponfilename = "images/icons/weapons/".$weaponfilename.".png";
				if (file_exists($weaponfilename)){
					$weapon = "<img src=\"".$weaponfilename."\" alt=\"Weapon: ".$session['user']['weapon']."\" title=\"Weapon: ".$session['user']['weapon']."\">";
					$args['info']['mouseover']['weapon']="`n<img src=\"".$weaponfilename."\">`n".$session['user']['weapon']."`n";
				} else {
					$weapon = "Weapon: ".$session['user']['weapon'];
					$args['info']['mouseover']['weapon']="`nWeapon: ".$session['user']['weapon']."`n";
				}
				// $args['info']['icons']['weapon']=array(
					// 'icon' => $weaponfilename,
					// 'mouseover' => "Weapon: ".$session['user']['weapon'],
				// );
			}
			break;
		case "commentbuffer":
			if (!get_module_pref("user_showweapons")){
				foreach($args AS $line=>$details){
					if (isset($details['info']['mouseover']['weapon'])){
						unset($args[$line]['info']['mouseover']['weapon']);
					}
				}
			}
		break;
		}
	return $args;
}

function commentaryicons_weapon_run(){
}
?>