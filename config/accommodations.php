<?php

/*
|--------------------------------------------------------------------------
| Accommodation page
|--------------------------------------------------------------------------
|
| SAMPLE CONTENT: the names below are generic placeholders. Replace them with
| the real lodges and camps you book, and use photos you have rights to.
| location: one of the keys in 'locations' (used by ?location= filters).
| image:    a full URL, or a file under storage/app/private/media/images.
|
*/

return [

    'locations' => [
        'serengeti' => 'Serengeti',
        'ngorongoro' => 'Ngorongoro',
        'karatu' => 'Karatu',
        'zanzibar' => 'Zanzibar',
    ],

    'items' => [
        [
            'name' => 'Central Serengeti Tented Camp',
            'location' => 'serengeti',
            'type' => 'Tented camp',
            'level' => 'Mid-range',
            'text' => 'Spacious canvas tents with en-suite bathrooms, close to the big cat territory of the central plains.',
            'image' => 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=900&q=75',
        ],
        [
            'name' => 'Serengeti Luxury Lodge',
            'location' => 'serengeti',
            'type' => 'Lodge',
            'level' => 'Luxury',
            'text' => 'Elegant suites, a pool overlooking the plains and fine dining after long days out on game drives.',
            'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=900&q=75',
        ],
        [
            'name' => 'Ngorongoro Crater Rim Lodge',
            'location' => 'ngorongoro',
            'type' => 'Lodge',
            'level' => 'Luxury',
            'text' => 'Wake above the clouds with sweeping crater views and an easy early start for the drive down to the crater floor.',
            'image' => 'https://images.unsplash.com/photo-1500835556837-99ac94a94552?auto=format&fit=crop&w=900&q=75',
        ],
        [
            'name' => 'Ngorongoro Highlands Camp',
            'location' => 'ngorongoro',
            'type' => 'Tented camp',
            'level' => 'Mid-range',
            'text' => 'A cosy highland camp with campfires on cool evenings, well placed for crater days and Olduvai Gorge.',
            'image' => 'https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?auto=format&fit=crop&w=900&q=75',
        ],
        [
            'name' => 'Karatu Coffee Farm Lodge',
            'location' => 'karatu',
            'type' => 'Farm lodge',
            'level' => 'Mid-range',
            'text' => 'Garden cottages on a working coffee farm between Lake Manyara and Ngorongoro, with walking trails on site.',
            'image' => 'https://images.unsplash.com/photo-1447752875215-b2761acb3c5d?auto=format&fit=crop&w=900&q=75',
        ],
        [
            'name' => 'Zanzibar Beachfront Resort',
            'location' => 'zanzibar',
            'type' => 'Beach resort',
            'level' => 'Luxury',
            'text' => 'Ocean-view villas, an infinity pool and white sand steps from your door: the perfect end to a safari.',
            'image' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=900&q=75',
        ],
        [
            'name' => 'Stone Town Boutique Hotel',
            'location' => 'zanzibar',
            'type' => 'Boutique hotel',
            'level' => 'Mid-range',
            'text' => 'Carved doors, rooftop views and character rooms in the heart of Stone Town\'s historic alleys.',
            'image' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=900&q=75',
        ],
    ],

];
