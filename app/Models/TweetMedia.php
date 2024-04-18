<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TweetMedia extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'string',
        'nsfw' => 'boolean'
    ];

    public function tweet(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Tweet::class);
    }
}
