<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'ewc_code',
        'default_weight_kg',
        'component',
        'concentration',
        'physical_form',
        'hazard_codes',
        'type',
        'is_active',
    ];

    protected $casts = [
        'default_weight_kg' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}