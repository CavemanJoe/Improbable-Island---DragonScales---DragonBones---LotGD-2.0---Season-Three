<?php

list($code1,$code2)=explode("^",$terraincode);
debug("The parts of the terraincode are $code1 and $code2.");
$image1=worldmapwn_image($code1,$terrainsinfo);

if ($code2){
	$image2=worldmapwn_image($code2,$terrainsinfo);}
debug("Image1 is $image1.");
debug("Image2 is $image2.");
			
//this is where the images are displayed
rawoutput("<div style=\" position:relative;\">");
rawoutput("<img style=\"position:absolute; top:35px; left:250px z-index:0;\" src=\"$image1\" alt=\"$currentloc\"/>");
		if ($image2!=false && $image2!=null){				
		rawoutput("<img style=\"position:absolute; top:35px; left:300px z-index:1;\" src=\"$image2\"/>");
		} else {
		debug("image2 fails");}
rawoutput("</div>");
?>
