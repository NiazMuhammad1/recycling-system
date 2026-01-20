<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'description','size_bytes','disk','path','original_name','mime_type','uploaded_by'
    ];

    public function attachable()
    {
        return $this->morphTo();
    }
}
