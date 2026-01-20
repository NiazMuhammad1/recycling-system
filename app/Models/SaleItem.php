<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id','stock_item_id','qty','description','price','line_total'
    ];

    protected $casts = [
        'qty' => 'integer',
        'price' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
    }
}
