<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Starting style for the packages that came with the site (change them in the admin). */
    protected const STYLES = [
        'budget' => ['ngorongoro-crater-day-trip', 'tarangire-ngorongoro-manyara-3-days', 'northern-circuit-express-4-days', 'marangu-route-6-days'],
        'classic' => ['serengeti-ngorongoro-safari', 'classic-northern-circuit-7-days', 'machame-route', 'safari-and-zanzibar-10-days'],
        'mid-range' => ['family-safari-adventure', 'calving-season-ndutu-6-days', 'lake-natron-serengeti-7-days', 'family-safari-and-beach-9-days', 'zanzibar-beach-escape', 'lemosho-route', 'great-migration-river-crossings'],
        'luxury' => ['safari-and-sea-honeymoon', 'fly-in-serengeti-luxury', 'grand-tanzania-12-days'],
    ];

    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            // Comfort level for the Style filter on /safaris: budget, classic, mid-range, luxury.
            $table->string('style', 20)->nullable()->after('category');
        });

        foreach (self::STYLES as $style => $slugs) {
            DB::table('packages')->whereIn('slug', $slugs)->whereNull('style')->update(['style' => $style]);
        }
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('style');
        });
    }
};
