<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class ProductModel extends Model
{
    protected $fillable = ['manufacturer_id','name','is_active'];

    public function manufacturer() { return $this->belongsTo(Manufacturer::class); }
    public function productModel() { return $this->belongsTo(ProductModel::class); }
}
