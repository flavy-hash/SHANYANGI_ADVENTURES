<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_views', function (Blueprint $table) {
            // ISO 3166-1 alpha-2 code looked up from the IP (the IP itself is never stored).
            $table->char('country', 2)->nullable()->after('referrer_host');
            $table->index(['country', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('page_views', function (Blueprint $table) {
            $table->dropIndex(['country', 'created_at']);
            $table->dropColumn('country');
        });
    }
};
