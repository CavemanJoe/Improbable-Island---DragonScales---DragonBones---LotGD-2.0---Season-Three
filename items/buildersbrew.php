<?php

//BUILDER'S BREW

function buildersbrew_define_item(){
	set_item_setting("cannotdiscard","1","buildersbrew");
	set_item_setting("context_forest","1","buildersbrew");
	set_item_setting("context_village","1","buildersbrew");
	set_item_setting("context_worldmap","1","buildersbrew");
	set_item_setting("context_lodge","1","buildersbrew");
	set_item_setting("description","A stasis flask containing the mythical Perfect Builder's Brew - a cup of tea so strong and sugary that it grants the strength of ten ordinary mortals to anybody who drinks it before performing building-related activities.","buildersbrew");
	set_item_setting("destroyafteruse","true","buildersbrew");
	set_item_setting("dkpersist","true","buildersbrew");
	set_item_setting("giftable","true","buildersbrew");
	set_item_setting("inventorylocation","lodgebag","buildersbrew");
	set_item_setting("image","buildersbrew.png","buildersbrew");
	set_item_setting("lodge","true","buildersbrew");
	set_item_setting("lodge_cost","250","buildersbrew");
	set_item_setting("lodge_longdesc","The Perfect Builder's Brew began as a theory concoted by the world's leading tea-related scientist and researcher Mrs Henrietta Wainwright of Avebury, Wiltshire.  In collaboration with the renowned builder and tea expert Dave Murphy of Birmingham and the enigmatic and eccentric Brew Monks of the Somerset Levels, she devised an atom-precise tea brewing technique and formula whose results are truly astonishing.`n`nUpon imbibing this tea, kept at perfect temperature in this electronic stasis-field flask, the Stamina cost of building- and decorating-related activities will drop to one tenth of its original level, and your experience gain for these actions will be increased by 20%.  The effects last until you trigger a new Game Day, and affect the following actions:`n`nLogging`nStonecutting`nCarpentry`nMasonry`nDecorating`nOutpost Wall Reinforcement`n`nThe Builder's Brew does `inot`i affect Travel costs or maximum backpack weights, so it's still be a good idea to have a friend help you to carry things around.  `b`4Please check the current Game Time before you drink your Brew!`0`b  Nothing sucks worse than drinking this expensive (but awesome!) tea and then realising you've only got ten minutes to use it!","buildersbrew");
	set_item_setting("usetext","`0You unscrew the lid of the Stasis Flask.  You feel a tiny movement from within the flask - as though the tea were frozen in time mid-slosh.  You peer inside - yes, the tea is swirling clockwise.  The stasis field must have been engaged just seconds after the prescribed stirring technique (six spins clockwise at 92rpm, break flow by pulling spoon across centre of cup, three spins anti-clockwise at 93rpm, hold spoon head at precise centre for .72333 of a second, four spins clockwise at 70.5rpm, ENJOY!).  You raise the cup reverently to your lips.`n`nIt is the perfect cup of tea.  Nothing more needs to be said.`n`nStanding up, your pants slowly slide down to reveal two inches of arse crack.  `iYou've got some building to do.`i","buildersbrew");
	set_item_setting("verbosename","Builder's Brew","buildersbrew");
	set_item_setting("require_file","buildersbrew.php","buildersbrew");
	set_item_setting("call_function","buildersbrew_use","buildersbrew");
}

function buildersbrew_use($args){
	global $session;
	require_once "modules/staminasystem/lib/lib.php";
	apply_stamina_buff('buildersbrew_1', array(
		"name"=>"Builder's Brew",
		"action"=>"Logging",
		"costmod"=>0.1,
		"expmod"=>1.2,
		"rounds"=>-1,
		"roundmsg"=>"The warmth from your Builder's Brew makes logging a breeze!",
	));
	apply_stamina_buff('buildersbrew_2', array(
		"name"=>"Builder's Brew",
		"action"=>"Stonecutting",
		"costmod"=>0.1,
		"expmod"=>1.2,
		"rounds"=>-1,
		"roundmsg"=>"The warmth from your Builder's Brew makes the stone seem as soft as butter!",
	));
	apply_stamina_buff('buildersbrew_3', array(
		"name"=>"Builder's Brew",
		"action"=>"Carpentry",
		"costmod"=>0.1,
		"expmod"=>1.2,
		"rounds"=>-1,
		"roundmsg"=>"The warmth from your Builder's Brew makes this carpentry job seem a hell of a lot easier!",
	));
	apply_stamina_buff('buildersbrew_3a', array(
		"name"=>"Builder's Brew",
		"action"=>"Reinforcement",
		"costmod"=>0.1,
		"expmod"=>1.2,
		"rounds"=>-1,
		"roundmsg"=>"The warmth from your Builder's Brew makes this reinforcement job seem a hell of a lot easier!",
	));
	apply_stamina_buff('buildersbrew_4', array(
		"name"=>"Builder's Brew",
		"action"=>"Masonry",
		"costmod"=>0.1,
		"expmod"=>1.2,
		"rounds"=>-1,
		"roundmsg"=>"The warmth from your Builder's Brew makes the stone feel light as a feather!",
	));
	apply_stamina_buff('buildersbrew_5', array(
		"name"=>"Builder's Brew",
		"action"=>"Decorating",
		"costmod"=>0.1,
		"expmod"=>1.2,
		"rounds"=>-1,
		"roundmsg"=>"The warmth from your Builder's Brew makes this decorating job a trivial matter indeed!",
	));
	return $args;
}

?>