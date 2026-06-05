<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'collection_id',
        'qty',
        'description',
        'price',
    ];

    /**
     * Get the collection that owns the invoice item.
     */
    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }
}