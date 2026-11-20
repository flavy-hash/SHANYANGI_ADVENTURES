<?php

/*
|--------------------------------------------------------------------------
| Route maps (package pages)
|--------------------------------------------------------------------------
|
| Maps use Leaflet with free OpenStreetMap tiles by default (no account needed).
| For the green "outdoors" look, create a free Mapbox account and set
| MAPBOX_TOKEN in .env (a public "pk." token, restricted to your domain).
|
*/

return [

    'mapbox_token' => env('MAPBOX_TOKEN'),

    'mapbox_style' => env('MAPBOX_STYLE', 'mapbox/outdoors-v12'),

];
