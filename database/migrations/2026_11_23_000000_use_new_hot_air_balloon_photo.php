<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** The original stock photo, as an online URL or its downloaded copy (seed/photo-1507608616759-….jpg). */
    protected const OLD_PHOTO = '1507608616759-54f48f0af0ee';

    public function up(): void
    {
        // Only when the card still has the original photo: one changed in the admin is kept.
        DB::table('activities')
            ->where('title', 'Hot Air Balloon Safari')
            ->where('image', 'like', '%'.self::OLD_PHOTO.'%')
            ->update(['image' => 'site/hot-air-balloon-card.webp']);
    }

    public function down(): void
    {
        DB::table('activities')
            ->where('title', 'Hot Air Balloon Safari')
            ->where('image', 'site/hot-air-balloon-card.webp')
            ->update(['image' => 'https://images.unsplash.com/photo-'.self::OLD_PHOTO.'?auto=format&fit=crop&w=900&q=75']);
    }
};
