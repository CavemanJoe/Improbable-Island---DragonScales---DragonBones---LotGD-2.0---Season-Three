<?php
require_once("common.php");
require_once("lib/commentary.php");
require_once("lib/sanitize.php");
require_once("lib/http.php");
require_once("lib/stamina/stamina.php");

$op=httpget("op");
	switch ($op){
		case "show":
			require_once("lib/stamina/show.php");
			break;
		case "superuser":
			check_su_access(0xFFFFFFFF &~ SU_DOESNT_GIVE_GROTTO);
			addcommentary();
			tlschema("superuser");
			require_once("lib/superusernav.php");
			superusernav();
			require_once("lib/stamina/superuser.php");
			break;
			
		case "editabis":
			require_once("lib/stamina/editabis.php");
			break;
		
		default:
			
		break;
	}

?>
