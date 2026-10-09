<?php

use App\Http\Controllers\ProtectedMediaController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

// Protected media: only reachable through signed, session-bound URLs (App\Support\ProtectedMedia).Dont touch these routes unless you know what you're doing; they are not meant to be browsed directly.
Route::middleware('signed:relative')->prefix('media')->name('media.')->controller(ProtectedMediaController::class)->group(function () {
    Route::get('image/{path}', 'image')->where('path', '[A-Za-z0-9/_.\-]+')->name('image');
    Route::get('stream/{stream}/index.m3u8', 'playlist')->where('stream', '[a-z0-9-]+')->name('playlist');
    Route::get('stream/{stream}/key', 'key')->where('stream', '[a-z0-9-]+')->name('key');
    Route::get('stream/{stream}/{segment}', 'segment')
        ->where(['stream' => '[a-z0-9-]+', 'segment' => 'seg_[0-9]{3,5}\.ts'])
        ->name('segment');
});

// Newsletter sign-up (footer form). Throttled to stop abuse.
Route::post('newsletter', [\App\Http\Controllers\NewsletterController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('newsletter.subscribe');

// Content pages (navigation). Filters use the query string, e.g. /safaris?type=honeymoon.
Route::controller(\App\Http\Controllers\PageController::class)->group(function () {
    Route::get('safaris', 'safaris')->name('safaris');
    Route::get('safaris/{slug}', 'package')->where('slug', '[a-z0-9-]+')->name('safaris.show');
    Route::get('activities', 'activities')->name('activities');
    Route::get('accommodations', 'accommodations')->name('accommodations');
    Route::get('about-us', 'about')->name('about');
    Route::get('reviews', 'reviews')->name('reviews');
    Route::get('contact-us', 'contact')->name('contact');
});

Route::post('contact-us', [\App\Http\Controllers\TripRequestController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('trip-request.store');

Route::controller(\App\Http\Controllers\BlogController::class)->prefix('blog')->group(function () {
    Route::get('/', 'index')->name('blog');
    Route::get('category/{category}', 'index')->where('category', '[a-z0-9-]+')->name('blog.category');
    Route::get('{slug}', 'show')->where('slug', '[a-z0-9-]+')->name('blog.show');
});

// Instant quotes on package pages: the visitor fills a short form and downloads a PDF
// through a signed link (QuoteController).
Route::post('safaris/{slug}/quote', [\App\Http\Controllers\QuoteController::class, 'store'])
    ->where('slug', '[a-z0-9-]+')
    ->middleware('throttle:5,1')
    ->name('quotes.store');
Route::get('quotes/{quote}/pdf', [\App\Http\Controllers\QuoteController::class, 'pdf'])
    ->middleware(['signed', 'throttle:20,1'])
    ->name('quotes.pdf');

// Live chat widget (resources/js/chat.js). The visitor's token is sent in the X-Chat-Token header.
Route::controller(\App\Http\Controllers\ChatController::class)->prefix('chat')->name('chat.')->group(function () {
    Route::post('start', 'start')->middleware('throttle:5,1')->name('start');
    Route::get('messages', 'messages')->middleware('throttle:60,1')->name('messages');
    Route::post('messages', 'send')->middleware('throttle:20,1')->name('send');
    Route::post('typing', 'typing')->middleware('throttle:40,1')->name('typing');
});
