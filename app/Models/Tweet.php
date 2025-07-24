<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tweet extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'string'
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(TwitterUser::class, 'author_id');
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Tweet::class, 'quote_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(TweetMedia::class, 'tweet_id');
    }
}
