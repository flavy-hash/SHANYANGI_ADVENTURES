<?php

/*
|--------------------------------------------------------------------------
| Packages
|--------------------------------------------------------------------------
|
| Packages themselves are managed in the admin panel (/admin → Packages).
| This file only holds settings shared by the website and the admin.
|
| currency: shown before prices.
| types:    filter tabs on the Safari page (?type=...), in display order.
|
*/

return [

    'currency' => '$',

    // Style filter on the Safari page (/safaris?style=...), set per package in the admin.
    'styles' => [
        'budget' => 'Budget',
        'classic' => 'Classic',
        'mid-range' => 'Mid-range',
        'luxury' => 'Luxury',
        'high-end' => 'High end',
    ],

    // Destination filter on the Safari page (/safaris?destination=...). A package belongs
    // to a destination when its category or trip types match, or when its itinerary
    // route passes through one of the listed places (see config/places.php names).
    'destinations' => [
        'safaris' => ['label' => 'Safaris', 'categories' => ['Safari', 'Safari & Beach'], 'types' => ['migration'], 'places' => ['Serengeti', 'Ngorongoro', 'Tarangire', 'Manyara', 'Ndutu', 'Natron', 'Mikumi', 'Nyerere', 'Ruaha']],
        'northern-circuit' => ['label' => 'Northern Circuit', 'categories' => [], 'types' => [], 'places' => ['Serengeti', 'Ngorongoro', 'Tarangire', 'Manyara', 'Ndutu', 'Karatu']],
        'kilimanjaro' => ['label' => 'Kilimanjaro', 'categories' => ['Kilimanjaro', 'Mount Meru'],'types' => ['kilimanjaro'], 'places' => []],
        'zanzibar' => ['label' => 'Zanzibar', 'categories' => ['Zanzibar'], 'types' => ['zanzibar'], 'places' => ['Zanzibar']],
    ],

    // Filter tabs on the Safari page, in display order.
    'types' => [
        'honeymoon' => 'Honeymoon',
        'family' => 'Family',
        'luxury' => 'Luxury',
        'migration' => 'Migration',
        'kilimanjaro' => 'Kilimanjaro',
        'zanzibar' => 'Zanzibar',
    ],

];
