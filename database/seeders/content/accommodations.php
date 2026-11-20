<?php

/*
|--------------------------------------------------------------------------
| Starter stays for the Accommodation page (/accommodations)
|--------------------------------------------------------------------------
|
| Loaded once by AccommodationSeeder (only into an empty table). After that,
| manage stays in the admin: Website Content → Accommodations.
|
| SAMPLE CONTENT: generic placeholder names. Replace them with the real lodges
| and camps you book, and use photos you have the rights to.
|
*/

return [
    [
        'name' => 'Central Serengeti Tented Camp',
        'location' => 'Serengeti',
        'type' => 'Tented camp',
        'level' => 'Mid-range',
        'text' => 'Spacious canvas tents with en-suite bathrooms, close to the big cat territory of the central plains.',
        'image' => 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=900&q=75',
        'gallery' => ['https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=1600&q=75', 'https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?auto=format&fit=crop&w=1600&q=75', 'https://images.unsplash.com/photo-1535941339077-2dd1c7963098?auto=format&fit=crop&w=1600&q=75'],
        'facilities' => ['En-suite bathrooms', 'Restaurant', 'Campfire', 'Views of the park', 'Laundry'],
    ],
    [
        'name' => 'Serengeti Luxury Lodge',
        'location' => 'Serengeti',
        'type' => 'Lodge',
        'level' => 'Luxury',
        'text' => 'Elegant suites, a pool overlooking the plains and fine dining after long days out on game drives.',
        'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=900&q=75',
        'gallery' => ['https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?auto=format&fit=crop&w=1600&q=75', 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=1600&q=75', 'https://images.unsplash.com/photo-1507608616759-54f48f0af0ee?auto=format&fit=crop&w=1600&q=75'],
        'facilities' => ['Swimming pool', 'Restaurant', 'Bar', 'Spa', 'Wi-Fi', 'Views of the park'],
    ],
    [
        'name' => 'Ngorongoro Crater Rim Lodge',
        'location' => 'Ngorongoro',
        'type' => 'Lodge',
        'level' => 'Luxury',
        'text' => 'Wake above the clouds with sweeping crater views and an easy early start for the drive down to the crater floor.',
        'image' => 'https://images.unsplash.com/photo-1500835556837-99ac94a94552?auto=format&fit=crop&w=900&q=75',
        'gallery' => ['https://images.unsplash.com/photo-1523805009345-7448845a9e53?auto=format&fit=crop&w=1600&q=75', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1600&q=75', 'https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?auto=format&fit=crop&w=1600&q=75'],
        'facilities' => ['Restaurant', 'Bar', 'Wi-Fi', 'Views of the park', 'Family rooms'],
    ],
    [
        'name' => 'Ngorongoro Highlands Camp',
        'location' => 'Ngorongoro',
        'type' => 'Tented camp',
        'level' => 'Mid-range',
        'text' => 'A cosy highland camp with campfires on cool evenings, well placed for crater days and Olduvai Gorge.',
        'image' => 'https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?auto=format&fit=crop&w=900&q=75',
        'gallery' => ['https://images.unsplash.com/photo-1500835556837-99ac94a94552?auto=format&fit=crop&w=1600&q=75', 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=1600&q=75', 'https://images.unsplash.com/photo-1447752875215-b2761acb3c5d?auto=format&fit=crop&w=1600&q=75'],
        'facilities' => ['En-suite bathrooms', 'Restaurant', 'Campfire', 'Laundry'],
    ],
    [
        'name' => 'Karatu Coffee Farm Lodge',
        'location' => 'Karatu',
        'type' => 'Farm lodge',
        'level' => 'Mid-range',
        'text' => 'Garden cottages on a working coffee farm between Lake Manyara and Ngorongoro, with walking trails on site.',
        'image' => 'https://images.unsplash.com/photo-1447752875215-b2761acb3c5d?auto=format&fit=crop&w=900&q=75',
        'gallery' => ['https://images.unsplash.com/photo-1585970480901-90d6bb2a48b5?auto=format&fit=crop&w=1600&q=75', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1600&q=75', 'https://images.unsplash.com/photo-1432405972618-c60b0225b8f9?auto=format&fit=crop&w=1600&q=75'],
        'facilities' => ['Swimming pool', 'Restaurant', 'Bar', 'Wi-Fi', 'Family rooms'],
    ],
    [
        'name' => 'Zanzibar Beachfront Resort',
        'location' => 'Zanzibar',
        'type' => 'Beach resort',
        'level' => 'Luxury',
        'text' => 'Ocean-view villas, an infinity pool and white sand steps from your door: the perfect end to a safari.',
        'image' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=900&q=75',
        'gallery' => ['https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1600&q=75', 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1600&q=75', 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=1600&q=75'],
        'facilities' => ['Beachfront', 'Swimming pool', 'Spa', 'Restaurant', 'Bar', 'Wi-Fi', 'Airport transfers'],
    ],
    [
        'name' => 'Stone Town Boutique Hotel',
        'location' => 'Zanzibar',
        'type' => 'Boutique hotel',
        'level' => 'Mid-range',
        'text' => 'Carved doors, rooftop views and character rooms in the heart of Stone Town\'s historic alleys.',
        'image' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=900&q=75',
        'gallery' => ['https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1600&q=75', 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=1600&q=75', 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1600&q=75'],
        'facilities' => ['Restaurant', 'Wi-Fi', 'Airport transfers', 'Laundry'],
    ],
];
