<?php

function cities_villagetext(){
	global $session;
	$city = getsetting("villagename", LOCATION_FIELDS);
	$version=getsetting("installer_version","1.1.1");
	if ($version<"2.0.0"){
		if ($session['user']['location']==get_userpref("homecity","cities")){$home = true;}
	} else {
		if ($session['user']['location']==$session['user']['homecity']){$home = true;}
	}
	if ($session['user']['location']==$city){$capital = true;}
	if ($session['user']['location'] == $city){
			// The city needs a name still, but at least now it's a bit
			// more descriptive
			// Let's do this a different way so that things which this
			// module (or any other) resets don't get resurrected.
			$args['text'] = array("All around you, the people of Improbable Central move about their business.  No one seems to pay much attention to you as they all seem absorbed in their own lives and problems.  Along various streets you see many different types of shops, each with a sign out front proclaiming the business done therein.  Off to one side, you see a very curious looking rock which attracts your eye with its strange shape and color.  People are constantly entering and leaving via the city gates to a variety of destinations.`n`n");
			$args['schemas']['text'] = "cities";
			$args['clock']="`n`0The clock on the inn reads `^%s.`0`n";
			$args['schemas']['clock'] = "cities";
			// if (is_module_active("calendar")) {
				// $args['calendar']="`n`0You hear a townsperson say that today is `^%1\$s`0, `^%3\$s %2\$s`0, `^%4\$s`0.`n";
				// $args['schemas']['calendar'] = "module-cities";
			// }
			$args['title']=array("%s, the Capital City",$city);
			$args['schemas']['title'] = "cities";
			$args['fightnav']="Combat Avenue";
			$args['schemas']['fightnav'] = "cities";
			$args['marketnav']="Store Street";
			$args['schemas']['marketnav'] = "cities";
			$args['tavernnav']="Ale Alley";
			$args['schemas']['tavernnav'] = "cities";
			$args['newestplayer']="";
			$args['schemas']['newestplayer'] = "cities";
		}
	if ($home==true){
			//in home city.
			blocknav("inn.php");
			blocknav("stables.php");
			blocknav("rock.php");
			// blocknav("hof.php");
			blocknav("mercenarycamp.php");
		}elseif ($capital==true){
			//in capital city.
			//blocknav("forest.php");
			blocknav("train.php");
			blocknav("weapons.php");
			blocknav("armor.php");
		}else{
			//in another city.
			blocknav("train.php");
			blocknav("inn.php");
			blocknav("stables.php");
			blocknav("rock.php");
			blocknav("clans.php");
			// blocknav("hof.php");
			blocknav("mercenarycamp.php");
		}
}


?>
