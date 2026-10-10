<?php

namespace App\Http\Controllers;

use App\Filament\Resources\Reviews\ReviewResource;
use App\Models\Package;
use App\Models\Review;
use App\Support\AdminAlerts;
use App\Support\UploadedImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * "Write a review" on the reviews page. Reviews are saved unpublished and
 * appear on the website only after the team approves them in the admin.
 */
class ReviewController extends Controller
{
    public const MAX_PHOTOS = 4;

    public const MAX_PHOTO_KB = 8192;

    public function store(Request $request): RedirectResponse
    {
        $back = route('reviews').'#write-review';

        // Honeypot: bots fill the hidden "website" field. Pretend success, store nothing.
        if (filled($request->input('website'))) {
            return redirect()->to($back)->with('review_sent', true);
        }

        try {
            $data = $request->validateWithBag('review', [
                'rating' => ['required', 'integer', 'between:1,5'],
                'name' => ['required', 'string', 'max:120'],
                'email' => ['required', 'string', 'email:rfc', 'max:255'],
                'country' => ['nullable', 'string', 'max:80'],
                'package_id' => ['nullable', 'integer', Rule::exists('packages', 'id')->where('is_published', true)],
                'travelled_on' => ['nullable', 'date_format:Y-m', 'before_or_equal:'.now()->format('Y-m')],
                'title' => ['required', 'string', 'max:150'],
                'body' => ['required', 'string', 'min:20', 'max:3000'],
                'photos' => ['nullable', 'array', 'max:'.self::MAX_PHOTOS],
                'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:'.self::MAX_PHOTO_KB],
            ], [
                'rating.required' => 'Please choose a star rating.',
                'travelled_on.before_or_equal' => 'Please choose a month that has already passed.',
                'body.min' => 'Please tell us a little more (at least 20 characters).',
                'photos.max' => 'You can add up to '.self::MAX_PHOTOS.' photos.',
                'photos.*.image' => 'Photos must be JPG, PNG or WebP images.',
                'photos.*.mimes' => 'Photos must be JPG, PNG or WebP images.',
                'photos.*.max' => 'Each photo must be smaller than '.(self::MAX_PHOTO_KB / 1024).' MB.',
            ], [
                'body' => 'review',
                'package_id' => 'trip',
                'travelled_on' => 'travel month',
            ]);
        } catch (ValidationException $e) {
            throw $e->redirectTo($back);
        }

        // Re-encoded on the private media disk (strips GPS/EXIF data); unreadable files are skipped.
        $photos = collect($request->file('photos', []))
            ->map(fn ($file) => UploadedImage::store($file, 'uploads/reviews'))
            ->filter()
            ->values()
            ->all();

        $review = Review::create([
            ...collect($data)->except('photos')->all(),
            'photos' => $photos ?: null,
            'travelled_on' => isset($data['travelled_on']) ? Carbon::createFromFormat('Y-m', $data['travelled_on'])->startOfMonth() : null,
            'source' => 'website',
            'is_published' => false,
            'is_sample' => false,
        ]);

        AdminAlerts::send(
            'New '.$review->rating.'-star review to approve',
            $review->name.': '.Str::limit($review->title, 80).($photos ? ' ('.count($photos).' '.Str::plural('photo', count($photos)).')' : ''),
            ReviewResource::getUrl('edit', ['record' => $review], panel: 'admin'),
            'heroicon-o-star',
        );

        return redirect()->to($back)->with('review_sent', strtok($review->name, ' '));
    }

    /**
     * Trips a reviewer can pick from.
     *
     * @return array<int, string>
     */
    public static function tripOptions(): array
    {
        return Package::query()->published()->ordered()->pluck('title', 'id')->all();
    }
}
