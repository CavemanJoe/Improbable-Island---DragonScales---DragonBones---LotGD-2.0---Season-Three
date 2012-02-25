<?php

//----------------------------------------------------
//Returns an array with the terrain codes for the 
//surronding hexes to $location
//----------------------------------------------------

//NOTE: Need to fix ne/nw/se/sw x & y changes
function worldmapwn_surround($location=false,$map=false){
	if ($location=false){$location=$session['user']['location'];}
	list($x,$y,$z)=explode(",",$location);
	if ($map==false){$map=worldmapwn_map_array($z);}
	$maxx=count($map)-4;
	$maxy=count($map[1]-2);//rectangular maps only, no jagged ones.				
	if ($y!=1){
		$newy=$y-1;
		$surrond["n"]=$map[$x][$newy+2];					
		if ($x!=1){
			$newx=$x-1;
			if ($y % 2 ==0){
				$newy=$y;
			} else {
				$newy=$y-1;
			}
			$surrond["nw"]=$map[$newx-1][$newy+2];
		}
		if ($x!=$maxx){
			$newx=$x+1;
			if ($y % 2 ==0){
				$newy=$y;
			} else {
				$newy=$y-1;
			}
			$surrond["ne"]=$map[$newx-1][$newy+2];
			addnav("Travel North-East","runmodule.php?module=worldmapwn&op=travel&dir=ne");}
	} else {
		$surrond["n"]="X";
		if (($x % 2)==0){
			$surrond["nw"]="X";
			$surrond["ne"]="X";
		}
	}
	if ($y!=$maxy){
		$newy=$y+1;
		$surrond["s"]=$map[$x][$newy+2];				
		if ($x!=1){
			addnav("Travel South-West","runmodule.php?module=worldmapwn&op=travel&dir=sw");}
		if ($x!=$maxx){
			addnav("Travel South-East","runmodule.php?module=worldmapwn&op=travel&dir=se");}
	}else {
		$surrond["s"]="X";
		if (($x % 2)==1){
			$surrond["sw"]="X";
			$surrond["se"]="X";
		}
	}

	return $surrond;
}

//-----------------------------------------------------
//Returns an array containing the world map, in the form
//of $array[x][y], with the co-ordinates (1,1) equal to
//$array[4][1]. If the map is not found it returns false.
//-----------------------------------------------------

function worldmapwn_map_array($mapid=1){
	$mapfilearray=get_module_setting("maps");
	$maploc=$mapfilearray[$mapid]["location"];
	//$maploc="improbableisland";
	$maploca="modules/worldmapwn/maps/". $maploc;
	//$mapopen=fopen($maploca,"r");
	$mapopen=fopen("maps/improbableisland.map","r");
	if ($mapopen=false){
		output("You look out and see what looks like a strange building site. To your right there are what appear to be men in orange worksuits laying rocks, while to your left another appears to be creating a lake using a giant hosepipe. It does not look like something to travel across, but maybe later.");
		if ($session['user']['superuser']==true){
			output("`nOops! There doesn't seem to be any map created. If you have created a map but are still seeing this message, make sure it is in modules/worldmapwn/maps and it is listed in the settings with a valid ID. Alternativly, the user has been told to open a mapid that isn't there.");}
		}
		return false;
	else {
		
				
		fclose($maploca);		
		
	$values = array();
	foreach(file($maploca) as $line => $content) {
		$values[$line] = explode(',',$content);
	}
	return $values
	}
}



?>
