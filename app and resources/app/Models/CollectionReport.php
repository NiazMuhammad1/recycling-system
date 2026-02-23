<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionReport extends Model
{
    protected $fillable = [
        'collection_id','type','file_id','generated_at','generated_by'
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function file()
    {
        return $this->belongsTo(File::class);
    }
}
