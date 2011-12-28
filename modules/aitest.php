<?php
function aitest_getmoduleinfo() {
	$info = array(
		"name" => "Creature A.I. Script Tester",
		"author" => "Iori",
		"version" => "1.1",
		"category" => "Administrative",
		"download" => "www.dragonprime.net",
		"settings" => array(
			"Script Testing Settings, title",
			"buffhp" => "How much to buff testing enemies' HP (percentage - 100 is no modification), int|100",
			"buffattack" => "How much to buff testing enemies' attack (percentage - 100 is no modification), int|100",
			"buffdefense" => "How much to buff testing enemies' defense (percentage - 100 is no modification), int|100",
			"allowgold" => "Allow gold?, bool|0",
			"allowexp" => "Allow experience?, bool|0",
			"These are in addition to any multipliers applied by other modules., note",
			"scriptonly" => "Only select from creatures with A.I. scripts?, bool|0",
			"allowload" => "Allows option to manually load encounter scripts?, bool|0",
		),
		"prefs" => array(
			"Script Testing Preferences, title",
			"access" => "Does this player have access to this module?, bool|0",
			"The user will also need grotto access in order to access the tester. Megausers will automatically have this option available to them.,note",
		),
	);
	return $info;
}
function aitest_install() {
	module_addhook("superuser");
	return true;
}
function aitest_uninstall() {
	return true;
}
function aitest_dohook($hookname, $args) {
	global $session;
	if (get_module_pref("access") || $session['user']['superuser'] & SU_MEGAUSER) {
		addnav("Creature Testing", "runmodule.php?module=aitest&opz=select");
	}
	return $args;
}
function aitest_run() {
	require("modules/aitest/run.php");
}
?>