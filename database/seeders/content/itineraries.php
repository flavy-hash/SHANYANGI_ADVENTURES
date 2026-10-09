<?php

/*
|--------------------------------------------------------------------------
| Starter itineraries for the package pages (/safaris/{slug})
|--------------------------------------------------------------------------
|
| Loaded by ContentSeeder, keyed by the package 'slug' in content/packages.php.
| After seeding, edit itineraries in the admin (/admin → Packages → Itinerary).
|
| facts:      shown in the price card (any label => value pairs).
| itinerary:  one entry per day:
|   title     "Day 1 • {title}"
|   image     photo for the day (+ optional 'caption')
|   text      what happens that day
|   note      optional small print
|   activity  optional highlighted activity: ['title', 'text', 'image' (optional)]
|   stay      where the night is spent (omit on the last day)
|   meals     meal plan for the day
|   options   optional accommodation choices: [['level' => 'Comfort', 'name' => '...'], ...]
| included / excluded: bullet lists.
|
| Images: full URLs, or files under storage/app/private/media/images.
|
*/

$img = fn (string $id) => "https://images.unsplash.com/photo-{$id}?auto=format&fit=crop&w=1200&q=75";

return [

    'serengeti-ngorongoro-safari' => [
        'location' => 'Serengeti & Ngorongoro, Tanzania',
        'overview' => [
            'A classic five-day journey through two of Africa\'s most famous wildlife areas. Spend two full days on the endless plains of the Serengeti, then descend into the Ngorongoro Crater, home to one of the densest concentrations of wildlife on the continent.',
            'Your private vehicle and guide give you the freedom to linger at sightings and travel at your own pace.',
        ],
        'facts' => [
            'Duration' => '5 Days · 4 Nights',
            'Group size' => 'Private',
            'Difficulty' => 'Easy',
            'Best time' => 'June – October',
        ],
        'itinerary' => [
            [
                'title' => 'Arusha to the Serengeti',
                'image' => $img('1516426122078-c23e76319801'), 'caption' => 'Serengeti Plains',
                'text' => 'Your guide collects you from your hotel in Arusha after breakfast. Drive through the green highlands of the Ngorongoro Conservation Area and down onto the Serengeti plains, with a picnic lunch on the way and your first game drive in the afternoon.',
                'stay' => 'Central Serengeti',
                'meals' => 'Full board',
                'options' => [
                    ['level' => 'Comfort', 'name' => 'Tented camp, Central Serengeti'],
                    ['level' => 'Premium', 'name' => 'Luxury lodge, Central Serengeti'],
                ],
                'stay_image' => $img('1504280390367-361c6d9f38f4'), 'stay_caption' => 'Tented camp',
            ],
            [
                'title' => 'Full Day in the Serengeti',
                'image' => $img('1535941339077-2dd1c7963098'), 'caption' => 'Seronera Valley',
                'text' => 'A full day exploring the Seronera area, known for its big cats. Early morning and late afternoon drives give you the best light and the most activity, with a relaxed break at camp or a picnic in the bush around midday.',
                'activity' => [
                    'title' => 'Hot Air Balloon Safari (Optional)',
                    'image' => $img('1507608616759-54f48f0af0ee'),
                    'text' => 'Float over the plains at sunrise and finish with a bush breakfast. Booked in advance and paid separately; ask us to reserve it for you.',
                ],
                'stay' => 'Central Serengeti',
                'meals' => 'Full board',
                'options' => [
                    ['level' => 'Comfort', 'name' => 'Tented camp, Central Serengeti'],
                    ['level' => 'Premium', 'name' => 'Luxury lodge, Central Serengeti'],
                ],
                'stay_image' => $img('1566073771259-6a8506099945'), 'stay_caption' => 'Safari lodge',
            ],
            [
                'title' => 'Serengeti to Ngorongoro',
                'image' => $img('1516026672322-bc52d61a55d5'), 'caption' => 'Serengeti',
                'text' => 'A final morning game drive in the Serengeti, then drive back towards the Ngorongoro highlands to your lodge on the crater rim, arriving in time for sunset over the caldera.',
                'stay' => 'Ngorongoro Crater rim',
                'meals' => 'Full board',
                'options' => [
                    ['level' => 'Comfort', 'name' => 'Highland lodge near the crater'],
                    ['level' => 'Premium', 'name' => 'Crater rim lodge with caldera views'],
                ],
                'stay_image' => $img('1500835556837-99ac94a94552'), 'stay_caption' => 'Crater rim',
            ],
            [
                'title' => 'Ngorongoro Crater',
                'image' => $img('1585970480901-90d6bb2a48b5'), 'caption' => 'Ngorongoro Crater',
                'text' => 'Descend early into the crater for a full game drive on the crater floor, looking for lions, elephants, buffalo and the rare black rhino. Enjoy a picnic lunch by the hippo pool before driving up to Karatu.',
                'note' => 'The crater has a daily time limit for vehicles; your guide will plan the day around it.',
                'stay' => 'Karatu',
                'meals' => 'Full board',
                'options' => [
                    ['level' => 'Comfort', 'name' => 'Garden lodge, Karatu'],
                    ['level' => 'Premium', 'name' => 'Coffee farm lodge, Karatu'],
                ],
                'stay_image' => $img('1447752875215-b2761acb3c5d'), 'stay_caption' => 'Karatu',
            ],
            [
                'title' => 'Return to Arusha',
                'image' => $img('1547471080-7cc2caa01a7e'), 'caption' => 'Northern Tanzania',
                'text' => 'After a relaxed breakfast, drive back to Arusha with an optional stop at Lake Manyara or the Mto wa Mbu market. Drop-off at your hotel or the airport in the afternoon.',
                'meals' => 'Breakfast & lunch',
            ],
        ],
        'included' => [
            'Private 4x4 safari vehicle with pop-up roof',
            'Professional English-speaking driver-guide',
            'Park entry and conservation fees',
            'Ngorongoro Crater service fee',
            'Accommodation as per itinerary',
            'All meals on safari and drinking water in the vehicle',
            'Pick-up and drop-off in Arusha',
        ],
        'excluded' => [
            'International flights and visa',
            'Travel insurance',
            'Optional hot air balloon safari',
            'Drinks at lodges and camps',
            'Tips for your guide and lodge staff',
        ],
    ],

    'machame-route' => [
        'location' => 'Mount Kilimanjaro, Tanzania',
        'overview' => [
            'Machame is one of the most popular routes to the summit of Kilimanjaro, Africa\'s highest mountain at 5,895 metres. It climbs through rainforest, heath and moorland into alpine desert, and its "climb high, sleep low" profile helps your body adjust to the altitude.',
            'Our mountain crew of guides, cooks and porters looks after you all the way from the gate to Uhuru Peak and back.',
        ],
        'facts' => [
            'Duration' => '7 Days · 6 Nights',
            'Group size' => 'Private',
            'Difficulty' => 'Challenging',
            'Summit' => '5,895 m',
            'Best time' => 'Jan – Mar, Jun – Oct',
        ],
        'itinerary' => [
            [
                'title' => 'Machame Gate to Machame Camp',
                'image' => $img('1447752875215-b2761acb3c5d'), 'caption' => 'Montane rainforest',
                'text' => 'Drive from Moshi to Machame Gate to register with the park. Your climb begins on a trail through lush rainforest, arriving at Machame Camp (about 3,000 m) in the late afternoon.',
                'stay' => 'Machame Camp (tents)',
                'meals' => 'Lunch & dinner',
            ],
            [
                'title' => 'Machame Camp to Shira Cave Camp',
                'image' => $img('1621414050946-1b936a78491f'), 'caption' => 'Heath and moorland',
                'text' => 'Leave the forest behind and climb a steeper ridge through heathland to the Shira Plateau. Camp near Shira Cave (about 3,750 m), with views of the summit on a clear evening.',
                'stay' => 'Shira Cave Camp (tents)',
                'meals' => 'Full board',
            ],
            [
                'title' => 'Shira to Lava Tower and Barranco',
                'image' => $img('1551632811-561732d1e306'), 'caption' => 'Lava Tower',
                'text' => 'An important acclimatisation day: climb to the Lava Tower (about 4,600 m) for lunch, then descend to Barranco Camp (about 3,950 m) to sleep lower than you climbed.',
                'stay' => 'Barranco Camp (tents)',
                'meals' => 'Full board',
            ],
            [
                'title' => 'Barranco Wall to Karanga Camp',
                'image' => $img('1489392191049-fc10c97e64b6'), 'caption' => 'Barranco Wall',
                'text' => 'Scramble up the famous Barranco Wall using your hands for balance, then follow a trail of ridges and valleys to Karanga Camp (about 3,995 m). A shorter day to rest and acclimatise.',
                'stay' => 'Karanga Camp (tents)',
                'meals' => 'Full board',
            ],
            [
                'title' => 'Karanga to Barafu Camp',
                'image' => $img('1621414050946-1b936a78491f'), 'caption' => 'Alpine desert',
                'text' => 'A short hike into the alpine desert to Barafu Camp (about 4,673 m), your base for the summit. Rest, eat early and prepare your gear for the night ahead.',
                'stay' => 'Barafu Camp (tents)',
                'meals' => 'Full board',
            ],
            [
                'title' => 'Summit Day: Uhuru Peak',
                'image' => $img('1609198092458-38a293c7ac4b'), 'caption' => 'Sunrise near the summit',
                'text' => 'Set off around midnight for the long, steady climb to Stella Point on the crater rim and on to Uhuru Peak (5,895 m) for sunrise over Africa. Then descend all the way to Mweka Camp (about 3,100 m).',
                'note' => 'Summit night is cold and demanding. Your guides set the pace and monitor everyone\'s health throughout.',
                'stay' => 'Mweka Camp (tents)',
                'meals' => 'Full board',
            ],
            [
                'title' => 'Mweka Camp to Mweka Gate',
                'image' => $img('1447752875215-b2761acb3c5d'), 'caption' => 'Mweka rainforest',
                'text' => 'A final walk down through the rainforest to Mweka Gate, where successful climbers receive their summit certificates. Transfer back to your hotel in Moshi or Arusha.',
                'meals' => 'Breakfast & lunch',
            ],
        ],
        'included' => [
            'Kilimanjaro National Park fees',
            'Professional mountain guides, cook and porters',
            'Quality mountain tents and camping equipment',
            'All meals and drinking water on the mountain',
            'Transfers to and from the park gate',
            'Summit certificate',
        ],
        'excluded' => [
            'International flights and visa',
            'Travel insurance covering high-altitude trekking',
            'Sleeping bag and personal climbing gear',
            'Hotel nights before and after the climb',
            'Tips for the mountain crew',
        ],
    ],

    'zanzibar-beach-escape' => [
        'location' => 'Zanzibar Archipelago, Tanzania',
        'overview' => [
            'A four-day island escape that combines the history of Stone Town with slow days on Zanzibar\'s white-sand beaches and a guided spice farm visit. The perfect way to unwind after a safari, or a short beach holiday on its own.',
        ],
        'facts' => [
            'Duration' => '4 Days · 3 Nights',
            'Group size' => 'Private',
            'Difficulty' => 'Easy',
            'Best time' => 'June – October',
        ],
        'itinerary' => [
            [
                'title' => 'Arrival in Stone Town',
                'image' => $img('1571896349842-33c89424de2d'), 'caption' => 'Stone Town',
                'text' => 'Arrive at Zanzibar Airport, where we meet you for the transfer to your hotel in Stone Town. Spend the afternoon exploring the old town\'s alleys, markets and seafront at your own pace.',
                'note' => 'Hotel check-in is usually from early afternoon.',
                'stay' => 'Stone Town',
                'meals' => 'Bed & breakfast',
                'options' => [
                    ['level' => 'Comfort', 'name' => 'Character hotel, Stone Town'],
                    ['level' => 'Premium', 'name' => 'Boutique heritage hotel, Stone Town'],
                ],
                'stay_image' => $img('1571896349842-33c89424de2d'), 'stay_caption' => 'Stone Town hotel',
            ],
            [
                'title' => 'Stone Town & Spice Tour',
                'image' => $img('1488646953014-85cb44e25828'), 'caption' => 'Stone Town',
                'text' => 'Join a guided walk through Stone Town in the morning, then head inland to a working spice farm before continuing to your beach hotel on the north coast.',
                'activity' => [
                    'title' => 'Spice Farm Tour',
                    'text' => 'See, smell and taste cloves, cinnamon, vanilla and pepper straight from the plant, with a local guide sharing the island\'s spice-trading history.',
                ],
                'stay' => 'North coast beach',
                'meals' => 'Bed & breakfast',
                'options' => [
                    ['level' => 'Comfort', 'name' => 'Beachfront guesthouse'],
                    ['level' => 'Premium', 'name' => 'Beach resort with pool'],
                ],
                'stay_image' => $img('1520250497591-112f2f40a3f4'), 'stay_caption' => 'Beach resort',
            ],
            [
                'title' => 'Beach Day',
                'image' => $img('1507525428034-b723cf961d3e'), 'caption' => 'North coast',
                'text' => 'A free day to swim, read and relax on the beach, or join the optional excursion below.',
                'activity' => [
                    'title' => 'Snorkelling Trip (Optional)',
                    'image' => $img('1544551763-46a013bb70d5'),
                    'text' => 'Head out by boat to the reefs off the coast to snorkel among tropical fish. Booked locally and paid separately.',
                ],
                'stay' => 'North coast beach',
                'meals' => 'Bed & breakfast',
                'options' => [
                    ['level' => 'Comfort', 'name' => 'Beachfront guesthouse'],
                    ['level' => 'Premium', 'name' => 'Beach resort with pool'],
                ],
                'stay_image' => $img('1520250497591-112f2f40a3f4'), 'stay_caption' => 'Beach resort',
            ],
            [
                'title' => 'Beach & Departure',
                'image' => $img('1507525428034-b723cf961d3e'), 'caption' => 'Indian Ocean',
                'text' => 'Enjoy a last morning by the ocean before your transfer back to the airport for your onward flight.',
                'meals' => 'Breakfast',
            ],
        ],
        'included' => [
            'Airport transfers in Zanzibar',
            'Accommodation as per itinerary',
            'Guided Stone Town walking tour',
            'Spice farm tour with tasting',
            'Daily breakfast',
        ],
        'excluded' => [
            'Flights to and from Zanzibar',
            'Visa and Zanzibar travel insurance',
            'Lunches and dinners',
            'Optional snorkelling trip',
            'Tips',
        ],
    ],

    'safari-and-sea-honeymoon' => [
        'location' => 'Northern Tanzania & Zanzibar',
        'overview' => [
            'Celebrate together with private game drives, intimate camps and starlit dinners in the Serengeti and Ngorongoro, then fly to Zanzibar for slow days by the Indian Ocean.',
            'We take care of the special touches, so you can simply enjoy each other and the journey.',
        ],
        'facts' => [
            'Duration' => '9 Days · 8 Nights',
            'Group size' => 'Private (2 guests)',
            'Difficulty' => 'Easy',
            'Best time' => 'June – October',
        ],
        'itinerary' => [
            ['title' => 'Arrival in Arusha', 'image' => $img('1547471080-7cc2caa01a7e'), 'caption' => 'Arusha', 'text' => 'We meet you at Kilimanjaro Airport and transfer you to a peaceful garden lodge in Arusha to rest after your flight.', 'stay' => 'Arusha', 'meals' => 'Dinner'],
            ['title' => 'Tarangire National Park', 'image' => $img('1585970480901-90d6bb2a48b5'), 'caption' => 'Tarangire', 'text' => 'Drive to Tarangire, famous for its large elephant herds and ancient baobab trees, for an afternoon game drive.', 'stay' => 'Tarangire area', 'meals' => 'Full board'],
            ['title' => 'Into the Serengeti', 'image' => $img('1516426122078-c23e76319801'), 'caption' => 'Serengeti', 'text' => 'Cross the Ngorongoro highlands and descend onto the Serengeti plains, game viewing on the way to your intimate tented camp.', 'stay' => 'Central Serengeti', 'meals' => 'Full board'],
            [
                'title' => 'Serengeti for Two', 'image' => $img('1535941339077-2dd1c7963098'), 'caption' => 'Serengeti',
                'text' => 'A full day of private game drives at your own pace, ending with sundowners on the plains.',
                'activity' => ['title' => 'Private Bush Dinner', 'image' => $img('1478131143081-80f7f84ca84d'), 'text' => 'A candlelit dinner for two under the stars, arranged with your camp.'],
                'stay' => 'Central Serengeti', 'meals' => 'Full board',
            ],
            ['title' => 'Serengeti to Ngorongoro', 'image' => $img('1500835556837-99ac94a94552'), 'caption' => 'Crater rim', 'text' => 'A last morning in the Serengeti before driving to your lodge on the rim of the Ngorongoro Crater.', 'stay' => 'Ngorongoro Crater rim', 'meals' => 'Full board'],
            ['title' => 'Crater & Fly to Zanzibar', 'image' => $img('1523805009345-7448845a9e53'), 'caption' => 'Ngorongoro', 'text' => 'Explore the crater floor in the morning, then transfer to the airstrip for your flight to Zanzibar and on to your beach hotel.', 'stay' => 'Zanzibar beach', 'meals' => 'Breakfast, lunch & dinner'],
            ['title' => 'Zanzibar Beach Day', 'image' => $img('1507525428034-b723cf961d3e'), 'caption' => 'Zanzibar', 'text' => 'A slow day of swimming, spa time and fresh seafood by the ocean.', 'stay' => 'Zanzibar beach', 'meals' => 'Half board'],
            [
                'title' => 'Stone Town & Sunset Dhow', 'image' => $img('1571896349842-33c89424de2d'), 'caption' => 'Stone Town',
                'text' => 'Explore Stone Town\'s alleys and markets with a local guide.',
                'activity' => ['title' => 'Sunset Dhow Cruise', 'text' => 'Sail along the coast on a traditional wooden dhow as the sun sets over the Indian Ocean.'],
                'stay' => 'Zanzibar beach', 'meals' => 'Half board',
            ],
            ['title' => 'Departure', 'image' => $img('1507525428034-b723cf961d3e'), 'caption' => 'Zanzibar', 'text' => 'A final breakfast by the sea before your transfer to Zanzibar Airport.', 'meals' => 'Breakfast'],
        ],
        'included' => [
            'Private safari vehicle and guide',
            'Park and conservation fees',
            'Accommodation as per itinerary',
            'Flight from the safari area to Zanzibar',
            'Private bush dinner and sunset dhow cruise',
            'Airport transfers',
        ],
        'excluded' => [
            'International flights and visa',
            'Travel insurance',
            'Drinks and spa treatments',
            'Tips',
        ],
    ],

    'family-safari-adventure' => [
        'location' => 'Tarangire, Lake Manyara & Ngorongoro',
        'overview' => [
            'A relaxed six-day safari designed for families, with shorter drives, lodges with space to play and guides who love sharing the bush with younger travellers.',
        ],
        'facts' => [
            'Duration' => '6 Days · 5 Nights',
            'Group size' => 'Private (your family)',
            'Difficulty' => 'Easy',
            'Best for' => 'All ages',
            'Best time' => 'June – October, Dec – Feb',
        ],
        'itinerary' => [
            ['title' => 'Arrival in Arusha', 'image' => $img('1547471080-7cc2caa01a7e'), 'caption' => 'Arusha', 'text' => 'Airport pick-up and transfer to a family-friendly lodge with gardens and a pool.', 'stay' => 'Arusha', 'meals' => 'Dinner'],
            ['title' => 'Tarangire Elephants', 'image' => $img('1585970480901-90d6bb2a48b5'), 'caption' => 'Tarangire', 'text' => 'A short drive to Tarangire to watch elephant families among the baobabs, with a picnic lunch in the park.', 'stay' => 'Tarangire area', 'meals' => 'Full board'],
            [
                'title' => 'Lake Manyara & Mto wa Mbu', 'image' => $img('1549366021-9f761d450615'), 'caption' => 'Lake Manyara',
                'text' => 'Game drive in Lake Manyara National Park, then visit the lively village of Mto wa Mbu.',
                'activity' => ['title' => 'Village Walk', 'image' => $img('1447752875215-b2761acb3c5d'), 'text' => 'Meet local farmers and artisans and see how bananas, rice and crafts are produced.'],
                'stay' => 'Karatu', 'meals' => 'Full board',
            ],
            ['title' => 'Ngorongoro Crater', 'image' => $img('1523805009345-7448845a9e53'), 'caption' => 'Ngorongoro', 'text' => 'A full morning on the crater floor spotting lions, zebras, hippos and more, back at the lodge in time for a swim.', 'stay' => 'Karatu', 'meals' => 'Full board'],
            ['title' => 'Farm Day in Karatu', 'image' => $img('1447752875215-b2761acb3c5d'), 'caption' => 'Karatu', 'text' => 'A slower day to explore the farm, walk the trails and enjoy family time at the lodge.', 'stay' => 'Karatu', 'meals' => 'Full board'],
            ['title' => 'Return to Arusha', 'image' => $img('1516426122078-c23e76319801'), 'caption' => 'Northern Tanzania', 'text' => 'Drive back to Arusha for your onward journey.', 'meals' => 'Breakfast & lunch'],
        ],
        'included' => [
            'Private safari vehicle and guide',
            'Park and conservation fees',
            'Family-friendly accommodation',
            'All meals on safari',
            'Village walk in Mto wa Mbu',
            'Airport transfers',
        ],
        'excluded' => [
            'International flights and visas',
            'Travel insurance',
            'Drinks',
            'Tips',
        ],
    ],

    'fly-in-serengeti-luxury' => [
        'location' => 'Serengeti & Ngorongoro, Tanzania',
        'overview' => [
            'Skip the long drives: light aircraft carry you between the parks, leaving more time for game drives and for enjoying some of Tanzania\'s finest lodges.',
        ],
        'facts' => [
            'Duration' => '5 Days · 4 Nights',
            'Group size' => 'Private',
            'Difficulty' => 'Easy',
            'Best time' => 'June – October',
        ],
        'itinerary' => [
            ['title' => 'Fly to the Serengeti', 'image' => $img('1516026672322-bc52d61a55d5'), 'caption' => 'Serengeti', 'text' => 'Fly from Arusha to a Serengeti airstrip, where your guide meets you for a game drive on the way to your lodge.', 'note' => 'Light-aircraft flights usually have a strict luggage limit, so pack in soft bags.', 'stay' => 'Serengeti', 'meals' => 'Full board'],
            [
                'title' => 'Serengeti in Style', 'image' => $img('1535941339077-2dd1c7963098'), 'caption' => 'Serengeti',
                'text' => 'Private game drives timed to the best light, with long lunches back at the lodge.',
                'activity' => ['title' => 'Hot Air Balloon Safari', 'image' => $img('1507608616759-54f48f0af0ee'), 'text' => 'A sunrise flight over the plains, followed by a champagne bush breakfast.'],
                'stay' => 'Serengeti', 'meals' => 'Full board',
            ],
            ['title' => 'Another Corner of the Serengeti', 'image' => $img('1516426122078-c23e76319801'), 'caption' => 'Serengeti', 'text' => 'Explore a different area of the park depending on the season and where the wildlife is.', 'stay' => 'Serengeti', 'meals' => 'Full board'],
            ['title' => 'Fly to Ngorongoro', 'image' => $img('1500835556837-99ac94a94552'), 'caption' => 'Crater rim', 'text' => 'A short flight to the Manyara airstrip and transfer up to your lodge on the crater rim.', 'stay' => 'Ngorongoro Crater rim', 'meals' => 'Full board'],
            ['title' => 'Crater & Fly Home', 'image' => $img('1585970480901-90d6bb2a48b5'), 'caption' => 'Ngorongoro', 'text' => 'Descend into the crater for a morning game drive, then fly back to Arusha.', 'meals' => 'Breakfast & lunch'],
        ],
        'included' => [
            'Scheduled light-aircraft flights as per itinerary',
            'Private game drives and guide',
            'Park and conservation fees',
            'Luxury accommodation, full board',
            'Hot air balloon safari',
        ],
        'excluded' => [
            'International flights and visa',
            'Travel insurance',
            'Premium drinks',
            'Tips',
        ],
    ],

    'great-migration-river-crossings' => [
        'location' => 'Northern Serengeti, Tanzania',
        'overview' => [
            'Base yourself close to the Mara River in the northern Serengeti, where the herds gather to cross during the middle of the year. Patience is key: your guide will position you for the best chance of seeing a crossing.',
            'The migration follows the rains, so timing can never be guaranteed, but this region offers excellent wildlife throughout the season.',
        ],
        'facts' => [
            'Duration' => '7 Days · 6 Nights',
            'Group size' => 'Private',
            'Difficulty' => 'Easy',
            'Season' => 'July – October',
        ],
        'itinerary' => [
            ['title' => 'Arrival in Arusha', 'image' => $img('1547471080-7cc2caa01a7e'), 'caption' => 'Arusha', 'text' => 'Airport pick-up and a relaxed evening at your lodge.', 'stay' => 'Arusha', 'meals' => 'Dinner'],
            ['title' => 'Fly to the Northern Serengeti', 'image' => $img('1516026672322-bc52d61a55d5'), 'caption' => 'Northern Serengeti', 'text' => 'A light-aircraft flight north, then a game drive to your camp near the Mara River.', 'stay' => 'Northern Serengeti', 'meals' => 'Full board'],
            ['title' => 'The Mara River', 'image' => $img('1585970480901-90d6bb2a48b5'), 'caption' => 'Mara River', 'text' => 'Spend the day along the river, watching the herds gather at the crossing points.', 'stay' => 'Northern Serengeti', 'meals' => 'Full board'],
            ['title' => 'Following the Herds', 'image' => $img('1535941339077-2dd1c7963098'), 'caption' => 'Northern Serengeti', 'text' => 'Another full day tracking the migration and the predators that follow it.', 'stay' => 'Northern Serengeti', 'meals' => 'Full board'],
            ['title' => 'Into the Central Serengeti', 'image' => $img('1516426122078-c23e76319801'), 'caption' => 'Serengeti', 'text' => 'Drive south through the heart of the park, game viewing along the way.', 'stay' => 'Central Serengeti', 'meals' => 'Full board'],
            ['title' => 'Seronera Big Cats', 'image' => $img('1523805009345-7448845a9e53'), 'caption' => 'Seronera', 'text' => 'A full day in the Seronera valley, well known for lions, leopards and cheetahs.', 'stay' => 'Central Serengeti', 'meals' => 'Full board'],
            ['title' => 'Fly Back to Arusha', 'image' => $img('1547471080-7cc2caa01a7e'), 'caption' => 'Serengeti', 'text' => 'A final morning drive before your flight back to Arusha.', 'meals' => 'Breakfast & lunch'],
        ],
        'included' => [
            'Flights between Arusha and the Serengeti',
            'Private game drives and guide',
            'Park and conservation fees',
            'Accommodation as per itinerary, full board',
            'Airport transfers',
        ],
        'excluded' => [
            'International flights and visa',
            'Travel insurance',
            'Drinks',
            'Tips',
        ],
    ],

    'lemosho-route' => [
        'location' => 'Mount Kilimanjaro, Tanzania',
        'overview' => [
            'Lemosho approaches Kilimanjaro from the west, crossing the wide Shira Plateau before joining the southern circuit to the summit. It is quieter in its early stages, and the extra days give your body more time to adjust to the altitude.',
        ],
        'facts' => [
            'Duration' => '8 Days · 7 Nights',
            'Group size' => 'Private',
            'Difficulty' => 'Challenging',
            'Summit' => '5,895 m',
            'Best time' => 'Jan – Mar, Jun – Oct',
        ],
        'itinerary' => [
            ['title' => 'Lemosho Gate to Mti Mkubwa Camp', 'image' => $img('1447752875215-b2761acb3c5d'), 'caption' => 'Rainforest', 'text' => 'Drive to Londorossi Gate to register, then on to the Lemosho trailhead for a gentle forest walk to Mti Mkubwa ("Big Tree") Camp.', 'stay' => 'Mti Mkubwa Camp (tents)', 'meals' => 'Lunch & dinner'],
            ['title' => 'To Shira 1 Camp', 'image' => $img('1621414050946-1b936a78491f'), 'caption' => 'Shira Ridge', 'text' => 'Leave the forest and climb onto the Shira Ridge, with wide views over the plateau.', 'stay' => 'Shira 1 Camp (tents)', 'meals' => 'Full board'],
            ['title' => 'Across the Shira Plateau', 'image' => $img('1489392191049-fc10c97e64b6'), 'caption' => 'Shira Plateau', 'text' => 'An easier day crossing the plateau to Shira 2 Camp, helping your body acclimatise.', 'stay' => 'Shira 2 Camp (tents)', 'meals' => 'Full board'],
            ['title' => 'Lava Tower to Barranco', 'image' => $img('1551632811-561732d1e306'), 'caption' => 'Lava Tower', 'text' => 'Climb high to the Lava Tower (about 4,600 m), then descend to sleep at Barranco Camp.', 'stay' => 'Barranco Camp (tents)', 'meals' => 'Full board'],
            ['title' => 'Barranco Wall to Karanga', 'image' => $img('1621414050946-1b936a78491f'), 'caption' => 'Barranco Wall', 'text' => 'Scramble up the Barranco Wall and continue to Karanga Camp.', 'stay' => 'Karanga Camp (tents)', 'meals' => 'Full board'],
            ['title' => 'Karanga to Barafu Camp', 'image' => $img('1489392191049-fc10c97e64b6'), 'caption' => 'Alpine desert', 'text' => 'A short climb to Barafu Camp, your base for summit night.', 'stay' => 'Barafu Camp (tents)', 'meals' => 'Full board'],
            ['title' => 'Summit Day: Uhuru Peak', 'image' => $img('1609198092458-38a293c7ac4b'), 'caption' => 'Sunrise near the summit', 'text' => 'Climb through the night to Uhuru Peak (5,895 m) for sunrise, then descend to Mweka Camp.', 'note' => 'Summit night is cold and demanding. Your guides set the pace and monitor everyone\'s health throughout.', 'stay' => 'Mweka Camp (tents)', 'meals' => 'Full board'],
            ['title' => 'Down to Mweka Gate', 'image' => $img('1447752875215-b2761acb3c5d'), 'caption' => 'Mweka rainforest', 'text' => 'Descend through the rainforest to Mweka Gate to collect your certificate, then transfer to your hotel.', 'meals' => 'Breakfast & lunch'],
        ],
        'included' => [
            'Kilimanjaro National Park fees',
            'Professional mountain guides, cook and porters',
            'Quality mountain tents and camping equipment',
            'All meals and drinking water on the mountain',
            'Transfers to and from the park gates',
            'Summit certificate',
        ],
        'excluded' => [
            'International flights and visa',
            'Travel insurance covering high-altitude trekking',
            'Sleeping bag and personal climbing gear',
            'Hotel nights before and after the climb',
            'Tips for the mountain crew',
        ],
    ],

];
