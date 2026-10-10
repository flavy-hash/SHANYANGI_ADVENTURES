<?php

/*
|--------------------------------------------------------------------------
| Guest Reviews
|--------------------------------------------------------------------------
|
| Reviews are managed in the admin panel (/admin → Reviews). Only publish
| real reviews from real guests; reviews marked as samples are never shown
| when APP_ENV=production.
|
| Links to your listings (badges on the home page and /reviews):
|
*/

return [

    'tripadvisor_url' => env('TRIPADVISOR_URL'),

    'google_reviews_url' => env('GOOGLE_REVIEWS_URL'),

];
