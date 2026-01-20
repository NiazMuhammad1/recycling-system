<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name','slug','is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function collectionItems()
    {
        return $this->hasMany(CollectionItem::class);
    }

    public function stockItems()
    {
        return $this->hasMany(StockItem::class);
    }
}
