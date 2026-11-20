<?php

/*
|--------------------------------------------------------------------------
| Shanyangi itineraries with group prices (from the company's own PDFs)
|--------------------------------------------------------------------------
|
| Loaded by MorePackagesSeeder: a package is only added when its slug doesn't
| exist yet. Prices are per person by group size (price_tiers); the card's
| "From" price is set to the lowest of them automatically.
|
*/

$img = fn (string $id) => "https://images.unsplash.com/photo-{$id}?auto=format&fit=crop&w=1200&q=75";

$photos = [
    'plains' => '1516426122078-c23e76319801', 'seronera' => '1535941339077-2dd1c7963098',
    'serengeti' => '1516026672322-bc52d61a55d5', 'rim' => '1500835556837-99ac94a94552',
    'tarangire' => '1585970480901-90d6bb2a48b5', 'crater' => '1523805009345-7448845a9e53',
    'arusha' => '1547471080-7cc2caa01a7e', 'manyara' => '1549366021-9f761d450615',
    'balloon' => '1507608616759-54f48f0af0ee', 'tented' => '1504280390367-361c6d9f38f4',
    'lodge' => '1566073771259-6a8506099945', 'campfire' => '1478131143081-80f7f84ca84d',
    'forest' => '1447752875215-b2761acb3c5d', 'beach' => '1507525428034-b723cf961d3e',
    'resort' => '1520250497591-112f2f40a3f4', 'stonetown' => '1571896349842-33c89424de2d',
    'snorkel' => '1544551763-46a013bb70d5', 'map' => '1488646953014-85cb44e25828',
    'waterfall' => '1432405972618-c60b0225b8f9',
];
$p = fn (string $key) => $img($photos[$key]);

// Price per person for groups of 2, 4 and 6.
$tiers = fn (int $two, int $four, int $six) => [
    ['people' => 2, 'price' => $two], ['people' => 4, 'price' => $four], ['people' => 6, 'price' => $six],
];

// Overnight: the lodge (as an accommodation option) plus a photo of that kind of stay.
$stay = fn (string $area, string $level, string $lodge, string $photo, string $caption, string $meals = 'Full board') => [
    'stay' => $area, 'meals' => $meals,
    'options' => [['level' => $level, 'name' => $lodge.' or similar']],
    'stay_image' => $p($photo), 'stay_caption' => $caption,
];

$safariIncluded = [
    '4WD safari vehicle with pop-up roof',
    'Three meals a day while on safari',
    'All park fees',
    "Driver's allowances",
    'English-speaking professional guide/driver',
    'Accommodation as per itinerary',
    'Government taxes',
    'Activities as described in the itinerary',
];

$safariExcluded = [
    'International flights',
    'Tips for your driver and hotel staff',
    'Visa fees',
    'Accommodation after the safari (book with us for an extended stay)',
    'Travel and health insurance',
    'Meals, drinks and excursions not shown in the itinerary',
    'Items of a personal nature',
];

$tourIncluded = [
    'All accommodation (unless listed as an upgrade)',
    'A professional driver/guide',
    'All transportation (unless labelled as optional)',
    'All taxes/VAT',
    'Round-trip airport transfers',
    'Meals as specified in the day-by-day itinerary',
];

$tourExcluded = [
    'International flights (from/to home)',
    'Additional accommodation before and at the end of the tour',
    'Tips',
    'Personal items (souvenirs, travel insurance, visa fees, etc.)',
    'Government-imposed increases in taxes and/or park fees',
    'Meals not specified in the day-by-day itinerary',
];

// Optional activities, shown below the itinerary.
$optional = [
    ['name' => 'Hot air balloon ride', 'price' => 'USD 500 per person per ride'],
    ['name' => 'Maasai boma visit', 'price' => 'USD 50 per vehicle'],
    ['name' => 'Walking safari', 'price' => 'From USD 30 per person'],
    ['name' => 'Sundowners', 'price' => 'From USD 50 per person'],
    ['name' => 'Olduvai Gorge (early human fossils)', 'price' => 'USD 36 per person'],
];


return [

    // 1 ─────────────────────────────────────────────────────────────────────
    [
        'slug' => 'wildebeest-great-migration-luxury-safari-4-days',
        'title' => 'Wildebeest Great Migration Luxury Safari',
        'category' => 'Safari',
        'style' => 'luxury',
        'types' => ['migration', 'luxury'],
        'tagline' => 'Fly in to the Mara River crossings',
        'chips' => [['label' => '4 Days'], ['label' => 'Fly-in'], ['label' => 'Great Migration', 'highlight' => true]],
        'text' => 'Fly from Arusha to the Northern Serengeti to follow the Great Migration near the Mara River, then explore the Central Serengeti, staying in high-end lodges.',
        'price_tiers' => $tiers(3771, 3303, 3146),
        'image' => $p('plains'),
        'location' => 'Northern & Central Serengeti, Tanzania',
        'overview' => [
            'Set off on an unforgettable journey through the heart of Tanzania\'s most iconic landscapes. Witness the awe-inspiring spectacle of the Great Migration as millions of wildebeest, zebras and gazelles cross the vast plains of the Serengeti National Park in their annual search for green pasture.',
            'Over four days you explore the Serengeti, renowned for its endless horizons and abundant wildlife, with the chance to see the Big Five in their natural habitat, while staying in high-end accommodation.',
            'Highlights: the Great Migration of millions of wildebeest with zebras and antelopes; endless plains; the Big Five (lion, elephant, buffalo, leopard and rhino); big cats and predators on the hunt; more than 500 bird species.',
        ],
        'facts' => ['Duration' => '4 Days · 3 Nights', 'Group size' => 'Private', 'Best time' => 'July – October (river crossings)'],
        'itinerary' => [
            [
                'title' => 'Arusha to the Northern Serengeti (Kogatende)',
                'image' => $p('plains'), 'caption' => 'Northern Serengeti',
                'text' => 'Around 06:30 we pick you up from your hotel with a packed lunch and take you to Arusha Airport for the two-hour flight to the Northern Serengeti. On arrival your game drive begins in one of the world\'s great wildlife destinations: a World Heritage Site of nearly 15,000 square kilometres, home to millions of wildebeest, thousands of lions and leopards, hundreds of cheetahs and more than 500 bird species. Drive to your camp for dinner and the night.',
                ...$stay('Northern Serengeti', 'Luxury', 'Mara Mara Tented Lodge', 'tented', 'Tented lodge', 'Full board with drinks'),
            ],
            [
                'title' => 'Full game drive in the Northern Serengeti (Mara River)',
                'image' => $p('seronera'), 'caption' => 'Mara River',
                'text' => 'A full day of game viewing in the north. In season you may meet the Great Migration at the Mara River, where columns of wildebeest, zebras and antelopes face strong currents, waiting crocodiles and steep riverbanks. Crossings are hard to predict: a herd can wait by the river for days before it decides to cross. The area is beautiful, with kopjes, woodland, riverine forest and open plains. Picnic lunch, then an afternoon game drive back to camp.',
                'note' => 'River crossings cannot be guaranteed and are sometimes a matter of luck.',
                ...$stay('Northern Serengeti', 'Luxury', 'Mara Mara Tented Lodge', 'tented', 'Tented lodge', 'Full board with drinks'),
            ],
            [
                'title' => 'Full game drive in the Central Serengeti',
                'image' => $p('serengeti'), 'caption' => 'Central Serengeti',
                'text' => 'With lunch boxes, drive south through the Central Serengeti, which supports every kind of animal imaginable: lions, leopards and cheetahs as well as the herbivores they hunt. Look for leopards resting in the trees and cheetahs showing their speed on the plains. Arrive at your lodge in the late afternoon.',
                ...$stay('Central Serengeti', 'Luxury', 'Serengeti Serena Safari Lodge', 'lodge', 'Safari lodge'),
            ],
            [
                'title' => 'Serengeti National Park to Arusha',
                'image' => $p('rim'), 'caption' => 'Ngorongoro highlands',
                'text' => 'After breakfast, drive back to Arusha through the Ngorongoro Conservation Area, with game viewing on the way and a picnic lunch. We drop you at Kilimanjaro International Airport for your flight home, or at your hotel if you are extending your stay.',
                'meals' => 'Breakfast & lunch',
            ],
        ],
        'included' => [...$safariIncluded, 'VAT (18%)', 'Flight from Arusha to the Serengeti'],
        'excluded' => $safariExcluded,
        'optional' => [['name' => 'Hot air balloon ride', 'price' => 'USD 500–600 per person per ride'], ...array_slice($optional, 1)],
    ],

    // 2 ─────────────────────────────────────────────────────────────────────
    [
        'slug' => 'calving-season-ndutu-safari-5-days',
        'title' => 'Calving Season Safari in Ndutu',
        'category' => 'Safari',
        'style' => 'mid-range',
        'types' => ['migration'],
        'tagline' => 'Thousands of newborn wildebeest every day',
        'chips' => [['label' => '5 Days'], ['label' => 'January – April'], ['label' => 'Calving season', 'highlight' => true]],
        'text' => 'A private safari to Tarangire, the Ngorongoro Crater and two days in the Ndutu area, where the wildebeest give birth to their calves between January and April.',
        'price_tiers' => $tiers(2425, 1927, 1762),
        'image' => $p('serengeti'),
        'location' => 'Tarangire, Ngorongoro & Ndutu, Tanzania',
        'overview' => [
            'On this five-day private safari you will be well looked after and see some of the best that Tanzania has to offer: monkeys in the forest, lions in trees, elephants among the acacias and a huge variety of animals inside a collapsed volcano.',
            'With two days in the Ndutu area of the Ngorongoro Conservation Area between January and April, you have the chance to watch the wildebeest giving birth to their calves.',
            'Highlights: more than 5,000 calves born every day in the Ndutu area; herds of elephants and ancient baobab trees in Tarangire; endless plains; the Big Five and big cats.',
        ],
        'facts' => ['Duration' => '5 Days · 4 Nights', 'Group size' => 'Private', 'Best time' => 'January – April'],
        'itinerary' => [
            [
                'title' => 'Welcome to Tanzania',
                'image' => $p('arusha'), 'caption' => 'Arusha',
                'text' => 'Our representative meets you at Kilimanjaro International Airport with a board showing your name and drives you to your accommodation in Arusha to relax.',
                ...$stay('Arusha', 'Mid-range', 'Arusha Planet Lodge & Spa', 'lodge', 'Arusha lodge', 'Bed & breakfast'),
            ],
            [
                'title' => 'Arusha to Tarangire National Park',
                'image' => $p('tarangire'), 'caption' => 'Tarangire',
                'text' => 'Leave around 08:00 for the 2.5-hour drive (120 km) to Tarangire, a park of baobabs and acacias with the greatest concentration of wildlife in the northern circuit outside the Serengeti ecosystem. Look for zebras, cheetahs, giraffes, lions, Thomson\'s and Grant\'s gazelles, kori bustards and many elephants. Picnic lunch by the Tarangire River, then an afternoon game drive before heading to your lodge.',
                ...$stay('Karatu', 'Mid-range', 'Farm of Dreams Lodge', 'forest', 'Karatu lodge'),
            ],
            [
                'title' => 'Ngorongoro Crater',
                'image' => $p('crater'), 'caption' => 'Ngorongoro Crater',
                'text' => 'Leave with a picnic lunch and descend into the Ngorongoro Crater, one of the natural wonders of the world: an extinct volcano about 3 million years old, 20 km wide and the largest unbroken caldera on earth. All of the Big Five live here, with hundreds of bird species. Enjoy at least six hours of game drives before continuing to your lodge in Ndutu.',
                ...$stay('Ndutu', 'Mid-range', 'Ndutu Safari Lodge', 'tented', 'Ndutu lodge'),
            ],
            [
                'title' => 'Game drive in the Ndutu area',
                'image' => $p('plains'), 'caption' => 'Ndutu',
                'text' => 'Head out around 06:30 after a cup of coffee for a sunrise game drive, as the bush wakes up on the dew-covered grass. This is the best chance to see large herds of wildebeest grazing with their newborn calves, and predators on the hunt. Stay out for the sunset before returning to the lodge.',
                ...$stay('Ndutu', 'Mid-range', 'Ndutu Safari Lodge', 'campfire', 'Evening at the lodge'),
            ],
            [
                'title' => 'Ndutu game drive and transfer to Arusha',
                'image' => $p('seronera'), 'caption' => 'Southern Serengeti',
                'text' => 'A last game drive in Ndutu, near the southern Serengeti, looking for elephants, buffaloes, hippos, giraffes, hyenas, leopards, cheetahs and lions. After lunch, drive back over the Ngorongoro highlands to Arusha, where we drop you at your hotel or the airport.',
                'meals' => 'Breakfast & lunch',
            ],
        ],
        'included' => $safariIncluded,
        'excluded' => $safariExcluded,
        'optional' => $optional,
    ],

    // 3 ─────────────────────────────────────────────────────────────────────
    [
        'slug' => 'wildebeest-migration-ndutu-calving-6-days',
        'title' => 'Wildebeest Migration in Ndutu: Calving Season',
        'category' => 'Safari',
        'style' => 'mid-range',
        'types' => ['migration'],
        'tagline' => 'Follow the herds and their predators',
        'chips' => [['label' => '6 Days'], ['label' => 'January – April'], ['label' => 'Peak predator action', 'highlight' => true]],
        'text' => 'Six days following the migration on the short-grass plains of Ndutu and the southern Serengeti, with Tarangire, the Ngorongoro Crater and the Central Serengeti.',
        'price_tiers' => $tiers(3014, 2422, 2226),
        'image' => $p('plains'),
        'location' => 'Tarangire, Ngorongoro, Ndutu & Serengeti, Tanzania',
        'overview' => [
            'The southern Serengeti and Ndutu are the best places to see thousands of wildebeest grazing on rich short grass in January, February, March and April. Predator activity is at its peak, with excellent sightings of cheetahs, lions, hyenas and leopards.',
            'This tour can also be adapted to the season.',
            'Highlights: more than 5,000 wildebeest born every day; predators hunting; the Big Five and big cats; ancient baobab trees and large herds of elephants.',
        ],
        'facts' => ['Duration' => '6 Days · 5 Nights', 'Group size' => 'Private', 'Best time' => 'January – April'],
        'itinerary' => [
            [
                'title' => 'Arrival in Arusha',
                'image' => $p('arusha'), 'caption' => 'Arusha',
                'text' => 'Our representative picks you up at Kilimanjaro International Airport and takes you to your hotel in the centre of Arusha to relax and prepare for the adventure ahead.',
                ...$stay('Arusha', 'Mid-range', 'Arusha Planet Lodge & Spa', 'lodge', 'Arusha lodge', 'Bed & breakfast'),
            ],
            [
                'title' => 'Arusha to Tarangire National Park',
                'image' => $p('tarangire'), 'caption' => 'Tarangire',
                'text' => 'An early start for Tarangire National Park (120 km, about 2.5 hours), with its huge termite mounds, ancient baobabs and rich birdlife. The large elephant population is an amazing sight; look out too for lions, giraffes, warthogs, ostriches, impalas, cheetahs, mongooses, buffaloes and baboons.',
                ...$stay('Karatu', 'Mid-range', 'Farm of Dreams Lodge', 'forest', 'Karatu lodge'),
            ],
            [
                'title' => 'Karatu to the Ngorongoro Crater',
                'image' => $p('crater'), 'caption' => 'Ngorongoro Crater',
                'text' => 'Breakfast at 06:30, then descend into the crater, the largest unbroken volcanic caldera in the world, about 3 million years old and 20 km wide. All of the Big Five live here. Enjoy at least six hours of game drives before driving on to your lodge in Ndutu.',
                ...$stay('Ndutu', 'Mid-range', 'Ndutu Safari Lodge', 'tented', 'Ndutu lodge'),
            ],
            [
                'title' => 'Full game drive in the Ndutu area',
                'image' => $p('serengeti'), 'caption' => 'Ndutu',
                'text' => 'Leave at 06:00 for an early game drive, return for breakfast and a rest, then head out again with a packed lunch, following the migration wherever it is. Between December and April the plains become calving grounds and nurseries, and predators gather to hunt the young calves. The game drive ends around 17:00.',
                ...$stay('Ndutu', 'Mid-range', 'Ndutu Safari Lodge', 'campfire', 'Evening at the lodge'),
            ],
            [
                'title' => 'Ndutu to the Central Serengeti',
                'image' => $p('seronera'), 'caption' => 'Central Serengeti',
                'text' => 'Drive into the Serengeti, whose name means "endless plains", home to Africa\'s largest surviving concentration of plains animals. Look out for the kopjes, granite outcrops rising from the sea of grass that shelter plants and wildlife. Picnic lunch on the way, then dinner and the night at your tented camp.',
                ...$stay('Central Serengeti', 'Mid-range', 'Serengeti Heritage Tented Camp', 'tented', 'Tented camp'),
            ],
            [
                'title' => 'Serengeti to Arusha',
                'image' => $p('rim'), 'caption' => 'Ngorongoro highlands',
                'text' => 'An early game drive, when the animals are most active and the sunrise is at its best, then drive back to Arusha through the Ngorongoro area.',
                'meals' => 'Breakfast & lunch',
            ],
        ],
        'included' => $safariIncluded,
        'excluded' => $safariExcluded,
        'optional' => $optional,
    ],

    // 4 ─────────────────────────────────────────────────────────────────────
    [
        'slug' => 'zanzibar-beach-honeymoon-holiday-7-days',
        'title' => 'Zanzibar Beach Honeymoon Holiday',
        'category' => 'Zanzibar',
        'style' => 'mid-range',
        'types' => ['honeymoon', 'zanzibar'],
        'tagline' => 'Stone Town, spices, forest and beach',
        'chips' => [['label' => '7 Days'], ['label' => 'Romantic'], ['label' => 'Nungwi beach', 'highlight' => true]],
        'text' => 'A relaxed honeymoon on Zanzibar: Stone Town and Changuu Island, Jozani Forest and a village visit, a spice tour, and beach days near Nungwi.',
        'price_tiers' => $tiers(1617, 1360, 1341),
        'image' => $p('beach'),
        'location' => 'Zanzibar, Tanzania',
        'overview' => [
            'A journey you and your partner will treasure for a lifetime. This beach holiday blends relaxed sightseeing, forest walks and cultural visits: the busy markets of Stone Town, the peaceful Jozani Forest, an immersive spice tour in Kizimbani village and a choice of water sports.',
            'Highlights: Stone Town tour; Changuu (Prison) Island; Jozani Forest; spice tour.',
        ],
        'facts' => ['Duration' => '7 Days · 6 Nights', 'Group size' => 'Private', 'Best time' => 'June – October, December – February'],
        'itinerary' => [
            [
                'title' => 'Arrival in Zanzibar',
                'image' => $p('resort'), 'caption' => 'Zanzibar',
                'text' => 'Our representative meets you at Zanzibar Airport and transfers you to your beach hotel to relax.',
                ...$stay('North coast beach', 'Mid-range', 'Warere Beach Hotel', 'resort', 'Beach hotel', 'Bed & breakfast'),
            ],
            [
                'title' => 'Stone Town tour & boat trip to Changuu Island',
                'image' => $p('stonetown'), 'caption' => 'Stone Town',
                'text' => 'Explore the most scenic parts of Stone Town, a UNESCO World Heritage Site: the former slave market and the Cathedral Church of Christ built on its site in 1874, the Sultan\'s Palace, the House of Wonders and the Old Fort, along with busy markets, winding alleys and carved, studded doors. Lunch at a restaurant (own account), then a half-hour local boat ride to Changuu (Prison) Island, about 5 km offshore.',
                ...$stay('North coast beach', 'Mid-range', 'Warere Beach Hotel', 'resort', 'Beach hotel', 'Bed & breakfast'),
            ],
            [
                'title' => 'Jozani Forest & cultural visit',
                'image' => $p('forest'), 'caption' => 'Jozani Forest',
                'text' => 'Tour the south of the island, starting in Jozani Forest, one of the last refuges of the rare red colobus monkey, with great photo opportunities along the paths. Then visit Jambiani village to meet villagers at work: a traditional herbalist, an organic farm on coral stone and the seaweed farms run by women, a good chance to learn about Swahili culture. Back at the hotel in the evening.',
                ...$stay('North coast beach', 'Mid-range', 'Warere Beach Hotel', 'resort', 'Beach hotel', 'Bed & breakfast'),
            ],
            [
                'title' => 'Spice tour in Kizimbani',
                'image' => $p('forest'), 'caption' => 'Kizimbani spice farm',
                'text' => 'Visit Kizimbani village in the hills north of Stone Town, where many of the island\'s spices grow. See and taste tropical fruits in season, such as jackfruit, breadfruit, carambola, pineapple and lychee, and discover the henna bush, perfume tree and lipstick tree alongside cardamom, cinnamon, cloves, black pepper and nutmeg.',
                ...$stay('North coast beach', 'Mid-range', 'Warere Beach Hotel', 'resort', 'Beach hotel', 'Bed & breakfast'),
            ],
            [
                'title' => 'Beach day in Nungwi',
                'image' => $p('beach'), 'caption' => 'Nungwi',
                'text' => 'Relax at Warere Beach, with the pool and private beach area. Water sports, snorkelling, diving, boat cruises and bird watching can be arranged at extra cost.',
                ...$stay('North coast beach', 'Mid-range', 'Warere Beach Hotel', 'resort', 'Beach hotel', 'Bed & breakfast'),
            ],
            [
                'title' => 'Another beach day in Nungwi',
                'image' => $p('snorkel'), 'caption' => 'Nungwi',
                'text' => 'A second free day by the sea. Nungwi beach and its famous baobab tree are close by.',
                ...$stay('North coast beach', 'Mid-range', 'Warere Beach Hotel', 'resort', 'Beach hotel', 'Bed & breakfast'),
            ],
            [
                'title' => 'Departure from Zanzibar Airport',
                'image' => $p('resort'), 'caption' => 'Zanzibar',
                'text' => 'After breakfast we transfer you to the airport, the end of your honeymoon in Zanzibar.',
                'meals' => 'Breakfast',
            ],
        ],
        'included' => [...$tourIncluded, 'Complimentary mineral drinking water'],
        'excluded' => $tourExcluded,
    ],

    // 5 ─────────────────────────────────────────────────────────────────────
    [
        'slug' => 'royal-wildlife-safari-8-days',
        'title' => 'Royal Wildlife Safari in Tanzania',
        'category' => 'Safari',
        'style' => 'luxury',
        'types' => ['luxury'],
        'tagline' => 'Four parks in carefully chosen luxury lodges',
        'chips' => [['label' => '8 Days'], ['label' => 'Luxury lodges'], ['label' => 'Tree-climbing lions', 'highlight' => true]],
        'text' => 'Tarangire\'s elephants, the endless Serengeti, a full day in the Ngorongoro Crater and Lake Manyara\'s tree-climbing lions, staying in carefully selected luxury lodges.',
        'price_tiers' => $tiers(6204, 5487, 5250),
        'image' => $p('tarangire'),
        'location' => 'Tarangire, Serengeti, Ngorongoro & Lake Manyara, Tanzania',
        'overview' => [
            'Marvel at the wildlife and enjoy the luxury of our carefully selected lodges and camps. With expert guides and every detail planned, this safari offers both excitement and relaxation.',
            'The Serengeti and Ngorongoro offer some of the richest Big Five viewing on the planet, and in migration season vast herds of wildebeest stretch as far as the eye can see. Tarangire adds large elephant herds, and Lake Manyara its tree-climbing lions.',
            'Highlights: baobab trees more than 300 years old in Tarangire; large elephant herds; the Big Five and big cats; the endless plains of the Serengeti; tree-climbing lions in Lake Manyara; the density of wildlife in the Ngorongoro Crater.',
        ],
        'facts' => ['Duration' => '8 Days · 7 Nights', 'Group size' => 'Private', 'Best time' => 'All year'],
        'itinerary' => [
            [
                'title' => 'Arrival in Arusha',
                'image' => $p('arusha'), 'caption' => 'Arusha',
                'text' => 'Our representative welcomes you at Kilimanjaro International Airport and drives you to your hotel in Arusha for the night.',
                ...$stay('Arusha', 'Luxury', 'Gran Meliá Arusha', 'lodge', 'Arusha hotel', 'Bed & breakfast'),
            ],
            [
                'title' => 'Arusha to Tarangire National Park',
                'image' => $p('tarangire'), 'caption' => 'Tarangire',
                'text' => 'Your guide collects you around 08:00 for the 2.5-hour drive to Tarangire, famous for thousands of elephants as well as wildebeest, zebras, giraffes, buffaloes, hippos and baobabs believed to be over 1,000 years old. Lunch at the Tarangire River picnic site, then a game drive until late afternoon.',
                ...$stay('Lake Manyara', 'Luxury', 'Lake Manyara Kilimamoja Lodge', 'lodge', 'Lodge above Lake Manyara'),
            ],
            [
                'title' => 'Into the Serengeti National Park',
                'image' => $p('plains'), 'caption' => 'Serengeti Plains',
                'text' => 'Drive through the Ngorongoro Conservation Area, stopping for 10–20 minutes at the crater viewpoint to learn how it formed, then continue with a game drive into the Serengeti, famous for its grasslands and the Big Five. Picnic lunch in the park, and sunset before reaching your lodge.',
                ...$stay('Central Serengeti', 'Luxury', 'Meliá Serengeti Lodge', 'lodge', 'Serengeti lodge'),
            ],
            [
                'title' => 'Full game drive in the Serengeti',
                'image' => $p('seronera'), 'caption' => 'Serengeti',
                'text' => 'A full day visiting different parts of the park in search of the Big Five and elusive animals like the leopard. In the right season you will see the Great Migration. Lunch at a picnic spot in the park.',
                ...$stay('Central Serengeti', 'Luxury', 'Meliá Serengeti Lodge', 'lodge', 'Serengeti lodge'),
            ],
            [
                'title' => 'Serengeti to the Ngorongoro Conservation Area',
                'image' => $p('rim'), 'caption' => 'Ngorongoro Crater rim',
                'text' => 'Wake before sunrise for a game drive when the animals are most active, with a full bush breakfast around 10:00. Continue with a game drive to the Ngorongoro Conservation Area and your lodge on the crater rim.',
                'activity' => ['title' => 'Hot air balloon over the Serengeti (Optional)', 'image' => $p('balloon'), 'text' => 'Float over the plains at sunrise, followed by a champagne bush breakfast. USD 500 per person.'],
                ...$stay('Ngorongoro Crater rim', 'Luxury', 'Ngorongoro Lodge, Meliá Collection', 'rim', 'Crater rim lodge'),
            ],
            [
                'title' => 'Ngorongoro Crater',
                'image' => $p('crater'), 'caption' => 'Ngorongoro Crater',
                'text' => 'Descend for a full day on the crater floor, a world of its own with about 600 hyenas, critically endangered black rhinos, elephants with impressive tusks and more than 50 lions. Lunch at the hippo pool picnic site.',
                ...$stay('Lake Manyara', 'Luxury', 'Lake Manyara Kilimamoja Lodge', 'lodge', 'Lodge above Lake Manyara'),
            ],
            [
                'title' => 'Lake Manyara National Park and transfer to Arusha',
                'image' => $p('manyara'), 'caption' => 'Lake Manyara',
                'text' => 'A game drive in Lake Manyara National Park, home to thousands of pink flamingos, over 400 bird species, tree-climbing lions and elephant herds. Lunch on the lake shore, then drive to Arusha for dinner and the night.',
                ...$stay('Arusha', 'Luxury', 'Gran Meliá Arusha', 'lodge', 'Arusha hotel'),
            ],
            [
                'title' => 'Departure from Kilimanjaro Airport',
                'image' => $p('arusha'), 'caption' => 'Arusha',
                'text' => 'Enjoy the hotel until your transfer to the airport at your preferred time. A day room is included. With a late flight you can add a trip to the waterfalls or hot springs (not included).',
                'meals' => 'No meals',
            ],
        ],
        'included' => ['Park fees', 'All activities (unless labelled as optional)', ...$tourIncluded, 'Mineral drinking water (1 bottle per day on safari)', 'Day room in Arusha on the last day'],
        'excluded' => $tourExcluded,
        'optional' => [
            ['name' => 'Hot air balloon (Serengeti)', 'price' => 'USD 500 per person'],
            ['name' => 'Maasai boma visit', 'price' => 'USD 50 per vehicle'],
            ['name' => 'Sundowner', 'price' => 'USD 50 per person'],
            ['name' => 'Walking safari', 'price' => 'From USD 30 per person sharing'],
            ['name' => 'Olduvai Gorge (early human fossils museum)', 'price' => 'USD 36 per person'],
            ['name' => 'Materuni Waterfalls', 'price' => 'USD 150 per person sharing'],
        ],
    ],

    // 6 ─────────────────────────────────────────────────────────────────────
    [
        'slug' => 'luxury-wildlife-cultural-safari-8-days',
        'title' => 'Tanzania Luxury Wildlife & Cultural Safari',
        'category' => 'Safari',
        'style' => 'luxury',
        'types' => ['luxury'],
        'tagline' => 'Tarangire, the crater and the Serengeti in style',
        'chips' => [['label' => '8 Days'], ['label' => 'Fly out'], ['label' => 'Maasai village & Olduvai', 'highlight' => true]],
        'text' => 'Two nights each in Tarangire, on the Ngorongoro Crater rim and in the Serengeti, with a Maasai village visit and Olduvai Gorge, flying back to Arusha at the end.',
        'price_tiers' => $tiers(4329, 3746, 3552),
        'image' => $p('crater'),
        'location' => 'Tarangire, Ngorongoro & Serengeti, Tanzania',
        'overview' => [
            'An eight-day luxury safari through Tanzania\'s most iconic parks, with unhurried game viewing and excellent comfort throughout.',
            'Tarangire is known for its large elephant herds, ancient baobabs, lions, leopards and birdlife. The Ngorongoro Crater, a UNESCO World Heritage Site, is one of the best places to see the Big Five, including black rhinos and large lion prides. The Serengeti is famous for the Great Migration, with millions of wildebeest and zebras followed by predators.',
        ],
        'facts' => ['Duration' => '8 Days · 7 Nights', 'Group size' => 'Private', 'Best time' => 'All year'],
        'itinerary' => [
            [
                'title' => 'Arrival in Arusha',
                'image' => $p('arusha'), 'caption' => 'Arusha',
                'text' => 'Our team meets you at Kilimanjaro International Airport and transfers you to your luxury hotel in Arusha.',
                ...$stay('Arusha', 'Luxury', 'Gran Meliá Arusha', 'lodge', 'Arusha hotel', 'Bed & breakfast'),
            ],
            [
                'title' => 'Arusha to Tarangire National Park',
                'image' => $p('tarangire'), 'caption' => 'Tarangire',
                'text' => 'Drive to Tarangire for an afternoon game drive and a picnic lunch. Elephant herds share the park with zebras, buffaloes and oryx that come from Manyara in search of water, and lions prowl for prey. More bird species breed here than anywhere else on the planet, and the ancient baobabs make it an African paradise.',
                ...$stay('Tarangire', 'Luxury', 'Tarangire Kuro Treetops Lodge', 'lodge', 'Treetop lodge'),
            ],
            [
                'title' => 'Full day in Tarangire National Park',
                'image' => $p('tarangire'), 'caption' => 'Tarangire',
                'text' => 'A day of game drives across grassy savannah, swamps, hills and baobab country. In the dry season hundreds of animals gather here. Hot lunch at your lodge, with the option to relax there in the afternoon and enjoy the views.',
                ...$stay('Tarangire', 'Luxury', 'Tarangire Kuro Treetops Lodge', 'lodge', 'Treetop lodge'),
            ],
            [
                'title' => 'Tarangire to the Ngorongoro Crater rim',
                'image' => $p('rim'), 'caption' => 'Ngorongoro Crater rim',
                'text' => 'A morning game drive along the Tarangire River, the only water source in the dry season. On the way to Ngorongoro, visit a Maasai village to see their dances and songs and their beautiful beadwork. Arrive in the late afternoon at your lodge with views over the crater.',
                'activity' => ['title' => 'Maasai village visit', 'text' => 'The Maasai love to share their culture: celebratory dances and songs, daily life in the boma and their famous beadwork.'],
                ...$stay('Ngorongoro Crater rim', 'Luxury', 'Ngorongoro Lodge, Meliá Collection', 'rim', 'Crater rim lodge'),
            ],
            [
                'title' => 'Ngorongoro Crater tour',
                'image' => $p('crater'), 'caption' => 'Ngorongoro Crater',
                'text' => 'A full day in the crater: 20 km across and 600 m deep, the largest intact caldera in the world, with an estimated 30,000 large mammals around its central soda lake. It is the best place in Tanzania to see black rhinos and black-maned lions, with flamingos, leopards, cheetahs and hyenas. Picnic lunch by the hippo pool.',
                ...$stay('Ngorongoro Crater rim', 'Luxury', 'Ngorongoro Lodge, Meliá Collection', 'rim', 'Crater rim lodge'),
            ],
            [
                'title' => 'Ngorongoro to the Serengeti via Olduvai Gorge',
                'image' => $p('plains'), 'caption' => 'Serengeti Plains',
                'text' => 'Stop at Olduvai Gorge, East Africa\'s most famous archaeological site, where remains of our ancestors more than 1.75 million years old were found; the museum explains the discoveries. Afternoon game drive on arrival in the Serengeti.',
                ...$stay('Central Serengeti', 'Luxury', 'Meliá Serengeti Lodge', 'lodge', 'Serengeti lodge'),
            ],
            [
                'title' => 'Full day in the Serengeti',
                'image' => $p('seronera'), 'caption' => 'Serengeti',
                'text' => 'Morning and afternoon game drives with a picnic lunch. Look for the great herds of wildebeest and zebras on the move, and the Big Five, cheetahs, giraffes and hyenas across open savannah and woodland.',
                ...$stay('Central Serengeti', 'Luxury', 'Meliá Serengeti Lodge', 'lodge', 'Serengeti lodge'),
            ],
            [
                'title' => 'Fly from the Serengeti to Arusha and departure',
                'image' => $p('arusha'), 'caption' => 'Arusha',
                'text' => 'A morning flight to Arusha, a day room at your hotel to relax and order a meal of your choice, then the transfer to Kilimanjaro International Airport for your flight home.',
                'meals' => 'Breakfast',
            ],
        ],
        'included' => [
            'Full board accommodation while on safari',
            'All game drives in a 4x4 safari Land Cruiser with pop-up roof',
            'All park fees',
            'Professional English-speaking driver/guide',
            'Bottled water in the vehicle (1 litre per person per day)',
            'Current government taxes and levies',
            '24-hour support during the safari',
            'Flight from the Serengeti to Arusha',
            'Day room in Arusha on the last day',
        ],
        'excluded' => ['International flights', 'Tanzania entry visa', 'Travel and health insurance', 'Tips for your guide and hotel staff', 'Meals, drinks and excursions not shown in the itinerary', 'Items of a personal nature'],
    ],

    // 7 ─────────────────────────────────────────────────────────────────────
    [
        'slug' => 'ultimate-wildlife-luxury-experience-8-days',
        'title' => 'Ultimate Tanzania Wildlife & Luxury Experience',
        'category' => 'Safari',
        'style' => 'luxury',
        'types' => ['luxury'],
        'tagline' => 'Three nights in the Serengeti, high-end lodges throughout',
        'chips' => [['label' => '8 Days'], ['label' => 'Private game drives'], ['label' => 'Big Five', 'highlight' => true]],
        'text' => 'The Ngorongoro Crater, a Maasai boma and Olduvai Gorge, three nights in the Central Serengeti, then Karatu and Tarangire, all in high-end lodges.',
        'price_tiers' => $tiers(6719, 5931, 5669),
        'image' => $p('seronera'),
        'location' => 'Ngorongoro, Serengeti & Tarangire, Tanzania',
        'overview' => [
            'Experience some of the most beautiful national parks in Africa on private game drives, from big cats such as lions, leopards and cheetahs to their prey: wildebeest, hartebeest, oryx, buffaloes and many more. Every night is spent in high-end accommodation with top amenities and service.',
            'Highlights: the Big Five in the Ngorongoro Crater; large elephant herds and ancient baobabs in Tarangire; abundant wildlife on the endless plains of the Serengeti.',
        ],
        'facts' => ['Duration' => '8 Days · 7 Nights', 'Group size' => 'Private', 'Best time' => 'All year'],
        'itinerary' => [
            [
                'title' => 'Arrival in Arusha',
                'image' => $p('arusha'), 'caption' => 'Arusha',
                'text' => 'Our representatives meet you at Kilimanjaro International Airport and transfer you to your luxury hotel in Arusha.',
                ...$stay('Arusha', 'Luxury', 'Gran Meliá Arusha', 'lodge', 'Arusha hotel', 'Bed & breakfast'),
            ],
            [
                'title' => 'Arusha to the Ngorongoro Crater',
                'image' => $p('crater'), 'caption' => 'Ngorongoro Crater',
                'text' => 'Your private guide drives you to the Ngorongoro Conservation Area for a full day in the crater, one of Africa\'s Seven Natural Wonders. Zebras, buffaloes, lions, wildebeest, baboons, elephants, flamingos, ostriches, hippos and rare black rhinos live here, and the Big Five are easily seen. Picnic lunch near the hippo pool.',
                ...$stay('Ngorongoro Crater rim', 'Luxury', 'Ngorongoro Lodge, Meliá Collection', 'rim', 'Crater rim lodge'),
            ],
            [
                'title' => 'Maasai boma, Olduvai Gorge and on to the Serengeti',
                'image' => $p('plains'), 'caption' => 'Serengeti Plains',
                'text' => 'Visit a Maasai boma in the Ngorongoro Conservation Area to learn about their dances, ceremonies and way of life alongside the wildlife. Continue to Olduvai Gorge, where Mary and Louis Leakey made their famous fossil discoveries in 1959, then picnic lunch and the drive into the Serengeti.',
                ...$stay('Central Serengeti', 'Luxury', 'Meliá Serengeti Lodge', 'lodge', 'Serengeti lodge'),
            ],
            [
                'title' => 'Full-day game drive in the Serengeti',
                'image' => $p('seronera'), 'caption' => 'Seronera Valley',
                'text' => 'The Central Serengeti offers superb game viewing all year thanks to its resident animals, and the Seronera River Valley is a must-see, especially in the dry season (June – November). Look for impalas, buffaloes, hippos, warthogs, topi, hartebeest, giraffes, lions and leopards. Picnic lunch on the plains.',
                ...$stay('Central Serengeti', 'Luxury', 'Meliá Serengeti Lodge', 'lodge', 'Serengeti lodge'),
            ],
            [
                'title' => 'Another full day in the Central Serengeti',
                'image' => $p('serengeti'), 'caption' => 'Central Serengeti',
                'text' => 'The park covers almost 15,000 km² and about 500 bird species have been recorded, from ostriches and secretary birds to black eagles. Picnic lunch, then more exploring before returning to the lodge.',
                ...$stay('Central Serengeti', 'Luxury', 'Meliá Serengeti Lodge', 'lodge', 'Serengeti lodge'),
            ],
            [
                'title' => 'Serengeti game drive and transfer to Karatu',
                'image' => $p('rim'), 'caption' => 'Karatu',
                'text' => 'A final Serengeti game drive, then through the Ngorongoro highlands to your luxurious lodge in Karatu.',
                ...$stay('Karatu', 'Luxury', 'Ngorongoro Oldeani Mountain Lodge', 'forest', 'Karatu lodge'),
            ],
            [
                'title' => 'Game drive in Tarangire National Park',
                'image' => $p('tarangire'), 'caption' => 'Tarangire',
                'text' => 'Tarangire is famous for its elephants, sometimes more than 200 at a time, drawn to the river in the dry season. Picnic lunch among baobabs and acacia trees, then a game drive until evening.',
                ...$stay('Tarangire', 'Luxury', 'Tarangire Kuro Treetops Lodge', 'lodge', 'Treetop lodge'),
            ],
            [
                'title' => 'Tarangire to Arusha',
                'image' => $p('arusha'), 'caption' => 'Arusha',
                'text' => 'Drive to Arusha and relax in a day room at a luxury hotel before the transfer to Kilimanjaro International Airport.',
                'meals' => 'Breakfast',
            ],
        ],
        'included' => [...$safariIncluded, 'Day room in Arusha on the last day'],
        'excluded' => ['International and domestic flights', ...array_slice($safariExcluded, 1)],
    ],

    // 8 ─────────────────────────────────────────────────────────────────────
    [
        'slug' => 'great-migration-luxury-safari-9-days',
        'title' => 'The Best Great Migration Luxury Safari',
        'category' => 'Safari',
        'style' => 'luxury',
        'types' => ['migration', 'luxury'],
        'tagline' => 'The whole northern circuit, with two nights at the Mara River',
        'chips' => [['label' => '9 Days'], ['label' => 'Walking safari'], ['label' => 'Great Migration', 'highlight' => true]],
        'text' => 'Tarangire, Lake Manyara, the Central and Northern Serengeti for the Great Migration, a walking safari, a Maasai boma, Olduvai Gorge and the Ngorongoro Crater.',
        'price_tiers' => $tiers(7873, 7000, 6705),
        'image' => $p('plains'),
        'location' => 'Tarangire, Lake Manyara, Serengeti & Ngorongoro, Tanzania',
        'overview' => [
            'Tanzania is one of Africa\'s top safari destinations, and big cats are especially easy to see. This safari covers the highlights of the northern circuit and the Great Migration of millions of wildebeest across the Serengeti in search of green pasture.',
            'Expect monkeys in the forest, lions in trees, elephants among the acacias and a huge variety of animals inside the collapsed volcano of the Ngorongoro Crater, with nights in high-end accommodation throughout.',
            'Highlights: the Great Migration in the Serengeti; the Big Five in the Ngorongoro Crater; tree-climbing lions and hot springs in Lake Manyara; elephant herds and baobabs in Tarangire.',
        ],
        'facts' => ['Duration' => '9 Days · 8 Nights', 'Group size' => 'Private', 'Best time' => 'July – October (river crossings)'],
        'itinerary' => [
            [
                'title' => 'Arrival in Arusha',
                'image' => $p('arusha'), 'caption' => 'Arusha',
                'text' => 'Our team meets you at Kilimanjaro International Airport and transfers you to your luxury hotel in Arusha.',
                ...$stay('Arusha', 'Luxury', 'Gran Meliá Arusha', 'lodge', 'Arusha hotel', 'Bed & breakfast'),
            ],
            [
                'title' => 'Arusha to Tarangire National Park',
                'image' => $p('tarangire'), 'caption' => 'Tarangire',
                'text' => 'Your guide picks you up at 08:00 for Tarangire, famous for its elephant herds and the river that draws wildlife in the dry season. Picnic lunch, then a game drive among baobabs and acacias until evening.',
                ...$stay('Tarangire', 'Luxury', 'Tarangire Kuro Treetops Lodge', 'lodge', 'Treetop lodge'),
            ],
            [
                'title' => 'Tarangire to Lake Manyara National Park',
                'image' => $p('manyara'), 'caption' => 'Lake Manyara',
                'text' => 'Explore Lake Manyara in the Rift Valley, famous for tree-climbing lions and more than 450 bird species, including pink flamingos on the soda lake. Picnic lunch and game viewing until evening.',
                ...$stay('Lake Manyara', 'Luxury', 'Lake Manyara Serena Safari Lodge', 'lodge', 'Lodge above Lake Manyara'),
            ],
            [
                'title' => 'Lake Manyara to the Serengeti National Park',
                'image' => $p('plains'), 'caption' => 'Serengeti Plains',
                'text' => 'Leave around 08:00 and cross the Ngorongoro Conservation Area (you will explore the crater on the last day) into the Serengeti, with game viewing on the way and a picnic lunch. Overnight in the Central Serengeti.',
                ...$stay('Central Serengeti', 'Luxury', 'Meliá Serengeti Lodge', 'lodge', 'Serengeti lodge'),
            ],
            [
                'title' => 'Full game drive in the Northern Serengeti',
                'image' => $p('seronera'), 'caption' => 'Northern Serengeti',
                'text' => 'Head north through a World Heritage Site of nearly 15,000 km², with millions of wildebeest, thousands of lions and leopards, hundreds of cheetahs and more than 500 bird species. Picnic lunch, then on to your camp in Kogatende.',
                ...$stay('Northern Serengeti', 'Luxury', 'Oleserai Luxury Camp, Kogatende', 'tented', 'Luxury camp'),
            ],
            [
                'title' => 'The Mara River in the Northern Serengeti',
                'image' => $p('serengeti'), 'caption' => 'Mara River',
                'text' => 'A full day in the north, where in season the migration crosses the Mara River, braving strong currents, crocodiles and steep banks. Crossings are hard to predict: herds may wait for days. Kopjes, woodland, riverine forest and open plains make this a stunning region.',
                'note' => 'River crossings cannot be guaranteed and are sometimes a matter of luck.',
                ...$stay('Northern Serengeti', 'Luxury', 'Oleserai Luxury Camp, Kogatende', 'tented', 'Luxury camp'),
            ],
            [
                'title' => 'Walking safari in the Serengeti',
                'image' => $p('plains'), 'caption' => 'Central Serengeti',
                'text' => 'Walk in the heart of the Serengeti with an armed park ranger, treading the same ground as lions, elephants and giraffes, and looking at the small things: tracks, droppings, insects and medicinal plants. Then drive to your lodge in the Central Serengeti.',
                'activity' => ['title' => 'Walking safari', 'text' => 'Escorted by an armed ranger: a quiet way to experience the bush outside the vehicle.'],
                ...$stay('Central Serengeti', 'Luxury', 'Meliá Serengeti Lodge', 'lodge', 'Serengeti lodge'),
            ],
            [
                'title' => 'Serengeti to the Ngorongoro Conservation Area',
                'image' => $p('rim'), 'caption' => 'Ngorongoro Crater rim',
                'text' => 'Visit a Maasai boma to see traditional dances and learn about their hunting skills and way of life, then Olduvai Gorge, where the Leakeys discovered early human fossils. Picnic lunch and on to your lodge on the crater rim.',
                ...$stay('Ngorongoro Crater rim', 'Luxury', 'Ngorongoro Lodge, Meliá Collection', 'rim', 'Crater rim lodge'),
            ],
            [
                'title' => 'Ngorongoro Crater and transfer to Arusha',
                'image' => $p('crater'), 'caption' => 'Ngorongoro Crater',
                'text' => 'After an early breakfast, a full day in the crater with zebras, buffaloes, lions, elephants, flamingos, hippos and rare black rhinos; picnic lunch near the hippo pool. In the late afternoon, leave the crater and drive to Arusha for your departure from Kilimanjaro International Airport, or to a hotel if you are extending your stay.',
                'meals' => 'Breakfast & lunch',
            ],
        ],
        'included' => $safariIncluded,
        'excluded' => ['International and domestic flights', ...array_slice($safariExcluded, 1)],
    ],

    // 9 ─────────────────────────────────────────────────────────────────────
    [
        'slug' => 'luxury-family-safari-great-migration-10-days',
        'title' => 'Tanzania Luxury Family Safari: Great Migration',
        'category' => 'Safari',
        'style' => 'luxury',
        'types' => ['family', 'migration', 'luxury', 'honeymoon'],
        'tagline' => 'All the top parks, at an easy pace',
        'chips' => [['label' => '10 Days'], ['label' => 'Families & couples'], ['label' => 'Fly out from Kogatende', 'highlight' => true]],
        'text' => 'Tarangire, Lake Manyara, the Ngorongoro Crater and the Central and Northern Serengeti, in luxury lodges and camps, flying back from Kogatende at the end.',
        'price_tiers' => [['people' => 2, 'price' => 8193]],
        'child_price' => 5043,
        'image' => $p('tarangire'),
        'location' => 'Tarangire, Lake Manyara, Ngorongoro & Serengeti, Tanzania',
        'overview' => [
            'Ideal for families looking for a relaxing holiday, and for honeymooning couples who want to see all of Tanzania\'s top safari parks, stay close to nature and be spoiled by luxurious lodges.',
            'Highlights: large elephant herds and ancient baobabs; tree-climbing lions; the Big Five; endless plains; the Great Migration; Olduvai Gorge and its early human fossils.',
        ],
        'facts' => ['Duration' => '10 Days · 9 Nights', 'Group size' => 'Private', 'Child price' => 'Under 12 years', 'Best time' => 'July – October'],
        'itinerary' => [
            [
                'title' => 'Airport pick-up and Arusha Coffee Lodge',
                'image' => $p('arusha'), 'caption' => 'Arusha',
                'text' => 'We pick you up at Kilimanjaro International Airport and take you to Arusha Coffee Lodge at the foot of Mount Meru, hidden in one of Tanzania\'s largest coffee plantations. Stay in a plantation cottage with a bar, lounge, pool and garden terrace, and wake up to the aroma of fresh coffee.',
                ...$stay('Arusha', 'Luxury', 'Elewana Arusha Coffee Lodge', 'forest', 'Coffee plantation lodge', 'Bed & breakfast'),
            ],
            [
                'title' => 'Game drive in Tarangire National Park',
                'image' => $p('tarangire'), 'caption' => 'Tarangire',
                'text' => 'Leave around 07:00 for Tarangire (120 km), Tanzania\'s sixth-largest park, where pythons climb trees and lions and leopards roam. Bird lovers can look for yellow-collared lovebirds, rufous-tailed weavers and ashy starlings, all endemic to this dry savannah. Dinner and the night at your camp inside the park.',
                ...$stay('Tarangire', 'Luxury', 'Tarangire Kuro Treetops Lodge', 'lodge', 'Treetop lodge', 'All inclusive'),
            ],
            [
                'title' => 'Full game drive in Tarangire National Park',
                'image' => $p('tarangire'), 'caption' => 'Tarangire',
                'text' => 'A full day among elephant herds, lions and baobabs, the "tree of life". The Tarangire River flows all year and is the only water for wildlife in the dry season. Campfire dinner back at camp.',
                ...$stay('Tarangire', 'Luxury', 'Tarangire Kuro Treetops Lodge', 'campfire', 'Campfire dinner', 'All inclusive'),
            ],
            [
                'title' => 'Lake Manyara National Park',
                'image' => $p('manyara'), 'caption' => 'Lake Manyara',
                'text' => 'Ernest Hemingway called Lake Manyara "the loveliest I had seen in Africa". More than 400 bird species have been recorded, and the acacia woodland is the haunt of Manyara\'s legendary tree-climbing lions and big-tusked elephants.',
                ...$stay('Lake Manyara', 'Luxury', 'Lake Manyara Serena Safari Lodge', 'lodge', 'Lodge above Lake Manyara'),
            ],
            [
                'title' => 'Ngorongoro Conservation Area',
                'image' => $p('crater'), 'caption' => 'Ngorongoro Crater',
                'text' => 'A full day in the Ngorongoro Conservation Area, highlands along the Rift Valley with the famous crater at their heart. It protects both wildlife and the Maasai, so you may see herders with their cattle among the animals.',
                ...$stay('Ngorongoro Crater rim', 'Luxury', 'Ngorongoro Serena Safari Lodge', 'rim', 'Crater rim lodge'),
            ],
            [
                'title' => 'Central Serengeti via Olduvai Gorge',
                'image' => $p('plains'), 'caption' => 'Serengeti Plains',
                'text' => 'With a picnic lunch, drive via Olduvai Gorge into the Serengeti, Tanzania\'s oldest and most popular park, famous for its annual migration. See great herds of buffaloes, elephants, giraffes, elands, topi, impalas and Grant\'s gazelles.',
                ...$stay('Central Serengeti', 'Luxury', 'Oleserai Luxury Camp, Turner Spring', 'tented', 'Luxury camp', 'All inclusive'),
            ],
            [
                'title' => 'Central Serengeti',
                'image' => $p('seronera'), 'caption' => 'Central Serengeti',
                'text' => 'A day of game viewing on the vast savannah, with a picnic lunch and more exploring in the afternoon.',
                ...$stay('Central Serengeti', 'Luxury', 'Oleserai Luxury Camp, Turner Spring', 'tented', 'Luxury camp', 'All inclusive'),
            ],
            [
                'title' => 'Northern Serengeti (Kogatende)',
                'image' => $p('serengeti'), 'caption' => 'Northern Serengeti',
                'text' => 'Head north for a full day tracking the Great Migration, as long columns of wildebeest plunge through crocodile-filled rivers on their journey north.',
                ...$stay('Northern Serengeti', 'Luxury', 'Mara Mara Tented Lodge', 'tented', 'Tented lodge'),
            ],
            [
                'title' => 'Full day in the Northern Serengeti (Kogatende)',
                'image' => $p('plains'), 'caption' => 'Mara River',
                'text' => 'Another full day in the north, among millions of wildebeest, lions, leopards, cheetahs and more than 500 bird species. Picnic lunch on the plains.',
                ...$stay('Northern Serengeti', 'Luxury', 'Mara Mara Tented Lodge', 'tented', 'Tented lodge'),
            ],
            [
                'title' => 'Fly from Kogatende to Kilimanjaro Airport',
                'image' => $p('seronera'), 'caption' => 'Kogatende',
                'text' => 'A last morning game drive among the kopjes and riverine forest of the north, picnic lunch, then the afternoon flight from Kogatende airstrip (around 14:45, arriving about 17:30) to Kilimanjaro International Airport for your international connection.',
                'meals' => 'Breakfast & lunch',
            ],
        ],
        'included' => [...$safariIncluded, 'Flight from the Serengeti to Kilimanjaro International Airport'],
        'excluded' => $safariExcluded,
        'optional' => $optional,
    ],

    // 10 ────────────────────────────────────────────────────────────────────
    [
        'slug' => 'best-of-tanzania-and-zanzibar-11-days',
        'title' => 'Best of Tanzania & Zanzibar Safari',
        'category' => 'Safari & Beach',
        'style' => 'mid-range',
        'types' => ['zanzibar'],
        'tagline' => 'Six days on safari, then Zanzibar',
        'chips' => [['label' => '11 Days'], ['label' => 'Safari & beach'], ['label' => 'Flight to Zanzibar included', 'highlight' => true]],
        'text' => 'Tarangire, the Serengeti, the Ngorongoro Crater and Lake Manyara, then a flight to Zanzibar for Stone Town, the beach and boat trips.',
        'price_tiers' => $tiers(4584, 3880, 3675),
        'image' => $p('beach'),
        'location' => 'Northern Tanzania & Zanzibar',
        'overview' => [
            'Experience the best of northern Tanzania in six days, with Tarangire, the Serengeti, the Ngorongoro Crater and Lake Manyara, then fly to the tropical island of Zanzibar to relax, discover Stone Town and enjoy boat trips.',
            'Highlights: large elephant herds and ancient baobabs; lion prides; the Big Five and big cats; endless plains; birdlife; a beach holiday and historical sites.',
        ],
        'facts' => ['Duration' => '11 Days · 10 Nights', 'Group size' => 'Private', 'Best time' => 'June – October, December – February'],
        'itinerary' => [
            [
                'title' => 'Arusha to Tarangire National Park',
                'image' => $p('tarangire'), 'caption' => 'Tarangire',
                'text' => 'We pick you up at 08:00 from your hotel in or around Arusha for the 2.5-hour drive (120 km) past local communities to Tarangire, famous for its elephants, baobabs and tree-climbing pythons. Game drive, then dinner at your lodge.',
                ...$stay('Karatu', 'Mid-range', 'Marera Valley Lodge', 'forest', 'Valley lodge'),
            ],
            [
                'title' => 'Serengeti National Park',
                'image' => $p('plains'), 'caption' => 'Serengeti Plains',
                'text' => 'Drive to the Serengeti gate, picnic lunch, then an afternoon game drive across the endless plains, home to a migration of about 1.5 million animals.',
                ...$stay('Central Serengeti', 'Mid-range', 'Serengeti Heritage Tented Camp', 'tented', 'Tented camp'),
            ],
            [
                'title' => 'Game drive in the Serengeti',
                'image' => $p('seronera'), 'caption' => 'Serengeti',
                'text' => 'A full day of game drives. Besides the resident impalas, buffaloes, crocodiles and hippos, the Serengeti is famous for its migrating herds, which follow the rains: south in November and December, north again from April to June. Look for leopards, cheetahs and lions.',
                ...$stay('Central Serengeti', 'Mid-range', 'Serengeti Heritage Tented Camp', 'tented', 'Tented camp'),
            ],
            [
                'title' => 'Serengeti to the Ngorongoro Crater rim',
                'image' => $p('rim'), 'caption' => 'Ngorongoro Crater rim',
                'text' => 'A morning among kopjes, woodland, riverine forest and open plains, with more than 500 bird species, from augur buzzards and kori bustards to black-headed herons and white-backed vultures. Picnic lunch, then on to your lodge on the crater rim.',
                ...$stay('Ngorongoro Crater rim', 'Mid-range', 'Ngorongoro Rhino Lodge', 'rim', 'Crater rim lodge'),
            ],
            [
                'title' => 'Ngorongoro Crater',
                'image' => $p('crater'), 'caption' => 'Ngorongoro Crater',
                'text' => 'Drive into the largest intact crater in the world, home to around 30,000 large mammals including black rhinos, hyenas, zebras and elephants. In the late afternoon, drive to your lodge.',
                ...$stay('Karatu', 'Mid-range', 'Marera Valley Lodge', 'forest', 'Valley lodge'),
            ],
            [
                'title' => 'Lake Manyara and back to Arusha',
                'image' => $p('manyara'), 'caption' => 'Lake Manyara',
                'text' => 'A game drive in Lake Manyara, with panoramic views, waterfalls, clear streams and hot springs; a two-hour walking safari can be booked. Drive back to Arusha before dinner.',
                ...$stay('Arusha', 'Mid-range', 'Tulia Boutique Hotel & Spa', 'lodge', 'Arusha hotel', 'Breakfast & dinner'),
            ],
            [
                'title' => 'Fly from Arusha to Zanzibar: Stone Town',
                'image' => $p('stonetown'), 'caption' => 'Stone Town',
                'text' => 'Transfer to Arusha Airport for your flight to Zanzibar and spend the first night in Stone Town, with views over the old city. A Stone Town tour, spice tour, Prison Island or Jozani Forest can be arranged at the hotel.',
                ...$stay('Stone Town', 'Mid-range', 'Zanzibar Serena Hotel', 'stonetown', 'Stone Town hotel', 'Bed & breakfast'),
            ],
            [
                'title' => 'Free day in Stone Town',
                'image' => $p('stonetown'), 'caption' => 'Stone Town',
                'text' => 'A day at leisure. Zanzibar has been shaped by centuries of visitors and settlers, from Persians and Omani Arabs to Indians, Portuguese and British, and Stone Town shows that mix in its streets and buildings.',
                ...$stay('Stone Town', 'Mid-range', 'Zanzibar Serena Hotel', 'stonetown', 'Stone Town hotel', 'Bed & breakfast'),
            ],
            [
                'title' => 'Beach day from Stone Town',
                'image' => $p('beach'), 'caption' => 'Zanzibar',
                'text' => 'Relax by the sea. Water sports can be arranged at the hotel at extra cost.',
                ...$stay('Stone Town', 'Mid-range', 'Zanzibar Serena Hotel', 'stonetown', 'Stone Town hotel', 'Bed & breakfast'),
            ],
            [
                'title' => 'Last full day in Stone Town',
                'image' => $p('snorkel'), 'caption' => 'Zanzibar',
                'text' => 'Another free day for the beach, a boat trip or more exploring.',
                ...$stay('Stone Town', 'Mid-range', 'Zanzibar Serena Hotel', 'stonetown', 'Stone Town hotel', 'Bed & breakfast'),
            ],
            [
                'title' => 'Departure from Zanzibar Airport',
                'image' => $p('resort'), 'caption' => 'Zanzibar',
                'text' => 'Breakfast and a last swim, then an afternoon transfer from Stone Town to the airport for your international flight.',
                'meals' => 'Breakfast',
            ],
        ],
        'included' => ['Park fees (non-residents)', 'All activities (unless labelled as optional)', ...$tourIncluded, 'Drinks as specified in the itinerary', 'Flight from Arusha to Zanzibar'],
        'excluded' => $tourExcluded,
        'optional' => array_slice($optional, 0, 3),
    ],

    // 11 ────────────────────────────────────────────────────────────────────
    [
        'slug' => 'premium-luxury-safari-and-beach-12-days',
        'title' => 'Premium Luxury Safari & Beach Holiday',
        'category' => 'Safari & Beach',
        'style' => 'luxury',
        'types' => ['luxury', 'zanzibar', 'honeymoon'],
        'tagline' => 'Luxury safari, then all-inclusive Nungwi',
        'chips' => [['label' => '12 Days'], ['label' => 'Safari & beach'], ['label' => 'All-inclusive beach', 'highlight' => true]],
        'text' => 'Tarangire, Lake Manyara, the Serengeti and the Ngorongoro Crater in luxury lodges, then Stone Town and four all-inclusive nights in Nungwi.',
        'price_tiers' => $tiers(7912, 7252, 7067),
        'image' => $p('resort'),
        'location' => 'Northern Tanzania & Zanzibar',
        'overview' => [
            'Enjoy the best that Tanzania has to offer: Tarangire\'s elephant families, the Serengeti, Lake Manyara and its flamingos, and the Ngorongoro Crater, the best place in Tanzania to see the Big Five. Then fly to Zanzibar for a beach holiday.',
            'Highlights: large elephant herds and ancient baobabs; lion prides; the Big Five and big cats; endless plains; birdlife; a beach holiday and historical sites.',
        ],
        'facts' => ['Duration' => '12 Days · 11 Nights', 'Group size' => 'Private', 'Best time' => 'June – October, December – February'],
        'itinerary' => [
            [
                'title' => 'Arrival in Arusha',
                'image' => $p('arusha'), 'caption' => 'Arusha',
                'text' => 'Our representative picks you up at the airport and drives you to your hotel in Arusha, an oasis of calm, to relax before the adventure.',
                ...$stay('Arusha', 'Luxury', 'Gran Meliá Arusha', 'lodge', 'Arusha hotel', 'Bed & breakfast'),
            ],
            [
                'title' => 'Arusha to Tarangire National Park',
                'image' => $p('tarangire'), 'caption' => 'Tarangire',
                'text' => 'Drive across rolling Maasai plains, passing herders in colourful dress, to Tarangire, known for its large elephant herds. From the open roof, look for zebras, wildebeest, buffaloes and giraffes, and perhaps lions stalking prey or leopards resting in the trees. Continue to your luxury lodge.',
                ...$stay('Karatu', 'Luxury', 'Ngorongoro Oldeani Mountain Lodge', 'forest', 'Mountain lodge'),
            ],
            [
                'title' => 'Lake Manyara National Park',
                'image' => $p('manyara'), 'caption' => 'Lake Manyara',
                'text' => 'Lake Manyara is small but diverse: the soda lake covers much of the park and attracts thousands of flamingos and over 500 other bird species. See monkeys, giraffes, zebras, buffaloes, elephants and, with luck, lions lounging in the trees.',
                ...$stay('Karatu', 'Luxury', 'Ngorongoro Oldeani Mountain Lodge', 'forest', 'Mountain lodge'),
            ],
            [
                'title' => 'Through the Ngorongoro highlands to the Serengeti',
                'image' => $p('plains'), 'caption' => 'Serengeti Plains',
                'text' => 'Pass the farms of the Iraqw people and the misty rainforest of the Ngorongoro Conservation Area, with a view of the crater, and reach the Serengeti by midday for an extensive game drive.',
                ...$stay('Central Serengeti', 'Luxury', 'Oleserai Luxury Camp, Turner Spring', 'tented', 'Luxury camp', 'All inclusive'),
            ],
            [
                'title' => 'A full day in the Serengeti',
                'image' => $p('seronera'), 'caption' => 'Serengeti',
                'text' => 'A day of game drives among the resident wildlife and, depending on the season, the migrating herds of zebras, antelopes and wildebeest, followed by leopards, cheetahs and lions.',
                ...$stay('Central Serengeti', 'Luxury', 'Oleserai Luxury Camp, Turner Spring', 'tented', 'Luxury camp', 'All inclusive'),
            ],
            [
                'title' => 'Serengeti to the Ngorongoro Crater',
                'image' => $p('crater'), 'caption' => 'Ngorongoro Crater',
                'text' => 'Reach the crater rim before lunch and descend into this 600 m-deep, 260 km² UNESCO site with around 25,000 animals: wildebeest, zebras, gazelles, elephants and perhaps an endangered black rhino. After a full afternoon in the crater, drive to Arusha for the night.',
                ...$stay('Arusha', 'Luxury', 'Gran Meliá Arusha', 'lodge', 'Arusha hotel', 'Breakfast & dinner'),
            ],
            [
                'title' => 'Fly from Arusha to Zanzibar: Stone Town',
                'image' => $p('stonetown'), 'caption' => 'Stone Town',
                'text' => 'Fly from Arusha to Zanzibar and spend the night in Stone Town with views of the old city. A Stone Town tour, spice tour, Prison Island or Jozani Forest can be arranged at the hotel.',
                ...$stay('Stone Town', 'Luxury', 'Zanzibar Serena Hotel', 'stonetown', 'Stone Town hotel', 'Bed & breakfast'),
            ],
            [
                'title' => 'Stone Town to Nungwi',
                'image' => $p('beach'), 'caption' => 'Nungwi',
                'text' => 'Transfer about 50 km north to Nungwi, a lively former fishing village surrounded by some of Zanzibar\'s most beautiful beaches, and one of the few places where you can watch both sunrise and sunset over the ocean.',
                ...$stay('North coast beach', 'Luxury', 'Meliá Zanzibar', 'resort', 'Beach resort', 'All inclusive'),
            ],
            [
                'title' => 'Beach day in Nungwi',
                'image' => $p('beach'), 'caption' => 'Nungwi',
                'text' => 'White sand and turquoise water: a day to rest and enjoy the resort.',
                ...$stay('North coast beach', 'Luxury', 'Meliá Zanzibar', 'resort', 'Beach resort', 'All inclusive'),
            ],
            [
                'title' => 'Another beach day in Nungwi',
                'image' => $p('snorkel'), 'caption' => 'Nungwi',
                'text' => 'Swim, snorkel or simply relax.',
                ...$stay('North coast beach', 'Luxury', 'Meliá Zanzibar', 'resort', 'Beach resort', 'All inclusive'),
            ],
            [
                'title' => 'Last full day in Nungwi',
                'image' => $p('resort'), 'caption' => 'Nungwi',
                'text' => 'A final day of island time before heading home.',
                ...$stay('North coast beach', 'Luxury', 'Meliá Zanzibar', 'resort', 'Beach resort', 'All inclusive'),
            ],
            [
                'title' => 'Departure from Zanzibar Airport',
                'image' => $p('beach'), 'caption' => 'Zanzibar',
                'text' => 'Relax for your last hours, then the transfer to the airport for your flight.',
                'meals' => 'Breakfast',
            ],
        ],
        'included' => ['Park fees', 'Accommodation as per itinerary', 'A professional driver/guide', 'All transportation (unless labelled as optional)', 'All taxes/VAT', 'Round-trip airport transfers', 'Meals as specified in the day-by-day itinerary', 'Drinking water on all days', 'Flight from Arusha to Zanzibar'],
        'excluded' => $tourExcluded,
    ],

];
