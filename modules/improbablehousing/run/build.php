<?php

$hid = httpget('hid');
$job = httpget('job');
$subjob = httpget('subjob');
$store = httpget('store');

page_header("Let's build!");

require_once "modules/improbablehousing/lib/lib.php";
require_once "modules/staminasystem/lib/lib.php";
$house=improbablehousing_gethousedata($hid);

$jobtoperform = $house['data']['buildjobs'][$job]['jobs'][$subjob];
if (!$jobtoperform){
	output("Before you can get started, you realise someone has already finished the job for you!`n`n");
	improbablehousing_bottomnavs($house,$rid);
	page_footer();
	break;
} else {
	$iitems = $jobtoperform['iitems'];
	$actions = $jobtoperform['actions'];

	foreach($iitems AS $iitem=>$qty){
		for ($i=0; $i<$qty; $i++){
			if ($store && $iitem!="toolbox_masonry" && $iitem!="toolbox_carpentry" && $iitem!="toolbox_decorating"){
				//use iitems from the Dwelling's storeroom
				if ($house['data']['store'][$iitem]>0){
					$house['data']['store'][$iitem]--;
					if ($house['data']['store'][$iitem]){
						output("You decide to use the materials that are already hanging around.  There's still %s of these left.`n`n",$house['data']['store'][$iitem]);
					} else {
						output("You decide to use the materials that are already hanging around.  That was the last one.`n`n");
					}
				} else {
					output("Before you can get started, you realise someone has already depleted the materials store!`n`n");
					improbablehousing_bottomnavs($house,$rid);
					page_footer();
					$interrupt = true;
				}
			} else {
				//determine iitems to use, and use them - assume we're using iitems from the Main inventory
				use_item($iitem);
				if ($iitem != "toolbox_carpentry" && $iitem != "toolbox_masonry"){
					$qleft = has_item_quantity($iitem);
					if ($qleft == 1){
						$iname = get_item_setting("verbosename",$iitem);
					} else {
						$iname = get_item_setting("plural",$iitem);
					}
					if (!$qleft){
						$qleft = "no";
					}
					output("You have %s %s left.`n`n",$qleft,$iname);
				}
			}
		}
	}
	
	if ($interrupt){
		break;
	}

	foreach($actions AS $action=>$reps){
		output_notl("%s`n`n",$house['data']['buildjobs'][$job]['jobs'][$subjob]['desc']);
		for ($i=0; $i<$reps; $i++){
			$actinfo = process_action($action);
			if ($actinfo['lvlinfo']['levelledup']){
				output("`n`c`b`0You gained a level in %s!  You are now level %s!  This action will cost fewer Stamina points now.`b`c`n",$action,$actinfo['lvlinfo']['newlvl']);
			}
		}
	}

	//now update the job counter, add our name to the Helpers list and show how many more times we must do this
	$jobtoperform['done']+=1;

	//check to see if the job has been done
	if ($jobtoperform['done']>=$jobtoperform['req']){
		$jobtoperform['completed']=1;
		$house['data']['buildjobs'][$job]['jobs'][$subjob]=$jobtoperform;
		//now check to see if all jobs have been done
		$finished_subjobs = 1;
		foreach($house['data']['buildjobs'][$job]['jobs'] AS $key=>$vals){
			if (!$vals['completed']){
				debug("Job not completed");
				$finished_subjobs = 0;
			}
		}
		if ($finished_subjobs){
			$house['data']['buildjobs'][$job]['completed']=1;
			debug("All jobs completed!");
			//Now do the effects listed under completioneffects
			if (isset($house['data']['buildjobs'][$job]['completioneffects']['deltas'])){
				foreach($house['data']['buildjobs'][$job]['completioneffects']['deltas'] AS $cat=>$delta){
					$house['data'][$cat]+=$delta;
				}
			}
			if (isset($house['data']['buildjobs'][$job]['completioneffects']['changes'])){
				foreach($house['data']['buildjobs'][$job]['completioneffects']['changes'] AS $cat=>$change){
					$house['data'][$cat]=$change;
				}
			}
			if (isset($house['data']['buildjobs'][$job]['completioneffects']['rooms'])){
				foreach($house['data']['buildjobs'][$job]['completioneffects']['rooms'] AS $roomkey=>$vals){
					if (isset($vals['deltas'])){
						foreach($vals['deltas'] AS $subval=>$subdelt){
							$house['data']['rooms'][$roomkey][$subval]+=$subdelt;
						}
					}
					if (isset($vals['changes'])){
						foreach($vals['changes'] AS $subval=>$subchange){
							$house['data']['rooms'][$roomkey][$subval]=$subchange;
						}
					}
					if (isset($vals['newsleepslots'])){
						foreach($vals['newsleepslots'] AS $newsleepslot){
							$house['data']['rooms'][$roomkey]['sleepslots'][]=$newsleepslot;
						}
					}
				}
			}
			if (isset($house['data']['buildjobs'][$job]['completioneffects']['newrooms'])){
				foreach($house['data']['buildjobs'][$job]['completioneffects']['newrooms'] AS $newroom){
					$house['data']['rooms'][]=$newroom;
				}
			}
			output_notl("%s`n`n",$house['data']['buildjobs'][$job]['completioneffects']['msg']);
			//now unset the build job
			unset($house['data']['buildjobs'][$job]);
			//are all build jobs done?  Then unset the $buildjobs array completely
			if (count($house['data']['buildjobs'])==0){
				unset($house['data']['buildjobs']);
			}
		}
	} else {
		$house['data']['buildjobs'][$job]['jobs'][$subjob]=$jobtoperform;
	}

	//write house data back to the database
	improbablehousing_sethousedata($house);

	//Show links to do more building
	improbablehousing_show_build_jobs($house);
	improbablehousing_bottomnavs($house,$rid);
	page_footer();
}

?>