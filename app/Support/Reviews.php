<?php

namespace App\Support;

use App\Models\Package;
use App\Models\Review;
use Illuminate\Support\Collection;

/**
 * Guest reviews for the website, as plain arrays for the review card component.
 *
 * Only published reviews are returned, and reviews marked as samples
 * (placeholders) are never returned in production (Review::scopeVisible).
 */
class Reviews
{
    /**
     * Publishable reviews, newest first.
     */
    public static function all(): Collection
    {
        return Review::query()->visible()->with('package')
            ->orderByDesc('travelled_on')->orderByDesc('id')
            ->get()
            ->map(fn (Review $review) => self::card($review));
    }

    public static function forPackage(Package $package): Collection
    {
        return Review::query()->visible()->with('package')
            ->whereBelongsTo($package)
            ->orderByDesc('travelled_on')->orderByDesc('id')
            ->get()
            ->map(fn (Review $review) => self::card($review));
    }

    /**
     * @return array{name: string, country: ?string, date: ?string, rating: int, title: string, text: string,
     *               package: ?string, package_slug: ?string, category: ?string, images: array, url: ?string, sample: bool}
     */
    public static function card(Review $review): array
    {
        $package = $review->package;

        return [
            'name' => $review->name,
            'country' => $review->country,
            'date' => $review->travelled_on?->toDateString(),
            'rating' => $review->rating,
            'title' => $review->title,
            'text' => $review->body,
            'package' => $package?->title,
            // Only link to packages that are live on the site.
            'package_slug' => $package?->is_published ? $package->slug : null,
            'category' => $package?->category,
            'images' => $review->photos ?? [],
            'url' => $review->source_url,
            'sample' => $review->is_sample,
        ];
    }

    /**
     * Average rating, count and how many reviews gave each star rating.
     *
     * @return array{average: float, count: int, breakdown: array<int, int>}
     */
    public static function summary(Collection $reviews): array
    {
        $ratings = $reviews->map(fn (array $review) => max(1, min(5, (int) round($review['rating'] ?? 5))));

        $breakdown = [];
        foreach ([5, 4, 3, 2, 1] as $stars) {
            $breakdown[$stars] = $ratings->filter(fn (int $rating) => $rating === $stars)->count();
        }

        return [
            'average' => $ratings->isEmpty() ? 0.0 : round($ratings->avg(), 1),
            'count' => $ratings->count(),
            'breakdown' => $breakdown,
        ];
    }
}
