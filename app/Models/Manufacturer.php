<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manufacturer extends Model
{
    protected $table = 'manufacturers';
    protected $primaryKey = 'id'; 
    public function productModels()
    {
        return $this->hasMany(ProductModel::class);
    }

    public function collectionItems()
    {
        return $this->hasMany(\App\Models\CollectionItem::class);
    }

}
