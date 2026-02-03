<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = [
        'name',
        'address_line_1','address_line_2','town','county','postcode','country',
        'contact_name','contact_email','contact_number',
        'on_site_contact_name','on_site_contact_number',
        'is_active',
    ];
}
