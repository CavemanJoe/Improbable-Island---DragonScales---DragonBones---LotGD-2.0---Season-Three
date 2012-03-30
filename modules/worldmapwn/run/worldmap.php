<?php
page_header("World Map");
addnav("Continue traveling","runmodule.php?module=worldmapwn&op=travel");

require_once("modules/worldmapwn/lib/readmap.php");
require_once("modules/worldmapwn/lib/terrain.php");
require_once("modules/worldmapwn/config/terrain.php");
$terrainsinfo=worldmapwn_terrainsinfo();
list($x,$y,$z)=explode(",",$currentloc);
$map=worldmapwn_map_array($z);
if ($map==false){
	output("You look at your map, but all you see is blank space How strange.");
	
}
$rownum=0;
require_once("modules/worldmapwn/config/terrain.php");
rawoutput("<div style=\" position:relative;\">");
foreach($map as $rownum=>$rows){
	if ($rownum<3){continue;}
	//if ($rownum<30||$rownum>75){continue;}
	$ycord=$rownum;
	foreach ($rows as $colnum=>$hex){
		//if ($colnum<25||$colnum>75){continue;}
		$xcord=$colnum;
		//debug("rownum is $rownum");
		$hexcode=worldmapwn_terraincodexyz($rownum,$colnum,$z,$map);
		//debug("hexcode is $hexcode");
		list($code1,$code2)=explode("^",$hexcode);
		//debug("At $rownum,$colnum code2 is $code2.");
		//d ebug("The parts of the terraincode are $code1 and $code2.");
		$image1=worldmapwn_image($code1,$terrainsinfo);			
		if ($code2){
			$image2=worldmapwn_image($code2,$terrainsinfo);
		}

		$size=36;
		$mapfactor=$size/72;
		if ($colnum % 2 ==0){
		$toplevel=($rownum*72-100)*$mapfactor+144;
		$leftlevel=$colnum*54*$mapfactor;
		} else {
		$toplevel=($rownum*72-100-36)*$mapfactor+144;
		$leftlevel=$colnum*54*$mapfactor;
		}


		$basedir="modules/worldmapwn/images";
		if ($rownum==3 && ($colnum % 2 ==1)){
		//rawoutput("<img style=\"position:absolute; top:0px; left:".$leftlevel."px; z-index:0;\" src=\"".$basedir."/off-map/border-nw-n-ne.png\" />");
		}
		if ($colnum==0){
		$voidtop=$toplevel+(36*$mapfactor);
		$voidleft=$leftlevel-(36*$mapfactor);
		rawoutput("<img style=\"position:absolute; top:".$voidtop."px; left:".$voidleft."px; z-index:0;\" src=\"".$basedir."/off-map/border-sw-nw.png\" height=\"$size\" width=\"$size\"/>");
		}
		//debug($leftlevel);
		rawoutput("<img style=\"position:absolute; top:".$toplevel."px; left:".$leftlevel."px; z-index:0;\" src=\"$image1\" title=\"$xcord,$yord,$z\" height=\"$size\" width=\"$size\" />");
		//rawoutput("<img style=\"position:absolute; top:".$toplevel."px; left:".$leftlevel."px; z-index:0;\" src=\"modules/worldmapwn/images/light.png\" alt=\"$currentloc\"/>");
		if ($image2!=false && $image2!=null){				
			rawoutput("<img style=\"position:absolute; top:".$toplevel."px; left:".$leftlevel."px; z-index:1;\" src=\"$image2\"/ title=\"$colnum,$rownum,$z\" height=\"$size\" width=\"$size\" />");
		}
		if ($x==$xcord && $y==$ycord){
			$imageloc=$basedir."/player/duelist.png";
			rawoutput("<img style=\"position:absolute; top:".$toplevel."px; left:".$leftlevel."px; z-index:5;\" src=\"$imageloc\" title=\"$colnum,$rownum,$z\"height=\"$size\" width=\"$size\" />");
		}
		$image2=false;//clears image2 for next time
	}
}
rawoutput("</div>");
page_footer();
?>
