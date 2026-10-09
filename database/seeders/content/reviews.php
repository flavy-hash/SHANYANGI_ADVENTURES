<?php

// Starter website content, loaded into the database by ContentSeeder (php artisan db:seed).
// After seeding, manage this content in the admin panel (/admin).

return [
    [
        'sample' => true,
        'name' => 'Sample Guest',
        'country' => 'United Kingdom',
        'date' => '2025-07-10',
        'rating' => 5,
        'title' => 'Serengeti Safari',
        'text' => 'Sample review text. Replace this with a real guest review describing their safari: the wildlife they saw, how the guide looked after them, the lodges and the overall experience of travelling with your team.',
        'package' => 'Serengeti & Ngorongoro Safari',
        'images' => [
            'https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?auto=format&fit=crop&w=600&q=70',
            'https://images.unsplash.com/photo-1535941339077-2dd1c7963098?auto=format&fit=crop&w=600&q=70',
        ],
    ],
    [
        'sample' => true,
        'name' => 'Sample Guest',
        'country' => 'Germany',
        'date' => '2026-01-15',
        'rating' => 5,
        'title' => 'Climbing Mount Kilimanjaro',
        'text' => 'Sample review text. Replace this with a real guest review of their climb: the route, the guides and porters, the camps, how acclimatisation went and what reaching the summit felt like.',
        'package' => 'Machame Route',
        'images' => [
            'https://images.unsplash.com/photo-1489392191049-fc10c97e64b6?auto=format&fit=crop&w=600&q=70',
            'https://images.unsplash.com/photo-1621414050946-1b936a78491f?auto=format&fit=crop&w=600&q=70',
        ],
    ],
    [
        'sample' => true,
        'name' => 'Sample Guest',
        'country' => 'Argentina',
        'date' => '2026-08-02',
        'rating' => 5,
        'title' => 'Beach & Stone Town',
        'text' => 'Sample review text. Replace this with a real guest review of their Zanzibar stay: the beaches, Stone Town, the excursions and how the trip was organised from start to finish.',
        'package' => 'Zanzibar Beach Escape',
        'images' => [
            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=600&q=70',
            'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=600&q=70',
        ],
    ],
];
