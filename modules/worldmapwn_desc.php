<?php

function worldmap_desc_getmoduleinfo(){
    $info = array(
        "name"=>"World Map Tile Desciptions",
        "version"=>"2012-02-25 1.0.0",
        "author"=>"Cousjava",
        "category"=>"World Map",
        "download"=>"https://github.com/Cousjava/Improbable-Island",
        ); 
   return $info;
}

function worldmap_desc_install(){
        module_addhook("worldmapwn");
	module_addhook("superuser");
        return true;
}


function worldmap_desc_uninstall(){
	return true;
}

function worldmap_desc_dohook($hookname,$args){
    global $session;
    switch($hookname){
   	case "worldmapwn":
		$desc=get_module_objpref("");		
		break;
	case "superuser":
		
		break;
	}
    return $args;
}


?>
