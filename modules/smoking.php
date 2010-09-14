<?php

function smoking_getmoduleinfo(){
	$info = array(
		"name"=>"Smoking",
		"version"=>"2008-06-24",
		"author"=>"Dan Hall",
		"category"=>"General",
		"download"=>"",
		"prefs"=>array(
			"addiction"=>"How addicted is the player,int|0",
			"coldturkey"=>"How many days has the player gone without smoking,int|0",
			"betweensmokes"=>"How many more page views has the player got until they get the cravings again,int|0",
		),
	);
	return $info;
}
function smoking_install(){
	module_addhook("newday");
	module_addhook("forest");
	module_addhook("village");
	return true;
}
function smoking_uninstall(){
	return true;
}
function smoking_dohook($hookname,$args){
	global $session;
	$addiction = get_module_pref("addiction");
	$coldturkey = get_module_pref("coldturkey");
	$betweensmokes = get_module_pref("betweensmokes");
	switch($hookname){
	case "newday":
		if ($addiction > 0){
			$addiction = ($addiction-$coldturkey);
		}
		if ($addiction > 18){
			output("`bYou really, really want a cigarette.`b`n`n");
			apply_buff("smoking",array(
				"name"=>"`7Nicotine Withdrawal`0",
				"defmod"=>((100-$addiction)/100),
				"atkmod"=>((100-$addiction)/100),
				"rounds"=>$addiction*2,
				"roundmsg"=>"You're so distracted by your nicotine cravings that you can't concentrate on the fight!",
				"schema"=>"module-smoking",
				)
			);
		}
		$coldturkey+=1;
		set_module_pref("addiction", $addiction);
		set_module_pref("coldturkey", $coldturkey);
		set_module_pref("betweensmokes", $betweensmokes);
		break;
	case "forest":
		addnav("Have a Smoke","runmodule.php?module=smoking&from=jungle");
		if (has_buff("smoking")){
		break;
		}
		$betweensmokes--;
		if ($addiction > 18){
			if ($betweensmokes<0){
				$betweensmokes = (250-$addiction);
				output("`n`bYou really, really want a cigarette.`b`n`n");
				apply_buff("smoking",array(
					"name"=>"`7Nicotine Withdrawal`0",
					"defmod"=>((100-$addiction)/100),
					"atkmod"=>((100-$addiction)/100),
					"allowinpvp"=>1,
					"allowintrain"=>1,
					"rounds"=>$addiction,
					"roundmsg"=>"You're so distracted by your nicotine cravings that you can't concentrate on the fight!",
					"schema"=>"module-smoking",
					)
				);
			}
		}
		if ($addiction>0) set_module_pref("betweensmokes", $betweensmokes);
		break;
	case "village":
		addnav($args["gatenav"]);
		addnav("Have a Smoke","runmodule.php?module=smoking&from=outpost");
		if (has_buff("smoking")){
		break;
		}
		$betweensmokes--;
		if ($addiction > 18){
			if ($betweensmokes<0){
				$betweensmokes = (250-$addiction);
				output("`n`bYou really, really want a cigarette.`b`n`n");
				apply_buff("smoking",array(
					"name"=>"`7Nicotine Withdrawal`0",
					"defmod"=>((100-$addiction)/100),
					"atkmod"=>((100-$addiction)/100),
					"allowinpvp"=>1,
					"allowintrain"=>1,
					"rounds"=>$addiction,
					"roundmsg"=>"You're so distracted by your nicotine cravings that you can't concentrate on the fight!",
					"schema"=>"module-smoking",
					)
				);
			}
		}
		if ($addiction>0) set_module_pref("betweensmokes", $betweensmokes);
		break;
	}
	return $args;
}
function smoking_run(){
	global $session;
	$cigarettes = $session['user']['gems'];
	$addiction = get_module_pref("addiction");
	$coldturkey = get_module_pref("coldturkey");
	$from = httpget("from");
	page_header("Let's all have a smoke!");
	if ($cigarettes>0){
		output("You sit down to have a quick smoke.`n`nYou feel `7Big and Clever!`0");
		$session['user']['gems']--;
		$addiction+=4;
		$coldturkey=0;
		$betweensmokes = (250-$addiction);
		if ($addiction<90){
			apply_buff("smoking",array(
				"name"=>"`7Nicotine Buzz`0",
				"defmod"=>1.2,
				"atkmod"=>1.2,
				"allowinpvp"=>1,
				"allowintrain"=>1,
				"rounds"=>100-$addiction,
				"roundmsg"=>"Your Nicotine Buzz helps you to fight.  Smoking is cool!",
				"schema"=>"module-smoking",
				)
			);
		}
		if ($addiction>90){
			apply_buff("smoking",array(
				"name"=>"`7Nicotine Buzz`0",
				"defmod"=>1.2,
				"atkmod"=>1.2,
				"allowinpvp"=>1,
				"allowintrain"=>1,
				"rounds"=>10,
				"roundmsg"=>"Your Nicotine Buzz helps you to fight.  Smoking is cool!",
				"schema"=>"module-smoking",
				)
			);
		}
	}
	if ($cigarettes==0){
		output("You sit down to have a quick smoke, but quickly realise that you don't actually have any cigarettes.`n`nYou feel `7a little bit silly!`0");
	}
	if ($from=="outpost"){
		addnav("Back to the Outpost","village.php");
	}
	if ($from=="jungle"){
		addnav("Back to the Jungle","forest.php");
	}
	set_module_pref("addiction", $addiction);
	set_module_pref("coldturkey", $coldturkey);
	set_module_pref("betweensmokes", $betweensmokes);
	page_footer();
	return $args;
}
?>