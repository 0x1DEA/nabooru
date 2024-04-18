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
        Schema::create('tweet_media', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tweet_id');

            $table->string('media_key');
            $table->string('type');
            $table->string('mime')->nullable();

            $table->integer('height');
            $table->integer('width');
            $table->text('alt_text')->nullable();
            $table->integer('duration')->nullable();

            $table->string('media_url')->nullable();
            $table->string('image_url');

            $table->boolean('downloaded')->default(false);

            $table->boolean('nsfw')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tweet_media');
    }
};
