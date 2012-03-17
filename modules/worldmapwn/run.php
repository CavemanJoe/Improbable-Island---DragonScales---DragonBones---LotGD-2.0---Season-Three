<?php

function worldmapwn_run_real(){
	require_once('modules/worldmapwn/lib/lib.php');
	require_once("modules/worldmapwn/config/terrain.php");
	//global $terrainsinfo;
	global $session;
	//require_once("modules/worldmapwn/config/terrains.php");
	switch (httpget("op")){
			case "admin":
				page_header("Worldmapwn Settings");		
				$canedit=get_module_pref("cannedit");
				if (httpget("admin")==true && $session['user']['superuser']==true && $canedit==true){
					output("`bMap File Settings`b");
					rawoutput("<form>");

					output("`bCity Locations`b");
					rawoutput("</form>");
				}				
				addnav("");
				addnav("X?Return to the Grotto","superuser.php");
				addnav("M?Return to the Mundane","village.php");
				page_footer();
				break;
			case "arrive":
				$dest=httpget("dest");
				require_once("lib/cityprefs.php");
				if ($dest==null||$dest==""){
				$city=getsetting("villagename");
				} else {
				$city=get_cityprefs_cityname("cityid",$dest);
				}
				$session['user']['location']=$city;
				redirect("village.php");
				break;
			case "continue":
				$loc=get_module_pref("worldXYZ",$house['location'],"worldmapen");
				$session['user']['location']=$loc;
				redirect("runmodule.php?module=worldmapwn&op=travel");break;
			case "beginjourney":
				redirect("runmodule.php?module=worldmapen&op=travel");break;
			case "gypsy":
				$buymap=httpget("buymap");
				$worldmapCostGold=get_module_setting("worldmapCostGold");
				if ($buymap == ''){
					output("`5\"`!Ah, yes.  An adventurer.  I could tell by looking into your eyes,`5\" the gypsy says.`n");
					output("\"`!Many people have lost their way while journeying without a guide such as this.");
					output("It will let you see all the world.`5\"`n");
					output("\"`!Yes, yes.  Let's see...  What sort of price should we put on this?");
					output("Hmm.  How about `^%s`! gold?`5\"",$worldmapCostGold);
					addnav(array("Buy World Map `0(`^%s gold`0)", $worldmapCostGold),
					"runmodule.php?module=worldmapen&op=gypsy&buymap=yes");
					addnav("Forget it","village.php");
				} elseif ($buymap == 'yes'){
					if ($session['user']['gold'] < $worldmapCostGold){
						output("`5\"`!What do you take me for?  A blind hag?  Come back when you have the money`5\"");
						addnav("Leave quickly","village.php");
					}else{
						output("`5\"`!Enjoy your newfound sight,`5\"  the gypsy says as she walks away to greet some patrons that have just strolled in.");
						$session['user']['gold']-=$worldmapCostGold;
						set_module_pref("worldmapbuy",1);
						require_once("lib/villagenav.php");
						villagenav();
					}

				}
				break;
			case "travel"://This is the main part of worldmapwn, the traveling part
				page_header("Journey");
				
				require_once("modules/worldmapwn/run/dir.php");

				$currentloc=$session['user']['location'];
				list($x,$y,$z)=explode(",",$currentloc);
				if ($session['user']['superuser']&~SU_DOESNT_GIVE_GROTTO){
					addnav("X?`bSuperuser Grotto`b","superuser.php");
				}

				require_once("modules/worldmapwn/lib/lib.php");
				$cities=worldmapwn_findcity($currentloc);
				debug("findcity results");
				debug($cities);
				foreach($cities as $dest){
					//for now,delete later once for loop is working
					//$dest="htddthtd";
					debug($dest);
					$destid=$dest["id"];
					$destname=$dest["name"];				
						addnav("Cities");
						addnav(array("O?Enter %s", $destname), "runmodule.php?module=worldmapwn&op=arrive&dest=$destid");
					

				}
				addnav("Journey");
				require_once("modules/worldmapwn/lib/readmap.php");
				$map=worldmapwn_map_array($z);
				//debug($map);
				if ($map==false){
					output("`0You look out and see what looks like a strange building site. To your right there are what appear to be men in orange worksuits laying rocks, while to your left another appears to be creating a lake using a giant hosepipe. It does not look like something to travel across, but maybe later.");
					if ($session['user']['superuser']==true){
						output("`n`n`1Oops! There doesn't seem to be any map created. If you have created a map but are still seeing this message, make sure it is in modules/worldmapwn/maps and it is listed in the settings with a valid ID. Alternativly, the user has been told to open a mapid that isn't there.");}
					}
					//page_footer();
					break;}
				$maxx=count($map)-4;
				$maxy=count($map[1])-2;//rectangular maps only, no jagged ones.

				
				if ($y!=1){
					addnav("Travel North","runmodule.php?module=worldmapwn&op=travel&dir=n");					
					if ($x!=1){
					addnav("Travel North-West","runmodule.php?module=worldmapwn&op=travel&dir=nw");}
					if ($x!=$maxx){
					addnav("Travel North-East","runmodule.php?module=worldmapwn&op=travel&dir=ne");}
				}
				if ($y!=$maxy){
					addnav("Travel South","runmodule.php?module=worldmapwn&op=travel&dir=s");					
					if ($x!=1){
					addnav("Travel South-West","runmodule.php?module=worldmapwn&op=travel&dir=sw");}
					if ($x!=$maxx){
					addnav("Travel South-East","runmodule.php?module=worldmapwn&op=travel&dir=se");}
				}

				require_once("modules/worldmapwn/lib/terrain.php");
				$terraincode=worldmapwn_terraincode_coords($currentloc,$map);
				debug($terraincode);
				modulehook("worldmapwn-travel");
				
				list($code1,$code2)=explode("^",$terraincode);
				debug("The parts of the terraincode are $code1 and $code2.");
				$image1=worldmapwn_image($code1,$terrainsinfo);
				
				if ($code2){
					$image2=worldmapwn_image($code2,$terrainsinfo);}
				debug("Image1 is $image1.");
				debug("Image2 is $image2.");				
				//this is where the images are displayed
				//<IMG STYLE="position:absolute; TOP:35px; LEFT:170px; WIDTH:50px; HEIGHT:50px" SRC="circle.gif">
						rawoutput("<div style=\" position:relative;\">");
						rawoutput("<img style=\"position:absolute; top:35px; left:250px z-index:0;\" src=\"$image1\" alt=\"$currentloc\"/>");
						if ($image2!=false && $image2!=null){				
						rawoutput("<img style=\"position:absolute; top:35px; left:300px z-index:1;\" src=\"$image2\"/>");
						} else {
						debug("image2 fails");}
						rawoutput("</div>");
				
				require_once("modules/worldmapwn/run/supertravel.php");
				page_footer();
				break;

			}


?>
