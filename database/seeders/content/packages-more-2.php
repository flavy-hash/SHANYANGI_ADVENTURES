<?php

/*
|--------------------------------------------------------------------------
| More starter packages: southern & cultural safaris, Zanzibar holidays
|--------------------------------------------------------------------------
|
| Loaded by MorePackagesSeeder together with packages-more.php: a package is
| only added when its slug doesn't exist yet. Prices are left empty ("On
| request"); set them in the admin.
|
*/

$img = fn (string $id) => "https://images.unsplash.com/photo-{$id}?auto=format&fit=crop&w=1200&q=75";

$safariIncluded = [
    'Private 4x4 safari vehicle with pop-up roof',
    'Professional English-speaking driver-guide',
    'Park entry and conservation fees',
    'Accommodation as per itinerary',
    'Meals as listed in the itinerary',
    'Drinking water in the vehicle',
];

$safariExcluded = [
    'International flights and visa',
    'Travel insurance',
    'Drinks at lodges and camps',
    'Optional activities',
    'Tips for your guide and lodge staff',
];

$zanzibarIncluded = [
    'Zanzibar airport transfers',
    'Hotel accommodation as per itinerary',
    'Meals as listed in the itinerary',
    'Guided excursions as listed, with entrance fees',
];

$zanzibarExcluded = [
    'International flights and visa',
    'Travel insurance',
    'Zanzibar infrastructure tax charged by hotels',
    'Drinks and personal expenses',
    'Optional excursions and tips',
];

return [

    // 1 ─────────────────────────────────────────────────────────────────────
    [
        'slug' => 'southern-tanzania-nyerere-ruaha-7-days',
        'title' => 'Southern Tanzania: Nyerere & Ruaha',
        'category' => 'Safari',
        'style' => 'luxury',
        'types' => ['luxury'],
        'tagline' => 'Boat safaris and big wild country',
        'chips' => [['label' => '7 Days'], ['label' => 'Fly-in'], ['label' => 'Boat & walking safaris', 'highlight' => true]],
        'text' => 'Fly into Tanzania\'s quieter south: boat safaris on the Rufiji River in Nyerere National Park, then the baobab-studded valleys of Ruaha, famous for large lion prides.',
        'image' => $img('1516026672322-bc52d61a55d5'),
        'location' => 'Nyerere & Ruaha National Parks, Tanzania',
        'overview' => [
            'The southern parks are huge, wild and far less visited than the northern circuit. Light aircraft connect them, so you spend your time on game drives, boat trips and guided walks instead of on the road.',
            'This is a trip for travellers who have done a classic safari, or who simply want more space and fewer vehicles.',
        ],
        'facts' => ['Duration' => '7 Days · 6 Nights', 'Group size' => 'Private', 'Difficulty' => 'Easy', 'Best time' => 'June – October'],
        'itinerary' => [
            [
                'title' => 'Dar es Salaam to Nyerere National Park',
                'image' => $img('1488646953014-85cb44e25828'), 'caption' => 'Dar es Salaam',
                'text' => 'A short morning flight from Dar es Salaam lands on a bush airstrip in Nyerere National Park, the former Selous. Afternoon game drive to your camp near the river.',
                'stay' => 'Nyerere National Park', 'meals' => 'Full board',
                'options' => [['level' => 'Premium', 'name' => 'Riverside tented camp, Nyerere']],
                'stay_image' => $img('1504280390367-361c6d9f38f4'), 'stay_caption' => 'Riverside camp',
            ],
            [
                'title' => 'Boat safari on the Rufiji River',
                'image' => $img('1549366021-9f761d450615'), 'caption' => 'Rufiji River',
                'text' => 'Glide past hippos, crocodiles and riverside elephants by boat, then head out on an afternoon game drive looking for wild dogs and lions.',
                'activity' => ['title' => 'Boat safari', 'image' => $img('1549366021-9f761d450615'), 'text' => 'A slow, quiet way to watch wildlife and birds along the water\'s edge.'],
                'stay' => 'Nyerere National Park', 'meals' => 'Full board',
                'options' => [['level' => 'Premium', 'name' => 'Riverside tented camp, Nyerere']],
                'stay_image' => $img('1504280390367-361c6d9f38f4'), 'stay_caption' => 'Riverside camp',
            ],
            [
                'title' => 'Walking safari in Nyerere',
                'image' => $img('1516426122078-c23e76319801'), 'caption' => 'Nyerere',
                'text' => 'Explore on foot with an armed ranger, learning about tracks, plants and insects, then enjoy a final game drive at sunset.',
                'stay' => 'Nyerere National Park', 'meals' => 'Full board',
                'options' => [['level' => 'Premium', 'name' => 'Riverside tented camp, Nyerere']],
                'stay_image' => $img('1478131143081-80f7f84ca84d'), 'stay_caption' => 'Evening at camp',
            ],
            [
                'title' => 'Fly to Ruaha National Park',
                'image' => $img('1585970480901-90d6bb2a48b5'), 'caption' => 'Ruaha',
                'text' => 'Fly west to Ruaha, one of Tanzania\'s largest parks. Its rocky hills, baobabs and sand rivers are home to big lion prides and many elephants.',
                'stay' => 'Ruaha National Park', 'meals' => 'Full board',
                'options' => [['level' => 'Premium', 'name' => 'Luxury camp, Ruaha']],
                'stay_image' => $img('1566073771259-6a8506099945'), 'stay_caption' => 'Safari lodge',
            ],
            [
                'title' => 'Ruaha: the Great Ruaha River',
                'image' => $img('1535941339077-2dd1c7963098'), 'caption' => 'Ruaha',
                'text' => 'A full day following the Great Ruaha River, where predators wait for the animals coming down to drink.',
                'stay' => 'Ruaha National Park', 'meals' => 'Full board',
                'options' => [['level' => 'Premium', 'name' => 'Luxury camp, Ruaha']],
                'stay_image' => $img('1566073771259-6a8506099945'), 'stay_caption' => 'Safari lodge',
            ],
            [
                'title' => 'Ruaha at your own pace',
                'image' => $img('1523805009345-7448845a9e53'), 'caption' => 'Ruaha',
                'text' => 'Choose between a long game drive with a picnic lunch, a guided walk, or a relaxed afternoon at camp before a sundowner overlooking the valley.',
                'stay' => 'Ruaha National Park', 'meals' => 'Full board',
                'options' => [['level' => 'Premium', 'name' => 'Luxury camp, Ruaha']],
                'stay_image' => $img('1504280390367-361c6d9f38f4'), 'stay_caption' => 'Tented camp',
            ],
            [
                'title' => 'Fly back to Dar es Salaam',
                'image' => $img('1547471080-7cc2caa01a7e'), 'caption' => 'Southern Tanzania',
                'text' => 'A final early game drive, then the flight back to Dar es Salaam for your onward connection, or straight on to Zanzibar.',
                'meals' => 'Breakfast & lunch',
            ],
        ],
        'included' => [...$safariIncluded, 'Light-aircraft flights Dar es Salaam – Nyerere – Ruaha – Dar es Salaam', 'Boat safari and guided walk'],
        'excluded' => $safariExcluded,
    ],

    // 2 ─────────────────────────────────────────────────────────────────────
    [
        'slug' => 'mikumi-weekend-safari-2-days',
        'title' => 'Mikumi Weekend Safari',
        'category' => 'Safari',
        'style' => 'budget',
        'types' => [],
        'tagline' => 'A quick safari from Dar es Salaam',
        'chips' => [['label' => '2 Days'], ['label' => 'Short break'], ['label' => 'Close to Dar', 'highlight' => true]],
        'text' => 'The easiest safari from Dar es Salaam or Zanzibar: open plains with elephants, giraffes, zebras and lions, just a few hours\' drive inland.',
        'image' => $img('1585970480901-90d6bb2a48b5'),
        'location' => 'Mikumi National Park, Tanzania',
        'overview' => [
            'Mikumi\'s open floodplain is often compared to the Serengeti on a smaller scale, and it is the closest big park to Dar es Salaam. It is ideal for a weekend, or for a taste of safari at the end of a beach or business trip.',
            'Fly-in options from Zanzibar are available if you prefer to skip the drive.',
        ],
        'facts' => ['Duration' => '2 Days · 1 Night', 'Group size' => 'Private', 'Difficulty' => 'Easy', 'Best time' => 'June – October'],
        'itinerary' => [
            [
                'title' => 'Dar es Salaam to Mikumi National Park',
                'image' => $img('1585970480901-90d6bb2a48b5'), 'caption' => 'Mikumi',
                'text' => 'Leave Dar es Salaam early and drive west to Mikumi National Park. After lunch, spend the afternoon on the Mkata floodplain looking for elephants, giraffes, buffalo and lions.',
                'stay' => 'Mikumi National Park', 'meals' => 'Lunch & dinner',
                'options' => [['level' => 'Comfort', 'name' => 'Tented camp, Mikumi'], ['level' => 'Premium', 'name' => 'Safari lodge, Mikumi']],
                'stay_image' => $img('1504280390367-361c6d9f38f4'), 'stay_caption' => 'Tented camp',
            ],
            [
                'title' => 'Morning game drive and return to Dar es Salaam',
                'image' => $img('1523805009345-7448845a9e53'), 'caption' => 'Mikumi',
                'text' => 'An early game drive when the animals are most active, breakfast at camp, then the drive back to Dar es Salaam in the afternoon.',
                'meals' => 'Breakfast & lunch',
            ],
        ],
        'included' => [...$safariIncluded, 'Transfers from and to Dar es Salaam'],
        'excluded' => $safariExcluded,
    ],

    // 3 ─────────────────────────────────────────────────────────────────────
    [
        'slug' => 'arusha-national-park-day-trip',
        'title' => 'Arusha National Park Day Trip',
        'category' => 'Safari',
        'style' => 'budget',
        'types' => ['family'],
        'tagline' => 'Walk, drive and canoe in one day',
        'chips' => [['label' => '1 Day'], ['label' => 'Easy'], ['label' => 'Walking safari', 'highlight' => true]],
        'text' => 'A day close to Arusha: a guided walk among giraffes and buffalo, a drive past the Momella Lakes and views of Mount Meru, with an optional canoe trip.',
        'image' => $img('1547471080-7cc2caa01a7e'),
        'location' => 'Arusha National Park, Tanzania',
        'overview' => [
            'Arusha National Park is small, green and only half an hour from town, which makes it perfect for the day you arrive or the day before a climb. It is one of the few parks where you can walk with a ranger.',
            'Colobus monkeys in the forest, flamingos on the lakes and Mount Meru on the skyline make it very different from the open plains of the Serengeti.',
        ],
        'facts' => ['Duration' => '1 Day', 'Group size' => 'Private', 'Difficulty' => 'Easy', 'Best time' => 'All year'],
        'itinerary' => [
            [
                'title' => 'Arusha National Park walking safari',
                'image' => $img('1547471080-7cc2caa01a7e'), 'caption' => 'Arusha National Park',
                'text' => 'Start with a guided walk with an armed ranger across open glades with giraffes, zebras and buffalo, then drive through the forest looking for colobus monkeys and on to the Momella Lakes. Picnic lunch with views of Mount Meru before returning to Arusha in the afternoon.',
                'activity' => ['title' => 'Canoeing on Small Momella Lake (Optional)', 'image' => $img('1503220317375-aaad61436b1b'), 'text' => 'Paddle quietly past water birds and, with luck, hippos and giraffes at the water\'s edge.'],
                'meals' => 'Picnic lunch',
            ],
        ],
        'included' => [...$safariIncluded, 'Armed ranger for the walking safari', 'Pick-up and drop-off in Arusha'],
        'excluded' => [...$safariExcluded, 'Optional canoeing'],
    ],

    // 4 ─────────────────────────────────────────────────────────────────────
    [
        'slug' => 'lake-eyasi-cultural-safari-4-days',
        'title' => 'Lake Eyasi Cultural Safari',
        'category' => 'Safari',
        'style' => 'mid-range',
        'types' => ['family'],
        'tagline' => 'People, landscapes and the crater',
        'chips' => [['label' => '4 Days'], ['label' => 'Cultural'], ['label' => 'Hadzabe & Datoga', 'highlight' => true]],
        'text' => 'Meet the Hadzabe hunter-gatherers and Datoga blacksmiths at Lake Eyasi, then spend a day in the Ngorongoro Crater and an afternoon in Tarangire.',
        'image' => $img('1489392191049-fc10c97e64b6'),
        'location' => 'Lake Eyasi, Ngorongoro & Tarangire, Tanzania',
        'overview' => [
            'This trip combines wildlife with time among some of Tanzania\'s oldest communities. At Lake Eyasi you join the Hadzabe on an early morning walk and visit Datoga families who craft metal jewellery and tools, always with a local guide and with respect for their way of life.',
            'The cultural visits are balanced with a full day in the Ngorongoro Crater and a game drive in Tarangire.',
        ],
        'facts' => ['Duration' => '4 Days · 3 Nights', 'Group size' => 'Private', 'Difficulty' => 'Easy', 'Best time' => 'All year'],
        'itinerary' => [
            [
                'title' => 'Arusha to Tarangire',
                'image' => $img('1585970480901-90d6bb2a48b5'), 'caption' => 'Tarangire',
                'text' => 'Drive to Tarangire National Park for an afternoon among elephants and baobabs, then continue to Karatu for the night.',
                'stay' => 'Karatu', 'meals' => 'Full board',
                'options' => [['level' => 'Comfort', 'name' => 'Garden lodge, Karatu'], ['level' => 'Premium', 'name' => 'Coffee farm lodge, Karatu']],
                'stay_image' => $img('1447752875215-b2761acb3c5d'), 'stay_caption' => 'Karatu',
            ],
            [
                'title' => 'Karatu to Lake Eyasi',
                'image' => $img('1489392191049-fc10c97e64b6'), 'caption' => 'Lake Eyasi',
                'text' => 'Drive down to the shores of Lake Eyasi. In the afternoon, visit a Datoga family and see how they turn scrap metal into jewellery and arrowheads, then walk to the lake for sunset.',
                'stay' => 'Lake Eyasi', 'meals' => 'Full board',
                'options' => [['level' => 'Comfort', 'name' => 'Lakeside camp, Lake Eyasi']],
                'stay_image' => $img('1478131143081-80f7f84ca84d'), 'stay_caption' => 'Lakeside camp',
            ],
            [
                'title' => 'Morning with the Hadzabe, then Ngorongoro',
                'image' => $img('1432405972618-c60b0225b8f9'), 'caption' => 'Lake Eyasi',
                'text' => 'Join a Hadzabe group at dawn as they gather food and track game in the bush, then drive up to the Ngorongoro highlands for the night.',
                'note' => 'Visits are arranged with the communities and part of the fee goes directly to them.',
                'stay' => 'Ngorongoro Crater rim', 'meals' => 'Full board',
                'options' => [['level' => 'Comfort', 'name' => 'Highland lodge near the crater'], ['level' => 'Premium', 'name' => 'Crater rim lodge with caldera views']],
                'stay_image' => $img('1500835556837-99ac94a94552'), 'stay_caption' => 'Crater rim',
            ],
            [
                'title' => 'Ngorongoro Crater and return to Arusha',
                'image' => $img('1523805009345-7448845a9e53'), 'caption' => 'Ngorongoro Crater',
                'text' => 'Spend the morning on the crater floor looking for lions, rhinos and flamingos, then drive back to Arusha.',
                'meals' => 'Breakfast & lunch',
            ],
        ],
        'included' => [...$safariIncluded, 'Ngorongoro Crater service fee', 'Hadzabe and Datoga community visits with a local guide', 'Pick-up and drop-off in Arusha'],
        'excluded' => $safariExcluded,
    ],

    // 5 ─────────────────────────────────────────────────────────────────────
    [
        'slug' => 'budget-camping-safari-5-days',
        'title' => 'Budget Camping Safari',
        'category' => 'Safari',
        'style' => 'budget',
        'types' => [],
        'tagline' => 'Sleep under canvas in the parks',
        'chips' => [['label' => '5 Days'], ['label' => 'Camping'], ['label' => 'Best value', 'highlight' => true]],
        'text' => 'The northern circuit at a friendly price: Tarangire, the Serengeti and the Ngorongoro Crater, sleeping in dome tents at public campsites with a cook travelling with you.',
        'image' => $img('1504280390367-361c6d9f38f4'),
        'location' => 'Tarangire, Serengeti & Ngorongoro, Tanzania',
        'overview' => [
            'Camping inside the parks is the most affordable way to see the Serengeti, and it brings you close to the sounds of the bush at night. Your guide drives, your cook prepares fresh meals, and we provide tents, mattresses and camping equipment.',
            'Facilities at public campsites are simple, with shared bathrooms. Bring a sleeping bag, or rent one from us.',
        ],
        'facts' => ['Duration' => '5 Days · 4 Nights', 'Group size' => 'Private or small group', 'Difficulty' => 'Moderate (camping)', 'Best time' => 'June – October, December – February'],
        'itinerary' => [
            [
                'title' => 'Arusha to Tarangire',
                'image' => $img('1585970480901-90d6bb2a48b5'), 'caption' => 'Tarangire',
                'text' => 'Afternoon game drive in Tarangire, then set up camp at a campsite outside the park.',
                'stay' => 'Tarangire area', 'meals' => 'Full board',
                'options' => [['level' => 'Camping', 'name' => 'Public campsite near Tarangire']],
                'stay_image' => $img('1504280390367-361c6d9f38f4'), 'stay_caption' => 'Campsite',
            ],
            [
                'title' => 'Into the Serengeti',
                'image' => $img('1516426122078-c23e76319801'), 'caption' => 'Serengeti Plains',
                'text' => 'Cross the Ngorongoro highlands into the Serengeti and game drive to a public campsite in the Seronera area.',
                'stay' => 'Central Serengeti', 'meals' => 'Full board',
                'options' => [['level' => 'Camping', 'name' => 'Public campsite, Central Serengeti']],
                'stay_image' => $img('1478131143081-80f7f84ca84d'), 'stay_caption' => 'Evening at camp',
            ],
            [
                'title' => 'A full day in the Serengeti',
                'image' => $img('1535941339077-2dd1c7963098'), 'caption' => 'Seronera Valley',
                'text' => 'Morning and afternoon game drives in the central Serengeti, with a lunch break back at camp.',
                'stay' => 'Central Serengeti', 'meals' => 'Full board',
                'options' => [['level' => 'Camping', 'name' => 'Public campsite, Central Serengeti']],
                'stay_image' => $img('1504280390367-361c6d9f38f4'), 'stay_caption' => 'Campsite',
            ],
            [
                'title' => 'Serengeti to Ngorongoro',
                'image' => $img('1500835556837-99ac94a94552'), 'caption' => 'Crater rim',
                'text' => 'A last morning in the Serengeti, then on to a campsite on the rim of the Ngorongoro Crater, which can be cold at night.',
                'stay' => 'Ngorongoro Crater rim', 'meals' => 'Full board',
                'options' => [['level' => 'Camping', 'name' => 'Simba campsite, crater rim']],
                'stay_image' => $img('1478131143081-80f7f84ca84d'), 'stay_caption' => 'Crater rim camp',
            ],
            [
                'title' => 'Ngorongoro Crater and return to Arusha',
                'image' => $img('1523805009345-7448845a9e53'), 'caption' => 'Ngorongoro Crater',
                'text' => 'Descend early into the crater for a game drive, then return to Arusha in the late afternoon.',
                'meals' => 'Breakfast & lunch',
            ],
        ],
        'included' => [...$safariIncluded, 'Ngorongoro Crater service fee', 'Camping fees, tents, mattresses and camping equipment', 'Safari cook'],
        'excluded' => [...$safariExcluded, 'Sleeping bag (rental available)'],
    ],

    // 6 ─────────────────────────────────────────────────────────────────────
    [
        'slug' => 'zanzibar-island-holiday-7-days',
        'title' => 'Zanzibar Island Holiday',
        'category' => 'Zanzibar',
        'style' => 'mid-range',
        'types' => ['zanzibar'],
        'tagline' => 'History, spices and white beaches',
        'chips' => [['label' => '7 Days'], ['label' => 'Relaxed'], ['label' => 'Stone Town & beach', 'highlight' => true]],
        'text' => 'Two nights in historic Stone Town, then four on the north coast beaches, with a spice farm, Prison Island\'s giant tortoises and a snorkelling trip to Mnemba.',
        'image' => $img('1507525428034-b723cf961d3e'),
        'location' => 'Zanzibar, Tanzania',
        'overview' => [
            'This holiday shows both sides of Zanzibar: the winding streets, markets and rooftop cafés of Stone Town, and the turquoise water and white sand of the north coast.',
            'Excursions are spread out so that you still have plenty of time to do nothing at all.',
        ],
        'facts' => ['Duration' => '7 Days · 6 Nights', 'Group size' => 'Private', 'Difficulty' => 'Easy', 'Best time' => 'June – October, December – February'],
        'itinerary' => [
            [
                'title' => 'Arrival in Stone Town',
                'image' => $img('1571896349842-33c89424de2d'), 'caption' => 'Stone Town',
                'text' => 'We meet you at Zanzibar Airport and take you to a boutique hotel in Stone Town. In the evening, try local street food at the Forodhani Gardens night market.',
                'stay' => 'Stone Town', 'meals' => 'Breakfast',
                'options' => [['level' => 'Comfort', 'name' => 'Boutique hotel, Stone Town']],
                'stay_image' => $img('1571896349842-33c89424de2d'), 'stay_caption' => 'Stone Town hotel',
            ],
            [
                'title' => 'Stone Town walk and Prison Island',
                'image' => $img('1571896349842-33c89424de2d'), 'caption' => 'Prison Island',
                'text' => 'A guided walk through Stone Town\'s history in the morning, then a short boat ride to Prison Island to meet its giant tortoises and swim from the sandbank.',
                'stay' => 'Stone Town', 'meals' => 'Breakfast',
                'options' => [['level' => 'Comfort', 'name' => 'Boutique hotel, Stone Town']],
                'stay_image' => $img('1571896349842-33c89424de2d'), 'stay_caption' => 'Stone Town hotel',
            ],
            [
                'title' => 'Spice farm and on to the north coast',
                'image' => $img('1447752875215-b2761acb3c5d'), 'caption' => 'Spice farm',
                'text' => 'Taste fresh cinnamon, cloves and vanilla on a spice farm, then continue to your beach hotel in Nungwi or Kendwa on the north coast.',
                'stay' => 'North coast beach', 'meals' => 'Half board',
                'options' => [['level' => 'Comfort', 'name' => 'Beach hotel, Nungwi'], ['level' => 'Premium', 'name' => 'Beachfront resort, Kendwa']],
                'stay_image' => $img('1520250497591-112f2f40a3f4'), 'stay_caption' => 'Beach hotel',
            ],
            [
                'title' => 'Snorkelling at Mnemba Atoll',
                'image' => $img('1544551763-46a013bb70d5'), 'caption' => 'Mnemba Atoll',
                'text' => 'A boat trip to Mnemba Atoll, with coral gardens, colourful fish and often dolphins on the way. Back at the hotel by lunchtime.',
                'stay' => 'North coast beach', 'meals' => 'Half board',
                'options' => [['level' => 'Comfort', 'name' => 'Beach hotel, Nungwi'], ['level' => 'Premium', 'name' => 'Beachfront resort, Kendwa']],
                'stay_image' => $img('1520250497591-112f2f40a3f4'), 'stay_caption' => 'Beach hotel',
            ],
            [
                'title' => 'A free day on the north coast',
                'image' => $img('1507525428034-b723cf961d3e'), 'caption' => 'Nungwi',
                'text' => 'Swim, read and walk along the beach, or visit the turtle sanctuary in Nungwi village.',
                'stay' => 'North coast beach', 'meals' => 'Half board',
                'options' => [['level' => 'Comfort', 'name' => 'Beach hotel, Nungwi'], ['level' => 'Premium', 'name' => 'Beachfront resort, Kendwa']],
            ],
            [
                'title' => 'Sunset dhow cruise',
                'image' => $img('1507525428034-b723cf961d3e'), 'caption' => 'Nungwi',
                'text' => 'Another easy beach day, ending with a sunset cruise on a traditional wooden dhow.',
                'stay' => 'North coast beach', 'meals' => 'Half board',
                'options' => [['level' => 'Comfort', 'name' => 'Beach hotel, Nungwi'], ['level' => 'Premium', 'name' => 'Beachfront resort, Kendwa']],
            ],
            [
                'title' => 'Departure from Zanzibar',
                'image' => $img('1520250497591-112f2f40a3f4'), 'caption' => 'Zanzibar',
                'text' => 'Breakfast by the sea and the transfer to Zanzibar Airport.',
                'meals' => 'Breakfast',
            ],
        ],
        'included' => [...$zanzibarIncluded, 'Stone Town walk, Prison Island, spice farm, Mnemba snorkelling and sunset dhow cruise'],
        'excluded' => $zanzibarExcluded,
    ],

    // 7 ─────────────────────────────────────────────────────────────────────
    [
        'slug' => 'zanzibar-honeymoon-6-days',
        'title' => 'Zanzibar Honeymoon',
        'category' => 'Zanzibar',
        'style' => 'luxury',
        'types' => ['honeymoon', 'zanzibar', 'luxury'],
        'tagline' => 'Barefoot luxury for two',
        'chips' => [['label' => '6 Days'], ['label' => 'Romantic'], ['label' => 'Private villa', 'highlight' => true]],
        'text' => 'A private villa on the east coast, a candlelit dinner on the sand, a sandbank picnic and a sunset dhow cruise: Zanzibar at its most romantic.',
        'image' => $img('1520250497591-112f2f40a3f4'),
        'location' => 'East coast & Stone Town, Zanzibar',
        'overview' => [
            'Designed for couples, this trip keeps the days slow and the moments special. You stay in a villa with its own plunge pool on the quiet east coast, with a night in Stone Town to finish.',
            'We add small surprises along the way: tell us about your celebration when you book.',
        ],
        'facts' => ['Duration' => '6 Days · 5 Nights', 'Group size' => 'Private', 'Difficulty' => 'Easy', 'Best time' => 'June – October, December – March'],
        'itinerary' => [
            [
                'title' => 'Arrival and transfer to the east coast',
                'image' => $img('1520250497591-112f2f40a3f4'), 'caption' => 'East coast',
                'text' => 'Private transfer from Zanzibar Airport to your villa on the east coast near Paje, with a welcome drink and time to settle in.',
                'stay' => 'East coast', 'meals' => 'Half board',
                'options' => [['level' => 'Premium', 'name' => 'Pool villa, east coast']],
                'stay_image' => $img('1566073771259-6a8506099945'), 'stay_caption' => 'Pool villa',
            ],
            [
                'title' => 'Sandbank picnic',
                'image' => $img('1507525428034-b723cf961d3e'), 'caption' => 'Sandbank',
                'text' => 'Sail to a sandbank that appears at low tide for a seafood picnic, swimming and snorkelling, just the two of you and your crew.',
                'stay' => 'East coast', 'meals' => 'Half board',
                'options' => [['level' => 'Premium', 'name' => 'Pool villa, east coast']],
            ],
            [
                'title' => 'Spa and dinner on the beach',
                'image' => $img('1520250497591-112f2f40a3f4'), 'caption' => 'Paje',
                'text' => 'A couples\' spa treatment in the afternoon and a private candlelit dinner on the beach in the evening.',
                'stay' => 'East coast', 'meals' => 'Half board',
                'options' => [['level' => 'Premium', 'name' => 'Pool villa, east coast']],
            ],
            [
                'title' => 'Jozani Forest and a slow afternoon',
                'image' => $img('1447752875215-b2761acb3c5d'), 'caption' => 'Jozani Forest',
                'text' => 'A short morning visit to Jozani Forest to see the rare red colobus monkeys, then back to the villa for a relaxed afternoon.',
                'stay' => 'East coast', 'meals' => 'Half board',
                'options' => [['level' => 'Premium', 'name' => 'Pool villa, east coast']],
            ],
            [
                'title' => 'Stone Town and a sunset dhow cruise',
                'image' => $img('1571896349842-33c89424de2d'), 'caption' => 'Stone Town',
                'text' => 'Transfer to Stone Town for a private walk through its old streets, then sail into the sunset on a traditional dhow. Dinner on a rooftop terrace.',
                'stay' => 'Stone Town', 'meals' => 'Breakfast & dinner',
                'options' => [['level' => 'Premium', 'name' => 'Heritage hotel, Stone Town']],
                'stay_image' => $img('1571896349842-33c89424de2d'), 'stay_caption' => 'Stone Town hotel',
            ],
            [
                'title' => 'Departure',
                'image' => $img('1507525428034-b723cf961d3e'), 'caption' => 'Zanzibar',
                'text' => 'A late breakfast, then the private transfer to Zanzibar Airport.',
                'meals' => 'Breakfast',
            ],
        ],
        'included' => [...$zanzibarIncluded, 'Private transfers throughout', 'Sandbank picnic, couples\' spa treatment, beach dinner, Jozani Forest, Stone Town walk and sunset dhow cruise'],
        'excluded' => $zanzibarExcluded,
    ],

    // 8 ─────────────────────────────────────────────────────────────────────
    [
        'slug' => 'stone-town-short-break-3-days',
        'title' => 'Stone Town Short Break',
        'category' => 'Zanzibar',
        'style' => 'budget',
        'types' => ['zanzibar'],
        'tagline' => 'Zanzibar\'s old town in three days',
        'chips' => [['label' => '3 Days'], ['label' => 'Cultural'], ['label' => 'Spices & history', 'highlight' => true]],
        'text' => 'A short, easy add-on to a safari or a business trip: Stone Town\'s history, a spice farm, Prison Island and the night food market.',
        'image' => $img('1571896349842-33c89424de2d'),
        'location' => 'Stone Town, Zanzibar',
        'overview' => [
            'Stone Town is a UNESCO World Heritage Site, a maze of carved doors, markets, mosques and churches shaped by African, Arab, Indian and European influences. Three days is enough to see the highlights at an easy pace.',
            'Easy to extend with a few nights on the beach.',
        ],
        'facts' => ['Duration' => '3 Days · 2 Nights', 'Group size' => 'Private', 'Difficulty' => 'Easy', 'Best time' => 'All year'],
        'itinerary' => [
            [
                'title' => 'Arrival and Forodhani night market',
                'image' => $img('1571896349842-33c89424de2d'), 'caption' => 'Stone Town',
                'text' => 'Transfer from the airport or ferry port to your hotel in Stone Town, and an evening walk to the Forodhani Gardens food market.',
                'stay' => 'Stone Town', 'meals' => 'Breakfast',
                'options' => [['level' => 'Comfort', 'name' => 'Boutique hotel, Stone Town']],
                'stay_image' => $img('1571896349842-33c89424de2d'), 'stay_caption' => 'Stone Town hotel',
            ],
            [
                'title' => 'Stone Town tour and spice farm',
                'image' => $img('1447752875215-b2761acb3c5d'), 'caption' => 'Spice farm',
                'text' => 'A guided morning walk through the old town and the Darajani market, then an afternoon at a spice farm tasting the fruits and spices Zanzibar is famous for.',
                'stay' => 'Stone Town', 'meals' => 'Breakfast & lunch',
                'options' => [['level' => 'Comfort', 'name' => 'Boutique hotel, Stone Town']],
            ],
            [
                'title' => 'Prison Island and departure',
                'image' => $img('1507525428034-b723cf961d3e'), 'caption' => 'Prison Island',
                'text' => 'A morning boat trip to Prison Island to see the giant tortoises and swim, then the transfer to the airport or ferry.',
                'meals' => 'Breakfast',
            ],
        ],
        'included' => [...$zanzibarIncluded, 'Stone Town tour, spice farm and Prison Island trip'],
        'excluded' => $zanzibarExcluded,
    ],

    // 9 ─────────────────────────────────────────────────────────────────────
    [
        'slug' => 'zanzibar-snorkelling-diving-5-days',
        'title' => 'Zanzibar Snorkelling & Diving',
        'category' => 'Zanzibar',
        'style' => 'mid-range',
        'types' => ['zanzibar'],
        'tagline' => 'Reefs, dolphins and turtles',
        'chips' => [['label' => '5 Days'], ['label' => 'Active'], ['label' => 'Mnemba Atoll', 'highlight' => true]],
        'text' => 'Five days on the north-east coast with boat trips to Mnemba Atoll, a dolphin and turtle trip, and the option to try diving or do certified dives.',
        'image' => $img('1544551763-46a013bb70d5'),
        'location' => 'North-east coast, Zanzibar',
        'overview' => [
            'Zanzibar\'s warm, clear water is ideal for snorkellers and divers. This trip is based close to Mnemba Atoll, a protected marine area with healthy coral, reef fish and frequent dolphin sightings.',
            'Beginners can try a first dive with an instructor; certified divers can book guided dives instead of the snorkelling trips.',
        ],
        'facts' => ['Duration' => '5 Days · 4 Nights', 'Group size' => 'Private or small group', 'Difficulty' => 'Easy to moderate', 'Best time' => 'October – March'],
        'itinerary' => [
            [
                'title' => 'Arrival on the north-east coast',
                'image' => $img('1520250497591-112f2f40a3f4'), 'caption' => 'Matemwe',
                'text' => 'Transfer from the airport to your beach hotel near Matemwe, facing Mnemba Atoll.',
                'stay' => 'North coast beach', 'meals' => 'Half board',
                'options' => [['level' => 'Comfort', 'name' => 'Beach hotel, Matemwe']],
                'stay_image' => $img('1520250497591-112f2f40a3f4'), 'stay_caption' => 'Beach hotel',
            ],
            [
                'title' => 'Mnemba Atoll snorkelling',
                'image' => $img('1544551763-46a013bb70d5'), 'caption' => 'Mnemba Atoll',
                'text' => 'A morning boat trip to Mnemba Atoll for two snorkelling stops over coral gardens. Afternoon at leisure.',
                'stay' => 'North coast beach', 'meals' => 'Half board',
                'options' => [['level' => 'Comfort', 'name' => 'Beach hotel, Matemwe']],
            ],
            [
                'title' => 'Try diving or a certified dive',
                'image' => $img('1544551763-46a013bb70d5'), 'caption' => 'Mnemba Atoll',
                'text' => 'Beginners learn the basics in the pool and then dive on a shallow reef with an instructor; certified divers join two guided dives.',
                'activity' => ['title' => 'Discover Scuba Diving (Optional)', 'text' => 'A supervised first dive, no experience needed. Booked and paid with the dive centre.'],
                'stay' => 'North coast beach', 'meals' => 'Half board',
                'options' => [['level' => 'Comfort', 'name' => 'Beach hotel, Matemwe']],
            ],
            [
                'title' => 'Dolphins and turtles',
                'image' => $img('1507525428034-b723cf961d3e'), 'caption' => 'Nungwi',
                'text' => 'Visit the turtle sanctuary in Nungwi and take a boat out to look for dolphins, with a swim at a quiet beach before returning.',
                'stay' => 'North coast beach', 'meals' => 'Half board',
                'options' => [['level' => 'Comfort', 'name' => 'Beach hotel, Matemwe']],
            ],
            [
                'title' => 'Departure',
                'image' => $img('1520250497591-112f2f40a3f4'), 'caption' => 'Zanzibar',
                'text' => 'Transfer to Zanzibar Airport. Leave at least 24 hours between your last dive and your flight.',
                'meals' => 'Breakfast',
            ],
        ],
        'included' => [...$zanzibarIncluded, 'Mnemba snorkelling trip with equipment', 'Dolphin and turtle excursion', 'Mnemba marine conservation fee'],
        'excluded' => [...$zanzibarExcluded, 'Scuba diving (booked with the dive centre)'],
    ],

    // 10 ────────────────────────────────────────────────────────────────────
    [
        'slug' => 'zanzibar-family-beach-7-days',
        'title' => 'Zanzibar Family Beach Holiday',
        'category' => 'Zanzibar',
        'style' => 'mid-range',
        'types' => ['family', 'zanzibar'],
        'tagline' => 'Easy days by the ocean for all ages',
        'chips' => [['label' => '7 Days'], ['label' => 'All ages'], ['label' => 'Family rooms', 'highlight' => true]],
        'text' => 'A family-friendly resort on a calm beach, short excursions children love: giant tortoises, a spice farm, Jozani\'s monkeys and a dhow trip, with plenty of pool time.',
        'image' => $img('1507525428034-b723cf961d3e'),
        'location' => 'Zanzibar, Tanzania',
        'overview' => [
            'We choose a resort with family rooms, a good pool and a beach that suits children at different tides. Excursions are short and spread out, so nobody gets tired.',
            'Easy to combine with our family safaris for a complete bush-and-beach holiday.',
        ],
        'facts' => ['Duration' => '7 Days · 6 Nights', 'Group size' => 'Private', 'Difficulty' => 'Easy', 'Best time' => 'June – October, December – February'],
        'itinerary' => [
            [
                'title' => 'Arrival and family resort',
                'image' => $img('1520250497591-112f2f40a3f4'), 'caption' => 'Zanzibar',
                'text' => 'Transfer from Zanzibar Airport to a family-friendly resort on the east coast. Time for a first swim.',
                'stay' => 'East coast', 'meals' => 'Half board',
                'options' => [['level' => 'Comfort', 'name' => 'Family resort, east coast']],
                'stay_image' => $img('1566073771259-6a8506099945'), 'stay_caption' => 'Family resort',
            ],
            [
                'title' => 'Beach and pool day',
                'image' => $img('1507525428034-b723cf961d3e'), 'caption' => 'East coast',
                'text' => 'A relaxed day at the resort: sandcastles, rock pools and the kids\' club.',
                'stay' => 'East coast', 'meals' => 'Half board',
                'options' => [['level' => 'Comfort', 'name' => 'Family resort, east coast']],
            ],
            [
                'title' => 'Jozani Forest and its red colobus monkeys',
                'image' => $img('1447752875215-b2761acb3c5d'), 'caption' => 'Jozani Forest',
                'text' => 'A short morning walk in Jozani Forest to see the playful red colobus monkeys and the mangrove boardwalk, back for lunch.',
                'stay' => 'East coast', 'meals' => 'Half board',
                'options' => [['level' => 'Comfort', 'name' => 'Family resort, east coast']],
            ],
            [
                'title' => 'Free day',
                'image' => $img('1520250497591-112f2f40a3f4'), 'caption' => 'Paje',
                'text' => 'Another easy day, with optional kite-watching on Paje beach or a gentle snorkelling trip.',
                'stay' => 'East coast', 'meals' => 'Half board',
                'options' => [['level' => 'Comfort', 'name' => 'Family resort, east coast']],
            ],
            [
                'title' => 'Spice farm and Prison Island',
                'image' => $img('1571896349842-33c89424de2d'), 'caption' => 'Prison Island',
                'text' => 'Taste and smell spices on a farm tour, then take a boat to Prison Island to meet giant tortoises, some more than a century old.',
                'stay' => 'East coast', 'meals' => 'Half board',
                'options' => [['level' => 'Comfort', 'name' => 'Family resort, east coast']],
            ],
            [
                'title' => 'Sunset dhow trip',
                'image' => $img('1507525428034-b723cf961d3e'), 'caption' => 'Zanzibar',
                'text' => 'A last beach day and an early-evening dhow sail for the whole family.',
                'stay' => 'East coast', 'meals' => 'Half board',
                'options' => [['level' => 'Comfort', 'name' => 'Family resort, east coast']],
            ],
            [
                'title' => 'Departure',
                'image' => $img('1520250497591-112f2f40a3f4'), 'caption' => 'Zanzibar',
                'text' => 'Breakfast and the transfer to Zanzibar Airport.',
                'meals' => 'Breakfast',
            ],
        ],
        'included' => [...$zanzibarIncluded, 'Family room or connecting rooms', 'Jozani Forest, spice farm, Prison Island and dhow trip'],
        'excluded' => $zanzibarExcluded,
    ],

];
