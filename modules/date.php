<?php

function date_getmoduleinfo(){
	$info = array(
		"name"=>"Kintian Date",
		"version"=>"1.0",
		"author"=>"Cousjava",
		"category"=>"General",
		"download"=>"",
		"settings"=>array(
			"day"=>"the current day of the month,int|1",
			"month"=>"The current month of the year,int|1",
			"year"=>"The current year,int|500",
		),
	);
	return $info;
}
function date_install(){
	module_addhook("newday-runonce");	
}
function date_uninstall(){
	return true;
}

function date_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "newday-runonce":
			$day=get_module_setting("day");
			$day++;
			$daysinmonth=42;
			if ($day<=$daysinmonth){
				set_module_pref("day",$day);
			} else {
				$monthsinyear=8;
				$month=get_module_setting("month");
				$month++;
				set_module_setting("day",1);
				if ($month<=$monthsinyear){
					set_module_setting("month",$month);
				} else {
					increment_module_setting("year");
					set_module_setting("month",1);
				}
			}
			break;
	}
	return $args;
}

?>
