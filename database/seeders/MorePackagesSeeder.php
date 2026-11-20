<?php

namespace Database\Seeders;

use App\Models\Package;

/**
 * Adds the additional starter packages (database/seeders/content/packages-more*.php).
 * A package is only created when its slug doesn't exist yet, so packages edited
 * (or deleted and recreated) in the admin are never overwritten.
 *
 * On an existing site: php artisan db:seed --class=MorePackagesSeeder --force
 */
class MorePackagesSeeder extends ContentSeeder
{
    public function run(): void
    {
        $order = (int) Package::query()->max('sort_order');
        $added = 0;

        $packages = [];
        foreach (['packages-more', 'packages-more-2', 'packages-more-3', 'packages-more-4'] as $file) {
            $packages = [...$packages, ...require __DIR__."/content/{$file}.php"];
        }

        foreach ($packages as $p) {
            if (Package::query()->where('slug', $p['slug'])->exists()) {
                continue;
            }

            Package::query()->create([
                'slug' => $p['slug'],
                'title' => $p['title'],
                'category' => $p['category'],
                'style' => $p['style'] ?? null,
                'tagline' => $p['tagline'] ?? null,
                'types' => $p['types'] ?? [],
                'chips' => array_map(fn ($c) => ['label' => $c['label'], 'highlight' => (bool) ($c['highlight'] ?? false)], $p['chips'] ?? []),
                'summary' => $p['text'],
                'price' => $p['price'] ?? null,
                'price_tiers' => $p['price_tiers'] ?? null,
                'child_price' => $p['child_price'] ?? null,
                'image' => $this->image($p['image'] ?? null),
                'location' => $p['location'] ?? null,
                'overview' => implode("\n\n", $p['overview'] ?? []),
                'facts' => $p['facts'] ?? [],
                'itinerary' => array_map(fn (array $day) => $this->day($day), $p['itinerary'] ?? []),
                'included' => array_values(array_unique($p['included'] ?? [])),
                'excluded' => array_values(array_unique($p['excluded'] ?? [])),
                'optional_activities' => $p['optional'] ?? [],
                'is_featured' => false,
                'is_published' => true,
                'sort_order' => ++$order,
            ]);
            $added++;
        }

        $this->command?->info($added ? "Added {$added} packages." : 'All additional packages already exist; nothing added.');
    }
}
