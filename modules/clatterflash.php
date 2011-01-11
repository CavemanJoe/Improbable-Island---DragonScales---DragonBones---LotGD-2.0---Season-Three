<?php

function clatterflash_getmoduleinfo(){
	$info = array(
		"name"=>"Clatter - Flash Version",
		"version"=>"2010-05-26",
		"author"=>"Dan Hall",
		"category"=>"Req Gambling",
		"download"=>"",
		"prefs"=>array(
			"lit"=>"Player's lights array,text|array()",
		),
		"settings"=>array(
			"played"=>"Total coins played,int|0",
			"won"=>"Total coins won,int|0",
		)
	);
	return $info;
}
function clatterflash_install(){
	return true;
}
function clatterflash_uninstall(){
	return true;
}
function clatterflash_dohook($hookname,$args){
	global $session;
	return $args;
}
function clatterflash_run(){
	global $session, $board, $flashvar, $boarddefinition;
	$boarddefinition=array(
		"startcoin0" => array(
			1 => 4,
			0 => 3,
		),
		"startcoin1" => array(
			1 => 5,
			0 => 4,
		),
		1 => array(
			1 => 8,
			0 => 8,
		),
		2 => array(
			1 => 9,
			0 => 8,
		),
		3 => array(
			1 => 10,
			0 => 9,
		),
		4 => array(
			1 => 11,
			0 => 10,
		),
		5 => array(
			1 => 12,
			0 => 11,
		),
		6 => array(
			1 => 13,
			0 => 12,
		),
		7 => array(
			1 => 13,
			0 => 13,
		),
		8 => array(
			1 => 15,
			0 => 14,
		),
		9 => array(
			1 => 16,
			0 => 15,
		),
		10 => array(
			1 => 17,
			0 => 16,
		),
		11 => array(
			1 => 18,
			0 => 17,
		),
		12 => array(
			1 => 19,
			0 => 18,
		),
		13 => array(
			1 => 20,
			0 => 19,
		),
		14 => array(
			1 => 21,
			0 => 21,
		),
		15 => array(
			1 => 22,
			0 => 21,
		),
		16 => array(
			1 => 23,
			0 => 22,
		),
		17 => array(
			1 => 24,
			0 => 23,
		),
		18 => array(
			1 => 25,
			0 => 24,
		),
		19 => array(
			1 => 26,
			0 => 25,
		),
		20 => array(
			1 => 26,
			0 => 26,
		),
		21 => array(
			1 => 28,
			0 => 27,
		),
		22 => array(
			1 => 29,
			0 => 28,
		),
		23 => array(
			1 => 30,
			0 => 29,
		),
		24 => array(
			1 => 31,
			0 => 30,
		),
		25 => array(
			1 => 32,
			0 => 31,
		),
		26 => array(
			1 => 33,
			0 => 32,
		),
		27 => array(
			1 => 34,
			0 => 34,
		),
		28 => array(
			1 => 35,
			0 => 34,
		),
		29 => array(
			1 => 36,
			0 => 35,
		),
		30 => array(
			1 => 37,
			0 => 36,
		),
		31 => array(
			1 => 38,
			0 => 37,
		),
		32 => array(
			1 => 39,
			0 => 38,
		),
		33 => array(
			1 => 39,
			0 => 39,
		),
		34 => array(
			1 => 0, //put these in later
			0 => 0,
		),
		35 => array(
			1 => 0,
			0 => 0,
		),
		36 => array(
			1 => 0,
			0 => 0,
		),
		37 => array(
			1 => 0,
			0 => 0,
		),
		38 => array(
			1 => 0,
			0 => 0,
		),
		39 => array(
			1 => 0,
			0 => 0,
		),
	);
	
	if (httpget('op')=="play"){
		page_header("Clatter!");
		$session['user']['gold']-=20;
		$woncoins=0;
		
		//New Flash clatter stuff
		
		$lit=unserialize(get_module_pref("lit","clatter"));
		if (!isset($lit[1])){
			$lit = array();
			$lit[1] = 0;
			$lit[2] = 0;
			$lit[3] = 0;
			$lit[4] = 0;
			$lit[5] = 0;
			$lit[6] = 0;
			$lit[7] = 0;
		}
		
		$coinstoplay=1;
		$coinsinplay=1;
		$tick=1;
		$flashvar="";
		if ($lit[1]) $flashvar.="&lamp1start=on";
		if ($lit[2]) $flashvar.="&lamp2start=on";
		if ($lit[3]) $flashvar.="&lamp3start=on";
		$board = array();
		
		while ($coinsinplay>0){
			$flashvar.="&tick".$tick."=";
			if ($coinstoplay){
				$coinstoplay--;
				//debug($coinstoplay);
				$coinsinplay++;
				$start = mt_rand(0,1);
				if ($start){
					$board["startcoin0"] = 1;
				} else {
					$board["startcoin1"] = 1;
				}
			}
			$coinsinplay = 0;
			$newboard=array();
			$tick++;
			foreach($board AS $position=>$value){
				if ($position == 15 || $position == 19){
					//1-coin bonus switch
					if ($lit[1]){
						$coinstoplay+=1;
						$coinsinplay+=1;
						$lit[3]=1;
						$lit[1]=0;
						$flashvar.="lamp3on|lamp1off|";
					} else {
						$lit[1]=1;
						$flashvar.="lamp1on|";
					}
				}
				if ($position == 30){
					//3-coin bonus switch
					if ($lit[3]){
						$coinstoplay+=3;
						$coinsinplay+=3;
						$lit[2]=1;
						$lit[3]=0;
						$flashvar.="lamp2on|lamp3off|";
					} else {
						$lit[3]=1;
						$flashvar.="lamp3on|";
					}
				}
				if (!$position) $coinsinplay--;
				if ($board[$position]==1){
					$board[$position]=0;
					$coinsinplay++;
					if (e_rand(0,1)){
						if ($position==34){
							//2-coin bonus switch
							if ($lit[2]){
								$coinstoplay+=2;
								$coinsinplay+=2;
								$lit[1]=1;
								$lit[2]=0;
								$flashvar.="lamp1on|lamp2off|";
							} else {
								$lit[2]=1;
								$flashvar.="lamp1on|";
							}
						} else if ($position==36){
							$woncoins++;
						}
						$flashvar.="coin".$position."l|";
						$newboard[$boarddefinition[$position][0]]=1;
					} else {
						if ($position==39){
							//2-coin bonus switch
							if ($lit[2]){
								$coinstoplay+=2;
								$coinsinplay+=2;
								$lit[1]=1;
								$lit[2]=0;
								$flashvar.="lamp1on|lamp2off|";
							} else {
								$lit[2]=1;
								$flashvar.="lamp1on|";
							}
						} else if ($position==37){
							$woncoins++;
						}
						$newboard[$boarddefinition[$position][1]]=1;
						$flashvar.="coin".$position."r|";
					}
				}
			}
			//debug($coinsinplay);
			$board=$newboard;
		}
		debug($flashvar);
		rawoutput("
		<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0\" width=\"350\" height=\"368\" id=\"clatterflash\" align=\"middle\">

<param name=\"allowScriptAccess\" value=\"sameDomain\" />
<param name=\"flashvars\" value=\"".$flashvar."\" />
<param name=\"movie\" value=\"modules/clatter/clatterflash.swf\" /><param name=\"quality\" value=\"high\" /><param name=\"bgcolor\" value=\"#ffffff\" /><embed src=\"modules/clatter/clatterflash.swf\" quality=\"high\" bgcolor=\"#ffffff\" width=\"350\" height=\"368\" name=\"clatterflash\" align=\"middle\" allowScriptAccess=\"sameDomain\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" flashvars=\"".$flashvar."\"/>

</object>
		");
		set_module_pref("lit",serialize($lit),"clatter");
		$winnings = $woncoins*20;
		$session['user']['gold']+=$winnings;
		
		// $lit=unserialize(get_module_pref("lit"));
		// if (!isset($lit[1])){
			// $lit = array();
			// $lit[1] = 0;
			// $lit[2] = 0;
			// $lit[3] = 0;
			// $lit[4] = 0;
			// $lit[5] = 0;
			// $lit[6] = 0;
			// $lit[7] = 0;
		// }
		// $coinsinplay=1;
		// $totalcoinsplayed=0;
		// output("You plonk down twenty Requisition tokens onto the bar, and blink.  A single Clatter token sits in a beery puddle.  You pick it up and put it into the slot on the top of the machine.`n`n");
		// increment_module_setting("played");
		// while ($coinsinplay>0){
			// $msg="";
			// $coinsinplay--;
			// $totalcoinsplayed++;
			// $start = mt_rand(0,1);
			// if ($start){
				// $coinposition = 6;
			// } else {
				// $coinposition = 8;
			// }
			
			// // output("Dropping coin %s`n",$totalcoinsplayed);
			// rawoutput("<table width=100% cellpadding=0 cellspacing=2><tr><td>");

			// $bonus=0;
			// for ($i=1;$i<=7;$i++){
				// if (mt_rand(0,1)){
					// $coinposition++;
				// } else {
					// $coinposition--;
				// }
				
				// //bounce coins off edges
				// if ($coinposition>13){
					// $coinposition=12;
				// }
				// if ($coinposition<1){
					// $coinposition=2;
				// }
				
				// //award bonuses
				// if ($i==3 && ($coinposition==3 || $coinposition==11)){
					// $bonus=1;
					// if ($lit[3]){
						// $coinsinplay+=1;
						// $lit[5]=1;
						// $lit[3]=0;
					// } else {
						// $lit[3]=1;
					// }
				// } else if (($i==5 && $coinposition==7)){
					// $bonus=1;
					// if ($lit[5]){
						// $coinsinplay+=3;
						// $lit[7]=1;
						// $lit[5]=0;
					// } else {
						// $lit[5]=1;
					// }
				// } else if (($i==7 && ($coinposition==1 || $coinposition==13))){
					// $bonus=1;
					// if ($lit[7]){
						// $coinsinplay+=2;
						// $lit[3]=1;
						// $lit[7]=0;
					// } else {
						// $lit[7]=1;
					// }
				// }
				// //output image
				// rawoutput("<img src=\"images/clatterflash/".$i."-".$coinposition."-".$lit[$i].".jpg\"><br />");
			// }// end individual coin drop
			// if ($coinposition==7 && $bonus){
				// $woncoins++;
				// increment_module_setting("won");
			// } else if ($coinposition==7){
				// $woncoins++;
				// increment_module_setting("won");
			// }
			// rawoutput("</td><td>");
			// output("%s`n`n",$msg);
			// rawoutput("</td></tr></table>");
		// } // end page load
		// set_module_pref("lit",serialize($lit));
		// $winnings = $woncoins*20;
		// $session['user']['gold']+=$winnings;
		// if ($winnings){
			// output("`nYou have won a total of %s Requisition tokens this time, minus your 20 Requisition stake.",$winnings);
			// if ($winnings==20){
				// output("  In other words, you broke even.  Huzzah!");
			// }
		// } else {
			// output("`nYou didn't win anything this time.");
		//}
	}
	
	addnav("Clatter");
	if ($session['user']['gold']>=20){
		addnav("Play Clatter","runmodule.php?module=clatterflash&op=play");
		addnav("Play Textual version","runmodule.php?module=clatter&op=play");
	} else {
		addnav("You can't afford to gamble right now.","");
	}
	addnav("Walk Away","inn.php");
	page_footer();
}
?>