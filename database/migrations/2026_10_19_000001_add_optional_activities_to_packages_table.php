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
        Schema::table('packages', function (Blueprint $table) {
            // Extras guests can add, shown below the itinerary: [{name, price}].
            $table->json('optional_activities')->nullable()->after('excluded');
        });

        // Move "Optional: Name – price" lines out of "Not included" into the new list.
        DB::table('packages')->select(['id', 'excluded'])->orderBy('id')->each(function ($row) {
            $excluded = json_decode((string) $row->excluded, true) ?: [];
            $optional = [];

            foreach ($excluded as $i => $item) {
                if (! is_string($item) || ! preg_match('/^Optional:\s*(.+)$/u', $item, $m)) {
                    continue;
                }
                [$name, $price] = array_pad(preg_split('/\s+[–-]\s+/u', $m[1], 2), 2, '');
                $optional[] = ['name' => Str::ucfirst(trim($name)), 'price' => Str::ucfirst(trim($price))];
                unset($excluded[$i]);
            }

            if ($optional) {
                DB::table('packages')->where('id', $row->id)->update([
                    'excluded' => json_encode(array_values($excluded)),
                    'optional_activities' => json_encode($optional),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('optional_activities');
        });
    }
};
