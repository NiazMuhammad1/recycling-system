<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name','county','country',
        'address_line_1','address_line_2','town','postcode',
        'contact_name','contact_email','contact_number',
        'on_site_contact_name','on_site_contact_number',
        'notes','is_active','created_by','updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
}
