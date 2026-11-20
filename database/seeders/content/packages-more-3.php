<?php

/*
|--------------------------------------------------------------------------
| More starter packages: Kilimanjaro routes and Mount Meru
|--------------------------------------------------------------------------
|
| Loaded by MorePackagesSeeder: a package is only added when its slug doesn't
| exist yet. Prices are left empty ("On request"); set them in the admin.
|
*/

$img = fn (string $id) => "https://images.unsplash.com/photo-{$id}?auto=format&fit=crop&w=1200&q=75";

$climbIncluded = [
    'Professional mountain guides, cooks and porters',
    'Kilimanjaro park, camping and rescue fees',
    'Quality mountain tents and sleeping mats',
    'All meals and treated drinking water on the mountain',
    'Pulse oximeter checks twice a day',
    'Transfers between your hotel and the gate',
    'Summit certificate',
];

$climbExcluded = [
    'International flights and visa',
    'Travel insurance covering high-altitude trekking',
    'Hotel nights before and after the climb',
    'Personal trekking gear and sleeping bag (rental available)',
    'Tips for the mountain crew',
];

$summitNote = 'Your guide checks your health throughout and may turn you back if it is not safe to continue.';

return [

    // 1 ─────────────────────────────────────────────────────────────────────
    [
        'slug' => 'rongai-route-7-days',
        'title' => 'Rongai Route',
        'category' => 'Kilimanjaro',
        'style' => 'mid-range',
        'types' => ['kilimanjaro'],
        'tagline' => 'The quiet northern approach',
        'chips' => [['label' => '7 Days'], ['label' => 'Challenging'], ['label' => 'Quieter route', 'highlight' => true]],
        'text' => 'Climb Kilimanjaro from the dry northern side near the Kenyan border: fewer people, gentle gradients and a night beside Mawenzi Tarn, coming down the Marangu side.',
        'image' => $img('1489392191049-fc10c97e64b6'),
        'location' => 'Mount Kilimanjaro, Tanzania',
        'overview' => [
            'The Rongai route starts at Nalemoru on the north-east of the mountain, which gets less rain than the southern slopes. It is a good choice in the wetter months and for climbers who prefer peaceful trails.',
            'The seven-day version adds an acclimatisation day at Mawenzi Tarn, below the jagged peaks of Mawenzi. You descend on the Marangu route, so you see both sides of the mountain.',
        ],
        'facts' => ['Duration' => '7 Days · 6 Nights', 'Group size' => 'Private or small group', 'Difficulty' => 'Challenging', 'Summit' => '5,895 m', 'Best time' => 'All year; best June – October'],
        'itinerary' => [
            [
                'title' => 'Rongai Gate to Simba Camp',
                'image' => $img('1447752875215-b2761acb3c5d'), 'caption' => 'Pine forest',
                'text' => 'Drive around the mountain to Nalemoru, register at Rongai Gate and walk through pine plantations and forest to Simba Camp (about 2,650 m).',
                'stay' => 'Simba Camp', 'meals' => 'Lunch & dinner',
            ],
            [
                'title' => 'Simba Camp to Kikelewa Caves',
                'image' => $img('1489392191049-fc10c97e64b6'), 'caption' => 'Moorland',
                'text' => 'Climb steadily through heath and moorland with views back over the plains of Kenya, past Second Cave to Kikelewa Caves (about 3,600 m).',
                'stay' => 'Kikelewa Caves', 'meals' => 'Full board',
            ],
            [
                'title' => 'Kikelewa Caves to Mawenzi Tarn',
                'image' => $img('1621414050946-1b936a78491f'), 'caption' => 'Mawenzi',
                'text' => 'A short, steep walk to Mawenzi Tarn (about 4,330 m), a small lake under the spires of Mawenzi. An afternoon walk higher helps you acclimatise.',
                'stay' => 'Mawenzi Tarn', 'meals' => 'Full board',
            ],
            [
                'title' => 'Acclimatisation day at Mawenzi Tarn',
                'image' => $img('1503220317375-aaad61436b1b'), 'caption' => 'Mawenzi Tarn',
                'text' => 'A guided hike up the ridges around Mawenzi, then back down to rest at camp. Climbing high and sleeping low prepares your body for the summit.',
                'stay' => 'Mawenzi Tarn', 'meals' => 'Full board',
            ],
            [
                'title' => 'Mawenzi Tarn to School Hut',
                'image' => $img('1551632811-561732d1e306'), 'caption' => 'The Saddle',
                'text' => 'Cross the high desert of the Saddle towards Kibo and climb to School Hut (about 4,750 m). Early dinner and rest before summit night.',
                'stay' => 'School Hut', 'meals' => 'Full board',
            ],
            [
                'title' => 'Summit day: Uhuru Peak',
                'image' => $img('1609198092458-38a293c7ac4b'), 'caption' => 'Uhuru Peak',
                'text' => 'Leave around midnight and climb by headlamp to Gilman\'s Point on the crater rim, then along the rim to Uhuru Peak at sunrise. Descend past Kibo Hut to Horombo Hut for the night.',
                'note' => $summitNote,
                'stay' => 'Horombo Hut', 'meals' => 'Full board',
            ],
            [
                'title' => 'Horombo Hut to Marangu Gate',
                'image' => $img('1447752875215-b2761acb3c5d'), 'caption' => 'Marangu Gate',
                'text' => 'Walk down through moorland and rainforest to Marangu Gate, collect your summit certificate and transfer to your hotel in Moshi or Arusha.',
                'meals' => 'Breakfast & lunch',
            ],
        ],
        'included' => $climbIncluded,
        'excluded' => $climbExcluded,
    ],

    // 2 ─────────────────────────────────────────────────────────────────────
    [
        'slug' => 'northern-circuit-route-9-days',
        'title' => 'Northern Circuit Route',
        'category' => 'Kilimanjaro',
        'style' => 'luxury',
        'types' => ['kilimanjaro'],
        'tagline' => 'The longest route, the best acclimatisation',
        'chips' => [['label' => '9 Days'], ['label' => 'Challenging'], ['label' => 'Highest success rate', 'highlight' => true]],
        'text' => 'Nine days around the quiet northern slopes of Kibo, starting from the Lemosho rainforest. The slow pace gives the best chance of reaching Uhuru Peak.',
        'image' => $img('1621414050946-1b936a78491f'),
        'location' => 'Mount Kilimanjaro, Tanzania',
        'overview' => [
            'The Northern Circuit begins like the Lemosho route, crossing the Shira Plateau, then turns north and circles the mountain on trails very few climbers use. You walk almost all the way round Kibo before your summit attempt from School Hut.',
            'With nine days on the mountain, your body has the most time to adapt to the altitude, which is why this route has the highest summit success rate.',
        ],
        'facts' => ['Duration' => '9 Days · 8 Nights', 'Group size' => 'Private or small group', 'Difficulty' => 'Challenging', 'Summit' => '5,895 m', 'Best time' => 'January – March, June – October'],
        'itinerary' => [
            [
                'title' => 'Lemosho Gate to Mti Mkubwa',
                'image' => $img('1447752875215-b2761acb3c5d'), 'caption' => 'Rainforest',
                'text' => 'Drive to Londorossi Gate to register, continue to the Lemosho trailhead and walk through rich rainforest to Mti Mkubwa, "the big tree" camp (about 2,650 m).',
                'stay' => 'Mti Mkubwa Camp', 'meals' => 'Lunch & dinner',
            ],
            [
                'title' => 'Mti Mkubwa to Shira 1 Camp',
                'image' => $img('1489392191049-fc10c97e64b6'), 'caption' => 'Shira Ridge',
                'text' => 'Leave the forest, climb over the Shira Ridge and descend onto the Shira Plateau to Shira 1 Camp (about 3,500 m).',
                'stay' => 'Shira 1 Camp', 'meals' => 'Full board',
            ],
            [
                'title' => 'Across the plateau to Shira 2 Camp',
                'image' => $img('1503220317375-aaad61436b1b'), 'caption' => 'Shira Plateau',
                'text' => 'An easy day across the open plateau with Kibo in front of you, to Shira 2 Camp (about 3,850 m).',
                'stay' => 'Shira 2 Camp', 'meals' => 'Full board',
            ],
            [
                'title' => 'Lava Tower to Moir Hut',
                'image' => $img('1551632811-561732d1e306'), 'caption' => 'Lava Tower',
                'text' => 'Climb to Lava Tower (about 4,600 m) for lunch, then leave the busy southern routes and descend to Moir Hut (about 4,200 m) on the northern side.',
                'stay' => 'Moir Hut', 'meals' => 'Full board',
            ],
            [
                'title' => 'Moir Hut to Buffalo Camp',
                'image' => $img('1621414050946-1b936a78491f'), 'caption' => 'Northern slopes',
                'text' => 'Traverse the quiet northern slopes, with wide views towards Kenya, to Buffalo Camp (about 4,000 m).',
                'stay' => 'Buffalo Camp', 'meals' => 'Full board',
            ],
            [
                'title' => 'Buffalo Camp to Third Cave',
                'image' => $img('1489392191049-fc10c97e64b6'), 'caption' => 'Northern slopes',
                'text' => 'Continue around the mountain past Pofu Camp to Third Cave (about 3,900 m), with Mawenzi coming into view.',
                'stay' => 'Third Cave Camp', 'meals' => 'Full board',
            ],
            [
                'title' => 'Third Cave to School Hut',
                'image' => $img('1551632811-561732d1e306'), 'caption' => 'The Saddle',
                'text' => 'Climb onto the Saddle to School Hut (about 4,750 m). Early dinner and rest before summit night.',
                'stay' => 'School Hut', 'meals' => 'Full board',
            ],
            [
                'title' => 'Summit day: Uhuru Peak',
                'image' => $img('1609198092458-38a293c7ac4b'), 'caption' => 'Uhuru Peak',
                'text' => 'Leave around midnight for Gilman\'s Point and follow the crater rim to Uhuru Peak at sunrise. Descend on the Mweka side to Mweka Camp (about 3,100 m).',
                'note' => $summitNote,
                'stay' => 'Mweka Camp', 'meals' => 'Full board',
            ],
            [
                'title' => 'Mweka Camp to Mweka Gate',
                'image' => $img('1447752875215-b2761acb3c5d'), 'caption' => 'Mweka Gate',
                'text' => 'A last walk down through the rainforest to Mweka Gate, where you receive your summit certificate before the transfer to your hotel.',
                'meals' => 'Breakfast & lunch',
            ],
        ],
        'included' => $climbIncluded,
        'excluded' => $climbExcluded,
    ],

    // 3 ─────────────────────────────────────────────────────────────────────
    [
        'slug' => 'umbwe-route-6-days',
        'title' => 'Umbwe Route',
        'category' => 'Kilimanjaro',
        'style' => 'classic',
        'types' => ['kilimanjaro'],
        'tagline' => 'Steep, direct and spectacular',
        'chips' => [['label' => '6 Days'], ['label' => 'Very challenging'], ['label' => 'For experienced hikers', 'highlight' => true]],
        'text' => 'The steepest and most direct way up Kilimanjaro, climbing a narrow forest ridge before joining the southern circuit at Barranco. Best for fit, experienced trekkers.',
        'image' => $img('1609198092458-38a293c7ac4b'),
        'location' => 'Mount Kilimanjaro, Tanzania',
        'overview' => [
            'Umbwe gains height quickly on a ridge through the forest, so it is quiet and dramatic but less forgiving on acclimatisation. We recommend it to climbers who have been at high altitude before.',
            'From Barranco you follow the southern circuit, over the Barranco Wall and via Karanga to Barafu, then come down the Mweka route.',
        ],
        'facts' => ['Duration' => '6 Days · 5 Nights', 'Group size' => 'Private or small group', 'Difficulty' => 'Very challenging', 'Summit' => '5,895 m', 'Best time' => 'January – March, June – October'],
        'itinerary' => [
            [
                'title' => 'Umbwe Gate to Umbwe Cave Camp',
                'image' => $img('1447752875215-b2761acb3c5d'), 'caption' => 'Rainforest ridge',
                'text' => 'From Umbwe Gate, climb steeply up a forest ridge using roots as steps, to Umbwe Cave Camp (about 2,900 m).',
                'stay' => 'Umbwe Cave Camp', 'meals' => 'Lunch & dinner',
            ],
            [
                'title' => 'Umbwe Cave to Barranco Camp',
                'image' => $img('1489392191049-fc10c97e64b6'), 'caption' => 'Barranco Valley',
                'text' => 'Follow the ridge out of the forest into moorland, with the Western Breach towering ahead, to Barranco Camp (about 3,950 m).',
                'stay' => 'Barranco Camp', 'meals' => 'Full board',
            ],
            [
                'title' => 'Barranco Wall to Karanga Camp',
                'image' => $img('1551632811-561732d1e306'), 'caption' => 'Barranco Wall',
                'text' => 'Scramble up the Barranco Wall (hands needed, no ropes) and follow the trail over ridges and valleys to Karanga Camp (about 3,995 m).',
                'stay' => 'Karanga Camp', 'meals' => 'Full board',
            ],
            [
                'title' => 'Karanga Camp to Barafu Camp',
                'image' => $img('1621414050946-1b936a78491f'), 'caption' => 'Barafu',
                'text' => 'A short climb over rocky ground to Barafu Camp (about 4,670 m). Early dinner and rest before summit night.',
                'stay' => 'Barafu Camp', 'meals' => 'Full board',
            ],
            [
                'title' => 'Summit day: Uhuru Peak',
                'image' => $img('1609198092458-38a293c7ac4b'), 'caption' => 'Uhuru Peak',
                'text' => 'Climb by headlamp to Stella Point on the crater rim, then on to Uhuru Peak at sunrise. Descend past Barafu to Mweka Camp for the night.',
                'note' => $summitNote,
                'stay' => 'Mweka Camp', 'meals' => 'Full board',
            ],
            [
                'title' => 'Mweka Camp to Mweka Gate',
                'image' => $img('1447752875215-b2761acb3c5d'), 'caption' => 'Mweka Gate',
                'text' => 'Walk down through the rainforest to Mweka Gate for your summit certificate and the transfer to your hotel.',
                'meals' => 'Breakfast & lunch',
            ],
        ],
        'included' => $climbIncluded,
        'excluded' => $climbExcluded,
    ],

    // 4 ─────────────────────────────────────────────────────────────────────
    [
        'slug' => 'mount-meru-climb-4-days',
        'title' => 'Mount Meru Climb',
        'category' => 'Mount Meru',
        'style' => 'mid-range',
        'types' => ['kilimanjaro'],
        'tagline' => 'Tanzania\'s second-highest peak',
        'chips' => [['label' => '4 Days'], ['label' => 'Challenging'], ['label' => 'Great Kilimanjaro warm-up', 'highlight' => true]],
        'text' => 'Climb Mount Meru (4,566 m) in Arusha National Park, walking past giraffes and buffalo on the first day and watching the sun rise behind Kilimanjaro from the summit.',
        'image' => $img('1503220317375-aaad61436b1b'),
        'location' => 'Mount Meru, Arusha National Park',
        'overview' => [
            'Mount Meru is a beautiful climb in its own right and the best preparation for Kilimanjaro. The lower slopes are in Arusha National Park, so an armed ranger walks with you and you often see wildlife on the trail.',
            'You sleep in mountain huts. The final ridge to Socialist Peak is narrow and exposed in places, with huge views into the ash cone and across to Kilimanjaro.',
        ],
        'facts' => ['Duration' => '4 Days · 3 Nights', 'Group size' => 'Private or small group', 'Difficulty' => 'Challenging', 'Summit' => '4,566 m', 'Best time' => 'June – February'],
        'itinerary' => [
            [
                'title' => 'Momella Gate to Miriakamba Hut',
                'image' => $img('1547471080-7cc2caa01a7e'), 'caption' => 'Arusha National Park',
                'text' => 'From Momella Gate, walk with your ranger through open glades where giraffes and buffalo graze, past a fig tree arch and a waterfall, to Miriakamba Hut (about 2,500 m).',
                'stay' => 'Miriakamba Hut', 'meals' => 'Lunch & dinner',
            ],
            [
                'title' => 'Miriakamba Hut to Saddle Hut',
                'image' => $img('1489392191049-fc10c97e64b6'), 'caption' => 'Mount Meru',
                'text' => 'A steep climb through forest to Saddle Hut (about 3,570 m). In the afternoon, an optional walk up Little Meru (3,820 m) for sunset views.',
                'stay' => 'Saddle Hut', 'meals' => 'Full board',
            ],
            [
                'title' => 'Summit day: Socialist Peak',
                'image' => $img('1621414050946-1b936a78491f'), 'caption' => 'Socialist Peak',
                'text' => 'Leave in the early hours and follow the crater rim ridge to Socialist Peak (4,566 m) as the sun rises behind Kilimanjaro. Descend to Saddle Hut, then on to Miriakamba Hut for the night.',
                'note' => $summitNote,
                'stay' => 'Miriakamba Hut', 'meals' => 'Full board',
            ],
            [
                'title' => 'Miriakamba Hut to Momella Gate',
                'image' => $img('1547471080-7cc2caa01a7e'), 'caption' => 'Momella Gate',
                'text' => 'Walk down through the forest, looking for colobus monkeys, to Momella Gate and your transfer to Arusha.',
                'meals' => 'Breakfast & lunch',
            ],
        ],
        'included' => [
            'Professional mountain guide, cook and porters',
            'Armed park ranger',
            'Arusha National Park, hut and rescue fees',
            'Hut accommodation on the mountain',
            'All meals and treated drinking water on the mountain',
            'Transfers from and to Arusha',
            'Summit certificate',
        ],
        'excluded' => $climbExcluded,
    ],

    // 5 ─────────────────────────────────────────────────────────────────────
    [
        'slug' => 'kilimanjaro-day-hike',
        'title' => 'Kilimanjaro Day Hike',
        'category' => 'Kilimanjaro',
        'style' => 'budget',
        'types' => ['kilimanjaro', 'family'],
        'tagline' => 'Set foot on Kilimanjaro in a day',
        'chips' => [['label' => '1 Day'], ['label' => 'Moderate'], ['label' => 'No camping needed', 'highlight' => true]],
        'text' => 'A day walk on the Marangu route through the rainforest to Mandara Hut and the Maundi Crater, with monkeys in the trees and, on a clear day, views of the summit.',
        'image' => $img('1447752875215-b2761acb3c5d'),
        'location' => 'Mount Kilimanjaro, Tanzania',
        'overview' => [
            'Not ready for the full climb? This day hike lets you experience Kilimanjaro\'s rainforest with a mountain guide and a packed lunch, and be back at your hotel by the evening.',
            'Suitable for most reasonably fit walkers, including older children.',
        ],
        'facts' => ['Duration' => '1 Day', 'Group size' => 'Private', 'Difficulty' => 'Moderate', 'Highest point' => 'About 2,780 m', 'Best time' => 'All year'],
        'itinerary' => [
            [
                'title' => 'Day hike to Mandara Hut',
                'image' => $img('1447752875215-b2761acb3c5d'), 'caption' => 'Rainforest',
                'text' => 'Pick-up from Moshi in the morning and drive to Marangu Gate. Walk up through the rainforest, looking for blue and colobus monkeys, to Mandara Hut (about 2,700 m), then on to the rim of the Maundi Crater for views over the moorland. Picnic lunch, walk back down to the gate and return to Moshi.',
                'meals' => 'Packed lunch',
            ],
        ],
        'included' => [
            'Mountain guide',
            'Kilimanjaro National Park day fees',
            'Packed lunch and drinking water',
            'Transfers from and to Moshi',
        ],
        'excluded' => [
            'Travel insurance',
            'Hiking boots and rain jacket',
            'Tips for your guide',
        ],
    ],

    // 6 ─────────────────────────────────────────────────────────────────────
    [
        'slug' => 'kilimanjaro-climb-and-safari-10-days',
        'title' => 'Kilimanjaro Climb & Safari',
        'category' => 'Kilimanjaro',
        'style' => 'mid-range',
        'types' => ['kilimanjaro'],
        'tagline' => 'Summit, then celebrate with wildlife',
        'chips' => [['label' => '10 Days'], ['label' => 'Challenging'], ['label' => 'Machame + safari', 'highlight' => true]],
        'text' => 'Climb Kilimanjaro on the six-day Machame route, then rest your legs on a safari to Tarangire and the Ngorongoro Crater before flying home.',
        'image' => $img('1609198092458-38a293c7ac4b'),
        'location' => 'Kilimanjaro, Tarangire & Ngorongoro, Tanzania',
        'overview' => [
            'The perfect combination: the challenge of Africa\'s highest mountain, followed by an easy safari in a comfortable vehicle. The same team looks after you from the airport to the end of your trip.',
            'Prefer more time on the mountain? We can swap in the seven-day Machame or eight-day Lemosho route.',
        ],
        'facts' => ['Duration' => '10 Days · 9 Nights', 'Group size' => 'Private or small group', 'Difficulty' => 'Challenging', 'Summit' => '5,895 m', 'Best time' => 'January – March, June – October'],
        'itinerary' => [
            [
                'title' => 'Arrival in Moshi',
                'image' => $img('1488646953014-85cb44e25828'), 'caption' => 'Moshi',
                'text' => 'Pick-up at Kilimanjaro Airport and transfer to your hotel in Moshi. In the evening, your guide checks your gear and briefs you on the climb.',
                'stay' => 'Moshi', 'meals' => 'Dinner',
                'options' => [['level' => 'Comfort', 'name' => 'Hotel in Moshi']],
                'stay_image' => $img('1566073771259-6a8506099945'), 'stay_caption' => 'Hotel in Moshi',
            ],
            [
                'title' => 'Machame Gate to Machame Camp',
                'image' => $img('1447752875215-b2761acb3c5d'), 'caption' => 'Rainforest',
                'text' => 'Register at Machame Gate and climb through the rainforest to Machame Camp (about 3,000 m).',
                'stay' => 'Machame Camp', 'meals' => 'Full board',
            ],
            [
                'title' => 'Machame Camp to Shira Cave Camp',
                'image' => $img('1489392191049-fc10c97e64b6'), 'caption' => 'Shira Plateau',
                'text' => 'Leave the forest behind and climb a rocky ridge onto the Shira Plateau to Shira Cave Camp (about 3,750 m).',
                'stay' => 'Shira Cave Camp', 'meals' => 'Full board',
            ],
            [
                'title' => 'Lava Tower to Barranco Camp',
                'image' => $img('1551632811-561732d1e306'), 'caption' => 'Lava Tower',
                'text' => 'Climb high to Lava Tower (about 4,600 m) for lunch, then descend to Barranco Camp (about 3,950 m) to sleep. A key acclimatisation day.',
                'stay' => 'Barranco Camp', 'meals' => 'Full board',
            ],
            [
                'title' => 'Barranco Wall and on to Barafu Camp',
                'image' => $img('1621414050946-1b936a78491f'), 'caption' => 'Barranco Wall',
                'text' => 'Scramble up the Barranco Wall, pass Karanga Valley and continue to Barafu Camp (about 4,670 m). Early dinner and rest.',
                'stay' => 'Barafu Camp', 'meals' => 'Full board',
            ],
            [
                'title' => 'Summit day: Uhuru Peak',
                'image' => $img('1609198092458-38a293c7ac4b'), 'caption' => 'Uhuru Peak',
                'text' => 'Climb by headlamp to Stella Point and on to Uhuru Peak at sunrise, then descend to Mweka Camp.',
                'note' => $summitNote,
                'stay' => 'Mweka Camp', 'meals' => 'Full board',
            ],
            [
                'title' => 'Mweka Camp to Mweka Gate',
                'image' => $img('1447752875215-b2761acb3c5d'), 'caption' => 'Mweka Gate',
                'text' => 'Walk down to Mweka Gate for your summit certificate, then back to Moshi for a hot shower and a celebration dinner.',
                'stay' => 'Moshi', 'meals' => 'Breakfast, lunch & dinner',
                'options' => [['level' => 'Comfort', 'name' => 'Hotel in Moshi']],
                'stay_image' => $img('1566073771259-6a8506099945'), 'stay_caption' => 'Hotel in Moshi',
            ],
            [
                'title' => 'Moshi to Tarangire',
                'image' => $img('1585970480901-90d6bb2a48b5'), 'caption' => 'Tarangire',
                'text' => 'Meet your safari guide and drive to Tarangire National Park for an afternoon among elephant herds and baobab trees. Overnight in Karatu.',
                'stay' => 'Karatu', 'meals' => 'Full board',
                'options' => [['level' => 'Comfort', 'name' => 'Garden lodge, Karatu']],
                'stay_image' => $img('1447752875215-b2761acb3c5d'), 'stay_caption' => 'Karatu',
            ],
            [
                'title' => 'Ngorongoro Crater',
                'image' => $img('1523805009345-7448845a9e53'), 'caption' => 'Ngorongoro Crater',
                'text' => 'Descend into the Ngorongoro Crater for a full day looking for lions, black rhinos, hippos and flamingos, with a picnic lunch by a hippo pool.',
                'stay' => 'Karatu', 'meals' => 'Full board',
                'options' => [['level' => 'Comfort', 'name' => 'Garden lodge, Karatu']],
                'stay_image' => $img('1447752875215-b2761acb3c5d'), 'stay_caption' => 'Karatu',
            ],
            [
                'title' => 'Departure from Kilimanjaro Airport',
                'image' => $img('1547471080-7cc2caa01a7e'), 'caption' => 'Arusha',
                'text' => 'Drive back via Arusha for lunch and souvenir shopping, then on to Kilimanjaro Airport for your flight home.',
                'meals' => 'Breakfast & lunch',
            ],
        ],
        'included' => [...$climbIncluded, 'Two nights in Moshi and two in Karatu', 'Private safari vehicle and driver-guide', 'Tarangire and Ngorongoro park fees and crater service fee'],
        'excluded' => $climbExcluded,
    ],

];
