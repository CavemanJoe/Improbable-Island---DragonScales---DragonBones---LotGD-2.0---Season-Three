<?php
//
//NOTE: This file is under the GPL.
//
//NOTE: This file would probably be good to cache.
//
//Formats terrain.cfg as an array.
//It works on the process $array[$string][$property][$value]
$terrainsinfo=array(


//BEGIN: WATER (12)
//
//BEGIN: Deep Water (12)

	"Wog"=>array(
		"image"=>"water/ocean-grey-tile",
		"id"=>"deep_water_gray",
		"name"=>"Deep Water",
		"editorname"=>"Gray Deep Water",
		"editorgroup"=>"water",
		"terraintype"=>"Deep Water"),
	"Wo"=>array(
		"image"=>"water/ocean-tile",
		"id"=>"deep_water",
		"name"=>"Deep Water",
		"editorname"=>"Medium Deep Water",
		"editorgroup"=>"water",
		"terraintype"=>"Deep Water"),
	"Wot"=>array(
		"image"=>"water/ocean-tropical-tile",
		"id"=>"deep_water_tropical",
		"name"=>"Deep Water",
		"editorname"=>"Tropical Deep Water",
		"editorgroup"=>"water",
		"terraintype"=>"Deep Water"),
	"Wog"=>array(
		"image"=>"water/ocean-grey-tile",
		"id"=>"",
		"name"=>"",
		"editorname"=>"",
		"editorgroup"=>"",
		"terraintype"=>""),

//END: Deep Water (67)
//
//BEGIN: Shallow Water (67)

	"Wwg"=>array(
		"image"=>"water/coast-grey-tile",
		"id"=>"gray_tropical_water",
		"name"=>"Shallow Water",
		"editorname"=>"Gray Shallow Water",
		"editorgroup"=>"water",
		"terraintype"=>"Shallow Water"),

	"Ww"=>array(
		"image"=>"water/coast-tile",
		"id"=>"shallow_water",
		"name"=>"Shallow Water",
		"editorname"=>"Medium Shallow Water",
		"editorgroup"=>"water",
		"terraintype"=>"Shallow Water"),
	"Wwt"=>array(
		"image"=>"water/coast-tropical-tile",
		"id"=>"tropical_water",
		"name"=>"Shallow Water",
		"editorname"=>"Tropical Shallow Water",
		"editorgroup"=>"water",
		"terraintype"=>"Shallow Water"),
	"Wwf"=>array(
		"image"=>"water/ford-tile",
		"id"=>"ford",
		"name"=>"Ford",
		"editorname"=>"Ford",
		"editorgroup"=>"water",
		"terraintype"=>"Shallow Water"),//also flat (flatness to be added)
	"Wwrg"=>array(
		"image"=>"water/water/reef-gray-tile",
		"id"=>"gray_reef",
		"name"=>"Coastal Reef",
		"editorname"=>"Gray Coastal Reef",
		"editorgroup"=>"water",
		"terraintype"=>"Shallow Water"),
	"Wwr"=>array(
		"image"=>"water/reef-tile",
		"id"=>"reef",
		"name"=>"Coastal Reef",
		"editorname"=>"Coastal Reef",
		"editorgroup"=>"water",
		"terraintype"=>"Shallow Water"),
	"Wwrt"=>array(
		"image"=>"water/reef-tropical-tile",
		"id"=>"tropical_reef",
		"name"=>"Coastal Reef",
		"editor_name"=>"Tropical Coastal Reef",    
		"editor_group"=>"water",
		"terraintype"=>"Shallow Water"),

//END: Shallow Water (119)
//
//END: WATER (119)
//
//BEGIN: SWAMP (120)

	"Ss"=>array(
		"image"=>"swamp/water-tile",
		"id"=>"swamp_water",
		"name"=>"Swamp",
		"editor_name"=>"Swamp",    
		"editor_group"=>"water",
		"terraintype"=>"Swamp"),
	"Sm"=>array(
		"image"=>"swamp/mud-tile",
		"id"=>"quagmire",
		"name"=>"Swamp",
		"editor_name"=>"Muddy Quagmire",    
		"editor_group"=>"water",
		"terraintype"=>"Shallow Water"),

//END: SWAMP (141)
//
//BEGIN: FLAT (143)
//
//BEGIN: Grass (146)

	"Gg"=>array(
		"image"=>"grass/green",
		"id"=>"grassland",
		"name"=>"Grassland",
		"editor_name"=>"Green Grass",    
		"editor_group"=>"flat",
		"terraintype"=>"Flat"),
	"Gs"=>array(
		"image"=>"grass/semi-dry",
		"id"=>"grass_dry",
		"name"=>"Grassland",
		"editor_name"=>"Semi-dry Grass",    
		"editor_group"=>"flat",
		"terraintype"=>"Flat"),
	"Gd"=>array(
		"image"=>"grass/dry",
		"id"=>"grass_dry",
		"name"=>"Grassland",
		"editor_name"=>"Dry Grass",    
		"editor_group"=>"flat",
		"terraintype"=>"Flat"),
	"Gll"=>array(
		"image"=>"grass/leaf-litter",
		"id"=>"leaf_litter",
		"name"=>"Grassland",
		"editor_name"=>"Leaf Litter",    
		"editor_group"=>"flat",
		"terraintype"=>"Flat"),

//END: Grass (190)
//
//BEGIN: Roads (191)

	"Rb"=>array(
		"image"=>"flat/dirt-dark",
		"id"=>"dirt_dark",
		"name"=>"Grassland",
		"editor_name"=>"Leaf Litter",    
		"editor_group"=>"flat",
		"terraintype"=>"Flat"),
	"Re"=>array(
		"image"=>"grass/leaf-litter",
		"id"=>"dirt",
		"name"=>"Dirt",
		"editor_name"=>"Regular Dirt",    
		"editor_group"=>"flat",
		"terraintype"=>"Flat"),
	"Rd"=>array(
		"image"=>"flat/desert-road",
		"id"=>"road",
		"name"=>"Dirt",
		"editor_name"=>"Dry Dirt",    
		"editor_group"=>"desert,flat",
		"terraintype"=>"Flat"),
	"Rr"=>array(
		"image"=>"flat/road",
		"id"=>"road",
		"name"=>"Road",
		"editor_name"=>"Regular Cobbles",    
		"editor_group"=>"flat",
		"terraintype"=>"Flat"),
	"Rrc"=>array(
		"image"=>"flat/road-clean",
		"id"=>"road_clean",
		"name"=>"Road",
		"editor_name"=>"Clean Gray Cobbles",    
		"editor_group"=>"flat",
		"terraintype"=>"Flat"),
	"Rp"=>array(
		"image"=>"flat/stone-path",
		"id"=>"stone_path",
		"name"=>"Road",
		"editor_name"=>"Overgrown Cobbles",    
		"editor_group"=>"flat",
		"terraintype"=>"Flat"),

//END: Roads (252)
//END: FLAT (252)
//
//BEGIN: FROZEN (254)

	"Ai"=>array(
		"image"=>"frozen/ice",
		"id"=>"ice",
		"name"=>"Ice",
		"editor_name"=>"Ice",    
		"editor_group"=>"frozem",
		"terraintype"=>"Frozen"),
	"Aa"=>array(
		"image"=>"frozen/snow",
		"id"=>"snow",
		"name"=>"Snow",
		"editor_name"=>"Snow",    
		"editor_group"=>"frozen",
		"terraintype"=>"Frozen"),

//END: FROZEN (252)
//
//BEGIN: DESERT (276)

	"Dd"=>array(
		"image"=>"sand/desert",
		"id"=>"desert",
		"name"=>"Desert",
		"editor_name"=>"Desert Sands",    
		"editor_group"=>"desert",
		"terraintype"=>"Sand"),
	"Ds"=>array(
		"image"=>"sand/beach",
		"id"=>"sand",
		"name"=>"Sand",
		"editor_name"=>"Beach Sands",    
		"editor_group"=>"desert",
		"terraintype"=>"Sand"),
	"Dd"=>array(
		"image"=>"sand/desert-oasis",
		"id"=>"oasis",
		"name"=>"Oasis",
		"editor_name"=>"Oasis",    
		"editor_group"=>"desert",
		"terraintype"=>"Sand"),
	"Do"=>array(//^Do
		"image"=>"frozen/ice",
		"id"=>"ice",
		"name"=>"Ice",
		"editor_name"=>"Ice",    
		"editor_group"=>"desert",
		"terraintype"=>"Sand"),
	"Dr"=>array(//^Dr
		"image"=>"misc/rubble-tile",
		"id"=>"sand_rubble",
		"name"=>"Rubble",
		"editor_name"=>"Rubble",    
		"editor_group"=>"desert",
		"terraintype"=>"Sand"),
	"Dc"=>array(//^Dc
		"image"=>"sand/crater",
		"id"=>"crater",
		"name"=>"Crater",
		"editor_name"=>"Crater",    
		"editor_group"=>"desert",
		"terraintype"=>"Sand"),

//END: DESERT (329)
//
//BEGIN: EMBELLISHMENTS (331)

	"Efm"=>array(//^
		"image"=>"embellishments/flowers-mixed",
		"id"=>"flowers_mixed",
		"name"=>"Mixed Flowers",
		"editor_name"=>"Mixed Flowers",    
		"editor_group"=>"embellishments",
		"terraintype"=>null),
	"Gvs"=>array(//^
		"image"=>"embellishments/farm-veg-spring-icon",
		"id"=>"farm",
		"name"=>"Farmland",
		"editor_name"=>"Farmland",    
		"editor_group"=>"embellishments",
		"terraintype"=>null),
	"Es"=>array(//^
		"image"=>"embellishments/stones-small7",
		"id"=>"farm",
		"name"=>"Stones",
		"editor_name"=>"Stones",    
		"editor_group"=>"embellishments",
		"terraintype"=>null),
	"Em"=>array(//^
		"image"=>"embellishments/mushroom",
		"id"=>"mushrooms_small",
		"name"=>"Small Mushrooms",
		"editor_name"=>"Small Mushrooms",    
		"editor_group"=>"embellishments",
		"terraintype"=>null),
	"Emf"=>array(//^
		"image"=>"embellishments/mushroom-farm-small",
		"id"=>"mushrooms_farm",
		"name"=>"Mushroom Farm",
		"editor_name"=>"Mushroom Farm",    
		"editor_group"=>"embellishments",//,cave
		"terraintype"=>null),//maybe cave?
	"Edp"=>array(//^
		"image"=>"embellishments/desert-plant5",
		"id"=>"desert_plants",
		"name"=>"Desert Plants",
		"editor_name"=>"Desert Plants",    
		"editor_group"=>"embellishments",//,desert
		"terraintype"=>null),
	"Edpp"=>array(//^
		"image"=>"embellishments/desert-plant",
		"id"=>"desert_plants_sans_bones",
		"name"=>"Desert Plants",
		"editor_name"=>"Desert Plants",    
		"editor_group"=>"embellishments",//,desert
		"terraintype"=>null),
	"Wm"=>array(//^
		"image"=>"misc/windmill-embellishment-tile",
		"id"=>"windmill",
		"name"=>"Windmill",
		"editor_name"=>"Windmill",    
		"editor_group"=>"embellishments",
		"terraintype"=>null),
	"Eff"=>array(//^
		"image"=>"embellishments/fence-se-nw-01",
		"id"=>"fence",
		"name"=>"Fence",
		"editor_name"=>"Fence",    
		"editor_group"=>"embellishments",
		"terraintype"=>null),
	"Esd"=>array(//^
		"image"=>"embellishments/rocks",
		"id"=>"sand_drifts",
		"name"=>"Stones with Sand Drifts",
		"editor_name"=>"Stones with Sand Drifts",    
		"editor_group"=>"embellishments",//,desert
		"terraintype"=>null),
	"Ewl"=>array(//^
		"image"=>"embellishments/water-lilies-flower-tile",
		"id"=>"water-lilies-flower",
		"name"=>"Flowering Water Lilies",
		"editor_name"=>"Flowering Water Lilies",    
		"editor_group"=>"embellishments",
		"terraintype"=>null),

//END: EMBELLISHMENTS (443)
//
//BEGIN: FORESTS (445)

	"Fet"=>array(//^
		"image"=>"forest/great-tree-tile",
		"id"=>"great_tree",
		"name"=>"Great Tree",
		"editor_name"=>"Great Tree",    
		"editor_group"=>"forest",
		"terraintype"=>"Forest"),
	"Fetd"=>array(//^
		"image"=>"forest/great-tree-dead-tile",
		"id"=>"great_tree_dead",
		"name"=>"Great Tree",
		"editor_name"=>"Dead Great Tree",    
		"editor_group"=>"forest",
		"terraintype"=>"Forest"),
	"Ft"=>array(//^
		"image"=>"forest/tropical-tile",
		"id"=>"tropical_forest",
		"name"=>"Forest",
		"editor_name"=>"Tropical Forest",    
		"editor_group"=>"forest",
		"terraintype"=>"Forest"),
	"Fp"=>array(//^
		"image"=>"forest/pine-tile",
		"id"=>"pine_forest",
		"name"=>"Pine Forest",
		"editor_name"=>"Pine Forest",    
		"editor_group"=>"forest",
		"terraintype"=>"Forest"),
	"Fpa"=>array(//^
		"image"=>"forest/snow-forest-tile",
		"id"=>"snow_forest",
		"name"=>"Forest",
		"editor_name"=>"Snowy Pine Forest",    
		"editor_group"=>"forest",
		"terraintype"=>"Forest"),
	"Fds"=>array(//^
		"image"=>"forest/deciduous-summer-tile",
		"id"=>"deciduous_forest_summer",
		"name"=>"Forest",
		"editor_name"=>"Summer Deciduous Forest",    
		"editor_group"=>"forest",
		"terraintype"=>"Forest"),
	"Fdf"=>array(//^
		"image"=>"forest/deciduous-fall-tile",
		"id"=>"deciduous_forest_fall",
		"name"=>"Forest",
		"editor_name"=>"Fall Deciduous Forest",    
		"editor_group"=>"forest",
		"terraintype"=>"Forest"),
	"Fdw"=>array(//^
		"image"=>"forest/deciduous-winter-tile",
		"id"=>"deciduous_forest_winter",
		"name"=>"Forest",
		"editor_name"=>"Winter Deciduous Forest",    
		"editor_group"=>"forest",
		"terraintype"=>"Forest"),
	"Fda"=>array(//^
		"image"=>"forest/deciduous-winter-snow-tile",
		"id"=>"deciduous_forest_winter_snow",
		"name"=>"Forest",
		"editor_name"=>"Snowy Deciduous Forest",    
		"editor_group"=>"forest",
		"terraintype"=>"Forest"),
	"Fms"=>array(//^
		"image"=>"forest/mixed-summer-tile",
		"id"=>"mixed_forest_summer",
		"name"=>"Forest",
		"editor_name"=>"Summer Mixed Forest",    
		"editor_group"=>"forest",
		"terraintype"=>"Forest"),
	"Fmf"=>array(//^
		"image"=>"forest/mixed-fall-tile",
		"id"=>"mixed_forest_fall",
		"name"=>"Forest",
		"editor_name"=>"Fall Mixed Forest",    
		"editor_group"=>"forest",
		"terraintype"=>"Forest"),
	"Fmv"=>array(//^
		"image"=>"forest/mixed-winter-tile",
		"id"=>"mixed_forest_winter",
		"name"=>"Forest",
		"editor_name"=>"Winter Mixed Forest",    
		"editor_group"=>"forest",
		"terraintype"=>"Forest"),
	"Fma"=>array(//^
		"image"=>"forest/mixed-winter-snow-tile",
		"id"=>"mixed_forest_winter_snow",
		"name"=>"Forest",
		"editor_name"=>"Snowy Mixed Forest",    
		"editor_group"=>"forest, frozen",
		"terraintype"=>"Forest"),//+frozen

//END: FOREST (603)
//
//BEGIN: HILLS (605)

	"Hh"=>array(
		"image"=>"hills/regular",
		"id"=>"hills",
		"name"=>"Hills",
		"editor_name"=>"Regular Hills",    
		"editor_group"=>"rough",
		"terraintype"=>"Hills"),
	"Hhd"=>array(
		"image"=>"hills/dry",
		"id"=>"hills_dry",
		"name"=>"Hills",
		"editor_name"=>"Dry Hills",    
		"editor_group"=>"rough",
		"terraintype"=>"Hills"),
	"Hd"=>array(
		"image"=>"hills/desert",
		"id"=>"desert_hills",
		"name"=>"Dunes",
		"editor_name"=>"Dunes",    
		"editor_group"=>"rough",
		"terraintype"=>"Hills"),//+desert
	"Ha"=>array(
		"image"=>"hills/snow",
		"id"=>"snow_hills",
		"name"=>"Hills",
		"editor_name"=>"Snow Hills",    
		"editor_group"=>"rough",
		"terraintype"=>"Hills"),//+frozen

//END: HILLS (647)
//
//BEGIN: MOUNTAINS (649)

	"Mm"=>array(
		"image"=>"mountains/basic-tile",
		"id"=>"mountains",
		"name"=>"Mountains",
		"editor_name"=>"Mountains",    
		"editor_group"=>"rough",
		"terraintype"=>"Mountains"),
	"Md"=>array(
		"image"=>"mountains/dry-tile",
		"id"=>"desert_mountains",
		"name"=>"Mountains",
		"editor_name"=>"Dry Mountains",    
		"editor_group"=>"rough, desert",
		"terraintype"=>"Mountains"),//+desert
	"Ms"=>array(
		"image"=>"mountains/snow-tile",
		"id"=>"snow_mountains",
		"name"=>"Mountains",
		"editor_name"=>"Snowy Mountains",    
		"editor_group"=>"rough",
		"terraintype"=>"Mountains"),

//END: MOUNTAINS (680)
//
//BEGIN: MIXED
// Mixed terrain type (hils+forest)(terrains.cfg line 682) are not supported, they are dealt with seperately
//END: MIXED
//
//BEGIN: INTERIOR (796)
	"Iwr"=>array(
		"image"=>"interior/wood-regular",
		"id"=>"desert_mountains",
		"name"=>"Basic Wooden Floor",

		"editor_name"=>"Basic Wooden Floor",    
		"editor_group"=>"flat",
		"terraintype"=>"Flat"),
//END: INTERIOR (807)
//
//BEGIN: UNDERGROUND (809)
	"Ii"=>array(//^
		"image"=>"cave/beam-tile",
		"id"=>"lit",
		"name"=>"Lit",
		"editor_name"=>"Beam of Light",    
		"editor_group"=>"cave",
		"terraintype"=>null),
	"Uu"=>array(
		"image"=>"cave/floor6",
		"id"=>"cave",
		"name"=>"Cave",

		"editor_name"=>"Cave Floor",    
		"editor_group"=>"cave",
		"terraintype"=>"Cave"),
	"Uue"=>array(
		"image"=>"cave/earthy-floor3",
		"id"=>"cave_earthy",
		"name"=>"Earthy Cave Floor",
		"editor_name"=>"Earthy Cave Floor",    
		"editor_group"=>"cave",
		"terraintype"=>"Cave"),
	"Ur"=>array(
		"image"=>"cave/flagstones-dark",
		"id"=>"flagstones_dark",
		"name"=>"Road",
		"editor_name"=>"Cave Path",    
		"editor_group"=>"cave, flat",
		"terraintype"=>"Cave"),//+flat
	"Urb"=>array(
		"image"=>"cave/path",
		"id"=>"cave_path",
		"name"=>"Road",
		"editor_name"=>"Dark Flagstones",    
		"editor_group"=>"rough",
		"terraintype"=>"Cave"),//+flat
	"Uf"=>array(
		"image"=>"forest/mushrooms-tile",
		"id"=>"fungus",
		"name"=>"Mushroom Grove",
		"editor_name"=>"Mushroom Grove",    
		"editor_group"=>"cave, forest",
		"terraintype"=>"Cave"),//+forest
	"Ufi"=>array(
		"image"=>"forest/mushrooms-beam-tile",
		"id"=>"fungus_beam",
		"name"=>"Mushroom Grove",
		"editor_name"=>"Lit Mushroom Grove",    
		"editor_group"=>"cave, flat",
		"terraintype"=>"Cave"),//+forest
	"Uh"=>array(
		"image"=>"cave/hills-variation",
		"id"=>"rocky_cave",
		"name"=>"Rockbound Cave",
		"editor_name"=>"Rockbound Cave",    
		"editor_group"=>"rough, cave",
		"terraintype"=>"Cave"),//+hills

//BEGIN: Mine rail tracks (893)

	"Br|"=>array(
		"image"=>"misc/rails-n-s",
		"id"=>"rails",
		"name"=>"Mine Rail",
		"editor_name"=>"Mine Rail",    
		"editor_group"=>"cave",
		"terraintype"=>"Cave"),
	"Br/"=>array(
		"image"=>"cave/floor6",
		"id"=>"cave",
		"name"=>"Mine Rail",
		"editor_name"=>"Mine Rail",    
		"editor_group"=>"cave",
		"terraintype"=>"Cave"),
	"Br\\"=>array(
		"image"=>"cave/floor6",
		"id"=>"cave",
		"name"=>"Mine Rail",
		"editor_name"=>"Mine Rail",    
		"editor_group"=>"cave",
		"terraintype"=>"Cave"),

//END: Mine rail tracks (930)
//END: CAVE (930)
//
//BEGIN: UNWALKABLE (932)

	"Qxu"=>array(
		"image"=>"chasm/regular-tile",
		"id"=>"canyon",
		"name"=>"Chasm",
		"editor_name"=>"Regular Chasm",    
		"editor_group"=>"cave, obstacle",
		"terraintype"=>"Flying"),
	"Qxe"=>array(
		"image"=>"chasm/earthy-tile",
		"id"=>"cave",
		"name"=>"Chasm",

		"editor_name"=>"Earthy Chasm",    
		"editor_group"=>"cave, obstacle",
		"terraintype"=>"Flying"),
	"Qxua"=>array(
		"image"=>"chasm/abyss-tile",
		"id"=>"cave",
		"name"=>"Chasm",
		"editor_name"=>"Cave Floor",    
		"editor_group"=>"Ethereal Abyss",
		"terraintype"=>"Flying"),
	"Ql"=>array(
		"image"=>"unwalkable/lava-chasm-tile",
		"id"=>"cave",
		"name"=>"Chasm",

		"editor_name"=>"Lava Chasm",    
		"editor_group"=>"cave, obstacle",
		"terraintype"=>"Flying"),
	"Qlf"=>array(
		"image"=>"unwalkable/lava",
		"id"=>"lava",
		"name"=>"Lava",
		"editor_name"=>"Lava",    
		"editor_group"=>"cave, obstacle",
		"terraintype"=>"Flying"),
	"Qt"=>array(
		"image"=>"mountains/volcano-tile",
		"id"=>"cave",
		"name"=>"Chasm",
		"editor_name"=>"Lava Chasm",    
		"editor_group"=>"rough, obstacle",
		"terraintype"=>"Flying"),

//END: UNWALKABLE (1000)
//
//BEGIN: IMPASSABLE (1002)
		
	//This is commented out as currently different parts of a tile are generated individually, complete ones don't work,
	//so these will be done by mountain tile+cloud tile
	/*"Mm^Xm"=>array(
		"image"=>"mountains/cloud-tile",
		"id"=>"cloud",
		"name"=>"Chasm",
		"editor_name"=>"Regular Impassable Mountains",    
		"editor_group"=>"rough, obstacle",
	"Md^Xm"=>array(
		"image"=>"mountains/cloud-desert-tile",
		"id"=>"clouddesert",
		"name"=>"Chasm",
		"editor_name"=>"Lava Chasm",    
		"editor_group"=>"Desert Impassable Mountains",
		"terraintype"=>"Flying"),
	"Ms^Xm"=>array(
		"image"=>"mountains/cloud-snow-tile",
		"id"=>"cloud_snow",
		"name"=>"Chasm",
		"editor_name"=>"Lava Chasm",    
		"editor_group"=>"Snowy Impassable Mountains",
		"terraintype"=>"Flying"),*/
	"Xm"=>array(
		"image"=>"mountains/cloud",
		"id"=>"cloud",
		"name"=>"Cloud",
		"editor_name"=>"Impassable ",    
		"editor_group"=>"Impassable",
		"terraintype"=>"Impassable"),


	"Xu"=>array(
		"image"=>"cave/wall-rough-tile",
		"id"=>"cavewall",
		"name"=>"Cave Wall",
		"editor_name"=>"Natural Cave Wall",    
		"editor_group"=>"cave, obstacle",
		"terraintype"=>"Impassable"),
	"Xuc"=>array(
		"image"=>"cave/wall-hewn-tile",
		"id"=>"cavewall",
		"name"=>"Cave Wall",
		"editor_name"=>"Hewn Cave Wall",    
		"editor_group"=>"cave, obstacle",
		"terraintype"=>"Impassable"),
	"Xue"=>array(
		"image"=>"cave/earthy-wall-rough-tile",
		"id"=>"cavewall",
		"name"=>"Cave Wall",
		"editor_name"=>"Natural Earthy Cave Wall",    
		"editor_group"=>"cave, obstacle",
		"terraintype"=>"Impassable"),
	"Xuce"=>array(
		"image"=>"cave/earthy-wall-hewn-tile",
		"id"=>"cavewall",
		"name"=>"Cave Wall",
		"editor_name"=>"Reinforced Earthy Cave Wall",    
		"editor_group"=>"cave, obstacle",
		"terraintype"=>"Impassable"),
	"Xos"=>array(
		"image"=>"walls/wall-stone-tilee",
		"id"=>"wall_stone",
		"name"=>"Cave Wall",
		"editor_name"=>"Stone Wall",    
		"editor_group"=>"cave, obstacle",
		"terraintype"=>"Impassable"),
	"Xol"=>array(
		"image"=>"walls/wall-stone-tile",
		"id"=>"wall_stone_lit",
		"name"=>"Stone Wall",
		"editor_name"=>"Lit Stone Wall",    
		"editor_group"=>"cave, obstacle",
		"terraintype"=>"Impassable"),
	"Xo"=>array(
		"image"=>"fog/fog3",//fog3 chosen for impassable mask
		"id"=>"impassable_overlay",
		"name"=>"Impassable",
		"editor_name"=>"Impassable Overlay",    
		"editor_group"=>"cave, obstacle",
		"terraintype"=>"Impassable"),
	"Xv"=>array(
		"image"=>"void/void",
		"id"=>"void",
		"name"=>"Void",
		"editor_name"=>"Void",    
		"editor_group"=>"obstacle, special",
		"terraintype"=>"Impassable"),

//END: IMPASSABLE (1120)
//
//BEGIN: VILLAGES (1122)

	"Vi"=>array(
		"image"=>"human",//village old name, here for compatability with older version of BfW
		"id"=>"village",
		"name"=>"Village",
		"editor_name"=>"Village",    
		"editor_group"=>"village",
		"terraintype"=>"Flat"),

//BEGIN: Desert (1125)

	"Vda"=>array(
		"image"=>"village/desert-tile",
		"id"=>"desert_village",
		"name"=>"Village",
		"editor_name"=>"Adobe Village",    
		"editor_group"=>"village, desert",
		"terraintype"=>"Flat"),
	"Vdt"=>array(
		"image"=>"village/desert-camp-tile",
		"id"=>"desert_village",
		"name"=>"Village",
		"editor_name"=>"Desert Tent Village",    
		"editor_group"=>"village, desert",
		"terraintype"=>"Flat"),
	"Vct"=>array(
		"image"=>"village/camp-tile",
		"id"=>"desert_village",
		"name"=>"Village",
		"editor_name"=>"Tent Village",    
		"editor_group"=>"village",
		"terraintype"=>"Flat"),

//END: Desert (1165)
//BEGIN: Orcish (1166)

	"Vo"=>array(
		"image"=>"village/orc-tile",
		"id"=>"orcish_village",
		"name"=>"Village",
		"editor_name"=>"Orcish Village",    
		"editor_group"=>"village",
		"terraintype"=>"Flat"),
	"Voa"=>array(
		"image"=>"village/orc-snow-tile",
		"id"=>"orcish_snow_village",
		"name"=>"Village",
		"editor_name"=>"Snowy Orcish Village",    
		"editor_group"=>"village",
		"terraintype"=>"Flat"),

//End: Orcish (1193)
//Begin: Elven (1194)

	"Vea"=>array(
		"image"=>"village/elven-snow-tile",
		"id"=>"elven_snow_village",
		"name"=>"Village",
		"editor_name"=>"Snowy Elven Village",    
		"editor_group"=>"village",
		"terraintype"=>"Flat"),
	"Ve"=>array(
		"image"=>"village/elven-tile",
		"id"=>"elven_village",
		"name"=>"Village",
		"editor_name"=>"Elven Village",    
		"editor_group"=>"village",
		"terraintype"=>"Flat"),

//End: Elven (1221)
//Begin: Human (1222)
	"Vh"=>array(
		"image"=>"village/human-tile",
		"id"=>"human_village",
		"name"=>"Village",
		"editor_name"=>"Cottage",    
		"editor_group"=>"village",
		"terraintype"=>"Flat"),
	"Vha"=>array(
		"image"=>"village/snow-tile",
		"id"=>"snow_village",
		"name"=>"Village",
		"editor_name"=>"Snowy Cottage",    
		"editor_group"=>"village",
		"terraintype"=>"Flat"),
	"Vhr"=>array(
		"image"=>"village/human-cottage-ruin-tile",
		"id"=>"human_village_ruin",
		"name"=>"Village",
		"editor_name"=>"Ruined Cottage",    
		"editor_group"=>"village",
		"terraintype"=>"Flat"),
	"Vhc"=>array(
		"image"=>"village/human-city-tile",
		"id"=>"city_village",
		"name"=>"Village",

		"editor_name"=>"Human City",    
		"editor_group"=>"village",
		"terraintype"=>"Flat"),
	"Vwm"=>array(
		"image"=>"village/windmill-tile",
		"id"=>"windmill_village",
		"name"=>"Village",
		"editor_name"=>"Windmill Village",    
		"editor_group"=>"village",
		"terraintype"=>"Flat"),
	"Vhca"=>array(
		"image"=>"village/human-city-snow-tile",
		"id"=>"city_village_wno",
		"name"=>"Village",
		"editor_name"=>"Snowy Human City",    
		"editor_group"=>"village",
		"terraintype"=>"Flat"),
	"Vhcr"=>array(
		"image"=>"village/human-city-ruin-tile",
		"id"=>"city_village",
		"name"=>"Village",
		"editor_name"=>"Ruined Human City",    
		"editor_group"=>"village",
		"terraintype"=>"Flat"),
	"Vhh"=>array(
		"image"=>"village/human-hills-tile",
		"id"=>"hill_village",
		"name"=>"Village",
		"editor_name"=>"Hill Stone Village",    
		"editor_group"=>"village, rough",
		"terraintype"=>"Hills"),//+flat
	"Vhha"=>array(
		"image"=>"village/human-snow-hills-tile",
		"id"=>"snow-hill_village",
		"name"=>"Village",
		"editor_name"=>"Snowy Hill Stone Village",    
		"editor_group"=>"village, frozen, rough",
		"terraintype"=>"Frozen"),//+hills+flat
	"Vhhr"=>array(
		"image"=>"village/human-hills-ruin-tile",
		"id"=>"hill_village_ruin",
		"name"=>"Village",
		"editor_name"=>"Ruined Hill Stone Village",    
		"editor_group"=>"village, rough",
		"terraintype"=>"Hills"),//+flat
	"Vht"=>array(
		"image"=>"village/tropical-tile",
		"id"=>"tropical_forest_village",
		"name"=>"Village",
		"editor_name"=>"Tropical Village",    
		"editor_group"=>"village",
		"terraintype"=>"Flat"),//+forest?
	"Vd"=>array(
		"image"=>"village/drake-tile",
		"id"=>"drake_village",
		"name"=>"Village",
		"editor_name"=>"Drake City",    
		"editor_group"=>"village",
		"terraintype"=>"Flat"),
//End: Human (1379)
//Begin: Underground (1380)
	"Vu"=>array(
		"image"=>"village/cave-tile",
		"id"=>"underground_village",
		"name"=>"Village",
		"editor_name"=>"Cave City",    
		"editor_group"=>"village",
		"terraintype"=>"Cave"),//+flat
	"Vud"=>array(
		"image"=>"village/dwarven-tile",
		"id"=>"dwarven_village",
		"name"=>"Village",
		"editor_name"=>"Dwarven City",    
		"editor_group"=>"village",
		"terraintype"=>"Cave"),//+flat
	"Vca"=>array(
		"image"=>"village/hut-snow-tile",
		"id"=>"hut_snow_village",
		"name"=>"Village",
		"editor_name"=>"Snowy Hut",    
		"editor_group"=>"village",
		"terraintype"=>"Frozen"),//+flat
	"Vc"=>array(
		"image"=>"village/hut-tile",
		"id"=>"hut_village",
		"name"=>"Village",
		"editor_name"=>"Hut",    
		"editor_group"=>"village",
		"terraintype"=>"Flat"),
	"Vl"=>array(
		"image"=>"village/log-cabin-tile",
		"id"=>"logcabin_village",
		"name"=>"Village",
		"editor_name"=>"Log Cabin",    
		"editor_group"=>"village",
		"terraintype"=>"Flat"),
	"Vla"=>array(
		"image"=>"village/log-cabin-tile",
		"id"=>"logcabin_snow_village",
		"name"=>"Village",
		"editor_name"=>"Snowy Log Cabin",    
		"editor_group"=>"village",
		"terraintype"=>"Frozen"),//+flat
	"Vaa"=>array(
		"image"=>"village/igloo-tile",
		"id"=>"igloo",
		"name"=>"Village",
		"editor_name"=>"Igloo",    
		"editor_group"=>"village",
		"terraintype"=>"Frozen"),//+flat

//End: Underground (1472)
//Begin: Water (1473)
	"Vhs"=>array(
		"image"=>"village/swampwater-tile",
		"id"=>"swamp_village",
		"name"=>"Village",
		"editor_name"=>"Swamp Village",    
		"editor_group"=>"water, village",
		"terraintype"=>"Swamp"),//+flat
	"Vm"=>array(
		"image"=>"village/coast-tile",
		"id"=>"mermen-village",
		"name"=>"Village",
		"editor_name"=>"Merfolk Village",    
		"editor_group"=>"water, village",
		"terraintype"=>"Shallow Water"),
	"Vov"=>array(
		"image"=>"fog/fog1",
		"id"=>"village_overlay",
		"name"=>"Village",
		"editor_name"=>"Village",    
		"editor_group"=>"village, special",
		"terraintype"=>"Flat"),

//End: Water
//END: VILLAGES
//
//BEGIN: CASTLES
	"Ce"=>array(
		"image"=>"castle/encampment/regular-tile",
		"id"=>"encampment",
		"name"=>"Encampment",
		"editor_name"=>"Encampment",    
		"editor_group"=>"castle",
		"terraintype"=>"Flat"),
	"Cea"=>array(
		"image"=>"castle/encampment/snow-tile",
		"id"=>"encampment_snow",
		"name"=>"Encampment",
		"editor_name"=>"Snowy Encampment",    
		"editor_group"=>"castle, frozen",
		"terraintype"=>"Frozen"),//+flat
	"Co"=>array(
		"image"=>"village/castle/orcish/tile",
		"id"=>"orcish_fort",
		"name"=>"Castle",
		"editor_name"=>"Orcish Castle",    
		"editor_group"=>"castle",
		"terraintype"=>"Flat"),
	"Coa"=>array(
		"image"=>"castle/winter-orcish/tile",
		"id"=>"snow_orcish_fort",
		"name"=>"Castle",
		"editor_name"=>"Snowy Orcish Castle",    
		"editor_group"=>"castle, frozen",
		"terraintype"=>"Frozen"),
	"Ch"=>array(
		"image"=>"castle/castle-tile",
		"id"=>"castle",
		"name"=>"Castle",
		"editor_name"=>"Human Castle",    
		"editor_group"=>"castle",
		"terraintype"=>"Flat"),
	"Cha"=>array(
		"image"=>"castle/snowy/castle-tile",
		"id"=>"snow_castle",
		"name"=>"Castle",
		"editor_name"=>"Snowy Human Castle",    
		"editor_group"=>"castle, frozen",
		"terraintype"=>"Frozen"),
	"Coa"=>array(
		"image"=>"castle/elven/tile",
		"id"=>"elven_castle",
		"name"=>"Castle",
		"editor_name"=>"Elvish Castle",    
		"editor_group"=>"castle",
		"terraintype"=>"Flat"),
	"Cud"=>array(
		"image"=>"castle/dwarven-castle-tile",
		"id"=>"dwarven_castle",
		"name"=>"Castle",
		"editor_name"=>"Dwarven Castle",    
		"editor_group"=>"castle, cave",
		"terraintype"=>"Cave"),
	"Chr"=>array(
		"image"=>"castle/ruin-tile",
		"id"=>"ruin",
		"name"=>"Ruined Castle",
		"editor_name"=>"Ruined HumanCastle",    
		"editor_group"=>"castle",
		"terraintype"=>"Flat"),	
	"Chw"=>array(
		"image"=>"castle/sunken-ruin-tile",
		"id"=>"sunkenruin",
		"name"=>"Ruined Castle",
		"editor_name"=>"Sunken Human Ruin",    
		"editor_group"=>"castle, water",
		"terraintype"=>"Shallow Water"),
	"Chs"=>array(
		"image"=>"castle/swamp-ruin-tile",
		"id"=>"swampruin",
		"name"=>"Ruined Castle",
		"editor_name"=>"Swamp Human Ruin",    
		"editor_group"=>"castle, water",
		"terraintype"=>"Swamp"),
	"Cd"=>array(
		"image"=>"castle/sand/tile",
		"id"=>"sand_castle",
		"name"=>"Castle",
		"editor_name"=>"Desert Castle",    
		"editor_group"=>"castle, desert",
		"terraintype"=>"Sand"),
	"Cdr"=>array(
		"image"=>"castle/sand/ruin-tile",
		"id"=>"sand_castle_ruin",
		"name"=>"Ruined Castle",
		"editor_name"=>"Ruined Desert Castle",    
		"editor_group"=>"castle, desert",
		"terraintype"=>"Sand"),

//END: CASTLES (1674)
//
//BEGIN: KEEPS (1676)
	"Ke"=>array(
		"image"=>"castle/encampment/regular-keep-tile",
		"id"=>"encampment_keep",
		"name"=>"Encampment Keep",
		"editor_name"=>"Encampment Keep",    
		"editor_group"=>"castle",
		"terraintype"=>"Flat"),
	"Ket"=>array(
		"image"=>"castle/encampment/tall-keep-tile",
		"id"=>"encampment_keep_tall",
		"name"=>"Encampment Keep",
		"editor_name"=>"Tall Encampment Keep",    
		"editor_group"=>"castle",
		"terraintype"=>"Flat"),
	"Kea"=>array(
		"image"=>"castle/encampment/snow-keep-tile",
		"id"=>"encampment_snow_keep",
		"name"=>"Encampment Keep",
		"editor_name"=>"Snowy Encampment Keep",    
		"editor_group"=>"castle, frozen",
		"terraintype"=>"Frozen"),//+flat
	"Ko"=>array(
		"image"=>"castle/orcish/keep-tile",
		"id"=>"orcish_keep",
		"name"=>"Keep",
		"editor_name"=>"Orcish Keep",    
		"editor_group"=>"castle",
		"terraintype"=>"Flat"),
	"Koa"=>array(
		"image"=>"castle/winter-orcish/keep-tile",
		"id"=>"snow_orcish_keep",
		"name"=>"Keep",
		"editor_name"=>"Snowy Orcish Keep",    
		"editor_group"=>"castle, frozen",
		"terraintype"=>"Frozen"),//+flat
	"Kh"=>array(
		"image"=>"castle/keep-tile",
		"id"=>"human_keep",
		"name"=>"Keep",
		"editor_name"=>"Human Keep",    
		"editor_group"=>"castle",
		"terraintype"=>"Flat"),
	"Kha"=>array(
		"image"=>"castle/snowy/keep-tile",
		"id"=>"snow_keep",
		"name"=>"Keep",
		"editor_name"=>"Snowy Human Keep",    
		"editor_group"=>"castle, frozen",
		"terraintype"=>"Frozen"),//+flat
	"Kv"=>array(
		"image"=>"castle/elven/keep-tile",
		"id"=>"elven_keep",
		"name"=>"Keep",
		"editor_name"=>"Elven Castle Keep",    
		"editor_group"=>"castle",
		"terraintype"=>"Frozen"),//+flat
	"Kud"=>array(
		"image"=>"castle/dwarven/keep-tile",
		"id"=>"dwarven_keep",
		"name"=>"Keep",
		"editor_name"=>"Dwarven Keep",    
		"editor_group"=>"castle",
		"terraintype"=>"Flat"),
	"Khr"=>array(
		"image"=>"castle/ruined-keep-tile",
		"id"=>"ruined_keep",
		"name"=>"Ruined Keep",
		"editor_name"=>"Ruined Keep",    
		"editor_group"=>"castle",
		"terraintype"=>"Flat"),
	"Khw"=>array(
		"image"=>"castle/sunken-keep-tile",
		"id"=>"sunken_keep",
		"name"=>"Ruined Keep",
		"editor_name"=>"Sunken Human Castle Keep",    
		"editor_group"=>"castle, water",
		"terraintype"=>"Shallow Water"),
	"Khs"=>array(
		"image"=>"castle/swamp-keep-tile",
		"id"=>"swamp_keep",
		"name"=>"Ruined Keep",
		"editor_name"=>"Swamp Human Castle Keep",    
		"editor_group"=>"castle, water",
		"terraintype"=>"Swamp"),
	"Kd"=>array(
		"image"=>"castle/sand/keep-tile",
		"id"=>"desert_keep",
		"name"=>"Keep",
		"editor_name"=>"Desert Keep",    
		"editor_group"=>"castle, desert",
		"terraintype"=>"Sand"),
	"Kdr"=>array(
		"image"=>"castle/sand/ruin-keep-tile",
		"id"=>"desert_keep_ruined",
		"name"=>"Ruined Keep",
		"editor_name"=>"Ruined Desert Keep",    
		"editor_group"=>"castle, desert",
		"terraintype"=>"Sand"),
	"Cov"=>array(//OVERLAYS
		"image"=>"fog/fog1",
		"id"=>"castle_overlay",
		"name"=>"Castle",
		"editor_name"=>"Castle",    
		"editor_group"=>"castle",
		"terraintype"=>"Flat"),
	"Kov"=>array(
		"image"=>"fog/fog1",
		"id"=>"desert_keep_ruined",
		"name"=>"Keep",
		"editor_name"=>"Keep",    
		"editor_group"=>"castle",
		"terraintype"=>"Flat"),
//END: KEEPS (1884)
//
//START: BRIDGES (1886)
//Start: Woorden (1889)
	"Bw|"=>array(
		"image"=>"bridge/wood-n-s",
		"id"=>"bridge",
		"name"=>"Bridge",
		"editor_name"=>"Wooden Bridge",    
		"editor_group"=>"castle",
		"terraintype"=>"Flat"),//+Shallow Water
	"Bw/"=>array(
		"image"=>"bridge/wood-ne-sw",
		"id"=>"bridgediag1",
		"name"=>"Bridge",
		"editor_name"=>"Wooden Bridge",    
		"editor_group"=>"castle",
		"terraintype"=>"Flat"),//+Shallow Water
	"Bw\\"=>array(
		"image"=>"bridge/wood-se-nw",
		"id"=>"bridgediag2",
		"name"=>"Bridge",
		"editor_name"=>"Wooden Bridge",    
		"editor_group"=>"bridge, water",
		"terraintype"=>"Flat"),//+Shallow Water
	"Bw|r"=>array(
		"image"=>"bridge/wood-rotting-n-s",
		"id"=>"rotbridge",
		"name"=>"Bridge",
		"editor_name"=>"Rotting Bridge",    
		"editor_group"=>"bridge, water",
		"terraintype"=>"Flat"),//+Shallow Water
	"Bw/r"=>array(
		"image"=>"bridge/wood-rotting-ne-sw",
		"id"=>"rotbridgediag2",
		"name"=>"Bridge",
		"editor_name"=>"Rotting Bridge",    
		"editor_group"=>"bridge, water",
		"terraintype"=>"Flat"),//+Shallow Water
	"Bw\\r"=>array(
		"image"=>"bridge/wood-rotting-se-nw",
		"id"=>"rotbridgediag2",
		"name"=>"Bridge",
		"editor_name"=>"Rotting Bridge",    
		"editor_group"=>"bridge, water",
		"terraintype"=>"Flat"),//+Shallow Water
//End: Wooden
//Start: Basic Stone
	"Bsb|"=>array(
		"image"=>"bridge/stonebridge-n-s-tile",
		"id"=>"stone_bridge",
		"name"=>"Bridge",
		"editor_name"=>"Basic Stone Bridge",
		"editor_group"=>"bridge, water",
		"terraintype"=>"Flat"),//+Shallow Water
	"Bsb\\"=>array(
		"image"=>"bridge/stonebridge-se-nw-tile",
		"id"=>"stone_bridge",
		"name"=>"Bridge",
		"editor_name"=>"Basic Stone Bridge",    
		"editor_group"=>"bridge, water",
		"terraintype"=>"Flat"),//+Shallow Water
	"Bsb/"=>array(
		"image"=>"bridge/stonebridge-ne-sw-tile",
		"id"=>"stone_bridge",
		"name"=>"Bridge",
		"editor_name"=>"Basic Stone Bridge",    
		"editor_group"=>"bridge, water",
		"terraintype"=>"Flat"),//+Shallow Water
//End: Basic Stone (2000)
//Start: Chasm (2001)
	"Bs/"=>array(
		"image"=>"cave/chasm-stone-bridge-s-n-tile",
		"id"=>"bridgechasm",
		"name"=>"Bridge",
		"editor_name"=>"Basic Stone Bridge",    
		"editor_group"=>"bridge, cave",
		"terraintype"=>"Flat"),//+Flying
	"Bs/"=>array(
		"image"=>"cave/chasm-stone-bridge-sw-ne-tile",
		"id"=>"bridgechasmdiag1",
		"name"=>"Bridge",
		"editor_name"=>"Cave Chasm Bridge",    
		"editor_group"=>"bridge, cave",
		"terraintype"=>"Flat"),//+Flying
	"Bs\\"=>array(
		"image"=>"chasm-stone-bridge-se-nw-tile",
		"id"=>"bridgechasmdiag2",
		"name"=>"Bridge",
		"editor_name"=>"Cave Chasm Bridge",    
		"editor_group"=>"bridge, cave",
		"terraintype"=>"Flat"),//+Flying

//End:Chasm (2037)
//END: BRIDGE (2037)
//
//START: SPECIAL (2039)

	"_off"=>array(
		"image"=>"off-map/symbol",
		"id"=>"bridgechasmdiag1",
		"name"=>"Void",
		"editor_name"=>"Off Map",    
		"editor_group"=>"special, obstacle",
		"terraintype"=>"Impassable"),
	"_fme"=>array(
		"image"=>"off-map/symbol",
		"id"=>"bridgechasmdiag1",
		"name"=>"Void",
		"editor_name"=>"Experimental Fake Map Edge",    
		"editor_group"=>"special, obstacle",
		"terraintype"=>"Impassable"),
	"_s"=>array(
		"image"=>"void/void",
		"id"=>"shroud",
		"name"=>"shroud",
		"editor_name"=>"Shroud",    
		"editor_group"=>"special",
		"terraintype"=>"Flat"),
	"_f"=>array(
		"image"=>"fog/fog1",
		"id"=>"fog",
		"name"=>"Fog",
		"editor_name"=>"Fog",    
		"editor_group"=>"special",
		"terraintype"=>"Flat"),

//END: SPECIAL (2079)
//
//START: DEPRECATED (2081)

	"Ggf"=>array(
		"image"=>"grass/flowers",
		"id"=>"grassland_flowers",
		"name"=>"Flowers",
		"editor_name"=>"Flowers",    
		"editor_group"=>"Flat",
		"terraintype"=>"Flat"),
	"Qv"=>array(
		"image"=>"mountains/volcano-tile",
		"id"=>"volcano_deprecate",
		"name"=>"Volcano",
		"editor_name"=>"Volcano",    
		"editor_group"=>"rough, obstacle",
		"terraintype"=>"Flying"),

//END: DEPRECATED (2107)
//
//START: TERRAIN ARCHETYPES
	"Gt"=>array(
		"image"=>"void/void",
		"id"=>"flat",
		"name"=>"Flat",
		"editor_name"=>"Flat",    
		"editor_group"=>"rough, obstacle",
		"terraintype"=>"Flying"),
	/*"Ft"=>array(
		"image"=>"void/void",
		"id"=>"forest",
		"name"=>"Forest",
		"editor_name"=>"Forest",    
		"editor_group"=>"rough, obstacle",
		"terraintype"=>"Flying"),*/
	"At"=>array(
		"image"=>"void/void",
		"id"=>"frozen",
		"name"=>"Frozen",
		"editor_name"=>"Frozen",    
		"editor_group"=>"rough, obstacle",
		"terraintype"=>"Flying"),
	"Vi"=>array(
		"image"=>"void/void",
		"id"=>"volcano",
		"name"=>"Volcano",
		"editor_name"=>"Village",    
		"editor_group"=>"rough, obstacle",
		"terraintype"=>"Flying"),
	"Xt"=>array(
		"image"=>"void/void",
		"id"=>"impassable",
		"name"=>"Impassable",
		"editor_name"=>"Impassable",    
		"editor_group"=>"obstacle",
		"terraintype"=>"Impassable"),
	"Qt"=>array(
		"image"=>"void/void",
		"id"=>"unwalkable",
		"name"=>"Unwalkable",
		"editor_name"=>"Unwalkable",    
		"editor_group"=>"obstacle",
		"terraintype"=>"Flying"),

//END: TERRAIN ARCHETYPES
//
//END: BfW TERRAINS
);

?>
