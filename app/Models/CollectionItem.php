<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\CodeGenerator;
use Illuminate\Support\Facades\DB;
class CollectionItem extends Model
{
    protected $fillable = [
        'collection_id','item_number','qty','category_id',
        'manufacturer_id','product_model_id','manufacturer_text','model_text',
        'serial_number','asset_tags','dimensions','weight_kg',
        'erasure_required','status','collected','collected_at','processed_at',
        'process_action','item_valuation','refurb_cost','hdd_serial','erasure_report_path',
        'stock_item_id',
    ];

    protected $casts = [
        'erasure_required' => 'boolean',
        'collected' => 'boolean',
        'weight_kg' => 'decimal:2',
        'item_valuation' => 'decimal:2',
        'refurb_cost' => 'decimal:2',
        'collected_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        // static::creating(function ($item) {
        //     if ($item->item_number) return;

        //     $collection = $item->collection()->first();
        //     $prefix = $collection->collection_number; // example: J00001 (use your column name)

        //     // lock rows for safe increment
        //     $next = DB::table('collection_items')
        //         ->where('collection_id', $item->collection_id)
        //         ->lockForUpdate()
        //         ->count() + 1;

        //     $item->item_number = $prefix . '-' . str_pad((string)$next, 3, '0', STR_PAD_LEFT);
        // });
        
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

    public function manufacturer(){ 
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id', 'id'); 
    }
    public function productModel(){ return $this->belongsTo(ProductModel::class); }
    

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
