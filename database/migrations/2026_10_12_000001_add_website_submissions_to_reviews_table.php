<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->string('email')->nullable()->after('country');        // reviewer's email (private, never shown)
            $table->string('source', 10)->default('admin')->after('source_url'); // admin / website
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['email', 'source']);
        });
    }
};
