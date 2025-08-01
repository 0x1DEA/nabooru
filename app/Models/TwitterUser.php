<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwitterUser extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'id' => 'string'
    ];
}
