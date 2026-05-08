<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionPdfEmail extends Model
{
    protected $fillable = [
        'collection_id',
        'email',
        'pdfs',
        'status',
        'sent_at',
        'error',
        'sent_by',
    ];

    protected $casts = [
        'pdfs' => 'array',
        'sent_at' => 'datetime',
    ];

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}