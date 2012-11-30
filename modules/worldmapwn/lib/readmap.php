<?php

//----------------------------------------------------
//Returns an array with the terrain codes for the 
//surronding hexes to $location
//----------------------------------------------------

function worldmapwn_surround($location=false,$map=false){
	global $session;
	if ($location=false){$location=$session['user']['location'];}
	list($x,$y,$z)=explode(",",$location);
	if ($map==false){$map=worldmapwn_map_array($z);}
	if ($map==false){//maparray failed to return
	$surrond=array("nw"=>"X","n"=>"X","ne"=>"X","se"=>"X","s"=>"X","sw"=>"X");//no map avaliable for that z, then all is impassable
	return $surround;}

	$maxx=count($map)-4;
	$maxy=count($map[1])-1;//rectangular maps only, no jagged ones.				
	if ($y!=1){
		$newy=$y-1;
		$surrond["n"]=$map[$x][$newy+2];					
		if ($x!=1){
			$newx=$x-1;
			if ($x % 2 ==0){
				$newy=$y;
			} else {
				$newy=$y-1;
			}
			$surrond["nw"]=$map[$newx-1][$newy+2];
		} else {$surrond["nw"]="X";}
		if ($x!=$maxx){
			$newx=$x+1;
			if ($x % 2 == 0){
				$newy=$y;
			} else {
				$newy=$y-1;
			}
			$surrond["ne"]=$map[$newx-1][$newy+2];
			addnav("Travel North-East","runmodule.php?module=worldmapwn&op=travel&dir=ne");
		}else {$surrond["ne"]="X";}
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
			$newx=$x-1;
			if ($x % 2 == 1){
				$newy=$y;
			} else {
				$newy=$y-1;
			}
			$surrond["sw"]=$map[$newx-1][$newy+2];
		} else {$surrond["sw"]="X";}
		if ($x!=$maxx){

			$newx=$x+1;
			if ($x % 2 == 1){
				$newy=$y;
			} else {
				$newy=$y-1;
			}
			$surrond["se"]=$map[$newx-1][$newy+2];
		} else {$surrond["se"]="X";}
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
//	END - worldmapwn_surrond
//-----------------------------------------------------
//
//-----------------------------------------------------
//	BEGIN - worldmapwn_map_array
//Returns an array containing the world map, in the form
//of $array[x][y], with the co-ordinates (1,1) equal to
//$array[4][1]. If the map is not found it returns false.
//-----------------------------------------------------

function worldmapwn_map_array($mapid=1){
	//$mapfilearray=get_module_setting("maps");
	//$mapfiles=unserialise($mapfilearray))
	//$maploc=$mapfilearray[$mapid]["location"];
	$maploc="improbableisland";
	$maploca="modules/worldmapwn/maps/". $maploc;
	//$mapopen=fopen($maploca,"r");
	$mapopen=fopen($maploca,"r");
	if ($mapopen==false){
		return false;
	}
			
				
				
		
	$values = array();
	$line=0;
	while($content=fgets($maploca)) {
		$values[$line] = explode(',',$content);
		$line++;
	}
	

	fclose($mapopen);

	return $values;
	
}

//-----------------------------------------------------
//	END - worldmapwn_map_array
//-----------------------------------------------------

?>
