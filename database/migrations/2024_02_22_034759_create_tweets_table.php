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

            $table->foreignId('author_id');

            $table->string('conversation_id')->nullable();

            $table->string('device')->nullable();

            $table->unsignedBigInteger('likes_count');
            $table->unsignedBigInteger('replies_count');
            $table->unsignedBigInteger('retweets_count');
            $table->unsignedBigInteger('quotes_count');
            $table->unsignedBigInteger('views_count')->nullable();
            $table->unsignedBigInteger('bookmarks_count');

            $table->foreignId('quote_id')->nullable();

            $table->text('text');

            $table->text('mentions');

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
