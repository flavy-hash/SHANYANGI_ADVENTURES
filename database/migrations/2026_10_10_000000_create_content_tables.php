<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Website content managed in the admin panel (/admin): packages with their
 * itineraries, guest reviews, blog posts, activities and page settings.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('category', 50);
            $table->string('tagline')->nullable();
            $table->json('types')->nullable();          // Safari page filters
            $table->json('chips')->nullable();          // [{label, highlight}]
            $table->text('summary');                    // card + banner text
            $table->unsignedInteger('price')->nullable();
            $table->string('image')->nullable();
            $table->string('location')->nullable();
            $table->text('overview')->nullable();       // paragraphs separated by blank lines
            $table->json('facts')->nullable();          // {label: value}
            $table->json('itinerary')->nullable();      // days
            $table->json('included')->nullable();
            $table->json('excluded')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 120);
            $table->string('country', 80)->nullable();
            $table->date('travelled_on')->nullable();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->string('title');
            $table->text('body');
            $table->json('photos')->nullable();
            $table->string('source_url')->nullable();   // link to the original (Google, Tripadvisor...)
            $table->boolean('is_published')->default(true);
            $table->boolean('is_sample')->default(false); // placeholders: never shown in production
            $table->timestamps();
        });

        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('category', 50);
            $table->date('published_at');
            $table->unsignedTinyInteger('read_minutes')->default(5);
            $table->string('image')->nullable();
            $table->text('excerpt');
            $table->longText('body');                   // paragraphs; lines starting "## " are headings
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('location', 50);
            $table->string('duration', 50)->nullable();
            $table->text('description');
            $table->string('image')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('packages');
    }
};
