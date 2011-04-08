<?php

function timeandweather_outposts_getmoduleinfo(){
	$info = array(
		"name"=>"Time and Weather: Outposts",
		"version"=>"2011-03-31",
		"author"=>"Dan Hall / Emily Hall, ImprobableIsland.com",
		"category"=>"Time and Weather",
		"download"=>"",
	);
	return $info;
}

function timeandweather_outposts_install(){
	module_addhook_priority("villagetext",100);
	return true;
}

function timeandweather_outposts_uninstall(){
	return true;
}

function timeandweather_outposts_dohook($hookname,$args){
	global $session,$outdoors,$shady,$rainy,$brightness;
	switch($hookname){
		case "villagetext":
			//debug($args);
			$outdoors = true;
			require_once "modules/timeandweather.php";
			$stateinfo = timeandweather_getcurrent();
			//debug($session['user']['location']);
			switch ($session['user']['location']){
				case "NewHome":
					$shady = false;
					switch($stateinfo['timezone']){
						case 1:
							//dawn
							switch ($stateinfo['weather']){
								case 1:
									$t = "A chilly fog hangs barely visible in the red dawn light.  Tiny fingers of frost stretch out from the wet ground, making a crunching noise under your feet.";
								break;
								case 2:
									$t = "The red dawn light does little to illuminate the dense mist that hangs over the outpost.  A few early risers are huddled in a knot, chatting near the museum.";
								break;
								case 3:
									$t = "There's a chill in the red dawn air, as you take stock of your surroundings.  The buildings and vegetation are covered with a thin film of dew.";
								break;
								case 4:
									$t = "Though the sun is a mere glimmer on the horizon, the skies are clear for miles.  A few early risers are taking advantage of the warmth by playing some sort of pitching game.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and the smell is sweet and earthy.";
									}
								break;
								case 5:
									$t = "The reddish dawn peaks through a cold and uncomfortable drizzly rain.  You spy a lonely smoker taking shelter under an awning.";
								break;
								case 6:
									$t = "The faint light of dawn struggles to push through the artificial dark of a pouring rain.  All sensible residents have taken shelter.";
								break;
								case 7:
									$t = "Dawn seems to have been rescheduled by the weather. Though it's early day, thunder plays angry counterpoint to the dark of a stormy rain.  There is no sign of inhabitants - most likely the residents are all snug in their beds.";
								break;
							}
						break;
						case 2:
							//sunrise
							switch ($stateinfo['weather']){
								case 1:
									$t = "A hazy mist hangs in the cool air, softened by the glow of a full sunrise.  A few locals begin to stir, preparing for a scorchingly hot day.";
								break;
								case 2:
									$t = "The warm sunrise glints softly off the morning dew, betraying that the coming day may be a hot one.";
								break;
								case 3:
									$t = "The red and orange of a spectacular sunrise lights up the outpost.  The cloudless sky makes you feel you can see forever.  A few couples walk hand in hand in the early light, admiring the view.";
								break;
								case 4:
									$t = "The warm air seems to make the reds and golds of a perfect sunrise even more intense.  The cloudless sky affords a spectacular view of the ocean.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and the smell is sweet and earthy.";
									}
								break;
								case 5:
									$t = "The sun is barely visible amidst the drizzle.  The cool wet air makes you shiver, as you look around for somewhere warmer to take shelter.";
								break;
								case 6:
									$t = "There's a sunrise somewhere, but it's not here.  The heavy rain pulls a curtain of artificial night over the sleepy outpost.  You spy a huddling smoker taking shelter under a nearby ledge.";
								break;
								case 7:
									$t = "The sun has been completely pushed back by the buffeting gales.  A strong wind whips pebbles at your face, and you narrowly miss stepping in a growing puddle of mud that looks deep enough to reach your knees.  The locals know better than to be out in this weather, and are likely sheltering safely elsewhere.";
								break;
							}
						break;
						case 3:
							//morning
							switch ($stateinfo['weather']){
								case 1:
									$t = "The morning air is thick and muggy, and makes you feel as if you're walking through a warm bowl of soup.   A few locals seem to have gathered for open-air exercise.  You admire their fortitude.";
								break;
								case 2:
									$t = "The sun is out in full force today; you can almost see the steam rise from your brow.  Some locals are taking advantage of the sunny day to play a strange variety of cricket.";
								break;
								case 3:
									$t = "It is a warm, sunny morning in NewHome.  Some residents are engaged in conversation outside the Museum.";
								break;
								case 4:
									$t = "It's a beautiful clear day, and all of NewHome appears to be outside.  The outpost is crowded with residents milling about, engaged in daily tasks and cheerful conversation.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and the smell is sweet and earthy.";
									}
								break;
								case 5:
									$t = "You catch sight of some small incoming clouds in the morning light, and before you can think to worry about an umbrella, it begins to sprinkle.  The residents quickly take shelter in surrounding establishments.";
								break;
								case 6:
									$t = "The darkening morning sky is full of dense, angry looking clouds.  You find yourself caught in a torrential downpour, and start to wonder if there's anywhere around to buy an umbrella.";
								break;
								case 7:
									$t = "The dark morning sky is split by lightning and the crash of thunder all around.  The outpost's residents appear to all be taking shelter.";
								break;
							}
						break;
						case 4:
							//afternoon
							switch ($stateinfo['weather']){
								case 1:
									$t = "The high sun makes the unrelenting humidity seem even worse.  Some residents have taken shelter under the shade of a nearby tree.";
								break;
								case 2:
									$t = "The afternoon sun is hot, and makes sweat bead on your forehead.  Some residents enjoy cool drinks under a nearby tree.";
								break;
								case 3:
									$t = "The afternoon sun is warm and comforting. Some residents are engaged in conversation outside the Museum.";
								break;
								case 4:
									$t = "The sun is warm and comforting in the clear afternoon sky. Some residents are engaged in conversation outside the Museum.  Other locals enjoy a cool drink under a nearby tree.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, but the high sun is quickly drying up the moisture.";
									}
								break;
								case 5:
									$t = "The mild afternoon sky has begun to darken, and light rain showers sprinkle the ground.  A few smokers have taken shelter under a nearby awning.";
								break;
								case 6:
									$t = "The afternoon sky is dark with clouds.  A torrential downpour has made many muddy puddles, which you try to avoid as you pick your way through the outposts.  The residents seem to have taken shelter in nearby establishments.";
								break;
								case 7:
									$t = "The afternoon sky is dark with angry clouds.  Flashes of lightning and crashes of thunder punctuate a heavy rain.  Everyone with sense and means has taken shelter.";
								break;
							}
						break;
						case 5:
							//sunset
							switch ($stateinfo['weather']){
								case 1:
									$t = "Though the sun is beginning to go down, the evening is still hot.  Small groups of residents mill about, chatting as they prepare to end the day.  Others take flames to nearby torches and oil lamps.";
								break;
								case 2:
									$t = "The evening sky is still bright as the sun begins to go down.  Several residents are taking advantage of the lingering warmth, chatting in small knots as the day winds down.  Others wander from lamp to lamp, lighting them in preparation for the coming night.";
								break;
								case 3:
									$t = "The evening sky is bright and clear as the sun begins to set.  You hear small murmurs of activity as locals wind down their business and prepare to turn in for the night.  Others wander around lighting the oil lamps that will keep NewHome illuminated and safe during the night.";
								break;
								case 4:
									$t = "The chilly evening sky is bright and lovely, as you watch a perfect sunset.  Locals chat in small knots, as they make their way back to their homes and prepare to turn in for the night.  Others wander around lighting the oil lamps that will keep NewHome illuminated and safe during the night.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain.";
									}
								break;
								case 5:
									$t = "You can barely see the sun set from behind its blanket of clouds.  You hear the murmur of activity begin to wind down, as locals prepare to turn in for the night.  Others wander around lighting the oil lamps that will keep NewHome illuminated and safe during the night.";
								break;
								case 6:
									$t = "You know it's evening, but you can't see the sunset through the darkening rain. The residents are hurriedly finishing up their tasks, in an effort to quickly return home.  The torches around the Outpost are being lit, and they burn brightly despite the rain.";
								break;
								case 7:
									$t = "The evening sun is nowhere to be seen amidst the crackle and flash of the storm.  Residents quickly seek shelter from the driving wind and pouring rain as the torches that light the outpost sputter and die.";
								break;
							}
						break;
						case 6:
							//dusk
							switch ($stateinfo['weather']){
								case 1:
									$t = "The evening air is warm and damp.  You catch sight of a few flashlights and some lanterns, as residents continue to converse in the dark of the evening.";
								break;
								case 2:
									$t = "The darkening evening air is moist on your brow. Some residents are engaged in conversation in the pool of light outside the Museum.";
								break;
								case 3:
									$t = "The sun has disappeared, and the stars are beginning to peek though the dome of the clear sky.";
								break;
								case 4:
									$t = "Though the sun has set, the cool, cloudless sky is lit by a million tiny pinpoints of starlight.  A few residents recline on the ground, stargazing.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and you wonder if there will be ground frost tonight.";
									}
								break;
								case 5:
									$t = "The sun has gone to bed, and the sticky night air feels like a warm bowl of soup.  A few residents are engaged in conversation outside the Museum, despite the fine drizzle of rain.  You understand why the Museum seems so attractive.";
								break;
								case 6:
									$t = "The sky has opened up and pours down rain, blotting out the light of the stars and moon, casting NewHome into eerie darkness puncutated by sputtering torches.  The locals seem to have taken shelter elsewhere.";
								break;
								case 7:
									$t = "The blackness of the sky is lit only by the intermittent flash of lightning and the occasional struggling torch.  Harsh wind and torrential rain hammers against you.  The sensible residents seem to have taken shelter.";
								break;
							}
						break;
						case 7:
							//night
							switch ($stateinfo['weather']){
								case 1:
									$t = "The cold night air is lit up with the many stars in their improbable alignments.  You're not sure whether these alien constellations would be visible outside of the Improbability Bubble surrounding the Island.";
								break;
								case 2:
									$t = "You shiver a bit as you gaze up at the light of what seems like a million stars.";
								break;
								case 3:
									$t = "Though the sun is gone, pinpoints of starlight illuminate the mild air.  Looking up, you can see the constellation 'Sneaky Lion.'";
								break;
								case 4:
									$t = "Though the sun is long gone, the air is still thick and warm from the heat of the day.  Some residents appear to be having a stargazing party, and point out various familiar forms.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and you wonder if there will be ground frost tonight.";
									}
								break;
								case 5:
									$t = "Though the sun is long gone, the air is thick and moist, with a fine drizzle of cold, lazy rain.  The stars are hiding behind a thick bank of clouds tonight - if it weren't for the torches dotted about the Outpost, NewHome would be pitch black.";
								break;
								case 6:
									$t = "The night is dark, and the sky has opened up and turned into a curtain of black rain.";
								break;
								case 7:
									$t = "The night is indeed dark and stormy.  Loud claps of thunder are punctuated by the high pitched whistle of heavy winds.  The locals appear to be sheltering elsewhere.";
								break;
							}
						break;
					}
					$args['clock'] = "`0".$t."`n`n";
				break;
				case "Kittania":
					$shady = true;
					switch($stateinfo['timezone']){
						case 1:
							//dawn
							switch ($stateinfo['weather']){
								case 1:
									$t = "Kittania stirs from a cold night.  The sun is not yet peeking over the horizon, but the lightening sky betrays a fine mist that hangs over a thin layer of ground frost.  The few torches illuminating the village have extinguished, and there's enough dawn light that the sparse buildings are now just visible between the huge, old oak trees.  Kittania, unlike other Outposts, wasn't cleared from the Jungle but rather built around it - the overall effect is as if someone wedged a few wooden buildings in wherever they would fit amongst the ancient trees, threw up a wall around the whole lot in a rough square, and called it a day.";
								break;
								case 2:
									$t = "The sun has not yet risen, but there's enough pre-dawn light to make out the buildings lurking in between the huge old oak trees that make up much of Kittania.  A fine layer of mist carpets the ground, obscuring the dense tangle of tree roots that delight in tripping newcomers.  Overhead, between the branches, you can see the sky beginning to lighten.  The morning is a cold and misty one, and most KittyMorph residents will still be in bed.";
								break;
								case 3:
									$t = "It is a cool, dewy dawn here in Kittania.  This wouldn't be an issue in most Outposts, but in Kittania - where the buildings were erected wherever they would fit between the pre-existing trees - it causes problems.  The ground here, bare earth or grass in most Outposts, is a gnarled tangle of huge, old tree roots, and they are now treacherously slippery.  Overhead, the dew on the branches is collecting, forming marble-sized raindrops, ready to fall down the back of your neck.  There's just enough pre-sunrise light for non-KittyMorphs to see where they're going - the night-vision-enhanced natives, of course, have no problems at all.";
								break;
								case 4:
									$t = "It is a warm, clear dawn in Kittania.  The torches illuminating the Outpost have extinguished, and the dim light of just-before-sunrise is beginning to appear.  It looks like it's going to be a pleasant day.  The sky is clear enough that the sunrise should be a good one.  You won't see it here, of course, because Kittania is what happens when you build a city and forget to chop the trees down first; the shops and houses are wedged in wherever they'll fit, and the sky is mostly obscured by the overhead canopy of branches and leaves.  Kittania is a very shady place.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and the smell is sweet and earthy.";
									}
								break;
								case 5:
									$t = "Kittania has not yet seen the sunrise, but it won't be far off.  Dawn light is creeping over the forest Outpost, but a little slower than usual today; the sky is a little dark, and a fine rain is falling.  Because Kittania was built around and between the trees rather than on top of their stumps, you get a little protection from the rain - that is, until the wind picks up and the trees seem to shake themselves like wet dogs, pelting you with marble-sized raindrops.  The Outpost is a little chilly without the sunrise to warm things up, and it smells like... well, like wet forest, which is basically what it is.";
								break;
								case 6:
									$t = "It should be getting a little lighter now, but dawn has been postponed by the heavy clouds rolling past overhead.  The cloud cover combines with the dense forest to keep Kittania just as dark as midnight.  The ground - bare earth or grass in other Outposts, a tangled gnarl of tree roots here - is slippery and treacherous.  The overhead branches provide little protection from the heavy rain, and mostly serve to concentrate smaller droplets into marble-sized projectiles that hit the ground with a smack rather than a tap.  Harsh electric light spills out into the Outpost from eBoy's Trading Station, already doing a roaring trade.  Warm candelight peeks through the leaded windows of The Sunny Spot, a treehouse pub thirty or so feet up in the largest oak.  The morning is cold, and smells of damp moss and wet cat.  Here's hoping that the rain will let up once the sun gets out of bed.";
								break;
								case 7:
									$t = "Were it not for the storm, Kittania would be a pleasant place to be right now.  Dawn has been cancelled.  Kittania is what happens when creatures more cat than human try to build a village in the middle of a dense forest but are too lazy to chop the trees down first, so at the best of times it's a pretty shady place - right now, it's almost pitch black.  The trees aren't so much protecting from the storm as concentrating it, gathering like-minded raindrops together into fist-sized falling puddles that hammer into the ground every time the wind rushes through.  The roar of the wind and the clash of the rain is almost deafening, and the frequent crack of thunder and flash of lightning doesn't help matters at all.  Nearly all KittyMorphs are now wearing clothing of some sort, and most have taken shelter inside the Cool Springs Cafe, a restaurant set in a semi-underground cave.  Even in these conditions, it rarely floods, and if it does there's always the ladder up to the Sunny Spot pub.  Either way, to stay outside in this storm is madness.";
								break;
							}
						break;
						case 2:
							//sunrise
							switch ($stateinfo['weather']){
								case 1:
									$t = "It is a cool, misty morning in Kittania, the sunrise casting rays of brilliant pink and orange light through the dense trees that surround every building.  A sunrise like this usually means scorching hot weather during the day; Kittania, with its perpetual shade, will be an ideal place to hang out today as long as the Outpost remains well-defended.";
								break;
								case 2:
									$t = "The sun rises on a mild, dewy day in Kittania.  The tree roots that make up the ground of this outpost are slippery with dew.  You can just about make out the brilliant orange sunrise through the dense foliage that permeates the outpost.  It will likely be a pleasant day.";
								break;
								case 3:
									$t = "A brilliant, pink-orange sunrise begins to warm the parts of Kittania that aren't in perpetual shade.  Kittania is still mostly dim, because of the dense wall of trees that permeate the Outpost - not out of a green-minded, conservation-centred outlook on life, but because chopping them down just seemed too much like hard work.";
								break;
								case 4:
									$t = "It is a warm, clear sunrise in Kittania, that will hopefully lead to a good morning.  The sun can be seen peeking over the horizon in its predictably overdramatic fashion.  The residents of Kittania - who were too lazy even to chop the trees down before building their Outpost amongst and between them - take no notice of the tedious, showy thing.  They'll get up in their own good time, damn it.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and the smell is sweet and earthy.";
									}
								break;
								case 5:
									$t = "The sunrise is cool and drizzly.  The residents of Kittania, knowing a bad deal when they see one, are mostly still tucked up in bed.  If the weather continues like this, they might just stay there all day.";
								break;
								case 6:
									$t = "The sun is trying its hardest to rise in Kittania.  It's honestly having a pretty hard time.  The trees obscuring most of the Outpost are compounded by the heavy clouds rolling overhead.  Because of the dense foliage, rain is always noisy in Kittania - and between the noise and the shade, the sun feels quite overwhelmed.  If it were a KittyMorph, it would give up and go back to bed.";
								break;
								case 7:
									$t = "Most of the residents of Kittania simply can't be arsed with this morning.  Angry black clouds are rolling past overhead, a harsh wind is blowing, and the Outpost is being pelted with marble-sized raindrops.  The few KittyMorphs that you can see are almost all wearing clothing of some kind, so the storm must be a pretty bad one.  You look over to the East, and note the tiniest orange glow from a sun that's trying its hardest to get noticed behind the dense forest in which Kittania lives.  Between the storm and the ever-present shade, it's not gonna happen.";
								break;
							}
						break;
						case 3:
							//morning
							switch ($stateinfo['weather']){
								case 1:
									$t = "Outside of Kittania, the morning is hot and humid.  Inside of Kittania, it's just bloody humid.  The Outpost of Kittania was built without first cutting down the trees, so there's plenty of shade to go around - nonetheless, seeing as it's a beautiful day and all, the few KittyMorph residents that are out of bed are mostly sparsely-clothed or nude.  That really isn't terribly unusual; depending on how you look at it, KittyMorphs either tend to have no body hangups at all, or tend to be notorious exhibitionists.";
								break;
								case 2:
									$t = "It is a hot, sunny morning in Kittania.  The sunlight filters in shafts and spots through the dense foliage surrounding and penetrating the Outpost.  The KittyMorphs that are awake seem to go about their business at their usual lackadaisical pace, many of them still yawning and drinking cups of the preposterously strong KittyMorph version of what the Humans would call coffee.";
								break;
								case 3:
									$t = "It is a warm, sunny morning in Kittania.  The sunlight filters in shafts and spots through the dense foliage surrounding and penetrating the Outpost.  Robots gather in the few areas with decent direct sunlight, cursing the very idea of coming to this silly, shady place, and wondering why the damned KittyMorphs didn't just chop the trees down when they decided to build a village.  That would have been the `isensible`i thing to do.  They ponder these things because they honestly just don't understand KittyMorphs.";
								break;
								case 4:
									$t = "The morning is clear and sunny, at a pleasant temperature.  Kittania, a shady place by virtue of the KittyMorphs not being arsed to chop down (or even trim) the trees before they started building, is a little cooler than most other Outposts.  That doesn't stop a few of the natives from going naked.  `iNothing`i stops the natives from going naked.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and the smell is sweet and earthy.";
									}
								break;
								case 5:
									$t = "The mostly-pleasant morning is marred only by a light drizzle of rain.  In Kittania, this isn't so bad.  The dense canopy of overhead branches serves to protect you from the rain - at least, until the wind kicks up.  When that happens, there's a miniature downpour - fat, cold raindrops fall from overhead in torrents for about two seconds, then everything's calm again.  They have a way of finding the back of your neck.  The native residents, of course, are used to this behaviour.  When they hear the wind picking up, they break from their slow, lazy pace in a moment of truly frightening swiftness - under hard cover, peer out at the tourists, smile at their misfortune, out and on their way again.  The KittyMorphs have this maneuver down to a fine art.";
								break;
								case 6:
									$t = "Even for the notoriously shady Kittania, the morning is darker than it should be.  Heavy rainclouds are dawdling overhead, pissing down rain onto the forest canopy that covers Kittania.  The ground - bare earth or grass in other Outposts, a tangled gnarl of tree roots here - is slippery and treacherous.  The overhead branches provide little protection from the heavy rain, and mostly serve to concentrate smaller droplets into marble-sized projectiles that hit the ground with a smack rather than a tap.  The morning is cold, and smells of damp moss and wet cat.  Here's hoping that the rain will let up a bit soon - there's nothing KittyMorphs hate more than getting unnecesarily wet and being forced to wear clothing.";
								break;
								case 7:
									$t = "The Outpost is being pelted by a brutal thunderstorm.  Kittania is what happens when creatures more cat than human try to build a village in the middle of a dense forest but are too lazy to chop the trees down first, so at the best of times it's a pretty shady place - right now, it's almost pitch black.  The trees aren't so much protecting from the storm as concentrating it, gathering like-minded raindrops together into fist-sized falling puddles that hammer into the ground every time the wind rushes through.  The roar of the wind and the clash of the rain is almost deafening, and the frequent crack of thunder and flash of lightning doesn't help matters at all.  Nearly all KittyMorphs are now wearing clothing of some sort, and most have taken shelter inside the Cool Springs Cafe, a restaurant set in a semi-underground cave.  Even in these conditions, it rarely floods, and if it does there's always the ladder up to the Sunny Spot pub.  Either way, to stay outside in this storm is madness.";
								break;
							}
						break;
						case 4:
							//afternoon
							switch ($stateinfo['weather']){
								case 1:
									$t = "Outside of Kittania, the afternoon is hot and humid.  Inside of Kittania, it's just bloody humid.  The Outpost of Kittania was built without first cutting down the trees, so there's plenty of shade to go around - nonetheless, seeing as it's a beautiful day and all, the KittyMorph residents in the Outpost are mostly sparsely-clothed or nude.  That really isn't terribly unusual; depending on how you look at it, KittyMorphs either tend to have no body hangups at all, or tend to be notorious exhibitionists.";
								break;
								case 2:
									$t = "It is a hot, sunny afternoon in Kittania.  The sunlight filters in shafts and spots through the dense foliage surrounding and penetrating the Outpost.  The KittyMorphs bustle back and forth about their business, having finally woken up.";
								break;
								case 3:
									$t = "It is a warm, sunny afternoon in Kittania, and the trees rustle with life.  The sunlight filters in shafts and spots through the dense foliage surrounding and penetrating the Outpost.  Robots gather in the few areas with decent direct sunlight, cursing the very idea of coming to this silly, shady place, and wondering why the damned KittyMorphs didn't just chop the trees down when they decided to build a village.  That would have been the `isensible`i thing to do.  They ponder these things because they honestly just don't understand KittyMorphs.";
								break;
								case 4:
									$t = "The afternoon is clear and sunny, at a pleasant temperature.  Kittania, a shady place by virtue of the KittyMorphs not being arsed to chop down (or even trim) the trees before they started building, is a little cooler than most other Outposts.  That doesn't stop a few of the natives from going naked.  `iNothing`i stops the natives from going naked.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, but the high sun is quickly drying up the moisture.";
									}
								break;
								case 5:
									$t = "The mostly-pleasant afternoon is marred only by a light drizzle of rain.  In Kittania, this isn't so bad.  The dense canopy of overhead branches serves to protect you from the rain - at least, until the wind kicks up.  When that happens, there's a miniature downpour - fat, cold raindrops fall from overhead in torrents for about two seconds, then everything's calm again.  They have a way of finding the back of your neck.  The native residents, of course, are used to this behaviour.  When they hear the wind picking up, they break from their slow, lazy pace in a moment of truly frightening swiftness - under hard cover, peer out at the tourists, smile at their misfortune, out and on their way again.  The KittyMorphs have this maneuver down to a fine art.";
								break;
								case 6:
									$t = "Even for the notoriously shady Kittania, the morning is darker than it should be.  Heavy rainclouds are dawdling overhead, pissing down rain onto the forest canopy that covers Kittania.  The ground - bare earth or grass in other Outposts, a tangled gnarl of tree roots here - is slippery and treacherous.  The overhead branches provide little protection from the heavy rain, and mostly serve to concentrate smaller droplets into marble-sized projectiles that hit the ground with a smack rather than a tap.  The morning is cold, and smells of damp moss and wet cat.  Here's hoping that the rain will let up a bit soon - there's nothing KittyMorphs hate more than getting unnecesarily wet and being forced to wear clothing.";
								break;
								case 7:
									$t = "The Outpost is being pelted by a brutal thunderstorm.  Kittania is what happens when creatures more cat than human try to build a village in the middle of a dense forest but are too lazy to chop the trees down first, so at the best of times it's a pretty shady place - right now, it's almost pitch black.  The trees aren't so much protecting from the storm as concentrating it, gathering like-minded raindrops together into fist-sized falling puddles that hammer into the ground every time the wind rushes through.  The roar of the wind and the clash of the rain is almost deafening, and the frequent crack of thunder and flash of lightning doesn't help matters at all.  Nearly all KittyMorphs are now wearing clothing of some sort, and most have taken shelter inside the Cool Springs Cafe, a restaurant set in a semi-underground cave.  Even in these conditions, it rarely floods, and if it does there's always the ladder up to the Sunny Spot pub.  Either way, to stay outside in this storm is madness.";
								break;
							}
						break;
						case 5:
							//sunset
							switch ($stateinfo['weather']){
								case 1:
									$t = "In the shady Outpost of Kittania, the sun is yawning, stretching, and preparing to call the job done.  The day has been a hot one, and Kittania has been popular for its dense shady areas.  The KittyMorph residents are almost all awake and outside by now, enjoying the brilliant colours as the sun sets between the trees.";
								break;
								case 2:
									$t = "The day has been a warm one, and the sun is setting now.  For an hour or so, if you look up through the trees in Kittania, you'll see jade on amber.  The occasional call of some horny bird or another can be heard overhead.";
								break;
								case 3:
									$t = "Kittania's sunset can be seen through the dense jungle in which the Outpost lives.  The Outpost rustles with even the softest breeze.  The sunset is clear, bright, and vivid, sending speckles of orange light through the thick overhead branches.";
								break;
								case 4:
									$t = "The sun is setting over the shady Outpost of Kittania.  A cool breeze blows, rustling the thick foliage that shrouds the Outpost.  Through the trees, you see baby blues, blood reds, deep oranges, hot pinks.  The musk of woodsmoke hangs in the air, and beneath it, the omnipresent sweet smell of the forest.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain.";
									}
								break;
								case 5:
									$t = "The sun is setting in Kittania.  The darkening clouds overhead make this sunset a darker one than usual; a light drizzle falls upon the trees that surround and permeate the Outpost, and those trees serve to condense the fine rain into large but infrequent drops.  The smell of damp, mossy forest is strong now, and fights with the scents of Maiko's spices and the Sunny Spot's ales.";
								break;
								case 6:
									$t = "The sunset in Kittania has been cancelled for today.  The thick black clouds rolling overhead have blotted out the sun's yawning display, darkening the already shady Outpost.  The rain patters and splashes on overhead branches, condensing into huge, falling puddles.  The Outpost is getting colder and darker as the rain keeps up.  The smell of heavy tar mixes in with the usual wet forest and cat; some KittyMorphs are lighting the torches early tonight, and you can't blame them.";
								break;
								case 7:
									$t = "The sun's final glow has been extinguished by the storm.  Kittania is what happens when creatures more cat than human try to build a village in the middle of a dense forest but are too lazy to chop the trees down first, so at the best of times it's a pretty shady place - right now, you can only see a damned thing because of the weatherproof torches scattered around the Outpost.  The trees aren't so much protecting from the storm as concentrating it, gathering like-minded raindrops together into fist-sized falling puddles that hammer into the ground every time the wind rushes through.  The roar of the wind and the clash of the rain is almost deafening, and the frequent crack of thunder and flash of lightning doesn't help matters at all.  Nearly all KittyMorphs are now wearing clothing of some sort, and most have taken shelter inside the Cool Springs Cafe, a restaurant set in a semi-underground cave.  Even in these conditions, it rarely floods, and if it does there's always the ladder up to the Sunny Spot pub.  Either way, to stay outside in this storm is madness.";
								break;
							}
						break;
						case 6:
							//dusk
							switch ($stateinfo['weather']){
								case 1:
									$t = "The sun has fallen beneath the horizon, and Kittania is immersed in a warm, damp dusk.  The lamps arranged at the bases of the trees have been lit, and the shady Outpost now stands in a warm yellow glow.  The evening dew makes the tree roots slippery, and the ground is thoroughly treacherous; the local KittyMorphs, being of course used to it by now, hop gaily about where the Human inhabitants have trouble finding their feet.";
								break;
								case 2:
									$t = "The sun has fallen beneath the horizon, and Kittania is immersed in a warm, damp dusk.  The lamps arranged between the trees have been lit, and the shady Outpost now stands in a warm yellow glow.  The evening air is mild and humid, and carries a heavy scent of wet cat.";
								break;
								case 3:
									$t = "The sun has set, although there's still just about enough light to see by.  Less than in other Outposts, of course; the residents of Kittania thought it too much trouble to clear the trees before declaring a place their home, and the resultant perpetual shade makes this Outpost darker than usual.  The residents are of course used to it, and their improved night vision helps a lot.";
								break;
								case 4:
									$t = "The skies are clear over Kittania, making this dusk a cool one.  The sun has set, and the shady Outpost is quickly losing the heat that it absorbed during the day.  Lamps are lit between the trees, casting multiple black-on-yellow catseye shadows from each of the passers-by.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and you wonder if there will be ground frost tonight.";
									}
								break;
								case 5:
									$t = "Although the sun has only just set, Kittania is dark; the clouds rolling overhead, and the natural shadiness of this place, have conspired to provoke an early lighting of the lamps dotted around between the trees.  Overhead, inviting light shines through the windows of the Sunny Spot, a pub set in the broadest and tallest old oak tree.  The humidity is high, and it feels as though there might be rain soon.";
								break;
								case 6:
									$t = "Dusk has turned to night in Kittania; the sun's only just set, but the twilight has been cancelled and replaced with a dark, rainy end to the day.  The lamps dotted around the Outpost have been lit, and they cast warm yellow light through the falling raindrops.  The Outpost smells like a wet forest full of damp cats, which is a remarkable coincidence because that's exactly what it is.";
								break;
								case 7:
									$t = "It is a dark and stormy night.  Kittania is what happens when creatures more cat than human try to build a village in the middle of a dense forest but are too lazy to chop the trees down first, so at the best of times it's a pretty shady place - right now, it's almost pitch black.  The trees aren't so much protecting from the storm as concentrating it, gathering like-minded raindrops together into fist-sized falling puddles that hammer into the ground every time the wind rushes through.  The roar of the wind and the clash of the rain is almost deafening, and the frequent crack of thunder and flash of lightning doesn't help matters at all.  Nearly all KittyMorphs are now wearing clothing of some sort, and most have taken shelter inside the Cool Springs Cafe, a restaurant set in a semi-underground cave.  Even in these conditions, it rarely floods, and if it does there's always the ladder up to the Sunny Spot pub.  Either way, to stay outside in this storm is madness.";
								break;
							}
						break;
						case 7:
							//night
							switch ($stateinfo['weather']){
								case 1:
									$t = "The stars and moon are very bright on this cold, cloudless night.  Looking up, the outline of each leaf is still visible.  Torches have been placed around Kittania, but they're fewer and further between than they would be in other Outposts; the trees permeating Kittania make open flames riskier than elsewhere, and KittyMorphs have naturally excellent night vision.";
								break;
								case 2:
									$t = "The stars and moon are very bright on this chilly, cloudless night.  Looking up, the outline of each leaf is still visible.  Torches have been placed around Kittania, but they're fewer and further between than they would be in other Outposts; the trees permeating Kittania make open flames riskier than elsewhere, and KittyMorphs have naturally excellent night vision.";
								break;
								case 3:
									$t = "It is a clear, still night in Kittania.  This place is what happens when its founders simply pick a random spot in a forest and say \"This'll do\" without cutting down the trees first, so torches are few and far between in this shady place.  Kittania is darker than any other Outpost, at least in part because of the KittyMorphs' naturally enhanced night vision.";
								break;
								case 4:
									$t = "It is a warm, humid night in Kittania.  This place is what happens when its founders simply pick a random spot in a forest and say \"This'll do\" without cutting down the trees first, so torches are few and far between in this shady place.  Kittania is darker than any other Outpost, at least in part because of the KittyMorphs' naturally enhanced night vision.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and you wonder if there will be ground frost tonight.";
									}
								break;
								case 5:
									$t = "Kittania is dark and humid tonight.  This place is what happens when its founders simply pick a random spot in a forest and say \"This'll do\" without cutting down the trees first, so torches are few and far between in this shady place.  A dampness in the air suggests that there might be rain later on.";
								break;
								case 6:
									$t = "The lamps dotted around Kittania have been lit, and cast warm yellow light through the falling raindrops.  The Outpost smells like a wet forest full of damp cats, which is a remarkable coincidence because that's exactly what it is.";
								break;
								case 7:
									$t = "It is a dark and stormy night.  Kittania is what happens when creatures more cat than human try to build a village in the middle of a dense forest but are too lazy to chop the trees down first, so at the best of times it's a pretty shady place - right now, it's almost pitch black.  The trees aren't so much protecting from the storm as concentrating it, gathering like-minded raindrops together into fist-sized falling puddles that hammer into the ground every time the wind rushes through.  The roar of the wind and the clash of the rain is almost deafening, and the frequent crack of thunder and flash of lightning doesn't help matters at all.  Nearly all KittyMorphs are now wearing clothing of some sort, and most have taken shelter somewhere inside.";
								break;
							}
						break;
					}
					$args['text'][0] = "`0".$t."`n`n";
					unset ($args['clock']);
				break;
				case "New Pittsburgh":
					switch($stateinfo['timezone']){
						case 1:
							switch($stateinfo['weather']){
								case 1:
									$t="A chilly fog hangs barely visible in the red dawn light.  Tiny fingers of frost stretch out from the wet sand, making a crunching noise under your feet.  Though it's early, you see a few shambling figures through the haze.";
								break;
								case 2:
									$t="The red dawn light does little to illuminate the dense mist that hangs over the outpost.  A few zombies are huddling around an old horse trough, moaning softly.  Early risers?";
								break;
								case 3:
									$t="There's a chill in the red dawn air, as you take stock of your surroundings.  The sandy terrain is moist, and the buildings are covered with a thin film of dew.";
								break;
								case 4:
									$t="Though the sun is a mere glimmer on the horizon, the skies are clear for miles.  You notice a group of zombies conversing in grunts and moans around an old horse trough, idly picking at the flappy bits of skin on their necks.";
								break;
								case 5:
									$t="The reddish dawn peaks through a cold and uncomfortable drizzly rain. You spy a lonely zombie smoking and poking at a gash in his chest.";
								break;
								case 6:
									$t="The faint light of dawn struggles to push through the artificial dark of a pouring rain.  It looks as if sensible residents - living and undead alike - have taken shelter.";
								break;
								case 7:
									$t="Dawn seems to have been rescheduled by the weather.  Though it's early day, thunder plays angry counterpoint to the dark of a stormy rain.  There is no sign of inhabitants - the undead are probably snug in their beds, sleeping like corpses.";
								break;
							}
						break;
						case 2:
							switch($stateinfo['weather']){
								case 1:
									$t="A hazy mist hangs in the cool air, softened by the glow of a full sunrise.  A few locals  have begun to stir, filling the air with grunts and moans.";
								break;
								case 2:
									$t="The warm sunrise glints softly off the morning dew.  The moist sand takes on the dragging footprints of the early risers.";
								break;
								case 3:
									$t="The red and orange of a spectacular sunrise lights up the outpost.  Beneath this cloudless sky, you feel like you can see forever.  On a morning like this, you almost don't mind the smell.";
								break;
								case 4:
									$t="The warm air seems to make the reds and golds of a perfect sunrise even more intense.  The cloudless sky affords a spectacular view of the beach and adjacent forest.";
								break;
								case 5:
									$t="The sun is barely visible amidst the gathering drizzle.  The cool wet air makes you shiver, as you look around for somewhere warmer to take shelter.";
								break;
								case 6:
									$t="There's a sunrise somewhere, but it's not here.  The heavy rain pulls a curtain of artificial night over the lifeless outpost.";
								break;
								case 7:
									$t="The sun has been completely pushed back by the buffeting gales.  A strong wind whips wet sand at your face, driving you to look for shelter.  The locals know better than to be out in this weather, and are probably staying dry inside.";
								break;
							}
						break;
						case 3:
							switch($stateinfo['weather']){
								case 1:
									$t="The morning air is thick and muggy, and makes you feel as if you're walking through a warm bowl of soup.  You notice a group of zombies conversing in grunts and moans around an old horse trough, idly picking at the flappy bits of skin on their necks.";
								break;
								case 2:
									$t="The sun is out in full force today; you can almost see the steam rise from your brow.  The dry beach makes an excellent playing ground - you see a few zombies congregating around a net, but don't recognize the sport.";
								break;
								case 3:
									$t="The warm morning sun highlights the sandy beach and glints off of the nearby jungle vegetation.  You notice a group of zombies conversing in grunts and moans around an old horse trough, idly picking at the flappy bits of skin on their necks.";
								break;
								case 4:
									$t="It's a lovely clear day and much of New Pittsburgh is outside.  Some zombies play a sort of net game on the sand, while another group of zombies has gathered around an old horse trough, idly picking at the flappy bits of skin on their necks.";
								break;
								case 5:
									$t="You catch sight of some small incoming clouds in the morning light, just as it begins to sprinkle.  The locals begin to shamble towards shelter.";
								break;
								case 6:
									$t="The darkening morning sky is full of dense, angry looking clouds.  You find yourself caught in a torrential downpour, as the sandy beach quickly turns to mush around you.";
								break;
								case 7:
									$t="The dark morning sky is split by lightning and the crash of thunder all around.  The outpost's residents appear to all be taking shelter.";
								break;
							}
						break;
						case 4:
							switch($stateinfo['weather']){
								case 1:
									$t="The high sun makes the unrelenting humidity seem even worse.  Despite the heat, you notice a group of zombies conversing in grunts and moans around an old horse trough, idly picking at the flappy bits of skin on their necks.";
								break;
								case 2:
									$t="The afternoon sun is hot, and makes sweat bead on your forehead.  You spy some residents heading towards Vigour Mortis in search of refreshment.";
								break;
								case 3:
									$t="The afternoon sun is warm and comforting.  You notice a group of zombies conversing in grunts and moans around an old horse trough, idly picking at the flappy bits of skin on their necks.";
								break;
								case 4:
									$t="The sun is warm and comforting in the clear afternoon sky.  Some zombies play a sort of net game on the sand, while another group of zombies has gathered around an old horse trough, idly picking at the flappy bits of skin on their necks.";
								break;
								case 5:
									$t="The mild afternoon sky has begun to darken, and light rain showers sprinkle the sand.  The local game of netball appears to be breaking up.";
								break;
								case 6:
									$t="The afternoon sky is dark with clouds.  A torrential downpour has begun to erode the beach.  The residents seem to have taken shelter in nearby establishments.";
								break;
								case 7:
									$t="The afternoon sky is dark with angry clouds.  Flashes of lightning and crashes of thunder punctuate a heavy rain.  Everyone appears to have taken shelter.";
								break;
							}
						break;
						case 5:
							switch($stateinfo['weather']){
								case 1:
									$t="Though the sun is beginning to go down, the evening is still hot.  You notice a group of zombies conversing in grunts and moans around an old horse trough, idly picking at the flappy bits of skin on their necks.";
								break;
								case 2:
									$t="The evening sky is still bright as the sun begins to go down.  Several residents are taking advantage of the lingering warmth, conversing in grunts and moans outside local establishments.";
								break;
								case 3:
									$t="The evening sky is bright and clear as the sun begins to set.  You hear small groans of activity as locals wind down their business and prepare to turn in for the night.";
								break;
								case 4:
									$t="The chilly evening sky is bright and lovely, as you watch a perfect sunset from the beach.  Locals murmur in small groups, as they shamble towards their homes and prepare to turn in for the night.";
								break;
								case 5:
									$t="You can barely see the sun set from behind its blanket of clouds.  You hear the murmur of activity begin to wind down, as locals prepare to turn in for the night.";
								break;
								case 6:
									$t="You know it's evening, but you can't see the sunset through the darkening rain.   The sand squishes underneath your feet, and around you, locals quickly finish up their business and shamble towards shelter.";
								break;
								case 7:
									$t="The evening sun is nowhere to be seen amidst the crackle and flash of the storm.  The driving wind whips wet sand at your face as residents quickly seek shelter from the pouring rain.";
								break;
							}
						break;
						case 6:
							switch($stateinfo['weather']){
								case 1:
									$t="The evening air is warm and damp.  You catch sight of a few flashlights and some lanterns, as residents continue to converse in grunts and moans through the dark of the evening.";
								break;
								case 2:
									$t="The darkening evening air is moist on your brow.  You see a group of zombies conversing in grunts and moans around an old horse trough.";
								break;
								case 3:
									$t="The sun has disappeared, and the stars are beginning to peek though the dome of the clear sky.  A zombie points heavenward, exclaiming \"BRAAAINS!\"  You peer upwards, and suppose the particular constellation does look a bit like grey matter.";
								break;
								case 4:
									$t="Though the sun has set, the cool, cloudless sky is lit by a million tiny pinpoints of starlight.  A few residents point awkwardly at the sky and murmur appreciatively.";
								break;
								case 5:
									$t="The sun has gone to bed, and the sticky night air feels like a warm bowl of soup.  There is still a group of zombies conversing in grunts and moans around an old horse trough.  You wonder what it is about horse troughs.";
								break;
								case 6:
									$t="The sky has opened up and pours down rain.  You can barely see your hand in front of your wet face.  The ferocity of the rain does seem to have cut down the smell of the place.  Perhaps it's also because the locals seem to have taken shelter elsewhere.";
								break;
								case 7:
									$t="The blackness of the sky is lit only by the intermittent flash of lightning.  A harsh wind and torrential rain pelts you with water and sand.  The sensible residents seem to have taken shelter.";
								break;
							}
						break;
						case 7:
							switch($stateinfo['weather']){
								case 1:
									$t="The cold night air is lit up with the many stars in their improbable alignments.  A group of locals point and moan at the numerous points of light.";
								break;
								case 2:
									$t="You shiver a bit as you gaze up at the light of what seems like a million stars.  Around you, you hear the groans and moans of resident stargazers.";
								break;
								case 3:
									$t="Though the sun is gone, pinpoints of starlight illuminate the mild air, softly lighting the sand around you.";
								break;
								case 4:
									$t="Though the sun is long gone, the air is still thick and warm from the heat of the day.  A group of zombies converse in grunts and moans around a horse trough.";
								break;
								case 5:
									$t="Though the sun is long gone, the air is thick and moist.  The stars are hiding behind a thick bank of clouds tonight, and you can barely make out your own hands.  Though you can't see them, you hear the murmur of zombie conversation - the locals must be night owls.";
								break;
								case 6:
									$t="The night is dark, and the sky has opened up and turned into a curtain of black rain.  You shiver and peer around the outpost, looking for a place to shelter.";
								break;
								case 7:
									$t="The night is indeed dark and stormy.  Loud claps of thunder are punctuated by the high pitched whistle of heavy winds.  You feel the sharp sting of wet sand against your face.  The locals appear to be sheltering elsewhere.";
								break;
							}
						break;
					}
					$args['clock'] = "`0".$t."`n`n";
				break;
				case "Squat Hole":
					switch($stateinfo['timezone']){
						case 1:
							//dawn
							switch ($stateinfo['weather']){
								case 1:
									$t = "A dense, chilly fog hangs barely visible in the red dawn light.  Tiny fingers of frost stretch out from the wet ground, making a sharply squelchy noise under your feet.  You nearly trip over a Midget asleep on the ground, hands clenched into fists.";
								break;
								case 2:
									$t = "The hazy dawn light does little to illuminate the dense mist that hangs over the outpost.  A few Midgets are sleepily staggering about in various degrees of pugnacious inebriation.";
								break;
								case 3:
									$t = "There's a chill in the red dawn air, as you take stock of your surroundings.  The cars and broken glass are covered with a thin film of dew, and you spot a couple of locals snoozing in a puddle of yellowish mud.";
								break;
								case 4:
									$t = "Though the sun is a mere glimmer on the horizon, the skies are clear for miles, allowing you to more easily step around the litter and avoid getting anything too nasty on your shoes.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and the smell is simply indescribable.";
									}
								break;
								case 5:
									$t = "The reddish dawn peaks through a cold and uncomfortable drizzly rain.  You see a Midget passed out on the ground, muttering and cursing in his sleep.";
								break;
								case 6:
									$t = "The faint light of dawn struggles to push through the dark of a pissing rain.  A few of the midgets passed out on the ground begin to slink towards drier ground.";
								break;
								case 7:
									$t = "It looks as if there will be no dawn today; thunder and lightening brawl with a torrential rain.  It looks like even the laziest Midgets are sheltering.";
								break;
							}
						break;
						case 2:
							//sunrise
							switch ($stateinfo['weather']){
								case 1:
									$t = "A hazy mist hangs in the cool air, softened by the glow of a full sunrise.  A Midget lying on the ground turns over and begins drowsily muttering.";
								break;
								case 2:
									$t = "The warm sunrise glints softly off the morning dew, and makes you yawn.  You nearly trip on a slick pile of broken glass.";
								break;
								case 3:
									$t = "The red and orange of a spectacular sunrise lights up the outpost.  The cloudless sky makes you wish your surroundings were a little more lovely.";
								break;
								case 4:
									$t = "The warm air seems to make the reds and golds of a perfect sunrise even more intense.  A sleeping midget burps, interrupting your reverie.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and the smell is quite unlike anything you've ever smelt before.";
									}
								break;
								case 5:
									$t = "The sun is barely visible amidst the gathering drizzle.  The cool wet air makes you shiver, as you look around for somewhere warmer to take shelter.";
								break;
								case 6:
									$t = "There's a sunrise somewhere, but it's not here.  The heavy rain makes it dark as night, though you think you spy a few locals huddling in rusted out cars.";
								break;
								case 7:
									$t = "The sun has been completely pushed back by the buffeting gales.  A strong wind whips mud and other detritus at your face.";
								break;
							}
						break;
						case 3:
							//morning
							switch ($stateinfo['weather']){
								case 1:
									$t = "The morning air is thick and muggy, and makes you feel as if you're walking through a warm bowl of soup.  Many of the locals are still in various states of unconsciousness around the public square.";
								break;
								case 2:
									$t = "The sun is out in full force today; you can almost see the steam rise from your brow. A few midgets are already cracking cans of cider by the side of the road.";
								break;
								case 3:
									$t = "The warm morning sun glints off broken glass and crushed cans.  A couple of midgets are arguing over the ownership of some scrap metal.";
								break;
								case 4:
									$t = "It's a beautiful clear day, and the locals are doing what they do best - drinking, and fighting.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and the smell is simply indescribable.";
									}
								break;
								case 5:
									$t = "Just as you catch sight of a cloud, it begins to rain.  This doesn't deter the knots of arguing midgets, who continue to loudly curse one another in the drizzle.";
								break;
								case 6:
									$t = "The darkening morning sky is full of dense, angry looking clouds.  You find yourself caught in a torrential downpour, and begin looking for shelter.";
								break;
								case 7:
									$t = "The dark morning sky is split by lightning and the crash of thunder all around.  A couple of locals have decided to take their argument inside.";
								break;
							}
						break;
						case 4:
							//afternoon
							switch ($stateinfo['weather']){
								case 1:
									$t = "The high sun makes the unrelenting humidity seem even worse. The stench of sweating Midget is even more pronounced than usual.";
								break;
								case 2:
									$t = "The afternoon sun is hot, and makes sweat bead on your forehead.  Some locals are enjoying cans of cider near their rusted-out wrecks.";
								break;
								case 3:
									$t = "The afternoon sun is warm and comforting.  Midgets everywhere are drinking, and a few are even collecting scrap.";
								break;
								case 4:
									$t = "The sun is warm and comforting.  Locals gather junk in the clear afternoon sun.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, but the high sun is quickly drying up the moisture.";
									}
								break;
								case 5:
									$t = "The mild afternoon sky has begun to darken, and light rain showers sprinkle the ground, making the already littered ground an even muddier mess.";
								break;
								case 6:
									$t = "The afternoon sky is dark with clouds.  A torrential downpour has made the ground even swampier; you try to avoid the worst of it as you pick your way through the Outpost.";
								break;
								case 7:
									$t = "The afternoon sky is dark with angry clouds.  Flashes of lightning and crashes of thunder punctuate a heavy rain, and the locals begin to shuffle towards shelter.";
								break;
							}
						break;
						case 5:
							//sunset
							switch ($stateinfo['weather']){
								case 1:
									$t = "Though the sun is beginning to go down, the evening is still hot.  A Midget crushes his empty cider can, and throws it at a fellow drunk.";
								break;
								case 2:
									$t = "The evening sky is still bright as the sun begins to go down.  A knot of Midgets appear to be making bets on a pair of \"racing\" ants.";
								break;
								case 3:
									$t = "The evening sky is bright and clear as the sun begins to set.  The off-key sounds of a bawdy ballad reach your ears.";
								break;
								case 4:
									$t = "The chilly evening sky is bright and lovely, as you watch an otherwise perfect sunset, tainted a bit by the smell and litter.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and the smell is unimaginable.";
									}
								break;
								case 5:
									$t = "You can barely see the sun set from behind its blanket of clouds.  The wafting odor of unwashed Midget tells you that plenty of locals are still out and about.";
								break;
								case 6:
									$t = "You know it's evening, but you can't see the sunset through the darkening rain.  You carefully pick your way through the town square in search of shelter.";
								break;
								case 7:
									$t = "The evening sun is nowhere to be seen amidst the crackle and flash of the storm.  Even the most inebriated locals have sought shelter.";
								break;
							}
						break;
						case 6:
							//dusk
							switch ($stateinfo['weather']){
								case 1:
									$t = "The evening air is warm and damp.  You hear the dulcet tones of breaking glass and swearing Midgets.";
								break;
								case 2:
									$t = "The darkening evening air is moist on your brow, as you breathe in the scent of decay and other bodily odors.";
								break;
								case 3:
									$t = "The sun has disappeared and the stars have begun to peak through the curtain of sky.  You wonder if there are any Midget stargazers.";
								break;
								case 4:
									$t = "Though the sun has set, the cool, cloudless sky is lit by a million tiny pinpoints of starlight.  A couple of Midgets are having a fistfight in the softly twinkly night.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and you wonder if there will be ground frost tonight.";
									}
								break;
								case 5:
									$t = "It's dark, and the sticky night air feels like a bowl of soup.  You sniff the air and amend that to a particularly `iunappetizing`i bowl of SkunkySkronky soup.";
								break;
								case 6:
									$t = "The sky has opened up and pours down rain.  You can barely see your hand in front of your wet face, much less your feet.  You hope you haven't stepped in anything too nasty, as you head towards shelter.";
								break;
								case 7:
									$t = "The blackness of the sky is lit only by the intermittent flash of lightning.  A harsh wind and torrential rain pelts you, chiming melodiously as it rings on the rusty husks of cars littered about.";
								break;
							}
						break;
						case 7:
							//night
							switch ($stateinfo['weather']){
								case 1:
									$t = "The cold night air is lit up with the many stars in their improbable alignments.  The diffuse light glints off of the many crumpled cider cans on the ground.";
								break;
								case 2:
									$t = "You shiver a bit as you gaze up at the light of what seems like a million stars.";
								break;
								case 3:
									$t = "Though the sun is gone, pinpoints of starlight illuminate the mild air.  Squinting, you swear you can make out a pattern of stars in the shape of a pair of upraised fingers.  How improbable.";
								break;
								case 4:
									$t = "Though the sun is long gone, the air is still thick and warm from the heat of the day.  A couple of Midgets are already slumped on the ground, sleeping off hangovers earned during the day.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and you wonder if there will be ground frost tonight.";
									}
								break;
								case 5:
									$t = "Though the sun is long gone, the air is thick and moist.  The stars are hiding behind a thick bank of clouds tonight, and you can barely make out your own hands.  You hear snoring, punctuated with occasional flatulence.";
								break;
								case 6:
									$t = "The night is dark, and the sky has opened up and turned into a curtain of black rain.  You shiver and begin to look for a warm place to shelter.";
								break;
								case 7:
									$t = "The night is indeed dark and stormy.  Loud claps of thunder are punctuated by the high pitched whistle of heavy winds.";
								break;
							}
						break;
					}
					$args['clock'] = "`0".$t."`n`n";
				break;
				case "Pleasantville":
					switch($stateinfo['timezone']){
						case 1:
							//dawn
							switch ($stateinfo['weather']){
								case 1:
									$t = "A chilly fog hangs barely visible in the red dawn light.  Tiny fingers of frost stretch out from the wet ground, making a crunching noise under your feet.";
								break;
								case 2:
									$t = "The red dawn light does little to illuminate the dense mist that hangs over the outpost.  A few early risers are huddled in a small circle, reciting early morning verse.";
								break;
								case 3:
									$t = "There's a chill in the red dawn air, as you take stock of your surroundings.  The buildings and vegetation are covered with a thin film of dew.";
								break;
								case 4:
									$t = "Though the sun is a mere glimmer on the horizon, the skies are clear for miles.  You spot an early riser shaking his fist and gloomily decrying the good weather.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and the smell is sweet and earthy.";
									}
								break;
								case 5:
									$t = "The reddish dawn peaks through a cold and uncomfortable drizzly rain.  The miserable weather has drawn a crowd of local poets.";
								break;
								case 6:
									$t = "The faint light of dawn struggles to push through the artificial dark of a pouring rain.  Pleasantville's residents are beginning to appear, drawn by the hazy skies.";
								break;
								case 7:
									$t = "Dawn seems to have been rescheduled by the weather. Though it's early day, thunder plays angry counterpoint to the dark of a stormy rain.  A few miserable early risers are plodding along, composing blank verse under their breath.";
								break;
							}
						break;
						case 2:
							//sunrise
							switch ($stateinfo['weather']){
								case 1:
									$t = "A hazy mist hangs in the cool air, softened by the glow of a full sunrise.  A few locals begin to stir, muttering angrily about the brightness of the day.";
								break;
								case 2:
									$t = "The warm sunrise glints softly off the morning dew, and makes you yawn.";
								break;
								case 3:
									$t = "The red and orange of a spectacular sunrise lights up the outpost.  The cloudless sky makes you feel you can see forever.  A local is comparing the sun's beauty to a cruel mistress.";
								break;
								case 4:
									$t = "The warm air seems to make the reds and golds of a perfect sunrise even more intense.  You seem to be the only one appreciating the view; several local poets are currently bemoaning the horrors of existence.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and the smell is sweet and earthy.";
									}
								break;
								case 5:
									$t = "The sun is barely visible amidst the gathering drizzle.  The cool wet air makes you shiver, as you look around for somewhere warmer to take shelter.";
								break;
								case 6:
									$t = "There's a sunrise somewhere, but it's not here.  The heavy rain pulls a curtain of artificial night over the sleepy outpost.  You spy a huddling smoker taking shelter under a nearby ledge.";
								break;
								case 7:
									$t = "The sun has been completely pushed back by the buffeting gales.  A strong wind whips dirt at your face, and you narrowly miss stepping in one of a number of puddles that look deep enough to reach your knees.";
								break;
							}
						break;
						case 3:
							//morning
							switch ($stateinfo['weather']){
								case 1:
									$t = "The morning air is thick and muggy, and makes you feel as if you're walking through a warm bowl of soup.   A few locals seem to have gathered for an open-air poetry slam, vying for the right to be the undisputed scribe of woe.";
								break;
								case 2:
									$t = "The sun is out in full force today; you can almost see the steam rise from your brow.  Some locals are taking advantage of the weather by holding a writing circle.";
								break;
								case 3:
									$t = "The warm morning sun glints dully off the non-reflective surfaces.  Some residents are engaged in debate as to which season is cruellest.";
								break;
								case 4:
									$t = "It's a beautiful clear day, but you wouldn't know it from the mood of the residents.  The outpost is crowded with residents glumly engaged in their daily tasks.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and the smell is sweet and earthy.";
									}
								break;
								case 5:
									$t = "You catch sight of some small incoming clouds in the morning light, and before you can think to worry about an umbrella, it begins to sprinkle.  \"It figures.\" you hear a local remark to himself.";
								break;
								case 6:
									$t = "The darkening morning sky is full of dense, angry looking clouds.  You find yourself caught in a torrential downpour, and start to wonder if there's anywhere around to buy an umbrella.";
								break;
								case 7:
									$t = "The dark morning sky is split by lightning and the crash of thunder all around.  A particularly dramatic poet is timing his recitation to the deafening weather.";
								break;
							}
						break;
						case 4:
							//afternoon
							switch ($stateinfo['weather']){
								case 1:
									$t = "The high sun makes the unrelenting humidity seem even worse.  Some residents have taken shelter under the shade of a nearby tree.";
								break;
								case 2:
									$t = "The afternoon sun is hot, and makes sweat bead on your forehead.  Some residents are debating existence under a nearby tree.";
								break;
								case 3:
									$t = "The afternoon sun is warm and comforting - to you, at least. Some residents are engaged in conversation about the futility of hope.";
								break;
								case 4:
									$t = "The sun is warm and comforting in the clear afternoon sky. Some residents are engaged in lively debate about existence and meaning and the bloody point of it all.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, but the high sun is quickly drying up the moisture.";
									}
								break;
								case 5:
									$t = "The mild afternoon sky has begun to darken, and light rain showers sprinkle the ground.  A few smokers have taken shelter under a nearby awning.";
								break;
								case 6:
									$t = "The afternoon sky is dark with clouds.  A torrential downpour has made many muddy puddles, which you try to avoid as you pick your way through the Outpost.";
								break;
								case 7:
									$t = "The afternoon sky is dark with angry clouds.  Flashes of lightning and crashes of thunder punctuate a heavy rain.  Everyone with sense and means has taken shelter.";
								break;
							}
						break;
						case 5:
							//sunset
							switch ($stateinfo['weather']){
								case 1:
									$t = "Though the sun is beginning to go down, the evening is still hot.  Small groups of locals mill about, as they prepare to end the day.";
								break;
								case 2:
									$t = "The evening sky is still bright as the sun begins to go down.  Several residents are taking advantage of the lingering warmth, and hold debate groups in small, darkly-clothed groupings.";
								break;
								case 3:
									$t = "The evening sky is bright and clear as the sun begins to set.  You hear small grumbles of activity as locals wind down their business and prepare to turn in for the night.";
								break;
								case 4:
									$t = "The chilly evening sky is bright and lovely, as you watch a perfect and otherwise unappreciated sunset.  Locals chat in small knots, questioning the meaning of it all, as they make their way back to their homes and prepare to turn in for the night.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain.";
									}
								break;
								case 5:
									$t = "You can barely see the sun set from behind its blanket of clouds.  You hear the murmur of activity begin to wind down, as locals prepare to turn in for the night.";
								break;
								case 6:
									$t = "You know it's evening, but you can't see the sunset through the darkening rain.  The residents are hurriedly finishing up their tasks, in an effort to quickly return home.";
								break;
								case 7:
									$t = "The evening sun is nowhere to be seen amidst the crackle and flash of the storm.  Residents resignedly seek shelter from the driving wind and pouring rain.";
								break;
							}
						break;
						case 6:
							//dusk
							switch ($stateinfo['weather']){
								case 1:
									$t = "The evening air is warm and damp.  You catch sight of a few flashlights and some lanterns, as residents continue to converse in the dark of the evening.";
								break;
								case 2:
									$t = "The darkening evening air is moist on your brow. Some residents are engaged in poetic debate.";
								break;
								case 3:
									$t = "The sun has disappeared, and the stars are beginning to peak though the dome of the clear sky.";
								break;
								case 4:
									$t = "Though the sun has set, the cool, cloudless sky is lit by a million tiny pinpoints of starlight.  A few residents recline on the ground, stargazing and reciting poetry about the cruel cold of space.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and you wonder if there will be ground frost tonight.";
									}
								break;
								case 5:
									$t = "The sun has gone to bed, and the sticky night air feels like a warm bowl of soup.  A few residents are engaged in poetry recital on the outskirts of the outpost.";
								break;
								case 6:
									$t = "The sky has opened up and pours down rain.  You can barely see your hand in front of your wet face.  The locals seem to have taken shelter elsewhere.";
								break;
								case 7:
									$t = "The blackness of the sky is lit only by the intermittant flash of lightning.  A harsh wind and torrential rain pelts you.  The sensible residents seem to have taken shelter.";
								break;
							}
						break;
						case 7:
							//night
							switch ($stateinfo['weather']){
								case 1:
									$t = "The cold night air is lit up with the many stars in their Improbable alignments.";
								break;
								case 2:
									$t = "You shiver a bit as you gaze up at the light of what seems like a million stars.";
								break;
								case 3:
									$t = "Though the sun is gone, pinpoints of starlight illuminate the mild air.  Looking up, you can see the constellation 'Tentacled Friend'";
								break;
								case 4:
									$t = "Though the sun is long gone, the air is still thick and warm from the heat of the day.  Some residents appear to be stargazing, reciting a new tale of woe for each improbable constellation.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and you wonder if there will be ground frost tonight.";
									}
								break;
								case 5:
									$t = "Though the sun is long gone, the air is thick and moist.  The stars are hiding behind a thick bank of clouds tonight, and you can barely make out your own hands.";
								break;
								case 6:
									$t = "The night is dark, and the sky has opened up and turned into a curtain of black rain.";
								break;
								case 7:
									$t = "The night is indeed dark and stormy.  Loud claps of thunder are punctuated by the high pitched whistle of heavy winds.  The locals appear to be sheltering elsewhere.";
								break;
							}
						break;
					}
					$args['clock'] = "`0".$t."`n`n";
				break;
				case "AceHigh":
					switch($stateinfo['timezone']){
						case 1:
							//dawn
							switch ($stateinfo['weather']){
								case 1:
									$t = "A chilly fog hangs barely visible in the red dawn light.  Tiny fingers of frost stretch out from the wet ground, making a crunching noise under your feet.  Though it's early, you see a few well-dressed persons quietly conversing in the square.";
								break;
								case 2:
									$t = "The red dawn light does little to illuminate the dense mist that hangs over the outpost.  You see a couple of well-dressed gentlemen conversing in the square, but as you begin to approach them they both disappear in a flash of light.";
								break;
								case 3:
									$t = "There's a chill in the red dawn air, as you take stock of your surroundings.  The buildings and jungle vegetation are covered with a thin film of dew, and the infamous locals appear to be sheltering elsewhere...";
								break;
								case 4:
									$t = "Though the sun is a mere glimmer on the horizon, the skies are clear for miles.  A few early risers are taking advantage of the warmth by engaging in some kind of strange sport.  As you approach to take a closer look, one of them vanishes.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and the smell is sweet and earthy.";
									}
								break;
								case 5:
									$t = "The reddish dawn peaks through a cold and uncomfortable drizzly rain.  You spy a single lady sheltering under a parasol.";
								break;
								case 6:
									$t = "Dawn struggles to push through the downpour of a thick jungle rain.  You see no sign of any locals, who are presumably sheltering elsewhere.";
								break;
								case 7:
									$t = "Though it should be dawn, the thunder and wind of a jungle storm push back the light.  The locals appear to be sheltering someplace more sensible.";
								break;
							}
						break;
						case 2:
							//sunrise
							switch ($stateinfo['weather']){
								case 1:
									$t = "A hazy mist hangs in the cool air, softened by the glow of a full sunrise.  Here and there you see lights appear as a few locals begin to stir, preparing for the day.";
								break;
								case 2:
									$t = "The warm sunrise glints softly off the morning dew, and makes you yawn.   Well-dressed gentlemen and ladies stroll about with impeccable manners. Every now and then, one of them explodes in an astonishing flash of green light, drawing polite applause from nearby persons.";
								break;
								case 3:
									$t = "The red and orange of a spectacular sunrise lights up the outpost.  The cloudless sky makes you feel you can see forever.  Well-dressed gentlemen and ladies stroll about with impeccable manners. Every now and then, one of them explodes in an astonishing flash of green light, drawing polite applause from nearby persons.";
								break;
								case 4:
									$t = "The warm jungle air seems to make the reds and golds of a perfect sunrise even more intense.  Well-dressed gentlemen and ladies stroll about with impeccable manners. Every now and then, one of them explodes in an astonishing flash of green light, drawing polite applause from nearby persons.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and the smell is sweet and earthy.";
									}
								break;
								case 5:
									$t = "The sun is barely visible amidst the gathering drizzle.  The cool wet air makes you shiver, as you look around for somewhere warmer to take shelter.  A few well-dressed gentlemen and lades stroll leisurely, opening perfectly matched umbrellas and parasols.";
								break;
								case 6:
									$t = "There's a sunrise somewhere, but it's not here.  The heavy rain pulls a curtain of artificial night over the sleepy outpost.   The few locals you spy are purposefully striding under the shelter of their umbrellas towards the dry indoors.";
								break;
								case 7:
									$t = "What sun there once was has been completely pushed back by the buffeting gales.  A strong wind whips leaves and sticks at your face.  The locals know better than to be out in this weather, and are likely sheltering safely elsewhere.";
								break;
							}
						break;
						case 3:
							//morning
							switch ($stateinfo['weather']){
								case 1:
									$t = "The morning air is thick and muggy, and makes you feel as if you're walking through a warm bowl of soup.  A few well-dressed gentlemen and ladies stroll about with impeccable manners. Every now and then, one of them explodes in an astonishing flash of green light, drawing polite applause from nearby persons.";
								break;
								case 2:
									$t = "The sun is out in full force today; you can almost see the steam rise from your brow.  You spy a group of well-dressed locals taking advantage of the weather to play an intriguing variety of croquet.";
								break;
								case 3:
									$t = "The sun is out in full force today; you can almost see the steam rise from your brow.  You spy a group of well-dressed locals taking advantage of the weather to play an intriguing variety of croquet.";
								break;
								case 4:
									$t = "It's a beautiful clear day, and much of AceHigh appears to be outside.  The outpost is crowded with residents milling about, engaged in daily tasks and cheerful conversation.  Well-dressed gentlemen and ladies stroll about with impeccable manners. Every now and then, one of them explodes in an astonishing flash of green light, drawing polite applause from nearby persons.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and the smell is sweet and earthy.";
									}
								break;
								case 5:
									$t = "You catch sight of some small incoming clouds in the morning light, and before you can think to worry about an umbrella, it begins to sprinkle.   The more prepared locals are already raising their parasols and umbrellas, perfectly matched to their impeccable attire.";
								break;
								case 6:
									$t = "The darkening morning sky is full of dense, angry looking clouds.  You find yourself caught in a torrential downpour.";
								break;
								case 7:
									$t = "The dark morning sky is split by lightning and the crash of thunder all around.  You spot a few brightly coloured flashes, as those locals who were outside decide to be elsewhere.";
								break;
							}
						break;
						case 4:
							//afternoon
							switch ($stateinfo['weather']){
								case 1:
									$t = "The high sun makes the unrelenting humidity seem even worse.  Some residents have taken shelter under the shade of a nearby tree and appear to be playing some kind of dice game.";
								break;
								case 2:
									$t = "The afternoon sun is hot, and makes sweat bead on your forehead.  A group of well-dressed ladies are enjoying a picnic lunch under a nearby tree.";
								break;
								case 3:
									$t = "The afternoon sun is warm and comforting.  Groups of well-dressed gentlemen and ladies mill about the center of the outpost, occasionally appearing and disappearing with great pyrotechnic fanfare.";
								break;
								case 4:
									$t = "The sun is warm and comforting in the clear afternoon sky.   A group of well-dressed gentlemen enjoy refreshments in the shade of a nearby tree.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, but the high sun is quickly drying up the moisture.";
									}
								break;
								case 5:
									$t = "The mild afternoon sky has begun to darken, and light rain showers sprinkle the ground.  The more prepared locals are already raising their parasols and umbrellas, perfectly matched to their impeccable attire.";
								break;
								case 6:
									$t = "The afternoon sky is dark with clouds.  A torrential downpour has made many muddy puddles, which you try to avoid as you pick your way across the grand square";
								break;
								case 7:
									$t = "The afternoon sky is dark with angry clouds.  Flashes of lightning and crashes of thunder punctuate a heavy rain.  Despite the inclement weather, you see some strangely coloured lightning with person-shaped afterimages.";
								break;
							}
						break;
						case 5:
							//sunset
							switch ($stateinfo['weather']){
								case 1:
									$t = "Though the sun is beginning to go down, the evening is still hot. Well-dressed gentlemen and ladies stroll about with impeccable manners. Every now and then, one of them explodes in an astonishing flash of green light, drawing polite applause from nearby persons.";
								break;
								case 2:
									$t = "The evening sky is still bright as the sun begins to go down.  Several residents are taking advantage of the lingering warmth, strolling arm in arm in their best attire.";
								break;
								case 3:
									$t = "The evening sky is bright and clear as the sun begins to set.  Well-dressed gentlemen and ladies stroll about with impeccable manners. Every now and then, one of them explodes in an astonishing flash of green light, drawing polite applause from nearby persons.";
								break;
								case 4:
									$t = "The chilly evening sky is bright and lovely, as you watch a perfect sunset.  A few of the well-dressed locals are chatting, while others amuse themselves with astonishing displays of light and sound.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain.";
									}
								break;
								case 5:
									$t = "You can barely see the sun set from behind its blanket of clouds.  You hear the murmur of activity begin to wind down, as locals prepare to turn in for the night.";
								break;
								case 6:
									$t = "You know it's evening, but you can't see the sunset through the darkening rain. A few lingering flashes of light are the only evidence of local activity.";
								break;
								case 7:
									$t = "The evening sun is nowhere to be seen amidst the crackle and flash of the storm. The locals have presumably retired to more sheltered spaces.";
								break;
							}
						break;
						case 6:
							//dusk
							switch ($stateinfo['weather']){
								case 1:
									$t = "The evening air is warm and damp.  You can still hear a murmur of activity, and catch sight of a few late-night gatherings, as the locals take advantage of the lingering day.";
								break;
								case 2:
									$t = "The darkening evening air is moist on your brow.  The murmur of conversation reaches your ears as locals continue to converse in the growing dusk.";
								break;
								case 3:
									$t = "The sun has disappeared, and the stars are beginning to peek though the dome of the clear sky.  A few well-dressed locals point intriguing instruments at the stars.";
								break;
								case 4:
									$t = "Though the sun has set, the cool, cloudless sky is lit by a million tiny pinpoints of starlight. A symposium of well-dressed stargazers has convened in the center of town.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and you wonder if there will be ground frost tonight.";
									}
								break;
								case 5:
									$t = "The sun has gone to bed, and the sticky night air feels like a warm bowl of soup.  You can still spy a few well-dressed locals chatting in the dusk, and you wonder at their apparent imperviousness to the muggy weather.";
								break;
								case 6:
									$t = "The sky has opened up and pours down rain.  You can barely see your hand in front of your wet face.  The locals seem to have taken shelter elsewhere.";
								break;
								case 7:
									$t = "The blackness of the sky is lit only by the intermittent flash of multicoloured lightning.  A harsh wind and torrential rain pelts you.  The sensible residents seem to have taken shelter.";
								break;
							}
						break;
						case 7:
							//night
							switch ($stateinfo['weather']){
								case 1:
									$t = "The cold night air is lit up with the many stars in their improbable alignments.  A few of the local astronomers map constellations and converse in low tones.";
								break;
								case 2:
									$t = "You shiver a bit as you gaze up at the light of what seems like a million stars.  You aren't alone in your stargazing - a couple of well-dressed locals are pointing strange instruments at the sky.";
								break;
								case 3:
									$t = "Though the sun is gone, pinpoints of starlight illuminate the mild air.  For a moment as you look up, the stars appear to take the shape of a top hat.";
								break;
								case 4:
									$t = "Though the sun is long gone, the air is still thick and warm from the heat of the day.  A few of the residents appear to be having a stargazing party, pointing out various familiar and unfamiliar forms, and taking measurements with strange instruments.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and you wonder if there will be ground frost tonight.";
									}
								break;
								case 5:
									$t = "Though the sun is long gone, the air is thick and moist.  The stars are hiding behind a thick bank of clouds tonight, and you can barely make out your own hands.  You occasionally catch a few scattered flashes of light, from unknown sources.";
								break;
								case 6:
									$t = "The night is dark, and the sky has opened up and turned into a curtain of black rain.";
								break;
								case 7:
									$t = "The night is indeed dark and stormy.  Loud claps of thunder are punctuated by the high pitched whistle of heavy winds.  It looks like everyone else is safely inside tonight.";
								break;
							}
						break;
					}
					$args['clock'] = "`0".$t."`n`n";
				break;
				case "Cyber City 404":
					switch($stateinfo['timezone']){
						case 1:
							//dawn
							switch ($stateinfo['weather']){
								case 1:
									$t = "A chilly fog hangs barely visible in the red dawn light.  Tiny fingers of frost stretch out from the wet ground, making a crunching noise under your feet.  A few residents engage in mechanical conversation in the early dawn light.";
								break;
								case 2:
									$t = "The red dawn light does little to illuminate the dense mountainous mist that hangs over the outpost.  A few residents engage in stilted conversation in the early light.";
								break;
								case 3:
									$t = "There's a chill in the red dawn air, as you take stock of your surroundings.  The buildings and vegetation are covered with a thin film of very cold dew that makes you shiver in the high elevation.";
								break;
								case 4:
									$t = "Though the sun is a mere glimmer on the horizon, the clear mountain air makes you think you can see for miles.  A few residents engage in stilted conversation in the early light.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and the smell is sweet and earthy.";
									}
								break;
								case 5:
									$t = "The reddish dawn peaks through a cold and uncomfortable drizzly rain. The residents have all retreated indoors, with no incentive to be outside.";
								break;
								case 6:
									$t = "The faint dawn light struggles to push through the dark of a pouring rain.  All of the residents must be staying dry elsewhere.";
								break;
								case 7:
									$t = "Though early dawn, there's no sign of the sun.  A crashing thunder and pouring rain punctuate the wet, rather miserable landscape.  You see no sign of the residents.";
								break;
							}
						break;
						case 2:
							//sunrise
							switch ($stateinfo['weather']){
								case 1:
									$t = "A hazy mist hangs in the cool air, softened by the glow of a full sunrise.  A few of the residents are taking advantage of the weather for charging, and engage in stilted mechanical conversation around the well in the village square.";
								break;
								case 2:
									$t = "The warm sunrise glints softly off the chilly morning dew, and makes you yawn. The residents are taking advantage of the sun, and engage in stilted mechanical conversation around the well in the village square.";
								break;
								case 3:
									$t = "The red and orange of a spectacular sunrise lights up the mountainous outpost.  The cloudless sky makes you feel you can see forever.  A group of residents gather to charge around the well, making mechanical conversation in the morning light.";
								break;
								case 4:
									$t = "The warm air seems to make the reds and golds of a perfect sunrise even more intense.  The cloudless sky affords a spectacular view of the mountains.  From the right angle, you can catch sight of the ocean beyond.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and the smell is sweet and earthy.";
									}
								break;
								case 5:
									$t = "The sun is barely visible amidst the gathering drizzle.  The cool wet mountain air makes you shiver, as you look around for somewhere warmer to take shelter.  The residents appear to be staying dry indoors.";
								break;
								case 6:
									$t = "There's a sunrise somewhere, but it's not here.  The heavy rain gives the outpost a sleepy deserted look, with not a resident in sight.";
								break;
								case 7:
									$t = "The sun has been completely pushed back by the buffeting gales.  The strong wind makes navigation difficult, as you try to avoid stumbling.  There's not a resident in sight.";
								break;
							}
						break;
						case 3:
							//morning
							switch ($stateinfo['weather']){
								case 1:
									$t = "The morning air is thick and muggy, and makes you feel as if you're walking through a warm bowl of soup. Some residents are engaged in stilted, mechanical conversation around the well in the village square. ";
								break;
								case 2:
									$t = "The sun is out in full force today; you can almost see the steam rise from your brow.  It looks like most of the town is out taking advantage of the good weather to charge.  A few residents make stilted mechanical conversation as they gather around the well.";
								break;
								case 3:
									$t = "The warm morning sun highlights the fantastic view afforded by the outpost's mountainous location.  Some residents are engaged in stilted, mechanical conversation around the well in the village square.";
								break;
								case 4:
									$t = "It's a beautiful clear day, and much of Cyber City 404 is outside, drinking in the solar resources.  Some residents are engaged in stilted, mechanical conversation around the well in the village square.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and the smell is sweet and earthy.";
									}
								break;
								case 5:
									$t = "The morning light makes a prism of the small raindrops just beginning to fall.  You watch as the residents start to take shelter. ";
								break;
								case 6:
									$t = "The dark morning sky is full of dark, angry clouds, as you're caught in a torrential downpour.  The residents are taking shelter elsewhere.";
								break;
								case 7:
									$t = "The dark morning sky is split by lightning and the crash of thunder all around.  The outpost's residents appear to all be taking shelter.";
								break;
							}
						break;
						case 4:
							//afternoon
							switch ($stateinfo['weather']){
								case 1:
									$t = "The high sun makes the unrelenting humidity seem even worse.  Most of the residents are charging, and making stitlted mechanical conversation.";
								break;
								case 2:
									$t = "The afternoon sun is hot, and makes sweat bead on your forehead. The outpost offers little in the way of shelter.";
								break;
								case 3:
									$t = "The afternoon sun is warm and comforting.  Many residents are taking advantage of the solar energy source, and charge around the well, while making stilted mechanical conversation.";
								break;
								case 4:
									$t = "The sun is warm and comforting in the clear afternoon sky.  Residents make stilted mechanical conversation while charging around the well.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, but the high sun is quickly drying up the moisture.";
									}
								break;
								case 5:
									$t = "The mild afternoon sky has begun to darken, and light rain showers sprinkle the ground.  Most of the residents have sought shelter indoors";
								break;
								case 6:
									$t = "The afternoon sky is dark with clouds, and the air is wet with a torrential downpour.  Most of the residents are seeking shelter indoors.";
								break;
								case 7:
									$t = "The afternoon sky is dark with angry clouds.  Flashes of lightning and crashes of thunder punctuate a heavy rain. There is no sign of activity outside, as everyone has taken shelter.";
								break;
							}
						break;
						case 5:
							//sunset
							switch ($stateinfo['weather']){
								case 1:
									$t = "Though the sun is beginning to go down, the evening is still hot.  Small groups of residents mill about, conversing with stilted, mechanical phrasing.";
								break;
								case 2:
									$t = "The evening sky is still bright, as the sun begins to go down, affording a fantastic view of the mountains.   A few residents chat in mechanical tones.";
								break;
								case 3:
									$t = "The evening sky is bright and clear as the sun begins to set.  Some residents are engaged in stilted, mechanical conversation around the well in the village square.";
								break;
								case 4:
									$t = "The chilly evening sky is bright and lovely, as you watch a perfect sunset. Some residents are engaged in stilted, mechanical conversation around the well in the village square.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain.";
									}
								break;
								case 5:
									$t = "You can barely see the sun set from behind its blanket of clouds. Most of the residents are sheltering indoors.";
								break;
								case 6:
									$t = "You know it's evening, but you can't see the sunset through the darkening rain.  There's not a single resident in sight; everyone has taken shelter elsewhere.";
								break;
								case 7:
									$t = "The evening sun is nowhere to be seen amidst the crackle and flash of the storm.  You look around for shelter from the driving wind and pouring rain.";
								break;
							}
						break;
						case 6:
							//dusk
							switch ($stateinfo['weather']){
								case 1:
									$t = "The evening air is warm and damp.  A few residents continue to converse, while others seem to stare at the sky.";
								break;
								case 2:
									$t = "The darkening evening air is moist on your brow.  You don't see any sign of the residents outdoors.";
								break;
								case 3:
									$t = "The sun has disappeared, and the stars are beginning to peek though the dome of the clear sky.  A few residents stare fixedly at the heavens.";
								break;
								case 4:
									$t = "Though the sun has set, the cool, cloudless sky is lit by a million tiny pinpoints of starlight.  Some of the residents are staring at the sky; you wonder if they are cataloguing, or just spectating for its own sake.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and you wonder if there will be ground frost tonight.";
									}
								break;
								case 5:
									$t = "The sun has gone to bed, and the sticky night air feels like a warm bowl of soup.  A few residents continue to practice their stilted conversation.";
								break;
								case 6:
									$t = "The sky has opened up and pours down rain.  You can barely see your hand in front of your wet face. All of the residents have taken shelter elsewhere.";
								break;
								case 7:
									$t = "The blackness of the sky is lit only by the intermittent flash of lightning, briefly highlighting the mountainous surrounding landscape.  A harsh wind and torrential rain pelts you.";
								break;
							}
						break;
						case 7:
							//night
							switch ($stateinfo['weather']){
								case 1:
									$t = "The cold night air is lit up with the many stars in their improbable alignments.  Picking out a few familiar ones, you wonder if the residents have any constellations particular to their outpost.";
								break;
								case 2:
									$t = "You shiver a bit as you gaze up at the light of what seems like a million stars.  You aren't the only one stargazing - you catch sight of a few residents fixedly staring at the heavens.";
								break;
								case 3:
									$t = "Though the sun is gone, pinpoints of starlight illuminate the mild air.  Looking up, you wonder if a particular arrangement doesn't look something like a gear, or cog.";
								break;
								case 4:
									$t = "Though the sun is long gone, the air is still thick and warm from the heat of the day.  A few locals continue their stilted mechanical conversation, but most residents silently observe the skies.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and you wonder if there will be ground frost tonight.";
									}
								break;
								case 5:
									$t = "Though the sun is long gone, the air is thick and moist.  The stars are hiding behind a thick bank of clouds tonight, and you can barely make out your own hands.";
								break;
								case 6:
									$t = "The night is dark, and the sky has opened up and turned into a curtain of black rain.  You see no sign of the residents, who are presumably sheltering elsewhere.";
								break;
								case 7:
									$t = "The night is indeed dark and stormy.  Loud claps of thunder are punctuated by the high pitched whistle of heavy winds.  The locals appear to be sheltering elsewhere.";
								break;
							}
						break;
					}
					$args['clock'] = "`0".$t."`n`n";
				break;
				case "Improbable Central":
					switch($stateinfo['timezone']){
						case 1:
							//dawn
							switch ($stateinfo['weather']){
								case 1:
									$t = "A chilly fog hangs barely visible in the red dawn light.  Tiny fingers of frost stretch out from the wet ground, making a crunching noise under your feet.  You aren't the only one out this early - you can just make out the shadowy outlines of figures, but their race and gender is unclear.";
								break;
								case 2:
									$t = "The red dawn light does little to illuminate the dense mist that hangs over the outpost.  Despite the time, a few early risers have congregated around a strange looking rock.";
								break;
								case 3:
									$t = "There's a chill in the red dawn air, as you take stock of your surroundings.  The buildings and vegetation are covered with a thin film of dew.  Though it's early, you see a few folks walking towards a strange rock.";
								break;
								case 4:
									$t = "Though the sun is a mere glimmer on the horizon, the skies are clear for miles.  A few early rises are taking advantage of the weather, and play a strange game in front of the Prancing SpiderKitty.  It looks a bit like lawn chess, but you don't recognize any of the pieces.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and the smell is sweet and earthy.";
									}
								break;
								case 5:
									$t = "The reddish dawn peaks through a cold and uncomfortable drizzly rain. You spot a lone zombie shuffling towards drier ground.";
								break;
								case 6:
									$t = "The faint light of dawn struggles to push through the artificial dark of a pouring rain.  Even the more hardy of the locals are moving towards shelter.";
								break;
								case 7:
									$t = "Dawn seems to have been rescheduled by the weather. Though it's early day, thunder plays angry counterpoint to the dark of a stormy rain.  There is no sign of inhabitants - most likely the residents are all snug in their beds.";
								break;
							}
						break;
						case 2:
							//sunrise
							switch ($stateinfo['weather']){
								case 1:
									$t = "A hazy mist hangs in the cool air, softened by the glow of a full sunrise.  A few locals begin to stir, preparing for the day.  You see a human conversing with a mutant near a strange looking rock.";
								break;
								case 2:
									$t = "The warm sunrise glints softly off the morning dew, and makes you yawn.  Out of the corner of your eye, you see a kittymorph stretching.";
								break;
								case 3:
									$t = "The red and orange of a spectacular sunrise lights up the outpost.  The cloudless sky makes you feel you can see forever. Pairs of people stroll hand in hand, admiring the view.";
								break;
								case 4:
									$t = "The warm air seems to make the reds and golds of a perfect sunrise even more intense. The cloudless sky offers a spectacular view of the nearby plains.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and the smell is sweet and earthy.";
									}
								break;
								case 5:
									$t = "The sun is barely visible amidst the gathering drizzle.  The cool wet air makes you shiver, as you look around for somewhere warmer to take shelter.";
								break;
								case 6:
									$t = "There's a sunrise somewhere, but it's not here.  The heavy rain pulls a curtain of artificial night over the sleepy outpost.  You spy a smoker taking refuge near The Prancing SpiderKitty.";
								break;
								case 7:
									$t = "The sun has been completely pushed back by the buffeting gales.  A strong wind whips pebbles and dirt at your face.";
								break;
							}
						break;
						case 3:
							//morning
							switch ($stateinfo['weather']){
								case 1:
									$t = "The morning air is thick and muggy, and makes you feel as if you're walking through a warm bowl of soup.  A few ambitious locals are playing a strange game in the square.";
								break;
								case 2:
									$t = "The sun is out in full force today; you can almost see the steam rise from your brow.  Locals of all shapes and sizes mill about, taking excercize.";
								break;
								case 3:
									$t = "The warm morning sun highlights the local businesses and glints dully as it strikes a strange looking rock.";
								break;
								case 4:
									$t = "It's a beautiful clear day, and all of Improbable Central appears to be outside.  The outpost is crowded with residents milling about, engaged in games, daily tasks and cheerful conversation.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and the smell is sweet and earthy.";
									}
								break;
								case 5:
									$t = "Looking at the sky, you see a few small incoming clouds that quickly begin to sprinkle.  The full town square begins to empty, as locals seek shelter.";
								break;
								case 6:
									$t = "The darkening morning sky fills quickly with rain, as a torrential downpour arrives.  You find yourself wishing for an umbrella.";
								break;
								case 7:
									$t = "The dark morning sky is split by lightning and the crash of thunder all around.  The outpost's residents appear to all be taking shelter.";
								break;
							}
						break;
						case 4:
							//afternoon
							switch ($stateinfo['weather']){
								case 1:
									$t = "The high sun makes the unrelenting humidity seem even worse.  Some residents have taken shelter under the shade of a nearby tree, while hardier locals excersize in the head of the day.";
								break;
								case 2:
									$t = "The afternoon sun is hot, and makes sweat bead on your forehead.  Some of the residents congregate for cool drinks outside of the Prancing SpiderKitty";
								break;
								case 3:
									$t = "The afternoon sun is warm and comforting. Some residents are engaged in conversation outside a Haberdashery.";
								break;
								case 4:
									$t = "The sun is warm and comforting in the clear afternoon sky. Some residents are engaged in conversation outside Petra's tattoo parlour.  Other locals enjoy a cool drink under a nearby tree.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, but the high sun is quickly drying up the moisture.";
									}
								break;
								case 5:
									$t = "The mild afternoon sky has begun to darken, and light rain showers sprinkle the ground.  A few smokers have taken shelter under a nearby awning.
";
								break;
								case 6:
									$t = "The afternoon sky is dark with clouds.  A torrential downpour has turned Improbable Central into a sea of muddy puddles.";
								break;
								case 7:
									$t = "The afternoon sky is dark with angry clouds.  Flashes of lightning and crashes of thunder punctuate a heavy rain.  Everyone with sense and means has taken shelter, and it sounds like an impromptu party is going on at The Prancing SpiderKitty.";
								break;
							}
						break;
						case 5:
							//sunset
							switch ($stateinfo['weather']){
								case 1:
									$t = "Though the sun is beginning to go down, the evening is still hot.  Small groups of residents mill about, chatting as they prepare to end the day.";
								break;
								case 2:
									$t = "The evening sky is still bright as the sun begins to go down.  Several residents are taking advantage of the lingering warmth, chatting in small knots as the day winds down.";
								break;
								case 3:
									$t = "The evening sky is bright and clear as the sun begins to set.  You hear small murmurs of activity as locals wind down their business and prepare to turn in for the night.";
								break;
								case 4:
									$t = "The chilly evening sky is bright and lovely, as you watch a perfect sunset's glow across the nearby plains.  Locals chat in small knots, as they make their way back to their homes to prepare for their evening.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain.";
									}
								break;
								case 5:
									$t = "You can barely see the sun set from behind its blanket of clouds.  You hear the murmur of activity begin to wind down, as locals prepare to turn in for the night.";
								break;
								case 6:
									$t = "You know it's evening, but you can't see the sunset through the darkening rain. The residents are hurriedly finishing up their tasks, in an effort to quickly return home.";
								break;
								case 7:
									$t = "The evening sun is nowhere to be seen amidst the crackle and flash of the storm.  Residents quickly seek shelter from the driving wind and pouring rain.";
								break;
							}
						break;
						case 6:
							//dusk
							switch ($stateinfo['weather']){
								case 1:
									$t = "The evening air is warm and damp.  You catch sight of a few strange lights, as hear the murmur of voices, as residents continue to converse in the dark of the evening.";
								break;
								case 2:
									$t = "The darkening evening air is moist on your brow.  A small group of people head towards a strange looking rock.";
								break;
								case 3:
									$t = "The sun has disappeared, and the stars are beginning to peek though the dome of the clear sky.  You spot a grouping of stars that looks like both an insect and a cat at the same time; you aren't quite sure how.";
								break;
								case 4:
									$t = "Though the sun has set, the cool, cloudless sky is lit by a million tiny pinpoints of starlight.  A few residents recline on the ground, while others point various instruments at the heavens.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and you wonder if there will be ground frost tonight.";
									}
								break;
								case 5:
									$t = "The sun has gone to bed, and the sticky night air feels like a warm bowl of soup.  A few residents are chatting outside The Prancing SpiderKitty, smoking.";
								break;
								case 6:
									$t = "The sky has opened up and pours down rain.  You can barely see your hand in front of your wet face.  The locals seem to have taken shelter elsewhere.";
								break;
								case 7:
									$t = "The blackness of the sky is lit only by the intermittent flash of lightning.  A harsh wind and torrential rain pelts you.  The sensible residents seem to have taken shelter.";
								break;
							}
						break;
						case 7:
							//night
							switch ($stateinfo['weather']){
								case 1:
									$t = "The cold night air is lit up with the many stars in their improbable alignments.";
								break;
								case 2:
									$t = "You shiver a bit as you gaze up at the light of what seems like a million stars.";
								break;
								case 3:
									$t = "Though the sun is gone, pinpoints of starlight illuminate the mild air.  Looking up, you can see the constellation 'Smoking SpiderKitty.'";
								break;
								case 4:
									$t = "Though the sun is long gone, the air is still thick and warm from the heat of the day.  A group of residents gather around a telescope, and point out various familiar forms.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and you wonder if there will be ground frost tonight.";
									}
								break;
								case 5:
									$t = "Though the sun is long gone, the air is thick and moist.  The stars are hiding behind a thick bank of clouds tonight, and you can barely make out your own hands.";
								break;
								case 6:
									$t = "The night is dark, and the sky has opened up and turned into a curtain of black rain.";
								break;
								case 7:
									$t = "The night is indeed dark and stormy.  Loud claps of thunder are punctuated by the high pitched whistle of heavy winds.  The locals appear to be sheltering elsewhere.";
								break;
							}
						break;
					}
					$args['clock'] = "`0".$t."`n`n";
				break;
			}
			break;
		}
	return $args;
}

function timeandweather_outposts_run(){
}
?>