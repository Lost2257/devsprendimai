<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $fillable = [
        'ip',
        'session_hash',
        'url',
        'referrer',
        'referrer_host',
        'source',
        'medium',
        'campaign',
        'term',
        'content',
        'device',
        'browser',
        'is_entry',
        'user_agent',
    ];

    protected $casts = [
        'is_entry' => 'boolean',
    ];
}
