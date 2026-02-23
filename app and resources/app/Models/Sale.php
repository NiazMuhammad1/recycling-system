<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\CodeGenerator;

class Sale extends Model
{
    protected $fillable = [
        'sale_number','sale_date','created_by',
        'customer_name','street','town','county','country','postcode','telephone','email',
        'subtotal','vat_rate','vat_amount','total','notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'sale_date' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Sale $sale) {
            if (empty($sale->sale_number)) {
                // 7 digits like X1600001
                $sale->sale_number = CodeGenerator::next('sales', 'X', 7);
            }
        });
    }

    public function items()
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
