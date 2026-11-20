<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accommodations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location', 80);          // area shown on the card and used for filters, e.g. "Serengeti"
            $table->string('type', 60)->nullable();   // e.g. Lodge, Tented camp
            $table->string('level', 60)->nullable();  // e.g. Mid-range, Luxury
            $table->text('description');
            $table->string('image')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accommodations');
    }
};
