<?php

page_header("City Prefs Editor");
    $op=httpget('cop');
	$cityid = httpget('cityid');
	if($cityid>0){
	$cityname=get_cityprefs_cityname("cityid",$cityid);
		page_header("%s Properties",$cityname);
		$modu=get_cityprefs_module("cityid",$cityid);
		if($modu!="none"){
			addnav("Operations");
			addnav("Module settings","configuration.php?op=modulesettings&module=$modu");
		}
		addnav("Navigation");
		if(is_module_active("cities"))	addnav(array("Journey to %s",$cityname),"runmodule.php?module=cities&op=travel&city=".urlencode($cityname)."&su=1");
		else addnav(array("Journey to %s",$cityname),"village.php");
	}
	addnav("Navigation");	
   addnav("Back to the Grotto","superuser.php");   
   if(is_module_active("modloc")) addnav("Module locations","runmodule.php?module=modloc");
   if($op!="su") addnav("Back to city list","runmodule.php?module=cityprefs&op=su");
switch ($op) {
	case "su":
			addnav("Operations");
            addnav("Auto-add new cities","superuser.php?op=cityprefs&op=update");  
            $id=translate_inline("ID");
            $name=translate_inline("City Name");
            $module=translate_inline("Module");
            $edit=translate_inline("Edit");
            $sql = "select * from ".db_prefix("cityprefs");
            $result=db_query($sql);
            rawoutput("<table border='0' cellpadding='3' cellspacing='0' align='center'><tr class='trhead'><td style=\"width:50px\">$id</td><td style='width:150px' align=center>$name</td><td align=center>$module</td><td align=center>$edit</td></tr>"); 
            for ($i = 0; $i < db_num_rows($result); $i++){
                $row = db_fetch_assoc($result);
                $vloc = array();
                $vname = getsetting("villagename", LOCATION_FIELDS);
                $vloc[$vname] = "village";
                $vloc = modulehook("validlocation", $vloc);
                ksort($vloc);
                reset($vloc);
                foreach($vloc as $loc=>$val) {
                    if ($loc == $row['cityname']) $area=$val;
                }
                rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td align=center>".$row['cityid']."</td><td align=center>");
                output_notl("%s",$row['cityname']);
                rawoutput("</td><td align=center>");
                output_notl("%s",$row['module']);
                rawoutput("</td><td align=center>");
                rawoutput("<a href='superuser.php?op=cityprefs&op=editmodule&area=".htmlentities($val)."&cityid=".$row['cityid']."'>$edit</a></td></tr>");
                addnav("","superuser.php?op=cityprefs&op=editmodule&area=".htmlentities($val)."&cityid=".$row['cityid']."");  
            }
            rawoutput("</table>");
            break;
    
        case "update":
            $vloc = array();
            $vloc = modulehook("validlocation", $vloc);
            ksort($vloc);
            reset($vloc);
            $out=0;
            foreach($vloc as $loc=>$val) {
                $sql = "select cityname from ".db_prefix("cityprefs")." where cityname='".addslashes($loc)."'";
                $result=db_query($sql);
                if(db_num_rows($result)==0){
                    $sql = "select modulename from ".db_prefix("module_settings")." where value='".addslashes($loc)."' and setting='villagename'";
                    $result=db_query($sql);
                    $row = db_fetch_assoc($result);           
                    $sql = "INSERT INTO ".db_prefix("cityprefs")." (module,cityname) VALUES ('".$row['modulename']."','".addslashes($loc)."')";
                    db_query($sql);
                    $out=1;
                    output("`n`@%s`0 was added.",$loc);
                }
            }
            if($out==0) output("There were no new locations found.");
        break; 
        case "editmodule": //code from clan editor by CortalUX
        case "editmodulesave":
			addnav("Operations");
			addnav("Edit city name and module","superuser.php?op=cityprefs&op=editcity&cityid=$cityid");
			addnav("Delete this city","superuser.php?op=cityprefs&op=delcity&cityid=$cityid");
			$mdule = httpget("mdule");
			if($mdule==""){
				output("Select a pref to edit.`n`n");
			}else{
				if ($op=="editmodulesave") {
					// Save module prefs
					$post = httpallpost();
					reset($post);
					while(list($key, $val) = each($post)) {
						set_module_objpref("city", $cityid, $key, stripslashes($val), $mdule);
					}
					output("`^Saved!`0`n");
				}
				require_once("lib/showform.php");
				rawoutput("<form action='superuser.php?op=cityprefs&op=editmodulesave&cityid=$cityid&mdule=$mdule' method='POST'>");
				module_objpref_edit("city", $mdule, $cityid);
				rawoutput("</form>");
				addnav("","superuser.php?module=cityprefs&op=editmodulesave&cityid=$cityid&mdule=$mdule");
				//code from clan editor by CortalUX
			}
 			addnav("Module Prefs");
			module_editor_navs("prefs-city","superuser.php?op=cityprefs&op=editmodule&cityid=$cityid&mdule=");
		break;
        
        case "editcity":
			output("Changing these values will not affect the city itself, just what city is associated with the preferences.  This is useful if you want to preserve prefs after removing a city.");
			addnav("Navigation");
			addnav("Back to city properties","superuser.php?op=cityprefs&op=editmodule&cityid=$cityid");
            $sql = "select * from ".db_prefix("cityprefs")." where cityid=$cityid";
            $result=db_query($sql);
            $row = db_fetch_assoc($result);
            $module=$row['module'];
            $city=$row['cityname'];
            $submit=translate_inline("Submit");
            rawoutput("<form action='superuser.php?op=cityprefs&op=editcity2&cityid=$cityid' method='POST'>");
            addnav("","superuser.php?op=cityprefs&op=editcity2&cityid=$cityid");
            rawoutput("<input name='cityname' id='cityname' value='$city' size='40' maxlength='255'><br>");
            rawoutput("<input name='modulename' id='modulename' value='$module' size='40' maxlength='255'><br>");
            rawoutput("<input type='submit' class='button' value='$submit'></form>");              
        break;
        
        case "editcity2":
			addnav("Navigation");
			addnav("Back to city properties","superuser.php?op=cityprefs&op=editmodule&cityid=$cityid");
            $cityname = httppost('cityname');
            $modulename = httppost('modulename');
            db_query("update ".db_prefix("cityprefs")." set cityname='".$cityname."',module='".$modulename."' where cityid=$cityid");
            output("The city name is now %s and the module name is %s.",$cityname,$modulename);
        break;
        
        case "delcity":
			addnav("Navigation");
            $cityid = httpget('cityid');
			addnav("Back to city properties","superuser.php?op=cityprefs&op=editmodule&cityid=$cityid");
			addnav("Options");
			addnav("Yes, delete it","superuser.php?op=cityprefs&op=delcity2&cityid=$cityid");
            output("Are you sure you want to delete this city?  All city prefs will be deleted.  If you would like to retain these settings for a future city, just rename it.");
        break;

        case "delcity2":
			addnav("Navigation");
			addnav("Back to city properties","superuser.php?op=cityprefs&op=editmodule&cityid=$cityid");
            $cityid = httpget('cityid');		
            db_query("delete from ".db_prefix("cityprefs")." where cityid=$cityid");
            output("The city has been deleted.");
        break;
}

?>
