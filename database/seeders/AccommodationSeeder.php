<?php

namespace Database\Seeders;

use App\Models\Accommodation;
use Database\Seeders\Concerns\DownloadsImages;
use Illuminate\Database\Seeder;

/**
 * Adds the starter stays (database/seeders/content/accommodations.php), but only when
 * the accommodations table is empty, so it never overwrites stays edited in the admin.
 * On an existing site it only fills in an empty gallery or facilities list of the
 * starter stays (matched by name), for their own pages.
 *
 * On an existing site: php artisan db:seed --class=AccommodationSeeder --force
 */
class AccommodationSeeder extends Seeder
{
    use DownloadsImages;

    public function run(): void
    {
        if (Accommodation::query()->exists()) {
            $filled = 0;
            foreach (require __DIR__.'/content/accommodations.php' as $stay) {
                $existing = Accommodation::query()->where('name', $stay['name'])->first();
                if (! $existing) {
                    continue;
                }
                $changes = array_filter([
                    'gallery' => empty($existing->gallery) && ! empty($stay['gallery']) ? array_values(array_filter(array_map(fn ($url) => $this->image($url), $stay['gallery']))) : null,
                    'facilities' => empty($existing->facilities) && ! empty($stay['facilities']) ? $stay['facilities'] : null,
                ]);
                if ($changes) {
                    $existing->update($changes);
                    $filled++;
                }
            }
            $this->command?->info($filled ? "Accommodations already exist; added photos/facilities to {$filled} starter stays." : 'Accommodations already exist; nothing added.');

            return;
        }

        foreach (require __DIR__.'/content/accommodations.php' as $i => $stay) {
            Accommodation::query()->create([
                'name' => $stay['name'],
                'location' => $stay['location'],
                'type' => $stay['type'] ?? null,
                'level' => $stay['level'] ?? null,
                'description' => $stay['text'],
                'image' => $this->image($stay['image'] ?? null),
                'gallery' => array_values(array_filter(array_map(fn ($url) => $this->image($url), $stay['gallery'] ?? []))),
                'facilities' => $stay['facilities'] ?? [],
                'is_published' => true,
                'sort_order' => $i + 1,
            ]);
        }

        $this->command?->info('Added '.count(require __DIR__.'/content/accommodations.php').' starter stays.');
    }
}
