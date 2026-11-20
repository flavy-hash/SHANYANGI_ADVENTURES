<?php

namespace App\Console\Commands;

use App\Support\ItineraryStays;
use Illuminate\Console\Command;

/**
 * Creates an accommodation page for every stay named in a package itinerary that
 * doesn't have one yet, so all itinerary accommodations link to a page. New
 * packages do this automatically when saved; run it once for existing packages.
 */
class AccommodationsFromItineraries extends Command
{
    protected $signature = 'accommodations:from-itineraries';

    protected $description = 'Create accommodation pages for the stays used in package itineraries';

    public function handle(): int
    {
        $created = ItineraryStays::sync();

        $this->info($created
            ? "Created {$created} accommodation pages. Add descriptions and photo galleries under Website Content → Accommodations."
            : 'Every itinerary accommodation already has a page.');

        return self::SUCCESS;
    }
}
