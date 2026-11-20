<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            // Price per person by group size: [{people: 2, price: 3771}, {people: 4, price: 3303}, ...].
            $table->json('price_tiers')->nullable()->after('price');
            // Price per child, when it differs from the adult price.
            $table->unsignedInteger('child_price')->nullable()->after('price_tiers');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['price_tiers', 'child_price']);
        });
    }
};
