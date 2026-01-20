<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\CodeGenerator;

class StockItem extends Model
{
    protected $fillable = [
        'stock_number','sku','serial_number','item_name',
        'category_id','manufacturer','model',
        'chassis','processor','memory','hdd',
        'price','cosmetic_condition','condition','warehouse_location','fully_functional',
        'type','speed','ram','ram_type','os','optical_drives',
        'notes',
        'source_collection_id','source_collection_item_id',
        'status','created_by','updated_by',
    ];

    protected $casts = [
        'fully_functional' => 'boolean',
        'price' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (StockItem $item) {
            if (empty($item->stock_number)) {
                // 7 digits like S1600003
                $item->stock_number = CodeGenerator::next('stock', 'S', 7);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function sourceCollection()
    {
        return $this->belongsTo(Collection::class, 'source_collection_id');
    }

    public function sourceCollectionItem()
    {
        return $this->belongsTo(CollectionItem::class, 'source_collection_item_id');
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function files()
    {
        return $this->morphMany(File::class, 'attachable');
    }

    public function activities()
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }
}
