<?php

function worldmapwn_readmap(){

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
	else{
		
				
		fclose($maploca);		
		
	$values = array();
	foreach(file($maploca) as $line => $content) {
		$values[$line] = explode(',',$content);
	}
	return $values
	}
}



?>
