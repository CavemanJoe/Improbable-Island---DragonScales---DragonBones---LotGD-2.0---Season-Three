<?php
/*
9.7
Originally Created by Kallell for www.phofire.com/logd
(Phofire of DragonPrime)
Special thanks to the Members of Dragon Prime
Version 0.9:
A Brothel which offers rewards at a cost along with the only
way to get access to a certain specialty - case 10 - substituted with charm


9.7 to 9.8 Conversion
Converted by Chris Vorndran (Sichae)

v1.0
After recieveing permission from Phofire, I begin the 9.8 conversion of the Brothel.
First ever Conversion, hope it goes well.

v1.1
Added in several functions, to take care of switching and e_rands
Added in bouncer effects.
Stories written by Kallell

File Name: brothel.php
*/

function brothel_getmoduleinfo(){
	$info = array(
		"name"=>"Community Brothel",
		"author"=>"Kallell (Phofire)<br>Converted by Chris Vorndran",
		"version"=>"1.5",
		"category"=>"Village",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=19",
		"vertxtloc"=>"http://dragonprime.net/users/Sichae/",
		"description"=>"Allows a user to spend Gems in order to gain entrace to a Gentleman/Lady's club. There, they shall be booned by a certain worker.",
		"settings"=>array(
			"Brothel Settings,title",
			"gemcost"=>"How many gems does it cost to use the brothel,int|2",
			"times"=>"How many times can this be accessed perday,int|2",
			"owner"=>"Owner's name `imust be female`i,text|Ge'Mah",
			"shoppe"=>"Name of Shoppe,text|Ge'Mah's Relaxation Respite",
			"allow"=>"Does this Brothel Allow Gay Partnering,bool|1",
			"Brothel Employee Names,title",
			"m1"=>"Name of First Male,text|Vincent",
			"m2"=>"Name of Second Male,text|Caramon",
			"m3"=>"Name of Third Male,text|Timothy",
			"m4"=>"Name of Fourth Male,text|Lancelot",
			"f1"=>"Name of First Female,text|Elissa",
			"f2"=>"Name of Second Female,text|Vermillion",
			"f3"=>"Name of Third Female,text|Sapphire",
			"f4"=>"Name of Fourth Female,text|Jessica",
			"mindk"=>"What is the minimum DK before this shop will appear to a user?,int|0",
			),
		"prefs"=>array(
			"Brothel Preferences,title",
			"done"=>"How many times has this been used today,int|0",
		)	);
	return $info;
}
function brothel_install(){
	$condition = "if (\$session['user']['location'] == \"Squat Hole\") {return true;} else {return false;};";
	module_addhook("village",false,$condition);
	module_addhook("newday");
	return true;
}
function brothel_uninstall(){
	return true;
}
function brothel_dohook($hookname,$args){
	global $session;
	$shoppe = get_module_setting("shoppe");
	switch($hookname){
	case "newday":
		set_module_pref("done",0);
		break;
  	case "village":
    	if ($session['user']['dragonkills'] >= get_module_setting("mindk")) {
			tlschema($args['schemas']['tavernnav']);
        	addnav($args['tavernnav']);
			tlschema();
        	addnav(array("%s",$shoppe),"runmodule.php?module=brothel&op=enter");
		}
		break;

}
return $args;
}
function brothel_run(){
	global $session;
	$gemcost=get_module_setting("gemcost");
	$theygems=$session['user']['gems'];
	$done = get_module_pref("done");
	$times = get_module_setting("times");
	$owner = get_module_setting("owner");
	$allow = get_module_setting("allow");
	$shoppe = get_module_setting("shoppe");

	$m1 = get_module_setting("m1");
	$m2 = get_module_setting("m2");
	$m3 = get_module_setting("m3");
	$m4 = get_module_setting("m4");

	$f1 = get_module_setting("f1");
	$f2 = get_module_setting("f2");
	$f3 = get_module_setting("f3");
	$f4 = get_module_setting("f4");

	$op = httpget("op");
    page_header(array("%s",color_sanitize($shoppe)));
    
if ($op=="enter" && $theygems>=$gemcost && $done<$times){
	blocknav("runmodule.php?module=brothel&op=enter");
    output("`2As you walk into a large home behind thick foilage, you are greeted by `5%s`2.",$owner);
    output(" `5%s`2, is an elf of incredible beauty.",$owner);
    output(" Behind her a number of Men and Women bustle about.`n`n");
    output("`2\"`5Please come in and rest, while one of my staff sees to your needs for only %s gems.`2\" she says.",$gemcost);
	if ($allow == 1){
	  addnav("Girls");
      addnav(array("%s",$f1),"runmodule.php?module=brothel&op=f1");
      addnav(array("%s",$f2),"runmodule.php?module=brothel&op=f2");
      addnav(array("%s",$f3),"runmodule.php?module=brothel&op=f3");
      addnav(array("%s",$f4),"runmodule.php?module=brothel&op=f4");
	  addnav("Guys");
      addnav(array("%s",$m1),"runmodule.php?module=brothel&op=m1");
      addnav(array("%s",$m2),"runmodule.php?module=brothel&op=m2");
      addnav(array("%s",$m3),"runmodule.php?module=brothel&op=m3");
      addnav(array("%s",$m4),"runmodule.php?module=brothel&op=m4");
	}else{
		if ($session['user']['sex']==0){
			addnav("Girls");
		    addnav(array("%s",$f1),"runmodule.php?module=brothel&op=f1");
			addnav(array("%s",$f2),"runmodule.php?module=brothel&op=f2");
			addnav(array("%s",$f3),"runmodule.php?module=brothel&op=f3");
			addnav(array("%s",$f4),"runmodule.php?module=brothel&op=f4");
		}else{
			addnav("Guys");
			addnav(array("%s",$m1),"runmodule.php?module=brothel&op=m1");
            addnav(array("%s",$m2),"runmodule.php?module=brothel&op=m2");
            addnav(array("%s",$m3),"runmodule.php?module=brothel&op=m3");
            addnav(array("%s",$m4),"runmodule.php?module=brothel&op=m4");
		}
	}
}elseif($theygems<$gemcost){
		output("`5%s `2looks at you, with her stunning eyes.",$owner);
		output(" She motions for a large burly man.");
		output(" He grabs you by your coat and neck and tosses you out of the building.`n`n");
		output("\"`5I am sorry, but you do not have enough gems for my services.");
		blocknav("runmodule.php?module=brothel&op=enter");
}elseif($done>=$times){
	output("I am quite sorry... but your libido can not handle that much in one day.");
	output(" Please come back tomorrow.");
	blocknav("runmodule.php?module=brothel&op=enter");
}switch($op){
	case "f1":
			$done++;
			set_module_pref("done",$done);
			brothel_girldoes();
      		$session['user']['gems']-=$gemcost;
			brothel_outcome();
			break;
	case "f2":
			$done++;
			set_module_pref("done",$done);
			brothel_girldoes();
			$session['user']['gems']-=$gemcost;
			brothel_outcome();
			break;
	case "f3":
			$done++;
			set_module_pref("done",$done);
			brothel_girldoes();
			$session['user']['gems']-=$gemcost;
			brothel_outcome();
			break;  
	case "f4":
			$done++;
			set_module_pref("done",$done);
			brothel_girldoes();
			$session['user']['gems']-=$gemcost;
			brothel_outcome();
			break;
	case "m1":
			$done++;
			set_module_pref("done",$done);
			brothel_guydoes();
			$session['user']['gems']-=$gemcost;
			brothel_outcome();
			break;
	case "m2":
			$done++;
			set_module_pref("done",$done);
			brothel_guydoes();
			$session['user']['gems']-=$gemcost;
			brothel_outcome();
			break;
	case "m3":
			$done++;
			set_module_pref("done",$done);
			brothel_guydoes();
			$session['user']['gems']-=$gemcost;
			brothel_outcome();
			break;
	case "m4":
			$done++;
			set_module_pref("done",$done);
			brothel_guydoes();
			$session['user']['gems']-=$gemcost;
			brothel_outcome();
			break;
}
if ($done<$times){
	addnav("Return to the Selection","runmodule.php?module=brothel&op=enter");
}
addnav("Leave");
villagenav();
page_footer();
}
function brothel_girldoes(){
	global $session;
	switch (e_rand(1,4)){
		case 1:
			output ("`%Leading you to a private room, where you spend much time enraptured by her eyes.");
			output (" She coaxes many stories of your bravery from you.");
			output (" It's as if you could talk for hours, and you do!");
			output (" Eventually you know you have to leave but feel quite energized.");
			output (" You feel like a new man.");
			break;
		case 2:
			output ("`%After being lead to an upstairs chamber, she pours you a drink.");
			output (" The first sip makes you feel warm but not as warm as the first sip of her lips.");
      		output (" The world becomes a blur in her arms.");
      		output (" When you wake up she is gone.");
      		output (" You are left feeling incredible.");
			break;
		case 3:
			output ("`%You're not sure if it's a mere woman, or an Angel that lead you to her private chambers.");
			output (" Her hands are ecstasy, as she massages your tired muscles.");
			output (" Her fingertips caress and excite your scars.");
			output (" Your body relaxes and eventually you fade off to sleep.");
			output (" She wakes you later and with a sly smile says you have a forest to clean up.");
			output (" You have never felt better.");
			break;
		case 4:
			output ("`%She gives you a coy smile then leads you to her private room.");
			output (" Upon closing the door however, the smile changes to something much different.");
			output (" She comes close and bites and claws at your skin.");
			output (" The entire time you experience pain greater than any battle, yet sweeter.");
			output (" You leave not quite sure what has happened, but looking forward to it again.");
			break;
}
}
function brothel_guydoes(){
	global $session;
	switch (e_rand(1,4)){
		case 1:
			output ("`\$The light of the setting sun shines into the window of one of the upstairs chambers.");
			output (" You study his muscular chest defined in the light and you cannot help yourself as you surrender to him."); 
      		output (" You wake up feeling energized and ready to take on the world once more.");
			break;
		case 2:
			output ("`\$You are led to a small, yet ornate room by him.");
			output (" He looks into your eyes, until it seems he can read your innermost thoughts.");
			output (" Although you are a fearless warrior who has seen many battles you feel vulnerable.");
			output (" Yet, at the same time, you share your deepest secrets with him.");
			output (" You talk until daylight and before you leave.");
			output (" He takes your hand and kisses it.");
			output (" He says the time he shared just chatting with you was just as good as any other woman he has been with.");
			output (" You leave feeling beautiful.");
			break;
		case 3:
			output ("`\$You are lying on satin sheets in one of the secluded boudoirs.");
			output (" He admires your body, worshiping you as if you were a goddess of some sort.");
			output (" You completely forget about the wounds you suffered that day.");
			output (" As he whispers words of love and adoration, you engage in romance.");
      		output (" You the happiest that you have been in a long time.");
			break;
		case 4:
			output ("`\$You ascend the stairs behind him, until you both come to a curtained off room.");
			output (" He sits you down on a pile of luxurious pillows and cushions."); 
      		output (" He pulls out a piece of parchment and begins to recite a sonnet.");
      		output (" You are immediately touched by his words.");
      		output (" He folds the parchment away and in doing so he leans in.");
      		output (" Whispering that he wrote it long ago as a young lad.");
      		output (" Never knowing, he'd actually find a woman that would fit the essence of the poem.");
			break;
}
}
function brothel_outcome(){
	// actually took this idea from the prizemount.php.
	// a separate function to do a switch
	// decided to adapt it to other parts of script
	global $session;
	 switch(e_rand(1,10)){
			case 1:
      		case 2:
      		case 3:
      		case 4:
      			output("`n`nYou now feel fully refreshed, ready to face the world. `n`n");
      			output("`6 `bYou gain some Stamina!`b");
      			$session['user']['turns']+=2;
      		break;
			case 5:
      			output("Your realize you have fallen asleep, upon gathering your articles your purse is a little light`n`n");
      			output("`6 `bYou have been robbed!`b");
      			$session['user']['gold']=0;
      		break;
			case 6:
      		case 7:
      		case 8:      
      			output("`n`n`6 `bIn your delightful mood you leave whistling a slight tune, you also aren't watching where you are going and trip for something.");
				output("`n`n Looking down you find a small pouch with 500 gold.`b");
      			$session['user']['gold']+=500;
      		break;
	  		case 9:
      		case 10:
      			output("`n`n`6`b You feel proud and alive, you're absolutely glowing!");
				output("`n`nYou Gain 5 charm");
      			$session['user']['charm']+=5;
				break;
		}
}
?>