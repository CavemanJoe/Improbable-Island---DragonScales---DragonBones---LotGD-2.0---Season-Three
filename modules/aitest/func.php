<?php
function aitest_loadenemy($badguy) {
	global $session;
	if (is_array($badguy)) {
		$badguy['creaturehealth'] = round($badguy['creaturehealth'] * get_module_setting("buffhp", "aitest") / 100);
		$badguy['creatureattack'] = round($badguy['creatureattack'] * get_module_setting("buffattack", "aitest") / 100);
		$badguy['creaturedefense'] = round($badguy['creaturedefense'] * get_module_setting("buffdefense", "aitest") / 100);
		if (!get_module_setting("allowgold")) $badguy['creaturegold'] = 0;
		if (!get_module_setting("allowexp")) $badguy['creatureexp'] = 0;
	} else {
		$badguy = array(
			"creaturename" => "An Evil Doppelganger of ".$session['user']['name'],
			"creatureattack" => $session['user']['attack'],
			"creaturedefense" => $session['user']['defense'],
			"creaturehealth" => $session['user']['maxhitpoints'],
			"creaturegold" => 0,
			"creatureexp" => 0,
		);
	}
	$badguy = modulehook("aitest", $badguy);
	return $badguy;
}
?>