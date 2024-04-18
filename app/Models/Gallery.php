<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Gallery extends Model
{
    use HasFactory;

    public function media(): BelongsToMany {
        return $this->belongsToMany(TweetMedia::class, 'gallery_media', 'gallery_id', 'media_id');
    }
}
