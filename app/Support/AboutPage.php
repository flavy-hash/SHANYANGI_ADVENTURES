<?php

namespace App\Support;

use App\Models\SiteSetting;

/**
 * Editable content of the About Us page (admin: "About Us" page).
 * Anything left empty in the admin falls back to these defaults.
 */
class AboutPage
{
    public const KEY = 'about';

    public static function defaults(): array
    {
        return [
            'hero_title' => 'About Shanyangi Adventures',
            'hero_subtitle' => 'A locally owned Tanzanian tour company planning private journeys across our home country.',
            'hero_image' => null,
            'story_title' => 'Rooted in Tanzania',
            'story' => "Shanyangi Adventures is a locally owned Tanzanian tour company. We plan private safaris, Kilimanjaro climbs, cultural experiences and Zanzibar escapes for travellers who want to see our country properly, at their own pace.\n\n"
                ."Every journey is planned personally. We listen to what you want from your trip, then build an itinerary around your dates, interests and budget, choosing the parks, lodges and experiences that suit you best.\n\n"
                .'Travelling with a local team means your trip supports the guides, crews, lodges and communities that make it possible.',
            'story_image' => null,
            'team_title' => 'The People Behind Your Trip',
            'team_subtitle' => 'From the first message to the final transfer, these are the people looking after you.',
            'team' => [
                ['icon' => 'compass', 'title' => 'Safari Guides', 'text' => 'Experienced guides who know the parks, the animals and the stories behind every sighting.'],
                ['icon' => 'calendar', 'title' => 'Trip Planners', 'text' => 'The people who shape your itinerary, answer your questions and handle every booking detail.'],
                ['icon' => 'mountain', 'title' => 'Mountain Crews', 'text' => 'Guides, cooks and porters who support every Kilimanjaro climb from the gate to the summit.'],
                ['icon' => 'headset', 'title' => 'On-Trip Support', 'text' => 'A team you can reach throughout your journey, from airport pickup to your final transfer.'],
            ],
        ];
    }

    /**
     * Stroke icons offered for team cards (24x24 SVG path data).
     */
    public static function icons(): array
    {
        return [
            'compass' => '<circle cx="12" cy="12" r="8.25"/><path d="m14.5 9.5-1.5 5-5 1.5 1.5-5 5-1.5Z"/>',
            'calendar' => '<rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 2v4M16 2v4M4 10h16"/>',
            'mountain' => '<path d="m3 20 6-11 4 6 2-3 6 8H3Z"/>',
            'headset' => '<path d="M4 13v-1a8 8 0 0 1 16 0v1"/><rect x="3" y="13" width="4" height="6" rx="1.5"/><rect x="17" y="13" width="4" height="6" rx="1.5"/>',
            'car' => '<path d="M5 17h14M6 17v2M18 17v2M4 13l2-5h12l2 5v4H4v-4Z"/><circle cx="8" cy="14.5" r="1"/><circle cx="16" cy="14.5" r="1"/>',
            'heart' => '<path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10Z"/>',
            'users' => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0M16 4.5a3.5 3.5 0 0 1 0 7M21.5 20a6.5 6.5 0 0 0-4-6"/>',
        ];
    }

    public static function paragraphs(?string $text): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R\s*\R/', (string) $text))));
    }

    public static function content(): array
    {
        return SiteSetting::get(self::KEY, self::defaults());
    }
}
