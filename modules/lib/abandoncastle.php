<?php
	global $session;
	global $badguy;
	$op = httpget('op');
	$locale = httpget('loc');
	$skill = httpget('skill');
	if (is_module_active('potions')) {
		set_module_pref('restrict', true, 'potions');
	}
	if (is_module_active('usechow')) {
		set_module_pref('restrict', true, 'usechow');
	}

	$knownmonsters = array('ghost1', 'ghost2', 'bat', 'rat', 'minotaur');
	if (in_array($op, $knownmonsters) || $op == "fight" || $op == "run") {
		abandoncastle_fight($op);
		die;
	}

	page_header("Abandoned Castle");
	if ($session['user']['hitpoints'] > 0){} else{
		redirect("shades.php");
	}

	$umaze = get_module_pref('maze');
	$umazeturn = get_module_pref('mazeturn');
	$upqtemp = get_module_pref('pqtemp');

	if ($op == "" && $locale == "") {
		output("`c`b`&Abandoned Castle`0`b`c`n`n");
		if (get_module_pref('enteredtoday')) {
			output("`2You tug on the door, however you cannot get it to open.`n`2(`3You can only enter the castle once per day`2)`0");
			if (get_module_setting("forestvil") == 1){
				addnav("Continue","forest.php");
			}else{
				villagenav();
			}
		} elseif ($session['user']['dragonkills'] >= get_module_setting('dkenter')) {
			output("`2You enter the Abandoned Castle, as you do the door slams behind you.");
			output("Try as you may the door won't budge!  Looks like you are going to have to find ");
			output("another way out of this place!`n");
			output("You look around, and in the dim light you see that the castle's narrow passage is ");
			output("littered with junk and remains of past visitors.`n");
			if ($session['user']['hashorse']>0){
				global $playermount;
				output("Too bad your %s couldn't come in with you, now you are all alone.`n",$playermount['mountname']);
			}
			output("This place feels strangely draining any buffs you have are unusable.`n`n");
			if (count($session['bufflist'])>0 && is_array($session['bufflist']) || $skill!=""){
				$skill="";
				if ($skill=="") $session['user']['buffbackup']=serialize($session['bufflist']);
				$session['bufflist']=array();
			} 
			$locale=6;
			$umazeturn = 0;
			set_module_pref("mazeturn", 0);
			//they have to do an unfinished maze.
			if ($maze==""){
				//maze generation array.  Mazes are premade.
				//as you add mazes make sure you change the e_rand value to match your quantity of mazes
				switch(e_rand(1,45)){
					case 1:
						//author: Lonnyl
						//title: uno
						$maze = array(j,d,d,d,b,c,k,o,d,d,k,f,d,b,d,a,n,i,d,b,d,e,i,d,c,k,m,j,d,p,g,o,e,o,b,k,i,k,g,j,n,f,k,g,o,e,g,j,e,i,a,b,a,h,g,j,h,i,h,i,n,g,i,e,j,e,i,d,k,j,d,d,h,l,i,h,g,j,d,h,g,o,b,d,c,d,d,h,i,b,d,c,k,g,j,d,b,d,k,j,c,d,d,e,i,a,d,a,d,e,i,d,d,n,i,k,i,k,i,d,e,o,d,d,b,b,a,k,i,d,d,h,o,d,d,h,m,z,i,d,d,d,n);
						break;
					case 2:
						//author: Kain
						//title: Kain's Klub
						$maze = array(j,d,b,d,n,g,j,d,k,j,k,f,k,i,d,b,c,a,k,i,e,g,g,i,k,j,h,l,i,a,n,g,m,g,l,i,h,o,c,k,f,d,h,l,i,e,l,j,k,z,g,g,j,d,e,j,h,i,e,g,i,e,i,c,d,e,i,d,k,m,i,b,c,d,d,n,g,j,d,c,d,k,g,j,d,d,b,h,i,k,j,k,g,g,g,o,k,i,k,j,h,g,f,h,g,f,d,c,n,g,i,d,h,g,j,e,i,d,d,d,e,j,d,d,h,g,f,d,k,j,k,g,i,d,d,d,c,c,n,i,h,i,h);
						break;
					case 3:
						//author: TundraWolf
						//title: woof
						$maze = array(o,b,k,j,k,f,d,d,b,d,k,j,e,f,c,c,a,d,k,f,k,m,g,g,f,n,l,f,n,f,e,f,k,g,g,i,k,f,a,b,h,i,h,g,i,h,l,i,e,m,f,d,d,n,g,j,b,c,k,m,o,a,b,d,k,g,g,i,b,c,b,b,e,i,d,h,g,i,k,g,j,h,g,i,b,b,k,m,l,i,h,m,j,h,j,e,i,c,k,f,b,n,z,g,j,h,f,b,k,g,g,g,j,h,m,g,j,c,e,g,g,g,i,h,o,k,m,m,j,c,e,g,i,d,d,d,c,d,d,c,d,h,m);
						break;
					case 4:
						//author: Hermione
						//title: easy 1
						$maze = array(j,d,d,d,b,c,b,b,k,j,k,i,d,d,d,a,d,c,h,i,h,g,j,d,d,n,f,d,d,d,d,d,h,f,d,d,d,a,d,d,d,d,d,k,f,d,d,d,e,j,d,d,d,d,e,f,d,d,d,a,a,d,d,d,d,h,i,d,d,d,a,c,d,d,n,o,k,j,d,d,n,g,j,d,d,d,d,e,i,d,d,d,a,a,b,b,b,b,h,o,d,d,d,e,g,f,e,f,a,n,o,b,d,d,e,g,g,f,a,a,n,j,c,d,d,e,i,e,g,g,f,n,z,d,d,d,h,o,c,c,c,c,n);
						break;
					case 5:
						//author: TundraWolf
						//title: woof woof
						$maze = array(j,b,b,k,j,a,b,b,b,d,k,f,a,a,h,g,g,f,c,e,j,e,f,a,e,j,h,g,f,d,h,g,g,f,c,h,g,o,h,i,d,k,g,m,g,j,d,h,j,d,d,k,g,i,k,g,i,d,k,f,d,k,g,g,j,h,f,b,k,g,i,k,g,g,g,i,k,f,e,g,i,k,g,g,g,g,j,h,f,a,h,l,i,h,g,m,g,i,k,f,a,d,a,d,d,h,z,g,j,h,f,a,k,g,j,b,k,g,g,i,k,g,i,e,f,c,a,e,g,m,j,e,m,o,h,i,d,c,h,i,d,h,m);
						break;
					case 6:
						//author: Hermione
						//title: eZ
						$maze = array(j,d,d,d,d,e,z,d,d,d,k,i,d,d,d,k,m,j,d,d,k,g,j,d,d,k,i,d,h,o,k,g,g,g,j,k,i,d,d,d,d,e,g,g,g,g,i,d,d,d,d,k,g,g,g,g,g,j,d,d,d,k,g,g,g,g,g,g,g,j,d,k,g,g,g,g,g,g,g,g,g,l,g,g,g,g,g,g,g,g,g,g,i,h,g,g,g,g,g,g,g,g,i,d,d,h,g,g,g,g,g,g,i,d,d,d,d,h,g,g,g,g,i,d,d,d,d,d,d,h,g,g,i,d,d,d,d,d,d,d,d,c,h);
						break;
					case 7:
						//author: Lonnyl
						//title: deuce
						$maze = array(j,d,b,k,j,a,d,k,j,d,k,i,k,g,g,g,g,j,h,f,d,e,j,c,e,g,f,h,i,b,a,b,h,i,b,c,a,e,z,o,h,i,a,n,j,c,n,m,g,g,j,k,o,a,n,g,j,b,n,g,i,h,g,l,i,k,f,h,i,n,i,d,n,g,f,d,e,i,b,d,d,d,d,n,g,i,d,e,j,h,o,d,k,o,k,f,d,k,g,f,b,b,d,c,n,g,i,n,g,g,g,g,g,o,d,k,f,n,j,h,g,g,i,e,j,k,g,g,j,h,j,e,i,n,i,h,i,c,c,h,o,h,m);
						break;
					case 8:
						//author: Lonnyl
						//title: MegaG
						$maze = array(j,b,b,b,b,c,b,b,b,b,k,i,h,g,m,g,j,a,a,a,a,e,j,d,c,n,g,g,g,g,g,g,g,g,j,d,d,h,g,g,g,g,g,g,g,i,d,b,k,g,g,g,g,g,g,g,j,k,m,g,g,g,g,g,g,m,i,h,f,k,g,g,g,g,g,i,k,j,d,h,g,g,g,g,g,i,k,g,g,j,d,h,g,g,g,i,k,g,g,g,g,o,b,e,g,f,k,g,g,g,g,i,n,g,m,m,m,g,g,g,g,g,j,k,i,n,z,o,h,g,g,g,i,h,i,d,d,h,o,d,h,m,m);
						break;
					case 9:
						//author: Lonnyl
						//title: MegaD
						$maze = array(j,d,d,d,d,c,d,d,d,d,k,f,d,d,d,d,d,d,d,d,k,g,f,d,d,d,d,d,d,d,d,h,g,f,d,d,d,d,d,d,d,d,k,g,f,d,d,d,d,d,d,d,k,g,g,f,d,d,d,d,d,d,n,g,g,g,f,d,d,d,d,d,n,j,h,m,m,f,d,d,d,d,d,d,h,j,n,z,f,d,d,d,d,d,d,d,h,l,g,f,d,d,d,d,d,d,d,d,h,g,f,d,d,d,d,d,d,d,d,d,h,f,d,d,d,d,d,d,d,d,d,n,i,d,d,d,d,d,d,d,d,d,n);
						break;
					case 10:
						//author: Hermione
						//title: Deadend City
						$maze = array(o,b,b,b,b,a,b,b,b,b,n,o,a,e,m,m,g,g,m,f,a,n,o,a,e,j,k,g,i,k,f,a,n,o,a,e,g,g,i,k,g,f,a,n,o,a,e,g,i,k,g,g,f,a,n,o,a,e,i,k,g,g,g,f,a,n,o,a,e,l,g,g,g,g,f,a,n,o,a,e,g,g,g,g,g,f,a,n,o,a,e,g,g,g,g,g,f,a,n,o,a,e,f,e,g,g,g,f,a,n,o,a,a,a,a,a,a,a,a,a,n,j,a,a,a,a,a,a,a,a,a,k,m,m,m,m,m,m,m,m,m,m,z);
						break;
					case 11:
						//author: Blayze
						//title: Hot
						$maze = array(j,k,o,k,j,e,j,b,b,d,k,g,i,k,g,g,g,m,m,i,k,g,i,k,g,g,g,f,d,k,j,h,g,j,h,g,g,g,f,k,i,c,n,g,i,k,f,h,g,g,g,j,d,k,g,j,h,i,k,g,g,g,i,k,i,h,i,k,j,h,g,g,i,k,i,d,k,j,h,i,d,h,g,o,a,d,d,h,i,d,b,d,d,c,d,c,d,b,n,j,n,g,j,n,j,k,l,l,i,k,g,z,m,f,d,h,f,a,e,j,h,g,i,d,c,d,d,h,g,g,i,k,i,d,d,d,d,d,d,h,i,d,h);
						break;
					case 12:
						//author: Kain
						//title: Kain's Krypt
						$maze = array(j,n,l,j,d,c,k,j,k,o,k,i,b,a,c,b,b,c,h,f,k,g,j,e,f,d,h,i,k,j,h,f,e,m,g,i,k,l,j,c,e,l,g,g,j,c,k,m,f,c,b,c,h,f,h,f,n,f,d,c,d,e,j,d,c,k,i,b,h,o,b,z,i,c,d,k,g,o,a,b,n,i,k,o,d,d,a,h,j,h,f,n,l,i,d,k,l,i,k,i,b,c,d,c,k,j,a,c,k,g,l,g,l,j,d,h,g,g,j,e,m,f,h,f,e,j,d,a,c,e,i,k,i,d,h,i,c,n,i,n,i,n,m);
						break;
					case 13:
						//author: Lonnyl
						//title: dizzy
						$maze = array(j,k,j,b,s,g,j,d,d,d,k,g,g,g,g,j,a,h,j,d,k,g,g,g,g,g,g,g,l,g,l,g,g,m,i,h,i,h,g,g,g,i,h,g,j,b,d,b,d,e,i,c,d,d,h,f,a,d,a,k,f,d,d,d,d,k,i,h,q,c,h,g,o,d,d,d,h,j,d,d,d,d,a,d,d,d,d,k,g,j,d,d,k,g,j,d,d,k,g,g,g,j,k,g,g,g,j,k,g,g,g,g,z,g,g,g,g,g,m,g,g,g,i,d,h,g,g,g,i,d,h,g,i,d,d,d,h,m,i,d,d,d,h);
						break;
					case 14:
						//author: Kain
						//title: Kain's Korner
						$maze = array(j,d,d,d,d,c,d,d,d,d,k,i,d,k,j,d,d,k,j,d,d,h,j,k,g,g,j,d,e,i,d,d,k,g,i,h,g,g,l,i,d,d,d,h,i,d,d,h,g,g,j,d,d,d,k,j,d,b,d,c,c,c,d,b,d,h,g,o,e,j,d,d,d,d,e,j,k,g,o,e,f,d,d,d,d,e,m,g,g,o,e,g,l,j,k,z,f,d,h,i,d,h,g,g,g,g,g,i,d,k,j,d,d,h,g,g,g,g,j,k,g,f,d,d,d,h,g,g,g,g,g,g,i,d,d,d,d,h,i,c,h,i,h);
						break;
					case 15:
						//author: Kain
						//title: Kain's Konfusion
						$maze = array(j,d,d,k,j,c,d,d,d,d,k,i,d,k,g,g,l,j,d,d,k,g,j,d,e,i,h,i,c,d,k,i,h,g,j,e,j,k,j,d,n,g,j,k,g,g,i,h,g,i,k,j,h,g,g,g,i,k,j,h,j,h,i,d,h,g,g,o,h,g,j,h,j,d,k,j,h,i,k,j,h,i,b,e,z,g,i,k,j,h,m,j,k,g,i,d,e,j,h,g,j,k,z,g,f,d,n,g,i,k,g,g,g,j,h,g,j,d,c,k,g,f,h,g,g,j,e,g,o,d,h,g,i,d,h,i,h,i,c,d,d,d,h);
						break;
					case 16:
						//author: Lonny
						//title: jump down turnaround
						$maze = array(j,d,d,d,d,c,d,d,d,d,k,i,d,d,d,d,b,d,d,d,d,h,j,d,d,d,d,c,d,d,d,d,k,g,j,b,d,d,d,d,d,d,d,h,g,g,g,j,k,j,k,j,k,j,k,g,g,i,h,i,h,i,h,i,h,g,g,i,d,d,d,d,k,j,d,d,h,i,d,d,d,d,k,g,i,d,d,k,j,b,b,b,b,h,g,j,d,d,h,f,a,a,a,e,j,h,f,b,d,n,f,a,r,a,e,g,j,a,e,j,k,f,a,a,a,e,g,i,c,h,g,g,i,c,c,c,h,i,d,d,d,h,z);
						break;
					case 17:
						//author: Lonny
						//title: Into the Vortex
						$maze = array(j,b,b,b,b,c,b,b,b,b,k,f,h,m,m,i,p,h,m,m,i,e,f,d,d,d,d,d,d,d,d,d,e,f,n,j,d,d,d,d,d,k,o,e,f,n,g,j,d,d,d,k,g,o,e,f,n,g,g,j,d,k,g,g,o,e,f,n,g,g,g,z,h,g,g,o,e,f,n,g,g,i,d,d,h,g,o,e,f,n,g,i,d,d,d,d,h,o,e,f,n,i,d,d,d,d,d,k,o,e,f,d,k,j,d,d,d,d,h,o,e,f,k,g,g,l,l,l,l,l,j,e,i,c,c,c,c,c,c,c,c,c,h);
						break;
					case 18:
						//author: Kain
						//title: Halls of Konfusion
						$maze = array(j,b,d,d,k,f,d,d,k,j,k,g,i,d,k,i,c,d,k,i,h,g,g,j,d,c,k,j,d,h,j,k,g,f,h,o,d,h,m,j,d,e,i,h,g,l,j,d,d,k,i,k,i,d,k,g,g,g,j,d,h,j,h,j,n,g,i,e,g,i,k,o,c,k,g,l,g,j,h,i,k,i,d,k,g,i,a,e,g,l,j,h,o,k,z,i,k,f,e,g,g,g,j,n,i,d,k,g,f,h,f,h,g,i,k,j,d,c,h,i,k,g,o,c,k,g,i,d,d,d,d,e,i,d,n,i,c,d,d,d,d,d,h);
						break;
					case 19:
						//author: TundraWolf
						//title: Twisted Dead End
						$maze = array(j,d,d,d,k,g,o,d,d,d,k,g,j,d,n,i,a,d,d,d,d,h,g,g,j,d,k,i,b,b,d,d,k,g,g,f,d,a,n,g,i,d,d,e,g,g,g,l,g,j,a,d,d,n,g,g,g,i,h,g,f,h,o,d,d,e,g,g,o,d,a,h,o,d,d,d,e,g,g,j,k,f,d,d,d,d,k,g,g,g,g,i,a,k,l,j,k,g,g,g,g,f,d,h,i,h,i,c,e,g,g,g,g,j,k,z,s,d,k,g,g,g,g,g,g,g,i,d,k,g,m,g,i,h,i,h,i,d,d,h,i,d,h);
						break; 
					case 20:
						//author: Lonny
						//title: nothing special
						$maze = array(z,j,d,k,j,c,d,d,d,d,k,g,i,d,e,i,d,b,d,d,k,g,i,d,k,i,d,d,h,o,k,g,g,j,k,g,o,k,j,d,d,h,g,g,g,f,e,j,h,i,d,k,j,h,g,g,g,g,i,k,j,d,h,g,j,e,g,g,g,j,h,g,j,b,h,g,g,g,g,g,i,d,c,h,g,j,e,g,i,h,g,j,d,n,j,h,g,i,e,j,d,h,f,d,d,h,o,c,n,g,i,d,k,m,j,d,b,d,d,d,e,j,k,i,d,h,l,g,j,d,s,g,m,i,d,d,d,c,h,i,d,d,h);
						break;
					case 21:
						//author: Hermione
						//title: chamber of secrets
						$maze = array(j,k,j,k,o,a,b,b,b,n,z,g,g,f,e,j,s,e,m,f,k,g,g,g,f,e,i,d,a,n,j,h,g,g,g,f,a,b,d,c,b,c,k,g,g,g,f,e,g,j,d,a,n,g,g,g,g,f,e,i,h,o,a,b,k,g,g,g,f,e,l,j,k,m,m,g,g,f,a,a,a,a,h,i,d,d,h,g,g,g,f,e,i,d,d,d,d,k,g,g,g,f,e,j,d,d,d,d,h,g,g,g,f,e,i,d,d,d,d,k,g,f,a,c,h,j,d,d,d,d,h,g,i,c,n,o,c,d,d,d,d,d,h);
						break;
					case 22:
						//author: Admin Lonny
						//title: Crossroads
						$maze = array(j,d,d,d,d,a,d,d,d,d,k,i,d,d,d,d,a,d,d,d,d,e,j,d,d,d,d,a,d,d,d,d,h,f,d,b,b,b,a,b,b,b,b,k,g,j,a,a,a,a,a,a,a,a,e,i,c,c,c,c,a,c,c,c,c,h,j,d,d,d,d,a,d,d,d,d,k,g,j,d,d,d,a,d,d,d,k,g,g,g,j,b,d,a,d,d,k,g,s,g,g,f,a,d,a,d,d,e,g,z,g,g,i,c,d,a,d,d,h,g,g,g,i,d,d,d,c,d,d,d,h,g,i,d,d,d,d,d,d,d,d,d,h);
						break;
					case 23:
						//author: Lonny
						//title: Loop D Loop
						$maze = array(j,b,d,b,d,a,k,j,k,j,k,g,f,k,f,k,g,g,f,h,g,g,g,i,h,i,h,g,i,a,d,c,h,g,j,k,j,k,f,k,f,d,b,k,g,i,e,i,e,i,e,f,k,i,h,i,d,c,b,c,n,g,f,h,j,k,j,b,b,e,j,d,e,f,k,g,g,f,h,i,h,g,j,h,i,c,c,e,f,b,b,k,g,g,j,d,d,k,g,f,a,a,e,g,g,g,j,k,g,g,f,a,a,e,g,g,g,z,g,g,g,f,c,c,h,g,g,i,d,h,g,g,i,d,d,d,h,i,d,d,d,h,m);
					break;
					case 24:
						//author: Lonny
						//title: 143
						$maze = array(o,d,b,d,d,c,k,j,d,d,k,j,d,a,d,d,n,f,h,o,d,h,g,o,c,n,j,k,f,d,d,b,k,f,d,d,d,c,e,f,d,d,h,m,i,d,d,d,n,g,f,d,d,d,k,j,d,b,b,d,h,g,j,d,d,h,f,k,g,g,l,o,h,i,d,b,n,g,g,g,g,g,z,d,d,k,i,k,g,g,i,h,g,i,d,n,g,j,h,g,g,j,d,a,d,d,d,e,i,k,g,g,g,l,g,j,d,n,g,j,h,g,g,g,g,g,g,o,d,h,i,k,m,i,c,h,m,i,d,d,d,d,h);
					break;
					case 25:
						//author: Lonny
						//title: 143 A&F
						$maze = array(o,d,b,d,d,c,k,j,d,d,k,j,d,a,d,d,n,f,h,o,d,h,g,o,c,n,j,k,f,d,d,b,k,f,d,d,d,c,e,f,d,d,h,m,i,d,d,d,n,g,f,d,d,d,k,j,d,b,b,d,h,g,j,d,d,h,f,k,g,g,l,o,h,i,d,b,n,g,g,g,g,g,z,d,d,k,i,k,g,g,i,h,g,i,d,n,g,j,h,g,g,j,d,a,n,o,d,e,i,k,g,g,g,l,g,j,b,n,g,j,h,g,g,g,g,g,g,i,d,h,i,k,m,i,c,h,m,i,d,d,d,d,h);
					break;
					case 26:
						//author: Lonny
						//title: more 143
						$maze = array(o,d,b,d,d,c,k,j,d,d,k,j,d,a,d,d,n,f,h,o,d,h,g,o,c,n,j,k,f,d,d,b,k,f,d,d,d,c,e,f,d,d,h,m,i,d,d,d,n,g,f,d,d,d,k,j,d,b,b,d,h,g,j,d,d,h,f,k,g,g,l,o,h,i,d,b,n,g,g,g,g,g,z,o,d,k,i,k,g,g,i,h,g,i,b,n,g,j,h,g,g,j,d,a,n,i,d,e,i,k,g,g,g,l,g,j,b,n,g,j,h,g,g,g,g,g,g,i,d,h,i,k,m,i,c,h,m,i,d,d,d,d,h);
					break;
					case 27:
						//author: Kain
						//title: kain's house of maze
						$maze = array(j,k,j,k,j,a,k,j,k,j,k,g,f,e,f,a,a,a,e,f,e,g,g,i,h,g,i,a,h,g,i,h,g,f,d,b,h,j,a,k,i,b,d,e,g,j,c,k,f,a,e,j,c,k,g,g,f,b,h,g,m,g,i,b,e,g,i,e,g,o,h,z,i,n,g,f,h,j,c,c,b,k,g,j,b,c,c,k,g,j,d,h,g,g,g,i,d,k,g,g,i,d,b,h,g,i,b,d,h,g,g,j,k,g,j,c,k,g,j,k,g,g,g,g,f,a,d,a,e,g,g,g,i,h,i,h,i,d,h,i,h,i,h);
					break;
					case 28:
						//autore: Excal
						//title: So near so far
						$maze = array(j,d,d,d,d,c,d,d,b,d,k,g,j,k,j,b,z,q,k,g,l,g,g,g,g,g,m,l,g,f,a,c,h,g,g,i,h,j,h,g,g,i,d,k,g,i,d,d,c,k,g,i,b,d,h,g,j,d,d,d,h,f,p,g,j,k,g,i,d,d,d,b,c,k,i,h,g,g,o,d,d,d,h,j,h,j,k,g,g,j,d,d,d,k,g,j,h,f,e,g,i,d,d,k,f,h,i,k,g,g,i,d,d,d,a,r,b,d,h,g,g,j,d,d,d,c,c,c,d,d,h,g,i,d,d,d,d,d,d,d,d,d,h);
					break;
					case 29:
					    //author= Excalibur
					    //title: So near so far
					    $maze = array(j,d,d,d,d,c,d,d,b,d,k,g,j,k,j,b,z,q,k,g,l,g,g,g,g,g,m,l,g,f,a,c,h,g,g,i,h,j,h,g,g,i,d,k,g,i,d,d,c,k,g,i,b,d,h,g,j,d,d,d,h,f,p,g,j,k,g,i,d,d,d,b,c,k,i,h,g,g,o,d,d,d,h,j,h,j,k,g,g,j,d,d,d,k,g,j,h,f,e,g,i,d,d,k,f,h,i,k,g,g,i,d,d,d,a,r,b,d,h,g,g,j,d,s,d,c,c,c,d,d,h,g,i,d,d,d,d,d,d,d,d,d,h);
				    break;
				    case 30:
					    //author= Poker
					    //title: The damned Hydra
					    $maze = array(j,d,k,j,k,g,j,d,d,b,k,f,k,i,h,i,c,h,o,d,c,e,g,g,o,b,d,d,k,l,j,d,e,g,i,d,c,d,d,e,i,e,s,e,f,k,o,d,d,d,a,b,a,d,e,g,g,j,d,d,d,a,a,c,n,g,m,g,g,j,k,j,e,g,j,b,h,l,g,m,g,m,g,g,g,g,f,k,g,g,o,c,d,c,e,g,f,e,g,f,a,d,d,k,j,c,a,a,h,m,g,i,d,k,g,f,n,g,g,j,k,i,k,j,h,m,m,j,e,g,m,g,o,h,i,d,n,z,c,h,i,d,h);
				    break;
					    case 31:
					    //author= Poker
					    //title: circle
					    $maze = array(j,d,d,d,d,a,d,d,d,d,k,f,d,p,b,d,c,d,b,r,d,e,f,d,d,c,n,z,o,c,d,d,e,g,j,d,k,j,c,k,j,d,k,g,g,g,j,h,i,b,h,i,k,g,g,g,g,i,d,n,i,d,d,h,g,g,g,i,d,d,d,b,d,d,d,h,g,f,d,d,d,d,a,d,d,d,d,e,g,l,j,b,k,g,j,d,d,k,g,g,i,h,s,m,f,h,j,k,g,g,g,o,b,h,j,e,j,h,m,g,g,g,o,c,d,h,g,i,d,d,h,g,i,d,d,d,d,c,d,d,d,d,h);
				    break;
				    case 32:
					    //author= Excalibur
					    //title: Many rows
					    $maze = array(j,d,d,d,z,f,d,d,d,d,k,g,j,d,d,k,f,d,d,d,d,h,g,g,j,k,i,c,b,b,d,d,k,g,g,g,f,d,d,c,a,d,d,h,g,m,e,f,b,b,b,a,b,k,l,f,p,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,f,h,g,g,g,g,g,g,g,g,g,g,j,h,g,g,g,g,g,g,g,g,g,i,k,g,g,g,g,g,g,g,g,g,j,h,g,g,g,g,g,g,g,g,i,c,k,g,m,m,m,m,m,g,i,d,d,h,i,d,d,d,d,d,h);
				    break;
				    case 33:
					    //author= Excalibur
					    //title: Be Careful
					    $maze = array(j,d,d,d,d,a,d,d,d,d,k,r,j,d,d,d,c,k,j,d,d,h,j,h,j,d,d,d,h,g,o,d,k,m,j,h,j,d,d,k,i,d,k,g,z,g,j,h,j,k,i,d,d,h,g,g,g,i,d,h,i,d,d,d,b,e,g,i,b,b,b,b,b,b,k,f,e,i,k,g,f,e,f,e,f,e,f,e,l,g,g,f,e,f,e,f,e,f,e,g,g,g,f,e,f,e,f,e,f,e,g,g,g,f,e,f,e,f,e,f,e,g,g,g,f,e,f,e,f,e,f,e,i,c,h,i,h,i,h,i,h,i,h);
				    break;
				    case 34:
					    //author= Aris
					    //title: Aris 1
					    $maze = array(j,b,b,b,k,f,b,b,b,b,k,g,g,i,h,i,a,a,a,c,c,h,g,f,d,k,j,a,e,g,j,n,l,g,i,k,m,f,a,e,g,f,k,g,f,n,g,l,i,c,e,g,g,i,e,g,j,a,a,d,k,i,h,f,k,g,g,g,f,a,b,a,d,b,h,g,g,g,f,a,a,h,i,d,c,d,h,g,g,i,a,a,k,j,n,o,d,d,e,f,k,i,a,h,i,k,j,b,b,e,f,e,z,m,o,d,e,i,c,c,e,m,g,f,d,p,b,c,d,d,b,h,o,h,i,d,d,c,n,o,d,c,n);
				    break;
				    case 35:
					    //author= Aris
					    //title: really bastard
					    $maze = array(j,d,d,d,d,c,d,d,d,d,k,g,j,d,n,o,d,d,d,d,k,g,f,e,j,d,d,d,b,d,k,f,e,g,g,g,l,o,d,c,k,g,g,g,g,g,f,e,j,k,l,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,z,f,e,g,m,g,g,g,g,g,f,q,e,g,m,j,e,g,g,f,e,i,c,h,g,l,g,g,g,g,g,i,d,d,n,m,g,g,g,g,g,i,d,d,b,d,d,h,g,g,g,i,b,n,o,c,d,d,d,h,g,i,d,c,d,n,o,d,d,d,d,h);
				    break;
				    case 36:
					    //author= Aris
					    //title: many traps
					    $maze = array(j,b,b,b,b,a,b,b,b,b,k,f,a,a,a,a,a,a,a,a,a,e,f,a,a,a,a,a,a,a,a,a,e,f,a,a,a,a,a,a,a,a,a,e,f,a,a,a,a,a,a,a,a,a,e,f,a,a,a,r,a,a,a,a,a,e,p,a,a,a,a,a,a,a,a,a,e,f,a,a,a,a,q,a,a,a,a,e,f,a,a,r,a,a,r,a,a,a,e,f,a,a,a,a,z,a,a,a,a,e,f,a,a,a,a,a,a,a,a,a,s,f,a,a,a,s,a,a,a,a,a,e,i,c,c,c,c,c,c,c,c,c,h);
				    break;
				    case 37:
					    //author= Poker
					    //title: Circle
					    $maze = array(j,d,d,d,d,a,d,d,d,d,k,g,j,d,d,d,c,d,d,d,k,g,g,g,j,d,d,b,d,d,k,g,g,g,g,g,j,d,c,d,k,g,g,g,g,g,g,g,j,d,k,g,g,g,g,g,g,g,g,f,p,g,g,g,g,g,g,g,g,f,e,z,e,g,f,e,g,g,g,g,g,g,p,e,g,g,g,g,g,g,g,g,i,d,h,g,g,g,g,g,g,g,i,d,d,d,h,g,g,g,g,g,i,d,d,d,d,d,h,g,g,g,i,d,d,d,b,d,d,d,h,g,i,d,d,d,d,c,d,d,d,d,h);
				    break;
				    case 38:
					    //author= Poker
					    //title: Circle 2
					    $maze = array(j,d,d,d,d,a,d,d,d,d,k,g,j,d,d,d,c,d,d,d,k,g,g,g,j,d,q,b,d,d,k,g,g,g,g,g,j,d,c,s,k,g,g,g,g,g,g,g,j,d,k,g,g,g,g,g,g,g,g,f,p,e,g,g,g,g,g,g,g,f,e,z,e,g,f,e,g,g,g,g,p,f,p,e,g,p,g,g,g,g,g,g,i,d,h,g,g,g,g,g,g,g,i,d,d,d,h,g,g,g,g,g,i,d,d,d,d,d,h,g,g,g,i,d,d,d,b,d,d,d,h,g,i,d,d,d,d,c,q,d,d,d,h);
				    break;
				    case 39:
					    //author= Excalibur
					    //title: Attention
					    $maze = array(j,d,d,z,o,c,b,d,d,d,k,g,j,d,b,b,d,c,d,d,d,e,g,i,b,e,g,j,b,d,d,d,e,g,j,a,c,c,c,c,d,d,d,e,g,g,f,d,d,d,d,d,d,d,e,g,g,f,d,d,d,d,d,d,d,e,g,g,f,d,d,d,d,d,d,d,e,g,g,f,d,d,d,d,d,d,d,e,g,g,f,d,d,d,d,d,d,d,h,g,g,f,d,d,d,d,d,d,d,s,i,h,f,d,d,d,d,d,d,d,e,j,b,a,d,d,d,d,d,d,d,e,i,c,c,d,d,d,d,d,d,d,h);
				    break;
				    case 40:
					    //author= Excalibur
					    //title: Damned Spyral
					    $maze = array(j,d,d,d,k,i,d,d,d,d,k,g,j,d,k,g,z,d,d,d,k,g,g,i,k,g,i,d,d,d,k,g,g,g,j,h,i,d,d,d,k,g,g,g,g,g,j,d,d,d,k,g,g,g,g,g,g,g,j,d,k,g,g,g,g,g,g,g,g,g,j,e,g,g,g,g,g,g,g,g,g,g,p,g,g,g,g,g,g,g,g,g,i,d,h,g,g,g,g,g,g,g,i,d,d,d,h,g,g,g,g,g,i,d,d,d,d,d,h,g,g,g,i,d,d,d,d,d,d,d,h,g,i,d,d,d,d,d,d,d,d,d,h);
				    break;
				    case 41:
					    //author= Excalibur
					    //title: Are you Lucky ?
					    $maze = array(j,b,k,j,k,g,j,b,b,b,r,i,a,a,e,g,g,f,a,a,a,e,j,a,a,e,g,g,f,a,a,a,e,i,a,a,e,g,g,f,a,a,a,e,j,a,a,e,g,g,f,a,a,a,e,i,a,a,e,g,g,f,a,a,a,e,j,a,a,e,g,g,f,a,a,a,e,i,a,a,e,g,g,f,a,a,a,e,j,c,a,e,g,g,f,a,a,a,e,i,b,a,e,g,g,f,a,c,a,e,j,h,i,e,g,g,i,h,j,e,g,g,j,k,p,i,c,d,d,h,g,g,i,h,i,c,n,z,d,d,d,h,m);
				    break;
				    case 42:
					    //author= Aris
					    //title: Three white roses
					    $maze = array(j,d,b,b,b,c,d,d,b,d,k,g,l,g,m,f,b,k,l,i,k,g,g,g,i,k,g,g,g,g,j,h,g,g,g,j,e,g,g,g,f,a,k,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,f,h,g,g,g,g,g,m,g,g,g,g,j,h,g,m,g,g,j,h,m,g,m,i,k,g,j,h,g,g,j,d,e,o,b,h,g,m,j,h,g,m,j,h,j,c,k,g,j,c,k,g,j,c,k,g,z,p,g,g,z,g,g,g,z,g,i,d,h,m,i,d,h,m,i,c,h);
				    break;
				    case 43:
					    //author= Aris
					    //title: Three red roses
					    $maze = array(j,d,b,b,b,c,d,d,b,d,k,g,l,g,m,f,b,k,l,i,k,g,g,g,i,k,g,g,g,g,j,h,g,g,g,j,e,g,g,g,f,a,k,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,f,h,g,g,g,g,g,m,g,g,g,g,j,h,g,m,g,g,j,h,m,g,m,i,k,g,j,h,g,g,j,d,e,o,b,h,g,m,j,h,g,m,j,h,j,c,k,g,j,c,k,g,j,c,k,g,z,g,g,f,z,g,g,g,z,g,i,d,h,m,i,d,h,m,i,p,h);
				    break;
				    case 44:
					    //author= Aris
					    //title: Three black roses
					    $maze = array(j,d,b,b,b,c,d,d,b,d,k,g,l,g,m,f,b,k,l,i,k,g,g,g,i,k,g,g,g,g,j,h,g,g,g,j,e,g,g,g,f,a,k,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,g,f,h,g,g,g,g,g,m,g,g,g,g,j,h,g,m,g,g,j,h,m,g,m,i,k,g,j,h,g,g,j,d,e,o,b,h,g,m,j,h,g,m,j,h,j,c,k,g,j,c,k,g,j,c,k,g,z,e,g,p,z,g,g,g,z,g,i,d,h,m,i,d,h,m,i,d,h);
				    break;
				    case 45:
						//author: DeZent
						//title: merryChrismas
						$maze = array(q,l,l,l,j,a,k,l,l,l,q,j,c,c,c,c,a,c,c,c,c,k,m,q,l,l,l,g,l,l,l,q,m,p,j,c,c,c,a,c,c,c,k,p,q,m,q,l,l,g,l,l,q,m,q,q,p,j,c,c,a,c,c,k,p,q,q,q,m,q,l,g,l,q,m,q,q,q,q,r,j,c,a,c,k,r,q,q,q,q,q,m,q,g,q,m,q,q,q,q,q,q,p,q,g,q,p,q,q,q,q,q,q,q,j,a,k,q,q,q,q,q,q,q,q,p,g,p,q,q,q,q,q,q,q,q,q,z,q,q,q,q,q);
					break;
				}
				$umaze = implode($maze,",");
				set_module_pref("maze", $umaze);
				if (!get_module_pref('super')){
				set_module_pref("enteredtoday", true);
				}
			}
			//addnav("Continue","runmodule.php?module=abandoncastle&loc=6");
		} else {
			output("You tug on the door, however you cannot get it to open.`n");
			output("Come back when you are a stronger and more experienced warrior.`n");
			if (get_module_setting("forestvil") == 1){
				addnav("Continue","forest.php");
			}else{
				villagenav();
			}
		}
	}

	//now let's navigate the maze
	if ($op <> ""){
		if ($op == "n") {
			$locale+=11;
			redirect("runmodule.php?module=abandoncastle&loc=$locale");
		}
		if ($op == "s"){
			$locale-=11;
			redirect("runmodule.php?module=abandoncastle&loc=$locale");
		}
		if ($op == "w"){
			$locale-=1;
			redirect("runmodule.php?module=abandoncastle&loc=$locale");
		}
		if ($op == "e"){
			$locale+=1;
			redirect("runmodule.php?module=abandoncastle&loc=$locale");
		}
		
	}else{
		if ($locale <> ""){
			//now deal with random events good stuff first
			//good stuff diminshes the longer player is in the maze
			//this is big... with lots of cases to help keep options open for future events
			//the lower cases should be good things the best at the lowest number 
			//and the opposite for bad things
			$maze=explode(",", $umaze);
			if ($locale=="") $locale = $upqtemp;
			$upqtemp = $locale;
			set_module_pref("pqtemp", $upqtemp);
			for ($i=0;$i<$locale-1;$i++){
			}
			$navigate=ltrim($maze[$i]);
			output("`4");
			if ($navigate <> "z"){
				switch(e_rand($umazeturn,2500)){
					case 1:
					case 2:
					case 3:
					case 4:
					case 5:
					case 6:
					case 7:
					case 8:
					case 9:
					case 10:
						output("Lucky Day!  You find a Gem!");
						$session['user']['gems']+=1;
						break;
					case 11:
					case 12:
					case 13:
					case 14:
					case 15:
					case 16:
					case 17:
					case 18:
					case 19:
					case 20:
						output("Lucky Day! You find 500 gold!");
						$session['user']['gold']+=500;
						break;
					case 21:
					case 22:
					case 23:
					case 24:
					case 25:
					case 26:
					case 27:
					case 28:
					case 29:
					case 30:
						output("Lucky Day! You find 450 gold!");
						$session['user']['gold']+=450;
						break;
					case 31:
					case 32:
					case 33:
					case 34:
					case 35:
					case 36:
					case 37:
					case 38:
					case 39:
					case 40:
						output("Lucky Day! You find 400 gold!");
						$session['user']['gold']+=400;
						break;
					case 41:
					case 42:
					case 43:
					case 44:
					case 45:
					case 46:
					case 47:
					case 48:
					case 49:
					case 50:
						output("Lucky Day! You find 350 gold!");
						$session['user']['gold']+=350;
						break;
					case 51:
					case 52:
					case 53:
					case 54:
					case 55:
					case 56:
					case 57:
					case 58:
					case 59:
					case 60:
						output("Lucky Day! You find 300 gold!");
						$session['user']['gold']+=300;
						break;
					case 61:
					case 62:
					case 63:
					case 64:
					case 65:
					case 66:
					case 67:
					case 68:
					case 69:
					case 70:
						output("Lucky Day! You find 250 gold!");
						$session['user']['gold']+=250;
						break;
					case 71:
					case 72:
					case 73:
					case 74:
					case 75:
					case 76:
					case 77:
					case 78:
					case 79:
					case 80:
						output("Lucky Day! You find 200 gold!");
						$session['user']['gold']+=200;
						break;
					case 81:
					case 82:
					case 83:
					case 84:
					case 85:
					case 86:
					case 87:
					case 88:
					case 89:
					case 90:
						output("Lucky Day! You find 150 gold!");
						$session['user']['gold']+=150;
						break;
					case 91:
					case 92:
					case 93:
					case 94:
					case 95:
					case 96:
					case 97:
					case 98:
					case 99:
					case 100:
						output("Lucky Day! You find 100 gold!");
						$session['user']['gold']+=100;
						break;
					case 101:
					case 102:
					case 103:
					case 104:
					case 105:
					case 106:
					case 107:
					case 108:
					case 109:
					case 110:
						output("Lucky Day! You find 50 gold!");
						$session['user']['gold']+=50;
						break;
					case 111:
					case 112:
					case 113:
					case 114:
					case 115:
					case 116:
					case 117:
					case 118:
					case 119:
					case 120:
						output("Lucky Day! You find 50 gold!");
						$session['user']['gold']+=50;
						break;
					case 121:
					case 122:
						if (is_module_active('potions')) {
							$upotion = get_module_pref('potion', 'potions');
							if ($upotion<5){
								output("Lucky Day! You find a Healing Potion!");
								set_module_pref('potion', ++$upotion, 'potions');
							}
						}
						break;
					case 123:
					case 124:
						if (is_module_active('usechow')) {
							$uchow  = get_module_pref("chow", "usechow");
							for ($i=0;$i<6;$i+=1){
								$chow[$i]=substr(strval($uchow),$i,1);
								if ($chow[$i] > 0) $userchow++;
							}
							if ($userchow<5){
								switch(e_rand(1,7)){
									case 1:
										output("`^Fortune smiles on you and you find a slice of bread!`0");
										for ($i=0;$i<6;$i+=1){
											$chow[$i]=substr(strval($uchow),$i,1);
											if ($chow[$i]=="0" and $done < 1){
												$chow[$i]="1";
												$done = 1;
											}
											$newchow.=$chow[$i];
										}
										break;
									case 2:
										output("`^Fortune smiles on you and you find a Pork Chop!`0");
										for ($i=0;$i<6;$i+=1){
											$chow[$i]=substr(strval($uchow),$i,1);
											if ($chow[$i]=="0" and $done < 1){
												$chow[$i]="2";
												$done = 1;
											}
											$newchow.=$chow[$i];
										}
										break;
									case 3:
										output("`^Fortune smiles on you and you find a Ham Steak!`0");
										for ($i=0;$i<6;$i+=1){
											$chow[$i]=substr(strval($uchow),$i,1);
											if ($chow[$i]=="0" and $done < 1){
												$chow[$i]="3";
												$done = 1;
											}
											$newchow.=$chow[$i];
										}
										break;
									case 4:
										output("`^Fortune smiles on you and you find a Steak!`0");
										for ($i=0;$i<6;$i+=1){
											$chow[$i]=substr(strval($uchow),$i,1);
											if ($chow[$i]=="0" and $done < 1){
												$chow[$i]="4";
												$done = 1;
											}
											$newchow.=$chow[$i];
										}
										break;
									case 5:
										output("`^Fortune smiles on you and you find a Whole Chicken!`0");
										for ($i=0;$i<6;$i+=1){
											$chow[$i]=substr(strval($uchow),$i,1);
											if ($chow[$i]=="0" and $done < 1){
												$chow[$i]="5";
												$done = 1;
											}
											$newchow.=$chow[$i];
										}
										break;
									case 6:
										output("`^Fortune smiles on you and you find a bottle of milk!`0");
										for ($i=0;$i<6;$i+=1){
											$chow[$i]=substr(strval($uchow),$i,1);
											if ($chow[$i]=="0" and $done < 1){
												$chow[$i]="6";
												$done = 1;
											}
											$newchow.=$chow[$i];
										}
										break;
									case 7:
										output("`^Fortune smiles on you and you find a bottle of Water!`0");
										for ($i=0;$i<6;$i+=1){
											$chow[$i]=substr(strval($uchow),$i,1);
											if ($chow[$i]=="0" and $done < 1){
												$chow[$i]="7";
												$done = 1;
											}
											$newchow.=$chow[$i];
										}
										break;
								}
								set_module_pref('chow', $newchow, 'usechow');
							}
						}
						break;
					case 125:
					case 126:
					case 127:
					case 128:
					case 129:
					case 130:
						output("Lucky Day! You find 10 gold!");
						$session['user']['gold']+=10;
						break;
					case 131:
					case 132:
					case 133:
					case 134:
					case 135:
					case 136:
					case 137:
					case 138:
					case 139:
					case 140:
						if (is_module_active('lonnycastle')){
						output("You find ");	
						set_module_pref('evil',(get_module_pref('evil','lonnycastle') - 1),'lonnycastle');
						find();
						}
						break;

				case 2321:
				case 2322:
				case 2323:
				case 2324:
				case 2325:
				case 2326:
				case 2327:
				case 2328:
				case 2329:
				case 2330:
					output("You hear a strange and eerie growling sound coming from somewhere.");
					break;
				case 2331:
				case 2332:
				case 2333:
				case 2334:
				case 2335:
				case 2336:
				case 2337:
				case 2338:
				case 2339:
				case 2340:
					output("You hear a blood curling scream coming from somewhere.");
					break;
				case 2341:
				case 2342:
				case 2343:
				case 2344:
				case 2345:
				case 2346:
				case 2347:
				case 2348:
				case 2349:
				case 2350:
					output("You encounter a putrid smell.  ");
					if (is_module_active('odor')){
					output("Some of that smell lingers with you.");
					set_module_pref('odor',(get_module_pref('odor','odor') - 1),'odor');
					}
					break;
				case 2351:
				case 2352:
				case 2353:
				case 2354:
				case 2355:
				case 2356:
				case 2357:
				case 2358:
				case 2359:
				case 2360:
					output("There is a skeleton laying in the corner.  Poor fellow didn't find his way out.");
					break;
				case 2361:
				case 2362:
				case 2363:
				case 2364:
				case 2365:
				case 2366:
				case 2367:
				case 2368:
				case 2369:
				case 2370:
					output("You see a rat chewing on what looks like a hand.");
					break;
				case 2371:
				case 2372:
				case 2373:
				case 2374:
				case 2375:
				case 2376:
				case 2377:
				case 2378:
				case 2379:
				case 2380:
					output("You hear a growl from somewhere very close.");
					break;
				case 2381:
				case 2382:
				case 2383:
				case 2384:
				case 2385:
				case 2386:
				case 2387:
				case 2388:
				case 2389:
				case 2390:
					output("A cold chill comes over you.");
					break;
				case 2391:
				case 2392:
				case 2393:
				case 2394:
				case 2395:
				case 2396:
				case 2397:
				case 2398:
				case 2399:
				case 2400:
					output("You hear screams for help coming from somewhere.");
					break;
				case 2401:
				case 2402:
				case 2403:
				case 2404:
				case 2405:
				case 2406:
				case 2407:
				case 2408:
				case 2409:
				case 2410:
					output("You hear screams for help coming from somewhere close.");
					break;
				case 2411:
				case 2412:
				case 2413:
				case 2414:
				case 2415:
				case 2416:
				case 2417:
				case 2418:
				case 2419:
				case 2420:
					output("You hear screams for help coming from somewhere.  Abruptly the Screaming Stops.");
					break;
				case 2421:
				case 2422:
				case 2423:
				case 2424:
				case 2425:
				case 2426:
				case 2427:
				case 2428:
				case 2429:
				case 2430:
					output("Ouch! You stepped on something sharp!");
					$session['user']['hitpoints']-=1;
					if ($session['user']['hitpoints']<1) $session['user']['hitpoints']=1;
					break;
				case 2431:
				case 2432:
				case 2433:
				case 2434:
				case 2435:
				case 2436:
				case 2437:
				case 2438:
				case 2439:
				case 2440:
					output("Ouch! You were bit by a spider");
					$session['user']['hitpoints']-=2;
					if ($session['user']['hitpoints']<1) $session['user']['hitpoints']=1;
					break;
				case 2441:
				case 2442:
				case 2443:
				case 2444:
				case 2445:
				case 2446:
				case 2447:
				case 2448:
				case 2449:
				case 2450:
					output("Ouch! You were bit by a rat.  The rat scurries away.");
					$session['user']['hitpoints']-=3;
					if ($session['user']['hitpoints']<1) $session['user']['hitpoints']=1;
					break;
				case 2451:
				case 2452:
				case 2453:
				case 2454:
				case 2455:
				case 2456:
				case 2457:
				case 2458:
				case 2459:
				case 2460:
					output("Ouch! You were bit by a big rat.  The rat scurries away.");
					$session['user']['hitpoints']-=4;
					if ($session['user']['hitpoints']<1) $session['user']['hitpoints']=1;
					break;
				case 2461:
				case 2462:
				case 2463:
					output("<big><big><big>`4Wham!<small><small><small>`n",true);
					output("`3As the world goes dim... you see that large spikes have erupted from the floor and impaled you.`n");
					$session['user']['hitpoints']=0;
					$session['user']['alive']=false;
					if (is_module_active("morgue")){
						$diedhow = translate_inline(" - Killed by spikes in the abandonded castle");
						set_module_pref("died",$diedhow,"morgue");
						set_module_pref("killdate",date("Y-m-D h:m:s"),"morgue");
					}
					$session['user']['gold']=0;
					addnews("`% %s `5 went into the Abandoned Castle and never came out.",$session['user']['name']);
					break;
				case 2464:
				case 2465:
				case 2466:
				case 2467:
				case 2468:
				case 2469:
				case 2470:
				case 2471:
					redirect("runmodule.php?module=abandoncastle&op=ghost1");
					break;
				case 2472:
				case 2473:
				case 2474:
				case 2475:
				case 2476:
				case 2477:
				case 2478:
				case 2479:
					redirect("runmodule.php?module=abandoncastle&op=ghost2");
					break;
				case 2480:
				case 2481:
				case 2482:
				case 2483:
				case 2484:
				case 2485:
				case 2486:
					redirect("runmodule.php?module=abandoncastle&op=bat");
					break;
				case 2487:
				case 2488:
				case 2489:
				case 2490:
				case 2491:
				case 2493:
				case 2494:
					redirect("runmodule.php?module=abandoncastle&op=rat");
					break;
				case 2495:
				case 2496:
					redirect("runmodule.php?module=abandoncastle&op=minotaur");	
					break;
				case 2497:
				case 2498:
					output("<big><big><big>`4Wham!<small><small><small>`n",true);
					output("`3As the world goes dim... you see that large spikes have erupted from the wall and impaled you.`n");
					$session['user']['hitpoints']=0;
					$session['user']['alive']=false;
					if (is_module_active("morgue")){
						$diedhow = translate_inline(" - Killed by spikes in the abandonded castle");
						set_module_pref("died",$diedhow,"morgue");
						set_module_pref("killdate",date("Y-m-D h:m:s"),"morgue");
					}
					$session['user']['gold']=0;
					addnews("`% %s `5 went into the Abandoned Castle and never came out.",$session['user']['name']);
					break;
				case 2499:
				case 2500:
					output("<big><big><big>`4Shoop!<small><small><small>`n",true);
					output("`3As the world goes dim... you see your body fall to the floor level where your head is laying.`n");
					$session['user']['hitpoints']=0;
					$session['user']['alive']=false;
					if (is_module_active("morgue")){
						$diedhow = translate_inline(" - Beheaded in the abandonded castle");
						set_module_pref("died",$diedhow,"morgue");
						set_module_pref("killdate",date("Y-m-D h:m:s"),"morgue");
					}
					$session['user']['gold']=0;
					addnews("`% %s `5 went into the Abandoned Castle and never came out.",$session['user']['name']);
					break;
				}
			}
			output("`7");	
			if ($navigate<>"z"){
				if ($navigate=="x"){
					output("You fell off of the end of the world!");
					$session['user']['hitpoints']=0;
					$session['user']['alive']=false;
					if (is_module_active("morgue")){
						$diedhow = translate_inline(" - Died in the abandonded castle");
						set_module_pref("died",$diedhow,"morgue");
						set_module_pref("killdate",date("Y-m-D h:m:s"),"morgue");
					}
					$session['user']['gold']=0;
					addnews("`% %s `5 went into the Abandoned Castle and never came out.",$session['user']['name']);
				}
				if ($navigate=="p"){
					output("You fall into a pit filled with spikes, you see the dim light way above you fade, just as your life is fading.`n");
					$session['user']['hitpoints']=0;
					$session['user']['alive']=false;
					if (is_module_active("morgue")){
						$diedhow = translate_inline(" - Killed by spikes in the abandonded castle");
						set_module_pref("died",$diedhow,"morgue");
						set_module_pref("killdate",date("Y-m-D h:m:s"),"morgue");
					}
					$session['user']['gold']=0;
					addnews("`% %s `5 went into the Abandoned Castle and never came out.",$session['user']['name']);
				}
				if ($navigate=="q"){
					output("You step on something on the floor you feel it shift and hear the rush of water.");
					output("The passage quickly fills with water, the world fades as your lungs burn for air.`n");
					$session['user']['hitpoints']=0;
					$session['user']['alive']=false;
					if (is_module_active("morgue")){
						$diedhow = translate_inline(" - Drowned in the abandonded castle");
						set_module_pref("died",$diedhow,"morgue");
						set_module_pref("killdate",date("Y-m-D h:m:s"),"morgue");
					}
					$session['user']['gold']=0;
					addnews("`% %s `5 went into the Abandoned Castle and never came out.",$session['user']['name']);
				}
				if ($navigate=="r"){
					output("You hear a slam come from behind you, when you turn around you see that a door has blocked you into ");
					output("a small section of passageway.  The walls start to rumble and close in on you.  Soon you find out what ");
					output("it is like for a bug under your foot.");
					$session['user']['hitpoints']=0;
					$session['user']['alive']=false;
					if (is_module_active("morgue")){
						$diedhow = translate_inline(" - Squished in the abandonded castle");
						set_module_pref("died",$diedhow,"morgue");
						set_module_pref("killdate",date("Y-m-D h:m:s"),"morgue");
					}
					$session['user']['gold']=0;
					addnews("`% %s `5 went into the Abandoned Castle and never came out.",$session['user']['name']);
				}
				if ($navigate=="s"){
					output("Out of nowhere a blade swings horizontally across your path.");
					output("The world goes dim as the top half of your body slides away from the bottom.`n");
					$session['user']['hitpoints']=0;
					$session['user']['alive']=false;
					if (is_module_active("morgue")){
						$diedhow = translate_inline(" - Sliced in half in the abandonded castle");
						set_module_pref("died",$diedhow,"morgue");
						set_module_pref("killdate",date("Y-m-D h:m:s"),"morgue");
					}
					$session['user']['gold']=0;
					addnews("`% %s `5 went into the Abandoned Castle and never came out.",$session['user']['name']);
				}
				if ($session['user']['hitpoints'] > 0){
					if ($locale=="6"){
						output("`nYou are at the entrance with Passages to the");
					}else{
						output("`nYou are in a Dark Corridor with Passages to the");
					}	
					$umazeturn++;
					set_module_pref('mazeturn', $umazeturn);
					if ($navigate=="a" or $navigate=="b" or $navigate=="e" or $navigate=="f" or $navigate=="g" or $navigate=="j" or $navigate=="k" or $navigate=="l"){
						addnav("North","runmodule.php?module=abandoncastle&op=n&loc=$locale");
						$directions.=" North";
						$navcount++;
					}
					if ($navigate=="a" or $navigate=="c" or $navigate=="e" or $navigate=="f" or $navigate=="g" or $navigate=="h" or $navigate=="i" or $navigate=="m"){
						if ($locale <> 6){
							addnav("South","runmodule.php?module=abandoncastle&op=s&loc=$locale");
							$navcount++;
							if ($navcount > 1) $directions.=",";
							$directions.=" South";
						}
					}
					if ($navigate=="a" or $navigate=="b" or $navigate=="c" or $navigate=="d" or $navigate=="e" or $navigate=="h" or $navigate=="k" or $navigate=="n"){
						addnav("West","runmodule.php?module=abandoncastle&op=w&loc=$locale");
						$navcount++;
						if ($navcount > 1) $directions.=",";
						$directions.=" West";
					}
					if ($navigate=="a" or $navigate=="b" or $navigate=="c" or $navigate=="d" or $navigate=="f" or $navigate=="i" or $navigate=="j" or $navigate=="o"){
						addnav("East","runmodule.php?module=abandoncastle&op=e&loc=$locale");
						$navcount++;
						if ($navcount > 1) $directions.=",";
						$directions.=" East";
					}
					output(" %s.`n",$directions);
				}else{
					addnav("Continue","shades.php");
				}
				$mazemap=$navigate;
				$mazemap.="maze.gif";
				output("<IMG SRC=\"images/%s\">\n",$mazemap,true);
				output("`n");
				output("`n<small>`7You = <img src=\"./images/mcyan.gif\" title=\"\" alt=\"\" style=\"width: 5px; height: 5px;\">`7, Entrance = <img src=\"./images/mgreen.gif\" title=\"\" alt=\"\" style=\"width: 5px; height: 5px;\">`7, Exit = <img src=\"./images/mred.gif\" title=\"\" alt=\"\" style=\"width: 5px; height: 5px;\"><big>",true);
				$mapkey2="<table style=\"height: 130px; width: 110px; text-align: left;\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tbody><tr><td style=\"vertical-align: top;\">";
				for ($i=0;$i<143;$i++){
					if ($i==$locale-1){
						$mapkey.="<img src=\"./images/mcyan.gif\" title=\"\" alt=\"\" style=\"width: 10px; height: 10px;\">";
					}else{
						if ($i==5){
							$mapkey.="<img src=\"./images/mgreen.gif\" title=\"\" alt=\"\" style=\"width: 10px; height: 10px;\">";
						}else{
							if (ltrim($maze[$i])=="z"){
								$exit=$i+1;
								$mapkey.="<img src=\"./images/mred.gif\" title=\"\" alt=\"\" style=\"width: 10px; height: 10px;\">";
							}else{
								$mapkey.="<img src=\"./images/mblack.gif\" title=\"\" alt=\"\" style=\"width: 10px; height: 10px;\">";
							}
						}
					}
					if ($i==10 or $i==21 or $i==32 or $i==43 or $i==54 or $i==65 or $i==76 or $i==87 or $i==98 or $i==109 or $i==120 or $i==131 or $i==142){
						$mapkey="`n".$mapkey;
						$mapkey2=$mapkey.$mapkey2;
						$mapkey="";
					}
				}
				$mapkey2.="</td></tr></tbody></table>";
				output("%s",$mapkey2,true);
				if (get_module_pref('super')){
					output("Superuser Map`n");
					$mapkey2="";
					$mapkey="";
					for ($i=0;$i<143;$i++){
						$keymap=ltrim($maze[$i]);
						$mazemap=$keymap;
						$mazemap.="maze.gif";
						$mapkey.="<img src=\"./images/$mazemap\" title=\"\" alt=\"\" style=\"width: 20px; height: 20px;\">";
						if ($i==10 or $i==21 or $i==32 or $i==43 or $i==54 or $i==65 or $i==76 or $i==87 or $i==98 or $i==109 or $i==120 or $i==131 or $i==142){
							$mapkey="`n".$mapkey;
							$mapkey2=$mapkey.$mapkey2;
							$mapkey="";
						}
					}
					output("%s",$mapkey2,true);
				}
				if (get_module_pref('super')) addnav("Superuser Exit","runmodule.php?module=abandoncastle&loc=$exit");
			}else{
				if (!is_array($session['bufflist']) || count($session['bufflist']) <= 0) {
			  	$session['bufflist'] = unserialize($session['user']['buffbackup']);
			  	if (!is_array($session['bufflist'])) $session['bufflist'] = array();
				}
				if ($session['user']['hashorse']>0){
					global $playermount;
					output("Your %s happily greets you at the exit.`n",$playermount['mountname']);
				}
				output("You have found your way out!`n");
				addnews("`% %s `5 made it out of the Abandoned Castle alive!  In %s moves!",$session['user']['name'],$umazeturn);
				$reward = 1000 - ($umazeturn*10);
				if ($reward < 0) $reward = 0;
				$gemreward = 0;
				if ($umazeturn < 101) $gemreward = 1;
				if ($umazeturn < 76) $gemreward = 2;
				if ($umazeturn < 51) $gemreward = 3;
				if ($umazeturn < 26) $gemreward = 4;
				output("`2You finished the maze in %s turns.`n",$umazeturn);
				output("`2You find a treasure of %s gold and %s gems.`n`n",$reward,$gemreward); 
				if (get_module_setting("forestvil") == 1){
					addnav("Continue","forest.php");
				}else{
					villagenav();
				}
				$session['user']['gold']+=$reward;
				$session['user']['gems']+=$gemreward;
				set_module_pref('maze',"");
				set_module_pref('mazeturn', 0);
				set_module_pref('pqtemp',"");
			}
		}
	}
	//I cannot make you keep this line here but would appreciate it left in.
	rawoutput("<div style=\"text-align: left;\"><a href=\"http://www.pqcomp.com\" target=\"_blank\">Abandonded Castle by Lonny @ http://www.pqcomp.com</a><br>");
	page_footer();
?>