<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}