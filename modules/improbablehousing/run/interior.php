<?php

global $session;
clear_module_pref("sleeping","improbablehousing");

$hid=httpget('hid');
$rid=httpget('rid');
if (!httpget('rid') || $rid==''){
	$rid = 0;
}
require_once "modules/improbablehousing/lib/lib.php";
$house=improbablehousing_gethousedata($hid);

//check to see if the player's acctid was in a sleeping spot, and if so, set the sleeping spot's occupier to zero
if (count($house['data']['rooms'])>0){
	foreach($house['data']['rooms'] AS $rkey=>$rvals){
		//debug($rvals);
		if (isset($rvals['sleepslots'])){
			//debug($rkey['sleepslots']);
			foreach($rvals['sleepslots'] AS $skey=>$svals){
				if ($svals['occupier']==$session['user']['acctid']){
					unset($house['data']['rooms'][$rkey]['sleepslots'][$skey]['occupier']);
				}
			}
		}
	}
}

if (!get_module_pref("superuser","improbablehousing")){
	//check to see if the player is registered as being in the room, and if not, set them
	if (isset($house['data']['rooms'][$rid]['occupants'])){
		$search = array_search($session['user']['name'],$house['data']['rooms'][$rid]['occupants']);
		if ($search===false){
			if (improbablehousing_canenter_room($house,$rid)){
				$house['data']['rooms'][$rid]['occupants'][$session['user']['acctid']]=$session['user']['name'];
			} else {
				redirect("runmodule.php?module=improbablehousing&op=lockbounce&hid=$hid");
			}
		}
		//unset them from other rooms
		foreach($house['data']['rooms'] AS $rkey=>$rvals){
			if (isset($rvals['occupants'])){
				foreach($rvals['occupants'] AS $okey=>$ovals){
					if ($okey == $session['user']['acctid'] && $rkey!=$rid){
						unset($house['data']['rooms'][$rkey]['occupants'][$okey]);
					}
				}
			}
		}
	} else {
		if (isset($house['data']['rooms'][$rid])){
			$house['data']['rooms'][$rid]['occupants']=array();
		}
	}
}

improbablehousing_sethousedata($house);

$housename = $house['data']['name'];
$roomname = $house['data']['rooms'][$rid]['name'];
$session['user']['location'] = "House: ".$housename.", Room ".$rid."";
page_header("%s: %s",$housename,$roomname);

// debug($house);

$subop=httpget('subop');
if (!$subop){
	//interior description
	if (isset($house['data']['rooms'][$rid]['name'])){
		output_notl("`0%s`0`n`n",$house['data']['rooms'][$rid]['desc']);
		
		foreach($house['data']['rooms'] AS $rkey=>$rvals){
			//show room links and description
			if ($rkey!=$rid){
				addnav("Explore");
				addnav("Return");
				if ($rvals['enterfrom']==$rid && (!$rvals['hidden'] || improbablehousing_getkeytype($house,$rid)>=100)){
					addnav("Explore");
					if (improbablehousing_canenter_room($house,$rkey)){
						if (!$rvals['hidden']){
							if ($rvals['locked']){
								addnav(array("%s (locked)",$rvals['name']),"runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=$rkey");
							} else {
								addnav(array("%s",$rvals['name']),"runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=$rkey");
							}
						} else {
							if ($rvals['locked']){
								addnav(array("%s (locked, secret)",$rvals['name']),"runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=$rkey");
							} else {
								addnav(array("%s (secret)",$rvals['name']),"runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=$rkey");
							}
						}
					} else {
						addnav(array("Can't enter %s",$rvals['name']),"");
					}
				} else if ($rkey==$house['data']['rooms'][$rid]['enterfrom']){
					addnav("Return");
					if (improbablehousing_canenter_room($house,$rkey)){
						if ($rvals['locked']){
							addnav(array("%s (locked)",$rvals['name']),"runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=$rkey");
						} else {
							addnav(array("%s",$rvals['name']),"runmodule.php?module=improbablehousing&op=interior&hid=$hid&rid=$rkey");
						}
					} else {
						addnav(array("Can't enter %s",$rvals['name']),"");
					}
				}
			}
		}
		
		improbablehousing_sleeplinks($house,$rid);
		
		if (improbablehousing_getkeytype($house,$rid)>=20){
			//handle normal locks
			addnav("Locks");
			if ($rid!=0){
				if ($house['data']['rooms'][$rid]['locked']){
					if ($house['data']['rooms'][$rid]['locked']==1){
						addnav("This room is `\$locked`0","");
						if (improbablehousing_getkeytype($house,$rid)>=30){
							addnav("Deadbolt this room","runmodule.php?module=improbablehousing&op=locks&toggle=$rid&hid=$hid&rid=$rid&deadbolt=lock");
						}
						addnav("Unlock this room","runmodule.php?module=improbablehousing&op=locks&toggle=$rid&hid=$hid&rid=$rid");
					} else if ($house['data']['rooms'][$rid]['locked']==2){
						addnav("This room is `\$deadbolted.`0","");
						if (improbablehousing_getkeytype($house,$rid)>=30){
							addnav("Open the deadbolt","runmodule.php?module=improbablehousing&op=locks&toggle=$rid&hid=$hid&rid=$rid&deadbolt=unlock");
						}
					}
				} else {
					addnav("This room is `@unlocked`0","");
					addnav("Lock this room","runmodule.php?module=improbablehousing&op=locks&toggle=$rid&hid=$hid&rid=$rid");
					if (improbablehousing_getkeytype($house,$rid)>=30){
						addnav("Deadbolt this room","runmodule.php?module=improbablehousing&op=locks&toggle=$rid&hid=$hid&rid=$rid&deadbolt=lock");
					}
				}
			}
		}
		
		improbablehousing_show_decorating_jobs($house);
		
		if (improbablehousing_getkeytype($house,$rid)>=100){
			//house owner / master keyholder "manage dwelling" links
			improbablehousing_new_build_jobs($house,$rid);
			addnav("Decorations");
			addnav("Decorate this room","runmodule.php?module=improbablehousing&op=decorate&subop=start&hid=$hid&rid=$rid");
			if ($rid==0){
				addnav("Decorate this Dwelling's exterior","runmodule.php?module=improbablehousing&op=decorate&subop=extdesc&hid=$hid");
			}
		}
		if (improbablehousing_getkeytype($house,$rid)>=100){
			if ($rid==0){
				if ($house['data']['locked']){
					addnav("This Dwelling is `\$locked`0","");
					addnav("Unlock this Dwelling","runmodule.php?module=improbablehousing&op=locks&toggle=master&hid=$hid&rid=$rid");
				} else {
					addnav("This Dwelling is `@unlocked`0","");
					addnav("Lock this Dwelling","runmodule.php?module=improbablehousing&op=locks&toggle=master&hid=$hid&rid=$rid");
				}
			}
			addnav("Manage Keys","runmodule.php?module=improbablehousing&op=keys&sub=start&hid=$hid&rid=$rid");
		}
		//remove this before going live!
		//addnav("Cheat","runmodule.php?module=improbablehousing&op=cheat&hid=$hid&rid=$rid");
	} else {
		output("This dwelling is not yet completed, and doesn't have any rooms to speak of.`n`n");
	}
	
	improbablehousing_show_build_jobs($house,$rid);
	addnav("Building Materials");
	addnav("Examine materials stock","runmodule.php?module=improbablehousing&op=store&sub=start&hid=$hid&rid=$rid");
	improbablehousing_list_occupants($house,$rid);
	//Handle interior nav links in a function (kitchens etc)
	$hook = array(
		"hid"=>$hid,
		"rid"=>$rid,
		"house"=>$house,
	);
	$hook = modulehook("improbablehousing_interior",$hook);
	$house = $hook['house'];
	if (!$house['data']['rooms'][$rid]['blockchat']){
		require_once("lib/commentary.php");
		addcommentary();
		if (improbablehousing_getkeytype($house,$rid)>=100){
			viewcommentary("dwelling-".$hid."-".$rid,"Chat with others in this room",25,"says",false,false,false,false,true);
		} else {
			viewcommentary("dwelling-".$hid."-".$rid,"Chat with others in this room");
		}
	}
}

if ($rid!=0){
	improbablehousing_bottomnavs($house);
} else {
	addnav("Leave");
	addnav("M?Back to the Island Map","runmodule.php?module=improbablehousing&op=exit&hid=$hid");
}
page_footer();

?>