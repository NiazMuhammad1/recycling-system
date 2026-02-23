<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CollectionItemHdd extends Model implements HasMedia
{
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('erasure_reports')
            ->singleFile(); // keep only 1 latest report per HDD
    }
    protected $fillable = [
        'collection_item_id',
        'manufacturer_id','product_model_id',
        'manufacturer_text','model_text',
        'serial','status','erasure_report_path','notes',
    ];

    public function item()
    {
        return $this->belongsTo(CollectionItem::class, 'collection_item_id');
    }

    public function manufacturerRel()
    {
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id');
    }

    public function productModel()
    {
        return $this->belongsTo(ProductModel::class, 'product_model_id');
    }
}
