<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\CodeGenerator;

class CollectionItem extends Model
{
    protected $fillable = [
        'collection_id','item_code','quantity','category_id',
        'manufacturer','model','serial_number','asset_tags',
        'erasure_required','is_collected',
        'status','erasure_report_label','erasure_completed_at',
        'stock_item_id','created_by','updated_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'erasure_required' => 'boolean',
        'is_collected' => 'boolean',
        'erasure_completed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (CollectionItem $item) {
            if (!empty($item->item_code)) {
                return;
            }

            $collection = $item->collection()->first();
            if (!$collection) {
                throw new \RuntimeException('CollectionItem must have a valid collection_id before creating.');
            }

            // Next index = count existing items + 1 (safe enough if you create items sequentially)
            // If you expect concurrent adding of items to the SAME collection, we can lock and compute max suffix.
            $existingCount = $collection->items()->count();
            $nextIndex = $existingCount + 1;

            $item->item_code = CodeGenerator::nextCollectionItemCode($collection->collection_code, $nextIndex);
        });
    }
    
    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
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
