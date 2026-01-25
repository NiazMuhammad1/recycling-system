<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Manufacturer extends Model
{
    public function productModels(): HasMany
    {
        return $this->hasMany(ProductModel::class);
    }

    public function collectionItems(): HasMany
    {
        return $this->hasMany(\App\Models\CollectionItem::class);
    }

}
