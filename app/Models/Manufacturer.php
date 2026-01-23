<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manufacturer extends Model
{
    protected $fillable = ['name','is_active'];

    public function productModels()
    {
        return $this->hasMany(ProductModel::class);
    }

    public function manufacturer() { return $this->belongsTo(Manufacturer::class); }
    public function productModel() { return $this->belongsTo(ProductModel::class); }

    public function models()
    {
        return $this->hasMany(ProductModel::class, 'manufacturer_id');
    }

}
