<?php

function timeandweather_outposts_getmoduleinfo(){
	$info = array(
		"name"=>"Time and Weather: Outposts",
		"version"=>"2010-11-02",
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
			$outdoors = true;
			require_once "modules/timeandweather.php";
			$stateinfo = timeandweather_getcurrent();
			switch ($session['user']['location']){
				case "NewHome":
					$shady = false;
					switch($stateinfo['timezone']){
						case 1:
							//dawn
							switch ($stateinfo['weather']){
								case 1:
									$t = "A chilly fog hangs barely visible in the red dawn light.  Tiny fingers of frost stretch out from the wet ground, making a crunching noise under your feet.";
									$brightness = "darker";
								break;
								case 2:
									$t = "The red dawn light does little to illuminate the dense mist that hands over the outpost.  A few early risers are huddled in a knot, chatting near the museum.";
									$brightness = "darker";
								break;
								case 3:
									$t = "There's a chill in the red dawn air, as you take stock of your surroundings.  The buildings and vegetation are covered with a thin film of dew.";
									$brightness = "darker";
								break;
								case 4:
									$t = "Though the sun is a mere glimmer on the horizon, the skies are clear for miles.  A few early risers are taking advantage of the warmth by playing some sort of pitching game.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and the smell is sweet and earthy.";
									}
									$brightness = "darker";
								break;
								case 5:
									$t = "The reddish dawn peaks through a cold and uncomfortable drizzly rain.  You spy a lonely smoker taking shelter under an awning.";
									$rainy = 1;
									$brightness = "darkest";
								break;
								case 6:
									$t = "The faint light of dawn struggles to push through the artificial dark of a pouring rain.  All sensible residents have taken shelter.";
									$rainy = 2;
									$brightness = "darkest";
								break;
								case 7:
									$t = "Dawn seems to have been rescheduled by the weather. Though it's early day, thunder plays angry counterpoint to the dark of a stormy rain.  There is no sign of inhabitants - most likely the residents are all snug in their beds.";
									$rainy = 3;
									$brightness = "darkest";
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
									$rainy = 1;
									$brightness = "darker";
								break;
								case 6:
									$t = "There's a sunrise somewhere, but it's not here.  The heavy rain pulls a curtain of artificial night over the sleepy outpost.  You spy a huddling smoker taking shelter under a nearby ledge.";
									$rainy = 2;
									$brightness = "darkest";
								break;
								case 7:
									$t = "The sun has been completely pushed back by the buffeting gales.  A strong wind whips pebbles at your face, and you narrowly miss stepping in a growing puddle of mud that looks deep enough to reach your knees.  The locals know better than to be out in this weather, and are likely sheltering safely elsewhere.";
									$rainy = 3;
									$brightness = "darkest";
								break;
							}
						break;
						case 3:
							//morning
							switch ($stateinfo['weather']){
								case 1:
									$t = "The morning air is thick and muggy, and makes you feel as if you're walking through a warm bowl of soup.   A few locals seem to have gathered for open-air exercise.  You admire their fortitude.";
									$brightness = "lighter";
								break;
								case 2:
									$t = "The sun is out in full force today; you can almost see the steam rise from your brow.  Some locals are taking advantage of the sunny day to play a strange variety of cricket.";
									$brightness = "lighter";
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
									$rainy = 1;
								break;
								case 6:
									$t = "The darkening morning sky is full of dense, angry looking clouds.  You find yourself caught in a torrential downpour, and start to wonder if there's anywhere around to buy an umbrella.";
									$brightness = "darker";
									$rainy = 2;
								break;
								case 7:
									$t = "The dark morning sky is split by lightning and the crash of thunder all around.  The outpost's residents appear to all be taking shelter.";
									$brightness = "darkest";
									$rainy = 3;
								break;
							}
						break;
						case 4:
							//afternoon
							switch ($stateinfo['weather']){
								case 1:
									$t = "The high sun makes the unrelenting humidity seem even worse.  Some residents have taken shelter under the shade of a nearby tree.";
									$brightness = "lighter";
								break;
								case 2:
									$t = "The afternoon sun is hot, and makes sweat bead on your forehead.  Some residents enjoy cool drinks under a nearby tree.";
									$brightness = "lighter";
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
									$rainy = 1;
								break;
								case 6:
									$t = "The afternoon sky is dark with clouds.  A torrential downpour has made many muddy puddles, which you try to avoid as you pick your way through the outposts.  The residents seem to have taken shelter in nearby establishments.";
									$brightness = "darker";
									$rainy = 2;
								break;
								case 7:
									$t = "The afternoon sky is dark with angry clouds.  Flashes of lightning and crashes of thunder punctuate a heavy rain.  Everyone with sense and means has taken shelter.";
									$brightness = "darkest";
									$rainy = 3;
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
									$brightness = "darker";
								break;
								case 6:
									$t = "You know it's evening, but you can't see the sunset through the darkening rain. The residents are hurriedly finishing up their tasks, in an effort to quickly return home.  The torches around the Outpost are being lit, and they burn brightly despite the rain.";
									$brightness = "darkest";
									$rainy = 2;
								break;
								case 7:
									$t = "The evening sun is nowhere to be seen amidst the crackle and flash of the storm.  Residents quickly seek shelter from the driving wind and pouring rain as the torches that light the outpost sputter and die.";
									$brightness = "darkest";
									$rainy = 3;
								break;
							}
						break;
						case 6:
							//dusk
							switch ($stateinfo['weather']){
								case 1:
									$t = "The evening air is warm and damp.  You catch sight of a few flashlights and some lanterns, as residents continue to converse in the dark of the evening.";
									$brightness = "darker";
								break;
								case 2:
									$t = "The darkening evening air is moist on your brow. Some residents are engaged in conversation in the pool of light outside the Museum.";
									$brightness = "darker";
								break;
								case 3:
									$t = "The sun has disappeared, and the stars are beginning to peek though the dome of the clear sky.";
									$brightness = "darker";
								break;
								case 4:
									$t = "Though the sun has set, the cool, cloudless sky is lit by a million tiny pinpoints of starlight.  A few residents recline on the ground, stargazing.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and you wonder if there will be ground frost tonight.";
									}
									$brightness = "darker";
								break;
								case 5:
									$t = "The sun has gone to bed, and the sticky night air feels like a warm bowl of soup.  A few residents are engaged in conversation outside the Museum, despite the fine drizzle of rain.  You understand why the Museum seems so attractive.";
									$rainy = 1;
									$brightness = "darker";
								break;
								case 6:
									$t = "The sky has opened up and pours down rain, blotting out the light of the stars and moon, casting NewHome into eerie darkness puncutated by sputtering torches.  The locals seem to have taken shelter elsewhere.";
									$rainy = 2;
									$brightness = "darkest";
								break;
								case 7:
									$t = "The blackness of the sky is lit only by the intermittent flash of lightning and the occasional struggling torch.  Harsh wind and torrential rain hammers against you.  The sensible residents seem to have taken shelter.";
									$rainy = 3;
									$brightness = "darkest";
								break;
							}
						break;
						case 7:
							//night
							switch ($stateinfo['weather']){
								case 1:
									$t = "The cold night air is lit up with the many stars in their improbable alignments.  You're not sure whether these alien constellations would be visible outside of the Improbability Bubble surrounding the Island.";
									$brightness = "darkest";
								break;
								case 2:
									$t = "You shiver a bit as you gaze up at the light of what seems like a million stars.";
									$brightness = "darkest";
								break;
								case 3:
									$t = "Though the sun is gone, pinpoints of starlight illuminate the mild air.  Looking up, you can see the constellation 'Sneaky Lion.'";
									$brightness = "darkest";
								break;
								case 4:
									$t = "Though the sun is long gone, the air is still thick and warm from the heat of the day.  Some residents appear to be having a stargazing party, and point out various familiar forms.";
									if ($stateinfo['change'] < 0){
										$t .= "  The ground is still a little damp from the earlier rain, and you wonder if there will be ground frost tonight.";
									}
									$brightness = "darkest";
								break;
								case 5:
									$t = "Though the sun is long gone, the air is thick and moist, with a fine drizzle of cold, lazy rain.  The stars are hiding behind a thick bank of clouds tonight - if it weren't for the torches dotted about the Outpost, NewHome would be pitch black.";
									$rainy = 1;
									$brightness = "darkest";
								break;
								case 6:
									$t = "The night is dark, and the sky has opened up and turned into a curtain of black rain.";
									$rainy = 2;
									$brightness = "darkest";
								break;
								case 7:
									$t = "The night is indeed dark and stormy.  Loud claps of thunder are punctuated by the high pitched whistle of heavy winds.  The locals appear to be sheltering elsewhere.";
									$rainy = 3;
									$brightness = "darkest";
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