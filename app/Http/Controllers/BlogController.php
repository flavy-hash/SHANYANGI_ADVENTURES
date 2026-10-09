<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

/**
 * Blog listing, category filter and article pages. Posts are managed in the
 * admin panel; categories are listed in config/blog.php.
 */
class BlogController extends Controller
{
    public function index(?string $category = null): View
    {
        $categories = config('blog.categories', []);

        abort_if($category !== null && ! array_key_exists($category, $categories), 404);

        $posts = BlogPost::query()->published()->latestFirst()
            ->when($category, fn (Builder $query) => $query->where('category', $category))
            ->get();

        $filters = [['label' => 'All Articles', 'url' => route('blog'), 'active' => $category === null]];
        foreach ($categories as $key => $label) {
            $filters[] = ['label' => $label, 'url' => route('blog.category', $key), 'active' => $category === $key];
        }

        return view('pages.blog', [
            'posts' => $posts,
            'categories' => $categories,
            'filters' => $filters,
            'activeLabel' => $category ? $categories[$category] : null,
        ]);
    }

    public function show(string $slug): View
    {
        $post = BlogPost::query()->published()->where('slug', $slug)->firstOrFail();

        // Same category first, then the most recent.
        $related = BlogPost::query()->published()->latestFirst()->whereKeyNot($post->getKey())->get()
            ->sortByDesc(fn (BlogPost $p) => $p->category === $post->category)
            ->take(3)
            ->values();

        return view('pages.blog-post', [
            'post' => $post,
            'categories' => config('blog.categories', []),
            'related' => $related,
        ]);
    }
}
