<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionItemCode extends Model
{
    protected $fillable = ['collection_item_id','item_prefix','seq'];
    public function item() {
        return $this->belongsTo(CollectionItem::class,'collection_item_id');
    }
}
