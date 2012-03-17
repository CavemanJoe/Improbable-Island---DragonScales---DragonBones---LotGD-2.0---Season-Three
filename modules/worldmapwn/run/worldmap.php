<?php
page_header("World Map");
addnav("Continue traveling","runmodule.php?module=worldmapwn&op=travel");

require_once("modules/worldmapwn/lib/readmap.php");
require_once("modules/worldmapwn/lib/terrain.php");
require_once("modules/worldmapwn/config/terrain.php");
$terrainsinfo=worldmapwn_terrainsinfo();
list($x,$y,$z)=explode(",",$currentloc);
$map=worldmapwn_map_array($z);
$rownum=0;
require_once("modules/worldmapwn/config/terrain.php");
rawoutput("<div style=\" position:relative;\">");
foreach($map as $rownum=>$rows){
	if ($rownum<3){continue;}
	foreach ($rows as $colnum=>$hex){
		//debug("rownum is $rownum");
		$hexcode=worldmapwn_terraincodexyz($rownum,$colnum,$z,$map);
		//debug("hexcode is $hexcode");
		list($code1,$code2)=explode("^",$hexcode);
		//debug("At $rownum,$colnum code2 is $code2.");
		//debug("The parts of the terraincode are $code1 and $code2.");
		$image1=worldmapwn_image($code1,$terrainsinfo);			
		if ($code2){
			$image2=worldmapwn_image($code2,$terrainsinfo);
		}
		$toplevel=$rownum*72;
		$leftlevel=$colnum*72;
		//debug($leftlevel);
		rawoutput("<img style=\"position:absolute; top:".$toplevel."px; left:".$leftlevel."px; z-index:0;\" src=\"$image1\" alt=\"$rownum,$colnum,$z\" />");
		//rawoutput("<img style=\"position:absolute; top:".$toplevel."px; left:".$leftlevel."px; z-index:0;\" src=\"modules/worldmapwn/images/light.png\" alt=\"$currentloc\"/>");
		if ($image2!=false && $image2!=null){				
			rawoutput("<img style=\"position:absolute; top:".$toplevel."px; left:".$leftlevel."px; z-index:1;\" src=\"$image2\"/ alt=\"$rownum,$colnum,$z\">");
		}
		if ($x==$rownum && $y==$colnum){
			$imageloc="modules/worldmapwn/images/player/duelist.png";
			rawoutput("<img style=\"position:absolute; top:".$toplevel."px; left:".$leftlevel."px; z-index:1;\" src=\"$imageloc\" alt=\"$rownum,$colnum,$z\"/>");
		}
		$image2=false;//clears image2 for next time
	}
}
rawoutput("</div>");
page_footer();
?>
