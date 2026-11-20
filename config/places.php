<?php

/*
|--------------------------------------------------------------------------
| Places for the itinerary route maps (package pages)
|--------------------------------------------------------------------------
|
| Each itinerary day is pinned on the map at one of these places. In the admin
| (Packages → Itinerary → Map location) you can pick one per day; days left on
| "Automatic" are matched from the day title, photo caption or accommodation,
| using the words in 'match' (the place mentioned last wins, so "Serengeti to
| Ngorongoro" is pinned at Ngorongoro). Coordinates are approximate.
|
| Add a place: 'key' => ['name' => ..., 'lat' => ..., 'lng' => ..., 'match' => [...]]
|
*/

return [

    // Arusha & Kilimanjaro region
    'arusha' => ['name' => 'Arusha', 'lat' => -3.3869, 'lng' => 36.6830, 'match' => ['arusha']],
    'kilimanjaro-airport' => ['name' => 'Kilimanjaro Airport', 'lat' => -3.4294, 'lng' => 37.0745, 'match' => ['kilimanjaro airport', 'kilimanjaro international', 'jro']],
    'moshi' => ['name' => 'Moshi', 'lat' => -3.3349, 'lng' => 37.3404, 'match' => ['moshi']],
    'arusha-national-park' => ['name' => 'Arusha National Park', 'lat' => -3.2400, 'lng' => 36.8700, 'match' => ['arusha national park', 'momella', 'mount meru']],

    // Northern safari circuit
    'tarangire' => ['name' => 'Tarangire National Park', 'lat' => -3.9000, 'lng' => 36.0000, 'match' => ['tarangire']],
    'lake-manyara' => ['name' => 'Lake Manyara', 'lat' => -3.5300, 'lng' => 35.8300, 'match' => ['lake manyara', 'manyara']],
    'mto-wa-mbu' => ['name' => 'Mto wa Mbu', 'lat' => -3.3700, 'lng' => 35.8500, 'match' => ['mto wa mbu']],
    'karatu' => ['name' => 'Karatu', 'lat' => -3.3378, 'lng' => 35.6744, 'match' => ['karatu']],
    'ngorongoro' => ['name' => 'Ngorongoro Crater', 'lat' => -3.1750, 'lng' => 35.5880, 'match' => ['ngorongoro', 'crater rim', 'crater floor', 'the crater']],
    'serengeti' => ['name' => 'Serengeti National Park', 'lat' => -2.4500, 'lng' => 34.8300, 'match' => ['serengeti', 'seronera', 'central serengeti']],
    'northern-serengeti' => ['name' => 'Northern Serengeti', 'lat' => -1.5800, 'lng' => 34.9300, 'match' => ['northern serengeti', 'north serengeti', 'mara river', 'kogatende']],
    'ndutu' => ['name' => 'Ndutu', 'lat' => -3.0100, 'lng' => 34.9900, 'match' => ['ndutu', 'southern serengeti']],
    'lake-natron' => ['name' => 'Lake Natron', 'lat' => -2.4200, 'lng' => 36.0000, 'match' => ['lake natron', 'natron']],
    'lake-eyasi' => ['name' => 'Lake Eyasi', 'lat' => -3.6000, 'lng' => 35.1000, 'match' => ['lake eyasi', 'eyasi']],

    // Mount Kilimanjaro (routes & camps)
    'londorossi-gate' => ['name' => 'Londorossi Gate', 'lat' => -2.9967, 'lng' => 37.1167, 'match' => ['londorossi']],
    'lemosho-gate' => ['name' => 'Lemosho Gate', 'lat' => -3.0347, 'lng' => 37.1150, 'match' => ['lemosho gate', 'lemosho trailhead']],
    'mti-mkubwa' => ['name' => 'Mti Mkubwa Camp', 'lat' => -3.0400, 'lng' => 37.1500, 'match' => ['mti mkubwa', 'big tree camp']],
    'shira-1' => ['name' => 'Shira 1 Camp', 'lat' => -3.0300, 'lng' => 37.2100, 'match' => ['shira 1']],
    'shira-2' => ['name' => 'Shira 2 Camp', 'lat' => -3.0550, 'lng' => 37.2550, 'match' => ['shira 2', 'shira plateau']],
    'machame-gate' => ['name' => 'Machame Gate', 'lat' => -3.1736, 'lng' => 37.2442, 'match' => ['machame gate']],
    'machame-camp' => ['name' => 'Machame Camp', 'lat' => -3.1030, 'lng' => 37.2593, 'match' => ['machame camp']],
    'shira-cave' => ['name' => 'Shira Cave Camp', 'lat' => -3.0650, 'lng' => 37.2450, 'match' => ['shira cave', 'shira camp', 'shira ridge', 'shira']],
    'lava-tower' => ['name' => 'Lava Tower', 'lat' => -3.0750, 'lng' => 37.3170, 'match' => ['lava tower']],
    'barranco' => ['name' => 'Barranco Camp', 'lat' => -3.0930, 'lng' => 37.3380, 'match' => ['barranco']],
    'karanga' => ['name' => 'Karanga Camp', 'lat' => -3.1000, 'lng' => 37.3720, 'match' => ['karanga']],
    'barafu' => ['name' => 'Barafu Camp', 'lat' => -3.0870, 'lng' => 37.3870, 'match' => ['barafu']],
    'uhuru-peak' => ['name' => 'Uhuru Peak', 'lat' => -3.0674, 'lng' => 37.3556, 'match' => ['uhuru', 'summit']],
    'mweka-camp' => ['name' => 'Mweka Camp', 'lat' => -3.1475, 'lng' => 37.3700, 'match' => ['mweka camp']],
    'mweka-gate' => ['name' => 'Mweka Gate', 'lat' => -3.2264, 'lng' => 37.3712, 'match' => ['mweka gate', 'mweka']],
    'marangu-gate' => ['name' => 'Marangu Gate', 'lat' => -3.2440, 'lng' => 37.5170, 'match' => ['marangu gate', 'marangu']],
    'mandara-hut' => ['name' => 'Mandara Hut', 'lat' => -3.2030, 'lng' => 37.5260, 'match' => ['mandara']],
    'horombo-hut' => ['name' => 'Horombo Hut', 'lat' => -3.1450, 'lng' => 37.4800, 'match' => ['horombo']],
    'kibo-hut' => ['name' => 'Kibo Hut', 'lat' => -3.0960, 'lng' => 37.4300, 'match' => ['kibo hut']],
    'rongai-gate' => ['name' => 'Rongai Gate (Nalemoru)', 'lat' => -2.9900, 'lng' => 37.5200, 'match' => ['rongai gate', 'nalemoru']],
    'simba-camp' => ['name' => 'Simba Camp', 'lat' => -3.0080, 'lng' => 37.4850, 'match' => ['simba camp', 'first cave']],
    'kikelewa' => ['name' => 'Kikelewa Caves', 'lat' => -3.0430, 'lng' => 37.4540, 'match' => ['kikelewa']],
    'mawenzi-tarn' => ['name' => 'Mawenzi Tarn', 'lat' => -3.0590, 'lng' => 37.4470, 'match' => ['mawenzi tarn']],
    'school-hut' => ['name' => 'School Hut', 'lat' => -3.0420, 'lng' => 37.4040, 'match' => ['school hut']],
    'moir-hut' => ['name' => 'Moir Hut', 'lat' => -3.0280, 'lng' => 37.2950, 'match' => ['moir']],
    'buffalo-camp' => ['name' => 'Buffalo Camp', 'lat' => -3.0110, 'lng' => 37.3470, 'match' => ['buffalo camp']],
    'third-cave' => ['name' => 'Third Cave Camp', 'lat' => -3.0250, 'lng' => 37.4120, 'match' => ['third cave']],
    'umbwe-gate' => ['name' => 'Umbwe Gate', 'lat' => -3.2000, 'lng' => 37.2850, 'match' => ['umbwe gate']],
    'umbwe-cave' => ['name' => 'Umbwe Cave Camp', 'lat' => -3.1330, 'lng' => 37.3080, 'match' => ['umbwe cave']],

    // Mount Meru
    'momella-gate' => ['name' => 'Momella Gate', 'lat' => -3.2210, 'lng' => 36.8590, 'match' => ['momella gate']],
    'miriakamba-hut' => ['name' => 'Miriakamba Hut', 'lat' => -3.2370, 'lng' => 36.8150, 'match' => ['miriakamba']],
    'saddle-hut' => ['name' => 'Saddle Hut', 'lat' => -3.2340, 'lng' => 36.7820, 'match' => ['saddle hut', 'little meru']],
    'socialist-peak' => ['name' => 'Socialist Peak, Mount Meru', 'lat' => -3.2467, 'lng' => 36.7483, 'match' => ['socialist peak']],

    // Southern Tanzania & the coast
    'dar-es-salaam' => ['name' => 'Dar es Salaam', 'lat' => -6.7924, 'lng' => 39.2083, 'match' => ['dar es salaam', 'dar']],
    'mikumi' => ['name' => 'Mikumi National Park', 'lat' => -7.4000, 'lng' => 37.0000, 'match' => ['mikumi']],
    'nyerere' => ['name' => 'Nyerere National Park', 'lat' => -7.8000, 'lng' => 38.2000, 'match' => ['nyerere', 'selous', 'rufiji']],
    'ruaha' => ['name' => 'Ruaha National Park', 'lat' => -7.6700, 'lng' => 34.9200, 'match' => ['ruaha']],

    // Zanzibar
    'stone-town' => ['name' => 'Stone Town, Zanzibar', 'lat' => -6.1622, 'lng' => 39.1921, 'match' => ['stone town', 'spice farm', 'spice tour']],
    'zanzibar-north' => ['name' => 'Nungwi, Zanzibar', 'lat' => -5.7266, 'lng' => 39.2964, 'match' => ['north coast', 'nungwi', 'kendwa', 'zanzibar beach', 'zanzibar']],
    'zanzibar-east' => ['name' => 'Paje, Zanzibar', 'lat' => -6.2670, 'lng' => 39.5340, 'match' => ['east coast', 'paje', 'jambiani', 'michamvi']],
    'zanzibar-airport' => ['name' => 'Zanzibar Airport', 'lat' => -6.2220, 'lng' => 39.2249, 'match' => ['zanzibar airport']],
    'prison-island' => ['name' => 'Prison Island, Zanzibar', 'lat' => -6.1180, 'lng' => 39.1680, 'match' => ['prison island', 'changuu']],
    'jozani' => ['name' => 'Jozani Forest, Zanzibar', 'lat' => -6.2640, 'lng' => 39.4100, 'match' => ['jozani']],
    'mnemba' => ['name' => 'Mnemba Atoll, Zanzibar', 'lat' => -5.8200, 'lng' => 39.3800, 'match' => ['mnemba']],

];
