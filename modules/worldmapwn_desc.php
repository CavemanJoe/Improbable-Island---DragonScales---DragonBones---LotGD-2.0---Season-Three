<?php

function worldmap_desc_getmoduleinfo(){
    $info = array(
        "name"=>"World Map Hex Desciptions",
        "version"=>"2012-02-25 1.0.0",
        "author"=>"Cousjava",
        "category"=>"World Map",
        "download"=>"https://github.com/Cousjava/Improbable-Island",
        ); 
   return $info;
}

function worldmap_desc_install(){
        module_addhook("worldmapwn");
        return true;
}


function worldmap_desc_uninstall(){
	return true;
}

function worldmap_desc_dohook($hookname,$args){
    global $session;
    switch($hookname){
   	case "worldmapwn":
		switch(httpget('op')){
			case "travel":
				require_once("modules/worldmapwn/lib/hexprefs.php");
				$hexid=get_worldmapwn_hexid();
				$desc = get_module_objpref("worldmapwn",$hexid,"hexdesc");
				output("`n`n`0%s",$desc);
				if ($session['user']['superuser']==true && get_module_pref("cannedit","worldmapwn")==true){
					addnav("Superuser");
					addnav("runmodule.php?module=worldmapwn_desc&op=locdesc");
				}
				break;
			}
	}
    return $args;
}

function worldmap_desc_run(){
	page_header("World Map Hex Descriptions");
		switch (httpget('op')){
			case "locdesc":
				addnav("Continue traveling","runmodule.php?module=worldmapwn&op=travel");
				addnav("Go to the Grotto","superuser.php");
				$hexid=get_worldmapwn_hexid();
				if ($_POST['sumbit']==true){
					$desc=$_POST['newdesc'];
					set_module_objpref("worldmapwn",$hexid,"hexdesc",$desc);
					output("The description of you current hex has been changed.`n`n")
				} else {
					$desc = get_module_objpref("worldmapwn",$hexid,"hexdesc");
				}
				output("Enter new hex description for your current location below (max 255 chars):");
				rawoutput('<form><textarea name="newdesc" rows="3" cols="30" value='."$desc".'> </textarea> <input type="submit" name="submit" value="Go"></form>');
		}


}


?>
