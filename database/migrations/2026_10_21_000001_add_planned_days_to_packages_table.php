<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            // How many days the trip has; the admin shows how many itinerary days are still missing.
            $table->unsignedSmallInteger('planned_days')->nullable()->after('style');
        });

        // Existing packages: the number in the duration tag ("6 Days"), else the days already written.
        DB::table('packages')->select(['id', 'chips', 'itinerary'])->orderBy('id')->each(function ($row) {
            $label = json_decode((string) $row->chips, true)[0]['label'] ?? '';
            $days = preg_match('/(\d+)\s*day/i', (string) $label, $m) ? (int) $m[1] : count(json_decode((string) $row->itinerary, true) ?: []);

            if ($days > 0) {
                DB::table('packages')->where('id', $row->id)->update(['planned_days' => $days]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('planned_days');
        });
    }
};
