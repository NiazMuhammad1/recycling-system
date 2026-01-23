<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\CodeGenerator;

class Collection extends Model
{
     protected $fillable = [
         'collection_code',
        'client_id',
        'collection_date',
        'collection_number','status','client_id','collection_date',
        'address_line_1','address_line_2','town','county','postcode','country',
        'contact_name','contact_email','contact_number','on_site_contact_name','on_site_contact_number',
        'vehicles_used','staff_members',
        'equipment_location','access_elevator','route_restrictions','other_information',
        'internal_notes','data_sanitisation','collection_type','logistics',
        'pre_collection_audit','equipment_classification',
        'collected_at','processed_at',
    ];

    protected $casts = [
        'collection_date' => 'datetime',   // fixes your: format() on string error
        'collected_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Collection $collection) {
            if (empty($collection->collection_code)) {
                $collection->collection_code = CodeGenerator::next('collections', 'J', 5);
            }
        });
    }

    public function getTotalWeightAttribute(): float
    {
        return (float)$this->items()->sum('weight_kg');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(CollectionItem::class);
    }

    public function files()
    {
        return $this->morphMany(File::class, 'attachable');
    }

    public function reports()
    {
        return $this->hasMany(CollectionReport::class);
    }

    public function stockItems()
    {
        return $this->hasMany(StockItem::class, 'source_collection_id');
    }

    public function activities()
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }
}
