<?php

//IMPROBABILITY BOMB

function improbabilitybomb_define_item(){
	set_item_setting("allowtransfer","fight","improbabilitybomb");
	set_item_setting("context_fight","1","improbabilitybomb");
	set_item_setting("cratefind","2","improbabilitybomb");
	set_item_setting("description","Improbability Bombs are used in combat. Their effects are somewhat unpredictable. Be careful with these.","improbabilitybomb");
	set_item_setting("destroyafteruse","true","improbabilitybomb");
	set_item_setting("eboy","true","improbabilitybomb");
	set_item_setting("giftable","true","improbabilitybomb");
	set_item_setting("image","improbabilitybomb.png","improbabilitybomb");
	set_item_setting("verbosename","Improbability Bomb","improbabilitybomb");
	set_item_setting("weight","1.5","improbabilitybomb");
	set_item_setting("require_file","improbabilitybomb.php","improbabilitybomb");
	set_item_setting("call_function","improbabilitybomb_use","improbabilitybomb");
}

function improbabilitybomb_use($args){
	global $session;
	$effect = e_rand(1,8);
	if (has_buff("ibomb7a") && $effect == 7){
		$effect = 3;
	}
	if (has_buff("ibomb8") && $effect == 8){
		$effect = 3;
	}
	apply_buff('startmsg', array(
		"rounds"=>1,
		"atkmod"=>1,
		"startmsg"=>"`0You light the fuse on the Improbability Bomb and toss it towards your opponent.",
		"schema"=>"improbabilitybomb"
	));
	switch ($effect){
		case 1:
			apply_buff('ibomb1', array(
				"rounds"=>1,
				"atkmod"=>1,
				"startmsg"=>"`0The bomb bursts into a shower of Requisition tokens!  Blimey, there must be about a thousand of them!  What's more, all these tokens are swirling into the air and nose-diving straight into your pocket.  Result!",
				"schema"=>"improbabilitybomb"
			));
			$gold = e_rand(900,1100);
			$session['user']['gold'] += $gold;
			break;
		case 2:
			apply_buff('ibomb2', array(
				"rounds"=>1,
				"atkmod"=>1,
				"startmsg"=>"`0The fuse fizzes and sparks, until eventually... it goes out.  The bomb is gone.  However, there's a tasty, tasty cigarette in its place!  You grab it before your enemy gets the chance.",
				"schema"=>"improbabilitybomb"
			));
			$session['user']['gems']++;
			break;
		case 3:
		case 4:
			apply_buff('ibomb3', array(
				"rounds"=>1,
				"minioncount"=>1,
				"minbadguydamage"=>"5+round(<attack>*1.0,0);",
				"maxbadguydamage"=>"5+round(<attack>*3.0,0);",
				"effectmsg"=>"`4The bomb explodes close enough to {badguy}`4 to do `^{damage}`4 damage!",
				"schema"=>"improbabilitybomb"
			));
			break;
		case 5:
			apply_buff('ibomb6', array(
				"rounds"=>1,
				"atkmod"=>1,
				"startmsg"=>"`0The Improbability Bomb breaks open, bathing you in a cool white light.  When it fades, you feel calm, self-confident and somehow more attractive.  Pretty useless in a combat situation, but hey, it's nice to be feel good about yourself.  You gain some Charm.",
				"schema"=>"improbabilitybomb"
			));
			$session['user']['charm']+=1;
			break;
		case 6:
			apply_buff('ibomb7a', array(
				"rounds"=>4,
				"minioncount"=>8,
				"minbadguydamage"=>0,
				"maxbadguydamage"=>5,
				"startmsg"=>"The bomb begins to roll around the theater of combat, bouncing off rocks like a pinball - and firing out showers of white-hot sparks!",
				"effectmsg"=>"`2A glowing spark leaps onto {badguy}, burning it for {damage} points!",
				"schema"=>"improbabilitybomb",
				"expireafterfight"=>1,
			));
			apply_buff('ibomb7b', array(
				"rounds"=>4,
				"minioncount"=>8,
				"mingoodguydamage"=>0,
				"maxgoodguydamage"=>5,
				"effectmsg"=>"`4A white-hot spark attaches to you, burning you for {damage} points!",
				"schema"=>"improbabilitybomb",
				"wearoff"=>"The bomb fizzles out and sends out one last dying volley of sparks.",
				"expireafterfight"=>1,
			));
			break;
		case 7:
			apply_buff('ibomb8', array(
				"startmsg"=>"`0The bomb uncurls, revealing a little `5Purple Monster!`0",
				"rounds"=>-1,
				"name"=>"`5Purple Monster`0",
				"minioncount"=>1,
				"minbadguydamage"=>5,
				"maxbadguydamage"=>50,
				"effectmsg"=>"`5The Purple Monster leaps towards {badguy} and bites down hard for {damage} damage!`0",
				"schema"=>"improbabilitybomb",
				"wearoff"=>"`5The Purple Monster, seeing its business here concluded, disappears with a faint 'pop.'`0",
				"expireafterfight"=>1,
			));
			break;
		case 8:
			$maxdmg = $session['user']['maxhitpoints']*2;
			if ($maxdmg < 500){
				$maxdmg = 500;
			}
			$mindmg = $session['user']['hitpoints']*0.5;
			if ($maxdmg < 200){
				$maxdmg = 200;
			}
			apply_buff('ibomb9', array(
				"rounds"=>1,
				"minioncount"=>1,
				"mingoodguydamage"=>$mindmg,
				"maxgoodguydamage"=>$maxdmg,
				"effectmsg"=>"`4Before the bomb even leaves your hand, it blows up in your face!  The explosion causes {damage} points!",
				"schema"=>"improbabilitybomb",
				"expireafterfight"=>1,
			));
		break;
	}

}

?>