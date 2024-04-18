<?php

namespace App\Libraries\Xtract;

enum MediaType: string
{
    case Video = 'video';
    case Image = 'photo';
    case Animated = 'animated_gif';
}
