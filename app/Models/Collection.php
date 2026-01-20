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
        'status',
        'address_line_1','address_line_2','town','county','postcode','country',
        'contact_name','contact_email','contact_number','on_site_contact_name','on_site_contact_number',
        'data_sanitisation','collection_type','logistics',
        'equipment_location','access_elevator','route_restrictions','other_information',
        'internal_notes','pre_collection_audit','equipment_classification',
        'value_amount','sold_amount','costs_amount','profit_amount',
        'client_confirmed_at','collected_at','processing_started_at','processed_at',
        'sent_to_client_at','sent_to_client_by','created_by','updated_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (Collection $collection) {
            if (empty($collection->collection_code)) {
                $collection->collection_code = CodeGenerator::next('collections', 'J', 5);
            }
        });
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
