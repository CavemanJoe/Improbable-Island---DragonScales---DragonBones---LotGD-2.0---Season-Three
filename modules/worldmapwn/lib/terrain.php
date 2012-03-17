<?php
//-----------------------------------------------------
//Returns the terrain code for a paticular hex.
//Coords must be in the format x,y,z.
//-----------------------------------------------------


function worldmapwn_terraincode_coords($coords,$map=false){
	require_once("modules/worldmapwn/lib/readmap.php");
	list($x,$y,$z)=explode(",",$coords);
	if ($map==false){$map=worldmapwn_map_array($z);}
	if ($map==false){return false;}
	$x=$x+4;
	debug("coords are $x,$y,$z");
	if ($map!=false){debug("Got map array for terrain coords");}
	//debug($map);
	//$y=$y;
	$code=$map[$x][$y];
	debug($code);
	return $code;
}

//-----------------------------------------------------
//Returns the terrain code for a paticular hex.
//Coords must be in the format $x, $y and $z and seperate arguments.
//-----------------------------------------------------

function worldmapwn_terraincodexyz($x,$y,$z=1,$map=false){
	if ($map==false){
	require_once("modules/worldmapwn/lib/readmap.php");
	$map=worldmapwn_map_array($z);}
	if ($map==false){return false;}
	$x=$x+4;
	$code=$map[$x][$y];
	return $code;
}

//----------------------------------------------------
// Returns the stamina cost for a paticular hex code
//
//----------------------------------------------------
function worldmapwn_staminacost($hexcode,$terrainsinfo=false){
	if ($terrainsinfo==false){
	require_once("modules/worldmapwn/config/terrain.php");
	$terrainsinfo=worldmapwn_terrainsinfo();
	}//global $terrainsinfo;
	list($code1,$code2)=explode("^",$hexcode);
	$terrain1=$terrainsinfo[$code1]["terraintype"];
	require_once("modules/staminasystem/lib/lib.php");
	$cost1=stamina_calculate_buffed_cost($terrain1);
	$terrain2=$terrainsinfo[$code2]["terraintype"];
	$cost2=stamina_calculate_buffed_cost($terrain2);
	$cost=($cost1+$cost2) / 2;
	return $cost;
}

function worldmapwn_image($hexcode,$terrainsinfo=false){
	if ($terrainsinfo==false){
	require_once("modules/worldmapwn/config/terrain.php");
	}	
	//global $terrainsinfo;
	//if ($hexcode=="Ft"){
	//debug("1,The hexcode really is Ft.");}
	//debug($terrainsinfo);
	//debug("hexcode for image is $hexcode.");
	$hexcode = str_replace(' ', '', $hexcode);
	//debug($hexcode);
//	if ($terrainsinfo /*&&/$terrainsinfo!=null*/)debug("terrainsinfo exists.");
	$image=$terrainsinfo["$hexcode"]['image'];
	//debug($terrainsinfo["$hexcode"]);
	//debug("Image is $image");
	$imageloc="modules/worldmapwn/images/" . $image . ".png";
	//debug($imageloc);
	return $imageloc;
}

?>
