@extends('layouts.app')

@section('title', $page['title'])
@section('description', $page['intro'])

@section('content')
    <x-page-hero
        :title="$page['title']"
        kicker="Legal"
        :subtitle="$page['intro']"
        image="https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?auto=format&fit=crop&w=2000&q=75"
        :crumbs="[['label' => $page['title']]]"
    />

    <section class="bg-tm-beige py-14 sm:py-20">
        <div class="tm-container">
            <div class="tm-legal">
                {{-- On this page --}}
                @if (count($content['toc']) > 2)
                    <nav class="tm-legal-toc" aria-label="On this page">
                        <p class="tm-legal-toc-title">On this page</p>
                        <ol>
                            @foreach ($content['toc'] as $item)
                                <li><a href="#{{ $item['id'] }}">{{ $item['title'] }}</a></li>
                            @endforeach
                        </ol>
                    </nav>
                @endif

                <article class="tm-prose tm-legal-body">
                    <p class="tm-legal-updated">Last updated: <time datetime="{{ $updatedOn->toDateString() }}">{{ $updatedOn->format('j F Y') }}</time></p>

                    {{ $content['html'] }}

                    <div class="tm-legal-more">
                        <p>Related:</p>
                        <ul>
                            @foreach (\App\Support\LegalPages::PAGES as $otherKey => $other)
                                @continue($otherKey === $key)
                                <li><a href="{{ route($other['route']) }}">{{ $other['title'] }}</a></li>
                            @endforeach
                            <li><a href="{{ route('contact') }}">Contact us</a></li>
                        </ul>
                    </div>
                </article>
            </div>
        </div>
    </section>
@endsection
