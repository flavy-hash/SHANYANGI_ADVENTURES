@extends('layouts.app')

@section('title', ($activeLabel ? $activeLabel.' Articles' : 'Blog').' | Shanyangi Adventures')
@section('description', 'Safari planning guides, Kilimanjaro advice and Tanzania travel tips.')

@section('content')
    <x-page-hero
        title="Stories & Planning Guides"
        kicker="The Blog"
        subtitle="Seasons, routes, packing lists and practical advice for planning your Tanzania adventure."
        image="https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=2000&q=75"
        :crumbs="array_filter([['label' => 'Blog', 'url' => route('blog')], $activeLabel ? ['label' => $activeLabel] : null])"
    />

    <section class="bg-tm-beige py-14 sm:py-20" aria-labelledby="blog-list-title">
        <div class="tm-container">
            <p class="tm-section-kicker">Latest Articles</p>
            <h2 id="blog-list-title" class="tm-section-title">{{ $activeLabel ? $activeLabel.' Articles' : 'Read Before You Go' }}</h2>
            <div class="tm-divider" aria-hidden="true"></div>

            <x-filter-chips :filters="$filters" label="Filter articles by category" />

            @if ($posts->isNotEmpty())
                <div class="tm-card-grid mt-10">
                    @foreach ($posts as $post)
                        <x-info-card
                            :title="$post['title']"
                            :href="route('blog.show', $post['slug'])"
                            :image="$post['image']"
                            :pill="$categories[$post['category']] ?? null"
                            :meta="$post->published_at->format('j M Y').' · '.$post->read_minutes.' min read'"
                            :text="$post['excerpt']"
                            link-label="Read article"
                        />
                    @endforeach
                </div>
            @else
                <p class="tm-empty">No articles in this category yet. <a href="{{ route('blog') }}">See all articles</a>.</p>
            @endif
        </div>
    </section>

    <x-cta-band />
@endsection
