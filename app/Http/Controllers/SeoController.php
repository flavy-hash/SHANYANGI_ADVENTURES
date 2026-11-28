<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\Activity;
use App\Models\BlogPost;
use App\Models\Package;
use App\Models\Review;
use App\Support\Seo;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

/**
 * /sitemap.xml and /robots.txt, generated so they always match the published content and
 * use APP_URL. (A static public/robots.txt would be served before Laravel: don't add one.)
 */
class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $packages = Package::query()->published()->ordered()->get(['slug', 'updated_at']);
        $stays = Accommodation::query()->published()->ordered()->get(['slug', 'updated_at']);
        $posts = BlogPost::query()->published()->latestFirst()->get(['slug', 'category', 'updated_at']);
        $categories = array_keys(config('blog.categories', []));

        $latest = fn ($items) => $items->max('updated_at');

        // [path, last modified]. Listing pages change when their newest item does.
        $pages = [
            ['/', $latest($packages)],
            [route('safaris', [], false), $latest($packages)],
            [route('tours', [], false), $latest($packages)],
            [route('activities', [], false), Activity::query()->published()->max('updated_at')],
            [route('accommodations', [], false), $latest($stays)],
            [route('blog', [], false), $latest($posts)],
            [route('reviews', [], false), Review::query()->visible()->max('updated_at')],
            [route('about', [], false), null],
            [route('contact', [], false), null],
        ];

        foreach ($categories as $category) {
            $inCategory = $posts->where('category', $category);
            if ($inCategory->isNotEmpty()) {
                $pages[] = [route('blog.category', $category, false), $latest($inCategory)];
            }
        }

        foreach ($packages as $package) {
            $pages[] = [route('safaris.show', $package->slug, false), $package->updated_at];
        }
        foreach ($stays as $stay) {
            $pages[] = [route('accommodations.show', $stay->slug, false), $stay->updated_at];
        }
        foreach ($posts as $post) {
            $pages[] = [route('blog.show', $post->slug, false), $post->updated_at];
        }

        foreach (['legal.privacy', 'legal.terms', 'legal.refund'] as $route) {
            $pages[] = [route($route, [], false), null];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach (collect($pages)->unique(fn ($page) => Seo::url($page[0])) as [$path, $modified]) {
            $xml .= '  <url><loc>'.htmlspecialchars(Seo::url($path), ENT_XML1).'</loc>'
                .($modified ? '<lastmod>'.Carbon::parse($modified)->toAtomString().'</lastmod>' : '')
                .'</url>'."\n";
        }

        $xml .= '</urlset>'."\n";

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function robots(): Response
    {
        $lines = ['User-agent: *'];
        foreach (config('seo.disallow', []) as $path) {
            $lines[] = 'Disallow: '.$path;
        }
        $lines[] = '';
        $lines[] = 'Sitemap: '.Seo::url('sitemap.xml');

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
