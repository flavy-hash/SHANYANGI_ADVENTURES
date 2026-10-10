<?php

/*
|--------------------------------------------------------------------------
| Site Navigation
|--------------------------------------------------------------------------
|
| Primary menu shown in the header. Items with "children" become dropdowns:
| a hover mega panel on desktop (links + "mega" copy and image) and a tap
| accordion in the mobile drawer. A child with its own title/text/cta/image
| swaps the panel's preview while hovered; others show the parent's "mega".
| Paths are relative to APP_URL.
|
*/

return [

    'primary' => [
      
        [
            'label' => 'Safari',
            'path' => '/safaris',
            'children' => [
                ['label' => 'All Safaris', 'path' => '/safaris'],
                [
                    'label' => 'Honeymoon', 'path' => '/safaris?type=honeymoon',
                    'title' => 'Honeymoon Safaris',
                    'text' => 'Private game drives, sundowners for two and intimate tented camps, then barefoot days on Zanzibar\'s beaches.',
                    'cta' => 'View Honeymoon Safaris',
                    'image' => 'https://images.unsplash.com/photo-1547471080-7cc2caa01a7e?auto=format&fit=crop&w=800&q=75',
                ],
                [
                    'label' => 'Family', 'path' => '/safaris?type=family',
                    'title' => 'Family Safaris',
                    'text' => 'Shorter drives, family-friendly lodges and guides who turn every sighting into an adventure for all ages.',
                    'cta' => 'View Family Safaris',
                    'image' => 'https://images.unsplash.com/photo-1585970480901-90d6bb2a48b5?auto=format&fit=crop&w=800&q=75',
                ],
                [
                    'label' => 'Luxury', 'path' => '/safaris?type=luxury',
                    'title' => 'Luxury Safaris',
                    'text' => 'Exclusive lodges, private vehicles and fly-in options between the parks for a seamless, high-end journey.',
                    'cta' => 'View Luxury Safaris',
                    'image' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=800&q=75',
                ],
                [
                    'label' => 'Migration', 'path' => '/safaris?type=migration',
                    'title' => 'Great Migration Safaris',
                    'text' => 'Follow the wildebeest herds across the Serengeti, timed around the river crossings and calving season.',
                    'cta' => 'View Migration Safaris',
                    'image' => 'https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?auto=format&fit=crop&w=800&q=75',
                ],
            ],
            'mega' => [
                'title' => 'Tanzania Safari Adventures',
                'text' => 'Serengeti, Ngorongoro, Tarangire and Lake Manyara with local guides who plan every route around where the wildlife is right now.',
                'cta' => 'Explore Safaris',
                'image' => 'https://images.unsplash.com/photo-1535941339077-2dd1c7963098?auto=format&fit=crop&w=800&q=75',
            ],
        ],
        [
            'label' => 'Activities',
            'path' => '/activities',
            'children' => [
                ['label' => 'All Activities', 'path' => '/activities'],
                [
                    'label' => 'Arusha', 'path' => '/activities?location=arusha',
                    'title' => 'Arusha Day Experiences',
                    'text' => 'Walking safaris in Arusha National Park, canoeing on the Momella Lakes and views of Mount Meru.',
                    'cta' => 'Explore Arusha',
                    'image' => 'https://images.unsplash.com/photo-1549366021-9f761d450615?auto=format&fit=crop&w=800&q=75',
                ],
                [
                    'label' => 'Materuni', 'path' => '/activities?location=materuni',
                    'title' => 'Materuni Waterfalls & Coffee',
                    'text' => 'Hike to a towering waterfall on the slopes of Kilimanjaro, then roast and taste coffee with a local family.',
                    'cta' => 'Explore Materuni',
                    'image' => 'https://images.unsplash.com/photo-1432405972618-c60b0225b8f9?auto=format&fit=crop&w=800&q=75',
                ],
                [
                    'label' => 'Mto wa Mbu', 'path' => '/activities?location=mto-wa-mbu',
                    'title' => 'Mto wa Mbu Cultural Tour',
                    'text' => 'Cycle through banana farms and rice fields, meet local artisans and taste traditional food near Lake Manyara.',
                    'cta' => 'Explore Mto wa Mbu',
                    'image' => 'https://images.unsplash.com/photo-1447752875215-b2761acb3c5d?auto=format&fit=crop&w=800&q=75',
                ],
                [
                    'label' => 'Zanzibar', 'path' => '/activities?location=zanzibar',
                    'title' => 'Zanzibar Experiences',
                    'text' => 'Stone Town walks, spice farm tours, dhow cruises and snorkelling in the Indian Ocean.',
                    'cta' => 'Explore Zanzibar',
                    'image' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=800&q=75',
                ],
            ],
            'mega' => [
                'title' => 'Experiences Beyond the Game Drive',
                'text' => 'Balloon flights, waterfall hikes, coffee farms and cultural village visits that add depth to any Tanzania itinerary.',
                'cta' => 'Explore Activities',
                'image' => 'https://images.unsplash.com/photo-1504432842672-1a79f78e4084?auto=format&fit=crop&w=800&q=75',
            ],
        ],
        [
            'label' => 'Accommodation',
            'path' => '/accommodations',
            'children' => [
                ['label' => 'All Stays', 'path' => '/accommodations'],
                [
                    'label' => 'Serengeti', 'path' => '/accommodations?location=serengeti',
                    'title' => 'Serengeti Lodges & Camps',
                    'text' => 'Tented camps and lodges placed to follow the herds through the seasons, right in the heart of the plains.',
                    'cta' => 'Stays in Serengeti',
                    'image' => 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=800&q=75',
                ],
                [
                    'label' => 'Ngorongoro', 'path' => '/accommodations?location=ngorongoro',
                    'title' => 'Ngorongoro Crater Stays',
                    'text' => 'Rim-top lodges with sweeping crater views and an easy early start for the descent to the crater floor.',
                    'cta' => 'Stays in Ngorongoro',
                    'image' => 'https://images.unsplash.com/photo-1500835556837-99ac94a94552?auto=format&fit=crop&w=800&q=75',
                ],
                [
                    'label' => 'Karatu', 'path' => '/accommodations?location=karatu',
                    'title' => 'Karatu Farm Lodges',
                    'text' => 'Peaceful farm lodges between Lake Manyara and Ngorongoro, an ideal base for crater days.',
                    'cta' => 'Stays in Karatu',
                    'image' => 'https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?auto=format&fit=crop&w=800&q=75',
                ],
                [
                    'label' => 'Zanzibar', 'path' => '/accommodations?location=zanzibar',
                    'title' => 'Zanzibar Beach Stays',
                    'text' => 'Boutique hotels in Stone Town and beachfront resorts on white sand, perfect after your safari.',
                    'cta' => 'Stays in Zanzibar',
                    'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=75',
                ],
            ],
            'mega' => [
                'title' => 'Lodges, Camps & Beach Stays',
                'text' => 'Hand-picked safari lodges and tented camps for every comfort level, close to the parks on your route.',
                'cta' => 'Browse Stays',
                'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=75',
            ],
        ],
        [
            'label' => 'Blog',
            'path' => '/blog',
            'children' => [
                ['label' => 'All Articles', 'path' => '/blog'],
                [
                    'label' => 'Safari', 'path' => '/blog/category/safari',
                    'title' => 'Safari Guides & Tips',
                    'text' => 'When to go, what to pack, park-by-park advice and what to expect on your first game drive.',
                    'cta' => 'Read Safari Articles',
                    'image' => 'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=800&q=75',
                ],
                [
                    'label' => 'Climbing', 'path' => '/blog/category/climbing',
                    'title' => 'Kilimanjaro Climbing Advice',
                    'text' => 'Route comparisons, acclimatisation, gear lists and training tips for reaching Uhuru Peak.',
                    'cta' => 'Read Climbing Articles',
                    'image' => 'https://images.unsplash.com/photo-1621414050946-1b936a78491f?auto=format&fit=crop&w=800&q=75',
                ],
                [
                    'label' => 'Travel Tips', 'path' => '/blog/category/travel-tips',
                    'title' => 'Tanzania Travel Tips',
                    'text' => 'Visas, vaccinations, money, seasons and local etiquette: everything to know before you fly.',
                    'cta' => 'Read Travel Tips',
                    'image' => 'https://images.unsplash.com/photo-1503220317375-aaad61436b1b?auto=format&fit=crop&w=800&q=75',
                ],
            ],
            'mega' => [
                'title' => 'Stories & Planning Guides',
                'text' => 'Seasons, packing lists, park guides and honest advice from the people who drive these roads every week.',
                'cta' => 'Read the Blog',
                'image' => 'https://images.unsplash.com/photo-1489392191049-fc10c97e64b6?auto=format&fit=crop&w=800&q=75',
            ],
        ],
        [
            'label' => 'About Us',
            'path' => '/about-us',
            'children' => [
                ['label' => 'Why Travel With Us', 'path' => '/about-us'],
                [
                    'label' => 'Our Story', 'path' => '/about-us#story',
                    'title' => 'Our Story',
                    'text' => 'Where we come from, what drives us, and why we still plan every journey personally.',
                    'cta' => 'Read Our Story',
                    'image' => 'https://images.unsplash.com/photo-1609198092458-38a293c7ac4b?auto=format&fit=crop&w=800&q=75',
                ],
                [
                    'label' => 'Our Team', 'path' => '/about-us#team',
                    'title' => 'Meet Our Team',
                    'text' => 'The guides, drivers and planners who look after you from airport pickup to your final sundowner.',
                    'cta' => 'Meet the Team',
                    'image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=800&q=75',
                ],
                [
                    'label' => 'Reviews', 'path' => '/reviews',
                    'title' => 'Traveller Reviews',
                    'text' => 'Read what guests say about their safaris, climbs and beach escapes with us.',
                    'cta' => 'Read Reviews',
                    'image' => 'https://images.unsplash.com/photo-1530789253388-582c481c54b0?auto=format&fit=crop&w=800&q=75',
                ],
            ],
            'mega' => [
                'title' => 'We are Shanyangi Adventures',
                'text' => 'A locally owned team of guides, planners and drivers who put every journey together personally.',
                'cta' => 'Meet the Team',
                'image' => 'https://images.unsplash.com/photo-1523805009345-7448845a9e53?auto=format&fit=crop&w=800&q=75',
            ],
        ],
        [
            'label' => 'Contacts',
            'path' => '/contact-us',
            'children' => [
                ['label' => 'Contact Details', 'path' => '/contact-us'],
                [
                    'label' => 'Send a Request', 'path' => '/contact-us#request',
                    'title' => 'Send a Trip Request',
                    'text' => 'Tell us your dates, group size and wish list, and we will reply with a tailored itinerary and quote.',
                    'cta' => 'Send a Request',
                    'image' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=800&q=75',
                ],
                [
                    'label' => 'Plan My Trip', 'path' => '/contact-us#request',
                    'title' => 'Plan My Trip',
                    'text' => 'Not sure where to start? We will help you choose the right parks, season and pace for your journey.',
                    'cta' => 'Start Planning',
                    'image' => 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=800&q=75',
                ],
            ],
            'mega' => [
                'title' => 'Start Planning Your Trip',
                'text' => 'Send a request, message us on WhatsApp or call the team. We usually reply within a few hours.',
                'cta' => 'Contact Us',
                'image' => 'https://images.unsplash.com/photo-1547471080-7cc2caa01a7e?auto=format&fit=crop&w=800&q=75',
            ],
        ],
    ],

    /*
    | Languages offered by the Google Translate switcher, keyed by Google
    | Translate language code. The first entry is the site's source language.
    */
    'languages' => [
        'en' => 'English',
        'de' => 'Deutsch',
        'fr' => 'Français',
        'sw' => 'Kiswahili',
        'zh-CN' => '中文',
        'nl' => 'Nederlands',
        'es' => 'Español',
    ],

];
