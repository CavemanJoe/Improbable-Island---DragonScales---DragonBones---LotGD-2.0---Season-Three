<?php
//Asteroid monster
global $badguy,$enemies,$index;
//creaturestartinghealth
if (!isset($badguy['asteroid_split'])){
	$badguy['asteroid_split']=1;
}
if ($badguy['creaturehealth'] < ($badguy['creaturestartinghealth']*0.5)){
	//check to see if this has already split
	if ($badguy['asteroid_split'] <= 3 && !$badguy['alreadysplit']){
		$newrock = $badguy;
		$badguy['alreadysplit']=1;
		$newrock['alreadysplit']=0;
		$newrock['alwaysattacks']=1;
		$newrock['creaturehealth'] = ceil($newrock['creaturehealth']/2);
		$newrock['creaturegold'] = ceil($newrock['creaturegold']/2);
		$newrock['creatureexp'] = ceil($newrock['creatureexp']/2);
		$newrock['creaturestartinghealth'] = $newrock['creaturehealth'];
		switch ($newrock['asteroid_split']){
			case 1:
				output("The Asteroid splits into two smaller Asteroids!`n");
				$newrock['creaturename'] = "Small Asteroid";
			break;
			case 2:
				output("The little Asteroid splits into two even tinier Asteroids!`n");
				$newrock['creaturename'] = "Tiny Asteroid";
			break;
			case 3:
				output("The tiny wee Asteroid splits into two even more miniscule Asteroids!`n");
				$newrock['creaturename'] = "Miniscule Asteroid";
			break;
		}
		$newrock['asteroid_split']+=1;
		$newrock['istarget']=false;
		battle_spawn($newrock);
		$badguy=$newrock;
	}
}

/*

Asteroid opening text

There's something twinkling in the sky.  Something twinkling and leaving a trail of smoke behind it.  Something getting gradually bigger.  Is that a meteorite?  Or a meteoroid?  Or could it even be a bolide?  Not having an astronomer handy to ask, you shrug and call it an asteroid.


*/
?>