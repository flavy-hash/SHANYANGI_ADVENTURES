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
