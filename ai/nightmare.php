<?php
//Asteroid monster
global $badguy,$enemies,$index;

$badguy['turnstolevelup']+=1;
if ($badguy['turnstolevelup']>=5){
	$badguy['creaturelevel']++;
	$badguy['creatureattack']=$badguy['creaturelevel'];
	$badguy['creaturedefense']=$badguy['creaturelevel'];
	$badguy['turnstolevelup']=0;
	output("`4`bThe Nightmare becomes stronger...`b`0`n");
}

?>