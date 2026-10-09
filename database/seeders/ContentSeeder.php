<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\BlogPost;
use App\Models\Package;
use App\Models\Review;
use App\Models\SiteSetting;
use App\Support\AboutPage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Loads the starter website content (database/seeders/content/*.php) into the
 * database. Safe to re-run: records are matched by slug/title and updated.
 *
 * Remote images are downloaded into the protected media disk so the website
 * and the admin treat them like uploaded images. Set SEED_DOWNLOAD_IMAGES=false
 * to keep the remote URLs instead (used by the test suite).
 */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPackages();
        $this->seedActivities();
        $this->seedBlog();
        $this->seedReviews();

        // Only create About content if it hasn't been edited in the admin yet.
        SiteSetting::query()->firstOrCreate(['key' => AboutPage::KEY], ['value' => AboutPage::defaults()]);
    }

    protected function seedPackages(): void
    {
        $packages = require __DIR__.'/content/packages.php';
        $itineraries = require __DIR__.'/content/itineraries.php';

        foreach ($packages as $i => $p) {
            $detail = $itineraries[$p['slug']] ?? [];

            Package::query()->updateOrCreate(['slug' => $p['slug']], [
                'title' => $p['title'],
                'category' => $p['category'],
                'tagline' => $p['tagline'] ?? null,
                'types' => $p['types'] ?? [],
                'chips' => array_map(fn ($c) => ['label' => $c['label'], 'highlight' => (bool) ($c['highlight'] ?? false)], $p['chips'] ?? []),
                'summary' => $p['text'],
                'price' => $p['price'],
                'image' => $this->image($p['image'] ?? null),
                'location' => $detail['location'] ?? null,
                'overview' => implode("\n\n", $detail['overview'] ?? []),
                'facts' => $detail['facts'] ?? [],
                'itinerary' => array_map(fn (array $day) => $this->day($day), $detail['itinerary'] ?? []),
                'included' => $detail['included'] ?? [],
                'excluded' => $detail['excluded'] ?? [],
                'is_featured' => (bool) ($p['featured'] ?? false),
                'is_published' => true,
                'sort_order' => $i + 1,
            ]);
        }
    }

    /**
     * Normalise one itinerary day to the shape used by the admin form.
     */
    protected function day(array $day): array
    {
        return [
            'title' => $day['title'],
            'image' => $this->image($day['image'] ?? null),
            'caption' => $day['caption'] ?? null,
            'text' => $day['text'],
            'note' => $day['note'] ?? null,
            'activity' => isset($day['activity']) ? [
                'title' => $day['activity']['title'],
                'text' => $day['activity']['text'],
                'image' => $this->image($day['activity']['image'] ?? null),
            ] : null,
            'stay' => $day['stay'] ?? null,
            'meals' => $day['meals'] ?? null,
            'options' => $day['options'] ?? [],
            'stay_image' => $this->image($day['stay_image'] ?? null),
            'stay_caption' => $day['stay_caption'] ?? null,
        ];
    }

    protected function seedActivities(): void
    {
        foreach (require __DIR__.'/content/activities.php' as $i => $a) {
            Activity::query()->updateOrCreate(['title' => $a['title']], [
                'location' => $a['location'],
                'duration' => $a['duration'] ?? null,
                'description' => $a['text'],
                'image' => $this->image($a['image'] ?? null),
                'is_published' => true,
                'sort_order' => $i + 1,
            ]);
        }
    }

    protected function seedBlog(): void
    {
        foreach (require __DIR__.'/content/blog.php' as $post) {
            BlogPost::query()->updateOrCreate(['slug' => $post['slug']], [
                'title' => $post['title'],
                'category' => $post['category'],
                'published_at' => $post['date'],
                'read_minutes' => $post['read_minutes'],
                'image' => $this->image($post['image'] ?? null),
                'excerpt' => $post['excerpt'],
                'body' => implode("\n\n", $post['body']),
                'is_published' => true,
            ]);
        }
    }

    protected function seedReviews(): void
    {
        foreach (require __DIR__.'/content/reviews.php' as $r) {
            Review::query()->updateOrCreate(['title' => $r['title'], 'name' => $r['name']], [
                'package_id' => Package::query()->where('title', $r['package'] ?? null)->value('id'),
                'country' => $r['country'] ?? null,
                'travelled_on' => $r['date'] ?? null,
                'rating' => $r['rating'] ?? 5,
                'body' => $r['text'],
                'photos' => array_values(array_filter(array_map(fn ($url) => $this->image($url), $r['images'] ?? []))),
                'source_url' => $r['url'] ?? null,
                'is_published' => true,
                'is_sample' => (bool) ($r['sample'] ?? false),
            ]);
        }
    }

    /**
     * Download a remote image into the media disk (once) and return its path.
     * Falls back to the original URL if downloading is disabled or fails.
     */
    protected function image(?string $url): ?string
    {
        if (! $url || ! str_starts_with($url, 'http') || ! config('media.seed_download_images')) {
            return $url;
        }

        $name = preg_match('/photo-[\w-]+/', $url, $m) ? $m[0] : md5($url);
        $path = "seed/{$name}.jpg";
        $disk = Storage::disk('media');

        if ($disk->exists($path)) {
            return $path;
        }

        try {
            $response = Http::timeout(30)->get(preg_replace('/([?&])w=\d+/', '$1w=1600', $url));

            if ($response->successful() && str_starts_with((string) $response->header('Content-Type'), 'image/')) {
                $disk->put($path, $response->body());

                return $path;
            }
        } catch (Throwable) {
            // fall through to the URL
        }

        $this->command?->warn("Could not download {$url}; keeping the remote URL.");

        return $url;
    }
}
