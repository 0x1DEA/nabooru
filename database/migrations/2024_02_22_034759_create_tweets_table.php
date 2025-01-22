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
        Schema::create('tweets', function (Blueprint $table) {
            $table->id()->comment('Tweet ID');

            $table->foreignId('author_id')->nullable();

            $table->string('conversation_id')->nullable();

            $table->string('device')->nullable();

            $table->unsignedBigInteger('likes_count')->nullable();
            $table->unsignedBigInteger('replies_count')->nullable();
            $table->unsignedBigInteger('retweets_count')->nullable();
            $table->unsignedBigInteger('quotes_count')->nullable();
            $table->unsignedBigInteger('views_count')->nullable();
            $table->unsignedBigInteger('bookmarks_count')->nullable();

            $table->foreignId('quote_id')->nullable();

            $table->text('data')->nullable();
            $table->text('text')->nullable();

            $table->text('mentions')->nullable();

            $table->boolean('tombstone');
            $table->boolean('stub');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tweets');
    }
};
