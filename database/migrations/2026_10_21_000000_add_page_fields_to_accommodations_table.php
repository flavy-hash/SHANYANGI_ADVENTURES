<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accommodations', function (Blueprint $table) {
            // Web address of the stay's own page: /accommodations/{slug}.
            $table->string('slug')->nullable()->unique()->after('name');
            // Extra photos for the page gallery (paths on the media disk), and short facility labels.
            $table->json('gallery')->nullable()->after('image');
            $table->json('facilities')->nullable()->after('gallery');
        });

        $taken = [];
        DB::table('accommodations')->orderBy('id')->get(['id', 'name'])->each(function ($row) use (&$taken) {
            $base = Str::slug($row->name) ?: 'stay';
            $slug = $base;
            for ($i = 2; in_array($slug, $taken, true); $i++) {
                $slug = "{$base}-{$i}";
            }
            $taken[] = $slug;
            DB::table('accommodations')->where('id', $row->id)->update(['slug' => $slug]);
        });
    }

    public function down(): void
    {
        Schema::table('accommodations', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'gallery', 'facilities']);
        });
    }
};
