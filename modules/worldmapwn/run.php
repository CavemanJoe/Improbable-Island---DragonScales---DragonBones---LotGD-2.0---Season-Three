<?php

function worldmapwn_run_real(){
	
	global $session
	
	switch (httpget("op"){
			global $session;
			case "beginjourney":break;
				
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
				switch (httpget("dir"){//This sets the users new location
					case "n":
						$start=$session['user']['location'];
						$startloc=explode(",",$start);
						$locchange=$startloc[2]-1;
						$newloc=$startloc[0].",".$locchange.",".$startloc[2];
						$session['user']['location']=$newloc;
						break;
					case "ne": break;
						$start=$session['user']['location'];
						$startloc=explode(",",$start);
						$locchangex=$startloc[0]-1;
						if ($starloc[0] % 2 ==0){
							$locchangey=$startloc[0];
						} else {
							$locchangey=$startloc[0]-1;
						}
						$newloc=$locchangex.",".$locchangey.",".$startloc[2];
						$session['user']['location']=$newloc;
					case "nw": break;
						$start=$session['user']['location'];
						$startloc=explode(",",$start);
						$locchangex=$startloc[0]+1;
						if ($starloc[0] % 2 ==0){
							$locchangey=$startloc[0];
						} else {
							$locchangey=$startloc[0]-1;
						}
						$newloc=$locchangex.",".$locchangey.",".$startloc[2];
						$session['user']['location']=$newloc;
					case "s":
						$start=$session['user']['location'];
						$startloc=explode(",",$start);
						$locchange=$startloc[2]+1;
						$newloc=$startloc[0].",".$startloc[1].",".$locchange;
						$session['user']['location']=$newloc;
						break;
					case "se": break;
						$start=$session['user']['location'];
						$startloc=explode(",",$start);
						$locchangex=$startloc[0]+1;
						if ($starloc[0] % 2 ==0){
							$locchangey=$startloc[0]+1;
						} else {
							$locchangey=$startloc[0];
						}
						$newloc=$locchangex.",".$locchangey.",".$startloc[2];
						$session['user']['location']=$newloc;
					case "sw": break;
						$start=$session['user']['location'];
						$startloc=explode(",",$start);
						$locchangex=$startloc[0]+1;
						if ($starloc[0] % 2 ==0){
							$locchangey=$startloc[0]+1;
						} else {
							$locchangey=$startloc[0];
						}
						$newloc=$locchangex.",".$locchangey.",".$startloc[2];
						$session['user']['location']=$newloc;
					case "begin":
						
						break;
					}

				$currentloc=$session['user']['location'];
				list($x,$y,$z)=explode(",",$currentloc);
				require_once("modules/worldmapwn/lib/readmap.php");
				$map=worldmapwn_map_array($z);
				$maxx=count($map)-4;
				$maxy=count($map[1]-2);//rectangular maps only, no jagged ones.
				addnav("Journey")
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
				
					
								
				

			default:
				break;
			}


?>
