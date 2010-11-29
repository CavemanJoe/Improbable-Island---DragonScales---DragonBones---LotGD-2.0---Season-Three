<?php


function companionheal_getmoduleinfo(){
    $info = array(
        "name"=>"Companion Heal",
        "version"=>"1.1",
        "author"=>"Qwyxzl",
        "category"=>"Forest",
        "download"=>"http://www.geocities.com/qwyxzl/companionheal.zip"
    );
    return $info;
}


function companionheal_install(){	
	module_addhook("footer-healer");
    return true;
}


function companionheal_uninstall(){
    return true;
}


function companionheal_dohook($hookname,$args){
	global $session, $companions;

    switch($hookname){
    	case "footer-healer":
    		$return = httpget("return");
			$returnline = $return>""?"&return=$return":"";
			addnav("`bHeal Companions`b");
			//basically the code from the mercenary camp
			foreach($companions as $name => $companion){
				if(isset($companion['cannotbehealed']) && $companion['cannotbehealed'] == true){
				}else{
					$points = $companion['maxhitpoints'] - $companion['hitpoints'];
					if($points > 0){
						$cost = round(log($session['user']['level']+1) * ($points + 10)*1.33);
						addnav(array("%s`0 (`^%s Gold`0)", $companion['name'], $cost), "runmodule.php?module=companionheal&name=".rawurlencode($name)."&cost=$cost$returnline");
					}
				}
			}
			break;
    }    
	return $args;
}


function companionheal_run(){
	global $session, $companions;
	$return = httpget("return");
	$returnline = $return>""?"&return=$return":"";
	
	//the dialog was adapted from healer.php
	page_header("Healer's Hut");
	$cost = httpget('cost');
	
	if($session['user']['gold'] < $cost){
		output("`3The old creature pierces you with a gaze hard and cruel.`n");
		output("Your lightning quick reflexes enable you to dodge the blow from its gnarled staff.`n");
		output("Perhaps you should get some more money before you attempt to engage in local commerce.`n`n");
	}else{
		$name = stripslashes(rawurldecode(httpget('name')));
		$session['user']['gold'] -= $cost;
		$companions[$name]['hitpoints'] = $companions[$name]['maxhitpoints'];
		output("`3With a grimace, %s`3 up-ends the potion the from the creature.`n", $name);
		output("Muscles knit back together, cuts close and bruises fade. %s`3 is ready to battle once again!`n", $name);
		output("You hand the creature your gold and are ready to be out of here.");
	}
	addnav("Continue", "healer.php?$returnline");
	page_footer();
}


?>