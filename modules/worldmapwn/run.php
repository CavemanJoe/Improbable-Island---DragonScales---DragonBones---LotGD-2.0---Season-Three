<?php

function worldmapwn_run_real(){
	
	global $session
	
	switch (httpget("op"){
			global $session;
			case "arrive":
				$dest=httpget("dest");
				$session['user']['location']=$dest;
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
				}break;

			case "travel"://This is the main part of worldmapwn, the traveling part
				page_header("Journey")
				switch (httpget("dir"){//This sets the users new location
					case "setloc":
						$loc=httpget('loc');
						$session['user']['location']=$loc;
					case "n":
						$start=$session['user']['location'];
						list($x,$y,$z)=explode(",",$start);
						$locchange=$y-1;
						$newloc=$x.",".$locchange.",".$z];
						$session['user']['location']=$newloc;
						break;
					case "ne": break;
						$start=$session['user']['location'];
						list($x,$y,$z)=explode(",",$start);
						$locchangex=$x+1;
						if ($x % 2 ==0){
							$locchangey=$y;
						} else {
							$locchangey=$y-1;
						}
						$newloc=$locchangex.",".$locchangey.",".$z;
						$session['user']['location']=$newloc;
					case "nw": break;
						$start=$session['user']['location'];
						list($x,$y,$z)=explode(",",$start);
						$locchangex=$x+1;
						if ($x % 2 ==0){
							$locchangey=$y;
						} else {
							$locchangey=$y-1;
						}
						$newloc=$locchangex.",".$locchangey.",".$z;
						$session['user']['location']=$newloc;
					case "s":
						$start=$session['user']['location'];
						list($x,$y,$z)=explode(",",$start);
						$locchange=$y+1;
						$newloc=$x.",".$locchange.",".$z;
						$session['user']['location']=$newloc;
						break;
					case "se": break;
						$start=$session['user']['location'];
						list($x,$y,$z)=explode(",",$start);
						$locchangex=$x+1;
						if ($x % 2 ==0){
							$locchangey=$y+1;
						} else {
							$locchangey=$y;
						}
						$newloc=$locchangex.",".$locchangey.",".$z;
						$session['user']['location']=$newloc;
					case "sw": break;
						$start=$session['user']['location'];
						list($x,$y,$z)=explode(",",$start);
						$locchangex=$x+1;
						if ($x % 2 ==0){
							$locchangey=$y+1;
						} else {
							$locchangey=$y;
						}
						$newloc=$locchangex.",".$locchangey.",".$z;
						$session['user']['location']=$newloc;
					case "begin":
						$cid = get_cityprefs_cityid("location",$session['user']['location']);
						$cname=$session['user']['location'];
						$cityloc = get_module_objpref("city",$cid,"location");
						$session['user']['location']=$cityloc;
						$outmess=e_rand(1,5);
						switch ($outmess){
							case 1:output("`b`&The gates of %s close behind you. A shiver runs down your back as you face the wilderness around you.`0`b",$cname);
							case 2:output("`b`&The gates of %s close behind you. You're all alone now...`0`b",$cname);
							case 3:output("`b`&The gates of %s close behind you. The sound of the wilderness settles in around you as you think to yourself what evil must lurk within.`0`b",$cname);	
							case 4:output("`b`&The gates of %s close behind you. Perhaps you should go back in...`0`b",$cname);
							case 5:output("`b`&The gates of %s close behind you. A howling noise bellows from deep within the forest.  You hear the guards from the other side of the gates yell \"Good Luck!\" and what sounds like \"they'll never make it.`0`b",$cname);
						}
						modulehook("worldmapwn-travel");
						break;
					}

				$currentloc=$session['user']['location'];
				list($x,$y,$z)=explode(",",$currentloc);
				if ($session['user']['superuser']&~SU_DOESNT_GIVE_GROTTO){
					addnav("X?`bSuperuser Grotto`b","superuser.php");
				}
				require_once("modules/worldmapwm/lib/lib.php");
				$cities=worldmapwn_findcities($currentloc);
				for ($cities as $dest){
					if ($dest==$currentloc){
						addnav("Cities");
						addnav(array("O?Enter %s", $dest), "runmodule.php?module=worldmapwn&op=city&city=$dest");
					}
				}
				addnav("Journey");
				require_once("modules/worldmapwn/lib/readmap.php");
				$map=worldmapwn_map_array($z);
				if ($map==false){
					output("`0You look out and see what looks like a strange building site. To your right there are what appear to be men in orange worksuits laying rocks, while to your left another appears to be creating a lake using a giant hosepipe. It does not look like something to travel across, but maybe later.");
					if ($session['user']['superuser']==true){
						output("`n`n1ops! There doesn't seem to be any map created. If you have created a map but are still seeing this message, make sure it is in modules/worldmapwn/maps and it is listed in the settings with a valid ID. Alternativly, the user has been told to open a mapid that isn't there.");}
					}
					page_footer();
					break;}
				$maxx=count($map)-4;
				$maxy=count($map[1]-2);//rectangular maps only, no jagged ones.
				
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
				
			case "admin":
				page_header("Worldmapwn Settings");		
				$canedit=get_module_pref("cannedit");
				if (httpget("admin"==true && $session['user']['superuser']==true && $canedit==true){
					output("`bMap File Settings`b");
					rawoutput("<form>");

					output("`bCity Locations`b");
					rawoutput("</form>");
				}				
				addnav("");
				addnav("X?Return to the Grotto","superuser.php");
				addnav("M?Return to the Mundane","village.php");
				page_footer();
			default:
				break;
			}


?>
