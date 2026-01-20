<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id','event','message','meta'
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function subject()
    {
        return $this->morphTo();
    }
}
