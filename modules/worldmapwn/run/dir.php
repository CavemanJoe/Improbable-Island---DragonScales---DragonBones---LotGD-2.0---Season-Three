<?php
switch (httpget("dir")){//This sets the users new location

					case "setloc":
						$loc=httpget('loc');
						$session['user']['location']=$loc;
					case "n":
						$start=$session['user']['location'];
						list($x,$y,$z)=explode(",",$start);
						$locchange=$y-1;
						$newloc=$x.",".$locchange.",".$z;
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

						require_once("lib/cityprefs.php");
						$cid = get_cityprefs_cityid("location",$session['user']['location']);
						debug($cid);
						$cname=$session['user']['location'];
						debug($cname);
						$cityloc = get_module_objpref("city",$cid,"worldXYZ");
						debug($cityloc);
						$session['user']['location']=$cityloc;
						$outmess=e_rand(1,5);
						switch ($outmess){
							case 1:output("`b`&The gates of %s close behind you. A shiver runs down your back as you face the wilderness around you.`0`b",$cname);break;
							case 2:output("`b`&The gates of %s close behind you. You're all alone now...`0`b",$cname);break;
							case 3:output("`b`&The gates of %s close behind you. The sound of the wilderness settles in around you as you think to yourself what evil must lurk within.`0`b",$cname);break;
							case 4:output("`b`&The gates of %s close behind you. Perhaps you should go back in...`0`b",$cname);break;
							case 5:output("`b`&The gates of %s close behind you. A howling noise bellows from deep within the forest.  You hear the guards from the other side of the gates yell \"Good Luck!\" and what sounds like \"they'll never make it.`0`b",$cname);break;

						}
						modulehook("worldmapwn-travel");
						break;
					}


?>
