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
        Schema::create('twitter_users', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('username');

            $table->string('avatar_url')->nullable();
            $table->string('banner_url')->nullable();

            $table->string('bio')->nullable();
            $table->string('location')->nullable();
            $table->string('url')->nullable();

            $table->unsignedBigInteger('likes_count');
            $table->unsignedBigInteger('tweets_count');
            $table->unsignedBigInteger('media_count');
            $table->unsignedBigInteger('following_count');
            $table->unsignedBigInteger('followers_count');
            $table->unsignedBigInteger('listed_count')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('twitter_users');
    }
};
