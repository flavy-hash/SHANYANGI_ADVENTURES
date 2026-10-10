@extends('layouts.app')

@section('title', $post['title'].' | Shanyangi Adventures')
@section('description', $post['excerpt'])

@section('content')
    @php($categoryLabel = $categories[$post['category']] ?? null)

    <x-page-hero
        :title="$post['title']"
        :kicker="$categoryLabel"
        :subtitle="$post->published_at->format('j F Y').' · '.$post->read_minutes.' min read'"
        :image="$post['image']"
        :crumbs="array_filter([
            ['label' => 'Blog', 'url' => route('blog')],
            $categoryLabel ? ['label' => $categoryLabel, 'url' => route('blog.category', $post['category'])] : null,
            ['label' => $post['title']],
        ])"
    />

    <article class="bg-tm-beige py-14 sm:py-20">
        <div class="tm-container">
            <div class="tm-prose">
                <p class="tm-prose-lead">{{ $post['excerpt'] }}</p>

                @foreach ($post->body_blocks as $block)
                    @if ($block['type'] === 'heading')
                        <h2>{{ $block['text'] }}</h2>
                    @else
                        <p>{{ $block['text'] }}</p>
                    @endif
                @endforeach

                <div class="tm-prose-footer">
                    <a href="{{ route('blog') }}" class="tm-text-link">
                        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="tm-flip-x"><path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.64L10.2 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.19-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd"/></svg>
                        All articles
                    </a>
                    <a href="{{ route('contact') }}#request" class="tm-btn-primary">Plan Your Trip</a>
                </div>
            </div>
        </div>
    </article>

    @if ($related->isNotEmpty())
        <section class="bg-tm-cream py-14 sm:py-20" aria-labelledby="related-title">
            <div class="tm-container">
                <p class="tm-section-kicker">Keep Reading</p>
                <h2 id="related-title" class="tm-section-title">More From the Blog</h2>
                <div class="tm-divider" aria-hidden="true"></div>

                <div class="tm-card-grid mt-10">
                    @foreach ($related as $item)
                        <x-info-card
                            :title="$item['title']"
                            :href="route('blog.show', $item['slug'])"
                            :image="$item['image']"
                            :pill="$categories[$item['category']] ?? null"
                            :meta="$item->published_at->format('j M Y').' · '.$item->read_minutes.' min read'"
                            :text="$item['excerpt']"
                            link-label="Read article"
                        />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
