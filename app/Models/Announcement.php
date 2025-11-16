<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['title','body','priority','target','active','views','published_by','published_at','expires_at','pinned'];
    protected $casts = [
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'pinned' => 'boolean',
        'active' => 'boolean',
        'views' => 'integer',
    ];
}
