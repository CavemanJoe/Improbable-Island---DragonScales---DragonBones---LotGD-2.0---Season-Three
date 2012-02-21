<?php

//-----------------------------------------------------
//Returns the terrain code for a paticular hex.
//Coords must be in the format x,y,z.
//-----------------------------------------------------

function worldmapwn_terraincode_coords($coords){
	require_once("modules/worldmapwn/lib/readmap.php");
	$loc=explode(",",$coords);
	$map=worldmapwn_map_array($loc[2]);
	if ($map==false){return false;}
	$x=$loc[0]+4;
	$y=$loc[1];
	$code=$map[$x][$y];
	return $code;
}

//-----------------------------------------------------
//Returns the terrain code for a paticular hex.
//Coords must be in the format $x, $y and $z and seperate arguments.
//-----------------------------------------------------

function worldmapwn_terraincodexyz($x,$y,$z=1){
	require_once("modules/worldmapwn/lib/readmap.php");
	$map=worldmapwn_map_array($z)
	if ($map==false){return false;}
	$x=$x+4;
	$code=$map[$x][$y];
	return $code;
}

?>
