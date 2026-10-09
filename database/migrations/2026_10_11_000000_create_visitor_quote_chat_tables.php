<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Visitor activity log, quotations and live chat.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Privacy-friendly page views: no IP address, cookies or full user agent.
        // visitor_hash is a daily-rotating anonymous ID (see App\Http\Middleware\LogPageView).
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->char('visitor_hash', 16);
            $table->string('path', 255);
            $table->string('page_type', 30);
            $table->foreignId('package_id')->nullable()->constrained()->nullOnDelete();
            $table->string('device', 10);                 // mobile / tablet / desktop
            $table->string('referrer_host', 120)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('created_at');
            $table->index(['package_id', 'created_at']);
        });

        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 30)->nullable()->unique();
            $table->string('source', 20)->default('admin');   // admin / website
            $table->string('status', 20)->default('draft');   // draft / sent / accepted / declined
            $table->foreignId('package_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('trip_request_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name', 120);
            $table->string('customer_email');
            $table->string('customer_phone', 40)->nullable();
            $table->string('customer_country', 80)->nullable();
            $table->unsignedTinyInteger('adults')->default(2);
            $table->unsignedTinyInteger('children')->default(0);
            $table->date('travel_start')->nullable();
            $table->date('travel_end')->nullable();
            $table->json('items')->nullable();                // [{description, quantity, unit_price}]
            $table->decimal('discount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->date('valid_until')->nullable();
            $table->timestamps();
        });

        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->char('token_hash', 64)->unique();         // sha256 of the visitor's secret token
            $table->string('visitor_name', 120)->nullable();
            $table->string('visitor_email')->nullable();
            $table->string('started_on', 255)->nullable();    // page path where the chat began
            $table->timestamp('last_message_at')->nullable();
            $table->timestamp('admin_read_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index('last_message_at');
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_conversation_id')->constrained()->cascadeOnDelete();
            $table->string('sender', 10);                     // visitor / team
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('body');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_conversations');
        Schema::dropIfExists('quotes');
        Schema::dropIfExists('page_views');
    }
};
