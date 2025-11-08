<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('source_id'); // FK to sources table
            $table->string('external_id')->nullable(); // provider's id
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->string('author')->nullable();
            $table->string('url');
            $table->string('image_url')->nullable();
            $table->string('category')->nullable();
            $table->string('language', 10)->nullable();
            $table->timestamp('published_at')->nullable();
            $table->json('meta')->nullable(); // provider raw payload
            $table->timestamps();

            $table->index('published_at');
            $table->index('category');
            $table->index('author');

            /* prevent duplicates: unique on source_id + external_id when available,
            fallback: unique hash of url */
            $table->unique(['source_id', 'external_id'], 'source_external_unique');
            $table->unique(['url'], 'url_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
