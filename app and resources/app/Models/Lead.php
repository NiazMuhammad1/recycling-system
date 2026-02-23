<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'lead_code','client_id',
        'company_name','contact_name','contact_email','contact_phone',
        'address_line_1','address_line_2','town','county','postcode','country',
        'source','status','expected_collection_date','notes',
        'created_by','updated_by',
    ];

    protected $casts = [
        'expected_collection_date' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
