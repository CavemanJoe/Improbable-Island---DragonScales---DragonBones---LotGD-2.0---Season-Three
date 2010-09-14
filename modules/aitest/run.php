<?php
page_header("Creature Testing");
global $session;
$opz = httpget("opz");
switch ($opz) {
	case "select":
		addnav("Grotto");
		addnav("Return to Grotto", "superuser.php");
		addnav("Heal");
		addnav("Complete Heal", "runmodule.php?module=aitest&opz=heal");
		output("`n`&Please select an enemy to test in battle.`n`n");
		$cr = db_prefix("creatures");
		if (get_module_setting("scriptonly")) {
			$sql = "SELECT creatureid, creaturelevel, creaturename FROM $cr WHERE creatureaiscript > '' ORDER BY creaturelevel ASC, creaturename ASC";
		} else {
			$sql = "SELECT creatureid, creaturelevel, creaturename FROM $cr ORDER BY creaturelevel ASC, creaturename ASC";
		}
		$result = db_query($sql);
		$level = "";
		require_once("lib/sanitize.php");
		while ($baddie = db_fetch_assoc($result)){
			if ($level != $baddie['creaturelevel']) {
				$list[] = 0;
				$list[] = "=== Level ".$baddie['creaturelevel']." ===";
				$level = $baddie['creaturelevel'];
			}
			$list[] = $baddie['creatureid'];
			$list[] = str_replace(",", " ", sanitize($baddie['creaturename']));
		}
		if (is_array($list) && count($list))
			$listjoin = join(",", $list);
		else
			$listjoin = "0,none,";
		require_once("lib/showform.php");
		rawoutput("<form action='runmodule.php?module=aitest&opz=pre' method='post'>");
		addnav("", "runmodule.php?module=aitest&opz=pre");
		$format = array(
			"id" => "Enemy, enum, ".$listjoin,
			"num" => "Number of enemies, range, 1, 20",
		);
		if (get_module_setting("allowload")) {
			$format['loadscript'] = "-Optional- Filepath and name of manual encounter script (including enemies),text,60";
			$format['note'] = "Do not use this option unless you know what you are doing!,note";
		}
		showform($format, array());
		rawoutput("</form>");
	break;
	case "pre":
		restore_buff_fields();
		$loadscript = httppost("loadscript");
		debug($loadscript);
		if (!empty($loadscript) && file_exists($loadscript)) {
			global $session;
			require($loadscript);
			//it is up to the loaded file to setup and create badguys and options.
		} else {
			$id = httppost("id");
			$num = max(1, httppost("num"));
			$cr = db_prefix("creatures");
			$sql = "SELECT * FROM $cr WHERE creatureid = $id LIMIT 1";
			$res = db_query($sql);
			$origbadguy = db_fetch_assoc($res);
			$stack = array();
			require_once("lib/forestoutcomes.php");
			require_once("modules/aitest/func.php");
			for ($i = 0; $i < $num; $i++) {
				$stack[] = aitest_loadenemy(buffbadguy($origbadguy));
			}
			$attackstack = array(
				"enemies" => $stack,
				"options" => array(
					"type" => "aitest"
				),
			);
			$session['user']['badguy'] = createstring($attackstack);
		}
		calculate_buff_fields();
	case "fighting":
		require("battle.php");
		if ($victory) {
			$gold = 0;
			$totalexp = 0;
			$expbonus = 0;
			$count = 0;
			foreach ($enemies as $index => $badguy) {
				tlschema("battle");
				$msg = translate_inline($badguy['creaturelose']);
				tlschema();
				output_notl("`c`b`&%s`0`b`c", $msg);
				output("`b`\$You have slain %s!`0`b`n", $badguy['creaturename']);
				if (getsetting("dropmingold",0)) {
					$badguy['creaturegold'] = e_rand(round($badguy['creaturegold']/4), round(3*$badguy['creaturegold']/4));
				}else{
					$badguy['creaturegold'] = e_rand(0, $badguy['creaturegold']);
				}
				$gold += $badguy['creaturegold'];
				$expbonus += round($badguy['creatureexp'] * (.2 * ($badguy['creaturelevel']-$session['user']['level'])));
				$count++;
			}
			foreach ($options['experience'] as $index=>$experience) {
				$totalexp += $experience;
			}
			$exp = round($totalexp / $count);
			$gold = e_rand(round($gold/$count), round($gold/$count) * $count);
			$expbonus = round($expbonus/$count);
			if ($gold) {
				output("`#You receive `^%s`# gold!`n", $gold);
			}
			if (getsetting("instantexp",false) == true) {
				$expgained = 0;
				foreach ($options['experiencegained'] as $index => $experience) {
					$expgained += $experience;
				}
				if (count($enemies) > 1) {
					output("`cDuring this fight you received `^%s`# total experience!`c`0",$exp + $expbonus);
				}
				$session['user']['experience'] += $expbonus;
			} else {
				output("You receive `^%s`# total experience!`n`0",$exp + $expbonus);
				$session['user']['experience'] += ($exp + $expbonus);
			}
			$session['user']['gold'] += $gold;
			addnav("Heal");
			addnav("Complete Heal", "runmodule.php?module=aitest&opz=heal");
			addnav("Return");
			addnav("Test Another Monster", "runmodule.php?module=aitest&opz=select");
			addnav("Return to Grotto", "superuser.php");
		} elseif ($defeat) {
			$killer = false;
			$names = array();
			$lastname = "";
			foreach ($enemies as $index => $badguy) {
				if (!empty($badguy['killedplayer'])) {
					$killer = $badguy['creaturename'];
				} elseif ($badguy['creaturehealth'] <= 0) continue;
				$names[] = $badguy['creaturename'];
				if (isset($badguy['creaturewin']) && $badguy['creaturewin'] > "") {
					$msg = translate_inline($badguy['creaturewin'],"battle");
					output_notl("`c`b`&%s`0`b`c",$msg);
				}
			}
			debug($names);
			if (count($names) > 1) $lastname = array_pop($names);
			$enemystring = join(", ", $names);
			debug($enemystring);
			$and = translate_inline("and");
			if (isset($lastname) && $lastname > "") $enemystring = "$enemystring $and $lastname";
			output("`c`b`&You have been slain by `%%s`&!!!`b`c", $killer != false ? $killer : $enemystring);
			$session['user']['hitpoints'] = 1;
			addnav("Heal");
			addnav("Complete Heal", "runmodule.php?module=aitest&opz=heal");
			addnav("Return");
			addnav("Test Another Monster", "runmodule.php?module=aitest&opz=select");
			addnav("Return to Grotto", "superuser.php");
		} else {
			if (httpget("op") == "run") redirect("runmodule.php?module=aitest&opz=select"); 
			else {
				require_once("lib/fightnav.php");
				fightnav(true, true, "runmodule.php?module=aitest&opz=fighting");
			}
		}
	break;
	case "heal":
		$session['user']['hitpoints'] = $session['user']['maxhitpoints'];
		redirect("runmodule.php?module=aitest&opz=select");
	break;
}
page_footer();
?>